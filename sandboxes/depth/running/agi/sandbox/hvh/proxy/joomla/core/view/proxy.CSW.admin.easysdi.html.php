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

class HTML_proxyCSW {

		/**
	 * 
	 * Edit configuration for CSW servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigCSW($xml,$new, $configId,$availableServletList,$availableVersion, $option, $task)
	{
		?>

	<form name='adminForm' id='adminForm' action='index.php' method='POST'>
		<input type='hidden' name='serviceType' id='serviceType' value="CSW" >
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
				
				HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.csw.CSWProxyServlet", $availableServletList,$availableVersion,"CSW")
		?>
			<fieldset class="adminform" id="ogcSearchFilterFS"><legend><?php echo JText::_( 'PROXY_CONFIG_CSW_OGC_SEARCH_FILTER' );?></legend>
			<table class="admintable">
				<tr>
					<td colspan="4"><input type='text' name='ogcSearchFilter'
						value='<?php echo $config->{"ogc-search-filter"}; ?>'></td>
				</tr>
			</table>
			</fieldset>
			
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
								<tr>
									<td class="key" id="service_contactlinkage_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_LINKAGE"); ?> : </td>
									<td colspan="2"><input name="service_contactlinkage" id="service_contactlinkage" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"Linkage"}; ?>"></td>
								</tr>
								<tr>
									<td class="key" id="service_contacthours_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_HOURS"); ?> : </td>
									<td colspan="2"><input name="service_contacthours" id="service_contacthours" type="text" size="80" value="<?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"HoursofSservice"}; ?>"></td>
								</tr>
								<tr>
									<td class="key" id="service_contactinstructions_t"><?php echo JText::_("PROXY_CONFIG_SERVICE_METADATA_CONTACT_INSTRUCTIONS"); ?> : </td>
									<td colspan="2"><textarea name="service_contactinstructions" id="service_contactinstructions"  cols="45" rows="5"  ><?php echo $config->{"service-metadata"}->{"ContactInformation"}->{"Instructions"}; ?></textarea></td>
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
	 * 
	 * Generate CSW form
	 * @param XML  $config
	 * @param XML $thePolicy
	 */
	function generateCSWHTML($config,$thePolicy, $rowsVisibility, $rowsStatus, $rowsObjectTypes,$servletVersion)
	{
	?>
		<script>
		var waitFor = 0;
		function submitbutton(pressbutton)
		{
			if(document.getElementById('crsSource').value == "" && 
					document.getElementById ('maxx').value == "" && 
					document.getElementById ('minx').value == "" && 
					document.getElementById ('maxy').value == "" &&
					document.getElementById ('miny').value == ""){
				document.getElementById ('maxxDestination').value = "";
				document.getElementById ('maxyDestination').value = "";
				document.getElementById ('minxDestination').value = "";
				document.getElementById ('minyDestination').value = "";
				submitform(pressbutton);
				return;
			}
			try{
				var crsSource = document.getElementById('crsSource').value;
				var maxx = document.getElementById ('maxx').value;
				var minx = document.getElementById ('minx').value;
				var maxy = document.getElementById ('maxy').value;
				var miny = document.getElementById ('miny').value;
				if(crsSource == "" || maxx == "" || maxy == "" || minx == "" || miny == ""){
					alert ("Veuillez vérifier la définition de votre filtre géographique.");
					return;
				}
				var source = new Proj4js.Proj(crsSource);
				var dest = new Proj4js.Proj('EPSG:4326');
				waitFor += 1;
				checkProjLoaded(minx, miny,maxx,maxy, source, dest,pressbutton)
			}catch (err){
				alert ("Veuillez vérifier la définition de votre filtre géographique.");
				return;
			}	
		}

		function checkProjLoaded(minx, miny,maxx,maxy, source, dest,pressbutton) {
		    if (!source.readyToUse || !dest.readyToUse) {
		      window.setTimeout(Proj4js.bind(checkProjLoaded, this, minx, miny,maxx,maxy, source, dest,pressbutton), 500);
		    } else {
			    waitFor -= 1;
			    calculateBBOX(minx, miny,maxx,maxy, source, dest,pressbutton);
		    }
		}

		function calculateBBOX(minx, miny,maxx,maxy, source, dest,pressbutton){
			var pLowerEastCorner = new Proj4js.Point(new Array(minx,miny));   
			Proj4js.transform(source, dest, pLowerEastCorner);
			var pUpperWestCorner = new Proj4js.Point(new Array(maxx,maxy));   
			Proj4js.transform(source, dest, pUpperWestCorner);

			document.getElementById ('maxxDestination').value = pUpperWestCorner.x;
			document.getElementById ('maxyDestination').value = pUpperWestCorner.y;
			document.getElementById ('minxDestination').value = pLowerEastCorner.x;
			document.getElementById ('minyDestination').value = pLowerEastCorner.y;

			submitform(pressbutton);
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
	}
	
}
?>