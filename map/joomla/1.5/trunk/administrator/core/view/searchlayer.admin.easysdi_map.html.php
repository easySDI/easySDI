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
	function listSearchLayer($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_MAP_SEARCH_LAYER"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_FT"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_GEOM"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_ENABLE"); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editSearchLayer')"><?php echo $row->description; ?></a></td>
				<td><?php echo $row->geometryname; ?></td>
				<td><input type="checkbox" disabled id="isEnable<?php echo $i;?>" name="isEnable<?php echo $i;?>" value="" <?php if($row->enable == 1)echo " checked" ?> /></td>
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		?>
		</tbody>
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
	  	<input type="hidden" name="task" value="searchlayer" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editSearchLayer ($search_layer, $rowsSearchLayerFT, $rowsDetailsFT, $option)
	{
		if ($search_layer->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_EDIT_SEARCH_LAYER"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_NEW_SEARCH_LAYER"), 'generic.png' );
		}
		?>			
		<script>	
		function submitbutton(pressbutton)
		{
			if(pressbutton == "saveSearchLayer")
			{
				if (document.getElementById('feature_type').value == "")
				{	
					alert ('<?php echo  JText::_( 'EASYSDI_SERACHLAYER_FT_VALIDATION_ERROR');?>');	
					return;
				}
				else if (document.getElementById('geometry_name').value == "")
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
										<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_ID"); ?></td>
										<td colspan="2"><?php echo $search_layer->id; ?></td>								
									</tr>
									<tr>
										<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_FT"); ?></td>
										<td>
										<?php echo JHTML::_("select.genericlist",$rowsSearchLayerFT, 'featuretype', 'size="1" class="inputbox" ', 'value', 'text',$search_layer->featuretype); ?>
										</td>	
										<td align="right"><a href="./index.php?option=com_easysdi_map&task=newFeatureType" > 
											<img class="helpTemplate" 
												 src="../templates/easysdi/icons/silk/add.png" 
												 alt="<?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_NEW_FT") ?>" 
												 />
										</a></td>							
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_GEOM"); ?></td>
										<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="100" name="geometryname" id="geometryname" value="<?php echo $search_layer->geometryname; ?>" /></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_ROW_DETAILS_FT"); ?></td>
										<td>
										<?php echo JHTML::_("select.genericlist",$rowsDetailsFT, 'rowdetailsfeaturetype', 'size="1" class="inputbox" ', 'value', 'text',$search_layer->rowdetailsfeaturetype); ?>										</td>
										<td align="right"><a href="./index.php?option=com_easysdi_map&task=newFeatureType" > 
											<img class="helpTemplate" 
												 src="../templates/easysdi/icons/silk/add.png" 
												 alt="<?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_NEW_FT") ?>" 
												 />
										</a></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_STYLES"); ?></td>
										<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="500" name="styles" id="styles" value="<?php echo $search_layer->styles; ?>" /></td>
									</tr>
									<tr>
										<td class="key"><?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_ENABLE"); ?></td>
										<td><input class="inputbox" value="1" type="checkbox" name="enable" id="enable" <?php if ($search_layer->enable == 1) echo " checked"; ?> /></td>
									</tr>
								</table>
							</fieldset>
						</td>
					</tr>
				</table>
				<input type="hidden" name="id" value="<?php echo $search_layer->id; ?>" />
				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="task" value="" />
			</form>
		<?php
	}
}
?>