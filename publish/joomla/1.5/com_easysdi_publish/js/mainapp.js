
var wpsServlet;
var baseUrl;
			
//Call init when the dom is ready, inits the page.
window.addEvent('domready', function() {
		if($('loadingImg') != null){
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
		}
		wpsServlet = $('wpsPublish').value;
		baseUrl = 'index.php?option=com_easysdi_publish&task=proxy&proxy_url=';
	
	  //Add onclick event for the three panes. That call the correct function on the controller.
		if($('findDataPane') != null){
			$('findDataPane').addEvent( 'click' , function() {
					//pgl = $('use_pagination_layer1').value;
				//	pgf = $('use_pagination_feature1').value;
					window.open('./index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=0', '_self');
			//	window.open('./index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=0', '_self');
			});
		}
		if($('publishLayersPane') != null){
			$('publishLayersPane').addEvent( 'click' , function() { 
				
			//	pgl = $('use_pagination_layer1').value;
			//	pgf = $('use_pagination_feature1').value;
				window.open('./index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1', '_self');
			//	window.open('./index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1', '_self');
			});
		}
		if($('viewLayersPane') != null){
			$('viewLayersPane').addEvent( 'click' , function() { 
				
			//	pgl = $('use_pagination_layer1').value;
			//	pgf = $('use_pagination_feature1').value;
				window.open('./index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=2', '_self');
			//	window.open('./index.php?option=com_easysdi_publish&task=gettingStarted&tabIndex=1', '_self');
			});
		}

});


//"can" not be call if a Layer exist. Anyway, further controls are done...
function deleteFs_click(guid){

//prompt the user
conf = confirm('delete the feature source?');
if(!conf)
	return false;


//Build the query
var postBody = WPSDeleteFeatureSource(guid);
//alert("url:"+baseUrl + wpsServlet+"   body:"+postBody);

$('loadingImg').style.visibility = 'visible';
$('loadingImg').style.display = 'block';

Ext.Ajax.request({
		url: baseUrl + wpsServlet,
		method: 'post',
		headers: {'Content-Type': 'text/xml'},
		xmlData:postBody,
		success: function(response){
			var ex = response.responseXML.getElementsByTagName('ows:Exception');
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			//handle the exception if there is
			if(ex.length > 0 && response.responseXML.getElementsByTagName('ows:ExceptionText')[0].firstChild.nodeValue.indexOf('404') == -1){
				code = ex[0].attributes[0].nodeValue;
				//TODO look what we do if an exception occurs.
				//Most relevant are: transformator did not succeed with supplied files
				//Files where missing.
				//For now, simply output the text in front-end for debug suppose.
				exText = response.responseXML.getElementsByTagName('ows:ExceptionText');
				//alert(exText[0].firstChild.nodeValue);
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsgCode').innerHTML = code;
				$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;
			}
			//No exception, get the Fs id  and submit
			else
			{
				out = response.responseXML.getElementsByTagName('wps:LiteralData');
				respFeatureId = out[0].textContent;
				//feed the hidden types with the response value					
				$('task').value = "deleteFeatureSource";
				$('featureSourceGuid').value = guid;
				$('adminForm').submit();
			}
		},
		failure: function(response){
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsgCode').innerHTML = "System error";
			//$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;		
		}
});
	
	return false;
}

