<?php

/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * Applications list controller class.
 */
class Easysdi_coreControllerApplications extends Easysdi_coreController {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function &getModel($name = 'Applications', $prefix = 'Easysdi_coreModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    function cancel() {
        // Clear the resource id from the session.
        JFactory::getApplication()->setUserState('com_easysdi_core.edit.applicationresource.id', null);

        $back_url = array('root' => 'index.php',
            'option' => 'com_easysdi_core',
            'view' => 'resources',
            'parentid' => JFactory::getApplication()->getUserState('com_easysdi_core.parent.resource.version.id'));
        $this->setRedirect(JRoute::_(Easysdi_coreHelper::array2URL($back_url), false));
    }

}
