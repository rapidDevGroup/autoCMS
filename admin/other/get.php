<?php
date_default_timezone_set('UTC');

class CheckIf404 {
    function __construct() {
        $getDataFromFiles = new GetDataFromFiles();
        $currentURI = explode('/', $_SERVER['REQUEST_URI']);

        if (isset($_GET['blog'])) {
            if (!$getDataFromFiles->loadFile('blog-' . $getDataFromFiles->getBlogPostKey(strtolower($_GET['blog'])) . '.json', true))
                $this->make404("blog file doesn't exists!");
            if (!$getDataFromFiles->getBlogPublished($_GET['blog']))
                $this->make404("blog is not published");

        } else if (isset($_GET['page'])) {
            // todo check if page exists
        } else if (isset($_GET['author'])) {
            // todo check if author exists
        } else if (isset($_GET['category'])) {
            // todo check if category exists
        }

        if (!isset($_GET['blog']) && !isset($_GET['page']) && $getDataFromFiles->getBlogPostPage() == $currentURI[1]) {
            $this->make404("No blog data!");
        }
    }

    private function make404($message = "") {
        header("HTTP/1.0 404 Not Found");
        print "<--" . $message . "-->\n";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/error.php')) {
            include_once('error.php');
        } else {
            print "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>";
        }
        die();
    }
}

class GetDataFromFiles {
    public $fileDataLocation = 'admin/data/';
    public $fileBlogDataLocation = 'admin/data/blog/';
    public $fileArray;
    public $fileBlogArray;

    function __construct() {
        $this->fileArray = Array();
        $this->fileBlogArray = Array();
    }

    public function loadFile($datafile, $isBlog = false) {
        if ($isBlog && file_exists($this->fileBlogDataLocation . $datafile)) {
            if (!isset($this->fileBlogArray[$datafile])) {
                $this->fileBlogArray[$datafile] = $this->loadBlogData($datafile);
            }
            return true;
        }

        if (file_exists($this->fileDataLocation . $datafile)) {
            if (!isset($this->fileArray[$datafile])) {
                $this->fileArray[$datafile] = $this->loadData($datafile);
            }
            return true;
        }

        return false;
    }

    private function loadData($dataFile) {
        return json_decode(file_get_contents($this->fileDataLocation . $dataFile), true);
    }

    private function loadBlogData($dataFile) {
        return json_decode(file_get_contents($this->fileBlogDataLocation . $dataFile), true);
    }

    public function getBlogPostKey($external) {
        if ($this->loadFile('autocms-blog.json')) {

            foreach ($this->fileArray['autocms-blog.json']['posts'] as $key => $data)
                if ($data['external'] == $external) return $key;
        }

        return null;
    }

    public function hasData($dataFile, $key, $count, $secondary) {
        if ($this->loadFile($dataFile)) {
            if (!is_null($secondary)
                    && !is_null($count)
                    && $this->fileArray[$dataFile][$key]['type'] == 'repeat') {
                return isset($this->fileArray[$dataFile][$key]['repeat'][$count][$secondary][$this->fileArray[$dataFile][$key]['repeat'][$count][$secondary]['type']])
                    && trim($this->fileArray[$dataFile][$key]['repeat'][$count][$secondary][$this->fileArray[$dataFile][$key]['repeat'][$count][$secondary]['type']]) != '';
            } else {
                return (isset($this->fileArray[$dataFile][$key][$this->fileArray[$dataFile][$key]['type']])
                    && trim($this->fileArray[$dataFile][$key][$this->fileArray[$dataFile][$key]['type']]) != '');
            }
        }

        return false;
    }

    public function hasBlogData($file, $key, $count) {
        return ($this->getBlogData($key, $count, $file, true) != '');
    }

    public function getData($dataFile, $key, $count, $secondary) {
        if ($this->loadFile($dataFile)) {
            if (!is_null($secondary) && !is_null($count) && $this->fileArray[$dataFile][$key]['type'] == 'repeat') {
                return $this->fileArray[$dataFile][$key]['repeat'][$count][$secondary][$this->fileArray[$dataFile][$key]['repeat'][$count][$secondary]['type']];
            }
            return $this->fileArray[$dataFile][$key][$this->fileArray[$dataFile][$key]['type']];
        }

        return '';
    }

