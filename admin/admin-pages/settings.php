<?php
include_once('header.php');
$settingsData = new SettingsData();
$data = $settingsData->getData();
?>
<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <h2 class="page-header">Settings</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/settings/update/" method="post" class="form-horizontal">
                <?php foreach($data as $key => $datum) { ?>
                    <div class="form-group">
                        <?php $desc = $datum['description']; ?>
                        <label for="<?=$key?>" class="col-lg-2 col-sm-2 control-label"><?=$desc?></label>
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
                        <button type="submit" class="btn btn-primary btn-block pull-right dirtyOK">Save Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include_once('footer.php');
?>