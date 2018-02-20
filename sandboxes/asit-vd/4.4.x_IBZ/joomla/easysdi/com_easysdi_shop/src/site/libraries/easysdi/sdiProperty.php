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

require_once JPATH_SITE. '/components/com_easysdi_shop/libraries/easysdi/sdiPropertyValue.php';

class sdiProperty {

    public $id;
    public $values;
    public $name;

    function __construct($session_property) {
        if (empty($session_property))
            return;

        $this->id = $session_property->id;
        $this->loadData();

        if (!isset($this->values))
            $this->values = array();

        foreach ($session_property->values as $value):
            $this->values[] = new sdiPropertyValue($value);
        endforeach;
    }

    private function loadData() {
        try {
            $lang = JFactory::getLanguage();
            $db = JFactory::getDbo();

            $query = $db->getQuery(true)
                    ->select('t.text1 as name')
                    ->from('#__sdi_translation t')
                    ->innerJoin('#__sdi_property p ON p.guid = t.element_guid')
                    ->where('p.id = ' . (int)$this->id)
                    ->where('t.language_id = (SELECT l.id FROM #__sdi_language l WHERE l.code = ' . $db->quote($lang->getTag()) .')');

            $db->setQuery($query);
            $item = $db->loadObject();
            $params = get_object_vars($item);
            foreach ($params as $key => $value){
                $this->$key = $value;
            }
            
        } catch (JDatabaseException $e) {
            
        }
    }

}

?>
