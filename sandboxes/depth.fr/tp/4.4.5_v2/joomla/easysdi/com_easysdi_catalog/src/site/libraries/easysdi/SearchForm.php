<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

abstract class CriteriaType{
    const System = 1;
    const Relation = 2;
    const Csw = 3;
}

class SearchForm {

    /** Tab value list */
    const SIMPLE = 1;
    const ADVANCED = 2;
    const HIDDEN = 3;

    /** BBOX search type */
    const SEARCHTYPEBBOX = 0;
    const SEARCHTYPEID = 1;

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

    /** @var string[] */
    protected $data;

    function __construct() {
        $this->db = JFactory::getDbo();
        $this->dom = new DOMDocument('1.0', 'utf-8');
        $this->dom->formatOutput = true;

        $this->simple = $this->dom->createElement('fieldset');
        $this->simple->setAttribute('name', 'simple');
        $this->advanced = $this->dom->createElement('fieldset');
        $this->advanced->setAttribute('name', 'advanced');
        $this->hidden = $this->dom->createElement('fieldset');
        $this->hidden->setAttribute('name', 'hidden');

        $this->data = JFactory::getApplication()->input->get('jform', array(), 'array');
    }

    /**
     * Get system fields
     */
    protected function loadSystemFields() {
        $catalog_id = JFactory::getApplication()->input->getInt('id');
        if(empty($catalog_id))
            $catalog_id = JFactory::getApplication()->getUserState('com_easysdi_catalog.edit.catalog.id');

        $query = $this->db->getQuery(true);

        $query->select('sc.id, sc.guid, sc.criteriatype_id, sc.name, sc.alias, sc.rendertype_id');
        $query->select('csc.searchtab_id, csc.defaultvalue, csc.defaultvaluefrom, csc.defaultvalueto, csc.params, csc.guid as catalogsearchcriteriaguid');
        $query->select('st.value as tab_value');
        $query->select('r.rendertype_id as rel_rendertype_id, r.attributechild_id, r.guid as relation_guid');
        $query->select('a.guid as attribute_guid');
        $query->from('#__sdi_searchcriteria AS sc');
        $query->innerJoin('#__sdi_catalog_searchcriteria csc ON sc.id = csc.searchcriteria_id');
        $query->innerJoin('#__sdi_sys_searchtab st ON st.id = csc.searchtab_id');
        $query->leftJoin('#__sdi_relation r on r.id = sc.relation_id');
        $query->leftJoin('#__sdi_attribute a on a.id = r.attributechild_id');
        if (isset($catalog_id)) {
            $query->where('csc.catalog_id = ' . (int)$catalog_id);
        } else {
            $query->where('csc.catalog_id = ' . (int)$this->item->id);
        }
        $query->order('csc.ordering ASC');

        $this->db->setQuery($query);
        $systemFields = $this->db->loadObjectList('id');

        return $systemFields;
    }

}

?>
