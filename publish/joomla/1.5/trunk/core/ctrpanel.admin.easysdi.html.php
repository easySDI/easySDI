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
require_once(JPATH_SITE.DS.'components'.DS.'com_easysdi_publish'.DS.'core'.DS.'proxy.php');

class HTML_ctrlpanel {

	function display_xml_error($error, $xml)
  {
    $return  = $xml[$error->line - 1] . "\n";
    $return .= str_repeat('-', $error->column) . "^\n";

    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "Warning $error->code: ";
            break;
         case LIBXML_ERR_ERROR:
            $return .= "Error $error->code: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "Fatal Error $error->code: ";
            break;
    }

    $return .= trim($error->message) .
               "\n  Line: $error->line" .
               "\n  Column: $error->column";

    if ($error->file) {
        $return .= "\n  File: $error->file";
    }

    return "$return\n\n--------------------------------------------\n\n";
  }
	
	function ctrlPanelPublish($format_rows, $crs_rows, $rowPublishConfig, $options, $config_Id, $partner_list, $layers, $use_pagination_layer, $pageNav, $filter_order, $filter_order_Dir, $search){
		//diffusor_rows obsolete
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		$index = JRequest::getVar('tabIndex');
		$tabs =& JPANE::getInstance('Tabs', array('startOffset'=>$index));			
		
		//the WPS address
		$wpsAddress = config_easysdi::getValue("WPS_PUBLISHER");
		
		//Get the server list from the WPS
		$wpsConfig = $wpsAddress."/services/config";
		
		$url = $wpsConfig."?operation=listPublicationServers";

		$doc = SITE_proxy::fetch($url, false);
		$xml = simplexml_load_string($doc);
		
		//check error by getting the doc
		if(!$xml){		
		   echo JText::_("EASYSDI_PUBLISH_ERROR_CONNECTING_TO_WPS")." ".$wpsAddress;
		   return;
		}
		//get the server edit list
		$servers = $xml->server;
		$diffusor_rows = Array();
		foreach ($servers as $row)
		{	
			$diffusor_rows[] = (object)array(
	  														'value' =>  (string)$row->id,
    														'text' => (string)$row->name);
		}

		$diffusionServers = $diffusor_rows;
		//$diffusionServers [] = JHTML::_('select.option','0', JText::_("EASYsdi_publish_diffuser_LIST_TITLE") );
		
		//File format for the user
		$format_list = array();
		$database->setQuery( "SELECT id AS value, publish_script_display_name as text FROM #__sdi_publish_script WHERE publish_script_is_public=1");
		$format_list = $database->loadObjectList();
		$format_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_PARTNER_LIST_TITLE"));
		$format_list = array_reverse($format_list);
		
		$crsId = JRequest::getVar('crs_id', 'new');
		$crsEdit = Array();
		$crsEdit[] = JHTML::_('select.option','new', JText::_("EASYSDI_PUBLISH_NEW_CRS") );
		$crsEdit = array_merge($crsEdit, $crs_rows);
		$crs = null;
		if($crsId != 'new'){
		   $database->setQuery( "SELECT * FROM #__sdi_publish_crs WHERE id=".$crsId);
		   $crs = $database->loadObjectList();
		   $crs = $crs[0];
		}else{
			 $crs = (object)array(
	  			'id' =>  "",
    				'code' => "",
    				'name' => ""
	  			 );
		 }
		
?>
		<form action="index.php" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
			echo $tabs->startPane("partnerPane");
			echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_GLOBAL_SETTINGS"),"partnerPane0");
?>	
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_PUBLISH_DEFAULT_VALUES"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_DEFAULT_LAYER_NUMBER"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="100" name="default_publisher_layer_number" value="<?php echo $rowPublishConfig->default_publisher_layer_number; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_DEFAULT_DATASET_UPLOAD_SIZE"); ?> : </td>
								<td><input class="inputbox" type="text" size="10" maxlength="100" name="default_dataset_upload_size" value="<?php echo $rowPublishConfig->default_dataset_upload_size; ?>" /></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PUBLISH_DEFAULT_DIFFUSION_SERVER"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$diffusionServers, 'default_diffusion_server_id', 'size="1" class="inputbox"', 'value', 'text', $rowPublishConfig->default_diffusion_server_id); ?></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_FORMAT_HANDLER"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$format_list, 'default_datasource_handler', 'size="1" class="inputbox"', 'value', 'text', $rowPublishConfig->default_datasource_handler); ?></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_DEFAULT_CRS"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$crs_rows, 'default_prefered_crs', 'size="1" class="inputbox"', 'value', 'text', $rowPublishConfig->default_prefered_crs); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_SUPPORTED_FORMAT_LIST"); ?></b></legend>
							<ul>
							<?php							
								foreach( $format_rows as $format ) 
									echo "<li>".$format->publish_script_name."</li>";
							?>
							</ul>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_CRS"); ?></b></legend>
						<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_MANAGE_CRS_LIST"); ?></b></legend>
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
									<td>
											<?php echo JHTML::_("select.genericlist",$crsEdit, 'crs_id', 'size="1" class="inputbox" onChange="javascript:reloadTab(0,\'editGlobalSettings\');"', 'value', 'text', $crsId); ?>
									</td>
									<td>
									  <INPUT type="button" name="deleteCrs" id="deleteCrs" value="<?php echo JText::_("EASYSDI_PUBLISH_DELETE"); ?>" onClick="javascript:reloadTab(0,'deleteCrs');">
								          <INPUT type="button" name="addCrs" id="addCrs" value="<?php echo JText::_("EASYSDI_PUBLISH_SAVE"); ?>" onClick="javascript:reloadTab(0,'saveConfig');">
									</td>
								</tr>
							</table>
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
									<td><?php echo JText::_("EASYSDI_PUBLISH_CRS_NAME"); ?> : </td>
									<td><input class="inputbox" type="text" size="20" maxlength="100" name="crs_name" value="<?php echo $crs->name; ?>" /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("EASYSDI_PUBLISH_CRS_CODE"); ?> : </td>
									<td><input class="inputbox" type="text" size="20" maxlength="100" name="crs_code" value="<?php echo $crs->code; ?>" /></td>
								</tr>
							</table>
						</fieldset>
						<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_SUPPORTED_CRS_LIST"); ?></b></legend>
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
									<td colspan="2">
									<ul>
									<?php							
										foreach( $crs_rows as $crs ) 
											echo "<li>".$crs->text."</li>";
									?>
									</ul>
									</td>
								</tr>
							</table>
						</fieldset>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_DIFFUSION"),"partnerPane1");
		
		//echo "<pre>";  print_r($xml);  echo "</pre>";
		
		$diffusorEdit = Array();
		$diffusorEdit[] = JHTML::_('select.option','new', JText::_("EASYSDI_PUBLISH_NEW_SERVER") );
		$diffusorEdit = array_merge($diffusorEdit, $diffusor_rows);
	
		//get the diffuser type list
		$serverType = $xml->serverTypes->serverType;		
		$diffusor_types = Array();
		foreach ($serverType as $row)
		{	
			$diffusor_types[] = (object)array(
	  														'value' =>  (string)$row->id,
    														'text' => (string)$row->name);
		}
				
		//get the database type list
		$dbType = $xml->dbTypes->dbType;		
		$diffusor_db_types = Array();
		foreach ($dbType as $row)
		{	
			$diffusor_db_types[] = (object)array(
	  														'value' =>  (string)$row->id,
    														'text' => (string)$row->name);
		}
		
		//load the default or specified diffusor REPLACE WITH IN MEMORY LIST
		$diffId = JRequest::getVar('diffusor_id', 'new');
		//If it's an existing diffusor, else new.
		$diffusor = null;
		if($diffId != 'new'){
		   $diffusor = $xml->xpath("//server[@id=$diffId]");
		   $diffusor = $diffusor[0];
		}else{
			 $diffusor = (object)array(
	  													'id' =>  "",
    														'name' => "",
    														'type' => "",
    														'url' => "",
    														'username' => "",
    														'password' => "",
    														'dbscheme' => "",
														'dbtemplate' => "",
    														'dbname' => "",
    														'dbtype' => "",
    														'dburl' => "",
    														'dbusername' => "",
    														'dbpassword' => "",
	  													 );
		 }
		 		 
		 $databseName = $diffusor->dbname;
     $pos = strstr($databseName, '@');
		 if(strlen($pos) > 0) {
		 		$databseName = explode('@', $databseName);
		 		$databseName = $databseName[0];
		 }
	  
		?>
	  <fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_SERVER_LIST"); ?></b></legend>
	  				<table class="adminlist" width="100%">
								<thead>
									<tr>
										<th width="10%" align="left"><?php   echo JText::_("EASYSDI_PUBLISH_NAME"); ?></th>
										<th width="30%" align="left"><?php   echo JText::_("EASYSDI_PUBLISH_TYPE");?></th>
										<th width="30%" align="left"><?php   echo JText::_("EASYSDI_PUBLISH_URL");?></th>
										<th width="30%" align="left"><?php   echo JText::_("EASYSDI_PUBLISH_USERNAME");?></th>
										<th width="30%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_PASSWORD");?></th>
										<th width="10%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_DATABASE");?></th>
										<th width="10%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_TYPE");?></th>
										<th width="10%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_URL");?></th>
										<th width="10%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_SCHEMA");?></th>
										<th width="30%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_USERNAME");?></th>
										<th width="10%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_PASSWORD");?></th>
										<!--<th width="10%" align="center">&nbsp;</th>-->
									</tr>
								</thead>
								<tbody>
