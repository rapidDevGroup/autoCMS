<?php

function get($file, $id) {
    $dataFile = 'admin/data/page-' . $file;
    $json = json_decode(file_get_contents($dataFile), true);

    return $json[$id][$json[$id]['type']];
}