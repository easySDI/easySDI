function changeValues()
{
	if(document.getElementById('servletClass').value == 'org.easysdi.proxy.csw.CSWProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="block";
		applyDisplay ("none","block");
		document.getElementById('servicemetadata_contact').style.display="block";
		document.getElementById('exceptionMode').style.display="none";
	}
	else if (document.getElementById('servletClass').value == 'org.easysdi.proxy.wfs.WFSProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="none";
		applyDisplay ("none","block");
		document.getElementById('servicemetadata_contact').style.display="none";
		document.getElementById('exceptionMode').style.display="block";
	}	
	else if (document.getElementById('servletClass').value == 'org.easysdi.proxy.wms.WMSProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="none";
		applyDisplay ("block","none");
		document.getElementById('servicemetadata_contact').style.display="block";
		document.getElementById('exceptionMode').style.display="block";
	}
	else
	{
		document.getElementById('specificGeonetowrk').style.display="none";
		document.getElementById('service_metadata').style.display="none";
		
	}
}


function applyDisplay (value1,value2)
{
	document.getElementById('service_metadata').style.display="block";
	document.getElementById('service_contacttype').style.display=value1;
	document.getElementById('service_contacttype_t').style.display=value1;
	document.getElementById('service_contactlinkage').style.display=value2;
	document.getElementById('service_contactlinkage_t').style.display=value2;
	document.getElementById('service_contacthours').style.display=value2;
	document.getElementById('service_contacthours_t').style.display=value2;
	document.getElementById('service_contactinstructions').style.display=value2;
	document.getElementById('service_contactinstructions_t').style.display=value2;
	
}