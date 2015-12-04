<?php
session_start();

define("VERSION", "0.3.2");
define("_LOG_COUNT_MAX_", 20);
date_default_timezone_set('America/Toronto');

require_once('system/Toro.php');
require_once('system/statusreturn.php');
require_once('system/functions.php');
require_once('system/handlers.php');
require_once('system/simple_html_dom.php');
require_once('system/sitemaps.php');

ToroHook::add("404", function() {
    echo json_encode(StatusReturn::E404('404 Not Found!!'), JSON_NUMERIC_CHECK);
});

ToroHook::add("404Web", function() {
    include_once('admin-pages/404.html');
});

Toro::serve(array(
    '/admin/'                                   => 'Init',
    '/login/'                                   => 'Login',
    '/logout/'                                  => 'Logout',
    '/dash/'                                    => 'Dash',
    '/logs/'                                    => 'Logs',
    '/nav/'                                     => 'Nav',
    '/nav/update/'                              => 'Nav',
    '/footer/'                                  => 'Footer',
    '/footer/update/'                           => 'Footer',
    '/settings/'                                => 'Settings',
    '/settings/update/'                         => 'Settings',
    '/analytics/'                               => 'Analytics',
    '/analytics/update/'                        => 'Analytics',
    '/blog/'                                    => 'Blog',
    '/blog/:alpha/:string/'                     => 'BlogPost',
    '/blog/:alpha/'                             => 'BlogPost',
    '/dash/:alpha/'                             => 'Dash',
    '/page/:alpha/'                             => 'Page',
    '/page/:alpha/update/'                      => 'Page',
    '/page/:alpha/desc/'                        => 'Description',
    '/page/:alpha/repeat/:key/'                 => 'Repeat',
    '/page/:alpha/repeat/:key/update/'          => 'Repeat',
    '/page/:alpha/repeat-dup/:key/:number/'     => 'RepeatDup',
    '/page/:alpha/repeat-del/:key/:number/'     => 'RepeatDel'
));