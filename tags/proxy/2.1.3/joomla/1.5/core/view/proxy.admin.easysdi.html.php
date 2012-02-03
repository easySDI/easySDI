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

class HTML_proxy {

	
function configComponent($xmlConfig){

	JToolBarHelper::title( JText::_(  'EASYSDI_EASYSDI_CONFIGURE COMPONENT' ), 'generic.png' );

	$option = JRequest::getVar('option'); 

	?>
		
	<form name='adminForm' id='adminForm' action='index.php' method='POST'>	
	<input
	type='hidden' name='option' value='<?php echo $option;?>'> <input
	type='hidden' name='task' value=''> 
	
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_EASYSDI_CONFIG FILE PATH' );?></legend>
<table class="admintable">
	<tr>
		<td colspan="4"><input type='text' name='filePath' size="200"
			value='<?php echo $xmlConfig->proxy->configFilePath;?>'></td>
	</tr>
</table>
</fieldset>

</form>

<?php
}
	
	function ctrlPanel(){
		JToolBarHelper::title( JText::_(  'EASYSDI_EASYSDI CONTROL PANEL' ), 'generic.png' );
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
			
$pane		=& JPane::getInstance('sliders');
	echo $pane->startPane("content-pane");
			echo $pane->startPanel( JText::_('EASYSDI_EASYSDI MODULES'), 'cpanel-panel-1' );
		?>
	<div id="cpanel">
		<?php
		$link = 'index.php?option=com_easysdi&amp;task=componentConfig';
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
				
					<?php 
					$text = JText::_( 'EASYSDI_COMPONENT CONFIGURATION' );
					echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
		
	<?php	
		$link = 'index.php?option=com_easysdi&amp;task=showConfigList';


?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php 
						$text = JText::_( 'EASYSDI_PROXY CONFIGURATION' );					
						echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
	
	</div> 
		<?php
echo $pane->endPanel();
	?>
<div id="rightcpanel">
<?php 

	
		echo $pane->startPanel( JText::_('EASYSDI_LICENSE'), 'cpanel-panel-licence' );
		?><PRE>
		<?php 		 
		$file = file_get_contents (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi'.DS.'license.txt');
		Echo $file;
		?></PRE>
		<?php
		echo $pane->endPanel();
	

	echo $pane->endPane();
	?>
	
	</div>
	
		<?php 
	}
	
	function getFeatureTypeAttributesList($theServer,$featureType){

		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
		
		foreach ($theServer->FeatureTypes->FeatureType as $ft )
		{
				if (strcmp($ft->{'Name'},$featureType->{'Name'})==0)
				{
					return HTML_proxy::buildAttributesListString($ft->Attributes);
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

	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0){
		  foreach($rows as $key => $row) {		  	
       		$rows[$key]->text = JText::_($rows[$key]->text);
  		}			    
		}
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
	function editConfigWFS($xml,$new, $configId,$availableServletList, $option, $task)
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
					$keywordString = "";
					foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
					{
						$keywordString .= $keyword .",";
					}
					$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
					HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.wfs.WFSProxyServlet", $availableServletList)
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
	 * Edit configuration for WMS servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigWMS($xml,$new, $configId,$availableServletList, $option, $task)
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
					$keywordString = "";
					foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
					{
						$keywordString .= $keyword .",";
					}
					$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;

					HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.wms.WMSProxyServlet", $availableServletList)
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
	 * 
	 * Edit configuration for CSW servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigCSW($xml,$new, $configId,$availableServletList, $option, $task)
	{
		?>

	<form name='adminForm' id='adminForm' action='index.php' method='POST'>
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
				$keywordString = "";
				foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
				{
					$keywordString .= $keyword .",";
				}
				$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
				
				HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.csw.CSWProxyServlet", $availableServletList)
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
	 * Edit configuration for WMTS 1.0.0 servlet
	 * @param unknown_type $xml
	 * @param unknown_type $new
	 * @param unknown_type $configId
	 * @param unknown_type $availableServletList
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	function editConfigWMTS100($xml,$new, $configId,$availableServletList, $option, $task)
	{
		?>

	<form name='adminForm' id='adminForm' action='index.php' method='POST'>
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
				$keywordString = "";
				foreach ($config->{"service-metadata"}->{"ServiceIdentification"}->{'KeywordList'}->Keyword as $keyword)
				{
					$keywordString .= $keyword .",";
				}
				$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
				
				HTML_proxy::genericServletInformationsHeader ($config, $configId, "org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet", $availableServletList)
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
	 * 
	 * Generic servlet informations form :
	 * - config Id
	 * - servlet type
	 * - host translator
	 * @param unknown_type $config
	 */
	function genericServletInformationsHeader ($config, $configId, $servletClass, $availableServletList)
	{
		?>
		<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_CONFIG ID' );?></legend>
			<table class="admintable">
				<tr>
					<td colspan="4"><input type='text' name='newConfigId'
						value='<?php echo $configId;?>'></td>
				</tr>
			</table>
			</fieldset>
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_SERVLET TYPE' );?></legend>
			
			<table class="admintable">
				<tr>
					<td>
					<?php echo JHTML::_("select.genericlist",$availableServletList, 'servletClass', 'size="1" onChange="submit()"', 'value', 'text', $servletClass ); ?>
					</td>
				</tr>
			</table>
			</fieldset>
			
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_HOST TRANSLATOR'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input size="100" type="text" name="hostTranslator"
						value="<?php  echo $config->{'host-translator'}; ?>"></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_SERVER LIST'); ?></legend>
			<table class="admintable">
				<thead>
				<tr>
					<th><?php echo JText::_( 'EASYSDI_SERVER_ALIAS'); ?></th>
					<th><?php echo JText::_( 'EASYSDI_URL'); ?></th>
					<th><?php echo JText::_( 'EASYSDI_USER'); ?></th>
					<th><?php echo JText::_( 'EASYSDI_PASSWORD'); ?></th>
				</tr>
				</thead>
				<tbody id="remoteServerTable" ><?php
				$remoteServerList = $config->{'remote-server-list'};
				$iServer=0;
				foreach ($remoteServerList->{'remote-server'} as $remoteServer){
					?><tr id="remoteServerTableRow<?php echo $iServer;?>">
							<td><input type="text" name="ALIAS_<?php echo $iServer;?>" id="ALIAS_<?php echo $iServer;?>" value="<?php echo $remoteServer->alias; ?>" size=20></td>
							<td><input type="text" name="URL_<?php echo $iServer;?>" value="<?php echo $remoteServer->url; ?>" size=70></td>
							<td><input name="USER_<?php echo $iServer;?>" type="text" value="<?php echo $remoteServer->user; ?>"></td>
							<td><input name="PASSWORD_<?php echo $iServer;?>" type="password" value="<?php echo $remoteServer->password; ?>">	
							<?php if ($iServer > 0){?>			
							<input id="removeServerButton" type="button" onClick="javascript:removeServer(<?php echo $iServer;?>);" value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>">
							<?php }?>
							</td>
					</tr>
					<?php if ($servletClass == "org.easysdi.proxy.csw.CSWProxyServlet"){?>
					<tr>						
						<td colspan="4">
						<div id="specificGeonetowrk" >
							<table>	
							<tr>									
							<td><?php echo JText::_( 'EASYSDI_MAX_RECORDS');?></td><td><input type="text" name="max-records_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'max-records'}; ?>" size=5></td>
							</tr>
							<tr>
							<td><?php echo JText::_( 'EASYSDI_LOGIN_SERVICE');?></td><td><input type="text" name="login-service_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'login-service'}; ?>" size=70></td>
							</tr>								
							</table>
						</div>
						</td>
						</tr>
					<?php
					}
				$iServer=$iServer+1;
				}
				?></tbody>
			</table>
			</fieldset>
			
			<script>
			var nbServer = <?php echo $iServer?>;
			</script>
			
		<?php 
	}
	
	/**
	 * 
	 * Generic servlet informations form :
	 * -  exception mode
	 * -  policy file
	 * -  log file parameter
	 * @param unknown_type $config
	 */
	function genericServletInformationsFooter ($config)
	{
		?>
				<fieldset class="adminform" id="exceptionMode"><legend><?php echo JText::_( 'PROXY_CONFIG_EXCEPTION_MANAGEMENT_MODE'); ?></legend>
				<table class="admintable">
					<tr>
						<td><input type="radio" name="exception_mode" value="permissive" <?php if (strcmp($config->{"exception"}->{"mode"},"permissive")==0 || !$config->{"exception"}->{"mode"}){echo "checked";} ?> > <?php echo JText::_( 'PROXY_CONFIG_EXCEPTION_MANAGEMENT_MODE_PERMISSIVE'); ?><br></td>
					</tr>
					<tr>
						<td><input type="radio" name="exception_mode" value="restrictive" <?php if (strcmp($config->{"exception"}->{"mode"},"restrictive")==0){echo "checked";} ?> > <?php echo JText::_( 'PROXY_CONFIG_EXCEPTION_MANAGEMENT_MODE_RESTRICTIVE'); ?><br></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY FILE LOCATION'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input name="policyFile" type="text" size=100
						value="<?php echo $config->{"authorization"}->{"policy-file"}; ?>"></td>
				</tr>
			</table>
			</fieldset>
	
			<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_XSLT_PATH'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input name="xsltPath" type="text" size=100
						value="<?php echo $config->{"xslt-path"}->{"url"}; ?>"></td>
				</tr>
			</table>
			</fieldset>
			<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_LOG'); ?></legend>
			<table class="admintable">
				
				<tr>
					<td colspan = "2">
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_FILE_NAME'); ?></td>
								<td><b>[<?php echo JText::_( 'EASYSDI_PREFIX'); ?>].[YYYYMMDD].[<?php echo JText::_( 'EASYSDI_SUFFIX'); ?>].[<?php echo JText::_( 'EASYSDI_EXTENSION'); ?>]</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan = "2">
					<table class="admintable">
						<tr>
							<th><?php echo JText::_( 'EASYSDI_PATH'); ?></th>
							<th><?php echo JText::_( 'EASYSDI_PREFIX'); ?></th>
							<th><?php echo JText::_( 'EASYSDI_SUFFIX'); ?></th>
							<th><?php echo JText::_( 'EASYSDI_EXTENSION'); ?></th>
						</tr>
						<tr>
							<td><input name="logPath"  id="logPath" size=70 type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"path"};?>"></td>
							<td><input name="logPrefix" id="logPrefix" type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"prefix"};?>"></td>
							<td><input name="logSuffix" id="logSuffix" type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"suffix"};?>"></td>
							<td><input name="logExt" type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"extension"};?>"></td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td colspan = "2">
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE'); ?></td>
								<td><select name="logger">
									<option <?php if (strcmp($config->{"log-config"}->{"logger"},"org.apache.log4j.Logger")==0){echo "selected";} ?>
										value="org.apache.log4j.Logger"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE_LOG4J'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"logger"},"org.easysdi.proxy.log.ProxyLogger")==0){echo "selected";} ?>
										value="org.easysdi.proxy.log.ProxyLogger"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE_EASYSDI'); ?></option>
								</select></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan = "2">
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_LEVEL'); ?></td>
								<td><select name="logLevel">
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"OFF")==0){echo "selected";} ?>
										value="OFF">OFF</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"FATAL")==0){echo "selected";} ?>
										value="FATAL">FATAL</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"ERROR")==0){echo "selected";} ?>
										value="ERROR">ERROR</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"WARN")==0){echo "selected";} ?>
										value="WARN">WARN</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"INFO")==0){echo "selected";} ?>
										value="INFO">INFO</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"DEBUG")==0){echo "selected";} ?>
										value="DEBUG">DEBUG</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"TRACE")==0){echo "selected";} ?>
										value="TRACE">TRACE</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"ALL")==0){echo "selected";} ?>
										value="TRACE">ALL</option>
								</select></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan = "2">
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_PERIOD'); ?></td>
								<td><select name="logPeriod">
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"daily")==0){echo "selected";} ?>
										value="daily"><?php echo JText::_( 'EASYSDI_DAILY'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"monthly")==0){echo "selected";} ?>
										value="monthly"><?php echo JText::_( 'EASYSDI_MONTHLY'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"weekly")==0){echo "selected";} ?>
										value="weekly"><?php echo JText::_( 'EASYSDI_WEEKLY'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"annualy")==0){echo "selected";} ?>
										value="annually"><?php echo JText::_( 'EASYSDI_ANNUALLY'); ?></option>
								</select></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</fieldset>
			<?php 
	}
	
	function showPoliciesList($xml){
		global $mainframe;
		$option = JRequest::getVar('option');
		$configId = JRequest::getVar("configId");
		$policyId = JRequest::getVar("policyId");
//		$task = JRequest::getVar("task");
		
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');

		?>


<form name='adminForm'  action='index.php' method='POST'>
<table>
	<tr>
		
		<td align="right" width="100%">
			<?php echo JText::_("EASYSDI_FILTER");?>&nbsp;
			<input type="text" name="search" id="search" value="<?php echo $search;?>" class="inputbox" onchange="document.adminForm.submit();"  />			
		</td>
		<td nowrap="nowrap"></td>
	</tr>
</table>

<table class="adminlist">
	<thead>
		<tr class="title">
			<th width="2%"><?php echo JText::_( 'EASYSDI_NUM'); ?></th>
			<th width="20" class='title'></th>
			<th width="30%"><?php echo JText::_( 'EASYSDI_POLICY ID'); ?></th>
			<th><?php echo JText::_( 'EASYSDI_USERS AND ROLES'); ?></th>
			<th><?php echo JText::_( 'EASYSDI_ORDER'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$isChecked = false;
	$i=0;
	foreach ($xml->config as $config) {

		if (strcmp($config['id'],$configId)==0){
			$policyFile = $config->{'authorization'}->{'policy-file'};
			
			$i=0;
			$count=0;
			if (!file_exists($policyFile)){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
			}
			
			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($policyFile);
				 
			

				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['ConfigId'],$configId)==0){
			
		$i++;	
		}
	}}}}
	
	$pageNav = new JPagination($i,$limitstart,$limit);
	foreach ($xml->config as $config) {

		if (strcmp($config['id'],$configId)==0){

			$servletClass=$config->{'servlet-class'};
			$policyFile = $config->{'authorization'}->{'policy-file'};
			$i=0;
			$count=0;
		if (!file_exists($policyFile)){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
			}
			
			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($policyFile);
			
				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['ConfigId'],$configId)==0){

						if ( (!(stripos($policy['Id'],$search)===False)) || strlen($search)==0){
							if (($count>=$limitstart || $limit==0)&& ($count < $limitstart+$limit || $limit==0)){
								?>
		<tr class="row<?php echo $i%2;?>">
			<td><?php echo $i + 1; ?></td>
			<td><input
			<?php 
				if (strlen($policyId)==0)
				{ 
					if (!$isChecked) 
					{
						echo 'checked'; 
						$isChecked =true;
					}
				}
				else
				{ 
					if(strcmp($policyId,$policy['Id'])==0)
					{
						echo 'checked';
					} 
				}?>
				type='radio' id="cb<?php echo$i ?>" name="policyId" value="<?php echo $policy['Id']; ?>" onclick="isChecked(this.checked);"> 
				</td>
				<td>
								
				<a href="#edit" onclick="document.getElementById('configId').value='<?php echo $configId;?>';document.getElementById('task').value='editPolicy';document.getElementById('cb<?php echo $i;?>').checked=true;document.adminForm.submit();;">
				<?php echo $policy['Id']; ?>	</a>
				
				</td>
			<td><?php  
			if (strcasecmp($policy->{'Subjects'}['All'],"true")==0){
				echo JText::_( 'EASYSDI_ALL USERS AND ROLES');
			}else{
					
				if (count($policy->Subjects->Role)>0){
					foreach ($policy->Subjects->Role as $role){
						echo  JText::_( $role).",";
					}
				}
					
				if (count($policy->Subjects->User)>0){
					foreach ($policy->Subjects->User as $user){
						echo  JText::_( $user).",";
					}

				}
			}

			?></td>
			<td>
			
            <?php echo $pageNav->orderUpIcon($i,  true, 'orderuppolicy', 'Move Up'); ?>
            <?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownpolicy', 'Move Down' ); ?>                       
        </td>
		</tr>

		<?php
		$i++;
							}
							$count++;
						}}
				}
			}
		}
	}
	?>
	</tbody>
	<tfoot>
	<?php
	
	//$pageNav = new JPagination($count,$limitstart,$limit);
	?>
		<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
	</tfoot>
