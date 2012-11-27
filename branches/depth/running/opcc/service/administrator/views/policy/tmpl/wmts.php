<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */


// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
$document->addScript( JURI::root(true).'/administrator/components/com_easysdi_service/libraries/openlayers/OpenLayers.js' );
$document->addScript( JURI::root(true).'/administrator/components/com_easysdi_service/libraries/proj4js/lib/proj4js-combined.js' );

foreach ($this->xml->config as $config) {
	if (strcmp($config['id'],$this->config)==0){
		$policyFile = $config->{'authorization'}->{'policy-file'};
		$this->servletClass =  $config->{'servlet-class'};
		$servletVersion =  "";
		if(isset($config->{"supported-versions"}->{"version"}))
		{
			foreach($config->{"supported-versions"}->{"version"} as $versionConfig){
				if(strcmp($servletVersion, $versionConfig)< 0){
					$servletVersion = $versionConfig;
				}
			}
		}

		if (!file_exists($policyFile)){
			global $mainframe;
			$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
		}
			
		if (file_exists($policyFile)) {
			$xmlConfigFile = simplexml_load_file($policyFile);
			if(!isset($this->id))
			{
				$thePolicy  									= $xmlConfigFile->addChild('Policy');
				$thePolicy ['Id']								= "new Policy";
				$policyId										= $thePolicy ['Id'];
				$thePolicy ['ConfigId'] 						= $this->config;
				$thePolicy ->Servers['All']						= "false";
				$thePolicy ->Subjects['All']					= "false";
				$thePolicy ->Operations['All']					= "true";
				$thePolicy ->AvailabilityPeriod->Mask			= "dd-mm-yyyy";
				$thePolicy ->AvailabilityPeriod->From->Date		= "28-01-2008";
				$thePolicy ->AvailabilityPeriod->To->Date		= "28-01-2108";
			}
			else
			{
				foreach ($xmlConfigFile->Policy as $policy)
				{
					if (strcmp($policy['Id'],$this->id)==0  && strcmp($policy['ConfigId'],$this->config)==0)
					{
						$thePolicy = $policy;
						break;
					}
				}
			}
		}
		?>
		<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
			<div class="width-60 fltlft">
			<?php $this->genericPolicyFields($thePolicy); ?>
			<script>
			var waitFor = 0;
			Joomla.submitbutton = function(task)
			{
				if(task=='policy.cancel')
				{
					Joomla.submitform(task, document.getElementById('item-form'));	
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
					submitWhenAllBBOXCalculated(task);

				}
			}

			function submitWhenAllBBOXCalculated (task){
				if(waitFor== 0)
					Joomla.submitform(task, document.getElementById('item-form'));
				else
					window.setTimeout(Proj4js.bind(submitWhenAllBBOXCalculated, this, task), 500);
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
			function disableWMTSLayers(iServ)
			{
				var iLay = 0;
				var check = document.getElementById('AllLayers@'+iServ).checked;
				while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
				{
					document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
					document.getElementById('layer@'+iServ+'@'+iLay).disabled = check;
					document.getElementById('fsLayer@'+iServ+'@'+iLay).disabled = check;
					document.getElementById('tableLayer@'+iServ+'@'+iLay).style.display = "none";
					iLay ++;
				}
			}
			function enableTableLayer(iServ,iLay)
			{
				var check = document.getElementById('layer@'+iServ+'@'+iLay).checked;
				var display = "none";
				if(check){
					display="block";
				}
				document.getElementById('tableLayer@'+iServ+'@'+iLay).style.display = display;
			}
			function disableWMTSServersLayers ()
			{
				var nb = 0;
				var iLay = 0;
				var display = "block";
				var check = document.getElementById('AllServers').checked;
				if (document.getElementById('AllServers').checked)
				{
					display="none";
				}
				
				while (document.getElementById('remoteServerTable@'+nb) != null)
				{
					document.getElementById('remoteServerTable@'+nb).style.display=display;
					document.getElementById('AllLayers@'+nb).checked = check;
					while (document.getElementById('layer@'+nb+'@'+iLay) != null)
					{
						document.getElementById('layer@'+nb+'@'+iLay).checked = check;
						iLay ++;
					}
					iLay = 0;
					nb ++;
				}	
			}
			</script>
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_AUTHORIZED_OPERATION'); ?></legend>
			<table class="admintable">
				<tr>
					<td >
					<?php if (strcasecmp($thePolicy->Operations['All'],'True')==0 || !$thePolicy->Operations){$checkedO='checked';} ?>	
						<input <?php echo $checkedO; ?>
						type="checkBox" name="AllOperations[]" id="AllOperations" 
						onclick="disableOperationCheckBoxes();"><?php echo JText::_( 'COM_EASYSDI_SERVICE_AUTHORIZED_OPERATION_ALL'); ?></td>
					<td><input type="checkBox" name="operation[]" id="oGetCapabilities" value="GetCapabilities" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetCapabilities')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'COM_EASYSDI_SERVICE_OPERATION_GETCAPABILITIES'); ?>
					</td>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetTile" value="GetTile" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetTile')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'COM_EASYSDI_SERVICE_OPERATION_GETTILE'); ?>
					</td>
				</tr>	
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetFeatureInfo" value="GetFeatureInfo" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetFeatureInfo')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'COM_EASYSDI_SERVICE_OPERATION_GETFEATUREINFO'); ?>
					</td>
				</tr>
			</table>
		</fieldset>

	<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_SERVER_ALL_TITLE'); ?></legend>
		<table class="admintable">
			<tr>
				<td><input type="checkBox" name="AllServers[]" id="AllServers" value="All" onclick="disableWMTSServersLayers();" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo 'checked'; ?>><?php echo JText::_( 'COM_EASYSDI_SERVICE_SERVER_ALL'); ?></td>
			</tr>
		</table>
	</fieldset>
	<?php 
		$remoteServerList = $config->{'remote-server-list'};
		$iServer=0;
		foreach ($remoteServerList->{'remote-server'} as $remoteServer){

			$pos1 = stripos($remoteServer->url, "?");
			$separator = "&";
			if ($pos1 === false) {
				//"?" Not found then use ? instead of &
				$separator = "?";
			}
				
			if($servletVersion != ""){
				$url = $remoteServer->url.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WMTS";
			}else{
				$url = $remoteServer->url.$separator."REQUEST=GetCapabilities&SERVICE=WMTS";
			}
				
			$session 	= curl_init($url);
			$httpHeader = array();
			if (!empty($remoteServer->user)  && !empty($remoteServer->password))
			{
				$httpHeader[]='Authorization: Basic '.base64_encode($remoteServer->user.':'.$remoteServer->password);
			}
			if (count($httpHeader)>0)
			{
				curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
			}
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($session);
			curl_close($session);
				
			$xmlCapa = simplexml_load_string($response);
			
// 			$urlWithPassword = $remoteServer->url;
			
// 			if (strlen($remoteServer->user)!=null && strlen($remoteServer->password)!=null){
// 				if (strlen($remoteServer->user)>0 && strlen($remoteServer->password)>0){

// 					if (strpos($remoteServer->url,"http:")===False){
// 						$urlWithPassword =  "https://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,8);
// 					}else{
// 						$urlWithPassword =  "http://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,7);
// 					}
// 				}
// 			}
						
// 			$pos1 = stripos($urlWithPassword, "?");
// 			$separator = "&";
// 			if ($pos1 === false) {
// 	    		//"?" Not found then use ? instead of &
// 	    		$separator = "?";  
// 			}

// 			if($servletVersion != ""){
// 				$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WMTS");
// 			}else{
// 				$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMTS");
// 			}
			
			$theServer = null;
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
				<legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_WMTS_SERVER'); ?> <?php echo $remoteServer->alias ;?> (<?php echo $remoteServer->url; ?>)</legend>
				<table  width ="100%"  class="admintable" id="remoteServerTable@<?php echo $iServer; ?>" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo "style='display:none'"; ?>>
					<tr>
						<td colspan="1">
							<input type="checkBox" 
								   name="AllLayers@<?php echo $iServer; ?>" 
								   id="AllLayers@<?php echo $iServer; ?>" 
								   value="All" <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) 
								   echo ' checked '; ?> 
								   onclick="disableWMTSLayers(<?php echo $iServer; ?>);">
								   <?php echo JText::_( 'COM_EASYSDI_SERVICE_LAYER_ALL'); ?>
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
						$bboxLayer = $this->getWMTSLayerBBOX($theServer, $identifier->nodeValue);
						?>
						<tr>
						<td>
						<fieldset class="adminform" id="fsLayer@<?php echo $iServer;?>@<?php echo $layernum;?>" >
							<legend>
								<input  
										<?php 
										if( $this->isWMTSLayerChecked($theServer,$identifier->nodeValue) || strcasecmp($theServer->Layers['All'],'True')==0) echo ' checked'; 
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
									if(!$this->isWMTSLayerChecked($theServer,$identifier->nodeValue) || strcasecmp($theServer->Layers['All'],'True')==0){
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
												<?php echo JText::_( 'COM_EASYSDI_SERVICE_GEOGRAPHIC_FILTER'); ?>	
											</th>
										</tr>
										<tr>
											<td>
											</td>
											<td align="right">
												<?php echo JText::_( 'COM_EASYSDI_SERVICE_BBOX_MAXY'); ?>
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
												<?php echo JText::_( 'COM_EASYSDI_SERVICE_BBOX_MINX'); ?>
											</td>
											<td>
												<input type="text" name="bboxminx@<?php echo $iServer; ?>@<?php echo $layernum; ?>" id="bboxminx@<?php echo $iServer; ?>@<?php echo $layernum; ?>" value="<?php echo $bboxLayer['minx'];?>">
											</td>
											<td align="right">
												<?php echo JText::_( 'COM_EASYSDI_SERVICE_BBOX_MAXX'); ?>
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
												<?php echo JText::_( 'COM_EASYSDI_SERVICE_BBOX_MINY'); ?>
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
												<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_TILEMATRIXSET_ID'); ?></th>
												<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_TILEMATRIX_MIN_SCALE_DENOMINATOR'); ?></th>
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
												$selectedDenominator = $this->getWMTSTileMatrixSetMinScaleDenominator($theServer,$identifier->nodeValue,$tileMatrixSetId);
												foreach($availableTileMatrix as $key=>$value) :
														if($value == $selectedDenominator){
															$selectedDenominator = $key." [ ".$value." ]";
														}
					 									 $availableTileMatrixList[] = JHTML::_('select.option', $key." [ ".$value." ]", $key." [ ".$value." ]");
					 							endforeach;
					 							$availableTileMatrixList = array_merge(array(JHTML::_('select.option', 'service-value', JText::_( 'COM_EASYSDI_SERVICE_SCALE_DENOMINATOR_DEFAULT'))), $availableTileMatrixList);
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
			</div>		 
			<input type="hidden" id="countServer" value="<?php echo $iServer -1; ?>">
			<input type="hidden" name="task" value="<?php echo JRequest::getVar('task');?>" />
			<input type="hidden" name="connector" value="<?php echo JRequest::getVar('layout');?>" />
			<input type="hidden" name="servletClass" value="<?php echo $this->servletClass;?>" />
			<input type="hidden" name="previoustask" value="<?php echo JRequest::getVar('task');?>" />
			<input type="hidden" name="policyId" value="<?php echo $this->id ;?>" />
			<input type="hidden" name="configId" value="<?php echo $this->config;  ?>" />
				
			<?php echo JHtml::_('form.token'); ?>
			<div class="clr"></div>
		    <style type="text/css">
		        /* Temporary fix for drifting editor fields */
		        .adminformlist li {
		            clear: both;
		        }
		    </style>
		</form>
		<?php 
	}
}
	