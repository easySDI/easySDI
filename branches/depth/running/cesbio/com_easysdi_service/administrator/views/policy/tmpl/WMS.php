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
$document = &JFactory::getDocument();
$document->addStyleSheet('components/com_easysdi_service/assets/css/easysdi_service.css');
$document->addScript( JURI::root(true).'/administrator/components/com_easysdi_service/librairies/openlayers/OpenLayers.js' );
$document->addScript( JURI::root(true).'/administrator/components/com_easysdi_service/librairies/proj4js/lib/proj4js-combined.js' );

foreach ($this->xml->config as $config) {
	if (strcmp($config['id'],$this->config)==0){
		$policyFile = $config->{'authorization'}->{'policy-file'};
		$this->servletClass =  $config->{'servlet-class'};
		$servletVersion =  "";
		foreach($config->{"supported-versions"}->{"version"} as $versionConfig){
			if(strcmp($servletVersion, $versionConfig)< 0){
				$servletVersion = $versionConfig;
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
	Joomla.submitbutton = function(task)
	{
		if(task=='policy.cancel')
		{
			Joomla.submitform(task, document.getElementById('item-form'));	
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
				Joomla.submitform(task, document.getElementById('item-form'));	
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
	function activateLayer(server,layerName){


		if (document.getElementById('layer@'+server+'@'+layerName).checked==true){
			document.getElementById('scaleMin@'+server+'@'+layerName).disabled=false;
			document.getElementById('scaleMax@'+server+'@'+layerName).disabled=false;
			document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=false;
			
		}else{
			document.getElementById('AllLayers@'+server).checked = false;
			document.getElementById('scaleMin@'+server+'@'+layerName).disabled=true;
			document.getElementById('scaleMin@'+server+'@'+layerName).value ="";
			document.getElementById('scaleMax@'+server+'@'+layerName).disabled=true;
			document.getElementById('scaleMax@'+server+'@'+layerName).value ="";
			document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=true;
			document.getElementById('LocalFilter@'+server+'@'+layerName).value ="";	
		}
	}
	function disableServersLayers ()
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
				document.getElementById('scaleMin@'+nb+'@'+iLay).disabled=!check;
				document.getElementById('scaleMax@'+nb+'@'+iLay).disabled=!check;
				document.getElementById('LocalFilter@'+nb+'@'+iLay).disabled=!check;
				iLay ++;
			}
			iLay = 0;
			nb ++;
		}	
	}
	function disableLayers(iServ)
	{
		var iLay = 0;
		var check = document.getElementById('AllLayers@'+iServ).checked;
		
		while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
		{
			document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
			document.getElementById('scaleMin@'+iServ+'@'+iLay).disabled=check;
			document.getElementById('scaleMax@'+iServ+'@'+iLay).disabled=check;
			document.getElementById('LocalFilter@'+iServ+'@'+iLay).disabled=check;
			
			iLay ++;
		}
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
						<td width="15"><input onClick="activateLayer('<?php echo $iServer ; ?>','<?php echo $layernum; ?>')" <?php if( $this->isWMSLayerChecked($theServer,$name) || strcasecmp($theServer->Layers['All'],'True')==0) echo ' checked';?> type="checkbox"
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
				<td align="center"><input <?php if(! $this->isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> type="text" size="10"
					id="scaleMin@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
					name="scaleMin@<?php echo $iServer; ?>@<?php echo $layernum;?>"
					value="<?php echo $this->getWMSLayerMinScale($theServer,$name); ?>"></td>
				<td align="center"><input <?php if(! $this->isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> type="text" size="10"
					id="scaleMax@<?php echo $iServer; ?>@<?php echo $layernum?>" 
					name="scaleMax@<?php echo $iServer; ?>@<?php echo $layernum;?>"
					value="<?php echo $this->getWMSLayerMaxScale($theServer,$name); ?>"></td>
				<td><textarea <?php if(! $this->isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> rows="3" cols="60"
					id="LocalFilter@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
					name="LocalFilter@<?php echo $iServer; ?>@<?php echo $layernum;?>"> 
					<?php $localFilter = $this->getWMSLayerLocalFilter($theServer,$name); if (!(strlen($localFilter)>	0)){} else {echo $localFilter;} ?></textarea></td>
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

		
	</div>		 
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
	