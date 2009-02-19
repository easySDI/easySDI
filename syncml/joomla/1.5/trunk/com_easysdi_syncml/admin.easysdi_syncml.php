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
include(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'syncml.funambolDB.easysdi.php');

global $fnblDB;
global $JOB_CONFIG_PATH;

$componentConfigFilePath = dirname(__FILE__).DS.'config'.DS.'com_easysdi.xml';
//load config file
$xmlConfig = simplexml_load_file($componentConfigFilePath);
if ($xml === false){		
		$mainframe->enqueueMessage(JText::_(  'UNABLE TO LOAD THE EASY SDI CONFIGURATION FILE' ),'error');
}

$FUNAMBOL_HOME = $xmlConfig->funambol_home;

$fnblLogger = simplexml_load_file($FUNAMBOL_HOME."config".DS."com".DS."funambol".DS."server".DS."logging".DS."logger".DS."funambol.xml");

if ($xml === false){		
		$mainframe->enqueueMessage(JText::_(  'UNABLE TO LOAD FUNAMBOL LOGGER CONFIGURATION FILE' ),'error');
}
$JOB_CONFIG_PATH = $FUNAMBOL_HOME."config".DS."db".DS."db".DS."tss-db".DS;
global $fnblDB;
$fnblDB = new FUNAMBOLDB_syncdbtable($FUNAMBOL_HOME);
$task = JRequest::getVar('task');
global $mainframe;
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'syncml.toolbar.easysdi.html.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'core'.DS.'syncml.admin.easysdi.html.php');

switch($task){
	
	case 'componentConfig':
		TOOLBAR_easysdi::editComponentConfig();
		easysdi_HTML::configComponent($xmlConfig, $fnblLogger);
		break;
	
	// Save module configuration
	case 'saveComponentConfig':
		saveComponentConfig($xmlConfig,$componentConfigFilePath, $fnblLogger);
		easysdi_HTML::ctrlPanel();
		break;
	
	//User clicked on edit config Button
	case 'editConfig':
		TOOLBAR_easysdi::editConfig();
		$jobFileName = JRequest::getVar('configId');
		if ($jobFileName == ""){		
			$mainframe->enqueueMessage(JText::_(  'SELECT A JOB FILE FIRST: '),'error');
			TOOLBAR_easysdi::configList();	
			easysdi_HTML::showConfigList($fnblDB);
			break;
		}
		$xmljobFile = simplexml_load_file($JOB_CONFIG_PATH.$jobFileName.".xml");
		if ($xmljobFile === false){		
			$mainframe->enqueueMessage(JText::_(  'CANNOT LOAD JOB FILE: '.$JOB_CONFIG_PATH.$jobFileName),'error');
		}
		
		easysdi_HTML::editConfig($xmljobFile, false);
		break;
		
	case 'addConfig':

		//Create in memory xml file and stuff some default values
		$templateConfigFilePath = dirname(__FILE__).DS.'templates'.DS.'tablesyncsourcetemplate.xml';
		$template = simplexml_load_file($templateConfigFilePath);
		if ($template === false){		
			$mainframe->enqueueMessage(JText::_(  'UNABLE TO LOAD THE SYNC SOURCE TEMPLATE FILE' ),'error');
		}
		//Here apply some default values...
		$res = $template->xpath('//void[@property="type"]');
		$res[0]->string = 'text/plain';
		
		//jndiDataSource
		$res = $template->xpath('//void[@property="jndiName"]');
		$res[0]->string = 'jdbc/Sync4j';
		
		TOOLBAR_easysdi::editConfig();
		easysdi_HTML::editConfig($template,true);

		break;
	case 'deleteConfig':
		$configId = JRequest::getVar('configId');
		if ($configId == ''){		
			$mainframe->enqueueMessage(JText::_(  'PLEASE SELECT A JOB TO DELETE BEFORE' ),'error');
		}
		deleteConfig($configId);
		TOOLBAR_easysdi::configList();
		easysdi_HTML::showConfigList($fnblDB);
		break;
		
	/* Not good id because we cannot change the source URI then
	case 'copyConfig':
		$configId = JRequest::getVar('configId');
		if ($configId == ''){		
			$mainframe->enqueueMessage(JText::_(  'PLEASE SELECT A JOB TO DELETE BEFORE' ),'error');
		}
		copyConfig($JOB_CONFIG_PATH, $configId);
		TOOLBAR_easysdi::configList();
		easysdi_HTML::showConfigList($jobList);
		break;
	*/
	
	//is save config button clicked for current Job
	//Job can be new or existing
	case 'saveConfig':
		$newSourceUri = JRequest::getVar('newSourceUri');
		$jobFileName = JRequest::getVar('newConfigName');
		
		//check if the job name exists and throw exception
		$fnblDB->connect();
		$configList = $fnblDB->getConfigList();
		$found = false;
		while (!$configList->EOF) 
		{
			if($configList->fields[0] == $jobFileName)
			{
				$found = true;
				break;
			}
			$configList->MoveNext();
		}
		if($found == true)
		{
			$mainframe->enqueueMessage(JText::_(  'JOB NAME ALREADY EXISTS' ),'error');
			TOOLBAR_easysdi::configList();
			easysdi_HTML::showConfigList($fnblDB);
			break;
		}
		
		//create a new file for serialization because it doesn't exist yet.
		if($newSourceUri != '')
		{
			try
			{ 
				//open the template
				$templateConfigFilePath = dirname(__FILE__).DS.'templates'.DS.'tablesyncsourcetemplate.xml';
				$template = simplexml_load_file($templateConfigFilePath);
				if ($template === false){		
					$mainframe->enqueueMessage(JText::_(  'UNABLE TO LOAD THE SYNC SOURCE TEMPLATE FILE' ),'error');
				}
				//create a new file for the job, stuff the template inside
				$ourFileHandle = fopen($JOB_CONFIG_PATH.$jobFileName.".xml", 'w');
				$template->asXML($JOB_CONFIG_PATH.$jobFileName.".xml");
				fclose($ourFileHandle);	
			}
			catch (Exception $e) { 
				$mainframe->enqueueMessage(JText::_(  'CANNOT CREATE NEW FUNAMBOL JOB FILE' ),'error');
			}
		}
		//the job file name has perhaps been changed, update It
		//rename in DB
		$sourceUri = JRequest::getVar('sourceUri');
		$fnblDB->connect();
		$res = $fnblDB->getNameForSyncSource($sourceUri);
		$oldName = $res->fields[0];
		if($oldName != ""){
			$fnblDB->renameSyncSource($sourceUri,$jobFileName);
			//Rename file
			rename($JOB_CONFIG_PATH.$oldName.".xml", $JOB_CONFIG_PATH.$jobFileName.".xml");
		}
		
	    $jobFileName = JRequest::getVar('newConfigName').'.xml';
				
		$xmljobFile = simplexml_load_file($JOB_CONFIG_PATH.$jobFileName);
		if ($xmljobFile === false){		
			$mainframe->enqueueMessage(JText::_(  'CANNOT LOAD JOB FILE: '.$JOB_CONFIG_PATH.$jobFileName),'error');
		}
		saveConfig($xmljobFile,$JOB_CONFIG_PATH);
		TOOLBAR_easysdi::configList();
		easysdi_HTML::showConfigList($fnblDB);
		break;
	case 'cancel':	
	//start point
	case 'showConfigList':
		TOOLBAR_easysdi::configList();		
		easysdi_HTML::showConfigList($fnblDB);
		break;
		
	case 'cancelConfigList':		
	case 'cancelComponentConfig':
	default:
		easysdi_HTML::ctrlPanel();		
		break;
}

