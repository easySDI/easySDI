<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

class EText {

    public static function _($guid, $text = 1, $default = '') {
        /*if(is_null($default)){
            $default = JText::_('COM_EASYSDI_CORE_TRANSLATION_NOT_FOUND');
        }*/
        
        $lang = JFactory::getLanguage();
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        switch ($text) {
            case 1:
                $query->select('t.text1 AS text');
                break;
            case 2:
                $query->select('t.text2 AS text');
                break;
            default:
                break;
        }

        $query->from('#__sdi_translation AS t');
        $query->innerJoin('#__sdi_language AS l ON t.language_id = l.id');
        $query->where('t.element_guid = ' . $query->quote($guid) );
        $query->where('l.code = ' . $query->quote($lang->getTag()));

        $db->setQuery($query);
        $textI18n = $db->loadObject();

        if ($textI18n && (!empty($textI18n->text) || empty($default))) {
            return $textI18n->text;
        } else {
            return $default;
        }
    }


}

?>
