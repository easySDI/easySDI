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

	function createBlockUser(){
		
		global $mainframe;

		$option = JRequest::getVar("option");

		$database=& JFactory::getDBO();

		$rowUser =&	 new JTableUser($database);

		if (!$rowUser->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		//User registered in the front-end need administrator validation
		//New status --> jos_users.block=1
		$rowUser->block=1;
		
		//Check if username already exit cause in jos_user name isn't unique but should be...
		$database->setQuery("SELECT count(*) FROM #__users WHERE username = '".$rowUser->username."'");
		$total = $database->loadResult();
		if($total > 0){
			echo "<div class='alert'>";
			echo JText::_("CORE_ACCOUNT_ALREADY_EXISTS");
			echo "</div>";
			exit;
		}
		
		//CSheck if email is unique also because it must be (pass recovery!).
		$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$rowUser->email."'");
		$total = $database->loadResult();
		if($total > 0){
			echo "<div class='alert'>";
			echo JText::_("CORE_EMAIL_ALREADY_REGISTRED");
			echo "</div>";
			exit;
		}
		
		$salt = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword(JRequest::getVar('password',''), $salt);
		$rowUser->password = $crypt . ':' . $salt;

		$rowUser->usertype='Registered';
		$rowUser->gid=18;
		
		
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
		
		$rowAccount = new account($database);
		if (!$rowAccount->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		$rowAccount->user_id=$rowUser->id;
		$rowAccount->guid=uniqid();
		
		$query = "SELECT id FROM #__users WHERE usertype='Super Administrator'";
		$database->setQuery( $query );
		$admin_id = $database->loadResult();
		$rowAccount->createdby = $admin_id;
		
		if (!$rowAccount->store()) {
				
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		for($i=1 ; $i<4 ; $i++)
		{
			$rowAddress = new address( $database );
			//$rowAddress->address_id=$address_id;
			$rowAddress->account_id=$rowAccount->id;
			$rowAddress->title_id=$_POST['title_id'][0];
			
			//select country id from code			
			$query = "SELECT id FROM #__sdi_list_country WHERE code='".$_POST['country_id'][0]."'";
			$database->setQuery($query);
			$cc = $database->loadResult();
			$rowAddress->country_id = $cc;
			$rowAddress->corporatename1=$_POST['corporatename1'][0];
			$rowAddress->corporatename2=$_POST['corporatename2'][0];
			$rowAddress->agentfirstname=$_POST['agentfirstname'][0];
			$rowAddress->agentlastname=$_POST['agentlastname'][0];
			$rowAddress->function=$_POST['function'][0];
			$rowAddress->street1=$_POST['street1'][0];
			$rowAddress->street2=$_POST['street2'][0];
			$rowAddress->postalcode=$_POST['postalcode'][0];
			$rowAddress->locality=$_POST['locality'][0];
			$rowAddress->phone=$_POST['phone'][0];
			$rowAddress->fax=$_POST['fax'][0];
			$rowAddress->email=$rowUser->email;
			$rowAddress->type_id=$i;

			if (!$rowAddress->store())
			{
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

		//SITE_partner::includePartnerExtension(0,'BOTTOM','registerPartner',$rowAccount->partner_id);
		
		
		//Send email notification to administrator
		$query = "SELECT count(*) FROM #__users WHERE (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator')";
		$database->setQuery( $query );
		$total = $database->loadResult();
		if($total >0)
		{
			$query = "SELECT * FROM #__users WHERE  (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator')";
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			
			/* Hack ASITVD */
			//$query = "SELECT profile_name FROM #__asitvd_community_profile WHERE  profile_id=".$_POST['profile_id'];
			//$database->setQuery( $query );
			//$prf = $database->loadResult();
						
			$query = "SELECT name FROM #__sdi_title WHERE id=".$rowAddress->title_id;
			$database->setQuery( $query );
			$title = $database->loadResult();
			
			$query = "SELECT country_name FROM #__sdi_list_country WHERE id='".$rowAddress->country_id."'";
			$database->setQuery( $query );
			$country = $database->loadResult();
			
			$mailer =& JFactory::getMailer();
			$body = JText::sprintf("CORE_NEW_USER_MAIL_BODY",
				//JText::_($prf), $rowUser->username, $rowUser->name, JText::_($title), $rowAddress->address_agent_lastname, $rowAddress->address_agent_firstname,
				"profile todo", $rowUser->username, $rowUser->name, JText::_($title), $rowAddress->agentlastname, $rowAddress->agentfirstname,
				$rowAddress->function, $rowAddress->street1, $rowAddress->street2, $rowAddress->postalcode,
				$rowAddress->locality, $country, $rowAddress->phone, $rowAddress->fax, $rowUser->email);
			
			$body = str_replace("\\t", "\t", $body);

			SITE_account::sendMail($rows,JText::sprintf("CORE_NEW_USER_MAIL_SUBJECT", $rowUser->name, $rowUser->username), $body);
		}
		
		//Send email notification to user
		$query = "SELECT * FROM #__users ";
		$query .= " WHERE id= ".$rowUser->id;
		$database->setQuery( $query );
		$row = $database->loadObjectList();
		//$mailer =& JFactory::getMailer();
		SITE_account::sendMail($row,JText::_("CORE_NEW_USER_MAIL_NOTIFICATION_SUBJECT"),JText::sprintf("CORE_NEW_USER_MAIL_NOTIFICATION_BODY").JText::sprintf("CORE_NEW_USER_MAIL_NOTIFICATION_BODY1",$rowUser->username).JText::sprintf("CORE_NEW_USER_MAIL_NOTIFICATION_BODY2",JRequest::getVar('password','')).JText::sprintf("CORE_NEW_USER_MAIL_NOTIFICATION_BODY3"));
		//redirect
		$url = config_easysdi::getValue("WELCOME_REDIRECT_URL");
		if($url)
		{
			$mainframe->redirect($url);
		}
		else
		{
			$mainframe->redirect('index.php');
		}
	}
	
	function sendMail ($rows,$subject,$body){

		$mailer =& JFactory::getMailer();
		//$mailer ->IsHTML(true);
		foreach ($rows as $row){
			$mailer->addRecipient($row->email);
		}

		$mailer->setSubject($subject);
		$user = JFactory::getUser();
		$mailer->setBody($body);

		if ($mailer->send() !==true){
				
		}
	}
	
	function includeAccountExtension($tab_id,$tab_location,$action,$account_id)
	{
		global $mainframe;
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
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_NOTCONNECTED_MSG"),"INFO");
			return;
		}
		if(!usermanager::isEasySDIUser($user))
		{
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_NOTCONNECTEDASEASYSDIUSER_MSG"),"INFO");
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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
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
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_NOTCONNECTED_MSG"),"INFO");
			return;
		}
		if(!usermanager::isEasySDIUser($user))
		{
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_NOTCONNECTEDASEASYSDIUSER_MSG"),"INFO");
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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
			
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}


		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}

		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}

		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
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
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_NOTCONNECTED_MSG"),"INFO");
			return;
		}
		if(!usermanager::isEasySDIUser($user))
		{
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_NOTCONNECTEDASEASYSDIUSER_MSG"),"INFO");
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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
	
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		
		$rowUser =&	 new JTableUser($database);
		$rowUser->load( $rowAccount->user_id );
	
		if ($id == 0)
		{
			$rowAccount->root_id=JRequest::getVar('type','');
			$rowAccount->parent_id=JRequest::getVar('type','');
			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}
	
		HTML_Account::showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, $rowUser, $rowAccount, $rowContact, $option );
		
	}
	
	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0)
		{
			foreach($rows as $key => $row)
			{		  	
      			$rows[$key]->text = JText::_($rows[$key]->text);
  			}			    
		}
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

			
		$type = JRequest::getVar("type",$rowAccount->id);
		if (!$type){
			$type=$rowAccount->id;
		}

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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
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
	
	function createAccount( ) 
	{
		global $mainframe;
		$option = JRequest::getVar("option");
		$database =& JFactory::getDBO();
		
		$titles = array();
		$titles[] = JHTML::_('select.option','0', JText::_("CORE_CHOOSE" ));
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_title WHERE id > 0 ORDER BY name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($titles );
		
		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("CORE_CHOOSE") );
		$database->setQuery( "SELECT code AS value, name AS text FROM #__sdi_list_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries);
		
		
		HTML_account::createAccount($option, $titles, $countries);
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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
			
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}


		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}

		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$rowAccount->id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}

		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
			
		$rowUser =&	 new JTableUser($database);
		$rowUser->load( $rowAccount->user_id );

		HTML_account::editAccount( $rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option );
	}
	
	// Création d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editRootAccount($id, $option) {
		global $mainframe;
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
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );
		
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );
		
		$database->setQuery( "SELECT id FROM #__sdi_address WHERE account_id=".$id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		
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

	// Création d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editAffiliateAccount( $id) {		
		global $mainframe;
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
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
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
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
		}
		
		$rowsAccountProfile = "";
		if($rowAccount->id)
		{
			//Get user profile
			$database->setQuery( "SELECT accountprofile_id as value FROM #__sdi_account_accountprofile WHERE account_id=".$rowAccount->id );
			$rowsAccountProfile = $database->loadObjectList();
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			}
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
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_DELETE_NOSELECTEDACCOUNT_MSG"),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));
			exit;
		}
		foreach( $cid as $account_id )
		{
			$account = new account( $database );
			$account->load( $account_id );
		
			$user =&	 new JTableUser($database);
			$user->load( $account->user_id );
			
			if (!$account->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));
			}
			
			SITE_account::includeAccountExtension(0,'BOTTOM','removeAccount',$account_id);
			
			
		}

		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount"), false));		
	}

	function exportAccount( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_EXPORT_NOSELECTEDACCOUNT_MSG"),"error");
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
				$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_ALREADYEXISTS_MSG"),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
				exit();
			}
		}
		
		//CSheck if email is unique also because it must be (pass recovery!).
        if (JRequest::getVar('old_email','') != $_POST['user_email']){
			$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$_POST['user_email']."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_ALREADYREGISTRED_MSG"),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
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

			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
			exit;
		}
	
		if (JRequest::getVar('id','') == '')
		{
			$database->setQuery( "UPDATE #__users SET registerDate=now() WHERE id = (".$rowUser->id.")");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
				exit;
			}
		}

		$rowAccount = new account( $database );
		if (!$rowAccount->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
			exit;
		}
		
		$rowAccount->user_id=$rowUser->id;
		$rowAccount->id=$_POST['account_id'];
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowAccount->guid == null)
			$rowAccount->guid = helper_easysdi::getUniqueId();
		
		
		if (!$rowAccount->store(false)) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
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
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
				exit;
			}
			
			$database->setQuery( "UPDATE #__sdi_address SET updated=now() WHERE id IN (".$rowAddress->id.")");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
				exit;
			}

			$counter++;
		}

		$query = "UPDATE #__sdi_account SET updated=now()";
		$query .= " WHERE id IN (".$rowAccount->id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')), false));
			exit;
		}
		
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
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=listAccount&type=$type"), false));
		}
		
		
	}
	
	function checkIsAccountDeletable($affiliate_id){
		global $mainframe;
		$database =& JFactory::getDBO();
		$user = JFactory::getUser();
		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}

		if (!$affiliate_id) {
			$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_SELECT_ROW_TO_DELETE"),"ERROR");
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
				array_push($errMsg,JText::_("CORE_ACCOUNT_DELETE_AFFILIATE_ERROR_CONCLUSION"));
			
			array_push($errMsg, JText::sprintf("CORE_ACCOUNT_DELETE_AFFILIATE_ERROR_PRODUCT",$user->username, $list));
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
				array_push($errMsg,JText::_("CORE_DELETE_AFFILIATE_ERROR_CONCLUSION"));
				
			array_push($errMsg, JText::sprintf("CORE_DELETE_AFFILIATE_ERROR_ACCOUNT",$user->username, $list));
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
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
			exit;
		}
		
		//Check if username already exit cause in jos_user name isn't unique but should be...
		if (JRequest::getVar('old_username','') != $rowUser->username){
			$database->setQuery("SELECT count(*) FROM #__users WHERE username = '".$rowUser->username."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("CORE_ACCOUNT_ALREADYEXISTS_MSG"),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
			}
		}
		
		
		//CSheck if email is unique also because it must be (pass recovery!).
        if (JRequest::getVar('old_email','') != $rowUser->email){
			$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$rowUser->email."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("CORE_EMAIL_ALREADYREGISTRED_MSG"),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
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
		
		if (!$rowUser->store()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
			exit;
		}

		if (JRequest::getVar('id','') == '')
		{
			$database->setQuery( "UPDATE #__users SET registerDate=now() WHERE id = (".$rowUser->id.")");
			if (!$database->query()) 
			{
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
			}
		}

		$rowAccount = new account( $database );
		if (!$rowAccount->bind( $_POST )) 
		{			
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
			exit;
		}
		
		$rowAccount->user_id=$rowUser->id;
		$rowAccount->id=$_POST['account_id'];
		
		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowAccount->guid == null)
			$rowAccount->guid = helper_easysdi::getUniqueId();
		
		if (!$rowAccount->store(false)) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
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
			print_r($rowAddress);
	
			// G�n�rer un guid
			require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
			if ($rowAddress->guid == null)
				$rowAddress->guid = helper_easysdi::getUniqueId();
			
			if (!$rowAddress->store()) 
			{
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
			}

			$database->setQuery( "UPDATE #__sdi_address SET updated=now() WHERE id IN (".$rowAddress->id.")");
			if (!$database->query()) 
			{
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
			}

			$counter++;
		}


		$database->setQuery( "DELETE FROM #__sdi_actor WHERE account_id IN (".$rowAccount->id.")");
		if (!$database->query()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
			exit;
		}
				
		foreach( $_POST['role_id'] as $role_id )
		{
			$database->setQuery( "INSERT INTO #__sdi_actor (guid, role_id, account_id, updated) VALUES ('".helper_easysdi::getUniqueId()."', ".$role_id.",".$rowAccount->id.",now())" );
			if (!$database->query()) 
			{
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
			}

		}
		
		//Save profile selection
		$database->setQuery( "DELETE FROM #__sdi_account_accountprofile WHERE account_id IN (".$rowAccount->id.")");
		if (!$database->query()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
			exit;
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
						$mainframe->enqueueMessage($database->getErrorMsg(),"error");
						$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
						exit;
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
			if (!$database->query()) 
			{
				$mainframe->enqueueMessage($database->getErrorMsg(),"error");
				$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
				exit;
			}
		}
		
		/* Fin de la reverification */
		
		$query = "UPDATE #__sdi_account SET updated=now()";
		$query .= " WHERE id IN (".$rowAccount->id.")";
		$database->setQuery( $query );
		if (!$database->query()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"error");
			$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
			exit;
		}
		
		SITE_account::includeAccountExtension(0,'BOTTOM','saveAccount',$rowAccount->id);
		$mainframe->redirect(JRoute::_(displayManager::buildUrl("index.php?option=$option&task=".JRequest::getVar('return','showAccount')."&type=".JRequest::getVar('type')), false));
	}
	

}

?>
