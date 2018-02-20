<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldFromToCalendar extends JFormField {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'FromToCalendar';

    /**
     * Method to get the field input markup.
     *
     * @return  string   The field input markup.
     *
     * @since   11.1
     */
    protected function getInput() {
        // Initialize some field attributes.
        $format = $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d';

        // Build the attributes array.
        $attributes = array();
        if ($this->element['size']) {
            $attributes['size'] = (int) $this->element['size'];
        }
        if ($this->element['maxlength']) {
            $attributes['maxlength'] = (int) $this->element['maxlength'];
        }
        if ($this->element['class']) {
            $attributes['class'] = 'fromtodatefield ' . (string) $this->element['class'];
        } else {
            $attributes['class'] = 'fromtodatefield';
        }
        if ((string) $this->element['readonly'] == 'true') {
            $attributes['readonly'] = 'readonly';
        }
        if ((string) $this->element['disabled'] == 'true') {
            $attributes['disabled'] = 'disabled';
        }
        if ($this->element['onchange']) {
            $attributes['onchange'] = (string) $this->element['onchange'];
        }
        if ($this->required) {
            $attributes['required'] = 'required';
            $attributes['aria-required'] = 'true';
        }

        // Handle the special case for "now".
        if (strtoupper($this->value) == 'NOW') {
            $this->value = strftime($format);
        }

        // Get some system objects.
        $config = JFactory::getConfig();
        $user = JFactory::getUser();

        // If a known filter is given use it.
        switch (strtoupper((string) $this->element['filter'])) {
            case 'SERVER_UTC':
                // Convert a date to UTC based on the server timezone.
                if ((int) $this->value) {
                    // Get a date object based on the correct timezone.
                    $date = JFactory::getDate($this->value, 'UTC');
                    $date->setTimezone(new DateTimeZone($config->get('offset')));

                    // Transform the date string.
                    $this->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;

            case 'USER_UTC':
                // Convert a date to UTC based on the user timezone.
                if ((int) $this->value) {
                    // Get a date object based on the correct timezone.
                    $date = JFactory::getDate($this->value, 'UTC');
                    $date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));

                    // Transform the date string.
                    $this->value = $date->format('Y-m-d H:i:s', true, false);
                }
                break;
        }

        $from_name = $this->name . '[from]';
        $to_name = $this->name . '[to]';

        $from_id = $this->id . '_from';
        $to_id = $this->id . '_to';

        $this->value = str_replace('0000-00-00 00:00:00', '', $this->value);
        $values = explode(',', str_replace('00:00:00', '', $this->value));

        $html = array();
        $html[] = '<div>';
        $html[] = '<div>' . JText::_('COM_EASYSDI_CATALOG_FROM') . '</div>';
        if(key_exists('0', $values)){
            $html[] = JHtml::_('calendar', $values[0], $from_name, $from_id, $format, $attributes);
        }  else {
            $html[] = JHtml::_('calendar', '', $from_name, $from_id, $format, $attributes);
        }
        $html[] = '<div>' . JText::_('COM_EASYSDI_CATALOG_TO') . '</div>';
        if(key_exists('1', $values)){
            $html[] = JHtml::_('calendar', $values[1], $to_name, $to_id, $format, $attributes);
        }  else {
            $html[] = JHtml::_('calendar', '', $to_name, $to_id, $format, $attributes);
        }
        $html[] = '</div>';

        return implode($html);
    }

}
