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

require_once JPATH_COMPONENT . '/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/mpdf/mpdf.php';

/**
 * Catalog controller class.
 */
class Easysdi_catalogControllerSheet extends Easysdi_catalogController {

    public function exportPDF() {
        $id = JFactory::getApplication()->input->get('id', null, 'STRING');
        $catalog = JFactory::getApplication()->input->get('catalog', null, 'STRING');
        $type = JFactory::getApplication()->input->get('type', null, 'STRING');
        $lang = JFactory::getApplication()->input->get('lang', null, 'STRING');
        $metadata = new cswmetadata($id);
        $metadata->load();
        $metadata->extend($catalog, $type, 'true', $lang);
        $file = $metadata->applyXSL($catalog, $type);
        
        $tmp = uniqid();
        $tmpfile = JPATH_BASE . '/tmp/' . $tmp;
        file_put_contents($tmpfile . '.xml', $file);
        $mpdf = new mPDF();
        $mpdf->WriteHTML($file);
        $mpdf->Output($tmpfile . '.pdf', 'F');
        $file = $mpdf->Output('', 'S');
        Easysdi_catalogControllerSheet::setResponse($file, $tmpfile . '.pdf', 'application/pdf', 'report.pdf', strlen($file));
    }

    public function exportXML() {
        $id = JFactory::getApplication()->input->get('id', null, 'STRING');
        $metadata = new cswmetadata($id);
        $metadata->load();
        $file = $metadata->dom->saveXML();
        $tmp = uniqid();
        $tmpfile = JPATH_BASE . '/tmp/' . $tmp;
        file_put_contents($tmpfile . '.xml', $file);
        Easysdi_catalogControllerSheet::setResponse($file, $tmpfile . '.xml', 'text/xml', 'report.xml', strlen($file));
    }

    public function printSheet() {
        
    }

    function setResponse($file, $filename, $contenttype, $downloadname, $size) {
        unlink($filename);
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