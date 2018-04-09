<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class sdiIndicatorShop_responsetimeproduct extends sdiIndicator {

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads 
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_RESPONSETIMEPRODUCTS_FILENAME");
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

        //<1h
        $query = $db->getQuery(true);
        $query->select($query->concatenate(array('\'<1h (\'', $query->castAsChar('count(odif.id)'), '\')\'')) . ' as timerange, count(odif.id) as count')
                ->from($db->quoteName('#__sdi_order', 'o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion', 'odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion', 'dif') . ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where($db->quoteName('odif.productstate_id') . ' IN(' . implode(',', array(
                            Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE,
                            Easysdi_shopHelper::PRODUCTSTATE_DELETED,
                            Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,
                        )) . ')')
                ->where('o.ordertype_id = 1');
        if ($db->name == 'mysqli')
            $query->where('UNIX_TIMESTAMP(' . $db->quoteName('odif.completed') . ') -  UNIX_TIMESTAMP(' . $db->quoteName('o.sent') . ')  < 3600');
        else
            $query->where('DATEDIFF(second,' . $db->quoteName('odif.completed') . ',' . $db->quoteName('o.sent') . ')  < 3600');
        $query->where('odif.completed between \'' . date("c", $timestart) . '\' and  \'' . date("c", $timeend) . '\' ');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $res_lt1h = $db->loadRowList();

        //1h-48h
        $query = $db->getQuery(true);
        $query->select($query->concatenate(array('\'1h-48h (\'', $query->castAsChar('count(odif.id)'), '\')\'')) . ' as timerange, count(odif.id) as count')
                ->from($db->quoteName('#__sdi_order', 'o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion', 'odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion', 'dif') . ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where($db->quoteName('odif.productstate_id') . ' IN(' . implode(',', array(
                            Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE,
                            Easysdi_shopHelper::PRODUCTSTATE_DELETED,
                            Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,
                        )) . ')')
                ->where('o.ordertype_id = 1');
        if ($db->name == 'mysqli') {
            $query
                    ->where('UNIX_TIMESTAMP(' . $db->quoteName('odif.completed') . ') -  UNIX_TIMESTAMP(' . $db->quoteName('o.sent') . ') <= 172800')
                    ->where('UNIX_TIMESTAMP(' . $db->quoteName('odif.completed') . ') -  UNIX_TIMESTAMP(' . $db->quoteName('o.sent') . ') >= 3600');
        } else {
            $query
                    ->where('DATEDIFF(second, ' . $db->quoteName('odif.completed') . ',' . $db->quoteName('o.sent') . ') <= 172800')
                    ->where('DATEDIFF(second,' . $db->quoteName('odif.completed') . ',' . $db->quoteName('o.sent') . ') >= 3600');
        }
        $query->where('odif.completed between \'' . date("c", $timestart) . '\' and  \'' . date("c", $timeend) . '\' ');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $res_1h_2d = $db->loadRowList();

        //>48h
        $query = $db->getQuery(true);
        $query->select($query->concatenate(array('\'>48h (\'', $query->castAsChar('count(odif.id)'), '\')\'')) . ' as timerange, count(odif.id) as count')
                ->from($db->quoteName('#__sdi_order', 'o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion', 'odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion', 'dif') . ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where($db->quoteName('odif.productstate_id') . ' IN(' . implode(',', array(
                            Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE,
                            Easysdi_shopHelper::PRODUCTSTATE_DELETED,
                            Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,
                        )) . ')')
                ->where('o.ordertype_id = 1');
        if ($db->name == 'mysqli') {
            $query->where('UNIX_TIMESTAMP(' . $db->quoteName('odif.completed') . ') -  UNIX_TIMESTAMP(' . $db->quoteName('o.sent') . ') > 172800');
        } else {
            $query->where('DATEDIFF(second, ' . $db->quoteName('odif.completed') . ',' . $db->quoteName('o.sent') . ') <= 172800');
        }
        $query->where('odif.completed between \'' . date("c", $timestart) . '\' and  \'' . date("c", $timeend) . '\' ');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $res_mt2d = $db->loadRowList();

        //average
        /* $query = $db->getQuery(true);
          $query  ->select('count(odif.id) as count')
          ->from($db->quoteName('#__sdi_order', 'o'))
          ->join('INNER', $db->quoteName('#__sdi_order_diffusion', 'odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
          ->join('INNER', $db->quoteName('#__sdi_diffusion', 'dif') . ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
          ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
          ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
          ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
          ->where($db->quoteName('odif.productstate_id') . ' IN(' . implode(',', array(
          Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE,
          Easysdi_shopHelper::PRODUCTSTATE_DELETED,
          Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,
          )) . ')')
          ->where($db->quoteName('odif.productstate_id') . ' = 1')
          ->where('o.ordertype_id = 1');
          $query->where('odif.completed between \'' . date("c", $timestart) . '\' and  \'' . date("c", $timeend) . '\' ');
          if ($organism != 'all') {
          $query->where($db->quoteName('org.id') . ' = ' . $organism);
          }

          $db->setQuery($query, 0, $limit);
          $res_mt29 = $db->loadRowList(); */


        $return = new stdClass();
        $return->data = array_merge($res_lt1h, $res_1h_2d, $res_mt2d);
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_RESPONSETIMEPRODUCTS_TITLE');
        $return->columns_title = array(JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_RESPONSETIMEPRODUCTS_COL1'), JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_RESPONSETIMEPRODUCTS_COL2'));

        return ($return);
    }

}
