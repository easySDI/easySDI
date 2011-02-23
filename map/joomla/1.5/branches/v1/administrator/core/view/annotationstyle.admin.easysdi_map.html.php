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
	function listAnnotationStyle($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROJECTION"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_NAME"); ?></th>
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
	  	</form>
		<?php		
	}
	
	function editAnnotationStyle($annotation_style, $option)
	{
		if ($annotation_style->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_EDIT_ANNOTATION_STYLE"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_NEW_ANNOTATION_STYLE"), 'generic.png' );
		}
		

	?>	
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton== "saveProjection")
		{
			if (document.getElementById('name').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_PROJECTION_NAME_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('title').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_PROJECTION_TITLE_VALIDATION_ERROR');?>');	
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
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_ID"); ?></td>
								<td><?php echo $annotation_style->id; ?></td>								
							</tr>
							<tr>
								<td  class="key" width="100p"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="200" name="name" id="name" value="<?php echo $annotation_style->name; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_FILL_COLOR"); ?></td>
								<td><input class="inputbox" type="text" size="7" maxlength="7" name="fillColor" id="fillColor" value="<?php echo $annotation_style->fillColor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_FILL_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="fillOpacity" id="fillOpacity" value="<?php echo $annotation_style->fillOpacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_STROKE_COLOR"); ?></td>
								<td><input class="inputbox" type="text" size="7" maxlength="7" name="strokeColor" id="strokeColor" value="<?php echo $annotation_style->strokeColor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_STROKE_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="strokeOpacity" id="strokeOpacity" value="<?php echo $annotation_style->strokeOpacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_STROKE_WIDTH"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="strokeWidth" id="strokeWidth" value="<?php echo $annotation_style->strokeWidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_STROKE_LINE_CAP"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="7" name="strokeLinecap" id="strokeLinecap" value="<?php echo $annotation_style->strokeLinecap; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_STROKE_DASH_STYLE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="11" name="strokeDashstyle" id="strokeDashstyle" value="<?php echo $annotation_style->strokeDashstyle; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_POINT_RADIUS"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="pointRadius" id="pointRadius" value="<?php echo $annotation_style->pointRadius; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_EXTERNAL_GRAPHIC"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="400" name="externalGraphic" id="externalGraphic" value="<?php echo $annotation_style->externalGraphic; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_GRAPHIC_WIDTH"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicWidth" id="graphicWidth" value="<?php echo $annotation_style->graphicWidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_GRAPHIC_HEIGHT"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicHeight" id="graphicHeight" value="<?php echo $annotation_style->graphicHeight; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_GRAPHIC_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicOpacity" id="graphicOpacity" value="<?php echo $annotation_style->graphicOpacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_GRAPHIC_X_OFFSET"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicXOffset" id="graphicXOffset" value="<?php echo $annotation_style->graphicXOffset; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_ANNOTATION_STYLE_GRAPHIC_Y_OFFSET"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="graphicYOffset" id="graphicYOffset" value="<?php echo $annotation_style->graphicYOffset; ?>" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $annotation_style->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
<?php
	}
}
?>