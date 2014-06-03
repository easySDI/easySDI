<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_easysdi_map/models/map.php';

abstract class Easysdi_mapHelper {

    public static function getMapScript($mapid, $cleared = false, $appname = "app", $renderto = "sdimapcontainer") {
        $model = JModelLegacy::getInstance('map', 'Easysdi_mapModel');
        $item = $model->getData($mapid);

        //Clear the map from all the tools
        //The goal is to have a clean map to use as a simple and quick data preview
        if ($cleared) {
            $item->tools = array();
            $item->urlwfslocator = null;
        }

        $config = Easysdi_mapHelper::getMapConfig($item, $cleared, $renderto);

        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);

        //Loading css files
        $doc = JFactory::getDocument();
        $base_url = Juri::base(true) . '/administrator/components/com_easysdi_core/libraries';
        $doc->addStyleSheet($base_url . '/ext/resources/css/ext-all.css');
        $doc->addStyleSheet($base_url . '/ext/resources/css/xtheme-gray.css');
        $doc->addStyleSheet($base_url . '/openlayers/theme/default/style.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/popup.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/layerlegend.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/gxtheme-gray.css');
        $doc->addStyleSheet($base_url . '/ux/geoext/resources/css/printpreview.css');
        $doc->addStyleSheet($base_url . '/gxp/theme/all.css');
        $doc->addStyleSheet(Juri::base(true) . '/components/com_easysdi_map/views/map/tmpl/easysdi.css');

        $output = '';

