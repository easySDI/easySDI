<?php

/**
 * @version     4.0.0
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org§> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_catalog/tables');

require_once JPATH_COMPONENT . '/controller.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormHtmlGenerator.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/DomCswCreator.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiRelation.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiClass.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiAttribute.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiNamespace.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiStereotype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/po/SdiResourcetype.php';

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/sdimetadata.php';

/**
 * Metadata controller class.
 */
class Easysdi_catalogControllerMetadata extends Easysdi_catalogController {

    /**
     *
     * @var JDatabaseDriver
     */
    private $db = null;

    /**
     *
     * @var JSession 
     */
    private $session;
    /**
     *
     * @var DOMDocument 
     */
    private $structure;
    /**
     *
     * @var SdiNamespaceDao 
     */
    private $nsdao;
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';

    function __construct() {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->nsdao = new SdiNamespaceDao();
        $this->structure = new DOMDocument('1.0', 'utf-8');
        $this->structure->loadXML(unserialize($this->session->get('structure')));

        parent::__construct();
    }

    /**
     * Method to check out an item for editing and redirect to the edit form.
     *
     * @since	1.6
     */
    public function edit() {
        $app = JFactory::getApplication();

        // Get the previous edit id (if any) and the current edit id.
        $previousId = (int) $app->getUserState('com_easysdi_catalog.edit.metadata.id');
        $editId = JFactory::getApplication()->input->getInt('id', null, 'array');

        // Set the user id for the user to edit in the session.
        $app->setUserState('com_easysdi_catalog.edit.metadata.id', $editId);

        // Get the model.
        $model = $this->getModel('Metadata', 'Easysdi_catalogModel');

        // Check out the item
        if ($editId) {
            $model->checkout($editId);
        }

        // Check in the previous user.
        if ($previousId) {
            $model->checkin($previousId);
        }

        // Redirect to the edit screen.
        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&view=metadata&layout=edit', false));
    }

    /**
     * Method to save a user's profile data.
     *
     * @return	void
     * @since	1.6
     */
    public function save() {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        $domXpathStr = new DOMXPath($this->structure);
        foreach ($this->nsdao->getAll() as $ns) {
            $domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }

        foreach ($data as $xpath => $value) {
            $xpatharray = explode('#', $xpath);
            if (count($xpatharray) > 1) {
                $query = $this->unSerializeXpath($xpatharray[0]) . '[@locale="#' . $xpatharray[1] . '"]';
            } else {
                $query = $this->unSerializeXpath($xpatharray[0]);
            }
            $element = $domXpathStr->query($query)->item(0);
            if (isset($element)) {
                if ($element->hasAttribute('codeList')) {
                    $element->setAttribute('codeListValue', $value);
                } elseif ($element->hasAttributeNS('http://www.w3.org/1999/xlink', 'href')) {
                    $element->setAttributeNS('http://www.w3.org/1999/xlink','xlink:href', $this->getHref($value));
                } else {
                    $element->nodeValue = $value;
                }
            }
        }

        $this->structure->formatOutput = true;
        $xml = $this->structure->saveXML();

        $smda = new sdiMetadata($data['id']);

        //$smda->update($dcc->getCsw());


        //
        //
//        // Initialise variables.
//        $app = JFactory::getApplication();
//        $model = $this->getModel('Metadata', 'Easysdi_catalogModel');
//
//        // Get the user data.
//        $data = JFactory::getApplication()->input->get('jform', array(), 'array');
//
//        // Validate the posted data.
//        $form = $model->getForm();
//        
//        if (!$form) {
//            JError::raiseError(500, $model->getError());
//            return false;
//        }
//
//        // Validate the posted data.
//        $data = $model->validate($form, $data);
//
//        // Check for errors.
//        if ($data === false) {
//            // Get the validation messages.
//            $errors = $model->getErrors();
//
//            // Push up to three validation messages out to the user.
//            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
//                if ($errors[$i] instanceof Exception) {
//                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
//                } else {
//                    $app->enqueueMessage($errors[$i], 'warning');
//                }
//            }
//
//            // Save the data in the session.
//            $app->setUserState('com_easysdi_catalog.edit.metadata.data', JRequest::getVar('jform'), array());
//
//            // Redirect back to the edit screen.
//            $id = (int) $app->getUserState('com_easysdi_catalog.edit.metadata.id');
//            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&view=metadata&layout=edit&id=' . $id, false));
//            return false;
//        }
//
//        // Attempt to save the data.
//        $return = $model->save($data);
//
//        // Check for errors.
//        if ($return === false) {
//            // Save the data in the session.
//            $app->setUserState('com_easysdi_catalog.edit.metadata.data', $data);
//
//            // Redirect back to the edit screen.
//            $id = (int) $app->getUserState('com_easysdi_catalog.edit.metadata.id');
//            $this->setMessage(JText::sprintf('Save failed', $model->getError()), 'warning');
//            $this->setRedirect(JRoute::_('index.php?option=com_easysdi_catalog&view=metadata&layout=edit&id=' . $id, false));
//            return false;
//        }
//
//
//        // Check in the profile.
//        if ($return) {
//            $model->checkin($return);
//        }
//
//        // Clear the profile id from the session.
//        $app->setUserState('com_easysdi_catalog.edit.metadata.id', null);
//
//        // Redirect to the list screen.
//        $this->setMessage(JText::_('COM_EASYSDI_CATALOG_ITEM_SAVED_SUCCESSFULLY'));
//        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
//
//        // Flush the data from the session.
//        $app->setUserState('com_easysdi_catalog.edit.metadata.data', null);
    }

