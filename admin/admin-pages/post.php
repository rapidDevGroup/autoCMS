<?php
include_once('header.php');

$blogData = new BlogData();
$fields = $blogData->getPostFields();
$openGraphTypes = $blogData->getPostOGTypes();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Post</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/blog/<?=$post_id?>/update/" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php if (!empty($openGraphTypes)) { ?>
                    <?php foreach($openGraphTypes as $key) { ?>
                        <div class="form-group">
                            <label class="col-lg-2 col-sm-2 control-label">
                                <?=$key?>
                                <?php $key = str_ireplace(':', '', $key); ?>
                            </label>
                            <?php if ($key != 'ogimage') { ?>
                                <div class="col-lg-9 col-sm-10">
                                    <input name="<?=$key?>" class="form-control" value="<?php if(isset($postInfo[$key])) { print $postInfo[$key]; } ?>" autocomplete="off">
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php foreach($fields as $key => $field) { ?>
                    <?php if ($field) { ?>
                        <?php if ($key != 'link-href' && $key != 'open-graph' && $key != 'link-next' && $key != 'link-prev') { ?>
                            <div class="form-group">
                                <label class="col-lg-2 col-sm-2 control-label">
                                    <?=ucwords(str_ireplace("-", " ", $key))?>
                                </label>
                                <?php if ($key == 'seo-schemas') { ?>
                                    <div class="col-lg-9 col-sm-10 textarea">
                                        <textarea name="<?=$key?>" class="form-control"><?php if(isset($postInfo[$key])) { print $postInfo[$key]; } ?></textarea>
                                    </div>
                                <?php } else if ($key == 'full-blog' || $key == 'short-blog') { ?>
                                    <div class="col-lg-9 col-sm-10 textarea">
                                        <textarea name="<?=$key?>" class="form-control editor"><?php if(isset($postInfo[$key])) { print $postInfo[$key]; } ?></textarea>
                                    </div>
                                <?php } else if ($key == 'title' || $key == 'author' || $key == 'keywords' || $key == 'description' || $key == 'image-alt-text' || $key == 'link-text' || $key == 'date' || $key == 'categories') { ?>
                                    <div class="col-lg-9 col-sm-10">
                                        <input name="<?=$key?>" class="form-control" value="<?php if(isset($postInfo[$key])) { print $postInfo[$key]; } ?>" autocomplete="off">
                                    </div>
                                <?php } else if ($key == 'image') { ?>
                                    <div class="col-lg-7 col-sm-7">
                                        <img id="<?=$key?>-image" class="img-responsive img-thumbnail" src="<?php if(isset($postInfo[$key])) { print $postInfo[$key]; } ?>">
                                        <input type="hidden" name="<?=$key?>-loaded" id="<?=$key?>-loaded" value="">
                                    </div>
                                    <div class="col-lg-2 col-sm-3">
                                        <input type="file" name="<?=$key?>" id="<?=$key?>" style="display: none;" onchange="readURL(this, '<?=$key?>');">
                                        <button type="button" class="btn btn-info btn-block upload-button dirtyOK" data-trigger="<?=$key?>">Upload Image</button>
                                        <br>
                                        <button type="button" class="btn btn-success btn-block upload-button dirtyOK" data-toggle="modal" data-target="#imageSelectModal" data-key="<?=$key?>">Select Media</button>
                                    </div>
                                <?php } ?>
                            </div>
                         <?php } ?>
                    <?php } ?>
                <?php } ?>
                <hr>
                <div class="row">
                    <div class="col-lg-offset-7 col-lg-2 col-sm-offset-6 col-sm-3">
                        <button name="save" type="submit" class="btn btn-warning btn-block dirtyOK">Save</button>
                    </div>
                    <div class="col-lg-2 col-sm-3">
                        <button name="publish" type="submit" class="btn btn-primary btn-block dirtyOK">Save and Publish</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br><br>

    <!-- Modal -->
    <?php
    $mediaData = new MediaData();
    $dataImages = $mediaData->getData();
    ?>
    <div class="modal fade" id="imageSelectModal" tabindex="-1" role="dialog" aria-labelledby="imageSelectModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Select Image</h4>
                </div>
                <div class="modal-body" style="height: 400px; overflow: scroll;">
                    <?php foreach($dataImages['images'] as $key => $datum) { ?>
                        <img src="<?=$datum['location']?>" class="img-responsive img-thumbnail">
                        <br><br>
                    <?php } ?>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>