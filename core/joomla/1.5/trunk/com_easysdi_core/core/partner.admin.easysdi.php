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

defined('_JEXEC') or die('Restricted access');

class ADMIN_partner {

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
	
	function listPartner($option) {
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
			$filter .= " OR #__easysdi_community_partner.partner_acronym LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_id LIKE '%$search%'";		
			$filter .= " OR #__easysdi_community_partner.partner_code LIKE '%$search%')";		
		}
	

		// D�compte des enregistrements totaux
		if ($type == '') {
			$query = "SELECT COUNT(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id IS NULL";
		} else {
			$query = "SELECT COUNT(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id = ".$type;
		}

		$query = "SELECT COUNT(*) FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id = IS NULL";
		
		$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		
		// Recherche des enregistrements selon les limites
		
		if ($type == '') {
			$query = "SELECT #__users.name as partner_name,#__users.username as partner_username,#__easysdi_community_partner.* FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id IS NULL";
		} else {
			$query = "SELECT #__users.name as partner_name,#__users.username as partner_username,#__easysdi_community_partner.* FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.parent_id = ".$type." AND #__easysdi_community_partner.root_id IS NOT NULL";
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
	
		HTML_partner::listPartner($use_pagination, $rows, $pageNav, $search, $option, $type, $profile, $category, $payment);
	}

