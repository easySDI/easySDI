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


class HTML_relation {
function listRelation(&$rows, $lists, $page, $option,  $filter_order_Dir, $filter_order)
	{
		$database =& JFactory::getDBO();
		$user	=& JFactory::getUser();
		$ordering = ($filter_order == 'ordering');
		
?>
	<form action="index.php" method="post" name="adminForm">
		<table>
			<tr>
				<td width="100%">
					<?php echo JText::_( 'Filter' ); ?>:
					<input type="text" name="searchRelation" id="searchRelation" value="<?php echo $lists['searchRelation'];?>" class="text_area" onchange="document.adminForm.submit();" />
					<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
					<button onclick="document.getElementById('searchRelation').value='';document.getElementById('filter_rendertype_id').value='-1';document.getElementById('filter_relationtype_id').value='-1';document.getElementById('filter_state').value='-1';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php
					echo $lists['rendertype_id'];
					?>
				</td>
				<td nowrap="nowrap">
					<?php
					echo $lists['relationtype_id'];
					?>
				</td>
				<td nowrap="nowrap">
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
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderRelation' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_PARENT"), 'parent_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_OBJECTTYPECHILD"), 'objecttypechild_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_CLASSCHILD"), 'classchild_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_ATTRIBUTECHILD"), 'attributechild_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_RENDERTYPE"), 'rendertype_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_RELATIONTYPE"), 'relationtype_name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_PUBLISHED"), 'published', @$filter_order_Dir, @$filter_order); ?></th>
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
				<td width="100px" align="right">
					<?php
					if ($filter_order=="ordering" and $filter_order_Dir=="asc"){
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, ($row->parent_id == @$rows[$i-1]->parent_id), 'orderupRelation', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, ($row->parent_id == @$rows[$i+1]->parent_id), 'orderdownRelation', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, ($row->parent_id == @$rows[$i-1]->parent_id), 'orderupRelation', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, ($row->parent_id == @$rows[$i+1]->parent_id), 'orderdownRelation', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, ($row->parent_id == @$rows[$i-1]->parent_id), 'orderdownRelation', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, ($row->parent_id == @$rows[$i+1]->parent_id), 'orderupRelation', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, ($row->parent_id == @$rows[$i-1]->parent_id), 'orderdownRelation', 'Move Down', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, ($row->parent_id == @$rows[$i+1]->parent_id), 'orderupRelation', 'Move Up', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->ordering;?>" class="text_area" style="text-align: center" />
	            </td>
	            <?php $link =  "index.php?option=$option&amp;task=editRelation&cid[]=$row->id";?>
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
				 <td><?php echo $row->parent_name; ?></td>
				 <td><?php echo $row->objecttypechild_name; ?></td>
				 <td><?php echo $row->classchild_name; ?></td>
				 <td><?php echo $row->attributechild_name; ?></td>
				 <td><?php echo $row->rendertype_name; ?></td>
				 <td><?php echo $row->relationtype_name; ?></td>
				<td> <?php echo JHTML::_('grid.published',$row,$i, 'tick.png', 'publish_x.png', 'relation_'); ?></td>
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
	  	<input type="hidden" name="task" value="listRelation" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
	  </form>
<?php
	}
	
	function newRelation(&$row, &$rowAttribute, $types, $type, $classes, $attributes, $objecttypes, $rendertypes, $relationtypes, $fieldsLength, $attributeFieldsLength, $boundsStyle, $style, $defaultStyle_textbox, $defaultStyle_textarea, $defaultStyle_Radio, $defaultStyle_Date, $defaultStyle_Locale_Textbox, $defaultStyle_Locale_Textarea, $defaultStyle_Choicelist, $renderStyle, $languages, $codevalues, $choicevalues, $selectedcodevalues, $profiles, $selected_profiles, $contexts, $selected_contexts, $attributetypes, $attributeid, $pageReloaded, $localeDefaults, $labels, $filterfields, $informations, $namespacelist, $searchCriteriaFieldsLength, $searchCriteria, $child_attributetype, $option)
	{
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		//print_r($attributetypes);
		
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150 ><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php if ($pageReloaded and array_key_exists('name', $_POST)) echo $_POST['name']; else echo $row->name;?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php if ($pageReloaded and array_key_exists('description', $_POST)) echo $_POST['description']; else echo $row->description;?></textarea></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_PUBLISHED"); ?></td>
					<?php if ($pageReloaded and array_key_exists('published', $_POST)) $published=$_POST['published']; else $published=$row->published;?>
					<td><?php echo JHTML::_('select.booleanlist', 'published', '', $published);?> </td>																
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PARENT"); ?></td>
					<?php if ($pageReloaded and array_key_exists('parent_id', $_POST)) $classid = $_POST['parent_id']; else $classid = $row->parent_id; ?>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'parent_id', 'size="1" class="inputbox"', 'value', 'text', $classid ); ?></td>							
				</tr>
				</table>
				<div id="div_bounds" style="<?php echo $boundsStyle; ?>">
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_LOWERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$lower=0;
						else
							$lower = $row->lowerbound;
					?>
					<td><input size="50" type="text" name ="lowerbound" value="<?php if ($pageReloaded and array_key_exists('lowerbound', $_POST)) echo $_POST['lowerbound']; else echo $lower;?>"> </td>							
				</tr>		
				<tr>
					<td><?php echo JText::_("CATALOG_UPPERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$upper=999;
						else
							$upper = $row->upperbound;
					?>
					<td><input size="50" type="text" name ="upperbound" value="<?php if ($pageReloaded and array_key_exists('upperbound', $_POST)) echo $_POST['upperbound']; else echo $upper;?>" onchange="javascript:submitbutton('newRelation');"> </td>							
				</tr>
				</table>
				</div>
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_PROFILE"); ?></td>
					<td>
						<?php
						if ($pageReloaded and array_key_exists('profiles', $_POST))
						{
							foreach($profiles as $profile)
							{
								?>
								<input size="50" type="checkbox" name ="profiles[]" value="<?php echo $profile->value?>" <?php echo in_array($profile->value, $_POST['profiles'])? 'checked="yes"':'';?>><?php echo $profile->text?></input>
								<?php
							}
						} 
						else
						{ 
							foreach($profiles as $profile)
							{
								?>
								<input size="50" type="checkbox" name ="profiles[]" value="<?php echo $profile->value?>" <?php echo in_array($profile->value, $selected_profiles)? 'checked="yes"':'';?>><?php echo $profile->text?></input>
								<?php
							} 
						}
						?>
					</td>
				</tr>
			</table>
			
				
				<table border="0" cellpadding="3" cellspacing="0">
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
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded and array_key_exists('label_'.$lang->code, $_POST)) echo $_POST['label_'.$lang->code]; else echo $labels[$lang->id]?>" maxlength="<?php echo $fieldsLength['label'];?>"></td>							
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
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_INFORMATION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="information<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded and array_key_exists('information_'.$lang->code, $_POST)) echo $_POST['information_'.$lang->code]; else echo $informations[$lang->id]?>" maxlength="<?php echo $fieldsLength['information'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td width=150><?php echo JText::_("CATALOG_RELATION_TYPECHILD_LABEL"); ?></td>
					<?php if ($pageReloaded and array_key_exists('type', $_POST)) $selectedType = $_POST['type']; else $selectedType = $type; ?>
					<td><?php echo JHTML::_("select.genericlist",$types, 'type', 'size="1" class="inputbox" onchange="javascript:submitbutton(\'newRelation\');"', 'value', 'text', $selectedType ); ?></td>
				</tr>
			</table>
			<br/><br/>
