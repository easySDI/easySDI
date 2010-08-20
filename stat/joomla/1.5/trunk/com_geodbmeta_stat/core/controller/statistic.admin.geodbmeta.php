<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */

defined('_JEXEC') or die('Restricted access');
class ADMIN_statistic {
		
	function listStatistic(){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$option=JRequest::getVar("option");
		
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
		$search = $mainframe->getUserStateFromRequest( "searchStatistic{$option}", 'searchStatistic', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		
		$statisticType= JRequest::getVar("statisticType","#__sdi_stat_performance");
		$filter="";
		
		//Text search
		if ($search)
		{
			switch($statisticType)
			{
				case "#__sdi_stat_performance":
					$filter .= ' where LOWER(service) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
					$filter .= ' or LOWER(operation) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
					break;
				case "#__sdi_stat_attribute":
					$filter .= ' where LOWER(attribute_name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
					break; 
				case "#__sdi_stat_metadata":
					$filter .= ' where LOWER(metadata_id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
					break;
			}		
		}
		
		//Date search
		$dateFormat = JRequest::getVar("dateFormat","%d-%m-%Y");
		$DateFrom = JRequest::getVar("DateFrom","");
		$DateTo = JRequest::getVar("DateTo","");
		if ($DateFrom !="")
			$DateFromFilter= " date >= STR_TO_DATE('".$DateFrom."', '".$dateFormat."')";
		if ($DateTo !="")
			$DateToFilter = " date <= STR_TO_DATE('".$DateTo."', '".$dateFormat."')";
		
		if ($DateFromFilter || $DateToFilter)
		{
			if($filter != "")
				$filter .= " AND ";
			else
				$filter .= " WHERE ";
			
			if($DateFromFilter)
				$filter .= $DateFromFilter;
			else
				$filter .= $DateToFilter;
		}
		if ($DateFromFilter && $DateToFilter)
		{
			$filter .= " AND ";
			$filter .= $DateToFilter;
		}
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		switch($statisticType)
		{
			case "#__sdi_stat_performance":
				if ( 		$filter_order <> "service" 
						and $filter_order <> "operation"  
						and $filter_order <> "min_time" 
						and $filter_order <> "max_time" 
						and $filter_order <> "average_time")
				{
					$filter_order		= "id";
					$filter_order_Dir	= "ASC";
				}
				break;
			case "#__sdi_stat_attribute":
				if ( 		$filter_order <> "attribute_name"
						and $filter_order <> "count"
						and $filter_order <> "date")
				{
					$filter_order		= "id";
					$filter_order_Dir	= "ASC";
				}
				break; 
			case "#__sdi_stat_metadata":
				if ( 		$filter_order <> "metadata_id"
						and $filter_order <> "count"
						and $filter_order <> "date")
				{
					$filter_order		= "id";
					$filter_order_Dir	= "ASC";
				}
				break;
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;		
		
		$query = "select * from  ".$statisticType;
		$query .= $filter;
		$query .= $orderby;
		$db->setQuery($query);
		
		$queryCount = "select COUNT(*) from  ".$statisticType;
		$queryCount .= $filter;
		$db->setQuery($queryCount);
		$total = $db->loadResult();
		
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
		}	

		$pageNav = new JPagination($total,$limitstart,$limit);
				
		$db->setQuery($query,$limitstart,$limit);		
		$statistics = $db->loadObjectList() ;
		if ($db->getErrorNum()) {
			echo "<div class='alert'>";			
			echo 			$db->getErrorMsg();
			echo "</div>";
		}	
		
		HTMLadmin_statistic::listStatistic($pageNav,$statistics,$option,$statisticType,$DateFrom,$DateTo,$filter_order_Dir, $filter_order,$search);
	}
}
?>
