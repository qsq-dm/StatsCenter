<?php include __DIR__.'/../include/header.php'; ?>
<!-- END NAVIGATION -->

<!-- MAIN PANEL -->
<div id="main" role="main">

<!-- RIBBON -->
<div id="ribbon">

    <span class="ribbon-button-alignment">
        <span id="refresh" class="btn btn-ribbon" data-title="refresh" rel="tooltip"
              data-placement="bottom"
              data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings."
              data-html="true"><i class="fa fa-refresh"></i></span> </span>

    <!-- breadcrumb -->
    <ol class="breadcrumb">
        <li>Home</li>
        <li>Dashboard</li>
    </ol>

</div>

<div id="content">
        <!-- row -->
        <div class="row">

            <!-- NEW WIDGET START -->
            <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">

                <div class="jarviswidget jarviswidget-color-darken jarviswidget-sortable" id="wid-id-0"
                     data-widget-editbutton="false" role="widget" style="">
                    <header role="heading">
                        <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                        <h2>接口调用明细</h2>
                        <span class="jarviswidget-loader"><i class="fa fa-refresh fa-spin"></i></span></header>
                    <div role="content">

                        <div class="jarviswidget-editbox">

                        </div>

                        <div class="widget-body no-padding">
                            <div class="widget-body-toolbar" style="height: 40px;">

                            </div>

                            <div class="form-inline" role="grid">
                                <div class="dt-top-row">
                                    <div id="data_table_stats_length" class="dataTables_length"><span
                                            class="smart-form">
                                            <label class="select" style="width:60px">
                                                <select value="25" size="1" name="dt_basic_length" aria-controls="data_table_stats">
                                                    <option value="25" selected="selected">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select><i></i></label></span>
                                    </div>
                                    <div class="dataTables_filter">
                                        <div class="form-group" style="width: 300px;">
                                            <select class="select2" id="module_id">
                                                <option value="">所有模块</option>
                                                <?php foreach ($modules as $m): ?>
                                                    <option value="<?= $m['id'] ?>: <?= $m['name'] ?>"
                                                        <?php if ($m['id'] == $_GET['module_id']) echo 'selected="selected"'; ?> ><?= $m['id'] ?>
                                                        : <?= $m['name'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group" style="width: 400px;">
                                            <select id="interface_id" class="select2">
                                                <option value="">所有接口</option>
                                                <?php foreach ($interfaces as $m): ?>
                                                    <option value="<?= $m['id'] ?>: <?= (empty($m['alias']) ? $m['name'] : $m['alias']) ?>"
                                                        <?php if ($m['id'] == $_GET['interface_id']) echo 'selected="selected"'; ?> >
                                                        <?= $m['id'] ?>: <?= (empty($m['alias']) ? $m['name'] : $m['alias']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            时间：
                                            <label class="select">
                                                <select class="input-sm" id="filter_hour_start">
                                                    <option value='00' selected="selected">00</option>
                                                    <?php
                                                    for ($i = 1; $i < 24; $i++)
                                                    {
                                                        $v = $i >= 10 ? $i : '0' . $i;
                                                        $select = (!empty($_GET['hour_start']) and $v == $_GET['hour_start']) ? 'selected="selected"' : '';
                                                        echo "<option value='$v' $select>$v</option>\n";
                                                    }
                                                    ?>
                                                </select>
                                            </label> ~
                                            <label class="select">
                                                <select class="input-sm" id="filter_hour_end">
                                                    <?php
                                                    if (empty($_GET['hour_end']))
                                                    {
                                                        $_GET['hour_end'] = '23';
                                                    }
                                                    for ($i = 0; $i < 24; $i++)
                                                    {
                                                        $v = $i >= 10 ? $i : '0' . $i;
                                                        $select = ($v == $_GET['hour_end']) ? 'selected="selected"' : '';
                                                        echo "<option value=$v $select>$v</option>\n";
                                                    }
                                                    ?>
                                                </select>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            日期：
                                            <input type="text" class="form-control datepicker"
                                                   data-dateformat="yy-mm-dd" id="data_key"
                                                   readonly="readonly" value="<?= $_GET['date_key'] ?>"
                                                />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <table class="table table-bordered table-hover dataTables_wrapper">
                                <thead>
                                <tr>
                                    <th>接口名称</th>
                                    <th>时间</th>
                                    <th>调用次数</th>
                                    <th>成功次数</th>
                                    <th>失败次数</th>
                                    <th>成功率</th>
                                    <th>响应最大值</th>
                                    <th>响应最小值</th>
                                    <th>平均响应时间</th>
                                    <th>失败平均时间</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php include __DIR__.'/include/format.php'; ?>
                                </tbody>
                            </table>

                            </div>

                        </div>
                        <!-- end widget content -->

                    </div>
                    <!-- end widget div -->

                </div>
                <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-1"
                     data-widget-editbutton="false" role="widget" style="">


                </div>
                <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-2"
                     data-widget-editbutton="false" role="widget" style="">

                </div>
                <div class="jarviswidget jarviswidget-color-blueDark jarviswidget-sortable" id="wid-id-3"
                     data-widget-editbutton="false" role="widget" style="">

                </div>
            </article>
            <!-- WIDGET END -->
        </div>
</div>
<!-- END MAIN CONTENT -->

</div>
<!-- END MAIN PANEL -->

<!-- SHORTCUT AREA : With large tiles (activated via clicking user name tag)
Note: These tiles are completely responsive,
you can add as many as you like
-->
<div id="shortcut">
    <ul>
        <li>
            <a href="#inbox.html" class="jarvismetro-tile big-cubes bg-color-blue"> <span class="iconbox"> <i
                        class="fa fa-envelope fa-4x"></i> <span>Mail <span
                            class="label pull-right bg-color-darken">14</span></span> </span> </a>
        </li>
        <li>
            <a href="#calendar.html" class="jarvismetro-tile big-cubes bg-color-orangeDark"> <span class="iconbox"> <i
                        class="fa fa-calendar fa-4x"></i> <span>Calendar</span> </span> </a>
        </li>
        <li>
            <a href="#gmap-xml.html" class="jarvismetro-tile big-cubes bg-color-purple"> <span class="iconbox"> <i
                        class="fa fa-map-marker fa-4x"></i> <span>Maps</span> </span> </a>
        </li>
        <li>
            <a href="#invoice.html" class="jarvismetro-tile big-cubes bg-color-blueDark"> <span class="iconbox"> <i
                        class="fa fa-book fa-4x"></i> <span>Invoice <span
                            class="label pull-right bg-color-darken">99</span></span> </span> </a>
        </li>
        <li>
            <a href="#gallery.html" class="jarvismetro-tile big-cubes bg-color-greenLight"> <span class="iconbox"> <i
                        class="fa fa-picture-o fa-4x"></i> <span>Gallery </span> </span> </a>
        </li>
        <li>
            <a href="javascript:void(0);" class="jarvismetro-tile big-cubes selected bg-color-pinkDark"> <span
                    class="iconbox"> <i class="fa fa-user fa-4x"></i> <span>My Profile </span> </span> </a>
        </li>
    </ul>
</div>
<?php include dirname(__DIR__).'/include/javascript.php'; ?>
<script src="<?=WEBROOT?>/apps/static/js/stats.js" type="text/javascript"></script>
<script src="<?=WEBROOT?>/apps/static/js/list.js" type="text/javascript"></script>
<script>
    StatsG.filter.hour_start = 0;
    StatsG.filter.hour_end = 23;
    $(function() {
        pageSetUp();
        StatsG.page_url = '/stats/detail/';
        StatsG.filter = <?php echo json_encode($_GET);?>;

        $("#datepicker").datepicker("option",
            $.datepicker.regional[ 'zh-CN' ]);

        $("#module_id").change(function(e) {
            var module_id = e.currentTarget.value.split(':')[0];
            window.localStorage.module_id = module_id;
            location.href = "/stats/index/?module_id=" + module_id + '&date_key' + '=<?=$_GET['date_key']?>';
        });

        $("#interface_id").change(function(e) {
            StatsG.filter.interface_id = e.currentTarget.value.split(':')[0];
            StatsG.go();
        });

        $("#filter_hour_start").change(function(e) {
            StatsG.filter.hour_start = $(this).val();
            StatsG.go();
        });

        $("#filter_hour_end").change(function(e) {
            StatsG.filter.hour_end = $(this).val();
            StatsG.go();
        });

        $("#data_key").change(function(){
            window.localStorage.date_key = $(this).val();
            StatsG.filter.date_key = window.localStorage.date_key;
            StatsG.go();
        });

    });
</script>

</body>
</html>