        if (JDEBUG) {
            $doc->addScript($base_url . '/media/jui/js/jquery.js');
            $doc->addScript($base_url . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript($base_url . '/media/jui/js/bootstrap.js');             
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base-debug.js');
            $doc->addScript($base_url . '/ext/ext-all-debug.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');
            $doc->addScript($base_url . '/openlayers/OpenLayers.debug.js');
            $doc->addScript($base_url . '/geoext/lib/GeoExt.js');
            $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');
            $doc->addScript($base_url . '/gxp/script/loader.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/WMSSource.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/OLSource.js');
            $doc->addScript($base_url . '/easysdi/js/sdi/plugins/SearchCatalog.js');
            $doc->addScript($base_url . '/easysdi/js/sdi/plugins/LayerDetailSheet.js');
            $doc->addScript($base_url . '/easysdi/js/sdi/plugins/LayerDownload.js');
            $doc->addScript($base_url . '/easysdi/js/sdi/plugins/LayerOrder.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/LayerTree.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/Print.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/LayerManager.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/BingSource.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/GoogleSource.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/OSMSource.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/LoadingIndicator.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/widgets/ScaleOverlay.js');
            $doc->addScript($base_url . '/easysdi/js/gxp/widgets/Viewer.js');
            $doc->addScript($base_url . '/easysdi/js/geoext/data/PrintProvider.js');
            $doc->addScript($base_url . '/easysdi/js/geoext/ux/PrintPreview.js');
            $doc->addScript($base_url . '/easysdi/js/geoext/widgets/PrintMapPanel.js');

            $doc->addScript(JURI::base(true) . '/media/system/js/mootools-core-uncompressed.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/core-uncompressed.js');
        } else {
            $doc->addScript($base_url . '/media/jui/js/jquery.min.js');
            $doc->addScript($base_url . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript($base_url . '/media/jui/js/bootstrap.min.js');              
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base.js');
            $doc->addScript($base_url . '/ext/ext-all.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');
            $doc->addScript($base_url . '/openlayers/OpenLayers.js');
            $doc->addScript($base_url . '/geoext/lib/geoext.min.js');
            $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');
            $doc->addScript($base_url . '/gxp/script/gxp.min.js');
            $doc->addScript($base_url . '/easysdi/js/sdi.min.js');

            $doc->addScript(JURI::base(true) . '/media/system/js/mootools-core.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/core.js');
        }

//        $files = glob(JURI::base(true) . '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/*.{js}', GLOB_BRACE);
//        foreach ($files as $file) {
//            $doc->addScript($file);
//        }
        
        $doc->addScript(JURI::base(true) . '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/fr.js');
        $doc->addScript(JURI::base(true) . '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/en.js');

        $output .= '<div id="' . $renderto . '" class="cls-' . $renderto . '"></div>';
        $output .= '<script>
            var ' . $appname . ';
            var loadingMask;
            Ext.Container.prototype.bufferResize = false;
            Ext.onReady(function(){
                loadingMask = new Ext.LoadMask(Ext.getBody(), {
                msg:"';
        $output .= JText::_('COM_EASYSDI_MAP_MAP_LOAD_MESSAGE');
        $output .= '"
                });
                loadingMask.show();
                var height = Ext.get("' . $renderto . '").getHeight();
                if(!height)  height = Ext.get("' . $renderto . '").getWidth() * 1/2;
                var width = Ext.get("' . $renderto . '").getWidth();
                OpenLayers.ImgPath = "administrator/components/com_easysdi_core/libraries/openlayers/img/";
                GeoExt.Lang.set("';
        $output .= $lang->getTag();
        $output .= '");
                ' . $appname . ' = new gxp.Viewer(' . $config . ');
                   ';

        //Add the mouseposition control if activated in the map configuration
        //Can not be done in the gxp.Viewer instanciation because it has to be done on the openlayers map object
        foreach ($item->tools as $tool) {
            if ($tool->alias == 'mouseposition') {
                $output .= $appname . '.mapPanel.map.addControl(new OpenLayers.Control.MousePosition());';
                break;
            }
        }

        $output .= 'var locator = null';

        $output .= '
            ' . $appname . '.on("ready", function (){ ';

        if (!empty($item->urlwfslocator)):
            //Only add a wfslocator if it doesn't exist already
            $output .= '
            if(locator == null){
                locator = { xtype: "gxp_autocompletecombo",
                                        listeners:{
                                                    select: function(list, record) {
                                                            var extent = new OpenLayers.Bounds();
                                                            extent.extend(record.data.feature.geometry.getBounds());
                                                            app.mapPanel.map.zoomToExtent(extent);
                                                            }
                                                   },
                                        url: "' . $item->urlwfslocator . '",
                                        fieldName: "' . $item->fieldname . '",
                                        featureType: "' . $item->featuretype . '",
                                        featurePrefix: "' . $item->featureprefix . '",
                                        fieldLabel: "' . $item->fieldname . '",
                                        geometryName:"' . $item->geometryname . '",
                                        maxFeatures:"10",
                                        emptyText: "Search..."};
                app.portal.items.items[0].items.items[0].toolbars[0].add(locator);
                app.portal.items.items[0].items.items[0].toolbars[0].doLayout();
            }';
        endif;

        $output .= '      loadingMask.hide(); 
                });';
        if (!$cleared) {
            $output .= '        SdiScaleLineParams= { 
                            bottomInUnits :"' . $item->bottomInUnits . '",
                            bottomOutUnits :"' . $item->bottomOutUnits . '",
                            topInUnits :"' . $item->topInUnits . '",
                            topOutUnits :"' . $item->topOutUnits . '"
                    }; ';
        }
        $output .= '
                    Ext.QuickTips.init();
                    Ext.apply(Ext.QuickTips.getQuickTip(), {maxWidth: 1000 });
                    Ext.EventManager.onWindowResize(function() {
                        ' . $appname . '.portal.setWidth(Ext.get("' . $renderto . '").getWidth());
                        ' . $appname . '.portal.setHeight(Ext.get("' . $renderto . '").getWidth() * 1/2);
                    });
            });';
        $output .= '</script>';

        return $output;
    }

    /**
     * @param Object        Complete map object (with all linked objects embedded)
     * 
     * @return string       Config JSON object to initialize map
     */
    public static function getMapConfig($item, $cleared, $renderto) {
        $user = JFactory::getUser();
        $app = JFactory::getApplication();
        $params = $app->getParams('com_easysdi_map');

        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);

        $config = '{';
        $proxyhost = $params->get('proxyhost');
        if (!empty($proxyhost)) :
            $config .= 'proxy :"' . $proxyhost . '",';
        else:
            $config .= 'proxy :"' . JURI::base() . "administrator/components/com_easysdi_core/libraries/proxy/proxy.php?=&=" . '",';
        endif;
        $config .= 'about: 
                        { 
                            title: "' . $item->title . '", 
                            "abstract": "' . $item->abstract . '"
                         },
                    portalConfig: 
                        {
                        renderTo:"' . $renderto . '",
                        width: width, 
                        height: height,
                        layout: "border",
                        region: "center",
                        items: [
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
                                        xtype: "gxp_googleearthpanel",
                                        id: "globe",
                                        tbar: [],
                                        mapPanel: "sdimap"
                                    }
                                ]
                            }';
        $config .= ' ,';

