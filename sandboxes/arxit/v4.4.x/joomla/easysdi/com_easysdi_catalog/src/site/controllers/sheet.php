<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
// No direct access
defined('_JEXEC') or die;

require_once JPATH_BASE . '/components/com_easysdi_catalog/controller.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/catalog/cswmetadata.php';
require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/mpdf/mpdf.php';

/**
 * Catalog controller class.
 */
class Easysdi_catalogControllerSheet extends Easysdi_catalogController {

    public function exportPDF($id = null, $download = true) {
        if (empty($id)) {
            $id = JFactory::getApplication()->input->get('id', null, 'STRING');
            $catalog = JFactory::getApplication()->input->get('catalog', null, 'STRING');
            $type = JFactory::getApplication()->input->get('type', null, 'STRING');
            $preview = JFactory::getApplication()->input->get('preview', null, 'STRING');
            $lang = JFactory::getApplication()->input->get('lang', null, 'STRING');
        } else {
            $lang = JFactory::getLanguage()->getTag();
            $catalog = "";
            $type = "";
            $preview = "search_list";
        }
        //Specific parameter for PDF output
        $out = JFactory::getApplication()->input->get('out', null, 'STRING');

        //get extended MD xml
        $metadata = new cswmetadata($id);
        $metadata->load('complete');
        $metadata->extend($catalog, $type, $preview, 'true', $lang);
        $xhtmlfile = $metadata->applyXSL(array('catalog' => $catalog, 'type' => $type, 'preview' => $preview, 'out' => $out));

        //PDF processor
        $mpdf = new mPDF();

        //add custom CSS
        $params = JComponentHelper::getParams('com_easysdi_catalog');
        $pdfexportcss = $params->get('pdfexportcss');
        if (isset($pdfexportcss) && strlen($pdfexportcss) > 0) {
            $mpdf->WriteHTML($pdfexportcss, 1);
        }

        //add HTML and process
        $mpdf->WriteHTML($xhtmlfile);
        $file = $mpdf->Output('', 'S');
        
        //return PDF data depending on usage
        if ($download) {
            $this->setResponse($file, 'application/pdf', 'report.pdf', strlen($file));
        } else {
            return $file;
        }
    }

    public function exportXML($id = null, $download = true) {
        if (empty($id)) {
            $id = JFactory::getApplication()->input->get('id', null, 'STRING');
        }
        $metadata = new cswmetadata($id);
        $metadata->load('complete');
        $metadata->dom->formatOutput = FALSE;
        $file = $metadata->dom->saveXML();
        if ($download) {
            $this->setResponse($file, 'text/xml', 'report.xml', strlen($file));
        } else {
            return $file;
        }
    }

    function setResponse($file, $contenttype, $downloadname, $size) {
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
