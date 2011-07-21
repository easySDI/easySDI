var geoQueryValid = new Array();

function addOption(selectList,myText)
{
	var elOptNew = document.createElement('option'); 
	elOptNew.text = document.getElementById(myText).value ; 
	elOptNew.value = document.getElementById(myText).value ;
	var elSel = document.getElementById(selectList);
	try { elSel.add(elOptNew, null);  } 
	catch(ex) {elSel.add(elOptNew); }

}
function removeOptionSelected(selectX)
{
  var elSel = document.getElementById(selectX);
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}

function disableList(chkBox,list)
{
	if (document.getElementById(chkBox).checked==true)
	{
		document.getElementById(list).disabled=true;
		for (i = document.getElementById(list).length - 1; i>=0; i--) 
		{
		    document.getElementById(list).options[i].selected = false;
		}
	}
	else
	{
		document.getElementById(list).disabled=false;
	}
}

function disableButton(chkBox,button){

if (document.getElementById(chkBox).checked==true){
document.getElementById(button).disabled=true;
}else{
document.getElementById(button).disabled=false;
}
}

function activateAttributeList(server,featureType)
{
	if (document.getElementById('selectAttribute@'+server+'@'+featureType).checked==true){
		document.getElementById('AttributeList@'+server+'@'+featureType).disabled=false;
		document.getElementById('AttributeList@'+server+'@'+featureType).value="";
	}
	else
	{
		document.getElementById('AttributeList@'+server+'@'+featureType).disabled=true;
		document.getElementById('AttributeList@'+server+'@'+featureType).value="";
	}
}
function activateFeatureType(server,featureType){


	if (document.getElementById('featuretype@'+server+'@'+featureType).checked==true){
		document.getElementById('LocalFilter@'+server+'@'+featureType).disabled=false;
		document.getElementById('LocalFilter@'+server+'@'+featureType).value = "";
		document.getElementById('RemoteFilter@'+server+'@'+featureType).disabled=false;		
		document.getElementById('RemoteFilter@'+server+'@'+featureType).value = "";
		document.getElementById('selectAttribute@'+server+'@'+featureType).checked = false;
		document.getElementById('selectAttribute@'+server+'@'+featureType).disabled = false;
		document.getElementById('AttributeList@'+server+'@'+featureType).disabled=true;
		document.getElementById('AttributeList@'+server+'@'+featureType).value="";

	}
	else
	{	
		document.getElementById('AllFeatureTypes@'+nb).checked = false;
		document.getElementById('LocalFilter@'+server+'@'+featureType).disabled=true;
		document.getElementById('LocalFilter@'+server+'@'+featureType).value = "";
		document.getElementById('RemoteFilter@'+server+'@'+featureType).disabled=true;		
		document.getElementById('RemoteFilter@'+server+'@'+featureType).value = "";
		document.getElementById('selectAttribute@'+server+'@'+featureType).checked = false;
		document.getElementById('selectAttribute@'+server+'@'+featureType).disabled = true;
		document.getElementById('AttributeList@'+server+'@'+featureType).disabled=true;
		document.getElementById('AttributeList@'+server+'@'+featureType).value="";
		
	}
}

function CheckQuery(server,featureType)
{
		var remote = document.getElementById('RemoteFilter@'+server+'@'+featureType).value;
		var local = document.getElementById('LocalFilter@'+server+'@'+featureType).value;
		if (remote.length == 0 && local.length >0)
		{
			geoQueryValid[geoQueryValid.length] = 'RemoteFilter@'+server+'@'+featureType;
			document.getElementById('RemoteFilter@'+server+'@'+featureType).style.backgroundColor = "#E2A09B";
		}
		else
		{
			geoQueryValid.remove('RemoteFilter@'+server+'@'+featureType);
			
			document.getElementById('RemoteFilter@'+server+'@'+featureType).style.backgroundColor = document.getElementById('LocalFilter@'+server+'@'+featureType).style.backgroundColor;
		}
}

Array.prototype.remove=function(s){
	for(i=0; i < this.length ; i++)
	{
		if(s==this[i])
		{
			this.splice(i, 1);
			return;
		}
	}
}
function fillTextArea (elementId, text)
{
	document.getElementById(elementId).value = "";
	document.getElementById(elementId).value = text;
}

function activateLayer(server,layerName){


	if (document.getElementById('layer@'+server+'@'+layerName).checked==true){
		document.getElementById('scaleMin@'+server+'@'+layerName).disabled=false;
		document.getElementById('scaleMax@'+server+'@'+layerName).disabled=false;
		document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=false;
		
	}else{
		document.getElementById('AllLayers@'+server).checked = false;
		document.getElementById('scaleMin@'+server+'@'+layerName).disabled=true;
		document.getElementById('scaleMin@'+server+'@'+layerName).value ="";
		document.getElementById('scaleMax@'+server+'@'+layerName).disabled=true;
		document.getElementById('scaleMax@'+server+'@'+layerName).value ="";
		document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=true;
		document.getElementById('LocalFilter@'+server+'@'+layerName).value ="";	
	}
}


function disableServersLayers ()
{
	var nb = 0;
	var iLay = 0;
	var display = "block";
	var check = document.getElementById('AllServers').checked;
	if (document.getElementById('AllServers').checked)
	{
		display="none";
	}
	
	while (document.getElementById('remoteServerTable@'+nb) != null)
	{
		document.getElementById('remoteServerTable@'+nb).style.display=display;
		document.getElementById('AllLayers@'+nb).checked = check;
		while (document.getElementById('layer@'+nb+'@'+iLay) != null)
		{
			document.getElementById('layer@'+nb+'@'+iLay).checked = check;
			document.getElementById('scaleMin@'+nb+'@'+iLay).disabled=!check;
			document.getElementById('scaleMax@'+nb+'@'+iLay).disabled=!check;
			document.getElementById('LocalFilter@'+nb+'@'+iLay).disabled=!check;
			iLay ++;
		}
		iLay = 0;
		nb ++;
	}	
}

