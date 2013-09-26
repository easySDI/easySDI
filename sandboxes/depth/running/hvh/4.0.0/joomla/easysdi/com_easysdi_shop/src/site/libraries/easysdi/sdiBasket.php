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

require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiExtraction.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPerimeter.php';

class sdiBasket {

    public $id;
    public $name;
    public $buffer;
    public $thirdparty;
    public $extractions = array();
    public $perimeters = array();
    public $extent;
    public $isrestrictedbyperimeter = false;
    public $surfacemin;
    public $surfacemax;
    public $free = true;
    public $visualization = '';

    function __construct() {
        $this->sdiUser = sdiFactory::getSdiUser();
    }

    function loadOrder($orderId) {
        try {
            $db = JFactory::getDbo();

            //Load order object
            $query = $db->getQuery(true)
                    ->select('o.id as id, o.name as name, o.thirdparty_id as thirparty, o.buffer as buffer , o.surface')
                    ->from('#__sdi_order o')
                    ->where('o.id = ' . $orderId);
            $db->setQuery($query);
            $order = $db->loadObject();
            $params = get_object_vars($order);
            foreach ($params as $key => $value) {
                $this->$key = $value;
            }

            //Load diffusion
            $query = $db->getQuery(true)
                    ->select('d.id as id, od.id as orderdiffusion_id, od.productstate_id, od.remark, od.fee, od.completed, od.file, od.size, od.created_by')
                    ->from('#__sdi_diffusion d')
                    ->innerJoin('#__sdi_order_diffusion od ON od.diffusion_id = d.id')
                    ->innerJoin('#__sdi_order o ON o.id = od.order_id')
                    ->where('o.id = ' . $orderId);
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
                        ->where('opv.orderdiffusion_id = ' . $extraction->orderdiffusion_id)
                        ->group('opv.property_id');
                $db->setQuery($query);
                $properties = $db->loadObjectList();
                foreach ($properties as $property):
                    $query = $db->getQuery(true)
                            ->select('opv.propertyvalue_id, opv.propertyvalue')
                            ->from('#__sdi_order_propertyvalue opv')
                            ->where('opv.orderdiffusion_id = ' . $extraction->orderdiffusion_id)
                            ->where('property_id = ' . $property->property_id);
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
                $ex->fee = $extraction->fee;
                $ex->completed = $extraction->completed;
                $ex->file = $extraction->file;
                $ex->size = $extraction->size;
                $ex->created_by = sdiFactory::getSdiUser($extraction->created_by)->name;
                $this->addExtraction($ex);
                $this->setPerimeters($ex->perimeters);
            endforeach;

            //Load Extent od the order
            //     extent = {"id": perimeter_id ,"name": perimeter_name, "surface": surface_order,"allowedbuffer": boolean,"buffer": int_value,"features": string_coordinates or id_array)};
            $query = $db->getQuery(true)
                    ->select('op.*, p.name as perimeter_name')
                    ->from('#__sdi_order_perimeter op')
                    ->innerJoin('#__sdi_perimeter p ON p.id = op.perimeter_id')
                    ->where('op.order_id = ' . $orderId);
            $db->setQuery($query);
            $perimeters = $db->loadObjectList();
            $extent = new stdClass();
            $extent->id = $perimeters[0]->perimeter_id;
            $extent->name = $perimeters[0]->perimeter_name;
            $extent->surface = $order->surface;
            $extent->buffer = $order->buffer;
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

    function addExtraction($extraction) {
        $this->setProperties($extraction);
        $this->extractions[] = $extraction;
    }

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
        foreach ($this->extractions as $key => $extraction):
            $this->setProperties($extraction);
        endforeach;
    }

    function setPerimeters($perimeters) {
        $this->perimeters = $perimeters;
        foreach ($this->perimeters as $perimeter):
            $perimeter->setAllowedBuffer($this->extractions);
        endforeach;
    }

    function setProperties($extraction) {
        if (!empty($extraction->visualization)):
            if ($this->sdiUser->canView($extraction->visualization))
                $this->visualization .= $extraction->visualization . ',';
        endif;
        if ($extraction->restrictedperimeter == '1')
            $this->isrestrictedbyperimeter = true;

        if ((empty($this->surfacemin) && !empty($extraction->surfacemin)) || (!empty($extraction->surfacemin) && $extraction->surfacemin > $this->surfacemin))
            $this->surfacemin = $extraction->surfacemin;

        if ((empty($this->surfacemax) && !empty($extraction->surfacemax)) || (!empty($extraction->surfacemax) && $extraction->surfacemax < $this->surfacemax))
            $this->surfacemax = $extraction->surfacemax;

        if ($extraction->pricing == 2)
            $this->free = false;
    }
    
    function renderAsOrder(){
        
    }
    
    function renderAsRequest(){
        
    }
    
    function renderAsBasket(){
        
    }

}

?>
