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

class HTML_partner {
	
	
	function listPartner($use_pagination, &$rows, &$pageNav, $search, $option, $type, $profile, $category, $payment)
	{				
		$database =& JFactory::getDBO();
		 
		$types = array();
		$types[] = JHTML::_('select.option','',JText::_("EASYSDI_LIST_ACCOUNT_ROOT") );
		$database->setQuery( "SELECT #__easysdi_community_partner.partner_id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.root_id IS NULL ORDER BY #__users.name" );
		$types = array_merge( $types, $database->loadObjectList() );

	/*	$profiles = array();
		$profiles[] = JHTML::_('select.option','',JText::_("EASYSDI_LIST_PROFILE") );
		$database->setQuery( "SELECT profile_id AS value,profile_name AS text FROM #__easysdi_community_profile WHERE profile_id > 0 ORDER BY profile_name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );

		$categories = array();
		$categories[] = JHTML::_('select.option','',JText::_("EASYSDI_LIST_CATEGORY") );		
		$database->setQuery( "SELECT category_id AS value,category_name AS text FROM #__easysdi_community_category WHERE category_id > 0 ORDER BY category_name" );
		$categories = array_merge( $categories, $database->loadObjectList() );

		$payments = array();
		$payments[] = JHTML::_('select.option','',JText::_("EASYSDI_LIST_PAYMENT") );		
		$database->setQuery( "SELECT payment_id AS value,payment_name AS text FROM #__easysdi_community_payment WHERE payment_id > 0 ORDER BY payment_name" );
		$payments = array_merge( $payments, $database->loadObjectList() );
*/
		//mosCommonHTML::loadOverlib();
		//$lists['pagination_radio'] = mosHTML::yesnoRadioList('use_pagination','onchange="javascript:submitbutton(\'listPartner\');"',$use_pagination);
		$lists['use_pagination'] = $use_pagination;

		switch ($type) {
			case '':
				$listFilter = "";
				$linkEdit = "editRootPartner";
				break;
			default:
				$listFilter = " disabled";
				$linkEdit = "editAffiliatePartner";
				break;
		}
JToolBarHelper::title(JText::_("EASYSDI_TITLE_ACCOUNT"));
?>
	<form action="index.php" method="GET" name="adminForm">
		<table >
			<tr>				
				<th class="user"><?php echo JText::_("EASYSDI_TITLE_ACCOUNT"); ?>&nbsp;<?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'listPartner\');"', 'value', 'text', $type ); ?></th>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listPartner\');" />			
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listPartner\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_TEXT_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_USER"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_ACCOUNT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_CODE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_LASTUPDATE"); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
	  		
			
			//$checked = mosCommonHTML::CheckedOutProcessing( $row, $i );
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->partner_id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->partner_id; ?></td>
				<td><?php echo $row->partner_username; ?></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $linkEdit; ?>')"><?php echo $row->partner_name; ?></a></td>
				<td><?php echo $row->partner_acronym; ?></td>
				<td><?php echo $row->partner_code; ?></td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->partner_update)); ?></td>
			</tr>
<?php
			$k = 1 - $k;
		}
		
			?></tbody>
			
		<?php			
		
		if ($lists['use_pagination'])
		{?>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<?php
		}
