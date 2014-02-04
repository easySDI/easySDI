<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EText
 *
 * @author Administrator
 */
class EText {

    public static function _($guid, $text = 1, $default = 'Translation not found') {
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
        $query->where('t.element_guid = "' . $guid . '"');
        $query->where('l.code = "' . $lang->getTag() . '"');

        $db->setQuery($query);
        $textI18n = $db->loadObject();

        if ($textI18n) {
            return $textI18n->text;
        } else {
            return $default;
        }
    }


}

?>
