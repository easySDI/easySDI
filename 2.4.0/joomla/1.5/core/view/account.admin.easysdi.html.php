<?php
//var_dump($_POST);

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

class HTML_account {


	function listAccount($use_pagination, &$rows, &$pageNav, $search, $option, $type, $profile, $category, $payment)
	{
		$database =& JFactory::getDBO();
			
		$types = array();
		$types[] = JHTML::_('select.option','',JText::_("CORE_ACCOUNT_LIST_ROOT_SELECT") );
		$type = JRequest::getVar("type","");
		
		if ($type==''){
			$database->setQuery( "SELECT #__sdi_account.id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NULL ORDER BY #__users.name" );
		}
		else{
			$database->setQuery( "SELECT #__sdi_account.id AS value,#__users.name AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id =$type" );

			echo "<br>";
			$types = array_merge( $types, $database->loadObjectList() );
			echo "<br>";
			$database->setQuery( "SELECT #__sdi_account.id AS value,CONCAT('&nbsp;&nbsp;&gt; ',#__users.name) AS text FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.root_id IS NOT NULL AND (#__sdi_account.parent_id = $type ) ORDER BY #__users.name" );
		}

		$types = array_merge( $types, $database->loadObjectList() );

		$lists['use_pagination'] = $use_pagination;

		switch ($type) {
			case '':
				$listFilter = "";
				$linkEdit = "editRootAccount";
				break;
			default:
				$listFilter = " disabled";
				$linkEdit = "editAffiliateAccount";
				break;
		}
		?>
<form action="index.php" method="GET" name="adminForm">
<table>
	<tr>
		<th class="user"><?php echo JText::_("CORE_ACCOUNT_LIST_ROOT_LABEL"); ?>&nbsp;<?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" onchange="javascript:submitbutton(\'listAccount\');"', 'value', 'text', $type ); ?>
		</th>
	</tr>
</table>
<table width="100%">
	<tr>
		<td align="right"><b><?php echo JText::_("CORE_FILTER");?></b>&nbsp; <input
			type="text" name="search" value="<?php echo $search;?>"
			class="inputbox" onchange="javascript:submitbutton('listAccount');" />
		</td>
	</tr>
</table>
<!-- <table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("CORE_ACCOUNT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listAccount\');"',$use_pagination); ?></td>
			</tr>
		</table>
		 -->
<table class="adminlist">
	<thead>
		<tr>
			<th width="20" class='title'><?php echo JText::_("CORE_SHARP"); ?></th>
			<th width="20" class='title'><input type="checkbox" name="toggle"
				value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
			<th class='title'><?php echo JText::_("CORE_ID"); ?></th>
			<th class='title'><?php echo JText::_("CORE_ACCOUNT_TAB_COL_USER"); ?></th>
			<th class='title'><?php echo JText::_("CORE_ACCOUNT_TAB_COL_ACCOUNT"); ?></th>
			<th class='title'><?php echo JText::_("CORE_ACCOUNT_TAB_COL_ACRONYM"); ?></th>
			<th class='title'><?php echo JText::_("CORE_ACCOUNT_TAB_COL_CODE"); ?></th>
			<th class='title'><?php echo JText::_("CORE_UPDATED"); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count($rows); $i < $n; $i++)
	{
		$row = $rows[$i];
		 
			

		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
			<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]"
				value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
			<td><?php echo $row->id; ?></td>
			<td><?php echo $row->account_username; ?></td>
			<td><a href="#edit"
				onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $linkEdit; ?>')"><?php echo $row->account_name; ?></a></td>
			<td><?php echo $row->acronym; ?></td>
			<td><?php echo $row->guid; ?></td>
			<td><?php echo date('d.m.Y H:i:s',strtotime($row->updated)); ?></td>
		</tr>
		<?php
		$k = 1 - $k;
	}

