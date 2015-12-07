<?php

function scanFiles($endsWith) {
    $files = scandir('../');
    $arr = Array();
    foreach ($files as $file) {
        if (endsWith($file, $endsWith)) $arr[] = $file;
    }
    return $arr;
}

function endsWith($string, $test) {
    $strLen = strlen($string);
    $testLen = strlen($test);
    if ($testLen > $strLen) return false;
    return substr_compare($string, $test, $strLen - $testLen, $testLen) === 0;
}

function renameFiles($files) {
    if (!file_exists ($_SERVER['DOCUMENT_ROOT'] . '/admin/originals/')) mkdir($_SERVER['DOCUMENT_ROOT'] . '/admin/originals/', 0755);
    foreach ($files as $file) {
        copy('../' . $file, './originals/' . $file);
        $newName = str_replace(Array('.html', '.htm'), '.php', $file);
        rename('../' . $file, '../' . $newName);
    }
}

function getPostPageName() {
    if (!file_exists("data/autocms-blog.json")) return '';
    $json = json_decode(file_get_contents("data/autocms-blog.json"), true);
    return $json['post-page'];
}

function hasBlog() {
    return file_exists("data/autocms-blog.json");
}

function addVariableToPage($file, $name, $value) {
    $dataFile = 'page-' . str_replace(Array('.html', '.htm'), '.json', $file);

    if (file_exists('data/' . $dataFile)) {
        $data = json_decode(file_get_contents('data/' . $dataFile), true);
    } else {
        $data = Array();
    }

    $data[$name] = $value;

    $fp = fopen('data/' . $dataFile, 'w');
    fwrite($fp, json_encode($data));
    fclose($fp);
}

function getImageType($fileExt, $source) {
    if ($fileExt === '') {
        $detect = exif_imagetype($source);
        if ($detect == IMAGETYPE_GIF) {
            $fileExt = 'gif';
        } else if ($detect == IMAGETYPE_JPEG) {
            $fileExt = 'jpg';
        } else if ($detect == IMAGETYPE_PNG) {
            $fileExt = 'jpg';
        } else {
            $fileExt = 'error';
        }
    }
    return $fileExt;
}

function saveDescription($file, $editKey, $editDesc) {
    $dataFile = 'data/' . $file . '.json';
    $json = json_decode(file_get_contents($dataFile), true);

    $logsData = new LogsData();
    if (strpos($editKey, '-') !== false) {
        list($repeatKey, $iteration, $itemKey) = explode("-", $editKey);
        if (isset($json[$repeatKey]['repeat'][$iteration][$itemKey])) {
            $logsData->addToLog('has changes a description on', str_replace('page-', '', $file) . ' page', Array('key' => $editKey, 'change' => Array('original' => $json[$editKey]['description'], 'new' => $editDesc)));
            $json[$repeatKey]['repeat'][$iteration][$itemKey]['description'] = trim($editDesc);
        }
    } else if (isset($json[$editKey])) {
        $logsData->addToLog('has changes a description on', str_replace('page-', '', $file) . ' page', Array('key' => $editKey, 'change' => Array('original' => $json[$editKey]['description'], 'new' => $editDesc)));
        $json[$editKey]['description'] = $editDesc;
    }

    $fp = fopen($dataFile, 'w');
    fwrite($fp, json_encode($json));
    fclose($fp);
}


function copyApacheConfig() {
    if (file_exists('./other/.htaccess2copy')) copy('./other/.htaccess2copy', '../.htaccess');
    if (file_exists('./other/robots.txt') && !file_exists('../robots.txt')) copy('./other/robots.txt', '../robots.txt');
}

function createXMLSitemap() {
    $domain = str_ireplace('www.', '', $_SERVER["HTTP_HOST"]);

    if (!file_exists("../sitemap.xml") && file_exists("../robots.txt")) {
        $file = '../robots.txt';
        $sitemapLine = "\n\nSitemap: http://" . $domain . '/sitemap.xml';
        file_put_contents($file, $sitemapLine, FILE_APPEND);
    }

    $sitemap = new Sitemap('http://' . $domain . '/');
    $sitemap->setPath('../');

    $postPageName = getPostPageName();

    $sitemap->addItem('', '1', 'daily');

    $pagesData = new PagesData();
    $pages = $pagesData->getData();
    foreach ($pages as $page) {
        if ($page != $postPageName && $page != 'error' && $page != 'index') {
            $sitemap->addItem($page . '/', '0.5', 'daily');
        }
    }

    $blogs = getBlogList();
    foreach ($blogs as $blog) {
        if (isset($blog['published'])) {
            $sitemap->addItem($postPageName . '/' . $blog['external'] . '/', '1', 'monthly');
        }
    }

    $sitemap->createSitemapIndex('http://' . $domain . '/', 'Today');
}

