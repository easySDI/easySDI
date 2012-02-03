<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

class HTML_simplesearch 
{
	/**
	* Simple searches types 
	*/
	function listSimpleSearch($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_SIMPLE_SEARCH"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_TITLE"); ?></th>				
			</tr>
		</thead>
		<tbody>		
		<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{		
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editSimpleSearch')"><?php echo $row->title; ?></a></td>				
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		
			?></tbody>
			
		<?php			
		
		if ($use_pagination)
		{?>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<?php
		}
		?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="simpleSearch" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editSimpleSearch ($simpleSearch, $rowsFilters,$rowsSelectedFilter,$rowsResultGrid,$rowsSelectedGrid, $option)
	{
		if ($simpleSearch->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_EDIT_SIMPLE_SEARCH"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_NEW_SIMPLE_SEARCH"), 'generic.png' );
		}
		

	?>			
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton == "saveSimpleSearch")
		{
			if (document.getElementById('title').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_SIMPLESEARCH_TITLE_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('dropdown_feature_type').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_SIMPLESEARCH_FT_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('dropdown_display_attr').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_SIMPLESEARCH_ATTR_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('dropdown_id_attr').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_SIMPLESEARCH_ID_ATTR_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('search_attribute').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_SIMPLESEARCH_SEARCH_ATTR_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('operator').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_SIMPLESEARCH_OPERATOR_VALIDATION_ERROR');?>');	
				return;
			}
			else
			{	
				submitform(pressbutton);
			}
		}
		else
		{
			submitform(pressbutton);
		}
	}
	</script>	
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>						
						<table class="admintable">
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_ID"); ?></td>
								<td><?php echo $simpleSearch->id; ?></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_TITLE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="500" name="title" id="title" value="<?php echo $simpleSearch->title; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_DROPDOWN_FT"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="dropdown_feature_type" id="dropdown_feature_type" value="<?php echo $simpleSearch->dropdown_feature_type; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_DROPDOWN_ATTR"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="dropdown_display_attr" id="dropdown_display_attr" value="<?php echo $simpleSearch->dropdown_display_attr; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_DROPDOWN_ID_ATTR"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="dropdown_id_attr" id="dropdown_id_attr" value="<?php echo $simpleSearch->dropdown_id_attr; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_SEARCH_ATTR"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="500" name="search_attribute" id="search_attribute" value="<?php echo $simpleSearch->search_attribute; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_OPERATOR"); ?></td>
								<td><input class="inputbox" type="text" size="5" maxlength="5" name="operator" id="operator" value="<?php echo $simpleSearch->operator; ?>" /></td>
							</tr>							
						</table>
					</fieldset>
					<fieldset>						
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_ADD_FILTERS"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$rowsFilters, 'filter_id[]', 'size="5" multiple="true" class="selectbox"', 'value', 'text', $rowsSelectedFilter ); ?></td>
								<td><a href="./index.php?option=com_easysdi_map&task=newAdditionalFilter" > 
									<img class="helpTemplate" 
										 src="../templates/easysdi/icons/silk/add.png" 
										 alt="<?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_NEW_ADD_FILTERS") ?>" 
										 />
								</a></td>
							</tr>
						</table>
					</fieldset>
					<fieldset>						
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_RESULT_GRID"); ?> : </td>
								<td><?php echo JHTML::_("select.genericlist",$rowsResultGrid, 'grid_id[]', 'size="5" multiple="true" class="selectbox"', 'value', 'text', $rowsSelectedGrid ); ?></td>
								<td><a href="./index.php?option=com_easysdi_map&task=newResultGrid" > 
									<img class="helpTemplate" 
										 src="../templates/easysdi/icons/silk/add.png" 
										 alt="<?php echo JText::_("EASYSDI_MAP_SIMPLE_SEARCH_NEW_RESULT_GRID") ?>" 
										 />
								</a></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $simpleSearch->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
<?php
	}
	
	/**
	 * Additional filters
	*/
	function listAdditionalFilter($use_pagination, $rows, $pageNav, $option)
	{
		JToolBarHelper::title(JText::_("EASYSDI_LIST_ADD_FILTER"));
		?>
		<form action="index.php" method="GET" name="adminForm">

		<table class="adminlist">
		<thead>
			<tr>
				<th width="20" class='title'><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_SHARP"); ?></th>
				<th width="20" class='title'><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($rows); ?>);" /></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_TITLE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_ATTR"); ?></th>	
				<th class='title'><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_VALUE"); ?></th>
				<th class='title'><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_OPERATOR"); ?></th>					
			</tr>
		</thead>
		<tbody>		
		<?php
		$k = 0;
		$i=0;
		foreach ($rows as $row)
		{		
		?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $i+$pageNav->limitstart+1;?></td>
				<td><input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="#edit" onclick="return listItemTask('cb<?php echo $i;?>','editAdditionalFilter')"><?php echo $row->title; ?></a></td>
				<td><?php echo $row->attribute; ?></td>
				<td><?php echo $row->value; ?></td>
				<td><?php echo $row->operator; ?></td>			
			</tr>
		<?php
			$k = 1 - $k;
			$i++;
		}
		
			?></tbody>
			
		<?php			
		
		if ($use_pagination)
		{?>
		<tfoot>
		<tr>	
		<td colspan="8"><?php echo $pageNav->getListFooter(); ?></td>
		</tr>
		</tfoot>
		<?php
		}
		?>
	  	</table>
	  	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="additionalFilter" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	</form>
		<?php		
	}
	
	function editAdditionalFilter ($additionalFilter, $option)
	{
		if ($additionalFilter->id != 0)
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_EDIT_ADD_FILTER"), 'generic.png' );
		}
		else
		{
			JToolBarHelper::title( JText::_("EASYSDI_MAP_NEW_ADD_FILTER"), 'generic.png' );
		}
		

	?>				
	<script>	
	function submitbutton(pressbutton)
	{
		if(pressbutton == "saveAdditionalFilter")
		{
			if (document.getElementById('attribute').value == "")
			{	
				alert ('<?php echo  JText::_( 'EASYSDI_ADDFILTER_ATTRIBUTE_VALIDATION_ERROR');?>');	
				return;
			}
			else if (document.getElementById('value').value == "")
			{
				alert ('<?php echo  JText::_( 'EASYSDI_ADDFILTER_VALUE_VALIDATION_ERROR');?>');	
				return;
			}			
			else
			{	
				submitform(pressbutton);
			}
		}
		else
		{
			submitform(pressbutton);
		}
	}
	</script>	
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">

		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td>
					<fieldset>						
						<table class="admintable">
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_ID"); ?></td>
								<td><?php echo $additionalFilter->id; ?></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_TITLE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="title" id="title" value="<?php echo $additionalFilter->title; ?>" /></td>								
							</tr>
							<tr>
								<td class="key" width="100p"><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_ATTRIBUTE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="attribute" id="attribute" value="<?php echo $additionalFilter->attribute; ?>" /></td>								
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_VALUE"); ?></td>
								<td><input class="inputbox" type="text" size="100" maxlength="100" name="value" id="value" value="<?php echo $additionalFilter->value; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_("EASYSDI_MAP_ADD_FILTER_OPERATOR"); ?></td>
								<td><input class="inputbox" type="text" size="5" maxlength="5" name="operator" id="operator" value="<?php echo $additionalFilter->operator; ?>" /></td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" value="<?php echo $additionalFilter->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="task" value="" />
	</form>
	
<?php
	}
}
?>