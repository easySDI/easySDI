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

class SITE_account {

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
	
	function showAccount( ) {
		global  $mainframe;
		$user = JFactory::getUser();

		//Allows Pathway with mod_menu_easysdi
		//breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYACCOUNT");
		
		//breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYACCOUNT",
		//								   "EASYSDI_MENU_ITEM_MYACCOUNT",
		//								   "index.php?option=$option&task=showAccount");
		
		if ($user->guest){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
			return;
		}
		if(!usermanager::isEasySDIUser($user))
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER"),"INFO");
			return;
		}
		if(!userManager::isUserAllowed($user,"MYACCOUNT"))
		{
			return;
		}

		$database =& JFactory::getDBO();
		$rowAccount = new AccountByUserId( $database );
		$rowAccount->load( $user->id );

			
		//Has the user the right to edit the account
		$query = "SELECT count(*)
		FROM #__sdi_actor as a ,
		#__sdi_list_role as b
		where a.role_id = b.id
		and b.code = 'MYACCOUNT'
		and a.account_id = $rowAccount->id";
		$database->setQuery($query );
		$hasTheRightToEdit = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
			$hasTheRightToEdit=0;
		}
			
		$query = "SELECT count(*)
		FROM #__sdi_actor as a ,
		#__sdi_list_role as b
		where a.role_id = b.id
		and b.code = 'ACCOUNT'
		and a.account_id = $rowAccount->id";
		$database->setQuery($query );
		$hasTheRightToManageHisOwnAffiliates = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
			$hasTheRightToManageHisOwnAffiliates=0;
		}
			
		if (is_null($rowAccount->root_id)){
			SITE_account::showRootAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates);
		}else{
			SITE_account::showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates);
		}
	}
	
	function showRootAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates) {
		global  $mainframe;
		$option = JRequest::getVar("option");
		$user = JFactory::getUser();
		if ($user->guest){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
			return;
		}
		if(!usermanager::isEasySDIUser($user))
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER"),"INFO");
			return;
		}
		$database =& JFactory::getDBO();
		$rowAccount = new AccountByUserId( $database );

		$rowAccount->load( $user->id );
		/*if ($rowAccount->Account_entry != null && $rowAccount->Account_entry != '0000-00-00') {
			$rowAccount->Account_entry = date('d.m.Y H:i:s',strtotime($rowAccount->Account_entry));
			} else {
			$rowAccount->Account_entry = null;
			}
			if ($rowAccount->Account_exit != null && $rowAccount->Account_exit != '0000-00-00')	{
			$rowAccount->Account_exit = date('d.m.Y H:i:s',strtotime($rowAccount->Account_exit));
			} else {
			$rowAccount->Account_exit = null;
			}*/

		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=1" );
		$contact_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
			
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}


		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
			
		$rowUser =&	 new JTableUser($database);
		$rowUser->load( $rowAccount->user_id );

		HTML_Account::showAccount( $hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates,$rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option );

	}


	function showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates)
	{	global  $mainframe;
	$user = JFactory::getUser();
	if ($user->guest)
	{
		$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
		return;
	}
	if(!usermanager::isEasySDIUser($user))
	{
		$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER"),"INFO");
		return;
	}

	$option = JRequest::getVar("option");
	$database =& JFactory::getDBO();

	$rowAccount = new AccountByUserId( $database );
	$rowAccount->load( $user->id );

	$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=1" );

	$contact_id = $database->loadResult();
	if ($database->getErrorNum())
	{
		echo "<div class='alert'>";
		echo $database->getErrorMsg();
		echo "</div>";
	}

	$rowContact = new address( $database );
	$rowContact->load( $contact_id );
/*
	$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=2" );
	$subscription_id = $database->loadResult();
	if ($database->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$database->getErrorMsg();
		echo "</div>";
	}

	$rowSubscription = new address( $database );
	$rowSubscription->load( $subscription_id );

	$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=3" );
	$delivery_id = $database->loadResult();
	if ($database->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$database->getErrorMsg();
		echo "</div>";
	}

	$rowDelivery = new address( $database );
	$rowDelivery->load( $delivery_id );
	if ($database->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$database->getErrorMsg();
		echo "</div>";
	}
*/	
	$rowUser =&	 new JTableUser($database);
	$rowUser->load( $rowAccount->user_id );

	if ($id == 0)
	{
		$rowAccount->root_id=JRequest::getVar('type','');
		$rowAccount->parent_id=JRequest::getVar('type','');
		$rowUser->usertype='Registered';
		$rowUser->gid=18;
	}

	//HTML_Account::showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, $rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery, $option );
	HTML_Account::showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, $rowUser, $rowAccount, $rowContact, $option );
	
	}
	
	function listAccount($option) 
	{
		global  $mainframe;

		//Allows Pathway with mod_menu_easysdi
		//breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYAFFILIATES");
		
		$user = JFactory::getUser();
		if(!userManager::isUserAllowed($user,"ACCOUNT"))
		{
			return;
		}

		//$option=JRequest::getVar("option");

		$db =& JFactory::getDBO();

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

		$rowAccount = new AccountByUserId( $db );
		$rowAccount->load( $user->id );

			
		//Has the user the right to manage the affiliate
		/*$query = "SELECT count(*) FROM #__easysdi_community_actor as a ,#__easysdi_community_role as b where a.role_id = b.role_id and role_code = 'ACCOUNT'  and Account_id = $rowAccount->Account_id";

		$db->setQuery($query );
		$hasTheRightToManageAffiliates = $db->loadResult();
		if ($db->getErrorNum()) {
		echo "<div class='alert'>";
		echo 			$db->getErrorMsg();
		echo "</div>";
		$hasTheRightToManageAffiliates=0;
		}
		*/
		$type = JRequest::getVar("type",$rowAccount->id);
		if (!$type){
			$type=$rowAccount->id;
		}

		/*if ($hasTheRightToManageAffiliates){*/
		$query = "SELECT COUNT(*) FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$type." AND #__sdi_account.id <> $type";
		$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.* FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$type." AND #__sdi_account.id <> $type ";

		$query .= $filter;
		$query .= " ORDER BY #__users.name";


		$db->setQuery( $query );
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		$types = array();
		$types[] = JHTML::_('select.option','',JText::_("CORE_LIST_ACCOUNT_ROOT") );

		if ($type==''){
			$db->setQuery( "SELECT #__sdi_account.id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NULL ORDER BY #__users.name" );
		}else{
			$db->setQuery( "SELECT #__sdi_account.id AS value,#__users.name AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id ='$type'" );
			$types = array_merge( $types, $db->loadObjectList() );
			$db->setQuery( "SELECT #__sdi_account.id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NOT NULL AND (#__sdi_account.parent_id = '$type' ) ORDER BY #__users.name" );
		}

		$types = array_merge( $types, $db->loadObjectList() );

		HTML_account::listAccount( $rows, $search, $option, $rowAccount->id,$types,$type);
	}

	function editAccount( ) 
	{
		global  $mainframe;
		$option = JRequest::getVar("option");
		$user = JFactory::getUser();

		if(!usermanager::isUserAllowed($user,"MYACCOUNT"))
		{
			return;
		}

		//Allows Pathway with mod_menu_easysdi
		$option = JRequest::getVar('option');
		/*breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYACCOUNT_EDIT",
										   "EASYSDI_MENU_ITEM_MYACCOUNT",
										   "index.php?option=$option&task=showPartner");
		*/
		$database =& JFactory::getDBO();
		$rowAccount = new AccountByUserId( $database );
		$rowAccount->load( $user->id );

		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=1" );
		$contact_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
			
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}


		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}
			
		$rowUser =&	 new JTableUser($database);
		$rowUser->load( $rowAccount->user_id );

		HTML_account::editAccount( $rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option );
	}
	
	// Cr�ation d'enregistrement (id = 0)
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
		echo $database->getErrorMsg();
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		//print_r($rowContact); echo "<br>";
		
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		echo $database->getErrorMsg();
		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );
		//print_r($rowSubscription); echo "<br>";
		
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		echo $database->getErrorMsg();
		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		//print_r($rowDelivery); echo "<br>";
		
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

		HTML_account::editRootAccount( $rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery, $option );
	}

	// Cr�ation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editAffiliateAccount( $id) {		
		$user = JFactory::getUser();

		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}

		
		//Allows Pathway with mod_menu_easysdi
		$option = JRequest::getVar('option');
		/*
		if($affiliate_id)
		{
			//Edit affiliate
			breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYAFFILIATES_EDIT",
											   "EASYSDI_MENU_ITEM_MYAFFILIATES",
											   "index.php?option=$option&task=listAffiliatePartner");
		}
		else
		{
			//Create affiliate
			breadcrumbsBuilder::addBreadCrumb("EASYSDI_MENU_ITEM_MYAFFILIATES_CREATE",
											   "EASYSDI_MENU_ITEM_MYAFFILIATES",
											   "index.php?option=$option&task=listAffiliatePartner");
		}
		*/
		
		$database =& JFactory::getDBO();
			
		$rowAccount = new accountByUserId( $database );
		
		if (!is_null($id))
		{
			if ($id!=0){
				$rowAccount->load( $id);
			}
			if ($rowAccount->user_id != $user->id ){
				$rowRootAccount = new accountByUserId( $database );
				$rowRootAccount->load( $user->id);
			}
		}
		else
		{
			$rowAccount->load( $user->id );
		}

		if ($id!=0){
			$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=1" );
			$contact_id = $database->loadResult();
			if ($database->getErrorNum()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
			}
			$rowContact = new address( $database );
			$rowContact->load( $contact_id );
			$rowUser =&	 new JTableUser($database);
			$rowUser->load( $rowAccount->user_id );

		}
		else {
			// new Affiliate
			$rootAccount = new accountByUserId( $database );
			$rootAccount->load($user->id);

			$parent_id = JRequest::getVar("type",$rootAccount->id);

			$parentAccount = new account( $database );
			$parentAccount ->load($parent_id );
			if ($parentAccount->root_id)
			{
				$rowAccount->root_id=$parentAccount->root_id;
			}
			else
			{
				$rowAccount->root_id=$parentAccount->id;
			}
			
			$rowAccount->parent_id=$parentAccount->id;
			
			$rowContact = new address( $database );
			$rowContact->id=0;
			$rowContact->country_id=0;
			
			$rowUser =&	 new JTableUser($database);
			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}
		$r_id=$rowAccount->parent_id;
		if(!$r_id)
		{
			$r_id = $rowAccount->id;
		}
		//Get user root profiles to get availaible profiles
		$language =& JFactory::getLanguage();
		$database->setQuery( "SELECT ap.id as value, t.label as text FROM #__sdi_language l, #__sdi_list_codelang cl, #__sdi_accountprofile ap LEFT OUTER JOIN #__sdi_translation t ON ap.guid=t.element_guid WHERE t.language_id=l.id AND l.codelang_id=cl.id AND cl.code='".$language->_lang."' AND ap.id IN(SELECT accountprofile_id FROM #__sdi_account_accountprofile WHERE account_id=".$r_id.")" );
		$rowsProfile = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		$rowsAccountProfile = "";
		if($rowAccount->id)
		{
			//Get user profile
			$database->setQuery( "SELECT accountprofile_id as value FROM #__sdi_account_accountprofile WHERE account_id=".$rowAccount->id );
			$rowsAccountProfile = $database->loadObjectList();
			echo $database->getErrorMsg();
		}

		if(!$rowAccount->id)
		{
			$rowAccount->id=0;
		}
		
		HTML_account::editAffiliateAccount( $rowUser, $rowAccount, $rowContact, $rowsProfile, $rowsAccountProfile, $option );
	}
	
	function removeAccount( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));
			exit;
		}
		foreach( $cid as $account_id )
		{
			$account = new account( $database );
			$account->load( $account_id );
		
			$user =&	 new JTableUser($database);
			$user->load( $account->user_id );
			//$user = new mosUser( $database );
			//$user->load( $account->user_id );
			if (!$account->delete()) {
				//echo "<script> alert('".$account->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));
			}
			/*if (!$user->delete()) {
				//echo "<script> alert('".$user->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false);
			}*/
			
			SITE_account::includeAccountExtension(0,'BOTTOM','removeAccount',$account_id);
			
			
		}

		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));		
	}

	function exportAccount( $cid, $option ) {
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � exporter'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage('S�lectionnez un enregistrement � exporter','error');
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));
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

	
	function saveAccount() {
		global $mainframe;
						
		$user = JFactory::getUser();
		if(!usermanager::isUserAllowed($user, "MYACCOUNT"))
		{
			return;
		}
		
		$option = JRequest::getVar("option");

		$database=& JFactory::getDBO(); 
		
		$rowUser =&	 new JTableUser($database);
		
		if (!$rowUser->bind( $_POST )) {			
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		//Check if username already exit cause in jos_user name isn't unique but should be...
		if (JRequest::getVar('old_username','') != $rowUser->username){
			$database->setQuery("SELECT count(*) FROM #__users WHERE username = '".$rowUser->username."'");
			$total = $database->loadResult();
			if($total > 0){
				echo "<div class='alert'>";
			        echo JText::_("EASYSDI_ACCOUNT_ALREADY_EXISTS");
			        echo "</div>";
				exit();
			}
		}
		
		//CSheck if email is unique also because it must be (pass recovery!).
                if (JRequest::getVar('old_email','') != $_POST['user_email']){
			$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$_POST['user_email']."'");
			$total = $database->loadResult();
			if($total > 0){
				echo "<div class='alert'>";
			        echo JText::_("EASYSDI_EMAIL_ALREADY_REGISTRED");
			        echo "</div>";
				exit();
			}
		}
		
		if (JRequest::getVar('old_password','') != $rowUser->password)
		{
			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword(JRequest::getVar('password',''), $salt);
			$rowUser->password = $crypt . ':' . $salt;
		}
		
		$rowUser->email = $_POST['user_email'];
		
		if (!$rowUser->store()) {

			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		if (JRequest::getVar('id','') == '')
		{
			$database->setQuery( "UPDATE #__users SET registerDate=now() WHERE id = (".$rowUser->id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}

		$rowAccount = new account( $database );
		if (!$rowAccount->bind( $_POST )) {			
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		$rowAccount->user_id=$rowUser->id;
		$rowAccount->id=$_POST['account_id'];
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowAccount->guid == null)
			$rowAccount->guid = helper_easysdi::getUniqueId();
		
		
		if (!$rowAccount->store(false)) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
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
	
			// G�n�rer un guid
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			if ($rowAddress->guid == null)
				$rowAddress->guid = helper_easysdi::getUniqueId();
				
			if (!$rowAddress->store()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
			
			$database->setQuery( "UPDATE #__sdi_address SET updated=now() WHERE id IN (".$rowAddress->id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}

			$counter++;
		}

		$query = "UPDATE #__sdi_account SET updated=now()";
		$query .= " WHERE id IN (".$rowAccount->id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		//$mainframe->redirect("index.php?option=$option&task=listAccount&type=$type");
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
	}

	function cancelAccount( $returnList, $option ) {
		global $mainframe;
		SITE_account::includeAccountExtension(0,'TOP','cancelAccount',0);
		$database =& JFactory::getDBO();
		$row = new account( $database );
		$row->bind( $_POST );
		$row->checkin();
		$type = $_POST['type'];
		
		SITE_account::includeAccountExtension(0,'BOTTOM','cancelAccount',0);
		if ($returnList == true) {
			//mosRedirect( "index2.php?option=$option&task=listAccount" );
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount&type=$type"), false));
		}
		
		
	}
	
	function checkIsAccountDeletable($affiliate_id){
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}

		if (!$affiliate_id) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"ERROR");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAffiliateAccount"), false));
			return;
		}
		
		$Account = new AccountByUserId( $database );
		$Account->load( $affiliate_id );
		$user =&	 new JTableUser($database);
		$user->load( $Account->user_id );		
		//Check if the user is referenced by product, order, affiliate before deleting it
		$errMsg = array();
		$query ="SELECT o.* FROM #__sdi_object o
					LEFT OUTER JOIN #__sdi_manager_object m ON o.id=m.object_id
					LEFT OUTER JOIN #__sdi_editor_object e ON o.id=e.object_id  
					WHERE	o.account_id=$Account->id 
							OR m.account_id=$Account->id 
							OR e.account_id=$Account->id";
		$database->setQuery( $query );
		$products = $database->loadObjectList();
		if($products)
		{
			$list = "";
			foreach ($products as $product)
			{
				$list .= "<br> - ".$product->name; 
			}	
			$list .= "<br>";
			if(count($errMsg) == 0)
				array_push($errMsg,JText::_("EASYSDI_DELETE_AFFILIATE_ERROR_CONCLUSION"));
			array_push($errMsg, JText::sprintf("EASYSDI_DELETE_AFFILIATE_ERROR_PRODUCT",$user->username, $list));
			//$mainframe->enqueueMessage(JText::sprintf("EASYSDI_DELETE_AFFILIATE_ERROR_PRODUCT",$user->username, $list));
			//$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAffiliateAccount"), false));
		}
		
		//Check if the user is Referenced by an pending order
		/*$query ="SELECT * FROM #__easysdi_order WHERE user_id=$user->id OR account_id=$Account->id";
		$database->setQuery( $query );
		$orders = $database->loadObjectList();
		if($orders)
		{
			$list = "";
			foreach ($orders as $order)
			{
				$list .= "<br> - ".$order->name; 
			}	
			$list .= "<br>";
			if(count($errMsg) == 0)
				array_push($errMsg,JText::_("EASYSDI_DELETE_AFFILIATE_ERROR_CONCLUSION"));
			array_push($errMsg, JText::sprintf("EASYSDI_DELETE_AFFILIATE_ERROR_ORDER",$user->username, $list));
			//$mainframe->enqueueMessage(JText::sprintf("EASYSDI_DELETE_AFFILIATE_ERROR_ORDER",$user->username, $list));
			//$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAffiliateAccount"), false));
		}
		*/
		//check if current user has children
		$query ="SELECT p.*, u.username FROM #__sdi_account p, #__users u WHERE (p.root_id=$Account->id OR p.parent_id=$Account->id AND ( p.user_id=u.id))";
		$database->setQuery( $query );
		$Accounts = $database->loadObjectList();
		if($Accounts)
		{
			$list = "";
			foreach ($Accounts as $Account)
			{
				$list .= "<br> - ".$Account->username; 
			}	
			$list .= "<br>";
			if(count($errMsg) == 0)
				array_push($errMsg,JText::_("EASYSDI_DELETE_AFFILIATE_ERROR_CONCLUSION"));
			array_push($errMsg, JText::sprintf("EASYSDI_DELETE_AFFILIATE_ERROR_Account",$user->username, $list));
			//$mainframe->enqueueMessage(JText::sprintf("EASYSDI_DELETE_AFFILIATE_ERROR_Account",$user->username, $list));
			//$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAffiliateAccount"), false));
		}
		//Add conclusion if there was an error
		
		return $errMsg;
	}

	function saveAffiliateAccount(  ) 
	{
		global $mainframe;

		$user = JFactory::getUser();
		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}

		$option = JRequest::getVar("option");

		$database=& JFactory::getDBO();

		$rowUser =&	 new JTableUser($database);


		if (!$rowUser->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		//Check if username already exit cause in jos_user name isn't unique but should be...
		if (JRequest::getVar('old_username','') != $rowUser->username){
			$database->setQuery("SELECT count(*) FROM #__users WHERE username = '".$rowUser->username."'");
			$total = $database->loadResult();
			if($total > 0){
				echo "<div class='alert'>";
			        echo JText::_("EASYSDI_ACCOUNT_ALREADY_EXISTS");
			        echo "</div>";
				exit();
			}
		}
		
		//CSheck if email is unique also because it must be (pass recovery!).
                if (JRequest::getVar('old_email','') != $rowUser->email){
			$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$rowUser->email."'");
			$total = $database->loadResult();
			if($total > 0){
				echo "<div class='alert'>";
			        echo JText::_("EASYSDI_EMAIL_ALREADY_REGISTRED");
			        echo "</div>";
				exit();
			}
		}
		
		if (JRequest::getVar('old_password','') != $rowUser->password)
		{
			//$rowUser->password = md5( JRequest::getVar('password','') );
			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword(JRequest::getVar('password',''), $salt);
			$rowUser->password = $crypt . ':' . $salt;
		}
		
		$rowUser->email = $_POST['user_email'];
		
		if (!$rowUser->store()) {

			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		if (JRequest::getVar('id','') == '')
		{
			$database->setQuery( "UPDATE #__users SET registerDate=now() WHERE id = (".$rowUser->id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}

		$rowAccount = new account( $database );
		if (!$rowAccount->bind( $_POST )) {			
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		$rowAccount->user_id=$rowUser->id;
		$rowAccount->id=$_POST['account_id'];
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowAccount->guid == null)
			$rowAccount->guid = helper_easysdi::getUniqueId();
		
		if (!$rowAccount->store(false)) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$counter=0;
		foreach( $_POST['address_id'] as $address_id )
		{
			$rowAddress = new address( $database );
			$rowAddress->id=$address_id;
			$rowAddress->account_id=$rowAccount->id;
			$rowAddress->type_id=$_POST['type_id'][$counter];
			
			if ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 2) {
				$index = 0;
			} elseif ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 3) {
				$index = 0;
			} else {
				$index = $counter;
			}

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
	
			// G�n�rer un guid
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			if ($rowAddress->guid == null)
				$rowAddress->guid = helper_easysdi::getUniqueId();
			
			if (!$rowAddress->store()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}

			$database->setQuery( "UPDATE #__sdi_address SET updated=now() WHERE id IN (".$rowAddress->id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}

			$counter++;
		}


		$database->setQuery( "DELETE FROM #__sdi_actor WHERE account_id IN (".$rowAccount->id.")");
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
				
		foreach( $_POST['role_id'] as $role_id )
		{
			$database->setQuery( "INSERT INTO #__sdi_actor (guid, role_id, account_id, updated) VALUES ('".helper_easysdi::getUniqueId()."', ".$role_id.",".$rowAccount->id.",now())" );
				if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}

		}
		
		//Save profile selection
		$database->setQuery( "DELETE FROM #__sdi_account_accountprofile WHERE account_id IN (".$rowAccount->id.")");
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";	
			exit();
		}
		$profile_id_list ="";
		if(isset($_POST['profile_id']))
		{
			if (count ($_POST['profile_id'] )>0)
			{
				foreach( $_POST['profile_id'] as $profile_id )
				{
					$database->setQuery( "INSERT INTO #__sdi_account_accountprofile (accountprofile_id, account_id) VALUES (".$profile_id.",".$rowAccount->id.")" );
					if (!$database->query()) 
					{
						echo "<div class='alert'>";
						echo $database->getErrorMsg();
						echo "</div>";
						exit();
					}
					$profile_id_list .= $profile_id;
					$profile_id_list .= ",";
				}
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
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit();
			}
		}
		
		/* Fin de la reverification */
		
		$query = "UPDATE #__sdi_account SET updated=now()";
		$query .= " WHERE id IN (".$rowAccount->id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		SITE_account::includeAccountExtension(0,'BOTTOM','saveAccount',$rowAccount->id);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
	}
	

}

?>
