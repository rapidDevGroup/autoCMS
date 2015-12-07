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