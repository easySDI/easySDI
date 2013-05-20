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

if ($curstep == "2")
{
	require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'core'.DS.'common.easysdi.php');
	
	$db =& JFactory::getDBO(); 		

	$query = "SELECT * FROM #__easysdi_location_definition where is_localisation = 1 ";

	$db->setQuery( $query );
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			echo $db->getErrorMsg();
	}
	
	?>
	
	<script>	
	
	/**
	On selection in the main combobox of the location module :
	Build the wfs url and call the fillSelectLocationLocation function
	*/		
	function selectLocationLocation(selected)
	{
		//selIndex = document.getElementById('locationListLocation').selectedIndex;
		//document.getElementById('locationListLocation')[selIndex].value
		<?php
		$query2 = "SELECT * FROM #__easysdi_location_definition ";
		$db->setQuery( $query2 );
		$rows2 = $db->loadObjectList();
			
		foreach ($rows2 as $row)
		{?>
	 		if ( selected == '<?php echo $row->id; ?>')
	 		{
	 		 	<?php 
	 		 	if ($row->id_location_filter>0 )
	 		 	{
	 		 		?>
	 		 		selectLocationLocation ('<?php echo $row->id_location_filter?>');
	 		 		<?php 
	 		 	}else
	 		 	{?>
					<?php 
					if ($row->maxfeatures =="-1")
					{
						?>
						var maxfeatures="";
						<?php
					} 
					else
					{
						?>	
						var maxfeatures="&MAXFEATURES=<?php echo $row->maxfeatures?>";
						<?php
					}
					
					//if (2 == 2 || ($row->user !=null && strlen($row->user)>0))
					/*if ($row->user != null && strlen($row->user)>0)
					{
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("PROXYHOST");
						$proxyhost = $proxyhost."&type=wfs&locationid=$row->id&url=";
						$wfs_url =  $proxyhost.urlencode  (trim($row->wfs_url));
					}
					else
					{
						$wfs_url = $row->wfs_url;						
					}*/
					$proxyhost = config_easysdi::getValue("PROXYHOST");
					$proxyhost = $proxyhost."&type=wfs&locationid=$row->id&url=";
					$wfs_url =  $proxyhost.urlencode  (trim($row->wfs_url));
					?>
	 				fillSelectLocationLocation("locationsListLocation<?php echo $row->id; ?>","<?php echo $row->location_name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>","",<?php echo $row->sort; ?>,maxfeatures);
	 				document.getElementById("locationsListLocation<?php echo $row->id; ?>").style.display='block';
	 				hideLocationParent(<?php echo $row->id; ?>);
	 				
	 			<?php 
				} 
				?>
	 			if (document.getElementById('locationblock<?php echo $row->id;?>')!=null)
	 			document.getElementById('locationblock<?php echo $row->id;?>').style.display='block';
	 		}
	 		else
	 		{
	 			if (document.getElementById('locationblock<?php echo $row->id;?>')!=null)
	 			{
	 				document.getElementById('locationblock<?php echo $row->id;?>').style.display='none';
	 			}
	 		}
		 
		 <?php 
		} 
		?>
      }
 
  

	 /**
	 In case of unselection in a combobox, disable the display of parent comboboxes already displayed
	 */
 	  function hideLocationParent(curId)
      {
      	//$("status").innerHTML = 'start'; 
      	
      	//Disable the display of the filter and search element corresponding to the unselected combobox
      	
		<?php
      	
		$queryAll = "SELECT * FROM #__easysdi_location_definition";
		$db->setQuery( $queryAll );
		$rowsAll = $db->loadObjectList();
		
		foreach ($rowsAll as $rowall)
		{	
			?>
			
			if(curId == '<?php echo $rowall->id_location_filter;?>')
			{
				if (document.getElementById('filter<?php echo $rowall->id_location_filter;?>')!=null )
			 	{
			 		document.getElementById('filter<?php echo $rowall->id_location_filter;?>').style.display = 'none';
			 	}
			 	if (document.getElementById('search<?php echo $rowall->id_location_filter;?>')!=null )
			 	{
			 		document.getElementById('search<?php echo $rowall->id_location_filter;?>').style.display = 'none';
			 	}
				if(document.getElementById('locationsListLocation<?php echo $rowall->id; ?>') != null)
				{
					document.getElementById('locationsListLocation<?php echo $rowall->id; ?>').style.display = 'none';
				}
		      	if (document.getElementById('filter<?php echo $rowall->id; ?>')!=null )
			 	{
			 		document.getElementById('filter<?php echo $rowall->id; ?>').style.display = 'none';
			 	}
			 	if (document.getElementById('search<?php echo $rowall->id; ?>')!=null )
			 	{
			 		document.getElementById('search<?php echo $rowall->id; ?>').style.display = 'none';
			 	}
				
				hideLocationParent('<?php echo $rowall->id; ?>');
			}
			
			<?php
		}?>
		//$("status").innerHTML = 'start + end'; 
      }
      
      /**
      After selection in a combobox, fill the parent
      */
      function fillParent(filterId, curId, parId, parentId)
      {
      	if (document.getElementById(curId)[document.getElementById(curId).selectedIndex].value == "-1")
      	{
      		//Hide the next comboboxes
      		hideLocationParent(curId.substring(21,curId.length));
      		//$("status").innerHTML = curId.substring(21,curId.length); 
      		return;
      	}
      
	    //Display the next parent elements
      	document.getElementById(parId).style.display = 'block';
      	if (document.getElementById('filter'+parentId)!=null )
	 	{
	 		document.getElementById('filter'+parentId).style.display = 'none';
	 	}
	 	if (document.getElementById('search'+parentId)!=null )
	 	{
	 		document.getElementById('search'+parentId).style.display = 'none';
	 	}
	 	
		<?php
		$query2 = "SELECT * FROM #__easysdi_location_definition ";
		$db->setQuery( $query2 );
		$rows2 = $db->loadObjectList();
		
		foreach ($rows2 as $row)
		{?>
	 		if ( parId == 'locationsListLocation<?php echo $row->id; ?>')
	 		{
	 			var filter ="";
	 			<?php 
	 			/*	if ($row->searchbox == 0) 
	 			{
	 			  	?>	 			  	
	 			  	filter =  "FILTER=<Filter><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></Filter>";
	 			  	<?php
	 			}
	 			else
	 			{*/
	 			?>
	 				if (document.getElementById(filterId)!=null && document.getElementById(filterId).value.length>0)
	 				{
	 					//filter =  "FILTER=<Filter><And><PropertyIsLike%20wildCard=\"*\"%20singleChar=\"_\"%20escape=\"!\"><PropertyName><?php echo $row->name_field_name ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"</Literal></PropertyIsLike><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";	 		 		
						//Only one occurence
	 		 			<?php if($row->allowMultipleSelection == 0) {?>
							filter =  "FILTER=<Filter><And><PropertyIsEqualTo><PropertyName><?php echo $row->name_field_name ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"</Literal></PropertyIsEqualTo><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";	 		 		
						<?php } 
						//several occurences
						else {?>	
							filter =  "FILTER=<Filter><And><PropertyIsLike%20wildCard=\"*\"%20singleChar=\"_\"%20escape=\"!\"><PropertyName><?php echo $row->name_field_name ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"</Literal></PropertyIsLike><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";	 		 		
						<?php } ?>
					}
		 		 	else
		 		 	{
		 		 		filter =  "FILTER=<Filter><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></Filter>";
		 		 	}
		 		 	
	 		 	<?php 
			/*	}*/
	 		 	
	 		 	if ($row->maxfeatures!="-1") 
	 		 	{?>
	 		 		var maxfeatures = "&MAXFEATURES=<?php echo$row->maxfeatures ?>";
	 		 	<?php 
				}
				else
				{?>
	 		 		var maxfeatures="";
	 		 		<?php
	 		 	}
	 		 	//if (1==1 && ($row->user !=null && strlen($row->user)>0))
	 		 	/*if ($row->user!= null && strlen($row->user)>0)
	 		 	{
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("PROXYHOST");
						$proxyhost = $proxyhost."&type=wfs&locationid=$row->id&url=";
						$wfs_url =  $proxyhost.urlencode  (trim($row->wfs_url));
				}
				else
				{
					$wfs_url = $row->wfs_url;						
				}*/
				$proxyhost = config_easysdi::getValue("PROXYHOST");
				$proxyhost = $proxyhost."&type=wfs&locationid=$row->id&url=";
				$wfs_url =  $proxyhost.urlencode  (trim($row->wfs_url));
				
	 		 	 ?>
	 		 	
	 			fillSelectLocationLocation("locationsListLocation<?php echo $row->id; ?>","<?php echo $row->location_name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>",filter,<?php echo $row->sort; ?>,maxfeatures);	 				 				 			
	 		}
		 
		 <?php 
		 } 
		 ?>
      }

    </script>   
    <table>
    <tr>
    <td>         
	<select id="locationListLocation"  onChange="selectLocationLocation(document.getElementById('locationListLocation')[document.getElementById('locationListLocation').selectedIndex].value)">
	<option value =""> </option>
	<?php
		foreach ($rows as $row)
		{			
			?>
			<option value ="<?php echo $row->id ?>"> <?php echo JText::_($row->location_name); ?> </option>
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
				helper_easysdi::generateHtmlLocationSelect($row,0);
			echo "</table></div>";	
	}			
			?>
		
	<br>		
				  
