<?php
/**
* @version		4.4.0
* @package     com_easysdi_processing
* @copyright	
* @license		
* @author		
*/


// no direct access
defined('_JEXEC') or die;

$user=sdiFactory::getSdiUser();
if(!$user->isEasySDI) {
    return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

var_dump($this->item);
$user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);



    //var_dump($order);
    //var_dump($userRoles);
    ?>
    !TODO