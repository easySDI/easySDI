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

if( $this->item ) : ?>

		<!-- Ext resources -->
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/ext/resources/css/ext-all.css">
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/ext/resources/css/xtheme-gray.css">
        <script type="text/javascript" src="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/ext/ext-all.js"></script> 
        <script type="text/javascript" src="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/ux/RowExpander.js"></script> 

        <!-- OpenLayers resources -->
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/openlayers/theme/default/style.css">
        <script type="text/javascript" src="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/openlayers/lib/OpenLayers.js"></script>

        <!-- GeoExt resources -->
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/geoext/resources/css/popup.css">
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/geoext/resources/css/layerlegend.css">
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/geoext/resources/css/gxtheme-gray.css">
        <script type="text/javascript" src="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/geoext/lib/GeoExt.js"></script>

        <!-- gxp resources -->
        <link rel="stylesheet" type="text/css" href="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/gxp/theme/all.css">
        <script type="text/javascript" src="http://localhost/opcc/administrator/components/com_easysdi_core/libraries/gxp/script/loader.js"></script>
        
        <!-- app resources -->
        <link rel="stylesheet" type="text/css" href="components/com_easysdi_map/views/context/tmpl/theme/app/style.css">
        <script src="http://maps.google.com/maps/api/js?v=3.6&sensor=false"></script>
        
		
        <script>
       
        var app;

		Ext.onReady(function(){
			Ext.BLANK_IMAGE_URL = "http://localhost/opcc//components/com_easysdi_map/views/context/tmpl/theme/app/img/blank.gif";
            OpenLayers.ImgPath = "http://localhost/opcc/administrator/components/com_easysdi_core/libraries/openlayers/img/";

            app = new gxp.Viewer(
            {
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
	                        xtype: "container",
	                        layout: "fit",
	                        region: "west",
	                        width: 200
                    	}
                    ],
                    
                },
                
                // configuration of all tool plugins for this application
                tools: 
                [
					{
					    ptype: "gxp_layertree",
					    outputConfig: {
					        id: "tree",
					        border: true,
					        tbar: [] // we will add buttons to "tree.tbar" later
					    },
					    outputTarget: "westpanel"
					}, 
                <?php 
                foreach ($this->item->tools as $tool)
                {
                	switch ($tool->alias)
                	{
                		case 'print':
                			?>
                			{
                				ptype: "gxp_print",
                				customParams: {
                					outputFilename: 'GeoExplorer-print'},
                					printService: 'http://suite.opengeo.org/geoserver/pdf/create.json',
                					actionTarget: "map.tbar",
                					showButtonText: true
                			},
                			<?php
                			break;
                		case 'navigation':
                			?>
                			{
                				ptype: "gxp_navigation", 
                				toggleGroup: "navigation"
                			},
                			<?php 
                			break;
                		case 'googlegeocoder':
                			?>
							{
								ptype: "gxp_googlegeocoder",
					            outputTarget: "map.tbar"
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
                		case 'zoom':
                			?>
                			 {
                                 ptype: "gxp_zoom",
                                 actionTarget: "map.tbar",
                                 toggleGroup: "navigation"
                             },
                			<?php 
                			break;
                		case 'zoomtoextent':
                			?>
                			{
                                ptype: "gxp_zoomtoextent",
                                actionTarget: "map.tbar"
                            },
                			<?php 
                			break;
                		case 'measure':
                			?>
                			{
                				ptype: "gxp_measure",
                                actionTarget: "map.tbar",
                                toggleGroup: "interaction"
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
                		case 'styler':
                			?>
                			{
                				ptype: "gxp_styler",
                				outputConfig: {
                					autoScroll: true, width: 320},
                				actionTarget: ["tree.tbar", "tree.contextMenu"]
                				
                			},
                			<?php 
                			break;
                		case 'layerproperties':
                			?>
                			{
                				ptype: "gxp_layerproperties",
                				id: "layerproperties",
                				outputConfig: 
                    			{
                					defaults: 
                    				{
                						autoScroll: true
                					}, 
                				width: 320
                				},
                				actionTarget: ["tree.tbar", "tree.contextMenu"],
                				outputTarget: "tree"
                			},
                			<?php 
                			break;
                		case 'googleearth':
                			?>
                			{
                				ptype: "gxp_googleearth",
                				actionTarget: ["map.tbar", "globe.tbar"]
                			},
                			<?php
                			break;
                		case 'getfeatureinfo':
                			?>
                			{
                				ptype: "gxp_wmsgetfeatureinfo", 
                				format: 'grid',
                				toggleGroup: "interaction",
                				showButtonText: true,
                				actionTarget: "map.tbar"
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
                    local: 
                    {
                        ptype: "gxp_wmssource",
                        url: "http://localhost/geoserverwms",
                        version: "1.3.0"
                    },
                    osm: 
                    {
                        ptype: "gxp_osmsource"
                    }
                },
                
                // map and layers
                map: 
                {
                    id: "sdimap", // id needed to reference map in portalConfig above
                    title: "Map",
                    header:false,
                    projection: "EPSG:900913",
                    center: [-10764594.758211, 4523072.3184791],
                    zoom: 3,
                    layers: 
                    [
                     	{
	                        source: "osm",
	                        name: "mapnik",
	                        group: "background"
                    	}, 
                    	{
	                        source: "local",
	                        name: "topp:states",
	                        selected: true
                    	}
                    ],
                    items: 
                    [
                     	{
	                        xtype: "gx_zoomslider",
	                        vertical: true,
	                        height: 100
                    	}
                    ]
                }

            });
    	});
        </script>
<?php else: ?>
    Could not load the item
<?php endif; ?>
