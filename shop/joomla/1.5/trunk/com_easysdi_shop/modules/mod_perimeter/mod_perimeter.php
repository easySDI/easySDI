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

if ($curstep == "2")
{
	$db =& JFactory::getDBO();

	$query = "select * from #__easysdi_basemap_definition where def = 1";
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$db->getErrorMsg();
		echo "</div>";
	}

	$decimal_precision = $rows[0]->decimalPrecisionDisplayed;

	$cid = 		$mainframe->getUserState('productList');
		
	if (count($cid)>0){
		$query = "SELECT * FROM #__easysdi_perimeter_definition order by ordering";

			
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
		$query .= " order by ordering";
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			echo 			$db->getErrorMsg();
		}




		$query = "SELECT count(*) as total ,perimeter_id  FROM `#__easysdi_product_perimeter` where product_id in (";
		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query .") AND isBufferAllowed= 1 group by perimeter_id having count(*) = $nbProduct";


		$db->setQuery( $query );

		$bufferRows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			echo 			$db->getErrorMsg();
		}


		?>

<script><!--
	
	
	function enableBufferByPerimeter(perimId){
	//document.getElementById('bufferValue').value=0;
		<?php	
	foreach ($bufferRows as $bufferRow)
		{		
			?>
		if (perimId == '<?php echo $bufferRow->perimeter_id ?>'){
			document.getElementById('bufferValue').disabled=false;
			return ;
		}
		
		<?php 
		}
		?>
		document.getElementById('bufferValue').disabled=true;
	}
	
			
	function selectPerimeter(perimListName){
	
	
	selIndex = document.getElementById(perimListName).selectedIndex;


	<?php	
	foreach ($rows as $row)
		{
			
			if (1==1 || ($row->user !=null && strlen($row->user)>0)){
						
						//if a user and password is requested then use the joomla proxy.
						$proxyhostOrig = config_easysdi::getValue("PROXYHOST");
						$proxyhost = $proxyhostOrig."&type=wfs&perimeterdefid=$row->id&url=";
						
						if ($row->wfs_url!=null && strlen($row->wfs_url)>0){
							$wfs_url =  $proxyhost.urlencode  (trim($row->wfs_url));
						}else{
							$wfs_url ="";
						}
						
						$proxyhost = $proxyhostOrig."&type=wms&perimeterdefid=$row->id&url=";
						if ( $row->wms_url!=null && strlen($row->wms_url)>0){
							$wms_url = $proxyhost.urlencode  (trim($row->wms_url));
						}else{
							$wms_url ="";
						}
					}else{
						$wfs_url = $row->wfs_url;
						
						$wms_url=	$row->wms_url;					
					}
			?>
	 	if (document.getElementById(perimListName)[selIndex].value == '<?php echo $row->id; ?>')
	 	{	
	 		$("scaleStatus").innerHTML = "";
	 		if(<?php echo $row->min_resolution;?> == 0 && <?php echo $row->max_resolution; ?> == 0 )
	 		{
	 			//Free selection perimeter case
	 			//Always display the button for manual perimeter
	 			document.getElementById('addPerimeterButton').style.display='block';
	 		}
	 		else
	 		{
			 	if (map.getScale() < <?php echo $row->max_resolution; ?> || map.getScale() > <?php echo $row->min_resolution; ?>){
					text = "<?php echo JText::_("EASYSDI_OUTSIDE_SCALE_RANGE"); ?>" + " : " + '<?php echo $row->perimeter_name; ?>' +  " ("+<?php echo $row->min_resolution; ?>+"," + <?php echo $row->max_resolution; ?> +")<BR>";
					$("scaleStatus").innerHTML = text;
					//document.getElementById(perimListName).selectedIndex = document.getElementById('lastSelectedPerimeterIndex').value;
					return;
					
				}
				//Display the button for manual perimeter
				if(<?php echo $row->is_localisation;?> == 0)
				{
					document.getElementById('addPerimeterButton').style.display='none';
				}
				else
				{
					document.getElementById('addPerimeterButton').style.display='block';
				}
			} 
			
			//document.getElementById('lastSelectedPerimeterIndex').value = document.getElementById(perimListName).selectedIndex;
	 		selectWFSPerimeter(document.getElementById(perimListName)[selIndex].value,"<?php echo $row->perimeter_name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>","<?php echo $row->area_field_name; ?>","<?php echo $wms_url; ?>","<?php echo $row->layer_name; ?>","<?php echo $row->img_format; ?>",<?php echo $row->min_resolution; ?>,<?php echo $row->max_resolution; ?>);
	 		enableBufferByPerimeter('<?php echo $row->id; ?>');	 	
	 		
	 	
	 	}
	 
	 <?php } ?>
      }
    --></script>


