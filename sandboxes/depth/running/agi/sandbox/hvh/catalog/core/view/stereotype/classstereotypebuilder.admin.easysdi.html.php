<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�??Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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
 *
 * string genericlist (array $arr, string $name,
 * [string $attribs = null], [string $key = 'value'],
 * [string $text = 'text'], [mixed $selected = NULL],
 * [ $idtag = false], [ $translate = false])
 */

defined('_JEXEC') or die('Restricted access');


class HTML_classstereotype_builder {
	
	function getGeographicExtentClass( $database, $fieldsetname, $relationObject, $parentFieldsetName, $xpathResults, $path, $scope, $master, $clone = false, $editable = 1){
		//Pour info $scope : gmd:MD_DataIdentification
		
		$hidden = "false";
		if($editable == 3)
			$hidden = "true";
		
		$disabled = "false";
		if($editable == 2)
			$disabled = "true";
		
		//Chargement des attributs de la relation spécifiques au stéréotype
		$query = "  SELECT sa.alias as alias, ra.value as value 
						FROM #__sdi_sys_stereotype s 
						INNER JOIN #__sdi_sys_attribute sa ON sa.stereotype_id = s.id
						INNER JOIN #__sdi_relation_attribute ra ON ra.attribute_id =sa.id
						INNER JOIN #__sdi_relation r ON r.id = ra.relation_id  
					WHERE s.id =".$relationObject->cl_stereotype_id."
					AND r.id = ".$relationObject->rel_id;
		$database->setQuery($query);
		$stereotypeAttributes = $database->loadObjectList();
		
		//Default language
		foreach($this->langList as $lang)
		{
			if ($lang->defaultlang == true){
				$default_lang = $lang->code_easysdi;
			}
		}
		
		//Build object to hold XML content
		$extent_object_array = array();
		if(!$master){
			//gmd:extent
			$nodes_extent = $xpathResults->query($relationObject->rel_isocode, $scope);
			foreach ($nodes_extent as $node_extent){
				//gmd:EX_Extent
				$nodes_EX_Extent = $xpathResults->query($relationObject->child_isocode, $node_extent);
				foreach ($nodes_EX_Extent as $node_EX_Extent){
					//Create a new object to hold extent description
					$extent_object = new stdClass;
				
					//sdi:extentType
					$nodes_extentType = $xpathResults->query("sdi:extentType", $node_EX_Extent);
					if(count($nodes_extentType)== 0){
						//no atttribute sdi:extentType
						$extent_object->extentType = null;
					}else{
						foreach ($nodes_extentType as $node_extentType){
							$category_nodes = $xpathResults->query("gco:CharacterString", $node_extentType);
							foreach ($category_nodes as $category_node){
								$extent_object->extentType = $category_node->nodeValue;
							}
						}
					}
				
					//gmd:geographicElement
					$nodes_geographicElement = $xpathResults->query("gmd:geographicElement", $node_EX_Extent);
					if(count($nodes_geographicElement)== 0){
						//no geographicElement
						$extent_object->geographicElement = null;
					}else{
						if(!isset($extent_object->extentType )){
							//Look for geographicExtent defined by, and only by, a BBOX
							foreach ($nodes_geographicElement as $node_geographicElement){
								$nodes_BBOX = $xpathResults->query("gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal", $node_geographicElement);
								if(count($nodes_BBOX) == 0){
									//Not a geographic bbox
									continue;
								}else{
									foreach ($nodes_BBOX as $node_BBOX){
										$extent_object->north = $node_BBOX->nodeValue;
									}
								}
								
								$nodes_BBOX = $xpathResults->query("gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal", $node_geographicElement);
								if(count($nodes_BBOX) == 0){
									//Not a geographic bbox
									continue;
								}else{
									foreach ($nodes_BBOX as $node_BBOX){
										$extent_object->south = $node_BBOX->nodeValue;
									}
								}
								
								$nodes_BBOX = $xpathResults->query("gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal", $node_geographicElement);
								if(count($nodes_BBOX) == 0){
									//Not a geographic bbox
									continue;
								}else{
									foreach ($nodes_BBOX as $node_BBOX){
										$extent_object->east = $node_BBOX->nodeValue;
									}
								}
								
								$nodes_BBOX = $xpathResults->query("gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal", $node_geographicElement);
								if(count($nodes_BBOX) == 0){
									//Not a geographic bbox
									continue;
								}else{
									foreach ($nodes_BBOX as $node_BBOX){
										$extent_object->west = $node_BBOX->nodeValue;
									}
								}
								
							}
						}else{
							//Look for geographicExtent defined by a predifined perimeter
							foreach ($nodes_geographicElement as $node_geographicElement){
								//Description is multilingual, get the one in default language
								$nodes_description_text = $xpathResults->query("gmd:EX_GeographicDescription/gmd:geographicIdentifier/gmd:MD_Identifier/gmd:code/gco:CharacterString", $node_geographicElement);
								if(count($nodes_description_text) == 0){
									//Not a geographicIdentifier
									continue;
								}else{
									foreach ($nodes_description_text as $node_description_text){
										$extent_object->description = $node_description_text->nodeValue;
									}
								}
							}
						}
					}
					//Add extent object to array
					array_push($extent_object_array, $extent_object);
				}
			}
		}
		
		//Liste des catégories de périmètres
		$language =& JFactory::getLanguage();
		
		$query = "SELECT bc.alias as alias, t.label as label
					FROM #__sdi_boundarycategory bc
						INNER JOIN #__sdi_translation t ON bc.guid = t.element_guid
						INNER JOIN #__sdi_language l ON t.language_id=l.id
						INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
					WHERE c.code='".$language->_lang."'";
		$database->setQuery( $query );
		$categorycontent = $database->loadObjectList();
		
		$dataValues = array();
		foreach ($categorycontent as $cont){
			$dataValues[$cont->alias] = html_Metadata::cleanText($cont->label);
		}
		
		//Multiplicité de la relation
		$rel_lowerbound = $relationObject->rel_lowerbound;
		$rel_upperbound = $relationObject->rel_upperbound;
		
		$comboboxName = $fieldsetname."-sdi_extentType__1";
		$itemselectorName = $fieldsetname."-gmd_geographicElement__1";
		$freeperimeterselectorName = $fieldsetname."-free-perimeter__1";
		$fieldNorthName = $fieldsetname."-north__1";
		$fieldSouthName = $fieldsetname."-south__1";
		$fieldEastName = $fieldsetname."-east__1";
		$fieldWestName = $fieldsetname."-west__1";
		
		
		//Categories of predefined perimeters
		$this->javascript .="
		var valueList = ".HTML_metadata::array2extjs($dataValues, true).";
		var selectedValueList = '';
		var defaultValueList = '';
		
		var comboboxCategories = createComboBox('".$comboboxName."', '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_PERIMETER_LABEL"))."', false, '1', '1', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_(""))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."', '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_CATEGORY_ALL"))."');
 		".$parentFieldsetName.".add(comboboxCategories);
		
		comboboxCategories.on('select', function(){
				var itemselector = Ext.getCmp('".$itemselectorName."');
				Ext.getCmp('".$fieldsetname."').getEl().mask();
				var selectedCategory = this.getValue();
				var selectedBoundaries =itemselector.toMultiselect.store.getRange();
				var boundaries_id = '';
				for (var i = 0; i < selectedBoundaries.length ; i++){
					var boundary_id = selectedBoundaries[i].get('value');
					boundaries_id = boundaries_id + boundary_id ;
					if(i != selectedBoundaries.length-1){
						boundaries_id = boundaries_id + ',';
					}
				}
				
				 Ext.Ajax.request({
			         url : 'index.php?option=com_easysdi_catalog&task=getBoundariesByCategory&category='+selectedCategory+'&exclude='+boundaries_id,
			                  method: 'GET',
			                  success: function ( result, request ) {
			                      var jsonData = Ext.util.JSON.decode(result.responseText);
			                      itemselector.fromMultiselect.store.removeAll();
			                      for  (var i = 0 ; i <jsonData.length ; i++){
				                  	var boundary = jsonData[i];
				                  	var boundaryRecord = Ext.data.Record.create([
					                  	{name : 'value'},
					                  	{name : 'text'},
					                  	{name : 'northbound'},
					                  	{name : 'southbound'},
					                  	{name : 'eastbound'},
					                  	{name : 'westbound'}
				                  	]);
				                  	
				                  	var record = new boundaryRecord({
				                  		value :  boundary['id'],
				                  		text : boundary['label'],
                 						northbound:boundary['northbound'],
                 						southbound : boundary['southbound'],
                 						eastbound : boundary['eastbound'],
                 						westbound : boundary['westbound']
				                  	});
	
				                  	   itemselector.fromMultiselect.store.add([record]);
								  } 
								  Ext.getCmp('".$fieldsetname."').getEl().unmask();
			               },
			                  failure: function ( result, request ) {
			                   var jsonData = Ext.util.JSON.decode(result.responseText);
			                   alert(jsonData);
			                   Ext.getCmp('".$fieldsetname."').getEl().unmask();
			               }
			       });
		}, comboboxCategories);
			
		";
		
		//Existing boundaries in the XML file
		$query_ids = "";
		$selectedBoundaries = array();
		$freeBoundaries = array();
		if(count($extent_object_array) > 0 ){
			foreach ($extent_object_array as $extent_object){
				if( isset($extent_object->description)){//Predefined perimeters
					$query = "SELECT b.id as id,Concat(t.label,' [',tbc.label,']') as label, 
									 b.northbound as northbound ,
									 b.southbound as southbound, 
									 b.eastbound as eastbound ,
									 b.westbound as westbound,
									 bc.alias as category  
								FROM #__sdi_boundary b
									INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id 
									INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
									INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
									INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
									INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
									INNER JOIN #__sdi_language l ON t.language_id=l.id
									INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
								WHERE c.code='".$default_lang."'
								AND cbc.code='".$default_lang."'
								AND t.title='".mysql_real_escape_string($extent_object->description)."'
					ORDER BY label";
					$database->setQuery( $query );
					$perimeter_selected = $database->loadObject();
		
					if(strlen($query_ids) != 0) {
						$query_ids .= ",";
					}
					$query_ids .= $perimeter_selected->id;
					array_push($selectedBoundaries,$perimeter_selected);
				}else{//Free perimeters
					$freeperimeter = new stdClass;
					$freeperimeter->id = '['.$extent_object->north.','.$extent_object->south.','.$extent_object->east.','.$extent_object->west.']';
					$freeperimeter->label = '['.round($extent_object->north,2).'...,'.round($extent_object->south,2).'...,'.round($extent_object->east,2).'...,'.round($extent_object->west,2).'...]';
					$freeperimeter->northbound = $extent_object->north;
					$freeperimeter->southbound = $extent_object->south;
					$freeperimeter->eastbound = $extent_object->east;
					$freeperimeter->westbound = $extent_object->west;
					
					array_push($freeBoundaries,$freeperimeter);
				}
			}
		}
		
		//Avalaible boundaries in the database
		if(count($selectedBoundaries) > 0 ){
			$query = "SELECT b.id as id, 
							 Concat(t.label,' [',tbc.label,']') as label, 
							 b.northbound as northbound ,
							 b.southbound as southbound, 
							 b.eastbound as eastbound ,
							 b.westbound as westbound,
							 bc.alias as category 
					FROM #__sdi_boundary b
						INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id 
						INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
						INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
						INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
						INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
						INNER JOIN #__sdi_language l ON t.language_id=l.id
						INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
					WHERE c.code='".$language->_lang."'
					AND cbc.code='".$language->_lang."'
					AND b.id NOT IN ($query_ids)
					ORDER BY label";
				
		}else{
			$query = "SELECT b.id as id, 
							 Concat(t.label,' [',tbc.label,']') as label, 
							 b.northbound as northbound ,
							 b.southbound as southbound, 
							 b.eastbound as eastbound ,
							 b.westbound as westbound,
							 bc.alias as category 
					FROM #__sdi_boundary b
						INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id 
						INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
						INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
						INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
						INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
						INNER JOIN #__sdi_language l ON t.language_id=l.id
						INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id
					WHERE c.code='".$language->_lang."'
					AND cbc.code='".$language->_lang."'
					ORDER BY label
			";
				
		}
		$database->setQuery( $query );
		$availableBoundaries = $database->loadObjectList();
		
		//Predefined perimeter item selector
		$clone ? $clone = 'true' : $clone ='false';
		$this->javascript .="
			 var sourceDS = new Ext.data.ArrayStore({
			        data: [";
					    foreach ($availableBoundaries as $boundary){
					    	$this->javascript .="[
						    	'".$boundary->id."',
						    	'".addslashes($boundary->label)."',
						    	'".$boundary->northbound."',
						    	'".$boundary->southbound."',
						    	'".$boundary->eastbound."',
						    	'".$boundary->westbound."',
						    	'".addslashes($boundary->category)."'
					    	],
					    	";
						};
						if(count($availableBoundaries)>0)
							$this->javascript = substr($this->javascript, 0, strlen($this->javascript)-1);
						$this->javascript .="],
			        fields: ['value','text', 'northbound', 'southbound', 'eastbound', 'westbound','category'],
			        sortInfo: {
			            field: 'text',
			            direction: 'ASC'
			        }
			    });
    		    
			var destinationDS = new   Ext.data.ArrayStore({
				data: [
						";
				    foreach ($selectedBoundaries as $boundary){
				    	$this->javascript .="[
					    	'".$boundary->id."',
					    	'".addslashes($boundary->label)."',
					    	'".$boundary->northbound."',
					    	'".$boundary->southbound."',
					    	'".$boundary->eastbound."',
					    	'".$boundary->westbound."',
					    	'".addslashes($boundary->category)."'
					  ],
		    		  ";
			        };
			        if(count($selectedBoundaries)>0)
						$this->javascript = substr($this->javascript, 0, strlen($this->javascript)-1);
					$this->javascript .="
					],
				fields: ['value','text', 'northbound', 'southbound', 'eastbound', 'westbound','category'],
			        sortInfo: {
			            field: 'text',
			            direction: 'ASC'
			        }
			});
				  
		 var ".$parentFieldsetName."_itemselector = new BoundaryItemSelector({
	                    name: '".$itemselectorName."',
	                    disabled:".$disabled.",
	                    comboboxname : '".$comboboxName."',
	                    id: '".$itemselectorName."',
	                    clone: ".$clone.",
	                    mincardbound : ".$rel_lowerbound.",
	                    maxcardbound : ".$rel_upperbound.",
			            fieldLabel: '',
			            maxcardReachMessage : '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_MAXCARD_REACH_MESSAGE"))."',
				        imagePath: '".JURI::root( true )."/administrator/components/com_easysdi_catalog/templates/images/',
			            multiselects: [{
			            	legend: '".JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_LIST_AVAILABLE")."',
			            	id: '".$itemselectorName."_available',
			            	minSelections:0,
	            			maxSelections:999,
	            			dynamic:true,
			                width: 200,
			                height: 200,
			                store: sourceDS,
			                displayField: 'text',
			                valueField: 'value'
			            },{
			            	legend: '".JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_LIST_SELECTED")."',
			            	id: '".$itemselectorName."_selected',
			                minSelections:0,
	            			maxSelections:999,
			                dynamic:true,
			                width: 200,
			                height: 200,
			                store: destinationDS,
			                displayField: 'text',
			                valueField: 'value'
			            }]
			        });
			        ";
					
			$this->javascript .="
			".$parentFieldsetName.".add(".$parentFieldsetName."_itemselector);
			";
					

		//Options of the relation
		foreach ($stereotypeAttributes as $stereotypeAttribute){
			
			if($stereotypeAttribute->alias == "displaymap"){
				$displaymap = $stereotypeAttribute->value ;
			}
			if ($stereotypeAttribute->alias == "strictperimeter"){
				$strictperimeter = $stereotypeAttribute->value;
			}
			if ($stereotypeAttribute->alias == "params"){
				$params = json_decode($stereotypeAttribute->value, true);
			}
		}
		
		//Free perimeter selector
		if($clone && !$strictperimeter){
			$this->javascript .="
			var freedestinationDS = new   Ext.data.ArrayStore({
				data: [
				";
				    foreach ($freeBoundaries as $perimeter){
				    	$this->javascript .="[
					    	'".$perimeter->id."',
					    	'".$perimeter->label."',
					    	'".$perimeter->northbound."',
					    	'".$perimeter->southbound."',
					    	'".$perimeter->eastbound."',
					    	'".$perimeter->westbound."'
					  ],
		    		  ";
			        };
			        if(count($freeBoundaries)>0)
						$this->javascript = substr($this->javascript, 0, strlen($this->javascript)-1);
					$this->javascript .="
				],
				fields: ['value','text', 'northbound', 'southbound', 'eastbound', 'westbound']
			});
			
			var ".$parentFieldsetName."_freeperimeterselector = new catalogFreePerimeterSelector({
	                    name: '".$freeperimeterselectorName."',
	                    comboboxname : '".$comboboxName."',
	                    disabled:".$disabled.",
	                    id: '".$freeperimeterselectorName."',
	                    clone: ".$clone.",
	                    mincardbound : ".$rel_lowerbound.",
	                    maxcardbound : ".$rel_upperbound.",
	                    boundaryItemSelector : ".$parentFieldsetName."_itemselector,
	                    fieldLabel: '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_FREEPERIMETER_LABEL"))."',
			            northLabel : '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_NORTH_LABEL"))."',
			            southLabel : '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_SOUTH_LABEL"))."',
			            eastLabel : '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_EAST_LABEL"))."',
			            westLabel : '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_WEST_LABEL"))."',
			            maxcardReachMessage : '".html_Metadata::cleanText(JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_MAXCARD_REACH_MESSAGE"))."',
				        imagePath: '".JURI::root( true )."/administrator/components/com_easysdi_catalog/templates/images/',
				        multiselects: [{
			            	legend: '".JText::_("CATALOG_STEREOTYPE_CLASS_GEOGRAPHICEXTENT_LIST_SELECTED")."',
			            	id: '".$freeperimeterselectorName."_selected',
			            	minSelections:".$rel_lowerbound.",
	            			maxSelections:".$rel_upperbound.",
			                dynamic:true,
			                width: 200,
			                height: 200,
			                store: freedestinationDS,
			                displayField: 'text',
			                valueField: 'value'
			            }]
			        });
  			       ".$parentFieldsetName.".add(".$parentFieldsetName."_freeperimeterselector);
  			       
  			       ".$parentFieldsetName."_itemselector.setFreePerimeterSelector(".$parentFieldsetName."_freeperimeterselector);
			";
		}
		
		
		//Map configuration
		//Note : see dynamic.js createFieldSet listener for the map creation itself 
		if($clone && $displaymap && $hidden == "false"){
			//NOTE : Predefined perimeters can have an empty geographic BBOX
			$this->javascript .="
				".$parentFieldsetName."_itemselector.addListener ('addItemTo',function(record){
					if (record.data.westbound != 0 && record.data.southbound != 0 && record.data.eastbound != 0 && record.data.northbound  != 0){
						var bounds = new OpenLayers.Bounds(record.data.westbound,record.data.southbound,record.data.eastbound,record.data.northbound);
						var feature = new OpenLayers.Feature.Vector(bounds.toGeometry());
						feature.id = record.data.value;
						this.mapHelper.perimeterLayer.addFeatures(feature);
					}
				}, this);
				
				".$parentFieldsetName."_itemselector.addListener ('removeItemTo',function(record){
					this.mapHelper.perimeterLayer.removeFeatures(this.mapHelper.perimeterLayer.getFeatureById(record.data.value));
				}, this);
			";
			$withFreePerimeter = $strictperimeter ? 0 : 1;
			$freePerimeterSelectorName = $strictperimeter ? 'null' : $parentFieldsetName."_freeperimeterselector";
			
				
			$this->javascript .="
				defaultBBoxConfig ={
					getLayers : function(){
						return new Array(new OpenLayers.Layer.".html_entity_decode($params['defaultBboxConfig']).")
					},
					defaultExtent:{
						left:".$params['defaultBboxConfigExtentLeft'].",bottom:".$params['defaultBboxConfigExtentBottom'].",right:".$params['defaultBboxConfigExtentRight'].",top:".$params['defaultBboxConfigExtentTop']."
					},
					freePerimeter : ".$withFreePerimeter.",
					freePerimeterSelector : ".$freePerimeterSelectorName.",
					initPerimeter:[
						";
						foreach ($selectedBoundaries as $perimeter){
							if(isset($perimeter->northbound)){
								$this->javascript .="{id:";
								$this->javascript .=$perimeter->id;
								$this->javascript .=",label:";
								$this->javascript .="'".$perimeter->label."'";
								$this->javascript .=",northbound:";
								$this->javascript .=$perimeter->northbound;
								$this->javascript .=",southbound:";
								$this->javascript .=$perimeter->southbound;
								$this->javascript .=",eastbound:";
								$this->javascript .=$perimeter->eastbound;
								$this->javascript .=",westbound:";
								$this->javascript .=$perimeter->westbound;
								$this->javascript .="},";
							}
				        }
				        if(!$strictperimeter){
					        foreach ($freeBoundaries as $perimeter){
					        	$this->javascript .="{id:";
					        	$this->javascript .="'".$perimeter->id."'";
					        	$this->javascript .=",label:";
					        	$this->javascript .="'".$perimeter->label."'";
					        	$this->javascript .=",northbound:";
					        	$this->javascript .=$perimeter->northbound;
					        	$this->javascript .=",southbound:";
					        	$this->javascript .=$perimeter->southbound;
					        	$this->javascript .=",eastbound:";
					        	$this->javascript .=$perimeter->eastbound;
					        	$this->javascript .=",westbound:";
					        	$this->javascript .=$perimeter->westbound;
					        	$this->javascript .="},";
					        }
				        }
				        $this->javascript .="
					]					
				};				
			";
		}else if (!$displaymap || $hidden == "true"){ //Object defaultBBoxConfig must be cleared
			$this->javascript .="
				defaultBBoxConfig =undefined;
				";
		}
		
		
		//Map configuration for the free perimeter handling
		if($clone && $displaymap && !$strictperimeter){
			$this->javascript .="
				".$parentFieldsetName."_freeperimeterselector.addListener ('addItemTo',function(record){
					var bounds = new OpenLayers.Bounds(record.data.westbound,record.data.southbound,record.data.eastbound,record.data.northbound);
					var feature = new OpenLayers.Feature.Vector(bounds.toGeometry());
					feature.id = record.data.value;
					this.mapHelper.perimeterLayer.addFeatures(feature);
				}, this);
			
			".$parentFieldsetName."_freeperimeterselector.addListener ('removeItemTo',function(record){
				this.mapHelper.perimeterLayer.removeFeatures(this.mapHelper.perimeterLayer.getFeatureById(record.data.value));
				}, this);
			";
		}
	}
}
?>