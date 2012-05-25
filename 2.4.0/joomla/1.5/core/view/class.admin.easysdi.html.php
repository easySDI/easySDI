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
		
class HTML_class {
function listClass(&$rows, $lists, $page, $option,  $filter_order_Dir, $filter_order)
	{
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$ordering = ($filter_order == 'ordering');
		
?>
	<form action="index.php" method="POST" name="adminForm">
		<table>
			<tr>
				<td width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="searchClass" id="searchClass" value="<?php echo $lists['searchClass'];?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('searchClass').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
			</tr>
		</table>
		
		
		<table class="adminlist">
		<thead>
			<tr>
				<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<!-- <th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderClass' ); ?></th> -->
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_ISOCODE"), 'class_isocode', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_ISROOTCLASS"), 'isrootclass', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ISSYSTEM"), 'issystem', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ISEXTENSIBLE"), 'isextensible', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_DESCRIPTION"), 'description', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>		
<?php
		$i=0;
		foreach ($rows as $row)
		{		
			$checked 	= JHTML::_('grid.checkedout',   $row, $i );		
?>
			<tr>
				<td align="center" width="10px"><?php echo $page->getRowOffset( $i );//echo $i+$page->limitstart+1;?></td>
				<td align="center">
					<?php echo $checked; ?>
				</td>												
				<td width="30px" align="center"><?php echo $row->id; ?></td>
				<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
				<!-- <td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupClass', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownClass', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupClass', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownClass', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownClass', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupClass', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownClass', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupClass', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	             -->
	            <?php $link =  "index.php?option=$option&amp;task=editClass&cid[]=$row->id";?>
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
				<td><?php echo htmlspecialchars($row->class_isocode); ?></td>
				<td width="100px" align="center">
					<img src="images/<?php echo ( $row->isrootclass ) ? 'tick.png' : 'publish_x.png';?>" width="16" height="16" border="0" alt="<?php echo ( $row->isrootclass ) ? JText::_( 'Yes' ) : JText::_( 'No' );?>" />
				</td>
				<td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->issystem ? $imgY : $imgX;
						$prefix = "class_issystem_";
						$task 	= $row->issystem ? 'unpublish' : 'publish';
						$alt = $row->issystem ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<td width="100px" align="center">
					<?php 
						$imgY = 'tick.png';
						$imgX = 'publish_x.png';
						$img 	= $row->isextensible ? $imgY : $imgX;
						$prefix = "class_isextensible_";
						$task 	= $row->isextensible ? 'unpublish' : 'publish';
						$alt = $row->isextensible ? JText::_( 'Yes' ) : JText::_( 'No' );		
					?>
					<a href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $prefix.$task;?>');">
						<img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt;?>" />
					</a>
				</td>
				<td><?php echo $row->description; ?></td>
				<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
			<tr>	
				<td colspan="11"><?php echo $page->getListFooter(); ?></td>
			</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listClass" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editClass(&$row, $accounts, $selected_accounts, $fieldsLength, $languages, $informations, $namespacelist,$stereotypelist, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm" onsubmit="javascript:PostSelect('adminForm', 'selected_accounts')">
			<table class="admintable" border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td class="key" width=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_CLASS_STEREOTYPE"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$stereotypelist, 'stereotype_id', 'size="1" class="inputbox" onchange=""', 'value', 'text', $row->stereotype_id ); ?></td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_CLASS_ISOCODE"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'namespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->namespace_id ); ?>
						<input size="50" type="text" name ="isocode" value="<?php echo $row->isocode?>" maxlength="<?php echo $fieldsLength['isocode'];?>"> 
					</td>							
				</tr>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_INFORMATION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><textarea  rows="4" cols="50" name="information<?php echo "_".$lang->code;?>" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['information'];?>);"><?php echo htmlspecialchars($informations[$lang->id])?></textarea></td>
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CORE_ISSYSTEM"); ?></td>
					<td><?php echo JHTML::_('select.booleanlist', 'issystem', '', $row->issystem);?> </td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CORE_ISEXTENSIBLE"); ?></td>
					<td><?php echo JHTML::_('select.booleanlist', 'isextensible', '', $row->isextensible);?> </td>							
				</tr>	
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_ISROOTCLASS"); ?></td>
					<td><?php echo JHTML::_('select.booleanlist', 'isrootclass', '', $row->isrootclass);?> </td>
				</tr>
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
			
			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>