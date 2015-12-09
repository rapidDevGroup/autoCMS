<?php

class PagesData extends DataBuild {
    public $dataFile = 'autocms-pages.json';

    public function addPage($pageName) {
        array_push($this->data, $pageName);
    }

    public function buildDataFile($files) {
        foreach ($files as $file) {
            $this->addPage(str_replace(Array('.html', '.htm'), '', $file));

            // create datafile to store stuff
            $dataFile = 'page-' . str_replace(Array('.html', '.htm'), '.json', $file);
            if (file_exists($this->dataLoc . $dataFile)) {
                $data = json_decode(file_get_contents($this->dataLoc . $dataFile), true);
            } else {
                $data = Array();
            }

            // start collecting fields to add to data
            $fileData = file_get_contents('../' . $file, true);

            $html = str_get_html($fileData);

            foreach ($html->find('html') as $htmlTag) {
                if (isset($htmlTag->lang)) {
                    $settingsData = new SettingsData();
                    $settingsData->setLang($htmlTag->lang);
                }
                $htmlTag->lang = "<?=get('autocms-settings.json', 'site-lang')?>";
            }

            foreach ($html->find('.auto-head title') as $pageTitle) {
                $data['title'] = Array('text' => $pageTitle->innertext, 'description' => 'title', 'type' => 'text');
                $pageTitle->innertext = "<?=get('$dataFile', 'title')?>";
            }

            foreach ($html->find('.auto-head meta') as $pageMeta) {
                if ($pageMeta->name == 'keywords' || $pageMeta->name == 'description' || $pageMeta->name == 'author') {
                    $data[$pageMeta->name] = Array('text' => $pageMeta->content, 'description' => $pageMeta->name, 'type' => 'text');
                    $pageMeta->content = "<?=get('$dataFile', '$pageMeta->name')?>";
                }
                if (isset($pageMeta->property) && isset($pageMeta->content)) {
                    $property = preg_replace("/[^a-z^A-Z^0-9_-]/", "", $pageMeta->property);
                    if ($pageMeta->property == "og:image") {
                        //todo: fix this

                        /*$imgFileName = '';
                        $source = $pageMeta->content;
                        $source = parse_url($source, PHP_URL_PATH);
                        $fileExt = pathinfo(parse_url($source, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $fileExt = MediaData::getImageType($fileExt, $source);

                        if ($fileExt != 'error') {
                            $media = new MediaData();
                            if (!$media->checkMediaLibrary('images', $source)) {
                                $imgFileName = $media->makeDateFolders() . uniqid() . '.' . $fileExt;

                                copy($source, $_SERVER['DOCUMENT_ROOT'] . $imgFileName);
                                $media->addToMediaLibrary('images', $imgFileName, $source);
                            } else {
                                $imgFileName = $media->getFromMediaLibrary('images', $source);
                            }
                        }
                        */
                        $data[$property] = Array('image' => $pageMeta->content, 'description' => $pageMeta->property, 'type' => 'image');
                        $pageMeta->content = "<?=get('$dataFile', '$property')?>";
                    } else if ($pageMeta->property == "og:url") {
                        $pageMeta->content = '<?="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]?>';
                    } else if ($pageMeta->property == "og:title") {
                        $pageMeta->content = "<?=get('$dataFile', 'title')?>";
                    } else if ($pageMeta->property == "og:description") {
                        $pageMeta->content = "<?=get('$dataFile', 'description')?>";
                    } else if ($pageMeta->property == "og:site_name") {
                        $pageMeta->content = "<?=get('autocms-settings.json', 'site-name')?>";
                    } else {
                        $data[$property] = Array('text' => $pageMeta->content, 'description' => $pageMeta->property, 'type' => 'text');
                        $pageMeta->content = "<?=get('$dataFile', '$property')?>";
                    }
                }
            }

            foreach($html->find('.auto-head') as $pageHead) {
                $pageHead->innertext .= "<?=get('autocms-analytics.json', 'analytics')?>";
                $pageHead->class = str_replace('auto-head', '', $pageHead->class);
                if (trim($pageHead->class) === '') $pageHead->class = null;
            }

            foreach($html->find('.auto-color, .auto-edit, .auto-edit-text, .auto-link, .auto-edit-img, .auto-edit-bg-img, .auto-repeat') as $edit) {
                $fieldID = uniqid();
                $desc = '';
                if (strpos($edit->class, 'auto-repeat') !== false) {

                    if (isset($edit->autocms)) $desc = $edit->autocms;
                    $data[$fieldID] = Array('repeat' => Array(), 'description' => $desc, 'type' => 'repeat');
                    $count = 0;
                    $data[$fieldID]['repeat'][$count] = Array();

                    foreach($html->find('.auto-repeat .auto-color, .auto-repeat .auto-edit, .auto-repeat .auto-edit-text, .auto-repeat .auto-link, .auto-repeat .auto-edit-img, .auto-repeat .auto-edit-bg-img') as $repeat) {
                        $desc = '';

                        $repeatFieldID = uniqid();
                        if (strpos($repeat->class, 'auto-edit-img') !== false) {
                            $this->makeImageBGImage($repeat, $data, $dataFile, $fieldID, $desc, false, $count, $repeatFieldID);
                        } else if (strpos($repeat->class, 'auto-edit-bg-img') !== false) {
                            $this->makeImageBGImage($repeat, $data, $dataFile, $fieldID, $desc, true, $count, $repeatFieldID);
                        } else if (strpos($repeat->class, 'auto-link') !== false) {
                            $this->makeLink($repeat, $data, $dataFile, $fieldID, $desc, $count, $repeatFieldID);
                        } else if (strpos($repeat->class, 'auto-edit-text') !== false) {
                            $this->makeHTMLText($repeat, $data, $dataFile, $fieldID, $desc, 'text', $count, $repeatFieldID);
                        } else if (strpos($repeat->class, 'auto-edit') !== false) {
                            $this->makeHTMLText($repeat, $data, $dataFile, $fieldID, $desc, 'html', $count, $repeatFieldID);
                        } else if (strpos($repeat->class, 'auto-color') !== false) {
                            $this->makeColor($repeat, $data, $dataFile, $fieldID, $desc, $count, $repeatFieldID);
                        }
                    }

                    $edit->class = str_replace('auto-repeat', '', $edit->class);
                    if (trim($edit->class) === '') $edit->class = null;
                    $edit->autocms = null;
                    $edit->outertext = '<?php for ($x = 0; $x ' . "< repeatCount('$dataFile', '$fieldID');" . ' $x++) { ?>' . $edit->outertext . "<?php } ?>";

                } else if (strpos($edit->class, 'auto-edit-img') !== false) {
                    $this->makeImageBGImage($edit, $data, $dataFile, $fieldID, $desc);
                } else if (strpos($edit->class, 'auto-edit-bg-img') !== false) {
                    $this->makeImageBGImage($edit, $data, $dataFile, $fieldID, $desc, true);
                } else if (strpos($edit->class, 'auto-link') !== false) {
                    $this->makeLink($edit, $data, $dataFile, $fieldID, $desc);
                } else if (strpos($edit->class, 'auto-edit-text') !== false) {
                    $this->makeHTMLText($edit, $data, $dataFile, $fieldID, $desc, 'text');
                } else if (strpos($edit->class, 'auto-edit') !== false) {
                    $this->makeHTMLText($edit, $data, $dataFile, $fieldID, $desc);
                } else if (strpos($edit->class, 'auto-color') !== false) {
                    $this->makeColor($edit, $data, $dataFile, $fieldID, $desc);
                }
            }

            // write data file
            $fp = fopen($this->dataLoc . $dataFile, 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);

            $fileTopper = '<?php require_once("admin/other/get.php") ?>';

            // write html file
            $fp = fopen('../' . $file, 'w');
            fwrite($fp, $fileTopper . $html);
            fclose($fp);
        }
    }

