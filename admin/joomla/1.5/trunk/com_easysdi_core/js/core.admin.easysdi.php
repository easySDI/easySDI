<?php

defined('_JEXEC') or die('Restricted access');
?>
<script>
		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			if (pressbutton == "cancelPartner")
			{
				submitform( pressbutton );
				return;
			}
			
			if(pressbutton == "editRootPartner" || pressbutton == "editAffiliatePartner" )
			{
				submitform( pressbutton );
				return;
			}
			
			// do field validation
			if (   form.elements['title_id[0]'].value == '0' 
				|| form.name.value == '' 
				|| form.username.value == '' 
				|| form.email.value == '' 
				|| (form.password.value == '' && form.id.value =='')
				|| form.elements['address_corporate_name1[0]'].value == ''
				|| form.elements['address_agent_firstname[0]'].value == ''
				|| form.elements['address_agent_lastname[0]'].value == ''
				|| form.elements['address_agent_function[0]'].value == ''
				|| form.elements['address_street1[0]'].value == ''
				|| form.elements['address_locality[0]'].value == ''
				|| form.elements['address_postalcode[0]'].value == ''
				 )
				
			{
				alert( "<?php echo JText::_("EASYSDI_CHECK_SUBMIT_FORM");?> ");
			} 
			else
			{
				submitform( pressbutton );
			}
		}
		
		function changeCategory(value)
		{
			var form = document.adminForm;
			switch (value)
			{
				case '1':	//Commune
					updateCategory(true,false,true,true,true,false,false,false,false,false);
					break;
				case '2':	//Canton
					updateCategory(true,false,true,true,true,true,true,true,true,true);
					break;
				case '3':	//Association communale
					updateCategory(true,true,true,true,true,false,false,false,false,false);
					break;
				case '4':	//Distributeur
					updateCategory(true,true,true,true,true,false,false,false,false,false);
					break;
				case '5':	//Association professionnelle
					updateCategory(true,true,false,true,false,true,true,true,true,true);
					break;
				case '6':	//Priv�
					updateCategory(true,true,true,true,true,true,true,true,true,true);
					break;
				case '7':	//Bureau technique
					updateCategory(true,true,false,false,true,true,true,true,true,true);
					break;
				case '8':	//Ami
					updateCategory(true,true,true,true,true,true,true,true,true,true);
					break;
				case '9':	//Autre
					updateCategory(false,true,true,true,true,true,true,true,true,true);
					break;
				case '10':	//Utilisateur
					updateCategory(true,true,true,true,true,true,true,true,true,true);
					break;
				case '11':	//Transport public
					updateCategory(true,true,true,false,true,true,true,true,true,true);
					break;
				default:
					updateCategory(true,true,true,true,true,true,true,true,true,true);
					break;
			}
		}
		function updateCategory(contract,inhabitant,activity,collaborator,member,electricity,gas,heating,telcom,network)
		{
			var form = document.adminForm;

			/*form.partner_contract.disabled = contract;
			form.partner_inhabitant.disabled = inhabitant;
			form.activity_id.disabled = activity;
			form.collaborator_id.disabled = collaborator;
			form.member_id.disabled = member;
			form.partner_electricity.disabled = electricity;
			form.partner_gas.disabled = gas;
			form.partner_heating.disabled = heating;
			form.partner_telcom.disabled= telcom;
			form.partner_network.disabled = network;
			*/
		}
		function changeAddress(value, target)
		{
			var form = document.adminForm;

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
			var form = document.adminForm;
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
				form.sameAddress1.checked = same;
			}
			if(target == 2)
			{
				form.sameAddress2.checked = same;
			}
			/*form.elements['sameAddress[]'][target].checked = same;*/
			changeAddress(same, target);
		}
		
</script>