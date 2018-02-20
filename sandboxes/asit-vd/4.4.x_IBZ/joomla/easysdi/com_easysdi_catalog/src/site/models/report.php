<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');

require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';
require_once JPATH_BASE . '/components/com_easysdi_catalog/helpers/easysdi_catalog.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/mpdf/mpdf.php';

/**
 * Easysdi_catalog model.
 */
class Easysdi_catalogModelReport extends JModelForm {

    var $_item = null;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since	1.6
     */
    protected function populateState() {
        $guids = JFactory::getApplication()->input->get('guid', array(), 'array');

        $this->setState('report.guids', $guids);
    }

    /**
     * Method to get an ojbect.
     *
     * @param	integer	The id of the object to get.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function &getData($id = null) {

        $this->_item = false;
        
        $jinput = JFactory::getApplication()->input;

        /*
         * Array of metadata guid
         */
        $guids = $this->getState('report.guids');

        /* Raport type. Possible value:
         * - complete
         * - core
         */
        $type = $jinput->get('type', 'complete', 'STRING');

        // Report language
        $lang = $jinput->get('lang', JFactory::getLanguage()->getTag(), 'STRING');

        //Is the call from joomla
        $callfromjoomla = $jinput->get('callfromjoomla', true, 'BOOLEAN');

        //Current catalog context
        $catalog = $jinput->get('catalog', '', 'STRING');

        /* Current preview. Possible value :
         * - editor
         * - public
         * - map
         * A preview corresponds to an association of a catalog and a type :
         * preview = catalog + type
         * If a preview is provided, its value is used to load the XSL file.
         * If no preview is provided, catalog and type values are used to load the XSL file
         */
        $preview = $jinput->get('preview', 'public', 'STRING');

        /*
         * Is last version of metadata
         */
        $lastVersion = $jinput->get('lastVersion', false, 'BOOLEAN');

        $mime = $jinput->get('mime', 'xhtml', 'STRING');

        $metadatas = array();
        foreach ($guids as $guid) {

            if ($lastVersion) {
                $guid = Easysdi_catalogHelper::getLastVersion($guid);
            }

            //Load CSW metadata
            $metadata = new cswmetadata($guid);
            $metadata->load($type);

            //Build extended metadata
            $metadata->extend($catalog, $type, $preview, $callfromjoomla, $lang);

            $metadatas[] = $metadata;
        }

        switch ($mime) {
            case 'pdf':
                $this->reportPDF($catalog, $type, $preview, $metadatas);
                break;

            case 'xml':
                $this->reportXML($metadatas);
                break;
            
            case 'xhtml':
                return $this->reportXHTML($catalog, $type, $preview, $metadatas);
        }

    }

    /**
     * Method to get the profile form.
     *
     * The base form is loaded from XML 
     * 
     * @param	array	$data		An optional array of data for the form to interogate.
     * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
     * @return	JForm	A JForm object on success, false on failure
     * @since	1.6
     */
    public function getForm($data = array(), $loadData = true) {
// Get the form.
        $form = $this->loadForm('com_easysdi_catalog.sheet', 'sheet', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return	mixed	The data for the form.
     * @since	1.6
     */
    protected function loadFormData() {
        $data = $this->getData();
        return $data;
    }

    private function reportXHTML($catalog, $type, $preview, $metadatas) {
        $item = '';
        foreach ($metadatas as $metadata) {
            $item .= $metadata->applyXSL(array('catalog' => $catalog, 'type' => $type, 'preview' => $preview));
        }

        $this->_item = $item;
        
        return $this->_item;
    }

    private function reportXML($metadatas) {

        $xmls = new DOMDocument('1.0', 'utf-8');
        $metadataset = $xmls->createElement('MetadataSet');

        foreach ($metadatas as $metadata) {
            $metadataNode = $xmls->createElement('Metadata');

            $xml = $xmls->importNode($metadata->dom->firstChild, true);

            $metadataNode->appendChild($xml);
            $metadataset->appendChild($metadataNode);
        }

        $xmls->appendChild($metadataset);

        $file = $xmls->saveXML();

        $this->setResponse($file, 'text/xml', 'report.xml', strlen($file));
    }

    private function reportPDF($catalog, $type, $preview, $metadatas){
        $xhtmlfile = '';
        foreach ($metadatas as $metadata) {
            $xhtmlfile .= $metadata->applyXSL(array('catalog' => $catalog, 'type' => $type, 'preview' => $preview));
        }

        $mpdf = new mPDF();
        $mpdf->WriteHTML($xhtmlfile);
        $file = $mpdf->Output('', 'S');

        $this->setResponse($file, 'application/pdf', 'report.pdf', strlen($file));
    }

    private function setResponse($file, $contenttype, $downloadname, $size) {
        error_reporting(0);
        ini_set('zlib.output_compression', 0);

        if (!strpos($contenttype, "html")) {
            header('Content-type: ' . $contenttype);
            header('Content-Disposition:attachment ; filename="' . $downloadname . '"');
        }
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
        header('Pragma: public');
        header("Expires: 0");
        header("Content-Length: " . $size);

        echo $file;
        die();
    }

}
