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
class Easysdi_catalogControllerSearchcriteria extends JControllerForm {

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
                        WHERE category_id IN (' . $categories . ' )
                        ORDER BY ordering');

        $boundaries = $db->loadObjectList();
        echo json_encode($boundaries);
        die();
    }

    /**
     * Get resource type from a specific catalog
     */
    function getResourcesTypes() {
        $jinput = JFactory::getApplication()->input;
        $catalogId = $jinput->get('catalog_id', '0', 'string');

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('r.id, r.`name`');
        $query->from('#__sdi_catalog_resourcetype cr');
        $query->innerJoin('#__sdi_resourcetype r on r.id = cr.resourcetype_id');
        $query->where('cr.catalog_id = '.$catalogId);
        $query->order('r.`name` ASC');
        
        $db->setQuery($query);
        
        $resourceTypes = $db->loadObjectList();
        echo json_encode($resourceTypes);
        die();
    }

}
