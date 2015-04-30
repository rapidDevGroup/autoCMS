<?php
include_once('header.php');
$data = getPageData($page);
?>

    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Content editing for <?=$page?></h2>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/page/<?=$page?>/" method="post" class="form-horizontal" enctype="multipart/form-data">
                <?php foreach($data as $key => $datum) { ?>
                    <div class="form-group">
                        <?php $desc = $datum['description']; ?>
                        <label for="<?=$key?>" class="col-lg-2 col-sm-2 control-label">
                            <a id="desc-<?=$key?>" class="desc-edit" data-type="text" data-pk="<?=$key?>" data-url="/admin/page/<?=$page?>/desc/" data-title="edit description"><?=$desc?></a>
                        </label>
                        <?php if ($datum['type'] == 'html') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <textarea name="<?=$key?>" class="form-control editor"><?=$datum['html']?></textarea>
                            </div>
                        <?php } else if ($datum['type'] == 'text') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <input name="<?=$key?>" class="form-control" value="<?=$datum['text']?>">
                            </div>
                        <?php } else if ($datum['type'] == 'image') { ?>
                            <div class="col-lg-7 col-sm-7">
                                <img id="<?=$key?>-image" class="img-responsive img-thumbnail" src="<?=$datum['image']?>">
                            </div>
                            <div class="col-lg-2 col-sm-3">
                                <input type="file" name="<?=$key?>" id="<?=$key?>" style="display: none;" onchange="readURL(this, '<?=$key?>');">
                                <button type="button" class="btn btn-default btn-block upload-button" data-trigger="<?=$key?>">Upload Image</button>
                            </div>
                        <?php } else if ($datum['type'] == 'repeat') { ?>
                            <div class="col-lg-9 col-sm-10">

                                <div class="rounded-border">

                                    <div id="carousel-repeat-<?=$key?>" class="carousel slide" data-ride="carousel" data-interval="false">

                                        <!-- Wrapper for slides -->
                                        <div class="carousel-inner" role="listbox" style="overflow: visible;">
                                            <?php $x = 0; foreach($datum['repeat'] as $repeatKey => $repeatData) { ?>
                                                <div class="item <?php if ($x == 0) { ?>active<?php } ?>">
                                                    <?php foreach($repeatData as $repeatItemKey => $repeatDatum) { ?>
                                                        <div class="form-group">
                                                            <?php $desc = $repeatDatum['description']; ?>
                                                            <label class="col-lg-2 col-sm-2 control-label">
                                                                <a id="desc-<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>" class="desc-edit" data-type="text" data-pk="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>" data-url="/admin/page/<?=$page?>/desc/" data-title="edit description"><?=$desc?></a>
                                                            </label>
                                                            <div class="col-lg-10 col-sm-10">
                                                                <?php if ($repeatDatum['type'] == 'html') { ?>
                                                                    <textarea name="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>" class="form-control editor"><?=$repeatDatum['html']?></textarea>
                                                                <?php } else if ($repeatDatum['type'] == 'text') { ?>
                                                                    <input name="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>" class="form-control" value="<?=$repeatDatum['text']?>">
                                                                <?php } else if ($repeatDatum['type'] == 'image') { ?>
                                                                    <div class="row">
                                                                        <div class="col-lg-9 col-sm-8">
                                                                            <img id="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>-image" class="img-responsive img-thumbnail" src="<?=$repeatDatum['image']?>">
                                                                        </div>
                                                                        <div class="col-lg-3 col-sm-4">
                                                                            <input type="file" name="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>" id="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>" style="display: none;" onchange="readURL(this, '<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>');">
                                                                            <button type="button" class="btn btn-default btn-block upload-button" data-trigger="<?=$repeatKey?>-<?=$x?>-<?=$repeatItemKey?>">Upload Image</button>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php $x++; } ?>
                                        </div>
                                    </div>
                                </div>

                                <nav>
                                    <ul class="pagination pagination-sm">
                                        <li><a href="#">Copy First</a></li>
                                        <?php for ($x = 0; $x < count($datum['repeat']); $x++) { ?>
                                            <li data-target="#carousel-repeat-<?=$key?>" data-slide-to="<?=$x?>"><a href="#"><?=$x+1?></a></li>
                                        <?php } ?>
                                    </ul>
                                </nav>

                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <hr>
                <div class="form-group">
                    <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                        <button type="submit" class="btn btn-primary btn-block pull-right">Save Page</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <br><br>

<?php
include_once('footer.php');
?>