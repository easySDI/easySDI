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
	function listAnnotationStyle( $rows, $pageNav,$search, $filter_order_Dir, $filter_order,$option)
	{
		JToolBarHelper::title(JText::_("MAP_ANNOTATION_STYLE"), 'map.png');
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
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("MAP_ANNOTATION_STYLE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td><?php echo $row->description; ?></a></td>
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
	  	<input type="hidden" name="task" value="annotationStyle" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  	</form>
		<?php		
	}
	
	function editAnnotationStyle($annotationStyle,$createUser,$updateUser,$fieldsLength, $option)
	{
		if ($annotationStyle->id != 0)
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_ANNOTATION_STYLE").': <small><small>['. JText::_("CORE_EDIT").']</small></small>', 'addedit.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("MAP_EDIT_ANNOTATION_STYLE").': <small><small>['. JText::_("CORE_NEW").']</small></small>', 'addedit.png' );
		}
		

	?>	
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton== "saveAnnotationStyle")
		{
			if (document.getElementById('name').value == "")
			{	
				alert ('<?php echo  JText::_( 'MAP_ANNOTATION_NAME_ERROR');?>');	
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
								<td  class="key" width="100p"><?php echo JText::_("MAP_ANNOTATION_STYLE_NAME"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['name'];?>" name="name" id="name" value="<?php echo $annotationStyle->name; ?>" /></td>								
							</tr>
							<tr>
								<td  class="key" width="100p"><?php echo JText::_("MAP_ANNOTATION_STYLE_DESCRIPTION"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['description'];?>" name="description" id="description" value="<?php echo $annotationStyle->description; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_FILL_COLOR"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['fillcolor'];?>" name="fillcolor" id="fillcolor" value="<?php echo $annotationStyle->fillcolor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_FILL_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['fillopacity'];?>" name="fillopacity" id="fillopacity" value="<?php echo $annotationStyle->fillopacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_COLOR"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['strokecolor'];?>" name="strokecolor" id="strokecolor" value="<?php echo $annotationStyle->strokeColor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['strokeopacity'];?>" name="strokeopacity" id="strokeopacity" value="<?php echo $annotationStyle->strokeopacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_WIDTH"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['strokewidth'];?>" name="strokewidth" id="strokewidth" value="<?php echo $annotationStyle->strokewidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_LINE_CAP"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['strokelinecap'];?>" name="strokelinecap" id="strokelinecap" value="<?php echo $annotationStyle->strokelinecap; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_STROKE_DASH_STYLE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['strokedashstyle'];?>" name="strokedashstyle" id="strokedashstyle" value="<?php echo $annotationStyle->strokedashstyle; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_POINT_RADIUS"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['pointradius'];?>" name="pointradius" id="pointradius" value="<?php echo $annotationStyle->pointradius; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_EXTERNAL_GRAPHIC"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['externalgraphic'];?>" name="externalgraphic" id="externalgraphic" value="<?php echo $annotationStyle->externalgraphic; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_WIDTH"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['graphicwidth'];?>" name="graphicwidth" id="graphicwidth" value="<?php echo $annotationStyle->graphicwidth; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_HEIGHT"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['graphicheight'];?>" name="graphicheight" id="graphicheight" value="<?php echo $annotationStyle->graphicheight; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_OPACITY"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['graphicopacity'];?>" name="graphicopacity" id="graphicopacity" value="<?php echo $annotationStyle->graphicopacity; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_X_OFFSET"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['graphicxoffset'];?>" name="graphicxoffset" id="graphicxoffset" value="<?php echo $annotationStyle->graphicxoffset; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("MAP_ANNOTATION_STYLE_GRAPHIC_Y_OFFSET"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['graphicyoffset'];?>" name="graphicyoffset" id="graphicyoffset" value="<?php echo $annotationStyle->graphicyoffset; ?>" /></td>
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