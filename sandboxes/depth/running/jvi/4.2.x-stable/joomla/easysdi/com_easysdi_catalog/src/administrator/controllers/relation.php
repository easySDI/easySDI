<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Relation controller class.
 */
class Easysdi_catalogControllerRelation extends JControllerForm
{

    function __construct() {
        $this->view_list = 'relations';
        parent::__construct();
    }

    function getRenderType() {
        $jinput = JFactory::getApplication()->input;
        $attributechild_id = $jinput->get('attributechild', '0', 'string');
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('rt.id as rendertypeid , rt.value as rendertypevalue, rts.stereotype_id as stereotypeid, av.id as attributevalueid, av.value as attributevaluevalue');
        $query->from('#__sdi_sys_rendertype rt');
        $query->innerJoin('#__sdi_sys_rendertype_stereotype rts ON rts.rendertype_id = rt.id');
        $query->innerJoin('#__sdi_attribute a ON a.stereotype_id = rts.stereotype_id');
        $query->leftJoin('#__sdi_attributevalue av ON av.attribute_id = a.id');
        $query->where('a.id='. (int)$attributechild_id);
        $query->order('rt.value');
        
        $db->setQuery($query);
       
        $rendertype = $db->loadObjectList();
        echo json_encode($rendertype);
        die();
    }
}