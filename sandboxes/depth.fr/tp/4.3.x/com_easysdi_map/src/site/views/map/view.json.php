<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_SITE . '/components/com_easysdi_map/helpers/easysdi_map.php';


class Easysdi_mapViewMap extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    // http://labs.omniti.com/labs/jsend
    private static function returnJson($data,$status='success',$message=null,$code=null) {
        JResponse::clearHeaders();
        JResponse::setHeader('Content-Type', 'application/json', true);
        JResponse::sendHeaders();
        $response_array=compact('status');
        foreach (['data','message','code'] as $param) {
            if (isset($$param)) $response_array[$param]=$$param;
        }
        return json_encode($response_array);
    }

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();
        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_map');
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }


        if (!$this->item) {
            echo self::returnJson(null, 'error', JText::_('COM_EASYSDI_MAP_MAP_NOT_FOUND'));
            return;
        }

        echo self::returnJson( Easysdi_mapHelper::getCleanMap($this->item) );
    }


}
