<?php

require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/dao/SdiNamespaceDao.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumLayerName.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/libraries/easysdi/enum/EnumServiceConnector.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormHtmlGenerator
 *
 * @author Administrator
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

    function __construct(JForm $form, DOMDocument $structure, $ajaxXpath = null) {
        $this->form = $form;
        $this->structure = $structure;
        $this->ldao = new SdiLanguageDao();
        $this->nsdao = new SdiNamespaceDao();
        $this->formHtml = new DOMDocument(null, 'utf-8');
        $this->ajaxXpath = $ajaxXpath;
        $this->db = JFactory::getDbo();
    }

    /**
     * 
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

        foreach ($this->domXpathStr->query($query, $parent) as $child) {

            switch ($child->getAttributeNS($this->catalog_uri, 'childtypeId')) {
                case EnumChildtype::$RELATION:
                    if (($child->getAttributeNS($this->catalog_uri, 'lowerbound') - $child->getAttributeNS($this->catalog_uri, 'upperbound')) != 0 && $child->getAttributeNS($this->catalog_uri, 'index') == 1) {
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
                    $parentname = $parent->nodeName;
                    $childName = $child->nodeName;
                    $field = $this->getAttribute($child);
                    $parentInner->appendChild($field);

                    break;
                case EnumChildtype::$RELATIONTYPE:
                    if ($child->getAttributeNS($this->catalog_uri, 'index') == 1) {
                        $action = $this->getAction($child);
                        $parentInner->appendChild($action);
                    }
                    $searchField = $this->getAttribute($child);
                    $fieldset = $this->getFieldset($child);

                    $fieldset->getElementsByTagName('div')->item(0)->appendChild($searchField);


                    $parentInner->appendChild($fieldset);

                    if ($this->domXpathStr->query('*[@catalog:childtypeId="2"]', $child)->length > 0) {
                        $this->recBuildForm($child, $fieldset);
                    }
                    break;
            }
        }
    }

    /**
     * Built on the action bar to add new relation instance.
     * 
     * @param SdiRelation $rel
     * @return DOMElement
     */
    private function getAction(DOMElement $relation) {

        $lowerbound = $relation->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $relation->getAttributeNS($this->catalog_uri, 'upperbound');
        $exist = $relation->getAttributeNS($this->catalog_uri, 'exist');

        switch ($relation->getAttributeNS($this->catalog_uri, 'childtypeId')) {
            case EnumChildtype::$RELATIONTYPE:
                $relid = $relation->getAttributeNS($this->catalog_uri, 'relationId');
                break;

            default:
                $relid = $relation->getAttributeNS($this->catalog_uri, 'dbid');
                break;
        }

        $occurance = $this->domXpathStr->query($this->removeIndex($relation->getNodePath()))->length;

        $debug = '[oc:' . $occurance . ' lb:' . $lowerbound . ' ub:' . $upperbound . '][' . $relation->nodeName . ']';

        $aAdd = $this->formHtml->createElement('a');
        $aAdd->setAttribute('id', 'add-btn-' . $this->serializeXpath($relation->getNodePath()));
        $aAdd->setAttribute('class', 'btn btn-success btn-mini add-btn add-btn-' . $this->serializeXpath($this->removeIndex($relation->getNodePath())));
        $aAdd->setAttribute('onclick', 'addFieldset(this.id, \'' . $this->serializeXpath($this->removeIndex($relation->getNodePath())) . '\',' . $relid . ', \'' . $this->serializeXpath($relation->parentNode->getNodePath()) . '\' ,' . $lowerbound . ',' . $upperbound . ')');

        if ($exist == 1) {
            if ($upperbound <= $occurance) {
                $aAdd->setAttribute('style', 'display:none;');
            }
        }

        $iAdd = $this->formHtml->createElement('i');
        $iAdd->setAttribute('class', 'icon-white icon-plus-2');

        $divOuter = $this->formHtml->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $this->serializeXpath($relation->getNodePath()));
        $divOuter->setAttribute('class', 'outer-level outer-fds-' . $this->serializeXpath($relation->getNodePath()));

        $divAction = $this->formHtml->createElement('div', EText::_($relation->getAttributeNS($this->catalog_uri, 'id')) . ' ' . $debug);
        $divAction->setAttribute('class', 'action-level');

        $aAdd->appendChild($iAdd);
        $divAction->appendChild($aAdd);

        return $divAction;
    }

    /**
     * Constructs a fieldset corresponding to a Class.
     * 
     * @param SdiRelation $rel
     * @return DOMElement
     */
    private function getFieldset(DOMElement $element) {
        $lowerbound = $element->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $element->getAttributeNS($this->catalog_uri, 'upperbound');
        $occurance = $this->domXpathStr->query($this->removeIndex($element->getNodePath()))->length;
        $index = $element->getAttributeNS($this->catalog_uri, 'index');
        $exist = $element->getAttributeNS($this->catalog_uri, 'exist');
        $guid = $element->getAttributeNS($this->catalog_uri, 'id');
        $legendAttribute = $element->getAttributeNS($this->catalog_uri, 'legend');
        $stereotypeId = $element->firstChild->getAttributeNS($this->catalog_uri, 'stereotypeId');

        $elementname = $element->nodeName;

        $aCollapse = $this->formHtml->createElement('a');
        $aCollapse->setAttribute('id', 'collapse-btn-' . $this->serializeXpath($element->getNodePath()));
        $aCollapse->setAttribute('class', 'btn btn-mini collapse-btn');
        $aCollapse->setAttribute('onclick', 'collapse(this.id)');

        $iCollapse = $this->formHtml->createElement('i');
        $iCollapse->setAttribute('class', 'icon-white icon-arrow-down');

        $aRemove = $this->formHtml->createElement('a');
        $aRemove->setAttribute('id', 'remove-btn-' . $this->serializeXpath($element->getNodePath()));
        $aRemove->setAttribute('class', 'btn btn-danger btn-mini pull-right remove-btn-' . $this->serializeXpath($this->removeIndex($element->getNodePath())));
        $aRemove->setAttribute('onclick', 'confirmFieldset(this.id, \'' . $this->serializeXpath($this->removeIndex($element->getNodePath())) . '\' , ' . $lowerbound . ',' . $upperbound . ')');

        $iRemove = $this->formHtml->createElement('i');
        $iRemove->setAttribute('class', 'icon-white icon-cancel-2');

        $divOuter = $this->formHtml->createElement('div');
        $divOuter->setAttribute('id', 'outer-fds-' . $this->serializeXpath($element->getNodePath()));
        $divOuter->setAttribute('class', 'outer-' . 0 . ' outer-fds-' . $this->serializeXpath($this->removeIndex($element->getNodePath())));

        $fieldset = $this->formHtml->createElement('fieldset');
        $fieldset->setAttribute('id', 'fds-' . $this->serializeXpath($element->getNodePath()));

        if ($guid != '') {
            $spanLegend = $this->formHtml->createElement('span', EText::_($guid));
        } else {
            $spanLegend = $this->formHtml->createElement('span', JText::_($legendAttribute));
        }

        $spanLegend->setAttribute('class', 'legend-' . 0);
        $legend = $this->formHtml->createElement('legend');

        $divInner = $this->formHtml->createElement('div');
        $divInner->setAttribute('id', 'inner-fds-' . $this->serializeXpath($element->getNodePath()));
        $divInner->setAttribute('class', 'inner-fds');
        if (!isset($_GET['relid'])) {
            $divInner->setAttribute('style', 'display:none;');
        }

        $divBottom = $this->formHtml->createElement('div');
        $divBottom->setAttribute('id', 'bottom-' . $this->serializeXpath($this->removeIndex($element->getNodePath())));

        if ($exist == 1) {
            $aCollapse->appendChild($iCollapse);
            $legend->appendChild($aCollapse);
            $legend->appendChild($spanLegend);
            if ($lowerbound < $occurance) {
                $aRemove->appendChild($iRemove);
                $legend->appendChild($aRemove);
            }
            $fieldset->appendChild($legend);
            $fieldset->appendChild($divInner);

            $divOuter->appendChild($fieldset);
        }
        if ($index == $occurance) {
            if ($stereotypeId == EnumStereotype::$GEOGRAPHICEXTENT) {
//                $divInner->appendChild($btnEdit);
//                $divInner->appendChild($divMap);
            }
            $divOuter->appendChild($divBottom);
        }

        return $divOuter;
    }

    /**
     * Encode special characters into HTML entities. Unless the <> characters.
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
     * Build a string with 
     * 
     * @param SdiRelation $rel
     * @return string
     */
    private function getAttribute(DOMElement $attribute) {
        $lowerbound = $attribute->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');

        $languages = $this->ldao->getSupported();
        if ($upperbound > 1) {
            $showButton = true;
        } else {
            $showButton = false;
        }

        $attributeGroup = $this->formHtml->createElement('div');
        $attributeGroup->setAttribute('class', 'attribute-group attribute-group-' . $this->removeIndex($this->serializeXpath($attribute->getNodePath())));
        $attributeGroup->setAttribute('id', 'attribute-group-' . $this->serializeXpath($attribute->getNodePath()));

        switch ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId')) {
            case EnumStereotype::$LOCALE:
                $nodePath = $attribute->firstChild->getNodePath();
                $jfield = $this->form->getField($this->serializeXpath($nodePath));
                foreach ($this->buildField($attribute, $jfield, $showButton) as $element) {
                    $attributeGroup->appendChild($element);
                }

                foreach ($languages as $key => $value) {
                    $jfield = $this->form->getField($this->serializeXpath($attribute->getNodePath() . '/gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString#' . $key));
                    if ($jfield) {
                        foreach ($this->buildField($attribute, $jfield) as $element) {
                            $attributeGroup->appendChild($element);
                        }
                    }
                }
                break;

            default:
                if ($attribute->getAttributeNS($this->catalog_uri, 'childtypeId') == EnumChildtype::$RELATIONTYPE) {
                    $nodePath = $attribute->getNodePath();
                    $showButton = false;
                } else {
                    $nodePath = $attribute->firstChild->getNodePath();
                }

                $jfield = $this->form->getField($this->serializeXpath($nodePath));
                if ($jfield) {
                    foreach ($this->buildField($attribute, $jfield, $showButton) as $element) {
                        $attributeGroup->appendChild($element);
                    }
                }
                break;
        }

        if ($attribute->getAttributeNS($this->catalog_uri, 'map')) {

            $attributeGroup->appendChild($this->getMap($attribute));
        }

        return $attributeGroup;
    }

    private function getMap(DOMElement $attribute) {
        $parent_path = str_replace('-', '_', $this->serializeXpath($attribute->parentNode->getNodePath()));

        $div = $this->formHtml->createElement('div');

        $btnEdit = $this->formHtml->createElement('button', 'Edition');
        $btnEdit->setAttribute('type', 'button');
        $btnEdit->setAttribute('class', 'btn btn-primary btn-small');
        $btnEdit->setAttribute('id', 'editBtn_' . $parent_path);
        $btnEdit->setAttribute('data-toggle', 'button');
        $btnEdit->setAttribute('onclick', 'polygonControl_' . $parent_path . '.activate();');

        $br = $this->formHtml->createElement('br');

        $divMap = $this->formHtml->createElement('div');
        $divMap->setAttribute('id', 'map_' . $parent_path);
        $divMap->setAttribute('style', 'width: 550px;height: 300px;');

        $script = $this->formHtml->createElement('script');
        $script->setAttribute('type', 'text/javascript');

        $map_id = JComponentHelper::getParams('com_easysdi_catalog')->get('catalogmap');

        $query = $this->db->getQuery(true);

        $query->select('m.srs, m.unit_id, m.maxresolution, m.maxextent, m.centercoordinates, l.layername, l.service_id, l.servicetype, l.asOLstyle, l.asOLoptions, l.asOLmatrixset, u.`alias` as unit_alias');
        $query->from('#__sdi_map as m');
        $query->innerJoin('#__sdi_map_layergroup mlg ON m.id = mlg.map_id');
        $query->innerJoin('#__sdi_layer_layergroup llg ON llg.group_id = mlg.group_id');
        $query->innerJoin('#__sdi_maplayer l ON l.id = llg.layer_id');
        $query->innerJoin('#__sdi_sys_unit u ON u.id = m.unit_id');
        $query->where('m.id=' . $map_id);
        $query->where('mlg.isbackground = 1');

        $this->db->setQuery($query);
        $map_config = $this->db->loadObject();

        $query = $this->db->getQuery(true);
        switch ($map_config->servicetype) {
            case 'physical':
                $query->select('resourceurl as serviceurl, serviceconnector_id');
                $query->from('#__sdi_physicalservice');
                $query->where('id = ' . $map_config->service_id);
                break;
            case 'virtual':
                $query->select('url, reflectedurl as serviceurl, serviceconnector_id');
                $query->from('#__sdi_virtualservice');
                $query->where('id = ' . $map_config->service_id);
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
                                                {type: G_PHYSICAL_MAP}
                                            );";
                        break;
                    case EnumLayerName::$SATELLITE:
                        $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Google(
                                                'Google Satellite',
                                                {type: G_SATELLITE_MAP, numZoomLevels: 22}
                                            );";
                        break;
                    case EnumLayerName::$HYBRIDE:
                        $layer_definition = "layer_$parent_path = new OpenLayers.Layer.Google(
                                                'Google Hybrid',
                                                {type: G_HYBRID_MAP, numZoomLevels: 20}
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
                                        type: " . $map_config->layername . "
                                    });";
                break;
            case EnumServiceConnector::$WMS:
                $layer_definition = "layer_$parent_path = new OpenLayers.Layer.WMS( 'WMS name',
                                    " . $service->serviceurl . ",
                                    {layers: " . $map_config->layername . "});";
                break;
            case EnumServiceConnector::$WMTS:
                $layer_definition = "layer_$parent_path = new OpenLayers.Layer.WMTS({
                                        name: 'Couche WMTS',
                                        url: " . $service->serviceurl . ",
                                        layer: " . $map_config->layername . ",
                                        matrixSet: " . $map_config->asOLmatrixset . ",
                                        style: " . $map_config->asOLstyle . ",
                                        " . $map_config->asOLoptions . "
                                    });";
                break;
        }

        $script->nodeValue = "var map_$parent_path, layer_$parent_path, polygonLayer_$parent_path, polygonControl_$parent_path ;
                            js('document').ready(function() {
                                var lon = 5;
                                var lat = 40;
                                var zoom = 5;

                                map_$parent_path = new OpenLayers.Map(\"map_$parent_path\",{projection: \"" . $map_config->srs . "\" , maxResolution: " . $map_config->maxresolution . " , units: \"" . $map_config->unit_alias . "\", maxExtent: [" . $map_config->maxextent . "], restrictedExtent: [" . $map_config->maxextent . "], center: [" . $map_config->centercoordinates . "]});
                                 
                                " . $layer_definition . "
                                polygonLayer_$parent_path = new OpenLayers.Layer.Vector('Polygon Layer');

                                map_$parent_path.addLayers([layer_$parent_path, polygonLayer_$parent_path]);
                                map_$parent_path.setCenter(new OpenLayers.LonLat(lon, lat), zoom);

                                var polyOptions = {sides: 4, irregular: true};
                                polygonControl_$parent_path = new OpenLayers.Control.DrawFeature(polygonLayer_$parent_path,
                                        OpenLayers.Handler.RegularPolygon,
                                        {handlerOptions: polyOptions});

                                map_$parent_path.addControl(polygonControl_$parent_path);
                                    
                               drawBB(polygonLayer_$parent_path, '$parent_path');
                                    
                               polygonLayer_$parent_path.events.register('featureadded', polygonLayer_$parent_path, function(e) {
                                    polygonControl_$parent_path.deactivate();
                                    js('#editBtn_$parent_path').removeClass('active');

                                    var bounds = e.feature.geometry.getBounds();

                                    js('#jform_" . $parent_path . "_sla_gmd_dp_northBoundLatitude_sla_gco_dp_Decimal').attr('value', bounds.top);
                                    js('#jform_" . $parent_path . "_sla_gmd_dp_southBoundLatitude_sla_gco_dp_Decimal').attr('value', bounds.bottom);
                                    js('#jform_" . $parent_path . "_sla_gmd_dp_eastBoundLongitude_sla_gco_dp_Decimal').attr('value', bounds.right);
                                    js('#jform_" . $parent_path . "_sla_gmd_dp_westBoundLongitude_sla_gco_dp_Decimal').attr('value', bounds.left);

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
     * 
     * @param JFile $field
     * @param string $guid
     * @return string
     */
    private function buildField(DOMElement $attribute, $field, $addButton = FALSE) {
        $guid = $attribute->getAttributeNS($this->catalog_uri, 'id');

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

        $control->appendChild($this->getInput($field));
        if ($addButton) {
            $control->appendChild($this->getAttributeAction($attribute));
        }


        $controlGroup->appendChild($controlLabel);
        $controlGroup->appendChild($control);

        if ($attribute->getAttributeNS($this->catalog_uri, 'stereotypeId') == EnumStereotype::$FILE) {
            $jfieldhidden = $this->form->getField($this->serializeXpath($attribute->firstChild->getNodePath()) . '_filehidden');
            $jfieldtext = $this->form->getField($this->serializeXpath($attribute->firstChild->getNodePath()) . '_filetext');

            $br = $this->formHtml->createElement('br');
            $control->appendChild($br);
            $control->appendChild($this->getInput($jfieldtext));
            $control->appendChild($this->getInput($jfieldhidden));
            $control->appendChild($this->getPreviewAction($attribute));
            $control->appendChild($this->getEmptyFileAction($attribute));
        }

        $elements[] = $controlGroup;
        $elements[] = $this->getInputScript($field, $guid);

        return $elements;
    }

    /**
     * 
     * @param type $field
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
     * 
     * @param type $field
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
     * 
     * @param type $field
     * @param string $guid
     * @return DOMElement
     */
    private function getInputScript($field, $guid) {
        $script_content = "js = jQuery.noConflict();

                    js('document').ready(function() {
                        js('#" . $field->id . "').tooltip({'trigger':'focus', 'title': \"" . addslashes(EText::_($guid, 2)) . "\"});
                    });";

        $script = $this->formHtml->createElement('script', $script_content);
        $script->setAttribute('type', 'text/javascript');

        return $script;
    }

    /**
     * 
     * @param DOMElement $attribute
     * @return DOMElement Description
     */
    private function getAttributeAction(DOMElement $attribute) {
        $lowerbound = $attribute->getAttributeNS($this->catalog_uri, 'lowerbound');
        $upperbound = $attribute->getAttributeNS($this->catalog_uri, 'upperbound');
        $relid = $attribute->getAttributeNS($this->catalog_uri, 'relid');

        $debug = ' [oc: lb:' . $lowerbound . ' ub:' . $upperbound . ']';
        $action = $this->formHtml->createElement('div', 'attribute actions' . $debug);

        $aAdd = $this->formHtml->createElement('a');
        $iAdd = $this->formHtml->createElement('i');

        if (isset($_GET['relid'])) {
            $aAdd->setAttribute('id', 'remove-btn-' . $this->serializeXpath($attribute->getNodePath()));
            $aAdd->setAttribute('class', 'btn btn-danger btn-mini remove-btn remove-btn-' . $this->serializeXpath($this->removeIndex($attribute->getNodePath())));
            $aAdd->setAttribute('onclick', 'confirmField(this.id, \'' . $this->removeIndex($this->serializeXpath($attribute->getNodePath())) . '\',' . $relid . ', \'' . $this->serializeXpath($attribute->parentNode->getNodePath()) . '\' ,' . $lowerbound . ',' . $upperbound . ')');

            $iAdd->setAttribute('class', 'icon-white icon-cancel-2');
        } else {
            $aAdd->setAttribute('id', 'add-btn-' . $this->serializeXpath($attribute->getNodePath()));
            $aAdd->setAttribute('class', 'btn btn-success btn-mini add-btn add-btn-' . $this->serializeXpath($this->removeIndex($attribute->getNodePath())));
            $aAdd->setAttribute('onclick', 'addField(this.id, \'' . $this->removeIndex($this->serializeXpath($attribute->getNodePath())) . '\',' . $relid . ', \'' . $this->serializeXpath($attribute->parentNode->getNodePath()) . '\' ,' . $lowerbound . ',' . $upperbound . ')');

            $iAdd->setAttribute('class', 'icon-white icon-plus-2');
        }

        $aAdd->appendChild($iAdd);

        return $aAdd;
    }

    private function getPreviewAction(DOMElement $attribute) {
        $a = $this->formHtml->createElement('a');
        $i = $this->formHtml->createElement('i');

        $a->setAttribute('id', 'preview-' . $this->serializeXpath($attribute->firstChild->getNodePath()));
        $a->setAttribute('target', '_blank');
        $a->setAttribute('class', 'btn btn-mini preview-btn');
        $a->setAttribute('href', $attribute->nodeValue);
        $a->setAttribute('target', '_blank');

        $i->setAttribute('class', 'icon-white icon-eye-open');

        $a->appendChild($i);

        return $a;
    }

    private function getEmptyFileAction(DOMElement $attribute) {
        $a = $this->formHtml->createElement('a');
        $i = $this->formHtml->createElement('i');

        $a->setAttribute('id', 'empty-btn-' . $this->serializeXpath($attribute->firstChild->getNodePath()));
        $a->setAttribute('class', 'btn btn-danger btn-mini empty-btn empty-btn-' . $this->serializeXpath($this->removeIndex($attribute->getNodePath())));
        $a->setAttribute('onclick', 'confirmEmptyFile(this.id)');

        $i->setAttribute('class', 'icon-white icon-cancel-2');

        $a->appendChild($i);

        return $a;
    }

    /**
     * 
     * @param string $xpath
     * @return string
     */
    private function serializeXpath($xpath) {
        $xpath = str_replace('[', '-la-', $xpath);
        $xpath = str_replace(']', '-ra-', $xpath);
        $xpath = str_replace('/', '-sla-', $xpath);
        $xpath = str_replace(':', '-dp-', $xpath);
        return $xpath;
    }

    /**
     * 
     * @param string $xpath
     * @return string
     */
    private function removeIndex($xpath) {
        return preg_replace('/[\[0-9\]*]/i', '', $xpath);
    }

}

?>
