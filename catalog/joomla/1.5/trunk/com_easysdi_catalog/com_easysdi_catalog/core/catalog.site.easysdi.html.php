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

class HTML_catalog{

	
function listCatalogContent($pageNav,$cswResults,$option, $total,$searchCriteria){
	global  $mainframe;
	$db =& JFactory::getDBO();
		$tabs =& JPANE::getInstance('Tabs');
		?>	
		<div class="contentin">	
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_TITLE"); ?></h2>
	
		<h3>Type de métadonnées:</h3>
		<table>
		  <tr>
		    <td><a href="">Métadonnées publiques (count)</a></td>
		  </tr>
		  <tr>
		    <td><a href="">Métadonnées privées (count)</a></td>
		  </tr>
		  <tr>
		    <td>&nbsp;</td>
		  </tr>
		</table>
		
		<h3> <?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>
		
		<?php
		echo $tabs->startPane("catalogPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_SIMPLE_CRITERIA"),"catalogPane");
		?>
		<br>
		<table width="100%"><tr><td>
		<form name="catalog_search_form" id="catalog_search_form" method="POST" > 
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></b>&nbsp;
					<input type="text" name="filterfreetextcriteria" value="<?php echo $searchCriteria;?>" class="inputbox"  />			
				</td>
			</tr>
			</table>	
				
			<input type="hidden" name="bboxMinX" id="bboxMinX" value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>">
			<input type="hidden" name="bboxMinY" id="bboxMinY" value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>">
			<input type="hidden" name="bboxMaxX" id="bboxMaxX" value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>">
			<input type="hidden" name="bboxMaxY" id="bboxMaxY" value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>">
		</form>
		</td>		
		</tr><tr><td><button type="submit" class="easysdi_search_button" onClick="document.getElementById('catalog_search_form').submit()"><?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button></td></tr></table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_ADVANCED_CRITERIA"),"catalogPane");
		?><br>
		<table width="100%">
		<tr><td>
		<?php
		
		HTML_catalog::generateMap();
		
		?></td></tr>
		<tr><td><button type="submit" class="easysdi_search_button" onClick="document.getElementById('catalog_search_form').submit()"><?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button></td></tr>
		</table>
		
		<?php
		
		echo $tabs->endPanel();
		echo $tabs->endPane();
		?>
		
		
		<?php if($cswResults){ ?>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<span class="easysdi_number_of_metadata_found"><?php echo JText::_("EASYSDI_CATALOG_NUMBER_OF_METADATA_FOUND");?> <?php echo $total ?> </span>
	<table>
	<thead>
	<tr>	
	<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_SHARP'); ?></th>
	<th><?php echo JText::_('EASYSDI_CATALOG_ORDERABLE'); ?></th>	
	<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_NAME'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		

		
		$xpath = new DomXPath($cswResults);		
		$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');		
		$nodes = $xpath->query('//gmd:MD_Metadata');
				 
		foreach($nodes  as $metadata){
			
			$i++;
			
			 $md = new geoMetadata($metadata);	
			?>		
			<tr >			
			<td><?php echo $i; ?></td>
			<?php
			$query = "select count(*) from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
			$db->setQuery( $query);

			$metadataId_count = $db->loadResult();
			if ($db->getErrorNum()) {								
					$metadataId_count = '0';					
			}			
			?>
			<td><div class="<?php if ($metadataId_count>0) {echo "easysdi_product_exists";} else {echo "easysdi_product_does_not_exist";} ?>"><?php echo $metadataId_count;?> </div></td>								
			<td><a class="modal" title="<?php echo JText::_("EASYSDI_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_shop&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $md->getDataIdentificationTitle();?></a></td>			
			</tr>			
				<?php		
		}		
	?>
	</tbody>
	</table>
	<?php } ?>
		</div>
	<?php
		
}
function generateMap(){	
?>
<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js-compressed.js"></script>
<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/defs/EPSG21781.js"></script>
<table width="100%">
 <tr>
  <td width="50%" valign="top">
   <table>
    <tr>
		<td>&nbsp;</td>
	</tr>
   	<tr>
	<td><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></td>
	  <td><input type="text" name="filterfreetextcriteria" value="<?php echo $searchCriteria;?>" class="inputbox"  /></td>
	</tr>
	<tr>
	<td>Fournisseur:</td>
	  <td>
	  	<select>
			<option>---</option>
		</select>
	  </td>
	</tr>
	<tr>
	<td>Thématique:</td>
	  <td><input type="text" name="" value="" class="inputbox" /></td>
	</tr>
	<tr>
	<td>Visualisable?</td>
	  <td><input type="checkbox" name="" value="" class="inputbox" /></td>
	</tr>
	<tr>
	<td>Commandable?</td>
	  <td><input type="checkbox" name="" value="" class="inputbox" /></td>
	</tr>
   </table>
  </td>
  <td width="50%">
   <table>
    <tr>
     <td><div id="map" class="tinymap"></div></td>
    </tr>
    <tr>
      <td><div id="panelDiv" class="olControlEditingToolbar" ></div></td>
    </tr>
   </table>
  </td>
 </tr>
</table>
<br>


<div id="docs">
</div>
<br>
<script>
      
var vectors = null;            
function initMap(){
 OpenLayers.ProxyHost="components/com_easysdi_shop/proxy.php?url=";




<?php
global  $mainframe;
$db =& JFactory::getDBO(); 
	
	
$query = "select * from #__easysdi_basemap_definition where def = 1"; 
$db->setQuery( $query);
$rows = $db->loadObjectList();		  
if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
}
?>
				   map = new OpenLayers.Map('map', {
                projection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
                displayProjection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
                units: "<?php echo $rows[0]->unit; ?>",
                minResolution: <?php echo $rows[0]->minResolution; ?>,
               maxResolution: <?php echo $rows[0]->maxResolution; ?>,    
                maxExtent: new OpenLayers.Bounds(<?php echo $rows[0]->maxExtent; ?>)
            });
				  
				 baseLayerVector = new OpenLayers.Layer.Vector(
                "BackGround",
                {isBaseLayer: true,transparent: "true"}
            ); 
				  map.addLayer(baseLayerVector);


<?php

$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rows[0]->id; 
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
                    {layers: '<?php echo $row->layers; ?>', format : "image/png",transparent: "true"},                                          
                     {singleTile: <?php echo $row->singletile; ?>},                                                    
                     {     
                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
                      	minResolution: <?php echo $row->minResolution; ?>,
                        maxResolution: <?php echo $row->maxResolution; ?>,                 
                     projection:"<?php echo $row->projection; ?>",
                      units: "<?php echo $row->unit; ?>",
                      transparent: "true"
                     }
                    );
                 map.addLayer(layer<?php echo $row->i; ?>);
