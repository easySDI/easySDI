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
include(JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'adodb5'.DS.'adodb-exceptions.inc.php');
include(JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'adodb5'.DS.'adodb.inc.php');

//This class enables access to the Funambol DB for DS Server
//like defined in Funambol\ds-server\install.properties
//It performs many tasks over the DB

class FUNAMBOLDB_syncdbtable
{    
	private $server;
	private $provider;
	private $user;
	private $pwd;
	private $db;
	
	private $connection;
	
	function FUNAMBOLDB_syncdbtable($funambolHome)
	{
		$configfFile = $funambolHome."config/com/funambol/server/db/db.xml";
		$xmlDBConf = simplexml_load_file($configfFile);
		if ($xmlDBConf === false){
			throw new Exception("CAN'T OPEN FUNAMBOL DB CONFIG FILE");
		}		
		$temp = explode(":",$xmlDBConf->object[0]->void[0]->string[1]);
		$this->provider = $temp[1];
		$temp2 = explode("/", $xmlDBConf->object[0]->void[0]->string[1]);
		$this->server = trim($temp2[2]);
		$temp3 = explode("?", $temp2[3]);
		$this->db = trim($temp3[0]);
		$this->user = $xmlDBConf->object[0]->void[2]->string[1];
		$this->pwd = $xmlDBConf->object[0]->void[3]->string[1];
	}
	
	function getConnection()
	{
		return $this->connection;
	}
	
	function connect()
	{
		$this->connection = NewADOConnection($this->provider."://$this->user:$this->pwd@$this->server/$this->db?persist");
	}
	
	//Adds a new Job config
	//Throws exception on error (ex: duplicate entry)
	function addSyncSource($srcUri, $name)
	{
		$sql = "insert into fnbl_sync_source (uri,config,name,sourcetype) ";
		$sql .= "values (\"$srcUri\", \"db/db/tss-db/$name.xml\", \"$name\", \"tss-db\")";
		$this->connection->Execute($sql);
	}
	
	//Updates an existing Job config (Only the name actually)
	//entering with Source Uri
	function renameSyncSource($sourceUri, $newName)
	{
		//the name
		$sql = "update fnbl_sync_source set name = \"$newName\" ";
		$sql .= "where uri=\"$sourceUri\"";
		$this->connection->Execute($sql);
		//the file ref
		$sql = "update fnbl_sync_source set config = \"db/db/tss-db/$newName.xml\" ";
		$sql .= "where uri=\"$sourceUri\"";
		$this->connection->Execute($sql);
	}
	
	//Deletes an existing Job config
	//entering with Source Uri
	function deleteSyncSource($sourceUri)
	{
		$sql = "delete from fnbl_sync_source ";
		$sql .= "where uri=\"$sourceUri\"";
		$this->connection->Execute($sql);
	}
	
	//Deletes an existing Job config
	//entering with Name
	function deleteSyncSourceForName($name)
	{
		$sql = "delete from fnbl_sync_source ";
		$sql .= "where name=\"$name\"";
		$this->connection->Execute($sql);
	}
	
	//Get config list
	function getConfigList()
	{
		$sql = "select name from fnbl_sync_source ";
		$res = $this->connection->Execute($sql);
		return $res;
	}
	
	function getNameForSyncSource($sourceUri)
	{
		$sql = "select name from fnbl_sync_source ";
		$sql .= "where uri = \"$sourceUri\"";
		$res = $this->connection->Execute($sql);
		return $res;
	}
}


?>