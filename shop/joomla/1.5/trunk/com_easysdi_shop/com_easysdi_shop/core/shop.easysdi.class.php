<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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

//foreach($_POST as $key => $val) 
//echo '$_POST["'.$key.'"]='.$val.'<br />';
defined('_JEXEC') or die('Restricted access');


class HTML_shop {

	function deleteProduct()
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		$id = JRequest::getVar('prodId');
		$option = JRequest::getVar('option');

		$productList = $mainframe->getUserState('productList');
		$newProductList = array ();
		
		if (is_array($productList))
		{
			foreach ($productList as $key => $value)
			{
				if ($value != $id)
				{
					$newProductList[]= $value;
				}
				if ($value == $id)
				{
					$query = "SELECT  a.code as code FROM #__easysdi_product_property b, #__easysdi_product_properties_definition  as a   WHERE a.id = b.property_value_id  and b .product_id = ". $id." order by a.order";
					$db->setQuery( $query );
					$rows = $db->loadObjectList();
					
					foreach($rows as $row){
					$property = $mainframe->getUserState($row->code.'_text_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->code.'_text_property_'.$id);
					
					$property = $mainframe->getUserState($row->code.'_textarea_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->code.'_textarea_property_'.$id);
					
					$property = $mainframe->getUserState($row->code.'_list_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->code.'_list_property_'.$id);
					
					$property = $mainframe->getUserState($row->code.'_mlist_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->code.'_mlist_property_'.$id);
					
					$property = $mainframe->getUserState($row->code.'_cbox_property_'.$id);
					unset ($property);
					$mainframe->setUserState($row->code.'_cbox_property_'.$id);
					}
				}
			}
			
		}
		$mainframe->setUserState('productList',$newProductList);

	}

	function orderPerimeter ($cid){
	?>
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
	<script type="text/javascript" src="./administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js"></script>
	<script><!--
	var map;
	var wfs=null;
	var wfs3=null;
	var vectors;
	var nameField;
	var idField;
	var areaField;
	var layerPerimeter;
	var wfsUrl ;
	var isFreeSelectionPerimeter = false;
	
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
	
	function initSelectedSurface(){
		var elSel = document.getElementById("selectedSurface");
		while (elSel.length > 0)
		{
			elSel.remove(elSel.length - 1);
		}
		document.getElementById('totalSurface').value = 0;
					document.getElementById('totalSurfaceDisplayed').value =  0;    		
		removeSelection();
	}
	function removeSelection(){
		if (vectors){
	
			var features = vectors.features;
			vectors.removeFeatures(features);
			//vectors.drawFeature (feature);
			/*for (var i = features.length-1;i>=0;i--){ 		
			var feature= features[i];
			if (features.length>=0){
				if (feature){				
			
				features = vectors.features;	
				vectors.addFeatures([feature]);
				vectors.drawFeature (feature);		
				features = vectors.features;			
			}
			}
		}*/
	}
	}
	function selectWFSPerimeter(perimId,perimName,perimUrl,featureTypeName,name,id,area,wmsUrl,layerName, imgFormat, pMinResolution , pMaxResolution){
	
		
		document.getElementById('perimeter_id').value = perimId;
		//freeSelectPerimeter();
		document.getElementById('manualPerimDivId').style.display='none';
		document.getElementById('manualAddGeometry').style.display='none';
		
			
		//Delete the current selection
		initSelectedSurface();
		
		        						 
		nameField = name;
		idField = id;
		areaField =area;
	
		
	
		if (wfs) {
			map.removeLayer(wfs);
			wfs.destroy();				
			wfs=null;		
		}
		if (wfs3) {	
			wfs3.destroy();				
			wfs3=null;		
		}
	
	
		if (layerPerimeter){
					map.removeLayer(layerPerimeter);
					layerPerimeter=null;
			}
		if (vectors){
			var features = vectors.features;
			vectors.removeFeatures(features)
		}
	
		if (perimUrl.length ==0 && wmsUrl.length ==0){
			//Free selection permiter.
			isFreeSelectionPerimeter = true;
		
		}else{
		
		isFreeSelectionPerimeter = false;
	
		if (wmsUrl.length > 0){
		
			layerPerimeter = new OpenLayers.Layer.WMS(perimName,
	                    wmsUrl,
	                    {layers: layerName, format : imgFormat  ,transparent: "true"},                                          
	                     {singleTile: true},                                                    
	                     {                     
						  minResolution: pMinResolution,
	               		  maxResolution: pMaxResolution,                                    	     
	                      maxExtent: map.maxExtent,
	                      projection: map.projection,
	                      units: map.units,
	                      transparent: "true"
	                     }
	                    );
	                 layerPerimeter.alwaysInRange=false;  
	                    
	                 map.addLayer(layerPerimeter);      
	    
	    wfsUrl = perimUrl+'?request=GetFeature&SERVICE=WFS&TYPENAME='+featureTypeName+'&VERSION=1.0.0';
	  
		}else{
		
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
		                    extractAttributes: false
		                       
		                },
	                { featureClass: OpenLayers.Feature.WFS}
		                 );
		
		
		wfs.events.register("loadstart", null, function() { $("status").innerHTML = "<?php echo JText::_("EASYSDI_LOADING_THE_PERIMETER") ?>"; })
		wfs.events.register("loadend", null, function() { $("status").innerHTML = ""; intersect();})
		
		map.addLayer(wfs);
		 }
		}
		
	}
	
	            
	function initMap(){
	 //OpenLayers.ProxyHost="components/com_easysdi_shop/proxy.php?url=";
	 
	 //OpenLayers.ProxyHost="index.php?option=com_easysdi_shop&no_html=1&task=proxy&url=";
	
	
	
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
	
	$decimal_precision = $rows[0]->decimalPrecisionDisplayed;
	?>
					map = new OpenLayers.Map('map', {
	                projection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"), 
					displayProjection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
	                units: "<?php echo $rows[0]->unit; ?>",
	                minResolution: <?php echo $rows[0]->minResolution; ?>,
	                maxResolution: <?php echo $rows[0]->maxResolution; ?>,    
	                maxExtent: new OpenLayers.Bounds(<?php echo $rows[0]->maxExtent; ?>),
	                controls: []
					<?php
						if($rows[0]->restrictedExtent == '1') echo  ",restrictedExtent: new OpenLayers.Bounds(".$rows[0]->maxExtent.")\n"
				    ?>
					<?php
						if($rows[0]->restrictedScales != '') echo  ",scales: [".$rows[0]->restrictedScales."]\n"
				    ?>
	            });
					  
					map.addControl(new OpenLayers.Control.MousePosition({ 
					div: document.getElementById("mouseposition"),
					prefix: '', 
					suffix: '', 
					separator: ' / ', 
					numDigits: 0
					}));
					
					
					 baseLayerVector = new OpenLayers.Layer.Vector(
	                "BackGround",
	                {isBaseLayer: true,transparent: "true"}
	            ); 
					  map.addLayer(baseLayerVector);
	
	
	<?php
	
	$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rows[0]->id." order by ordering"; 
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
					  
					layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
					
						<?php 
						if ($row->user != null && strlen($row->user)>0){
							//if a user and password is requested then use the joomla proxy.
							$proxyhost = config_easysdi::getValue("PROXYHOST");
							$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
							echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
						}else{	
							//if no user and password then don't use any proxy.					
							echo "\"$row->url\",";	
						}					
						?>
						
	                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
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
	                 map.addLayer(layer<?php echo $i; ?>);
	<?php 
	$i++;
	
	} ?>                    
				 
					map.events.register("zoomend", null, 
										function() { 
													document.getElementById('previousExtent').value = map.getExtent().toBBOX();
													//$("scale").innerHTML = "<?php echo JText::_("EASYSDI_MAP_SCALE") ?>"+map.getScale().toFixed(0);
													$("scale").innerHTML = document.getElementById('previousExtent').value ;
													 text = "";
								
													for (i=0; i<map.layers.length ;i++){
															if (map.getScale() < map.layers[i].maxScale || map.getScale() > map.layers[i].minScale){
															 text =text + map.layers[i].name + "<?php echo JText::_("EASYSDI_OUTSIDE_SCALE_RANGE") ?>" +" ("+map.layers[i].minScale+"," + map.layers[i].maxScale +")<BR>";
															} 
														}
														$("scaleStatus").innerHTML = text;
													}
										)
	                
	               	//map.addControl(new OpenLayers.Control.LayerSwitcher());
	                //map.addControl(new OpenLayers.Control.Attribution());       
	                
	                                         
	            	vectors = new OpenLayers.Layer.Vector("Vector Layer",{isBaseLayer: false,transparent: "true"});
	                map.addLayer(vectors);
					$("scale").innerHTML = "<?php echo JText::_("EASYSDI_MAP_SCALE") ?>"+map.getScale().toFixed(0);
					
					function OpenLayerCtrlClicked(ctrl, evt, ctrlPanel, otherPanel){
						if(ctrl != null){
							//If type buuton, trigger it directly
							if(ctrl.type == OpenLayers.Control.TYPE_BUTTON){
								ctrl.trigger();
							}
							//else deactivate buttons from both panels
							else
							{				
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
							//display edit shap only if freeselction is active
							/*
							var selectedOption = document.getElementById("perimeterList").options[document.getElementById("perimeterList").selectedIndex].text;
							if(selectedOption == 'Périmètre libre')
								panelEdition.controls[0].div.style.visibility = "hidden";
							else
								panelEdition.controls[0].displayClass='';
						*/
							}
					}
					
					
				<?php
					if ( JRequest::getVar('previousExtent') != "")
					{
				?>
					map.zoomToExtent(new OpenLayers.Bounds(<?php echo JRequest::getVar('previousExtent'); ?>) );
				<?php
					}
					else
					{
				?>
					map.zoomToMaxExtent();
				<?php	
					}
				?>
	             
				/*zb = new OpenLayers.Control.ZoomBox();
				md = new OpenLayers.Control.MouseDefaults();
				zo =  new OpenLayers. Control. ZoomOut();*/
				
				// var containerPanel = document.getElementById("panelDiv");
	            // var panel = new OpenLayers.Control.Panel({div: containerPanel});
				
				//enabling navigation history
        	    navHistory = new OpenLayers.Control.NavigationHistory();
        	    map.addControl (navHistory);
				navHistory.previous.title='<?php echo JText::_("EASYSDI_TOOL_NAVPREVIOUS_HINT") ?>';
 	    	    navHistory.next.title='<?php echo JText::_("EASYSDI_TOOL_NAVNEXT_HINT") ?>';
				  
				//Zoom in
				oZoomBoxInCtrl = new OpenLayers.Control.ZoomBox({
        	    title: '<?php echo JText::_("EASYSDI_TOOL_ZOOMIN_HINT") ?>'
				});
				
				//Zoom out
				oZoomBoxOutCtrl = new OpenLayers.Control.ZoomBox({
        	    out: true, displayClass: "olControlZoomBoxOut",
        	    title: '<?php echo JText::_("EASYSDI_TOOL_ZOOMOUT_HINT") ?>'
				});
				
				//Pan
				oDragPanCtrl = new OpenLayers.Control.DragPan({
        	    title: '<?php echo JText::_("EASYSDI_TOOL_PAN_HINT") ?>'
				});
				
				//Zoom to full extends
				oZoomMxExtCtrl = new OpenLayers.Control.ZoomToMaxExtent({
        	    map: map, title: '<?php echo JText::_("EASYSDI_TOOL_MAXEXTENT_HINT") ?>'
				});
				
				/*
					OpenLayers Edition controls
				*/
							
				rectControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});		
				rectControl.title = '<?php echo JText::_("EASYSDI_TOOL_RECTCTRL_HINT") ?>';
				rectControl.featureAdded = function() { intersect();};												
				rectControl.handler.setOptions({irregular: true});                                  
	            rectControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("EASYSDI_TOOL_REC_ACTIVATED") ?>"; })
	            
	            
	            //Polygonal  bounding box selection
	            polyControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Polygon,{'displayClass':'olControlDrawFeaturePolygon'});
				polyControl.title = '<?php echo JText::_("EASYSDI_TOOL_POLYCTRL_HINT") ?>';
	            polyControl.featureAdded = function() { intersect();};
				polyControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("EASYSDI_TOOL_POLY_ACTIVATED") ?>"; })
			
