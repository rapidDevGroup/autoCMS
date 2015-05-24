<?php
$post = null;

function getPostID($external) {
    $dataFile = 'data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);

    foreach ($json['posts'] as $key => $data) {
        if ($data['external'] == $external) return $key;
    }

    return null;
}

// if request is a post, make sure file exists
if (isset($_GET['blog']) && !file_exists($_SERVER['DOCUMENT_ROOT'] . 'admin/data/blog/' . getPostID(strtolower($_GET['blog'])) . '.json')) {
    header("HTTP/1.0 404 Not Found");
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '404.php')) include_once('404.php');
    die();
} else if (isset($_GET['blog'])) {
    $post = strtolower($_GET['blog']);
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
function getBlog($key, $count = null) {
    $dataBlogListFile = 'admin/data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);

    $dataFile = 'admin/data/blog/' . strtolower($_GET['post']) . '.json';
    $json = json_decode(file_get_contents($dataFile), true);

    // todo: get file name from blog, look up key in blog file

    return $json[$key][$json[$key]['type']];
}

// get how many blog list count to show on this page
function blogCount($file) {

}
