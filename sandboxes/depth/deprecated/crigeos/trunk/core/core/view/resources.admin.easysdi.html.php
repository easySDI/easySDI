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
	
	
	function listResources(&$rows, &$pageNav, $option, $search, $use_pagination)
	{			
?>
	<form action="index.php" method="GET" name="adminForm">

		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("CORE_FILTER");?></b>&nbsp;
					<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "CORE_GO" ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( "CORE_RESET" ); ?></button>			
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="50px" class='title'><?php echo JText::_("CORE_RESOURCES_TAB_COL_LANGUAGE"); ?></th>
				<th width="30px" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("CORE_RESOURCES_TAB_COL_CURRENT"); ?></th>
				<!-- <th class='title'><?php //echo JText::_("CORE_RESOURCES_TAB_COL_SIDE"); ?></th> -->
				<th class='title'><?php echo JText::_("CORE_RESOURCES_TAB_COL_COMPONENT"); ?></th>
				<th class='title'><?php echo JText::_("CORE_UPDATED"); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		foreach ($rows as $row)
		{		
?>
			<tr>
				<td width="50px" align="center"><?php echo $row->language; ?></td>
				<td width="30px" align="center"><input type="checkbox" id="cb<?php echo key($rows);?>" name="filename" value="<?php echo $row->filename; ?>" onclick="isChecked(this.checked);" /></td>
				<td align="center"><?php
				if ($row->published == 1) {	 ?>
					<img src="images/tick.png" alt="<?php echo JText::_("CORE_RESOURCES_TAB_ALT_CURRENT"); ?>"/>
					<?php
				} else {
					?>
					&nbsp;
				<?php
				}
				?></td>
				<!-- <td><?php //echo $row->side; ?></td>-->
				<td><a href="index.php?option=com_easysdi_core&task=editResource&filename=<?php echo $row->filename; ?>"><?php echo $row->component;  ?></a></td>
				<td><?php echo $row->updatedate; ?></td>
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
	  	<input type="hidden" name="task" value="listResources" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  </form>
<?php
	}
	
	
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
					
					<span class="componentheading">
						&nbsp;[<?php echo basename($file); //.' : '; ?>
						<?php //echo is_writable($file) ? '<b><font color="green"> '.JText::_("CORE_RESOURCES_WRITABLE").'</font></b>' : '<font color="red"> '.JText::_("CORE_RESOURCES_READONLY").'</font></b>' ?>]
					</span>
					</th>
			</tr></table></td>
			<?php
			/*
			jimport('joomla.filesystem.path');
			if (JPath::canChmod($file)) {
				if (is_writable($file)) { ?>
			<td align="right">
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<input type="hidden" name="enable_write" value="0" />
				<label for="disable_write"><?php echo JText::_("CORE_RESOURCES_CHANGENOTWRITABLE"); ?></label>
			</td>
				<?php } 
				else
				{ ?>
			<td align="right">
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<input type="hidden" name="disable_write" value="0" />
				<label for="enable_write"><?php echo JText::_("CORE_RESOURCES_IGNOREWRITABLE"); ?></label>
			</td>
			<?php
				} // if
			} // if
			*/
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
