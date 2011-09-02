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

class HTML_proxyWFS {

	
	function getFeatureTypeAttributesList($theServer,$featureType){

		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
		
		foreach ($theServer->FeatureTypes->FeatureType as $ft )
		{
				if (strcmp($ft->{'Name'},$featureType->{'Name'})==0)
				{
					return HTML_proxyWFS::buildAttributesListString($ft->Attributes);
				}
			}
		return "";
	}
	
	function buildAttributesListString ($Attributes)
	{
		$attString ="";
		foreach($Attributes->{'Attribute'} as $att)
		{
			$attString .= $att;
			$attString .= ",";
		}
		$attString = substr  ($attString, 0, strlen ($attString)-1 );
		return $attString;
	}
	
		
	function getFeatureTypeRemoteFilter($theServer,$featureType)
	{
		if (count($theServer->FeatureTypes->FeatureType )==0) return "";

		foreach ($theServer->FeatureTypes->FeatureType as $ft ){

				if (strcmp($ft->{'Name'},$featureType->{'Name'})==0)
				{
					return $ft->{'RemoteFilter'};
				}
			}

		return "";
	}

	function getFeatureTypeLocalFilter($theServer,$featureType){

		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){

			if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){

				return $ft->{'LocalFilter'};
			}	
			}
		return "";
	}

	function getLayerLocalFilter($theServer,$layer){

		if (count($theServer->Layers->Layer)==0) return "";


		foreach ($theServer->Layers->Layer as $theLayer )
		{
				if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
				{
					return $theLayer->{'Filter'};
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
	
	function isChecked($theServer,$featureType){

		if (strcasecmp($theServer->{"FeatureTypes"}['All'],"true")==0) return true;
		
		if (count($theServer->FeatureTypes->FeatureType )==0) return false;
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){
				if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){
					
					return true;
				}

			}
		return false;


	}

	
	
	/**
	 * 
	 * Edit configuration for WFS servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigWFS($xml,$new, $configId,$availableServletList,$availableVersion, $option, $task)
	{
		?><form name='adminForm' id='adminForm' action='index.php' method='POST'>
			<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >
			<input type='hidden' name="isNewConfig" value="<?php echo $new; ?>">
			<input type='hidden' name='option' value='<?php echo $option;?>'> 
			<input type='hidden' name='task' value='<?php echo $task;?>'> 
			<input type='hidden' name='configId' value='<?php echo $configId;?>'> 
			<input type='hidden' name="nbServer" id="nbServer" value=''>	
			<?php
			foreach ($xml->config as $config) {
				if (strcmp($config['id'],$configId)==0){
					$servletClass=$config->{'servlet-class'};
					$servletVersion=$config->{'servlet-version'};
					$keywordString = "";
					foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
					{
						$keywordString .= $keyword .",";
					}
					$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
					HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.wfs.WFSProxyServlet", $availableServletList,$availableVersion,$servletVersion)
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
	 * 
	 * Generate WFS form
	 * @param XML  $config
	 * @param XML $thePolicy
	 */
	function generateWFSHTML($config,$thePolicy,$servletVersion)
	{
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
			//$policyServerList = $thePolicy->xpath('//Server');

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
						     src="../templates/easysdi/icons/silk/help.png" 
							 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
							
							  />	
					</a></th>
					<th><b><?php echo JText::_( 'EASYSDI_GEOGRAPHIC_FILTER_ON_QUERY'); ?></b>
					<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpQueryTemplate&filter_type=filterQuery" rel="{handler:'iframe',size:{x:600,y:290}}"> 	
						<img class="helpTemplate" 
						     src="../templates/easysdi/icons/silk/help.png" 
							 alt="<?php echo JText::_("EASYSDI_GEOGRAPHIC_FILTER_QUERY_TEMPLATE") ?>" 
							
							  />	
					</a>
					</th>
					
					<th><b><?php echo JText::_( 'EASYSDI_GEOGRAPHIC_FILTER_ON_ANSWER'); ?></b>
					<a class="modal" href="./index.php?option=com_easysdi_proxy&tmpl=component&task=helpQueryTemplate&filter_type=filterAnswer" rel="{handler:'iframe',size:{x:600,y:320}}"> 
						<img class="helpTemplate" 
							 src="../templates/easysdi/icons/silk/help.png" 
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
										   <?php if( HTML_proxyWFS::isChecked($theServer,$featureType)) echo 'checked';?> 
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
										   <?php if( ! HTML_proxyWFS::isChecked($theServer,$featureType)) echo 'disabled ';?> 
										   <?php  $attributes =  HTML_proxyWFS::getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){}else{echo 'checked '; } ?>
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
											  <?php  $attributes =  HTML_proxyWFS::getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){echo 'disabled';}?>
												id="AttributeList@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
												name="AttributeList@<?php echo $iServer; ?>@<?php echo $ftnum; ?>">
												<?php $attributes =  HTML_proxyWFS::getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){}else{echo $attributes; }?>
									</textarea>
								</td>
							</tr>
						</table>
					</td> 
					<td>
						<textarea rows="4" 
								  cols="50" 
								  onChange="CheckQuery('<?php echo $iServer; ?>','<?php echo $ftnum; ?>')" 
								  <?php if( ! HTML_proxyWFS::isChecked($theServer,$featureType)) echo 'disabled';?>
								 id="RemoteFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
								 name="RemoteFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>" <?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>>
								 <?php $remoteFilter = HTML_proxyWFS::getFeatureTypeRemoteFilter($theServer,$featureType); if (strcmp($remoteFilter,"")!=0){echo $remoteFilter;}?>
						</textarea>
					</td>
					<td>
						<textarea rows="4" 
								  cols="50" 
								  onChange="CheckQuery('<?php echo $iServer; ?>','<?php echo $featureType->{'Name'}; ?>')" 
								  <?php if( ! HTML_proxyWFS::isChecked($theServer,$featureType)) echo 'disabled';?>
									<?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>
										id="LocalFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
										name="LocalFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"><?php $localFilter = HTML_proxyWFS::getFeatureTypeLocalFilter($theServer,$featureType); 
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
	}
	
	
}
?>