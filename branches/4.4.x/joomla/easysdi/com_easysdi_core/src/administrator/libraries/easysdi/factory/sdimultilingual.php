<?php
/**
 * @version		4.4.0
 * @package     com_easysdi_core
 * @copyright	
 * @license		
 * @author		
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
