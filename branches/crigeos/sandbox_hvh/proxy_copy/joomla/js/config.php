<?php 

?>
<script>


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
		document.getElementById('nbServer').value = nbServer;
		for(i = 0; i<=nbServer ; i++)
		{
			if(document.getElementById('ALIAS_'+i) != null)
			{
				if(document.getElementById('ALIAS_'+i).value == "")
				{
					alert ('<?php echo  JText::_( 'PROXY_CONFIG_EDIT_VALIDATION_SERVICE_ALIAS_ERROR');?>');	
					return;
				}
			}
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
	var fils = document.getElementById("remoteServerTableRow"+servNo);
	noeud.removeChild(fils);
}

function addNewServer(){
	
	var tr = document.createElement('tr');	
	tr.id = "remoteServerTableRow"+nbServer;
		
	var tdAlias = document.createElement('td');
	var tdUrl = document.createElement('td');
	var tdUser = document.createElement('td');
	var tdPwd = document.createElement('td');	

	var inputAlias = document.createElement('input');
	inputAlias.size=20;
	inputAlias.type="text";
	inputAlias.name="ALIAS_"+nbServer;
	inputAlias.id="ALIAS_"+nbServer;
				
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

	tdAlias.appendChild(inputAlias);
	tr.appendChild(tdAlias);
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