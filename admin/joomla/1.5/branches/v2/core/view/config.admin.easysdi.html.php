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

class HTML_config {
	
	/*
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editConfig')"><?php echo $row->code; ?></a></td>
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
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="thekey" value="<?php echo $rowConfig->code; ?>" /></td>								
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
				
			
	function editAffiliateAccount( &$rowUser, &$rowAccount, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		//mosMakeHtmlSafe( $rowPartner, ENT_QUOTES );

		$database->setQuery( "SELECT #__users.name as text,#__sdi_account.id as value FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id = ".$rowAccount->root_id );
		$root_name = $database->loadResult();	

		$titles = array();
		$titles[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_TITLE_SELECT") );
		
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_title WHERE id > 0 ORDER BY name" );
		
		$titles = array_merge( $titles, $database->loadObjectList());
		HTML_partner::alter_array_value_with_Jtext($titles);
		


		$countries = array();
		$countries[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT code AS value, name AS text FROM #__sdi_country ORDER BY name" );
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
								<td><?php echo $rowAccount->id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CODE"); ?> : </td>
								<td><?php echo $rowAccount->code; ?><input type="hidden" name="code" value="<?php echo $rowAccount->code; ?>" /></td>
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACCOUNT_ROOT"); ?> : </td>
								<td><?php echo $root_name ?><input type="hidden" name="root_id" value="<?php echo $rowAccount->root_id; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowAccount->description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" value="<?php echo $rowAccount->url; ?>" /></td>
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
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[]" value="<?php echo $rowContact->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[]" value="<?php echo $rowContact->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[]" value="<?php echo $rowContact->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[]" value="<?php echo $rowContact->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[]" value="<?php echo $rowContact->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[]" value="<?php echo $rowContact->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[]" value="<?php echo $rowContact->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[]" value="<?php echo $rowContact->postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[]" value="<?php echo $rowContact->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[]" value="<?php echo $rowContact->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[]" value="<?php echo $rowContact->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[]" value="<?php echo $rowContact->email; ?>" /></td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_RIGHTS"),"affiliatePane");
?>
		<table border="0" cellpadding="0" cellspacing="0">
<?php
		$database->setQuery( "SELECT * FROM #__sdi_list_roletype ORDER BY type_name" );
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
				$query = "SELECT id AS value, name AS text FROM #__sdi_role WHERE type_id=".$row->type_id;
				$query .= " AND id IN (SELECT role_id FROM #__sdi_actor WHERE account_id=".$rowAccount->id.")";
				$query .= " ORDER BY name";
				$database->setQuery( $query );
				$rights = array_merge( $rights, $database->loadObjectList() );
				$selected = array();
				$query = "SELECT role_id AS value FROM #__sdi_actor WHERE account_id=".$rowAccount->id;
				$query .= " AND role_id IN (SELECT role_id FROM #__sdi_actor WHERE account_id=".$rowAccount->id.")";
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
		$query = "SELECT * FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";
			
			
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
		<input type="hidden" name="account_id" value="<?php echo $rowAccount->id; ?>" />
		<input type="hidden" name="parent_id" value="<?php echo $rowAccount->parent_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editAffiliateAccount" />
	</form>
<?php
	}
*/
	function showConfig($option, $coreList, $catalogItem, $catalogList, $shopItem, $shopList, $proxyItem, $proxyList, $fieldsLength, $attributetypelist )
	{
		global $mainframe;

		// Load tooltips behavior
		JHTML::_('behavior.tooltip');
		JHTML::_('behavior.switcher');

		// Load component specific configurations
		$table =& JTable::getInstance('component');
		$table->loadByOption( 'com_users' );
		$userparams = new JParameter( $table->params, JPATH_ADMINISTRATOR.DS.'components'.DS.'com_users'.DS.'config.xml' );
		$table->loadByOption( 'com_media' );
		$mediaparams = new JParameter( $table->params, JPATH_ADMINISTRATOR.DS.'components'.DS.'com_media'.DS.'config.xml' );

		// Build the component's submenu
		?>
		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu" class="configuration">
					<li><a id="core" class="active"><?php echo JText::_( 'CORE_CONFIGURATION_CORE_TAB_TITLE' ); ?></a></li>
<?php
if ($catalogItem > 0){
?>
					<li><a id="catalog"><?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_TAB_TITLE' ); ?></a></li>
<?php
}
if ($shopItem > 0){
?>
					<li><a id="shop"><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_TAB_TITLE' ); ?></a></li>
<?php
}
if ($proxyItem > 0){
?>					
					<li><a id="proxy"><?php echo JText::_( 'CORE_CONFIGURATION_PROXY_TAB_TITLE' ); ?></a></li>
<?php
}
?>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
		<?php 
		// Load settings for the FTP layer
		jimport('joomla.client.helper');
		$ftp =& JClientHelper::setCredentialsFromRequest('ftp');
		?>
		<form action="index.php" method="post" name="adminForm">
		<?php if ($ftp) {
			require_once($tmplpath.DS.'ftp.php');
		} ?>
		<div id="config-document">
			<div id="page-core">
				<table class="noshow">
					<tr>
						<td width="65%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_CORE_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_JAVABRIDGE_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_JAVABRIDGE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="java_bridge_url" value="<?php echo $coreList[0]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_DESCRIPTIONLENGTH_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_DESCRIPTIONLENGTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="description_length" value="<?php echo $coreList[1]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOWIDTH_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOWIDTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="logo_width" value="<?php echo $coreList[2]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOHEIGHT_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_LOGOHEIGHT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="logo_height" value="<?php echo $coreList[3]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_PAGINATIONMETADATA_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_PAGINATIONMETADATA_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="pagination_metadata" value="<?php echo $coreList[4]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_WELCOMEREDIRECT_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_WELCOMEREDIRECT_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="welcome_redirect_url" value="<?php echo $coreList[5]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CORE_FOP_LABEL' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CORE_FOP_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="fop_url" value="<?php echo $coreList[6]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table> 
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
<?php
if ($catalogItem > 0){
?>
			<div id="page-catalog">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_FIELDSET_TITLE' ); ?></legend>
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_URL_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_URL_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_url" value="<?php echo $catalogList[0]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_CODE' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_CODE' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_encoding_code" value="<?php echo $catalogList[7]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_VAL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_ENCODING_VAL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_encoding_val" value="<?php echo $catalogList[8]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_ISOCODE_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_ISOCODE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_isocode" value="<?php echo $catalogList[1]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_NORTH_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_NORTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_north" value="<?php echo $catalogList[2]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_SOUTH_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_SOUTH_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_south" value="<?php echo $catalogList[3]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_EAST_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_EAST_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_east" value="<?php echo $catalogList[4]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_WEST_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_WEST_LABEL' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="catalog_boundary_west" value="<?php echo $catalogList[5]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_TYPE_LABEL' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_CATALOG_BOUNDARY_TYPE_LABEL' ); ?>
											</span>
										</td>
										<td>
											<?php echo JHTML::_("select.genericlist",$attributetypelist, 'catalog_boundary_type', 'size="1" class="inputbox"', 'value', 'text', $catalogList[6]->value ); ?>
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
<?php
}
if ($shopItem > 0){
?>
			<div id="page-shop">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_FIELDSET_TITLE' ); ?></legend>
								
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PROXYHOST' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PROXYHOST' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="proxyhost" value="<?php echo $shopList[0]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARCHIVE_DELAY' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARCHIVE_DELAY' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="archive_delay" value="<?php echo $shopList[1]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_HISTORY_DELAY' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_HISTORY_DELAY' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="10" name="history_delay" value="<?php echo $shopList[2]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_CADDY_DESC_LENGTH' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_CADDY_DESC_LENGTH' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="caddy_description_length" value="<?php echo $shopList[3]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_AREAPRECISION' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_AREAPRECISION' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="mod_perim_area_precision" value="<?php echo $shopList[4]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_METERTOKILOMETERLIMIT' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_MOD_PERIM_METERTOKILOMETERLIMIT' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="mod_perim_metertokilometerlimit" value="<?php echo $shopList[5]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP4' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP4' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shop_article_step4" value="<?php echo $shopList[6]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root();?>includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_REQUIREMENTS' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_CONTENT_FORMAT' ); ?></div>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP5' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_STEP5' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shop_article_step5" value="<?php echo $shopList[7]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root();?>includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_REQUIREMENTS' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_CONTENT_FORMAT' ); ?></div>
										</td>
									</tr>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_TERMS_OF_USE' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'CORE_CONFIGURATION_SHOP_ARTICLE_TERMS_OF_USE' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="shop_article_terms_of_use" value="<?php echo $shopList[8]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
										<td>
											<div style="font-weight: bold" >
												<img src="<?php echo JURI::root();?>includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_REQUIREMENTS' ); ?>
											</div>
										</td>
										<td>
											<div  ><?php echo JText::_( 'CORE_CONFIGURATION_SHOP_PLUGIN_CONTENT_FORMAT' ); ?></div>
										</td>
									</tr>
									</tbody>
								</table>
								
