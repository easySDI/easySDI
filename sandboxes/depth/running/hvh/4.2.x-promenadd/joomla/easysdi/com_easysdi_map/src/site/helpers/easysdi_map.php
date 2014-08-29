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
        
        //Polyline feature controller
//        $output .= '
//            var vector = new OpenLayers.Layer.Vector("3D viewer controller");
//            
//            ' . $appname . '.mapPanel.map.addLayer(vector);
//            var modifycontrol = new OpenLayers.Control.ModifyFeature(vector);
//            modifycontrol.mode = OpenLayers.Control.ModifyFeature.DRAG | OpenLayers.Control.ModifyFeature.ROTATE;
//           
//            
//            ' .$appname.'.mapPanel.map.addControl(modifycontrol);
//                
//            modifycontrol.activate();
//       
//            var points = [
//                new OpenLayers.Geometry.Point(8.49302, 47.31141),
//                new OpenLayers.Geometry.Point(8.03802, 46.86641),
//                new OpenLayers.Geometry.Point(8.49302, 46.25141)                
//            ];
//            var line = new OpenLayers.Geometry.LineString(points);
//                
//            var geometrycollection = new OpenLayers.Geometry.Collection(
//                [
//                    new OpenLayers.Geometry.Point(8.03802, 46.86641),
//                    line
//                ]
//            );
//               
//            var feature = new OpenLayers.Feature.Vector(geometrycollection);
//            vector.addFeatures(feature);
//            
//        ';
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
        

        // Feature point and drag/rotate control
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
                externalGraphic: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAADS0lEQVQ4T21TbUhTURg+16lzTmc6aUb+GOIQPwayaFPBjxQh/01oFfUzEQYZKDI/fsSK+YFLI0oKoWImRkhBjqVIc4thjI2tSNMy8iObiOJw0+nmdLfnXDD60YWXc8+9533e533e5zDkP4/BYHiTnJxce3x8zAoEAu5EfHw82djYYBISEvwZGRnXtFrtR3xmmZP83t5e+draWmRwcHDRaDS+LC0tvZqTk0NisRiJi4sjR0dHZHp6mszPzz9JT083Mgyz3d7eHuAAurq6ivl8vj0cDvuj0ejFrKwseWJioqmyslJIGaAqCQQCHADW10h+FAqFPuv1+h2mqamJjwSzUqmspWAej8cKsEYcHKuvr1dIJBISDAbJwcEBQUtkamoqyLJsY0dHxyt6ngFKlVQqtdXU1BCv10uWl5fdqKDd399vlMvlNxA8u93OMVCr1cTtdtM2hnk8XktnZ+c2MzQ0NFZQUHAJVYnFYlnCjyGEHb2z6NtSXV2dabPZ1g8PD7+UlJRczM/PJ06nM7S1taVubm5+z4yOji4qFAoZPpDx8fEPaWlpj9HzTwi3gHBAeblQKBwAmABsmioqKojVaqVMGyD6MDMyMrKI/mWbm5tkYmLCnpKScgcAc62trdv9/f0NEPYKkp8DTJ6Xl9dWVlbGAaysrOigxVPGZDI5i4qKVFQks9nsFYvFt3Q63QwVqLu7W4xpKAEQiEQiF9CqQaVSEYfDEV1dXdWjzRcMTHMzNzf3IVjQHywoP2hra2uhJqEgdEoQWSMSie7hjGRnZ4cK+RWgg4h3TF9fnwyzdlVVVZ1CFeJyuajit/E+A2FjwDgD593Pzs6WFBYWksnJySOfz/cMOW/hTi8zMDCAd4EOLejr6uoILEyZcDNPTU0lGCehiQDhevf7/S5QH0O4MC0P58Senh4p0O6i38sYE18mk3Hugx8IhKKCkdnZWQL3eZBoAbtZsPsE8GUOQKPR8MrLy/P29vauw3VKAJzHNNIoAJ7Y0tLSt93dXTd0WMBU1pOSkhbAeA4mDP+9TNiAffJZGKYYVc9BIDFWFpUOMcIt7H9jGwKzH2DwHef3OCv/e5upHqB4GhcqE2smEkRYGQBEkBgEo1+g7aOVT/L+AJdAxiHOPcVHAAAAAElFTkSuQmCC",
                graphicWidth: 16,
                graphicHeight: 16,
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

        var pixel_dis_x = 0; 
        var pixel_dis_y = 0; // you can change this two values to get best radius geometry position.

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
