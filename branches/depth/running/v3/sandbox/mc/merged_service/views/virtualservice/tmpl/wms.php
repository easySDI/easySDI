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
						echo JHTML::_("select.genericlist",$this->serviceconnectorlist, 'serviceconnector', 'size="1" onChange="document.getElementById(\'layout\').value=document.getElementById(\'serviceconnector\').value;submit()"', 'value', 'value', 'WMS');
					?>
					</td>
				</tr>
			</table>
		</fieldset>
		<?php
			$keywordString = "";
			if(isset($this->config->{"service-metadata"}->{'KeywordList'}->Keyword)) {
				foreach ($this->config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword) {
					$keywordString .= $keyword .",";
				}
			}
			$keywordString = substr($keywordString, 0, strlen($keywordString)-1);
			
			$this->genericServletInformationsHeader ("WMS");
		?>
		<fieldset class="adminform" id="service_metadata" ><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA'); ?></legend>
			<table class="admintable" >
				<tr>
					<td><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_TITLE"); ?> : <span class="star">*</span></td>
					<td>
						<input  class="inputbox required" name="service_title" id="service_title" type="text" size=100 value="
							<?php
								if( isset($this->config->{"service-metadata"}->{"Title"}) )
									echo $this->config->{"service-metadata"}->{"Title"};
							?>
						">
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_ABSTRACT"); ?> : </td>
					<td>
						<input name="service_abstract" id="service_abstract" type="text" size=100 value="
							<?php
								if( isset($this->config->{"service-metadata"}->{"Abstract"}) )
									echo $this->config->{"service-metadata"}->{"Abstract"};
							?>
						">
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_KEYWORD"); ?> : </td>
					<td><input name="service_keyword" id="service_keyword" type="text" size=100 value="<?php echo $keywordString; ?>"></td>
				</tr>
				<tr>
					<td colspan="2">
						<fieldset class="adminform" id ="servicemetadata_contact"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_METADATA_CONTACT'); ?></legend>
							<table>
								<tr>
									<td class="key" ><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_ORGANIZATION"); ?> : </td>
									<td colspan="2">
										<input name="service_contactorganization" id="service_contactorganization" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactOrganization"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactOrganization"}; 
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_PERSON"); ?> : </td>
									<td colspan="2">
										<input name="service_contactperson" id="service_contactperson" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactName"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactName"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_POSITION"); ?> : </td>
									<td colspan="2">
										<input name="service_contactposition" id="service_contactposition" type="text" size="80" value="
											<?php
												if(isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactPosition"}))
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactPosition"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key" id="service_contacttype_t"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_ADRESSTYPE"); ?> : </td>
									<td colspan="2">
										<input name="service_contacttype" id="service_contacttype" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"AddressType"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"AddressType"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key" ><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_ADRESS"); ?> : </td>
									<td colspan="2">
										<input name="service_contactadress" id="service_contactadress" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Address"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Address"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_CITY"); ?> : </td>
									<td>
										<input name="service_contactpostcode" id="service_contactpostcode" type="text" size="5" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"PostalCode"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"PostalCode"};
											?>
										">
									</td>
									<td>
										<input name="service_contactcity" id="service_contactcity" type="text" size="68" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"City"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"City"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_STATE"); ?> : </td>
									<td colspan="2">
										<input name="service_contactstate" id="service_contactstate" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"State"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"State"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_COUNTRY"); ?> : </td>
									<td colspan="2">
										<input name="service_contactcountry" id="service_contactcountry" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Country"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ContactAddress"}->{"Country"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_PHONE"); ?> : </td>
									<td colspan="2">
										<input name="service_contacttel" id="service_contacttel" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"VoicePhone"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"VoicePhone"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_FAX"); ?> : </td>
									<td colspan="2">
										<input name="service_contactfax" id="service_contactfax" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"Facsimile"}) ) 
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"Facsimile"};
											?>
										">
									</td>
								</tr>
								<tr>
									<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONTACT_MAIL"); ?> : </td>
									<td colspan="2">
										<input name="service_contactmail" id="service_contactmail" type="text" size="80" value="
											<?php
												if( isset($this->config->{"service-metadata"}->{"ContactInformation"}->{"ElectronicMailAddress"}) )
													echo $this->config->{"service-metadata"}->{"ContactInformation"}->{"ElectronicMailAddress"};
											?>
										">
									</td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_FEES"); ?> : </td>
					<td>
						<input name="service_fees" id="service_fees" type="text" size=100 value="
							<?php
								if( isset($this->config->{"service-metadata"}->{"Fees"}) )
									echo $this->config->{"service-metadata"}->{"Fees"};
							?>
						">
					</td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_("COM_EASYSDI_SERVICE_METADATA_CONSTRAINTS"); ?> : </td>
					<td>
						<input name="service_accessconstraints" id="service_accessconstraints" type="text" size=100 value="
							<?php
								if( isset($this->config->{"service-metadata"}->{"AccessConstraints"}) )
									echo $this->config->{"service-metadata"}->{"AccessConstraints"};
							?>
						">
					</td>
				</tr>
			</table>
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