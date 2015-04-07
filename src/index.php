<?php
session_start();

require_once('toro.php');
require_once('statusreturn.php');

ToroHook::add("404", function() {
    echo json_encode(StatusReturn::E404(), JSON_NUMERIC_CHECK);
});

Toro::serve(array(
    '/admin/'                   => 'InitSetup',
    '/dash/'                    => 'AdminDash',

    '/:alpha/'                  => 'InitSetup'
));

class InitSetup {
    function get($page = null) {
        if (is_null($page) && authNeeded()) {
            include_once('admin-pages/init-setup.php');
        } else {
            include_once('admin-pages/login.html');
        }
    }
    function post($page = null) {
        if ($page == 'create-auth' && authNeeded()) {
            if ($_POST['user'] != '' && $_POST['password'] != '' && $_POST['password'] == $_POST['password2']) {

                // todo: don't store password like this
                $_SESSION["user"] = $_POST['user'];
                $_SESSION["password"] = $_POST['password'];

                $userArray = Array('user' => $_POST['user'], 'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 'role' => Array('admin'));

                $fp = fopen('data/access.json', 'w');
                fwrite($fp, json_encode($userArray));
                fclose($fp);

                include_once('admin-pages/login.html');
            } else {
                // todo: better error messaging
                include_once('admin-pages/init-setup.php?error=error');
            }
        } else {
            include_once('admin-pages/404.html');
        }
    }
}

class AdminDash {
    function get() {
        if (checkPass() && !authNeeded()) {

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
    $key = array_search($user, $json);
    return password_verify($pass, $json[$key]);
}