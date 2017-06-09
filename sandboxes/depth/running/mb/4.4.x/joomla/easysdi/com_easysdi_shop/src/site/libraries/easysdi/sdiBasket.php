<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiExtraction.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPerimeter.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/models/property.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/models/diffusion.php';

/**
 * Represents an easySDI basket or an order with all its products, perimeter and properties
 */
class sdiBasket {

    public $id;
    public $name;
    public $thirdparty;
    public $extractions = array();
    public $perimeters = array();
    public $extent;
    public $isrestrictedbyperimeter = false;
    public $surfacemin;
    public $surfacemax;
    public $free = true;
    public $visualization = '';
    public $created;
    public $created_by;
    public $thirdorganism;
    public $freeperimetertool = '';
    private $order;

    /**
     * contructor of sdiBasket, to load an existing basket loadOrder($id)
     */
    function __construct() {
        $this->sdiUser = sdiFactory::getSdiUser();
    }

    /**
     * loads an existing basket (order)
     * @param integer $orderId The order id to load from database
     * @param boolean $copy Make a copy (default false)
     */
    function loadOrder($orderId, $copy = false) {
        try {
            $db = JFactory::getDbo();

            $this->order = $this->getOrder($orderId);

            //Copy order
            if ($copy) {
                //reset ID
                $this->id = null;

                //create the copy name
                $this->name = $this->createCopyName();
                
                //Check if the third party is still available
                $this->checkThirdParty();
                    }
                
            //For "non copies": reload the user who's created the order (e.g. in views: order, request )
            if (!$copy) {
                $this->sdiUser = sdiFactory::getSdiUser($this->order->user_id);
            }

            //Load Extent of the order
            //extent = {"id": perimeter_id ,"name": perimeter_name, "surface": surface_order,"allowedbuffer": boolean,"buffer": int_value,"features": string_coordinates or id_array)};
            $this->extent = $this->getExtent();

            //Load extractions of the order
            $extractions = $this->loadOrderExtractions();
            
            //Handle perimeter modification
            $isPerimeterChanged = false;
            foreach ($extractions as $extraction) :
                //Try to build the object {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}

                if ($copy) {//check availability
                    if (!$this->isAvailable($extraction)) {
                        return false;
                    }
                }

                //Build object
                $orderExtractionObject = $this->buildOrderExtraction($extraction);
                $orderProperties = $this->getOrderProperties($extraction->orderdiffusion_id);

                if ($copy) {
                    $diffusionProperties = $this->getDiffusionProperties($extraction);

                    //Check if the order properties are coherent with the diffusion properties  
                    $properties = $this->cleanUpProperties($diffusionProperties, $orderProperties);
                    if (gettype ($properties ) != 'array') {                        
                        return false;
                    }
                    $orderProperties = $properties;
                }

                foreach ($orderProperties as $property):
                    $properyvalues = $this->loadOrderPropertyValues($extraction->orderdiffusion_id, $property->property_id);

                    $propertyobject = new stdClass();
                    $propertyobject->id = $property->property_id;
                    $propertyobject->values = array();

                    //Load the property definition
                    $modelProperty = JModelLegacy::getInstance('Property', 'Easysdi_shopModel');
                    $objProperty = $modelProperty->getItem($property->property_id);
                    foreach ($properyvalues as $properyvalue):
                        if ($copy) {
                            //Check if all the value are still available
                            try {
                                if (!$this->isPropertyValueAvailable($extraction->id, $properyvalue, $objProperty->mandatory)) {
                                    continue;
                                }
                            } catch (Exception $ex) {
                                return false;
                            }
                        }
                        $value = new stdClass();
                        $value->id = $properyvalue->propertyvalue_id;
                        $value->value = $properyvalue->propertyvalue;
                        $propertyobject->values[] = $value;
                    endforeach;
                    $orderExtractionObject->properties[] = $propertyobject;
                endforeach;

                $ex = new sdiExtraction($orderExtractionObject);
                if ($copy) {
                    $hasPerimeter = false;
                    foreach ($ex->perimeters as $diffusionPerimeter):
                        if ($this->extent->id == $diffusionPerimeter->id) {
                            //Perimeter still available    
                            $hasPerimeter = true;
                            break;
                        }
                    endforeach;
                    if (!$hasPerimeter) {
                        //Perimeter is no more available
                        $isPerimeterChanged = true;
                    }
                }

                $ex->productstate_id = $extraction->productstate_id;
                $ex->remark = $extraction->remark;
                $ex->completed = $extraction->completed;
                $ex->file = $extraction->file;
                $ex->displayname = $extraction->displayname;
                $ex->otp = $extraction->otp;
                $ex->otpchance = $extraction->otpchance;
                $ex->size = $extraction->size;
                $ex->created_by = sdiFactory::getSdiUserByJoomlaId($extraction->created_by)->name;

                $this->addExtraction($ex);
                $this->perimeters = $ex->perimeters;
            endforeach;

            if($copy && $isPerimeterChanged):
                //Check if a common perimeter is still available
                $perimeters = array();
                $orderPerimeters = array();
                $hasCommon = false;
                
                foreach($this->extractions as $extraction):
                    foreach($extraction->perimeters as $perimeter):
                        if(isset($perimeters[$perimeter->id])):
                            if($perimeters[$perimeter->id] == count($this->extractions)-1):
                                //Ok at least one perimeter is common
                                $hasCommon = true;
                                array_push($orderPerimeters, $perimeter);
                                //break 2;
                            endif;
                            $perimeters[$perimeter->id] = $perimeters[$perimeter->id] + 1;
                else:
                            if(count($this->extractions) == 1):
                                $hasCommon = true;
                                array_push($orderPerimeters, $perimeter);
                            else:
                                $perimeters[$perimeter->id] = 1;
                endif;
                        endif;
            endforeach;
                endforeach;
                if(!$hasCommon):
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_COPY_ORDER_ERROR'), 'error');
                        JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
                        return false;
                    else:
                        $this->extent = null;
                        $this->surface = null;  
                        $this->perimeters = $orderPerimeters;
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_COPY_ORDER_PERIMETER'), 'warning');

                endif;
            endif;
        } catch (JDatabaseException $ex) {
            JFactory::getApplication()->enqueueMessage($ex->getMessage(), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }
    }

    /**
     * add an extraction to the basket
     * @param sdiExtraction $extraction
     */
    function addExtraction($extraction) {
        $this->setProperties($extraction);
        $this->extractions[] = $extraction;
    }

    /**
     * remove an extraction (=diffusion/product) from the basket by its id
     * @param integer $id
     */
    function removeExtraction($id) {
        foreach ($this->extractions as $key => $extraction):
            if ($extraction->id == $id) {
                unset($this->extractions[$key]);
                break;
            }
        endforeach;

        $this->isrestrictedbyperimeter = false;
        $this->free = true;
        $this->visualization = '';
        $this->surfacemax = null;
        $this->surfacemin = null;
        foreach ($this->extractions as $key => $extraction):
            $this->setProperties($extraction);
        endforeach;
    }

    /**
     * set the permieters of each extraction
     * @param sdiPerimeter $perimeters
     */
    function setPerimeters($perimeters) {
        $this->perimeters = $perimeters;
    }

    /**
     * set the properties of each extraction
     * @param sdiExtraction $extraction
     */
    function setProperties($extraction) {
        if (!empty($extraction->visualization)) {
            if ($this->sdiUser->canView($extraction->visualization)) {
                $this->visualization .= $extraction->visualization . ',';
            }
        }
        if ($extraction->restrictedperimeter == '1') {
            $this->isrestrictedbyperimeter = true;
        }

        if ((empty($this->surfacemin) && !empty($extraction->surfacemin)) || (!empty($extraction->surfacemin) && $extraction->surfacemin > $this->surfacemin)) {
            $this->surfacemin = $extraction->surfacemin;
        }

        if ((empty($this->surfacemax) && !empty($extraction->surfacemax)) || (!empty($extraction->surfacemax) && $extraction->surfacemax < $this->surfacemax)) {
            $this->surfacemax = $extraction->surfacemax;
        }

        if ($extraction->pricing == 2) {
            $this->free = false;
        }
    }

    /**
     * Load the order information from the database
     * @param int $id Order id
     * @return boolean True on success
     */
    private function getOrder($id) {

        //Load order object
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('o.id as id, '
                        . 'o.name as name, '
                        . 'o.thirdparty_id as thirdparty, '
                        . 'o.mandate_ref as mandate_ref, '
                        . 'o.mandate_contact as mandate_contact, '
                        . 'o.mandate_email as mandate_email, '
                        . 'org.name as thirdorganism, '
                        . 'o.surface as surface, '
                        . 'o.level as level, '
                        . 'o.freeperimetertool as freeperimetertool, '
                        . 'o.created, '
                        . 'o.created_by, '
                        . 'o.orderstate_id as orderstate_id, '
                        . 'o.user_id as user_id ,'
                        . 'o.usernotified as usernotified ,'
                        . 'o.validated_date as validated_date ,'
                        . 'o.validated_reason as validated_reason ,'
                        . 'o.validated_by as validated_by ,'
                        . 'o.access_token as access_token ,'
                        . 'o.validation_token as validation_token ')
                ->from('#__sdi_order o')
                ->leftJoin('#__sdi_organism org ON org.id = o.thirdparty_id')
                ->where('o.id = ' . (int) $id);
        $db->setQuery($query);
        $order = $db->loadObject();
        $params = get_object_vars($order);
        foreach ($params as $key => $value) {
            $this->$key = $value;
}
        return $order;
    }

    /**
     * Generate a new name for a copied order
     * @return boolean
     */
    private function createCopyName() {

        $copySuffix = JText::_('COM_EASYSDI_SHOP_BASKET_COPY_ORDER_NAME_SUFFIX');
        $incrementSeparator = ' ';
        $pattern = '/.*' . $copySuffix . '(?:$|' . $incrementSeparator . '(\d*)$)/';
        $matches = array();
        $matched = preg_match($pattern, $this->name, $matches, PREG_OFFSET_CAPTURE);

        //order name contains the copy suffix
        if ($matched) {
            if (count($matches) > 1) { //is third copy or more, change increment
                $newName = substr($this->name, 0, $matches[1][1]) . (((int) $matches[1][0]) + 1);
            } else { //is the second copy, add increment
                $newName = $this->name . $incrementSeparator . '2';
            }
        } else { //is the first copy
            $newName = $this->name . $copySuffix;
        }

        return $newName;
    }

    /**
     * 
     * @return boolean
     */
    private function checkThirdParty() {
        $db = JFactory::getDbo();
        if (isset($this->thirdparty)) {
            $query = $db->getQuery(true)
                    ->select("id, state, selectable_as_thirdparty")
                    ->from("#__sdi_organism")
                    ->where("id = " . (int) $this->thirdparty);
            $db->setQuery($query);
            $organism = $db->loadObject();

            if (!isset($organism) || $organism->state != 1 || $organism->selectable_as_thirdparty != 1) {
                //Third party is no more available : allow copy, remove all third parts information
                $this->thirdparty = null;
                $this->thirdorganism = null;
                $this->mandate_ref = null;
                $this->mandate_contact = null;
                $this->mandate_email = null;
                JFactory::getApplication()->enqueueMessage(JText::_("COM_EASYSDI_SHOP_BASKET_COPY_ORDER_THIRDPARTY"), 'warning');                
            }
        }
    }

    /**
     * 
     * @param type $extraction
     * @return boolean
     */
    private function isAvailable($extraction) {
        if ($extraction->metadatastate_id != 3 //Metadata is not published
                || !sdiModel::checkAccessScope($extraction->rguid, $extraction->raccessscope_id, $this->sdiUser)  //Metadata is no more accessible for the user
                || $extraction->hasextraction != 1 //Extraction is disabled
                || $extraction->diffusionstate_id != 1  //Diffusion is unpublished
                || !sdiModel::checkAccessScope($extraction->guid, $extraction->accessscope_id, $this->sdiUser) //User has no right on this diffusion
        ) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_SHOP_BASKET_COPY_ORDER_ERROR'), 'error');
            JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
            return false;
        }
        return true;
    }