    public function getRepeatCount($dataFile, $key) {
        if ($this->loadFile($dataFile)) {
            return count($this->fileArray[$dataFile][$key]['repeat']);
        }

        return 0;
    }

    public function getBlogPostCount($dataFile, $key = 'blog-count') {
        if ($this->loadFile($dataFile)) {
            return $this->fileArray[$dataFile][$key]['blog-count'];
        }

        return 0;
    }

    public function getBlogCount($file, $key) {
        if ($this->loadFile('autocms-blog.json')) {
            if (isset($_GET['page']) && is_numeric($_GET['page'])) $currentPage = $_GET['page'];
            else $currentPage = 1;
            $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '', $file) . '.json';
            $postPerPage = $this->getBlogPostCount($dataFile);

            if (empty($this->fileArray['autocms-blog.json']['posts'])) return 0;
            $countPub = 0;
            foreach ($this->fileArray['autocms-blog.json']['posts'] as $post) {
                if (isset($_GET['category'], $post['published']) && in_array($this->cleanURL($_GET['category']), array_map(Array($this, 'cleanURL'), explode(' ', $post['categories'])))) {
                    $countPub++;
                } else if (isset($_GET['author'], $post['published']) && $this->cleanURL($post['author']) == $_GET['author'] && !isset($_GET['category'])) {
                    $countPub++;
                } else if (isset($post['published']) && !isset($_GET['author']) && !isset($_GET['category'])) {
                    $countPub++;
                }
            }

            $countPub -= ($currentPage * $postPerPage) - $postPerPage;

            if ($key == 'rss-count') {
                $maxCount = $this->getBlogPostCount($file, $key);
            } else {
                $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '', $file) . '.json';
                $maxCount = $this->getBlogPostCount($dataFile, $key);
            }

            if ($maxCount == 0) return $countPub;

