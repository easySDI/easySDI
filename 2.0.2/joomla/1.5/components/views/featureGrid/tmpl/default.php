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

JHTML::script('ext-base.js', 'components/com_easysdi_map/externals/ext/adapter/ext/');
JHTML::script('ext-all-debug.js', 'components/com_easysdi_map/externals/ext/');
// And OpenLayers
JHTML::script('OpenLayers.js', 'components/com_easysdi_map/externals/openlayers/');
JHTML::script('proj4js-compressed.js', 'administrator/components/com_easysdi_core/common/lib/js/proj4js/');
// GeoExt
JHTML::script('GeoExt.js', 'components/com_easysdi_map/externals/geoext/lib/');
// Now the component specific JavaScript
JHTML::script('core.js', 'components/com_easysdi_map/classes/');
JHTML::script('i18n.js', 'components/com_easysdi_map/classes/');
JHTML::script('pagination.js', 'components/com_easysdi_map/classes/');
JHTML::script('report_base.js', 'components/com_easysdi_map/elements/');
require(JPATH_COMPONENT.DS.'php'.DS.'lang.php');
require(JPATH_COMPONENT.DS.'php'.DS.'params.php');
JHTML::script('featureGrid.js', 'components/com_easysdi_map/views/featureGrid/tmpl/');

// Execute the layout and pass the $POST filter variable down to the page.
if (array_key_exists('body', $_POST)) {
  $readyJS = "
  var featureType='".$_GET['featureType']."';
  var geometryName='".$_GET['geometryName']."';
  Ext.onReady(function() {
    rpt = new EasySDI_Map.FeatureGrid({filter: '".$_POST['body']."'});
  });";
  $document->addScriptDeclaration($readyJS);
} else {

  echo '<span class="error">Cannot access this page externally.</span>';
}

?>