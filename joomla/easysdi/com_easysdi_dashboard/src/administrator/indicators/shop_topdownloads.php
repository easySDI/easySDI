<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class sdiIndicatorShop_topdownloads extends sdiIndicator {

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads 
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_FILENAME");
    }

    private $total = 1;

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

        $queryTotal = $this->getBaseQuery($organism, $timestart, $timeend, true);
        $db->setQuery($queryTotal);
        $this->total = $db->loadResult();

        if ($this->total > 0) {
            $query = $this->getBaseQuery($organism, $timestart, $timeend);
            $db->setQuery($query, 0, $limit);
            $res = $db->loadRowList();
        } else {
            $res = array();
        }

        $return = new stdClass();
        $return->data = $res;
        $return->total = $this->total;
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_TITLE');
        $return->columns_title = array(
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_COL1'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_COL2'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_COL3')
        );

        return ($return);
    }

    private function getBaseQuery($organism, $timestart, $timeend, $isCount = false) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if ($isCount) {
            $query->select('count(dd.id) as count');
        } else {
            $query->select('dif.name as prod_name, count(dd.id) as cnt, CONCAT(ROUND(count(dd.id) / ' . $this->total . ' * 100,1),\'%\') ');
        }
        $query->from($db->quoteName('#__sdi_diffusion_download', 'dd'))
                ->join('INNER', $db->quoteName('#__sdi_diffusion', 'dif') . ' ON (' . $db->quoteName('dd.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dd.executed between \'' . date("c", $timestart) . '\' and  \'' . date("c", $timeend) . '\' ');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        if (!$isCount) {
            $query->group($db->quoteName('dif.id'))
                    ->group($db->quoteName('dif.name'))
                    ->order('count(dd.id) DESC');
        }
        return $query;
    }

}
