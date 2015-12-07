<?php

class SettingsData extends Data {
    public $dataFile = 'autocms-settings.json';

    public function createFile() {
        if (!file_exists($this->dataLoc . $this->dataFile)) {
            $this->data = Array();
            $this->data['site-name'] = Array('text' => '', 'description' => 'name of site', 'type' => 'text');
            $this->data['site-host'] = Array('text' => '', 'description' => 'host of the site', 'type' => 'text', 'placeholder' => 'http://yourdomain.com/');
            $fp = fopen($this->dataLoc . $this->dataFile, 'w');
            fwrite($fp, json_encode($this->data));
            fclose($fp);
        }
    }

    public function updateData($data) {
        $changeLog = Array();

        foreach ($data as $key => $datum) {
            if ($key != 'key' && isset($this->data[$key]) && $this->data[$key][$this->data[$key]['type']] != trim($datum)) {
                $changeLog[] = Array('key' => $key, 'change' => Array('original' => $this->data[$key][$this->data[$key]['type']], 'new' => trim($datum)));
                $this->data[$key][$this->data[$key]['type']] = trim($datum);
            }
        }

        if (count($changeLog) > 0) {
            $logsData = new LogsData();
            $logsData->addToLog('has updated', ' the settings', $changeLog);
        }
    }
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

            $settings = new SettingsData();
            $settings->updateData($_POST);

            header('Location: /admin/settings/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}