<?php
								$k = 0;
								$i=0;
    
								foreach ($servers as $row)
								{
?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left"><?php echo $row->name; ?></td>
										<td align="center"><?php echo $row->type; ?></td>
										<td align="center"><?php echo $row->url; ?></td>
										<td align="center"><?php echo $row->username; ?></td>
										<td align="center"><?php echo $row->password; ?></td>
										<td align="left"><?php echo $row->dbname; ?></td>
										<td align="center"><?php echo $row->dbtype; ?></td>
										<td align="center"><?php echo $row->dburl; ?></td>
										<td align="center"><?php echo $row->dbscheme; ?></td>
										<td align="center"><?php echo $row->dbusername; ?></td>
										<td align="center"><?php echo $row->dbpassword; ?></td>
									  <!-- <td align="center"><a href="index.php?option=com_easysdi_publish&task=viewLayer&id=<?php echo $row->id; ?>"><?php echo JText::_("EASYSDI_PUBLISH_VIEW"); ?></a></td> -->
									</tr>
<?php
									$k = 1 - $k;
									$i++;
								}
?>
								</tbody>
				</table>
		</fieldset>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_SELECT_DIFFUSION_SERVER"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td>
										<td><?php echo JHTML::_("select.genericlist",$diffusorEdit, 'diffusor_id', 'size="1" class="inputbox" onChange="javascript:reloadTab(1,\'editGlobalSettings\');"', 'value', 'text', $diffId); ?></td>
								</td>
								<td><INPUT type="button" name="deleteDiffusor" id="deleteDiffusor" value="<?php echo JText::_("EASYSDI_PUBLISH_DELETE"); ?>" onClick="javascript:reloadTab(1,'deleteDiffusor');"></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_PUBLISH_VALUES"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="20" maxlength="100" name="diffusion_server_name" value="<?php echo $diffusor->name; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_TYPE"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$diffusor_types, 'diffusor_type_id', 'size="1" class="inputbox"', 'value', 'text', $diffusor->type); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="diffusion_server_url" value="<?php echo $diffusor->url; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_USERNAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="20" maxlength="100" name="diffusion_server_username" value="<?php echo $diffusor->username; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_PASSWORD"); ?> : </td>
								<td><input class="inputbox" type="text" size="20" maxlength="100" name="diffusion_server_password" value="<?php echo $diffusor->password; ?>" /></td>
							</tr>
							<tr>
								<td colspan="2">
									<fieldset>
									<legend><b><?php echo JText::_("EASYSDI_PUBLISH_SPATIAL_DATABASE"); ?></b></legend>
										<table border="0" cellpadding="3" cellspacing="0">
										<tr>
											<td><?php echo JText::_("EASYSDI_PUBLISH_DATABASE"); ?> : </td>
											<td><input class="inputbox" type="text" size="20" maxlength="100" name="diffusion_server_db_name" value="<?php echo $databseName; ?>" /></td>
										</tr>
										<tr>
												<td><?php echo JText::_("EASYSDI_PUBLISH_TYPE"); ?> : </td>
												<td><?php echo JHTML::_("select.genericlist",$diffusor_db_types, 'diffusor_bd_type_id', 'size="1" class="inputbox"', 'value', 'text', $diffusor->dbtype); ?></td>
										</tr>
										<tr>
												<td><?php echo JText::_("EASYSDI_PUBLISH_URL"); ?> : </td>
												<td><input class="inputbox" type="text" size="50" maxlength="100" name="diffusion_server_db_url" value="<?php echo $diffusor->dburl; ?>" /></td>
										</tr>
										<tr>
											<td><?php echo JText::_("EASYSDI_PUBLISH_SCHEMA"); ?> : </td>
											<td><input class="inputbox" type="text" size="50" maxlength="100" name="diffusion_server_db_scheme" value="<?php echo $diffusor->dbscheme; ?>" /></td>
										</tr>
										<tr>
											<td><?php echo JText::_("EASYSDI_PUBLISH_TEMPLATE"); ?> : </td>
											<td><input class="inputbox" type="text" size="50" maxlength="100" name="diffusion_server_db_template" value="<?php echo $diffusor->dbtemplate; ?>" /></td>
										</tr>
										<tr>
											<td><?php echo JText::_("EASYSDI_PUBLISH_USERNAME"); ?> : </td>
											<td><input class="inputbox" type="text" size="20" maxlength="100" name="diffusion_server_db_username" value="<?php echo $diffusor->dbusername; ?>" /></td>
										</tr>
										<tr>
											<td><?php echo JText::_("EASYSDI_PUBLISH_PASSWORD"); ?> : </td>
											<td><input class="inputbox" type="text" size="20" maxlength="100" name="diffusion_server_db_password" value="<?php echo $diffusor->dbpassword; ?>" /></td>
										</tr>
										</table>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td align="right"><INPUT type="button" name="addDiffServer" id="addDiffServer" value="<?php echo JText::_("EASYSDI_PUBLISH_SAVE"); ?>" onClick="javascript:reloadTab(1,'saveConfig');"></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_PARTNER_SETTINGS"),"partnerPane2");
		
		$partner_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_PARTNER_LIST_TITLE"));
		
		//load the default or specified user
		$publishUser = new publishUser( $database );
		$userId = JRequest::getVar('easysdi_user_id', 0);
		//If it's an existing diffusor, else new.
		
		if($userId != 0){
			$query = "SELECT id FROM #__sdi_publish_user where easysdi_user_id=".$userId;
			$database->setQuery( $query);
			$tempUsr = $database->loadObjectList();
			if($tempUsr != Array())
				$publishUser->load($tempUsr[0]->id);
		}
		
		$selectedDiffusor;
		if($publishUser->publish_user_diff_server_id != "")
			$selectedDiffusor = $publishUser->publish_user_diff_server_id;
		else
			$selectedDiffusor = $rowPublishConfig->default_diffusion_server_id;
		//load user roles, for now, we have two roles
		$database->setQuery( "SELECT id as value, code as text FROM #__sdi_list_role where code='GEOSERVICE_DATA_MANA' OR code='GEOSERVICE_MANAGER';");
		$role_list = $database->loadObjectList();
		HTML_ctrlpanel::alter_array_value_with_Jtext($role_list);
		//$role_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_PARTNER_ROLE_LIST_TITLE"));
		
		$currentPartner = JRequest::getVar('easysdi_user_id')==''?$publishUser->easysdi_user_id:JRequest::getVar('easysdi_user_id');
		$selectedRole = array();
		$database->setQuery( "SELECT role_id AS value FROM #__sdi_actor WHERE account_id=".$currentPartner);
		//$database->setQuery( "SELECT m.role_id AS value FROM #__sdi_publish_user_role_map m, #__sdi_publish_user u  WHERE u.id = m.publish_user_id AND u.easysdi_user_id=".$currentPartner);
		$selectedRole = $database->loadObjectList();
		
		//load scripts
		$database->setQuery( "SELECT id as value, publish_script_name as text FROM #__sdi_publish_script where publish_script_is_public=0;");
		$script_list = $database->loadObjectList();
		$selectedScript = array();
		$database->setQuery( "SELECT m.publish_script_id AS value FROM #__sdi_publish_script_map m, #__sdi_publish_user u  WHERE u.id = m.publish_user_id AND u.easysdi_user_id=".$currentPartner);
		$selectedScript = $database->loadObjectList();
		
		
		
