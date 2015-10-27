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
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/curl.php';

/**
 * Diffusion controller class.
 */
class Easysdi_shopControllerDownload extends Easysdi_shopController {

    private $sdiUser = null;

    private function stop($url = '', $message = 'COM_EASYSDI_SHOP_DOWNLOAD_NO_RIGHT', $type = 'warning') {
        //If the call came from another client than Joomla :
        //Return an http code 401
        if (!JSession::checkToken()) {
            $this->stopOnUnAuthorize();
            return false;
        }
        //If the call came from Joomla :
        //redirect to the login page
        if (empty($url)) {
            $uri = JUri::getInstance()->toString();
            $u64 = base64_encode($uri);
            $url = '/index.php?option=com_users&view=login&return=' . $u64;
        }
        $this->setMessage(JText::_($message), $type);
        $this->setRedirect(JRoute::_($url, false));
        return false;
    }

    private function stopOnUnAuthorize() {
        header('HTTP/1.1 401 Unauthorized', true, 401);
        die();
    }

    private function common() {
        $app = JFactory::getApplication();
        $db = JFactory::getDBO();

        //Get diffusion object
        $id = $app->input->getInt('id', null);
        $query = $db->getQuery(true)
                ->select('*')
                ->from('#__sdi_diffusion')
                ->where('id = ' . (int) $id);
        $db->setQuery($query);
        $diffusion = $db->loadObject();

        //Get current user
        $this->sdiUser = sdiFactory::getSdiUser();

        //If access scope is not public, check if user is authenticated 
        if ($diffusion->accessscope_id != 1) {
            if (!$this->checkSdiUser()) {
                return false;
            }
        }

        //Check accessscope
        if (!$this->checkAccessScope($diffusion)) {
            return false;
        }

        return $diffusion;
    }

    private function checkSdiUser() {
        if ($this->sdiUser->isEasySDI) {
            return true;
        }
        //Check if basic authentication was made at the call
        $user = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];

        //Check credentials
        if (isset($user) && isset($pw)) {
            $userId = JUserHelper::getUserId($user);

            if (empty($userId)) {
                return $this->stop();
            }
            $JoomlaUser = new JUser($userId);

            if (JUserHelper::verifyPassword($pw, $JoomlaUser->password)) {
                $this->sdiUser = sdiFactory::getSdiUser($userId);
                return true;
            } else {
                return $this->stop();
            }
        } else {
            return $this->stop();
        }
    }

    private function checkAccessScope($diffusion) {
        //check if the user has the right to download
        switch ($diffusion->accessscope_id) {
            case 2: //accesscope by organism's category
                $categories = sdiModel::getAccessScopeCategory($diffusion->guid);
                if (count($categories) == 0)
                    return $this->stop();
                if (!$this->sdiUser->isPartOfCategories($categories))
                    return $this->stop();
                return true;
                break;
            case 3: //accessscope by organism
                $organisms = sdiModel::getAccessScopeOrganism($diffusion->guid);
                $organism = $sdiUser->getMemberOrganisms();
                if (!in_array($organism[0]->id, $organisms))
                    return $this->stop();
                return true;
                break;
            case 4: //accessscope by user
                $users = sdiModel::getAccessScopeUser($diffusion->guid);
                if (!in_array($sdiUser->id, $users))
                    return $this->stop();
                return true;
                break;
            case 1: //accessscope: public
            default:
                return true;
                break;
        }
        return false;
    }

    public function direct() {
        $tmpl = JFactory::getApplication()->input->get('tmpl', null);

        $diffusion = $this->common();
        if ($diffusion === false)
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
        $db = JFactory::getDBO();

        $diffusion = $this->common();
        if ($diffusion === false)
            return false;

        $layout = !empty($diffusion->perimeter_id) ? 'grid' : 'default';

        try {
            //Record the download for statistic purpose        
            $columns = array('diffusion_id', 'user_id', 'executed');
            $userid = ($this->sdiUser->isEasySDI ? $this->sdiUser->id : null);
            $values = array($diffusion->id, $userid, $db->quote(date("Y-m-d H:i:s")));
            $query = $db->getQuery(true)
                    ->insert($db->quoteName('#__sdi_diffusion_download'))
                    ->columns($columns)
                    ->values(implode(',', $values));
            $db->setQuery($query);
            $db->execute();
        } catch (RuntimeException $e) {
            //Statistic can not be recorded but it doesn't mean that the download have to be interrupted
            //TODO : a message has to be sent to the site admin that this record can not be done
        }
        try {
            error_reporting(0);

            if (!empty($diffusion->file)) { //Download local file
                $pos = strrpos($diffusion->file, '.');
                $extension = substr($diffusion->file, $pos);
                $file = @file_get_contents($fileFolder . '/' . $diffusion->file);
                // If cannot DL the file, it returns an error msg to the client
                if ($file === false) {
                    throw new Exception();
                }
                ini_set('zlib.output_compression', 0);
                header('Pragma: public');
                header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
                header('Content-Transfer-Encoding: none');
                header("Content-Length: " . strlen($file));
                header('Content-Type: application/octetstream; name="' . $extension . '"');
                header('Content-Disposition: attachement; filename="' . $diffusion->name . $extension . '"');
                echo $file;
                die();
            } elseif (!empty($diffusion->fileurl)) { //Download remote file
                $this->call($diffusion->fileurl, $diffusion->name);
            } elseif (!empty($diffusion->perimeter_id) && null !== $featurecode = $jinput->getHtml('featurecode', null)) {  //Download remote file by grid
                $url = str_replace('{CODE}', $featurecode, $diffusion->packageurl);
                $this->call($url, $diffusion->name);
            } else { //Download is not well configured
                $this->setMessage(JText::_('RESOURCE_LOCATION_UNAVAILABLE'), 'error');
                $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=download&layout=' . $layout . '&id=' . $id, false));
                return false;
            }
        } catch (Exception $e) {
            //Download cannot be done            
            try {//Remove statistic
                $conditions = array(
                    $db->quoteName('diffusion_id') . ' = ' . $values[0],
                    $db->quoteName('user_id') . ' = ' . $values[1],
                    $db->quoteName('executed') . ' = ' . $values[2]
                );
                $query = $db->getQuery(true)
                        ->delete($db->quoteName('#__sdi_diffusion_download'))
                        ->where($conditions);
                $db->setQuery($query);
                $db->execute();
            } catch (RuntimeException $e) {
                //Statistic can not be deleted, a message has to be sent to the site admin                
            }
            //Return error to client
            $this->setMessage(JText::_('COM_EASYSDI_SHOP_THE_RESOURCE_CANNOT_BE_READ'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=download&layout=' . $layout . '&id=' . $id, false));
            return false;
        }
    }

    /**
     * Perform the call to remote url usin CurlHelper class
     * @param type $url
     * @param type $diffusion
     */
    private function call($url, $name) {
        $curlHelper = new CurlHelper();
        $curldata['url'] = $url;
        $pos = strrpos($url, '.');
        $extension = ($pos) ? substr($url, $pos) : null;
        if ($extension) {
            $curldata['fileextension'] = $extension;
        }
        $curldata['filename'] = $name . $extension;
        $curlHelper->get($curldata);
    }
}
