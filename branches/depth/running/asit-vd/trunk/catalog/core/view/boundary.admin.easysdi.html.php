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

?>
<script type="text/javascript">
	function submitbutton(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton != 'saveBoundary' && pressbutton != 'applyBoundary') {
			submitform( pressbutton );
			return;
		}

		// Récuperer tous les labels et contrôler qu'ils soient saisis
		var labelEmpty = 0;
		labels = document.getElementById('labels');
		fields = labels.getElementsByTagName('input');
		
		for (var i = 0; i < fields.length; i++)
		{
			if (fields.item(i).value == "")
				labelEmpty=1;
		}
		
		
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'CATALOG_BOUNDARY_SUBMIT_NONAME', true ); ?>" );
		}
		else if (labelEmpty > 0) 
		{
			alert( "<?php echo JText::_( 'CATALOG_BOUNDARY_SUBMIT_NOLABELS', true ); ?>" );
		}
		
		else 
		{
			submitform( pressbutton );
		}
	}
	
	var request;
	function getParentList(){
		var selectedCategory = document.getElementById('category_id').value;
		request = getHTTPObject();
	    document.getElementById("progress").style.visibility = "visible";
	    request.onreadystatechange = parseServerResponse;
	    request.open("GET", "index.php?option=com_easysdi_catalog&task=getParentPerimeterList&category_id="+selectedCategory, true);
	    request.send(null);
	}

	function getHTTPObject(){
	    var xhr = false;
	    if (window.XMLHttpRequest){
	        xhr = new XMLHttpRequest();
	    } else if (window.ActiveXObject) {
	        try{
	            xhr = new ActiveXObject("Msxml2.XMLHTTP");
	        }catch(e){
	            try{
	                xhr = new ActiveXObject("Microsoft.XMLHTTP");
	            }catch(e){
	                xhr = false;
	            }
	        }
	    }
	    return xhr;
	}

	function parseServerResponse()
	{
	    // if request object received response
	    if(request.readyState == 4){
	    	document.getElementById("progress").style.visibility = "hidden";
			var JSONtext = request.responseText;
			document.getElementById('parent_id').options.length = 0;
			
			var valuevalue = -1;
			var valuetext = -1;
			var JSONobject = JSON.parse(JSONtext, function (key, value) {
				if(key == 'text'){
					valuetext = value;
				}
				if(key == 'value'){
					valuevalue = value;
				}
				if(valuevalue != -1 && valuetext != -1){
					var elOptNew = document.createElement('option');
				  	elOptNew.text = valuetext;
				  	elOptNew.value = valuevalue;
				  	try{
						document.getElementById('parent_id').add(elOptNew, null);// standards compliant; doesn't work in IE
				  	}catch (ex){
				  		document.getElementById('parent_id').add(elOptNew); //IE only
				  	}
					valuevalue = -1;
					valuetext = -1;
				}
			});			
	    }
	}
</script>

<?php 

class HTML_boundary {
function listBoundary(&$rows, $page, $option,  $filter_order_Dir, $filter_order)
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
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderBoundary' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_PERIMETER_CATEGORY_TITLE"), 'category_title', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'modified', @$filter_order_Dir, @$filter_order); ?></th>
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
							 <?php echo $page->orderUpIcon($i, true, 'orderupBoundary', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownBoundary', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupBoundary', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownBoundary', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownBoundary', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupBoundary', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderdownBoundary', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderupBoundary', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
				 <?php $link =  "index.php?option=$option&amp;task=editBoundary&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->category_title; ?></td>
				<td width="100px"><?php if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td>
			</tr>
<?php
			$i ++;
		}
		
