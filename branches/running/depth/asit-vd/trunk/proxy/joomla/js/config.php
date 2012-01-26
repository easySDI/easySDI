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
var currentServerIndex;

function negoVersionServer(servNo,service,availableVersions){
	var url = document.getElementById("URL_"+servNo).value;
	var user = document.getElementById("URL_"+servNo).value;
	var password = document.getElementById("PASSWORD_"+servNo).value;
	currentServerIndex = servNo;
	
    request = getHTTPObject();
    document.getElementById("progress").style.visibility = "visible";
    request.onreadystatechange = sendData;
    request.open("GET", "index.php?option=com_easysdi_proxy&task=negociateVersionForServer&url="+url+"&user="+user+"&password="+password+"&service="+service+"&availableVersions="+availableVersions, true);
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
    	document.getElementById("progress").style.visibility = "hidden";
		var JSONtext = request.responseText;
		var unsupportedVersions = availableVersions;
		var JSONobject = JSON.parse(JSONtext, function (key, value) {
		    var type;
		    if (value && typeof value === 'string') {
		    	var version = document.getElementById(value+"_"+currentServerIndex);
		    	version.setAttribute("class","supported");
		    	document.getElementById(value+"_"+currentServerIndex+"_state").value = "supported";
		    	unsupportedVersions = unsupportedVersions.remove(value);
		    }
		});
		var len = unsupportedVersions.length;
		for (var i = 0; i<len; i++) {
			var version = document.getElementById(unsupportedVersions[i]+"_"+currentServerIndex);
	    	version.setAttribute("class","unsupported");
	    	document.getElementById(unsupportedVersions[i]+"_"+currentServerIndex+"_state").value = "unsupported";
		}
    }
}

Array.prototype.remove = function(obj) {
	  var a = [];
	  for (var i=0; i<this.length; i++) {
	    if (this[i] != obj) {
	      a.push(this[i]);
	    }
	  }
	  return a;
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
	var tdVersions = document.createElement('td');

	var inputAlias = document.createElement('input');
	inputAlias.size=20;
	inputAlias.type="text";
	inputAlias.name="ALIAS_"+nbServer;
	inputAlias.id="ALIAS_"+nbServer;
				
	var inputUrl = document.createElement('input');
	inputUrl.size=70;
	inputUrl.type="text";
	inputUrl.name="URL_"+nbServer;
	inputUrl.id="URL_"+nbServer;
	
	var inputUser = document.createElement('input');
	inputUser.type="text";
	inputUser.name="USER_"+nbServer;
	inputUser.id="USER_"+nbServer;
	
	var inputPassword = document.createElement('input');
	inputPassword.type="password";
	inputPassword.name="PASSWORD_"+nbServer;
	inputPassword.id="PASSWORD_"+nbServer;

	tdAlias.appendChild(inputAlias);
	tr.appendChild(tdAlias);
	tdUrl.appendChild(inputUrl);
	tr.appendChild(tdUrl);
	tdUser.appendChild(inputUser);
	tr.appendChild(tdUser);
	tdPwd.appendChild(inputPassword);

	tr.appendChild(tdPwd);
	
	var vButton = document.createElement('a');
	vButton.setAttribute("onClick","javascript:negoVersionServer("+nbServer+",'"+service+"','"+JSON.stringify(availableVersions)+"');");
	vButton.setAttribute("href","#");
	var vImg = document.createElement ('img');
	vImg.setAttribute("class","helpTemplate");
	vImg.setAttribute("src","../templates/easysdi/icons/silk/arrow_switch.png");
	vImg.setAttribute("alt","Version");
	vButton.appendChild(vImg);
	tdNegociate.appendChild(vButton);
	tr.appendChild(tdNegociate);

	var tdVersionsTable = document.createElement('table');
	var tdVersionsTableTr = document.createElement('tr');
	var len = availableVersions.length;
	for (var i = 0; i<len; i++) {
		var tdVersionsTableTd = document.createElement('td');
		tdVersionsTableTd.setAttribute("class","unknown");
		tdVersionsTableTd.setAttribute("id",availableVersions[i]+"_"+nbServer);
		var ta = document.createTextNode(availableVersions[i]);
		tdVersionsTableTd.appendChild(ta);

		var tdVersionsTableTdInput = document.createElement('input');
		tdVersionsTableTdInput.setAttribute("type","hidden");
		tdVersionsTableTdInput.setAttribute("name",availableVersions[i]+"_"+nbServer+"_state");
		tdVersionsTableTdInput.setAttribute("id",availableVersions[i]+"_"+nbServer+"_state");
		tdVersionsTableTdInput.setAttribute("value","unknown");
		tdVersionsTableTd.appendChild(tdVersionsTableTdInput);
		
		tdVersionsTableTr.appendChild(tdVersionsTableTd);
	}
	tdVersionsTable.appendChild(tdVersionsTableTr);
	tdVersions.appendChild(tdVersionsTable);
	tr.appendChild(tdVersions);
	
	var aButton = document.createElement('input');
	aButton.type="button";
	aButton.value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>";
	aButton.setAttribute("onClick","javascript:removeServer("+nbServer+");");
	tdRemove.appendChild(aButton);
	tr.appendChild(tdRemove);
	
	document.getElementById("remoteServerTable").appendChild(tr);
	nbServer = nbServer + 1;
}
</script>
<?php 
?>