function disableServersFeatureTypes ()
{
	var nb = 0;
	var iFeat = 0;
	var display = "block";
	var check = document.getElementById('AllServers').checked;
	if (document.getElementById('AllServers').checked)
	{
		display="none";
	}
	
	while (document.getElementById('remoteServerTable@'+nb) != null)
	{
		document.getElementById('remoteServerTable@'+nb).style.display=display;
		document.getElementById('AllFeatureTypes@'+nb).checked = check;
		while (document.getElementById('featuretype@'+nb+'@'+iFeat) != null)
		{
			document.getElementById('featuretype@'+nb+'@'+iFeat).checked = check;
			document.getElementById('selectAttribute@'+nb+'@'+iFeat).disabled=check;
			document.getElementById('AttributeList@'+nb+'@'+iFeat).disabled=check;
			document.getElementById('RemoteFilter@'+nb+'@'+iFeat).disabled=check;
			document.getElementById('LocalFilter@'+nb+'@'+iFeat).disabled=check;
			iFeat ++;
		}
		iFeat = 0;
		nb ++;
	}	
}

function disableLayers(iServ)
{
	var iLay = 0;
	var check = document.getElementById('AllLayers@'+iServ).checked;
	
	while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
	{
		document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
		document.getElementById('scaleMin@'+iServ+'@'+iLay).disabled=check;
		document.getElementById('scaleMax@'+iServ+'@'+iLay).disabled=check;
		document.getElementById('LocalFilter@'+iServ+'@'+iLay).disabled=check;
		
		iLay ++;
	}
}

function disableWMTSLayers(iServ)
{
	var iLay = 0;
	var check = document.getElementById('AllLayers@'+iServ).checked;
	
	while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
	{
		document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
		iLay ++;
	}
}

function disableWMTSServersLayers ()
{
	var nb = 0;
	var iLay = 0;
	var display = "block";
	var check = document.getElementById('AllServers').checked;
	if (document.getElementById('AllServers').checked)
	{
		display="none";
	}
	
	while (document.getElementById('remoteServerTable@'+nb) != null)
	{
		document.getElementById('remoteServerTable@'+nb).style.display=display;
		document.getElementById('AllLayers@'+nb).checked = check;
		while (document.getElementById('layer@'+nb+'@'+iLay) != null)
		{
			document.getElementById('layer@'+nb+'@'+iLay).checked = check;
			iLay ++;
		}
		iLay = 0;
		nb ++;
	}	
}

function disableFeatureTypes(iServ)
{
	var iFeat = 0;
	var check = document.getElementById('AllFeatureTypes@'+iServ).checked;
	
	while (document.getElementById('featuretype@'+iServ+'@'+iFeat) != null)
	{
//		document.getElementById('featuretype@'+iServ+'@'+iFeat).disabled = check;
		document.getElementById('featuretype@'+iServ+'@'+iFeat).checked = check;
		document.getElementById('selectAttribute@'+iServ+'@'+iFeat).disabled=check;
		document.getElementById('AttributeList@'+iServ+'@'+iFeat).disabled=check;
		document.getElementById('RemoteFilter@'+iServ+'@'+iFeat).disabled=check;
		document.getElementById('LocalFilter@'+iServ+'@'+iFeat).disabled=check;
		
		iFeat ++;
	}
}

function addNewMetadataToExclude(nbParam,nbServer)
{
	var tr = document.createElement('tr');	
	var tdParam = document.createElement('td');	
	var inputParam = document.createElement('input');
	inputParam.size=200;
	inputParam.type="text";
	inputParam.name="param_"+nbServer+"_"+document.getElementById(nbParam).value;
	tdParam.appendChild(inputParam);
	tr.appendChild(tdParam);
	document.getElementById("metadataParamTable").appendChild(tr);
	document.getElementById(nbParam).value = document.getElementById(nbParam).value +1 ;
}

function disableVisibilitiesCheckBoxes ()
{
	var check = document.getElementById('AllVisibilities').checked;

	var visibilityArray = new Array();
	visibilityArray = document.getElementsByName('visibility[]');
	for ( i = 0 ; i < visibilityArray.length ; i++)
	{
		visibilityArray[i].disabled = check;
		visibilityArray[i].checked = check;
	}
}
function disableStatusCheckBoxes ()
{
	var check = document.getElementById('AllStatus').checked;

	var statusArray = new Array();
	statusArray = document.getElementsByName('status[]');
	for ( i = 0 ; i < statusArray.length ; i++)
	{
		statusArray[i].disabled = check;
		statusArray[i].checked = check;
	}
	if(check)
	{
		document.getElementsByName('objectversion_mode')[0].disabled = check;
		document.getElementsByName('objectversion_mode')[1].disabled = check;
		document.getElementsByName('objectversion_mode')[1].checked = check;
	}
	
}

function disableCheckBoxes (nameAll, name)
{
	var check = document.getElementById(nameAll).checked;

	var objectArray = new Array();
	objectArray = document.getElementsByName(name);
	for ( i = 0 ; i < objectArray.length ; i++)
	{
		objectArray[i].disabled = check;
		objectArray[i].checked = check;
	}
}
function disableVersionModeRadio()
{
	var check = !document.getElementById('published').checked;
	document.getElementsByName('objectversion_mode')[0].disabled = check;
	document.getElementsByName('objectversion_mode')[1].disabled = check;
	
}

