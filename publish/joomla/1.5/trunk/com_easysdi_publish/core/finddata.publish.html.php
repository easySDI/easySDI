<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

class PUBLISH_Finddata {
	
	function Finddata($featureSourceId, $featureSourceGuid, $wpsPublish, $currentUser, $config){
		global  $mainframe;
		$database =& JFactory::getDBO();
		$base_url = substr($_SERVER['PHP_SELF'], 0, -9);
		$isUpdate = $featureSourceId == 0 ? false : true;
		
		//the easysdi partner_id of the logged user
		$currentPartner = $currentUser->easysdi_user_id;
		$selectedScript = 0;
		$maxFileSize = $currentUser->publish_user_total_space == 0 ? 
				$config->default_dataset_upload_size : $currentUser->publish_user_total_space;
		
		//Script list for the user
		$script_list = array();
		$database->setQuery( "SELECT m.publish_script_id AS value, s.publish_script_display_name as text FROM #__sdi_publish_script_map m, #__sdi_publish_user u, #__sdi_publish_script s WHERE u.id = m.publish_user_id AND s.id = m.publish_script_id AND u.easysdi_user_id=".$currentPartner);
		$script_list = $database->loadObjectList();
		$script_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_PARTNER_LIST_TITLE"));
		$script_list = array_reverse($script_list);
		
		//File format for the user
		$format_list = array();
		$database->setQuery( "SELECT publish_script_name AS value, publish_script_display_name as text FROM #__sdi_publish_script WHERE publish_script_is_public=1");
		$format_list = $database->loadObjectList();
		$format_list [] = JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_PARTNER_LIST_TITLE"));
		$format_list = array_reverse($format_list);
		
		//Epsg code list
		$epsg_list = array();
		$epsg_list [] = JHTML::_('select.option',0, JTEXT::_("EASYSDI_PUBLISH_CHOOSEPROJECTION"));
		$epsg_list [] = JHTML::_('select.option','EPSG:4326', 'WGS84');
		$epsg_list [] = JHTML::_('select.option','EPSG:21781', 'CH1903 / LV03');
		$epsg_list [] = JHTML::_('select.option','EPSG:26986', 'North American Datum 1983');
		$epsg_list [] = JHTML::_('select.option','EPSG:26740', 'NAD 17 zone 10');
		$epsg_list [] = JHTML::_('select.option','EPSG:2277', 'NAD83 / Texas Central (ftUS)');
					
		//Retrieve the existing Feature Source name
		$joomlaUser = JFactory::getUser();
		$query = "SELECT f.name FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id;
		$database->setQuery($query);
		$res = $database->loadObjectList();
		$existingNames = "";
		for($i = 0; $i<count($res); $i++){
			$existingNames .= $res[$i]->name;
			if($i != (count($res)-1))
					$existingNames .= ",";
		}
		
		//Retrieve fs name and fieldsName
		$fsName = "";
		$fieldsName = "";
		$selectedEpsg = 0;
		//default datasource handler		
		$database->setQuery( "SELECT publish_script_name FROM #__sdi_publish_script where id=".$config->default_datasource_handler);
		$selectedFormat = $database->loadResult();
		
		if($isUpdate)
		{
			$database->setQuery( "SELECT name FROM #__sdi_publish_featuresource where id=".$featureSourceId);
			$fsName = $database->loadResult();
			
			//$database->setQuery( "SELECT fieldsName FROM #__sdi_publish_featuresource where id=".$featureSourceId);
			//$fieldsName = $database->loadResult();
			
			$database->setQuery( "SELECT projection FROM #__sdi_publish_featuresource where id=".$featureSourceId);
			$selectedEpsg = $database->loadResult();
			
			$database->setQuery( "SELECT publish_script_name FROM #__sdi_publish_script where id=".$config->default_datasource_handler);
		  $selectedFormat = $database->loadResult();
		}
		else
		{
			$fsName = "New";	
		}
		
		JHTML::script('Swiff.Uploader.js', 'components/com_easysdi_publish/js/fancyupload/');
		JHTML::script('Fx.ProgressBar.js', 'components/com_easysdi_publish/js/fancyupload/');
		JHTML::script('Lang.js', 'components/com_easysdi_publish/js/');
		JHTML::script('FancyUpload2.js', 'components/com_easysdi_publish/js/fancyupload/');
		JHTML::script('fancyuploader.js', 'components/com_easysdi_publish/js/');
		JHTML::script('finddata.js', 'components/com_easysdi_publish/js/');
	  
	  $param = array('size'=>array('x'=>350,'y'=>800) );
		JHTML::_("behavior.modal","a.modal",$param);
	?>
	<script type="text/javascript">
  
  var EASYSDI_PUBLISH_TEXT_PROGRESSION = '<?php echo JText::_("EASYSDI_PUBLISH_TEXT_PROGRESSION");?>';
  
  </script>
	
	
	<link rel="stylesheet" href="components/com_easysdi_publish/css/fancy.css" type="text/css" media="screen, projection">
		<div id="page">
		<h2 class="contentheading"><?php echo JText::_("EASYSDI_PUBLISH_PUBLISH_TITLE"); ?></h2>
		<div class="contentin">
			<h3><?php if($isUpdate) echo JText::_("EASYSDI_PUBLISH_UPDATE_FEATURESOURCE_NAME").": ".$fsName; else echo JText::_("EASYSDI_PUBLISH_NEW_FEATURESOURCE_NAME")?></h3>
				<div class="publish">	
					<!-- File choice -->
						<fieldset style="width:430px;">
						<legend><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_FILES"); ?> (<?php echo JText::_("EASYSDI_PUBLISH_MAX_FILE_SIZE"); ?>: <?php echo $maxFileSize; ?> <?php echo JText::_("EASYSDI_PUBLISH_MB"); ?>)</legend>
						<form action="components/com_easysdi_publish/core/script.php" method="post" enctype="multipart/form-data" id="form-demo">
							<div id="demo-fallback" class="hide">
								<p><b>
											Please activate the "System - Mootools Upgrade" plugin in the Joomla! backend,
											 because Mootools 1.2 is required for the uploader (next version of Publish will suppress this incompatibility). 
								</b></p>
							</div>
							<div style="width:430px;" id="demo-status" class="hide">
								<p>
									<a href="#" id="demo-browse"><?php echo JText::_("EASYSDI_PUBLISH_ADD_FILES"); ?></a> |
									<a href="#" id="demo-clear"><?php echo JText::_("EASYSDI_PUBLISH_CLEAR_FILES"); ?></a><!--  |
									<a href="#" id="demo-upload"><?php echo JText::_("EASYSDI_PUBLISH_START_UPLOAD"); ?></a> -->
								</p>
								<div>
									<span class="overall-title"></span><br />
									<img src="components/com_easysdi_publish/img/fancyupload/progress-bar/bar.gif" class="progress overall-progress" />
								</div>
								<div>
									<span class="current-title"></span><br />
									<img src="components/com_easysdi_publish/img/fancyupload/progress-bar/bar.gif" class="progress current-progress" />
								</div>
								<div class="current-text"></div>
							</div>
							<ul id="demo-list"></ul>
						</form>
						</fieldset>
					<!-- <input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option' );?>" /> -->
					<form name="publish_form" id="publish_form"  method="POST">
						<input type="hidden" name="fileList" id="fileList" value="" />
						<input type="hidden" name="userId" id="userId" value="<?php echo $currentPartner;?>" />
						<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo $base_url; ?>" />
						<input type="hidden" name="diffusionServerName" id="diffusionServerName" value="<?php echo $currentUser->diffusion_server_name; ?>" />
						<input type="hidden" name="featureSourceId" id="featureSourceId" value="<?php echo $featureSourceId; ?>" />
						<input type="hidden" name="fieldsName" id="fieldsName" value="<?php echo $fieldsName; ?>" />
						<input type="hidden" name="featureSourceGuid" id="featureSourceGuid" value="<?php echo $featureSourceGuid; ?>" />
						<input type="hidden" name="servAdr" id="servAdr" value="<?php echo "http://".$_SERVER['SERVER_ADDR']; ?>" />
						<input type="hidden" name="maxFileSize" id="maxFileSize" value="<?php echo $maxFileSize*1024*1024; ?>" />
						<input type="hidden" name="existingNames" id="existingNames" value="<?php echo $existingNames;?>" />
						<input type="hidden" name="task" id="task" value="<?php echo JRequest::getVar('task' );?>" />
						<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option');?>" />

						<!-- Dataset choice -->
						<fieldset style="width:460px;">
						<legend><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_DATASET"); ?></legend>
						<!-- Feature Source name -->
						<table>
							<tr>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_NAME"); ?>:</td>
								<td align="left"><input id="featuresource_name" name="featuresource_name" class="inputbox" type="text" size="10" maxlength="100" value="<?php echo $fsName; ?>" <?php if($isUpdate) echo "DISABLED"; ?>/></td>	
							</tr>
						</table>
						<table>
								<tr>
									<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_DATASET").":"; ?></td>
									<!-- the dataset -->
									<td align="left"><select class="inputbox" size="1" id="datasets" name="datasets">
											<option value="0"><?php echo JText::_("EASYSDI_PUBLISH_PARTNER_LIST_TITLE"); ?></option>
											</select>
									</td>
									<!-- projection -->
									<td align="left"><?php echo JHTML::_("select.genericlist",$epsg_list, 'projection', 'size="1" class="inputbox"', 'value', 'text', $selectedEpsg); ?></td>
								  <td align="left"><a class="modal" href="./index.php?tmpl=component&option=com_easysdi_publish&task=showEpsgList" rel="{handler:'iframe',size:{x:650,y:350}}"><?php echo JText::_("EASYSDI_PUBLISH_VIEW_EPSG_LIST"); ?></a></td>
									<!-- <td align="left"><button name="searchds" id="searchds"><?php echo JText::_("EASYSDI_PUBLISH_SEARCH"); ?></button></td> -->
								</tr>
						</table>
						</fieldset>
						
						<!-- Advanced choice -->
						<table>
							<tr>
								<td align="left"><input class="inputbox" type="checkbox" name="chkBxAdvanced" id="chkBxAdvanced" onclick="javascript:chkBxAdvanced_click();" /></td>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_ADVANCED"); ?></td>	
							</tr>
						</table>
						
						<!-- Advanced tab -->
						<div id="advancedTab">
							<fieldset style="width:460px;">
							<legend><?php echo JText::_("EASYSDI_PUBLISH_APPLY_SCRIPT"); ?></legend>
								<table>
								<tr>
									<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_SCRIPT"); ?></td>
									<td colspan="2" align="left"><?php echo JHTML::_("select.genericlist",$script_list, 'transfScriptId', 'size="1" class="inputbox"', 'value', 'text', $selectedScript); ?></td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_FORMAT_HANDLER"); ?></td>
									<td align="left"><?php echo JHTML::_("select.genericlist",$format_list, 'transfFormatId', 'size="1" class="inputbox"', 'value', 'text', $selectedFormat); ?></td>
								  <td align="left"><a class="modal" href="./index.php?tmpl=component&option=com_easysdi_publish&task=showFormatList" rel="{handler:'iframe',size:{x:350,y:450}}"><?php echo JText::_("EASYSDI_PUBLISH_DATASOURCE HANDLER_FORMAT_LIST"); ?></a></td>
								</tr>
							</table>
							</fieldset>
						</div>
						
							<table style="width:480px;">
								<tr>
									<td align="left">
										<button name="home" id="home"><?php echo JText::_("EASYSDI_PUBLISH_HOME"); ?></button>
									<td align="right">
										<div style="font-weight:bold" width="50px" id="progress"></div>
									</td>
									<td align="right" width="30px">
										<img id="loadingImg" width="25px" height="25px" src="components/com_easysdi_publish/img/loading.gif"></img>
									</td>
									<!--
									<td width="40px" align="right">
										<button name="demo-upload" id="demo-upload"><?php echo JText::_("EASYSDI_PUBLISH_UPLOAD_FILES"); ?></button>
									</td>
									-->
									<td width="40px" align="right">
										<button name="validateFs" id="validateFs"><?php if($isUpdate) echo JText::_("EASYSDI_PUBLISH_UPDATE"); else echo JText::_("EASYSDI_PUBLISH_CREATE"); ?></button>
									</td>
								</tr>
							</table>
						</form>
						<table style="width:480px;">
							<tr>
								<td align="right"><div style="width:430px;" id="errorMsg" class="errorMsg"></td>
							</tr>
						</table>	
				</div>
			</div>
		</div>

<?php
	}
}
?>