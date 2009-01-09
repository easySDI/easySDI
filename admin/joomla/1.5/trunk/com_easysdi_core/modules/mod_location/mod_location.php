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

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'common.easysdi.php');

global  $mainframe;
$curstep = JRequest::getVar('step',0);

if ($curstep == "2"){
$db =& JFactory::getDBO(); 		

	$query = "select count(*)  from #__easysdi_product where is_localisation = 1 ";		
	$db->setQuery( $query );
	$nbProduct = $db->loadResult();
		
	$query = "SELECT * FROM #__easysdi_perimeter_definition where is_localisation = 1 ";

	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			echo $db->getErrorMsg();
	}
	?>
	
	<script>			
	function selectPerimeterLocation(selected){
	
	//selIndex = document.getElementById('perimeterListLocation').selectedIndex;
	//document.getElementById('perimeterListLocation')[selIndex].value
	<?php
	$query2 = "SELECT * FROM #__easysdi_perimeter_definition ";
	$db->setQuery( $query2 );
	$rows2 = $db->loadObjectList();
		
	foreach ($rows2 as $row)
		{?>
	 	if ( selected == '<?php echo $row->id; ?>'){
	 		 	<?php if ($row->id_perimeter_filter>0 ){
	 		 		?>
	 		 		selectPerimeterLocation ('<?php echo $row->id_perimeter_filter?>');
	 		 		<?php 
	 		 	}else{?>
	 				fillSelectPerimeterLocation("perimetersListLocation<?php echo $row->id; ?>","<?php echo $row->perimeter_name; ?>","<?php echo $row->wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>","");
	 			<?php } ?>
	 			if (document.getElementById('locationblock<?php echo $row->id;?>')!=null)
	 			document.getElementById('locationblock<?php echo $row->id;?>').style.display='block';
	 		}else{
	 		if (document.getElementById('locationblock<?php echo $row->id;?>')!=null)
	 			document.getElementById('locationblock<?php echo $row->id;?>').style.display='none';
	 		}
	 
	 <?php } ?>
      }
      
      function fillParent(filterId, curId, parId){


	<?php
	$query2 = "SELECT * FROM #__easysdi_perimeter_definition ";
	$db->setQuery( $query2 );
	$rows2 = $db->loadObjectList();
		
	foreach ($rows2 as $row)
		{?>
	 	if ( parId == 'perimetersListLocation<?php echo $row->id; ?>'){
	 			var filter ="";
	 			  
	 			if (document.getElementById(filterId).value.length==0){
	 		 		filter =  "<Filter><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></Filter>&MAXFEATURES=50";
	 		 	}else{
	 		 		filter =  "<Filter><And><PropertyIsLike wildCard=\"%25\" singleChar=\"\" escapeChar=\"!\"><PropertyName><?php echo $row->name_field_name ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"*</Literal></PropertyIsLike><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";
	 		 		//filter =  "<Filter><And><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>&MAXFEATURES=50";
	 		 	}
	 		 	
	 		fillSelectPerimeterLocation("perimetersListLocation<?php echo $row->id; ?>","<?php echo $row->perimeter_name; ?>","<?php echo $row->wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>",filter);	 				 				 			
	 		}
	 
	 <?php } ?>
      }

    </script>   
        <table>
        
        <tr>
        <td>         
	<select id="perimeterListLocation"  onChange="selectPerimeterLocation(document.getElementById('perimeterListLocation')[document.getElementById('perimeterListLocation').selectedIndex].value)">
	<option value =""> </option>
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
		
		</table>


<?php
	foreach ($rows as $row)
	{ 			
			echo "<div id=\"locationblock$row->id\" style=\"display:none\" ><table>";			
				helper_easysdi::generateHtmlPerimeterSelect($row,0);
			echo "</table></div>";	
			
		}			
			?>
		
	<br>		
				  
<script>	


function sortList(mylist) {
	var lb = document.getElementById(mylist);
	var arrTexts = new Array();
	var arrValues = new Array();
	var arrOldTexts = new Array();

	for(i=0; i<lb.length; i++) {
		arrTexts[i] = lb.options[i].text;
		arrValues[i] = lb.options[i].value;	
		arrOldTexts[i] = lb.options[i].text;
	}

	arrTexts.sort();

	for(i=0; i<lb.length; i++){
		lb.options[i].text = arrTexts[i];
		for(j=0; j<lb.length; j++){
			if (arrTexts[i] == arrOldTexts[j]){
				lb.options[i].value = arrValues[j];
				j = lb.length;
			}
		}
	}
}


	var wfs4;
	var location_id_field;
	
	function freeSelectPerimeterLocation(perimetersListLocationId){
			var elSel = document.getElementById(perimetersListLocationId);
			while (elSel.length > 0)
				{
					elSel.remove(elSel.length - 1);
			}
		}
      	
		function fillSelectPerimeterLocation(perimetersListLocationId,location_perimeter_name,location_wfs_url,location_feature_type_name,location_name_field_name,location_id_field_name ,filter){		
		
		var elSel = document.getElementById(perimetersListLocationId);
		freeSelectPerimeterLocation(perimetersListLocationId);
		
		elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("EASYSDI_LOADING_MANUAL_PERIMETER");?>","");
		
		
		
		
		location_id_field = location_id_field_name; 
		
		var wfsUrlWithBBox = location_wfs_url+'?request=GetFeature&SERVICE=WFS&TYPENAME='+location_feature_type_name+'&VERSION=1.0.0' ;
		if (filter.length > 0) wfsUrlWithBBox = wfsUrlWithBBox +"&FILTER="+filter;
		else wfsUrlWithBBox = wfsUrlWithBBox + "&BBOX="+map.maxExtent.toBBOX();
		wfsUrlWithBBox = wfsUrlWithBBox;
		wfs4 = new OpenLayers.Layer.Vector("selectedFeatures", {
                    strategies: [new OpenLayers.Strategy.Fixed()],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: wfsUrlWithBBox,
                        format: new OpenLayers.Format.GML()                        
                    })
                });		    	            
		wfs4.events.register("featureadded", null, function(event) {
		var elSel = document.getElementById(perimetersListLocationId);
		if (elSel.options[0].value==""){
				
				elSel.remove(0);
		}
				
		var feat2 = event.feature;
		
		var perim = document.getElementById(perimetersListLocationId);
		var id = feat2.attributes[location_id_field_name];
		var name = feat2.attributes[location_name_field_name];		
		perim.options[perim.options.length] =  new Option(name,id);
		sortList(perimetersListLocationId)
              });              
             map.addLayer(wfs4);
              map.removeLayer(wfs4);                  
            }
            
            
function recenterOnPerimeterLocation(perimetersListLocationId){
		
		var elSel = document.getElementById(perimetersListLocationId);
			  for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {			     	
                    var wfsFeatures = wfs4.features;
					var idToLookFor = elSel.options[i].value;
					var found = false;
					for(var j=wfsFeatures.length-1; j>=0; j--) {
                    	feat2 = wfsFeatures[j];                       
                      
                       if (idToLookFor == feat2.attributes[location_id_field]){
                       		found=true;
							map.zoomToExtent (feat2.geometry.getBounds(),true);

                       		break;
                       }
                       }                       
                       break;
                       }                       
			     	}		  	       		
		}				
		</script>				
		<?php	
}
?>
