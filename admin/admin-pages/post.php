<?php
include_once('header.php');
$fields = getPostFields();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Post</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/blog/update/" method="post" class="form-horizontal">
                <?php foreach($fields as $key => $field) { ?>
                    <?php if ($field) { ?>
                        <div class="form-group">
                            <label class="col-lg-2 col-sm-2 control-label">
                                <?=$key?>
                            </label>
                            <?php if ($key == 'full-blog' || $key == 'short-blog') { ?>
                                <div class="col-lg-9 col-sm-10 textarea">
                                    <textarea name="<?=$key?>" class="form-control editor"></textarea>
                                </div>
                            <?php } else if ($key == 'title' || $key == 'author' || $key == 'keywords' || $key == 'description' || $key == 'image-alt' || $key == 'link-text') { ?>
                                <div class="col-lg-9 col-sm-10">
                                    <input name="<?=$key?>" class="form-control" value="" autocomplete="off">
                                </div>
                            <?php } else if ($key == 'image') { ?>
                                <div class="col-lg-7 col-sm-7">
                                    <img id="<?=$key?>-image" class="img-responsive img-thumbnail" src="">
                                </div>
                                <div class="col-lg-2 col-sm-3">
                                    <input type="file" name="<?=$key?>" id="<?=$key?>" style="display: none;" onchange="readURL(this, '<?=$key?>');">
                                    <button type="button" class="btn btn-info btn-block upload-button dirtyOK" data-trigger="<?=$key?>">Upload Image</button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                <hr>
                <div class="form-group">
                    <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                        <div class="btn-group btn-group-justified">
                            <a href="#" class="btn btn-warning dirtyOK">Save</a>
                            <a href="#" class="btn btn-primary dirtyOK">Publish</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br><br>
</div>
<?php
include_once('footer.php');
?>