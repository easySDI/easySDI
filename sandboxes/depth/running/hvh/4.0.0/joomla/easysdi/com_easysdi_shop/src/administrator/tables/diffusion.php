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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * diffusion Table class
 */
class Easysdi_shopTablediffusion extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_diffusion', 'id', $db);
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param	array		Named array
     * @return	null|string	null is operation was satisfactory, otherwise returns an error
     * @see		JTable:bind
     * @since	1.5
     */
    public function bind($array, $ignore = '') {
        $input = JFactory::getApplication()->input;
        $task = $input->getString('task', '');

        //Support for file field: deposit
        if (isset($_FILES['jform']['name']['deposit'])):
            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.file');
            $file = $_FILES['jform'];

            //Check if the server found any error.
            $fileError = $file['error']['deposit'];
            $message = '';
            if ($fileError > 0 && $fileError != 4) {
                switch ($fileError) :
                    case 1:
                        $message = JText::_('File size exceeds allowed by the server');
                        break;
                    case 2:
                        $message = JText::_('File size exceeds allowed by the html form');
                        break;
                    case 3:
                        $message = JText::_('Partial upload error');
                        break;
                endswitch;
                if ($message != '') :
                    JError::raiseWarning(500, $message);
                    return false;
                endif;
            }
            else if ($fileError == 4) {
                if (isset($array['deposit_hidden'])):;
                    $array['deposit'] = $array['deposit_hidden'];
                endif;
            }
            else {

                //Check for filesize
                $fileSize = $file['size']['deposit'];
                if ($fileSize > 33554432):
                    JError::raiseWarning(500, 'File bigger than 32MB');
                    return false;
                endif;

                //Replace any special characters in the filename
                $filename = explode('.', $file['name']['deposit']);
                $filename[0] = preg_replace("/[^A-Za-z0-9]/i", "-", $filename[0]);

                //Add Timestamp MD5 to avoid overwriting
                $filename = md5(time()) . '-' . implode('.', $filename);
                $uploadPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' . DIRECTORY_SEPARATOR . $filename;
                $fileTemp = $file['tmp_name']['deposit'];

                if (!JFile::exists($uploadPath)):

                    if (!JFile::upload($fileTemp, $uploadPath)):

                        JError::raiseWarning(500, 'Error moving file');

                        return false;

                    endif;

                endif;
                $array['deposit'] = $filename;
            }

        endif;
        //Support for file field: file
        if (isset($_FILES['jform']['name']['file'])):
            jimport('joomla.filesystem.file');
            jimport('joomla.filesystem.file');
            $file = $_FILES['jform'];

            //Check if the server found any error.
            $fileError = $file['error']['file'];
            $message = '';
            if ($fileError > 0 && $fileError != 4) {
                switch ($fileError) :
                    case 1:
                        $message = JText::_('File size exceeds allowed by the server');
                        break;
                    case 2:
                        $message = JText::_('File size exceeds allowed by the html form');
                        break;
                    case 3:
                        $message = JText::_('Partial upload error');
                        break;
                endswitch;
                if ($message != '') :
                    JError::raiseWarning(500, $message);
                    return false;
                endif;
            }
            else if ($fileError == 4) {
                if (isset($array['file_hidden'])):;
                    $array['file'] = $array['file_hidden'];
                endif;
            }
            else {

                //Check for filesize
                $fileSize = $file['size']['file'];
                if ($fileSize > 33554432):
                    JError::raiseWarning(500, 'File bigger than 32MB');
                    return false;
                endif;

                //Replace any special characters in the filename
                $filename = explode('.', $file['name']['file']);
                $filename[0] = preg_replace("/[^A-Za-z0-9]/i", "-", $filename[0]);

                //Add Timestamp MD5 to avoid overwriting
                $filename = md5(time()) . '-' . implode('.', $filename);
                $uploadPath = JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easysdi_shop' . DIRECTORY_SEPARATOR . '/' . DIRECTORY_SEPARATOR . $filename;
                $fileTemp = $file['tmp_name']['file'];

                if (!JFile::exists($uploadPath)):

                    if (!JFile::upload($fileTemp, $uploadPath)):

                        JError::raiseWarning(500, 'Error moving file');

                        return false;

                    endif;

                endif;
                $array['file'] = $filename;
            }

        endif;

        return parent::bind($array, $ignore);
    }

    /**
     * Define a namespaced asset name for inclusion in the #__assets table
     * @return string The asset name 
     *
     * @see JTable::_getAssetName 
     */
    protected function _getAssetName() {
        $k = $this->_tbl_key;
        return 'com_easysdi_shop.diffusion.' . (int) $this->$k;
    }

    /**
     * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @see JTable::_getAssetParentId 
     */
    protected function _getAssetParentId($table = null, $id = null) {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_easysdi_shop');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

}
