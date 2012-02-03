/**
 */
var wpsServlet = "";
var baseUrl = "";
var userId = "";
var errorMsg = "";

window.addEvent('domready', function() {
				/*
				* Register event handlers
				*/
				
				//init the form
				init();
				
				$('home').addEvent('click', function() {
					return home_click();
				});
				
				$('featureSourceId').addEvent('change', function() {
					featureSourceId_change();
				});
				
				$('validateLayer').addEvent('click', function() {
					return validateLayer_click();
				});
				
				$('layer_name').addEvent('change', function() {
					layer_name_change();
				});
				/*
				$('validateFs').addEvent('click', function() {
					validateFs_click()
				});
				*/
});

function init(){
			//load config elements
			wpsServlet = 'servletPublish';
			baseUrl = $('baseUrl').value+'components/com_easysdi_publish/core/proxy.php?proxy_url=';
			userId = $('userId').value;
			//Disable fields name and desciption if no Feature Source Selected
			if($('featureSourceId').options[$('featureSourceId').selectedIndex].value == 0){
				//$('fieldsNameTab').style.visibility = 'hidden';
				//$('fieldsNameTab').style.display = 'none';	
				$('descriptionTab').style.visibility = 'hidden';
				$('descriptionTab').style.display = 'none';
				$('validateLayer').style.visibility = 'hidden';
				$('validateLayer').style.display = 'none';
				//Disable create/update button
				//$('validateLayer').disabled = true;
				//$('validateLayer').className = 'buttonDisabled';

			}
			
			//Hide loading image
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			
			//Display Layername as default for title
			if($('layerTitle').value == '')
				$('layerTitle').value = $('layer_name').value;
}

function home_click()
{
	$('task').value = 'gettingStarted';
	$('publish_form').submit();
	return false;
}

function layer_name_change(){
	if($('layerTitle').value == '' || $('layerTitle').value == EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_NEW_LAYER'))
		$('layerTitle').value = $('layer_name').value;
}

function validateForm(){
	//var fieldsName = $('fieldsName').value.split(",");
	//Reset background
	$('layer_name').style.background = "";
	$('geometry').style.background = "";
	//for (var i = 0; i < fieldsName.length; i++){
	//	$('attributeAlias'+i).style.background = "";
	//}
	$('layerTitle').style.background = "";
	$('layerDescription').style.background = "";
	$('layerQuality').style.background = "";
	$('layerKeyword').style.background = "";
	
	
	//check layer name
	if($('layer_name').value == "" || $('layer_name').value == EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_NEW_LAYER')){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_RES_NAME');
		displayErr(errorMsg, $('layer_name'));
		return false;
	}
	
	if(!isAlphanumeric($('layer_name'))){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_NAME_L_N');
		displayErr(errorMsg, $('layer_name'));
		return false;	
	}
	
	//check layer name unique
	
	if(!checkNameUnique($('layer_name').value) && $('task').value=='createLayer'){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_SAME_RES_NAME');
		displayErr(errorMsg, $('layer_name'));
		return false;	
	}
	
	
	//check the layer length
	if(!lengthRestriction($('layer_name'), 0, 20)){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_RES_NAME_TOO_LONG')+" 20";
		displayErr(errorMsg, $('layer_name'));
		return false;	
	}
	
	//check the aliases, if they contain comma and reserved keywords
	/*
	for (var i = 0; i < fieldsName.length; i++){
		var alias = $('attributeAlias'+i).value;
		if(alias == "")
			continue;
		//check content
		if(!isAlphanumeric($('attributeAlias'+i))){
			errorMsg = "The alias can only contain letters and numbers";
			displayErr(errorMsg, $('attributeAlias'+i));
			return false;
		}
		
		//check length
		if(!lengthRestriction($('attributeAlias'+i), 0, 20)){
			errorMsg = "The alias is too long, max 20 characters";
			displayErr(errorMsg, $('attributeAlias'+i));
			return false;	
		}
		
		//check reserved keywords
		
		var kwrd;
		if(kwrd = alias.toLowerCase().match("geom")){
			if(alias == "geom" || alias == "the_geom"){
				errorMsg = "Incorrect value for alias, reserved keyword found: '"+kwrd+"'";
				displayErr(errorMsg, $('attributeAlias'+i));
				return false;
			}
		}
		
		//if(kwrd = alias.toLowerCase().match("id")){
		//	if(alias == "id"){
		//		errorMsg = "Incorrect value for alias, reserved keyword found: '"+kwrd+"'";
		//		displayErr(errorMsg, $('attributeAlias'+i));
		//		return false;
		//	}
		//}
		
	}
	*/
	//check layer title
	if($('layerTitle').value == "" || $('layerTitle').value == "New"){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_RES_TITLE');
		displayErr(errorMsg, $('layerTitle'));
		return false;
	}
	
	if(!isAlphanumeric($('layerTitle'))){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_NAME_L_N');
		displayErr(errorMsg, $('layerTitle'));
		return false;	
	}
	
	//check length
	if(!lengthRestriction($('layerTitle'), 0, 20)){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_FIELD_TOO_LONG')+"20";
		displayErr(errorMsg, $('layerTitle'));
		return false;	
	}
	
	//check the layer description
	if(!lengthRestriction($('layerDescription'), 0, 400)){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_FIELD_TOO_LONG')+"400";
		displayErr(errorMsg, $('layerDescription'));
		return false;	
	}
	
	//check the layer quality
	if(!lengthRestriction($('layerQuality'), 0, 100)){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_FIELD_TOO_LONG')+"100";
		displayErr(errorMsg, $('layerQuality'));
		return false;	
	}
	
	//check the keyword list
	if(!lengthRestriction($('layerKeyword'), 0, 100)){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_FIELD_TOO_LONG')+"100";
		displayErr(errorMsg, $('layerKeyword'));
		return false;	
	}
	
	if($('geometry').options[$('geometry').selectedIndex].value == EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_SELECT_GEOMETRY')){
		errorMsg = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_GEOMETRY');
		displayErr(errorMsg, $('geometry'));
		return false;
	}
	
	return true;
}

