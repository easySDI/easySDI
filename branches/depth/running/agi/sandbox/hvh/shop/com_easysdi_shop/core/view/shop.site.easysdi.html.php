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
class HTML_shop
{
	function searchProducts ($suppliers,$account, $account_id, $user,$rows,$public,$countMD,$total, $limitstart, $limit,$option,$task,$view, $step)
	{
		
		$db =& JFactory::getDBO();
		$previewtype 	= config_easysdi::getValue("CATALOG_METADATA_PREVIEW_TYPE_PUBLIC");
		$previewcontext = config_easysdi::getValue("CATALOG_METADATA_PREVIEW_CONTEXT_PUBLIC");
		?>
		<div class="contentin">
		<script>
		 function submitOrderForm(){
		 	document.getElementById('orderForm').submit();
		 }
		 
		 function addOrder(cid, id){
		 	var form = document.getElementById('orderForm');
			var elem = document.createElement('input');
			elem.setAttribute('type', 'hidden');
			elem.setAttribute('name', 'cid[]');
			elem.setAttribute('value', cid);
			form.appendChild(elem);
			form.submit();
		 }
		 </script>
		
		<form name="orderForm" id="orderForm" 	action='<?php echo JRoute::_("index.php") ?>' method='GET'>
		<h3><?php echo JText::_("SHOP_SEARCH_CRITERIA_TITLE"); ?></h3>
		<script  type="text/javascript">
			window.addEvent('domready', function() {
				//Handler for the clear button
				$('easysdi_clear_button').addEvent('click', function() {
					$('freetextcriteria').value='';
					$('partner_id').value = '';
					$('filter_visible').checked = false;
					$('update_select').value = 'equal';
					$('update_cal').value = '';
					$('orderForm').submit();
				});
			});
		</script>
		<table width="100%" class="mdCatContent">
			<tr>
				<td align="left"><b><?php echo JText::_("SHOP_SHOP_FILTER_TITLE");?></b>&nbsp;
				<td align="left"><input type="text" id="freetextcriteria"  name="freetextcriteria" value="<?php echo JRequest::getVar('freetextcriteria'); ?>" class="inputbox" /></td>
				<td class="catalog_controls">
					<button type="submit" class="easysdi_search_button">
						<?php echo JText::_("SHOP_SEARCH_BUTTON"); ?></button>&nbsp;
					<button id="easysdi_clear_button" class="easysdi_clear_button" type="submit">
						<?php echo JText::_("SHOP_CLEAR_BUTTON"); ?></button>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_("SHOP_SHOP_FILTER_SUPPLIER");?></td>
				<td><?php echo JHTML::_("select.genericlist", $suppliers, 'partner_id', 'size="1" class="inputbox" ', 'value', 'text', $account_id); ?></td>
				<td>&nbsp;</td>		
			</tr>
			<tr>
				<td><?php echo JText::_("SHOP_SHOP_FILTER_VISIBLE");?></td>
				<td><input type="checkbox" id="filter_visible" name="filter_visible" <?php if (JRequest::getVar('filter_visible')) echo " checked"; ?> class="inputbox" /></td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><?php echo JText::_("SHOP_SHOP_FILTER_UPDATE");?></td>
				<td>
					<select id="update_select" size="1" name="update_select">
						<option value="equal" <?php if(JRequest::getVar('update_select')=="equal") echo "SELECTED"; ?>><?php echo JText::_("SHOP_SHOP_FILTER_DATE_EQUAL");?></option>
						<option value="smallerorequal" <?php if(JRequest::getVar('update_select')=="smallerorequal") echo "SELECTED"; ?>><?php echo JText::_("SHOP_SHOP_FILTER_DATE_BEFORE");?></option>
						<option value="greaterorequal" <?php if(JRequest::getVar('update_select')=="greaterorequal") echo "SELECTED"; ?>><?php echo JText::_("SHOP_SHOP_FILTER_DATE_AFTER");?></option>
						<option value="different" <?php if(JRequest::getVar('update_select')=="different") echo "SELECTED"; ?>><?php echo JText::_("SHOP_SHOP_FILTER_DATE_NOTEQUAL");?></option>
					</select>
				
					<?php echo JHTML::_('calendar',JRequest::getVar('update_cal'), "update_cal","update_cal","%d.%m.%Y"); ?>
				</td>
			</tr>
		</table>
		
		<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
		<table width="100%">
		   <tr>
			<td colspan="3">&nbsp;</td>
		   </tr>
		   <tr>
			<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
			<td align="center"><?php echo JText::_("SHOP_SHOP_DISPLAY"); ?> <?php echo $pageNav->getLimitBox(); ?></td>
			<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
		   </tr>
		</table>
		<h3><?php echo JText::_("SHOP_SEARCH_RESULTS_TITLE"); ?></h3>
		<input type='hidden' name='option' value='<?php echo $option;?>'> 
		<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'> 
		<input type='hidden' id="view" name='view' value='<?php echo $view; ?>'> 
		<input type='hidden' id="fromStep" name='fromStep' value='1'> 
		<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'>
		<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>"> 
		<span class="searchCriteria">
		<table width="100%">
		   <tr>
		   	<td colspan="3" align="left"><?php echo JText::_("SHOP_SHOP_NUMBER_OF_PRODUCT_FOUND");?><?php echo $total ?></td>
		   </tr>
		</table>
		<table class="mdsearchresult" width="100%">
			<?php
			$param = array('size'=>array('x'=>800,'y'=>800) );
			JHTML::_("behavior.modal","a.modal",$param);
			$i=0;
			if(count($rows) == 0 && !$user->guest && !userManager::hasRight($account->id,"REQUEST_EXTERNAL")){
				?>
				<tr>
					<td colspan="2">
						<div class="alert">
							<?php echo JText::_("SHOP_SHOP_MESSAGE_NO_ORDER_RIGHTS") ;?>
						</div>
					</td>
				</tr>
				<?php
			}
			foreach ($rows  as $row){
				$account_logo = $row->supplier_logo;
				$logoWidth = config_easysdi::getValue("logo_width");
				$logoHeight = config_easysdi::getValue("logo_height");
				$isMdFree = $row->free;
				$isAvailable = $row->available;
				$isMdPublic = false;
				if($row->visibility_id == $public)
				{
					$isMdPublic = true;
				}
				$query = "select count(*) from #__sdi_product p 
										INNER JOIN #__sdi_objectversion v ON v.id=p.objectversion_id 
										where p.viewurlwms != '' AND v.metadata_id = '".$row->metadata_id."'";
				$db->setQuery( $query);
				$hasPreview = $db->loadResult();
				if ($db->getErrorNum()) {
					$hasPreview = 0;
				}
				
				if($row->pathfile || $row->grid_id)
				{
					$hasProductFile = 1;
				}
				else 
				{
					$query = "select count(*) from #__sdi_product p 
											INNER JOIN #__sdi_product_file pf ON p.id=pf.product_id 
											where  p.id = $row->id";
					$db->setQuery( $query);
					$hasProductFile = $db->loadResult();
					if ($db->getErrorNum()) {
						$hasProductFile = 0;
					}
				}
				$product = new product($db);
				$product->load($row->id);
				$isViewable = $product->isUserAllowedToView($account->id);
				$isLoadable = $product->isUserAllowedToLoad($account->id);
				
				?>
		<tr>
			 <td class="imgHolder" rowspan=3>
			 <img <?php if($logoWidth != "") echo "width=\"$logoWidth px\"";?> <?php if($logoHeight != "") echo "width=\"$logoHeight px\"";?> src="<?php echo $row->supplier_logo;?>" title="<?php echo $row->supplier_name;?>"></img>   
			  </td>
			  <td colspan=3><span class="mdtitle"><?php echo $row->name; ?></span>
			  </td>
			  <td valign="top" rowspan=2>
			    <table id="info_md">
				  <tr>
				     <td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("SHOP_SHOP_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
				  </tr>
				  <tr>
				     <td><div <?php if($isMdFree) echo 'title="'.JText::_("SHOP_SHOP_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
				  </tr>
				  <tr>
				  	<td>&nbsp;</td>
				  </tr>
				</table>
			  </td>
			 </tr>
			 <tr>
			  <td colspan=3><span class="mdsupplier"><?php echo $row->supplier_name;?></span></td>
			 </tr>
		     <tr>
		     	<td class="mdActionViewFile"><span class="mdviewfile">
			  	<a class="modal"
						title="<?php echo JText::_("SHOP_SHOP_VIEW_MD_FILE"); ?>"
						href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&type=<?php echo $previewtype;  ?>&context=<?php echo $previewcontext;  ?>&id=<?php echo $row->metadata_guid;  ?>"
						rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo JText::_("SHOP_SHOP_VIEW_MD_FILE"); ?>
					</a></span>
			  </td>
			  <?php 
			  if($isAvailable  && $hasProductFile>0 && $isLoadable)
			  {
			  	
			  	?>
			  	 <td class="mdActionDownloadProduct"><span class="mdviewfile">
			  	
				<a title="<?php echo JText::_("SHOP_SHOP_DOWNLOAD_AVAILABLE_PRODUCT"); ?>" 
					class="modal" 
					href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=downloadAvailableProduct&cid[]=<?php echo $row->id?>" 
					rel="{handler:'iframe',size:{x:500,y:550}}"> 
				<?php echo JText::_("SHOP_SHOP_DOWNLOAD_AVAILABLE_PRODUCT"); ?>
					</a></span>
			 	 </td>
			  	<?php 
			  }
			  else
			  {
			  	?>
			  	 <td class="mdActionAddToCart"><span class="mdviewfile">
			  	<a title="<?php echo JText::_("SHOP_SHOP_ADD_TO_CART"); ?>"
						href="#" onclick="addOrder(<?php echo $row->id.",".$i; ?>)"><?php echo JText::_("SHOP_SHOP_ADD_TO_CART"); ?>
					</a></span>
			  	</td>
			  	<?php 
			  }
			  ?>
			 
			  <td class="mdActionViewProduct">
			  <?php if ($hasPreview > 0 && $isViewable){ ?>
			    <span class="mdviewproduct">
			    <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_shop&task=previewProduct&metadata_id=<?php echo $row->metadata_id;?>"
			    rel="{handler:'iframe',size:{x:560,y:580}}"><?php echo JText::_("SHOP_SHOP_PREVIEW_PRODUCT"); ?></a></span>
			    <?php } ?>
		      </td>
			  <td class="shopNoAction">&nbsp;</td>
			 </tr>
			 <tr>
			    <td colspan="5" halign="middle"><div class="separator" /></td>
			 </tr>
					<?php
					$i=$i+1;
			}
			?>
			</table>
			<table width="100%">
			   <tr>
				<td colspan="3">&nbsp;</td>
			   </tr>
			   <tr>
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center">&nbsp;</td>
				<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
			   </tr>
			</table>
			<input type="hidden" name="limitstart" value="<?php echo $limitstart;?>">
			<input type="hidden" name="countMD" value="<?php echo $countMD;?>">
		</span></form>
		</div>
	<?php
	}


	function orderPerimeter ($cid, $basemap, $basemap_contents, $option, $task,$step,$decimal_precision)
	{
		global  $mainframe;
		?>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers.js"></script>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/proj4js/lib/proj4js.js"></script>
		<script type="text/javascript" src="./administrator/components/com_easysdi_shop/lib/openlayers2.11/lib/OpenLayers/Control/LoadingPanel.js"></script>
		<script>
		var map;
		var panelEdition;
		var pointControl;
		var modifyFeatureControl;
		var loadingpanel;
		var wfs = null;
		var wfs3=null;
		var wfs5=null;
		var uploadedPerimeterContent = '<?php echo str_replace("\r\n", "\\n", $mainframe->getUserState('perimeterContent')); $mainframe->setUserState('perimeterContent', null);?>';
		var vectors;
		var nameField;
		var idField;
		var areaField;
		var layerPerimeter;
		var wfsUrl ;
		var isFreeSelectionPerimeter = false;
		var wfsSelection;
		var fromZoomEnd = false;
		var meterToKilometerLimit = <?php echo config_easysdi::getValue("SHOP_CONFIGURATION_MOD_PERIM_METERTOKILOMETERLIMIT",1000000);?>;
		var SHOP_PERIMETER_SURFACE_M2 = '<?php echo JText::_("SHOP_PERIMETER_SURFACE_M2");?>';
		var SHOP_PERIMETER_SURFACE_KM2 = '<?php echo JText::_("SHOP_PERIMETER_SURFACE_KM2");?>';
		var SHOP_PERIMETER_SURFACE_SELECTED = '<?php echo JText::_("SHOP_PERIMETER_SURFACE_SELECTED");?>';
		var SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION = <?php echo config_easysdi::getValue("SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION",2);?>;
		
		
		//addGeometryPerimeter
		var oldLoad = window.onload;
		window.onload=function()
		{
			initMap();
			fromZoomEnd= false;
			selectPerimeter('perimeterList', false);	
			if (oldLoad) oldLoad();
			
			/* upload perimeter from uploaded list */
			if(uploadedPerimeterContent != "")
				drawUploadedPerimeter();
		}
		
		function drawUploadedPerimeter() 
		{
		    //Clear existing free perimeter
		    var elSel = document.getElementById('selectedSurface');		
		    var i;
		    for (i = elSel.length - 1; i>=0; i--) {
		    	document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) - parseFloat(elSel.options[i].value);
		    	elSel.remove(i);			  	       
		    }
		    
		    var wkt = new OpenLayers.Format.Text();
	            var collection = wkt.read(uploadedPerimeterContent);
		    if(!collection) {
			    alert("<?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_BAD_FORMAT'); ?>");
			    return false;
		    }else if(!collection.length || collection.length==0){
				alert("<?php echo JText::_('SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_EMPTY_COLLECTION'); ?>");
				return false;
		    }
		    else{
			    if(collection.constructor != Array) {
	                    	collection = [collection];
	                    }
			    for(var i=0; i<collection.length; ++i) {
				   xVal = parseFloat(collection[i].geometry.x).toFixed(<?php echo $decimal_precision; ?>);
			   	   yVal = parseFloat(collection[i].geometry.y).toFixed(<?php echo $decimal_precision; ?>);
			   	   if (elSel.options.selectedIndex>=0)
			   	   {
			   	   	var selectedIndex = elSel.options.selectedIndex;
                           	   
			   	   	for (i = elSel.length - 1; i>=selectedIndex; i--) 
			   	   	{
			   	   		elSel.options[i+1] = new Option (elSel.options[i].text,elSel.options[i].value);
			   	   	}
			   	   	elSel.options[selectedIndex] = new Option(xVal+" / "+yVal,xVal+" "+yVal);
			   	   }
			   	   else
			   	   {
			   	   	elSel.options[elSel.options.length] = new Option(xVal+" / "+yVal,xVal+" "+yVal);
			   	   }
			    }
		    }
		    
		    //Draw new perimeter
	            drawSelectedSurface();
		    
	        }
		
		function onFeatureSelect(feature) 
		{
	            selectedFeature = feature;
	            popup = new OpenLayers.Popup.FramedCloud("chicken", 
	                                     feature.geometry.getBounds().getCenterLonLat(),
	                                     null,
	                                     "<div style='font-size:.8em'>Feature: " + feature.id +"<br />Area: " + feature.geometry.getArea()+"</div>",
	                                     null, true, onPopupClose);
	            feature.popup = popup;
	            map.addPopup(popup);
	        }
		
		function onFeatureSelect(feature) 
		{
	            selectedFeature = feature;
	            popup = new OpenLayers.Popup.FramedCloud("chicken", 
	                                     feature.geometry.getBounds().getCenterLonLat(),
	                                     null,
	                                     "<div style='font-size:.8em'>Feature: " + feature.id +"<br />Area: " + feature.geometry.getArea()+"</div>",
	                                     null, true, onPopupClose);
	            feature.popup = popup;
	            map.addPopup(popup);
	        }
		
		
		/**
		Delete the previous selection
		*/
		function initSelectedSurface(){
			var elSel = document.getElementById("selectedSurface");
			while (elSel.length > 0)
			{
				elSel.remove(elSel.length - 1);
			}
			document.getElementById('totalSurface').value = 0;
			
			document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";		
			document.getElementById('totalSurfaceDisplayed').value = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value))).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION) : parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value)/1000000)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION); 		
			removeSelection();
		}
		
		function removeSelection(){
			if (vectors)
			{
				var features = vectors.features;
				vectors.removeFeatures(features);
			}
		}
		
		/**
		Add a WFS layer to the current map  
		*/
		function addLayerWfs (layerUrl, count)
		{
			//wfs is the OL vector with already selected features
		        //wfs2 is the OL vector with new selected feature
			if (!wfs)
			{
		     	wfs = new OpenLayers.Layer.Vector("selectedFeatures", {
	                    strategies: [new OpenLayers.Strategy.Fixed()],
	                    protocol: new OpenLayers.Protocol.HTTP({
	                        url: layerUrl,
	                        format: new OpenLayers.Format.GML()
	                    })
	                });
			wfsRegisterEvents(wfs, count);
			map.addLayer(wfs);
			}
			else
			{
			    wfs2 = new OpenLayers.Layer.Vector("selectedFeatures", {
	                    strategies: [new OpenLayers.Strategy.Fixed()],
	                    protocol: new OpenLayers.Protocol.HTTP({
	                        url: layerUrl,
	                        format: new OpenLayers.Format.GML()                                                
	                    })});
	                
	              wfs2.events.register("featureadded", null, 
			      function(myEvent) {
				      loadingpanel.decreaseCounter();
				      if(count == 1)
					      loadingpanel.decreaseCounter();
			      });
	              	      wfs2.events.register("featuresadded", null, 
	              				function(myEvent) { 
								loadingpanel.decreaseCounter();
								removeSelection();
								var wfsFeatures = wfs.features;
								//loop each feature in new selection
								for(var k=0; k<myEvent.features.length; k++){
									// look for a feature with the same id
									var idToLookFor = myEvent.features[k].attributes[idField];
									var found = false;
									for(var j=wfsFeatures.length-1; j>=0; j--) 
									{
										feat2 = wfsFeatures[j];                       
										if (idToLookFor == feat2.attributes[idField])
										{       
											found=true;
											wfs2.removeFeatures(new Array(myEvent.features[k]));
											wfs.removeFeatures([wfsFeatures[j]]);
											break;
										}
									}
									if (!found)
									{
										wfs.addFeatures([myEvent.features[k].clone()]);
										wfs2.removeFeatures(new Array(myEvent.features[k]));
									}
								}
								
								map.removeLayer(wfs2);
						}
					);
				//Don't remove a layer before beeing sure it is loaded...
		       		map.addLayer(wfs2);
				
				}
		 
		}
		
		function refreshWfsOnce()
		{
			map.removeLayer(wfs2);
			wfs2.events.unregister('loadend', null, refreshWfsOnce);
		}
		
		/**
		Reload the features of the user selection stored in the selectedSurface list
		*/
		function getWFSOfSelectedSurface()
		{
			var selectedSurface = document.getElementById('selectedSurface');
			if(selectedSurface.options.length > 0)
			{
				loadingpanel.increaseCounter();
				wfsUrlWithFilter = wfsUrl + '&FILTER=';
				wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc">');
				if(selectedSurface.options.length>1)
				{
					wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:Or>');
				}
				
				for(var i=0; i<selectedSurface.options.length ; i++) 
				{
					var idSurface = selectedSurface.options[i].value;
					wfsUrlWithFilter = wfsUrlWithFilter + escape('<ogc:PropertyIsEqualTo><ogc:PropertyName>' + idField +'</ogc:PropertyName><ogc:Literal>'+ idSurface +'</ogc:Literal></ogc:PropertyIsEqualTo>');
				}
				if(selectedSurface.options.length>1)
				{
					wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Or>');
				}
				wfsUrlWithFilter = wfsUrlWithFilter + escape('</ogc:Filter>');
				
				addLayerWfs(wfsUrlWithFilter, selectedSurface.options.length);
			}
		}
		
		
		/**
		Init the map with layers corresponding to the selected WFS perimeter.
		Add layers correponding to user selection if exists.
		*/
		function selectWFSPerimeter(perimId,
									perimName,
									perimUrl,
									featureTypeName,
									name,id,
									area,
									wmsUrl,
									layerName, 
									imgFormat, 
									pMinResolution , 
									pMaxResolution,
									isOutOfRange, 
									bFromZoomEnd
									)
		{
			//If the modifyFeatureControl has been activated, it needs to be deactivate to avoid "ghost" features to be displayed
			try
			{
				modifyFeatureControl.deactivate();
			}
			catch (err)
			{
			}
			if($("toolsStatus").innerHTML == "<?php echo JText::_("SHOP_OL_TOOL_MODIFY_ACTIVATED") ?>")
			{
				$("toolsStatus").innerHTML = "";
			}
			document.getElementById('perimeter_id').value = perimId;
			
			//Delete the current selection
			//only if the perimeter is different from the one register in the user session
			//And if the call is not resulting from a zoom end event
			if(bFromZoomEnd == false)
			{
				if(perimId != '<?php echo $mainframe->getUserState('perimeter_id'); ?>' )
				{
					initSelectedSurface();
				}
			}
			 						 
			nameField = name;
			idField = id;
			areaField =area;
		
			if (wfs) 
			{
				map.removeLayer(wfs);
				wfs.destroy();				
				wfs=null;		
			}
			
			if (wfs3) 
			{	
				wfs3.destroy();				
				wfs3=null;		
			}
		
			if (layerPerimeter)
			{
				map.removeLayer(layerPerimeter);
				layerPerimeter=null;
			}
			if (vectors)
			{
				var features = vectors.features;
				vectors.removeFeatures(features);
			}
		
			if (perimUrl.length ==0 && wmsUrl.length ==0)
			{
				//Free selection perimeter.
				isFreeSelectionPerimeter = true;
				//draw selection polygon
				drawSelectedSurface();
			}
			else
			{
				isFreeSelectionPerimeter = false;
	
				if(isOutOfRange == false)
				{
					if (wmsUrl.length > 0)
					{
						layerPerimeter = new OpenLayers.Layer.WMS(perimName,
				                    wmsUrl,
				                    {layers: layerName, format : imgFormat  ,transparent: "true"},                                          
				                     {singleTile: true,
						     ratio:1},                                                    
				                     {                     
									  minScale: pMinResolution,
				               		  maxScale: pMaxResolution,                                    	     
				                      maxExtent: map.maxExtent,
				                      projection: map.projection,
				                      units: map.units,
				                      transparent: "true"
				                     }
				                    );
				                 layerPerimeter.alwaysInRange=false;  
				                 layerPerimeter.alpha = setAlpha('image/png');
				                 map.addLayer(layerPerimeter);      
				    
				    wfsUrl = perimUrl+'?request=GetFeature&SERVICE=WFS&TYPENAME='+featureTypeName+'&VERSION=1.0.0';
				  
					}
					else
					{
						var myStyles = new OpenLayers.StyleMap({
					                "default": new OpenLayers.Style({                 
					                    fillColor: "#ffcc66",
					                    strokeColor: "#ff9933",
					                    strokeWidth: 2
					                }),
					                "select": new OpenLayers.Style({
					                    fillColor: "#66ccff",
					                    strokeColor: "#3399ff"
					                })
					            });
						
						
						wfs = new OpenLayers.Layer.WFS( perimName,
						                perimUrl,
						                {typename: featureTypeName}, {
						                    typename: featureTypeName,                                    
						                    extractAttributes: true
						                       
						                },
					                { featureClass: OpenLayers.Feature.WFS}
						                 );
						
						//wfs.events.register("loadstart", null, function() {$("loadingPanelPosition").style.display = 'block';$("status").innerHTML = "<?php echo JText::_("SHOP_SHOP_MESSAGE_LOADING_THE_PERIMETER") ?>"; })
						wfs.events.register("loadend", null, function() { intersect();})
						
						map.addLayer(wfs);
				 	}	
				}
				else
				{
					if(bFromZoomEnd == false)
					{
						wfsUrl = "";
						
					}		
				}
				//call WFS to add selected surfaces
				getWFSOfSelectedSurface();
			}	
			fromZoomEnd = false;
		}
		
		           
	
	function setAlpha(imageformat)
	{
		var filter = false;
		if (imageformat.toLowerCase().indexOf("png") > -1) {
			filter = OpenLayers.Util.alphaHack(); 
		}
		return filter;
	}
	
		            
		function initMap(){
		<?php
		
		$decimal_precision = $basemap[0]->decimalprecision;
		//default style for manually drawed object and selected
		if($basemap[0]->dfltfillcolor != '')
		echo "OpenLayers.Feature.Vector.style['default']['fillColor'] = '".$basemap[0]->dfltfillcolor."';\n";
		if($basemap[0]->dfltstrkcolor != '')
		echo "OpenLayers.Feature.Vector.style['default']['strokeColor'] = '".$basemap[0]->dfltstrkcolor."';\n";
		if($basemap[0]->dfltstrkwidth != '')
		echo "OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '".$basemap[0]->dfltstrkwidth."';\n";
		
		//style for polygon edition
		if($basemap[0]->selectfillcolor != '')
		echo "OpenLayers.Feature.Vector.style['select']['fillColor'] = '".$basemap[0]->selectfillcolor."';\n";
		if($basemap[0]->selectstrkcolor != '')
		echo "OpenLayers.Feature.Vector.style['select']['strokeColor'] = '".$basemap[0]->selectstrkcolor."';\n";
		
		//default style for object being drawn
		if($basemap[0]->tempfillcolor != '')
		echo "OpenLayers.Feature.Vector.style['temporary']['fillColor'] = '".$basemap[0]->tempfillcolor."';\n";
		if($basemap[0]->tempstrkcolor != '')
		echo "OpenLayers.Feature.Vector.style['temporary']['strokeColor'] = '".$basemap[0]->tempstrkcolor."';\n";
		?>
				map = new OpenLayers.Map('map', {
		                projection: new OpenLayers.Projection("<?php echo $basemap[0]->projection; ?>"), 
						displayProjection: new OpenLayers.Projection("<?php echo $basemap[0]->projection; ?>"),
		                units: "<?php echo $basemap[0]->unit; ?>",
		                minScale: <?php echo $basemap[0]->minresolution; ?>,
		                maxScale: <?php echo $basemap[0]->maxresolution; ?>,
		                maxExtent: new OpenLayers.Bounds(<?php echo $basemap[0]->maxextent; ?>),
		                controls: []
						<?php
							if($basemap[0]->restrictedextent == '1') echo  ",restrictedExtent: new OpenLayers.Bounds(".$basemap[0]->maxextent.")\n"
					    ?>
						<?php
							if($basemap[0]->restrictedscales != '') echo  ",scales: [".$basemap[0]->restrictedscales."]\n"
					    ?>
		            });
						map.addControl(new OpenLayers.Control.MousePosition({ 
						div: document.getElementById("mouseposition"),
						prefix: '', 
						suffix: '', 
						separator: ' / ', 
						numDigits: 0
						}));
						
						loadingpanel = new OpenLayers.Control.LoadingPanel({ 
						div: document.getElementById("loadingPanelPosition")
						});
						map.addControl(loadingpanel);
						baseLayerVector = new OpenLayers.Layer.Vector("BackGround",{isBaseLayer: true, transparent: true});
						map.addControl(new OpenLayers.Control.Attribution());
						map.addLayer(baseLayerVector);
		
		<?php
		$i=0;
		foreach ($basemap_contents as $basemap_content){				  
		?>				
						  
				layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $basemap_content->urltype; ?>( "<?php echo $basemap_content->name; ?>",
				
					<?php 
					if ($basemap_content->user != null && strlen($basemap_content->user)>0){
						//if a user and password is requested then use the joomla proxy.
						$proxyhost = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
						$proxyhost = $proxyhost."&type=wms&basemapscontentid=$basemap_content->id&url=";
						echo "\"$proxyhost".urlencode  (trim($basemap_content->url))."\",";												
					}else{	
						//if no user and password then don't use any proxy.					
						echo "\"$basemap_content->url\",";	
					}					
					?>
					
	                    {layers: '<?php echo $basemap_content->layers; ?>', format : "<?php echo $basemap_content->imgformat; ?>",transparent: "true"},
			    
			    <?php if($basemap_content->singletile == 1 && strlen($basemap_content->attribution)>0){ ?>
			    {singleTile: <?php echo $basemap_content->singletile; ?>,
			     ratio: 1,
			     attribution: '<?php echo $basemap_content->attribution; ?>'},
			     <?php }else if($basemap_content->singletile == 1 && !strlen($basemap_content->attribution)>0){ ?>
		             {singleTile: <?php echo $basemap_content->singletile; ?>,
			     ratio: 1},
			     <?php }else if($basemap_content->singletile == 0 && strlen($basemap_content->attribution)>0){?>
			     {attribution: '<?php echo $basemap_content->attribution; ?>'},
			     <?php }else{?>
			     {singleTile: <?php echo $basemap_content->singletile; ?>},
			     <?php }?>
	                     {     
	                      maxExtent: new OpenLayers.Bounds(<?php echo $basemap_content->maxExtent; ?>),
	                      minScale: <?php echo $basemap_content->minresolution; ?>,
	                      maxScale: <?php echo $basemap_content->maxresolution; ?>,                 
	                      projection:"<?php echo $basemap_content->projection; ?>",
	                      units: "<?php echo $basemap_content->unit; ?>",
	                      transparent: "true"
	                     }
	                    );
	                    <?php
	                    if (strtoupper($basemap_content->urltype) =="WMS")
	                    {
	                    ?>
	                    layer<?php echo $i; ?>.alpha = setAlpha('image/png');
	                    <?php
	                    } 
	                    ?>
	                 map.addLayer(layer<?php echo $i; ?>);
		                 
		<?php 
		$i++;
		
		} 
		//Add the preview product layer if needed
		$previewProductId = JRequest::getVar('previewProductId');
		if($previewProductId)
		{
			$queryPreviewLayer = "SELECT * FROM #__sdi_product WHERE id = $previewProductId";
			$db->setQuery( $queryPreviewLayer);
			$product = $db->loadObject();
			if ($db->getErrorNum()) {						
				echo "<div class='alert'>";			
				echo $db->getErrorMsg();
				echo "</div>";
			}
			?>
			
			previewLayer = new OpenLayers.Layer.WMS('PreviewProduct',
							<?php 
							if ($product->viewuser != null && strlen($product->viewuser)>0){
								//if a user and password is requested then use the joomla proxy.
								$proxyhost = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
								$proxyhost = $proxyhost."&type=wms&previewId=$previewProductId&url=";
								echo "\"$proxyhost".urlencode (trim($product->viewurlwms))."\",";												
							}else{	
								//if no user and password then don't use any proxy.					
								echo "\"$product->viewurlwms\",";	
							}					
							?>
								
			                    {layers: '<?php echo $product->viewlayers ; ?>', 
			                    format : "<?php echo $product->viewimgformat ; ?>"  ,
			                    transparent: "true"},
			                    {singleTile: true,
					      ratio:1},                                                    
			                     {                     
								  minScale: <?php echo $product->viewminresolution ; ?>,
			               		  maxScale: <?php echo $product->viewmaxresolution ; ?>,                                    	     
			                      maxExtent: map.maxExtent,
			                      projection:"<?php echo $product->viewprojection ; ?>",
			                      units: "<?php echo $product->viewunit ; ?>",
			                      transparent: "true"
			                     }
			                    );
			 previewLayer.alpha = setAlpha('image/png');
			 map.addLayer (previewLayer);
			
			<?php
		}
		
		?>                    
				
		
				map.events.register("zoomend", null, function(){
					fromZoomEnd = true;
					document.getElementById('previousExtent').value = map.getExtent().toBBOX();
					$("scale").innerHTML = "<?php echo JText::_("SHOP_SHOP_MAP_SCALE") ?>"+map.getScale().toFixed(0);
					text = "";
					
					for (i=0; i<map.layers.length ;i++){
							if (map.getScale() < map.layers[i].maxScale || map.getScale() > map.layers[i].minScale){
							 text = text + map.layers[i].name + "<?php echo JText::_("SHOP_SHOP_MESSAGE_OUTSIDE_SCALE_RANGE") ?>" +" ("+map.layers[i].minScale+"," + map.layers[i].maxScale +")<BR>";
							} 
						}
					$("shopWarnLogo").className = 'shopWarnLogoInactive';
					$("scaleStatus").innerHTML = text;
					
					selectPerimeter('perimeterList', fromZoomEnd);
				});
				
					vectors = new OpenLayers.Layer.Vector("Vector Layer",{isBaseLayer: false,transparent: true});
				
					map.addLayer(vectors);
						$("scale").innerHTML = "<?php echo JText::_("SHOP_SHOP_MAP_SCALE") ?>"+map.getScale().toFixed(0);
						
					function OpenLayerCtrlClicked(ctrl, evt, ctrlPanel, otherPanel){
							if(ctrl != null){
								//If type button, trigger it directly
								if(ctrl.type == OpenLayers.Control.TYPE_BUTTON){
									ctrl.trigger();
								}
								//else deactivate buttons from both panels
								else
								{	
									//check for unauthorized buttons
									if (isFreeSelectionPerimeter && ctrl.displayClass=="olControlDrawFeaturePointDisable")
										return;
								
									if (!isFreeSelectionPerimeter && ctrl.displayClass=="olControlModifyFeatureDisable")
										return;
								
									var controls = ctrlPanel.controls;	
									for(var i = 0; i<controls.length; ++i){
										if(controls[i].type != OpenLayers.Control.TYPE_BUTTON){
											controls[i].deactivate();
										}
									}
									controls = otherPanel.controls;	
									for(var i = 0; i<controls.length; ++i){
										if(controls[i].type != OpenLayers.Control.TYPE_BUTTON)
											controls[i].deactivate();
									}
									//active the clicked button
									ctrl.activate();
								}
							}
						}
						
						
					<?php
						if($mainframe->getUserState('previousExtent') != "")
						{
							?>
								//map.zoomToExtent(new OpenLayers.Bounds(<?php echo JRequest::getVar('previousExtent'); ?>) );
								map.zoomToExtent(new OpenLayers.Bounds(<?php echo $mainframe->getUserState('previousExtent'); ?>) );
							<?php
						}
						else
						{
							?>
								map.zoomToMaxExtent();
							<?php	
						}
					?>
		             
					//enabling navigation history
	        	    navHistory = new OpenLayers.Control.NavigationHistory();
	        	    map.addControl (navHistory);
					navHistory.previous.title='<?php echo JText::_("SHOP_OL_TOOL_NAVPREVIOUS_HINT") ?>';
	 	    	    navHistory.next.title='<?php echo JText::_("SHOP_OL_TOOL_NAVNEXT_HINT") ?>';
					  
					//Zoom in
					oZoomBoxInCtrl = new OpenLayers.Control.ZoomBox({
	        	    title: '<?php echo JText::_("SHOP_OL_TOOL_ZOOMIN_HINT") ?>'
					});
					oZoomBoxInCtrl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_ZOOMIN_ACTIVATED") ?>"; fromZoomEnd =false;})
	
					//Zoom out
					oZoomBoxOutCtrl = new OpenLayers.Control.ZoomBox({
	        	    out: true, displayClass: "olControlZoomBoxOut",
	        	    title: '<?php echo JText::_("SHOP_OL_TOOL_ZOOMOUT_HINT") ?>'
					});
					oZoomBoxOutCtrl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_ZOOMOUT_ACTIVATED") ?>"; fromZoomEnd =false;})
					
					//Pan
					oDragPanCtrl = new OpenLayers.Control.DragPan({
	        	    title: '<?php echo JText::_("SHOP_OL_TOOL_PAN_HINT") ?>'
					});
					oDragPanCtrl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_PAN_ACTIVATED") ?>"; fromZoomEnd =false;})
					
					//Zoom to full extends
					oZoomMxExtCtrl = new OpenLayers.Control.ZoomToMaxExtent({
	        	    map: map, title: '<?php echo JText::_("SHOP_OL_TOOL_MAXEXTENT_HINT") ?>'
					});
					/*
						OpenLayers Edition controls
					*/
					rectControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});		
					rectControl.title = '<?php echo JText::_("SHOP_OL_TOOL_RECTCTRL_HINT") ?>';
					rectControl.featureAdded = function() { intersect();};												
					rectControl.handler.setOptions({irregular: true});                                  
		            rectControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_REC_ACTIVATED") ?>"; fromZoomEnd =false; })
		            
		            
		            //Polygonal  bounding box selection
		            polyControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Polygon,{'displayClass':'olControlDrawFeaturePolygon'});
					polyControl.title = '<?php echo JText::_("SHOP_OL_TOOL_POLYCTRL_HINT") ?>';
		            polyControl.featureAdded = function() { intersect();};
					polyControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_POLY_ACTIVATED") ?>"; fromZoomEnd =false;})
				
					//Point selection
		            pointControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Point,{id: 'pointCtrl','displayClass':'olControlDrawFeaturePoint'});
	                pointControl.title = '<?php echo JText::_("SHOP_OL_TOOL_POINTCTRL_HINT") ?>';
					pointControl.featureAdded = function() { intersect();};
					pointControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_POINT_ACTIVATED") ?>"; fromZoomEnd =false; })            
		         
					//Modify feature shape  
					modifyFeatureControl = new OpenLayers.Control.ModifyFeature(vectors,{'displayClass':'olControlDrawFeaturePath'});				
					modifyFeatureControl.title = '<?php echo JText::_("SHOP_OL_TOOL_MODFEATURE_HINT") ?>';
					modifyFeatureControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("SHOP_OL_TOOL_MODIFY_ACTIVATED") ?>"; fromZoomEnd =false;})
					
					vectors.events.on({
							"afterfeaturemodified": intersect                
					});
					
					/*
						Container panel for standard controls
					*/
					var panel;
		            
					panel = new OpenLayers.Control.Panel({defaultControl: oZoomBoxInCtrl,
					onClick: function (ctrl, evt) {
						OpenLayerCtrlClicked(ctrl, evt, this, panelEdition);
					}});
					
					panel.addControls([   
					  oZoomBoxInCtrl,
					  oZoomBoxOutCtrl,
					  oDragPanCtrl,
	 	    	      navHistory.previous, 	          
	 	    	      navHistory.next,
					  oZoomMxExtCtrl
					  
	        	    ]);
	        	    map.addControl(panel);
	        	     
					/*
						Container panel for custom controls
					*/
	        	    var containerEdition = document.getElementById("panelEdition");
	        	    panelEdition = new OpenLayers.Control.Panel({div: containerEdition,
					onClick: function (ctrl, evt) {
	        	        OpenLayerCtrlClicked(ctrl, evt, this, panel);
	        	    }});
					
	        	    panelEdition.addControls([
					  pointControl,
					  rectControl,
					  polyControl,
					  modifyFeatureControl            	 	              
	        	    ]);
	        	    
	        	    map.addControl(panelEdition);
		}
		         
		var format = new OpenLayers.Format.XML();
		function getElementsByTagNameNS(node, uri, name) {
					
		            var nodes = format.getElementsByTagNameNS(node, uri, name);
		            var pieces = [];
		            for(var i=0; i<nodes.length; ++i) {
		                pieces.push(format.write(nodes[i]));
		            }
		            return (pieces.join(' '));
		}
		
		
		function loadstartfunc(){
		alert("START");
		}
		function loadendfunc(){
		alert("END");
		}
		
		
		function is_array(input){
	    	return typeof(input)=='object'&&(input instanceof Array);
	    }
		
		function intersect() 
		{
			if (isFreeSelectionPerimeter)
		  	{
		   		var features = vectors.features; 
				var feature = features[features.length-1];
	     
	     		document.getElementById("selectedSurface").options.length=0;
	     
			if(feature.geometry.components == null)
			{
				return;
			}
	     		else if (feature.geometry.components[0].components.length > 2)
	     		{
	   		 		featureArea = feature.geometry.getArea();
		    	}else
		    	{
		    		featureArea = 0;
		    	}
		    	
				document.getElementById('totalSurface').value =  parseFloat(featureArea );
				document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = parseFloat(featureArea) <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";
			        document.getElementById('totalSurfaceDisplayed').value = parseFloat(featureArea) <= meterToKilometerLimit ? parseFloat( parseFloat(featureArea)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION) : parseFloat( parseFloat(featureArea/1000000)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION);
		     
		     	if (feature.geometry instanceof OpenLayers.Geometry.Polygon)
		     	{
		     		var polygonSize = feature.geometry.components[0].components.length;
		     		var components = feature.geometry.components[0].components;
		     	
			     	var i = 0;
			     	while (i< polygonSize)
			     	{
		     			document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
						new Option(components [i].x.toFixed(<?php echo $decimal_precision; ?>) +" / "+components [i].y.toFixed(<?php echo $decimal_precision; ?>),components [i].x.toFixed(<?php echo $decimal_precision; ?>) +" "+components [i].y.toFixed(<?php echo $decimal_precision; ?>));
						i++;
		     		}          
		     	}
				if (feature.geometry instanceof OpenLayers.Geometry.Point)
				{
		         	document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(featureArea );       
				document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";
				document.getElementById('totalSurfaceDisplayed').value = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value))).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION) : parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value)/1000000)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION);
				   	    		
				   	document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
					new Option(feature.geometry,feature.geometry);
				}      
				drawSelectedSurface();	 
		 	}
		 	else
		 	{
				//Start to render feature from a selection
				loadingpanel.increaseCounter();
		   		var features = vectors.features;
		        var gmlOptions = {featureType: "feature",featureNS: "http://example.com/feature"};
		
				gml = new OpenLayers. Format. GML.v2(gmlOptions);
		
		        feature = features[features.length-1];
		        var doc = format.read(gml.write(feature, true));               
				
				if (feature.geometry instanceof OpenLayers.Geometry.Polygon)
				{
					wfsUrlWithFilter = wfsUrl+ '&FILTER='+escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc"><ogc:Intersect><ogc:PropertyName>msGeometry</ogc:PropertyName>'+getElementsByTagNameNS(doc,'http://www.opengis.net/gml', 'Polygon')+'</ogc:Intersect></ogc:Filter>');
				}
				
				if (feature.geometry instanceof OpenLayers.Geometry.Point)
				{
					wfsUrlWithFilter = wfsUrl+'&FILTER='+escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc"><ogc:Intersect><ogc:PropertyName>msGeometry</ogc:PropertyName>'+getElementsByTagNameNS(doc,'http://www.opengis.net/gml', 'Point')+'</ogc:Intersect></ogc:Filter>');		
				}
				
				addLayerWfs(wfsUrlWithFilter, null);
			 }
			return;                     
		}
		
		
		function wfsRegisterEvents(wfsObj, count)
		 {
			 if(wfsObj == null){
				wfsObj = wfs;
				count = 0;
			 }
		 			wfsObj.events.register("featureremoved",null,function(event)
		 			{
						//Selected features just removed from wfsObj
						loadingpanel.decreaseCounter();
						//If the call results from a zoom on the map, do not remove selected features
						
						if(fromZoomEnd == false)
						{
					        	feat2 = event.feature;
							var name = feat2.attributes[nameField];
							var id = feat2.attributes[idField];   
							var area = feat2.attributes[areaField];
							var featArea = 0;	
		            				if (areaField.length > 0 && area)
							{
								featArea = area; 
							}
							else 
							{
								featArea = feat2.geometry.getArea();
							}
					        	
								var found = -1;
														
								for (var k=document.getElementById("selectedSurface").options.length-1;k>=0;k--)
								{
									if (document.getElementById("selectedSurface").options[k].value ==  id)
									{
										//Remove the value							
										document.getElementById("selectedSurface").remove(k);								
										document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) - parseFloat(featArea);
										document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";
										document.getElementById('totalSurfaceDisplayed').value = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value))).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION) : parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value)/1000000)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION);
																										
										found=k;																			
									}            				
								}			
						}		                
					});
					
					wfsObj.events.register("featureadded", null, function(event) 
					{ 
						//Selected features just loaded from wfsObj
						loadingpanel.decreaseCounter();
						if(count != null)
							for(var i=0; i<count; i++)
								loadingpanel.decreaseCounter();
						removeSelection();
						    		
						feat2 = event.feature;
						var name = feat2.attributes[nameField];
						var id = feat2.attributes[idField];
			                       
						var area = feat2.attributes[areaField];
						var featArea = 0;	
						if (areaField.length > 0 && area)
						{
							featArea = area; 
						}else {
							featArea = feat2.geometry.getArea();
						}
			                   	
		  				
			   		    //Add the surface in the selectedSurface list
			   		    //To manage the reload of a previous selection, check if the surface exists already before adding it
			   		    var selectSurface = document.getElementById('selectedSurface');
			   		   
			   		    if( selectSurface.options.length > 0 )
			   		    {
			   		    	for (var i=0 ; i < selectSurface.options.length ; i++)
				   		    {
				   		    	if(selectSurface.options[i].value == id )
				   		    	{
				   		    
				   		    		break;
				   		    	}
				   		    	if(i == selectSurface.options.length -1)
				   		    	{
				   		    
				   		    		document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = new Option(name,id);
				   		    		//Add the new value
			           				document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(featArea);                       	                       	                         
								document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";
								document.getElementById('totalSurfaceDisplayed').value = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value))).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION) : parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value)/1000000)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION);
			   		    
				   		    	}
				   		    }
			   		    	
			   		    }
			   		    else
			   		    {
				   		    document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = new Option(name,id);
						    //Add the new value
						    document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(featArea);                       	                       	                         
						    document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";
						    document.getElementById('totalSurfaceDisplayed').value = parseFloat(document.getElementById('totalSurface').value) <= meterToKilometerLimit ? parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value))).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION) : parseFloat( parseFloat(parseFloat(document.getElementById('totalSurface').value)/1000000)).toFixed(SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION);
			   		    }
			   		    
				});
		 }
		 
		</script>
		<table class="infoShop">
		<!-- info sup -->
	        <tr>
		   <td class="shopWarnLogoContainer">
		      <div id="shopWarnLogo" class="shopWarnLogoInactive">
		      </div>
		   </td>
		   <td class="scaleStatusContainer" colspan="3">
		      <div id="scaleStatus"/>
		   </td>
		</tr>
		
		<!-- second line -->
		<tr class="shopHeader">
		  <td class="shopInfoLogoContainer">
		    <div id="loadingPanelPosition" class="olControlLoadingPanel"/>
		  </td>
		  <td class="shopInfoMessageContainer" colspan="2" align="left">
		    <div id="status"><?php echo JText::_("SHOP_SHOP_MESSAGE_LOADING_THE_PERIMETER") ?></div>
		  </td>
		  <td class="toolsStatusContainer">
		    <div id="toolsStatus"><?php echo JText::_("SHOP_SHOP_MESSAGE_NO_TOOL_ACTIVATED") ?> </div>
		  </td>
		 </tr>
		 
		 
		 <!-- the map -->
		 <tr>
		    <td colspan="4">
		  	<div id="map" class="smallmap"></div>
		    </td>
		 </tr>
		 <!-- info inf -->
		 <tr>
		 <td colspan="4">
		
		 <table class="infoShop" width="100%"> 
		 <tr class="shopFooter">
		  <td class="scaleContainer">
		     <div id="scale"/>
		  </td>
		  <td class="beforeCoordinateTextHolder">&nbsp;</td>
		  <td class="coordinateTextHolder"><?php echo JText::_("SHOP_SHOP_MAP_COORDINATE") ?></td>
		  <td class="coordinateContainer">
		    <div id="mouseposition"></div>
		  </td>
		 </tr>
		 </table>
		 
		 </td>
		 </tr>
		</table>
		
		<div id="docs"></div>
		<div id="panelDiv" class="historyContent"></div>
		
		<?php 	
		
		?>
		<script>
		
		function isSelfIntersect(){
		var features = vectors.features;
		if (features.length == 0) {
				 return;
		} 
		var feature= features[features.length-1];
		var lines = new Array();
					  	
		   
		  if (feature.geometry instanceof OpenLayers.Geometry.Polygon){
		     	
		     	var polygonSize = feature.geometry.components[0].components.length;     	
		     	var components = feature.geometry.components[0].components;
		     	     
		     	var i = 0;
		     	while (i< polygonSize-1){		     	
				     		lines.push (new OpenLayers.Geometry.LineString ([
				     				new OpenLayers. Geometry. Point(components [i].x,components [i].y),
				     				new OpenLayers. Geometry. Point(components [i+1].x,components [i+1].y)
				     				]));		     				
				     			     			     	
					i++;
		     	}     	
		     	
		     	for (i=0;i< lines.length;i++){
		     		count=0;
		     		for (j=0;j< lines.length;j++){
		     		
		     		//On ne doit pas comparer la ligne avec elle m�me
		     			if (i != j){
			     			if (lines[i].intersects (lines[j])) {
			     			count++;	     			
			     			}     	
		     			}
		     			if (count > 2) {
		     				//More than 2 intersectios for a line, mean that a line intersects another one.
		     				alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_SELF_INTERSECTING_POLYGON"); ?>");	     			
			     			return true;
		     			}     			     				     		
		     		}
		     
		     		
		     	}     	
		     }
		     
		     return false;
		}
		
		 function submitOrderForm()
		 { 	
		 	var selectedSurface = document.getElementById('selectedSurface');
		 	
			//If feature edition is active, ask the user if he whants to leave edition
			if(modifyFeatureControl.active == true){
				if (confirm("<?php echo JText::_("SHOP_SHOP_MESSAGE_QUIT_AND_SAVE_EDITION"); ?>")){
					try
					{
						modifyFeatureControl.deactivate();
					}
					catch (err)
					{
					}
				}
				else{
					return;
				}
			}
			
			if (document.getElementById('step').value == 3 && isSelfIntersect()==true)
		 	{
		 		return ;
		 	}
	 		
			 if (selectedSurface.options.length>0)
			 {	  	
			 	var replicSelectedSurface = document.getElementById('replicSelectedSurface');
			 	var replicSelectedSurfaceName = document.getElementById('replicSelectedSurfaceName');
			 	 	
			 
				 var i=0;
				 for (i=0;i<selectedSurface.options.length;i++)
				 {  
					 replicSelectedSurface.options[i] = new Option(selectedSurface.options[i].value,selectedSurface.options[i].value);
					 replicSelectedSurfaceName.options[i] = new Option(selectedSurface.options[i].text,selectedSurface.options[i].text);
					 replicSelectedSurface.options[i].selected=true;
					 replicSelectedSurfaceName.options[i].selected=true;
					 //Take care here to finish with the same point that the first for a free selection
					 if(i == (selectedSurface.options.length - 1) && isFreeSelectionPerimeter){
						 if(selectedSurface.options[i].value != selectedSurface.options[0].value){
							 replicSelectedSurface.options[i+1] = new Option(selectedSurface.options[0].value,selectedSurface.options[0].value);
							 replicSelectedSurfaceName.options[i+1] = new Option(selectedSurface.options[0].text,selectedSurface.options[0].text);
							 replicSelectedSurface.options[i+1].selected=true;
							 replicSelectedSurfaceName.options[i+1].selected=true;
						 }
					 }
				 }
				document.getElementById('totalArea').value=document.getElementById('totalSurface').value;
			 	
			 	var totalArea = document.getElementById('totalArea').value;
				var selectedSurfaceMin = document.getElementById('totalSurfaceMin').value;
			 	var selectedSurfaceMax = document.getElementById('totalSurfaceMax').value;
			 	if(!(document.getElementById('step').value <= document.getElementById('fromStep').value) && document.forms['orderForm'].elements['task'].value != 'deleteProduct'){
					if ((parseFloat(totalArea) > parseFloat(selectedSurfaceMax)))
			 		{
			 			alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_SELECTED_SURFACE_ABOVE_MAX"); ?>");
						document.getElementById('step').value = document.getElementById('fromStep').value;
			 			return ;
			 		}
					if ((parseFloat(totalArea) < parseFloat(selectedSurfaceMin)) || parseFloat(totalArea) <= 0 )
			 		{
			 			alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_SELECTED_SURFACE_BELLOW_MIN"); ?>");
						document.getElementById('step').value = document.getElementById('fromStep').value;
			 			return ;
			 		}
			 	}
			 	var bufferValue = document.getElementById('bufferValue').value;
				if( parseFloat(bufferValue) < 0)
				{
			 		alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_ERROR_BUFFER_VALUE"); ?>");
			 		return ;
			 	}
			 	
			 	
			 	document.getElementById('bufferValue2').value = document.getElementById('bufferValue').value; 
			 	document.getElementById('orderForm').submit();
			 }
			 else 
			 {
			 	if (document.getElementById('step').value == 2 || document.getElementById('step').value == 1)
			 	{
			 		document.getElementById('bufferValue2').value = document.getElementById('bufferValue').value; 
			 		document.getElementById('orderForm').submit();
			 		 			
			 	}else
			 	{
		 			document.getElementById('step').value = 2;
		 			alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_NO_SELECTED_DATA"); ?>");
		 		}
			 }
		 }
		 </script>
		<div style="display: none;">
		<form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='POST'>
			<input type="hidden" id="bufferValue2" name="bufferValue2" value="0"> 
			<select multiple="multiple" size="10" id="replicSelectedSurface" name="replicSelectedSurface[]"></select> 
			<select multiple="multiple" size="10" id="replicSelectedSurfaceName" name="replicSelectedSurfaceName[]"></select> 
			<input type='hidden' id="totalArea" name='totalArea' value='<?php echo JRequest::getVar('totalArea'); ?>'> 
			<input type='hidden' id="fromStep" name='fromStep' value='2'> 
			<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
			<input type='hidden' id="option" name='option' value='<?php echo $option; ?>'> 
			<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'> 
			<input type='hidden' id="view" name='view' value='<?php echo JRequest::getVar('view'); ?>'> 
			<input type='hidden' id="perimeter_id" name='perimeter_id' value='0'> 
			<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
			<input type='hidden' id='previousExtent' name='previousExtent' value="<?php echo JRequest::getVar('previousExtent'); ?>" />
			<input type='hidden' id='previewProductId' name='previewProductId' value="<?php echo JRequest::getVar('previewProductId'); ?>" />
		</form>
		</div>
		<?php
	}
	
	function orderRecap ($product)
	{
		?>
		<table>
		<thead class='contentheading'>
			<tr>
				<td></td>
				<td><?php echo JText::_("SHOP_SHOP_DATA_IDENTIFICATION");?></td>
				<td><?php echo JText::_("SHOP_SHOP_ORGANISATION_NAME");?></td>
			</tr>
		</thead>
		<tbody>
		<?php
		$i = 0;
		foreach( $rows as $product ){
			?>
			<tr>
				<td><input type="hidden" id="cb<?php echo $i;?>" name="cid[]"
					value="<?php echo $product->id; ?>" /></td>
				<td><a
					href='index.php?option=com_easysdi_core&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->name; ?></a>
				</td>
				<td><a
					href='index.php?option=com_easysdi_core&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->supplier_name; ?></a>
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
	
	function orderProperties ($products, $option, $task, $step)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		?>
		<script>
		var initFields = false;
		window.addEvent('domready', function() {
		 // select automatically properties that have only one choice
		 if(initFields == false){
			var aSel = $('orderForm').getElementsByTagName("select");
			for(var i=0;i<aSel.length;i++){
				if(aSel[i].options.length == 3){
					aSel[i].options[2].selected = true;
				}
			}
			initFields = true;
		 }
		});
		 function submitOrderForm()
		 {
		 	document.getElementById('orderForm').submit();
		 }
		 </script>
		<form name="orderForm" id="orderForm"
			action='<?php echo JRoute::_("index.php") ?>' method='POST'>
			<?php
			$language =& JFactory::getLanguage();
			foreach ($products as $product  ){
				$query = "SELECT DISTINCT pd.id as id, 
									pd.ordering as property_order, 
									pd.mandatory as mandatory, 
									t.label as property_text, 
									pd.type as type, 
									pd.id as property_id							
						  FROM #__sdi_language l, 
						  		#__sdi_list_codelang cl,
						  		#__sdi_product_property p 
						  INNER JOIN #__sdi_propertyvalue pvd ON p.propertyvalue_id=pvd.id 
						  INNER JOIN #__sdi_property pd ON pvd.property_id=pd.id 
						  LEFT OUTER JOIN #__sdi_translation t ON pd.guid=t.element_guid 
						  WHERE p.product_id=".$product->id." 
						  AND t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' 
						  AND pd.published=1 
						  AND (pd.account_id = 0 OR pd.account_id = ".$product->supplier_id." ) 
						  ORDER by property_order";
		
				$db->setQuery( $query );
				$rows = $db->loadObjectList();
			?>
			<br/>
			<fieldset id="fieldset_properties" class="fieldset_properties"><legend><?php echo JText::_($product->name);?></legend>
			<ol class="product_proprety_row_list">
			<?php
				if (count($rows)>0){
				foreach ($rows as $row){
					echo "<li>";
					$query="SELECT pd.mandatory as mandatory, 
											pvd.ordering as value_order, 
											pvd.name as value_text,
											t.label as value_trans, 
											pvd.id as value
										FROM #__sdi_language l, 
						  					#__sdi_list_codelang cl,
						  					#__sdi_product_property p 
										INNER JOIN #__sdi_propertyvalue pvd on p.propertyvalue_id=pvd.id 
										INNER JOIN #__sdi_property pd on pvd.property_id=pd.id 
										LEFT OUTER JOIN #__sdi_translation t ON pvd.guid=t.element_guid 
										WHERE p.product_id=".$product->id." 
										AND t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' 
										AND pd.published=1 
										AND pd.id=".$row->id." 
										ORDER BY value_order";
					
					switch($row->type){
						case "list":
							$db->setQuery( $query );
							$rowsValue = $db->loadObjectList();
							$isMandatoryClass = $row->mandatory == 1 ?  " mdtryElem" : "";
							echo "<select class=\"product_proprety_row_list$isMandatoryClass\" 
										  name='".$row->property_id."_list_property_".$product->id."[]' 
										  id='".$row->property_id."_list_property_".$product->id."[]'>";
							//add a default option that replaces the text before the list, value = -1 to be sure to not interfer with pvd.id.
							echo "<option value='-1'>-". JText::_($row->property_text)."-</option>";
							foreach ($rowsValue as $rowValue){
								$selProduct = $mainframe->getUserState($row->property_id.'_list_property_'.$product->id);
								$selected = "";
								if ( is_array($selProduct)){
									if (in_array($rowValue->value,$selProduct)) $selected ="selected";
								}
								echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_trans)."</option>";
							}
							echo "</select>";
							if($rowValue->mandatory == 1) echo "<span class=\"mdtyProperty\">*</span>"; else echo "&nbsp;";
							break;
						case "mlist":
							echo "<div id='".$row->property_id."_mlist_property_".$product->id."[]_label'>".JText::_($row->property_text).":</div>";
								$db->setQuery( $query );
							$rowsValue = $db->loadObjectList();
							$isMandatoryClass = $row->mandatory == 1 ?  " mdtryElem" : "";
							echo "<table><tr><td>";
							echo "<select class=\"product_proprety_row_list$isMandatoryClass\" 
									      multiple size='".count($rowsValue)."' 
									      name='".$row->property_id."_mlist_property_".$product->id."[]' 
									      id='".$row->property_id."_mlist_property_".$product->id."[]'>";
							foreach ($rowsValue as $rowValue){
								$selProduct = $mainframe->getUserState($row->property_id.'_mlist_property_'.$product->id);
								$selected = "";
								if ( is_array($selProduct)){
									if (in_array($rowValue->value,$selProduct)) $selected ="selected";
								}
								echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_trans)."</option>";
							}
							echo "</select>";
							echo "</td><td>";
							if($rowValue->mandatory == 1) echo "<div class=\"mdtyProperty\">*</div>"; else echo "&nbsp;";
							echo "</td></tr></table>";
							break;
						case "cbox":
							$html = "";
								$db->setQuery( $query );
							$rowsValue = $db->loadObjectList();
							$isMandatoryClass = $row->mandatory == 1 ?  "mdtryElem" : "";
							$html .= "<div class=\"product_proprety_cbox_group\">";
							foreach ($rowsValue as $rowValue){
								$selProduct = $mainframe->getUserState($row->property_id.'_cbox_property_'.$product->id);
								$selected = "";
								if ( is_array($selProduct)){
									if (in_array($rowValue->value,$selProduct)) $selected ="checked";
								}
								
								$html .= "&nbsp;&nbsp;<input type='checkbox' 
														class='".$isMandatoryClass."' 
														name='".$row->property_id."_cbox_property_".$product->id."[]' 
														id='".$row->property_id."_cbox_property_".$product->id."[]' ".$selected." 
														value='".$rowValue->value."'/>&nbsp;".JText::_($rowValue->value_trans)."&nbsp;";
								$html .="<br/>";
							}
							$html .= "</div>";
							echo "<span id='".$row->property_id."_cbox_property_".$product->id."[]_label'>".JText::_($row->property_text).": "."</span>";
							if($rowValue->mandatory == 1) 
								echo "<span id='".$row->property_id."_cbox_property_".$product->id."[]_group' class=\"mdtyProperty\">*</span><br/>";
							echo $html;
							break;
						case "text":
							echo "<div id='".$row->property_id."_text_property_".$product->id."_label'>".JText::_($row->property_text).":</div>";
							$db->setQuery( $query );
							$rowsValue = $db->loadObjectList();
							$isMandatoryClass = $row->mandatory == 1 ?  " mdtryElem" : "";
							foreach ($rowsValue as $rowValue){
								$valueText = $mainframe->getUserState($row->property_id.'_text_property_'.$product->id);
			
								if ($valueText){
									$selected = $valueText;
								}else{
									$selected = "";
									//$selected = $rowValue->val_trans;
								}
								echo "<table><tr><td>";
								//value: 
								echo "<input class=\"msgProperty$isMandatoryClass\" 
											 type='text' 
											 name='".$row->property_id."_text_property_".$product->id."' 
											 id='".$row->property_id."_text_property_".$product->id."' 
											 value='".JText::_($selected)."' />";
								echo "</td><td>";
								if($rowValue->mandatory == 1) echo "<div class=\"mdtyProperty\">*</div>"; else echo "&nbsp;";
								echo "</td></tr></table>";
								break;
							}
							break;
							
							
						case "textarea":
							echo "<div id='".$row->property_id."_textarea_property_".$product->id."[]_label'>".JText::_($row->property_text).":</div>";
							$db->setQuery( $query );
							$rowsValue = $db->loadObjectList();
							$isMandatoryClass = $row->mandatory == 1 ?  "mdtryElem" : "";
							
						foreach ($rowsValue as $rowValue){
								$selProduct = $mainframe->getUserState($row->property_id.'_textarea_property_'.$product->id);
												
								if (count($selProduct)>0){
									$selected = $selProduct[0];
								}else{
									$selected = "";
									//$selected = $rowValue->val_trans;
								}
								echo "<table><tr><td>";
								echo "<TEXTAREA class='".$isMandatoryClass."' 
										rows=3 COLS=62 
										name='".$row->property_id."_textarea_property_".$product->id."[]' 
										id='".$row->property_id."_textarea_property_".$product->id."[]'>".JText::_($selected)."</textarea>";
								echo "</td><td>";
								if($rowValue->mandatory == 1) echo "<div class=\"mdtyProperty\">*</div>"; else echo "&nbsp;";
								echo "</td></tr></table>";
								break;
							}
							break;
						case "message":
							echo JText::_(trim($row->property_text));
							$db->setQuery( $query );
							$rowsValue = $db->loadObjectList();
							foreach ($rowsValue as $rowValue){
								$selected = $rowValue->val_trans;
								?>
								<div class="product_proprety_message_title">
								<?php 
								echo JText::_($selected);
								?>
								<input type='hidden' name='<?php echo $row->property_id; ?>_message_property_<?php echo $product->id; ?>' id='<?php echo $row->property_id; ?>_message_property_<?php echo $product->id; ?>' value='<?php echo $selected;?>'></input>
								</div>
								<?php 
								break;
							}
			
							break;
					}
					echo "</li>";
				}

				}
				?>
				</ol>
				</fieldset>
				<?php
				}
				?> 
				<input type='hidden' id="fromStep" name='fromStep' value='3'> 
				<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
				<input type='hidden' id="option" name='option' value='<?php echo $option; ?>'>
				<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'>
				<input type='hidden' id="view" name='view' value='<?php echo JRequest::getVar('view'); ?>'>
				<input type='hidden' id="totalArea" name='totalArea' value='<?php echo JRequest::getVar('totalArea'); ?>'> 
				<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
				</form>
				<?php 
			
	}
	
	function orderDefinition($cid,$user, $rows, $option,$task,$step){
		global  $mainframe;
		$db =& JFactory::getDBO();
		if (!$user->guest){
			?>
			<div class="info"><?php echo JText::_("SHOP_SHOP_MESSAGE_CONNECTED_WITH_USER").$user->name;  ?></div>
			<?php
		}
		?>
		<script>
		 function submitOrderForm(){
			 if(document.getElementById('step').value <= document.getElementById('fromStep').value)
			 {
			 	//Step back in the tab, don't need to check the values filled in the form
			 }
			 else
			 {
				 if (!document.getElementById('order_type_d').checked && !document.getElementById('order_type_o').checked){
				 
				 	alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_TYPE_NOT_FILL") ?>");
				 	return;
				 }
				 if (document.getElementById('order_name').value.length == 0){
				 
				 	alert("<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_NAME_NOT_FILL") ?>");
				 	return;
				 }
				 //Limit order name to 40 characters
				 if(document.getElementById('order_name').value.length > 40){
					alert('<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_NAME_TOO_LONG"); ?>');
					return;
				}
			 }
		 	document.getElementById('orderForm').submit();
		 }
		 </script>
	 <form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='GET'>
		<input type='hidden' id="fromStep" name='fromStep' value='4'> 
		<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
		<input type='hidden' id="option" name='option' value='<?php echo $option; ?>'>
		<input type='hidden' id="task" name='task' value='order'> 
		<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
		<table class="ordersteps">
			<tr>
				<td class="orderstepsTitle"><?php echo JText::_("SHOP_ORDER_NAME"); ?>:</td>
				<td><input type="text" class="infoClient" name="order_name" id="order_name" value="<?php echo $mainframe->getUserState('order_name'); ?>"></td>
			</tr>
			<tr>
				<td colspan="2"><div class="separator" /></td>
			</tr>
			<tr>
				<td class="orderstepsTitle"><?php echo JText::_("SHOP_ORDER_TYPE_D"); ?>:</td>
				<td><input type="radio" name="order_type" id="order_type_d" value="D" <?php if ("D" == $mainframe->getUserState('order_type')) echo "checked"; ?>></td>
			</tr>
			<tr>
				<td class="orderstepsTitle"><?php echo JText::_("SHOP_ORDER_TYPE_O"); ?>:</td>
				<td><input type="radio" name="order_type" id="order_type_o" value="O" <?php if ("O" == $mainframe->getUserState('order_type')) echo "checked"; ?>></td>
			</tr>
			<tr>
				<td colspan="2"><div class="separator" /></td>
			</tr>
		
			<?php
		
			if ($user->guest )
			{
			?>
			<tr>
				<td class="orderstepsTitle"><?php echo JText::_("SHOP_SHOP_MESSAGE_USER_NOT_CONNECTED"); ?>:</td>
			</tr>
			<tr>
				<td class="orderstepsTitle"><?php echo JText::_("SHOP_AUTH_USER"); ?>:</td>
				<td><input type="text" class="infoClient" name="user" value=""></td>
			</tr>
			<tr>
				<td class="orderstepsTitle"><?php echo JText::_("SHOP_AUTH_PASSWORD"); ?>:</td>
				<td><input type="password" class="infoClient" name="password" value=""></td>
			</tr>
			<tr>
					<td colspan="2"><div class="separator" /></td>
				</tr>
			<?php
			}
			 ?>  
				
				<tr>
					<td class="orderstepsTitle"><?php echo JText::_("SHOP_SHOP_ORDER_THIRD_PARTY"); ?>:</td>
					<td>
						<select class="thirdPartySelect" name="third_party">
							<option value="0"><?php echo JText::_("SHOP_SHOP_ORDER_FOR_NOBODY"); ?></option>
							<?php
							$third_party = $mainframe->getUserState('third_party');
							echo $third_party;
							foreach ($rows as $row){
								$selected="";
								if ($third_party == $row->account_id) $selected="selected";
								echo "<option ".$selected." value=\"".$row->account_id."\">".$row->name."</option>";
							}
							?>
						</select>
					</td>
				</tr>
			</table>
		</form>

			<!--
      		//Call here the include content item plugin, or a specific article.
			//Insert into the EasySDI config a key SHOP_CONFIGURATION_ARTICLE_STEP4 with
			//the value like {include_content_item 148} refering to the plugin and
			//article you would like to call.
			//-->       
		
			<table width="100%" id="infoStep4">
			<?php

			$row->text = config_easysdi::getValue("SHOP_CONFIGURATION_ARTICLE_STEP4");
			$args = array( 1,&$row,&$params);
			JPluginHelper::importPlugin( 'content' );
			$dispatcher =& JDispatcher::getInstance();
			$results = $dispatcher->trigger('onPrepareContent', 
			array(&$row,&$params,0));
			echo $row->text;
			?>
		    </table>
		<?php
	}
	
	function orderSend ($account,$user,$rows,$step,$hasExternal,$hasInternal,$public,$private)
	{
		$db =& JFactory::getDBO();
		?>
		<div class="contentin">
		<br>
		<br>
		<script>
		 function submitOrderForm()
		 {
		 	document.getElementById('orderForm').submit();
		 }
		 </script>
		<form id="orderForm" name="orderForm" action="<?php echo JRoute::_("index.php"); ?>">
			<input type='hidden' id="fromStep" name='fromStep' value='5'> 
			<input type="hidden" name="task" id="taskOrderForm" value="order"> 
			<input type="hidden" name="option" value="<?php echo JRequest::getVar('option'); ?>"> 
			<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
			<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>">
		</form>
			<?php
			if (!$user->guest)
			{
				$isProductAllowed = true;
				
				foreach ($rows as $row)
				{
					if($row->published == '0' ||  ($row->visibility_id <> $public && $row->visibility_id <> $private))
					{
						HTML_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_PRODUCT_NOT_ORDERABLE", $row->name );
						$isProductAllowed = false;
						break;
					}
					
					$query = "SELECT COUNT(*) FROM #__sdi_product p 
									INNER JOIN #__sdi_objectversion v ON v.id = p.objectversion_id
									INNER JOIN #__sdi_object o ON o.id = v.object_id
									INNER JOIN #__sdi_metadata m ON m.id = v.metadata_id
									WHERE p.published=1 AND o.published = 1
									AND p.id = $row->id
									AND
									 (o.account_id =  $account->id
									OR
									o.account_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id )
									OR 
									o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = (SELECT root_id FROM #__sdi_account WHERE id = $account->id ))
									OR
									o.account_id IN (SELECT id FROM #__sdi_account WHERE root_id = $account->id ))" ;
							$db->SetQuery($query);
							$countProduct = $db->loadResult();
					if($row->visibility_id == $private)
					{	
						//User needs to belong to the product's partner group
						if($countProduct == 1 && $hasInternal == true )
						{
							//The product belongs to the current user's group and the user has the internal right
						}
						else
						{
							//The product does not belong to the current user's group, or the user does not have the internal right
							HTML_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_USER_RIGHT", $row->data_title );
							$isProductAllowed = false;
							break;
						}		
						
					}
					else if($row->visibility_id == $public)
					{
						if($hasExternal == false)
						{
							//User does not have the right to order external product
							HTML_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_USER_RIGHT", $row->data_title );
							$isProductAllowed = false;
							break;
						}
					}
					else
					{
						//Product is not orderable
						HTML_shop::displayErrorMessage("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_USER_RIGHT", $row->data_title );
						$isProductAllowed = false;
						break;
					}
					
				}
				
				
				if($isProductAllowed == true)
				{
				//	
				//Call here the include content item plugin, or a specific article.
				//Insert into the EasySDI config a key SHOP_CONFIGURATION_ARTICLE_STEP5 with
				//the value like {include_content_item 148} refering to the plugin and
				//article you would like to call.
				//
				?>
				<table width="100%" id="generalConditions">
				<?php
				$row->text = config_easysdi::getValue("SHOP_CONFIGURATION_ARTICLE_STEP5");
				$args = array( 1,&$row,&$params);
				JPluginHelper::importPlugin( 'content' );
				$dispatcher =& JDispatcher::getInstance();
				$results = $dispatcher->trigger('onPrepareContent', 
				array(&$row,&$params,0));
				
				echo $row->text;
				
				?>
		        </table>
				<input
				onClick="document.getElementById('taskOrderForm').value = 'saveOrder';submitOrderForm();"
				type="button"
				class="button"
				value='<?php echo JText::_("SHOP_SHOP_ORDER_SAVE_BUTTON"); ?>'> <input
				onClick="document.getElementById('taskOrderForm').value = 'sendOrder';submitOrderForm();"
				type="button"
				class="button"
				value='<?php echo JText::_("SHOP_SHOP_ORDER_SEND_BUTTON"); ?>'> <?php
				}
			}
			else
			{
				//User not connected
				?>
				<div class="alert"><?php echo JText::_("SHOP_SHOP_MESSAGE_NOT_CONNECTED");?></div>
				<?php
			}
			?></div><?php 
	}
	
	function displayErrorMessage ($error, $product)
	{
		?>
		<div class="alert">
			<span class="alertheader"><?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_IN_ORDER");?><br></span>
			<?php echo JText::_($error);?>
			<span class="alerthighlight"><?php echo $product;?><br></span>
			<?php echo JText::_("SHOP_SHOP_MESSAGE_ORDER_PROBLEM_ACTION");?><br>
		</div>
		<?php 
	}
	
	function termsOfUse($id, $option, $task,$view,$step,$row)
	{
		?>
		<form name="dlProductForm" id="dlProductForm" 	 action='index.php' method='GET'>
			<table>
				<tr>
					<td >
						<table width="100%" >
							<tr>
								<td colspan ="2" >
									<?php
									echo $row->text;
									?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table>
							<tr>
								<td width="50%" align="right" >    
									<input
										onClick="document.getElementById('task').value = 'shop'; try{ window.parent.document.getElementById('sbox-window').close();}catch(err){javascript:history.back();}"
										type="button"
										class="button"
										value='<?php echo JText::_("SHOP_SHOP_PRODUCT_TERMS_DENY"); ?>'> 
								</td>
								<td width="50%" align="left">
									<input 
									onClick="document.getElementById('task').value = 'downloadProduct';document.getElementById('product_id').value = '<?php echo $id; ?>';document.getElementById('dlProductForm').submit();try{ window.parent.document.getElementById('sbox-window').close();}catch(err){}"
									type="button"
									class="button"
									value='<?php echo JText::_("SHOP_SHOP_PRODUCT_TERMS_ACCEPT"); ?>'> 
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<input type='hidden' name='option' value='<?php echo $option;?>'> 
			<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'> 
			<input type='hidden' id="view" name='view' value='<?php echo $view; ?>'> 
			<input type='hidden' id="fromStep" name='fromStep' value='1'> 
			<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'>
			<input type='hidden' id="product_id" name='product_id' value=''>
			<input type='hidden' id="resource" name='resource' value='<?php echo JRequest::getVar('resource');?>'>
		</form>
		
		<?php
	}
}
?>