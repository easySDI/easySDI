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

class ADMIN_serviceaccount 
{
	function editServiceAccount ($id, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$account;
		if ($id)
		{
			$query = "SELECT  p.id, u.name, u.username, u.usertype 
					  FROM  #__users u INNER JOIN #__sdi_account p ON u.id = p.user_id 
					  WHERE p.id=$id";
			$query .= " LIMIT 1 ";
			$db->setQuery( $query);
			$accounts = $db->loadObjectList();
			$account = $accounts[0];
		}
		else
		{
			$query = "SELECT p.id, u.name, u.username, u.usertype 
						FROM #__sdi_map_serviceaccount sa 
						INNER JOIN #__sdi_account p ON p.id = sa.account_id 
						INNER JOIN #__users u ON u.id = p.user_id";
			$query .= " LIMIT 1 ";
			$db->setQuery( $query);
			$accounts = $db->loadObjectList();
			$account = $accounts[0];
		}
		$query = "SELECT sa.*, u.name, u.username, u.usertype 
				  FROM #__sdi_map_serviceaccount sa 
				  INNER JOIN #__sdi_account p ON p.id = sa.account_id
				  INNER JOIN #__users u ON u.id = p.user_id";
		$query .= " LIMIT 1 ";
		$db->setQuery( $query);
		$services_account = $db->loadObjectList();
		$service_account = $services_account[0];
				
		//Get availaible easysdi account
		$db->setQuery( "SELECT p.id as value, u.name as text 
						FROM #__users u 
						INNER JOIN #__sdi_account p 
						ON u.id = p.user_id " );
		$rowsAccount = $db->loadObjectList();
		echo $db->getErrorMsg();			

		HTML_serviceaccount::editServiceAccount($service_account,$account, $rowsAccount, $option);
	}
	
	
	function saveServiceAccount($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
		
		$serviceAccount = new serviceAccount ($db);
		if (!$serviceAccount->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=serviceAccount" );
			exit();
		}		
				
		if (!$serviceAccount->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=serviceAccount" );
			exit();
		}
	}
}
?>