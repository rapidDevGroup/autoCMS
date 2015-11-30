<?php
$post_id = null;

function getPostID($external) {
    $dataFile = 'admin/data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);
    foreach ($json['posts'] as $key => $data)
        if ($data['external'] == $external) return $key;

    return null;
}

// todo: if blog get variable exist but not on the post page, also give 404


$dataBlogListFile = 'admin/data/autocms-blog.json';
if (file_exists($dataBlogListFile)) {
    $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
    $baseCall = explode('/', $_SERVER['REQUEST_URI']);
    if (!isset($_GET['blog']) && $jsonBlog['post-page'] == $baseCall[1]) {
        make404();
    }
}

// if request is a post, make sure file exists
if (isset($_GET['blog']) && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/data/blog/blog-' . getPostID(strtolower($_GET['blog'])) . '.json')) {
    make404();
} else if (isset($_GET['blog'])) {
    $dataBlogListFile = 'admin/data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
}

function make404() {
    header("HTTP/1.0 404 Not Found");
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/error.php')) {
        include_once('error.php');
    } else {
        print "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>";
    }
    die();
}

// get data
function get($file, $key, $count = null, $secondary = null) {
    $dataFile = 'admin/data/' . $file;
    if (file_exists($dataFile)) {
        $json = json_decode(file_get_contents($dataFile), true);

        if (!is_null($secondary) && !is_null($count) && $json[$key]['type'] == 'repeat') {
            return $json[$key]['repeat'][$count][$secondary][$json[$key]['repeat'][$count][$secondary]['type']];
        }
        return $json[$key][$json[$key]['type']];
    }

    return '';
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

    if (!is_null($count)) {
        $pageKey = array_keys($jsonBlog['posts'])[$count];
        $dataFile = 'admin/data/blog/blog-' . $pageKey . '.json';
        if (file_exists($dataFile)) $json = json_decode(file_get_contents($dataFile), true);

        if ($key == 'link') {
            return '/' . $jsonBlog['post-page'] . '/' . $jsonBlog['posts'][$pageKey]['external'] . '/';
        } else if (isset($json[$key])) {
            return $json[$key];
        }
    } else {
        $post_id = getPostID(strtolower($_GET['blog']));
        $dataFile = 'admin/data/blog/blog-' . $post_id . '.json';
        if (file_exists($dataFile)) {
            $json = json_decode(file_get_contents($dataFile), true);
            return $json[$key];
        }
    }
    return '';
}

// get how many blog list count to show on this page
function blogCount($file, $key) {
    $dataBlogListFile = 'admin/data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);

    $countPub = 0;
    foreach ($jsonBlog['posts'] as $data)
        if (isset($data['published'])) $countPub++;

    $dataFile = 'page-' . str_replace(Array('.html', '.htm'), '.json', $file);
    if (file_exists('admin/data/' . $dataFile)) {
        $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
    } else {
        return 0;
    }

    if ($fromFile[$key]['blog-count'] == 0) return $countPub;

    return ($fromFile[$key]['blog-count'] < $countPub ? $fromFile[$key]['blog-count'] : $countPub);
}
