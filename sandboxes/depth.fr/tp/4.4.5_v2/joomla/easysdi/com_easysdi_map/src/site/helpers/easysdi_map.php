<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_easysdi_map/models/map.php';

abstract class Easysdi_mapHelper {

    public static function getMapScript($mapid, $cleared = false, $appname = "app", $renderto = "sdimapcontainer", $options=[]) {
        $model = JModelLegacy::getInstance('map', 'Easysdi_mapModel');
        $item = $model->getData($mapid);
        $base_url = Juri::base(true) . '/components/com_easysdi_core/libraries';
        $doc = JFactory::getDocument();
        
        if (!isset($options['map_options'])) $options['map_options']=null;
        if (!isset($options['sharelink'])) $options['sharelink']=true;

        if ($item->type=='geoext'){
        //Clear the map from all the tools
        //The goal is to have a clean map to use as a simple and quick data preview
        if ($cleared) {
            $item->tools = array();
            $item->urlwfslocator = "";
        }

        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);
        $user = JFactory::getUser();

        //Loading css files
        $doc = JFactory::getDocument();
        $base_url = Juri::root(true) . '/components/com_easysdi_core/libraries';
        $doc->addStyleSheet($base_url . '/ext/resources/css/ext-all.css');
        $doc->addStyleSheet($base_url . '/ext/resources/css/xtheme-gray.css');
        $doc->addStyleSheet($base_url . '/OpenLayers-2.13.1/theme/default/style.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/popup.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/layerlegend.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/gxtheme-gray.css');
        $doc->addStyleSheet($base_url . '/ux/geoext/resources/css/printpreview.css');
        $doc->addStyleSheet($base_url . '/gxp/theme/all.css');
        $doc->addStyleSheet(Juri::root(true) . '/components/com_easysdi_map/views/map/tmpl/easysdi.css?v=' . sdiFactory::getSdiFullVersion());
        $doc->addStyleSheet($base_url . '/easysdi/js/sdi/widgets/IndoorLevelSlider.css?v=' . sdiFactory::getSdiFullVersion());

        //Loadind js files
        if (JDEBUG) {
            $doc->addScript(Juri::root(true) . '/media/jui/js/jquery.js');
            $doc->addScript(Juri::root(true) . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript(Juri::root(true) . '/media/jui/js/bootstrap.js');
            $doc->addScript(JURI::root(true) . '/media/system/js/mootools-core-uncompressed.js');
            $doc->addScript(JURI::root(true) . '/media/system/js/core-uncompressed.js');
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base-debug.js');
            $doc->addScript($base_url . '/ext/ext-all-debug.js');
            $doc->addScript($base_url . '/proj4js-1.1.0/lib/proj4js.js');
                $doc->addScript($base_url . '/ux/ext/RowExpander.js');
                $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');
            $doc->addScript($base_url . '/OpenLayers-2.13.1/OpenLayers.debug.js');
            $doc->addScript($base_url . '/easysdi/js/OpenLayers/override-openlayers.js?v=' . sdiFactory::getSdiFullVersion());
            $doc->addScript($base_url . '/geoext/lib/overrides/override-ext-ajax.js?v=' . sdiFactory::getSdiFullVersion());
            $doc->addScript($base_url . '/geoext/lib/GeoExt.js');
            $doc->addScript($base_url . '/geoext/lib/GeoExt/data/PrintProvider.js');
            $doc->addScript($base_url . '/gxp/script/gxp.js?v=' . sdiFactory::getSdiFullVersion());
            $doc->addScript($base_url . '/easysdi/js/gxp/plugins/Popup.js');
            $doc->addScript($base_url . '/easysdi/js/sdi.js?v=' . sdiFactory::getSdiFullVersion());
        }else{
            $doc->addScript(JURI::root(true) . '/media/jui/js/jquery.min.js');
            $doc->addScript(JURI::root(true) . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript(JURI::root(true) . '/media/jui/js/bootstrap.min.js');
            $doc->addScript(JURI::root(true) . '/media/system/js/mootools-core.js');
            $doc->addScript(JURI::root(true) . '/media/system/js/core.js');
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base.js');
            $doc->addScript($base_url . '/ext/ext-all.js');
            $doc->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');
            $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');
            $doc->addScript($base_url . '/OpenLayers-2.13.1/OpenLayers.js');
            $doc->addScript($base_url . '/easysdi/js/OpenLayers/override-openlayers.js?v=' . sdiFactory::getSdiFullVersion());
            $doc->addScript($base_url . '/geoext/lib/overrides/override-ext-ajax.js?v=' . sdiFactory::getSdiFullVersion());
            $doc->addScript($base_url . '/geoext/lib/geoextcon.min.js');
            $doc->addScript($base_url . '/geoext/lib/GeoExt/data/PrintProvider.js');
            $doc->addScript($base_url . '/gxp/script/gxp.min.js?v=' . sdiFactory::getSdiFullVersion());
            $doc->addScript($base_url . '/easysdi/js/sdi.min.js?v=' . sdiFactory::getSdiFullVersion());
            //$doc->addScript($base_url . '/easysdi/js/sdi.js');
        }

            foreach (glob(JPATH_SITE . '/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/*.js') as $file) {
                $doc->addScript(str_replace(JPATH_SITE, JURI::root(true), $file));
        }

        $doc->addScript(JURI::root(true) . '/components/com_easysdi_map/helpers/map.js?v=' . sdiFactory::getSdiFullVersion());

        $params = JComponentHelper::getParams('com_easysdi_map');
        $proxyhost = JURI::root() . "index.php?option=com_easysdi_core&task=proxy.run&url=";

        //Default group
        foreach ($item->groups as $group) :
            if ($group->isdefault) {
                //Acces not allowed
                if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                    break;
                $defaultgroup = $group->alias;
                break;
            }
        endforeach;

        //Groups are added in the order saved in the database
        $groups = array();
        foreach ($item->groups as $group) :
            //Acces not allowed
            if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                continue;

            if ($group->isbackground) {
                $backgroundname = $group->name;
                if ($group->isdefaultopen) :
                    $backgroundexpanded = "true";
                else :
                    $backgroundexpanded = "false";
                endif;
            }
            else {
                $g = new stdClass();
                $g->alias = $group->alias;
                $g->title = $group->name;
                $g->expanded = ($group->isdefaultopen) ? true : false;
                array_push($groups, $g);
            }
        endforeach;

        //Services
        $services = array();
        if (isset($item->physicalservices)) :
            foreach ($item->physicalservices as $service) :
                //Acces not allowed
                if (!in_array($service->access, $user->getAuthorisedViewLevels()))
                    continue;
                if($service->serviceconnector_id == 3) //WMTS
                    continue;
                array_push($services, Easysdi_mapHelper::getServiceDescriptionObject($service));
            endforeach;
        endif;

        if (isset($item->virtualservices)) :
            foreach ($item->virtualservices as $service) {
                if($service->serviceconnector_id == 3) //WMTS
                    continue;
                array_push($services, Easysdi_mapHelper::getServiceDescriptionObject($service));
            }
        endif;

        //Layers
        $layers = array();
        foreach ($item->groups as $group) {
            //Acces not allowed
            if (!in_array($group->access, $user->getAuthorisedViewLevels()))
                continue;

            if (!empty($group->layers)) {
                foreach ($group->layers as $layer) {
                    //Acces not allowed
                    if (!in_array($layer->access, $user->getAuthorisedViewLevels()))
                        continue;
                    array_push($layers, Easysdi_mapHelper::getLayerDescriptionObject($layer, $group));
                }
            }
        }

        //Mouseposition
        $mouseposition = 'false';
        foreach ($item->tools as $tool) {
            if ($tool->alias == 'mouseposition') {
                $mouseposition = 'true';
                break;
            }
        }

        //Build object with params needed by the javascript map object
        $data = new stdClass();
            $data->bottomInUnits = isset($item->bottomInUnits) ? $item->bottomInUnits : null;
            $data->bottomOutUnits = isset($item->bottomOutUnits) ? $item->bottomOutUnits : null;
            $data->topInUnits = isset($item->topInUnits) ? $item->topInUnits : null;
            $data->topOutUnits = isset($item->topOutUnits) ? $item->topOutUnits : null;
            $data->title = isset($item->title) ? $item->title : null;
            $data->abstract = isset($item->abstract) ? $item->abstract : null;
        $data->tools = $item->tools;
        $data->rootnodetext = $item->rootnodetext;
        $data->srs = $item->srs;
        $data->maxextent = $item->maxextent;
        $data->maxresolution = $item->maxresolution;
        $data->units = $item->unit;
            $data->centercoordinates = isset($item->centercoordinates) ? $item->centercoordinates : null;
            $data->restrictedextent = isset($item->restrictedextent) ? $item->restrictedextent : null;
            $data->zoom = isset($item->zoom) ? $item->zoom : null;
            $data->urlwfslocator = isset($item->urlwfslocator) ? $item->urlwfslocator : null;
            $data->fieldname = isset($item->fieldname) ? $item->fieldname : null;
            $data->featuretype = isset($item->featuretype) ? $item->featuretype : null;
            $data->featureprefix = isset($item->featureprefix) ? $item->featureprefix : null;
            $data->fieldname = isset($item->fieldname) ? $item->fieldname : null;
            $data->geometryname = isset($item->geometryname) ? $item->geometryname : null;
        if(isset($item->level)){
            $data->level = $item->level;
        }

        $c = ($cleared) ? 'true' : 'false';
   

        $layer_default_name="";
        foreach ($item->groups as  $group) {
            if ($group->name == $backgroundname) {
                foreach ($group->layers as  $layer) {
                   // var_dump($item->default_backgroud_layer , $layer->id,$layer->name);
                    if ($item->default_backgroud_layer == $layer->id) {
                        $layer_default_name=$layer->name;
                    }
                }


            }
        }

        foreach ($layers as  $layer) {
            if ($layer->title ==  $layer_default_name) {
                $layer->visibility=true;
            } 
        }
        $popupheight = JComponentHelper::getParams('com_easysdi_map')->get('popupheight');
        $popupwidth = JComponentHelper::getParams('com_easysdi_map')->get('popupwidth');
        if ($popupheight ==0 || $popupheight==null) {
            $popupheight=400;
        }
        if ($popupwidth ==0 || $popupwidth==null) {
            $popupwidth=200;
        }
        $output = '<script>
        var popup_size = {"popupheight":'.$popupheight.',"popupwidth":'.$popupwidth.'};
            var msg = "' . JText::_('COM_EASYSDI_MAP_MAP_LOAD_MESSAGE') . '";
            var layermsg = "' . JText::_('COM_EASYSDI_MAP_LAYER_LOAD_MESSAGE') . '";
            var cleared = "' . $c . '";
            var data = ' . json_encode((array) $data) . ';
            var renderto = "' . $renderto . '";
            var proxyhost = "' . $proxyhost . '" ;
            var params = ' . json_encode($params) . ';
            var mwidth = "' . $params->get('iframewidth') . '";
            var mheight = "' . $params->get('iframeheight') . '";
            var langtag  = "' . $lang->getTag() . '";
            var appname = "' . $appname . '";
            var defaultgroup = "' . $defaultgroup . '";
            var groups = ' . json_encode($groups) . ';
            var backgroundname = "' . $backgroundname . '";
            var backgroundexpanded = ' . $backgroundexpanded . ';
            var loadingMask;
            var width;
            var heigth;
            var services = ' . json_encode($services) . ';
            var layers = ' . json_encode($layers) . ';
                var mouseposition = "' . $mouseposition . '";


        </script>
        <div id="' . $renderto . '" class="cls-' . $renderto . '"></div>';
        }else{
            //Loadind js files
            JHtml::_('jquery.framework');
            if (JDEBUG) {
                //$doc->addStyleSheet("http://unpkg.com/leaflet@1.0.3/dist/leaflet.css");
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet/leaflet.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/Leaflet.ZoomBox/L.Control.ZoomBox.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-measure/leaflet-measure.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-control-geocoder/Control.Geocoder.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/sidebar-v2/css/leaflet-sidebar.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-EasyPrint/L.Control.EasyPrint.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-EasyLayer/easyLayer.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-EasyAddLayer/easyAddLayer.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-EasyLegend/easyLegend.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-EasyGetFeature/easyGetFeature.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-Easy/easyLeaflet.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/font-awesome-4.3.0/css/font-awesome.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet-graphicscale/Leaflet.GraphicScale.min.css');

                $doc->addScript($base_url . '/leaflet/libs/i18next-1.9.0/i18next-1.9.0.min.js');
                $doc->addScript('https://maps.google.com/maps/api/js?v=3&sensor=false');
                $doc->addScript($base_url . '/leaflet/libs/leaflet/leaflet.js');
                //$doc->addScript("http://unpkg.com/leaflet@1.0.3/dist/leaflet.js");
                $doc->addScript($base_url . '/leaflet/libs/shramov/tile/Google.js');
                $doc->addScript($base_url . '/leaflet/libs/shramov/tile/Bing.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet.TileLayer.WMTS-master/leaflet-tilelayer-wmts-src.js');
                $doc->addScript($base_url . '/leaflet/libs/Leaflet.ZoomBox/L.Control.ZoomBox.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-measure/leaflet-measure.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-control-geocoder/Control.Geocoder.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-EasyPrint/L.Control.EasyPrint.js');
                $doc->addScript($base_url . '/leaflet/libs/sidebar-v2/js/leaflet-sidebar.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-EasyLayer/easyLayer.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-EasyAddLayer/easyAddLayer.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-EasyLegend/easyLegend.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-EasyGetFeature/easyGetFeature.js');
                $doc->addScript($base_url . '/leaflet/libs/wms-capabilities/wms-capabilities.min.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-graphicscale/Leaflet.GraphicScale.min.js');
                $doc->addScript($base_url . '/proj4js-2.3.14/proj4.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet.nontiledlayer/NonTiledLayer.js');
                $doc->addScript($base_url . '/leaflet/libs/easysdi_leaflet/easysdi_leaflet.js?v=' . sdiFactory::getSdiFullVersion());
            }else{
                $doc->addStyleSheet($base_url . '/leaflet/libs/leaflet/leaflet.css');
                $doc->addStyleSheet($base_url . '/leaflet/libs/easySDI_leaflet.pack/main.css?v=' . sdiFactory::getSdiFullVersion());
                $doc->addScript($base_url . '/proj4js-2.3.14/proj4.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet/leaflet.js');
                $doc->addScript($base_url . '/leaflet/libs/leaflet-proj4Leaflet/proj4leaflet.js');
                $doc->addScript($base_url . '/leaflet/libs/easySDI_leaflet.pack/easySDI_leaflet.pack.min.js?v=' . sdiFactory::getSdiFullVersion());
                $doc->addScript('https://maps.google.com/maps/api/js?v=3&sensor=false');
            }
            
            $output = "<div id='easySDIMap' class='easySDImapPrintBlock'><div id='map' class='easySDI-leaflet sidebar-map ' data-url='".JURI::base(true)."/index.php?option=com_easysdi_map&view=map&id=".$mapid."&format=json' ";
            if ($options['sharelink'])
                $output .= " data-sharelink=true";
            if ($options['map_options'])
                $output .= " data-mapoptions='".json_encode($options['map_options'])."'";

            $output .= "></div></div>";
        }

        return $output;
    }

    /**
     *
     * @param type $service
     * @return \stdClass
     */
    public static function getServiceDescriptionObject($service) {
        $url = '';
        //Initilization of the service url if the service is physic or virtual
        if (isset($service->resourceurl)) {
            $url = $service->resourceurl;
        } elseif (isset($service->url)) {
            $url = $service->url;
        }
        $obj = new stdClass();
        if ($service->server_id) {
            $obj->server = $service->server_id;
        }
        switch ($service->serviceconnector_id) :
            case 2 :
                $obj->alias = $service->alias;
                $obj->ptype = "sdi_gxp_wmssource";
                $obj->url = $url;
                break;
            case 3 :
                $obj->alias = $service->alias;
                $obj->ptype = "sdi_gxp_olsource";
                $obj->url = $url;
                break;
            case 11 :
                $obj->alias = $service->alias;
                $obj->ptype = "gxp_wmscsource";
                $obj->url = $url;
                break;
            case 12 :
                $obj->alias = $service->alias;
                $obj->ptype = "sdi_gxp_bingsource";
                break;
            case 13 :
                $obj->alias = $service->alias;
                $obj->ptype = "sdi_gxp_googlesource";
                break;
            case 14 :
                $obj->alias = $service->alias;
                $obj->ptype = "sdi_gxp_osmsource";
                break;
        endswitch;
        return $obj;
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
            case 3 :
                $config = '{id:"' . $service->alias . '",';
                $config .= '
                    ptype: "sdi_gxp_olsource",
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

    public static function getLayerDescriptionObject($layer, $group) {
        $obj = new stdClass();
        if ($layer->isindoor == 1):
            $obj->isindoor = 1;
            $obj->levelfield = $layer->levelfield;
        endif;

        if ($layer->servertype) {
            $obj->servertype = $layer->servertype;
        }
        if ($layer->asOL) {
            $obj->source = "ol";

            switch ($layer->serviceconnector) {
                case 'WMTS' :
                    $obj->type = "OpenLayers.Layer.WMTS";
                    $obj->name = $layer->name;
                    $obj->url = $layer->serviceurl;
                    $obj->layer = $layer->layername;

                    if ($layer->isdefaultvisible == 1)
                        $obj->visibility = true;
                    else
                        $obj->visibility = false;

                    if ($layer->istiled == 1)
                        $obj->singleTile = true;
                    else
                        $obj->singleTile = false;

                    $obj->transitionEffect = "resize";
                    $obj->opacity = $layer->opacity;
                    $obj->style = $layer->asOLstyle;
                    $obj->matrixSet = $layer->asOLmatrixset;
                    $obj->asOLoptions = $layer->asOLoptions;
//                    $options = preg_replace("/\s\s+/", " ", $layer->asOLoptions);
//                    $params = explode(',', $options);
//                    foreach($params as $param){
//                        $KVP = explode(':',$param);
//                        $obj->asOLoptions[$KVP[0]] = $KVP[1];
//                    }
                    break;
                case 'WMS' :
                case 'WMSC' :
                    $obj->type = "OpenLayers.Layer.WMS";
                    $obj->name = $layer->name;
                    $obj->url = $layer->serviceurl;
                    $obj->layers = $layer->layername;
                    $obj->version = $layer->version;

                    if ($layer->serviceconnector == 'WMSC'):
                        $obj->tiled = true;
                    endif;

                    if ($layer->isdefaultvisible == 1)
                        $obj->visibility = true;
                    else
                        $obj->visibility = false;

                    if ($layer->istiled == 1)
                        $obj->singleTile = true;
                    else
                        $obj->singleTile = false;

                    $obj->opacity = $layer->opacity;
                    $obj->transitionEffect = "resize";
                    $obj->style = $layer->asOLstyle;

                    $obj->asOLoptions = $layer->asOLoptions;

                    break;
            }
            if ($group->isbackground)
                $obj->group = "background";
            else
                $obj->group = $group->alias;
        }
        else {
            switch ($layer->serviceconnector) {
                case 'WMTS':
                    break;
                default :
                    $obj->source = $layer->servicealias;
                    if ($layer->istiled == 1)
                        $obj->tiled = true;
                    else
                        $obj->tiled = false;

                    if (!empty($layer->version)) {
                        $obj->version = $layer->version;
                    }

                    if (!empty($layer->attribution)) {
                        $obj->attribution = $layer->attribution;
                    }
                    $obj->name = $layer->layername;
                    $obj->title = $layer->name;

                    if ($group->isbackground)
                        $obj->group = "background";
                    else
                        $obj->group = $group->alias;


                    if ($group->alias == "background")
                        $obj->fixed = true;

                    if ($layer->isdefaultvisible == 1)
                        $obj->visibility = true;
                    else
                        $obj->visibility = false;

                    $obj->opacity = $layer->opacity;
                    break;
            }
        }

        if (!empty($layer->metadata_guid)):
            $obj->href = Easysdi_mapHelper::getLayerDetailSheetToolUrl($layer->metadata_guid, JFactory::getLanguage()->getTag(), '', 'map');
        elseif (!empty($layer->metadatalink)):
            $obj->href = $layer->metadatalink;
        endif;
        if (!empty($layer->hasdownload)):
            $obj->download = Easysdi_mapHelper::getLayerDownloadToolUrl($layer->diffusion_id);
        endif;
        if (!empty($layer->hasextraction)):
            $obj->order = Easysdi_mapHelper::getLayerOrderToolUrl($layer->metadata_guid, JFactory::getLanguage()->getTag(), '');
        endif;

        return $obj;
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

    private static function getServiceConnector($service) { //!TODO a refaire utiliser le modele ??? refaire aussi getServiceDescriptionObject ?
        $keys=array(
            1=>"CSW",
            2=>"WMS",
            3=>"WMTS",
            4=>"WFS",
            5=>"WCS",
            6=>"WCPS",
            7=>"SOS",
            8=>"SPS",
            9=>"WPS",
            10=>"OLS",
            11=>"WMSC",
            12=>"Bing",
            13=>"Google",
            14=>"OSM"
            );

        $i= (integer) $service->serviceconnector_id;
        if (isset($keys[$i]))
            return $keys[$i];

        return 'serviceconnector'.$service->serviceconnector_id;
    }

    /**
    * return options needed to use easySDImap_leaflet.js
    * @return [type] [description]
    */
    public static function getCleanMap($ori){
        $user = JFactory::getUser();

        $proxyhost = JURI::base() . "index.php?option=com_easysdi_core&task=proxy.run&url=";
        $lang=JFactory::getLanguage()->getTag();

        $res=compact('proxyhost','lang');

        $default_group;

        if (in_array($ori->access, $user->getAuthorisedViewLevels())){
            foreach (array('id','name','title','srs','maxresolution','numzoomlevel','maxextent','restrictedextent','centercoordinates','zoom','unit','tools','default_backgroud_layer') as $key) {
                if(property_exists($ori, $key)) {
                  $res[$key]=$ori->$key;
              }
            }

            $res['groups']=array();
            $res['services']=array();

            foreach ($ori->groups as $group) {
                if (in_array($group->access, $user->getAuthorisedViewLevels())){

                    if ($group->isdefault)
                        $default_group = $group->alias;

                    $resG=array();
                    foreach (array('id','alias','ordering','name','isbackground') as $key) {
                      if(property_exists($group, $key)) {
                        $resG[$key]=$group->$key;
                        }
                    }

                    $resG['layers']=array();
                    foreach ($group->layers as $layer) {
                      if (in_array($layer->access, $user->getAuthorisedViewLevels())){
                        $resL=array();
                        foreach (array('id','alias','ordering','name','servicetype','layername','istiled','isdefaultvisible','opacity','asOL','asOLstyle','asOLmatrixset','asOLoptions','metadatalink','attribution','serviceurl','serviceconnector','servicealias','version') as $key) {

                              if(property_exists($layer, $key)) {
                                $resL[$key]=$layer->$key;
                              }
                          }

                        if($resL['hasdownload'])
                            $resL['downloadurl']=self::getLayerDownloadToolUrl($resL['diffusion_id']);

                        if($resL['hasextraction'])
                            $resL['extractionurl']=self::getLayerOrderToolUrl($resL['metadata_guid'],JFactory::getLanguage()->getTag(), '');

                         $resG['layers'][]=$resL;
                      }
                    }// end each layers
                $res['groups'][]=$resG;
              }
            } // end each groups

                            $res['default_group']=$default_group;

                if(property_exists($ori,'virtualservices'))
                foreach ($ori->virtualservices as $service) {
                 if (in_array($service->access, $user->getAuthorisedViewLevels())){
                    $desc=self::getServiceDescriptionObject($service);
                    $resL=array(
                        'servicetype'=>'virtual',
                        'servicealias'=>$desc->alias,
                        'serviceurl'=>$desc->url,
                        'serviceconnector'=>self::getServiceConnector($service)
                        );
                    foreach (array('guid','ordering','name') as $key) {
                      if(property_exists($service, $key)) {
                        $resL[$key]=$service->$key;
                    }
                }

                if ($resL['servicealias']!==null)
                    $res['services'][]=$resL;
            }
        }



        if(property_exists($ori,'physicalservices'))
        foreach ($ori->physicalservices as $service) {
            if (in_array($service->access, $user->getAuthorisedViewLevels())){
                $desc=self::getServiceDescriptionObject($service);
                $resL=array(
                    'servicetype'=>'physical',
                    'servicealias'=>isset($desc->alias)?$desc->alias:null,
                    'serviceurl'=>isset($desc->url)?$desc->url:null,
                    'serviceconnector'=>self::getServiceConnector($service)
                    );
                foreach (array('guid','ordering','name') as $key) {
                  if(property_exists($service, $key)) {
                    $resL[$key]=$service->$key;
                }
            }
            if ($resL['servicealias']!==null)
                $res['services'][]=$resL;
            }
        }

            $config = JFactory::getConfig();
            $res['sitename']=$config->get( 'sitename' );
        }
        return $res;
    }

}
