<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/model/sdimodel.php';

/**
 * Diffusion controller class.
 */
class Easysdi_shopControllerDownload extends Easysdi_shopController {

    public function direct() {
        $db = JFactory::getDBO();
        $id = JFactory::getApplication()->input->getInt('id', null);
        $tmpl = JFactory::getApplication()->input->get('tmpl', null);
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_diffusion')
                ->where('id = ' . (int) $id);
        $db->setQuery($query);
        $diffusion = $db->loadObject();

        //check if the user has the right to download
        $sdiUser = sdiFactory::getSdiUser();
        if ($diffusion->accessscope_id != 1):
            if (!$sdiUser->isEasySDI):
                $this->setMessage(JText::_('Not authorized to access this resource'), 'warning');
                $this->setRedirect(JURI::base());
                return false;
            endif;
            if ($diffusion->accessscope_id == 2):
                $organisms = sdiModel::getAccessScopeOrganism($diffusion->guid);
                $organism = $sdiUser->getMemberOrganisms();
                if (!in_array($organism[0]->id, $organisms)):
                    $this->setMessage(JText::_('Not authorized to access this resource'), 'warning');
                    $this->setRedirect(JURI::base());
                    return false;
                endif;
            endif;
            if ($diffusion->accessscope_id == 3):
                $users = sdiModel::getAccessScopeUser($diffusion->guid);
                if (!in_array($sdiUser->id, $users)):
                    $this->setMessage(JText::_('Not authorized to access this resource'), 'warning');
                    $this->setRedirect(JURI::base());
                    return false;
                endif;
            endif;
            if ($diffusion->accessscope_id == 4):
                $orgCategoriesIdList = $sdiUser->getMemberOrganismsCategoriesIds();
                $allowedCategories = sdiModel::getAccessScopeCategory($diffusion->guid);
                if (count(array_intersect($orgCategoriesIdList, $allowedCategories)) < 1):
                    $this->setMessage(JText::_('Not authorized to access this resource'), 'warning');
                    $this->setRedirect(JURI::base());
                    return false;
                endif;
            endif;
        endif;

        if (!empty($diffusion->file) || !empty($diffusion->fileurl)):
            $url = 'index.php?option=com_easysdi_shop&view=download&layout=default&id=' . $id;
        elseif (!empty($diffusion->perimeter_id)) :
            $url = 'index.php?option=com_easysdi_shop&view=download&layout=grid&id=' . $id;
        endif;
        if (!empty($tmpl)) {
            $url .= '&tmpl=' . $tmpl;
        }
        $this->setRedirect(JRoute::_($url, false));
    }

    public function download() {
        $jinput = JFactory::getApplication()->input;
        $params = JFactory::getApplication()->getParams('com_easysdi_shop');
        $fileFolder = $params->get('fileFolder');

        $db = JFactory::getDBO();
        $id = $jinput->getInt('id', null);
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_diffusion')
                ->where('id = ' . (int) $id);
        $db->setQuery($query);
        $diffusion = $db->loadObject();

        //check if the user has the right to download
        $sdiUser = sdiFactory::getSdiUser();
        if ($diffusion->accessscope_id != 1):
            if (!$sdiUser->isEasySDI):
                return null;
            endif;
            if ($diffusion->accessscope_id == 2):
                $organisms = sdiModel::getAccessScopeOrganism($diffusion->guid);
                $organism = $sdiUser->getMemberOrganisms();
                if (!in_array($organism[0]->id, $organisms)):
                    return null;
                endif;
            endif;
            if ($diffusion->accessscope_id == 3):
                $users = sdiModel::getAccessScopeUser($diffusion->guid);
                if (!in_array($sdiUser->id, $users)):
                    return null;
                endif;
            endif;
            if ($diffusion->accessscope_id == 4):
                $orgCategoriesIdList = $sdiUser->getMemberOrganismsCategoriesIds();
                if (count(array_intersect($orgCategoriesIdList, $allowedCategories)) < 1):
                    return null;
                endif;
            endif;
        endif;

        if (!empty($diffusion->file)):
            $file = file_get_contents($fileFolder . '/' . $diffusion->file);
        elseif (!empty($diffusion->fileurl)):
            $file = file_get_contents($diffusion->fileurl);
        elseif (!empty($diffusion->perimeter_id)) :
            $url = $jinput->get('url', null);
            if (empty($url)) {
                die();
            }
            $file = file_get_contents($url);
        endif;

        //Record the download for statistic purpose        
        // Insert columns.
        $columns = array('diffusion_id, user_id', 'executed');
        // Insert values.
        if ($sdiUser->isEasySDI):
            $id = $sdiUser->id;
        else:
            $id = null;
        endif;
        $values = array($diffusion->id, $id, $query->quote(date("Y-m-d H:i:s")));
        $query = $db->getQuery(true)
                ->insert('#__sdi_diffusion_download')
                ->columns($columns)
                ->values(implode(',', $values));
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (RuntimeException $e) {
            //Statistic can not be recorded but it doesn't mean that the download have to be interrupted
            //TODO : a message has to be sent to the site admin that this record can not be done
        }

        error_reporting(0);

        $pos = strrpos($diffusion->file, '.');
        $extension = substr($diffusion->file, $pos + 1);

        ini_set('zlib.output_compression', 0);
        header('Pragma: public');
        header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: none');
        header("Content-Length: " . strlen($file));
        header('Content-Type: application/octetstream; name="' . $extension . '"');
        header('Content-Disposition: attachement; filename="' . $diffusion->name . '.' . $extension . '"');

        echo $file;
        die();
    }

}
