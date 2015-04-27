<?php
include_once('header.php');
$files = scanFiles('.html');
?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Dashboard</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <?php if (count($files) > 0) { ?>
            <div class="col-lg-4">
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
        <!--div class="col-lg-4">
            <div class="panel panel-yellow">
                <div class="panel-heading">
                    Files Missing Content Management
                </div>
                <div class="panel-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>
                </div>
                <div class="panel-footer">
                    Panel Footer
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel panel-green">
                <div class="panel-heading">
                    Completed Files
                </div>
                <div class="panel-body">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>
                </div>
                <div class="panel-footer">
                    Panel Footer
                </div>
            </div>
        </div-->
    </div>
    <!-- /.row -->

    <div class="modal fade" id="fixUnprocessed" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Select Files to Process</h4>
                </div>
                <form action="/admin/dash/process/" method="post" class="form-horizontal">
                    <div class="modal-body">
                        <?php foreach($files as $file) { ?>
                            <div class="checkbox">
                                <label><input type="checkbox" checked="checked" name="files[]" value="<?=$file?>"> <?=$file?></label>
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

<?php
include_once('footer.php');
?>