<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Relation controller class.
 */
class Easysdi_catalogControllerRelation extends JControllerForm {

    function __construct() {
        $this->view_list = 'relations';
        parent::__construct();
    }

    function getRenderType() {
        $jinput = JFactory::getApplication()->input;
        $attributechild_id = $jinput->get('attributechild', '0', 'string');

        $rendertype = array();
        
        if ($this->isLocale($attributechild_id)) {
            $rendertype = $this->getLocaleRenderType($attributechild_id);
        }else{
            $rendertype = $this->getDefaultRenderType($attributechild_id);
        }
        
        echo json_encode($rendertype);
        die();
    }

    private function getLocaleRenderType($attributechild_id) {
        $lang = JFactory::getLanguage();
        
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('rt.id as rendertypeid , rt.value as rendertypevalue, rts.stereotype_id as stereotypeid, av.id as attributevalueid, t.text2 as attributevaluevalue');
        $query->from('#__sdi_sys_rendertype rt');
        $query->innerJoin('#__sdi_sys_rendertype_stereotype rts ON rts.rendertype_id = rt.id');
        $query->innerJoin('#__sdi_attribute a ON a.stereotype_id = rts.stereotype_id');
        $query->leftJoin('#__sdi_attributevalue av ON av.attribute_id = a.id');
        $query->leftJoin('#__sdi_translation t ON t.element_guid = av.guid');
        $query->leftJoin('#__sdi_language l ON l.id=t.language_id');
        $query->where('a.id=' . (int) $attributechild_id);
        $query->where('l.code='.$query->quote($lang->getTag()));
        $query->order('rt.value');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    private function getDefaultRenderType($attributechild_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('rt.id as rendertypeid , rt.value as rendertypevalue, rts.stereotype_id as stereotypeid, av.id as attributevalueid, av.value as attributevaluevalue');
        $query->from('#__sdi_sys_rendertype rt');
        $query->innerJoin('#__sdi_sys_rendertype_stereotype rts ON rts.rendertype_id = rt.id');
        $query->innerJoin('#__sdi_attribute a ON a.stereotype_id = rts.stereotype_id');
        $query->leftJoin('#__sdi_attributevalue av ON av.attribute_id = a.id');
        $query->where('a.id=' . (int) $attributechild_id);
        $query->order('rt.value');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Check if attribute is an localechoice
     * 
     * @param int $attributechild_id
     * @return boolean
     */
    private function isLocale($attributechild_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.stereotype_id');
        $query->from('#__sdi_attribute a');
        $query->where('a.id=' . (int) $attributechild_id);

        $db->setQuery($query);
        $attribute = $db->loadObject();

        if ($attribute->stereotype_id == 10) {
            return true;
        } else {
            return false;
        }
    }

}