?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listPartner" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  </form>
<?php
	}
	
	function editRootPartner( &$rowUser, &$rowPartner, $rowContact, $rowSubscription, $rowDelivery, $option )
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_ACCOUNT_ROOT"), 'generic.png' );
		//mosMakeHtmlSafe( $rowPartner, ENT_QUOTES );

		$profiles = array();
		
		/*$profiles[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_PROFILE_SELECT"));		
		$database->setQuery( "SELECT profile_id AS value, profile_name AS text FROM #__easysdi_community_profile WHERE profile_id > 0 ORDER BY profile_name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );

		$categories = array();
		$categories[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_CATEGORY_SELECT") );
		$database->setQuery( "SELECT category_id AS value, category_name AS text FROM #__easysdi_community_category WHERE category_id > 0 ORDER BY category_name" );
		$categories = array_merge( $categories, $database->loadObjectList() );

		$payments = array();
		$payments[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_PAYMENT_SELECT") );
		$database->setQuery( "SELECT payment_id AS value, payment_name AS text FROM #__easysdi_community_payment WHERE payment_id > 0 ORDER BY payment_name" );
		$payments = array_merge( $payments, $database->loadObjectList() );
*/
		$titles = array();
		$titles[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_TITLE_SELECT" ));
		$database->setQuery( "SELECT title_id AS value, title_name AS text FROM #__easysdi_community_title WHERE title_id > 0 ORDER BY title_name" );
		$titles = array_merge( $titles, $database->loadObjectList() );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT country_code AS value, country_name AS text FROM #__easysdi_community_country ORDER BY country_name" );
		$countries = array_merge( $countries, $database->loadObjectList() );

		$activities = array();
	/*	$activities[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_ACTIVITY_SELECT") );
		$database->setQuery( "SELECT activity_id AS value, activity_name AS text FROM #__easysdi_community_activity WHERE activity_id > 0 ORDER BY activity_name" );
		$activities = array_merge( $activities, $database->loadObjectList() );

		$collaborators = array();
		$collaborators[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_COLLABORATOR_SELECT") );
		$database->setQuery( "SELECT collaborator_id AS value, collaborator_name AS text FROM #__easysdi_community_collaborator WHERE collaborator_id > 0 ORDER BY collaborator_name" );
		$collaborators = array_merge( $collaborators, $database->loadObjectList() );

		$members = array();
		
		$members[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_MEMBER_SELECT") );
		$database->setQuery( "SELECT member_id AS value, member_name AS text FROM #__easysdi_community_member WHERE member_id > 0 ORDER BY member_name" );
		$members = array_merge( $members, $database->loadObjectList() );
*/
?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("partnerPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"partnerPane");
?>
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(0,'TOP','editPartner',$rowPartner->partner_id);						
		?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_JOOMLA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
								<td><?php echo $rowUser->id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACCOUNT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowUser->name; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_USER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="<?php echo $rowUser->username; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PASSWORD"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $rowUser->password; ?>" /><input type="hidden" name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="email" value="<?php echo $rowUser->email; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_GROUP"); ?> : </td>
								<td><?php echo $rowUser->usertype." [".$rowUser->gid."]" ; ?><input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" /><input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_REGISTER"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LASTVISIT"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_TEXT_EASYSDI"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
								<td><?php echo $rowPartner->partner_id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CODE"); ?> : </td>
								<td><?php echo $rowPartner->partner_code; ?><input type="hidden" name="partner_code" value="<?php echo $rowPartner->partner_code; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_acronym" value="<?php echo $rowPartner->partner_acronym; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_description" value="<?php echo $rowPartner->partner_description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_url" value="<?php echo $rowPartner->partner_url; ?>" /></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php 
		//Include BOTTOM extension		
		
		ADMIN_partner::includePartnerExtension(0,'BOTTOM','editPartner',$rowPartner->partner_id);
								
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_CONTACT"),"partnerPane");
?>
		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress[]" value="">
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(1,'TOP','editPartner',$rowPartner->partner_id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_EASYSDI"); ?></b></legend>
		
		<table border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[]" value="<?php echo $rowContact->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[]" value="<?php echo $rowContact->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[]" value="<?php echo $rowContact->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[]" value="<?php echo $rowContact->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[]" value="<?php echo $rowContact->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[]" value="<?php echo $rowContact->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[]" value="<?php echo $rowContact->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[]" value="<?php echo $rowContact->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[]" value="<?php echo $rowContact->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[]" value="<?php echo $rowContact->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[]" value="<?php echo $rowContact->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[]" value="<?php echo $rowContact->address_email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(1,'BOTTOM','editPartner',$rowPartner->partner_id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_BILLING"),"partnerPane");
?>
		<input type="hidden" name="type_id[]" value="2">
		
		
		
		
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(2,'TOP','editPartner',$rowPartner->partner_id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_EASYSDI"); ?></b></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			
			<tr>
				<td></td>
				<td><input type="checkbox" name="sameAddress[]" onClick="javascript:changeAddress(this.checked, 1)"><?php echo JText::_("EASYSDI_TEXT_ADDRESS_SAME"); ?></td>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[]" value="<?php echo $rowSubscription->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[]" value="<?php echo $rowSubscription->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[]" value="<?php echo $rowSubscription->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[]" value="<?php echo $rowSubscription->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[]" value="<?php echo $rowSubscription->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[]" value="<?php echo $rowSubscription->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[]" value="<?php echo $rowSubscription->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[]" value="<?php echo $rowSubscription->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[]" value="<?php echo $rowSubscription->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[]" value="<?php echo $rowSubscription->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[]" value="<?php echo $rowSubscription->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[]" value="<?php echo $rowSubscription->address_email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(2,'BOTTOM','editPartner',$rowPartner->partner_id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_DELIVERY"),"partnerPane");
?>
		<input type="hidden" name="type_id[]" value="3">
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(3,'TOP','editPartner',$rowPartner->partner_id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_EASYSDI"); ?></b></legend>		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td></td>
				<td><input type="checkbox" name="sameAddress[]" onClick="javascript:changeAddress(this.checked, 2)"><?php echo JText::_("EASYSDI_TEXT_ADDRESS_SAME"); ?></td>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[]" value="<?php echo $rowDelivery->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[]" value="<?php echo $rowDelivery->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[]" value="<?php echo $rowDelivery->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[]" value="<?php echo $rowDelivery->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[]" value="<?php echo $rowDelivery->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[]" value="<?php echo $rowDelivery->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[]" value="<?php echo $rowDelivery->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[]" value="<?php echo $rowDelivery->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[]" value="<?php echo $rowDelivery->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[]" value="<?php echo $rowDelivery->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[]" value="<?php echo $rowDelivery->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[]" value="<?php echo $rowDelivery->address_email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(3,'BOTTOM','editPartner',$rowPartner->partner_id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_RIGHTS"),"partnerPane");
?>
<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(4,'TOP','editPartner',$rowPartner->partner_id);						
		?>
		<table border="0" cellpadding="0" cellspacing="0">		
<?php
		$database->setQuery( "SELECT * FROM #__easysdi_community_role_type ORDER BY type_name" );
		$rows = $database->loadObjectList();
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type_name ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			$rights = array();
			$database->setQuery( "SELECT role_id AS value, role_name AS text FROM #__easysdi_community_role WHERE type_id=".$row->type_id." ORDER BY role_name" );
			$rights = array_merge( $rights, $database->loadObjectList() );
			$selected = array();
			$database->setQuery( "SELECT role_id AS value FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->partner_id );
			$selected = $database->loadObjectList();

?>
								<td><?php echo JHTML::_("select.genericlist",$rights, 'role_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
<?php
		}
?>
		</table>
		<?php 
		//Include TOP extension		
		ADMIN_partner::includePartnerExtension(4,'BOTTOM','editPartner',$rowPartner->partner_id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_AFFILIATES"),"partnerPane");

			$query = "SELECT * FROM #__menu WHERE menutype='mainmenu' ORDER BY ordering";
			//$query = "SELECT partner_id as id, partner_code as name, parent_id as parent FROM #__easysdi_community_partner ORDER BY partner_id";
			$database->setQuery( $query );
			$src_list = $database->loadObjectList();

			print "<pre>";
			print_r ($src_list);
			print "</pre>";

			$preload = array();
			
			$preload[] = JHTML::_('select.option','0', 'Select one or more options' );

			$selected = array();
			
			$selected[] = JHTML::_('select.option','2');
			$selected[] = JHTML::_('select.option','4');
			
			//echo mosHTML::treeSelectList( &$src_list, 1, $preload, 'test', 'class="inputbox" size="10" multiple="true"', 'value', 'text', $selected );

		echo $tabs->endPanel();
		echo $tabs->endPane();
?>
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />
		<input type="hidden" name="partner_id" value="<?php echo $rowPartner->partner_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowSubscription->address_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowDelivery->address_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	<script language="javascript" type="text/javascript">
		changeCategory('<?php echo $rowPartner->category_id; ?>');
		compareAddress(0, 1);
		compareAddress(0, 2);
	</script>
<?php
	}

	function editAffiliatePartner( &$rowUser, &$rowPartner, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		//mosMakeHtmlSafe( $rowPartner, ENT_QUOTES );

		$database->setQuery( "SELECT #__users.name as text,#__easysdi_community_partner.partner_id as value FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.partner_id = ".$rowPartner->root_id );
		$root_name = $database->loadResult();	

		$titles = array();
		$titles[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_TITLE_SELECT") );
		
		$database->setQuery( "SELECT title_id AS value, title_name AS text FROM #__easysdi_community_title WHERE title_id > 0 ORDER BY title_name" );
		$titles = array_merge( $titles, $database->loadObjectList() );

		$countries = array();
		$countries[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT country_code AS value, country_name AS text FROM #__easysdi_community_country ORDER BY country_name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		JToolBarHelper::title(JText::_("EASYSDI_TITLE_ACCOUNT_AFFILIATE"));

?>
	
	
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("affiliatePane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"affiliatePane");
?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_JOOMLA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
								<td><?php echo $rowUser->id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACCOUNT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowUser->name; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_USER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="<?php echo $rowUser->username; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PASSWORD"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $rowUser->password; ?>" /><input type="hidden" name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="email" value="<?php echo $rowUser->email; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_GROUP"); ?> : </td>
								<td><?php echo $rowUser->usertype." [".$rowUser->gid."]" ; ?><input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" /><input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_REGISTER"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LASTVISIT"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_TEXT_EASYSDI"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
								<td><?php echo $rowPartner->partner_id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CODE"); ?> : </td>
								<td><?php echo $rowPartner->partner_code; ?><input type="hidden" name="partner_code" value="<?php echo $rowPartner->partner_code; ?>" /></td>
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACCOUNT_ROOT"); ?> : </td>
								<td><?php echo $root_name ?><input type="hidden" name="root_id" value="<?php echo $rowPartner->root_id; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_acronym" value="<?php echo $rowPartner->partner_acronym; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_description" value="<?php echo $rowPartner->partner_description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_url" value="<?php echo $rowPartner->partner_url; ?>" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_CONTACT"),"affiliatePane");
?>
		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress[]" value="">
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[]" value="<?php echo $rowContact->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[]" value="<?php echo $rowContact->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[]" value="<?php echo $rowContact->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[]" value="<?php echo $rowContact->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[]" value="<?php echo $rowContact->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[]" value="<?php echo $rowContact->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[]" value="<?php echo $rowContact->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[]" value="<?php echo $rowContact->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[]" value="<?php echo $rowContact->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[]" value="<?php echo $rowContact->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[]" value="<?php echo $rowContact->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[]" value="<?php echo $rowContact->address_email; ?>" /></td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_RIGHTS"),"affiliatePane");
?>
		<table border="0" cellpadding="0" cellspacing="0">
<?php
		$database->setQuery( "SELECT * FROM #__easysdi_community_role_type ORDER BY type_name" );
		$rows = $database->loadObjectList();
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type_name ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			$rights = array();
			if ($rowPartner->root_id) {
				$query = "SELECT role_id AS value, role_name AS text FROM #__easysdi_community_role WHERE type_id=".$row->type_id;
				$query .= " AND role_id IN (SELECT role_id FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->root_id.")";
				$query .= " ORDER BY role_name";
				$database->setQuery( $query );
				$rights = array_merge( $rights, $database->loadObjectList() );
				$selected = array();
				$query = "SELECT role_id AS value FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->partner_id;
				$query .= " AND role_id IN (SELECT role_id FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->root_id.")";
				$database->setQuery( $query );
				$selected = $database->loadObjectList();
			}

?>
								<td><?php echo JHTML::_("select.genericlist",$rights, 'role_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
<?php
		}
?>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_AFFILIATES"),"affiliatePane");
		echo $tabs->endPanel();
		echo $tabs->endPane();
?>
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />
		<input type="hidden" name="partner_id" value="<?php echo $rowPartner->partner_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editAffiliatePartner" />
	</form>
<?php
	}

}
	
?>
