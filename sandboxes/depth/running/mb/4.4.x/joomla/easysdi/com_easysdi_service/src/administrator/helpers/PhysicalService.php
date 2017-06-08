<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

abstract class PhysicalService {

    public $id;
    public $name;
    public $url;
    protected $rawXml;
    protected $compliance;
    protected $connector;
    protected $xmlCapabilities;
    protected $user;
    protected $password;

    abstract public function populate();

    abstract public function sortLists();

    abstract public function loadData($data);

    abstract public function setLayerAsConfigured($layerList);

    public function __construct($id, $url, $user, $password, $connector) {
        $this->id = $id;
        $this->url = $url;
        $this->connector = $connector;
        $this->user = $user;
        $this->password = $password;
    }

    public function getRawXml() {
        return $this->rawXml;
    }

    /**
     * Request the Capabilities of the server and store them.
     * 
     * @param string $rawXML : optional xmlString that contains the capabilities of the server (no request is made if that param is set)
     * 
     * @return bool : true on success, false on error
     */
    public function getCapabilities($rawXML = null) {
        $this->rawXml = $rawXML;
        if (!isset($rawXML)) {

            //try to set compliance
            try {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('sv.value as value');
                $query->from('#__sdi_physicalservice_servicecompliance ssc');
                $query->join('INNER', '#__sdi_sys_servicecompliance sc ON sc.id = ssc.servicecompliance_id ');
                $query->join('INNER', '#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id ');
                $query->where('ssc.service_id = ' . (int) $this->id);
                $query->order('sv.ordering DESC');
                $db->setQuery($query);
                $this->compliance = $db->loadResult();
            } catch (Exception $e) {
                $this->setError($e->getMessage());
            }

            $completeUrl = $this->url . "?REQUEST=GetCapabilities&SERVICE=" . $this->connector;
            if (isset($this->compliance)) {
                $completeUrl .= "&version=" . $this->compliance;
            }

            $session = curl_init($completeUrl);
            $httpHeader = array();
            // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
            // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
            // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
            // This allows to work around servers that do not support that header.
            // We're emptying the 'Expect' header, saying to the server: please accept the body right now. 
            $httpHeader [] = 'Expect:';
            if (!empty($this->user) && !empty($this->password)) {
                $httpHeader[] = 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->password);                
            }
            curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
            curl_setopt($session, CURLOPT_HEADER, false);
            curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($session, CURLOPT_SSL_VERIFYPEER, false);
            $this->rawXml = curl_exec($session);
            $http_status = curl_getinfo($session, CURLINFO_HTTP_CODE);
            curl_close($session);

            //HTTP status error
            if ($http_status != '200') {
//				echo $completeUrl;
                return false;
            }
        }
        $this->rawXml = stripcslashes($this->rawXml);
        $xmlCapa = simplexml_load_string($this->rawXml);

        $namespaces = $xmlCapa->getNamespaces(true);
        foreach ($namespaces as $key => $value) {
            if ($key == '') {
                $xmlCapa->registerXPathNamespace("dflt", $value);
            } else {
                $xmlCapa->registerXPathNamespace($key, $value);
            }
        }

        $this->xmlCapabilities = $xmlCapa;
        return true;
    }

}
