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
		<?php 
		if ($rowObject->id == 0)
		{
		?>
							<tr>
								<td colspan="2">
									<fieldset>
										<legend><?php echo JText::_("CATALOG_OBJECT_VERSION"); ?></legend>
										<table>
											<tr>
												<td><?php echo JText::_("CATALOG_OBJECT_VERSION_NAME"); ?> : </td>
												<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="version_name" value="<?php if ($pageReloaded) echo $_POST['version_name']; ?>" /></td>								
											</tr>
											<tr>
												<td><?php echo JText::_("CATALOG_OBJECT_VERSION_DESCRIPTION"); ?> : </td>
												<td><textarea rows="4" cols="50" name ="version_description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php if ($pageReloaded) echo $_POST['version_description'];?></textarea></td>								
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
		<?php
		} 
		?>
							<!-- 
							<tr>
								<td><?php echo JText::_("CORE_CODE"); ?> : </td>
								<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['code'];?>" name="code" value="<?php echo $rowObject->code; ?>" /></td>								
							</tr>
							 -->
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
							<!-- <tr>							
								<td><?php //echo JText::_("CORE_MANAGER_MAIN_LABEL"); ?> : </td>
								<td><?php //echo JHTML::_("select.genericlist",$managers, 'manager_id', 'size="1" class="inputbox" ', 'value', 'text', $rowObject->manager_id  ); ?></td>								
							</tr>
							 -->
							<tr>							
								<td><?php echo JText::_("CORE_OBJECT_TYPE_LABEL"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$objecttypes, 'objecttype_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObject\');"', 'value', 'text', ($pageReloaded)? $_POST['objecttype_id'] : $rowObject->objecttype_id  ); ?></td>								
							</tr>
							<tr>							
								<td><?php echo JText::_("CORE_OBJECT_SUPPLIERNAME"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$accounts, 'account_id', 'size="1" class="inputbox" onchange="javascript:submitform(\'editObject\');"', 'value', 'text', ($pageReloaded)? $_POST['account_id'] : $rowObject->account_id ); ?></td>								
							</tr>
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
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_OBJECT_SUPPLIERNAME"), 'account_name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_OBJECTTYPE_NAME"), 'objecttype_name', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_METADATA_STATE"), 'state', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JText::_("CATALOG_OBJECT_VERSION_COL"); ?></th>
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
					<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'object_'); ?></td>
					<td><?php echo $row->account_name; ?></td>						
					<td><?php echo JText::_($row->description); ?></td>		
					<td><?php echo JText::_($row->objecttype_name); ?></td>
					<td><?php echo JText::_($row->state); ?></td>
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
					<td><?php echo $versions; ?></td>		
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
			<td colspan="14"><?php echo $page->getListFooter(); ?></td>
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
	
	function historyAssignMetadata($rows, $page, $object_id, $option)
	{
		?>
		<form action="index.php" method="post" name="adminForm">
			<table class="adminlist">
			<thead>
				<tr>					 			
					<th class='title' width="10px"><?php echo JText::_("CATALOG_HISTORYASSIGN_ASSIGNEDBY"); ?></th>
					<th class='title' width="10px"><?php echo JText::_("CATALOG_HISTORYASSIGN_ASSIGNEDTO"); ?></th>
					<th class='title' width="10px"><?php echo JText::_("CATALOG_HISTORYASSIGN_DATE"); ?></th>
				</tr>
			</thead>
			<tbody>		
	<?php
			$k = 0;
			$i=0;
			foreach ($rows as $row)
			{		  				
	?>
				<tr>
					<td><?php echo $row->assignedby; ?></td>						
					<td><?php echo $row->assignedto; ?></td>						
					<td><?php echo date('d.m.Y h:i:s',strtotime($row->date)); ?></td>
				</tr>
	<?php
				$k = 1 - $k;
				$i ++;
			}
			
				?>
			</tbody>
			<tfoot>
			<tr>	
			<td colspan="3"><?php echo $page->getListFooter(); ?></td>
			</tr>
			</tfoot>
			</table>
		  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
		  	<input type="hidden" name="task" value="historyAssignMetadata" />
		  	<input type="hidden" name="object_id" value="<?php echo $object_id; ?>" />
		  </form>
	<?php
			
	}
	
	function viewObjectLink($parent_objectlinks, $child_objectlinks, $object_id, $option)
	{
		JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');

		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		
		$javascript = "";
	
		?>
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
		<?php
		
		$javascript .="
			var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			
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
		JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
		JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');
		JHTML::script('Components_extjs.js', 'administrator/components/com_easysdi_catalog/js/');
		
		$uri =& JUri::getInstance();
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base() . 'components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		
		$javascript = "";
	
		?>
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
		<?php
		
		$javascript .="
			var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
			Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
			
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
					method: 'POST',
					border: false,
			        collapsed: false,
			        labelWidth: 200,
					renderTo: document.getElementById('formContainer'),
			        standardSubmit:true,
			        items        : [
			        	manageObjectLinkFilter(objecttype, id, name, status, manager, editor, fromDate, toDate),
			        	{
			        		id			: 'gridPanel',
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
						},
				       { 
				         id:'objectlinks', 
				         xtype: 'hidden',
				         value:'' 
				       },
				       { 
				         id:'task', 
				         xtype: 'hidden',
				         value:'saveObjectLink' 
				       },
				       { 
				         id:'option', 
				         xtype: 'hidden',
				         value:'".$option."' 
				       },
				       { 
				         id:'object_id', 
				         xtype: 'hidden',
				         value:'".$object_id."' 
				       }
					],
					buttons: [
						{
							text:'".html_Metadata::cleanText(JText::_('CORE_SAVE'))."',
		                    handler: function(){
		                    	var selectedValues = new Array();
					 			var grid = Ext.getCmp('selected').store.data;
							 	for (var i = 0 ; i < grid.length ;i++) 
					 			{
					 				selectedValues.push(grid.get(i).get('value'));
								}
								
		                    	form.getForm().setValues({objectlinks: selectedValues.join(', ')});
							    form.getForm().submit();
		                    	}
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
}
?>