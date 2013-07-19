<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class sdiUser {

    /**
     * Unique id
     *
     * @var    integer
     */
    public $id = null;
    
    /**
     * Unique name
     *
     * @var    varchar
     */
    public $name = null;

    /**
     * Unique juser
     *
     * @var    JUser
     */
    public $juser = null;

    /**
     * Unique role
     *
     * @var    array
     */
    public $role = null;

    /**
     * Unique lang
     *
     * @var    object
     */
    public $lang = null;
    
    const member = 1;
    const resourcemanager = 2;
    const metadataresponsible = 3;
    const metadataeditor = 4;
    const diffusionmanager = 5;
    const viewmanager = 6;
    const extractionresponsible = 7;
    const ordereligible = 8;
    

    /**
     * 
     * @param interger $juser Joomla user identifier
     * @throws Exception
     */
    function __construct($juser = null) {

        if ($juser == null)
            throw new Exception('Not an EasySDI user');

        $this->juser = $juser;
        $this->name = $juser->name;
        $this->lang = JFactory::getLanguage();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('u.*')
                ->from('#__sdi_user AS u')
                ->where('u.user_id = ' . $juser->id)
        ;
        $db->setQuery($query);
        $user = $db->loadObject();

        if(!$user)
            throw new Exception('Not an EasySDI user');
        
        $this->id = $user->id;

        $query = $db->getQuery(true)
                ->select('uro.role_id as  role_id, o.name as organism_name, o.id as organism_id')
                ->from('#__sdi_user_role_organism  uro')
                ->innerJoin('#__sdi_organism o ON o.id = uro.organism_id')
                ->where('uro.user_id = ' . $this->id)
        ;
        $db->setQuery($query);
        $roles = $db->loadObjectList();

        $this->role = array();
        foreach ($roles as $role) {
            if (!isset($this->role[$role->role_id]))
                $this->role[$role->role_id] = array();
            $organism = new stdClass();
            $organism->id = $role->organism_id;
            $organism->name = $role->organism_name;
            array_push($this->role[$role->role_id], $organism);
        }
    }

    /**
     * 
     * @return boolean
     */
    public function isResourceManager() {
        if (isset($this->role[2])) {
            return true;
        }
        return false;
    }

    /**
     * Get the resource type the user can manage
     * @return type
     */
    public function getResourceType() {
        $db = JFactory::getDbo();

        $cls = '(rt.accessscope_id = 1 
                            OR ((rt.accessscope_id = 3) AND (' . $this->id . ' IN (select a.user_id from sdi_sdi_accessscope a where a.entity_guid = rt.guid)))';

        foreach ($this->role[2] as $organism):
            $cls .= 'OR ((rt.accessscope_id = 2) AND (';
            $cls .= $organism->id . ' in (select a.organism_id from sdi_sdi_accessscope a where a.entity_guid = rt.guid)';
            $cls .= '))';
        endforeach;

        $cls .= '
                 )';

        $query = $db->getQuery(true)
                ->select('rt.id as id, rt.name as name, t.text1 as label')
                ->from('#__sdi_resourcetype rt')
                ->innerJoin('#__sdi_translation t ON t.element_guid = rt.guid')
                ->innerJoin('#__sdi_language l ON l.id = t.language_id')
                ->where('l.code = "' . $this->lang->getTag() . '"')
                ->where($cls)
        ;
        $db->setQuery($query);
        $resourcetypes = $db->loadObjectList();

        return $resourcetypes;
    }

    /**
     * Get the organism the user is member of
     * @return type
     */
    public function getMemberOrganisms() {
        return $this->role[1];
    }

    /**
     * Get the Organisms for which the user is resource manager
     * @return type
     */
    public function getResourceManagerOrganisms() {
        return $this->role[2];
    }

    /**
     * Get the Organisms for which the user is metadata responsible
     * @return type
     */
    public function getMetadataResponsibleOrganisms() {
        return $this->role[3];
    }

    /**
     * Get the Organisms for which the user is metadata editor
     * @return type
     */
    public function getMetadataEditorOrganisms() {
        return $this->role[4];
    }

    /**
     * Get the Organisms for which the user is diffusion manager
     * @return type
     */
    public function getDiffusionManagerOrganisms() {
        return $this->role[5];
    }

    /**
     * Get the Organisms for which the user is preview manager
     * @return type
     */
    public function getPreviewManagerOrganisms() {
        return $this->role[6];
    }

    /**
     * Get the Organisms for which the user is extraction responsible
     * @return type
     */
    public function getExtractionResponsibleOrganisms() {
        return $this->role[7];
    }

    /**
     * Get the Organisms for which the user is allowed to order
     * @return type
     */
    public function getOrderEligibleOrganisms() {
        return $this->role[8];
    }
    
    /**
     * Get if a user has the specific right on the specific item, or all the right on an item
     * @return type
     */
    public function authorize ($item, $right=null){
        if(is_null($item))
            return false;
        
        if($right == null){
            //Return all rights on the item
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('urr.role_id')
                ->from('#__sdi_user_role_resource urr')
                ->where('urr.user_id = '.$this->id)
                ->where('urr.resource_id = '.$item);
            $db->setQuery($query);
            return $db->loadObjectList();
        }else{
            //Return if the user has the specific right on the specific item
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select('urr.id')
                ->from('#__sdi_user_role_resource urr')
                ->where('urr.user_id = '.$this->id)
                ->where('urr.resource_id = '.$item)
                ->where('urr.role_id = '.$right);
            $db->setQuery($query);
            $result = $db->loadObject();
            if($result != null)
                return true;
            else 
                return false;
        }
    }
    
    /**
     * Is the user authorized to do action on the metadata specified by the given id
     * @param type $item
     * @param type $right
     * @return boolean
     */
    public function authorizeOnMetadata ($item, $right=null){
        if(is_null($item))
            return false;
        
        $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select ('v.resource_id')
                    ->from('#__sdi_version v')
                    ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                    ->where('m.id = '.$item);
            $db->setQuery($query);
         
        return $this->authorize($db->loadResult(), $right);
    }
    
    /**
     * Is the user authorized to do action on the version specified by the given id
     * @param type $item
     * @param type $right
     * @return boolean
     */
    public function authorizeOnVersion ($item, $right=null){
        if(is_null($item))
            return false;
        
        $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select ('v.resource_id')
                    ->from('#__sdi_version v')
                    ->where('v.id = '.$item);
            $db->setQuery($query);
         
        return $this->authorize($db->loadResult(), $right);
    }

}

?>
