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

class HTML_perimeter {

	function editPerimeter( $rowPerimeter,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PERIMETER"), 'generic.png' );
			
		?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
<?php
		echo $tabs->startPane("PerimeterPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"PerimeterPane");

		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_TEXT_JOOMLA"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width="100p"><?php echo JText::_("EASYSDI_PERIMETER_ID"); ?> : </td>
								<td><?php echo $rowPerimeter->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>			

							<tr>
								<td><?php echo JText::_("EASYSDI_WFS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="wfs_url" value="<?php echo $rowPerimeter->wfs_url; ?>" /></td>
							</tr>
							
							<tr>							
								<td><?php echo JText::_("EASYSDI_FEATURETYPE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="feature_type_name" value="<?php echo $rowPerimeter->feature_type_name; ?>" /></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_WMS_URL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="wms_url" value="<?php echo $rowPerimeter->wms_url; ?>" /></td>
							</tr>
							
							<tr>
								<td><?php echo JText::_("EASYSDI_WMS_MIN_RESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="min_resolution" value="<?php echo $rowPerimeter->min_resolution; ?>" /></td>
							</tr>
							

							<tr>
								<td><?php echo JText::_("EASYSDI_WMS_MAX_RESOLUTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="max_resolution" value="<?php echo $rowPerimeter->max_resolution; ?>" /></td>
							</tr>
							
														
							<tr>
								<td><?php echo JText::_("EASYSDI_IMG_FORMAT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="img_format" value="<?php echo $rowPerimeter->img_format; ?>" /></td>
								<td>ex : image/png</td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_LAYER_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="layer_name" value="<?php echo $rowPerimeter->layer_name; ?>" /></td>
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_PERIMETER_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="perimeter_name" value="<?php echo $rowPerimeter->perimeter_name; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_PERIMETER_DESC"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="perimeter_desc" value="<?php echo $rowPerimeter->perimeter_desc; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_PERIMETER_AREA_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="area_field_name" value="<?php echo $rowPerimeter->area_field_name; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("EASYSDI_PERIMETER_NAME_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name_field_name" value="<?php echo $rowPerimeter->name_field_name; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PERIMETER_ID_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="id_field_name" value="<?php echo $rowPerimeter->id_field_name; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PERIMETER_FILTER_FIELD_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="filter_field_name" value="<?php echo $rowPerimeter->filter_field_name; ?>" /></td>
								<?php
									$perimList = array();
									$perimList [] = JHTML::_('select.option','-1', JText::_("EASYSDI_PERIM_LIST") );
									$database->setQuery( "SELECT id AS value, perimeter_name AS text FROM #__easysdi_perimeter_definition order by perimeter_name" );
									$perimList = array_merge($perimList, $database->loadObjectList());
		
		
															?>
								<td><?php echo JText::_("EASYSDI_PERIMETER_FILTER_FIELD_NAME_DEPENDS_OF"); ?></td>
								
								<td><?php echo JHTML::_("select.genericlist",$perimList, 'id_perimeter_filter', 'size="1" class="inputbox"', 'value', 'text', $rowPerimeter->id_perimeter_filter ); ?></td>
								<td>
								
								
								</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_LOCALISATION"); ?> : </td>
							<td><select name="is_localisation" > <option value="1" <?php if($rowPerimeter->is_localisation == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowPerimeter->is_localisation == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							
						<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_SEARCHBOX"); ?> : </td>
							<td><select name="searchbox" > <option value="1" <?php if($rowPerimeter->searchbox == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowPerimeter->searchbox == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_MAXFEATURES"); ?> : </td>
							<td><input type name="maxfeatures"  value="<?php echo $rowPerimeter->maxfeatures ?>"> 
							</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_SORT"); ?> : </td>
							<td><select name="sort" > <option value="1" <?php if($rowPerimeter->sort == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowLocation->sort == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							</tr>
							
							<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_USER"); ?> : </td>
							<td><input type="text" name="user"  value="<?php echo $rowPerimeter->user?>"></td>
							</tr>
							<tr>							
							<td><?php echo JText::_("EASYSDI_PERIMETER_PASSWORD"); ?> : </td>
							<td><input type="password" name="password"  value="<?php echo $rowPerimeter->password ?>"></td>
							</tr>
							<tr>							
							<td><?php echo JText::_("EASYSDI_PERIMETER_CODE"); ?> : </td>
							<td><input type="text" name="perimeter_code"  value="<?php echo $rowPerimeter->perimeter_code ?>"></td>
							</tr>
							
							
							
						</table>
					</fieldset>
				</td>
			</tr>
			
		</table>
		
		
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();		
		?>
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	
	
	function listPerimeter($use_pagination, $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PERIMETER"));
		
		$ordering = ($filter_order == 'ordering');
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listPerimeter\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title' width="10px"><?php echo JText::_("EASYSDI_PERIMETER_DEF"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PERIMETER_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderPerimeter' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PERIMETER_WFS_URL"), 'wfs_url', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PERIMETER_LAYER_NAME"), 'layer_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PERIMETER_PERIMETER_NAME"), 'perimeter_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PERIMETER_PERIMETER_DESC"), 'perimeter_desc', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++)
		{
			$row = $rows[$i];	  				
?>
			<tr class="<?php echo "row$k"; ?>">
				<td width="10px" align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
								
				<td width="30px"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupPerimeter', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownPerimeter', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupPerimeter', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownPerimeter', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownPerimeter', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupPerimeter', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownPerimeter', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupPerimeter', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>	
				<td><?php echo $row->wfs_url; ?></td>				
				<td><?php echo $row->layer_name; ?></td>
				<td><?php echo $row->perimeter_name; ?></td>
				<td><?php echo $row->perimeter_desc; ?></td>
			</tr>
<?php
			$k = 1 - $k;
		}
		
			?></tbody>
			
		<?php			
		
		if (JRequest::getVar('use_pagination',0))
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
	  	<input type="hidden" name="task" value="listPerimeter" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
}
?>