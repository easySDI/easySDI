<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class Indicator{
    
    public function getData($organism,$timestart,$timeend,$limit=0){
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true)
                ->select('count(odif.id) as \'total_ext\'')
                
                ->from($db->quoteName('#__sdi_order','o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion','odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion','dif') .  ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version','v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource','r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism','org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                
                ->where('o.sent between \''.date("c",$timestart).'\' and  \''.date("c",$timeend).'\' ')
                ->where('o.ordertype_id = 1');
        if($organism != 'all'){
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query,0,$limit);
        $resNbExt = $db->loadAssoc();
        
        $query = $db->getQuery(true)
                ->select('count(odif.id) as \'total_ext_manual\'')
                
                ->from($db->quoteName('#__sdi_order','o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion','odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion','dif') .  ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version','v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource','r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism','org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                
                ->where('o.sent between \''.date("c",$timestart).'\' and  \''.date("c",$timeend).'\' ')
                ->where('o.ordertype_id = 1')
                ->where('dif.productmining_id = 2');
        if($organism != 'all'){
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query,0,$limit);
        $resNbExtManu = $db->loadAssoc();
        
        $query = $db->getQuery(true)
                ->select('count(odif.id) as \'total_ext_auto\'')
                
                ->from($db->quoteName('#__sdi_order','o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion','odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion','dif') .  ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version','v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource','r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism','org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                
                ->where('o.sent between \''.date("c",$timestart).'\' and  \''.date("c",$timeend).'\' ')
                ->where('o.ordertype_id = 1')
                ->where('dif.productmining_id = 1');
        if($organism != 'all'){
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query,0,$limit);
        $resNbExtAuto = $db->loadAssoc();   
        
        $query = $db->getQuery(true)
                ->select('count(odif.id) as \'total_ext_fee\'')
                
                ->from($db->quoteName('#__sdi_order','o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion','odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion','dif') .  ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version','v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource','r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism','org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                
                ->where('o.sent between \''.date("c",$timestart).'\' and  \''.date("c",$timeend).'\' ')
                ->where('o.ordertype_id = 1')
                ->where('dif.pricing_id = 2');
        if($organism != 'all'){
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query,0,$limit);
        $resNbExtFee = $db->loadAssoc(); 
        
        $query = $db->getQuery(true)
                ->select('count(odif.id) as \'total_ext_free\'')
                
                ->from($db->quoteName('#__sdi_order','o'))
                ->join('INNER', $db->quoteName('#__sdi_order_diffusion','odif') . ' ON (' . $db->quoteName('o.id') . ' = ' . $db->quoteName('odif.order_id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_diffusion','dif') .  ' ON (' . $db->quoteName('odif.diffusion_id') . ' = ' . $db->quoteName('dif.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_version','v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource','r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism','org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                
                ->where('o.sent between \''.date("c",$timestart).'\' and  \''.date("c",$timeend).'\' ')
                ->where('o.ordertype_id = 1')
                ->where('dif.pricing_id = 1');
        if($organism != 'all'){
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query,0,$limit);
        $resNbExtFree = $db->loadAssoc();         
        
        
        $return = new stdClass();
        $return->data = array_merge($resNbExt,$resNbExtManu,$resNbExtAuto,$resNbExtFee,$resNbExtFree);

        $json = '';
        $json = json_encode($return);

        return ($json);

    }
    public function getReport($organism,$timestart,$timeend,$limit,$report_format='pdf'){
        return Easysdi_dashboardHelper::getBirtReportProxy('json_2columns.rptdesign', $report_format, Indicator::getData($organism,$timestart,$timeend,1000));      
    }
    
    
}

