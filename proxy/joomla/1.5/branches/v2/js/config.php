<?php 

?>
<script>
function changeValues()
{
	if(document.getElementById('servletClass').value == 'org.easysdi.proxy.csw.CSWProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="block";
		applyDisplay ("none","block");
		document.getElementById('servicemetadata_contact').style.display="block";
		document.getElementById('exceptionMode').style.display="none";
		document.getElementById('ogcSearchFilterFS').style.display="block";
		document.getElementById('removeServerButton').style.display="none";
	}
	else if (document.getElementById('servletClass').value == 'org.easysdi.proxy.wfs.WFSProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="none";
		applyDisplay ("none","block");
		document.getElementById('servicemetadata_contact').style.display="none";
		document.getElementById('exceptionMode').style.display="block";
		document.getElementById('ogcSearchFilterFS').style.display="none";
		document.getElementById('removeServerButton').style.display="block";
	}	
	else if (document.getElementById('servletClass').value == 'org.easysdi.proxy.wms.WMSProxyServlet')
	{
		document.getElementById('specificGeonetowrk').style.display="none";
		applyDisplay ("block","none");
		document.getElementById('servicemetadata_contact').style.display="block";
		document.getElementById('exceptionMode').style.display="block";
		document.getElementById('ogcSearchFilterFS').style.display="none";
		document.getElementById('removeServerButton').style.display="block";
	}
	else
	{
		document.getElementById('specificGeonetowrk').style.display="none";
		document.getElementById('service_metadata').style.display="none";
		document.getElementById('ogcSearchFilterFS').style.display="none";
		document.getElementById('removeServerButton').style.display="block";
		
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

function submitbutton(pressbutton){

	if (pressbutton=="addNewServer"){	
		addNewServer();
	}
	else if (pressbutton=="saveConfig")
	{	
		if(document.getElementById('logPath').value == "" || 
		   document.getElementById('logPrefix').value == "" || 
		   document.getElementById('logSuffix').value == "" )
		{
			alert ('<?php echo  JText::_( 'PROXY_CONFIG_EDIT_VALIDATION_LOGFILE_ERROR');?>');	
			return;
		}
		if(document.getElementById('service_title').value == ""  )
		{
			alert ('<?php echo  JText::_( 'PROXY_CONFIG_EDIT_VALIDATION_SERVICE_MD_ERROR');?>');	
			return;
		}
		if(document.getElementById('servletClass').value == 'org.easysdi.proxy.csw.CSWProxyServlet')
		{
			document.getElementById('service_contacttype').value = "";
		}
		else if (document.getElementById('servletClass').value == 'org.easysdi.proxy.wfs.WFSProxyServlet')
		{
			document.getElementById('service_contactorganization').value=""; 
			document.getElementById('service_contactperson').value=""; 
			document.getElementById('service_contactposition').value="";
			document.getElementById('service_contacttype').value="";
			document.getElementById('service_contactadress').value="";
			document.getElementById('service_contactpostcode').value="";
			document.getElementById('service_contactcity').value="";
			document.getElementById('service_contactstate').value="";
			document.getElementById('service_contactcountry').value="";
			document.getElementById('service_contacttel').value="";
			document.getElementById('service_contactfax').value="";
			document.getElementById('service_contactmail').value="";
			document.getElementById('service_contactlinkage').value="";
			document.getElementById('service_contacthours').value="";
			document.getElementById('service_contactinstructions').value="";
		}	
		else if (document.getElementById('servletClass').value == 'org.easysdi.proxy.wms.WMSProxyServlet')
		{
			document.getElementById('service_contactlinkage').value="";
			document.getElementById('service_contacthours').value="";
			document.getElementById('service_contactinstructions').value="";
		}
		submitform(pressbutton);
	}
	else
	{
		submitform(pressbutton);
	}
}



function removeServer(servNo){

	noeud = document.getElementById("remoteServerTable");
	var fils = noeud.childNodes;
	
	noeud.removeChild(fils[servNo]);
	
	noeud = document.getElementById("remoteServerTable");
	fils = noeud.childNodes;
	var nbFils = fils.length;
	
	for(var i = 0; i < nbFils; i++){
			fils[i].childNodes[0].childNodes[0].name="URL_"+i;	
			fils[i].childNodes[1].childNodes[0].name="USER_"+i;
			fils[i].childNodes[2].childNodes[0].name="PASSWORD_"+i;						
			fils[i].childNodes[2].childNodes[1].setAttribute("onClick","javascript:removeServer("+i+");");
	} 
	nbServer = nbServer - 1;
}

function addNewServer(){
	
	var tr = document.createElement('tr');	
	var tdUrl = document.createElement('td');
	tdUrl.className="key";
	
	var tdUser = document.createElement('td');
	var tdPwd = document.createElement('td');				
	var inputUrl = document.createElement('input');
	inputUrl.size=70;
	inputUrl.type="text";
	inputUrl.name="URL_"+nbServer;
	
	var inputUser = document.createElement('input');
	inputUser.type="text";
	inputUser.name="USER_"+nbServer;
	
	var inputPassword = document.createElement('input');
	inputPassword.type="password";
	inputPassword.name="PASSWORD_"+nbServer;
	
	tdUrl.appendChild(inputUrl);
	tr.appendChild(tdUrl);
	tdUser.appendChild(inputUser);
	tr.appendChild(tdUser);
	
	tdPwd.appendChild(inputPassword);
	
	var aButton = document.createElement('input');
	aButton.type="button";
	aButton.value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>";
	aButton.setAttribute("onClick","javascript:removeServer("+nbServer+");");
		
	tdPwd.appendChild(aButton);
	
	
	tr.appendChild(tdPwd);
	
	document.getElementById("remoteServerTable").appendChild(tr);
	
	
	nbServer = nbServer + 1;
}
</script>
<?php 
?>