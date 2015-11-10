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
     * Unique perimeter
     *
     * @var    varchar
     */
    public $perimeter = null;

    /**
     * Unique juser
     *
     * @var    JUser
     */
    public $juser = null;

    /**
     * Unique user
     *
     * @var    Object
     */
    public $user = null;

    /**
     * Unique role
     *
     * @var    array
     */
    public $role = null;

    /**
     * Member organism categories
     *
     * @var    array
     */
    public $orgCategoriesIds = null;
    
    public $organismsCategories = null;

    /**
     * @var boolean
     */
    public $isEasySDI = true;

    /**
     * Unique lang
     *
     * @var    
     */
    public $lang = null;

    /**
     * EasySDI user roles
     * 
     */
    const member = 1;
    const resourcemanager = 2;
    const metadataresponsible = 3;
    const metadataeditor = 4;
    const diffusionmanager = 5;
    const viewmanager = 6;
    const extractionresponsible = 7;
    const pricingmanager = 9;
    const validationmanager = 10;

    /**
     * 
     * @param interger $juser Joomla user identifier
     * @throws Exception
     */
    function __construct($sdiId = null) {
        if (!empty($sdiId)):
            $user = $this->getUserById($sdiId);
        else:
            $user = $this->getCurrentUser();
        endif;

        $this->lang = JFactory::getLanguage()->getTag();

        if (!$user) {
            $this->isEasySDI = false;
        } else {
            $this->id = $user->id;
            $this->user = $user;
            $this->perimeter = $user->perimeter;

            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('uro.role_id as  role_id, o.name as organism_name, o.id as organism_id, o.guid as organism_guid')
                    ->from('#__sdi_user_role_organism  uro')
                    ->innerJoin('#__sdi_organism o ON o.id = uro.organism_id')
                    ->where('uro.user_id = ' . (int) $this->id)
            ;
            $db->setQuery($query);
            $roles = $db->loadObjectList();

            // populates roles
            $this->role = array();
            foreach ($roles as $role) {
                if (!isset($this->role[$role->role_id]))
                    $this->role[$role->role_id] = array();
                $organism = new stdClass();
                $organism->id = $role->organism_id;
                $organism->name = $role->organism_name;
                $organism->guid = $role->organism_guid;
                array_push($this->role[$role->role_id], $organism);
            }

            //populates organim's categories, if member of an organism
            if(isset($this->role[self::member][0])){
                $query = $db->getQuery(true)
                        ->select('c.*')
                        ->from('#__sdi_organism_category AS oc')
                        ->innerJoin('#__sdi_category AS c ON c.id=oc.category_id')
                        ->where('oc.organism_id='.(int)$this->role[self::member][0]->id);
                $db->setQuery($query);
                $this->organismsCategories = $db->loadObjectList();
                
                $this->orgCategoriesIds = array();
                foreach($this->organismsCategories as $category)
                    array_push($this->orgCategoriesIds, $category->id);
            }
        }
    }

    private function getUserById($sdiId) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('u.*, o.perimeter, juser.id as jid')
                ->from('#__sdi_user AS u')
                ->innerJoin("#__sdi_user_role_organism uro ON uro.user_id=u.id")
                ->innerJoin("#__sdi_organism o ON o.id = uro.organism_id")
                ->innerJoin("#__users juser ON juser.id = u.user_id")
                ->where("uro.role_id = 1")
                ->where('u.id = ' . (int) $sdiId)
        ;
        $db->setQuery($query);
        $user = $db->loadObject();

        $this->juser = JFactory::getUser($user->jid);
        $this->name = $this->juser->name;

        return $user;
    }

    private function getCurrentUser() {

        $this->juser = JFactory::getUser();
        $this->name = $this->juser->name;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('u.*, o.perimeter')
                ->from('#__sdi_user AS u')
                ->innerJoin("#__sdi_user_role_organism uro ON uro.user_id=u.id")
                ->innerJoin("#__sdi_organism o ON o.id = uro.organism_id")
                ->where("uro.role_id = 1")
                ->where('u.user_id = ' . (int) $this->juser->id)
        ;
        $db->setQuery($query);
        return $db->loadObject();
    }

    /**
     * 
     * @return boolean
     */
    public function isResourceManager() {
        if (!$this->isEasySDI) {
            return false;
        }
        if (isset($this->role[2])) {
            return true;
        }
        return false;
    }

    /**
     * Get the resource type the user can manage
     * @return array
     */
    public function getResourceType() {
        if (!$this->isEasySDI) {
            return null;
        }
        $db = JFactory::getDbo();

        $cls = '(rt.accessscope_id = 1 
                            OR ((rt.accessscope_id = 3) AND (' . $this->id . ' IN (select a.user_id from #__sdi_accessscope a where a.entity_guid = rt.guid)))';

        foreach ($this->role[2] as $organism):
            $cls .= 'OR ((rt.accessscope_id = 2) AND (';
            $cls .= $organism->id . ' in (select a.organism_id from #__sdi_accessscope a where a.entity_guid = rt.guid)';
            $cls .= '))';
        endforeach;

        $categories = $this->getMemberOrganismsCategoriesIds();
        foreach ($categories as $cat):
            $cls .= 'OR ((rt.accessscope_id = 4) AND (';
            $cls .= $cat . ' in (select a.category_id from #__sdi_accessscope a where a.entity_guid = rt.guid)';
            $cls .= '))';
        endforeach;

        $cls .= '
                 )';

        $query = $db->getQuery(true)
                ->select('rt.id as id, rt.name as name, t.text1 as label')
                ->from('#__sdi_resourcetype rt')
                ->innerJoin('#__sdi_translation t ON t.element_guid = rt.guid')
                ->innerJoin('#__sdi_language l ON l.id = t.language_id')
                ->where('l.code = ' . $db->quote($this->lang))
                ->where('rt.predefined = 0')
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
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[1];
    }
    
    public function getMemberOrganismsCategories(){
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->organismsCategories;
    }

    /**
     * Get the organism's categories the user is member of
     * @return type
     */
    public function getMemberOrganismsCategoriesIds() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->orgCategoriesIds;
    }

    /**
     * Get the Organisms for which the user is resource manager
     * @return type
     */
    public function getResourceManagerOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[2];
    }

    /**
     * Get the Organisms for which the user is metadata responsible
     * @return type
     */
    public function getMetadataResponsibleOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[3];
    }

    /**
     * Get the Organisms for which the user is metadata editor
     * @return type
     */
    public function getMetadataEditorOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[4];
    }

    /**
     * Get the Organisms for which the user is diffusion manager
     * @return type
     */
    public function getDiffusionManagerOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[5];
    }

    /**
     * Get the Organisms for which the user is preview manager
     * @return type
     */
    public function getPreviewManagerOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[6];
    }

    /**
     * Get the Organisms for which the user is extraction responsible
     * @return type
     */
    public function getExtractionResponsibleOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[7];
    }

    /**
     * Get the Organisms for which the user is pricing manager
     * @return type
     */
    public function getPricingManagerOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[self::pricingmanager];
    }

    /**
     * Get the Organisms for which the user is pricing manager
     * @return type
     */
    public function getTPValidationManagerOrganisms() {
        if (!$this->isEasySDI) {
            return null;
        }
        return $this->role[self::validationmanager];
    }

    /**
     * Get if a user has the specific right on the specific item, or all the right on an item
     * @param int $item
     * @param int $right
     * @return mixed
     */
    public function authorize($item, $right = null) {
        if (!$this->isEasySDI) {
            return false;
        }
        if (is_null($item))
            return $this->isResourceManager();

        if ($right == null) {
            //Return all rights on the item
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('urr.role_id')
                    ->from('#__sdi_user_role_resource urr')
                    ->where('urr.user_id = ' . (int) $this->id)
                    ->where('urr.resource_id = ' . (int) $item);
            $db->setQuery($query);
            return $db->loadObjectList();
        } else {
            //Return if the user has the specific right on the specific item
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('urr.id')
                    ->from('#__sdi_user_role_resource urr')
                    ->where('urr.user_id = ' . (int) $this->id)
                    ->where('urr.resource_id = ' . (int) $item)
                    ->where('urr.role_id = ' . (int) $right);
            $db->setQuery($query);
            $result = $db->loadObject();
            if ($result != null)
                return true;
            else
                return false;
        }
    }

    /**
     * Is the user authorized to do action on the metadata specified by the given id
     * @param int $item
     * @param int $right
     * @return boolean
     */
    public function authorizeOnMetadata($item, $right = null) {
        if (!$this->isEasySDI) {
            return false;
        }
        if (is_null($item))
            return false;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('v.resource_id')
                ->from('#__sdi_version v')
                ->innerJoin('#__sdi_metadata m ON m.version_id = v.id')
                ->where('m.id = ' . (int) $item);
        $db->setQuery($query);

        return $this->authorize($db->loadResult(), $right);
    }

    /**
     * Is the user authorized to do action on the version specified by the given id
     * @param int $item
     * @param string $right
     * @return boolean
     */
    public function authorizeOnVersion($item, $right = null) {
        if (!$this->isEasySDI) {
            return false;
        }

        if (is_null($item))
            return false;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('v.resource_id')
                ->from('#__sdi_version v')
                ->where('v.id = ' . (int) $item);
        $db->setQuery($query);

        return $this->authorize($db->loadResult(), $right);
    }

    /**
     * Is the user authorized to view or preview the item
     * @param int $item    
     * @return boolean
     */
    public function canView($item) {
        if (is_null($item))
            return false;

        $db = JFactory::getDbo();

        if (!$this->isEasySDI) {
            //Not an EasySDI user, usually it means current user is a Joomla guest (not logged user)
            $cls = 'v.accessscope_id = 1';
        } else {
            $cls = '(v.accessscope_id = 1 
                                OR ((v.accessscope_id = 3) AND (' . $this->id . ' IN (select a.user_id from #__sdi_accessscope a where a.entity_guid = v.guid)))';

            foreach ($this->role[1] as $organism):
                $cls .= 'OR ((v.accessscope_id = 2) AND (';
                $cls .= $organism->id . ' in (select a.organism_id from #__sdi_accessscope a where a.entity_guid = v.guid)';
                $cls .= '))';
            endforeach;

            $categories = $this->getMemberOrganismsCategoriesIds();
            foreach ($categories as $cat):
                $cls .= 'OR ((v.accessscope_id = 4) AND (';
                $cls .= $cat . ' in (select a.category_id from #__sdi_accessscope a where a.entity_guid = v.guid)';
                $cls .= '))';
            endforeach;

            $cls .= ')';
        }



        $query = $db->getQuery(true)
                ->select('v.id')
                ->from('#__sdi_visualization v')
                ->where($cls)
                ->where('v.id = ' . (int) $item);
        $db->setQuery($query);
        $result = $db->loadResult();

        return ($result) ? true : false;
    }

    /**
     * Is the user authorized to download or order the item
     * @param int $item    
     * @return boolean
     */
    public function canGet($item) {
        if (is_null($item))
            return false;

        return true;
    }

    /**
     * Send mail to current user
     * @param type $subject
     * @param type $body
     * @return boolean
     */
    public function sendMail($subject, $body) {
        //Get mailer
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();

        //Get sender
        $sender = array(
            $config->get('config.mailfrom'),
            $config->get('config.fromname'));
        $mailer->setSender($sender);

        //Get recipient
        $recipient = $this->juser->email;
        $mailer->addRecipient($recipient);

        //Create the mail content
        $body = $body;
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $mailer->isHTML(TRUE);

        //Send the mail
        $send = $mailer->Send();
        if ($send !== true) {
            return false;
        } else {
            return true;
        }
    }

    public function getResponsibleExtraction() {
        if (!$this->isEasySDI) {
            return false;
        }
        $db = JFactory::getDbo();

        $query = $db->getQuery(true)
                ->select('d.id')
                ->from('#__sdi_user_role_resource urr')
                ->innerJoin('#__sdi_version v ON v.resource_id = urr.resource_id')
                ->innerJoin('#__sdi_diffusion d ON d.version_id = v.id')
                ->where('urr.user_id = ' . (int) $this->id)
                ->where('urr.role_id = 7');
        $db->setQuery($query);

        return $db->loadColumn();
    }

}

?>