function processBlog($files) {
    $dataFile = 'data/autocms-blog.json';
    $blogFolder = $_SERVER['DOCUMENT_ROOT'] . '/admin/data/blog/';
    if (!is_dir($blogFolder)) {
        mkdir($blogFolder);
    }

    if (!file_exists($dataFile)) {
        $blogArr = Array('post-page' => null, 'og-types' => Array(), 'types' => Array('title' => false, 'keywords' => false, 'description' => false, 'author' => false, 'date' => false, 'image' => false, 'image-alt-text' => false, 'short-blog' => false, 'full-blog' => false, 'link-text' => false, 'link-href' => false, 'open-graph'), 'posts' => Array());
    } else {
        $blogArr = json_decode(file_get_contents($dataFile), true);
    }

    foreach ($files as $file) {
        $fileData = file_get_contents('../' . $file, true);

        $html = str_get_html($fileData);

        foreach($html->find('.auto-blog-head, .auto-blog-list, .auto-blog-post') as $blog) {
            if (strpos($blog->class, 'auto-blog-head') !== false) {
                foreach($html->find('.auto-blog-head title') as $pageTitle) {
                    $pageTitle->innertext = "<?=getBlog('title')?>";
                    $blogArr['types']['title'] = true;
                }
                foreach($html->find('.auto-blog-head meta') as $pageMeta) {
                    if ($pageMeta->name == 'keywords' || $pageMeta->name == 'description' || $pageMeta->name == 'author') {
                        $pageMeta->content = "<?=getBlog('$pageMeta->name')?>";
                        $blogArr['types'][$pageMeta->name] = true;
                    }
                    if (isset($pageMeta->property) && isset($pageMeta->content)) {
                        $property = preg_replace("/[^a-z^A-Z^0-9_-]/", "", $pageMeta->property);
                        if ($pageMeta->property == "og:image") {
                            $pageMeta->content = "<?=get('autocms-settings.json', 'site-host')?><?=getBlog('image')?>";
                        } else if ($pageMeta->property == "og:title") {
                            $pageMeta->content = "<?=getBlog('title')?>";
                        } else if ($pageMeta->property == "og:description") {
                            $pageMeta->content = "<?=getBlog('description')?>";
                        } else if ($pageMeta->property == "og:author" || $pageMeta->property == 'article:author') {
                            $pageMeta->content = "<?=getBlog('author')?>";
                        } else if ($pageMeta->property == "og:url") {
                            $pageMeta->content = "<?=getBlog('link-href')?>";
                        } else if ($pageMeta->property == "og:type") {
                            $pageMeta->content = "article";
                        } else if ($pageMeta->property == "og:site_name") {
                            $pageMeta->content = "<?=get('autocms-settings.json', 'site-name')?>";
                        } else {
                            $pageMeta->content = "<?=getBlog('$property')?>";
                            $blogArr['types']['open-graph'] = true;
                            $blogArr['og-types'][$property] = $pageMeta->property;
                        }
                    }
                }
                $blog->innertext .= "<?=get('autocms-analytics.json', 'analytics')?>";
                $blog->class = str_replace('auto-blog-head', '', $blog->class);

            } else if (strpos($blog->class, 'auto-blog-list') !== false) {
                foreach($html->find('.auto-blog-list .auto-blog-title, .auto-blog-list .auto-blog-date, .auto-blog-list .auto-blog-bg-img, .auto-blog-list .auto-blog-img, .auto-blog-list .auto-blog-short, .auto-blog-list .auto-blog-full, .auto-blog-list .auto-blog-link, .auto-blog-list .auto-blog-link-href') as $list) {
                    if (strpos($list->class, 'auto-blog-title') !== false) {
                        $list->innertext = '<?=getBlog("title", "$x")?>';
                        $list->class = str_replace('auto-blog-title', '', $list->class);
                        $blogArr['types']['title'] = true;
                    } else if (strpos($list->class, 'auto-blog-bg-img') !== false) {
                        $list->style = "background-image: url('<?=getBlog(" . '"image", $x' . ")?>');";
                        $list->class = str_replace('auto-blog-bg-img', '', $list->class);
                        $blogArr['types']['image'] = true;
                    } else if (strpos($list->class, 'auto-blog-img') !== false) {
                        $list->src = '<?=getBlog("image", "$x")?>';
                        $list->alt = '<?=getBlog("image-alt-text", "$x")?>';
                        $list->class = str_replace('auto-blog-img', '', $list->class);
                        $blogArr['types']['image'] = true;
                        $blogArr['types']['image-alt-text'] = true;
                    } else if (strpos($list->class, 'auto-blog-short') !== false) {
                        $list->innertext = '<?=getBlog("short-blog", "$x")?>';
                        $list->class = str_replace('auto-blog-short', '', $list->class);
                        $blogArr['types']['short-blog'] = true;
                    } else if (strpos($list->class, 'auto-blog-full') !== false) {
                        $list->innertext = '<?=getBlog("full-blog", "$x")?>';
                        $list->class = str_replace('auto-blog-full', '', $list->class);
                        $blogArr['types']['full'] = true;
                    } else if (strpos($list->class, 'auto-blog-date') !== false) {
                        $list->innertext = '<?=getBlog("date", "$x")?>';
                        $list->class = str_replace('auto-blog-date', '', $list->class);
                        $blogArr['types']['date'] = true;
                    } else if (strpos($list->class, 'auto-blog-link-href') !== false) {
                        $list->href = '<?=getBlog("link-href", "$x")?>';
                        $list->class = str_replace('auto-blog-link-href', '', $list->class);
                        $blogArr['types']['link-href'] = true;
                    } else if (strpos($list->class, 'auto-blog-link') !== false) {
                        $list->href = '<?=getBlog("link", "$x")?>';
                        $list->innertext = '<?=getBlog("link-text", "$x")?>';
                        $list->class = str_replace('auto-blog-link', '', $list->class);
                        $blogArr['types']['link-text'] = true;
                    }
                    if (trim($list->class) === '') $list->class = null;
                }
                $fieldID = uniqid();

                $blog->class = str_replace('auto-blog-list', '', $blog->class);
                $blog->outertext = '<?php for ($x = 0; $x ' . '< blogCount("' . $file . '", "' . $fieldID . '");' . ' $x++) { ?>' . $blog->outertext . "<?php } ?>";

                addVariableToPage($file, $fieldID, Array('blog-count' => 3, 'description' => 'blog display count', 'type' => 'blog-count'));

            } else if (strpos($blog->class, 'auto-blog-post') !== false) {
                $blogArr['post-page'] = str_replace(Array('.html', '.htm'), '', $file);
                foreach($html->find('.auto-blog-post .auto-blog-title, .auto-blog-post .auto-blog-date, .auto-blog-post .auto-blog-bg-img, .auto-blog-post .auto-blog-img, .auto-blog-post .auto-blog-short, .auto-blog-post .auto-blog-full') as $post) {
                    if (strpos($post->class, 'auto-blog-title') !== false) {
                        $post->innertext = '<?=getBlog("title")?>';
                        $post->class = str_replace('auto-blog-title', '', $post->class);
                        $blogArr['types']['title'] = true;
                    } else if (strpos($post->class, 'auto-blog-bg-img') !== false) {
                        $post->style = "background-image: url('<?=getBlog(" . '"image"' . ")?>');";
                        $post->class = str_replace('auto-blog-bg-img', '', $post->class);
                        $blogArr['types']['image'] = true;
                    } else if (strpos($post->class, 'auto-blog-img') !== false) {
                        $post->src = '<?=getBlog("image")?>';
                        $post->alt = '<?=getBlog("image-alt-text")?>';
                        $post->class = str_replace('auto-blog-img', '', $post->class);
                        $blogArr['types']['image'] = true;
                        $blogArr['types']['image-alt-text'] = true;
                    } else if (strpos($post->class, 'auto-blog-short') !== false) {
                        $post->innertext = '<?=getBlog("short-blog")?>';
                        $post->class = str_replace('auto-blog-short', '', $post->class);
                        $blogArr['types']['short-blog'] = true;
                    } else if (strpos($post->class, 'auto-blog-full') !== false) {
                        $post->innertext = '<?=getBlog("full-blog")?>';
                        $post->class = str_replace('auto-blog-full', '', $post->class);
                        $blogArr['types']['full-blog'] = true;
                    } else if (strpos($post->class, 'auto-blog-date') !== false) {
                        $post->innertext = '<?=getBlog("date")?>';
                        $post->class = str_replace('auto-blog-date', '', $post->class);
                        $blogArr['types']['date'] = true;
                    }
                    if (trim($post->class) === '') $post->class = null;
                }
                $blog->class = str_replace('auto-blog-post', '', $blog->class);
            }
            if (trim($blog->class) === '') $blog->class = null;
        }

        $fp = fopen('../' . $file, 'w');
        fwrite($fp, $html);
        fclose($fp);
    }

    $fp = fopen($dataFile, 'w');
    fwrite($fp, json_encode($blogArr));
    fclose($fp);
}

