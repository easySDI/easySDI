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



class geoMeatdata{
	var $metadata;
	
	function __construct( &$md )
	{
		$this->metadata  = $md;
		
		//var_dump($metadata);
	}
	
	function getFileIdentifier(){
		
		return $this->metadata->fileIdentifier->children("http://www.isotc211.org/2005/gco")->CharacterString; 
	}
	
	function getDataIdentificationTitle($lang="fr"){
		
		if (count($this->metadata->identificationInfo->MD_DataIdentification->citation->CI_Citation->title->LocalisedCharacterString) >0){
			return $this->metadata->identificationInfo->MD_DataIdentification->citation->CI_Citation->title->LocalisedCharacterString;
		}else{
			return $this->metadata->identificationInfo->MD_DataIdentification->citation->CI_Citation->title->PT_FreeText->textGroup->LocalisedCharacterString;
		}
			return "";	
	
	}
	
	function getOrganisationName($lang="fr") {
		if (count($this->metadata->identificationInfo->MD_DataIdentification->pointOfContact->CI_ResponsibleParty->organisationName->LocalisedCharacterString )>0){
		return $this->metadata->identificationInfo->MD_DataIdentification->pointOfContact->CI_ResponsibleParty->organisationName->LocalisedCharacterString;
		}else{
			return $this->metadata->identificationInfo->MD_DataIdentification->pointOfContact->CI_ResponsibleParty->organisationName->PT_FreeText->textGroup->LocalisedCharacterString;
		}
		return "";
	}
	
	
	
}
class HTML_shop {

function deleteProduct(){
	global  $mainframe;
	$id = JRequest::getVar('prodId');
	
	$productList = $mainframe->getUserState('productList');
	if (is_array($productList)){
		
		foreach ($productList as $key => $value){
			if ($value == $id){
				unset($productList[$key]);
			}
			
		}
		 $mainframe->setUserState('productList',$productList);
	}
		
	}
function showMetadata(){
	?>
	<?php
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$id = JRequest::getVar('id');
	
	$catalogUrlBase = config_easysdi::getValue("catalog_url");	
	
	$catalogUrlCapabilities = $catalogUrlBase."?request=GetCapabilities&service=CSW";
	$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$id;

	$cswResults = DOMDocument::load($catalogUrlGetRecordById);
			
	
	$processor = new xsltProcessor();
	$style = new DomDocument();
	
	$user =& JFactory::getUser();
	$language = $user->getParam('language', '');
	
	if (file_exists(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl")){
		$style->load(dirname(__FILE__).'/../xsl/iso19115_'.$language.".xsl");
	}else{
		$style->load(dirname(__FILE__).'/../xsl/iso19115.xsl');
	}
	$processor->importStylesheet($style);
	echo $processor->transformToXml($cswResults);
		 ?>
		
		 <?php	
		 
		 /***Add consultation informations*/
		 $db =& JFactory::getDBO(); 
	
	
		$query = "select max(weight)+1 from #__easysdi_product  where metadata_id='$id'";
		$db->setQuery( $query);
		$maxHit = $db->loadResult();
		$query = "update #__easysdi_product set weight = $maxHit where metadata_id='$id' "; 
		$db->setQuery( $query); 
		if (!$db->query()) {
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";			
		}
		 
}	

function orderPerimeter ($cid){	
	?>
<script type="text/javascript" src="http://www.openlayers.org/api/OpenLayers.js"></script>
<!-- script type="text/javascript" src="./templates/easysdi/lib/js/openlayers/OpenLayers.js"></script -->
<script type="text/javascript" src="./templates/easysdi/lib/js/proj4js/proj4js-compressed.js"></script>


<script>
var map;
var wfs;
var vectors;
var nameField;
var idField;
var areaField;
var layerPerimeter;

function selectWFSPerimeter(perimId,perimName,perimUrl,featureTypeName,name,id,area,wmsUrl,layerName){
	
	document.getElementById('perimeter_id').value = perimId;
	 
	nameField = name;
	idField = id;
	areaField =area;
	
	
	if (layerPerimeter){
				map.removeLayer(layerPerimeter);
		}
	if (wfs) {
		map.removeLayer(wfs);
	}
	
	if (wmsUrl.length > 0){
	
		layerPerimeter = new OpenLayers.Layer.WMS(perimName,
                    wmsUrl,
                    {layers: layerName, format : "image/png",transparent: "true"},                                          
                     {singleTile: true},                                                    
                     {     
                      maxExtent: map.maxExtent,
                      projection: map.projection,
                      units: map.units,
                      transparent: "true"
                     }
                    );
                 map.addLayer(layerPerimeter);
	
	}else{
	
	
	wfs = new OpenLayers.Layer.WFS( perimName,
	                perimUrl,
	                {typename: featureTypeName}, {
	                    typename: featureTypeName,                                    
	                    extractAttributes: true
	                       
	                }  );
	
	wfs.events.register("loadstart", null, function() { $("status").innerHTML = "<?php echo JText::_("LOADING_THE_PERIMETER") ?>"; })
	wfs.events.register("loadend", null, function() { $("status").innerHTML = ""; intersect();})
	
	map.addLayer(wfs);
	 }
	
	
}

function initMap(){
 OpenLayers.ProxyHost="/proxy.php?url=";




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
                , controls: [] 
            });
				  
				  
				  
				  
				  
				 baseLayerVector = new OpenLayers.Layer.Vector(
                "Vector Layer",
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
            
            vectors.events.register("featureadded", vectors, function(e) { 
            
              alert(vectors.features[vectors.features.length-1].geometry.components.components.length);
            });
            
            
                        
            map.addLayer(vectors);
			zb = new OpenLayers.Control.ZoomBox();
            var panel = new OpenLayers.Control.Panel({defaultControl: zb});
            
            
			rectControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});
			rectControl.featureAdded = function() { intersect();};
            rectControl.handler.setOptions({irregular: true});
                       
            
            
            polyControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Polygon,{'displayClass':'olControlDrawFeaturePolygon'});
            polyControl.featureAdded = function() { intersect();};
			
			                                 
            pointControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Point,{'displayClass':'olControlDrawFeaturePoint'});
            pointControl.featureAdded = function() { intersect();};
            navHistory = new OpenLayers.Control.NavigationHistory();
            map.addControl (navHistory);                                 
            panel.addControls([            	
              new OpenLayers.Control.MouseDefaults(), 
              zb,         
              new OpenLayers. Control. ZoomOut(),                                                
 	          rectControl, 	          
 	          polyControl,
 	          pointControl,
 	          new OpenLayers.Control.ModifyFeature(vectors,{'displayClass':'olControlModifyFeature'}), 	          
 	          navHistory.previous, 	          
 	          navHistory.next
            ]);
            map.addControl(panel);
           
	
}


 function onPopupClose(evt) {
            selectControl.unselect(selectedFeature);
        }
        function onFeatureSelect(feature) {        
            selectedFeature = feature;
            
            document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(feature.geometry.getArea()); 
            
            
            document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
            	new Option(feature.geometry.getArea(),feature.geometry.getArea() );
            
            
            
            
            myDiv =  "<div style='font-size:.8em'>Feature: " + feature.id +"<br />Area: " + feature.geometry.getArea();
            myDiv +="AttributeeS";
            for (var j in feature.attributes) {
                myDiv += "<li>"+j+":"+feature.attributes[j]+"</li>";
            }
            myDiv += "</div>";
             
            popup = new OpenLayers.Popup.FramedCloud("chicken", 
                                     feature.geometry.getBounds().getCenterLonLat(),
                                     null,
                                    
                                    myDiv
                                     ,
                                     null, true, onPopupClose);
            feature.popup = popup;
            map.addPopup(popup);
        }
        function onFeatureUnselect(feature) {
            map.removePopup(feature.popup);
            feature.popup.destroy();
            feature.popup = null;
        }    

         
         
 

var displayedFeature = null;
        function feature_info_hover(feature) {
            if (displayedFeature != feature &&
               (!feature.layer.selectedFeatures.length ||
               (feature.layer.selectedFeatures[0] == feature))) {
            feature_info(feature);
            displayedFeature = feature;
           }
        }
        function feature_info(feature) {
            var html = "<ul>";
            for(var i in feature.attributes)
               html += "<li><b>" + i + "</b>: "+  feature.attributes[i] + "</li>";
            html += "</ul>";
            OpenLayers.Util.getElement('feature_info').innerHTML = html;
        }


 function intersect() {
 
 if (document.getElementById('perimeter_id').value == "-1"){
 
 
 }
 			return;
            var features = vectors.features;
            var wfsFeatures = wfs.features;
            
            var feat1, feat2, intersects12, intersects21;
            var parts = [];
            // reset attributes
            for(var i=0; i<features.length; ++i) {
                features[i].attributes.intersectsWith = [];
            }
            for(var i=0; i<wfsFeatures.length; ++i) {
                wfsFeatures[i].attributes.intersectsWith = [];
            }
          
            for(var i=0; i<features.length; ++i) {
                feat1 = features[i];
                for(var j=0; j<wfsFeatures.length; ++j) {
                    feat2 = wfsFeatures[j];
                    intersects12 = feat1.geometry.intersects(feat2.geometry);
                    if(intersects12) {
                       var name = feat2.attributes[nameField];
                       var id = document.getElementById('perimeter_id').value +"."+feat2.attributes[idField];
            		   var area = feat2.attributes[areaField];
            		   var featArea = 0;	
            		       
            		       
            		       
            			if (areaField.length > 0){
            					featArea = area; 
            				}else {
            					featArea = feat2.geometry.getArea();
            				}
            				
            				//If the id is already in then don't save it again.
            				
            				var isIdIn=false;
            				for (c=0; c<document.getElementById("selectedSurface").options.length;c++){
            				if (document.getElementById("selectedSurface").options[c].value==id){
            					isIdIn=true;
            					break;
            				}
            				}
            				if (!isIdIn){
		                       	document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(featArea);                         
        		    			document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
            							new Option(name,id);
            					}
            					            	
                    }
                    
                    
                    
                }
            }
            
        }

