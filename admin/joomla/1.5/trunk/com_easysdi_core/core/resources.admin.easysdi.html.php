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
	
	
	function listResources(&$rows, &$pageNav, $option, $cur_lang)
	{			
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
			<tr class="<?php echo $pageNav->rowNumber( $i ); ?>">
				<td align="center"><?php echo $row->language; ?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php
				if ($row->published == 1) {	 ?>
					<img src="images/tick.png" alt="<?php echo _RESOURCE_CURRENT ?>"/>
					<?php
				} else {
					?>
					&nbsp;
				<?php
				}
				?></td>
				<td> echo $row->component; </td>
				<td>echo $row->creationdate;</td>
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
	
	
	function editResource($language, &$content, $option )
	{
		global  $mainframe;
		global $mosConfig_absolute_path;
		$language_path = $mosConfig_absolute_path . "/administrator/components/com_asitvd/lang/" . $language . ".php";
		?>
		<form action="index2.php" method="post" name="adminForm">
		<table cellpadding="1" cellspacing="1" border="0" width="100%">
		<tr>
			<td width="270"><table class="adminheading">
				<tr>
					<th class="langmanager"><?php echo _ASITVD_TITLE_EDITRESOURCE ?><span class="componentheading">
						&nbsp;[<?php echo $language.'.php : '; ?>
						<?php echo is_writable($language_path) ? '<b><font color="green"> Modifiable</font>' : '<font color="red"> Non Modifiable</font></b>' ?>]
					</span></th>
			</tr></table></td>
            <?php
			if (mosIsChmodable($language_path)) {
				if (is_writable($language_path)) {
?>
			<td>
				<input type="checkbox" id="disable_write" name="disable_write" value="1"/>
				<label for="disable_write"><?php echo _ASITVD_TITLE_CHANGENOTWRITABLE ?></label>
			</td>
<?php
				} else {
?>
			<td>
				<input type="checkbox" id="enable_write" name="enable_write" value="1"/>
				<label for="enable_write"><?php echo _ASITVD_TITLE_IGNOREWRITABLE ?></label>
			</td>
<?php
				} // if
			} // if
?>
		</tr>
		</table>
		<table class="adminform">
			<tr><th><?php echo $language_path; ?></th></tr>
			<tr><td><textarea style="width:100%" cols="110" rows="50" name="filecontent" class="inputbox"><?php echo $content; ?></textarea></td></tr>
		</table>
		<input type="hidden" name="language" value="<?php echo $language; ?>" />
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="editResource" />
		</form>
	<?php
	}

}
	
?>
