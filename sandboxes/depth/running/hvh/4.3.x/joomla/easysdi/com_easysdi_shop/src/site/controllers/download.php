<?php

/**
 * @version     4.3.2
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
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
    private function stop($url = '', $message = 'Not authorized to access this resource', $type = 'warning'){
        if(empty($url)){
            $uri = JUri::getInstance()->toString();
            $u64 = base64_encode($uri);
            $url = '/index.php?option=com_users&view=login&return='.$u64;
        }
        
        $this->setMessage(JText::_($message), $type);
        $this->setRedirect(JRoute::_($url, false));
        return false;
    }
    
    private function common(){
        $db = JFactory::getDBO();
        $id = JFactory::getApplication()->input->getInt('id', null);
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_diffusion')
                ->where('id = ' . (int) $id);
        $db->setQuery($query);
        $diffusion = $db->loadObject();
        
        //check if the user has the right to download
        //@TODO: should return an error msg if he doesn't have the right
        $sdiUser = sdiFactory::getSdiUser();
        switch($diffusion->accessscope_id){
            case 4: //accesscope by organism's category
                $categories = sdiModel::getAccessScopeCategory($diffusion->guid);
                if(count($categories)==0)
                    return null;
                
                $organism = $sdiUser->getMemberOrganisms();
                
                $query = $db->getQuery(true)
                        ->select('id')
                        ->from('#__sdi_organism_category')
                        ->where('organism_id='.(int)$organism[0]->id)
                        ->where('category_id IN ('.implode($categories).')');
                $db->setQuery($query);
                $db->execute();
                $numRows = $db->getNumRows();
                if($numRows==0)
                    return $this->stop();
                
                break;

            case 3: //accessscope by user
                $users = sdiModel::getAccessScopeUser($diffusion->guid);
                if (!in_array($sdiUser->id, $users))
                    return $this->stop();
                
                break;

            case 2: //accessscope by organism
                $organisms = sdiModel::getAccessScopeOrganism($diffusion->guid);
                $organism = $sdiUser->getMemberOrganisms();
                if (!in_array($organism[0]->id, $organisms))
                    return $this->stop();
                
                break;

            case 1: //accessscope: public
            default:
                //nothing to do
        }
        
        return $diffusion;
    }

    public function direct() {
        $tmpl = JFactory::getApplication()->input->get('tmpl', null);
        
        $diffusion = $this->common();
        if($diffusion === false)
            return false;

        $id = JFactory::getApplication()->input->getInt('id', null);
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
		$id = JFactory::getApplication()->input->getInt('id', null);

        $diffusion = $this->common();
        if($diffusion === false)
            return false;
        
        $layout = !empty($diffusion->perimeter_id) ? 'grid' : 'default';
        if (!empty($diffusion->file)){
            $file = file_get_contents($fileFolder . '/' . $diffusion->file);
            
            //@TODO: retrieve why file_get_contents returns false
            if($file === false)
                $dlError = 'COM_EASYSDI_SHOP_THE_RESOURCE_CANNOT_BE_READ';
            
            $pos = strrpos($diffusion->file, '.');
            $extension = substr($diffusion->file, $pos);
        }elseif (!empty($diffusion->fileurl)){
            $ch = curl_init($diffusion->fileurl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            
            if( ($file=curl_exec($ch)) === false)
                $dlError = curl_error($ch);
            
            curl_close($ch);
            
            $pos = strrpos($diffusion->fileurl, '.');
            $extension = substr($diffusion->fileurl, $pos);
        }elseif (!empty($diffusion->perimeter_id) && null !== $featurecode = $jinput->getHtml('featurecode', null) ){
            $url = str_replace('{CODE}', $featurecode,  $diffusion->packageurl);
            $file = @file_get_contents($url);
            
            //@TODO: retrieve why file_get_contents returns false
            if($file === false)
                $dlError = 'COM_EASYSDI_SHOP_THE_RESOURCE_CANNOT_BE_READ';
            
            $pos = strrpos($url, '.');
            $extension = substr($url, $pos);
        }else{
            $this->setMessage(JText::_('RESOURCE_LOCATION_UNAVAILABLE'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=download&layout='.$layout.'&id=' . $id, false));
            return false;
        }
        
        // If cannot DL the file, it returns an error msg to the client
        if($file === false){
            $this->setMessage(JText::_($dlError), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=download&layout='.$layout.'&id=' . $id, false));
            return false;
        }

        //Record the download for statistic purpose        
        // Insert columns.
        $columns = array('diffusion_id, user_id', 'executed');
        // Insert values.
        if ($sdiUser->isEasySDI):
            $id = $sdiUser->id;
        else:
            $id = null;
        endif;
        $db = JFactory::getDBO();
        $values = array($diffusion->id, $id, $db->quote(date("Y-m-d H:i:s")));
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
        header('Content-Type: application/octetstream; name="' . $extension . '"');
        header('Content-Disposition: attachement; filename="' . $diffusion->name . $extension . '"');

        echo $file;
        die();
    }

}