	?>
	</tbody>

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
<input type="hidden" name="task" value="listAccount" /> 
<input type="hidden" name="boxchecked" value="0" /> 
<input type="hidden" name="hidemainmenu" value="0" /> 
<input type="hidden" name="JId" value="" />
</form>
	<?php
	}

	function alter_array_value_with_Jtext(&$rows)
	{
		if (count($rows)>0){
			foreach($rows as $key => $row) {
				$rows[$key]->text = JText::_($rows[$key]->text);
			}
		}
	}


	function editRootAccount( &$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery, $rowsProfile, $rowsAccountProfile, $option )
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$tabs =& JPANE::getInstance('Tabs');
		//mosMakeHtmlSafe( $rowAccount, ENT_QUOTES );

		$profiles = array();
		$titles = array();
		$titles[] = JHTML::_('select.option','0', JText::_("CORE_ACCOUNT_LIST_TITLE_SELECT" ));
		$database->setQuery( "SELECT id AS value, label AS text FROM #__sdi_title WHERE id > 0 ORDER BY label" );
		$titles = array_merge( $titles, $database->loadObjectList());

		HTML_account::alter_array_value_with_Jtext($titles );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("CORE_ACCOUNT_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_list_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries );

		$usersJoomla = array();
		$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE id = $rowUser->id");
		$usersJoomla = array_merge($usersJoomla, $database->loadObjectList());
		$usersJoomla [] = JHTML::_('select.option','0', JText::_("CORE_ACCOUNT_LIST_USERJOOMLA_SELECT") );
		if(count($usersJoomla)== 1 )
		{
			$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE NOT EXISTS (SELECT * FROM #__sdi_account WHERE #__sdi_account.user_id = #__users.id ) ORDER BY name");
			$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
			if($rowAccount->id <> 0 )
			{
				$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE  id = $rowAccount->user_id");
				$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
			}
		}
		else
		{
			$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE NOT EXISTS (SELECT * FROM #__sdi_account WHERE #__sdi_account.user_id = #__users.id ) AND id <> $rowUser->id ORDER BY name");
			$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
				
			if($rowAccount->id <> 0 && $rowAccount->user_id <> $rowUser->id)
			{
				$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE  id = $rowAccount->user_id");
				$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
			}
			//$usersJoomla []=	JHTML::_('select.option', '0', JText::_("EASYSDI_LIST_USER_JOOMLA_SELECT") );;
		}

		$activities = array();
		?>
<form action="index.php" method="post" name="adminForm" id="adminForm"
	class="adminForm"><?php
	echo $tabs->startPane("accountPane");
	echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_GENERAL_LABEL"),"accountPane");
	?> <?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(0,'TOP','editAccount',$rowAccount->id);
	?>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<fieldset><legend><?php echo JText::_("CORE_ACCOUNT_FIELDSET_JOOMLA_LABEL"); ?></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("CORE_ID"); ?> :</td>
				<td><?php echo $rowUser->id; ?></td>
			</tr>

			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_USERJOOMLA_LABEL"); ?> :</td>
				<td><?php echo JHTML::_("select.genericlist",$usersJoomla, 'JId', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editRootAccount\');"', 'value', 'text',$rowUser->id); ?></td>

			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="name" value="<?php echo $rowUser->name; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="username" value="<?php echo $rowUser->username; ?>" /><input type="hidden"
					name="old_username" value="<?php echo $rowUser->username; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_PASSWORD_LABEL"); ?> :</td>
				<td><input class="inputbox" type="password" size="50"
					maxlength="100" name="password"
					value="<?php echo $rowUser->password; ?>" /><input type="hidden"
					name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="user_email" value="<?php echo $rowUser->email; ?>" /><input type="hidden"
					name="old_email" value="<?php echo $rowUser->email; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_GROUP_LABEL"); ?> :</td>
				<td><?php echo $rowUser->usertype." [".$rowUser->gid."]" ; ?><input
					type="hidden" name="usertype"
					value="<?php echo $rowUser->usertype; ?>" /><input type="hidden"
					name="gid" value="<?php echo $rowUser->gid; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> :</td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> :</td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset><legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("CORE_ID"); ?> :</td>
				<td><?php echo $rowAccount->id; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_CODE"); ?> :</td>
				<td><?php echo $rowAccount->guid; ?><input type="hidden" name="guid"
					value="<?php echo $rowAccount->guid; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_LOGO_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="logo" value="<?php echo $rowAccount->logo; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_DESCRIPTION"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="description" value="<?php echo $rowAccount->description; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="200"
					name="url" value="<?php echo $rowAccount->url; ?>" /></td>
			</tr>

			<?php
			if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" ){
				?>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYMETADATA_LABEL"); ?> :</td>
				<td><input class="inputbox" value="1" type="checkbox"
					name="notify_new_metadata"
					<?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYDISTRIBUTION_LABEL"); ?>
				:</td>
				<td><input class="inputbox" value="1" type="checkbox"
					name="notify_distribution"
					<?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
			</tr>
			<?php
			}?>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYORDERREADY_LABEL"); ?> :
				</td>
				<td><input class="inputbox" value="1" type="checkbox"
					name="notify_order_ready"
					<?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ISREBATE_LABEL"); ?> :</td>
				<td><input class="inputbox" value="1" type="checkbox"
					name="isrebate"
					<?php if ($rowAccount->isrebate == 1) echo " checked"; ?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_REBATE_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="rebate" value="<?php echo $rowAccount->rebate; ?>" /></td>
			</tr>


		</table>
		</fieldset>
		</td>
	</tr>
