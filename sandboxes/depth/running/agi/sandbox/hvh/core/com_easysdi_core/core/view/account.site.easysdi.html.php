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
	function createAccount($option, $titles, $countries){
	?>
	<script language="javascript" type="text/javascript">
	function submitBlockUser()
		{
			var form = document.AccountForm;
			if (form.elements['task'].value == "createBlockUser")
			{
				if(form.elements['password'].value != form.elements['password_chk'].value)
				{
					alert( <?php echo "'".JText::_("CORE_PASSWD_CHECK_INVALID")."'"; ?>);
					return;
				}
				// do field validation
				if (form.elements['name'].value == '' 
					|| form.elements['username'].value == '' 
					|| form.elements['email'].value == '' 
					|| (form.elements['password'].value == '' && form.elements['id'].value =='')
					|| form.elements['corporatename1[0]'].value == ''
					|| form.elements['agentfirstname[0]'].value == ''
					|| form.elements['agentfirstname[0]'].value == ''
					|| form.elements['title_id[0]'].value == '0'
				
					|| form.elements['street1[0]'].value == ''
					|| form.elements['locality[0]'].value == ''
					|| form.elements['postalcode[0]'].value == ''
					
					)
				{
					alert( <?php echo "'".JText::_("CORE_REGISTER_MISSING_VALUES")."'"; ?>);
				} else {
					form.submit( );
				}
			}
			
			/*
			
			// do field validation
			if (form.elements['name'].value == '' 
				|| form.elements['username'].value == '' 
				|| form.elements['user_email'].value == '' 
				|| (form.elements['password'].value == '' && form.elements['id'].value =='')
		        ){
				alert( <?php echo "'".JText::_("CORE_REGISTER_MISSING_VALUES")."'"; ?>);
			} else {
				document.getElementById('AccountForm').submit();
			}
			
			//for my account
			if(!form.elements['address_corporate_name1']){
				if(form.elements['address_corporate_name1[0]'].value == ''
				|| form.elements['address_agent_firstname[0]'].value == ''
				|| form.elements['address_agent_lastname[0]'].value == ''
				|| form.elements['title_id[0]'].value == '0'
				|| form.elements['address_street1[0]'].value == ''
				|| form.elements['address_locality[0]'].value == ''
				|| form.elements['address_postalcode[0]'].value == '')
				{
				  alert( <?php echo "'".JText::_("CORE_REGISTER_MISSING_VALUES")."'"; ?>);
			        } else {
				   document.getElementById('partnerForm').submit();
			        }
			//from new user registration
			}else{
			        if(form.elements['address_corporate_name1'].value == ''
				|| form.elements['address_agent_firstname'].value == ''
				|| form.elements['address_agent_lastname'].value == ''
				|| form.elements['title_id'].value == '0'
				|| form.elements['address_street1'].value == ''
				|| form.elements['address_locality'].value == ''
				|| form.elements['address_postalcode'].value == '')
				{
				   alert( <?php echo "'".JText::_("CORE_REGISTER_MISSING_VALUES")."'"; ?>);
			        } else {
				   document.getElementById('partnerForm').submit();
			        }
			
			}
			*/
			
		}
		</script>
	
	
	
	
		<div class="contentin">
		<h2 class="contentheading"> <?php echo JText::_("CORE_REGISTER_ACCOUNT_TITLE"); ?></h2>
		<form action="index.php" method="post" name="AccountForm" id="AccountForm" class="AccountForm">
		<br>
		<table width="100%">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("CORE_ACCOUNT_GEN_INFO"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
							<?php	
								//SITE_account::includeAccountExtension(0,'TOP','requestPartner',0);
							?> 
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_TEXT_ACCOUNT_NAME"); ?> : </td>
								<td><input onblur="$('address_corporate_name1').value = this.value;" class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $_POST["name"]; ?>" /> *</td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_TEXT_USER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="<?php echo $_POST["username"]; ?>" /> *</td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PASSWORD_LABEL"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $_POST["password"]; ?>" /> *</td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PASSWORD_CHECK_LABEL"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password_chk" value="<?php echo $_POST["password_chk"]; ?>" /> *</td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="email" value="<?php echo $_POST["email"]; ?>" /> *</td>
							</tr>																			
						</table>
					</fieldset>
					<fieldset>
						<legend><b><?php echo JText::_("CORE_TEXT_CONTACT_ADRESS"); ?></b></legend>
		
						<table border="0" cellpadding="3" cellspacing="0">			
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_TEXT_ORGANISATION"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" id="address_corporate_name1" name="corporatename1[0]" value="<?php echo $_POST["corporatename1[0]"]; ?>"/> *</td>
						</tr>
						<tr>
							<td class="ptitle"></td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[0]" value="<?php echo $_POST["corporatename2[0]"]; ?>" /></td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_TEXT_CONTACT_TITLE"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $_POST["title_id[0]"] ); ?> *</td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[0]" value="<?php echo $_POST["agentfirstname[0]"]; ?>" /> *</td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[0]" value="<?php echo $_POST["agentlastname[0]"]; ?>" /> *</td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[0]" value="<?php echo $_POST["function[0]"]; ?>" /></td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[0]" value="<?php echo $_POST["street1[0]"]; ?>" /> *</td>
						</tr>
						<tr>
							<td class="ptitle"></td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[0]" value="<?php echo $_POST["street2[0]"]; ?>" /></td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[0]" value="<?php echo $_POST["postalcode[0]"]; ?>" /> *</td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="locality[0]" value="<?php echo $_POST["locality[0]"]; ?>" /> *</td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', 'CH' ); ?> *</td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[0]" value="<?php echo $_POST["phone[0]"]; ?>" /></td>
						</tr>
						<tr>
							<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[0]" value="<?php echo $_POST["fax[0]"]; ?>" /></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>		
		<input type="hidden" name="type_id[]" value="2">
		<input type="hidden" name="task" value="">
		<input type="hidden" name="option" value="<?php echo $option; ?>">
		<button type="button" onClick="document.getElementById('AccountForm').task.value='createBlockUser'; submitBlockUser();" ><?php echo JText::_("CORE_VALIDATE"); ?></button>
	</form>
	
	</div>	
		
		
		
	<?php
	}
	
	
	function showAccount( $hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates,&$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		
		$tabs =& JPANE::getInstance('Tabs');
	?>
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("CORE_FE_ACCOUNT_TITLE"); ?></h2>
	
	<?php
		$profiles = array();
								
		$database->setQuery( "SELECT label AS text FROM #__sdi_title WHERE id = ".$rowContact->title_id ." ORDER BY label" );
		$title = JText::_($database->loadResult());
								
		$database->setQuery( "SELECT label AS text FROM #__sdi_title WHERE id = ".$rowSubscription->title_id ." ORDER BY label" );
		$title_s = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT label AS text FROM #__sdi_title WHERE id = ".$rowDelivery->title_id ." ORDER BY label" );
		$title_d = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT  name AS text FROM #__sdi_list_country where id = '".$rowContact->country_id."'" );		 
		$countryContact = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_list_country where id ='".$rowSubscription->country_id."'" );
		$countrySubscription = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_list_country where id ='".$rowDelivery->country_id."'" );
		$countryDelivery = JText::_($database->loadResult());
		
		
		$activities = array();
?>					
<?php
		echo $tabs->startPane("AccountPane");
		echo $tabs->startPanel(JText::_("CORE_OBJECT_TAB_TITLE_GENERAL"),"AccountPane");
?>
<br>
		<table width="100%">
			<tr>
				<td>
					<fieldset class="Account_properties">
						<legend><?php echo JText::_("CORE_FE_ACCOUNT"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
					
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><?php echo $rowUser->name; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> : </td>
								<td><?php echo $rowUser->username; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><?php echo $rowUser->email; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->acronym; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOGO_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->logo; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><?php echo $rowAccount->description; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->url; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ISREBATE_LABEL"); ?> : </td>
								<td><input class="inputbox" value="1" type="checkbox" disabled="disabled" <?php if ($rowAccount->isrebate == 1) echo " checked"; ?> /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_REBATE_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->rebate; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"AccountPane");
?>
<br>
		<table width="100%">
			<tr>
				<td>
		<fieldset class="Account_properties">
		<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"); ?></b></legend>
		
		<table  border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
				<td><?php echo $rowContact->corporatename1; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><?php echo $rowContact->corporatename2; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JText::_($title); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><?php echo $rowContact->agentfirstname; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><?php echo $rowContact->agentlastname; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><?php echo $rowContact->function; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><?php echo $rowContact->street1; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><?php echo $rowContact->street2; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><?php echo $rowContact->postalcode; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
				<td><?php echo $rowContact->locality; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo $countryContact; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><?php echo $rowContact->phone; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><?php echo $rowContact->fax; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><?php echo $rowContact->email; ?> </td>
			</tr>
		</table>
		</fieldset>
		</td></tr></table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"),"AccountPane");
?>
<br>	
		<table width="100%">
			<tr>
				<td>							
					<fieldset class="Account_properties">
						<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"); ?></b></legend>
						<table  border="0" cellpadding="3" cellspacing="0">			
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->corporatename1; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowSubscription->corporatename2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
								<td><?php echo JText::_($title_s); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->agentfirstname; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->agentlastname; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->function; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->street1; ?></td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowSubscription->street2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->postalcode; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->locality; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
								<td><?php echo $countrySubscription; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->phone; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->fax; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->email; ?> </td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"),"AccountPane") ;
?>
<br>

<table width="100%">
			<tr>
				<td>
					<fieldset class="Account_properties">
						<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"); ?></b></legend>		
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->corporatename1; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowDelivery->corporatename2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
								<td><?php echo JText::_($title_d);?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->agentfirstname; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->agentlastname; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->function; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->street1; ?></td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowDelivery->street2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->postalcode; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->locality; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
								<td><?php echo $countryDelivery; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->phone; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->fax; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
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
			echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"),"AccountPane");
?>
			<br>
			<div class="easysdi_site_aff_list">
			<br>
			<table width="100%">
			<tr>
				<td>
				<fieldset class="Account_properties">
					<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"); ?></b></legend>	
<?php	
					$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";
					$database->setQuery( $query );
					$src_list = $database->loadObjectList();
					
					if(count($src_list) != 0)
					{	
						
						userTree::buildTreeView($src_list[0], true);
					}
					//HTML_Account::print_child($src_list );				
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
		<form action="./index.php?option=<?php echo $option ?>" method="POST" name="AccountForm" id="AccountForm" class="AccountForm">
		<!-- input type="hidden" id="option" name="option" value="" /-->
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" id="tab" name="tab" value="" />
<?php 
		if ($hasTheRightToEdit)
		{ 
?>			
		<button type="button" onCLick="var form = document.getElementById('AccountForm');form.task.value='editAccount';form.submit();" ><?php echo JText::_("CORE_FE_ACCOUNT_EDIT_TITLE"); ?></button>
<?php 
		} 
?>					
		</form>
	
	</div>	
<?php
	}
	
	//function showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, &$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery , $option )
	function showAffiliateAccount($hasTheRightToEdit,$hasTheRightToManageHisOwnAffiliates, &$rowUser, &$rowAccount, $rowContact, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		
		$index = JRequest::getVar('tabIndex', 0);
		$tabs =& JPANE::getInstance('Tabs',array('startOffset'=>$index));
		
?>
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("CORE_FE_ACCOUNT_TITLE"); ?></h2>
	
	<?php
		$profiles = array();
								
		$database->setQuery( "SELECT label AS text FROM #__sdi_title WHERE id = ".$rowContact->title_id ." ORDER BY name" );
		$title = JText::_($database->loadResult());
								
		/*$database->setQuery( "SELECT label AS text FROM #__sdi_title WHERE id = ".$rowSubscription->title_id ." ORDER BY name" );
		$title_s = JText::_($database->loadResult());
		
		$database->setQuery( "SELECT label AS text FROM #__sdi_title WHERE id = ".$rowDelivery->title_id ." ORDER BY name" );
		$title_d = JText::_($database->loadResult());
		*/
		
		$database->setQuery( "SELECT  name AS text FROM #__sdi_list_country where id = '".$rowContact->country_id."'" );		 
		$countryContact = JText::_($database->loadResult());
		
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_list_country where id ='".$rowSubscription->country_id."'" );
		$countrySubscription = JText::_($database->loadResult());
		
		
		$database->setQuery( "SELECT name AS text FROM #__sdi_list_country where id ='".$rowDelivery->country_id."'" );
		$countryDelivery = JText::_($database->loadResult());
		
		
		$activities = array();
?>					
		<form action="./index.php?option=<?php echo $option ?>" method="POST" name="AccountForm" id="AccountForm" class="AccountForm">
<?php
		echo $tabs->startPane("accountPane");
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_GENERAL_LABEL"),"accountPane");
?>
<br>
		<table width="100%">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("CORE_ACCOUNT_FIELDSET_JOOMLA_LABEL"); ?></legend>
						<table  border="0" cellpadding="3" cellspacing="0">
					
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><?php echo $rowUser->name; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> : </td>
								<td><?php echo $rowUser->username; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><?php echo $rowUser->email; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->acronym; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOGO_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->logo; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><?php echo $rowAccount->description; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->url; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ISREBATE_LABEL"); ?> : </td>
								<td><input class="inputbox" value="1" type="checkbox" disabled="disabled" <?php if ($rowAccount->isrebate == 1) echo " checked"; ?> /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_REBATE_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->rebate; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"partnerPane");