	// Cr�ation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editRootPartner($id, $option) 
	{
		if($id=='')
		{
			$id = JRequest::getVar('partner_id');
			if ($id=='')
			{
				$id=0;
			}
		}
		$database =& JFactory::getDBO(); 
		$rowPartner = new partner( $database );
		$rowPartner->load( $id );
		$rowPartner->parent_id = $id; 
		/*if ($rowPartner->partner_entry != null && $rowPartner->partner_entry != '0000-00-00') {
			$rowPartner->partner_entry = date('d.m.Y H:i:s',strtotime($rowPartner->partner_entry));
		} else {
			$rowPartner->partner_entry = null;
		}*/
		/*if ($rowPartner->partner_exit != null && $rowPartner->partner_exit != '0000-00-00')	{
			$rowPartner->partner_exit = date('d.m.Y H:i:s',strtotime($rowPartner->partner_exit));
		} else {
			$rowPartner->partner_exit = null;
		}*/
	
		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$id." AND type_id=1" );
		$contact_id = $database->loadResult();
		echo $database->getErrorMsg();
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$id." AND type_id=2" );
		$subscription_id = $database->loadResult();
		echo $database->getErrorMsg();
		$rowSubscription = new address( $database );
		$rowSubscription->load( $subscription_id );

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$id." AND type_id=3" );
		$delivery_id = $database->loadResult();
		echo $database->getErrorMsg();
		$rowDelivery = new address( $database );
		$rowDelivery->load( $delivery_id );
		
		//Get availaible profile
		$database->setQuery( "SELECT profile_id as value, profile_translation as text FROM #__easysdi_community_profile" );
		$rowsProfile = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		//Get user profile
		if($rowPartner->partner_id)
		{
			$database->setQuery( "SELECT profile_id as value FROM #__easysdi_community_partner_profile WHERE partner_id=".$rowPartner->partner_id );
			$rowsPartnerProfile = $database->loadObjectList();
			echo $database->getErrorMsg();
		}
		
		$rowUser =&	 new JTableUser($database);
		$JId = JRequest::getVar('JId');
		if($JId=='')
		{
			$JId = $rowPartner->user_id;
		}
		$rowUser->load($JId);		
		if ($JId == '')
		{
			$rowUser->usertype='Registered';
			$rowUser->gid=18;
		}

		HTML_partner::editRootPartner( $rowUser, $rowPartner, $rowContact, $rowSubscription, $rowDelivery, $option, $rowsProfile, $rowsPartnerProfile );
	}

	// Cr�ation d'enregistrement (id = 0)
	// ou modification de l'enregistrement id = n
	function editAffiliatePartner( $id, $option ) {		
		// if $id is empty, situation is : refresh page after change the joomla account
		// then get the _POST variable 'partner_id' to retrieve the partner object from the database
		if($id=='')
		{
			$id = JRequest::getVar('partner_id');
			if ($id=='')
			{
				//if 'partner_id' does not return a user object, set 'id' to 0 as in create situation
				$id=0;
			}
		}
		$database =& JFactory::getDBO();

		$rowPartner = new partner( $database );
		$rowPartner->load( $id );
	
		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$id." AND type_id=1" );		
		$contact_id = $database->loadResult();
		echo $database->getErrorMsg();		
		$rowContact = new address( $database );
		$rowContact->load( $contact_id );

		
		$rowUser =&	 new JTableUser($database);
		//JId represents the selected joomla account
		$JId = JRequest::getVar('JId');
		if($JId=='')
		{
			//no joomla account select, so get the joomla account of the current partner 
			$JId = $rowPartner->user_id;
		}
		$rowUser->load($JId);
		//$rowUser->load( $rowPartner->user_id );
				
		if ($id == 0)
		{
			$type = JRequest::getVar('type','');
			$database->setQuery( "SELECT root_id FROM #__easysdi_community_partner WHERE partner_id='".$type."'");		
			$root_id = $database->loadResult();
			
		 		if ($root_id != null){
					$rowPartner->root_id=$root_id;
		 		}else{
		 			$rowPartner->root_id=$type;
		 		}
		 		
			$rowPartner->parent_id=$type;
			
			if ($JId == '')
			{
				$rowUser->usertype='Registered';
				$rowUser->gid=18;
			}
		}
		
		//Get user root profiles to get availaible profiles
		$database->setQuery( "SELECT profile_id as value, profile_translation as text FROM #__easysdi_community_profile WHERE profile_id IN(SELECT profile_id FROM #__easysdi_community_partner_profile WHERE partner_id=".$rowPartner->root_id.")" );
		$rowsProfile = $database->loadObjectList();
		echo $database->getErrorMsg();
		
		if($rowPartner->partner_id)
		{
			//Get user profile
			$database->setQuery( "SELECT profile_id as value FROM #__easysdi_community_partner_profile WHERE partner_id=".$rowPartner->partner_id );
			$rowsPartnerProfile = $database->loadObjectList();
			echo $database->getErrorMsg();
		}

		HTML_partner::editAffiliatePartner( $rowUser, $rowPartner, $rowContact, $option, $rowsProfile, $rowsPartnerProfile  );
	}
	
	function removePartner( $cid, $option ) {
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit;
		}
		foreach( $cid as $partner_id )
		{
			$partner = new partner( $database );
			$partner->load( $partner_id );
		
			$user =&	 new JTableUser($database);
			$user->load( $partner->user_id );
			
			//delete adresses
			$database->setQuery( "DELETE FROM #__easysdi_community_address WHERE partner_id=".$partner_id);
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
			}
			
			//delete partner
			if (!$partner->delete()) {
				//echo "<script> alert('".$partner->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
			}
			
			//delete juser
			if (!$user->delete()) {
				//echo "<script> alert('".$user->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
			}
			
			ADMIN_partner::includePartnerExtension(0,'BOTTOM','removePartner',$partner_id);
			
			
		}

		$mainframe->redirect("index.php?option=$option&task=listPartner" );		
	}

	function exportPartner( $cid, $option ) {
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � exporter'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage('S�lectionnez un enregistrement � exporter','error');
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit;
		}

		$document = new DomDocument();
		$xml = "<easysdi>";
		
		foreach( $cid as $partner_id )
		{
			$xml .= "<partner>";
			$partner = new partner( $database );
			$partner->load( $partner_id );
			$xml .= utf8_encode($partner->toXML(true));
		
			$xml .= "<user>";
			$user =&	 new JTableUser($database);
			$user->load( $partner->user_id );
			//$user =& JFactory::getUser( $partner->user_id );
			/*$user = new mosUser( $database );
			$user->load( $partner->user_id );*/
			$xml .= utf8_encode($user->toXML(true));
			$xml .= "</user>";

			$xml .= "<contact>";
			$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$partner_id." AND type_id=1" );	
			$contact_id = $database->loadResult();
			$contact = new address( $database );
			$contact->load( $contact_id );
			$xml .= utf8_encode($contact->toXML(true));
			$xml .= "</contact>";

			$xml .= "<subscription>";
			$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$partner_id." AND type_id=2" );
			$subscription_id = $database->loadResult();
			$subscription = new address( $database );
			$subscription->load( $subscription_id );
			$xml .= utf8_encode($subscription->toXML(true));
			$xml .= "</subscription>";

			$xml .= "<delivery>";
			$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$partner_id." AND type_id=3" );
			$delivery_id = $database->loadResult();
			$delivery = new address( $database );
			$delivery->load( $delivery_id );
			$xml .= utf8_encode($delivery->toXML(true));
			$xml .= "</delivery>";

			$xml .= "</partner>";
		}
		$xml .= "</easysdi>";
		//echo "<textarea rows='1000' cols='1000'>".$xml."</textarea>"; 		
		$document->loadXML($xml);
	
		$processor = new xsltProcessor();
		$style = new DomDocument();
		$style->load(dirname(__FILE__).'/../xsl/partner.export.xsl');
		
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

	
	function savePartner( $returnList, $option ) {
		global $mainframe;
						
		
		$database=& JFactory::getDBO(); 
		
		$rowUser =&	 new JTableUser($database);
		
		
		if (!$rowUser->bind( $_POST )) {			
			//echo "<script> alert('".$rowUser->getError()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
		
		//Check if username already exit cause in jos_user name isn't unique but should be...
		if (JRequest::getVar('old_username','') != $rowUser->username){
			$database->setQuery("SELECT count(*) FROM #__users WHERE username = '".$rowUser->username."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("EASYSDI_ACCOUNT_ALREADY_EXISTS"),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
				exit();
			}
		}
		
		//CSheck if email is unique also because it must be (pass recovery!).
                if (JRequest::getVar('old_email','') != $rowUser->email){
			$database->setQuery("SELECT count(*) FROM #__users WHERE email = '".$rowUser->email."'");
			$total = $database->loadResult();
			if($total > 0){
				$mainframe->enqueueMessage(JText::_("EASYSDI_EMAIL_ALREADY_REGISTRED"),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
				exit();
			}
		}		
		
		//rebuild password
		if (JRequest::getVar('old_password','') != $rowUser->password)
		{
			//$rowUser->password = md5( JRequest::getVar('password','') );
			$salt = JUserHelper::genRandomPassword(32);
			$crypt = JUserHelper::getCryptedPassword(JRequest::getVar('password',''), $salt);
			$rowUser->password = $crypt . ':' . $salt;			
		}
		if (!$rowUser->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
		
		if (JRequest::getVar('id','') == '')
		{
			$database->setQuery( "UPDATE #__users SET registerDate=now() WHERE id = (".$rowUser->id.")");
			if (!$database->query()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
				exit();
			}
		}

		$rowPartner = new partner( $database );
		if (!$rowPartner->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
		
		$rowPartner->user_id=$rowUser->id;
		if ($rowPartner->partner_code == null)
		{
			$rowPartner->partner_code = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}
		
		if (!$rowPartner->store(false)) {
			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			//echo "<script> alert('".$rowPartner->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
	
		$counter=0;
		foreach( $_POST['address_id'] as $address_id )
		{
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
				//echo "<script> alert('".$rowAddress->getError()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
				exit();
			}
			
			$database->setQuery( "UPDATE #__easysdi_community_address SET address_update=now() WHERE address_id IN (".$rowAddress->address_id.")");
			if (!$database->query()) {
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
				exit();
			}

			$counter++;
		}

		$database->setQuery( "DELETE FROM #__easysdi_community_actor WHERE partner_id IN (".$rowPartner->partner_id.")");
		if (!$database->query()) {
			//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
		
		if(isset($_POST['role_id']))
		{
			if (count ($_POST['role_id'] )>0){
			foreach( $_POST['role_id'] as $role_id )
			{
				$database->setQuery( "INSERT INTO #__easysdi_community_actor (role_id, partner_id, actor_update) VALUES (".$role_id.",".$rowPartner->partner_id.",now())" );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listPartner" );
					exit();
				}
				
			}
			}
		}
		
		//Save profile selection
		$database->setQuery( "DELETE FROM #__easysdi_community_partner_profile WHERE partner_id IN (".$rowPartner->partner_id.")");
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
		$profile_id_list ="";
		if(isset($_POST['profile_id']))
		{
			if (count ($_POST['profile_id'] )>1)
			{
				foreach( $_POST['profile_id'] as $profile_id )
				{
					$database->setQuery( "INSERT INTO #__easysdi_community_partner_profile (profile_id, partner_id) VALUES (".$profile_id.",".$rowPartner->partner_id.")" );
					if (!$database->query()) 
					{
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						$mainframe->redirect("index.php?option=$option&task=listPartner" );
						exit();
					}
					$profile_id_list .= $profile_id;
					$profile_id_list .= ",";
				}
			}else{
/*
				$profile_id = $_POST['profile_id'];
				if(is_array($profile_id))
					$profile_id = $profile_id[0];
				$database->setQuery( "INSERT INTO #__easysdi_community_partner_profile (profile_id, partner_id) VALUES (".$profile_id.",".$rowPartner->partner_id.")" );
*/
				$database->setQuery( "INSERT INTO #__easysdi_community_partner_profile (profile_id, partner_id) VALUES (".$_POST['profile_id'].",".$rowPartner->partner_id.")" );
				if (!$database->query()) 
				{
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listPartner" );
					exit();
				}
				$profile_id_list .= $profile_id;
/*
				$profile_id_list .= ",";
*/
			}
		}
		if($profile_id_list )
		{
			$profile_id_list = substr($profile_id_list, 0, strlen ($profile_id_list)-1 );
			//Update affiliate user profile
			$database->setQuery( "DELETE FROM #__easysdi_community_partner_profile 
					   WHERE partner_id IN (SELECT partner_id FROM #__easysdi_community_partner WHERE root_id=".$rowPartner->partner_id.") 
					   AND 
					   profile_id NOT IN (".$profile_id_list.")");
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPartner" );
				exit();
			}
		}
		
		
		//Set partner update date
		$query = "UPDATE #__easysdi_community_partner SET partner_update=now()";
		$query .= " WHERE partner_id IN (".$rowPartner->partner_id.")";
		$database->setQuery( $query );
		if (!$database->query()) {
			//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPartner" );
			exit();
		}
		
		//include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_asitvd'.DS.'core'.DS.'partner.admin.asitvd.html.php');
		//ADMIN_ASITVD_Partner::savePartnerFields ($rowPartner->partner_id); 
		
		
		ADMIN_partner::includePartnerExtension(0,'BOTTOM','savePartner',$rowPartner->partner_id);
		
		
		if ($returnList == true) {			
			$mainframe->redirect("index.php?option=$option&task=listPartner");
		}
		
	}

	function cancelPartner( $returnList, $option ) {
		global $mainframe;
		ADMIN_partner::includePartnerExtension(0,'TOP','cancelPartner',0);
		$database =& JFactory::getDBO();
		$row = new partner( $database );
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
