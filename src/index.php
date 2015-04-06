<?php

require_once('toro.php');
require_once('statusreturn.php');

ToroHook::add("404", function() {
    echo json_encode(StatusReturn::E404(), JSON_NUMERIC_CHECK);
});

Toro::serve(array(
    '/admin/' 		        	=> 'InitSetup',
    '/:alpha/' 	               	=> 'InitSetup'
));

class InitSetup {
    function get($page = null) {
        if (is_null($page)) {
            echo "<!DOCKTYPE html>" .
                "<html><header><title>website admin</title></header>" .
                "<body>Please setup a login</body></html>";
        } else if ($page === 'login') {
            echo "<!DOCKTYPE html>" .
                "<html><header><title>website admin</title></header>" .
                "<body>Please login</body></html>";
        }
    }
}