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
		var waitFor = 0;
		var rtask = '';
		Joomla.submitbutton = function(task)
		{
			rtask= task;
			if(document.getElementById('crsSource').value == "" && 
					document.getElementById ('maxx').value == "" && 
					document.getElementById ('minx').value == "" && 
					document.getElementById ('maxy').value == "" &&
					document.getElementById ('miny').value == ""){
				document.getElementById ('maxxDestination').value = "";
				document.getElementById ('maxyDestination').value = "";
				document.getElementById ('minxDestination').value = "";
				document.getElementById ('minyDestination').value = "";
				Joomla.submitform(rtask, document.getElementById('item-form'));
			}
			try{
				var crsSource = document.getElementById('crsSource').value;
				var maxx = document.getElementById ('maxx').value;
				var minx = document.getElementById ('minx').value;
				var maxy = document.getElementById ('maxy').value;
				var miny = document.getElementById ('miny').value;
				if(crsSource == "" || maxx == "" || maxy == "" || minx == "" || miny == ""){
					alert ("Check your geographic filter definition.");
					return;
				}
				var source = new Proj4js.Proj(crsSource);
				var dest = new Proj4js.Proj('EPSG:4326');
				waitFor += 1;
				checkProjLoaded(minx, miny,maxx,maxy, source, dest)
			}catch (err){
				alert ("Check your geographic filter definition.");
				return;
			}	
		}

		function checkProjLoaded(minx, miny,maxx,maxy, source, dest) {
		    if (!source.readyToUse || !dest.readyToUse) {
		      window.setTimeout(Proj4js.bind(checkProjLoaded, this, minx, miny,maxx,maxy, source, dest,pressbutton), 500);
		    } else {
			    waitFor -= 1;
			    calculateBBOX(minx, miny,maxx,maxy, source, dest);
		    }
		}

		function calculateBBOX(minx, miny,maxx,maxy, source, dest){
			var pLowerEastCorner = new Proj4js.Point(new Array(minx,miny));   
			Proj4js.transform(source, dest, pLowerEastCorner);
			var pUpperWestCorner = new Proj4js.Point(new Array(maxx,maxy));   
			Proj4js.transform(source, dest, pUpperWestCorner);

			document.getElementById ('maxxDestination').value = pUpperWestCorner.x;
			document.getElementById ('maxyDestination').value = pUpperWestCorner.y;
			document.getElementById ('minxDestination').value = pLowerEastCorner.x;
			document.getElementById ('minyDestination').value = pLowerEastCorner.y;

			Joomla.submitform(rtask, document.getElementById('item-form'));
		}
		
		function disableOperationCheckBoxes()
		{
			var check = document.getElementById('AllOperations').checked;
			
			document.getElementById('oGetCapabilities').disabled=check;
			document.getElementById('oDescribeRecord').disabled=check;
			document.getElementById('oTransaction').disabled=check;
			document.getElementById('oGetRecords').disabled=check;
			document.getElementById('oGetRecordbyId').disabled=check;
			document.getElementById('oGetCapabilities').checked=check;
			document.getElementById('oDescribeRecord').checked=check;
			document.getElementById('oTransaction').checked=check;
			document.getElementById('oGetRecords').checked=check;
			document.getElementById('oGetRecordbyId').checked=check;

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
					<td><input type="checkBox" name="operation[]" id="oDescribeRecord" value="DescribeRecord" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'DescribeRecord')==0) echo 'checked';			
						}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_DESCRIBERECORD'); ?></td>
						<td><input type="checkBox" name="operation[]" id="oHarvest"  value="Harvest" disabled >
						<i><?php echo JText::_( 'PROXY_CONFIG_OPERATION_HARVEST'); ?></i></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetRecords" value="GetRecords" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'GetRecords')==0) echo 'checked';			
						}?>
						><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETRECORDS'); ?></td>
					<td><input type="checkBox" name="operation[]" id="oGetDomain" value="GetDomain" disabled >
					<i><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETDOMAIN'); ?></i></td>
				</tr>
				<tr>
					<td></td>
					<td><input type="checkBox" name="operation[]" id="oGetRecordbyId" value="GetRecordbyId" <?php if (strcasecmp($checkedO,'checked')==0){echo 'disabled checked';} ?>
						<?php foreach ($thePolicy->Operations->Operation as $operation)
						{
							if(strcasecmp($operation->Name,'GetRecordbyId')==0) echo 'checked';			
						}?>><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETRECORDBYID'); ?></td>
					<td></td>
				</tr>
			</table>
		</fieldset>
		
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_EASYSDI_MD_FILTER'); ?></legend>
		<table class="admintable">
		<tr>
		<th align="center"><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_OBJECTTYPE'); ?></th>
		<th align="center"><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_VISIBILITY'); ?></th>
		<th align="center"><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_STATUS'); ?></th>
		</tr>
		<tr>
		<td valign="top">
		<fieldset class="adminform">
			<table class="admintable">
				<tr>
					<td >
						<?php if (strcasecmp($thePolicy->ObjectTypes['All'],'True')==0 || !$thePolicy->ObjectTypes ){$checkedC='checked';} ?>	
						<input <?php echo $checkedC; ?>
							   type="checkBox" 
							   name="AllObjectType[]" 
							   id="AllObjectType" 
							   onclick="disableCheckBoxes('AllObjectType','objectType[]');">
							   <?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_OBJECTTYPE_ALL'); ?>
					</td>
					<?php 
					foreach ($rowsObjectTypes as $objectType)
					{
						?>
						<td>
						<input type="checkBox" 
							   name="objectType[]" 
							   id="<?php echo $objectType->value;?>" 
							   value="<?php echo $objectType->value;?>" 
							   <?php if (strcasecmp($checkedC,'checked')==0){echo 'disabled checked';} ?>
							   <?php foreach ($thePolicy->ObjectTypes->ObjectType as $policyObjectType)
							   {
							   		if(strcasecmp($objectType->value,$policyObjectType)==0) echo 'checked';			
							   }?>
						><?php echo JText::_($objectType->text); ?>
						</td>
						</tr>
						<tr>
						<td></td>
						<?php 
					}
					?>
				</tr>
			</table>
		</fieldset>
		</td>
		<td valign="top" >
		<fieldset class="adminform">
			<table class="admintable">
				<tr>
					<td >
						<?php if (strcasecmp($thePolicy->ObjectVisibilities['All'],'True')==0 || !$thePolicy->ObjectVisibilities){$checkedV='checked';} ?>	
						<input <?php echo $checkedV; ?>
							   type="checkBox" 
							   name="AllVisibilities[]" 
							   id="AllVisibilities" 
							   onclick="disableVisibilitiesCheckBoxes();">
							   <?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_VISIBILITY_ALL'); ?>
					</td>
					<?php 
					foreach ($rowsVisibility as $visibility)
					{
						?>
						<td>
						<input type="checkBox" 
							   name="visibility[]" 
							   id="<?php echo $visibility->value;?>" 
							   value="<?php echo $visibility->value;?>" 
							   <?php if (strcasecmp($checkedV,'checked')==0){echo 'disabled checked';} ?>
							   <?php foreach ($thePolicy->ObjectVisibilities->Visibility as $policyVisibility)
							   {
							   		if(strcasecmp($visibility->value,$policyVisibility)==0) echo 'checked';			
							   }?>
						><?php echo $visibility->text; ?>
						</td>
						</tr>
						<tr>
						<td></td>
						<?php 
					}
					?>
				</tr>
			</table>
		</fieldset>
		</td>
		<td valign="top">
		<fieldset class="adminform">
			<table class="admintable">
				<tr>
					<td >
						<?php if (strcasecmp($thePolicy->ObjectStatus['All'],'True')==0 || !$thePolicy->ObjectStatus ){$checkedS='checked';} ?>	
						<input <?php echo $checkedS; ?>
							   type="checkBox" 
							   name="AllStatus[]" 
							   id="AllStatus" 
							   onclick="disableStatusCheckBoxes();">
							   <?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_STATUS_ALL'); ?>
					</td>
					<?php 
					foreach ($rowsStatus as $status)
					{
						if (strcasecmp($status->value, 'published')==0)
						{
							$versionMode = "all";
							?>
							<td>
							<input type="checkBox" 
								   name="status[]" 
								   id="<?php echo $status->value;?>" 
								   value="<?php echo $status->value;?>" 
								   onclick="disableVersionModeRadio();"
								   <?php if (strcasecmp($checkedS,'checked')==0){echo 'disabled checked';} ?>
								   <?php foreach ($thePolicy->ObjectStatus->Status as $policyStatus)
								   {
								   		if(strcasecmp($status->value,$policyStatus)==0) {
								   			echo 'checked';
								   			$versionMode =  $policyStatus['version'];
								   		}			
								   }?>
							><?php echo JText::_($status->text); ?>
							</td>
							<td><i><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_STATUS_VERSION_MODE'); ?></i></td>
							<td><input type="radio" name="objectversion_mode" value="last" <?php if (strcmp($versionMode,"last")==0 ){echo "checked";} ?>  <?php if (strcasecmp($checkedS,'checked')==0){echo 'disabled';} ?>> <?php echo JText::_( 'PROXY_CONFIG_VERSION_MANAGEMENT_MODE_LAST'); ?><br></td>
							<td><input type="radio" name="objectversion_mode" value="all" <?php if (strcmp($versionMode,"all")==0){echo "checked";} ?>  <?php if (strcasecmp($checkedS,'checked')==0){echo 'disabled';} ?> > <?php echo JText::_( 'PROXY_CONFIG_VERSION_MANAGEMENT_MODE_ALL'); ?><br></td>
							<?php 
						}
						else
						{
							?>
							<td>
							<input type="checkBox" 
								   name="status[]" 
								   id="<?php echo $status->value;?>" 
								   value="<?php echo $status->value;?>" 
								   <?php if (strcasecmp($checkedS,'checked')==0){echo 'disabled checked';} ?>
								   <?php foreach ($thePolicy->ObjectStatus->Status as $policyStatus)
								   {
								   		if(strcasecmp($status->value,$policyStatus)==0) {
								   			echo 'checked';
								   			$versionMode =  $policyStatus['version'];
								   		}			
								   }?>
							><?php echo JText::_($status->text); ?>
							</td>
							<td></td>
							<td></td>
							<td></td>
							<?php
						}
						?>
						</tr>
						<tr>
						<td></td>
						<?php 
					}
					?>
				</tr>
			</table>
		</fieldset>
		</td>
		
		</tr>
		</table>
	</fieldset>
	<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_HARVESTED_MD_FILTER'); ?></legend>
			<table class="admintable">
				<tr>
					<td >
					<?php echo JText::_( 'PROXY_CONFIG_HARVESTED_MD_INCLUDE'); ?>
					</td>
					<td >
					<input type="radio" name="IncludeHarvested" value="true" <?php if($thePolicy->IncludeHarvested != "false") echo "checked"; ?>> <?php echo JText::_( 'CORE_YES'); ?>
					<input type="radio" name="IncludeHarvested" value="false" <?php if($thePolicy->IncludeHarvested == "false") echo "checked"; ?>> <?php echo JText::_( 'CORE_NO'); ?>
					</td>
				</tr>
			</table>
		</fieldset>
		
		
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_CSW_GEOGRAPHICAL_FILTER'); ?> 
			</legend>
			<table class="admintable">
				<tr>
					<td colspan="4" >
						<b><?php echo JText::_( 'PROXY_CONFIG_CSW_GEOGRAPHIC_FILTER_CRS'); ?>   </b>	
						<input type="text" size="35" maxlength="100" name="crsSource" id="crsSource" title="<?php echo JText::_( 'PROXY_CONFIG_CSW_GEOGRAPHIC_FILTER_CRS_TITLE'); ?>" value="<?php echo $thePolicy->BBOXFilter['crsSource'];?>">
					</td>
				</tr>
				<tr></tr>
				<tr>
					<td>
					</td>
					<td align="right">
						<?php echo JText::_( 'PROXY_CONFIG_BBOX_MAXY'); ?>
					</td>
					<td>
						<input type="text" name="maxy" id="maxy" value="<?php echo $thePolicy->BBOXFilter['maxy'];?>">
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
						<input type="text" name="minx" id="minx" value="<?php echo $thePolicy->BBOXFilter['minx'];?>">
					</td>
					<td align="right">
						<?php echo JText::_( 'PROXY_CONFIG_BBOX_MAXX'); ?>
					</td>
					<td>
						<input type="text" name="maxx" id="maxx" value="<?php echo $thePolicy->BBOXFilter['maxx'];?>">
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
						<input type="text" name="miny" id="miny" value="<?php echo $thePolicy->BBOXFilter['miny'];?>">
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
			</table>
			<input type="hidden" name="minyDestination"	id="minyDestination"    value="<?php echo $thePolicy->BBOXFilter->miny;?>">
			<input type="hidden" name="minxDestination" id="minxDestination"	value="<?php echo $thePolicy->BBOXFilter->minx;?>">
			<input type="hidden" name="maxyDestination" id="maxyDestination"	value="<?php echo $thePolicy->BBOXFilter->maxy;?>">
			<input type="hidden" name="maxxDestination" id="maxxDestination"	value="<?php echo $thePolicy->BBOXFilter->maxx;?>">
		</fieldset>
		<?php
		$remoteServerList = $config->{'remote-server-list'};
		$iServer=0;

		foreach ($remoteServerList->{'remote-server'} as $remoteServer){
		?>			
			<input type="hidden" name="remoteServer<?php echo $iServer;?>"	value="<?php echo $remoteServer->url ?>">
			
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_CSW_SERVER'); ?> <?php echo $remoteServer->url ?> <input type="button" value="<?php echo JText::_( 'EASYSDI_ADD NEW PARAM');?>" onClick="addNewMetadataToExclude('nbParam<?php echo $iServer; ?>',<?php echo $iServer; ?>);"></legend>
				<table  class="admintable">
				<thead>
					<tr>
						<th><b><?php echo JText::_( 'EASYSDI_ATTRIBUTE TO EXCLUDE'); ?></b></th>		
					</tr>
					</thead>
					<tbody id="metadataParamTable">
					<?php 
						foreach ($thePolicy->Servers->Server as $policyServer)
						{			
							if (strcmp($policyServer->url,$remoteServer->url)==0)
							{
								$theServer  = $policyServer;
								break;
							}
						}
						$iparam  =0;
						if ($theServer && $theServer !=null && $theServer->{'Metadata'} !=null && $theServer->{'Metadata'}->{'Attributes'}!=null && $theServer->{'Metadata'}->{'Attributes'}->{'Exclude'} !=null && $theServer->{'Metadata'}->{'Attributes'}->{'Exclude'}->{'Attribute'} !=null)
						{
							foreach ($theServer->{'Metadata'}->{'Attributes'}->{'Exclude'}->{'Attribute'} as $attributeToExclude)
							{			
								?>
								<tr>
									<td><input name="param_<?php echo $iServer;?>_<?php echo $iparam;?>" type="text" class="text_area" size="200" value='<?php echo $attributeToExclude; ?>'></td>
								</tr>
								<?php 
								$iparam  ++;
							} 
						}?>
						<input type ="hidden" id="nbParam<?php echo $iServer; ?>"  value="<?php echo $iparam;?>">
					</tbody>
				</table>
			</fieldset>	
		<?php	
		$iServer = $iServer +1;
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
	