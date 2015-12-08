<?php

// todo: Maybe separate blog and blog posts stuff?

class BlogData extends Data {
    public $dataFile = 'autocms-blog.json';
    public $blogDataLocation;

    function __construct() {
        parent::__construct();
        $this->blogDataLocation = $_SERVER['DOCUMENT_ROOT'] . '/admin/data/blog/';
    }

    public function createFile() {
        if (!file_exists($this->dataLoc . $this->dataFile)) {
            $this->data = Array('post-page' => null, 'og-types' => Array(), 'types' => Array('title' => false, 'keywords' => false, 'description' => false, 'author' => false, 'date' => false, 'image' => false, 'image-alt-text' => false, 'short-blog' => false, 'full-blog' => false, 'link-text' => false, 'link-href' => false, 'open-graph'), 'posts' => Array());
            $fp = fopen($this->dataLoc . $this->dataFile, 'w');
            fwrite($fp, json_encode($this->data));
            fclose($fp);
        }

        if (!is_dir($this->blogDataLocation)) {
            mkdir($this->blogDataLocation);
        }
    }

    public function buildDataFile($files) {

        foreach ($files as $file) {
            $fileData = file_get_contents('../' . $file, true);

            $html = str_get_html($fileData);

            foreach($html->find('.auto-blog-head, .auto-blog-list, .auto-blog-post') as $blog) {
                if (strpos($blog->class, 'auto-blog-head') !== false) {
                    foreach($html->find('.auto-blog-head title') as $pageTitle) {
                        $pageTitle->innertext = "<?=getBlog('title')?>";
                        $this->data['types']['title'] = true;
                    }
                    foreach($html->find('.auto-blog-head meta') as $pageMeta) {
                        if ($pageMeta->name == 'keywords' || $pageMeta->name == 'description' || $pageMeta->name == 'author') {
                            $pageMeta->content = "<?=getBlog('$pageMeta->name')?>";
                            $this->data['types'][$pageMeta->name] = true;
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
                                $this->data['types']['open-graph'] = true;
                                $this->data['og-types'][$property] = $pageMeta->property;
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
                            $this->data['types']['title'] = true;
                        } else if (strpos($list->class, 'auto-blog-bg-img') !== false) {
                            $list->style = "background-image: url('<?=getBlog(" . '"image", $x' . ")?>');";
                            $list->class = str_replace('auto-blog-bg-img', '', $list->class);
                            $this->data['types']['image'] = true;
                        } else if (strpos($list->class, 'auto-blog-img') !== false) {
                            $list->src = '<?=getBlog("image", "$x")?>';
                            $list->alt = '<?=getBlog("image-alt-text", "$x")?>';
                            $list->class = str_replace('auto-blog-img', '', $list->class);
                            $this->data['types']['image'] = true;
                            $this->data['types']['image-alt-text'] = true;
                        } else if (strpos($list->class, 'auto-blog-short') !== false) {
                            $list->innertext = '<?=getBlog("short-blog", "$x")?>';
                            $list->class = str_replace('auto-blog-short', '', $list->class);
                            $this->data['types']['short-blog'] = true;
                        } else if (strpos($list->class, 'auto-blog-full') !== false) {
                            $list->innertext = '<?=getBlog("full-blog", "$x")?>';
                            $list->class = str_replace('auto-blog-full', '', $list->class);
                            $this->data['types']['full'] = true;
                        } else if (strpos($list->class, 'auto-blog-date') !== false) {
                            $list->innertext = '<?=getBlog("date", "$x")?>';
                            $list->class = str_replace('auto-blog-date', '', $list->class);
                            $this->data['types']['date'] = true;
                        } else if (strpos($list->class, 'auto-blog-link-href') !== false) {
                            $list->href = '<?=getBlog("link-href", "$x")?>';
                            $list->class = str_replace('auto-blog-link-href', '', $list->class);
                            $this->data['types']['link-href'] = true;
                        } else if (strpos($list->class, 'auto-blog-link') !== false) {
                            $list->href = '<?=getBlog("link", "$x")?>';
                            $list->innertext = '<?=getBlog("link-text", "$x")?>';
                            $list->class = str_replace('auto-blog-link', '', $list->class);
                            $this->data['types']['link-text'] = true;
                        }
                        if (trim($list->class) === '') $list->class = null;
                    }
                    $fieldID = uniqid();

                    $blog->class = str_replace('auto-blog-list', '', $blog->class);
                    $blog->outertext = '<?php for ($x = 0; $x ' . '< blogCount("' . $file . '", "' . $fieldID . '");' . ' $x++) { ?>' . $blog->outertext . "<?php } ?>";

                    PagesData::addVariableToPage($file, $fieldID, Array('blog-count' => 3, 'description' => 'blog display count', 'type' => 'blog-count'));

                } else if (strpos($blog->class, 'auto-blog-post') !== false) {
                    $blogArr['post-page'] = str_replace(Array('.html', '.htm'), '', $file);
                    foreach($html->find('.auto-blog-post .auto-blog-title, .auto-blog-post .auto-blog-date, .auto-blog-post .auto-blog-bg-img, .auto-blog-post .auto-blog-img, .auto-blog-post .auto-blog-short, .auto-blog-post .auto-blog-full') as $post) {
                        if (strpos($post->class, 'auto-blog-title') !== false) {
                            $post->innertext = '<?=getBlog("title")?>';
                            $post->class = str_replace('auto-blog-title', '', $post->class);
                            $this->data['types']['title'] = true;
                        } else if (strpos($post->class, 'auto-blog-bg-img') !== false) {
                            $post->style = "background-image: url('<?=getBlog(" . '"image"' . ")?>');";
                            $post->class = str_replace('auto-blog-bg-img', '', $post->class);
                            $this->data['types']['image'] = true;
                        } else if (strpos($post->class, 'auto-blog-img') !== false) {
                            $post->src = '<?=getBlog("image")?>';
                            $post->alt = '<?=getBlog("image-alt-text")?>';
                            $post->class = str_replace('auto-blog-img', '', $post->class);
                            $this->data['types']['image'] = true;
                            $this->data['types']['image-alt-text'] = true;
                        } else if (strpos($post->class, 'auto-blog-short') !== false) {
                            $post->innertext = '<?=getBlog("short-blog")?>';
                            $post->class = str_replace('auto-blog-short', '', $post->class);
                            $this->data['types']['short-blog'] = true;
                        } else if (strpos($post->class, 'auto-blog-full') !== false) {
                            $post->innertext = '<?=getBlog("full-blog")?>';
                            $post->class = str_replace('auto-blog-full', '', $post->class);
                            $this->data['types']['full-blog'] = true;
                        } else if (strpos($post->class, 'auto-blog-date') !== false) {
                            $post->innertext = '<?=getBlog("date")?>';
                            $post->class = str_replace('auto-blog-date', '', $post->class);
                            $this->data['types']['date'] = true;
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
    }

    public function getPostPageName() {
        if (!$this->hasFile()) return Array();
        return $this->data['post-page'];
    }

    public function getPostFields() {
        if (!$this->hasFile()) return Array();
        return $this->data['types'];
    }

    public function getPostOGTypes() {
        if (!$this->hasFile()) return Array();
        return $this->data['og-types'];
    }

    static public function getPostData($post_id) {
        $dataFile = 'data/blog/blog-' . $post_id . '.json';
        return json_decode(file_get_contents($dataFile), true);
    }

    public function getBlogList() {
        if (!$this->hasFile()) return Array();
        return $this->data['posts'];
    }

    public function updateBlogPost($post_id, $data, $publish = false) {
        $dataFile = $this->blogDataLocation . 'blog-' . $post_id . '.json';
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
            if (DashboardUtils::endsWith($key, '-loaded') && trim($datum) != '') $key = str_replace('-loaded', '', $key);
            if ($key != 'save') {
                $changeLog[] = Array('key' => $key, 'change' => Array('original' => $json[$key], 'new' => trim($datum)));
                $json[$key] = trim($datum);
            }
        }

        $logsData = new LogsData();
        if (count($changeLog) > 0 && !$isNew) {
            $logsData->addToLog('has updated', $data['title'] . ' blog', $changeLog);
        } else if (count($changeLog) > 0 && $isNew) {
            $logsData->addToLog('has created', $data['title'] . ' blog', $changeLog);
        }

        $externalTitle = preg_replace('/[^a-z0-9-]/i', '', str_replace(' ', '-', strtolower(trim($json['title']))));

        if ($isNew || (isset($this->data['posts'][$post_id]['external']) && $this->data['posts'][$post_id]['external'] != $externalTitle)) {
            $externalTitleOriginal = $externalTitle;

            $count = 0;
            foreach ($this->data['posts'] as $key => $data) {
                while ($data['external'] == $externalTitle) {
                    $externalTitle = $externalTitleOriginal . '-' . $count++;
                }
            }

            if ($isNew) {
                $this->data['posts'][$post_id] = Array('external' => $externalTitle, 'title' => $json['title'], 'creator' => $_SESSION["user"], 'created' => $creationTime);
            } else {
                $this->data['posts'][$post_id]['title'] = $json['title'];
                $this->data['posts'][$post_id]['external'] = $externalTitle;
                $this->data['posts'][$post_id]['last-updated'] = $updateTime;
            }
        } else {
            $this->data['posts'][$post_id]['last-updated'] = $updateTime;
        }

        if ($publish) {
            $this->data['posts'][$post_id]['published'] = $updateTime;
        }

        $domain = str_ireplace('www.', '', $_SERVER["HTTP_HOST"]);
        $postPage = $this->data['post-page'];
        $json['link-href'] = 'http://' . $domain . '/' . $postPage . '/' . $externalTitle . '/';

        $fp = fopen($dataFile, 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);

        DashboardUtils::createXMLSitemap();
    }

    public function publishPost($post_id) {
        $this->data['posts'][$post_id]['published'] = time();
        DashboardUtils::createXMLSitemap();
    }

    public function unpublishPost($post_id) {
        unset($this->data['posts'][$post_id]['published']);
        DashboardUtils::createXMLSitemap();
    }

    public function trashPost($post_id) {
        unset($this->data['posts'][$post_id]);
        if (file_exists($this->blogDataLocation . 'blog-' . $post_id . '.json')) unlink($this->blogDataLocation . 'blog-' . $post_id . '.json');
    }

    public function orderBlog() {
        $this->data['posts'] = DashboardUtils::arrayMSort($this->data['posts'], array('published'=>SORT_DESC));
    }
}

class Blog {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/blog.php');
        } else {
            include_once('401.html');
        }
    }
}

class BlogPost {
    function get($post_id = null, $action = null) {
        $users = new UsersData();
        if (is_null($post_id)) {
            include_once('404.html');
        } else if ($users->checkPass() && !$users->authNeeded()) {

            $blogData = new BlogData();
            if ($action == 'publish') {
                $blogData->publishPost($post_id);
                $blogData->orderBlog();
                header('Location: /admin/blog/?updated=true');
            } else if ($action == 'unpublish') {
                $blogData->unpublishPost($post_id);
                $blogData->orderBlog();
                header('Location: /admin/blog/?updated=true');
            } else if ($action == 'trash') {
                $blogData->trashPost($post_id);
                $blogData->orderBlog();
                header('Location: /admin/blog/?updated=true');
            } else {
                if ($post_id == 'new') {
                    $post_id = uniqid();
                } else {
                    $postInfo = $blogData->getPostData($post_id);
                }

                include_once('admin-pages/post.php');
            }
        } else {
            include_once('401.html');
        }
    }
    function post($post_id = null, $action = null) {
        $users = new UsersData();
        if (is_null($post_id)) {
            include_once('404.html');
        } else if ($users->checkPass() && !$users->authNeeded()) {
            $blogData = new BlogData();
            if ($action == 'update') {
                $blogData->updateBlogPost($post_id, $_POST, isset($_POST['publish']));
                $media = new MediaData();
                $media->uploadFiles($post_id, true);
            }
            $blogData->orderBlog();

            header('Location: /admin/blog/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}
