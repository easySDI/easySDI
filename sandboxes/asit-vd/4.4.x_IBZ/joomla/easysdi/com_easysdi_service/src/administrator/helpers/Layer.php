<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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