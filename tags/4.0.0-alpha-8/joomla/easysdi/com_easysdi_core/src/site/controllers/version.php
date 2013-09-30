<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables/metadata.php';

/**
 * Version controller class.
 */
class Easysdi_coreControllerVersion extends Easysdi_coreController {

    public function remove() {
        // Initialise variables.
        $model = $this->getModel('Version', 'Easysdi_coreModel');

        // Get the user data.
        $data = array();
        $data['id'] = JFactory::getApplication()->input->get('id', null, 'int');

        //First Delete the metadata
        $metadata = JTable::getInstance('metadata', 'Easysdi_catalogTable');
        $keys = array("version_id" => $data['id']);
        $metadata->load($keys);
        if (!$metadata->delete($metadata->id)) {
            // Redirect back to the list screen.
            $this->setMessage(JText::_('Metadata can not be deleted.'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }
        
        //Check how many versions left on the resource
        $version = $model->getData($data['id']);
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true)
                ->select('count(id)')
                ->from('#__sdi_version')
                ->where('resource_id = ' . $version->resource_id);
        $dbo->setQuery($query);
        $num = $dbo->loadResult();
   
        // Attempt to delete the version.
        $return = $model->delete($data);
        // Check for errors.
        if ($return === false) {
            // Redirect back to the list screen.
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
            return false;
        }

        //Delete resource if needed
        if ($num == 1){
            $resource = JTable::getInstance('resource', 'Easysdi_coreTable');
            $resource->load($version->resource_id);
            if(!$resource->delete($resource->id)){
                $this->setMessage(JText::_('Resource can not be deleted.'), 'error');
                $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
                return false;
            }
        }
        
        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_DELETED_SUCCESSFULLY'));
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

}