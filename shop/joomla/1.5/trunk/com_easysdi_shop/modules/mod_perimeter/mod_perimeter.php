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

global  $mainframe;
$curstep = JRequest::getVar('step',0);

if ($curstep == "2"){
$db =& JFactory::getDBO(); 		
$cid = 		$mainframe->getUserState('productList');
					
	if (count($cid)>0){
			$query = "SELECT * FROM #__easysdi_perimeter_definition";
	
			
	$query = "select count(*)  from #__easysdi_product where id in (";
	foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.")";
		
	$db->setQuery( $query );
	$nbProduct = $db->loadResult();
		
	$query = "SELECT * FROM #__easysdi_perimeter_definition WHERE id in (SELECT perimeter_id  FROM #__easysdi_product_perimeter  where product_id in (";

		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}			
		$query  = substr($query , 0, -1);
	$query = $query . ") group by perimeter_id  having  count(*)  = ".$nbProduct.")";
	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			echo 			$db->getErrorMsg();
	}		


	?>
	
	<script>
			
	function selectPerimeter(){
	
	selIndex = document.getElementById('perimeterList').selectedIndex;


	<?php	
	foreach ($rows as $row)
		{?>
	 	if (document.getElementById('perimeterList')[selIndex].value == '<?php echo $row->id; ?>'){
	 			selectWFSPerimeter(document.getElementById('perimeterList')[selIndex].value,"<?php echo $row->perimeter_name; ?>","<?php echo $row->wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>","<?php echo $row->area_field_name; ?>","<?php echo $row->wms_url; ?>","<?php echo $row->layer_name; ?>");	 		
	 		}
	 
	 <?php } ?>
      }
    </script>   
    
    
    
        <table>
        <tr>
        <td> 
	<select id="perimeterList"  onChange="selectPerimeter()">
	<!-- option value="-1"><?php echo JText::_("EASYSDI_SELECT_THE_PERIMETER"); ?></option -->
	<?php
	foreach ($rows as $row)
		{			
			?>
			<option value ="<?php echo $row->id ?>"> <?php echo JText::_($row->perimeter_name); ?> </option>
			<?php 
				  		
		}
		?>
		</select>
		</td></tr>
		<tr><td>
	<div id="panelEdition" class="olControlEditingToolbar"></div>
	</td></tr>  
<tr><td>
<div id="status">  </div>
</td></tr></table>
	<br>	
		<?php
		$query =  "SELECT * FROM #__easysdi_product  WHERE id in (";
		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.")";
		$querySurfaceMax = $query." having min(surface_max)";
		$querySurfaceMin = $query." having max(surface_min)";
		
		$db->setQuery( $querySurfaceMax);
		$rowsSurfaceMin = $db->loadObjectList();
		foreach( $rowsSurfaceMin as $surfaceMin )
		{
			echo JText::_("EASYSDI_SURFACE_MIN")." ".$surfaceMin->surface_min." ".JText::_("EASYSDI_SURFACE_MIN_UNIT")."<br>";
			?><input type="hidden" size="10" id="totalSurfaceMin" disabled="disabled" value="<?php echo $surfaceMin->surface_min; ?>">
			<?php					
		
		}
		
		$db->setQuery( $querySurfaceMax);
		$rowsSurfaceMax = $db->loadObjectList();
		foreach( $rowsSurfaceMax as $surfaceMax )
		{
			echo JText::_("EASYSDI_SURFACE_MAX")." ".$surfaceMax->surface_max." ".JText::_("EASYSDI_SURFACE_MAX_UNIT")."<br>";
			?><input type="hidden" size="10" id="totalSurfaceMax" disabled="disabled" value="<?php echo $surfaceMax->surface_max; ?>">
			<?php
		}
		
		 
		$totalArea = $mainframe->getUserState('totalArea');
		if (!$totalArea) $totalArea=0;
		
		echo JText::_("EASYSDI_SURFACE_SELECTED");?>
		<input type="text" size="30" id="totalSurface" disabled="disabled" value="<?php echo $totalArea; ?>"><br>
		<select multiple="multiple" size="10" id="selectedSurface" onChange="changeSelectedSurface()">
		<?php																								
			$selSurfaceList = $mainframe->getUserState('selectedSurfaces');
			$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
			$i=0;
			foreach ($selSurfaceList as $sel){
				
				echo "<option value=\"$sel\">$selSurfaceListName[$i]</option>";
				$i++;
			}
					
		 ?>		  
		</select>
		
		
		  
		<script>
		function removeSelected()  {
		     
     if (isFreeSelectionPerimeter){
		var elSel = document.getElementById('selectedSurface');		
		var i;
			  for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {
			     document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) - parseFloat(elSel.options[i].value);
			      elSel.remove(i);			  	       
			    }
			  }
			 	
			 	drawSelectedSurface();
			 				 
		}else {

		var elSel = document.getElementById('selectedSurface');		
		 for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {
				var idToLookFor =  elSel.options[i].value;
				var wfsFeatures = wfs.features;
				// look for a feature with the same id
				var found = false;
				
				for(var j=wfsFeatures.length-1; j>=0; j--) {
                    feat2 = wfsFeatures[j];                       
                      
 //                      if (idToLookFor ==  document.getElementById('perimeter_id').value +"."+feat2.attributes[idField]){
                       if (idToLookFor ==  feat2.attributes[idField]){
                       	found=true;
                       
                       	wfs.removeFeatures([wfsFeatures[j]]);
                       	break;
                       }
                       }

			    }
			  }
			  
			  
		}
			  
		}



		 function drawSelectedSurface(){
			 var elSel = document.getElementById('selectedSurface');
			 var features = vectors.features; 
     	     var feature= features[features.length-1];
			var selectedComponents =      new Array();
			  	
			  	if (feature){				  
			  		var newLinearRingComponents = new Array();
	
					for (i = elSel.length - 1; i>=0; i--) {
							var curValue = elSel.options[i].value;
						var x= curValue.substring(0,curValue .indexOf(" ", 0));
						var y= curValue.substring(curValue .indexOf(" ", 0)+1,curValue .length);
					    newLinearRingComponents.push (new OpenLayers.Geometry.Point(x,y));		     
					    }
	
						var newLinearRing = new OpenLayers.Geometry.LinearRing(newLinearRingComponents);
						feature = new OpenLayers. Feature. Vector(new OpenLayers.Geometry.Polygon([newLinearRing]));									  			
						vectors.removeFeatures(features);				
						vectors.addFeatures([feature]);
			  			vectors.drawFeature (feature);
			  			
     				if (feature.geometry.components[0].components.length > 2){
   			 				featureArea = feature.geometry.getArea();
    					}else{
    						featureArea = 0;
    					}
					document.getElementById('totalSurface').value =  parseFloat(featureArea );    		
		}
		
		}
		
		
