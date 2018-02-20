<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_SITE . '/components/com_easysdi_shop/helpers/easysdi_shop.php';

class sdiIndicatorShop_topdownloadsdetails extends sdiIndicator {

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_FILENAME");
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
        // Get and test the diffusion ID
        $vDiffusionID = JRequest::getVar('diffusion');
        if (!is_numeric($vDiffusionID)) {
            exit();
        }

        // Test the acces to diffusion for this organism
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("
		COUNT(*) FROM
		" . $db->quoteName('#__sdi_diffusion', 'sdidif') . ",
		" . $db->quoteName('#__sdi_version', 'sdiver') . ",
		" . $db->quoteName('#__sdi_resource', 'sdires') . ",
		" . $db->quoteName('#__sdi_organism', 'sdiorg') . "
		WHERE
		sdidif.version_id=sdiver.id AND
		sdiver.resource_id=sdires.id AND
		sdires.organism_id=sdiorg.id AND
		sdidif.id=$vDiffusionID AND sdiorg.id=$organism
		");
        $db->setQuery($query);
        $vTestAccess = $db->loadColumn();
        if ($vTestAccess[0] < 1) {
            exit();
        }

        // Get diffusion name
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("name FROM " . $db->quoteName('#__sdi_diffusion') . " WHERE id=$vDiffusionID");
        $db->setQuery($query);
        $vDiffusionName = $db->loadColumn();

        // Get download list from diffusion id
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("* FROM " . $db->quoteName('#__sdi_diffusion_download') . " WHERE diffusion_id=$vDiffusionID AND executed BETWEEN '" . date("c", $timestart) . "' AND '" . date("c", $timeend) . "'");
        $db->setQuery($query);
        $results = $db->loadAssocList();

        // Group downloads by organisms
        $vOrganismsCounter = array();
        $vTotalCounter = count($results);
        for ($rows = 0; $rows < count($results); $rows++) {
            $vGetUserOrganism = $this->fnGetUserOrganism($results[$rows]["user_id"]);
            $vOrganismID = $vGetUserOrganism["ID"];
            $vOrganismsCounter[$vOrganismID]["Diffusion"] = $vDiffusionName[0];
            $vOrganismsCounter[$vOrganismID]["Name"] = $vGetUserOrganism["Name"];
            $vOrganismsCounter[$vOrganismID]["Counter"] ++;
            $vOrganismsCounter[$vOrganismID]["Percents"] = 0;
        }

        // Calculate percents for each organisms
        foreach ($vOrganismsCounter as $vKey => $vItem) {
            $vOrganismsCounter[$vKey]["Percents"] = round((100 / $vTotalCounter) * $vOrganismsCounter[$vKey]["Counter"], 2);
        }

        // Sort all data by counter desc
        $vArraySort = array();
        foreach ($vOrganismsCounter as $vKey => $vValue) {
            $vArraySort["Diffusion"][$vKey] = $vValue["Diffusion"];
            $vArraySort["Name"][$vKey] = $vValue["Name"];
            $vArraySort["Counter"][$vKey] = $vValue["Counter"];
            $vArraySort["Percents"][$vKey] = $vValue["Percents"];
        }
        array_multisort($vArraySort["Counter"], SORT_NUMERIC, SORT_DESC, $vArraySort["Diffusion"], $vArraySort["Name"], $vArraySort["Percents"], $vOrganismsCounter);

        // Return results
        $return = new stdClass();
        $return->data = $vOrganismsCounter;
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_TITLE');
        $return->columns_title = array(
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_COL1'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_COL2'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_COL3'),
            JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_COL4'),
        );
        return ($return);
    }

    /**
     * Return the user organism or default name if null
     */
    private function fnGetUserOrganism($vfnUserID) {
        // If user id is null
        if (!is_numeric($vfnUserID)) {
            return array(
                "ID" => "0",
                "Name" => JText::_("COM_EASYSDI_DASHBOARD_SHOP_IND_TOPDOWNLOADS_DETAILS_ORGANISM_EMPTY")
            );
        }

        // Get organism id and name
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("
		* FROM " . $db->quoteName('#__sdi_user_role_organism', 'sdirole') . ",
		" . $db->quoteName('#__sdi_organism', 'sdiorg') . "
		WHERE sdirole.organism_id=sdiorg.id AND role_id=1 AND sdirole.user_id=$vfnUserID
		");
        $db->setQuery($query);
        $results = $db->loadAssoc();
        return array(
            "ID" => $results["organism_id"],
            "Name" => $results["name"]
        );
    }

}

