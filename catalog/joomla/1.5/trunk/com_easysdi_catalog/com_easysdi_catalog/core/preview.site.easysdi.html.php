<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

defined('_JEXEC') or die('Restricted access');

class HTML_preview{
	


function previewProduct($id){
	
		
	?>
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
	
	<script
	type="text/javascript"
	src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/proj4js-compressed.js"></script>
	
	
	
	<?php	
	global  $mainframe;
	$db =& JFactory::getDBO(); 
	

	$query = "SELECT * FROM #__easysdi_product WHERE metadata_id = '$id'"; 
	$db->setQuery( $query);
	$rowProduct = $db->loadObject();		  
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}					  
	
	
	$query = "select * from #__easysdi_basemap_definition where id = (SELECT previewBaseMapId FROM #__easysdi_product WHERE metadata_id = '$id')"; 
	$db->setQuery( $query);
	$rowsBaseMap = $db->loadObject();		  
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}					  
?>
<script>
function initMap(){
	map = new OpenLayers.Map('map', {
    		projection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            displayProjection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            units: "<?php echo $rowsBaseMap->unit; ?>",

//Bug when prohection == 4326 the resolutions are not managed properly
<?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
            minScale: <?php echo $rowsBaseMap->minResolution; ?>,
            maxScale: <?php echo $rowsBaseMap->maxResolution; ?>,                
			<?php } ?>
            maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxExtent; ?>)            
            });
				  
			baseLayerVector = new OpenLayers.Layer.Vector(
                "BackGround",
                {isBaseLayer: true,transparent: "true"}
            ); 
			map.addLayer(baseLayerVector);
<?php

$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rowsBaseMap->id." order by ordering"; 
$db->setQuery( $query);
$rows = $db->loadObjectList();
		  
if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
}
$i=0;
foreach ($rows as $row){				  
?>				
				  
		layer<?php echo $row->i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
                    "<?php echo $row->url; ?>",
                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
                     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
                   <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
                      	minResolution: <?php echo $row->minResolution; ?>,
                        maxResolution: <?php echo $row->maxResolution; ?>,
                        <?php } ?>                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                 map.addLayer(layer<?php echo $row->i; ?>);
<?php 
$i++;
} ?>                    
		
		layerProduit = new OpenLayers.Layer.WMS( "<?php echo $rowProduct->metadata_id; ?>",
                    "<?php echo $rowProduct->previewWmsUrl; ?>",
                    {isBaseLayer:true,layers: '<?php echo $rowProduct->previewWmsLayers; ?>', 
                    	format : "<?php echo $rowProduct->previewImageFormat; ?>",transparent: "true"},                                          
                     {singleTile: "false"},                                                    
                     { 
                       
                      maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxExtent; ?>),
                      <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>                                          
                      minScale: <?php echo $rowProduct->previewMinResolution; ?>,
                      maxScale: <?php echo $rowProduct->previewMaxResolution; ?>,
                      <?php } ?>                 
                     projection:"<?php echo $rowProduct->previewProjection; ?>",
                      units: "<?php echo $rowProduct->previewUnit; ?>",
                      transparent: "true"
                     }
                    );
                 map.addLayer(layerProduit);
		
			
      map.zoomToExtent(new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxExtent; ?>));
      //map.addControl(new OpenLayers.Control.LayerSwitcher());
      map.addControl(new OpenLayers.Control.Attribution());         
      
      
                                                            
}
var oldLoad = window.onload;
window.onload=function(){
initMap();
if (oldLoad) oldLoad();
}                       
</script>   
	
	<div id="map" class="smallmap"></div>
	
	<?php
}
}
?>