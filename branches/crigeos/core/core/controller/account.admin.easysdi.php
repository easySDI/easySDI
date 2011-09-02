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

class ADMIN_account {

	function includeAccountExtension($tab_id,$tab_location,$action,$account_id)
	{
		$database =& JFactory::getDBO();
				
		$database->setQuery( "SELECT code FROM #__sdi_accountextension WHERE accounttab_id = ".$tab_id." AND tablocation_id = '".$tab_location."' AND action = '".$action."' ORDER BY ordering" );
		$rows = $database->loadObjectList() ;
		
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{			
			$row = $rows[$i];
			eval ($row->code);			
		}
	}
	
	function listAccount($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$filter = "";
		if ( $search ) {
			$filter .= " AND (#__users.name LIKE '%$search%'";
			$filter .= " OR #__users.username LIKE '%$search%'";		
			$filter .= " OR #__sdi_account.acronym LIKE '%$search%'";		
			$filter .= " OR #__sdi_account.id LIKE '%$search%'";		
			$filter .= " OR #__sdi_account.code LIKE '%$search%')";		
		}
	

		// Decompte des enregistrements totaux
		if ($type == '') {
			$query = "SELECT COUNT(*) FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NULL";
		} else {
			$query = "SELECT COUNT(*) FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id = ".$type;
		}

		$query = "SELECT COUNT(*) FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NULL";
		
		$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		//print_r($type); echo "<br>";
		if ($type == '') {
			$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.* FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NULL";
		} else {
			$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.* FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$type." AND #__sdi_account.root_id IS NOT NULL";
		}			
		$query .= $filter;
		$query .= " ORDER BY #__users.name";
		if ($use_pagination) {
			$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
		}
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
	
		HTML_account::listAccount($use_pagination, $rows, $pageNav, $search, $option, $type, $profile, $category, $payment);
	}

