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

//foreach($_POST as $key => $val) 
//echo '$_POST["'.$key.'"]='.$val.'<br />';


class HTML_object {

	function editObject($rowObject, $rowMetadata, $id, $accounts, $objecttypes, $visibilities, $projections, $fieldsLength, $languages, $labels, $editors, $selected_editors, $managers, $selected_managers, $hasVersioning, $pageReloaded, $option ){
		
		global  $mainframe;
				
		$database =& JFactory::getDBO(); 

		JHTML::_('behavior.calendar');

		$baseMaplist = array();		

		jimport("joomla.utilities.date");
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
		$database =& JFactory::getDBO(); 
		
		?>				
	<form action="index.php" method="POST" name="adminForm" id="adminForm" class="adminForm" onsubmit="Pre_Post('adminForm', 'selected_managers', 'manager'); Pre_Post('adminForm', 'selected_editors', 'editor');document.getElementById('adminForm').submit;">
		<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<table class="admintable" border="0" cellpadding="3" cellspacing="0">
<?php
if (!$hasVersioning)
{ 
?>
						<tr>
							<td class="key"><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" name="metadata_guid" value="<?php if ($pageReloaded) echo $_POST['metadata_guid']; else echo $rowMetadata->guid; ?>" disabled="disabled" /></td>								
						</tr>
						
<?php
}
?>
						<tr>
							<td class="key"><?php echo JText::_("CORE_NAME"); ?> : </td>
							<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php if ($pageReloaded) echo $_POST['name']; else echo $rowObject->name; ?>" /></td>								
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
							<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php if ($pageReloaded) echo $_POST['description']; else echo $rowObject->description?></textarea></td>								
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
							<td><?php echo JHTML::_('select.booleanlist', 'published', '', ($pageReloaded)? $_POST['published'] : $rowObject->published); ?> </td>																
						</tr>
						<tr>							
							<td class="key"><?php echo JText::_("CATALOG_OBJECT_VISIBILITY_LABEL"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$visibilities, 'visibility_id', 'size="1" class="inputbox"', 'value', 'text', ($pageReloaded)? $_POST['visibility_id'] : $rowObject->visibility_id  ); ?></td>								
						</tr>
						<tr>
							<td colspan="2">
								<fieldset>
									<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
									<table class="admintable" border="0" cellpadding="0" cellspacing="0">
		<?php
		foreach ($languages as $lang)
		{ 
		?>
							<tr>
							<td class="key"><?php echo JText::_("CORE_".strtoupper($lang->code)); ?> : </td>
							<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded) echo htmlspecialchars($_POST['label_'.$lang->code]); else echo htmlspecialchars($labels[$lang->id])?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
							</tr>
		<?php
		}
		?>
									</table>
								</fieldset>
							</td>
						</tr>
						<tr>							
							<td class="key"><?php echo JText::_("CORE_OBJECT_TYPE_LABEL"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$objecttypes, 'objecttype_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObject\');"', 'value', 'text', ($pageReloaded)? $_POST['objecttype_id'] : $rowObject->objecttype_id  ); ?></td>								
						</tr>
						<tr>							
							<td class="key"><?php echo JText::_("CORE_OBJECT_SUPPLIERNAME"); ?> : </td>
							<td><?php echo JHTML::_("select.genericlist",$accounts, 'account_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObject\');"', 'value', 'text', ($pageReloaded)? $_POST['account_id'] : $rowObject->account_id ); ?></td>								
						</tr>
						<tr>
					<td colspan=2>
					<fieldset>
						<legend><?php echo JText::_( 'CORE_MANAGER_NAME'); ?></legend>
						<table class="admintable" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th><b><?php echo JText::_( 'CORE_AVAILABLE'); ?></b></th>
								<th></th>
								<th><b><?php echo JText::_( 'CORE_SELECTED'); ?></b></th>
							</tr>
							<tr>
								<td>
									<select name="managers[]" id="managers" size="10" multiple="multiple">
									<?php
									foreach ($managers as $manager){
										echo "<option value='".$manager->value."'>".$manager->text."</option>";
									}
									?>
									</select></td>
								<td>
								<table>
									<tr>
										<td><input type="button" value="<<" id="removeAllmanagers"
											onclick="javascript:TransfertAll('selected_managers','managers');"></td>
									</tr>
									<tr>
										<td><input type="button" value="<" id="removemanager"
											onclick="javascript:Transfert('selected_managers', 'managers');"></td>
									</tr>
									<tr>
										<td><input type="button" value=">" id ="addmanager"
											onclick="javascript:Transfert('managers','selected_managers');"></td>
									</tr>
									<tr>
										<td><input type="button" value=">>" id="addAllmanagers"
											onclick="javascript:TransfertAll('managers', 'selected_managers');"></td>
									</tr>
								</table>
								</td>
								<td>
									<select name="selected_managers[]" id="selected_managers" size="10" multiple="multiple">
									<?php
										foreach ($selected_managers as $selected_managers){
											echo "<option value='".$selected_managers->value."'>".$selected_managers->text."</option>";
										}
									?>
								</select></td>
							</tr>
							</table>
					</fieldset>
				</td>
			</tr>	
			<tr>
					<td colspan=2>
					<fieldset>
						<legend><?php echo JText::_( 'CORE_EDITOR_NAME'); ?></legend>
						<table class="admintable" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<th><b><?php echo JText::_( 'CORE_AVAILABLE'); ?></b></th>
								<th></th>
								<th><b><?php echo JText::_( 'CORE_SELECTED'); ?></b></th>
							</tr>
							<tr>
								<td>
									<select name="editors[]" id="editors" size="10" multiple="multiple">
									<?php
									foreach ($editors as $editor){
										echo "<option value='".$editor->value."'>".$editor->text."</option>";
									}
									?>
									</select></td>
								<td>
								<table>
									<tr>
										<td><input type="button" value="<<" id="removeAlleditors"
											onclick="javascript:TransfertAll('selected_editors','editors');"></td>
									</tr>
									<tr>
										<td><input type="button" value="<" id="removeeditor"
											onclick="javascript:Transfert('selected_editors', 'editors');"></td>
									</tr>
									<tr>
										<td><input type="button" value=">" id ="addeditor"
											onclick="javascript:Transfert('editors','selected_editors');"></td>
									</tr>
									<tr>
										<td><input type="button" value=">>" id="addAlleditors"
											onclick="javascript:TransfertAll('editors', 'selected_editors');"></td>
									</tr>
								</table>
								</td>
								<td>
									<select name="selected_editors[]" id="selected_editors" size="10" multiple="multiple">
									<?php
										foreach ($selected_editors as $selected_editors){
											echo "<option value='".$selected_editors->value."'>".$selected_editors->text."</option>";
										}
									?>
								</select></td>
							</tr>
							</table>
					</fieldset>
				</td>
			</tr>	
<?php
if ($rowObject->id == 0 and $hasVersioning) {
?>
			<tr>
				<td colspan=2>
					<fieldset>
						<legend><?php echo JText::_( 'CATALOG_OBJECT_OBJECTVERSION'); ?></legend>
						<table class="admintable" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="key"><?php echo JText::_("CATALOG_OBJECT_OBJECTVERSION_METADATAID_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" name="metadata_guid" value="<?php if ($pageReloaded) echo $_POST['metadata_guid']; else echo $rowMetadata->guid; ?>" disabled="disabled" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("CATALOG_OBJECT_OBJECTVERSION_DESCRIPTION_LABEL"); ?> : </td>
								<td><textarea rows="4" cols="50" name ="version_description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php if ($pageReloaded) echo $_POST['version_description'];?></textarea></td>								
							</tr>	
						</table>
					</fieldset>
				</td>
			</tr>	
<?php 
}?>
					</table>
					<br></br>
					<table border="0" cellpadding="3" cellspacing="0">
<?php
$user =& JFactory::getUser();
if ($rowObject->created)
{ 
?>
						<tr>
							<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
							<td><?php if ($rowObject->created) {echo date('d.m.Y h:i:s',strtotime($rowObject->created));} ?></td>
							<td>, </td>
							<?php
								if ($rowObject->createdby and $rowObject->createdby<> 0)
								{
									$query = "SELECT name FROM #__users WHERE id=".$rowObject->createdby ;
									$database->setQuery($query);
									$createUser = $database->loadResult();
								}
								else
									$createUser = "";
							?>
							<td><?php echo $createUser; ?></td>
						</tr>
<?php
}
if ($rowObject->updated)
{ 
?>
						<tr>
							<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
							<td><?php if ($rowObject->updated and $rowObject->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($rowObject->updated));} ?></td>
							<td>, </td>
							<?php
								if ($rowObject->updatedby and $rowObject->updatedby<> 0)
								{
									$query = "SELECT name FROM #__users WHERE id=".$rowObject->updatedby ;
									$database->setQuery($query);
									$updateUser = $database->loadResult();
								}
								else
									$updateUser = "";
							?>
							<td><?php echo $updateUser; ?></td>
						</tr>
<?php
}
?>
					</table>
				</td>
			</tr>
			
