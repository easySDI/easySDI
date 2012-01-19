<?php

/**
* EasySDI, a solution to implement easily any spatial data infrastructure
* Copyright (C) EasySDI Community
* For more information : www.easysdi.org
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
*/

$document = &JFactory::getDocument();

// Clear the mootools script
JHTML::_('behavior.mootools');
$headerstuff = $document->getHeadData();
$headerstuff['scripts'] = array();
$document->setHeadData($headerstuff);
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.jsLoaderUtil.php');
$jsLoader =JSLOADER_UTIL::getInstance();

JHTML::script('ext-base.js', $jsLoader->getPath("map","ext","/adapter/ext/"));//'components/com_rwgis_map/externals/ext/adapter/ext/');
JHTML::script('ext-all.js', $jsLoader->getPath("map","ext"));//'components/com_rwgis_map/externals/ext/');
// And OpenLayers
JHTML::script('SingleFile.js', $jsLoader->getPath("map","openlayers", "/lib/OpenLayers/"));
JHTML::script('OpenLayers.js', $jsLoader->getPath("map","openlayers"));//'components/com_rwgis_map/externals/openlayers/');
JHTML::script('proj4js-compressed.js', $jsLoader->getPath("map", "proj4js"));//'components/com_rwgis_map/externals/proj4js/');
// GeoExt
JHTML::script('SingleFile.js',  $jsLoader->getPath("map","geoext", "/lib/GeoExt/"));
JHTML::script('GeoExt.js',  $jsLoader->getPath("map","geoext", "/script/"));// 'components/com_rwgis_map/externals/geoext/lib/');
// Now the component specific JavaScript
JHTML::script('core.js', 'components/com_easysdi_map/classes/');
JHTML::script('i18n.js', 'components/com_easysdi_map/classes/');
JHTML::script('featureDetailsHelper.js', 'components/com_easysdi_map/classes/');
JHTML::script('SortableWFS.js', 'components/com_easysdi_map/classes/');
JHTML::script('report_base.js', 'components/com_easysdi_map/elements/');
require(JPATH_COMPONENT.DS.'php'.DS.'lang.php');
require(JPATH_COMPONENT.DS.'php'.DS.'params.php');
JHTML::script('featureDetails.js', 'components/com_easysdi_map/views/featureDetails/tmpl/');

// Execute the layout
$readyJS = "Ext.onReady(function() {
  rpt = new EasySDI_Map.FeatureDetails({});

 });";
$document->addScriptDeclaration($readyJS);

?>