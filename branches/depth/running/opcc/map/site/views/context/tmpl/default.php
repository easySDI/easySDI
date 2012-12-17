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

JHTML::script('ext-base.js', 'administrator/components/com_easysdi_core/libraries/ext/adapter/ext/');
JHTML::script('ext-all.js', 'administrator/components/com_easysdi_core/libraries/ext/');
JHTML::script('RowExpander.js', 'administrator/components/com_easysdi_core/libraries/ux/ext/');
JHTML::script('OpenLayers.js', 'administrator/components/com_easysdi_core/libraries/openlayers/');
JHTML::script('geoext.min.js', 'administrator/components/com_easysdi_core/libraries/geoext/lib/');
JHTML::script('PrintPreview.js', 'administrator/components/com_easysdi_core/libraries/ux/geoext/');
JHTML::script('gxp.min.js', 'administrator/components/com_easysdi_core/libraries/gxp/script/');

JHTML::script('sdi.min.js', 'administrator/components/com_easysdi_core/libraries/easysdi/js/');


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
	<div id="sdimapcontainer" class="cls-sdimapcontainer">
	</div>
      <script>
      	var app;
      	var loadingMask;
      	
      	Ext.Container.prototype.bufferResize = false;
		Ext.onReady(function(){

			loadingMask = new Ext.LoadMask(Ext.getBody(), {
	                msg: "<?php echo JText::_('COM_EASYSDI_MAP_CONTEXT_LOAD_MESSAGE');?>"
	            });
            loadingMask.show();

            var height = Ext.get("sdimapcontainer").getHeight();
            if(!height)  height = Ext.get("sdimapcontainer").getWidth() * 2/3;
            var width = Ext.get("sdimapcontainer").getWidth();
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
            		renderTo:"sdimapcontainer",
            		width: width, 
            	    height: height,
                    layout: "border",
                    region: "center",
                    items: 
                    [
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
					    rootNodeText: "<?php echo $this->item->rootnodetext;?>",
					    <?php
					    foreach ($this->item->groups as $group)
					    {
					    	if($group->isdefault)
					    	{
					    		//Acces not allowed
					    		if(!in_array($group->access, $user->getAuthorisedViewLevels()))
					    			break;
					    		?>
					    		defaultGroup: "<?php echo $group->alias; ?>",
					    		<?php
					    		break;
					    	}
					    	
					    } 
					    ?>        
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
						    
						    	if($group->isbackground)
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
                				toggleGroup: "measure",
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
                			    showButtonText: false
                			},
                			<?php
                			break;
                	}
                }
                ?>
                ],
                
                // layer sources
                <?php
				switch ($this->item->defaultserviceconnector_id)
                {
                	case 2 :
                    	?>
                    	defaultSourceType: "gxp_wmssource",
                        <?php
                    	break;
                    case 11 :
                    	?>
                    	defaultSourceType: "gxp_wmscsource",
                        <?php
                    	break;
                }
                ?>
                
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
                		    case 11 :
                		    ?>
                		       	"<?php echo $service->alias ?>":
                        	 	{
	                	            ptype: "gxp_wmscsource",
	                	            url: "<?php echo $service->url;?>"
                        	 	},
                		    <?php
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
                	maxResolution: <?php echo $this->item->maxresolution;?>,
                	units: "<?php echo $this->item->unit;?>",
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
                     						            visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
                     						            singleTile: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
                     						            transitionEffect: 'resize',
                     						            opacity: <?php echo $layer->opacity;?>,
                     						           	style: "<?php echo $layer->asOLstyle;  ?>",
                     						           	matrixSet: "<?php echo $layer->asOLmatrixset;  ?>",
                     						            <?php if (!empty($layer->metadatalink)){?>
                    				   			        metadataURL: "<?php echo $layer->metadatalink;  ?>",
                    					   			    <?php }?>
                    				   			        <?php 
                    			                     	echo  $layer->asOLoptions;
                    			                     	?>
                     						         }
                     						     ],
                     						     group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>"
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
														version: "<?php echo $layer->version;  ?>"
													},
													{
														 visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
														 singleTile: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
														 opacity: <?php echo $layer->opacity;?>,
														 transitionEffect: 'resize',
														 style: "<?php echo $layer->asOLstyle;  ?>",
														 <?php 
														 if (!empty($layer->metadatalink)){
														 ?>
			   			                     				metadataURL: "<?php echo $layer->metadatalink;  ?>",
				   			                     		 <?php }?>
			   			                     			<?php echo  $layer->asOLoptions; ?>
													}
                                				],
                                				group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>"
                     						},
                     						<?php 
                     						break;
                     					case 'WMSC' :
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
                     									tiled: true
                     								},
                     								{
                     									visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
                     									singleTile: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
                     									opacity: <?php echo $layer->opacity;?>,
                     									transitionEffect: 'resize',
                     									style: "<?php echo $layer->asOLstyle;  ?>",
                     									<?php 
                     									if (!empty($layer->metadatalink)){
                     									?>
                     									metadataURL: "<?php echo $layer->metadatalink;  ?>",
                     									<?php }?>
                     									<?php echo  $layer->asOLoptions; ?>
                     									}
                     						     ],
                     						    group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>"
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
			                     				//tiled value gives the transitionEffect value see WMSSource.js l.524
			                     				tiled: <?php if ($layer->istiled == 1) echo "true"; else echo "false"; ?>,
			                     				<?php if (!empty($layer->version)){?>
			                     				version: "<?php echo $layer->version;  ?>",
												<?php }?>
			                     				<?php if (!empty($layer->metadatalink)){?>
			                     				metadataURL: "<?php echo $layer->metadatalink;  ?>",
			                     				<?php }?>
			                     				name: "<?php echo $layer->layername;?>",
			                     				group: "<?php if($group->isbackground)echo 'background'; else echo $group->alias;?>",
			                     				<?php if ($group->alias == "background") echo "fixed: true,";?>
			                     				visibility: <?php  if ($layer->isdefaultvisible == 1) echo "true"; else echo "false"; ?>,
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
                ],
                mapPlugins:
                [
					{
					    ptype: 'gxp_loadingindicator',
					    loadingMapMessage: '<?php echo JText::_('COM_EASYSDI_MAP_LAYER_LOAD_MESSAGE');?>'
					}
                ]
            });
            
            app.on("ready", function (){
            	loadingMask.hide();
            });

    	});
        </script>
<?php else: ?>
    Could not load the item
<?php endif; ?>
