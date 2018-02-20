<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/CatalogNs.php';

class FormStereotype {

    private $namespaces = array();

    function __construct() {

        $nsdao = new SdiNamespaceDao();

        foreach ($nsdao->getAll() as $ns) {
            $this->namespaces[$ns->prefix] = $ns->uri;
        }
    }

    /**
     * Returns the structure of a stereotype.
     * 
     * @param stdClass $result
     * @return DOMElement[]
     */
    public function getStereotype($result) {
        $elements = array();
        $dom = new DOMDocument('1.0', 'utf-8');

        switch ($result->stereotype_id) {

            case EnumStereotype::$LOCALE:
            case EnumStereotype::$LOCALECHOICE:
            case EnumStereotype::$GEMET:
                $elements = $this->getI18nStereotype();
                break;

            case EnumStereotype::$LIST:
                $elements[] = $this->getListStereotype($result);
                break;

            case EnumStereotype::$GEOGRAPHICEXTENT:
                if ($result->upperbound < 2) {
                    $element = $this->getExtendStereotype($result->relGuid);
                } else {
                    $element = $this->getMultipleExtendStereotype($result);
                }
                $elements[] = $element;
                break;
            case EnumStereotype::$MAPGEOGRAPHICEXTENT:
                $elements[] = $dom->createElement('stereotype');
                break;
            case EnumStereotype::$FREEMAPGEOGRAPHICEXTENT:
                $elements[] = $dom->createElement('stereotype');
                break;

            default:
                $elements[] = $dom->createElementNS($result->stereotype_ns_uri, $result->stereotype_ns_prefix . ':' . $result->stereotype_isocode);
                break;
        }

        return $elements;
    }

    /**
     * 
     * @param stdClass $result
     * @return DOMElement
     */
    private function getListStereotype($result) {
        $dom = new DOMDocument('1.0', 'utf-8');

        $element = $dom->createElementNS($result->list_ns_uri, $result->list_ns_prefix . ':' . $result->attribute_type_isocode);
        
        if (!empty($result->attribute_codelist)) {
            $element->setAttribute('codeList', $result->attribute_codelist);
            if (!empty($result->defaultvalue)) {
                $element->setAttribute('codeListValue', $result->defaultvalue);
            } else {
                $element->setAttribute('codeListValue', '');
            }
        }else{
            if (!empty($result->defaultvalue)) {
                $element->nodeValue = "";
                $item = $dom->createTextNode($result->defaultvalue);
                $element->appendChild($item);
            } 
        }
        return $element;
    }

    /**
     * 
     * @return DOMElement[]
     */
    private function getI18nStereotype($values = '') {
        $sdiLangue = new SdiLanguageDao();
        $languages = $sdiLangue->getSupported();
        $default = $sdiLangue->getDefaultLanguage();
        $dom = new DOMDocument('1.0', 'utf-8');
        $elements = array();

        $characterString = $dom->createElementNS($this->namespaces['gco'], 'gco:CharacterString');
        $characterString->nodeValue = isset($values[$default->{'iso3166-1-alpha2'}]) ? $values[$default->{'iso3166-1-alpha2'}] : '';

        $elements[] = $characterString;
        foreach ($languages as $key => $value) {
            $pt_freetext = $dom->createElementNS($this->namespaces['gmd'], 'gmd:PT_FreeText');
            $textGroup = $dom->createElementNS($this->namespaces['gmd'], 'gmd:textGroup');
            $localisedcs = $dom->createElementNS($this->namespaces['gmd'], 'gmd:LocalisedCharacterString');
            $localisedcs->setAttribute('locale', '#' . $key);
            $localisedcs->nodeValue = isset($values[$default->{'iso3166-1-alpha2'}]) ? $values[$key] : '';

            $textGroup->appendChild($localisedcs);
            $pt_freetext->appendChild($textGroup);

            $elements[] = $pt_freetext;
        }

        return $elements;
    }