<script>	


	/**
	Sortlist
	*/
	function sortList(mylist) 
	{
		var lb = document.getElementById(mylist);
		var arrTexts = new Array();
		var arrValues = new Array();
		var arrOldTexts = new Array();
	
		for(i=0; i<lb.length; i++) 
		{
			arrTexts[i] = lb.options[i].text;
			arrValues[i] = lb.options[i].value;	
			arrOldTexts[i] = lb.options[i].text;
		}
	
		arrTexts.sort();
	
		for(i=0; i<lb.length; i++)
		{
			lb.options[i].text = arrTexts[i];
			for(j=0; j<lb.length; j++)
			{
				if (arrTexts[i] == arrOldTexts[j])
				{
					lb.options[i].value = arrValues[j];
					j = lb.length;
				}
			}
		}
	}


	/**
	freeSelectLocationLocation
	*/
	var wfs4;
	var location_id_field;
	var loadingLocation = false;
	function freeSelectLocationLocation(locationsListLocationId)
	{
		var elSel = document.getElementById(locationsListLocationId);
		while (elSel.length > 0)
		{
			elSel.remove(elSel.length - 1);
		}
	}
    
    /**
    fillSelectLocationLocation
    */
	function fillSelectLocationLocation(locationsListLocationId,location_location_name,location_wfs_url,location_feature_type_name,location_name_field_name,location_id_field_name ,filter,isSort,maxfeatures)
	{		
		var elSel = document.getElementById(locationsListLocationId);
		
		freeSelectLocationLocation(locationsListLocationId);
		elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("EASYSDI_LOADING_MANUAL_PERIMETER");?>","");
		loadingLocation=true;
		location_id_field = location_id_field_name; 
		var wfsUrlWithBBox = location_wfs_url+'?request=GetFeature&SERVICE=WFS&TYPENAME='+location_feature_type_name+'&VERSION=1.0.0' ;
	
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
		var elSel = document.getElementById(locationsListLocationId);
		wfs4.events.register("loadend", null, function(event) {
			if(wfs4.features.length == 0)
			{
				elSel.remove(0);
				elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("EASYSDI_MANUAL_PERIMETER_NO_FEATURE");?>","");
				loadingPerimeter=false;
			}
		});
		wfs4.events.register("featuresadded", null, function(event) {
							//if (elSel.options[0].value==""){
							if (loadingLocation==true)
							{
								elSel.remove(0);
								elSel.options[0] =  new Option("- "+location_location_name+" -","-1");
								loadingLocation=false;
							}
									
							for(var k=0; k<event.features.length; k++){
								var feat2 = event.features[k];
								var perim = document.getElementById(locationsListLocationId);
								var id = feat2.attributes[location_id_field_name];
								var name = feat2.attributes[location_name_field_name];	
								perim.options[perim.options.length] =  new Option(name,id);
							}
							
							if (isSort == 1) 
							{
								sortList(locationsListLocationId);
							}
							
							map.removeLayer(wfs4);
							
							
							//If only one occurence (title + choice = 2), then autoselect it directly and trigger the onchange event
							//recenterOnLocationLocation
							if(perim.length == 2){
								perim.options[1].selected = true;
								recenterOnLocationLocation(perim.id);
							}
							
					              });              
		map.addLayer(wfs4);
		                
	}
            
