<?php
include_once('header.php');
$data = getAnalyticsData();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Analytics Script</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/analytics/update/" method="post" class="form-horizontal">
                <?php foreach($data as $key => $datum) { ?>
                    <div class="form-group">
                        <?php $desc = $datum['description']; ?>
                        <label for="<?=$key?>" class="col-lg-2 col-sm-2 control-label">
                            <a id="desc-<?=$key?>" class="desc-edit dirtyOK" data-type="text" data-pk="<?=$key?>" data-url="/admin/page/<?=$page?>/desc/" data-title="edit description"><?=$desc?></a>
                        </label>
                        <?php if ($datum['type'] == 'analytics') { ?>
                            <div class="col-lg-9 col-sm-10">
                                <textarea name="<?=$key?>" rows="6" class="form-control"><?=$datum['analytics']?></textarea>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <hr>
                <div class="form-group">
                    <div class="col-lg-offset-9 col-lg-2 col-sm-offset-9 col-sm-3">
                        <button type="submit" class="btn btn-primary btn-block pull-right dirtyOK">Save Script</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>