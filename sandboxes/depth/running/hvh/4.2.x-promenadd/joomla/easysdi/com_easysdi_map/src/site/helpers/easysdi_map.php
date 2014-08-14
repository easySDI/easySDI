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
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/bootstrap.js');
            $doc->addScript($base_url . '/proj4js-1.1.0/lib/proj4js.js');
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base-debug.js');
            $doc->addScript($base_url . '/ext/ext-all-debug.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');
            $doc->addScript($base_url . '/OpenLayers-2.13.1/OpenLayers.debug.js');
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
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery.min.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/bootstrap.min.js');
            $doc->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base.js');
            $doc->addScript($base_url . '/ext/ext-all.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');
            $doc->addScript($base_url . '/OpenLayers-2.13.1/OpenLayers.js');
            $doc->addScript($base_url . '/geoext/lib/geoext.min.js');
            $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');
            $doc->addScript($base_url . '/gxp/script/gxp.min.js');
            $doc->addScript($base_url . '/easysdi/js/sdi.min.js');

            $doc->addScript(JURI::base(true) . '/media/system/js/mootools-core.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/core.js');
        }

        $doc->addScript($base_url . '/OpenLayers-2.13.1/InlineXhtml/lib/OpenLayers/Layer/WMS/InlineXhtml.js');
        $doc->addScript($base_url . '/OpenLayers-2.13.1/InlineXhtml/lib/OpenLayers/Layer/InlineXhtml.js');
        $doc->addScript($base_url . '/OpenLayers-2.13.1/InlineXhtml/lib/OpenLayers/Layer/ScalableInlineXhtml.js');
        $doc->addScript($base_url . '/OpenLayers-2.13.1/InlineXhtml/lib/OpenLayers/Tile/InlineXhtml.js');

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

        //SVG with InlineXhtml addin
//        $output .= '
//            var xhtml_layer = new OpenLayers.Layer.ScalableInlineXhtml(
//                                    "3D viewer controller",
//                                    "' .$base_url . '/OpenLayers-2.13.1/InlineXhtml/feature.xml",
//                                    app.mapPanel.map.getExtent(),
//                                    new OpenLayers.Size(30, 30),
//                                    {isBaseLayer: false}
//                                );
//            ';
//        $output .= $appname . '.mapPanel.map.addLayer(xhtml_layer);';
//        


        $output .= '
            vector = new OpenLayers.Layer.Vector();
            ';
        
        $output .= $appname . '.mapPanel.map.addLayer(vector);
           
            var point1 = new OpenLayers.Geometry.Point(772931.23002, 5758321.424114);

            minion = new OpenLayers.Feature.Vector(point1, null, {
                externalGraphic: "data:image/png;base64,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",
                graphicWidth: 64,
                graphicHeight: 64,
                rotation:0,
                fillOpacity: 1
            });
            
            vector.addFeatures([minion]);
            
            var dragFeature = new OpenLayers.Control.DragFeature(vector, {
                onStart: function(){
                       minion.style.externalGraphic = "data:image/png;base64,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";
                    vector.redraw();
                    },
                    onComplete : function (){
                        minion.style.externalGraphic = "data:image/png;base64,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";
                        vector.redraw();
                        alert("Minion is at : " + minion.geometry.x + ", " +minion.geometry.y);
                    },
                onDrag: function() {
                    
                }   
            });

            ' . $appname . '.mapPanel.map.addControl(dragFeature);