function getPostFields() {
    $dataFile = 'data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);

    return $json['types'];
}

function getPostOGTypes() {
    $dataFile = 'data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);

    return $json['og-types'];
}

function getPostData($post_id) {
    $dataFile = 'data/blog/blog-' . $post_id . '.json';
    $json = json_decode(file_get_contents($dataFile), true);

    return $json;
}

function getBlogList() {
    if (!file_exists("data/autocms-blog.json")) return [];
    $json = json_decode(file_get_contents("data/autocms-blog.json"), true);
    return $json['posts'];
}

function updateBlogPost($post_id, $data, $publish = false) {
    $dataFile = 'data/blog/blog-' . $post_id . '.json';
    $changeLog = Array();
    $isNew = false;
    $updateTime = time();
    $creationTime = time();
    if (file_exists($dataFile)) {
        $json = json_decode(file_get_contents($dataFile), true);
    } else {
        $isNew = true;
        $json = Array('title' => null, 'keywords' => null, 'description' => null, 'author' => null, 'image' => null, 'image-alt-text' => null, 'short-blog' => null, 'full-blog' => null, 'link-text' => null, 'link-href' => null);
    }

    foreach ($data as $key => $datum) {
        $changeLog[] = Array('key' => $key, 'change' => Array('original' => $json[$key], 'new' => trim($datum)));
        $json[$key] = trim($datum);
    }

    $logsData = new LogsData();
    if (count($changeLog) > 0 && !$isNew) {
        $logsData->addToLog('has updated', $data['title'] . ' blog', $changeLog);
    } else if (count($changeLog) > 0 && $isNew) {
        $logsData->addToLog('has created', $data['title'] . ' blog', $changeLog);
    }

    $dataBlogFile = 'data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogFile), true);

    $externalTitle = preg_replace('/[^a-z0-9-]/i', '', str_replace(' ', '-', strtolower(trim($json['title']))));

    if ($isNew || (isset($jsonBlog['posts'][$post_id]['external']) && $jsonBlog['posts'][$post_id]['external'] != $externalTitle)) {
        $externalTitleOriginal = $externalTitle;

        $count = 0;
        foreach ($jsonBlog['posts'] as $key => $data) {
            while ($data['external'] == $externalTitle) {
                $externalTitle = $externalTitleOriginal . '-' . $count++;
            }
        }

        if ($isNew) {
            $jsonBlog['posts'][$post_id] = Array('external' => $externalTitle, 'title' => $json['title'], 'creator' => $_SESSION["user"],'created' => $creationTime);
        } else {
            $jsonBlog['posts'][$post_id]['title'] = $json['title'];
            $jsonBlog['posts'][$post_id]['external'] = $externalTitle;
            $jsonBlog['posts'][$post_id]['last-updated'] = $updateTime;
        }
    } else {
        $jsonBlog['posts'][$post_id]['last-updated'] = $updateTime;
    }

    if ($publish) {
        $jsonBlog['posts'][$post_id]['published'] = $updateTime;
    }

    $domain = str_ireplace('www.', '', $_SERVER["HTTP_HOST"]);
    $postPage = $jsonBlog['post-page'];
    $json['link-href'] = 'http://' . $domain . '/' . $postPage . '/' . $externalTitle . '/';

    $fp = fopen($dataFile, 'w');
    fwrite($fp, json_encode($json));
    fclose($fp);

    $fp = fopen($dataBlogFile, 'w');
    fwrite($fp, json_encode($jsonBlog));
    fclose($fp);

    createXMLSitemap();
}

