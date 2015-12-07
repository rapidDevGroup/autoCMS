<?php
include_once('header.php');
$files = DashboardUtils::scanFiles('.html');
$logsData = new LogsData();
$logs = $logsData->getLogData(-5);
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Dashboard</h2>
        </div>
    </div>

    <div class="row">
        <?php if (count($files) > 0) { ?>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="panel panel-red">
                    <div class="panel-heading">
                        Unprocessed Files
                    </div>
                    <div class="panel-body">
                        <ul>
                            <?php foreach($files as $file) { ?>
                                <li><?=$file?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-danger btn-sm pull-right" data-toggle="modal" data-target="#fixUnprocessed">Fix This</button>
                        <?=count($files)?> Listed Files
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
        <?php if (count($logs) > 0) { ?>
            <div class="col-xs-12 col-sm-6 col-md-4">
                <div class="panel panel-green">
                    <div class="panel-heading">
                        CMS Recent History
                    </div>
                    <div class="panel-body">
                        <?php foreach($logs as $log) { ?>
                            <div class="media">
                                <div class="media-left">
                                    <div style="position:relative;">
                                        <div style="position: absolute; top: 2px; width: 100%; text-align: center; font-size: 12px; z-index: 2; color: #fff;"><?=date("M", $log['timestamp'])?></div>
                                        <div style="position: absolute; top: 15px; width: 100%; text-align: center; font-size: 30px; z-index: 2;"><?=date("j", $log['timestamp'])?></div>
                                        <img class="media-object" src="/admin/img/ic_calendar.png" alt="<?=$log['timestamp']?>" style="width: 60px;">
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h4 class="media-heading"><?=$log['user']?></h4>
                                    <?=$log['action']?> <?=$log['page']?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="panel-footer">
                        <a href="/admin/logs/" class="btn btn-success btn-sm pull-right" style="color: #fff;">View Details</a>
                        Last <?=count($logs)?> Logs Shown
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="modal fade" id="fixUnprocessed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Files to Process</h4>
                </div>
                <form action="/admin/dash/process/" method="post" class="form-horizontal" target="_parent">
                    <div class="modal-body">
                        <?php foreach($files as $file) { ?>
                            <div class="checkbox">
                                <label><input type="hidden" checked="checked" name="files[]" value="<?=$file?>"> <?=$file?></label>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Process Files</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>