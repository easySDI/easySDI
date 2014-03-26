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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');


global  $mainframe;
global  $areaPrecision;
global  $meterToKilometerLimit;
$curstep = JRequest::getVar('step',0);
$areaPrecision = config_easysdi::getValue("SHOP_CONFIGURATION_MOD_PERIM_AREAPRECISION",2);
$meterToKilometerLimit = config_easysdi::getValue("SHOP_CONFIGURATION_MOD_PERIM_METERTOKILOMETERLIMIT",1000000);


if ($curstep == "2")
{
	$db =& JFactory::getDBO();

	$query = "select * from #__sdi_basemap b where b.`default` = 1";
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$db->getErrorMsg();
		echo "</div>";
	}

	$decimal_precision = $rows[0]->decimalprecision;
	$cid = 		$mainframe->getUserState('productList');

	if (count($cid)>0)
	{
		$query = "SELECT * FROM #__sdi_perimeter order by ordering";
		$query = "select count(*)  from #__sdi_product where id in (";
		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query.")";
		$db->setQuery( $query );
		$nbProduct = $db->loadResult();

		$query = "SELECT * FROM #__sdi_perimeter WHERE id in (SELECT perimeter_id  FROM #__sdi_product_perimeter  where product_id in (";
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
		$query = "SELECT count(*) as total ,perimeter_id  FROM `#__sdi_product_perimeter` where product_id in (";
		foreach( $cid as $id )
		{
			$query = $query.$id."," ;
		}
		$query  = substr($query , 0, -1);
		$query = $query .") AND buffer= 1 group by perimeter_id having count(*) = $nbProduct";
		$db->setQuery( $query );
		$bufferRows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			echo 			$db->getErrorMsg();
		}
		?>

		<script>
		function enableBufferByPerimeter(perimId)
		{
			<?php	
			foreach ($bufferRows as $bufferRow)
			{		
			?>
			if (perimId == '<?php echo $bufferRow->perimeter_id ?>')
			{
				document.getElementById('bufferValue').disabled=false;
				document.getElementById('bufferValue').style.display = 'inline';
				document.getElementById('bufferValueSuffix').innerHTML = '<?php echo JText::_("SHOP_PERIMETER_BUFFER_UNIT"); ?>';
				document.getElementById('bufferRegion').style.display='block';
				
				return ;
			}
			<?php 
			}
			?>
			document.getElementById('bufferValue').disabled=true;
			document.getElementById('bufferValue').style.display = 'none';
			document.getElementById('bufferValueSuffix').innerHTML = '<?php echo JText::_("SHOP_PERIMETER_BUFFER_UNAVAILABLE"); ?>';
			document.getElementById('bufferRegion').style.display='none';
		}


		function selectPerimeter(perimListName, bFromZoomEnd)
		{
			var isOutOfRange = false;
			selIndex = document.getElementById(perimListName).selectedIndex;

			<?php	
			foreach ($rows as $row)
			{
				if (1==1 || ($row->user !=null && strlen($row->user)>0))
				{
					//if a user and password is requested then use the joomla proxy.
					$proxyhostOrig = config_easysdi::getValue("SHOP_CONFIGURATION_PROXYHOST");
					$proxyhost = $proxyhostOrig."&type=wfs&perimeterdefid=$row->id&url=";

					if ($row->urlwfs!=null && strlen($row->urlwfs)>0)
					{
						$urlwfs =  $proxyhost.urlencode  (trim($row->urlwfs));
					}
					else
					{
						$urlwfs ="";
					}

					$proxyhost = $proxyhostOrig."&type=wms&perimeterdefid=$row->id&url=";
					if ( $row->urlwms!=null && strlen($row->urlwms)>0)
					{
						$urlwms = $proxyhost.urlencode  (trim($row->urlwms));
					}else{
						$urlwms ="";
					}
				}
				else
				{
					$urlwfs = $row->urlwfs;
					$urlwms=	$row->urlwms;					
				}
				?>
				if (document.getElementById(perimListName)[selIndex].value == '<?php echo $row->id; ?>')
				{	
					isOutOfRange = false;
					if($("shopWarnLogo") != null && $("scaleStatus") != null){
						$("shopWarnLogo").className = "shopWarnLogoInactive";
						$("scaleStatus").innerHTML = "";
					}
					if(<?php echo $row->minresolution;?> == 0 && <?php echo $row->maxresolution; ?> == 0 )
					{
						//Free selection perimeter case
						//Always display the button for manual perimeter
						document.getElementById('addPerimeterButton').style.display='block';
						document.getElementById('manualAddGeometry').style.display='block';
						//Hide manual 
						document.getElementById('manualPerimDivId').style.display='none';
					}
					else
					{
						//hide freeselect
						document.getElementById('manualAddGeometry').style.display='none';
						
						if (map.getScale() < <?php echo $row->maxresolution; ?> || map.getScale() > <?php echo $row->minresolution; ?>)
						{
							text = "<?php echo JText::_("SHOP_SHOP_MESSAGE_OUTSIDE_SCALE_RANGE"); ?>" + " : " + '<?php echo addslashes($row->name); ?>' +  " ("+<?php echo $row->minresolution; ?>+"," + <?php echo $row->maxresolution; ?> +")<BR>";
							$("shopWarnLogo").className = 'shopWarnLogoActive';
							$("scaleStatus").innerHTML = text;
							isOutOfRange = true;
						}
						//Display the button for manual perimeter
						if(<?php echo $row->islocalisation;?> == 0)
						{
							document.getElementById('addPerimeterButton').style.display='none';
							document.getElementById('manualAddGeometry').style.display='none';
							document.getElementById('manualPerimDivId').style.display='none';
						}
						else
						{	
							document.getElementById('addPerimeterButton').style.display='block';
							document.getElementById('manualAddGeometry').style.display='block';
						}
					} 
					selectWFSPerimeter( document.getElementById(perimListName)[selIndex].value,
							"<?php echo $row->name; ?>",
							"<?php echo $urlwfs; ?>",
							"<?php echo $row->featuretype; ?>",
							"<?php echo $row->fieldname; ?>",
							"<?php echo $row->fieldid; ?>",
							"<?php echo $row->fieldarea; ?>",
							"<?php echo $urlwms; ?>",
							"<?php echo $row->layername; ?>",
							"<?php echo $row->imgformat; ?>",
							<?php echo $row->minresolution; ?>,
									<?php echo $row->maxresolution; ?>,
											isOutOfRange,
											bFromZoomEnd);

					enableBufferByPerimeter('<?php echo $row->id; ?>');
					
					//show/hide menu buttons according free perim
					if (isFreeSelectionPerimeter)
					{
						//disable pointer button
						if(pointControl != null)
							pointControl.displayClass="olControlDrawFeaturePointDisable";
						//show modify feature ctrl
						if(modifyFeatureControl != null)
							modifyFeatureControl.displayClass="olControlModifyFeature";
						if(panelEdition != null)
							try{panelEdition.redraw();}catch(err){}
					}else
					{
						//show pointer button
						if(pointControl != null)
							pointControl.displayClass="olControlDrawFeaturePoint";
						//hide modify feature ctrl
						if(modifyFeatureControl != null)
							modifyFeatureControl.displayClass="olControlModifyFeatureDisable";
						if(panelEdition != null)
							try{panelEdition.redraw();}catch(err){}
					}
					
					//Refresh content of manual perimeter
					//Is normally based on the state of addPerimeterButton. User had to click it before
					//now it's auto-triggered.
					if(document.getElementById('addPerimeterButton').style.display == 'block')
						selectManualPerimeter();
					
					//Display hint when no manual selection is available
					if(document.getElementById('manualAddGeometry').style.display == 'none' &&
					   document.getElementById('manualPerimDivId').style.display == 'none'){
						document.getElementById('manualPerimHint').style.display='block';
					}else{
					        document.getElementById('manualPerimHint').style.display='none';
					}
				}
				<?php 
			} 
			?>
		}
		</script>

		<input type="hidden" size="30" id="lastSelectedPerimeterIndex"  value="0">
		<table >
		<tr>
		<td><select id="perimeterList" onChange="selectPerimeter('perimeterList', false)"  >
		<?php
		
		$q = "SELECT id FROM #__sdi_perimeter WHERE code='FREE'";
		$db->setQuery($q);
		$free_perim_id = $db->loadResult();
		
		foreach ($rows as $row)
		{
		?>
		<option value="<?php echo $row->id ?>" <?php if ($row->id == $mainframe->getUserState('perimeter_id') || $row->id == JRequest::getVar('perimeter_id')) { echo 'SELECTED';};?>><?php echo JText::_($row->name); ?>
		</option>
		<?php
		}
		?>
		</select></td>
		</tr>
		<tr>
		<td>
		<!--
		<fieldset>
		<legend><?php echo JText::_('SHOP_PERIMETER_PERIMETER_SELECTION');?></legend>
		-->
		<?php
		    $index = JRequest::getVar('tabIndex',0);
		    $tabs =& JPANE::getInstance('Tabs', array('startOffset' => $index));
		    echo $tabs->startPane("selectPane");
                    echo $tabs->startPanel(JText::_("SHOP_PERIMETER_PERIMETER_GRAPHIC_MODE"),"graphSelectPane");
		    ?>
		    <div id="panelEdition" class="olControlEditingToolbar"></div>
		    <?php
		    echo $tabs->endPanel();
                    echo $tabs->startPanel(JText::_("SHOP_PERIMETER_PERIMETER_MANU_MODE"),"manuSelectPane");
		    ?>
		    <div id="addPerimeterButton">
		        <div id="manualPerimDivId" style="display: none">
			<?php include 'manual_perimeter.php' ;?></div>
			<div id="manualAddGeometry" style="display: none">
			<table>
			<tr>
			<td><?php echo JText::_("SHOP_PERIMETER_ADD_Y_TEXT");?></td>
			<td><input type="text" id="xText" value=""></td>
			</tr>
			<tr>
			<td><?php echo JText::_("SHOP_PERIMETER_ADD_X_TEXT");?></td>
			<td><input type="text" id="yText" value=""></td>
			</tr>
			<tr>
			   <td colspan="2" align="left">
			      <table class="perimeterActionSpaceHolder">
			         <tr>
				    <td>
			               <button title="<?php echo JText::_("SHOP_PERIMETER_ADD_GEOMETRY");?>" class="addCoordinateButton" type="button"
			                   onClick="if(isValidCoordinates()){addGeometryPerimeter();document.getElementById('xText').value='';document.getElementById('yText').value='';}">
			               </button>
				    </td>
				    <td>
			               <button title="<?php echo JText::_("SHOP_PERIMETER_MODIFY_GEOMETRY");?>" class="editCoordinateButton" type="button"
				         onClick="if(isValidCoordinates()){modifyGeometryPerimeter();document.getElementById('xText').value='';document.getElementById('yText').value='';}">
			               </button>
				    </td>
				    <td>
			               <div class="particular-loadperim-link">
				            <a title="<?php echo JText::_("SHOP_PERIMETER_LOAD_PERIMETER_FROM_LIST_LINK");?>" rel="{handler:'iframe',size:{x:600,y:600}}" href="./index.php?tmpl=component&option=com_easysdi_shop&task=loadListForPerim&perimeter_id=<?php echo $free_perim_id; ?>&Itemid=<?php echo JRequest::getVar('Itemid');?>" class="modal">&nbsp;</a>
			                </div>
			            </td>
				 </tr>
		             </table>
			   </td>
			 </tr>
			</table>
			</div>
		    </div>
		    <div id="manualPerimHint" style="display:none" class="graphSelHint"><?php echo JText::_("SHOP_PERIMETER_PERIMETER_NO_MANUAL_SELECTION");?></div>
		    <?php
		    ?>

		    <?php
		    echo $tabs->endPanel();
		    echo $tabs->endPane();
		?>
		</td>
		</tr>
		</table>
		
		<fieldset>
		<legend><?php echo JText::_('SHOP_PERIMETER_CURRENT_SELECTION');?></legend>
		<!-- Current selection -->
		<table>
		<tr>
		<td>
		<select multiple="multiple" size="6" id="selectedSurface"  	onChange="changeSelectedSurface()">
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
		</td>
		<td valign="bottom">
			<button title="<?php echo JText::_("SHOP_PERIMETER_PERIMETER_REMOVE");?>" class="deletePerimeterButton" type="button"
					onClick="removeSelected();"></button>
		</td>
		</tr>
		</table>
		</fieldset>
		<!-- Buffer -->
		<fieldset id="bufferRegion">
		<legend><?php echo JText::_('SHOP_PERIMETER_BUFFER');?></legend>
		<table>
		<tr>
		<td>
		<?php echo JText::_("SHOP_PERIMETER_BUFFER");?>
		<input type="text" id="bufferValue" size="10" value="<?php echo $mainframe->getUserState('bufferValue') ;?>" onchange="checkBufferValue()">
		<span id="bufferValueSuffix"><?php echo JText::_("SHOP_PERIMETER_BUFFER_UNIT"); ?></span></td>
		</tr>
		</table>
		</fieldset>
		
		<fieldset>
		<legend><?php echo JText::_('SHOP_PERIMETER_INFO');?></legend>
		<!-- infos -->
		<table>
		<?php
		$querySurfaceMax = "SELECT min(surfacemax) as surfacemax FROM #__sdi_product  WHERE id in (";
		$querySurfaceMin = "SELECT max(surfacemin) as surfacemin FROM #__sdi_product  WHERE id in (";
		foreach( $cid as $id )
		{
			$querySurfaceMax = $querySurfaceMax.$id."," ;
			$querySurfaceMin = $querySurfaceMin.$id."," ;
		}
		$querySurfaceMax  = substr($querySurfaceMax , 0, -1);
		$querySurfaceMin  = substr($querySurfaceMin , 0, -1);
		$querySurfaceMax = $querySurfaceMax.")";
		$querySurfaceMin = $querySurfaceMin.")";
		$db->setQuery( $querySurfaceMin);
		$rowsSurfaceMin = $db->loadObjectList();
		foreach( $rowsSurfaceMin as $surfaceMin )
		{
			echo "<tr><td>".JText::_("SHOP_PERIMETER_SURFACE_MIN")." ".renderAreaText($surfaceMin->surfacemin, true)."</td></tr>\n";
			?>
			<input type="hidden" size="10" id="totalSurfaceMin" disabled="disabled" value="<?php echo $surfaceMin->surfacemin; ?>">
			<?php

		}

		$db->setQuery( $querySurfaceMax);
		$rowsSurfaceMax = $db->loadObjectList();
		foreach( $rowsSurfaceMax as $surfaceMax )
		{
			echo "<tr><td>".JText::_("SHOP_PERIMETER_SURFACE_MAX")." ".renderAreaText($surfaceMax->surfacemax, true)."</td></tr>\n";
			?>
			<input type="hidden" size="10" id="totalSurfaceMax" disabled="disabled" value="<?php echo $surfaceMax->surfacemax; ?>">
			<?php
		}


		$totalArea = $mainframe->getUserState('totalArea');
		if (!$totalArea) $totalArea=0;
		$un = "";
		if($totalArea <= $meterToKilometerLimit)
			$un = JText::_("SHOP_PERIMETER_SURFACE_M2");
		else
			$un = JText::_("SHOP_PERIMETER_SURFACE_KM2");

		echo "<div id=\"SHOP_PERIMETER_SURFACE_SELECTED\">".JText::_("SHOP_PERIMETER_SURFACE_SELECTED")." ($un):"."</div>";?>
		<input type="hidden" size="30" id="totalSurface" disabled="disabled" value="<?php echo $totalArea; ?>">
		<tr><td><input type="text"  id="totalSurfaceDisplayed" disabled="disabled" value="<?php echo renderAreaText($totalArea, false); ?>"></td></tr>
		</table>
		</fieldset>
		
		<script><!--
				
				window.addEvent('domready', function() {
					$('xText').addEvent('keydown', function(event){
					    //catch enter key
					    if (event.keyCode == '13'){
						    if(document.getElementById('selectedSurface').options.selectedIndex == -1){
							    addGeometryPerimeter();
							    document.getElementById('xText').value='';
							    document.getElementById('yText').value='';
						    }
						    else
						    {
							    modifyGeometryPerimeter();
							    document.getElementById('xText').value='';
							    document.getElementById('yText').value='';
						    }
					    }
					});
					
					$('yText').addEvent('keydown', function(event){
					    //catch enter key
					    if (event.keyCode == '13'){
						    if(document.getElementById('selectedSurface').options.selectedIndex == -1){
							    addGeometryPerimeter();
							    document.getElementById('xText').value='';
							    document.getElementById('yText').value='';
						    }
						    else
						    {
							    modifyGeometryPerimeter();
							    document.getElementById('xText').value='';
							    document.getElementById('yText').value='';
						    }
					    }
					});
				});


				function checkBufferValue()
				{
					var bufferValue = document.getElementById('bufferValue').value;
					document.getElementById('bufferValue2').value = document.getElementById('bufferValue').value;
					if( parseFloat(bufferValue) < 0)
					{
						$("status").innerHTML = "<?php echo JText::_("SHOP_SHOP_MESSAGE_ERROR_BUFFER_VALUE"); ?>";
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
								//Search in wfs5
								var wfsFeatures = null;
								var found =false;
								if(wfs5 != null){
									wfsFeatures = wfs5.features;
									for(var j=wfsFeatures.length-1; j>=0; j--) {
										feat2 = wfsFeatures[j];                       
                                                                	
										if (idToLookFor ==  feat2.attributes[idField]){
											found=true;
                                                                	
											wfs5.removeFeatures([wfsFeatures[j]]);
											break;
										}
									}
								}
								//Search in wfs
								if(wfs != null && found == false){
									wfsFeatures = wfs.features;
									for(var j=wfsFeatures.length-1; j>=0; j--) {
										feat2 = wfsFeatures[j];                       
                                                                	
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

				}





				function drawSelectedSurface()
				{	
					var meterToKilometerLimit = <?php echo $meterToKilometerLimit;?>;
					var SHOP_PERIMETER_SURFACE_M2 = '<?php echo JText::_("SHOP_PERIMETER_SURFACE_M2");?>';
					var SHOP_PERIMETER_SURFACE_KM2 = '<?php echo JText::_("SHOP_PERIMETER_SURFACE_KM2");?>';
					var SHOP_PERIMETER_SURFACE_SELECTED = '<?php echo JText::_("SHOP_PERIMETER_SURFACE_SELECTED");?>';
					var elSel = document.getElementById('selectedSurface');
					var features = vectors.features;
					if (features.length == 0 && elSel.options.length == 1) 
					{
						vectors.addFeatures([new OpenLayers. Feature. Vector(new OpenLayers.Geometry.Point())]);
					}
					
					else if (features.length == 0 && elSel.options.length > 1) 
					{
						vectors.addFeatures([new OpenLayers. Feature. Vector(new OpenLayers.Geometry.Polygon())]);
					}
					
					var feature= features[features.length-1];
					var selectedComponents = new Array();
					if (feature)
					{	
						if(elSel.options.length == 1){
							var curValue = elSel.options[0].value;
							var x= curValue.substring(0,curValue .indexOf(" ", 0));
							var y= curValue.substring(curValue .indexOf(" ", 0)+1,curValue .length);
							var newPoint = new OpenLayers.Geometry.Point(x,y);
							feature = new OpenLayers. Feature. Vector(newPoint);

						}
						else if (elSel.options.length > 1) 
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
						}
															  			
						vectors.removeFeatures(features);				
						if(elSel.options.length != 0)
							vectors.addFeatures([feature]);
						if(feature.geometry.bounds && !fromZoomEnd)
							map.zoomToExtent(feature.geometry.bounds); 
						if(feature.geometry.components){
							if (feature.geometry.components[0].components.length > 2)
							{
								featureArea = feature.geometry.getArea();
							}
							else
							{
								featureArea = 0;
							}
						}
						else
						{
							featureArea = 0;
						}
						document.getElementById('totalSurface').value =  parseFloat(featureArea );
						document.getElementById('SHOP_PERIMETER_SURFACE_SELECTED').innerHTML = featureArea <= meterToKilometerLimit ? SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_M2+"):" : SHOP_PERIMETER_SURFACE_SELECTED+" ("+SHOP_PERIMETER_SURFACE_KM2+"):";
						document.getElementById('totalSurfaceDisplayed').value = featureArea <= meterToKilometerLimit ? parseFloat( parseFloat(featureArea)).toFixed(<?php echo $areaPrecision; ?> ) : parseFloat( parseFloat(featureArea/1000000)).toFixed(<?php echo $areaPrecision; ?> );
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
										wfsRegisterEvents(wfs, 0);
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
						var xVal = document.getElementById("xText").value;
						var yVal = document.getElementById("yText").value;
						
						
						//Numeric test
						if(!isNumeric(xVal) || !isNumeric(yVal)){
							alert('<?php echo JText::_("SHOP_PERIMETER_MESSAGE__ERROR_COORD_INVALID");?>');
							return false;
						}else{
							xVal = parseFloat(xVal).toFixed(<?php echo $decimal_precision; ?>);
							yVal = parseFloat(yVal).toFixed(<?php echo $decimal_precision; ?>);
						}
						
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

						drawSelectedSurface();	
					}
				}
				
				function isValidCoordinates(){
					var xVal = document.getElementById("xText").value;
					var yVal = document.getElementById("yText").value;
					
					if(!isNumeric(xVal)){
						alert('<?php echo JText::_("SHOP_PERIMETER_MESSAGE__ERROR_COORDX_INVALID");?>' + ' ' + document.getElementById("xText").value);
						return false;
					}
					else if (!isNumeric(yVal))
					{
						alert('<?php echo JText::_("SHOP_PERIMETER_MESSAGE__ERROR_COORDY_INVALID");?>' + ' '+  document.getElementById("yText").value);
						return false;
					}
					else{
						return true;
					}
				}
				
				function isNumeric(str){
					var val = /^[-+]?[0-9]+(\.[0-9]+)?$/.test(str);
					return val;
				} 

				function alltrim(str) {
					return str.replace(/^\s+|\s+$/g, '');
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
								elSel.options[i].value = parseFloat(document.getElementById("xText").value).toFixed(<?php echo $decimal_precision; ?>) +" "+ parseFloat(document.getElementById("yText").value).toFixed(<?php echo $decimal_precision; ?>);	
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

				//Manual selection starts here
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
				-->
				</script>

				
					<?php
	}
}else{

	if ($curstep > 2){
		$db =& JFactory::getDBO();
		$bufferValue = $mainframe->getUserState('bufferValue');
		$query = "select * from #__sdi_basemap where `default` = 1";
		$db->setQuery( $query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		$decimal_precision = $rows[0]->decimalprecision;
		$totalArea = $mainframe->getUserState('totalArea');
		if (!$totalArea) $totalArea=0;
		?>
		<div>


		<?php
				$perimeter_id = $mainframe->getUserState('perimeter_id');
				$queryPerimeter = "SELECT name FROM #__sdi_perimeter WHERE id =$perimeter_id";
				$db->setQuery($queryPerimeter);
				$perimeter_name = $db->loadResult();
				if ($db->getErrorNum()) {
					echo "<div class='alert'>";
					echo 			$db->getErrorMsg();
					echo "</div>";
				}
				?>
				<div>
				<h4><?php 
						echo $perimeter_name;
				?></h4>
				</div>
				<?php
						$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');
						if ($selSurfaceListName!=null){
							foreach ($selSurfaceListName as $sel){

								echo $sel."<br>";

							}
						}
						?>
						</div>
						<div>
						<h5>
						<?php
								echo JText::_("SHOP_PERIMETER_SURFACE_TOTALE")." ".renderAreaText($totalArea, true);
						?></h5>
						</div>
						<div>
						<h5>
						<?php
								if ($bufferValue>0){
									echo JText::_("SHOP_PERIMETER_BUFFER_LABEL")." ".$bufferValue." ".JText::_("SHOP_PERIMETER_BUFFER_UNIT") ;
								}
						?>
						</h5>
						</div>
						<?php
	}
}

function renderAreaText($surfSqMtr, $langSuffix){
	global  $areaPrecision;
	global  $meterToKilometerLimit;
	$text = "";
	//display m2
	if($surfSqMtr <= $meterToKilometerLimit){
		$text = round($surfSqMtr,$areaPrecision);
		if($langSuffix)
			$text .=" ".JText::_("SHOP_PERIMETER_SURFACE_M2") ;
	}
	//display km2
	else if($surfSqMtr > $meterToKilometerLimit){
		$text = round($surfSqMtr/1000000,$areaPrecision);
		if($langSuffix)
			$text .=" ".JText::_("SHOP_PERIMETER_SURFACE_KM2") ;
	}
	return $text;
}

function renderArea($surfSqMtr){
	global $meterToKilometerLimit;

}


?>
