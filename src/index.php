<?php
session_start();

define("VERSION", "0.0.5");

require_once('toro.php');
require_once('statusreturn.php');

ToroHook::add("404", function() {
    echo json_encode(StatusReturn::E404(), JSON_NUMERIC_CHECK);
});

Toro::serve(array(
    '/admin/'                   => 'Init',
    '/login/'                   => 'Login',
    '/logout/'                  => 'Logout',
    '/dash/'                    => 'Dash'
));

class Init {
    function get($page = null) {
        if (is_null($page) && authNeeded()) {
            include_once('admin-pages/init-setup.php');
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

                $fp = fopen('data/access.json', 'w');
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
    function post() {
        if (checkPass($_POST['user'], $_POST['password']) && !authNeeded()) {
            $_SESSION["user"] = $_POST['user'];
            $_SESSION["password"] = $_POST['password'];

            include_once('admin-pages/dash.php');
        } else {
            include_once('admin-pages/401.html');
        }
    }
}

function authNeeded() {
    $json = json_decode(file_get_contents("data/access.json"), true);
    return sizeof($json) === 0;
}

function checkPass($user = null, $pass = null) {
    if (is_null($user)) $user = $_SESSION["user"];
    if (is_null($pass)) $pass = $_SESSION["password"];
    $json = json_decode(file_get_contents("data/access.json"), true);
    $key =  search($json, 'user', $user)[0];

    // if (password_verify($pass, $key['password']))
    if ($key['password'] == $pass && $pass != '') {
        $_SESSION["role"] = serialize($key['role']);
        return true;
    }
    return false;
}

function search($array, $key, $value) {
    $results = array();
    if (is_array($array)) {
        if (isset($array[$key]) && $array[$key] == $value) {
            $results[] = $array;
        }
        foreach ($array as $subArray) {
            $results = array_merge($results, search($subArray, $key, $value));
        }
    }

    return $results;
}