var oldLoad = window.onload;
window.onload=function(){
initMap();
if (oldLoad) oldLoad();
}
</script>
 	
<div id="map" class="smallmap"></div>
<br>
<div id="docs">
</div>
<br>
<div id="panel" class="olControlEditingToolbar"></div>
<br>
 <div id="paneldiv" class="olControlNavToolbar"></div>
<br>
 <div id="status"></div>
 <br>
<div id="intersections"></div>

<?php 	$step = JRequest::getVar('step',"2");
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
 ?>
 <script>
 function submitOrderForm(){ 	
 	var selectedSurface = document.getElementById('selectedSurface');
 	
 if (selectedSurface.options.length>0){	  	
 	var replicSelectedSurface = document.getElementById('replicSelectedSurface');
 	var replicSelectedSurfaceName = document.getElementById('replicSelectedSurfaceName');
 	 	
 
	 var i=0; 
	 for (i=0;i<selectedSurface.options.length;i++){  
	 replicSelectedSurface.options[i] = new Option(selectedSurface.options[i].value,selectedSurface.options[i].value);
	 replicSelectedSurfaceName.options[i] = new Option(selectedSurface.options[i].text,selectedSurface.options[i].text);
	 replicSelectedSurface.options[i].selected=true;
	 replicSelectedSurfaceName.options[i].selected=true;
	 //replicSelectedSurface.options[i]= new Option(selectedSurface.options[i].value,selectedSurface.options[i].text);
	 
	 }
	document.getElementById('totalArea').value=document.getElementById('totalSurface').value;
 	document.getElementById('orderForm').submit();
 	}else {
 		if (document.getElementById('step').value == 1){
 			document.getElementById('orderForm').submit();
 		}else{
 			alert("<?php echo JText::_("NO_SELETED_DATA"); ?>");
 			}
 	}
 }
 </script>
 <div style="display: none;">
<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='POST'>

	<select multiple="multiple" size="10" id="replicSelectedSurface" name="replicSelectedSurface[]"></select>  	
	<select multiple="multiple" size="10" id="replicSelectedSurfaceName" name="replicSelectedSurfaceName[]"></select>
	<input type='hidden' id ="totalArea" name='totalArea' value='0'>	
	<input type='hidden' id ="fromStep" name='fromStep' value='2'>
	<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
	<input type='hidden' id ="option" name='option' value='<?php echo $option; ?>'>
	<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden' id ="perimeter_id" name='perimeter_id' value='<?php echo $task; ?>'>	
	<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
</form>
</div>
<?php

}

function orderRecap ($cid,$option){

	global  $mainframe;
	$db =& JFactory::getDBO(); 
		
	$query= "select * from #__easysdi_product where id in (";
	foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.")";
		$query =  $query . " and orderable = 1";
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}		
?>	
	<table>
	<thead class='contentheading'>
	<tr>
	<td></td>
	<td><?php echo JText::_("DATA_IDENTIFICATION");?></td>
	<td>
	<?php echo JText::_("ORGANISATION_NAME");?>
	</td>
	</tr>
	</thead>
	<tbody>
	
	
	<?php
	$i = 0;
		foreach( $rows as $product ){			
	?>	
	<tr>						
		<td><input type="hidden" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $product->id; ?>" /></td>
	<td>
	<a  href='index.php?option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->data_title; ?></a>
	</td>
	<td>
	<a  href='index.php?option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->supplier_name; ?></a>
	</td>		
	</tr>	
	<?php
		$i = $i +1;
		}		
	?>
	</tbody>
	</table>
	<?php
}

