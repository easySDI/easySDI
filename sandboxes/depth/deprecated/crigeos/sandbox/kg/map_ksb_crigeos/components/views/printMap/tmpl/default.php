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
<?php 
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.jsLoaderUtil.php');
$jsLoader =JSLOADER_UTIL::getInstance();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Easysdi print MAP</title>
<!-- <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" /> -->
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<link rel="stylesheet" href="<?php  echo $jsLoader->getPath("map","ext")?>/resources/css/ext-all.css" type="text/css" />
<script type="text/javascript" src="<?php  echo  $jsLoader->getPath("map","ext","/adapter/ext/")?>/ext-base.js"></script>
<script type="text/javascript" src="<?php  echo  $jsLoader->getPath("map","ext")?>/ext-all.js"></script>
<script type="text/javascript" src="<?php  echo $jsLoader->getPath("map","jquery")?>/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?php  echo $jsLoader->getPath("map","jqueryui")?>/jquery-ui-1.7.2.custom.min.js"></script>
<link rel="stylesheet" href="<?php  echo $jsLoader->getPath("map","jqueryui")?>/css/jquery-ui-1.7.2.custom.css" type="text/css" />
<script type="text/javascript" src="<?php  echo $jsLoader->getPath("map","json")?>/json.js"></script>
<script type="text/javascript" src="<?php echo $jsLoader->getPath("map","openlayers", "/lib/OpenLayers/")?>/SingleFile.js"></script>
<script type="text/javascript" src="<?php echo $jsLoader->getPath("map","openlayers")?>/OpenLayers.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/core.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/classes/i18n.js"></script>
<script type="text/javascript" src="components/com_easysdi_map/views/printMap/tmpl/standard_layout.js"></script>

<?php
$h = JRequest::getVar ('mapPanelHeight');
$w = JRequest::getVar ('mapPanelWidth');
?>
<script type="text/javascript">
<?php
include(JPATH_COMPONENT.DS.'php'.DS.'lang.php');
echo $s;
include(JPATH_COMPONENT.DS.'php'.DS.'params.php');
echo $s;
?>
var completeMap = '<?php echo JRequest::getVar ('mapPanel') ?>';
thisMap = null;
Ext.onReady(function() {
	printMap({});
	});
</script>
</head>
<body>
<div id="map" style='border: thin; width: <?php echo $w?>px; height: <?php echo $h?>px;'>
</div>
</body>
</html>
