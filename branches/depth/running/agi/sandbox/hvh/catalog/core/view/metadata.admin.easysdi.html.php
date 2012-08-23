<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin de??Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.jsLoaderUtil.php');

if((JRequest::getVar('task') =="editMetadata")||
(JRequest::getVar('task') =="askForEditMetadata")|| 
(JRequest::getVar('task') =="importXMLMetadata")|| 
(JRequest::getVar('task') =="importCSWMetadata") ||(JRequest::getVar('task') =="replicateMetadata")){
?>
<script>
var thesaurusConfig = '<?php echo config_easysdi::getValue("thesaurusUrl");?>';
</script>
<?php }
			
JHTML::script('ext-base.js', 'administrator/components/com_easysdi_catalog/ext/adapter/ext/');
JHTML::script('ext-all.js', 'administrator/components/com_easysdi_catalog/ext/');
JHTML::script('catalogMapPanel.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('catalogFreePerimeterSelector.js', 'administrator/components/com_easysdi_catalog/js/');
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
JHTML::script('thesaur.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('HS.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('BoundaryItemSelector.js', 'administrator/components/com_easysdi_catalog/js/');
JHTML::script('MultiSelect.js', 'administrator/components/com_easysdi_catalog/js/');

$jsLoader =JSLOADER_UTIL::getInstance();
JHTML::script('SingleFile.js', $jsLoader->getPath("map","openlayers", "/lib/OpenLayers/"));
JHTML::script('OpenLayers.js', $jsLoader->getPath("map","openlayers"));

class HTML_metadata {
	var $javascript = "";
	var $langList = array ();
	var $mandatoryMsg = "";
	var $regexMsg = "";
	var $boundaries = array();
	var $paths = array();
	var $boundaries_name = array();
	var $catalogBoundaryIsocode = "";
	var $qTipDismissDelay = "5000"; // Durée par défaut de l'affichage du tooltip
	var $parentId_class="";
	var $parentId_attribute="";
	var $parentGuid="";
	
	/**
	 * editMetadata
	 * Enter description here ...
	 * @param unknown_type $object_id
	 * @param unknown_type $root
	 * @param unknown_type $metadata_id
	 * @param unknown_type $xpathResults
	 * @param unknown_type $profile_id
	 * @param unknown_type $isManager
	 * @param unknown_type $isEditor
	 * @param unknown_type $boundaries
	 * @param unknown_type $catalogBoundaryIsocode
	 * @param unknown_type $type_isocode
	 * @param unknown_type $isPublished
	 * @param unknown_type $isValidated
	 * @param unknown_type $object_name
	 * @param unknown_type $version_title
	 * @param unknown_type $option
	 * @param unknown_type $defautBBoxConfig
	 */
	function editMetadata($object_id, $root, $metadata_id, $xpathResults, $profile_id, $isManager, $isEditor, $boundaries, $catalogBoundaryIsocode, $type_isocode, $isPublished, $isValidated, $object_name, $version_title, $option, $defautBBoxConfig="")
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
		$this->qTipDismissDelay = config_easysdi::getValue("catalog_metadata_qtipDelay");
		$this->mandatoryMsg = html_Metadata::cleanText(JText::_('CATALOG_METADATA_EDIT_MANDATORY_MSG'));
		$this->regexMsg = html_Metadata::cleanText(JText::_('CATALOG_METADATA_EDIT_REGEX_MSG'));
	
		// Récupérer les noms des périmètres
		$this->boundaries[JText::_('CORE_METADATASTATE_LIST')] = array('northbound'=>0, 'southbound'=>0, 'westbound'=>0, 'eastbound'=>0);
		
		foreach($boundaries as $boundary)
		{
			$this->boundaries_name[] = addslashes(JText::_($boundary->guid."_LABEL"));
			$this->boundaries[JText::_($boundary->guid."_LABEL")] = array('northbound'=>$boundary->northbound, 'southbound'=>$boundary->southbound, 'westbound'=>$boundary->westbound, 'eastbound'=>$boundary->eastbound);
		}
		$this->catalogBoundaryIsocode = $catalogBoundaryIsocode;
	
		$catalogBoundaryIsocode = config_easysdi::getValue("catalog_boundary_isocode");
		$this->paths[] = array(	'northbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_north")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'southbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_south")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'westbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_west")."-".str_replace(":", "_", $type_isocode)."__1"), 
								'eastbound'=>"-".str_replace(":", "_", config_easysdi::getValue("catalog_boundary_east")."-".str_replace(":", "_", $type_isocode)."__1"));
		
		
		
		// Récupèrer les infos pour la métadonnée parente pour le lien entre les types d'objet où cet objet est l'enfant et la borne parent max est égale à 1
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
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/ext/resources/css/ext-all.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/form_layout_backend.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/MultiSelect.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/fileuploadfield.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/superboxselect.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/shCore.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/shThemeDefault.css');
		$document->addStyleSheet($uri->base(true) . '/components/com_easysdi_catalog/templates/css/mapHelper.css');
		
		$url = 'index.php?option='.$option.'&task=saveMetadata';
		$preview_url = 'index.php?option='.$option.'&task=previewXMLMetadata';
		$update_url = 'index.php?option='.$option.'&task=updateMetadata';
		
		$user =& JFactory::getUser();
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

		// Langues à gérer
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
			<form action="index.php" method="post" name="adminForm" id="adminForm"
			class="adminForm">
			<input type="hidden" name="option" value="<?php echo $option; ?>" /> 
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="object_id" value="<?php echo $object_id;?>" />
			</form>
	
				<?php $document->addScriptDeclaration( $defautBBoxConfig );?>
			
				<?php
				$this->javascript .="
						var domNode = Ext.DomQuery.selectNode('div#element-box div.m')
						Ext.DomHelper.insertHtml('beforeEnd',domNode,'<div id=formContainer></div>');

						// Waiting message during page loading
						var myMask = new Ext.LoadMask(Ext.getBody(), {msg:'Please wait...'});
						
						// Declaration of the variables potentially used in all the windows that can be built
						var win;
						var winxml;
						var wincsw;
						var winrct;
						var winrst;
						var winupload;
    
						// Tools for the preview
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
						HS.Lang['".$userLang."']['CATALOG_METADATA_CLEAR_UPLOADEDFILE_WAIT_PRG']='".html_Metadata::cleanText(JText::_('CATALOG_METADATA_CLEAR_UPLOADEDFILE_WAIT_PRG'))."';
						HS.Lang['".$userLang."']['CATALOG_METADATA_ALERT_CLEAR_UPLOADEDFILE_CONFIRM_TITLE']='".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_CLEAR_UPLOADEDFILE_CONFIRM_TITLE'))."';
						HS.Lang['".$userLang."']['CORE_METADATA_UPLOADFILE_ERROR']='".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_ERROR'))."';
						HS.Lang['".$userLang."']['CATALOG_METADATA_ALERT_CLEAR_UPLOADEDFILE_CONFIRM_MSG']='".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_CLEAR_UPLOADEDFILE_CONFIRM_MSG'))."';
						HS.Lang['".$userLang."']['CATALOG_METADATA_ALERT_MINUS_ACTION_CONFIRM_TITLE']='".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_MINUS_ACTION_CONFIRM_TITLE'))."';
						HS.Lang['".$userLang."']['CATALOG_METADATA_ALERT_MINUS_ACTION_CONFIRM_MSG']='".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_MINUS_ACTION_CONFIRM_MSG'))."';
						
						// Create the form that will hold in the structure
						var form = new Ext.form.FormPanel({
								id:'metadataForm',
								url: 'index.php',
								labelAlign: 'left',
						        labelWidth: 200,
						        border:false,
						        collapsed:false,
						        renderTo: document.getElementById('formContainer'),
						        isInvalid: false,
						       
						        showUploadFileWindow : function (caller){
						        	var longStartValue = Ext.ComponentMgr.get(caller).getValue();
						        	var shortStartValue = longStartValue.substring(longStartValue.lastIndexOf('/')+1);
						        	var doesFileExist = false;
						        	if (Ext.ComponentMgr.get(caller).getValue().length != 0)
						        		doesFileExist = true ;
						        	if(winupload) winupload.close();
									winupload = new Ext.Window({
										title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_UPLOADFILE_ALERT'))."',
										width:500,
										height:160,
										closeAction:'hide',
										layout:'fit',
										border:true,
										closable:true,
										renderTo:Ext.getBody(),
										frame:true,
										backvalue:'',
										items:[{
											xtype:'form',
											fileUpload: true,
											isUpload: true,
											id:'uploadfileform' ,
											defaultType:'textfield',
											method:'POST',
											enctype:'multipart/form-data',
											frame:true ,
											defaults:{anchor:'95%'},
											items:[
												{
													xtype: 'fileuploadfield',
													id: 'uploadfilefield',
													name: 'uploadfilefield',
													value : shortStartValue,
													buttonText: '".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_BUTTON'))."',
													fieldLabel: '".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_LABEL'))."',
													listeners : {
														fileselected : function(){
															Ext.ComponentMgr.get('winupload_OK').enable();
															Ext.ComponentMgr.get('winupload_DELETE').disable();
														}
													}
												},
												{
													xtype: 'label'
													
										            
												},
												{
													xtype: 'label',
													html : '<table cellspacing=\"10\"><tr><td> <img src=\"./templates/system/images/notice-info.png\" width=\"32\" height=\"32\"  /></td><td><p>".html_Metadata::cleanText(JText::_('CORE_METADATA_STEREOTYPE_FILE_WARNING'))."</td></tr> </table>'
												}
											]
											,buttonAlign:'right'
											,buttons: [
												{
													id : 'winupload_DELETE',
													text: '".html_Metadata::cleanText(JText::_('CATALOG_ALERT_DELETE'))."',
													handler: Ext.ComponentMgr.get('metadataForm').clearUploadedFile.createCallback(caller),
													disabled : !doesFileExist
												},
												{
													id : 'winupload_OK',
													text:'".html_Metadata::cleanText(JText::_('CATALOG_ALERT_UPLOAD'))."',
													disabled : true,
													handler: function(){
														if(Ext.ComponentMgr.get(caller).getValue().length != 0){
															Ext.Msg.confirm(
																	'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_UPLOADFILE_FILE_EXISTS_TITLE'))."',
																	'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_UPLOADFILE_FILE_EXISTS_MSG'))."',
																	function(btn, text){
																		if (btn == 'yes'){
																			Ext.MessageBox.show({
																				title: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_CLEAR_UPLOADEDFILE_WAIT'))."',
																				msg: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_CLEAR_UPLOADEDFILE_WAIT_PRG'))."',
																				width:300,
																				wait:true,
																				waitConfig: {
																					interval:200}
																			});
																			Ext.Ajax.request({
																				url:'index.php?option=com_easysdi_catalog&task=deleteUploadedFile&file='+Ext.ComponentMgr.get(caller).getValue(),
																				method:'GET',
																				success:function(result,request) {
																					if(JSON.parse (result.responseText).success){
																						Ext.MessageBox.hide();
																						Ext.ComponentMgr.get(caller).setValue('');
																						if(Ext.ComponentMgr.get(caller.concat('_hiddenVal')))
																							Ext.ComponentMgr.get(caller.concat('_hiddenVal')).setValue('');
																						Ext.ComponentMgr.get('metadataForm').uploadfile(caller);
																					}else{
																						Ext.MessageBox.hide();
																						Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_ERROR'))." : '+JSON.parse (result.responseText).cause);
																					}
																				},
																				failure:function(result,request) {
																					Ext.MessageBox.hide();
																					Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_ERROR'))." : '+JSON.parse (result.response.responseText).cause);
																				}
																			});
																		}
																	}
																);
														}else{
															Ext.ComponentMgr.get('metadataForm').uploadfile(caller);
														}
														
													}
												},
												{
													text: '".html_Metadata::cleanText(JText::_('CATALOG_ALERT_CLOSE'))."',
													handler: function(){
														winupload.close();
														Ext.ComponentMgr.get(caller).setValue (longStartValue);
													}
												}
												
											]
										}]
								 	});
								 	
									winupload.on('beforeclose', function(){
										Ext.ComponentMgr.get(caller).setValue(winupload.backvalue);
										if(Ext.ComponentMgr.get(caller.concat('_hiddenVal')))
											Ext.ComponentMgr.get(caller.concat('_hiddenVal')).setValue(winupload.backvalue);
									}, this);
									
									winupload.show();
								},
						        
								clearUploadedFile : function  (caller){
									Ext.Msg.confirm(
										'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_CLEAR_UPLOADEDFILE_CONFIRM_TITLE'))."',
										'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_ALERT_CLEAR_UPLOADEDFILE_CONFIRM_MSG'))."',
										function(btn, text){
											if (btn == 'yes'){
												Ext.MessageBox.show({
													title: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_CLEAR_UPLOADEDFILE_WAIT'))."',
													msg: '".html_Metadata::cleanText(JText::_('CATALOG_METADATA_CLEAR_UPLOADEDFILE_WAIT_PRG'))."',
													width:300,
													wait:true,
													waitConfig: {
														interval:200}
												});
												Ext.Ajax.request({
													url:'index.php?option=com_easysdi_catalog&task=deleteUploadedFile&file='+Ext.ComponentMgr.get(caller).getValue(),
													method:'GET',
													success:function(result,request) {
														if(JSON.parse (result.responseText).success){
															Ext.MessageBox.hide();
															Ext.ComponentMgr.get(caller).setValue('');
															if(Ext.ComponentMgr.get(caller.concat('_hiddenVal')))
																Ext.ComponentMgr.get(caller.concat('_hiddenVal')).setValue('');
															//Ext.ComponentMgr.get('metadataForm').saveMetadataAfterLinkedFileUpdate();
															if(winupload)
																winupload.close();
														}else {
															Ext.MessageBox.hide();
															Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_ERROR'))." : '+JSON.parse (result.responseText).cause);
															
														}	
													},
													failure:function(result,request) {
														Ext.MessageBox.hide();
														Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_ERROR'))." : '+JSON.parse (result.responseText).cause);
													}
												});
											}
										}
									)
								},
								
								uploadfile : function  (caller){
									winupload.items.get(0).getForm().submit({
										url: 'index.php?option=com_easysdi_catalog&task=uploadFileAndGetLink',
										waitMsg: '".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_WAIT'))."',
										success: function(form,action){
											winupload.backvalue = JSON.parse (action.response.responseText).url;
											//Ext.ComponentMgr.get('metadataForm').saveMetadataAfterLinkedFileUpdate();
											winupload.close();
										},
										failure: function(form,action){
											Ext.MessageBox.alert('".html_Metadata::cleanText(JText::_('CORE_METADATA_UPLOADFILE_ERROR'))." : '+JSON.parse (action.response.responseText).cause);
											winupload.close();
										}
									});
								},
								
								saveMetadataAfterLinkedFileUpdate : function (){
									myMask.show();
							        var fields = new Array();
							        form.getForm().fieldInvalid =false;	
							        form.cascade(function(cmp)
							        {
										if(cmp.isValid){
										    if(!cmp.isValid()&& Ext.get(cmp.id)){														
												form.getForm().fieldInvalid =true;													
											}
										}
					        			if (cmp.getId() == 'gmd_MD_Metadata-gmd_MD_DataIdentification__2-gmd_abstract__2-gmd_LocalisedCharacterString-fr-FR__1'){
			         					}
						          					
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
							        form.getForm().setValues({task: 'saveMetadata'});
							        form.getForm().setValues({metadata_id: '".$metadata_id."'});
							        form.getForm().setValues({object_id: '".$object_id."'});
									form.getForm().submit({
										scope: this,
										method	: 'POST',
										clientValidation: false,
										success: function(form, action){
							                myMask.hide();
										},
										failure: function(form, action){
                        					if (action.result)
												alert(action.result.errors.xml);
											else
												alert('Form save error');
											myMask.hide();
										},
										url:'".$url."'
									});
								},
								
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
														var xml = '<xml version=\"1.0\" encoding=\"UTF-8\">' + action.result.file.xml;
														xml = xml.split('<br>').join('\\n');
														var html = '<pre class=\"brush: xml;gutter: false;\">' + xml + '</pre>';
														
														// Create an iframe to host the XML preview
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
						        }],
						       	tbar: new Ext.Toolbar({
						       			toolbarCls: 'x-panel-fbar',
						       			buttonAlign: 'right',
						       			layout: 'toolbar',
						       			cls: 'x-panel-footer x-panel-footer-noborder x-panel-btns',
						       			items: [".HTML_metadata::buildTBar($isPublished, $isValidated, $object_id, $metadata_id, $account_id, $metadata_collapse, $option)."]
						       			})
						    });
						//console.log(form.fbar.layout);
						//console.log(form.fbar);		
						
						var ".$fieldsetName." = new Ext.form.FieldSet({id:'".str_replace(":", "_", $root[0]->isocode)."', cls: 'easysdi_shop_backend_form', title:'".html_Metadata::cleanText(JText::_($root[0]->label))."', xtype: 'fieldset', clones_count: 1});
						form.add(".$fieldsetName.");";
						
				$queryPath="/";
				// Boucle pour construire la structure
				$node = $xpathResults->query($queryPath."/".$root[0]->isocode);
				$nodeCount = $node->length;
				
				HTML_metadata::buildTree($database, 0, $root[0]->id, $root[0]->id, $fieldsetName, 'form', str_replace(":", "_", $root[0]->isocode), $xpathResults, null, $node->item(0), $queryPath, $root[0]->isocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
				
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

				// Ajout du bouton VALIDER seulement si l'utilisateur courant est gestionnaire de la métadonnée
				if(!$isPublished)
				{
					$this->javascript .="
					var saveButton = new Ext.Button( {
							                text: '".JText::_('CORE_SAVE')."',
							                handler: function()
							                {
							                	myMask.show();
							                 	var fields = new Array();
							                 	form.getForm().fieldInvalid =false;	
							        			form.cascade(function(cmp)
							        			{
										        	if(cmp.isValid){
									        				if(!cmp.isValid()&& Ext.get(cmp.id)){														
																	form.getForm().fieldInvalid =true;													
														
																
															}
													}
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
							        			
							        			if(!form.getForm().fieldInvalid) 
					        					{	
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
															// Retour à la page précèdente
															Ext.MessageBox.alert('".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TITLE')."', 
								                    						 '".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TEXT')."',
								                    						 function () {window.open ('./index.php?option=".$option."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."&task=cancelMetadata','_parent');});
													
														
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
												else{
													alert('Please verify whether all required fields are present');
													myMask.hide();
												
												}
							        	}
						        })
							        
						form.fbar.add(saveButton);
							        
						form.render();";
					
					
					$this->javascript .="
						var applyButton = new Ext.Button( {
							text: '".JText::_('CATALOG_APPLY')."',
							handler: function(){
										myMask.show();
										var fields = new Array();
										form.getForm().fieldInvalid =false;
										form.cascade(function(cmp){
											if(cmp.isValid){
												if(!cmp.isValid()&& Ext.get(cmp.id)){
													form.getForm().fieldInvalid =true;
												}
											}
											if (cmp.xtype=='fieldset')
											{
												if (cmp.clones_count)
												{
													fields.push(cmp.getId()+','+cmp.clones_count);
												}
											}
										});
					
										if(!form.getForm().fieldInvalid)
										{
											var fieldsets = fields.join(' | ');
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
																Ext.MessageBox.alert('".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TITLE')."',
																'".JText::_('CATALOG_SAVEMETADATA_MSG_SUCCESS_TEXT')."',
																function () {
																	//window.open ('./index.php?option=".$option."&cid[]=".$object_id."&lang=".JRequest::getVar('lang')."&task=askForEditMetadata','_parent');
																}
																);
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
										else
										{
											alert('Please verify whether all required fields are present');
											myMask.hide();
										}
							}
						})
					 
						form.fbar.add(applyButton);
						form.render();";
					
				}
				
				// Ajout du bouton METTRE A JOUR seulement si l'utilisateur courant est gestionnaire de la métadonnée
				// et que la métadonnée est publiée
				else if($isPublished)
				{
					$this->javascript .="
					form.fbar.add(new Ext.Button({text: '".JText::_('CORE_UPDATE')."',
										handler: function()
						                {
						                	myMask.show();
						                 	var fields = new Array();
						                 	form.getForm().fieldInvalid =false;		
						        			form.cascade(function(cmp)
						        			{
						        			
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
						        			
						        			if (!form.getForm().fieldInvalid) 
					        				{	
												form.getForm().setValues({fieldsets: fieldsets});
							                 	form.getForm().setValues({task: 'updateMetadata'});
							                 	form.getForm().setValues({metadata_id: '".$metadata_id."'});
							                 	form.getForm().setValues({object_id: '".$object_id."'});
												form.getForm().setValues({account_id: '".$account_id."'});
												form.getForm().submit({
											    	scope: this,
													method	: 'POST',
													clientValidation: false,
													success: function(form, action) 
													{
														Ext.MessageBox.alert('".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TITLE')."', 
								                    						 '".JText::_('CATALOG_UPDATEMETADATA_MSG_SUCCESS_TEXT')."',
								                    						 function () {window.open ('./index.php?option=".$option."&Itemid=".JRequest::getVar('Itemid')."&lang=".JRequest::getVar('lang')."&task=cancelMetadata','_parent');});
													
														myMask.hide();
													},
													failure: function(form, action) 
													{
	                        							if (action.result)
															alert(action.result.errors.xml);
														else
															alert('Form update error');
															
														myMask.hide();
													},
													url:'".$update_url."'
												});
											}
											else{
													alert('Please verify whether all required fields are present');
													myMask.hide();
											}
							        	}})
							        );
						form.render();";
					}
										
					
					
					$this->javascript .="
						form.add(createHidden('option', 'option', '".$option."'));
						form.add(createHidden('task', 'task', ''));
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
	
	/**
	 * Enter description here ...
	 * @param unknown_type $database
	 * @param unknown_type $ancestor
	 * @param unknown_type $parent
	 * @param unknown_type $parentFieldset
	 * @param unknown_type $parentFieldsetName
	 * @param unknown_type $ancestorFieldsetName
	 * @param unknown_type $parentName
	 * @param unknown_type $xpathResults
	 * @param unknown_type $parentScope
	 * @param unknown_type $scope
	 * @param unknown_type $queryPath
	 * @param unknown_type $currentIsocode
	 * @param unknown_type $account_id
	 * @param unknown_type $profile_id
	 * @param unknown_type $option
	 */
	function buildTree($database, $ancestor, $parent, $parentFieldset, $parentFieldsetName, $ancestorFieldsetName, $parentName, $xpathResults, $parentScope, $scope, $queryPath, $currentIsocode, $account_id, $profile_id, $option,$parent_object,$isManager, $isEditor)
	{
		// On récupère dans des variables le scope respectivement pour le traitement des classes enfant et
		// pour le traitement des attributs enfants.
		// Cela permet d'éviter les effets de bord
						
		// Stockage du path pour atteindre ce noeud du XML
		$queryPath = $queryPath."/".$currentIsocode;
		
		// Construire la liste déroulante des périmètres prédéfinis si on est au bon endroit
		if ($this->catalogBoundaryIsocode == $currentIsocode AND count($this->boundaries_name) > 0)
		{
			$this->javascript .="
									var valueList = ".HTML_metadata::array2extjs($this->boundaries_name, true).";
								    var boundaries = ".HTML_metadata::array2json($this->boundaries).";
								    var paths = ".HTML_metadata::array2json($this->paths).";
								     // La liste
								     ".$parentFieldsetName.".add(createComboBox_Boundaries('".$parentName."_boundaries', '".addslashes(JText::_("BOUNDARIES"))."', false, '1', '1', valueList, '', false, '".html_Metadata::cleanText(JText::_("BOUNDARIES_TIP"))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."', boundaries, paths));
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
						 rel.editable as editable,
						 rel.editoraccessibility as editoraccessibility,
						 CONCAT(relation_namespace.prefix,':',rel.isocode) as rel_isocode, 
						 rel.relationtype_id as reltype_id, 
						 rel.classassociation_id as association_id,
						 a.guid as attribute_guid,
						 a.name as attribute_name, 
						 CONCAT(attribute_namespace.prefix,':',a.isocode) as attribute_isocode, 
						 CONCAT(list_namespace.prefix,':',a.type_isocode) as list_isocode, 
						 a.attributetype_id as attribute_type, 
						 tc.id as cl_stereotype_id,
						 a.default as attribute_default, 
						 a.pattern as attribute_pattern, 
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
						 LEFT OUTER JOIN #__sdi_sys_stereotype as t
						 	 ON a.attributetype_id = t.id 
					     LEFT OUTER JOIN #__sdi_class as c
					  		 ON rel.classchild_id=c.id
					  	 LEFT OUTER JOIN jos_sdi_sys_stereotype as tc
			 				ON c.stereotype_id = tc.id 
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
				  GROUP BY rel.id ORDER BY rel.ordering, rel.id";		
		$database->setQuery( $query );
		
		$rowChilds = array_merge( $rowChilds, $database->loadObjectList() );

		// Parcours des attributs enfants
		foreach($rowChilds as $child)
		{
			//La visibilité est héréditaire : mise à jour de la valeur du child courant en fonction du parent
			if(isset($parent_object) && $parent_object->editable > $child->editable)
				$child->editable = $parent_object->editable;
			
			//For editors, visibility can be downgrade by admin configuration
			if(!$isManager && $isEditor && isset($child->editoraccessibility) && $child->editoraccessibility > $child->editable )
				$child->editable = $child->editoraccessibility;
			
			//Traitement d'une relation vers un attribut
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
				
				// Le modéle de saisie
				$regex = addslashes($child->attribute_pattern);
				
				if ($regex == null)
					$regex = "";
				
				// Le message à afficher en cas d'erreur de saisie selon le modèle
				$regexmsg = JText::_(strtoupper($child->attribute_guid)."_REGEXMSG");
				if ($regexmsg == null or substr($regexmsg, -9, 9) == "_REGEXMSG")
				{
					$regexmsg = "";			
				}	
				
				// Mise en place des contrôles
				// Cas des champs systèmes qui doivent être désactivés
				$disabled = "false";
				if ($child->editable == 2 )
					$disabled = "true";
				
				// Cas des champs qui sont obligatoires
				$mandatory = "false";
				if ($child->rel_lowerbound > 0)
					$mandatory = "true";
	
				// Longueur max des champs
				$maxLength = 999;
				if ($child->length)
					$maxLength = $child->length;
				
				//Champs caché
				$hidden = "false";
				if ($child->editable == 3 )
					$hidden = "true";
				
				// On regarde dans le XML s'il contient la balise correspondante au code ISO de l'attribut enfant,
				// et combien de fois au niveau courant
				$mainNode = $xpathResults->query($child->attribute_isocode, $scope);
				$attributeCount = $mainNode->length;
				
				if ($child->attribute_type == 6 and $attributeCount > 1)
					$attributeCount = 1;
				
				//boucle si une occurence de l'attribut dans le XML
				// On n'entre dans cette boucle que si on a trouvé au moins une occurence de l'attribut dans le XML
				for ($pos=0; $pos<$attributeCount; $pos++)
				{
					/*
					 * COMPREHENSION DU MODELE
					 * La relation vers l'attribut n'a jamais de code ISO.
					 */  
					//echo "----------- On traite ".$child->attribute_isocode." [".$child->attribute_type."] -----------<br>";
				
					// Construction du master qui permet d'ajouter des occurences de la relation.
					// Le master contient les données de la première occurence.
					if ($pos==0)
					{	
						$attributeScope = $mainNode->item($pos);
						// Traitement de l'attribut enfant.
						
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
								$node = $xpathResults->query($type_isocode, $attributeScope);
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Si le xpathParentId est defini, regarder si on est au xpath souhaite.	
								if ($this->parentId_attribute <> "")
								{
									// Vérification qu'on est bien dans l'attribut choisi. La classe n'a pas d'utilité
									if ($parentScope <> NULL and $this->parentId_attribute == $child->attribute_id)
									{
										// Stocker le guid du parent
										$nodeValue = $this->parentGuid;
									}
								}
								
								// Selon le rendu de l'attribut, on fait des traitements différents
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
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								// Cas où le noeud n'existe pas dans le XML. Inutile de rechercher la valeur
								if ($parentScope <> NULL and $parentScope->nodeName == $scope->nodeName)
									$nodeValue="";
								else
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
								
								if($hidden == "false")
								{
									// Selon le rendu de l'attribut, on fait des traitements différents
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
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// Local
							case 3:
								// Traitement de la classe enfant
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
											 	
								if ($node->length>0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue="";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								$defaultVal = "";
								
								switch ($child->rendertype_id)
								{
									default:
										/* Traitement spécifique aux langues */
										$listNode = $xpathResults->query($child->attribute_isocode, $scope);
										$listCount = $listNode->length;

										for($pos=0;$pos<=$listCount; $pos++)
										{
											if ($pos==0)
											{	
												$currentScope=$listNode->item($pos);
												// Traitement de la multiplicité
												// Récupération du path du bloc de champs qui va être créé pour construire le nom
												$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
								
												$fieldsetName = "fieldset".$child->attribute_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
												// Créer un nouveau fieldset
												$this->javascript .="
													var ".$fieldsetName." = createFieldSet('".$LocName."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', true); 
													".$parentFieldsetName.".add(".$fieldsetName.");
												";
												
												foreach($this->langList as $row)
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													if ($row->defaultlang)
													{
														$langNode = $xpathResults->query("gco:CharacterString", $currentScope);
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														}
													}
													else
													{
														$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $currentScope);

														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														}
													}
													
													if($hidden == "false")
													{
														// Selon le rendu de l'attribut, on fait des traitements differents
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
													}
													if($hidden == "true" || $disabled == "true")
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
												// Traitement de la multiplicité
												// Récupération du path du bloc de champs qui va être créé pour construire le nom
												$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
												$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__".($pos+1);
								
												$this->javascript .="
												var master = Ext.getCmp('".$master."');						
												";
												
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
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														}
													}
													else
													{
														$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
														$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $currentScope);
														if ($langNode->length > 0)
															$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
														else
														{
															$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
															$nodeValue = html_Metadata::cleanText($database->loadResult());
															$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
														}
													}

													if($hidden == "false")
													{
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
													if($hidden == "true" || $disabled == "true")
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
											// Traitement de la multiplicité
											// Récupération du path du bloc de champs qui va être créé pour construire le nom
											$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__1";
											$LocName = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."__2";
							
											$this->javascript .="
											var master = Ext.getCmp('".$master."');						
											";
											
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
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
													}
												}
												else
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
													}
												}
			
												if($hidden == "false")
												{
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
												if($hidden == "true" || $disabled == "true")
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
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
								}
								if($hidden == "true" || $disabled == "true") 
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// Date
							case 5:
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
								{
									if($child->attribute_default == 'today')
										$nodeValue = html_Metadata::cleanText(date ('d.m.Y'));
									else
										$nodeValue = html_Metadata::cleanText($child->attribute_default);
								}
								
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								break;
							// List
							case 6:
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								$content = array();
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
								}
									
								// S'il n'y a pas de valeurs existantes, récupérer les valeurs par défaut
								$nodeDefaultValues = array();
								if (count($nodeValues) == 0)
								{
									// Elements sélectionnés par défaut
									$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id;
									$database->setQuery( $query );
									$selectedContent = $database->loadObjectList();
										
									// Construction de la liste
									foreach ($selectedContent as $cont)
									{
										$nodeValues[] = html_Metadata::cleanText($cont->value);
										$nodeDefaultValues[] = html_Metadata::cleanText($cont->value);
									}
								}
								
								if($hidden == "false")
								{
									// Selon le rendu de l'attribut, on fait des traitements différents
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
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}

								break;
							// Link
							case 7:
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Selon le rendu de l'attribut, on fait des traitements différents
								if($hidden == "false"){
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

								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// DateTime
							case 8:
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
								{
									if($child->attribute_default == 'today')
										$nodeValue = html_Metadata::cleanText(date ('d.m.Y'));
									else
										$nodeValue = html_Metadata::cleanText($child->attribute_default);
								}
			
								$nodeValue = substr($nodeValue, 0, 10);
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// TextChoice
							case 9:
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);

								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Traitement spécifique aux listes
								// Traitement des enfants de type list
								$content = array();
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
								$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									
								$language =& JFactory::getLanguage();
								
								$node = $xpathResults->query($type_isocode, $relNode->item(0));
								if ($node->length > 0)
								{
									// Chercher le titre associé au texte localisé souhaité, ou s'il n'y a pas de titre le contenu
									foreach ($content as $cont)
									{
										if ($cont->value == html_Metadata::cleanText($node->item(0)->nodeValue))
											$nodeValues[] = $cont->guid;
									}
								}
								else
									$nodeValues[] = "";
									
								
								$nodeDefaultValues = array();
								if (count($nodeValues) == 0)
								{
									// Elements sélectionnés par défaut
									$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
									$database->setQuery( $query );
									$selectedContent = $database->loadObjectList();
										
									// Construction de la liste
									foreach ($selectedContent as $cont)
									{
										$nodeValues[] = $cont->guid;
										$nodeDefaultValues[] = $cont->guid;
									}
								}
								
								$simple=true;
								if ($child->rel_lowerbound>0)
									$simple = false;
								
								if($hidden == "false")
								{
							 		$this->javascript .="
									var valueList = ".HTML_metadata::array2extjs($dataValues, $simple, true, true).";
								     var selectedValueList = ".HTML_metadata::array2json($nodeValues).";
								     var defaultValueList = ".HTML_metadata::array2json($nodeDefaultValues).";
								     ".$parentFieldsetName.".add(createChoiceBox('".$listName."', '".html_Metadata::cleanText(JText::_($label))."', ".$mandatory.", '".$child->rel_lowerbound."', '".$child->rel_upperbound."', valueList, selectedValueList, defaultValueList, ".$disabled.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".JText::_($this->mandatoryMsg)."'));
								    ";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// LocaleChoice
							case 10:
								// Traitement de la classe enfant
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
								else
									$nodeValue = "";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Traitement spécifique aux listes
								// Traitement des enfants de type list
								$content = array();
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
								$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									
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
											$result = $database->loadObject();
											$nodeValues[] = $result->guid;
										}
									}
								}
									
								$nodeDefaultValues = array();
								if (count($nodeValues) == 0)
								{
									// Elements selectionnes par defaut
									$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
									$database->setQuery( $query );
									$selectedContent = $database->loadObjectList();
										
									// Construction de la liste
									foreach ($selectedContent as $cont)
									{
										$nodeValues[] = $cont->guid;
										$nodeDefaultValues[] = $cont->guid;
									}
								}
									
								if (count($nodeValues) == 0)
									$nodeValues[] = "";
									
								
								if($hidden == "false")
								{
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
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								break;
							// Thesaurus GEMET
							case 11:
								$uri =& JUri::getInstance();
								$language =& JFactory::getLanguage();
								$userLang="";
								$defaultLang="";
								$langArray = Array();
								foreach($this->langList as $row)
								{									
									if ($row->defaultlang) // Langue par défaut de la métadonnée
										$defaultLang = $row->gemetlang;
									
									if ($row->code_easysdi == $language->_lang) // Langue courante de l'utilisateur
										$userLang = $row->gemetlang;
										
									$langArray[] = $row->gemetlang;
								}
								$value = "";
								$listNode = $xpathResults->query($child->attribute_isocode, $scope);
								$listCount = $listNode->length;		
								$nodeValues = array();
								for($keyPos=0;$keyPos<$listCount; $keyPos++)
								{
									$currentScope=$listNode->item($keyPos);
								
									if ($currentScope and $currentScope->nodeName <> "")
									{
										$nodeValue= "";
										$nodeKeyword= "";
										// Récupérer le texte localisé stocké
										foreach($this->langList as $row)
										{
											if ($row->defaultlang)
											{
												$keyNode = $xpathResults->query("gco:CharacterString", $currentScope);
												if ($keyNode->length > 0)
												{
													$nodeValue .= $row->gemetlang.": ".html_Metadata::cleanText($keyNode->item(0)->nodeValue).";";
												}
											}
											else
											{
												$keyNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $currentScope);
												if ($keyNode->length > 0)
													$nodeValue .= $row->gemetlang.": ".html_Metadata::cleanText($keyNode->item(0)->nodeValue).";";
												
											}
											if ($row->gemetlang == $userLang and $keyNode->item(0))
												$nodeKeyword = html_Metadata::cleanText($keyNode->item(0)->nodeValue);
										}
									}
									if ($nodeKeyword <> "")
										$nodeValues[] = "{keyword:'$nodeKeyword', value: '$nodeValue'}";
								}

								if (count($nodeValues)>0)
									$value = "[".implode(",",$nodeValues)."]";
								else
									$value = "[]";
								// Créer un bouton pour appeler la fenêtre de choix dans le Thesaurus GEMET
								$this->javascript .="
								var winthge;
								
								Ext.BLANK_IMAGE_URL = '".$uri->base(true)."/components/com_easysdi_catalog/ext/resources/images/default/s.gif';
								
								var thes = new ThesaurusReader({
																  id:'".$currentName."_PANEL_THESAURUS',
																  lang: '".$userLang."',
															      outputLangs: ".str_replace('"', "'", HTML_metadata::array2json($langArray)).", //['en', 'cs', 'fr', 'de'] 
															      separator: ' > ',
															      appPath: '".$uri->base(true)."/components/com_easysdi_catalog/js/',
															      returnPath: false,
															      returnInspire: true,
															      thesaurusUrl:thesaurusConfig,
															      width: 300, 
															      height:400,
															      win_title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_ALERT'))."',
															      layout: 'fit',
															      targetField: '".$currentName."',
															      proxy: '".$uri->base(true)."/components/com_easysdi_catalog/js/proxy.php?url=',
															      handler: function(result){
															      				var target = Ext.ComponentMgr.get(this.targetField);
															    				var s = '';
												      		    					
																      		    var reliableRecord = result.terms[thes.lang];
																      		    
																      		    // S'assurer que le mot-clé n'est pas déjé sélectionné
																      		    if (!target.usedRecords.containsKey(reliableRecord))
																				{
																					// Sauvegarde dans le champs SuperBoxSelect des mots-cles dans toutes les langues de EasySDI
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
										disabled:".$disabled.",
										handler: function()
								                {
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
										
								        // Champs spécifiques au clonage
								        dynamic:true,
								        minOccurs:1,
							            maxOccurs:1,
							            clone: false,
										clones_count: 1,
							            extendedTemplate: null
									})
								);
								
								// Créer le champ qui contiendra les mots-clés du thesaurus choisis
								".$parentFieldsetName.".add(createSuperBoxSelect('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."', ".$value.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ".$disabled."));
								";
								break;
							case 14:
								// Cas où le noeud n'existe pas dans le XML. Inutile de rechercher la valeur
								$node = $xpathResults->query("gmd:MI_Identifier/gmd:code/gco:CharacterString", $attributeScope);
								
								if ($parentScope <> NULL and $parentScope->nodeName == $scope->nodeName)
									$nodeValue="";
								else
									$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);

								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
								
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createStereotypeFileTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', false, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								break;
							default:
								// Traitement de la classe enfant
								$node = $xpathResults->query($type_isocode, $attributeScope);
											 	
								$nodeValue = html_Metadata::cleanText($node->item($pos)->nodeValue);
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								if($hidden == "false"){
									// Selon le rendu de l'attribut, on fait des traitements différents
									switch ($child->rendertype_id)
									{
										default:
											$this->javascript .="
											".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
											break;
									}
								}
								if($hidden == "true" || $disabled == "true")
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
						
						// Si on est en train de traiter un attribut de type liste, il faut encore récupérer
						// Le code ISO de la liste, sinon on récupère le code ISO du type d'attribut
						if ($child->attribute_type == 6 )
							$type_isocode = $child->list_isocode;
						else
							$type_isocode = $child->t_isocode;
	
						// Modifier le path d'accés à l'attribut
						$queryPath = $queryPath."/".$child->attribute_isocode."/".$type_isocode;
						
						// Construction du nom de l'attribut
						$master = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode)."__1";
						$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode)."__".($pos+1);
						$currentName = $name;
						
						if ($child->attribute_type <> 9 and $child->attribute_type <> 10 and $child->attribute_type <> 11)
						{
							// Traitement de l'attribut enfant
							$node = $xpathResults->query($type_isocode, $attributeScope);
							// Si le fieldset n'existe pas, inutile de récupérer une valeur
							if ($parentScope <> NULL and $parentScope->nodeName == $scope->nodeName)
								$nodeValue = "";
							else
								$nodeValue = html_Metadata::cleanText($node->item($pos-1)->nodeValue);
							
							// Récupération de la valeur par défaut, s'il y a lieu
							if ($child->attribute_default <> "" and $nodeValue == "")
							{
								if(( $child->attribute_type == 5 || $child->attribute_type == 8 ) && $child->attribute_default == 'today')
									$nodeValue = html_Metadata::cleanText(date ('d.m.Y'));
								else
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
							}
		
						}
						$this->javascript .="
						var master = Ext.getCmp('".$master."');						
						";
						
						// Traitement de chaque attribut selon son type
						switch($child->attribute_type)
						{
							// Guid
							case 1:
								// Selon le rendu de l'attribut, on fait des traitements differents
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
								if($hidden == "false"){
									// Selon le rendu de l'attribut, on fait des traitements différents
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
								}
								if($hidden == "true" || $disabled == "true")
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
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
							// Date
							case 5:
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
								}
								if($hidden == "true" || $disabled == "true")
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
								if($hidden == "false"){
									// Selon le rendu de l'attribut, on fait des traitements différents
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
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								break;
							// DateTime
							case 8:
								$nodeValue = substr($nodeValue, 0, 10);
								if($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
										".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
										";
								}
								break;
							// TextChoice
							case 9:
								// Traitement de la classe enfant
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
								else
									$nodeValue = "";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Traitement spécifique aux listes
								// Traitement des enfants de type list
								$content = array();
								$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
								$database->setQuery( $query );
								$content = $database->loadObjectList();
								
								$dataValues = array();
								$nodeValues = array();
									
								// Traitement de la multiplicité
								// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
									
								$relNode = $xpathResults->query($child->attribute_isocode, $scope);
								$language =& JFactory::getLanguage();
									
								$node = $xpathResults->query($type_isocode, $relNode->item(0));
								if ($node->length > 0)
								{
									// Chercher le titre associé au texte localisé souhaité et
									foreach ($content as $cont)
									{
										if ($cont->value == html_Metadata::cleanText($node->item(0)->nodeValue))
											$nodeValues[] = $cont->guid;
									}
								}
								else
									$nodeValues[] = "";
								
									
								$nodeDefaultValues = array();
								if (count($nodeValues) == 0)
								{
									// Elements sélectionnés par défaut
									$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
									$database->setQuery( $query );
									$selectedContent = $database->loadObjectList();
								
									// Construction de la liste
									foreach ($selectedContent as $cont)
									{
										$nodeValues[] = $cont->guid;
										$nodeDefaultValues[] = $cont->guid;
									}
								}
								
								if($hidden == "false"){
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
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								break;
							// LocaleChoice
							case 10:
								// Traitement de la classe enfant
								$node = $xpathResults->query("gco:CharacterString", $attributeScope);
								
								if ($node->length >0)
									$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
								else
									$nodeValue = "";
									
								// Récupération de la valeur par défaut, s'il y a lieu
								if ($child->attribute_default <> "" and $nodeValue == "")
									$nodeValue = html_Metadata::cleanText($child->attribute_default);
			
								// Traitement spécifique aux listes
								// Traitement des enfants de type list
								$content = array();
								$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
								$database->setQuery( $query );
								$content = $database->loadObjectList();
								
								$dataValues = array();
								$nodeValues = array();
									
								// Traitement de la multiplicité
								// Récupération du path du bloc de champs qui va être créé pour construire le nom
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
									
								$relNode = $xpathResults->query($child->attribute_isocode, $scope);
								
								$language =& JFactory::getLanguage();
									
								// Récupérer le texte localisé stocké
								foreach($this->langList as $row)
								{
									if ($row->code_easysdi == $language->_lang)
									{
										if ($row->defaultlang)
											$node = $xpathResults->query("gco:CharacterString", $attributeScope);
										else
											$node = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
											
										if ($node->length > 0)
										{
											// Chercher le titre associe au texte localise souhaite, ou s'il n'y a pas de titre le contenu
											$query = "SELECT t.title, t.content, c.guid FROM #__sdi_codevalue c, #__sdi_translation t, #__sdi_language l, #__sdi_list_codelang cl WHERE c.guid=t.element_guid AND t.language_id=l.id AND l.codelang_id=cl.id and cl.code='".$language->_lang."' AND t.content = '".html_Metadata::cleanText($node->item(0)->nodeValue)."'"." ORDER BY c.ordering";
											$database->setQuery( $query );
											$result = $database->loadObject();
											$nodeValues[] = $result->guid;
										}
									}
								}
									
								$nodeDefaultValues = array();
								if (count($nodeValues) == 0)
								{
									// Elements selectionnes par defaut
									$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
									$database->setQuery( $query );
									//echo $database->getQuery()."<br>";
									$selectedContent = $database->loadObjectList();
								
									// Construction de la liste
									foreach ($selectedContent as $cont)
									{
										$nodeValues[] = $cont->guid;
										$nodeDefaultValues[] = $cont->guid;
									}
								}
								
								
								if (count($nodeValues) == 0)
									$nodeValues[] = "";
								
								if($hidden == "false"){
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
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								break;
							// Thesaurus GEMET
							case 11:
								// Le Thesaurus GEMET  n'existe qu'en un exemplaire
								break;
							case 14:
								//echo "Recherche de gco:CharacterString dans ".$attributeScope->nodeName."<br>";
								$node = $xpathResults->query("gmd:MI_Identifier/gmd:code/gco:CharacterString", $attributeScope);
								$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
								
								if ($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createStereotypeFileTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', false, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								break;

							default:
								if ($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", true, master, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
								}								
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								
								break;
						}
					}
				}
				
				//Attribut n'existe pas dans le XML
				// Si le XML ne contient aucune occurence de l'attribut
				// il faut le créer vide
				if ($attributeCount==0)
				{
					$nodeValue = "";
						
					// Positionner le code ISO de l'attribut
					$attributeScope = $scope;
						
					if ($child->attribute_type == 6 )
						$type_isocode = $child->list_isocode;
					else
						$type_isocode = $child->t_isocode;
	
					$queryPath = $queryPath."/".$type_isocode;
					$name = $parentName."-".str_replace(":", "_", $child->attribute_isocode)."-".str_replace(":", "_", $type_isocode);
					$currentName = $name."__1";

					// Récupération de la valeur par défaut, s'il y a lieu
					if ($child->attribute_default <> "" )
					{
						if(( $child->attribute_type == 5 || $child->attribute_type == 8 ) && $child->attribute_default == 'today')
							$nodeValue = html_Metadata::cleanText(date ('d.m.Y'));
						else
							$nodeValue = html_Metadata::cleanText($child->attribute_default);
					}
					
					// Traitement de chaque attribut selon son type
					switch($child->attribute_type)
					{
						// Guid
						case 1:
							// Selon le rendu de l'attribut, on fait des traitements differents
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
							if ($hidden == "false")
							{
								// Selon le rendu de l'attribut, on fait des traitements differents
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
							}
							if($hidden == "true" || $disabled == "true")
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
									/* Traitement specifique aux langues */
									
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
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue;//html_Metadata::cleanText($nodeValue);
													}
												}
												else
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue;//html_Metadata::cleanText($nodeValue);
														//$nodeValue = "";
													}
												}
												
												if($hidden == "false"){
													// Selon le rendu de l'attribut, on fait des traitements differents
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
												}
												if($hidden == "true" || $disabled == "true")
												{
													$this->javascript .="
													".$parentFieldsetName.".add(createHidden('".$LocLangName."_hiddenVal', '".$LocLangName."_hiddenVal', '".$nodeValue."'));
													";
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
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
													}
												}
												else
												{
													$LocLangName = $LocName."-gmd_LocalisedCharacterString-".$row->code_easysdi."__1";
													$langNode = $xpathResults->query("gmd:PT_FreeText/gmd:textGroup/gmd:LocalisedCharacterString"."[@locale='#".$row->code."']", $attributeScope);
													if ($langNode->length > 0)
														$nodeValue = html_Metadata::cleanText($langNode->item(0)->nodeValue);
													else
													{
														$database->setQuery("SELECT defaultvalue FROM #__sdi_translation WHERE element_guid='".$child->attribute_guid."' AND language_id=".$row->id);
														$nodeValue = html_Metadata::cleanText($database->loadResult());
														$defaultVal= $nodeValue; //html_Metadata::cleanText($nodeValue);
													}
												}
												
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
						// Number
						case 4:
							//If relation is hidden
							if ($hidden == "false")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createNumberField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', true, 3, ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
							}
							if($hidden == "true" || $disabled == "true")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// Date
						case 5:
							if ($hidden == "false")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
							}
							if($hidden == "true" || $disabled == "true")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							break;
						// List
						case 6:
							// Traitement spécifique aux listes
							// Traitement des enfants de type list
							$content = array();
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
							
							$relNode = $xpathResults->query($child->attribute_isocode, $attributeScope);
								
							for ($pos=0;$pos<$relNode->length;$pos++)
							{
							$listNode = $xpathResults->query($child->list_isocode, $relNode->item($pos));
							if ($listNode->length > 0)
								if ($child->codeList <> null)
									$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->getAttribute('codeListValue'));
								else
									$nodeValues[]=html_Metadata::cleanText($listNode->item(0)->nodeValue);
							}
								
							// S'il n'y a pas de valeurs existantes, récupérer les valeurs par défaut
							$nodeDefaultValues = array();
							if (count($nodeValues) == 0)
							{
								// Elements sélectionnés par défaut
								$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id;
								$database->setQuery( $query );
								$selectedContent = $database->loadObjectList();
							
								// Construction de la liste
								foreach ($selectedContent as $cont)
								{
									$nodeValues[] = html_Metadata::cleanText($cont->value);
									$nodeDefaultValues[] = html_Metadata::cleanText($cont->value);
								}
							}
							
							if ($hidden == "true")
							{
								$this->javascript .="
								var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
								".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
								";
								break;
							}
							// Selon le rendu de l'attribut, on fait des traitements différents
							switch ($child->rendertype_id)
							{
								default:
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
					
							if($disabled == "true")
							{
								$this->javascript .="
								 var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
								".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
								";
							}
							break;
						// Link
						case 7:
							if ($hidden == "false")
							{
								// Selon le rendu de l'attribut, on fait des traitements differents
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
							}
							if($hidden == "true" || $disabled == "true")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// DateTime
						case 8:
							$nodeValue = substr($nodeValue, 0, 10);
								
							if ($hidden == "false")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createDateField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
							}
							if($hidden == "true" || $disabled == "true")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
						// TextChoice
						case 9:
							// Traitement specifique aux listes
							//echo $ancestorFieldsetName." - ".$parentName." - ".$child->attribute_isocode. " (1)<br>";					
							// Traitement des enfants de type list
							$content = array();
							//$query = "SELECT c.*, rel.* FROM #__easysdi_metadata_classes c, #__easysdi_metadata_classes_classes rel WHERE rel.classes_to_id = c.id and c.type='list' and rel.classes_from_id=".$parent." and (c.partner_id=0 or c.partner_id=".$account_id.") ORDER BY c.ordering";
							$query = "SELECT * FROM #__sdi_codevalue WHERE published=true AND attribute_id = ".$child->attribute_id." ORDER BY ordering";
							$database->setQuery( $query );
							$content = $database->loadObjectList();

						 	$dataValues = array();
						 	$nodeValues = array();
					
						 	// Traitement de la multiplicite
						 	// Recuperation du path du bloc de champs qui va etre cree pour construire le nom
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
						 	
							// Elements selectionnes par defaut
							$nodeDefaultValues = array();
							$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
							$database->setQuery( $query );
							//echo $database->getQuery()."<br>";
							$selectedContent = $database->loadObjectList();
							
						 	// Construction de la liste
						 	foreach ($selectedContent as $cont)
						 	{
						 		$nodeValues[] = $cont->guid;
								$nodeDefaultValues[] = $cont->guid;
						 		
					 		}
							if ($hidden == "true")
							{
								$this->javascript .="
								 var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
								".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
								";
								break;
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
							
							if($disabled == "true")
							{
								$this->javascript .="
								 var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
								".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
								";
							}
								
							break;
							// LocaleChoice
							case 10:
								// Traitement spécifique aux listes
								// Traitement des enfants de type list
								$content = array();
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
								$relNode = $xpathResults->query($child->attribute_isocode, $scope);
									
								// Elements sélectionnés par défaut
								$nodeDefaultValues = array();
								$query = "SELECT c.* FROM #__sdi_codevalue c, #__sdi_defaultvalue d WHERE c.id=d.codevalue_id AND c.published=true AND d.attribute_id = ".$child->attribute_id." ORDER BY c.ordering";
								$database->setQuery( $query );
								$selectedContent = $database->loadObjectList();
								
								// Construction de la liste
								foreach ($selectedContent as $cont)
								{
									$nodeValues[] = $cont->guid;
									$nodeDefaultValues[] = $cont->guid;
								
								}
								if ($hidden == "true")
								{
									$this->javascript .="
									var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
									break;
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
			
								
								if($disabled == "true")
								{
									$this->javascript .="
									 var defaultValueList = ".HTML_metadata::array2json($nodeValues).";
									".$parentFieldsetName.".add(createHidden('".$listName."_hiddenVal', '".$listName."_hiddenVal', defaultValueList));
									";
								}
								
								break;
							// Thesaurus GEMET
							case 11:
								$uri =& JUri::getInstance();
								$language =& JFactory::getLanguage();
								$userLang="";
								$defaultLang="";
								$langArray = Array();
								foreach($this->langList as $row)
								{									
									if ($row->defaultlang) // Langue par defaut de la metadonnee
										$defaultLang = $row->gemetlang;
									
									if ($row->code_easysdi == $language->_lang) // Langue courante de l'utilisateur
										$userLang = $row->gemetlang;
										
									$langArray[] = $row->gemetlang;
								}
								$this->javascript .="
								// Créer un bouton pour appeler la fenêtre de choix dans le Thesaurus GEMET
								var winthge;
								
								Ext.BLANK_IMAGE_URL = '".$uri->base(true)."/components/com_easysdi_catalog/ext/resources/images/default/s.gif';
								
								var thes = new ThesaurusReader({
																  id:'".$currentName."_PANEL_THESAURUS',
																  lang: '".$userLang."',
															      outputLangs: ".str_replace('"', "'", HTML_metadata::array2json($langArray)).", //['en', 'cs', 'fr', 'de'] 
															      separator: ' > ',
															      appPath: '".$uri->base(true)."/components/com_easysdi_catalog/js/',
															      returnPath: false,
															      returnInspire: true,
															      thesaurusUrl:thesaurusConfig,
															      width: 300, 
															      height:400,
															      win_title:'".html_Metadata::cleanText(JText::_('CATALOG_METADATA_THESAURUSGEMET_ALERT'))."',
															      layout: 'fit',
															      targetField: '".$currentName."',
															      proxy: '".$uri->base(true)."/components/com_easysdi_catalog/js/proxy.php?url=',
															      handler: function(result){
															      				var target = Ext.ComponentMgr.get(this.targetField);
															    				var s = '';
												      		    					    
																      		    var reliableRecord = result.terms[thes.lang];
																      		    
																      		    // S'assurer que le mot-cle n'est pas deje selectionne
																      		    if (!target.usedRecords.containsKey(reliableRecord))
																				{
																					// Sauvegarde dans le champs SuperBoxSelect des mots-cles dans toutes les langues de EasySDI
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
										disabled:".$disabled.",
										handler: function()
								                {
								                	// Créer une iframe pour demander à l'utilisateur le type d'import
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
										 dynamic:true,
								        minOccurs:1,
							            maxOccurs:1,
							            clone: false,
										clones_count: 1,
							            extendedTemplate: null
									})
								);
								
								// Créer le champ qui contiendra les mots-clés du thesaurus choisis
								".$parentFieldsetName.".add(createSuperBoxSelect('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."', '', false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."',".$disabled."));
								";
								
								break;
							case 14:
								$node = $xpathResults->query("gmd:MI_Identifier/gmd:code/gco:CharacterString", $attributeScope);
								$nodeValue = html_Metadata::cleanText($node->item(0)->nodeValue);
								
								if ($hidden == "false")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createStereotypeFileTextField('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', false, '".$maxLength."', '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));
									";
								}
								if($hidden == "true" || $disabled == "true")
								{
									$this->javascript .="
									".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
									";
								}
								break;
						default:
							if ($hidden == "false")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createTextArea('".$currentName."', '".html_Metadata::cleanText(JText::_($label))."',".$mandatory.", false, null, '".$child->rel_lowerbound."', '".$child->rel_upperbound."', '".$nodeValue."', '".html_Metadata::cleanText($child->attribute_default)."', ".$disabled.", ".$maxLength.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '".$regex."', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', '".html_Metadata::cleanText(JText::_($regexmsg))."'));";
							}
							if($hidden == "true" || $disabled == "true")
							{
								$this->javascript .="
								".$parentFieldsetName.".add(createHidden('".$currentName."_hiddenVal', '".$currentName."_hiddenVal', '".$nodeValue."'));
								";
							}
							
							break;
					}
				}
			}
		//Traitement d'une relation vers une classe
		// Récupération des relations de cette classe (parent) vers d'autres classes (enfants)
		// Traitement d'une relation vers une classe
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
			
			// On regarde dans le XML s'il contient la balise correspondante au code ISO de la relation,
			// et combien de fois au niveau courant
			$node = $xpathResults->query($child->rel_isocode."/".$child->child_isocode, $scope);
			$relCount = $node->length;
			
			// Traitement de chaque occurence de la relation dans le XML.
			// Si pas trouvé, on entre une fois dans la boucle pour créer
			// une seule occurence de saisie (master)
			for ($pos=0; $pos<=$relCount; $pos++)//for ($pos=0; $pos<=$relCount; $pos++)
			{
				//COMPREHENSION DU MODELE : C'est la relation qui est multiple. 
				//De ce fait on a toujours un et un seul enfant pour chaque relation trouvée.
				
				// Construction du master qui permet d'ajouter des occurences de la relation.
				// Le master doit contenir une structure mais pas de données.
				if ($pos==0)//$pos==0
				{
					// Traitement de la relation entre la classe parent et la classe enfant
					// S'il y a au moins une occurence de la relation dans le XML, on change le scope
					if ($relCount > 0)
						$relScope = $node->item($pos);
					else
						$relScope = $scope;
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// Récupération du noeud XML correspondant au code ISO de la relation
						$childnode = $xpathResults->query($child->child_isocode, $relScope);
			
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

					// Le code ISO de la classe enfant devient le code ISO du nouveau parent
					$nextIsocode = $child->child_isocode;
				
					//stereotype case 1 :  on créer le master = la structure sans données
					if ($child->cl_stereotype_id <> null)
					{
						//Tester le stereotype pour créer le bon
						$database->setQuery("SELECT alias FROM #__sdi_sys_stereotype WHERE id =".$child->cl_stereotype_id);
						$stereotype = $database->loadResult();
						switch ($stereotype){
							case "geographicextent":
								//Attention : l'ensemble des classe stereotypée liées à ce noeud parent vont
								//être traité lors du premier, et unique, appel au classstereotype_builder.
								//Ce traitement particulier est dû à la gestion particulière de la cardinalité de la
								//relation vers une classe stereotypée geographicExtent (utilisation d'un ItemSelector)
								if($pos == 0){
									// Créer un nouveau fieldset
									if($child->editable == 3){
										$this->javascript .="
										var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."',false, true, '".JText::_( 'CATALOG_STEREOTYPE_CLASS_MAP_PANEL_LABEL' )."');
										".$parentFieldsetName.".add(".$fieldsetName.");
										";
									}
									else
									{
										$this->javascript .="
										var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."',false, true, '".JText::_( 'CATALOG_STEREOTYPE_CLASS_MAP_PANEL_LABEL' )."');
										".$parentFieldsetName.".add(".$fieldsetName.");
										";
									}
									HTML_classstereotype_builder::getGeographicExtentClass($database,$name, $child, $fieldsetName, $xpathResults, $queryPath,$scope, true, false,$child->editable );
								}
						}
					}
					else
					{
						// Créer un nouveau fieldset
						if($child->editable == 3)
						{
							// Fieldset caché
							$this->javascript .="
							var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
							".$parentFieldsetName.".add(".$fieldsetName.");
							";
						}
						else if($child->editable == 2)
						{
							// Fieldset désactivé avec cardinalité forcée pour désactiver les bouttons plus et minus
							$this->javascript .="
							var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
							".$parentFieldsetName.".add(".$fieldsetName.");
							";
						}
						else 
						{
							// Fieldset actif
							$this->javascript .="
							var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
							".$parentFieldsetName.".add(".$fieldsetName.");
							";
						}	
						
					}
					// Test pour le cas d'une relation qui boucle une classe sur elle-même
					if ($ancestor <> $parent)					
						HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option,$child,$isManager, $isEditor);
		
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
					}
				}//end - //$pos==0
				// Ici on va traiter toutes les occurences trouvées dans le XML
				else //else !($pos==0)
				{
					// Traitement de la relation entre la classe parent et la classe enfant
					$relScope = $node->item($pos-1);
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// Récupération du noeud XML correspondant au code ISO de la relation
						//echo "Recherche de ".$child->child_isocode. " dans ".$relScope->nodeName."<br>";
						$childnode = $xpathResults->query($child->child_isocode, $relScope);
			
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
					
					// Le code ISO de la classe enfant devient le code ISO du nouveau parent
					$nextIsocode = $child->child_isocode;
					
					//TODO : stereotype case 2 : traitement des occurences trouvées dans le XML
					if ($child->cl_stereotype_id <> null)
					{
						//Tester le stereotype pour créer le bon
						$database->setQuery("SELECT alias FROM #__sdi_sys_stereotype WHERE id =".$child->cl_stereotype_id);
						$stereotype = $database->loadResult();
						switch ($stereotype){
							case "geographicextent":
								//Attention : l'ensemble des classe stereotypée liées à ce noeud parent vont
								//être traité lors du premier, et unique, appel au classstereotype_builder.
								//Ce traitement particulier est dû à la gestion particulière de la cardinalité de la
								//relation vers une classe stereotypée geographicExtent (utilisation d'un ItemSelector)
								if($pos == 1){
									// Créer un nouveau fieldset
									if($child->editable == 3)
									{
										$this->javascript .="
										var master = Ext.getCmp('".$master."');
										var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false,true, '".JText::_( 'CATALOG_STEREOTYPE_CLASS_MAP_PANEL_LABEL' )."');
										".$parentFieldsetName.".add(".$fieldsetName.");
										";
									}
									else 
									{
										$this->javascript .="
										var master = Ext.getCmp('".$master."');
										var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false,true, '".JText::_( 'CATALOG_STEREOTYPE_CLASS_MAP_PANEL_LABEL' )."');
										".$parentFieldsetName.".add(".$fieldsetName.");
										";
									}										
									HTML_classstereotype_builder::getGeographicExtentClass($database,$name, $child, $fieldsetName, $xpathResults, $queryPath,$scope, false, true,$child->editable);
								}
						}
					}
					else
					{
						// Créer un nouveau fieldset
						if($child->editable == 3)
						{
							// Fieldset caché
							$this->javascript .="
							var master = Ext.getCmp('".$master."');
							var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
							".$parentFieldsetName.".add(".$fieldsetName.");
							";
						}
						else if($child->editable == 2)
						{
							// Fieldset désactivé avec cardinalité forcée pour désactiver les bouttons plus et minus
							$this->javascript .="
							var master = Ext.getCmp('".$master."');
							var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
							".$parentFieldsetName.".add(".$fieldsetName.");
							";
						}
						else
						{
							// Fieldset actif
							$this->javascript .="
							var master = Ext.getCmp('".$master."');
							var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
							".$parentFieldsetName.".add(".$fieldsetName.");
							";
						}
					}
					
					// Appel récursif de la fonction pour le traitement du prochain niveau
					HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
					
					// Classassociation_id contient une classe
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
					}
				}//end - else !($pos==0)
			}// end - for ($pos=0; $pos<=$relCount; $pos++)
	
			// Si la classe est obligatoire mais qu'elle n'existe pas à l'heure actuelle dans le XML, 
			// il faut créer en plus du master un bloc de saisie qui ne puisse pas être supprimé par l'utilisateur 
			//OU
			//Si la classe enfant est de type geographic extent
			if (($relCount==0 and $child->rel_lowerbound>0) || ($child->cl_stereotype_id <> null && $relCount==0))
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

				// Le code ISO de la classe enfant devient le code ISO du nouveau parent
				$nextIsocode = $child->child_isocode;
					
				//TODO : stereotype case 3 : occurence obligatoire mais pas présente dans le XML donc création d'une occurence en plus du master
				if ($child->cl_stereotype_id <> null)
				{
					//Tester le stereotype pour créer le bon
					$database->setQuery("SELECT alias FROM #__sdi_sys_stereotype WHERE id =".$child->cl_stereotype_id);
					$stereotype = $database->loadResult();
					switch ($stereotype){
						case "geographicextent":
							// Créer un nouveau fieldset
							if($child->editable == 3)
							{
								$this->javascript .="
								var master = Ext.getCmp('".$master."');
								var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1,1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."',false, true,'".JText::_( 'CATALOG_STEREOTYPE_CLASS_MAP_PANEL_LABEL' )."');
								".$parentFieldsetName.".add(".$fieldsetName.");
								";
							}
							else
							{
								$this->javascript .="
								var master = Ext.getCmp('".$master."');
								var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1,1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."',false, true,'".JText::_( 'CATALOG_STEREOTYPE_CLASS_MAP_PANEL_LABEL' )."');
								".$parentFieldsetName.".add(".$fieldsetName.");
								";
							}
								
							HTML_classstereotype_builder::getGeographicExtentClass($database,$name, $child, $fieldsetName, $xpathResults, $queryPath,$scope, false, true, $child->editable);
					}
				}
				else
				{
					// Créer un nouveau fieldset
					if($child->editable == 3)
					{
						//Fieldset caché
						$this->javascript .="
						var master = Ext.getCmp('".$master."');
						var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
						".$parentFieldsetName.".add(".$fieldsetName.");
						";
					}
					else if($child->editable == 2)
					{
						// Fieldset désactivé avec cardinalité forcée pour désactiver les bouttons plus et minus
						$this->javascript .="
						var master = Ext.getCmp('".$master."');
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
						".$parentFieldsetName.".add(".$fieldsetName.");
						";
					}
					else
					{
						// Fieldset actif
						$this->javascript .="
						var master = Ext.getCmp('".$master."');
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
						".$parentFieldsetName.".add(".$fieldsetName.");
						";
					}
				}
				
				// Appel récursif de la fonction pour le traitement du prochain niveau
				if ($ancestor <> $parent)
					HTML_metadata::buildTree($database, $parent, $child->child_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
					
				// Classassociation_id contient une classe
				if ($child->association_id <>0)
				{
					// Appel récursif de la fonction pour le traitement du prochain niveau
					if ($ancestor <> $parent)
						HTML_metadata::buildTree($database, $parent, $child->association_id, $child->child_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
				}
			}
		}
		
		// Traitement d'une relation vers un type d'objet
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
			// Si pas trouvé, on entre une fois dans la boucle pour créer
			// une seule occurence de saisie (master)
			//echo $child->rel_lowerbound. " - ".$child->rel_upperbound."<br>";
			for ($pos=0; $pos<=$relCount; $pos++)
			{
				// Construction du master qui permet d'ajouter des occurences de la relation.
				// Le master doit contenir une structure mais pas de données.
				if ($pos==0)
				{
					$guid = "";
					$results = array();
					$results = HTML_metadata::array2json($results);
						
					// On construit le nom de l'occurence qui a forcement l'index 2
					$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
					
					// Traitement de la relation entre la classe parent et la classe enfant
					// S'il y a au moins une occurence de la relation dans le XML, on change le scope
					if ($relCount > 0)
						$relScope = $node->item($pos);
					else
						$relScope = $scope;
						
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// Récupération du noeud XML correspondant au code ISO de la relation
						$childnode = $xpathResults->query($child->rel_isocode, $relScope);
			
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
					
					// Créer un nouveau fieldset
					if($child->editable == 3)
					{
						// Fieldset caché
						$this->javascript .="
						var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
						".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					}
					else if ($child->editable == 2)
					{
						$this->javascript .="
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", true, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					}
					else
					{
						$this->javascript .="
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, false, false, true, true, null, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false);
						".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					}
						
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
					}
				}
				else
				{
					$guid = substr($node->item($pos-1)->attributes->getNamedItem('href')->value, strpos($node->item($pos-1)->attributes->getNamedItem('href')->value, "&id=") + strlen("&id=") , 36);
					
					// Récupérer tous les objets du type d'objet lié dont le nom comporte le searchPattern
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
					
					$results = HTML_metadata::array2json($results);
			
					// Construction du nom du fieldset qui va correspondre à la classe
					// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
					// On récupère le master qui a l'index 1
					$master = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
					// On construit le nom de l'occurence qui a forcèment l'index 2
					$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__".($pos+1);
				
					// Traitement de la relation entre la classe parent et la classe enfant
					$relScope = $node->item($pos-1);
					
					// Traitement de la classe enfant
					if ($relCount > 0)
					{					
						// Récupération du noeud XML correspondant au code ISO de la relation
						$childnode = $xpathResults->query($child->rel_isocode, $relScope);
			
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
					
					// Créer un nouveau fieldset
					if($child->editable == 3)
					{
						// Fieldset caché
						$this->javascript .="
						var master = Ext.getCmp('".$master."');							
						var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					}
					else if ($child->editable == 2)
					{
						$this->javascript .="
						var master = Ext.getCmp('".$master."');							
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", true, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					}
					else
					{
						$this->javascript .="
						var master = Ext.getCmp('".$master."');							
						var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
						".$parentFieldsetName.".add(".$fieldsetName.");
						".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', ".$results.", false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
						";
					}
					
					// Classassociation_id contient une classe
					$nextIsocode = $child->rel_isocode;
					if ($child->association_id <>0)
					{
						// Appel récursif de la fonction pour le traitement du prochain niveau
						if ($ancestor <> $parent)
							HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
					}
				}
			}
			
			// Si l'objet est obligatoire mais qu'il n'existe pas à l'heure actuelle dans le XML, 
			// il faut créer en plus du master un bloc de saisie qui ne puisse pas être supprimé par l'utilisateur 
			if ($relCount==0 and $child->rel_lowerbound>0)
			{
				$guid = "";
				$results = array();
				$results = HTML_metadata::array2json($results);
			
				// Construction du nom du fieldset qui va correspondre à la classe
				// On n'y met pas la relation qui n'a pas d'intérêt pour l'unicité du nom
				// On récupère le master qui a l'index 1
				$master = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__1";
				// On construit le nom de l'occurence qui a forcèment l'index 2
				$name = $parentName."-".str_replace(":", "_", $child->rel_isocode)."__2";
			
				// Le scope reste le même, il n'aura de toute façon plus d'utilité pour les enfants
				// puisqu'à partir de ce niveau plus rien n'existe dans le XML	
				$classScope = $scope;
					
				// Construction du fieldset
				$fieldsetName = "fieldset".$child->rel_id."_".str_replace("-", "_", helper_easysdi::getUniqueId());
				$searchFieldName = $name."-"."SEARCH__1"; //2";

				// Créer un nouveau fieldset
				if($child->editable == 3)
				{
					// Fieldset caché
					$this->javascript .="
					var master = Ext.getCmp('".$master."');							
					var ".$fieldsetName." = createFieldSetHidden('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
					".$parentFieldsetName.".add(".$fieldsetName.");
					".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', '', false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
					";
				}
				else if ($child->editable == 2)
				{
					$this->javascript .="
					var master = Ext.getCmp('".$master."');							
					var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, 1, 1, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
					".$parentFieldsetName.".add(".$fieldsetName.");
					".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', '', true, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
					";
				}
				else
				{
					$this->javascript .="
					var master = Ext.getCmp('".$master."');							
					var ".$fieldsetName." = createFieldSet('".$name."', '".html_Metadata::cleanText($label)."', true, true, true, true, true, master, ".$child->rel_lowerbound.", ".$child->rel_upperbound.", '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', false); 
					".$parentFieldsetName.".add(".$fieldsetName.");
					".$fieldsetName.".add(createSearchField('".$searchFieldName."', '".$child->objecttype_id."', '".html_Metadata::cleanText(JText::_('SEARCH'))."',true, false, null, '1', '1', '', false, 0, '".html_Metadata::cleanText(JText::_($tip))."', '".$this->qTipDismissDelay."', '', '".html_Metadata::cleanText(JText::_($this->mandatoryMsg))."', ''));
					";
				}
				
				// Classassociation_id contient une classe
				$nextIsocode = $child->rel_isocode;
				if ($child->association_id <>0)
				{
					// Appel récursif de la fonction pour le traitement du prochain niveau
					if ($ancestor <> $parent)
						HTML_metadata::buildTree($database, $parent, $child->association_id, $child->objecttype_id, $fieldsetName, $parentFieldsetName, $name, $xpathResults, $scope, $classScope, $queryPath, $nextIsocode, $account_id, $profile_id, $option, $child,$isManager, $isEditor);
				}
			}
		}
		//Fin de Traitement d'une relation vers type d'objet
		}
	}
	
	
	/**
	 * cleanText
	 * Enter description here ...
	 * @param unknown_type $text
	 * @return mixed
	 */
	function cleanText($text)
	{
		$text = addslashes($text);
		$text = str_replace(chr(13),"\\r",$text);
		$text = str_replace(chr(10),"\\n",$text);
		return $text;
	}

	/**
	 * array2extjs
	 * Enter description here ...
	 * @param unknown_type $arr
	 * @param unknown_type $simple
	 * @param unknown_type $multi
	 * @param unknown_type $textlist
	 * @return mixed
	 */
	function array2extjs($arr, $simple, $multi = false, $textlist = false) {
		
		$extjsArray = "";

		if ($simple)
		{
			// Entree vide
			$extjsArray .= "['', ''], ";
		}
		$id=0;
		foreach($arr as $key=>$value)
		{
			$extjsArray .= "[";
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
	
	/**
	 * array2checkbox
	 * Enter description here ...
	 * @param unknown_type $id
	 * @param unknown_type $radio
	 * @param unknown_type $arr
	 * @param unknown_type $selected
	 * @param unknown_type $tip
	 * @return mixed
	 */
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
	
	/**
	 * array2json
	 * Enter description here ...
	 * @param unknown_type $arr
	 * @return string
	 */
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
				// :TO DO: Is there any more datatype we should be in the lookout for? (Object?)

				$parts[] = $str;
			}
		}
		$json = implode(',',$parts);

		if($is_list)
			return '[' . $json . ']';//Return numerical JSON

		$return = '[' . $json . ']';
		return $return;//Return associative JSON
	}
	
	/**
	 * buildTBar
	 * Enter description here ...
	 * @param unknown_type $isPublished
	 * @param unknown_type $isValidated
	 * @param unknown_type $object_id
	 * @param unknown_type $metadata_id
	 * @param unknown_type $account_id
	 * @param unknown_type $metadata_collapse
	 * @param unknown_type $option
	 * @return string
	 */
	function buildTBar($isPublished, $isValidated, $object_id, $metadata_id, $account_id, $metadata_collapse, $option)
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
				                	// Creer une iframe pour demander e l'utilisateur le type d'import
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
														         id:'serviceversion', 
														         xtype: 'hidden',
														         value:'".html_Metadata::cleanText($importref->serviceversion)."' 
														       },
														       { 
														         id:'outputschema', 
														         xtype: 'hidden',
														         value:'".html_Metadata::cleanText($importref->outputschema)."' 
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
					// Créer une iframe pour demander à l'utilisateur le type d'import
					$menu .= "handler: function()
				                {
				                	
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
														         id:'serviceversion', 
														         xtype: 'hidden',
														         value:'".html_Metadata::cleanText($importref->serviceversion)."' 
														       },
														       { 
														         id:'outputschema', 
														         xtype: 'hidden',
														         value:'".html_Metadata::cleanText($importref->outputschema)."' 
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
		// Boutons de replication
		if (!$isPublished)
		{
			$replicate_url = 'index.php?option='.$option.'&task=replicateMetadata';
		
			// Replication de metadonnee
			$objecttypes = array();
			$listObjecttypes = array();
			$database->setQuery( "SELECT id as value, name as text FROM #__sdi_objecttype WHERE predefined=0 ORDER BY name" );
			$objecttypes= array_merge( $objecttypes, $database->loadObjectList() );
			foreach($objecttypes as $ot)
			{
				$listObjecttypes[$ot->value] = $ot->text;
			}
			$listObjecttypes = HTML_metadata::array2extjs($listObjecttypes, true);
			
			$objectstatus = array();
			$listObjectStatus = array();
			$database->setQuery( "SELECT id as value, label as text FROM #__sdi_list_metadatastate " );
			$objectstatus= array_merge( $objectstatus, $database->loadObjectList() );
			foreach($objectstatus as $ot)
			{
				$listObjectStatus[$ot->value] = JText::_($ot->text);
			}
			$listObjectStatus = HTML_metadata::array2extjs($listObjectStatus, true);
			
			
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
							            					options.params.objectname = Ext.getCmp('objectname').getValue();
													 		options.params.objectstatus = Ext.getCmp('objectstatus').getValue();
													 		options.params.objectversion = Ext.getCmp('objectversion').getValue().getGroupValue();
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
													 displayField:'text'
											       },
											       {
											       		id:'objectname',
											       		hiddenName:'objectname_hidden',
											       		xtype:'textfield',
											       		fieldLabel : '".addslashes(JText::_('CATALOG_METADATA_REPLICATE_ALERT_OBJECTNAME_LABEL'))."'
											       },
											        { 
											         typeAhead:true,
											       	 triggerAction:'all',
											       	 mode:'local',
											       	 fieldLabel:'".addslashes(JText::_('CATALOG_METADATA_REPLICATE_ALERT_OBJECTSTATUS_LABEL'))."', 
											         id:'objectstatus', 
											         hiddenName:'objectstatus_hidden', 
											         xtype: 'combo',
											         editable: false,
											         store: new Ext.data.ArrayStore({
														        id: 0,
														        fields: [
														            'value',
														            'text'
														        ],
														        data: ".$listObjectStatus."
														    }),
													 valueField:'value',
													 displayField:'text'
											       },
											       {
											        fieldLabel: '".addslashes(JText::_('CATALOG_METADATA_REPLICATE_ALERT_OBJECTVERSION_LABEL'))."',
											        xtype: 'radiogroup', 
        											id:'objectversion', 
        											cls: 'x-check-group-alt',
        											columns: [80,80],
        											vertical:false,
											        items: [
											                {boxLabel: '".addslashes(JText::_('CATALOG_METADATA_REPLICATE_ALERT_OBJECTVERSION_ALL_LABEL'))."', name: 'version_grp', inputValue: 'All',checked: true},
											                {boxLabel: '".addslashes(JText::_('CATALOG_METADATA_REPLICATE_ALERT_OBJECTVERSION_LAST_LABEL'))."', name: 'version_grp', inputValue: 'Last'}
											        	] 
											       },
											       {
        											xtype: 'panel', 
        											buttonAlign:'right' ,
											       	buttons: [{ 
												       	xtype: 'button', 
												       	text:'".html_Metadata::cleanText(JText::_('CORE_SEARCH_BUTTON'))."',
									                    handler: function(){
									                    	var modelDest = Ext.getCmp('objectselector');                
												 			modelDest.store.removeAll();                
												 			modelDest.store.reload({                    
													 			params: { 
													 				objecttype_id: Ext.getCmp('objecttype_id').getValue(),
													 				objectname : Ext.getCmp('objectname').getValue(),
													 				objectstatus : Ext.getCmp('objectstatus').getValue(),
													 				objectversion : Ext.getCmp('objectversion').getValue().getGroupValue(),
																}                
															});	
														}
												       }]
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
													 	id: 'bbar',
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
							
							// Masquer le bouton de rafraechissement
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
					                	// Creer une iframe pour confirmer la reinitialisation
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
		
		return implode(', ', $tbar);
	}
}
?>
