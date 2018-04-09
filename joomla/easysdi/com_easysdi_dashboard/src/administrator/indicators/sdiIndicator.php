<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

abstract class sdiIndicator {

    /**
     * the filename for downloads
     * @var strin 
     */
    protected $downloadFileName;

    /**
     * Constructor
     */
    function __construct() {
        $this->downloadFileName = $this->_getIndicatorFileName();
    }

    /**
     * Returns the data of the indicator as JSON object
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  DATA object 
     */
    abstract protected function _getData($organism, $timestart, $timeend, $limit = 0);

    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads 
     */
    abstract protected function _getIndicatorFileName();

    /**
     * Returns the data of the indicator in required format
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   string $dataformat return format json, csv or pdf
     * @param   int $limit number of record to return, 0 = unlimited (default)
     */
    public function sendResponse($organism, $timestart, $timeend, $dataformat, $limit = 0) {
        $document = JFactory::getDocument();
        switch ($dataformat) {
            case 'json':
                //RFC https://tools.ietf.org/html/rfc7159
                $document->setMimeEncoding('application/json');
                echo($this->getJson($organism, $timestart, $timeend, $limit));
                break;
            case 'pdf':
                $document->setMimeEncoding('application/pdf');
                //RFC https://tools.ietf.org/html/rfc3778
                JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->downloadFileName . '.pdf"');
                echo($this->getPDF($organism, $timestart, $timeend));
                break;
            case 'csv':
                $document->setMimeEncoding('text/csv');
                $document->setCharset('ISO-8859-1');
                //RFC http://tools.ietf.org/html/rfc7111#section-5
                JResponse::setHeader('Content-Disposition', 'attachment; filename="' . $this->downloadFileName . '.csv"');
                echo($this->getCSV($organism, $timestart, $timeend));
                break;
        }
    }

    /**
     * Returns the data of the indicator as JSON object
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  JSON object 
     */
    private function getJson($organism, $timestart, $timeend, $limit = 0) {
        //encode indicator data
        return json_encode($this->getData($organism, $timestart, $timeend, $limit));
    }

    /**
     * Returns the data of the indicator as PHP object
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  Data object 
     */
    private function getData($organism, $timestart, $timeend, $limit = 0) {
        $data = $this->_getData($organism, $timestart, $timeend, $limit);
        //add from/to readable date
        $data->datefrom = JHtml::date($timestart, JText::_('DATE_FORMAT_LC3'), "GMT");
        $data->dateto = JHtml::date($timeend, JText::_('DATE_FORMAT_LC3'), "GMT");
        $data->organismname = $this->getOrgnameFromFilter($organism);
        return $data;
    }

    /**
     * Returns the data of the indicator as CSV string
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  CSV string 
     */
    private function getCSV($organism, $timestart, $timeend) {
        return $this->buildCSV($this->getData($organism, $timestart, $timeend, 0));
    }

    /**
     * Returns the data of the indicator as PDF fiel with a table of results
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  PDF binary string 
     */
    private function getPDF($organism, $timestart, $timeend) {
        $params = JComponentHelper::getParams('com_easysdi_dashboard');
        $reporthtmlheader = $params->get('reporthtmlheader');
        $reportcss = $params->get('reportcss');

        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/mpdf/mpdf.php';
        $mpdf = new mPDF('', 'A4-L');
        //load default css
        $mpdf->WriteHTML(file_get_contents(JPATH_SITE . '/components/com_easysdi_dashboard/assets/css/dashboard_pdf_report.css'), 1);
        //load custom additional css
        if (isset($reportcss) && strlen($reportcss) > 0) {
            $mpdf->WriteHTML($reportcss, 1);
        }
        //add custom header
        if (isset($reporthtmlheader) && strlen($reporthtmlheader) > 0) {
            $mpdf->WriteHTML($reporthtmlheader, 2);
        }
        //add body
        $mpdf->WriteHTML($this->buildXHTMLtable($this->getData($organism, $timestart, $timeend, 0)), 2);
        return $mpdf->Output('', 'S');
    }

    /**
     * Get the organism name
     * @param mixed $organism int orgID or string 'all'
     * @return String organism name or translated 'all'
     */
    private function getOrgnameFromFilter($organism) {
        if ($organism == 'all') {
            return JText::_('COM_EASYSDI_DASHBOARD_FILTERS_ALL_ORGANISMS');
        } else {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                    ->select('o.name')
                    ->from($db->quoteName('#__sdi_organism', 'o'))
                    ->where('o.id = ' . (int) $organism);

            $db->setQuery($query, 0, 1);
            return $db->loadResult();
        }
    }

    /**
     * Buils a simple html table structure based on JSON data
     * @param object $do data object
     * @return string an XHTML simple structure (1 title + 1 table)
     */
    private function buildXHTMLtable($do) {

        $xhtml .= '<h1>' . $do->title . '</h1>';
        $xhtml .= '<h2>' . JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_FROM')
                . ' ' . $do->datefrom . ' '
                . JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_TO')
                . ' ' . $do->dateto . ' '
                . JText::sprintf('COM_EASYSDI_DASHBOARD_FOR_ORG', $do->organismname)
                . '</h2>';
        $xhtml .= '<table>';
        $xhtml .= '  <thead>';
        $xhtml .= '      <tr>';
        foreach ($do->columns_title as $title) {
            $xhtml .='         <th>' . $title . '</th>';
        }
        $xhtml .= '      </tr>';
        $xhtml .= '  </thead>';
        $xhtml .= '  <tbody>';
        foreach ($do->data as $row) {
            $xhtml .= '      <tr>';
            foreach ($row as $col) {
                $xhtml .= '         <td>' . $col . '</td>';
            }
            $xhtml .= '      </tr>';
        }
        $xhtml .= '  </tbody>';
        $xhtml .= '</table>';
        return $xhtml;
    }

    /**
     * Buils a CSV table structure based on JSON data
     * @param object $do data object
     * @return string an CSV string
     */
    private function buildCSV($do) {
        $delimiter = ';';
        $enclosure = '"';
        $eol = "\r\n";

        $csv = '';
        $csv .= $enclosure . implode($enclosure . $delimiter . $enclosure, $do->columns_title) . $enclosure;
        $csv .= $eol;
        foreach ($do->data as $row) {
            $csv .= $enclosure . implode($enclosure . $delimiter . $enclosure, $row) . $enclosure;
            $csv .= $eol;
        }
        return iconv("UTF-8", "ISO-8859-1//TRANSLIT", $csv);
    }

}
