<?php

namespace Maaaxim;

use Maaaxim\Photo\Exceptions\GpsException;
use Maaaxim\Photo\Gps;
use Maaaxim\Photo\Scanner;

require_once("vendor/autoload.php");

$scanner = new Scanner();
$photoList = $scanner->scanDirectory("photos");

$gps = new Gps();

foreach($photoList as $photo){
    try {
        $coordinates = $gps->getImageLocation($photo);
        echo "lat: {$coordinates["latitude"]}; lon: {$coordinates["longitude"]}".PHP_EOL;
    } catch (GpsException $exception) {
        echo "{$photo}: Set up GPS to EXIF data".PHP_EOL;
    }
}