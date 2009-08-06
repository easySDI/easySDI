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
/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/
defined('_JEXEC') or die('Restricted access');


class HTML_metadata {

function listMetadataTabs($use_pagination, $rows, $pageNav,$option, $filter_order_Dir, $filter_order, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_TABS"));
		
		$partners = array();
		
		$ordering = ($filter_order == 'ordering');
		?>
	<form action="index.php" method="post" name="adminForm">
		<table>
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
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandardClasses\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title' width="10px"><?php echo JText::_("EASYSDI_METADATA_TABS_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_TABS_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderMetadataTabs' ); ?></th>			
				<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_TABS_TEXT"), 'text', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' ><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_PARTNER_NAME"), 'partner_name', @$filter_order_Dir, @$filter_order); ?></th>														
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
				<td align="center" width="10px"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataTabs', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataTabs', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataTabs', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataTabs', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataTabs', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataTabs', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataTabs', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataTabs', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>	
				<?php $link =  "index.php?option=$option&amp;task=editMetadataTab&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->text; ?></a></td>
				<td><?php echo $row->partner_name; ?></td>
				<!-- <td><?php echo $row->partner_name; ?></td>  -->																											
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataTabs" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
	
function editMetadataTabs($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
														
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	 								
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TEXT_KEY"); ?></td>
		<td><input size="50" type="text" name ="text" value="<?php echo $row->text?>"> </td>							
	</tr>							

	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TABS_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				

	</table> 
	<input type="hidden" name="order" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
			
</form>
	<?php 	
		
	}
	
function listStandardClasses($use_pagination, $rows, $pageNav,$option,$type, $filter_order, $filter_order_Dir, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_STANDARD_CLASSES"));
		
		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_standard WHERE is_deleted =0 " );
						
 
			$types =  $database->loadObjectList() ;													
	
		$partners = array();
		
		$ordering = ($filter_order == 'ordering');
		?>
	<form action="index.php" method="post" name="adminForm">
		<table>
			<tr>
				<td align="left" width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="searchStd" id="searchStd" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('searchStd').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandardClasses\');"',$use_pagination); ?></td>
											
				<td class="user"><b><?php echo JText::_("EASYSDI_TITLE_STANDARD"); ?></b><?php echo JHTML::_("select.genericlist", $types, 'type', 'size="1" class="inputbox" onChange="javascript:submitbutton(\'listMetadataStandardClasses\');"', 'value', 'text', $type ); ?></td>
			</tr>
		
	
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title' width="10px"><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_ID"), 'id', @$filter_order_Dir, @$filter_order ); ?>		
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order ); ?>		
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderMetadataStandardClasses' ); ?></th>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_STANTARD_NAME"), 'standard_name', @$filter_order_Dir, @$filter_order ); ?>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_CLASS_NAME"), 'class_name', @$filter_order_Dir, @$filter_order ); ?>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_STANDARD_CLASSES_POSITION"), 'position', @$filter_order_Dir, @$filter_order ); ?>
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
				<td width="10px" align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataStandardClasses', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataStandardClasses', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataStandardClasses', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataStandardClasses', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataStandardClasses', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataStandardClasses', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataStandardClasses', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataStandardClasses', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>	
				<?php $link =  "index.php?option=$option&amp;task=editMetadataStandardClasses&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->standard_name; ?></a></td>
				<td><a href="<?php echo $link;?>"><?php echo $row->class_name; ?></a></td>																												
				<td><?php echo $row->position; ?></td>							
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataStandardClasses" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
	
