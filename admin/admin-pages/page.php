<?php
include_once('header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Content editing for <?=$page?> page</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/page/<?=$page?>/update/" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php foreach($data as $key => $datum) { ?>
                    <div class="form-group">
                        <?php $desc = $datum['description']; ?>
                        <label for="<?=$key?>" class="col-lg-2 col-sm-2 control-label">
                            <a id="desc-<?=$key?>" class="desc-edit dirtyOK" data-type="text" data-pk="<?=$key?>" data-url="/admin/page/<?=$page?>/desc/" data-title="edit description"><?=$desc?></a>
                        </label>
                        <?php if ($datum['type'] == 'script') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <textarea name="<?=$key?>" rows="6" class="form-control" placeholder="<?=$datum['placeholder']?>"><?=$datum['script']?></textarea>
                            </div>
                        <?php } else if ($datum['type'] == 'html') { ?>
                            <div class="col-lg-9 col-sm-10 textarea">
                                <textarea name="<?=$key?>" class="form-control editor"><?=$datum['html']?></textarea>
                            </div>
                        <?php } else if ($datum['type'] == 'text') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <input type="text" name="<?=$key?>" class="form-control" value="<?=$datum['text']?>" autocomplete="off">
                            </div>
                        <?php } else if ($datum['type'] == 'link') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <input type="text" name="<?=$key?>" class="form-control" value="<?=$datum['link']?>" autocomplete="off">
                            </div>
                        <?php } else if ($datum['type'] == 'color') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <div class="input-group color-picker" data-align="left" data-format="rgba">
                                    <input name="<?=$key?>" type="text" value="<?=$datum['color']?>" class="form-control" autocomplete="off">
                                    <span class="input-group-addon"><i></i></span>
                                </div>
                            </div>
                        <?php } else if ($datum['type'] == 'image') { ?>
                            <div class="col-lg-7 col-sm-7">
                                <img id="<?=$key?>-image" class="img-responsive img-thumbnail" src="<?=$datum['image']?>">
                            </div>
                            <div class="col-lg-2 col-sm-3">
                                <input type="file" name="<?=$key?>" id="<?=$key?>" style="display: none;" onchange="readURL(this, '<?=$key?>');">
                                <input type="hidden" name="<?=$key?>-loaded" id="<?=$key?>-loaded" value="">
                                <button type="button" class="btn btn-info btn-block upload-button dirtyOK" data-trigger="<?=$key?>">Upload Image</button>
                                <br>
                                <button type="button" class="btn btn-success btn-block upload-button dirtyOK" data-toggle="modal" data-target="#imageSelectModal" data-key="<?=$key?>">Select Media</button>
                            </div>
                        <?php } else if ($datum['type'] == 'repeat') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <a href="/admin/page/<?=$page?>/repeat/<?=$key?>/" class="btn btn-success btn-block">Edit Repeat (<?=count($datum['repeat'])?>)</a>
                            </div>
                        <?php } else if ($datum['type'] == 'blog-count') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-addon">Blog List Display Count</span>
                                    <input type="number" name="<?=$key?>" class="form-control" value="<?=$datum['blog-count']?>" autocomplete="off">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <hr>
                <div class="form-group">
                    <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                        <button type="submit" class="btn btn-primary btn-block pull-right dirtyOK">Save Page</button>
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