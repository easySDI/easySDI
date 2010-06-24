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

class HTML_properties {

	function editProperties( $rowProperties,$partners, $id, $option ){
		global  $mainframe;
		$database =& JFactory::getDBO();
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PROPERTIES"), 'generic.png' );
		?>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<?php
		echo $tabs->startPane("propertiesPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"propertiesrPane");
		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td ><?php echo JText::_("EASYSDI_ID"); ?> : </td>
								<td><?php echo $rowProperties->id; ?>
								<input type="hidden" name="id" value="<?php echo $id;?>"></td>								
							</tr>
			
  							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_MANDATORY"); ?> : </td>
								<td><select class="inputbox" name="mandatory" >								
								<option value="0" <?php if( $rowProperties->mandatory == 0 ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_FALSE"); ?></option>
								<option value="1" <?php if( $rowProperties->mandatory == 1 ) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select></td>															
							</tr>
  							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_PARTNER_ID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $rowProperties->partner_id ); ?></td>															
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_PUBLISHED"); ?> : </td>
								<td><select class="inputbox" name="published" >								
								<option value="0" <?php if( $rowProperties->published == 0 ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_FALSE"); ?></option>
								<option value="1" <?php if( $rowProperties->published == 1 ) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option>
								</select></td>	
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_UPDATE_DATE"); ?> : </td>																
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProperties->update_date)); ?></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_CODE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="code" value="<?php echo $rowProperties->code; ?>" /></td>
							</tr>							
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_TEXT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="text" value="<?php echo $rowProperties->text; ?>" /></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_TRANSLATION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="translation" value="<?php echo $rowProperties->translation; ?>" /></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_TYPE_CODE"); ?> : </td>
								<td><select class="inputbox" name="type_code" >								
								<option value="list" <?php if( $rowProperties->type_code == 'list' ) echo "selected"; ?> ><?php echo JText::_("EASYSDI_PROPERTY_LIST"); ?></option>
								<option value="mlist" <?php if( $rowProperties->type_code == 'mlist' ) echo "selected"; ?>><?php echo JText::_("EASYSDI_PROPERTY_MULTIPLE_LIST"); ?></option>
								<option value="cbox" <?php if( $rowProperties->type_code == 'cbox' ) echo "selected"; ?>><?php echo JText::_("EASYSDI_PROPERTY_CBOX"); ?></option>
								<option value="text" <?php if( $rowProperties->type_code == 'text' ) echo "selected"; ?>><?php echo JText::_("EASYSDI_PROPERTY_TEXT"); ?></option>
								<option value="textarea" <?php if( $rowProperties->type_code == 'textarea' ) echo "selected"; ?>><?php echo JText::_("EASYSDI_PROPERTY_TEXT_AREA"); ?></option>
								<option value="message" <?php if( $rowProperties->type_code == 'message' ) echo "selected"; ?>><?php echo JText::_("EASYSDI_PROPERTY_MESSAGE"); ?></option>
								</select>
								</td>
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
		<input class="inputbox" type="hidden" size="50" maxlength="100" name="order" value="<?php echo $rowProperties->order; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function listProperties($use_pagination, $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROPERTIES"));
		$ordering = ($filter_order == 'order');
		?>
		<form action="index.php" method="post" name="adminForm">
			
			<table width="100%">
				<tr>
					<td align="right">
						<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
						<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
						<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>			
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>																																			
					<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listProperties\');"',$use_pagination); ?></td>
				</tr>
			</table>
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("EASYSDI_PROPERTIES_DEF"); ?></th>
					<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'order', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderProperties' ); ?></th>			
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_MANDATORY"), 'mandatory', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_UPDATE_DATE"), 'update_date', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_TEXT"), 'text', @$filter_order_Dir, @$filter_order); ?></th>				
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_TRANSLATION"), 'translation', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td width="30px"><?php echo $row->id; ?></td>
				<td> <?php echo JHTML::_('grid.published',$row,$i); ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="order" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupProperties', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownProperties', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupProperties', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownProperties', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownProperties', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupProperties', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownProperties', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupProperties', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->order;?>" class="text_area" style="text-align: center" />
	            </td>
				<td><?php echo $row->mandatory; ?></td>				
							
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->update_date)); ?></td>
				<td><?php echo $row->text; ?></td>
				<td><?php echo $row->translation; ?></td>
			</tr>
			<?php
				$k = 1 - $k;
			}		
			?>
			</tbody>
			
		<?php		
		if (JRequest::getVar('use_pagination',0))
		{
		?>
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
	  	<input type="hidden" name="task" value="listProperties" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="publishedobject" value="properties">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
	<?php		
	}	

	function editPropertiesValues( $rowProperties, $property, $id, $option ){
		global  $mainframe;
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PROPERTIES"), 'generic.png' );
		$properties_id = JRequest::getVar(properties_id,-1);
		if ($properties_id == -1){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ERROR_NO_PROPERTY_ID"),"ERROR");	
		}
		else
		{
		?>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<?php
		echo $tabs->startPane("propertiesPane");
		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_GENERAL"),"propertiesPane");
		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("EASYSDI_EASYSDI_GENERIC"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td ><?php echo JText::_("EASYSDI_ID"); ?> : </td>
								<td><?php echo $rowProperties->id; ?>
								<input type="hidden" name="id" value="<?php echo $id;?>"></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_PROPERTIES_NAME"); ?> : </td>																						
								<td><?php echo JText::_($property->text); ?></td>
							</tr>
  							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_VALUE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="value" value="<?php echo $rowProperties->value; ?>" /></td>
							</tr>												
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_TEXT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="text" value="<?php echo $rowProperties->text; ?>" /></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_TRANSLATION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="translation" value="<?php echo $rowProperties->translation; ?>" /></td>
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
		<input type="hidden" name="properties_id" value="<?php echo $properties_id; ?>" />
		<input type="hidden" size="50" maxlength="100" name="order" value="<?php echo $rowProperties->order; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
		}
	}
	
	function listPropertiesValues($properties_id,$use_pagination, $rows,$row, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROPERTIES")." ".JText::_($row->text));
		$ordering = ($filter_order == 'order');
		?>
		<form action="index.php" method="post" name="adminForm">
		<input type ="hidden" name ="properties_id" value ="<?php echo $properties_id ?>">
			<table width="100%">
				<tr>
					<td align="right">
						<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
						<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton('listPropertiesValues');" />			
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>																																			
					<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listPropertiesValues\');"',$use_pagination); ?></td>
				</tr>
			</table>
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("EASYSDI_PROPERTIES_DEF"); ?></th>
					<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_VALUES_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'order', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderPropertiesValues' ); ?></th>			
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_VALUE"), 'value', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_TEXT"), 'text', @$filter_order_Dir, @$filter_order); ?></th>				
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_TRANSLATION"), 'translation', @$filter_order_Dir, @$filter_order); ?></th>			
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
					<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
					<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
					<td width="30px"><?php echo $row->id; ?></td>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<td width="100px" align="right">
						<?php
						if ($filter_order=="order" and $filter_order_Dir=="asc"){
							if ($disabled){
						?>
								 <?php echo $pageNav->orderUpIcon($i, true, 'orderupPropertiesValues', '', false ); ?>
					             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownPropertiesValues', '', false ); ?>
			            <?php
							}
							else {
						?>
								 <?php echo $pageNav->orderUpIcon($i, true, 'orderupPropertiesValues', 'Move Up', isset($rows[$i-1]) ); ?>
					             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownPropertiesValues', 'Move Down', isset($rows[$i+1]) ); ?>
						<?php
							}		
						}
						else{ 
							if ($disabled){
						?>
								 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownPropertiesValues', '', false ); ?>
					             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupPropertiesValues', '', false ); ?>
			            <?php
							}
							else {
						?>
								 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownPropertiesValues', 'Move Down', isset($rows[$i-1]) ); ?>
			 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupPropertiesValues', 'Move Up', isset($rows[$i+1]) ); ?>
						<?php
							}
						}?>
						<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->order;?>" class="text_area" style="text-align: center" />
		            </td>
					<td><?php echo $row->value; ?></td>				
					<td><?php echo $row->text; ?></td>
					<td><?php echo $row->translation; ?></td>
				</tr>
			<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
			<?php	
			if (JRequest::getVar('use_pagination',0))
			{
			?>
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
	  	<input type="hidden" name="task" value="listPropertiesValues" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
		<?php
	}
}
?>