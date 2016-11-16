<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
use Google\StaticMaps\Path;
use Google\StaticMaps\PathPoint;

/*
 * Generates a 600x600 pixel google map, centered over 2 path points, 1 defined
 * using coordinates, the other using a string. Scale set to 2 (double resolution)
 */


$oStaticMap = new Map();
$oStaticMap->setHeight(600)
		->setWidth(600)
		->setMapType('hybrid')
		->setFormat('jpg')
		->setScale(2);

//Create Path Object and set styling
$oPath = new Path();
$oPath->setColor('red')
		->setWeight(5);

//Create Path Point
$oPathPoint = new PathPoint();
$oPathPoint->setLatitude(51.855376)
		->setLongitude(-0.576904);
$oPath->setPoint($oPathPoint);

//Create Another Path Point
$oPathPoint2 = new PathPoint();
$oPathPoint2->setLocation('Wembley, UK');
$oPath->setPoint($oPathPoint2);

//Add Points to Map
$oStaticMap->setMapPath($oPath);

echo '<img src="' . $oStaticMap . '" height="' . $oStaticMap->getHeight() * 2 . '" width="' . $oStaticMap->getWidth() * 2 . '" />';