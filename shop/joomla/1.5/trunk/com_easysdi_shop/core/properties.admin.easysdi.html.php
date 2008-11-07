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

class HTML_properties {

	function editProperties( $rowProperties,$id, $option ){
		
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
								<td width="100p"><?php echo JText::_("EASYSDI_ID"); ?> : </td>
								<td><?php echo $rowProperties->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>
			
							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_ORDER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="order" value="<?php echo $rowProperties->order; ?>" /></td>
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
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="partner_id" value="<?php echo $rowProperties->partner_id; ?>" /></td>
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
								<input type="hidden"  name="update_date" value="<?php echo date('d.m.Y H:i:s',strtotime($rowProperties->update_date)); ?>" />
								<td><?php echo date('d.m.Y H:i:s',strtotime($rowProperties->update_date)); ?></td>
							</tr>
							<tr>
															
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_TEXT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="text" value="<?php echo $rowProperties->text; ?>" /></td>
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
	
	
	
	function listProperties($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROPERTIES"));
		
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listProperties\');" />			
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
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_DEF"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_PUBLISHED"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_ORDER"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_MANDATORY"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_UPDATE_DATE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_TEXT"); ?></th>				
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
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
								
				<td><?php echo $row->id; ?></td>
				<td> <?php echo JHTML::_('grid.published',$row,$i); ?></td>
				<td><?php echo $row->order; ?></td>				
				<td><?php echo $row->mandatory; ?></td>				
							
				<td><?php echo date('d.m.Y H:i:s',strtotime($row->update_date)); ?></td>
				<td><?php echo $row->text; ?></td>
				
				
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
	  	<input type="hidden" name="task" value="listProperties" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="publishedobject" value="properties">
	  </form>
<?php
		
}	











	function editPropertiesValues( $rowProperties,$id, $option ){
		
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		$tabs =& JPANE::getInstance('Tabs');
		JToolBarHelper::title( JText::_("EASYSDI_TITLE_EDIT_PROPERTIES"), 'generic.png' );
		$properties_id = JRequest::getVar(properties_id,-1);
		
		if ($properties_id == -1){
			$mainframe->enqueueMessage(JText::_("EASYSDI_ERROR_NO_PROPERTY_ID"),"ERROR");	
			
		}else{
		$query = "SELECT * FROM #__easysdi_product_properties_definition where id=".$properties_id;		
		 													
		$database->setQuery( $query );
		$rows = $database->loadObject();
		if ($database->getErrorNum()) {						
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						 		
		}		
		
		
		?>				
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<input type="hidden" name="properties_id" value="<?php echo $properties_id; ?>" />
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
								<td width="100p"><?php echo JText::_("EASYSDI_ID"); ?> : </td>
								<td><?php echo $rowProperties->id; ?></td>
								<input type="hidden" name="id" value="<?php echo $id;?>">								
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_PROPERTIES_NAME"); ?> : </td>																						
								<td><?php echo JText::_($rows->text); ?></td>
							</tr>
							<tr>
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_ORDER"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="order" value="<?php echo $rowProperties->order; ?>" /></td>
							</tr>
  							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_VALUE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="value" value="<?php echo $rowProperties->value; ?>" /></td>
							</tr>
																				
							<tr>							
								<td><?php echo JText::_("EASYSDI_PROPERTIES_VALUE_TEXT"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="text" value="<?php echo $rowProperties->text; ?>" /></td>
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
	}
	
	
	
	function listPropertiesValues($properties_id,$use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		$query="select text from #__easysdi_product_properties_definition where id = $properties_id";		 
		 $database->setQuery( $query );			
		$row = $database->loadObject() ;
		JToolBarHelper::title(JText::_("EASYSDI_LIST_PROPERTIES")." ".JText::_($row->text));
		
		
		?>
	
	<form action="index.php" method="post" name="adminForm">
	<input type ="hidden" name ="properties_id" value ="<?php echo $properties_id ?>">	  
		<table width="100%">
			<tr>
				<td align="right">
					<b><?php echo JText::_("EASYSDI_FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" onChange="javascript:submitbutton(\'listProperties\');" />			
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
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_DEF"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_VALUES_ID"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_ORDER"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_VALUE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_PROPERTIES_TEXT"); ?></th>				
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
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>								
				<td><?php echo $row->id; ?></td>						
				<td>				
            <span><?php echo $pageNav->orderUpIcon($i, true, 'orderupproperties', 'Move Up', isset($rows[$i-1]) ); ?></span>
            <span><?php echo $pageNav->orderDownIcon($i, $n, true, 'orderdownproperties', 'Move Down', isset($rows[$i+1]) ); ?></span>
				<td><?php echo $row->value; ?></td>
				<td><?php echo $row->text; ?></td>
				
				
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
	  	<input type="hidden" name="task" value="listProperties" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  </form>
<?php
		
}



}
?>