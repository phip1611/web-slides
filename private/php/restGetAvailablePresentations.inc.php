<?php
$presentations = array();
$dir = dir(__DIR__.'/../src/html/');

if ($dir != null) {
    while (false !== ($entry = $dir->read())) {
        if (preg_match('/presentation-([A-z0-9-])+/', $entry)) {
            $entry = str_replace('presentation-', '', $entry);
            $entry = str_replace('.html', '', $entry);
            $presentations[] = $entry;
        }
    }
    $dir->close();
};
$jsonData->setData('availablePresentations', $presentations);
die($jsonData->getJsonAsString());