<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class sdiIndicatorShop_topusers extends sdiIndicator {

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads 
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_TOPUSERS_FILENAME");
    }

    /**
     * Returns the data of the indicator as JSON object
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  DATA object 
     */
    protected function _getData($organism, $timestart, $timeend, $limit = 0) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
                ->select('ju.name as user_name, count(odif.id) as count')
                ->from($db->quoteName('#__sdi_order', 'o'))
                ->join('INNER', $db->quoteName('#__sdi_user', 'eu') . ' ON (' . $db->quoteName('o.user_id') . ' = ' . $db->quoteName('eu.id') . ')')
                ->join('INNER', $db->quoteName('#__users', 'ju') . ' ON (' . $db->quoteName('eu.user_id') . ' = ' . $db->quoteName('ju.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion', 'odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion', 'dif') . ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('o.ordertype_id = 1')
                ->where($db->quoteName('odif.productstate_id') . ' IN(' . implode(',', array(
                            Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE,
                            Easysdi_shopHelper::PRODUCTSTATE_DELETED,
                            Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,
                        )) . ')')
                ->where('odif.completed between \'' . date("c", $timestart) . '\' and  \'' . date("c", $timeend) . '\' ')
                ->group($db->quoteName('eu.id'))
                ->group($db->quoteName('ju.name'))
                ->order('count(odif.id) DESC');

        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }

        $db->setQuery($query, 0, $limit);
        $res = $db->loadRowList();

        $return = new stdClass();
        $return->data = $res;
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPUSERS_TITLE');
        $return->columns_title = array(JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPUSERS_COL1'), JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPUSERS_COL2'));

        return ($return);
    }

}
