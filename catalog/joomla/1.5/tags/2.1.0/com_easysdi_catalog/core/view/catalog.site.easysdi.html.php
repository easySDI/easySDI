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
		$user =& JFactory::getUser();
		$app =& JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 
		//$templateDir =  JURI::base() . 'templates/'.$this->template.'/' ;
		
		$listMaxLength = config_easysdi::getValue("CATALOG_SEARCH_MULTILIST_LENGTH");
		
		// Scripts
		JHTML::script('catalog.js', 'administrator/components/com_easysdi_catalog/js/');
		JHTML::script('search.js', 'administrator/components/com_easysdi_catalog/js/');
		
		// Deux parties de la recherche
		$simulatedTabIndex = JRequest::getVar('simulatedTabIndex');
		$advancedSrch = JRequest::getVar('advancedSrch',0);

		// Choix radio pour les versions 
		$versions = array(
		JHTML::_('select.option',  '0', JText::_( 'CATALOG_SEARCH_VERSIONS_CURRENT' ) ),
		JHTML::_('select.option',  '1', JText::_( 'CATALOG_SEARCH_VERSIONS_ALL' ) )
		);

		// Types d'objet du contexte
		$objecttypes = array();
		if ($context <> "")
		{
			$db->setQuery("SELECT ot.id AS value, t.label as text 
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
									WHERE c.code = '".$context."')
				 ORDER BY ot.ordering");
		}
		else
		{
			$db->setQuery("SELECT ot.id AS value, t.label as text 
				 FROM #__sdi_objecttype ot 
				 INNER JOIN #__sdi_translation t ON t.element_guid=ot.guid
				 INNER JOIN #__sdi_language l ON t.language_id=l.id
				 INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id
				 WHERE ot.predefined=false 
				 	   AND cl.code='".$language->_lang."'
				 ORDER BY ot.ordering");
		}
		$objecttypes = array_merge( $objecttypes, $db->loadObjectList() );
		HTML_catalog::alter_array_value_with_Jtext($objecttypes);
		?>
		<script text="javascript">
		function toggleMe(obj){
			obj.value = (obj.checked)? 1:0 ;
		};
		function toggleChecked(id){
			document.getElementById(id).checked = document.getElementById(id).value ? 1: 0;
		}

		
		</script>
<div id="page">
<h1 class="contentheading"><?php echo JText::_("CATALOG_SEARCH_TITLE"); ?></h1>
<div class="contentin">

<h3><?php echo JText::_("CATALOG_SEARCH_CRITERIA_TITLE"); ?></h3>


<form name="catalog_search_form" id="catalog_search_form" method="GET" action="">
	<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" /> 
	<input type="hidden" name="view" id="view" value="<?php echo JRequest::getVar('view' );?>" /> 
	<input type="hidden" name="context" id="context" value="<?php echo JRequest::getVar('context' );?>" /> 
	<input type="hidden" name="bboxMinX" id="bboxMinX" value="<?php echo JRequest::getVar('bboxMinX', "-180" );?>" /> 
	<input type="hidden" name="bboxMinY" id="bboxMinY" value="<?php echo JRequest::getVar('bboxMinY', "-90" );?>" /> 
	<input type="hidden" name="bboxMaxX" id="bboxMaxX" value="<?php echo JRequest::getVar('bboxMaxX', "180" ); ?>" /> 
	<input type="hidden" name="bboxMaxY" id="bboxMaxY" value="<?php echo JRequest::getVar('bboxMaxY', "90" );?>" /> 
	<input type="hidden" name="Itemid" id="Itemid" value="<?php echo JRequest::getVar('Itemid');?>" /> 
	<input type="hidden" name="lang" id="lang" value="<?php echo JRequest::getVar('lang');?>" /> 
	<input type="hidden" name="tabIndex" id="tabIndex" value="" /> 
	<input type="hidden" name="limit" id="limit" value="20" /> 
	<input type="hidden" name="limitstart" id="limitstart" value="0" /> 
	<input type="hidden" name="tabIndex" id="tabIndex" value="" /> 
	<input type="hidden" name="simulatedTabIndex" id="simulatedTabIndex" value="<?php echo JRequest::getVar('simulatedTabIndex');?>" /> 
	<input type="hidden" name="advancedSrch" id="advancedSrch" value="<?php echo JRequest::getVar('advancedSrch', 0);?>" /> 

<!-- This is the simple search -->
<div id ="divSimpleSearch" class="row">
	<?php
	foreach($listSimpleFilters as $searchFilter)
	{
		switch ($searchFilter->attributetype_code)
		{
			case "guid":
			case "text":
			case "locale":
			case "number":
			case "link":
				/* Fonctionnement texte */
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<input type="text"
						id="<?php echo 'filter_'.$searchFilter->guid;?>"
						name="<?php echo 'filter_'.$searchFilter->guid;?>"
						value="<?php echo JRequest::getVar('filter_'.$searchFilter->guid);?>"
						class="inputbox text full" />
				</div>
				<?php
				break;
			case "textchoice":
			case "localechoice":
				/* Fonctionnement liste de choix*/
				$choicevalues=array();
				$choicevalues[] = JHTML::_('select.option','', '');
				$db->setQuery( "SELECT c.*, t.title, t.content FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
				$list = $db->loadObjectList();
				
				// Si la premi�re entr�e a un titre, construire une liste sur le titre
				if ($list[0]->title <> "")
				{
					$db->setQuery( "SELECT c.id as value, t.title as text FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
					$choicevalues = array_merge( $choicevalues, $db->loadObjectList() );
				}
				// Sinon, construire une liste sur le contenu
				else
				{
					$db->setQuery( "SELECT c.id as value, IF (LENGTH(t.content)>50,CONCAT(LEFT(t.content, 50), '...'),t.content) as text FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
					$choicevalues = array_merge( $choicevalues, $db->loadObjectList() );
				}
				
				$size=(int)$listMaxLength;
				if (count($choicevalues) < (int)$listMaxLength)
					$size = count($choicevalues);
					
				$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchFilter->guid)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<?php echo JHTML::_("select.genericlist", $choicevalues, 'filter_'.$searchFilter->guid.'[]', 'class="inputbox text full" style="vertical-align:top" '.$multiple, 'value', 'text', JRequest::getVar('filter_'.$searchFilter->guid)); ?>
					<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
						<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
					</a>
				</div>
				<?php
				break;
			case "list":
				/* Fonctionnement liste*/
				$list = array();
				$list[] = JHTML::_('select.option', '', '');
				$query = "SELECT cv.value as value, t.label as text FROM #__sdi_attribute a RIGHT OUTER JOIN #__sdi_codevalue cv ON a.id=cv.attribute_id INNER JOIN #__sdi_translation t ON t.element_guid=cv.guid INNER JOIN #__sdi_language l ON t.language_id=l.id INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id WHERE cl.code='".$language->_lang."' AND a.id=".$searchFilter->attribute_id;
				$db->setQuery( $query);
				$list = array_merge( $list, $db->loadObjectList() );
			
				$size=(int)$listMaxLength;
				if (count($list) < (int)$listMaxLength)
					$size = count($list);
					
				$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchFilter->guid)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<?php echo JHTML::_("select.genericlist", $list, 'filter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('filter_'.$searchFilter->guid)); ?>
					<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
						<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
					</a>
				</div>
				<?php
				break;
			case "date":
			case "datetime":
				/* Fonctionnement p�riode*/
				?>
				<div class="row">
					<div class="label"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></div>
					<div class="checkbox">
						<div>
							<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
							<?php echo helper_easysdi::calendar(JRequest::getVar('create_cal_'.$searchFilter->guid), "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div>
						<div>
							<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
							<?php echo helper_easysdi::calendar(JRequest::getVar('update_cal_'.$searchFilter->guid), "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div>
					</div>
				</div>
				<?php
				break;
			case null: // Cas des attributs syst�mes, car ils n'ont pas de relation li�e
				if ($searchFilter->criteriatype_id == 1) // Attributs syst�me
				{	
					switch ($searchFilter->criteria_code)
					{
						case "objecttype":
							$selectedObjectType = array();
							if (JRequest::getVar('systemfilter_'.$searchFilter->guid) and JRequest::getVar('systemfilter_'.$searchFilter->guid) <> "Array")
								$selectedObjectType = JRequest::getVar('systemfilter_'.$searchFilter->guid);
							else if (!JRequest::getVar('bboxMinX') or JRequest::getVar('systemfilter_'.$searchFilter->guid) == "Array")
								$selectedObjectType = array();
							
							?>
							<div class="row">
								<div class="label">
									<?php echo JText::_($searchFilter->guid."_LABEL");?>
								</div>
								<div class="checkbox">
									<?php echo helper_easysdi::checkboxlist($objecttypes, 'systemfilter_'.$searchFilter->guid.'[]', 'size="1" class="inputbox checkbox" ', 'class="inputbox checkbox"', 'value', 'text', $selectedObjectType); ?>
								</div>
							</div>
							<?php
							break;
							
							case "definedBoundary":
						
								$boundaries = array();
								$db->setQuery( "SELECT name, guid FROM #__sdi_boundary") ;
								$boundaries = $db->loadObjectList() ;								
								$selectedValue = trim(JRequest::getVar('systemfilter_'.$searchFilter->guid, ""));
								?>

							<div class="row">
								<div class="label">
								<?php echo JText::_($searchFilter->guid."_LABEL");?>
								</div>
								<div>
								<select name="<?php echo 'systemfilter_'.$searchFilter->guid;?>" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>">
									<option value="" <?php if($selectedValue ==""){?> selected="selected" <?Php }?> 
									
									></option>
									<?php foreach ($boundaries as $boundary){
								    ?> echo <option value="<?php echo JText::_($boundary->guid);?>" <?php if($selectedValue == trim($boundary->guid)){?> selected="selected" <?Php }?> ><?php echo JText::_($boundary->name);?></option>
								   <?php }?>
								</select>
								</div>
								
							</div>					
						<?php
						break;
						case "fulltext":
							?>
							<div class="row">
								<label for="simple_filterfreetextcriteria"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="text" id="simple_filterfreetextcriteria"
									name="simple_filterfreetextcriteria"
									value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>"
									class="inputbox text full" />
							</div>
							<?php
							break;
						case "versions":
							$selectedVersion = 0;
							if (JRequest::getVar('systemfilter_'.$searchFilter->guid))
								$selectedVersion = JRequest::getVar('systemfilter_'.$searchFilter->guid);
							?>	
							<div class="row">
								<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
								<div class="checkbox">
								<?php echo helper_easysdi::radiolist($versions, 'systemfilter_'.$searchFilter->guid, 'class="checkbox"', 'class="checkbox"', 'value', 'text', $selectedVersion); ?>
								</div>
							</div>
							<?php
							break;
						/* Only accounts that have at least one md */
						case "account_id":
							/* Fonctionnement liste*/
							$accounts = array();
							$accounts[] = JHTML::_('select.option', '', '');
							$query = "SELECT DISTINCT a.id as value, a.name as text 
									  FROM #__sdi_account a, #__sdi_object o, #__sdi_objectversion ov, #__users u 
									  WHERE u.id=a.user_id AND a.id=o.account_id AND o.id=ov.object_id AND a.root_id IS NULL 
									  ORDER BY a.name
									  ";
							$db->setQuery( $query);
							$accounts = array_merge( $accounts, $db->loadObjectList() );
						
							$size=(int)$listMaxLength;
							if (count($accounts) < (int)$listMaxLength)
								$size = count($accounts);					
							
							$multiple = 'size="1"';
							
							if (count(JRequest::getVar('systemfilter_'.$searchFilter->guid)) > 1)
								$multiple='size="'.$size.'" multiple="multiple"';
							
							?>
							<div class="row">
								<label for="<?php echo $searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<?php echo JHTML::_("select.genericlist", $accounts, 'systemfilter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('systemfilter_'.$searchFilter->guid)); ?>
										<a onclick="javascript:toggle_multi_select('<?php echo 'systemfilter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
											<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
										</a>
							</div>
							<?php
							break;
						case "object_name":
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="text" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									value="<?php echo JRequest::getVar('systemfilter_'.$searchFilter->guid);?>"
									class="inputbox text full" />
							</div>
							<?php
							break;
						case "title":
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="text" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									value="<?php echo JRequest::getVar('systemfilter_'.$searchFilter->guid);?>"
									class="inputbox text full" />
							</div>
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
							$managers = array_merge( $managers, $db->loadObjectList() );
						
							$size=(int)$listMaxLength;
							if (count($managers) < (int)$listMaxLength)
								$size = count($managers);
							
							$multiple = 'size="1"';
							if (count(JRequest::getVar('systemfilter_'.$searchFilter->guid)) > 1)
								$multiple='size="'.$size.'" multiple="multiple"';
							
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid; ?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<?php echo JHTML::_("select.genericlist", $managers, 'systemfilter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('systemfilter_'.$searchFilter->guid)); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo 'systemfilter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
								</a>
							</div>
							<?php
							break;
						case "metadata_created":
							/* Fonctionnement p�riode*/
							?>
							<div class="row">
								<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
								<div class="checkbox">
								<div>
								<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
								<?php echo helper_easysdi::calendar(JRequest::getVar('create_cal_'.$searchFilter->guid), "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div><div>
								<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
								<?php echo helper_easysdi::calendar(JRequest::getVar('update_cal_'.$searchFilter->guid), "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div></div>
							</div>
							<?php
							break;
						case "metadata_published":
							/* Fonctionnement p�riode*/
							?>
							<div class="row">
								<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
								<div class="checkbox">
								<div>
								<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
								<?php echo helper_easysdi::calendar(JRequest::getVar('create_cal_'.$searchFilter->guid), "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div><div>
								<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
								<?php echo helper_easysdi::calendar(JRequest::getVar('update_cal_'.$searchFilter->guid), "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div></div>
							</div>
							<?php
							break;
						case "isDownloadable":
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="checkbox" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									value="<?php echo JRequest::getVar('systemfilter_'.$searchFilter->guid, 0);?>"									
									class="inputbox checkbox" 
									onClick="toggleMe(this)"
									<?php if( JRequest::getVar('systemfilter_'.$searchFilter->guid)==1){
									echo "checked = true";
									}									
									?>	 />
								
							</div>
							<?php					
						break;
							
						default:
							break;
				}
				break;
			}
			else // Cas des attributs OGC qui ne sont pas li�s � une relation
			{
				switch ($searchFilter->rendertype_code)
				{
				case "date":
					/* Fonctionnement p�riode*/
					?>
					<div class="row">
						<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
						<div class="checkbox">
						<div>
						<label class="checkbox" for="<?php echo "filter_create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
						<?php echo helper_easysdi::calendar(JRequest::getVar('filter_create_cal_'.$searchFilter->guid), "filter_create_cal_".$searchFilter->guid,"filter_create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div><div>
						<label class="checkbox" for="<?php echo "filter_update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
						<?php echo helper_easysdi::calendar(JRequest::getVar('filter_update_cal_'.$searchFilter->guid), "filter_update_cal_".$searchFilter->guid,"filter_update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div></div>
					</div>
					<?php
					break;
				case "textbox":
				default:
				/* Fonctionnement texte*/
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
					<input type="text"
						id="<?php echo 'filter_'.$searchFilter->guid;?>"
						name="<?php echo 'filter_'.$searchFilter->guid;?>"
						value="<?php echo JRequest::getVar('filter_'.$searchFilter->guid);?>"
						class="inputbox text full" />
				</div>
				<?php
					break;
				}
			}
		default:
			break;
		}
	}
	?>
</div>

<!-- This is the advanced search -->
<div id="divAdvancedSearch" class="row">
	<?php

	
	foreach($listAdvancedFilters as $searchFilter)
	{

		switch ($searchFilter->attributetype_code)
		{
			case "guid":
			case "text":
			case "locale":
			case "number":
			case "link":
				/* Fonctionnement texte*/
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<input type="text"
						id="<?php echo 'filter_'.$searchFilter->guid;?>"
						name="<?php echo 'filter_'.$searchFilter->guid;?>"
						value="<?php echo JRequest::getVar('filter_'.$searchFilter->guid);?>"
						class="inputbox text full" />
				</div>
				<?php
				break;
			case "textchoice":
			case "localechoice":
				/* Fonctionnement liste de choix*/
				$choicevalues=array();
				$choicevalues[] = JHTML::_('select.option','', '');
				$db->setQuery( "SELECT c.*, t.title, t.content FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
				$list = $db->loadObjectList();
				
				// Si la premi�re entr�e a un titre, construire une liste sur le titre
				if ($list[0]->title <> "")
				{
					$db->setQuery( "SELECT c.id as value, t.title as text FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
					$choicevalues = array_merge( $choicevalues, $db->loadObjectList() );
				}
				// Sinon, construire une liste sur le contenu
				else
				{
					$db->setQuery( "SELECT c.id as value, IF (LENGTH(t.content)>50,CONCAT(LEFT(t.content, 50), '...'),t.content) as text FROM #__sdi_attribute a, #__sdi_list_attributetype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
					$choicevalues = array_merge( $choicevalues, $db->loadObjectList() );
				}
													 	
				$size=(int)$listMaxLength;
				if (count($choicevalues) < (int)$listMaxLength)
					$size = count($choicevalues);
					
				$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchFilter->guid)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<?php echo JHTML::_("select.genericlist", $choicevalues, 'filter_'.$searchFilter->guid.'[]', 'class="inputbox text full" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('filter_'.$searchFilter->guid)); ?>
					<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
						<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
					</a>
				</div>
				<?php
				break;
			case "list":
				/* Fonctionnement liste*/
				$list = array();
				$list[] = JHTML::_('select.option', '', '');
				$query = "SELECT cv.value as value, t.label as text FROM #__sdi_attribute a RIGHT OUTER JOIN #__sdi_codevalue cv ON a.id=cv.attribute_id INNER JOIN #__sdi_translation t ON t.element_guid=cv.guid INNER JOIN #__sdi_language l ON t.language_id=l.id INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id WHERE cl.code='".$language->_lang."' AND a.id=".$searchFilter->attribute_id;
				$db->setQuery( $query);
				$list = array_merge( $list, $db->loadObjectList() );
			
				$size=(int)$listMaxLength;
				if (count($list) < (int)$listMaxLength)
					$size = count($list);
					
				$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchFilter->guid)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<?php echo JHTML::_("select.genericlist", $list, 'filter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('filter_'.$searchFilter->guid)); ?>
					<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
						<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
					</a>
				</div>
				<?php
				break;
			case "date":
			case "datetime":
				/* Fonctionnement p�riode*/
				?>
				<div class="row">
					<div class="label"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></div>
					<div class="checkbox">
					<div>
					<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
					<?php echo helper_easysdi::calendar(JRequest::getVar('create_cal_'.$searchFilter->guid), "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</div><div>
					<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
					<?php echo helper_easysdi::calendar(JRequest::getVar('update_cal_'.$searchFilter->guid), "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</div></div>
				</div>
				<?php
				break;
			case null: // Cas des attributs qui ne sont pas li�s � une relation
				if ($searchFilter->criteriatype_id == 1) // Attributs syst�me
				{
					switch ($searchFilter->criteria_code)
					{
						case "objecttype":
							$selectedObjectType = array();
							if (JRequest::getVar('systemfilter_'.$searchFilter->guid) and JRequest::getVar('systemfilter_'.$searchFilter->guid) <> "Array")
								$selectedObjectType = JRequest::getVar('systemfilter_'.$searchFilter->guid);
							else if (!JRequest::getVar('bboxMinX') or JRequest::getVar('systemfilter_'.$searchFilter->guid) == "Array")
								$selectedObjectType = $objecttypes;
													
							?>
					<div class="row">
						<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
						<div class="checkbox">
						<?php echo helper_easysdi::checkboxlist($objecttypes, 'systemfilter_'.$searchFilter->guid.'[]', 'size="1" class="inputbox checkbox" ', 'class="inputbox checkbox"', 'value', 'text', $selectedObjectType); ?>
						</div>
					</div>
					<?php
					break;
					case "definedBoundary":
						
								$boundaries = array();
								$db->setQuery( "SELECT name, guid FROM #__sdi_boundary") ;
								$boundaries = $db->loadObjectList() ;								
								$selectedValue = trim(JRequest::getVar('systemfilter_'.$searchFilter->guid, ""));
								?>

							<div class="row">
								<div class="label">
								<?php echo JText::_($searchFilter->guid."_LABEL");?>
								</div>
								<div>
								<select name="<?php echo 'systemfilter_'.$searchFilter->guid;?>" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>">
									<option value="" <?php if($selectedValue ==""){?> selected="selected" <?Php }?> 
									
									></option>
									<?php foreach ($boundaries as $boundary){
								    ?> echo <option value="<?php echo JText::_($boundary->guid);?>" <?php if($selectedValue == trim($boundary->guid)){?> selected="selected" <?Php }?> ><?php echo JText::_($boundary->name);?></option>
								   <?php }?>
								</select>
								</div>
								
							</div>					
						<?php
						break;
				case "fulltext":
					?>
					<div class="row">
						<label for="simple_filterfreetextcriteria"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
						<input type="text" id="simple_filterfreetextcriteria"
							name="simple_filterfreetextcriteria"
							value="<?php echo JRequest::getVar('simple_filterfreetextcriteria');?>"
							class="inputbox text full" />
					</div>
					<?php
					break;
				case "isDownloadable":
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="checkbox" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									value="<?php echo JRequest::getVar('systemfilter_'.$searchFilter->guid);?>"									
									class="inputbox checkbox" 
									onClick="toggleMe(this)" 
									<?php if( JRequest::getVar('systemfilter_'.$searchFilter->guid)==1){
									echo "checked = true";
									}									
									?>	
									/>
									
									
							</div>
							<?php					
					break;
				case "versions":
					$selectedVersion = 0;
					if (JRequest::getVar('systemfilter_'.$searchFilter->guid))
						$selectedVersion = JRequest::getVar('systemfilter_'.$searchFilter->guid);
						
					?>
					<div class="row">
						<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
						<div class="checkbox">
						<?php echo helper_easysdi::radiolist($versions, 'systemfilter_'.$searchFilter->guid, 'class="checkbox"', 'class="checkbox"', 'value', 'text', $selectedVersion); ?>
						</div>
					</div>
					<?php
					break;
				case "account_id":
					/* Fonctionnement liste*/
					$accounts = array();
					$accounts[] = JHTML::_('select.option', '', '');
					$query = "SELECT DISTINCT a.id as value, a.name as text 
									  FROM #__sdi_account a, #__sdi_object o, #__sdi_objectversion ov, #__users u 
									  WHERE u.id=a.user_id AND a.id=o.account_id AND o.id=ov.object_id AND a.root_id IS NULL 
									  ORDER BY a.name
									  ";
					/*$query = "SELECT DISTINCT a.id as value, a.name as text 
							  FROM #__sdi_account a 
							  INNER JOIN #__users u ON u.id=a.user_id
							  WHERE a.root_id IS NULL 
							  ";*/
					$db->setQuery( $query);
					$accounts = array_merge( $accounts, $db->loadObjectList() );
				
					$size=(int)$listMaxLength;
					if (count($accounts) < (int)$listMaxLength)
						$size = count($accounts);
							
					$multiple = 'size="1"';
					if (count(JRequest::getVar('systemfilter_'.$searchFilter->guid)) > 1)
						$multiple='size="'.$size.'" multiple="multiple"';
					
					?>
					<div class="row">
						<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
						<?php echo JHTML::_("select.genericlist", $accounts, 'systemfilter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('systemfilter_'.$searchFilter->guid)); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo 'systemfilter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
								</a>
					</div>
					<?php
					break;
				case "object_name":
					?>
					<div class="row">
						<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
						<input type="text" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
							name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
							value="<?php echo JRequest::getVar('systemfilter_'.$searchFilter->guid);?>"
							class="inputbox text full" />
					</div>
					<?php
					break;
				case "title":
					?>
					<div class="row">
						<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
						<input type="text" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
							name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
							value="<?php echo JRequest::getVar('systemfilter_'.$searchFilter->guid);?>"
							class="inputbox text full" />
					</div>
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
					$managers = array_merge( $managers, $db->loadObjectList() );
				
					$size=(int)$listMaxLength;
					if (count($managers) < (int)$listMaxLength)
						$size = count($managers);
						
					$multiple = 'size="1"';
					if (count(JRequest::getVar('systemfilter_'.$searchFilter->guid)) > 1)
						$multiple='size="'.$size.'" multiple="multiple"';
					
					?>
					<div class="row">
						<label for="<?php echo 'systemfilter_'.$searchFilter->guid; ?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
						<?php echo JHTML::_("select.genericlist", $managers, 'systemfilter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', JRequest::getVar('systemfilter_'.$searchFilter->guid)); ?>
						<a onclick="javascript:toggle_multi_select('<?php echo 'systemfilter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
							<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
						</a>
					</div>
					<?php
					break;
				case "metadata_created":
					/* Fonctionnement p�riode*/
					?>
					<div class="row">
						<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
						<div class="checkbox">
						<div>
						<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
						<?php echo helper_easysdi::calendar(JRequest::getVar('create_cal_'.$searchFilter->guid), "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div><div>
						<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
						<?php echo helper_easysdi::calendar(JRequest::getVar('update_cal_'.$searchFilter->guid), "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div></div>
					</div>
					<?php
					break;
				case "metadata_published":
					/* Fonctionnement p�riode*/
					?>
					<div class="row">
						<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
						<div class="checkbox">
						<div>
						<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
						<?php echo helper_easysdi::calendar(JRequest::getVar('create_cal_'.$searchFilter->guid), "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div><div>
						<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
						<?php echo helper_easysdi::calendar(JRequest::getVar('update_cal_'.$searchFilter->guid), "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
						</div></div>
					</div>
					<?php
					break;
				default:
					break;
		}
		break;
		}
		else // Cas des attributs OGC qui ne sont pas li�s � une relation
		{
			switch ($searchFilter->rendertype_code)
			{
			case "date":
				/* Fonctionnement p�riode*/
				?>
				<div class="row">
					<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
					<div class="checkbox">
					<div>
					<label class="checkbox" for="<?php echo "filter_create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
					<?php echo helper_easysdi::calendar(JRequest::getVar('filter_create_cal_'.$searchFilter->guid), "filter_create_cal_".$searchFilter->guid,"filter_create_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</div><div>
					<label class="checkbox" for="<?php echo "filter_update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
					<?php echo helper_easysdi::calendar(JRequest::getVar('filter_update_cal_'.$searchFilter->guid), "filter_update_cal_".$searchFilter->guid,"filter_update_cal_".$searchFilter->guid,"%d.%m.%Y", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</div></div>
				</div>
				<?php
				break;
			case "textbox":
			default:
			/* Fonctionnement texte*/
			?>
			<div class="row">
				<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
				<input type="text"
					id="<?php echo 'filter_'.$searchFilter->guid;?>"
					name="<?php echo 'filter_'.$searchFilter->guid;?>"
					value="<?php echo JRequest::getVar('filter_'.$searchFilter->guid);?>"
					class="inputbox text full" />
			</div>
			<?php
				break;
			}
		}
default:
	break;
		}
	}
	?>
</div>

<!-- Les boutons Rechercher / Vider -->
<div class="row">
	<div class="checkbox row">
		<input type="checkbox" id="advSearchRadio" name="advSearchRadio" class="checkbox"/>
		<label for="advSearchRadio" class="checkbox"><?php echo JText::_("CATALOG_SEARCH_TEXT_ADVANCED_CRITERIA"); ?></label>
	</div>
</div>
<div class="row catalogActionButton">
	<input type="submit" id="simple_search_button" name="simple_search_button" class="easysdi_search_button submit" value ="<?php echo JText::_("CATALOG_SEARCH_SEARCH_BUTTON"); ?>"/>
	<input type="button" id="easysdi_clear_button" name="easysdi_clear_button" class="easysdi_clear_button submit" value ="<?php echo JText::_("CATALOG_SEARCH_CLEAR_BUTTON"); ?>"/>
</div>

</form>
</div>

<?php 
/*
 * Results
 */
if($cswResults)
{
	?>
	<div class="searchresults">
	<h3><?php echo JText::_("CATALOG_SEARCH_RESULTS_TITLE"); ?></h3>
	
	<!-- Count of results -->
	<p><strong><?php echo JText::_("CATALOG_SEARCH_NUMBER_OF_METADATA_FOUND");?></strong>&nbsp;<?php echo $total ?>&nbsp;</p>
	
	<div class="ticker">
	<?php
	$i=0;
	$param = array('size'=>array('x'=>800,'y'=>800) );
	JHTML::_("behavior.modal","a.modal",$param);
	
	// on indique bien qu'on veut les m�tadonn�es retourn�es, et pas tous les gmd:MD_Metadata qui pourraient se trouver
	// dans la construction de la m�tadonn�e (notamment les mauvais remplacements de mtadonn�es, pour les contacts p.ex.)
	$xpath = new DomXPath($cswResults);
	$xpath->registerNamespace('csw','http://www.opengis.net/cat/csw/2.0.2');
	$xpath->registerNamespace('gmd','http://www.isotc211.org/2005/gmd');
	$nodes = $xpath->query('//csw:SearchResults/gmd:MD_Metadata');
	
	// Build of extendend XML for each result entry
	foreach($nodes  as $metadata)
	{
		$i++;
		
		$md = new geoMetadata($metadata);
	
		$xmlBase = new DomDocument('1.0', 'UTF-8');
		$xmlBase = $md->metadata;
		$xmlBase->formatOutput = true;
	
		$doc = displayManager::constructXML($xmlBase, $db, $language, $md->getFileIdentifier(), false, null, null);
		
		// Répertoire des fichiers xsl, s'il y en a un
		$xslFolder = ""; 
		
		if (isset($context))
		{
			$db->setQuery("SELECT xsldirectory FROM #__sdi_context WHERE code='".$context."'");
			$xslFolder = $db->loadResult(); 
		}
		if ($xslFolder <> "")
			$xslFolder = $xslFolder."/";
		
		$style = new DomDocument();
		if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_result_'.$language->_lang.'.xsl')){
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_result_'.$language->_lang.'.xsl');
		}else{
			$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.$xslFolder.'XML2XHTML_result.xsl');
		}
	
		$processor = new xsltProcessor();
		$processor->importStylesheet($style);
		$xml = new DomDocument();
		$xml = $processor->transformToXml($doc);
	
		printf($xml);
	}
	?>
	</div>
	
	
	<div class="paging">
    	<h3 class="hidden"><?php JText::_('WEITERE TREFFER ANZEIGEN'); ?></h3>
    	<p class="info"><?php echo $pageNav->getPagesCounter(); ?></p>
		<p class="select"><?php echo $pageNav->getPagesLinks( ); ?></p>
  	</div>

	
	
	<?php 
	} 
	?>
	</div>
	</div>
	<?php
	}
	
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
}


?>