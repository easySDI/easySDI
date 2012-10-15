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

?>
<form action="<?php echo JRoute::_('index.php?option=com_easysdi_service&view=virtualservice&id='.JRequest::getVar('id',null)); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="width-60 fltlft">
		
			 <fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONNECTOR_CHOICE' );?></legend>
				<table class="admintable">
					<tr>
						<td>
						<?php 
						echo JHTML::_("select.genericlist",$this->serviceconnectorlist, 'serviceconnector', 'size="1" onChange="document.getElementById(\'layout\').value=document.getElementById(\'serviceconnector\').value;submit()"', 'value', 'value', 'WMTS'); ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<?php
			$keywordString = "";
			foreach ($this->config->{"service-metadata"}->{"ServiceIdentification"}->{'KeywordList'}->Keyword as $keyword)
			{
				$keywordString .= $keyword .",";
			}
			$keywordString = substr($keywordString, 0, strlen($keywordString)-1) ;
			
			$this->genericServletInformationsHeader ("WMTS");
			?>
			
			<fieldset class="adminform" id="service_metadata" ><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA'); ?></legend>
				<fieldset class="adminform" id="service_identification" ><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA_IDENTIFICATION'); ?></legend>
					<table class="admintable" >
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_TITLE"); ?> : <span class="star">*</span></td>
							<td><input class="inputbox required" name="service_title" id="service_title" type="text" size=100 value="<?php echo $this->config{"service-metadata"}->{"ServiceIdentification"}->{"Title"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_ABSTRACT"); ?> : </td>
							<td><input name="service_abstract" id="service_abstract" type="text" size=100 value="<?php echo $this->config{"service-metadata"}->{"ServiceIdentification"}->{"Abstract"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_KEYWORD"); ?> : </td>
							<td><input name="service_keyword" id="service_keyword" type="text" size=100 value="<?php echo $keywordString; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_FEES"); ?> : </td>
							<td><input name="service_fees" id="service_fees" type="text" size=100 value="<?php echo $this->config{"service-metadata"}->{"ServiceIdentification"}->{"Fees"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONSTRAINTS"); ?> : </td>
							<td><input name="service_accessconstraints" id="service_accessconstraints" type="text" size=100 value="<?php echo $this->config{"service-metadata"}->{"ServiceIdentification"}->{"AccessConstraints"}; ?>"></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform" id="service_identification" ><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_METADATA_PROVIDER'); ?></legend>
					<table class="admintable" >
						<tr>
							<td class="key" ><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_PROVIDER_NAME"); ?> : </td>
							<td colspan="2"><input name="service_providername" id="service_providername" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ProviderName"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_PROVIDER_SITE"); ?> : </td>
							<td colspan="2"><input name="service_providersite" id="service_providersite" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ProviderSite"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_RESPONSIBLE_NAME"); ?> : </td>
							<td colspan="2"><input name="service_responsiblename" id="service_responsiblename" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{"IndividualName"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_RESPONSIBLE_POSITION"); ?> : </td>
							<td colspan="2"><input name="service_responsibleposition" id="service_responsibleposition" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{"PositionName"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_RESPONSIBLE_ROLE"); ?> : </td>
							<td colspan="2"><input name="service_responsiblerole" id="service_responsiblerole" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{"Role"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" ><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_ADRESSTYPE"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactadresstype" id="service_responsiblecontactadresstype" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"AddressType"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" ><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_ADRESS"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactadress" id="service_responsiblecontactadress" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{'DelivryPoint'}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_CITY"); ?> : </td>
							<td><input name="service_responsiblecontactpostcode" id="service_responsiblecontactpostcode" type="text" size="5" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"PostalCode"}; ?>"></td>
							<td><input name="service_responsiblecontactcity" id="service_responsiblecontactcity" type="text" size="68" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"City"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_AREA"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactarea" id="service_responsiblecontactarea" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"Area"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_COUNTRY"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactcountry" id="service_responsiblecontactcountry" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"Country"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_MAIL"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactmail" id="service_responsiblecontactmail" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{'Address'}->{"ElectronicMailAddress"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_PHONE"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactphone" id="service_responsiblecontactphone" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"Telephone"}->{"VoicePhone"}; ?>"></td>
						</tr>
						<tr>
							<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_FAX"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactfax" id="service_responsiblecontactfax" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"Telephone"}->{"Facsimile"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" id="service_contactlinkage_t"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_LINK"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontactonline" id="service_responsiblecontactonline" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"OnlineResource"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" id="service_contacthours_t"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_HOURS"); ?> : </td>
							<td colspan="2"><input name="service_responsiblecontacthours" id="service_responsiblecontacthours" type="text" size="80" value="<?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"HoursOfService"}; ?>"></td>
						</tr>
						<tr>
							<td class="key" id="service_contactinstructions_t"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_INSTRUCTIONS"); ?> : </td>
							<td colspan="2"><textarea name="service_responsiblecontactinstructions" id="service_responsiblecontactinstructions"  cols="45" rows="5"  ><?php echo $this->config{"service-metadata"}->{"ServiceProvider"}->{"ResponsibleParty"}->{'Contact'}->{"Instructions"}; ?></textarea></td>
						</tr>
					</table>
				</fieldset>
			</fieldset>
			<?php
			$this->genericServletInformationsFooter ();
			?>
		
	</div>
	<input type="hidden" name="layout" id="layout" value="" />
	<input type="hidden" name="task" value="<?php echo JRequest::getCmd('task');?>" />
	<input type="hidden" name="previoustask" value="<?php echo JRequest::getCmd('task');?>" />
		
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>

		
    <style type="text/css">
        /* Temporary fix for drifting editor fields */
        .adminformlist li {
            clear: both;
        }
    </style>
</form>