	// Creation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editRootAccount($id, $option) {
		if($id=='')
		{
			$id = JRequest::getVar('id');
			if ($id=='')
			{
				$id=0;
			}
		}
		$database =& JFactory::getDBO(); 
		$rowAccount = new account( $database );
		$rowAccount->load( $id );
		$rowAccount->parent_id = $id; 
		/*if ($rowAccount->account_entry != null && $rowAccount->account_entry != '0000-00-00') {
			$rowAccount->account_entry = date('d.m.Y H:i:s',strtotime($rowAccount->account_entry));
		} else {
			$rowAccount->account_entry = null;
		}*/
		/*if ($rowAccount->account_exit != null && $rowAccount->account_exit != '0000-00-00')	{
			$rowAccount->account_exit = date('d.m.Y H:i:s',strtotime($rowAccount->account_exit));
		} else {
			$rowAccount->account_exit = null;
		}*/
	
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=1" );
		$contact_id = $database->loadResult();
		//echo $database->getErrorMsg();
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		//print_r($rowContact); echo "<br>";
		
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		//echo $database->getErrorMsg();
		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );
		//print_r($rowSubscription); echo "<br>";
		
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		//echo $database->getErrorMsg();
		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		//print_r($rowDelivery); echo "<br>";
		
		//Get availaible profile
		$language =& JFactory::getLanguage();
		$database->setQuery( "SELECT ap.id as value, t.label as text FROM #__sdi_language l, #__sdi_list_codelang cl, #__sdi_accountprofile ap LEFT OUTER JOIN #__sdi_translation t ON ap.guid=t.element_guid WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."'" );
		$rowsProfile = $database->loadObjectList();
		
		//Get user profile
		$rowsAccountProfile="";
		if($rowAccount->id)
		{
			$database->setQuery( "SELECT accountprofile_id as value FROM #__sdi_account_accountprofile WHERE account_id=".$rowAccount->id );
			$rowsAccountProfile = $database->loadObjectList();
			echo $database->getErrorMsg();
		}
		
		$rowUser =&	 new JTableUser($database);
		$JId = JRequest::getVar('JId');
		if($JId=='')
		{
			$JId = $rowAccount->user_id;
		}
		$rowUser->load($JId);		
		if ($JId == '')
		{
			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}

		HTML_account::editRootAccount( $rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery, $rowsProfile, $rowsAccountProfile, $option );
	}

	// Creation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editAffiliateAccount( $id, $option ) {		
		// if $id is empty, situation is : refresh page after change the joomla account
		// then get the _POST variable 'account_id' to retrieve the account object from the database
		if($id=='')
		{
			$id = JRequest::getVar('id');
			if ($id=='')
			{
				//if 'account_id' does not return a user object, set 'id' to 0 as in create situation
				$id=0;
			}
		}
		$database =& JFactory::getDBO();

		$rowAccount = new account( $database );
		$rowAccount->load( $id );
	
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=1" );		
		$contact_id = $database->loadResult();
		//echo $database->getErrorMsg();		
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );

		$rowUser =&	 new JTableUser($database);
		//JId represents the selected joomla account
		$JId = JRequest::getVar('JId');
		if($JId=='')
		{
			//no joomla account select, so get the joomla account of the current account 
			$JId = $rowAccount->user_id;
		}
		$rowUser->load($JId);
				
		$type = JRequest::getVar('type','');
		
		if ($id == 0)
		{
			$database->setQuery( "SELECT root_id FROM #__sdi_account WHERE id='".$type."'");		
			$root_id = $database->loadResult();
			
		 		if ($root_id != null){
					$rowAccount->root_id=$root_id;
		 		}else{
		 			$rowAccount->root_id=$type;
		 		}
		 		
			$rowAccount->parent_id=$type;
			
			if ($JId == '')
			{
				$rowUser->usertype='Registered';
				$rowUser->gid=18;
			}
		}

		//Get availaible profile
		$language =& JFactory::getLanguage();
		$database->setQuery( "SELECT ap.id as value, t.label as text FROM #__sdi_language l, #__sdi_list_codelang cl, #__sdi_accountprofile ap LEFT OUTER JOIN #__sdi_translation t ON ap.guid=t.element_guid WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' AND ap.id IN(SELECT accountprofile_id FROM #__sdi_account_accountprofile WHERE account_id=".$rowAccount->root_id.")" );
		$rowsProfile = $database->loadObjectList();
		
		//Get user profile
		$rowsAccountProfile="";
		
		if($rowAccount->id <>0)
		{
			$database->setQuery( "SELECT accountprofile_id as value FROM #__sdi_account_accountprofile WHERE account_id=".$rowAccount->id );
			$rowsAccountProfile = $database->loadObjectList();
			echo $database->getErrorMsg();
		}
		
		
		HTML_account::editAffiliateAccount( $rowUser, $rowAccount, $rowContact, $type, $rowsProfile, $rowsAccountProfile, $option );
	}
	
	function removeAccount( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			//echo "<script> alert('Selectionnez un enregistrement e supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("Selectionnez un enregistrement e supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit;
		}
		foreach( $cid as $account_id )
		{
			$account = new account( $database );
			$account->load( $account_id );
		
			// Vider les entrees de la table #__sdi_address
			$database->setQuery("DELETE FROM #__sdi_address WHERE account_id=".$account_id);
			//echo $database->getQuery()."<br>";
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			
			// Vider les entrees de la table #__sdi_account_accountprofile
			$database->setQuery("DELETE FROM #__sdi_account_accountprofile WHERE account_id=".$account_id);
			//echo $database->getQuery()."<br>";
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			// Vider les entrees de la table #__sdi_actor
			$database->setQuery("DELETE FROM #__sdi_actor WHERE account_id=".$account_id);
			//echo $database->getQuery()."<br>";
			if (!$database->query())
			{	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			
			//$user =&	 new JTableUser($database);
			//$user->load( $account->user_id );
			//$user = new mosUser( $database );
			//$user->load( $account->user_id );
			if (!$account->delete()) {
				//echo "<script> alert('".$account->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
			}
			/* Contrairement e la V1, ne pas supprimer le compte Joomla associe */
			/*if (!$user->delete()) {
				//echo "<script> alert('".$user->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
			}
			*/
			ADMIN_account::includeAccountExtension(0,'BOTTOM','removeAccount',$account_id);
			
			
		}

		$mainframe->redirect("index.php?option=$option&task=listAccount" );		
	}

	function exportAccount( $cid, $option ) {
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			//echo "<script> alert('Selectionnez un enregistrement e exporter'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage('Selectionnez un enregistrement e exporter','error');
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit;
		}

		$document = new DomDocument();
		$xml = "<easysdi>";
		
		foreach( $cid as $account_id )
		{
			$xml .= "<account>";
			$account = new account( $database );
			$account->load( $account_id );
			$xml .= utf8_encode($account->toXML(true));
		
			$xml .= "<user>";
			$user =&	 new JTableUser($database);
			$user->load( $account->user_id );
			//$user =& JFactory::getUser( $account->user_id );
			/*$user = new mosUser( $database );
			$user->load( $account->user_id );*/
			$xml .= utf8_encode($user->toXML(true));
			$xml .= "</user>";

			$xml .= "<contact>";
			$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$account_id." AND type_id=1" );	
			$contact_id = $database->loadResult();
			$contact = new address( $database );
			$contact->load( $contact_id );
			$xml .= utf8_encode($contact->toXML(true));
			$xml .= "</contact>";

			$xml .= "<subscription>";
			$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$account_id." AND type_id=2" );
			$subscription_id = $database->loadResult();
			$subscription = new address( $database );
			$subscription->load( $subscription_id );
			$xml .= utf8_encode($subscription->toXML(true));
			$xml .= "</subscription>";

			$xml .= "<delivery>";
			$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$account_id." AND type_id=3" );
			$delivery_id = $database->loadResult();
			$delivery = new address( $database );
			$delivery->load( $delivery_id );
			$xml .= utf8_encode($delivery->toXML(true));
			$xml .= "</delivery>";

			$xml .= "</account>";
		}
		$xml .= "</easysdi>";
		//echo "<textarea rows='1000' cols='1000'>".$xml."</textarea>"; 		
		$document->loadXML($xml);
	
		$processor = new xsltProcessor();
		$style = new DomDocument();
		$style->load(dirname(__FILE__).'/../xsl/account.export.xsl');
		
		$processor->importStylesheet($style);
		
		
		error_reporting(0);
		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: none');
		header('Content-Type: application/octetstream; name="export.csv"');
		header('Content-Disposition: attachement; filename="export.csv"');
		echo $processor->transformToXml($document);

		die();

	}

	
	function saveAccount( $returnList, $option ) {
		global $mainframe;
						
		$database=& JFactory::getDBO(); 
		
		$rowUser =&	 new JTableUser($database);
		
		$type = $_POST['type'];
		
		if (!$rowUser->bind( $_POST)) {			
			//echo "<script> alert('".$rowUser->getError()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($rowUser->getError(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listAccount" );
			//exit();
		}
		//echo JRequest::getVar('password','')."<br>".JRequest::getVar('old_password','')."<br>".$rowUser->password."<br>";
		
		//Check if username already exit cause in jos_user name isn't unique but should be...
		if (JRequest::getVar('old_username','') != $rowUser->username){
			$database->setQuery("SELECT count(*) FROM #__users WHERE username = '".$rowUser->username."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_ALREADY_EXISTS"),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=ctrlPanelAccountManager" );
			}
		}
		
		//CSheck if email is unique also because it must be (pass recovery!).
                if (JRequest::getVar('old_email','') != $_POST['user_email']){
			$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$_POST['user_email']."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("CORE_EMAIL_ALREADY_REGISTRED"),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=ctrlPanelAccountManager" );
			}
		}
		
		if (JRequest::getVar('old_password','') != $rowUser->password)
		{
			$rowUser->password = md5( JRequest::getVar('password','') );
			/*$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword(JRequest::getVar('password',''), $salt);
			$rowUser->password = $crypt . ':' . $salt;*/
		}
		$rowUser->email = $_POST['user_email'];
		//print_r($rowUser); 
		//break;
		
		if (!$rowUser->store(false)) {
			$mainframe->enqueueMessage($rowUser->getError(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit();
		}
		
		if (JRequest::getVar('id','') == '')
		{
			$database->setQuery( "UPDATE #__users SET registerDate=now() WHERE id = (".$rowUser->id.")");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
				exit();
			}
		}

		$rowAccount = new account( $database );
		if (!$rowAccount->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit();
		}
		
		$rowAccount->user_id=$rowUser->id;
		$rowAccount->id=$_POST['account_id'];
		
		/*if ($rowAccount->code == null)
		{
			$rowAccount->code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}*/
		
		// Generer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowAccount->guid == null)
			$rowAccount->guid = helper_easysdi::getUniqueId();
		
		
		if (!$rowAccount->store(false)) {
			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			//echo "<script> alert('".$rowAccount->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	
		$counter=0;
		foreach( $_POST['address_id'] as $address_id )
		{
			$rowAddress = new address( $database );
			$rowAddress->id=$address_id;
			$rowAddress->account_id=$rowAccount->id;
			$rowAddress->type_id=$_POST['type_id'][$counter];
			
			if( $counter == 1 && isset($_POST['sameAddress1'] )  && $_POST['sameAddress1'] == 'on')
			{
				$index = 0;
			}
			elseif ($counter == 2 && isset($_POST['sameAddress2'] ) && $_POST['sameAddress2'] == 'on')
			{
				$index = 0;
			}
			else
			{
				$index = $counter;
			}
				/*if ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 2) 
				{
					$index = 0;
				} 
				elseif
				(
					$_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 3) {
					$index = 0;
				} 
				else 
				{
					$index = $counter;
				}*/
			
			
			$rowAddress->title_id=$_POST['title_id'][$index];
			$rowAddress->country_id=$_POST['country_id'][$index];
			$rowAddress->corporatename1=$_POST['corporatename1'][$index];
			$rowAddress->corporatename2=$_POST['corporatename2'][$index];
			$rowAddress->agentfirstname=$_POST['agentfirstname'][$index];
			$rowAddress->agentlastname=$_POST['agentlastname'][$index];
			$rowAddress->function=$_POST['function'][$index];
			$rowAddress->street1=$_POST['street1'][$index];
			$rowAddress->street2=$_POST['street2'][$index];
			$rowAddress->postalcode=$_POST['postalcode'][$index];
			$rowAddress->locality=$_POST['locality'][$index];
			$rowAddress->phone=$_POST['phone'][$index];
			$rowAddress->fax=$_POST['fax'][$index];
			$rowAddress->email=$_POST['email'][$index];
	
			// Generer un guid
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			if ($rowAddress->guid == null)
				$rowAddress->guid = helper_easysdi::getUniqueId();
				
			if (!$rowAddress->store()) {
				//echo "<script> alert('".$rowAddress->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
				exit();
			}
			
			$database->setQuery( "UPDATE #__sdi_address SET updated=now() WHERE id IN (".$rowAddress->id.")");
			if (!$database->query()) {
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
				exit();
			}

			$counter++;
		}
		$database->setQuery( "DELETE FROM #__sdi_actor WHERE account_id IN (".$rowAccount->id.")");
		if (!$database->query()) {
			//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit();
		}
		
		if(isset($_POST['role_id']))
		{
			if (count ($_POST['role_id'] )>0){
			foreach( $_POST['role_id'] as $role_id )
			{
				$database->setQuery( "INSERT INTO #__sdi_actor (guid, role_id, account_id, updated) VALUES ('".helper_easysdi::getUniqueId()."', ".$role_id.",".$rowAccount->id.",now())" );
				if (!$database->query()) {
					//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listAccount" );
					exit();
				}
				
			}
			}
		}
		
		//Save profile selection
		$database->setQuery( "DELETE FROM #__sdi_account_accountprofile WHERE account_id IN (".$rowAccount->id.")");
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit();
		}
		$profile_id_list ="";
		
		if(isset($_POST['profile_id']))
		{
			if (count ($_POST['profile_id'] )>1)
			{
				foreach( $_POST['profile_id'] as $profile_id )
				{
					$database->setQuery( "INSERT INTO #__sdi_account_accountprofile (accountprofile_id, account_id) VALUES (".$profile_id.",".$rowAccount->id.")" );
					if (!$database->query()) 
					{
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=listAccount" );
						exit();
					}
					$profile_id_list .= $profile_id;
					$profile_id_list .= ",";
				}
			}
			else{
				$database->setQuery( "INSERT INTO #__sdi_account_accountprofile (accountprofile_id, account_id) VALUES (".$_POST['profile_id'][0].",".$rowAccount->id.")" );
				if (!$database->query()) 
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listAccount" );
					exit();
				}
				$profile_id_list .= $profile_id;
			}
		}
		
		if($profile_id_list )
		{
			$profile_id_list = substr($profile_id_list, 0, strlen ($profile_id_list)-1 );
			//Update affiliate user profile
			$database->setQuery( "DELETE FROM #__sdi_account_accountprofile 
					   WHERE account_id IN (SELECT account_id FROM #__sdi_account WHERE root_id=".$rowAccount->id.") 
					   AND 
					   accountprofile_id NOT IN (".$profile_id_list.")");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
				exit();
			}
		}
		
		//Set account update date
		$query = "UPDATE #__sdi_account SET updated=now()";
		$query .= " WHERE id IN (".$rowAccount->id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
			exit();
		}
		
		//include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_asitvd'.DS.'core'.DS.'account.admin.asitvd.html.php');
		//ADMIN_ASITVD_Account::saveAccountFields ($rowAccount->account_id); 
		
		
		ADMIN_account::includeAccountExtension(0,'BOTTOM','saveAccount',$rowAccount->id);
		
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listAccount&type=$type");
		}
		
	}

	function cancelAccount( $returnList, $option ) {
		global $mainframe;
		ADMIN_account::includeAccountExtension(0,'TOP','cancelAccount',0);
		$database =& JFactory::getDBO();
		$row = new account( $database );
		$row->bind( $_POST );
		$row->checkin();
		$type = $_POST['type'];
		
		ADMIN_account::includeAccountExtension(0,'BOTTOM','cancelAccount',0);
		if ($returnList == true) {
			//mosRedirect( "index2.php?option=$option&task=listAccount" );
			$mainframe->redirect("index.php?option=$option&task=listAccount&type=$type" );
		}
		
		
	}
	
	

}

?>
