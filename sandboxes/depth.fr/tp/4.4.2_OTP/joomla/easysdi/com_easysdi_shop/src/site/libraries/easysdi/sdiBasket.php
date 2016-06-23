<?php

/**
 * @version     4.4.1
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2016. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiExtraction.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPerimeter.php';

/**
 * reprents an easySDI basket or an order with all its products, permter and properties
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

            //Load order object
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
                    ->where('o.id = ' . (int) $orderId);
            $db->setQuery($query);
            $order = $db->loadObject();
            $params = get_object_vars($order);
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }

            //Copy order
            if ($copy) {
                //reset ID
                $this->id = null;

                //create the copy name
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
                
                $this->name = $newName;
            }

            //For "non copies": reload the user who's created the order (e.g. in views: order, request )
            if (!$copy) {
                $this->sdiUser = sdiFactory::getSdiUser($order->user_id);
            }

            //Load diffusion
            $query = $db->getQuery(true)
                    ->select('d.id as id, d.otp as otp, od.id as orderdiffusion_id, od.productstate_id, od.remark, od.completed,' . $db->quoteName('od.file') . ' , od.displayName as displayname, od.otpchance as otpchance, od.size, od.created_by')
                    ->from('#__sdi_diffusion d')
                    ->innerJoin('#__sdi_order_diffusion od ON od.diffusion_id = d.id')
                    ->innerJoin('#__sdi_order o ON o.id = od.order_id')
                    ->where('o.id = ' . (int) $orderId);
            $db->setQuery($query);
            $extractions = $db->loadObjectList();
            foreach ($extractions as $extraction) :
                //Try to build the object {"id":5,"properties":[{"id": 1, "values" :[{"id" : 4, "value" : "foo"}]},{"id": 1, "values" :[{"id" : 5, "value" : "bar"}]}]}
                $extractionobject = new stdClass();
                $extractionobject->id = $extraction->id;
                $extractionobject->properties = array();
                $query = $db->getQuery(true)
                        ->select('opv.property_id')
                        ->from('#__sdi_order_propertyvalue opv')
                        ->where('opv.orderdiffusion_id = ' . (int) $extraction->orderdiffusion_id)
                        ->group('opv.property_id');
                $db->setQuery($query);
                $properties = $db->loadObjectList();
                foreach ($properties as $property):
                    $query = $db->getQuery(true)
                            ->select('opv.propertyvalue_id, opv.propertyvalue')
                            ->from('#__sdi_order_propertyvalue opv')
                            ->where('opv.orderdiffusion_id = ' . (int) $extraction->orderdiffusion_id)
                            ->where('property_id = ' . (int) $property->property_id);
                    $db->setQuery($query);
                    $properyvalues = $db->loadObjectList();
                    $propertyobject = new stdClass();
                    $propertyobject->id = $property->property_id;
                    $propertyobject->values = array();
                    foreach ($properyvalues as $properyvalue):
                        $value = new stdClass();
                        $value->id = $properyvalue->propertyvalue_id;
                        $value->value = $properyvalue->propertyvalue;
                        $propertyobject->values[] = $value;
                    endforeach;
                    $extractionobject->properties[] = $propertyobject;
                endforeach;

                $ex = new sdiExtraction($extractionobject);
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
                $this->setPerimeters($ex->perimeters);
            endforeach;

            //Load Extent of the order
            //     extent = {"id": perimeter_id ,"name": perimeter_name, "surface": surface_order,"allowedbuffer": boolean,"buffer": int_value,"features": string_coordinates or id_array)};
            $query = $db->getQuery(true)
                    ->select('op.*, p.name as perimeter_name')
                    ->from('#__sdi_order_perimeter op')
                    ->innerJoin('#__sdi_perimeter p ON p.id = op.perimeter_id')
                    ->where('op.order_id = ' . (int) $orderId);
            $db->setQuery($query);
            $perimeters = $db->loadObjectList();
            $extent = new stdClass();
            $extent->id = $perimeters[0]->perimeter_id;
            $extent->name = $perimeters[0]->perimeter_name;
            $extent->surface = $order->surface;
            $extent->level = $order->level;
            $extent->features = array();
            foreach ($perimeters as $perimeter):
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

            $this->extent = $extent;
        } catch (JDatabaseException $e) {
            
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
//        foreach ($this->perimeters as $perimeter):
//            $perimeter->setAllowedBuffer($this->extractions);
//        endforeach;
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

}

?>
