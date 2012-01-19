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

class HTML_language {
	
	
	function listLanguage(&$rows, $page, $option,  $filter_order_Dir, $filter_order)
	{			
		global  $mainframe;
		$database =& JFactory::getDBO();
		$ordering = ($filter_order == 'ordering');
		
?>
	<form action="index.php" method="GET" name="adminForm">
		<table class="adminlist">
		<thead>
			<tr>
				<th class='title' width="10px"><?php echo JText::_("CORE_SHARP"); ?></th>
				<th class='title' width="10px"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>				
				<th class='title' width="30px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ID"), 'id', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_ORDER"), 'ordering', @$filter_order_Dir, @$filter_order); ?>
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderLanguage' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>			
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_LABEL"), 'label', @$filter_order_Dir, @$filter_order); ?></th>			
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_CODE"), 'code', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_LANGUAGE_ISOCODE"), 'isocode', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_CODEEASYSDI"), 'codelang', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_LANGUAGE_GEMETLANG"), 'gemetlang', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_LANGUAGE_DEFAULT"), 'defaultlang', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>
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
							 <?php echo $page->orderUpIcon($i, true, 'orderupLanguage', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownLanguage', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupLanguage', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownLanguage', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownLanguage', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupLanguage', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownLanguage', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupLanguage', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	             <?php $link =  "index.php?option=$option&amp;task=editLanguage&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>
	            <td><?php echo $row->label; ?></td>
	            <td><?php echo $row->code; ?></td>
	            <td><?php echo $row->isocode; ?></td>
	            <td><?php echo $row->codelang; ?></td>
	            <td><?php echo $row->gemetlang; ?></td>
	            <td align="center">
		            <?php
					if ($row->defaultlang == 1) {
					?>
						<img src="templates/khepri/images/menu/icon-16-default.png" alt="<?php echo JText::_( 'Published' ); ?>" />
					<?php
					} 
					else {
					?>
						&nbsp;
					<?php
					}
					?>
				</td>
				<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'language_'); ?></td>
				<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="13"><?php echo $page->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listLanguage" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	
	function editLanguage(&$row, $fieldsLength, $codes, $option )
	{
		global  $mainframe;
		$database =& JFactory::getDBO(); 
		
		JHTML::script('core.js', 'administrator/components/com_easysdi_core/js/');
		
		?>
		<form action="index.php" method="post" name="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>							
					<td><?php echo JText::_("CORE_NAME"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['name'];?>" name="name" value="<?php echo $row->name; ?>" /></td>
				</tr>					
				<tr>							
					<td><?php echo JText::_("CORE_LABEL"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['label'];?>" name="label" value="<?php echo $row->label; ?>" /></td>
				</tr>					
				<tr>
					<td><?php echo JText::_("CORE_CODE"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['code'];?>" name="code" value="<?php echo $row->code; ?>" /></td>
					<td>
						<div>
							<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_LANGUAGE_CODE_TIP' ); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_LANGUAGE_ISOCODE"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['isocode'];?>" name="isocode" value="<?php echo $row->isocode; ?>" /></td>
					<td>
						<div>
							<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_LANGUAGE_ISOCODE_TIP' ); ?>
						</div>
					</td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_LANGUAGE_GEMETLANG"); ?> : </td>
					<td><input class="inputbox" type="text" size="50" maxlength="<?php echo $fieldsLength['gemetlang'];?>" name="gemetlang" value="<?php echo $row->gemetlang; ?>" /></td>
					<td>
						<div>
							<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_LANGUAGE_GEMETLANG_TIP' ); ?>
						</div>
					</td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_CODEEASYSDI"); ?> : </td>
					<td><?php echo JHTML::_("select.genericlist",$codes, 'codelang_id', 'size="1" class="inputbox"', 'value', 'text', $row->codelang_id); ?></td>
					<td>
						<div>
							<img src="<?php echo JURI::root(true);?>/includes/js/ThemeOffice/warning.png" style="vertical-align:top" alt="" /> <?php echo JText::_( 'CORE_LANGUAGE_CODELANG_TIP' ); ?>
						</div>
					</td>								
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?> : </td>
					<td colspan="2"><textarea rows="4" cols="50" name ="description" onkeypress="maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
					<td><?php echo JHTML::_('select.booleanlist', 'published', '', $row->published);?> </td>																
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
if ($row->updated and $row->updated<> '0000-00-00 00:00:00')
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
