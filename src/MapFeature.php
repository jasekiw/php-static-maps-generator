<?php
namespace Google\StaticMaps;
use Exception;

/**
 * @author Ben Squire <b.squire@gmail.com>
 * @license Apache 2.0
 *
 * @package Map
 *
 * @abstract This class abstracts the map feature part of the Google
 * Static map API. i.e: Determine those map features that are visible
 * and how they are styled.
 *
 * @see https://github.com/bensquire/php-static-maps-generator
 */
class MapFeature {

	const SEPERATOR = '|';

	protected $aFeatures = array(
		'administrative',
		'administrative.country',
		'administrative.land_parcel',
		'administrative.locality',
		'administrative.neighborhood',
		'administrative.province',
		'all',
		'landscape',
		'landscape.man_made',
		'landscape.natural',
		'poi',
		'poi.attraction',
		'poi.business',
		'poi.government',
		'poi.medical',
		'poi.park',
		'poi.place_of_worship',
		'poi.school',
		'poi.sports_complex',
		'road',
		'road.arterial',
		'road.highway',
		'road.highway.controlled_access',
		'road.local',
		'transit',
		'transit.line',
		'transit.station',
		'transit.station.airport',
		'transit.station.bus',
		'transit.station.rail',
		'water'
	);
	protected $aElements = array('all', 'geometry', 'labels');
	protected $sFeature = null;
	protected $sElement = null;
	protected $oStyle = null;

	public function __construct($aParams = array()) {
		if (isset($aParams['feature'])) {
			$this->setFeature($aParams['feature']);
		}

		if (isset($aParams['element'])) {
			$this->setElement($aParams['element']);
		}

		if (isset($aParams['style'])) {
			$this->setStyle($aParams['style']);
		}
	}
    
    /**
     * Sets the type of feature the object represents
     *
     * @param string $sFeature
     *
     * @return MapFeature
     * @throws Exception
     */
	public function setFeature($sFeature) {
		if (!in_array($sFeature, $this->aFeatures)) {
			throw new Exception('Unknown Map Feature');
		}

		$this->sFeature = $sFeature;
		return $this;
	}
    
    /**
     * Creates the feature styling object either using an associative array of
     * values or by passing in an instance of _GoogleStaticMapFeatureStyling.
     *
     * @param FeatureStyling $mStyle
     *
     * @return MapFeature
     * @throws Exception
     */
	public function setStyle($mStyle) {
		if ($mStyle instanceof FeatureStyling) {
			$this->oStyle = $mStyle;
		} elseif (is_array($mStyle)) {
			$this->oStyle = new FeatureStyling($mStyle);
		} else {
			throw new Exception('Invalid type passed to Map Feature Styling.');
		}

		return $this;
	}
    
    /**
     * Sets the element of the feature you are styling, 'all', 'geometry', 'labels'.
     *
     * @param string $sElement
     *
     * @return MapFeature
     * @throws Exception
     */
	public function setElement($sElement) {
		if (!in_array($sElement, $this->aElements)) {
			throw new Exception('Unknown Map Element');
		}

		$this->sElement = $sElement;
		return $this;
	}

	/**
	 * Returns the feature being editted
	 *
	 * @return string
	 */
	public function getFeature() {
		return $this->sFeature;
	}

	/**
	 * Returns the features styling object.
	 *
	 * @return FeatureStyling
	 */
	public function getStyle() {
		return $this->oStyle;
	}

	/**
	 * Returns the element of the feature thats being styled
	 *
	 * @return string
	 */
	public function getElement() {
		return $this->sElement;
	}

	/**
	 * Builds the url string of the feature styling
	 *
	 * @return string
	 */
	public function build() {
		if ($this->oStyle instanceOf FeatureStyling) {
			$styles = $this->oStyle->build();
		}

		return 'style=feature:' . $this->sFeature . $this::SEPERATOR . 'element:' . $this->sElement . ((isset($styles) ? $this::SEPERATOR . $styles : ''));
	}

}

?>