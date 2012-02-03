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
global  $mainframe;
$curstep = JRequest::getVar('step',0);

if ($curstep == "2"){
$db =& JFactory::getDBO(); 		


 $query = "SELECT * FROM #__sdi_perimeter where islocalisation = 1 order by ordering";

	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			echo $db->getErrorMsg();
	}
	?>
	
	<script>			
	function selectPerimeterPerimeter(selected){
	<?php
	$query2 = "SELECT * FROM #__sdi_perimeter order by ordering ";
	$db->setQuery( $query2 );
	$rows2 = $db->loadObjectList();
		
	foreach ($rows2 as $row)
		{?>
	 	if ( selected == '<?php echo $row->id; ?>'){
	 			
	 		 	<?php if ($row->filterperimeter_id>0 ){
	 		 		?>
	 		 		selectPerimeterPerimeter ('<?php echo $row->filterperimeter_id?>');
	 		 		<?php 
	 		 	}else{?>
				
					<?php if ($row->maxfeatures =="-1"){
						?>
						var maxfeatures="";
						<?php
					} else{
					?>	
					var maxfeatures="&MAXFEATURES=<?php echo $row->maxfeatures?>";					
					<?php
					}
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
						$proxyhost = $proxyhost."&type=wfs&perimeterdefid=$row->id&url=";
						$wfs_url =  $proxyhost.urlencode  (trim($row->urlwfs));
					?>					
	 				fillSelectPerimeterPerimeter("perimetersListPerimeter<?php echo $row->id; ?>","<?php echo $row->name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->featuretype; ?>","<?php echo $row->fieldname; ?>","<?php echo $row->fieldid; ?>","",<?php echo $row->sort; ?>,maxfeatures);
	 				document.getElementById("perimetersListPerimeter<?php echo $row->id; ?>").style.display='block';
	 				hideParent('<?php echo $row->id; ?>');
	 			<?php } ?>
	 			if (document.getElementById('perimeterblock<?php echo $row->id;?>')!=null)
	 			document.getElementById('perimeterblock<?php echo $row->id;?>').style.display='block';
	 		}else
	 		{
		 		if (document.getElementById('perimeterblock<?php echo $row->id;?>')!=null)
		 		{
		 			document.getElementById('perimeterblock<?php echo $row->id;?>').style.display='none';
		 			
		 		}
	 		}
	 
	 <?php } ?>
      }
      
    
      
    /**
	 In case of unselection in a combobox, disable the display of parent comboboxes already displayed
	 */
 	  function hideParent(curId)
 	  //alert ('hide parent');
      {
      	<?php
      	
		$queryAll = "SELECT * FROM #__sdi_perimeter";
		$db->setQuery( $queryAll );
		$rowsAll = $db->loadObjectList();
		
		foreach ($rowsAll as $rowall)
		{	
			?>
			if(curId == '<?php echo $rowall->filterperimeter_id;?>')
			{
				if(document.getElementById('perimetersListPerimeter<?php echo $rowall->id; ?>') != null)
				{
					document.getElementById('perimetersListPerimeter<?php echo $rowall->id; ?>').style.display = 'none';
				}
		      	if (document.getElementById('peri_filter<?php echo $rowall->id; ?>')!=null )
			 	{
			 		document.getElementById('peri_filter<?php echo $rowall->id; ?>').style.display = 'none';
			 	}
			 	if (document.getElementById('peri_search<?php echo $rowall->id; ?>')!=null )
			 	{
			 		document.getElementById('peri_search<?php echo $rowall->id; ?>').style.display = 'none';
			 	}
				
				hideParent('<?php echo $rowall->id; ?>');
			}
			
			<?php
		}?>
      }
   function fillPerimeterParent(filterId, curId, parId, parentId)
   {
  	 if (document.getElementById(curId)[document.getElementById(curId).selectedIndex].value == "-1")
      	{
      		//Hide the next comboboxes
      		hideParent(curId.substring(23,curId.length));
      		return;
      	}
      
	    //Display the next parent elements
      	document.getElementById(parId).style.display = 'block';
      	if (document.getElementById('peri_filter'+parentId)!=null )
	 	{
	 		document.getElementById('peri_filter'+parentId).style.display = 'none';
	 	}
	 	if (document.getElementById('peri_search'+parentId)!=null )
	 	{
	 		document.getElementById('peri_search'+parentId).style.display = 'none';
	 	}
	 	
	<?php
	$query2 = "SELECT * FROM #__sdi_perimeter order by ordering";
	$db->setQuery( $query2 );
	$rows2 = $db->loadObjectList();
		
	foreach ($rows2 as $row)
		{?>
	 	if ( parId == 'perimetersListPerimeter<?php echo $row->id; ?>'){
	 			var filter ="";
	 			if (document.getElementById(filterId)==null || document.getElementById(filterId).value.length==0){
	 		 		filter =  "FILTER=<Filter><PropertyIsEqualTo><PropertyName><?php echo $row->fieldname ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></Filter>";
	 		 	}else{
					//Only one occurence
	 		 		<?php if($row->allowMultipleSelection == 0) {?>
						filter =  "FILTER=<Filter><And><PropertyIsEqualTo><PropertyName><?php echo $row->fieldsearch ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"</Literal></PropertyIsEqualTo><PropertyIsEqualTo><PropertyName><?php echo $row->fieldfilter ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";	 		 		
					<?php } 
					//several occurences
					else {?>	
						filter =  "FILTER=<Filter><And><PropertyIsLike%20wildCard=\"*\"%20singleChar=\"_\"%20escape=\"!\"><PropertyName><?php echo $row->fieldsearch ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"</Literal></PropertyIsLike><PropertyIsEqualTo><PropertyName><?php echo $row->fieldfilter ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";	 		 		
					<?php } ?>
				}
	 		 	<?php /*}*/
	 		 	
	 		 	
	 		 	if ($row->maxfeatures!="-1") {?>
	 		 	 var maxfeatures = "&MAXFEATURES=<?php echo$row->maxfeatures ?>";
	 		 	<?php }else{?>
	 		 		var maxfeatures="";
	 		 		<?php
	 		 	} 
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
						$proxyhost = $proxyhost."&type=wfs&perimeterdefid=$row->id&url=";
						$wfs_url =  $proxyhost.urlencode  (trim($row->urlwfs));
	 		 	?>
	 		fillSelectPerimeterPerimeter("perimetersListPerimeter<?php echo $row->id; ?>","<?php echo $row->name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->featuretype; ?>","<?php echo $row->fieldname; ?>","<?php echo $row->fieldid; ?>",filter,<?php echo $row->sort; ?>,maxfeatures);	 				 				 			
	 		}
	 
	 <?php } ?>
      }

    </script>   
        <table>
        
        <tr>
        <td>         
	<select id="perimeterListPerimeter" style='display:none' disabled="disabled" onChange="selectPerimeterPerimeter(document.getElementById('perimeterListPerimeter')[document.getElementById('perimeterListPerimeter').selectedIndex].value)">
	<option value =""> </option>
	<?php
	foreach ($rows as $row)
		{			
			?>
			<option value ="<?php echo $row->id ?>"  > <?php echo JText::_($row->name); ?> </option>
			<?php 				  		
		}
		?>
		</select>
		</td></tr>
		
		</table>