function editStandardClasses($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
		$tabslist = array();
		$tabslist[] = JHTML::_('select.option','0', JText::_("EASYSDI_TABS_LIST") );
		$database->setQuery( "SELECT id AS value,  text AS text FROM #__easysdi_metadata_tabs  " );
		$tabslist = $database->loadObjectList() ;
		
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
			
		$classeslist = array();
		$classeslist[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_classes  WHERE is_final = 1 ORDER BY name" );
		$classeslist = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
		$selClassesList = array();		
		$database->setQuery( "SELECT class_id AS value FROM #__easysdi_metadata_standard_classes  WHERE id = $row->id ");
		$selClassesList  = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}						
		
		
		$standardlist = array();
		$standardlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_STANDARD_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_standard  WHERE is_deleted =0  ORDER BY name" );
		$standardlist = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}
					
		$selStandardList = array();		
		$database->setQuery( "SELECT standard_id AS value FROM #__easysdi_metadata_standard_classes  WHERE id = $row->id ");
		$selStandardList  = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}	
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
		
	<tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_STANDARD"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$standardlist, 'standard_id', 'size="1" class="inputbox"', 'value', 'text', $selStandardList ); ?></td>
	 </tr>
	 								
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$classeslist, 'class_id', 'size="1" class="inputbox"', 'value', 'text', $selClassesList ); ?></td>
	 </tr>
	 <tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>
	 <tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_CLASSES_TAB_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$tabslist, 'tab_id', 'size="1" class="inputbox"', 'value', 'text', $row->tab_id ); ?></td>							
	</tr> 		
			
	
	</table>
	 
	<input type="hidden" name="order" value="0" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}
	
		
	
function listStandard($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_STANDARD"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataStandard\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_STANDARD_INHERITED"); ?></th>																							
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>
				<?php $link =  "index.php?option=$option&amp;task=editMetadataStandard&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
												
				
				<td><?php echo $row->inherited; ?></td>							
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataStandard" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
function editStandard($row,$id, $option ){
		global  $mainframe;
		
		JToolBarHelper::title(JText::_("EASYSDI_EDIT_METADATA_STANDARD"));
		
		$database =& JFactory::getDBO(); 

		$standards = array();
		$standards[] = JHTML::_('select.option','0', JText::_("EASYSDI_STANDARDS_LIST") );
		$database->setQuery( "SELECT id AS value, name AS text FROM #__easysdi_metadata_standard  where is_deleted =0 AND is_global = 1 ORDER BY name" );
		$standards = array_merge( $standards, $database->loadObjectList() );
		
		
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_INHERITED"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$standards, 'inherited', 'size="1" class="inputbox"', 'value', 'text',  $row->inherited ); ?></td>		 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_STANDARD_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td>		 
	</tr>		
			
	
	</table>
	 
	 
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}
	
	
	
	
	
	
	
		
function listExt($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_EXT"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataLocfreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_EXT_NAME"); ?></th>			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>	
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>
				<?php $link =  "index.php?option=$option&amp;task=editMetadataExt&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>
												
				
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataExt" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
	
function editExt($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_EXT_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>						
			
	
	</table>
	 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}

function listLocfreetext($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_LOCFREETEXT"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataLocfreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_NAME"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_LANG"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_DEFAULT_VALUE"); ?></th>																			
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>
				<?php $link =  "index.php?option=$option&amp;task=editMetadataLocfreetext&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>
				<td><?php echo $row->lang; ?></td>
				<td><?php echo $row->default_value; ?></td>				
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataFreetext" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
	
function editLocfreetext($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	

	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>		
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>
		<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_LANG"); ?></td>
		<td><input size="50" type="text" name ="lang" value="<?php echo $row->lang?>"> </td>		 
	</tr>								
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LOCFREETEXT_DEFAULT_VALUE"); ?></td>
		<td><input size="50" type="text" name ="default_value" value="<?php echo $row->default_value?>"> </td>		 
	</tr>
	 
	</table>
	
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />		
	
</form>
	<?php 	
		
	}
	
	
	
