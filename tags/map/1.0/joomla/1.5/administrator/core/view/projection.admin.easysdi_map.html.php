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
	function listProjection($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROJECTION"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_PROJECTION_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROJECTION_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROJECTION_TITLE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROJECTION_ENABLE"); ?></th>
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
				<td><?php echo $row->title; ?></td>
				<td><input type="checkbox" disabled id="isEnable<?php echo $i;?>" name="isEnable<?php echo $i;?>" value="" <?php if($row->enable == 1)echo " checked" ?> /></td>
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
	  	<input type="hidden" name="task" value="projection" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editProjection ($projection, $option)
	{
		if ($projection->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_EDIT_PROJECTION"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_NEW_PROJECTION"), 'generic.png' );
		}
		

	?>	
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton== "saveProjection")
		{
			if (document.getElementById('name').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_PROJECTION_NAME_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('title').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_PROJECTION_TITLE_VALIDATION_ERROR');?>');	
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
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_PROJECTION_ID"); ?></td>
								<td><?php echo $projection->id; ?></td>								
							</tr>
							<tr>
								<td  class="key" width="100p"><?php echo JText::_("EASYSDI_PROJECTION_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="200" name="name" id="name" value="<?php echo $projection->name; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_PROJECTION_TITLE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="200" name="title" id="title" value="<?php echo $projection->title; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_PROJECTION_TEXT"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="500" name="proj4text" id="proj4text" value="<?php echo $projection->proj4text; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_PROJECTION_DIGIT"); ?></td>
								<td><input class="inputbox" type="text" size="2" maxlength="2" name="numDigits" id="numDigits" value="<?php echo $projection->numDigits; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_PROJECTION_ENABLE"); ?></td>
								<td><input class="inputbox" value="1" type="checkbox" name="enable" id="enable"  <?php if ($projection->enable == 1) echo " checked"; ?> /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $projection->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
<?php
	}
}
?>