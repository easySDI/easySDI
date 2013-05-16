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

	function listCatalogContentWithPan ($pageNav,$cswResults,$option, $total,$searchCriteria,$maxDescr, $selectedVersions, $listSimpleFilters, $listAdvancedFilters, $listHiddenFilters)
	{
		global  $mainframe;
		$option= JRequest::getVar('option');
		$context= JRequest::getVar('context');
		$db =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		$user =& JFactory::getUser();
		$app =& JFactory::getApplication(); 
		$templateDir = JURI::base() . 'templates/' . $app->getTemplate(); 
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

		//Context guid
		$db->setQuery("SELECT guid FROM #__sdi_context WHERE code = '".$context."'");
		$contextguid = $db->loadResult();
		
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
		<script type="text/javascript">
		function toggleMe(obj){
			obj.value = (obj.checked)? 1:0 ;
		};
		function toggleChecked(id){
			document.getElementById(id).checked = document.getElementById(id).value ? 1: 0;
		}
				
		</script>
		
		<div id="page">
		<h1 class="contentheading"><?php echo JText::_($contextguid."_TITLE"); ?></h1>
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
			<input type="hidden" name="defaultSearch" id="defaultSearch" value="0" /> 
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
					HTML_catalog::generateFieldHTML ($searchFilter, $objecttypes,$listMaxLength,$versions,$templateDir);
				}
				?>
			</div>

			<!-- This is the advanced search -->
			<div id="divAdvancedSearch" class="row">
				<?php
				foreach($listAdvancedFilters as $searchFilter)
				{
					HTML_catalog::generateFieldHTML ($searchFilter, $objecttypes,$listMaxLength,$versions,$templateDir);
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
	
	
	function generateFieldHTML ( $searchFilter, $objecttypes,$listMaxLength,$versions,$templateDir){
		global  $mainframe;
		$db =& JFactory::getDBO();
		$defaultSearch = JRequest::getVar('defaultSearch', 1);
		$language =& JFactory::getLanguage();

	
		
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
						value="<?php echo $defaultSearch ? $searchFilter->defaultvalue : JRequest::getVar('filter_'.$searchFilter->guid);?>"
						class="inputbox text full" />
				</div>
				<?php
				break;
			case "textchoice":
			case "localechoice":
				/* Fonctionnement liste de choix*/
				$choicevalues=array();
				$choicevalues[] = JHTML::_('select.option','', '');
				$db->setQuery( "SELECT  c.*, 
										t.title, 
										t.content 
								FROM #__sdi_attribute a, 
									 #__sdi_sys_stereotype at,  
									 #__sdi_codevalue c, 
									 #__sdi_translation t, 
									 #__sdi_language l, 
									 #__sdi_list_codelang cl 
								WHERE a.id=c.attribute_id 
								AND a.attributetype_id=at.id 
								AND c.guid=t.element_guid 
								AND t.language_id=l.id 
								AND l.codelang_id=cl.id 
								and cl.code='".$language->_lang."' 
								AND (at.alias='textchoice' OR at.alias='localechoice') 
								AND attribute_id=".$searchFilter->attribute_id." 
								AND c.published=true 
								ORDER BY c.name" );
				$list = $db->loadObjectList();
				
				// Si la première entrée a un titre, construire une liste sur le titre
				if ($list[0]->title <> "")
				{
					$db->setQuery( "SELECT c.id as value, t.title as text FROM #__sdi_attribute a, #__sdi_sys_stereotype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
					$choicevalues = array_merge( $choicevalues, $db->loadObjectList() );
				}
				// Sinon, construire une liste sur le contenu
				else
				{
					$db->setQuery( "SELECT c.id as value, IF (LENGTH(t.content)>50,CONCAT(LEFT(t.content, 50), '...'),t.content) as text FROM #__sdi_attribute a, #__sdi_sys_stereotype at,  #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE a.id=c.attribute_id AND a.attributetype_id=at.id AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND (at.code='textchoice' OR at.code='localechoice') AND attribute_id=".$searchFilter->attribute_id." AND c.published=true ORDER BY c.name" );
					$choicevalues = array_merge( $choicevalues, $db->loadObjectList() );
				}
													 	
				$size=(int)$listMaxLength;
				if (count($choicevalues) < (int)$listMaxLength)
					$size = count($choicevalues);
					
				/*$multiple = 'size="1"';
				if (count(JRequest::getVar('filter_'.$searchFilter->guid)) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';*/
				
				//Load default values
				$defaultSelection=array();
				if ($defaultSearch)
					$defaultSelection = json_decode($searchFilter->defaultvalue );
				else
					$defaultSelection = JRequest::getVar('filter_'.$searchFilter->guid);
				
				$multiple = 'size="1"';
				if (count($defaultSelection) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<?php echo JHTML::_("select.genericlist", $choicevalues, 'filter_'.$searchFilter->guid.'[]', 'class="inputbox text full" style="vertical-align:top " '.$multiple, 'value', 'text', $defaultSelection); ?>
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
				$query = "SELECT cv.value as value, 
								 t.label as text 
							FROM #__sdi_attribute a 
							RIGHT OUTER JOIN #__sdi_codevalue cv ON a.id=cv.attribute_id 
							INNER JOIN #__sdi_translation t ON t.element_guid=cv.guid 
							INNER JOIN #__sdi_language l ON t.language_id=l.id 
							INNER JOIN #__sdi_list_codelang cl ON l.codelang_id=cl.id 
						WHERE cl.code='".$language->_lang."' 
						AND a.id=".$searchFilter->attribute_id;
				$db->setQuery( $query);
				$list = array_merge( $list, $db->loadObjectList() );
			
				$size=(int)$listMaxLength;
				if (count($list) < (int)$listMaxLength)
					$size = count($list);
				
				//Load default values
				$defaultSelection=array();
				if ($defaultSearch)
					$defaultSelection = json_decode($searchFilter->defaultvalue );
				else
					$defaultSelection = JRequest::getVar('filter_'.$searchFilter->guid);
				
				$multiple = 'size="1"';
				if (count($defaultSelection) > 1)
					$multiple='size="'.$size.'" multiple="multiple"';
				
				?>
				<div class="row">
					<label for="<?php echo 'filter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></label>
					<?php echo JHTML::_("select.genericlist", $list, 'filter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text', $defaultSelection); ?>
					<a onclick="javascript:toggle_multi_select('<?php echo 'filter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
						<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
					</a>
				</div>
				<?php
				break;
			case "date":
			case "datetime":
				/* Fonctionnement période*/
				if ($defaultSearch) {
					$valuefrom = ($searchFilter->defaultvaluefrom  == '0000-00-00')? null : $searchFilter->defaultvaluefrom  ;
					$valueto = ($searchFilter->defaultvalueto  == '0000-00-00')? null : $searchFilter->defaultvalueto  ;
				}else{
					$valuefrom = JRequest::getVar('create_cal_'.$searchFilter->guid);
					$valueto = JRequest::getVar('update_cal_'.$searchFilter->guid);
				}
				?>
				<div class="row">
					<div class="label"><?php echo JText::_($searchFilter->relation_guid."_LABEL");?></div>
					<div class="checkbox">
					<div>
					<label class="checkbox" for="<?php echo "create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
					<?php echo helper_easysdi::calendar($valuefrom, "create_cal_".$searchFilter->guid,"create_cal_".$searchFilter->guid,"%Y-%m-%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</div><div>
					<label class="checkbox" for="<?php echo "update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
					<?php echo helper_easysdi::calendar($valueto, "update_cal_".$searchFilter->guid,"update_cal_".$searchFilter->guid,"%Y-%m-%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
					</div></div>
				</div>
				<?php
				break;
			case null: // Cas des attributs qui ne sont pas liés à une relation
				if ($searchFilter->criteriatype_id == 1) // Attributs syst�me
				{
					switch ($searchFilter->criteria_code)
					{
						case "objecttype":
							$selectedObjectType = array();
							if ($defaultSearch)
								$selectedObjectType = json_decode($searchFilter->defaultvalue );
							else{
								if (JRequest::getVar('systemfilter_'.$searchFilter->guid) and JRequest::getVar('systemfilter_'.$searchFilter->guid) <> "Array")
									$selectedObjectType = JRequest::getVar('systemfilter_'.$searchFilter->guid);
								else if (!JRequest::getVar('bboxMinX') or JRequest::getVar('systemfilter_'.$searchFilter->guid) == "Array")
									$selectedObjectType = $objecttypes;
							}
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
							JHTML::script('ext-base.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
							JHTML::script('ext-all.js', 'administrator/components/com_easysdi_catalog/ext/');
							JHTML::_('stylesheet', 'catalog_search.css', 'administrator/components/com_easysdi_catalog/templates/css/');
							
							$params = json_decode($searchFilter->params);
							if(isset ($params->boundarycategory) && count($params->boundarycategory)>0){
								$category_list = implode(",", $params->boundarycategory);
							}
							
							$selectedValue = "";
							$selectedText = "";
							if ($defaultSearch){
								$selectedValue = $searchFilter->defaultvalue ;
							}else{					
								$selectedValue = trim(JRequest::getVar('systemfilter_'.$searchFilter->guid, ""));
							}
							if(strlen($selectedValue) > 0){
								$db->setQuery( "SELECT Concat(t.label,' [',tbc.label,']')  
												FROM #__sdi_boundary b
												INNER JOIN #__sdi_boundarycategory bc ON b.category_id = bc.id
												INNER JOIN #__sdi_translation tbc ON bc.guid = tbc.element_guid
												INNER JOIN #__sdi_language lbc ON tbc.language_id=lbc.id
												INNER JOIN #__sdi_list_codelang cbc ON lbc.codelang_id=cbc.id
												INNER JOIN #__sdi_translation t ON b.guid = t.element_guid
												INNER JOIN #__sdi_language l ON t.language_id=l.id
												INNER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id 
												WHERE b.guid ='".$selectedValue."'
												AND c.code='".$language->_lang."'
												AND cbc.code='".$language->_lang."' ");
								$selectedText = $db->loadResult() ;
							}
							?>
							<div class="row">
								<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
								<div id="catalogSearchFormExtentDiv" ></div>
							</div>	
							<script>
							var Tpl = new Ext.XTemplate('<tpl for="."><div class="search-item">{text}</div></tpl>');

							var contactStore= new Ext.data.Store({
								 reader: new Ext.data.JsonReader({
								        fields: ['value', 'text']
							        }),
								 proxy: new Ext.data.HttpProxy({
								    url: 'index.php?option=com_easysdi_catalog&task=getBoundariesByLabel&category=<?php echo $category_list ;?>'
								 }),
								 autoLoad:true
							});

							var combo = new Ext.form.ComboBox({
				                 id:'extentComboBox',
				                 hiddenName:'<?php echo  'systemfilter_'.$searchFilter->guid;?>',
				        		 valueField: 'value',
	                             displayField: 'text',
	                             minChars:0,
	                             tpl:Tpl,
	                             store:contactStore,
	                             hideLabel: true,
	                             typeAhead: false,
	                             hideTrigger:true,
	                             itemSelector: 'div.search-item',
	                             selectOnFocus: true,
	                             autoWidth:true,
	                             value:'<?php echo  $selectedText;?>',
	                             hiddenValue:'<?php echo  $selectedValue;?>',
	                             renderTo: document.getElementById('catalogSearchFormExtentDiv')
							});
							</script>				
							<?php
							break;
						case "fulltext":
							?>
							<div class="row">
								<label for="simple_filterfreetextcriteria"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="text" id="simple_filterfreetextcriteria"
									name="simple_filterfreetextcriteria"
									value="<?php echo $defaultSearch ? $searchFilter->defaultvalue : JRequest::getVar('simple_filterfreetextcriteria');?>"
									class="inputbox text full" />
							</div>
							<?php
							break;
						case "isFree":
						case "isOrderable": 
						case "isDownloadable":
							$valueToDisplay = "";
							if ($defaultSearch){
								if($searchFilter->defaultvalue == 1)
									$valueToDisplay = "checked = true";
							} else {
								if( JRequest::getVar('systemfilter_'.$searchFilter->guid)==1 )
									$valueToDisplay = "checked = true";
							}	
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<input type="checkbox" id="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									name="<?php echo 'systemfilter_'.$searchFilter->guid;?>"
									value="1"									
									class="inputbox checkbox" 
									onClick="toggleMe(this)" 
									<?php echo $valueToDisplay;?>	
									/>
							</div>
							<?php					
							break;
						case "versions":
							$selectedVersion = 0;
							if ($defaultSearch){
								$selectedVersion = $searchFilter->defaultvalue ;
							} else {
								if (JRequest::getVar('systemfilter_'.$searchFilter->guid))
									$selectedVersion = JRequest::getVar('systemfilter_'.$searchFilter->guid);
							}
							
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
							$db->setQuery( $query);
							$accounts = array_merge( $accounts, $db->loadObjectList() );
						
							$size=(int)$listMaxLength;
							if (count($accounts) < (int)$listMaxLength)
								$size = count($accounts);
													
							if ($defaultSearch)
								$selectedAccount = json_decode($searchFilter->defaultvalue );
							else
								$selectedAccount = JRequest::getVar('systemfilter_'.$searchFilter->guid);
								
							$multiple = 'size="1"';
							if (count($selectedAccount) > 1)
								$multiple='size="'.$size.'" multiple="multiple"';
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid;?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<?php echo JHTML::_("select.genericlist", $accounts, 'systemfilter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text',$selectedAccount ); ?>
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
									value="<?php echo $defaultSearch ? $searchFilter->defaultvalue : JRequest::getVar('systemfilter_'.$searchFilter->guid);?>"
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
									value="<?php echo $defaultSearch ? $searchFilter->defaultvalue : JRequest::getVar('simple_filterfreetextcriteria');?>"
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
								
							if ($defaultSearch)
								$selectedAccount = json_decode($searchFilter->defaultvalue );
							else
								$selectedAccount = JRequest::getVar('systemfilter_'.$searchFilter->guid);
							
							$multiple = 'size="1"';
							if (count($selectedAccount) > 1)
								$multiple='size="'.$size.'" multiple="multiple"';
							?>
							<div class="row">
								<label for="<?php echo 'systemfilter_'.$searchFilter->guid; ?>"><?php echo JText::_($searchFilter->guid."_LABEL");?></label>
								<?php echo JHTML::_("select.genericlist", $managers, 'systemfilter_'.$searchFilter->guid.'[]', 'class="inputbox text large" style="vertical-align:top " '.$multiple, 'value', 'text',$selectedAccount); ?>
								<a onclick="javascript:toggle_multi_select('<?php echo 'systemfilter_'.$searchFilter->guid;?>', <?php echo $size;?>); return false;" href="#">
									<img src="<?php echo $templateDir;?>/icons/silk/add.png" alt="Expand"/>
								</a>
							</div>
							<?php
							break;
						case "metadata_created":
						case "metadata_published":
							/* Fonctionnement période*/
							if ($defaultSearch) {
								$valuefrom = ($searchFilter->defaultvaluefrom  == '0000-00-00')? null : $searchFilter->defaultvaluefrom  ;
							 	$valueto = ($searchFilter->defaultvalueto  == '0000-00-00')? null : $searchFilter->defaultvalueto  ;
							}else{
								$valuefrom = JRequest::getVar('systemfilter_create_cal_'.$searchFilter->guid);
							   	$valueto = JRequest::getVar('systemfilter_update_cal_'.$searchFilter->guid);
							}
							?>
							<div class="row">
								<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
								<div class="checkbox">
								<div>
								<label class="checkbox" for="<?php echo "systemfilter_create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
								<?php echo helper_easysdi::calendar($valuefrom, "systemfilter_create_cal_".$searchFilter->guid,"systemfilter_create_cal_".$searchFilter->guid,"%Y-%m-%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div><div>
								<label class="checkbox" for="<?php echo "systemfilter_update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
								<?php echo helper_easysdi::calendar($valueto, "systemfilter_update_cal_".$searchFilter->guid,"systemfilter_update_cal_".$searchFilter->guid,"%Y-%m-%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
								</div></div>
							</div>
							<?php
							break;	
						default:
							break;
					}
				break;
			}
			else // Cas des attributs OGC qui ne sont pas liés à une relation
			{
				switch ($searchFilter->rendertype_code)
				{
					case "date":
						/* Fonctionnement période*/
						?>
						<div class="row">
							<div class="label"><?php echo JText::_($searchFilter->guid."_LABEL");?></div>
							<div class="checkbox">
							<div>
							<label class="checkbox" for="<?php echo "filter_create_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_FROM");?></label>
							<?php echo helper_easysdi::calendar(JRequest::getVar('filter_create_cal_'.$searchFilter->guid), "filter_create_cal_".$searchFilter->guid,"filter_create_cal_".$searchFilter->guid,"%Y-%m-%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
							</div><div>
							<label class="checkbox" for="<?php echo "filter_update_cal_".$searchFilter->guid;?>"><?php echo JText::_("CORE_DATE_TO");?></label>
							<?php echo helper_easysdi::calendar(JRequest::getVar('filter_update_cal_'.$searchFilter->guid), "filter_update_cal_".$searchFilter->guid,"filter_update_cal_".$searchFilter->guid,"%Y-%m-%d", 'class="calendar searchTabs_calendar text medium hasDatepicker"', 'class="ui-datepicker-trigger"', $templateDir.'/media/icon_agenda.gif', JText::_("CATALOG_SEARCH_CALENDAR_ALT")); ?>
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
		$pages = $pageNav->getPagesLinks();
		$links = $pages['pages'];
		foreach ($links as $page )
		{
			echo $page;
		}
	}	
}
?>