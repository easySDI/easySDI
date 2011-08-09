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
		global  $mainframe;
		$db =& JFactory::getDBO();
		$query = "SELECT p.* FROM #__sdi_product p
					   INNER JOIN #__sdi_objectversion ov
					   WHERE ov.metadata_id = '$id'"; 
		$db->setQuery( $query);
		$rowProduct = $db->loadObject();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		//Check if user allowed to preview this product
		$product = new product($db);
		$product->load($rowProduct->id);
		$user = JFactory::getUser();
		$account = new accountByUserId( $db );
		$account->load( $user->id );

		if(!$product->isUserAllowedToView($account->id)){
			HTML_preview::displayErrorMessage();
		}
		else{
			HTML_preview::displayMap($rowProduct);
		}

	}
	
	function displayMap ($rowProduct){
		global  $mainframe;
		$db =& JFactory::getDBO();		
		?>
		<script
			type="text/javascript"
			src="./administrator/components/com_easysdi_shop/lib/openlayers2.8/lib/OpenLayers.js"></script>
		<script
			type="text/javascript"
			src="./administrator/components/com_easysdi_shop/lib/proj4js/lib/proj4js.js"></script>
		<h2 class="contentheading">
		<?php echo JText::_("SHOP_PRODUCT_PREVIEW"); ?>
		</h2>
		<h3 >
		<?php echo JText::_("SHOP_PRODUCT_PREVIEW_TITLE"); ?>
			:
			<?php echo $rowProduct->name; ?>
		</h3>
		<?php
		$query = "select * from #__sdi_basemap where id = $rowProduct->viewbasemap_id";
		$db->setQuery( $query);
		$rowsBaseMap = $db->loadObject();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}
		?>
		<script type="text/javascript">
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
		            minScale: <?php echo $rowsBaseMap->minresolution; ?>,
		            maxScale: <?php echo $rowsBaseMap->maxresolution; ?>,                
					<?php } ?>
		            maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxextent; ?>)            
		            });
						  
					baseLayerVector = new OpenLayers.Layer.Vector(
		                "BackGround",
		                {isBaseLayer: true,transparent: "true"}
		            ); 
					map.addLayer(baseLayerVector);
		<?php
		$query = "select * from #__sdi_basemapcontent where basemap_id = ".$rowsBaseMap->id." order by ordering"; 
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
			layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->urltype; ?>( "<?php echo $row->name; ?>",
                    "<?php echo $row->url; ?>",
                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->imgformat; ?>",transparent: "true"},                                          
                     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxextent; ?>),
                   <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
                      	minScale: <?php echo $row->minresolution; ?>,
                        maxScale: <?php echo $row->maxresolution; ?>,
                        <?php } ?>                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                     <?php
                    if (strtoupper($row->urltype) =="WMS")
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
		layerProduit = new OpenLayers.Layer.WMS( "<?php echo $id; ?>",
                    "<?php echo $rowProduct->viewurlwms; ?>",
                    {isBaseLayer:true,layers: '<?php echo $rowProduct->viewlayers; ?>', 
                    	format : "<?php echo $rowProduct->viewimgformat; ?>",transparent: "true"},                                          
                     {singleTile: "false"},                                                    
                     { 
                       
                      maxExtent: new OpenLayers.Bounds(<?php echo $rowsBaseMap->maxextent; ?>),
                      <?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>                                          
                      minScale: <?php echo $rowProduct->viewminresolution; ?>,
                      maxScale: <?php echo $rowProduct->viewmaxresolution; ?>,
                      <?php } ?>                 
                     projection:"<?php echo $rowProduct->viewprojection; ?>",
                      units: "<?php echo $rowProduct->viewunit; ?>",
                      transparent: "true"
                     }
                    );
                 
                 layerProduit.alpha = setAlpha('image/png');
                 map.addLayer(layerProduit);
		
			
	      map.zoomToExtent(new OpenLayers.Bounds(<?php echo $rowProduct->viewextent; ?>));
	      map.addControl(new OpenLayers.Control.Attribution());         
   		}
		</script>
		<div id="map" class="smallmap"></div>
		<script type="text/javascript">
			window.onload=function()
			{	
				initMap();
			}
		</script>
		<?php
	}
	
	function displayErrorMessage (){
		JError::raiseWarning( 100, JText::_("SHOP_MSG_NOT_ALLOWED_TO_PREVIEW_THIS_PRODUCT") );
	}
}
?>