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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Easysdi print MAP</title>
<!-- <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> -->
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="components/com_easysdi_map/externals/ext/resources/css/ext-all.css" type="text/css" />
<script type="text/javascript" src="components/com_easysdi_map/externals/ext/adapter/ext/ext-base.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/externals/ext/ext-all.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/externals/jquery/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/externals/jquery/jquery-ui-1.7.2.custom.min.js"></script>
<link rel="stylesheet" href="components/com_easysdi_map/externals/jquery/css/jquery-ui-1.7.2.custom.css" type="text/css" />
<script type="text/javascript" src="components/com_easysdi_map/externals/json/json.js"></script>
<link rel="stylesheet" href="components/com_easysdi_map/resource/xslt/getFeatureInfo.css" type="text/css" />
<script type="text/javascript" src="components/com_easysdi_map/externals/openlayers/OpenLayers.js"></script>
<!--<script type="text/javascript" src="components/com_easysdi_map/externals/OpenLayers-2.8/lib/OpenLayers.js"></script>-->
<script type="text/javascript" src="components/com_easysdi_map/classes/SortableWFS.js"></script>
<script type="text/javascript" src="administrator/components/com_easysdi_core/common/lib/js/proj4js/proj4js-compressed.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/externals/geoext/lib/GeoExt.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/core.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/i18n.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/triggerManager.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/featureDetailsHelper.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/searchManager.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/sortableWFS.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/dialog.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/TristateCheckboxNode.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/TristateCheckboxNodeUI.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/LayerNode.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/layerTree.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/multiGeomGml.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/precisionTree.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/featureSelectionModel.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/distinctFeatureReader.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/pagination.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/radionode.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/clickFeature.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/triggerTextBox.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/viewPort.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/layout_base.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/mapPanel.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/searchPanel.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/layerPanel.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/legendPanel.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/filterPanel.js"></script>
<!-- <script type="text/javascript" src="components/com_easysdi_map/elements/RwgFilterPanel.js"></script> -->
<script type="text/javascript" src="components/com_easysdi_map/elements/gridPanel.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/featurePopup.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/elements/Ext.ux.form.SearchField.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/views/printMap/tmpl/standard_layout.js"></script>

<script type="text/javascript">
<?php
include(JPATH_COMPONENT.DS.'php'.DS.'lang.php');
echo $s;
include(JPATH_COMPONENT.DS.'php'.DS.'params.php');
echo $s;
?>
var completeMap = '<?php echo JRequest::getVar ('mapPanel') ?>';
var mapPanelHeight = <?php echo JRequest::getVar ('mapPanelHeight') ?>;
var mapPanelWidth = <?php echo JRequest::getVar ('mapPanelWidth') ?>;
var mapPanel = null;
Ext.onReady(function() {
//$(document).ready(function() {
	  mapPanel = new EasySDI_Map.printMapLayout({});
	  //print();
})
</script>
</head>
<body>
<div id="map"></div>
</body>
</html>
