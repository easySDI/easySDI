<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_dashboard
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
class Indicator {

    public function getData($organism, $timestart, $timeend, $limit = 0) {
        $db = JFactory::getDbo();
        /* Total diffusions */
        $query = $db->getQuery(true)
                ->select(' count(dif.id) as \'total_diff\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')');
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDif = $db->loadAssoc();

        /* Total diffusions with extraction*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_hasextraction\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasdownload = 0') 
                ->where('dif.hasextraction = 1')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifExt = $db->loadAssoc();
        
        /* Total diffusions with download*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_hasdownload\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 0') 
                ->where('dif.hasdownload = 1')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifDown = $db->loadAssoc();
        
        /* Total diffusions with download and extraction*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_hasdownandext\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 1') 
                ->where('dif.hasdownload = 1')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifDownExt = $db->loadAssoc();
        
        /* Total diffusions free*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_free\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.pricing_id = 1')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifFree = $db->loadAssoc();      
        
       /* Total diffusions fee*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_fee\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.pricing_id = 2')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifFee = $db->loadAssoc();    
        
       /* Total manual mining*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_manual\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 1')
                ->where('dif.productmining_id = 2')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifManu = $db->loadAssoc();   
        
       /* Total auto mining*/
        $query = $db->getQuery(true)
                ->select('count(dif.id) as \'total_diff_auto\'')
                ->from($db->quoteName('#__sdi_diffusion', 'dif'))
                ->join('INNER', $db->quoteName('#__sdi_version', 'v') . ' ON (' . $db->quoteName('dif.version_id') . ' = ' . $db->quoteName('v.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_resource', 'r') . ' ON (' . $db->quoteName('v.resource_id') . ' = ' . $db->quoteName('r.id') . ')')
                ->join('INNER', $db->quoteName('#__sdi_organism', 'org') . ' ON (' . $db->quoteName('r.organism_id') . ' = ' . $db->quoteName('org.id') . ')')
                ->where('dif.hasextraction = 1')
                ->where('dif.productmining_id = 1')        ;
        if ($organism != 'all') {
            $query->where($db->quoteName('org.id') . ' = ' . $organism);
        }
        $db->setQuery($query, 0, $limit);
        $resNbDifAuto = $db->loadAssoc();         
        
        $return = new stdClass();
        $return->data = array_merge($resNbDif,$resNbDifExt,$resNbDifDown,$resNbDifDownExt,$resNbDifFree,$resNbDifFee,$resNbDifManu,$resNbDifAuto);

        $json = '';
        $json = json_encode($return);

        return ($json);
    }

    public function getReport($organism, $timestart, $timeend, $limit, $report_format = 'pdf') {
        return Easysdi_dashboardHelper::getBirtReportProxy('json_2columns.rptdesign', $report_format, Indicator::getData($organism, $timestart, $timeend, 1000));
    }

}
