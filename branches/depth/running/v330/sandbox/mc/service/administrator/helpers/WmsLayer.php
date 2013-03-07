<?php
require_once('Layer.php');

class WmsLayer extends Layer{
	public $bbox;
	public $gmlFilter;
	public $scaleMin;
	public $scaleMax;
	public $SRS;
	public $minX;
	public $minY;
	public $maxX;
	public $maxY;
	
}