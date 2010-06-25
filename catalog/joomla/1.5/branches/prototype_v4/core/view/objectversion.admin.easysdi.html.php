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


class HTML_objectversion {
	
	function newObjectVersion($object_id, $fieldsLength, $metadata_guid, $listVersionNames, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" name="metadata_guid" value="<?php echo $metadata_guid; ?>" disabled="disabled" /></td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_NAME"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name"/></td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"></textarea></td>								
				</tr>
			</table>
			
			<input type="hidden" name="object_id" value="<?php echo $object_id?>" />
			<input type="hidden" name="metadata_guid" value="<?php echo $metadata_guid?>" />
			<input type="hidden" name="created" value="<?php echo date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo $user->id; ?>" /> 
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="" />
			
			<input type="hidden" name="versionNames" value="<?php echo implode(", ", $listVersionNames);?>" />
		</form>
			<?php 	
	}
}
?>