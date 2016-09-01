<?php
include_once('header.php');
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Repeat editing on page <?=$page?></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/page/<?=$page?>/repeat/<?=$key?>/update/" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php $x = 0; foreach($data as $repeatData) { ?>
                    <?php foreach($repeatData as $repeatItemKey => $repeatDatum) { ?>
                        <div class="form-group">
                            <?php $desc = $repeatDatum['description']; ?>
                            <label class="col-lg-2 col-sm-2 control-label">
                                <a id="desc-<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" class="desc-edit dirtyOK" data-type="text" data-pk="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" data-url="/admin/page/<?=$page?>/desc/" data-title="edit description"><?=$desc?></a>
                            </label>
                            <?php if ($repeatDatum['type'] == 'html') { ?>
                                <div class="col-lg-9 col-sm-10 textarea">
                                    <textarea name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" data-key="<?=$repeatItemKey?>" class="form-control editor"><?=$repeatDatum['html']?></textarea>
                                </div>
                            <?php } else if ($repeatDatum['type'] == 'link') { ?>
                                <div class="col-lg-9 col-sm-10">
                                    <input type="text" name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" class="form-control" value="<?=$repeatDatum['link']?>" autocomplete="off">
                                </div>
                            <?php } else if ($repeatDatum['type'] == 'text') { ?>
                                <div class="col-lg-9 col-sm-10">
                                    <input name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" data-key="<?=$repeatItemKey?>" class="form-control" value="<?=$repeatDatum['text']?>" autocomplete="off">
                                </div>
                            <?php } else if ($repeatDatum['type'] == 'color') { ?>
                                <div class="col-lg-9 col-sm-10">
                                    <div class="input-group color-picker" data-align="left" data-format="rgba">
                                        <input name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" type="text" value="<?=$repeatDatum['color']?>" class="form-control" autocomplete="off">
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                            <?php } else if ($repeatDatum['type'] == 'image') { ?>
                                <div class="col-lg-7 col-sm-7">
                                    <img id="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>-image" data-key="<?=$repeatItemKey?>" class="img-responsive img-thumbnail" src="<?=$repeatDatum['image']?>">
                                </div>
                                <div class="col-lg-2 col-sm-3">
                                    <input type="file" name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" data-key="<?=$repeatItemKey?>" id="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" style="display: none;" onchange="readURL(this, '<?=$key?>-<?=$x?>-<?=$repeatItemKey?>');">
                                    <input type="hidden" name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>-loaded" id="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>-loaded" value="">
                                    <button type="button" class="btn btn-info btn-block upload-button dirtyOK" data-key="<?=$repeatItemKey?>" data-trigger="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>">Upload Image</button>
                                    <br>
                                    <button type="button" class="btn btn-success btn-block upload-button dirtyOK" data-toggle="modal" data-target="#imageSelectModal" data-key="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>">Select Media</button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-lg-offset-6 col-lg-3 col-sm-offset-5 col-sm-4">
                            <div class="btn-group btn-group-justified">
                                <?php if ($x != 0) { ?><a href="/admin/page/<?=$page?>/repeat-up/<?=$key?>/<?=$x?>/" class="btn btn-warning">Move Up</a><?php } ?>
                                <?php if (count($data)-1 != $x) { ?><a href="/admin/page/<?=$page?>/repeat-down/<?=$key?>/<?=$x?>/" class="btn btn-warning">Move Down</a><?php } ?>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-3">
                            <div class="btn-group btn-group-justified">
                                <a href="/admin/page/<?=$page?>/repeat-dup/<?=$key?>/<?=$x?>/" class="btn btn-warning">Duplicate</a>
                                <?php if (count($data) > 1) { ?><a href="/admin/page/<?=$page?>/repeat-del/<?=$key?>/<?=$x?>/" class="btn btn-danger">Delete</a><?php } ?>
                            </div>
                        </div>
                    </div>
                    <hr>
                <?php $x++; } ?>
                <div class="form-group">
                    <div class="col-lg-2 col-sm-3">
                        <a href="/admin/page/<?=$page?>/" class="btn btn-success btn-block">Return</a>
                    </div>
                    <div class="col-lg-offset-7 col-lg-2 col-sm-offset-6 col-sm-3">
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