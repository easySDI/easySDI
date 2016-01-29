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
        $query = $db->getQuery(true);
        $query->select('v.id , v.value');
        $query->from('#__sdi_sys_serviceversion v');
        $query->innerJoin('#__sdi_sys_servicecompliance compliance ON v.id=compliance.serviceversion_id ');
        $query->innerJoin('#__sdi_physicalservice_servicecompliance psc ON compliance.id=psc.servicecompliance_id');
        $query->where('psc.service_id=' . (int)$service_id);
        $query->order('v.value');
        
        $db->setQuery($query);
        
        $versions = $db->loadObjectList();
        echo json_encode($versions);
        die();
    }

}