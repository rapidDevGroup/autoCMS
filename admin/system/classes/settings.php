<?php

class SettingsData extends Data {
    public $dataFile = 'autocms-settings.json';

    public function createFile() {
        if (!file_exists($this->dataLoc . $this->dataFile)) {
            $this->data = Array();
            $this->data['site-name'] = Array('text' => '', 'description' => 'name of site', 'type' => 'text');
            $this->data['site-email'] = Array('text' => '', 'description' => 'site main email', 'type' => 'text');
            $this->data['site-lang'] = Array('text' => 'en-US', 'description' => 'site locale', 'type' => 'text', 'placeholder' => 'locale code');
            $this->data['site-description'] = Array('text' => '', 'description' => 'describe your site', 'type' => 'text');
            $this->data['site-categories'] = Array('text' => '', 'description' => 'your site categories', 'type' => 'text');
            $this->data['site-host'] = Array('text' => 'http://' . $_SERVER['HTTP_HOST'], 'description' => 'host of the site', 'type' => 'text', 'placeholder' => 'http://yourdomain.com');
            $this->data['rss-count'] = Array('number' => '10', 'description' => 'num of posts in rss', 'type' => 'number');
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

    public function setLang($lang) {
        $this->data['site-lang']['text'] = $lang;
    }

    public function getHost() {
        return $this->data['site-host']['text'];
    }

    public function getSiteName() {
        return $this->data['site-name']['text'];
    }

    public function getSitCategories() {
        return $this->data['site-categories']['text'];
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