		</table>
		<input type="hidden" name="cid[]" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $rowObject->guid; ?>" />
		<input type="hidden" name="ordering" value="<?php echo $rowObject->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo ($rowObject->created)? $rowObject->created : date ('Y-m-d H:i:s');?>" />
		<input type="hidden" name="createdby" value="<?php echo ($rowObject->createdby)? $rowObject->createdby : $user->id; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo ($rowObject->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo ($rowObject->createdby)? $user->id : ''; ?>" /> 
		<input type="hidden" name="metadata_guid" value="<?php if ($pageReloaded) echo $_POST['metadata_guid']; else echo $rowMetadata->guid; ?>" />
		
		<input type="hidden" name="id" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
	<?php
	}
	
	function listObject($rows, $lists, $page, $filter_order_Dir, $filter_order, $option)
	{
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$ordering = ($filter_order == 'ordering');
		
		$partners =	array(); ?>
		<form action="index.php" method="post" name="adminForm">
			<table width="100%">
				<tr>
					<td width="100%">
						<?php echo JText::_( 'Filter' ); ?>:
						<input type="text" name="searchObject" id="searchObject" value="<?php echo $lists['searchObject'];?>" class="text_area" onchange="document.adminForm.submit();" />
						<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
						<button onclick="document.getElementById('searchObject').value='';document.getElementById('filter_account_id').value='-1';document.getElementById('filter_objecttype_id').value='-1';document.getElementById('filter_state').value='-1';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
					</td>
					<td align="right" width="100%" nowrap="nowrap">
						<?php
						echo $lists['account_id'];
						?>
					</td>
					<td align="right" width="100%" nowrap="nowrap">
						<?php
						echo $lists['objecttype_id'];
						?>
					</td>
					<td align="right" nowrap="nowrap">
						<?php
						echo $lists['state'];
						?>
					</td>
				</tr>
			</table>
			
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
					<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
					<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
					<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderObject' ); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECT_MANAGEVERSION"), 'hasVersioning', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JText::_("CATALOG_OBJECT_MANAGEAPPLICATION"); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_OBJECT_SUPPLIERNAME"), 'account_name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_OBJECTTYPE_NAME"), 'objecttype_name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JText::_("CORE_METADATA_MANAGERS"); ?></th>
					<th class='title'><?php echo JText::_("CORE_METADATA_EDITORS"); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
				</tr>
			</thead>
			<tbody>		
	<?php
			$k = 0;
			$i=0;
			foreach ($rows as $row)
			{			
				$checked 	= JHTML::_('grid.checkedout',   $row, $i );
					  				
	?>
				<tr> <!-- class="<?php //echo "row$k"; ?>" -->
					<td align="center" width="10px"><?php echo $page->getRowOffset( $i );//echo $i+$page->limitstart+1;?></td>
					<td align="center">
					<?php echo $checked; ?>
				</td>	
					<td width="30px" align="center"><?php echo $row->id; ?></td>
					<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
					<td width="100px" align="right">
						<?php
						if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
							if ($disabled){
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderupObject', '', false ); ?>
					             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObject', '', false ); ?>
			            <?php
							}
							else {
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderupObject', 'Move Up', isset($rows[$i-1]) ); ?>
					             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObject', 'Move Down', isset($rows[$i+1]) ); ?>
						<?php
							}		
						}
						else{ 
							if ($disabled){
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderdownObject', '', false ); ?>
					             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObject', '', false ); ?>
			            <?php
							}
							else {
						?>
								 <?php echo $page->orderUpIcon($i, true, 'orderdownObject', 'Move Down', isset($rows[$i-1]) ); ?>
			 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObject', 'Move Up', isset($rows[$i+1]) ); ?>
						<?php
							}
						}?>
						<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
		            </td>
					<?php
					$link = "index.php?option=$option&amp;task=editObject&cid[]=$row->id";
					?>								
					
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
					<td align="center">
						<?php 
						if ($row->hasVersioning)
						{
						?>
							<?php $link =  "index.php?option=$option&amp;task=listObjectVersion&object_id=$row->id";?>
							<?php 
							if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
							{
								?>
								<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
								<?php
							} 
							else 
							{
								?>
								<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'CATALOG_OBJECT_MANAGEVERSION' ); ?>">
									<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
								</a>
								<?php
							}
							?>
							<?php
						}
						?>							
					</td>
					<td align="center">
						<?php $link =  "index.php?option=$option&amp;task=listApplication&object_id=$row->id";?>
						<?php 
						if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
						{
							?>
							<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
							<?php
						} 
						else 
						{
							?>
							<a href="<?php echo $link; ?>" title="<?php echo JText::_( 'CATALOG_OBJECT_MANAGEAPPLICATION' ); ?>">
								<img src="<?php echo JURI::root(true); ?>/includes/js/ThemeOffice/mainmenu.png" border="0" />
							</a>
							<?php
						}
						?>
					</td>
					<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'object_'); ?></td>
					<td><?php echo $row->account_name; ?></td>						
					<td><?php echo $row->description; ?></td>		
					<td><?php echo $row->objecttype_name; ?></td>
					<?php 		
					$managers = "";
					$database->setQuery( "SELECT b.name FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
					$managers = implode(", ", $database->loadResultArray());
					
					$editors = "";
					$database->setQuery( "SELECT b.name FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
					$editors = implode(", ", $database->loadResultArray());
					?>
					<td><?php echo $managers; ?></td>		
					<td><?php echo $editors; ?></td>		
					<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
				</tr>
	<?php
				$k = 1 - $k;
				$i ++;
			}
			
				?>
			</tbody>
			<tfoot>
			<tr>	
			<td colspan="15"><?php echo $page->getListFooter(); ?></td>
			</tr>
			</tfoot>
			</table>
		  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
		  	<input type="hidden" name="task" value="listObject" />
		  	<input type="hidden" name="boxchecked" value="0" />
		  	<input type="hidden" name="hidemainmenu" value="0">
		  	<input type="hidden" name="publishedobject" value="object">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		  </form>
	<?php
			
	}	

	function alter_array_value_with_JTEXT_(&$rows)
	{		
		if (count($rows)>0)
		{
			foreach($rows as $key => $row)
			{		  	
      			$rows[$key]->text = JText::_($rows[$key]->text);
  			}			    
		}
	}
}
?>