?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_PARTNER"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JHTML::_("select.genericlist",$partner_list, 'easysdi_user_id', 'size="1" class="inputbox" onChange="javascript:reloadTab(2,\'editGlobalSettings\');"', 'value', 'text', $userId); ?></td>
							</tr>
						</table>
					</fieldset>
								
				</td>
			</tr>
			<tr>
				<td>
					<div id="partnerSettings">
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_PARTNER_VALUES"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_PARTNER_ROLE"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$role_list, 'user_roles_id[]', 'size="'.count($role_list).'" multiple="true" class="selectbox"', 'value', 'text', $selectedRole ); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_MAX_LAYERS"); ?> : </td>
								<td><input class="inputbox" type="text" size="20" maxlength="100" name="publish_user_max_layers" value="<?php echo $publishUser->publish_user_max_layers == 0 ? $rowPublishConfig->default_publisher_layer_number : $publishUser->publish_user_max_layers; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_MAX_SPACE"); ?> : </td>
								<td><input class="inputbox" type="text" size="20" maxlength="100" name="publish_user_total_space" value="<?php echo $publishUser->publish_user_total_space == 0 ? $rowPublishConfig->default_dataset_upload_size : $publishUser->publish_user_total_space; ?>" /></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PUBLISH_DIFF_SERVER_NEW_SERVICE"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$diffusionServers, 'publish_user_diff_server_id', 'size="1" class="inputbox"', 'value', 'text', $selectedDiffusor); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_AVAILABLE SCRIPT_FOR_USER"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$script_list, 'user_scripts_id[]', 'size="'.count($script_list).'" multiple="true" class="selectbox"', 'value', 'text', $selectedScript ); ?></td>
							</tr>
						</table>
					</fieldset>
				</div>			
				</td>
			</tr>
		</table>
		
		
