<?php

function get($file, $key, $count = null, $secondary = null) {
    $dataFile = 'admin/data/' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    if (!is_null($secondary) && !is_null($count) && $json[$key]['type'] == 'repeat') {
        return $json[$key]['repeat'][$count][$secondary][$json[$key]['repeat'][$count][$secondary]['type']];
    }
    return $json[$key][$json[$key]['type']];
}

function getBlog($count, $key) {
    $dataFile = 'admin/data/autocms-blog.json';
    $json = json_decode(file_get_contents($dataFile), true);

}

function repeatCount($file, $key) {
    $dataFile = 'admin/data/' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    return count($json[$key]['repeat']);
}