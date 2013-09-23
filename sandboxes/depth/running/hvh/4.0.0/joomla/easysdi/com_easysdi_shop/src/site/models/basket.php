<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_COMPONENT . "/libraries/easysdi/sdiBasket.php";

/**
 * Easysdi_shop model.
 */
class Easysdi_shopModelBasket extends JModelLegacy {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $content = JFactory::getApplication()->getUserState('com_easysdi_shop.basket.content');
        $this->setState('basket.content', $content);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($content = null) {
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($content)) {
                $content = $this->getState('basket.content');
            }

            $this->_item = unserialize($content);
        }

        return $this->_item;
    }

    /**
     * Method to save the data.
     *
     * @param	sdiBasket		The data.
     * @return	mixed		false on failure.
     * @since	1.6
     */
    public function save($basket) {
        if (empty($basket))
            return false;

        $data = array();

        //Save order object
        if (empty($basket->id))
            $data['id'] = 0;
        else
            $data['id'] = $basket->id;

        $data['name'] = $basket->name;
        $data['buffer'] = $basket->buffer;
        $data['surface'] = $basket->extent->surface;
        $data['thirdparty_id'] = $basket->thirdparty;
        switch (JFactory::getApplication()->input->get('action', 'save', 'string')) {
            case 'order':
                $data['ordertype_id'] = 1;
                $data['orderstate_id'] = 6;
                break;
            case 'estimate':
                $data['ordertype_id'] = 2;
                $data['orderstate_id'] = 6;
                break;
            case 'draft':
                $data['orderstate_id'] = 7;
                break;
        }
        $data['user_id'] = sdiFactory::getSdiUser()->id;


        $table = $this->getTable();
        if ($table->save($data) === true) {
            if (!empty($basket->id)) {
                $this->cleanTables($basket->id);
            }
            $db = JFactory::getDbo();
            
            //Save diffusions
            foreach($basket->extractions as $diffusion):
                $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
                $od = array();
                $od['order_id'] = $table->id;
                $od['diffusion_id'] = $diffusion->id;
                $od['productstate_id'] = 2;
                $orderdiffusion->save($od);
                
                //Save properties
                foreach($diffusion->properties as $property ):
                    foreach($property->values as $value ):
                        $orderpropertyvalue = JTable::getInstance('orderpropertyvalue', 'Easysdi_shopTable');
                        $v = array();
                        $v['orderdiffusion_id'] = $orderdiffusion->id;
                        $v['property_id'] = $property->id;
                        $v['propertyvalue_id'] = $value->id;
                        $v['propertyvalue'] = $value->value;
                        $orderpropertyvalue->save($v);
                    endforeach;
                endforeach;                
            endforeach;           
            
            //Save perimeters
            $orderperimeter = JTable::getInstance('orderperimeter', 'Easysdi_shopTable');
            $op = array();
            $op['order_id'] = $table->id;
            $op['perimeter_id'] = $basket->extent->id;
            $op['value'] = 2;
            $orderperimeter->save($op);
             
            
        }


        return true;
    }

    private function cleanTables($order_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        //Select orderdiffusion_id
        $query->select('id')
                ->from('#__sdi_order_diffusion')
                ->where('order_id = ' . $order_id);
        $db->setQuery($query);
        $orderdiffusion = $db->loadColumn();

        foreach ($orderdiffusion as $id):
            $query = $db->getQuery(true);
            $query->delete('#__sdi_order_propertyvalue')
                    ->where('orderdiffusion_id =' . $id);
            $db->setQuery($query);
            $db->execute();
        endforeach;

        $query = $db->getQuery(true);
        $query->delete('#__sdi_order_diffusion')
                ->where('order_id = ' . $order_id);

        $db->setQuery($query);
        if (!$db->execute())
            return false;
        
        $query = $db->getQuery(true);
        $query->delete('#__sdi_order_perimeter')
                ->where('order_id = ' . $order_id);

        $db->setQuery($query);
        if (!$db->execute())
            return false;

        return true;
    }

    public function getTable($type = 'Order', $prefix = 'Easysdi_shopTable', $config = array()) {
        $this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');
        return JTable::getInstance($type, $prefix, $config);
    }

}