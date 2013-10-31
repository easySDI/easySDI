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

    
    public function download() {
        $params = JFactory::getApplication()->getParams('com_easysdi_shop');
        $fileFolder = $params->get('fileFolder');

        $db = JFactory::getDBO();
        $id = JFactory::getApplication()->input->getInt('id', null);
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_diffusion')
                ->where('id = ' . (int) $id);
        $db->setQuery($query);
        $diffusion = $db->loadObject();

        //check if the user has the right to download
        $sdiUser = sdiFactory::getSdiUser();
        if ($diffusion->accessscope_id != 1):
            if (!$sdiUser->isEasySDI)
                if ($diffusion->accessscope_id == 2):
                    $organisms = sdiModel::getAccessScopeOrganism($diffusion->guid);
                    $organism = sdiFactory::getSdiUser()->getMemberOrganisms();
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
        endif;

        if (!empty($diffusion->file)):
            $file = file_get_contents($fileFolder . '/' . $diffusion->file);
        elseif (!empty($diffusion->fileurl)):
            $file = file_get_contents($diffusion->fileurl);
        elseif (!empty($diffusion->perimeter_id)) :
            //TODO : map with grid to download file   
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
        $values = array($diffusion->id, $id, '"'.date("Y-m-d H:i:s").'"');
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

        ini_set('zlib.output_compression', 0);
        header('Pragma: public');
        header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: none');
        header("Content-Length: " . strlen($file));
        header('Content-Type: application/octetstream; name="zip"');
        header('Content-Disposition: attachement; filename="' . $diffusion->name . '.zip"');

        echo $file;
        die();
    }

}