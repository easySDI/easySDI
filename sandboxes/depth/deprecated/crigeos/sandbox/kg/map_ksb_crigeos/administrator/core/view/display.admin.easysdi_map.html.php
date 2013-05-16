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

class HTML_display 
{
	function listDisplay( $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("MAP_DISPLAY_OPTION"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<script>
		function saveChanges (id)
		{
			document.getElementById('id').value = id;
			if (document.getElementById('isEnable'+id).checked)
			{
				document.getElementById('enable').value = 1;
			}
			else
			{
				document.getElementById('enable').value = 0;
			}  
			submitform('saveDisplay');
		}
		</script>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_DISPLAY_SHARP"); ?></th>
				<th class='title'><?php echo JText::_("MAP_DISPLAY_TRANSLATION"); ?></th>
				<th class='title'><?php echo JText::_("MAP_PROJECTION_ENABLE"); ?></th>
				
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
				<td><?php echo JText::_($row->code); ?></td>
				<td><input type="checkbox"  id="isEnable<?php echo $row->id;?>" name="isEnable<?php echo $row->id;?>" value="" <?php if($row->enable == 1)echo " checked" ?> onChange="saveChanges(<?php echo $row->id; ?>);" /></td>
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
	  	<input type="hidden" name="task" value="display" />
	  	<input type="hidden" name="id" id="id" value="" />
	  	<input type="hidden" name="enable" id="enable" value="" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
}
?>