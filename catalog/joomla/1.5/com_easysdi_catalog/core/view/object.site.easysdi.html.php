<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�"Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		JHTML::_('behavior.calendar');

		$baseMaplist = array();		
		jimport("joomla.utilities.date");
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
		$user =& JFactory::getUser();
		$currentAccount = new accountByUserId($database);
		$currentAccount->load($user->id);
		$root_account = $currentAccount->id;
		if ($currentAccount->root_id)
			$root_account = $currentAccount->root_id;
			
		
		
		?>	
		<div id="page">
		<?php 
if ($rowObject->id == 0)
{
?>
			<h2 class="contentheading"><?php echo JText::_( 'CATALOG_NEW_OBJECT' )?></h2>
<?php 
}
else
{
?>
			<h2 class="contentheading"><?php echo JText::_( 'CATALOG_EDIT_OBJECT' )?></h2>
<?php 
}
?>			
	<div id="contentin" class="contentin">
		 <form action="index.php" method="POST" name="adminForm" id="adminForm" class="adminForm" onsubmit="Pre_Post('adminForm', 'selected_managers', 'manager'); Pre_Post('adminForm', 'selected_editors', 'editor');document.getElementById('adminForm').submit;">
		<div class="row">
			 <div class="row">
				<input type="button" id="simple_search_button" name="simple_search_button" class="submit" value ="<?php echo JText::_("CORE_SAVE"); ?>" onClick="Pre_Post('adminForm', 'selected_managers', 'manager'); Pre_Post('adminForm', 'selected_editors', 'editor'); verify();"/>
				<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('adminForm').task.value='cancelObject';window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=cancelObject&object_id='.$rowObject->id)); ?>', '_self')"/>
			</div>	 
		 </div>
		<div class="row">
			<div class="row">
				<div class="row">
							<?php
if (!$hasVersioning)
{ 
?>
						<div class="row">
							<label for="metadata_guid"><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </label>
							<input class="inputbox text full" type="text" size="50" name="metadata_guid" value="<?php if ($pageReloaded) echo $_POST['metadata_guid']; else echo $rowMetadata->guid; ?>" disabled="disabled" />								
						</div>
						
<?php
}
?>
							<div class="row">
								<label for="name"><?php echo JText::_("CORE_NAME"); ?> : </label>
								<input class="inputbox" type="text" size="50" class="text full" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php if ($pageReloaded) echo $_POST['name']; else echo $rowObject->name; ?>" />								
							</div>
							<div class="row">
								<label for="description"><?php echo JText::_("CORE_DESCRIPTION"); ?> : </label>
								<textarea rows="4" cols="50" name ="description" class="text full" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php if ($pageReloaded) echo $_POST['description']; else echo $rowObject->description?></textarea>								
							</div>
							<div class="checkbox">
								<div class="row">
									<label for="published"><?php echo JText::_("CATALOG_OBJECT_PUBLISHED_IN_CATALOG"); ?> : </label>
									<?php echo helper_easysdi::booleanlist('published', 'class="checkbox"', 'class="checkbox"', ($pageReloaded)? $_POST['published'] : $rowObject->published, 'CORE_YES', 'CORE_NO'); ?>
								</div>
							</div>
							<div class="row">							
								<label for="visibility_id"><?php echo JText::_("CATALOG_OBJECT_VISIBILITY_LABEL"); ?> : </label>
								<?php echo JHTML::_("select.genericlist",$visibilities, 'visibility_id', 'size="1" class="inputbox checkbox full"', 'value', 'text', ($pageReloaded)? $_POST['visibility_id'] : $rowObject->visibility_id  ); ?>								
							</div>
							<div class="row">
								<fieldset>
									<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
									<table class="admintable" border="0" cellpadding="0" cellspacing="0">
			<?php
			foreach ($languages as $lang)
			{ 
			?>
								<tr>
								<td class="key"><?php echo JText::_("CORE_".strtoupper($lang->code)); ?> : </td>
								<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded) echo $_POST['label_'.$lang->code]; else echo $labels[$lang->id]?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
								</tr>
			<?php
			}
			?>
										</table>
									</fieldset>
							</div>
							<div class="row">							
								<label for="objecttype_id"><?php echo JText::_("CORE_OBJECT_TYPE_LABEL"); ?> : </label>
								<?php echo JHTML::_("select.genericlist",$objecttypes, 'objecttype_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObject\');"', 'value', 'text', ($pageReloaded)? $_POST['objecttype_id'] : $rowObject->objecttype_id  ); ?>								
							</div>
							<div class="row">	
								<fieldset>
									<legend><?php echo JText::_( 'CORE_MANAGER_NAME'); ?></legend>
									<table>
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
													<td><input type="button" value=" << " id="removeAllmanagers"
														onclick="javascript:TransfertAll('selected_managers','managers');"></td>
												</tr>
												<tr>
													<td><input type="button" value=" < " id="removemanager"
														onclick="javascript:Transfert('selected_managers', 'managers');"></td>
												</tr>
												<tr>
													<td><input type="button" value=" > " id ="addmanager"
														onclick="javascript:Transfert('managers','selected_managers');"></td>
												</tr>
												<tr>
													<td><input type="button" value=" >> " id="addAllmanagers"
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
						</div>	
						<div class="row">
							<fieldset>
								<legend><?php echo JText::_( 'CORE_EDITOR_NAME'); ?></legend>
								<table>
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
												<td><input type="button" value=" << " id="removeAlleditors"
													onclick="javascript:TransfertAll('selected_editors','editors');"></td>
											</tr>
											<tr>
												<td><input type="button" value=" < " id="removeeditor"
													onclick="javascript:Transfert('selected_editors', 'editors');"></td>
											</tr>
											<tr>
												<td><input type="button" value=" > " id ="addeditor"
													onclick="javascript:Transfert('editors','selected_editors');"></td>
											</tr>
											<tr>
												<td><input type="button" value=" >> " id="addAlleditors"
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
					</div>	