<!-- Partie li�e � une relation vers un attribut -->
<?php
if ($type == 2)
{ 
?>
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150><?php echo JText::_("CATALOG_ATTRIBUTECHILD"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$attributes, 'attributechild_id', 'size="1" class="inputbox" onchange="javascript:submitbutton(\'newRelation\');"', 'value', 'text', $attributeid ); // javascript:changeVisibility(this.value); javascript:updatelist(this.value)?></td>							
				</tr>	
			</table>
			<div id="div_render" style="<?php echo $renderStyle; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CATALOG_RENDERTYPE"); ?></td>
					<?php if ($pageReloaded and array_key_exists('rendertype_id', $_POST)) $selectedRendertype = $_POST['rendertype_id']; else $selectedRendertype = $row->rendertype_id; ?>
					<td><?php echo JHTML::_("select.genericlist",$rendertypes, 'rendertype_id', 'size="1" class="inputbox" onchange="javascript:changeDefaultField(this.value);"', 'value', 'text', $selectedRendertype ); ?></td>							
				</tr>	
			</table>
			</div>
			<div id = "div_defaultVal_textbox" style="<?php echo $defaultStyle_textbox; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><input size="50" type="text" name ="default_tb" value="<?php echo $rowAttribute->default?>" maxlength="<?php echo $fieldsLength['defaultvalue'];?>"> </td>							
			</tr>
			</table>
			</div>
			<div id = "div_defaultVal_textarea" style="<?php echo $defaultStyle_textarea; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><textarea rows="4" cols="50" name ="default_ta" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['defaultvalue'];?>);"><?php echo $rowAttribute->default?></textarea></td>							
			</tr>
			</table>
			</div>

			<div id = "div_defaultVal_list" style="<?php echo $style; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<?php
				
				if ($pageReloaded and array_key_exists('upperbound', $_POST))
					$upper =$_POST['upperbound'];
				else
					$upper = $row->upperbound;  
				if ($upper <= 1)
				{
				?>
				<td><?php echo JHTML::_("select.genericlist",$codevalues, 'defaultList[]', 'size="1" class="inputbox"', 'value', 'text', $selectedcodevalues ); ?></td>
				<?php 
				}
				else
				{
				?>
				<td><?php echo JHTML::_("select.genericlist",$codevalues, 'defaultList[]', 'size="5" class="inputbox" multiple="multiple"', 'value', 'text', $selectedcodevalues ); ?></td>
				<?php 
				}
				?>							
			</tr>
			</table>
			</div>
			
			<div id = "div_defaultVal_choicelist" style="<?php echo $defaultStyle_Choicelist; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><?php echo JHTML::_("select.genericlist",$choicevalues, 'defaultChoice[]', 'size="1" class="inputbox"', 'value', 'text'); ?></td>							
			</tr>
			</table>
			</div>
			
			<div id = "div_defaultVal_radio" style="<?php echo $defaultStyle_Radio; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150 valign="top"><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><?php echo JHTML::_("select.booleanlist", 'defaultDate_Radio', 'onclick="javascript:changeDateVisibility(this.value);"', ($rowAttribute->default == "today")? false: true, 'Date fixe', 'Date du jour' ); ?>
					<div id = "div_defaultDate" style="<?php echo $defaultStyle_Date; ?>">
					<?php echo JHTML::_("calendar", $rowAttribute->default, 'defaultDate', 'defaultDate', '%d.%m.%Y'); ?>
					</div>
				</td>							
			</tr>
			</table>
			</div>
			
			<div id = "div_defaultVal_locale_textbox" style="<?php echo $defaultStyle_Locale_Textbox; ?>">
			<table border="0" cellpadding="3" cellspacing="0" width=540>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CORE_DEFAULT"); ?></legend>
							<table>
