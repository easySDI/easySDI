<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Easysdi_service helper.
 */
class Easysdi_serviceHelper {

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
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_SERVICE_TITLE_PHYSICALSERVICES'), 'index.php?option=com_easysdi_service&view=physicalservices', $vName == 'physicalservices'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_SERVICE_TITLE_CATEGORIES'), "index.php?option=com_categories&extension=com_easysdi_service", $vName == 'categories'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_SERVICE_TITLE_VIRTUALSERVICES'), 'index.php?option=com_easysdi_service&view=virtualservices', $vName == 'virtualservices'
        );
        JHtmlSidebar::addEntry(
                Easysdi_coreHelper::getMenuSpacer() . JText::_('COM_EASYSDI_SERVICE_TITLE_POLICIES'), 'index.php?option=com_easysdi_service&view=policies', $vName == 'policies'
        );
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_map');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_monitor');
        Easysdi_coreHelper::addComponentSubmeu('com_easysdi_dashboard');

        if ($vName == 'categories.catid') {
            JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_CATEGORIES'));
        }
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActionsVirtualService($id = null) {
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($id)) {
            $assetName = 'com_easysdi_service';
        } else {
            $assetName = 'com_easysdi_service.virtualservice.' . (int) $id;
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
     * Gets a list of the actions that can be performed on physicalservice object.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActionsPhysicalService($categoryId = null, $id = null) {
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($id) && empty($categoryId)) {
            $assetName = 'com_easysdi_service';
        } elseif (empty($id)) {
            $assetName = 'com_easysdi_service.category.' . (int) $categoryId;
        } else {
            $assetName = 'com_easysdi_service.physicalservice.' . (int) $id;
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
     * Gets a list of the actions that can be performed.
     *
     * @return	JObject
     * @since	1.6
     */
    public static function getActionsPolicy($id = null) {
        $user = JFactory::getUser();
        $result = new JObject;

        if (empty($id)) {
            $assetName = 'com_easysdi_service';
        } else {
            $assetName = 'com_easysdi_service.policy.' . (int) $id;
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
     * Execute all the needed requests on the remote server to achieve the negotiation version.
     * Result of those requests are a list of supported versions by the remote server.
     * @param string $url : url of the remote server
     * @param string $user : user for authentication, if needed
     * @param string $password : password for authentication, if needed
     * @param string $service : service type (WFS, WMS, CSW or WMTS)
     */
    public static function negotiation($params) {
        $service = $params['service'];
        $url = $params['resurl'];
        $user = $params['resuser'];
        $password = $params['respassword'];
        $supported_versions = array();
        $httpHeader = array();

        if (isset($params['serurl'])) {
            //Authentication needed
            $s_url = $params['serurl'];
            $s_user = $params['seruser'];
            $s_password = $params['serpassword'];

            //Perform a geonetwork login
            $ch = curl_init();
            if (!$ch) {
                echo 'Error';
                die();
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $s_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
            curl_setopt($ch, CURLOPT_POSTFIELDS, "username=" . $s_user . "&password=" . $s_password);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
            ob_start();
            curl_exec($ch);
            ob_end_clean();
            curl_close($ch);
            unset($ch);
        }


        $pos1 = stripos($url, "?");
        $separator = "&";
        if ($pos1 === false) {
            //"?" Not found then use ? instead of &
            $separator = "?";
        }

        //Get the implemented version of the requested ServiceConnector
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('c.id as id, sv.value as value');
        $query->from('#__sdi_sys_serviceconnector sc');
        $query->innerJoin('#__sdi_sys_servicecompliance c ON c.serviceconnector_id = sc.id');
        $query->innerJoin('#__sdi_sys_serviceversion sv ON c.serviceversion_id = sv.id');
        $query->where('c.implemented = 1');
        $query->where('sc.value = ' . $query->quote($service));
        $db->setQuery($query);
        $implemented_versions = $db->loadObjectList();

        $completeurl = "";
        foreach ($implemented_versions as $version) {
            $service = $service == 'WMSC' ? 'WMS' : $service;
            $completeurl = $url . $separator . "REQUEST=GetCapabilities&SERVICE=" . $service . "&VERSION=" . $version->value;
            $response = Easysdi_serviceHelper::requestWithCurl($completeurl, $user, $password);

            //Avoid PHP Warning to be returned 
            libxml_use_internal_errors(true);
            $xmlCapa = simplexml_load_string($response);
            
            if ($xmlCapa === false) {
                if ($service == "WMTS") {
                    //Try a REST request
                    $completeurl = $url . "/" . $version->value . "/WMTSCapabilities.xml";
                    $response = Easysdi_serviceHelper::requestWithCurl($completeurl, $user, $password);

                    $xmlCapa = simplexml_load_string($response);
                    if ($xmlCapa === false) {
                        Easysdi_serviceHelper::sendBackError();
                    }
                } else {
                    Easysdi_serviceHelper::sendBackError();
                }
            }
            if ($xmlCapa->getName() == "ServiceExceptionReport") {
                continue;
            }
            foreach ($xmlCapa->attributes() as $key => $value) {
                if ($key == 'version') {
                    if ($value[0] == $version->value)
                        $supported_versions[$version->id] = $version->value;
                }
            }
            libxml_clear_errors();
        }
        $encoded = json_encode($supported_versions);
        echo $encoded;
        die();
    }

    /**
     * Perform a request with CURL
     * @param type $completeurl
     * @param type $user
     * @param type $password
     * @return type
     */
    private static function requestWithCurl($completeurl, $user, $password) {
        $session = curl_init($completeurl);
        $httpHeader[] = 'Expect:';
        if (!empty($user) && !empty($password)) {
            $httpHeader[] = 'Authorization: Basic ' . base64_encode($user . ':' . $password);
        }
        curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($session);
        curl_close($session);
        return $response;
    }

    /**
     * Return a JSON formated error to the caller of the negociation
     */
    private static function sendBackError() {
        $supported_versions['ERROR'] = JText::_('COM_EASYSDI_SERVICE_FORM_DESC_SERVICE_NEGOTIATION_ERROR');
        echo json_encode($supported_versions);
        die();
    }

    /**
     * Execute a GetCapabilities to return queriable layers.
     * @param string $service : prefixed id of the service
     */
    public static function getLayers($params) {
        $service = $params['service'];
        $user = $params['user'];
        $password = $params['password'];

        $physical_id = -1;

        $db = JFactory::getDbo();
        $pos = strstr($service, 'physical_');
        if ($pos) {
            $id = substr($service, strrpos($service, '_') + 1);
            $query = $db->getQuery(true);
            $query->select('s.resourceurl as url, sc.value as connector');
            $query->from('#__sdi_physicalservice s');
            $query->innerJoin('#__sdi_sys_serviceconnector sc  ON sc.id = s.serviceconnector_id');
            $query->where('s.id=' . (int) $id);

            $db->setQuery($query);
            $resource = $db->loadObject();

            $query = $db->getQuery(true);
            $query->select('sv.value as value, sc.id as id');
            $query->from('#__sdi_physicalservice_servicecompliance ssc');
            $query->innerJoin('#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id');
            $query->innerJoin('#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
            $query->where('ssc.service_id =' . (int) $id);
            $query->order('sv.ordering DESC');

            $db->setQuery($query, 0, 1);
            $compliance = $db->loadObject();
            $physical_id = $id;
        } else {
            $id = substr($service, strrpos($service, '_') + 1);

            $query = $db->getQuery(true);
            $query->select('ps.id as physicalservice_id, ps.resourceurl as url, sc.value as connector');
            $query->from('#__sdi_virtualservice vs');
            $query->innerJoin('#__sdi_physicalservice ps ON ps.id = vs.physicalservice_id');
            $query->innerJoin('#__sdi_sys_serviceconnector sc  ON sc.id = ps.serviceconnector_id');
            $query->where('vs.id=' . (int) $id);

            $db->setQuery($query);
            $resource = $db->loadObject();

            $query = $db->getQuery(true);
            $query->select('sv.value as value, sc.id as id');
            $query->from('#__sdi_physicalservice_servicecompliance pssc');
            $query->innerJoin('#__sdi_sys_servicecompliance sc ON sc.id = pssc.servicecompliance_id');
            $query->innerJoin('#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
            $query->where('pssc.service_id =' . $resource->physicalservice_id);
            $query->order('sv.ordering DESC');

            $db->setQuery($query, 0, 1);
            $compliance = $db->loadObject();
            $physical_id = $resource->physicalservice_id;
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
        $httpHeader[] = 'Expect:';
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
        if ($xmlCapa->getName() == "ServiceExceptionReport") {
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
            if ($key == '') {
                $xmlCapa->registerXPathNamespace("dflt", $value);
            } else {
                $xmlCapa->registerXPathNamespace($key, $value);
            }
        }
        $version = $xmlCapa->xpath('@version');
        $version = (string) $version[0];

        switch ($resource->connector) {
            case "WMS":
            case "WMSC":
                switch ($version) {
                    case "1.3.0":
                        $wmsLayerList = $xmlCapa->xpath('//dflt:Layer');
                        break;
                    default:
                        $wmsLayerList = $xmlCapa->xpath('/Capability//Layer[Name]');
                        break;
                }

                //flushing the wmslayer table
                $tab_layer = JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
                $tab_layer->wipeByPhysicalId($physical_id);
                unset($tab_layer);

                //inserting each wmslayer
                foreach ($wmsLayerList as $wmsLayer) {
                    $tab_layer = JTable::getInstance('wmslayer', 'Easysdi_serviceTable');
                    $tab_layer->save(Array(
                        'name' => (String) $wmsLayer->Name,
                        'description' => (String) $wmsLayer->Title,
                        'physicalservice_id' => $physical_id
                    ));
                    unset($tab_layer);
                }
                break;
            case "WMTS":
                $time_stack = 0;
                $wmtsLayerList = $xmlCapa->xpath('/dflt:Capabilities/dflt:Contents/dflt:Layer');
                //flushing the wmtslayer table
                $tab_layer = JTable::getInstance('wmtslayer', 'Easysdi_serviceTable');
                $tab_layer->wipeByPhysicalId($physical_id);
                unset($tab_layer);

                //inserting each wmtslayer
                foreach ($wmtsLayerList as $wmtsLayer) {

                    $tab_layer = JTable::getInstance('wmtslayer', 'Easysdi_serviceTable');
                    $tab_layer->save(Array(
                        'name' => (String) $wmtsLayer->children('ows', true)->Identifier,
                        'description' => (String) $wmtsLayer->children('ows', true)->Title,
                        'physicalservice_id' => $physical_id
                    ));

                    $wmtslayer_id = $tab_layer->id;

                    foreach ($wmtsLayer->TileMatrixSetLink as $tileMatrixSet) {
                        $tileMatrixSetIdentifier = (String) $tileMatrixSet->TileMatrixSet;

                        //we save the tilematrixset
                        $supported_CRS = $xmlCapa->xpath("/dflt:Capabilities/dflt:Contents/dflt:TileMatrixSet[ows:Identifier = '" . $tileMatrixSetIdentifier . "']");
                        $supported_CRS = (String) $supported_CRS[0]->children('ows', true)->SupportedCRS;

                        $tab_tileMatrixSet = JTable::getInstance('tileMatrixSet', 'Easysdi_serviceTable');
                        $tab_tileMatrixSet->save(Array(
                            'identifier' => $tileMatrixSetIdentifier,
                            'supported_crs' => $supported_CRS,
                            'wmtslayer_id' => $wmtslayer_id,
                        ));

                        //we probe if the tilematrixset has limits
                        $hasLimits = isset($tileMatrixSet->TileMatrixSetLimits);

                        //we get the list of authorized tilematrix for this tilematrixset
                        if ($hasLimits) {
                            $authorized_tiles = Array();
                            foreach ($tileMatrixSet->TileMatrixSetLimits->TileMatrixLimits as $limits) {
                                $tileMatrixIdentifier = (String) $limits->TileMatrix;
                                $authorized_tiles[] = $tileMatrixIdentifier;
                            }
                        }

                        //we get the list of all the tilematrix for this tilematrixset
                        $tileMatrixList = $xmlCapa->xpath("/dflt:Capabilities/dflt:Contents/dflt:TileMatrixSet[ows:Identifier = '" . $tileMatrixSetIdentifier . "']/dflt:TileMatrix");

                        //we sanitize the output of the xpath
                        $tileMatrixArray = Array();
                        foreach ($tileMatrixList as $tileMatrix) {
                            $identifier = (String) $tileMatrix->children('ows', true)->Identifier;
                            //if there are limits on the tilematrixset we filter the list of tilematrix with authorized tilematrixes and we save
                            if (!$hasLimits || in_array($identifier, $authorized_tiles)) {
                                $tileMatrixArray[] = Array(
                                    'identifier' => $identifier,
                                    'scaledenominator' => (String) $tileMatrix->ScaleDenominator,
                                    'topleftcorner' => (String) $tileMatrix->TopLeftCorner,
                                    'tilewidth' => (String) $tileMatrix->TileWidth,
                                    'tileheight' => (String) $tileMatrix->TileHeight,
                                    'matrixwidth' => (String) $tileMatrix->MatrixWidth,
                                    'matrixheight' => (String) $tileMatrix->MatrixHeight,
                                    'tilematrixset_id' => $tab_tileMatrixSet->id,
                                );
                            }
                        }
                        $tab_tileMatrix = JTable::getInstance('tileMatrix', 'Easysdi_serviceTable');
                        $tab_tileMatrix->saveBatch($tileMatrixArray);
                        unset($tab_tileMatrixSet, $tab_tileMatrix, $tileMatrixArray);
                    }
                    unset($tab_layer);
                }

                break;
            case "WFS":
                $featureTypeList = $xmlCapa->xpath('//dflt:FeatureType');

                //flushing the featureClass table
                $tab_layer = JTable::getInstance('featureclass', 'Easysdi_serviceTable');
                $tab_layer->wipeByPhysicalId($physical_id);
                unset($tab_layer);

                //inserting each featureClass
                foreach ($featureTypeList as $featureType) {
                    $tab_layer = JTable::getInstance('featureclass', 'Easysdi_serviceTable');
                    $tab_layer->save(Array(
                        'name' => (String) $featureType->Name,
                        'description' => (String) $featureType->Title,
                        'physicalservice_id' => $physical_id
                    ));
                    unset($tab_layer);
                }
                break;
        }
    }

}
