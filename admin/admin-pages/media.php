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
            <form action="/admin/media/update/" method="post" class="form-horizontal">
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
                                        <?php foreach ($datum as $image) { ?>
                                            <div class="row">
                                                <div class="col-md-12 col-lg-8">
                                                    <img src="<?=$image['location']?>" class="img-responsive img-thumbnail">
                                                    <br><br>
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
                        <button type="submit" class="btn btn-primary btn-block pull-right dirtyOK">Delete Unused</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>