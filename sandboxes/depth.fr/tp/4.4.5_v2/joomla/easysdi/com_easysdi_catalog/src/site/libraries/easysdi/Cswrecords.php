<?php

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/OgcFilters.php';

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class Cswrecords extends SearchForm {

    /** */
    const ROLE_MEMBEROF = 1;
    
    /** @var array */
    private $searchcriteria;

    /* @var SdiLanguageDao */
    private $ldao;

    /** @var OgcFilters */
    private $ogcFilters;

    /** @var boolean if is true, set harvested at true */
    private $addHarvested = true;
    private $ogcUri = 'http://www.opengis.net/ogc';
    private $ogcPrefix = 'ogc';

    function __construct($item) {
        parent::__construct();

        $this->item = $item;
        $this->searchcriteria = parent::loadSystemFields();
        $this->ldao = new SdiLanguageDao();
        $this->ogcFilters = new OgcFilters($this->dom);
        
    }

    public function getRecords() {
        // Workaround to replace accented characters in the search fields.
        array_walk($this->data, array($this,'replaceAccent'));
        
        $lang = JFactory::getLanguage()->getTag();
        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $catalogurl = $params->get('catalogurl');
        $srpn = $params->get('searchresultpaginationnumber');
        $startposition = JFactory::getApplication()->input->getInt('start',0 ) + 1;
        
        //Contextual search result pagination number
        $q = $this->db->getQuery(true)
                ->select('contextualsearchresultpaginationnumber')
                ->from('#__sdi_catalog')
                ->where('id = ' . (int) $this->item->id);
        $this->db->setQuery($q);
        $csrpn = $this->db->loadResult();
        
        // choose limit between global and contextual configuration
        $limit = empty($csrpn) || $csrpn == 0 ? $srpn : $csrpn;

        //Csw sorting field
        $q = $this->db->getQuery(true)
                ->select('css.ogcsearchsorting')
                ->from('#__sdi_catalog_searchsort css')
                ->innerJoin('#__sdi_language l ON l.id = css.language_id ')
                ->where('css.catalog_id = ' . (int) $this->item->id)
                ->where('l.code = ' . $this->db->quote($lang) );
        $this->db->setQuery($q);
        $ogcsearchsorting = $this->db->loadResult();

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

        $and1 = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
        $and2 = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
        $and3 = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
        $and4 = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');

        $or1 = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Or');

        foreach ($this->data as $key => $value) {
            $name = explode('_', $key);

            if (key_exists($name[0], $this->searchcriteria)) {

                switch ($this->data['searchtype']) {
                    case 'simple':
                        if ($this->searchcriteria[$name[0]]->tab_value != 'advanced') {
                            $element = $this->switchOnFieldName($name, $value);
                            if (isset($element)) {
                                $and3->appendChild($element);
                            }
                        }
                        break;
                    case 'advanced':
                        $element = $this->switchOnFieldName($name, $value);
                        if (isset($element)) {
                            $and3->appendChild($element);
                        }
                        break;
                }
            }
        }

        $and1->appendChild($and2);
        if ($and3->hasChildNodes()) {
            $and2->appendChild($and3);
        }
        $and2->appendChild($or1);

        $filter->appendChild($and1);

        $or1->appendChild($this->ogcFilters->getIsEqualTo('harvested', 'true'));
        $or1->appendChild($and4);

        $and4->appendChild($this->ogcFilters->getIsEqualTo('harvested', 'false'));
        $and4->appendChild($this->getResouceType($this->getAllResourcetype()));

        $cswfilter = $this->getCswFilter($this->item->id);

        if (!empty($cswfilter)) {
            $and1->appendChild($cswfilter);
        }

        // Permanent criteria
        $and4->appendChild($this->ogcFilters->getIsEqualTo('metadatastate', 'published'));
        $datetime = new DateTime('tomorrow');
        $and4->appendChild($this->ogcFilters->getIsLessOrEqual('published', $datetime->format('Y-m-d')));

        // User and organism filter
        $and4->appendChild($this->getOrganismBlock());

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

        $results = $this->CURLRequest('POST', $catalogurl, $body);
        
        if (!$results) {
            return false;
        }
        $doc = new DOMDocument();
        $loadTest = $doc->loadXML($results);

        if ($loadTest) {
            // ContrÃ´ler si le XML ne contient pas une erreur
            if ($doc->getElementsByTagName('ExceptionReport')->length > 0) {
                $msg = $doc->getElementsByTagName("ExceptionReport")->item(0)->nodeValue;
                JFactory::getApplication()->enqueueMessage($msg, 'error');
                return false;
            } else {
                $searchResults = $doc->getElementsByTagName('SearchResults')->item(0);
                $matched = $searchResults->getAttribute('numberOfRecordsMatched');
                $nextRecord = $searchResults->getAttribute('nextRecord');

                //Put the numberOfRecordsMatched in session variable for pagination
                JFactory::getApplication('com_easysdi_catalog')->setUserState('global.list.total', $matched);
            }
        }

        return $doc;
    }

    /**
     * 
     * @param string $type Method of the request POST, GET
     * @param string $url 
     * @param string $xmlBody Rquest body
     * @return string response
     */
    protected function CURLRequest($type, $url, $xmlBody = "") {
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
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml; charset="UTF-8"', 'charset="UTF-8"', 'Expect:'));
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

    private function switchOnFieldName($name, $value) {
        switch ($name[1]) {
            case 'fulltext':
                if (!empty($value)) {
                    return $this->getFullText($value);
                }
                break;
            case 'resourcetype':
                if (count(array_filter($value)) > 0) {
                    $this->addHarvested = false;
                    return $this->getResouceType($value);
                }
                break;
            case 'versions':
                if ($value) {
                    $this->addHarvested = false;
                    return $this->getVersions($value);
                }
                break;
            case 'resourcename':
                if (!empty($value)) {
                    $this->addHarvested = false;
                    return $this->getResouceName($value);
                }
                break;
            case 'created':
                $this->addHarvested = false;
                if (!empty($value['from']) || !empty($value['to'])) {
                    return $this->getMetadataCreated($value['from'], $value['to']);
                }
                break;
            case 'published':
                $this->addHarvested = false;
                if (!empty($value['from']) || !empty($value['to'])) {
                    return $this->getMetadataPublished($value['from'], $value['to']);
                }
                break;
            case 'organism':
                if (count(array_filter($value)) > 0) {
                    $this->addHarvested = false;
                    return $this->getOrganism($value);
                }
                break;
            case 'definedBoundary':
                if (count(array_filter($value)) > 0) {
                    return $this->getDefinedBoundary($name, $value);
                }
                break;
            case 'isDownloadable':
                $this->addHarvested = false;
                return $this->getIsDownloadable();
            case 'isFree':
                $this->addHarvested = false;
                return $this->getIsFree();
            case 'isOrderable':
                $this->addHarvested = false;
                return $this->getIsOrderable();
            case 'isViewable':
                $this->addHarvested = false;
                return $this->getIsViewable();
                break;
            default :
                if (is_array($value)) {
                    $value_filter = array_filter($value);
                    if (count($value_filter) > 0) {
                        $element = $this->switchOnRenderType($name, $value_filter);
                        if ($element != FALSE) {
                            return $element;
                        }
                    }
                } else {
                    if (!empty($value)) {

                        $element = $this->switchOnRenderType($name, $value);
                        if ($element != FALSE) {
                            return $element;
                        }
                    }
                }
                break;
        }
    }

    private function getCswFilter($catalog_id) {
        //Csw filter
        $q = $this->db->getQuery(true)
                ->select('cswfilter')
                ->from('#__sdi_catalog')
                ->where('id = ' . (int) $catalog_id);
        $this->db->setQuery($q);
        $cswfilter = $this->db->loadResult();

        //Csw filter defines on the catalog
        if (!empty($cswfilter)) {

            $dom = new DOMDocument('1.0', 'utf-8');
            $dom->loadXML($cswfilter);

            $cloned = $dom->firstChild->cloneNode(TRUE);

            return $this->dom->importNode($cloned, TRUE);
        } else {
            return false;
        }
    }

    private function switchOnRenderType($propertyName, $value) {

        if (array_key_exists($propertyName[0], $this->searchcriteria)) {
            $searchcriteria = $this->searchcriteria[$propertyName[0]];

            if (isset($searchcriteria->rel_rendertype_id)) {
                $rendertype_id = $searchcriteria->rel_rendertype_id;
            } else {
                $rendertype_id = $searchcriteria->rendertype_id;
            }

            switch ($rendertype_id) {
                case EnumRendertype::$LIST:
                    return $this->getList($propertyName[1], $value);
                case EnumRendertype::$RADIOBUTTON:
                case EnumRendertype::$CHECKBOX:
                    return $this->ogcFilters->getIsEqualTo($propertyName[1], $value);
                case EnumRendertype::$TEXTBOX:
                case EnumRendertype::$TEXTAREA:
                    return $this->ogcFilters->getIsLike($propertyName[1], $value);
                case EnumRendertype::$DATE:
                    return $this->getDate($propertyName[1], $value['from'], $value['to']);
                default:
                    break;
            }
        } else {
            return false;
        }
    }

    private function getList($propertyName, $value) {
        $or = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':Or');

        foreach ($value as $literal) {
            $or->appendChild($this->ogcFilters->getIsEqualTo($propertyName, $literal));
        }

        return $or;
    }

    private function getDate($propertyName, $from = '', $to = '') {
        if (!empty($from) && empty($to)) {
            return $this->ogcFilters->getIsGreatherOrEqual($propertyName, $from);
        } elseif (empty($from) && !empty($to)) {
            return $this->ogcFilters->getIsLessOrEqual($propertyName, $to);
        } else {
            return $this->ogcFilters->getIsBetween($propertyName, $from, $to);
        }
    }

    private function getFullText($literal) {
        $language_code = JFactory::getLanguage()->getTag();
        $language = $this->ldao->getByCode($language_code);
        $catalog_language_id = JComponentHelper::getParams('com_easysdi_catalog')->get('defaultlanguage');

        $or = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':Or');

        if ($language->id == $catalog_language_id) {
            $title = $this->ogcFilters->getIsLike('title', $literal);
            $keyword = $this->ogcFilters->getIsLike('keyword', $literal);
            $abstract = $this->ogcFilters->getIsLike('abstract', $literal);
            $resourcename = $this->ogcFilters->getIsLike('resourcename', $literal);
        } else {
            $title = $this->ogcFilters->getIsLike('title_' . $language->{'iso3166-1-alpha2'}, $literal);
            $keyword = $this->ogcFilters->getIsLike('keyword_' . $language->{'iso3166-1-alpha2'}, $literal);
            $abstract = $this->ogcFilters->getIsLike('abstract_' . $language->{'iso3166-1-alpha2'}, $literal);
            $resourcename = $this->ogcFilters->getIsLike('resourcename', $literal);
        }

        $or->appendChild($title);
        $or->appendChild($keyword);
        $or->appendChild($abstract);
        $or->appendChild($resourcename);

        return $or;
    }

    private function getResouceType($value) {
        $or = $this->ogcFilters->getOperator(OgcFilters::OPERATOR_OR);

        foreach ($value as $literal) {
            $or->appendChild($this->ogcFilters->getIsEqualTo('resourcetype', strtolower($literal)));
        }

        return $or;
    }

    private function getVersions($literal) {
        $or = $this->ogcFilters->getOperator(OgcFilters::OPERATOR_OR);
        $or->appendChild($this->ogcFilters->getIsLessOrEqual('endpublished', '0000-00-01'));
        $datetime = new DateTime('tomorrow');
        $or->appendChild($this->ogcFilters->getIsGreatherOrEqual('endpublished', $datetime->format('Y-m-d')));
        return $or;
        
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

    private function getOrganism($value) {
        $or = $this->ogcFilters->getOperator(OgcFilters::OPERATOR_OR);

        foreach ($value as $literal) {
            $or->appendChild($this->ogcFilters->getIsEqualTo('resourceorganism', strtolower($literal)));
        }

        return $or;
    }

    private function getDefinedBoundary($propertyName, $value) {
        $searchCriteria = $this->searchcriteria[$propertyName[0]];
        
        $params = json_decode($searchCriteria->params);
        if(empty($params)){
            $params = new stdClass();
            $params->searchboundarytype = parent::SEARCHTYPEBBOX;
        }

        $or = $this->dom->createElementNS($this->ogcUri, $this->ogcPrefix . ':Or');

        foreach ($value as $literal) {
            if(empty($literal)){
                continue;
            }
            if ($params->searchboundarytype == parent::SEARCHTYPEID) {
                $this->getDefinedBoundaryById($or,$literal,$params->boundarysearchfield);
            } else {
                $coordinate = explode('#', $literal);
                $or->appendChild($this->ogcFilters->getBBox($coordinate[3], $coordinate[1], $coordinate[2], $coordinate[0]));
            }
        }

        return $or;
    }
    
    private function getDefinedBoundaryById(&$or, $literal, $boundarysearchfield) {
        $query = $this->db->getQuery(true);
        $query->select('b.alias, b.parent_id, p.alias as parent_alias');
        $query->from('#__sdi_boundary b');
        $query->join('LEFT', '#__sdi_boundary p on b.parent_id = p.id');
        $query->where('b.alias LIKE ' . $this->db->quote($literal));
        $this->db->setQuery($query);
        $bound = $this->db->loadObject();
        if(!empty($bound)){
            $this->getDefinedBoundaryById($or, $bound->parent_alias, $boundarysearchfield);
        }
        $or->appendChild($this->ogcFilters->getIsEqualTo($boundarysearchfield, $literal));
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

    private function getAllResourcetype() {
        $query = $this->db->getQuery(true);

        $query->select('t.alias');
        $query->from('#__sdi_resourcetype t');
        $query->innerJoin('#__sdi_catalog_resourcetype crt ON crt.resourcetype_id = t.id');
        $query->where('crt.catalog_id = ' . (int)$this->item->id);

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        $resourcetype = array();

        foreach ($results as $result) {
            $resourcetype[] = $result->alias;
        }

        return $resourcetype;
    }

    private function getOrganismBlock() {
        $or = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:Or');
        $or->appendChild($this->ogcFilters->getIsEqualTo('scope', 'public'));

        $sdiUser = sdiFactory::getSdiUser();

        if($sdiUser->isEasySDI){

            // scope user
            $and = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
            $and->appendChild($this->ogcFilters->getIsEqualTo('scope', 'user'));
            $and->appendChild($this->ogcFilters->getIsEqualTo('sdiuser', $sdiUser->user->guid));
            $or->appendChild($and);

            // scope organism
            $organisms = $sdiUser->getMemberOrganisms();
            $and = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
            $and->appendChild($this->ogcFilters->getIsEqualTo('scope', 'organism'));
            $and->appendChild($this->ogcFilters->getIsEqualTo('sdiorganism', $organisms[0]->guid));
            $or->appendChild($and);

            // scope category
            foreach($sdiUser->getMemberOrganismsCategories() as $category){
                $and = $this->dom->createElementNS('http://www.opengis.net/ogc', 'ogc:And');
                $and->appendChild($this->ogcFilters->getIsEqualTo('scope', 'category'));
                $and->appendChild($this->ogcFilters->getIsEqualTo('sdiorganism', $category->guid));
                $or->appendChild($and);
            }
        }

        return $or;
    }
    
    /**
     * 
     * @param string $string
     * @return string A string without accent
     */
    function replaceAccent($item, $itemkey) {
        $unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
        
        if(is_array($item)){
            foreach ($item as $key => $value) {
                $item[$key] = strtr($value, $unwanted_array);
            }
            $this->data[$itemkey] = $item;
        }  else {
            $this->data[$itemkey] = strtr($item, $unwanted_array);
        }
    }

}
