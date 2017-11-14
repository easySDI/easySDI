<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/
// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_processing/helpers/easysdi_processing.php';
jimport( 'joomla.filesystem.file' );

/**
 * MyOrder controller class.
 */
class Easysdi_processingControllerMyOrder extends Easysdi_processingController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        //$previousId = (int) $app->getUserState('com_easysdi_processing.edit.myorder.id');
        //$editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the id to edit in the session.
        //$app->setUserState('com_easysdi_processing.edit.myorder.id', $editId);
        $app->setUserState('processing.id', $app->input->get('processing', '', 'INT'));

        // Get the model.
        $model = $this->getModel('Myorder', 'Easysdi_processingModel');

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorder&layout=edit&processing='.$app->input->get('processing', '', 'INT'), false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save($andclose = true) {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Myorder', 'Easysdi_processingModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        //var_dump($app->input->files); die();

        // Validate the posted data.
        $form = $model->getForm();

        if (!$form) {
            JError::raiseError(500, $model->getError());
            return false;
        }

        // Validate the posted data.
        $data = $model->validate($form, $data);

        // Check for errors.
        if ($data === false) {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
                if ($errors[$i] instanceof Exception) {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                } else {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_easysdi_processing.edit.myorder.data', JRequest::getVar('jform'), array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_processing.edit.myorder.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorder&layout=edit&id=' . $id, false));
            return false;
        }

        //Rebuild complete url if storage is URL
        if($data['filestorage'] == 'url'){
            $data['fileurl'] = Easysdi_processingHelper::unparse_url(parse_url($data['fileurl']), array(
                'user' => $data['userurl'],
                'pass' => $data['passurl']
            ));
            unset($data['userurl'], $data['passurl']);
        }else{
             //if processing order creation
            if ($data['id']==''){
                //Clean and prepare data
                $jinput = JFactory::getApplication()->input;
                $form = $jinput->get('jform', null, 'ARRAY');
                switch ($data['filestorage']) {
                    case 'upload':
                        $data['fileurl'] = null;
                        break;
                    case 'url':
                        $data['file'] = null;
                        $data['file_hidden'] = null;
                        break;
                }

                $params = JFactory::getApplication()->getParams('com_easysdi_processing');
                $fileFolder = $params->get('upload_path');
                $maxfilesize = $params->get('maxuploadfilesize', 0);
                jimport('joomla.filesystem.file');

                //Support for file field: file
                if (isset($_FILES['jform']['name']['file'])):

                    $file = $_FILES['jform'];

                    //Check if the server found any error.
                    $fileError = $file['error']['file'];
                    $message = '';
                    if ($fileError > 0 && $fileError != 4) {
                        switch ($fileError) :
                            case 1:
                                $message = JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_SERVER_SIZE_MAX');
                                break;
                            case 2:
                                $message = JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_HTML_SIZE_MAX');
                                break;
                            case 3:
                                $message = JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD');
                                break;
                        endswitch;
                        if ($message != '') :
                            JError::raiseWarning(500, $message);
                            return false;
                        endif;
                    }
                    else if ($fileError == 4) {
                        if (!isset($data['file'])):;
                            //delete existing file
                            if (isset($data['id'])) {
                                $db = JFactory::getDbo();
                                $query = $db->getQuery(true)
                                        ->select($db->quoteName('file'))
                                        ->from('#__sdi_processing_order')
                                        ->where('id = ' . (int) $data['id']);
                                $db->setQuery($query);
                                $file = $db->loadResult();
                                if (!empty($file)) {
                                    $uploadPath = $fileFolder . '/'. $file;
                                    if (JFile::exists($uploadPath))
                                        JFile::delete($uploadPath);
                                }
                            }
                        endif;
                    }
                    else {
                        //Check for filesize
                        $fileSize = $file['size']['file'];
                        if ($fileSize > $maxfilesize * 1048576):
                            JError::raiseWarning(500, JText::sprintf('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_SERVER_SIZE_MAX', $maxfilesize));
                            return false;
                        endif;

                        //Replace any special characters in the filename
                        $filename = explode('.', $file['name']['file']);
                        $filename[0] = preg_replace("/[^A-Za-z0-9]/i", "-", $filename[0]);

                        //Add Timestamp MD5 to avoid overwriting
                        $filename = md5(time()) . '-' . implode('.', $filename);
                        $uploadPath = $fileFolder . '/'. $filename;
                        $fileTemp = $file['tmp_name']['file'];

                        if (!JFile::exists($uploadPath)):
                            if (!JFile::upload($fileTemp, $uploadPath,false,true)):
                                JError::raiseWarning(500, JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD'));
                                return false;
                            endif;
                        endif;
                        $data['file'] = $filename;
                    }

                endif;

                //Support for other file fields
                foreach($_FILES as $pfile => $pdata){
                    //Upload des fichiers en paramètre
                    if ($pfile <> 'jform'){
                        //$name=$pdata['name'];
                        $file = $_FILES[$pfile];

                        //Check if the server found any error.
                        $fileError = $file['error'];
                        $message = '';
                        if ($fileError > 0 && $fileError != 4) {
                            switch ($fileError) :
                                case 1:
                                    $message = JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_SERVER_SIZE_MAX');
                                    break;
                                case 2:
                                    $message = JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_HTML_SIZE_MAX');
                                    break;
                                case 3:
                                    $message = JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD');
                                    break;
                            endswitch;
                            if ($message != '') :
                                JError::raiseWarning(500, $message);
                                return false;
                            endif;
                        }
                        else {
                            //Check for filesize
                            $fileSize = $file['size'];
                            if ($fileSize > $maxfilesize * 1048576):
                                JError::raiseWarning(500, JText::sprintf('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_SERVER_SIZE_MAX', $maxfilesize));
                                return false;
                            endif;

                            //Replace any special characters in the filename
                            $filename = explode('.', $file['name']);
                            $filename[0] = preg_replace("/[^A-Za-z0-9]/i", "-", $filename[0]);

                            //Add Timestamp MD5 to avoid overwriting
                            $filename = md5(time()) . '-' . implode('.', $filename);
                            $uploadPath = $fileFolder . '/'. $filename;
                            $fileTemp = $file['tmp_name'];

                            if (!JFile::exists($uploadPath)):
                                if (!JFile::upload($fileTemp, $uploadPath,false,true)):
                                    JError::raiseWarning(500, JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD'));
                                    return false;
                                endif;
                            endif;
                            $data['parameters'] = str_replace($file['name'],$filename,$data['parameters']);
                        }
                    }
                }
            }
        }

        $user = sdiFactory::getSdiUser();
        $data['user_id']=$user->id;
        // Attempt to save the data.
        $return = $model->save($data);


        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_processing.edit.myorder.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorder&layout=edit&id=' . $return, false));
            return false;
        }

        //Send Email to the processing manager
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('contact_id')
                ->from('#__sdi_processing')
                ->where('id = ' . (int) $data['processing_id']);
        $db->setQuery($query);
        $processing_manager = $db->loadResult();

        $query = $db->getQuery(true);
        $query->select('name')
                ->from('#__sdi_processing')
                ->where('id = ' . (int) $data['processing_id']);
        $db->setQuery($query);
        $processing_type = $db->loadResult();

        $current_user=sdiFactory::getSdiUser();

        $sdiUser = sdiFactory::getSdiUser($processing_manager);

        $url_request = JURI::root().'index.php?option=com_easysdi_processing&view=myrequest&task=myrequest.edit&id=' . $model->getState('myorder.id');

        if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_DONE_SUBJECT'), JText::sprintf('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_DONE_BODY', $current_user->name, $data['name'], $processing_type, $url_request))):
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ERROR_MESSAGE'));
        endif;

        //Send Email to the processing observers
        $query = $db->getQuery(true);
        $query->select('sdi_user_id')
                ->from('#__sdi_processing_obs')
                ->where('processing_id = ' . (int) $data['processing_id']);
        $db->setQuery($query);
        $users = $db->loadObjectList();

        foreach ($users as $user) :
            $sdiUser = sdiFactory::getSdiUser($user->sdi_user_id);
            if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_DONE_OBSERVER_SUBJECT'), JText::sprintf('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_DONE_OBSERVER_BODY', $current_user->name, $data['name'], $processing_type))):
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ERROR_MESSAGE'));
            endif;
        endforeach;


        if (!$andclose) {
            // Redirect back to the edit screen.
            $app->setUserState('com_easysdi_processing.edit.myorder.data', null);
            $this->setMessage(JText::_('COM_EASYSDI_PROCESSING_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorder&layout=edit&id=' . $return, false));
        } else {
            // Check in the profile.
            if ($return) {
                $model->checkin($return);
            }

            // Flush the data from the session.
            $this->clearSession();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_PROCESSING_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorders', false));
        }


    }



    function cancel() {
        // Flush the data from the session.
        $this->clearSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorders', false));
    }

    public function remove() {
        $app = JFactory::getApplication();
        // Initialise variables.
        $db = JFactory::getDbo();
        $model = $this->getModel('Myorder', 'Easysdi_processingModel');

        // Get the user data.
        $data = array();
        $data['id'] = JFactory::getApplication()->input->get('order_id', null, 'int');

        $order = $model->getData($data['id']);
        
        $params = JFactory::getApplication()->getParams('com_easysdi_processing');
            $fileFolder = $params->get('upload_path');
            $outputFolder=$params->get('output_path');
            
            $filepath = $fileFolder . '/'. $order->file;
            
            if (JFile::exists($filepath))
                JFile::delete($filepath);
            
            $filepath = $outputFolder . '/' .$data['id'] . '/' . $order->output;
            
            if (JFile::exists($filepath))
                JFile::delete($filepath);
            
            $filepath = $outputFolder . '/' . $data['id'] . '/' . $order->outputpreview;
            
            if (JFile::exists($filepath))
                JFile::delete($filepath);

        try {
            try {
                $db->transactionStart();
            } catch (Exception $exc) {
                $db->connect();
                $driver_begin_transaction = $db->name . '_begin_transaction';
                $driver_begin_transaction($db->getConnection());
            }
            $model->delete($data['id']);
            $db->transactionCommit();
            
            

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_PROCESSING_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorders', false));
        } catch (Exception $exc) {
            $db->transactionRollback();
            $this->setMessage(JText::_('Error : ') . $exc->getMessage(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myorders', false));
            return false;
        }
    }

     function apply() {
        $this->save(false);
    }

    function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_processing.edit.myorder.id', null);
        $app->setUserState('com_easysdi_processing.edit.myorder.processing.id', null);
        // Flush the data from the session.
        $app->setUserState('com_easysdi_processing.edit.myorder.ur', null);
        $app->setUserState('com_easysdi_processing.edit.myorder.data', null);
    }


     private static function fileResponse($file) {
        if (!file_exists($file)) return JError::raiseWarning(404, JText::_('JERROR_AN_ERROR_HAS_OCCURRED'));

        $file_info=Easysdi_processingHelper::getFileInfo($file);
        $result = file_get_contents($file);
        $headers['Content-Type']=$file_info['mime_type'];
        $headers['Content-Disposition']='attachment; filename="'.$file_info['basename'].'"';

        JResponse::clearHeaders();
        foreach ($headers as $key => $value) {
            JResponse::setHeader($key, $value, true);
        }

        JResponse::sendHeaders();
        echo $result;
        JFactory::getApplication()->close();
    }

    public function proxy()
    {

        $jinput = JFactory::getApplication()->input;
        $inputs= $jinput->getArray(array('order_id'=>'int', 'type'=>'word', 'file'=>'string', 'access_key'=>'string'));

        $order_model=$this->getModel('myorder');
        $order=$order_model->getItem($inputs['order_id']);
        $user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);
        $private_access=($order->access_key!==null && $order->access_key==$inputs['access_key']);


        if (in_array('creator',$user_roles) || in_array('contact',$user_roles) || $private_access || $inputs['type']=='field' ) {
            if (($inputs['type']=='output') || ($inputs['type']=='outputpreview')) {
                $file_path=JComponentHelper::getParams('com_easysdi_processing')->get('output_path');

                    $file_path.= '/' . $order->id.'/'.basename($inputs['file']);
            } elseif($inputs['type']=='field') {
                $file_path=JComponentHelper::getParams('com_easysdi_processing')->get('upload_path');
                $file_path.= '/'.basename($inputs['file']);

             } else {
                if ($order->filestorage == 'url')
                {
                    $ch = curl_init($order->fileurl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                    if( ($file=curl_exec($ch)) === false)
                        $dlError = curl_error($ch);

                    curl_close($ch);
                    if($file === false){
                        $this->setMessage(JText::_($dlError), 'error');
                        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=download&layout='.$layout.'&id=' . $id, false));
                        return false;
                    }
                    ini_set('zlib.output_compression', 0);
                    header('Pragma: public');
                    header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
                    header('Content-Transfer-Encoding: none');
                    header("Content-Length: " . strlen($file));
                    header('Content-Type: application/octetstream; name="' . pathinfo($order->fileurl, PATHINFO_EXTENSION) . '"');
                    header('Content-Disposition: attachement; filename="' . pathinfo($order->fileurl, PATHINFO_BASENAME) . '"');
                    echo $file;

                    die();
                }else{
                    $file_path=JComponentHelper::getParams('com_easysdi_processing')->get('upload_path');
                    $file_path.= '/'.$order->file;
                }
            }
            return self::fileResponse($file_path);
        } else {
            return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
        }
    }

}