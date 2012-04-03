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


class HTML_searchcriteria {
	function listSearchCriteria(&$rows, $page, $filter_order_Dir, $filter_order, $context_id, $option)
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
				<?php echo JHTML::_('grid.order',  $rows, 'filesave.png', 'saveOrderSearchCriteria' ); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_NAME"), 'name', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_CRITERIATYPE"), 'criteriatype_label', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_SEARCHCRITERIA_TAB"), 'tab_label', @$filter_order_Dir, @$filter_order); ?></th>
				<th class='title' width="100px"><?php echo JHTML::_('grid.sort',   JText::_("CORE_UPDATED"), 'updated', @$filter_order_Dir, @$filter_order); ?></th>
			</tr>
		</thead>
		<tbody>
					
<?php
		$i=0;
		foreach ($rows as $row)
		{	
			// Name
			$name = $row->name;
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
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', 'Move Up', isset($rows[$i-1]) ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}		
					}
					else{ 
						if ($disabled){
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', '', false ); ?>
				             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', '', false ); ?>
		            <?php
						}
						else {
					?>
							 <?php echo $page->orderUpIcon($i, true, 'orderupSearchCriteria', 'Move Up', isset($rows[$i-1]) ); ?>
		 		             <?php echo $page->orderDownIcon($i, count($rows)-1, true, 'orderdownSearchCriteria', 'Move Down', isset($rows[$i+1]) ); ?>
					<?php
						}
					}?>
					<input type="text" id="or<?php echo $i;?>" name="ordering[]" size="5" <?php echo $disabled; ?> value="<?php echo $row->cc_ordering;?>" class="text_area" style="text-align: center" />
	            </td>
				<?php $link =  "index.php?option=$option&amp;task=editSearchCriteria&context_id=$context_id&cid[]=$row->id";?>
				<td><a href="<?php echo $link;?>"><?php echo $name; ?></a></td>
				<td align="center"><?php echo JText::_($row->criteriatype_label);?></td>
				<?php $tab 	= ADMIN_searchcriteria::tab($row, $i);?>
				<td width="100px" align="center">
					<?php echo $tab;?>
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
				<td colspan="8"><?php echo $page->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="option" value="<?php echo $option; ?>" />
	  	<input type="hidden" name="task" value="listSearchCriteria" />
	  	<input type="hidden" name="boxchecked" value="0" />
	  	<input type="hidden" name="hidemainmenu" value="0">
	  	<input type="hidden" name="context_id" value="<?php echo $context_id; ?>" />
	  	<input type="hidden" name="filter_order_Dir" value="<?php echo $filter_order_Dir; ?>" />
	  	<input type="hidden" name="filter_order" value="<?php echo $filter_order; ?>" />
</form>