<?php
if ($rowAttribute->attributetype_id == 3)
foreach ($languages as $lang)
{
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="default_tb<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded and array_key_exists('default_'.$lang->code, $_POST)) echo $_POST['default_'.$lang->code]; else echo $localeDefaults[$lang->id]?>" maxlength="<?php echo $fieldsLength['defaultvalue'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				</table>
			</div>
			<div id = "div_defaultVal_locale_textarea" style="<?php echo $defaultStyle_Locale_Textarea; ?>">
			<table border="0" cellpadding="3" cellspacing="0" width=540>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CORE_DEFAULT"); ?></legend>
							<table>
<?php
if ($rowAttribute->attributetype_id == 3)
foreach ($languages as $lang)
{ 	
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><textarea rows="4" cols="50" name ="default_ta<?php echo "_".$lang->code;?>" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['defaultvalue'];?>);"><?php if ($pageReloaded and array_key_exists('default_'.$lang->code, $_POST)) echo $_POST['default_'.$lang->code]; else echo $localeDefaults[$lang->id]?></textarea></td>
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				</table>
			</div>

			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_RELATION_ISSEARCHFITLER"); ?></td>
					<td><?php echo JHTML::_('select.booleanlist', 'issearchfilter', 'onclick="javascript:changeContextsVisibility(this.value);"', $row->issearchfilter);?> </td>																
				</tr>
				<tr>
					<td colspan="2">
						<div id="div_contexts" style="<?php echo ($row->issearchfilter)? "display:inline":"display:none"; ?>">
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
									<td width=150 ><?php echo JText::_("CATALOG_RELATION_CONTEXT"); ?></td>
									<td>
										<?php
										if (count($contexts) > 0)
										{
											foreach($contexts as $context)
											{
												?>
													<input size="50" type="checkbox" name ="contexts[]" value="<?php echo $context->value?>" <?php echo in_array($context->value, $selected_contexts)? 'checked="yes"':'';?>><?php echo $context->text?></input>
												<?php
											} 
										}
										else
										{
											?>
											<div>
											<?php
											echo JText::_( 'CATALOG_RELATION_NOCONTEXT' );
											?>
											</div>
											<?php
										}
										?>
									</td>
								</tr>
							</table>
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
								<td colspan="2">
									<fieldset id="filterfields">
										<legend align="top"><?php echo JText::_("CATALOG_CONTEXT_FILTERFIELD"); ?></legend>
										<table>
			<?php
			foreach ($languages as $lang)
			{ 
			?>
								<tr>
								<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
								<td><input size="50" type="text" name ="filterfield<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded and array_key_exists('filterfield_'.$lang->code, $_POST)) echo $_POST['filterfield_'.$lang->code]; else echo "";?>" maxlength="<?php echo $searchCriteriaFieldsLength['ogcsearchfilter'];?>"></td>							
								</tr>
			<?php
			}
			?>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
				<!-- <tr>
					<td><?php echo JText::_("CATALOG_RELATION_OGCSEARCHFILTER"); ?></td>
					<td><input size="50" type="text" name ="ogcsearchfilter" value="<?php //if ($pageReloaded and array_key_exists('ogcsearchfilter', $_POST)) echo $_POST['ogcsearchfilter']; else echo $searchCriteria->ogcsearchfilter?>" maxlength="<?php //echo $searchCriteriaFieldsLength['ogcsearchfilter'];?>"></td>							
				</tr>
				 -->
			</table>
			
<?php 
}
else if ($type == 1)
{
?>
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td><?php echo JText::_("CATALOG_CLASSCHILD"); ?></td>
					<?php if ($pageReloaded and array_key_exists('classchild_id', $_POST)) $selectedClasschild=$_POST['classchild_id']; else $selectedClasschild=$row->classchild_id;?>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'classchild_id', 'size="1" class="inputbox"', 'value', 'text', $selectedClasschild ); ?></td>							
				</tr>
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_ISOCODE"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'namespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->namespace_id ); ?>
						<input size="50" type="text" name ="isocode" value="<?php if ($pageReloaded and array_key_exists('isocode', $_POST)) echo $_POST['isocode']; else echo $row->isocode;?>" maxlength="<?php echo $fieldsLength['isocode'];?>"> 
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_RELATIONTYPE"); ?></td>
					<?php if ($pageReloaded and array_key_exists('relationtype_id', $_POST)) $selectedRelationtype=$_POST['relationtype_id']; else $selectedRelationtype=$row->relationtype_id;?>
					<td><?php echo JHTML::_("select.genericlist",$relationtypes, 'relationtype_id', 'size="1" class="inputbox" onchange="javascript:changeBoundsVisibility(this.value);"', 'value', 'text', $selectedRelationtype ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASSASSOCIATION"); ?></td>
					<?php if ($pageReloaded and array_key_exists('classassociation_id', $_POST)) $selectedClassassociation=$_POST['classassociation_id']; else $selectedClassassociation=$row->classassociation_id;?>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'classassociation_id', 'size="1" class="inputbox"', 'value', 'text', $selectedClassassociation ); ?></td>							
				</tr>
				</table>