    /**
     * 
     * @param stdClass $result
     * @return DOMElement
     */
    private function getMultipleExtendStereotype($result) {
        $dom = new DOMDocument('1.0', 'utf-8');

        $EX_Extent = $dom->createElementNS($this->namespaces['gmd'], 'gmd:EX_Extent');
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':dbid', '0');
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$CLASS);
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$GEOGRAPHICEXTENT);

        $description = $dom->createElementNS($this->namespaces['gmd'], 'gmd:description');
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$BOUNDARY);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$LIST);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':upperbound', $result->upperbound);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', $result->lowerbound);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':relid', $result->id);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':relGuid', $result->relGuid);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_DESCRIPTION');

        $CharacterString = $dom->createElementNS($this->namespaces['gco'], 'gco:CharacterString');

        $description->appendChild($CharacterString);
        $EX_Extent->appendChild($description);

        return $EX_Extent;
    }

    /**
     * 
     * @param type $name
     */
    public function getMultipleExtentStereotype($name) {
        $dom = new DOMDocument('1.0', 'utf-8');
        $boundary = $this->getBoundaryByName($name);

        $extent = $dom->createElementNS($this->namespaces['gmd'], 'gmd:extent');
        $extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':exist', '1');
        $extent->appendChild($dom->importNode($this->getExtendStereotype(null, $boundary->descriptions, $boundary->northbound, $boundary->southbound, $boundary->eastbound, $boundary->westbound, $boundary->codes), true));

        return $extent;
    }

    /**
     * 
     * Returns the structure of the stereotype "Extent"
     * 
     * @param type $extent_type_value
     * @param array $descriptions
     * @param type $northbound_value
     * @param type $southbound_value
     * @param type $eastbound_value
     * @param type $westbound_value
     * @param array $codes
     * @param type $wrap_extent
     * 
     * @return DOMElement
     */
    public function getExtendStereotype($relationGuid = null, $descriptions = '', $northbound_value = '', $southbound_value = '', $eastbound_value = '', $westbound_value = '', $codes = '', $wrap_extent = false) {
        $dom = new DOMDocument('1.0', 'utf-8');

        $extent = $dom->createElementNS($this->namespaces['gmd'], 'gmd:extent');
        //$extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':exist', '1');

        $EX_Extent = $dom->createElementNS($this->namespaces['gmd'], 'gmd:EX_Extent');
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':dbid', '0');
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$CLASS);
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$GEOGRAPHICEXTENT);
        $EX_Extent->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':id', 'decedd18-b418-4336-9980-20f8db3ebb4b');

        $description = $dom->createElementNS($this->namespaces['gmd'], 'gmd:description');
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$BOUNDARY);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$LIST);
        $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_DESCRIPTION');
        if (isset($relationGuid)) {
            $description->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':relGuid', $relationGuid);
        }

        $geographicElement = $dom->createElementNS($this->namespaces['gmd'], 'gmd:geographicElement');
        $geographicElement->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$RELATION);
        $geographicElement->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', '3');
        $geographicElement->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':upperbound', '3');
        $geographicElement->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':id', 'e3bbff44-f3c1-498b-a940-d0bc717f82de');

        $geographicElement1 = $geographicElement->cloneNode();
        $geographicElement1->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':exist', '1');
        $geographicElement1->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_GEOGRAPHICELEMENT');

        $EX_GeographicBoundingBox = $dom->createElementNS($this->namespaces['gmd'], 'gmd:EX_GeographicBoundingBox');
        $EX_GeographicBoundingBox->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$CLASS);
        $EX_GeographicBoundingBox->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':id', '3fcd1f3c-c5a6-4f76-9ac5-195b7138e82b');

        $extentTypeCode = $dom->createElementNS($this->namespaces['gmd'], 'gmd:extentTypeCode');
        $extentTypeCode->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $extentTypeCode->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$HIDDEN);

        $northBoundLatitude = $dom->createElementNS($this->namespaces['gmd'], 'gmd:northBoundLatitude');
        $northBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', '1');
        $northBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $northBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $northBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$NUMBER);
        $northBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_NORTHBOUNDLATITUDE');
        $northBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':boundingbox', 'true');

        $southBoundLatitude = $dom->createElementNS($this->namespaces['gmd'], 'gmd:southBoundLatitude');
        $southBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', '1');
        $southBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $southBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $southBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$NUMBER);
        $southBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_SOUTHBOUNDLATITUDE');
        $southBoundLatitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':boundingbox', 'true');

        $eastBoundLongitude = $dom->createElementNS($this->namespaces['gmd'], 'gmd:eastBoundLongitude');
        $eastBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', '1');
        $eastBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $eastBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $eastBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$NUMBER);
        $eastBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_EASTBOUNDLONGITUDE');
        $eastBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':boundingbox', 'true');

        $westBoundLongitude = $dom->createElementNS($this->namespaces['gmd'], 'gmd:westBoundLongitude');
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', '1');
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$TEXTBOX);
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':stereotypeId', EnumStereotype::$NUMBER);
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':label', 'COM_EASYSDI_CATALOG_EXTENT_WESTBOUNDLONGITUDE');
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':boundingbox', 'true');
        $westBoundLongitude->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':map', 'true');

        $geographicElement2 = $geographicElement->cloneNode();
        $geographicElement2->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':id', 'f8b8668a-cc39-40fd-8491-b19d28e74704');
        //$geographicElement2->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':exist', '0');

        $EX_GeographicDescription = $dom->createElementNS($this->namespaces['gmd'], 'gmd:EX_GeographicDescription');
        $EX_GeographicDescription->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$CLASS);

        $geographicIdentifier = $dom->createElementNS($this->namespaces['gmd'], 'gmd:geographicIdentifier');
        $geographicIdentifier->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$RELATION);
        $geographicIdentifier->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':lowerbound', '1');
        $geographicIdentifier->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':upperbound', '1');
        //$geographicIdentifier->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':exist', '0');

        $MD_Identifier = $dom->createElementNS($this->namespaces['gmd'], 'gmd:MD_Identifier');
        $MD_Identifier->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$CLASS);

        $code = $dom->createElementNS($this->namespaces['gmd'], 'gmd:code');
        $code->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':childtypeId', EnumChildtype::$ATTRIBUT);
        $code->setAttributeNS(CatalogNs::URI, CatalogNs::PREFIX . ':rendertypeId', EnumRendertype::$HIDDEN);

        $CharacterString = $dom->createElementNS($this->namespaces['gco'], 'gco:CharacterString');
        $Boolean = $dom->createElementNS($this->namespaces['gco'], 'gco:Boolean', 'true');
        $Decimal = $dom->createElementNS($this->namespaces['gco'], 'gco:Decimal');

        foreach ($this->getI18nStereotype($descriptions) as $element) {
            $description->appendChild($dom->importNode($element, true));
        }
        $extentTypeCode->appendChild($Boolean->cloneNode(true));
        $northBoundLatitude->appendChild($Decimal->cloneNode());
        $southBoundLatitude->appendChild($Decimal->cloneNode());
        $eastBoundLongitude->appendChild($Decimal->cloneNode());
        $westBoundLongitude->appendChild($Decimal->cloneNode());
        foreach ($this->getI18nStereotype($codes) as $element) {
            $code->appendChild($dom->importNode($element, true));
        }

        $MD_Identifier->appendChild($code);
        $geographicIdentifier->appendChild($MD_Identifier);

        $EX_GeographicBoundingBox->appendChild($extentTypeCode->cloneNode(true));
        $EX_GeographicBoundingBox->appendChild($northBoundLatitude);
        $EX_GeographicBoundingBox->appendChild($southBoundLatitude);
        $EX_GeographicBoundingBox->appendChild($eastBoundLongitude);
        $EX_GeographicBoundingBox->appendChild($westBoundLongitude);

        $EX_GeographicDescription->appendChild($extentTypeCode->cloneNode(true));
        $EX_GeographicDescription->appendChild($geographicIdentifier);

        $geographicElement1->appendChild($EX_GeographicBoundingBox);
        $geographicElement2->appendChild($EX_GeographicDescription);

        $EX_Extent->appendChild($description);
        $EX_Extent->appendChild($geographicElement1);
        if (!empty($descriptions)) {
            $EX_Extent->appendChild($geographicElement2);
        }

        $northBoundLatitude->firstChild->nodeValue = $northbound_value;
        $southBoundLatitude->firstChild->nodeValue = $southbound_value;
        $eastBoundLongitude->firstChild->nodeValue = $eastbound_value;
        $westBoundLongitude->firstChild->nodeValue = $westbound_value;

        if ($wrap_extent) {
            $extent->appendChild($EX_Extent);
            return $extent;
        } else {
            return $EX_Extent;
        }
    }

    /**
     * 
     * @param string $name
     * @return stdClass Object representation of boundary
     */
    private function getBoundaryByName($name) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('b.guid, b.id AS code, b.name AS description, b.northbound, b.southbound, b.eastbound,b.westbound, bc.name AS extent_type')
                ->from('#__sdi_boundary b')
                ->innerJoin('#__sdi_translation t ON b.guid=t.element_guid')
                ->innerJoin('#__sdi_boundarycategory bc ON bc.id=b.category_id')
                ->where('t.text1=' . $query->quote($name))
        ;

        $db->setQuery($query);
        $boundary = $db->loadObject();

        $query = $db->getQuery(true);
        $query->select('t.text1 AS description, t.text3 AS code, l.' . $query->quoteName('iso3166-1-alpha2') . ' AS lang_code');
        $query->from('#__sdi_translation t');
        $query->innerJoin('#__sdi_language l ON l.id = t.language_id');
        $query->where('element_guid = ' . $query->quote($boundary->guid));

        $db->setQuery($query);
        $translations = $db->loadObjectList();

        $descriptions = array();
        $codes = array();
        foreach ($translations as $translation) {
            $descriptions[$translation->lang_code] = $translation->description;
            $codes[$translation->lang_code] = $translation->code;
        }

        $boundary->descriptions = $descriptions;
        $boundary->codes = $codes;

        return $boundary;
    }

}