    function cancel() {

        $this->setRedirect(JRoute::_('index.php?option=com_easysdi_core&view=resources', false));
    }

    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

    private function getHref($guid) {
        $query = $this->db->getQuery(true);
        $query->select('ns.`prefix`, rt.fragment');
        $query->from('#__sdi_resource as r');
        $query->innerJoin('#__sdi_resourcetype as rt ON r.resourcetype_id = rt.id');
        $query->innerJoin('#__sdi_namespace as ns ON rt.fragmentnamespace_id = ns.id');
        $query->where('r.guid = \'' . $guid . '\'');

        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        $href = JComponentHelper::getParams('com_easysdi_catalog')->get('catalogurl');
        $href.= '?request=GetRecordById&service=CSW&version=2.0.2&elementSetName=full&outputschema=csw:IsoRecord&id=' . $guid;
        $href .= '&fragment=' . $result->prefix . '%3A' . $result->fragment;

        return $href;
    }
    
    /**
     * 
     * @param string $language
     * @param string $encoding
     * @return DOMElement[] 
     * 
     */
    private function getHeader($default = 'deu', $encoding = 'utf8') {
        $headers = array();

        $language = $this->structure->createElement('gmd:language');
        $characterString = $this->structure->createElement('gco:CharacterString', $default);
        $language->appendChild($characterString);
        $headers[] = $language;

        $characterSet = $this->structure->createElement('gmd:characterSet');
        $characterSetCode = $this->structure->createElement('gmd:MD_CharacterSetCode');
        $characterSetCode->setAttribute('codeListValue', $encoding);
        $characterSetCode->setAttribute('codeList', 'http://www.isotc211.org/2005/resources/codeList.xml#MD_CharacterSetCode');
        $characterSet->appendChild($characterSetCode);

        $headers[] = $characterSet;

        $locale = $this->structure->createElement('gmd:locale');
        $characterEncoding = $this->structure->createElement('gmd:characterEncoding');
        $characterEncodingSetCode = $this->structure->createElement('gmd:MD_CharacterSetCode', strtoupper($encoding));
        $characterEncodingSetCode->setAttribute('codeListeValue', $encoding);
        $characterEncodingSetCode->setAttribute('codeList', '#MD_CharacterSetCode');
        $characterEncoding->appendChild($characterEncodingSetCode);
        foreach ($this->ldao->get() as $key => $value) {
            if ($value->{'iso639-2T'} != $default) {
                $pt_locale = $this->structure->createElement('gmd:PT_Locale');
                $pt_locale->setAttribute('id', $key);

                $languageCode = $this->structure->createElement('gmd:languageCode');
                $languageCodeChild = $this->structure->createElement('gmd:LanguageCode', $value->value);
                $languageCodeChild->setAttribute('codeListValue', $value->{'iso639-2T'});
                $languageCodeChild->setAttribute('codeList', '#LanguageCode');

                $languageCode->appendChild($languageCodeChild);

                $pt_locale->appendChild($languageCode);
                $pt_locale->appendChild($characterEncoding);

                $locale->appendChild($pt_locale);
            }
        }

        $headers[] = $locale;

        return $headers;
    }

}