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

class PUBLISH_Buildlayer {
	
	function Buildlayer($featureSources, $layerId, $layerGuid, $wpsPublish, $currentUser, $config){
		global  $mainframe;
				
		$database =& JFactory::getDBO();
		$base_url = substr($_SERVER['PHP_SELF'], 0, -9);
		$isUpdate = $layerId == 0 ? false : true;
		$layer = null;
		if($isUpdate){
			$database->setQuery( "SELECT * FROM #__sdi_publish_layer where id=".$layerId);
			$layer = $database->loadObjectList();
			$layer = $layer[0];
		}else{
			$layer = new layer($layerId);
		}
					
		//the easysdi partner_id of the logged user
		$featureSourceId = JRequest::getVar("featureSourceId", $layer->featuresourceId);
		$currentPartner = $currentUser->easysdi_user_id;
		//$database->setQuery( "SELECT ETL_type FROM #__sdi_publish_etl where id=".$config->default_etl_id);
		//$defaultEtlName = $database->loadResult();
		
		//Geometry list
		$geometry_list = array();
		$geometry_list [] = JHTML::_('select.option',JTEXT::_("\$EASYSDI_PUBLISH_SELECT_GEOMETRY"), "");
		$geometry_list [] = JHTML::_('select.option','point', JTEXT::_("EASYSDI_PUBLISH_POINT"));
		$geometry_list [] = JHTML::_('select.option','line', JTEXT::_("EASYSDI_PUBLISH_LINE"));
		$geometry_list [] = JHTML::_('select.option','polygon', JTEXT::_("EASYSDI_PUBLISH_POLYGON"));
		
		//Retrieve layer name
		$layerName;
		$selectedGeometry = JTEXT::_("\$EASYSDI_PUBLISH_SELECT_GEOMETRY");
		if($isUpdate)
		{
			$database->setQuery( "SELECT name FROM #__sdi_publish_layer where id=".$layerId);
			$layerName = $database->loadResult();
			
			$database->setQuery( "SELECT geometry FROM #__sdi_publish_layer where id=".$layerId);
			$selectedGeometry = $database->loadResult();
		}
		else
		{
			$layerName = JRequest::getVar("layer_name", JTEXT::_("\$EASYSDI_PUBLISH_NEW_LAYER"));	
		}
		
		//load fslist from the server to check status
		$wpsConfig = $wpsPublish."/services/config";
		$fsList = "";
		foreach ($featureSources as $row)
						$fsList .= $row->featureGUID.",";
		$url = $wpsConfig."?operation=ListFeatureSources&list=".$fsList;
		$doc = SITE_proxy::fetch($url, false);
		$xml = simplexml_load_string($doc);
		//$xml = simplexml_load_file($url);
		$i=0;
		$swlWhere = "";
		foreach ($featureSources as $row){
			$srvFs = $xml->xpath("//featuresource[@guid='$row->featureGUID']");
	   	if(count($srvFs) > 0){
					$srvFs = $srvFs[0];
	   			$status = (string)$srvFs->status;
	   			if($status != "AVAILABLE")
	   				$swlWhere .= " AND featureGUID != '".$row->featureGUID."'";
					$i++;
			}
		}

		//Feature Source list for the user with status "CREATED"
		$fs_list = array();
		$query = "SELECT id AS value, name as text FROM #__sdi_publish_featuresource where partner_id=".$currentPartner.$swlWhere." order by name asc";
		$database->setQuery($query);
		$fs_list = (Array)$database->loadObjectList();
		array_unshift($fs_list,JHTML::_('select.option','0', JText::_("EASYSDI_PUBLISH_CHOOSEFEATURESOURCE")));
		//$fs_list = array_reverse($fs_list);
		
		JHTML::script('buildlayer.js', 'components/com_easysdi_publish/js/');
		
		//Fields name from feature source,and GUID
    //$fieldsName = "";
		$featureSourceGuid = "";
		//$arrAliases="";
		//$fieldAliases="";
		if($featureSourceId != ""){
			//$database->setQuery( "SELECT fieldsName FROM #__sdi_publish_featuresource where id=".$featureSourceId);
			//$fieldsName = $database->loadResult();
		
			//featureSourceGuid
			$database->setQuery( "SELECT featureGUID FROM #__sdi_publish_featuresource where id=".$featureSourceId);
			$featureSourceGuid = $database->loadResult();
		
			//aliases
			//$database->setQuery( "SELECT fieldsaliases FROM #__sdi_publish_featuresource where id=".$featureSourceId);
			//$fieldAliases = $database->loadResult();
			//$arrAliases = explode(",", $fieldAliases);
		}
		//$fieldsName = explode(",", $fieldsName);
		
		//Retrieve the existing Layer name
		$joomlaUser = JFactory::getUser();
		$query = "SELECT l.name FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id;
		$database->setQuery($query);
		$res = $database->loadObjectList();
		$existingNames = "";
		for($i = 0; $i<count($res); $i++){
			$existingNames .= $res[$i]->name;
			if($i != (count($res)-1))
					$existingNames .= ",";
		}
		
		
		//Ensure fields names & aliases are defined
		$i=0;
		foreach ($featureSources as $row){
			$srvFs = $xml->xpath("//featuresource[@guid='$row->featureGUID']");
	   	if(count($srvFs) > 0){
					$srvFs = $srvFs[0];
	   		//echo "<pre>";  print_r($srvFs);  echo "</pre>";

				//insert fields name once, is new fs, move to layer
					/*
					if($row->fieldsName == "undefined" || $row->fieldsName == "" || $row->fieldsName == "null"){
						$fName = (string)$srvFs->fieldsname;
						$database->setQuery( "UPDATE #__sdi_publish_featuresource SET fieldsaliases = '".$fName."', fieldsName = '".$fName."' where id=".$row->id);
						$res = $database->query();
					}
					*/
				$i++;
			}
		}

		
		JHTML::script('ext-base.js', 'components/com_easysdi_publish/js/extuploader/');
		JHTML::script('ext-all.js', 'components/com_easysdi_publish/js/extuploader/');
	?>
	
	
	
	<div id="page">
	  <h2 class="contentheading"><?php echo JText::_("EASYSDI_PUBLISH_PUBLISH_TITLE"); ?></h2>
		<div class="contentin">	
			 <h3><?php echo JText::_("EASYSDI_PUBLISH_LAYER_NAME"); ?>: <?php echo $layerName ?></h3>
				<div class="publish">	
					<form name="publish_form" id="publish_form"  method="POST">
						<input type="hidden" name="userId" id="userId" value="<?php echo $currentPartner;?>" />
						<input type="hidden" name="wpsPublish" id="wpsPublish" value="<?php echo $wpsPublish;?>" />
						<input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo $base_url; ?>" />
						<input type="hidden" name="layerId" id="layerId" value="<?php echo $layerId; ?>" />
						<input type="hidden" name="layerGuid" id="layerGuid" value="<?php echo $layerGuid; ?>" />
						<input type="hidden" name="featureSourceGuid" id="featureSourceGuid" value="<?php echo $featureSourceGuid; ?>" />
						<!-- <input type="hidden" name="fieldsName" id="fieldsName" value="<?php echo implode(",",$fieldsName); ?>" /> -->
						<!-- <input type="hidden" name="currentAliases" id="currentAliases" value="<?php echo $fieldAliases; ?>" /> -->
						<input type="hidden" name="existingNames" id="existingNames" value="<?php echo $existingNames;?>" />
						<input type="hidden" name="wmsUrl" id="wmsUrl" value="" />
						<input type="hidden" name="wfsUrl" id="wfsUrl" value="" />
						<input type="hidden" name="kmlUrl" id="kmlUrl" value="" />
						<input type="hidden" name="minx" id="minx" value="" />
						<input type="hidden" name="miny" id="miny" value="" />
						<input type="hidden" name="maxx" id="maxx" value="" />
						<input type="hidden" name="maxy" id="maxy" value="" />
						<input type="hidden" name="task" id="task" value="<?php echo JRequest::getVar('task' );?>" />
						<input type="hidden" name="option" id="option" value="<?php echo JRequest::getVar('option');?>" />

						
						<table class="layerGeneralInfo">
							<!-- The layer name -->
							<tr>
								<td align="left" width="40%"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_NAME"); ?>:</td>
								<td align="left"><input id="layer_name" name="layer_name" class="inputbox" type="text" size="20" maxlength="100" value="<?php echo $layerName; ?>"  <?php if($isUpdate) echo "DISABLED"; ?>/></td>	
							</tr>
							<!-- The Feature Source -->
							<tr>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_FEATURESOURCE_NAME"); ?>:</td>
								<td align="left"><?php 
								   if(!$isUpdate)
								      echo JHTML::_("select.genericlist",$fs_list, 'featureSourceId', 'size="1" class="inputbox" ', 'value', 'text', $featureSourceId == "" ? 0 : $featureSourceId); 
								   else
								      echo JHTML::_("select.genericlist",$fs_list, 'featureSourceId', 'size="1" class="inputbox" disabled="true"', 'value', 'text', $featureSourceId == "" ? 0 : $featureSourceId); 
							        ?></td>
							</tr>
						  <!-- The Geometry -->
							<tr>
								<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_CHOOSE_STYLE"); ?>:</td>
								<td align="left"><?php 
								    if(!$isUpdate)
								       echo JHTML::_("select.genericlist",$geometry_list, 'geometry', 'size="1" class="inputbox"', 'value', 'text', $selectedGeometry); 
								   else
								       echo JHTML::_("select.genericlist",$geometry_list, 'geometry', 'size="1" class="inputbox" disabled="true"', 'value', 'text', $selectedGeometry); 
							           ?></td>
							</tr>
						</table>
						
						<!-- fields Name -->
						<!--
						<div id="fieldsNameTab">
						<fieldset>
							<legend><?php echo JText::_("EASYSDI_PUBLISH_FIELDS_NAME"); ?></legend>
								<table width="100%" class="list">
								<thead>
									<tr>
										<th width="40%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_NAME"); ?></th>
										<th width="60%" align="center"><?php echo JText::_("EASYSDI_PUBLISH_TEXT_ALIAS"); ?></th>
									</tr>
								</thead>
								<tbody>
<?php
								$i=0;
								foreach ($fieldsName as $name)
								{
?>
									<tr>
										<td align="left"><?php echo $name; ?>:</td>
										<td align="left"><input id="attributeAlias<?php echo $i; ?>" name="attributeAlias<?php echo $i; ?>" class="inputbox" type="text" size="25" maxlength="300" value="<?php if($isUpdate) echo $arrAliases[$i]; ?>" /></td>
									</tr>
<?php
									$i++;
								}
?>
							</tbody>
							</table>
						</fieldset>
						</div>
						-->
						<!-- Description -->
						<div id="descriptionTab">
							<fieldset>
							<legend><?php echo JText::_("EASYSDI_PUBLISH_DESCRIPTION"); ?></legend>
								<table width="100%">
								<tr>
									<td align="left"  width="40%"><?php echo JText::_("EASYSDI_PUBLISH_TITLE"); ?></td>
									<td align="left"><input id="layerTitle" name="layerTitle" class="inputbox" type="text" size="20" maxlength="100" value="<?php echo $layer->title; ?>" /></td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_DESCRIPTION"); ?></td>
									<td align="left"><textarea id="layerDescription" name="layerDescription" cols=40 rows=3  WRAP=SOFT class="inputbox" ><?php echo $layer->description; ?></textarea></td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_QUALITY"); ?></td>
									<td align="left"><input id="layerQuality" name="layerQuality" class="inputbox" type="text" size="20" maxlength="100" value="<?php echo $layer->quality_area; ?>" /></td>
								</tr>
								<tr>
									<td align="left"><?php echo JText::_("EASYSDI_PUBLISH_KEYWORDLIST"); ?></td>
									<td align="left"><input id="layerKeyword" name="layerKeyword" class="inputbox" type="text" size="20" maxlength="100" value="<?php echo $layer->keywords; ?>" /></td>
								</tr>
							</table>
							</fieldset>
						</div>
					</form>
					<!-- Error message holder -->
					<table style="width:480px;">
						<tr>
							<td align="right">
							   <div style="width:430px;" id="errorMsg" class="errorMsg">
							     <table>
							       <tr><td id="errorMsgCode"></td></tr>
							       <tr><td id="errorMsgDescr"></td></tr>
							     </table>
							   </div>
							</td>
						</tr>
					</table>	
					<!-- Create/update button holder -->
					<table class="layerGeneralInfo">
						<tr>
							<td width="40px" align="left">
									<button name="home" id="home"><?php echo JText::_("EASYSDI_PUBLISH_HOME"); ?></button>
								</td>
							<td align="right"><img id="loadingImg" width="25px" height="25px" src="components/com_easysdi_publish/img/loading.gif"></img></td>
							<td width="40px" align="right">
							<button name="validateLayer" id="validateLayer"><?php if($isUpdate) echo JText::_("EASYSDI_PUBLISH_UPDATE"); else echo JText::_("EASYSDI_PUBLISH_CREATE"); ?></button>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>

<?php
	}
}
?>