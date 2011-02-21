
var wpsServlet;
var baseUrl;
			
//Call init when the dom is ready, inits the page.
window.addEvent('domready', function() {
			
			wpsServlet = $('wpsPublish').value;
			baseUrl = $('baseUrl').value+'components/com_easysdi_publish/core/proxy.php?proxy_url=';
				 
	
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

var req = new Request({
		url: baseUrl + wpsServlet,
		method: 'post',
		evalResponse: true,
		onSuccess: function(responseText, responseXML){
			//alert(responseText);
			var ex = responseXML.getElementsByTagName('ows:Exception');
			//handle the exception if there is
			if(ex.length > 0){
				code = ex[0].attributes[0].nodeValue;
				//TODO look what we do if an exception occurs.
				//Most relevant are: transformator did not succeed with supplied files
				//Files where missing.
				//For now, simply output the text in front-end for debug suppose.
				exText = responseXML.getElementsByTagName('ows:ExceptionText');
				//alert(exText[0].firstChild.nodeValue);
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsg').set('html', exText[0].firstChild.nodeValue);
			}
			//No exception, get the Layer id and attributes in response and submit
			else
			{
				cont = false;
				
				out = responseXML.getElementsByTagName('wps:RawDataOutput');
				//read the rawdataoutput
				if(out != null){
					//get the layerId
					lastChild = out[0].lastChild;
					if(lastChild != null){
						respFeatureId = lastChild.textContent;
						//check if this was the one requested
						if(respFeatureId == guid){
							cont = true;
						}
					}
				}
				
				if(cont == true){
					//feed the hidden types with the response value					
					$('task').value = "deleteFeatureSource";
					$('featureSourceGuid').value = guid;
					$('adminForm').submit();
				}
			}
		},
		headers:{'content-type': 'text/xml' },
		onRequest: function() { 
			//Activate here please wait...
		},
		// Our request will most likely succeed, but just in case, we'll add an
		// onFailure method which will let the user know what happened.
		onFailure: function(){
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsg').set('html', "System error: returned status code " + xhr.status + " " + xhr.statusText + " please try again or contact the service provider for help.");
		}
	});
	req.send(postBody);
	
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

//send and handle the request
var req = new Request({
	url: baseUrl + wpsServlet,
	method: 'post',
	evalResponse: true,
	onSuccess: function(responseText, responseXML){
			//alert(responseText);
			var ex = responseXML.getElementsByTagName('ows:Exception');
			//handle exceptions if they are
			if(ex.length > 0){
				code = ex[0].attributes[0].nodeValue;
				exText = responseXML.getElementsByTagName('ows:ExceptionText');
				//alert(exText);
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsg').set('html', exText[0].firstChild.nodeValue);
			}
			//No exception
			else
			{
				cont = false;
				
				out = responseXML.getElementsByTagName('wps:RawDataOutput');
				//read the rawdataoutput
				if(out != null){
					lastChild = out[0].lastChild;
					if(lastChild != null){
						respLayerId = lastChild.textContent;
						//check if this was the one requested
						if(respLayerId == guid){
							cont = true;
						}
					}
				}
				
				if(cont == true){
					//Submit the form for saving the values
					//Setting the hidden types
					$('task').value = "deleteLayer";
					$('layerGuid').value = guid;
					$('adminForm').submit();
				}
			}
		},
		headers:{'content-type': 'text/xml' }, 
		// Our request will most likely succeed, but just in case, we'll add an
		// onFailure method which will let the user know what happened.
		onFailure: function(e){
			alert('Request has not been sent, please check your network connection.');
		}
	});
	req.send(postBody);	

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