?>
<br>
		<table width="100%">
			<tr>
				<td>
		<fieldset class="fieldset_properties">
		<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"); ?></b></legend>
		
		<table  border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_FE_ACCOUNT"); ?> : </td>
				<td><?php echo $rowContact->corporatename1; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><?php echo $rowContact->corporatename2; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JText::_($title); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><?php echo $rowContact->agentfirstname; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><?php echo $rowContact->agentlastname; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><?php echo $rowContact->function; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><?php echo $rowContact->street1; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><?php echo $rowContact->street2; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><?php echo $rowContact->postalcode; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
				<td><?php echo $rowContact->locality; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo $countryContact; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><?php echo $rowContact->phone; ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><?php echo $rowContact->fax; ?> </td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><?php echo $rowContact->email; ?> </td>
			</tr>
		</table>
		</fieldset>
		</td></tr></table>
<?php
		echo $tabs->endPanel();
	/*
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"),"accountPane");
?>
<br>	
		<table width="100%">
			<tr>
				<td>							
					<fieldset class="fieldset_properties">
						<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"); ?></b></legend>
						<table  border="0" cellpadding="3" cellspacing="0">			
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_FE_ACCOUNT"); ?> : </td>
								<td><?php echo $rowSubscription->corporatename1; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowSubscription->corporatename2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
								<td><?php echo JText::_($title_s); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->agentfirstname; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->agentlastname; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->function; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->street1; ?></td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowSubscription->street2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->postalcode; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->locality; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
								<td><?php echo $countrySubscription; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->phone; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->fax; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><?php echo $rowSubscription->email; ?> </td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"),"accountPane") ;
?>
<br>

<table width="100%">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"); ?></b></legend>		
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_FE_ACCOUNT"); ?> : </td>
								<td><?php echo $rowDelivery->corporatename1; ?> </td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowDelivery->corporatename2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
								<td><?php echo JText::_($title_d);?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->agentfirstname; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->agentlastname; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->function; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->street1; ?></td>
							</tr>
							<tr>
								<td class="ptitle"></td>
								<td><?php echo $rowDelivery->street2; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->postalcode; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->locality; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
								<td><?php echo $countryDelivery; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->phone; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->fax; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><?php echo $rowDelivery->email; ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();	
		*/	
		if ($hasTheRightToManageHisOwnAffiliates>0)
		{
			echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"),"accountPane");
?>
			<br/>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td>
				<fieldset class="fieldset_properties">
					<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"); ?></b></legend>	
<?php	
					$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";
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
<?php
			echo $tabs->endPanel(); 
		}
