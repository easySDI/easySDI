function toggle_state(obj){
	if(obj.checked){
		if(obj.name == 'external')
			$('data_internal').checked = true;
		if(obj.name == 'metadata_external')
			$('metadata_internal').checked = true;
	}
	else
	{
		if(obj.name == 'internal')
			$('data_external').checked = false;
		if(obj.name == 'metadata_internal')
			$('metadata_external').checked = false;
	}
}

function fieldManagement()
{
	if (document.forms['productForm'].free.value == '0')
	{
		document.getElementById('productfile').disabled = true;
		document.getElementById('pathfile').disabled = true;
		document.getElementById('available').disabled = true;
		document.getElementById('available').value = '0';
		document.getElementById('deleteFileButton').disabled = true;
		document.getElementById('linkFile').value = true;
		document.getElementById('surfacemin').disabled = false;
		document.getElementById('surfacemax').disabled = false;
		document.getElementById('notification').disabled = false;
		document.getElementById('treatmenttype_id').disabled = false;
	}
	else if (document.forms['productForm'].free.value == '1' && document.forms['productForm'].available.value == '0')
	{
		document.getElementById('productfile').disabled = true;
		document.getElementById('pathfile').disabled = true;
		document.getElementById('available').disabled = false;
		document.getElementById('deleteFileButton').disabled = true;
		document.getElementById('linkFile').disabled = false;
		document.getElementById('surfacemin').disabled = false;
		document.getElementById('surfacemax').disabled = false;
		document.getElementById('notification').disabled = false;
		document.getElementById('treatmenttype_id').disabled = false;
	}
	else
	{
		document.getElementById('productfile').disabled = false;
		document.getElementById('pathfile').disabled = false;
		document.getElementById('available').disabled = false;
		document.getElementById('deleteFileButton').disabled = false;
		document.getElementById('linkFile').disabled = false;
		document.getElementById('surfacemin').disabled = true;
		document.getElementById('surfacemax').disabled = true;
		document.getElementById('notification').disabled = true;
		document.getElementById('treatmenttype_id').disabled = true;
	}
}

function deleteFile (form, message){

	if (confirm(message)== true)
	{
		form.task.value='deleteProductFile';
		form.submit();
	}
}

function productAvailability_change(obj, id){
	if(obj.checked){
		$('buffer_'+id).disabled = false;
	}
	else
	{
		$('buffer_'+id).checked = false;
		$('buffer_'+id).disabled = true;
	}
}

function displayAuthentication()
{
	if (document.forms['productForm'].service_type[0].checked)
	{
		document.getElementById('viewpassword').disabled = true;
		document.getElementById('viewpassword').value = "";
		document.getElementById('viewuser').disabled = true;
		document.getElementById('viewuser').value ="";
		document.getElementById('viewaccount_id').disabled = false;
	}
	else
	{
		document.getElementById('viewpassword').disabled = false;
		document.getElementById('viewuser').disabled = false;
		document.getElementById('viewaccount_id').disabled = true;
		document.getElementById('viewaccount_id').value = '0';
	}
}	
function accessibilityEnable(choice,list)
{
	var form = document.productForm;
	if (form.elements[choice].value=='0')
	{
		form.elements[list].disabled=false;
	}
	else
	{
		form.elements[list].disabled=true;
		for (i = form.elements[list].length - 1; i>=0; i--) 
		{
			form.elements[list].options[i].selected = false;
		}
	}
}
function ServiceFieldManagement(){
	if (document.forms['productForm'].viewurltype.value == 'WMS')
	{
		document.getElementById('viewminresolution').disabled = false;
		document.getElementById('viewmaxresolution').disabled = false;
		document.getElementById('viewmatrixset').disabled = true;
		document.getElementById('viewmatrixset').value = null;
		document.getElementById('viewmatrix').disabled = true;
		document.getElementById('viewmatrix').value = null;
	}else{
		document.getElementById('viewminresolution').disabled = true;
		document.getElementById('viewminresolution').value = null;
		document.getElementById('viewmaxresolution').disabled = true;
		document.getElementById('viewmaxresolution').value = null;
		document.getElementById('viewmatrixset').disabled = false;
		document.getElementById('viewmatrix').disabled = false;
	}
}