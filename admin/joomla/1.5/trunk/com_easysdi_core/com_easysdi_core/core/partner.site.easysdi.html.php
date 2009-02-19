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

class HTML_partner 
{
	
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
	
	function listPartner( &$rows, &$pageNav, $search, $option, $root_partner_id,$types,$type)
	{				
		$database =& JFactory::getDBO();		 
							
?>
	<div class="contentin">
	<form action="index.php" method="GET" id="adminAffiliatePartnerForm" name="adminAffiliatePartnerForm">
	
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_AFFILIATE_LIST"); ?></h2>
	
	<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listPartner\');" />			
				</td>
				<td>
					<?php echo JText::_("EASYSDI_TITLE_ACCOUNT"); ?>&nbsp;<?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" "', 'value', 'text', $type ); ?>				
				</td>
			</tr>
		</table>
		<button type="submit" class="searchButton" > <?php echo JText::_("EASYSDI_SEARCH_BUTTON"); ?></button>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>
		<table class="affiliatePartnerList">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_TEXT_SHARP"); ?></th>
				<th width="20" class='title'></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_USER"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_ACCOUNT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_TEXT_LASTUPDATE"); ?></th>
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
				<td><input type="radio" id="cb<?php echo $i;?>" name="affiliate_id" value="<?php echo $row->user_id; ?>"  /></td>
				<td><?php echo $row->partner_id; ?></td>
				<td><?php echo $row->partner_username; ?></td>
				<td><a href="#edit" onclick="document.getElementById('task').value='editAffiliateById';document.getElementById('cb<?php echo $i;?>').checked=true;document.getElementById('adminAffiliatePartnerForm').submit();"><?php echo $row->partner_name; ?></a></td>
				<td><?php echo $row->partner_acronym; ?></td>				
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->partner_update)); ?></td>
			</tr>
<?php
			$k = 1 - $k;
		}
		
			?></tbody>
			
		<?php			
		
?>
	  	</table>
	  	<input type="hidden" name="return" value="listAffiliatePartner" />
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" id="task" name="task" value="listAffiliatePartner" />	  	 	 
	  </form>	  
	  <button type="button" onclick="document.getElementById('task').value='createAffiliate';document.getElementById('adminAffiliatePartnerForm').submit();"><?php echo JText::_("EASYSDI_NEW_AFFILIATE"); ?></button>
	  </div>