?>		
<?php
		echo $tabs->endPane();
?>
		
		<script language="javascript" type="text/javascript">
		
		window.addEvent('domready', function() {
				initTabs();
		});		
		function initTabs(){
			//Add event handler to reload the page at the good tab
			var aDt = $('AccountForm').getElementsByTagName("dt");
			for (var i=0; i<aDt.length;i++)
			{
				aDt[i].addEvent('click', function(){toggleTabs(i)});
			}
			/*
			aDt[0].addEvent('click', function(){toggleTabs(0)});
			aDt[1].addEvent('click', function(){toggleTabs(1)});
			aDt[2].addEvent('click', function(){toggleTabs(2)});
			aDt[3].addEvent('click', function(){toggleTabs(3)});
			aDt[4].addEvent('click', function(){toggleTabs(4)});*/
		}
		function toggleTabs(id){
			$('tabIndex').value = id;
			if(id == 4)
				$('editAccountButton').style.display = 'none';
			else
				$('editAccountButton').style.display = 'block';
			
		}
		</script>
		
		<!-- input type="hidden" id="option" name="option" value="" /-->
		<input type="hidden" id="task" name="task" value="" />
		<input type="hidden" id="tabIndex" name="tabIndex" value="<?php echo JRequest::getVar('tabIndex'); ?>" />
		<input type="hidden" id="tab" name="tab" value="" />
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
		
