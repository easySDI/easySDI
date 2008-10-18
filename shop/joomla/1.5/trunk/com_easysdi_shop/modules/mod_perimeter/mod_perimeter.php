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

	 	if (document.getElementById('perimeterList')[selIndex].value == '-1'){
	 			selectWFSPerimeter(-1,"","","","","","","","");	 		
	 		}

	<?php	
	foreach ($rows as $row)
		{?>
	 	if (document.getElementById('perimeterList')[selIndex].value == '<?php echo $row->id; ?>'){
	 			selectWFSPerimeter(document.getElementById('perimeterList')[selIndex].value,"<?php echo $row->perimeter_name; ?>","<?php echo $row->wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>","<?php echo $row->area_field_name; ?>","<?php echo $row->wms_url; ?>","<?php echo $row->layer_name; ?>");	 		
	 		}
	 
	 <?php } ?>
      }
    </script>   
    
    
    
         
	<select id="perimeterList"  onChange="selectPerimeter()">
	<option value="-1"><?php echo JText::_("SELECT_THE_PERIMETER"); ?></option>
	<?php
	foreach ($rows as $row)
		{			
			?>
			<option value ="<?php echo $row->id ?>"> <?php echo JText::_($row->perimeter_name); ?> </option>
			<?php 
				  		
		}
		?>
		</select><br>
	
	
		
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
			echo JText::_("SURFACE_MIN")." ".$surfaceMin->surface_min." ".JText::_("SURFACE_MIN_UNIT")."<br>";	
		}
		
		$db->setQuery( $querySurfaceMax);
		$rowsSurfaceMax = $db->loadObjectList();
		foreach( $rowsSurfaceMax as $surfaceMax )
		{
			echo JText::_("SURFACE_MAX")." ".$surfaceMax->surface_max." ".JText::_("SURFACE_MAX_UNIT")."<br>";
		}
		
		 
		$totalArea = $mainframe->getUserState('totalArea');
		if (!$totalArea) $totalArea=0;
		
		echo JText::_("SURFACE_SELECTED");?>
		<input type="text" size="10" id="totalSurface" disabled="disabled" value="<?php echo $totalArea; ?>"><br>
		<select multiple="multiple" size="10" id="selectedSurface">
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
		var elSel = document.getElementById('selectedSurface');		
		var i;
			  for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {
			     document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) - parseFloat(elSel.options[i].value);
			      elSel.remove(i);
			    }
			  }
		}
		function addManualPerimeter()
		
		{ 
			alert(vectors.id);
			map.removeLayer(vectors.id);
				 vectors = new OpenLayers.Layer.Vector(
                "Vector Layer",
                {isBaseLayer: false,transparent: "true"}
            );
            
              map.addLayer(vectors);
              
            }
		</script>
		<button class="deletePerimeterButton" type="button" onClick="removeSelected();"><?php echo JText::_("REMOVE_VALUE");?></button>
						
				
						
						
		<button class="addPerimeterButton" type="button" onClick="addManualPerimeter();"><?php echo JText::_("ADD_MANUAL_PERIMETER");?></button>		
		
		<?php
	}
}else{
	
	if ($curstep > 2){
	
	
	$totalArea = $mainframe->getUserState('totalArea');
	if (!$totalArea) $totalArea=0;
	?>
	<div>	
	<fieldset>
	<legend><?php echo $totalArea." ".JText::_("SURFACE_MAX_UNIT") ;?></legend>	
	
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
