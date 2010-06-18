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


class HTML_account 
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
	
	function listAccount( &$rows, &$pageNav, $search, $option, $root_account_id,$types,$type)
	{				
		$database =& JFactory::getDBO();		 
							
?>
	<div class="contentin">
	<form action="index.php" method="GET" id="adminAffiliateAccountForm" name="adminAffiliateAccountForm">
	
	<h2 class="contentheading"><?php echo JText::_("EASYSDI_AFFILIATE_LIST"); ?></h2>
	
	<h3> <?php echo JText::_("EASYSDI_SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton('listAccount');" />			
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
		<table class="affiliateAccountList">
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
				<td><?php echo $row->id; ?></td>
				<td><?php echo $row->account_username; ?></td>
				<td><a href="#edit" onclick="document.getElementById('task').value='editAffiliateById';document.getElementById('cb<?php echo $i;?>').checked=true;document.getElementById('adminAffiliateAccountForm').submit();"><?php echo $row->account_name; ?></a></td>
				<td><?php echo $row->acronym; ?></td>				
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->updated)); ?></td>
			</tr>
<?php
			$k = 1 - $k;
		}
		
			?></tbody>
			
		<?php			
		
?>
	  	</table>
	  	<input type="hidden" name="return" value="listAffiliateAccount" />
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" id="task" name="task" value="listAffiliateAccount" />	  	 	 
	  </form>	  
	  <button type="button" onclick="document.getElementById('task').value='createAffiliate';document.getElementById('adminAffiliateAccountForm').submit();"><?php echo JText::_("EASYSDI_NEW_AFFILIATE"); ?></button>
	  </div>
