<?php
use Google\StaticMaps\Map;
class MapTest extends PHPUnit_Framework_TestCase
{
    public function test_map() {
        $oStaticMap = new Map();
        $oStaticMap->setCenter("London,UK")
            ->setHeight(300)
            ->setWidth(232)
            ->setZoom(8)
            ->setHttps(true);
        self::assertEquals(300, $oStaticMap->getHeight());
        self::assertEquals(232, $oStaticMap->getWidth());
        self::assertEquals(
            'https://maps.google.com/maps/api/staticmap?center=London%2CUK&zoom=8&language=en-GB&maptype=roadmap&format=png&size=232x300&scale=1&sensor=false',
            $oStaticMap->buildSource()
        );
    }
}