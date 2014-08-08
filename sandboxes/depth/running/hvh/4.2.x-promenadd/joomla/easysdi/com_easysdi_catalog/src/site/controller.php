<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';

class Easysdi_catalogController extends JControllerLegacy {

    public function display($cachable = false, $urlparams = false) {

        // If view is sheet, we'll check access scope before 
        // doing anything (like getting CSW data etc...)
        $vName = $this->input->get('view', null);
        if ($vName == 'sheet'):

            //check if a guid is given
            $mdGuid = $this->input->get('guid', null);
            if (empty($mdGuid)):
                $item = JFactory::getApplication()->input->get('id');
                $db = JFactory::getDbo();
                $query = $db->getQuery(true)
                        ->select('guid')
                        ->from('#__sdi_metadata')
                        ->where('id = ' . (int) $item);
                $db->setQuery($query);
                $mdGuid = $db->loadResult();
            endif;
            if (empty($mdGuid)) :
                $this->setMessage(JText::_('No metadata guid or id given'), 'warning');
                $this->setRedirect(JURI::base());
                return false;
            endif;
            //check if the user has the right to see the sheet
            $db = JFactory::getDBO();
            $query = $db->getQuery(true)
                    ->select('r.*')
                    ->from('#__sdi_metadata m')
                    ->join('INNER', '#__sdi_version v on m.version_id = v.id')
                    ->join('INNER', '#__sdi_resource r on v.resource_id = r.id')
                    ->where('m.guid = \'' . (string) $mdGuid . '\'');
            $db->setQuery($query);
            $resource = $db->loadObject();
            $app = JFactory::getApplication();
            $sdiUser = sdiFactory::getSdiUser();
            if ($resource->accessscope_id != 1):
                if (!$sdiUser->isEasySDI):
                    JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                    return;
                endif;
                if ($resource->accessscope_id == 2):
                    $organisms = sdiModel::getAccessScopeOrganism($resource->guid);
                    $organism = $sdiUser->getMemberOrganisms();
                    if (!in_array($organism[0]->id, $organisms)):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                endif;
                if ($resource->accessscope_id == 3):
                    $users = sdiModel::getAccessScopeUser($resource->guid);
                    if (!in_array($sdiUser->id, $users)):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                endif;
                if ($resource->accessscope_id == 4):
                    $orgCategoriesIdList = $sdiUser->getMemberOrganismsCategoriesIds();
                    $allowedCategories = sdiModel::getAccessScopeCategory($resource->guid);
                    if (count(array_intersect($orgCategoriesIdList, $allowedCategories)) < 1):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                endif;
            endif;
        endif;

        parent::display($cachable, $urlparams);

        return $this;
    }

}
