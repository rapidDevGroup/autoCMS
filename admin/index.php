<?php
session_start();

define("VERSION", "0.4.0");
define("_LOG_COUNT_MAX_", 20);
date_default_timezone_set('UTC');

require_once('system/Toro.php');
require_once('system/classes.php');
require_once('system/statusreturn.php');
require_once('system/simple_html_dom.php');
require_once('system/sitemaps.php');

ToroHook::add("404", function() {
    echo json_encode(StatusReturn::E404('404 Not Found!!'), JSON_NUMERIC_CHECK);
});

ToroHook::add("404Web", function() {
    include_once('404.html');
});

Toro::serve(array(
    '/admin/'                                   => 'Init',
    '/login/'                                   => 'Login',
    '/logout/'                                  => 'Logout',
    '/dash/'                                    => 'Dashboard',
    '/logs/'                                    => 'Logs',
    '/nav/'                                     => 'Nav',
    '/nav/update/'                              => 'Nav',
    '/footer/'                                  => 'Footer',
    '/footer/update/'                           => 'Footer',
    '/settings/'                                => 'Settings',
    '/settings/update/'                         => 'Settings',
    '/analytics/'                               => 'Analytics',
    '/analytics/update/'                        => 'Analytics',
    '/media/'                                   => 'Media',
    '/media/update/'                            => 'Media',
    '/blog/'                                    => 'Blog',
    '/blog/:alpha/:string/'                     => 'BlogPost',
    '/blog/:alpha/'                             => 'BlogPost',
    '/dash/:alpha/'                             => 'Dashboard',
    '/page/:alpha/'                             => 'Pages',
    '/page/:alpha/update/'                      => 'Pages',
    '/page/:alpha/desc/'                        => 'Description',
    '/page/:alpha/repeat/:key/'                 => 'Repeat',
    '/page/:alpha/repeat/:key/update/'          => 'Repeat',
    '/page/:alpha/repeat-dup/:key/:number/'     => 'RepeatDup',
    '/page/:alpha/repeat-del/:key/:number/'     => 'RepeatDel'
));