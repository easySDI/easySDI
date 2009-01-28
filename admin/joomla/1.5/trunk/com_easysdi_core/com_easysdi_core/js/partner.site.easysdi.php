<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

defined('_JEXEC') or die('Restricted access');
?>
<script>
		function submitbutton()
		{
		
			var form = document.partnerForm;
			if (form.task.value == "createBlockUser")
			{
				if(form.password.value != form.password_chk.value)
				{
					alert( "<?php echo JText::_("EASYSDI_CHECK_PW_SUBMIT_FORM");?> ");
					return;
				}
				
			}
			
			// do field validation
			if (form.name.value == '' 
				|| form.username.value == '' 
				|| form.email.value == '' 
				|| (form.password.value == '' && form.id.value =='')
				|| form.address_corporate_name1.value == ''
				|| form.title_id.value == ''
				|| form.address_agent_firstname.value == ''
				|| form.address_agent_lastname.value == ''
				|| form.address_street1.value == ''
				|| form.address_postalcode.value == ''
				|| form.address_locality.value == ''
				
				)
			{
				alert( "<?php echo JText::_("EASYSDI_CHECK_SUBMIT_FORM");?> ");
			} else {
				form.submit( );
			}
		}
	
		function changeAddress(value, target)
		{
			var form = document.partnerForm;
			
			form.elements['address_corporate_name1[]'][target].disabled = value;
			form.elements['address_corporate_name2[]'][target].disabled = value;
			form.elements['title_id[]'][target].disabled = value;
			form.elements['address_agent_firstname[]'][target].disabled = value;
			form.elements['address_agent_lastname[]'][target].disabled = value;
			form.elements['address_agent_function[]'][target].disabled = value;
			form.elements['address_street1[]'][target].disabled = value;
			form.elements['address_street2[]'][target].disabled = value;
			form.elements['address_postalcode[]'][target].disabled = value;
			form.elements['address_locality[]'][target].disabled = value;
			form.elements['country_code[]'][target].disabled = value;
			form.elements['address_phone[]'][target].disabled = value;
			form.elements['address_fax[]'][target].disabled = value;
			form.elements['address_email[]'][target].disabled = value;
		}
		function compareAddress(source, target)
		{
			var form = document.partnerForm;
			var same = true;

			if (same == true) same = (form.elements['address_corporate_name1[]'][source].value == form.elements['address_corporate_name1[]'][target].value);
			if (same == true) same = (form.elements['address_corporate_name2[]'][source].value == form.elements['address_corporate_name2[]'][target].value);
			if (same == true) same = (form.elements['title_id[]'][source].value == form.elements['title_id[]'][target].value);
			if (same == true) same = (form.elements['address_agent_firstname[]'][source].value == form.elements['address_agent_firstname[]'][target].value);
			if (same == true) same = (form.elements['address_agent_lastname[]'][source].value == form.elements['address_agent_lastname[]'][target].value);
			if (same == true) same = (form.elements['address_agent_function[]'][source].value == form.elements['address_agent_function[]'][target].value);
			if (same == true) same = (form.elements['address_street1[]'][source].value == form.elements['address_street1[]'][target].value);
			if (same == true) same = (form.elements['address_street2[]'][source].value == form.elements['address_street2[]'][target].value);
			if (same == true) same = (form.elements['address_postalcode[]'][source].value == form.elements['address_postalcode[]'][target].value);
			if (same == true) same = (form.elements['address_locality[]'][source].value == form.elements['address_locality[]'][target].value);
			if (same == true) same = (form.elements['country_code[]'][source].value == form.elements['country_code[]'][target].value);
			if (same == true) same = (form.elements['address_phone[]'][source].value == form.elements['address_phone[]'][target].value);
			if (same == true) same = (form.elements['address_fax[]'][source].value == form.elements['address_fax[]'][target].value);
			if (same == true) same = (form.elements['address_email[]'][source].value == form.elements['address_email[]'][target].value);
			form.elements['sameAddress[]'][target].checked = same;
			changeAddress(same, target);
		}
</script>