            if (!isset($_GET['author']) && !isset($_GET['category'])) {
                return ($maxCount < $countPub ? $maxCount : $countPub);
            } else {
                return $countPub;
            }
        }

        return 0;
    }

    public function getBlogData($key, $count, $file, $clean) {
        if ($this->loadFile('autocms-blog.json')) {
            $currentPage = 1;
            $postPerPage = 0;
            if (!is_null($count)) {
                if (isset($_GET['page']) && is_numeric($_GET['page'])) $currentPage = $_GET['page'];
                $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '', $file) . '.json';
                $postPerPage = $this->getBlogPostCount($dataFile);
            }
            $currentCount = ($currentPage * $postPerPage) - $postPerPage;

            if (!is_null($count)) {
                if (isset($_GET['author'])) {
                    $newArray = array_filter($this->fileArray['autocms-blog.json']['posts'], array($this, 'getAuthoredPosts'));
                } else if (isset($_GET['category'])) {
                    $newArray = array_filter($this->fileArray['autocms-blog.json']['posts'], array($this, 'getCategoryPosts'));
                } else {
                    $newArray = array_filter($this->fileArray['autocms-blog.json']['posts'], array($this, 'getPublishedPosts'));
                }
                $pageKey = array_keys($newArray)[$currentCount + $count];
                $blogFile = 'blog-' . $pageKey . '.json';
                if ($this->loadFile($blogFile, true)) {
                    if (!$clean) {
                        $baseURL = '/';
                        if ($this->loadFile('autocms-settings.json')) {
                            $baseURL = $this->getData('autocms-settings.json', 'site-host', null, null) . '/';
                            $currentURI = explode('/', $_SERVER['REQUEST_URI']);
                            if ((isset($_GET['author']) && $currentURI[1] != 'author') || (isset($_GET['category']) && $currentURI[1] != 'category')) {
                                $baseURL .= $currentURI[1] . '/';
                            }
                        }

                        if ($key == 'author' && !isset($_GET['author'])) {
                            return '<a href="' . $baseURL . 'author/' . $this->cleanURL($this->fileBlogArray[$blogFile][$key]) . '/">' . $this->fileBlogArray[$blogFile][$key] . '</a>';
                        } else if ($key == 'categories') {
                            $catArr = array_unique(explode(' ', $this->fileBlogArray[$blogFile]['categories']));
                            foreach ($catArr as $arrKey => $cat) {
                                $newCat = $this->cleanURL($cat);
                                if ($newCat != $this->cleanURL($_GET['category'])) {
                                    $catArr[$arrKey] = '<a href="' . $baseURL . 'category/' . $newCat . '/">' . $newCat . '</a>';
                                } else {
                                    $catArr[$arrKey] = $newCat;
                                }
                            }
                            return implode(' ', $catArr);
                        }
                    }
                    return $this->fileBlogArray[$blogFile][$key];
                }
            } else {
                $post_id = $this->getBlogPostKey(strtolower($_GET['blog']));
                $blogFile = 'blog-' . $post_id . '.json';
                if ($this->loadFile($blogFile, true)) {
                    if (!$clean) {
                        $baseURL = '/';
                        if ($this->loadFile('autocms-settings.json')) {
                            $baseURL = $this->getData('autocms-settings.json', 'site-host', null, null) . '/';
                            $currentURI = explode('/', $_SERVER['REQUEST_URI']);
                            if ($currentURI[1] == $this->getBlogPostPage()) {
                                $baseURL .= $this->fileArray['autocms-blog.json']['list-pagination-pages'] . '/';
                            }
                        }

                        if ($key == 'author' && !isset($_GET['author'])) {
                            return '<a href="' . $baseURL . 'author/' . $this->cleanURL($this->fileBlogArray[$blogFile][$key]) . '/">' . $this->fileBlogArray[$blogFile][$key] . '</a>';
                        } else if ($key == 'categories') {
                            $catArr = array_unique(explode(' ', $this->fileBlogArray[$blogFile]['categories']));
                            foreach ($catArr as $arrKey => $cat) {
                                $newCat = $this->cleanURL($cat);
                                if ($newCat != $this->cleanURL($_GET['category'])) {
                                    $catArr[$arrKey] = '<a href="' . $baseURL . 'category/' . $newCat . '/">' . $newCat . '</a>';
                                } else {
                                    $catArr[$arrKey] = $newCat;
                                }
                            }
                            return implode(' ', $catArr);
                        }
                    }
                    return $this->fileBlogArray[$blogFile][$key];
                }
            }
        }
        return '';
    }

    public function getPublishedPosts($post) {
        return isset($post['published']);
    }

    public function getAuthoredPosts($post) {
        return ($this->cleanURL($post['author']) == $_GET['author']) && isset($post['published']);
    }

    public function getCategoryPosts($post) {
        return (in_array($this->cleanURL($_GET['category']), array_map(Array($this, 'cleanURL'), explode(' ', $post['categories'])))) && isset($post['published']);
    }
    
    private function cleanURL($string) {
        //Lower case everything
        $string = trim(strtolower($string));
        //Make alphanumeric (removes all other characters)
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        //Clean up multiple dashes or whitespaces
        $string = preg_replace("/[\s-]+/", " ", $string);
        //Convert whitespaces and underscore to dash
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }

    public function getBlogPageControl($type, $file) {
        if ((isset($_GET['author']) || isset($_GET['category'])) && ($type == 'has-next' || $type == 'has-prev')) return false;

        if ($this->loadFile('autocms-blog.json')) {
            $currentPage = 1;
            if (isset($_GET['page']) && is_numeric($_GET['page'])) $currentPage = $_GET['page'];

            $blogCount = 0;
            if (!empty($this->fileArray['autocms-blog.json']['posts']))
                foreach ($this->fileArray['autocms-blog.json']['posts'] as $post) {
                    if (isset($post['published'])) $blogCount++;
                }

            $dataFile = 'page-' . str_ireplace(Array('.html', '.htm'), '', $file) . '.json';
            $postPerPage = $this->getBlogPostCount($dataFile);

            if (($type == 'has-next' || $type == 'has-prev')
                && ($blogCount == 0 || $postPerPage == 0 || $blogCount <= $postPerPage)) return false;

            $location = str_ireplace(Array('index.html', 'index.htm', '.html', '.htm'), '/', $file);
            if ($location != '/') $location = '/' . $location;

            if ($type == 'next') {
                return $location . 'page/' . ($currentPage + 1) . '/';
            } else if ($type == 'prev') {
                if (($currentPage - 1) > 1) {
                    return $location . 'page/' . ($currentPage - 1) . '/';
                } else {
                    return $location;
                }
            } elseif ($type == 'has-next') {
                return ($currentPage * $postPerPage < $blogCount ? $this->fileArray['autocms-blog.json']['pagination']['has-next'] : '');
            } elseif ($type == 'has-prev') {
                return ($currentPage > 1 ? $this->fileArray['autocms-blog.json']['pagination']['has-prev'] : '');
            }
        }
        return '';
    }

    public function getBlogPostControls($type) {
        if ($this->loadFile('autocms-blog.json')) {
            $hasPrev = false;
            $hasNext = false;
            $newArr = array_filter($this->fileArray['autocms-blog.json']['posts'], array($this, 'getPublishedPosts'));
            $arrKeys = array_keys($newArr);
            $keyPos = array_search($this->getBlogPostKey(strtolower($_GET['blog'])), $arrKeys);
            $count = count($arrKeys);
            if ($count-1 > $keyPos) {
                $hasPrev = true;
            }
            if ($keyPos > 0) {
                $hasNext = true;
            }
            if ($hasNext && $type == 'has-next') {
                return $this->fileArray['autocms-blog.json']['pagination']['has-next-post'];
            } else if ($hasPrev && $type == 'has-prev') {
                return $this->fileArray['autocms-blog.json']['pagination']['has-prev-post'];
            }
            $baseURL = '/';
            if ($this->loadFile('autocms-settings.json')) {
                $baseURL = $this->getData('autocms-settings.json', 'site-host', null, null) . '/';
                $currentURI = explode('/', $_SERVER['REQUEST_URI']);
                if ($currentURI[1] == $this->getBlogPostPage()) {
                    $baseURL .= $currentURI[1] . '/';
                }
            }
            if ($type == 'next') {
                return $baseURL . $newArr[$arrKeys[$keyPos-1]]['external'] . '/';
            } else if ($type == 'prev') {
                return $baseURL . $newArr[$arrKeys[$keyPos+1]]['external'] . '/';
            }
        }

        return '';
    }

    public function getBlogPostPage() {
        if ($this->loadFile('autocms-blog.json')) {
            return $this->fileArray['autocms-blog.json']['post-page'];
        }

        return '';
    }

    public function getBlogPublished($post_external) {
        if ($this->loadFile('autocms-blog.json')) {
            return isset($this->fileArray['autocms-blog.json']['posts'][$this->getBlogPostKey(strtolower($post_external))]['published']);
        }

        return '';
    }
}

