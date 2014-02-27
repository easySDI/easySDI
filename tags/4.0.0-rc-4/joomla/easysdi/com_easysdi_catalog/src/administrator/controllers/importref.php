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
 * Importref controller class.
 */
class Easysdi_catalogControllerImportref extends JControllerForm
{

    function __construct() {
        $this->view_list = 'importrefs';
        parent::__construct();
    }
    
    function getVersion() {
        $jinput = JFactory::getApplication()->input;
        $service_id = $jinput->get('service_id', '0', 'string');

        $db = JFactory::getDbo();
        $db->setQuery('SELECT v.id , v.value 
                        FROM #__sdi_sys_serviceversion v 
                        INNER JOIN #__sdi_sys_servicecompliance compliance ON v.id=compliance.serviceversion_id  
                        INNER JOIN #__sdi_physicalservice_servicecompliance psc ON compliance.id=psc.servicecompliance_id  
                        WHERE psc.service_id=' . $service_id . ' 
                        ORDER BY v.value');
        $versions = $db->loadObjectList();
        echo json_encode($versions);
        die();
    }

}