				//Point selection
	            pointControl = new OpenLayers.Control.DrawFeature(vectors, OpenLayers.Handler.Point,{'displayClass':'olControlDrawFeaturePoint'});
                pointControl.title = '<?php echo JText::_("EASYSDI_TOOL_POINTCTRL_HINT") ?>';
				pointControl.featureAdded = function() { intersect();};
				pointControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("EASYSDI_TOOL_POINT_ACTIVATED") ?>"; })            
	         
				//Modify feature shape  
	            modifyFeatureControl = new OpenLayers.Control.ModifyFeature(vectors,{'displayClass':'olControlModifyFeature'});
				modifyFeatureControl.title = '<?php echo JText::_("EASYSDI_TOOL_MODFEATURE_HINT") ?>';
				modifyFeatureControl.events.register("activate", null, function() { $("toolsStatus").innerHTML = "<?php echo JText::_("EASYSDI_TOOL_MODIFY_ACTIVATED") ?>"; })
				
				vectors.events.on({
	                "afterfeaturemodified": intersect                
	            });
	
	
	            /*
					Container panel for standard controls
				*/
				var panelEdition;
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
	
	function intersect() 
	{
	  if (isFreeSelectionPerimeter)
	  {
	  	 var features = vectors.features; 
	     var feature = features[features.length-1];
	     
	     document.getElementById("selectedSurface").options.length=0;
	     
	     if (feature.geometry.components[0].components.length > 2)
	     {
	   		 featureArea = feature.geometry.getArea();
	    }else{
	    	featureArea = 0;
	    }
		document.getElementById('totalSurface').value =  parseFloat(featureArea );
		document.getElementById('totalSurfaceDisplayed').value =  parseFloat(featureArea ).toFixed(<?php echo $decimal_precision; ?> );    		
	     
	     if (feature.geometry instanceof OpenLayers.Geometry.Polygon){
	     	
	     	var polygonSize = feature.geometry.components[0].components.length;
	     	
	     	var components = feature.geometry.components[0].components;
	     	
	     	var i = 0;
	     	while (i< polygonSize){
	     	
	     	document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
				new Option(components [i].x.toFixed(<?php echo $decimal_precision; ?>) +" / "+components [i].y.toFixed(<?php echo $decimal_precision; ?>),components [i].x +" "+components [i].y);
				i++;
	     	}          
	     
	     }
		if (feature.geometry instanceof OpenLayers.Geometry.Point){
	         	 		 	 
	     document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(featureArea );                         
	   	 document.getElementById('totalSurfaceDisplayed').value =  (parseFloat(document.getElementById('totalSurface').value) + parseFloat(featureArea ) ).toFixed(<?php echo $decimal_precision; ?> );    		
	   	 document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
				new Option(feature.geometry,feature.geometry);
	}      
		drawSelectedSurface();	 
	 }else{
		  $("status").innerHTML = "<?php echo JText::_("EASYSDI_LOADING_THE_PERIMETER") ?>"; 
	
	   var features = vectors.features;
	          var gmlOptions = {
	                featureType: "feature",
	                featureNS: "http://example.com/feature"
	            };
	
		gml = new OpenLayers. Format. GML.v2(gmlOptions);
	
		//for(var i=features.length-1; i>=0; i--) {
	        feature = features[features.length-1];
	        var doc = format.read(gml.write(feature, true));               
			
			if (feature.geometry instanceof OpenLayers.Geometry.Polygon){
				wfsUrlWithFilter = wfsUrl+ '&FILTER='+escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc"><ogc:Intersect><ogc:PropertyName>msGeometry</ogc:PropertyName>'+getElementsByTagNameNS(doc,'http://www.opengis.net/gml', 'Polygon')+'</ogc:Intersect></ogc:Filter>');
								
			}
			
			if (feature.geometry instanceof OpenLayers.Geometry.Point){
				wfsUrlWithFilter = wfsUrl+'&FILTER='+escape('<ogc:Filter xmlns:ogc="http://www.opengis.net/ogc"><ogc:Intersect><ogc:PropertyName>msGeometry</ogc:PropertyName>'+getElementsByTagNameNS(doc,'http://www.opengis.net/gml', 'Point')+'</ogc:Intersect></ogc:Filter>');		
			}
			
			if (!wfs) {				
			//	wfs.destroy();
		     wfs = new OpenLayers.Layer.Vector("selectedFeatures", {
	                    strategies: [new OpenLayers.Strategy.Fixed()],
	                    protocol: new OpenLayers.Protocol.HTTP({
	                        url: wfsUrlWithFilter,
	                        format: new OpenLayers.Format.GML()
	                    })
	                });		    	            
		
	
				 wfsRegisterEvents();
				 map.addLayer(wfs);			
				}else{
				    
				  wfs2 = new OpenLayers.Layer.Vector("selectedFeatures", {
	                    strategies: [new OpenLayers.Strategy.Fixed()],
	                    protocol: new OpenLayers.Protocol.HTTP({
	                        url: wfsUrlWithFilter,
	                        format: new OpenLayers.Format.GML()                                                
	                    })});	
	                
	                
	                wfs2.events.register("featureadded", null, function(myEvent) { 
	                $("status").innerHTML = "";
					removeSelection();
					
					
					var wfsFeatures = wfs.features;
	
					// look for a feature with the same id
					var idToLookFor = myEvent.feature.attributes[idField];
					var found = false;
					for(var j=wfsFeatures.length-1; j>=0; j--) {
	                    feat2 = wfsFeatures[j];                       
	                      
	                       if (idToLookFor == feat2.attributes[idField]){
	                       	found=true;
	                       
	                       	wfs.removeFeatures([wfsFeatures[j]]);
	                       	break;
	                       }
	                       }
	                       
	                       if (!found){
	                       	wfs.addFeatures([myEvent.feature]);
	                       }
				});
	            map.addLayer(wfs2);		
	            map.removeLayer(wfs2);    
	                }
					
	              
	   // }
	 }
	 
	return;                     
	}
	function wfsRegisterEvents()			
	 {
	 
	 //	wfs.events.register("loadstart", null, function() { $("status").innerHTML = "<?php echo JText::_("EASYSDI_LOADING_THE_PERIMETER") ?>"; })
	//	wfs.events.register("loadend", null, function() { $("status").innerHTML = ""; })
		  
	 			wfs.events.register("featureremoved",null,function(event){
				
				        feat2 = event.feature;
	                    var name = feat2.attributes[nameField];
	                    //var id = document.getElementById('perimeter_id').value +"."+feat2.attributes[idField];
	                    var id = feat2.attributes[idField];   
	            		var area = feat2.attributes[areaField];
	            		var featArea = 0;	
	            		if (areaField.length > 0 && area){
	            					featArea = area; 
	            			}else {
	            					featArea = feat2.geometry.getArea();
	            			}
				
							var found = -1;
													
							for (var k=document.getElementById("selectedSurface").options.length-1;k>=0;k--){
															
								if (document.getElementById("selectedSurface").options[k].value ==  id){
									//Remove the value							
									document.getElementById("selectedSurface").remove(k);								
									document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) - parseFloat(featArea);
									document.getElementById('totalSurfaceDisplayed').value = (parseFloat(document.getElementById('totalSurface').value) - parseFloat(featArea)).toFixed(<?php echo $decimal_precision; ?>);
																									
									found=k;																			
								}            				
							}						                
	            }
	            
				
				);
				 
