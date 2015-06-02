<?php

/**
 * @version     4.3.2
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
            $item->urlwfslocator = "";
        }

        //Load admin language file
        $lang = JFactory::getLanguage();
        $lang->load('com_easysdi_map', JPATH_ADMINISTRATOR);
        $user = JFactory::getUser();

        //Loading css files
        $doc = JFactory::getDocument();
        $base_url = Juri::base(true) . '/administrator/components/com_easysdi_core/libraries';
        $doc->addStyleSheet($base_url . '/ext/resources/css/ext-all.css');
        $doc->addStyleSheet($base_url . '/ext/resources/css/xtheme-gray.css');
        $doc->addStyleSheet($base_url . '/OpenLayers-2.13.1/theme/default/style.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/popup.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/layerlegend.css');
        $doc->addStyleSheet($base_url . '/geoext/resources/css/gxtheme-gray.css');
        $doc->addStyleSheet($base_url . '/ux/geoext/resources/css/printpreview.css');
        $doc->addStyleSheet($base_url . '/gxp/theme/all.css');
        $doc->addStyleSheet(Juri::base(true) . '/components/com_easysdi_map/views/map/tmpl/easysdi.css');
        $doc->addStyleSheet($base_url . '/easysdi/js/sdi/widgets/IndoorLevelSlider.css');

        //Loadind js files
        if (JDEBUG) {
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/bootstrap.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/mootools-core-uncompressed.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/core-uncompressed.js');            
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base-debug.js');
            $doc->addScript($base_url . '/ext/ext-all-debug.js');
            $doc->addScript($base_url . '/proj4js-1.1.0/lib/proj4js.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');            
            $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');    
            $doc->addScript($base_url . '/OpenLayers-2.13.1/OpenLayers.debug.js');            
            $doc->addScript($base_url . '/geoext/lib/overrides/override-ext-ajax.js');
            $doc->addScript($base_url . '/geoext/lib/GeoExt.js');
            $doc->addScript($base_url . '/geoext/lib/GeoExt/data/PrintProvider.js');
            $doc->addScript($base_url . '/gxp/script/gxp.js');
            $doc->addScript($base_url . '/easysdi/js/sdi.js');
            
        }else{
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery.min.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/jquery-noconflict.js');
            $doc->addScript(Juri::base(true) . '/media/jui/js/bootstrap.min.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/mootools-core.js');
            $doc->addScript(JURI::base(true) . '/media/system/js/core.js');
            $doc->addScript($base_url . '/ext/adapter/ext/ext-base.js');
            $doc->addScript($base_url . '/ext/ext-all.js');
            $doc->addScript($base_url . '/proj4js-1.1.0/lib/proj4js-compressed.js');
            $doc->addScript($base_url . '/ux/ext/RowExpander.js');
            $doc->addScript($base_url . '/ux/geoext/PrintPreview.js');    
            $doc->addScript($base_url . '/OpenLayers-2.13.1/OpenLayers.js');
            $doc->addScript($base_url . '/geoext/lib/overrides/override-ext-ajax.js');
            $doc->addScript($base_url . '/geoext/lib/geoextcon.min.js');
            $doc->addScript($base_url . '/geoext/lib/GeoExt/data/PrintProvider.js');
            $doc->addScript($base_url . '/gxp/script/gxp.min.js');
            $doc->addScript($base_url . '/easysdi/js/sdi.min.js');
        }

        foreach (glob(JPATH_BASE . '/administrator/components/com_easysdi_core/libraries/easysdi/js/gxp/locale/*.js') as $file) {
            $doc->addScript(str_replace(JPATH_BASE, '', $file));
        }

        $doc->addScript(Juri::base(true) . '/components/com_easysdi_map/helpers/map.js');

        $app = JFactory::getApplication();
        $params = $app->getParams('com_easysdi_map');
        $proxyhost = JURI::base() . "index.php?option=com_easysdi_core&task=proxy.run&url=";

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
        $data->bottomInUnits = $item->bottomInUnits;
        $data->bottomOutUnits = $item->bottomOutUnits;
        $data->topInUnits = $item->topInUnits;
        $data->topOutUnits = $item->topOutUnits;
        $data->title = $item->title;
        $data->abstract = $item->abstract;
        $data->tools = $item->tools;
        $data->rootnodetext = $item->rootnodetext;
        $data->srs = $item->srs;
        $data->maxextent = $item->maxextent;
        $data->maxresolution = $item->maxresolution;
        $data->units = $item->unit;
        $data->centercoordinates = $item->centercoordinates;
        $data->restrictedextent = $item->restrictedextent;
        $data->zoom = $item->zoom;
        $data->urlwfslocator = $item->urlwfslocator;
        $data->fieldname = $item->fieldname;
        $data->featuretype = $item->featuretype;
        $data->featureprefix = $item->featureprefix;
        $data->fieldname = $item->fieldname;
        $data->geometryname = $item->geometryname;
        if(isset($item->level)){
            $data->level = $item->level;
        }

        $c = ($cleared) ? 'true' : 'false';

        $output = '<script>
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
    
    /**
    * return options needed to use easySDImap_leaflet.js
    * @return [type] [description]
    */
    public static function getCleanMap($ori){
        $user = JFactory::getUser();
        $res=array();
        
        if (in_array($ori->access, $user->getAuthorisedViewLevels())){
            foreach (array('id','name','title','srs','maxresolution','numzoomlevel','maxextent','restrictedextent','centercoordinates','zoom','unit','tools') as $key) {
                if(property_exists($ori, $key)) {
                  $res[$key]=$ori->$key;
              }
            }

            $res['groups']=array();

            foreach ($ori->groups as $group) {
                if (in_array($group->access, $user->getAuthorisedViewLevels())){
                    $resG=array();
                    foreach (array('id','alias','ordering','name','isbackground','isdefault') as $key) {
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
                         $resG['layers'][]=$resL;

                      }
                    }// end each layers
                $res['groups'][]=$resG;
              }
            } // end each groups

            $config = JFactory::getConfig();
            $res['sitename']=$config->get( 'sitename' );
        }
        return $res;
    }

}
