<?php

defined('_JEXEC') or die('Restricted access');
?>
<script>
		function submitbutton(pressbutton)
		{
			var form = document.adminForm;
			if (pressbutton == "cancelAccount")
			{
				submitform( pressbutton );
				return;
			}
			
			if(pressbutton == "editRootAccount" || pressbutton == "editAffiliateAccount" )
			{
				submitform( pressbutton );
				return;
			}
			
			// do field validation
			if (   form.elements['title_id[0]'].value == '0' 
				|| form.elements['name'].value == '' 
				|| form.elements['username'].value == '' 
				|| form.elements['user_email'].value == '' 
				|| (form.elements['password'].value == '' && form.elements['id'].value =='')
				|| form.elements['corporatename1[0]'].value == ''
				|| form.elements['agentfirstname[0]'].value == ''
				|| form.elements['agentlastname[0]'].value == ''
				//|| form.elements['function[0]'].value == ''
				|| form.elements['street1[0]'].value == ''
				|| form.elements['locality[0]'].value == ''
				|| form.elements['postalcode[0]'].value == ''
				 )
				
			{
				alert( "<?php echo JText::_("CORE_CHECK_SUBMIT_FORM");?> ");
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

			/*form.account_contract.disabled = contract;
			form.account_inhabitant.disabled = inhabitant;
			form.activity_id.disabled = activity;
			form.collaborator_id.disabled = collaborator;
			form.member_id.disabled = member;
			form.account_electricity.disabled = electricity;
			form.account_gas.disabled = gas;
			form.account_heating.disabled = heating;
			form.account_telcom.disabled= telcom;
			form.account_network.disabled = network;
			*/
		}
		function changeAddress(value, target)
		{
			var form = document.adminForm;

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
			var form = document.adminForm;
			var same = true;
			//console.log(source);
			//console.log(form.elements['title_id']);
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