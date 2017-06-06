<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_shop
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiBasket.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiExtraction.php';
require_once JPATH_SITE . '/components/com_easysdi_shop/libraries/easysdi/sdiPerimeter.php';

class Easysdi_shopBasketStringParser {

    /**
     * basket from constructor, used for search and replace strings
     * @var sdiBasket  
     */
    private $basket;

    /**
     * associative array of values to search and replace
     * key = text to search
     * value = the replacement value
     * @var string[]  
     */
    private $searchStrings = array();

    /**
     * asdasdas
     * @param sdiBasket $basket
     * @return Easysdi_shopOrderMailParser
     */
    function __construct($basket) {
        if (empty($basket))
            return false;

        $this->basket = $basket;

        $this->populateStringsArray();
        $this->loadStringsFromPlugins();
    }

    /**
     * populates the search and replace array from basket
     */
    private function populateStringsArray() {
        $this->searchStrings['{ORDER_ID}'] = $this->basket->id;
        $this->searchStrings['{ORDER_NAME}'] = $this->basket->name;
        $this->searchStrings['{ORDER_CREATE_DATE}'] = JHtml::date($this->basket->created, JText::_('DATE_FORMAT_LC3'));
        $this->searchStrings['{CLIENT_NAME}'] = $this->basket->sdiUser->name;
        $this->searchStrings['{CLIENT_EMAIL}'] = $this->basket->sdiUser->juser->email;
        $this->searchStrings['{CLIENT_ORG_NAME}'] = isset($this->basket->sdiUser->role[1][0]->name) ? $this->basket->sdiUser->role[1][0]->name : null;
        $this->searchStrings['{THIRD_PARTY_NAME}'] = $this->basket->thirdorganism;
        $this->searchStrings['{MANDATE_REF}'] = $this->basket->mandate_ref;
        $this->searchStrings['{MANDATE_CONTACT}'] = $this->basket->mandate_contact;
        $this->searchStrings['{MANDATE_EMAIL}'] = $this->basket->mandate_email;
        $this->searchStrings['{VALIDATED_DATE}'] = JHtml::date($this->basket->validated_date, JText::_('DATE_FORMAT_LC3'));
        $this->searchStrings['{VALIDATED_REASON}'] = nl2br(htmlspecialchars($this->basket->validated_reason));
        if (is_numeric($this->basket->validated_by)) {
            $validator = new sdiUser($this->basket->validated_by);
            $this->searchStrings['{VALIDATED_BY_NAME}'] = $validator->name;
            $this->searchStrings['{VALIDATED_BY_ORG}'] = isset($validator->role[1][0]->name) ? $validator->role[1][0]->name : null;
            ;
        } else {
            $this->searchStrings['{VALIDATED_BY_NAME}'] = null;
            $this->searchStrings['{VALIDATED_BY_ORG}'] = null;
        }
        $this->searchStrings['{SITE_URL}'] = JURI::base();
        $this->searchStrings['{SITE_NAME}'] = JFactory::getConfig()->get('sitename');
        $this->searchStrings['{SITE_LINK}'] = '<a href="' . JURI::base() . '">' . JFactory::getConfig()->get('sitename') . '</a>';

        /* will be set on call depending on usage */
        $this->searchStrings['{DIFFUSIONS_LIST}'] = null;
        $this->searchStrings['{DIRECT_URL}'] = null;
        $this->searchStrings['{DIRECT_URL_TOKEN}'] = null;
    }

    /**
     * Load the plugins and get more string or replace existing ones
     * Note: plugins must be in 'easysdi_shop_mails' folder and 
     * offer the 'getReplaceStringsArray' public function that returns 
     * and associative array of strings (same structure as $this->searchStrings)
     */
    private function loadStringsFromPlugins() {
        JPluginHelper::importPlugin('easysdi_shop_mail');
        $app = JFactory::getApplication();

        //add strings from each plugin
        $additionalStringsArrays = $app->triggerEvent('getReplaceStringsArray', array('basket' => $this->basket));
        foreach ($additionalStringsArrays as $additionalStrings) {
            foreach ($additionalStrings as $search => $replace) {
                $this->searchStrings[$search] = $replace;
            }
        }
    }