$loadFileClass = new GetDataFromFiles();

new CheckIf404();

function getPostID($external) {
    global $loadFileClass;
    return $loadFileClass->getBlogPostKey($external);
}

function has($file, $key, $count = null, $secondary = null) {
    global $loadFileClass;
    return $loadFileClass->hasData($file, $key, $count, $secondary);
}

function hasBlog($key, $count = null, $file = null) {
    global $loadFileClass;
    return $loadFileClass->hasBlogData($file, $key, $count);
}

function get($file, $key, $count = null, $secondary = null) {
    global $loadFileClass;
    return $loadFileClass->getData($file, $key, $count, $secondary);
}

function repeatCount($file, $key) {
    global $loadFileClass;
    return $loadFileClass->getRepeatCount($file, $key);
}

function getBlog($key, $count = null, $file = null, $clean = false) {
    global $loadFileClass;
    return $loadFileClass->getBlogData($key, $count, $file, $clean);
}

function blogCount($file, $key) {
    global $loadFileClass;
    return $loadFileClass->getBlogCount($file, $key);
}

function getBlogPage($type, $file) {
    global $loadFileClass;
    return $loadFileClass->getBlogPageControl($type, $file);
}

function getBlogPostControls($type) {
    global $loadFileClass;
    return $loadFileClass->getBlogPostControls($type);
}
