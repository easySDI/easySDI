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

/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/

defined('_JEXEC') or die('Restricted access');


class SITE_partner {

	function includePartnerExtension($tab_id,$tab_location,$action,$partner_id)
	{
		$database =& JFactory::getDBO();

		$database->setQuery( "SELECT code FROM #__easysdi_partner_extension WHERE tab_id = ".$tab_id." AND tab_location = '".$tab_location."' AND action = '".$action."' ORDER BY order_number" );
		$rows = $database->loadObjectList() ;

		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			eval ($row->code);
		}
	}

	function listPartner() {
		global  $mainframe;

		/**
		 * Allow Pathway with mod_menu_easysdi
		 */
		 // Get the menu item object
        $menus = &JSite::getMenu();
        $menu  = $menus->getActive();
 		 //Handle the breadcrumbs
        if(!$menu)
        {
			//Add item in pathway		
			$breadcrumbs = & $mainframe->getPathWay();
		    $breadcrumbs->addItem( JText::_("EASYSDI_MENU_ITEM_MYAFFILIATES"), '' );
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
				$filter .= " OR #__easysdi_community_partner.partner_acronym LIKE '%$search%'";
				$filter .= " OR #__easysdi_community_partner.partner_id LIKE '%$search%'";
				$filter .= " OR #__easysdi_community_partner.partner_code LIKE '%$search%')";
			}

			$rowPartner = new partnerByUserId( $db );
			$rowPartner->load( $user->id );

			
			//Has the user the right to manage the affiliate
			/*$query = "SELECT count(*) FROM #__easysdi_community_actor as a ,#__easysdi_community_role as b where a.role_id = b.role_id and role_code = 'ACCOUNT'  and partner_id = $rowPartner->partner_id";

			$db->setQuery($query );
			$hasTheRightToManageAffiliates = $db->loadResult();
			if ($db->getErrorNum()) {
				echo "<div class='alert'>";
				echo 			$db->getErrorMsg();
				echo "</div>";
				$hasTheRightToManageAffiliates=0;
			}
*/
			$type = JRequest::getVar("type",$rowPartner->partner_id);
			if (!$type){
				$type=$rowPartner->partner_id;
			}

			/*if ($hasTheRightToManageAffiliates){*/
				$query = "SELECT COUNT(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.parent_id = ".$type." AND #__easysdi_community_partner.partner_id <> $type";
					

				$query .= $filter;
				$db->setQuery( $query );
				$total = $db->loadResult();

				$pageNav = new JPagination($total,$limitstart,$limit);
					
				$query = "SELECT #__users.name as partner_name,#__users.username as partner_username,#__easysdi_community_partner.* FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.parent_id = ".$type." AND #__easysdi_community_partner.partner_id <> $type ";
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
					$db->setQuery( "SELECT #__easysdi_community_partner.partner_id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id IS NULL ORDER BY #__users.name" );
				}else{
					$db->setQuery( "SELECT #__easysdi_community_partner.partner_id AS value,#__users.name AS text FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.partner_id ='$type'" );
					$types = array_merge( $types, $db->loadObjectList() );
					$db->setQuery( "SELECT #__easysdi_community_partner.partner_id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id IS NOT NULL AND (#__easysdi_community_partner.parent_id = '$type' ) ORDER BY #__users.name" );
				}

				$types = array_merge( $types, $db->loadObjectList() );

				HTML_partner::listPartner( $rows, $pageNav, $search, $option, $rowPartner->partner_id,$types,$type);
			/*}else{
				echo "<div class='alert'>";
				echo JText::_("EASYSDI_NOT ALLOWED TO EDIT AFFILIATES");
				echo "</div>";
			}*/
		//}
	}


	function editPartner( ) {

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
		$rowPartner = new partnerByUserId( $database );
		$rowPartner->load( $user->id );

		/*if ($rowPartner->partner_entry != null && $rowPartner->partner_entry != '0000-00-00') {
			$rowPartner->partner_entry = date('d.m.Y H:i:s',strtotime($rowPartner->partner_entry));
		} else {
			$rowPartner->partner_entry = null;
		}
		if ($rowPartner->partner_exit != null && $rowPartner->partner_exit != '0000-00-00')	{
			$rowPartner->partner_exit = date('d.m.Y H:i:s',strtotime($rowPartner->partner_exit));
		} else {
			$rowPartner->partner_exit = null;
		}*/

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=1" );
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


		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=3" );
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
		$rowUser->load( $rowPartner->user_id );

		HTML_partner::editPartner( $rowUser, $rowPartner, $rowContact, $rowSubscription, $rowDelivery ,$option );
		
	}

	function showPartner( ) {
		global  $mainframe;
		$user = JFactory::getUser();
		
		/**
		 * Allow Pathway with mod_menu_easysdi
		 */
		 // Get the menu item object
        $menus = &JSite::getMenu();
        $menu  = $menus->getActive();
 		 //Handle the breadcrumbs
        if(!$menu)
        {
			//Add item in pathway		
			$breadcrumbs = & $mainframe->getPathWay();
		    $breadcrumbs->addItem( JText::_("EASYSDI_MENU_ITEM_MYACCOUNT"), '' );
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
		$rowPartner = new partnerByUserId( $database );
		$rowPartner->load( $user->id );
						
			
		//Has the user the right to edit the account
		$query = "SELECT count(*) 
					FROM #__easysdi_community_actor as a ,
					#__easysdi_community_role as b 
					where a.role_id = b.role_id 
					and role_code = 'MYACCOUNT'  
					and partner_id = $rowPartner->partner_id";
		$database->setQuery($query );
		$hasTheRightToEdit = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
			$hasTheRightToEdit=0;
		}
			
		$query = "SELECT count(*) 
						FROM #__easysdi_community_actor as a ,
							 #__easysdi_community_role as b 
					    where a.role_id = b.role_id 
					    	  and role_code = 'ACCOUNT'  
					    	  and partner_id = $rowPartner->partner_id";
		$database->setQuery($query );
		$hasTheRightToManageHisOwnAffiliates = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
			$hasTheRightToManageHisOwnAffiliates=0;
		}
			
		if (is_null($rowPartner->root_id)){
			SITE_partner::showRootPartner($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates);
		}else{
			SITE_partner::showAffiliatePartner($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates);
		}
	}

	function showRootPartner($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates) {
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
		$rowPartner = new partnerByUserId( $database );

		$rowPartner->load( $user->id );
		/*if ($rowPartner->partner_entry != null && $rowPartner->partner_entry != '0000-00-00') {
			$rowPartner->partner_entry = date('d.m.Y H:i:s',strtotime($rowPartner->partner_entry));
		} else {
			$rowPartner->partner_entry = null;
		}
		if ($rowPartner->partner_exit != null && $rowPartner->partner_exit != '0000-00-00')	{
			$rowPartner->partner_exit = date('d.m.Y H:i:s',strtotime($rowPartner->partner_exit));
		} else {
			$rowPartner->partner_exit = null;
		}*/

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=1" );
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


		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		if ($database->getErrorNum()) {
			echo "<div class='alert'>";
			echo 			$database->getErrorMsg();
			echo "</div>";
		}

		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=3" );
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
		$rowUser->load( $rowPartner->user_id );

		HTML_partner::showPartner( $hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates,$rowUser, $rowPartner, $rowContact, $rowSubscription, $rowDelivery ,$option );
		
	}


	function showAffiliatePartner($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates) 
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

		$rowPartner = new partnerByUserId( $database );
		$rowPartner->load( $user->id );

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=1" );

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
		$rowUser->load( $rowPartner->user_id );

		if ($id == 0)
		{
			$rowPartner->root_id=JRequest::getVar('type','');
			$rowPartner->parent_id=JRequest::getVar('type','');
			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}

		HTML_partner::showAffiliatePartner($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, $rowUser, $rowPartner, $rowContact, $option );
		
	}
	
	function createUser()
	{		
		$option = JRequest::getVar("option");
		HTML_partner::createUser( $option );
		
	}
	// Cr�ation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editAffiliatePartner($affiliate_id = null ) {


		$user = JFactory::getUser();
		
		if(!usermanager::isUserAllowed($user, "ACCOUNT"))
		{
			return;
		}	
		
		$option = JRequest::getVar("option");
		$database =& JFactory::getDBO();
			
		if (!is_null($affiliate_id)){
				
			$rowPartner = new partnerByUserId( $database );
				
			if ($affiliate_id!=0){
				$rowPartner->load( $affiliate_id);
			}				
			if ($rowPartner->user_id != $user->id ){
				$rowRootPartner = new partnerByUserId( $database );
				$rowRootPartner ->load( $user->id);
			}
			else
			{	
			}
		}
		else
		{				
			$rowPartner = new partnerByUserId( $database );
			$rowPartner->load( $user->id );
		}
		
		if ($affiliate_id!=0){
			$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$rowPartner->partner_id." AND type_id=1" );
			$contact_id = $database->loadResult();
			if ($database->getErrorNum()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
			}
			$rowContact = new address( $database );
			$rowContact->load( $contact_id );
			$rowUser =&	 new JTableUser($database);
			$rowUser->load( $rowPartner->user_id );

		}
		else {
			// new Affiliate
			$rootPartner = new partnerByUserId( $database );
			$rootPartner ->load($user->id);

				
			$parent_id = JRequest::getVar("type",$rootPartner ->partner_id);

			$parentPartner = new partnerByPartnerId( $database );
			$parentPartner ->load($parent_id );
			if ($parentPartner->root_id){
				$rowPartner->root_id=$parentPartner->root_id;
			}else{
				$rowPartner->root_id=$parentPartner->partner_id;
			}
			$rowPartner->parent_id=$parentPartner->partner_id;
			

			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}
			
		HTML_partner::editAffiliatePartner( $rowUser, $rowPartner, $rowContact, $option );	
	}

	function removePartner( $cid, $option ) {
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
		foreach( $cid as $partner_id )
		{
			$partner = new partnerByUserId( $database );
			$partner->load( $partner_id );

			$user =&	 new JTableUser($database);
			$user->load( $partner->user_id );
			//$user = new mosUser( $database );
			//$user->load( $partner->user_id );
			if (!$partner->delete()) {
				//echo "<script> alert('".$partner->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
			}
			if (!$user->delete()) {
				//echo "<script> alert('".$user->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
			}
				
			ADMIN_partner::includePartnerExtension(0,'BOTTOM','removePartner',$partner_id);
				
				
		}

		$mainframe->redirect("index.php?option=$option&task=listPartner" );
	}



	function savePartner(  ) {
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

		$rowPartner = new partnerByPartnerId ( $database );
		if (!$rowPartner->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$rowPartner->user_id=$rowUser->id;
		if ($rowPartner->partner_code == null)
		{
			$rowPartner->partner_code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}

		if (!$rowPartner->store(false)) {
				
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$counter=0;
		foreach( $_POST['address_id'] as $address_id )
		{
			/*$rowAddress = new address( $database );
			$rowAddress->address_id=$address_id;
			$rowAddress->partner_id=$rowPartner->partner_id;
			$rowAddress->type_id=$_POST['type_id'][$counter];
			if ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 2) {
				$index = 0;
			} elseif ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 3) {
				$index = 0;
			} else {
				$index = $counter;
			}

			$rowAddress->title_id=$_POST['title_id'][$index];
			$rowAddress->country_code=$_POST['country_code'][$index];
			$rowAddress->address_corporate_name1=$_POST['address_corporate_name1'][$index];
			$rowAddress->address_corporate_name2=$_POST['address_corporate_name2'][$index];
			$rowAddress->address_agent_firstname=$_POST['address_agent_firstname'][$index];
			$rowAddress->address_agent_lastname=$_POST['address_agent_lastname'][$index];
			$rowAddress->address_agent_function=$_POST['address_agent_function'][$index];
			$rowAddress->address_street1=$_POST['address_street1'][$index];
			$rowAddress->address_street2=$_POST['address_street2'][$index];
			$rowAddress->address_postalcode=$_POST['address_postalcode'][$index];
			$rowAddress->address_locality=$_POST['address_locality'][$index];
			$rowAddress->address_phone=$_POST['address_phone'][$index];
			$rowAddress->address_fax=$_POST['address_fax'][$index];
			$rowAddress->address_email=$_POST['address_email'][$index];
*/
			$rowAddress = new address( $database );
			$rowAddress->address_id=$address_id;
			$rowAddress->partner_id=$rowPartner->partner_id;
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
			$rowAddress->country_code=$_POST['country_code'][$index];
			$rowAddress->address_corporate_name1=$_POST['address_corporate_name1'][$index];
			$rowAddress->address_corporate_name2=$_POST['address_corporate_name2'][$index];
			$rowAddress->address_agent_firstname=$_POST['address_agent_firstname'][$index];
			$rowAddress->address_agent_lastname=$_POST['address_agent_lastname'][$index];
			$rowAddress->address_agent_function=$_POST['address_agent_function'][$index];
			$rowAddress->address_street1=$_POST['address_street1'][$index];
			$rowAddress->address_street2=$_POST['address_street2'][$index];
			$rowAddress->address_postalcode=$_POST['address_postalcode'][$index];
			$rowAddress->address_locality=$_POST['address_locality'][$index];
			$rowAddress->address_phone=$_POST['address_phone'][$index];
			$rowAddress->address_fax=$_POST['address_fax'][$index];
			$rowAddress->address_email=$_POST['address_email'][$index];
	
			if (!$rowAddress->store()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
				
			$database->setQuery( "UPDATE #__easysdi_community_address SET address_update=now() WHERE address_id IN (".$rowAddress->address_id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}

			$counter++;
		}



		/*
		 $database->setQuery( "DELETE FROM #__easysdi_community_actor WHERE partner_id IN (".$rowPartner->partner_id.")");
		 if (!$database->query()) {
		 echo "<div class='alert'>";
		 echo $database->getErrorMsg();
		 echo "</div>";
		 exit;
		 }

		 foreach( $_POST['role_id'] as $role_id )
		 {
			$database->setQuery( "INSERT INTO #__easysdi_community_actor (role_id, partner_id, actor_update) VALUES (".$role_id.",".$rowPartner->partner_id.",now())" );
			if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
			}
				
			}*/




		$query = "UPDATE #__easysdi_community_partner SET partner_update=now()";
		$query .= " WHERE partner_id IN (".$rowPartner->partner_id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
			
		$mainframe->redirect("index.php?option=$option&task=".JRequest::getVar('return','showPartner') );
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

		$rowPartner = new partnerByPartnerId ( $database );
		if (!$rowPartner->bind( $_POST )) {
			echo "<div class='alert'>";			
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
		
		$rowPartner->user_id=$rowUser->id;
		if ($rowPartner->partner_code == null)
		{
			$rowPartner->partner_code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}
		
		if (!$rowPartner->store(false)) {
			
			echo "<div class='alert'>";			
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
				
		for($i=1 ; $i<4 ; $i++)
		{
			$rowAddress = new address( $database );
			//$rowAddress->address_id=$address_id;
			$rowAddress->partner_id=$rowPartner->partner_id;
			$rowAddress->title_id=$_POST['title_id'];
			$rowAddress->country_code=$_POST['country_code'];
			$rowAddress->address_corporate_name1=$_POST['address_corporate_name1'];
			$rowAddress->address_corporate_name2=$_POST['address_corporate_name2'];
			$rowAddress->address_agent_firstname=$_POST['address_agent_firstname'];
			$rowAddress->address_agent_lastname=$_POST['address_agent_lastname'];
			$rowAddress->address_agent_function=$_POST['address_agent_function'];
			$rowAddress->address_street1=$_POST['address_street1'];
			$rowAddress->address_street2=$_POST['address_street2'];
			$rowAddress->address_postalcode=$_POST['address_postalcode'];
			$rowAddress->address_locality=$_POST['address_locality'];
			$rowAddress->address_phone=$_POST['address_phone'];
			$rowAddress->address_fax=$_POST['address_fax'];
			/*$rowAddress->address_email=$_POST['address_email'];*/
			$rowAddress->type_id=$i;			

			if (!$rowAddress->store()) 
			{
				echo "<div class='alert'>";			
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
			
			$database->setQuery( "UPDATE #__easysdi_community_address SET address_update=now() WHERE address_id IN (".$rowAddress->address_id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";			
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
		}			
		
		$query = "UPDATE #__easysdi_community_partner SET partner_update=now()";
		$query .= " WHERE partner_id IN (".$rowPartner->partner_id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";			
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}			
		
		SITE_partner::includePartnerExtension(0,'BOTTOM','registerPartner',$rowPartner->partner_id);									

		//Send email notification to administrator
		$query = "SELECT count(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator')";
		$database->setQuery( $query );
		$total = $database->loadResult();
		if($total >0){
		$query = "SELECT * FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND (#__users.usertype='Administrator' OR #__users.usertype='Super Administrator')";
		$database->setQuery( $query );

		$rows = $database->loadObjectList();
		$mailer =& JFactory::getMailer();
		
		SITE_partner::sendMail($rows,JText::_("EASYSDI_NEW_USER_MAIL_SUBJECT"),JText::sprintf("EASYSDI_NEW_USER_MAIL_BODY",$rowUser->username));
		
		//Send email notification to user
		$query = "SELECT * FROM #__users ";
		$query .= " WHERE id= ".$rowUser->id;
		$database->setQuery( $query );
		$row = $database->loadObjectList();
		//$mailer =& JFactory::getMailer();		
		SITE_partner::sendMail($rows,JText::_("EASYSDI_NEW_USER_MAIL_NOTIFICATION_SUBJECT"),JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY").JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY1",$rowUser->username).JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY2",JRequest::getVar('password','')).JText::sprintf("EASYSDI_NEW_USER_MAIL_NOTIFICATION_BODY3"));
		
		//redirect		
		$mainframe->redirect("index.php" );	
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
	

	function saveAffiliatePartner(  ) {
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

		$rowPartner = new partnerByPartnerId ( $database );
		if (!$rowPartner->bind( $_POST )) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$rowPartner->user_id=$rowUser->id;
		if ($rowPartner->partner_code == null)
		{
			$rowPartner->partner_code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}

		if (!$rowPartner->store(false)) {
				
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		$counter=0;
		foreach( $_POST['address_id'] as $address_id )
		{
			$rowAddress = new address( $database );
			$rowAddress->address_id=$address_id;
			$rowAddress->partner_id=$rowPartner->partner_id;
			$rowAddress->type_id=$_POST['type_id'][$counter];
			if ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 2) {
				$index = 0;
			} elseif ($_POST['sameAddress'][$counter] == 'on' && $rowAddress->type_id == 3) {
				$index = 0;
			} else {
				$index = $counter;
			}

			$rowAddress->title_id=$_POST['title_id'][$index];
			$rowAddress->country_code=$_POST['country_code'][$index];
			$rowAddress->address_corporate_name1=$_POST['address_corporate_name1'][$index];
			$rowAddress->address_corporate_name2=$_POST['address_corporate_name2'][$index];
			$rowAddress->address_agent_firstname=$_POST['address_agent_firstname'][$index];
			$rowAddress->address_agent_lastname=$_POST['address_agent_lastname'][$index];
			$rowAddress->address_agent_function=$_POST['address_agent_function'][$index];
			$rowAddress->address_street1=$_POST['address_street1'][$index];
			$rowAddress->address_street2=$_POST['address_street2'][$index];
			$rowAddress->address_postalcode=$_POST['address_postalcode'][$index];
			$rowAddress->address_locality=$_POST['address_locality'][$index];
			$rowAddress->address_phone=$_POST['address_phone'][$index];
			$rowAddress->address_fax=$_POST['address_fax'][$index];
			$rowAddress->address_email=$_POST['address_email'][$index];

			if (!$rowAddress->store()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
				
			$database->setQuery( "UPDATE #__easysdi_community_address SET address_update=now() WHERE address_id IN (".$rowAddress->address_id.")");
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}

			$counter++;
		}




		$database->setQuery( "DELETE FROM #__easysdi_community_actor WHERE partner_id IN (".$rowPartner->partner_id.")");
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}

		foreach( $_POST['role_id'] as $role_id )
		{
			$database->setQuery( "INSERT INTO #__easysdi_community_actor (role_id, partner_id, actor_update) VALUES (".$role_id.",".$rowPartner->partner_id.",now())" );
			if (!$database->query()) {
				echo "<div class='alert'>";
				echo $database->getErrorMsg();
				echo "</div>";
				exit;
			}
				
		}
		$query = "UPDATE #__easysdi_community_partner SET partner_update=now()";
		$query .= " WHERE partner_id IN (".$rowPartner->partner_id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			echo "<div class='alert'>";
			echo $database->getErrorMsg();
			echo "</div>";
			exit;
		}
			
		$mainframe->redirect("index.php?option=$option&task=".JRequest::getVar('return','showPartner') );
	}

	function cancelPartner( $returnList, $option ) {
		global $mainframe;
		ADMIN_partner::includePartnerExtension(0,'TOP','cancelPartner',0);
		$database =& JFactory::getDBO();
		$row = new partnerByUserId( $database );
		$row->bind( $_POST );
		$row->checkin();
		ADMIN_partner::includePartnerExtension(0,'BOTTOM','cancelPartner',0);
		if ($returnList == true) {
			//mosRedirect( "index2.php?option=$option&task=listPartner" );
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
		}


	}

}

?>
