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
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_community_profile";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_community_profile ";
		$query .= " ORDER BY profile_code";
		if ($use_pagination) 
		{
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else
		{
			$db->setQuery( $query);
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_profile::listProfile($use_pagination, $rows, $pageNav, $option);
	}
	
	function editProfile ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
				
		$profile = new profile ($db);
		$profile->load($id);
				
		//Get availaible roles
		$db->setQuery( "SELECT role_id as value, role_name as text FROM #__easysdi_community_role" );
		$rowsRoles = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current roles
		$db->setQuery( "SELECT id_role as value FROM #__easysdi_map_profile_role WHERE id_prof=$id" );
		$rowsSelectedRoles = $db->loadObjectList();
		echo $db->getErrorMsg();

		HTML_profile::editProfile($profile,$rowsRoles,$rowsSelectedRoles, $option);
	}
	
	
	function saveProfile($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$profile_id = JRequest::getVar('id');
		
		$db->setQuery( "DELETE FROM #__easysdi_map_profile_role WHERE id_prof=".$profile_id );
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
					$db->setQuery( "INSERT INTO #__easysdi_map_profile_role (id_role, id_prof) VALUES (".$roles.",".$profile_id.")" );
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