function deleteConfig($configId){
	//Unregister Job from DB
	//Update the funambol DB
	global $JOB_CONFIG_PATH;
	global $fnblDB;
	try 
	{
		$fnblDB->connect();
		$fnblDB->deleteSyncSourceForName($configId);
	} catch (exception $e){
		$mainframe->enqueueMessage(JText::_(  'UNABLE TO DELETE JOB FROM FUNAMBOL DB' ),'error');
		echo $e->getMessage();
		return;
	}	
	try
	{ 
		unlink ($JOB_CONFIG_PATH.$configId.".xml");
	}
	catch (Exception $e) { 
		$mainframe->enqueueMessage(JText::_(  'CANNOT DELETE JOB ID FILE' ),'error');
	} 
}

/*
function copyConfig($path, $configId){
	$jobFile =  substr($configId, 0, strlen($configId)-4);     
	$jobFile = $jobFile.'_copy_'.time();
	$xmljobFile = simplexml_load_file($path.$configId);
	if ($xmljobFile === false){		
		$mainframe->enqueueMessage(JText::_(  'CANNOT LOAD JOB FILE: '.$JOB_CONFIG_PATH.$jobFileName),'error');
	}
	//job name
	$res = $xmljobFile->xpath('//void[@property="name"]');
	$res[0]->string = $jobFile;
				
	//Source URI
	$res = $xmljobFile->xpath('//void[@property="sourceURI"]');
	$res[0]->string = $jobFile;
	
	$xmljobFile->asXML($path.$jobFile.'.xml');
}
*/

