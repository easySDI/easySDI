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

defined('_JEXEC') or die('Restricted access');
?>
<script>
		function submitbutton()
		{
		
			var form = document.partnerForm;
			if (form.elements['task'].value == "createBlockUser")
			{
				if(form.elements['password'].value != form.elements['password_chk'].value)
				{
					alert( "<?php echo JText::_("EASYSDI_CHECK_PW_SUBMIT_FORM");?> ");
					return;
				}
				// do field validation
				if (form.elements['name'].value == '' 
					|| form.elements['username'].value == '' 
					|| form.elements['email'].value == '' 
					|| (form.elements['password'].value == '' && form.elements['id'].value =='')
					|| form.elements['address_corporate_name1'].value == ''
					|| form.elements['address_agent_firstname'].value == ''
					|| form.elements['address_agent_lastname'].value == ''
					|| form.elements['title_id'].value == '0'
				
					|| form.elements['address_street1'].value == ''
					|| form.elements['address_locality'].value == ''
					|| form.elements['address_postalcode'].value == ''
					
					)
				{
					alert( "<?php echo JText::_("EASYSDI_CHECK_SUBMIT_FORM");?> ");
				} else {
					form.submit( );
				}
				
			}
			
			// do field validation
			if (form.elements['name'].value == '' 
				|| form.elements['username'].value == '' 
				|| form.elements['email'].value == '' 
				|| (form.elements['password'].value == '' && form.elements['id'].value =='')
				|| form.elements['address_corporate_name1[0]'].value == ''
				|| form.elements['address_agent_firstname[0]'].value == ''
				|| form.elements['address_agent_lastname[0]'].value == ''
				|| form.elements['title_id[0]'].value == '0'
			
				|| form.elements['address_street1[0]'].value == ''
				|| form.elements['address_locality[0]'].value == ''
				|| form.elements['address_postalcode[0]'].value == ''
				
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

			form.elements['address_corporate_name1['+ target+']'].disabled = value;
			form.elements['address_corporate_name2['+ target+']'].disabled = value;
			form.elements['title_id['+ target+']'].disabled = value;
			form.elements['address_agent_firstname['+ target+']'].disabled = value;
			form.elements['address_agent_lastname['+ target+']'].disabled = value;
			form.elements['address_agent_function['+ target+']'].disabled = value;
			form.elements['address_street1['+ target+']'].disabled = value;
			form.elements['address_street2['+ target+']'].disabled = value;
			form.elements['address_postalcode['+ target+']'].disabled = value;
			form.elements['address_locality['+ target+']'].disabled = value;
			form.elements['country_code['+ target+']'].disabled = value;
			form.elements['address_phone['+ target+']'].disabled = value;
			form.elements['address_fax['+ target+']'].disabled = value;
			form.elements['address_email['+ target+']'].disabled = value;	
			
			if (value == true)
			{
				form.elements['address_corporate_name1['+ target+']'].value = form.elements['address_corporate_name1[0]'].value;
				form.elements['address_corporate_name2['+ target+']'].value  = form.elements['address_corporate_name2[0]'].value ;
				form.elements['title_id['+ target+']'].value  = form.elements['title_id[0]'].value ;
				form.elements['address_agent_firstname['+ target+']'].value = form.elements['address_agent_firstname[0]'].value ;
				form.elements['address_agent_lastname['+ target+']'].value = form.elements['address_agent_lastname[0]'].value ;
				form.elements['address_agent_function['+ target+']'].value = form.elements['address_agent_function[0]'].value ;
				form.elements['address_street1['+ target+']'].value = form.elements['address_street1[0]'].value ;
				form.elements['address_street2['+ target+']'].value = form.elements['address_street2[0]'].value ;
				form.elements['address_postalcode['+ target+']'].value = form.elements['address_postalcode[0]'].value ;
				form.elements['address_locality['+ target+']'].value = form.elements['address_locality[0]'].value ;
				form.elements['country_code['+ target+']'].value = form.elements['country_code[0]'].value ;
				form.elements['address_phone['+ target+']'].value = form.elements['address_phone[0]'].value ;
				form.elements['address_fax['+ target+']'].value = form.elements['address_fax[0]'].value ;
				form.elements['address_email['+ target+']'].value = form.elements['address_email[0]'].value ;
			}
		}
		function compareAddress(source, target)
		{
			var form = document.partnerForm;
			var same = true;

			if (same == true) same = (form.elements['address_corporate_name1['+ source + ']'].value == form.elements['address_corporate_name1['+ target +']'].value);
			if (same == true) same = (form.elements['address_corporate_name2['+ source + ']'].value == form.elements['address_corporate_name2['+ target +']'].value);
			if (same == true) same = (form.elements['title_id['+ source + ']'].value == form.elements['title_id['+ target +']'].value);
			if (same == true) same = (form.elements['address_agent_firstname['+ source + ']'].value == form.elements['address_agent_firstname['+ target +']'].value);
			if (same == true) same = (form.elements['address_agent_lastname['+ source + ']'].value == form.elements['address_agent_lastname['+ target +']'].value);
			if (same == true) same = (form.elements['address_agent_function['+ source + ']'].value == form.elements['address_agent_function['+ target +']'].value);
			if (same == true) same = (form.elements['address_street1['+ source + ']'].value == form.elements['address_street1['+ target +']'].value);
			if (same == true) same = (form.elements['address_street2['+ source + ']'].value == form.elements['address_street2['+ target +']'].value);
			if (same == true) same = (form.elements['address_postalcode['+ source + ']'].value == form.elements['address_postalcode['+ target +']'].value);
			if (same == true) same = (form.elements['address_locality['+ source + ']'].value == form.elements['address_locality['+ target +']'].value);
			if (same == true) same = (form.elements['country_code['+ source + ']'].value == form.elements['country_code['+ target +']'].value);
			if (same == true) same = (form.elements['address_phone['+ source + ']'].value == form.elements['address_phone['+ target +']'].value);
			if (same == true) same = (form.elements['address_fax['+ source + ']'].value == form.elements['address_fax['+ target +']'].value);
			if (same == true) same = (form.elements['address_email['+ source + ']'].value == form.elements['address_email['+ target +']'].value);
			if(target == 1)
			{
				form.elements['sameAddress1'].checked = same;
			}
			if(target == 2)
			{
				form.elements['sameAddress2'].checked = same;
			}
			/*form.elements['sameAddress[]'][target].checked = same;*/
			changeAddress(same, target);
		}
</script>