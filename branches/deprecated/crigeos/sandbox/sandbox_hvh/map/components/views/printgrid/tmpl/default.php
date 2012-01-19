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

JHTML::script('SingleFile.js', $jsLoader->getPath("map","openlayers", "/lib/OpenLayers/"));
JHTML::script('OpenLayers.js', $jsLoader->getPath("map","openlayers"));// 'components/com_rwgis_map/externals/openlayers/');
JHTML::script('SortableWFS.js', 'components/com_easysdi_map/classes/');
// Now the component specific JavaScript
JHTML::script('ext-base.js',  $jsLoader->getPath("map","ext","/adapter/ext/"));// 'components/com_rwgis_map/externals/ext/adapter/ext/');
JHTML::script('ext-all.js',  $jsLoader->getPath("map","ext"));//'components/com_rwgis_map/externals/ext/');

JHTML::script('i18n.js', 'components/com_easysdi_map/classes/');
JHTML::script('printGrid.js', 'components/com_easysdi_map/views/printgrid/tmpl/');
require(JPATH_COMPONENT.DS.'php'.DS.'lang.php');
require(JPATH_COMPONENT.DS.'php'.DS.'params.php');

// Execute the layout and pass the $POST filter variable down to the page.
if (array_key_exists('body', $_POST)) {
	$sortField = "";
	if (array_key_exists('sortField', $_GET))
	  $sortField = $_GET['sortField'];
	$sortDir = "";
	if (array_key_exists('sortDir', $_GET))
	  $sortDir = $_GET['sortDir'];
  $s .="
   
  var sortField=('$sortField');
  var sortDir=('$sortDir');
  var featureType=('".$_GET['featureType']."');
  var page=new PrintGrid('".$_POST['body']."');";
  $document->addScriptDeclaration($s);
} else {
  echo '<span class="error">Cannot access this page externally.</span>';
}

?>
<div id="banner" style="float: left; width: 100%;">
	<div id="banner-bg" style="float: left; width: 100%;" >
		<img src="templates/easysdi_map/images/gouvernement.gif"  style="float: left;" />
		<img src="templates/easysdi_map/images/topvisu.gif"  style="float: left;"/>
		<img src="templates/easysdi_map/images/gouvernementLogo.png" alt="Le Gouvernement du Grande-Duch&eacute; de Luxembourg" style="float: right" />
	</div>
</div>
<div id="printreport" style="float: left; width: 100%">
	<img src="components/com_easysdi_map/externals/ext/resources/images/default/shared/large-loading.gif" width="32" height="32" style="margin: 4em;" />	
</div>