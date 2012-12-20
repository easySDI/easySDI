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
/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/

defined('_JEXEC') or die('Restricted access');

class HTML_proxyWMTS {

	/**
	 * Get the Layer local filter
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @return string
	 */
	function getWMTSLayerLocalFilter($theServer,$layer){

		if (count($theServer->Layers->Layer)==0) return "";


		foreach ($theServer->Layers->Layer as $theLayer )
		{
				if (strcmp($theLayer->{'Name'},$layer)==0)
				{
					return $theLayer->{'Filter'};
				}
			}
		return "";
	}

	/**
	 * Is current layer checked
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @return boolean
	 */
	function isWMTSLayerChecked($theServer,$layer){
		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;
		if (count($theServer->Layers->Layer)==0) return false;
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get the current layer BBOX filter
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @return Ambiguous
	 */
	function getWMTSLayerBBOX($theServer, $layer){
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				$bbox = array();
				$bbox['minx'] = $theLayer->BoundingBox['minx'];
				$bbox['miny'] = $theLayer->BoundingBox['miny'];
				$bbox['maxx'] = $theLayer->BoundingBox['maxx'];
				$bbox['maxy'] = $theLayer->BoundingBox['maxy'];
				$bbox['spatial-operator'] = $theLayer->BoundingBox['spatialoperator'];
				return $bbox;
			}
		}
	}	
	
	/**
	 * Get select minscaledenominator
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @param unknown_type $TileMatrixSet
	 */
	function getWMTSTileMatrixSetMinScaleDenominator($theServer, $layer, $TileMatrixSet){
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				foreach ($theLayer->TileMatrixSet as $theTileMatrixSet){
					if(strcmp($theTileMatrixSet['id'],$TileMatrixSet) == 0){
						return $theTileMatrixSet->{'minScaleDenominator'};
					}
				}
			}
		}
	}	
	
	/**
	 * 
	 * Edit configuration for WMTS 1.0.0 servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigWMTS($xml,$new, $configId,$availableServletList,$availableVersion, $option, $task)
	{
		?>

	<form name='adminForm' id='adminForm' action='index.php' method='POST'>
		<input type='hidden' name='serviceType' id='serviceType' value="WMTS" >
		<input type='hidden' name="isNewConfig" value="<?php echo $new; ?>">
		<input type='hidden' name='option' value='<?php echo $option;?>'> 
		<input type='hidden' name='task' value='<?php echo $task;?>'> 
		<input type='hidden' name='configId' value='<?php echo $configId;?>'> 
		<input type='hidden' name="nbServer" id="nbServer" value=''>
		<?php
			foreach ($xml->config as $config) {
			if (strcmp($config['id'],$configId)==0){
				$servletClass=$config->{'servlet-class'};
				$keywordString = "";
				foreach ($config->{"service-metadata"}->{"ServiceIdentification"}->{'KeywordList'}->Keyword as $keyword)
				{
					$keywordString .= $keyword .",";
				}
				$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
				
				HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.wmts.WMTSProxyServlet", $availableServletList,$availableVersion,"WMTS")
		?>
			
			<fieldset class="adminform" id="service_metadata" ><legend><?php echo JText::_( 'PROXY_CONFIG_FS_SERVICE_METADATA'); ?></legend>
				<fieldset class="adminform" id="service_identification" ><legend><?php echo JText::_( 'PROXY_CONFIG_FS_SERVICE_METADATA_IDENTIFICATION'); ?></legend>
					<table class="admintable" >
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_TITLE"); ?> : </td>
							<td><input name="service_title" id="service_title" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"ServiceIdentification"}->{"Title"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_ABSTRACT"); ?> : </td>
							<td><input name="service_abstract" id="service_abstract" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"ServiceIdentification"}->{"Abstract"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_KEYWORD"); ?> : </td>
							<td><input name="service_keyword" id="service_keyword" type="text" size=100 value="<?php echo $keywordString; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_FEES"); ?> : </td>
							<td><input name="service_fees" id="service_fees" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"ServiceIdentification"}->{"Fees"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONSTRAINTS"); ?> : </td>
							<td><input name="service_accessconstraints" id="service_accessconstraints" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"ServiceIdentification"}->{"AccessConstraints"}; ?>"></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform" id="service_identification" ><legend><?php echo JText::_( 'PROXY_CONFIG_FS_SERVICE_METADATA_PROVIDER'); ?></legend>
					<table class="admintable" >
						<tr>
							<td class="key" ><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_PROVIDER_NAME"); ?> : </td>
							<td colspan="2"><input name="service_providername" id="service_providername" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ProviderName"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_PROVIDER_SITE"); ?> : </td>
							<td colspan="2"><input name="service_providersite" id="service_providersite" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ProviderSite"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_NAME"); ?> : </td>
							<td colspan="2"><input name="service_responsiblename" id="service_responsiblename" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{"IndividualName"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_POSITION"); ?> : </td>
							<td colspan="2"><input name="service_responsibleposition" id="service_responsibleposition" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{"PositionName"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_ROLE"); ?> : </td>
							<td colspan="2"><input name="service_responsiblerole" id="service_responsiblerole" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{"Role"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" ><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_ADRESS_TYPE"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactadresstype" id="service_responsiblecontactadresstype" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"AddressType"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" ><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_ADRESS_DELIVRYPOINT"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactadress" id="service_responsiblecontactadress" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'DelivryPoint'}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_CITY"); ?> : </td>
							<td><input name="service_responsiblecontactpostcode" id="service_responsiblecontactpostcode" type="text" size="5" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"PostalCode"}; ?>"></td>
							<td><input name="service_responsiblecontactcity" id="service_responsiblecontactcity" type="text" size="68" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"City"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_AREA"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactarea" id="service_responsiblecontactarea" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"Area"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_COUNTRY"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactcountry" id="service_responsiblecontactcountry" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"Country"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_MAIL"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactmail" id="service_responsiblecontactmail" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"ElectronicMailAddress"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_PHONE"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactphone" id="service_responsiblecontactphone" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"Telephone"}->{"VoicePhone"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_FAX"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactfax" id="service_responsiblecontactfax" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"Telephone"}->{"Facsimile"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" id="service_contactlinkage_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_ONLINERES"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactonline" id="service_responsiblecontactonline" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"OnlineResource"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" id="service_contacthours_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_HOURS"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontacthours" id="service_responsiblecontacthours" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"HoursOfService"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" id="service_contactinstructions_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_RESPONSIBLE_CONTACT_INSTRUCTIONS"); ?> : </td>
							<td colspan="2"><textarea name="service_responsiblecontactinstructions" id="service_responsiblecontactinstructions"  cols="45" rows="5"  ><?php echo $config->{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"Instructions"}; ?></textarea></td>
						</tr>
					</table>
				</fieldset>
			</fieldset>
	
				<?php
				HTML_proxy::genericServletInformationsFooter ($config);
				break;
			}
		}
		?>
		</form>
		<?php
	}
	
	
	

	/**
	 * Generate WMTS 100 form
	 * @param XML $config
	 * @param XML $thePolicy
	 */
	function generateWMTSHTML($config,$thePolicy,$servletVersion){
	?>
	<script>

	var waitFor = 0;
	function submitbutton(pressbutton)
	{
		if(pressbutton=='cancelPolicy')
		{
			submitform(pressbutton);	
		}
		else
		{
			var countServer =  document.getElementById('countServer').value;
			for (var i = 0 ; i <= countServer ; i++){
				var countLayer = document.getElementById('countLayer'+i).value;
				for (var j = 0 ; j <= countLayer ; j++){
					var bboxmaxx = document.getElementById ('bboxmaxx@'+i+'@'+j).value;
					var bboxminx = document.getElementById ('bboxminx@'+i+'@'+j).value;
					var bboxmaxy = document.getElementById ('bboxmaxy@'+i+'@'+j).value;
					var bboxminy = document.getElementById ('bboxminy@'+i+'@'+j).value;
					if(bboxmaxx != '' && bboxmaxy != '' && bboxminx != '' && bboxminy != ''){
						var countTileMatrixSet =  document.getElementById('countTileMatrixSet@'+i+'@'+j).value;
						for (var k = 0 ; k <= countTileMatrixSet ; k++){
							var CRS = document.getElementById('tmsCRS@'+i+'@'+j+'@'+k).value;

							if(CRS == null){
								CRS = 'EPSG:4326';
							}
							//if TileMatrixSet CRS is urn:ogc:def:crs:OGC:1.3:CRS84 :
							//proj4js does not support OGC authority crs so replace it by the corresponding EPSG:4326
							if(CRS.lastIndexOf("CRS84")!=-1){
								CRS = 'EPSG:4326';
							}
							
							var source = new Proj4js.Proj('EPSG:4326');
							var dest = new Proj4js.Proj(CRS);     
							waitFor += 1;
							checkProjLoaded(i,j,k, bboxminx, bboxminy,bboxmaxx,bboxmaxy, source, dest)
						}
					}	
				}
			}
			submitWhenAllBBOXCalculated(pressbutton);

		}
	}

	function submitWhenAllBBOXCalculated (pressbutton){
		if(waitFor== 0)
			submitform(pressbutton);
		else
			window.setTimeout(Proj4js.bind(submitWhenAllBBOXCalculated, this, pressbutton), 500);
	}
	
	function calculateBBOX(i,j,k, bboxminx, bboxminy,bboxmaxx,bboxmaxy, source, dest){
		var pLowerEastCorner = new Proj4js.Point(new Array(bboxminx,bboxminy));   
		Proj4js.transform(source, dest, pLowerEastCorner);
		var pUpperWestCorner = new Proj4js.Point(new Array(bboxmaxx,bboxmaxy));   
		Proj4js.transform(source, dest, pUpperWestCorner);

		document.getElementById ('bboxmaxx@'+i+'@'+j+'@'+k).value = pUpperWestCorner.x;
		document.getElementById ('bboxmaxy@'+i+'@'+j+'@'+k).value = pUpperWestCorner.y;
		document.getElementById ('bboxminx@'+i+'@'+j+'@'+k).value = pLowerEastCorner.x;
		document.getElementById ('bboxminy@'+i+'@'+j+'@'+k).value = pLowerEastCorner.y;
	}

	function checkProjLoaded(i,j,k, bboxminx, bboxminy,bboxmaxx,bboxmaxy, source, dest) {
	    if (!source.readyToUse || !dest.readyToUse) {
	      window.setTimeout(Proj4js.bind(checkProjLoaded, this, i,j,k, bboxminx, bboxminy,bboxmaxx,bboxmaxy, source, dest), 500);
	    } else {
		    waitFor -= 1;
		    document.getElementById('unitCRS@'+i+'@'+j+'@'+k).value = dest.units;
	    	calculateBBOX(i,j,k, bboxminx, bboxminy,bboxmaxx,bboxmaxy, source, dest);
	    }
	  
	}

	
	function disableOperationCheckBoxes()
		{
			var check = document.getElementById('AllOperations').checked;
			
			document.getElementById('oGetCapabilities').disabled=check;
			document.getElementById('oGetTile').disabled=check;
			document.getElementById('oGetFeatureInfo').disabled=check;
			document.getElementById('oGetCapabilities').checked=check;
			document.getElementById('oGetTile').checked=check;
			document.getElementById('oGetFeatureInfo').checked=check;
		}
		</script>
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_OPERATION'); ?></legend>
			<table class="admintable">
				<tr>
					<td >
					<?php if (strcasecmp($thePolicy->Operations['All'],'True')==0 || !$thePolicy->Operations){$checkedO='checked';} ?>	
						<input <?php echo $checkedO; ?>
						type="checkBox" name="AllOperations[]" id="AllOperations" 
						onclick="disableOperationCheckBoxes();"><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_OPERATION_ALL'); ?></td>
					<td><input type="checkBox" name="operation[]" id="oGetCapabilities" value="GetCapabilities" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetCapabilities')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETCAPABILITIES'); ?>
					</td>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetTile" value="GetTile" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetTile')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETTILE'); ?>
					</td>
				</tr>	
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetFeatureInfo" value="GetFeatureInfo" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetFeatureInfo')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETFEATUREINFO'); ?>
					</td>
				</tr>
			</table>
		</fieldset>

	<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_SERVER_ALL_TITLE'); ?></legend>
		<table class="admintable">
			<tr>
				<td><input type="checkBox" name="AllServers[]" id="AllServers" value="All" onclick="disableWMTSServersLayers();" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo 'checked'; ?>><?php echo JText::_( 'PROXY_CONFIG_SERVER_ALL'); ?></td>
			</tr>
		</table>
	</fieldset>
	<?php 
		$remoteServerList = $config->{'remote-server-list'};
		$iServer=0;
		foreach ($remoteServerList->{'remote-server'} as $remoteServer){

			$urlWithPassword = $remoteServer->url;
			
			if (strlen($remoteServer->user)!=null && strlen($remoteServer->password)!=null){
				if (strlen($remoteServer->user)>0 && strlen($remoteServer->password)>0){

					if (strpos($remoteServer->url,"http:")===False){
						$urlWithPassword =  "https://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,8);
					}else{
						$urlWithPassword =  "http://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,7);
					}
				}
			}
						
			$pos1 = stripos($urlWithPassword, "?");
			$separator = "&";
			if ($pos1 === false) {
	    		//"?" Not found then use ? instead of &
	    		$separator = "?";  
			}

			if($servletVersion != ""){
// 				$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WMTS");
				$ch = curl_init($urlWithPassword.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WMTS");
			}else{
// 				$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMTS");
				$ch = curl_init($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMTS");
			}
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$xml_raw = curl_exec($ch);
			$xmlCapa = simplexml_load_string($xml_raw);
			
			if ($xmlCapa === false){
					global $mainframe;		
							$mainframe->enqueueMessage(JText::_('EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' )." - ".$urlWithPassword,'error');
			}
			else{			
				//Save to file
				$confObject = JFactory::getApplication();
				$tmpPath = $confObject->getCfg('tmp_path');
				$tmpName = "wmts".md5(time().rand());
				$tmpFile = $tmpPath."/".$tmpName.".xml";
				$xmlCapa->asXML($tmpFile);
				
			foreach ($thePolicy->Servers->Server as $policyServer){			
				if (strcmp($policyServer->url,$remoteServer->url)==0){
					$theServer  = $policyServer;
				}
					
			}
			?>

	<input type="hidden" name="capaServer@<?php echo $iServer;?>" id="capaServer@<?php echo $iServer;?>" value="<?php echo $tmpName; ?>">
	<input type="hidden" name="remoteServer<?php echo $iServer;?>" id="remoteServer<?php echo $iServer;?>" value="<?php echo $remoteServer->url ;?>">
	<fieldset class="adminform" id="fsServer<?php echo $iServer;?>" >
		<legend><?php echo JText::_( 'PROXY_CONFOG_WMTS_SERVER'); ?> <?php echo $remoteServer->alias ;?> (<?php echo $remoteServer->url; ?>)</legend>
		<table  width ="100%"  class="admintable" id="remoteServerTable@<?php echo $iServer; ?>" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo "style='display:none'"; ?>>
			<tr>
				<td colspan="1">
					<input type="checkBox" 
						   name="AllLayers@<?php echo $iServer; ?>" 
						   id="AllLayers@<?php echo $iServer; ?>" 
						   value="All" <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) 
						   echo ' checked '; ?> 
						   onclick="disableWMTSLayers(<?php echo $iServer; ?>);">
						   <?php echo JText::_( 'PROXY_CONFIG_LAYER_ALL'); ?>
				</td>
			</tr>
			
			
			<?php
			$layernum = 0;
			$namespaces = $xmlCapa->getDocNamespaces();
			$dom_capa = dom_import_simplexml ($xmlCapa);
			
			//Get the TileMatrixSet definition
			$contents = $dom_capa->getElementsByTagNameNS($namespaces[''],'Contents')->item(0);
			$tileMatrixSets = $contents->getElementsByTagName('TileMatrixSet');
			$describedTileMatrixSets = array();
			$describedTileMatrixSetsCRS = array();
			foreach ( $tileMatrixSets as $tileMatrixSet){
				//Get the TileMatrix definition
				if($tileMatrixSet->parentNode->nodeName == "Contents" )
				{
					$tileMatrixSetIdentifier = $tileMatrixSet->getElementsByTagNameNS($namespaces['ows'],'Identifier')->item(0)->nodeValue;
					$tileMatrixSetCRS = $tileMatrixSet->getElementsByTagNameNS($namespaces['ows'],'SupportedCRS')->item(0)->nodeValue;
					$tileMatrices = $tileMatrixSet->getElementsByTagName('TileMatrix');
					$describedTileMatrices = array();
					for($tm = 0; $tm<$tileMatrices->length; $tm++){
						$tileMatrix = $tileMatrices->item($tm); 
						$tileMatrixIdentifier = $tileMatrix->getElementsByTagNameNS($namespaces['ows'],'Identifier')->item(0)->nodeValue;
						$tileMatrixScaleDenominator = $tileMatrix->getElementsByTagName('ScaleDenominator')->item(0)->nodeValue;
						$describedTileMatrices[$tileMatrixIdentifier] = $tileMatrixScaleDenominator;
					}
					$describedTileMatrixSets[$tileMatrixSetIdentifier] = $describedTileMatrices;
					$describedTileMatrixSetsCRS[$tileMatrixSetIdentifier]= $tileMatrixSetCRS;
				}
			}
			
    		$layers = $dom_capa->getElementsByTagNameNS($namespaces[''],'Layer');
    		
			foreach ( $layers as $layer){
				$identifiers = $layer->getElementsByTagNameNS($namespaces['ows'],'Identifier');
				$TileMatrixSetLinks = $layer->getElementsByTagName('TileMatrixSetLink');
			 		$identifier = $identifiers->item(0); 
		    		if($identifier->parentNode->nodeName == "Layer")
					{
						$title = $identifier->parentNode->getElementsByTagNameNS($namespaces['ows'],'Title')->item(0)->nodeValue;
						$bboxLayer = HTML_proxyWMTS::getWMTSLayerBBOX($theServer, $identifier->nodeValue);
						?>
						<tr>
						<td>
						<fieldset class="adminform" id="fsLayer@<?php echo $iServer;?>@<?php echo $layernum;?>" >
							<legend>
								<input  
										<?php 
										if( HTML_proxyWMTS::isWMTSLayerChecked($theServer,$identifier->nodeValue) || strcasecmp($theServer->Layers['All'],'True')==0) echo ' checked'; 
										if(strcasecmp($theServer->Layers['All'],'True')==0) echo ' disabled ';
										?> 
										type="checkbox"
										id="layer@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
										name="layer@<?php echo $iServer; ?>@<?php echo $layernum;?>"
										value="<?php echo $identifier->nodeValue;?>"
										onclick="enableTableLayer(<?php echo $iServer;?>,<?php echo $layernum;?>);"
										>
										<?php echo $identifier->nodeValue; ?> ( <?php echo $title;?> )
							</legend>
							<table id="tableLayer@<?php echo $iServer;?>@<?php echo $layernum;?>" 
								<?php 
									if(!HTML_proxyWMTS::isWMTSLayerChecked($theServer,$identifier->nodeValue) || strcasecmp($theServer->Layers['All'],'True')==0){
										 echo ' style="display: none;" ';
									}else{
										 echo ' style="display: block;" ';
									}
								?> >
								
								<tr>
									<td>
									<table>
										<tr>
											<th colspan="4">
												<?php echo JText::_( 'PROXY_CONFIG_GEOGRAPHIC_FILTER'); ?>	
											</th>
										</tr>
										<tr>
											<td>
											</td>
											<td align="right">
												<?php echo JText::_( 'PROXY_CONFIG_BBOX_MAXY'); ?>
											</td>
											<td>
												<input type="text" name="bboxmaxy@<?php echo $iServer; ?>@<?php echo $layernum; ?>" id="bboxmaxy@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="<?php echo $bboxLayer['maxy'];?>">
											</td>
											<td>
											</td>
											<td>
											</td>
										</tr>
										<tr>
											<td>
												<?php echo JText::_( 'PROXY_CONFIG_BBOX_MINX'); ?>
											</td>
											<td>
												<input type="text" name="bboxminx@<?php echo $iServer; ?>@<?php echo $layernum; ?>" id="bboxminx@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="<?php echo $bboxLayer['minx'];?>">
											</td>
											<td align="right">
												<?php echo JText::_( 'PROXY_CONFIG_BBOX_MAXX'); ?>
											</td>
											<td>
												<input type="text" name="bboxmaxx@<?php echo $iServer; ?>@<?php echo $layernum; ?>" id="bboxmaxx@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="<?php echo $bboxLayer['maxx'];?>">
											</td>
											<td>
												
											</td>
										</tr>
										<tr>
											<td>
											</td>
											<td align="right">
												<?php echo JText::_( 'PROXY_CONFIG_BBOX_MINY'); ?>
											</td>
											<td>
												<input type="text" name="bboxminy@<?php echo $iServer; ?>@<?php echo $layernum; ?>" id="bboxminy@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="<?php echo $bboxLayer['miny'];?>">
											</td>
											<td>
											</td>
											<td>
											</td>
										</tr>
										<tr>
											<td colspan="4">
												<input type="radio" name="spatial-operator@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="touch"  <?php if($bboxLayer['spatial-operator'] != "within"  ) echo " checked ";?>> Touch  
												<input type="radio" name="spatial-operator@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="within" <?php if($bboxLayer['spatial-operator'] == "within") echo " checked ";?>> Within 
											</td>
										</tr>
									</table>
									</td>
									<td valign="top"  colspan="2">
										<table width ="100%">
											<tr>
												<th><?php echo JText::_( 'PROXY_CONFIG_TILEMATRIXSET_ID'); ?></th>
												<th><?php echo JText::_( 'PROXY_CONFIG_TILEMATRIX_MIN_SCALE_DENOMINATOR'); ?></th>
											</tr>
											<?php 
											for($id = 0; $id<$TileMatrixSetLinks->length; $id++){ 
												$availableTileMatrix =  array();
												$availableTileMatrixList = array();
												$tileMatrixSet = $TileMatrixSetLinks->item($id);
												$tileMatrixSetIds = $tileMatrixSet->getElementsByTagName('TileMatrixSet');
												?>
												<tr>
												<td valign="top"  width ="14%">
												<?php
												$tileMatrixSetId = $tileMatrixSetIds->item(0)->nodeValue; 
												echo $tileMatrixSetId;?>
												<input type="hidden" name="TileMatrixSetId@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="TileMatrixSetId@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="<?php echo $tileMatrixSetId; ?>">
												</td>
												<td valign="top"  width ="86%">
												<?php 
												$availableTileMatrix = $describedTileMatrixSets[$tileMatrixSetId];
												$selectedDenominator = HTML_proxyWMTS::getWMTSTileMatrixSetMinScaleDenominator($theServer,$identifier->nodeValue,$tileMatrixSetId);
												foreach($availableTileMatrix as $key=>$value) :
														if($value == $selectedDenominator){
															$selectedDenominator = $key." [ ".$value." ]";
														}
					 									 $availableTileMatrixList[] = JHTML::_('select.option', $key." [ ".$value." ]", $key." [ ".$value." ]");
					 							endforeach;
					 							$availableTileMatrixList = array_merge(array(JHTML::_('select.option', 'service-value', JText::_( 'PROXY_CONFIG_SCALE_DENOMINATOR_DEFAULT'))), $availableTileMatrixList);
					 							echo JHTML::_("select.genericlist",$availableTileMatrixList, 'minScaleDenominator@'.$iServer."@".$layernum."@".$id, 'size="1" ', 'value', 'text', $selectedDenominator );
												?>
												<input type="hidden" name="tmsCRS@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="tmsCRS@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="<?php echo $describedTileMatrixSetsCRS[$tileMatrixSetId]; ?>">
												<input type="hidden" name="unitCRS@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="unitCRS@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="">
												<input type="hidden" name="bboxmaxx@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="bboxmaxx@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="">
												<input type="hidden" name="bboxminx@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="bboxminx@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="">
												<input type="hidden" name="bboxmaxy@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="bboxmaxy@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="">
												<input type="hidden" name="bboxminy@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" id="bboxminy@<?php echo $iServer; ?>@<?php echo $layernum; ?>@<?php echo $id; ?>" value="">
												</td>
												</tr>
												<?php 
											}
											?>
										</table>
									</td>
								</tr>
							</table>
							<input type="hidden" name="countTileMatrixSet@<?php echo $iServer; ?>@<?php echo $layernum; ?>" id="countTileMatrixSet@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="<?php echo $TileMatrixSetLinks->length -1; ?>">
						</fieldset>
					</td>
				</tr>
				
				<?php  
				$layernum += 1;
				}
			}?>
		</table>
		<input type="hidden" id="countLayer<?php echo $iServer; ?>" value="<?php echo $layernum -1; ?>">
	</fieldset>
	<?php
	$iServer = $iServer +1;
			}
		}
		?>
		<input type="hidden" id="countServer" value="<?php echo $iServer -1; ?>">
		<?php 
	}

}
?>