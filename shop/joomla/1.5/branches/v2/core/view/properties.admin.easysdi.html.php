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

	function editProperties( $property,$accounts, $id,$languages, $labels, $option ){
		global  $mainframe;
		$database =& JFactory::getDBO();
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_TITLE_EDIT_PROPERTIES"), 'generic.png' );
		?>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
					<legend align="top"><?php echo JText::_("SHOP_GENERAL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key" ><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $property->id; ?>
								<input type="hidden" name="id" value="<?php echo $id;?>"></td>								
							</tr>
							<tr>							
								<td class="key"><?php echo JText::_("CORE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $property->name; ?>" /></td>
							</tr>							
							<tr>							
								<td class="key"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $property->description; ?>" /></td>
							</tr>
  							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROPERTIES_MANDATORY"); ?> : </td>
								<td><select class="inputbox" name="mandatory" >								
								<option value="0" <?php if( $property->mandatory == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_FALSE"); ?></option>
								<option value="1" <?php if( $property->mandatory == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
								</select></td>															
							</tr>
  							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROPERTIES_ACCOUNT_ID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$accounts, 'account_id', 'size="1" class="inputbox"', 'value', 'text', $property->account_id ); ?></td>															
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROPERTIES_PUBLISHED"); ?> : </td>
								<td><select class="inputbox" name="published" >								
								<option value="0" <?php if( $property->published == 0 ) echo "selected"; ?> ><?php echo JText::_("CORE_FALSE"); ?></option>
								<option value="1" <?php if( $property->published == 1 ) echo "selected"; ?>><?php echo JText::_("CORE_TRUE"); ?></option>
								</select></td>	
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CORE_UPDATED"); ?> : </td>																
								<td><?php echo date('d.m.Y H:i:s',strtotime($property->updated)); ?></td>
							</tr>
							
							
							
							<tr>							
								<td class="key"><?php echo JText::_("SHOP_PROPERTIES_TYPE_CODE"); ?> : </td>
								<td><select class="inputbox" name="type" >								
								<option value="list" <?php if( $property->type == 'list' ) echo "selected"; ?> ><?php echo JText::_("SHOP_PROPERTY_LIST"); ?></option>
								<option value="mlist" <?php if( $property->type == 'mlist' ) echo "selected"; ?>><?php echo JText::_("SHOP_PROPERTY_MULTIPLE_LIST"); ?></option>
								<option value="cbox" <?php if( $property->type == 'cbox' ) echo "selected"; ?>><?php echo JText::_("SHOP_PROPERTY_CBOX"); ?></option>
								<option value="text" <?php if( $property->type == 'text' ) echo "selected"; ?>><?php echo JText::_("SHOP_PROPERTY_TEXT"); ?></option>
								<option value="textarea" <?php if( $property->type == 'textarea' ) echo "selected"; ?>><?php echo JText::_("SHOP_PROPERTY_TEXT_AREA"); ?></option>
								<option value="message" <?php if( $property->type == 'message' ) echo "selected"; ?>><?php echo JText::_("SHOP_PROPERTY_MESSAGE"); ?></option>
								</select>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<fieldset>
						<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
						<table>
							<?php
							foreach ($languages as $lang)
							{ 
							?>
								<tr>
								<td  class="key" WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
								<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php echo $labels[$lang->id]?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
								</tr>
							<?php
							}
							?>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input class="inputbox" type="hidden" size="50" maxlength="100" name="ordering" value="<?php echo $property->ordering; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="guid" value="<?php echo $property->guid; ?>" />
		<input type="hidden" name="created" value="<?php echo $property->created; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $property->createdby; ?>" /> 
		<input type="hidden" name="checked_out" value="<?php echo $property->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $property->checked_out_time; ?>" />
		</form>
	<?php
	}
	
	function listProperties( $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		JToolBarHelper::title(JText::_("SHOP_LIST_PROPERTIES"));
		$ordering = ($filter_order == 'ordering');
		?>
		<form action="index.php" method="post" name="adminForm">
			<table  width="100%">
				<tr>
					<td class="key" >
						<?php echo JText::_("CORE_FILTER");?>:
						<input type="text" name="searchProperty" id="searchProperty" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
						<button onclick="document.getElementById('searchProperty').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>			
					</td>
				</tr>
			</table>
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
					<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PROPERTIES_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderProperties' ); ?></th>			
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("cORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("SHOP_PROPERTIES_MANDATORY"), 'mandatory', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
									
					
				</tr>
			</thead>
			<tbody>		
			<?php
				$k = 0;
				for ($i=0, $n=count($rows); $i < $n; $i++)
				{
					$row = $rows[$i];	  		
					$link = 'index.php?option='.$option.'&task=editProperties&cid[]='.$row->id;		
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td width="30px"><?php echo $row->id; ?></td>
				<td> <?php echo JHTML::_('grid.published',$row,$i); ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
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
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	            <td>
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					echo $row->name;
				} 
				else 
				{
					?>
					<a href="<?php echo $link;?>"><?php echo $row->name; ?></a>
					<?php
				}
				?>
				</td>
	            <td><?php echo $row->description; ?></td>
				<td><?php echo $row->mandatory; ?></td>
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->updated)); ?></td>
				
				
			</tr>
			<?php
				$k = 1 - $k;
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
	  	<input type="hidden" name="task" value="listProperties" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="publishedobject" value="properties">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
	<?php		
	}	

	function editPropertiesValues( $property_value, $property, $id, $languages, $labels,$option ){
		global  $mainframe;
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("SHOP_TITLE_EDIT_PROPERTIES_VALUE")." : ".$property->name, 'generic.png' );
		if ($property->id == -1){
			$mainframe->enqueueMessage(JText::_("SHOP_ERROR_NO_PROPERTY_ID"),"ERROR");	
		}
		else
		{
		?>				
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<legend><?php echo JText::_("SHOP_GENERAL"); ?></legend>
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("CORE_ID"); ?> : </td>
								<td><?php echo $property_value->id; ?>
								<input type="hidden" name="id" value="<?php echo $id;?>"></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("SHOP_PROPERTIES_VALUE_PROPERTIES_NAME"); ?> : </td>																						
								<td><?php echo JText::_($property->name); ?></td>
							</tr>
  							<tr>							
								<td class="key"><?php echo JText::_("CORE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="name" value="<?php echo $property_value->name; ?>" /></td>
							</tr>												
							<tr>							
								<td class="key"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="description" value="<?php echo $property_value->description; ?>" /></td>
							</tr>
							
							<tr>
							<td colspan="2">
							
							</td>
							</tr>
						</table>
					</fieldset>
					<fieldset>
							<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
							<table>
							<?php
							foreach ($languages as $lang)
							{ 
							?>
									<tr>
									<td  class="key" WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
									<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php echo $labels[$lang->id]?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
									</tr>
							<?php
							}
							?>
							</table>
							</fieldset>
				</td>
			</tr>
		</table>

		<input type="hidden" name="property_id" value="<?php echo $property->id; ?>" />
		<input type="hidden" size="50" maxlength="100" name="ordering" value="<?php echo $property_value->ordering; ?>" />
		<input type="hidden" name="guid" value="<?php echo $property_value->guid; ?>" />
		<input type="hidden" name="createdby" value="<?php echo $property_value->createdby; ?>" />
		<input type="hidden" name="created" value="<?php echo $property_value->created; ?>" />
		<input type="hidden" name="checked_out" value="<?php echo $property_value->checked_out; ?>" />
		<input type="hidden" name="checked_out_time" value="<?php echo $property_value->checked_out_time; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<?php
		}
	}
	
	function listPropertiesValues($use_pagination, $rows,$property, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
		JToolBarHelper::title(JText::_("SHOP_LIST_PROPERTIES_VALUES")." : ".JText::_($property->name));
		$user	=& JFactory::getUser();
		$ordering = ($filter_order == 'ordering');
		?>
		<form action="index.php" method="post" name="adminForm">
		<input type ="hidden" name ="property_id" value ="<?php echo $property->id; ?>">
			<table class="admintable" width="100%">
			<tr>
				<td class="key" align="right" width="100%">
					<?php echo JText::_("FILTER"); ?>:
					<input type="text" name="searchPropertyValue" id="searchPropertyValue" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( "GO" ); ?></button>
					<button onclick="document.getElementById('searchPropertyValue').value='';this.form.submit();"><?php echo JText::_( "RESET" ); ?></button>
				</td>
			</tr>
		</table>
			<table width="100%">
				<tr>																																			
					<td align="left"><b><?php echo JText::_("CORE_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listPropertiesValues\');"',$use_pagination); ?></td>
				</tr>
			</table>
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
					<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderPropertiesValues' ); ?></th>			
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>				
								
				</tr>
			</thead>
			<tbody>		
			<?php
			$k = 0;
			for ($i=0, $n=count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];	  				
				$link = 'index.php?option='.$option.'&task=editPropertiesValues&cid[]='.$row->id.'&property_id='.$property->id; 
			?>
				<tr class="<?php echo "row$k"; ?>">
					<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
					<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
					<td width="30px"><?php echo $row->id; ?></td>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<td width="100px" align="right">
						<?php
						if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
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
						<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
		            </td>
		            <td>
					<?php 
					if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
					{
						echo $row->name;
					} 
					else 
					{
						?>
						<a href="<?php echo $link;?>"><?php echo $row->name; ?></a>
						<?php
					}
					?>
					</td>
					<td><?php echo $row->description; ?></td>
					<td><?php echo date('d.m.Y H:i:s',strtotime($row->updated)); ?></td>
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