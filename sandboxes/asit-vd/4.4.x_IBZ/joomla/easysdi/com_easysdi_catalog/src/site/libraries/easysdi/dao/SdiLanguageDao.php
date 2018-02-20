<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiDao.php';

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class SdiLanguageDao extends SdiDao{

    public function getAll(){
        
        return array_merge($this->getDefault(), $this->getSupported());
    }
    
    /**
     * 
     * @return array A table containing the list of supported languages. 
     * The key of array is the language code.
     */
    public function getSupported() {
        $supportedIds = JComponentHelper::getParams('com_easysdi_catalog')->get('languages');
        
        if(!isset($supportedIds) || (sizeof($supportedIds)==0)){
            return array();
        }
        
        $languageIds = implode(',', $supportedIds);

        $query = $this->db->getQuery(true);
        
        $query->select('*');
        $query->from('#__sdi_language l');
        $query->where('id IN (' . $languageIds . ')');
        $query->innerJoin('#__languages ls ON ls.lang_code=l.code');

        $this->db->setQuery($query);
        $languages = $this->db->loadObjectList('iso3166-1-alpha2');

        return $languages;
    }
    
    /**
     * 
     * @return stdClass
     */
    public function getDefault(){
        $languageId = JComponentHelper::getParams('com_easysdi_catalog')->get('defaultlanguage');
        
        $query = $this->db->getQuery(true);
        
        $query->select('*');
        $query->from('#__sdi_language l');
        $query->where('id = '.(int)$languageId);
        $query->innerJoin('#__languages ls ON ls.lang_code=l.code');

        $this->db->setQuery($query);
        $languages = $this->db->loadObjectList('iso3166-1-alpha2');


        return $languages;
    }
    
    public function getDefaultLanguage(){
        $languageId = JComponentHelper::getParams('com_easysdi_catalog')->get('defaultlanguage');
        
        $query = $this->db->getQuery(true);
        
        $query->select('*');
        $query->from('#__sdi_language l');
        $query->where('id = '.(int)$languageId);
        $query->innerJoin('#__languages ls ON ls.lang_code=l.code');

        $this->db->setQuery($query);
        $language = $this->db->loadObject();

        return $language;
    }

    public function getByCode($code) {
        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_language l');
        $query->where('code = ' . $query->quote($code) );
        $query->innerJoin('#__languages ls ON ls.lang_code=l.code');
        $this->db->setQuery($query);
        $language = $this->db->loadObject();
        
        return $language;
        
    }
    
    public function getByIso639($code){
        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_language l');
        $query->where($this->db->quoteName('iso639-2T'). ' = ' . $query->quote($code) );
        $query->innerJoin('#__languages ls ON ls.lang_code=l.code');
        $this->db->setQuery($query);
        
        $language = $this->db->loadObject();
        
        return $language;
    }
    
    public function getByIso3166($code){
        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_language l');
        $query->where($this->db->quoteName('iso3166-1-alpha2') . ' = ' . $query->quote($code) );
        $query->innerJoin('#__languages ls ON ls.lang_code=l.code');
        $this->db->setQuery($query);
        
        $language = $this->db->loadObject();
        
        return $language;
    }

}

?>