function orderProperties($cid){
	
global  $mainframe;
$db =& JFactory::getDBO(); 

	$step = JRequest::getVar('step',"2");
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$cid = $mainframe->getUserState('productList');	
?>
<h2 class="contentheading"><?php echo JText::_("PROPERTIES_SELECT_TITLE"); ?></h2>

<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>
<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='GET'>

<?php 


$query= "select * from #__easysdi_product where id in (";
foreach( $cid as $id ) {
			$query = $query.$id."," ;
		}
$query  = substr($query , 0, -1);
$query = $query.")";
$db->setQuery( $query);
$products = $db->loadObjectList();
if ($db->getErrorNum()) {						
		echo "<div class='alert'>";			
		echo 			$db->getErrorMsg();
		echo "</div>";
}
	

foreach ($products as $product  ){

$query = "SELECT  a.id as id,a.order as property_order, a.mandatory , a.text as property_text FROM #__easysdi_product_property b, #__easysdi_product_properties_definition  as a   WHERE a.id = b.property_value_id  and b .product_id = ". $product->id." order by a.order"
?>


<fieldset>
	<legend><?php echo $product->data_title;?></legend>						
<?php
$db->setQuery( $query );
$rows = $db->loadObjectList();
?>
<ol>

<?php 

if (count($rows)>0){
	foreach ($rows as $row){
		echo "<li>";									
		echo 	JText::_($row->property_text);
		
				$query = "SELECT  b.order as value_order, b.text as value_text ,b.id as value FROM #__easysdi_product_properties_values_definition  as b where b.properties_id  =".$row->id." order by  b.order";

				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				echo "<select name='property_".$product->id."[]'>";
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState('property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="selected";
					} 
					echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_text)."</option>";					
				}
		echo "</li>";
			 
		}
		
		//echo "</select>";
		//echo "</fieldset>";
		
}
		?>
</ol>
</fieldset>
<hr>
		<?php
		
	}
	?>
	<input type='hidden' id ="fromStep" name='fromStep' value='3'>
	<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
	<input type='hidden' id ="option" name='option' value='<?php echo $option; ?>'>
	<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
</form>
<?php 
	
}


function orderDefinition($cid){
	
		
	global  $mainframe;
	$db =& JFactory::getDBO(); 
	
	$step = JRequest::getVar ('step',4 );
	$option = JRequest::getVar ('option' );
	$task = JRequest::getVar ('task' );
	
?>
<h2 class="contentheading"><?php echo JText::_("USER_INFO_TITLE"); ?></h2>
<?php
$user = JFactory::getUser();	
if (!$user->guest){
	?>
	<div class="info"><?php echo JText::_("CONNECTED_WITH_USER").$user->name;  ?></div>
	<?php
	}
?>
<script>
 function submitOrderForm(){
 if (!document.getElementById('order_type_d').checked && !document.getElementById('order_type_o').checked){
 
 	alert("<?php echo JText::_("ORDER_TYPE_NOT_FILL") ?>");
 	return;
 }
 if (document.getElementById('order_name').value.length == 0){
 
 	alert("<?php echo JText::_("ORDER_NAME_NOT_FILL") ?>");
 	return;
 }
 	document.getElementById('orderForm').submit();
 }
 </script>
<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='GET'>
	<input type='hidden' id ="fromStep" name='fromStep' value='4'>
	<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
	<input type='hidden' id ="option" name='option' value='<?php echo $option; ?>'>
	<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">

	

<?php echo JText::_("ORDER_NAME"); ?><input type="text" name="order_name" id="order_name" value ="<?php echo $mainframe->getUserState('order_name'); ?>">
<br>
<?php echo JText::_("ORDER_TYPE_DEVIS"); ?><input type="radio" name="order_type" id="order_type_d"  value="D" <?php if ("D" == $mainframe->getUserState('order_type')) echo "checked"; ?>>
<br>
<?php echo JText::_("ORDER_TYPE_COMMANDE"); ?><input type="radio" name="order_type" id="order_type_o" value="O" <?php if ("O" == $mainframe->getUserState('order_type')) echo "checked"; ?>>


<?php

	
if ($user->guest){
		echo "<hr>";
		echo JText::_("USER_NOT_CONNECTED");
		?>
		
		<input type="text" name="user" value"">
		<input type="password" name="password" value"">
		<?php
}
$query = "select a.partner_id as partner_id, a.partner_acronym as acronym from jos_easysdi_community_partner a, jos_easysdi_community_actor b, jos_easysdi_community_role c where c.role_code = 'TIERCE' and c.role_id = b.role_id AND a.partner_id = b.partner_id";
$db->setQuery( $query);
$rows = $db->loadObjectList();
if ($db->getErrorNum()) {						
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
	}		
?>
<br>
<hr>
<?php echo JText::_("ORDER_THIRD_PARTY"); ?>
<select name="third_party">
<option value="0"><?php echo JText::_("ORDER_FOR_NOBODY"); ?></option>
<?php 
  
 $third_party = $mainframe->getUserState('third_party');
  echo $third_party;
	foreach ($rows as $row){
		$selected="";
		if ($third_party == $row->partner_id) $selected="selected";
	echo "<option ".$selected." value=\"".$row->partner_id."\">".$row->acronym."</option>";
	
} 
?>
</select>
</form>
<?php
	
 
}

