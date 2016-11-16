<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
/*
 * Generates a 300x232 pixel google map, centered over london, using an HTTPS
 * connection
 */


$oStaticMap = new Map();
$oStaticMap->setCenter("London,UK")
		->setHeight(300)
		->setWidth(232)
		->setZoom(8)
		->setHttps(true);

echo '<img src="' . $oStaticMap . '" height="' . $oStaticMap->getHeight() . '" width="' . $oStaticMap->getWidth() . '" />';
