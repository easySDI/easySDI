<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ¬"Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
		/*$database->setQuery( "SELECT id AS value,  alias AS text FROM #__CORE_basemap_definition " );
		$baseMaplist = $database->loadObjectList() ;
		
		if ($database->getErrorNum()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			}	
		*/
		jimport("joomla.utilities.date");
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_catalog'.DS.'js'.DS.'catalog.js.php');
		/*require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_CORE_core'.DS.'common'.DS.'easysdi.config.php');
		
		
		$catalogUrlBase = config_easysdi::getValue("catalog_url");				
		$catalogUrlGetRecordById = $catalogUrlBase."?request=GetRecordById&service=CSW&version=2.0.1&elementSetName=full&id=".$rowObject->metadata_id;

			
		$cswResults = DOMDocument::load($catalogUrlGetRecordById);
 
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_CORE_core'.DS.'core'.DS.'geoMetadata.php');
		
		//$geoMD = new geoMetadata($cswResults ->getElementsByTagNameNS  ( "http://www.isotc211.org/2005/gmd" , "MD_Metadata"  )->item(0));
		$geoMD = new geoMetadata($cswResults);				 
		*/
		$database =& JFactory::getDBO(); 
		
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
		    <table width="100%">
				<tr>
					<td width="100%" align="right">
						<button type="button" onClick="document.getElementById('adminForm').task.value='saveObject'; Pre_Post('adminForm', 'selected_managers', 'manager'); Pre_Post('adminForm', 'selected_editors', 'editor'); document.getElementById('adminForm').submit();" ><?php echo JText::_("CORE_SAVE"); ?></button>		
						<br></br>
					</td>
					<td width="100%" align="right">
						<button type="button" onClick="document.getElementById('adminForm').task.value='cancelObject';document.getElementById('adminForm').submit();" ><?php echo JText::_("CORE_CANCEL"); ?></button>
						<br></br>
					</td>
				</tr>
		   </table>
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
								<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded) echo $_POST['label_'.$lang->code]; else echo $labels[$lang->id]?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
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
							<!-- <tr>							
								<td><?php //echo JText::_("CORE_OBJECT_SUPPLIERNAME"); ?> : </td>
								<td><?php //echo JHTML::_("select.genericlist",$accounts, 'account_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObject\');"', 'value', 'text', ($pageReloaded)? $_POST['account_id'] : $rowObject->account_id ); ?></td>								
							</tr> -->
					<tr>	
						<td colspan=2>
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
		<input type="hidden" name="account_id" value="<?php echo $root_account; ?>" />
		
		<input type="hidden" name="id" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		</div>
		</div>
	<?php
	}
	
	function listObject($pageNav,$rows,$option,$rootAccount, $listObjectType, $search, $lists)
	{
		$database =& JFactory::getDBO(); 
		$user	=& JFactory::getUser();
		
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("CATALOG_FE_LIST_OBJECT"); ?></h2>
		<div class="contentin">
		<h3> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h3>
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("CORE_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
			</tr>
			<tr>
				<td align="left">
					<input type="text" name="searchProduct" value="<?php echo $search;?>" class="inputboxSearchProduct" " />			
				</td>
			</tr>
			<tr>
				<td align="left">
					<br/>
					<?php echo $listObjectType; ?>
				</td>
			</tr>
			<tr>
				<td align="right">
					<button type="submit" class="searchButton" onClick="document.getElementById('task').value='listObject';document.getElementById('productListForm').submit();"> <?php echo JText::_("CORE_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<button type="button" onClick="document.getElementById('task').value='newObject';document.getElementById('productListForm').submit();" ><?php echo JText::_("CATALOG_NEW_OBJECT"); ?></button>
				</td>
			</tr>
		</table>
		<br/>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("CORE_SHOP_DISPLAY"); ?> <?php echo $pageNav->getLimitBox(); ?></td>
				<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h3>
	<script>
		function suppressObject_click(id){
			conf = confirm('<?php echo JText::_("CATALOG_CONFIRM_OBJECT_DELETE"); ?>');
			if(!conf)
				return false;
			window.open('./index.php?option=com_easysdi_catalog&task=deleteObject&cid[]='+id, '_self');
		}
		function archiveObject_click(id){
			conf = confirm('<?php echo JText::_("CATALOG_CONFIRM_OBJECT_ARCHIVE"); ?>');
			if(!conf)
				return false;
			window.open('./index.php?option=com_easysdi_catalog&task=archiveObject&cid[]='+id, '_self');
		}
		function viewObjectLink_click(id){
			window.open('./index.php?option=com_easysdi_catalog&task=viewObjectLink&backpage=object&cid[]='+id, '_self');
		}
		function manageObjectLink_click(id){
			window.open('./index.php?option=com_easysdi_catalog&task=manageObjectLink&cid[]='+id, '_self');
		}
		function newObjectVersion_click(id) {
			window.open('./index.php?option=com_easysdi_catalog&task=versionaliseObject&cid[]='+id, '_self');
		}
	</script>
	<table id="myProducts" class="box-table" width="100%">
	<thead>
	<tr>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', $lists['order_Dir'], $lists['order']); ?></th>
	<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECT_OBJECTTYPE"), 'objecttype', $lists['order_Dir'], $lists['order']); ?></th>
	<!-- <th><?php //echo JText::_('CORE_METADATA_STATE'); ?></th> -->
	<!--<th><?php //echo JText::_('CATALOG_OBJECT_VERSION_COL'); ?></th>-->
	<th><?php echo JText::_('CORE_METADATA_MANAGERS'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_EDITORS'); ?></th>
	<!-- <th><?php //echo JText::_('CORE_UPDATED'); ?></th> -->
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
	<!-- <th class="logo">&nbsp;</th> -->
	<!-- <th class="logo">&nbsp;</th> -->
	<!-- <th class="logo">&nbsp;</th> -->
	</tr>
	</thead>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
			<!-- <td ><a class="modal" title="<?php //echo JText::_("CATALOG_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=<?php //echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php //echo $row->name ;?></a></td> -->
			<td ><?php echo $row->name ;?></td>
			<td ><?php echo $row->objecttype ;?></td>
			<!-- <td ><?php //echo JText::_($row->state); ?></td> -->
			<?php 		
			/*$versions = "";
			$database->setQuery( "SELECT name FROM #__sdi_objectversion WHERE object_id=".$row->id." ORDER BY created" );
			$versions = implode(", ", $database->loadResultArray());*/
			
			$managers = "";
			$database->setQuery( "SELECT b.name FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
			$managers = implode(", ", $database->loadResultArray());
			
			$editors = "";
			$database->setQuery( "SELECT b.name FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
			$editors = implode(", ", $database->loadResultArray());
			?>
			<!-- <td ><?php //echo $versions; ?></td> -->
			<td ><?php echo $managers; ?></td>
			<td ><?php echo $editors; ?></td>
			<!-- <td ><?php //if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td> -->
			<!-- <td class="logo"><a id="editProduct"  href="./index.php?option=com_easysdi_catalog&task=editObject&cid[]=<?php echo $row->id;?>">Editer</a></td> -->
			<?php 
			if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
			{
				?>
				<td></td>
				<td></td>
				<td></td>
				<!-- <td></td> -->
				<?php 
			} 
			else 
			{
				$objecttype = new objecttype($database);
				$objecttype->load($row->objecttype_id);
				
				if ($objecttype->hasVersioning)
				{
					?>
					<td class="logo"><div title="<?php echo helper_easysdi::formatDivTitle(JText::_('CATALOG_OBJECT_MANAGEVERSION')); ?>" id="listObjectVersion" onClick="window.open('./index.php?option=com_easysdi_catalog&task=listObjectVersion&object_id=<?php echo $row->id;?>', '_self');"></div></td>
					<?php
				}
				else
				{
					?>
					<td></td>
					<?php 
				}
				?>
				<td class="logo"><div title="<?php echo helper_easysdi::formatDivTitle(JText::_('CORE_EDIT_OBJECT')); ?>" id="editObject" onClick="window.open('./index.php?option=com_easysdi_catalog&task=editObject&cid[]=<?php echo $row->id;?>', '_self');"></div></td>
				<?php
				//if ($row->metadatastate_id == 2 or $row->metadatastate_id == 4) // Impossible de supprimer si le statut n'est pas "ARCHIVED" ou "UNPUBLISHED"
				//{
				?> 
				<td class="logo"><div title="<?php echo helper_easysdi::formatDivTitle(JText::_('CORE_DELETE_OBJECT')); ?>" id="deleteObject" onClick="return suppressObject_click('<?php echo $row->id; ?>');" ></div></td>
				<?php 
				/*}
				else {
				?>
				<td></td>
				<?php 
				}*/
				?>
				<?php
				//if ($row->metadatastate_id == 1) // Impossible d'archiver si le statut n'est pas "PUBLISHED"
				//{
				?> 
				<!-- <td class="logo"><div title="<?php //echo JText::_('CORE_ARCHIVE_OBJECT'); ?>" id="archiveObject" onClick="return archiveObject_click('<?php //echo $row->id; ?>');" ></div></td> -->
				<?php 
				//}
				//else {
				?>
				<!-- <td></td> -->
				<?php 
				//}
				?>
				<!-- <td class="logo"><div title="<?php //echo JText::_('CORE_MANAGELINK_OBJECT'); ?>" id="manageObjectLink" onClick="return manageObjectLink_click('<?php //echo $row->id; ?>');" ></div></td> -->
				<?php
			}
			?>
			<!-- <td class="logo"><div title="<?php //echo JText::_('CORE_VIEWLINK_OBJECT'); ?>" id="viewObjectLink" onClick="return viewObjectLink_click('<?php //echo $row->id; ?>');" ></div></td> -->
			<!-- <td class="logo"><div title="<?php //echo JText::_('CORE_NEWVERSION_OBJECT'); ?>" id="newObjectVersion" onClick="return newObjectVersion_click('<?php //echo $row->id; ?>');" ></div></td> -->
			
			</tr>
			<?php		
		}
		
	?>
	</tbody>
	</table>
	<br/>
	<table width="100%">
		<tr>																																						
			<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
			<td align="center">&nbsp;</td>
			<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task" name="task" value="listObject">
			<input type="hidden" id="backpage" name="backpage" value="object">
			<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
			<?php if (userManager::hasRight($rootAccount->id,"INTERNAL")){?> 
			<?php }  ?>
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