			wfs.events.register("featureadded", null, function(event) { 
				removeSelection();
				$("status").innerHTML = "";
				    		
	              feat2 = event.feature;
	              var name = feat2.attributes[nameField];
	              //var id = document.getElementById('perimeter_id').value +"."+feat2.attributes[idField];
	              var id = feat2.attributes[idField];
	                       
	              var area = feat2.attributes[areaField];
	              var featArea = 0;	
	            	if (areaField.length > 0 && area){
	            					featArea = area; 
	            				}else {
	            					featArea = feat2.geometry.getArea();
	            				}
	
							
	                       	
	                       	//Add the new value
		            document.getElementById('totalSurface').value = parseFloat(document.getElementById('totalSurface').value) + parseFloat(featArea);                       	                       	                         
		            document.getElementById('totalSurfaceDisplayed').value = (parseFloat(document.getElementById('totalSurface').value) + parseFloat(featArea)).toFixed(<?php echo $decimal_precision; ?>);
		   		    document.getElementById("selectedSurface").options[document.getElementById("selectedSurface").options.length] = 
	            	new Option(name,id);
	            			
	            }
	            
				 //}
				 );
	 }
	 
	var oldLoad = window.onload;
	window.onload=function(){
	initMap();
	selectPerimeter('perimeterList');
	if (oldLoad) oldLoad();}
	--></script>
	
	<div id="map" class="smallmap"></div>
	
	<hr>
	
	<table width="100%">
	 <tr>
	  <td width="40" rowspan=2>
	    <div id="infoLogo" class="shopInfoLogo"/>
	  </td>
	  <td width="120">
	    <div id="scale"/>
	  </td>
	  <td>
	    <div id="scaleStatus"/>
	  </td>
	  <td width="100" align="right">
	    <?php echo JText::_("EASYSDI_MAP_COORDINATE") ?>
	  </td>
	  <td width="100" align="right">
	    <div id="mouseposition"></div>
	  </td>
	 </tr>
	 <tr>
	  <td colspan=4>
	    <div id="toolsStatus"> <?php echo JText::_("EASYSDI_TOOL_NOTHING_ACTIVATED") ?> </div>
	  </td>
	 </tr>
	 <tr>
	  <td colspan=4>
	    <div id="status"/>
	  </td>
	 </tr>
	</table>
	
	
	<div id="docs"></div>
	<br>
	<div id="panelDiv" class="historyContent"></div>
	
	<br>
	
	<?php 	$step = JRequest::getVar('step',"2");
	$option = JRequest::getVar('option');
	$task = JRequest::getVar('task');
	
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
		     			//alert (i+" "+j+" "+lines[i].intersects (lines[j]));		
	     			}
	     			if (count > 2) {
	     				//More than 2 intersectios for a line, mean that a line intersects another one.
	     				alert("<?php echo JText::_("EASYSDI_SELF_INTERSECTING_POLYGON"); ?>");	     			
		     			return true;
	     			}     			     				     		
	     		}
	     
	     		
	     	}     	
	     }
	     
	     return false;
	}
	
	 function submitOrderForm(){ 	
	 	var selectedSurface = document.getElementById('selectedSurface');
	 	
	 	if (document.getElementById('step').value == 3 && 
	 			isSelfIntersect()==true){
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
			 }
			document.getElementById('totalArea').value=document.getElementById('totalSurface').value;
		 	
		 	var totalArea = document.getElementById('totalArea').value;
		 	var selectedSurfaceMin = document.getElementById('totalSurfaceMin').value;
		 	var selectedSurfaceMax = document.getElementById('totalSurfaceMax').value;
		 	 
		 	if ( (parseFloat(totalArea) > parseFloat(selectedSurfaceMax))|| (parseFloat(totalArea) < parseFloat(selectedSurfaceMin)))
		 	{
		 		alert("<?php echo JText::_("EASYSDI_BAD_AREA"); ?>");
		 		return ;
		 	}
		 	
		 	var bufferValue = document.getElementById('bufferValue').value;
			if( parseFloat(bufferValue) < 0)
			{
		 		alert("<?php echo JText::_("EASYSDI_MESSAGE_ERROR_BUFFER_VALUE"); ?>");
		 		return ;
		 	}
		 	
		 	
		 	document.getElementById('bufferValue2').value = document.getElementById('bufferValue').value; 
		 	document.getElementById('orderForm').submit();
		 }
		 else 
		 {
		 	if (document.getElementById('step').value == 1){
		 		document.getElementById('orderForm').submit();
		 		 			
		 	}else
		 	{
	 			alert("<?php echo JText::_("EASYSDI_NO_SELECTED_DATA"); ?>");
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
		<input type='hidden' id='previousExtent' name='previousExtent' value="" />
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
			<td><?php echo JText::_("EASYSDI_DATA_IDENTIFICATION");?></td>
			<td><?php echo JText::_("EASYSDI_ORGANISATION_NAME");?></td>
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
				href='index.php?option=com_easysdi_core&task=showMetadata&id=<?php echo $product->metadata_id;?>'><?php echo $product->data_title; ?></a>
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

	function orderProperties($cid){

		global  $mainframe;
		$db =& JFactory::getDBO();

		$step = JRequest::getVar('step',"2");
		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$cid = $mainframe->getUserState('productList');
		?>


<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>
<form name="orderForm" id="orderForm"
	action='<?php echo JRoute::_("index.php") ?>' method='POST'><?php


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
		//$query = "SELECT  a.id as id,a.order as property_order, a.mandatory , a.text as property_text ,a.type_code,a.code FROM #__easysdi_product_property b, #__easysdi_product_properties_definition  as a   WHERE a.id = b.property_value_id  and b .product_id = ". $product->id." and a.published=1 order by a.order"
		$query = "SELECT DISTINCT pd.id as id, pd.order as property_order, pd.mandatory as mandatory, pd.text as property_text, pd.type_code as type_code, pd.code as code FROM #__easysdi_product_property p inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id where p.product_id=".$product->id." and pd.published=1 order by property_order";
?>


<fieldset><legend><?php echo JText::_($product->data_title);?></legend> <?php
$db->setQuery( $query );
$rows = $db->loadObjectList();
		
?>
<ol>

<?php

if (count($rows)>0){
	foreach ($rows as $row){
		echo "<li>";
		switch($row->type_code){
			case "list":
				echo 	JText::_($row->property_text);

				//$query = "SELECT  b.order as value_order, b.text as value_text ,b.id as value FROM #__easysdi_product_properties_values_definition  as b where b.properties_id  =".$row->id." order by  b.order";
				$query="SELECT pvd.order as value_order, pvd.text as value_text, pvd.id as value FROM #__easysdi_product_property p inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id where p.product_id=".$product->id." and pd.published=1 and pd.id=".$row->id." order by value_order";
				
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
				
								echo "<select name='".$row->code."_list_property_".$product->id."[]'>";
				foreach ($rowsValue as $rowValue){
					

					$selProduct = $mainframe->getUserState($row->code.'_list_property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="selected";
					}
					echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_text)."</option>";
				}
				echo "</select>";
				break;
			case "mlist":
				echo 	JText::_($row->property_text);

				//$query = "SELECT  b.order as value_order, b.text as value_text ,b.id as value FROM #__easysdi_product_properties_values_definition  as b where b.properties_id  =".$row->id." order by  b.order";
				$query="SELECT pvd.order as value_order, pvd.text as value_text, pvd.id as value FROM #__easysdi_product_property p inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id where p.product_id=".$product->id." and pd.published=1 and pd.id=".$row->id." order by value_order";
				
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();


				echo "<select multiple size='10' name='".$row->code."_mlist_property_".$product->id."[]'>";
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->code.'_mlist_property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="selected";
					}
					echo "<option ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_text)."</option>";
				}
				echo "</select>";
				break;
			case "cbox":
				echo 	JText::_($row->property_text);

				//$query = "SELECT  b.order as value_order, b.text as value_text ,b.id as value FROM #__easysdi_product_properties_values_definition  as b where b.properties_id  =".$row->id." order by  b.order";
				$query="SELECT pvd.order as value_order, pvd.text as value_text, pvd.id as value FROM #__easysdi_product_property p inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id where p.product_id=".$product->id." and pd.published=1 and pd.id=".$row->id." order by value_order";
				
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();



				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->code.'_cbox_property_'.$product->id);
					$selected = "";
					if ( is_array($selProduct)){
						if (in_array($rowValue->value,$selProduct)) $selected ="checked";
					}


					echo "<input type='checkbox' name='".$row->code."_cbox_property_".$product->id."[]' ".$selected." value='".$rowValue->value."'>". JText::_($rowValue->value_text)."<br>";
				}

				break;
			case "text":
				echo 	JText::_($row->property_text);

				//$query = "SELECT  b.order as value_order, b.text as value_text ,b.id as value FROM #__easysdi_product_properties_values_definition  as b where b.properties_id  =".$row->id." order by  b.order";
				$query="SELECT pvd.order as value_order, pvd.text as value_text, pvd.id as value FROM #__easysdi_product_property p inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id where p.product_id=".$product->id." and pd.published=1 and pd.id=".$row->id." order by value_order";
				
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
					
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->code.'_text_property_'.$product->id);

					if (count($selProduct)>0){
						$selected = $selProduct[0];
					}else{
						$selected = $rowValue->value_text;
					}
					echo "<input type='text' name='".$row->code."_text_property_".$product->id."[]'  value='$selected'>";
					break;
				}

				break;
				
				
			case "textarea":
				echo 	JText::_($row->property_text);

				//$query = "SELECT  b.order as value_order, b.text as value_text ,b.id as value FROM #__easysdi_product_properties_values_definition  as b where b.properties_id  =".$row->id." order by  b.order";
				$query="SELECT pvd.order as value_order, pvd.text as value_text, pvd.id as value FROM #__easysdi_product_property p inner join #__easysdi_product_properties_values_definition pvd on p.property_value_id=pvd.id inner join #__easysdi_product_properties_definition pd on pvd.properties_id=pd.id where p.product_id=".$product->id." and pd.published=1 and pd.id=".$row->id." order by value_order";
				
				$db->setQuery( $query );
				$rowsValue = $db->loadObjectList();
					
				foreach ($rowsValue as $rowValue){
					$selProduct = $mainframe->getUserState($row->code.'_textarea_property_'.$product->id);
									
					if (count($selProduct)>0){
						$selected = $selProduct[0];
					}else{
						$selected = $rowValue->value_text;
					}

					echo "<TEXTAREA  rows=10 COLS=40 name='".$row->code."_textarea_property_".$product->id."[]'>$selected</textarea>";
					break;
				}

				break;
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


	function orderDefinition($cid){


		global  $mainframe;
		$db =& JFactory::getDBO();

		$step = JRequest::getVar ('step',4 );
		$option = JRequest::getVar ('option' );
		$task = JRequest::getVar ('task' );

		?>

		<?php
		$user = JFactory::getUser();
		if (!$user->guest){
			?>
<div class="info"><?php echo JText::_("EASYSDI_CONNECTED_WITH_USER").$user->name;  ?></div>
			<?php
		}
		?>
<script>
 function submitOrderForm(){
 if (!document.getElementById('order_type_d').checked && !document.getElementById('order_type_o').checked){
 
 	alert("<?php echo JText::_("EASYSDI_ORDER_TYPE_NOT_FILL") ?>");
 	return;
 }
 if (document.getElementById('order_name').value.length == 0){
 
 	alert("<?php echo JText::_("EASYSDI_ORDER_NAME_NOT_FILL") ?>");
 	return;
 }
 	document.getElementById('orderForm').submit();
 }
 </script>
 <form name="orderForm" id="orderForm" action='<?php echo JRoute::_("index.php") ?>' method='GET'>
	<input type='hidden' id="fromStep" name='fromStep' value='4'> 
	<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'> 
	<input type='hidden' id="option" name='option' value='<?php echo $option; ?>'>
	<input type='hidden' id="task" name='task' value='order'> 
	<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>"> <?php echo JText::_("EASYSDI_ORDER_NAME"); ?>
	<input type="text" name="order_name" id="order_name" value="<?php echo $mainframe->getUserState('order_name'); ?>"> 
	<br>
	<?php echo JText::_("EASYSDI_ORDER_TYPE_DEVIS"); ?>
	<input type="radio" name="order_type" id="order_type_d" value="D" <?php if ("D" == $mainframe->getUserState('order_type')) echo "checked"; ?>>
	<br>
	<?php echo JText::_("EASYSDI_ORDER_TYPE_COMMANDE"); ?>
	<input type="radio" name="order_type" id="order_type_o" value="O" <?php if ("O" == $mainframe->getUserState('order_type')) echo "checked"; ?>>
	<?php

	if ($user->guest){
		echo "<hr>";
		echo JText::_("EASYSDI_USER_NOT_CONNECTED");
		?> <input type="text" name="user" value=""> <input type="password"
	name="password" value=""> <?php
	}
	$query = "select a.partner_id as partner_id, j.name as name 
				from #__easysdi_community_partner a, #__easysdi_community_actor b, #__easysdi_community_role c, #__users as j 
				where c.role_code = 'TIERCE' and c.role_id = b.role_id AND a.partner_id = b.partner_id and a.user_id = j.id";
	$db->setQuery( $query);
	$rows = $db->loadObjectList();
	if ($db->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$db->getErrorMsg();
		echo "</div>";
	}
	?> <br>
<hr>
	<?php echo JText::_("EASYSDI_ORDER_THIRD_PARTY"); ?> <select
	name="third_party">
	<option value="0"><?php echo JText::_("EASYSDI_ORDER_FOR_NOBODY"); ?></option>
	<?php

	$third_party = $mainframe->getUserState('third_party');
	echo $third_party;
	foreach ($rows as $row){
		$selected="";
		if ($third_party == $row->partner_id) $selected="selected";
		echo "<option ".$selected." value=\"".$row->partner_id."\">".$row->name."</option>";

	}
	?>
</select></form>
	<?php


	}

	function orderSend($cid){

		global $mainframe;

		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$step = JRequest::getVar('step',5);


		$user = JFactory::getUser();



		?>
<div class="contentin"><br>
<br>
<script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 </script>
<form id="orderForm" name="orderForm"
	action="<?php echo JRoute::_("index.php"); ?>"><input type='hidden'
	id="fromStep" name='fromStep' value='5'> <input type="hidden"
	name="task" id="taskOrderForm" value="order"> <input type="hidden"
	name="option" value="<?php echo JRequest::getVar('option'); ?>"> <input
	type='hidden' id="step" name='step' value='<?php echo $step; ?>'> <input
	type='hidden' name='Itemid'
	value="<?php echo  JRequest::getVar ('Itemid' );?>"></form>

		<?php
		if (!$user->guest){
			?> <input
	onClick="document.getElementById('taskOrderForm').value = 'saveOrder';submitOrderForm();"
	type="button"
	value='<?php echo JText::_("EASYSDI_ORDER_SAVE_BUTTON"); ?>'> <input
	onClick="document.getElementById('taskOrderForm').value = 'sendOrder';submitOrderForm();"
	type="button"
	value='<?php echo JText::_("EASYSDI_ORDER_SEND_BUTTON"); ?>'> <?php
		}else{
			?>
<div class="alert"><?php echo JText::_("EASYSDI_NOT_CONNECTED");?></div>
			<?php
		}
		?></div>
		<?php
	}



	function saveOrder($orderStatus){
		global $mainframe;


		$user = JFactory::getUser();
		if (!$user->guest){
			$cid = $mainframe->getUserState('productList');

			$option = JRequest::getVar('option');
			$task = JRequest::getVar('task');
			$order_type = $mainframe->getUserState('order_type');
			$order_name = $mainframe->getUserState('order_name');
			$third_party = $mainframe->getUserState('third_party');
			$bufferValue = $mainframe->getUserState('bufferValue');
				
			$db =& JFactory::getDBO();

			jimport("joomla.utilities.date");
			$date = new JDate();

			$queryStatus = "SELECT id from #__easysdi_order_status_list where code = '".$orderStatus."'";
			$db->setQuery($queryStatus );
			$orderStatus = $db->loadResult();

			$queryType = "SELECT id from #__easysdi_order_type_list where code = '".$order_type."'";
			$db->setQuery($queryType );
			$order_type = $db->loadResult();
			
			$queryType = "SELECT id from #__easysdi_order_product_status_list where code = 'AWAIT'";
			$db->setQuery($queryType );
			$await_type = $db->loadResult();
			$queryType = "SELECT id from #__easysdi_order_product_status_list where code = 'AVAILABLE'";
			$db->setQuery($queryType );
			$available_type = $db->loadResult();
			
			$query = "INSERT INTO #__easysdi_order(third_party,type,order_id,name,status,order_date,order_update,user_id,buffer) VALUES ($db->Quote($third_party) ,'$order_type',0,'$order_name','$orderStatus','$date->toMySQL()','$date->toMySQL()',$user->id,$bufferValue)";
			$db->setQuery($query );

			if (!$db->query()) {
				echo "<div class='alert'>";
				echo $db->getErrorMsg();
				echo "</div>";
			}

			$order_id	= $db->insertId();
			$totalArea = $mainframe->getUserState('totalArea');
			$perimeter_id = $mainframe->getUserState('perimeter_id');

			$selSurfaceList = $mainframe->getUserState('selectedSurfaces');
			$selSurfaceListName = $mainframe->getUserState('selectedSurfacesName');

			$i=0;
			foreach ($selSurfaceList as $sel){

				//Before the dot, it is the perimeter id, after the dot id of the data
				$query =  "INSERT INTO #__easysdi_order_product_perimeters (id,order_id,perimeter_id,value,text) VALUES (0,$order_id,$perimeter_id,'$sel','$selSurfaceListName[$i]')";
				$db->setQuery($query );
				if (!$db->query()) {
					echo "<div class='alert'>";
					echo $db->getErrorMsg();
					echo "</div>";
					exit;
				}
				$i++;
			}

			foreach ($cid as $product_id){


				if ($product_id != "0"){					
					
					$query = "INSERT INTO #__easysdi_order_product_list(id,product_id,order_id, status) VALUES (0,".$product_id.",".$order_id.",".$await_type.")";

					$db->setQuery($query );
					if (!$db->query()) {
						echo "<div class='alert'>";
						echo $db->getErrorMsg();
						echo "</div>";
						exit;
					}

					$order_product_list_id = $db->insertId();


					$query = "SELECT  a.code as code FROM #__easysdi_product_property b, #__easysdi_product_properties_definition  as a   WHERE a.id = b.property_value_id  and b .product_id = ". $product_id." order by a.order";
					$db->setQuery( $query );
					$rows = $db->loadObjectList();
					
					foreach($rows as $row){

					$productProperties  = $mainframe->getUserState($row->code."_list_property_".$product_id);
					print_r($productProperties);
					if (count($productProperties)>0){
					
					
					
					$mainframe->setUserState($row->code.'_list_property_'.$product_id,null);

					
					foreach ($productProperties as $property_id){
						
							$query = "INSERT INTO #__easysdi_order_product_properties(id,order_product_list_id,property_id,code) VALUES (0,".$order_product_list_id.",".$property_id.",'$row->code')";

						$db->setQuery($query );
						if (!$db->query()) {
							echo "<div class='alert'>";
							echo $db->getErrorMsg();
							echo "</div>";
							exit;
						}
						
						
						
					}
					}

					$productProperties  = $mainframe->getUserState($row->code."_mlist_property_".$product_id);
					print_r($productProperties);
					if (count($productProperties)>0){
					
					
					
					$mainframe->setUserState($row->code.'_mlist_property_'.$product_id,null);

					
					foreach ($productProperties as $property_id){
						
							$query = "INSERT INTO #__easysdi_order_product_properties(id,order_product_list_id,property_id,code) VALUES (0,".$order_product_list_id.",".$property_id.",'$row->code')";

						$db->setQuery($query );
						if (!$db->query()) {
							echo "<div class='alert'>";
							echo $db->getErrorMsg();
							echo "</div>";
						}
						
						
						
					}
					}
					
					
					$productProperties  = $mainframe->getUserState($row->code."_cbox_property_".$product_id);
					print_r($productProperties);
					if (count($productProperties)>0){
					
					
					
					$mainframe->setUserState($row->code.'_cbox_property_'.$product_id,null);

					
					foreach ($productProperties as $property_id){
						
							$query = "INSERT INTO #__easysdi_order_product_properties(id,order_product_list_id,property_id,code) VALUES (0,".$order_product_list_id.",".$property_id.",'$row->code')";

						$db->setQuery($query );
						if (!$db->query()) {
							echo "<div class='alert'>";
							echo $db->getErrorMsg();
							echo "</div>";
							exit;
						}
						
						
						
					}
					}
					
					
					
					$productProperties  = $mainframe->getUserState($row->code."_text_property_".$product_id);
					print_r($productProperties);
					if (count($productProperties)>0){
					
					
					
					$mainframe->setUserState($row->code.'_text_property_'.$product_id,null);

					
					foreach ($productProperties as $property_id){
						
						$query = "INSERT INTO #__easysdi_order_product_properties(id,order_product_list_id,property_value,code) VALUES (0,$order_product_list_id,\"$property_id\",'$row->code')";
						

						$db->setQuery($query );
						if (!$db->query()) {
							echo "<div class='alert'>";
							echo $db->getErrorMsg();
							echo "</div>";
							exit;
						}
						
						
						
					}
					}
					
					
					$productProperties  = $mainframe->getUserState($row->code."_textarea_property_".$product_id);
					print_r($productProperties);
					if (count($productProperties)>0){
					
					
					
					$mainframe->setUserState($row->code.'_textarea_property_'.$product_id,null);

					
					foreach ($productProperties as $property_id){
						
						$query = "INSERT INTO #__easysdi_order_product_properties(id,order_product_list_id,property_value,code) VALUES (0,$order_product_list_id,\"$property_id\",'$row->code')";
						

						$db->setQuery($query );
						if (!$db->query()) {
							echo "<div class='alert'>";
							echo $db->getErrorMsg();
							echo "</div>";
							exit;
						}
						
						
					
					}
					}					
				}
				}
			}

			$queryStatus = "select id from #__easysdi_order_status_list where code ='SENT'";
			$db->setQuery($queryStatus);
			$sent = $db->loadResult();
			
			/* Met à jour le status pour un devis dont le prix est connu comme étant gratuit 
				et envoi un mail pour dire qu'un devis sur la donnée gratuite à été demandé*/
			$query = "SELECT o.name as cmd_name,
							 u.email as email , 
							 p.id as product_id, 
							 p.data_title as data_title , 
							 p.partner_id as partner_id   
					  FROM #__users u,
					  	   #__easysdi_community_partner pa, 
					  	   #__easysdi_order_product_list opl , 
					  	   #__easysdi_product p,
					  	   #__easysdi_order o, 
					  	   #__easysdi_order_type_list otl 
					  WHERE opl.order_id= $order_id 
					  AND p.id = opl.product_id 
					  and p.is_free = 1 
					  and opl.status='".$await_type."' 
					  and o.type=otl.id 
					  and otl.code='D' 
					  AND p.diffusion_partner_id = pa.partner_id 
					  and pa.user_id = u.id 
					  and o.order_id=opl.order_id 
					  and o.status='".$sent."' ";

			$db->setQuery( $query );
			$rows = $db->loadObjectList();
			if ($db->getErrorNum()) {
				echo "<div class='alert'>";
				echo 			$db->getErrorMsg();
				echo "</div>";
			}

			foreach ($rows as $row){
				$query = "UPDATE   #__easysdi_order_product_list opl set status = ".$available_type." WHERE opl.order_id= $order_id AND opl.product_id = $row->product_id";
				$db->setQuery( $query );
				if (!$db->query()) {
					echo "<div class='alert'>";
					echo $db->getErrorMsg();
					echo "</div>";
					exit;
				}
				$user = JFactory::getUser();

				SITE_product::sendMailByEmail($row->email,JText::_("EASYSDI_REQUEST_FREE_PRODUCT_SUBJECT"),JText::sprintf("EASYSDI_REQEUST_FREE_PROUCT_MAIL_BODY",$row->data_title,$row->cmd_name,$user->name));
					
			}
				
				
				
			$query = "SELECT COUNT(*) FROM #__easysdi_order_product_list WHERE order_id=$order_id AND STATUS = '".$await_type."' ";
			$db->setQuery($query);
			$total = $db->loadResult();
			jimport("joomla.utilities.date");
			$date = new JDate();
			if ( $total == 0){
				$queryStatus = "select id from #__easysdi_order_status_list where code ='FINISH'";
				$db->setQuery($queryStatus);
				$status_id = $db->loadResult();
			}else{
				$queryStatus = "select id from #__easysdi_order_status_list where code ='PROGRESS'";
				$db->setQuery($queryStatus);
				$status_id = $db->loadResult();
			}
			
			
			
			$query = "UPDATE   #__easysdi_order  SET status =".$status_id." ,response_date ='". $date->toMySQL()."'  WHERE order_id=$order_id and status=".$sent;

			$db->setQuery($query);
			if (!$db->query()) {
				echo "<div class='alert'>";
				echo $db->getErrorMsg();
				echo "</div>";
				exit;
			}
			if ($total == 0){
				SITE_cpanel::notifyUserByEmail($order_id);
			}


			$mainframe->setUserState('productList',null);
			$mainframe->setUserState('order_type',null);
			$mainframe->setUserState('order_name',null);
			$mainframe->setUserState('third_party',null);
			$mainframe->setUserState('selectedSurfacesName',null);
			$mainframe->setUserState('selectedSurfaces',null);
			$mainframe->setUserState('totalArea',null);
			$mainframe->setUserState('perimeter_id',null);
			$mainframe->setUserState('bufferValue',null);


		}else{

			?>
<div class="alert"><?php echo JText::_("EASYSDI_NOT_ALLOWED"); ?></div>
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
			
			$cid = JRequest::getVar ('cid',  array());
			/*
			 * Save the product list from the step 1
			 */
			$productList = $mainframe->getUserState('productList');
			if (is_array($productList))
			{
				foreach ($cid as $key => $value)
				{
					if(array_key_exists($value , $productList))
					{
						
					}
					else
					{
						$productList[]=$value;
					}
				}
				$mainframe->setUserState('productList',$productList);
			}
			else 
			{
				$mainframe->setUserState('productList',$cid);
			}
			/*if (is_array($mainframe->getUserState('productList'))){
				$cid = array_merge($cid,$mainframe->getUserState('productList'));
			}
			$mainframe->setUserState('productList',$cid);*/
		}

		if ($fromStep == 2) {
			/*
			 * Save the perimeter from the step 2
			 */
			$selSurfaceList = JRequest::getVar ('replicSelectedSurface', array() );
			$mainframe->setUserState('selectedSurfaces',$selSurfaceList);
			$selSurfaceListName = JRequest::getVar ('replicSelectedSurfaceName', array(0) );
			$mainframe->setUserState('selectedSurfacesName',$selSurfaceListName);
				
			$bufferValue = JRequest::getVar ('bufferValue2', 0 );
			$mainframe->setUserState('bufferValue',$bufferValue);
				
			$totalArea = JRequest::getVar ('totalArea', 0 );
			$mainframe->setUserState('totalArea',$totalArea);

			$perimeter_id = JRequest::getVar ('perimeter_id', 0 );
			$mainframe->setUserState('perimeter_id',$perimeter_id);
		}

		if ($fromStep == 3) {
			/*
			 * Save the properties from the step 3
			 */
			$cid = $mainframe->getUserState('productList');
			$db =& JFactory::getDBO();
			
			//
			foreach ($cid as $key =>  $id){
				
				
					$query = "SELECT  a.code as code FROM #__easysdi_product_property b, #__easysdi_product_properties_definition  as a   WHERE a.id = b.property_value_id  and b .product_id = ". $id." order by a.order";
					$db->setQuery( $query );
					$rows = $db->loadObjectList();
					
					foreach($rows as $row){
				
				
				$property=	JRequest::getVar($row->code."_text_property_$id", array() );
				$mainframe->setUserState($row->code.'_text_property_'.$id,$property);

				
				$property=	JRequest::getVar($row->code."_textarea_property_$id", array() );
				$mainframe->setUserState($row->code.'_textarea_property_'.$id,$property);

				$property=	JRequest::getVar($row->code."_list_property_$id", array() );
				$mainframe->setUserState($row->code.'_list_property_'.$id,$property);


				$property=	JRequest::getVar($row->code."_cbox_property_$id", array() );
				$mainframe->setUserState($row->code.'_cbox_property_'.$id,$property);

				$property=	JRequest::getVar($row->code."_mlist_property_$id", array() );
				$mainframe->setUserState($row->code.'_mlist_property_'.$id,$property);
					}				 
				 
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
		if ( !is_array($productList) || count($productList) == 0){
			JRequest::setVar('step',1);
			JRequest::setVar('fromStep',0);
		}
		
		
	}

	function order(){

		global $mainframe;


		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$cid = JRequest::getVar ('cid', array() );
		$step = JRequest::getVar('step',1);

		HTML_shop::manageSession();
		$productList = $mainframe->getUserState('productList');
		if ( !is_array($productList) || count($productList) == 0)
		{
			$step = 1;
			$curStep = '';
			$fromStep = '';
		}
		?>
<table>
	<tr>
		<td>
		<div class="headerShop"><?php $curStep = 1; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>">
			<table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
			<?php } ?> <?php $curStep = 2; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?> <?php $curStep = 3; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?> <?php $curStep = 4; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?> <?php $curStep = 5; if(count($productList)>0&& ($curStep<$step-1 || $curStep==$step+1)) { ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="selectableStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }elseif(count($productList)>0 && ($curStep==$step-1)){ ?>
		<div
			onClick="document.getElementById('step').value='<?php echo $curStep; ?>' ;submitOrderForm();"
			class="previousStep"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php }else {?>
		<div
			class="<?php if($curStep==$step) {echo "currentStep";} else{echo "unselectableStep";}?>"><table><tr><td class="stepLabel"><?php echo $curStep;?></td><td class="stepCaption"><?php echo JText::_("EASYSDI_STEP".$curStep); ?></td></tr></table>
		</div>
		<?php } ?></div>
		</td>
	</tr>
	<tr>
		<td>
		<div class="bodyShop">
		<?php if ($step ==1) HTML_shop::searchProducts();?>
		<?php if ($step ==2) HTML_shop::orderPerimeter($cid,$option);?> 
		<?php if ($step ==3) HTML_shop::orderProperties($cid,$option);?>
		<?php if ($step ==4) HTML_shop::orderDefinition($cid);?> 
		<?php if ($step ==5) HTML_shop::orderSend($cid);?>
		</div>
		</td>
	</tr>
</table>
		<?php
	}

	function importProduct(){



		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (!$catalogUrlBase) $catalogUrlBase ="http://localhost:8081/proxy/ogc/geonetwork";
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
				//for ($i=1; $i<=1;$i=$i+$inc){
				$catalogUrlGetRecordsMD =  $catalogUrlGetRecords . "&startPosition=".$i."&maxRecords=".$inc;
				$cswResults= simplexml_load_file($catalogUrlGetRecordsMD);
				echo $catalogUrlGetRecordsMD."<br>";
				foreach ($cswResults->children("http://www.opengis.net/cat/csw")->SearchResults->children("http://www.isotc211.org/2005/gmd")->MD_Metadata as $metadata){

					$md = new geoMetadata($metadata);

					echo "<b>".$i."</b><br>";
						
					$query= "insert into #__easysdi_product (metadata_id,id,supplier_name,data_title,metadata_standard_id,hasMetadata) values(".$db->Quote($md->getFileIdentifier()).",0,".$db->Quote($md->getDistributionOrganisationName()).",".$db->Quote($md->getDataIdentificationTitle()).",(select id from #__easysdi_metadata_standard WHERE name = 'ASITVD - ISO 19115:2003/19139' ),1)";
					echo $query."<br>";
					$db->setQuery( $query);
					if (!$db->query()) {
						echo "<div class='alert'>";
						echo "<b>".$db->getErrorMsg()."</b><br>";
						echo "</div>";
						exit;
					}
				}
			}

		}



	}

	function searchProducts($orderable = 1){
		global $mainframe;
		$db =& JFactory::getDBO();

		/*	$language=&JFactory::getLanguage();
		 $language->load('com_easysdi');*/
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',5);

		$option = JRequest::getVar('option');
		$task = JRequest::getVar('task');
		$view = JRequest::getVar('view');
		$step = JRequest::getVar('step',"1");
		$countMD = JRequest::getVar('countMD');
		$simpleSearchCriteria  	= JRequest::getVar('simpleSearchCriteria','lastAddedMD');
		$freetextcriteria = JRequest::getVar('freetextcriteria','');
		$freetextcriteria = $db->getEscaped( trim( strtolower( $freetextcriteria ) ) );


		$cid = JRequest::getVar ('cid', array() );

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

		if($partner->partner_id == 0)
		{
			//No user logged, display only external products
			$filter .= " AND (EXTERNAL=1) ";
		}
		else
		{
			//User logged, display products according to users's rights
			if(userManager::hasRight($partner->partner_id,"REQUEST_EXTERNAL"))
			{
				if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (p.EXTERNAL=1
					OR
					(p.INTERNAL =1 AND
					(p.partner_id =  $partner->partner_id
					OR
					p.partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					))) ";
				}
				else
				{
					$filter .= " AND (EXTERNAL=1) ";
				}
			}
			else
			{
				if(userManager::hasRight($partner->partner_id,"REQUEST_INTERNAL"))
				{
					$filter .= " AND (p.INTERNAL =1 AND
					(p.partner_id =  $partner->partner_id
					OR
					p.partner_id = (SELECT root_id FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id )
					))) ";
				}
				else
				{
					//no command right
					$filter .= " AND (EXTERNAL = 10) ";
				}
			}
		}

		//$filter .= " AND (EXTERNAL=1 OR (INTERNAL =1 AND PARTNER_ID IN (SELECT PARTNER_ID FROM #__easysdi_community_partner WHERE partner_id = $partner->partner_id OR root_id = $partner->partner_id))) ";

		if ($simpleSearchCriteria == "favoriteProduct"){

			$queryFav = "SELECT product_id FROM #__easysdi_user_product_favorite WHERE partner_id = $partner->partner_id ";
			$db->setQuery( $queryFav);
			$productList = $db->loadResultArray();

			if (count($productList)>0){
				$filterFav = " AND p.ID IN (";
				foreach( $productList as $id){
					$filterFav  = $filterFav.$id.",";
				}
				$filterFav  = substr($filterFav  , 0, -1);
				$filterFav  = $filterFav.")";
				$filter .= $filterFav ;
			}else $filter = " AND 1=0";
		}


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

