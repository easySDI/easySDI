<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once('Layer.php');

class WfsFeatureType extends Layer{
	public $remoteFilterGML;
	public $localFilterGML;
	public $enabled;
	
	public function loadData ($data) {
		foreach ($data as $key => $value) {
			if (property_exists('WfsFeatureType', $key)) {
				$this->{$key} = $value;
				if ('enabled' != $key) {
					$this->hasConfig = true;
				}
			}
		}
	}
}