<?php

// todo: Maybe separate blog and blog posts stuff?

class BlogData extends Data {
    public $dataFile = 'autocms-blog.json';
    public $blogDataLocation;

    function __construct() {
        $this->blogDataLocation = $this->dataLoc . 'blog/';
        parent::__construct();
    }

    public function createFile() {
        if (!file_exists($this->dataLoc . $this->dataFile)) {
            $this->data = Array('post-page' => null, 'og-types' => Array(), 'types' => Array('title' => false, 'keywords' => false, 'description' => false, 'author' => false, 'seo-schemas' => true, 'date' => false, 'categories' => false, 'image' => false, 'image-alt-text' => false, 'short-blog' => false, 'full-blog' => false, 'link-text' => false, 'link-href' => false, 'open-graph' => false), 'posts' => Array());
            $fp = fopen($this->dataLoc . $this->dataFile, 'w');
            fwrite($fp, json_encode($this->data));
            fclose($fp);
        }

        if (!is_dir($this->blogDataLocation)) {
            mkdir($this->blogDataLocation);
        }
    }

    private function scanForAutoCurly($fileData, $file) {
        if (stripos($fileData, 'auto-blog-list') !== false) {
            $fileData = preg_replace("/{-{auto-blog-link-href}-}/i", "<?=get('autocms-settings.json', 'site-host')?>".'<?=getBlog("link-href", $x,"' . $file . '")?>', $fileData);
            $fileData = preg_replace("/{-{auto-blog-post-id}-}/i", '<?=getBlog("post-id", $x,"' . $file . '")?>', $fileData);
        } else if (stripos($fileData, 'auto-blog-post') !== false) {
            $fileData = preg_replace("/{-{auto-blog-link-href}-}/i", "<?=get('autocms-settings.json', 'site-host')?>".'<?=getBlog("link-href")?>', $fileData);
            $fileData = preg_replace("/{-{auto-blog-post-id}-}/i", '<?=getBlog("post-id")?>', $fileData);
        }

        return $fileData;
    }

