<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

class sdiPropertyValue {
    public $id;
    public $value;
    public $name;
        
    function __construct($session_value) {
        if (empty($session_value))
            return;

        $this->id = $session_value->id;
        $this->value = $session_value->value;
        if($this->id != 0)
            $this->loadData();
        
    }
    
    private function loadData() {
        try {
            $lang = JFactory::getLanguage();
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->select('t.text1 as name')
                    ->from('#__sdi_translation t')
                    ->innerJoin('#__sdi_propertyvalue p ON p.guid = t.element_guid')
                    ->where('p.id = ' . (int)$this->id)
                    ->where('t.language_id = (SELECT l.id FROM #__sdi_language l WHERE l.code = ' . $db->quote($lang->getTag()) . ')')
                    ->group('t.text1');
            

            $db->setQuery($query);
            $item = $db->loadObject();
            if(isset($item)){
                $params = get_object_vars($item);

                foreach ($params as $key => $value){
                    $this->$key = $value;
                }
            }
            
            
        } catch (JDatabaseException $e) {
            
        }
    }
}

?>
