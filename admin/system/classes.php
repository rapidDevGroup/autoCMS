<?php

include_once('classes/analytics.php');
include_once('classes/blog.php');
include_once('classes/dashboard.php');
include_once('classes/footer.php');
include_once('classes/logs.php');
include_once('classes/media.php');
include_once('classes/minimize.php');
include_once('classes/navigation.php');
include_once('classes/pages.php');
include_once('classes/rss.php');
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

    function addHas(&$edit, $dataFile, $fieldID, $count = null, $repeatFieldID = null) {
        $level = $edit->getAttribute('data-autocms-has');
        if (is_numeric($level)) {
            if (is_null($count)) {
                if ($level == 0) {
                    $edit->outertext = "<?php if (has('$dataFile', '$fieldID')) { ?>" . $edit->outertext . "<?php } ?>";
                } else if ($level == 1) {
                    $edit->parent()->outertext = "<?php if (has('$dataFile', '$fieldID')) { ?>" . $edit->parent()->outertext . "<?php } ?>";
                } else if ($level == 2) {
                    $edit->parent()->parent()->outertext = "<?php if (has('$dataFile', '$fieldID')) { ?>" . $edit->parent()->parent()->outertext . "<?php } ?>";
                }
            } else {
                if ($level === 0) {
                    $edit->outertext = "<?php if (has('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')) { ?>" . $edit->outertext . "<?php } ?>";
                } else if ($level == 1) {
                    $edit->parent()->outertext = "<?php if (has('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')) { ?>" . $edit->parent()->outertext . "<?php } ?>";
                } else if ($level == 2) {
                    $edit->parent()->parent()->outertext = "<?php if (has('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')) { ?>" . $edit->parent()->parent()->outertext . "<?php } ?>";
                }
            }
        }
    }
    
    public function addHasBlog(&$edit, $fieldID, $list = false, $dataFile = null) {
        $level = $edit->getAttribute('data-autocms-has');
        if (is_numeric($level)) {
            if ($list) {
                file_put_contents('levels.txt', "list: $level isnull:" . is_null($level) . " isnumeric: " . is_numeric($level) . "\n", FILE_APPEND);
                if ($level == 0) {
                    file_put_contents('levels.txt', "level zero $level\n", FILE_APPEND);
                    $edit->outertext = "<?php if (hasBlog('$fieldID', " . '$x' . ", '$dataFile')) { ?>" . $edit->outertext . "<?php } ?>";
                } else if ($level == 1) {
                    $edit->parent()->outertext = "<?php if (hasBlog('$fieldID', " . '$x' . ", '$dataFile')) { ?>" . $edit->parent()->outertext . "<?php } ?>";
                } else if ($level == 2) {
                    $edit->parent()->parent()->outertext = "<?php if (hasBlog('$fieldID', " . '$x' . ", '$dataFile')) { ?>" . $edit->parent()->parent()->outertext . "<?php } ?>";
                }
            } else {
                file_put_contents('levels.txt', "not list: $level isnull:" . is_null($level) . " isnumeric: " . is_numeric($level) . "\n", FILE_APPEND);
                if ($level == 0) {
                    file_put_contents('levels.txt', "level zero $level\n", FILE_APPEND);
                    $edit->outertext = "<?php if (hasBlog('$fieldID')) { ?>" . $edit->outertext . "<?php } ?>";
                } else if ($level == 1) {
                    $edit->parent()->outertext = "<?php if (hasBlog('$fieldID')) { ?>" . $edit->parent()->outertext . "<?php } ?>";
                } else if ($level == 2) {
                    $edit->parent()->parent()->outertext = "<?php if (hasBlog('$fieldID')) { ?>" . $edit->parent()->parent()->outertext . "<?php } ?>";
                }
            }
            file_put_contents('levels.txt', "end $level\n", FILE_APPEND);
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

    function makeNavLink(&$edit, &$dataArr, $dataFile, $fieldID, $desc) {
        $dataArr[$fieldID] = Array('link' => trim($edit->href), 'description' => $desc, 'type' => 'link');
        $edit->href = "<?=get('$dataFile', '$fieldID')?>";
        $this->addHas($edit, $dataFile, $fieldID);
    }

    function makeNavText(&$edit, &$dataArr, $dataFile, $fieldID, $desc) {
        $dataArr[$fieldID] = Array('text' => trim($edit->innertext), 'description' => $desc, 'type' => 'text');
        $edit->innertext = "<?=get('$dataFile', '$fieldID')?>";
        $this->addHas($edit, $dataFile, $fieldID);
    }

    function makeLink(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $count = null, $repeatFieldID = null) {
        if (is_null($repeatFieldID)) {
            $dataArr[$fieldID] = Array('link' => trim($edit->href), 'description' => $desc, 'type' => 'link');
            $edit->href = "<?=get('$dataFile', '$fieldID')?>";
            $this->addHas($edit, $dataFile, $fieldID);
        } else {
            $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array('link' => trim($edit->href), 'description' => $desc, 'type' => 'link');
            $edit->href = "<?=get('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')?>";
            $this->addHas($edit, $dataFile, $fieldID, $count, $repeatFieldID);
        }
    }

    function makeHTMLText(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $type = 'html', $count = null, $repeatFieldID = null) {
        if (is_null($repeatFieldID)) {
            $dataArr[$fieldID] = Array($type => trim($edit->innertext), 'description' => $desc, 'type' => $type);
            $edit->innertext = "<?=get('$dataFile', '$fieldID')?>";
            $this->addHas($edit, $dataFile, $fieldID);
        } else {
            $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array($type => trim($edit->innertext), 'description' => $desc, 'type' => $type);
            $edit->innertext = "<?=get('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')?>";
            $this->addHas($edit, $dataFile, $fieldID, $count, $repeatFieldID);
        }
    }

    function makeDataText(&$edit, &$dataArr, $dataFile, $fieldID, $count = null) {
        $dataList = explode(' ', $edit->getAttribute('data-autocms-data'));
        foreach ($dataList AS $attribute) {
            $currentData = $edit->getAttribute('data-' . $attribute);
            $desc = $attribute;
            if (is_null($count)) {
                $fieldID = uniqid();
                $dataArr[$fieldID] = Array('text' => trim($currentData), 'description' => $desc, 'type' => 'text');
                $edit->setAttribute('data-' . $attribute, "<?=get('$dataFile', '$fieldID')?>");
                $this->addHas($edit, $dataFile, $fieldID);
            } else {
                $repeatFieldID = uniqid();
                $dataArr[$fieldID]['repeat'][$count][$repeatFieldID] = Array('text' => trim($currentData), 'description' => $desc, 'type' => 'text');
                $edit->setAttribute('data-' . $attribute, "<?=get('$dataFile', '$fieldID', " . '$x' . ", '$repeatFieldID')?>");
                $this->addHas($edit, $dataFile, $fieldID, $count, $repeatFieldID);
            }
        }
    }

    function makeColor(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $count = null, $repeatFieldID = null) {
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
            $edit->style = str_ireplace($tag, '', $edit->style) . "background-color: <?=get('$dataFile', '$fieldID', ".'$x'.", '$repeatFieldID')?>;";
            $this->addHas($edit, $dataFile, $fieldID, $count, $repeatFieldID);
        } else {
            $edit->style = str_ireplace($tag, '', $edit->style) . "background-color: <?=get('$dataFile', '$fieldID')?>;";
            $this->addHas($edit, $dataFile, $fieldID);
        }
    }

    function makeImageBGImage(&$edit, &$dataArr, $dataFile, $fieldID, $desc, $isBG = false, $count = null, $repeatFieldID = null) {
        $tag = null;
        $source = null;

        if ($isBG) {
            $source = $edit->style;
            preg_match('~\bbackground(-image)?\s*:(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $source, $matches);
            $source = $matches[4];
            $tag = $matches[0];
        } else {
            $source = $edit->src;
        }

        if ($source[0] != '/') $source = '/' . $source;
        if ($source[0] == '/') $source = $_SERVER['DOCUMENT_ROOT'] . $source;

        $fileExt = pathinfo(parse_url($edit->src, PHP_URL_PATH), PATHINFO_EXTENSION);
        $fileExt = MediaData::getImageType($fileExt, $source);

        if ($fileExt != 'error') {
            $media = new MediaData();
            if (!$media->checkMediaLibrary('images', $source)) {
                $imgFileName = $media->makeDateFolders() . uniqid() . '.' . $fileExt;

                if (file_exists($source)) copy($source, $_SERVER['DOCUMENT_ROOT'] . $imgFileName);
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
                    $edit->style = str_ireplace($tag, '', $edit->style) . "background-image: url('<?=get('$dataFile', '$fieldID', ".'$x'.", '$repeatFieldID')?>');";
                } else {
                    $edit->style = str_ireplace($tag, '', $edit->style) . "background-image: url('<?=get('$dataFile', '$fieldID')?>');";
                }
            } else {
                $altText = $edit->alt;
                if ($altText === false) $altText = '';
                $altFieldID = uniqid();
                if (!is_null($repeatFieldID)) {
                    $edit->src = "<?=get('$dataFile', '$fieldID', ".'$x'.", '$repeatFieldID')?>";
                    $dataArr[$fieldID]['repeat'][$count][$altFieldID] = Array('text' => $altText, 'description' => 'image alt text', 'type' => 'text', 'parent' => $fieldID);

                    $this->addHas($edit, $dataFile, $fieldID, $count, $repeatFieldID);
                } else {
                    $edit->src = "<?=get('$dataFile', '$fieldID')?>";
                    $dataArr[$altFieldID] = Array('text' => $altText, 'description' => 'image alt text', 'type' => 'text', 'parent' => $fieldID);

                    $this->addHas($edit, $dataFile, $fieldID);
                }

                if (!is_null($repeatFieldID)) {
                    $edit->alt = "<?=get('$dataFile', '$fieldID', ".'$x'.", '$altFieldID')?>";
                } else {
                    $edit->alt = "<?=get('$dataFile', '$altFieldID')?>";
                }
            }
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