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

		$database->setQuery( "SELECT code FROM #__easysdi_accountextension WHERE accounttab_id = ".$tab_id." AND tablocation_id = '".$tab_location."' AND action = '".$action."' ORDER BY ordering" );
		$rows = $database->loadObjectList() ;

		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			eval ($row->code);
		}
	}

	function listAccount() {
		global  $mainframe;

		/**
		 * Allow Pathway with mod_menu_easysdi
		 */
		// Get the menu item object
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		$params = &$mainframe->getParams();
		//Handle the breadcrumbs
		if(!$menu)
		{
			$params->set('page_title',	JText::_("EASYSDI_MENU_ITEM_MYAFFILIATES"));
			//Add item in pathway
			$breadcrumbs = & $mainframe->getPathWay();
			$breadcrumbs->addItem( JText::_("EASYSDI_MENU_ITEM_MYAFFILIATES"), '' );
			$document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
		}
		/**/

		$user = JFactory::getUser();
		if(!userManager::isUserAllowed($user,"ACCOUNT"))
		{
			return;
		}
		/*if ($user->guest){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
			return;
			}
			if(!usermanager::isEasySDIUser($user))
			{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER"),"INFO");
			return;
			}*/

			
		$option=JRequest::getVar("option");

		$db =& JFactory::getDBO();

		$limit = JRequest::getVar('limit', 5 );
		$limitstart = JRequest::getVar('limitstart', 0 );


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

		$rowAccount = new accountByUserId( $db );
		$rowAccount->load( $user->id );

		$type = JRequest::getVar("type",$rowAccount->id);
		if (!$type){
			$type=$rowAccount->id;
		}

		/*if ($hasTheRightToManageAffiliates){*/
		$query = "SELECT COUNT(*) FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$type." AND #__sdi_account.id <> $type";
			

		$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();

		$pageNav = new JPagination($total,$limitstart,$limit);
			
		$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.* FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.parent_id = ".$type." AND #__sdi_account.id <> $type ";
		$query .= $filter;
		$query .= " ORDER BY #__users.name";


		$db->setQuery( $query ,$limitstart,$limit);
		$rows = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$db->getErrorMsg();
			echo "</div>";
		}

		$types = array();
		$types[] = JHTML::_('select.option','',JText::_("EASYSDI_LIST_ACCOUNT_ROOT") );

		if ($type==''){
			$db->setQuery( "SELECT #__sdi_account.id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NULL ORDER BY #__users.name" );
		}else{
			$db->setQuery( "SELECT #__sdi_account.id AS value,#__users.name AS text FROM #__users, #__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id ='$type'" );
			$types = array_merge( $types, $db->loadObjectList() );
			$db->setQuery( "SELECT #__sdi_account.id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users, #__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NOT NULL AND (#__sdi_account.parent_id = '$type' ) ORDER BY #__users.name" );
		}

		$types = array_merge( $types, $db->loadObjectList() );

		HTML_account::listAccount( $rows, $pageNav, $search, $option, $rowAccount->id,$types,$type);
		/*}else{
		 echo "<div class='alert'>";
		 echo JText::_("EASYSDI_NOT ALLOWED TO EDIT AFFILIATES");
		 echo "</div>";
			}*/
		//}
	}


	function editAccount( ) {

		global  $mainframe;
		$option = JRequest::getVar("option");
		$user = JFactory::getUser();

		if(!usermanager::isUserAllowed($user,"MYACCOUNT"))
		{
			return;
		}
		/*if ($user->guest){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_NOT_CONNECTED"),"INFO");
			return;
			}
			if(!usermanager::isEasySDIUser($user))
			{
			$mainframe->enqueueMessage(JText::_("EASYSDI_NOT_CONNECTED_AS_EASYSDI_USER"),"INFO");
			return;
			}*/

		$database =& JFactory::getDBO();
		$rowAccount = new accountByUserId( $database );
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

	function showAccount( ) {
		global  $mainframe;
		$user = JFactory::getUser();

		/**
		 * Allow Pathway with mod_menu_easysdi
		 */
		// Get the menu item object
		$menus = &JSite::getMenu();
		$menu  = $menus->getActive();
		$params = &$mainframe->getParams();
		//Handle the breadcrumbs
		if(!$menu)
		{
			$params->set('page_title',	JText::_("EASYSDI_MENU_ITEM_MYACCOUNT"));
			//Add item in pathway
			$breadcrumbs = & $mainframe->getPathWay();
			$breadcrumbs->addItem( JText::_("EASYSDI_MENU_ITEM_MYACCOUNT"), '' );
			$document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
		}
		/**/
			
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
		$rowAccount = new accountByUserId( $database );
		$rowAccount->load( $user->id );

			
		//Has the user the right to edit the account
		$query = "SELECT count(*)
		FROM #__sdi_actor as a ,
		#__sdi_role as b
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
		#__sdi_role as b
		where a.role_id = b.id
		and code = 'ACCOUNT'
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
		$rowAccount = new accountByUserId( $database );

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

		HTML_account::showAccount( $hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates,$rowUser, $rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option );

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

	$rowAccount = new accountByUserId( $database );
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

	$rowUser =&	 new JTableUser($database);
	$rowUser->load( $rowAccount->user_id );

	if ($id == 0)
	{
		$rowAccount->root_id=JRequest::getVar('type','');
		$rowAccount->parent_id=JRequest::getVar('type','');
		$rowUser->usertype='Registered';
		$rowUser->gid=18;
	}

	HTML_account::showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, $rowUser, $rowAccount, $rowContact, $option );

	}

	function createUser()
	{
		global $mainframe;
		//$user     =& JFactory::getUser();
		/*	$params = &$mainframe->getParams();
		 $menus	= &JSite::getMenu();
		 $menu	= $menus->getActive();

		 // because the application sets a default page title, we need to get it
		 // right from the menu item itself
		 if (is_object( $menu )) {
			$menu_params = new JParameter( $menu->params );
			if (!$menu_params->get( 'page_title')) {
			$params->set('page_title',	JText::_("EASYSDI_PATHWAY_REGISTRATION"));
			}
			} else {
			$params->set('page_title',	JText::_("EASYSDI_PATHWAY_REGISTRATION"));
			//Add item in pathway
			$breadcrumbs = & $mainframe->getPathWay();
			$breadcrumbs->addItem( JText::_("EASYSDI_PATHWAY_REGISTRATION"), '' );
			$document	= &JFactory::getDocument();
			$document->setTitle( $params->get( 'page_title' ) );
			}*/



		/**
		 * Manage Pathway
		 */
		// Get the menu item object
		/*$menus = &JSite::getMenu();
		 $menu  = $menus->getActive();
		 //Handle the breadcrumbs
		 if(!$menu)
		 {
			//Add item in pathway
			$breadcrumbs = & $mainframe->getPathWay();
			$breadcrumbs->addItem( JText::_("EASYSDI_PATHWAY_REGISTRATION"), '' );
			}*/
		/**/

		$option = JRequest::getVar("option");
		HTML_account::createUser( $option );

	}
	// Cr�ation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editAffiliateAccount($affiliate_id = null ) {


		$user = JFactory::getUser();

		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}

		$option = JRequest::getVar("option");
		$database =& JFactory::getDBO();
			
		if (!is_null($affiliate_id)){

			$rowAccount = new accountByUserId( $database );

			if ($affiliate_id!=0){
				$rowAccount->load( $affiliate_id);
			}
			if ($rowAccount->user_id != $user->id ){
				$rowRootAccount = new accountByUserId( $database );
				$rowRootAccount->load( $user->id);
			}
			else
			{
			}
		}
		else
		{
			$rowAccount = new accountByUserId( $database );
			$rowAccount->load( $user->id );
		}

		if ($affiliate_id!=0){
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

			$parentAccount = new accountByAccountId( $database );
			$parentAccount ->load($parent_id );
			if ($parentAccount->root_id){
				$rowAccount->root_id=$parentAccount->root_id;
			}else{
				$rowAccount->root_id=$parentAccount->id;
			}
			$rowAccount->parent_id=$parentAccount->id;
				

			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}
			
		HTML_account::editAffiliateAccount( $rowUser, $rowAccount, $rowContact, $option );
	}

	function removeAccount( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();

		$user = JFactory::getUser();
		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"ERROR");
			exit;
		}
		foreach( $cid as $account_id )
		{
			$account = new accountByUserId( $database );
			$account->load( $account_id );

			$user =&	 new JTableUser($database);
			$user->load( $account->user_id );

			if (!$account->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
			}
			if (!$user->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listAccount" );
			}

			ADMIN_account::includeAccountExtension(0,'BOTTOM','removeAccount',$account_id);


		}

		$mainframe->redirect("index.php?option=$option&task=listAccount" );
	}



	function saveAccount(  ) {
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
		if (JRequest::getVar('old_password','') != $rowUser->password)
		{
			$rowUser->password = md5( JRequest::getVar('password','') );
		}
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

		$rowAccount = new accountByAccountId ( $database );
		if (!$rowAccount->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$rowAccount->user_id=$rowUser->id;
		if ($rowAccount->code == null)
		{
			$rowAccount->code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}

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
			$rowAddress->country_id=$_POST['country_code'][$index];
			$rowAddress->corporatename1=$_POST['address_corporate_name1'][$index];
			$rowAddress->corporatename2=$_POST['address_corporate_name2'][$index];
			$rowAddress->agentfirstname=$_POST['address_agent_firstname'][$index];
			$rowAddress->agentlastname=$_POST['address_agent_lastname'][$index];
			$rowAddress->function=$_POST['address_agent_function'][$index];
			$rowAddress->street1=$_POST['address_street1'][$index];
			$rowAddress->street2=$_POST['address_street2'][$index];
			$rowAddress->postalcode=$_POST['address_postalcode'][$index];
			$rowAddress->locality=$_POST['address_locality'][$index];
			$rowAddress->phone=$_POST['address_phone'][$index];
			$rowAddress->fax=$_POST['address_fax'][$index];
			$rowAddress->email=$_POST['address_email'][$index];

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
			
		$mainframe->redirect("index.php?option=$option&task=".JRequest::getVar('return','showAccount') );
	}

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

		$rowUser->password = md5( JRequest::getVar('password','') );

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

		$rowAccount = new accountByAccountId ( $database );
		if (!$rowAccount->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$rowAccount->user_id=$rowUser->id;
		if ($rowAccount->code == null)
		{
			$rowAccount->code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}
		
		if (!$rowAccount->store(false)) {
				
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
			$rowAddress->title_id=$_POST['title_id'];
			$rowAddress->country_id=$_POST['country_code'];
			$rowAddress->corporatename1=$_POST['address_corporate_name1'];
			$rowAddress->corporatename2=$_POST['address_corporate_name2'];
			$rowAddress->agentfirstname=$_POST['address_agent_firstname'];
			$rowAddress->agentlastname=$_POST['address_agent_lastname'];
			$rowAddress->function=$_POST['address_agent_function'];
			$rowAddress->street1=$_POST['address_street1'];
			$rowAddress->street2=$_POST['address_street2'];
			$rowAddress->postalcode=$_POST['address_postalcode'];
			$rowAddress->locality=$_POST['address_locality'];
			$rowAddress->phone=$_POST['address_phone'];
			$rowAddress->fax=$_POST['address_fax'];
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

		SITE_account::includeAccountExtension(0,'BOTTOM','registerAccount',$rowAccount->id);

		//Send email notification to administrator
		$query = "SELECT count(*) FROM #__users WHERE (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator')";
		$database->setQuery( $query );
		$total = $database->loadResult();
		if($total >0)
		{
			$query = "SELECT * FROM #__users WHERE  (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator')";
			$database->setQuery( $query );

			$rows = $database->loadObjectList();
			$mailer =& JFactory::getMailer();

			SITE_account::sendMail($rows,JText::_("EASYSDI_NEW_USER_MAIL_SUBJECT"),JText::sprintf("EASYSDI_NEW_USER_MAIL_BODY",$rowUser->username));
		}
		//Send email notification to user
		$query = "SELECT * FROM #__users ";
		$query .= " WHERE id= ".$rowUser->id;
		$database->setQuery( $query );
		$row = $database->loadObjectList();
		//$mailer =& JFactory::getMailer();
		SITE_account::sendMail($row,JText::_("EASYSDI_NEW_USER_MAIL_NOTIFICATION_SUBJECT"),JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY").JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY1",$rowUser->username).JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY2",JRequest::getVar('password','')).JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY3"));

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
		$mailer ->IsHTML(true);
		foreach ($rows as $row){
			$mailer->addRecipient($row->email);
		}

		$mailer->setSubject($subject);
		$user = JFactory::getUser();
		$mailer->setBody($body);

		if ($mailer->send() !==true){
				
		}
	}


	function saveAffiliateAccount(  ) {
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
		if (JRequest::getVar('old_password','') != $rowUser->password)
		{
			$rowUser->password = md5( JRequest::getVar('password','') );
		}
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

		$rowAccount = new accountByAccountId ( $database );
		if (!$rowAccount->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$rowAccount->user_id=$rowUser->id;
		if ($rowAccount->code == null)
		{
			$rowAccount->code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}

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
			$rowAddress->country_id=$_POST['country_code'][$index];
			$rowAddress->corporatename1=$_POST['address_corporate_name1'][$index];
			$rowAddress->corporatename2=$_POST['address_corporate_name2'][$index];
			$rowAddress->agentfirstname=$_POST['address_agent_firstname'][$index];
			$rowAddress->agentlastname=$_POST['address_agent_lastname'][$index];
			$rowAddress->function=$_POST['address_agent_function'][$index];
			$rowAddress->street1=$_POST['address_street1'][$index];
			$rowAddress->street2=$_POST['address_street2'][$index];
			$rowAddress->postalcode=$_POST['address_postalcode'][$index];
			$rowAddress->locality=$_POST['address_locality'][$index];
			$rowAddress->phone=$_POST['address_phone'][$index];
			$rowAddress->fax=$_POST['address_fax'][$index];
			$rowAddress->email=$_POST['address_email'][$index];

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
			$database->setQuery( "INSERT INTO #__sdi_actor (role_id, account_id, actor_update) VALUES (".$role_id.",".$rowAccount->id.",now())" );
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
			
		$mainframe->redirect("index.php?option=$option&task=".JRequest::getVar('return','showAccount') );
	}

	function cancelAccount( $returnList, $option ) {
		global $mainframe;
		ADMIN_account::includeAccountExtension(0,'TOP','cancelAccount',0);
		$database =& JFactory::getDBO();
		$row = new accountByUserId( $database );
		$row->bind( $_POST );
		$row->checkin();
		ADMIN_account::includeAccountExtension(0,'BOTTOM','cancelAccount',0);
		if ($returnList == true) {
			$mainframe->redirect("index.php?option=$option&task=listAccount" );
		}


	}

}

?>
