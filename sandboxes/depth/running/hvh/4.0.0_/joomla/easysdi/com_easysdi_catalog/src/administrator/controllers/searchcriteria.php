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
 * Searchcriteria controller class.
 */
class Easysdi_catalogControllerSearchcriteria extends JControllerForm
{

    function __construct() {
        $this->view_list = 'searchcriterias';
        parent::__construct();
    }

    
     function getBoundaries() {
        $jinput = JFactory::getApplication()->input;
        $categories = $jinput->get('categories', '0', 'string');
       
        $categories = json_decode($categories); 
        $categories = implode(',', $categories);
        
        $db = JFactory::getDbo();
        $db->setQuery('SELECT id, name
                        FROM #__sdi_boundary  
                        WHERE category_id IN ('.$categories.' )
                        ORDER BY ordering');
       
        $boundaries = $db->loadObjectList();
        echo json_encode($boundaries);
        die();
    }
}