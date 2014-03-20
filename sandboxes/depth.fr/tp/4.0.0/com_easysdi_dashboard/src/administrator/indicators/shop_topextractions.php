<?php
/**
 * @version     4.0.0
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class Indicator{
    
    public function getData($organism,$timestart,$timeend,$limit=0){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
                ->select('dif.name as prod_name, count(odif.id) as count')
                
                ->from($db->quoteName('#__sdi_order','o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion','odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion','dif') .  ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version','v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource','r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism','org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                
                ->where('o.sent between \''.date("c",$timestart).'\' and  \''.date("c",$timeend).'\' ')
                ->where('o.ordertype_id = 1')

                ->group($db->quoteName('dif.id'))
                
                ->order('2 DESC');
                
        if($organism != 'all'){
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }

        $db->setQuery($query,0,$limit);
        $res = $db->loadRowList();
        
        $return = new stdClass();
        $return->data = $res;
        $return->title = JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_TITLE');
                $return->period_title = JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_FROM')
                                .' '.date("Y-m-d",$timestart).' '
                                .JText::_('COM_EASYSDI_DASHBOARD_PERIOD_TITLE_TO')
                                .' '.date("Y-m-d",$timeend);
        $return->columns_title = array(JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL1'),JText::_('COM_EASYSDI_DASHBOARD_SHOP_IND_TOPPRODUCTS_COL2'));
        
        $json='';
        $json = json_encode($return);

        return ($json);

    }
    public function getReport($organism,$timestart,$timeend,$limit,$report_format='pdf'){
        return Easysdi_dashboardHelper::getBirtReportProxy('json_2columns.rptdesign', $report_format, Indicator::getData($organism,$timestart,$timeend,1000));      
    }
    
    
}

