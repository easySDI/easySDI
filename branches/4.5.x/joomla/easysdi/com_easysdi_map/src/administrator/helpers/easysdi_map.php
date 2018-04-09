<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_map helper.
 */
class Easysdi_mapHelper {

    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($vName = '') {
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_core');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_user');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_catalog');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_shop');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_processing');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_service');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_map');
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_MAP_TITLE_MAPS'), 'index.php?option=com_easysdi_map&view=maps', $vName == 'maps'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_MAP_TITLE_LAYERS'), 'index.php?option=com_easysdi_map&view=layers', $vName == 'layers'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_MAP_TITLE_GROUPS'), 'index.php?option=com_easysdi_map&view=groups', $vName == 'groups'
        );
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_monitor');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_dashboard');
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActions($dataType = null, $Id = null) {
        $user = JFactory::getUser();
        $result = new JObject;

        if (!empty($dataType) && !empty($Id)) {
            $assetName = 'com_easysdi_map.' . $dataType . '.' . (int) $Id;
        } else {
            $assetName = 'com_easysdi_map';
        }

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    /**
     * Execute a GetCapabilities to return queriable layers.
     * @param string $service : prefixed id of the service
     */
    public static function getLayers($params) {
        $service = $params['service'];
        if (empty($service)) {
            echo json_encode(array());
            die();
        }

        $db = JFactory::getDbo();
        $pos = strstr($service, 'physical_');
        if ($pos) {
            $id = substr($service, strrpos($service, '_') + 1);
            $query = $db->getQuery(true);
            $query->select('s.resourceurl as url, sc.value as connector, s.resourceusername as username, s.resourcepassword as password');
            $query->from('#__sdi_physicalservice s');
            $query->innerJoin('#__sdi_sys_serviceconnector sc  ON sc.id = s.serviceconnector_id');
            $query->where('s.id=' . substr($service, strrpos($service, '_') + 1));

            $db->setQuery($query);
            $resource = $db->loadObject();
            if ($resource->username) {
                $user = $resource->username;
                $password = $resource->password;
            }

            $query = $db->getQuery(true);
            $query->select('sv.value as value, sc.id as id');
            $query->from('#__sdi_physicalservice_servicecompliance ssc');
            $query->innerJoin('#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
            $query->innerJoin('#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
            $query->where('ssc.service_id =' . $id);

            $db->setQuery($query, 0, 1);
            $compliance = $db->loadObject();
        } else {
            $id = substr($service, strrpos($service, '_') + 1);

            $query = $db->getQuery(true);
            $query->select('s.url as url,  sc.value as connector, s.alias as alias');
            $query->from('#__sdi_virtualservice s');
            $query->innerJoin('#__sdi_sys_serviceconnector sc  ON sc.id = s.serviceconnector_id');
            $query->where('s.id=' . substr($service, strrpos($service, '_') + 1));

            $db->setQuery($query);
            $resource = $db->loadObject();
            $Juser = JFactory::getUser();
            $user = $Juser->username;
            $password = $Juser->password;
            //Url is deducted from the component Service config
            $params = JComponentHelper::getParams('com_easysdi_service');
            $resource->url = $params->get('proxyurl') . $resource->alias;

            $query = $db->getQuery(true);
            $query->select('sv.value as value, sc.id as id FROM #__sdi_virtualservice_servicecompliance ssc');
            $query->innerJoin('#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
            $query->innerJoin('#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
            $query->where('ssc.service_id =' . (int) $id);
            $query->order('sv.value DESC');

            $db->setQuery($query, 0, 1);
            $compliance = $db->loadObject();
        }

        $url = $resource->url;
        $connector = ($resource->connector == "WMSC") ? "WMS" : $resource->connector;
        $pos1 = stripos($url, "?");
        $separator = "&";
        if ($pos1 === false) {
            $separator = "?";
        }

        $completeurl = $url . $separator . "REQUEST=GetCapabilities&SERVICE=" . $connector;

        if ($compliance && $compliance->value) {
            $completeurl .= "&version=" . $compliance->value;
        }
        $session = curl_init($completeurl);
        $httpHeader = array();
        // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
        // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
        // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
        // This allows to work around servers that do not support that header.
        // We're emptying the 'Expect' header, saying to the server: please accept the body right now. 
        $httpHeader [] = 'Expect:';
        if (!empty($user) && !empty($password)) {
            $httpHeader[] = 'Authorization: Basic ' . base64_encode($user . ':' . $password);
        }
        curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        $http_status = curl_getinfo($session, CURLINFO_HTTP_CODE);
        curl_close($session);

        //HTTP status error
        if ($http_status != '200') {
            $result['ERROR'] = JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_HTTP_ERROR') . $http_status;
            echo json_encode($result);
            die();
        }

        $xmlCapa = simplexml_load_string($response);
        $result = array();

        //Response empty
        if ($xmlCapa === false) {
            $result['ERROR'] = JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_ERROR');
            echo json_encode($result);
            die();
        }

        //OGC exception returned
        if ($xmlCapa->getName() == "ServiceExceptionReport" || $xmlCapa->getName() == "ExceptionReport") {
            foreach ($xmlCapa->children() as $exception) {
                $ogccode = $exception['code'];
                break;
            }
            $result['ERROR'] = JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_OGC_ERROR') . $ogccode;
            echo json_encode($result);
            die();
        }

        //Parse capabilities to get layers
        $namespaces = $xmlCapa->getNamespaces(true);
        foreach ($namespaces as $key => $value) {
            if ($key == '')
                $xmlCapa->registerXPathNamespace("dflt", $value);
            else
                $xmlCapa->registerXPathNamespace($key, $value);
        }
        $version = $xmlCapa->xpath('@version');
        $version = (string) $version[0];

        switch ($resource->connector) {
            case "WMS" :
            case "WMSC" :
                switch ($version) {
                    case "1.3.0":
                        $layers_array = $xmlCapa->xpath('//dflt:Layer/dflt:Name');
                        if (!empty($layers_array)) {
                            foreach ($layers_array as $layer):
                                $r = $xmlCapa->xpath('//dflt:Layer[dflt:Name="' . (string) $layer . '"]/dflt:Title[1]');
                                $layers [(string) $layer] = (string) $r[0];
                            endforeach;
                        }
                        break;
                    default:
                        $layers_array = $xmlCapa->xpath('//Layer/Name');
                        if (!empty($layers_array)) {
                            foreach ($layers_array as $layer):
                                $r = $xmlCapa->xpath('//Layer[Name="' . (string) $layer . '"]/Title[1]');
                                $layers [(string) $layer] = (string) $r[0];
                            endforeach;
                        }
                        break;
                }
                break;
            case "WMTS" :
                $layers_array = $xmlCapa->xpath('//dflt:Layer/ows:Identifier');
                if (!empty($layers_array)) {
                    foreach ($layers_array as $layer):
                        $r = $xmlCapa->xpath('//dflt:Layer[ows:Identifier="' . (string) $layer . '"]/ows:Title[1]');
                        $layers [(string) $layer] = (string) $r[0];
                    endforeach;
                }
                break;
        }


        if (!$layers) {
            $result['ERROR'] = JText::_('COM_EASYSDI_MAP_GET_CAPABILITIES_LAYERS_ERROR');
            echo json_encode($result);
            die();
        }

//        foreach ($layers as $layer) {
//            $result[] = (string) $layer;
//        }

        $encoded = json_encode($layers);
        echo $encoded;
        die();
    }

    public static function getMaps($params) {
        $group = $params['group'];
       /* if (empty($group)) {
            echo json_encode(array());
            die();
        }*/
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_layer_layergroup');
        $query->where('group_id=' .$group);

        $db->setQuery($query);
        $resources = $db->loadObjectList();
        $result=[];
        foreach ($resources as $resource) {
            
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from('#__sdi_maplayer');
            $query->where('id=' . $resource->layer_id);
    
            $db->setQuery($query);
            $tmp = $db->loadAssoc();
            $tmp['layer_layergroup']=$resource->id;
            $result[]=$tmp;
        }
        $encoded = json_encode($result);
        echo $encoded;
        die();

    }

}