<div class="contentin"><script>
 function submitOrderForm(){
 	document.getElementById('orderForm').submit();
 }
 
 function addOrder(cid, id){
 	var form = document.getElementById('orderForm');
	var elem = document.createElement('input');
	elem.setAttribute('type', 'checkbox');
	elem.setAttribute('id', 'ch'+id);
	elem.setAttribute('name', 'cid[]');
	elem.setAttribute('value', cid);
	form.appendChild(elem);
	elem.style.visibility = "hidden"; 
	elem.checked=true;
	form.submit();
 }
 </script>
<form name="orderForm" id="orderForm"
	action='<?php echo JRoute::_("index.php") ?>' method='GET'>


<h3><?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
<br>
<span class="searchCriteria"> <input name="freetextcriteria" type="text" value="<?php echo JRequest::getVar('freetextcriteria'); ?>">
<br>
<input type="radio" name="simpleSearchCriteria" value="lastAddedMD" <?php if ($simpleSearchCriteria == "lastAddedMD") echo "checked";?>> 
	<?php echo JText::_("EASYSDI_LAST_ADDED_MD"); ?>
<br>
<input type="radio" name="simpleSearchCriteria" value="moreConsultedMD" <?php if ($simpleSearchCriteria == "moreConsultedMD") echo "checked";?>>
	<?php echo JText::_("EASYSDI_MORECONSULTED_MD"); ?>
