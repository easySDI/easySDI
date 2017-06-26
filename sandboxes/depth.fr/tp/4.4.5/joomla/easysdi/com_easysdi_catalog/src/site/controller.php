<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
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
        $tName = $this->input->get('task', null);
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
            //check if the user has the right to see the sheet if the resource exists in database, else this could be an harvested metadata
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
            if (isset($resource) && ($resource->accessscope_id != 1)):
                if (!$sdiUser->isEasySDI):
                    JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                    return;
                endif;
                if ($resource->accessscope_id == 3)://Access scope organism                    
                    $organisms = sdiModel::getAccessScopeOrganism($resource->guid);
                    //Is the user member, editor or responsible of one of the authorized organism?
                    $authorg = array_merge($sdiUser->getMemberOrganisms(),$sdiUser->getMetadataEditorOrganisms(),$sdiUser->getMetadataResponsibleOrganisms());
                    $ids = array();
                    foreach ($authorg as $o){
                        array_push($ids, $o->id);
                    }                    
                    if (count(array_intersect($ids, $organisms)) == 0):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;                   
                elseif ($resource->accessscope_id == 4)://Access scope user
                    $users = sdiModel::getAccessScopeUser($resource->guid);
                    if (!in_array($sdiUser->id, $users)):
                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
                        return;
                    endif;
                elseif ($resource->accessscope_id == 2)://Access scope organism category
                    $orgCategoriesIdList = $sdiUser->getMemberOrganismsCategoriesIds();
                    $allowedCategories = sdiModel::getAccessScopeCategory($resource->guid);
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
            $jinput = JFactory::getApplication()->input;

            $guid = $jinput->get('metadata_guid', array(), 'array');
            $type = $jinput->get('reporttype', null, 'STRING');
            $lang = $jinput->get('language', null, 'STRING');
            $callfromjoomla = $jinput->get('callfromjoomla', true, 'BOOLEAN');
            $catalog = $jinput->get('context', null, 'STRING');
            $lastVersion = $jinput->get('lastVersion',null, 'STRING');

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
            if(!empty($lastVersion)){
                if($lastVersion == 'yes'){
                    $jinput->set('lastVersion', 1);
                }else{
                    $jinput->set('lastVersion', 0);
                }
            }

            $jinput->set('format', null);

            $jinput->set('guid', $guid);
            $jinput->set('type', $type);
            if(isset($language)){
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
