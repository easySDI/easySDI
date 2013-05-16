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
	function listProfile( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_PROFILE"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchProfile" id="searchProfile" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchProfile').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_PROFILE_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_PROFILE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_PROFILE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editProfile')"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		
		?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="profile" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editProfile ($profile,$rowsRoles,$rowsSelectedRoles, $option)
	{
		HTML_profile::alter_array_value_with_Jtext($rowsRoles);
		JToolBarHelper::title( JText::_("MAP_EDIT_RIGHT_PROFILE").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		
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
								<td class="key" width="100p"><?php echo JText::_("MAP_PROFILE_NAME"); ?></td>
								<td><?php echo $profile->name; ?></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_PROFILE_DESCRIPTION"); ?></td>
								<td><?php echo $profile->description; ?></td>	
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_PROFILE_ROLE"); ?></td>
								<td><?php echo JHTML::_("select.genericlist",$rowsRoles, 'roles[]', 'size="20" multiple="true" class="selectbox"', 'value', 'text', $rowsSelectedRoles ); ?></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $profile->id; ?>" />
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