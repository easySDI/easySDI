<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

class HTML_catalog{


	function listCatalogContentWithPan ($pageNav,$cswResults,$option, $total,$searchCriteria,$maxDescr, $selectedVersions, $listSimpleFilters, $listAdvancedFilters)
	{
		global  $mainframe;
		$option= JRequest::getVar('option');
		$context= JRequest::getVar('context');
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		
		$listMaxLength = config_easysdi::getValue("CATALOG_SEARCH_MULTILIST_LENGTH");
		
		$simulatedTabIndex = JRequest::getVar('simulatedTabIndex');
		$advancedSrch = JRequest::getVar('advancedSrch',0);

		// Rien à voir, à supprimer dès que possible
		//require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.displayManager.class.php');
		//displayManager::generateSitemap("C:\\RecorderWebGIS\\sitemap\\sitemap.xml");
		// -----------------------------------------
		
		/*$accounts = array();
		 $accounts[0]='';

		 //Do not display a furnisher without product

		 $query = "SELECT  #__sdi_account.id as value, #__users.name as text
		 FROM #__users, #__sdi_account
		 INNER JOIN #__sdi_object ON #__sdi_account.id = #__sdi_object.account_id
		 WHERE #__users.id = #__sdi_account.user_id AND
		 #__sdi_account.id IN (SELECT #__sdi_object.account_id FROM #__sdi_object WHERE #__sdi_object.published=1)
		 GROUP BY #__sdi_account.id
		 ORDER BY #__users.name";

		 $db->setQuery( $query);
		 $accounts = array_merge( $accounts, $db->loadObjectList() );
		 if ($db->getErrorNum())
		 {
			echo "<div class='alert'>";
			echo 	$db->getErrorMsg();
			echo "</div>";
			}
			*/



		$versions = array(
		JHTML::_('select.option',  '0', JText::_( 'CATALOG_SEARCH_VERSIONS_CURRENT' ) ),
		JHTML::_('select.option',  '1', JText::_( 'CATALOG_SEARCH_VERSIONS_ALL' ) )
		);

		$objecttypes = array();
		//$objecttypes[] = JHTML::_('select.option', '', '');
		if ($context <> "")
		{
			$db->setQuery("SELECT id AS value, name as text FROM #__sdi_objecttype WHERE id IN
								(SELECT co.objecttype_id 
								FROM #__sdi_context_objecttype co
								INNER JOIN #__sdi_context c ON c.id=co.context_id 
								WHERE c.code = '".$context."')
						   ORDER BY name");
		}
		else
		{
			$db->setQuery("SELECT id AS value, name as text FROM #__sdi_objecttype ORDER BY name");
		}
		$objecttypes = array_merge( $objecttypes, $db->loadObjectList() );
		HTML_catalog::alter_array_value_with_Jtext($objecttypes);

		//$rowContext = new contextByCode($db);
		//$rowContext->load($context);

		//print_r($rowContext);

		/*$listSearchFilters = array();
		 $db->setQuery("SELECT r.*, a.id as attribute_id, at.code as attributetype_code
		 FROM #__sdi_context c
		 INNER JOIN #__sdi_relation_context rc ON c.id=rc.context_id
		 INNER JOIN #__sdi_relation r ON r.id=rc.relation_id
		 INNER JOIN #__sdi_attribute a ON r.attributechild_id=a.id
		 INNER JOIN #__sdi_list_attributetype at ON at.id=a.attributetype_id
		 WHERE c.code='".$context."'
		 ORDER BY r.ordering");
		 $listSearchFilters = array_merge( $listSearchFilters, $db->loadObjectList() );

		 //echo "<hr>";print_r($listSearchFilters);
		 */
		?>
<div id="page">
<h2 class="contentheading"><?php echo JText::_("CATALOG_SEARCH_TITLE"); ?></h2>
<div class="contentin">

<form name="catalog_search_form" id="catalog_search_form" method="GET">
<input type="hidden" name="option" id="option"
	value="<?php echo JRequest::getVar('option' );?>" /> <input
	type="hidden" name="view" id="view"
	value="<?php echo JRequest::getVar('view' );?>" /> <input type="hidden"
	name="context" id="context"
	value="<?php echo JRequest::getVar('context' );?>" /> <input
	type="hidden" name="bboxMinX" id="bboxMinX"
	value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>" /> <input
	type="hidden" name="bboxMinY" id="bboxMinY"
	value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>" /> <input
	type="hidden" name="bboxMaxX" id="bboxMaxX"
	value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>" /> <input
	type="hidden" name="bboxMaxY" id="bboxMaxY"
	value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>" /> <input
	type="hidden" name="Itemid" id="Itemid"
	value="<?php echo JRequest::getVar('Itemid');?>" /> <input
	type="hidden" name="lang" id="lang"
	value="<?php echo JRequest::getVar('lang');?>" /> <input type="hidden"
	name="tabIndex" id="tabIndex" value="" /> <input type="hidden"
	name="simulatedTabIndex" id="simulatedTabIndex"
	value="<?php echo JRequest::getVar('simulatedTabIndex');?>" /> <input
	type="hidden" name="advancedSrch" id="advancedSrch"
	value="<?php echo JRequest::getVar('advancedSrch', 0);?>" /> <script
	type="text/javascript">
			
				window.addEvent('domready', function() {
				/*
				* Register event handlers
				*/
					//initialize the page
					init();
					
					//Toggle the state of the advanced search
					$('advSearchRadio').addEvent('click', function() {
						toggleAdvancedSearch($('advSearchRadio').checked);
					});
					
					//Handler for the clear button
					$('easysdi_clear_button').addEvent('click', function() {
						easysdiClearButton_click();
					});
					
					//Handler for the search button
					$('simple_search_button').addEvent('click', function() {
						easysdiSearchButton_click();
					});
					
				});
				
				function init(){
					//hide advanced search
					toggleAdvancedSearch($('advancedSrch').value);
				}
				
				function easysdiClearButton_click(){
					clearBasicSearch();
					clearAdvancedSearch();
					//document.getElementById('tabIndex').value = '0';
					//document.getElementById('catalog_search_form').submit();
				}
				
				function easysdiSearchButton_click(){
					document.getElementById('tabIndex').value = '0';
					document.getElementById('catalog_search_form').submit();
				}
				
				function toggleAdvancedSearch(isVisible){
					if(isVisible == true){
						$('divAdvancedSearch').style.visibility = 'visible';
						$('divAdvancedSearch').style.display = 'block';
						$('advSearchRadio').checked = true;
						$('advancedSrch').value=1;
					}else{
						$('divAdvancedSearch').style.visibility = 'hidden';
						$('divAdvancedSearch').style.display = 'none';
						$('advSearchRadio').checked = false;
						$('advancedSrch').value=0;
						//Do not keep data in a hidden table
						clearAdvancedSearch();
					}
				}
				
				function clearBasicSearch ()
				{
					// Lister tous les champs qui sont dans le div divSimpleSearch
					var divSimpleSearch;
					divSimpleSearch = document.getElementById('divSimpleSearch');
					var fields;
					fields = divSimpleSearch.getElementsByTagName('input');
					
					for (var i = 0; i < fields.length; i++)
					{
						if (fields.item(i).type == "checkbox") // Les checkbox de l'objecttype
							fields.item(i).checked = "checked";
						else if (fields.item(i).type == "radio") // Les radios de la version
							if (fields.item(i).value == 0)
								fields.item(i).checked = "checked";
							else
								fields.item(i).checked = "";
						else 
							fields.item(i).value = "";
					}

					fields = divSimpleSearch.getElementsByTagName('select');
					
					for (var i = 0; i < fields.length; i++)
					{
						fields.item(i).value = "";
					}
					
					 //document.getElementById('simple_filterfreetextcriteria').value = '';
					 //document.getElementById('account_id').value = '';
					 //document.getElementById('objecttype_id[]').value = '';
					 
				}
				
				function clearAdvancedSearch ()
				{
					// Lister tous les champs qui sont dans le div divAdvancedSearch
					var divAdvancedSearch;
					divAdvancedSearch = document.getElementById('divAdvancedSearch');
					var fields;
					fields = divAdvancedSearch.getElementsByTagName('input');
					
					for (var i = 0; i < fields.length; i++)
					{
						if (fields.item(i).type == "checkbox") // Les checkbox de l'objecttype
							fields.item(i).checked = "checked";
						else if (fields.item(i).type == "radio") // Les radios de la version
							if (fields.item(i).value == 0)
								fields.item(i).checked = "checked";
							else
								fields.item(i).checked = "";
						else 
							fields.item(i).value = "";
					}

					fields = divAdvancedSearch.getElementsByTagName('select');
					
					for (var i = 0; i < fields.length; i++)
					{
						fields.item(i).value = "";
					}
					
					 //document.getElementById('filter_visible').value = '';
					 //document.getElementById('filter_orderable').value = '';
					 //document.getElementById('filter_theme').value = '';
					 //document.getElementById('create_select').value = 'equal';
					 //document.getElementById('create_cal').value = '';
					 //document.getElementById('update_select').value = 'equal';
					 //document.getElementById('update_cal').value = '';

					 //document.getElementById("bboxMinX").value = "-180"; 	
					 //document.getElementById("bboxMinY").value ="-90";
					 //document.getElementById("bboxMaxX").value ="180"; 	
					 //document.getElementById("bboxMaxY").value ="90";
				}
				
			</script>

<h3><?php echo JText::_("CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>

<!--
				This is the simple search
			-->
<div id ="divSimpleSearch">
<table border="0" cellpadding="2" cellspacing="0" width="100%"
	class="mdCatContent">
	<?php

	foreach($listSimpleFilters as $searchfilter)
	{

		switch ($searchfilter->attributetype_code)
		{
			case "guid":
			case "text":
			case "locale":
			case "number":
			case "link":
			case "textchoice":
			case "localechoice":
				/* Fonctionnement texte*/
				?>
				<tr>
					<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
					<td align="left"><input type="text"
						id="<?php echo 'filter_'.$searchfilter->name;?>"
						name="<?php echo 'filter_'.$searchfilter->name;?>"
						value="<?php echo JRequest::getVar('filter_'.$searchfilter->name);?>"
						class="inputbox" /></td>
				</tr>
				<?php
				break;
			case "list":
				/* Fonctionnement liste*/
				$list = array();
				$list[] = JHTML::_('select.option', '', '');
				$query = "SELECT cv.value as value, t.label as text FROM #__sdi_attribute a RIGHT OUTER JOIN #__sdi_codevalue cv ON a.id=cv.attribute_id INNER JOIN #__sdi_translation t ON t.element_guid=cv.guid INNER JOIN #__sdi_language l ON t.language_id=l.id INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id WHERE cl.code='".$language->_lang."' AND a.id=".$searchfilter->attribute_id;
				$db->setQuery( $query);
				$list = array_merge( $list, $db->loadObjectList() );
			
				$size=(int)$listMaxLength;
				if (count($list) < (int)$listMaxLength)
					$size = count($list);
					
				$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchfilter->name)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<tr>
					<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
					<td>
						<div id="<?php echo 'div_'.$searchfilter->name;?>">
							<?php echo JHTML::_("select.genericlist", $list, 'filter_'.$searchfilter->name.'[]', 'class="inputbox" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('filter_'.$searchfilter->name)); ?>
							<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchfilter->name;?>', <?php echo $size;?>); return false;" href="#">
								<img src="<?php echo JURI::root(true);?>/templates/easysdi/icons/silk/add.png" alt="Expand"/>
							</a>
						</div>
					</td>
				</tr>
				<?php
				break;
			case "date":
			case "datetime":
				/* Fonctionnement période*/
				?>
				<tr>
					<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" class="searchTabs_date">
						<tr>
							<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_FROM");?></td>
							<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('create_cal_'.$searchfilter->name), "create_cal_".$searchfilter->name,"create_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
							</td>
							<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_TO");?></td>
							<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('update_cal_'.$searchfilter->name), "update_cal_".$searchfilter->name,"update_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				<?php
				break;
			case null: // Cas des attributs systèmes, car ils n'ont pas de relation liée
				if ($searchfilter->criteriatype_id == 1) // Attributs système
				{	
					switch ($searchfilter->criteria_code)
					{
						case "objecttype":
							$selectedObjectType = array();
							if (JRequest::getVar('objecttype_id'))
								$selectedObjectType = JRequest::getVar('objecttype_id');
							else if (!JRequest::getVar('bboxMinX'))
								$selectedObjectType = $objecttypes;
							
							?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td><?php
						echo HTML_catalog::checkboxlist($objecttypes, 'objecttype_id[]', 'size="1" class="inputbox" ', 'value', 'text', $selectedObjectType);
						?></td>
						<td></td>
					</tr>
					<?php
					break;
				case "fulltext":
					?>
					<tr>
						<td align="left"><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<!-- this was the old advanced critera: filterfreetextcriteria -->
						<td align="left"><input type="text" id="simple_filterfreetextcriteria"
							name="simple_filterfreetextcriteria"
							value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>"
							class="inputbox" /></td>
					</tr>
					<?php
					break;
				case "versions":
					$selectedVersion = 0;
					if (JRequest::getVar('versions'))
						$selectedVersion = JRequest::getVar('versions');
					?>	
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL"); ?></td>
						<td><?php echo JHTML::_('select.radiolist', $versions, 'versions', 'class="radio"', 'value', 'text', $selectedVersion);?></td>
					</tr>
					<?php
					break;
				case "account_id":
					/* Fonctionnement liste*/
					$accounts = array();
					$accounts[] = JHTML::_('select.option', '', '');
					$query = "SELECT DISTINCT a.id as value, a.name as text 
							  FROM #__sdi_account a 
							  INNER JOIN #__users u ON u.id=a.user_id
							  WHERE a.root_id IS NULL 
							  ";
					$db->setQuery( $query);
					//echo $db->getQuery();
					$accounts = array_merge( $accounts, $db->loadObjectList() );
				
					$size=(int)$listMaxLength;
					if (count($accounts) < (int)$listMaxLength)
						$size = count($accounts);					
					
					$multiple = 'size="1"';
					
					if (count(JRequest::getVar('filter_'.$searchfilter->name)) > 1)
						$multiple='size="'.$size.'" multiple="multiple"';
					
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
							<div id="<?php echo 'div_'.$searchfilter->name;?>">
								<?php echo JHTML::_("select.genericlist", $accounts, $searchfilter->code.'[]', 'class="inputbox" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar($searchfilter->code)); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo $searchfilter->code;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo JURI::root(true);?>/templates/easysdi/icons/silk/add.png" alt="Expand"/>
								</a>
							</div>
						</td>
					</tr>
					<?php
					break;
				case "object_name":
					?>
					<tr>
						<td align="left"><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td align="left"><input type="text" id="<?php echo $searchfilter->code;?>"
							name="<?php echo $searchfilter->code;?>"
							value="<?php echo JRequest::getVar($searchfilter->code);?>"
							class="inputbox" /></td>
					</tr>
					<?php
					break;
				case "title":
					?>
					<tr>
						<td align="left"><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td align="left"><input type="text" id="<?php echo $searchfilter->code;?>"
							name="<?php echo $searchfilter->code;?>"
							value="<?php echo JRequest::getVar($searchfilter->code);?>"
							class="inputbox" /></td>
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
					//echo $db->getQuery();
					$managers = array_merge( $managers, $db->loadObjectList() );
				
					$size=(int)$listMaxLength;
					if (count($managers) < (int)$listMaxLength)
						$size = count($managers);
					
					$multiple = 'size="1"';
					if (count(JRequest::getVar('filter_'.$searchfilter->name)) > 1)
						$multiple='size="'.$size.'" multiple="multiple"';
					
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
							<div id="<?php echo 'div_'.$searchfilter->name;?>">
								<?php echo JHTML::_("select.genericlist", $managers, $searchfilter->code.'[]', 'class="inputbox" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar($searchfilter->code)); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo $searchfilter->code;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo JURI::root(true);?>/templates/easysdi/icons/silk/add.png" alt="Expand"/>
								</a>
							</div>
						</td>
					</tr>
					<?php
					break;
				case "metadata_created":
					/* Fonctionnement période*/
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" class="searchTabs_date">
							<tr>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_FROM");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('create_cal_'.$searchfilter->code), "create_cal_".$searchfilter->code,"create_cal_".$searchfilter->code,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_TO");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('update_cal_'.$searchfilter->code), "update_cal_".$searchfilter->code,"update_cal_".$searchfilter->code,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<?php
					break;
				case "metadata_published":
					/* Fonctionnement période*/
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" class="searchTabs_date">
							<tr>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_FROM");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('create_cal_'.$searchfilter->name), "create_cal_".$searchfilter->name,"create_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_TO");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('update_cal_'.$searchfilter->name), "update_cal_".$searchfilter->name,"update_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<?php
					break;
				default:
						?>
						<!-- <tr>
							<td><?php //echo JText::_("CATALOG_SEARCH_FILTER_ACCOUNT");?></td>
							<td><?php //echo JHTML::_("select.genericlist", $accounts, 'account_id', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('account_id')); ?></td>		
						</tr>
						 -->
						 <?php
						 break;
		}
		break;
		}
		else // Cas des attributs OGC qui ne sont pas liés à une relation
		{
			/* Fonctionnement texte*/
			?>
			<tr>
				<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
				<td align="left"><input type="text"
					id="<?php echo 'filter_'.$searchfilter->name;?>"
					name="<?php echo 'filter_'.$searchfilter->name;?>"
					value="<?php echo JRequest::getVar('filter_'.$searchfilter->name);?>"
					class="inputbox" /></td>
			</tr>
			<?php
		}
default:
	break;
		}
	}
	?>
</table>
</div>

<!--
				This is the advanced search
			-->
<div id="divAdvancedSearch">
<table border="0" cellpadding="2" cellspacing="0" width="100%"
	class="mdCatContent">

	<?php

	foreach($listAdvancedFilters as $searchfilter)
	{

		switch ($searchfilter->attributetype_code)
		{
			case "guid":
			case "text":
			case "locale":
			case "number":
			case "link":
			case "textchoice":
			case "localechoice":
				/* Fonctionnement texte*/
				?>
				<tr>
					<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
					<td align="left"><input type="text"
						id="<?php echo 'filter_'.$searchfilter->name;?>"
						name="<?php echo 'filter_'.$searchfilter->name;?>"
						value="<?php echo JRequest::getVar('filter_'.$searchfilter->name);?>"
						class="inputbox" /></td>
				</tr>
				<?php
				break;
			case "list":
				/* Fonctionnement liste*/
				$list = array();
				$list[] = JHTML::_('select.option', '', '');
				$query = "SELECT cv.value as value, t.label as text FROM #__sdi_attribute a RIGHT OUTER JOIN #__sdi_codevalue cv ON a.id=cv.attribute_id INNER JOIN #__sdi_translation t ON t.element_guid=cv.guid INNER JOIN #__sdi_language l ON t.language_id=l.id INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id WHERE cl.code='".$language->_lang."' AND a.id=".$searchfilter->attribute_id;
				$db->setQuery( $query);
				$list = array_merge( $list, $db->loadObjectList() );
			
				$size=(int)$listMaxLength;
				if (count($list) < (int)$listMaxLength)
					$size = count($list);
					
				$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchfilter->name)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<tr>
					<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
					<td>
						<div id="<?php echo 'div_'.$searchfilter->name;?>">
							<?php echo JHTML::_("select.genericlist", $list, 'filter_'.$searchfilter->name.'[]', 'class="inputbox" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('filter_'.$searchfilter->name)); ?>
							<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchfilter->name;?>'); return false;" href="#">
								<img src="<?php echo JURI::root(true);?>/templates/easysdi/icons/silk/add.png" alt="Expand"/>
							</a>
						</div>
					</td>
				</tr>
				<?php
				break;
			case "date":
			case "datetime":
				/* Fonctionnement période*/
				?>
				<tr>
					<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
					<td>
					<table border="0" cellpadding="0" cellspacing="0" class="searchTabs_date">
						<tr>
							<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_FROM");?></td>
							<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('create_cal_'.$searchfilter->name), "create_cal_".$searchfilter->name,"create_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
							</td>
							<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_TO");?></td>
							<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('update_cal_'.$searchfilter->name), "update_cal_".$searchfilter->name,"update_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
							</td>
						</tr>
					</table>
					</td>
				</tr>
				<?php
				break;
			case null: // Cas des attributs qui ne sont pas liés à une relation
				if ($searchfilter->criteriatype_id == 1) // Attributs système
				{
					switch ($searchfilter->criteria_code)
					{
						case "objecttype":
							$selectedObjectType = array();
							if (JRequest::getVar('objecttype_id'))
								$selectedObjectType = JRequest::getVar('objecttype_id');
							else if (!JRequest::getVar('bboxMinX'))
								$selectedObjectType = $objecttypes;						
							?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td><?php
						echo HTML_catalog::checkboxlist($objecttypes, 'objecttype_id[]', 'size="1" class="inputbox" ', 'value', 'text', $selectedObjectType);
						?></td>
						<td></td>
					</tr>
					<?php
					break;
				case "fulltext":
					?>
					<tr>
						<td align="left"><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<!-- this was the old advanced critera: filterfreetextcriteria -->
						<td align="left"><input type="text" id="simple_filterfreetextcriteria"
							name="simple_filterfreetextcriteria"
							value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>"
							class="inputbox" /></td>
					</tr>
					<?php
					break;
				case "versions":
					$selectedVersion = 0;
					if (JRequest::getVar('versions'))
						$selectedVersion = JRequest::getVar('versions');
						
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL"); ?></td>
						<td><?php echo JHTML::_('select.radiolist', $versions, 'versions', 'class="radio"', 'value', 'text', $selectedVersion);?></td>
					</tr>
					<?php
					break;
				case "account_id":
					/* Fonctionnement liste*/
					$accounts = array();
					$accounts[] = JHTML::_('select.option', '', '');
					$query = "SELECT DISTINCT a.id as value, a.name as text 
							  FROM #__sdi_account a 
							  INNER JOIN #__users u ON u.id=a.user_id
							  WHERE a.root_id IS NULL 
							  ";
					$db->setQuery( $query);
					//echo $db->getQuery();
					$accounts = array_merge( $accounts, $db->loadObjectList() );
				
					$size=(int)$listMaxLength;
					if (count($accounts) < (int)$listMaxLength)
						$size = count($accounts);
							
					$multiple = 'size="1"';
					if (count(JRequest::getVar('filter_'.$searchfilter->name)) > 1)
						$multiple='size="'.$size.'" multiple="multiple"';
					
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
							<div id="<?php echo 'div_'.$searchfilter->name;?>">
								<?php echo JHTML::_("select.genericlist", $accounts, $searchfilter->code.'[]', 'class="inputbox" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar($searchfilter->code)); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo $searchfilter->code;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo JURI::root(true);?>/templates/easysdi/icons/silk/add.png" alt="Expand"/>
								</a>
							</div>
						</td>
					</tr>
					<?php
					break;
				case "object_name":
					?>
					<tr>
						<td align="left"><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td align="left"><input type="text" id="<?php echo $searchfilter->code;?>"
							name="<?php echo $searchfilter->code;?>"
							value="<?php echo JRequest::getVar($searchfilter->code);?>"
							class="inputbox" /></td>
					</tr>
					<?php
					break;
				case "title":
					?>
					<tr>
						<td align="left"><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td align="left"><input type="text" id="<?php echo $searchfilter->code;?>"
							name="<?php echo $searchfilter->code;?>"
							value="<?php echo JRequest::getVar($searchfilter->code);?>"
							class="inputbox" /></td>
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
					//echo $db->getQuery();
					$managers = array_merge( $managers, $db->loadObjectList() );
				
					$size=(int)$listMaxLength;
					if (count($managers) < (int)$listMaxLength)
						$size = count($managers);
						
					$multiple = 'size="1"';
					if (count(JRequest::getVar('filter_'.$searchfilter->name)) > 1)
						$multiple='size="'.$size.'" multiple="multiple"';
					
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
							<div id="<?php echo 'div_'.$searchfilter->name;?>">
								<?php echo JHTML::_("select.genericlist", $managers, $searchfilter->code.'[]', 'class="inputbox" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar($searchfilter->code)); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo $searchfilter->code;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo JURI::root(true);?>/templates/easysdi/icons/silk/add.png" alt="Expand"/>
								</a>
							</div>
						</td>
					</tr>
					<?php
					break;
				case "metadata_created":
					/* Fonctionnement période*/
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" class="searchTabs_date">
							<tr>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_FROM");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('create_cal_'.$searchfilter->code), "create_cal_".$searchfilter->code,"create_cal_".$searchfilter->code,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_TO");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('update_cal_'.$searchfilter->code), "update_cal_".$searchfilter->code,"update_cal_".$searchfilter->code,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<?php
					break;
				case "metadata_published":
					/* Fonctionnement période*/
					?>
					<tr>
						<td><?php echo JText::_($searchfilter->guid."_LABEL");?></td>
						<td>
						<table border="0" cellpadding="0" cellspacing="0" class="searchTabs_date">
							<tr>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_FROM");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('create_cal_'.$searchfilter->name), "create_cal_".$searchfilter->name,"create_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
								<td class="searchTabs_date_bounds"><?php echo JText::_("CORE_DATE_TO");?></td>
								<td class="searchTabs_date_field"><?php echo JHTML::_('calendar',JRequest::getVar('update_cal_'.$searchfilter->name), "update_cal_".$searchfilter->name,"update_cal_".$searchfilter->name,"%d.%m.%Y", 'class="searchTabs_calendar"'); ?>
								</td>
							</tr>
						</table>
						</td>
					</tr>
					<?php
					break;
				default:
					?>
					<!-- <tr>
												<td><?php //echo JText::_("CATALOG_SEARCH_FILTER_ACCOUNT");?></td>
												<td><?php //echo JHTML::_("select.genericlist", $accounts, 'account_id', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('account_id')); ?></td>		
											</tr>
											 -->
											 <?php
											 break;
		}
		break;
		}
		else // Cas des attributs OGC qui ne sont pas liés à une relation
		{
			/* Fonctionnement texte*/
			?>
			<tr>
				<td><?php echo JText::_($searchfilter->relation_guid."_LABEL");?></td>
				<td align="left"><input type="text"
					id="<?php echo 'filter_'.$searchfilter->name;?>"
					name="<?php echo 'filter_'.$searchfilter->name;?>"
					value="<?php echo JRequest::getVar('filter_'.$searchfilter->name);?>"
					class="inputbox" /></td>
			</tr>
			<?php
		}
default:
	break;
		}

		/*
			$themes = array();
			$themes[] = JHTML::_('select.option', '', '');
			$query = "SELECT #__sdi_list_topiccategory.code as value, #__sdi_list_topiccategory.label as text FROM `#__sdi_list_topiccategory`";
			$db->setQuery( $query);
			$themes = array_merge( $themes, $db->loadObjectList() );
			HTML_catalog::alter_array_value_with_Jtext($themes);

			?>
			<tr>
			<td ><?php echo JText::_("CATALOG_SEARCH_FILTER_THEME");?></td>
			<td><?php echo JHTML::_("select.genericlist", $themes, 'filter_theme', 'size="1" class="inputbox" ', 'value', 'text', JRequest::getVar('filter_theme')); ?></td>
			</tr>

			<tr>
			<td><?php echo JText::_("CATALOG_SEARCH_FILTER_VISIBLE");?></td>
			<td><input type="checkbox" id="filter_visible" name="filter_visible" <?php if (JRequest::getVar('filter_visible')) echo " checked"; ?> class="inputbox" /></td>
			</tr>
			<tr>
			<td><?php echo JText::_("CATALOG_SEARCH_FILTER_ORDERABLE");?></td>
			<td><input type="checkbox" id="filter_orderable" name="filter_orderable" <?php if (JRequest::getVar('filter_orderable')) echo " checked"; ?> class="inputbox" /></td>
			</tr>
			<tr>
			<td><?php echo JText::_("CATALOG_SEARCH_CREATED");?></td>
			<td>
			<select id="create_select" size="1" name="create_select">
			<option value="equal" <?php if(JRequest::getVar('create_select')=="equal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_EQUAL");?></option>
			<option value="smallerorequal" <?php if(JRequest::getVar('create_select')=="smallerorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_BEFORE");?></option>
			<option value="greaterorequal" <?php if(JRequest::getVar('create_select')=="greaterorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_AFTER");?></option>
			<option value="different" <?php if(JRequest::getVar('create_select')=="different") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_NOTEQUAL");?></option>
			</select>
			<?php echo JHTML::_('calendar',JRequest::getVar('create_cal'), "create_cal","create_cal","%d.%m.%Y"); ?>
			</td>
			</tr>
			<tr>
			<td><?php echo JText::_("CATALOG_SEARCH_UPDATED");?></td>
			<td>
			<select id="update_select" size="1" name="update_select">
			<option value="equal" <?php if(JRequest::getVar('update_select')=="equal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_EQUAL");?></option>
			<option value="smallerorequal" <?php if(JRequest::getVar('update_select')=="smallerorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_BEFORE");?></option>
			<option value="greaterorequal" <?php if(JRequest::getVar('update_select')=="greaterorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_AFTER");?></option>
			<option value="different" <?php if(JRequest::getVar('update_select')=="different") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_NOTEQUAL");?></option>
			</select>
			<?php echo JHTML::_('calendar',JRequest::getVar('update_cal'), "update_cal","update_cal","%d.%m.%Y"); ?>
			</td>
			</tr>
			<?php */
	}
	?>
	<!-- <tr>
		<td><?php //echo JText::_("CATALOG_SEARCH_FILTER_VISIBLE");?></td>
		<td><input type="checkbox" id="filter_visible" name="filter_visible"
		<?php //if (JRequest::getVar('filter_visible')) echo " checked"; ?>
			class="inputbox" /></td>
	</tr>
	 -->
	<!-- <tr>
		<td><?php //echo JText::_("CATALOG_SEARCH_CREATED");?></td>
		<td><select id="create_select" size="1" name="create_select">
			<option value="equal"
			<?php //if(JRequest::getVar('create_select')=="equal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_EQUAL");?></option>
			<option value="smallerorequal"
			<?php //if(JRequest::getVar('create_select')=="smallerorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_BEFORE");?></option>
			<option value="greaterorequal"
			<?php //if(JRequest::getVar('create_select')=="greaterorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_AFTER");?></option>
			<option value="different"
			<?php //if(JRequest::getVar('create_select')=="different") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_NOTEQUAL");?></option>
		</select> <?php //echo JHTML::_('calendar',JRequest::getVar('create_cal'), "create_cal","create_cal","%d.%m.%Y"); ?>
		</td>
	</tr>
	<tr>
		<td><?php //echo JText::_("CATALOG_SEARCH_UPDATED");?></td>
		<td><select id="update_select" size="1" name="update_select">
			<option value="equal"
			<?php //if(JRequest::getVar('update_select')=="equal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_EQUAL");?></option>
			<option value="smallerorequal"
			<?php //if(JRequest::getVar('update_select')=="smallerorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_BEFORE");?></option>
			<option value="greaterorequal"
			<?php //if(JRequest::getVar('update_select')=="greaterorequal") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_AFTER");?></option>
			<option value="different"
			<?php //if(JRequest::getVar('update_select')=="different") echo "SELECTED"; ?>><?php echo JText::_("CATALOG_SEARCH_DATE_NOTEQUAL");?></option>
		</select> <?php //echo JHTML::_('calendar',JRequest::getVar('update_cal'), "update_cal","update_cal","%d.%m.%Y"); ?>
		</td>
	</tr>
	 -->
</table>

			<?php
			//Feature deactivated for now...
			//HTML_catalog::generateMap();

			?></div>

<!-- Les boutons Rechercher / Vider -->
<table border="0" cellpadding="2" cellspacing="0" width="100%"
	class="mdCatContent">
	<tr>
		<td colspan="2"><input id="advSearchRadio" name="advSearchRadio"
			type="checkBox" value="" /> <span><?php echo JText::_("CATALOG_SEARCH_TEXT_ADVANCED_CRITERIA"); ?></span>
		</td>
	</tr>
	<tr>
		<td class="catalog_controls">
		<button id="simple_search_button" name="simple_search_button"
			type="submit" class="easysdi_search_button"><?php echo JText::_("CATALOG_SEARCH_SEARCH_BUTTON"); ?></button>
		</td>
		<td>
		<button type="button" id="easysdi_clear_button"
			class="easysdi_clear_button"><?php echo JText::_("CATALOG_SEARCH_CLEAR_BUTTON"); ?></button>
		</td>
	</tr>
</table>
</form>



			<?php if($cswResults){

			 //
			 //
			 //
			 // Nothing to do out there...
			 //
			 //
			 //







			 ?> <br />
<table width="100%">
	<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
</table>
<h3><?php echo JText::_("CATALOG_SEARCH_RESULTS_TITLE"); ?></h3>

<span class="easysdi_number_of_metadata_found"><?php echo JText::_("CATALOG_SEARCH_NUMBER_OF_METADATA_FOUND");?>
			 <?php echo $total ?> </span>
<table class="mdsearchresult">
	<!--
	<thead>
		<tr>

	 		<th><?php echo JText::_('CORE_SHARP'); ?></th>
			<th><?php echo JText::_('CATALOG_SEARCH_ORDERABLE'); ?></th>

			<th><?php echo JText::_('CATALOG_SEARCH_ROOT_LOGO'); ?></th>
			<th><?php echo JText::_('CATALOG_SEARCH_OBJECT_NAME'); ?></th>
		</tr>
	</thead>
-->
<?php
$i=0;
$param = array('size'=>array('x'=>800,'y'=>800) );
JHTML::_("behavior.modal","a.modal",$param);



$xpath = new DomXPath($cswResults);
$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
$nodes = $xpath->query('//gmd:MD_Metadata');

foreach($nodes  as $metadata){

	$i++;

	$md = new geoMetadata($metadata);

	$doc = $md->metadata;
	$doc->formatOutput = true;

	$root = $doc->getElementsByTagName("MD_Metadata");
	$root = $root->item(0);

	$XMLNewRoot = $doc->createElement("Metadata");
	$doc->appendChild($XMLNewRoot);
	$XMLNewRoot->appendChild($root);

	$XMLSdi = $doc->createElement("sdi:Metadata");
	$XMLNewRoot->appendChild($XMLSdi);
	//$doc->appendChild($XMLSdi);
	$XMLSdi->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:sdi', 'http://www.depth.ch/sdi');

	//print_r($md->metadata->saveXML());echo "<hr>";

	$md_orderable=0;
	$pOrderableExt = 0;
	$pOrderableInt = 0;

	$query = "select DISTINCT o.visibility_id from #__sdi_object o
				INNER JOIN #__sdi_objectversion ov ON ov.object_id=o.id
				INNER JOIN #__sdi_metadata m ON m.id=ov.metadata_id
				where m.guid = '".$md->getFileIdentifier()."'";
	$db->setQuery( $query);
	//$pOrderableExt = $db->loadResult();
	$pOrderable = $db->loadResult();

	/*$query = "select o.visibility_id from #__sdi_object o, #__sdi_metadata m where o.metadata_id=m.id AND m.guid = '".$md->getFileIdentifier()."'";
		$db->setQuery( $query);
		$pOrderableInt = $db->loadResult();
		*/
	//if($pOrderableExt == 1 || $pOrderableInt == 1)
	if($pOrderable == 1 || $pOrderable == 2)
	{
		$md_orderable=1;
	}
	//echo $md->getFileIdentifier()." Ext:".$pOrderableExt." Int:".$pOrderableInt."__".$query ."<br>";

	$query = "SELECT count(*) FROM #__sdi_object o, #__sdi_metadata m WHERE o.metadata_id=m.id AND o.previewWmsUrl != '' AND m.guid = '".$md->getFileIdentifier()."'";

	$db->setQuery( $query);

	$hasPreview = $db->loadResult();
	if ($db->getErrorNum()) {
		$hasPreview = 0;

	}

	$queryAccountID = "	select o.account_id 
						FROM #__sdi_metadata m
						INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						WHERE m.guid = '".$md->getFileIdentifier()."'";
	$db->setQuery($queryAccountID);
	$account_id = $db->loadResult();

	if ($account_id <> "")
	{
		$queryAccountLogo = "select logo from #__sdi_account where id = ".$account_id;
		$db->setQuery($queryAccountLogo);
		$account_logo = $db->loadResult();
	
		$query="select CONCAT( CONCAT( ad.agentfirstname, ' ' ) , ad.agentlastname ) AS name from #__sdi_account a inner join #__sdi_address ad on a.id = ad.account_id WHERE ad.account_id = ".$account_id ." and ad.type_id=1" ;
		$db->setQuery($query);
		$supplier= $db->loadResult();
	}
	else
	{
		$account_logo = "";
		$supplier= "";
	}
	
	$user =& JFactory::getUser();
	$language = $user->getParam('language', '');

	$logoWidth = config_easysdi::getValue("logo_width");
	$logoHeight = config_easysdi::getValue("logo_height");

	$isMdPublic = false;
	$isMdFree = true;

	//Define if the md is free or not
	$queryAccountID = "	select o.is_free 
						FROM #__sdi_metadata m
						INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						WHERE m.guid = '".$md->getFileIdentifier()."'";
	$db->setQuery($queryAccountID);
	$is_free = $db->loadResult();
	if($is_free == 0 or $is_free == "")
	{
		$isMdFree = false;
	}

	//Define if the md is public or not
	$queryAccountID = "	select o.visibility_id 
						FROM #__sdi_metadata m
						INNER JOIN #__sdi_objectversion ov ON ov.metadata_id = m.id
						INNER JOIN #__sdi_object o ON o.id = ov.object_id 
						WHERE m.guid = '".$md->getFileIdentifier()."'";
	$db->setQuery($queryAccountID);
	$external = $db->loadResult();
	if($external == 1 or $external == "")
	{
		$isMdPublic = true;
	}
	//}

	// Récupérer le type d'objet
	$queryObjecttype = "select DISTINCT ot.id from #__sdi_objecttype ot
							INNER JOIN #__sdi_object o ON o.objecttype_id=ot.id
							INNER JOIN #__sdi_objectversion ov ON ov.object_id=o.id
							INNER JOIN #__sdi_metadata m ON m.id=ov.metadata_id
							 where m.guid = '".$md->getFileIdentifier()."'";
	$db->setQuery($queryObjecttype);
	$objecttype_id = $db->loadResult();

	if ($objecttype_id <> "")
	{
		// Récupérer le logo du type d'objet
		$queryObjecttypeLogo = "select logo from #__sdi_objecttype where id = ".$objecttype_id;
		$db->setQuery($queryObjecttypeLogo);
		$objecttype_logo = $db->loadResult();
	}
	else
	{
		$objecttype_logo = "";
	}
	
	// Créer une entrée pour le logo du compte
	$XMLALogo = $doc->createElement("sdi:account_logo", $account_logo);
	$XMLALogo->setAttribute('width', $logoWidth);
	$XMLALogo->setAttribute('height', $logoHeight);
	$XMLSdi->appendChild($XMLALogo);

	// Créer une entrée pour le logo du type d'objet
	$XMLOTLogo = $doc->createElement("sdi:objecttype_logo", $objecttype_logo);
	$XMLOTLogo->setAttribute('width', $logoWidth);
	$XMLOTLogo->setAttribute('height', $logoHeight);
	$XMLSdi->appendChild($XMLOTLogo);

	// Créer une entrée pour la visibilité de la métadonnée
	$XMLMDVisibility = $doc->createElement("sdi:metadata_visibility", (int)$isMdPublic);
	$XMLSdi->appendChild($XMLMDVisibility);

	// Créer une entrée pour la commande de l'objet
	$XMLOrderable = $doc->createElement("sdi:orderable", $md_orderable);
	$XMLSdi->appendChild($XMLOrderable);

	// Créer une entrée pour la gratuité de la métadonnée
	$XMLMDFree = $doc->createElement("sdi:is_free", (int)$isMdFree);
	$XMLSdi->appendChild($XMLMDFree);

	// Créer une entrée pour lae preview de l'objet
	$XMLPreview = $doc->createElement("sdi:preview", $hasPreview);
	$XMLSdi->appendChild($XMLPreview);

	//$doc->save("C:\\RecorderWebGIS\\catalog_search\\catalog_search_".$md->getFileIdentifier().".xml");

	$language = $user->getParam('language', '');
	$style = new DomDocument();
	if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XHTML_result_'.$language.'.xsl')){
		$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XHTML_result_'.$language.'.xsl');
	}else{
		$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XML2XHTML_result.xsl');
	}

	$processor = new xsltProcessor();
	$processor->importStylesheet($style);
	$xml = new DomDocument();
	$xml = $processor->transformToXml($doc);

	printf($xml);

	/*
	 * La partie qui suit a été remplacée par une transformation  xslt
	 */

	/*
		?>
		<tr>
		<!-- <td><?php echo $i; ?></td>  -->
		<?php
		?>

		<td valign="top" rowspan=3>
		<img width="<?php echo $logoWidth ?>px" height="<?php echo $logoHeight ?>px" src="<?php echo $account_logo;?>" alt="<?php echo JText::_('CATALOG_SEARCH_ROOT_LOGO');?>"></img>
		</td>
		<td colspan="3"><span class="mdtitle"><?php echo $md->getDataIdentificationTitle();?></span>
		</td>
		<td valign="top" rowspan=2>
		<table id="info_md">
		<tr>
		<td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("CATALOG_SEARCH_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
		</tr>
		<tr>
		<td><div <?php if($md_orderable == 1) echo 'title="'.JText::_("CATALOG_SEARCH_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else echo 'title="'.JText::_("CATALOG_SEARCH_INFOLOGO_NOTORDERABLE").'" class="easysdi_product_does_not_exist"';?>></div></td>
		</tr>
		<tr>
		<td><div <?php if($isMdFree) echo 'title="'.JText::_("CATALOG_SEARCH_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
		</tr>
		</table>
		</td>
	 </tr>
	 <tr>
	 <td colspan="3"><span class="mddescr"><?php echo mb_substr($md->getDescription($language), 0, $maxDescr, 'UTF-8'); if(strlen($md->getDescription($language))>$maxDescr)echo" [...]";?></span></td>
	 </tr>
	 <tr>
	 <!--
	 <a	class="<?php if ($md_orderable>0) {echo "easysdi_orderable";} else {echo "easysdi_not_orderable";} ?>"
	 href="./index.php?option=com_easysdi_shop&view=shop" target="_self"><?php echo JText::_("CATALOG_VIEW_MD"); ?>
	 </a>
	 -->
	 <td><span class="mdviewfile">
	 <a class="modal"
	 title="<?php echo JText::_("CATALOG_VIEW_MD"); ?>"
	 href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>"
	 rel="{handler:'iframe',size:{x:650,y:600}}"><?php echo JText::_("CATALOG_VIEW_MD"); ?>
	 </a></span>
	 </td>
	 <?php if ($hasPreview > 0){ ?>
	 <td><span class="mdviewproduct">
	 <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=previewProduct&metadata_id=<?php echo $md->getFileIdentifier();?>"
	 rel="{handler:'iframe',size:{x:558,y:415}}"><?php echo JText::_("CATALOG_PREVIEW"); ?></a></span>
	 </td>
		<?php } ?>
		<td>&nbsp;</td>
	 </tr>
	 <tr>
	 <td colspan="5" halign="middle"><div class="separator" /></td>
	 </tr>
	 <?php*/
}
?>
</table>

<!-- pageNav at footer -->
<table width="100%">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="center">&nbsp;</td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
	</tr>
</table>

<?php } ?></div>


</div>


<?php


	}
	/*
	 function listCatalogContent($pageNav,$cswResults,$option, $total,$searchCriteria,$maxDescr){
		global  $mainframe;
		$db =& JFactory::getDBO();
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_CATALOG_TITLE"); ?></h2>
		<div class="contentin">



		<form name="catalog_search_form" id="catalog_search_form"  method="GET">
		<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" />
		<input type="hidden" name="view" id="view" value="<?php echo JRequest::getVar('view' );?>" />
		<input type="hidden" name="bboxMinX" id="bboxMinX" value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>" />
		<input type="hidden" name="bboxMinY" id="bboxMinY" value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>" />
		<input type="hidden" name="bboxMaxX" id="bboxMaxX" value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>" />
		<input type="hidden" name="bboxMaxY" id="bboxMaxY" value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>" />
		<input type="hidden" name="Itemid" id="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" />
		<input type="hidden" name="lang" id="lang" value="<?php echo JRequest::getVar('lang');?>" />
		<input type="hidden" name="tabIndex" id="tabIndex" value="" />
		<h3><?php echo JText::_("EASYSDI_CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>

		<?php
		$index = JRequest::getVar('tabIndex', 0);
		$tabs =& JPANE::getInstance('Tabs', array('startOffset'=>$index));
		//	echo $tabs->startPane("catalogPane");
		//	echo $tabs->startPanel(JText::_("EASYSDI_TEXT_SIMPLE_CRITERIA"),"catalogPanel1");
		?> <br/>

		<table width="100%">
		<tr>
		<td>
		<table width="100%">
		<tr>
		<td align="left"><b><?php echo JText::_("EASYSDI_CATALOG_FILTER_TITLE");?></b>&nbsp;
		<input type="text" id="simple_filterfreetextcriteria"  name="simple_filterfreetextcriteria" value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>" class="inputbox" /></td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<table>
		<tr>
		<td>
		<button type="submit" class="easysdi_search_button"
		onclick="clearDetailsForm();
		document.getElementById('tabIndex').value = '0';
		document.getElementById('catalog_search_form').submit();">
		<?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
		</td>
		<td>
		<button type="submit" class="easysdi_clear_button"
		onclick="clearForm();
		document.getElementById('tabIndex').value = '0';
		document.getElementById('catalog_search_form').submit();">
		<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
		</td>
		</tr>
		</table>
		<?php
		//		echo $tabs->endPanel();
		//		echo $tabs->startPanel(JText::_("EASYSDI_TEXT_ADVANCED_CRITERIA"),"catalogPanel2");
		?><br/>
		<table width="100%" >
		<tr>
		<td><?php
		HTML_catalog::generateMap();
		?></td>
			
		</tr>
		</table>
		<table>
		<tr>
		<td>
		<button type="submit" class="easysdi_search_button"
		onclick="clearForm();
		document.getElementById('tabIndex').value = '1';
		document.getElementById('catalog_search_form').submit();">
		<?php echo JText::_("EASYSDI_CATALOG_SEARCH_BUTTON"); ?></button>
		</td>
		<td>
		<button type="submit" class="easysdi_clear_button"
		onclick="clearDetailsForm();
		document.getElementById('tabIndex').value = '1';
		document.getElementById('catalog_search_form').submit();">
		<?php echo JText::_("EASYSDI_CATALOG_CLEAR_BUTTON"); ?></button>
		</td>
		</tr>
		</table>
		<script  type="text/javascript">
		function clearDetailsForm ()
		{
		document.getElementById('filterfreetextcriteria').value = '';
		document.getElementById('filter_visible').value = '';
		document.getElementById('partner_id').value = '';
		document.getElementById('filter_orderable').value = '';
		document.getElementById('filter_theme').value = '';
		document.getElementById("bboxMinX").value = "-180";
		document.getElementById("bboxMinY").value ="-90";
		document.getElementById("bboxMaxX").value ="180";
		document.getElementById("bboxMaxY").value ="90";
		}
		function clearForm()
		{
		document.getElementById('simple_filterfreetextcriteria').value = '';
		}
		</script>
		<?php
		//		echo $tabs->endPanel();
		//		echo $tabs->endPane();
		?>
		</form>


		<?php if($cswResults){ ?> <br/>
		<table width="100%">
		<tr>
		<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
		<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
		</table>
		<h3><?php echo JText::_("EASYSDI_SEARCH_RESULTS_TITLE"); ?></h3>

		<span class="easysdi_number_of_metadata_found"><?php echo JText::_("EASYSDI_CATALOG_NUMBER_OF_METADATA_FOUND");?>
		<?php echo $total ?> </span>
		<table class="mdsearchresult">
		<!--
		<thead>
		<tr>

		<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_SHARP'); ?></th>
		<th><?php echo JText::_('EASYSDI_CATALOG_ORDERABLE'); ?></th>

		<th><?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO'); ?></th>
		<th><?php echo JText::_('EASYSDI_CATALOG_PRODUCT_NAME'); ?></th>
		</tr>
		</thead>
		-->
		<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);



		$xpath = new DomXPath($cswResults);
		$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
		$nodes = $xpath->query('//gmd:MD_Metadata');

		foreach($nodes  as $metadata){
			
		$i++;
			
		$md = new geoMetadata($metadata);
		?>
		<tr>
		<!-- <td><?php echo $i; ?></td>  -->
		<?php
		$query = "select count(*) from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
		$db->setQuery( $query);

		$md_orderable = $db->loadResult();

		if ($db->getErrorNum()) {
		$md_orderable = '0';
		}


		$query = "select count(*) from #__easysdi_product where previewBaseMapId is not null AND previewBaseMapId>0 AND metadata_id = '".$md->getFileIdentifier()."'";

		$db->setQuery( $query);

		$hasPreview = $db->loadResult();
		if ($db->getErrorNum()) {
		$hasPreview = 0;

		}

		$queryPartnerID = "select partner_id from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
		$db->setQuery($queryPartnerID);
		$partner_id = $db->loadResult();
			
		$queryPartnerLogo = "select partner_logo from #__easysdi_community_partner where partner_id = ".$partner_id;
		$db->setQuery($queryPartnerLogo);
		$partner_logo = $db->loadResult();
			
		$query="select CONCAT( CONCAT( a.address_agent_firstname, ' ' ) , a.address_agent_lastname ) AS name from #__easysdi_community_partner p inner join #__easysdi_community_address a on p.partner_id = a.partner_id WHERE p.partner_id = ".$partner_id ." and a.type_id=1" ;
		$db->setQuery($query);
		$supplier= $db->loadResult();
			
		$user =& JFactory::getUser();
		$language = $user->getParam('language', '');
			
		$logoWidth = config_easysdi::getValue("logo_width");
		$logoHeight = config_easysdi::getValue("logo_height");

		$isMdPublic = false;
		$isMdFree = true;
		if( $md_orderable != 0)
		{
		//Define if the md is free or not
		$queryPartnerID = "select is_free from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
		$db->setQuery($queryPartnerID);
		$is_free = $db->loadResult();
		if($is_free == 0)
		{
		$isMdFree = false;
		}

		//Define if the md is public or not
		$queryPartnerID = "select external from #__easysdi_product where metadata_id = '".$md->getFileIdentifier()."'";
		$db->setQuery($queryPartnerID);
		$external = $db->loadResult();
		if($external == 1)
		{
		$isMdPublic = true;
		}
		}
			
			
		?>

		<td valign="top" rowspan=3>
		<img width="<?php echo $logoWidth ?>px" height="<?php echo $logoHeight ?>px" src="<?php echo $partner_logo;?>" alt="<?php echo JText::_('EASYSDI_CATALOG_ROOT_LOGO');?>"></img>
		</td>
		<td colspan="3"><span class="mdtitle"><a><?php echo $md->getDataIdentificationTitle();?></a></span>
		</td>
		<td valign="top" rowspan=2>
		<table id="info_md">
		<tr>
		<td><div <?php if($isMdPublic) echo 'class="publicMd"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_PRIVATEMD").'" class="privateMd"';?>></div></td>
		</tr>
		<tr>
		<td><div <?php if($md_orderable>0) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_ORDERABLE").'" class="easysdi_product_exists"'; else echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_NOTORDERABLE").'" class="easysdi_product_does_not_exist"';?>></div></td>
		</tr>
		<tr>
		<td><div <?php if($isMdFree) echo 'title="'.JText::_("EASYSDI_CATALOG_INFOLOGO_FREEMD").'" class="freeMd"'; else echo 'class="notFreeMd"';?>></div></td>
		</tr>
		</table>
		</td>
	 </tr>
	 <tr>
	 <td colspan="3"><span class="mddescr"><?php echo substr($md->getDescription($language), 0, $maxDescr); if(strlen($md->getDescription($language))>$maxDescr)echo" [...]";?></span></td>
	 </tr>
	 <tr>
	 <!--
	 <a	class="<?php if ($md_orderable>0) {echo "easysdi_orderable";} else {echo "easysdi_not_orderable";} ?>"
	 href="./index.php?option=com_easysdi_shop&view=shop" target="_self"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
	 </a>
	 -->
	 <td><span class="mdviewfile">
	 <a class="modal"
	 title="<?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>"
	 href="./index.php?tmpl=component&option=com_easysdi_core&task=showMetadata&id=<?php echo $md->getFileIdentifier();  ?>"
	 rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_VIEW_MD_FILE"); ?>
	 </a></span>
	 </td>
	 <?php if ($hasPreview > 0){ ?>
	 <td><span class="mdviewproduct">
	 <a class="modal" href="./index.php?tmpl=component&option=com_easysdi_catalog&task=previewProduct&metadata_id=<?php echo $md->getFileIdentifier();?>"
	 rel="{handler:'iframe',size:{x:650,y:550}}"><?php echo JText::_("EASYSDI_PREVIEW_PRODUCT"); ?></a></span>
	 </td>
		<?php } ?>
		<td>&nbsp;</td>
	 </tr>
	 <tr>
	 <td colspan="4">&nbsp;</td>
	 </tr>


	 <?php
	 }
	 ?>
	 </table>

	 <?php } ?></div>


	 </div>

	 <?php

	 }
	 */
	function generateMap()
	{

		?>
<script
	type="text/javascript"
	src="administrator/components/com_easysdi_core/common/lib/js/openlayers2.7/OpenLayers.js"></script>
<script
	type="text/javascript"
	src="administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/proj4js.js"></script>
<script
	type="text/javascript"
	src="administrator/components/com_easysdi_core/common/lib/js/proj4js/lib/defs/EPSG21781.js"></script>

<script type="text/javascript">
		
		var vectorsCatalog;            
		var mapCatalog;
		var baseLayerVectorCatalog;
		
		function setAlpha(imageformat)
		{
			var filter = false;
			if (imageformat.toLowerCase().indexOf("png") > -1) {
				filter = OpenLayers.Util.alphaHack(); 
			}
			return filter;
		}
		
		function initMapCatalog(){
			
		 <?php
		global  $mainframe;
		$db =& JFactory::getDBO();
		$query = "select * from #__easysdi_basemap_definition where def = 1"; 
		$db->setQuery( $query);
		$rows = $db->loadObjectList();		  
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		?>
					var options = {
			    	projection: "<?php echo $rows[0]->projection; ?>",
		            displayProjection: new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),
		            units: "<?php echo $rows[0]->unit; ?>",
					<?php if ($rows[0]->projection == "EPSG:4326") {}else{ ?>
		            minScale: <?php echo $rows[0]->minResolution; ?>,
		            maxScale: <?php echo $rows[0]->maxResolution; ?>,                
					<?php } ?>
		            maxExtent: new OpenLayers.Bounds(<?php echo $rows[0]->maxExtent; ?>)
					};
			mapCatalog = new OpenLayers.Map("mapCatalog",options);
			
			baseLayerVectorCatalog = new OpenLayers.Layer.Vector("BackGround Catalog",{isBaseLayer: true,transparent: "true"}); 
			mapCatalog.addLayer(baseLayerVectorCatalog);
		
		<?php
		
		$query = "select * from #__easysdi_basemap_content where basemap_def_id = ".$rows[0]->id." order by ordering"; 
		$db->setQuery( $query);
		$rows = $db->loadObjectList();		  
		if ($db->getErrorNum()) {						
					echo "<div class='alert'>";			
					echo 			$db->getErrorMsg();
					echo "</div>";
		}
		$i=0;
		foreach ($rows as $row){				  
		?>				
						  
						layer<?php echo $i; ?> = new OpenLayers.Layer.<?php echo $row->url_type; ?>( "<?php echo $row->name; ?>",
		                    <?php 
							if ($row->user != null && strlen($row->user)>0){
								//if a user and password is requested then use the joomla proxy.
								$proxyhost = config_easysdi::getValue("PROXYHOST");
								$proxyhost = $proxyhost."&type=wms&basemapscontentid=$row->id&url=";
								echo "\"$proxyhost".urlencode  (trim($row->url))."\",";												
							}else{	
								//if no user and password then don't use any proxy.					
								echo "\"$row->url\",";	
							}					
							?>
		                    
		                    {layers: '<?php echo $row->layers; ?>', format : "<?php echo $row->img_format; ?>",transparent: "true"},                                          
		                     {singleTile: <?php echo $row->singletile; ?>},                                                    
		                     {     
		                      maxExtent: new OpenLayers.Bounds(<?php echo $row->maxExtent; ?>),
		                      	<?php if ($rowsBaseMap->projection == "EPSG:4326") {}else{ ?>
		                      	minScale: <?php echo $row->minResolution; ?>,
		                        maxScale: <?php echo $row->maxResolution; ?>,
		                        <?php } ?>                     
		                     projection:"<?php echo $row->projection; ?>",
		                      units: "<?php echo $row->unit; ?>",
		                      transparent: "true"
		                     }
		                    );
		                  <?php
			                    if (strtoupper($row->url_type) =="WMS")
			                    {
			                    	?>
			                    	layer<?php echo $i; ?>.alpha = setAlpha('image/png');
			                    	<?php
			                    } 
			                    ?>
		                 mapCatalog.addLayer(layer<?php echo $i; ?>);
		<?php 
		$i++;
		} ?>                    
		
					 
				mapCatalog.events.register("zoomend", null, 
							function() { 
								document.getElementById('previousExtent').value = mapCatalog.getExtent().toBBOX();
							})
		                
		               mapCatalog.addControl(new OpenLayers.Control.LayerSwitcher());
		                mapCatalog.addControl(new OpenLayers.Control.Attribution());                                
		            vectorsCatalog = new OpenLayers.Layer.Vector(
		                "Vector Layer",
		                {isBaseLayer: false,transparent: "true"                                
		                }
		            );
		            
		           
		                       
		                        
		            mapCatalog.addLayer(vectorsCatalog);
		           <?php
					if ( JRequest::getVar('previousExtent') != "")
					{
						?>
							mapCatalog.zoomToExtent(new OpenLayers.Bounds(<?php echo JRequest::getVar('previousExtent'); ?>) );
						<?php
					}
					else
					{
						?>
							mapCatalog.zoomToMaxExtent();
						<?php	
					}
				?>
		            
		            var containerPanel = document.getElementById("panelDiv");
		            var panel = new OpenLayers.Control.Panel({div: containerPanel});
		            
				  var panelEdition = new OpenLayers.Control.Panel({div: containerPanel});
		          rectControl = new OpenLayers.Control.DrawFeature(vectorsCatalog, OpenLayers.Handler.RegularPolygon,{'displayClass':'olControlDrawFeatureRectangle'});
				  rectControl.featureAdded = function(event) { removeSelection();setLonLat(event);};												
				  rectControl.handler.setOptions({irregular: true});                                  
		          panelEdition.addControls([rectControl] );
		          mapCatalog.addControl(panelEdition);      
		          showSelection();   	
		}
		
		function showSelection(){
		
			if (document.getElementById("bboxMinX").value == "-180" && 	
		  		document.getElementById("bboxMinY").value == "-90" &&
		  		document.getElementById("bboxMaxX").value == "180" &&  	
		  		document.getElementById("bboxMaxY").value == "90" )
		  		{
		  		//show nothing
		  		return;
		  		} 
		  		else
		  		{
		  			//alert ('in show selection');
		  			bounds = new OpenLayers.Bounds(document.getElementById("bboxMinX").value,document.getElementById("bboxMinY").value,document.getElementById("bboxMaxX").value,document.getElementById("bboxMaxY").value).toGeometry();
		  			bounds.transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"));
		  			vectorsCatalog.addFeatures([new OpenLayers.Feature.Vector(bounds )]);
		  			
		  		}
		
		}
		
		function removeSelection()
		{
			if (vectorsCatalog.features.length > 1)
			{
				vectorsCatalog.removeFeatures(vectorsCatalog.features[0]);
			}
		}
		
		function setLonLat(feature)
		{
			var bounds = feature.geometry.getBounds();
			var transformedBounds =  	bounds.transform(new OpenLayers.Projection("<?php echo $rows[0]->projection; ?>"),new OpenLayers.Projection("EPSG:4326"));
		  	  	   	  	 
		  document.getElementById("bboxMinX").value =transformedBounds.left; 	
		  document.getElementById("bboxMinY").value =transformedBounds.bottom;
		  document.getElementById("bboxMaxX").value =transformedBounds.right; 	
		  document.getElementById("bboxMaxY").value =transformedBounds.top;
		  
		  
		}
		</script>

<!-- Geographic filter deactivated for now...
		<table >
			<tr>
				<td >
				<fieldset>
				<legend><?php echo JText::_("EASYSDI_PUBLISH_CARTO_FILTER"); ?></legend>
					<table>
						<tr>
							<td>
						
							</td>
							<td>
								<div id="mapCatalog"  class="tinymap"></div>
						
							</td>
							</tr>
							<tr>
							<td>
						
							</td>
							<td>
								<div id="panelDiv" class="olControlEditingToolbar"></div>
							</td>
						</tr>
					</table>
				</fieldset>
				</td>
			</tr>
		</table>
		 -->

<input
	type='hidden' id='previousExtent' name='previousExtent'
	value="<?php echo JRequest::getVar('previousExtent'); ?>" />
<script>
		
		/* Geographic filter deactivated for now...
			window.onload=function()
				{	
					initMapCatalog();
				}
		*/
			</script>
<br />
<div id="docs"></div>

<br />
				<?php

	}

	function alter_array_value_with_Jtext(&$rows)
	{
		if (count($rows)>0)
		{
			foreach($rows as $key => $row)
			{
				$rows[$key]->text = JText::_($rows[$key]->text);
			}
		}
	}

	function getPages($pageNav)
	{
		echo 'getPages';
		//$list = array();
		$pages = $pageNav->getPagesLinks();
		$links = $pages['pages'];
		foreach ($links as $page )
		{
			echo $page;
		}
		//return $list;
	}

	/**
	 * Generates an HTML radio list
	 *
	 * @param array An array of objects
	 * @param string The value of the HTML name attribute
	 * @param string Additional HTML attributes for the <select> tag
	 * @param mixed The key that is selected
	 * @param string The name of the object variable for the option value
	 * @param string The name of the object variable for the option text
	 * @returns string HTML for the select list
	 */
	function checkboxlist( $arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = null, $idtag = false, $translate = false )
	{
		reset( $arr );
		$html = '';

		if (is_array($attribs)) {
			$attribs = JArrayHelper::toString($attribs);
		}

		$id_text = $name;
		if ( $idtag ) {
			$id_text = $idtag;
		}

		for ($i=0, $n=count( $arr ); $i < $n; $i++ )
		{
			$k    = $arr[$i]->$key;
			$t    = $translate ? JText::_( $arr[$i]->$text ) : $arr[$i]->$text;
			$id    = ( isset($arr[$i]->id) ? @$arr[$i]->id : null);

			$extra    = '';
			$extra    .= $id ? " id=\"" . $arr[$i]->id . "\"" : '';
			if (is_array( $selected ))
			{
				foreach ($selected as $val)
				{
					$k2 = is_object( $val ) ? $val->$key : $val;
						
					if ($k == $k2)
					{
						$extra .= " checked=\"checked\"";
						break;
					}
				}
			} else {
				$extra .= ((string)$k == (string)$selected ? " checked=\"checked\"" : '');
			}
			$html .= "\n\t<input type=\"checkbox\" name=\"$name\" id=\"$id_text$k\" value=\"".$k."\"$extra $attribs />";
			$html .= "\n\t<label for=\"$id_text$k\">$t</label>";
		}
		$html .= "\n";
		return $html;
	}
}


?>