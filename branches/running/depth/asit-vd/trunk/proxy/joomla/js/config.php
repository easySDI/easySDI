<script>

/**
 * @function submitbutton 
 */
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

		var t = document.getElementById('supportedVersionsByConfig').value;
		if(t == '["NA"]' || t == "[]"){
			alert ('<?php echo  JText::_( 'PROXY_CONFIG_EDIT_VALIDATION_CONFIG_SUPPORTED_VERSION_ERROR');?>');
			return;
		}

		var elements = getElementsByClassName(document.getElementById('remoteServerTable'), "unknown");
	    if (elements.length > 0 ){
			alert ('<?php echo  JText::_( 'PROXY_CONFIG_EDIT_VALIDATION_SERVER_NEGOTIATION_MISSING_ERROR');?>');
			return;
		}
		
		submitform(pressbutton);
	}
	else
	{
		submitform(pressbutton);
	}
}


/**
 * @function removeServer
 */
function removeServer(servNo){

	noeud = document.getElementById("remoteServerTable");
	var fils = document.getElementById("remoteServerTableRow"+servNo);
	noeud.removeChild(fils);

	setConfigVersion();
}

var request;
var currentServerIndex;

/**
 * @function negoVersionServer
 * Build the request for the proxy PHP that will perform the negociation version.
 */
function negoVersionServer(servNo,service,availableVersions){
	var url = document.getElementById("URL_"+servNo).value;
	var user = document.getElementById("USER_"+servNo).value;
	var password = document.getElementById("PASSWORD_"+servNo).value;
	currentServerIndex = servNo;
	
    request = getHTTPObject();
    document.getElementById("progress").style.visibility = "visible";
    request.onreadystatechange = getSupportedVersions;
    request.open("GET", "index.php?option=com_easysdi_proxy&task=negociateVersionForServer&url="+url+"&user="+user+"&password="+password+"&service="+service+"&availableVersions="+availableVersions, true);
    request.send(null);
}

/**
 * Instantiate Http Request
 */
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

/**
 * @function getSupportedVersions
 * Get the request response and fill appropriate fields in the document
 */
function getSupportedVersions()
{
    // if request object received response
    if(request.readyState == 4){
    	document.getElementById("progress").style.visibility = "hidden";
		var JSONtext = request.responseText;
		var unsupportedVersions = availableVersions;

		//Set the supported versions (for the server)
		var JSONobject = JSON.parse(JSONtext, function (key, value) {
		    var type;
		    if (value && typeof value === 'string') {
		    	var version = document.getElementById(value+"_"+currentServerIndex);
		    	version.setAttribute("class","supportedversion");
		    	document.getElementById(value+"_"+currentServerIndex+"_state").value = "supported";
		    	unsupportedVersions = unsupportedVersions.remove(value);
		    }
		});

		//None of the available versions are supported by the remote server
		//Send a negotiation error
		if(availableVersions.length == unsupportedVersions.length){
			alert('<?php echo  JText::sprintf( 'PROXY_CONFIG_NEGOTIATION_VERSION_FAILED_FOR_A_SERVER', i);?>');
			document.getElementById("supportedVersionsByConfig").value=JSON.stringify(new Array('NA'));
			removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
			document.getElementById("supportedVersionsByConfigText").appendChild(createSupportedVersionByConfigTable(new Array('NA'))) ;
			return;
		}

		//Set the unsupported versions (for the server)
		var len = unsupportedVersions.length;
		for (var i = 0; i<len; i++) {
			var version = document.getElementById(unsupportedVersions[i]+"_"+currentServerIndex);
	    	version.setAttribute("class","unsupportedversion");
	    	document.getElementById(unsupportedVersions[i]+"_"+currentServerIndex+"_state").value = "unsupported";
		}

		//Set the negociated version (for the config)
		setConfigVersion();
    }
}

