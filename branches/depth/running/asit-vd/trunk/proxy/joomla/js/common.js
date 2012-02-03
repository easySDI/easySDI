function changeValues()
{
	if(document.getElementById('servletClass').value == 'org.easysdi.proxy.csw.CSWProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="block";
	}
	else
	{
		document.getElementById('specificGeonetowrk').style.display="none";
	}
}


function submitbutton(pressbutton)
{
	if (pressbutton=="addNewServer")
	{	
		addNewServer();
	}
	else
	{	
		submitform(pressbutton);
	}
}