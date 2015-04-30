<?php

function get($file, $id, $count = null, $secondary = null) {
    $dataFile = 'admin/data/' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    if (!is_null($secondary) && !is_null($count) && $json[$id]['type'] == 'repeat') {
        return $json[$id]['repeat'][$count][$secondary][$json[$id]['repeat'][$count][$secondary]['type']];
    }
    return $json[$id][$json[$id]['type']];
}

function repeatCount($file, $id) {
    $dataFile = 'admin/data/' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    return count($json[$id]['repeat']);
}