        $layertreeactivated = false;
        foreach ($item->tools as $tool) :
            if ($tool->alias == 'layertree') :
                $layertreeactivated = true;
                $config .= '{
                        id: "westpanel",
                        xtype: "panel",
                        header: false,
                        split: true,
                        collapsible: true,
                        collapseMode: "mini",
                        hideCollapseTool: true,
                        layout: "fit",
                        region: "west",
                        width: 200, 
                        items:[ ]
                    },';
                break;
            endif;
        endforeach;

        if (!$layertreeactivated) :
            $config .= '{
                        id: "westpanel",
                        xtype: "panel",
                        header: false,
                        split: false,
                        layout: "fit",
                        region: "west",
                        width: 0
                    },';
        endif;

        foreach ($item->tools as $tool) :
            if ($tool->alias == 'getfeatureinfo') {
                $config .= '{
                                id:"hiddentbar",
                                xtype:"panel",
                                split: false,
                                layout: "fit",
                                height:0,
                                region:"south",
                                items:[]
                            },';
                break;
            }
        endforeach;

        $config .= '
                            ]
                    },                        
                    tools: [';


        $config .= '{
                            ptype: "sdi_gxp_layermanager",
                            rootNodeText: "' . $item->rootnodetext . '",';

        foreach ($item->groups as $group) :
            if ($group->isdefault) {
                //Acces not allowed
                if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                    break;
                $config .= 'defaultGroup: "' . $group->alias . '",';
                break;
            }
        endforeach;

        $config .= 'outputConfig: {
                            id: "tree",
                            border: true,
                            tbar: [] 
                            },
                            groups: {';


        //Groups are added in the order saved in the database
        foreach ($item->groups as $group) :
            //Acces not allowed
            if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                continue;

            if ($group->isbackground) {
                $config .= '
                                    "background": {
                                    title: "' . $group->name . '", 
                                    exclusive: true,';
                if ($group->isdefaultopen) :
                    $config .= 'expanded: true},';
                else :
                    $config .= 'expanded: false},';
                endif;
            }
            else {
                $config .= '"' . $group->alias . '" : {
                                        title : "' . $group->name . '",';
                if ($group->isdefaultopen) :
                    $config .= 'expanded: true},';
                else :
                    $config .= 'expanded: false},';
                endif;
            }
        endforeach;

        $config .= '},';
        $config .= ' outputTarget: "westpanel"
                        },';

        $width = $params->get('iframewidth');
        $height = $params->get('iframeheight');
                        
        foreach ($item->tools as $tool) :
            switch ($tool->alias) :
                case 'googleearth':
                    $config .= '
                    {
                    ptype: "gxp_googleearth",
                    actionTarget: ["map.tbar", "globe.tbar"]
                    },
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'navigationhistory':
                    $config .= '
                    {
                    ptype: "gxp_navigationhistory",
                    actionTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'navigation':
                    $config .= '
                    {
                    ptype: "gxp_navigation",
                    actionTarget: "map.tbar", 
                    toggleGroup: "navigation"
                    },
                    ';
                    break;
                case 'zoom':
                    $config .= '
                    {
                    ptype: "gxp_zoom",
                    actionTarget: "map.tbar",
                    toggleGroup: "navigation",
                    showZoomBoxAction: true,
                    controlOptions: {zoomOnClick: false}
                    },
                    ';
                    break;
                case 'zoomtoextent':
                    if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "gxp_zoomtoextent",
                        actionTarget: "map.tbar"
                        },
                        {
                        ptype: "gxp_zoomtolayerextent",
                        actionTarget: {target: "tree.contextMenu", index: 0}
                        },
                        ';
                    }
                    break;
                case 'measure':
                    $config .= '
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "gxp_measure",
                    toggleGroup: "measure",
                    actionTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'addlayer':
                    if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "gxp_addlayers",
                        actionTarget: "tree.tbar"
                        },
                        ';
                    }
                    break;
                case 'searchcatalog':
                    if ($layertreeactivated) {                        
                        $config .= '
                        {
                        ptype: "sdi_searchcatalog",
                        actionTarget: "tree.tbar",
                        url: "' . JURI::root() . 'index.php?option=com_easysdi_catalog&view=catalog&id=' . $tool->params . '&preview=map&tmpl=component",
                        iwidth : "' . $width . '",
                        iheight : "' . $height . '"
                        },
                        ';
                        }
                        break;
               case 'layerdetailsheet':
                   if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "sdi_layerdetailsheet",
                        actionTarget: ["tree.contextMenu"],
                        iwidth : "' . $width . '",
                        iheight : "' . $height . '"
                        },';
                   }
                    break;
                case 'layerdownload':
                   if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "sdi_layerdownload",
                        actionTarget: ["tree.contextMenu"],
                        iwidth : "' . $width . '",
                        iheight : "' . $height . '"
                        },';
                   }
                    break;
                case 'layerorder':
                   if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "sdi_layerorder",
                        actionTarget: ["tree.contextMenu"],
                        iwidth : "' . $width . '",
                        iheight : "' . $height . '"
                        },';
                   }
                    break;
                case 'removelayer':
                    if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "gxp_removelayer",
                        actionTarget: ["tree.contextMenu"]
                        },
                        ';
                    }
                    break;

                case 'layerproperties':
                    if ($layertreeactivated) {
                        $config .= '
                        {
                        ptype: "gxp_layerproperties",
                        id: "layerproperties",
                        actionTarget: ["tree.contextMenu"]
                        },
                        ';
                    }
                    break;

                case 'getfeatureinfo':
                    $config .= '
                    {
                    ptype: "gxp_wmsgetfeatureinfo",
                    popupTitle: "Feature Info", 
                    toggleGroup: "interaction", 
                    format: "' . $tool->params . '", 
                    actionTarget: "hiddentbar",
                    defaultAction: 0
                    },

                    ';
                    break;
                case 'googlegeocoder':
                    $config .= '
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "gxp_googlegeocoder",
                    outputTarget: "map.tbar"
                    },
                    ';
                    break;
                case 'print':
                    if (!$params->get('printserviceurl'))
                        continue;
                    else
                        $config .= '
                    {
                    actions: ["-"],
                    actionTarget: "map.tbar"
                    },
                    {
                    ptype: "sdi_gxp_print",
                    customParams: {outputFilename: "GeoExplorer-print"},
                    printService: "' . $params->get('printserviceurl') . '",';
                    if ($params->get('printserviceprinturl') == '')
                        $config .= 'printURL : "' . $params->get('printserviceurl') . 'print.pdf",';
                    else
                        $config .= 'printURL : "' . $params->get('printserviceprinturl') . '",';
                    if ($params->get('printservicecreateurl') == '')
                        $config .= ' createURL : "' . $params->get('printserviceurl') . 'create.json",';
                    else
                        $config .= ' createURL : "' . $params->get('printservicecreateurl') . '",';

                    $config .= 'includeLegend: true, 
                    actionTarget: "map.tbar",
                    showButtonText: false
                    },
                    ';
                    break;
            endswitch;
        endforeach;
        $config .= '
                        
        ],';

        // layer sources
        //Default service is always wms
        $config .= '
                defaultSourceType: "sdi_gxp_wmssource",
                ';


        $config .= '
        sources: 
        {
        "ol": { ptype: "sdi_gxp_olsource" }, ';

        foreach ($item->physicalservices as $service) :
            //Acces not allowed
            if (!in_array($service->access, $user->getAuthorisedViewLevels()))
                continue;
            $config .= Easysdi_mapHelper::getServiceDescription($service);
        endforeach;

        if (isset($item->virtualservices)) :
            foreach ($item->virtualservices as $service) {
                $config .= Easysdi_mapHelper::getServiceDescription($service);
            }
        endif;

        $config .= ' 
            },

            // map and layers
            map: 
            {';
        if ($cleared):
            $config .= 'controls : [],';
        endif;
        $config .= 'id: "sdimap",
            title: "Map",
            header:false,
            projection: "' . $item->srs . '",        
            maxExtent : [' . $item->maxextent . '],';
        if (!empty($item->centercoordinates)):
            $config .= '  center: [' . $item->centercoordinates . '],';
        endif;
        if (!empty($item->restrictedextent)):
            $config .= '  restrictedExtent: [' . $item->restrictedextent . '],';
        endif;
        if (!empty($item->zoom)):
            $config .= '  zoom : ' . $item->zoom . ',';
        endif;
        $config .= ' maxResolution: ' . $item->maxresolution . ',
            units: "' . $item->unit . '",
            layers: 
            [
            ';

        //Layers have to be added the lowest before the highest
        //To do that, the groups have to be looped in reverse order
        $groups_reverse = array_reverse($item->groups);
        foreach ($groups_reverse as $group) {
            //Acces not allowed
            if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                continue;

            if (!empty($group->layers)) {
                foreach ($group->layers as $layer) {
                    //Acces not allowed
                    if (!in_array($layer->access, $user->getAuthorisedViewLevels()))
                        continue;

                    $config .= Easysdi_mapHelper::getLayerDescription($layer,$group);
                    
                    
                }
            }
        }
        $config .= '
        ]
        }
        ,';


        if (!$cleared) {
            $config .= ' 
        mapItems: 
        [            
            {
                xtype: "gx_zoomslider",
                vertical: true,
                height: 100
            }        
            ,
            {
                xtype: "gxp_scaleoverlay"
            }
        ],
        ';
        }
        $config .= '
        mapPlugins:
        [
            {
                ptype: "sdi_gxp_loadingindicator",
                loadingMapMessage: "' . JText::_('COM_EASYSDI_MAP_LAYER_LOAD_MESSAGE') . '"
            }
        ]