<br>
<input type="radio" name="simpleSearchCriteria" value="lastUpdatedMD" <?php if ($simpleSearchCriteria == "lastUpdatedMD") echo "checked";?>> 
	<?php echo JText::_("EASYSDI_LAST_UPDATED_MD"); ?>
<br>
	<?php if (!$user->guest){ ?> <input type="radio" name="simpleSearchCriteria" value="favoriteProduct" <?php if ($simpleSearchCriteria == "favoriteProduct") echo "checked";?>>
	<?php echo JText::_("EASYSDI_FAVORITE_PRODUCT"); ?>
<br>
	<?php  }?> 
</span> 
<br>
<button type="submit" class="searchButton"><?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
<br>
<br>
<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>

<input type='hidden' name='option' value='<?php echo $option;?>'> 
<input type='hidden' id="task" name='task' value='<?php echo $task; ?>'> 
<input type='hidden' id="view" name='view' value='<?php echo $view; ?>'> 
<input type='hidden' id="fromStep" name='fromStep' value='1'> 
<input type='hidden' id="step" name='step' value='<?php echo $step; ?>'>
<input type='hidden' name='Itemid' value="<?php echo  JRequest::getVar ('Itemid' );?>"> 
	<?php $pageNav = new JPagination($total,$limitstart,$limit); ?>