<?php 
}
	else if ($type == 3) // Type d'enfant = type d'objet
{
?>
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPECHILD"); ?></td>
					<?php if ($pageReloaded and array_key_exists('objecttypechild_id', $_POST)) $selectedObjecttypechild=$_POST['objectchild_id']; else $selectedObjecttypechild=$row->objecttypechild_id;?>
					<td><?php echo JHTML::_("select.genericlist",$objecttypes, 'objecttypechild_id', 'size="1" class="inputbox"', 'value', 'text', $selectedObjecttypechild ); ?></td>							
				</tr>
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_ISOCODE"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'namespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->namespace_id ); ?>
						<input size="50" type="text" name ="isocode" value="<?php if ($pageReloaded and array_key_exists('isocode', $_POST)) echo $_POST['isocode']; else echo $row->isocode;?>" maxlength="<?php echo $fieldsLength['isocode'];?>"> 
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_RELATIONTYPE"); ?></td>
					<?php if ($pageReloaded and array_key_exists('relationtype_id', $_POST)) $selectedRelationtype=$_POST['relationtype_id']; else $selectedRelationtype=$row->relationtype_id;?>
					<td><?php echo JHTML::_("select.genericlist",$relationtypes, 'relationtype_id', 'size="1" class="inputbox" onchange="javascript:changeBoundsVisibility(this.value);"', 'value', 'text', $selectedRelationtype ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASSASSOCIATION"); ?></td>
					<?php if ($pageReloaded and array_key_exists('classassociation_id', $_POST)) $selectedClassassociation=$_POST['classassociation_id']; else $selectedClassassociation=$row->classassociation_id;?>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'classassociation_id', 'size="1" class="inputbox"', 'value', 'text', $selectedClassassociation ); ?></td>							
				</tr>
				</table>
<?php 
}
?>
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
if ($row->updated and $row->updated <> '0000-00-00 00:00:00')
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
			<!-- <input type="hidden" name="attributetypes" value="<?php //print_r($attributetypes); ?>" /> -->
			<?php echo JHTML::_("select.genericlist",$attributetypes, 'attributetypes', 'size="1" class="inputbox" style="display:none"', 'value', 'text', $attributeid); ?>
			<div id="txtHint"></div>
			<input type="hidden" name="reload" value="<?php echo true;?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="newRelation" />
			<input type="hidden" name="child_attributetype" value="<?php echo $child_attributetype?>" />
		</form>
			<?php 	
	}

	function editAttributeRelation(&$row, &$rowAttribute, $classes, $attributes, $rendertypes, $fieldsLength, $attributeFieldsLength, $style, $style_choice, $defaultStyle_textbox, $defaultStyle_textarea, $defaultStyle_Radio, $defaultStyle_Date, $defaultStyle_Locale_Textbox, $defaultStyle_Locale_Textarea, $languages, $codevalues, $selectedcodevalues, $choicevalues, $selectedchoicevalues, $profiles, $selected_profiles, $contexts, $selected_contexts, $attributetypes, $attributeid, $pageReloaded, $localeDefaults, $labels, $filterfields, $informations, $searchCriteriaFieldsLength, $searchCriteria, $boundsStyle, $renderStyle, $child_attributetype, $option)
	{
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		//print_r($attributetypes);
		$tabs =& JPANE::getInstance('Tabs');
		
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
		<?php
		echo $tabs->startPane("generalPane");
		echo $tabs->startPanel(JText::_("CATALOG_RELATION_TAB_GENERAL_LABEL"),"generalPane");
		?>
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td width=150 ><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php if ($pageReloaded) echo $_POST['name']; else echo $row->name;?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
					<td><?php echo JHTML::_('select.booleanlist', 'published', '', $row->published);?> </td>																
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASS"); ?></td>
					<?php if ($pageReloaded) $classid = $_POST['parent_id']; else $classid = $row->parent_id; ?>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'parent_id', 'size="1" class="inputbox"', 'value', 'text', $classid ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_ATTRIBUTECHILD"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$attributes, 'attributechild_id', 'size="1" class="inputbox" onchange="javascript:submitbutton(\'editRelation\');"', 'value', 'text', $attributeid ); // javascript:changeVisibility(this.value); javascript:updatelist(this.value)?></td>							
				</tr>
				</table>
				<div id="div_bounds" style="<?php echo $boundsStyle; ?>">
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_LOWERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$lower=0;
						else
							$lower = $row->lowerbound;
					?>
					<td><input size="50" type="text" name ="lowerbound" value="<?php if ($pageReloaded) echo $_POST['lowerbound']; else echo $lower;?>"> </td>							
				</tr>		
				<tr>
					<td><?php echo JText::_("CATALOG_UPPERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$upper=999;
						else
							$upper = $row->upperbound;
					?>
					<td><input size="50" type="text" name ="upperbound" value="<?php if ($pageReloaded) echo $_POST['upperbound']; else echo $upper;?>" onchange="javascript:submitbutton('editRelation');"> </td>							
				</tr>
				</table>
				</div>
				<div id="div_render" style="<?php echo $renderStyle; ?>">
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_RENDERTYPE"); ?></td>
					<?php 
					//$selectedRendertype = 0;
					//if (!$pageReloaded) 
						$selectedRendertype = $row->rendertype_id;
					?>
					<td><?php echo JHTML::_("select.genericlist",$rendertypes, 'rendertype_id', 'size="1" class="inputbox" onchange="javascript:changeDefaultField(this.value);"', 'value', 'text', $selectedRendertype ); ?></td>							
				</tr>
				</table>
				</div>
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td><?php echo JText::_("CATALOG_PROFILE"); ?></td>
					<td>
						<?php
						if ($pageReloaded)
						{
							foreach($profiles as $profile)
							{
								?>
								<input size="50" type="checkbox" name ="profiles[]" value="<?php echo $profile->value?>" <?php echo in_array($profile->value, $_POST['profiles'])? 'checked="yes"':'';?>><?php echo $profile->text?></input>
								<?php
							}
						} 
						else
						{ 
							foreach($profiles as $profile)
							{
								?>
								<input size="50" type="checkbox" name ="profiles[]" value="<?php echo $profile->value?>" <?php echo in_array($profile->value, $selected_profiles)? 'checked="yes"':'';?>><?php echo $profile->text?></input>
								<?php
							} 
						}
						?>
					</td>
				</tr>	
			</table>
			<div id = "div_defaultVal_textbox" style="<?php echo $defaultStyle_textbox; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><input size="50" type="text" name ="default_tb" value="<?php echo $rowAttribute->default?>" maxlength="<?php echo $fieldsLength['defaultvalue'];?>"> </td>							
			</tr>
			</table>
			</div>
			<div id = "div_defaultVal_textarea" style="<?php echo $defaultStyle_textarea; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><textarea rows="4" cols="50" name ="default_ta" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['defaultvalue'];?>);"><?php echo $rowAttribute->default?></textarea></td>							
			</tr>
			</table>
			</div>

			<div id = "div_defaultVal_list" style="<?php echo $style; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<?php
				
				if ($pageReloaded and array_key_exists('upperbound', $_POST))
					$upper =$_POST['upperbound'];
				else
					$upper = $row->upperbound;  
				if ($upper <= 1)
				{
				?>
				<td><?php echo JHTML::_("select.genericlist",$codevalues, 'defaultList[]', 'size="1" class="inputbox" style="width:380px" ', 'value', 'text', $selectedcodevalues ); ?></td>
				<?php 
				}
				else
				{
				?>
				<td><?php echo JHTML::_("select.genericlist",$codevalues, 'defaultList[]', 'size="5" class="inputbox" style="width:380px" multiple="multiple"', 'value', 'text', $selectedcodevalues ); ?></td>
				<?php 
				}
				?>							
			</tr>
			</table>
			</div>
			
			<div id = "div_defaultVal_choice" style="<?php echo $style_choice; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><?php echo JHTML::_("select.genericlist",$choicevalues, 'defaultChoice[]', 'size="1" class="inputbox" style="width:380px" ', 'value', 'text', $selectedchoicevalues ); ?></td>							
			</tr>
			</table>
			</div>
			
			<div id = "div_defaultVal_radio" style="<?php echo $defaultStyle_Radio; ?>">
			<table border="0" cellpadding="3" cellspacing="0">
			<tr>
				<td WIDTH=150 valign="top"><?php echo JText::_("CORE_DEFAULT"); ?></td>
				<td><?php echo JHTML::_("select.booleanlist", 'defaultDate_Radio', 'onclick="javascript:changeDateVisibility(this.value);"', ($rowAttribute->default == "today")? false: true, 'Date fixe', 'Date du jour' ); ?>
					<div id = "div_defaultDate" style="<?php echo $defaultStyle_Date; ?>">
					<?php echo JHTML::_("calendar", $rowAttribute->default, 'defaultDate', 'defaultDate', '%d.%m.%Y'); ?>
					</div>
				</td>							
			</tr>
			</table>
			</div>
			
			<div id = "div_defaultVal_locale_textbox" style="<?php echo $defaultStyle_Locale_Textbox; ?>">
			<table border="0" cellpadding="3" cellspacing="0" width=540>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CORE_DEFAULT"); ?></legend>
							<table>
<?php
if ($rowAttribute->attributetype_id == 3)
foreach ($languages as $lang)
{
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="default_tb<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded and array_key_exists('default_'.$lang->code, $_POST)) echo htmlspecialchars($_POST['default_'.$lang->code]); else echo htmlspecialchars($localeDefaults[$lang->id])?>" maxlength="<?php echo $fieldsLength['defaultvalue'];?>"></td>							
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				</table>
			</div>
			<div id = "div_defaultVal_locale_textarea" style="<?php echo $defaultStyle_Locale_Textarea; ?>">
			<table border="0" cellpadding="3" cellspacing="0" width=540>
				<tr>
					<td colspan="2">
						<fieldset>
							<legend align="top"><?php echo JText::_("CORE_DEFAULT"); ?></legend>
							<table>
<?php
if ($rowAttribute->attributetype_id == 3)
foreach ($languages as $lang)
{ 	
?>
					<tr>
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><textarea rows="4" cols="50" name ="default_ta<?php echo "_".$lang->code;?>" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['defaultvalue'];?>);"><?php if ($pageReloaded and array_key_exists('default_'.$lang->code, $_POST)) echo htmlspecialchars($_POST['default_'.$lang->code]); else echo htmlspecialchars($localeDefaults[$lang->id])?></textarea></td>
					</tr>
<?php
}
?>
							</table>
						</fieldset>
					</td>
				</tr>
				</table>
			</div>
				
				<table width=540>
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
					<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="label<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded) echo htmlspecialchars($_POST['label_'.$lang->code]); else echo htmlspecialchars($labels[$lang->id])?>" maxlength="<?php echo $attributeFieldsLength['label'];?>"></td>							
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
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_INFORMATION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="information<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($informations[$lang->id])?>" maxlength="<?php echo $fieldsLength['information'];?>"></td>							
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
if ($row->updated and $row->updated <> '0000-00-00 00:00:00')
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
			 
			<?php
		echo $tabs->endPanel();
		echo $tabs->startPanel(JText::_("CATALOG_RELATION_TAB_SEARCH_LABEL"),"generalPane");
		?>
			<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td width=150 ><?php echo JText::_("CATALOG_RELATION_ISSEARCHFITLER"); ?></td>
					<td><?php echo JHTML::_('select.booleanlist', 'issearchfilter', 'onclick="javascript:changeContextsVisibility(this.value);"', $row->issearchfilter);?> </td>																
				</tr>
				<tr>
					<td colspan="2">
						<div id="div_contexts" style="<?php echo ($row->issearchfilter)? "display:inline":"display:none"; ?>">
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
									<td width=150 ><?php echo JText::_("CATALOG_RELATION_CONTEXT"); ?></td>
									<td>
										<?php
										if (count($contexts) > 0)
										{
											foreach($contexts as $context)
											{
												?>
													<input size="50" type="checkbox" name ="contexts[]" value="<?php echo $context->value?>" <?php echo in_array($context->value, $selected_contexts)? 'checked="yes"':'';?>><?php echo $context->text?></input>
												<?php
											} 
										}
										else
										{
											?>
											<div>
											<?php
											echo JText::_( 'CATALOG_RELATION_NOCONTEXT' );
											?>
											</div>
											<?php
										}
										?>
									</td>
								</tr>
								<!-- 
								<tr>
									<td><?php //echo JText::_("CATALOG_RELATION_OGCSEARCHFILTER"); ?></td>
									<td><input size="50" type="text" name ="ogcsearchfilter" value="<?php //if ($pageReloaded and array_key_exists('ogcsearchfilter', $_POST)) echo $_POST['ogcsearchfilter']; else echo $searchCriteria->ogcsearchfilter?>" maxlength="<?php //echo $searchCriteriaFieldsLength['ogcsearchfilter'];?>"></td>							
								</tr>
								 -->
							</table>
							<table border="0" cellpadding="3" cellspacing="0">
								<tr>
								<td colspan="2">
									<fieldset id="filterfields">
										<legend align="top"><?php echo JText::_("CATALOG_CONTEXT_FILTERFIELD"); ?></legend>
										<table>
			<?php
			
			foreach ($languages as $lang)
			{ 
			?>
								<tr>
								<td WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
								<td><input size="50" type="text" name ="filterfield<?php echo "_".$lang->code;?>" value="<?php if ($pageReloaded and array_key_exists('filterfield_'.$lang->code, $_POST)) echo htmlspecialchars($_POST['filterfield_'.$lang->code]); else echo htmlspecialchars($filterfields[$lang->id])?>" maxlength="<?php echo $searchCriteriaFieldsLength['ogcsearchfilter'];?>"></td>							
								</tr>
			<?php
			}
			?>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
			</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
		?> 
			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			<!-- <input type="hidden" name="attributetypes" value="<?php //print_r($attributetypes); ?>" /> -->
			<?php echo JHTML::_("select.genericlist",$attributetypes, 'attributetypes', 'size="1" class="inputbox" style="display:none"', 'value', 'text', $attributeid); ?>
			<div id="txtHint"></div>
			<input type="hidden" name="type" value='2' />
			<input type="hidden" name="child_attributetype" value="<?php echo $child_attributetype?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
	
	function editClassRelation(&$row, $classes, $relationtypes, $fieldsLength, $boundsStyle, $profiles, $selected_profiles, $contexts, $selected_contexts, $languages, $labels, $informations, $namespacelist, $option)
	{
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
					<td><?php echo JHTML::_('select.booleanlist', 'published', '', $row->published);?> </td>																
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PARENT"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'parent_id', 'size="1" class="inputbox"', 'value', 'text', $row->parent_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASSCHILD"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'classchild_id', 'size="1" class="inputbox"', 'value', 'text', $row->classchild_id ); ?></td>							
				</tr>
				</table>
				<div id="div_bounds" style="<?php echo $boundsStyle; ?>">
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CATALOG_LOWERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$lower=0;
						else
							$lower = $row->lowerbound;
					?>
					<td><input size="50" type="text" name ="lowerbound" value="<?php echo $lower;?>"> </td>							
				</tr>		
				<tr>
					<td><?php echo JText::_("CATALOG_UPPERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$upper=999;
						else
							$upper = $row->upperbound;
					?>
					<td><input size="50" type="text" name ="upperbound" value="<?php echo $upper;?>"> </td>							
				</tr>
				</table>
				</div>
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_ISOCODE"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'namespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->namespace_id ); ?>
						<input size="50" type="text" name ="isocode" value="<?php echo $row->isocode?>" maxlength="<?php echo $fieldsLength['isocode'];?>">
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_RELATIONTYPE"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$relationtypes, 'relationtype_id', 'size="1" class="inputbox" onchange="javascript:changeBoundsVisibility(this.value);"', 'value', 'text', $row->relationtype_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASSASSOCIATION"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'classassociation_id', 'size="1" class="inputbox"', 'value', 'text', $row->classassociation_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PROFILE"); ?></td>
					<td>
						<?php
						foreach($profiles as $profile)
						{
							?>
							<input size="50" type="checkbox" name ="profiles[]" value="<?php echo $profile->value?>" <?php echo in_array($profile->value, $selected_profiles)? 'checked="yes"':'';?>><?php echo $profile->text?></input>
							<?php
						} 
						?>
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
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
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
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_INFORMATION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="information<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($informations[$lang->id])?>" maxlength="<?php echo $fieldsLength['information'];?>"></td>							
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
if ($row->updated and $row->updated <> '0000-00-00 00:00:00')
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
			<input type="hidden" name="guid" value="<?php echo $row->guid; ?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			
			<input type="hidden" name="type" value='1' />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
	
	function editObjectRelation(&$row, $classes, $objecttypes, $relationtypes, $fieldsLength, $boundsStyle, $profiles, $selected_profiles, $contexts, $selected_contexts, $languages, $labels, $informations, $namespacelist, $option)
	{
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table border="0" cellpadding="3" cellspacing="0">	
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td><input size="50" type="text" name ="name" value="<?php echo $row->name?>" maxlength="<?php echo $fieldsLength['name'];?>"> </td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CORE_PUBLISHED"); ?> : </td>
					<td><?php echo JHTML::_('select.booleanlist', 'published', '', $row->published);?> </td>																
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PARENT"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'parent_id', 'size="1" class="inputbox"', 'value', 'text', $row->parent_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_OBJECTTYPECHILD"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$objecttypes, 'objecttypechild_id', 'size="1" class="inputbox"', 'value', 'text', $row->objecttypechild_id ); ?></td>							
				</tr>
				</table>
				<div id="div_bounds" style="<?php echo $boundsStyle; ?>">
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CATALOG_LOWERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$lower=0;
						else
							$lower = $row->lowerbound;
					?>
					<td><input size="50" type="text" name ="lowerbound" value="<?php echo $lower;?>"> </td>							
				</tr>		
				<tr>
					<td><?php echo JText::_("CATALOG_UPPERBOUND"); ?></td>
					<?php
						if ($row->id == 0)
							$upper=999;
						else
							$upper = $row->upperbound;
					?>
					<td><input size="50" type="text" name ="upperbound" value="<?php echo $upper;?>"> </td>							
				</tr>
				</table>
				</div>
				<table border="0" cellpadding="3" cellspacing="0">
				<tr>
					<td WIDTH=150><?php echo JText::_("CORE_ISOCODE"); ?></td>
					<td>
						<?php echo JHTML::_("select.genericlist",$namespacelist, 'namespace_id', 'size="1" class="inputbox"', 'value', 'text', $row->namespace_id ); ?>
						<input size="50" type="text" name ="isocode" value="<?php echo $row->isocode?>" maxlength="<?php echo $fieldsLength['isocode'];?>"> 
					</td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_RELATIONTYPE"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$relationtypes, 'relationtype_id', 'size="1" class="inputbox" onchange="javascript:changeBoundsVisibility(this.value);"', 'value', 'text', $row->relationtype_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_CLASSASSOCIATION"); ?></td>
					<td><?php echo JHTML::_("select.genericlist",$classes, 'classassociation_id', 'size="1" class="inputbox"', 'value', 'text', $row->classassociation_id ); ?></td>							
				</tr>
				<tr>
					<td><?php echo JText::_("CATALOG_PROFILE"); ?></td>
					<td>
						<?php
						foreach($profiles as $profile)
						{
							?>
							<input size="50" type="checkbox" name ="profiles[]" value="<?php echo $profile->value?>" <?php echo in_array($profile->value, $selected_profiles)? 'checked="yes"':'';?>><?php echo $profile->text?></input>
							<?php
						} 
						?>
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
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
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
						<fieldset>
							<legend align="top"><?php echo JText::_("CATALOG_INFORMATION"); ?></legend>
							<table>
<?php
foreach ($languages as $lang)
{ 
?>
					<tr>
					<td width=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="information<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($informations[$lang->id])?>" maxlength="<?php echo $fieldsLength['information'];?>"></td>							
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
if ($row->updated and $row->updated <> '0000-00-00 00:00:00')
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
			<input type="hidden" name="guid" value="<?php echo $row->guid; ?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			
			<input type="hidden" name="type" value='3' />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
			<?php 	
	}
}
?>