<?php
	}
	
	function showAccount( $hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates,&$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
	?>
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("EASYSDI_ACCOUNT_TITLE"); ?></h2>
	
	<?php
		$profiles = array();
								
		$database->setQuery( "SELECT name AS text FROM #__sdi_title WHERE id = ".$rowContact->title_id ." ORDER BY name" );
		$title = JText::_($database->loadResult());
		
								
		$database->setQuery( "SELECT name AS text FROM #__sdi_title WHERE id = ".$rowSubscription->title_id ." ORDER BY name" );
		$title_s = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_title WHERE id = ".$rowDelivery->title_id ." ORDER BY name" );
		$title_d = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT  name AS text FROM #__sdi_country where code = '".$rowContact->country_code."'" );		 
		$countryContact = JText::_($database->loadResult());
		
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_country where code ='".$rowSubscription->country_code."'" );
		$countrySubscription = JText::_($database->loadResult());
		
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_country where code ='".$rowDelivery->country_code."'" );
		$countryDelivery = JText::_($database->loadResult());
		
		
		$activities = array();
?>					
<?php
		echo $tabs->startPane("accountPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"accountPane");
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
								<td><?php echo $rowAccount->acronym; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LOGO"); ?> : </td>
								<td><?php echo $rowAccount->logo; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><?php echo $rowAccount->description; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><?php echo $rowAccount->url; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_IS_REBATE"); ?> : </td>
								<td><input class="inputbox" value="1" type="checkbox" disabled="disabled" <?php if ($rowAccount->isrebate == 1) echo " checked"; ?> /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_REBATE"); ?> : </td>
								<td><?php echo $rowAccount->rebate; ?></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_CONTACT"),"accountPane");
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
				<td><?php echo $rowContact->corporatename1; ?> </td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->corporatename2; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JText::_($title); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><?php echo $rowContact->agentfirstname; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><?php echo $rowContact->agentlastname; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><?php echo $rowContact->function; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><?php echo $rowContact->street1; ?> </td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->street2; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><?php echo $rowContact->postalcode; ?>
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowContact->locality; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo $countryContact; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><?php echo $rowContact->phone; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><?php echo $rowContact->fax; ?> </td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><?php echo $rowContact->email; ?> </td>
			</tr>
		</table>
		</fieldset>
		</td></tr></table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_BILLING"),"accountPane");
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
								<td><?php echo $rowSubscription->corporatename1; ?> </td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowSubscription->corporatename2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
								<td><?php echo JText::_($title_s); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
								<td><?php echo $rowSubscription->agentfirstname; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
								<td><?php echo $rowSubscription->agentlastname; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
								<td><?php echo $rowSubscription->function; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
								<td><?php echo $rowSubscription->street1; ?></td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowSubscription->street2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
								<td><?php echo $rowSubscription->postalcode; ?>
								&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowSubscription->locality; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
								<td><?php echo $countrySubscription; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
								<td><?php echo $rowSubscription->phone; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
								<td><?php echo $rowSubscription->fax; ?> </td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><?php echo $rowSubscription->email; ?> </td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_DELIVERY"),"accountPane") ;
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
								<td><?php echo $rowDelivery->corporatename1; ?> </td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowDelivery->corporatename2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
								<td><?php echo JText::_($title_d);?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
								<td><?php echo $rowDelivery->agentfirstname; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
								<td><?php echo $rowDelivery->agentlastname; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
								<td><?php echo $rowDelivery->function; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
								<td><?php echo $rowDelivery->street1; ?></td>
							</tr>
							<tr>
								<td></td>
								<td><?php echo $rowDelivery->street2; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
								<td><?php echo $rowDelivery->postalcode; ?>
								&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowDelivery->locality; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
								<td><?php echo $countryDelivery; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
								<td><?php echo $rowDelivery->phone; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
								<td><?php echo $rowDelivery->fax; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
								<td><?php echo $rowDelivery->email; ?></td>
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
			echo $tabs->startPanel(JText::_("EASYSDI_TEXT_AFFILIATES"),"accountPane");
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
					$query = "SELECT * FROM #__sdi_account up, #__users u where up.account_id = '$rowAccount->id' AND up.user_id = u.id ORDER BY id";
					$database->setQuery( $query );
					$src_list = $database->loadObjectList();
					
					if(count($src_list) != 0)
					{	
						
						userTree::buildTreeView($src_list[0], true);
					}		
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
		<form action="./index.php?option=<?php echo $option ?>" method="POST" name="accountForm" id="accountForm" class="accountForm">
		<!-- input type="hidden" id="option" name="option" value="" /-->
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" id="tab" name="tab" value="" />
<?php 
		if ($hasTheRightToEdit)
		{ 
?>			
		<button type="button" onCLick="var form = document.getElementById('accountForm');form.task.value='editAccount';form.submit();" ><?php echo JText::_("EASYSDI_EDIT_ACCOUNT"); ?></button>
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
			$query = "SELECT * FROM #__sdi_account up, #__users u where up.account_id != up.parent_id  AND up.parent_id = '$childUser->account_id' AND up.user_id = u.id ORDER BY id";						
			$database->setQuery( $query );
			$src_list = $database->loadObjectList();
			if (count ($src_list)>0)
			{
				HTML_account::print_child($src_list);
			}
			echo "</li>";					
		}
		echo "</ol>";
	}
				
	function editAccount( &$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option)
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
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_title WHERE id > 0 ORDER BY name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($titles );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT code AS value, name AS text FROM #__sdi_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries );
		$activities = array();
	
?>				
	<form action="index.php?option=<?php echo $option ?>" method="post" name="accountForm" id="accountForm" class="accountForm">
<?php
		echo $tabs->startPane("accountPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"accountPane");
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
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_LOGO"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="logo" value="<?php echo $rowAccount->logo; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowAccount->description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="url" value="<?php echo $rowAccount->url; ?>" /></td>
							</tr>
<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" )
							{
?>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_METADATA"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_new_metadata" <?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_DISTRIBUTION"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_distribution" <?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
<?php
							}
?>
							<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_ORDER_READY"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_CONTACT"),"accountPane");
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
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[0]" value="<?php echo $rowContact->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[0]" value="<?php echo $rowContact->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[0]" value="<?php echo $rowContact->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[0]" value="<?php echo $rowContact->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
			</tr>
		</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_BILLING"),"accountPane");
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
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[1]" value="<?php echo $rowSubscription->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[1]" value="<?php echo $rowSubscription->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[1]" value="<?php echo $rowSubscription->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[1]" value="<?php echo $rowSubscription->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[1]" value="<?php echo $rowSubscription->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[1]" value="<?php echo $rowSubscription->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[1]" value="<?php echo $rowSubscription->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[1]" value="<?php echo $rowSubscription->postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[1]" value="<?php echo $rowSubscription->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_code[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->country_code ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[1]" value="<?php echo $rowSubscription->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[1]" value="<?php echo $rowSubscription->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[1]" value="<?php echo $rowSubscription->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_DELIVERY"),"accountPane");
?>
<br>
		<input type="hidden" name="type_id[]" value="3">
		<table width="100%">
			<tr>
				<td>
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(3,'TOP','editAccount',$rowAccount->id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_DELIVERY_ADDRESS"); ?></b></legend>		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td></td>
				<td><input type="checkbox" name="sameAddress2" onClick="javascript:changeAddress(this.checked, 2)"><?php echo JText::_("EASYSDI_TEXT_ADDRESS_SAME"); ?></td>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[2]" value="<?php echo $rowDelivery->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[2]" value="<?php echo $rowDelivery->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[2]" value="<?php echo $rowDelivery->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[2]" value="<?php echo $rowDelivery->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[2]" value="<?php echo $rowDelivery->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[2]" value="<?php echo $rowDelivery->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[2]" value="<?php echo $rowDelivery->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[2]" value="<?php echo $rowDelivery->postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[2]" value="<?php echo $rowDelivery->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[2]" value="<?php echo $rowDelivery->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[2]" value="<?php echo $rowDelivery->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[2]" value="<?php echo $rowDelivery->email; ?>" /></td>
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
		<input type="hidden" name="account_id" value="<?php echo $rowAccount->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowSubscription->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowDelivery->id; ?>" />				
				
		<!-- input type="hidden" name="option" value="<?php echo $option; ?>" / -->
		<input type="hidden" name="task" value="" />
						
		<button type="button" onCLick="var form = document.accountForm;form.task.value='saveAccount';submitbutton();" ><?php echo JText::_("EASYSDI_SAVE_ACCOUNT"); ?></button>
		
		<button type="button" onCLick="var form = document.getElementById('accountForm');form.task.value='showAccount';form.submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_ACCOUNT"); ?></button>
		

	</form>
	<script language="javascript" type="text/javascript">		
		compareAddress(0, 1);
		compareAddress(0, 2);
	</script>
	</div>	
<?php
	}

	
	function showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, &$rowUser, &$rowAccount, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
	
		$database->setQuery( "SELECT #__users.name as text,#__sdi_account.account_id as value FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id = ".$rowAccount->root_id );
		$root_name = $database->loadResult();	
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_title WHERE id = ".$rowContact->title_id ." ORDER BY name" );
		$title = $database->loadResult();

		
		$database->setQuery( "SELECT  name AS text FROM #__sdi_country where country_code = '".$rowContact->country_code."'" );
		 
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
								<td><?php echo $rowAccount->acronym; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_DESCRIPTION"); ?> : </td>
								<td><?php echo $rowAccount->description; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_TEXT_WEBSITE"); ?> : </td>
								<td><?php echo $rowAccount->url; ?></td>
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
				<td><?php echo $rowContact->corporatename1; ?></td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->corporatename2; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JText::_($title);?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><?php echo $rowContact->agentfirstname; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><?php echo $rowContact->agentlastname; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><?php echo $rowContact->function; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><?php echo $rowContact->street1; ?></td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo $rowContact->street2; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><?php echo $rowContact->postalcode; ?>
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <?php echo $rowContact->address_locality; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo $country;?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><?php echo $rowContact->phone; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><?php echo $rowContact->fax; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><?php echo $rowContact->email; ?> </td>								
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
	
		<form action="./index.php?option=<?php echo $option ?>" method="POST" name="accountForm" id="accountForm" class="accountForm">
		<!-- input type="hidden" id="option" name="option" value="" /-->
		<input type="hidden" id="task" name="task" value="" />
		<?php if ($hasTheRightToManageHisOwnAffiliates)
		{ ?>					
		<button type="button" onCLick="var form = document.getElementById('accountForm');form.task.value='editAffiliateAccount';form.submit();" ><?php echo JText::_("EASYSDI_EDIT_ACCOUNT"); ?></button>
		<?php
		} ?>				
	</form>	