</table>
	<input type='hidden' name='option' id='option' value='<?php echo $option;?>'>
	<input type='hidden' name='task' id='task' value='editPolicyList'>
	<input type='hidden' name='configId'id='configId' value='<?php echo $configId;?>'>
	<input type="hidden" name="boxchecked" value="<?php echo ($_POST['policyId'] or $isChecked)?1:0;?>" />
	<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >
</form>

	<?php

	}

	function editPolicy($xml,$new=false, $rowsProfile, $rowsUser, $rowsVisibility, $rowsStatus, $rowsObjectTypes){

		$policyId = JRequest::getVar("policyId");
		$configId = JRequest::getVar("configId");
		$option = JRequest::getVar("option");
		
		foreach ($xml->config as $config) {
			if (strcmp($config['id'],$configId)==0){

				$policyFile = $config->{'authorization'}->{'policy-file'};
				$servletClass =  $config->{'servlet-class'};
				
			if (!file_exists($policyFile)){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
			}
			
				if (file_exists($policyFile)) {
					$xmlConfigFile = simplexml_load_file($policyFile);
				
					if($new){
						
				$thePolicy  = $xmlConfigFile->addChild('Policy');
				$thePolicy ['Id']="new Policy";
				$policyId=$thePolicy ['Id'];
				$thePolicy ['ConfigId']=$configId;
				$thePolicy ->Servers['All']="false";
				$thePolicy ->Subjects['All']="false";
				$thePolicy ->Operations['All']="true";
				$thePolicy ->AvailabilityPeriod->Mask="dd-mm-yyyy";
				$thePolicy ->AvailabilityPeriod->From->Date="28-01-2008";
				$thePolicy ->AvailabilityPeriod->To->Date="28-01-2108";				
				}else{
					foreach ($xmlConfigFile->Policy as $policy){

						if (strcmp($policy['Id'],$policyId)==0  && strcmp($policy['ConfigId'],$configId)==0){								
							$thePolicy = $policy;
							break;

						}
					}
				}
				}				
				?>

<form name='adminForm' action='index.php' method='POST'><input
	type='hidden' name='option' value='<?php echo $option;?>'> <input
	type='hidden' name='task' value=''> <input type='hidden'
	name='servletClass' value='<?php echo $servletClass; ?>'> <input
	type='hidden' name='configId' value='<?php echo $configId; ?>'> <input
	type='hidden' name='policyId' value='<?php echo $policyId; ?>'> <input
	type='hidden' name='isNewPolicy' value='<?php echo $new; ?>'>
	<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >



<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY IDENTIFICATION'); ?></legend>
<table class="admintable">
	<tr>
		<td class="key"><?php echo JText::_( 'EASYSDI_CONFIGURATION ID'); ?></td>
		<td><input type="text" size="100" value="<?php echo $configId ?>" disabled="disabled"></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_( 'EASYSDI_POLICY ID'); ?></td>
		<td><input type="text" size="100" name="newPolicyId" value="<?php echo $policyId ?>"></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_( 'EASYSDI_SERVLET'); ?></td>
		<td><input type="text" size="100" value="<?php echo $servletClass;?>" disabled="disabled" size=50></td>
	</tr>

</table>
</fieldset>
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
		
		
		if(document.getElementById('maxHeight') != null)//Just for WMS policy
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
		else if (document.getElementById('remoteServer0') != null) //Just for WFS policy
		{
			if(geoQueryValid.length != 0)
			{
				alert ('<?php echo  JText::_( 'EASYSDI_QUERY_VALIDATION_ERROR');?>');
			}
			else
			{
				submitform(pressbutton);
			}
		}
		else
		{
			submitform(pressbutton);
		}
	}
}
</script>

<fieldset class="adminform"><legend>Users and Groups</legend>
<table class="admintable">
	<tr>
		<td><input
		<?php if (strcasecmp($thePolicy->Subjects['All'],'True')==0){echo 'checked';} ?>
			type="checkBox" name="AllUsers[]" id="AllUsers" 
			onclick="disableList('AllUsers','userNameList');disableList('AllUsers','roleNameList');">
		<?php echo JText::_( 'EASYSDI_ANONYMOUS'); ?></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_USERS'); ?></b></th>
		<th></th>		
		<th><b><?php echo JText::_( 'EASYSDI_ROLES'); ?></b></th>
	</tr>
	
	<tr>
<?php
			$userSelected = array();
			foreach ($thePolicy->Subjects->User as $user)
			{
				$ou->value = $user;
				$userSelected[] =$ou;
				$ou = null;				
			}
			
			$profileSelected = array();
			foreach ($thePolicy->Subjects->Role as $role)
			{
				$or->value = $role;
				$profileSelected[] = $or;
				$or = null;
			}
			$disabled ="";
			if (strcasecmp($thePolicy->Subjects['All'],'True')==0)
			{
				$disabled = "disabled ";
			}
			HTML_proxy::alter_array_value_with_Jtext($rowsProfile);
?>
	<td><?php echo JHTML::_("select.genericlist",$rowsUser, 'userNameList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userSelected ); ?></td>
	<td></td>
	<td><?php echo JHTML::_("select.genericlist", $rowsProfile, 'roleNameList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $profileSelected ); ?></td>
	</tr>

</table>
</fieldset>
<?php JHTML::_( 'behavior.modal' ); ?>
<?php JHTML::_('behavior.calendar'); ?>
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_AVAILIBILITY'); ?></legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_DATE TIME FORMAT'); ?> </b>: <?php echo $thePolicy->{'AvailabilityPeriod'}->Mask; ?>
		</th>
		<input name="dateFormat" type="hidden" value"dd-mm-yyyy">
		<td></td>
	</tr>
	<tr>
			
		<td><b><?php echo JText::_( 'EASYSDI_FROM'); ?></b> 	
			<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->From->Date, "dateFrom","dateFrom","%d-%m-%Y"); ?>		
			</td>
		<td><b><?php echo JText::_( 'EASYSDI_TO'); ?></b>		
			<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->To->Date, "dateTo","dateTo","%d-%m-%Y"); ?>
			</td>
			
	</tr>
