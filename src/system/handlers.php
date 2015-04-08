<?php


class Init {
    function get() {
        if (authNeeded()) {
            include_once('admin-pages/init-setup.php');
        } else if (checkPass() && !authNeeded()) {
            include_once('admin-pages/dash.php');
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
        if (authNeeded()) {
            if ($_POST['user'] != '' && $_POST['password'] != '' && $_POST['password'] == $_POST['password2']) {

                // todo: don't store password like this
                $_SESSION["user"] = $_POST['user'];
                $_SESSION["password"] = $_POST['password'];

                $userArray = Array('user' => $_POST['user'], 'password' => $_POST['password'], 'role' => Array('admin'));
                //$userArray = Array('user' => $_POST['user'], 'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 'role' => Array('admin'));

                $fp = fopen('data/autocms-access.json', 'w');
                fwrite($fp, '['.json_encode($userArray).']');
                fclose($fp);

                include_once('admin-pages/login.php');
            } else {
                // todo: better error messaging
                include_once('admin-pages/init-setup.php?error=error');
            }
        } else {
            include_once('admin-pages/404.html');
        }
    }
}

class Logout {
    function get() {

        $_SESSION["user"] = '';
        $_SESSION["password"] = '';
        $_SESSION["role"] = '';

        session_destroy();

        include_once('admin-pages/login.php');
    }
}

class Dash {
    function get() {
        if (checkPass() && !authNeeded()) {
            include_once('admin-pages/dash.php');
        } else {
            include_once('admin-pages/401.html');
        }
    }
    function post($action = null) {
        if ($action == 'process' && checkPass() && !authNeeded()) {

            buildDataFilesByTags($_POST['files']);
            renameFiles($_POST['files']);

            include_once('admin-pages/process.php');

        } else if (is_null($action) && checkPass($_POST['user'], $_POST['password']) && !authNeeded()) {
            $_SESSION["user"] = $_POST['user'];
            $_SESSION["password"] = $_POST['password'];

            include_once('admin-pages/dash.php');
        } else {
            include_once('admin-pages/401.html');
        }
    }
}

class Page {
    function get($page = null) {
        if (is_null($page) && checkPass() && !authNeeded()) {
            include_once('admin-pages/dash.php');
        } else if (checkPass() && !authNeeded()) {
            include_once('admin-pages/page.php');
        } else {
            include_once('admin-pages/401.html');
        }
    }
}