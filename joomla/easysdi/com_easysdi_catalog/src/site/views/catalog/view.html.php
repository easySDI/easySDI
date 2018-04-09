<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/SearchHtmlForm.php';

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';

/**
 * View to edit
 */
class Easysdi_catalogViewCatalog extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $params;

    /**
     * Display the view
     */
    public function display($tpl = null) {
        $app = JFactory::getApplication();

        $this->state = $this->get('State');
        $this->item = $this->get('Data');
        $this->params = $app->getParams('com_easysdi_catalog');
        $this->pagination = $this->get('Pagination');
        $this->form = $this->get('Form');
        $this->preview = $app->input->get('preview', '', 'STRING');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     */
    protected function _prepareDocument() {
        $app = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('com_easysdi_catalog_DEFAULT_PAGE_TITLE'));
        }
        $title = $this->params->get('page_title', '');
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

    /**
     * Return a list of result node
     * 
     * @return array
     */
    public function getResults() {
        if (empty($this->item->dom)) {
            return false;
        } else {
            $xpath = new DomXPath($this->item->dom);
            $xpath->registerNamespace('csw', 'http://www.opengis.net/cat/csw/2.0.2');
            $xpath->registerNamespace('gmd', 'http://www.isotc211.org/2005/gmd');
            $nodes = $xpath->query('//csw:SearchResults/gmd:MD_Metadata');

            $results = array();
            foreach ($nodes as $node) {
                $metadata = new cswmetadata();
                $metadata->init($node);
                $metadata->extend($this->item->alias, 'result', $this->preview, 'true', JFactory::getLanguage()->getTag());
                $result = $metadata->applyXSL(array('catalog' => $this->item->alias, 'type' => 'result', 'preview' => $this->preview));

                $results[] = $result;
            }

            return $results;
        }
    }

    /**
     * Return the HTML search form for the current catalog
     * @return type
     */
    public function getSearchForm() {
        $shf = new SearchHtmlForm($this->form);

        $htmlForm = $shf->getForm();

        return $htmlForm;
    }

    /**
     * Return the "no results" page content (a string or article/table content)
     * @param String $configVal values can be:
     * - empty: will return the default string
     * - an integer : will return the content of com_content article with this id
     * - a strig composed like this: "databaseNonPrefixedTable:fieldToGet:idTolookFor" 
     *   will get the content of the specified table/field with the id
     * @return String, simple simple or HTML content for the "no result" page
     */
    public function getEmptyResultContent($configVal = '') {
        // no config, use default string
        if (is_null($configVal) || $configVal == '') {
            return $this->getDefaultEmptyResultContent();
        }
        //with config and direct number, use joomla content article id
        if (is_numeric($configVal)) {
            $table_plan = & JTable::getInstance('Content', 'JTable');
            $table_plan_return = $table_plan->load(array('id' => $configVal));
            if ($table_plan_return) {
                return($table_plan->introtext);
            }
        }
        $expl = explode(":", $configVal);
        if (strlen($expl[0]) > 1 & strlen($expl[1]) > 1 & is_numeric($expl[2]) & !isset($expl[3])) {
            $tableName = $expl[0];
            $theTable = '#__' . $expl[0];
            $theField = $expl[1];
            $theId = (int) $expl[2];
            $theIdField = 'id';

            $db = JFactory::getDbo();

            //table does not exist
            if (!array_search($db->getPrefix() . $tableName, $db->getTableList())) {
                return $this->getDefaultEmptyResultContent();
            }
            $tableCols = $db->getTableColumns($theTable);
            //select field does not exist
            if (!array_key_exists($theField, $tableCols)) {
                return $this->getDefaultEmptyResultContent();
            }
            //id field does not exist
            if (!array_key_exists($theIdField, $tableCols)) {
                return $this->getDefaultEmptyResultContent();
            }
            
            //Try to load content from table
            $query = $db->getQuery(true);
            $query->select($db->escape($theField));
            $query->from($db->escape($theTable));
            $query->where('id=' . $db->escape($theId));
            $db->setQuery($query, 0, 1);
            $content = $db->loadResult();
            if ($content) {
                return $content;
            }
        }

        //fallback
        return $this->getDefaultEmptyResultContent();
    }

    /**
     * Return the default "no results" string
     * @return String
     */
    private function getDefaultEmptyResultContent() {
        return JText::_('COM_EASYSDI_CATALOG_NO_RESULTS');
    }

    public function isAdvanced() {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        if (empty($data)) {
            return false;
        }

        if (isset($data['searchtype']) && $data['searchtype'] == 'advanced') {
            return true;
        } else {
            return false;
        }
    }

}