    /**
     * Gets a string list on diffusion, may be restricted by $limitedDiffusionList.
     * By defult, all diffusions are returned
     * @param integer[] $limitedDiffusionList an aray of intergers containing the authorized diffusions
     * @return string HTML list of diffusions
     */
    private function getDiffusionsList($limitedDiffusionList = null) {
        $isLimited = is_array($limitedDiffusionList);

        //temp diffusion array
        $diffusions = array();
        foreach ($this->basket->extractions as $extraction) {
            if (!$isLimited || in_array($extraction->id, $limitedDiffusionList)) {
                $diffusions[] = $extraction->name;
            }
        }

        if (count($diffusions) == 0) {
            return null;
        }

        return '<ul><li>' . implode('</li><li>', $diffusions) . '</li></ul>';
    }

    /**
     * replace all occurences of $searchStrings[] in $strinToReplaceIn
     * @param string $strinToReplaceIn original string
     * @return string The string with all matching occurences replaced
     */
    private function getReplacedString($strinToReplaceIn) {
        return str_replace(array_keys($this->searchStrings), array_values($this->searchStrings), $strinToReplaceIn);
    }

    /**
     * Returns the string with all ocurences replaced with basket values
     * @param type $strinToReplaceIn
     * @return string The string with all matching occurences replaced
     */
    public function getReplacedStringForClient($strinToReplaceIn) {
        $this->searchStrings['{DIRECT_URL}'] = JRoute::_(JURI::base() . 'index.php?option=com_easysdi_shop&view=order&id=' . $this->basket->id);
        $this->searchStrings['{DIRECT_URL_TOKEN}'] = $this->searchStrings['{DIRECT_URL}'] . '&a_token=' . $this->basket->access_token;
        $this->searchStrings['{DIFFUSIONS_LIST}'] = $this->getDiffusionsList();
        return $this->getReplacedString($strinToReplaceIn);
    }

    /**
     * Returns the string with all ocurences replaced with basket values,
     * the links are populated with validation manager information, and eventually tokens
     * @param type $strinToReplaceIn
     * @param type $ValidatorId
     * @return string The string with all matching occurences replaced
     */
    public function getReplacedStringForValidator($strinToReplaceIn, $ValidatorId) {
        $this->searchStrings['{DIRECT_URL}'] = JRoute::_(JURI::base() . 'index.php?option=com_easysdi_shop&view=order&layout=validation&id=' . $this->basket->id . '&vm=' . (int) $ValidatorId);
        $this->searchStrings['{DIRECT_URL_TOKEN}'] = $this->searchStrings['{DIRECT_URL}'] . '&v_token=' . $this->basket->validation_token;
        $this->searchStrings['{DIFFUSIONS_LIST}'] = $this->getDiffusionsList();
        return $this->getReplacedString($strinToReplaceIn);
    }

    /**
     * Returns the string with all ocurences replaced with basket values,
     * the diffusion is limited by the ids passed as a parameter
     * (a supplier has to get only his diffusions)
     * @param string $strinToReplaceIn original string
     * @param integer[] $limitedDiffusionList an aray of intergers containing the authorized diffusions
     * @return string The string with all matching occurences replaced
     */
    public function getReplacedStringForSupplier($strinToReplaceIn, $limitedDiffusionList) {
        $this->searchStrings['{DIRECT_URL}'] = JRoute::_(JURI::base() . 'index.php?option=com_easysdi_shop&view=request&id=' . $this->basket->id);
        $this->searchStrings['{DIRECT_URL_TOKEN}'] = $this->searchStrings['{DIRECT_URL}'];
        $this->searchStrings['{DIFFUSIONS_LIST}'] = $this->getDiffusionsList($limitedDiffusionList);
        return $this->getReplacedString($strinToReplaceIn);
    }
    
    /**
     * Returns the string with all ocurences replaced with basket values,
     * the diffusion is limited by the ids passed as a parameter
     * (a supplier has to get only his diffusions)
     * @param string $strinToReplaceIn original string
     * @param integer[] $limitedDiffusionList an aray of intergers containing the authorized diffusions
     * @return string The string with all matching occurences replaced
     */
    public function getReplacedStringForOTP($strinToReplaceIn, $otp) {
        $this->searchStrings['{OTP}'] = $otp;
        return $this->getReplacedString($strinToReplaceIn);
    }

}
