<?php

class MediaData extends Data {
    public $dataFile = 'autocms-media.json';

    public function createFile() {
        if (!file_exists($this->dataLoc . $this->dataFile)) {
            $this->data = Array();
            $this->data['images'] = Array();
            $fp = fopen($this->dataLoc . $this->dataFile, 'w');
            fwrite($fp, json_encode($this->data));
            fclose($fp);
        }
    }

    public function addToMediaLibrary($type, $location, $originalLocation = null) {
        if (is_null($originalLocation) || trim($originalLocation) != '') {
            $imgId = uniqid();
            $this->data[$type][$imgId] = Array('original-location' => $originalLocation, 'location' => $location);
        }
    }

    public function checkMediaLibrary($type, $originalLocation) {
        foreach ($this->data[$type] as $entry) {
            if ($entry['original-location'] == $originalLocation) return true;
        }

        return false;
    }

    public function getFromMediaLibrary($type, $originalLocation) {
        foreach ($this->data[$type] as $entry) {
            if ($entry['original-location'] == $originalLocation) return $entry['location'];
        }

        return '';
    }

    public function deleteImage($type, $id) {
        foreach ($this->data[$type] as $key => $entry) {
            if ($key == $id) {
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $entry['location'])) unlink($_SERVER['DOCUMENT_ROOT'] . $entry['location']);
            }
        }
        unset($this->data[$type][$id]);
    }

    public function uploadFiles($page, $isBlog = false) {
        foreach ($_FILES as $key => $data) {
            if ($_FILES[$key]['error'] == 0) {
                $sourceName = $_FILES[$key]['name'];

                $fileExt = pathinfo($sourceName, PATHINFO_EXTENSION);
                if ($fileExt === '') {
                    $detect = exif_imagetype($sourceName);
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

                if ($fileExt != 'error') {
                    $imgFileName = $this->makeDateFolders() . uniqid() . '.' . $fileExt;
                    move_uploaded_file($_FILES[$key]['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $imgFileName);
                    $this->addToMediaLibrary('images', $imgFileName);

                    $dataFile = null;
                    $json = null;
                    if (!$isBlog && $page != 'media') {
                        $dataFile = 'data/page-' . $page . '.json';
                        $json = json_decode(file_get_contents($dataFile), true);

                        foreach ($json as $jsonKey => $datum) {
                            if ($key == $jsonKey && $json[$key]['type'] == 'image' && isset($json[$key])) {
                                $json[$key]['image'] = $imgFileName;
                            } else {
                                list($repeatKey, $iteration, $itemKey) = explode("-", $key);
                                if (isset($json[$repeatKey]['repeat'][$iteration][$itemKey]) && $json[$repeatKey]['repeat'][$iteration][$itemKey]['type'] == 'image') {
                                    $json[$repeatKey]['repeat'][$iteration][$itemKey]['image'] = $imgFileName;
                                }
                            }
                        }
                    } else if ($isBlog) {
                        $dataFile = 'data/blog/blog-' . $page . '.json';
                        $json = json_decode(file_get_contents($dataFile), true);
                        $json['image'] = $imgFileName;
                    }

                    if ($page != 'media') {
                        $fp = fopen($dataFile, 'w');
                        fwrite($fp, json_encode($json));
                        fclose($fp);
                    }
                }
            }
        }
    }

    public function makeDateFolders() {
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/assets/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/assets/');
        }
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/assets/auto-images/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/assets/auto-images/');
        }
        $year = date("Y", time());
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/assets/auto-images/'.$year.'/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/assets/auto-images/'.$year.'/');
        }
        $month = date("m", time());
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/assets/auto-images/'.$year.'/'.$month.'/')) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/assets/auto-images/'.$year.'/'.$month.'/');
        }

        return '/assets/auto-images/'.$year.'/'.$month.'/';
    }

    static public function getImageType($fileExt, $source) {
        if ($fileExt === '' && file_exists($source)) {
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
}

class Media {
    function get() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {
            include_once('admin-pages/media.php');
        } else {
            include_once('401.html');
        }
    }
    function post() {
        $users = new UsersData();
        if ($users->checkPass() && !$users->authNeeded()) {

            $media = new MediaData();
            if (isset($_POST['delete'])) {
                $media->deleteImage('images', $_POST['delete']);
            }

            if (!empty($_FILES)) {
                $media->uploadFiles('media');
            }

            header('Location: /admin/media/?updated=true');
        } else {
            include_once('401.html');
        }
    }
}