<?php
/**
* EasySDI, a solution to implement easily any spatial data infrastructure
* Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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

class ADMIN_publish {
	function deleteDiffusor($diffusor_id, $options)
	{
		$database =& JFactory::getDBO();
		$error = "";
		//the WPS address
		$database->setQuery("SELECT value FROM #__easysdi_config WHERE thekey='WPS_PUBLISHER'");
		$wpsAddress = $database->loadResult();
		
		//Get the server list from the WPS
		$wpsConfig = $wpsAddress."/config";
		if($diffusor_id != 'new')
		{
			$url = $wpsConfig."?operation=deletePublicationServer";
	  	$url.= "&id=".$diffusor_id;								 
			
			//store to the WPS					
			//echo $url."<br>";					 
			$xml = simplexml_load_file($url);
			//echo "<pre>";  print_r($xml);  echo "</pre>";
				
			//Look for an exception
			foreach($xml->xpath('//ows:ExceptionText') as $exc) { 
    		$exc->registerXPathNamespace('ows', 'http://www.opengis.net/ows/1.1../../../ows/1.1.0/owsExceptionReport.xsd'); 
				$error = (string)$exc;
				
			} 	
		}
		return $error;
	}
	
	function deleteScript($scriptId, $options)
	{
		$database =& JFactory::getDBO();

		if($scriptId != 0)
		{
			$database->setQuery( "DELETE FROM #__easysdi_publish_script where id=".$scriptId );
			$database->query();
			if ($database -> getErrorNum()) {
				$mainframe->enqueueMessage($database -> stderr(),"ERROR");
				return false;
			}
		}
		return true;
	}
	
	function editGlobalSettings($options)
	{

		global $mainframe;

		//
		//General pane
		//

		//enum of formats
		$format_rows = null;
		
		//partner list
		$partner_list = null;
		//instance of config in db
		$rowPublishConfig = null;
		//Config id
		$config_Id=null;
		$database =& JFactory::getDBO();

		//Get the config
		$database->setQuery( "SELECT id FROM #__easysdi_publish_config" );
		$rows = $database->loadObjectList() ;

		//Should be only one config
		if (count($rows) > 1)
		{
			//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage(JText::_("EASYSDI_PUBLISH_ERROR_CONFIG").$rows[0]->id,"INFO");
			$mainframe->redirect("index.php" );
			//exit();
		}
		//load the config
		$config_Id = $rows[0]->id;
		$rowPublishConfig = new publishConfig( $database );
		$rowPublishConfig->load($config_Id);

		//load Format list
		$database->setQuery( "SELECT publish_script_name FROM #__easysdi_publish_script WHERE publish_script_is_public=1 order by publish_script_name" );
		$format_rows = $database->loadObjectList() ;

		//load partner list
		$database->setQuery( "SELECT p.partner_id as value, j.name as text FROM #__easysdi_community_partner p, #__users j where p.user_id = j.id;" );
		$partner_list = $database->loadObjectList();

		//
		//Diffusion pane
		//
/*
		$query = "SELECT id as value, diffusion_server_name as text FROM #__easysdi_publish_diffusor";
		$query .= " ORDER BY diffusion_server_name";
		$database->setQuery( $query);
		$diffusor_rows = $database->loadObjectList();

		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}
	*/	
		//layer list
		//Search Layers regarding the limits
		//setup the pagination
		$option = JRequest::getVar('option','com_easysdi_publish');
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination_layer = JRequest::getVar('use_pagination_layer',1);
		
		
		//count existing layer for user, unit pagination
		$query = "SELECT COUNT(*) FROM #__easysdi_publish_layer";					
		$database->setQuery( $query );
		$total = $database->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		
		$search				= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
		$search				= JString::strtolower( $search );

	
		//the free search text
		$where = " WHERE l.featuresourceId=f.id AND l.partner_id=u.easysdi_user_id AND u.easysdi_user_id=p.partner_id AND p.user_id=j.id";
		if ($search)
		{
			$where .= ' and( LOWER(l.name) LIKE '.$database->Quote( '%'.$database->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(f.name) LIKE '.$database->Quote( '%'.$database->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(l.id) LIKE '.$database->Quote( '%'.$database->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(j.name) LIKE '.$database->Quote( '%'.$database->getEscaped( $search, true ).'%', false );
			$where .= ')';
		}

		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'l.id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
	
		// Test if filter is valid
		if ($filter_order <> "l.id" and $filter_order<>"j.name" and $filter_order <> "l.name" and $filter_order <> "l.title" and $filter_order <> "f.name" and $filter_order <> "l.creation_date" and $filter_order <> "l.update_date")
		{
			$filter_order		= "l.id";
			$filter_order_Dir	= "ASC";
		}
		
		//the order regarding column
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		//build the query
		$query = "SELECT l.id, j.name as joomlaname, l.name as layername, l.title, f.name as fsname, l.creation_date, l.update_date ";
		$query .= "FROM #__easysdi_publish_layer l, #__easysdi_publish_featuresource f, #__easysdi_publish_user u, #__easysdi_community_partner p, #__users j";
		$query .= $where;
		$query .= $orderby;
		
		//if pagination is used
		if ($use_pagination_layer) {
			$database->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else{
			$database->setQuery( $query);
		}

		//get results from db
		$layers = $database->loadObjectList();
		if ($database->getErrorNum()) {
			echo $database->stderr();
			return false;
		}

		HTML_ctrlpanel::ctrlPanelPublish($format_rows, $rowPublishConfig, $options, $config_Id, $partner_list, $layers, $use_pagination_layer, $pageNav, $filter_order, $filter_order_Dir, $search);
	}

	function saveConfig( $options, $uploadFileName ) {

		global $mainframe;
		$database=& JFactory::getDBO();

		//the WPS address
		$database->setQuery("SELECT value FROM #__easysdi_config WHERE thekey='WPS_PUBLISHER'");
		$wpsAddress = $database->loadResult();
		
		//Get the server list from the WPS
		$wpsConfig = $wpsAddress."/config";

		//save the global settings
		$publishConfig =& new publishConfig( $database );
		$publishConfig->id=$_POST['config_id'];
		$publishConfig->default_publisher_layer_number=$_POST['default_publisher_layer_number'];
		$publishConfig->default_dataset_upload_size=$_POST['default_dataset_upload_size'];
		//save the wps config key
		$database->setQuery("UPDATE #__easysdi_config SET value='".$_POST['publication_server_wps_address']."' WHERE thekey='WPS_PUBLISHER'");
		$database->query();
		$publishConfig->default_diffusion_server_id=$_POST['default_diffusion_server_id'];
		$publishConfig->default_datasource_handler=$_POST['default_datasource_handler'];

		//Makes an update because config_id is given. Otherwise it would make an insert
		if (!$publishConfig->store()) {
			//echo "<script> alert('".$rowAddress->getError()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
			exit();
		}

		//save the diffusor settings to the wps
		//If diffusor_id = 0 => create new, else update
		
		$dId = "";
		if($_POST['diffusor_id'] != 'new'){
			$dId = $_POST['diffusor_id'];
		}
		$diffusion_server_name=$_POST['diffusion_server_name'];
		$diffusor_type_id=$_POST['diffusor_type_id'];
		$diffusion_server_url=$_POST['diffusion_server_url'];
		$diffusion_server_username=$_POST['diffusion_server_username'];
		$diffusion_server_password=$_POST['diffusion_server_password'];
		$diffusion_server_db_name=$_POST['diffusion_server_db_name'];
		$diffusor_bd_type_id=$_POST['diffusor_bd_type_id'];
		$diffusion_server_db_url=$_POST['diffusion_server_db_url'];
		$diffusion_server_db_username=$_POST['diffusion_server_db_username'];
		$diffusion_server_db_password=$_POST['diffusion_server_db_password'];

		$url = $wpsConfig."?operation=managePublicationServer";
	  $url.= "&id=".$dId;
		$url.= "&name=".$diffusion_server_name;
		$url.= "&type=".$diffusor_type_id;
		$url.= "&url=".$diffusion_server_url;
		$url.= "&username=".$diffusion_server_username;
		$url.= "&password=".$diffusion_server_password;
		$url.= "&dbname=".$diffusion_server_db_name;
		$url.= "&dbtype=".$diffusor_bd_type_id;
		$url.= "&dburl=".$diffusion_server_db_url;
		$url.= "&dbusername=".$diffusion_server_db_username;
		$url.= "&dbpassword=".$diffusion_server_db_password;								 
		
			
		//Makes an update because diffusor_id is given. Otherwise it would make an insert
		//save only if diffusion server has a name, url, dbname, dbtype etc...
		if($_POST['diffusion_server_name'] != "" && $_POST['diffusor_type_id'] != "" && $_POST['diffusion_server_url'] != "" && $_POST['diffusion_server_db_name'] != "" && $_POST['diffusor_bd_type_id'] != "" && $_POST['diffusion_server_db_url'] != "" )
		{
			//store to the WPS					
			
			//echo $url."<br>";
						 
			$xml = simplexml_load_file($url);
			
			//echo "<pre>";  print_r($xml);  echo "</pre>";
			
			//Look for an exception
			foreach($xml->xpath('//ows:ExceptionText') as $exc) { 
    		$exc->registerXPathNamespace('ows', 'http://www.opengis.net/ows/1.1../../../ows/1.1.0/owsExceptionReport.xsd'); 
    		$mainframe->enqueueMessage((string)$exc,"ERROR");
				$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
    		exit;
			} 
		}
		
		
		//Save partner settings
		$user =& new publishUser($database);

		//does the publish_user exit?
		$query = "SELECT id FROM #__easysdi_publish_user where easysdi_user_id=".$_POST['easysdi_user_id'];
		$database->setQuery( $query);
		$publish_user = $database->loadObjectList();

		//Create new user or update
		if($publish_user != Array())
		$user->id = $publish_user[0]->id;
		$user->easysdi_user_id=$_POST['easysdi_user_id'];
		$user->publish_user_max_layers=$_POST['publish_user_max_layers'];
		$user->publish_user_total_space=$_POST['publish_user_total_space'];
		$user->publish_user_diff_server_id=$_POST['publish_user_diff_server_id'];
		//Do not store or update a user if no current partner has been selected
		if($_POST['easysdi_user_id'] != 0)
		{
			if (!$user->store()) {
				//echo "<script> alert('".$rowAddress->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
				exit();
			}

			$query = "SELECT id FROM #__easysdi_publish_user where easysdi_user_id=".$_POST['easysdi_user_id'];
			$database->setQuery( $query);
			$publish_user = $database->loadObjectList();

			//Update roles
			//get roles id reference from db
			$query = "SELECT role_id FROM #__easysdi_community_role where role_code='GEOSERVICE_DATA_MANA'";
			$database->setQuery( $query);
			$role_data_manager = $database->loadObjectList();
			
			$query = "SELECT role_id FROM #__easysdi_community_role where role_code='GEOSERVICE_MANAGER'";
			$database->setQuery( $query);
			$role_manager = $database->loadObjectList();
			
			//clear roles first
			$database->setQuery( "DELETE FROM #__easysdi_community_actor WHERE partner_id=".$_POST['easysdi_user_id']." AND (role_id=".$role_data_manager[0]->role_id." OR role_id=".$role_manager[0]->role_id.")");
			if (!$database->query()) {
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
				exit();
			}

			//update role values
			if (count ($_POST['user_roles_id'] )>0){
				foreach( $_POST['user_roles_id'] as $role )
				{

					$database->setQuery( "INSERT INTO #__easysdi_community_actor (role_id, partner_id, actor_update) VALUES (".$role.",".$user->easysdi_user_id.",now())" );
					if (!$database->query()) {
						//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
						exit();
					}
				}
			}
			
			//clear scripts first
			$database->setQuery( "DELETE FROM #__easysdi_publish_script_map WHERE publish_user_id=".$publish_user[0]->id);
			if (!$database->query()) {
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
				exit();
			}

			//update script values
			if (count ($_POST['user_scripts_id'] )>0){
				foreach( $_POST['user_scripts_id'] as $srpt )
				{
					$database->setQuery( "INSERT INTO #__easysdi_publish_script_map (publish_user_id, publish_script_id) VALUES (".$publish_user[0]->id.",".$srpt.")" );
					if (!$database->query()) {
						//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
						exit();
					}
				}
			}

		}

		//Update scripts
		$script =& new script($database);
		//If man_script_id = 0 => create new, else update
		if($_POST['man_script_id'] != 0)
		$script->id=$_POST['man_script_id'];
		$script->publish_script_name=$_POST['publish_script_name'];
		$script->publish_script_description=$_POST['publish_script_description'];
		$script->publish_script_conditions=$_POST['publish_script_conditions'];
		if(JRequest::getVar('publish_script_is_public') == "on")
			$script->publish_script_is_public=1;
		else
			$script->publish_script_is_public=0;
		$script->publish_script_file=$uploadFileName;
		//Makes an update because man_script_id is given. Otherwise it would make an insert
		//save only if script has a name
		if($_POST['publish_script_name'] != "")
		{
			if (!$script->store()) {
				//echo "<script> alert('".$rowAddress->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=editGlobalSettings" );
				exit();
			}
		}

	}

}