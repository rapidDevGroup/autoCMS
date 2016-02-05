<?php

class DashboardUtils {
    static public function scanFiles($endsWith) {
        $files = scandir('../');
        $arr = Array();
        foreach ($files as $file) {
            if (DashboardUtils::endsWith($file, $endsWith)) $arr[] = $file;
        }
        return $arr;
    }

    static public function endsWith($string, $test) {
        $strLen = strlen($string);
        $testLen = strlen($test);
        if ($testLen > $strLen) return false;
        return substr_compare($string, $test, $strLen - $testLen, $testLen) === 0;
    }

    static public function startsWith($haystack, $needle) {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
    }

    static public function renameFiles($files) {
        foreach ($files as $file) {
            $newName = str_ireplace(Array('.html', '.htm'), '.php', $file);
            rename('../' . $file, '../' . $newName);
        }
    }

    static public function backupFiles($files) {
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/admin/originals/')) mkdir($_SERVER['DOCUMENT_ROOT'] . '/admin/originals/');
        foreach ($files as $file) {
            if (file_exists('../' . $file)) copy('../' . $file, './originals/' . $file);
        }
    }

    static public function copyApacheConfig() {
        if (file_exists('./other/.htaccess2copy')) copy('./other/.htaccess2copy', '../.htaccess');
        if (file_exists('./other/robots.txt') && !file_exists('../robots.txt')) copy('./other/robots.txt', '../robots.txt');
    }

    static public function createXMLSiteMap() {
        $domain = str_ireplace('www.', '', $_SERVER["HTTP_HOST"]);

        if (!file_exists("../sitemap.xml") && file_exists("../robots.txt")) {
            $file = '../robots.txt';
            $siteMapLine = "\n\nSitemap: http://" . $domain . '/sitemap.xml';
            file_put_contents($file, $siteMapLine, FILE_APPEND);
        }

        $siteMap = new Sitemap('http://' . $domain . '/');
        $siteMap->setPath('../');

        $blogData = new BlogData();
        $postPageName = $blogData->getPostPageName();

        $siteMap->addItem('', '1', 'daily');

        $pagesData = new PagesData();
        $pages = $pagesData->getData();
        foreach ($pages as $page) {
            if ($page != $postPageName && $page != 'error' && $page != 'index') {
                $siteMap->addItem($page . '/', '0.5', 'daily');
            }
        }

        $blogList = $blogData->getBlogList();
        if (!empty($blogList)) {
            foreach ($blogList as $blog) {
                if (isset($blog['published'])) {
                    $siteMap->addItem($postPageName . '/' . $blog['external'] . '/', '1', 'monthly');
                }
            }
        }

        $siteMap->createSitemapIndex('http://' . $domain . '/', 'Today');
    }

    static public function arrayMSort($array, $cols) {
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
}

class Dashboard {
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

            DashboardUtils::backupFiles($_POST['files']);

            MinimizeTools::minimizeHTMLFiles($_POST['files']);

            new AnalyticsData();
            new MediaData();
            new SettingsData();

            $rss = new RSSData();
            $rss->buildDataFile($_POST['files']);

            $nav = new NavigationData();
            $nav->buildDataFile($_POST['files']);

            $blog = new BlogData();
            $blog->buildDataFile($_POST['files']);

            $footer = new FooterData();
            $footer->buildDataFile($_POST['files']);

            $pages = new PagesData();
            $pages->buildDataFile($_POST['files']);

            DashboardUtils::renameFiles($_POST['files']);
            DashboardUtils::copyApacheConfig();
            DashboardUtils::createXMLSiteMap();

            $logsData = new LogsData();
            $logsData->addToLog('has initiated the CMS on the following files:', implode(" ", $_POST['files']));

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