<?php
	}
	
	function editSystemSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $context_id, $tabList, $tab_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<table class="admintable"  border="0" cellpadding="3" cellspacing="0">	
				<tr >
					<td  class="key" width=150><?php echo JText::_("CORE_NAME"); ?></td>
					<td width=150><?php echo $row->name; ?></td>							
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("CORE_DESCRIPTION"); ?></td>
					<td><textarea rows="4" cols="50" name ="description" onkeypress="javascript:maxlength(this,<?php echo $fieldsLength['description'];?>);"><?php echo $row->description?></textarea></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
			<td><?php echo JHTML::_('select.genericlist', $tabList, 'tabList', 'class="list"', 'value', 'text', $tab_id);?></td>
		</tr>
	
		<?php 
		$listMaxLength = config_easysdi::getValue("CATALOG_SEARCH_MULTILIST_LENGTH");
		switch ($row->code)
		{
			case "objecttype":
				$selectedObjectType = json_decode($row->defaultvalue, true);
				
				// Types d'objet du contexte
				$objecttypes = array();
				$database->setQuery("SELECT ot.id AS value, t.label as text
							 FROM #__sdi_objecttype ot 
							 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
							 INNER JOIN #__sdi_language l ON t.language_id=l.id
							 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
							 WHERE ot.predefined=false 
							 	   AND cl.code='".$language->_lang."'
							 	   AND ot.id IN 
							 	   				(SELECT co.objecttype_id 
												FROM #__sdi_context_objecttype co
												INNER JOIN #__sdi_context c ON c.id=co.context_id 
												WHERE c.id = ".$context_id.")
							 ORDER BY ot.ordering");

				$objecttypes = array_merge( $objecttypes, $database->loadObjectList() );
				?>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td><?php echo helper_easysdi::checkboxlist($objecttypes, 'defaultvalue[]', 'size="1" class="inputbox checkbox" ', 'class="inputbox checkbox"', 'value', 'text', $selectedObjectType); ?></td>
				</tr>
				<?php
				break;
			case "definedBoundary":	
				$boundaries = array();
				$database->setQuery( "SELECT name, guid FROM #__sdi_boundary") ;
				$boundaries = $database->loadObjectList() ;
				?>
				<tr >
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
						<select name="defaultvalue" id="defaultvalue">
							<option value="" <?php if($selectedValue ==""){?> selected="selected" <?php }?>></option>
							<?php 
								foreach ($boundaries as $boundary){
						    ?> 
						    	<option value="<?php echo JText::_($boundary->guid);?>" <?php if($row->defaultvalue == trim($boundary->guid)){?> selected="selected" <?Php }?> ><?php echo JText::_($boundary->name);?></option>
						   <?php }?>
						</select>
					</td>
				</tr>		
				<?php 
				break;
			case "title":
			case "object_name":
			case "fulltext":
				?>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
					<input type="text" id="defaultvalue" name="defaultvalue" value="<?php echo $row->defaultvalue;?>"/>
					</td>
				</tr>
				<?php
				break;
			case "versions":
				// Choix radio pour les versions
				$versions = array(
				JHTML::_('select.option',  '0', JText::_( 'CATALOG_SEARCH_VERSIONS_CURRENT' ) ),
				JHTML::_('select.option',  '1', JText::_( 'CATALOG_SEARCH_VERSIONS_ALL' ) )
				);
				?>	
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
					<?php echo helper_easysdi::radiolist($versions, 'defaultvalue', 'class="checkbox"', 'class="checkbox"', 'value', 'text', $row->defaultvalue); ?>
					</td>
				</tr>
				<?php
				break;
			case "account_id":
				/* Only accounts that have at least one md */
				/* Fonctionnement liste*/
				$accounts = array();
				$accounts[] = JHTML::_('select.option', '', '');
				$query = "SELECT DISTINCT a.id as value, a.name as text 
						  FROM #__sdi_account a, #__sdi_object o, #__sdi_objectversion ov, #__users u 
						  WHERE u.id=a.user_id AND a.id=o.account_id AND o.id=ov.object_id AND a.root_id IS NULL 
						  ORDER BY a.name
						  ";
				$database->setQuery( $query);
				$accounts = array_merge( $accounts, $database->loadObjectList() );
			
				$size=(int)$listMaxLength;
				if (count($accounts) < (int)$listMaxLength)
					$size = count($accounts);					
				
				$multiple = 'size="1"';
				
				if (count(json_decode($row->defaultvalue,false)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
						<?php echo JHTML::_("select.genericlist", $accounts, 'defaultvalue[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', json_decode($row->defaultvalue,false)); ?>
						<a onclick="javascript:toggle_multi_select('defaultvalue', <?php echo $size;?>); return false;" href="#">
							<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
						</a>
					</td>
				</tr>
				<?php
				break;
			case "managers":
				/* Fonctionnement liste*/
				$managers = array();
				$managers[] = JHTML::_('select.option', '', '');
				$query = "SELECT DISTINCT ma.account_id as value, a.name as text
													  FROM #__sdi_manager_object ma 
													  INNER JOIN #__sdi_account a ON a.id=ma.account_id 
													  INNER JOIN #__users u ON u.id=a.user_id 
													  ";
				$db->setQuery( $query);
				$managers = array_merge( $managers, $database->loadObjectList() );
				
				$size=(int)$listMaxLength;
				if (count($managers) < (int)$listMaxLength)
				$size = count($managers);
					
				$multiple = 'size="1"';
				if (count(json_decode($row->defaultvalue,false)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
					
				?>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
					<?php echo JHTML::_("select.genericlist", $managers, 'defaultvalue[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text',json_decode($row->defaultvalue,false)); ?>
					<a onclick="javascript:toggle_multi_select('<?php echo 'systemfilter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
						<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
					</a>
					</td>
				</tr>
				<?php
				break;
			case "metadata_created":
			case "metadata_published":
				/* Fonctionnement période*/
				?>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
					<label class="checkbox" for="from"><?php echo JText::_("CORE_DATE_FROM");?></label>
					<?php echo helper_easysdi::calendar(($row->defaultvaluefrom == '0000-00-00')? null : $row->defaultvaluefrom , 'defaultvaluefrom','defaultvaluefrom',"%Y.%m.%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', JURI::base().'components/com_easysdi_catalog/templates/images/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					<label class="checkbox" for="to"><?php echo JText::_("CORE_DATE_TO");?></label>
					<?php echo helper_easysdi::calendar(($row->defaultvalueto == '0000-00-00')? null : $row->defaultvalueto , 'defaultvalueto','defaultvalueto',"%Y.%m.%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', JURI::base().'components/com_easysdi_catalog/templates/images/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</td>
				</tr>
				<?php
				break;
			case "isFree":
			case "isOrderable":
			case "isDownloadable":
				?>
				<tr>
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE");?></td>
					<td>
					<input type="checkbox" id="defaultvalue" name="defaultvalue" value="1" class="inputbox checkbox" <?php if( $row->defaultvalue==1){ echo "checked = true";} ?>	 />
					</td>
				</tr>
				<?php		
				break;
			default:
				break;
		}
		
			?>
			</table>
			<table class="admintable" border="0" cellpadding="3" cellspacing="0">
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
					<td  class="key"  WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
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
			 
			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
			<input type="hidden" name="context_id" value="<?php echo $context_id?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			<input type="hidden" name="name" value="<?php echo $row->name?>" />
			<input type="hidden" name="code" value="<?php echo $row->code?>" />
			<input type="hidden" name="criteriatype_id" value="<?php echo $row->criteriatype_id?>" />
			<input type="hidden" name="label" value="<?php echo $row->label; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php 	
	}		

	function editRelationSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $context_id, $tabList, $tab_id, $option)
	{
		global  $mainframe;
		$database =& JFactory::getDBO();
		
		$database->setQuery("SELECT at.code FROM #__sdi_relation r
									INNER JOIN #__sdi_attribute a ON a.id=r.attributechild_id
									INNER JOIN #__sdi_list_attributetype at ON a.attributetype_id=at.id
									WHERE r.id=".$row->relation_id);
		$attributetype = $database->loadResult();
		?>
			<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
				<table class="admintable" border="0" cellpadding="3" cellspacing="0">	
					<tr>
						<td class="key" width=150><?php echo JText::_("CORE_NAME"); ?></td>
						<td width=150><?php echo $row->name; ?></td>								
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
						<td><?php echo JHTML::_('select.genericlist', $tabList, 'tabList', 'class="list"', 'value', 'text', $tab_id);?></td>							
					</tr>
					<tr>
						<td class="key" width=250 ><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE"); ?></td>
						<td>
						<?php 
						switch ($attributetype){
							case 'guid':
							case 'text':
							case 'locale':
							case 'number':
							case 'integer':
							case 'link':
							case 'Thesaurus GEMET':
							case 'url':
							case 'textchoice':
							case 'localchoice':
								?>
								<input type="text" size="100" MAXLENGTH="500" id="defaultvalue" name="defaultvalue" value="<?php echo $row->defaultvalue;?>" />
								<?php 
								break;
							case 'date':
							case 'datetime':
								?>
								<div>
									<label class="checkbox" for="from"><?php echo JText::_("CORE_DATE_FROM");?></label>
									<?php echo helper_easysdi::calendar(($row->defaultvaluefrom == '0000-00-00')? null : $row->defaultvaluefrom , 'defaultvaluefrom','defaultvaluefrom',"%Y.%m.%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', JURI::base().'components/com_easysdi_catalog/templates/images/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
									<label class="checkbox" for="to"><?php echo JText::_("CORE_DATE_TO");?></label>
									<?php echo helper_easysdi::calendar(($row->defaultvalueto == '0000-00-00')? null : $row->defaultvalueto , 'defaultvalueto','defaultvalueto',"%Y.%m.%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', JURI::base().'components/com_easysdi_catalog/templates/images/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div>
								<?php 
								break;
							case 'list':
								$database->setQuery("SELECT attributechild_id FROM #__sdi_relation WHERE id=".$row->relation_id);
								$attributechild_id = $database->loadResult();
								$language =& JFactory::getLanguage();
								$database->setQuery("SELECT cv.value as value, t.label as text
																							FROM #__sdi_codevalue cv 
																							INNER JOIN #__sdi_translation t ON t.element_guid = cv.guid
																							WHERE cv.attribute_id=".$attributechild_id." 
																							AND t.language_id = (SELECT l.id 
																													FROM #__sdi_language l 
																													INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id 
																													WHERE c.code='".$language->_lang."' )");
								$values = $database->loadAssocList();
								echo JHTML::_("select.genericlist",$values, 'defaultvalue[]', 'size="5" class="inputbox" style="width:380px" multiple="multiple"', 'value', 'text', json_decode($row->defaultvalue,false) );
								break;
						}
						?>
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
				<td><?php echo JText::_("CORE_CREATED"); ?> :</td>
				<td><?php if ($row->created) {echo date('d.m.Y h:i:s',strtotime($row->created));} ?></td>
				<td>,</td>
				
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

			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
			<input type="hidden" name="context_id" value="<?php echo $context_id?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" />
			<input type="hidden" name="name" value="<?php echo $row->name?>" />
			<input type="hidden" name="code" value="<?php echo $row->code?>" /> 
			<input type="hidden" name="criteriatype_id" value="<?php echo $row->criteriatype_id?>" />
			<input type="hidden" name="ogcsearchfilter" value="<?php echo $row->ogcsearchfilter?>" />
			<input type="hidden" name="label" value="<?php echo $row->label; ?>" />
			<input type="hidden" name="relation_id" value="<?php echo $row->relation_id; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php 	
	}
	
	function editOGCSearchCriteria($row, $tab, $selectedTab, $fieldsLength, $languages, $labels, $filterfields, $context_id, $tabList, $tab_id, $rendertypes, $option)
	{
		global  $mainframe;
		
		$database =& JFactory::getDBO(); 

		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
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
					<td class="key"><?php echo JText::_("CATALOG_SEARCHCRITERIA_TAB"); ?></td>
					<td><?php echo JHTML::_('select.genericlist', $tabList, 'tabList', 'class="list"', 'value', 'text', $tab_id);?></td>							
				</tr>
				<tr>
					<td class="key" width=150 ><?php echo JText::_("CATALOG_RENDERTYPE"); ?></td>
					<?php 
						$selectedRendertype = $row->rendertype_id;
					?>
					<td><?php echo JHTML::_("select.genericlist",$rendertypes, 'rendertype_id', 'size="1" class="inputbox" onchange="javascript:changeDefaultValueField(this.value);"', 'value', 'text', $selectedRendertype ); ?></td>							
				</tr>
				<tr>
					<td class="key" width=150 ><?php echo JText::_("CATALOG_SEARCHCRITERIA_DEFAULT_VALUE"); ?></td>
					<td>
						<input type="text" id="defaultvalue" name="defaultvalue" value="<?php echo $row->defaultvalue;?>"  <?php if($selectedRendertype == 5){?> style="display:block;" <?php }else {?> style="display:none;"  <?php }?>/>
						<div id="div_defaultvalue" <?php if($selectedRendertype == 5){?> style="display:none;" <?php }else {?> style="display:block;"  <?php }?>>
						<label class="checkbox" for="from"><?php echo JText::_("CORE_DATE_FROM");?></label>
						<?php echo helper_easysdi::calendar($row->defaultvaluefrom, 'defaultvaluefrom','defaultvaluefrom',"%Y.%m.%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', JURI::base().'components/com_easysdi_catalog/templates/images/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						<label class="checkbox" for="to"><?php echo JText::_("CORE_DATE_TO");?></label>
						<?php echo helper_easysdi::calendar($row->defaultvalueto, 'defaultvalueto','defaultvalueto',"%Y.%m.%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', JURI::base().'components/com_easysdi_catalog/templates/images/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div>
					</td>			
				</tr>
			</table>
			<table class="admintable" border="0" cellpadding="3" cellspacing="0">
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
					<td class="key" WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
					<td><input size="50" type="text" name ="filterfield<?php echo "_".$lang->code;?>" value="<?php echo htmlspecialchars($filterfields[$lang->id])?>" maxlength="<?php echo $fieldsLength['ogcsearchfilter'];?>"></td>							
						</tr>
						
<?php
}
?>
							</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<table class="admintable" border="0" cellpadding="3" cellspacing="0">
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
					<td class="key" WIDTH=140><?php echo JText::_("CORE_".strtoupper($lang->code)); ?></td>
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
			<td><?php echo JText::_("CORE_CREATED"); ?> :</td>
			<td><?php if ($row->created) {echo date('d.m.Y h:i:s',strtotime($row->created));} ?></td>
			<td>,</td>
			
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

			<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
			<input type="hidden" name="guid" value="<?php echo $row->guid?>" />
			<input type="hidden" name="context_id" value="<?php echo $context_id?>" />
			<input type="hidden" name="ordering" value="<?php echo $row->ordering; ?>" />
			<input type="hidden" name="created" value="<?php echo ($row->created)? $row->created : date ('Y-m-d H:i:s');?>" />
			<input type="hidden" name="createdby" value="<?php echo ($row->createdby)? $row->createdby : $user->id; ?>" /> 
			<input type="hidden" name="updated" value="<?php echo ($row->created) ? date ("Y-m-d H:i:s") :  ''; ?>" />
			<input type="hidden" name="updatedby" value="<?php echo ($row->createdby)? $user->id : ''; ?>" /> 
			<input type="hidden" name="criteriatype_id" value="<?php echo $row->criteriatype_id?>" />
			<input type="hidden" name="label" value="<?php echo $row->label; ?>" />
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="id" value="<?php echo $row->id?>" />
			<input type="hidden" name="task" value="" />
		</form>
	<?php 	
	}
}
?>