<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SearchForm
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class SearchForm {

    /** Tab value list */
    const SIMPLE = 1;
    const ADVANCED = 2;
    const HIDDEN = 3;

    /** @var JDatabaseDriver */
    protected $db;

    /** @var DOMDocument */
    protected $dom;

    /** @var DOMElement */
    protected $simple;

    /** @var DOMElement */
    protected $advanced;
    
     /** @var DOMElement */
    protected $hidden;

    /** @var stdClass */
    protected $item;

    function __construct() {
        $this->db = JFactory::getDbo();
        $this->dom = new DOMDocument('1.0', 'utf-8');

        $this->simple = $this->dom->createElement('fieldset');
        $this->simple->setAttribute('name', 'simple');
        $this->advanced = $this->dom->createElement('fieldset');
        $this->advanced->setAttribute('name', 'advanced');
        $this->hidden = $this->dom->createElement('fieldset');
        $this->hidden->setAttribute('name', 'hidden');
    }

    /**
     * Get system fields
     */
    protected function loadSystemFields() {
        $query = $this->db->getQuery(true);

        $query->select('sc.id, sc.guid, sc.name, sc.alias, sc.rendertype_id');
        $query->select('csc.searchtab_id, csc.defaultvalue, csc.defaultvaluefrom, csc.defaultvalueto');
        $query->select('st.value');
        $query->from('#__sdi_searchcriteria AS sc');
        $query->innerJoin('#__sdi_catalog_searchcriteria csc ON sc.id = csc.searchcriteria_id');
        $query->innerJoin('#__sdi_sys_searchtab st ON st.id = csc.searchtab_id');
        $query->where('sc.criteriatype_id = ' . EnumCriteriaType::SYSTEM);
        $query->where('csc.catalog_id = ' . $this->item->id);
        $query->order('csc.ordering ASC');

        $this->db->setQuery($query);
        $systemFields = $this->db->loadObjectList();

        return $systemFields;
    }

}

?>
