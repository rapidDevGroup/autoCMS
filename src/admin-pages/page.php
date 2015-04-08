<?php
include_once('header.php');
$data = getPageData($page);
?>

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Content Editing for Page <?=$page?></h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/page/<?=$page?>/" method="post" class="form-horizontal">
                <?php foreach($data as $key => $datum) { ?>
                    <div class="form-group">
                        <?php $desc = $datum['description'];
                        if ($desc == '') {
                            $desc = 'Add Description';
                        } ?>
                        <label for="<?=$key?>" class="col-lg-2 col-sm-2 control-label"><a href=""><?=$desc?></a></label>
                        <?php if ($datum['type'] == 'html') { ?>
                            <div class="col-lg-8 col-sm-10">
                                <textarea name="<?=$key?>" class="form-control"><?=$datum['text']?></textarea>
                            </div>
                        <?php } else if ($datum['type'] == 'text') { ?>
                            <div class="col-lg-8 col-sm-10">
                                <input name="<?=$key?>" class="form-control" value="<?=$datum['text']?>">
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-8 col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary pull-right">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

<?php
include_once('footer.php');
?>