<?php
	foreach ($rows as $row)
	{ 			
			echo "<div id=\"perimeterblock$row->id\" style=\"display:none\" ><table>";			
				HTML_perimeterBuilder::generateHtmlPerimeterSelect($row,0);
			echo "</table></div>";	
			
		}			
			?>
		
	<br>		
				  
<script>	

	/**
	*/
	function sortList(mylist) 
	{
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

	/**
	*/
	var wfs4;
	var perimeter_id_field;
	var loadingPerimeter = false;
	function freeSelectPerimeterPerimeter(perimetersListPerimeterId)
	{
			var elSel = document.getElementById(perimetersListPerimeterId);
			while (elSel.length > 0)
			{
				elSel.remove(elSel.length - 1);
			}
	}
      	
	/**
	*/
	function fillSelectPerimeterPerimeter(perimetersListPerimeterId,perimeter_perimeter_name,perimeter_wfs_url,perimeter_feature_type_name,perimeter_name_field_name,perimeter_id_field_name ,filter,isSort,maxfeatures,user,password)
	{		
		var elSel = document.getElementById(perimetersListPerimeterId);
		freeSelectPerimeterPerimeter(perimetersListPerimeterId);
		
		elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("SHOP_PERIMETER_MANUAL_LOADING");?>","");
		loadingPerimeter=true;
		
		perimeter_id_field = perimeter_id_field_name; 
		
		var wfsUrlWithBBox = perimeter_wfs_url+'?request=GetFeature&SERVICE=WFS&TYPENAME='+perimeter_feature_type_name+'&VERSION=1.0.0' ;
		if (filter.length > 0) wfsUrlWithBBox = wfsUrlWithBBox +"&"+filter;	
		else wfsUrlWithBBox = wfsUrlWithBBox + "&BBOX="+map.maxExtent.toBBOX();
		wfsUrlWithBBox = wfsUrlWithBBox+maxfeatures;
		wfs4 = new OpenLayers.Layer.Vector("selectedFeatures", {
                    strategies: [new OpenLayers.Strategy.Fixed()],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: wfsUrlWithBBox,
                        format: new OpenLayers.Format.GML()                        
                    })
                });
		var elSel = document.getElementById(perimetersListPerimeterId);
		wfs4.events.register("loadend", null, function(event) {
			if(wfs4.features.length == 0)
			{
				elSel.remove(0);
				elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("SHOP_PERIMETER_MANUAL_NO_FEATURE");?>","");
				loadingPerimeter=false;
			}
		});
		
		wfs4.events.register("featuresadded", null, function(event) {
		if (loadingPerimeter==true){
				elSel.remove(0);
				elSel.options[0] =  new Option("- "+perimeter_perimeter_name+" -","-1");
				loadingPerimeter=false;
		}
		
		var perim = document.getElementById(perimetersListPerimeterId);
		for(var k=0; k<event.features.length; k++){
			var feat2 = event.features[k];
			var id = feat2.attributes[perimeter_id_field_name];
			var name = feat2.attributes[perimeter_name_field_name];		
			perim.options[perim.options.length] =  new Option(name,id);
		}
		
		if (isSort == 1) {
			sortList(perimetersListPerimeterId);
		}
		map.removeLayer(wfs4);
		
		
		//If only one occurence (title + choice = 2), then autoselect it directly and trigger the onchange event
		//recenterOnPerimeterPerimeter
		if(perim.length == 2){
			perim.options[1].selected = true;
			recenterOnPerimeterPerimeter(perim.id);
		}
		
              });              
             map.addLayer(wfs4);
            }
            
	function recenterOnPerimeterPerimeter(perimetersListPerimeterId)
	{
		var elSel = document.getElementById(perimetersListPerimeterId);
		for (i = elSel.length - 1; i>=0; i--) {
			if (elSel.options[i].selected) 
			{			     	
				var wfsFeatures = wfs4.features;
				var idToLookFor = elSel.options[i].value;
				var found = false;
				for(var j=wfsFeatures.length-1; j>=0; j--) 
				{
					feat2 = wfsFeatures[j];                       
					if (idToLookFor == feat2.attributes[perimeter_id_field]){
						found=true;
						map.zoomToExtent (feat2.geometry.getBounds(),true);
						//Check if the feature is already in the list before add it to the wfs
						var fnd = false;
						for (var l=document.getElementById("selectedSurface").options.length-1;l>=0;l--)
						{
							if (document.getElementById("selectedSurface").options[l].value ==  idToLookFor)
							{
								fnd = true;
								break;
							}
						}
						if(fnd == false){						
							if (!wfs5)
							{
								wfs5 = new OpenLayers.Layer.Vector("selectedFeatures");
								map.addLayer(wfs5);
								var opt = {
									strategies: [new OpenLayers.Strategy.Fixed({preload:false})],
									protocol: new OpenLayers.Protocol.HTTP({                      					 	
                        						format: new OpenLayers.Format.GML()})
								}
								wfsRegisterEvents(wfs5, 0);
							}
							
							wfs5.addFeatures([wfsFeatures[j].clone()])
						}
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
class HTML_perimeterBuilder
{ 
	function generateHtmlPerimeterSelect($row,$parent){
		$db =& JFactory::getDBO();
		if ($row->id_perimeter_filter > 0 )
		{
			$query = "SELECT * FROM #__sdi_perimeter where id = $row->filterperimeter_id";
			$db->setQuery( $query );
			$rows2 = $db->loadObject();
			HTML_perimeterBuilder::generateHtmlPerimeterSelect($rows2,$row->id);

		}
		if ($parent == 0)
		{
			echo "<tr>";
			echo "<td><select style='display:none' 
							  id=\"perimetersListPerimeter$row->id\"	
							  onChange=\"recenterOnPerimeterPerimeter('perimetersListPerimeter$row->id')\">
							  <option > </option></select></td>";

			echo "</tr>";
		}
		else
		{
			if ($row->searchbox == 1) 
			{
				echo "<tr>";
				echo "<td><select  style='display:none' id=\"perimetersListPerimeter$row->id\"	
						onChange=\"hideParent($row->id);
						if (document.getElementById('perimetersListPerimeter$row->id')[document.getElementById('perimetersListPerimeter$row->id').selectedIndex].value != -1)
			      		{
							if (document.getElementById('peri_filter'+$row->id)!=null )
						 	{
						 		document.getElementById('peri_filter'+$row->id).style.display = 'block';
						 	}
						 	if (document.getElementById('peri_search'+$row->id)!=null )
						 	{
						 		document.getElementById('peri_search'+$row->id).style.display = 'block';
						 	}
						}			
						\">
						<option > </option></select></td>";
				echo "</tr>";
				echo "<tr >";
				echo "<td><table><tr><td><input style='display:none' 
												class=\"locFilter\" 
												size=5 
												length=5 
												type=\"text\" 
												id =\"peri_filter$row->id\" 
												value=\"\" ></td><td>"	;
				echo "<button style='display:none' 
								onClick=\"fillPerimeterParent ('peri_filter$row->id','perimetersListPerimeter$row->id','perimetersListPerimeter$parent','$parent') \" 
								id=\"peri_search$row->id\"
								type=\"button\" 
								class=\"searchButton\">".JText::_("SHOP_PERIMETER_SEARCH")."
					 </button></td></tr></table></td>"	;
				echo "</tr>";
			}
			else
			{
				echo "<tr>";
				echo "<td><select style='display:none' id=\"perimetersListPerimeter$row->id\"	
							onChange=\"hideParent($row->id);
							fillPerimeterParent ('peri_filter$row->id','perimetersListPerimeter$row->id','perimetersListPerimeter$parent','$parent') \"><option > </option></select></td>";
				echo "</tr>";
			}
		}

	}
}
?>
