<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/helpers/easysdi_catalog.php';

class Easysdi_catalogController extends JControllerLegacy {

    var $_resource = null;
    var $_metadataguid = null;
    var $_metadata = null;

    public function display($cachable = false, $urlparams = false) {

        $jinput = JFactory::getApplication()->input;

        // If view is sheet, we'll check access scope before 
        // doing anything (like getting CSW data etc...)
        $vName = $jinput->get('view', null);
        $tName = $jinput->get('task', null);

        if ($vName == 'sheet'):
            //Transform param request into metadata guid
            $code = $jinput->get('code', null);
            $resourcetype = $jinput->get('resourcetype', null);
            $_metadataguid = $jinput->get('guid', null);
            if (!empty($code) && !empty($resourcetype) && empty($_metadataguid)):
                //Get last published version of resource
                $_resource = Easysdi_catalogHelper::getResourceFromCode($code, $resourcetype);
                $_metadata = Easysdi_catalogHelper::getLastPublishedMetadataOfResource($_resource->id);
                $_metadataguid = $_metadata->guid;
                $jinput->set('guid', $_metadataguid);
            endif;

            //Check if a metadata id is given
            if (empty($_metadataguid)):
                $item = $jinput->get('id');
                $_metadata = Easysdi_catalogHelper::getMetadataFromId($item);
                $_metadataguid = $_metadata->guid;
            endif;

            //Error : can't go further
            if (empty($_metadataguid)) :
                $this->setMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_NOT_FOUND'), 'error');
                $this->setRedirect(JURI::base());
                return false;
            endif;

            //check if the user has the right to see the sheet if the resource exists in database, else this could be an harvested metadata
            if (empty($_resource)):
                $_resource = Easysdi_catalogHelper::getResourceFromMetadata($_metadataguid);
            endif;

            $sdiUser = sdiFactory::getSdiUser();
            if (isset($_resource) && ($_resource->accessscope_id != 1)):
                if (!$sdiUser->isEasySDI):
                    JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                    return;
                endif;
                if ($_resource->accessscope_id == 3):
                    $organisms = sdiModel::getAccessScopeOrganism($_resource->guid);
                    $organism = $sdiUser->getMemberOrganisms();
                    if (!in_array($organism[0]->id, $organisms)):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                endif;
                if ($_resource->accessscope_id == 4):
                    $users = sdiModel::getAccessScopeUser($_resource->guid);
                    if (!in_array($sdiUser->id, $users)):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                endif;
                if ($_resource->accessscope_id == 2):
                    $orgCategoriesIdList = $sdiUser->getMemberOrganismsCategoriesIds();
                    $allowedCategories = sdiModel::getAccessScopeCategory($_resource->guid);
                    if (count(array_intersect($orgCategoriesIdList, $allowedCategories)) < 1):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                endif;
            endif;
        endif;

        /**
         * Mapping easysdi v2 report URL
         */
        if ($tName == 'getReport') {
            $guid = $jinput->get('metadata_guid', array(), 'array');
            $type = $jinput->get('reporttype', null, 'STRING');
            $lang = $jinput->get('language', null, 'STRING');
            $callfromjoomla = $jinput->get('callfromjoomla', true, 'BOOLEAN');
            $catalog = $jinput->get('context', null, 'STRING');
            $lastVersion = $jinput->get('lastVersion', null, 'STRING');

            // map language parameter
            if (!empty($lang)) {
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_language');
                $query->where('UPPER(datatable) LIKE UPPER(' . $query->quote($lang) . ')');
                $db->setQuery($query);
                $language = $db->loadObject();
            }

            // map lastVersion parameter
            if (!empty($lastVersion)) {
                if ($lastVersion == 'yes') {
                    $jinput->set('lastVersion', 1);
                } else {
                    $jinput->set('lastVersion', 0);
                }
            }

            $jinput->set('format', null);
            $jinput->set('guid', $guid);
            $jinput->set('type', $type);
            if (isset($language)) {
                $jinput->set('lang', $language->code);
            }
            $jinput->set('callfromjoomla', $callfromjoomla);
            $jinput->set('catalog', $catalog);
            $jinput->set('view', 'report');
            $jinput->set('tmpl', 'component');
        }

        /**
         * Require 
         */
        if ($vName == 'report') {
            $guids = JFactory::getApplication()->input->get('guid', array(), 'array');
            if (empty($guids)) {
                JError::raiseWarning(400, JText::_('COM_EASYSDI_CATALOG_REPORT_MISSING_GUID'));
                return;
            }
        }

        parent::display($cachable, $urlparams);
        return $this;
    }

}
