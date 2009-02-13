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
 
header ("content-type: text/xml");
//Avoid php to output directly errors here
//echo '<!--';
//include_once("syncdbtable.funambolDB.easysdi.php");
include_once("../lib/adodb5/adodb-exceptions.inc.php");
include_once('../lib/adodb5/adodb.inc.php');
include_once('../lib/adodb5/toexport.inc.php');

//Retrieve connection infos
$operation = $_REQUEST['operation'];
$server = $_REQUEST['host'];
$provider = $_REQUEST['provider'];
$user = $_REQUEST['user'];
$pwd = $_REQUEST['pwd'];
$db = $_REQUEST['db'];
$tablename = $_REQUEST['tablename'];
$password = $_REQUEST['password'];
$email = $_REQUEST['email'];
$first_name = $_REQUEST['first_name'];
$last_name = $_REQUEST['last_name'];
$username = $_REQUEST['username'];
$description = $_REQUEST['description'];
$deviceId = $_REQUEST['deviceid'];
$principalId = $_REQUEST['principalid'];

//Try establishing a connection
$DB;
try 
{
	if($db == "funambol")
	{
		$componentConfigFilePath = '../config/com_easysdi.xml';
		$xmlConfig = simplexml_load_file($componentConfigFilePath);
		if ($xmlConfig === false){
			throw new Exception("CAN'T OPEN EASYSDI CONFIG FILE");
		}
		$funambolHome = $xmlConfig->funambol_home;
		$configfFile = $funambolHome."config\\com\\funambol\\server\\db\\db.xml";
		$xmlDBConf = simplexml_load_file($configfFile);
		if ($xmlDBConf === false){
			throw new Exception("CAN'T OPEN FUNAMBOL DB CONFIG FILE");
		}
		
		$temp = explode(":",$xmlDBConf->object[0]->void[0]->string[1]);
		$fnblProvider = $temp[1];
		
		$temp2 = explode("/", $xmlDBConf->object[0]->void[0]->string[1]);
		$fnblServer = trim($temp2[2]);
		$temp3 = explode("?", $temp2[3]);
		$fnblDb = trim($temp3[0]);
		$fnblUser = $xmlDBConf->object[0]->void[2]->string[1];
		$fnblPwd = $xmlDBConf->object[0]->void[3]->string[1];

		$DB = NewADOConnection($fnblProvider."://$fnblUser:$fnblPwd@$fnblServer/$fnblDb?persist");
	}
	else
	{	
		$DB = NewADOConnection($provider."://$user:$pwd@$server/$db?persist");
	}
} catch (exception $e){
	echo '-->';
	echo '<exception>';
		echo($e->getMessage());
	echo '</exception>';
	exit();
}
//echo '-->';
//print_r($tables);

switch ($operation) {
case "gettablenames":
    getTableNames($DB);
    break;
case "gettablerows":
    getTableRows($DB, $tablename);
    break;
case "getusers":
    getUsers($DB);
    break;
case "getdevices":
    getDevices($DB);
    break;
case "getprincipals":
    getPrincipals($DB);
    break;
case "insertuser":
    insertUser($DB, $password, $email, $first_name, $last_name, $username);
    break;
case "insertdevice":
    insertDevice($DB, $description, $deviceId);
    break;
case "insertprincipal":
    insertPrincipal($DB,  $username, $deviceId, $principalId);
    break;
case "updateuser":
    updateUser($DB, $password, $email, $first_name, $last_name, $username);
    break;
case "updatedevice":
    updateDevice($DB, $description, $deviceId);
    break;
case "updateprincipal":
    updatePrincipal($DB,  $username, $deviceId, $principalId);
    break;
case "deleteuser":
	deleteUser($DB, $username);
	break;
case "deletedevice":
	deleteDevice($DB, $deviceId);
	break;
case "deleteprincipal":
	deletePrincipal($DB, $principalId);
	break;
}

function getTableNames($DB)
{
	$tables = $DB->MetaTables('TABLES');
	echo '<tables>';
	foreach($tables as $k) {
		echo '<table>';
		echo $k;
		echo '</table>';
	}
	echo '</tables>';
}

function getTableRows($DB, $tablename)
{
	$columns = $DB->MetaColumnNames($tablename, false);
	echo '<columns>';
	foreach($columns as $k) {
		echo '<column>';
		echo $k;
		echo '</column>';
	}
	echo '</columns>';
}

function getUsers($DB)
{
	$sql = "select * from fnbl_user";
	$res = $DB->Execute($sql);
	$xml = rs2xml($res);
	echo $xml;
}

function getDevices($DB)
{
	$sql = "select id, description  from fnbl_device";
	$res = $DB->Execute($sql);
	$xml = rs2xml($res);
	echo $xml;
}

function getPrincipals($DB)
{
	$sql = "select * from fnbl_principal";
	$res = $DB->Execute($sql);
	$xml = rs2xml($res);
	echo $xml;
}

function updateUser($DB, $password, $email, $first_name, $last_name, $username)
{
	$sql = "update fnbl_user ";
	$sql .= "set password = \"$password\", email = \"$email\", first_name = \"$first_name\", last_name = \"$last_name\" ";
	$sql .= "where username = \"$username\"";
	try
	{
		$DB->Execute($sql);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
}

function updateDevice($DB, $description, $deviceId)
{
	$sql = "update fnbl_device ";
	$sql .= "set description = \"$description\" ";
	$sql .= "where id = \"$deviceId\"";
	try
	{
		$DB->Execute($sql);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
}

function updatePrincipal($DB, $username, $deviceId, $principalId)
{
	$sql = "update fnbl_principal ";
	$sql .= "set username = \"$username\", device = \"$deviceId\" ";
	$sql .= "where id = \"$principalId\"";
	try
	{
		$DB->Execute($sql);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
}

function insertUser($DB, $password, $email, $first_name, $last_name, $username)
{	
	//the user
	$sql1 = "insert into fnbl_user(username, password, email, first_name, last_name) ";
	$sql1 .= "values (\"$username\", \"$password\", \"$email\", \"$first_name\", \"$last_name\")";
	//his role
	$sql2 = "insert into fnbl_user_role(username, role) ";
	$sql2 .= "values (\"$username\", \"sync_user\")";
	try
	{
		$DB->Execute($sql1);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
	try
	{
		$DB->Execute($sql2);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
}

function insertDevice($DB, $description, $deviceId)
{
	$sql = "insert into fnbl_device(id, description, charset, server_password) ";
	$sql .= "values (\"$deviceId\", \"$description\", \"UTF-8\",\"fnbl\")";
	try
	{
		$DB->Execute($sql);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
}

function insertPrincipal($DB, $username, $deviceId, $principalId)
{
	$sql = "insert into fnbl_principal(id, username, device) ";
	$sql .= "values (\"$principalId\", \"$username\", \"$deviceId\")";
	try
	{
		$DB->Execute($sql);
	}
	catch (exception $e)
	{
		echo '<exception>';
		echo($e->getMessage());
		echo '</exception>';
	}
}

function deleteUser($DB, $username)
{
	$sql = "delete from fnbl_user ";
	$sql .= "where username = \"$username\"";
	$DB->Execute($sql);
}

function deleteDevice($DB, $deviceId)
{
	$sql = "delete from fnbl_device ";
	$sql .= "where id = \"$deviceId\"";
	$DB->Execute($sql);
}

function deletePrincipal($DB, $principalId)
{
	$sql = "delete from fnbl_principal ";
	$sql .= "where id = \"$principalId\"";
	$DB->Execute($sql);
}

?>