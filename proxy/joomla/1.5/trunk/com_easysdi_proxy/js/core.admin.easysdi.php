<?php

defined('_JEXEC') or die('Restricted access');
?>
<script>
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton != "savePolicy")
	{
		submitform( pressbutton );
		return;
	}
	
	// do field validation
	if (   form.elements['title_id[0]'].value == '0' 
		|| form.elements['name'].value == '' 
		|| form.elements['username'].value == '' 
		|| form.elements['email'].value == '' 
		|| (form.elements['password'].value == '' && form.elements['id'].value =='')
		|| form.elements['address_corporate_name1[0]'].value == ''
		|| form.elements['address_agent_firstname[0]'].value == ''
		|| form.elements['address_agent_lastname[0]'].value == ''
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
}</script>