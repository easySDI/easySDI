<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * EasySDI
 *
 * @since    4.0.0
 */
abstract class sdiMultilingual 
{
    /**
	 
	 * @since   4.0.0
	 */
	public static function getTranslation($guid, $lang = null)
	{
            return sdiMultilingual::translate($guid, $lang, 'text1');
	}
        
        public static function getAlternateTranslation($guid, $lang = null)
	{
            return sdiMultilingual::translate($guid, $lang, 'text2');
	}
        
        private static function translate ($guid, $lang, $field){
            if($guid == null)
                return null;
            
            if($lang == null)
                $lang = JFactory::getLanguage();
            
            $db = JFactory::getDbo();
            
            $query = $db->getQuery(true)
                    ->select('t.'.$field)
                    ->from('#__sdi_translation t')
                    ->where('t.element_guid='. $db->quote($guid))
                    ->where('t.language_id = (SELECT l.id FROM #__sdi_language l WHERE l.code = ' . $db->quote($lang->getTag()) . ')');
            
            $db->setQuery($query);
            return $db->loadResult();
        }
        
}
?>
