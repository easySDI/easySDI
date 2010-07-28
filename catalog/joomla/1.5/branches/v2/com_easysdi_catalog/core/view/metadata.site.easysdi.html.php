<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
					
JHTML::script('ext-base-debug.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
JHTML::script('ext-all-debug.js', 'administrator/components/com_easysdi_catalog/ext/');
JHTML::script('dynamic.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedField.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedFieldSet.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedFormPanel.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('ExtendedHidden.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('MultiSelect.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('SearchField.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('FileUploadField.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('shCore.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('shBrushXml.js', 'administrator/components/com_easysdi_catalog/js/');

class HTML_metadata {
	var $javascript = "";
	var $langList = array ();
	var $mandatoryMsg = "";
	var $regexMsg = "";
	var $boundaries = array();
	var $paths = array();
	var $boundaries_name = array();
	var $catalogBoundaryIsocode = "";
	
	function listMetadata($pageNav, $rows, $option, $rootAccount, $search)
	{
		$database =& JFactory::getDBO(); 
		$user = JFactory::getUser();
		
		?>
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("CATALOG_EDIT_METADATA"); ?></h2>
		<div class="contentin">
		<h3> <?php echo JText::_("CORE_SEARCH_CRITERIA_TITLE"); ?></h3>
		
		<form action="index.php" method="GET" id="productListForm" name="productListForm">
		<table width="100%">
			<tr>
				<td align="left">
					<b><?php echo JText::_("CORE_SHOP_FILTER_TITLE");?></b>&nbsp;
				</td>
				<td align="left">
					<input type="text" name="search" value="<?php echo $search;?>" class="inputboxSearchProduct" " />
				</td>
				<td align="right">
					<button type="submit" class="searchButton" onClick="document.getElementById('task').value='listMetadata';document.getElementById('productListForm').submit();"> <?php echo JText::_("CORE_SEARCH_BUTTON"); ?></button>
				</td>
			</tr>
		</table>
		<br>	
		<!-- pageNav header -->
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("CORE_SHOP_DISPLAY"); ?>
				<?php echo $pageNav->getLimitBox(); ?>
				</td>
				<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<h3><?php echo JText::_("CORE_SEARCH_RESULTS_TITLE"); ?></h3>
	<?php
	if(count($rows) == 0){
		echo "<table><tbody><tr><td colspan=\"11\">".JText::_("CORE_NO_RESULT_FOUND")."</td>";
	}else{?>
	<table class="box-table" id="MyProducts">
	<thead>
	<tr>
	<th><?php echo JText::_('CATALOG_METADATA_OBJECTNAME'); ?></th>
	<th><?php echo JText::_('CATALOG_METADATA_VERSIONTITLE'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_STATE'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_MANAGERS'); ?></th>
	<th><?php echo JText::_('CORE_METADATA_EDITORS'); ?></th>
	<!-- <th><?php //echo JText::_('CORE_CREATED'); ?></th> -->
	<!-- <th><?php //echo JText::_('CORE_UPDATED'); ?></th> -->
	<th></th>
	<th></th>
	<th></th>
	</tr>
	</thead>
	<?php } ?>
	<tbody>
	<?php
		$i=0;
		$param = array('size'=>array('x'=>800,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
		foreach ($rows as $row)
		{	$i++;
			
			?>		
			<tr>
				<td >
					<?php echo $row->name;  ?>
				</td>
				<td >
					<a class="modal" title="<?php echo JText::_("CATALOG_VIEW_MD"); ?>" href="./index.php?tmpl=component&option=com_easysdi_catalog&toolbar=1&task=showMetadata&id=<?php echo $row->metadata_guid;  ?>" rel="{handler:'iframe',size:{x:650,y:600}}"> <?php echo $row->version_title ;?></a>
				</td>
				<td ><?php echo JText::_($row->state); ?></td>
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
				<!-- <td ><?php //echo date('d.m.Y h:i:s',strtotime($row->version_created)); ?></td> -->
				<!-- <td ><?php //if ($row->updated and $row->updated<> '0000-00-00 00:00:00') {echo date('d.m.Y h:i:s',strtotime($row->updated));} ?></td> -->
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					?>
					<td></td>
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
					if ($isManager) // Le rôle de gestionnaire prime sur celui d'éditeur, au cas où l'utilisateur a les deux
					{
						if ($rowMetadata->metadatastate_id == 4 // En travail
							or $rowMetadata->metadatastate_id == 3 // Validé
							or ($rowMetadata->metadatastate_id == 1 and $rowMetadata->published <= date('Y-m-d H:i:s') )// Publié et date du jour >= date de publication
							)
						{
							?>
								<td class="logo" align="center"><div title="<?php echo JText::_('CATALOG_EDIT_METADATA'); ?>" id="editMetadata" onClick="document.getElementById('task').value='editMetadata';document.getElementById('cid[]').value=<?php echo $row->version_id?>;document.getElementById('productListForm').submit();"></div></td>
							<?php
						}
						else
						{
							?>
								<td></td>
							<?php 
						}
					}
					else if ($isEditor)
					{
						// L'utilisateur courant, si c'est un éditeur, doit être éditeur de la métadonnée
						$rowCurrentUser = new accountByUserId($database);
						$rowCurrentUser->load($user->get('id'));
			
						if ($rowMetadata->metadatastate_id == 4 and $rowMetadata->editor_id == $rowCurrentUser->id) // En travail et tâche d'édition assignée
						{
							?>
							<td class="logo" align="center"><div title="<?php echo JText::_('CATALOG_EDIT_METADATA'); ?>" id="editMetadata" onClick="document.getElementById('task').value='editMetadata';document.getElementById('cid[]').value=<?php echo $row->version_id?>;document.getElementById('productListForm').submit();"></div></td>
							<?php
						} 
						else
						{
							?>
								<td></td>
							<?php 
						}
					}
				}
			?>
				<?php 
				if (  JTable::isCheckedOut($user->get ('id'), $row->checked_out ) ) 
				{
					?>
					<td></td>
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
					if ($isManager) // Le rôle de gestionnaire prime sur celui d'éditeur, au cas où l'utilisateur a les deux
					{
						if (($rowMetadata->metadatastate_id == 1 and date('Y-m-d H:i:s') >= $rowMetadata->published )// Publié et date du jour >= date de publication
							)
						{
							?>
								<td class="logo" align="center"><div title="<?php echo JText::_('CATALOG_ARCHIVE_METADATA'); ?>" id="archiveMetadata" onClick="document.getElementById('task').value='archiveMetadata';document.getElementById('cid[]').value=<?php echo $rowMetadata->id?>;document.getElementById('productListForm').submit();"></div></td>
							<?php
						}
						else
						{
							?>
								<td></td>
							<?php 
						}
						if ($rowMetadata->metadatastate_id == 3 or $rowMetadata->metadatastate_id == 2 or  ($rowMetadata->metadatastate_id == 1 and date('Y-m-d H:i:s') >= $rowMetadata->published)// Archivé, Validé ou Publié
							)
						{
							?>
								<td class="logo" align="center"><div title="<?php echo JText::_('CATALOG_INVALIDATE_METADATA'); ?>" id="invalidateMetadata" onClick="document.getElementById('task').value='invalidateMetadata';document.getElementById('cid[]').value=<?php echo $rowMetadata->id?>;document.getElementById('productListForm').submit();"></div></td>
							<?php
						}
						else
						{
							?>
								<td></td>
							<?php 
						}
					}
					else
					{
						?>
							<td></td>
						<?php
					}
				}
			?>
				<td class="logo" align="center"><div title="<?php echo JText::_('CATALOG_HISTORYASSIGN_METADATA'); ?>" id="historyAssignMetadata" onClick="document.getElementById('task').value='historyAssignMetadata';document.getElementById('cid[]').value=<?php echo $row->id?>;document.getElementById('productListForm').submit();"></div></td>
				<!-- <td class="logo"><div title="<?php //echo JText::_('CORE_VIEWLINK_OBJECT'); ?>" id="viewObjectLink" onClick="document.getElementById('task').value='viewObjectLink';document.getElementById('backpage').value='metadata';document.getElementById('cid[]').value=<?php //echo $row->id?>;document.getElementById('productListForm').submit();" ></div></td> -->
			</tr>
			
				<?php		
		}
		
	?>
			</tbody>
			</table>
			<br/>
			<table width="100%">
				<tr>																																						
					<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
					<td align="center">&nbsp;</td>
					<td align="right"> <?php echo $pageNav->getPagesLinks(); ?></td>
				</tr>
			</table>
			
			<input type="hidden" id="cid[]" name="cid[]" value="">
			<input type="hidden" id="id" name="id" value="">
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" id="task" name="task" value="listMetadata">
			<input type="hidden" id="backpage" name="backpage" value="metadata">
			<?php
			if (userManager::hasRight($rootAccount->id,"METADATA")){?>
			
			<!-- <button type="button" onClick="document.getElementById('task').value='editMetadata';document.getElementById('productListForm').submit();"><?php echo JText::_("CATALOG_EDIT_METADATA"); ?></button> -->
			<?php }?>
		</form>
		</div>
		</div>
	<?php
	}
	
	function editMetadata($object_id, $root, $metadata_id, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $option)
	{
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		
		$uri =& JUri::getInstance();
		
		$database =& JFactory::getDBO();
		
		$language =& JFactory::getLanguage();
		
		if (file_exists($uri->base(true).'/components/com_easysdi_catalog/ext/src/locale/ext-lang-'.$language->_lang.'.js')) 
			JHTML::script('ext-lang-'.$language->_lang.'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		else
			JHTML::script('ext-lang-'.substr($language->_lang, 0 ,2).'.js', 'administrator/components/com_easysdi_catalog/ext/src/locale/');
		
		$metadata_collapse = config_easysdi::getValue("metadata_collapse");
		
		$this->mandatoryMsg = html_Metadata::cleanText(JText::_('CATALOG_METADATA_EDIT_MANDATORY_MSG'));
		$this->regexMsg = html_Metadata::cleanText(JText::_('CATALOG_METADATA_EDIT_REGEX_MSG'));
	
		// Récupérer les noms des périmètres
		$this->boundaries[JText::_('CORE_METADATASTATE_LIST')] = array('northbound'=>0, 'southbound'=>0, 'westbound'=>0, 'eastbound'=>0);
		
		foreach($boundaries as $boundary)
		{
			$this->boundaries_name[] = $boundary->name;
			$this->boundaries[$boundary->name] = array('northbound'=>$boundary->northbound, 'southbound'=>$boundary->southbound, 'westbound'=>$boundary->westbound, 'eastbound'=>$boundary->eastbound);
		}
		//print_r($this->boundaries);
		$this->catalogBoundaryIsocode = $catalogBoundaryIsocode;
	
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$this->paths[] = array(	'northbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_north")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'southbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_south")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'westbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_west")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'eastbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_east")."-".str_replace(":", "_", $type_isocode)."__1"));
		
		
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/form_layout_backend.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/MultiSelect.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/fileuploadfield.css');
		
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shCore.css');
		$document->addStyleSheet($uri->base(true) . '/administrator/components/com_easysdi_catalog/templates/css/shThemeDefault.css');
		
		$url = 'index.php?option='.$option.'&task=saveMetadata';
		$preview_url = 'index.php?option='.$option.'&task=previewXMLMetadata';
		$invalidate_url = 'index.php?option='.$option.'&task=invalidateMetadata';
		$validate_url = 'index.php?option='.$option.'&task=validateMetadata';
		$update_url = 'index.php?option='.$option.'&task=updateMetadata';
		$publish_url = 'index.php?option='.$option.'&task=validateForPublishMetadata';
		$assign_url = 'index.php?option='.$option.'&task=assignMetadata';
		
		$user =& JFactory::getUser();
		$user_id = $user->get('id');

		$this->javascript = "";
		
		$database->setQuery( "SELECT a.root_id FROM #__sdi_account a,#__users u where a.root_id is null AND a.user_id = u.id and u.id=".$user_id." ORDER BY u.name" );
		$account_id = $database->loadResult();
		if ($account_id == null)
			$account_id = $user_id;

		// Lister les langues que Joomla va prendre en charge
		//load folder filesystem class
		/*
		jimport('joomla.filesystem.folder');
		$path = JLanguage::getLanguagePath();
		$dirs = JFolder::folders( $path );
		$this->langList = array ();
		$rowid = 0;
		foreach ($dirs as $dir)
		{
			$files = JFolder::files( $path.DS.$dir, '^([-_A-Za-z]*)\.xml$' );
			foreach ($files as $file)
			{
				$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$file);
	
				$row 			= new StdClass();
				$row->id 		= $rowid;
				$row->language 	= substr($file,0,-4);
	
				if (!is_array($data)) {
					continue;
				}
				foreach($data as $key => $value) {
					$row->$key = $value;
				}
	
				$this->langList[] = $row;
				$rowid++;
			}
		}
		*/
		
		// Langues à gérer
		$this->langList = array();
		$database->setQuery( "SELECT l.id, l.name, l.label, l.defaultlang, l.code as code, l.isocode, c.code as code_easysdi FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY l.ordering" );
		$this->langList= array_merge( $this->langList, $database->loadObjectList() );
		
		$fieldsetName = "fieldset".$root[0]->id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
		?>
			<!-- Pour permettre le retour à la liste des produits depuis la toolbar Joomla -->
			<div id="page">
		   <h2 class="contentheading"><?php echo JText::_("CATALOG_EDIT_METADATA") ?></h2>
		   <div id="contentin" class="contentin">
		   <table width="100%">
			<tr>
				<td width="100%"><div id="editMdOutput"></div></td>
			</tr>
		   </table>
		   <form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
		</div>
		</div>
				<?php
				$this->javascript .="
						//var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
						//Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');
						var domNode = Ext.DomQuery.selectNode('div#editMdOutput')
						Ext.DomHelper.insertHtml('afterBegin',domNode,'<div id=formContainer></div>');
				
						// Message d'attente pendant les chargements
						var myMask = new Ext.LoadMask(Ext.getBody(), {msg:'Please wait...'});

						var win;
    					var winxml;
						var wincsw;
						var winrct;
						var winrst;
    
						SyntaxHighlighter.config.clipboardSwf = 'clipboard.swf';
														
						// Créer le formulaire qui va contenir la structure
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
														
														// Créer une iframe pour accueillir le preview XML
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
				// Bouclage pour construire la structure
				$node = $xpathResults->query($queryPath."/".$root[0]->isocode);
				$nodeCount = $node->length;
				//echo $nodeCount." fois ".$root[0]->isocode;
				HTML_metadata::buildTree($database, 0, $root[0]->id, $root[0]->id, $fieldsetName, 'form', str_replace(":", "_", $root[0]->isocode), $xpathResults, $node->item(0), $queryPath, $root[0]->isocode, $account_id, $profile_id, $option);
				
				// Retraverser la structure et autoriser les nulls pour tous les champs cachés
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
													// Retour à la page précédente
													Ext.MessageBox.alert('".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TITLE')."', 
							                    						 '".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TEXT')."',
							                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');});
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
				
				if ($isEditor and !$isPublished)
				{
					$this->javascript .="
					form.fbar.add(new Ext.Button({text: '".JText::_('CORE_VALIDATE')."',
									handler: function()
					                {
					                	myMask.show();
					                 	var fields = new Array();
					        			form.getForm().isInvalid=false;
						        		form.cascade(function(cmp)
					        			{
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
													
													// countValues doit être égal à zéro ou à countFields. Sinon, lever une erreur
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
					        			
										if (!form.getForm().isInvalid)
					        			{
						        			form.getForm().setValues({fieldsets: fieldsets});
						                 	form.getForm().setValues({task: 'validateMetadata'});
						                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
						                 	form.getForm().setValues({object_id: '".$object_id."'});
						                 	form.getForm().setValues({account_id: '".$account_id."'});
											form.getForm().submit({
										    	scope: this,
												method	: 'POST',
												clientValidation: true,
												success: function(form, action) 
												{
													Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_MSG_SUCCESS_TITLE')."', 
							                    						 '".JText::_('CATALOG_VALIDATEMETADATA_MSG_SUCCESS_TEXT')."',
							                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');});

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
										else
										{
											Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TEXT')."');
														
											myMask.hide();
										}
						        	}})
						        );
					form.render();";
				}
				
				// Ajout du bouton PUBLISH seulement si l'utilisateur courant est gestionnaire de la métadonnée
				// et que la métadonnée n'est pas publiée
				if($isManager and $isValidated)
				{
					$this->javascript .="
						form.fbar.add(new Ext.Button({text: '".JText::_('CORE_PUBLISH')."',
											handler: function()
							                {
							                	myMask.show();
							                 	var fields = new Array();
							        			form.getForm().isInvalid=false;
							        			form.cascade(function(cmp)
							        			{
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
														
														// countValues doit être égal à zéro ou à countFields. Sinon, lever une erreur
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
							        			
							        			if (!form.getForm().isInvalid)
							        			{
													form.getForm().setValues({fieldsets: fieldsets});
								                 	form.getForm().setValues({task: 'validateForPublishMetadata'});
								                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
								                 	form.getForm().setValues({object_id: '".$object_id."'});
													form.getForm().submit({
												    	scope: this,
														method	: 'POST',
														clientValidation: true,
														success: function(form, action) 
														{
															xml = (action.result.file.xml);
															xmlfile = xml.split('<br>').join('\\n');
															// Créer une iframe pour demander à l'utilisateur la date de publication
															if (!win)
																win = new Ext.Window({
																		                title:'Publication',
																		                width:300,
																		                height:130,
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
																						         fieldLabel:'Date de publication', 
																						         id:'publishdate', 
																						         xtype: 'datefield',
																						         format: 'd.m.Y',
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
																					                    						 function () {window.open ('./index.php?option=".$option."&task=listMetadata','_parent');});																
																										},
																										failure: function(form, action) 
																										{
																											win.hide();
																											myMask.hide();
	
																					                    	Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TEXT')."');
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
																win.items.get(0).findById('publishdate').setValue('');
																
									  						win.show();
									  						
															myMask.hide();
														},
														failure: function(form, action) 
														{
	                        								Ext.MessageBox.alert('".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_PUBLISHMETADATA_MSG_FAILURE_TEXT')."');
																
															myMask.hide();
														},
														url:'".$publish_url."'
													});
												}
												else
												{
													Ext.MessageBox.alert('".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_VALIDATEMETADATA_LANGUAGE_MSG_FAILURE_TEXT')."');
																
													myMask.hide();
												}
								        	}})
								        );
						form.render();";
						
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
							                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');});
											
													
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
				
				// Ajout du bouton METTRE A JOUR seulement si l'utilisateur courant est gestionnaire de la métadonnée
				// et que la métadonnée est publiée
				if($isManager and $isPublished)
				{
					$this->javascript .="
					form.fbar.add(new Ext.Button({text: '".JText::_('CORE_UPDATE')."',
										handler: function()
						                {
						                	myMask.show();
						                 	var fields = new Array();
						        			form.cascade(function(cmp)
						        			{
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
											form.getForm().submit({
										    	scope: this,
												method	: 'POST',
												clientValidation: true,
												success: function(form, action) 
												{
													Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TITLE')."', 
							                    						 '".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TEXT')."',
							                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');});
												
													myMask.hide();
												},
												failure: function(form, action) 
												{
                        							Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TITLE')."', '".JText::_('CATALOG_UPDATEMETADATA_MSG_FAILURE_TEXT')."');
															
													myMask.hide();
												},
												url:'".$update_url."'
											});
							        	}})
							        );
						form.render();";
				}
					
				if(!$isPublished and !$isValidated)
				{
					// Assignation de métadonnée
					$editors = array();
					$listEditors = array();
					$database->setQuery( "	SELECT DISTINCT c.id AS value, b.name AS text FROM #__users b, #__sdi_editor_object a LEFT OUTER JOIN #__sdi_account c ON a.account_id = c.id LEFT OUTER JOIN #__sdi_manager_object d ON d.account_id=c.id WHERE c.user_id=b.id AND (a.object_id=".$object_id." OR d.object_id=".$object_id.") 
													AND c.user_id <> ".$user_id."  
											ORDER BY b.name" );
					$editors = array_merge( $editors, $database->loadObjectList() );
					foreach($editors as $e)
					{
						$listEditors[$e->value] = $e->text;
					}
					//print_r($listEditors);
					//$editors = str_replace('"', "'", HTML_metadata::array2json($editors));
					//print_r($editors);
					//print_r(HTML_metadata::array2extjs($listEditors, false));
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
													// Créer une iframe pour demander à l'utilisateur la date de publication
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
																                    text:'Submit',
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
																				                    						 function () {window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');});
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
																                    text: 'Close',
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
					                	window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');
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

		// Tout fermer ou tout ouvrir, selon la clé de config METADATA_COLLAPSE 
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
	
function buildTree($database, $ancestor, $parent, $parentFieldset, $parentFieldsetName, $ancestorFieldsetName, $parentName, $xpathResults, $scope, $queryPath, $currentIsocode, $account_id, $profile_id, $option)
	{
		//echo $parent." - ".$parentFieldsetName."<br>";
		//echo "<hr>SCOPE: ".$scope->nodeName."<br>";
		// On récupère dans des variables le scope respectivement pour le traitement des classes enfant et
		// pour le traitement des attributs enfants.
		// Cela permet d'éviter les effets de bord
		//$classScope = $scope;
		//$attributScope = $scope;
						
		// Stockage du path pour atteindre ce noeud du XML
		$queryPath = $queryPath."/".$currentIsocode;
		
		// Construire la liste déroulante des périmètres prédéfinis si on est au bon endroit
		//echo $this->catalogBoundaryIsocode." == ".$currentIsocode.", ".count($this->boundaries)."<br>";
		if ($this->catalogBoundaryIsocode == $currentIsocode AND count($this->boundaries_name) > 0)
		{
			$this->javascript .="
									var valueList = ".HTML_metadata::array2extjs($this->boundaries_name, true).";
								    var boundaries = ".HTML_metadata::array2json($this->boundaries).";
								    var paths = ".HTML_metadata::array2json($this->paths).";
								     // La liste
								     ".$parentFieldsetName.".add(createComboBox_Boundaries('".$parentName."_boundaries', '".html_Metadata::cleanText(JText::_("BOUNDARIES"))."', false, '1', '1', valueList, '', false, '".html_Metadata::cleanText(JText::_("BOUNDARIES"))."', '".JText::_($this->mandatoryMsg)."', boundaries, paths));
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
				
				// Le modèle de saisie
				//$regex = html_Metadata::cleanText($child->attribute_pattern);
				$regex = addslashes($child->attribute_pattern);
				
				if ($regex == null)
					$regex = "";
				
				// Le message à afficher en cas d'erreur de saisie selon le modèle
				$regexmsg = JText::_(strtoupper($child->attribute_guid)."_REGEXMSG");
				if ($regexmsg == null or substr($regexmsg, -9, 9) == "_REGEXMSG")
				{
					$regexmsg = "";			
				}	

				//echo " > ".$child->attribute_isocode." - ".$regex." - ".$regexmsg."<br>";
				
				// Mise en place des contrôles
				// Cas des champs système qui doivent être désactivés
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
				//echo "Le scope pour l'attribut est ".$scope->nodeName." et on recherche ".$child->attribute_isocode."<br>";
				$mainNode = $xpathResults->query($child->attribute_isocode, $scope);
				$attributeCount = $mainNode->length;
	
				//echo "L'attribut enfant ".$child->attribute_isocode." existe ".$attributeCount." fois.<br>";
				
				if ($child->attribute_type == 6 and $attributeCount > 1)
					$attributeCount = 1;
				//echo "Pardon, ".$attributeCount." fois.<br>";
				
				
				// On n'entre dans cette boucle que si on a trouvé au moins une occurence de l'attribut dans le XML
				for ($pos=0; $pos<$attributeCount; $pos++)
				{
					/*
					 * COMPREHENSION DU MODELE
					 * La relation vers l'attribut n'a jamais de code ISO.
					 */  
					//echo "----------- On traite ".$child->attribute_isocode." -----------<br>";
				
					// Construction du master qui permet d'ajouter des occurences de la relation.
					// Le master contient les données de la première occurence.
					if ($pos==0)
					{	
						//echo $pos.") mainNode: ".$mainNode->nodeName."<br>";
						$attributeScope = $mainNode->item($pos);
						//echo "Cas ".$pos."<br>";
						// Traitement de l'attribut enfant.
						// Changement du scope pour le noeud correspondant à cette occurence de l'attribut dans le XML
						//echo $pos.") attributeScope: ".$attributeScope->nodeName."<br>";
						// Modifier le path d'accès à l'attribut
						// $queryPath = $queryPath."/".$child->attribute_id;
						
						// Si on est en train de traiter un attribut de type liste, il faut encore récupérer
						// Le code ISO de la liste, sinon on récupère le code ISO du type d'attribut
						if ($child->attribute_type == 6 )
							$type_isocode = $child->list_isocode;
						else
							$type_isocode = $child->t_isocode;
	
						// Modifier le path d'accès à l'attribut
						$queryPath = $queryPath."/".$child->attribute_isocode."/".$type_isocode;
						
						// Construction du nom de l'attribut
						$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode);
						$currentName = $name."__".($pos+1);
	
						// Traitement de chaque attribut selon son type
						switch($child->attribute_type)
						{
							// Guid (toujours disabled, donc toujours un champ caché)
							case 1:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
								}
								break;
							// Text
							case 2:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
											 	
								if ($node->length>0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue="";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								/*if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
								*/
								$defaultVal = "";
								
								switch ($child->rendertype_id)
								{
									default:
										/* Traitement spécifique aux langues */
										
										// Stockage du path pour atteindre ce noeud du XML
										//$queryPath = $child->attribute_isocode."/gmd:LocalisedCharacterString";
										$queryPath = "gmd:LocalisedCharacterString";
											
										$listNode = $xpathResults->query($child->attribute_isocode, $scope);
										$listCount = $listNode->length;
										//echo "2) Il y a ".$listCount." occurences de ".$child->attribute_isocode." dans ".$scope->nodeName."<br>";
										for($pos=0;$pos<=$listCount; $pos++)
										{
											if ($pos==0)
											{	
												$currentScope=$listNode->item($pos);
												// Traitement de la multiplicité
												// Récupération du path du bloc de champs qui va être créé pour construire le nom
												//$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
												$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
								
												//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
												$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
												$this->javascript .="
												var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', true); 
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
															$defaultVal= html_Metadata::cleanText($nodeValue);
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
															$defaultVal= html_Metadata::cleanText($nodeValue);
															//$nodeValue = "";
														}
													}
													
													//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
													//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
													//echo $LocLangName." - ".$nodeValue."<br>";
													// Selon le rendu de l'attribut, on fait des traitements différents
													switch ($child->rendertype_id)
													{
														// Textarea
														case 1:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														// Textbox
														case 5:
															$this->javascript .="
															//console.log(".$parentFieldsetName.".getId() + ': ' + ".$parentFieldsetName.".collapsed + '\\n\\r a la creation de ' + '".$currentName."' + '\\n\\r' + ".$parentFieldsetName.".masterflow);
															".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														default:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
													}
												}
											}
											else
											{
												$currentScope=$listNode->item($pos-1);
												// Traitement de la multiplicité
												// Récupération du path du bloc de champs qui va être créé pour construire le nom
												$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
												$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
								
												$this->javascript .="
												var master = Ext.getCmp('".$master."');						
												";
												
												//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
												$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
												$this->javascript .="
													var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', true); 
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
															$defaultVal= html_Metadata::cleanText($nodeValue);
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
															$defaultVal= html_Metadata::cleanText($nodeValue);
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
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														// Textbox
														case 5:
															$this->javascript .="
															".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
														default:
															$this->javascript .="
															".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
															";
															break;
													}
												}
											}
										}
										if ($listCount==0 and $child->rel_lowerbound>0)
										{
											// Traitement de la multiplicité
											// Récupération du path du bloc de champs qui va être créé pour construire le nom
											$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__2";
							
											$this->javascript .="
											var master = Ext.getCmp('".$master."');						
											";
											
											//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
											$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
											$this->javascript .="
											var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', true); 
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
														$defaultVal= html_Metadata::cleanText($nodeValue);
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
														$defaultVal= html_Metadata::cleanText($nodeValue);
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
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													// Textbox
													case 5:
														$this->javascript .="
														".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													default:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
												}
											}
										}
										
										break;
								}
								break;
							// Number
							case 4:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id;
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
									 	
									 	// S'il n'y a pas de valeurs existantes, récupérer les valeurs par défaut
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
									 		// Elements sélectionnés par défaut
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
													var valueList = ".HTML_metadata::array2checkbox($listName, false, $dataValues, $nodeValues, html_Metadata::cleanText(JText::_($tip)))."
											     	var selectedValueList = ".HTML_metadata::array2json($nodeValues)."
											     	var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues)."
											     	// La liste
											     	".$parentFieldsetName.".add(createCheckboxGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
											     	";
											 	}
											 	break;
											// Radiobutton
											case 3:
												if ($child->rel_lowerbound == $child->rel_upperbound)
											 	{
											 		$this->javascript .="
													var valueList = ".HTML_metadata::array2checkbox($listName, true, $dataValues, $nodeValues, html_Metadata::cleanText(JText::_($tip)))."
											     	var selectedValueList = ".HTML_metadata::array2json($nodeValues)."
											     	var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues)."
											     	// La liste
											     	".$parentFieldsetName.".add(createRadioGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
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
													var valueList = ".HTML_metadata::array2extjs($dataValues, false)."
											     	var selectedValueList = ".HTML_metadata::array2json($nodeValues)."
											     	var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues)."
											     	// La liste
											     	".$parentFieldsetName.".add(createMultiSelector('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
											     	";
											 	}
											 	else
											 	{
											 		$this->javascript .="
													var valueList = ".HTML_metadata::array2extjs($dataValues, true).";
												     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
												     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues)."
											     	// La liste
												     ".$parentFieldsetName.".add(createComboBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
												    ";
											 	}
												 
												break;
										}
										
									 	break;
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								$nodeValue = substr($nodeValue, 0, 10);
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										//$query = "SELECT t.title, t.content, c.guid FROM #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE published=true AND attribute_id = ".$child->attribute_id." AND c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND t.content = '".html_Metadata::cleanText($node->item(0)->nodeValue)."'"." ORDER BY c.ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
										 	// Elements sélectionnés par défaut
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
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
									    ";
									break;
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
										
									 	// Récupérer le texte localisé stocké
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
													$query = "SELECT t.title, t.content, c.guid FROM #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND t.content = '".html_Metadata::cleanText($node->item(0)->nodeValue)."'"." ORDER BY c.ordering";
													$database->setQuery( $query );
													//echo $database->getQuery()."<br>";
													//$cont_guid = $database->loadResult();
													
													//$nodeValues[] = $database->loadResult();
													$result = $database->loadObject();
													if ($result->title <> "")
														$nodeValues[] = $result->title;
													else
														$nodeValues[] = $result->guid;

													//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
													//echo html_Metadata::cleanText($node->item(0)->nodeValue)."<br>";
													//$nodeValues[] = html_Metadata::cleanText($node->item(0)->nodeValue);
										 		}
												/*else
													$nodeValues[] = "";*/
											}
										}

										//print_r($nodeValues); echo "<br>";
									 	
										$nodeDefaultValues = array();
										if (count($nodeValues) == 0)
									 	{
										 	// Elements sélectionnés par défaut
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
										var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
									     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
									     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
									     // La liste
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
									    ";
									break;
								}
								
								break;
							default:
								// Traitement de la classe enfant
								//echo "Recherche de ".$type_isocode." dans ".$attributeScope->nodeName."<br>";
								//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								//echo "Trouve ".$nodeValue."<br>";
								//echo "Valeur en 0: ".$nodeValue."<br>";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
						
						if ($child->attribute_type <> 9 and $child->attribute_type <> 10)
						{
							//echo $type_isocode." - ".$attributeScope->nodeName."<br>";
							// Traitement de l'attribut enfant
							//$node = $xpathResults->query($child->attribute_isocode."/".$type_isocode, $attributeScope);
							$node = $xpathResults->query($type_isocode, $attributeScope);
							//echo $node->length." - ".$node->item(0)->nodeName."<br>";
							
							$nodeValue = html_Metadata::cleanText($node->item($pos-1)->nodeValue);
							
							// Récupération de la valeur par défaut, s'il y a lieu
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
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
										break;
								}
								break;
							// Text
							case 2:
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
							// Number
							case 4:
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
										break;
									// Textbox
									case 5:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
										";
										break;
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									// Textarea
									case 1:
									// Textbox
									case 5:
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
								 			// Chercher le titre associé au texte localisé souhaité et 
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
										 	// Elements sélectionnés par défaut
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
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."', master, true));
									    ";
										
								 	break;
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
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
										
									 	// Récupérer le texte localisé stocké
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
													$query = "SELECT t.title, t.content, c.guid FROM #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND t.content = '".html_Metadata::cleanText($node->item(0)->nodeValue)."'"." ORDER BY c.ordering";
													$database->setQuery( $query );
													//echo $database->getQuery()."<br>";
													//$cont_guid = $database->loadResult();
													
													//$nodeValues[] = $database->loadResult();
													$result = $database->loadObject();
													if ($result->title <> "")
														$nodeValues[] = $result->title;
													else
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
										 	// Elements sélectionnés par défaut
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
									     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."', master, true));
									    ";
									break;
								}
								
								break;
							default:
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										$this->javascript .="
										".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
				// il faut le créer vide
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
					
					// Récupération de la valeur par défaut, s'il y a lieu
					if ($child->attribute_default <> "")
						$nodeValue = html_Metadata::cleanText($child->attribute_default);
				
					
					// Traitement de chaque attribut selon son type
					switch($child->attribute_type)
					{
						// Guid
						case 1:
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
									break;
								// Textbox
								case 5:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
									break;
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
									break;
							}
							break;
						// Text
						case 2:
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
									break;
								// Textbox
								case 5:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
									break;
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
									/* Traitement spécifique aux langues */
									
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
											// Traitement de la multiplicité
											// Récupération du path du bloc de champs qui va être créé pour construire le nom
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
							
											//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
											$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
											$this->javascript .="
											var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', true); 
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
														$defaultVal= html_Metadata::cleanText($nodeValue);
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
														$defaultVal= html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												
												//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->name)." - ".$nodeValue."<br>";
												//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
												// Selon le rendu de l'attribut, on fait des traitements différents
												switch ($child->rendertype_id)
												{
													// Textarea
													case 1:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													// Textbox
													case 5:
														$this->javascript .="
														".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."','".$defaultVal."',  ".$disabled.", '".$maxLength."', '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													default:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."','".$defaultVal."',  ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
												}
											}
										}
										else
										{
											// Traitement de la multiplicité
											// Récupération du path du bloc de champs qui va être créé pour construire le nom
											$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
							
											$this->javascript .="
											var master = Ext.getCmp('".$master."');						
											";
											
											//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
											$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
											$this->javascript .="
											var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', true); 
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
														$defaultVal= html_Metadata::cleanText($nodeValue);
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
														$defaultVal= html_Metadata::cleanText($nodeValue);
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
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													// Textbox
													case 5:
														$this->javascript .="
														".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
													default:
														$this->javascript .="
														".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
														";
														break;
												}
											}
										}
									}
									if ($listCount==0 and $child->rel_lowerbound>0)
									{
										// Traitement de la multiplicité
										// Récupération du path du bloc de champs qui va être créé pour construire le nom
										$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
										$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__2";
						
										$this->javascript .="
										var master = Ext.getCmp('".$master."');						
										";
										
										//echo $LocName." - ".$child->attribute_id." - ".JText::_($label)." - ".$child->rel_lowerbound." - ".$child->rel_upperbound." - ".$parentFieldsetName."<br>";
										$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
										$this->javascript .="
										var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText(JText::_($label))."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', true); 
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
											$defaultVal= html_Metadata::cleanText($nodeValue);
											//$nodeValue = "";
													
					
											//echo $LocLangName." - ".$child->attribute_id." - ".JText::_($row->code)." - ".$nodeValue."<br>";
											//echo $child->attribute_id." - ".$LocLangName." - ".$nodeValue."<br>";
											switch ($child->rendertype_id)
											{
												// Textarea
												case 1:
													$this->javascript .="
													".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
													";
													break;
												// Textbox
												case 5:
													$this->javascript .="
													".$fieldsetName.".add(createTextField('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", '".$maxLength."', '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
													";
													break;
												default:
													$this->javascript .="
													".$fieldsetName.".add(createTextArea('".$LocLangName."', '".JText::_($row->label)."',".$mandatory.", false, null, '1', '1', '".$nodeValue."', '".$defaultVal."', ".$disabled.", ".$maxLength.", '', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
													";
													break;
											}
										}
									}
									break;
							}
							break;
						// Number
						case 4:
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
								// Textbox
								case 5:
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
								// Textbox
								case 5:
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								default:
									// Traitement spécifique aux listes
									//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (2)<br>";					
									// Traitement des enfants de type list
									$content = array();
									//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
									$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id;
									$database->setQuery( $query );
									$content = $database->loadObjectList();
										
								 	$dataValues = array();
								 	$nodeValues = array();
							
								 	// Traitement de la multiplicité
								 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
									
									// S'il n'y a pas de valeurs existantes, récupérer les valeurs par défaut
									$nodeDefaultValues = array();
									if (count($nodeValues) == 0)
								 	{
								 		// Elements sélectionnés par défaut
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
										     	".$parentFieldsetName.".add(createCheckboxGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
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
										     	".$parentFieldsetName.".add(createRadioGroup('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
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
										     	".$parentFieldsetName.".add(createMultiSelector('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '1', '1', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
										     	";
										 	}
										 	else
										 	{
										 		$this->javascript .="
												var valueList = ".HTML_metadata::array2extjs($dataValues, true).";
											     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
											     var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
										     	
											     // La liste
											     ".$parentFieldsetName.".add(createComboBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
											    ";
										 	}										 
											break;
									}
								 	break;
							}
							break;
						// Link
						case 7:
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
									break;
								// Textbox
								case 5:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
									break;
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								// Textarea
								case 1:
								// Textbox
								case 5:
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
									 	
										// Elements sélectionnés par défaut
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
									    ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
									    ";
								}
								
								break;
							// LocaleChoice
							case 10:
								// Selon le rendu de l'attribut, on fait des traitements différents
								switch ($child->rendertype_id)
								{
									default:
										// Traitement spécifique aux listes
										//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
										// Traitement des enfants de type list
										$content = array();
										//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
										$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
										$database->setQuery( $query );
										$content = $database->loadObjectList();
	
									 	$dataValues = array();
									 	$nodeValues = array();
								
									 	// Traitement de la multiplicité
									 	// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
									 	
										// Elements sélectionnés par défaut
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
									    ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".JText::_($this->mandatoryMsg)."'));
									    ";
									break;
								}
								
								break;
						default:
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								default:
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
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
		// Récupération des relations de cette classe (parent) vers d'autres classes (enfants)
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
						// Créer un nouveau fieldset
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
				
				//echo "Appel récursif avec les valeurs suivantes:<br> ";
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
			// Si pas trouvé, on entre une fois dans la boucle pour créer
			// une seule occurence de saisie (master)
			for ($pos=0; $pos<=$relCount; $pos++)
			{
				/*
				 * COMPREHENSION DU MODELE
				 * C'est la relation qui est multiple. De ce fait on a toujours un et un
				 * seul enfant pour chaque relation trouvée.
				 */  
				
				// Construction du master qui permet d'ajouter des occurences de la relation.
				// Le master doit contenir une structure mais pas de données.
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
						// Récupération du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->child_isocode, $relScope);
						//echo "Trouve ".$childnode->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouvé des occurences, on modifie le scope.
						if ($childnode->length > 0)
							$classScope = $childnode->item(0);
						else
							$classScope = $relScope;	
					}
					else
						$classScope = $relScope;
					
					// Construction du nom du fieldset qui va correspondre à la classe
					// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
					// On démarre l'indexation à 1
					$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__".($pos+1);
							
					// Construction du bloc de la classe enfant
					// Nom du fieldset avec guid pour l'unicité
					$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					
					$this->javascript .="
						// Créer un nouveau fieldset
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."'); 
						".$parentFieldsetName.".add(".$fieldsetName.");
					";

					/*
					// S'il y a un xlink:title défini, alors afficher une balise pour le saisir
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
					// Appel récursif de la fonction pour le traitement du prochain niveau
					//HTML_metadata::buildTree($prof, $database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $option);
					//echo "Appel récursif avec les valeurs suivantes:<br> ";
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
					
					// Test pour le cas d'une relation qui boucle une classe sur elle-même
					if ($ancestor <> $parent)					
						HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
		
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
				// Ici on va traiter toutes les occurences trouvées dans le XML
				else
				{
					// Traitement de la relation entre la classe parent et la classe enfant
					$relScope = $node->item($pos-1);
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// Récupération du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->child_isocode, $relScope);
						//echo "Trouve ".$node->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouvé des occurences, on modifie le scope.
						if ($childnode->length > 0)
							$classScope = $childnode->item(0);
						else
							$classScope = $relScope;
					}
					else
						$classScope = $relScope;
					
					// Construction du nom du fieldset qui va correspondre à la classe
					// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
					// On récupère le master qui a l'index 1
					$master = $parentName."-".str_replace(":", "_", $child->child_isocode)."__1";
					// On construit le nom de l'occurence
					$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__".($pos+1);
							
					// Construction du bloc de la classe enfant
					// Nom du fieldset avec guid pour l'unicité
					$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
					
					$this->javascript .="
						var master = Ext.getCmp('".$master."');							
						// Créer un nouveau fieldset
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."'); 
						".$parentFieldsetName.".add(".$fieldsetName.");
					";
					
					/*
					// S'il y a un xlink:title défini, alors afficher une balise pour le saisir
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
					// Appel récursif de la fonction pour le traitement du prochain niveau
					//HTML_metadata::buildTree($prof, $database, $child->classes_to_id, $child->classes_to_id, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $option);
					
					//echo "Appel récursif avec les valeurs suivantes:<br> ";
					//echo "Parent = ".$child->child_id."<br>";
					//echo "Parent Fieldset = ".$child->child_id."<br>";
					//echo "Parent Name = ".$name."<br>";
					//echo "Scope = ".$classScope->nodeName."<br>";
					//echo "QueryPath = ".$queryPath."<br>";
					//echo "Current Isocode = ".$nextIsocode."<br>";
					//echo "Account Id = ".$account_id."<br>";
					//echo "<hr>";
					
					HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
				
				
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
			}

			// Si la classe est obligatoire mais qu'elle n'existe pas à l'heure actuelle dans le XML, 
			// il faut créer en plus du master un bloc de saisie qui ne puisse pas être supprimé par l'utilisateur 
			if ($relCount==0 and $child->rel_lowerbound>0)
			{
				// Construction du nom du fieldset qui va correspondre à la classe
				// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
				// On récupère le master qui a l'index 1
				$master = $parentName."-".str_replace(":", "_", $child->child_isocode)."__1";
				// On construit le nom de l'occurence qui a forcément l'index 2
				$name = $parentName."-".str_replace(":", "_", $child->child_isocode)."__2";

				// Le scope reste le même, il n'aura de toute façon plus d'utilité pour les enfants
				// puisqu'à partir de ce niveau plus rien n'existe dans le XML	
				$classScope = $scope;
					
				// Construction du fieldset
				$fieldsetName = "fieldset".$child->child_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
				
				$this->javascript .="
					var master = Ext.getCmp('".$master."');							
					// Créer un nouveau fieldset
					var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."'); 
					".$parentFieldsetName.".add(".$fieldsetName.");
				";	

				/*	
				// S'il y a un xlink:title défini, alors afficher une balise pour le saisir
				if ($child->has_xlinkTitle)
				{
					$xlinkTitleValue = "";

					$this->javascript .="
					fieldset".$child->classes_to_id.".add(createTextArea('".$name."_xlinktitle', '".JText::_('EDIT_METADATA_EXTENSION_TITLE')."',true, false, null, '1', '1', '".$xlinkTitleValue."'));";
				}
				*/	
					
				// Le code ISO de la classe enfant devient le code ISO du nouveau parent
				$nextIsocode = $child->child_isocode;
				
				
				// Appel récursif de la fonction pour le traitement du prochain niveau
				if ($ancestor <> $parent)
					HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					
				// Classassociation_id contient une classe
				if ($child->association_id <>0)
				{
					// Appel récursif de la fonction pour le traitement du prochain niveau
					if ($ancestor <> $parent)
						HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
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
			/*if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
			{
				$tip = JText::_(strtoupper($child->class_guid)."_INFORMATION");
				if ($tip == null or substr($tip, -12, 12) == "_INFORMATION")
					$tip = ""; //$child->rel_name;			
			}*/
			
			// On regarde dans le XML s'il contient la balise correspondante au code ISO de la relation,
			// et combien de fois au niveau courant
			$node = $xpathResults->query($child->rel_isocode, $scope);
			$relCount = $node->length;
			
			//echo $relCount." enregistrements trouves pour ".$child->rel_isocode." dans ".$scope->nodeName."<br>";
			// Traitement de chaque occurence de la relation dans le XML.
			// Si pas trouvé, on entre une fois dans la boucle pour créer
			// une seule occurence de saisie (master)
			//echo $child->rel_lowerbound. " - ".$child->rel_upperbound."<br>";
			for ($pos=0; $pos<=$relCount; $pos++)
			{
				// Construction du master qui permet d'ajouter des occurences de la relation.
				// Le master doit contenir une structure mais pas de données.
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
						
			// On construit le nom de l'occurence qui a forcément l'index 2
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
						// Récupération du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->rel_isocode, $relScope);
						//echo "Trouve ".$childnode->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouvé des occurences, on modifie le scope.
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
							// Créer un nouveau fieldset
							var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."'); 
								".$parentFieldsetName.".add(".$fieldsetName.");
							".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
							";
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
				else
				{
					$guid = substr($node->item($pos-1)->attributes->getNamedItem('href')->value, -36);
					//echo "Trouve ".$guid."<br>";
					$results = array();
					$database->setQuery( "SELECT o.id as id, 
												 m.guid as guid, 
												 CONCAT(o.name, ' ', ov.title) as name 
										  FROM 	 #__sdi_object o, 
										  		 #__sdi_objecttype ot, 
										  		 #__sdi_metadata m,
										  		 #__sdi_objectversion ov 
										  WHERE  o.id=ov.object_id
										  		 AND ov.metadata_id=m.id 
										  		 AND o.objecttype_id=ot.id 
										  		 AND ot.id=".$child->objecttype_id." 
										  		 AND m.guid ='".$guid."'" );
					$results= array_merge( $results, $database->loadObjectList() );
					//$results = HTML_metadata::array2json(array ("total"=>count($results), "contacts"=>$results));
					$results = HTML_metadata::array2json($results);
					//$results = $results[0]->guid;
					//print_r($results);
						
			
					// Construction du nom du fieldset qui va correspondre à la classe
					// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
					// On récupère le master qui a l'index 1
					$master = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
					// On construit le nom de l'occurence qui a forcément l'index 2
					$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__".($pos+1);
				
					// Traitement de la relation entre la classe parent et la classe enfant
					$relScope = $node->item($pos-1);
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// Récupération du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->rel_isocode, $relScope);
						//echo "Trouve ".$childnode->length." fois<br>";
						// Compte du nombre d'occurence du code ISO de la classe enfant dans le XML
						//$childCount = $node->length;
						//echo "La classe ".$child->child_isocode." existe ".$node->length." fois.<br>";
			
						// Si on a trouvé des occurences, on modifie le scope.
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
						// Créer un nouveau fieldset
						var master = Ext.getCmp('".$master."');							
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."'); 
							".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
					}
				}
			}
			
			
			
			// Si l'objet est obligatoire mais qu'il n'existe pas à l'heure actuelle dans le XML, 
			// il faut créer en plus du master un bloc de saisie qui ne puisse pas être supprimé par l'utilisateur 
			if ($relCount==0 and $child->rel_lowerbound>0)
			{
				$guid = "";
				//echo "Trouve ".$guid."<br>";
				$results = array();
				$results = HTML_metadata::array2json($results);
			
				// Construction du nom du fieldset qui va correspondre à la classe
				// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
				// On récupère le master qui a l'index 1
				$master = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
				// On construit le nom de l'occurence qui a forcément l'index 2
				$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__2";
			
				// Le scope reste le même, il n'aura de toute façon plus d'utilité pour les enfants
				// puisqu'à partir de ce niveau plus rien n'existe dans le XML	
				$classScope = $scope;
					
				// Construction du fieldset
				$fieldsetName = "fieldset".$child->rel_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
				$searchFieldName = $name."-"."SEARCH__1"; //2";
				
				$this->javascript .="
					// Créer un nouveau fieldset
					var master = Ext.getCmp('".$master."');							
					var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."'); 
						".$parentFieldsetName.".add(".$fieldsetName.");
					".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', '', false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
					";
				
				// Classassociation_id contient une classe
				$nextIsocode = $child->rel_isocode;
				if ($child->association_id <>0)
				{
					// Appel récursif de la fonction pour le traitement du prochain niveau
					if ($ancestor <> $parent)
						HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option);
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
		//$text = str_replace("’","\’",$text);
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
			// Entrée vide
			$extjsArray .= "['', ''], ";
		}
		$id=0;
		foreach($arr as $key=>$value)
		{
			$extjsArray .= "[";
			//$extjsArray .= "'".$id."', ";
			if ($textlist) // Mettre le titre à vide si on est dans une liste de type texte
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
	
	function historyAssignMetadata($rows, $pageNav, $object_id, $option)
	{
		$database =& JFactory::getDBO(); 
		?>	
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("CATALOG_HISTORYASSIGN_METADATA"); ?></h2>
		<div class="contentin">
		<form action="index.php" method="POST" id="historyassignForm" name="historyassignForm">
		<table width="100%">
			<tr>
				<td align="right">
					<button type="button" onClick="document.getElementById('task').value='listMetadata';document.getElementById('historyassignForm').submit();" ><?php echo JText::_("CORE_CANCEL"); ?></button>
				</td>
			</tr>
		</table>
		<br/>		
		<table width="100%">
			<tr>																																						
				<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
				<td align="center"><?php echo JText::_("CORE_SHOP_DISPLAY"); ?> <?php echo $pageNav->getLimitBox(); ?></td>
				<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
			</tr>
		</table>
	<table id="myHistoryAssign" class="box-table">
	<thead>
	<tr>
	<th><?php echo JText::_('CATALOG_HISTORYASSIGN_ASSIGNEDBY'); ?></th>
	<th><?php echo JText::_('CATALOG_HISTORYASSIGN_ASSIGNEDTO'); ?></th>
	<th><?php echo JText::_('CATALOG_HISTORYASSIGN_DATE'); ?></th>
	<th><?php echo JText::_('CATALOG_HISTORYASSIGN_INFORMATION'); ?></th>
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
	<br/>
	<table width="100%">
		<tr>																																						
			<td align="left"><?php echo $pageNav->getPagesCounter(); ?></td>
			<td align="center">&nbsp;</td>
			<td align="right"><?php echo $pageNav->getPagesLinks(); ?></td>
		</tr>
	</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>">
			<input type="hidden" name="object_id" value="<?php echo $object_id; ?>">
			<input type="hidden" id="task" name="task" value="historyAssignMetadata">
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
															         id:'task', 
															         xtype: 'hidden',
															         value:'importXMLMetadata' 
															       },
															       { 
															         id:'option', 
															         xtype: 'hidden',
															         value:'".$option."' 
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
			// Boutons de réplication
			if (!$isPublished)
			{
				$replicate_url = 'index.php?option='.$option.'&task=replicateMetadata';
			
				// Réplication de métadonnée
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
		                	// Créer une iframe pour demander à l'utilisateur le type d'import
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
						                	// Créer une iframe pour confirmer la réinitialisation
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
	                	window.open ('./index.php?option=".$option."&task=cancelMetadata&object_id=".$object_id."','_parent');
		        	}}";
				
		return implode(', ', $tbar);
	}
}
?>
