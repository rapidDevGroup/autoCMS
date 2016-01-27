<?php

class NavigationData extends DataBuild {
    public $dataFile = 'autocms-nav.json';
    public $sectionName = 'navigation';

    function buildDataFile($files) {
        foreach ($files as $file) {
            $fileData = file_get_contents('../' . $file, true);

            $html = str_get_html($fileData);

            foreach($html->find('.auto-nav, .auto-nav-internal, .auto-nav-link, .auto-nav-text') as $navigation) {
                $desc = '';
                if (isset($navigation->autocms)) $desc = $navigation->autocms;
                $fieldID = uniqid();
                if (strpos($navigation->class, 'auto-nav') !== false) {
                    if (isset($navigation->autocms)) {
                        $desc = preg_replace("/[^a-z^A-Z^0-9_-]/", "", $navigation->autocms);

                        $this->data[$desc] = Array('text' => $navigation->innertext, 'description' => $navigation->autocms, 'type' => 'text');
                        $navigation->innertext = "<?=get('$this->dataFile', '$desc')?>";
                        $navigation->href = str_replace(Array('index.html', 'index.htm', '.html', '.htm'), '/', '/' . $navigation->href);
                        $navigation->href = str_replace('//', '/', $navigation->href);

                        //$navigation->class = str_replace('auto-nav', '', $navigation->class);
                        //if (trim($navigation->class) === '') $navigation->class = null;
                    }
                } else if (strpos($navigation->class, 'auto-nav-link') !== false) {
                    $this->makeNavLink($navigation, $this->data, $this->dataFile, $fieldID, $desc);
                } else if (strpos($navigation->class, 'auto-nav-text') !== false) {
                    $this->makeNavText($navigation, $this->data, $this->dataFile, $fieldID, $desc);
                } else if (strpos($navigation->class, 'auto-nav-internal') !== false) {
                    $navigation->href = str_replace(Array('index.html', 'index.htm', '.html', '.htm'), '/', '/' . $navigation->href);
                    $navigation->href = str_replace('//', '/', $navigation->href);

                    //$navigation->class = str_replace('auto-nav-internal', '', $navigation->class);
                    //if (trim($navigation->class) === '') $navigation->class = null;
                }
            }

            $fp = fopen('../' . $file, 'w');
            fwrite($fp, $html);
            fclose($fp);
        }
    }
}

class Nav {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/nav.php');
        } else {
            include_once('401.html');
        }
    }
    function post() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {

            $nav = new NavigationData();
            $nav->updateData($_POST);

            header('Location: /admin/nav/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}