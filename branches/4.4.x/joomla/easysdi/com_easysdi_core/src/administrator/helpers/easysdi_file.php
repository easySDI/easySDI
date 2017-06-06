<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/helpers/easysdi_core.php';

/**
 * upload helper
 */
class Easysdi_filedHelper {

    /**
     * 
     * @param array $files Array containing files array like in $_FILES superglobal.
     * @param string $target_folder Folder to save unpload file.
     * @param boolean $add_prefix Set true if we must add a prefix.
     * @param string $prefix String to add as prefix.
     * @param boolean $add_suffix Set true if we must add a suffix.
     * @param string $suffix String to add as suffix.
     * @param array $allowed_mime Allowed mime type array. Exemple: 'application/xml'
     * @param array $exclude_mime Exclude mime type array. Exemple: 'application/xml'
     * @param boolean $create_thumbnail Set true if we must create thumbnail.
     * @param string $thumb_path Set thumbnails save path.
     * @param string $thumb_width Set thumbnail width. Heigth is calculated.
     */
    public function upload($files, $target_folder, $target_root_url, $add_prefix = false, $prefix = NULL, $add_suffix = false, $suffix = NULL, array $allowed_mime = array(), array $exclude_mime = array(), $create_thumbnail = false, $thumb_path = NULL, $thumb_root_url = NULL, $thumb_width = NULL) {
        try {
            if (!empty($files)) {

                $response = array();
                foreach ($files as $key => $file) {
                    if (!$this->isAllowedMime($file['type'], $allowed_mime, $exclude_mime)) {
                        $this->rollback($response);
                        throw new Exception(JText::sprintf('COM_EASYSDI_CATALOG_FILE_ERROR_NOT_SUPPORTED',$file['name']));
                    }

                    $full_name = $this->addPrefixSuffix($file['name'], $add_prefix, $prefix, $add_suffix, $suffix);
                    $target_name = $target_folder . '/' . $full_name;

                    if (move_uploaded_file($file['tmp_name'], $target_name)) {
                        $file['target_name'] = $target_name;
                        $file['url'] = $target_root_url . '/' . $full_name;

                        // Create thumbnail
                        if ($create_thumbnail) {
                            $file['thumbnail'] = $this->getThumbnail($target_name, $thumb_path, $thumb_root_url, $thumb_width);
                        }

                        $response[$key] = $file;
                    } else {
                        $this->rollback($response);
                    }
                }
            } else {
                throw new Exception(JText::_('COM_EASYSDI_CATALOG_FILE_ERROR_NO_FILE'));
            }

            return $response;
        } catch (Exception $exc) {
            throw new Exception(JText::_('COM_EASYSDI_CATALOG_FILE_ERROR_FAIL'), 0, $exc);
        }
    }

    /**
     * Rollback an array of file
     * 
     * @param array $files
     * @throws Exception
     */
    private function rollback($files) {
        foreach ($files as $file) {
            if (!unlink($file['target_name'])) {
                throw new Exception(JText::sprintf('COM_EASYSDI_CATALOG_FILE_ERROR_ROLLBACK',$file['target_name']) );
            }
        }
    }

    /**
     * Check if type is allowed. If allowed is null all value exept excude is valide.
     * 
     * @param string $type Exemple: 'application/xml
     * @param array $allowed_mime Allowed mime type array. Exemple: 'application/xml'
     * @param array $exclude_mime Exclude mime type array. Exemple: 'application/xml'
     * @return boolean
     */
    private function isAllowedMime($type, $allowed_mime, $exclude_mime) {
        $allowed = true;

        if (!empty($allowed_mime) && !in_array($type, $allowed_mime)) {
            $allowed = false;
        }

        if (in_array($type, $exclude_mime)) {
            $allowed = false;
        }

        return $allowed;
    }

    /**
     * 
     * Add prefix and suffix to name
     * 
     * @param string $name 
     * @param boolean $add_prefix Set true if we must add a prefix.
     * @param string $prefix String to add as prefix.
     * @param boolean $add_suffix Set true if we must add a suffix.
     * @param string $suffix String to add as suffix.
     * @return string
     */
    public function addPrefixSuffix($name, $add_prefix = false, $prefix = NULL, $add_suffix = false, $suffix = NULL) {
        if ($add_prefix) {
            $name = $this->addPrefix($name, $prefix);
        }

        if ($add_suffix) {
            $name = $this->addSuffixe($name, $suffix);
        }

        return $name;
    }

