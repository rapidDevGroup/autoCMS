<?php

include_once('classes/analytics.php');
include_once('classes/footer.php');
include_once('classes/logs.php');
include_once('classes/media.php');
include_once('classes/navigation.php');
include_once('classes/pages.php');
include_once('classes/settings.php');
include_once('classes/users.php');

class Data {
    public $dataFile;
    public $dataLoc = 'data/';
    public $data;

    function __construct() {
        if (!$this->hasFile()) {
            $this->createFile();
        }
        $this->data = json_decode(file_get_contents($this->dataLoc . $this->dataFile), true);
    }

    function __destruct() {
        $fp = fopen($this->dataLoc . $this->dataFile, 'w');
        fwrite($fp, json_encode($this->data));
        fclose($fp);
    }

    public function createFile() {
        if (!file_exists($this->dataLoc . $this->dataFile)) {
            $this->data = Array();
            $fp = fopen($this->dataLoc . $this->dataFile, 'w');
            fwrite($fp, json_encode($this->data));
            fclose($fp);
        }
    }

    public function getData() {
        return $this->data;
    }

    public function hasFile() {
        return file_exists($this->dataLoc . $this->dataFile);
    }
}

class DataBuild extends Data {
    public $sectionName;

    function makeLink(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $count = null, $repeatFieldID = null) {
        if (isset($edit->autocms)) $desc = $edit->autocms;

        if (is_null($repeatFieldID)) {
            $dataArr[$fieldID] = Array('link' => trim($edit->href), 'description' => $desc, 'type' => 'link');
            $edit->href = "<?=get('$dataFile', '$fieldID')?>";
        } else {
            $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array('link' => trim($edit->href), 'description' => $desc, 'type' => 'link');
            $edit->href = "<?=get('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')?>";
        }

        $edit->class = str_replace('auto-link', '', $edit->class);
        if (trim($edit->class) === '') $edit->class = null;
        $edit->autocms = null;
    }

    function makeHTMLText(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $type = 'html', $count = null, $repeatFieldID = null) {
        if (isset($edit->autocms)) $desc = $edit->autocms;

        if (is_null($repeatFieldID)) {
            $dataArr[$fieldID] = Array($type => trim($edit->innertext), 'description' => $desc, 'type' => $type);
            $edit->innertext = "<?=get('$dataFile', '$fieldID')?>";
        } else {
            $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array($type => trim($edit->innertext), 'description' => $desc, 'type' => $type);
            $edit->innertext = "<?=get('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')?>";
        }
        if ($type == 'html') {
            $edit->class = str_replace('auto-edit', '', $edit->class);
        } else {
            $edit->class = str_replace('auto-edit-text', '', $edit->class);
        }
        if (trim($edit->class) === '') $edit->class = null;
        $edit->autocms = null;
    }

    function makeColor(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $count = null, $repeatFieldID = null) {
        if (isset($edit->autocms)) $desc = $edit->autocms;

        $color = $edit->style;
        preg_match('~\bbackground(-color)?\s*:\s*(#[0-9a-f]{3,6}|[a-z\(\0-9,\)]+)\s*(;)?~i', $color, $matches);
        $color = $matches[2];
        $tag = $matches[0];

        if (!is_null($repeatFieldID)) {
            $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array('color' => $color, 'description' => $desc, 'type' => 'color');
        } else {
            $dataArr[$fieldID] = Array('color' => $color, 'description' => $desc, 'type' => 'color');
        }

        if (!is_null($repeatFieldID)) {
            $edit->style = str_replace($tag, '', $edit->style) . "background-color: <?=get('$dataFile', '$fieldID', ".'$x'.", '$repeatFieldID')?>;";
        } else {
            $edit->style = str_replace($tag, '', $edit->style) . "background-color: <?=get('$dataFile', '$fieldID')?>;";
        }
        $edit->class = str_replace('auto-color', '', $edit->class);
        if (trim($edit->class) === '') $edit->class = null;

        $edit->autocms = null;
    }

