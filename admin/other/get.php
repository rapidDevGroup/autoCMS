<?php

//todo: page is a reserved page name, as is admin, and assets

date_default_timezone_set('UTC');

$post_id = null;
$dataFiles = Array();

function getPostID($external) {
    global $dataFiles;
    $json = null;
    if (isset($dataFiles['autocms-blog.json'])) {
        $json = $dataFiles['autocms-blog.json'];
    } else {
        $dataFile = 'admin/data/autocms-blog.json';
        $json = json_decode(file_get_contents($dataFile), true);
        $dataFiles['autocms-blog.json'] = $json;
    }
    foreach ($json['posts'] as $key => $data)
        if ($data['external'] == $external) return $key;

    return null;
}

$dataBlogListFile = 'admin/data/autocms-blog.json';
if (file_exists($dataBlogListFile)) {
    $jsonBlog = null;
    if (isset($dataFiles['autocms-blog.json'])) {
        $jsonBlog = $dataFiles['autocms-blog.json'];
    } else {
        $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
        $dataFiles['autocms-blog.json'] = $jsonBlog;
    }
    $baseCall = explode('/', $_SERVER['REQUEST_URI']);
    if (!isset($_GET['blog']) && $jsonBlog['post-page'] == $baseCall[1] && !isset($_GET['page'])) {
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

// has data
function has($file, $key, $count = null, $secondary = null) {
    global $dataFiles;
    $dataFile = 'admin/data/' . $file;
    if (file_exists($dataFile)) {
        $json = null;
        if (isset($dataFiles[$file])) {
            $json = $dataFiles[$file];
        } else {
            $json = json_decode(file_get_contents($dataFile), true);
            $dataFiles[$file] = $json;
        }

        if (!is_null($secondary) && !is_null($count) && $json[$key]['type'] == 'repeat' && isset($json[$key]['repeat'][$count][$secondary][$json[$key]['repeat'][$count][$secondary]['type']]) && trim($json[$key]['repeat'][$count][$secondary][$json[$key]['repeat'][$count][$secondary]['type']]) != '') {
            return true;
        }
        return (isset($json[$key][$json[$key]['type']]) && trim($json[$key][$json[$key]['type']]) != '');
    }

    return false;
}

// get data
function get($file, $key, $count = null, $secondary = null) {
    global $dataFiles;
    $dataFile = 'admin/data/' . $file;
    if (file_exists($dataFile)) {
        $json = null;
        if (isset($dataFiles[$file])) {
            $json = $dataFiles[$file];
        } else {
            $json = json_decode(file_get_contents($dataFile), true);
            $dataFiles[$file] = $json;
        }
        if (!is_null($secondary) && !is_null($count) && $json[$key]['type'] == 'repeat') {
            return $json[$key]['repeat'][$count][$secondary][$json[$key]['repeat'][$count][$secondary]['type']];
        }
        return $json[$key][$json[$key]['type']];
    }

    return '';
}

// return count of the repeat
function repeatCount($file, $key) {
    global $dataFiles;
    $json = null;
    if (isset($dataFiles[$file])) {
        $json = $dataFiles[$file];
    } else {
        $dataFile = 'admin/data/' . $file;
        $json = json_decode(file_get_contents($dataFile), true);
        $dataFiles[$file] = $json;
    }

    return count($json[$key]['repeat']);
}

// get the post data
function getBlog($key, $count = null, $file = null) {
    global $dataFiles, $post_id;
    $currentPage = 1;
    $postPerPage = 0;
    if (!is_null($count)) {
        if (isset($_GET['page']) && is_numeric($_GET['page'])) $currentPage = $_GET['page'];

        $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '.json', $file);
        if (file_exists('admin/data/' . $dataFile)) {
            $fromFile = null;
            if (isset($dataFiles[$dataFile])) {
                $fromFile = $dataFiles[$dataFile];
            } else {
                $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
                $dataFiles[$dataFile] = $fromFile;
            }
            $postPerPage = $fromFile['blog-count']['blog-count'];
        }
    }
    $currentCount = ($currentPage * $postPerPage) - $postPerPage;

    $dataBlogListFile = 'admin/data/autocms-blog.json';
    if (file_exists($dataBlogListFile)) {
        $jsonBlog = null;
        if (isset($dataFiles['autocms-blog.json'])) {
            $jsonBlog = $dataFiles['autocms-blog.json'];
        } else {
            $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
            $dataFiles['autocms-blog.json'] = $jsonBlog;
        }
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

function getBlogPage($type, $file) {
    global $dataFiles;
    $currentPage = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) $currentPage = $_GET['page'];

    $dataBlogListFile = 'admin/data/autocms-blog.json';
    if (file_exists($dataBlogListFile)) {
        $jsonBlog = null;
        if (isset($dataFiles['autocms-blog.json'])) {
            $jsonBlog = $dataFiles['autocms-blog.json'];
        } else {
            $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
            $dataFiles['autocms-blog.json'] = $jsonBlog;
        }
    }

    $blogCount = 0;
    if (!empty($jsonBlog['posts'])) foreach ($jsonBlog['posts'] as $post) if (isset($post['published'])) $blogCount++;

    $postPerPage = 0;
    $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '.json', $file);
    if (file_exists('admin/data/' . $dataFile)) {
        $fromFile = null;
        if (isset($dataFiles[$dataFile])) {
            $fromFile = $dataFiles[$dataFile];
        } else {
            $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
            $dataFiles[$dataFile] = $fromFile;
        }
        $postPerPage = $fromFile['blog-count']['blog-count'];
    }

    if ($blogCount == 0 || $postPerPage == 0 || $blogCount <= $postPerPage) return false;

    $location = str_ireplace(Array('index.html', 'index.htm', '.html', '.htm'), '/', $file);
    if ($location != '/') $location = '/' . $location;
    if ($type == 'next') {
        return $location . 'page/' . ($currentPage + 1) . '/';
    } else if ($type == 'prev') {
        if (($currentPage-1) > 1) {
            return $location . 'page/' . ($currentPage - 1) . '/';
        } else {
            return $location;
        }
    } elseif ($type == 'has-next') {
        return ($currentPage * $postPerPage < $blogCount ? 'display:inline':'');
    } elseif ($type == 'has-prev') {
        return ($currentPage > 1 ? 'display:inline':'');
    }
    return '';
}

// get how many blog list count to show on this page
function blogCount($file, $key) {
    global $dataFiles;
    $currentPage = 1;
    $postPerPage = 0;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) $currentPage = $_GET['page'];

    $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '.json', $file);
    if (file_exists('admin/data/' . $dataFile)) {
        $fromFile = null;
        if (isset($dataFiles[$dataFile])) {
            $fromFile = $dataFiles[$dataFile];
        } else {
            $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
            $dataFiles[$dataFile] = $fromFile;
        }
        $postPerPage = $fromFile['blog-count']['blog-count'];
    }

    $dataBlogListFile = 'admin/data/autocms-blog.json';
    if (file_exists($dataBlogListFile)) {
        $jsonBlog = json_decode(file_get_contents($dataBlogListFile), true);
    } else {
        return 0;
    }
    if (empty($jsonBlog['posts'])) return 0;
    $countPub = 0;
    foreach ($jsonBlog['posts'] as $data) if (isset($data['published'])) $countPub++;

    $countPub -= ($currentPage * $postPerPage) - $postPerPage;

    if ($key == 'rss-count') {
        if (file_exists('admin/data/' . $file)) {
            $fromFile = null;
            if (isset($dataFiles[$dataFile])) {
                $fromFile = $dataFiles[$dataFile];
            } else {
                $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
                $dataFiles[$dataFile] = $fromFile;
            }
        } else {
            return 0;
        }
        $maxCount = $fromFile['rss-count']['number'];
    } else {
        $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '.json', $file);
        if (file_exists('admin/data/' . $dataFile)) {
            $fromFile = null;
            if (isset($dataFiles[$dataFile])) {
                $fromFile = $dataFiles[$dataFile];
            } else {
                $fromFile = json_decode(file_get_contents('admin/data/' . $dataFile), true);
                $dataFiles[$dataFile] = $fromFile;
            }
        } else {
            return 0;
        }
        $maxCount = $fromFile[$key]['blog-count'];
    }

    if ($maxCount == 0) return $countPub;

    return ($maxCount < $countPub ? $maxCount : $countPub);
}
