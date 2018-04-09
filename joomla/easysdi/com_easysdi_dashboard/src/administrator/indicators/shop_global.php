<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class sdiIndicatorShop_global extends sdiIndicator {

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads 
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_GLOBAL_FILENAME");
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
        /* Total diffusions */
        $query = $db->getQuery(true)
                ->select(' count(dif.id) as \'total_diff\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('(dif.hasdownload = 1 OR dif.hasextraction = 1)');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDif = $db->loadAssoc();

        /* Total diffusions with extraction */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_hasextraction\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasdownload = 0')
                ->where('dif.hasextraction = 1');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifExt = $db->loadAssoc();

        /* Total diffusions with download */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_hasdownload\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 0')
                ->where('dif.hasdownload = 1');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifDown = $db->loadAssoc();

        /* Total diffusions with download and extraction */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_hasdownandext\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 1')
                ->where('dif.hasdownload = 1');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifDownExt = $db->loadAssoc();

        /* Total diffusions free */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_free\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('(dif.hasdownload = 1 OR dif.hasextraction = 1)')
                ->where('dif.pricing_id = ' . Easysdi_shopHelper::PRICING_FREE);
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifFree = $db->loadAssoc();

        /* Total diffusions fee */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_fee\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('(dif.hasdownload = 1 OR dif.hasextraction = 1)')
                ->where('dif.pricing_id <> ' . Easysdi_shopHelper::PRICING_FREE);
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifFee = $db->loadAssoc();

        /* Total manual mining */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_manual\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 1')
                ->where('dif.productmining_id = ' . Easysdi_shopHelper::PRODUCTMININGMANUAL);
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifManu = $db->loadAssoc();

        /* Total auto mining */
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_auto\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 1')
                ->where('dif.productmining_id = ' . Easysdi_shopHelper::PRODUCTMININGAUTO);
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifAuto = $db->loadAssoc();

        $return = new stdClass();
        $return->data = array_merge($resNbDif, $resNbDifExt, $resNbDifDown, $resNbDifDownExt, $resNbDifFree, $resNbDifFee, $resNbDifManu, $resNbDifAuto);

        return ($return);
    }

}
