<?php
require "../vendor/autoload.php";
use Google\StaticMaps\Map;
/*
 * Generates a 300x232 pixel google map, centered over london, 
 * with light green features.
 */


$oStaticMap = new Map();
$oStaticMap->setCenter("London,UK")
		->setHeight(300)
		->setWidth(232)
		->setZoom(8)
		->setFormat("jpg")
		->setFeatureStyling([
			"feature" => "all",
			"element" => "all",
			"style" => [
				"hue" => "#006400", //Green features
				"lightness" => 50  //Very light...
            ]
        ]);

echo '<img src="' . $oStaticMap . '" height="' . $oStaticMap->getHeight() . '" width="' . $oStaticMap->getWidth() . '" />';