function orderSend($cid){
	
	global $mainframe;

	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$step = JRequest::getVar('step',5);
	
	
	$user = JFactory::getUser();
	
		
	
?>
<div class="contentin">
<h2 class="contentheading"> <?php echo JText::_("ORDER_CONFIRM"); ?></h2>
<br>
<br>
<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>
<form id="orderForm" name="orderForm" action="<?php echo JRoute::_("index.php"); ?>">
<input type='hidden' id ="fromStep" name='fromStep' value='5'>
<input type="hidden" name="task" id="task" value="none" >
<input type="hidden" name="option" value="<?php echo JRequest::getVar('option'); ?>" >
<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">

</form>
	
<?php
	
if (!$user->guest){
	?>
	<input onClick="document.getElementById('task').value = 'saveOrder';submitOrderForm();" type="button" value='<?php echo JText::_("ORDER_SAVE_BUTTON"); ?>' > 
	<input onClick="document.getElementById('task').value = 'sendOrder';submitOrderForm();" type="button" value='<?php echo JText::_("ORDER_SEND_BUTTON"); ?>' >	
	
	<?php
	}else{?>
	<div class="alert">  
		<?php echo JText::_("NOT_CONNECTED");?>
	</div>
		<?php
	}
?>


</div>
<?php
}



function saveOrder($orderStatus){
	global $mainframe;
	
		
	$user = JFactory::getUser();
	if (!$user->guest){	
	$cid = $mainframe->getUserState('productList');
	$mainframe->setUserState('productList',null);
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$order_type = $mainframe->getUserState('order_type');
	$mainframe->setUserState('order_type',null);	
	$order_name = $mainframe->getUserState('order_name');
	$mainframe->setUserState('order_name',null);
	$third_party = $mainframe->getUserState('third_party');
	$mainframe->setUserState('third_party',null);
	
	$db =& JFactory::getDBO();
	
	jimport("joomla.utilities.date");
	$date = new JDate();
	
	

	$query = "INSERT INTO #__easysdi_order(third_party,type,order_id,name,status,order_update,user_id) VALUES (".$db->Quote($third_party)." ,'".$order_type."',0,'".$order_name."','".$orderStatus."','".$date->toMySQL()."',".$user->id.")";
	
	$db->setQuery($query );
	
	if (!$db->query()) {		
		echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";						
		}
	
	$order_id	= $db->insertId();
	$totalArea = $mainframe->getUserState('totalArea');
	$mainframe->setUserState('totalArea',null);

	$selSurfaceList = $mainframe->getUserState('selectedSurfaces');
	$mainframe->setUserState('selectedSurfaces',null);
	$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
	$mainframe->setUserState('selectedSurfacesName',null);
	
	$i=0;
	foreach ($selSurfaceList as $sel){
	
	//Before the dot, it is the perimeter id, after the dot id of the data	
	$pos1 = stripos($sel, ".");			
	
	$query =  "INSERT INTO #__easysdi_order_product_perimeters (id,order_id,perimeter_id,value,text) VALUES (0,$order_id,".substr($sel,0,$pos1).",'".substr($sel,$pos1+1)."',".$db->Quote($selSurfaceListName[$i]).")";
	$db->setQuery($query );	
	if (!$db->query()) {		
			echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";						
		}	
	$i++;
	}
		
	
	foreach ($cid as $product_id){
	
	if ($product_id != "0"){			
	$query = "INSERT INTO #__easysdi_order_product_list(id,product_id,order_id) VALUES (0,".$product_id.",".$order_id.")";
	
	$db->setQuery($query );	
	if (!$db->query()) {		
		echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";						
		}
			
	$productProperties  = $mainframe->getUserState('property_'.$product_id);
	$mainframe->setUserState('property_'.$product_id,null);
		
	foreach ($productProperties as $property_id){
	$query = "INSERT INTO #__easysdi_order_product_properties(id,order_product_list_id,property_id) VALUES (0,".$db->insertId().",".$property_id.")";
	
	$db->setQuery($query );	
	if (!$db->query()) {		
		echo "<div class='alert'>";
			echo $db->getErrorMsg();
			echo "</div>";						
		}
			
	}
	
	}
	
	}
	
	}else{
		
		?>
		<div class="alert" ><?php echo JText::_("NOT_ALLOWED"); ?></div>
		<?php
	}
	
	
}