<?php
	}
	
	function showPartner( $hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates,&$rowUser, &$rowPartner, $rowContact, $rowSubscription, $rowDelivery ,$option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
	?>
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("EASYSDI_ACCOUNT_TITLE"); ?></h2>
	
	<?php
		$profiles = array();
								
		$database->setQuery( "SELECT title_name AS text FROM #__easysdi_community_title WHERE title_id = ".$rowContact->title_id ." ORDER BY title_name" );
		$title = JText::_($database->loadResult());
		
								
		$database->setQuery( "SELECT title_name AS text FROM #__easysdi_community_title WHERE title_id = ".$rowSubscription->title_id ." ORDER BY title_name" );
		$title_s = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT title_name AS text FROM #__easysdi_community_title WHERE title_id = ".$rowDelivery->title_id ." ORDER BY title_name" );
		$title_d = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT  country_name AS text FROM #__easysdi_community_country where country_code = '".$rowContact->country_code."'" );		 
		$countryContact = JText::_($database->loadResult());
		
		
		$database->setQuery( "SELECT country_name AS text FROM #__easysdi_community_country where country_code ='".$rowSubscription->country_code."'" );
		$countrySubscription = JText::_($database->loadResult());
		
		
		$database->setQuery( "SELECT country_name AS text FROM #__easysdi_community_country where country_code ='".$rowDelivery->country_code."'" );
		$countryDelivery = JText::_($database->loadResult());
		
		
		$activities = array();
?>					
<?php
		echo $tabs->startPane("partnerPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"partnerPane");
?>
<br>
		<table width="100%">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_ACCOUNT"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
					
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACCOUNT"); ?> : </td>
								<td><?php echo $rowUser->name; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_USER"); ?> : </td>
								<td><?php echo $rowUser->username; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><?php echo $rowUser->email; ?></td>
							</tr>							
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_REGISTER"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LASTVISIT"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
												
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?> : </td>
								<td><?php echo $rowPartner->partner_acronym; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><?php echo $rowPartner->partner_description; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><?php echo $rowPartner->partner_url; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_IS_REBATE"); ?> : </td>
								<td><input class="inputbox" value="1" type="checkbox" disabled="disabled" <?php if ($rowPartner->isrebate == 1) echo " checked"; ?> /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_REBATE"); ?> : </td>
								<td><?php echo $rowPartner->rebate; ?></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_CONTACT"),"partnerPane");
?>
<br>
		<table width="100%">
			<tr>
				<td>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_CONTACT_ADRESS"); ?></b></legend>
		
		<table  border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><?php echo $rowContact->address_corporate_name1; ?> </td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->address_corporate_name2; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JText::_($title); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><?php echo $rowContact->address_agent_firstname; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><?php echo $rowContact->address_agent_lastname; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><?php echo $rowContact->address_agent_function; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><?php echo $rowContact->address_street1; ?> </td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->address_street2; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><?php echo $rowContact->address_postalcode; ?>
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowContact->address_locality; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo $countryContact; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><?php echo $rowContact->address_phone; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><?php echo $rowContact->address_fax; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><?php echo $rowContact->address_email; ?> </td>
			</tr>
		</table>
		</fieldset>
		</td></tr></table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_BILLING"),"partnerPane");
?>
<br>	
		<table width="100%">
			<tr>
				<td>							
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_TEXT_BILLING_ADDRESS"); ?></b></legend>
						<table  border="0" cellpadding="3" cellspacing="0">			
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
								<td><?php echo $rowSubscription->address_corporate_name1; ?> </td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowSubscription->address_corporate_name2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
								<td><?php echo JText::_($title_s); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
								<td><?php echo $rowSubscription->address_agent_firstname; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
								<td><?php echo $rowSubscription->address_agent_lastname; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
								<td><?php echo $rowSubscription->address_agent_function; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
								<td><?php echo $rowSubscription->address_street1; ?></td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowSubscription->address_street2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
								<td><?php echo $rowSubscription->address_postalcode; ?>
								&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowSubscription->address_locality; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
								<td><?php echo $countrySubscription; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
								<td><?php echo $rowSubscription->address_phone; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
								<td><?php echo $rowSubscription->address_fax; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><?php echo $rowSubscription->address_email; ?> </td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_DELIVERY"),"partnerPane") ;
?>
<br>

<table width="100%">
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_TEXT_DELIVERY_ADDRESS"); ?></b></legend>		
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
								<td><?php echo $rowDelivery->address_corporate_name1; ?> </td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowDelivery->address_corporate_name2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
								<td><?php echo JText::_($title_d);?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
								<td><?php echo $rowDelivery->address_agent_firstname; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
								<td><?php echo $rowDelivery->address_agent_lastname; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
								<td><?php echo $rowDelivery->address_agent_function; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
								<td><?php echo $rowDelivery->address_street1; ?></td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowDelivery->address_street2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
								<td><?php echo $rowDelivery->address_postalcode; ?>
								&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowDelivery->address_locality; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
								<td><?php echo $countryDelivery; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
								<td><?php echo $rowDelivery->address_phone; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
								<td><?php echo $rowDelivery->address_fax; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><?php echo $rowDelivery->address_email; ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();		
		if ($hasTheRightToManageHisOwnAffiliates>0)
		{
			echo $tabs->startPanel(JText::_("EASYSDI_TEXT_AFFILIATES"),"partnerPane");
?>
			<br>
			<div class="easysdi_site_aff_list">
			<br>
			<table width="100%">
			<tr>
				<td>
				<fieldset>
					<legend><b><?php echo JText::_("EASYSDI_TEXT_AFFILIATES_LIST"); ?></b></legend>	
<?php	
					$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id = '$rowPartner->partner_id' AND up.user_id = u.id ORDER BY partner_id";
					$database->setQuery( $query );
					$src_list = $database->loadObjectList();
					
					HTML_partner::print_child($src_list );				
?>
				</fieldset>		
				</td>
			</tr>
		</table>	
			</div>
			<br>
<?php
			echo $tabs->endPanel(); 
		}
?>		
<?php
		echo $tabs->endPane();
?>
		<form action="./index.php?option=<?php echo $option ?>" method="POST" name="partnerForm" id="partnerForm" class="partnerForm">
		<!-- input type="hidden" id="option" name="option" value="" /-->
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" id="tab" name="tab" value="" />
<?php 
		if ($hasTheRightToEdit)
		{ 
?>			
		<button type="button" onCLick="var form = document.getElementById('partnerForm');form.task.value='editPartner';form.submit();" ><?php echo JText::_("EASYSDI_EDIT_PARTNER"); ?></button>
<?php 
		} 
?>					
		</form>
	
	</div>	
<?php
	}
	
	function print_child($childList){
		$database =& JFactory::getDBO();		
		echo "<ol>";
		foreach ($childList as $childUser )
		{
			echo "<li><b><i>$childUser->name ($childUser->username)</i></b>";
			$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id != up.parent_id  AND up.parent_id = '$childUser->partner_id' AND up.user_id = u.id ORDER BY partner_id";						
			$database->setQuery( $query );
			$src_list = $database->loadObjectList();
			if (count ($src_list)>0)
			{
				HTML_partner::print_child($src_list);
			}
			echo "</li>";					
		}
		echo "</ol>";
	}
				
	function editPartner( &$rowUser, &$rowPartner, $rowContact, $rowSubscription, $rowDelivery ,$option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');		
	?>
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("EASYSDI_ACCOUNT_TITLE"); ?></h2>
	
	<?php
		$profiles = array();
		
		$titles = array();
		$titles[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_TITLE_SELECT" ));
		$database->setQuery( "SELECT title_id AS value, title_name AS text FROM #__easysdi_community_title WHERE title_id > 0 ORDER BY title_name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($titles );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT country_code AS value, country_name AS text FROM #__easysdi_community_country ORDER BY country_name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($countries );
		$activities = array();
	
?>				
	<form action="index.php?option=<?php echo $option ?>" method="post" name="partnerForm" id="partnerForm" class="partnerForm">
<?php
		echo $tabs->startPane("partnerPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"partnerPane");
?>
<br>		
		<table width="100%">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_ACCOUNT"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
					
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
								<td><?php echo JText::_("EASYSDI_TEXT_REGISTER"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LASTVISIT"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
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
<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" )
							{
?>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_METADATA"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_new_metadata" <?php if ($rowPartner->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_DISTRIBUTION"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_distribution" <?php if ($rowPartner->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
<?php
							}
?>
							<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_ORDER_READY"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowPartner->notify_order_ready == 1) echo " checked"; ?> /></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_CONTACT"),"partnerPane");
?>
<br>
		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress" value="">
	
	<table width="100%">
			<tr>
				<td>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_CONTACT_ADRESS"); ?></b></legend>
		
		<table border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[0]" value="<?php echo $rowContact->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[0]" value="<?php echo $rowContact->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[0]" value="<?php echo $rowContact->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[0]" value="<?php echo $rowContact->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[0]" value="<?php echo $rowContact->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[0]" value="<?php echo $rowContact->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[0]" value="<?php echo $rowContact->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[0]" value="<?php echo $rowContact->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[0]" value="<?php echo $rowContact->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[0]" value="<?php echo $rowContact->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[0]" value="<?php echo $rowContact->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[0]" value="<?php echo $rowContact->address_email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
			</tr>
		</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_BILLING"),"partnerPane");
?>
<br>
		<input type="hidden" name="type_id[]" value="2">			
	<table width="100%">
	<tr>
		<td>		
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_BILLING_ADDRESS"); ?></b></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			
			<tr>
				<td></td>
				<td><input type="checkbox" name="sameAddress1" onClick="javascript:changeAddress(this.checked, 1)"><?php echo JText::_("EASYSDI_TEXT_ADDRESS_SAME"); ?></td>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[1]" value="<?php echo $rowSubscription->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[1]" value="<?php echo $rowSubscription->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[1]" value="<?php echo $rowSubscription->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[1]" value="<?php echo $rowSubscription->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[1]" value="<?php echo $rowSubscription->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[1]" value="<?php echo $rowSubscription->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[1]" value="<?php echo $rowSubscription->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[1]" value="<?php echo $rowSubscription->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[1]" value="<?php echo $rowSubscription->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[1]" value="<?php echo $rowSubscription->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[1]" value="<?php echo $rowSubscription->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[1]" value="<?php echo $rowSubscription->address_email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_DELIVERY"),"partnerPane");
?>
<br>
		<input type="hidden" name="type_id[]" value="3">
		<table width="100%">
			<tr>
				<td>
		<?php 
		//Include TOP extension		
		SITE_partner::includePartnerExtension(3,'TOP','editPartner',$rowPartner->partner_id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_DELIVERY_ADDRESS"); ?></b></legend>		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td></td>
				<td><input type="checkbox" name="sameAddress2" onClick="javascript:changeAddress(this.checked, 2)"><?php echo JText::_("EASYSDI_TEXT_ADDRESS_SAME"); ?></td>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[2]" value="<?php echo $rowDelivery->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[2]" value="<?php echo $rowDelivery->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[2]" value="<?php echo $rowDelivery->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[2]" value="<?php echo $rowDelivery->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[2]" value="<?php echo $rowDelivery->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[2]" value="<?php echo $rowDelivery->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[2]" value="<?php echo $rowDelivery->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[2]" value="<?php echo $rowDelivery->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[2]" value="<?php echo $rowDelivery->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[2]" value="<?php echo $rowDelivery->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[2]" value="<?php echo $rowDelivery->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[2]" value="<?php echo $rowDelivery->address_email; ?>" /></td>
			</tr>
		</table>
		</fieldset>		
		</td>
	</tr>
</table>
<?php
		echo $tabs->endPanel();		
		echo $tabs->endPane();
?>
		<input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" />
		<input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>"/>
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />	
		<input type="hidden" name="partner_id" value="<?php echo $rowPartner->partner_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowSubscription->address_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowDelivery->address_id; ?>" />				
				
		<!-- input type="hidden" name="option" value="<?php echo $option; ?>" / -->
		<input type="hidden" name="task" value="" />
						
		<button type="button" onCLick="var form = document.partnerForm;form.task.value='savePartner';submitbutton();" ><?php echo JText::_("EASYSDI_SAVE_PARTNER"); ?></button>
		
		<button type="button" onCLick="var form = document.getElementById('partnerForm');form.task.value='showPartner';form.submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_PARTNER"); ?></button>
		

	</form>
	<script language="javascript" type="text/javascript">		
		compareAddress(0, 1);
		compareAddress(0, 2);
	</script>
	</div>	
<?php
	}

	
	function showAffiliatePartner($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, &$rowUser, &$rowPartner, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
	
		$database->setQuery( "SELECT #__users.name as text,#__easysdi_community_partner.partner_id as value FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.partner_id = ".$rowPartner->root_id );
		$root_name = $database->loadResult();	
		
		$database->setQuery( "SELECT title_name AS text FROM #__easysdi_community_title WHERE title_id = ".$rowContact->title_id ." ORDER BY title_name" );
		$title = $database->loadResult();

		
		$database->setQuery( "SELECT  country_name AS text FROM #__easysdi_community_country where country_code = '".$rowContact->country_code."'" );
		 
		$countryContact = $database->loadResult() ;
?>
	
<?php
		echo $tabs->startPane("affiliatePane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"affiliatePane");
		
		
?>
<br>

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
								<td><?php echo $rowUser->name; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_USER"); ?> : </td>
								<td><?php echo $rowUser->username; ?></td>
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><?php echo $rowUser->email; ?></td>
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_REGISTER"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LASTVISIT"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>																													
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACRONYM"); ?> : </td>
								<td><?php echo $rowPartner->partner_acronym; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><?php echo $rowPartner->partner_description; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><?php echo $rowPartner->partner_url; ?></td>
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
<br>

		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_CONTACT"); ?></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><?php echo $rowContact->address_corporate_name1; ?></td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->address_corporate_name2; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JText::_($title);?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><?php echo $rowContact->address_agent_firstname; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><?php echo $rowContact->address_agent_lastname; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><?php echo $rowContact->address_agent_function; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><?php echo $rowContact->address_street1; ?></td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->address_street2; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><?php echo $rowContact->address_postalcode; ?>
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowContact->address_locality; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo $country;?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><?php echo $rowContact->address_phone; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><?php echo $rowContact->address_fax; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><?php echo $rowContact->address_email; ?> </td>								
			</tr>
		</table>
		</fieldset>
		</td>
		</tr>
		</table>
		
<?php
		echo $tabs->endPanel();	
		echo $tabs->endPane();
?>
	
		<form action="./index.php?option=<?php echo $option ?>" method="POST" name="partnerForm" id="partnerForm" class="partnerForm">
		<!-- input type="hidden" id="option" name="option" value="" /-->
		<input type="hidden" id="task" name="task" value="" />
		<?php if ($hasTheRightToManageHisOwnAffiliates)
		{ ?>					
		<button type="button" onCLick="var form = document.getElementById('partnerForm');form.task.value='editAffiliatePartner';form.submit();" ><?php echo JText::_("EASYSDI_EDIT_PARTNER"); ?></button>
		<?php
		} ?>				
	</form>	
<?php
	}


	function editAffiliatePartner( &$rowUser, &$rowPartner, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
	
		$database->setQuery( "SELECT #__users.name as text,#__easysdi_community_partner.partner_id as value FROM #__users,#__easysdi_community_partner WHERE #__users.id=#__easysdi_community_partner.user_id AND #__easysdi_community_partner.partner_id = ".$rowPartner->root_id );
		$root_name = $database->loadResult();	
		$titles = array();
		$titles[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_TITLE_SELECT") );
		
		$database->setQuery( "SELECT title_id AS value, title_name AS text FROM #__easysdi_community_title WHERE title_id > 0 ORDER BY title_name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($titles );
		$countries = array();
		$countries[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT country_code AS value, country_name AS text FROM #__easysdi_community_country ORDER BY country_name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($countries);
		
?>
	
	
	<form action="index.php?option=<?php echo $option; ?>" method="post" name="partnerForm" id="partnerForm" class="partnerForm">
<?php
		echo $tabs->startPane("affiliatePane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"affiliatePane");
?>
<br>
		<table width ="100%" border="0" cellpadding="0" cellspacing="0">
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
								<td><?php echo JText::_("EASYSDI_TEXT_REGISTER"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LASTVISIT"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_TEXT_IDENT"); ?> : </td>
								<td><?php echo $rowPartner->partner_id; ?></td>
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
							<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" ){
								?>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_METADATA"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_new_metadata" <?php if ($rowPartner->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_DISTRIBUTION"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_distribution" <?php if ($rowPartner->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
							<?php
							}?>
							<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_ORDER_READY"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowPartner->notify_order_ready == 1) echo " checked"; ?> /></td>
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
<br>

		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress" value="">
				<table width ="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
		
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_CONTACT_ADRESS"); ?></b></legend>
		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1[0]" value="<?php echo $rowContact->address_corporate_name1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2[0]" value="<?php echo $rowContact->address_corporate_name2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname[0]" value="<?php echo $rowContact->address_agent_firstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname[0]" value="<?php echo $rowContact->address_agent_lastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function[0]" value="<?php echo $rowContact->address_agent_function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1[0]" value="<?php echo $rowContact->address_street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2[0]" value="<?php echo $rowContact->address_street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode[0]" value="<?php echo $rowContact->address_postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality[0]" value="<?php echo $rowContact->address_locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone[0]" value="<?php echo $rowContact->address_phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax[0]" value="<?php echo $rowContact->address_fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_email[0]" value="<?php echo $rowContact->address_email; ?>" /></td>								
			</tr>
		</table>
		</fieldset>
		</td>
		</tr></table>
		
<?php
		echo $tabs->endPanel();
	

			
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_RIGHTS"),"affiliatePane");
?>
<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
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
			
			
			$query = "SELECT role_id AS value, role_name AS text FROM #__easysdi_community_role WHERE type_id=".$row->type_id;
			$query .= " AND role_id IN (SELECT role_id FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->parent_id.")";
			$query .= " ORDER BY role_name";
			$database->setQuery( $query );
 
			$rights = array_merge( $rights, $database->loadObjectList() );
			HTML_partner::alter_array_value_with_Jtext($rights);
			$selected = array();
			$query = "SELECT role_id AS value FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->partner_id;
			$query .= " AND role_id IN (SELECT role_id FROM #__easysdi_community_actor WHERE partner_id=".$rowPartner->parent_id.")";
			$database->setQuery( $query );
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
		echo $tabs->endPanel();		
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_AFFILIATES"),"partnerPane");
		?>
<br>
<div class="easysdi_site_aff_list">
<br>
	<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_AFFILIATES_LIST"); ?></b></legend>		
		
	<?php	
		$query = "SELECT * FROM #__easysdi_community_partner up, #__users u where up.partner_id = '$rowPartner->partner_id' AND up.user_id = u.id ORDER BY partner_id";
			
				
		$database->setQuery( $query );
		$src_list = $database->loadObjectList();
		
		HTML_partner::print_child($src_list );
			
		
	
		
		?>
	</fieldset>		
	</div><br>
		
		<?php
		echo $tabs->endPanel(); 
		
		
		echo $tabs->endPane();
?>
		<input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" />
		<input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>"/>
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />		
		<input type="hidden" name="partner_id" value="<?php echo $rowPartner->partner_id; ?>" />
		<input type="hidden" name="parent_id" value="<?php echo $rowPartner->parent_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editAffiliatePartner" />
		<input type="hidden" name="return" value="<?php echo JRequest::getVar('return','showPartner');?>"/>
		<input type="hidden" name="root_id" value="<?php echo $rowPartner->root_id; ?>" />
		<button type="button" onCLick="var form = document.partnerForm;form.task.value='saveAffiliatePartner';submitbutton();" ><?php echo JText::_("EASYSDI_SAVE_PARTNER"); ?></button>
		<button type="button" onCLick="var form = document.getElementById('partnerForm');form.task.value='<?php echo JRequest::getVar('return','showPartner'); ?>';form.submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_PARTNER"); ?></button>
		
	</form>
<?php
	}
	
				
	function createUser( $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');		
	?>
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("EASYSDI_ACCOUNT_TITLE"); ?></h2>
	
	<?php
		$profiles = array();
		
		
		$titles = array();
		$titles[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_TITLE_SELECT" ));
		$database->setQuery( "SELECT title_id AS value, title_name AS text FROM #__easysdi_community_title WHERE title_id > 0 ORDER BY title_name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($titles );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT country_code AS value, country_name AS text FROM #__easysdi_community_country ORDER BY country_name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_partner::alter_array_value_with_Jtext($countries );
		$activities = array();
	
?>				
	<form action="index.php?option=<?php echo $option ?>" method="post" name="partnerForm" id="partnerForm" class="partnerForm">

<br>		
		<table width="100%">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_ACCOUNT"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
							<?php		
								SITE_partner::includePartnerExtension(0,'TOP','requestPartner',0);
							?> 
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ACCOUNT_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="" /> *</td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_USER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="" /> *</td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PASSWORD"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="" /> *</td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PASSWORD_CHECK"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password_chk" value="" /> *</td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="email" value="" /> *</td>
							</tr>																			
							
							
						</table>
					</fieldset>
					<fieldset>
						<legend><b><?php echo JText::_("EASYSDI_TEXT_CONTACT_ADRESS"); ?></b></legend>
		
						<table border="0" cellpadding="3" cellspacing="0">			
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_ORGANISATION"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name1" value="" /> *</td>
						</tr>
						<tr>
							<td></td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_corporate_name2" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id', 'size="1" class="inputbox"', 'value', 'text', 0 ); ?> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_firstname" value="" /> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_lastname" value="" /> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_agent_function" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street1" value="" /> *</td>
						</tr>
						<tr>
							<td></td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_street2" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
							<td><input class="inputbox" type="text" size="5" maxlength="5" name="address_postalcode" value="" />
							&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="address_locality" value="" /> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code', 'size="1" class="inputbox"', 'value', 'text', 'CH' ); ?> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_phone" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="address_fax" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>		
 
		<input type="hidden" name="usertype" value="" />
		<input type="hidden" name="gid" value=""/>
		<input type="hidden" name="id" value="" />	
		<input type="hidden" name="partner_id" value="" />
		<input type="hidden" name="address_id" value="" />
			
		
		<!-- input type="hidden" name="option" value="<?php echo $option; ?>" / -->
		<input type="hidden" name="task" value="" />						
		<button type="button" onCLick="var form = document.partnerForm;form.task.value='createBlockUser';submitbutton();" ><?php echo JText::_("EASYSDI_SAVE_PARTNER"); ?></button>
		
	</form>
	
	</div>	
<?php
	}
}

?>
