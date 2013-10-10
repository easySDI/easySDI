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
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/order.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_shop/tables/orderdiffusion.php';

/**
 * Order controller class.
 */
class Easysdi_shopControllerOrder extends Easysdi_shopController {

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_shop.edit.order.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_shop.edit.order.id', $editId);

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=edit', false));
    }

    /**
     * 
     */
    function download() {
        $diffusion_id = JFactory::getApplication()->input->getInt('id', null, 'int');
        $order_id = JFactory::getApplication()->input->getInt('order', null, 'int');

        if (empty($diffusion_id)):
            $return['ERROR'] = JText::_('COM_EASYSDI_SHOP_ORDER_ERROR_EMPTY_ID');
            echo json_encode($return);
            die();
        endif;

        //Check user right on this order
        $order = JTable::getInstance('order', 'Easysdi_shopTable');
        $order->load($order_id);
        if ($order->user_id != sdiFactory::getSdiUser()->id):
            $return['ERROR'] = JText::_('JERROR_ALERTNOAUTHOR');
            echo json_encode($return);
            die();
        endif;

        //Load order response
        $orderdiffusion = JTable::getInstance('orderdiffusion', 'Easysdi_shopTable');
        $keys = array();
        $keys['order_id'] = $order_id;
        $keys['diffusion_id'] = $diffusion_id;
        $orderdiffusion->load($keys);

        $folder = JFactory::getApplication()->getParams('com_easysdi_shop')->get('orderresponseFolder');
        $file = $folder.'\\'.$order_id.'\\'.$diffusion_id.'\\'.$orderdiffusion->file;


        error_reporting(0);

        ini_set('zlib.output_compression', 0);
        header('Pragma: public');
        header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
        header('Content-Transfer-Encoding: none');
        header("Content-Length: " . filesize($file));
        header('Content-Type: application/octetstream; name="' . $orderdiffusion->file . '"');
        header('Content-Disposition: attachement; filename="' . $orderdiffusion->file . '"');

        readfile($file);
        die();
    }

    function cancel() {
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));
    }

    public function remove() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = JFactory::getApplication();
        $model = $this->getModel('Order', 'Easysdi_shopModel');

        // Get the user data.
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

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
            $app->setUserState('com_easysdi_shop.edit.order.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.order.id');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=edit&id=' . $id, false));
            return false;
        }

        // Attempt to save the data.
        $return = $model->delete($data);

        // Check for errors.
        if ($return === false) {
            // Save the data in the session.
            $app->setUserState('com_easysdi_shop.edit.order.data', $data);

            // Redirect back to the edit screen.
            $id = (int) $app->getUserState('com_easysdi_shop.edit.order.id');
            $this->setMessage(JText::sprintf('Delete failed', $model->getError()), 'warning');
            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_shop&view=order&layout=edit&id=' . $id, false));
            return false;
        }


        // Check in the profile.
        if ($return) {
            $model->checkin($return);
        }

        // Clear the profile id from the session.
        $app->setUserState('com_easysdi_shop.edit.order.id', null);

        // Redirect to the list screen.
        $this->setMessage(JText::_('COM_EASYSDI_SHOP_ITEM_DELETED_SUCCESSFULLY'));
        $menu = & JSite::getMenu();
        $item = $menu->getActive();
        $this->setRedirect(JRoute::_($item->link, false));

        // Flush the data from the session.
        $app->setUserState('com_easysdi_shop.edit.order.data', null);
    }

}