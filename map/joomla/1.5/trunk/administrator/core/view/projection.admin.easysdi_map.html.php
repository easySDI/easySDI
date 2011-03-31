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

class HTML_projection 
{
	function listProjection( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_PROJECTION"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchProjection" id="searchProjection" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchProjection').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_PROJECTION_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_PROJECTION_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_PROJECTION_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_PROJECTION_ENABLE"), 'enable', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editProjection')"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
				<td><input type="checkbox" disabled id="isEnable<?php echo $i;?>" name="isEnable<?php echo $i;?>" value="" <?php if($row->enable == 1)echo " checked" ?> /></td>
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		?></tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="projection" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editProjection ($projection, $createUser, $updateUser,$fieldsLength, $option)
	{
		if ($projection->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_PROJECTION").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_PROJECTION").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>	
		<script>	
		function submitbutton(pressbutton)
		{
			if(pressbutton== "saveProjection")
			{
				if (document.getElementById('name').value == "")
				{	
					alert ('<?php echo  JText::_( 'MAP_PROJECTION_NAME_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('description').value == "")
				{
					alert ('<?php echo  JText::_( 'MAP_PROJECTION_TITLE_VALIDATION_ERROR');?>');	
					return;
				}			
				else
				{	
					submitform(pressbutton);
				}
			}
			else
			{	
				submitform(pressbutton);
			}
		}
		</script>			
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<fieldset>
							<table class="admintable">
								<tr>
									<td  class="key" width="100p"><?php echo JText::_("MAP_PROJECTION_NAME"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $projection->name; ?>" /></td>								
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_PROJECTION_DESCRIPTION"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $projection->description; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_PROJECTION_CODE"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['code'];?>" name="code" id="code" value="<?php echo $projection->code; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_PROJECTION_TEXT"); ?></td>
									<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['proj4text'];?>" name="proj4text" id="proj4text" value="<?php echo $projection->proj4text; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_PROJECTION_DIGIT"); ?></td>
									<td><input class="inputbox" type="text" size="2" maxlength="<?php echo $fieldsLength['numDigits'];?>" name="numDigits" id="numDigits" value="<?php echo $projection->numDigits; ?>" /></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("MAP_PROJECTION_ENABLE"); ?></td>
									<td><input class="inputbox" value="1" type="checkbox" name="enable" id="enable"  <?php if ($projection->enable == 1) echo " checked"; ?> /></td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<br></br>
			<table border="0" cellpadding="3" cellspacing="0">
			<?php
			if ($projection->created)
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
					<td><?php if ($projection->created) {echo date('d.m.Y h:i:s',strtotime($projection->created));} ?></td>
					<td>, </td>
					<td><?php echo $createUser; ?></td>
				</tr>
			<?php
			}
			if ($projection->updated and $projection->updated<> '0000-00-00 00:00:00')
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($projection->updated and $projection->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($projection->updated));} ?></td>
					<td>, </td>
					<td><?php echo $updateUser; ?></td>
				</tr>
			<?php
			}
			?>		
		</table>
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $projection->id; ?>" />
			<input type="hidden" name="guid" value="<?php echo $projection->guid?>" />
			<input type="hidden" name="ordering" value="<?php echo $projection->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo $projection->created;?>" />
			<input type="hidden" name="createdby" value="<?php echo $projection->createdby; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo $projection->created; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo $projection->createdby; ?>" /> 
		</form>
		<?php
	}
}
?>