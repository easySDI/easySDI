<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumLayerName.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumServiceConnector.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRelationScope.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';

/**
 * This Class will browse the xml structure in session and create the tree fielset.
 *
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class FormHtmlGenerator {

    /**
     *
     * @var JForm
     */
    private $form;

    /**
     *
     * @var DOMDocument
     */
    private $structure;

    /**
     *
     * @var SdiLanguageDao
     */
    private $ldao;

    /**
     *
     * @var SdiNamespaceDao
     */
    private $nsdao;

    /**
     *
     * @var DOMDocument
     */
    private $formHtml;

    /**
     *
     * @var DOMXPath
     */
    private $domXpathStr;

    /**
     *
     * @var DOMXPath
     */
    private $domXpathFormHtml;

    /**
     *
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     *
     * @var string
     */
    private $ajaxXpath;
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';
    private $catalog_prefix = 'catalog';
    
    /**
     * Roles
     * @var boolean
     */
    private $isNotOnlyOrganismManager = false;

    function __construct(JForm $form, DOMDocument $structure, $ajaxXpath = null) {
        $this->form = $form;
        $structure->preserveWhiteSpace = false;
        $this->structure = $structure;
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->formHtml = new DOMDocument(null, 'utf-8');
        $this->ajaxXpath = $ajaxXpath;
        $this->db = JFactory::getDbo();
        
        $this->user = new sdiUser();
        $this->userParams = json_decode($this->user->juser->params);

        //roles
        $metadata_id = $form->getData()->get('id');
        
        $this->isNotOnlyOrganismManager =  $ajaxXpath != null
                                        || $this->user->authorizeOnMetadata($metadata_id, sdiUser::metadataresponsible)
                                        || $this->user->authorizeOnMetadata($metadata_id, sdiUser::metadataeditor);
    }

    /**
     * This function returns the form in HTML.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     * @return string
     */
    public function buildForm() {
        $this->domXpathStr = new DOMXPath($this->structure);
        $this->domXpathFormHtml = new DOMXPath($this->formHtml);

        foreach ($this->nsdao->getAll() as $ns) {
            $this->domXpathStr->registerNamespace($ns->prefix, $ns->uri);
        }

        if (isset($this->ajaxXpath)) {
            $root = $this->domXpathStr->query($this->ajaxXpath)->item(0);
            switch ($root->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$ATTRIBUT:
                    $this->formHtml->appendChild($this->getAttribute($root));
                    $html = $this->formHtml->saveHTML();

                    return $html;
                    break;

                default:
                    $rootFieldset = $this->getFieldset($root);
                    break;
            }
        } else {
            $root = $this->domXpathStr->query('/*')->item(0);
            $rootFieldset = $this->formHtml->createElement('fieldset');
        }

        $this->formHtml->appendChild($rootFieldset);

        $this->recBuildForm($root, $rootFieldset);

        $this->formHtml->formatOutput = true;
        $html = $this->formHtml->saveHTML();

        return $html;
    }

    /**
     * This function recursively browse the XML structure to create the HTML form.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $parent Parent element in the XML structure.
     * @param DOMElement $parentHtml Parent element in the HTML structure.
     */
    private function recBuildForm(DOMElement $parent, DOMElement $parentHtml) {
        switch ($parent->parentNode->nodeType) {
            case XML_DOCUMENT_NODE:
                $query = '*[@catalog:childtypeId="0"]|*[@catalog:childtypeId="2"]|*[@catalog:childtypeId="3"]';
                $parentInner = $parentHtml;
                break;
            case XML_ELEMENT_NODE:
                $query = '*/*[@catalog:childtypeId="0"]|*/*[@catalog:childtypeId="2"]|*/*[@catalog:childtypeId="3"]|*[@catalog:childtypeId="2"]';
                if ($parent->getAttributeNS($this->catalog_uri, 'exist') == 1) {
                    $parentInner = $parentHtml->getElementsByTagName('div')->item(0);
                } else {
                    $parentInner = $parentHtml;
                }

                break;
        }

        if (isset($_GET['relid'])) {
            switch ($parent->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$RELATIONTYPE:
                    $searchField = $this->getAttribute($parent);
                    $parentHtml->getElementsByTagName('div')->item(0)->appendChild($searchField);
                    break;
            }
        }

        foreach ($this->domXpathStr->query($query, $parent) as $index => $child) {
            $exist = $child->getAttributeNS($this->catalog_uri, 'exist');

            switch ($child->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$RELATION:
                    if ($child->getAttributeNS($this->catalog_uri, 'index') == 1 && $this->isNotOnlyOrganismManager) {
                        $action = $this->getAction($child);
                        $parentInner->appendChild($action);
                    }
                    $fieldset = $this->getFieldset($child);

                    $parentInner->appendChild($fieldset);

                    if ($this->domXpathStr->query('*/*[@catalog:childtypeId="0"]|*/*[@catalog:childtypeId="2"]|*[@catalog:childtypeId="2"]', $child)->length > 0) {
                        $this->recBuildForm($child, $fieldset);
                    }
                    break;
                case EnumChildtype::$ATTRIBUT:
                    if ($child->getAttributeNS($this->catalog_uri, 'stereotypeId') == EnumStereotype::$GEMET) {
                        if ($child->getAttributeNS($this->catalog_uri, 'index') == 1) {
                            $field = $this->getAttribute($child);
                            $parentInner->appendChild($field);
                        }
                    } else {
                        $field = $this->getAttribute($child);
                        $parentInner->appendChild($field);
                    }

                    break;
                case EnumChildtype::$RELATIONTYPE:
                    if ($child->getAttributeNS($this->catalog_uri, 'index') == 1 && $this->isNotOnlyOrganismManager) {
                        $action = $this->getAction($child);
                        $parentInner->appendChild($action);
                    }

                    $fieldset = $this->getFieldset($child);

                    $searchField = $this->getAttribute($child);
                    if ($fieldset->getElementsByTagName('div')->length > 0) {
                        $fieldset->getElementsByTagName('div')->item(0)->appendChild($searchField);
                    }

                    /* if ($this->domXpathStr->query('descendant::*[@catalog:childtypeId="2"]', $child)->length > 0) {
                      $searchField = $this->getAttribute($child);
                      $fieldset->getElementsByTagName('div')->item(0)->appendChild($searchField);
                      } */


                    $parentInner->appendChild($fieldset);

                    if ($this->domXpathStr->query('descendant::*[@catalog:childtypeId="2"]', $child)->length > 0) {
                        $this->recBuildForm($child, $fieldset);
                    }
                    break;
            }
        }
    }

    /**
     * Built on the action bar to add new relation instance.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $relation Current relation.
     * @return DOMElement The DIV block that contains the label and the button "add" if necessary.
     */
    private function getAction(DOMElement $relation) {


        $lowerbound = $relation->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $relation->getAttributeNS($this->catalog_uri, 'upperbound');
        $exist = $relation->getAttributeNS($this->catalog_uri, 'exist');
        $level = $relation->getAttributeNS($this->catalog_uri, 'level');
        $guid = $relation->getAttributeNS($this->catalog_uri, 'id');

        switch ($relation->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                $relid = $relation->getAttributeNS($this->catalog_uri, 'relationId');
                break;

            default:
                $relid = $relation->getAttributeNS($this->catalog_uri, 'dbid');
                break;
        }

        $aAdd = $this->formHtml->createElement('a');
        $aAdd->setAttribute('id', 'add-btn' . FormUtils::serializeXpath($this->removeIndex($relation->getNodePath())) . '-' . $guid);
        $aAdd->setAttribute('class', 'btn btn-success btn-mini add-btn');
        $aAdd->setAttribute('data-relid', $relid);
        $aAdd->setAttribute('data-parentpath', FormUtils::serializeXpath($relation->parentNode->getNodePath()));
        $aAdd->setAttribute('data-lowerbound', $lowerbound);
        $aAdd->setAttribute('data-upperbound', $upperbound);

        $iAdd = $this->formHtml->createElement('i');
        $iAdd->setAttribute('class', 'icon-white icon-plus-2');

        $divOuter = $this->formHtml->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds' . FormUtils::serializeXpath($this->removeIndex($relation->getNodePath())));
        $divOuter->setAttribute('class', 'outer-' . $level);

        $divAction = $this->formHtml->createElement('div', EText::_($relation->getAttributeNS($this->catalog_uri, 'id')));
        $divAction->setAttribute('class', 'action');

        if ($firstChild = $this->getFirstNonTextChild($relation->childNodes)) {

            if (!$firstChild->getAttributeNS($this->catalog_uri, 'stereotypeId') == EnumStereotype::$GEOGRAPHICEXTENT) {
                $aAdd->appendChild($iAdd);
                $divAction->appendChild($aAdd);
            }
        } else {
            $aAdd->appendChild($iAdd);
            $divAction->appendChild($aAdd);
        }

        return $divAction;
    }

    /**
     * Built fieldset corresponding to a relation instance.
     * This method also creates the "Close" button and, if necessary, the "Delete" button.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $element
     * @return DOMElement A fieldset containing the necessary buttons.
     */
    private function getFieldset(DOMElement $element) {
        $lowerbound = $element->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $element->getAttributeNS($this->catalog_uri, 'upperbound');
        $guid = $element->getAttributeNS($this->catalog_uri, 'id');

        $exist = $element->getAttributeNS($this->catalog_uri, 'exist');

        $legendAttribute = $element->getAttributeNS($this->catalog_uri, 'legend');
        $level = $element->getAttributeNS($this->catalog_uri, 'level');
        $scopeId = $element->getAttributeNS($this->catalog_uri, 'scopeId');
        
//        $stereotypeId = $element->firstChild->getAttributeNS($this->catalog_uri, 'stereotypeId');

        $aCollapse = $this->formHtml->createElement('a');
        $aCollapse->setAttribute('id', 'collapse-btn' . FormUtils::serializeXpath($element->getNodePath()));
        $aCollapse->setAttribute('class', 'btn btn-mini collapse-btn');

        $iCollapse = $this->formHtml->createElement('i');
        $iCollapse->setAttribute('class', 'icon-white icon-arrow-right');
        
        if($this->isNotOnlyOrganismManager){
            $aRemove = $this->formHtml->createElement('a');
            $aRemove->setAttribute('id', 'remove-btn' . FormUtils::serializeXpath($element->getNodePath()));
            $aRemove->setAttribute('class', 'btn btn-danger btn-mini pull-right remove-btn');
            $aRemove->setAttribute('data-lowerbound', $lowerbound);
            $aRemove->setAttribute('data-upperbound', $upperbound);
            $aRemove->setAttribute('data-xpath', FormUtils::serializeXpath($this->removeIndex($element->getNodePath())) . '-' . $guid);

            $iRemove = $this->formHtml->createElement('i');
            $iRemove->setAttribute('class', 'icon-white icon-cancel-2');
        }

        $fieldset = $this->formHtml->createElement('fieldset');
        $class = array();
        switch ($scopeId) {
            case EnumRelationScope::HIDDEN:
                $class[] = 'scope-hidden';
                break;
            
            case EnumRelationScope::VISIBLE:
                $class[] = 'scope-visible';
                break;
        }

        if ($guid != '') {
            $spanLegend = $this->formHtml->createElement('span', EText::_($guid)); //
        } else {
            $spanLegend = $this->formHtml->createElement('span', JText::_($legendAttribute));
        }

        $spanLegend->setAttribute('class', 'legend');
        $legend = $this->formHtml->createElement('legend');

        $divInner = $this->formHtml->createElement('div');
        $divInner->setAttribute('id', 'inner-fds' . FormUtils::serializeXpath($element->getNodePath()));
        $divInner->setAttribute('class', 'inner-fds');
        if (!isset($_GET['relid'])) {
            $divInner->setAttribute('style', 'display:none;');
        }

        if ($exist == 1) {
            $fieldset->setAttribute('id', 'fds' . FormUtils::serializeXpath($element->getNodePath()));
            $class[] = 'fds' . FormUtils::serializeXpath(FormUtils::removeIndexFromXpath($element->getNodePath(),-1)) . '-' . $guid;

            $aCollapse->appendChild($iCollapse);
            $legend->appendChild($aCollapse);
            $legend->appendChild($spanLegend);
            if($this->isNotOnlyOrganismManager){
                $aRemove->appendChild($iRemove);
                $legend->appendChild($aRemove);
            }

            $fieldset->appendChild($legend);
            $fieldset->appendChild($divInner);
        }
        
        $fieldset->setAttribute('class', implode(' ', $class));
       
        return $fieldset;
    }

    /**
     * Encode special characters into HTML entities. Unless the <> characters.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param string $text
     * @return string
     */
    private function convert($text) {
        $text = htmlentities($text, ENT_NOQUOTES, "UTF-8");
        $text = htmlspecialchars_decode($text);
        return $text;
    }

    /**
     * This method constructs a set of attributes.
     * It will contain either a single field or a field for each language or a group of fields corresponding to a stereotype.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute The current attribute.
     * @return DOMElement DIV containing a group of fields.
     */
    private function getAttribute(DOMElement $attribute) {

        $languages = $this->ldao->getSupported();
        $userLanguageIndex = 0;

        if ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId') == EnumChildtype::$RELATIONTYPE) {
            $upperbound = 1;
        } else {
            $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');
        }

        $stereotypeId = $attribute->getAttributeNS($this->catalog_uri, 'stereotypeId');
        $rendertypeId = $attribute->getAttributeNS($this->catalog_uri, 'rendertypeId');
        $scopeId = $attribute->getAttributeNS($this->catalog_uri, 'scopeId');

        $attributeGroup = $this->formHtml->createElement('div');
        $class = array('attribute-group');
        if ($rendertypeId == 1000) {
            $class[] = 'scope-hidden';
            $class[] = 'attribute-group' . FormUtils::serializeXpath($this->removeIndex($attribute->getNodePath()));
        } else {
            $class[] = 'attribute-group' . FormUtils::serializeXpath($this->removeIndex($attribute->getNodePath()));
        }
        
        if($scopeId == EnumRelationScope::HIDDEN){
            $class[] = 'scope-hidden';
        }
        
        $attributeGroup->setAttribute('class', implode(' ', $class));

        $attributeGroup->setAttribute('id', 'attribute-group' . FormUtils::serializeXpath($attribute->getNodePath()));

        if ($upperbound > 1 && !($rendertypeId == EnumRendertype::$LIST && $upperbound > 1) && $this->isNotOnlyOrganismManager) {
            $attributeGroup->appendChild($this->getAttributeAction($attribute));
        }

        switch ($stereotypeId) {
            case EnumStereotype::$GEMET:
            case EnumStereotype::$LOCALE:
                if ($stereotypeId == EnumStereotype::$GEMET) {
                    $attributeGroup->appendChild($this->getGemet($attribute));
                } elseif ($stereotypeId == EnumStereotype::$LOCALE) {
                    $class = $attributeGroup->getAttribute('class') . ' i18n';
                    $attributeGroup->setAttribute('class', $class);
                }

                $nodePath = $attribute->firstChild->getNodePath();
                $jfield = $this->form->getField(FormUtils::serializeXpath($nodePath));

                foreach ($this->buildField($attribute, $jfield) as $element) {
                    $attributeGroup->appendChild($element);
                }

                foreach ($languages as $key => $value) {

                    $jfield = $this->form->getField(FormUtils::serializeXpath($attribute->getNodePath() . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString#' . $key));
                    if ($jfield) {
                        foreach ($this->buildField($attribute, $jfield) as $element) {
                            $attributeGroup->appendChild($element);
                        }
                    }
                }

                if ($stereotypeId == EnumStereotype::$GEMET) {

                    $cnt = 0;
                    foreach ($this->ldao->getAll() as $key => $value) {
                        if (strpos($this->userParams->language, $key)) {
                            $userLanguageIndex = $cnt;
                            break;
                        }
                        $cnt++;
                    }

                    $cnt = 0;
                    foreach ($this->domXpathFormHtml->query('descendant::div[@class="control-group"][position()>0]', $attributeGroup) as $control_group) {
                        if ($cnt != $userLanguageIndex) {
                            $control_group->setAttribute('style', 'display: none;');
                        }
                        $cnt++;
                    }

                    foreach ($this->domXpathFormHtml->query('descendant::select', $attributeGroup) as $select) {
                        $i = 0;
                        foreach ($this->domXpathFormHtml->query('descendant::option', $select) as $option) {
                            $option->setAttribute('selected', 'selected');
                            $option->setAttribute('id', $select->getAttribute('id') . '_option_' . $i);
                            $i++;
                            $option->setAttribute('index', $i);
                        }
                    }
                }
                break;

            default:
                if ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId') == EnumChildtype::$RELATIONTYPE) {
                    $nodePath = $attribute->getNodePath();
                } else {
                    $nodePath = $attribute->firstChild->getNodePath();
                }

                $occurance = 0;

                switch ($rendertypeId) {
                    case EnumRendertype::$CHECKBOX:
                        /** @var JFormField */
                        $jfield = $this->form->getField(FormUtils::removeIndexToXpath(FormUtils::serializeXpath($nodePath)));
                        $fieldid = $jfield->__get('id');
                        $query = 'descendant::*[@id="' . $fieldid . '"]';
                        $occurance = $this->domXpathFormHtml->query($query)->length;
                        break;
                    case EnumRendertype::$LIST:
                        // Mutiple list
                        if ($upperbound > 1) {
                            switch ($stereotypeId) {
                                case EnumStereotype::$BOUNDARY:
                                    $jfield = $this->form->getField(FormUtils::removeIndexToXpath(FormUtils::serializeXpath($nodePath), 12, 15));
                                    break;

                                default:
                                    /* if (!empty($this->ajaxXpath)) {
                                      $path = explode('/', $nodePath);
                                      $lastPart = $path[count($path) - 1];
                                      unset($path[count($path) - 1]);
                                      $xpath = explode('[', $lastPart);
                                      $newPath = implode('/', $path) . '/' . $xpath[0];

                                      $nodePath = str_replace($this->ajaxXpath, $newPath, $nodePath);
                                      } */
                                    $jfield = $this->form->getField(FormUtils::removeIndexToXpath(FormUtils::serializeXpath($nodePath)));
                                    break;
                            }

                            $fieldid = $jfield->__get('id');
                            $query = 'descendant::*[@id="' . $fieldid . '"]';
                            $occurance = $this->domXpathFormHtml->query($query)->length;

                            // Single list
                        } else {
                            $jfield = $this->form->getField(FormUtils::serializeXpath($nodePath));
                        }
                        break;
                    default:
                        $jfield = $this->form->getField(FormUtils::serializeXpath($nodePath));
                        break;
                }

                if ($jfield) {

                    if ($occurance < 1) {
                        foreach ($this->buildField($attribute, $jfield) as $element) {
                            $attributeGroup->appendChild($element);
                        }
                    }
                } else {
                    JFactory::getApplication()->enqueueMessage('Field not found ' . FormUtils::serializeXpath($nodePath), 'warning');
                }
                break;
        }

        if ($attribute->getAttributeNS($this->catalog_uri, 'map')) {
            $map_id = JComponentHelper::getParams('com_easysdi_catalog')->get('catalogmap');

            if (isset($map_id)) {
                $attributeGroup->appendChild($this->getMap($attribute, $map_id));
            }
        }
        return $attributeGroup;
    }

    /**
     * Returns a DIV containing the fields of the stereotype "geographic extent."
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute The current attribute.
     * @return DOMElement The DIV.
     */
    private function getMap(DOMElement $attribute, $map_id) {
        $parent_path = str_replace('-', '_', FormUtils::serializeXpath($attribute->parentNode->getNodePath()));
        $select_parent_path = str_replace('-', '_', FormUtils::serializeXpath($attribute->parentNode->parentNode->parentNode->getNodePath()));

        $div = $this->formHtml->createElement('div');

        $btnEdit = $this->formHtml->createElement('button', 'Edition');
        $btnEdit->setAttribute('type', 'button');
        $btnEdit->setAttribute('class', 'btn btn-primary btn-small edit_bb');
        $btnEdit->setAttribute('id', 'editBtn_' . $parent_path);
        $btnEdit->setAttribute('data-toggle', 'button');
        $btnEdit->setAttribute('onclick', 'polygonControl_' . $parent_path . '.activate(); clearbbselect(\'' . $select_parent_path . '\')');

        $br = $this->formHtml->createElement('br');

        $divMap = $this->formHtml->createElement('div');
        $divMap->setAttribute('id', 'map_' . $parent_path);
        $divMap->setAttribute('style', 'width: 550px;height: 300px;');

        $script = $this->formHtml->createElement('script');
        $script->setAttribute('type', 'text/javascript');

        $query = $this->db->getQuery(true);

        $query->select('m.srs, m.unit_id, m.maxresolution, m.restrictedextent, m.zoom, m.maxextent, m.centercoordinates, l.layername, l.service_id, l.servicetype, l.asOLstyle, l.asOLoptions, l.asOLmatrixset, u.alias as unit_alias');
        $query->from('#__sdi_map as m');
        $query->innerJoin('#__sdi_map_layergroup mlg ON m.id = mlg.map_id');
        $query->innerJoin('#__sdi_layer_layergroup llg ON llg.group_id = mlg.group_id');
        $query->innerJoin('#__sdi_maplayer l ON l.id = llg.layer_id');
        $query->innerJoin('#__sdi_sys_unit u ON u.id = m.unit_id');
        $query->where('m.id=' . (int) $map_id);
        $query->where('mlg.isbackground = 1');

        $this->db->setQuery($query);
        $map_config = $this->db->loadObject();

        $query = $this->db->getQuery(true);
        switch ($map_config->servicetype) {
            case 'physical':
                $query->select('resourceurl as serviceurl, serviceconnector_id');
                $query->from('#__sdi_physicalservice');
                $query->where('id = ' . (int) $map_config->service_id);
                break;
            case 'virtual':
                $query->select('url, reflectedurl as serviceurl, serviceconnector_id');
                $query->from('#__sdi_virtualservice');
                $query->where('id = ' . (int) $map_config->service_id);
                break;
        }

        $this->db->setQuery($query);
        $service = $this->db->loadObject();

        if (empty($service->serviceurl)) {
            $service->serviceurl = $service->url;
        }

        switch ($service->serviceconnector_id) {
            case EnumServiceConnector::$GOOGLE:
                switch ($map_config->layername) {
                    case EnumLayerName::$ROADMAP:
                        $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Google(
                                                'Google Streets',
                                                {numZoomLevels: 20}
                                            );";
                        break;
                    case EnumLayerName::$TERRAIN:
                        $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Google(
                                                'Google Physicial',
                                                {type: google.maps.MapTypeId.TERRAIN}
                                            );";
                        break;
                    case EnumLayerName::$SATELLITE:
                        $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Google(
                                                'Google Satellite',
                                                {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22}
                                            );";
                        break;
                    case EnumLayerName::$HYBRID:
                        $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Google(
                                                'Google Hybrid',
                                                {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20}
                                            );";
                        break;
                }
                break;
            case EnumServiceConnector::$OSM:
                $layer_definition = "layer_$parent_path = new OpenLayers.Layer.OSM();";
                break;
            case EnumServiceConnector::$BING:
                $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Bing({
                                        name: 'Bing',
                                        key: apiKey,
                                        type: '" . $map_config->layername . "'
                                    });";
                break;
            case EnumServiceConnector::$WMS:
                $layer_definition = "layer_$parent_path = new OpenLayers.Layer.WMS( 'WMS name',
                                    '" . $service->serviceurl . "',
                                    {layers: '" . $map_config->layername . "'});";
                break;
            case EnumServiceConnector::$WMTS:
                $layer_definition = "layer_$parent_path = new OpenLayers.Layer.WMTS({
                                        name: 'Couche WMTS',
                                        url: '" . $service->serviceurl . "',
                                        layer: '" . $map_config->layername . "',
                                        matrixSet: " . $map_config->asOLmatrixset . ",
                                        style: " . $map_config->asOLstyle . ",
                                        " . $map_config->asOLoptions . "
                                    });";
                break;
        }

        $centercoords = explode(',', $map_config->centercoordinates);
        $script->nodeValue = "var map_$parent_path, layer_$parent_path, polygonLayer_$parent_path, polygonControl_$parent_path ;
                            js('document').ready(function() {


                                var map_options = {projection: \"" . $map_config->srs . "\"
                                    , maxResolution: " . $map_config->maxresolution . "
                                    , units: \"" . $map_config->unit_alias . "\"
                                    , maxExtent: [" . $map_config->maxextent . "]";
        if (!empty($map_config->restrictedextent)) {
            $script->nodeValue .= ", restrictedExtent: [" . $map_config->restrictedextent . "]";
        }

        $script->nodeValue .= "};";


        $script->nodeValue.= " map_$parent_path = new OpenLayers.Map(\"map_$parent_path\", map_options);

                                " . $layer_definition . "
                                polygonLayer_$parent_path = new OpenLayers.Layer.Vector('Polygon Layer');
                                
                                var Navigation = new OpenLayers.Control.Navigation({
                                    'zoomWheelEnabled': false,
                                    'defaultDblClick': function ( event ) { 
                                        return; 
                                     }
                                });

                                map_$parent_path.addControl(Navigation);

                                var NavigationControls = map_$parent_path.getControlsByClass('OpenLayers.Control.Navigation')
                                  , i;

                                for ( i = 0; i < NavigationControls.length; i++ ) {
                                    NavigationControls[i].disableZoomWheel();
                                }

                                map_$parent_path.addLayers([layer_$parent_path, polygonLayer_$parent_path]);

                                    var psource = new Proj4js.Proj(\"" . $map_config->srs . "\");
                                    var pdest   = new Proj4js.Proj(\"EPSG:4326\");

                    ";


        if (!empty($map_config->centercoordinates) && !empty($map_config->zoom)) {
            $script->nodeValue .= "
                                    var centercoord = new Proj4js.Point(" . $centercoords[0] . ", " . $centercoords[1] . ");
                                    map_$parent_path.setCenter(new OpenLayers.LonLat(centercoord.x, centercoord.y), $map_config->zoom);";
        } else if (!empty($map_config->centercoordinates)) {
            $script->nodeValue .= "
                                    var centercoord = new Proj4js.Point(" . $centercoords[0] . ", " . $centercoords[1] . ");
                                    map_$parent_path.setCenter(new OpenLayers.LonLat(centercoord.x, centercoord.y));";
        } else {
            $script->nodeValue .= "map_$parent_path.zoomToMaxExtent(); ";
        }


        $script->nodeValue .= "
                                var polyOptions = {sides: 4, irregular: true};
                                polygonControl_$parent_path = new OpenLayers.Control.DrawFeature(polygonLayer_$parent_path,
                                        OpenLayers.Handler.RegularPolygon,
                                        {handlerOptions: polyOptions});

                                map_$parent_path.addControl(polygonControl_$parent_path);

                               setTimeout(function(){drawBB('$parent_path');}, 2000);

                               polygonLayer_$parent_path.events.register('featureadded', polygonLayer_$parent_path, function(e) {
                                    polygonControl_$parent_path.deactivate();
                                    js('#editBtn_$parent_path').removeClass('active');

                                    var bounds = e.feature.geometry.getBounds();

                                    var source = new Proj4js.Proj(map_" . $parent_path . ".getProjection());
                                    var dest   = new Proj4js.Proj(\"EPSG:4326\");

                                    var bottom_left = new Proj4js.Point(bounds.left, bounds.bottom);
                                    var top_right = new Proj4js.Point(bounds.right, bounds.top);

                                    Proj4js.transform(source, dest, bottom_left);
                                    Proj4js.transform(source, dest, top_right);

                                    js('#jform_" . $parent_path . "_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value', top_right.y);
                                    js('#jform_" . $parent_path . "_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value', bottom_left.y);
                                    js('#jform_" . $parent_path . "_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value', top_right.x);
                                    js('#jform_" . $parent_path . "_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value', bottom_left.x);

                                    map_$parent_path.zoomToExtent(polygonLayer_$parent_path.getDataExtent());
                                });

                                polygonLayer_$parent_path.events.register('beforefeatureadded', polygonLayer_$parent_path, function(e) {
                                    polygonLayer_$parent_path.removeAllFeatures();

                                });

                            });";

        $div->appendChild($btnEdit);
        $div->appendChild($br);
        $div->appendChild($divMap);
        $div->appendChild($script);

        return $div;
    }

    /**
     * This method returns a DIV containing the fields of the stereotype "GEMET".
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getGemet(DOMElement $attribute) {
        // predefine default language
        $default = $this->ldao->getDefaultLanguage()->gemet;

        // build languages array
        $languages = array();
        foreach ($this->ldao->getAll() as $language) {
            $languages[] = "'{$language->gemet}'";

            // if match, override default language
            if ($language->code == $this->userParams->language)
                $default = $language->gemet;
        }

        $parent_path = str_replace('-', '_', FormUtils::serializeXpath($attribute->parentNode->getNodePath()));
        $div = $this->formHtml->createElement('div');

        $script = $this->formHtml->createElement('script');
        $script->setAttribute('type', 'text/javascript');

        $script_code = "js = jQuery.noConflict();

        js('document').ready(function() {

            var languages = new Array(" . implode(',', $languages) . ");
            var index = '';
            if(js('#jform_" . $parent_path . "_sla_gmd_dp_keyword_sla_gco_dp_CharacterString').length == 0){
                 index = '_la_1_ra_';
            }

            // path to Ext images
            Ext.BLANK_IMAGE_URL = '" . JURI::root() . "components/com_easysdi_core/libraries/ext/resources/images/default/s.gif';

            // sets the user interface language
            HS.setLang('" . $default . "');

            var writeTerms = function(result) {

                for(var i=0; i < languages.length; i++){
                    var paths = result.terms[languages[i]].split('>');
                    var keyword = paths[paths.length - 1];
                    var option_string = '<option class=\''+result.uri+'\' value=\"'+keyword+'\" selected>'+keyword+'</option>';

                    if(i==0){
                        var current_select = js('#jform_" . $parent_path . "_sla_gmd_dp_keyword'+index+'_sla_gco_dp_CharacterString');
                        var current_div = js('#jform_" . $parent_path . "_sla_gmd_dp_keyword'+index+'_sla_gco_dp_CharacterString_chzn ul li[class=\'search-field\'] input');

                    }else{
                        var current_select = js('#jform_" . $parent_path . "_sla_gmd_dp_keyword'+index+'_sla_gmd_dp_PT_FreeText_sla_gmd_dp_textGroup_sla_gmd_dp_LocalisedCharacterString_'+languages[i].toUpperCase());
                        var current_div = js('#jform_" . $parent_path . "_sla_gmd_dp_keyword'+index+'_sla_gmd_dp_PT_FreeText_sla_gmd_dp_textGroup_sla_gmd_dp_LocalisedCharacterString_'+languages[i].toUpperCase()+'_chzn ul li[class=\'search-field\'] input');
                    }
                    current_select.append(option_string);
                    current_select.trigger('liszt:updated');
                    current_div.attr('style','width: 0px');

                    addToStructure('" . $attribute->getAttributeNS($this->catalog_uri, 'relid') . "','" . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . "');
                }
            }

            Ext.onReady(function() {

                var thes = new ThesaurusReader({
                    appPath: '" . JUri::base() . "components/com_easysdi_core/libraries/gemetclient-2.0.0/src/',
                    lang: '" . $default . "',
                    outputLangs: [" . implode(',', $languages) . "],
                    title: '" . JText::_('COM_EASYSDI_CATALOG_GEMET_THESAURUS_TITLE') . "',
                    separator: ' > ',
                    returnPath: true,
                    returnInspire: true,
                    width: 520, height: 300,
                    layout: 'fit',
                    proxy: '" . JUri::base() . "components/com_easysdi_core/libraries/gemetclient-2.0.0/src/proxy.php?url=',
                    handler: writeTerms
                });

                thes.render('gemet');

                // HACK TO FIX RENDER
                js('#ext-gen10').css('width', 'auto');
                js('html').remove('#gemet #ext-comp-1002-xcollapsed');
            });

            js('select[id^=jform_{$parent_path}_sla_gmd_dp_keyword]').on('change',function(evt, params){
                if(params.deselected){
                    var opt = js('select[id^=jform_{$parent_path}_sla_gmd_dp_keyword] option[value=\"'+params.deselected+'\"]');
                    
                    var index_number = opt[0].index;
                    
                    for(var i=0; i<languages.length; i++){
                        if(i == 0){
                            var select = js('#jform_{$parent_path}_sla_gmd_dp_keyword'+index+'_sla_gco_dp_CharacterString');
                        }
                        else{
                            var select = js('#jform_{$parent_path}_sla_gmd_dp_keyword'+index+'_sla_gmd_dp_PT_FreeText_sla_gmd_dp_textGroup_sla_gmd_dp_LocalisedCharacterString_'+languages[i].toUpperCase());
                        }
                        
                        js('#'+select[0][index_number].id).remove();
                        js('#'+select[0].id).trigger('liszt:updated');
                    }
                    
                    removeFromStructure('" . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . "-sla-gmd-dp-keyword-la-'+index_number+'-ra-');
                }
            });

        });";

        $script->nodeValue = $script_code;

        $aModal = $this->formHtml->createElement('a', JText::_('COM_EASYSDI_CATALOG_THESAURUS_GEMET'));
        $aModal->setAttribute('data-toggle', 'modal');
        $aModal->setAttribute('href', '#myModal');
        $aModal->setAttribute('class', 'btn btn-primary btn-lg');
        if(!$this->isNotOnlyOrganismManager){
            $aModal->setAttribute('disabled', 'disabled');
        }

        $divModal = $this->formHtml->createElement('div');
        $divModal->setAttribute('class', 'modal fade hide ext-strict');
        $divModal->setAttribute('id', 'myModal');
        $divModal->setAttribute('tabindex', '-1');
        $divModal->setAttribute('role', 'dialog');
        $divModal->setAttribute('aria-labelledby', 'myModalLabel');
        $divModal->setAttribute('aria-hidden', 'true');

        $divDialog = $this->formHtml->createElement('div');
        $divDialog->setAttribute('class', 'modal-dialog');

        $divContent = $this->formHtml->createElement('div');
        $divContent->setAttribute('class', 'modal-content');

        $divHeader = $this->formHtml->createElement('div');
        $divHeader->setAttribute('class', 'modal-header');

        $btnClose = $this->formHtml->createElement('button', '&times;');
        $btnClose->setAttribute('type', 'button');
        $btnClose->setAttribute('class', 'close');
        $btnClose->setAttribute('data-dismiss', 'modal');
        $btnClose->setAttribute('aria-hidden', 'true');

        $h4 = $this->formHtml->createElement('h4', JText::_('COM_EASYSDI_CATALOG_THESAURUS_GEMET'));
        $h4->setAttribute('class', 'modal-title');

        $divBody = $this->formHtml->createElement('div');
        $divBody->setAttribute('class', 'modal-body');

        $divGemet = $this->formHtml->createElement('div');
        $divGemet->setAttribute('id', 'gemet');
        $divGemet->setAttribute('class', 'gemet');

        $divHeader->appendChild($btnClose);
        $divHeader->appendChild($h4);

        $divBody->appendChild($divGemet);

        $divContent->appendChild($divHeader);
        $divContent->appendChild($divBody);

        $divDialog->appendChild($divContent);

        $divModal->appendChild($divDialog);

        $div->appendChild($aModal);
        $div->appendChild($divModal);
        $div->appendChild($script);

        return $div;
    }

    /**
     * This method builds up the field retrieve structure Joomla field.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute The current attribute.
     * @param JField $field The Joomla JField
     * @param boolean $addButton Defines whether the "Add" button must be created.
     * @return DOMElement[]
     */
    private function buildField(DOMElement $attribute, JFormField $field) {
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'id');
        $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');
        $lowerbound = $attribute->getAttributeNS($this->catalog_uri, 'lowerbound');
        $stereotypeId = $attribute->getAttributeNS($this->catalog_uri, 'stereotypeId');
        $rendertypeId = $attribute->getAttributeNS($this->catalog_uri, 'rendertypeId');
        $occurance = $this->domXpathStr->query($attribute->getNodePath())->length;

        $elements = array();

        $controlGroup = $this->formHtml->createElement('div');
        $controlGroup->setAttribute('class', 'control-group');

        $controlLabel = $this->formHtml->createElement('div');
        $controlLabel->setAttribute('class', 'control-label');

        $control = $this->formHtml->createElement('div');
        $control->setAttribute('class', 'controls');

        if ($field->label != '') {
            $controlLabel->appendChild($this->getLabel($field));
        }

        if ($stereotypeId == EnumStereotype::$FILE) {
            $input_append = $this->formHtml->createElement('div');
            $input_append->setAttribute('class', 'input-append file-controls');
            
            $attach_button = $this->formHtml->createElement('button', JText::_('COM_EASYSDI_CATALOG_FILE_ATTACH'));
            $attach_button->setAttribute('class', 'btn attach-btn');
            $attach_button->setAttribute('type', 'button');
            $attach_button->setAttribute('rendertypeId', $rendertypeId);
            
            $preview_button = $this->formHtml->createElement('button',  JText::_('COM_EASYSDI_CATALOG_FILE_PREVIEW'));
            $preview_button->setAttribute('class', 'btn btn-preview');
            $preview_button->setAttribute('type', 'button');
            $preview_button->setAttribute('data-target', $field->id);
            
            $delete_button = $this->formHtml->createElement('button', JText::_('COM_EASYSDI_CATALOG_FILE_DELETE'));
            $delete_button->setAttribute('class', 'btn btn-danger btn-delete');
            $delete_button->setAttribute('type', 'button');
            $delete_button->setAttribute('data-target', $field->id);
            
            $input_append->appendChild($this->getInput($field));
            $input_append->appendChild($attach_button);
            $input_append->appendChild($preview_button);
            $input_append->appendChild($delete_button);
            $control->appendChild($input_append);
        }else{
            $control->appendChild($this->getInput($field));
        }

        $controlGroup->appendChild($controlLabel);
        $controlGroup->appendChild($control);

        if ($occurance < $lowerbound) {
            $controlGroup->appendChild($this->getAttributeRemoveAction($attribute));
        }

        $elements[] = $controlGroup;

        //$elements[] = $this->getInputScript($field, $guid);

        if ($rendertypeId == EnumRendertype::$LIST && $upperbound > 1 && $stereotypeId != EnumStereotype::$BOUNDARY) {
            $elements[] = $this->getMultiSelectScript($field, $attribute);
        }

        return $elements;
    }

    /**
     * Import the HTML structure of the label in a DOMElement.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param JField $field The Joomla JField
     * @return DOMElement
     */
    private function getLabel($field) {
        $labelString = $field->label;
        $domlocal = new DOMDocument();
        $domlocal->loadHTML($this->convert($labelString));

        $domXapth = new DOMXPath($domlocal);
        $label = $domXapth->query('/*/*/*')->item(0);

        $cloned = $label->cloneNode(TRUE);

        $imported = $this->formHtml->importNode($cloned, TRUE);

        return $imported;
    }

    /**
     * Import the HTML structure of the label in a DOMElement.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.2
     *
     * @param JField $field The Joomla JField
     * @return DOMElement
     */
    private function getLabelText($field) {
        $element = $this->getLabel($field);

        return $element->nodeValue;
    }

    /**
     * Import HTML structure of input field in a DOMElement.
     *
     * @param JField $field The Joomla JField.
     * @return DOMElement
     */
    private function getInput($field) {
        $domlocal = new DOMDocument();
        $domlocal->loadHTML($this->convert($field->input));

        $domXapth = new DOMXPath($domlocal);
        $input = $domXapth->query('/*/*/*')->item(0);

        $cloned = $input->cloneNode(TRUE);

        $imported = $this->formHtml->importNode($cloned, TRUE);

        return $imported;
    }

    /**
     * This method creates the tooltip script.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param JFormField $field The Joomla JField
     * @param string $guid Guid find in translations.
     * @return DOMElement
     */
    private function getInputScript($field, $guid) {
        $script_content = "js = jQuery.noConflict();

                    js('document').ready(function() {
                        js('#" . $field->id . "').tooltip({'trigger':'focus', 'title': \"" . preg_replace('/(\r\n|\n|\r)/', '<br/>', addslashes(EText::_($guid, 2, null))) . "\"});
                    });";

        $script = $this->formHtml->createElement('script', $script_content);
        $script->setAttribute('type', 'text/javascript');

        return $script;
    }

    /**
     * This method creates the tooltip script.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param JFormField $field The Joomla JField
     * @return DOMElement
     */
    private function getMultiSelectScript($field, DOMElement $attribute) {

        $script_content = "js = jQuery.noConflict();
                            js('#" . $field->__get('id') . "').on('change',function(e, params) {
                                
                                if(params.selected != null){
                                    addToStructure(" . $attribute->getAttributeNS($this->catalog_uri, 'relid') . ", '" . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . "');
                                }else{
                                    removeFromStructure('" . FormUtils::serializeXpath($attribute->getNodePath()) . "');
                                }

                            });";


        $script = $this->formHtml->createElement('script', $script_content);
        $script->setAttribute('type', 'text/javascript');

        return $script;
    }

    /**
     * Create the "ADD" bouton, if necessary.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute The current attribute.
     * @return DOMElement
     */
    private function getAttributeAction(DOMElement $attribute) {
        $lowerbound = $attribute->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');

        $action = $this->formHtml->createElement('div');
        $action->setAttribute('id', 'attribute-action' . FormUtils::serializeXpath($attribute->getNodePath()));
        $action->setAttribute('class', 'attribute-action attribute-action' . FormUtils::serializeXpath($this->removeIndex($attribute->getNodePath())));
        $action->setAttribute('data-lowerbound', $lowerbound);
        $action->setAttribute('data-upperbound', $upperbound);
        $action->setAttribute('data-relid', $relid);
        $action->setAttribute('data-parentpath', FormUtils::serializeXpath($attribute->parentNode->getNodePath()));
        $action->setAttribute('data-button-class', FormUtils::serializeXpath($this->removeIndex($attribute->getNodePath())));

        $aAdd = $this->formHtml->createElement('a');
        $aAdd->setAttribute('id', 'attribute-add-btn' . FormUtils::serializeXpath($this->removeIndex($attribute->getNodePath())));
        $aAdd->setAttribute('class', 'btn btn-success pull-right btn-mini attribute-add-btn');
        $aAdd->setAttribute('style', 'display:none');

        $iAdd = $this->formHtml->createElement('i');
        $iAdd->setAttribute('class', 'icon-white icon-plus-2');

        $aRemove = $this->formHtml->createElement('a');
        $aRemove->setAttribute('id', 'attribute-remove-btn' . FormUtils::serializeXpath($attribute->getNodePath()));
        $aRemove->setAttribute('class', 'btn btn-danger btn-mini pull-right attribute-remove-btn');
        $aRemove->setAttribute('style', 'display:none');

        $iRemove = $this->formHtml->createElement('i');
        $iRemove->setAttribute('class', 'icon-white icon-cancel-2');
        $aRemove->appendChild($iRemove);

        $aAdd->appendChild($iAdd);
        $action->appendChild($aAdd);
        $action->appendChild($aRemove);

        if (JDEBUG) {
            $lowerboundspan = $this->formHtml->createElement('span', 'lowerbound:' . $lowerbound);
            $upperboundspan = $this->formHtml->createElement('span', 'upperbound:' . $upperbound);
            $action->appendChild($lowerboundspan);
            $action->appendChild($upperboundspan);
        }

        return $action;
    }

    /**
     * Adds the "Preview" button to the File stereotype.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getPreviewAction(DOMElement $attribute) {
        $a = $this->formHtml->createElement('a');
        $i = $this->formHtml->createElement('i');

        $a->setAttribute('id', 'preview-' . FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $a->setAttribute('target', '_blank');
        $a->setAttribute('class', 'btn btn-mini preview-btn');
        $a->setAttribute('href', $attribute->nodeValue);
        $a->setAttribute('target', '_blank');

        $i->setAttribute('class', 'icon-white icon-eye-open');

        $a->appendChild($i);

        return $a;
    }

    /**
     * Adds the "Clear" button to the File stereotype.
     *
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     *
     * @param DOMElement $attribute The current attribute.
     * @return DOMElement
     */
    private function getEmptyFileAction(DOMElement $attribute) {
        $a = $this->formHtml->createElement('a');
        $i = $this->formHtml->createElement('i');

        $a->setAttribute('id', 'empty-btn-' . FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $a->setAttribute('class', 'btn btn-danger btn-mini empty-btn empty-btn-' . FormUtils::serializeXpath($this->removeIndex($attribute->getNodePath())));
        $a->setAttribute('onclick', 'confirmEmptyFile(this.id)');

        $i->setAttribute('class', 'icon-white icon-cancel-2');

        $a->appendChild($i);

        return $a;
    }

    /**
     * Remove index from XPath
     *
     * @param string $xpath
     * @return string
     * @deprecated since version 4.2.4 Please use FormUtils::removeIndexFromXpath
     * 
     */
    private function removeIndex($xpath) {
        return preg_replace('/[\[0-9\]*]/i', '', $xpath);
    }

    /**
     * 
     * @param DOMNodeList $childNodes
     * @return DOMElement First non text node
     */
    private function getFirstNonTextChild(DOMNodeList $childNodes) {
        foreach ($childNodes as $child) {
            if ($child->nodeType != XML_TEXT_NODE) {
                return $child;
            }
        }

        return false;
    }

}

?>
