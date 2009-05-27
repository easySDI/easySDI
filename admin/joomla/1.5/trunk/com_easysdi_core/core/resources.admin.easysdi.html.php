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

class HTML_resources {
	
	
	function listResources($use_pagination, &$rows, &$pageNav, $option)
	{				
		$database =& JFactory::getDBO();
		 
		JToolBarHelper::title(JText::_("EASYSDI_LIST_RESOURCES"));
?>
	<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_RESOURCE_LANGUAGE"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_RESOURCE_CURRENT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_RESOURCE_COMPONENT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_RESOURCE_UPDATEDATE"); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		foreach ($rows as $row)
		{		
?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $language;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
<?php
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
	
	
	function editResource( &$rowConfig,$option )
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
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="thekey" value="<?php echo $rowConfig->thekey; ?>" /></td>								
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

}
	
?>
