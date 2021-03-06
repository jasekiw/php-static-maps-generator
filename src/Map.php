<?php
namespace Google\StaticMaps;
use Exception;
use GuzzleHttp\Client;

/**
 * @author Ben Squire <b.squire@gmail.com>
 * @license Apache 2.0
 *
 * @package Map
 *
 * @abstract This class generates an img src which can be used to load a
 * 			'Google Static Map', it currently supports free features,
 * 			with plans to integrate the premium features at a later date.
 *
 * 			Editable Features include:
 * 				-	Map zoom, language, img format, scale etc
 * 				-	Markers
 * 				-	Feature Styling
 *
 * 			Please note Google restricts you to 25,000 unique map generations
 * 			each day.
 *
 * @see https://github.com/bensquire/php-static-maps-generator
 *
 * @example examples/example1.php
 * @example examples/example2.php
 * @example examples/example3.php
 * @example examples/example4.php
 * @example examples/example5.php
 * @example examples/example6.php
 */
class Map {

	const MAX_URL_LENGTH = 2048;
    
    const sGoogleURL = 'maps.google.com/maps/api/staticmap';
	const aLanguages = ['eu', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'en', 'en-AU', 'en-GB', 'es', 'eu', 'fa', 'fi', 'fil', 'fr', 'gl', 'gu', 'hi', 'hr', 'hu', 'id', 'it', 'iw', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'nl', 'nn', 'no', 'or', 'pl', 'pt', 'pt-BR', 'pt-PT', 'rm', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'tl', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh-CN', 'zh-TW'];
    const aFormatTypes = ['png', 'png8', 'png32', 'gif', 'jpg', 'jpg-baseline'];
    const aMapTypes = ['roadmap', 'satellite', 'hybrid', 'terrain'];
    const aScales = [1, 2, 4]; //4 is business only
	protected $bHTTPS = true; // by default we need it to be https or else google will reject our request
	protected $sAPIKey = null;  //TODO Finishing Adding
	protected $mCenter = null;  //{latitude,longitude} or ('city hall, new york, ny')
	protected $iZoom = 10;
	protected $iHeight = 200;
	protected $iWidth = 200;
	protected $iScale = 1;
	protected $sFileFormat = 'png';
	protected $sMapType = 'roadmap'; //See $map_types;
	protected $sLanguage = 'en-GB';
	protected $sRegion = '';   //TODO Add
	protected $aMarkers = [];
	protected $oPath = [];   //TODO Add
	protected $aVisible = [];  //TODO Add
	protected $aFeatureStyling = [];
	protected $bSensor = false;

	public function __construct() {
		
	}

	/**
	 * Magic Method to output final image source.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->buildSource();
	}
    
    /**
     * Sets a single map marker instance, using either an array of parameters,
     * or by passing in  _GoogleStaticMapMarker object
     *
     * e.g:    $map->setMarker(array('color'=>'blue','size'=>'mid','longitude'=>-0.12437000,'latitude'=>51.59413528));
     *
     * @param Marker | array $aParams
     *
     * @return Map
     * @throws Exception
     */
	public function setMarker($aParams) {

		if ($aParams instanceof Marker) {
			$this->aMarkers[] = $aParams;
		} elseif (is_array($aParams)) {
			$this->aMarkers[] = new Marker($aParams);
		} else {
			throw new Exception('Unknown marker type passed in, not array nor object');
		}

		return $this;
	}

	/**
	 * Sets the whether we should use https to retrieve the map
	 *
	 * @param bool $bHTTPs
	 *
*@return Map
	 */
	public function setHttps($bHTTPs) {
		$this->bHTTPS = (bool) $bHTTPs;
		return $this;
	}

	/**
	 * Set the API Key used to retrieve this map (server or client)
	 *
	 * @param string $sKey API key, letters and/or numbers only
	 * @return Map
	 * @throws Exception
	 */
	public function setAPIKey($sKey) {
		if (preg_match('/^[^a-zA-Z0-9]+$/i', $sKey)) {
			throw new Exception('Invalid API key');
		}

		$this->sAPIKey = $sKey;
		return $this;
	}

	/**
	 * Sets the center location of the map, actual location worked out by google
	 * so input varies greatly:
	 *
	 * e.g:	$map->setCenter('London,UK');
	 * e.g:	$map->setCenter('-0.12437000,51.59413528');
	 *
	 * @param string $sCenter
	 *
     * @return Map
	 */
	public function setCenter($sCenter) {
		$this->mCenter = $sCenter;
		return $this;
	}
    
    /**
     * Sets the maps resolution (1 == Normal, 2 == Double, 4 == Quad)
     *
     * @param int $iScale
     *
     * @return Map
     * @throws Exception
     */
	public function setScale($iScale) {
		if (!is_int($iScale) || !in_array($iScale, self::aScales)) {
			throw new Exception('Invalid map scale value: ' . $iScale);
		}

		$this->iScale = $iScale;
		return $this;
	}
    
    /**
     * Sets the zoom level of the map, valid values 0 to 22.
     *
     * e.g:    $map->setZoom(22);
     *
     * @param int $iZoom
     *
     * @return Map
     * @throws Exception
     */
	public function setZoom($iZoom) {
		if ($iZoom < 0 || $iZoom > 22) {
			throw new Exception('Invalid Zoom amount requested, 0 to 22, acceptable');
		}

		$this->iZoom = (int) $iZoom;
		return $this;
	}
    
    /**
     * Sets the map type, options are:
     * 'roadmap', 'satellite', 'hybrid', 'terrain'
     *
     * e.g:    $map->setMapType('satellite');
     *
     * @param string $sMapType
     *
     * @return Map
     * @throws Exception
     */
	public function setMapType($sMapType) {
		if (!in_array($sMapType, self::aMapTypes)) {
			throw new Exception('Unknown maptype requested.');
		}

		$this->sMapType = $sMapType;
		return $this;
	}
    
    /**
     * Sets the output format of the map. Expected formats are:
     * 'png', 'png8', 'png32', 'gif', 'jpg', 'jpg-baseline'
     *
     * e.g:    $map->setFormat('png8');
     *
     * @param string $sFileFormat
     *
     * @return Map
     * @throws Exception
     */
	public function setFormat($sFileFormat) {

		if (!in_array($sFileFormat, self::aFormatTypes)) {
			throw new Exception('Unknown image format requested');
		}

		$this->sFileFormat = $sFileFormat;
		return $this;
	}
    
    /**
     * Sets the height (in pixels) of the map. Maximum of 640px.
     *
     * e.g:    $map->setHeight(320);
     *
     * @param int $iHeight
     *
     * @return Map
     * @throws Exception
     */
	public function setHeight($iHeight) {

		if (!is_numeric($iHeight)) {
			throw new Exception('Heights must be numeric');
		}

		if ($iHeight > 640) {
			throw new Exception('Maximum height of 640px exceeded');
		}

		$this->iHeight = (int) $iHeight;
		return $this;
	}
    
    /**
     * Sets the width (in pixels) of the map. Maximum of 640px.
     *
     * e.g:    $map->setWidth(240);
     *
     * @param int $iWidth
     *
     * @return Map
     * @throws Exception
     */
	public function setWidth($iWidth) {

		if (!is_numeric($iWidth)) {
			throw new Exception('Widths must be numeric');
		}

		if ($iWidth > 640) {
			throw new Exception('Maximum width of 640px exceeded');
		}

		$this->iWidth = (int) $iWidth;
		return $this;
	}
    
    /**
     * Set the language of the map, acceptable values are:
     * 'eu', 'bg', 'bn', 'ca', 'cs', 'da', 'de', 'el', 'en', 'en-AU', 'en-GB', 'es', 'eu', 'fa', 'fi', 'fil', 'fr', 'gl', 'gu', 'hi', 'hr', 'hu', 'id', 'it', 'iw', 'ja', 'kn', 'ko', 'lt', 'lv', 'ml', 'mr', 'nl', 'nn', 'no', 'or', 'pl', 'pt', 'pt-BR', 'pt-PT', 'rm', 'ro', 'ru', 'sk', 'sl', 'sr', 'sv', 'tl', 'ta', 'te', 'th', 'tr', 'uk', 'vi', 'zh-CN', 'zh-TW'
     *
     * e.g:    $map->setLanguage('ca');
     *
     * @param string $sLanguage
     *
     * @return Map
     * @throws Exception
     */
	public function setLanguage($sLanguage) {
		if (!in_array($sLanguage, self::aLanguages)) {
			throw new Exception('Unknown language requested');
		}

		$this->sLanguage = $sLanguage;
		return $this;
	}
    
    /**
     * Create (or adds) the styling of single the map feature, pass in either an object of _GoogleStaticMapFeature or an array of parameters
     *
     * e.g:    $map->setFeatureStyling(array('feature'=>'all', 'element'=>'all', 'style'=>array('hue'=>'6095C6', 'saturation'=>-23, 'gamma'=>3.88, 'lightness'=>16)));
     *
     * @param MapFeature| array  $mFeatureStyling
     *
     * @return Map
     * @throws Exception
     */
	public function setFeatureStyling($mFeatureStyling) {
		if ($mFeatureStyling instanceof MapFeature) {
			$this->aFeatureStyling[] = $mFeatureStyling;
		} elseif (is_array($mFeatureStyling)) {
			$this->aFeatureStyling[] = new MapFeature($mFeatureStyling);
		} else {
			throw new Exception('Unknown Feature Styling Passed');
		}

		return $this;
	}

	/**
	 * Creates the GoogleMapPath object used to draw points on the map. Either
	 * pass an array of values through, or an Path object.
	 * 
	 * @param mixed $mPath Path or array to build object
	 * @return Map
	 */
	public function setMapPath($mPath) {
		if ($mPath instanceof Path)
			$this->oPath = $mPath;
		 elseif (is_array($mPath))
			$this->oPath = new Path($mPath);
		return $this;
	}

	/**
	 * Returns an array of set Marker objects;
	 *
	 * e.g:	$markers = $map->getMarkers();
	 *
	 * @return array
	 */
	public function getMarkers() {
		return $this->aMarkers;
	}

	/**
	 * Returns the parameter passed to set the map
	 *
	 * e.g:	$center = $map->getCenter();
	 *
	 * @return string
	 */
	public function getCenter() {
		return $this->mCenter;
	}

	/**
	 * Returns the zoom level set.
	 *
	 * e.g:	$zoom = $map->getZoom();
	 *
	 * @return int
	 */
	public function getZoom() {
		return $this->iZoom;
	}

	/**
	 * Returns the set map type.
	 *
	 * e.g:	$type = $map->getType();
	 *
	 * @return string
	 */
	public function getMapType() {
		return $this->sMapType;
	}

	/**
	 * Returns the set format of the map
	 *
	 * e.g:	$format = $map->getFormat();
	 *
	 * @return string
	 */
	public function getFormat() {
		return self::aFormatTypes;
	}

	/**
	 * Returns the set height of the map
	 *
	 * e.g:	$height = $map->getHeight();
	 *
	 * @return int
	 */
	public function getHeight() {
		return $this->iHeight;
	}

	/**
	 * Returns the set width of the map
	 *
	 * e.g:	$width = $map->getWidth();
	 *
	 * @return int
	 */
	public function getWidth() {
		return $this->iWidth;
	}

	/**
	 * Returns the set language of the map;
	 *
	 * e.g:	$language = $map->getLanguage();
	 *
	 * @return string
	 */
	public function getLanguage() {
		return $this->sLanguage;
	}

	/**
	 * Returns the an array of map feature stylings.
	 *
	 * e.g:	$styling = $map->getFeatureStyling();
	 *
	 * @return array
	 */
	public function getFeatureStyling() {
		return $this->aFeatureStyling;
	}

	/**
	 * Checks whether the url is within the allowed length
	 *
	 * @param string $sString
	 * @return boolean
	 */
	public function validLength($sString) {
		return ((strlen($sString) > $this::MAX_URL_LENGTH) ? false : true);
	}
    
    /**
     * Creates the final url for the image tag
     *
     * e.g:    $url = $map->buildSource();
     * @return string
     * @throws Exception
     */
	public function buildSource() {
		$aURL = array();

		$aURL[] = 'center=' . urlencode($this->mCenter);
		$aURL[] = 'zoom=' . $this->iZoom;
		$aURL[] = 'language=' . $this->sLanguage;
		$aURL[] = 'maptype=' . $this->sMapType;
		$aURL[] = 'format=' . $this->sFileFormat;
		$aURL[] = 'size=' . $this->iWidth . 'x' . $this->iHeight;
		$aURL[] = 'scale=' . $this->iScale;


		if (strlen($this->sAPIKey) > 0) {
			$aURL[] = 'key=' . $this->sAPIKey;
		}

		if (!empty($this->aMarkers)) {
			foreach ($this->aMarkers AS $oMarker) {
				$aURL[] = $oMarker->build();
			}
		}

		if (!empty($this->aFeatureStyling)) {
			foreach ($this->aFeatureStyling AS $oFeature) {
				$aURL[] = $oFeature->build();
			}
		}


		if ($this->oPath instanceof Path) {
			$aURL[] = $this->oPath->build();
		}

		$aURL[] = 'sensor=' . (($this->bSensor) ? 'true' : 'false');

		$sSrcTag = 'http' . (($this->bHTTPS === true) ? 's' : '') . '://' . self::sGoogleURL . '?' . implode('&', $aURL);

		if (!$this->validLength($sSrcTag)) {
			throw new Exception('URL Exceeded maxiumum length of ' . $this::MAX_URL_LENGTH . ' characters.');
		}

		return $sSrcTag;
	}
    
    /**
     * @param bool $dataUrl adds the data url header to the base64 encoded image
     *
     * @return string
     */
    public function getImage($dataUrl = false) {
        $client = new Client([
            'timeout'  => 5.0,
        ]);
        $url = $this->buildSource();
        $img = base64_encode($client->get($url)->getBody()->getContents());
        if($dataUrl) {
            $type = $this->sFileFormat;
            if(strpos($type, "png") !== false)
                $type = "png";
            else if(strpos($type, "jpeg") !== false)
                $type = "jpg";
            $img = "data:image/" . $type . ";base64," . $img;
        }
        return $img;
    }

}