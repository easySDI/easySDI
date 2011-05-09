<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

class HTML_systemaccount 
{
	
	
	function editSystemAccount ($system_account, $account, $rowsAccount, $option, $code)
	{
		if($code == "guest")
			JToolBarHelper::title( JText::_("CORE_GUEST_ACCOUNT_EDIT"), 'generic.png' );
		else
			JToolBarHelper::title( JText::_("CORE_SERVICE_ACCOUNT_EDIT"), 'generic.png' );
	?>			
	<script>	
	
	</script>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>						
						<table class="admintable">
							<tr>
								<td class="key" width="100p"><?php echo JText::_("CORE_SYSTEM_ACCOUNT_USER_ID"); ?></td>
								<td><?php echo $account->account_id; ?></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("CORE_SYSTEM_ACCOUNT_NAME"); ?></td>
								<td><?php echo $account->name; ?></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_SYSTEM_ACCOUNT_USER_NAME"); ?></td>
								<td><?php echo $account->username; ?></td>	
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_SYSTEM_ACCOUNT_USER_TYPE"); ?></td>
								<td><?php echo $account->usertype; ?></td>	
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_SYSTEM_ACCOUNT_CHANGE"); ?></td>
								<td><?php echo JHTML::_("select.genericlist",$rowsAccount, 'account_id', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'systemAccount\');"', 'value', 'text',$account->account_id); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $system_account->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="code" value="<?php echo $code; ?>" />
		<input type="hidden" name="guid" value="<?php echo $system_account->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $system_account->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $system_account->createdby; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $system_account->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $system_account->checked_out_time; ?>" />
	</form>
	
<?php
	}
	

}
?>