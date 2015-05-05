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
                            <?php } else if ($repeatDatum['type'] == 'text') { ?>
                                <div class="col-lg-9 col-sm-10">
                                    <input name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" data-key="<?=$repeatItemKey?>" class="form-control" value="<?=$repeatDatum['text']?>" autocomplete="off">
                                </div>
                            <?php } else if ($repeatDatum['type'] == 'image') { ?>
                                <div class="col-lg-7 col-sm-7">
                                    <img id="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>-image" data-key="<?=$repeatItemKey?>" class="img-responsive img-thumbnail" src="<?=$repeatDatum['image']?>">
                                </div>
                                <div class="col-lg-2 col-sm-3">
                                    <input type="file" name="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" data-key="<?=$repeatItemKey?>" id="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>" style="display: none;" onchange="readURL(this, '<?=$key?>-<?=$x?>-<?=$repeatItemKey?>');">
                                    <button type="button" class="btn btn-info btn-block upload-button dirtyOK" data-key="<?=$repeatItemKey?>" data-trigger="<?=$key?>-<?=$x?>-<?=$repeatItemKey?>">Upload Image</button>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
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
</div>
<?php
include_once('footer.php');
?>