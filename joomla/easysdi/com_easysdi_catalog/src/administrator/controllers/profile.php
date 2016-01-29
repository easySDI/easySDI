<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_catalog
 * @copyright	
 * @license		
 * @author		
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Profile controller class.
 */
class Easysdi_catalogControllerProfile extends JControllerForm {

    function __construct() {
        $this->view_list = 'profiles';
        parent::__construct();
    }

    function getAttributeIdentifier() {
        $jinput = JFactory::getApplication()->input;
        $class_id = $jinput->get('class_id', '0', 'string');

        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('a.id , a.name');
        $query->from('#__sdi_attribute a');
        $query->innerJoin('#__sdi_relation rel ON a.id=rel.attributechild_id');
        $query->where('a.stereotype_id=1');
        $query->where('rel.parent_id=' . (int)$class_id);
        $query->order('a.name');
        
        $db->setQuery($query);
        $attributes = $db->loadObjectList();
        echo json_encode($attributes);
        die();
    }

}