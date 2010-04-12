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

function com_uninstall(){
	
global  $mainframe;

$db =& JFactory::getDBO();
$query = "DELETE FROM #__components where `option`= 'com_easysdi_syncml'";

$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		
	}

//Drop Funambol DB
$aQuery = array();

$aQuery[] = "SET FOREIGN_KEY_CHECKS = 0";
$aQuery[] = "drop table if exists fnbl_client_mapping       ";
$aQuery[] = "drop table if exists fnbl_last_sync            ";
$aQuery[] = "drop table if exists fnbl_sync_source          ";
$aQuery[] = "drop table if exists fnbl_principal            ";
$aQuery[] = "drop table if exists fnbl_user_role            ";
$aQuery[] = "drop table if exists fnbl_user                 ";
$aQuery[] = "drop table if exists fnbl_device               ";
$aQuery[] = "drop table if exists fnbl_id                   ";
$aQuery[] = "drop table if exists fnbl_connector_source_type";
$aQuery[] = "drop table if exists fnbl_module_connector     ";
$aQuery[] = "drop table if exists fnbl_sync_source_type     ";
$aQuery[] = "drop table if exists fnbl_module               ";
$aQuery[] = "drop table if exists fnbl_connector            ";
$aQuery[] = "drop table if exists fnbl_role                 ";
$aQuery[] = "drop table if exists fnbl_ds_cttype_rx         ";
$aQuery[] = "drop table if exists fnbl_ds_cttype_tx         ";
$aQuery[] = "drop table if exists fnbl_ds_ctcap_prop_param  ";
$aQuery[] = "drop table if exists fnbl_ds_ctcap_prop        ";
$aQuery[] = "drop table if exists fnbl_ds_ctcap             ";
$aQuery[] = "drop table if exists fnbl_ds_filter_rx         ";
$aQuery[] = "drop table if exists fnbl_ds_filter_cap        ";
$aQuery[] = "drop table if exists fnbl_ds_mem               ";
$aQuery[] = "drop table if exists fnbl_device_ext           ";
$aQuery[] = "drop table if exists fnbl_device_datastore     ";
$aQuery[] = "drop table if exists fnbl_device_caps          ";
$aQuery[] = "drop table if exists fnbl_device_config        ";

//Execute Queries
foreach ($aQuery as $value) {
   	$db->setQuery($value);
   	if (!$db->query()) {
   		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
   	}
}
	
$mainframe->enqueueMessage("Congratulation EasySdi syncml manager is uninstalled. The database entries have also been deleted","INFO");

}


?>