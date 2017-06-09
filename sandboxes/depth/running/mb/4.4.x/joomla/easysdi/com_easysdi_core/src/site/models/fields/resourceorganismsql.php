<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_map
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';

/**
 * Supports an custom SQL select list
 */
class JFormFieldResourceOrganismSQL extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'ResourceOrganismSQL';

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

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        //Allowed resourcetype as children
        $id = JFactory::getApplication()->getUserState('com_easysdi_core.edit.resource.id');
        $user = sdiFactory::getSdiUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('o.id, o.name')
                ->from('#__sdi_organism o')
                ->order('o.name')
                ;
        
        if($user->authorize($id, sdiUser::resourcemanager) || ($id==0 && $user->isResourceManager())){
            $query
                ->innerJoin('#__sdi_user_role_organism uro ON uro.organism_id=o.id')
                ->where('o.state=1')
                ->where('uro.role_id='.(int)sdiUser::resourcemanager)
                ->where('uro.user_id='.(int)$user->id)
                ;
        }
        else{
            $query
                ->innerJoin('#__sdi_resource r ON r.organism_id=o.id')
                ->where('r.id='.(int)$id)
                ;
        }

        // Set the query and get the result list.
        $db->setQuery($query);
        $items = $db->loadObjectlist();
        
        if(count($items)>1){
            $options[] = JHtml::_('select.option', '', null);
        }

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                $options[] = JHtml::_('select.option', $item->id, $item->name);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
