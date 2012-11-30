<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// no direct access
defined('_JEXEC') or die;

//Load admin language file
$lang = JFactory::getLanguage();
$lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);
$user = JFactory::getUser();

if( $this->item ) : 

JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_core/libraries/ext/adapter/ext/');
JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_core/libraries/ext/');
JHTML::script('RowExpander.js', 'administrator/components/com_easysdi_core/libraries/ux/ext/');
JHTML::script('OpenLayers.js', 'administrator/components/com_easysdi_core/libraries/openlayers/lib/');
JHTML::script('GeoExt.js', 'administrator/components/com_easysdi_core/libraries/geoext/lib/');
JHTML::script('PrintPreview.js', 'administrator/components/com_easysdi_core/libraries/ux/GeoExt/');
JHTML::script('loader.js', 'administrator/components/com_easysdi_core/libraries/gxp/script/');
JHTML::script('LayerTree.js', 'administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/');
JHTML::script('Print.js', 'administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/');
JHTML::script('LayerManager.js', 'administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/plugins/');
JHTML::script('PrintProvider.js', 'administrator/components/com_easysdi_core/libraries/easysdi/js/geoext/data/');

JHTML::_('stylesheet', 'ext-all.css', 'administrator/components/com_easysdi_core/libraries/ext/resources/css/');
JHTML::_('stylesheet', 'xtheme-gray.css', 'administrator/components/com_easysdi_core/libraries/ext/resources/css/');
JHTML::_('stylesheet', 'style.css', 'administrator/components/com_easysdi_core/libraries/openlayers/theme/default/');
JHTML::_('stylesheet', 'popup.css', 'administrator/components/com_easysdi_core/libraries/geoext/resources/css/');
JHTML::_('stylesheet', 'layerlegend.css', 'administrator/components/com_easysdi_core/libraries/geoext/resources/css/');
JHTML::_('stylesheet', 'gxtheme-gray.css', 'administrator/components/com_easysdi_core/libraries/geoext/resources/css/');
JHTML::_('stylesheet', 'printpreview.css', 'administrator/components/com_easysdi_core/libraries/ux/geoext/resources/css/');
JHTML::_('stylesheet', 'all.css', 'administrator/components/com_easysdi_core/libraries/gxp/theme/');
JHTML::_('stylesheet', 'style.css', 'components/com_easysdi_map/views/context/tmpl/theme/app/');
?>
      <script>
      	var app;
      	Ext.Container.prototype.bufferResize = false;
		Ext.onReady(function(){
			OpenLayers.ImgPath = "administrator/components/com_easysdi_core/libraries/openlayers/img/";

			GeoExt.Lang.set("<?php echo $lang->getTag(); ?>");
			
            app = new gxp.Viewer(
            {
            	<?php
            	$proxyhost = $this->params->get('proxyhost');
            	if (!empty($proxyhost))
            	{
            	?> 
            	proxy:"/cgi-bin/proxy.cgi?url=",
                <?php
				}
                ?>
            	about: { 
			    	title: "<?php echo $this->item->title;?>", 
			    	"abstract": "<?php echo $this->item->abstract;?>" 
			    	}, 
            	portalConfig: 
                {
                    layout: "border",
                    region: "center",
                   
                    
                    items: 
                    [
                     	{
                     		id:"portaltbar",
						    xtype:"toolbar",
						    border: false,
						    height:35,
						    region:"north",
						    items:[]
						    
						},
                        {
	                        id: "centerpanel",
	                        xtype: "panel",
	                        layout: "card",
	                        region: "center",
	                        border: false,
	                        activeItem: 0, 
	                        items: [
			    	                    "sdimap",
			                            {
				                            xtype: 'gxp_googleearthpanel',
				                            id: "globe",
				                            tbar: [],
				                            mapPanel: "sdimap"
		                        		}
	                  				]
                    	}, 
                    	{
	                    	id: "westpanel",
	                        xtype: "panel",
	                        header: false,
	                        split: true,
	                        collapsible: true,
	                        collapseMode: "mini",
	                        hideCollapseTool: true,
	                        layout: "fit",
	                        region: "west",
	                        width: 200
                    	},
                    	{
                     		id:"hiddentbar",
						    xtype:"toolbar",
						    border: false,
						    height:0,
						    region:"south",
						    items:[]
						    
						}
                    ],
                    
                },
                
                // Tools
                tools: 
                [
                 {
					    ptype: "sdi_gxp_layermanager",
					    outputConfig: {
					        id: "tree",
					        border: true,
					        tbar: [] 
					    },
					    groups: {
						    <?php
						    foreach ($this->item->groups as $group)
						    {
						    	//Acces not allowed
						    	if(!in_array($group->access, $user->getAuthorisedViewLevels()))
						    		continue;
						    
						    	if($group->alias == 'background')
						    	{
						    		?>
						    		"background": {
							            title: "<?php echo $group->name; ?>", 
							            exclusive: true,
							            expanded: <?php if ($group->isdefaultopen) echo "true"; else echo "false";?>
							        },
						    		<?php
						    	}
						    	else
						    	{
							    	?>
							    	"<?php echo $group->alias; ?>" : {
								    	title : "<?php echo $group->name; ?>",
								    	expanded: <?php if ($group->isdefaultopen) echo "true"; else echo "false";?> 
							    	},
							    	<?php
								}
						    } 
						    ?>        
					    },
					    outputTarget: "westpanel"
					},
                <?php 
                foreach ($this->item->tools as $tool)
                {
                	switch ($tool->alias)
                	{
                		case 'googleearth':
                			?>
                		    {
                		    	ptype: "gxp_googleearth",
                		        actionTarget: ["map.tbar", "globe.tbar"]
                		    },
                		    {
                                actions: ["-"],
                                actionTarget: "map.tbar"
                            },
                		    <?php
                		    break;
                		case 'navigationhistory':
                			?>
                			{
                                ptype: "gxp_navigationhistory",
                                actionTarget: "map.tbar"
                            },
                			<?php 
                			break;
                		case 'navigation':
                			?>
                				{
                    				ptype: "gxp_navigation",
                			    	actionTarget: "map.tbar", 
                			        toggleGroup: "navigation"
                			    },
                			    <?php 
                			    break;
                		case 'zoom':
                			?>
                			 {
                                 ptype: "gxp_zoom",
                                 actionTarget: "map.tbar",
                                 toggleGroup: "navigation",
                                 showZoomBoxAction: true,
                                 controlOptions: {zoomOnClick: false}
                             },
                			<?php 
                			break;
                		case 'zoomtoextent':
                			?>
                			{
                                ptype: "gxp_zoomtoextent",
                                actionTarget: "map.tbar"
                            },
                            {
                                ptype: "gxp_zoomtolayerextent",
                                actionTarget: {target: "tree.contextMenu", index: 0}
                            },
                			<?php 
                			break;
                		case 'measure':
                			?>
                			{
                                actions: ["-"],
                                actionTarget: "map.tbar"
                            },
                			{
                				ptype: "gxp_measure",
                                actionTarget: "map.tbar"
                			},
                			<?php 
                			break;
                		case 'addlayer':
                			?>
                			{
                                ptype: "gxp_addlayers",
                                actionTarget: "tree.tbar"
                            },
                			<?php 
                			break;
                		case 'removelayer':
                			?>
                            {
                               ptype: "gxp_removelayer",
                               actionTarget: ["tree.tbar", "tree.contextMenu"]
                            },
                			<?php 
                			break;
                		
                		case 'layerproperties':
                			?>
                			{
                				ptype: "gxp_layerproperties",
                				id: "layerproperties",
                				actionTarget: ["tree.tbar", "tree.contextMenu"]
                			},
                			<?php 
                			break;
                		
                		case 'getfeatureinfo':
                			?>
                			
                			{
                				ptype: "gxp_wmsgetfeatureinfo",
                				toggleGroup: "interaction", 
                				format: "grid", 
                				actionTarget: "hiddentbar",
                				defaultAction: 0
                			},
                			<?php 
                			break;
                		case 'googlegeocoder':
                			?>
                			{
                                actions: ["-"],
                                actionTarget: "map.tbar"
                            },
                			{
                				ptype: "gxp_googlegeocoder",
                				outputTarget: "map.tbar"
                			},
                			<?php
                			break;
                		case 'print':
                			if(!$this->params->get('printserviceurl'))
                				continue;
                			else 
                			?>
                			{
                                actions: ["-"],
                                actionTarget: "map.tbar"
                            },
                			{
                				ptype: "sdi_gxp_print",
                				customParams: {outputFilename: 'GeoExplorer-print'},
                			    printService: "<?php echo $this->params->get('printserviceurl');?>",
                			    printURL: "<?php if($this->params->get('printserviceprinturl')=='') echo $this->params->get('printserviceurl').'print.pdf'; else  echo $this->params->get('printserviceprinturl');?>",
                			    createURL: "<?php if($this->params->get('printservicecreateurl') == '') echo  $this->params->get('printserviceurl').'create.json'; else  echo $this->params->get('printservicecreateurl');?>",
                			    includeLegend: true, 
                			    actionTarget: "map.tbar",
                			    showButtonText: true
                			},
                			<?php
                			break;
                	}
                }
                ?>
                ],
                
                // layer sources
                sources: 
                {
                	"ol": { ptype: "gxp_olsource" }, 
                
                	<?php
                    foreach ($this->item->physicalservices as $service)
                	{
                		//Acces not allowed
                		if(!in_array($service->access, $user->getAuthorisedViewLevels()))
                			continue;
                		?>
                		"<?php echo $service->alias ?>":
                		{
                    		<?php
                    		switch ($service->serviceconnector_id)
                    		{
                    			case 2 :
                    				?>
                    				ptype: "gxp_wmssource",
                    				url: "<?php echo $service->resourceurl;?>"
                        			<?php
                    				break;
                    			case 11 :
                    				?>
                    				ptype: "gxp_wmscsource",
                    				url: "<?php echo $service->resourceurl;?>"
                        			<?php
                    				break;
                    			case 12 :
                    				?>
                    				ptype: "gxp_bingsource"
                    				<?php
                    				break;
                    			case 13 : 
                    				?>
                    				ptype: "gxp_googlesource"
                    				<?php
                    				break;
                    			case 14 :
                    				?>
                    				ptype: "gxp_osmsource"
                    				<?php
                    				break;
                    		}
                    		?>
                		},
                		<?php
                	}
                	foreach ($this->item->virtualservices as $service)
                	{
                		switch ($service->serviceconnector_id)
                		{
                		  	case 2 :
                		   	?>
                		       	"<?php echo $service->alias ?>":
                        	 	{
	                	            ptype: "gxp_wmssource",
	                	            url: "<?php echo $service->url;?>"
                        	 	},
                		    <?php
                		    break;
                		}
                		        
                	}
                	?>
                   
                },
                
                // map and layers
                map: 
                {
                    id: "sdimap", // id needed to reference map in portalConfig above
                    title: "Map",
                    header:false,
                    projection: "<?php echo $this->item->srs;?>",
                    center: [<?php echo $this->item->centercoordinates;?>],
                    maxExtent : [<?php echo $this->item->maxextent;?>],
                    restrictedExtent: [<?php echo $this->item->maxextent;?>],
                    numZoomLevels: <?php echo $this->item->numzoomlevel;;?>,
                    layers: 
                    [
                     <?php
                     foreach ($this->item->groups as $group)
                     {
                     	//Acces not allowed
                     	if(!in_array($group->access, $user->getAuthorisedViewLevels()))
                     		continue;
                     
                     	if(!empty ($group->layers) )
                     	{
                     		foreach ($group->layers as $layer)
                     		{
                     			//Acces not allowed
                     			if(!in_array($layer->access, $user->getAuthorisedViewLevels()))
                     				continue;
                     		
                     			if($layer->asOL || $layer->serviceconnector == 'WMTS')
                     			{
                     				switch ($layer->serviceconnector)
                     				{
                     					case 'WMTS' :
                     						?>
                     						{
                     							source: "ol",
                     						    type: "OpenLayers.Layer.WMTS",
                     						    args: [
                     						    	{
                     						        	name:"<?php echo $layer->name;?>", 
                     						            url : "<?php echo $layer->serviceurl;?>", 
                     						            layer: "<?php echo $layer->layername;?>", 
                     						            visibility: <?php  if ($layer->isdefaultvisible) echo "true"; else echo "false"; ?>,
                     						            opacity: <?php echo $layer->opacity;?>
                     						            <?php if(!empty($layer->asOLparams) || !empty($layer->metadatalink)){?>
                    									,
                    									<?php if (!empty($layer->metadatalink)){?>
                    				   			        metadataURL: "<?php echo $layer->metadatalink;  ?>"
                    					   			    <?php if(!empty($layer->asOLparams)) echo ',';?>
                    				   			        <?php }?>
                    				   			        <?php 
                    			                     		echo  $layer->asOLparams;
                    			                     	}?>
                     						         }
                     						     ],
                     						     group: "<?php echo $group->alias;?>"
                     						 },
                     						 <?php
                     						break;
                     					case 'WMS' : 
                     						?>
                     						{
                         						source : "ol",
                         						type : "OpenLayers.Layer.WMS",
                         						
                         						args: 
                             					[
													"<?php echo $layer->name;?>",
													"<?php echo $layer->serviceurl;?>",
													{
														
														layers: "<?php echo $layer->layername;?>", 
														version: "<?php echo $layer->version;  ?>",
														visibility: <?php  if ($layer->isdefaultvisible) echo "true"; else echo "false"; ?>,
                     						            opacity: <?php echo $layer->opacity;?>
													}
													<?php
													if(!empty($layer->asOLparams) || !empty($layer->metadatalink))
													{
														?>
														 ,{
															 <?php 
															 if (!empty($layer->metadatalink)){
															 ?>
				   			                     				metadataURL: "<?php echo $layer->metadatalink;  ?>"
					   			                     		 <?php if(!empty($layer->asOLparams)) echo ',';?>
				   			                     			 <?php }?>
				   			                     			 <?php 
			                     						           echo  $layer->asOLparams;
			                     						     ?>
														 }
														 <?php 
													}
                     						        ?>
                                				],
                    						    group: "<?php echo $group->alias;?>"
                     						},
                     						<?php 
                     						break;
                     				}
                     			}
                     			else 
                     			{
	                     			switch ($layer->serviceconnector)
	                     			{
	                     				case 'WMTS':
	                     					 break;
										default :
	                     			    	?>
											{
			                     				source: "<?php echo $layer->servicealias;  ?>",
			                     				version: "<?php echo $layer->version;  ?>",
			                     				<?php if (!empty($layer->metadatalink)){?>
			                     				metadataURL: "<?php echo $layer->metadatalink;  ?>",
			                     				<?php }?>
			                     				name: "<?php echo $layer->layername;?>",
			                     				group: "<?php echo $group->alias;?>",
			                     				<?php if ($group->alias == "background") echo "fixed: true,";?>
			                     				visibility: <?php  if ($layer->isdefaultvisible) echo "true"; else echo "false"; ?>,
			                     				opacity: <?php echo $layer->opacity;?>
			                     			},
			                     			<?php
			                     			break;
									}
								}	
							}
                     	}
                     } 
                     ?>
                    ]
                }
                ,
                mapItems: 
                [
                 	{
                        xtype: "gx_zoomslider",
                        vertical: true,
                        height: 100
                	},
                	{
                	 	xtype: "gxp_scaleoverlay"
                    }
                ]
            });

    	});
        </script>
<?php else: ?>
    Could not load the item
<?php endif; ?>
