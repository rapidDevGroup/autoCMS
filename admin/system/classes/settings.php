<?php

class SettingsData extends Data {
    public $dataFile = 'autocms-settings.json';
}

class Settings {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/settings.php');
        } else {
            include_once('401.html');
        }
    }
    function post() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            // todo: update settings

            header('Location: /admin/settings/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}