function displayErr(err, elem){
		elem.focus();
		//border-bottom:1px solid #FBC2C4;
		elem.style.background = "#FBE3E4";
		$('errorMsg').style.display = 'block';
		$('errorMsg').style.visibilty = 'visible';
		$('errorMsg').innerHTML = err;
}

function validateLayer_click()
{
	// Do some validation stuffs
	
	if(!validateForm())
		return false;
	
	// Build the WPS post body
	//featureTypeId: mandatory
	featureTypeId = $('featureSourceGuid').value;
	
	//layerId: none if new, else update
	layerId = $('layerGuid').value;
	
	//The title
	theTitle = $('layerTitle').value;
	
	//The title
	theName = $('layer_name').value;
	
	//quality
	quality = $('layerQuality').value;

	//keywords
	keywords = $('layerKeyword').value;
	
	//abstract
	theAbstract = $('layerDescription').value;
	
	//Geometry
	geometry = $('geometry').value;
	
	var aliases = "";
	
	var postBody = WPSPublishLayer(featureTypeId, layerId, aliases, theTitle, theName, quality, keywords, theAbstract, geometry);
	//alert("url:"+baseUrl + wpsServlet+"   body:"+postBody);
	
	$('loadingImg').style.visibility = 'visible';
	$('loadingImg').style.display = 'block';
	$('validateLayer').disabled = true;
        $('validateLayer').className = 'buttonDisabled';
		  
	Ext.Ajax.request({
		//loadMask: true,
		url: "index.php?option=com_easysdi_publish&task=proxy&proxy_url=" + wpsServlet,
		method: 'post',
		headers: {'Content-Type': 'text/xml'},
		xmlData:postBody,
		success: function(response){
			//alert(responseText);
			$('validateLayer').disabled = false;
			$('validateLayer').className = '';
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
					$('publish_form').submit();
				}
			}
		},
		failure: function(response){
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsg').innerHTML = EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_SYS_1') +" "+ xhr.status + " " + xhr.statusText + " " + EasySDI_Pub.lang.getLocal('EASYSDI_PUBLISH_ERROR_SYS_2');
		}

	});

}

function featureSourceId_change(){
	
	//Reload the form because the attribute names have changed
	$('publish_form').submit();
}
