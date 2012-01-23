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

var request;

function negoVersionServer(servNo){
	var url = document.getElementById("URL_"+servNo).value;
	var user = document.getElementById("URL_"+servNo).value;
	var password = document.getElementById("PASSWORD_"+servNo).value;
	var service = document.getElementById("servletClass").value;
    request = getHTTPObject();
    request.onreadystatechange = sendData;
    request.open("GET", "index.php?option=com_easysdi_proxy&task=negociateVersionForServer&url="+url+"&user="+user+"&password="+password+"&service="+service, true);
    request.send(null);
}

function getHTTPObject(){
    var xhr = false;
    if (window.XMLHttpRequest){
        xhr = new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        try{
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        }catch(e){
            try{
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }catch(e){
                xhr = false;
            }
        }
    }
    return xhr;
}

function sendData()
{
    // if request object received response
    if(request.readyState == 4){
		var JSONtext = request.responseText;
		// convert received string to JavaScript object
		var JSONobject = JSON.parse(JSONtext);
 		// notice how variables are used
		var msg = "Number of errors: "+
		"\n- "+JSONobject.version[0]+
		"\n- "+JSONobject.version[1];
 
		alert(msg);
    }
}

function addNewServer(){
	
	var tr = document.createElement('tr');	
	tr.id = "remoteServerTableRow"+nbServer;
		
	var tdAlias = document.createElement('td');
	var tdUrl = document.createElement('td');
	var tdUser = document.createElement('td');
	var tdPwd = document.createElement('td');	
	var tdRemove = document.createElement('td');	
	var tdNegociate = document.createElement('td');

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

	tr.appendChild(tdPwd);
	
	var vButton = document.createElement('a');
	vButton.setAttribute("onClick","javascript:negoVersionServer("+nbServer+");");
	vButton.setAttribute("href","#");
	var vImg = document.createElement ('img');
	vImg.setAttribute("class","helpTemplate");
	vImg.setAttribute("src","../templates/easysdi/icons/silk/arrow_switch.png");
	vImg.setAttribute("alt","Version");
	vButton.appendChild(vImg);
	
	var aButton = document.createElement('input');
	aButton.type="button";
	aButton.value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>";
	aButton.setAttribute("onClick","javascript:removeServer("+nbServer+");");

	tdNegociate.appendChild(vButton);
	tdRemove.appendChild(aButton);
	
	tr.appendChild(tdNegociate);
	tr.appendChild(tdRemove);
	
	
	document.getElementById("remoteServerTable").appendChild(tr);
	
	
	nbServer = nbServer + 1;
}
</script>
<?php 
?>