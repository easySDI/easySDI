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

class ADMIN_profile
{
	function listProfile($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();

		//Global variable for pagination
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);

		//Pagination
		$query ='SELECT COUNT(*) FROM #__easysdi_community_profile';
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);

		//Request list of profile
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
			echo $db->stderr();
			return false;
		}
		HTML_profile::listProfile($use_pagination, $rows, $pageNav,$option);
	}

	function editProfile($profile_id, $option)
	{
		$database =& JFactory::getDBO();
		if($profile_id != 0)
		{
			$profile = new profile( $database );
			$profile->load( $profile_id);
		}
		//Get availaible roles
		$database->setQuery( "SELECT role_id as value, role_name as text FROM #__easysdi_community_role" );
		$rowsRoles = $database->loadObjectList();
		echo $database->getErrorMsg();

		//Get current roles
		$database->setQuery( "SELECT id_role as value FROM #__easysdi_map_profile_role WHERE id_prof=$profile_id" );
		$rowsSelectedRoles = $database->loadObjectList();
		echo $database->getErrorMsg();
		HTML_profile::editProfile($profile,$rowsRoles,$rowsSelectedRoles, $option);
	}

	function deleteProfile($profile_id,$option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $profile_id ) || count( $profile_id ) < 1)
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProfile" );
			exit;
		}
		foreach( $profile_id as $id )
		{
			$profile = new profile( $database );
			$profile->load( $id );

			if (!$profile->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProfile" );
			}
		}
	}

	function saveProfile($profile_id,$option)
	{
		global $mainframe;
		$database=& JFactory::getDBO();

		$profile= new profile( $database );
		if (!$profile->bind( $_POST ))
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProfile" );
			exit();
		}
		$profile->profile_code = str_replace(" ","",$profile->profile_code);
		if (!$profile->store(false))
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProfile" );
			exit();
		}else
		{
			if($database->insertid()) $profile_id = $database->insertid();
			$database->setQuery( "DELETE FROM #__easysdi_map_profile_role WHERE id_prof=".$profile_id );
			if (!$database->query())
			{
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			if(isset($_POST['roles']))
			{
				if (count ($_POST['roles'])>0)
				{
					foreach( $_POST['roles'] as $roles )
					{
						$database->setQuery( "INSERT INTO #__easysdi_map_profile_role (id_role, id_prof) VALUES (".$roles.",".$profile_id.")" );
						if (!$database->query())
						{
							$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						}
					}
				}
			}
		}
	}
}
?>