<input type="hidden" size="30" id="lastSelectedPerimeterIndex"  value="0">
<table>
	<tr>
		<td><select id="perimeterList" onChange="selectPerimeter('perimeterList')"  >
			<!-- option value="-1"><?php echo JText::_("EASYSDI_SELECT_THE_PERIMETER"); ?></option -->
			<?php
			foreach ($rows as $row)
			{
				?>
			<option value="<?php echo $row->id ?>" <?php if ($row->id == $mainframe->getUserState('perimeter_id')) { echo 'SELECTED';};?>><?php echo JText::_($row->perimeter_name); ?>
			</option>
			<?php

			}
			?>
		</select></td>
	</tr>
	<tr>
		<td>
		<div id="panelEdition" class="olControlEditingToolbar"></div>
		</td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_BUFFER"); ?>
		<input type="text" id="bufferValue"  value="<?php echo $mainframe->getUserState('bufferValue') ;?>" onchange="checkBufferValue()"></td>
	</tr>
</table>
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
				?>
<input type="hidden" size="10" id="totalSurfaceMin" disabled="disabled"
	value="<?php echo $surfaceMin->surface_min; ?>">
				<?php

			}

			$db->setQuery( $querySurfaceMax);
			$rowsSurfaceMax = $db->loadObjectList();
			foreach( $rowsSurfaceMax as $surfaceMax )
			{
				echo JText::_("EASYSDI_SURFACE_MAX")." ".$surfaceMax->surface_max." ".JText::_("EASYSDI_SURFACE_MAX_UNIT")."<br>";
				?>
<input type="hidden" size="10" id="totalSurfaceMax" disabled="disabled"
	value="<?php echo $surfaceMax->surface_max; ?>">
				<?php
			}

				
			$totalArea = $mainframe->getUserState('totalArea');
			if (!$totalArea) $totalArea=0;


			echo JText::_("EASYSDI_SURFACE_SELECTED");?>
<input type="hidden" size="30" id="totalSurface" disabled="disabled" value="<?php echo $totalArea; ?>">
<input type="text"  id="totalSurfaceDisplayed" disabled="disabled" value="<?php echo round($totalArea, $decimal_precision); ?>">
<br>
<select multiple="multiple" size="10" id="selectedSurface"  	onChange="changeSelectedSurface()">
	<?php
		$selSurfaceList = $mainframe->getUserState('selectedSurfaces');
		$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
		$i=0;
		foreach ($selSurfaceList as $sel)
		{
			echo "<option value=\"$sel\">$selSurfaceListName[$i] </option>";
			$i++;
		}
	?>
</select>



<script><!--


