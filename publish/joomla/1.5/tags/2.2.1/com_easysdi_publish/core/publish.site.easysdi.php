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

class SITE_publish {
	
	function gettingStarted($userRights, $wpsPublish, $currentUser){

		global  $mainframe;
		
		$db =& JFactory::getDBO();
		$index = JRequest::getVar('tabIndex',0);
		 
		$joomlaUser = JFactory::getUser();
		$option = JRequest::getVar('option','com_easysdi_publish');
		//setup the pagination
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',10);
		if($limit == "")
			$limit = 10;
		if($limit == 0)
			$limitstart = 0;
		//count remaining layers for user
		$query = "SELECT COUNT(*) FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id;					
		$db->setQuery( $query );
		$createdlayers = $db->loadResult();
		$remainingLayers = $currentUser->publish_user_max_layers - $createdlayers;
                
		//get the url of the users diffusion server
		//Get the server list from the WPS
		$wpsConfig = $wpsPublish."/services/config";
		$url = $wpsConfig."?operation=listPublicationServers";
		$doc = SITE_proxy::fetch($url, false);
		$xml = simplexml_load_string($doc);
		//get the server edit list
		$servers = $xml->server;
		$user_diffusor_url = "";
		foreach ($servers as $row)
		{	
			if((string)$row->id == (string)$currentUser->publish_user_diff_server_id)
				$user_diffusor_url = (string)$row->url;
		}
    
    $pageNav=null;
    $featureSources=null;
    $layers=null;
    
		if($index == 0){
				//count existing layers for user
				$query = "SELECT COUNT(*) FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id;					
				$db->setQuery( $query );
				$total = $db->loadResult();
				$pageNav = new JPagination($total,$limitstart,$limit);
				
				//Search Feature Sources regarding the limits
				$query = "SELECT f.* FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id;
				$query .= " ORDER BY f.name";
				$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
				//$db->setQuery( $query);
				
				$featureSources = $db->loadObjectList();
				if ($db->getErrorNum()) {
					echo $db->stderr();
					return false;
				}
		}
		elseif($index == 1){
				//count existing layers for user
				$query = "SELECT COUNT(*) FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id;					
				$db->setQuery( $query );
				$total = $db->loadResult();
				$pageNav = new JPagination($total,$limitstart,$limit);
				
				//Search Layers regarding the limits
				$query = "SELECT l.* FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id;
				$query .= " ORDER BY l.name";
				$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
				
				$layers = $db->loadObjectList();
				if ($db->getErrorNum()) {
					echo $db->stderr();
					return false;
				}	
				
				$featureSources = (Object)Array();
		}
		
		HTML_site::gettingStarted($featureSources, $layers, $pageNav, $userRights, $wpsPublish, $remainingLayers, $currentUser->publish_user_max_layers, $user_diffusor_url);
	}
	
	function createFeatureSource($wpsPublish, $currentUser, $config){
		$featureSourceId = 0;	
		$featureSourceGuid = 0;
		PUBLISH_Finddata::Finddata($featureSourceId, $featureSourceGuid, $wpsPublish, $currentUser, $config);
	}
	
	function editFeatureSource($wpsPublish, $currentUser, $config){
		global  $mainframe;
		$db =& JFactory::getDBO();
		//retrieve Feature Source info
		$featureSourceId = JRequest::getVar('featureSource');	
		$db->setQuery( "SELECT featureGUID FROM #__sdi_publish_featuresource where id=".$featureSourceId);
		$featureSourceGuid = $db->loadResult();
		if($db->getErrorNum()){
				$mainframe->enqueueMessage(JText::_("ERROR_EXECUTING_QUERY"),"ERROR");
				return;
		}
		
		PUBLISH_Finddata::Finddata($featureSourceId, $featureSourceGuid, $wpsPublish, $currentUser, $config);
	}
	
