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


 $query = "SELECT * FROM #__easysdi_perimeter_definition where is_localisation = 1 order by ordering";

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
	$query2 = "SELECT * FROM #__easysdi_perimeter_definition order by ordering ";
	$db->setQuery( $query2 );
	$rows2 = $db->loadObjectList();
		
	foreach ($rows2 as $row)
		{?>
	 	if ( selected == '<?php echo $row->id; ?>'){
	 			
	 		 	<?php if ($row->id_perimeter_filter>0 ){
	 		 		?>
	 		 		selectPerimeterPerimeter ('<?php echo $row->id_perimeter_filter?>');
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
					
					
					if (1==1 || ($row->user !=null && strlen($row->user)>0)){
						
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("PROXYHOST");
						$proxyhost = $proxyhost."&type=wfs&perimeterdefid=$row->id&url=";
						$wfs_url =  $proxyhost.urlencode  (trim($row->url));
					}else{
						$wfs_url = $row->wfs_url;						
					}
					
					
					?>					


						 		 	
	 				fillSelectPerimeterPerimeter("perimetersListPerimeter<?php echo $row->id; ?>","<?php echo $row->perimeter_name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>","",<?php echo $row->sort; ?>,maxfeatures);
	 				
	 			<?php } ?>
	 			if (document.getElementById('perimeterblock<?php echo $row->id;?>')!=null)
	 			document.getElementById('perimeterblock<?php echo $row->id;?>').style.display='block';
	 		}else{
	 		if (document.getElementById('perimeterblock<?php echo $row->id;?>')!=null)
	 			document.getElementById('perimeterblock<?php echo $row->id;?>').style.display='none';
	 		}
	 
	 <?php } ?>
      }
      
      function fillPerimeterParent(filterId, curId, parId){


	<?php
	$query2 = "SELECT * FROM #__easysdi_perimeter_definition order by ordering";
	$db->setQuery( $query2 );
	$rows2 = $db->loadObjectList();
		
	foreach ($rows2 as $row)
		{?>
	 	if ( parId == 'perimetersListPerimeter<?php echo $row->id; ?>'){
	 			var filter ="";
	 			  
	 			  	<?php if ($row->searchbox == 0) {
	 			  	?>	 			  	
	 			  	filter =  "FILTER=<Filter><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></Filter>";
	 			  	<?php
	 			  	}else{?>
	 			  	
	 			  	
	 			if (document.getElementById(filterId)==null || document.getElementById(filterId).value.length==0){
	 		 		filter =  "FILTER=<Filter><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></Filter>";
	 		 	}else{
	 		 		filter =  "FILTER=<Filter><And><PropertyIsLike%20wildCard=\"*\"%20singleChar=\"_\"%20escape=\"!\"><PropertyName><?php echo $row->name_field_name ?></PropertyName><Literal>"+ document.getElementById(filterId).value+"</Literal></PropertyIsLike><PropertyIsEqualTo><PropertyName><?php echo $row->filter_field_name ?></PropertyName><Literal>"+ document.getElementById(curId).value+"</Literal></PropertyIsEqualTo></And></Filter>";	 		 		
	 		 	}
	 		 	<?php }
	 		 	
	 		 	
	 		 	if ($row->maxfeatures!="-1") {?>
	 		 	 var maxfeatures = "&MAXFEATURES=<?php echo$row->maxfeatures ?>";
	 		 	<?php }else{?>
	 		 		var maxfeatures="";
	 		 		<?php
	 		 	} 
	 		 	
	 		 	if (1== 1 || ($row->user !=null && strlen($row->user)>0)){
						
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("PROXYHOST");
						$proxyhost = $proxyhost."&type=wfs&perimeterdefid=$row->id&url=";
						$wfs_url =  $proxyhost.urlencode  (trim($row->url));
					}else{
						$wfs_url = $row->wfs_url;						
					}
	 		 	
	 		 	?>
	 		 	
	 		fillSelectPerimeterPerimeter("perimetersListPerimeter<?php echo $row->id; ?>","<?php echo $row->perimeter_name; ?>","<?php echo $wfs_url; ?>","<?php echo $row->feature_type_name; ?>","<?php echo $row->name_field_name; ?>","<?php echo $row->id_field_name; ?>",filter,<?php echo $row->sort; ?>,maxfeatures);	 				 				 			
	 		}
	 
	 <?php } ?>
      }

    </script>   
        <table>
        
        <tr>
        <td>         
	<select id="perimeterListPerimeter"  disabled="disabled" onChange="selectPerimeterPerimeter(document.getElementById('perimeterListPerimeter')[document.getElementById('perimeterListPerimeter').selectedIndex].value)">
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
			echo "<div id=\"perimeterblock$row->id\" style=\"display:none\" ><table>";			
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
	var perimeter_id_field;
	var loadingPerimeter = false;
	function freeSelectPerimeterPerimeter(perimetersListPerimeterId){
			var elSel = document.getElementById(perimetersListPerimeterId);
			while (elSel.length > 0)
				{
					elSel.remove(elSel.length - 1);
			}
		}
      	
		function fillSelectPerimeterPerimeter(perimetersListPerimeterId,perimeter_perimeter_name,perimeter_wfs_url,perimeter_feature_type_name,perimeter_name_field_name,perimeter_id_field_name ,filter,isSort,maxfeatures,user,password){		
		
		var elSel = document.getElementById(perimetersListPerimeterId);
		freeSelectPerimeterPerimeter(perimetersListPerimeterId);
		
		elSel.options[elSel.options.length] =  new Option("<?php echo JText::_("EASYSDI_LOADING_MANUAL_PERIMETER");?>","");
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
		wfs4.events.register("featureadded", null, function(event) {
		var elSel = document.getElementById(perimetersListPerimeterId);
		//if (elSel.options[0].value==""){
		if (loadingPerimeter==true){
				
				elSel.remove(0);
				elSel.options[0] =  new Option("","");
				loadingPerimeter=false;
		}
				
		var feat2 = event.feature;
		
		var perim = document.getElementById(perimetersListPerimeterId);
		var id = feat2.attributes[perimeter_id_field_name];
		var name = feat2.attributes[perimeter_name_field_name];		
		perim.options[perim.options.length] =  new Option(name,id);
		if (isSort == 1) {
			sortList(perimetersListPerimeterId);
		}
              });              
             map.addLayer(wfs4);
              map.removeLayer(wfs4);                  
            }
            
            
function recenterOnPerimeterPerimeter(perimetersListPerimeterId){
		
		var elSel = document.getElementById(perimetersListPerimeterId);
			  for (i = elSel.length - 1; i>=0; i--) {
			    if (elSel.options[i].selected) {			     	
                    var wfsFeatures = wfs4.features;
					var idToLookFor = elSel.options[i].value;
					var found = false;
					for(var j=wfsFeatures.length-1; j>=0; j--) {
                    	feat2 = wfsFeatures[j];                       
                      
                       if (idToLookFor == feat2.attributes[perimeter_id_field]){
                       		found=true;
							map.zoomToExtent (feat2.geometry.getBounds(),true);
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
                       		     		wfs.addFeatures([wfsFeatures[j]])
                       		     		
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
