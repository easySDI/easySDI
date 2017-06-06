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
class JFormFieldMapLayerSQL extends JFormFieldList {

    /**
     * The form field type.
     *
     * @var    string
     * @since  11.1
     */
    public $type = 'MapLayerSQL';

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

        $db = JFactory::getDbo();
        $sdiUser = sdiFactory::getSdiUser();
        $visualization_id = JFactory::getApplication()->getUserState('com_easysdi_map.edit.visualization.id');

        $cls = '(ml.accessscope_id = 1 OR ((ml.accessscope_id = 3) AND (' . (int)$sdiUser->id . ' IN (select a.user_id from #__sdi_accessscope a where a.entity_guid = ml.guid)))';
        $organisms = $sdiUser->getMemberOrganisms();
        $cls .= 'OR ((ml.accessscope_id = 2) AND (';
        $cls .= $organisms[0]->id . ' in (select a.organism_id from #__sdi_accessscope a where a.entity_guid = ml.guid)';
        $cls .= '))';
        $cls .= ')';

        if (!empty($visualization_id)):
            $exclusioncls = 'ml.id NOT IN (SELECT v.maplayer_id FROM #__sdi_visualization v WHERE v.id <> ' . (int)$visualization_id . ' AND v.maplayer_id IS NOT NULL)';
        else:
            $exclusioncls = 'ml.id NOT IN (SELECT v.maplayer_id FROM #__sdi_visualization v WHERE v.maplayer_id IS NOT NULL)';
        endif;
        
        //Exclude layers from de Bing, Google et OSM
        $exclusionbgo = 'ml.id NOT IN (select ml.id from #__sdi_maplayer ml, #__sdi_physicalservice as ps WHERE ml.service_id = ps.id AND ml.service_id = ps.id and ml.servicetype = '. $db->quote('physical') .' and serviceconnector_id IN (12,13,14))';

        
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_maplayer ml')
                ->where($cls)
                ->where('ml.state = 1')
                ->where($exclusioncls)
                ->where($exclusionbgo);



        $db->setQuery($query);
        $layers = $db->loadObjectList();

        // Build the field options.
        if (!empty($layers)) {
            foreach ($layers as $layer) {
                $options[] = JHtml::_('select.option', $layer->id, $layer->name);
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}
