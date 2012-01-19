<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�"Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
 * 
 * string genericlist (array $arr, string $name, 
 * [string $attribs = null], [string $key = 'value'], 
 * [string $text = 'text'], [mixed $selected = NULL], 
 * [ $idtag = false], [ $translate = false])
 */

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
					
JHTML::script('ext-base.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
JHTML::script('ext-all.js', 'administrator/components/com_easysdi_catalog/ext/');
JHTML::script('dynamic.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedButton.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedField.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedFieldSet.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedFormPanel.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedHidden.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('MultiSelect.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('SearchField.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('SuperBoxSelect.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('FileUploadField.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('shCore.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('shBrushXml.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('GemetClient.js', 'administrator/components/com_easysdi_catalog/js/');

class HTML_metadata {
	var $javascript = "";
	var $langList = array ();
	var $mandatoryMsg = "";
	var $regexMsg = "";
	var $boundaries = array();
	var $paths = array();
	var $boundaries_name = array();
	var $catalogBoundaryIsocode = "";
	var $qTipDismissDelay = "5000"; // Dur�e par d�faut de l'affichage du tooltip
	var $parentId_class="";
	var $parentId_attribute="";
	var $parentGuid="";
	
	
	function listMetadata($pageNav, $rows, $option, $rootAccount, $listObjectType, $filter_objecttype_id, $search, $lists)
	{
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		?>
		<script>
		function tableOrdering( order, dir, view )
		{
			var form = document.getElementById("metadataListForm");
			
			form.filter_order.value 	= order;
			form.filter_order_Dir.value	= dir;
			form.submit( view );
		}
					
		</script>
		<div id="page">
		<h1 class="contentheading"><?php echo JText::_("CATALOG_LIST_METADATA"); ?></h1>
		<div class="contentin">
		<h2> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h2>
		
		<form action="index.php" method="POST" id="metadataListForm" name="metadataListForm">
		<div class="row">
			 <div class="row">
			 	<label for="searchObjectName"><?php echo JText::_("CATALOG_METADATA_FILTER_OBJECTNAME");?></label>
			 	<input type="text" name="searchObjectName" value="<?php echo $search;?>" class="inputboxSearchProduct text large" />
			 </div>
			 <div class="row">
			 	<label for="searchObjectType"><?php echo JText::_("CATALOG_METADATA_FILTER_OBJECTTYPE");?></label>
			 	<?php echo JHTML::_('select.genericlist',  $listObjectType, 'filter_md_objecttype_id', 'class="inputbox" size="1"', 'value', 'text', $filter_objecttype_id); ?>
			 </div>
			 <div class="row">
				<input type="submit" id="simple_search_button" name="simple_search_button" class="easysdi_search_button submit" value ="<?php echo JText::_("CORE_SEARCH_BUTTON"); ?>" onClick="document.getElementById('metadataListForm').task.value='listMetadata';document.getElementById('metadataListForm').submit();"/>
			</div>	 
		 </div>
	<div class="ticker">
	<h2><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h2>
	<?php
	if(count($rows) == 0){
		//echo "<table><tbody><tr><td colspan=\"11\">".JText::_("CORE_NO_RESULT_FOUND")."</td>";
		echo "<p><strong>".JText::_("CATALOG_METADATA_NORESULTFOUND")."</strong>&nbsp;0&nbsp;</p>";
		
	}else{?>
	<table class="box-table" id="MyMetadatas">
	<thead>
		<tr>
			<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_METADATA_OBJECTNAME"), 'name', $lists['order_Dir'], $lists['order']); ?></th>
			<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CATALOG_METADATA_VERSIONTITLE"), 'version_title', $lists['order_Dir'], $lists['order']); ?></th>
			<th class='title'><?php echo JHTML::_('grid.sort',   JText::_("CORE_METADATA_STATE"), 'state', $lists['order_Dir'], $lists['order']); ?></th>
			<th class='title'><?php echo JText::_('CORE_METADATA_MANAGERS'); ?></th>
			<th class='title'><?php echo JText::_('CORE_METADATA_EDITORS'); ?></th>
			<th class='title'><?php echo JText::_('CATALOG_METADATA_ACTIONS'); ?></th>
		</tr>
	</thead>
	<?php 
	} 
	?>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			$rowMetadata = new metadataByGuid($database);
			$rowMetadata->load($row->metadata_guid);
			
			?>		
			<tr>
				<td >
					<?php echo $row->name;  ?>
				</td>
				<td >
					<a class="modal" title="<?php echo addslashes(JText::_("CATALOG_VIEW_MD")); ?>" href="./index.php?tmpl=component&option=com_easysdi_catalog&toolbar=1&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->version_title ;?></a>
				</td>
				<?php
if ($row->state == "CORE_PUBLISHED" and date('Y-m-d') < date('Y-m-d', strtotime($rowMetadata->published)))
{ 
?>
				<td ><?php echo JText::_($row->state).JText::sprintf("CATALOG_FE_METADATA_PUBLISHEDSTATE_DATE", date('d.m.Y', strtotime($rowMetadata->published))); ?></td>
<?php
}
else if ($row->state == "CORE_ARCHIVED" and date('Y-m-d') < date('Y-m-d', strtotime($rowMetadata->archived)))
{ 
?>
				<td ><?php echo JText::_($row->state).JText::sprintf("CATALOG_FE_METADATA_PUBLISHEDSTATE_DATE", date('d.m.Y', strtotime($rowMetadata->archived))); ?></td>
<?php
}
else
{ 
?>
				<td ><?php echo JText::_($row->state); ?></td>
<?php
}
?>	
				<?php 		
				$managers = "";
				$database->setQuery( "SELECT b.name FROM #__sdi_manager_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
				$managers = implode(", ", $database->loadResultArray());
				
				$editors = "";
				$database->setQuery( "SELECT b.name FROM #__sdi_editor_object a,#__users b, #__sdi_account c where a.account_id = c.id AND c.user_id=b.id AND a.object_id=".$row->id." ORDER BY b.name" );
				$editors = implode(", ", $database->loadResultArray());
				?>
				<td ><?php echo $managers; ?></td>
				<td ><?php echo $editors; ?></td>
				<td class="metadataActions">
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					?>
					<div class="logo" id="emptyPicto"></div>
					<?php 
				} 
				else 
				{
					// Est-ce que cet utilisateur est un manager?
					$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$row->id) ;
					$total = $database->loadResult();
					if ($total == 1)
						$isManager = true;
					else
						$isManager = false;
					
					// Est-ce que cet utilisateur est un editeur?
					$database->setQuery( "SELECT count(*) FROM #__sdi_editor_object e, #__sdi_object o, #__sdi_account a WHERE e.object_id=o.id AND e.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$row->id) ;
					$total = $database->loadResult();
					if ($total == 1)
						$isEditor = true;
					else
						$isEditor = false;
					
					$rowMetadata = new metadataByGuid($database);
					$rowMetadata->load($row->metadata_guid);
					if ($isManager) // Le r�le de gestionnaire prime sur celui d'�diteur, au cas o� l'utilisateur a les deux
					{
						if ($rowMetadata->metadatastate_id == 4 // En travail
							or $rowMetadata->metadatastate_id == 3 // Valid�
							or ($rowMetadata->metadatastate_id == 2 and $rowMetadata->archived >= date('Y-m-d H:i:s'))// Archiv� et date du jour <= date d'archivage
							or ($rowMetadata->metadatastate_id == 1 and $rowMetadata->published <= date('Y-m-d H:i:s') )// Publi� et date du jour >= date de publication
							)
						{
							?>
								<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_EDIT_METADATA_ACTION')); ?>" id="editMetadata" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?task=editMetadata&option='.$option.'&cid[]='.$row->version_id)); ?>', '_self');"></div>
							<?php
						}
						else
						{
							?>
								<div class="logo" id="emptyPicto"></div>
							<?php 
						}
					}
					else if ($isEditor)
					{
						// L'utilisateur courant, si c'est un �diteur, doit �tre �diteur de la m�tadonn�e
						$rowCurrentUser = new accountByUserId($database);
						$rowCurrentUser->load($user->get('id'));
			
						if ($rowMetadata->metadatastate_id == 4 and $rowMetadata->editor_id == $rowCurrentUser->id) // En travail et t�che d'�dition assign�e
						{
							?>
							<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_EDIT_METADATA_ACTION')); ?>" id="editMetadata" onClick="window.open('<?php echo JRoute::_(displayManager::buildUrl('index.php?task=editMetadata&option='.$option.'&cid[]='.$row->version_id)); ?>', '_self'); "></div>
							
							<?php
						} 
						else
						{
							?>
								<div class="logo" id="emptyPicto"></div>
							<?php 
						}
					}
				}
			?>
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					?>
					<div class="logo" id="emptyPicto"></div>
					<div class="logo" id="emptyPicto"></div>
					<?php 
				} 
				else 
				{
					// Est-ce que cet utilisateur est un manager?
					$database->setQuery( "SELECT count(*) FROM #__sdi_manager_object m, #__sdi_object o, #__sdi_account a WHERE m.object_id=o.id AND m.account_id=a.id AND a.user_id=".$user->get('id')." AND o.id=".$row->id) ;
					$total = $database->loadResult();
					if ($total == 1)
						$isManager = true;
					else
						$isManager = false;
					
					$rowMetadata = new metadataByGuid($database);
					$rowMetadata->load($row->metadata_guid);
					if ($isManager) // Le r�le de gestionnaire prime sur celui d'�diteur, au cas o� l'utilisateur a les deux
					{
						if (($rowMetadata->metadatastate_id == 1 and date('Y-m-d H:i:s') >= $rowMetadata->published )// Publi� et date du jour >= date de publication
							)
						{
							?>
								<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_ARCHIVE_METADATA')); ?>" id="archiveMetadata" onClick="document.getElementById('metadataListForm').task.value='archiveMetadata';document.getElementById('cid[]').value=<?php echo $rowMetadata->id?>;document.getElementById('metadataListForm').submit();"></div>
							<?php
						}
						else
						{
							?>
								<div class="logo" id="emptyPicto"></div>
							<?php 
						}
						if ($rowMetadata->metadatastate_id == 3 or ($rowMetadata->metadatastate_id == 2 ) or  ($rowMetadata->metadatastate_id == 1 and date('Y-m-d H:i:s') >= $rowMetadata->published)// Archiv�, Valid� ou Publi�
							)
						{
							?>
								<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_INVALIDATE_METADATA')); ?>" id="invalidateMetadata" onClick="document.getElementById('metadataListForm').task.value='invalidateMetadata';document.getElementById('cid[]').value=<?php echo $rowMetadata->id?>;document.getElementById('metadataListForm').submit();"></div>
							<?php
						}
						else
						{
							?>
								<div class="logo" id="emptyPicto"></div>
							<?php 
						}
					}
					else
					{
						?>
							<div class="logo" id="emptyPicto"></div>
						<?php
					}
				}
			?>
				<div class="logo" title="<?php echo addslashes(JText::_('CATALOG_HISTORYASSIGN_METADATA')); ?>" id="historyAssignMetadata" onClick="document.getElementById('metadataListForm').task.value='historyAssignMetadata';document.getElementById('cid[]').value=<?php echo $rowMetadata->id?>;document.getElementById('metadataListForm').submit();"></div>
				</td>
			</tr>
			
				<?php		
		}
		
	?>
			</tbody>
			</table>
			</div>
			<div class="paging">
		    	<h3 class="hidden"><?php JText::_('WEITERE TREFFER ANZEIGEN'); ?></h3>
		    	<p class="info"><?php echo $pageNav->getPagesCounter(); ?></p>
				<p class="select"><?php echo $pageNav->getPagesLinks( ); ?></p>
		  	</div>
			 
			<input type="hidden" id="cid[]" name="cid[]" value="">
			<input type="hidden" id="id" name="id" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task" name="task" value="listMetadata">
			<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
			<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
		</form>
		</div>
		</div>
	<?php
	}
	
	function editMetadata($object_id, $root, $metadata_id, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $object_name, $version_title, $option)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		$uri =& JUri::getInstance();
		
		$database =& JFactory::getDBO();
		$language =& JFactory::getLanguage();
		$user	=& JFactory::getUser();
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		if (file_exists($uri->base(true).'/components/com_easysdi_catalog/ext/src/locale/ext-lang-'.$language->_lang.'.js')) 
			JHTML::script('ext-lang-'.$language->_lang.'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		else
			JHTML::script('ext-lang-'.substr($language->_lang, 0 ,2).'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		
		$metadata_collapse = config_easysdi::getValue("metadata_collapse");
		$this->qTipDismissDelay = config_easysdi::getValue("catalog_metadata_qtipDelay");
		$this->mandatoryMsg = html_Metadata::cleanText(JText::_('CATALOG_METADATA_EDIT_MANDATORY_MSG'));
		$this->regexMsg = html_Metadata::cleanText(JText::_('CATALOG_METADATA_EDIT_REGEX_MSG'));
	
		// R�cup�rer les noms des p�rim�tres
		$this->boundaries[JText::_('CORE_METADATASTATE_LIST')] = array('northbound'=>0, 'southbound'=>0, 'westbound'=>0, 'eastbound'=>0);
		
		foreach($boundaries as $boundary)
		{
			$this->boundaries_name[] = JText::_($boundary->guid."_LABEL");
			$this->boundaries[JText::_($boundary->guid."_LABEL")] = array('northbound'=>$boundary->northbound, 'southbound'=>$boundary->southbound, 'westbound'=>$boundary->westbound, 'eastbound'=>$boundary->eastbound);
		}
		//print_r($this->boundaries);
		$this->catalogBoundaryIsocode = $catalogBoundaryIsocode;
	
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$this->paths[] = array(	'northbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_north")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'southbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_south")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'westbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_west")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'eastbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_east")."-".str_replace(":", "_", $type_isocode)."__1"));
		
		
		
		// R�cup�rer les infos pour la m�tadonn�e parente pour le lien entre les types d'objet o� cet objet est l'enfant et la borne parent max est �gale � 1
		$database->setQuery( "SELECT otl.class_id, otl.attribute_id, parent_m.guid as parent_guid 
							  FROM #__sdi_objecttypelink otl
							  INNER JOIN #__sdi_object child_o ON otl.child_id=child_o.objecttype_id
							  INNER JOIN #__sdi_objectversion child_ov ON child_ov.object_id=child_o.id
							  INNER JOIN #__sdi_object parent_o ON otl.parent_id=parent_o.objecttype_id
							  INNER JOIN #__sdi_objectversion parent_ov ON parent_ov.object_id=parent_o.id
							  INNER JOIN #__sdi_metadata parent_m ON parent_ov.metadata_id=parent_m.id
							  INNER JOIN #__sdi_objectversionlink ovl ON (ovl.parent_id=parent_ov.id and ovl.child_id=child_ov.id)
							  WHERE otl.parentbound_upper=1
							  		AND child_o.id=".$object_id);
		$parentInfos = $database->loadObject();
		
		if (count($parentInfos) > 0)
		{
			$this->parentId_class = $parentInfos->class_id;
			$this->parentId_attribute = $parentInfos->attribute_id;
		}
		else
		{
			$this->parentId_class = "";
			$this->parentId_attribute = "";
		}
		
		if ($this->parentId_class <> "" and $this->parentId_attribute <> "")
			$this->parentGuid = $parentInfos->parent_guid;
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/form_layout_backend.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/MultiSelect.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/fileuploadfield.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/superboxselect.css');
		
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shCore.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shThemeDefault.css');
		
		$url = 'index.php?option='.$option.'&task=saveMetadata';
		$preview_url = 'index.php?option='.$option.'&task=previewXMLMetadata';
		$invalidate_url = 'index.php?option='.$option.'&task=invalidateMetadata';
		$validate_url = 'index.php?option='.$option.'&task=validateMetadata';
		$update_url = 'index.php?option='.$option.'&task=updateMetadata';
		$publish_url = 'index.php?option='.$option.'&task=validateForPublishMetadata';
		$assign_url = 'index.php?option='.$option.'&task=assignMetadata';
		
		$user_id = $user->get('id');

		$this->javascript = "";
		
		$database->setQuery( "SELECT a.root_id 
							  FROM #__sdi_account a
							  INNER JOIN #__users u ON a.user_id = u.id
							  WHERE a.root_id is null 
							  		AND u.id=".$user_id." 
							  ORDER BY u.name" );
		$account_id = $database->loadResult();
		if ($account_id == null)
			$account_id = $user_id;

		// Langues � g�rer
		$this->langList = array();
		$database->setQuery( "SELECT l.id, l.name, l.label, l.defaultlang, l.code as code, l.isocode, l.gemetlang, c.code as code_easysdi 
							  FROM #__sdi_language l, #__sdi_list_codelang c 
							  WHERE l.codelang_id=c.id 
							  		AND published=true 
							  ORDER BY l.ordering" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		
		// Langue de l'utilisateur pour la construction du Thesaurus Gemet
		$userLang="";
		foreach($this->langList as $row)
		{									
			if ($row->code_easysdi == $language->_lang) // Langue courante de l'utilisateur
				$userLang = $row->gemetlang;
		}
								
		// Premier noeud						
		$fieldsetName = "fieldset".$root[0]->id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
		?>
			<!-- Pour permettre le retour � la liste des produits depuis la toolbar Joomla -->
			<div id="page">
		   <h2 class="contentheading"><?php echo JText::sprintf("CATALOG_EDIT_METADATA", $object_name, $version_title) ?></h2>
		   <div id="contentin" class="contentin easysdi-table">
		   <table width="100%">
			<tr>
				<td width="700px"><div id="editMdOutput"></div></td>
			</tr>
		   </table>
		   <form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
			<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
			</form>
		</div>
		</div>
				<?php
				$this->javascript .="
						var domNode = Ext.DomQuery.selectNode('div#editMdOutput')
						Ext.DomHelper.insertHtml('afterBegin',domNode,'<div id=formContainer></div>');
				
						// Message d'attente pendant les chargements
						var myMask = new Ext.LoadMask(Ext.getBody(), {msg:'Please wait...'});

						// Construction des variables pour les diff�rentes fen�tres qui pourraient �tre g�n�r�es
						var win;
    					var winxml;
						var wincsw;
						var winrct;
						var winrst;
    
						// Outils pour la pr�visualisation
						SyntaxHighlighter.config.clipboardSwf = 'clipboard.swf';
														
						// sets the user interface language
						HS.setLang('".$userLang."');
						//console.log(HS.getLang());

						HS.Lang['".$userLang."']['Home']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_HOME'))."';
						HS.Lang['".$userLang."']['Search']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_SEARCH'))."';
						HS.Lang['".$userLang."']['Use']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_USE'))."';
						HS.Lang['".$userLang."']['Themes']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_THEMES'))."';
						HS.Lang['".$userLang."']['Groups']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_GROUPS'))."';
						HS.Lang['".$userLang."']['Warning']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_WARNING'))."';
						HS.Lang['".$userLang."']['characters required']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_CHARACTERSREQUIRED'))."';
						HS.Lang['".$userLang."']['Top concepts']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_TOPCONCEPTS'))."';
						HS.Lang['".$userLang."']['Found']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_FOUND'))."';
						HS.Lang['".$userLang."']['INSPIRE themes']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_INSPIRETHEMES'))."';
						HS.Lang['".$userLang."']['GEMET top concepts']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_GEMETTOPCONCEPTS'))."';
						HS.Lang['".$userLang."']['BT']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_BT'))."';
						HS.Lang['".$userLang."']['NT']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_NT'))."';
						HS.Lang['".$userLang."']['RT']='".html_Metadata::cleanText(JText::_('CATALOG_GEMETCOMPONENT_HS_RT'))."';
												
						// Cr�er le formulaire qui va contenir la structure
						var form = new Ext.ux.ExtendedFormPanel({
								id:'metadataForm',
								url: 'index.php',
								labelAlign: 'left',
						        labelWidth: 200,
						        border:false,
						        buttonAlign: 'right',
						       	collapsed:false,
						        renderTo: document.getElementById('formContainer'),
						        isInvalid: false,
						        buttons: [{
							                text: '".JText::_('CORE_XML_PREVIEW')."',
							                handler: function()
							                {
							                	myMask.show();
							                 	var fields = new Array();
							        			form.cascade(function(cmp)
							        			{
								        			if (cmp.xtype=='fieldset')
							         				{
							         					if (cmp.clones_count)
							          					{
							           						fields.push(cmp.getId()+','+cmp.clones_count);
							         					}
							         				}
							        			});
							        			var fieldsets = fields.join(' | ');
							        			
												form.getForm().setValues({fieldsets: fieldsets});
							              		form.getForm().setValues({task: 'previewXMLMetadata'});
							                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
							                 	form.getForm().setValues({object_id: '".$object_id."'});
												form.getForm().submit({
											    	scope: this,
													method	: 'POST',
													clientValidation: false,
													success: function(form, action) 
													{
														//alert('Metadata previewed successfully.');
														//alert(action.result.file.xml.replace('_carriagereturn_', '\\n'));
														
														//mifWin.setSrc();
														//var reg=new RegExp('(_carriagereturn_)', 'g');
														//xml = xml.replace(reg, '<br>');
														
														var xml = action.result.file.xml;
														xml = xml.split('<br>').join('\\n');
														var html = '<pre class=\"brush: xml;gutter: false;\">' + xml + '</pre>';
														
														// Cr�er une iframe pour accueillir le preview XML
														mifWin = new Ext.Window({
						
														      title         : 'XML Preview',
														      width         : 845,
														      height        : 469,
														      maximizable   : false,
														      collapsible   : false,
														      id            : 'xmlpreview',
														      constrain     : false,
														      loadMask      : {msg: 'Loading...'},
														      autoScroll    : true,
														      html			: html
														  });
								  						mifWin.show();
								  						SyntaxHighlighter.highlight();
														
								  						myMask.hide();
													},
													failure: function(form, action) 
													{
                        								if (action.result)
															alert(action.result.errors.xml);
														else
															alert('Form validation error');
															
														myMask.hide();
													},
													url:'".$preview_url."'
												});
							        	}
						        }
						       ],
						       	tbar: new Ext.Toolbar({
						       			toolbarCls: 'x-panel-fbar',
						       			buttonAlign: 'right',
						       			layout: 'toolbar',
						       			cls: 'x-panel-footer x-panel-footer-noborder x-panel-btns',
						       			items: [".HTML_metadata::buildTBar($isManager, $isEditor, $isPublished, $isValidated, $object_id, $metadata_id, $account_id, $metadata_collapse, $option)."]
						       			})
						    });
							
						var ".$fieldsetName." = new Ext.form.FieldSet({id:'".str_replace(":", "_", $root[0]->isocode)."', cls: 'easysdi_shop_backend_form', title:'".html_Metadata::cleanText(JText::_($root[0]->label))."', xtype: 'fieldset', clones_count: 1});
						form.add(".$fieldsetName.");";
		
		
				$queryPath="/";
				// Boucle pour construire la structure
				$node = $xpathResults->query($queryPath."/".$root[0]->isocode);
				$nodeCount = $node->length;
				//echo $nodeCount." fois ".$root[0]->isocode;
				HTML_metadata::buildTree($database, 0, $root[0]->id, $root[0]->id, $fieldsetName, 'form', str_replace(":", "_", $root[0]->isocode), $xpathResults, null, $node->item(0), $queryPath, $root[0]->isocode, $account_id, $profile_id, $option);
				
				// Retraverser la structure et autoriser les nulls pour tous les champs cach�s
				$this->javascript .="
					//var hiddenFields= new Array();
					form.cascade(function(cmp)
					{
						if (cmp.xtype=='fieldset')
						{
							//console.log('Fieldset: ' + cmp.getId() + ' - ' + cmp.clone);
							if (cmp.clone == false)
							{
								//var f = cmp.items;
								cmp.cascade(function (field)
								{
									//hiddenFields.push(field.getId());
									//console.log('Field: ' + field.getId() + ' - ' + field.allowBlank);
									if (field.allowBlank == false)
									{
										//console.log('Field or fieldset to change: ' + field.getId());
										//console.log(field);
										field.allowBlank = true;
									}
									if (field.regex)
									{
										//console.log('Field or fieldset to change: ' + field.getId());
										field.regex = '';
									}
								})
							}
						}
					});
					//console.log(hiddenFields);
				";								

				// Sauvegarde basique, pas de contr�les ExtJs. Uniquement quand une m�tadonn�e n'est pas publi�e.
				if (!$isPublished)
				{
					$this->javascript .="
					form.fbar.add(new Ext.Button( {
						                text: '".JText::_('CORE_SAVE')."',
						                handler: function()
						                {
						                	myMask.show();
						                 	var fields = new Array();
						        			form.cascade(function(cmp)
						        			{
							        			if (cmp.getId() == 'gmd_MD_Metadata-gmd_MD_DataIdentification__2-gmd_abstract__2-gmd_LocalisedCharacterString-fr-FR__1')
					         					{
					         						//alert(cmp.getId() + \" - \" + cmp.getValue());
					         						//alert(escape(cmp.getValue()));
					         					}
					          					
						         				//alert(cmp.getId() + \" - \" + cmp.xtype);
												if (cmp.xtype=='fieldset')
						         				{
						         					//alert(cmp.getId() + \" - \" + cmp.clones_count);
													if (cmp.clones_count)
						          					{
						           						fields.push(cmp.getId()+','+cmp.clones_count);
						         					}
						         				}
						        			});
						        			var fieldsets = fields.join(' | ');
						        			//alert(fieldsets);
											
						        			form.getForm().setValues({fieldsets: fieldsets});
						              		form.getForm().setValues({task: 'saveMetadata'});
						                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
						                 	form.getForm().setValues({object_id: '".$object_id."'});
											form.getForm().submit({
										    	scope: this,
												method	: 'POST',
												clientValidation: false,
												success: function(form, action) 
												{
													// Retour � la page pr�c�dente
													Ext.MessageBox.alert('".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TITLE')."', 
							                    						 '".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TEXT')."',
							                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
													myMask.hide();
												},
												failure: function(form, action) 
												{
                        							if (action.result)
														alert(action.result.errors.xml);
													else
														alert('Form save error');
													myMask.hide();
												},
												url:'".$url."'
											});
						        	}
					        })
						        );
					form.render();";
				}
				
				// Possibilit� de valider lorsqu'on est �diteur. Contr�les ExtJs et passage de l'�tat "En travail" � "Valid�"
				if ($isEditor and !$isPublished)
				{
					$this->javascript .="
						form.fbar.add(new Ext.Button({text: '".JText::_('CORE_VALIDATE')."',
									handler: function()
					                {
					                	myMask.show();
					                 	var fields = new Array();
					        			form.getForm().isInvalid=false;
					        			form.getForm().fieldInvalid =false;
					        			form.getForm().extValidationCorrupt =false;
						        		form.cascade(function(cmp)
					        			{
					        				//verifies whether client validation is ok for any field that needs validation.
					        				if(cmp.isValid){
						        				if(!cmp.isValid()&& Ext.get(cmp.id)){														
														form.getForm().fieldInvalid =true;														
											
													if(!Ext.getCmp(cmp.id)){														
															form.getForm().extValidationCorrupt =true;														
													}
												}
											}
					        			
						        			if (cmp.xtype=='fieldset')
					         				{
					         					if (cmp.clones_count)
					          						fields.push(cmp.getId()+','+cmp.clones_count);
					         				}
						         				
						         				// Validation des champs langue
					         					if (cmp.isLanguageFieldset && cmp.rendered == true && cmp.clone == true)
					         					{
					         						var countFields = cmp.items.length;
					         						var countValues = 0;
													
													for (var i=0; i < countFields ; i++)
													{
														field = cmp.items.get(i); 
														if (field.getValue() != '')
														{
															countValues++;
														}
													}
													
													// countValues doit �tre �gal � z�ro ou � countFields. Sinon, lever une erreur
													if (countValues != countFields && countValues != 0)
													{
														//console.log(cmp.getId());
														for (var i=0; i < countFields ; i++)
														{
															field = cmp.items.get(i);
															if (field.getValue() == '')
																field.markInvalid('".html_Metadata::cleanText(JText::_('CATALOG_VALIDATEMETADATA_LANGUAGEINVALID_MSG'))."');
														} 
														form.getForm().isInvalid=true;
													}
					         					}
					        			});
					        			var fieldsets = fields.join(' | ');
					        			
		        						myMask.hide();

					        			form.getForm().setValues({fieldsets: fieldsets});
							            form.getForm().setValues({task: 'validateMetadata'});
										form.getForm().setValues({metadata_id: '".$metadata_id."'});
										form.getForm().setValues({object_id: '".$object_id."'});
										form.getForm().setValues({account_id: '".$account_id."'});
								
													        			
										if ((!form.getForm().isInvalid) &&(!form.getForm().fieldInvalid) )
					        			{	
					        				form.getForm().submit({
											    	scope: this,
													method	: 'POST',
													clientValidation: false,
													success: function(form, action) 
													{
														Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_SUCCESS_TITLE')."', 
								                    						 '".JText::_('CATALOG_VALIDATEMETADATA_MSG_SUCCESS_TEXT')."',
								                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
	
														myMask.hide();
													},
													failure: function(form, action) 
													{ 
	                        							Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TEXT')."');
															
														myMask.hide();
													},
													url:'".$validate_url."'
												});
											
										}
										else{			
													if(form.getForm().extValidationCorrupt){															
															Ext.Msg.show({
															   modal : true,														
															   title:'".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."',
															   msg: '".JText::_('CATALOG_VALIDATEMETADATA_MSG_EXTCORRUPT')."',
															   buttons: Ext.Msg.YESNO,
				
															   fn:  function(btn){															           			
						        								
																		if (btn == 'no')
																			form.getForm().fieldInvalid = true;
																		else
																			{
																					form.getForm().submit({
																				    	scope: this,
																						method	: 'POST',
																						clientValidation: false,
																						success: function(form, action) 
																						{
																							Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_SUCCESS_TITLE')."', 
																	                    						 '".JText::_('CATALOG_VALIDATEMETADATA_MSG_SUCCESS_TEXT')."',
																	                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
										
																							myMask.hide();
																						},
																						failure: function(form, action) 
																						{ 
										                        							Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TEXT')."');
																								
																							myMask.hide();
																						},
																						url:'".$validate_url."'
																					});
				
																			}
																	} ,
															   animEl: 'elId'
															});			        			 		
																		
													
													}else if (form.getForm().isInvalid){													
											
														Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TEXT')."');
																	
														myMask.hide();
														
													}else{
														Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TEXT')."');
															
														myMask.hide();
											
													
													}
											
										}
						        	}
								}
								)
						        );
					form.render();";
				}
				
				// Possibilit� de publier lorsqu'on est gestionnaire. Contr�les ExtJs et passage de l'�tat "Valid�" � "Publi�"
				if($isManager and $isValidated)
				{
					$this->javascript .="
							form.fbar.add(new Ext.Button({text: '".JText::_('CORE_PUBLISH')."',
											handler: function()
							                {
							                	myMask.show();
							                 	var fields = new Array();

							        			form.getForm().isInvalid=false;
							        			form.getForm().fieldInvalid =false;
					        					form.getForm().extValidationCorrupt =false;
							        			form.cascade(function(cmp)
							        			{
							        				//verifies whether client validation is ok for any field that needs validation.
							        				if(cmp.isValid){
								        				if(!cmp.isValid()&& Ext.get(cmp.id)){														
																form.getForm().fieldInvalid =true;														
													
															if(!Ext.getCmp(cmp.id)){														
																	form.getForm().extValidationCorrupt =true;														
															}
														}
													}
													
								        			if (cmp.xtype=='fieldset')
							         				{
							         					if (cmp.clones_count)
							          						fields.push(cmp.getId()+','+cmp.clones_count);
							         				}
							         				
							         				// Validation des champs langue
						         					if (cmp.isLanguageFieldset && cmp.rendered == true && cmp.clone == true)
						         					{
						         						var countFields = cmp.items.length;
						         						var countValues = 0;
														
														for (var i=0; i < countFields ; i++)
														{
															field = cmp.items.get(i); 
															if (field.getValue() != '')
															{
																countValues++;
															}
														}
														
														// countValues doit �tre �gal � z�ro ou � countFields. Sinon, lever une erreur
														if (countValues != countFields && countValues != 0)
														{
															for (var i=0; i < countFields ; i++)
															{
																field = cmp.items.get(i);
																if (field.getValue() == '')
																	field.markInvalid('".html_Metadata::cleanText(JText::_('CATALOG_VALIDATEMETADATA_LANGUAGEINVALID_MSG'))."');
															} 
															form.getForm().isInvalid=true;
														}
						         					}
							        			});
							        			var fieldsets = fields.join(' | ');
							        			
							        			form.getForm().setValues({fieldsets: fieldsets});
								                form.getForm().setValues({task: 'validateForPublishMetadata'});
								                form.getForm().setValues({metadata_id: '".$metadata_id."'});
								                form.getForm().setValues({object_id: '".$object_id."'});
							        				myMask.hide();
							        			//if (!form.getForm().isInvalid)
								        			if ((!form.getForm().isInvalid) &&(!form.getForm().fieldInvalid) )							        			
								        			{
	
								        				form.getForm().submit({
																						    	scope: this,
																								method	: 'POST',
																								clientValidation: false,
																								success: function(form, action) 
																								{
																									xml = (action.result.file.xml);
																									xmlfile = xml.split('<br>').join('\\n');
																									// Cr�er une iframe pour demander � l'utilisateur la date de publication
																									if (!win)
																										win = new Ext.Window({
																												                title:'Publication',
																												                width:300,
																												                height:170,
																												                closeAction:'hide',
																												                layout:'fit', 
																															    border:false, 
																															    closable:false, 
																															    modal:true,
																															    renderTo:Ext.getBody(), 
																															    frame:true,
																															    items:[{ 
																																     xtype:'form' 
																																     ,id:'publishform' 
																																     ,defaultType:'textfield' 
																																     ,frame:true 
																																     ,method:'post' 
																																     ,defaults:{anchor:'95%'} 
																																     ,items:[ 
																																       { 
																																         fieldLabel:'".html_Metadata::cleanText(JText::_('CATALOG_VALIDATEMETADATA_PUBLISHBOX_DATE_MSG'))."', 
																																         id:'publishdate', 
																																         xtype: 'datefield',
																																         format: 'd.m.Y',
																																         value:'' 
																																       },
																																       { 
																																         fieldLabel:'".html_Metadata::cleanText(JText::_('CATALOG_VALIDATEMETADATA_PUBLISHBOX_ARCHIVELAST_MSG'))."', 
																																         id:'archivelast', 
																																         xtype: 'checkbox',
																																         checked:false 
																																       },
																																       { 
																																         id:'metadata_id', 
																																         xtype: 'hidden',
																																         value:'".$metadata_id."' 
																																       },
																																       { 
																																         id:'object_id', 
																																         xtype: 'hidden',
																																         value:'".$object_id."' 
																																       },
																																       { 
																																         id:'account_id', 
																																         xtype: 'hidden',
																																         value:'".$account_id."' 
																																       },
																																       { 
																																         id:'xml', 
																																         xtype: 'hidden',
																																         value: xmlfile
																																       },
																																       { 
																																         id:'option', 
																																         xtype: 'hidden',
																																         value:'".$option."' 
																																       },
																																       { 
																																         id:'task', 
																																         xtype: 'hidden',
																																         value: 'publishMetadata'
																																       }
																																    ] 
																																     ,buttonAlign:'right' 
																																     ,buttons: [{
																														                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
																														                    handler: function(){
																														                    	myMask.show();
																														                    	win.items.get(0).getForm().setValues({task: 'publishMetadata'});
																		                 														win.items.get(0).getForm().submit({
																																		    	scope: this,
																																				method	: 'POST',
																																				url:'index.php?option=com_easysdi_catalog&task=publishMetadata',
																																				success: function(form, action) 
																																				{
																																					win.hide();
																																					myMask.hide();
											
																															                    	Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_SUCCESS_TITLE')."', 
																															                    						 '".JText::_('CATALOG_PUBLISHMETADATA_MSG_SUCCESS_TEXT')."',
																															                    						 function () {window.open ('./index.php?option=".$option."&Itemid=".JRequest::getVar('Itemid')."&task=listMetadata','_parent');});																
																																				},
																																				failure: function(form, action) 
																																				{
																																					win.hide();
																																					myMask.hide();
																																					
																															                    	if (action.result)
																															                    	{
																															                    		Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', action.result.errors.message);
																															                    	}
																															                    	else
																															                    	{
																															                    		Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TEXT')."');
																															                    	}
																																				}
																																				});
																														                    }
																														                },{
																														                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
																														                    handler: function(){
																														                        win.hide();
																														                }
																																   }] 
																												                }]
																												            });
																									else
																									{
																										win.items.get(0).findById('publishdate').setValue('');
																										win.items.get(0).findById('archivelast').setValue(false);
																									}
																										
																			  						win.show();
																			  						
																									myMask.hide();
																								},
																								failure: function(form, action) 
																								{
										 															if (action.result)
																			                    	{
																			                    		Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', action.result.errors.message);
																			                    	}
																			                    	else
																									{
											                        									Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TEXT')."');
																									}
																									myMask.hide();
																								},
																								url:'".$publish_url."'
																							});
														
													}
													else{
				
																if(form.getForm().extValidationCorrupt){															
																			Ext.Msg.show({
																			   modal : true,														
																			   title:'".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."',
																			   msg: '".JText::_('CATALOG_PUBLISHMETADATA_MSG_EXTCORRUPT')."',
																			   buttons: Ext.Msg.YESNO,
								
																			   fn:  function(btn){															           			
										        								
																						if (btn == 'no'){
																							return;
																						}
																						else{
																							form.getForm().submit({
																						    	scope: this,
																								method	: 'POST',
																								clientValidation: false,
																								success: function(form, action) 
																								{
																									xml = (action.result.file.xml);
																									xmlfile = xml.split('<br>').join('\\n');
																									// Cr�er une iframe pour demander � l'utilisateur la date de publication
																									if (!win)
																										win = new Ext.Window({
																												                title:'Publication',
																												                width:300,
																												                height:170,
																												                closeAction:'hide',
																												                layout:'fit', 
																															    border:false, 
																															    closable:false, 
																															    modal:true,
																															    renderTo:Ext.getBody(), 
																															    frame:true,
																															    items:[{ 
																																     xtype:'form' 
																																     ,id:'publishform' 
																																     ,defaultType:'textfield' 
																																     ,frame:true 
																																     ,method:'post' 
																																     ,defaults:{anchor:'95%'} 
																																     ,items:[ 
																																       { 
																																         fieldLabel:'".html_Metadata::cleanText(JText::_('CATALOG_VALIDATEMETADATA_PUBLISHBOX_DATE_MSG'))."', 
																																         id:'publishdate', 
																																         xtype: 'datefield',
																																         format: 'd.m.Y',
																																         value:'' 
																																       },
																																       { 
																																         fieldLabel:'".html_Metadata::cleanText(JText::_('CATALOG_VALIDATEMETADATA_PUBLISHBOX_ARCHIVELAST_MSG'))."', 
																																         id:'archivelast', 
																																         xtype: 'checkbox',
																																         checked:false 
																																       },
																																       { 
																																         id:'metadata_id', 
																																         xtype: 'hidden',
																																         value:'".$metadata_id."' 
																																       },
																																       { 
																																         id:'object_id', 
																																         xtype: 'hidden',
																																         value:'".$object_id."' 
																																       },
																																       { 
																																         id:'account_id', 
																																         xtype: 'hidden',
																																         value:'".$account_id."' 
																																       },
																																       { 
																																         id:'xml', 
																																         xtype: 'hidden',
																																         value: xmlfile
																																       },
																																       { 
																																         id:'option', 
																																         xtype: 'hidden',
																																         value:'".$option."' 
																																       },
																																       { 
																																         id:'task', 
																																         xtype: 'hidden',
																																         value: 'publishMetadata'
																																       }
																																    ] 
																																     ,buttonAlign:'right' 
																																     ,buttons: [{
																														                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
																														                    handler: function(){
																														                    	myMask.show();
																														                    	win.items.get(0).getForm().setValues({task: 'publishMetadata'});
																		                 														win.items.get(0).getForm().submit({
																																		    	scope: this,
																																				method	: 'POST',
																																				url:'index.php?option=com_easysdi_catalog&task=publishMetadata',
																																				success: function(form, action) 
																																				{
																																					win.hide();
																																					myMask.hide();
											
																															                    	Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_SUCCESS_TITLE')."', 
																															                    						 '".JText::_('CATALOG_PUBLISHMETADATA_MSG_SUCCESS_TEXT')."',
																															                    						 function () {window.open ('./index.php?option=".$option."&Itemid=".JRequest::getVar('Itemid')."&task=listMetadata','_parent');});																
																																				},
																																				failure: function(form, action) 
																																				{
																																					win.hide();
																																					myMask.hide();
																																					
																															                    	if (action.result)
																															                    	{
																															                    		Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', action.result.errors.message);
																															                    	}
																															                    	else
																															                    	{
																															                    		Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TEXT')."');
																															                    	}
																																				}
																																				});
																														                    }
																														                },{
																														                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
																														                    handler: function(){
																														                        win.hide();
																														                }
																																   }] 
																												                }]
																												            });
																									else
																									{
																										win.items.get(0).findById('publishdate').setValue('');
																										win.items.get(0).findById('archivelast').setValue(false);
																									}
																										
																			  						win.show();
																			  						
																									myMask.hide();
																								},
																								failure: function(form, action) 
																								{
										 															if (action.result)
																			                    	{
																			                    		Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', action.result.errors.message);
																			                    	}
																			                    	else
																									{
											                        									Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TEXT')."');
																									}
																									myMask.hide();
																								},
																								url:'".$publish_url."'
																							});
																						}
																					} ,
																			   animEl: 'elId'
																			});			        			 		
																						
																	
																	}else if (form.getForm().isInvalid){													
															
																		Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TEXT')."');
																					
																		myMask.hide();
																		

																		
																	}else{
																		Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TEXT')."');
																			
																		myMask.hide();

																	
																	}
																	
														}//end else 		
												
												
												
								        	}})
								        );
						form.render();";
					// Possibilit� de revenir en travail. Contr�les ExtJs et passage � l'�tat "En travail"
					$this->javascript .="
					form.fbar.add(new Ext.Button({text: '".JText::_('CORE_INVALIDATE')."',
									handler: function()
					                {
					                	myMask.show();
					                 	
					                	form.getForm().setValues({task: 'invalidateMetadata'});
					                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
					                 	form.getForm().setValues({object_id: '".$object_id."'});
										form.getForm().submit({
									    	scope: this,
											method	: 'POST',
											clientValidation: false,
											success: function(form, action) 
											{
												Ext.MessageBox.alert('".JText::_('CATALOG_INVALIDATEMETADATA_MSG_SUCCESS_TITLE')."', 
							                    						 '".JText::_('CATALOG_INVALIDATEMETADATA_MSG_SUCCESS_TEXT')."',
							                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
											
													
												myMask.hide();
											},
											failure: function(form, action) 
											{
                        						Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CATALOG_INVALIDATEMETADATA_MSG_FAILURE_TITLE'))."', '".html_Metadata::cleanText(JText::_('CATALOG_INVALIDATEMETADATA_MSG_FAILURE_TEXT'))."');
												
												myMask.hide();
											},
											url:'".$invalidate_url."'
										});
						        	}})
						        );
					form.render();";
				}
				
				// Ajout du bouton METTRE A JOUR seulement si l'utilisateur courant est gestionnaire de la m�tadonn�e
				// et que la m�tadonn�e est publi�e
				// Possibilit� de mettre � jour lorsqu'on est gestionnaire et que l'�tat est "Publi�". Contr�les ExtJs.
				if($isManager and $isPublished)
				{
					$this->javascript .="
					form.fbar.add(new Ext.Button({text: '".JText::_('CORE_UPDATE')."',
										handler: function()
						                {
						                	myMask.show();
						                 	var fields = new Array();
						                 	form.getForm().fieldInvalid =false;
					        				form.getForm().extValidationCorrupt =false;
						        			form.cascade(function(cmp)
						        			{
						        				//verifies whether client validation is ok for any field that needs validation.
						        				if(cmp.isValid){
							        				if(!cmp.isValid()&& Ext.get(cmp.id)){														
															form.getForm().fieldInvalid =true;														
												
														if(!Ext.getCmp(cmp.id)){														
																form.getForm().extValidationCorrupt =true;														
														}
													}
												}
												
							        			if (cmp.xtype=='fieldset')
						         				{
						         					if (cmp.clones_count)
						          						fields.push(cmp.getId()+','+cmp.clones_count);
						         				}
						        			});
						        			var fieldsets = fields.join(' | ');
						        			
											form.getForm().setValues({fieldsets: fieldsets});
						                 	form.getForm().setValues({task: 'updateMetadata'});
						                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
						                 	form.getForm().setValues({object_id: '".$object_id."'});
						                 	form.getForm().setValues({account_id: '".$account_id."'});
						                 	
							            	if (!form.getForm().fieldInvalid) 
						        			{
												form.getForm().submit({
											    	scope: this,
													method	: 'POST',
													clientValidation: false,
													success: function(form, action) 
													{
														Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TITLE')."', 
								                    						 '".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TEXT')."',
								                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
													
														myMask.hide();
													},
													failure: function(form, action) 
													{
	                        							Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TEXT')."');
																
														myMask.hide();
													},
													url:'".$update_url."'
												});
											}//end 
											else{
												if(form.getForm().extValidationCorrupt){															
															Ext.Msg.show({
															   modal : true,														
															   title:'".JText::_('CATALOG_VALIDATEMETADATA_MSG_FAILURE_TITLE')."',
															   msg: '".JText::_('CATALOG_VALIDATEMETADATA_MSG_EXTCORRUPT')."',
															   buttons: Ext.Msg.YESNO,
				
															   fn:  function(btn){															           			
						        								
																		if (btn == 'no')
																			form.getForm().fieldInvalid = true;
																		else
																			{
																						form.getForm().submit({
																				    	scope: this,
																						method	: 'POST',
																						clientValidation: false,
																						success: function(form, action) 
																						{
																							Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TITLE')."', 
																	                    						 '".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TEXT')."',
																	                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
																						
																							myMask.hide();
																						},
																						failure: function(form, action) 
																						{
										                        							Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TEXT')."');
																									
																							myMask.hide();
																						},
																						url:'".$update_url."'
																					});
				
																			}
																	} ,
															   animEl: 'elId'
															});			        			 		
																		
													
													}else{
														Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TEXT')."');
															
														myMask.hide();
											
													
													}
				
				
											}
											
											
											
							        	}})
							        );
						form.render();";
				}
					
				if(!$isPublished and !$isValidated)
				{
					// Assignation de m�tadonn�e
					$editors = array();
					$listEditors = array();
					$database->setQuery( "	SELECT DISTINCT c.id AS value, b.name AS text 
											FROM #__users b, #__sdi_editor_object a 
											LEFT OUTER JOIN #__sdi_account c ON a.account_id = c.id 
											LEFT OUTER JOIN #__sdi_manager_object d ON d.account_id=c.id 
											WHERE c.user_id=b.id AND (a.object_id=".$object_id." OR d.object_id=".$object_id.") 
													AND c.user_id <> ".$user_id."  
											ORDER BY b.name" );
					$editors = array_merge( $editors, $database->loadObjectList() );
					foreach($editors as $e)
					{
						$listEditors[$e->value] = $e->text;
					}
					
					$listEditors = HTML_metadata::array2extjs($listEditors, false);
					
					$this->javascript .="
					form.fbar.add(new Ext.Button({text: '".JText::_('CORE_ASSIGN')."',
										handler: function()
						                {
						                	/*myMask.show();
						                 	form.getForm().submit({
										    	scope: this,
												method	: 'POST',
												clientValidation: false,
												success: function(form, action) 
												{*/
													// Cr�er une iframe pour demander � l'utilisateur la date de publication
													if (!win)
														win = new Ext.Window({
														                title:'".addslashes(JText::_('CORE_METADATA_ASSIGN_ALERT'))."',
														                width:500,
														                height:200,
														                closeAction:'hide',
														                layout:'fit', 
																	    border:false, 
																	    closable:false, 
																	    renderTo:Ext.getBody(), 
																	    frame:true,
																	    items:[{ 
																		     xtype:'form' 
																		     ,id:'assignform' 
																		     ,defaultType:'textfield' 
																		     ,frame:true 
																		     ,method:'post' 
																		     ,defaults:{anchor:'95%'} 
																		     ,items:[ 
																		       { 
																		       	 typeAhead:true,
																		       	 triggerAction:'all',
																		       	 mode:'local',
																		         fieldLabel:'".addslashes(JText::_('CORE_METADATA_ASSIGN_ALERT_EDITOR_LABEL'))."', 
																		         id:'editor', 
																		         hiddenName:'editor_hidden', 
																		         xtype: 'combo',
																		         store: new Ext.data.ArrayStore({
																					        id: 0,
																					        fields: [
																					            'value',
																					            'text'
																					        ],
																					        data: ".$listEditors."
																					    }),
																				 valueField:'value',
																				 displayField:'text'
																		       },
																		       { 
																		         id:'information', 
																		         xtype: 'textarea',
																		         fieldLabel:'".addslashes(JText::_('CORE_METADATA_ASSIGN_ALERT_INFORMATION_LABEL'))."', 
																		         grow: true,
																		         multiline:true,
																		         value:'' 
																		       },
																		       { 
																		         id:'metadata_id', 
																		         xtype: 'hidden',
																		         value:'".$metadata_id."' 
																		       },
																		       { 
																		         id:'object_id', 
																		         xtype: 'hidden',
																		         value:'".$object_id."' 
																		       }
																		    ] 
																		     ,buttonAlign:'right' 
																		     ,buttons: [{
																                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
																                    handler: function(){
																                    	myMask.show();
																                    	win.items.get(0).getForm().submit({
																				    	scope: this,
																						method	: 'POST',
																						url:'".$assign_url."',
																						success: function(form, action) 
																						{
										                        							win.hide();
																	                    	myMask.hide();
																	                    	
																	                    	Ext.MessageBox.alert('".JText::_('CATALOG_ASSIGNMETADATA_MSG_SUCCESS_TITLE')."', 
																				                    						 '".JText::_('CATALOG_ASSIGNMETADATA_MSG_SUCCESS_TEXT')."',
																				                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_parent');});
																						},
																						failure: function(form, action) 
																						{
										                        							if (action.result)
																								alert(action.result.errors.xml);
																							else
																								alert('Form assign error');
																								
																							win.hide();
																	                    	myMask.hide();
																						}
																						});
																                    }
																                },
																                {
																                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
																                    handler: function(){
																                        win.hide();
																                    }
																                }]
																		   }] 
														                
														            });
													else
													{
														win.items.get(0).findById('editor').setValue('');
														win.items.get(0).findById('information').setValue('');
													}	
							  						win.show();
							  						/*myMask.hide();
								  				},
												failure: function(form, action) 
												{
	                        						if (action.result)
														alert(action.result.errors.xml);
													else
														alert('Form assign error');
														
													myMask.hide();
												},
												url:'".$assign_url."'
											});*/
							        	}})
							        );
					form.render();";
				}
				// Ajout de bouton de retour
				$this->javascript .="
				form.fbar.add(new Ext.Button({text: '".JText::_('CORE_CANCEL')."',
									handler: function()
					                {
					                	//history.back();
					                	window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_self');
					                	//window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');
						        	}})
						        );
				form.render();";
				
				
				$this->javascript .="
					form.add(createHidden('option', 'option', '".$option."'));
					form.add(createHidden('task', 'task', 'saveMetadata'));
					form.add(createHidden('metadata_id', 'metadata_id', '".$metadata_id."'));
					form.add(createHidden('object_id', 'object_id', '".$object_id."'));
					form.add(createHidden('account_id', 'account_id', '".$account_id."'));
					form.add(createHidden('fieldsets', 'fieldsets', ''));
		    		// Affichage du formulaire
		    		form.doLayout();";

		// Tout fermer ou tout ouvrir, selon la cl� de config METADATA_COLLAPSE 
		if ($metadata_collapse == 'true')
		{
			$this->javascript .="
				form.cascade(function(cmp)
        		{
        			if (cmp.xtype=='fieldset')
         			{
         				if (cmp.clone == true)
						{
         					if (cmp.collapsible == true && cmp.rendered == true)
								cmp.collapse(true);
         				}
         			}
        		});
        	";
		}
		else
		{
			$this->javascript .="
				form.cascade(function(cmp)
        		{
        			if (cmp.xtype=='fieldset')
         			{
         				if (cmp.clone == true)
						{
         					if (cmp.collapsible == true && cmp.rendered == true)
								cmp.expand(true);
         				}
         			}
        		});
        	";
		}
		print_r("<script type='text/javascript'>Ext.onReady(function(){".$this->javascript."});</script>");
	}
	
	function buildTree($database, $ancestor, $parent, $parentFieldset, $parentFieldsetName, $ancestorFieldsetName, $parentName, $xpathResults, $parentScope, $scope, $queryPath, $currentIsocode, $account_id, $profile_id, $option)
	{
		//echo $parent." - ".$parentFieldsetName."<br>";
		//echo "<hr>SCOPE: ".$scope->nodeName."<br>";
		// On r�cup�re dans des variables le scope respectivement pour le traitement des classes enfant et
		// pour le traitement des attributs enfants.
		// Cela permet d'�viter les effets de bord
		//$classScope = $scope;
		//$attributScope = $scope;
						
		// Stockage du path pour atteindre ce noeud du XML
		$queryPath = $queryPath."/".$currentIsocode;
		
		// Construire la liste d�roulante des p�rim�tres pr�d�finis si on est au bon endroit
		//echo $this->catalogBoundaryIsocode." == ".$currentIsocode.", ".count($this->boundaries)."<br>";
		if ($this->catalogBoundaryIsocode == $currentIsocode AND count($this->boundaries_name) > 0)
		{
			$this->javascript .="
									var valueList = ".HTML_metadata::array2extjs($this->boundaries_name, true).";
								    var boundaries = ".HTML_metadata::array2json($this->boundaries).";
								    var paths = ".HTML_metadata::array2json($this->paths).";
								     // La liste
								     ".$parentFieldsetName.".add(createComboBox_Boundaries('".$parentName."_boundaries', '".html_Metadata::cleanText(JText::_("BOUNDARIES"))."', false, '1', '1', valueList, '', false, '".html_Metadata::cleanText(JText::_("BOUNDARIES_TIP"))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."', boundaries, paths));
								    ";
		}
		
		$rowChilds = array();
		$query = "SELECT rel.id as rel_id, 
						 rel.guid as rel_guid,
						 rel.name as rel_name, 
						 rel.upperbound as rel_upperbound, 
						 rel.lowerbound as rel_lowerbound, 
						 rel.attributechild_id as attribute_id, 
						 rel.rendertype_id as rendertype_id, 
						 rel.classchild_id as child_id, 
						 rel.objecttypechild_id as objecttype_id, 
						 CONCAT(relation_namespace.prefix,':',rel.isocode) as rel_isocode, 
						 rel.relationtype_id as reltype_id, 
						 rel.classassociation_id as association_id,
						 a.guid as attribute_guid,
						 a.name as attribute_name, 
						 CONCAT(attribute_namespace.prefix,':',a.isocode) as attribute_isocode, 
						 CONCAT(list_namespace.prefix,':',a.type_isocode) as list_isocode, 
						 a.attributetype_id as attribute_type, 
						 a.default as attribute_default, 
						 a.pattern as attribute_pattern, 
						 a.isSystem as attribute_system, 
						 a.length as length,
						 a.codeList as codeList,
						 a.information as tip,
						 CONCAT(attributetype_namespace.prefix,':',t.isocode) as t_isocode, 
						 accountrel_attribute.account_id as attributeaccount_id,
						 c.name as child_name,
						 c.guid as class_guid, 
						 CONCAT(child_namespace.prefix,':',c.isocode) as child_isocode, 
						 accountrel_class.account_id as classaccount_id
				  FROM	 #__sdi_relation as rel 
						 JOIN #__sdi_relation_profile as prof
						 	 ON rel.id = prof.relation_id
						 LEFT OUTER JOIN #__sdi_attribute as a
				  		 	 ON rel.attributechild_id=a.id 
						     LEFT OUTER JOIN #__sdi_list_attributetype as t
						  		 ON a.attributetype_id = t.id 
					     LEFT OUTER JOIN #__sdi_class as c
					  		 ON rel.classchild_id=c.id
					     LEFT OUTER JOIN #__sdi_list_relationtype as reltype
					  		 ON rel.relationtype_id=reltype.id	 
					     LEFT OUTER JOIN #__sdi_account_attribute as accountrel_attribute
					  		 ON accountrel_attribute.attribute_id=attribute_id
					     LEFT OUTER JOIN #__sdi_account_class as accountrel_class
					  		 ON accountrel_class.class_id=class_id
					  	 LEFT OUTER JOIN #__sdi_namespace as attribute_namespace
					  		 ON attribute_namespace.id=a.namespace_id
					  	 LEFT OUTER JOIN #__sdi_namespace as list_namespace
					  		 ON list_namespace.id=a.listnamespace_id
					  	 LEFT OUTER JOIN #__sdi_namespace as child_namespace
					  		 ON child_namespace.id=c.namespace_id
					     LEFT OUTER JOIN #__sdi_namespace as relation_namespace
					  		 ON relation_namespace.id=rel.namespace_id
					  	 LEFT OUTER JOIN #__sdi_namespace as attributetype_namespace
					  		 ON attributetype_namespace.id=t.namespace_id
				  WHERE  rel.parent_id=".$parent."
				  		 AND 
				  		 prof.profile_id=".$profile_id."
				  		 AND 
				  		 rel.published = 1
				  		 AND
				  		 (
				  		 	(accountrel_attribute.account_id is null or accountrel_attribute.account_id=".$account_id.")
				  		 	OR
				  		 	(accountrel_class.account_id is null or accountrel_class.account_id=".$account_id.")
				  		 )
				  ORDER BY rel.ordering, rel.id";		
		$database->setQuery( $query );
		
		//echo $database->getquery()."<br>";
		//$rowAttributeChilds = array_merge( $rowAttributeChilds, $database->loadObjectList() );
		$rowChilds = array_merge( $rowChilds, $database->loadObjectList() );

		// Parcours des attributs enfants
		//foreach($rowAttributeChilds as $child)
		foreach($rowChilds as $child)
		{
			// Traitement d'une relation vers un attribut
			if ($child->attribute_id <> null)
			{
				$label = JText::_($child->rel_guid."_LABEL");
				if ($label == null or substr($label, -6, 6) == "_LABEL")
					$label = $child->rel_name;
				
				// L'aide contextuelle vient de la relation
				// Si la relation n'en a pas, on prend celle de l'attribut
				// Si l'attribut n'en a pas on prend le nom de l'attribut
				$tip = JText::_(strtoupper($child->rel_guid)."_INFORMATION");
				if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
				{
					$tip = JText::_(strtoupper($child->attribute_guid)."_INFORMATION");
					if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
						$tip = ""; //$child->attribute_name;			
				}	
				//echo " > ".$child->attribute_isocode." - ".$label."<br>";
				
				// Le mod�le de saisie
				//$regex = html_Metadata::cleanText($child->attribute_pattern);
				$regex = addslashes($child->attribute_pattern);
				
				if ($regex == null)
					$regex = "";
				
				// Le message � afficher en cas d'erreur de saisie selon le mod�le
				$regexmsg = JText::_(strtoupper($child->attribute_guid)."_REGEXMSG");
				if ($regexmsg == null or substr($regexmsg, -9, 9) == "_REGEXMSG")
				{
					$regexmsg = "";			
				}	

				//echo " > ".$child->attribute_isocode." - ".$regex." - ".$regexmsg."<br>";
				
				// Mise en place des contr�les
				// Cas des champs syst�me qui doivent �tre d�sactiv�s
				$disabled = "false";
				if ($child->attribute_system)
					$disabled = "true";
				
				// Cas des champs qui sont obligatoires
				$mandatory = "false";
				if ($child->rel_lowerbound > 0)
					$mandatory = "true";
	
				// Longueur max des champs
				$maxLength = 999;
				if ($child->length)
					$maxLength = $child->length;
				
				// On regarde dans le XML s'il contient la balise correspondante au code ISO de l'attribut enfant,
				// et combien de fois au niveau courant
				//echo "Le scope pour l'attribut est <b>".$scope->nodeName."</b> et on recherche <b>".$child->attribute_id.$child->attribute_isocode."</b><br>";
				$mainNode = $xpathResults->query($child->attribute_isocode, $scope);
				$attributeCount = $mainNode->length;
	
				//echo "L'attribut enfant ".$child->attribute_isocode." existe ".$attributeCount." fois.<br>";
				
				if ($child->attribute_type == 6 and $attributeCount > 1)
					$attributeCount = 1;
				//echo "Pardon, ".$attributeCount." fois.<br>";
				
				
				// On n'entre dans cette boucle que si on a trouv� au moins une occurence de l'attribut dans le XML
				for ($pos=0; $pos<$attributeCount; $pos++)
				{
					/*
					 * COMPREHENSION DU MODELE
					 * La relation vers l'attribut n'a jamais de code ISO.
					 */  
					//echo "----------- On traite ".$child->attribute_isocode." [".$child->attribute_type."] -----------<br>";
				
					// Construction du master qui permet d'ajouter des occurences de la relation.
					// Le master contient les donn�es de la premi�re occurence.
					if ($pos==0)
					{	
						//echo $pos.") mainNode: ".$mainNode->nodeName."<br>";
						$attributeScope = $mainNode->item($pos);
						//echo "Cas ".$pos."<br>";
						// Traitement de l'attribut enfant.
						// Changement du scope pour le noeud correspondant � cette occurence de l'attribut dans le XML
						//echo $pos.") attributeScope: ".$attributeScope->nodeName."<br>";
						// Modifier le path d'acc�s � l'attribut
						// $queryPath = $queryPath."/".$child->attribute_id;
						
						// Si on est en train de traiter un attribut de type liste, il faut encore r�cup�rer
						// Le code ISO de la liste, sinon on r�cup�re le code ISO du type d'attribut
						if ($child->attribute_type == 6 )
							$type_isocode = $child->list_isocode;
						else
							$type_isocode = $child->t_isocode;
	
						// Modifier le path d'acc�s � l'attribut
						$queryPath = $queryPath."/".$child->attribute_isocode."/".$type_isocode;
						
						// Construction du nom de l'attribut
						$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode);
						$currentName = $name."__".($pos+1);
	
						// Traitement de chaque attribut selon son type
						switch($child->attribute_type)
						{
							// Guid (toujours disabled, donc toujours un champ cach�)
							case 1:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Si le xpathParentId est d�fini, regarder si on est au xpath souhait�.	
								if ($this->parentId_attribute <> "")
								{
									//gmd_MD_Metadata-gmd_parentIdentifier-gco_CharacterString__1
									// V�rification qu'on est bien dans l'attribut choisi. La classe n'a pas d'utilit�
									if ($parentScope <> NULL and $this->parentId_attribute == $child->attribute_id)
									{
										// Stocker le guid du parent
										$nodeValue = $this->parentGuid;
									}
								}
								
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createDisplayField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDisplayField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
								}
								break;
							// Text
							case 2:
								// Traitement de la classe enfant
								//echo $currentName."; recherche de ".$type_isocode." dans ".$attributeScope->nodeName.", enfant de ".$attributeScope->parentNode->nodeName;//."<br>";
								//echo "<br><b>".$parentScope->nodeName." - ".$scope->nodeName."</b><br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								// Cas où le noeud n'existe pas dans le XML. Inutile de rechercher la valeur
								if ($parentScope <> NULL and $parentScope->nodeName == $scope->nodeName)
									$nodeValue = "";
								else
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								
								//echo "<i>Trouve ".$nodeValue."</i><hr>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// Local
							case 3:
								// Traitement de la classe enfant
								//echo "Recherche de gco:CharacterString dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								//echo "Trouve ".$node->length."<br>";
											 	
								if ($node->length>0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue="";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$node->item($pos)->nodeValue."<br>";
								//print_r($node->item($pos)->nodeValue); echo "<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								/*if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
								*/
								$defaultVal = "";
								
								switch ($child->rendertype_id)
								{
									default:
										/* Traitement sp�cifique aux langues */
										
										// Stockage du path pour atteindre ce noeud du XML
										//$queryPath = $child->attribute_isocode."/gmd:LocalisedCharacterString";
										//$queryPath = "gmd:LocalisedCharacterString";
											
										$listNode = $xpathResults->query($child->attribute_isocode, $scope);
										$listCount = $listNode->length;
										//echo "2) Il y a ".$listCount." occurences de ".$child->attribute_isocode." dans ".$scope->nodeName."<br>";
										for($pos=0;$pos<=$listCount; $pos++)
										{
											if ($pos==0)
											{	
												$currentScope=$listNode->item($pos);
												// Traitement de la multiplicit�
												// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
												//$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
												$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
								
												//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
												$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
												$this->javascript .="
													// Créer un nouveau fieldset
													var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
													".$parentFieldsetName.".add(".$fieldsetName.");
												";

												foreach($this->langList as $row)
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													if ($row->defaultlang)
													{
														$langNode = $xpathResults->query("gco:CharacterString", $currentScope);
														//echo "2a) Il y a ".$langNode->length." occurences de gco:CharacterString dans ".$currentScope->nodeName."<br>";
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
															//$nodeValue = "";
														}
													}
													else
													{
														//print_r($row);echo "<br>";
														//echo $row->language."<br>";
														//$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
														$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $currentScope);
														//echo "2b) Il y a ".$langNode->length." occurences de gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString dans ".$currentScope->nodeName."<br>";
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
															//$nodeValue = "";
														}
													}
													
													//$nodeValue = "";
													
													//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
													//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
													//echo $LocLangName." - ".$nodeValue."<br>";
													// Selon le rendu de l'attribut, on fait des traitements diff�rents
													switch ($child->rendertype_id)
													{
														// Textarea
														case 1:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".html_Metadata::cleanText(JText::_($row->label))."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".html_Metadata::cleanText($defaultVal)."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														// Textbox
														case 5:
															$this->javascript .="
															//console.log(".$parentFieldsetName.".getId() + ': ' + ".$parentFieldsetName.".collapsed + '\\n\\r a la creation de ' + '".$currentName."' + '\\n\\r' + ".$parentFieldsetName.".masterflow);
															".$fieldsetName.".add(createTextField('".$LocLangName."', '".html_Metadata::cleanText(JText::_($row->label))."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".html_Metadata::cleanText($defaultVal)."', ".$disabled.", '".$maxLength."', '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														default:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".html_Metadata::cleanText(JText::_($row->label))."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".html_Metadata::cleanText($defaultVal)."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
													}
													
													if ($child->attribute_system)
													{
														$this->javascript .="
														".$parentFieldsetName.".add(createHidden('".$LocLangName."_hiddenVal', '".$LocLangName."_hiddenVal', '".$nodeValue."'));
														";
													}
												}
											}
											else
											{
												$currentScope=$listNode->item($pos-1);
												// Traitement de la multiplicit�
												// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
												$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
												$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
								
												$this->javascript .="
												var master = Ext.getCmp('".$master."');						
												";
												
												//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
												$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
												$this->javascript .="
													var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
														".$parentFieldsetName.".add(".$fieldsetName.");
												";
													
												foreach($this->langList as $row)
												{
													if ($row->defaultlang)
													{
														$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
														$langNode = $xpathResults->query("gco:CharacterString", $currentScope);
														//echo "2) Il y a ".$langNode->length." occurences de gco:CharacterString dans ".$currentScope->nodeName."<br>";
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
															//$nodeValue = "";
														}
													}
													else
													{
														//print_r($row);echo "<br>";
														//echo $row->language."<br>";
														$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
														$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $currentScope);
														//echo "2) Il y a ".$langNode->length." occurences de gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString dans ".$currentScope->nodeName."<br>";
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
															//$nodeValue = "";
														}
													}
													
													
													//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
													//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
													switch ($child->rendertype_id)
													{
														// Textarea
														case 1:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														// Textbox
														case 5:
															$this->javascript .="
															".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														default:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
													}
													
													if ($child->attribute_system)
													{
														$this->javascript .="
														".$fieldsetName.".add(createHidden('".$LocLangName."_hiddenVal', '".$LocLangName."_hiddenVal', '".$nodeValue."'));
														";
													}
												}
											}
										}
										if ($listCount==0 and $child->rel_lowerbound>0)
										{
											// Traitement de la multiplicit�
											// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
											$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__2";
							
											$this->javascript .="
											var master = Ext.getCmp('".$master."');						
											";
											
											//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
											$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
											$this->javascript .="
											var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
												".$parentFieldsetName.".add(".$fieldsetName.");
											";
												
											foreach($this->langList as $row)
											{
												if ($row->defaultlang)
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gco:CharacterString", $attributeScope);
													//echo "2) Il y a ".$langNode->length." occurences de gco:CharacterString dans ".$currentScope->nodeName."<br>";
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												else
												{
													//print_r($row);echo "<br>";
													//echo $row->language."<br>";
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
													//echo "2) Il y a ".$langNode->length." occurences de gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString dans ".$currentScope->nodeName."<br>";
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
												//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
												switch ($child->rendertype_id)
												{
													// Textarea
													case 1:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													// Textbox
													case 5:
														$this->javascript .="
														".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													default:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
												}
													
												if ($child->attribute_system)
												{
													$this->javascript .="
													".$fieldsetName.".add(createHidden('".$LocLangName."_hiddenVal', '".$LocLangName."_hiddenVal', '".$nodeValue."'));
													";
												}
											}
										}
										
										break;
								}
								break;
							// Number
							case 4:
								// gco-distance
							case 12:
								// gco-Integer
							case 13:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
								
							
							// Date
							case 5:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// List
							case 6:
								// Traitement de la classe enfant
								//echo "Recherche de <b>".$type_isocode."</b> dans <b>".$attributeScope->nodeName."</b><br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id;
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	 
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contLabel = JText::_($cont->guid."_LABEL");
											$cond1 = !$contLabel;
											$cond2 = substr($contLabel, -6, 6) == "_LABEL";
											if ($cond1 or $cond2)
												$contLabel = $cont->name;
											
											$dataValues[$cont->value] = html_Metadata::cleanText($contLabel);
											
									 	}
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
									 	$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	
									 	for ($pos=0;$pos<$relNode->length;$pos++)
									 	{
									 		$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
									 		
									 		if ($listNode->length > 0)
									 		{
									 			if ($child->codeList <> null)
													$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
										 		else
										 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
									 		}
									 		/*else
								 				$nodeValues[]="";*/
									 	}
									 	
									 	//echo "existant: ".count($nodeValues)."<br>";
									 	
									 	// S'il n'y a pas de valeurs existantes, r�cup�rer les valeurs par d�faut
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
									 		// Elements s�lectionn�s par d�faut
											$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id;
											$database->setQuery( $query );
											//echo $database->getQuery()."<br>";
											$selectedContent = $database->loadObjectList();
											
										 	// Construction de la liste
										 	foreach ($selectedContent as $cont)
										 	{
										 		$nodeValues[] = html_Metadata::cleanText($cont->value);
										 		$nodeDefaultValues[] = html_Metadata::cleanText($cont->value);
									 		}
										}
									 	
										//echo "selectionne par defaut: "; print_r($nodeDefaultValues); echo "<hr>";
									 	
										switch ($child->rendertype_id)
										{
											// Checkbox
											case 2:
												if ($child->rel_lowerbound < $child->rel_upperbound)
											 	{
											 		$this->javascript .="
													var valueList = ".HTML_metadata::array2checkbox($listName, false, $dataValues, $nodeValues, html_Metadata::cleanText(JText::_($tip))).";
											     	var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
											     	var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
											     	// La liste
											     	".$parentFieldsetName.".add(createCheckboxGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
											     	";
											 	}
											 	break;
											// Radiobutton
											case 3:
												if ($child->rel_lowerbound == $child->rel_upperbound)
											 	{
											 		$this->javascript .="
													var valueList = ".HTML_metadata::array2checkbox($listName, true, $dataValues, $nodeValues, html_Metadata::cleanText(JText::_($tip))).";
											     	var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
											     	var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
											     	// La liste
											     	".$parentFieldsetName.".add(createRadioGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
											     	";
											 	}
											 	break;
											// List
											case 4:
											default:
												// Deux traitement pour deux types de listes
											 	if (($child->rel_upperbound - $child->rel_lowerbound) > 1 )
											 	{
											 		$this->javascript .="
													var valueList = ".HTML_metadata::array2extjs($dataValues, false).";
											     	var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
											     	var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
											     	// La liste
											     	".$parentFieldsetName.".add(createMultiSelector('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
											     	";
											 	}
											 	else
											 	{
											 		$this->javascript .="
													var valueList = ".HTML_metadata::array2extjs($dataValues, true).";
												     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
												     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
											     	// La liste
												     ".$parentFieldsetName.".add(createComboBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
												    ";
											 	}
												 
												break;
										}
										
									 	break;
								}
								
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								
								
								break;
							// Link
							case 7:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// DateTime
							case 8:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
								
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								$nodeValue = substr($nodeValue, 0, 10);
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// TextChoice
							case 9:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	 
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contTitle = JText::_($cont->guid."_TITLE");
											$cond1 = !$contTitle;
											$cond2 = substr($contTitle, -6, 6) == "_TITLE";
											if ($cond1 or $cond2)
												$contTitle = "";
											
											$contContent = JText::_($cont->guid."_CONTENT");
											$cond1 = !$contContent;
											$cond2 = substr($contContent, -6, 6) == "_CONTENT";
											if ($cond1 or $cond2)
												$contContent = $cont->value;
												
											if ($contTitle == "")
												$dataValues[$cont->guid."_TITLE"] = array(html_Metadata::cleanText($contContent), $cont->guid);
											else
												$dataValues[html_Metadata::cleanText($contTitle)] = array(html_Metadata::cleanText($contContent), $cont->guid);
									 	}
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
										$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	/*
									 	for ($pos=0;$pos<$relNode->length;$pos++)
									 	{
									 		$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
									 		echo $type_isocode." dans ".$relNode->item($pos)->nodeName."<br>";
									 		if ($listNode->length > 0)
									 		{
									 			if ($child->codeList <> null)
													$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
										 		else
										 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
									 		}
									 		else
								 				$nodeValues[]="";
									 	}
									 	*/
									 	//echo "existant: ".count($nodeValues)."<br>";
									 	$language =& JFactory::getLanguage();
										
									 	$node = $xpathResults->query($type_isocode, $relNode->item(0));
										//echo $type_isocode."[@locale='".$row->code."']"." dans ".$relNode->item(0)->nodeName."<br>";
										if ($node->length > 0)
								 		{
								 			// Chercher le titre associé au texte localisé souhaité, ou s'il n'y a pas de titre le contenu
								 			foreach ($content as $cont)
									 		{
									 			if ($cont->value == html_Metadata::cleanText($node->item(0)->nodeValue))
													$nodeValues[] = $cont->guid;
									 			
									 		}
											//echo html_Metadata::cleanText($node->item(0)->nodeValue)."<br>";
											//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
								 		}
										else
											$nodeValues[] = "";
									
								
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
										 	// Elements s�lectionn�s par d�faut
											$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
											$database->setQuery( $query );
											//echo $database->getQuery()."<br>";
											$selectedContent = $database->loadObjectList();
											
										 	// Construction de la liste
										 	foreach ($selectedContent as $cont)
										 	{
										 		//$nodeValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
										 		$nodeValues[] = $cont->guid;
										 		//$nodeDefaultValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
										 		$nodeDefaultValues[] = $cont->guid;
									 		}
										}
									 	
										//echo "selectionne par defaut: "; print_r($nodeValues); echo "<hr>";
									 	
										$simple=true;
										if ($child->rel_lowerbound>0)
											$simple = false;
											
								 		$this->javascript .="
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
									    ";
									break;
								}
								
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// LocaleChoice
							case 10:
								// Traitement de la classe enfant
								//echo "Recherche de gco:CharacterString dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								//echo "Trouve ".$node->length."<br>";
								
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	 
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contTitle = JText::_($cont->guid."_TITLE");
											$cond1 = !$contTitle;
											$cond2 = substr($contTitle, -6, 6) == "_TITLE";
											if ($cond1 or $cond2)
												$contTitle = "";
											
											$contContent = JText::_($cont->guid."_CONTENT");
											$cond1 = !$contContent;
											$cond2 = substr($contContent, -6, 6) == "_CONTENT";
											if ($cond1 or $cond2)
												$contContent = "";
												
											if ($contTitle == "")
												$dataValues[$cont->guid."_TITLE"] = array(html_Metadata::cleanText($contContent), $cont->guid);
											else
												$dataValues[html_Metadata::cleanText($contTitle)] = array(html_Metadata::cleanText($contContent), $cont->guid);
									 	}
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
									 	$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	/*
									 	for ($pos=0;$pos<$relNode->length;$pos++)
									 	{
									 		$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
									 		echo $type_isocode." dans ".$relNode->item($pos)->nodeName."<br>";
									 		if ($listNode->length > 0)
									 		{
									 			if ($child->codeList <> null)
													$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
										 		else
										 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
									 		}
									 		else
								 				$nodeValues[]="";
									 	}
									 	*/
									 	//echo "existant: ".count($nodeValues)."<br>";
									 	$language =& JFactory::getLanguage();
										
									 	// R�cup�rer le texte localis� stock�
									 	foreach($this->langList as $row)
										{
											if ($row->code_easysdi == $language->_lang)
											{
												if ($row->defaultlang)
													$node = $xpathResults->query("gco:CharacterString", $relNode->item(0));
												else
													$node = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $relNode->item(0));
												
												//echo $type_isocode."[@locale='".$row->code."']"." dans ".$relNode->item(0)->nodeName."<br>";
												
										 		if ($node->length > 0)
										 		{
										 			// Chercher le titre associé au texte localisé souhaité, ou s'il n'y a pas de titre le contenu
										 			$query = "SELECT t.title, t.content, c.guid 
															  FROM #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl 
															  WHERE c.guid=t.element_guid 
															        AND t.language_id=l.id 
															        AND l.codelang_id=cl.id 
															        AND cl.code='".$language->_lang."' 
															        AND t.content = '".html_Metadata::cleanText($node->item(0)->nodeValue)."'"." 
															        ORDER BY c.ordering";
													$database->setQuery( $query );
													//echo $database->getQuery()."<br>";
													//$cont_guid = $database->loadResult();
													
													//$nodeValues[] = $database->loadResult();
													$result = $database->loadObject();
													/* Mis en commentaire � cause du bug #3919
													 * if ($result->title <> "")
														$nodeValues[] = $result->title;
													else*/
														$nodeValues[] = $result->guid;

													//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
													//echo html_Metadata::cleanText($node->item(0)->nodeValue)."<br>";
													//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
										 		}
											}
										}

										//print_r($nodeValues); echo "<br>";
									 	
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
										 	// Elements s�lectionn�s par d�faut
											$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
											$database->setQuery( $query );
											//echo $database->getQuery()."<br>";
											$selectedContent = $database->loadObjectList();
											
										 	// Construction de la liste
										 	foreach ($selectedContent as $cont)
										 	{
										 		//$nodeValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
										 		//$nodeDefaultValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
										 		$nodeValues[] = $cont->guid;
												$nodeDefaultValues[] = $cont->guid;
									 		}
										}
									 	
										if (count($nodeValues) == 0)
											$nodeValues[] = "";
										//echo "selectionne par defaut: "; print_r($nodeValues); echo "<hr>";
									 	
										$simple=true;
										if ($child->rel_lowerbound>0)
											$simple = false;
										
								 		$this->javascript .="
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
									    ";
									break;
								}
								
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// Thesaurus GEMET
							case 11:
								//echo "1) ".$child->rel_lowerbound."', '".$child->rel_upperbound."<br>";
								//echo "Recherche de gco:CharacterString dans ".$attributeScope->nodeName."<br>";
								
								$uri =& JUri::getInstance();
		
								$language =& JFactory::getLanguage();
								
								$userLang="";
								$defaultLang="";
								$langArray = Array();
								foreach($this->langList as $row)
								{									
									if ($row->defaultlang) // Langue par d�faut de la m�tadonn�e
										$defaultLang = $row->gemetlang;
									
									if ($row->code_easysdi == $language->_lang) // Langue courante de l'utilisateur
										$userLang = $row->gemetlang;
										
									$langArray[] = $row->gemetlang;
								}
								/*print_r($langArray);
								echo "<hr>";
								print_r(str_replace('"', "'", HTML_metadata::array2json($langArray)));
								*/
								
								$value="";
								$listNode = $xpathResults->query($child->attribute_isocode, $scope);
								$listCount = $listNode->length;		
								//echo "Il y a ".$listCount." occurences de ".$child->attribute_isocode." dans ".$scope->nodeName."<br>";
								$nodeValues = array();
								for($keyPos=0;$keyPos<$listCount; $keyPos++)
								{
									$currentScope=$listNode->item($keyPos);
									//echo "Position ".$keyPos.", ".$currentScope->nodeName." avec ".$currentScope->nodeValue."<br>";
								
									if ($currentScope and $currentScope->nodeName <> "")
									{
									$nodeValue= "";
									$nodeKeyword= "";
									// R�cup�rer le texte localis� stock�
									foreach($this->langList as $row)
									{
										//echo $row->gemetlang."<br>";
										if ($row->defaultlang)
										{
											$keyNode = $xpathResults->query("gco:CharacterString", $currentScope);
											//echo "Il y a ".$keyNode->length." occurences de gco:CharacterString dans ".$currentScope->nodeName."<br>";
											if ($keyNode->length > 0)
											{
												$nodeValue .= $row->gemetlang.": ".html_Metadata::cleanText($keyNode->item(0)->nodeValue).";";
												//$nodeKeyword = html_Metadata::cleanText($keyNode->item(0)->nodeValue);
											}
										}
										else
										{
											$keyNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $currentScope);
											//echo "Il y a ".$keyNode->length." occurences de gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString dans ".$currentScope->nodeName."<br>";
											if ($keyNode->length > 0)
												$nodeValue .= $row->gemetlang.": ".html_Metadata::cleanText($keyNode->item(0)->nodeValue).";";
											
										}
										
										if ($row->gemetlang == $userLang and $keyNode->item(0))
											$nodeKeyword = html_Metadata::cleanText($keyNode->item(0)->nodeValue);
									}
									}
									if ($nodeKeyword <> "")
										$nodeValues[] = "{keyword:'$nodeKeyword', value: '$nodeValue'}";
									//echo $nodeValue."<br>";
									//echo $nodeKeyword."<br>";
									
								}

								if (count($nodeValues)>0)
									$value = "[".implode(",",$nodeValues)."]";
								else
									$value = "[]";
								
								$this->javascript .="
								// Cr�er un bouton pour appeler la fen�tre de choix dans le Thesaurus GEMET
								var winthge;
								
								Ext.BLANK_IMAGE_URL = '".$uri->base(true)."/components/com_easysdi_catalog/ext/resources/images/default/s.gif';
								
								var thes = new ThesaurusReader({
																  id:'".$currentName."_PANEL_THESAURUS',
																  lang: '".$userLang."',
															      outputLangs: ".str_replace('"', "'", HTML_metadata::array2json($langArray)).", //['en', 'cs', 'fr', 'de'] 
															      separator: ' > ',
															      appPath: '".$uri->base(true)."/administrator/components/com_easysdi_catalog/js/',
															      returnPath: false,
															      returnInspire: true,
															      width: 300, 
															      height:400,
															      win_title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_ALERT'))."',
															      layout: 'fit',
															      targetField: '".$currentName."',
															      proxy: '".$uri->base(true)."/administrator/components/com_easysdi_catalog/js/proxy.php?url=',
															      handler: function(result){
															      				var target = Ext.ComponentMgr.get(this.targetField);
															    				var s = '';
												      		    					
																      		    var reliableRecord = result.terms[thes.lang];
																      		    
																      		    // S'assurer que le mot-cl� n'est pas d�j� s�lectionn�
																      		    if (!target.usedRecords.containsKey(reliableRecord))
																				{
																					// Sauvegarde dans le champs SuperBoxSelect des mots-cl�s dans toutes les langues de EasySDI
																				    for(l in result.terms) 
																				    {
																				    	s += l+': '+result.terms[l]+';';
																		                  }
																				    target.addItem({keyword:result.terms[this.lang], value: s});
																				}
																				else
																				{
																					Ext.MessageBox.alert('".JText::_('CATALOG_EDITMETADATA_THESAURUSSELECT_MSG_SUCCESS_TITLE')."', 
															                    						 '".JText::_('CATALOG_EDITMETADATA_THESAURUSSELECT_MSG_SUCCESS_TEXT')."');
																				
																				}
																			}
															  });
								
								 							  
								".$parentFieldsetName.".add(
									new Ext.Button({
										id:'".$currentName."_button',
										text:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_BUTTON'))."',
										handler: function()
								                {
								                	// Cr�er une iframe pour demander � l'utilisateur le type d'import
													if (!winthge)
														winthge = new Ext.Window({
														                id:'".$currentName."_win',
																  		itemId:'".$currentName."_win',
																  		title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_ALERT'))."',
														                width:500,
														                height:500,
														                closeAction:'hide',
														                layout:'fit', 
																	    border:true, 
																	    closable:true, 
																	    renderTo:Ext.getBody(), 
																	    frame:true,
																	    listeners: {
																			'show': function (animateTarget, cb, scope)
																					{
																						this.items.get(0).emptyAll();
																						this.items.get(0).getTopConcepts(this.items.get(0).CONCEPT);
																					}
																			},
																	    items:[thes]
														            });
														else
														{
															// Vider les champs du composant Thesaurus
														}
														
														winthge.show();
										        	},
										
								        // Champs sp�cifiques au clonage
								        dynamic:true,
								        minOccurs:1,
							            maxOccurs:1,
							            clone: false,
										clones_count: 1,
							            extendedTemplate: null
									})
								);
								
								// Cr�er le champ qui contiendra les mots-cl�s du thesaurus choisis
								".$parentFieldsetName.".add(createSuperBoxSelect('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."', ".$value.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."'));
								";
								break;
							default:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
						}
					}
					// Occurences qui ne correspondent pas au master
					else
					{
						$attributeScope = $mainNode->item($pos);
						//echo $pos.") attributeScope: ".$attributeScope->nodeName."<br>";
						// Positionner le code ISO de l'attribut
						//$queryPath = $queryPath."/".$child->attribute_id;
						
						if ($child->attribute_type == 6 )
							$type_isocode = $child->list_isocode;
						else
							$type_isocode = $child->t_isocode;
	
						$queryPath = $queryPath."/".$child->attribute_isocode."/".$type_isocode;
						
						
						//$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
						$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode)."__1";
						//$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode)."__1";
						//$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode)."__".($pos+1);
						//$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+2);
						//$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
						$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode)."__".($pos+1);
						
						$currentName = $name;
						
						//echo $master." - ".$name." - ".$currentName."<br>";
						
						if ($child->attribute_type <> 9 and $child->attribute_type <> 10 and $child->attribute_type <> 11)
						{
							//echo $type_isocode." - ".$attributeScope->nodeName."<br>";
							// Traitement de l'attribut enfant
							//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
							$node = $xpathResults->query($type_isocode, $attributeScope);
							//echo $node->length." - ".$node->item(0)->nodeName."<br>";
							
							// Si le fieldset n'existe pas, inutile de r�cup�rer une valeur
								if ($parentScope <> NULL and $parentScope->nodeName == $scope->nodeName)
									$nodeValue = "";
								else
									$nodeValue = html_Metadata::cleanText($node->item($pos-1)->nodeValue);
								
							
							// R�cup�ration de la valeur par d�faut, s'il y a lieu
							if ($child->attribute_default <> "" and $nodeValue == "")
								$nodeValue = html_Metadata::cleanText($child->attribute_default);
		
							//echo $nodeValue."<br>";
						}
						$this->javascript .="
						var master = Ext.getCmp('".$master."');						
						";
						
						// Traitement de chaque attribut selon son type
						switch($child->attribute_type)
						{
							// Guid
							case 1:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createDisplayField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDisplayField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
								}
								break;
							// Text
							case 2:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// Local
							case 3:
								// Le else n'existe pas pour les langues
								break;
							// Number//distance/integer
							case 4:case 12:case 13:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// Date
							case 5:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// List
							case 6:
								// Le else n'existe pas pour les listes;
								break;
							// Link
							case 7:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// DateTime
							case 8:
								$nodeValue = substr($nodeValue, 0, 10);
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// TextChoice
							case 9:
								// Traitement de la classe enfant
								//echo "Recherche de gco:CharacterString dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								//echo "Trouve ".$node->length."<br>";
								
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
								else
									$nodeValue = "";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	//$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	$masterlistName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
										$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
					  
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contTitle = JText::_($cont->guid."_TITLE");
											$cond1 = !$contTitle;
											$cond2 = substr($contTitle, -6, 6) == "_TITLE";
											if ($cond1 or $cond2)
												$contTitle = "";
											
											$contContent = JText::_($cont->guid."_CONTENT");
											$cond1 = !$contContent;
											$cond2 = substr($contContent, -6, 6) == "_CONTENT";
											if ($cond1 or $cond2)
												$contContent = $cont->value;
												
											if ($contTitle == "")
												$dataValues[$cont->guid."_TITLE"] = array(html_Metadata::cleanText($contContent), $cont->guid);
											else
												$dataValues[html_Metadata::cleanText($contTitle)] = array(html_Metadata::cleanText($contContent), $cont->guid);
									 	}
									 	
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
									 	$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	/*
									 	for ($pos=0;$pos<$relNode->length;$pos++)
									 	{
									 		$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
									 		echo $type_isocode." dans ".$relNode->item($pos)->nodeName."<br>";
									 		if ($listNode->length > 0)
									 		{
									 			if ($child->codeList <> null)
													$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
										 		else
										 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
									 		}
									 		else
								 				$nodeValues[]="";
									 	}
									 	*/
									 	//echo "existant: ".count($nodeValues)."<br>";
									 	$language =& JFactory::getLanguage();
										
									 	$node = $xpathResults->query($type_isocode, $relNode->item(0));
										//echo $type_isocode."[@locale='".$row->code."']"." dans ".$relNode->item(0)->nodeName."<br>";
										if ($node->length > 0)
								 		{
								 			// Chercher le titre associ� au texte localis� souhait� et 
											foreach ($content as $cont)
									 		{
									 			if ($cont->value == html_Metadata::cleanText($node->item(0)->nodeValue))
													$nodeValues[] = $cont->guid;
									 		}
											//echo html_Metadata::cleanText($node->item(0)->nodeValue)."<br>";
											//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
								 		}
										else
											$nodeValues[] = "";
									
								
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
										 	// Elements s�lectionn�s par d�faut
											$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
											$database->setQuery( $query );
											//echo $database->getQuery()."<br>";
											$selectedContent = $database->loadObjectList();
											
										 	// Construction de la liste
										 	foreach ($selectedContent as $cont)
										 	{
										 		//$nodeValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
										 		//$nodeDefaultValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
												$nodeValues[] = $cont->guid;
												$nodeDefaultValues[] = $cont->guid;
										 	}
										}
									 	
										//echo "selectionne par defaut: "; print_r($nodeValues); echo "<hr>";
									 	
										$simple=true;
										if ($child->rel_lowerbound>0)
											$simple = false;
										
										$this->javascript .="
										var master = Ext.getCmp('".$masterlistName."'); 
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."', master, true));
									    ";
										
								 	break;
								}
								
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// LocaleChoice
							case 10:
								// Traitement de la classe enfant
								//echo "Recherche de gco:CharacterString dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								//echo "Trouve ".$node->length."<br>";
								
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
								else
									$nodeValue = "";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// R�cup�ration de la valeur par d�faut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	//$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	$masterlistName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
										$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
					  
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contTitle = JText::_($cont->guid."_TITLE");
											$cond1 = !$contTitle;
											$cond2 = substr($contTitle, -6, 6) == "_TITLE";
											if ($cond1 or $cond2)
												$contTitle = "";
											
											$contContent = JText::_($cont->guid."_CONTENT");
											$cond1 = !$contContent;
											$cond2 = substr($contContent, -6, 6) == "_CONTENT";
											if ($cond1 or $cond2)
												$contContent = "";
												
											if ($contTitle == "")
												$dataValues[$cont->guid."_TITLE"] = array(html_Metadata::cleanText($contContent), $cont->guid);
											else
												$dataValues[html_Metadata::cleanText($contTitle)] = array(html_Metadata::cleanText($contContent), $cont->guid);
									 	}
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
										$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	/*
									 	for ($pos=0;$pos<$relNode->length;$pos++)
									 	{
									 		$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
									 		echo $type_isocode." dans ".$relNode->item($pos)->nodeName."<br>";
									 		if ($listNode->length > 0)
									 		{
									 			if ($child->codeList <> null)
													$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
										 		else
										 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
									 		}
									 		else
								 				$nodeValues[]="";
									 	}
									 	*/
									 	//echo "existant: ".count($nodeValues)."<br>";
									 	$language =& JFactory::getLanguage();
										
									 	// R�cup�rer le texte localis� stock�
									 	foreach($this->langList as $row)
										{
											if ($row->code_easysdi == $language->_lang)
											{
												if ($row->defaultlang)
													$node = $xpathResults->query("gco:CharacterString", $attributeScope);
												else
													$node = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
												//$node = $xpathResults->query("gco:CharacterString", $attributeScope);
										
												//echo $type_isocode."[@locale='".$row->code."']"." dans ".$relNode->item(0)->nodeName."<br>";
												if ($node->length > 0)
										 		{
										 			// Chercher le titre associ� au texte localis� souhait�, ou s'il n'y a pas de titre le contenu
													$query = "SELECT t.title, t.content, c.guid FROM #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND t.content = '".html_Metadata::cleanText($node->item(0)->nodeValue)."'"." ORDER BY c.ordering";
													$database->setQuery( $query );
													//echo $database->getQuery()."<br>";
													//$cont_guid = $database->loadResult();
													
													//$nodeValues[] = $database->loadResult();
													$result = $database->loadObject();
													/* Mis en commentaire � cause du bug #3919
													 * if ($result->title <> "")
														$nodeValues[] = $result->title;
													else*/
														$nodeValues[] = $result->guid;

													//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
													//echo html_Metadata::cleanText($node->item(0)->nodeValue)."<br>";
													//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
										 		}
												/*else
													$nodeValues[] = "";*/
											}
										}
										
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
										 	// Elements s�lectionn�s par d�faut
											$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
											$database->setQuery( $query );
											//echo $database->getQuery()."<br>";
											$selectedContent = $database->loadObjectList();
											
										 	// Construction de la liste
										 	foreach ($selectedContent as $cont)
										 	{
										 		//$nodeValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
										 		//$nodeDefaultValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
												$nodeValues[] = $cont->guid;
												$nodeDefaultValues[] = $cont->guid;
										 	}
										}
									 	
										if (count($nodeValues) == 0)
											$nodeValues[] = "";
											
										//echo "selectionne par defaut: "; print_r($nodeValues); echo "<hr>";
									 	
										$simple=true;
										if ($child->rel_lowerbound>0)
											$simple = false;
										
								 		$this->javascript .="
										var master = Ext.getCmp('".$masterlistName."'); 
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."', master, true));
									    ";
									break;
								}
								
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// Thesaurus GEMET
							case 11:
								// Le Thesaurus GEMET  n'existe qu'en un exemplaire
								break;
							default:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
								}
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
						}
					}
				}
					
					
				// Si le XML ne contient aucune occurence de l'attribut
				// il faut le cr�er vide
				if ($attributeCount==0)
				{
					$nodeValue = "";
						
					// Positionner le code ISO de l'attribut
					//$queryPath = $queryPath."/".$child->attribute_id;
					$attributeScope = $scope;
					//echo "3) attributeScope: ".$attributeScope->nodeName."<br>";
						
					if ($child->attribute_type == 6 )
						$type_isocode = $child->list_isocode;
					else
						$type_isocode = $child->t_isocode;
	
					$queryPath = $queryPath."/".$type_isocode;
					$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode);
					$currentName = $name."__1";
					
					// R�cup�ration de la valeur par d�faut, s'il y a lieu
					if ($child->attribute_default <> "")
						$nodeValue = html_Metadata::cleanText($child->attribute_default);
				
					
					// Traitement de chaque attribut selon son type
					switch($child->attribute_type)
					{
						// Guid
						case 1:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								// Textbox
								case 5:
									$this->javascript .="
									".$parentFieldsetName.".add(createDisplayField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
									break;
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createDisplayField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
									break;
							}
							break;
						// Text
						case 2:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
									break;
								// Textbox
								case 5:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
									break;
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
							}
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// Local
						case 3:
							$defaultVal="";
							switch ($child->rendertype_id)
							{
								default:
									/* Traitement sp�cifique aux langues */
									
									// Stockage du path pour atteindre ce noeud du XML
									//$queryPath = $child->attribute_isocode."/gmd:LocalisedCharacterString";
									$queryPath = "gco:CharacterString";
											
									$listNode = $xpathResults->query($child->attribute_isocode, $attributeScope);
									$listCount = $listNode->length;
									//echo "2) Il y a ".$listCount." occurences de ".$child->attribute_isocode." dans ".$attributeScope->nodeName."<br>";
									for($pos=0;$pos<=$listCount; $pos++)
									{
										if ($pos==0)
										{	
											// Traitement de la multiplicit�
											// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
							
											//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
											$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
											$this->javascript .="
											var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
												".$parentFieldsetName.".add(".$fieldsetName.");
											";
												
											foreach($this->langList as $row)
											{
												if ($row->defaultlang)
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gco:CharacterString", $attributeScope);
													//echo "2) Il y a ".$langNode->length." occurences de gco:CharacterString dans ".$currentScope->nodeName."<br>";
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												else
												{
													//print_r($row);echo "<br>";
													//echo $row->language."<br>";
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
													//echo "2) Il y a ".$langNode->length." occurences de gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString dans ".$currentScope->nodeName."<br>";
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												
												//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
												//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
												// Selon le rendu de l'attribut, on fait des traitements diff�rents
												switch ($child->rendertype_id)
												{
													// Textarea
													case 1:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													// Textbox
													case 5:
														$this->javascript .="
														".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."','".$defaultVal."',  ".$disabled.", '".$maxLength."', '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													default:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."','".$defaultVal."',  ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
												}
												if ($child->attribute_system)
												{
													$this->javascript .="
													".$parentFieldsetName.".add(createHidden('".$LocLangName."_hiddenVal', '".$LocLangName."_hiddenVal', '".$nodeValue."'));
													";
												}	
											}
										}
										else
										{
											// Traitement de la multiplicit�
											// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
											$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
							
											$this->javascript .="
											var master = Ext.getCmp('".$master."');						
											";
											
											//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
											$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
											$this->javascript .="
											var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
												".$parentFieldsetName.".add(".$fieldsetName.");
											";
												
											foreach($this->langList as $row)
											{
												if ($row->defaultlang)
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gco:CharacterString", $attributeScope);
													//echo "2) Il y a ".$langNode->length." occurences de gco:CharacterString dans ".$currentScope->nodeName."<br>";
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												else
												{
													//print_r($row);echo "<br>";
													//echo $row->language."<br>";
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
													//echo "2) Il y a ".$langNode->length." occurences de gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString dans ".$currentScope->nodeName."<br>";
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												
												//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
												//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
												switch ($child->rendertype_id)
												{
													// Textarea
													case 1:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													// Textbox
													case 5:
														$this->javascript .="
														".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													default:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
												}
											}
										}
									}
									if ($listCount==0 and $child->rel_lowerbound>0)
									{
										// Traitement de la multiplicit�
										// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
										$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
										$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__2";
						
										$this->javascript .="
										var master = Ext.getCmp('".$master."');						
										";
										
										//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
										$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
										$this->javascript .="
										var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
											".$parentFieldsetName.".add(".$fieldsetName.");
										";
											
										foreach($this->langList as $row)
										{
											//print_r($row);echo "<br>";
											//echo $row->language."<br>";
											$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
					
											/*$node = $xpathResults->query($queryPath."[@locale='".$row->code."']", $attributeScope);
											if ($node->length > 0)
											$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
											else*/
										
											$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
											$nodeValue = html_Metadata::cleanText($database->loadResult());
											$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
											//$nodeValue = "";
													
					
											//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->code)." - ".$nodeValue."<br>";
											//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
											switch ($child->rendertype_id)
											{
												// Textarea
												case 1:
													$this->javascript .="
													".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
													";
													break;
												// Textbox
												case 5:
													$this->javascript .="
													".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
													";
													break;
												default:
													$this->javascript .="
													".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
													";
													break;
											}
										}
									}
									break;
							}
							break;
						// Number // distance/integer
						case 4:case 12:case 13:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
								// Textbox
								case 5:
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
							}
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// Date
						case 5:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
								// Textbox
								case 5:
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
							}
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// List
						case 6:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								default:
									// Traitement sp�cifique aux listes
									//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (2)<br>";					
									// Traitement des enfants de type list
									$content = array();
									//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
									$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id;
									$database->setQuery( $query );
									$content = $database->loadObjectList();
										
								 	$dataValues = array();
								 	$nodeValues = array();
							
								 	// Traitement de la multiplicit�
								 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
								 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
								 	 
								 	// Construction de la liste
								 	foreach ($content as $cont)
								 	{
								 		$contLabel = JText::_($cont->guid."_LABEL");
										$cond1 = !$contLabel;
										$cond2 = substr($contLabel, -6, 6) == "_LABEL";
										if ($cond1 or $cond2)
											$contLabel = $cont->name;
										
									 	$dataValues[$cont->value] = html_Metadata::cleanText($contLabel);
								 	}
									
								 	//echo "2)Contenu: "; print_r($dataValues); echo "<br>";
	
								 	$relNode = $xpathResults->query($child->attribute_isocode, $attributeScope);
								 		
								 	for ($pos=0;$pos<$relNode->length;$pos++)
								 	{
								 		$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
								 		if ($listNode->length > 0)
								 		if ($child->codeList <> null)
										//if ($content[0]->l_codeValue)
								 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
								 		else
								 			$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
								 		/*else
								 			$nodeValues[]="";*/
								 	}
									//echo "existant: ".count($nodeValues)."<br>";
									
									// S'il n'y a pas de valeurs existantes, r�cup�rer les valeurs par d�faut
									$nodeDefaultValues = array();
									if (count($nodeValues) == 0)
								 	{
								 		// Elements s�lectionn�s par d�faut
										$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id;
										$database->setQuery( $query );
										//echo $database->getQuery()."<br>";
										$selectedContent = $database->loadObjectList();
										
									 	// Construction de la liste
									 	foreach ($selectedContent as $cont)
									 	{
									 		$nodeValues[] = html_Metadata::cleanText($cont->value);
								 			$nodeDefaultValues[] = html_Metadata::cleanText($cont->value);
								 		}
									}
								 	
									//echo "selectionne par defaut: "; print_r($nodeValues); echo "<hr>";
									 	
								 		
								 	switch ($child->rendertype_id)
									{
										// Checkbox
										case 2:
											if ($child->rel_lowerbound < $child->rel_upperbound)
										 	{
										 		$this->javascript .="
												var valueList = ".HTML_metadata::array2checkbox($listName, false, $dataValues, $nodeValues, html_Metadata::cleanText(JText::_($tip))).";
										     	var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
										     	var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
										     	// La liste
										     	".$parentFieldsetName.".add(createCheckboxGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
										     	";
										 	}
										 	break;
										// Radiobutton
										case 3:
											if ($child->rel_lowerbound == $child->rel_upperbound)
										 	{
										 		$this->javascript .="
												var valueList = ".HTML_metadata::array2checkbox($listName, true, $dataValues, $nodeValues, html_Metadata::cleanText(JText::_($tip))).";
										     	var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
										     	var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
										     	// La liste
										     	".$parentFieldsetName.".add(createRadioGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
										     	";
										 	}
										 	break;
										// List
										case 4:
										default:
											// Deux traitement pour deux types de listes
										 	if (($child->rel_upperbound - $child->rel_lowerbound) > 1 )
										 	{
										 		$this->javascript .="
												var valueList = ".HTML_metadata::array2extjs($dataValues, false).";
										     	var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
										     	var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
										     	
										     	// La liste
										     	".$parentFieldsetName.".add(createMultiSelector('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
										     	";
										 	}
										 	else
										 	{
										 		$this->javascript .="
												var valueList = ".HTML_metadata::array2extjs($dataValues, true).";
											     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
											     var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
										     	
											     // La liste
											     ".$parentFieldsetName.".add(createComboBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
											    ";
										 	}										 
											break;
									}
								 	break;
							}
							
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
								";
							}
							
							break;
						// Link
						case 7:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
								// Textbox
								case 5:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
									break;
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
							}
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// DateTime
						case 8:
							$nodeValue = substr($nodeValue, 0, 10);
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
								// Textbox
								case 5:
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
							}
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
							// TextChoice
							case 9:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	 
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contTitle = JText::_($cont->guid."_TITLE");
											$cond1 = !$contTitle;
											$cond2 = substr($contTitle, -6, 6) == "_TITLE";
											if ($cond1 or $cond2)
												$contTitle = "";
											
											$contContent = JText::_($cont->guid."_CONTENT");
											$cond1 = !$contContent;
											$cond2 = substr($contContent, -6, 6) == "_CONTENT";
											if ($cond1 or $cond2)
												$contContent = $cont->value;
												
											if ($contTitle == "")
												$dataValues[$cont->guid."_TITLE"] = array(html_Metadata::cleanText($contContent), $cont->guid);
											else
												$dataValues[html_Metadata::cleanText($contTitle)] = array(html_Metadata::cleanText($contContent), $cont->guid);
									 	}
									 	
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
									 	$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	
										// Elements s�lectionn�s par d�faut
										$nodeDefaultValues = array();
										$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
										$database->setQuery( $query );
										//echo $database->getQuery()."<br>";
										$selectedContent = $database->loadObjectList();
										
									 	// Construction de la liste
									 	foreach ($selectedContent as $cont)
									 	{
									 		//$nodeValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
									 		//$nodeDefaultValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
											$nodeValues[] = $cont->guid;
											$nodeDefaultValues[] = $cont->guid;
									 	}
								 										 		
								 		$simple=true;
										if ($child->rel_lowerbound>0)
											$simple = false;
										
										$this->javascript .="
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									    var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									    var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									    ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
									    ";
								}
								
								
								if ($child->attribute_system)
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// LocaleChoice
							case 10:
								// Selon le rendu de l'attribut, on fait des traitements diff�rents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement sp�cifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicit�
									 	// R�cup�ration du path du bloc de champs qui va �tre cr�� pour construire le nom
									 	$listName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
									 	 
									 	// Construction de la liste
									 	foreach ($content as $cont)
									 	{
									 		$contTitle = JText::_($cont->guid."_TITLE");
											$cond1 = !$contTitle;
											$cond2 = substr($contTitle, -6, 6) == "_TITLE";
											if ($cond1 or $cond2)
												$contTitle = "";
											
											$contContent = JText::_($cont->guid."_CONTENT");
											$cond1 = !$contContent;
											$cond2 = substr($contContent, -6, 6) == "_CONTENT";
											if ($cond1 or $cond2)
												$contContent = "";
												
											if ($contTitle == "")
												$dataValues[$cont->guid."_TITLE"] = array(html_Metadata::cleanText($contContent), $cont->guid);
											else
												$dataValues[html_Metadata::cleanText($contTitle)] = array(html_Metadata::cleanText($contContent), $cont->guid);
									 	}
										//echo "1)Contenu: "; print_r($dataValues); echo "<br>";
										
									 	$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									 	
										// Elements s�lectionn�s par d�faut
										$nodeDefaultValues = array();
										$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
										$database->setQuery( $query );
										//echo $database->getQuery()."<br>";
										$selectedContent = $database->loadObjectList();
										
									 	// Construction de la liste
									 	foreach ($selectedContent as $cont)
									 	{
									 		//$nodeValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
									 		//$nodeDefaultValues[] = html_Metadata::cleanText(JText::_($cont->guid."_TITLE"));
									 		$nodeValues[] = $cont->guid;
											$nodeDefaultValues[] = $cont->guid;
								 		}

								 		$simple=true;
										if ($child->rel_lowerbound>0)
											$simple = false;
										
								 		$this->javascript .="
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									    var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									    var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									    // La liste
									    ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
									    ";
									break;
								}
								
								
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
								";
							}
							
							break;
						// Thesaurus GEMET
							case 11:
								//echo "3) ".$currentName."', '".html_Metadata::cleanText(JText::_($label))."', '".$child->rel_lowerbound."', '".$child->rel_upperbound."<br>";
								
								$uri =& JUri::getInstance();
		
								$language =& JFactory::getLanguage();
								
								$userLang="";
								$defaultLang="";
								$langArray = Array();
								foreach($this->langList as $row)
								{									
									if ($row->defaultlang) // Langue par d�faut de la m�tadonn�e
										$defaultLang = $row->gemetlang;
									
									if ($row->code_easysdi == $language->_lang) // Langue courante de l'utilisateur
										$userLang = $row->gemetlang;
										
									$langArray[] = $row->gemetlang;
								}
								/*print_r($langArray);
								echo "<hr>";
								print_r(str_replace('"', "'", HTML_metadata::array2json($langArray)));
								*/
													
								$this->javascript .="
								// Cr�er un bouton pour appeler la fen�tre de choix dans le Thesaurus GEMET
								var winthge;
								
								Ext.BLANK_IMAGE_URL = '".$uri->base(true)."/components/com_easysdi_catalog/ext/resources/images/default/s.gif';
								
								var thes = new ThesaurusReader({
																  id:'".$currentName."_PANEL_THESAURUS',
																  lang: '".$userLang."',
															      outputLangs: ".str_replace('"', "'", HTML_metadata::array2json($langArray)).", //['en', 'cs', 'fr', 'de'] 
															      separator: ' > ',
															      appPath: '".$uri->base(true)."/administrator/components/com_easysdi_catalog/js/',
															      returnPath: false,
															      returnInspire: true,
															      width: 300, 
															      height:400,
															      win_title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_ALERT'))."',
															      layout: 'fit',
															      targetField: '".$currentName."',
															      proxy: '".$uri->base(true)."/administrator/components/com_easysdi_catalog/js/proxy.php?url=',
															      handler: function(result){
															      				var target = Ext.ComponentMgr.get(this.targetField);
															    				var s = '';
																			    
																      		    var reliableRecord = result.terms[thes.lang];
																      		    
																      		    // S'assurer que le mot-cl� n'est pas d�j� s�lectionn�
																      		    if (!target.usedRecords.containsKey(reliableRecord))
																				{
																					// Sauvegarde dans le champs SuperBoxSelect des mots-cl�s dans toutes les langues de EasySDI
																				    for(l in result.terms) 
																				    {
																				    	s += l+': '+result.terms[l]+';';
																				    }
																				    target.addItem({keyword:result.terms[this.lang], value: s});
																				}
																				else
																				{
																					Ext.MessageBox.alert('".JText::_('CATALOG_EDITMETADATA_THESAURUSSELECT_MSG_SUCCESS_TITLE')."', 
															                    						 '".JText::_('CATALOG_EDITMETADATA_THESAURUSSELECT_MSG_SUCCESS_TEXT')."');
																				
																				}
																			}
															  });
								
								 							  
								".$parentFieldsetName.".add(
									new Ext.Button({
										id:'".$currentName."_button',
										text:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_BUTTON'))."',
										handler: function()
								                {
								                	// Cr�er une iframe pour demander � l'utilisateur le type d'import
													if (!winthge)
														winthge = new Ext.Window({
														                id:'".$currentName."_win',
																  		itemId:'".$currentName."_win',
																  		title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_ALERT'))."',
														                width:500,
														                height:500,
														                closeAction:'hide',
														                layout:'fit', 
																	    border:true, 
																	    closable:true, 
																	    renderTo:Ext.getBody(), 
																	    frame:true,
																	    listeners: {
																			'show': function (animateTarget, cb, scope)
																					{
																						this.items.get(0).emptyAll();
																						this.items.get(0).getTopConcepts(this.items.get(0).CONCEPT);
																					}
																			},
																	    items:[thes]
														            });
														else
														{
															// Vider les champs du composant Thesaurus
														}	
														
														winthge.show();
										        	},
										
								        // Champs sp�cifiques au clonage
								        dynamic:true,
								        minOccurs:1,
							            maxOccurs:1,
							            clone: false,
										clones_count: 1,
							            extendedTemplate: null
									})
								);
								
								// Cr�er le champ qui contiendra les mots-cl�s du thesaurus choisis
								".$parentFieldsetName.".add(createSuperBoxSelect('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."', '', false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."'));
								";
								
								break;
						default:
							// Selon le rendu de l'attribut, on fait des traitements diff�rents
							switch ($child->rendertype_id)
							{
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
							}
							if ($child->attribute_system)
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
					}
				}
			}
		// R�cup�ration des relations de cette classe (parent) vers d'autres classes (enfants)
		//$database->setQuery( $query );
		//$rowClassChilds = array_merge( $rowClassChilds, $database->loadObjectList() );
		//echo "<br>";
		//print_r($rowClassChilds);
		//echo "<br>";
		// Parcours des relations enfant
		//foreach($rowClassChilds as $child)
		// Parcours des classes
		else if ($child->child_id <> null)
		{
			$label = $label = JText::_($child->rel_guid."_LABEL");
			if ($label == null or substr($label, -6, 6) == "_LABEL")
				$label = $child->rel_name;
				
			// L'aide contextuelle vient de la relation
			// Si la relation n'en a pas, on prend celle de l'attribut
			// Si l'attribut n'en a pas on prend le nom de l'attribut
			$tip = JText::_(strtoupper($child->rel_guid)."_INFORMATION");
			if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
			{
				$tip = JText::_(strtoupper($child->class_guid)."_INFORMATION");
				if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
					$tip = ""; //$child->rel_name;			
			}
			
			//echo $child->child_isocode." - ".$child->rel_guid."_LABEL"."<br>";
			// On regarde dans le XML s'il contient la balise correspondante au code ISO de la relation,
			// et combien de fois au niveau courant
			//echo "Recherche de ".$child->rel_isocode."/".$child->child_isocode. " dans ".$scope->nodeName."<br>";
			$node = $xpathResults->query($child->rel_isocode."/".$child->child_isocode, $scope);
			$relCount = $node->length;
			//echo "Trouve ".$relCount." fois<br>";
			
			//echo "La relation ".$child->rel_isocode. " avec la classe enfant ".$child->child_isocode." existe ".$relCount." fois.<br>";
		/*
			if ($child->reltype_id == 5)
			{
				//$generalizationName = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
				$generalizationName = str_replace(":", "_", $child->rel_isocode)."__1";
				$generalizationName2 = str_replace(":", "_", $child->rel_isocode)."__2";
				$generalizationName = substr($parentName, 0, strrpos($parentName, "-"))."-".str_replace(":", "_", $child->rel_isocode)."__1";
				$generalizationName2 = substr($parentName, 0, strrpos($parentName, "-"))."-".str_replace(":", "_", $child->rel_isocode)."__2";
				//echo strlen($parentName)." - ".strrpos($parentName, "-")." - ".$len."<br>";
				//echo substr($parentName, 0, strrpos($parentName, "-"))."<br>";
				//echo $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1"."<br>";
				//echo substr($parentName, 0, strrpos($parentName, "-"))."-".str_replace(":", "_", $child->rel_isocode)."__1"."<br>";
				$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
				$fieldsetName2 = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					
				//echo $fieldsetName." - ".$fieldsetName2."<br>";
				//echo $parent." - ".$child->child_id."<br>";
				//if ($parent <> $child->child_id)
				//{
					$this->javascript .="
						// Cr�er un nouveau fieldset
						//console.log(ancestor.getId());							
						var ".$fieldsetName." = ".$parentFieldsetName.".duplicates('".$generalizationName."', '".html_Metadata::cleanText($label)."', ".$ancestorFieldsetName."); 
						//var ".$fieldsetName." = createFieldSet('".$generalizationName."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, 0, 1, '".html_Metadata::cleanText(JText::_($tip))."'); 
						//var master = Ext.getCmp('".$generalizationName."');							
						var ".$fieldsetName2." = ".$parentFieldsetName.".duplicates('".$generalizationName2."', '".html_Metadata::cleanText($label)."', ".$ancestorFieldsetName."); 
						//var ".$fieldsetName2." = createFieldSet('".$generalizationName2."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 0, 1, '".html_Metadata::cleanText(JText::_($tip))."'); 
						//".$ancestorFieldsetName.".add(".$fieldsetName.");
						//".$ancestorFieldsetName.".add(".$fieldsetName2.");
						//".$ancestorFieldsetName.".doLayout();
					";	
				//}
				
				//echo "Appel r�cursif avec les valeurs suivantes:<br> ";
				//echo "Ancestor Fieldset = ".$ancestorFieldsetName."<br>";
				//echo "Parent Fieldset = ".$parentFieldsetName."<br>";
				//echo "Fieldset = ".$fieldsetName."<br>";
				//echo "<hr>";
				
				HTML_metadata::buildTree($database, $ancestor, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, substr($parentName, 0, strrpos($parentName, "-"))."-".str_replace(":", "_", $child->child_isocode)."__1", $xpathResults, $relScope, $queryPath, $child->child_isocode, $account_id, $profile_id, $option);
				HTML_metadata::buildTree($database, $ancestor, $child->child_id, $child->child_id, $fieldsetName2, $parentFieldsetName, substr($parentName, 0, strrpos($parentName, "-"))."-".str_replace(":", "_", $child->child_isocode)."__1", $xpathResults, $relScope, $queryPath, $child->child_isocode, $account_id, $profile_id, $option);
				//function buildTree($database, $ancestor, $parent, $parentFieldset, $parentFieldsetName, $parentName, $xpathResults, $scope, $queryPath, $currentIsocode, $account_id, $profile_id, $option)
			}
			else
			{*/
						
			// Traitement de chaque occurence de la relation dans le XML.
			// Si pas trouv�, on entre une fois dans la boucle pour cr�er
			// une seule occurence de saisie (master)
			for ($pos=0; $pos<=$relCount; $pos++)
			{
				/*
				 * COMPREHENSION DU MODELE
				 * C'est la relation qui est multiple. De ce fait on a toujours un et un
				 * seul enfant pour chaque relation trouv�e.
				 */  
				
				// Construction du master qui permet d'ajouter des occurences de la relation.
				// Le master doit contenir une structure mais pas de donn�es.
				if ($pos==0)
				{
					// Traitement de la relation entre la classe parent et la classe enfant
					// S'il y a au moins une occurence de la relation dans le XML, on change le scope
					//echo "relcount: ".$relCount." - ".$node->item($pos)->nodeName."<br>";
					if ($relCount > 0)
						$relScope = $node->item($pos);
					else
						$relScope = $scope;
					
					//echo "relscope: ".$relScope->nodeName."<br>";
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// R�cup�ration du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->child_isocode, $relScope);
						//echo "Trouve ".$childnode->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouv� des occurences, on modifie le scope.
						if ($childnode->length > 0)
							$classScope = $childnode->item(0);
						else
							$classScope = $relScope;	
					}
					else
						$classScope = $relScope;
					
					// Construction du nom du fieldset qui va correspondre � la classe
					// On n'y met pas la relation qui n'a pas d'int�r�t pour l'unicit� du nom
					// On d�marre l'indexation � 1
					$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__".($pos+1);
							
					// Construction du bloc de la classe enfant
					// Nom du fieldset avec guid pour l'unicit�
					$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					
					$this->javascript .="
						// Cr�er un nouveau fieldset
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
					";

					/*
					// S'il y a un xlink:title d�fini, alors afficher une balise pour le saisir
					if ($child->has_xlinkTitle)
					{
						if ($nodeCount > 0)
						$xlinkTitleValue = html_Metadata::cleanText($node->item($pos)->getAttribute('xlink:title'));
						else
						$xlinkTitleValue = "";

						$this->javascript .="
						fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', '".JText::_('EDIT_METADATA_EXTENSION_TITLE')."',true, false, null, '1', '1', '".$xlinkTitleValue."'));";
					}
					*/

					// Le code ISO de la classe enfant devient le code ISO du nouveau parent
					$nextIsocode = $child->child_isocode;
					// Appel r�cursif de la fonction pour le traitement du prochain niveau
					//HTML_metadata::buildTree($prof, $database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $option);
					//echo "Appel r�cursif avec les valeurs suivantes:<br> ";
					//echo "Ancetre = ".$ancestor."<br>";
					//echo "Parent = ".$child->child_id."<br>";
					//echo "Parent Fieldset = ".$child->child_id."<br>";
					//echo "Parent Name = ".$name."<br>";
					//echo "Parent avec un $ = ".$parent."<br>";
					//echo "Scope = ".$scope->nodeName."<br>";
					//echo "ClassScope = ".$classScope->nodeName."<br>";
					//echo "QueryPath = ".$queryPath."<br>";
					//echo "Current Isocode = ".$nextIsocode."<br>";
					//echo "Account Id = ".$account_id."<br>";
					//echo "<hr>";
					
					// Test pour le cas d'une relation qui boucle une classe sur elle-m�me
					if ($ancestor <> $parent)					
						HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
		
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel r�cursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
				// Ici on va traiter toutes les occurences trouv�es dans le XML
				else
				{
					// Traitement de la relation entre la classe parent et la classe enfant
					$relScope = $node->item($pos-1);
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// R�cup�ration du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->child_isocode, $relScope);
						//echo "Trouve ".$node->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouv� des occurences, on modifie le scope.
						if ($childnode->length > 0)
							$classScope = $childnode->item(0);
						else
							$classScope = $relScope;
					}
					else
						$classScope = $relScope;
					
					// Construction du nom du fieldset qui va correspondre � la classe
					// On n'y met pas la relation qui n'a pas d'int�r�t pour l'unicit� du nom
					// On r�cup�re le master qui a l'index 1
					$master = $parentName."-".str_replace(":", "_", $child->child_isocode)."__1";
					// On construit le nom de l'occurence
					$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__".($pos+1);
							
					// Construction du bloc de la classe enfant
					// Nom du fieldset avec guid pour l'unicit�
					$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					
					$this->javascript .="
						var master = Ext.getCmp('".$master."');							
						// Cr�er un nouveau fieldset
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
					";
					
					/*
					// S'il y a un xlink:title d�fini, alors afficher une balise pour le saisir
					if ($child->has_xlinkTitle)
					{
						if ($nodeCount > 0)
						$xlinkTitleValue = html_Metadata::cleanText($node->item($pos)->getAttribute('xlink:title'));
						else
						$xlinkTitleValue = "";

						$this->javascript .="
						fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', '".JText::_('EDIT_METADATA_EXTENSION_TITLE')."',true, false, null, '1', '1', '".$xlinkTitleValue."'));";
					}
					*/

					// Le code ISO de la classe enfant devient le code ISO du nouveau parent
					$nextIsocode = $child->child_isocode;
					// Appel r�cursif de la fonction pour le traitement du prochain niveau
					//HTML_metadata::buildTree($prof, $database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $option);
					
					//echo "Appel r�cursif avec les valeurs suivantes:<br> ";
					//echo "Parent = ".$child->child_id."<br>";
					//echo "Parent Fieldset = ".$child->child_id."<br>";
					//echo "Parent Name = ".$name."<br>";
					//echo "Scope = ".$classScope->nodeName."<br>";
					//echo "QueryPath = ".$queryPath."<br>";
					//echo "Current Isocode = ".$nextIsocode."<br>";
					//echo "Account Id = ".$account_id."<br>";
					//echo "<hr>";
					
					HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
				
				
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel r�cursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
			}

			// Si la classe est obligatoire mais qu'elle n'existe pas � l'heure actuelle dans le XML, 
			// il faut cr�er en plus du master un bloc de saisie qui ne puisse pas �tre supprim� par l'utilisateur 
			if ($relCount==0 and $child->rel_lowerbound>0)
			{
				// Construction du nom du fieldset qui va correspondre � la classe
				// On n'y met pas la relation qui n'a pas d'int�r�t pour l'unicit� du nom
				// On r�cup�re le master qui a l'index 1
				$master = $parentName."-".str_replace(":", "_", $child->child_isocode)."__1";
				// On construit le nom de l'occurence qui a forc�ment l'index 2
				$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__2";

				// Le scope reste le m�me, il n'aura de toute fa�on plus d'utilit� pour les enfants
				// puisqu'� partir de ce niveau plus rien n'existe dans le XML	
				$classScope = $scope;
					
				// Construction du fieldset
				$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
				
				$this->javascript .="
					var master = Ext.getCmp('".$master."');							
					// Cr�er un nouveau fieldset
					var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
					".$parentFieldsetName.".add(".$fieldsetName.");
				";	

				/*	
				// S'il y a un xlink:title d�fini, alors afficher une balise pour le saisir
				if ($child->has_xlinkTitle)
				{
					$xlinkTitleValue = "";

					$this->javascript .="
					fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', '".JText::_('EDIT_METADATA_EXTENSION_TITLE')."',true, false, null, '1', '1', '".$xlinkTitleValue."'));";
				}
				*/	
					
				// Le code ISO de la classe enfant devient le code ISO du nouveau parent
				$nextIsocode = $child->child_isocode;
				
				
				// Appel r�cursif de la fonction pour le traitement du prochain niveau
				if ($ancestor <> $parent)
					HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					
				// Classassociation_id contient une classe
				if ($child->association_id <>0)
				{
					// Appel r�cursif de la fonction pour le traitement du prochain niveau
					if ($ancestor <> $parent)
						HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
				}
			}
		
		}
		else if ($child->objecttype_id <> null)
		{
			$label = $label = JText::_($child->rel_guid."_LABEL");
			if ($label == null or substr($label, -6, 6) == "_LABEL")
				$label = $child->rel_name;
				
			// L'aide contextuelle vient de la relation
			// Si la relation n'en a pas, on prend celle de l'attribut
			// Si l'attribut n'en a pas on prend le nom de l'attribut
			$tip = JText::_(strtoupper($child->rel_guid)."_INFORMATION");
			if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
			{
				$tip = "";			
			}
			
			// On regarde dans le XML s'il contient la balise correspondante au code ISO de la relation,
			// et combien de fois au niveau courant
			$node = $xpathResults->query($child->rel_isocode, $scope);
			$relCount = $node->length;
			
			//echo $relCount." enregistrements trouves pour ".$child->rel_isocode." dans ".$scope->nodeName."<br>";
			// Traitement de chaque occurence de la relation dans le XML.
			// Si pas trouv�, on entre une fois dans la boucle pour cr�er
			// une seule occurence de saisie (master)
			//echo $child->rel_lowerbound. " - ".$child->rel_upperbound."<br>";
			for ($pos=0; $pos<=$relCount; $pos++)
			{
				// Construction du master qui permet d'ajouter des occurences de la relation.
				// Le master doit contenir une structure mais pas de donn�es.
				if ($pos==0)
				{
					/*if ($relCount > 0)
			{
				$guid = substr($node->item($pos)->attributes->getNamedItem('href')->value, -36);
				//echo "Trouve ".$guid."<br>";
				$results = array();
				$database->setQuery( "SELECT o.id as id, m.guid as guid, o.name as name FROM #__sdi_object o, #__sdi_objecttype ot, #__sdi_metadata m where o.metadata_id=m.id AND o.objecttype_id=ot.id AND ot.id=".$child->objecttype_id." AND m.guid ='".$guid."'" );
				$results= array_merge( $results, $database->loadObjectList() );
				//$results = HTML_metadata::array2json(array ("total"=>count($results), "contacts"=>$results));
				$results = HTML_metadata::array2json($results);
				//$results = $results[0]->guid;
				//print_r($results);
			}
			else
			{*/
				$guid = "";
				//echo "Trouve ".$guid."<br>";
				$results = array();
				$results = HTML_metadata::array2json($results);
			//}
						
			// On construit le nom de l'occurence qui a forc�ment l'index 2
					$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
					
					// Traitement de la relation entre la classe parent et la classe enfant
					// S'il y a au moins une occurence de la relation dans le XML, on change le scope
					//echo "relcount: ".$relCount." - ".$node->item($pos)->nodeName."<br>";
					if ($relCount > 0)
						$relScope = $node->item($pos);
					else
						$relScope = $scope;
						
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// R�cup�ration du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->rel_isocode, $relScope);
						//echo "Trouve ".$childnode->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouv� des occurences, on modifie le scope.
						if ($childnode->length > 0)
							$classScope = $childnode->item(0);
						else
							$classScope = $relScope;	
					}
					else
						$classScope = $relScope;
					
					
					// Construction du fieldset
					$fieldsetName = "fieldset".$child->rel_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					//echo $fieldsetName."<br>";
					$searchFieldName = $name."-"."SEARCH__1"; //.($pos+1);
					
					$this->javascript .="
							// Cr�er un nouveau fieldset
							var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
								".$parentFieldsetName.".add(".$fieldsetName.");
							".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
							";
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel r�cursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
				else
				{
					//$guid = substr($node->item($pos-1)->attributes->getNamedItem('href')->value, -36);
					$guid = substr($node->item($pos-1)->attributes->getNamedItem('href')->value, strpos($node->item($pos-1)->attributes->getNamedItem('href')->value, "&id=") + strlen("&id=") , 36);
					
					//echo "Trouve ".$guid."<br>";
					$total = 0;
					$database->setQuery( "SELECT count(*) 
										  FROM 	 #__sdi_object o, 
										  		 #__sdi_objectversion ov 
										  WHERE  o.id=ov.object_id
										  		 AND o.id IN (	SELECT ov.object_id 
																FROM 	 #__sdi_objectversion ov,
																  		 #__sdi_metadata m
																WHERE  ov.metadata_id=m.id 
																  	   AND m.guid ='".$guid."'
										  		 			  )" );
					$total = $database->loadResult();
					
					$results = array();
					if ($total > 1)
					{
						$database->setQuery( "SELECT o.id as id, 
												 m.guid as guid, 
												 CONCAT(o.name, ' [', ov.title, ']') as name 
										  FROM 	 #__sdi_object o, 
										  		 #__sdi_objecttype ot, 
										  		 #__sdi_metadata m,
										  		 #__sdi_objectversion ov 
										  WHERE  o.id=ov.object_id
										  		 AND ov.metadata_id=m.id 
										  		 AND o.objecttype_id=ot.id 
										  		 AND ot.id=".$child->objecttype_id." 
										  		 AND m.guid ='".$guid."'" );
					}
					else
					{
						$database->setQuery( "SELECT o.id as id, 
												 m.guid as guid, 
												 o.name as name 
										  FROM 	 #__sdi_object o, 
										  		 #__sdi_objecttype ot, 
										  		 #__sdi_metadata m,
										  		 #__sdi_objectversion ov 
										  WHERE  o.id=ov.object_id
										  		 AND ov.metadata_id=m.id 
										  		 AND o.objecttype_id=ot.id 
										  		 AND ot.id=".$child->objecttype_id." 
										  		 AND m.guid ='".$guid."'" );
					}
					$results= array_merge( $results, $database->loadObjectList() );
					//$results = HTML_metadata::array2json(array ("total"=>count($results), "contacts"=>$results));
					$results = HTML_metadata::array2json($results);
					//$results = $results[0]->guid;
					//print_r($results);
						
			
					// Construction du nom du fieldset qui va correspondre � la classe
					// On n'y met pas la relation qui n'a pas d'int�r�t pour l'unicit� du nom
					// On r�cup�re le master qui a l'index 1
					$master = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
					// On construit le nom de l'occurence qui a forc�ment l'index 2
					$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__".($pos+1);
				
					// Traitement de la relation entre la classe parent et la classe enfant
					$relScope = $node->item($pos-1);
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// R�cup�ration du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->rel_isocode, $relScope);
						//echo "Trouve ".$childnode->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouv� des occurences, on modifie le scope.
						if ($childnode->length > 0)
							$classScope = $childnode->item(0);
						else
							$classScope = $relScope;	
					}
					else
						$classScope = $relScope;
							
					
						
					// Construction du fieldset
					$fieldsetName = "fieldset".$child->rel_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					$searchFieldName = $name."-"."SEARCH__1"; //.($pos+1);
					
					$this->javascript .="
						// Cr�er un nouveau fieldset
						var master = Ext.getCmp('".$master."');							
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
							".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel r�cursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
			}
			
			
			
			// Si l'objet est obligatoire mais qu'il n'existe pas � l'heure actuelle dans le XML, 
			// il faut cr�er en plus du master un bloc de saisie qui ne puisse pas �tre supprim� par l'utilisateur 
			if ($relCount==0 and $child->rel_lowerbound>0)
			{
				$guid = "";
				//echo "Trouve ".$guid."<br>";
				$results = array();
				$results = HTML_metadata::array2json($results);
			
				// Construction du nom du fieldset qui va correspondre � la classe
				// On n'y met pas la relation qui n'a pas d'int�r�t pour l'unicit� du nom
				// On r�cup�re le master qui a l'index 1
				$master = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
				// On construit le nom de l'occurence qui a forc�ment l'index 2
				$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__2";
			
				// Le scope reste le m�me, il n'aura de toute fa�on plus d'utilit� pour les enfants
				// puisqu'� partir de ce niveau plus rien n'existe dans le XML	
				$classScope = $scope;
					
				// Construction du fieldset
				$fieldsetName = "fieldset".$child->rel_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
				$searchFieldName = $name."-"."SEARCH__1"; //2";
				
				$this->javascript .="
					// Cr�er un nouveau fieldset
					var master = Ext.getCmp('".$master."');							
					var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
					".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', '', false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
					";
				
				// Classassociation_id contient une classe
				$nextIsocode = $child->rel_isocode;
				if ($child->association_id <>0)
				{
					// Appel r�cursif de la fonction pour le traitement du prochain niveau
					if ($ancestor <> $parent)
						HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
				}
			}
		}
			//}
		}
	}
	
	/*
	 * RenderType = TextBox
	 */
	function createTextBox()
	{
		
	}
	
	/*
	 * RenderType = TextArea
	 */
	function createTextArea()
	{
		
	}
	
	/*
	 * RenderType = RadioButton
	 */
	function createRadioButton()
	{
		
	}
	
	/*
	 * RenderType = CheckBox
	 */
	function createCheckBox()
	{
		
	}
	
	/*
	 * RenderType = List
	 */
	function createList()
	{
		
	}
	
	function cleanText($text)
	{
		//echo " ------- encoding: ".mb_detect_encoding($text)."<br>";
		//echo " ------- ".$text."<br>";
		/*if (mb_detect_encoding($text) == "UTF-8")
			$text = utf8_encode($text);
		//echo " ------- ".$text."<br>";
		$text = utf8_decode($text);
		//echo " ------- ".$text."<br>";
		$text = str_replace("\n","\\n",$text);
		//echo " ------- ".$text."<br>";
		$text = str_replace("\r","\\r",$text);
		//echo " ------- ".$text."<br>";
		$text = str_replace("\t","\\t",$text);
		//echo " ------- ".$text."<br>";
		$text = str_replace("'","\'",$text);
		//echo " ------- ".$text."<br>";
		//$text = str_replace("","\",$text);
		//echo " ------- ".$text."<br>";
		if (ord(substr($text, -1)) == 92)
			$text = $text.chr(92);
		if (mb_detect_encoding($text) <> "UTF-8")
			$text = utf8_encode($text);
		//echo " ------- ".$text."<br>";
		
		
		if (mb_detect_encoding($text) == "UTF-8")
			$text = utf8_encode($text);
		*/
		
		//$text = html_entity_decode($text, ENT_QUOTES, "UTF-8");
		
		$text = addslashes($text);
		$text = str_replace(chr(13),"\\r",$text);
		$text = str_replace(chr(10),"\\n",$text);
		//echo $text."<br>";
		return $text;
	}

function array2extjs($arr, $simple, $multi = false, $textlist = false) {
		
		$extjsArray = "";

		if ($simple)
		{
			// Entr�e vide
			$extjsArray .= "['', ''], ";
		}
		$id=0;
		foreach($arr as $key=>$value)
		{
			$extjsArray .= "[";
			//$extjsArray .= "'".$id."', ";
			if ($textlist) // Mettre le titre � vide si on est dans une liste de type texte
			{
				if (substr($key, -6, 6) == "_TITLE")
					$extjsArray .= "'', ";
				else
					$extjsArray .= "'".$key."', ";
			}
			else
				$extjsArray .= "'".$key."', ";
				
			if ($multi)
			{
				foreach ($value as $val)
					$extjsArray .= "'".$val."',";
			}
			else
				$extjsArray .= "'".$value."'";
			$extjsArray .= "], ";
			$id ++;
		}
		$extjsArray = '[' . $extjsArray. ']';
		return str_replace("], ]", "]]", $extjsArray);//Return ExtJS array
	}
	
	function array2checkbox($id, $radio, $arr, $selected, $tip) {
		$checkboxArray = "";
		$pos = 0;
		
		foreach($arr as $key=>$value)
		{
			$checkboxArray .= "{";
			if ($radio)
			{
				$checkboxArray .= "name: '".$id."', ";
				$checkboxArray .= "xtype: 'radio', ";
			}
			else
			{
				$checkboxArray .= "name: '".$id."_".$pos."', ";
				$checkboxArray .= "xtype: 'checkbox', ";
			}
			//$checkboxArray .= "id: '".$id."_".$pos."', ";
			$checkboxArray .= "boxLabel: '".$key."', ";
			$checkboxArray .= "inputValue: '".$value."', ";
			$checkboxArray .= "qTip: '".$tip."'";
			if (in_array($value, $selected))
				$checkboxArray .= ", checked: true";
			$checkboxArray .= "}, ";
			$pos++;
		}
		$checkboxArray = '[' . $checkboxArray. ']';
		return str_replace("}, ]", "}]", $checkboxArray);//Return ExtJS array
	}
	
	function array2json($arr)
	{
		if(function_exists('json_encode'))
			return json_encode($arr); //Lastest versions of PHP already has this functionality >=5.2.0.
	    
		$parts = array();
		$is_list = false;

		//Find out if the given array is a numerical array
		$keys = array_keys($arr);
		$max_length = count($arr)-1;
		if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
			$is_list = true;
			for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
				if($i != $keys[$i]) { //A key fails at position check.
					$is_list = false; //It is an associative array.
					break;
				}
			}
		}

		foreach($arr as $key=>$value)
		{
			if(is_array($value)) { //Custom handling for arrays
				if($is_list)
				$parts[] = HTML_metadata::array2json($value); /* :RECURSION: */
				else
				$parts[] = '"' . $key . '":' . HTML_metadata::array2json($value); /* :RECURSION: */
			}
			else
			{
				$str = '';
				if(!$is_list)
				$str = '"' . $key . '":';

				//Custom handling for multiple data types
				if(is_numeric($value))
				$str .= $value; //Numbers
				elseif($value === false)
				$str .= 'false'; //The booleans
				elseif($value === true)
				$str .= 'true';
				else
				$str .= '"' . addslashes($value) . '"'; //All other things
				// :TODO: Is there any more datatype we should be in the lookout for? (Object?)

				$parts[] = $str;
			}
		}
		$json = implode(',',$parts);

		if($is_list)
			return '[' . $json . ']';//Return numerical JSON

		$return = '[' . $json . ']';
		return $return;//Return associative JSON
	}
	
	function historyAssignMetadata($rows, $pageNav, $metadata_id, $option)
	{
		$database =& JFactory::getDBO(); 
		
		$app	= &JFactory::getApplication();
		$router = &$app->getRouter();
		$router->setVars($_REQUEST);
		
		?>	
		
		<div id="page">
		<h1 class="contentheading"><?php echo JText::_("CATALOG_HISTORYASSIGN_METADATA"); ?></h1>
		<div class="contentin">
		<form action="index.php" method="POST" id="historyassignForm" name="historyassignForm">
		<div class="row">
			<input type="submit" id="back_button" name="back_button" class="submit" value ="<?php echo JText::_("CORE_CANCEL"); ?>" onClick="document.getElementById('historyassignForm').task.value='listMetadata';document.getElementById('historyassignForm').submit();"/>
		</div>
	<table id="myHistoryAssign" class="box-table">
	<thead>
	<tr>
	<th class="title"><?php echo JText::_('CATALOG_HISTORYASSIGN_ASSIGNEDBY'); ?></th>
	<th class="title"><?php echo JText::_('CATALOG_HISTORYASSIGN_ASSIGNEDTO'); ?></th>
	<th class="title"><?php echo JText::_('CATALOG_HISTORYASSIGN_DATE'); ?></th>
	<th class="title"><?php echo JText::_('CATALOG_HISTORYASSIGN_INFORMATION'); ?></th>
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
			<td ><?php echo $row->assignedby; ?></td>
			<td ><?php echo $row->assignedto; ?></td>
			<td ><?php echo $row->date; ?></td>
			<td ><?php echo $row->information; ?></td>
			</tr>
			<?php		
		}
	?>
	</tbody>
	</table>
	<?php echo $pageNav->getPagesCounter(); ?>&nbsp;<?php echo $pageNav->getPagesLinks(); ?>
	
	<input type="hidden" name="option" value="<?php echo $option; ?>">
	<input type="hidden" name="metadata_id" value="<?php echo $metadata_id; ?>">
	<input type="hidden" id="task" name="task" value="historyAssignMetadata">
	<input type="hidden" id="Itemid" name="Itemid" value="<?php echo JRequest::getVar('Itemid'); ?>">
	<input type="hidden" id="lang" name="lang" value="<?php echo JRequest::getVar('lang'); ?>">
	</form>
	</div>
	</div>
	<?php
	}
	
	function buildTBar($isManager, $isEditor, $isPublished, $isValidated, $object_id, $metadata_id, $account_id, $metadata_collapse, $option)
	{
		$database =& JFactory::getDBO();
		
		$tbar = array();
		
		$collapse_title=($metadata_collapse == 'true')? JText::_('CORE_OPENALL') : JText::_('CORE_CLOSEALL');
		
		// Tout ouvrir ou tout fermer
		$tbar[] = "{
						text: '".$collapse_title."',
				        listeners: {        
						 				click: {            
						 							fn:function() {
						 								if (this.allCollapsed)
											        		{
											        			this.setText('".JText::_('CORE_CLOSEALL')."');
											        			
											                	myMask.show();
											                	form.cascade(function(cmp)
											        			{
												        			if (cmp.xtype=='fieldset')
											         				{
											         					//console.log('Fieldset: ' + cmp.getId() + ' - ' + cmp.clone);
																		if (cmp.clone == true)
																		{
											         						//console.log(cmp);
																			if (cmp.collapsible == true && cmp.rendered == true)
																				cmp.expand(true);
											         					}
											         				}
											        			});
											                 	form.doLayout();
											                 	myMask.hide();
												        	
													        	this.allCollapsed = false;
											        		}
											        		else
											        		{
											        			this.setText('".JText::_('CORE_OPENALL')."');
											        			
											                	myMask.show();
											                 	form.cascade(function(cmp)
											        			{
												        			if (cmp.xtype=='fieldset')
											         				{
											         					//console.log('Fieldset: ' + cmp.getId() + ' - ' + cmp.clone);
																		if (cmp.clone == true)
																		{
											         						//console.log(cmp);
																			if (cmp.collapsible == true && cmp.rendered == true)
																				cmp.collapse(true);
											         					}
											         				}
											        			});
											                 	form.doLayout();
											              		myMask.hide();
												        		
												        		this.allCollapsed= true;
											        		}																				}        
												}	
									},
				        allCollapsed: ".$metadata_collapse."
		        }";
		
		if ($isManager)
		{
			// Boutons d'import
			if(!$isPublished)
			{
				$importxml_url = 'index.php?option='.$option.'&task=importXMLMetadata';
				$importcsw_url = 'index.php?option='.$option.'&task=importCSWMetadata';
				
				$importrefs = array();
				$database->setQuery( "SELECT * FROM #__sdi_importref ORDER BY ordering" );
				$importrefs= array_merge( $importrefs, $database->loadObjectList() );
				
				$menu = "[";
				foreach($importrefs as $key => $importref)
				{
					$menu .= "{ xtype: 'menuitem', 
								text: '".JText::_($importref->guid."_LABEL")."', ";
					
					if ($importref->url == "") // Import depuis un fichier xml
					{
				       $menu .= "handler: function()
					                {
					                	// Créer une iframe pour demander à l'utilisateur le type d'import
										if (!winxml)
											winxml = new Ext.Window({
											                title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_IMPORT_XMLFILE_ALERT'))."',
											                width:600,
											                height:130,
											                closeAction:'hide',
											                layout:'fit', 
														    border:true, 
														    closable:true, 
														    renderTo:Ext.getBody(), 
														    frame:true,
														    items:[{ 
															     xtype:'form' 
															     ,id:'importxmlform' 
															     ,defaultType:'textfield' 
															     ,frame:true 
															     ,method:'post' 
															     ,enctype: 'multipart/form-data'
															     ,fileUpload: true
																 ,url:'".$importxml_url."'
																 ,standardSubmit: true
															     ,defaults:{anchor:'95%'} 
															     ,items:[ 
															       { 
															       	 xtype: 'fileuploadfield',
														             id: 'xmlfile',
														             name: 'xmlfile',
														             fieldLabel: '".html_Metadata::cleanText(JText::_('CORE_METADATA_IMPORT_ALERT_UPLOAD_XMLFILE_LABEL'))."'
															       },
															       { 
															         id:'metadata_id', 
															         xtype: 'hidden',
															         value:'".$metadata_id."' 
															       },
															       { 
															         id:'object_id', 
															         xtype: 'hidden',
															         value:'".$object_id."' 
															       },
															       { 
															         id:'xslfile', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->xslfile)."' 
															       },
															       { 
															         id:'pretreatmentxslfile', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->pretreatmentxslfile)."' 
															       },
															       { 
															         id:'importtype_id', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->importtype_id)."' 
															       },
															       { 
															         id:'task', 
															         xtype: 'hidden',
															         value:'importXMLMetadata' 
															       },
															       { 
															         id:'option', 
															         xtype: 'hidden',
															         value:'".$option."' 
															       },
															       { 
															         id:'Itemid', 
															         xtype: 'hidden',
															         value:'".JRequest::getVar('Itemid')."' 
															       },
															       { 
															         id:'lang', 
															         xtype: 'hidden',
															         value:'".JRequest::getVar('lang')."' 
															       }			
															    ] 
															     ,buttonAlign:'right' 
															     ,buttons: [{ 
													                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
													                    handler: function(){
													                    	Ext.MessageBox.show({
												                    						title: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_IMPORTXML_MSG_CONFIRM_TITLE'))."', 
												                    						msg: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_IMPORTXML_MSG_CONFIRM_TEXT'))."',
												                    						buttons: Ext.MessageBox.OKCANCEL,
												                    						icon: Ext.MessageBox.QUESTION, 
												                    						fn: function (btn, text){
										                    									  	if (btn == 'ok')
										                    									  	{
										                    									  		myMask.show();
										                    											winxml.items.get(0).getForm().submit();
										                    									  	}
										                    									  } 
												                    						});        	
													                    }
													                },
													                {
													                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
													                    handler: function(){
													                        winxml.hide();
													                    }
													                }]
															   }] 
											                
											            });
											else
											{
												winxml.items.get(0).findById('xmlfile').setValue('');
											}	
					  						winxml.show();
							        	}
							";
					}
					else // Import depuis un serveur CSW
					{
						$menu .= "handler: function()
					                {
					                	// Créer une iframe pour demander à l'utilisateur le type d'import
										if (!wincsw)
											wincsw = new Ext.Window({
											                title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_IMPORT_CSW_ALERT'))."',
											                width:500,
											                height:130,
											                closeAction:'hide',
											                layout:'fit', 
														    border:true, 
														    closable:true, 
														    renderTo:Ext.getBody(), 
														    frame:true,
														    items:[{ 
															     xtype:'form' 
															     ,id:'importcswform' 
															     ,frame:true 
															     ,method:'POST' 
															     ,url:'".$importcsw_url."'
																 ,standardSubmit: true
															     ,defaults:{anchor:'95%'} 
															     ,items:[ 
															       { 
															       	 xtype: 'textfield',
														             id: 'id',
														             fieldLabel: '".html_Metadata::cleanText(JText::_('CORE_METADATA_IMPORT_ALERT_UPLOAD_METADATAID_LABEL'))."'
															       },
															       { 
															         id:'metadata_id', 
															         xtype: 'hidden',
															         value:'".$metadata_id."' 
															       },
															       { 
															         id:'object_id', 
															         xtype: 'hidden',
															         value:'".$object_id."' 
															       },
															       { 
															         id:'xslfile', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->xslfile)."' 
															       },
															       { 
															         id:'pretreatmentxslfile', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->pretreatmentxslfile)."' 
															       },
															       { 
															         id:'importtype_id', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->importtype_id)."' 
															       },
															       { 
															         id:'url', 
															         xtype: 'hidden',
															         value:'".html_Metadata::cleanText($importref->url)."' 
															       },
															       { 
															         id:'task', 
															         xtype: 'hidden',
															         value:'importCSWMetadata' 
															       },
															       { 
															         id:'option', 
															         xtype: 'hidden',
															         value:'".$option."' 
															       },
															       { 
															         id:'Itemid', 
															         xtype: 'hidden',
															         value:'".JRequest::getVar('Itemid')."' 
															       },
															       { 
															         id:'lang', 
															         xtype: 'hidden',
															         value:'".JRequest::getVar('lang')."' 
															       }
															    ] 
															     ,buttonAlign:'right' 
															     ,buttons: [{
													                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
													                    handler: function(){
													                    	Ext.MessageBox.show({
												                    						title: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_IMPORTCSW_MSG_CONFIRM_TITLE'))."', 
												                    						msg: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_IMPORTCSW_MSG_CONFIRM_TEXT'))."',
												                    						buttons: Ext.MessageBox.OKCANCEL,
												                    						icon: Ext.MessageBox.QUESTION, 
												                    						fn: function (btn, text){
										                    									  	if (btn == 'ok')
										                    									  	{
										                    									  		myMask.show();
										                    											wincsw.items.get(0).getForm().submit();
										                    									  	}
										                    									  } 
												                    						}); 
													                    }},
													                {
													                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
													                    handler: function(){
													                        wincsw.hide();
													                    }
													                }]
															   }] 
											                
											            });
											else
											{
												wincsw.items.get(0).findById('id').setValue('');
											}	
					  						wincsw.show();
							        	}
							";
					}
					
	
					if ($key <> count($importrefs)-1)
						$menu .= "}, ";
					else
						$menu .= "}";
					
				}
				$menu .= "]";
				
				$tbar[] = "
					{
			            text: '".JText::_('CATALOG_IMPORT')."',
						menu: {
				                xtype: 'menu',
				                id: 'importMenu',
				                plain: true,
				                items: ".$menu."
			    	        }
		            }
				";
			}
			// Boutons de r�plication
			if (!$isPublished)
			{
				$replicate_url = 'index.php?option='.$option.'&task=replicateMetadata';
			
				// R�plication de m�tadonn�e
				$objecttypes = array();
				$listObjecttypes = array();
				$database->setQuery( "SELECT id as value, name as text FROM #__sdi_objecttype WHERE predefined=0 ORDER BY name" );
				$objecttypes= array_merge( $objecttypes, $database->loadObjectList() );
				foreach($objecttypes as $ot)
				{
					$listObjecttypes[$ot->value] = $ot->text;
				}
				$listObjecttypes = HTML_metadata::array2extjs($listObjecttypes, true);
				
				$this->javascript .="
				var replicateDataStore = new Ext.data.Store({
								id: 'objectListStore',
						        proxy: new Ext.data.HttpProxy({
						            url: 'index.php?option=com_easysdi_catalog&task=getObjectVersion'
						        }),
						        reader: new Ext.data.JsonReader({
						            root: 'objects',
						            totalProperty: 'total',
						            remoteSort: true,
						            id: 'version_id'
						        }, [
						            {name: 'version_id', mapping: 'version_id'},
					            	{name: 'object_name', mapping: 'object_name'},
						            {name: 'version_title', mapping: 'version_title'},
					            	{name: 'metadata_guid', mapping: 'metadata_guid'}
						        ]),
						        // turn on remote sorting
						        remoteSort: true,
						        totalProperty: 'total',
						        baseParams: {dir:'ASC', sort:'object_name', start:0, limit:10, objecttype_id:''},
					       	    listeners: {
								            beforeload: {
								            				fn:function(store, options) {
								            					options.params.objecttype_id = Ext.getCmp('objecttype_id').getValue();
																return true;		
												            }
												         }
										}
						    });";
				$tbar[] ="
					{
			            text: '".JText::_('CATALOG_REPLICATE')."',
						handler: function()
		                {
		                	// Cr�er une iframe pour demander à l'utilisateur le type d'import
							if (!winrct)
								winrct = new Ext.Window({
								                title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_ALERT'))."',
								                width:600,
								                height:430,
								                closeAction:'hide',
								                layout:'fit', 
											    border:true, 
											    closable:true, 
											    renderTo:Ext.getBody(), 
											    frame:true,
											    items:[{ 
												     xtype:'form' 
												     ,id:'replicateform' 
												     ,defaultType:'textfield' 
												     ,frame:true 
												     ,method:'post' 
												     ,url:'".$replicate_url."'
													 ,standardSubmit: true
												     //,defaults:{anchor:'95%'} 
												     ,items:[ 
												       { 
												       	 typeAhead:true,
												       	 triggerAction:'all',
												       	 mode:'local',
												         fieldLabel:'".addslashes(JText::_('CATALOG_METADATA_REPLICATE_ALERT_OBJECTTYPE_LABEL'))."', 
												         id:'objecttype_id', 
												         hiddenName:'objecttypeid_hidden', 
												         xtype: 'combo',
												         editable: false,
												         store: new Ext.data.ArrayStore({
															        id: 0,
															        fields: [
															            'value',
															            'text'
															        ],
															        data: ".$listObjecttypes."
															    }),
														 valueField:'value',
														 displayField:'text',
														 listeners: {        
														 				select: {            
														 							fn:function(combo, value) {
														 								var modelDest = Ext.getCmp('objectselector');                
														 								modelDest.store.removeAll();                
														 								//reload region store and enable region                 
														 								modelDest.store.reload({                    
														 								params: { 
														 									objecttype_id: combo.getValue() 
																							}                
																						});																						}        
																				}	
																	}
												       },
												       {
												       	 id:'objectselector',
												       	 itemId:'objectselector',
												       	 xtype:'grid',
												       	 autoExpandColumn:'object_name',
												       	 autoHeight: true,
												       	 loadMask: true,
												       	 frame:true,
												       	 cm: new Ext.grid.ColumnModel([
														        {id:'version_id', header: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_GRID_VERSIONID_COLUMN'))."', hidden: true, dataIndex: 'version_id'},
													        	{id:'object_name', header: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_GRID_OBJECTNAME_COLUMN'))."', sortable: true, editable:false, dataIndex: 'object_name', menuDisabled: true},
														        {id:'version_title', header: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_GRID_VERSIONTITLE_COLUMN'))."', sortable: true, editable:false, dataIndex: 'version_title', menuDisabled: true},
													        	{header: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_GRID_METADATAGUID_COLUMN'))."', width: 150, sortable: true, editable:false, dataIndex: 'metadata_guid', menuDisabled: true}
														    ]),
														 ds: replicateDataStore, 
														 viewConfig: {
														 	forceFit: true,
														 	scrollOffset:0
														 },
														 selModel: new Ext.grid.RowSelectionModel({singleSelect:true}),														 
														 bbar: new Ext.PagingToolbar({
												            pageSize: 10,
												            store: replicateDataStore
												        })
												       },
												       { 
												         id:'metadata_id', 
												         xtype: 'hidden',
												         value:'".$metadata_id."' 
												       },
												       { 
												         id:'object_id', 
												         xtype: 'hidden',
												         value:'".$object_id."' 
												       },
												       { 
												         id:'metadata_guid', 
												         xtype: 'hidden',
												         value:'' 
												       },
												       { 
												         id:'task', 
												         xtype: 'hidden',
												         value:'replicateMetadata' 
												       },
												       { 
												         id:'option', 
												         xtype: 'hidden',
												         value:'".$option."' 
												       },
												       { 
												         id:'Itemid', 
												         xtype: 'hidden',
												         value:'".JRequest::getVar('Itemid')."' 
												       },
												       { 
												         id:'lang', 
												         xtype: 'hidden',
												         value:'".JRequest::getVar('lang')."' 
												       }
												    ] 
												     ,buttonAlign:'right' 
												     ,buttons: [{ 
										                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_SUBMIT'))."',
										                    handler: function(){
										                    	var grid = winrct.items.get(0).findById('objectselector');
										                    	if (grid.getSelectionModel().hasSelection())
																{
										                    		Ext.MessageBox.show({
										                    						title: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_MSG_CONFIRM_TITLE'))."', 
										                    						msg: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_MSG_CONFIRM_TEXT'))."',
										                    						buttons: Ext.MessageBox.OKCANCEL,
										                    						icon: Ext.MessageBox.QUESTION, 
										                    						fn: function (btn, text){
								                    									  	if (btn == 'ok')
								                    									  	{
								                    									  		myMask.show();
																	                    		selectedObject=grid.getSelectionModel().getSelected();
																	                    		winrct.items.get(0).getForm().setValues({metadata_guid: selectedObject.get('metadata_guid')});
																	                    		winrct.items.get(0).getForm().submit();
								                    									  	}
								                    									  } 
										                    						});
										                    	}
										                    	else
										                    	{
										                    		alert('".html_Metadata::cleanText(JText::_('CATALOG_METADATA_REPLICATE_GRID_EMPTY_ALERT'))."');
										                    	}
										                    }
										                },
										                {
										                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
										                    handler: function(){
										                        winrct.hide();
										                    }
										                }]
												   }] 
								                
								            });
								else
								{
									winrct.items.get(0).findById('objecttype_id').setValue('');
									winrct.items.get(0).findById('objectselector').store.removeAll();
								}
	
								Ext.getCmp('objectselector').store.load();
								
								// Masquer le bouton de rafraîchissement
								Ext.getCmp('objectselector').getBottomToolbar().refresh.hide();
								winrct.show();
				        	}
		            }";
			}
			
			// Bouton de reset
			if (!$isPublished)
			{
				$reset_url = 'index.php?option='.$option.'&task=resetMetadata';
			
				// Ajout de bouton de reset
					$tbar[] ="{text: '".JText::_('CORE_RESET')."',
										handler: function()
						                {
						                	// Cr�er une iframe pour confirmer la r�initialisation
											if (!winrst)
												winrst = new Ext.Window({
												                title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_CONFIRM_RESET_ALERT'))."',
												                width:370,
												                height:100,
												                closeAction:'hide',
												                layout:'fit', 
															    border:true, 
															    closable:true, 
															    renderTo:Ext.getBody(), 
															    frame:true,
															    items:[{ 
																     xtype:'form' 
																     ,id:'resetform' 
																     ,defaultType:'textfield' 
																     ,frame:true 
																     ,method:'post' 
																     ,url:'".$reset_url."'
																	 ,standardSubmit: true
																     ,items:[ 
																       { 
																       	 typeAhead:true,
																       	 triggerAction:'all',
																       	 mode:'local',
																         xtype: 'label',
																         text: '".JText::_('CATALOG_METADATA_CONFIRM_RESET')."'
																       },
																       { 
																         id:'metadata_id', 
																         xtype: 'hidden',
																         value:'".$metadata_id."' 
																       },
																       { 
																         id:'object_id', 
																         xtype: 'hidden',
																         value:'".$object_id."' 
																       },
																       { 
																         id:'cid[]', 
																         xtype: 'hidden',
																         value:'".$object_id."' 
																       },
																       { 
																         id:'task', 
																         xtype: 'hidden',
																         value:'resetMetadata' 
																       },
																       { 
																         id:'option', 
																         xtype: 'hidden',
																         value:'".$option."' 
																       },
																       { 
																         id:'Itemid', 
																         xtype: 'hidden',
																         value:'".JRequest::getVar('Itemid')."' 
																       },
																       { 
																         id:'lang', 
																         xtype: 'hidden',
																         value:'".JRequest::getVar('lang')."' 
																       }
																    ] 
																     ,buttonAlign:'center' 
																     ,buttons: [{ 
														                    text:'".html_Metadata::cleanText(JText::_('CORE_ALERT_CONFIRM'))."',
														                    handler: function(){
														                    	myMask.show();
														                    	winrst.items.get(0).getForm().submit();
														                    }
														                },
														                {
														                    text: '".html_Metadata::cleanText(JText::_('CORE_ALERT_CANCEL'))."',
														                    handler: function(){
														                        winrst.hide();
														                    }
														                }]
																   }] 
												                
												            });
						  						winrst.show();
										}
							}";
			}
		}
		
		// Ajout de bouton de retour
		$tbar[] ="{text: '".JText::_('CORE_CANCEL')."',
					handler: function()
	                {
	                	window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."','_self');
		        	}}";
				
		return implode(', ', $tbar);
	} 
}
?>
