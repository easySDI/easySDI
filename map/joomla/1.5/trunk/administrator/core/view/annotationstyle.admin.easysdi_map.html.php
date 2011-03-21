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

class HTML_annotationstyle 
{
	function listAnnotationStyle($use_pagination, $rows, $pageNav,$search, $filter_order_Dir, $filter_order,$option)
	{
		JToolBarHelper::title(JText::_("MAP_ANNOTATION_STYLE"));
		?>
		<form action="index.php" method="GET" name="adminForm">
		<table width="100%">
			<tr>
				<td class="key"  width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchAnnotationStyle" id="searchAnnotationStyle" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchAnnotationStyle').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("MAP_ANNOTATION_STYLE_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_ANNOTATION_STYLE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editAnnotationStyle')"><?php echo $row->name; ?></a></td>
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
	  	<input type="hidden" name="task" value="annotationStyle" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editAnnotationStyle($annotationStyle,$createUser,$updateUser, $option)
	{
		if ($annotationStyle->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_ANNOTATION_STYLE"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_NEW_ANNOTATION_STYLE"), 'generic.png' );
		}
		

	?>	
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton== "saveAnnotationStyle")
		{
			if (document.getElementById('name').value == "")
			{	
				alert ('<?php echo  JText::_( 'MAP_PROJECTION_NAME_ANNOTATION_ERROR');?>');	
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
								<td class="key" width="100p"><?php echo JText::_("MAP_ANNOTATION_STYLE_ID"); ?></td>
								<td><?php echo $annotationStyle->id; ?></td>								
							</tr>
							<tr>
								<td  class="key" width="100p"><?php echo JText::_("MAP_ANNOTATION_STYLE_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="200" name="name" id="name" value="<?php echo $annotationStyle->name; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_FILL_COLOR"); ?></td>
								<td><input class="inputbox" type="text" size="7" maxlength="7" name="fillColor" id="fillColor" value="<?php echo $annotationStyle->fillColor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_FILL_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="fillOpacity" id="fillOpacity" value="<?php echo $annotationStyle->fillOpacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_COLOR"); ?></td>
								<td><input class="inputbox" type="text" size="7" maxlength="7" name="strokeColor" id="strokeColor" value="<?php echo $annotationStyle->strokeColor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="strokeOpacity" id="strokeOpacity" value="<?php echo $annotationStyle->strokeOpacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_WIDTH"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="strokeWidth" id="strokeWidth" value="<?php echo $annotationStyle->strokeWidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_LINE_CAP"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="7" name="strokeLinecap" id="strokeLinecap" value="<?php echo $annotationStyle->strokeLinecap; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_DASH_STYLE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="11" name="strokeDashstyle" id="strokeDashstyle" value="<?php echo $annotationStyle->strokeDashstyle; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_POINT_RADIUS"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="pointRadius" id="pointRadius" value="<?php echo $annotationStyle->pointRadius; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_EXTERNAL_GRAPHIC"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="400" name="externalGraphic" id="externalGraphic" value="<?php echo $annotationStyle->externalGraphic; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_WIDTH"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicWidth" id="graphicWidth" value="<?php echo $annotationStyle->graphicWidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_HEIGHT"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicHeight" id="graphicHeight" value="<?php echo $annotationStyle->graphicHeight; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicOpacity" id="graphicOpacity" value="<?php echo $annotationStyle->graphicOpacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_X_OFFSET"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicXOffset" id="graphicXOffset" value="<?php echo $annotationStyle->graphicXOffset; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_Y_OFFSET"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicYOffset" id="graphicYOffset" value="<?php echo $annotationStyle->graphicYOffset; ?>" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			</table> 
			<br></br>
			<table border="0" cellpadding="3" cellspacing="0">
			<?php
			if ($annotationStyle->created)
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
					<td><?php if ($annotationStyle->created) {echo date('d.m.Y h:i:s',strtotime($annotationStyle->created));} ?></td>
					<td>, </td>
					<td><?php echo $createUser; ?></td>
				</tr>
			<?php
			}
			if ($annotationStyle->updated and $annotationStyle->updated<> '0000-00-00 00:00:00')
			{ 
			?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($annotationStyle->updated and $annotationStyle->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($annotationStyle->updated));} ?></td>
					<td>, </td>
					<td><?php echo $updateUser; ?></td>
				</tr>
			<?php
			}
			?>		
		</table>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo $annotationStyle->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $annotationStyle->guid?>" />
		<input type="hidden" name="ordering" value="<?php echo $annotationStyle->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo $annotationStyle->created;?>" />
		<input type="hidden" name="createdby" value="<?php echo $annotationStyle->createdby; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo $annotationStyle->created; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo $annotationStyle->createdby; ?>" /> 
	</form>
	
<?php
	}
}
?>