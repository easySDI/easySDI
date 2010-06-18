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
		
			var form = document.accountForm;
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
					|| form.elements['corporatename1'].value == ''
					|| form.elements['agentfirstname'].value == ''
					|| form.elements['agentlastname'].value == ''
					|| form.elements['title_id'].value == '0'
				
					|| form.elements['street1'].value == ''
					|| form.elements['locality'].value == ''
					|| form.elements['postalcode'].value == ''
					
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
				|| form.elements['corporatename1[0]'].value == ''
				|| form.elements['agentfirstname[0]'].value == ''
				|| form.elements['agentlastname[0]'].value == ''
				|| form.elements['title_id[0]'].value == '0'
			
				|| form.elements['street1[0]'].value == ''
				|| form.elements['locality[0]'].value == ''
				|| form.elements['postalcode[0]'].value == ''
				
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

			form.elements['corporatename1['+ target+']'].disabled = value;
			form.elements['corporatename2['+ target+']'].disabled = value;
			form.elements['title_id['+ target+']'].disabled = value;
			form.elements['agentfirstname['+ target+']'].disabled = value;
			form.elements['agentlastname['+ target+']'].disabled = value;
			form.elements['function['+ target+']'].disabled = value;
			form.elements['street1['+ target+']'].disabled = value;
			form.elements['street2['+ target+']'].disabled = value;
			form.elements['postalcode['+ target+']'].disabled = value;
			form.elements['locality['+ target+']'].disabled = value;
			form.elements['country_id['+ target+']'].disabled = value;
			form.elements['phone['+ target+']'].disabled = value;
			form.elements['fax['+ target+']'].disabled = value;
			form.elements['email['+ target+']'].disabled = value;	
			
			if (value == true)
			{
				form.elements['corporatename1['+ target+']'].value = form.elements['corporatename1[0]'].value;
				form.elements['corporatename2['+ target+']'].value  = form.elements['corporatename2[0]'].value ;
				form.elements['title_id['+ target+']'].value  = form.elements['title_id[0]'].value ;
				form.elements['agentfirstname['+ target+']'].value = form.elements['agentfirstname[0]'].value ;
				form.elements['agentlastname['+ target+']'].value = form.elements['agentlastname[0]'].value ;
				form.elements['function['+ target+']'].value = form.elements['function[0]'].value ;
				form.elements['street1['+ target+']'].value = form.elements['street1[0]'].value ;
				form.elements['street2['+ target+']'].value = form.elements['street2[0]'].value ;
				form.elements['postalcode['+ target+']'].value = form.elements['postalcode[0]'].value ;
				form.elements['locality['+ target+']'].value = form.elements['locality[0]'].value ;
				form.elements['country_id['+ target+']'].value = form.elements['country_id[0]'].value ;
				form.elements['phone['+ target+']'].value = form.elements['phone[0]'].value ;
				form.elements['fax['+ target+']'].value = form.elements['fax[0]'].value ;
				form.elements['email['+ target+']'].value = form.elements['email[0]'].value ;
			}
		}
		function compareAddress(source, target)
		{
			var form = document.partnerForm;
			var same = true;

			if (same == true) same = (form.elements['corporatename1['+ source + ']'].value == form.elements['corporatename1['+ target +']'].value);
			if (same == true) same = (form.elements['corporatename2['+ source + ']'].value == form.elements['corporatename2['+ target +']'].value);
			if (same == true) same = (form.elements['title_id['+ source + ']'].value == form.elements['title_id['+ target +']'].value);
			if (same == true) same = (form.elements['agentfirstname['+ source + ']'].value == form.elements['agentfirstname['+ target +']'].value);
			if (same == true) same = (form.elements['agentlastname['+ source + ']'].value == form.elements['agentlastname['+ target +']'].value);
			if (same == true) same = (form.elements['function['+ source + ']'].value == form.elements['function['+ target +']'].value);
			if (same == true) same = (form.elements['street1['+ source + ']'].value == form.elements['street1['+ target +']'].value);
			if (same == true) same = (form.elements['street2['+ source + ']'].value == form.elements['street2['+ target +']'].value);
			if (same == true) same = (form.elements['postalcode['+ source + ']'].value == form.elements['postalcode['+ target +']'].value);
			if (same == true) same = (form.elements['locality['+ source + ']'].value == form.elements['locality['+ target +']'].value);
			if (same == true) same = (form.elements['country_id['+ source + ']'].value == form.elements['country_id['+ target +']'].value);
			if (same == true) same = (form.elements['phone['+ source + ']'].value == form.elements['phone['+ target +']'].value);
			if (same == true) same = (form.elements['fax['+ source + ']'].value == form.elements['fax['+ target +']'].value);
			if (same == true) same = (form.elements['email['+ source + ']'].value == form.elements['email['+ target +']'].value);
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