<?php
	}


	function editAffiliateAccount( &$rowUser, &$rowAccount, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
	
		$database->setQuery( "SELECT #__users.name as text,#__sdi_account.id as value FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id = ".$rowAccount->root_id );
		$root_name = $database->loadResult();	
		$titles = array();
		$titles[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_TITLE_SELECT") );
		
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_title WHERE id > 0 ORDER BY name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($titles );
		$countries = array();
		$countries[] = JHTML::_('select.option','0',JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries);
		
?>
	
	
	<form action="index.php?option=<?php echo $option; ?>" method="post" name="accountForm" id="accountForm" class="accountForm">
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
								<td><?php echo $rowAccount->id; ?></td>
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
							<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" ){
								?>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_METADATA"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_new_metadata" <?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_NEW_DISTRIBUTION"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_distribution" <?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
							<?php
							}?>
							<tr>
									<td><?php echo JText::_("EASYSDI_NOTIFY_ORDER_READY"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
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
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[0]" value="<?php echo $rowContact->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[0]" value="<?php echo $rowContact->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[0]" value="<?php echo $rowContact->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[0]" value="<?php echo $rowContact->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" />
				&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("EASYSDI_TEXT_EMAIL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>								
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
			
			
			$query = "SELECT id AS value, name AS text FROM #__sdi_role WHERE type_id=".$row->type_id;
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
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_AFFILIATES"),"accountPane");
		?>
<br>
<div class="easysdi_site_aff_list">
<br>
	<fieldset>
		<legend><b><?php echo JText::_("EASYSDI_TEXT_AFFILIATES_LIST"); ?></b></legend>		
		
	<?php	
		$query = "SELECT * FROM #__sdi_account up, #__users u where up.id = '$rowAccount->account_id' AND up.user_id = u.id ORDER BY up.id";
			
				
		$database->setQuery( $query );
		$src_list = $database->loadObjectList();
		if(count($src_list) != 0)
		{	
			userTree::buildTreeView($src_list[0], true);
		}
		//HTML_account::print_child($src_list );
			
		
	
		
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
		<input type="hidden" name="account_id" value="<?php echo $rowAccount->account_id; ?>" />
		<input type="hidden" name="parent_id" value="<?php echo $rowAccount->parent_id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->address_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editAffiliateAccount" />
		<input type="hidden" name="return" value="<?php echo JRequest::getVar('return','showAccount');?>"/>
		<input type="hidden" name="root_id" value="<?php echo $rowAccount->root_id; ?>" />
		<button type="button" onCLick="var form = document.accountForm;form.task.value='saveAffiliateAccount';submitbutton();" ><?php echo JText::_("EASYSDI_SAVE_ACCOUNT"); ?></button>
		<button type="button" onCLick="var form = document.getElementById('accountForm');form.task.value='<?php echo JRequest::getVar('return','showAccount'); ?>';form.submit();" ><?php echo JText::_("EASYSDI_CANCEL_EDIT_ACCOUNT"); ?></button>
		
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
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_title WHERE id > 0 ORDER BY name" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($titles );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT code AS value, name AS text FROM #__sdi_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries );
		$activities = array();
	
?>				
	<form action="index.php?option=<?php echo $option ?>" method="post" name="accountForm" id="accountForm" class="accountForm">

<br>		
		<table width="100%">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_ACCOUNT"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
							<?php		
								SITE_account::includeAccountExtension(0,'TOP','requestAccount',0);
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
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1" value="" /> *</td>
						</tr>
						<tr>
							<td></td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_TITLE"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id', 'size="1" class="inputbox"', 'value', 'text', 0 ); ?> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FIRSTNAME"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname" value="" /> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_LASTNAME"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname" value="" /> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_CONTACT_FUNCTION"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="function" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_ADDRESS"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1" value="" /> *</td>
						</tr>
						<tr>
							<td></td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_POSTALCODE"); ?> : </td>
							<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode" value="" />
							&nbsp;<?php echo JText::_("EASYSDI_TEXT_LOCALITY"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality" value="" /> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_COUNTRY"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id', 'size="1" class="inputbox"', 'value', 'text', 'CH' ); ?> *</td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_PHONE"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone" value="" /></td>
						</tr>
						<tr>
							<td><?php echo JText::_("EASYSDI_TEXT_FAX"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax" value="" /></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>		
 
		<input type="hidden" name="usertype" value="" />
		<input type="hidden" name="gid" value=""/>
		<input type="hidden" name="id" value="" />	
		<input type="hidden" name="account_id" value="" />
		<input type="hidden" name="address_id" value="" />
			
		
		<!-- input type="hidden" name="option" value="<?php echo $option; ?>" / -->
		<input type="hidden" name="task" value="" />						
		<button type="button" onCLick="var form = document.accountForm;form.task.value='createBlockUser';submitbutton();" ><?php echo JText::_("EASYSDI_SAVE_ACCOUNT"); ?></button>
		
	</form>
	
	</div>	
<?php
	}
}

?>