<?php
		echo $tabs->endPanel();
		
		/*
		
		echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_SCRIPT_MANAGEMENT"),"partnerPane3");

		//load scripts
		$database->setQuery( "SELECT id as value, publish_script_name as text FROM #__sdi_publish_script;");
		$script_list = $database->loadObjectList();
		$script_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_NEW_SCRIPT"));
		
		//load the default or specified script
		$script = new script( $database );
		$scriptId = JRequest::getVar('man_script_id', 0);
		//If it's an existing diffusor, else new.
		if($scriptId != 0)	
			$script->load($scriptId);
    */

?>		
    <!--
		<div class="info"><?php echo JText::_("EASYSDI_PUBLISH_SELECT_SCRIPT_MANAGEMENT_HINT"); ?></div>
		<br/>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_PUBLISH_SELECT_SCRIPT"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JHTML::_("select.genericlist",$script_list, 'man_script_id', 'size="1" class="inputbox" onChange="javascript:reloadTab(3,\'editGlobalSettings\');"', 'value', 'text', $scriptId); ?></td>
								<td><INPUT type="button" name="deleteScript" id="deleteScript" value="<?php echo JText::_("EASYSDI_PUBLISH_DELETE"); ?>" onClick="javascript:reloadTab(3,'deleteScript');"></td>
							</tr>
						</table>
					</fieldset>
								
				</td>
			</tr>
			<tr>
				<td>
				  <div id="scriptConfig">
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_PUBLISH_SCRIPT_CONFIGURATION"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="40" maxlength="100" name="publish_script_name" value="<?php echo $script->publish_script_name; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="40" maxlength="100" name="publish_script_description" value="<?php echo $script->publish_script_description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_CONDITION"); ?> : </td>	
								<td><input class="inputbox" type="text" size="40" maxlength="100" name="publish_script_conditions" value="<?php echo $script->publish_script_conditions; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_PUBLIC"); ?> : </td>
								<td><input class="inputbox" type="checkbox" name="publish_script_is_public" name="publish_script_is_public" <?php if($script->publish_script_is_public == 1)echo "CHECKED"; ?> /></td>
							</tr>
						</table>
					</filedset>
				   </div>
				</td>
			</tr>
		</table>
		-->
	<input type="hidden" name="task" value="editGlobalSettings"/>
	<input type="hidden" name="option" value="<?php echo $options; ?>" />
	<input type="hidden" name="config_id" value="<?php echo $config_Id; ?>" />
	<input type="hidden" id="tabIndex" name="tabIndex" value="<?php echo JRequest::getVar('tabIndex'); ?>" />
	<!--
	<input type="hidden" id="publish_script_file" name="publish_script_file" value="<?php echo $script->publish_script_file; ?>" />	
	<div id="fileUpload">
		<table>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_PUBLISH_FILE"); ?></legend>
						<table>
							<tr>
								<td><?php echo JText::_("EASYSDI_PUBLISH_FILE"); ?> : </td>
								<td><input class="inputbox" type="file" size="40" maxlength="100" name="Filedata" /></td>
								<td><input type="button" name="addScript" id="addScript" onClick="javascript:reloadTab(3,'saveConfig');" value="<?php echo JText::_("EASYSDI_PUBLISH_SAVE"); ?>"/></td>
								<input type="hidden" name="config_id" value="<?php echo $config_Id; ?>" />
							</tr>
							<tr>
								<td><div id="uploadMsg"/></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
	</div>
	-->
