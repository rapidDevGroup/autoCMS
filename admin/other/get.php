<?php
$post = null;

// if request is a post, make sure file exists
if (isset($_GET['post']) && !file_exists($_SERVER['DOCUMENT_ROOT'] . 'admin/data/blog/' . strtolower($_GET['post']) . '.json')) {
    header("HTTP/1.0 404 Not Found");
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '404.php')) include_once('404.php');
    die();
} else if (isset($_GET['post'])) {
    $post = strtolower($_GET['post']);
}

// get data
function get($file, $key, $count = null, $secondary = null) {
    $dataFile = 'admin/data/' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    if (!is_null($secondary) && !is_null($count) && $json[$key]['type'] == 'repeat') {
        return $json[$key]['repeat'][$count][$secondary][$json[$key]['repeat'][$count][$secondary]['type']];
    }
    return $json[$key][$json[$key]['type']];
}

// return count of the repeat
function repeatCount($file, $key) {
    $dataFile = 'admin/data/' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    return count($json[$key]['repeat']);
}

// get the post data
function getBlogList($key, $count = 0) {
    $dataFile = 'admin/data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);

    // todo: get file name from blog, look up key in blog file

    return $json[$key][$json[$key]['type']];
}

// get the post data
function getBlog($key) {
    $dataFile = 'admin/data/blog/' . strtolower($_GET['post']) . '.json';
    $json = json_decode(file_get_contents($dataFile), true);

    return $json[$key][$json[$key]['type']];
}

// get how many blog list count to show on this page
function blogCount($file) {

}
