<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_service
 * @copyright	
 * @license		
 * @author		
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldSupportedVersions extends JFormField {

    protected $type = 'supportedversions';

    public function getInput() {
        // Initialize variables.
        $html = array();

        $supportedversions = json_decode($this->form->getValue('supportedversions'));
        // Start the action field output.
        $html[] = '<span id="div-supportedversions" class="span5 ' . (string) $this->element['class'] . ' ">';
        if ($supportedversions) {
            foreach ($supportedversions as $supportedversion) {
                $html[] .= '<span class="label label-info">';
                $html[] .= $supportedversion;
                $html[] .= '</span>';
            }
        }
        $html[] .= '</span>';


        $html[] .= '</span>';

        return implode($html);

        //if(isset($this->element['extension']))
    }

}