function saveComponentConfig($xmlConfig,$componentConfigFilePath, $fnblLogger){
	$funambolHome = JRequest::getVar("funambolHome");
	$xmlConfig->funambol_home = $funambolHome;
	$xmlConfig->asXML($componentConfigFilePath);
	
	//update logging
	$loglevel =  JRequest::getVar("fnblLoggingLevel");
	$logApppender = JRequest::getVar("fnblLoggingAppenders");
	$temp = $fnblLogger->xpath('//void[@property="level"]');
	$temp[0]->string = $loglevel;
	$temp = $fnblLogger->xpath('//void[@property="appenders"]');
	$temp[0]->object[0]->void[0]->string = $logApppender;
	$fnblLogger->asXML($funambolHome."config".DS."com".DS."funambol".DS."server".DS."logging".DS."logger".DS."funambol.xml");
}

function saveConfig($xml,$configFilePath){
	global $fnblDB;
	global $mainframe;
	$configId = JRequest::getVar("newConfigName");
	$new;
	if(JRequest::getVar("newSourceUri")!=''){
		$new=true;
	}else{
		$new = false;
	}
	
	//Clear all datas and rewrite them
	//get new values from server
	$templateConfigFilePath = dirname(__FILE__).DS.'templates'.DS.'tablesyncsourcetemplate.xml';
	$xml = simplexml_load_file($templateConfigFilePath);
	if ($xml === false){		
		$mainframe->enqueueMessage(JText::_(  'UNABLE TO LOAD THE SYNC SOURCE TEMPLATE FILE' ),'error');
	}
	
	//Update the funambol DB
	try 
	{
		$fnblDB->connect();
		$jobId = JRequest::getVar("newConfigName");
		if ($new){
			$fnblDB->addSyncSource(JRequest::getVar("newSourceUri"), $jobId);
		//If it's an existing config, update only name in funambol db
		}
		else
		{
			$res = $xml->xpath('//void[@property="sourceURI"]');
	        $fnblDB->renameSyncSource($res[0]->string,$jobId);
		}
	 } catch (exception $e){
		$mainframe->enqueueMessage(JText::_(  'UNABLE TO UPDATE FUNAMBOL DB' ),'error');
		echo $e->getMessage();
		return;
	}		
	
	//fields mapping
	$fieldMappings = $xml->xpath('//void[@property="fieldMapping"]');
	$i = 0;
	while(JRequest::getVar("MappedField_Server".$i) != ''){
		$newNode = $fieldMappings[0]->object->addChild('void');
		$newNode->addAttribute('method','put');
		$newNode->addChild('string', JRequest::getVar("MappedField_Server".$i));
		$newNode->addChild('string',JRequest::getVar("MappedField_Client".$i));
		$i++;
	}
	$count = $i;
	
	//binary fields
	$fieldBinary = $xml->xpath('//void[@property="binaryFields"]');
	$i = 0;
	while($i < $count){
		if(JRequest::getVar("MappedField_Binary".$i)==''){
			$i++;
			continue;
		}
		$newNode = $fieldBinary[0]->object->addChild('void');
		$newNode->addAttribute('method','add');
		$newNode->addChild('string',JRequest::getVar("MappedField_Binary".$i));
		$i++;
	}
	
	//content type
	$contentType = JRequest::getVar("encodingType");
	$res = $xml->xpath('//void[@property="type"]');
	$res[0]->string = $contentType;
	
	//jndiDataSource
	$jndiDataSource = JRequest::getVar("jndiDataSource");
	$res = $xml->xpath('//void[@property="jndiName"]');
	$res[0]->string = $jndiDataSource;
	
	//keyfield
	$keyField = JRequest::getVar("tiKey");
	$res = $xml->xpath('//void[@property="keyField"]');
	$res[0]->string = $keyField;
	
	//job name
	$jobId = JRequest::getVar("newConfigName");
	$res = $xml->xpath('//void[@property="name"]');
	$res[0]->string = $jobId;
		
	//Principal
	$principal = JRequest::getVar("tiPrincipal");
	$res = $xml->xpath('//void[@property="updatePrincipalField"]');
	$res[0]->string = $principal;
	
	//Source URI
	if($new){
		$sourceUri = JRequest::getVar("newSourceUri");
		$res = $xml->xpath('//void[@property="sourceURI"]');
		$res[0]->string = $sourceUri;
	}else{
		$sourceUri = JRequest::getVar("sourceUri");
		$res = $xml->xpath('//void[@property="sourceURI"]');
		$res[0]->string = $sourceUri;
	}
	
	//db table name
	$tableName = JRequest::getVar("tiTableName");
	$res = $xml->xpath('//void[@property="tableName"]');
	$res[0]->string = $tableName;
	
	//update DateField
	$updateDateField = JRequest::getVar("tiLastUpdateTime");
	$res = $xml->xpath('//void[@property="updateDateField"]');
	$res[0]->string = $updateDateField;
	
	//update DateType
	$updateDateType = JRequest::getVar("tiLastUpdateType");
	$res = $xml->xpath('//void[@property="updateTypeField"]');
	$res[0]->string = $updateDateType;
	
	//Serialize
	$xml->asXML($configFilePath.$configId.'.xml');
}
?>