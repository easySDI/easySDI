<?php

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/OgcFilters.php';

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class cswrecords {

    /** @var DOMDocument */
    private $dom;

    /** @var OgcFilters */
    private $ogcFilters;
    private $ogcUri = 'http://www.opengis.net/ogc';
    private $ogcPrefix = 'ogc';

    function __construct() {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;

        $this->ogcFilters = new OgcFilters($this->dom);
    }

    public function getRecords($catalog_id) {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        $db = JFactory::getDbo();
        $lang = JFactory::getLanguage()->getTag();
        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $catalogurl = $params->get('catalogurl');
        $srpn = $params->get('searchresultpaginationnumber');
        $startposition = JFactory::getApplication()->input->getInt('start', 1);

        //Or getvar startposition of JPagination
        //Criteria from the catalog : 
        //Resourcetype
        $q = $db->getQuery(true)
                ->select('rt.alias')
                ->from('#__sdi_resourcetype rt')
                ->innerJoin('#__sdi_catalog_resourcetype crt ON crt.resourcetype_id = rt.id')
                ->where('crt.catalog_id = ' . (int) $catalog_id);
        $db->setQuery($q);
        $resourcetypes = $db->loadColumn();

        //Csw filter + Contextual search result pagination number
        $q = $db->getQuery(true)
                ->select('cswfilter, contextualsearchresultpaginationnumber')
                ->from('#__sdi_catalog')
                ->where('id = ' . (int) $catalog_id);
        $db->setQuery($q);
        $result = $db->loadAssoc();
        
        $cswfilter = $result['cswfilter'];
        $csrpn = $result['contextualsearchresultpaginationnumber'];

        //Csw sorting field
        $q = $db->getQuery(true)
                ->select('css.ogcsearchsorting')
                ->from('#__sdi_catalog_searchsort css')
                ->innerJoin('#__sdi_language l ON l.id = css.language_id ')
                ->where('css.catalog_id = ' . (int) $catalog_id)
                ->where('l.code = "' . $lang . '"');
        $db->setQuery($q);
        $ogcsearchsorting = $db->loadResult();
        
        
        // choose limit between global and contextual configuration
        $limit = empty($csrpn) || $csrpn == 0 ? $srpn : $csrpn;
        

        //Get posted criterias
        //...
        //Build request with posted criterias
        //...


        $getrecords = $this->dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:GetRecords');
        $getrecords->setAttribute('service', 'CSW');
        $getrecords->setAttribute('version', '2.0.2');
        //if resulttype exists
        $getrecords->setAttribute('resultType', 'results');
        $getrecords->setAttribute('outputSchema', 'csw:IsoRecord');
        $getrecords->setAttribute('content', 'CORE');
        //if maxrecords != 0
        $getrecords->setAttribute('maxRecords', $limit);
        //if startposition != 0
        $getrecords->setAttribute('startPosition', $startposition);

        $query = $this->dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:Query');
        $query->setAttribute('typeNames', 'gmd:MD_Metadata');

        //if elementsetname exists
        $elementsetname = $this->dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:ElementSetName', 'full');

        $constraint = $this->dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:Constraint');
        $constraint->setAttribute('version', '1.1.0');

        $filter = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Filter');


        $or = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Or');
        $and = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
        $orresourcetype = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Or');

        foreach ($data as $name => $value) {
            switch ($name) {
                case 'resourcetype':
                    foreach ($value as $resourcetype) {
                        $orresourcetype->appendChild($this->getResouceType(strtolower($resourcetype)));
                    }
                    break;
                case 'versions':
                    if ($value) {
                        $and->appendChild($this->getVersions());
                    }
                    break;
                case 'resourcename':
                    if (!empty($value)) {
                        $and->appendChild($this->getResouceName($value));
                    }
                    break;
                case 'metadata_created':
                    if(!empty($value['from'])||!empty($value['to'])){
                        $and->appendChild($this->getMetadataCreated($value['from'], $value['to']));
                    }
                    break;
                case 'metadata_published':
                    if(!empty($value['from'])||!empty($value['to'])){
                        $and->appendChild($this->getMetadataPublished($value['from'], $value['to']));
                    }
                    break;
                case 'organism':
                    if(!empty($value)){
                        $and->appendChild($this->getOrganism($value));
                    }
                    break;
                case 'isdownloadable':
                    $and->appendChild($this->getIsDownloadable());
                    break;
               case 'isfree':
                    $and->appendChild($this->getIsFree());
                    break;
                case 'isorderable':
                    $and->appendChild($this->getIsOrderable());
                    break;
                    
            }
        }

        $and->appendChild($orresourcetype);
        $and->appendChild($this->ogcFilters->getIsEqualTo('harvested', 'false'));
        $or->appendChild($and);
        $or->appendChild($this->ogcFilters->getIsEqualTo('harvested', 'true'));

        $filter->appendChild($or);

        //Ogc search sorting
        if (!empty($ogcsearchsorting)):
            $sortby = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:SortBy');
            $sortbyproperty = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:SortProperty');
            $propertyname = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyName', strtolower($ogcsearchsorting));
            $sortorder = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:SortOrder', 'ASC');
            $sortbyproperty->appendChild($propertyname);
            $sortbyproperty->appendChild($sortorder);
            $sortby->appendChild($sortbyproperty);
        endif;

        $constraint->appendChild($filter);
        $query->appendChild($elementsetname);
        $query->appendChild($constraint);
        if (!empty($ogcsearchsorting)):
            $query->appendChild($sortby);
        endif;
        $getrecords->appendChild($query);
        $this->dom->appendChild($getrecords);

        $body = $this->dom->saveXML();

        $results = cswrecords::CURLRequest('POST', $catalogurl, $body);
        if (!$results) {
            return false;
        }
        $doc = new DOMDocument();
        $doc->loadXML($results);

        $total = 0;
        if ($results) {
            // Contrôler si le XML ne contient pas une erreur
            if ($doc->getElementsByTagName('ExceptionReport')->length > 0) {
                $msg = $doc->getElementsByTagName("ExceptionReport")->item(0)->nodeValue;
                JFactory::getApplication()->enqueueMessage($msg, 'error');
                return false;
            } else {
                $SearchResults = $doc->getElementsByTagName('SearchResults ');

                //Put the numberOfRecordsMatched in session variable for pagination
                JFactory::getApplication('com_easysdi_catalog')->setUserState('global.list.total', '238');

//                foreach ($SearchResults->item(0)->attributes as $a => $b) {
//                    if ($a == 'numberOfRecordsMatched') {
//                        $total = $b;
//                    }
//                }
//                // Si le nombre de résultats retournés a changé, adapter la page affichée
//                if ($limitstart >= $total) {
//                    $limitstart = ( $limit != 0 ? ((floor($total / $limit) * $limit) - 1) : 0 );
//                    $mainframe->setUserState('limitstart', $limitstart);
//                }
//
//                if ($limitstart < 0) {
//                    $limitstart = 0;
//                    $mainframe->setUserState('limitstart', $limitstart);
//                }
//
//                $pageNav = new JPagination($total, $limitstart, $limit);
//                $cswResults = DOMDocument::loadXML($xmlResponse);
            }
        }

        return $doc;
    }

    protected static function CURLRequest($type, $url, $xmlBody = "") {
        // Get COOKIE as key=value
        $cookiesList = array();
        foreach ($_COOKIE as $key => $val) {
            $cookiesList[] = $key . "=" . $val;
        }
        $cookies = implode(";", $cookiesList);

        $ch = curl_init($url);
        // Configuration
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        // cURL obeys the RFCs as it should. Meaning that for a HTTP/1.1 backend if the POST size is above 1024 bytes
        // cURL sends a 'Expect: 100-continue' header. The server acknowledges and sends back the '100' status code.
        // cuRL then sends the request body. This is proper behaviour. Nginx supports this header.
        // This allows to work around servers that do not support that header.
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"','Expect:'));
        // We're emptying the 'Expect' header, saying to the server: please accept the body right now. 
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, "");

        // Specific POST
        if ($type == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$xmlBody");
        }
        // Specific GET
        else if ($type == "GET") {
            curl_setopt($ch, CURLOPT_POST, 0);
        }

        //User authentication
        $params = JComponentHelper::getParams('com_easysdi_contact');
        $serviceaccount_id = $params->get('serviceaccount');
        $juser = JFactory::getUser($serviceaccount_id);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $juser->username . ":" . $juser->password);
        
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    private function getFullText($literal) {
        $operatorNode = $this->ogcFilters->getOperator($operator);

        $filter1 = $this->ogcFilters->getIsLike('mainsearch', $literal);
        $filter2 = $this->ogcFilters->getIsLike('keyword', $literal);
        $filter3 = $this->ogcFilters->getIsLike('abstract', $literal);

        $operatorNode->appendChild($filter1);
        $operatorNode->appendChild($filter2);
        $operatorNode->appendChild($filter3);

        return $operatorNode;
    }

    private function getResouceType($literal) {
        return $this->ogcFilters->getIsEqualTo('resourcetype', $literal);
    }

    private function getVersions() {
        return $this->ogcFilters->getIsEqualTo('lastversion', 'true');
    }

    private function getResouceName($literal) {
        return $this->ogcFilters->getIsLike('resourcename', $literal);
    }

    private function getMetadataCreated($from = '', $to = '') {
        if (!empty($from) && empty($to)) {
            return $this->ogcFilters->getIsGreatherOrEqual('created', $from);
        } elseif (empty($from) && !empty($to)) {
            return $this->ogcFilters->getIsLessOrEqual('created', $to);
        } else {
            return $this->ogcFilters->getIsBetween('created', $from, $to);
        }
    }

    private function getMetadataPublished($from = '', $to = '') {
       if (!empty($from) && empty($to)) {
            return $this->ogcFilters->getIsGreatherOrEqual('published', $from);
        } elseif (empty($from) && !empty($to)) {
            return $this->ogcFilters->getIsLessOrEqual('published', $to);
        } else {
            return $this->ogcFilters->getIsBetween('published', $from, $to);
        }
    }

    private function getOrganism($literal) {
        return $this->ogcFilters->getIsEqualTo('organism', $literal);
    }

    private function getDefinedBoundary($literal) {
        /**
         * @todo NOT IMPLEMENTED
         */
    }

    private function getIsDownloadable() {
        return $this->ogcFilters->getIsEqualTo('isdownloadable', 'true');
    }

    private function getIsFree() {
        return $this->ogcFilters->getIsEqualTo('isfree', 'true');
    }

    private function getIsOrderable() {
        return $this->ogcFilters->getIsEqualTo('isorderable', 'true');
    }

    private function getIsViewable() {
        return $this->ogcFilters->getIsEqualTo('isviewable', 'true');
    }

}
