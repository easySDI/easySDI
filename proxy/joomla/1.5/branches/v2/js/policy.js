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
	
		document.getElementById('scaleMin@'+server+'@'+layerName).disabled=true;
		document.getElementById('scaleMin@'+server+'@'+layerName).value ="";
		document.getElementById('scaleMax@'+server+'@'+layerName).disabled=true;
		document.getElementById('scaleMax@'+server+'@'+layerName).value ="";
		document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=true;
		document.getElementById('LocalFilter@'+server+'@'+layerName).value ="";	
	}
}

function disableOperationCheckBoxes()
{
	var check = document.getElementById('AllOperations').checked;
	
	document.getElementById('oGetCapabilities').disabled=check;
	document.getElementById('oGetMap').disabled=check;
	document.getElementById('oGetFeatureInfo').disabled=check;
//	document.getElementById('oDescribeLayer').disabled=check;
	document.getElementById('oGetLegendGraphic').disabled=check;
//	document.getElementById('oGetStyles').disabled=check;
//	document.getElementById('oPutStyles').disabled=check;
	document.getElementById('oGetCapabilities').checked=check;
	document.getElementById('oGetMap').checked=check;
	document.getElementById('oGetFeatureInfo').checked=check;
//	document.getElementById('oDescribeLayer').checked=check;
	document.getElementById('oGetLegendGraphic').checked=check;
//	document.getElementById('oGetStyles').checked=check;
//	document.getElementById('oPutStyles').checked=check;
}
function disableServers ()
{
	var nb = 0;
	var display = "block";
	if (document.getElementById('AllServers').checked)
		display="none";
	
	while (document.getElementById('adminTable@'+nb) != null)
	{
		document.getElementById('adminTable@'+nb).style.display=display;
		nb ++;
	}	
}

function disableLayers(iServ)
{
	var iLay = 0;
	var check = document.getElementById('AllLayers@'+iServ).checked;
	
	while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
	{
		document.getElementById('layer@'+iServ+'@'+iLay).disabled = check;
		document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
		if(check)
		{
			document.getElementById('scaleMin@'+iServ+'@'+iLay).disabled=check;
			document.getElementById('scaleMax@'+iServ+'@'+iLay).disabled=check;
			document.getElementById('LocalFilter@'+iServ+'@'+iLay).disabled=check;
		}
		iLay ++;
	}
}