	function saveFeatureSource(){
		global $mainframe;
		$database=& JFactory::getDBO();
		
		$featureSource =& new featureSource($database);
		//if featureSourceId = 0 => create new, else update
		if(JRequest::getVar("featureSourceId") != 0)
			$featureSource->id = JRequest::getVar("featureSourceId");
		$featureSource->featureGUID = JRequest::getVar("featureSourceGuid");
		$featureSource->partner_id = JRequest::getVar("userId");
		$featureSource->name = JRequest::getVar("featuresource_name");
		$featureSource->projection = JRequest::getVar("projection");
		$featureSource->formatId = JRequest::getVar("transfFormatId");
		$featureSource->scriptId = JRequest::getVar("transfScriptId");
		$featureSource->fileList = JRequest::getVar("fileList");
		$featureSource->responseDoc = JRequest::getVar("responseDoc");
		if(JRequest::getVar("featureSourceId") == 0)
			$featureSource->creation_date = date('y-m-j, G-i-s');
		$featureSource->update_date = date('y-m-j, G:i:s');
		
		
		//save only if diffusion server has a name
		if($featureSource->name != "")
		{
			if (!$featureSource->store()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted");		
				exit();
			}
		}
		/*TODO, the Feature Source is stored, do some clean up in temp directory. Look at $featureSource->fileList */
				
	}
	
	function deleteFeatureSource($id){
		global $mainframe;
		$database=& JFactory::getDBO();
		//check if a layer exits
		$database->setQuery( "SELECT count(*) FROM #__sdi_publish_featuresource f, #__sdi_publish_layer l where l.featuresourceId=f.id AND f.id=".$id);
		$result = $database->loadResult();
		if($result == 0){
				$database->setQuery( "DELETE FROM #__sdi_publish_featuresource where id=".$id);
				$res = $database->query();
		}
	}
	
	function createLayer($wpsPublish, $currentUser, $config){
			$joomlaUser = JFactory::getUser();
			$db=& JFactory::getDBO();
			//count remaining layers for user
			$query = "SELECT COUNT(*) FROM #__sdi_publish_layer l, #__sdi_account p where p.id=l.partner_id AND p.user_id=".$joomlaUser->id;					
			$db->setQuery( $query );
			$createdlayers = $db->loadResult();
			$remainingLayers = $currentUser->publish_user_max_layers - $createdlayers;
			if($remainingLayers == 0){
				echo JText::_("EASYSDI_PUBLISH_NO_MORE_LAYER");
				return;
			}
			$layerId = 0;
			$layerGuid = 'none';
			
			//load all featuresource for the partner
				//$query = "SELECT f.featureGUID, f.fieldsName FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id;
				$query = "SELECT f.featureGUID FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id;
				$query .= " ORDER BY f.name";
				$db->setQuery( $query );	
				
				$featureSources = $db->loadObjectList();
				if ($db->getErrorNum()) {
					echo $db->stderr();
					return false;
				}
				
			PUBLISH_Buildlayer::Buildlayer($featureSources, $layerId, $layerGuid, $wpsPublish, $currentUser, $config);
	}
		
