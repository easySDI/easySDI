<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
abstract class cswrecords {

    public static function getRecords($catalog_id) {

        $db = JFactory::getDbo();
        $lang = JFactory::getLanguage()->getTag();
        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $catalogurl = $params->get('catalogurl');
        $limit = $params->get('searchresultpaginationnumber');
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

        //Csw filter
        $q = $db->getQuery(true)
                ->select('cswfilter')
                ->from('#__sdi_catalog')
                ->where('id = ' . (int) $catalog_id);
        $db->setQuery($q);
        $cswfilter = $db->loadResult();

        //Csw sorting field
        $q = $db->getQuery(true)
                ->select('css.ogcsearchsorting')
                ->from('#__sdi_catalog_searchsort css')
                ->innerJoin('#__sdi_language l ON l.id = css.language_id ')
                ->where('css.catalog_id = ' . (int) $catalog_id)
                ->where('l.code = "' . $lang . '"');
        $db->setQuery($q);
        $ogcsearchsorting = $db->loadResult();

        //Get posted criterias
        //...
        //Build request with posted criterias
        //...

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;
        $getrecords = $dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:GetRecords');
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

        $query = $dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:Query');
        $query->setAttribute('typeNames', 'gmd:MD_Metadata');

        //if elementsetname exists
        $elementsetname = $dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:ElementSetName', 'full');

        $constraint = $dom->createElementNS('http://www.opengis.net/cat/csw/2.0.2', 'csw:Constraint');
        $constraint->setAttribute('version', '1.1.0');

        $filter = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Filter');
        $globalAnd = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
        
        //Add filters here
        //allowed resource types
        /**
          <ogc:Or>
          <ogc:And>
          <ogc:Or>
          <ogc:PropertyIsEqualTo>
          <ogc:PropertyName>resourcetype</ogc:PropertyName>
          <ogc:Literal>Layer</ogc:Literal>
          </ogc:PropertyIsEqualTo>
          <ogc:PropertyIsEqualTo>
          <ogc:PropertyName>resourcetype</ogc:PropertyName>
          <ogc:Literal>Geoproduct</ogc:Literal>
          </ogc:PropertyIsEqualTo>
          </ogc:Or>
          <ogc:Or>
          <ogc:PropertyIsEqualTo>
          <ogc:PropertyName>harvested</ogc:PropertyName>
          <ogc:Literal>false</ogc:Literal>
          </ogc:PropertyIsEqualTo>
          <ogc:PropertyIsNull>
          <ogc:PropertyName>harvested</ogc:PropertyName>
          </ogc:PropertyIsNull>
          </ogc:Or>
          </ogc:And>
          <ogc:PropertyIsEqualTo>
          <ogc:PropertyName>harvested</ogc:PropertyName>
          <ogc:Literal>true</ogc:Literal>
          </ogc:PropertyIsEqualTo>
          </ogc:Or>
         */
        
        $or  = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Or');
        $and = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
        $orresourcetype = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Or');

        foreach ($resourcetypes as $resourcetype):
            $proprt = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyIsEqualTo');
            $propnamert = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyName', 'resourcetype');
            $literalrt = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Literal', strtolower($resourcetype));

            $proprt->appendChild($propnamert);
            $proprt->appendChild($literalrt);
            $orresourcetype->appendChild($proprt);
        endforeach;
        
        $proph = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyIsEqualTo');
        $propnameh = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyName', 'harvested');
        $literalh = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Literal', 'false');
        $proph->appendChild($propnameh);
        $proph->appendChild($literalh);
        
        $and->appendChild($orresourcetype);
        $and->appendChild($proph);
        $or->appendChild($and);
        
        $prop = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyIsEqualTo');
        $propname = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyName', 'harvested');
        $literal = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Literal', 'true');
        $prop->appendChild($propname);
        $prop->appendChild($literal);
        $or->appendChild($prop);
        
        $globalAnd->appendChild($or);
                

        //Csw filter defines on the catalog
        if (!empty($cswfilter)):
            $globalAnd->appendChild($cswfilter);
        endif;

        $filter->appendChild($globalAnd);
        
        //Ogc search sorting
        if (!empty($ogcsearchsorting)):
            $sortby = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:SortBy');
            $sortbyproperty = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:SortProperty');
            $propertyname = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:PropertyName', strtolower($ogcsearchsorting));
            $sortorder = $dom->createElementNS('http://www.opengis.net/ogc', 'ogc:SortOrder', 'ASC');
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
        $dom->appendChild($getrecords);

        $body = $dom->saveXML();

        $results = cswrecords::CURLRequest('POST', $catalogurl, $body);
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"'));
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

}

?>
