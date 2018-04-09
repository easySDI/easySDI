<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('sql');

class JFormFieldPhysicalServiceSQL extends JFormFieldSQL {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'physicalserviceSQL';

    /**
     * The service connector type.
     *
     * @var    string
     */
    protected $serviceconnectorField;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param   string  $name  The property name for which to the the value.
     *
     * @return  mixed  The property value or null.
     *
     * @since   3.2
     */
    public function __get($name) {
        switch ($name) {
            case 'serviceconnectorField':
                return $this->$name;
        }

        return parent::__get($name);
    }

    /**
     * Method to set certain otherwise inaccessible properties of the form field object.
     *
     * @param   string  $name   The property name for which to the the value.
     * @param   mixed   $value  The value of the property.
     *
     * @return  void
     *
     * @since   3.2
     */
    public function __set($name, $value) {
        switch ($name) {
            case 'serviceconnectorField':
                $this->$name = (string) $value;
                break;

            default:
                parent::__set($name, $value);
        }
    }

    /**
     * Method to attach a JForm object to the field.
     *
     * @param   SimpleXMLElement  $element  The SimpleXMLElement object representing the <field /> tag for the form field object.
     * @param   mixed             $value    The form field value to validate.
     * @param   string            $group    The field name group control value. This acts as as an array container for the field.
     *                                      For example if the field has name="foo" and the group value is set to "bar" then the
     *                                      full field name would end up being "bar[foo]".
     *
     * @return  boolean  True on success.
     *
     * @see     JFormField::setup()
     * @since   3.2
     */
    public function setup(SimpleXMLElement $element, $value, $group = null) {
        $return = parent::setup($element, $value, $group);

        if ($return) {
            $this->serviceconnectorField = (string) $this->element['serviceconnectorField'];
        }

        return $return;
    }

    /**
     * Method to get the custom field options.
     * Use the query attribute to supply a query to generate the list.
     *
     * @return  array  The field option objects.
     *
     * @since   11.1
     */
    protected function getOptions() {
        $options = array();
        $services = array();

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';

        // Get the database object.
        $db = JFactory::getDbo();

        // Set the query and get the result list.
        $query = "SELECT s.id as id, s.alias as alias, syv.value as version FROM #__sdi_physicalservice s
                        INNER JOIN #__sdi_physicalservice_servicecompliance sc ON sc.service_id = s.id
                        INNER JOIN #__sdi_sys_servicecompliance syc ON syc.id = sc.servicecompliance_id
                        INNER JOIN #__sdi_sys_serviceversion syv ON syv.id = syc.serviceversion_id
                        INNER JOIN #__sdi_sys_serviceconnector sycc ON sycc.id = syc.serviceconnector_id
                        WHERE sycc.value = '" . $this->serviceconnectorField . "'
                        AND s.state IN  (1,0)
                        ";

        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items)) {
            $start = true;
            foreach ($items as $item) {
                if (empty($services[$item->$key])) {
                    $services [$item->$key] = $item->alias . ' - [' . $item->version;
                } else {
                    if ($start == false) {
                        $services [$item->$key] = $services [$item->$key] . '-';
                    }
                    $services [$item->$key] = $services [$item->$key] . $item->version;
                }
                $start = false;
            }
        }

        foreach ($services as $k => $v) {
            $options[] = JHtml::_('select.option', $k, $v . ']');
        }

        return $options;
    }

}