							</fieldset>				
						</td>
					</tr>
				</table>
			</div>
<?php
}
if ($proxyItem > 0){
?>	
			<div id="page-proxy">
				<table class="noshow">
					<tr>
						<td width="60%">
							<fieldset class="adminform">
								<legend><?php echo JText::_( 'CORE_CONFIGURATION_PROXY_FIELDSET_TITLE' ); ?></legend>
								 
								<table class="admintable" cellspacing="1">
									<tbody>
									<tr>
										<td valign="top" class="key">
											<span class="editlinktip hasTip" title="<?php echo JText::_( 'PROXY_CONFIG' ); ?>::<?php echo JText::_( 'TIPTMPFOLDER' ); ?>">
												<?php echo JText::_( 'PROXY_CONFIG' ); ?>
											</span>
										</td>
										<td>
											<input class="text_area" type="text" size="100" name="proxy_config" value="<?php echo $proxyList[0]->value; ?>" maxlength="<?php echo $fieldsLength['value'];?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>	
						</td>
					</tr>
				</table>
			</div>
		</div>
<?php
}
?>
		<div class="clr"></div>

		<input type="hidden" name="c" value="global" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="catalog_item" value="<?php echo $catalogItem; ?>" />
		<input type="hidden" name="shop_item" value="<?php echo $shopItem; ?>" />
		<input type="hidden" name="proxy_item" value="<?php echo $proxyItem; ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		<?php
	}
}
	
?>
