<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
/*
 * Generates a 300x232 pixel google map, centered over london, using an HTTPS
 * connection
 */


$oStaticMap = new Map();
$oStaticMap->setCenter("London,UK")
    ->setHeight(232)
    ->setWidth(300)
    ->setZoom(8)
    ->setHttps(true);
$img = $oStaticMap->getImage(true);
echo '<img src="' . $img . '" height="' . $oStaticMap->getHeight() . '" width="' . $oStaticMap->getWidth() . '" />';