    /**
     * 
     * Add suffixe to a string name
     * 
     * @param string $name
     * @param string $suffix String to add as suffix.
     * @param string $glue
     * @return string
     */
    public function addSuffixe($name, $suffix = '', $glue = '_') {
        if (empty($suffix)) {
            $suffix = Easysdi_coreHelper::uuid();
        }

        return $name . $glue . $suffix;
    }

    /**
     * Add prefix to a string name
     * 
     * @param string $name
     * @param string $prefix String to add as prefix.
     * @param string $glue
     * @return string
     */
    public function addPrefix($name, $prefix = null, $glue = '_') {
        if (empty($prefix)) {
            $prefix = Easysdi_coreHelper::uuid();
        }

        return $prefix . $glue . $name;
    }

    /**
     * Create a thumbnail from an image file
     * 
     * @param string $img_path
     * @param string $thumb_path
     * @param int $thumb_width
     * @return string thumbnail path
     * @throws Exception
     */
    public function getThumbnail($img_path, $thumb_path, $thumb_root_url, $thumb_width) {
        $mime_icon_folder = JPATH_ADMINISTRATOR . '/components/com_easysdi_core/assets/images/mime';

        $info = pathinfo($img_path);

        try {
            // Create thumb folder if not exist
            if (!file_exists($thumb_path)) {
                mkdir($thumb_path);
            }
            
            if (!extension_loaded('gd')) {
                return $this->getDefaultThumbnail($img_path, $thumb_path, $thumb_root_url);
            }
        } catch (Exception $exc) {
            throw $exc;
        }

        switch (strtolower($info['extension'])) {
            case 'jpg':
            case 'jpeg';
                $img = imagecreatefromjpeg($img_path);
                break;

            case 'gif':
                $img = imagecreatefromgif($img_path);
                break;

            case 'png':
                $img = imagecreatefrompng($img_path);
                break;
            default:
                $img = imagecreatefrompng($mime_icon_folder . '/' . $info['extension'] . '-icon-128x128.png');
                break;
        }

        // original size
        $width = imagesx($img);
        $height = imagesy($img);

        // Thumbnail size
        $new_width = $thumb_width;
        $new_height = floor($height * ( $thumb_width / $width ));

        $tmp_img = imagecreatetruecolor($new_width, $new_height);
        $black = imagecolorallocate($tmp_img, 0, 0, 0);
        imagecolortransparent($tmp_img,$black);
        
        imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        
        // Create thumbnail
        switch (strtolower($info['extension'])) {
            case 'jpg':
            case 'jpeg';
                $success = imagejpeg($tmp_img, $thumb_path . '/' . $info['basename']);
                break;

            case 'gif':
                $success = imagegif($tmp_img, $thumb_path . '/' . $info['basename']);
                break;

            case 'png':
                $success = imagepng($tmp_img, $thumb_path . '/' . $info['basename']);
                break;
            default :
                $success = imagepng($tmp_img, $thumb_path . '/' . $info['extension'] . '-icon-128x128.png');
                $none_image = true;
                break;
        }

        if ($success) {
            if($none_image){
                return $thumb_root_url . '/' . $info['extension'] . '-icon-128x128.png';
            }  else {
                return $thumb_root_url . '/' . $info['basename'];
            }
            
        } else {
            throw new Exception(JText::_('COM_EASYSDI_CATALOG_FILE_ERROR_THUMB_CREATE'));
        }
    }

    public function getDefaultThumbnail($img_path, $thumb_path, $thumb_root_url) {
        $mime_icon_folder = JPATH_ADMINISTRATOR . '/components/com_easysdi_core/assets/images/mime';

        $info = pathinfo($img_path);

        if (copy($mime_icon_folder . '/' . $info['extension'] . '-icon-128x128.png', $thumb_path . '/' . $info['basename'])) {
            return $thumb_root_url . '/' . $info['basename'];
        } else {
            throw new Exception(JText::_('COM_EASYSDI_CATALOG_FILE_ERROR_THUMB_COPY'));
        }
    }

}
