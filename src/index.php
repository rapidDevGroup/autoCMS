<?php

require_once('toro.php');
require_once('statusreturn.php');

ToroHook::add("404", function() {
    echo json_encode(StatusReturn::E404(), JSON_NUMERIC_CHECK);
});

Toro::serve(array(
    '/admin/'                    => 'InitSetup',
    '/:alpha/'                   => 'InitSetup'
));

class InitSetup {
    function get($page = null) {
        if (is_null($page) && authNeeded()) {
            include_once('admin-pages/init-setup.html');
        } else {
            include_once('admin-pages/login.html');
        }
    }
    function post($page = null) {
        if ($page == 'create-auth' && authNeeded()) {
            print_r($_POST);


        } else {
            include_once('admin-pages/404.html');
        }
    }
}

function authNeeded() {
    $json = json_decode(file_get_contents("data/access.json"), true);
    return sizeof($json) === 0;
}
