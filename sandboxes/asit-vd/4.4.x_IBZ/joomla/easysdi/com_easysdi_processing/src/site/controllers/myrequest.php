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

/**
 * MyRequest controller class.
 */
class Easysdi_processingControllerMyRequest extends Easysdi_processingController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_processing.edit.myrequest.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the id to edit in the session.
        $app->setUserState('com_easysdi_processing.edit.myrequest.id', $editId);
        
        // Get the model.
        $model = $this->getModel('MyRequest', 'Easysdi_processingModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequest&layout=edit', false));
       
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
        $return = true;
        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Myrequest', 'Easysdi_processingModel');
        $db = JFactory::getDbo();

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        // Validate the posted data.
        $form = $model->getForm();

        if (!$form) {
            JError::raiseError(500, $model->getError());
            $return = false;
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
            $app->setUserState('com_easysdi_processing.edit.myrequest.data', JRequest::getVar('jform'), array());

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_processing.edit.myrequest.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequest&layout=edit&id=' . $id, false));
            $return = false;
        }else{
            //if results
            //Clean and prepare data
            $jinput = JFactory::getApplication()->input;
            $form = $jinput->get('jform', null, 'ARRAY');
            
            $params = JFactory::getApplication()->getParams('com_easysdi_processing');
            $fileFolder = $params->get('output_path');
            $maxfilesize = $params->get('maxuploadfilesize', 0);

            //Support for file field: file
            if (isset($_FILES['jform']['name']['output'])):
                jimport('joomla.filesystem.file');
                $file = $_FILES['jform'];

                //Check if the server found any error.
                $fileError = $file['error']['output'];
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
                        $return=false;
                    endif;
                }
                else if ($fileError == 4) {
                    if (!isset($data['output'])){
                        //delete existing file
                        if (isset($data['id'])) {
                            $db = JFactory::getDbo();
                            $query = $db->getQuery(true)
                                    ->select($db->quoteName('output'))
                                    ->from('#__sdi_processing_order')
                                    ->where('id = ' . (int) $data['id']);
                            $db->setQuery($query);
                            $file = $db->loadResult();
                            if (!empty($file)) {
                                $filePath = $fileFolder . '/' .$data['id']. '/'. $file;
                                if (JFile::exists($filePath))
                                    JFile::delete($filePath);
                            }
                        }
                    }
                }
                else {
                    //Check for filesize
                    $fileSize = $file['size']['output'];
                    if ($fileSize > $maxfilesize * 1048576):
                        JError::raiseWarning(500, JText::sprintf('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_SERVER_SIZE_MAX', $maxfilesize));
                        $return=false;
                    endif;

                    //Replace any special characters in the filename
                    $filename = explode('.', $file['name']['output']);
                    $filename[0] = preg_replace("/[^A-Za-z0-9]/i", "-", $filename[0]);

                    //Add Timestamp MD5 to avoid overwriting
                    $filename = implode('.', $filename);
                    $filePath = $fileFolder . '/' .$data['id']. '/'. $filename;
                    $fileTemp = $file['tmp_name']['output'];

                    if (!JFile::exists($filePath)):
                        if (!JFile::upload($fileTemp, $filePath,false,true)):
                            JError::raiseWarning(500, JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD'));
                            $return=false;
                        endif;
                    endif;
                    $data['output'] = $filename;
                    $data['sent'] = date("Y-m-d H:i:s");
                }

            endif;
            //Support for file field: file
            if (isset($_FILES['jform']['name']['outputpreview'])):
                jimport('joomla.filesystem.file');
                $file = $_FILES['jform'];

                //Check if the server found any error.
                $fileError = $file['error']['outputpreview'];
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
                        $return=false;
                    endif;
                }
                else if ($fileError == 4) {
                    
                    if (!isset($data['outputpreview'])){
                        //delete existing file
                        if (isset($data['id'])) {
                            
                            $query = $db->getQuery(true)
                                    ->select($db->quoteName('outputpreview'))
                                    ->from('#__sdi_processing_order')
                                    ->where('id = ' . (int) $data['id']);
                            $db->setQuery($query);
                            $file = $db->loadResult();
                            if (!empty($file)) {
                                $filePath = $fileFolder . '/' .$data['id']. '/'. $file;
                                if (JFile::exists($filePath))
                                    JFile::delete($filePath);
                            }
                        }
                    }
                }
                else {
                    //Check for filesize
                    $fileSize = $file['size']['outputpreview'];
                    if ($fileSize > $maxfilesize * 1048576):
                        JError::raiseWarning(500, JText::sprintf('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_SERVER_SIZE_MAX', $maxfilesize));
                        $return=false;
                    endif;

                    //Replace any special characters in the filename
                    $filename = explode('.', $file['name']['outputpreview']);
                    $filename[0] = preg_replace("/[^A-Za-z0-9]/i", "-", $filename[0]);

                    //Add Timestamp MD5 to avoid overwriting
                    $filename = implode('.', $filename);
                    $filePath = $fileFolder . '/' .$data['id']. '/'. $filename;
                    $fileTemp = $file['tmp_name']['outputpreview'];

                    if (!JFile::exists($filePath)):
                        if (!JFile::upload($fileTemp, $filePath,false,true)):
                            JError::raiseWarning(500, JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD'));
                            $return=false;
                        endif;
                    endif;
                    $data['outputpreview'] = $filename;
                }

            endif;
            
        }
        
        if ($return)
            $return = $model->save($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_processing.edit.myrequest.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequest&layout=edit&id=' . $return, false));
            return false;
        }
        
        
         //Send Email to the customer
        $model = $this->getModel('Myorder', 'Easysdi_processingModel');
        $order = $model->getData($data['id']);
        $sdiUser = sdiFactory::getSdiUser($order->user_id);

        //If the status of the processing is fail, send an email with the informations
        if ($data['status']=='fail') {

            if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDKO_SUBJECT'), JText::sprintf('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDKO_BODY', $order->name, $data['info']))):
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ERROR_MESSAGE'));
            endif;

            //Send Email to the processing observers
            $query = $db->getQuery(true);
            $query->select('sdi_user_id')
                    ->from('#__sdi_processing_obs')
                    ->where('processing_id = ' . (int) $order->processing_id);
            $db->setQuery($query);
            $users = $db->loadObjectList();

            foreach ($users as $user) :
                $sdiUser = sdiFactory::getSdiUser($user->sdi_user_id);
                if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDKO_OBSERVER_SUBJECT'), JText::sprintf('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDKO_OBSERVER_BODY',  $data['name'],$sdiUser->name, $processing_type))):
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ERROR_MESSAGE'));
                endif;
            endforeach;
        }else{
            if ($data['status']=='done') {
                //If the status of the processing is done, send an email with the link to the order
                $url_order = JURI::root().'index.php?option=com_easysdi_processing&view=myorder&id=' . $order->id;

                if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDOK_SUBJECT'), JText::sprintf('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDOK_BODY', $order->name, $url_order))):
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ERROR_MESSAGE'));
                endif;

                //Send Email to the processing observers
                $query = $db->getQuery(true);
                $query->select('sdi_user_id')
                        ->from('#__sdi_processing_obs')
                        ->where('processing_id = ' . (int) $order->processing_id);
                $db->setQuery($query);
                $users = $db->loadObjectList();

                foreach ($users as $user) :
                    $sdiUser = sdiFactory::getSdiUser($user->sdi_user_id);
                    if (!$sdiUser->sendMail(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDOK_OBSERVER_SUBJECT'), JText::sprintf('COM_EASYSDI_PROCESSING_SEND_MAIL_ORDER_PROCESSEDOK_OBSERVER_BODY', $data['name'], $sdiUser->name, $processing_type))):
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_PROCESSING_SEND_MAIL_ERROR_MESSAGE'));
                    endif;
                endforeach;
            }
        }
       


        if (!$andclose) {
            // Redirect back to the edit screen.
            $app->setUserState('com_easysdi_processing.edit.myrequest.data', null);
            $this->setMessage(JText::_('COM_EASYSDI_PROCESSING_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequest&layout=edit&id=' . $return, false));
        } else {
            // Check in the profile.
            if ($return) {
                $model->checkin($return);
            }

            // Flush the data from the session.
            $this->clearSession();

            // Redirect to the list screen.
            $this->setMessage(JText::_('COM_EASYSDI_PROCESSING_ITEM_SAVED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequests', false));
        }
    }

    function cancel() {
        // Flush the data from the session.
        $this->clearSession();
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequests', false));
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
            $this->setMessage(JText::_('COM_EASYSDI_CORE_ITEM_DELETED_SUCCESSFULLY'));
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequests', false));
        } catch (Exception $exc) {
            $db->transactionRollback();
            $this->setMessage(JText::_('Error : ') . $exc->getMessage(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_processing&view=myrequests', false));
            return false;
        }
    }

    function apply() {
        $this->save(false);
    }

    function clearSession() {
        $app = JFactory::getApplication();
        // Clear the id from the session.
        $app->setUserState('com_easysdi_processing.edit.myrequest.id', null);
        $app->setUserState('com_easysdi_processing.edit.myrequest.processing.id', null);
        // Flush the data from the session.
        $app->setUserState('com_easysdi_processing.edit.myrequest.ur', null);
        $app->setUserState('com_easysdi_processing.edit.myrequest.data', null);
    }


     private static function fileResponse($file) {
        if (!file_exists($file)) return JError::raiseWarning(404, JText::_('JERROR_AN_ERROR_HAS_OCCURRED'));

        $file_info=Easysdi_processingHelper::getFileInfo($file);
        $result = file_get_contents($file);
        $headers= array(
        'Content-Type'=>$file_info['mime_type'],
        'Content-Disposition'=>'attachment; filename="'.$file_info['basename'].'"',
        );

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
        $inputs= $jinput->getArray(array('order_id'=>'int', 'type'=>'word', 'file'=>'string'));

        $order_model=$this->getModel('myorder');
        $order=$order_model->getItem($inputs['order_id']);
        $user_roles=Easysdi_processingHelper::getCurrentUserRolesOnData($order);
        if (in_array('creator',$user_roles) || in_array('creator',$user_roles) ) {
            if ($inputs['type']=='output') {
                $file_path=JComponentHelper::getParams('com_easysdi_processing')->get('output_path');
            } else {
                $file_path=JComponentHelper::getParams('com_easysdi_processing')->get('upload_path');
            }
            $file_path.=$inputs['order_id'].'/'.$inputs['file'];
            return self::fileResponse($file_path);
        } else {
            return JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
        }
    }

}