    /**
     * 
     * @return type
     */
    private function loadOrderExtractions() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('d.id as id, d.state as diffusionstate_id,d.guid as guid,d.hasextraction,r.guid as rguid,'
                        . ' m.metadatastate_id,r.accessscope_id as raccessscope_id,  '
                        . 'd.accessscope_id as accessscope_id, od.id as orderdiffusion_id, d.otp as otp,'
                        . 'od.productstate_id, od.remark, od.completed,' . $db->quoteName('od.file') . ' , '
                        . 'od.displayName as displayname, od.otpchance as otpchance, od.size, od.created_by')
                ->from('#__sdi_diffusion d')
                ->innerJoin('#__sdi_order_diffusion od ON od.diffusion_id = d.id')
                ->innerJoin('#__sdi_order o ON o.id = od.order_id')
                ->innerJoin('#__sdi_metadata m ON m.version_id = d.version_id')
                ->innerJoin('#__sdi_version v ON v.id = d.version_id')
                ->innerJoin('#__sdi_resource r ON r.id = v.resource_id')
                ->where('o.id = ' . (int) $this->order->id);
        $db->setQuery($query);
        $extractions = $db->loadObjectList();

        return $extractions;
    }

    /**
     * 
     * @param type $extraction
     * @return \stdClass
     */
    private function buildOrderExtraction($extraction) {
        $extractionobject = new stdClass();
        $extractionobject->id = $extraction->id;
        $extractionobject->properties = array();

        return $extractionobject;
    }

    private function getOrderProperties($orderdiffusion_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('opv.property_id')
                ->from('#__sdi_order_propertyvalue opv')
                ->where('opv.orderdiffusion_id = ' . (int) $orderdiffusion_id)
                ->group('opv.property_id');
        $db->setQuery($query);
        $properties = $db->loadObjectList();

        return $properties;
    }

    /**
     * 
     * @param type $extraction
     * @return type
     */
    private function getDiffusionProperties($extraction) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('p.id, p.mandatory')
                ->from('#__sdi_property p ')
                ->innerJoin('#__sdi_propertyvalue pv ON pv.property_id = p.id')
                ->innerJoin('#__sdi_diffusion_propertyvalue dpv ON dpv.propertyvalue_id = pv.id')
                ->where('dpv.diffusion_id = ' . (int) $extraction->id)
                ->group('p.id')
        ;
        $db->setQuery($query);
        $diffusionProperties = $db->loadObjectList();
        return $diffusionProperties;
    }

    /**
     * 
     * @param type $diffusionProperties
     * @param type $orderProperties
     * @return boolean|array
     */
    private function cleanUpProperties($diffusionProperties, $orderProperties) {
        foreach ($diffusionProperties as $diffusionProperty):
            foreach ($orderProperties as $property):
                if ($property->property_id == $diffusionProperty->id):
                    $property->check = 1;
                    continue 2;
                endif;
            endforeach;
            if ($diffusionProperty->mandatory == 1) {
                //The mandatory property is not define in the order : block copy and sent message
                JFactory::getApplication()->enqueueMessage(JText::_("COM_EASYSDI_SHOP_BASKET_COPY_ORDER_ERROR"), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
                return false;
            }
        endforeach;

        //Remove properties which have been removed from the diffusion definition
        $cleanupProperties = array();
        foreach ($orderProperties as $property):
            if (isset($property->check)):
                array_push($cleanupProperties, $property);
            endif;
        endforeach;

        return $cleanupProperties;
    }

    /**
     * 
     * @param type $orderdiffusion_id
     * @param type $property_id
     * @return type
     */
    private function loadOrderPropertyValues($orderdiffusion_id, $property_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('opv.propertyvalue_id, opv.propertyvalue')
                ->from('#__sdi_order_propertyvalue opv')
                ->where('opv.orderdiffusion_id = ' . (int) $orderdiffusion_id)
                ->where('property_id = ' . (int) $property_id);
        $db->setQuery($query);
        $properyvalues = $db->loadObjectList();
        return $properyvalues;
    }

    /**
     * 
     * @param type $orderId
     * @param type $properyvalue
     * @param type $isPropMandatory
     * @return boolean
     * @throws Exception
     */
    private function isPropertyValueAvailable($diffusion_id, $properyvalue, $isPropMandatory) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('dpv.id, pv.state')
                ->from('#__sdi_diffusion_propertyvalue dpv')
                ->innerJoin('#__sdi_propertyvalue pv ON pv.id = dpv.propertyvalue_id')
                ->where('dpv.propertyvalue_id = ' . (int) $properyvalue->propertyvalue_id)
                ->where("dpv.diffusion_id = " . (int) $diffusion_id);
        $db->setQuery($query);
        $prop = $db->loadObject();
        if (!isset($prop) || $prop->state != 1) {
            if ($isPropMandatory == 1) {
                //Propertyvalue is no more available, block the copy and send message
                JFactory::getApplication()->enqueueMessage(JText::_("COM_EASYSDI_SHOP_BASKET_COPY_ORDER_ERROR"), 'error');
                JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_easysdi_shop&view=orders', false));
                throw new Exception();
            } else {
                //Remove the value from the order
                return false;
            }
        }
        return true;
    }

    /**
     * 
     * @param type $perimeter
     * @return type
     */
    private function getExtent() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('op.*, p.name as perimeter_name')
                ->from('#__sdi_order_perimeter op')
                ->innerJoin('#__sdi_perimeter p ON p.id = op.perimeter_id')
                ->where('op.order_id = ' . (int) $this->order->id);
        $db->setQuery($query);
        $orderPerimeters = $db->loadObjectList();

        $extent = new stdClass();
        $extent->id = $orderPerimeters[0]->perimeter_id;
        $extent->name = $orderPerimeters[0]->perimeter_name;
        $extent->surface = $this->order->surface;
        $extent->level = $this->order->level;
        $extent->features = array();

        foreach ($orderPerimeters as $perimeter):
            if (!strpos($perimeter->value, ',')):
                //Feature id
                $feature = new stdClass();
                $feature->id = $perimeter->value;
                $feature->name = $perimeter->text;
                $extent->features[] = $feature;
            else:
                //Coordinates
                $extent->features = $perimeter->value;
            endif;
        endforeach;

        return $extent;
    }

}