function recenterOnPerimeter(){
	
		
		var elSel = document.getElementById("perimetersList");
			  for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {			     	
                    var wfsFeatures = wfs3.features;
					var idToLookFor = elSel.options[i].value;
					var found = false;
					for(var j=wfsFeatures.length-1; j>=0; j--) {
                    	feat2 = wfsFeatures[j];                       
                      
                       if (idToLookFor == feat2.attributes[idField]){
                       		found=true;
							map.zoomToExtent (feat2.geometry.getBounds(),true);

                       		break;
                       }
                       }                       
                       break;
                       }                       
			     	}		  	       
		
		}
		
		
	function addManualPerimeter(){
		
		var elSel = document.getElementById("perimetersList");
			  for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {			     	
                    var wfsFeatures = wfs3.features;
					var idToLookFor = elSel.options[i].value;
					var found = false;
					for(var j=wfsFeatures.length-1; j>=0; j--) {
                    	feat2 = wfsFeatures[j];                       
                      
                       if (idToLookFor == feat2.attributes[idField]){
                       		found=true;
                       		if (!wfs){                       		
                       		  wfs = new OpenLayers.Layer.Vector("selectedFeatures", {
                    					strategies: [new OpenLayers.Strategy.Fixed()],
                    					  protocol: new OpenLayers.Protocol.HTTP({
                      					 	
                        					format: new OpenLayers.Format.GML()                        
                    						})                    				
                				}
                				                				 		                			
                				);
                				wfsRegisterEvents();
                				map.addLayer(wfs);		                                                       
                       		}
                       		     		wfs.addFeatures([wfsFeatures[j]]);
                       		break;
                       }
                       }                       
                       break;
                       }                       
			     	}		  	       
			    }
			  
			 
		
		
		function addGeometryPerimeter(){
			if (isFreeSelectionPerimeter){
			
			var elSel = document.getElementById('selectedSurface');
			if (elSel.options.selectedIndex>=0){
			
			
			var selectedIndex = elSel.options.selectedIndex;
			
			for (i = elSel.length - 1; i>=selectedIndex; i--) {
					elSel.options[i+1] = new Option (elSel.options[i].text,elSel.options[i].value);
			    }
			
			elSel.options[selectedIndex] = new Option(document.getElementById("xText").value+" "+document.getElementById("yText").value,document.getElementById("xText").value+" "+document.getElementById("yText").value);
			
			}
			else{
					elSel.options[elSel.options.length] =  new Option(document.getElementById("xText").value+" "+document.getElementById("yText").value,document.getElementById("xText").value+" "+document.getElementById("yText").value);
			}

			drawSelectedSurface();	
				}
		}
		
		
		function modifyGeometryPerimeter(){
			if (isFreeSelectionPerimeter){
			
			var elSel = document.getElementById('selectedSurface');
				for (i = elSel.length - 1; i>=0; i--) {
				if (elSel.options[i].selected) {
					var curValue = elSel.options[i].value;
					elSel.options[i].value = document.getElementById("xText").value+" "+document.getElementById("yText").value;
					elSel.options[i].text = document.getElementById("xText").value+" "+document.getElementById("yText").value;				
					drawSelectedSurface();																		
					break;
				}		     
			    }
			
				
				}
		}
		function changeSelectedSurface(){
			if (isFreeSelectionPerimeter){
						
				var elSel = document.getElementById('selectedSurface');
				for (i = elSel.length - 1; i>=0; i--) {
				if (elSel.options[i].selected) {
					var curValue = elSel.options[i].value;
						
					document.getElementById("xText").value=curValue.substring(0,curValue .indexOf(" ", 0));
					document.getElementById("yText").value=curValue.substring(curValue .indexOf(" ", 0)+1,curValue .length);
					break;
				}		     
			    }
					    
					    
			}
		}
		
		function selectManualPerimeter(){
			
			if (isFreeSelectionPerimeter){
				document.getElementById('manualPerimDivId').style.display='none';
				document.getElementById('manualAddGeometry').style.display='block';								
			}else{
				document.getElementById('manualPerimDivId').style.display='block';
				document.getElementById('manualAddGeometry').style.display='none';						
				fillSelectPerimeter();
			}
		}
		
			
			
		function freeSelectPerimeter(){
			var elSel = document.getElementById("perimetersList");
			while (elSel.length > 0)
				{
					elSel.remove(elSel.length - 1);
			}
		}
		
		function fillSelectPerimeter(){
		var elSel = document.getElementById("perimetersList");
		freeSelectPerimeter();
		
		elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("EASYSDI_LOADING_MANUAL_PERIMETER");?>","");
		
		var wfsUrlWithBBox = wfsUrl +"&BBOX="+map.maxExtent.toBBOX()+"&MAXFEATURES=50";
		
		wfs3 = new OpenLayers.Layer.Vector("selectedFeatures", {
                    strategies: [new OpenLayers.Strategy.Fixed()],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: wfsUrlWithBBox,
                        format: new OpenLayers.Format.GML()                        
                    })
                });		    	            
		wfs3.events.register("featureadded", null, function(event) {
		var elSel = document.getElementById("perimetersList");
		if (elSel.options[0].value==""){
				elSel.remove(0);
		}
		
		
		var feat2 = event.feature;
		
		var perim = document.getElementById("perimetersList");
		var id = feat2.attributes[idField];
		var name = feat2.attributes[nameField];
		
		perim.options[perim.options.length] =  new Option(name,id);
              });
              
             map.addLayer(wfs3);
              map.removeLayer(wfs3);    
              
              
            }
		</script>
		
		<button class="deletePerimeterButton" type="button" onClick="removeSelected();"><?php echo JText::_("EASYSDI_REMOVE_SELECTED_VALUE");?></button>
						
		<button class="addPerimeterButton" type="button" onClick="selectManualPerimeter();"><?php echo JText::_("EASYSDI_SELECT_MANUAL_PERIMETER");?></button>
		
		
		<div  id="manualPerimDivId" style="display:none">
			<?php
			/*	
				<select id="perimetersList"></select>
				<button class="addPerimeterButton" type="button" onClick="addManualPerimeter();"><?php echo JText::_("EASYSDI_ADD_MANUAL_PERIMETER");?></button>
				<button class="addPerimeterButton" type="button" onClick="recenterOnPerimeter();"><?php echo JText::_("EASYSDI_RECENTER_MANUAL_PERIMETER");?></button>
			*/
			 ?>
			
			<?php include 'manual_perimeter.php' ;?>
		</div>
		<div  id="manualAddGeometry" style="display:none">
				
			<?php echo JText::_("EASYSDI_ADD_X_TEXT");?> <input type="text" id ="xText" value="0"><br>
			<?php echo JText::_("EASYSDI_ADD_Y_TEXT");?> <input type="text" id ="yText" value="0"><br>
			<button class="addPerimeterButton" type="button" onClick="addGeometryPerimeter();"><?php echo JText::_("EASYSDI_ADD_GEOMETRY_PERIMETER");?></button>			
			<button class="addPerimeterButton" type="button" onClick="modifyGeometryPerimeter();"><?php echo JText::_("EASYSDI_MODIFY_GEOMETRY_PERIMETER");?></button>			
		</div>
		<?php
	}
}else{
	
	if ($curstep > 2){
	
	
	$totalArea = $mainframe->getUserState('totalArea');
	if (!$totalArea) $totalArea=0;
	?>
	<div>	
	<fieldset>
	<legend><?php echo $totalArea." ".JText::_("EASYSDI_SURFACE_MAX_UNIT") ;?></legend>	
	
		<?php																								
			
			$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
			if ($selSurfaceListName!=null){
			foreach ($selSurfaceListName as $sel){
				
				echo $sel."<br>";		
				
			}
			}
		 ?>		  		
		</fieldset>
	</div>
<?php	
	}
}
?>