';
        $config .='}';

        return $config;
    }
    
    
    public static function getServiceDescription($service){
        $url='';
        //Initilization of the service url if the service is physic or virtual
        if (isset($service->resourceurl)){
            $url = $service->resourceurl;
        }elseif (isset($service->url)){
            $url = $service->url;
        }
            $config='';
            switch ($service->serviceconnector_id) :
                case 2 :
                    $config = ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_wmssource",
                    url: "' . $url . '"
                    },
                    ';
                    break;
                case 11 :
                    $config = ' 
                    "' . $service->alias . '":
                    {
                    ptype: "gxp_wmscsource",
                     url: "' . $url . '"
                    },
                    ';
                    break;
                case 12 :
                    $config = ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_bingsource"
                    },
                    ';
                    break;
                case 13 :
                    $config = ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_googlesource"
                    },
                    ';
                    break;
                case 14 :
                    $config = ' 
                    "' . $service->alias . '":
                    {
                    ptype: "sdi_gxp_osmsource"
                    },
                    ';
                    break;
            endswitch;
            return $config;
    }
    
    
    public static function getExtraServiceDescription($service){
        $url='';
        $config='';
        //Initilization of the service url if the service is physic or virtual
        if (isset($service->resourceurl)){
            $url = $service->resourceurl;
        }elseif (isset($service->url)){
            $url = $service->url;
        }
            switch ($service->serviceconnector_id) :
                case 2 :
                    $config ='{id:"' . $service->alias . '",';
                    $config .= '
                    ptype: "sdi_gxp_wmssource",
                    hidden : "true",
                    url: "' . $url . '"
                    }
                    ';
                    break;
                case 11 :
                    $config ='{id:"' . $service->alias . '",';
                    $config .= ' 
                    ptype: "gxp_wmscsource",
                    hidden : "true",
                    url: "' . $url . '"
                    }
                    ';
                    break;
                case 12 :
                    $config ='{id:"' . $service->alias . '",';
                    $config .= '
                    ptype: "sdi_gxp_bingsource",
                    hidden : "true",
                    }
                    ';
                    break;
                case 13 :
                    $config ='{id:"' . $service->alias . '",';
                    $config .= '
                    ptype: "sdi_gxp_googlesource",
                    hidden : "true",
                    }
                    ';
                    break;
                case 14 :
                    $config ='{id:"' . $service->alias . '",';
                    $config .= '
                    ptype: "sdi_gxp_osmsource",
                    hidden : "true",
                    }
                    ';
                    break;
                default :
                    $config ='{id:"' . $service->alias . '",';
                    $config .= '
                    ptype: "sdi_gxp_olsource",
                    hidden : "true",
                    }
                    ';
            endswitch;
            return $config;
    }
        
    public static function getLayerDescription($layer, $group){
        $config = ' { ';

        if ($layer->asOL) {
            $config .= 'source : "ol", ';

            switch ($layer->serviceconnector) {
                case 'WMTS' :
                    $config .= ' 
                    type: "OpenLayers.Layer.WMTS",
                    args: [
                    {
                    name:"' . $layer->name . '", 
                    url : "' . $layer->serviceurl . '", 
                    layer: "' . $layer->layername . '", ';

                    if ($layer->isdefaultvisible == 1)
                        $config .= 'visibility: true,';
                    else
                        $config .= 'visibility: false,';

                    if ($layer->istiled == 1)
                        $config .= 'singleTile: true,';
                    else
                        $config .= 'singleTile: false,';

                    $config .= 'transitionEffect: "resize",
                    opacity: ' . $layer->opacity . ',
                    style: "' . $layer->asOLstyle . '",
                    matrixSet: "' . $layer->asOLmatrixset . '",';

                    $config .= $layer->asOLoptions;

                    $config .=' }
                    ],';

                    break;
                case 'WMS' :
                case 'WMSC' :
                    $config .= ' 

                    type : "OpenLayers.Layer.WMS",
                    args: 
                    [
                    "' . $layer->name . '",
                    "' . $layer->serviceurl . '",
                    {
                    layers: "' . $layer->layername . '", 
                    version: "' . $layer->version . '"';
                    if($layer->serviceconnector == 'WMSC'):
                        $config .= ', tiled: true';
                    endif;

                    $config .= '
                    },
                    {';

                    if ($layer->isdefaultvisible == 1)
                        $config .= 'visibility :  true';
                    else
                        $config .= 'visibility :  false';
                    $config .= ',';

                    if ($layer->istiled == 1)
                        $config .= 'singleTile :  true';
                    else
                        $config .= 'singleTile :  false';
                    $config .=',
                    opacity: ' . $layer->opacity . ',
                    transitionEffect: "resize",
                    style: "' . $layer->asOLstyle . '",';


                    $config .= $layer->asOLoptions;
                    $config .= '}
                    ],';
                    break;
            }
            if ($group->isbackground)
                $config .= 'group: "background",';
            else
                $config .= 'group: "' . $group->alias . '",';
        }
        else {
            switch ($layer->serviceconnector) {
                case 'WMTS':
                    break;
                default :
                    $config .= '
                    source: "' . $layer->servicealias . '",';

                    if ($layer->istiled == 1)
                        $config .= 'tiled :  true,';
                    else
                        $config .= 'tiled :  false,';

                    if (!empty($layer->version)) {
                        $config .= 'version: "' . $layer->version . '",';
                    }

                    if (!empty($layer->attribution)) {
                        $config .= 'attribution: "' . $layer->attribution . '",';
                    }
                    $config .= 'name: "' . $layer->layername . '",
                    title: "' . $layer->name . '",';
                    if ($group->isbackground)
                        $config .= ' group : "background",';
                    else
                        $config .= ' group : "' . $group->alias . '",';
                    if ($group->alias == "background")
                        $config .= 'fixed: true,';

                    if ($layer->isdefaultvisible == 1)
                        $config .= 'visibility :  true,';
                    else
                        $config .= 'visibility :  false,';

                    $config .= 'opacity: ' . $layer->opacity . ',

                    ';
                    break;
            }
        }

        if (!empty($layer->metadata_guid)):
            $config .= 'href: "' . Easysdi_mapHelper::getLayerDetailSheetToolUrl($layer->metadata_guid, JFactory::getLanguage()->getTag(), '', 'map') . '",';
        elseif(!empty($layer->metadatalink)):
            $config .= 'href: "' . $layer->metadatalink . '",';
        endif;
        if (!empty($layer->hasdownload)):
            $config .= 'download: "' . Easysdi_mapHelper::getLayerDownloadToolUrl($layer->diffusion_id) . '",';
        endif;
        if (!empty($layer->hasextraction)):
            $config .= 'order: "' . Easysdi_mapHelper::getLayerOrderToolUrl($layer->metadata_guid, JFactory::getLanguage()->getTag(), '') . '",';
        endif;

        $config .= ' }, ';
        
        return $config;
    }
    
    public static function getLayerDownloadToolUrl ($diffusion_id){
        return htmlentities(JURI::root() . 'index.php?option=com_easysdi_shop&task=download.direct&tmpl=component&id=' . $diffusion_id);
    }
    
    public static function getLayerOrderToolUrl ($metadata_guid, $lang, $catalog){
        return htmlentities(JURI::root() . 'index.php?option=com_easysdi_catalog&view=sheet&guid=' . $metadata_guid . '&lang=' . $lang . '&catalog=' . $catalog . '&type=shop&preview=map&tmpl=component');
    }
    
    public static function getLayerDetailSheetToolUrl ($metadata_guid, $lang, $catalog, $preview){
        return htmlentities(JURI::root() . 'index.php?option=com_easysdi_catalog&view=sheet&guid=' . $metadata_guid . '&lang=' . $lang . '&catalog=' . $catalog . '&preview=' . $preview . '&tmpl=component');
    }

}

