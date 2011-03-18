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

class HTML_resultgrid 
{
	function listResultGrid($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_MAP_RESULT_GRID"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_TITLE"); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editResultGrid')"><?php echo $row->internal_name; ?></a></td>
				<td><?php echo $row->title; ?></td>
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
	  	<input type="hidden" name="task" value="resultGrid" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editResultGrid ($resultGrid,$rowsResultGridFT,$rowsDetailsFT, $option)
	{
		if ($resultGrid->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_EDIT_RESULT_GRID"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_NEW_RESULT_GRID"), 'generic.png' );
		}
		$rowsDetailsFTChoice = array();
		$rowsDetailsFTChoice[] = JHTML::_('select.option','0', JText::_("EASYSDI_MAP_ROW_DETAIL_CHOICE" ));
		$rowsDetailsFTChoice = array_merge( $rowsDetailsFTChoice, $rowsDetailsFT);

	?>		
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton == "saveResultGrid")
		{
			if (document.getElementById('internal_name').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_RESULTGRID_NAME_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('title').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_RESULTGRID_TITLE_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('feature_type').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_RESULTGRft_id_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('distinct_fk').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_RESULTGRID_FK_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('distinct_pk').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_RESULTGRID_PK_VALIDATION_ERROR');?>');	
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
						<table class="admintable" >
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_ID"); ?></td>
								<td><?php echo $resultGrid->id; ?></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_NAME"); ?></td>
								<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="100" name="internal_name" id="internal_name" value="<?php echo $resultGrid->internal_name; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_TITLE"); ?></td>
								<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="500" name="title" id="title" value="<?php echo $resultGrid->title; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_RESULT_GRft_idYPE"); ?></td>
								<td>
								<?php echo JHTML::_("select.genericlist",$rowsResultGridFT, 'feature_type', 'size="1" class="inputbox" ', 'value', 'text',$resultGrid->feature_type); ?>								
								</td>
								<td align="right"><a href="./index.php?option=com_easysdi_map&task=newFeatureType" > 
									<img class="helpTemplate" 
										 src="../templates/easysdi/icons/silk/add.png" 
										 alt="<?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_NEW_FT") ?>" 
										 />
								</a></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_DISTINCTFK"); ?></td>
								<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="100" name="distinct_fk" id="distinct_fk" value="<?php echo $resultGrid->distinct_fk; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_DISTINCTPK"); ?></td>
								<td colspan="2"><input class="inputbox" type="text" size="100" maxlength="100" name="distinct_pk" id="distinct_pk" value="<?php echo $resultGrid->distinct_pk; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_RESULT_GRID_DETAIL_FT"); ?></td>
								<td>
								<?php echo JHTML::_("select.genericlist",$rowsDetailsFTChoice, 'row_details_feature_type', 'size="1" class="inputbox" ', 'value', 'text',$resultGrid->row_details_feature_type); ?>
								</td>
								<td align="right"><a href="./index.php?option=com_easysdi_map&task=newFeatureType" > 
									<img class="helpTemplate" 
										 src="../templates/easysdi/icons/silk/add.png" 
										 alt="<?php echo JText::_("EASYSDI_MAP_SEARCH_LAYER_NEW_FT") ?>" 
										 />
								</a></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $resultGrid->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
<?php
	}
}
?>