<?php
namespace App;

class Alert
{
    public $setting;
    public $log;
    public $handler;
    public $worker_id;

    const SVR_PORT = 9990;
    const CHECK_TIME = 5;//5min pre check
    const PREFIX="YYPUSH";
    const PROCESS_NAME = "alert_server";

    protected $serv;
    protected $pid_file;

    public $user;

    function __construct()
    {
        $this->handler = new \App\Handler($this);
    }

    function onMasterStart($server)
    {
        swoole_set_process_name(self::PROCESS_NAME.": master");
    }

    function onManagerStart($server)
    {
        swoole_set_process_name(self::PROCESS_NAME.": manager");
        $this->log("stats server start");
        file_put_contents($this->pid_file,$server->master_pid);
    }

    function onManagerStop($server)
    {
        $this->log("stats server shutdown");
        if (file_exists($this->pid_file))
        {
            unlink($this->pid_file);
        }
    }

    function onWorkerStart(\swoole_server $serv, $worker_id)
    {
        $this->log("worker start {$worker_id}");
        $this->worker_id = $worker_id;
        swoole_set_process_name(self::PROCESS_NAME.": worker #$worker_id");
        if ($worker_id == 0)
        {
            $serv->addtimer(self::CHECK_TIME*60*1000);
            $this->log("{$this->worker_id} add timer min ".self::CHECK_TIME);
        }
        $gets['select'] = 'id,mobile';
        $tmp = table('user',"platform")->gets($gets);
        foreach ($tmp as $t)
        {
            if (!empty($t['mobile']))
                $this->user[$t['id']] = $t['mobile'];
        }
    }

    /**
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    function onPackage(\swoole_server $serv, $fd, $from_id, $data)
    {

    }

    function onTimer(\swoole_server $serv, $interval)
    {
        $interfaces = \Swoole::$php->redis->sMembers(self::PREFIX);
        if (!empty($interfaces))
        {
            foreach ($interfaces as $id)
            {
                $interface = table("interface")->get($id)->get();
                $interface['interface_id'] = $interface['id'];
                $interface['interface_name'] = $interface['name'];
                if (!empty($interface) and $interface['enable_alert'] == 1 and (!empty($interface['succ_hold']) or !empty($interface['wave_hold']))
                    and (!empty($interface['owner_uid']) or !empty($interface['backup_uids'])))
                {
                    $alert_ids = '';
                    if (!empty($interface['backup_uids'])) {
                        $alert_ids = $interface['backup_uids'];
                    }
                    if (!empty($module['owner_uid'])) {
                        $alert_ids .= $module['owner_uid'];
                    }
                    $interface['alert_uids'] = explode(',',$alert_ids);
                    $mobile = array();
                    foreach ($interface['alert_uids'] as $uid)
                    {
                        $mobile[$uid] = $this->user[$uid];
                    }
                    $interface['alert_mobiles'] = implode(',',$mobile);
                    $data = \Swoole::$php->redis->hGetAll(self::PREFIX."::".$interface['id']);
                    $interface = array_merge($interface,$data);
                    $serv->task($interface);
                }
                else
                {
                    $module_id = $interface['module_id'];
                    $module = \Swoole::$php->redis->hGetAll($key = self::PREFIX."::MODULE::".$module_id);
                    if (!empty($module) and $module['enable_alert'] == 1 and (!empty($module['succ_hold']) or !empty($module['wave_hold']))
                        and !empty($module['alert_uids']))
                    {
                        $interface['module_id'] = $module['module_id'];
                        $interface['module_name'] = $module['module_name'];
                        $interface['alert_uids'] = $module['alert_uids'];
                        $interface['alert_mobiles'] = $module['alert_mobiles'];
                        $interface['alert_weixins'] = $module['alert_weixins'];
                        $interface['succ_hold'] = $module['succ_hold'];
                        $interface['wave_hold'] = $module['wave_hold'];
                        $interface['alert_int'] = $module['alert_int'];
                        $data = \Swoole::$php->redis->hGetAll(self::PREFIX."::".$interface['id']);
                        $interface = array_merge($interface,$data);
                        $serv->task($interface);
                    }
                    else {
                        $this->log("{$this->worker_id} interface {$id} condition not meet,do not report");
                    }
                }
            }
        }
    }

    function onTask($serv, $task_id, $from_worker_id, $interface)
    {
        $time_key = self::getMinute() - 3;//当前时间减去2 统计要占用两个时间片

        if ($time_key)
        {
            $gets['select'] = "total_count,fail_count,time_key";
            $gets['interface_id'] = $interface['id'];
            $gets['module_id'] = $interface['module_id'];
            $gets['date_key'] = date('Y-m-d');
            $gets['time_key'] = $time_key;
            $table = "stats_".date('Ymd');
            //判断表是否存在
            $res = \Swoole::$php->db->query("SELECT table_name FROM information_schema.TABLES WHERE table_name ='$table'")->fetch();
            if ($res)
            {
                $tmp = table($table)->gets($gets);
                if (!empty($tmp))
                {
                    $this->handler->alert($interface,$tmp[0]); //传入最多数据 后期详细数据报警
                }
                else
                {
                    $fake = array(
                        'total_count' => 0,
                        'fail_count' => 0,
                        'time_key' => $time_key,
                    );
                    $this->handler->alert($interface,$fake);
                }
                $this->log("{$this->worker_id} on task data details mysql {$time_key} interface {$interface['id']}:".json_encode($interface,1).
                    "mysql data:".json_encode($tmp,JSON_UNESCAPED_UNICODE));
            }
            else
            {
                $this->log("{$this->worker_id} on task {$table} is not exists");
            }
        }
    }

    function onFinish($serv, $task_id, $data)
    {
        $this->log("on fin ".print_r(json_decode($data,1),1));
    }

    static function getMinute()
    {
        return intval((date('G')*60 + date('i')) / self::CHECK_TIME);
    }

    function log($msg)
    {
        $this->log->info($msg);
    }

    function setLogger($log)
    {
        $this->log = $log;
    }

    //获取报警接口信息
    function get_interface()
    {

    }

    function run($_setting = array())
    {
        $default_setting = array(
            'worker_num' => 4,
            'task_worker_num' => 4,
            'max_request' => 0,
        );
        $this->pid_file = $_setting['pid_file'];
        $setting = array_merge($default_setting, $_setting);
        $this->setting = $setting;
        $serv = new \swoole_server('0.0.0.0', self::SVR_PORT, SWOOLE_PROCESS, SWOOLE_UDP);
        $serv->set($setting);
        $serv->on('start', array($this, 'onMasterStart'));
        $serv->on('managerStop', array($this, 'onManagerStop'));
        $serv->on('managerStart', array($this, 'onManagerStart'));
        $serv->on('workerStart', array($this, 'onWorkerStart'));
        $serv->on('receive', array($this, 'onPackage'));
        $serv->on('timer', array($this, 'onTimer'));
        $serv->on('task', array($this, 'onTask'));
        $serv->on('finish', array($this, 'onFinish'));
        $this->serv = $serv;
        $this->serv->start();
    }
}
