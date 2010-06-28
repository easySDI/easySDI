<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

	function editObject($rowObject, $rowMetadata, $id, $accounts, $objecttypes, $projections, $fieldsLength, $languages, $labels, $editors, $selected_editors, $managers, $selected_managers, $visibilities, $pageReloaded, $option ){
		
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
		$tabs =& JPANE::getInstance('Tabs');
		
		?>				
	<form action="index.php" method="POST" name="adminForm" id="adminForm" class="adminForm" onsubmit="Pre_Post('adminForm', 'selected_managers', 'manager'); Pre_Post('adminForm', 'selected_editors', 'editor');document.getElementById('adminForm').submit;">
<?php
		echo $tabs->startPane("objectPane");
		echo $tabs->startPanel(JText::_("CORE_OBJECT_TAB_TITLE_GENERAL"),"objectPane");

		?>		
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<!-- <legend><?php //echo JText::_("CORE_GENERAL"); ?></legend> -->
						<table border="0" cellpadding="3" cellspacing="0">
							<tr>
								<td width=150><?php echo JText::_("CORE_OBJECT_METADATAID_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['metadata_guid'];?>" name="metadata_guid" value="<?php echo $rowMetadata->guid; ?>" disabled="disabled" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_NAME"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php if ($pageReloaded) echo $_POST['name']; else echo $rowObject->name; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
								<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php if ($pageReloaded) echo $_POST['description']; else echo $rowObject->description?></textarea></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
								<td><?php echo JHTML::_('select.booleanlist', 'published', '', ($pageReloaded)? $_POST['published'] : $rowObject->published); ?> </td>																
							</tr>
							<tr>							
								<td><?php echo JText::_("CORE_METADATA_VISIBILITY_LABEL"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$visibilities, 'visibility_id', 'size="1" class="inputbox"', 'value', 'text', ($pageReloaded)? $_POST['visibility_id'] : $rowMetadata->visibility_id  ); ?></td>								
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
								<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?> : </td>
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
								<td><?php echo JText::_("CORE_OBJECT_TYPE_LABEL"); ?> : </td>
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
					</fieldset>
				</td>
			</tr>
			
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CORE_OBJECT_TAB_TITLE_PREVIEW"),"objectPane");
		?>
		<br>
		<table width="100%" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>
						<!-- <legend><?php //echo JText::_("CORE_PREVIEW"); ?></legend> -->
						<table border="0" cellpadding="3" cellspacing="0">						
							<tr>
								<td width=150><?php echo JText::_("CORE_OBJECT_PREVIEW_PROJECTION_LABEL"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$projections, 'projection_id', 'size="1" class="inputbox" ', 'value', 'text', $rowObject->projection_id  ); ?></td>
							</tr>
							<tr>							
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_MINRESOLUTION_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="view_minResolution" value="<?php echo $rowObject->view_minResolution; ?>" /></td>							
							</tr>
							<tr>							
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_MAXRESOLUTION_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="view_maxResolution" value="<?php echo $rowObject->view_maxResolution; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_MAXEXTENT_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="view_maxExtent" value="<?php echo $rowObject->view_maxExtent; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_DECIMALPRECISION_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="50" name="view_decimalPrecisionDisplayed" value="<?php echo $rowObject->view_decimalPrecisionDisplayed; ?>" /></td>							
							</tr>
							<tr>
							
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_MAXEXTENDISRESTRICTIVE_LABEL"); ?> : </td>
								<td><?php echo JHTML::_('select.booleanlist', 'view_restrictedExtend', '', $rowObject->view_restrictedExtend);?> </td>															
							</tr>	
							<tr>
							
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_RESTRICTEDSCALES_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="view_restrictedScales" value="<?php echo $rowObject->view_restrictedScales; ?>" /></td>							
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_URL_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['view_url'];?>" name="view_url" value="<?php echo $rowObject->view_url; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_LAYERS_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['view_layers'];?>" name="view_layers" value="<?php echo $rowObject->view_layers; ?>" /></td>								
							</tr>							
							<tr>
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_IMAGEFORMAT_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="100" name="view_ImageFormat" value="<?php echo $rowObject->view_ImageFormat; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_USER_LABEL"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['view_user'];?>" id="view_user" name="view_user" value="<?php echo $rowObject->view_user; ?>" /></td>								
							</tr>
							<tr>
								<td><?php echo JText::_("CORE_OBJECT_PREVIEW_PASSWORD_LABEL"); ?> : </td>
								<td><input class="inputbox" type="password" size="50" maxlength="<?php echo $fieldsLength['view_password'];?>" id="view_password" name="view_password" value="<?php echo $rowObject->view_password; ?>" /></td>								
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
		<input type="hidden" name="cid[]" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="guid" value="<?php echo $rowObject->guid; ?>" />
		<input type="hidden" name="ordering" value="<?php echo $rowObject->ordering; ?>" />
		<input type="hidden" name="created" value="<?php echo ($rowObject->created)? $rowObject->created : date ('Y-m-d H:i:s');?>" />
		<input type="hidden" name="createdby" value="<?php echo ($rowObject->createdby)? $rowObject->createdby : $user->id; ?>" /> 
		<input type="hidden" name="updated" value="<?php echo ($rowObject->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
		<input type="hidden" name="updatedby" value="<?php echo ($rowObject->createdby)? $user->id : ''; ?>" /> 
		<input type="hidden" name="metadata_guid" value="<?php echo $rowMetadata->guid; ?>" />
		
		<input type="hidden" name="id" value="<?php echo $rowObject->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
		</form>
		<button type="button" onClick="document.getElementById('adminForm').task.value='saveObject'; Pre_Post('adminForm', 'selected_managers', 'manager'); Pre_Post('adminForm', 'selected_editors', 'editor'); document.getElementById('adminForm').submit();" ><?php echo JText::_("CORE_SAVE"); ?></button>		
		<button type="button" onClick="document.getElementById('adminForm').task.value='cancelObject';document.getElementById('adminForm').submit();" ><?php echo JText::_("CORE_CANCEL"); ?></button>
		
	<?php
	}
	
	function listObject($pageNav,$rows,$option,$rootAccount,$search)
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
				<td align="left">
					<input type="text" name="searchProduct" value="<?php echo $search;?>" class="inputboxSearchProduct" " />			
				</td>
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
	<table id="myProducts" class="box-table">
	<thead>
	<tr>
	<th class="logo2"></th>
	<th><?php echo JText::_('CORE_NAME'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_STATE'); ?></th>
	<th><?php echo JText::_('CATALOG_OBJECT_VERSION_COL'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_MANAGERS'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_EDITORS'); ?></th>
	<th><?php echo JText::_('CORE_UPDATED'); ?></th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
	<th class="logo">&nbsp;</th>
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
			<td class="logo2"><div <?php if($row->visibility_id==1 && $row->orderable == 1) echo 'title="'.JText::_("CORE_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else if($row->visibility_id==2 && $row->orderable == 1) echo 'title="'.JText::_("CORE_INFOLOGO_ORDERABLE_INTERNAL").'" class="easysdi_product_exists_internal"';?>></div></td>
			<td ><a class="modal" title="<?php echo JText::_("CATALOG_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->name ;?></a></td>
			<td ><?php echo JText::_($row->state); ?></td>
			<?php 		
			$versions = "";
			$database->setQuery( "SELECT name FROM #__sdi_objectversion WHERE object_id=".$row->id." ORDER BY created" );
			$versions = implode(", ", $database->loadResultArray());
			
			$managers = "";
			$database->setQuery( "SELECT b.name FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
			$managers = implode(", ", $database->loadResultArray());
			
			$editors = "";
			$database->setQuery( "SELECT b.name FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
			$editors = implode(", ", $database->loadResultArray());
			?>
			<td ><?php echo $versions; ?></td>
			<td ><?php echo $managers; ?></td>
			<td ><?php echo $editors; ?></td>
			<td ><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			<!-- <td class="logo"><a id="editProduct"  href="./index.php?option=com_easysdi_catalog&task=editObject&cid[]=<?php echo $row->id;?>">Editer</a></td> -->
			<?php 
			if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
			{
				?>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<?php 
			} 
			else 
			{
				?>
				<td class="logo"><div title="<?php echo JText::_('CORE_EDIT_OBJECT'); ?>" id="editObject" onClick="window.open('./index.php?option=com_easysdi_catalog&task=editObject&cid[]=<?php echo $row->id;?>', '_self');"></div></td>
				<?php
				if ($row->metadatastate_id == 2) // Impossible de supprimer si le statut n'est pas "ARCHIVED"
				{
				?> 
				<td class="logo"><div title="<?php echo JText::_('CORE_DELETE_OBJECT'); ?>" id="deleteObject" onClick="return suppressObject_click('<?php echo $row->id; ?>');" ></div></td>
				<?php 
				}
				else {
				?>
				<td></td>
				<?php 
				}
				?>
				<?php
				if ($row->metadatastate_id == 1) // Impossible d'archiver si le statut n'est pas "PUBLISHED"
				{
				?> 
				<td class="logo"><div title="<?php echo JText::_('CORE_ARCHIVE_OBJECT'); ?>" id="archiveObject" onClick="return archiveObject_click('<?php echo $row->id; ?>');" ></div></td>
				<?php 
				}
				else {
				?>
				<td></td>
				<?php 
				}
				?>
				<td class="logo"><div title="<?php echo JText::_('CORE_MANAGELINK_OBJECT'); ?>" id="manageObjectLink" onClick="return manageObjectLink_click('<?php echo $row->id; ?>');" ></div></td>
				<?php
			}
			?>
			<td class="logo"><div title="<?php echo JText::_('CORE_VIEWLINK_OBJECT'); ?>" id="viewObjectLink" onClick="return viewObjectLink_click('<?php echo $row->id; ?>');" ></div></td>
			<td class="logo"><div title="<?php echo JText::_('CORE_NEWVERSION_OBJECT'); ?>" id="newObjectVersion" onClick="return newObjectVersion_click('<?php echo $row->id; ?>');" ></div></td>
			
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
			<?php if (userManager::hasRight($rootAccount->id,"INTERNAL")){?> 
			<?php }  ?>
		</form>
		</div>
		</div>
	<?php
			
	}	

	function viewObjectLink($parent_objectlinks, $child_objectlinks, $object_id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO();
		JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');

		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'administrator/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		
		$javascript = "";
	
		$backPage = JRequest::getVar('backpage');
		
		$object = new object($database);
		$object->load($object_id);
		$object_name = "\"".$object->name."\"";
		?>
			<div id="page">
		    <h2 class="contentheading"><?php echo JText::_("CATALOG_VIEW_OBJECTLINK")." ".$object_name ?></h2>
		    <div id="contentin" class="contentin">
		    <table width="100%">
		    	<tr>
					<td width="100%" align="right">
					<?php if ($backPage == 'object'){?>
						<button type="button" onClick="window.open ('./index.php?tmpl=component&option=com_easysdi_catalog&task=cancelObject&object_id=<?php echo $object_id;?>','_parent');"><?php echo JText::_("CORE_CANCEL"); ?></button>
					<?php }else if ($backPage == 'metadata'){?>
						<button type="button" onClick="window.open ('./index.php?tmpl=component&option=com_easysdi_catalog&task=cancelMetadata&object_id=<?php echo $object_id;?>','_parent');"><?php echo JText::_("CORE_CANCEL"); ?></button>
					<?php }?>
						<br></br>
					</td>
				</tr>
				<tr>
					<td width="100%"><div id="editMdOutput"></div></td>
				</tr>
		   </table>
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
			</div></div>
		<?php
		
		$javascript .="
			//var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			//Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			var domNode = Ext.DomQuery.selectNode('div#editMdOutput')
			Ext.DomHelper.insertHtml('afterBegin',domNode,'<div id=formContainer></div>');
	
			
			// Column Model shortcut array
			var cols = [
				{ id : 'value', hidden: true, dataIndex: 'value'},
				{ id : 'name', header: '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTLINK_GRID_NAME_HEADER"))."', sortable: true, dataIndex: 'name'}
			];
			
			var parentGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($parent_objectlinks), "links"=>$parent_objectlinks)).",
				root   : 'links'
		    });
		    
			// declare the source Grid
		    var parentGrid = new Ext.grid.GridPanel({
				store            : parentGridStore,
		        columns          : cols,
				stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTLINK_PARENTGRID_TITLE"))."'
		    });
		
		    var childGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($child_objectlinks), "links"=>$child_objectlinks)).",
				root   : 'links'
		    });
		
		    // create the destination Grid
		    var childGrid = new Ext.grid.GridPanel({
				store            : childGridStore,
		        columns          : cols,
				stripeRows       : true,
		        autoExpandColumn : 'name',
		        title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTLINK_CHILDGRID_TITLE"))."'
		    });
		    
			// Créer le formulaire qui va contenir la structure
			var form = new Ext.form.FormPanel(
				{
					id:'linksForm',
					url: 'index.php',
					border:false,
			        collapsed:false,
			        renderTo: document.getElementById('formContainer'),
			        items        : [
			        	{
			        		xtype		 : 'panel',
							width        : 650,
							height       : 300,
							layout       : 'hbox',
							defaults     : { flex : 1 }, //auto stretch
							layoutConfig : { align : 'stretch' },
							items        : [
								parentGrid,
								childGrid
							]
						}
					]
			    }
			);
	        
			// Affichage du formulaire
    		form.doLayout();
    	";
					
		print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
	}
	
	function manageObjectLink($objectlinks, $selected_objectlinks, $listObjecttypes, $listStatus, $listManagers, $listEditors, $object_id, $option)
	{
		global $mainframe;
		$database=& JFactory::getDBO();
		JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');
		JHTML::script('Components_extjs.js', 'administrator/components/com_easysdi_catalog/js/');
		
		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'administrator/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		
		$javascript = "";
	
		$object = new object($database);
		$object->load($object_id);
		$object_name = "\"".$object->name."\"";
		?>
			<div id="page">
		    <h2 class="contentheading"><?php echo JText::_("CATALOG_MANAGE_OBJECTLINK")." ".$object_name ?></h2>
		    <div id="contentin" class="contentin">
		    <table width="100%">
				<tr>
					<td width="100%" align="right">
						<button type="button" onClick="window.open ('./index.php?tmpl=component&option=com_easysdi_catalog&task=cancelObjectLink&object_id=<?php echo $object_id;?>','_parent');"><?php echo JText::_("CORE_CANCEL"); ?></button>
						<br></br>
					</td>
				</tr>
				<tr>
					<td width="100%"><div id="editMdOutput"></div></td>
				</tr>
		   </table>
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
			</div></div>
		<?php
		
		$javascript .="
			//var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			//Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			var domNode = Ext.DomQuery.selectNode('div#editMdOutput')
			Ext.DomHelper.insertHtml('afterBegin',domNode,'<div id=formContainer></div>');
	
			// Column Model shortcut array
			var cols = [
				{ id : 'value', hidden: true, dataIndex: 'value', menuDisabled: true},
				{ id : 'name', header: '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTLINK_GRID_NAME_HEADER"))."', sortable: true, dataIndex: 'name', menuDisabled: true}
			];
			
			var unselectedGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($objectlinks), "links"=>$objectlinks)).",
				root   : 'links'
		    });
		    
			// declare the source Grid
		    var unselectedGrid = new Ext.grid.GridPanel({
		    	id				 : 'unselected',
				ddGroup          : 'selectedGridDDGroup',
		        ds				 : getObjectList(),
				columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        flex			 : 5,
		        loadMask		 : true,
		        frame			 : false,
				title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTLINK_UNSELECTEDGRID_TITLE"))."',
		        viewConfig: {
							 	forceFit: true,
								scrollOffset:0
							 }
		    });
			    
		    var selectedGridStore = new Ext.data.JsonStore({
		        fields : [{name: 'value', mapping : 'value'}, {name: 'name', mapping : 'name'}],
		        data   : ".HTML_metadata::array2json(array ("total"=>count($selected_objectlinks), "links"=>$selected_objectlinks)).",
				root   : 'links'
		    });
		
		    // create the destination Grid
		    var selectedGrid = new Ext.grid.GridPanel({
				id				 : 'selected',
				ddGroup          : 'unselectedGridDDGroup',
		        store            : selectedGridStore,
		        columns          : cols,
				enableDragDrop   : true,
		        stripeRows       : true,
		        autoExpandColumn : 'name',
		        flex			 : 5,
		        loadMask		 : true,
		        frame			 : false,
				title            : '".html_Metadata::cleanText(JText::_("CATALOG_OBJECTLINK_SELECTEDGRID_TITLE"))."'
		    });
		    
			var htmlButtons = new Ext.Panel({
				id				 : 'htmlButtons',
				frame			 : false,
				border			 : false,
				layout      	 : 'vbox',
				flex			 : 1,
				layoutConfig	 : { align : 'center', pack:'center'},
				defaults		 : {margins:'0 0 5 0'},
                items			 : [
									{
										xtype: 'button',
										text: '<<',
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								
											selected.store.removeAll();	
			 								unselected.store.reload({                    
			 									params: 
			 									{ 
				 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
				 									id:Ext.getCmp('id').getValue(),
				 									name:Ext.getCmp('name').getValue(),
				 									status:Ext.getCmp('status').getValue(),
				 									manager:Ext.getCmp('manager').getValue(),
				 									editor:Ext.getCmp('editor').getValue(),
				 									fromDate:Ext.getCmp('fromDate').getValue(),
				 									toDate:Ext.getCmp('toDate').getValue(),
				 									selectedObjects: ''
												}                
											});	
						                }
									},
									{
										xtype: 'button',
										text: '<',
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								
						                	var records = selected.selModel.getSelections();
			 								Ext.each(records, selected.store.remove, selected.store);
	                        				
			 								var selectedValues = new Array();
			 								var grid = Ext.getCmp('selected').store.data;
			 								for (var i = 0 ; i < grid.length ;i++) 
			 								{
			 									selectedValues.push(grid.get(i).get('value'));
											}
											
			 								unselected.store.reload({                    
			 									params: 
			 									{ 
				 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
				 									id:Ext.getCmp('id').getValue(),
				 									name:Ext.getCmp('name').getValue(),
				 									status:Ext.getCmp('status').getValue(),
				 									manager:Ext.getCmp('manager').getValue(),
				 									editor:Ext.getCmp('editor').getValue(),
				 									fromDate:Ext.getCmp('fromDate').getValue(),
				 									toDate:Ext.getCmp('toDate').getValue(),
				 									selectedObjects: selectedValues.join(', ')
												}                
											});	
						                }
									},
									{
										xtype: 'button',
										text: '>',
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								var records = unselected.selModel.getSelections();
			 								Ext.each(records, unselected.store.remove, unselected.store);
	                        				selected.store.add(records);
                        					selected.store.sort('name', 'ASC');
						                }
									},
									{
										xtype: 'button',
										text: '>>',
										handler: function()
						                {
						                	var unselected = Ext.getCmp('unselected');
						                	var selected = Ext.getCmp('selected');                
			 								var records = unselected.store.getRange();
			 								Ext.each(records, unselected.store.remove, unselected.store);
	                        				selected.store.add(records);
                        					selected.store.sort('name', 'ASC');
						                }
									}
								   ]
		    });
		    
		    var objecttype = new Array();
		    objecttype['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_OBJECTTYPE_LABEL'))."';
			objecttype['list'] = $listObjecttypes;
			
			var id = new Array();
		    id['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_ID_LABEL'))."';
			
			var name = new Array();
		    name['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_NAME_LABEL'))."';
			
		    var status = new Array();
		    status['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_STATUS_LABEL'))."';
			status['list'] = $listStatus;
			
			var manager = new Array();
		    manager['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_MANAGER_LABEL'))."';
			manager['list'] = $listManagers;
			
			var editor = new Array();
		    editor['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_EDITOR_LABEL'))."';
			editor['list'] = $listEditors;
			
			var fromDate = new Array();
		    fromDate['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_FROMDATE_LABEL'))."';
			
		    var toDate = new Array();
		    toDate['label'] = '".html_Metadata::cleanText(JText::_('CATALOG_OBJECTLINK_TODATE_LABEL'))."';
			
		    // Créer le formulaire qui va contenir la structure
			var form = new Ext.form.FormPanel(
				{
					id:'linksForm',
					url: 'index.php',
					border:false,
			        collapsed:false,
			        renderTo: document.getElementById('formContainer'),
			        items        : [
			        	manageObjectLinkFilter(objecttype, id, name, status, manager, editor, fromDate, toDate),
			        	{
			        		xtype		 : 'panel',
							width        : 650,
							height       : 300,
							layout       : 'hbox',
							border		 : false,
							layoutConfig : { align: 'stretch', pack : 'start', padding: '10 10 10 10'},
                            items        : [
								unselectedGrid,
								htmlButtons,
								selectedGrid
							]
						}
					]
			    }
			);

			/****
	        * Setup Drop Targets
	        ***/
	        // This will make sure we only drop to the  view scroller element
	        var unselectedGridDropTargetEl =  unselectedGrid.getView().scroller.dom;
	        var unselectedGridDropTarget = new Ext.dd.DropTarget(unselectedGridDropTargetEl, {
	                ddGroup    : 'unselectedGridDDGroup',
	                notifyDrop : function(ddSource, e, data){
	                       var records =  ddSource.dragData.selections;
	                        Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
	                        //unselectedGrid.store.add(records);
	                        //unselectedGrid.store.sort('name', 'ASC');
	                        
	                        var selectedValues = new Array();
				 			var grid = Ext.getCmp('selected').store.data;
						 	for (var i = 0 ; i < grid.length ;i++) 
				 			{
				 				selectedValues.push(grid.get(i).get('value'));
							}
							
				 			unselectedGrid.store.reload({                    
				 			params: { 
				 				selectedObjects: selectedValues.join(', ')
								}                
							});	
							
	                        return true
	                }
	        });
	
	
	        // This will make sure we only drop to the view scroller element
	        var selectedGridDropTargetEl = selectedGrid.getView().scroller.dom;
	        var selectedGridDropTarget = new Ext.dd.DropTarget(selectedGridDropTargetEl, {
	                ddGroup    : 'selectedGridDDGroup',
	                notifyDrop : function(ddSource, e, data){
	                		var records =  ddSource.dragData.selections;
	                        Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
	                        selectedGrid.store.add(records);
	                        selectedGrid.store.sort('name', 'ASC');
	                        return true
	                }
	        });
	        
			// Affichage du formulaire
    		form.doLayout();
    		
    		// Remplir une première fois les valeurs sélectionnées
    		var selectedValues = new Array();
 			var grid = Ext.getCmp('selected').store.data;
 			for (var i = 0 ; i < grid.length ;i++) 
 			{
 				selectedValues.push(grid.get(i).get('value'));
			}
			
			unselectedGrid.store.load({params: {selectedObjects: selectedValues.join(', ')}});
    		
    		function getObjectList()
			{
				var ds = new Ext.data.Store({
					        proxy: new Ext.data.HttpProxy({
					            url: 'index.php?option=com_easysdi_catalog&task=getObjectForLink'
					        }),
					        reader: new Ext.data.JsonReader({
					            root: 'links',
					            totalProperty: 'total',
					            id: 'value'
					        }, [
					            {name: 'value', mapping: 'value'},
					            {name: 'name', mapping: 'name'}
					        ]),
					        // turn on remote sorting
					        remoteSort: true,
					        baseParams: {limit:100, dir:'ASC', sort:'name', object_id:'".$object_id."'}
					    });
				return ds;
			}
    	";
					
		print_r("<script type='text/javascript'>Ext.onReady(function(){".$javascript."});</script>");
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