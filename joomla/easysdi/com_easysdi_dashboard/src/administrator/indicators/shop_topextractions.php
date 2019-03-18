<?php

/**
 * @version     4.5.1
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2018. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class sdiIndicatorShop_topextractions extends sdiIndicator {

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_FILENAME");
    }

    private $total = 0;

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

        // Calculate the median & set durations to minutes for exports
        for ($rows = 0; $rows < count($res); $rows++) {
            $res[$rows][5] = $this->fnCalcMedianDiffusion($res[$rows][5], $organism, $timestart, $timeend);
            $res[$rows][6] = $res[$rows][3]; // Min
            $res[$rows][7] = $res[$rows][4]; // Max
            $res[$rows][8] = $res[$rows][5]; // Med
        }

        // Convert durations to minutes, hours, days
        for ($rows = 0; $rows < count($res); $rows++) {
            $res[$rows][3] = Easysdi_dashboardHelper::DurationConvert($res[$rows][3]); // Min
            $res[$rows][4] = Easysdi_dashboardHelper::DurationConvert($res[$rows][4]); // Max
            $res[$rows][5] = Easysdi_dashboardHelper::DurationConvert($res[$rows][5]); // Med
        }

        // Return results
        $return = new stdClass();
        $return->data = $res;
        $return->total = $this->total;
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_TITLE');
        $return->columns_title = array(
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL1'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL2'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL3'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL4'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL5'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL6'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL7_EXVIEW_EXPDF') . '_EXVIEW_EXPDF',
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL8_EXVIEW_EXPDF') . '_EXVIEW_EXPDF',
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL9_EXVIEW_EXPDF') . '_EXVIEW_EXPDF',
        );
        return ($return);
    }

    private function getBaseQuery($organism, $timestart, $timeend, $isCount = false) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        if ($isCount) {
            $query->select('count(odif.id) as count');
        } else {
            $query->select('dif.name as prod_name, COUNT(odif.id) as cnt, ROUND(count(odif.id) / ' . $this->total . ' * 100,1) ');
        }


        // Time duration Min
        $query->select("
		(
		SELECT
		ROUND(MIN(TIMESTAMPDIFF(MINUTE,IF(vSdiOrder.validated_date IS NULL,vSdiOrder.sent,vSdiOrder.validated_date),vSdiOrderDiff.completed)))
		FROM " . $db->quoteName("#__sdi_order", "vSdiOrder") . "," . $db->quoteName("#__sdi_order_diffusion", "vSdiOrderDiff") . "
		WHERE
		vSdiOrderDiff.order_id=vSdiOrder.id
		AND vSdiOrderDiff.productstate_id IN(" . implode(',', array(Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE, Easysdi_shopHelper::PRODUCTSTATE_DELETED, Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,)) . ")
		AND vSdiOrderDiff.completed BETWEEN '" . date("c", $timestart) . "' AND '" . date("c", $timeend) . "'
		AND vSdiOrder.ordertype_id=1
		AND vSdiOrderDiff.diffusion_id=odif.diffusion_id
		) AS vDurationMin
		");

        // Time duration Max
        $query->select("
		(
		SELECT
		ROUND(MAX(TIMESTAMPDIFF(MINUTE,IF(vSdiOrder.validated_date IS NULL,vSdiOrder.sent,vSdiOrder.validated_date),vSdiOrderDiff.completed)))
		FROM " . $db->quoteName("#__sdi_order", "vSdiOrder") . "," . $db->quoteName("#__sdi_order_diffusion", "vSdiOrderDiff") . "
		WHERE
		vSdiOrderDiff.order_id=vSdiOrder.id
		AND vSdiOrderDiff.productstate_id IN(" . implode(',', array(Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE, Easysdi_shopHelper::PRODUCTSTATE_DELETED, Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,)) . ")
		AND vSdiOrderDiff.completed BETWEEN '" . date("c", $timestart) . "' AND '" . date("c", $timeend) . "'
		AND vSdiOrder.ordertype_id=1
		AND vSdiOrderDiff.diffusion_id=odif.diffusion_id
		) AS vDurationMax
		");

        // Column to calcuate the MEDIAN from diffusion id
        $query->select("odif.diffusion_id");

        // Next query
        $query->from($db->quoteName('#__sdi_order', 'o'))
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
                ->where('odif.completed BETWEEN \'' . date("c", $timestart) . '\' AND  \'' . date("c", $timeend) . '\' ')
                ->where('o.ordertype_id = 1');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        if (!$isCount) {
            $query->group($db->quoteName('dif.id'))
                    ->group($db->quoteName('dif.name'))
                    ->order('count(odif.id) DESC');
        }
        return $query;
    }

    /**
     * Calculate the median from a diffusion
     */
    private function fnCalcMedianDiffusion($vfnDiffID, $organism, $timestart, $timeend) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("
	TIMESTAMPDIFF(MINUTE,IF(vSdiOrder.validated_date IS NULL,vSdiOrder.sent,vSdiOrder.validated_date),vSdiOrderDiff.completed)
	FROM #__sdi_order AS vSdiOrder,#__sdi_order_diffusion AS vSdiOrderDiff
	WHERE
	vSdiOrderDiff.order_id=vSdiOrder.id
	AND vSdiOrderDiff.productstate_id IN(" . implode(',', array(Easysdi_shopHelper::PRODUCTSTATE_AVAILABLE, Easysdi_shopHelper::PRODUCTSTATE_DELETED, Easysdi_shopHelper::PRODUCTSTATE_REJECTED_SUPPLIER,)) . ")
	AND vSdiOrderDiff.completed BETWEEN '" . date("c", $timestart) . "' AND '" . date("c", $timeend) . "'
	AND vSdiOrder.ordertype_id=1
	AND vSdiOrderDiff.diffusion_id=$vfnDiffID
	");
        $db->setQuery($query);
        $results = $db->loadColumn(0);
        $vResult = $this->fnCalcMedianArray($results);
        return $vResult;
    }

    /**
     * Calculate the median from an array
     */
    private function fnCalcMedianArray($vfnData) {
        sort($vfnData);
        $count = count($vfnData); //total numbers in array
        $middleval = floor(($count - 1) / 2); // find the middle value, or the lowest middle value
        if ($count % 2) { // odd number, middle is the median
            $median = $vfnData[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $vfnData[$middleval];
            $high = $vfnData[$middleval + 1];
            $median = (($low + $high) / 2);
        }
        return $median;
    }

}

