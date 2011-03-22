<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

class HTML_resources {
	
	function editResource($file, &$content, $option )
	{
		global  $mainframe;
		?>
		<form action="index.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td ><table class="adminheading">
				<tr>
					<th class="langmanager">
					
					<?php echo JText::_(MAP_EDIT_RESOURCE); ?><span class="componentheading">
						&nbsp;[<?php echo basename($file).' : '; ?>
						<?php echo is_writable($file) ? '<b><font color="green"> '.JText::_(MAP_RESOURCE_WRITABLE).' </font></b>' : '<font color="red"> '.JText::_(MAP_RESOURCE_NONWRITABLE).' </font></b>' ?>]
					</span>
					</th>
			</tr></table></td>
			<?php
			jimport('joomla.filesystem.path');
			
			if (JPath::canChmod($file)) {
				if (is_writable($file)) { ?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<input type="hidden" name="enable_write" value="0" />
				<label for="disable_write"><?php echo JText::_(MAP_RESOURCE_CHANGENOTWRITABLE); ?></label>
			</td>
				<?php } 
				else
				{ ?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<input type="hidden" name="disable_write" value="0" />
				<label for="enable_write"><?php echo JText::_(MAP_RESOURCE_IGNOREWRITABLE); ?></label>
			</td>
			<?php
				} // if
			} // if
			?>
		</tr>
		</table>
		
		<table class="adminform">
			<tr><th><?php echo $file; ?></th></tr>
			<tr><td><textarea style="width:100%" cols="110" rows="50" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="filename" value="<?php echo $file; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="editResource" />
		</form>
	<?php
	}

}
	
?>