function setConfigVersion (){
	var supportedElementsArray = getElementsByValue('supported');
	var supportedVersionByServer = new Array();
	
	for (var i = 0; i < supportedElementsArray.length; i++ ){
		var  id = supportedElementsArray[i];
		var version = id.substring(0,id.indexOf("_", 0));
		var server = id.substring(id.indexOf("_", 0)+1,id.indexOf("_", id.indexOf("_", 0)+1));
		if(supportedVersionByServer[server] == undefined){
			supportedVersionByServer[server] = new Array();
		}
		var j = supportedVersionByServer[server].length;
		supportedVersionByServer[server][j]= version;
		supportedVersionByServer[server].sort();
		supportedVersionByServer[server].reverse();
	}

	//Cases were negotiation failed
	if(supportedVersionByServer.length == 0){
		alert('<?php echo  JText::_( 'PROXY_CONFIG_NEGOTIATION_VERSION_FAILED_FOR_ALL_SERVER');?>');
		document.getElementById("supportedVersionsByConfig").value=JSON.stringify(new Array('NA'));
		removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
		document.getElementById("supportedVersionsByConfigText").appendChild(createSupportedVersionByConfigTable(new Array('NA'))) ;
		return;
	}
	for(var i = 0;i < supportedVersionByServer.length;i++){
		if(supportedVersionByServer[i] == undefined)
		{
			alert('<?php echo  JText::sprintf( 'PROXY_CONFIG_NEGOTIATION_VERSION_FAILED_FOR_A_SERVER', i);?>');
			document.getElementById("supportedVersionsByConfig").value=JSON.stringify(new Array('NA'));
			removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
			document.getElementById("supportedVersionsByConfigText").appendChild(createSupportedVersionByConfigTable(new Array('NA'))) ;
			return;
		}
	}

	//Negotiation
	var i = 0;
	var v = 0;
	var sNegotiatedVersion = supportedVersionByServer[i][v];
	while (i < supportedVersionByServer.length)	{		
		for(var j = 0 ; j < supportedVersionByServer[i].length ; j++){
			if(sNegotiatedVersion == supportedVersionByServer[i][j]){
				i= i + 1;
				break;
			}else{
				if (j == supportedVersionByServer[i].length-1 && i == supportedVersionByServer.length-1){
					sNegotiatedVersion = 'NA';
				}
				if (j == supportedVersionByServer[i].length-1){
					v= v+1;
					i=0;
					sNegotiatedVersion = supportedVersionByServer[i][v];
				}
			}
		}
	}

	
	var aNupportedVersionByConfig = supportedVersionByServer[0];
	i = 1;
	v = 0;
	var sCurrentVersion = supportedVersionByServer[0][0];
	
	while (i < supportedVersionByServer.length)	{		
		for(var j = 0 ; j < supportedVersionByServer[i].length ; j++){
			if(sCurrentVersion == supportedVersionByServer[i][j]){
				if( i == supportedVersionByServer.length - 1 && v < supportedVersionByServer[0].length -1){
					v = v+1;
					i = 1;
					sCurrentVersion = supportedVersionByServer[0][v];
				}else{
					i = i+1;
				}
				break;
			}else if (j == supportedVersionByServer[i].length-1 ){
				if(aNupportedVersionByConfig.contains(sCurrentVersion))
					aNupportedVersionByConfig = aNupportedVersionByConfig.remove(sCurrentVersion);
				if (v < supportedVersionByServer[0].length -1 ){
					v = v+1;
					i = 1;
					sCurrentVersion = supportedVersionByServer[0][v];
				}else{
					i = supportedVersionByServer.length;
					break;
				}
			}
		}
	}

	document.getElementById("supportedVersionsByConfig").value=JSON.stringify(aNupportedVersionByConfig);
	removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
	document.getElementById("supportedVersionsByConfigText").appendChild(createSupportedVersionByConfigTable(aNupportedVersionByConfig)) ; 
}

/**
 * 
 */
function removeAllElementChild (cell){
	if ( cell.hasChildNodes() )
	{
	    while ( cell.childNodes.length >= 1 )
	    {
	        cell.removeChild( cell.firstChild );       
	    } 
	}
}

/**
 * 
 */
function createSupportedVersionByConfigTable(aNupportedVersionByConfig){
	var table = document.createElement('table');
	var tr = document.createElement('tr');
	table.appendChild(tr);
	
	for( var i = 0 ; i < aNupportedVersionByConfig.length ; i++ ){
		var td = document.createElement('td');
		var text = document.createTextNode(aNupportedVersionByConfig[i]);
		td.setAttribute("class","supportedversion");
		td.appendChild(text);
		tr.appendChild(td);
	}

	return table;
}

/**
 * @function remove 
 * Remove array element.
 */
Array.prototype.remove = function(obj) {
	  var a = [];
	  for (var i=0; i<this.length; i++) {
	    if (this[i] != obj) {
	      a.push(this[i]);
	    }
	  }
	  return a;
	}

/**
 * @function contains
 Return if the array contains the specified object
 */
Array.prototype.contains = function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i] == obj) {
            return true;
        }
    }
    return false;
}
/**
* @function getElementsByValue
* Finds elements in FORMs whose value property matches
* the given value.
*
* @param val (string, required)
* The value to search for.
*
* @param src (variable, optional)
* Node reference or Id to an HTML tag to start searching in.
*
* @return array
* An array of all matching elements.
*/
function getElementsByValue(val, src) {
	var forms, fields;
	var matches = [];
	var i = j = forms_end = fields_end = 0;

	if (document.getElementsByTagName) {
		if (!src) {
			src = document;
		} else if (typeof(src) === 'string') {
			src = document.getElementById(src);
		}
		forms = src.getElementsByTagName('form');

		forms_end = forms.length;
		for (i = 0; i < forms_end; i++) {
			fields = forms[i].elements;
			fields_end = fields.length
			for (j = 0; j<fields_end ; j++) {
				if (fields[j].value == val) {
					matches.push(fields[j].id);
				}
			}
		}
	}
	return matches;
}

/**
 * @getElementsByClassName
 * Finds elements in FORMs whose class property matches
 * the given value.
 */
function getElementsByClassName(node,classname) {
	  if (node.getElementsByClassName) { // use native implementation if available
	    return node.getElementsByClassName(classname);
	  } else {
	    return (function getElementsByClass(searchClass,node) {
	        if ( node == null )
	          node = document;
	        var classElements = [],
	            els = node.getElementsByTagName("*"),
	            elsLen = els.length,
	            pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)"), i, j;

	        for (i = 0, j = 0; i < elsLen; i++) {
	          if ( pattern.test(els[i].className) ) {
	              classElements[j] = els[i];
	              j++;
	          }
	        }
	        return classElements;
	    })(classname, node);
	  }
	}

/**
 * @function addNewServer :
 * Build the document elements needed to define a new server
 * ALIAS
 * URL
 * USER
 * PASSWORD
 * AvailableVersions
 * SupportedVersions
 */
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