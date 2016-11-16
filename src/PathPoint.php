<?php
namespace Google\StaticMaps;
use Exception;

/**
 * @author Ben Squire <b.squire@gmail.com>
 * @license Apache 2.0
 *
 * @package Map
 *
 * @abstract This class abstracts the path points that can be placed onto the
 * Google Static Maps. Either via coordinates or as a string location.
 *
 * @see https://github.com/bensquire/php-static-maps-generator
 */
class PathPoint {

	protected $fLongitude = null;
	protected $fLatitude = null;
	protected $sLocation = null;

	public function __construct($aParams = array()) {
		
	}
    
    /**
     * Set the longitude of the map point.
     *
     * @param float $fLongitude
     *
     * @return PathPoint
     * @throws Exception
     */
	public function setLongitude($fLongitude) {
		if (!is_numeric($fLongitude)) {
			throw new Exception('Invalid longitude value.');
		}

		$this->fLongitude = (float) $fLongitude;
		return $this;
	}
    
    /**
     * Set the Latitude of the map point.
     *
     * @param float $fLatitude
     *
     * @return PathPoint
     * @throws Exception
     */
	public function setLatitude($fLatitude) {
		if (!is_numeric($fLatitude)) {
			throw new Exception('Invalid latitude value.');
		}
		$this->fLatitude = (float) $fLatitude;
		return $this;
	}

	/**
	 * Set a string location of the map point.
	 *
	 * @param string $sLocation
	 *
     * @return PathPoint
	 * @throws Exception
	 */
	public function setLocation($sLocation) {
		if (strlen($sLocation) === 0) {
			throw new Exception('No string location provided...');
		}

		$this->sLocation = (string) $sLocation;
		return $this;
	}

	/**
	 * Return the float longitude
	 * 
	 * @return float
	 */
	public function getLongitude() {
		return $this->fLongitude;
	}

	/**
	 * Return the float of the latitude
	 * 
	 * @return float
	 */
	public function getLatitude() {
		return $this->fLatitude;
	}

	/**
	 * Return the location string
	 * 
	 * @return string
	 */
	public function getLocation() {
		return $this->sLocation;
	}

	/**
	 * Recombines the coordinates of the map point
	 * 
	 * @return string
	 */
	protected function combineCoordinates() {
		return $this->fLatitude . ',' . $this->fLongitude;
	}

	/**
	 * Build the Map Path Point Part of the URL
	 *
	 * @return string
	 */
	public function build() {
		if (strlen($this->fLongitude) > 0 && strlen($this->fLatitude) > 0) {
			return $this->combineCoordinates();
		} elseif (strlen($this->sLocation) > 0) {
			return urlencode($this->sLocation);
		}

		return '';
	}

}