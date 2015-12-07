<?php

class BlogData extends Data {

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