	function editLayer($wpsPublish, $currentUser, $config){
		global $mainframe;
		$db=& JFactory::getDBO();
		$joomlaUser = JFactory::getUser();
		//retrieve Layer info
		$layerId = JRequest::getVar('id');
		
		$query = "SELECT f.featureGUID FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id;
		$query .= " ORDER BY f.name";
		$db->setQuery( $query );	
				
		$featureSources = $db->loadObjectList();
		if ($db->getErrorNum()){
			echo $db->stderr();
			return false;
		}
				
		$db->setQuery( "SELECT layerGuid FROM #__sdi_publish_layer where id=".$layerId);
		$layerGuid = $db->loadResult();
		if($db->getErrorNum()){
				$mainframe->enqueueMessage(JText::_("ERROR_EXECUTING_QUERY"),"ERROR");
				return;
		}
		PUBLISH_Buildlayer::Buildlayer($featureSources, $layerId, $layerGuid, $wpsPublish, $currentUser, $config);
}
	
	
	function saveLayer($isCopy){
		
		global $mainframe;
		$database=& JFactory::getDBO();
		$layer =& new layer($database);
		
		if($isCopy){
			$layer->load(JRequest::getVar("layerIdToCopy"));
		}
		
		/*
		print_r($_GET);
		print_r($layer);
		exit;
		*/
		
		if($isCopy){
			$layer->id = 0;
			$layer->name = JRequest::getVar("layerCopyName");
			$layer->title = JRequest::getVar("layerCopyName");
		}else{
			if(JRequest::getVar("layerId") != 0)
				$layer->id = JRequest::getVar("layerId");
			$layer->featuresourceId = JRequest::getVar("featureSourceId");
			$layer->name = JRequest::getVar("layer_name");
			$layer->title = JRequest::getVar("layerTitle");
			$layer->geometry = JRequest::getVar("geometry");
			$layer->description = JRequest::getVar("layerDescription");
			$layer->quality_area = JRequest::getVar("layerQuality");
			$layer->keywords = JRequest::getVar("layerKeyword");
			$layer->layerGuid = JRequest::getVar("layerGuid");
			$layer->update_date = date('y-m-j, G:i:s');
			$layer->partner_id = JRequest::getVar("userId");
			$layer->bbox = JRequest::getVar("minx").",".JRequest::getVar("miny").",".JRequest::getVar("maxx").",".JRequest::getVar("maxy");
		}
		$layer->wmsUrl = JRequest::getVar("wfsUrl");
		$layer->wfsUrl = JRequest::getVar("wmsUrl");
		$layer->kmlUrl = JRequest::getVar("kmlUrl");
		if(JRequest::getVar("layerId") == 0)
			$layer->creation_date = date('y-m-j, G-i-s');
		
		//save only if layer has a name
		if($layer->name != "")
		{
			if (!$layer->store()) {	
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1");		
				exit();
			}
				//retrieve aliases
				$currentAliases = JRequest::getVar("currentAliases");
				$temp = explode(",",$currentAliases);
				$aliases = "";
				
				for ($i = 0; $i < count($temp); $i++){
					if($i > 0)
						$aliases .= ",";
					//if no alias set, keep current
					$newAlias = JRequest::getVar("attributeAlias".$i);
					if($newAlias == "")
						$aliases .= $temp[$i];
					else
						$aliases .= $newAlias;
				}
				$database->setQuery( "UPDATE #__sdi_publish_featuresource SET fieldsaliases = '".$aliases."' where id=".$layer->featuresourceId);
				$res = $database->query();
		}
		/*TODO, the Feature Source is stored, do some clean up in temp directory. Look at $featureSource->fileList */
				
	}
	
	function deleteLayer($id){
		global $mainframe;
		$database=& JFactory::getDBO();
		$database->setQuery( "DELETE FROM #__sdi_publish_layer where id=".$id);
		$res = $database->query();
	}
	
	function showFsStats($wpsAddress){
		global $mainframe;
		$db =& JFactory::getDBO();
		$joomlaUser = JFactory::getUser();
		$guid = JRequest::getVar('guid',0);
		$query = "SELECT * FROM #__sdi_publish_featuresource f, #__sdi_account p where p.id=f.partner_id AND p.user_id=".$joomlaUser->id." AND f.featureGUID='".$guid."'";
		$db->setQuery($query);
		$row = $db->loadObjectList();
		if(count($row) < 1)
			$row = null;
		else{
		  $row =$row[0];
			//load fslist from the server and update fs object
			$wpsConfig = $wpsAddress."/services/config";
			$url = $wpsConfig."?operation=ListFeatureSources&list=".$guid;
			$doc = SITE_proxy::fetch($url, false);
			$xml = simplexml_load_string($doc);
			$swlWhere = "";
			$srvFs = $xml->xpath("//featuresource[@guid='$guid']");
	  	if(count($srvFs) > 0){
					$srvFs = $srvFs[0];
					
	  			$row->status = (string)$srvFs->status;
	  			$row->excmessage = (string)$srvFs->excmessage;
	  			$row->exccode = (string)$srvFs->exccode;
				$row->excstacktrace = (string)$srvFs->excstacktrace;
				$row->tablename = (string)$srvFs->tablename;
				$row->featureguid = (string)$srvFs->featureguid;
			}
		}
		HTML_site::showFsStats($row);
	}
	
	function showEpsgList(){
			HTML_epsg::listDefinitions();
	}
	
	function showFormatList(){
			HTML_site::showFormatList();
	}
}
?>