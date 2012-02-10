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

class HTML_proxyWMS {

	
	function getWMSLayerLocalFilter($theServer,$layer){

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
	
	function getLayerMinScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
				if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
				{
					return $theLayer->ScaleMin;
				}
			}
		return "";
	}

	function getLayerMaxScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
				if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
				{
					return $theLayer->ScaleMax;
				}
			}
		return "";
	}

	function getWMSLayerMinScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
				if (strcmp($theLayer->{'Name'},$layer)==0)
				{
					return $theLayer->ScaleMin;
				}
			}
		return "";
	}
	function getWMSLayerMaxScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
			foreach ($theServer->Layers->Layer as $theLayer )
			{
				if (strcmp($theLayer->{'Name'},$layer)==0)
				{
					return $theLayer->ScaleMax;
				}
			}
		return "";
	}
	function isLayerChecked($theServer,$layer){

		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;

		if (count($theServer->Layers->Layer)==0) return false;

		foreach ($theServer->Layers->Layer as $theLayer )
		{
				if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
				{
					return true;
				}
			}
		return false;
	}
	
		function isWMSLayerChecked($theServer,$layer){
	
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
	 * 
	 * Edit configuration for WMS servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigWMS($xml,$new, $configId,$availableServletList, $availableVersion,$option, $task)
	{
		?>
		
		<form name='adminForm' id='adminForm' action='index.php' method='POST'>
		
			<input type='hidden' name='serviceType' id='serviceType' value="WMS" >
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
					foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
					{
						$keywordString .= $keyword .",";
					}
					$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;

					HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.wms.WMSProxyServlet", $availableServletList,$availableVersion,"WMS")
					?>
		
			<fieldset class="adminform" id="service_metadata" ><legend><?php echo JText::_( 'PROXY_CONFIG_FS_SERVICE_METADATA'); ?></legend>
				<table class="admintable" >
					<tr>
						<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_TITLE"); ?> : </td>
						<td><input name="service_title" id="service_title" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"Title"}; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_ABSTRACT"); ?> : </td>
						<td><input name="service_abstract" id="service_abstract" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"Abstract"}; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_KEYWORD"); ?> : </td>
						<td><input name="service_keyword" id="service_keyword" type="text" size=100 value="<?php echo $keywordString; ?>"></td>
					</tr>
					<tr>
						<td colspan="2">
						<fieldset class="adminform" id ="servicemetadata_contact"><legend><?php echo JText::_( 'PROXY_CONFIG_FS_SERVICE_METADATA_CONTACT'); ?></legend>
							<table>
								<tr>
									<td class="key" ><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_ORGANIZATION"); ?> : </td>
									<td colspan="2"><input name="service_contactorganization" id="service_contactorganization" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactOrganization"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_PERSON"); ?> : </td>
									<td colspan="2"><input name="service_contactperson" id="service_contactperson" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactName"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_POSITION"); ?> : </td>
									<td colspan="2"><input name="service_contactposition" id="service_contactposition" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactPosition"}; ?>"></td>
								</tr>
								<tr>
									<td class="key" id="service_contacttype_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_ADRESSTYPE"); ?> : </td>
									<td colspan="2"><input name="service_contacttype" id="service_contacttype" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"AddressType"}; ?>"></td>
								</tr>
								<tr>
									<td class="key" ><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_ADRESS"); ?> : </td>
									<td colspan="2"><input name="service_contactadress" id="service_contactadress" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Address"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_CITY"); ?> : </td>
									<td><input name="service_contactpostcode" id="service_contactpostcode" type="text" size="5" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"PostalCode"}; ?>"></td>
									<td><input name="service_contactcity" id="service_contactcity" type="text" size="68" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"City"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_STATE"); ?> : </td>
									<td colspan="2"><input name="service_contactstate" id="service_contactstate" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"State"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_COUNTRY"); ?> : </td>
									<td colspan="2"><input name="service_contactcountry" id="service_contactcountry" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Country"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_TEL"); ?> : </td>
									<td colspan="2"><input name="service_contacttel" id="service_contacttel" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"VoicePhone"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_FAX"); ?> : </td>
									<td colspan="2"><input name="service_contactfax" id="service_contactfax" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"Facsimile"}; ?>"></td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_MAIL"); ?> : </td>
									<td colspan="2"><input name="service_contactmail" id="service_contactmail" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"ElectronicMailAddress"}; ?>"></td>
								</tr>
							</table>
						</fieldset>
						 </td>
					</tr>
					
					<tr>
						<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_FEES"); ?> : </td>
						<td><input name="service_fees" id="service_fees" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"Fees"}; ?>"></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONSTRAINTS"); ?> : </td>
						<td><input name="service_accessconstraints" id="service_accessconstraints" type="text" size=100 value="<?php echo $config->{"service-metadata"}->{"AccessConstraints"}; ?>"></td>
					</tr>
				</table>
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
	/**
	 * 
	 * Generate WMS form
	 * @param XML  $config
	 * @param XML $thePolicy
	 */
	function generateWMSHTML($config,$thePolicy,$servletVersion){
	?>
	<script>

	function submitbutton(pressbutton)
	{
		if(pressbutton=='cancelPolicy')
		{
			submitform(pressbutton);	
		}
		else
		{
			if( (document.getElementById('minWidth').value != "" && document.getElementById('minHeight').value == "")
			  ||(document.getElementById('minWidth').value == "" && document.getElementById('minHeight').value != "")
			  ||(document.getElementById('maxWidth').value != "" && document.getElementById('maxHeight').value == "")
			  ||(document.getElementById('maxWidth').value == "" && document.getElementById('maxHeight').value != "")
			){
				alert ('<?php echo  JText::_( 'EASYSDI_IMAGE_SIZE_VALIDATION_ERROR');?>');		
			}
			else
			{
				//Calculate BBOX
				var countServer = document.getElementById('countServer').value;
				for (var i = 0 ; i <= countServer ; i++){
					var countLayer = document.getElementById('countLayer'+i).value;
					for (var j = 0 ; j <= countLayer ; j++){
						if(document.getElementById('LocalFilter@'+i+'@'+j) == null){
							continue;
						}
						if(document.getElementById('LocalFilter@'+i+'@'+j).value != ""){
							var value = document.getElementById('LocalFilter@'+i+'@'+j).value;
							value = value.replace(/^\s*|\s*$/g,'');
							if(value.length == 0 ){
								continue;
							}
							//Get the srs name
							var index = value.indexOf("srsName=\"", 0);
							var indexEnd = value.indexOf("\"", index+9) ;
							var srsValue = value.substring(index+9,indexEnd);

							//Complete filter GML
							value = "<gml:featureMembers xmlns:gml=\"http://www.opengis.net/gml\"><gml:FeatureFilter xmlns:gml=\"http://www.opengis.net/gml\">" + value + "</gml:FeatureFilter></gml:featureMembers>";
							//Load filter as DOMDocument
							if (window.ActiveXObject){
								var doc=new ActiveXObject('Microsoft.XMLDOM');
								doc.async='false';
								doc.loadXML(value);
							} else {
								var parser=new DOMParser();
								var doc=parser.parseFromString(value,'text/xml');
							}
							var gmlOptions = {
					                featureName: "FeatureFilter",
					                gmlns: "http://www.opengis.net/gml"};
							var theParser = new OpenLayers.Format.GML(gmlOptions);
						    var features = theParser.read(doc);
							var bbox = features[0].geometry.getBounds().toBBOX();
							document.getElementById('BBOX@'+i+'@'+j).value = srsValue+','+bbox;
						}
					}
				}
				submitform(pressbutton);
			}
		}
	}
	
	function disableOperationCheckBoxes()
	{
		var check = document.getElementById('AllOperations').checked;
		
		document.getElementById('oGetCapabilities').disabled=check;
		document.getElementById('oGetMap').disabled=check;
		document.getElementById('oGetFeatureInfo').disabled=check;
		document.getElementById('oGetLegendGraphic').disabled=check;
		document.getElementById('oGetCapabilities').checked=check;
		document.getElementById('oGetMap').checked=check;
		document.getElementById('oGetFeatureInfo').checked=check;
		document.getElementById('oGetLegendGraphic').checked=check;
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
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETCAPABILITIES'); ?></td>
					<td><input type="checkBox" name="operation[]" id="oGetLegendGraphic" value="GetLegendGraphic" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetLegendGraphic')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETLEGENDGRAPHIC'); ?></td>
					
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetMap" value="GetMap" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetMap')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETMAP'); ?></td>
					<!--<td><input type="checkBox" name="operation[]" id="oPutStyles"  value="PutStyles" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'PutStyles')==0) echo 'checked';			
						}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_PUTSTYLES'); ?></td>-->
						<td><input type="checkBox" name="operation[]" id="oDescribeLayer" value="DescribeLayer" disabled>
						<i><?php echo JText::_( 'PROXY_CONFIG_OPERATION_DESCRIBELAYER'); ?></i></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetFeatureInfo"  value="GetFeatureInfo" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'GetFeatureInfo')==0) echo 'checked';			
						}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETFEATUREINFO'); ?></td>
					<!--<td><input type="checkBox" name="operation[]" id="oGetStyles" value="GetStyles" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'GetStyles')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETSTYLES'); ?></td>-->
				</tr>
				<tr>
					<td></td>
					<!--<td><input type="checkBox" name="operation[]" id="oDescribeLayer" value="DescribeLayer" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'DescribeLayer')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_DESCRIBELAYER'); ?></td>-->
					
					<td></td>
				</tr>
			</table>
		</fieldset>
	<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_IMAGE_SIZE'); ?>
	<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpImageSize" rel="{handler:'iframe',size:{x:600,y:180}}"> 
			<img class="helpTemplate" 
				 src="../templates/easysdi/icons/silk/help.png" 
				 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
				 />
		</a></legend>
		<table class="admintable">
			
			<tr>
				<td class="key">
				<?php echo JText::_( 'EASYSDI_IMAGE_SIZE_MIN'); ?>
				</td>
				<td>
					<table>
					<tr>
					<td><b><?php echo JText::_( 'EASYSDI_IMAGE_SIZE_WIDTH'); ?></b></td>
					<td><input id="minWidth" name="minWidth" type="text" size ="10" value="<?php if (strlen($thePolicy->ImageSize->Minimum->Width)>0)echo $thePolicy->ImageSize->Minimum->Width ; ?>" /></td>
					</tr>
					<tr>
					<td><b><?php echo JText::_( 'EASYSDI_IMAGE_SIZE_HEIGHT'); ?></b></td>
					<td><input type="text" id="minHeight" name="minHeight" size ="10" value="<?php if (strlen($thePolicy->ImageSize->Minimum->Height)> 0 ) echo $thePolicy->ImageSize->Minimum->Height ; ?>" /></td>
					</tr>
					</table>
				</td>	
			</tr>
			<tr>
				<td class="key"> 
				<?php echo JText::_( 'EASYSDI_IMAGE_SIZE_MAX'); ?>
				</td>
				<td>
					<table>
					<tr>
					<td><b><?php echo JText::_( 'EASYSDI_IMAGE_SIZE_WIDTH'); ?></b></td>
					<td><input type="text" id="maxWidth" name="maxWidth" size ="10" value="<?php if (strlen($thePolicy->ImageSize->Maximum->Width)> 0 ) echo $thePolicy->ImageSize->Maximum->Width ; ?>" /></td>
					</tr>
					<tr>
					<td><b><?php echo JText::_( 'EASYSDI_IMAGE_SIZE_HEIGHT'); ?></b></td>
					<td><input type="text" id="maxHeight" name="maxHeight" size ="10" value="<?php if (strlen($thePolicy->ImageSize->Maximum->Height)> 0 )  echo $thePolicy->ImageSize->Maximum->Height ; ?>" /></td>
					</tr>
					</table>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_SERVER_ALL_TITLE'); ?></legend>
		<table class="admintable">
			<tr>
				<td><input type="checkBox" name="AllServers[]" id="AllServers" value="All" onclick="disableServersLayers();" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo 'checked'; ?>><?php echo JText::_( 'PROXY_CONFIG_SERVER_ALL'); ?></td>
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
				$completeurl = $urlWithPassword.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WMS";
				$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WMS");
			}else{
				$completeurl = $urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMS";
				$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMS");
			}
			
			if ($xmlCapa === false){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_('EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' )." - ".$completeurl,'error');
			}else{	
					
				foreach ($thePolicy->Servers->Server as $policyServer){			
				if (strcmp($policyServer->url,$remoteServer->url)==0){
					$theServer  = $policyServer;
				}
					
			}
			?>

	<input type="hidden" name="remoteServer<?php echo $iServer;?>" id="remoteServer<?php echo $iServer;?>" value="<?php echo $remoteServer->url ?>">
	<fieldset class="adminform" id="fsServer<?php echo $iServer;?>" >
		<legend><?php echo JText::_( 'EASYSDI_WMS_SERVER'); ?> <?php echo $remoteServer->alias ;?> (<?php echo $remoteServer->url; ?>)</legend>
		<table class="admintable">
			<tr>
				<td class="key"><?php echo JText::_( 'EASYSDI_WFS_SERVER_PREFIXE'); ?> </td>		
				<td>
				<input type="text" size ="100"   name="serverPrefixe<?php echo $iServer; ?>" id="serverPrefixe<?php echo $iServer; ?>" value="<?php echo $theServer->Prefix; ?>">
				</td>
			</tr>
			<tr>
				<td class="key"><?php echo JText::_( 'EASYSDI_WFS_SERVER_NAMESPACE'); ?></td>
				<td>
				<input type="text" size ="100"  name="serverNamespace<?php echo $iServer; ?>" id="serverNamespace<?php echo $iServer; ?>" value="<?php echo $theServer->Namespace; ?>">
				</td>
			</tr>
		</table>
		<br>
		<table class="admintable" id="remoteServerTable@<?php echo $iServer; ?>" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo "style='display:none'"; ?>>
			<tr>
				<th><b><?php echo JText::_( 'EASYSDI_LAYER NAME'); ?></b></th>
				<th><b><?php echo JText::_( 'EASYSDI_SCALE MIN'); ?></b></th>
				<th><b><?php echo JText::_( 'EASYSDI_SCALE MAX'); ?></b></th>
				<th><b><?php echo JText::_( 'EASYSDI_LOCAL FILTER'); ?></b>
				<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpQueryWMSTemplate" rel="{handler:'iframe',size:{x:600,y:280}}"> 
					<img class="helpTemplate" 
						 src="../templates/easysdi/icons/silk/help.png" 
						 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
						 />
				</a></th>
			</tr>
			<tr>
				<td colspan="4"><input type="checkBox" name="AllLayers@<?php echo $iServer; ?>" id="AllLayers@<?php echo $iServer; ?>" value="All" <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' checked '; ?> onclick="disableLayers(<?php echo $iServer; ?>);"><?php echo JText::_( 'PROXY_CONFIG_LAYER_ALL'); ?></td>
			</tr>
			<?php
			$layernum = 0;
			$namespaces = $xmlCapa->getDocNamespaces();
			$dom_capa = dom_import_simplexml ($xmlCapa);
			//WMS 1.3.0
    		$layers = $dom_capa->getElementsByTagNameNS($namespaces[''],'Layer');
    		if($layers->length == 0)
    		{
    			//WMS 1.1.1 and 1.1.0
    			$layers = $dom_capa->getElementsByTagName('Layer');
    		}
    		
    		foreach ( $layers as $layer){
    			//WMS 1.3.0
				$title = $layer->getElementsByTagNameNS($namespaces[''],'Title')->item(0)->nodeValue ;
				$name = $layer->getElementsByTagNameNS($namespaces[''],'Name')->item(0)->nodeValue ;
				if($title == null){
					//WMS 1.1.1 and 1.1.0
					$title = $layer->getElementsByTagName('Title')->item(0)->nodeValue ;
					$name = $layer->getElementsByTagName('Name')->item(0)->nodeValue ;
				}
				if($name != null){
					if (strlen($name)>0 ){
					?>
			<tr>
				<td class="key" >
					<table width ="100%" height="100%" >
						<tr valign="top" >
						<td width="15"><input onClick="activateLayer('<?php echo $iServer ; ?>','<?php echo $layernum; ?>')" <?php if( HTML_proxyWMS::isWMSLayerChecked($theServer,$name) || strcasecmp($theServer->Layers['All'],'True')==0) echo ' checked';?> type="checkbox"
							id="layer@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
							name="layer@<?php echo $iServer; ?>@<?php echo $layernum;?>"
						value="<?php echo $name;?>"></td>
						<td align="left"><?php echo $name; ?></td>
						</tr>
						<tr >
						<td colspan="2" align="left">"<?php echo $title;?>"
							</td>
						</tr>
					</table>		
				</td>
				<td align="center"><input <?php if(! HTML_proxyWMS::isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> type="text" size="10"
					id="scaleMin@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
					name="scaleMin@<?php echo $iServer; ?>@<?php echo $layernum;?>"
					value="<?php echo HTML_proxyWMS::getWMSLayerMinScale($theServer,$name); ?>"></td>
				<td align="center"><input <?php if(! HTML_proxyWMS::isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> type="text" size="10"
					id="scaleMax@<?php echo $iServer; ?>@<?php echo $layernum?>" 
					name="scaleMax@<?php echo $iServer; ?>@<?php echo $layernum;?>"
					value="<?php echo HTML_proxyWMS::getWMSLayerMaxScale($theServer,$name); ?>"></td>
				<td><textarea <?php if(! HTML_proxyWMS::isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> rows="3" cols="60"
					id="LocalFilter@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
					name="LocalFilter@<?php echo $iServer; ?>@<?php echo $layernum;?>"> 
					<?php $localFilter = HTML_proxyWMS::getWMSLayerLocalFilter($theServer,$name); if (!(strlen($localFilter)>	0)){} else {echo $localFilter;} ?></textarea></td>
			<td><input type="hidden" id="BBOX@<?php echo $iServer; ?>@<?php echo $layernum;?>" name="BBOX@<?php echo $iServer; ?>@<?php echo $layernum;?>" value=""></td>
			</tr>
			<?php 
			$layernum += 1;
					}
				}
			}?>
		</table>
		<input type="hidden" id="countLayer<?php echo $iServer; ?>" value="<?php echo $layernum; ?>">
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