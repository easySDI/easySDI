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

class HTML_searchlayer 
{
	function listSearchLayer( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option)
	{
		JToolBarHelper::title(JText::_("MAP_LIST_SEARCH_LAYER"), 'map.png');
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchSearchLayer" id="searchSearchLayer" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchSearchLayer').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_SEARCH_LAYER_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_SEARCH_LAYER_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_SEARCH_LAYER_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_SEARCH_LAYER_ENABLE"), 'enable', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editSearchLayer')"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->description; ?></td>
				<td><input type="checkbox" disabled id="isEnable<?php echo $i;?>" name="isEnable<?php echo $i;?>" value="" <?php if($row->enable == 1)echo " checked" ?> /></td>
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
	  	<input type="hidden" name="task" value="searchLayer" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editSearchLayer ($search_layer, $rowsSearchLayerFT, $rowsDetailsFT,$createUser, $updateUser,$fieldsLength, $option)
	{
		if ($search_layer->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_SEARCH_LAYER").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_SEARCH_LAYER").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		?>			
		<script>	
		function submitbutton(pressbutton)
		{
			if(pressbutton == "saveSearchLayer")
			{
				if (document.getElementById('featuretype').value == "")
				{	
					alert ('<?php echo  JText::_( 'EASYSDI_SERACHLAYER_FT_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('geometryname').value == "")
				{
					alert ('<?php echo  JText::_( 'EASYSDI_SERACHLAYER_GEOM_VALIDATION_ERROR');?>');	
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
										<td class="key"><?php echo JText::_("MAP_SEARCH_LAYER_NAME"); ?></td>
										<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $search_layer->name; ?>" /></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("MAP_SEARCH_LAYER_DESCRIPTION"); ?></td>
										<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $search_layer->description; ?>" /></td>
									</tr>
									<tr>
										<td class="key" width="100p"><?php echo JText::_("MAP_SEARCH_LAYER_FT"); ?></td>
										<td>
										<?php echo JHTML::_("select.genericlist",$rowsSearchLayerFT, 'featuretype', 'size="1" class="inputbox" ', 'value', 'text',$search_layer->featuretype); ?>
										</td>	
										<td align="right"><a href="./index.php?option=com_easysdi_map&task=newFeatureType" > 
											<img class="helpTemplate" 
												 src="../templates/easysdi/icons/silk/add.png" 
												 alt="<?php echo JText::_("MAP_SEARCH_LAYER_NEW_FT") ?>" 
												 />
										</a></td>							
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("MAP_SEARCH_LAYER_GEOM"); ?></td>
										<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['geometryname'];?>" name="geometryname" id="geometryname" value="<?php echo $search_layer->geometryname; ?>" /></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("MAP_SEARCH_LAYER_ROW_DETAILS_FT"); ?></td>
										<td>
										<?php echo JHTML::_("select.genericlist",$rowsDetailsFT, 'rowdetailsfeaturetype', 'size="1" class="inputbox" ', 'value', 'text',$search_layer->rowdetailsfeaturetype); ?>										</td>
										<td align="right"><a href="./index.php?option=com_easysdi_map&task=newFeatureType" > 
											<img class="helpTemplate" 
												 src="../templates/easysdi/icons/silk/add.png" 
												 alt="<?php echo JText::_("MAP_SEARCH_LAYER_NEW_FT") ?>" 
												 />
										</a></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("MAP_SEARCH_LAYER_STYLES"); ?></td>
										<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['styles'];?>" name="styles" id="styles" value="<?php echo $search_layer->styles; ?>" /></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("MAP_SEARCH_LAYER_ENABLE"); ?></td>
										<td><input class="inputbox" value="1" type="checkbox" name="enable" id="enable" <?php if ($search_layer->enable == 1) echo " checked"; ?> /></td>
									</tr>
								</table>
							</fieldset>
						</td>
					</tr>
				</table>
				<br></br>
				<table border="0" cellpadding="3" cellspacing="0">
				<?php
				if ($search_layer->created)
				{ 
				?>
					<tr>
						<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
						<td><?php if ($search_layer->created) {echo date('d.m.Y h:i:s',strtotime($search_layer->created));} ?></td>
						<td>, </td>
						<td><?php echo $createUser; ?></td>
					</tr>
				<?php
				}
				if ($search_layer->updated and $search_layer->updated<> '0000-00-00 00:00:00')
				{ 
				?>
					<tr>
						<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
						<td><?php if ($search_layer->updated and $search_layer->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($search_layer->updated));} ?></td>
						<td>, </td>
						<td><?php echo $updateUser; ?></td>
					</tr>
				<?php
				}
				?>		
			</table>
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="id" value="<?php echo $search_layer->id; ?>" />
				<input type="hidden" name="guid" value="<?php echo $search_layer->guid?>" />
				<input type="hidden" name="ordering" value="<?php echo $search_layer->ordering; ?>" />
				<input type="hidden" name="created" value="<?php echo $search_layer->created;?>" />
				<input type="hidden" name="createdby" value="<?php echo $search_layer->createdby; ?>" /> 
				<input type="hidden" name="updated" value="<?php echo $search_layer->created; ?>" />
				<input type="hidden" name="updatedby" value="<?php echo $search_layer->createdby; ?>" /> 
			</form>
		<?php
	}
}
?>