<?php
		//echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_PUBLISH_LAYER_MANAGEMENT"),"partnerPane4");

		//load scripts
//		$database->setQuery( "SELECT id as value, publish_script_name as text FROM #__sdi_publish_script;");
//		$script_list = $database->loadObjectList();
//		$script_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_NEW_SCRIPT"));
		
		//load the default or specified script
//		$script = new script( $database );
//		$scriptId = JRequest::getVar('man_script_id', 0);
		//If it's an existing diffusor, else new.
//		if($scriptId != 0)	
//			$script->load($scriptId);


?>		

		<table>
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandardClasses\');"',$use_pagination_layer); ?></td>
			</tr>
		</table>
		
		<table class="adminlist" width="100%">
								<thead>
									<tr>
										<th width="10%" align="left"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_PUBLISH_ID"), 'l.id', @$filter_order_Dir, @$filter_order ); ?></th>
										<th width="30%" align="left"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_PUBLISH_NAME"), 'l.name', @$filter_order_Dir, @$filter_order ); ?></th>
										<th width="30%" align="left"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_PUBLISH_USER"), 'j.name', @$filter_order_Dir, @$filter_order ); ?></th>
										<th width="30%" align="left"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_PUBLISH_TITLE"), 'l.title', @$filter_order_Dir, @$filter_order ); ?></th>
										<th width="30%" align="center"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_PUBLISH_FEATURESOURCE_NAME"), 'f.name', @$filter_order_Dir, @$filter_order ); ?></th>
										<th width="10%" align="center"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_CREATION_DATE"), 'l.creation_date', @$filter_order_Dir, @$filter_order ); ?></th>
										<th width="10%" align="center"><?php echo JHTML::_('grid.sort', JText::_("EASYSDI_UPDATE_DATE"), 'l.update_date', @$filter_order_Dir, @$filter_order ); ?></th>
										<!--<th width="10%" align="center">&nbsp;</th>-->
									</tr>
								</thead>
								<tbody>
