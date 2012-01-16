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

JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
				
		
class HTML_objecttype {
	
	
	function listObjectType(&$rows, $page, $option,  $filter_order_Dir, $filter_order)
	{			
		$database =& JFactory::getDBO();
		$ordering = ($filter_order == 'ordering');
		
?>
	<form action="index.php" method="POST" name="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderObjectType' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>			
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPE_PREDEFINED"), 'predefined', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPE_HASVERSIONING"), 'hasVersioning', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$i=0;
		foreach ($rows as $row)
		{		
?>
			<tr>
				<td align="center" width="10px"><?php echo $page->getRowOffset( $i );//echo $i+$page->limitstart+1;?></td>
				<td width="10px"><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupObjectType', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObjectType', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupObjectType', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownObjectType', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownObjectType', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObjectType', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownObjectType', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupObjectType', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	             <?php $link =  "index.php?option=$option&amp;task=editObjectType&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>
	            <!-- <td><?php //echo $row->isoscopecode; ?></td> -->
	            <td><?php echo $row->description; ?></td>
	            <td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->predefined ? $imgY : $imgX;
						$prefix = "objecttype_predefined_";
						$task 	= $row->predefined ? 'unpublish' : 'publish';
						$alt = $row->predefined ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->hasVersioning ? $imgY : $imgX;
						$prefix = "objecttype_hasversioning_";
						$task 	= $row->hasVersioning ? 'unpublish' : 'publish';
						$alt = $row->hasVersioning ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
	            <td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="9"><?php echo $page->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listObjectType" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	
	function editObjectType(&$row, $fieldsLength, $languages, $labels, $accounts, $selected_accounts, $profiles, $namespacelist, $option )
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		
		?>
		<form action="index.php" method="POST" name="adminForm" id="adminForm" onsubmit="PostSelect('adminForm', 'selected_accounts')">
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>							
					<td width=150><?php echo JText::_("CORE_NAME"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php echo $row->name; ?>" /></td>
				</tr>					
				<tr>							
					<td width=150><?php echo JText::_("CORE_CODE"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['code'];?>" name="code" value="<?php echo $row->code; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPE_LOGO"); ?> : </td>
					<td><input class="inputbox" type="text" size="100" maxlength="<?php echo $fieldsLength['logo'];?>" name="logo" value="<?php echo $row->logo; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPE_PREDEFINED"); ?> : </td>
					<td><?php echo JHTML::_('select.booleanlist', 'predefined', '', $row->predefined); ?> </td>																
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPE_HASVERSIONING"); ?> : </td>
					<td><?php echo JHTML::_('select.booleanlist', 'hasVersioning', '', $row->hasVersioning); ?> </td>																
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPE_PREFIXCODE"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['code'];?>" name="code" value="<?php echo $row->code; ?>" /></td>
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PROFILE"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$profiles, 'profile_id', 'size="1" class="inputbox"', 'value', 'text', $row->profile_id); ?></td>
				</tr>
				<tr>
					<td WIDTH=150><?php echo JText::_("CATALOG_OBJECTTYPE_FRAGMENT"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'fragmentnamespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->fragmentnamespace_id ); ?>
						<input size="50" type="text" name ="fragment" value="<?php if ($pageReloaded and array_key_exists('fragment', $_POST)) echo $_POST['fragment']; else echo $row->fragment;?>" maxlength="<?php echo $fieldsLength['fragment'];?>"> 
					</td>							
				</tr>
				<tr>
					<td colspan="2">
						<fieldset id="labels">
							<legend align="top"><?php echo JText::_("CORE_LABEL"); ?></legend>
							<table>
									<?php
									foreach ($languages as $lang)
									{ 
									?>
														<tr>
														<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?> : </td>
														<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($labels[$lang->id])?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
														</tr>
									<?php
									}
									?>
							</table>
						</fieldset>
					</td>
				</tr>
				
				<!-- Adding section to allow admin to configure extra parameters for sitemap url generation -->
				<tr>
					<td colspan="2">
						<fieldset id="labels">
							<legend align="top"><?php echo JText::_("CATALOG_SITEMAP_CONFIG_LABEL"); ?></legend>
							<table>
				
								<tr>
								<td width=140><?php echo JText::_("CATALOG_SITEMAP_EXTRAPARAMS") ?> : </td>
								<td><input id="sitemapParams" onblur="updateSampleRobotUrl()" style="width:244px" type="text" name ="sitemapParams" value="<?php echo htmlspecialchars( $row->sitemapParams)?>" ></td>							
								</tr>
								<tr>
									<td width=140><?php echo JText::_("CATALOG_SITEMAP_URLSAMPLE") ?> : </td>
									<td><div id="sampleRobotUrl"></div></td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<!-- End of sitemap url configuration -->
				<tr>
					<td colspan=2>
						<fieldset>
							<legend><?php echo JText::_( 'CORE_ACCOUNT_NAME'); ?></legend>
							<table>
								<tr>
									<th><b><?php echo JText::_( 'CORE_AVAILABLE'); ?></b></th>
									<th></th>
									<th><b><?php echo JText::_( 'CORE_SELECTED'); ?></b></th>
								</tr>
								<tr>
									<td>
										<select name="accounts[]" id="accounts" size="10" multiple="multiple">
										<?php
										foreach ($accounts as $account){
											echo "<option value='".$account->value."'>".$account->text."</option>";
										}
										?>
										</select></td>
									<td>
									<table>
										<tr>
											<td><input type="button" value="<<" id="removeAllAccounts"
												onclick="javascript:TransfertAll('selected_accounts','accounts');"></td>
										</tr>
										<tr>
											<td><input type="button" value="<" id="removeAccount"
												onclick="javascript:Transfert('selected_accounts', 'accounts');"></td>
										</tr>
										<tr>
											<td><input type="button" value=">" id ="addAccount"
												onclick="javascript:Transfert('accounts','selected_accounts');"></td>
										</tr>
										<tr>
											<td><input type="button" value=">>" id="addAllAccounts"
												onclick="javascript:TransfertAll('accounts', 'selected_accounts');"></td>
										</tr>
									</table>
									</td>
									<td>
										<select name="selected_accounts[]" id="selected_accounts" size="10" multiple="multiple">
										<?php
											foreach ($selected_accounts as $selected_accounts){
												echo "<option value='".$selected_accounts->value."'>".$selected_accounts->text."</option>";
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
			<script>
				window.onload = updateSampleRobotUrl; 
			</script>
<?php
$user =& JFactory::getUser();
if ($row->created)
{ 
?>
				<tr>
					<td><?php echo JText::_("CORE_CREATED"); ?> : </td>
					<td><?php if ($row->created) {echo date('d.m.Y h:i:s',strtotime($row->created));} ?></td>
					<td>, </td>
					<?php
						if ($row->createdby and $row->createdby<> 0)
						{
							$query = "SELECT name FROM #__users WHERE id=".$row->createdby ;
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
if ($row->updated)
{ 
?>
				<tr>
					<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
					<td><?php if ($row->updated and $row->updated<> 0) {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
					<td>, </td>
					<?php
						if ($row->updatedby and $row->updatedby<> 0)
						{
							$query = "SELECT name FROM #__users WHERE id=".$row->updatedby ;
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
			<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="guid" value="<?php echo $row->guid; ?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			
			<input type="hidden" name="cid[]" value="<?php echo $row->id;?>" />
			<input type="hidden" name="option" value="<?php echo $option;?>" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php
	}

}
	
?>
