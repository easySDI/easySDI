<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class Easysdi_monitorController extends JControllerLegacy
{
    
/**
	 * Method to display the view
	 *
	 * @access	public
	 */
        public function display($cachable = false, $urlparams = false)
	{
		// Get the document object.
		$document	= JFactory::getDocument();

		// Set the default view name and format from the Request.
		$vName = $this->input->get('view', 'proxy');
		$this->input->set('view', $vName);

		parent::display($cachable);

		return $this;
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
		
		echo "read";
		die();
		$database=& JFactory::getDBO(); 
		$User = JFactory::getUser();		
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