//            dragFeature.activate();
            
    OpenLayers.Control.RotateGraphicFeature = OpenLayers.Class(OpenLayers.Control.ModifyFeature, {
        rotateHandleStyle: null,

        initialize: function(layer, options) {
            OpenLayers.Control.ModifyFeature.prototype.initialize.apply(this, arguments);
            this.mode = OpenLayers.Control.ModifyFeature.ROTATE | OpenLayers.Control.ModifyFeature.DRAG;
            this.geometryTypes = ["OpenLayers.Geometry.Point"]

            var init_style = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style.select);
            this.rotateHandleStyle = OpenLayers.Util.extend(init_style, {
                externalGraphic: "data:image/png;base64,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",
                graphicWidth: 64,
                graphicHeight: 64,
                fillOpacity: 1
            });
        },

        resetVertices: function() {
            OpenLayers.Control.ModifyFeature.prototype.resetVertices.apply(this, arguments);
            this.collectRadiusHandle();
        },    

       collectRadiusHandle: function() {
        var geometry = this.feature.geometry;
        var bounds = geometry.getBounds();
        console.log("bounds information: ", bounds);
        var center = bounds.getCenterLonLat();
        var originGeometry = new OpenLayers.Geometry.Point(
            center.lon, center.lat
        );

        var center_px = this.map.getPixelFromLonLat(center);

        var pixel_dis_x = 15; 
        var pixel_dis_y = 15; // you can change this two values to get best radius geometry position.

        var radius_px = center_px.add(pixel_dis_x, pixel_dis_y);
        var raius_lonlat = this.map.getLonLatFromPixel(radius_px);

        var radiusGeometry = new OpenLayers.Geometry.Point(
            raius_lonlat.lon, raius_lonlat.lat
        );
        var radius = new OpenLayers.Feature.Vector(radiusGeometry, null, this.rotateHandleStyle);
        var resize = (this.mode & OpenLayers.Control.ModifyFeature.RESIZE);
        var reshape = (this.mode & OpenLayers.Control.ModifyFeature.RESHAPE);
        var rotate = (this.mode & OpenLayers.Control.ModifyFeature.ROTATE);

        var scope_this = this; // in order to get the feature inside radiusGeometry.move function
        radiusGeometry.move = function(x, y) {
            OpenLayers.Geometry.Point.prototype.move.call(this, x, y);
            var dx1 = this.x - originGeometry.x;
            var dy1 = this.y - originGeometry.y;
            var dx0 = dx1 - x;
            var dy0 = dy1 - y;
            if(rotate) {
                var a0 = Math.atan2(dy0, dx0);
                var a1 = Math.atan2(dy1, dx1);
                var angle = a1 - a0;
                angle *= 180 / Math.PI;
                // if the feature has been set a externalGraphic, then change the rotation property of the feature style
                if (scope_this.feature.style.rotation !== undefined) {
                    var old_angle = scope_this.feature.style.rotation;
                    var new_angle = old_angle - angle;
                    scope_this.feature.style.rotation = new_angle;                   
                    // redraw the feature
                    scope_this.layer.drawFeature(scope_this.feature);
                }
            }

        };
        radius._sketch = true;
        this.radiusHandle = radius;
        this.radiusHandle.renderIntent = this.vertexRenderIntent;
        this.layer.addFeatures([this.radiusHandle], {silent: true});
    },


        CLASS_NAME: "OpenLayers.Control.RotateGraphicFeature"
    });
            
    var rotateControl = new OpenLayers.Control.RotateGraphicFeature(vector, {
//        dragStart: function(feature){
//                       minion.style.externalGraphic = "data:image/png;base64,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";
//                        minion.layer.redraw();
//////                        rotateControl.resetVertices();
//                    },
        dragComplete : function (vertex){
                        minion.style.externalGraphic = "data:image/png;base64,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";
                        minion.layer.redraw();
                        rotateControl.resetVertices();
                        alert("Minion is at : " + minion.geometry.x + ", " +minion.geometry.y + ", Rotate : "+minion.style.rotation);
                    }
});
' . $appname . '.mapPanel.map.addControl(rotateControl);
rotateControl.activate();
;
        ';
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
                    toggleGroup: "navigation",
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

        if (isset($item->physicalservices)) :
            foreach ($item->physicalservices as $service) :
                //Acces not allowed
                if (!in_array($service->access, $user->getAuthorisedViewLevels()))
                    continue;
                $config .= Easysdi_mapHelper::getServiceDescription($service);
            endforeach;
        endif;

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

                    $config .= Easysdi_mapHelper::getLayerDescription($layer, $group);
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
                xtype: "sdi_gxp_scaleoverlay"
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

    public static function getServiceDescription($service) {
        $url = '';
        //Initilization of the service url if the service is physic or virtual
        if (isset($service->resourceurl)) {
            $url = $service->resourceurl;
        } elseif (isset($service->url)) {
            $url = $service->url;
        }
        $config = '';
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

    public static function getExtraServiceDescription($service) {
        $url = '';
        $config = '';
        //Initilization of the service url if the service is physic or virtual
        if (isset($service->resourceurl)) {
            $url = $service->resourceurl;
        } elseif (isset($service->url)) {
            $url = $service->url;
        }
        switch ($service->serviceconnector_id) :
            case 2 :
                $config = '{id:"' . $service->alias . '",';
                $config .= '
                    ptype: "sdi_gxp_wmssource",
                    hidden : "true",
                    url: "' . $url . '"
                    }
                    ';
                break;
            case 11 :
                $config = '{id:"' . $service->alias . '",';
                $config .= ' 
                    ptype: "gxp_wmscsource",
                    hidden : "true",
                    url: "' . $url . '"
                    }
                    ';
                break;
            case 12 :
                $config = '{id:"' . $service->alias . '",';
                $config .= '
                    ptype: "sdi_gxp_bingsource",
                    hidden : "true",
                    }
                    ';
                break;
            case 13 :
                $config = '{id:"' . $service->alias . '",';
                $config .= '
                    ptype: "sdi_gxp_googlesource",
                    hidden : "true",
                    }
                    ';
                break;
            case 14 :
                $config = '{id:"' . $service->alias . '",';
                $config .= '
                    ptype: "sdi_gxp_osmsource",
                    hidden : "true",
                    }
                    ';
                break;
            default :
                $config = '{id:"' . $service->alias . '",';
                $config .= '
                    ptype: "sdi_gxp_olsource",
                    hidden : "true",
                    }
                    ';
        endswitch;
        return $config;
    }

    public static function getLayerDescription($layer, $group) {
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
                    if ($layer->serviceconnector == 'WMSC'):
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
                        $config .= "attribution: '" . $layer->attribution . "',";
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
        elseif (!empty($layer->metadatalink)):
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

    public static function getLayerDownloadToolUrl($diffusion_id) {
        return htmlentities(JURI::root() . 'index.php?option=com_easysdi_shop&task=download.direct&tmpl=component&id=' . $diffusion_id);
    }

    public static function getLayerOrderToolUrl($metadata_guid, $lang, $catalog) {
        return htmlentities(JURI::root() . 'index.php?option=com_easysdi_catalog&view=sheet&guid=' . $metadata_guid . '&lang=' . $lang . '&catalog=' . $catalog . '&type=shop&preview=map&tmpl=component');
    }

    public static function getLayerDetailSheetToolUrl($metadata_guid, $lang, $catalog, $preview) {
        return htmlentities(JURI::root() . 'index.php?option=com_easysdi_catalog&view=sheet&guid=' . $metadata_guid . '&lang=' . $lang . '&catalog=' . $catalog . '&preview=' . $preview . '&tmpl=component');
    }

}
