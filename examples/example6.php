<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
use Google\StaticMaps\Path;
use Google\StaticMaps\PathPoint;

/*
 * Generates a 600x600 pixel google map, centered over 3 path points, 1 defined
 * using coordinates, the other 2 using a string. The path is filled, as there
 * are more than 2 with a transparent yellow.
 */


$oStaticMap = new Map();
$oStaticMap->setHeight(600)
		->setWidth(600)
		->setMapType('hybrid')
		->setFormat('png8');


//Create Path Object and set styling
$oPath = new Path();
$oPath->setColor('0x00000000')
		->setWeight(5)
		->setFillColor('0xFFFF0033');

//Create Point
$oPathPoint = new PathPoint();
$oPathPoint->setLatitude(51.855376)
		->setLongitude(-0.576904);
$oPath->setPoint($oPathPoint);

//Create Another Path Point
$oPathPoint2 = new PathPoint();
$oPathPoint2->setLocation('Wembley, UK');
$oPath->setPoint($oPathPoint2);

//Create Another Path Point
$oPathPoint3 = new PathPoint();
$oPathPoint3->setLocation('Barnet, UK');
$oPath->setPoint($oPathPoint3);

//Add Points to Map
$oStaticMap->setMapPath($oPath);

echo '<img src="' . $oStaticMap . '" height="' . $oStaticMap->getHeight() . '" width="' . $oStaticMap->getWidth() . '" />';