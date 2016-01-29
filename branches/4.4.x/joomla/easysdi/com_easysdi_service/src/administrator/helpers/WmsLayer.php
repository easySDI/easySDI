<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */

require_once('Layer.php');

class WmsLayer extends Layer{
	public $enabled;
	public $maxX;
	public $maxY;
	public $minX;
	public $minY;
	public $geographicFilter;
	public $maximumScale;
	public $minimumScale;
	public $srsSource;
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('WmsLayer', $key)) {
				$this->{$key} = $value;
				if ('enabled' != $key) {
					$this->hasConfig = true;
				}
			}
		}
	}
}