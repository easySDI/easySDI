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
	var adminTestXQueryReportUrl =  "<?php echo "index.php?option=com_easysdi_catalog&task=processXQueryReport&cid="?>";
	var listXQueryReportUrl = "<?php echo "index.php?option=com_easysdi_catalog&task=listQueryReports"?>";
	var listMyReportUrl = "<?php echo "index.php?option=com_easysdi_catalog&view=catalog+managment&task=listMyReports"?>";

	
	var adminPanelUrl = "<?php echo "index.php?option=com_easysdi_core"?>";
	var selectOnlyOneMsg = "<?php echo JText::_("CATALOG_XQUERY_SELECTONLYONE") ?>";
	var reportToPreviewId = 0;
	var reportToUpdateId = 0;
	var reportToDeleteId = 0;
	var reportToAssignId = <?php echo JRequest::getVar('cid', 0)?>;
	var tmpReportIds = new Array();
	var tmpUserAccountsToAdd = new Array();
	var tmpUserAccountsToRemove = new Array();
	var currentOrgFilter = "";
	var sortByQueryName=  "<?php echo JRequest::getVar('sortByQueryName', "asc")?>";
	var sortByUserFullName=  "<?php echo JRequest::getVar('sortByUserFullName', "asc")?>";
	var sortByUserEmailAddress=  "<?php echo JRequest::getVar('sortByUserEmailAddress', "asc")?>";
	var showUsers=  <?php echo JRequest::getVar('showUsers', 0)?>; // 0= all, 1= users assigned only, 2= users not yet assigned.

	
	function submitbutton(action,id) 
	{
				
		actionUrl ="";
		if (action == "newXQueryReport" )
			actionUrl = newReportUrl;
		
		else if (action == "editXQueryReport" ){

			if (id!=undefined)
				actionUrl = editReportUrl + id;
				
			else if(tmpReportIds.length == 1)
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
			if (id!=undefined)
				actionUrl = assignReportUrl +  id + currentOrgFilter + "&resetpagination=1";
			else if(tmpReportIds.length == 1)
				actionUrl = assignReportUrl +  reportToAssignId + currentOrgFilter + "&resetpagination=1";
			else{
				 if (reportToAssignId != 0)
					 actionUrl = assignReportUrl +  reportToAssignId + currentOrgFilter +"&resetpagination=1" ;
				 else
					alert(selectOnlyOneMsg);
			}
		}
		else if (action == "assignXQueryReportSortEmail" ){
			
			if(tmpReportIds.length == 1)
				actionUrl = assignReportUrl +  reportToAssignId + getOrgFilter() + "&sortByUserEmailAddress="+sortByUserEmailAddress +"&sortType=email";
			else{
				 if (reportToAssignId != 0)
					 actionUrl = assignReportUrl +  reportToAssignId + getOrgFilter()+ "&sortByUserEmailAddress="+sortByUserEmailAddress +"&sortType=email";
				 else
					alert(selectOnlyOneMsg);
			}
		}
		else if (action == "assignXQueryReportSortFullName" ){
			
			if(tmpReportIds.length == 1)
				actionUrl = assignReportUrl +  reportToAssignId + getOrgFilter()+ "&sortByUserFullName="+sortByUserFullName +"&sortType=fullname";
			else{
				 if (reportToAssignId != 0)
					 actionUrl = assignReportUrl +  reportToAssignId + getOrgFilter() + "&sortByUserFullName="+sortByUserFullName +"&sortType=fullname";
				 else
					alert(selectOnlyOneMsg);
			}
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
		else if (action =="listXQueryReport"){
			actionUrl = listXQueryReportUrl +"&sortByQueryName="+sortByQueryName;
		}

		else if(action =="listMyReport"){
			actionUrl = listMyReportUrl +"&sortByQueryName="+sortByQueryName ;
		}
		else if (action =="adminPanel"){
			actionUrl = adminPanelUrl ;
		}
		
		else{ actionUrl ="";}
		


		if(actionUrl !=""){
			document.adminForm.action= actionUrl;
			document.adminForm.submit();
		}
		
	}

	function getOrgFilter(){
		node = document.getElementById("orgfilter")
		index= node.selectedIndex;
		currentOrgFilter = "&root="+node[index].value;
		return currentOrgFilter;
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

		debugger;
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

	function setSort(item, sortType, refreshType){
		if(item=="QueryName")
			sortByQueryName = sortType;
		else if(item=="userFullName")
			sortByUserFullName = sortType;
		else if(item=="userEmail")
			sortByUserEmailAddress = sortType;
		else 
			return;

		submitbutton(refreshType);
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
		
		$selectsql = "select * from #__sdi_xqueryreport order by xqueryname ".JRequest::getVar('sortByQueryName', "asc");
		
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
		
			}
		}
		catch(Exception $e)
		{
			$mainframe->redirect($xqueryHomeUrl, $e->getTraceAsString());
		}
		
		try{
			if($deletesql != ""){
				$deletesql ="DELETE FROM #__sdi_xqueryreportassignation where report_id in(".join(",",$cid).")";
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
		
		$sortType = JRequest::getVar('sortType', '');
		$sortByEmail =  JRequest::getVar('sortByUserEmailAddress', 'asc');
		$sortByUserFullName =  JRequest::getVar('sortByUserFullName', 'asc');
		
		if($resetpagination ==1){			
			//JRequest::setVar('limit', 10);
			JRequest::setVar('limitstart', 0);
			JRequest::setVar('resetpagination', 0);
		}
	
		//	JRequest::setVar('limitstart', 0);
		
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
			$query = "SELECT #__users.name as account_name,#__users.email as account_email, #__users.usertype as account_usertype,   #__sdi_account.user_id as id FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NOT NULL ";
					} else {
			$query = "SELECT #__users.name as account_name,#__users.email as account_email, #__users.usertype as account_usertype,   #__sdi_account.user_id as id FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$root_acc_id." AND #__sdi_account.root_id IS NOT NULL ";
		}	
		
		$sortCondition = "";
		if($sortType =="email")
			$sortCondition = "order by account_email  " . $sortByEmail;
		else if ($sortType =="fullname")
			$sortCondition = "order by account_name " . $sortByUserFullName;
		else
			$sortCondition = "order by account_name asc";
			
		$query.=$sortCondition;
		
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
		
		$limit		=  JRequest::getVar('limit', 10);
		$limitstart	=  JRequest::getVar('limitstart', 0);
		//get the total
		$query = "select count(*)  from #__sdi_xqueryreportassignation where user_id =".$user->id;
		$database->setQuery($query);
		$total = $database->loadResult();
		$pagination = new JPagination($total,$limitstart,$limit);
		
		$selectsql = "select #__sdi_xqueryreport.* from #__sdi_xqueryreportassignation , #__sdi_xqueryreport  
					  where #__sdi_xqueryreport.id=#__sdi_xqueryreportassignation.report_id AND #__sdi_xqueryreportassignation.user_id =".$user->id." order by #__sdi_xqueryreport.xqueryname ".JRequest::getVar('sortByQueryName', "asc");
		
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
	
	$usertype= strtolower($user->usertype);
	
  	if($access ==''){
  		
  		if(strpos($usertype, "admin")=== FALSE)
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
  	$mxqueryPaginationStep = config_easysdi::getValue("catalog_mxquerypagination");
  	$maxRecords = self::getMaxRecords($database, $cid);
  	
  	
  	
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
  	$data .= '&maxrecords='.urlencode($maxRecords).'&paginationstep='.urlencode($mxqueryPaginationStep);

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
	if (strpos($contentType, "html")===FALSE)
		header('Content-Disposition: attachment; filename="results'.$extension);
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
		$paginationStep = JRequest::getVar('paginationstep');
		$startPosition= JRequest::getVar('startposition');
		
    	if(($user->id=='') || ($paginationStep=='')|| ($startPosition=='')){
				$xmlResponse = "ERROR : No user id found";
				header("Content-Length: ".strlen($xmlResponse)); 
				echo $xmlResponse; 
				die;
		}

		$usertype= strtolower($user->usertype);	
		
		
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
			
			if(strpos($usertype, "admin")=== FALSE){ //allowing admins to access anyway.
				$xmlResponse ="ERROR:User has no access to this report";
				header("Content-Length: ".strlen($xmlResponse)); //
				echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
				die;
			}
			
		}
		

		if($cid == 0){
			$xmlResponse = "ERROR : No report ID provided";
			header("Content-Length: ".strlen($xmlResponse)); //
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
			
		}
		
			
		$medatadataFilterSql = "select sqlfilter from #__sdi_xqueryreport where id=".$cid ;
		
  		$database->setQuery( $medatadataFilterSql);
		$metadataIdSqlfilter = trim($database->loadResult());
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		
		}
		
		if ($metadataIdSqlfilter =='')
			$metadataIdSqlfilter = "select guid from #__sdi_metadata";
		
		$cswfilter="";
		
 		$database->setQuery( $metadataIdSqlfilter, $startPosition, $paginationStep);
 		
 		
		$metadataIds = $database->loadObjectList();
		
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		}
		$cswfilter = ADMIN_xQuery::buildCSWFilter($metadataIds, $report->ogcfilter );		
		
		//$maxRecords = count($metadataIds);
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		$xmlBody = ADMIN_xQuery::BuildCSWRequest($paginationStep, 0, "results", "gmd:MD_Metadata", "full", "1.1.0", $cswfilter, $ogcsearchsorting, "ASC");
		$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$xmlBody);
		
	
    		
		echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
		die;
	
	}
		