</table>
</fieldset>

			<?php 
			if (strcmp($servletClass,"org.easysdi.proxy.wfs.WFSProxyServlet")==0){
				HTML_proxy::generateWFSHTML($config,$thePolicy);
			}
			else if (strcmp($servletClass,"org.easysdi.proxy.wms.WMSProxyServlet")==0 ){
				HTML_proxy::generateWMSHTML($config,$thePolicy);  }
				
			else if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 ){					
				HTML_proxy::generateCSWHTML($config,$thePolicy, $rowsVisibility, $rowsStatus, $rowsObjectTypes);  
			}
			else if (strcmp($servletClass,"org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet")==0 ){					
				HTML_proxy::generateWMTS100HTML($config,$thePolicy);  
			}
			break;
			}		
				
		} ?>

</form>
		<?php
	}
	
	/**
	 * Generate WMTS 100 form
	 * @param XML $config
	 * @param XML $thePolicy
	 */
	function generateWMTS100HTML($config,$thePolicy){
	?>
	<script>
		function disableOperationCheckBoxes()
		{
			var check = document.getElementById('AllOperations').checked;
			
			document.getElementById('oGetCapabilities').disabled=check;
			document.getElementById('oGetTile').disabled=check;
			document.getElementById('oGetCapabilities').checked=check;
			document.getElementById('oGetTile').checked=check;
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
					<td><input type="checkBox" name="operation[]" id="oGetFeatureInfo" value="GetFeatureInfo"  disabled>
					<i><?php echo JText::_( 'PROXY_CONFIG_OPERATION_GETFEATUREINFO'); ?></i></td>
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

			$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&version=1.0.0&SERVICE=WMTS");
			
			if ($xmlCapa === false){
					global $mainframe;		
							$mainframe->enqueueMessage(JText::_('EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' )." - ".$urlWithPassword,'error');
			}
			else{			
			foreach ($thePolicy->Servers->Server as $policyServer){			
				if (strcmp($policyServer->url,$remoteServer->url)==0){
					$theServer  = $policyServer;
				}
					
			}
			?>

	<input type="hidden" name="remoteServer<?php echo $iServer;?>" id="remoteServer<?php echo $iServer;?>" value="<?php echo $remoteServer->url ?>">
	<fieldset class="adminform" id="fsServer<?php echo $iServer;?>" >
		<legend><?php echo JText::_( 'PROXY_CONFOG_WMTS_SERVER'); ?> <?php echo $remoteServer->alias ;?> (<?php echo $remoteServer->url; ?>)</legend>
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
		<table width ="100%"  class="admintable" id="remoteServerTable@<?php echo $iServer; ?>" <?php if (strcasecmp($thePolicy->Servers['All'],'True')==0 ) echo "style='display:none'"; ?>>
			<tr>
				<td colspan="1"><input type="checkBox" name="AllLayers@<?php echo $iServer; ?>" id="AllLayers@<?php echo $iServer; ?>" value="All" <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' checked '; ?> onclick="disableWMTSLayers(<?php echo $iServer; ?>);"><?php echo JText::_( 'PROXY_CONFIG_LAYER_ALL'); ?></td>
			</tr>
			<tr>
				<th><b><?php echo JText::_( 'PROXY_CONFIG_LAYER_ID'); ?></b></th>
			</tr>
			
			<?php
			$layernum = 0;
			$namespaces = $xmlCapa->getDocNamespaces();
			$dom_capa = dom_import_simplexml ($xmlCapa);
			
    		$layers = $dom_capa->getElementsByTagNameNS($namespaces[''],'Layer');
			foreach ( $layers as $layer){
				$identifiers = $layer->getElementsByTagNameNS($namespaces['ows'],'Identifier');
				foreach ( $identifiers as $identifier){
					if($identifier->parentNode->nodeName == "Layer")
					{
						$title = $identifier->parentNode->getElementsByTagNameNS($namespaces['ows'],'Title')->item(0)->nodeValue;
					?>
					<tr>
						<td class="key" >
							<table width ="100%" height="100%" >
								<tr valign="top" >
								<td width="15"><input  
									<?php if( HTML_proxy::isWMTSLayerChecked($theServer,$identifier->nodeValue) || strcasecmp($theServer->Layers['All'],'True')==0) echo ' checked';?> type="checkbox"
									id="layer@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
									name="layer@<?php echo $iServer; ?>@<?php echo $layernum;?>"
									value="<?php echo $identifier->nodeValue;?>"></td>
								<td align="left"><?php echo $identifier->nodeValue; ?> ( <?php echo $title;?> )</td>
								</tr>
							</table>		
						</td>
						
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
	
	/**
	 * 
	 * Generate CSW form
	 * @param XML  $config
	 * @param XML $thePolicy
	 */
	function generateCSWHTML($config,$thePolicy, $rowsVisibility, $rowsStatus, $rowsObjectTypes)
	{
	?>
		<script>
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
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_VISIBILITY'); ?></legend>
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
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_STATUS'); ?></legend>
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
		<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_AUTHORIZED_OBJECTTYPE'); ?></legend>
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

	/**
	 * 
	 * Generate WMS form
	 * @param XML  $config
	 * @param XML $thePolicy
	 */
	function generateWMSHTML($config,$thePolicy){
	?>
	<script>
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
			$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMS");
			if ($xmlCapa === false){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_('EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' )." - ".$urlWithPassword,'error');
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
						<td width="15"><input onClick="activateLayer('<?php echo $iServer ; ?>','<?php echo $layernum; ?>')" <?php if( HTML_proxy::isWMSLayerChecked($theServer,$name) || strcasecmp($theServer->Layers['All'],'True')==0) echo ' checked';?> type="checkbox"
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
				<td align="center"><input <?php if(! HTML_proxy::isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> type="text" size="10"
					id="scaleMin@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
					name="scaleMin@<?php echo $iServer; ?>@<?php echo $layernum;?>"
					value="<?php echo HTML_proxy::getWMSLayerMinScale($theServer,$name); ?>"></td>
				<td align="center"><input <?php if(! HTML_proxy::isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> type="text" size="10"
					id="scaleMax@<?php echo $iServer; ?>@<?php echo $layernum?>" 
					name="scaleMax@<?php echo $iServer; ?>@<?php echo $layernum;?>"
					value="<?php echo HTML_proxy::getWMSLayerMaxScale($theServer,$name); ?>"></td>
				<td><textarea <?php if(! HTML_proxy::isWMSLayerChecked($theServer,$name)) {echo 'disabled';}?> <?php if (strcasecmp($theServer->Layers['All'],'True')==0 ) echo ' disabled '; ?> rows="3" cols="60"
					id="LocalFilter@<?php echo $iServer; ?>@<?php echo $layernum;?>" 
					name="LocalFilter@<?php echo $iServer; ?>@<?php echo $layernum;?>"> 
					<?php $localFilter = HTML_proxy ::getWMSLayerLocalFilter($theServer,$name); if (!(strlen($localFilter)>	0)){} else {echo $localFilter;} ?></textarea></td>
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

	/**
	 * 
	 * Generate WFS form
	 * @param XML  $config
	 * @param XML $thePolicy
	 */
	function generateWFSHTML($config,$thePolicy)
	{
		?>
		<script>
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
			
			$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&version=1.0.0&SERVICE=WFS");
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
										   <?php if( HTML_proxy ::isChecked($theServer,$featureType)) echo 'checked';?> 
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
										   <?php if( ! HTML_proxy ::isChecked($theServer,$featureType)) echo 'disabled ';?> 
										   <?php  $attributes =  HTML_proxy::getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){}else{echo 'checked '; } ?>
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
											  <?php  $attributes =  HTML_proxy::getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){echo 'disabled';}?>
												id="AttributeList@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
												name="AttributeList@<?php echo $iServer; ?>@<?php echo $ftnum; ?>">
												<?php $attributes =  HTML_proxy::getFeatureTypeAttributesList($theServer,$featureType) ;if (strcmp($attributes,"")==0){}else{echo $attributes; }?>
									</textarea>
								</td>
							</tr>
						</table>
					</td> 
					<td>
						<textarea rows="4" 
								  cols="50" 
								  onChange="CheckQuery('<?php echo $iServer; ?>','<?php echo $ftnum; ?>')" 
								  <?php if( ! HTML_proxy ::isChecked($theServer,$featureType)) echo 'disabled';?>
								 id="RemoteFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
								 name="RemoteFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>" <?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>>
								 <?php $remoteFilter = HTML_proxy ::getFeatureTypeRemoteFilter($theServer,$featureType); if (strcmp($remoteFilter,"")!=0){echo $remoteFilter;}?>
						</textarea>
					</td>
					<td>
						<textarea rows="4" 
								  cols="50" 
								  onChange="CheckQuery('<?php echo $iServer; ?>','<?php echo $featureType->{'Name'}; ?>')" 
								  <?php if( ! HTML_proxy ::isChecked($theServer,$featureType)) echo 'disabled';?>
									<?php if (strcasecmp($theServer->FeatureTypes['All'],'True')==0 ) echo ' disabled '; ?>
										id="LocalFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"
										name="LocalFilter@<?php echo $iServer; ?>@<?php echo $ftnum; ?>"><?php $localFilter = HTML_proxy ::getFeatureTypeLocalFilter($theServer,$featureType); 
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
	
	function showConfigList($xml){
		global $mainframe;
		JToolBarHelper::title( JText::_(  'EASYSDI_SHOW CONFIGURATIONS LIST' ), 'generic.png' );
		jimport("joomla.html.pagination");
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');

		?>

<form name='adminForm' action='index.php' method='POST'>
<input
	type='hidden' name='option'
	value='<?php echo JRequest::getVar('option') ;?>'> <input type='hidden'
	name='task' value='<?php echo JRequest::getVar('task') ;?>'> <input type='hidden' name='boxchecked'
	value='<?php echo ($_POST['configId'])?1:0;?>'>
	<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >

<table>
	<tr>

		<td align="right" width="100%">
			<?php echo JText::_("EASYSDI_FILTER");?>&nbsp;
			<input type="text" name="search" id="search" value="<?php echo $search;?>" class="inputbox" onchange="document.adminForm.submit();"  />			
		</td>
		<td nowrap="nowrap"></td>
	</tr>
</table>


<table class="adminlist">
	<thead>
		<tr>
			<th width="2%" class='title'><?php echo JText::_( 'EASYSDI_NUM' ); ?></th>
			<th width="2%" class='title'></th>
			<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION ID'); ?></b></th>
			<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION_TYPE'); ?></b></th>
			<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION_URL'); ?></b></th>

		</tr>
	</thead>
	<tbody>
	<?php
	$id = JRequest::getVar("configId","");

	$i=0;
	foreach ($xml->config as $config) {
		if (!(stripos($config['id'],$search)===False) || !(stripos($config->{'servlet-class'},$search)===False) || strlen($search)==0){
			if (($i>=$limitstart || $limit==0)&& ($i < $limitstart+$limit || $limit==0)){
				$policyFile = $config->{'authorization'}->{'policy-file'};
				?>
		<tr class="row<?php echo $i%2; ?>">
			<td><?php echo $i+1;?></td>
			<td><input
			<?php if (strlen($id)>0)
				  {
				  	if (strcmp($id,$config['id'])==0)
				  	{
				  		echo 'checked';
				  	}
				  } 
				 ?>
				type="radio" id="cb<?php echo$i ?>" name="configId" value="<?php echo $config['id'] ?>" onclick="document.getElementById('serviceType').value='<?php 
					if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
					{
						echo "WMS";
					}
					else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet")
					{
						echo "WMTS100";
					}
					else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
					{
						echo "CSW";
					}

					else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
					{
						echo "WFS";
					} ?>'; isChecked(this.checked);"></td>
			<td><b><?php echo $config['id']?></b> </td>
			<td><?php 
			if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
			{
				//echo "<b>".WMS."  </b>";
				echo "<b>WMS</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.v100.WMTS100ProxyServlet")
			{
				echo "<b>WMTS100</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
			{
				//echo "<b>".CSW."  </b>";  
				echo "<b>CSW</b>";
			}
//			else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.SimpleWFSProxyServlet")
//			{
//				//echo "<b>".WFS."  </b>";
//				echo "<b>WFS</b>";
//			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
			{
				//echo "<b>".WFS."  </b>";
				echo "<b>WFS</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.cgp.GGPProxyServlet")
			{
				//echo "<b>".CGP."  </b>";
				echo "<b>CGP</b>";
			}
			echo  "{".$config->{'servlet-class'}."}";  ?>
			</td>
			<td><?php	echo $config->{'host-translator'};?></td>
		</tr>
		

		<?php
			}
			$i++;

		}

	}?>
	</tbody>
	<tfoot>
	<?php

	$pageNav = new JPagination(count($xml->config),$limitstart,$limit);
	?>
		<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
	</tfoot>
</table>
</form>
	<?php
	
	}
	
	function helpAttributeFilter ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_ALL' ); ?>
		</h3>
		<p>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_ALL_CONTENT' ); ?>
		</p>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_SELECT' ); ?>
		</h3>
		<p>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_SELECT_CONTENT' ); ?>
		</p>
		<?php 
	}
	
	function helpImageSize ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_IMAGE_SIZE_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_IMAGE_SIZE' ); ?>
		</h3>
		<p>
		<?php echo JText::_(  'EASYSDI_HELP_IMAGE_SIZE_CONTENT' ); ?>
		</p>
		
		<?php 
	}
	
	function helpQueryTemplate ($filter_type)
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_TEMPLATE_TITLE' ); ?>
		</h2>
		<h3>
		<?php if($filter_type == "filterQuery")
		{
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY' ); 
		}
		else
		{
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_ANSWER' ); 
		}
		?>
		</h3>
		
		<?php if($filter_type == "filterQuery")
		{
		?>
		<textarea ROWS="8" COLS="75"><Filter xmlns:gml='http://www.opengis.net/gml'>
  <BBOX>
    <PropertyName>geometryName</PropertyName>
      <Box srsName=\"EPSG:4326\" gml=\"http://www.opengis.net/gml\">
        <coordinates>-180,-90 180,90</coordinates>
      </Box>
  </BBOX>
</Filter></textarea>
		<?php
		}
		else
		{ 
		?>
		<textarea ROWS="13" COLS="75"><Filter xmlns:gml='http://www.opengis.net/gml'>
  <Within>
    <PropertyName>geometryName</PropertyName>
      <gml:Polygon xmlns:gml='http://www.opengis.net/gml' srsName='EPSG:4326'>
      <gml:outerBoundaryIs>
        <gml:LinearRing>
          <gml:coordinates>-180,-90 -180,90 180,90 180,-90 -180,-90
          </gml:coordinates>
        </gml:LinearRing>
      </gml:outerBoundaryIs>
    </gml:Polygon>
  </Within>
</Filter></textarea>
		<?php
		} 
		if($filter_type == "filterQuery")
		{
			?>
			<p>
			<?php 
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY_REM' );
			?>
			</p>
			<?php
		}
		
	}
	
	function helpQueryWMSTemplate ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_TEMPLATE_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_WMS_QUERY' ); ?>
		</h3>
		
		
		<textarea ROWS="8" COLS="75"><gml:Polygon xmlns:gml="http://www.opengis.net/gml" srsName="EPSG:21781">
  <gml:outerBoundaryIs>
    <gml:LinearRing>                    
      <gml:coordinates>470000,50000 470000,210000 600000,210000 600000,50000 470000,50000
      </gml:coordinates>
    </gml:LinearRing>
  </gml:outerBoundaryIs>
</gml:Polygon></textarea>
		
			<p>
			<?php 
			//echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY_REM' );
			?>
			</p>
			<?php
		
	}


}
?>