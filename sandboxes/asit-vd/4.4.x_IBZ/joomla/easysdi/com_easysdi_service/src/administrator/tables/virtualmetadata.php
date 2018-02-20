<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/database/sditable.php';

/**
 * virtualmetadata Table class
 */
class Easysdi_serviceTablevirtualmetadata extends sdiTable {

    /**
     * Constructor
     *
     * @param JDatabase A database connector object
     */
    public function __construct(&$db) {
        parent::__construct('#__sdi_virtualmetadata', 'id', $db);
    }

    public function save($src, $orderingFilter = '', $ignore = '') {
        $data = array();

        $data['title'] = $src['title'];
        $data['summary'] = $src['summary'];
        $data['keyword'] = $src['keyword'];
        $data['contactorganization'] = $src['contactorganization'];
        $data['contactname'] = $src['contactname'];
        $data['contactposition'] = $src['contactposition'];
        $data['contactadress'] = $src['contactadress'];
        $data['contactpostalcode'] = $src['contactpostalcode'];
        $data['contactlocality'] = $src['contactlocality'];
        $data['contactstate'] = $src['contactstate'];
        $data['country_id'] = $src['country_id'];
        $data['contactphone'] = $src['contactphone'];
        $data['contactfax'] = $src['contactfax'];
        $data['contactemail'] = $src['contactemail'];
        $data['contacturl'] = $src['contacturl'];
        $data['contactavailability'] = $src['contactavailability'];
        $data['contactinstruction'] = $src['contactinstruction'];
        $data['fee'] = $src['fee'];
        $data['accessconstraint'] = $src['accessconstraint'];
        $data['virtualservice_id'] = $src['id'];
        $data['contacturl'] = $src['contacturl'];

        foreach (array_keys($data) as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        if (!isset($src['inheritedcontact'])) { // see if the checkbox has been submitted
            $data['inheritedcontact'] = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $data['inheritedcontact'] = 1; //else mark the field checked
        }

        if (!isset($src['inheritedtitle'])) { // see if the checkbox has been submitted
            $data['inheritedtitle'] = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $data['inheritedtitle'] = 1; //else mark the field checked
        }

        if (!isset($src['inheritedsummary'])) { // see if the checkbox has been submitted
            $data['inheritedsummary'] = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $data['inheritedsummary'] = 1; //else mark the field checked
        }

        if (!isset($src['inheritedkeyword'])) { // see if the checkbox has been submitted
            $data['inheritedkeyword'] = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $data['inheritedkeyword'] = 1; //else mark the field checked
        }

        if (!isset($src['inheritedfee'])) { // see if the checkbox has been submitted
            $data['inheritedfee'] = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $data['inheritedfee'] = 1; //else mark the field checked
        }

        if (!isset($src['inheritedaccessconstraint'])) { // see if the checkbox has been submitted
            $data['inheritedaccessconstraint'] = 0; // if it has not been submitted, mark the field unchecked
        } else {
            $data['inheritedaccessconstraint'] = 1; //else mark the field checked
        }
        return parent::save($data, $orderingFilter, $ignore);
    }

    public function store($updateNulls = false) {
        return parent::store(true);
    }

    public function loadByVirtualServiceID($virtualservice_id = null) {

        // Initialise the query.
        $query = $this->_db->getQuery(true);
        $query->select('*');
        $query->from($query->quoteName($this->_tbl));
        $query->where($this->_db->quoteName('virtualservice_id') . ' = ' . (int) $virtualservice_id);

        $this->_db->setQuery($query);

        try {
            $row = $this->_db->loadAssoc();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        // Legacy error handling switch based on the JError::$legacy switch.
        // @deprecated  12.1
        if (JError::$legacy && $this->_db->getErrorNum()) {
            $e = new JException($this->_db->getErrorMsg());
            $this->setError($e);
            return false;
        }

        // Check that we have a result.
        if (empty($row)) {
            $e = new JException(JText::_('JLIB_DATABASE_ERROR_EMPTY_ROW_RETURNED'));
            $this->setError($e);
            return false;
        }

        // Bind the object with the row and return.
        return $this->bind($row);
    }

}
