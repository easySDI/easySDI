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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');
jimport("joomla.html.pagination");

class MonitorController extends JController
{
	/**
	 * Method to display the view
	 *
	 * @access	public
	 */
	function display()
	{
		parent::display();
	}
	
	function create(){
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();		
		$usertype= strtolower($User->usertype);	  	
		$row =	json_decode(JRequest::getVar("rows"));	
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
		$saveSql = "INSERT INTO #__sdi_monitor_exports
		 (exportName, exportType, xsltUrl,exportDesc)
			VALUES ('".$row->exportName."','". $row->exportType."','".$row->xsltUrl."','".$row->exportDesc."')";
		try{
			
			$database->setQuery($saveSql);
			$rows = $database->loadObjectList();
			echo "{success:true}";
					
		
		}
		catch(Exception $e){
			echo "{success:false, error:".$e->getTraceAsString()."}";
		}
		die();
		
	
	}
	function read(){
		
		//echo "read";
	//	die();
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();		
		$usertype= strtolower($User->usertype);	  		
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
		
		$limit		=  JRequest::getVar('limit', 15);
		$limitstart	=  JRequest::getVar('start', 0);
		//get the total
		$query = "select count(*)  from #__sdi_monitor_exports";
		$database->setQuery($query);
		$total = $database->loadResult();
		$pagination = new JPagination($total,$limitstart,$limit);
		
		$selectsql = "select * from #__sdi_monitor_exports order by id desc";
		
		try{
			
			$database->setQuery( $selectsql, $pagination->limitstart, $pagination->limit);
			$rows = $database->loadObjectList();
			echo "{success:true,results:".$total.",rows:".json_encode($rows)."}";
					
		
		}
		catch(Exception $e){
			echo "{success:false, error:".$e->getTraceAsString()."}";
		}
		die();
	
	}
	function update(){
		//"id":"1","exportName":"name1","exportType":"csw","exportDesc":"desc111","xsltUrl":"url1"
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();
		$usertype= strtolower($User->usertype);	  	
		$row =	json_decode(JRequest::getVar("rows"));
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
		$updateSql = "UPDATE			 #__sdi_monitor_exports 
						SET				 exportName='".$row->exportName."' 
										, exportType='".$row->exportType."'
										, exportDesc='".$row->exportDesc."'													
										, xsltUrl='".$row->xsltUrl."'									
					    WHERE id = ".$row->id;
		$database->setQuery($updateSql);
		$result =$database->query();
		if ($database->getErrorNum()) {
				
			echo "{success:false, error:".$e->getTraceAsString()."}";
			die();
		
		}
		echo "{success:true}";
		die();
	
		
	
	}
	function delete(){
		
		$database=& JFactory::getDBO(); 
		$User =& JFactory::getUser();		
		$usertype= strtolower($User->usertype);	
		$id =	JRequest::getVar("rows");  		
	  	if(strpos($usertype, "admin")=== FALSE){
			echo "{success:false, error:user is not admin}";
			die();
		}
	
		$deletesql ="DELETE FROM #__sdi_monitor_exports where id =".$id;
		$database->setQuery($deletesql);
		$result =$database->query();
		if ($database->getErrorNum()) {
				
			echo "{success:false, error:".$e->getTraceAsString()."}";
			die();
		
		}
		echo "{success:true}";
		die();
		
		
	
	}
	
}