function manageSession(){
		
	global $mainframe;
	$fromStep = JRequest::getVar('fromStep',0);
	
	/**
	 * Comming from nowhere. Save nothing!
	 */
	if ($fromStep == 0) return ;
	
	if ($fromStep == 1) {
		$cid = JRequest::getVar ('cid', array(0) );
		/* 
		 * Save the product list from the step 1
		*/
		if (is_array($mainframe->getUserState('productList'))){
			$cid = array_merge($cid,$mainframe->getUserState('productList'));
		}
		$mainframe->setUserState('productList',$cid);	
	}
	
	if ($fromStep == 2) {
		/* 
		 * Save the perimeter from the step 2
		*/
		$selSurfaceList = JRequest::getVar ('replicSelectedSurface', array(0) );																				
		$mainframe->setUserState('selectedSurfaces',$selSurfaceList);
		$selSurfaceListName = JRequest::getVar ('replicSelectedSurfaceName', array(0) );																				
		$mainframe->setUserState('selectedSurfacesName',$selSurfaceListName);
		$totalArea = JRequest::getVar ('totalArea', 0 );																				
		$mainframe->setUserState('totalArea',$totalArea);
	}
	
	if ($fromStep == 3) {
		/* 
		 * Save the properties from the step 3
		*/
		$cid = $mainframe->getUserState('productList');
		foreach ($cid as $id){
			$property=	JRequest::getVar("property_".$id, array(0) );									
			$mainframe->setUserState('property_'.$id,$property);			
		}
		
	}
	
	if ($fromStep == 4) {
		/* 
		 * Save the user's information from the step 4
		*/
		
		$third_party = JRequest::getVar("third_party");
		$order_name = JRequest::getVar("order_name");
		$order_type = JRequest::getVar("order_type");	
						
		$mainframe->setUserState('third_party',$third_party);
		$mainframe->setUserState('order_name',$order_name);
		$mainframe->setUserState('order_type',$order_type );

		$user = JFactory::getUser();	
		if ($user->guest){
	
			$options=array();
			$credentials = array();
			$credentials['username'] = JRequest::getVar('user');
			$credentials['password'] = JRequest::getString('password');		
			$error = $mainframe->login($credentials, $options);

			 if(JError::isError($error))
        		{ 		
        			$step = JRequest::getVar('step',4);
					echo "<div class='alert'>";			
					echo $error->getMessage();
					echo "</div>";
        }
		
		}
	}
	
	
	$productList = $mainframe->getUserState('productList');
	
	if (is_array($productList)){
		
	if (count($productList)==0){
		JRequest::setVar('step',1);
	}
	}
}

