<?php

class LogsData extends Data {
    public $dataFile = 'autocms-logs.json';

    public function addToLog($action, $page, $details = null) {
        $this->data[] = Array('user' => $_SESSION["user"], 'action' => $action, 'page' => $page, 'timestamp' => time(), 'details' => $details);

        if (count($this->data) > _LOG_COUNT_MAX_) {
            $this->data = array_slice($this->data, -_LOG_COUNT_MAX_);
        }
    }

    public function getLogData($num = 0, $get = null, $user = null) {
        if (!is_null($user)) {
            $userArr = Array();
            foreach ($this->data as $key => $data) {
                if ($this->data[$key]['user'] == $user) $userArr[] = $this->data[$key];
            }

            return array_reverse($userArr);
        }

        return array_reverse(array_slice($this->data, $num, $get));
    }
}

class Logs {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/logs.php');
        } else {
            include_once('401.html');
        }
    }
}