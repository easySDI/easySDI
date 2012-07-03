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
		function submitbutton(pressbutton)
		{
			if(pressbutton=='cancelPolicy')
			{
				submitform(pressbutton);	
			}
			else
			{
				
				var server = 0;
				while (document.getElementById('serverPrefixe'+server) != null)
				{
					if(document.getElementById('serverPrefixe'+server).value == "" || document.getElementById('serverNamespace'+server).value == "")
					{
						alert ('<?php echo  JText::_( 'EASYSDI_NAMESPACE_VALIDATION_ERROR');?>');	
						return;
					}
					server ++;
				}
				if(geoQueryValid.length != 0)
				{
					alert ('<?php echo  JText::_( 'EASYSDI_QUERY_VALIDATION_ERROR');?>');
				}
				else
				{
					submitform(pressbutton);
				}
			}
		}
		
		function disableOperationCheckBoxes()
		{
			var check = document.getElementById('AllOperations').checked;
			
			document.getElementById('oGetCapabilities').disabled=check;
			document.getElementById('oTransaction').disabled=check;
			document.getElementById('oDescribeFeatureType').disabled=check;
			document.getElementById('oGetFeature').disabled=check;
			document.getElementById('oGetCapabilities').checked=check;
			document.getElementById('oTransaction').checked=check;
			document.getElementById('oDescribeFeatureType').checked=check;
			document.getElementById('oGetFeature').checked=check;
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
					<td><input type="checkBox" name="operation[]" id="oTransaction" value="Transaction" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'Transaction')==0) echo 'checked';			
						}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_TRANSACTION'); ?></td>
					
				</tr>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oDescribeFeatureType" value="DescribeFeatureType" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
							{
								if(strcasecmp($operation->Name,'DescribeFeatureType')==0) echo 'checked';			
							}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_DESCRIBEFEATURETYPE'); ?></td>
						<td><input type="checkBox" name="operation[]" id="oLockFeature" value="LockFeature" disabled>
							<i><?php echo JText::_( 'PROXY_CONFIG_OPERATION_LOCKFEATURE'); ?></i></td>
					
				</tr>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetFeature"  value="GetFeature" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'GetFeature')==0) echo 'checked';			
						}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETFEATURE'); ?></td>
					<td><input type="checkBox" name="operation[]" id="oGetFeatureWithLock" value="GetFeatureWithLock" disabled >
						<i><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETFEATUREWITHLOCK'); ?></i></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_SERVER_ALL_TITLE'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input type="checkBox" name="AllServers[]" id="AllServers" value="All" onclick="disableServersFeatureTypes();" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo 'checked'; ?>><?php echo JText::_( 'PROXY_CONFIG_SERVER_ALL'); ?></td>
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
					$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&version=".$servletVersion."&SERVICE=WFS");
				}else{
					$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WFS");
				}
				
			if ($xmlCapa === false){
					global $mainframe;		
							$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' ),'error');
			}
			else{
	
			foreach ($thePolicy->Servers->Server as $policyServer)
			{
				if (strcmp($policyServer->{'url'},$remoteServer->url)==0)
				{
					$theServer  = $policyServer;
				}
			}
			?>
			<input type="hidden" id ="remoteServer<?php echo $iServer; ?>" name="remoteServer<?php echo $iServer; ?>" value="<?php echo $remoteServer->url; ?>">
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_WFS_SERVER'); ?> <?php echo $remoteServer->url ?></legend>
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
					<th><b><?php echo JText::_( 'EASYSDI_FEATURETYPE NAME'); ?></b></th>
					<th><b><?php echo JText::_( 'EASYSDI_FILTERED_ATTRIBUTES_LABEL'); ?></b>
					<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpAttributeFilter" rel="{handler:'iframe',size:{x:600,y:190}}"> 	
						<img class="helpTemplate" 
						     src="components/com_easysdi_service/assets/images/s_help.png" 
							 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
							
							  />	
					</a></th>
					<th><b><?php echo JText::_( 'EASYSDI_GEOGRAPHIC_FILTER_ON_QUERY'); ?></b>
					<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpQueryTemplate&filter_type=filterQuery" rel="{handler:'iframe',size:{x:600,y:290}}"> 	
						<img class="helpTemplate" 
						     src="components/com_easysdi_service/assets/images/s_help.png" 
							 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
							
							  />	
					</a>
					</th>
					
					<th><b><?php echo JText::_( 'EASYSDI_GEOGRAPHIC_FILTER_ON_ANSWER'); ?></b>
					<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpQueryTemplate&filter_type=filterAnswer" rel="{handler:'iframe',size:{x:600,y:320}}"> 
						<img class="helpTemplate" 
							  src="components/com_easysdi_service/assets/images/s_help.png" 
							 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
							 />
					</a>
					</th>		
				</tr>
				<tr>
					<td colspan="4"><input type="checkBox" name="AllFeatureTypes@<?php echo $iServer; ?>" id="AllFeatureTypes@<?php echo $iServer; ?>" value="All" <?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' checked '; ?> onclick="disableFeatureTypes(<?php echo $iServer; ?>);"><?php echo JText::_( 'PROXY_CONFIG_FEATURES_ALL'); ?></td>
				</tr>
				<?php
				$pos1 = stripos($urlWithPassword, "?");
						$separator = "&";
						if ($pos1 === false) {
				    		//"?" Not found then use ? instead of &
				    		$separator = "?";  
						}
				
				$ftnum = 0;
				foreach ($xmlCapa->{'FeatureTypeList'}->{'FeatureType'} as $featureType){
						$xmlDescribeFeature = simplexml_load_file($urlWithPassword.$separator."VERSION=1.0.0&REQUEST=DescribeFeatureType&SERVICE=WFS&TYPENAME=".$featureType->{'Name'});
					if ($xmlDescribeFeature === false){
								global $mainframe;		
								$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO DESCRIBE THE FEATURE TYPE OF THE REMOTE SERVER.' ),'error');
						}
				
				 foreach ($xmlDescribeFeature->children('http://www.w3.org/2001/XMLSchema') as $entry) {
				 	$element = $entry->{'complexContent'}->{'extension'}->{'sequence'}->{'element'};
				 	for ($i=0;$i<count($element);$i++){
				 		$a = $element[$i]->attributes();
				 		$type = substr($a['type'],strrpos($a['type'], ":")+1);
				 		$geometryName = '';
				 		switch($type){
				 			case 'AbstractGeometryType':
				 			case 'PointType':
				 			case 'PointPropertyType':
				 			case 'CurvePropertyType':
				 			case 'OrientableCurveType':
				 			case 'CompositeCurveType':
				 			case 'AbstractCurveSegmentType':
				 			case 'LineStringType':
				 			case 'ArcStringType':
				 			case 'ArcType':
				 			case 'CircleType':
				 			case 'ArcStringByBulgeType':
				 			case 'ArcByBulgeType':
				 			case 'ArcByCenterPointType':
				 			case 'CircleByCenterPointType':
				 			case 'CubicSplineType':
				 			case 'KnotType':
				 			case 'KnotPropertyType':
				 			case 'BSplineType':
				 			case 'BezierType':
				 			case 'SurfacePropertyType':
				 			case 'SurfaceArrayPropertyType':
				 			case 'OrientableSurfaceType':
				 			case 'CompositeSurfaceType':
				 			case 'PolygonType':
				 			case 'TriangleType':
				 			case 'RectangleType':
				 			case 'RingType':
				 			case 'RingPropertyType':
				 			case 'LinearRingType':
				 			case 'LinearRingPropertyType':
				 			case 'SolidPropertyType':
				 			case 'SolidArrayPropertyType':
				 			case 'SolidType':
				 			case 'CompositeSolidType':
				 			case 'GeometricComplexType':
				 			case 'GeometricComplexPropertyType':
				 			case 'MultiGeometryType':
				 			case 'MultiGeometryPropertyType':
				 			case 'MultiPointType':
				 			case 'MultiPointPropertyType':
				 			case 'MultiCurveType':
				 			case 'MultiCurvePropertyType':
				 			case 'MultiSurfaceType':
				 			case 'MultiSurfacePropertyType':
				 			case 'MultiSolidType':
				 			case 'MultiSolidPropertyType':
				 			case 'DirectPositionType':
				 			case 'CoordType':
				 			case 'MultiLineStringType':
				 			case 'MultiPolygonType':
				 			case 'LineStringPropertyType':
				 			case 'PolygonPropertyType':
				 			case 'MultiLineStringPropertyType':
				 			case 'MultiPolygonPropertyType':
				 			case 'GeometryPropertyType':
				 			case 'GeometryArrayPropertyType':
				 			case 'GeometricPrimitivePropertyType':
				 			case 'GeometryAssociationType':
				 				$geometryName  = $a['name'];
				 				break;
				 		}
				 	}
				 }
				   	 	 	
				 ?>	 
				<tr>
					<td class="key" >
						<table width ="100%" height="100%" >
							<tr valign="top" >
								<td width="15">
									<input align="left" 
										   onClick="activateFeatureType('<?php echo $iServer; ?>','<?php echo $ftnum;?>')" 
										   <?php if( $this->isChecked($theServer,$featureType)) echo 'checked';?> 
										   type="checkbox"
										  id="featuretype@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
								          name="featuretype@<?php echo $iServer; ?>@<?php echo $ftnum; ?>" 
								          value="<?php echo $featureType->{'Name'}; ?>"></td>
								<td align="left"><?php echo $featureType->{'Name'}; ?></td>
							</tr>
							<tr >
								<td colspan="2" align="left">
									"<?php if (strrpos($featureType->{'Title'}, ":") === false) echo $featureType->{'Title'}; else echo substr($featureType->{'Title'},strrpos($featureType->{'Title'}, ":")+1);?>"
								</td>
							</tr>
						</table>		
					</td>
					<td  align="center">
						<table>
							<tr>
								<td><?php echo JText::_( 'EASYSDI_FEATURE_TYPE_SELECT_ATTRIBUTE'); ?> 
									<input align="left" 
										   <?php if( ! $this->isChecked($theServer,$featureType)) echo 'disabled ';?> 
										   <?php  $attributes =  $this->getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){}else{echo 'checked '; } ?>
										   onClick="activateAttributeList('<?php echo $iServer; ?>','<?php echo $ftnum; ?>')" type="checkbox"
										   id="selectAttribute@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
										   name="selectAttribute@<?php echo $iServer; ?>@<?php echo $ftnum; ?>" 
										   value="" 
								           <?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>/>
								</td>
							</tr>
							<tr>
								<td>
									<textarea rows="2" 
											  cols="22" 
											  <?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>
											  <?php  $attributes =  $this->getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){echo 'disabled';}?>
												id="AttributeList@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
												name="AttributeList@<?php echo $iServer; ?>@<?php echo $ftnum; ?>">
												<?php $attributes =  $this->getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){}else{echo $attributes; }?>
									</textarea>
								</td>
							</tr>
						</table>
					</td> 
					<td>
						<textarea rows="4" 
								  cols="50" 
								  onChange="CheckQuery('<?php echo $iServer; ?>','<?php echo $ftnum; ?>')" 
								  <?php if( ! $this->isChecked($theServer,$featureType)) echo 'disabled';?>
								 id="RemoteFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
								 name="RemoteFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>" <?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>>
								 <?php $remoteFilter = $this->getFeatureTypeRemoteFilter($theServer,$featureType); if (strcmp($remoteFilter,"")!=0){echo $remoteFilter;}?>
						</textarea>
					</td>
					<td>
						<textarea rows="4" 
								  cols="50" 
								  onChange="CheckQuery('<?php echo $iServer; ?>','<?php echo $featureType->{'Name'}; ?>')" 
								  <?php if( ! $this->isChecked($theServer,$featureType)) echo 'disabled';?>
									<?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>
										id="LocalFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
										name="LocalFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"><?php $localFilter = $this->getFeatureTypeLocalFilter($theServer,$featureType); 
										if (strcmp($localFilter,"")==0)
										{	
										} 
										else 
										{
											echo $localFilter;
										} ?>
						</textarea>		
					</td>
				</tr>
				
				<?php
					$ftnum += 1; 
				}
				?>
			</table>
			
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
	