<?php
								$k = 0;
								$i=0;
								foreach ($layers as $row)
								{
?>
									<tr class="<?php echo "row$k"; ?>">
										<td align="left"><?php echo $row->id; ?></td>
										<td align="left"><?php echo $row->layername; ?></td>
										<td align="center"><?php echo $row->joomlaname; ?></td>
										<td align="center"><?php echo $row->title; ?></td>
										<td align="center"><?php echo $row->fsname; ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->creation_date)); ?></td>
										<td align="center"><?php echo date("d-m-Y", strtotime($row->update_date)); ?></td>
									  <!-- <td align="center"><a href="index.php?option=com_easysdi_publish&task=viewLayer&id=<?php echo $row->id; ?>"><?php echo JText::_("EASYSDI_PUBLISH_VIEW"); ?></a></td> -->
									</tr>
<?php
									$k = 1 - $k;
									$i++;
								}
?>
								</tbody>
<?php			
		
								if ($use_pagination_layer)
								{?>
									<tfoot>
										<tr>	
											<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
										</tr>
									</tfoot>
								<?php
								}
?>
							</table>
		
		<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  <input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	</form>
	</div>
	
<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
?>
	
	
	<script language="javascript" type="text/javascript">
		
	window.addEvent('domready', function() {
	//Touille the help link
	try
	{
		document.getElementById('toolbar-help').getChildren()[0].setAttribute("onClick","window.open('http://forge.easysdi.org/wiki/publish')");
        }catch(e){}

	//Inits the page
	init();
	});
	
	
	function init(){
		
		var i=0;

		//Add event handler to reload the page at the good tab
		
		$('partnerPane0').addEvent('click', function() {
				$('tabIndex').value = 0;
		});
		$('partnerPane1').addEvent('click', function() {
				$('tabIndex').value = 1;
		});
		$('partnerPane2').addEvent('click', function() {
				$('tabIndex').value = 2;
		});
		
		//$('partnerPane3').addEvent('click', function() {
		//		$('tabIndex').value = 3;	
		//});
		
		$('partnerPane4').addEvent('click', function() {
				$('tabIndex').value = 4;
		});
		
		
		var uploadStatus=<?php echo JRequest::getVar('status', -1) ?>;
		var uploadSrc='<?php echo JRequest::getVar('src', '') ?>';
		var fileName='<?php echo JRequest::getVar('filename', '') ?>';
		
		objSelDif = document.getElementById('diffusor_id');
		objBtn = document.getElementById('deleteDiffusor');
		//If new server, deactive delete button
		if(objSelDif.options[objSelDif.selectedIndex].value == 'new')
			objBtn.disabled = true;
		
		objSelDif = document.getElementById('crs_id');
		objBtn = document.getElementById('deleteCrs');
		//If new crs, deactive delete button
		if(objSelDif.options[objSelDif.selectedIndex].value == 'new')
			objBtn.disabled = true;
		
		//objSelScr = document.getElementById('man_script_id');
		
		objBtn = document.getElementById('deleteScript');
		//If new script, deactive delete button
		/*
		if(objSelScr.options[objSelScr.selectedIndex].value == 0){
			objBtn.disabled = true;
		}
		*/
		
		/*
		//if new script and no file uploaded, hide config
		if(objSelScr.options[objSelScr.selectedIndex].value == 0
			&& uploadStatus == -1){
			$('scriptConfig').style.visibility = 'hidden';
			$('scriptConfig').style.display = 'none';
		}
		*/
		
		//disable partner value if no partner selected
		objParSel = document.getElementById('easysdi_user_id');
		objDivValues = document.getElementById('partnerSettings');
		if(objParSel.options[objParSel.selectedIndex].value == 0){
			objDivValues.style.visibility = 'hidden';
			objDivValues.style.display = 'none';
		}
		
		//Handle the uploaded file
		if(uploadStatus != -1){
			objDivMsg = $('uploadMsg');
			if(uploadStatus == 1){
				objDivMsg.style.backgroundColor='#FF0000';
				objDivMsg.innerHTML = "upload successfull: "+fileName;
			}else{
				objDivMsg.style.backgroundColor='#00FF00';
				objDivMsg.innerHTML = "upload failed.";
			}
		}
		
		//remove default listener on PageNav, I do it myself
		$('limit').onchange = "";

	/*	
		//Set the file name as hidden type for the admin form
		objHidType = $('publish_script_file');
		alert(base64_decode(uploadSrc));
		alert(uploadSrc);
		objHidType.value = base64_decode(uploadSrc);
	*/
	}
	
	$('limit').addEvent('change', function(e) {
			reloadTab(4,'editGlobalSettings');
	});
	
	function reloadTab(tabId, btn){
		$('tabIndex').value = tabId;
		//form = document.getElementById('adminForm');
		//elem = document.createElement("input");
		//elem.setAttribute("type", "hidden");
		//elem.setAttribute("name", "tabIndex");
		//elem.setAttribute("id", "hiddenName");
		//elem.setAttribute("value", tabId);
		//form.appendChild(elem);
		submitbutton(btn);
	}
	
	function base64_decode( data ) {
  	  // Decodes string using MIME base64 algorithm  
  	  // 
  	  // version: 905.3122
  	  // discuss at: http://phpjs.org/functions/base64_decode
  	  // +   original by: Tyler Akins (http://rumkin.com)
  	  // +   improved by: Thunder.m
  	  // +      input by: Aman Gupta
  	  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  	  // +   bugfixed by: Onno Marsman
  	  // +   bugfixed by: Pellentesque Malesuada
  	  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  	  // +      input by: Brett Zamir (http://brett-zamir.me)
  	  // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  	  // -    depends on: utf8_decode
  	  // *     example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
  	  // *     returns 1: 'Kevin van Zonneveld'
  	  // mozilla has this native
  	  // - but breaks in 2.0.0.12!
  	  //if (typeof this.window['btoa'] == 'function') {
  	  //    return btoa(data);
  	  //}
  	
  	  var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
  	  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0, ac = 0, dec = "", tmp_arr = [];
  	
  	  if (!data) {
  	      return data;
  	  }
  	
  	  data += '';

   	 do {  // unpack four hexets into three octets using index points in b64
      	  h1 = b64.indexOf(data.charAt(i++));
      	  h2 = b64.indexOf(data.charAt(i++));
      	  h3 = b64.indexOf(data.charAt(i++));
      	  h4 = b64.indexOf(data.charAt(i++));
      	
      	  bits = h1<<18 | h2<<12 | h3<<6 | h4;
      	
      	  o1 = bits>>16 & 0xff;
      	  o2 = bits>>8 & 0xff;
      	  o3 = bits & 0xff;
      	
      	  if (h3 == 64) {
      	      tmp_arr[ac++] = String.fromCharCode(o1);
      	  } else if (h4 == 64) {
      	      tmp_arr[ac++] = String.fromCharCode(o1, o2);
      	  } else {
      	      tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
      	  }
   	 } while (i < data.length);

   	 dec = tmp_arr.join('');
   	 dec = utf8_decode(dec);

   	 return dec;
	}
	
	function utf8_decode ( str_data ) {
	    // Converts a UTF-8 encoded string to ISO-8859-1  
	    // 
	    // version: 905.3122
	    // discuss at: http://phpjs.org/functions/utf8_decode
	    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
	    // +      input by: Aman Gupta
	    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // +   improved by: Norman "zEh" Fuchs
	    // +   bugfixed by: hitwork
	    // +   bugfixed by: Onno Marsman
	    // +      input by: Brett Zamir (http://brett-zamir.me)
	    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	    // *     example 1: utf8_decode('Kevin van Zonneveld');
	    // *     returns 1: 'Kevin van Zonneveld'
	    var tmp_arr = [], i = 0, ac = 0, c1 = 0, c2 = 0, c3 = 0;
	    
	    str_data += '';
	    
	    while ( i < str_data.length ) {
	        c1 = str_data.charCodeAt(i);
	        if (c1 < 128) {
	            tmp_arr[ac++] = String.fromCharCode(c1);
	            i++;
	        } else if ((c1 > 191) && (c1 < 224)) {
	            c2 = str_data.charCodeAt(i+1);
	            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
	            i += 2;
	        } else {
	            c2 = str_data.charCodeAt(i+1);
	            c3 = str_data.charCodeAt(i+2);
	            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
	            i += 3;
	        }
	    }
	
	    return tmp_arr.join('');
	}

	
	</script>

<?php
	}
	
	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0){
		  foreach($rows as $key => $row) {		  	
       		$rows[$key]->text = JText::_($rows[$key]->text);
  		}			    
		}
	}
}
?>