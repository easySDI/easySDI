<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class sdiIndicatorGlobal_dates extends sdiIndicator{
    
    /**
     * Return the indicator name for file download
     * @return  string the indicator filename for downloads 
     */
    protected function _getIndicatorFileName() {
        return JText::_("COM_EASYSDI_DASHBOARD_GLOBAL_IND_DATES");
    }

    /**
     * Returns the data of the indicator as JSON object
     * @param   mixed $organism and integer for organismID or 'all' (backend usage only)
     * @param   int $timestart start timestamp
     * @param   int $timeend end timestamp
     * @param   int $limit number of record to return, 0 = unlimited (default)
     * @return  DATA object 
     */
    protected function _getData($organism,$timestart,$timeend,$limit=0){
       
        $return = new stdClass();
        $return->data = array();
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_GLOBAL_IND_DATES');
        $return->columns_title = array();
        
        return ($return);

    }
}

