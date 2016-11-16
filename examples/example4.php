<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
use Google\StaticMaps\Marker;

/*
 * Generates a 300x232 pixel google map, centered over london, using an HTTPS
 * connection, 2 markers (Med & Large) with labels.
 */


$oStaticMap = new Map();
$oStaticMap->setCenter('London,UK')
		->setHeight(300)
		->setWidth(300)
		->setZoom(8)
		->setMapType('hybrid')
		->setFormat('png');

$oStaticMap->setMarker(array(
	'color' => 'blue',
	'size' => 'mid',
	'longitude' => -0.062004,
	'latitude' => 51.462564,
	'label' => 'C'
));

$oMarker = new Marker();
$oMarker->setColor('red')
		->setSize('large')
		->setLongitude(-0.576904)
		->setLatitude(51.855376)
		->setLabel('B');

$oStaticMap->setMarker($oMarker);

echo '<img src="' . $oStaticMap . '" height="' . $oStaticMap->getHeight() . '" width="' . $oStaticMap->getWidth() . '" />';