function getMaxRecords($database, $cid){
	
		
		
		$medatadataFilterSql = "select sqlfilter from #__sdi_xqueryreport where id=".$cid ;
		
  		$database->setQuery( $medatadataFilterSql);
		$metadataIdSqlfilter = trim($database->loadResult());
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		
		}
		
		if ($metadataIdSqlfilter =='')
			$metadataIdSqlfilter = "select guid from #__sdi_metadata";
		
		
 		$database->setQuery( $metadataIdSqlfilter);
		$metadataIds = $database->loadObjectList();		
		if ($database->getErrorNum()) {			
			$xmlResponse = "ERROR : " +$database->getErrorMsg();
			echo $xmlResponse; // return the xml response directly as xml. // or see how it is done by export xml.
			die;
		}		
		
		$maxRecords = count($metadataIds);
		return  $maxRecords;
}		

  
  
  function buildCSWFilter($metadataIds, $ogcfilter){
		
  		$cswfilterCond="";
				
  		
  		foreach ($metadataIds as $metadataId)
  		{
  			//keep it so to keep the request "small"
  			$cswfilterCond .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>$metadataId->guid</ogc:Literal></ogc:PropertyIsEqualTo>\r\n";
  		}
  		if($cswfilterCond!="")
  			$cswfilterCond= "<ogc:Or>".$cswfilterCond."</ogc:Or>";	
  			
  		
  			
		$cswfilterCond.= $ogcfilter;
		$cswfilter = "<ogc:Filter xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">\r\n";
		$cswfilter .= $cswfilterCond;
		$cswfilter .= "</ogc:Filter>\r\n";
		
		return $cswfilter;
			
  }
  
	function BuildCSWRequest($maxRecords, $startPosition, $resultType, $typeNames, $elementSetName, $constraintVersion, $filter, $sortBy, $sortOrder, $mode = 'CORE')
	{
		//Bug: If we have accents, we must specify ISO-8859-1
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$req .= "";
		
		//Get Records section
		$req .=  "<csw:GetRecords 
					xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" 
					service=\"CSW\" 
					version=\"2.0.2\" ";
		
		if ($resultType != "")
		{
			$req .= "resultType=\"$resultType\" 
					outputSchema=\"csw:IsoRecord\" 
					content=\"".$mode."\" ";
		}

		// add max records if not 0
		if($maxRecords != 0)
			$req .= "maxRecords=\"".$maxRecords."\" ";
		
		//add start position
		if($startPosition != 0)
			$req .= "startPosition=\"".$startPosition."\" ";
		
		$req .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xsi:schemaLocation=\"http://www.opengis.net/cat/csw/2.0.2 http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd\">\r\n";
	
		//Query section
		//Types name
		$req .= "<csw:Query typeNames=\"".$typeNames."\">\r\n";
		if($elementSetName != "")
		{
			//ElementSetName
			$req .= "<csw:ElementSetName>".$elementSetName."</csw:ElementSetName>\r\n";
		}
		//ConstraintVersion
		$req .="<csw:Constraint version=\"".$constraintVersion."\">\r\n";
		//filter
		$req .= $filter."\r\n";
		
		$req .= "</csw:Constraint>\r\n";
		
		//Sort by
		if($sortBy != "" && $sortOrder != ""){
			$req .= "<ogc:SortBy>";
			$req .= "<ogc:SortProperty>";
			$req .= "<ogc:PropertyName>".$sortBy."</ogc:PropertyName>";
			$req .= "<ogc:SortOrder>".$sortOrder."</ogc:SortOrder>";
			$req .= "</ogc:SortProperty>";
			$req .= "</ogc:SortBy>";
		}
		
		
		$req .= "</csw:Query>\r\n";
		$req .= "</csw:GetRecords>\r\n";
		
		//echo htmlspecialchars(utf8_encode($req));
		return utf8_encode($req);
	}
}


?>