    static public function addVariableToPage($file, $name, $value) {
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

    static public function saveDescription($file, $editKey, $editDesc) {
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

    static public function getPageData($file) {
        $dataFile = 'data/page-' . $file . '.json';
        return json_decode(file_get_contents($dataFile), true);
    }

    static public function updatePage($file, $data) {
        $dataFile = 'data/page-' . $file . '.json';
        $json = json_decode(file_get_contents($dataFile), true);
        $changeLog = Array();

        foreach ($data as $key => $datum) {
            if (DashboardUtils::endsWith($key, '-loaded') && trim($datum) != '') $key = str_replace('-loaded', '', $key);
            if ($key != 'key' && isset($json[$key]) && $json[$key][$json[$key]['type']] != trim($datum)) {
                $changeLog[] = Array('key' => $key, 'change' => Array('original' => $json[$key][$json[$key]['type']], 'new' => trim($datum)));
                $json[$key][$json[$key]['type']] = trim($datum);
            } else {
                list($repeatKey, $iteration, $itemKey) = explode("-", $key);
                if (isset($json[$repeatKey]['repeat'][$iteration][$itemKey]) && $json[$repeatKey]['repeat'][$iteration][$itemKey][$json[$repeatKey]['repeat'][$iteration][$itemKey]['type']] != trim($datum)) {
                    $changeLog[] = Array('key' => $key, 'change' => Array('original' => $json[$repeatKey]['repeat'][$iteration][$itemKey][$json[$repeatKey]['repeat'][$iteration][$itemKey]['type']], 'new' => trim($datum)));
                    $json[$repeatKey]['repeat'][$iteration][$itemKey][$json[$repeatKey]['repeat'][$iteration][$itemKey]['type']] = trim($datum);
                }
            }
        }

        if (count($changeLog) > 0) {
            $logsData = new LogsData();
            $logsData->addToLog('has updated', $file . ' page', $changeLog);
        }

        $fp = fopen($dataFile, 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
    }

    static public function duplicateRepeat($page, $key, $num) {
        $dataFile = 'data/page-' . $page . '.json';
        $json = json_decode(file_get_contents($dataFile), true);

        foreach ($json as $jsonKey => $datum) {
            if ($key != 'key' && $jsonKey == $key && isset($json[$key]) && $json[$key]['type'] == 'repeat' && count($json[$key]['repeat']) > $num && isset($json[$key]['repeat'][$num])) {
                $logsData = new LogsData();
                $logsData->addToLog('has duplicated repeat on', $page . ' page', Array('key' => $key, 'change' => Array('duplicated' => $json[$key]['repeat'][$num])));
                $json[$key]['repeat'][] = $json[$key]['repeat'][$num];
            }
        }

        $fp = fopen($dataFile, 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
    }

    static public function deleteRepeat($page, $key, $num) {
        $dataFile = 'data/page-' . $page . '.json';
        $json = json_decode(file_get_contents($dataFile), true);

        foreach ($json as $jsonKey => $datum) {
            if ($key != 'key' && $jsonKey == $key && isset($json[$key]) && $json[$key]['type'] == 'repeat' && count($json[$key]['repeat']) > $num && isset($json[$key]['repeat'][$num])) {
                $logsData = new LogsData();
                $logsData->addToLog('has deleted repeat on', $page . ' page', Array('key' => $key, 'change' => Array('deleted' => $json[$key]['repeat'][$num])));
                array_splice($json[$key]['repeat'], $num, 1);
            }
        }

        $fp = fopen($dataFile, 'w');
        fwrite($fp, json_encode($json));
        fclose($fp);
    }

    static public function getRepeatData($file, $key) {
        $dataFile = 'data/page-' . $file . '.json';
        $json = json_decode(file_get_contents($dataFile), true);

        return $json[$key]['repeat'];
    }
}

class Pages {
    function get($page = null) {
        $users = new UsersData();
        if (is_null($page) && $users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/dash.php');
        } else if ($users->checkPass() && !$users->authNeeded()) {

            $data = PagesData::getPageData($page);

            include_once('admin-pages/page.php');
        } else {
            include_once('401.html');
        }
    }
    function post($page = null) {
        $users = new UsersData();
        if (is_null($page)) {
            include_once('404.html');
        } else if (!is_null($page) && $users->checkPass() && !$users->authNeeded()) {

            PagesData::updatePage($page, $_POST);
            $media = new MediaData();
            $media->uploadFiles($page);

            header('Location: /admin/page/' . $page . '/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}

class RepeatDel {
    function get($page, $key, $num) {
        $users = new UsersData();
        if (is_null($page) || is_null($key) || is_null($num)) {
            include_once('404.html');
        } else if ($users->checkPass() && !$users->authNeeded()) {
            PagesData::deleteRepeat($page, $key, $num);

            header('Location: /admin/page/' . $page . '/repeat/' . $key . '/');

        } else {
            include_once('401.html');
        }
    }
}

class RepeatDup {
    function get($page, $key, $num) {
        $users = new UsersData();
        if (is_null($page) || is_null($key) || is_null($num)) {
            include_once('404.html');
        } else if ($users->checkPass() && !$users->authNeeded()) {
            PagesData::duplicateRepeat($page, $key, $num);

            header('Location: /admin/page/' . $page . '/repeat/' . $key . '/');

        } else {
            include_once('401.html');
        }
    }
}

class Repeat {
    function get($page = null, $key = null) {
        $users = new UsersData();
        if (is_null($page) || is_null($key)) {
            include_once('admin-pages/dash.php');
        } else if ($users->checkPass() && !$users->authNeeded()) {

            $data = PagesData::getRepeatData($page, $key);

            include_once('admin-pages/repeat.php');
        } else {
            include_once('401.html');
        }
    }
    function post($page = null, $key = null) {
        $users = new UsersData();
        if (is_null($page) || is_null($key)) {
            include_once('404.html');
        } else if (!is_null($page) && $users->checkPass() && !$users->authNeeded()) {

            PagesData::updatePage($page, $_POST);
            $media = new MediaData();
            $media->uploadFiles($page);

            header('Location: /admin/page/' . $page . '/repeat/' . $key . '/?updated=true');

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

            PagesData::saveDescription('page-' . $page, $_POST['pk'], $_POST['value']);

            echo json_encode(StatusReturn::S200('Description Saved!'), JSON_NUMERIC_CHECK);
        } else if ($users->checkPass() && !$users->authNeeded()) {

            PagesData::saveDescription('autocms-' . $page, $_POST['pk'], $_POST['value']);

            echo json_encode(StatusReturn::S200('Description Saved!'), JSON_NUMERIC_CHECK);
        } else {
            echo json_encode(StatusReturn::E401('401 Not Authorized!'), JSON_NUMERIC_CHECK);
        }
    }
}