function listClass($use_pagination, $rows, $pageNav,$option, $filter_order, $filter_order_Dir, $search){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_CLASS"));
		
		$partners = array();
		
		$ordering = ($filter_order == 'ordering');
		?>
	<form action="index.php" method="post" name="adminForm">
	
		<table>
			<tr>
				
				<td align="left" width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="search" id="search" value="<?php echo $search;?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataClass\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th width="10px" class='title'><?php echo JText::_("EASYSDI_METADATA_CLASS_SHARP"); ?></th>
				<th width="10px" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th width="30px" class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_ID"), 'id', @$filter_order_Dir, @$filter_order ); ?></th>		
				<th width="100px" class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_PROPERTIES_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order ); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderMetadataClass' ); ?></th>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_PARTNER_NAME"), 'user_name', @$filter_order_Dir, @$filter_order ); ?></th>		
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_NAME"), 'class_name', @$filter_order_Dir, @$filter_order ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_ISOKEY"), 'iso_key', @$filter_order_Dir, @$filter_order ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("EASYSDI_METADATA_CLASS_TYPE"), 'type', @$filter_order_Dir, @$filter_order ); ?></th>																				
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
				<td width="10px" align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px"><?php echo $row->id; ?></td>
	            <?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataClass', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataClass', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderupMetadataClass', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderdownMetadataClass', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataClass', '', false ); ?>
				             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataClass', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $pageNav->orderUpIcon($i, true, 'orderdownMetadataClass', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $pageNav->orderDownIcon($i, count($rows)-1, true, 'orderupMetadataClass', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="order[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
				<td><?php echo $row->user_name; ?></td>								
				<?php $link =  "index.php?option=$option&amp;task=editMetadataClass&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->class_name; ?></a></td>												
				<td><?php echo $row->iso_key; ?></td>
				<td><?php echo $row->type; ?></td>						
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataClass" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
		
}	
	
	function editClass($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
		$freetextlist = array();
		$freetextlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_FREETEXT_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_freetext ORDER BY name" );
		$freetextlist = array_merge( $freetextlist, $database->loadObjectList() );

		$locfreetextlist = array();
		$locfreetextlist[] = JHTML::_('select.option','0', JText::_("EASYSDI_LOCFREETEXT_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_loc_freetext ORDER BY name" );
		$locfreetextlist = $database->loadObjectList() ;
		
		$classeslist = array();
		$classeslist[] = JHTML::_('select.option','0', JText::_("EASYSDI_CLASS_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_classes  where  id <> $row->id ORDER BY name" );
		$classeslist = $database->loadObjectList() ;
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		
		$selClassesList = array();
		if($row->type == 'class'){
			
			$database->setQuery( "SELECT classes_to_id AS value FROM #__easysdi_metadata_classes_classes  WHERE classes_from_id = $row->id ");
			$selClassesList  = $database->loadObjectList() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
			
		}
		$selFreetextList = array();
		if($row->type == 'freetext'){
			$database->setQuery( "SELECT freetext_id AS value FROM #__easysdi_metadata_classes_freetext  WHERE classes_id = $row->id ");
			$selFreetextList   = $database->loadObjectList() ;		
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		}
		
		$selLocfreetextList = array();
		if($row->type == 'locfreetext'){
			$database->setQuery( "SELECT loc_freetext_id AS value FROM #__easysdi_metadata_classes_locfreetext  WHERE classes_id = $row->id ");
			$selLocfreetextList   = $database->loadObjectList() ;
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}				
		}

		
		
		$listlist = array();
		$listist[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_LIST") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_list ORDER BY name" );
		$listlist = array_merge( $listlist, $database->loadObjectList() );

		
		$selListList = array();
		if($row->type == 'list'){
			$database->setQuery( "SELECT list_id AS value FROM #__easysdi_metadata_classes_list  WHERE classes_id = $row->id ");
			$selListList   = $database->loadObjectList() ;		
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		}
		
		$listext = array();
		$listext[] = JHTML::_('select.option','0', JText::_("EASYSDI_LIST_EXT") );
		$database->setQuery( "SELECT id AS value,  name AS text FROM #__easysdi_metadata_ext ORDER BY name" );
		$listext = array_merge( $listext, $database->loadObjectList() );

		
		$selExtList = array();
		if($row->type == 'ext'){
			$database->setQuery( "SELECT ext_id AS value FROM #__easysdi_metadata_classes_ext  WHERE classes_id = $row->id ");
			$selExtList   = $database->loadObjectList() ;		
			if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}		
		}
?>
<script>
function selectTypeParameters(){

var typevalue = document.getElementById('type').value;
document.getElementById('freetext').disabled=true;
document.getElementById('locfreetext').disabled=true;
document.getElementById('class').disabled=true;
document.getElementById('list').disabled=true;
document.getElementById('ext').disabled=true;
document.getElementById(typevalue).disabled=false;
}
var oldLoad = window.onload;
window.onload=function(){
		selectTypeParameters();		
		if (oldLoad) oldLoad();
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	

	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_IS_FINAL"); ?></td>
		<td><select name="is_final" > <option value="1" <?php if($row->is_final == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_final == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
					
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_ISO_KEY"); ?></td>
		<td><input size="50" type="text" name ="iso_key" value="<?php echo $row->iso_key?>"> </td>		 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_CLASS_TYPE"); ?></td>
		<td><select name="type" id="type" onChange="selectTypeParameters()"> 
				<option value="freetext" <?php if($row->type == "freetext") echo "selected"; ?>><?php echo JText::_("EASYSDI_FREETEXT"); ?></option> 
 				<option value="locfreetext" <?php if($row->type == "locfreetext") echo "selected"; ?>><?php echo JText::_("EASYSDI_LOCFREETEXT"); ?></option>
 				<option value="ext" <?php if($row->type == "ext") echo "selected"; ?>><?php echo JText::_("EASYSDI_EXT"); ?></option>
 				<option value="class" <?php if($row->type == "class") echo "selected"; ?>><?php echo JText::_("EASYSDI_CLASS"); ?></option>
 				<option value="list" <?php if($row->type == "list") echo "selected"; ?>><?php echo JText::_("EASYSDI_LIST"); ?></option>
		</select></td> 				
	</tr>	
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_LIST_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$listlist, 'list[]', 'size="1" class="inputbox"', 'value', 'text', $selListList ); ?></td>
	 </tr>
	  <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_EXT_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$listext, 'ext[]', 'size="1" class="inputbox"', 'value', 'text', $selExtList ); ?></td>
	 </tr>
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_FREETEXT_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$freetextlist, 'freetext[]', 'size="1" class="inputbox"', 'value', 'text', $selFreetextList ); ?></td>
	 </tr>
	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_LOCFREETEXT_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$locfreetextlist, 'locfreetext[]', 'size="10" multiple class="inputbox"', 'value', 'text', $selLocfreetextList ); ?></td>
	 </tr>

	 <tr>
	 	<td><?php echo JText::_("EASYSDI_METADATA_CLASS_CLASSES_CHOICE"); ?></td>
	 	<td><?php echo JHTML::_("select.genericlist",$classeslist, 'class[]', 'size="10" multiple class="inputbox"', 'value', 'text', $selClassesList ); ?></td>
	 </tr>
	 
	</table> 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}
	
	
	
	
	function editFreetext($row,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	 
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>"> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $row->translation?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DESCRIPTION"); ?></td>
		<td><input size="50" type="text" name ="description" value="<?php echo $row->description?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_GLOBAL"); ?></td>
		<td><select name="is_global" > <option value="1" <?php if($row->is_global == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_global == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_CONSTANT"); ?></td>
		<td><select name="is_constant" > <option value="1" <?php if($row->is_constant == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_constant == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_ID"); ?></td>
		<td><select name="is_id" > <option value="1" <?php if($row->is_id == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_id == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_DATE"); ?></td>
		<td><select name="is_date" > <option value="1" <?php if($row->is_date == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_date == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_NUMBER"); ?></td>
		<td><select name="is_number" > <option value="1" <?php if($row->is_number == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_number == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_IS_CONSTANT"); ?></td>
		<td><select name="is_constant" > <option value="1" <?php if($row->is_constant == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($row->is_constant == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td> 
	</tr>				
					
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DEFAULT_VALUE"); ?></td>
		<td><input size="50" type="text" name ="default_value" value="<?php echo $row->default_value?>"> </td>		 
	</tr>
	
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $row->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}
	
	function editListContent($rowMDList,$id, $option,$list_id ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
			
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">		
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_CODE_KEY"); ?></td>
		<td><input size="50" type="text" name ="code_key" value="<?php echo $rowMDList->code_key?>"> </td>							
	</tr>							
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_ISO_CODE"); ?></td>
		<td><input size="50" type="text" name ="key" value="<?php echo $rowMDList->key?>"> </td>							
	</tr>	
	<!-- 						
	<tr>
		<td><?php //echo JText::_("EASYSDI_METADATA_LIST_ISO_VALUE"); ?></td>
		<td><input size="50" type="text" name ="value" value="<?php //echo $rowMDList->value?>"> </td>							
	</tr>
	 -->			
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $rowMDList->translation?>"> </td>							
	</tr>	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	
	</table>
	 <input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $id;?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}
	
	
	function editList($rowMDList,$id, $option ){
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
				
		$partners = array();
		$partners[] = JHTML::_('select.option','0', JText::_("EASYSDI_PARTNERS_LIST") );
		$database->setQuery( "SELECT a.partner_id AS value, b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id ORDER BY b.name" );
		$partners = array_merge( $partners, $database->loadObjectList() );
		
		
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
	<table border="0" cellpadding="3" cellspacing="0">	
	
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_ID"); ?></td>
		<td><?php echo $rowMDList->id?> </td>							
	</tr>
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_NAME"); ?></td>
		<td><input size="50" type="text" name ="name" value="<?php echo $rowMDList->name?>"> </td>							
	</tr>		
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></td>
		<td><input size="50" type="text" name ="translation" value="<?php echo $rowMDList->translation?>"> </td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_ID"); ?></td>
		<td><?php echo JHTML::_("select.genericlist",$partners, 'partner_id', 'size="1" class="inputbox"', 'value', 'text', $row->partner_id ); ?></td>							
	</tr>				
	<tr>
		<td><?php echo JText::_("EASYSDI_METADATA_LIST_MULTIPLE"); ?></td>
		<td><select name="multiple" > <option value="1" <?php if($rowMDList->multiple == 1) echo "selected"; ?>><?php echo JText::_("EASYSDI_TRUE"); ?></option> 
		<option value="0" <?php if($rowMDList->multiple == 0) echo "selected"; ?>><?php echo JText::_("EASYSDI_FALSE"); ?></option></select></td>		 
	</tr>

	</table> 
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	<input type="hidden" name="id" value="<?php echo $rowMDList->id?>" />
	<input type="hidden" name="task" value="" />
</form>
	<?php 	
		
	}

	function listFreetext($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_FREETEXT"));
		
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataFreetext\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_FREETEXT_DEFAULT_VALUE"); ?></th>																			
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<?php $link =  "index.php?option=$option&amp;task=editMetadataFreetext&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>

				<td><?php echo $row->default_value; ?></td>				
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataFreetext" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	



	function listListContent($use_pagination, $rows, $pageNav,$option,$list_id){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_LIST"));
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataList\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_CODE_KEY"); ?></th>				
				<!-- <th class='title'><?php //echo JText::_("EASYSDI_METADATA_LIST_VALUE"); ?></th> -->															
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>
				<?php $link =  "index.php?option=$option&amp;task=editMetadataListContent&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->code_key; ?></a></td>																												
												
				
				<!-- <td><?php //echo $row->value; ?></td> -->
				<td><?php echo $row->translation; ?></td>
				
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataList" />
	  	<input type="hidden" name="list_id" value="<?php echo $list_id; ?>" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	


	function listList($use_pagination, $rows, $pageNav,$option){
	
		$database =& JFactory::getDBO();
		JToolBarHelper::title(JText::_("EASYSDI_LIST_METADATA_LIST"));
		$partners = array();
		
		?>
	<form action="index.php" method="post" name="adminForm">
		
		<table width="100%">
			<tr>																																			
				<td align="left"><b><?php echo JText::_("EASYSDI_TEXT_PAGINATE"); ?></b><?php echo  JHTML::_( "select.booleanlist", 'use_pagination','onchange="javascript:submitbutton(\'listMetadataList\');"',$use_pagination); ?></td>
			</tr>
		</table>
		<table class="adminlist">
		<thead>
			<tr>					 			
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_SHARP"); ?></th>
				<th class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_ID"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_PARTNER_NAME"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_LIST_NAME"); ?></th>															
				<th class='title'><?php echo JText::_("EASYSDI_METADATA_TRANSLATION"); ?></th>
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
				<td><?php echo $row->id; ?></td>				
				<?php 
				$query = "SELECT b.name AS text FROM #__easysdi_community_partner a,#__users b where a.root_id is null AND a.user_id = b.id AND partner_id=".$row->partner_id ;
				$database->setQuery($query);				 
		 		?>
				<td><?php echo $database->loadResult(); ?></td>								
				<?php $link =  "index.php?option=$option&amp;task=editMetadataList&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>																												
				<td><?php echo $row->translation; ?></td>
				
			</tr>
<?php
			$k = 1 - $k;
			$i ++;
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
	  	<input type="hidden" name="task" value="listMetadataList" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">	  	
	  </form>
<?php
		
}	
}
?>
