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

class HTML_localisation 
{
	function listLocalisation($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_LOCALISATION"));
		$order_field = JRequest::getVar ('order_field');
		?>
		<script>
		function tableOrder(task, orderField)
		{
			document.forms['adminForm'].elements['order_field'].value=orderField;
			document.forms['adminForm'].submit();
			return;
		
		}
		</script>
		<form action="index.php" method="GET" name="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_LOCALISATION_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><a href="javascript:tableOrder('localisation', 'title');" title="Click to sort by this column"><?php echo JText::_("EASYSDI_LOCALISATION_TITLE"); ?></a></th>
				<th class='title'><?php echo JText::_("EASYSDI_LOCALISATION_URL"); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editLocalisation')"><?php echo $row->title; ?></a></td>
				<td><?php echo $row->wfs_url; ?></td>								 
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
	  	<input type="hidden" name="order_field" value="" />
	  	<input type="hidden" name="task" value="localisation" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editLocalisation( $localisation, $option )
	{
		global  $mainframe;
		if ($localisation->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_LOCALISATION_EDIT"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_LOCALISATION_NEW"), 'generic.png' );
		}
		?>		
		<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton == "saveLocalisation")
		{
			
			if (document.getElementById('wfs_url').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_LOCALISATION_CT_WFS_URL_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('title').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_LOCALISATION_CT_TITLE_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('area_field_name').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_LOCALISATION_CT_AREA_NAME_VALIDATION_ERROR');?>');	
				return;
			}else
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
								<td class="key" ><?php echo JText::_("EASYSDI_LOCALISATION_ID"); ?></td>
								<td><?php echo $localisation->id; ?></td>																
							</tr>	
							<tr>							
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_WFS_URL"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="1000" name="wfs_url" id="wfs_url" value="<?php echo $localisation->wfs_url; ?>" /></td>							
							</tr>	
									
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_TITLE"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="1000" name="title" id="title" value="<?php echo $localisation->title; ?>" /></td>
													
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_AREA_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="area_field_name" id="area_field_name" value="<?php echo $localisation->area_field_name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_NAME_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name_field_name" id="name_field_name" value="<?php echo $localisation->name_field_name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_ID_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="id_field_name" id="id_field_name" value="<?php echo $localisation->id_field_name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_FEAT_TYPE_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="feature_type_name" id="feature_type_name" value="<?php echo $localisation->feature_type_name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_PARENT_FK_FIELD_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="parent_fk_field_name" id="parent_fk_field_name" value="<?php echo $localisation->parent_fk_field_name; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_PARENT_ID"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="parent_id" id="parent_id" value="<?php echo $localisation->parent_id; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_LOCALISATION_MAX_FEATURE"); ?></td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="maxfeatures" id="maxfeatures" value="<?php echo $localisation->maxfeatures; ?>" /></td>
							</tr>							
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="id" value="<?php echo $localisation->id;?>">
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
}
?>