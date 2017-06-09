<?php

/**
 * @version     4.4.5
 * @package     plg_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die;

/**
 * 
 *
 * @package     plg_easysdi_service
 * @subpackage  
 * @since       3.3.0
 */
class plgContentEasysdiservice extends JPlugin {

    /**
     * 
     * @param object $subject
     * @param array $config
     */
    public function __construct($subject, $config) {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    /**
     * Method is called right after the item is saved
     *
     * @param	string		The context of the content passed to the plugin (added in 1.6)
     * @param	object		A JTableContent object
     * @param	bool		If the content is just about to be created
     * @since	2.5
     */
    public function onContentAfterSave($context, $data, $isNew) {
        $options = explode('.', $context);
        if ($options[0] != 'com_easysdi_service' && $options[0] != 'com_easysdi_contact') {
            return true;
        }

        return $this->onContentAfterAction($context, $data, 'UPDATE');
    }

    /**
     * This is fired when the item is published, unpublished, archived, or unarchived from the list view.
     *
     * @param	string		The context of the content passed to the plugin (added in 1.6)
     * @param	array		A list of primary key ids of the content that has changed state
     * @param	string		The value of the state that the content has been changed to.
     * @since	2.5
     */
    public function onContentChangeState($context, $pks, $value) {
        $options = explode('.', $context);
        if ($options[0] != 'com_easysdi_service' && $options[0] != 'com_easysdi_contact') {
            return true;
        }

        foreach ($pks as $pk) {
            $this->onContentIdAfterAction($context, $pk, 'UPDATE');
        }
        return true;
    }

    /**
     *  Method is called right after the item was deleted
     *
     * @param	string	The context for the content passed to the plugin.
     * @param	object	The data relating to the content that was deleted.
     * @return	boolean
     * @since	2.5
     */
    public function onContentAfterDelete($context, $data) {
        $options = explode('.', $context);
        if ($options[0] != 'com_easysdi_service' && $options[0] != 'com_easysdi_contact')
            return true;

        return $this->onContentAfterAction($context, $data, 'DELETE');
    }

    /**
     * Method is called after a catch action (update, delete, state changed)
     * 
     * @param string	The context for the content passed to the plugin.
     * @param object	The data relating to the item.
     * @param string	Operation performed on the item : UPDATE or DELETE
     * @return	boolean
     */
    private function onContentAfterAction($context, $data, $operation) {
        return $this->onContentIdAfterAction($context, $data->id, $operation);
    }

    /**
     * Method is called after a catch action (update, delete, state changed)
     * with the item id as parameter.
     * Perform a call to the EasySDI Proxy Cache Invalidation Servlet with the
     * required parameters to invalidate only the needed regions of the cache.
     * 
     * @param string	The context for the content passed to the plugin.
     * @param int		The data id relating to the item.
     * @param string	Operation performed on the item : UPDATE or DELETE
     * @return	boolean
     */
    private function onContentIdAfterAction($context, $id, $operation) {
//		$entity = "";
//		if ($context == 'com_easysdi_service.policy') {
//			$entity = "SdiPolicy";
//		}
//		else if ($context == 'com_easysdi_service.virtualservice') {
//			$entity = "SdiVirtualservice";
//		}
//		else if ($context == 'com_easysdi_service.physicalservice') {
//			$entity = "SdiPhysicalservice";
//		}
//		else if ($context == 'com_easysdi_contact.user') {
//			$entity = "SdiUser";
//		}
//		else if ($context == 'com_easysdi_contact.organism') {
//			$entity = "SdiOrganism";
//		}
//                else if ($context == "com_easysdi_contact.category") {
//                        $entity = "SdiCategory";
//                }

        $app = JFactory::getApplication();

        $params = JComponentHelper::getParams('com_easysdi_service');
        if (!isset($params)) {
            return true;
        }
        $url = $params->get('proxyurl');
        if (!isset($url)) {
            return true;
        }

        //$url .= "cache?entityclass=".$entity."&id=".$id."&operation=".$operation."&complete=FALSE";
        $url .= "cache?complete=TRUE";
        $user = JFactory::getUser();

        $session = curl_init($url);
        $httpHeader[] = 'Authorization: Basic ' . base64_encode($user->username . ':' . $user->password);
        $httpHeader[] = 'Expect:';
        curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($session, CURLOPT_HEADER, false);
        curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
        $rawresponse = curl_exec($session);
        curl_close($session);
        $response = json_decode($rawresponse);

        $displaymessageinapplication = $this->params->def('displaymessageinapplication', 'both');
        if (
                $displaymessageinapplication == 'both' ||
                ($displaymessageinapplication == 'fe' && $app->isSite()) ||
                ($displaymessageinapplication == 'be' && $app->isAdmin())
        ) {
            if (isset($response) && $response->{"status"} == "OK") {
                $app->enqueueMessage(JText::_('PLG_EASYSDICONTENT_OK_INVALIDATION'), 'notice');
                return true;
            } else {
                if (isset($response->{"message"})) {
                    $app->enqueueMessage(JText::_('PLG_EASYSDICONTENT_ERR_INVALIDATION') . " " . $response->{"message"}, 'error');
                } else {
                    $app->enqueueMessage(JText::_('PLG_EASYSDICONTENT_ERR_PROXY_CONNECTION'), 'warning');
                }
                return false;
            }
        }
    }

}
