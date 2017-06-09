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

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * order Table class
 */
class Easysdi_processingTableorder extends sdiTable {
    
    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_processing_order', 'id', $db);        
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

       /* //if processing order creation
        if ($array['id']==''){
            //Clean and prepare data
            $jinput = JFactory::getApplication()->input;
            $form = $jinput->get('jform', null, 'ARRAY');
            switch ($array['filestorage']) {
                case 'upload':
                    $array['fileurl'] = null;
                    break;
                case 'url':
                    $array['file'] = null;
                    $array['file_hidden'] = null;
                    break;
            }

            $params = JFactory::getApplication()->getParams('com_easysdi_processing');
            $fileFolder = $params->get('upload_path');
            $maxfilesize = $params->get('maxuploadfilesize', 0);

            //Support for file field: file
            if (isset($_FILES['jform']['name']['file'])):
                jimport('joomla.filesystem.file');
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
                    if (!isset($array['file'])):;
                        //delete existing file
                        if (isset($array['id'])) {
                            $db = JFactory::getDbo();
                            $query = $db->getQuery(true)
                                    ->select($db->quoteName('file'))
                                    ->from('#__sdi_processing_order')
                                    ->where('id = ' . (int) $array['id']);
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
                        if (!JFile::upload($fileTemp, $uploadPath)):
                            JError::raiseWarning(500, JText::_('COM_EASYSDI_PROCESSING_FORM_MSG_DIFFUSION_ERROR_UPLOAD'));
                            return false;
                        endif;
                    endif;
                    $array['file'] = $filename;
                }

            endif;
        }*/
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
        return 'com_easysdi_processing.order.' . (int) $this->$k;
    }

    /**
     * Returns the parrent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
     *
     * @see JTable::_getAssetParentId 
     */
    protected function _getAssetParentId(JTable $table = null, $id = null) {
        // We will retrieve the parent-asset from the Asset-table
        $assetParent = JTable::getInstance('Asset');
        // Default: if no asset-parent can be found we take the global asset
        $assetParentId = $assetParent->getRootId();
        // The item has the component as asset-parent
        $assetParent->loadByName('com_easysdi_processing');
        // Return the found asset-parent-id
        if ($assetParent->id) {
            $assetParentId = $assetParent->id;
        }
        return $assetParentId;
    }

}