//    function utf8_encode ( argString ) {
    // http://kevin.vanzonneveld.net
    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: sowberry
    // +    tweaked by: Jack
    // +   bugfixed by: Onno Marsman
    // +   improved by: Yves Sucaet
    // +   bugfixed by: Onno Marsman
    // *     example 1: utf8_encode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'
 
  /*  var string = (argString+'').replace(/\r\n/g, "\n").replace(/\r/g, "\n");
 
    /*var utftext = "";
    var start, end;
    var stringl = 0;
 
    start = end = 0;
    stringl = string.length;
    for (var n = 0; n < stringl; n++) {
        var c1 = string.charCodeAt(n);
        var enc = null;
 
        if (c1 < 128) {
            end++;
        } else if((c1 > 127) && (c1 < 2048)) {
            enc = String.fromCharCode((c1 >> 6) | 192) + String.fromCharCode((c1 & 63) | 128);
        } else {
            enc = String.fromCharCode((c1 >> 12) | 224) + String.fromCharCode(((c1 >> 6) & 63) | 128) + String.fromCharCode((c1 & 63) | 128);
        }
        if (enc !== null) {
            if (end > start) {
                utftext += string.substring(start, end);
            }
            utftext += enc;
            start = end = n+1;
        }
    }
 
    if (end > start) {
        utftext += string.substring(start, string.length);
    }
 
    return utftext;
}*/
    /** 
    recenterOnLocationLocation
    */
	function recenterOnLocationLocation(locationsListLocationId)
	{
		var elSel = document.getElementById(locationsListLocationId);
		for (i = elSel.length - 1; i>=0; i--) 
		{
		    if (elSel.options[i].selected) 
		    {			     	
			var wfsFeatures = wfs4.features;
			var idToLookFor = elSel.options[i].value;
			var found = false;
			for(var j=wfsFeatures.length-1; j>=0; j--) 
			{
				feat2 = wfsFeatures[j];                       
				if (idToLookFor == feat2.attributes[location_id_field])
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
		</script>
		<?php	
}

?>