</table>
					<?php
					//Include BOTTOM extension

					ADMIN_account::includeAccountExtension(0,'BOTTOM','editAccount',$rowAccount->id);

					?> <?php
					echo $tabs->endPanel();
					echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"accountPane");
					?> <input type="hidden" name="type_id[]" value="1"> <input
	type="hidden" name="sameAddress" value=""> <?php 
	//Include TOP extension
	ADMIN_account::includeAccountExtension(1,'TOP','editAccount',$rowAccount->id);
	?>
<fieldset><legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>

<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td><?php echo JText::_("CORE_NAME"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename1[0]"
			value="<?php echo $rowContact->corporatename1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename2[0]"
			value="<?php echo $rowContact->corporatename2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentfirstname[0]"
			value="<?php echo $rowContact->agentfirstname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentlastname[0]"
			value="<?php echo $rowContact->agentlastname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="5" maxlength="5"
			name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" />
		&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input
			class="inputbox" type="text" size="50" maxlength="100"
			name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>
	</tr>
</table>
</fieldset>
	<?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(1,'BOTTOM','editAccount',$rowAccount->id);
	?> <?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"),"accountPane");
	?> <input type="hidden" name="type_id[]" value="2"> <?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(2,'TOP','editAccount',$rowAccount->id);

	?>
<fieldset><legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td></td>
		<td><input type="checkbox" name="sameAddress1"
			onClick="javascript:changeAddress(this.checked, 1)"><?php echo JText::_("CORE_ACCOUNT_SAMEADDRESS_LABEL"); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_NAME"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename1[1]"
			value="<?php echo $rowSubscription->corporatename1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename2[1]"
			value="<?php echo $rowSubscription->corporatename2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->title_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentfirstname[1]"
			value="<?php echo $rowSubscription->agentfirstname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentlastname[1]"
			value="<?php echo $rowSubscription->agentlastname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="function[1]" value="<?php echo $rowSubscription->function; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street1[1]" value="<?php echo $rowSubscription->street1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street2[1]" value="<?php echo $rowSubscription->street2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="5" maxlength="5"
			name="postalcode[1]"
			value="<?php echo $rowSubscription->postalcode; ?>" /> &nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?>
		: <input class="inputbox" type="text" size="50" maxlength="100"
			name="locality[1]" value="<?php echo $rowSubscription->locality; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->country_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="phone[1]" value="<?php echo $rowSubscription->phone; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="fax[1]" value="<?php echo $rowSubscription->fax; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="email[1]" value="<?php echo $rowSubscription->email; ?>" /></td>
	</tr>
</table>
</fieldset>
	<?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(2,'BOTTOM','editAccount',$rowAccount->id);
	?> <?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"),"accountPane");
	?> <input type="hidden" name="type_id[]" value="3"> <?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(3,'TOP','editAccount',$rowAccount->id);
	?>
<fieldset><legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td></td>
		<td><input type="checkbox" name="sameAddress2"
			onClick="javascript:changeAddress(this.checked, 2)"><?php echo JText::_("CORE_ACCOUNT_SAMEADDRESS_LABEL"); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_NAME"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename1[2]"
			value="<?php echo $rowDelivery->corporatename1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename2[2]"
			value="<?php echo $rowDelivery->corporatename2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->title_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentfirstname[2]"
			value="<?php echo $rowDelivery->agentfirstname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentlastname[2]"
			value="<?php echo $rowDelivery->agentlastname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="function[2]" value="<?php echo $rowDelivery->function; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street1[2]" value="<?php echo $rowDelivery->street1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street2[2]" value="<?php echo $rowDelivery->street2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="5" maxlength="5"
			name="postalcode[2]" value="<?php echo $rowDelivery->postalcode; ?>" />
		&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input
			class="inputbox" type="text" size="50" maxlength="100"
			name="locality[2]" value="<?php echo $rowDelivery->locality; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->country_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="phone[2]" value="<?php echo $rowDelivery->phone; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="fax[2]" value="<?php echo $rowDelivery->fax; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="email[2]" value="<?php echo $rowDelivery->email; ?>" /></td>
	</tr>
</table>
</fieldset>
	<?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(3,'BOTTOM','editAccount',$rowAccount->id);
	?> <?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_RIGHTS_LABEL"),"accountPane");
	?> <?php
	//Include TOP extension
	ADMIN_account::includeAccountExtension(4,'TOP','editAccount',$rowAccount->id);
	?>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<fieldset><legend><?php echo JText::_('CORE_ACCOUNT_RIGHTS_PROFILE'); ?></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
			<?php
			HTML_account::alter_array_value_with_Jtext($rowsProfile);
			$selected = array();
			$selected = $rowsAccountProfile;
			?>
				<td><?php echo JHTML::_("select.genericlist",$rowsProfile, 'profile_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<?php
	$database->setQuery( "SELECT * FROM #__sdi_list_roletype ORDER BY name" );
	$rows = $database->loadObjectList();
	for ($i=0, $n=count($rows); $i < $n; $i++)
	{
		$row = $rows[$i];
		?>
	<tr>
		<td>
		<fieldset><legend><?php echo JText::_('CORE_ACCOUNT_RIGHTS_FONCTION'); ?></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
			<?php
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'common'.DS.'easysdi.config.php');
		
			$rights = array();
			$query = "SELECT id AS value, label AS text FROM #__sdi_list_role WHERE roletype_id=".$row->id;
			$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
			if($enableFavorites != 1)
				$query .= " AND code !='FAVORITE' ";
			$query .= " ORDER BY name";
			$database->setQuery( $query );
			 
			$rights = array_merge( $rights, $database->loadObjectList() );
			HTML_account::alter_array_value_with_Jtext($rights);
			$selected = array();
			$database->setQuery( "SELECT role_id AS value FROM #__sdi_actor WHERE account_id=".$rowAccount->id );
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
	ADMIN_account::includeAccountExtension(4,'BOTTOM','editAccount',$rowAccount->id);
	?> <?php
	echo $tabs->endPanel();
	echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"),"accountPane");

	$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";

	$database->setQuery( $query );
	$user =& JFactory::getUser();
	$src_list = $database->loadObjectList();
	
	if(count($src_list) != 0)
	{
		userTree::buildTreeView($src_list[0], false);
	}
	//HTML_account::print_child($src_list );

	echo $tabs->endPanel();
	echo $tabs->endPane();
	?> 
	<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />
	<input type="hidden" name="type" value="<?php echo JRequest::getVar("type",""); ?>" /> 
	<input type="hidden" name="account_id" value="<?php echo $rowAccount->id; ?>" />
	<input type="hidden" name="address_id[0]" value="<?php echo $rowContact->id; ?>" /> 
	<input type="hidden" name="address_id[1]" value="<?php echo $rowSubscription->id; ?>" /> 
	<input type="hidden" name="address_id[2]" value="<?php echo $rowDelivery->id; ?>" /> 
	<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
	<input type="hidden" name="task" value="" />
	
	<input type="hidden" name="ordering" value="<?php echo $rowAccount->ordering; ?>" />
	<input type="hidden" name="created" value="<?php echo ($rowAccount->created)? $rowAccount->created : date ('Y-m-d H:i:s');?>" />
	<input type="hidden" name="createdby" value="<?php echo ($rowAccount->createdby)? $rowAccount->createdby : $user->id; ?>" /> 
	<input type="hidden" name="updated" value="<?php echo ($rowAccount->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
	<input type="hidden" name="updatedby" value="<?php echo ($rowAccount->createdby)? $user->id : ''; ?>" />
	</form>

<script language="javascript" type="text/javascript">
		compareAddress(0, 1);
		compareAddress(0, 2);
	</script>
	<?php
	}



	function editAffiliateAccount( &$rowUser, &$rowAccount, $rowContact, $type, $rowsProfile, $rowsAccountProfile, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO();
		$tabs =& JPANE::getInstance('Tabs');
		//mosMakeHtmlSafe( $rowAccount, ENT_QUOTES );
		
		$database->setQuery( "SELECT #__users.name as text,#__sdi_account.id as value FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id = ".$rowAccount->root_id );
		$root_name = $database->loadResult();
		

		$database->setQuery( "SELECT #__users.name as text,#__sdi_account.id as value FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id = ".$rowAccount->parent_id );
		$parent_name = $database->loadResult();


		$titles = array();
		$titles[] = JHTML::_('select.option','0',JText::_("CORE_ACCOUNT_LIST_TITLE_SELECT") );

		$database->setQuery( "SELECT id AS value, label AS text FROM #__sdi_title WHERE id > 0 ORDER BY label" );

		$titles = array_merge( $titles, $database->loadObjectList());
		HTML_account::alter_array_value_with_Jtext($titles);



		$countries = array();
		$countries[] = JHTML::_('select.option','0',JText::_("CORE_ACCOUNT_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_list_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries );



		$usersJoomla = array();
		$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE id = $rowUser->id");
		$usersJoomla = array_merge($usersJoomla, $database->loadObjectList());
		$usersJoomla [] = JHTML::_('select.option','0', JText::_("CORE_ACCOUNT_LIST_USERJOOMLA_SELECT") );
		if(count($usersJoomla)== 1 )
		{
				
			$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE NOT EXISTS (SELECT * FROM #__sdi_account WHERE #__sdi_account.user_id = #__users.id ) ORDER BY name");
			$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
			if($rowAccount->id <> 0 )
			{
				$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE  id = $rowAccount->user_id");
				$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
			}
		}
		else
		{
			$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE NOT EXISTS (SELECT * FROM #__sdi_account WHERE #__sdi_account.user_id = #__users.id ) AND id <> $rowUser->id ORDER BY name");
			$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
				
			if($rowAccount->id <> 0 && $rowAccount->user_id <> $rowUser->id)
			{
				$database->setQuery("SELECT id AS value, name AS text FROM #__users WHERE  id = $rowAccount->user_id");
				$usersJoomla = array_merge( $usersJoomla, $database->loadObjectList());
			}
			//$usersJoomla []=	JHTML::_('select.option', '0', JText::_("EASYSDI_LIST_USER_JOOMLA_SELECT") );;
		}

		$usersParent = array();
		if($rowAccount->user_id == 0)
		{
			$database->setQuery("SELECT #__sdi_account.id AS value,#__users.name AS text
			FROM #__users 
			INNER JOIN #__sdi_account ON #__users.id = #__sdi_account.user_id 
			WHERE (#__sdi_account.root_id = $rowAccount->root_id 
			OR #__sdi_account.id = $rowAccount->root_id) 
			ORDER BY #__users.name ");
		}
		else
		{
			$database->setQuery("SELECT #__sdi_account.id AS value, #__users.name AS text
			FROM #__users 
			INNER JOIN #__sdi_account ON #__users.id = #__sdi_account.user_id 
			WHERE #__users.id <> $rowAccount->user_id 
			AND  (#__sdi_account.root_id = $rowAccount->root_id 
			OR #__sdi_account.id = $rowAccount->root_id
			)
			AND 
			(
				(
					#__sdi_account.parent_id <> $rowAccount->id
					AND
					#__sdi_account.root_id <> $rowAccount->id
				)
				OR
				(
					#__sdi_account.root_id IS NULL 
					AND 
					#__sdi_account.parent_id IS NULL 
				)
			)
			
			ORDER BY #__users.name");
		}
		if(is_array($database->loadObjectList()))
		{
			$usersParent =  array_merge($usersParent, $database->loadObjectList());
		}
			
		?>


<form action="index.php" method="post" name="adminForm" id="adminForm"
	class="adminForm"><?php
	echo $tabs->startPane("affiliatePane");
	echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_GENERAL_LABEL"),"affiliatePane");
	?>
<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<fieldset><legend><?php echo JText::_("CORE_ACCOUNT_FIELDSET_JOOMLA_LABEL"); ?></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("CORE_ID"); ?> :</td>
				<td><?php echo $rowUser->id; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_USERJOOMLA_LABEL"); ?> :</td>
				<td><?php echo JHTML::_("select.genericlist",$usersJoomla, 'JId', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editAffiliateAccount\');"', 'value', 'text',$rowUser->id); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="name" value="<?php echo $rowUser->name; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="username" value="<?php echo $rowUser->username; ?>" /><input type="hidden"
					name="old_username" value="<?php echo $rowUser->username; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_PASSWORD_LABEL"); ?> :</td>
				<td><input class="inputbox" type="password" size="50"
					maxlength="100" name="password"
					value="<?php echo $rowUser->password; ?>" /><input type="hidden"
					name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="user_email" value="<?php echo $rowUser->email; ?>" /><input type="hidden"
					name="old_email" value="<?php echo $rowUser->email; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_GROUP_LABEL"); ?> :</td>
				<td><?php echo $rowUser->usertype." [".$rowUser->gid."]" ; ?><input
					type="hidden" name="usertype"
					value="<?php echo $rowUser->usertype; ?>" /><input type="hidden"
					name="gid" value="<?php echo $rowUser->gid; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> :</td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> :</td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
	<tr>
		<td>
		<fieldset><legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("CORE_ID"); ?> :</td>
				<td><?php echo $rowAccount->id; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_CODE"); ?> :</td>
				<td><?php echo $rowAccount->guid; ?><input type="hidden" name="guid"
					value="<?php echo $rowAccount->guid; ?>" /></td>
			</tr>

			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ROOTACCOUNT_LABEL"); ?> :</td>
				<td><?php echo $root_name ?><input type="hidden" name="root_id"
					value="<?php echo $rowAccount->root_id; ?>" /></td>
			</tr>


			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_PARENTACCOUNT_LABEL"); ?> :</td>
				<td><?php echo JHTML::_("select.genericlist", $usersParent, 'parent_id', 'size="1" class="inputbox" ', 'value', 'text', $rowAccount->parent_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_DESCRIPTION"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="description" value="<?php echo $rowAccount->description; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> :</td>
				<td><input class="inputbox" type="text" size="50" maxlength="100"
					name="url" value="<?php echo $rowAccount->url; ?>" /></td>
			</tr>
			<?php
			if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" ){
				?>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYMETADATA_LABEL"); ?> :</td>
				<td><input value="1" class="inputbox" type="checkbox"
					name="notify_new_metadata"
					<?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYDISTRIBUTION_LABEL"); ?>
				:</td>
				<td><input value="1" class="inputbox" type="checkbox"
					name="notify_distribution"
					<?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
			</tr>
			<?php
			}?>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYORDERREADY_LABEL"); ?> :
				</td>
				<td><input class="inputbox" value="1" type="checkbox"
					name="notify_order_ready"
					<?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
			</tr>

		</table>
		</fieldset>
		</td>
	</tr>
</table>
					<?php
					echo $tabs->endPanel();
					echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"affiliatePane");
					?> <input type="hidden" name="type_id[]" value="1"> <input
	type="hidden" name="sameAddress[]" value="">

<table border="0" cellpadding="3" cellspacing="0">
	<tr>
		<td><?php echo JText::_("CORE_NAME"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename1[0]"
			value="<?php echo $rowContact->corporatename1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="corporatename2[0]"
			value="<?php echo $rowContact->corporatename2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentfirstname[0]"
			value="<?php echo $rowContact->agentfirstname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="agentlastname[0]"
			value="<?php echo $rowContact->agentlastname; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
	</tr>
	<tr>
		<td></td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="5" maxlength="5"
			name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" />
		&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input
			class="inputbox" type="text" size="50" maxlength="100"
			name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> :</td>
		<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
	</tr>
	<tr>
		<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> :</td>
		<td><input class="inputbox" type="text" size="50" maxlength="100"
			name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>
	</tr>
</table>
					<?php
					echo $tabs->endPanel();
					echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_RIGHTS_LABEL"),"affiliatePane");
					?>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_('EASYSDI_ACCOUNTPROFILE_TITLE'); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			$profiles = array();
			$profiles = array_merge( $profiles, $rowsProfile );
			HTML_account::alter_array_value_with_Jtext($rowsProfile);
			$selected = array();
			$selected = $rowsAccountProfile;
?>
								<td><?php echo JHTML::_("select.genericlist",$profiles, 'profile_id[]', 'size="15" multiple="true" class="selectbox"', 'value', 'text', $selected ); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
<?php
		$database->setQuery( "SELECT * FROM #__sdi_list_roletype ORDER BY name" );
		$rows = $database->loadObjectList();
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_($row->name); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			$rights = array();
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'common'.DS.'easysdi.config.php');
			if ($rowAccount->root_id) {
				$query = "SELECT id AS value, name AS text FROM #__sdi_list_role WHERE roletype_id=".$row->id;
				$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
				if($enableFavorites != 1)
					$query .= " AND code !='FAVORITE' ";
				$query .= " AND id IN (SELECT role_id FROM #__sdi_actor WHERE account_id=".$rowAccount->parent_id.")";
				$query .= " ORDER BY name";
				$database->setQuery( $query );
				$rights = array_merge( $rights, $database->loadObjectList() );
				HTML_account::alter_array_value_with_Jtext($rights);
				$selected = array();
				$query = "SELECT role_id AS value FROM #__sdi_actor WHERE account_id=".$rowAccount->id;
				$query .= " AND role_id IN (SELECT role_id FROM #__sdi_actor WHERE account_id=".$rowAccount->parent_id.")";
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
echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"),"affiliatePane");
$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";


$database->setQuery( $query );
$user =& JFactory::getUser();
$src_list = $database->loadObjectList();

if(count($src_list) != 0)
{
	userTree::buildTreeView($src_list[0], false);
}
//HTML_account::print_child($src_list );

echo $tabs->endPanel();
echo $tabs->endPane();
?> 
<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />
<input type="hidden" name="type" value="<?php echo $type; ?>" /> 
<input type="hidden" name="account_id" value="<?php echo $rowAccount->id; ?>" />
<input type="hidden" name="address_id[0]" value="<?php echo $rowContact->id; ?>" /> 
<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
<input type="hidden" name="task" value="editAffiliateAccount" />

<input type="hidden" name="ordering" value="<?php echo $rowAccount->ordering; ?>" />
<input type="hidden" name="created" value="<?php echo ($rowAccount->created)? $rowAccount->created : date ('Y-m-d H:i:s');?>" />
<input type="hidden" name="createdby" value="<?php echo ($rowAccount->createdby)? $rowAccount->createdby : $user->id; ?>" /> 
<input type="hidden" name="updated" value="<?php echo ($rowAccount->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
<input type="hidden" name="updatedby" value="<?php echo ($rowAccount->createdby)? $user->id : ''; ?>" />

</form>
<?php
	}

}

?>