    function makeImageBGImage(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $isBG = false, $count = null, $repeatFieldID = null) {
        if (isset($edit->autocms)) $desc = $edit->autocms;
        $tag = null;
        $source = null;

        if ($isBG) {
            $source = $edit->style;
            preg_match('~\bbackground(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $source, $matches);
            $source = $matches[4];
            $tag = $matches[0];
            if (substr($source, 0, 1) == "/") $source = $_SERVER['DOCUMENT_ROOT'] . $source;
        } else {
            $source = $edit->src;
            if (substr($source, 0, 1) == "/") $source = $_SERVER['DOCUMENT_ROOT'] . $source;
        }

        $fileExt = pathinfo(parse_url($edit->src, PHP_URL_PATH), PATHINFO_EXTENSION);
        $fileExt = getImageType($fileExt, $source);

        if ($fileExt != 'error') {
            $media = new MediaData();
            if (!$media->checkMediaLibrary('images', $source)) {
                $imgFileName = $media->makeDateFolders() . uniqid() . '.' . $fileExt;

                copy($source, $_SERVER['DOCUMENT_ROOT'] . $imgFileName);
                $media->addToMediaLibrary('images', $imgFileName, $source);
            } else {
                $imgFileName = $media->getFromMediaLibrary('images', $source);
            }

            if (!is_null($repeatFieldID)) {
                $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array('image' => $imgFileName, 'description' => $desc, 'type' => 'image');
            } else {
                $dataArr[$fieldID] = Array('image' => $imgFileName, 'description' => $desc, 'type' => 'image');
            }

            if ($isBG) {
                if (!is_null($repeatFieldID)) {
                    $edit->style = str_replace($tag, '', $edit->style) . "background-image: url('<?=get('$dataFile', '$fieldID', ".'$x'.", '$repeatFieldID')?>');";
                } else {
                    $edit->style = str_replace($tag, '', $edit->style) . "background-image: url('<?=get('$dataFile', '$fieldID')?>');";
                }
                $edit->class = str_replace('auto-edit-bg-img', '', $edit->class);
            } else {
                $altText = $edit->alt;
                if ($altText === false) $altText = '';
                $altFieldID = uniqid();
                if (!is_null($repeatFieldID)) {
                    $edit->src = "<?=get('$dataFile', '$fieldID', ".'$x'.", '$repeatFieldID')?>";
                    $dataArr[$fieldID]['repeat'][$count][$altFieldID] = Array('text' => $altText, 'description' => 'image alt text', 'type' => 'text', 'parent' => $fieldID);
                } else {
                    $edit->src = "<?=get('$dataFile', '$fieldID')?>";
                    $dataArr[$altFieldID] = Array('text' => $altText, 'description' => 'image alt text', 'type' => 'text', 'parent' => $fieldID);
                }

                if (!is_null($repeatFieldID)) {
                    $edit->alt = "<?=get('$dataFile', '$fieldID', ".'$x'.", '$altFieldID')?>";
                } else {
                    $edit->alt = "<?=get('$dataFile', '$altFieldID')?>";
                }
                $edit->class = str_replace('auto-edit-img', '', $edit->class);
            }
            if (trim($edit->class) === '') $edit->class = null;
            $edit->autocms = null;
        }
    }

    public function updateData($newData) {
        $changeLog = Array();

        foreach ($newData as $key => $datum) {
            if ($this->data[$key][$this->data[$key]['type']] != trim($datum)) {
                $changeLog[] = Array('key' => $key, 'change' => Array('original' => $this->data[$key][$this->data[$key]['type']], 'new' => trim($datum)));
                $this->data[$key][$this->data[$key]['type']] = trim($datum);
            }
        }

        if (count($changeLog) > 0) {
            $logsData = new LogsData();
            $logsData->addToLog('has updated', $this->sectionName, $changeLog);
        }
    }
}