<?php 
		if ($hasTheRightToEdit)
		{ 
?>		
		<!-- <button type="button" id="editPartnerButton" onCLick="var form = document.getElementById('partnerForm');form.task.value='editPartner';form.submit();" ><?php //echo JText::_("EASYSDI_EDIT_PARTNER"); ?></button> -->
		<button id="editAccountButton" type="button" onClick="var form = document.getElementById('AccountForm');form.task.value='editAccount';form.submit();" ><?php echo JText::_("CORE_FE_ACCOUNT_EDIT_TITLE"); ?></button>
		
<?php 
		} 
?>					
		</form>
	
	</div>	
<?php
	}
	
	function listAccount( &$rows, $search, $option, $root_Account_id,$types,$type)
	{	
		$database =& JFactory::getDBO();		 
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
?>
	<div id="page">
	<h2 class="contentheading"><?php echo JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"); ?></h2>
	<div class="contentin">
	<form action="index.php" method="GET" id="adminAffiliateAccountForm" name="adminAffiliateAccountForm">
	<h3> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h3>
		<div class="row">
			 <div class="row">
			 	<?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputboxEditAffiliates" onchange="document.getElementById(\'adminAffiliateAccountForm\').submit();"', 'value', 'text', $type ); ?>
			 </div>
			 <div class="row">
				<input type="button" id="newaffiliateaccount_button" name="newaffiliateaccount_button" class="submit" value ="<?php echo JText::_("CORE_NEW_AFFILIATE"); ?>" onClick="document.getElementById('adminAffiliateAccountForm').task.value='createAffiliate';document.getElementById('adminAffiliateAccountForm').submit();"/>
			</div>	 
		 </div>
		<br/>
	<h3><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h3>
		<script>
		function suppressAffiliate_click(name, url){
			text = '<?php echo JText::_("CORE_SHOP_CONFIRM_AFFILIATE_DELETE"); ?>';
			conf = confirm(text.replace('%s',name));
			if(!conf)
				return false;
			window.open(url, '_self');
		}
		</script>
		<table id="affiliateTable" class="box-table">
		<thead>
			<tr>
				<th class='title'><?php echo JText::_("CORE_ACCOUNT_TAB_COL_USER"); ?></th>
				<th class='title'><?php echo JText::_("CORE_ACCOUNT_TAB_COL_ACCOUNT"); ?></th>
				<th class='title'><?php echo JText::_('CORE_ACCOUNT_ACTIONS'); ?></th>				
			</tr>
		</thead>
		<tbody>		
<?php

		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
			
			$deleteErrors = SITE_Account::checkIsAccountDeletable($row->user_id);
			$deleteErrorsTxt = "";
			if (count($deleteErrors) > 0)
				$deleteErrorsTxt = $deleteErrors[0]." ";
			
			$m = 0;
			foreach ($deleteErrors as $err){
				if($m > 0)
				   $deleteErrorsTxt .= $err;
			   	if($m > 0 && $m < (count($deleteErrors)-1))
				   $deleteErrorsTxt .= " ".JText::_("CORE_ACCOUNT_AND")." ";
				$m++;
			}
			
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $row->account_username; ?></td>
				<td><?php echo $row->account_name; ?></td>
				<td class="affiliateActions">
					<div class="logo" title="<?php echo addslashes(JText::_('CORE_ACTION_EDIT_AFFILIATE')); ?>" id="editAffiliate" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&return=listAffiliateAccount&task=editAffiliateById&affiliate_id='.$row->user_id.'&type='.$type.'&search='.addslashes($search))); ?>', '_self');"></div>
					<div class="logo <?php if(count($deleteErrors) == 0) echo "deletableAccount"; else echo "unDeletableAccount";?>" title="<?php if(count($deleteErrors) == 0) echo JText::_('CORE_ACTION_DELETE_AFFILIATE'); else echo $deleteErrorsTxt; ?>" id="deleteAffiliate" <?php if(count($deleteErrors) == 0) echo "onClick=\"return suppressAffiliate_click('".addslashes($row->name)."', '".JRoute::_(displayManager::buildUrl('index.php?option=com_easysdi_core&return=listAffiliateAccount&task=deleteAffiliate&affiliate_id='.$row->user_id.'&type='.$type.'&search='.addslashes($search)))."');\"";?>></div>
				</td>
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
	  	<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			  	 	 
	  </form>
	  </div>
	  </div>
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
		
	function editAccount( &$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery ,$option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$index = JRequest::getVar('tabIndex', 0);
		$tabs =& JPANE::getInstance('Tabs',array('startOffset'=>$index));
		
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
	?>
	
	
	<?php
		$profiles = array();
		
		$titles = array();
		$titles[] = JHTML::_('select.option','0', JText::_("CORE_ACCOUNT_LIST_TITLE_SELECT" ));
		$database->setQuery( "SELECT id AS value, label AS text FROM #__sdi_title WHERE id > 0 ORDER BY label" );
		$titles = array_merge( $titles, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($titles );

		$countries = array();
		$countries[] = JHTML::_('select.option','0', JText::_("CORE_ACCOUNT_LIST_COUNTRY_SELECT") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_list_country ORDER BY name" );
		$countries = array_merge( $countries, $database->loadObjectList() );
		HTML_account::alter_array_value_with_Jtext($countries );
		$activities = array();
?>				
	<div class="contentin">
	<h2 class="contentheading"> <?php echo JText::_("CORE_ACCOUNT_EDIT_TITLE"); ?></h2>
	<form action="index.php?option=<?php echo $option ?>" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("accountPane");
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_GENERAL_LABEL"),"accountPane");
?>
<br>		
		<table width="100%">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("CORE_ACCOUNT_FIELDSET_JOOMLA_LABEL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
					
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowUser->name; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="<?php echo $rowUser->username; ?>" /><input type="hidden" name="old_username" value="<?php echo $rowUser->username; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PASSWORD_LABEL"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $rowUser->password; ?>" /><input type="hidden" name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="user_email" value="<?php echo $rowUser->email; ?>" /></td><input type="hidden" name="old_email" value="<?php echo $rowUser->email; ?>" /></td>
							</tr>											
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOGO_LABEL"); ?> : </td>
								<td><?php echo $rowAccount->logo; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowAccount->description; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="200" name="url" value="<?php echo $rowAccount->url; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" )
							{
?>
								<tr>
									<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_NOTIFYMETADATA_LABEL"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_new_metadata" <?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_NOTIFYDISTRIBUTION_LABEL"); ?> : </td>
									<td class="ptitle"><input value="1" class="inputbox" type="checkbox" name="notify_distribution" <?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
<?php
							}
?>
							<tr>
									<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_NOTIFYORDERREADY_LABEL"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"accountPane");
?>
<br>
		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress" value="">
	
	<table width="100%">
			<tr>
				<td>
		<fieldset class="fieldset_properties">
		<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"); ?></b></legend>
		
		<table border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[0]" value="<?php echo $rowContact->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[0]" value="<?php echo $rowContact->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[0]" value="<?php echo $rowContact->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[0]" value="<?php echo $rowContact->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
			</tr>
		</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"),"accountPane");
?>
<br>
		<input type="hidden" name="type_id[]" value="2">			
	<table width="100%">
	<tr>
		<td>		
		<fieldset class="fieldset_properties">
		<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"); ?></b></legend>
		<table border="0" cellpadding="3" cellspacing="0">
			
			<tr>
				<td class="ptitle"></td>
				<td><input type="checkbox" name="sameAddress1" onClick="javascript:changeAddress(this.checked, 1)"><?php echo JText::_("CORE_ACCOUNT_SAMEADDRESS_LABEL"); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[1]" value="<?php echo $rowSubscription->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[1]" value="<?php echo $rowSubscription->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->title_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[1]" value="<?php echo $rowSubscription->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[1]" value="<?php echo $rowSubscription->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[1]" value="<?php echo $rowSubscription->function; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[1]" value="<?php echo $rowSubscription->street1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[1]" value="<?php echo $rowSubscription->street2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[1]" value="<?php echo $rowSubscription->postalcode; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="locality[1]" value="<?php echo $rowSubscription->locality; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->country_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[1]" value="<?php echo $rowSubscription->phone; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[1]" value="<?php echo $rowSubscription->fax; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[1]" value="<?php echo $rowSubscription->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
	</tr>
</table>
		
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"),"accountPane");
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
		<fieldset class="fieldset_properties">
		<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"); ?></b></legend>		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td class="ptitle"></td>
				<td><input type="checkbox" name="sameAddress2" onClick="javascript:changeAddress(this.checked, 2)"><?php echo JText::_("CORE_ACCOUNT_SAMEADDRESS_LABEL"); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[2]" value="<?php echo $rowDelivery->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[2]" value="<?php echo $rowDelivery->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->title_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[2]" value="<?php echo $rowDelivery->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[2]" value="<?php echo $rowDelivery->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[2]" value="<?php echo $rowDelivery->function; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[2]" value="<?php echo $rowDelivery->street1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[2]" value="<?php echo $rowDelivery->street2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[2]" value="<?php echo $rowDelivery->postalcode; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="locality[2]" value="<?php echo $rowDelivery->locality; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->country_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[2]" value="<?php echo $rowDelivery->phone; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[2]" value="<?php echo $rowDelivery->fax; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
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
		<div class="row">
			 <div class="row">
				<input type="button" id="save_button" name="save_button" class="submit" value ="<?php echo JText::_("CORE_SAVE"); ?>" onClick="var form = document.adminForm;form.task.value='saveAccount';submitbutton();"/>
				<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="var form = document.getElementById('adminForm');form.task.value='showAccount';form.submit();"/>
			</div>	 
		 </div>
		 
		<input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" />
		<input type="hidden" name="logo" value="<?php echo $rowAccount->logo;  ?>" />
		<input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>"/>
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />	
		<input type="hidden" name="account_id" value="<?php echo $rowAccount->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowSubscription->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowDelivery->id; ?>" />				
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
				
		<!-- input type="hidden" name="option" value="<?php echo $option; ?>" / -->
		<input type="hidden" name="task" value="" />
		

	</form>
	<script language="javascript" type="text/javascript">		
		compareAddress(0, 1);
		compareAddress(0, 2);
	</script>
	</div>	
<?php
	}
	
	function editRootAccount( &$rowUser, &$rowAccount, $rowContact, $rowSubscription, $rowDelivery, $option )
	{
		global  $mainframe;
		$database =& JFactory::getDBO();

		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		
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
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("accountPane");
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_GENERAL_LABEL"),"accountPane");
?>
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(0,'TOP','editAccount',$rowAccount->id);						
		?>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("CORE_ACCOUNT_FIELDSET_JOOMLA_LABEL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td ><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowUser->id; ?></td>
							</tr>
		
							<tr>							
								<td><?php echo JText::_("CORE_ACCOUNT_USERJOOMLA_LABEL"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$usersJoomla, 'JId', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'editRootAccount\');"', 'value', 'text',$rowUser->id); ?></td>							
							
							</tr>					
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowUser->name; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="<?php echo $rowUser->username; ?>" /><input type="hidden" name="old_username" value="<?php echo $rowUser->username; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_PASSWORD_LABEL"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $rowUser->password; ?>" /><input type="hidden" name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="user_email" value="<?php echo $rowUser->email; ?>" /><input type="hidden" name="old_email" value="<?php echo $rowUser->email; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_GROUP_LABEL"); ?> : </td>
								<td><?php echo $rowUser->usertype." [".$rowUser->gid."]" ; ?><input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" /><input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<fieldset>
						<legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td ><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowAccount->id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_CODE"); ?> : </td>
								<td><?php echo $rowAccount->guid; ?><input type="hidden" name="guid" value="<?php echo $rowAccount->guid; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_LOGO_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="logo" value="<?php echo $rowAccount->logo; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowAccount->description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="200" name="url" value="<?php echo $rowAccount->url; ?>" /></td>
							</tr>
							
							<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" ){
								?>
								<tr>
									<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYMETADATA_LABEL"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_new_metadata" <?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYDISTRIBUTION_LABEL"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_distribution" <?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
							<?php
							}?>
							<tr>
									<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYORDERREADY_LABEL"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
							</tr>
							<tr>							
									<td><?php echo JText::_("CORE_ACCOUNT_ISREBATE_LABEL"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="isrebate" <?php if ($rowAccount->isrebate == 1) echo " checked"; ?> /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_REBATE_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="rebate" value="<?php echo $rowAccount->rebate; ?>" /></td>
							</tr>
							
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php 
		//Include BOTTOM extension		
		
		SITE_account::includeAccountExtension(0,'BOTTOM','editAccount',$rowAccount->id);
								
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"accountPane");
?>
		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress" value="">
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(1,'TOP','editAccount',$rowAccount->id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
		
		<table border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td><?php echo JText::_("CORE_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[0]" value="<?php echo $rowContact->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[0]" value="<?php echo $rowContact->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[0]" value="<?php echo $rowContact->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[0]" value="<?php echo $rowContact->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" />
				&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(1,'BOTTOM','editAccount',$rowAccount->id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_BILLING_LABEL"),"accountPane");
?>
		<input type="hidden" name="type_id[]" value="2">
		
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(2,'TOP','editAccount',$rowAccount->id);						
		
		?>
		<fieldset>
		<legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>
		<table border="0" cellpadding="3" cellspacing="0">			
			<tr>
				<td></td>
				<td><input type="checkbox" name="sameAddress1"   onClick="javascript:changeAddress(this.checked, 1)"><?php echo JText::_("CORE_ACCOUNT_SAMEADDRESS_LABEL"); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[1]" value="<?php echo $rowSubscription->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[1]" value="<?php echo $rowSubscription->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[1]" value="<?php echo $rowSubscription->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[1]" value="<?php echo $rowSubscription->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[1]" value="<?php echo $rowSubscription->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[1]" value="<?php echo $rowSubscription->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[1]" value="<?php echo $rowSubscription->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[1]" value="<?php echo $rowSubscription->postalcode; ?>" />
				&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[1]" value="<?php echo $rowSubscription->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[1]', 'size="1" class="inputbox"', 'value', 'text', $rowSubscription->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[1]" value="<?php echo $rowSubscription->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[1]" value="<?php echo $rowSubscription->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[1]" value="<?php echo $rowSubscription->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(2,'BOTTOM','editAccount',$rowAccount->id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_DELIVERY_LABEL"),"accountPane");
?>
		<input type="hidden" name="type_id[]" value="3">
		
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(3,'TOP','editAccount',$rowAccount->id);						
		?>
		<fieldset>
		<legend><b><?php echo JText::_("CORE_ACCOUNT_FIELDSET_EASYSDI_LABEL"); ?></b></legend>		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td></td>
				<td><input type="checkbox"  name="sameAddress2"   onClick="javascript:changeAddress(this.checked, 2)"><?php echo JText::_("CORE_ACCOUNT_SAMEADDRESS_LABEL"); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_NAME"); ?> : </td>
				<td><input class="inputbox"  type="text" size="50" maxlength="100" name="corporatename1[2]" value="<?php echo $rowDelivery->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox"  type="text" size="50" maxlength="100" name="corporatename2[2]" value="<?php echo $rowDelivery->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[2]" value="<?php echo $rowDelivery->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[2]" value="<?php echo $rowDelivery->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[2]" value="<?php echo $rowDelivery->function; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[2]" value="<?php echo $rowDelivery->street1; ?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[2]" value="<?php echo $rowDelivery->street2; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[2]" value="<?php echo $rowDelivery->postalcode; ?>" />
				&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[2]" value="<?php echo $rowDelivery->locality; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[2]', 'size="1" class="inputbox"', 'value', 'text', $rowDelivery->country_id ); ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[2]" value="<?php echo $rowDelivery->phone; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[2]" value="<?php echo $rowDelivery->fax; ?>" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[2]" value="<?php echo $rowDelivery->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(3,'BOTTOM','editAccount',$rowAccount->id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_RIGHTS_LABEL"),"accountPane");
?>
<?php 
		//Include TOP extension		
		SITE_account::includeAccountExtension(4,'TOP','editAccount',$rowAccount->id);						
		?>
		<table border="0" cellpadding="0" cellspacing="0">		
<?php
		$database->setQuery( "SELECT DISTINCT type FROM #__sdi_list_role ORDER BY type" );
		$rows = $database->loadObjectList();
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];
?>
			<tr>
				<td>
					<fieldset>
						<legend><?php echo $row->type ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			$rights = array();
			$database->setQuery( "SELECT id AS value, name AS text FROM #__sdi_list_role WHERE type='".$row->type."' ORDER BY name" );
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
		SITE_account::includeAccountExtension(4,'BOTTOM','editAccount',$rowAccount->id);						
		?>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"),"accountPane");

			$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";
			
			$database->setQuery( $query );
			$user = JFactory::getUser();
			$src_list = $database->loadObjectList();
			if(count($src_list) != 0)
			{	
				userTree::buildTreeView($src_list[0], true);
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
		
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
	</form>

	<script language="javascript" type="text/javascript">
		compareAddress(0, 1);
		compareAddress(0, 2);
	</script>
<?php
	}
				

						
	function editAffiliateAccount( &$rowUser, &$rowAccount, $rowContact, $rowsProfile, $rowsAccountProfile, $option )
	{
		global  $mainframe;

		$database =& JFactory::getDBO(); 
		
		$user =& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		
		$index = JRequest::getVar('tabIndex');
		$tabs =& JPANE::getInstance('Tabs',array('startOffset'=>$index));
	
		$database->setQuery( "SELECT #__users.name as text,#__sdi_account.id as value FROM #__users,#__sdi_account WHERE #__users.id=#__sdi_account.user_id AND #__sdi_account.id = ".$rowAccount->root_id );
		$root_name = $database->loadResult();	
		
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
			
?>
	<div class="contentin">
	<?php
	if ($rowUser->id)
	{ ?>
	<h2 class="contentheading"> <?php echo JText::_("CORE_ACCOUNT_AFFILIATE_EDIT_TITLE"); ?></h2>
	<?php
	}
	else
	{ ?>
	<h2 class="contentheading"> <?php echo JText::_("EASYSDI_CREATE_AFFILIATE_TITLE"); ?></h2>
	<?php
	} ?>
	
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("affiliatePane");
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_GENERAL_LABEL"),"affiliatePane");
?>
<br>
		<table width ="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_("CORE_ACCOUNT_FIELDSET_JOOMLA_LABEL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowUser->id; ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ACCOUNT_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $rowUser->name; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_USER_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="username" value="<?php echo $rowUser->username; ?>" /><input type="hidden" name="old_username" value="<?php echo $rowUser->username; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PASSWORD_LABEL"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="100" name="password" value="<?php echo $rowUser->password; ?>" /><input type="hidden" name="old_password" value="<?php echo $rowUser->password; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="user_email" value="<?php echo $rowUser->email; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $rowAccount->id; ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_ACRONYM_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="acronym" value="<?php echo $rowAccount->acronym; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $rowAccount->description; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_ACCOUNT_WEBSITE_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="200" name="url" value="<?php echo $rowAccount->url; ?>" /></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_REGISTER_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->registerDate)); ?></td>
							</tr>
							<tr>
								<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTVISIT_LABEL"); ?> : </td>
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowUser->lastvisitDate)); ?></td>
							</tr>
							<?php 
							if ($rowUser->usertype == "Administrator" || $rowUser->usertype == "Super Administrator" ){
								?>
								<tr>
									<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYMETADATA_LABEL"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_new_metadata" <?php if ($rowAccount->notify_new_metadata == 1) echo " checked"; ?> /></td>
								</tr>
								<tr>
									<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYDISTRIBUTION_LABEL"); ?> : </td>
									<td><input value="1" class="inputbox" type="checkbox" name="notify_distribution" <?php if ($rowAccount->notify_distribution == 1) echo " checked"; ?> /></td>
								</tr>
							<?php
							}?>
							<tr>
									<td><?php echo JText::_("CORE_ACCOUNT_NOTIFYORDERREADY_LABEL"); ?> : </td>
									<td><input class="inputbox" value="1" type="checkbox" name="notify_order_ready" <?php if ($rowAccount->notify_order_ready == 1) echo " checked"; ?> /></td>
							</tr>
							
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_CONTACT_LABEL"),"affiliatePane");
?>
		<input type="hidden" name="type_id[]" value="1">
		<input type="hidden" name="sameAddress" value="">
		
		<table width ="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
		
		<fieldset class="fieldset_properties">
		<legend><b><?php echo JText::_("EASYSDI_TEXT_CONTACT_ADRESS"); ?></b></legend>
		
		<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_NAME"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename1[0]" value="<?php echo $rowContact->corporatename1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="corporatename2[0]" value="<?php echo $rowContact->corporatename2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_CONTACT_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$titles, 'title_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->title_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FIRSTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentfirstname[0]" value="<?php echo $rowContact->agentfirstname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_LASTNAME_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="agentlastname[0]" value="<?php echo $rowContact->agentlastname; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FUNCTION_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="function[0]" value="<?php echo $rowContact->function; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_ADDRESS_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street1[0]" value="<?php echo $rowContact->street1; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"></td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="street2[0]" value="<?php echo $rowContact->street2; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_POSTALCODE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="5" maxlength="5" name="postalcode[0]" value="<?php echo $rowContact->postalcode; ?>" />
				&nbsp;<?php echo JText::_("CORE_ACCOUNT_LOCALITY_LABEL"); ?> : <input class="inputbox" type="text" size="50" maxlength="100" name="locality[0]" value="<?php echo $rowContact->locality; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_COUNTRY_LABEL"); ?> : </td>
				<td><?php echo JHTML::_("select.genericlist",$countries, 'country_id[0]', 'size="1" class="inputbox"', 'value', 'text', $rowContact->country_id ); ?></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_PHONE_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="phone[0]" value="<?php echo $rowContact->phone; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_FAX_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="fax[0]" value="<?php echo $rowContact->fax; ?>" /></td>
			</tr>
			<tr>
				<td class="ptitle"><?php echo JText::_("CORE_ACCOUNT_EMAIL_LABEL"); ?> : </td>
				<td><input class="inputbox" type="text" size="50" maxlength="100" name="email[0]" value="<?php echo $rowContact->email; ?>" /></td>
			</tr>
		</table>
		</fieldset>
		</td>
		</tr></table>
<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_ACCOUNT_TAB_RIGHTS_LABEL"),"affiliatePane");
?>
<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
				<td>
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_('CORE_ACCOUNTPROFILE_TITLE'); ?></legend>
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
					<fieldset class="fieldset_properties">
						<legend><?php echo JText::_($row->name); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
<?php
			$rights = array();
			
			require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'common'.DS.'easysdi.config.php');
			
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
?>
<br/>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
   <td>
	<fieldset class="fieldset_properties">
		<legend><b><?php echo JText::_("CORE_ACCOUNT_TAB_AFFILIATES_LABEL"); ?></b></legend>		
		
	<?php	
		$query = "SELECT *, up.id as account_id FROM #__sdi_account up, #__users u where up.id = '$rowAccount->id' AND up.user_id = u.id ORDER BY up.id";
		$database->setQuery( $query );
		$user = JFactory::getUser();
		$src_list = $database->loadObjectList();
			
		if(count($src_list) != 0)
		{	
			userTree::buildTreeView($src_list[0], true);
		}
		//HTML_account::print_child($src_list );
?>
	</fieldset>
	</td>
	</tr>
	</table>
		<?php	
		echo $tabs->endPanel();
		echo $tabs->endPane();
?>

		<div class="row">
			 <div class="row">
				<input type="button" id="save_button" name="save_button" class="submit" value ="<?php echo JText::_("CORE_SAVE"); ?>" onClick="var form = document.adminForm;form.task.value='saveAffiliateAccount';submitbutton();"/>
				<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="window.history.go(-1);"/>
			</div>	 
		 </div>
		 
		
		<input type="hidden" name="id" value="<?php echo $rowUser->id; ?>" />
		<input type="hidden" name="type" value="<?php echo JRequest::getVar('type'); ?>" />
		<input type="hidden" name="account_id" value="<?php echo $rowAccount->id; ?>" />
		<input type="hidden" name="address_id[]" value="<?php echo $rowContact->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="editAffiliateAccount" />
		
		<input type="hidden" name="usertype" value="<?php echo $rowUser->usertype; ?>" />
		<input type="hidden" name="gid" value="<?php echo $rowUser->gid; ?>"/>
		<input type="hidden" name="parent_id" value="<?php echo $rowAccount->parent_id; ?>" />
		<input type="hidden" name="return" value="<?php echo JRequest::getVar('return','showAccount');?>"/>
		<input type="hidden" name="search" value="<?php echo JRequest::getVar('search');?>"/>
		<input type="hidden" name="root_id" value="<?php echo $rowAccount->root_id; ?>" />
		
		<input type="hidden" name="ordering" value="<?php echo $rowAccount->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo ($rowAccount->created)? $rowAccount->created : date ('Y-m-d H:i:s');?>" />
		<input type="hidden" name="createdby" value="<?php echo ($rowAccount->createdby)? $rowAccount->createdby : $user->id; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo ($rowAccount->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo ($rowAccount->createdby)? $user->id : ''; ?>" />
		
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
				
	</form>
	</div>
<?php
	}

	
	
}
	
?>