function publishPost($post_id) {
    $dataBlogFile = 'data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogFile), true);

    $jsonBlog['posts'][$post_id]['published'] = time();

    $fp = fopen($dataBlogFile, 'w');
    fwrite($fp, json_encode($jsonBlog));
    fclose($fp);

    createXMLSitemap();
}

function unpublishPost($post_id) {
    $dataBlogFile = 'data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogFile), true);

    unset($jsonBlog['posts'][$post_id]['published']);

    $fp = fopen($dataBlogFile, 'w');
    fwrite($fp, json_encode($jsonBlog));
    fclose($fp);

    createXMLSitemap();
}

function trashPost($post_id) {
    $dataBlogFile = 'data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogFile), true);

    unset($jsonBlog['posts'][$post_id]);

    $fp = fopen($dataBlogFile, 'w');
    fwrite($fp, json_encode($jsonBlog));
    fclose($fp);

    unlink('data/blog/blog-' . $post_id . '.json');
}

function orderBlog() {
    $dataBlogFile = 'data/autocms-blog.json';
    $jsonBlog = json_decode(file_get_contents($dataBlogFile), true);

    $jsonBlog['posts'] = arrayMSort($jsonBlog['posts'], array('published'=>SORT_DESC));

    $fp = fopen($dataBlogFile, 'w');
    fwrite($fp, json_encode($jsonBlog));
    fclose($fp);
}

function arrayMSort($array, $cols) {
    $colArr = array();
    foreach ($cols as $col => $order) {
        $colArr[$col] = array();
        foreach ($array as $k => $row) { $colArr[$col]['_'.$k] = strtolower($row[$col]); }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colArr[\''.$col.'\'],'.$order.',';
    }
    $eval = substr($eval,0,-1).');';
    eval($eval);
    $ret = array();
    foreach ($colArr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k,1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}