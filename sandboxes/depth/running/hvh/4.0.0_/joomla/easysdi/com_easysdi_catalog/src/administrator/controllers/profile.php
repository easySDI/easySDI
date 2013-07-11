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
        $db->setQuery('SELECT a.id , a.name 
                        FROM #__sdi_attribute a 
                        INNER JOIN #__sdi_relation rel ON a.id=rel.attributechild_id  
                        WHERE a.stereotype_id=1 
                        AND rel.parent_id=' . $class_id . ' 
                        ORDER BY a.name');
        $attributes = $db->loadObjectList();
        echo json_encode($attributes);
        die();
    }

}