function order(){
	
	global $mainframe;
	
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$cid = JRequest::getVar ('cid', array(0) );
	$step = JRequest::getVar('step',1);
	
	HTML_shop::manageSession();
	$productList = $mainframe->getUserState('productList');
	
	
?>

			
	<table>
	<tr>
	<td>
	<div class="headerShop">
	<?php $curStep = 1; if(count($productList)>0&& ($curStep<$step || $curStep==$step+1)) { ?> <div  onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();" class="selectableStep"><?php echo JText::_("STEP".$curStep); ?> </div><?php }else {?><div class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><?php echo JText::_("STEP".$curStep); ?> </div>  <?php } ?>
	<?php $curStep = 2; if(count($productList)>0 && ($curStep<$step || $curStep==$step+1)) { ?> <div onClick="document.getElementById('step').value='<?php echo $curStep; ?>';submitOrderForm();" class="selectableStep"><?php echo JText::_("STEP".$curStep); ?> </div><?php }else {?><div class="<?php if ($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><?php echo JText::_("STEP".$curStep); ?> </div>  <?php } ?>
	<?php $curStep = 3; if(count($productList)>0 && ($curStep<$step || $curStep==$step+1)) { ?> <div onClick="document.getElementById('step').value='<?php echo $curStep; ?>';submitOrderForm();" class="selectableStep"><?php echo JText::_("STEP".$curStep); ?> </div><?php }else {?><div class="<?php if ($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><?php echo JText::_("STEP".$curStep); ?> </div>  <?php } ?>
	<?php $curStep = 4; if(count($productList)>0 && ($curStep<$step || $curStep==$step+1)) { ?> <div onClick="document.getElementById('step').value='<?php echo $curStep; ?>';submitOrderForm();" class="selectableStep"><?php echo JText::_("STEP".$curStep); ?> </div><?php }else {?><div class="<?php if ($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><?php echo JText::_("STEP".$curStep); ?> </div>  <?php } ?>
	<?php $curStep = 5; if(count($productList)>0 && ($curStep<$step || $curStep==$step+1)) { ?> <div onClick="document.getElementById('step').value='<?php echo $curStep; ?>';submitOrderForm();" class="selectableStep"><?php echo JText::_("STEP".$curStep); ?> </div><?php }else {?><div class="<?php if ($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><?php echo JText::_("STEP".$curStep); ?> </div>  <?php } ?>
	</div>
	</td></tr>
	<tr>
	<td>
	<div class="bodyShop">
	<?php if ($step ==1) HTML_shop::searchProducts();?>
	<?php if ($step ==2) HTML_shop::orderPerimeter($cid,$option);?>
	<?php if ($step ==3) HTML_shop::orderProperties($cid,$option);?>
	<?php if ($step ==4) HTML_shop::orderDefinition($cid);?>
	<?php if ($step ==5) HTML_shop::orderSend($cid);?>
	</div>
		</td></tr></table>
	<?php
	
}



function importProduct(){
	
	
	
	$catalogUrlBase = config_easysdi::getValue("catalog_url");
	if ($catalogUrlBase) $catalogUrlBase ="http://localhost:8081/proxy/ogc/geonetwork";
	$catalogUrlGetRecords = $catalogUrlBase."?request=GetRecords&service=CSW&version=2.0.1&resultType=results&namespace=csw%3Ahttp%3A%2F%2Fwww.opengis.net%2Fcat%2Fcsw&outputSchema=csw%3AIsoRecord&elementSetName=full&constraintLanguage=FILTER&constraint_language_version=1.1.0";
	$catalogUrlGetRecordsCount =  $catalogUrlGetRecords . "&startPosition=1&maxRecords=1";
	
	$cswResults= simplexml_load_file($catalogUrlGetRecordsCount);
		
	if ($cswResults !=null){
	$countMD = 0;
	foreach($cswResults->children("http://www.opengis.net/cat/csw")->SearchResults->attributes() as $a => $b) {
		if ($a=='numberOfRecordsMatched'){
			$countMD = $b;			
		}    		
	}
	$db =& JFactory::getDBO();
	
	$inc = 1;
	//$countMD
	for ($i=1; $i<=$countMD;$i=$i+$inc){
	$catalogUrlGetRecordsMD =  $catalogUrlGetRecords . "&startPosition=".$i."&maxRecords=".$inc;
	$cswResults= simplexml_load_file($catalogUrlGetRecordsMD); 
	echo $catalogUrlGetRecordsMD."<br>";
	foreach ($cswResults->children("http://www.opengis.net/cat/csw")->SearchResults->children("http://www.isotc211.org/2005/gmd")->MD_Metadata as $metadata){
		
		$md = new geoMeatdata($metadata);
		
		echo "<b>".$i."</b><br>";
		
		$query= "insert into #__easysdi_product (metadata_id,id,supplier_name,data_title) values(".$db->Quote($md->getFileIdentifier()).",0,".$db->Quote($md->getOrganisationName()).",".$db->Quote($md->getDataIdentificationTitle()).")";
		echo $query."<br>";		
		$db->setQuery( $query);		
		if (!$db->query()) {			
			echo "<div class='alert'>";									
				echo "<b>".$db->getErrorMsg()."</b><br>";
				echo "</div>";
		}							
	}
	}
	
	}
	
	
	
}


function searchProducts($orderable = 1){
	global $mainframe;
	$db =& JFactory::getDBO();
	
	$language=&JFactory::getLanguage();
	$language->load('com_easysdi');
	$limitstart = JRequest::getVar('limitstart',0);
	$limit = JRequest::getVar('limit',5);
	
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	$step = JRequest::getVar('step',"1");
	$countMD = JRequest::getVar('countMD');		
	$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','lastAddedMD');
	$freetextcriteria = JRequest::getVar('freetextcriteria','');
	$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );
	
	
	$cid = JRequest::getVar ('cid', array(0) );
	
	$filter = "";	
	
	$productList = $mainframe->getUserState('productList');
	if (count($productList)>0){
		$filter = " AND ID NOT IN (";
		foreach( $productList as $id){
		$filter = $filter.$id.",";
		}
		$filter = substr($filter , 0, -1);
		$filter = $filter.")";
	}
	
	if ($freetextcriteria){
		$filter = $filter." AND (DATA_TITLE like '%".$freetextcriteria."%' ";
		$filter = $filter." OR METADATA_ID = '$freetextcriteria')";
	}
		
		$user = JFactory::getUser();
		
		$partner = new partnerByUserId($db);
		if (!$user->guest){
			$partner->load($user->id);
		}else{
			$partner->partner_id = 0;
		}
	
	$filter .= " AND (EXTERNAL=1 OR (INTERNAL =1 AND PARTNER_ID IN (SELECT PARTNER_ID FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id OR root_id = $partner->partner_id))) ";
	$query  = "SELECT COUNT(*) FROM #__easysdi_product p where published=1 and orderable = ".$orderable;
	$query  = $query .$filter ;
	$db->setQuery( $query);
	$total = $db->loadResult();
		
	$query  = "SELECT * FROM #__easysdi_product p where published=1 and  orderable = ".$orderable;			
	$query  = $query .$filter ;
		
	if ($simpleSearchCriteria == "moreConsultedMD"){
				$query  = $query." order by weight";
	}
	if ($simpleSearchCriteria == "lastAddedMD"){
				$query  = $query." order by creation_date";
	}
	if ($simpleSearchCriteria == "lastUpdatedMD"){
				$query  = $query." order by update_date";
	}
		
	
	$db->setQuery( $query,$limitstart,$limit);
	$rows = $db->loadObjectList();
			
	if ($db->getErrorNum()) {
		echo "<div class='alert'>"; 										
		echo 	$db->getErrorMsg();
		echo "</div>";
	}			 
	?>	
	
	<div class="contentin">	
<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>		
	<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='POST'>
	<h2 class="contentheading"><?php echo JText::_("SEARCH_PRODUCT_TITLE"); ?></h2>
	
	<h3> <?php echo JText::_("SEARCH_CRITERIA_TITLE"); ?></h3>
		<br>
		<span class="searchCriteria">
			<input name="freetextcriteria" type="text" value=""><br>
		 	<input type="radio" name="simpleSearchCriteria" value="lastAddedMD" <?php if ($simpleSearchCriteria == "lastAddedMD") echo "checked";?>> <?php echo JText::_("LAST_ADDED_MD"); ?><br>
		 	<input type="radio" name="simpleSearchCriteria" value="moreConsultedMD" <?php if ($simpleSearchCriteria == "moreConsultedMD") echo "checked";?>> <?php echo JText::_("MORECONSULTED_MD"); ?><br>
		 	<input type="radio" name="simpleSearchCriteria" value="lastUpdatedMD" <?php if ($simpleSearchCriteria == "lastUpdatedMD") echo "checked";?> > <?php echo JText::_("LAST_UPDATED_MD"); ?><br>
	 	</span>
	 	<br>
	 	<button type="submit" class="searchButton" > <?php echo JText::_("SEARCH_BUTTON"); ?></button>
	 	<button type="submit" class="searchButton" > <?php echo JText::_("ADD_TO_PANEL"); ?></button>
	 	<br>
	 	<br>
	<h3><?php echo JText::_("SEARCH_RESULTS_TITLE"); ?></h3>
			 			 
	<input type='hidden' name='option' value='<?php echo $option;?>'>
	<input type='hidden' id ="task" name='task' value='<?php echo $task; ?>'>
	<input type='hidden' id ="fromStep" name='fromStep' value='1'>
	<input type='hidden' id ="step" name='step' value='<?php echo $step; ?>'>
	<input type='hidden'  name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
	
	
	<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
	<span class="searchCriteria">
	<table width="100%">
	<tr><td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
	</table>
	
	<?php
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);	
	$i=0;	
	
	foreach ($rows  as $row){		
		?>
<hr>		
		<table width="100%">
		
		<tr>		
		<td><img src="./img.gif" width="40" height="40"> </td>
		<td><span class="mdtitle" ><?php echo $row->data_title; ?></span><br>
			<span class="mdsupplier" ><?php echo $row->supplier_name;?></span><br>
			<a class="modal" title="<?php echo JText::_("VIEW_MD"); ?>" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo JText::_("VIEW_MD"); ?></a>
			<a target="_PDF" title="<?php echo JText::_("VIEW_MD_PDF"); ?>" href="./index.php?tmpl=component&format=pdf&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" > <?php echo JText::_("VIEW_MD_PDF"); ?></a>													
		</td>
		<td> 				
		<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" <?php if (in_array($row->id,$cid)) { echo "checked";};?>/></td>				
		</tr>				
		</table>		
		<?php
		$i=$i+1;
	}	
	?>	
		<input type="hidden" name="countMD" value="<?php echo $countMD;?>">				
	</span>	
	</form>
</div>	
	<?php
}}
?>