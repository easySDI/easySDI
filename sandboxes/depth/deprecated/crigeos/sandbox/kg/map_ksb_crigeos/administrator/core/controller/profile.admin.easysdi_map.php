<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class ADMIN_profile 
{
	function listProfile ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchProfile{$option}", 'searchProfile', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_accountprofile";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_accountprofile ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "description")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		$query .= $orderby;
				
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_profile::listProfile( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option);
	}
	
	function editProfile ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
				
		$accountprofile = new accountprofile ($db);
		$accountprofile->load($id);
				
		//Get availaible roles
		$db->setQuery( "SELECT id as value, name as text FROM #__sdi_list_role" );
		$rowsRoles = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current roles
		$db->setQuery( "SELECT role_id as value FROM #__sdi_profile_role WHERE profile_id=$id" );
		$rowsSelectedRoles = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_profile::editProfile($accountprofile,$rowsRoles,$rowsSelectedRoles, $option);
	}
	
	
	function saveProfile($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$profile_id = JRequest::getVar('id');
		
		$db->setQuery( "DELETE FROM #__sdi_profile_role WHERE profile_id=".$profile_id );
		if (!$db->query()) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}
		if(isset($_POST['roles']))
		{
			if (count ($_POST['roles'])>0)
			{					
				foreach( $_POST['roles'] as $roles )
				{
					$db->setQuery( "INSERT INTO #__sdi_profile_role (role_id, profile_id) VALUES (".$roles.",".$profile_id.")" );
					if (!$db->query()) 
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					}
				}
			}
		}
	}

}
?>