function checkBufferValue()
{
	var bufferValue = document.getElementById('bufferValue').value;
	document.getElementById('bufferValue2').value = document.getElementById('bufferValue').value;
	if( parseFloat(bufferValue) < 0)
	{
		$("status").innerHTML = "<?php echo JText::_("EASYSDI_MESSAGE_ERROR_BUFFER_VALUE"); ?>";
	}	
	else
	{	
		$("status").innerHTML = "";
	}
}


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
		
		



	 function drawSelectedSurface()
	 {	
	 	var elSel = document.getElementById('selectedSurface');
		var features = vectors.features;
		if (features.length == 0) 
		{
			vectors.addFeatures([new OpenLayers. Feature. Vector(new OpenLayers.Geometry.Polygon())]);
		} 
        var feature= features[features.length-1];
		var selectedComponents = new Array();
		if (feature)
		{				  
			var newLinearRingComponents = new Array();
			for (i = elSel.length - 1; i>=0; i--) 
			{
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
		  			
     		if (feature.geometry.components[0].components.length > 2)
     		{
   		 		featureArea = feature.geometry.getArea();
    		}
    		else
    		{
    			featureArea = 0;
    		}
			document.getElementById('totalSurface').value =  parseFloat(featureArea );   
			document.getElementById('totalSurfaceDisplayed').value =  parseFloat(featureArea ).toFixed(<?php echo $decimal_precision; ?> );    		
		}
		
	}
		
		
	function recenterOnPerimeter()
	{
		var elSel = document.getElementById("perimetersList");
		for (i = elSel.length - 1; i>=0; i--) 
		{
			if (elSel.options[i].selected) 
			{			     	
            	var wfsFeatures = wfs3.features;
				var idToLookFor = elSel.options[i].value;
				var found = false;
				for(var j=wfsFeatures.length-1; j>=0; j--) 
				{
                   	feat2 = wfsFeatures[j];                       
                     
                    if (idToLookFor == feat2.attributes[idField])
                    {
                    	found=true;
						map.zoomToExtent (feat2.geometry.getBounds(),true);
                       	break;
                     }
                 }                       
                 break;
             }                       
    	}		  	       
    }
		
		
	function addManualPerimeter()
	{
		var elSel = document.getElementById("perimetersList");
	    for (i = elSel.length - 1; i>=0; i--) 
	    {
			if (elSel.options[i].selected) 
			{			     	
            	var wfsFeatures = wfs3.features;
				var idToLookFor = elSel.options[i].value;
				var found = false;
				for(var j=wfsFeatures.length-1; j>=0; j--) 
				{
                   	feat2 = wfsFeatures[j];                       
                    if (idToLookFor == feat2.attributes[idField])
                    {
                    	found=true;
                       	if (!wfs)
                       	{                       		
                       	  wfs = new OpenLayers.Layer.Vector("selectedFeatures", {
                    					strategies: [new OpenLayers.Strategy.Fixed()],
                    					  protocol: new OpenLayers.Protocol.HTTP({                      					 	
                        					format: new OpenLayers.Format.GML()})});
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
			  
			 
		
		
		function addGeometryPerimeter()
		{
			if (isFreeSelectionPerimeter)
			{
				var elSel = document.getElementById('selectedSurface');
				if (elSel.options.selectedIndex>=0)
				{
					var selectedIndex = elSel.options.selectedIndex;
				
					for (i = elSel.length - 1; i>=selectedIndex; i--) 
					{
						elSel.options[i+1] = new Option (elSel.options[i].text,elSel.options[i].value);
					}
				
					elSel.options[selectedIndex] = new Option(parseFloat(document.getElementById("xText").value).toFixed(<?php echo $decimal_precision; ?>)+" / "+ parseFloat(document.getElementById("yText").value).toFixed(<?php echo $decimal_precision; ?>),document.getElementById("xText").value+" "+document.getElementById("yText").value);
			
				}
				else
				{
					elSel.options[elSel.options.length] =  new Option(parseFloat(document.getElementById("xText").value).toFixed(<?php echo $decimal_precision; ?>)+" / "+ parseFloat(document.getElementById("yText").value).toFixed(<?php echo $decimal_precision; ?>),document.getElementById("xText").value+" "+document.getElementById("yText").value);
				}

				drawSelectedSurface();	
			}
		}
		
		
		function modifyGeometryPerimeter()
		{
			if (isFreeSelectionPerimeter)
			{
				var elSel = document.getElementById('selectedSurface');
				for (i = elSel.length - 1; i>=0; i--) 
				{
					if (elSel.options[i].selected) 
					{
						var curValue = elSel.options[i].value;
						elSel.options[i].value = document.getElementById("xText").value+" "+document.getElementById("yText").value;
						elSel.options[i].text = parseFloat(document.getElementById("xText").value).toFixed(<?php echo $decimal_precision; ?>) +" / "+ parseFloat(document.getElementById("yText").value).toFixed(<?php echo $decimal_precision; ?>);				
						drawSelectedSurface();																		
						break;
					}		     
			    }
			}
		}
		
		function changeSelectedSurface()
		{
			if (isFreeSelectionPerimeter)
			{
				var elSel = document.getElementById('selectedSurface');
				for (i = elSel.length - 1; i>=0; i--) 
				{
					if (elSel.options[i].selected) 
					{
						var curValue = elSel.options[i].value;
						
						document.getElementById("xText").value=curValue.substring(0,curValue .indexOf(" ", 0));
						document.getElementById("yText").value=curValue.substring(curValue .indexOf(" ", 0)+1,curValue .length);
						break;
					}		     
			    }
			}
		}
		
		function selectManualPerimeter()
		{
			if (isFreeSelectionPerimeter)
			{
				document.getElementById('manualPerimDivId').style.display='none';
				document.getElementById('manualAddGeometry').style.display='block';								
			}else
			{
				document.getElementById('manualPerimDivId').style.display='block';
				document.getElementById('manualAddGeometry').style.display='none';						
				fillSelectPerimeter();
			}
		}
		
			
			
		function freeSelectPerimeter()
		{
			var elSel = document.getElementById("perimetersList");
			while (elSel.length > 0)
			{
				elSel.remove(elSel.length - 1);
			}
		}
		
		function fillSelectPerimeter()
		{
			selectPerimeterPerimeter(document.getElementById('perimeterList')[document.getElementById('perimeterList').selectedIndex].value)
        }
		--></script>

<button class="deletePerimeterButton" type="button"
	onClick="removeSelected();"><?php echo JText::_("EASYSDI_REMOVE_SELECTED_VALUE");?></button>

<button class="addPerimeterButton" type="button" id="addPerimeterButton"
	onClick="selectManualPerimeter();"><?php echo JText::_("EASYSDI_SELECT_MANUAL_PERIMETER");?></button>


<div id="manualPerimDivId" style="display: none"><?php
/*
 <select id="perimetersList"></select>
 <button class="addPerimeterButton" type="button" onClick="addManualPerimeter();"><?php echo JText::_("EASYSDI_ADD_MANUAL_PERIMETER");?></button>
 <button class="addPerimeterButton" type="button" onClick="recenterOnPerimeter();"><?php echo JText::_("EASYSDI_RECENTER_MANUAL_PERIMETER");?></button>
 */
?> <?php include 'manual_perimeter.php' ;?></div>
<div id="manualAddGeometry" style="display: none"><?php echo JText::_("EASYSDI_ADD_X_TEXT");?>
<input type="text" id="xText" value=""><br>
<?php echo JText::_("EASYSDI_ADD_Y_TEXT");?> <input type="text"
	id="yText" value=""><br>
<button class="addPerimeterButton" type="button"
	onClick="addGeometryPerimeter();document.getElementById('xText').value='';document.getElementById('yText').value='';"><?php echo JText::_("EASYSDI_ADD_GEOMETRY_PERIMETER");?></button>
<button class="addPerimeterButton" type="button"
	onClick="modifyGeometryPerimeter();document.getElementById('xText').value='';document.getElementById('yText').value='';"><?php echo JText::_("EASYSDI_MODIFY_GEOMETRY_PERIMETER");?></button>
</div>
<?php
	}
}else{

	if ($curstep > 2){
		$db =& JFactory::getDBO();
		$bufferValue = $mainframe->getUserState('bufferValue');
		 //$bufferValue = JRequest::getVar('bufferValue');

		$query = "select * from #__easysdi_basemap_definition where def = 1";
		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}
	
		$decimal_precision = $rows[0]->decimalPrecisionDisplayed;
			

		$totalArea = $mainframe->getUserState('totalArea');
		if (!$totalArea) $totalArea=0;
		?>
<div>
<fieldset><legend><?php echo round($totalArea,$decimal_precision)." ".JText::_("EASYSDI_SURFACE_MAX_UNIT") ;?></legend>

		<?php
			
		$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
		if ($selSurfaceListName!=null){
			foreach ($selSurfaceListName as $sel){

				echo $sel."<br>";

			}
		}
		?></fieldset>
		<?php
		if ($bufferValue>0){
			echo JText::_("EASYSDI_BUFFER_VALUE")." ".$bufferValue." ".JText::_("EASYSDI_BUFFER_UNIT") ;
		}
		?></div>
		<?php
	}
}
?>
