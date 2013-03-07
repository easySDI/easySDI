<?php
abstract class Layer {
	public $name;
	public $description;
	
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
			}
		}
	}
	
}