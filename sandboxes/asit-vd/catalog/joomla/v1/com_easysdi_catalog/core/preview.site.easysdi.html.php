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

defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');

class HTML_preview{
	


function previewProduct($id){
	?>
		
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.8/lib/OpenLayers.js"></script>
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js"></script>
	
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
	
	?>
	<div id="productPreview">
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_PRODUCT_PREVIEW"); ?></h2>
	<h3 class="productPreviewTitle"><?php echo JText::_("EASYSDI_CATALOG_PRODUCT_TITLE"); ?> : <?php echo $rowProduct->data_title; ?></h3>
	<div class="contentin">
	<?php
	
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
function setAlpha(imageformat)
{
	var filter = false;
	if (imageformat.toLowerCase().indexOf("png") > -1) {
		filter = OpenLayers.Util.alphaHack(); 
	}
	return filter;
}
var map;
var baseLayerVector;
function initMap(){
	map = new OpenLayers.Map('map', {
    		projection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            displayProjection: new OpenLayers.Projection("<?php echo $rowsBaseMap->projection; ?>"),
            units: "<?php echo $rowsBaseMap->unit; ?>",
<?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
            minScale: <?php echo $rowsBaseMap->minResolution; ?>,
            maxScale: <?php echo $rowsBaseMap->maxResolution; ?>,                
			<?php } ?>
            maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxExtent; ?>)
	     <?php
			if($rowsBaseMap->restrictedExtent == '1') echo  ",restrictedExtent: new OpenLayers.Bounds(".$rowsBaseMap->maxExtent.")\n"
	    ?>
		<?php
			if($rowsBaseMap->restrictedScales != '') echo  ",scales: [".$rowsBaseMap->restrictedScales."]\n"
	    ?>
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
				  
		layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
                    
		<?php 
		if ($row->user != null && strlen($row->user)>0){
			//if a user and password is requested then use the joomla proxy.
			$proxyhost = config_easysdi::getValue("PROXYHOST");
			$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
			echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
		}else{	
			//if no user and password then don't use any proxy.					
			echo "\"$row->url\",";	
		}					
		?>
		
		     {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
                     <?php if (strlen($row->attribution)>0){?>
		     {attribution: '<?php echo $row->attribution; ?>'},
		     <?php }?>
		     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
                   <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
                      	minScale: <?php echo $row->minResolution; ?>,
                        maxScale: <?php echo $row->maxResolution; ?>,
                        <?php } ?>                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                     <?php
                    if (strtoupper($row->url_type) =="WMS")
                    {
                    	?>
                    	layer<?php echo $i; ?>.alpha = setAlpha('image/png');
                    	<?php
                    } 
                    ?>
                 map.addLayer(layer<?php echo $i; ?>);
<?php 
$i++;
} ?>                    
		
		layerProduit = new OpenLayers.Layer.WMS( "<?php echo $rowProduct->metadata_id; ?>",
                    
		    <?php 
		    if ($rowProduct->previewUser != null && strlen($rowProduct->previewUser)>0){
		    	//if a user and password is requested then use the joomla proxy.
		    	$proxyhost = config_easysdi::getValue("PROXYHOST");
			$proxyhost = $proxyhost."&type=wms&previewId=$rowProduct->id&url=";
		    	echo "\"$proxyhost".urlencode  (trim($rowProduct->previewWmsUrl))."\",";												
		    }else{	
		    	//if no user and password then don't use any proxy.					
		    	echo "\"$rowProduct->previewWmsUrl\",";	
		    }					
		    ?>		    
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
                 
                 layerProduit.alpha = setAlpha('image/png');
                 map.addLayer(layerProduit);
		
			
      map.zoomToExtent(new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxExtent; ?>));
      //map.addControl(new OpenLayers.Control.LayerSwitcher());
      map.addControl(new OpenLayers.Control.Attribution());         
                                                            
	}


                      
	</script> 
		<table class="productPreview">
		   <tr>
			<td align="center"><div id="map" class="smallmap"></div></td>
		   </tr>
		   <tr>
			<td align="center">&nbsp</div></td>
		   </tr>
		</table>
		</div>
		</div>
	<script>
		window.onload=function()
		{	
			initMap();
		}
	</script>
		<?php
	}
}
?>