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

class HTML_profile 
{
	function listProfile($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROFILE"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_MAP_PROFILE_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_PROFILE_CODE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_PROFILE_DESCRIPTION"); ?></th>
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
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->profile_id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editProfile')"><?php echo $row->profile_code; ?></a></td>
				<td><?php echo $row->profile_description; ?></td>
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
	  	<input type="hidden" name="task" value="profile" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editProfile ($profile,$rowsRoles,$rowsSelectedRoles, $option)
	{
		HTML_profile::alter_array_value_with_Jtext($rowsRoles);
		JToolBarHelper::title( JText::_("EASYSDI_MAP_EDIT_RIGHT_PROFILE"), 'generic.png' );
		
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
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_PROFILE_ID"); ?></td>
								<td><?php echo $profile->profile_id; ?></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_PROFILE_CODE"); ?></td>
								<td><?php echo $profile->profile_code; ?></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_PROFILE_DESCRIPTION"); ?></td>
								<td><?php echo $profile->profile_description; ?></td>	
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_PROFILE_ROLE"); ?></td>
								<td><?php echo JHTML::_("select.genericlist",$rowsRoles, 'roles[]', 'size="20" multiple="true" class="selectbox"', 'value', 'text', $rowsSelectedRoles ); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $profile->profile_id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
<?php
	}
	
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
}
?>