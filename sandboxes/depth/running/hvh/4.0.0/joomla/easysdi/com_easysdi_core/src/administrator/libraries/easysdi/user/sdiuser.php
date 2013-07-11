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
     * Unique juser
     *
     * @var    integer
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

    function __construct($juser = null) {

        if ($juser == null || $juser == 0)
            throw new Exception('Not an EasySDI user');

        $this->juser = $juser;
        $this->lang = JFactory::getLanguage();

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('u.*')
                ->from('#__sdi_user AS u')
                ->where('u.user_id = ' . $juser)
        ;
        $db->setQuery($query);
        $user = $db->loadObject();

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

    public function isResourceManager() {
        if (isset($this->role[2])) {
            return true;
        }
        return false;
    }

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

    public function getMemberOrganisms() {
        return $this->role[1];
    }

    public function getResourceManagerOrganisms() {
        return $this->role[2];
    }

    public function getMetadataResponsibleOrganisms() {
        return $this->role[3];
    }

    public function getMetadataEditorOrganisms() {
        return $this->role[4];
    }

    public function getDiffusionManagerOrganisms() {
        return $this->role[5];
    }

    public function getPreviewManagerOrganisms() {
        return $this->role[6];
    }

    public function getExtractionResponsibleOrganisms() {
        return $this->role[7];
    }

    public function getOrderEligibleOrganisms() {
        return $this->role[8];
    }

}

?>
