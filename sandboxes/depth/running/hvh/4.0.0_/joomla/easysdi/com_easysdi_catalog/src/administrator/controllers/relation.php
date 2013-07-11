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
        $db->setQuery('SELECT rt.id , rt.value
                        FROM #__sdi_sys_rendertype rt 
                        INNER JOIN #__sdi_sys_rendertype_stereotype rts ON rts.rendertype_id = rt.id
                        INNER JOIN #__sdi_attribute a ON a.stereotype_id = rts.stereotype_id
                        WHERE a.id='.$attributechild_id.' 
                        ORDER BY rt.value');
       
        $rendertype = $db->loadObjectList();
        echo json_encode($rendertype);
        die();
    }
}