<?php

class UsersData extends Data {
    public $dataFile = 'autocms-access.json';

    public function authNeeded() {
        return empty($this->data);
    }

    public function checkPass($user = null, $pass = null) {
        if (is_null($user)) $user = $_SESSION["user"];
        if (is_null($pass)) $pass = $_SESSION["password"];
        $key = $this->search($this->data, 'user', $user)[0];

        if (password_verify($pass, $key['password'])) {
            $_SESSION["roles"] = serialize($key['roles']);
            return true;
        }
        return false;
    }

    public function addUser($user, $password, $roles) {
        // todo: make sure no two user names are the same
        $_SESSION["user"] = $user;
        $_SESSION["password"] = $password;

        $this->data[] = Array('user' => $user, 'password' => password_hash($password, PASSWORD_DEFAULT), 'roles' => $roles);

        return true;
    }

    public function delUser($user) {
        // todo
    }

    public function changePassword($password) {
        $_SESSION["password"] = $password;

        foreach($this->data as $key => $user) {
            if ($user['user'] == $_SESSION["user"]) {
                $json[$key]['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        addToLog('has changed', 'his/her password', null);
    }

    public function search($array, $key, $value) {
        $results = array();
        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }
            foreach ($array as $subArray) {
                $results = array_merge($results, $this->search($subArray, $key, $value));
            }
        }
        return $results;
    }
}

class Init {
    function get() {
        $users = new UsersData();
        if ($users->authNeeded()) {
            include_once('admin-pages/init-setup.php');
        } else if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/init.php');
        } else {
            include_once('admin-pages/login.php');
        }
    }
    function post() {
        $users = new UsersData();
        if ($users->checkPass($_POST['user'], $_POST['password']) && !$users->authNeeded()) {
            $_SESSION["user"] = $_POST['user'];
            $_SESSION["password"] = $_POST['password'];

            header('Location: /admin/');
        } else {
            include_once('admin-pages/login.php');
        }
    }
}

class Login {
    function get() {
        include_once('admin-pages/login.php');
    }
    function post() {
        $users = new UsersData();
        if ($users->authNeeded()) {
            if ($_POST['user'] != '' && $_POST['password'] != '' && $_POST['password'] == $_POST['password2']) {

                $users->addUser($_POST['user'], $_POST['password'], Array('admin'));

                include_once('admin-pages/login.php');
            } else {
                // todo: better error messaging
                include_once('admin-pages/init-setup.php?error=error');
            }
        } else {
            include_once('404.html');
        }
    }
}

class Logout {
    function get() {

        $_SESSION["user"] = '';
        $_SESSION["password"] = '';
        $_SESSION["roles"] = '';

        session_destroy();

        include_once('admin-pages/login.php');
    }
}