function copyLayer_click(msg, guid, idToCopy){
	var newName = prompt(msg, "");
	if(newName == '' || newName == null){
		return false;
	}
	var postBody = WPSCopyLayer(guid, newName);
	//alert("url:"+baseUrl + wpsServlet+"   body:"+postBody);
	
	
	$('loadingImg').style.visibility = 'visible';
	$('loadingImg').style.display = 'block';
	
	Ext.Ajax.request({
		//loadMask: true,
		url: "index.php?option=com_easysdi_publish&task=proxy&proxy_url=" + wpsServlet,
		method: 'post',
		headers: {'Content-Type': 'text/xml'},
		xmlData:postBody,
		success: function(response){
			//alert(responseText);
			//$('validateLayer').disabled = false;
			//$('validateLayer').className = '';
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			var ex = response.responseXML.getElementsByTagName('ows:Exception');
			//handle the exception if there is
			if(ex.length > 0){
				code = ex[0].attributes[0].nodeValue;
				//TODO look what we do if an exception occurs.
				//Most relevant are: transformator did not succeed with supplied files
				//Files where missing.
				//For now, simply output the text in front-end for debug suppose.
				exText = response.responseXML.getElementsByTagName('ows:ExceptionText');
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsgCode').innerHTML = code;
				$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;
			}
			//No exception, get the Layer id and attributes in response and submit
			else
			{
				cont = false;
				respLayerId = "";
				respEndpoints = Array();
				respBbox = Array();
				
				var out = response.responseXML.getElementsByTagName('wps:Output');
				//read the rawdataoutput
				if(out != null){
					//get the layerId
					lastChild = out[0].getElementsByTagName('wps:LiteralData')[0];
					if(lastChild != null){
						respLayerId = lastChild.textContent;
						cont = true;
					}
					//get the wms url
					lastChild = out[1].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respEndpoints[0] = lastChild.textContent;
						cont = true;
					}
					//get the wfs url
					lastChild = out[2].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respEndpoints[1] = lastChild.textContent;
						cont = true;
					}
					//get the kml url
					lastChild = out[3].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respEndpoints[2] = lastChild.textContent;
						cont = true;
					}
					//get minx
					lastChild = out[4].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respBbox[0] = lastChild.textContent;
						cont = true;
					}
					//get miny
					lastChild = out[5].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respBbox[1] = lastChild.textContent;
						cont = true;
					}
					//get maxX
					lastChild = out[6].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respBbox[2] = lastChild.textContent;
						cont = true;
					}
					//get maxY
					lastChild = out[7].getElementsByTagName('wps:LiteralData')[0];;
					if(lastChild != null){
						respBbox[3] = lastChild.textContent;
						cont = true;
					}
					
				}
				
				if(cont == true){
					//feed the hidden types with the response value					
					$('layerGuid').value = respLayerId;
					$('wmsUrl').value = respEndpoints[0];
					$('wfsUrl').value = respEndpoints[1];
					$('kmlUrl').value = respEndpoints[2];
					$('minx').value = respBbox[0];
					$('miny').value = respBbox[1];
					$('maxx').value = respBbox[2];
					$('maxy').value = respBbox[3];
					
					//Submit the form for saving the values
					$('task').value = 'saveLayer';
					$('copyLayer').value = 1;
					$('layerIdToCopy').value = idToCopy;
					$('layerCopyName').value = newName;
					
					document.getElementById('adminForm').submit();
				}else{
					$('errorMsg').style.display = 'block';
					$('errorMsg').style.visibilty = 'visible';
					$('errorMsgCode').innerHTML = "Something went wrong with the server response.";
					//$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;
				}
			}
		},
		failure: function(response){
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsgCode').innerHTML = "System error";
			//$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;
		}

	});
	
	return false;
}


function deleteLayer_click(guid){

//prompt the user
conf = confirm('delete the layer?');
if(!conf)
	return false;

//Build the query
var postBody = WPSDeleteLayer(guid);
//alert("url:"+baseUrl + wpsServlet+"   body:"+postBody);

$('loadingImg').style.visibility = 'visible';
$('loadingImg').style.display = 'block';

//send and handle the request
Ext.Ajax.request({
		url: baseUrl + wpsServlet,
		method: 'post',
		headers: {'Content-Type': 'text/xml'},
		xmlData:postBody,
		success: function(response){
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			var ex = response.responseXML.getElementsByTagName('ows:Exception');
			//handle exceptions if they are
			if(ex.length > 0 && response.responseXML.getElementsByTagName('ows:ExceptionText')[0].firstChild.nodeValue.indexOf('404') == -1){
				code = ex[0].attributes[0].nodeValue;
				exText = response.responseXML.getElementsByTagName('ows:ExceptionText');
				//alert(exText);
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsgCode').innerHTML = code;
				$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;
			}
			//No exception, delete the Layer
			else
			{
					//Submit the form for saving the values
					//Setting the hidden types
					$('task').value = "deleteLayer";
					$('layerGuid').value = guid;
					$('adminForm').submit();
			}
		},
		failure: function(response){
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsgCode').innerHTML = "System error";
			//$('errorMsgDescr').innerHTML = exText[0].firstChild.nodeValue;
		}
});

	return false;
}

//convenience method to get selected radio because handling radio in js is just ugly.
function getSelectedItem(radio) {
	choosen = "";
	len = radio.length
	for (i = 0; i <len; i++) {
		if (radio[i].checked) {
			choosen = radio[i].value
		}
	}
	return choosen
}

