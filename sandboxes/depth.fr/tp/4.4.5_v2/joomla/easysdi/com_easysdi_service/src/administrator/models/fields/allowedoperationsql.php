<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('sql');

class JFormFieldAllowedoperationSQL extends JFormFieldSQL {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'allowedoperationSQL';

    /**
     * The service connector type.
     *
     * @var    string
     */
    protected $serviceconnector;

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
            case 'serviceconnector':
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
            case 'serviceconnector':
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
            $this->serviceconnector = (string) $this->element['serviceconnector'];
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
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];

        // Get the database object.
        $db = JFactory::getDbo();

        // Set the query and get the result list.
        $subquery = "SELECT sv.value, sv.ordering
					FROM #__sdi_sys_servicecompliance sc
					INNER JOIN #__sdi_sys_serviceversion sv
					ON sv.id = sc.serviceversion_id
					INNER JOIN #__sdi_sys_serviceconnector scc
					ON scc.id = sc.serviceconnector_id
					WHERE scc.value = 'CSW'
					ORDER BY sv.ordering DESC
					";
        $db->setQuery($subquery);
        $serviceversions = $db->loadObjectlist();
        
        $query = "SELECT so.id as id, so.value as value
				FROM #__sdi_sys_serviceoperation so
				INNER JOIN #__sdi_sys_operationcompliance oc
				ON oc.serviceoperation_id = so.id
				INNER JOIN #__sdi_sys_servicecompliance sc
				ON sc.id = oc.servicecompliance_id
				INNER JOIN #__sdi_sys_serviceversion sv
				ON sv.id = sc.serviceversion_id
				INNER JOIN #__sdi_sys_serviceconnector scc
				ON scc.id = sc.serviceconnector_id
				WHERE scc.value = '" . $this->serviceconnector . "'
				AND oc.implemented = 1
				AND so.state = 1
				AND sv.value = '" . $serviceversions[0]->value . "'";

        $db->setQuery($query);
        $items = $db->loadObjectlist();

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($this->translate == true) {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                } else {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        return $options;
    }

}
