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

class HTML_product{
	
	function listProduct($pageNav,$rows,$option){
		?>	
		<div class="contentin">
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
		<h2 class="contentheading"><?php echo JText::_("LIST_PRODUCT"); ?></h2>
	
		<h3> <?php echo JText::_("SEARCH_CRITERIA_TITLE"); ?></h3>
	
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("FILTER");?></b>&nbsp;
					<input type="text" name="search" value="<?php echo $search;?>" class="inputbox" " />			
				</td>
			</tr>
		</table>
		
		<button type="submit" class="searchButton" > <?php echo JText::_("SEARCH_BUTTON"); ?></button>
		<br>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td><td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("SEARCH_RESULTS_TITLE"); ?></h3>
	
	
	<table>
	<thead>
	<tr>
	<th><?php echo JText::_('PRODUCT_SHARP'); ?></th>
	<th></th>
	<th><?php echo JText::_('PRODUCT_NAME'); ?></th>
	<th><?php echo JText::_('PRODUCT_INTERNAL'); ?></th>
	<th><?php echo JText::_('PRODUCT_EXTERNAL'); ?></th>
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
			<td><?php echo $i; ?></td>
			<td><input type="radio" name="id" value="<?php echo $row->id ;?>"></td>						
			<td><a class="modal" title="<?php echo JText::_("VIEW_MD"); ?>" href="./index.php?tmpl=component&option=<?php echo $option; ?>&task=showMetadata&id=<?php echo $row->metadata_id;  ?>" rel="{handler:'iframe',size:{x:500,y:500}}"> <?php echo $row->data_title ;?></a></td>
			<td><input type="checkbox" <?php if ($row->internal) {echo "checked";};?> disabled> </td>
			<td><input type="checkbox" <?php if ($row->external) {echo "checked";};?> disabled> </td>
			</tr>
			
				<?php		
		}
		
	?>
	</tbody>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task<?php echo $option; ?>" name="task" value="listProduct">
			<button type="button" onClick="document.getElementById('task<?php echo $option; ?>').value='archiveOrder';document.getElementById('productListForm').submit();" ><?php echo JText::_("ARCHIVE_ORDER"); ?></button>
		</form>
		</div>
	<?php
		
		
	}
	
}
?>