    public function buildDataFile($files) {

        foreach ($files as $file) {
            $fileData = file_get_contents('../' . $file, true);

            $fileData = $this->scanForAutoCurly($fileData, $file);

            $html = str_get_html($fileData);

            // pages in blog lists
            foreach($html->find('.auto-blog-next, .auto-blog-prev, .auto-blog-has-prev, .auto-blog-has-next') as $pagination) {
                if (stripos($pagination->class, 'auto-blog-next') !== false) {
                    $pagination->href = '<?=getBlogPage("next","' . $file . '")?>';
                    $this->data['types']['link-next'] = true;
                    $this->data['list-pagination-pages'] = str_ireplace(Array('.html', '.htm'), '', $file);
                } else if (stripos($pagination->class, 'auto-blog-prev') !== false) {
                    $pagination->href = '<?=getBlogPage("prev","' . $file . '")?>';
                    $this->data['types']['link-prev'] = true;
                    $this->data['list-pagination-pages'] = str_ireplace(Array('.html', '.htm'), '', $file);
                } else if (stripos($pagination->class, 'auto-blog-has-next') !== false) {
                    $this->data['pagination']['has-next'] = $pagination->style;
                    $pagination->style = '<?=getBlogPage("has-next","' . $file . '")?>';
                } else if (stripos($pagination->class, 'auto-blog-has-prev') !== false) {
                    $this->data['pagination']['has-prev'] = $pagination->style;
                    $pagination->style = '<?=getBlogPage("has-prev","' . $file . '")?>';
                }
            }

            // pages in posts
            foreach($html->find('.auto-blog-post .auto-blog-next-post, .auto-blog-post .auto-blog-prev-post, .auto-blog-post .auto-blog-has-prev-post, .auto-blog-post .auto-blog-has-next-post') as $pagination) {
                if (stripos($pagination->class, 'auto-blog-next-post') !== false) {
                    $pagination->href = '<?=getBlogPostControls("next")?>';
                    $this->data['types']['link-next-post'] = true;
                } else if (stripos($pagination->class, 'auto-blog-prev-post') !== false) {
                    $pagination->href = '<?=getBlogPostControls("prev")?>';
                    $this->data['types']['link-prev-post'] = true;
                } else if (stripos($pagination->class, 'auto-blog-has-next-post') !== false) {
                    $this->data['pagination']['has-next-post'] = $pagination->style;
                    $pagination->style = '<?=getBlogPostControls("has-next")?>';
                } else if (stripos($pagination->class, 'auto-blog-has-prev-post') !== false) {
                    $this->data['pagination']['has-prev-post'] = $pagination->style;
                    $pagination->style = '<?=getBlogPostControls("has-prev")?>';
                }
            }

            foreach($html->find('.auto-blog-head, .auto-blog-list, .auto-blog-post') as $blog) {
                if (stripos($blog->class, 'auto-blog-head') !== false) {
                    foreach($html->find('.auto-blog-head title') as $pageTitle) {
                        $pageTitle->innertext = "<?=getBlog('title')?>";
                        $this->data['types']['title'] = true;
                    }
                    foreach($html->find('.auto-blog-head meta') as $pageMeta) {
                        if ($pageMeta->name == 'keywords' || $pageMeta->name == 'description') {
                            $pageMeta->content = "<?=getBlog('$pageMeta->name')?>";
                            $this->data['types'][$pageMeta->name] = true;
                        } else if ($pageMeta->name == 'author') {
                            $pageMeta->content = "<?=getBlog('$pageMeta->name', null, null, true)?>";
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
                                $pageMeta->content = "<?=getBlog('author', null, null, true)?>";
                            } else if ($pageMeta->property == "og:url") {
                                $pageMeta->content = "<?=get('autocms-settings.json', 'site-host')?><?=getBlog('link-href')?>";
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
                    $blog->innertext .= "<?=getBlog('seo-schemas')?>";

                } else if (stripos($blog->class, 'auto-blog-list') !== false) {
                    foreach($html->find('.auto-blog-list .auto-blog-title, .auto-blog-list .auto-blog-date, .auto-blog-list .auto-blog-cats, .auto-blog-list .auto-blog-bg-img, .auto-blog-list .auto-blog-img, .auto-blog-list .auto-blog-short, .auto-blog-list .auto-blog-full, .auto-blog-list .auto-blog-link, .auto-blog-list .auto-blog-link-href, .auto-blog-list .auto-blog-author') as $list) {
                        if (stripos($list->class, 'auto-blog-title') !== false) {
                            $list->innertext = '<?=getBlog("title", $x,"' . $file . '")?>';
                            $this->data['types']['title'] = true;
                            $this->addHasBlog($list, 'title', true, $file);
                        } else if (stripos($list->class, 'auto-blog-bg-img') !== false) {
                            $list->style = "background-image: url('<?=getBlog(" . '"image", $x' . ", '$file')?>');";
                            $this->data['types']['image'] = true;
                            //$this->addHasBlog($list, 'image', true, $file);
                        } else if (stripos($list->class, 'auto-blog-img') !== false) {
                            $list->src = '<?=getBlog("image", $x,"' . $file . '")?>';
                            $list->alt = '<?=getBlog("image-alt-text", $x,"' . $file . '")?>';
                            $this->data['types']['image'] = true;
                            $this->data['types']['image-alt-text'] = true;
                            $this->addHasBlog($list, 'image', true, $file);
                        } else if (stripos($list->class, 'auto-blog-short') !== false) {
                            $list->innertext = '<?=getBlog("short-blog", $x,"' . $file . '")?>';
                            $this->data['types']['short-blog'] = true;
                            $this->addHasBlog($list, 'short-blog', true, $file);
                        } else if (stripos($list->class, 'auto-blog-full') !== false) {
                            $list->innertext = '<?=getBlog("full-blog", $x,"' . $file . '")?>';
                            $this->data['types']['full'] = true;
                            $this->addHasBlog($list, 'full-blog', true, $file);
                        } else if (stripos($list->class, 'auto-blog-date') !== false) {
                            $list->innertext = '<?=getBlog("date", $x,"' . $file . '")?>';
                            $this->data['types']['date'] = true;
                            $this->addHasBlog($list, 'date', true, $file);
                        } else if (stripos($list->class, 'auto-blog-cats') !== false) {
                            $list->innertext = '<?=getBlog("categories", $x,"' . $file . '")?>';
                            $this->data['types']['categories'] = true;
                            $this->addHasBlog($list, 'categories', true, $file);
                        } else if (stripos($list->class, 'auto-blog-link-href') !== false) {
                            $list->href = "<?=get('autocms-settings.json', 'site-host')?>".'<?=getBlog("link-href", $x,"' . $file . '")?>';
                            $this->data['types']['link-href'] = true;
                            $this->addHasBlog($list, 'link-href', true, $file);
                        } else if (stripos($list->class, 'auto-blog-link') !== false) {
                            $list->href = "<?=get('autocms-settings.json', 'site-host')?>".'<?=getBlog("link-href", $x,"' . $file . '")?>';
                            $list->innertext = '<?=getBlog("link-text", $x,"' . $file . '")?>';
                            $this->data['types']['link-text'] = true;
                            $this->addHasBlog($list, 'link-text', true, $file);
                        } else if (stripos($list->class, 'auto-blog-author') !== false) {
                            $list->innertext = '<?=getBlog("author", $x,"' . $file . '")?>';
                            $this->data['types']['author'] = true;
                            $this->addHasBlog($list, 'author', true, $file);
                        }
                        if (trim($list->class) === '') $list->class = null;
                    }

                    $blog->outertext = '<?php for ($x = 0; $x ' . '< blogCount("' . $file . '", "blog-count");' . ' $x++) { ?>' . $blog->outertext . "<?php } ?>";

                    PagesData::addVariableToPage($file, 'blog-count', Array('blog-count' => 3, 'description' => 'blog display count', 'type' => 'blog-count'));

                } else if (stripos($blog->class, 'auto-blog-post') !== false) {
                    $this->data['post-page'] = str_ireplace(Array('.html', '.htm'), '', $file);
                    foreach($html->find('.auto-blog-post .auto-blog-title, .auto-blog-post .auto-blog-date, .auto-blog-post .auto-blog-cats, .auto-blog-post .auto-blog-bg-img, .auto-blog-post .auto-blog-img, .auto-blog-post .auto-blog-short, .auto-blog-post .auto-blog-full, .auto-blog-post .auto-blog-author, .auto-blog-post .auto-blog-link-href') as $post) {
                        if (stripos($post->class, 'auto-blog-title') !== false) {
                            $post->innertext = '<?=getBlog("title")?>';
                            $this->data['types']['title'] = true;
                            $this->addHasBlog($post, 'title');
                        } else if (stripos($post->class, 'auto-blog-bg-img') !== false) {
                            $post->style = "background-image: url('<?=getBlog(" . '"image"' . ")?>');";
                            $this->data['types']['image'] = true;
                            $this->addHasBlog($post, 'image');
                        } else if (stripos($post->class, 'auto-blog-img') !== false) {
                            $post->src = '<?=getBlog("image")?>';
                            $post->alt = '<?=getBlog("image-alt-text")?>';
                            $this->data['types']['image'] = true;
                            $this->data['types']['image-alt-text'] = true;
                            $this->addHasBlog($post, 'image');
                        } else if (stripos($post->class, 'auto-blog-short') !== false) {
                            $post->innertext = '<?=getBlog("short-blog")?>';
                            $this->data['types']['short-blog'] = true;
                            $this->addHasBlog($post, 'short-blog');
                        } else if (stripos($post->class, 'auto-blog-full') !== false) {
                            $post->innertext = '<?=getBlog("full-blog")?>';
                            $this->data['types']['full-blog'] = true;
                            $this->addHasBlog($post, 'full-blog');
                        } else if (stripos($post->class, 'auto-blog-date') !== false) {
                            $post->innertext = '<?=getBlog("date")?>';
                            $this->data['types']['date'] = true;
                            $this->addHasBlog($post, 'date');
                        } else if (stripos($post->class, 'auto-blog-cats') !== false) {
                            $post->innertext = '<?=getBlog("categories")?>';
                            $this->data['types']['categories'] = true;
                            $this->addHasBlog($post, 'categories');
                        } else if (stripos($post->class, 'auto-blog-author') !== false) {
                            $post->innertext = '<?=getBlog("author")?>';
                            $this->data['types']['author'] = true;
                            $this->addHasBlog($post, 'author');
                        } else if (stripos($post->class, 'auto-blog-link-href') !== false) {
                            $post->href = "<?=get('autocms-settings.json', 'site-host')?>" . '<?=getBlog("link-href")?>';
                            $this->data['types']['link-href'] = true;
                            $this->addHasBlog($post, 'link-href');
                        }
                        if (trim($post->class) === '') $post->class = null;
                    }
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
            if ($key != 'save' && $key != 'publish') {
                $changeLog[] = Array('key' => $key, 'change' => Array('original' => $json[$key], 'new' => trim($datum)));
                $json[$key] = trim($datum);
            }
        }
        $json['published'] = $updateTime;

        $logsData = new LogsData();
        if (count($changeLog) > 0 && !$isNew) {
            $logsData->addToLog('has updated', $data['title'] . ' blog', $changeLog);
        } else if (count($changeLog) > 0 && $isNew) {
            $logsData->addToLog('has created', $data['title'] . ' blog', $changeLog);
        }

        $externalTitle = $this->cleanURL($json['title']);

        if ($isNew || (isset($this->data['posts'][$post_id]['external']) && $this->data['posts'][$post_id]['external'] != $externalTitle)) {
            $externalTitleOriginal = $externalTitle;

            $count = 0;
            if (!empty($this->data['posts'])) {
                foreach ($this->data['posts'] as $key => $data) {
                    while ($data['external'] == $externalTitle) {
                        $externalTitle = $externalTitleOriginal . '-' . $count++;
                    }
                }
            }

            if ($isNew) {
                $this->data['posts'][$post_id] = Array('external' => $externalTitle, 'title' => $json['title'], 'author' => $json['author'], 'categories' => $json['categories'], 'creator' => $_SESSION["user"], 'created' => $creationTime);
            } else {
                $this->data['posts'][$post_id]['title'] = $json['title'];
                $this->data['posts'][$post_id]['external'] = $externalTitle;
                $this->data['posts'][$post_id]['author'] = $json['author'];
                $this->data['posts'][$post_id]['categories'] = $json['categories'];
                $this->data['posts'][$post_id]['last-updated'] = $updateTime;
            }
        } else {
            $this->data['posts'][$post_id]['last-updated'] = $updateTime;
            $this->data['posts'][$post_id]['title'] = $json['title'];
            $this->data['posts'][$post_id]['author'] = $json['author'];
            $this->data['posts'][$post_id]['categories'] = $json['categories'];
            $this->data['posts'][$post_id]['last-updated'] = $updateTime;
        }

        if ($publish) {
            $this->data['posts'][$post_id]['published'] = $updateTime;
        }

        $postPage = $this->data['post-page'];
        $json['link-href'] = '/' . $postPage . '/' . $externalTitle . '/';
        $json['post-id'] = $externalTitle;

        $fp = fopen($dataFile, 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);

        DashboardUtils::createXMLSiteMap();
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

    public function publishPost($post_id) {
        $this->data['posts'][$post_id]['published'] = time();
        DashboardUtils::createXMLSiteMap();
    }

    public function unpublishPost($post_id) {
        unset($this->data['posts'][$post_id]['published']);
        DashboardUtils::createXMLSiteMap();
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
