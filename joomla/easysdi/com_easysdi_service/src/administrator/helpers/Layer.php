<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */

abstract class Layer {
	public $name;
	public $description;
	
	protected $hasConfig = false;
	
	public function __construct($name, $description) {
		$this->name = $name;
		$this->description = $description;
	}
	
	public static function compareNames ($a, $b) {
		$al = strtolower($a->name);
		$bl = strtolower($b->name);
		if ($al == $bl) {
			return 0;
		}
		return ($al > $bl) ? +1 : -1;
	}
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('Layer', $key)) {
				$this->{$key} = $value;
				$this->hasConfig = true;
			}
		}
	}
	
	public function hasConfig () {
		return $this->hasConfig;
	}
	
	public function setHasConfig($value) {
		if (is_bool($value)) {
			$this->hasConfig = $value;
		}
	}
	
}