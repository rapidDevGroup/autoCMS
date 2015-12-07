<?php
include_once('header.php');
$navigationData = new NavigationData();
$data = $navigationData->getData();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Content editing for navigation text</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/nav/update/" method="post" class="form-horizontal">
                <?php foreach($data as $key => $datum) { ?>
                    <div class="form-group">
                        <?php $desc = $datum['description'];
                        if ($desc == '') {
                            $desc = 'add description';
                        } ?>
                        <label for="<?=$key?>" class="col-lg-2 col-sm-2 control-label"><a id="desc-<?=$key?>" class="desc-edit" data-type="text" data-pk="<?=$key?>" data-url="/admin/page/nav/desc/" data-title="edit description"><?=$desc?></a></label>
                        <?php if ($datum['type'] == 'html') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <textarea name="<?=$key?>" class="form-control" placeholder="<?=$datum['placeholder']?>"><?=$datum['html']?></textarea>
                            </div>
                        <?php } else if ($datum['type'] == 'text') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <input name="<?=$key?>" class="form-control" value="<?=$datum['text']?>" autocomplete="off" placeholder="<?=$datum['placeholder']?>">
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <hr>
                <div class="form-group">
                    <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                        <button type="submit" class="btn btn-primary btn-block pull-right dirtyOK">Save Navigation</button>
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