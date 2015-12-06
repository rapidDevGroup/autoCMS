<?php

class Dash {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/dash.php');
        } else {
            include_once('401.html');
        }
    }
    function post($action = null) {
        $users = new UsersData();
        if ($action == 'process' && $users->checkPass() && !$users->authNeeded()) {

            new AnalyticsData();
            new MediaData();

            $nav = new NavigationData();
            $nav->buildDataFile($_POST['files']);
            processBlog($_POST['files']);
            $footer = new FooterData();
            $footer->buildDataFile($_POST['files']);

            $pages = new PagesData();
            $pages->buildDataFile($_POST['files']);
            renameFiles($_POST['files']);
            copyApacheConfig();
            createXMLSitemap();
            addToLog('has initiated the CMS', implode(" ", $_POST['files']));

            header('Location: /admin/');
        } else {
            include_once('401.html');
        }
    }
    function post_xhr($action = null) {
        $users = new UsersData();
        if (is_null($action)) {
            echo json_encode(StatusReturn::E400('400 Missing Required Data!'), JSON_NUMERIC_CHECK);
        } else if ($action == 'change-pass' && $users->checkPass() && !$users->authNeeded()) {
            if ($_POST['current'] != '' && $_POST['password'] != '' && $_POST['password'] == $_POST['password2'] && $users->checkPass(null, $_POST['current'])) {

                $users->changePassword($_POST['password']);

                echo json_encode(StatusReturn::S200('Password Changed!'), JSON_NUMERIC_CHECK);
            } else {
                echo json_encode(StatusReturn::E400('400 Missing Required Data!'), JSON_NUMERIC_CHECK);
            }
        } else {
            echo json_encode(StatusReturn::E401('401 Not Authorized!'), JSON_NUMERIC_CHECK);
        }
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
            if ($action == 'publish') {
                publishPost($post_id);
                header('Location: /admin/blog/?updated=true');
                orderBlog();
                die();
            } else if ($action == 'unpublish') {
                unpublishPost($post_id);
                header('Location: /admin/blog/?updated=true');
                orderBlog();
                die();
            } else if ($action == 'trash') {
                trashPost($post_id);
                header('Location: /admin/blog/?updated=true');
                orderBlog();
                die();
            }

            if ($post_id == 'new') $post_id = uniqid();
            else $postInfo = getPostData($post_id);

            include_once('admin-pages/post.php');
        } else {
            include_once('401.html');
        }
    }
    function post($post_id = null, $action = null) {
        $users = new UsersData();
        if (is_null($post_id)) {
            include_once('404.html');
        } else if ($users->checkPass() && !$users->authNeeded()) {

            if ($action == 'update') {
                updateBlogPost($post_id, $_POST, isset($_POST['publish']));
                $media = new MediaData();
                $media->uploadFiles($post_id, true);
            }
            orderBlog();

            header('Location: /admin/blog/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}

class Logs {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/logs.php');
        } else {
            include_once('401.html');
        }
    }
}

class Description {
    function post_xhr($page = null) {
        $users = new UsersData();
        if (is_null($page)) {
            echo json_encode(StatusReturn::E400('400 Missing Required Data!'), JSON_NUMERIC_CHECK);
        } else if ($page != 'nav' && $users->checkPass() && !$users->authNeeded()) {

            saveDescription('page-' . $page, $_POST['pk'], $_POST['value']);

            echo json_encode(StatusReturn::S200('Description Saved!'), JSON_NUMERIC_CHECK);
        } else if ($users->checkPass() && !$users->authNeeded()) {

            saveDescription('autocms-' . $page, $_POST['pk'], $_POST['value']);

            echo json_encode(StatusReturn::S200('Description Saved!'), JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(StatusReturn::E401('401 Not Authorized!'), JSON_NUMERIC_CHECK);
        }
    }
}

