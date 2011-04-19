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

?>


<?php if((JRequest::getVar("task")!="provideXMLDataForXQueryReport") && 
(JRequest::getVar("task") !="processXQueryReport") &&
(JRequest::getVar("task")!="adminTestXQueryReport"))
{?>


<script type="text/javascript">
	var newReportUrl ="<?php echo "index.php?option=com_easysdi_catalog&task=newXQueryReport&cid=0"?>";
	var editReportUrl ="<?php echo "index.php?option=com_easysdi_catalog&task=editXQueryReport&cid="?>";
	var saveReportUrl ="<?php echo "index.php?option=com_easysdi_catalog&task=saveXQueryReport&cid="?>";
	var deleteReportUrl ="<?php echo "index.php?option=com_easysdi_catalog&task=deleteXQueryReport&cid="?>";
	var cancelReportUrl ="<?php echo "index.php?option=com_easysdi_catalog&task=listQueryReports&cid=0&resetpagination=1"?>";
	var assignReportUrl = "<?php echo "index.php?option=com_easysdi_catalog&task=assignXQueryReport&cid="?>";
	var saveUserReportAccessUrl = "<?php echo "index.php?option=com_easysdi_catalog&task=saveXQueryUserReportAccess&cid="?>";
	var processReportUrl = "<?php echo "index.php?option=com_easysdi_catalog&task=processXQueryReport&cid="?>";
	var adminTestXQueryReportUrl =  "<?php echo "../index.php?option=com_easysdi_catalog&task=processXQueryReport&cid="?>";
	
	var selectOnlyOneMsg = "<?php echo JText::_("CATALOG_XQUERY_SELECTONLYONE") ?>";
	var reportToPreviewId = 0;
	var reportToUpdateId = 0;
	var reportToDeleteId = 0;
	var reportToAssignId = <?php echo JRequest::getVar('cid', 0)?>;
	var tmpReportIds = new Array();
	var tmpUserAccountsToAdd = new Array();
	var tmpUserAccountsToRemove = new Array();
	var currentOrgFilter = "";
		
	function submitbutton(action,id) 
	{
		debugger;
		
		actionUrl ="";
		if (action == "newXQueryReport" )
			actionUrl = newReportUrl;
		
		else if (action == "editXQueryReport" ){
			if(tmpReportIds.length == 1)
				actionUrl = editReportUrl + reportToPreviewId;
			else
				alert(selectOnlyOneMsg);
		}
		
		else if (action == "saveXQueryReport" ){
			actionUrl = saveReportUrl + reportToUpdateId;
		}
		
		else if (action == "deleteXQueryReport" ){
			actionUrl = deleteReportUrl + reportToDeleteId;
		}
		
		else if (action == "cancelXQueryReport" ){
			actionUrl = cancelReportUrl;
		}		
		else if (action == "assignXQueryReport" ){
			if(tmpReportIds.length == 1)
				actionUrl = assignReportUrl +  reportToAssignId + currentOrgFilter + "&resetpagination=1";
			else
				 if (reportToAssignId != 0)
					 actionUrl = assignReportUrl +  reportToAssignId + currentOrgFilter +"&resetpagination=1" ;
				 else
					alert(selectOnlyOneMsg);
		}
		else if (action == "saveXQueryUserReportAccess" ){
			
				actionUrl = saveUserReportAccessUrl +  reportToAssignId + "&add="+tmpUserAccountsToAdd.join(";") + "&remove="+tmpUserAccountsToRemove.join(";") ;
					
		}
		else if (action == "processXQueryReport" ){
			actionUrl = processReportUrl + id ;
		}
		else if (action =="adminTestXQueryReport"){
			actionUrl = adminTestXQueryReportUrl + id ;
		}
		else{ actionUrl ="";}
		


		if(actionUrl !=""){
			document.adminForm.action= actionUrl;
			document.adminForm.submit();
		}
		
	}

	function filterByOrg(){
		node = document.getElementById("orgfilter")
		index= node.selectedIndex;
		currentOrgFilter = "&root="+node[index].value;

		submitbutton("assignXQueryReport");
	}
	
	function setUserReportAccess(obj, id){
		
		indexofId = null;

		if(obj.checked){

			for(i=0; i< tmpUserAccountsToRemove.length ;i++){
				if(tmpUserAccountsToRemove[i]==id){
					indexofId= i;
					break
				}
			}

			if(null!=indexofId)
				tmpUserAccountsToRemove.splice(i,1); // start at index i and remove 1 element
			else{
				tmpUserAccountsToAdd.push(id);
			}
			
			

			
		}
		else{
			for(i=0; i< tmpUserAccountsToAdd.length ;i++){
				if(tmpUserAccountsToAdd[i]==id){
					indexofId= i;
					break
				}
			}

			if(null!=indexofId)
				tmpUserAccountsToAdd.splice(i,1); // start at index i and remove 1 element
			else{
				tmpUserAccountsToRemove.push(id);
			}

		}
		
	
		
	}
	function setReportIdToPreview(obj, id){
		reportToPreviewId= id;
		reportToAssignId =  id;
		indexofId = null;

		if(obj.checked)
			tmpReportIds.push(id);
		else{
			for(i=0; i< tmpReportIds.length ;i++){
				if(tmpReportIds[i]==id){
					indexofId= i;
					break
				}
			}

			if(null!=indexofId)
				tmpReportIds.splice(i,1); // start at index i and remove 1 element

		}
		
		reportToDeleteId = tmpReportIds.join(";");
		
	}

	function submitform(pressbutton){

		 if(document.adminForm.baseURI){
			 currentbaseuri =document.adminForm.baseURI
			 document.adminForm.action =currentbaseuri.replace("&resetpagination=1","");
		 }
		 if (pressbutton) {
	 		document.adminForm.task.value=pressbutton;
		 }
		 if (typeof document.adminForm.onsubmit == "function") {
			 document.adminForm.onsubmit();
		 }
		 document.adminForm.submit();
	} 
</script>
<?php }//end if?>
<?php

