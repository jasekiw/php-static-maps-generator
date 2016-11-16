<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
/*
 * Generates a 300x232 pixel google map, centered over london,
 * with feature styling and a medium marker placed off centre.
 */


$oStaticMap = new Map();
$oStaticMap->setCenter('London,UK')
		->setHeight(300)
		->setWidth(232)
		->setZoom(8)
		->setMapType('hybrid')
		->setFormat('png');

$oStaticMap->setMarker([
	'color' => 'blue',
	'size' => 'mid',
	'longitude' => -0.062004,
	'latitude' => 51.462564,
	'label' => 'b'
]);

echo '<img src="' . $oStaticMap . '" height="' . $oStaticMap->getHeight() . '" width="' . $oStaticMap->getWidth() . '" />';