<span class="searchCriteria">
<table width="100%">
	<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
</table>
<table class="mdsearchresult" width="100%">
	<?php
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);
	$i=0;

	foreach ($rows  as $row){
		$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$row->metadata_id."'";
		$db->setQuery($queryPartnerID);
		$partner_id = $db->loadResult();
		
		$queryPartnerLogo = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
		$db->setQuery($queryPartnerLogo);
		$partner_logo = $db->loadResult();
		
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");
		
		$isMdPublic = false;
		$isMdFree = true;
		//Define if the md is free or not
		$queryPartnerID = "select is_free from #__easysdi_product where metadata_id = '".$row->metadata_id."'";
		$db->setQuery($queryPartnerID);
		$is_free = $db->loadResult();
		if($is_free == 0)
		{
			$isMdFree = false;
		}
		
		//Define if the md is public or not
		$queryPartnerID = "select external from #__easysdi_product where metadata_id = '".$row->metadata_id."'";
		$db->setQuery($queryPartnerID);
		$external = $db->loadResult();
		if($external == 1)
		{
			$isMdPublic = true;
		}
		
		$query = "select count(*) from #__easysdi_product where previewBaseMapId is not null AND previewBaseMapId>0 AND metadata_id = '".$row->metadata_id."'";
		$db->setQuery( $query);
		$hasPreview = $db->loadResult();
		if ($db->getErrorNum()) {
			$hasPreview = 0;
		}
		?>
<tr>
	 <td valign="top" rowspan=3>
	    <img src="<?php echo $partner_logo;?>" title="<?php echo $row->supplier_name;?>"></img>
	  </td>
	  <td colspan=3><span class="mdtitle"><a><?php echo $row->data_title; ?></a></span>
	  </td>
	  <td valign="top" rowspan=2>
	    <table id="info_md">
		  <tr>
		     <td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
		  </tr>
		  <tr>
		     <td><div <?php if($isMdFree) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
		  </tr>
		</table>
	  </td>
	 </tr>
	 <tr>
	  <td colspan=3><span class="mdsupplier"><?php echo $row->supplier_name;?></span></td>
	 </tr>
     <tr>
	  <td><span class="mdviewfile">
	  	<a title="<?php echo JText::_("EASYSDI_ADD_TO_CART"); ?>"
				href="#" onclick="addOrder(<?php echo $row->id.",".$i; ?>)"><?php echo JText::_("EASYSDI_ADD_TO_CART"); ?>
			</a></span>
	  </td>
	  	<?php if ($hasPreview > 0){ ?>
	  <td><span class="mdviewproduct">
	    <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=previewProduct&metadata_id=<?php echo $row->metadata_id;?>"
			rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_PREVIEW_PRODUCT"); ?></a></span>
      </td>
		<?php } ?>
	  <td>&nbsp;</td>
	 </tr>
	 <tr>
	   <td colspan=4>&nbsp;</td>
	 </tr>
			<?php
			$i=$i+1;
	}
	?>
	</table>
	<input type="hidden" name="countMD" value="<?php echo $countMD;?>">
</span></form>
</div>
	<?php
	}}
	?>