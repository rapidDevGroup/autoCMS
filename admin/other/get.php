<?php
$post_id = null;
$currentCount = 0;

function getPostID($external) {
    $dataFile = 'admin/data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);
    foreach ($json['posts'] as $key => $data)
        if ($data['external'] == $external) return $key;

    return null;
}

$dataBlogListFile = 'admin/data/autocms-blog.json';
if (file_exists($dataBlogListFile)) {
    $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
    $baseCall = explode('/', $_SERVER['REQUEST_URI']);
    if (!isset($_GET['blog']) && $jsonBlog['post-page'] == $baseCall[1]) {
        make404();
    }
    // if request is a post, make sure file exists
    if (isset($_GET['blog']) && !file_exists($_SERVER['DOCUMENT_ROOT'] . '/admin/data/blog/blog-' . getPostID(strtolower($_GET['blog'])) . '.json')) {
        make404();
    }
    if (isset($_GET['blog']) && !isset($jsonBlog['posts'][getPostID(strtolower($_GET['blog']))]['published'])) {
        make404();
    }
}

function make404() {
    header("HTTP/1.0 404 Not Found");
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/error.php')) {
        include_once('error.php');
    } else {
        print "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server..</p></body></html>";
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
    if (file_exists($dataBlogListFile)) {
        $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
    } else {
        return '';
    }

    if (!is_null($count)) {
        foreach ($jsonBlog['posts'] as $post) {
            if (isset($post['published'])) {
                break;
            } else {
                $currentCount++;
            }
        }
        $pageKey = array_keys($jsonBlog['posts'])[$count + $currentCount];
        $dataFile = 'admin/data/blog/blog-' . $pageKey . '.json';
        if (file_exists($dataFile)) {
            $json = json_decode(file_get_contents($dataFile), true);
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
    if (file_exists($dataBlogListFile)) {
        $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
    } else {
        return false;
    }
    $countPub = 0;
    foreach ($jsonBlog['posts'] as $data)
        if (isset($data['published'])) $countPub++;

    if ($key == 'rss-count') {
        if (file_exists('admin/data/' . $file)) {
            $fromFile = json_decode(file_get_contents('admin/data/' . $file), true);
        } else {
            return 0;
        }
        $maxCount = $fromFile['rss-count']['number'];
    } else {
        $dataFile = 'page-' . str_replace(Array('.html', '.htm'), '.json', $file);
        if (file_exists('admin/data/' . $dataFile)) {
            $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
        } else {
            return 0;
        }
        $maxCount = $fromFile[$key]['blog-count'];
    }

    if ($maxCount == 0) return $countPub;

    return ($maxCount < $countPub ? $maxCount : $countPub);
}
