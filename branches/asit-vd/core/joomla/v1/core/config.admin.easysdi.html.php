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

class HTML_config {
	
	
	function listConfig($use_pagination, &$rows, &$pageNav, $option)
	{				
		$database =& JFactory::getDBO();
		 
		JToolBarHelper::title(JText::_("EASYSDI_LIST_CONFIG"));
?>
	<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_CONFIG_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_CONFIG_KEY"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_CONFIG_VALUE"); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{		
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editConfig')"><?php echo $row->thekey; ?></a></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editConfig')"><?php echo $row->value; ?></a></td>
			</tr>
<?php
			$k = 1 - $k;
			$i++;
		}
		
			?></tbody>
			
		<?php			
		
		if ($use_pagination)
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
	  	<input type="hidden" name="task" value="listConfig" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
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

	
	function editConfig( &$rowConfig,$option )
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_EDIT_CONFIG"), 'generic.png' );

	?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $rowConfig->id; ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_CONFIG_KEY"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="thekey" value="<?php echo $rowConfig->thekey; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_CONFIG_VALUE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="value" value="<?php echo $rowConfig->value; ?>" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $rowConfig->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
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
		
		$titles = array_merge( $titles, $database->loadObjectList());
		HTML_partner::alter_array_value_with_Jtext($titles);
		


		$countries = array();
		$countries[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT country_code AS value, country_name AS text FROM #__easysdi_community_country ORDER BY country_name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($countries );
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
								<td ><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
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
								<td ><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
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
				$query .= " AND role_id IN (SELECT role_id FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->parent_id.")";
				$query .= " ORDER BY role_name";
				$database->setQuery( $query );
				$rights = array_merge( $rights, $database->loadObjectList() );
				$selected = array();
				$query = "SELECT role_id AS value FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->partner_id;
				$query .= " AND role_id IN (SELECT role_id FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->parent_id.")";
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
		$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id = '$rowPartner->partner_id' AND up.user_id = u.id ORDER BY partner_id";
			
			
			$database->setQuery( $query );
			$src_list = $database->loadObjectList();
		if(count($src_list) != 0)
		{	
			userTree::buildTreeView($src_list[0],false);
		}
		//HTML_partner::print_child($src_list );
			
		echo $tabs->endPanel();
		echo $tabs->endPane();
?>
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />
		<input type="hidden" name="type" value="<?php echo JRequest::getVar("type",""); ?>" />
		<input type="hidden" name="partner_id" value="<?php echo $rowPartner->partner_id; ?>" />
		<input type="hidden" name="parent_id" value="<?php echo $rowPartner->parent_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editAffiliatePartner" />
	</form>
<?php
	}

}
	
?>