class ADMIN_xQuery{

	function listQueryReports(){
		
		$xqueryHomeUrl ="index.php?option=com_easysdi_catalog&task=listQueryReports&cid=0";
		
		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		
		
		$resetpagination = JRequest::getVar('resetpagination', 0);
		if($resetpagination ==1){			
			JRequest::setVar('limit', 10);
			JRequest::setVar('limitstart', 0);
			JRequest::setVar('resetpagination', 0);
		}
		
		$limit		=  JRequest::getVar('limit', 10);
		$limitstart	=  JRequest::getVar('limitstart', 0);
		//get the total
		$query = "select count(*)  from #__sdi_xqueryreport";
		$database->setQuery($query);
		$total = $database->loadResult();
		$pagination = new JPagination($total,$limitstart,$limit);
		
		$selectsql = "select * from #__sdi_xqueryreport";
		try{
			
			$database->setQuery( $selectsql, $pagination->limitstart, $pagination->limit);
			$rows = $database->loadObjectList();
			if ($database->getErrorNum()) {
				
				$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");	
		
			}
			else{
				
				HTML_xquery::list_XQueryReports($rows, $pagination);
					
			}
		}
		catch(Exception $e){
			$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
		}
		
		 
		
	}
	function newXQueryReport(){

		HTML_xquery::newXQueryReport();
	}
	function editXQueryReport(){
		
		$cid = JRequest::getVar('cid');
		
		$xqueryHomeUrl ="index.php?option=com_easysdi_catalog&task=listQueryReports&cid=0";
		
		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		
		$selectsql = "select * from #__sdi_xqueryreport where id =".$cid;
		try{
			$database->setQuery($selectsql);
			$row = $database->loadObjectList();
			if ($database->getErrorNum()) {
				
				$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");	
		
			}
			else{
				
			HTML_xquery::editXQueryReport($row);
					
			}
		}
		catch(Exception $e){
			$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
		}
		

		
	}
	function saveXQueryReport(){
		
		$cid = JRequest::getVar('cid');
		$xfileid = 0;
		
		
		$xqueryHomeUrl ="index.php?option=com_easysdi_catalog&task=listQueryReports&cid=0";
		
		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		$adminUser =& JFactory::getUser();		

		if($cid!=0){
			
			$database->setQuery("select xfileid from #__sdi_xqueryreport where id=".$cid);
			$xfileid =$database->loadResult();
			
		}
		$xsltUrl =JRequest::getVar( 'xsltUrl');
		$xQueryReportName =JRequest::getVar( 'xQueryReportName' );
		$metadataIdSql=JRequest::getVar( 'metadataIdSql');
		$ogcfilter=JRequest::getVar( 'ogcfilter' );
		$reportcode=JRequest::getVar( 'reportcode' );
		$desc=JRequest::getVar( 'description' );
	    $applicationType = JRequest::getVar( 'applicationType');
		
		$metadataIdSql = strtolower($metadataIdSql);
		$updateindex = strpos($metadataIdSql, "update");
		$deleteindex = strpos($metadataIdSql, "delete");
		$insertindex = strpos($metadataIdSql, "insert");
		
		$fromindex = strpos($metadataIdSql, "from");
		$commaInSelect =  strpos(substr($metadataIdSql,0,$fromindex), ",");
		
		$newReport = false ;
		if($updateindex || $updateindex || $insertindex){			
			$mainframe->redirect( $xqueryHomeUrl, JText::_("CATALOG_XQUERY_SQLDISALLOWED"),"ERROR");
			
		}
		
		if($commaInSelect){			
			$mainframe->redirect( $xqueryHomeUrl, JText::_("CATALOG_XQUERY_SELECTINCORRECT"),"ERROR");
			
		}
		$metadataIdSql= $metadataIdSql;
		
		
		$uid = ADMIN_xQuery::saveXQueryCode();
		
		
		if (!ADMIN_xQuery::testUid($uid)) {// then its a stacktrace.
			$mainframe->redirect($xqueryHomeUrl, $uid,"ERROR");		
		}
			
		
		if( $cid==0){
			$saveSql = "INSERT INTO #__sdi_xqueryreport (sqlfilter, ogcfilter, xslttemplateurl,xqueryname, xfileid, reportcode, description, applicationType)
					    VALUES ('".$metadataIdSql."','". $ogcfilter."','". $xsltUrl."','".$xQueryReportName."','".$uid."','".$reportcode."','".$desc."','".$applicationType."')";
			$newReport = true;
		}else{
			$saveSql = "UPDATE			 #__sdi_xqueryreport 
						SET				 sqlfilter='".$metadataIdSql."' 
										, ogcfilter='". $ogcfilter."'
										, xslttemplateurl='". $xsltUrl."'													
										, xqueryname='".$xQueryReportName."'
										, xfileid='".$uid."'
										, reportcode='".$reportcode."'
										, description='".$desc."'
										, applicationType='".$applicationType."'
					    WHERE id = ".$cid;
			$newReport = false;
			
			//delete the other one
		}
		
		
		$lastInsertId = null;
		try{
			$database->setQuery($saveSql);
			$result =$database->query();
			if($newReport)
				$lastInsertId = $database->insertid(); 
			if (!$result)
			{	
				$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");
				
			}
			else{
				if($newReport){
					$saveSql = "INSERT INTO #__sdi_xqueryreportassignation (report_id, user_id)	VALUES (".$lastInsertId.",".$adminUser->id.")";		
					$database->setQuery($saveSql);
					$result =$database->query();
					if(!result){
						$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");		
					}									
					
				}else{
					//deleteOld xfileid
					if($xfileid!=''){
						$fileDeleted = ADMIN_xQuery::deleteXfileWithId($xfileid);
						if (!ADMIN_xQuery::testUid($fileDeleted)) {// then its a stacktrace.
							$mainframe->redirect($xqueryHomeUrl, $fileDeleted,"ERROR");
						}	
					}
				}
				
				
				$mainframe->redirect($xqueryHomeUrl, JText::_("CATALOG_XQUERY_SAVESUCCESS"));
				
			}
		}
		catch(Exception $e)
		{
			$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
		}
		
		


				
	}
	function deleteXQueryReport(){

		$cid = explode(";",JRequest::getVar('cid'));
		
		$xqueryHomeUrl ="index.php?option=com_easysdi_catalog&task=listQueryReports&cid=0";
		
		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		
		$deletesql="";
		if( $cid[0]==0 && count(cid)==1){
			$mainframe->redirect($xqueryHomeUrl, JText::_("CATALOG_XQUERY_NOREPORTSELECTED"), "ERROR");
		}else{
				$deletesql ="DELETE FROM #__sdi_xqueryreport where id in(".join(",",$cid).")";

		}		
		
		
		try{
			if($deletesql != ""){
				$database->setQuery($deletesql);
				if (!$database->query())
				{
					$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");
						
				}
				else{
					$mainframe->redirect($xqueryHomeUrl, JText::_("CATALOG_XQUERY_DELETESUCCESS"));
						
				}
			}
		}
		catch(Exception $e)
		{
			$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
		}
		

	}
	function assignXQueryReport(){

		// reportid
		global $mainframe;	
			
		$db =& JFactory::getDBO(); 
		
		
		$reportId = JRequest::getVar('cid', 0);
		$resetpagination = JRequest::getVar('resetpagination', 0);
		if($resetpagination ==1){			
			JRequest::setVar('limit', 10);
			JRequest::setVar('limitstart', 0);
			JRequest::setVar('resetpagination', 0);
		}
		
		$limit		=  JRequest::getVar('limit', 10);
		$limitstart	=  JRequest::getVar('limitstart', 0);
		
		if($reportId ==0){			
			$mainframe->redirect( $xqueryHomeUrl, JText::_("CATALOG_XQUERY_REPORT_ID_MISSING"),"ERROR");			
		}
		
	// D�compte des enregistrements totaux
		$root_acc_id= JRequest::getVar('root');
		
		if ($root_acc_id == '') {
			$query = "SELECT COUNT(*) from #__sdi_account WHERE #__sdi_account.root_id IS NOT NULL";
		} else {
			$query = "SELECT COUNT(*) from #__sdi_account WHERE #__sdi_account.root_id =".$root_acc_id;
		}
		

		$db->setQuery( $query );
		$total = $db->loadResult();
		$pagination = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		//print_r($type); echo "<br>";
		if ($root_acc_id == '') { //select all users
			$query = "SELECT #__users.name as account_name,#__users.email as account_email, #__users.usertype as account_usertype,   #__sdi_account.user_id as id FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NOT NULL order by account_name ASC";
					} else {
			$query = "SELECT #__users.name as account_name,#__users.email as account_email, #__users.usertype as account_usertype,   #__sdi_account.user_id as id FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$root_acc_id." AND #__sdi_account.root_id IS NOT NULL order by account_name ASC";
		}	
				
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		

		$useraccounts = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		//selecting company names
		$query = "SELECT id, name  FROM #__sdi_account WHERE  #__sdi_account.root_id IS  NULL order by name";
		$db->setQuery( $query);		

		$orgaccounts = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		
		$currentUsers =array();
		// setting them a
		if ($useraccounts){			
			foreach ($useraccounts as $user)
				$currentUsers[]= $user->id;
		}	
		// find those who are already assigned
		$assignedUsers = array();
		$userList = trim( join(",",$currentUsers));
		if($userList != ''){
			$query ="select user_id from #__sdi_xqueryreportassignation where report_id =".$reportId." AND user_id in (".$userList.")";
			$db->setQuery( $query);		
	
			$result = $db->loadObjectList();
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			}
			
			foreach ($result as $assignedUser){
				$assignedUsers[$assignedUser->user_id] =true;
			}
		
		}
		HTML_xquery::assignXQueryReport($orgaccounts, $useraccounts, $pagination, $assignedUsers  );
		
		
	}
	
	function saveXQueryUserReportAccess(){
		
		$cid = JRequest::getVar('cid');
		
		$xqueryHomeUrl ="index.php?option=com_easysdi_catalog&task=listQueryReports&cid=0";
		
		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		
		$reportId =JRequest::getVar('cid', 0);
		$usersToAdd =JRequest::getVar( 'add', '' );
		$usersToRemove=JRequest::getVar( 'remove','');

		if($reportId ==0){			
			$mainframe->redirect( $xqueryHomeUrl, JText::_("CATALOG_XQUERY_REPORT_ID_MISSING"),"ERROR");			
		}		
		
		
		$insertArr = array();
		$addUserArr = explode(";",$usersToAdd);
		foreach( $addUserArr as $user){			
			$insertArr[] = "(".$reportId.",".$user.")";
		}
				
		$saveSql = "INSERT INTO #__sdi_xqueryreportassignation (report_id, user_id)	VALUES ".join(",", $insertArr);		
		
		if(trim($usersToAdd) !=''){
			try{
				$database->setQuery($saveSql);
				if (!$database->query())
				{	
					$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");
					
				}			
			}
			catch(Exception $e)
			{
				$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
				
			}
		}
		
		
		$removeUserArr = explode(";",$usersToRemove);
		
		$deleteSql = "DELETE FROM  #__sdi_xqueryreportassignation  WHERE report_id = ".$reportId."  AND user_id IN (". join(",",$removeUserArr).") ";
		
		if(trim($usersToRemove) !=''){
			try{
				$database->setQuery($deleteSql);
				if (!$database->query())
				{	
					$mainframe->redirect($xqueryHomeUrl, $database->getErrorMsg(),"ERROR");
					
				}			
			}
			catch(Exception $e)
			{
				$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
				
			}
		}
		
		// if we reach here it all went well.
		if((trim($usersToRemove) !='') || (trim($usersToAdd) !=''))
		$mainframe->redirect($xqueryHomeUrl,  JText::_("CATALOG_XQUERY_REPORT_ASSIGN_SUCCESS"));
		
	}
	
	
	function listMyReports(){
		
	    $homeUrl ="index.php";
		
		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$resetpagination = JRequest::getVar('resetpagination', 0);
		if($resetpagination ==1){			
			JRequest::setVar('limit', 10);
			JRequest::setVar('limitstart', 0);
			JRequest::setVar('resetpagination', 0);
		}
		
		$limit		=  JRequest::getVar('limit', 10);
		$limitstart	=  JRequest::getVar('limitstart', 0);
		//get the total
		$query = "select count(*)  from #__sdi_xqueryreportassignation where user_id =".$user->id;
		$database->setQuery($query);
		$total = $database->loadResult();
		$pagination = new JPagination($total,$limitstart,$limit);
		
		$selectsql = "select #__sdi_xqueryreport.* from #__sdi_xqueryreportassignation , #__sdi_xqueryreport  
					  where #__sdi_xqueryreport.id=#__sdi_xqueryreportassignation.report_id AND #__sdi_xqueryreportassignation.user_id =".$user->id;
		
		try{
			
			$database->setQuery( $selectsql, $pagination->limitstart, $pagination->limit);
			$rows = $database->loadObjectList();
			if ($database->getErrorNum()) {
				
				$mainframe->redirect($homeUrl, $database->getErrorMsg(),"ERROR");	
		
			}
			else{
				
				HTML_xquery::listMyReports($rows, $pagination);
					
			}
		}
		catch(Exception $e){
			$mainframe->redirect($homeUrl, $e->getTraceAsString());
		}
	}
	
  function saveXQueryCode()
  {
  	$ch = curl_init();
  	
  	$mxqueryroot = config_easysdi::getValue("catalog_mxqueryurl");
  	$mxqueryManage= $mxqueryroot."/manage";
  	curl_setopt($ch, CURLOPT_URL, $mxqueryManage);
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_POST, true);

  	$data =   'operationtype=1&xquerycode='.urlencode(JRequest::getVar("reportcode", " "))."&fileid=";

  	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  	
  	try{
  		$output = curl_exec($ch);
  	}
  	catch(Exception $e){

  		$output =  $e->getTraceAsString();
  	}
  	$info = curl_getinfo($ch);
  	curl_close($ch);
	return $output;

	
  }	 
  
  
  function deleteXfileWithId($id)
  {
  	$ch = curl_init();
  	
  	$mxqueryroot = config_easysdi::getValue("catalog_mxqueryurl");
  	$mxqueryManage= $mxqueryroot."/".manage;
  	curl_setopt($ch, CURLOPT_URL, $mxqueryManage);
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_POST, true);

  	$data =   "operationtype=0&xquerycode=&fileid=".trim($id);

  	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  	
  	try{
  		$output = curl_exec($ch);
  	}
  	catch(Exception $e){

  		$output =  $e->getTraceAsString();
  	}
  	$info = curl_getinfo($ch);
  	curl_close($ch);
	return $output;

	
  }	 
  
  function process() {
  	
  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, "http://localhost:8080/MXQuery/process");
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_POST, true);

  	$data =   'url='.urlencode(JRequest::getVar("url", " ")).'&fileid='.urlencode(JRequest::getVar("fileid", " "));

  	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  	
  	try{
  		$output = curl_exec($ch);
  	}
  	catch(Exception $e){

  		$output =  $e->getTraceAsString();
  	}
  	$info = curl_getinfo($ch);
  	curl_close($ch);
	return  $output;
  //	HTML_testMxQuery::display( $output);
  //  die();
  }
  
  function testUid($uid){
  	
  		$uidArr = explode("-",$uid);
		if (count($uidArr)!=5) // then its a stacktrace.
			return false;
		else
			return true ;
  }
  function processXQueryReport(){
  	
  	global $mainframe;	

  	$fullURI = explode("?",$_SERVER['REQUEST_URI']);
  	
  	$homeUrl = "http://".$_SERVER['HTTP_HOST']."/".$fullURI[0] ;
	$database=& JFactory::getDBO(); 
	$user =& JFactory::getUser();
	$cid= JRequest::getVar('cid',0);	
	
  	if($user->id==''){
		 $mainframe->redirect($homeUrl,  JText::_("CATALOG_XQUERY_NOACCESS"), "ERROR");
	}
		
	$selectsql = "select id from #__sdi_xqueryreportassignation where report_id=".$cid." and user_id=".$user->id;
	
  	$database->setQuery( $selectsql);
	$access = $database->loadResult();
	if ($database->getErrorNum()) {			
		$mainframe->redirect($homeUrl, $database->getErrorMsg(),"ERROR");	
		
	}
	
  	if($access ==''){
		 $mainframe->redirect($homeUrl,  JText::_("CATALOG_XQUERY_NOACCESS"), "ERROR");
	}
	
		
	
  	$selectsql = "select xfileid, reportcode, xslttemplateurl,  applicationType from #__sdi_xqueryreport where id=".$cid;
	
  	$database->setQuery( $selectsql);
	$row = $database->loadObjectList();
	if ($database->getErrorNum()) {			
			$mainframe->redirect($homeUrl, $database->getErrorMsg(),"ERROR");	
	}
	
	if(($row[0]->xfileid =='') || ($row[0]->reportcode =='')){
		$mainframe->redirect($homeUrl,  JText::_("CATALOG_XQUERY_NOREPORTCODECREATED"), "ERROR");
	}		
	$xfileid = $row[0]->xfileid;
	
  	$selectsql = "select prefix, uri from #__sdi_namespace";
	
  	$database->setQuery($selectsql);
	$namespaces = $database->loadObjectList();
	if ($database->getErrorNum()) {			
		$mainframe->redirect($homeUrl, $database->getErrorMsg(),"ERROR");	
	}
	
	$nsList ="";

	$index= 0;
	foreach($namespaces as $ns){
		$index = $index+1;
		$nsList .=$ns->prefix ."=".$ns->uri.";"	;	
	}
	
  	$mxqueryroot = config_easysdi::getValue("catalog_mxqueryurl");
  	$mxqueryProcess= $mxqueryroot."/process";
  	
  	
  	$cookiesList=array();
  	foreach($_COOKIE as $key => $val)
  	{
  		$cookiesList[]=$key."=".$val;
  	}
  	$cookies= implode(";", $cookiesList);

  	$ch = curl_init();
  	curl_setopt($ch, CURLOPT_URL, $mxqueryProcess);
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  	curl_setopt($ch, CURLOPT_POST, true);
  	curl_setopt($ch, CURLOPT_COOKIE, $cookies);

	$getXMLUrl = "http://".$_SERVER['HTTP_HOST']."/".$fullURI[0]."?option=com_easysdi_catalog&task=provideXMLDataForXQueryReport&cid=".$cid ;
  	$data =   'url='.urlencode($getXMLUrl).'&fileid='.urlencode(trim($xfileid)).'&namespaces='.urlencode(trim($nsList));

  	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  	
  	try{
  		$output = curl_exec($ch);
  	}
  	catch(Exception $e){

  		$output =  $e->getTraceAsString();
  	}
  	$info = curl_getinfo($ch);
  	curl_close($ch);
  	
	$extension =".txt";
	$contentType ="text/plain";
	
	if( $row[0]->xslttemplateurl !=''){
		try{
			$ch = curl_init();
			$timeout = 10;
			curl_setopt($ch,CURLOPT_URL, $row[0]->xslttemplateurl);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$data = curl_exec($ch);
			curl_close($ch);

			$style = new DomDocument();
			$style->loadXML($data);

			$output = $processor->transformToDoc($output);
			$output = displayManager::buildXHTML($style, $output);
		}
		catch(Exception $e){

			$output =  $e->getTraceAsString();
		}
		
	
	}	

	if( $row[0]->applicationType == 1){
		$contentType ="text/xml";
		$extension =".xml";
	}else if( $row[0]->applicationType == 2){
		$contentType ="text/html";
		$extension =".html";
	}else{
		$contentType ="text/plain";
		$extension =".txt";
	}


	ini_set('zlib.output_compression', 0);
	header('Content-type:'.$contentType);
	header('Content-Disposition: attachement; filename="results'.$extension);
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
	header('Pragma: public');
	header("Expires: 0");
	header("Content-Length: ".strlen($output)); // Attention, très important que la taille soit juste, sinon IE pos problème

	echo $output;
	//Very important, if you don't call this, the content-type will have no effect
	die();
  	

  	
  	
  }
  function provideXMLDataForXQueryReport(){
  	
  		ini_set('zlib.output_compression', 0);
		header('Content-type: application/xml');
		header('Content-Disposition: attachement; filename="metadata.xml"');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Pragma: public');
		header("Expires: 0"); 
  	
  	  	$fullURI = explode("?",$_SERVER['REQUEST_URI']);
  		$homeUrl = "http://".$_SERVER['HTTP_HOST']."/".$fullURI[0] ;
  		global $mainframe;	
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		$cid= JRequest::getVar('cid',0);
		
    	if($user->id==''){
				$xmlResponse = "ERROR : No user id found";
		}
		
  		$selectsql = "select id from #__sdi_xqueryreportassignation where report_id=".$cid." and user_id=".$user->id;
		$medatadataFilterSql ="";
		$metadataIdSqlfilter ="";
		
  		$database->setQuery( $selectsql);
		$access = $database->loadResult();
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			header("Content-Length: ".strlen($xmlResponse)); //
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		}
		
		if($access ==''){
			$xmlResponse ="ERROR:User has no access to this report";
			header("Content-Length: ".strlen($xmlResponse)); //
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		}
		
		// verify access;
		if($cid == 0){
			$xmlResponse = "ERROR : No report ID provided";
			header("Content-Length: ".strlen($xmlResponse)); //
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
			
		}
		
			
		$medatadataFilterSql = "select sqlfilter from #__sdi_xqueryreport where id=".$cid ;
		
  		$database->setQuery( $medatadataFilterSql);
		$metadataIdSqlfilter = $database->loadResult();
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		
		}
		
	
 		$database->setQuery( $metadataIdSqlfilter);
		$metadataIds = $database->loadObjectList();
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		}
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$cswfilter = ADMIN_xQuery::buildCSWFilter($metadataIds, $report->ogcfilter );		
		$xmlBody = SITE_catalog::BuildCSWRequest(0, 0, "results", "gmd:MD_Metadata", "full", "1.1.0", $cswfilter, $ogcsearchsorting, "ASC");
		$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$xmlBody);
		echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
		die;
	
	}
		
		

  
  
  function buildCSWFilter($metadataIds, $ogcfilter){
		
				
  		$cswfilterCond.= "<ogc:Or>";
  		foreach ($metadataIds as $metadataId)
  		{
  			//keep it so to keep the request "small"
  			$cswfilterCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>$metadataId->guid</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
  		}
  			
  		$cswfilterCond.= "</ogc:Or>";		
		$cswfilterCond.= $ogcfilter;
		$cswfilter = "<ogc:Filter xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">\r\n";
		$cswfilter .= $cswfilterCond;
		$cswfilter .= "</ogc:Filter>\r\n";
		
		return $cswfilter;
			
  }
}


?>