			?>
		</tbody>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $page->getListFooter(); ?></td>
		</tr>
		</tfoot>
		</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listBoundary" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function editBoundary(&$row, $fieldsLength, $languages, $labels,$titles, $contents,$categories,$parents, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<div id="progress" style="visibility:hidden">
				<img id="progress_image"  src="components/com_easysdi_core/templates/images/loader.gif" alt="">
			</div>
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PERIMETER_CATEGORY_LIST"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$categories, 'category_id', 'size="1" class="inputbox" onchange="getParentList()"', 'value', 'text', $row->category_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PERIMETER_PARENT_LIST"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$parents, 'parent_id', 'size="1" class="inputbox" onchange=""', 'value', 'text', $row->parent_id ); ?></td>							
				</tr>
			</table>
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td colspan="2">
						<fieldset id="titles">
							<legend align="top"><?php echo JText::_("CATALOG_PERIMETER_MULTI_ID"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="title<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($titles[$lang->id])?>" maxlength="<?php echo $fieldsLength['title'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset id="labels">
							<legend align="top"><?php echo JText::_("CATALOG_PERIMETER_MULTI_NAME"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($labels[$lang->id])?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset id="contents">
							<legend align="top"><?php echo JText::_("CORE_DESCRIPTION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><textarea rows="5" cols="50" name ="content<?php echo "_".$lang->code;?>" ><?php echo htmlspecialchars($contents[$lang->id])?></textarea></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td>
						<fieldset id="boundaries">
							<legend><?php echo JText::_("CATALOG_BOUNDARY_BOUNDS"); ?></legend>
							<table border="0" cellpadding="3" cellspacing="0">	
								<tr>
									<td width=140><?php echo JText::_("CATALOG_BOUNDARY_NORTHBOUND"); ?></td>
									<td><input size="50" type="text" name ="northbound" value="<?php echo $row->northbound?>"> </td>							
								</tr>
								<tr>
									<td><?php echo JText::_("CATALOG_BOUNDARY_SOUTHBOUND"); ?></td>
									<td><input size="50" type="text" name ="southbound" value="<?php echo $row->southbound?>"> </td>							
								</tr>
								<tr>
									<td><?php echo JText::_("CATALOG_BOUNDARY_EASTBOUND"); ?></td>
									<td><input size="50" type="text" name ="eastbound" value="<?php echo $row->eastbound?>"> </td>							
								</tr>
								<tr>
									<td><?php echo JText::_("CATALOG_BOUNDARY_WESTBOUND"); ?></td>
									<td><input size="50" type="text" name ="westbound" value="<?php echo $row->westbound?>"> </td>							
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
			<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
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
	
	function listBoundaryCategory(&$rows, $page, $option,  $filter_order_Dir, $filter_order)
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
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_TITLE"), 'title', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_ALIAS"), 'alias', @$filter_order_Dir, @$filter_order); ?></th>
					<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'modified', @$filter_order_Dir, @$filter_order); ?></th>
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
					 <?php $link =  "index.php?option=$option&amp;task=editBoundaryCategory&cid[]=$row->id";?>
					<td><a href="<?php echo $link;?>"><?php echo $row->title; ?></a></td>
					<td><?php echo $row->alias; ?></td>
					<td width="100px"><?php if ($row->modified and $row->modified<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->modified));} ?></td>
				</tr>
	<?php
				$i ++;
			}
			
				?>
			</tbody>
			<tfoot>
			<tr>	
			<td colspan="8"><?php echo $page->getListFooter(); ?></td>
			</tr>
			</tfoot>
			</table>
		  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
		  	<input type="hidden" name="task" value="listBoundary" />
		  	<input type="hidden" name="boxchecked" value="0" />
		  	<input type="hidden" name="hidemainmenu" value="0">
		  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
		  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
		  </form>
	<?php
		}
		
		function editBoundaryCategory(&$row, $fieldsLength, $languages, $labels, $parents, $option)
		{
			global  $mainframe;
		
			$database =& JFactory::getDBO();
		
			?>
				<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
					<table border="0" cellpadding="3" cellspacing="0">	
						<tr>
							<td width=150><?php echo JText::_("CORE_TITLE"); ?></td>
							<td><input size="50" type="text" name ="title" value="<?php echo $row->title?>" maxlength="<?php echo $fieldsLength['title'];?>"> </td>							
						</tr>
						<tr>
							<td><?php echo JText::_("CORE_ALIAS"); ?></td>
							<td><input size="50" type="text" name ="alias" value="<?php echo $row->alias?>" maxlength="<?php echo $fieldsLength['alias'];?>"></td>							
						</tr>
						<tr>
							<td><?php echo JText::_("CATALOG_PERIMETER_CATEGORY_PARENT_LIST"); ?></td>
						<td><?php echo JHTML::_("select.genericlist",$parents, 'parent_id', 'size="1" class="inputbox" onchange=""', 'value', 'text', $row->parent_id ); ?></td>							
				</tr>
					</table>
					<table border="0" cellpadding="3" cellspacing="0">
							<tr>
							<td colspan="2">
								<fieldset id="labels">
									<legend align="top"><?php echo JText::_("CATALOG_PERIMETER_MULTI_NAME"); ?></legend>
									<table>
		<?php
		foreach ($languages as $lang)
		{ 
		?>
							<tr>
							<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
							<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($labels[$lang->id])?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
							</tr>
		<?php
		}
		?>
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
								if ($row->created_by and $row->created_by<> 0)
								{
									$query = "SELECT name FROM #__users WHERE id=".$row->created_by ;
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
		if ($row->modified)
		{ 
		?>
						<tr>
							<td><?php echo JText::_("CORE_UPDATED"); ?> : </td>
							<td><?php if ($row->modified and $row->modified<> 0) {echo date('d.m.Y h:i:s',strtotime($row->modified));} ?></td>
							<td>, </td>
							<?php
								if ($row->modified_by and $row->modified_by<> 0)
								{
									$query = "SELECT name FROM #__users WHERE id=".$row->modified_by ;
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
					<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
					<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
					<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
					<input type="hidden" name="created_by" value="<?php echo ($row->created_by)? $row->created_by : $user->id; ?>" /> 
					<input type="hidden" name="modified" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
					<input type="hidden" name="modified_by" value="<?php echo ($row->created_by)? $user->id : ''; ?>" /> 
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="id" value="<?php echo $row->id?>" />
					<input type="hidden" name="task" value="" />
				</form>
					<?php 	
			}
}
?>