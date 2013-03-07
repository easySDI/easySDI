
	var request;
	var selectedservice;
	var layername_select; 
	
	function init()
	{
		if(document.getElementById ('jform_asOL').checked == true)
		{
			document.getElementById ('jform_asOLstyle').disabled = false;
			document.getElementById ('jform_asOLmatrixset').disabled = false;
			document.getElementById ('jform_asOLoptions').disabled = false;
		}
		var service_select = document.getElementById('jform_service_id');
		getLayers(service_select);
		setServiceConnector(service_select);
	}

	function clearLayers (selectObj)
	{
		layername_select = document.getElementById('jform_layername');
		while ( layername_select.options.length > 0 ) layername_select.options[0] = null;
	}
	
	function getLayers (selectObj)
	{
		layername_select = document.getElementById('jform_layername');
		while ( layername_select.options.length > 0 ) layername_select.options[0] = null;
		
		var idx = selectObj.selectedIndex; 
		
		selectedservice = selectObj.options[idx].value;
		user = document.getElementById('jform_user').value;
		password = document.getElementById('jform_password').value;
		if (document.getElementById(selectedservice))
		{
			var jsonalllayers = document.getElementById(selectedservice).value; 
			var allayers = JSON.parse(jsonalllayers);
			for(var i=0; i < allayers.length ; i++){
				addLayerOption(allayers[i], allayers[i]);
			}
		} 
		else
		{
			request = false;
		    if (window.XMLHttpRequest){
		    	request = new XMLHttpRequest();
		    } else if (window.ActiveXObject) {
		        try{
		        	request = new ActiveXObject("Msxml2.XMLHTTP");
		        }catch(e){
		            try{
		            	request = new ActiveXObject("Microsoft.XMLHTTP");
		            }catch(e){
		            	request = false;
		            }
		        }
		    }
		    if(!request)
			    return;

		    var query 			= "index.php?option=com_easysdi_map&task=getLayers&service="+selectedservice+"&user="+user+"&password="+password;
			
		    document.getElementById("progress").style.visibility = "visible";
		    request.onreadystatechange = setLayers;
		    request.open("GET", query, true);
		    request.send(null);
		}		
	}

	function setLayers()
	{
	    if(request.readyState == 4){
	    	layername_select = document.getElementById('jform_layername');
			while ( layername_select.options.length > 0 ) layername_select.options[0] = null;
			
	    	document.getElementById("progress").style.visibility = "hidden";
			var JSONtext = request.responseText;
			
			if(JSONtext == "[]"){
				return;
			}

			
			var ok = true;
			
			var JSONobject = JSON.parse(JSONtext, function (key, value) {
				if(key && typeof key === 'string' && key == 'ERROR'){
					alert(value);   
					ok = false;	
					return;
			    }
			    if (value && typeof value === 'string') {
			    	addLayerOption(value, value);
			    }
			});

			if(ok)
			{
				var input = document.createElement("input");
				input.setAttribute("type", "hidden");
				input.setAttribute("name", selectedservice);
				input.setAttribute("id", selectedservice);
				input.setAttribute("value", JSONtext);
				document.getElementById("layer-form").appendChild(input);
			}
	    }
	}

	function addLayerOption (id, value)
	{
		var onloadlayername = document.getElementById('jform_onloadlayername').value;
		
		var option = new Option( id,value);
    	var i = layername_select.length;
		layername_select.options[layername_select.length] = option;
		if(onloadlayername && onloadlayername == value)
			layername_select.options[i].selected = "1";
	}
	
	
	
	function enableOlparams()
	{
		if(document.getElementById ('jform_asOL').checked == true)
		{
			document.getElementById ('jform_asOLoptions').disabled = false;
			document.getElementById ('jform_asOLoptions').value = "";
			document.getElementById ('jform_asOLstyle').disabled = false;
			document.getElementById ('jform_asOLstyle').value = ""
			document.getElementById ('jform_asOLmatrixset').disabled = false;
			document.getElementById ('jform_asOLmatrixset').value="";
		}
		else
		{
			document.getElementById ('jform_asOLoptions').disabled = true;
			document.getElementById ('jform_asOLoptions').value = "";
			document.getElementById ('jform_asOLstyle').disabled = true;
			document.getElementById ('jform_asOLstyle').value = ""
			document.getElementById ('jform_asOLmatrixset').disabled = true;
			document.getElementById ('jform_asOLmatrixset').value="";
		}
	}
	
	function setServiceConnector (selectObj)
	{
		var serviceconnectorlist = JSON.parse(document.getElementById('serviceconnectorlist').value);
		var idx = selectObj.selectedIndex; 
		var service = selectObj.options[idx].value;
		for(var i=0; i < serviceconnectorlist.length ; i++){
			var serviceconnector = JSON.parse(serviceconnectorlist[i]);
			if(serviceconnector[0] == service)
			{
				var connector =  serviceconnector[1];
				document.getElementById('jform_serviceconnector').value = connector;
				if (connector == 2){
					document.getElementById ('WMTS-info').style.display = "none";
					document.getElementById ('jform_asOL').disabled = false;
					document.getElementById ('jform_asOLoptions').style.display = "block";
					document.getElementById ('jform_asOLoptions-lbl').style.display = "block";
					document.getElementById ('jform_asOLstyle').style.display = "block";
					document.getElementById ('jform_asOLstyle-lbl').style.display = "block";
					document.getElementById ('jform_asOLmatrixset').style.display = "none";
					document.getElementById ('jform_asOLmatrixset-lbl').style.display = "none";
					document.getElementById ('jform_asOLstyle').className += " required";
					document.getElementById ('jform_asOLstyle-lbl').className += " required";
				}
				else if (connector == 11){
					document.getElementById ('WMTS-info').style.display = "none";
					document.getElementById ('jform_asOL').disabled = false;
					document.getElementById ('jform_asOLoptions').style.display = "block";
					document.getElementById ('jform_asOLoptions-lbl').style.display = "block";
					document.getElementById ('jform_asOLstyle').style.display = "block";
					document.getElementById ('jform_asOLstyle-lbl').style.display = "block";
					document.getElementById ('jform_asOLmatrixset').style.display = "none";
					document.getElementById ('jform_asOLmatrixset-lbl').style.display = "none";
					document.getElementById ('jform_asOLstyle').className += " required";
					document.getElementById ('jform_asOLstyle-lbl').className += " required";
				}
				else if (connector == 3){
					document.getElementById ('WMTS-info').style.display = "block";
					document.getElementById ('jform_asOL').disabled = false;
					document.getElementById ('jform_asOL').checked = true;
					document.getElementById ('jform_asOLoptions').style.display = "block";
					document.getElementById ('jform_asOLoptions-lbl').style.display = "block";
					document.getElementById ('jform_asOLstyle').style.display = "block";
					document.getElementById ('jform_asOLstyle-lbl').style.display = "block";
					document.getElementById ('jform_asOLmatrixset').style.display = "block";
					document.getElementById ('jform_asOLmatrixset-lbl').style.display = "block";
					document.getElementById ('jform_asOLstyle').className += " required";
					document.getElementById ('jform_asOLstyle-lbl').className += " required";
					document.getElementById ('jform_asOLmatrixset').className += " required";
					document.getElementById ('jform_asOLmatrixset-lbl').className += " required";
				}
				else{
					document.getElementById ('WMTS-info').style.display = "none";
					document.getElementById ('jform_asOL').checked = false;
					document.getElementById ('jform_asOL').disabled = true;
					document.getElementById ('jform_asOLoptions').style.display = "none";
					document.getElementById ('jform_asOLoptions-lbl').style.display = "none";
					document.getElementById ('jform_asOLstyle').style.display = "none";
					document.getElementById ('jform_asOLstyle-lbl').style.display = "none";
					document.getElementById ('jform_asOLmatrixset').style.display = "none";
					document.getElementById ('jform_asOLmatrixset-lbl').style.display = "none";
					document.getElementById ('jform_asOLstyle').className = "inputbox";
					document.getElementById ('jform_asOLstyle-lbl').className = "hasTip";
					document.getElementById ('jform_asOLmatrixset').className = "inputbox";
					document.getElementById ('jform_asOLmatrixset-lbl').className = "hasTip";
				}
				
				if(document.getElementById ('jform_asOL').checked == true)
				{
					document.getElementById ('jform_asOLoptions').disabled = false;
					document.getElementById ('jform_asOLstyle').disabled = false;
					document.getElementById ('jform_asOLmatrixset').disabled = false;
				}
				else
				{
					document.getElementById ('jform_asOLoptions').disabled = true;
					document.getElementById ('jform_asOLstyle').disabled = true;
					document.getElementById ('jform_asOLmatrixset').disabled = true;
				}
			}
		}
	}
	
	window.addEvent('domready', init);