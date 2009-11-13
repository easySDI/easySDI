<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

class HTML_perimeter {

	function editPerimeter( $rowPerimeter,$rowsAccount,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		//Get if the current perimeter is used by an other in the field "id_perimeter_filter".
		//This means that a perimeter depends on this one for a filter and
		//so this means that the current perimeter can not be used in "manual perimeter"
		$queryIsFilter = "select * from  #__easysdi_perimeter_definition  where id_perimeter_filter=$rowPerimeter->id ";
		$database->setQuery( $queryIsFilter );
		$result = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
		$is_filter = false;
		if(count($result) >0)
		{
			$is_filter = true;	
		}
		//
		
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PERIMETER"), 'generic.png' );
			
		?>				
		<script>
		function choosePerimeterAsFilter(perimeterId)
		{
			<?php
			$query = "SELECT * FROM #__easysdi_perimeter_definition WHERE is_localisation = 1 ";
			$database->setQuery($query);
			$rows = $database->loadObjectList();
			if ($database->getErrorNum()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
			foreach ($rows as $row)
			{
				?>
				if(perimeterId == '<?php echo $row->id ;?>')
				{
					alert( "<?php echo JText::_("EASYSDI_PERIMETER_DEF_VALIDATION");?> ");
					return;
				}
				<?php 
			}
			?>
		}
		</script>
		<script>
		function displayAuthentication()
		{
			if (document.forms['adminForm'].service_type[0].checked)
			{
				document.getElementById('password').disabled = true;
				document.getElementById('password').value = "";
				document.getElementById('user').disabled = true;
				document.getElementById('user').value ="";
				document.getElementById('easysdi_account_id').disabled = false;
			}
			else
			{
				document.getElementById('password').disabled = false;
				document.getElementById('user').disabled = false;
				document.getElementById('easysdi_account_id').disabled = true;
				document.getElementById('easysdi_account_id').value = '0';
			}
		}		
		</script>
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
								<td ><?php echo JText::_("EASYSDI_PERIMETER_ID"); ?> : </td>
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
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="filter_field_name" value="<?php echo $rowPerimeter->filter_field_name; ?>"  /></td>
								<?php
									$perimList = array();
									$perimList [] = JHTML::_('select.option','-1', JText::_("EASYSDI_PERIM_LIST") );
									$database->setQuery( "SELECT id AS value, perimeter_name AS text FROM #__easysdi_perimeter_definition order by perimeter_name" );
									$perimList = array_merge($perimList, $database->loadObjectList());
		
		
															?>
								<td><?php echo JText::_("EASYSDI_PERIMETER_FILTER_FIELD_NAME_DEPENDS_OF"); ?></td>
								
								<td><?php echo JHTML::_("select.genericlist",$perimList, 'id_perimeter_filter', 'size="1" class="inputbox" onChange="javascript:choosePerimeterAsFilter(this.value);"', 'value', 'text', $rowPerimeter->id_perimeter_filter ); ?></td>
								<td>
								
								
								</td>
							</tr>
							<tr>
							<td><?php echo JText::_("EASYSDI_PERIMETER_LOCALISATION"); ?> : </td>
							<?php
							if($is_filter == false)
							{
							?>
							<td><select name="is_localisation" > <option value="1" <?php if($rowPerimeter->is_localisation == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
								<option value="0" <?php if($rowPerimeter->is_localisation == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
							</td>
							<?php
							}
							else
							{
							?>
							<td colspan="2"><select name="is_localisation" disabled >  
								<option value="0" selected ><?php echo JText::_("EASYSDI_FALSE"); ?></option></select>
								<?php echo JText::_("EASYSDI_PERIMETER_DISABLE_VISIBLE_REASON"); ?>
							</td>
							<?php
							}
							?>
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
							<td><?php echo JText::_("EASYSDI_PERIMETER_CODE"); ?> : </td>
							<td><input type="text" name="perimeter_code"  value="<?php echo $rowPerimeter->perimeter_code ?>"></td>
							</tr>
							<tr>
							<td colspan ="3">
							<fieldset>
							<legend><?php echo JText::_("EASYSDI_BASE_MAP_AUTHENTICATION"); ?></legend>
								<table>
								<tr>
									<td >
										<input type="radio" name="service_type" value="via_proxy" onclick="javascript:displayAuthentication();" <?php if ($rowPerimeter->easysdi_account_id) echo "checked";?>>
									</td>
									<td colspan="2">
										<?php echo JText::_("EASYSDI_BASEMAP_VIA_PROXY"); ?>
									</td>
								</tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_EASYSDI_ACCOUNT"); ?> : </td>
									<td><?php $enable = $rowPerimeter->easysdi_account_id? "" : "disabled"  ; echo JHTML::_("select.genericlist",$rowsAccount, 'easysdi_account_id', 'size="1" class="inputbox" onChange="" '.$enable , 'value', 'text',$rowPerimeter->easysdi_account_id); ?></td>
								</tr>
								<tr>
									<td >
									 	<input type="radio" name="service_type" value="direct" onclick="javascript:displayAuthentication();" <?php if (!$rowPerimeter->easysdi_account_id) echo "checked";?>> 
								 	</td>
								 	<td colspan="2">
									 	 <?php echo JText::_("EASYSDI_BASEMAP_DIRECT"); ?>
								 	</td>
							 	<tr>
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_USER"); ?> : </td>
									<td><input <?php if ($rowPerimeter->easysdi_account_id){echo "disabled";} ?> class="inputbox" type="text" size="50" maxlength="100" name="user" id="user" value="<?php echo $rowPerimeter->user; ?>" /></td>							
								</tr>							
								<tr>
									<td></td>
									<td><?php echo JText::_("EASYSDI_BASEMAP_PASSWORD"); ?> : </td>
									<td><input <?php if ($rowPerimeter->easysdi_account_id){echo "disabled";} ?> class="inputbox" type="password" size="50" maxlength="100" name="password" id="password" value="<?php echo $rowPerimeter->password; ?>" /></td>							
								</tr>
								
								</table>
							</fieldset>	
							<td>	
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