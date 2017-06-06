<?php

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/EText.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiLanguageDao.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumChildtype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRenderType.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumStereotype.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumRelationScope.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormUtils.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/FormStereotype.php';

/**
 * This Class will generate a form in XML format for Joomla.
 * 
 * @version     4.3.2
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class FormGenerator {

    /** @var JObject */
    private $item;

    /** @var sdiUser */
    private $user;

    /** @var JDatabaseDriver */
    private $db = null;

    /** @var DOMDocument */
    private $csw;

    /** @var SdiLanguageDao */
    private $ldao;

    /** @var JSession */
    private $session;

    /**  @var DOMDocument */
    private $form;

    /** @var DOMDocument */
    public $structure;

    /** @var string */
    public $ajaxXpath;

    /** @var DOMXPath */
    private $domXpathStr;

    /**  @var DOMXPath */
    private $domXpathCsw;

    /** @var string */
    private $catalog_uri = 'http://www.easysdi.org/2011/sdi/catalog';

    /** @var string */
    private $catalog_prefix = 'catalog';
    private $boundaryAttributeOptions = null;

    function __construct(JObject $item = null) {
        $this->db = JFactory::getDbo();
        $this->session = JFactory::getSession();
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->user = sdiFactory::getSdiUser();

        $this->form = new DOMDocument('1.0', 'utf-8');
        $this->structure = new DOMDocument('1.0', 'utf-8');
        $this->structure->preserveWhiteSpace = FALSE;

        if (isset($item)) {
            $this->item = $item;
            $this->csw = $item->csw;
            $this->setDomXpathCsw();
            $this->session->set('item', $item);
        } else {
            $this->item = $this->session->get('item');
        }
    }

    /**
     * Returns a form structure to Joomla format.
     * 
     * @return string Form structure in Joomla format
     * @version 4.0.0
     */
    public function getForm() {

        if (!isset($_GET['relid'])) {
            $query = $this->db->getQuery(true);
            $query->select('r.id, r.name, r.childtype_id');
            $query->select('c.id as class_id, c.name as class_name,c.isocode as class_isocode, c.guid as class_guid, c.isrootclass');
            $query->select('ns.id as ns_id, ns.prefix as ns_prefix, ns.uri as ns_uri');
            $query->from('#__sdi_profile AS p');
            $query->innerJoin('#__sdi_relation AS r ON p.class_id = r.parent_id');
            $query->innerJoin('#__sdi_attribute AS a ON a.id = p.metadataidentifier');
            $query->innerJoin('#__sdi_class AS c ON c.id = r.parent_id');
            $query->innerJoin('#__sdi_namespace AS ns ON ns.id = c.namespace_id');
            $query->where('p.id = ' . (int) $this->item->profile_id);
            $query->where('c.isrootclass = ' . $query->quote(true));

            $this->db->setQuery($query);
            $result = $this->db->loadObject();

            $root = $this->getDomElement($result->ns_uri, $result->ns_prefix, $result->class_isocode, $result->class_id, EnumChildtype::$CLASS, $result->class_guid);
            foreach ($this->nsdao->getAll() as $ns) {
                $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:' . $ns->prefix, $ns->uri);
            }

            $root->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');

            $this->structure->appendChild($root);

            $this->getChildTree($root);
        } else {

            $this->structure->loadXML(unserialize($this->session->get('structure')));
            $this->setDomXpathStr();

            $parent = $this->domXpathStr->query(FormUtils::unSerializeXpath($_GET['parent_path']))->item(0);

            $query = $this->getRelationQuery();
            $query->where('r.id = ' . $_GET['relid']);

            $this->db->setQuery($query);
            $result = $this->db->loadObject();

            $scope_id = $this->getFieldScope($this->item->id, $result->relationscope_id, $result->editorrelationscope_id);

            switch ($result->childtype_id) {
                case EnumChildtype::$CLASS:

                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->isocode, $result->id, EnumChildtype::$RELATION, $result->guid, 1, $result->upperbound);
                    $class = $this->getDomElement($result->class_ns_uri, $result->class_ns_prefix, $result->class_name, $result->class_id, EnumChildtype::$CLASS, $result->class_guid);
                    $relation->appendChild($class);

                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':scopeId', $scope_id);

                    $coll = $this->domXpathStr->query($parent->getNodePath() . '/*[@catalog:dbid="' . $_GET['relid'] . '" and @catalog:childtypeId="0"]');

                    //try to set the refNode, depending on the prevSibl existence
                    $refNode = $coll->item($coll->length - 1)->nextSibling;

                    //add the child to the parent, before the refNode if defined or as last parent's child
                    isset($refNode) ? $parent->insertBefore($relation, $refNode) : $parent->appendChild($relation);

                    $root = $class;
                    $this->ajaxXpath = $relation->getNodePath();

                    $this->getChildTree($root);
                    break;
                case EnumChildtype::$RELATIONTYPE:
                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->name, $result->id, EnumChildtype::$RELATIONTYPE, $result->guid, $result->lowerbound, $result->upperbound);
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:show', 'embed');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:actuate', 'onLoad');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:type', 'simple');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '');
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':resourcetypeId', $result->resourcetype_id);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':relationId', $result->id);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':accessscopeLimitation', $result->accessscope_limitation);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':scopeId', $scope_id);

                    if (isset($result->classass_id)) {
                        $class = $this->getDomElement($result->classass_ns_uri, $result->classass_ns_prefix, $result->classass_name, $result->classass_id, EnumChildtype::$CLASS, $result->guid);

                        $relation->appendChild($class);
                    }

                    $coll = $this->domXpathStr->query($parent->getNodePath() . '/*[@catalog:dbid="' . $_GET['relid'] . '" and @catalog:childtypeId="3"]');

                    //try to set the refNode, depending on the prevSibl existence
                    $refNode = $coll->item($coll->length - 1)->nextSibling;

                    //add the child to the parent, before the refNode if defined or as last parent's child
                    isset($refNode) ? $parent->insertBefore($relation, $refNode) : $parent->appendChild($relation);

                    $this->ajaxXpath = $relation->getNodePath();

                    if (isset($class)) {
                        $this->getChildTree($class);
                    } else {
                        $this->getChildTree($relation);
                    }

                    $root = $relation;

                    break;
                case EnumChildtype::$ATTRIBUT:
                    $v = $this->domXpathStr->query('descendant::*[@catalog:relid="' . $_GET['relid'] . '"]');
                    $attribute = $this->domXpathStr->query('descendant::*[@catalog:relid="' . $_GET['relid'] . '"]')->item($v->length -1);
                    $cloned = $attribute->cloneNode(true);
                    $clearNode = $this->clearNodeValue($cloned);

                    switch ($scope_id) {
                        // readOnly
                        case 2:
                        case 3:
                            $clearNode->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', "true");
                            break;
                    }
                    $coll = $this->domXpathStr->query($parent->getNodePath() . '/*[@catalog:dbid="' . $_GET['relid'] . '" and @catalog:childtypeId="2"]');

                    //try to set the refNode, depending on the prevSibl existence
                    $item = $coll->item($coll->length - 1);
                    if (isset($item)) {
                        $refNode = $item->nextSibling;
                        //add the child to the parent, before the refNode if defined or as last parent's child
                        isset($refNode) ? $parent->insertBefore($clearNode, $refNode) : $parent->appendChild($clearNode);
                    } else {
                        $parent->appendChild($clearNode);
                    }
                    $parent->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');
                    $root = $cloned;
                    $this->ajaxXpath = $cloned->getNodePath();
                    break;
            }
        }

        $rootXpath = $root->getNodePath();
        $this->setDomXpathStr();

        if (isset($this->csw)) {
            $this->setDomXpathCsw();
            if (!$this->cleanStructure()) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_EASYSDI_CATALOG_METADATA_XML_IMPORT_ERROR'), 'error');
            }
        }
        $this->session->set('structure', serialize($this->structure->saveXML()));
        $this->setDomXpathStr();
        $form = $this->buildForm($this->domXpathStr->query($rootXpath)->item(0));
        return $form;
    }

    /**
     * Recursive method of constructing the tree
     * 
     * @param DOMElement $parent Current element.
     */
    private function getChildTree(DOMElement $parent, $level = 1) {

        $childs = $this->getChildNode($parent, $level);

        foreach ($childs as $child) {
            $parent->appendChild($child);

            /* @var $element DOMElement */
            foreach ($child->childNodes as $element) {

                switch ($element->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                    case EnumChildtype::$CLASS:
                        $this->getChildTree($element, $level + 1);
                        break;
                    case EnumChildtype::$RELATIONTYPE:
                        $this->getChildTree($element, $level + 1);
                        break;
                }
            }
        }
    }

    /**
     * Retrieves the children of the element passed as parameter.
     * 
     * @param DOMElement $parent
     * @return DOMElement[] Childs of parent relation.
     */
    private function getChildNode(DOMElement $parent, $level) {
        $childs = array();

        $this->setDomXpathStr();

        if ($parent->parentNode->nodeType == XML_ELEMENT_NODE) {
            $occurance = 0;
            switch ($parent->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$RELATIONTYPE:
                    $relationExist = $parent;
                    $lowerbound = $parent->getAttributeNS($this->catalog_uri, 'lowerbound');
                    if (isset($this->domXpathCsw)) {
                        $occurance = $this->domXpathCsw->query('/*' . $parent->getNodePath())->length;
                    }
                    break;
                case EnumChildtype::$CLASS:
                default :
                    $relationExist = $parent->parentNode;
                    $lowerbound = $parent->parentNode->getAttributeNS($this->catalog_uri, 'lowerbound');
                    if (isset($this->domXpathCsw)) {
                        if ($parent->getAttributeNS($this->catalog_uri, 'dbid') == 0) {
                            $occurance = $this->domXpathCsw->query('/*' . $this->removeIndex($parent->parentNode->getNodePath()))->length;
                        } else {
                            $occurance = $this->domXpathCsw->query('/*' . $this->removeIndex($parent->getNodePath()))->length;
                        }
                    }
                    break;
            }

            if (!$relationExist->hasAttributeNS($this->catalog_uri, 'exist')) {
                if ($lowerbound > 0 || $occurance > 0) {
                    $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '1');
                } else {
                    $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '0');
                    return $childs;
                }
            }

            if ($parent->getAttributeNS($this->catalog_uri, 'childtypeId') == EnumChildtype::$RELATIONTYPE) {
                return $childs;
            }

            // Specific case for Stereotype boundary
            $stereotype_id = $parent->getAttributeNS($this->catalog_uri, 'stereotypeId');

            if ($stereotype_id == EnumStereotype::$GEOGRAPHICEXTENT) {
                $occurance = $this->domXpathStr->query($relationExist->getNodePath())->length;
                $relationExist->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', '0');
            }
        }
        $parent_id = $parent->getAttributeNS($this->catalog_uri, 'dbid');
        $formStereotype = new FormStereotype();

        $query = $this->getRelationQuery();
        $query->where('r.parent_id = ' . $query->quote($parent_id));
        $query->where('r.state = 1');
        $query->order('r.ordering');
        $query->order('r.name');

        $this->db->setQuery($query);
        $results = $this->db->loadObjectList();

        foreach ($results as $result) {

            $scope_id = $this->getFieldScope($this->item->id, $result->relationscope_id, $result->editorrelationscope_id);

            switch ($result->childtype_id) {
                case EnumChildtype::$CLASS:
                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->isocode, $result->id, EnumChildtype::$RELATION, $result->guid, $result->lowerbound, $result->upperbound);
                    $class = $this->getDomElement($result->class_ns_uri, $result->class_ns_prefix, $result->class_isocode, $result->class_id, EnumChildtype::$CLASS, $result->class_guid, null, null, $result->class_stereotype_id);

                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':level', $level);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':scopeId', $scope_id);

                    switch ($result->class_stereotype_id) {
                        case EnumStereotype::$GEOGRAPHICEXTENT:
                            $params = array();
                            $params['stereotype_id'] = $result->class_stereotype_id;
                            $params['upperbound'] = $result->upperbound;
                            $params['lowerbound'] = $result->lowerbound;
                            $params['id'] = $result->id;
                            $params['relGuid'] = $result->guid;

                            foreach ($formStereotype->getStereotype(JArrayHelper::toObject($params)) as $st) {
                                $relation->appendChild($this->structure->importNode($st, true));
                            }

                            break;
                        default :
                            $relation->appendChild($class);

                            if (isset($result->classass_id)) {
                                $classass = $this->getDomElement($result->classass_ns_uri, $result->classass_ns_prefix, $result->classass_name, $result->classass_id, EnumChildtype::$CLASS, $result->guid);
                                $relation->appendChild($classass);
                            }
                            break;
                    }

                    $childs[] = $relation;

                    break;

                case EnumChildtype::$ATTRIBUT:
                    $attribute = $this->getDomElement($result->attribute_ns_uri, $result->attribute_ns_prefix, $result->attribute_isocode, $result->attribute_id, EnumChildtype::$ATTRIBUT, $result->attribute_guid, $result->lowerbound, $result->upperbound, $result->stereotype_id, $result->rendertype_id);
                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'scopeId', $scope_id);
                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'relGuid', $result->guid);
                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'relid', $result->id);
                    $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'maxlength', $result->attribute_length);
                    if ($scope_id == 2 || $scope_id == 3) {
                        $attribute->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':' . 'readonly', "true");
                    }

                    if ($result->stereotype_id == 6) {
                    //Get the default value. 
                        if (null !== $defaultvalue = $this->getDefaultValue($result->id, null, true)) {
                            if (is_array($defaultvalue)) {
                                //Generate a childNode for each default value
                                foreach ($defaultvalue as $value) {
                                    $defaultAttribute = $attribute->cloneNode(true);
                                    $defaultResult = clone $result;
                                    $defaultResult->defaultvalue = $value;
                                    foreach ($formStereotype->getStereotype($defaultResult) as $st) {
                                        $defaultAttribute->appendChild($this->structure->importNode($st, true));
                                    }
                                    array_push($childs, $defaultAttribute);
                                }
                            } else {
                            $result->defaultvalue = $defaultvalue;
                        }
                    }

                    //Dummy node : allows the addition of a new selected option when all defaults were deselected and remove from the XML                    
                        if (isset($result->defaultvalue) && $result->upperbound > 1 && $scope_id == 1) {
                        $dummyAttribute = $attribute->cloneNode(true);
                        $dummyresult = clone $result;
                        $dummyresult->defaultvalue = null;
                        foreach ($formStereotype->getStereotype($dummyresult) as $st) {
                            $dummyAttribute->appendChild($this->structure->importNode($st, true));
                        }
                            array_push($childs, $dummyAttribute);
                    }
                    }

                    foreach ($formStereotype->getStereotype($result) as $st) {
                        $attribute->appendChild($this->structure->importNode($st, true));
                    }

                    array_push($childs, $attribute);
                    break;
                case EnumChildtype::$RELATIONTYPE:
                    $relation = $this->getDomElement($result->uri, $result->prefix, $result->isocode, $result->id, EnumChildtype::$RELATIONTYPE, $result->guid, $result->lowerbound, $result->upperbound);
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:show', 'embed');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:actuate', 'onLoad');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:type', 'simple');
                    $relation->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', '');
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':resourcetypeId', $result->resourcetype_id);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':relationId', $result->id);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':accessscopeLimitation', $result->accessscope_limitation);
                    $relation->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':scopeId', $scope_id);
                    if (isset($result->classass_id)) {
                        $class = $this->getDomElement($result->classass_ns_uri, $result->classass_ns_prefix, $result->classass_name, $result->classass_id, EnumChildtype::$CLASS, $result->guid);
                        $relation->appendChild($class);
                    } else {
                        $class = $this->structure->createElementNS($this->catalog_uri, $this->catalog_prefix . ':dummy');
                        $class->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':exist', 0);
                        $class->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', 1);
                        $class->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':dbid', 0);
                        $relation->appendChild($class);
                    }
                    $childs[] = $relation;
                    break;
            }
        }
        return $childs;
    }

    /**
     * This method creates a DOM element.
     * 
     * @param string $uri Namespace URI
     * @param string $prefix Namespace prefix
     * @param string $name Name of the element.
     * @param string $id Id for current element
     * @param int $childtypeId Childtype @see EnumChildtype
     * @param string $guid Guid for current element
     * @param int $lowerbound Minimum occurrence for current element.
     * @param int $upperbound Maximum occurrence for current element.
     * @param int $stereotypeId Stereotype @see EnumStereotype
     * @param int $rendertypeId Rendertype @see EnumRendertype
     * 
     * @return DOMElement 
     */
    private function getDomElement($uri, $prefix, $name, $id, $childtypeId, $guid, $lowerbound = null, $upperbound = null, $stereotypeId = null, $rendertypeId = null) {
        $element = $this->structure->createElementNS($uri, $prefix . ':' . $name);

        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':dbid', $id);
        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':id', $guid);
        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':childtypeId', $childtypeId);
        $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':index', '1');

        if (isset($lowerbound)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':lowerbound', $lowerbound);
        }
        if (isset($upperbound)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':upperbound', $upperbound);
        }
        if (isset($rendertypeId)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':rendertypeId', $rendertypeId);
        }
        if (isset($stereotypeId)) {
            $element->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':stereotypeId', $stereotypeId);
        }

        return $element;
    }

    /**
     * Clone structure and remove from the clone node that not in csw domdocument and get the value from the csw
     * 
     */
    private function cleanStructure() {
        //clone the structure - having a document between the structure and the csw let us do the bi-directional merge
        $clone_structure = new DOMDocument('1.0', 'utf-8');
        $clone_structure->loadXML($this->structure->saveXML());
        $domXpathClone = new DOMXPath($clone_structure);

        $this->registerNamespace($domXpathClone);

        $coll = $domXpathClone->query('//*[@catalog:childtypeId="' . EnumChildtype::$CLASS . '"]|//*[@catalog:childtypeId="' . EnumChildtype::$ATTRIBUT . '"]|//*[@catalog:childtypeId="' . EnumChildtype::$RELATIONTYPE . '"]');

        for ($j = 0; $j < $coll->length; $j++) {
            /* @var $node DOMElement */
            $node = $coll->item($j);
            $childType = $node->getAttributeNs($this->catalog_uri, 'childtypeId');
            $nodePath = $node->getNodePath();

            //Multiselect with default value will return from "$this->structure" several node indexed with [1], [2]
            //Because each default value will have a nodePath indexed AND a dummy empty node will be also present
            //So at least, with a multiselect with one default value, 2 nodes will be present in the XMl structure
            //Need to handle just one
            if((substr($nodePath, -1) === "]")){
                $start = strrpos($nodePath, "[");
                $nodePath = substr($nodePath, 0,  $start);
            }

            if ($childType == EnumChildtype::$CLASS) {
                $paths = explode('/', $nodePath);
                $index = count($paths) - 2;
                $index_node_name = $this->removeIndex($paths[$index]);
                $paths[$index] = $index_node_name;
                $nodePath = implode('/', $paths);
            }

            $occurance = $this->domXpathCsw->query('/*' . $nodePath)->length;
            $occurance_clone = $domXpathClone->query($nodePath)->length;

            // if occurance == 0 remove node from clone
            if ($occurance == 0) {
                if (!method_exists($node->parentNode, 'getAttributeNs'))
                    return false;

                $parentChildType = @$node->parentNode->getAttributeNs($this->catalog_uri, 'childtypeId');

                //look for the ancestor under which we can clean the structure
                while (!isset($node->nextSibling) && !isset($node->previousSibling) && $parentChildType != EnumChildtype::$RELATIONTYPE) {
                    $node = $node->parentNode;
                }

                //remove the child
                $parent = $node->parentNode;
                $parent->removeChild($node);
                $clone_structure->normalizeDocument();

                //reset collection and loop
                $coll = $domXpathClone->query('//*[@catalog:childtypeId="' . EnumChildtype::$CLASS . '"]|//*[@catalog:childtypeId="' . EnumChildtype::$ATTRIBUT . '"]|//*[@catalog:childtypeId="' . EnumChildtype::$RELATIONTYPE . '"]');
                $j = 0;
                continue;
            }

            $childtype = $node->getAttributeNS($this->catalog_uri, 'childtypeId');
            if ($childtype == EnumChildtype::$CLASS) {
                $node = $node->parentNode;
            }

            if ($occurance < $occurance_clone) {//Default values are present in the structure, but CSW metadata already has value(s) selected in fewer occurance 
                for ($i = $occurance_clone; $i > $occurance + 1; $i--) {
                    //remove the child
                    $parent = $node->parentNode;
                    if (isset($node->nextSibling)) {
                        $node = $node->previousSibling;
                    }
                    $parent->removeChild($node->nextSibling);
                    $clone_structure->normalizeDocument();
                }
            } else {
            for ($i = $occurance_clone; $i < $occurance; $i++) {
                $cloneNode = $node->cloneNode(true);
                $cloneNode->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':index', $i + 1);
                isset($node->nextSibling) ? $node->parentNode->insertBefore($cloneNode, $node->nextSibling) : $node->parentNode->appendChild($cloneNode);
            }
        }
        }

        $this->getValue($clone_structure->firstChild);
        $this->mergeToStructure($clone_structure, $domXpathClone);
        return true;
    }

    /**
     * Merge structure clone to original structure
     * 
     * @param DOMDocument $clone
     * @param DOMXPath $domXpathClone
     * 
     */
    private function mergeToStructure(DOMDocument $clone, DOMXPath $domXpathClone) {
        /* @var $node DOMElement */
        foreach ($this->domXpathStr->query('//*[@catalog:childtypeId="' . EnumChildtype::$CLASS . '"]|//*[@catalog:childtypeId="' . EnumChildtype::$ATTRIBUT . '"]') as $node) {
            if (strpos($node->getNodePath(), 'gmd:extent') > -1) {
                $breakpoint = true;
            }
            if ($domXpathClone->query($node->getNodePath())->length == 0) {
                do {
                    $childToImport = $node;
                    $node = $node->parentNode;
                    $id = $node->getAttributeNS($this->catalog_uri, 'id');
                } while ($domXpathClone->query($node->getNodePath() . '[@catalog:id="' . $id . '"]')->length == 0);

                //$target = $domXpathClone->query($node->getNodePath())->item(0);
                $targets = $domXpathClone->query($node->getNodePath());
                $target = $targets->item(0);
                $prevSibl = $this->domXpathStr->query($childToImport->getNodePath())->item(0)->previousSibling;
                if (isset($prevSibl)) {
                    $coll = $domXpathClone->query($prevSibl->getNodePath());

                    //try to set the refNode, depending on the prevSibl existence
                    //$refNode = $coll->length > 0 ? $coll->item($coll->length - 1)->nextSibling : $target->firstChild;
                    if ($coll->length > 0) {
                        $refNode = $coll->item($coll->length - 1)->nextSibling;
                        //$target = $targets->item($coll->length - 1);
                    } else {
                        $refNode = $target->firstChild;
                    }
                } else {
                    $refNode = $target->firstChild;
                }

                if (empty($refNode)) {
                    $breakpoint = true;
                }

                try {
                    //add the child to the parent, before the refNode if defined or as last parent's child
                    if (isset($refNode)) {
                        $target->insertBefore($clone->importNode($childToImport, true), $refNode);
                    } else {
                        $target->appendChild($clone->importNode($childToImport, true));
                    }
                } catch (Exception $exc) {
                    $exc->getTraceAsString();
                }
            }
        }

        //replace the structure with the clone
        $this->structure->loadXML($clone->saveXML());
        $breakpoint = true;
    }

    /**
     * This method adds the required number of occurrences of a relation.
     */
    private function mergeCsw() {

        $exclude_node = array('gmd:geographicElement');

        foreach ($this->domXpathStr->query('//*[@catalog:childtypeId="0"]|//*[@catalog:childtypeId="2"]|//*[@catalog:childtypeId="3"]') as $relation) {
            $xpath = $relation->firstChild->getNodePath();
            $nbr = $this->domXpathCsw->query('/*' . $xpath)->length;

            if ($nbr == 0) {
                continue;
            }

            if (!in_array($relation->nodeName, $exclude_node)) {
                $hasSibling = isset($relation->nextSibling);
                if ($hasSibling) {
                    $nextSibling = $relation->nextSibling;
                }
                for ($i = 1; $i < $nbr; $i++) {
                    $clone = $relation->cloneNode(true);
                    $clone->setAttributeNS($this->catalog_uri, $this->catalog_prefix . ':index', $i + 1);
                    if ($hasSibling) {
                        $relation->parentNode->insertBefore($clone, $nextSibling);
                    } else {
                        $relation->parentNode->appendChild($clone);
                    }
                }
            }
        }

        $this->getValue($this->structure->getElementsByTagNameNS('*', '*')->item(0));
    }

    /**
     * This method retrieves the value of an attribute using XPath.
     * 
     * @param DOMNode $child The current attribute.
     */
    private function getValue(DOMNode &$child) {
        if (strstr($child->getNodePath(), 'colors')) {
            $breakpoint = true;
        }

        foreach ($child->childNodes as $i => $node) {
            if ($this->hasChildElement($node)) {
                if ($node->getAttributeNS($this->catalog_uri, 'childtypeId') == EnumChildtype::$RELATIONTYPE) {
                    $nodeCsw = $this->domXpathCsw->query('/*' . $node->getNodePath())->item(0);
                    if (isset($nodeCsw)) {
                        $node->setAttributeNS('http://www.w3.org/1999/xlink', 'xlink:href', $nodeCsw->getAttributeNS('http://www.w3.org/1999/xlink', 'href'));
                    }
                }

                $this->getValue($node);
            } else {
                $nodeCsw = $this->domXpathCsw->query('/*' . $node->getNodePath())->item(0);
                if (isset($nodeCsw)) {
                    $node->nodeValue = htmlspecialchars($nodeCsw->nodeValue);

                    if (isset($nodeCsw->attributes)) {
                        foreach ($nodeCsw->attributes as $attribute) {
                            $node->setAttribute($attribute->nodeName, $attribute->nodeValue);
                        }
                    }
                }
            }
        }
    }

    /**
     * 
     * @param type $node
     * 
     * Check if node has DOMElement child
     */
    private function hasChildElement(DOMNode $node) {
        $hasElementChild = false;
        foreach ($node->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE) {
                $hasElementChild = true;
            }
        }

        return $hasElementChild;
    }

    /**
     * Returns a form structure in Joomla format
     * 
     * @return string Form structure to Joomla format.
     */
    private function buildForm(DOMElement $root) {
        $form = $this->form->createElement('form');
        $form->appendChild($this->getHiddenFields());

        $fieldset = $this->form->createElement('fieldset');
        $fieldset->setAttribute('name', 'nonhidden');

        switch ($root->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$ATTRIBUT:
            case EnumChildtype::$RELATIONTYPE:
                $query = 'descendant-or-self::*[@catalog:childtypeId="2"]|descendant-or-self::*[@catalog:childtypeId="3"]';
                break;

            default:
                $query = 'descendant::*[@catalog:childtypeId="2"]|descendant::*[@catalog:childtypeId="3"]';
                break;
        }

        foreach ($this->domXpathStr->query($query, $root) as $attribute) {
            $attributes = $this->getFormField($attribute);
            if ($attributes == null) {
                continue;
            }
            if (is_array($attributes)) {
                foreach ($attributes as $attr) {
                    $fieldset->appendChild($attr);
                }
            } else {
                $fieldset->appendChild($attributes);
            }
        }

        $form->appendChild($fieldset);
        $this->form->appendChild($form);
        $this->form->formatOutput = true;
        return $this->form->saveXML();
    }

    /**
     * Creates hidden fields added to the form.
     * 
     * @return DOMElement
     */
    private function getHiddenFields() {
        $fieldset = $this->form->createElement('fieldset');
        $fieldset->setAttribute('name', 'hidden');

        $id = $this->form->createElement('field');
        $id->setAttribute('name', 'id');
        $id->setAttribute('type', 'hidden');
        $id->setAttribute('filter', 'safehtml');

        $guid = $this->form->createElement('field');
        $guid->setAttribute('name', 'guid');
        $guid->setAttribute('type', 'hidden');
        $guid->setAttribute('filter', 'safehtml');

        $metadatastateId = $this->form->createElement('field');
        $metadatastateId->setAttribute('name', 'metadatastate_id');
        $metadatastateId->setAttribute('type', 'hidden');
        $metadatastateId->setAttribute('filter', 'safehtml');

        $published = $this->form->createElement('field');
        $published->setAttribute('name', 'published');
        $published->setAttribute('type', 'hidden');
        $published->setAttribute('filter', 'safehtml');

        $fieldset->appendChild($id);
        $fieldset->appendChild($guid);
        $fieldset->appendChild($metadatastateId);
        $fieldset->appendChild($published);

        return $fieldset;
    }

    /**
     * Returns a field corresponding to RenderType Joomla format.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormField(DOMElement $attribute) {
        switch ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                return $this->getRelationType($attribute);
            case EnumChildtype::$ATTRIBUT:
                switch ($attribute->getAttributeNS($this->catalog_uri, 'rendertypeId')) {
                    case EnumRendertype::$TEXTBOX:
                        return $this->getFormTextBoxField($attribute);
                    case EnumRendertype::$TEXTAREA:
                        return $this->getFormTextAreaField($attribute);
                    case EnumRendertype::$CHECKBOX:
                        return $this->getFormCheckboxField($attribute);
                    case EnumRendertype::$RADIOBUTTON:
                        return $this->getFormRadioButtonField($attribute);
                    case EnumRendertype::$LIST:
                        return $this->getFormListField($attribute);
                    case EnumRendertype::$DATE:
                        return $this->getFormDateField($attribute);
                    case EnumRendertype::$DATETIME:
                        return $this->getFormDateField($attribute);
                    case EnumRendertype::$GEMET:
                        return $this->getFormGemetField($attribute);
                    case EnumRendertype::$HIDDEN:
                        return $this->getFormHiddenField($attribute);
                    case EnumRendertype::$UPLOAD:
                    case EnumRendertype::$URL:
                    case EnumRendertype::$UPLOADANDURL:
                        return $this->getFormFileField($attribute);
                }
                break;
            default:
                break;
        }
        return null;
    }

    /**
     * Create a field of type text.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormTextBoxField(DOMElement $attribute) {

        $maxlength = $attribute->getAttributeNS($this->catalog_uri, 'maxlength');
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $relId = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $label = $attribute->getAttributeNS($this->catalog_uri, 'label');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $fields = array();
        $field = $this->form->createElement('field');

        $validator = $this->getValidatorClass($attribute);

        $field->setAttribute('type', 'text');
        $field->setAttribute('class', $validator);
        if (strpos($validator, 'required') !== false) {
            $field->setAttribute('labelclass', 'labelrequired');
        }
        if ($maxlength > 0) {
            $field->setAttribute('maxlength', $maxlength);
        }
        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        if ($guid != '') {
            if ($this->domXpathStr->query('*/*/*', $attribute)->length > 0) {
                $field->setAttribute('label', EText::_($guid) . ' (' . $this->ldao->getDefaultLanguage()->value . ')');
                $field->setAttribute('default', $this->getDefaultValue($relId, $attribute->firstChild->nodeValue, FALSE, $this->ldao->getDefaultLanguage()->id));
            } else {
                $field->setAttribute('label', EText::_($guid));
                $field->setAttribute('default', $this->getDefaultValue($relId, $attribute->firstChild->nodeValue));
            }
        } else {
            $field->setAttribute('label', JText::_($label));
            $field->setAttribute('default', $this->getDefaultValue($relId, $attribute->firstChild->nodeValue));
        }

        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $fields[] = $field;

        foreach ($this->domXpathStr->query('*/*/*', $attribute) as $i18nChild) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'text');
            $field->setAttribute('class', $validator);

            if ($maxlength > 0) {
                $field->setAttribute('maxlength', $maxlength);
            }
            if ($readonly) {
                $field->setAttribute('readonly', 'true');
            }

            $localeValue = str_replace('#', '', $i18nChild->getAttribute('locale'));
            $field->setAttribute('default', $this->getDefaultValue($relId, $i18nChild->nodeValue, FALSE, $this->ldao->getByIso3166($localeValue)->id));
            $field->setAttribute('name', FormUtils::serializeXpath($i18nChild->getNodePath()) . $i18nChild->getAttribute('locale'));
            $field->setAttribute('label', EText::_($guid) . ' (' . $this->ldao->getByIso3166($localeValue)->value . ')'); //
            $description = EText::_($guid, 2);
            if (!empty($description)) {
                $field->setAttribute('description', $description);
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type textarea.
     * 
     * @param DOMElement $attribute 
     * @return DOMElement
     *
     */
    private function getFormTextAreaField(DOMElement $attribute) {
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $validator = $this->getValidatorClass($attribute);
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $fields = array();
        $field = $this->form->createElement('field');

        $field->setAttribute('type', 'textarea');
        $field->setAttribute('class', $validator);
        if (strpos($validator, 'required') !== false) {
            $field->setAttribute('labelclass', 'labelrequired');
        }
        $field->setAttribute('rows', 5);
        $field->setAttribute('cols', 5);

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));

        if ($this->domXpathStr->query('*/*/*', $attribute)->length > 0) {
            $field->setAttribute('label', EText::_($guid) . ' (' . $this->ldao->getDefaultLanguage()->value . ')');
            $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, FALSE, $this->ldao->getDefaultLanguage()->id));
        } else {
            $field->setAttribute('label', EText::_($guid));
            $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue));
        }


        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $fields[] = $field;

        foreach ($this->domXpathStr->query('*/*/*', $attribute) as $i18nChild) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'textarea');
            $field->setAttribute('class', $validator);
            $field->setAttribute('rows', 5);
            $field->setAttribute('cols', 5);

            if ($readonly) {
                $field->setAttribute('readonly', 'true');
            }

            $localeValue = str_replace('#', '', $i18nChild->getAttribute('locale'));
            $field->setAttribute('default', $this->getDefaultValue($relid, $i18nChild->nodeValue, FALSE, $this->ldao->getByIso3166($localeValue)->id));
            $field->setAttribute('name', FormUtils::serializeXpath($i18nChild->getNodePath()) . $i18nChild->getAttribute('locale'));
            $field->setAttribute('label', EText::_($guid) . ' (' . $this->ldao->getByIso3166($localeValue)->value . ')');
            $description = $this->getDescription($attribute_guid, $guid);
            if (!empty($description)) {
                $field->setAttribute('description', $description);
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type checkboxes.
     * 
     * @param DOMElement $attribute 
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormCheckboxField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $allValues = $this->domXpathStr->query('child::*[@catalog:relid="' . $relid . '"]', $attribute->parentNode);
        $default = array();
        foreach ($allValues as $node) {
            if ($node->firstChild->hasAttribute('codeListValue')) {
            $default[] = $node->firstChild->getAttribute('codeListValue');
            } else {
                $default[] = $node->firstChild->nodeValue;
        }
        }

        $field->setAttribute('type', 'checkboxes');
        $name = FormUtils::removeIndexToXpath(FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('name', $name);

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('multiple', 'true');
        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $field->setAttribute('default', $this->getDefaultValue($relid, implode(',', $default), true));

        $i = 1;
        foreach ($this->getAttributOptions($attribute) as $opt) {
            $option = $this->form->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->value);
            $option->setAttribute('onclick', "addOrRemoveCheckbox(this.id," . $relid . ",'" . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . "','" . FormUtils::serializeXpath($attribute->getNodePath()) . "')");

            $field->appendChild($option);
            $i++;
        }

        return $field;
    }

    /**
     * Create a field of type radio.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormRadioButtonField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $field->setAttribute('type', 'radio');
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->getAttribute('codeListValue')));
        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        foreach ($this->getAttributOptions($attribute) as $opt) {
            $option = $this->form->createElement('option', EText::_($opt->guid));
            $option->setAttribute('value', $opt->value);

            $field->appendChild($option);
        }

        return $field;
    }

    /**
     * Create a field of type grouplist or list.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormListField(DOMElement $attribute) {
        $field = $this->form->createElement('field');
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $label = $attribute->getAttributeNS($this->catalog_uri, 'label');
        $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $validator = $this->getValidatorClass($attribute);
        $field->setAttribute('class', $validator);
        if (strpos($validator, 'required') !== false) {
            $field->setAttribute('labelclass', 'labelrequired');
        }

        if ($upperbound > 1) {
            $name = FormUtils::removeIndexToXpath(FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
            $field->setAttribute('name', $name);
            $field->setAttribute('multiple', 'true');
            $field->setAttribute('type', 'MultipleDefaultList');
        } else {
            $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        }

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $defaultvalues = null;
        foreach ($this->getAttributOptions($attribute) as $opt) {
            switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
                case EnumStereotype::$LOCALECHOICE:
                    $group = $this->form->createElement('group');
                    $group->setAttribute('label', EText::_($opt->guid));

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid, 2));
                    } else {
                        $option = $this->form->createElement('option');
                    }

                    $option->setAttribute('value', $opt->guid);
                    $field->setAttribute('type', 'groupedlist');
                    $field->setAttribute('label', EText::_($guid));

                    if ($upperbound > 1) {
                        $allValues = $this->domXpathStr->query('descendant::*[@catalog:relid="' . $relid . '"]', $attribute->parentNode->parentNode);
                        $default = array();
                        foreach ($allValues as $node) {
                            if (!empty($node->firstChild->nodeValue)) {
                                $default[] = $this->getDefaultValue($relid, $this->getGuidFromLocaleValue($relid, $node->firstChild->nodeValue), true);
                            }
                        }
                        $field->setAttribute('default', implode(',', $default));
                        $field->setAttribute('type', 'MultipleDefaultList');
                        $field->appendChild($option);
                    } else {
                        $field->setAttribute('default', $this->getDefaultValue($relid, $this->getGuidFromLocaleValue($relid, $attribute->firstChild->nodeValue), true));
                        $group->appendChild($option);
                        $field->appendChild($group);
                    }
                    break;
                case EnumStereotype::$BOUNDARY:
                    $option = $this->form->createElement('option', $opt->name);
                    $option->setAttribute('value', $opt->value);
                    $field->appendChild($option);
					if ($guid != '') {
                        $field->setAttribute('label', EText::_($guid));
                    } else {
                        $field->setAttribute('label', JText::_($label));
                    }

                    if ($upperbound > 1) {
                        if ($defaultvalues == null) {//Execute only once this part : it's the selected options and general field's informations definition
							$allValues = $this->domXpathStr->query('descendant::*[@catalog:relid="' . $relid . '"]', $attribute->parentNode->parentNode->parentNode);
							$defaultvalues = array();
							foreach ($allValues as $node) {
								if (!empty($node->firstChild->nodeValue)) {
										$defaultvalues[] = $node->firstChild->nodeValue;
								}
							}
							$name = FormUtils::removeIndexToXpath(FormUtils::serializeXpath($attribute->firstChild->getNodePath()), 12, 15);
							$field->setAttribute('name', $name);
							$field->setAttribute('default', $this->getDefaultValue($relid, implode(',', $defaultvalues), true));
							$field->setAttribute('css', 'sdi-multi-extent-select');
							$field->setAttribute('type', 'MultipleDefaultList');                            
                        }
                    } else {
                        $field->setAttribute('onchange', 'setBoundary(\'' . FormUtils::serializeXpath($attribute->parentNode->getNodePath()) . '\',this.value);');
                        $field->setAttribute('default', $attribute->firstChild->nodeValue);
                        $field->setAttribute('type', 'list');
                    }
                    break;
                case EnumStereotype::$TEXTCHOICE:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($guid));
                    $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, true));

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid));
                    } else {
                        $option = $this->form->createElement('option');
                    }
                    $option->setAttribute('value', $opt->value);

                    $field->appendChild($option);
                    break;
                default:
                    $field->setAttribute('type', 'list');
                    $field->setAttribute('label', EText::_($guid));
                    if ($upperbound == 1) {
                        if ($attribute->firstChild->hasAttribute('codeListValue')) {
                            $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->getAttribute('codeListValue'), true));
                        } else {
                            $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, true));
                        }
                    }
                    if ($upperbound > 1) {
                        $allValues = $this->domXpathStr->query('child::*[@catalog:relid="' . $relid . '"]', $attribute->parentNode);
                        $default = array();
                        foreach ($allValues as $node) {
                            if ($node->firstChild->hasAttribute('codeListValue')) {
                                $default[] = $node->firstChild->getAttribute('codeListValue');
                            } else {
                                $default[] = $node->firstChild->nodeValue;
                            }
                        }
                        $field->setAttribute('type', 'MultipleDefaultList');
                        $field->setAttribute('default', $this->getDefaultValue($relid, implode(',', $default), true));
                    }

                    if ($opt->guid != '') {
                        $option = $this->form->createElement('option', EText::_($opt->guid));
                    } else {
                        $option = $this->form->createElement('option');
                    }
                    $option->setAttribute('value', $opt->value);
                    $field->appendChild($option);
                    break;
            }
        }
        return $field;
    }

    /**
     * Create a field of type calendar.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     * @since 4.0.0
     */
    private function getFormDateField(DOMElement $attribute) {
        $field = $this->form->createElement('field');

        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        if ($readonly) {
            $field->setAttribute('readonly', 'true');
        }

        $validator = $this->getValidatorClass($attribute);

        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('type', 'text');
        $field->setAttribute('class', $validator);
        if (strpos($validator, 'required') !== false) {
            $field->setAttribute('labelclass', 'labelrequired');
        }
        //$field->setAttribute('format', '%Y-%m-%d');
        $field->setAttribute('label', EText::_($guid)); //
        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $field->setAttribute('default', $this->getDefaultValue($relid, substr($attribute->firstChild->nodeValue, 0, 10)));
        return $field;
    }

    private function getFormDateTimeField(SdiRelation $rel) {
        //TODO: Not Implemented
    }

    /**
     * Create a field of type list specific for GEMET stereotype.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormGemetField(DOMElement $attribute) {
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $label = $attribute->getAttributeNS($this->catalog_uri, 'label');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $fields = array();

        $field = $this->form->createElement('field');

        $field->setAttribute('type', 'list');
        $field->setAttribute('multiple', 'true');
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        if ($guid != '') {
            $field->setAttribute('label', EText::_($guid));
        } else {
            $field->setAttribute('label', JText::_($label));
        }
        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $defaults = array();
        foreach ($this->domXpathStr->query('descendant::gco:CharacterString', $attribute->parentNode) as $element) {
            $defaults[] = $element->nodeValue;
        }

        foreach ($defaults as $opt) {
            $option = $this->form->createElement('option', $opt);
            $option->setAttribute('value', $opt);
            $field->appendChild($option);
        }

        $fields[] = $field;

        foreach ($this->ldao->getSupported() as $key => $lang) {
            $field = $this->form->createElement('field');

            $field->setAttribute('type', 'list');
            $field->setAttribute('multiple', 'true');
            $field->setAttribute('name', FormUtils::serializeXpath($attribute->getNodePath() . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString#' . $key));
            $field->setAttribute('label', EText::_($guid) . ' ' . $this->ldao->getByIso3166($key)->value);
            $description = $this->getDescription($attribute_guid, $guid);
            if (!empty($description)) {
                $field->setAttribute('description', $description);
            }

            $defaults = array();
            foreach ($this->domXpathStr->query('*/*/*/*[@locale="#FR"]', $attribute->parentNode) as $element) {
                $defaults[] = $element->nodeValue;
            }

            foreach ($defaults as $opt) {
                $option = $this->form->createElement('option', $opt);
                $option->setAttribute('value', $opt);
                $field->appendChild($option);
            }

            $fields[] = $field;
        }

        return $fields;
    }

    /**
     * Create a field of type Hidden
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormHiddenField(DOMElement $attribute) {
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');
        $stereotypeid = $attribute->getAttributeNS($this->catalog_uri, 'stereotypeId');

        $field = $this->form->createElement('field');


        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('type', 'hidden');

        switch ($stereotypeid) {
            case 6:
            case 9:
                $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue, true));
                break;

            default:
                $field->setAttribute('default', $this->getDefaultValue($relid, $attribute->firstChild->nodeValue));
                break;
        }

        $field->setAttribute('label', 'label');

        return $field;
    }

    /**
     * Create a field of type list specific for Relationtype.
     * 
     * @param DOMElement $relationtype
     * @return DOMElement
     */
    private function getRelationType(DOMElement $relationtype) {
        $field = $this->form->createElement('field');

        $guid = $relationtype->getAttributeNS($this->catalog_uri, 'id');
        if (preg_match('/id=([a-z0-9-]*)/i', $relationtype->getAttributeNS('http://www.w3.org/1999/xlink', 'href'), $default) === 1) {
            $field->setAttribute('default', $default[1]);
        } else {
            $field->setAttribute('default', '');
        }

        $name = $relationtype->nodeName;

        $validator = $this->getValidatorClass($relationtype);

        $field->setAttribute('class', 'required ' . $validator);
        if (strpos($validator, 'required') !== false) {
            $field->setAttribute('labelclass', 'labelrequired');
        }
        $field->setAttribute('name', FormUtils::serializeXpath($relationtype->getNodePath()));
        $field->setAttribute('type', 'list');
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('description', $this->getDescription(null, $guid));

        foreach ($this->getAttributOptions($relationtype) as $opt) {
            $option = $this->form->createElement('option', $opt->name);
            $option->setAttribute('value', $opt->guid);

            $field->appendChild($option);
        }

        return $field;
    }

    /**
     * Create a field of type file.
     * 
     * @param DOMElement $attribute
     * @return DOMElement
     */
    private function getFormFileField(DOMElement $attribute) {
        $fields = array();

        $maxlength = $attribute->getAttributeNS($this->catalog_uri, 'maxlength');
        $readonly = $attribute->getAttributeNS($this->catalog_uri, 'readonly');
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'relGuid');
        $style = $attribute->getAttributeNS($this->catalog_uri, 'style');
        $attribute_guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

        $field = $this->form->createElement('field');

        $validator = $this->getValidatorClass($attribute);

        $field->setAttribute('class', 'required ' . $validator);
        if (strpos($validator, 'required') !== false) {
            $field->setAttribute('labelclass', 'labelrequired');
        }
        $field->setAttribute('type', 'text');
        $field->setAttribute('name', FormUtils::serializeXpath($attribute->firstChild->getNodePath()));
        $field->setAttribute('label', EText::_($guid));
        $field->setAttribute('default', $attribute->firstChild->nodeValue);
        $description = $this->getDescription($attribute_guid, $guid);
        if (!empty($description)) {
            $field->setAttribute('description', $description);
        }

        $field->setAttribute('readonly', 'true');

        $fields[] = $field;

        return $fields;
    }

    /**
     * applyAccessScopeLimitation
     * 
     * @param type $query
     * @param DOMElement|int $attribute
     * @return string - a where clause to add to the query
     */
    private function applyAccessScopeLimitation(&$query, $attribute = 0) {
        $asl = is_int($attribute) ? $attribute : $attribute->getAttributeNS($this->catalog_uri, 'accessscopeLimitation');
        switch ($asl) {
            case 0: // no limitation = nothing to do
                break;

            case 1: // limit to resources of the current user's organism
                //user's organism
                $organisms = $this->user->getMemberOrganisms();
                return "r.organism_id = " . (int) $organisms[0]->id;
            case 2: // limit to resources of the current metadata's organism
                $query->innerJoin('#__sdi_version v2 ON v2.id=' . (int) $this->item->version_id)
                        ->innerJoin('#__sdi_resource r2 ON r2.id=v2.resource_id');
                return 'r.organism_id=r2.organism_id';
            case 3: // both case 1 and case 2
                return '(' . $this->applyAccessScopeLimitation($query, 1) . ' OR ' . $this->applyAccessScopeLimitation($query, 2) . ')';
        }
    }

    /**
     * Retrieves the list of options for fields such list, checkbox and radio.
     * 
     * @param DOMElement $attribute
     * @return mixed List of options for list type fields, checkbox and radio.
     * @since 4.0
     */
    private function getAttributOptions(DOMElement $attribute) {
        $query = $this->db->getQuery(true);

        switch ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                $query->select("r.id, CONCAT(r.name, ' - ', o.name) as name , m.guid");
                $query->from('#__sdi_resource r');
                $query->innerJoin('#__sdi_version v on v.resource_id = r.id');
                $query->innerJoin('#__sdi_metadata m on m.version_id = v.id');
                $query->innerJoin('#__sdi_organism o on o.id = r.organism_id');
                $query->where('r.resourcetype_id = ' . (int) $attribute->getAttributeNS($this->catalog_uri, 'resourcetypeId'));
                $query->order('r.name ASC');

                //user's organism's categories
                $categories = $this->user->getMemberOrganismsCategoriesIds();
                array_push($categories, 0);

                //user's organism
                $organisms = $this->user->getMemberOrganisms();

                //apply resource's accessscope
                $query->where("("
                        . "r.accessscope_id = 1 "
                        . "OR (r.accessscope_id = 2 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.category_id IN (" . implode(',', $categories) . ") AND a.entity_guid = r.guid ) > 0) "
                        . "OR (r.accessscope_id = 3 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.organism_id = " . (int) $organisms[0]->id . " AND a.entity_guid = r.guid ) = 1) "
                        . "OR (r.accessscope_id = 4 AND (SELECT COUNT(*) FROM #__sdi_accessscope a WHERE a.user_id = " . (int) $this->user->id . " AND a.entity_guid = r.guid ) = 1)"
                        . ")"
                );

                $asl = $this->applyAccessScopeLimitation($query, $attribute);
                if (strlen($asl))
                    $query->where($asl);

                $this->db->setQuery($query);
                $result = $this->db->loadObjectList();

                $first = array('id' => '', 'guid' => '', 'name' => '');
                array_unshift($result, (object) $first);
                break;
            case EnumChildtype::$ATTRIBUT:
                switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
                    case EnumStereotype::$BOUNDARY:
                        return $this->getBoundaries();
                    default:
                        $query->select('id, guid, name, value');
                        $query->from('#__sdi_attributevalue');
                        $query->where('attribute_id = ' . $attribute->getAttributeNS($this->catalog_uri, 'dbid'));
                        $query->where('state = 1');
                        $query->order('ordering ASC');

                        $this->db->setQuery($query);
                        $result = $this->db->loadObjectList();
                        switch ($attribute->getAttributeNS($this->catalog_uri, 'rendertypeId')) {
                            case EnumRendertype::$CHECKBOX:
                            case EnumRendertype::$RADIOBUTTON:
                                //case EnumRendertype::$LIST: //TODO see https://forge.easysdi.org/issues/1171 : it breaks single selects
                                break;
                            default:
                                $first = array('id' => '', 'guid' => '', 'name' => '', 'value' => '');
                                array_unshift($result, (object) $first);
                                break;
                        }
                        break;
                }
                break;
        }
        return $result;
    }

    /**
     * 
     * @return type
     */
    private function getBoundaries() {
        if ($this->boundaryAttributeOptions == null) {//Execute only once
            $query = $this->db->getQuery(true);
            $query->select('b.id, b.guid, b.name as value, bc.guid as cat_guid, bc.name as cat_name');
            $query->from('#__sdi_boundary b');
            $query->innerJoin('#__sdi_boundarycategory bc ON b.category_id = bc.id');
            $query->where('b.state = 1');
            $query->order('b.name ASC');
            $this->db->setQuery($query);
            $this->boundaryAttributeOptions = $this->db->loadObjectList();

            foreach ($this->boundaryAttributeOptions as $r) {
                $r->name = EText::_($r->guid) . ' [' . EText::_($r->cat_guid, 1, $r->cat_name) . ']';
            }

            $first = array('id' => '', 'guid' => '', 'value' => '', 'name' => '');
            array_unshift($this->boundaryAttributeOptions, (object) $first);
        }
        return $this->boundaryAttributeOptions;
    }

    /**
     * Returns the CSS classes used for validation.
     * Exemple
     * <code>class = "required validate-sdilocale"</code>
     * 
     * @param DOMElement $attribute
     * @return string
     * @since 4.0.0
     */
    private function getValidatorClass(DOMElement $attribute) {
        $validator = array();
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'id');
        $patterns = $this->getPatterns();

        if ($attribute->getAttributeNS($this->catalog_uri, 'lowerbound') > 0) {
            $validator[] = 'required';
        }

        if (array_key_exists($guid, $patterns)) {
            if ($patterns[$guid]->attribute_pattern != '') {
                $validator[] = 'validate-sdi' . $patterns[$guid]->guid;
            } elseif ($patterns[$guid]->stereotype_pattern != '') {
                $validator[] = 'validate-sdi' . $patterns[$guid]->stereotype_name;
            }
        }

        return implode(' ', $validator);
    }

    /**
     * Get the list of the attribute and stereotype patterns.
     * 
     * @return array
     */
    private function getPatterns() {
        $query = $this->db->getQuery(true);

        $query->select('a.id, a.guid, a.pattern as attribute_pattern, s.defaultpattern as stereotype_pattern, s.value as stereotype_name');
        $query->from('#__sdi_relation as r');
        $query->innerJoin('#__sdi_attribute as a on r.attributechild_id = a.id');
        $query->leftJoin('#__sdi_sys_stereotype as s on a.stereotype_id = s.id');
        $query->where('r.state = 1');

        $this->db->setQuery($query);
        return $this->db->loadObjectList('guid');
    }

    /**
     * Unserialze the Xpath
     * 
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     * 
     * @param string $xpath
     * @return string Unserialized XPath
     */
    private function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

    private function registerNamespace($o) {
        foreach ($this->nsdao->getAll() as $ns) {
            $o->registerNamespace($ns->prefix, $ns->uri);
        }
    }

    private function setDomXpathStr() {
        $this->domXpathStr = new DOMXPath($this->structure);
        $this->registerNamespace($this->domXpathStr);
    }

    private function setDomXpathCsw() {
        $this->domXpathCsw = new DOMXPath($this->csw);
        $this->registerNamespace($this->domXpathCsw);
    }

    /**
     * 
     * @return JDatabaseQuery
     */
    private function getRelationQuery() {
        $query = $this->db->getQuery(true);
        $query->select('r.name, r.isocode, r.id, r.ordering, r.guid, r.childtype_id, r.parent_id, r.lowerbound, r.upperbound, r.rendertype_id, r.relationscope_id, r.editorrelationscope_id, r.accessscope_limitation');
        $query->select('c.id as class_id, c.name AS class_name,c.isocode as class_isocode, c.guid AS class_guid');
        $query->select('ca.id as classass_id, ca.name AS classass_name, ca.guid AS classass_guid');
        $query->select('a.id as attribute_id, a.name AS attribute_name, a.guid AS attribute_guid, a.isocode AS attribute_isocode, a.type_isocode as attribute_type_isocode, a.codelist as attribute_codelist, a.pattern as attribute_pattern, a.length as attribute_length');
        $query->select('rt.id as resourcetype_id, rt.name as resourcetype_name, rt.fragment as resourcetype_fragment');
        $query->select('st.id as stereotype_id, st.value as stereotype_value, st.isocode as stereotype_isocode, st.defaultpattern as stereotype_defaultpattern');
        $query->select('stc.id as class_stereotype_id, stc.value as class_stereotype_value, stc.isocode as class_stereotype_isocode, stc.defaultpattern as class_stereotype_defaultpattern');
        $query->select('ns.id as ns_id, ns.prefix, ns.uri');
        $query->select('nsc.id as class_ns_id, nsc.prefix as class_ns_prefix, nsc.uri as class_ns_uri');
        $query->select('nsca.id as classass_ns_id, nsca.prefix as classass_ns_prefix, nsca.uri as classass_ns_uri');
        $query->select('nsa.id as attribute_ns_id, nsa.prefix as attribute_ns_prefix, nsa.uri as attribute_ns_uri');
        $query->select('nsst.id as stereotype_ns_id, nsst.prefix as stereotype_ns_prefix, nsst.uri as stereotype_ns_uri');
        $query->select('nsstc.id as class_stereotype_ns_id, nsstc.prefix as class_stereotype_ns_prefix, nsstc.uri as class_stereotype_ns_uri');
        $query->select('nsl.id as list_ns_id, nsl.prefix as list_ns_prefix, nsl.uri as list_ns_uri');
        $query->select('nsrt.id as resourcetype_ns_id, nsrt.prefix as resourcetype_ns_prefix, nsrt.uri as resourcetype_ns_uri');
        $query->from('#__sdi_relation AS r');
        $query->innerJoin('#__sdi_relation_profile AS rp ON r.id = rp.relation_id');
        $query->leftJoin('#__sdi_class AS c ON c.id = r.classchild_id');
        $query->leftJoin('#__sdi_class AS ca ON ca.id = r.classassociation_id');
        $query->leftJoin('#__sdi_attribute AS a ON a.id = r.attributechild_id');
        $query->leftJoin('#__sdi_resourcetype AS rt ON rt.id = r.childresourcetype_id');
        $query->leftJoin('#__sdi_sys_stereotype AS st ON st.id = a.stereotype_id');
        $query->leftJoin('#__sdi_sys_stereotype AS stc ON stc.id = c.stereotype_id');
        $query->leftJoin('#__sdi_namespace AS ns ON ns.id = r.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsc ON nsc.id = c.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsca ON nsca.id = ca.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsa ON nsa.id = a.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsst ON nsst.id = st.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsstc ON nsstc.id = stc.namespace_id');
        $query->leftJoin('#__sdi_namespace AS nsl ON nsl.id = a.listnamespace_id');
        $query->leftJoin('#__sdi_namespace AS nsrt ON nsrt.id = rt.fragmentnamespace_id');
        $query->where('rp.profile_id = ' . (int) $this->item->profile_id);

        return $query;
    }

    /**
     * Return the default value(s) of a field
     * @param type $relation_id
     * @param type $value
     * @param type $isList
     * @param type $language_id
     * @return string|array defaul value(s)
     */
    private function getDefaultValue($relation_id, $value, $isList = false, $language_id = null) {
        if (isset($value) || (gettype($value) == "integer" && $value == 0)) {
            return $value;
        }
        if (empty($relation_id)) {
            return '';
        }

        $query = $this->db->getQuery(true);
        if ($isList) {
            $query->select('av.value, av.guid, a.stereotype_id');
            $query->from('#__sdi_relation_defaultvalue rdv');
            $query->innerJoin('#__sdi_attributevalue av on av.id = rdv.attributevalue_id');
            $query->innerJoin('#__sdi_attribute a on a.id=av.attribute_id');
            $query->where('rdv.relation_id = ' . (int) $relation_id);
        } else {
            $query->select('value');
            $query->from('#__sdi_relation_defaultvalue');
            $query->where('relation_id=' . (int) $relation_id);
            if (isset($language_id)) {
                $query->where('language_id=' . (int) $language_id);
            }
        }

        $this->db->setQuery($query);
        if ($isList) {
            $result = $this->db->loadObjectList();
        } else {
        $result = $this->db->loadObject();
        }

        if ($result == null) {
            return null;
        }
        if (empty($result)) {
            return '';
        }

        //Handle simple and multiple default value for list field
        if ($isList) {
            if (count($result) > 1) {
                $defaults = array();
                foreach ($result as $r) {
                    array_push($defaults, $this->extractDefaultValue($r));
                }
                return $defaults;
        } else {
                return $this->extractDefaultValue($result[0]);
            }
            } else {
                return $result->value;
            }
        }

    /**
     * Extract default value depending on the object type
     * @param type $obj
     * @return string default value
     */
    private function extractDefaultValue($obj) {
        if (isset($obj->stereotype_id) && $obj->stereotype_id == EnumStereotype::$LOCALECHOICE) {
            return $obj->guid;
        } else {
            return $obj->value;
    }
    }

    private function getGuidFromLocaleValue($relation_id, $texte) {
        $query = $this->db->getQuery(true);

        $query->select('av.guid');
        $query->from('#__sdi_attributevalue av');
        $query->innerJoin('#__sdi_relation r ON r.attributechild_id = av.attribute_id');
        $query->innerJoin('#__sdi_translation t ON t.element_guid = av.guid');
        $query->where('r.id = ' . (int) $relation_id);
        $query->where('t.text2 = ' . $query->quote($texte));

        $this->db->setQuery($query);
        $result = $this->db->loadObject();

        return isset($result) && isset($result->guid) ? $result->guid : '';
    }

    /**
     * 
     * @param DOMElement $element
     * @return \DOMElement
     */
    private function clearNodeValue(DOMElement $element) {
        $nodes = $element->getElementsByTagNameNS('*', '*');

        foreach ($nodes as $node) {
            if (!$this->hasChildElement($node)) {
                $node->nodeValue = NULL;
            }
        }

        return $element;
    }

    /**
     * get scope for specific user and field
     * 
     * @param int $metadata_id
     * @param int $relationscope_id
     * @param int $editorrelationscope_id
     * @return int
     */
    private function getFieldScope($metadata_id, $relationscope_id, $editorrelationscope_id) {
        $rights = array(EnumRelationScope::HIDDEN);

        if ($this->user->authorizeOnMetadata($metadata_id, sdiUser::resourcemanager) || $this->user->authorizeOnMetadata($metadata_id, sdiUser::metadataresponsible) || $this->user->isOrganismManager($metadata_id, 'metadata')) {
            if (!empty($relationscope_id)) {
                $rights[] = $relationscope_id;
            }
        }

        if ($this->user->authorizeOnMetadata($metadata_id, sdiUser::metadataeditor) || $this->user->isOrganismManager($metadata_id, 'metadata')) {
            if (!empty($editorrelationscope_id)) {
                $rights[] = $editorrelationscope_id;
            }
        }

        return min($rights);
    }

    /**
     * 
     * @param string $attribute_guid
     * @param string $relation_guid
     * @return string field description
     */
    private function getDescription($attribute_guid, $relation_guid) {
        $description = '';

        $description_attribute = EText::_($attribute_guid, 2);
        $description_relation = EText::_($relation_guid, 2);

        if (!empty($description_relation)) {
            $description = $description_relation;
        } elseif ($description_attribute) {
            $description = $description_attribute;
        }

        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__sdi_attribute a');
        $query->where('a.guid = ' . $query->quote($attribute_guid));

        $this->db->setQuery($query);
        $attribute = $this->db->loadObject();

        if (!empty($attribute->pattern)) {
            $description .= '<br/><span class="attribute-pattern">(' . $attribute->pattern . ')</span>';
        }

        return $description;
    }

    /**
     * Remove index from XPath
     *
     * @param string $xpath
     * @return string
     * @deprecated since version 4.2.4 Please use FormUtils::removeIndexFromXpath
     */
    private function removeIndex($xpath) {
        return preg_replace('/[\[0-9\]*]/i', '', $xpath);
    }

}

?>
