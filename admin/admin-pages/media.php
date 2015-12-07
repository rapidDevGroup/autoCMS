<?php
include_once('header.php');
$mediaData = new MediaData();
$data = $mediaData->getData();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Media Library</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    <?php $count = 0; foreach($data as $key => $datum) { ?>
                        <?php if (!empty($datum)) { ?>
                            <li role="presentation"<?php if ($count == 0) { ?> class="active"<?php } ?>><a href="#<?=$key?>" aria-controls="<?=$key?>" role="tab" data-toggle="tab"><?=ucfirst($key)?></a></li>
                        <?php } ?>
                    <?php $count++; } ?>
                </ul>

                <div class="tab-content">
                    <br>
                    <?php $count = 0; foreach($data as $key => $datum) { ?>
                        <?php if (!empty($datum)) { ?>
                            <div role="tabpanel" class="tab-pane<?php if ($count == 0) { ?> active<?php } ?>" id="<?=$key?>">
                                <?php if ($key == 'images') { ?>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-9">
                                            <img id="media-upload-image" class="img-responsive img-thumbnail" src="">
                                        </div>
                                        <div class="col-sm-12 col-md-3">
                                            <form action="/admin/media/update/" method="post" class="form-horizontal" enctype="multipart/form-data">
                                                <input type="file" name="media-upload" id="media-upload" style="display: none;" onchange="readURL(this, 'media-upload');">
                                                <button type="button" class="btn btn-info btn-block upload-button dirtyOK" data-trigger="media-upload">Upload New Image</button>
                                                <br>
                                                <button type="submit" id="media-upload-save" class="btn btn-primary btn-block upload-button dirtyOK" style="display: none">Save New Media</button>
                                            </form>
                                        </div>
                                    </div>
                                    <hr>
                                    <?php foreach ($datum as $id => $image) { ?>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-9">
                                                <img src="<?=$image['location']?>" class="img-responsive img-thumbnail">
                                                <br><br>
                                            </div>
                                            <div class="col-sm-12 col-md-3">
                                                <form action="/admin/media/update/" method="post" class="form-horizontal">
                                                    <input type="hidden" name="delete" value="<?=$id?>">
                                                    <button type="submit" class="btn btn-danger btn-block upload-button dirtyOK">Delete Image</button>
                                                </form>
                                                <br>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php $count++; } ?>
                </div>

            </div>
            <hr>
            <div class="form-group">
                <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                    <!--button type="submit" class="btn btn-primary btn-block pull-right dirtyOK">Delete Unused</button-->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>