<?php
if ($rowObject->id == 0 and $hasVersioning) {
?>
			<div class="row">
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
			</div>	
<?php 
}?>
						</div>
						<div class="row">
<?php
$user =& JFactory::getUser();
if ($rowObject->created)
{ 
?>
							<div class="row">
								<label><?php echo JText::_("CORE_CREATED"); ?> : </label>
								<?php if ($rowObject->created) {echo date('d.m.Y h:i:s',strtotime($rowObject->created));} ?>
								, 
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
								<?php echo $createUser; ?>
							</div>
<?php
}
if ($rowObject->updated)
{ 
?>
							<div class="row">
								<label><?php echo JText::_("CORE_UPDATED"); ?> : </label>
								<?php if ($rowObject->updated and $rowObject->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($rowObject->updated));} ?>
								, 
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
								<?php echo $updateUser; ?>
							</div>
<?php
}
?>
					</div>
			</div>
		</div>
		<input type="hidden" name="cid[]" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $rowObject->guid; ?>" />
		<input type="hidden" name="ordering" value="<?php echo $rowObject->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo ($rowObject->created)? $rowObject->created : date ('Y-m-d H:i:s');?>" />
		<input type="hidden" name="createdby" value="<?php echo ($rowObject->createdby)? $rowObject->createdby : $user->id; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo ($rowObject->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo ($rowObject->createdby)? $user->id : ''; ?>" /> 
		<input type="hidden" name="metadata_guid" value="<?php if ($pageReloaded) echo $_POST['metadata_guid']; else echo $rowMetadata->guid; ?>" />
		<input type="hidden" name="account_id" value="<?php echo $root_account; ?>" />
			
		<input type="hidden" name="id" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
		<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
		</form>
		</div>
		</div>
	<?php
	}
	
	function listObject($pageNav,$rows,$option,$rootAccount, $listObjectType, $filter_objecttype_id, $search, $lists)
	{
		$database =& JFactory::getDBO(); 
		$user	=& JFactory::getUser();
		
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		?>
		<script>
		function tableOrdering( order, dir, view )
		{
			var form = document.getElementById("objectListForm");
			
			form.filter_order.value 	= order;
			form.filter_order_Dir.value	= dir;
			form.submit( view );
		}
					
		</script>
		<div id="page">
		<h1 class="contentheading"><?php echo JText::_("CATALOG_FE_LIST_OBJECT"); ?></h1>
		<div class="contentin">
		<h2> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h2>
		<form action="index.php" method="POST" id="objectListForm" name="objectListForm">
	
		<div class="row">
			 <div class="row">
			 	<label for="searchObjectName"><?php echo JText::_("CATALOG_OBJECT_FILTER_OBJECTNAME");?></label>
			 	<input type="text" name="searchObjectName" value="<?php echo $search;?>" class="inputboxSearchProduct text full" />
			 </div>
			 <div class="row">
			 	<label for="searchObjectType"><?php echo JText::_("CATALOG_OBJECT_FILTER_OBJECTTYPE");?></label>
			 	<?php echo JHTML::_('select.genericlist',  $listObjectType, 'filter_objecttype_id', 'class="inputbox full" size="1"', 'value', 'text', $filter_objecttype_id); ?>
			 </div>
			 <div class="row">
				<input type="submit" id="simple_search_button" name="simple_search_button" class="submit" value ="<?php echo JText::_("CORE_SEARCH_BUTTON"); ?>" onClick="document.getElementById('objectListForm').task.value='listObject';document.getElementById('objectListForm').submit();"/>
				<input type="submit" id="newobject_button" name="newobject_button" class="submit" value ="<?php echo JText::_("CATALOG_NEW_OBJECT"); ?>" onClick="document.getElementById('objectListForm').task.value='newObject';document.getElementById('objectListForm').submit();"/>
			</div>	 
		 </div>
	<script>
		function suppressObject_click(url){
			conf = confirm('<?php echo JText::_("CATALOG_CONFIRM_OBJECT_DELETE"); ?>');
			if(!conf)
				return false;
			window.open(url, '_self');
		}
	</script>
	<div class="ticker">
	<h2><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h2>
	<?php
	if(count($rows) == 0){
		echo "<p><strong>".JText::_("CATALOG_OBJECT_NORESULTFOUND")."</strong>&nbsp;0&nbsp;</p>";
		
	}else{?>
	<table id="myObjects" class="box-table" width="100%">
	<thead>
	<tr>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECT_OBJECTTYPE"), 'objecttype', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JText::_('CORE_METADATA_MANAGERS'); ?></th>
	<th class='title'><?php echo JText::_('CORE_METADATA_EDITORS'); ?></th>
	<th class='title'><?php echo JText::_('CATALOG_METADATA_ACTIONS'); ?></th>
	</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<td ><?php echo $row->name ;?></td>
			<td ><?php echo $row->objecttype ;?></td>
			<?php 		
			$managers = "";
			$database->setQuery( "SELECT b.name FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
			$managers = implode(", ", $database->loadResultArray());
			
			$editors = "";
			$database->setQuery( "SELECT b.name FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
			$editors = implode(", ", $database->loadResultArray());
			?>
			<td ><?php echo $managers; ?></td>
			<td ><?php echo $editors; ?></td>
			<td class="objectActions">
			<?php 
			if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
			{
				?>
				<div class="logo" id="emptyPicto"></div>
				<div class="logo" id="emptyPicto"></div>
				<div class="logo" id="emptyPicto"></div>
				<div class="logo" id="emptyPicto"></div>
				<?php 
			} 
			else 
			{
				?>
				<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECT_MANAGEAPPLICATION')); ?>" id="listApplication" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listApplication&object_id='.$row->id)); ?>', '_self');"></div>
				<?php 
				$objecttype = new objecttype($database);
				$objecttype->load($row->objecttype_id);
				
				if ($objecttype->hasVersioning)
				{
					?>
					<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_OBJECT_MANAGEVERSION')); ?>" id="listObjectVersion" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=listObjectVersion&object_id='.$row->id)); ?>', '_self');"></div>
					<?php
				}
				else
				{
					?>
					<div class="logo" id="emptyPicto"></div>
					<?php 
				}
				?>
				<div class="logo" title="<?php echo addslashes(JText::_('CORE_EDIT_OBJECT')); ?>" id="editObject" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=editObject&cid[]='.$row->id)); ?>', '_self');"></div>
				<div class="logo" title="<?php echo addslashes(JText::_('CORE_DELETE_OBJECT')); ?>" id="deleteObject" onClick="return suppressObject_click('<?php echo JRoute::_(displayManager::buildUrl('index.php?option='.$option.'&task=deleteObject&cid[]='.$row->id)); ?>');" ></div>
			<?php 
			}
			?>
			</td>
			</tr>
			<?php		
		}
		
	?>
	</tbody>
	</table>
	
	</div>
	<div class="paging">
    	<h3 class="hidden"><?php JText::_('WEITERE TREFFER ANZEIGEN'); ?></h3>
    	<p class="info"><?php echo $pageNav->getPagesCounter(); ?></p>
		<p class="select"><?php echo $pageNav->getPagesLinks( ); ?></p>
  	</div>
  	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task" name="task" value="listObject">
			<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
			<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			<input type="hidden" id="backpage" name="backpage" value="object">
			<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		</div>
		</div>
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