<?php 
$i++;
} ?>                    

			 

                map.zoomToExtent(new OpenLayers.Bounds(<?php echo $rows[0]->maxExtent; ?>));
               map.addControl(new OpenLayers.Control.LayerSwitcher());
                map.addControl(new OpenLayers.Control.Attribution());                                
            vectors = new OpenLayers.Layer.Vector(
                "Vector Layer",
                {isBaseLayer: false,transparent: "true"                                
                }
            );
            
           
                       
                        
            map.addLayer(vectors);
            var containerPanel = document.getElementById("panelDiv");
            var panel = new OpenLayers.Control.Panel({div: containerPanel});
            
		  var panelEdition = new OpenLayers.Control.Panel({div: containerPanel});
         
			
		 	rectControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});
			rectControl.featureAdded = function(event) { removeSelection();setLonLat(event);};												
			rectControl.handler.setOptions({irregular: true});                                  
        
                
        panelEdition.addControls([rectControl] );
            
              map.addControl(panelEdition);      
               showSelection();   	
}

function showSelection(){

	if (document.getElementById("bboxMinX").value == "-180" && 	
  		document.getElementById("bboxMinY").value == "-90" &&
  		document.getElementById("bboxMaxX").value == "180" &&  	
  		document.getElementById("bboxMaxY").value == "90" ){
  		//show nothing
  		} else{
  		
  		bounds = new OpenLayers.Bounds(document.getElementById("bboxMinX").value,document.getElementById("bboxMinY").value,document.getElementById("bboxMaxX").value,document.getElementById("bboxMaxY").value).toGeometry()
  		bounds.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"));
  		 vectors.addFeatures([new OpenLayers. Feature. Vector(bounds )]);
  		
  		}

}

function removeSelection(){

if (vectors.features.length > 1){
vectors.removeFeatures(vectors.features[0]);
}

}

function setLonLat(feature){



	var bounds = feature.geometry.getBounds();
	

 var transformedBounds =  	bounds.transform(new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),new OpenLayers.Projection("EPSG:4326"));
  	  	   	  	 
  document.getElementById("bboxMinX").value =transformedBounds.left; 	
  document.getElementById("bboxMinY").value =transformedBounds.bottom;
  document.getElementById("bboxMaxX").value =transformedBounds.right; 	
  document.getElementById("bboxMaxY").value =transformedBounds.top;
  
  
}

initMap();

</script>

	
	
<?php
}
}
?>