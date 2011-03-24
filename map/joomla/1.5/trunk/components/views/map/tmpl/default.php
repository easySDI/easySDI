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

// Include the ext library. Replace with ext-all.js for production
JHTML::stylesheet('ext-all.css', 'components/com_easysdi_map/externals/ext/resources/css/');
JHTML::script('ext-base.js', 'components/com_easysdi_map/externals/ext/adapter/ext/');
JHTML::script('ext-all.js', 'components/com_easysdi_map/externals/ext/');
// And JQuery
JHTML::script('jquery-1.3.2.min.js', 'components/com_easysdi_map/externals/jquery/');
JHTML::script('jquery-ui-1.7.2.custom.min.js', 'components/com_easysdi_map/externals/jquery/');
JHTML::script('jquery.download.js', 'components/com_easysdi_map/views/map/js/');
JHTML::stylesheet('jquery-ui-1.7.2.custom.css', 'components/com_easysdi_map/externals/jquery/css/');
JHTML::stylesheet('getFeatureInfo.css', 'components/com_easysdi_map/resource/xslt/');
// And OpenLayers
JHTML::script('OpenLayers.js', 'components/com_easysdi_map/externals/openlayers/');
//JHTML::script('OpenLayers.js', 'Lib/');
JHTML::script('SortableWFS.js', 'components/com_easysdi_map/classes/');
JHTML::script('getfeatureinfo.js', 'components/com_easysdi_map/classes/');
JHTML::script('proj4js-compressed.js', 'components/com_easysdi_map/externals/proj4js/');
// And the GeoExt library
JHTML::script('GeoExt.js', 'components/com_easysdi_map/externals/geoext/lib/');
// Now the component specific JavaScript
JHTML::script('core.js', 'components/com_easysdi_map/classes/');
JHTML::script('i18n.js', 'components/com_easysdi_map/classes/');
// Trigger manager must be loaded before the classes which use it
JHTML::script('triggerManager.js', 'components/com_easysdi_map/classes/');
JHTML::script('featureDetailsHelper.js', 'components/com_easysdi_map/classes/');
JHTML::script('searchManager.js', 'components/com_easysdi_map/classes/');
JHTML::script('sortableWFS.js', 'components/com_easysdi_map/classes/');
JHTML::script('dialog.js', 'components/com_easysdi_map/classes/');
JHTML::script('TristateCheckboxNode.js', 'components/com_easysdi_map/classes/');
JHTML::script('TristateCheckboxNodeUI.js', 'components/com_easysdi_map/classes/');
JHTML::script('LayerNode.js', 'components/com_easysdi_map/classes/');
JHTML::script('layerTree.js', 'components/com_easysdi_map/classes/');
JHTML::script('multiGeomGml.js', 'components/com_easysdi_map/classes/');
JHTML::script('precisionTree.js', 'components/com_easysdi_map/classes/');
JHTML::script('featureSelectionModel.js', 'components/com_easysdi_map/classes/');
JHTML::script('distinctFeatureReader.js', 'components/com_easysdi_map/classes/');
JHTML::script('pagination.js', 'components/com_easysdi_map/classes/');
JHTML::script('radionode.js', 'components/com_easysdi_map/classes/');
JHTML::script('clickFeature.js', 'components/com_easysdi_map/classes/');
JHTML::script('triggerTextBox.js', 'components/com_easysdi_map/classes/');
JHTML::script('viewPort.js', 'components/com_easysdi_map/elements/');
JHTML::script('layout_base.js', 'components/com_easysdi_map/elements/');
JHTML::script('mapPanel.js', 'components/com_easysdi_map/elements/');
JHTML::script('searchPanel.js', 'components/com_easysdi_map/elements/');
JHTML::script('layerPanel.js', 'components/com_easysdi_map/elements/');
JHTML::script('legendPanel.js', 'components/com_easysdi_map/elements/');
JHTML::script('filterPanel.js', 'components/com_easysdi_map/elements/');
//JHTML::script('RwgFilterPanel.js', 'components/com_easysdi_map/elements/');
JHTML::script('gridPanel.js', 'components/com_easysdi_map/elements/');
JHTML::script('featurePopup.js', 'components/com_easysdi_map/elements/');
JHTML::script('Ext.ux.form.SearchField.js', 'components/com_easysdi_map/elements/');
require(JPATH_COMPONENT.DS.'php'.DS.'lang.php');
require(JPATH_COMPONENT.DS.'php'.DS.'params.php');
//JSON library
JHTML::script('json.js', 'components/com_easysdi_map/externals/json/');

JHTML::script('StyledLayerDescriptor.js', 'components/com_easysdi_map/classes/');

$doc =& JFactory::getDocument();
$doc->addStyleDeclaration('body{overflow:hidden;}');
/*
 * Include extensions resources :
 * The GUI elements that can be extended are :
 * - filterPanel
 * - searchPanel
 * If extensions, for this two GUI elements, are installed and registered in the database,
 * then this section loads the javascript files and includes the php files needed by them
 */
//Javascript files
$db =& JFactory::getDBO();
$query = "SELECT resource_folder, resource_file FROM #__sdi_map_extensionresource WHERE resource_type='js' AND id_ext IN
			(SELECT id FROM #__sdi_mapextension WHERE  extended_object='FilterPanel' OR extended_object='SearchPanel') ";
$db->setQuery($query);
$resources = $db->loadObjectList();
foreach ($resources as $resource)
{
	JHTML::script($resource->resource_file, $resource->resource_folder);
}
//Php files
$query = "SELECT resource_folder, resource_file FROM #__sdi_map_extensionresource WHERE resource_type='php' AND id_ext IN
			(SELECT id FROM #__sdi_mapextension WHERE  extended_object='FilterPanel' OR extended_object='SearchPanel') ";
$db->setQuery($query);
$resources = $db->loadObjectList();
foreach ($resources as $resource)
{
	require(JPATH_BASE.DS.$resource->resource_folder.$resource->resource_file);
}

// Now the layout specific JavaScript
JHTML::script($this->layoutJs, 'components/com_easysdi_map/views/map/tmpl/');
// Execute the layout

$readyJS = "
var easySDImap = null;
function loadMap(){
easySDImap = new EasySDI_Map.RwgLayout(
{
		renderTo: 'map',
		bodyCssClass: 'mapAutoHeight',
		monitorResize: true,
		listeners:
     {
      'afterlayout': function(p) 
      {
        p.layerTree.loadLayers();
        p.legendPanel.refresh();
      },
      single:true
    }
  });
  	new Ext.ToolTip( {
			target : 'localisationInputWidth',
			html : EasySDI_Map.lang.getLocal('MP_ZOOM_TTIP'),
			dismissDelay: 5000
		});
  }
Ext.QuickTips.init();  
Ext.onReady(function() {
//$(document).ready(function() {
	if ($.browser.msie)	setTimeout(loadMap, 2000);
	else loadMap();
  });
";

$document->addScriptDeclaration($readyJS);

?>