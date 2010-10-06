/**
 */
var up = null;
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
				$('validateFs').addEvent('click', function() {
					return validateFs_click();
				});
				$('transfFormatId').addEvent('change', function() {
					transfFormatId_change();
				});
				$('transfScriptId').addEvent('change', function() {
					transfScriptId_change();
				});
				$('featuresource_name').addEvent('change', function() {
					featuresource_name_change();
				});
				/*
				$('searchds').addEvent('click', function() {
					searchds_click();
				});
				*/
				//$('chkBxAdvanced').fireEvent('click');
						
});

function init(){
			//load config elements
			wpsServlet = $('wpsPublish').value;
			//serverAdress = $('servAdr').value;
			//if($('baseUrl').value != "/")
			//   baseUrl = serverAdress+"/"+$('baseUrl').value+'components/com_easysdi_publish/core/proxy.php?proxy_url=';
			//else
			//   baseUrl = serverAdress+"/"+'components/com_easysdi_publish/core/proxy.php?proxy_url=';
		  baseUrl = $('baseUrl').value+'components/com_easysdi_publish/core/proxy.php?proxy_url=';
			
		  
		  userId = $('userId').value;
			//hide the advanced tab
			//Function $('') == document.getElementById('').
			$('advancedTab').style.visibility = 'hidden';
			$('advancedTab').style.display = 'none';
			//Hide loading image
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
}

function home_click()
{
	$('task').value = 'gettingStarted';
	$('publish_form').submit();
	return false;
}
function featuresource_name_change()
{
	$('errorMsg').style.display = 'none';
	$('errorMsg').style.visibilty = 'hidden';
	$('errorMsg').set('html', '');
}
function transfFormatId_change()
{
	$('transfScriptId').selectedIndex = $('transfScriptId').length -1;
	$('errorMsg').style.display = 'none';
	$('errorMsg').style.visibilty = 'hidden';
	$('errorMsg').set('html', '');
}

function transfScriptId_change()
{
	$('transfFormatId').selectedIndex = $('transfFormatId').length-1;
	$('errorMsg').style.display = 'none';
	$('errorMsg').style.visibilty = 'hidden';
	$('errorMsg').set('html', '');
}

function validateForm(){
	//Reset background
	$('featuresource_name').style.background = "";
	$('transfFormatId').style.background = "";
	$('projection').style.background = "";
	
	//check FS name
	if($('featuresource_name').value == "" || $('featuresource_name').value == "New"){
		errorMsg = "You must enter a Feature Source name";
		displayErr(errorMsg, $('featuresource_name'));
		return false;
	}
	
	if(!isAlphanumeric($('featuresource_name'))){
		errorMsg = "The name can only contain letters and numbers";
		displayErr(errorMsg, $('featuresource_name'));
		return false;	
	}
	
	if(!checkNameUnique($('featuresource_name').value) && $('task').value=='createFeatureSource'){
		errorMsg = "There is already a Feature Source with the same name";
		displayErr(errorMsg, $('featuresource_name'));
		return false;	
	}
	
	//check length
	if(!lengthRestriction($('featuresource_name'), 0, 20)){
		errorMsg = "The Feature Source Name is too long, max 20 characters";
		displayErr(errorMsg, $('featuresource_name'));
		return false;	
	}
		
	if($('transfFormatId').options[$('transfFormatId').selectedIndex].value == 0 &&
			$('transfScriptId').options[$('transfScriptId').selectedIndex].value == 0){
		 errorMsg = "You must either choose a script or a file format.";
		displayErr(errorMsg, $('transfFormatId'));
		return false;	
	}
	
	if($('datasets').options[$('datasets').selectedIndex].value == ""){
		 errorMsg = "You must choose a supported dataset from the source file(s).";
		displayErr(errorMsg, $('dataset'));
		return false;	
	}
	
	if($('projection').options[$('projection').selectedIndex].value == 0){
		errorMsg = "You must choose a projection.";
		displayErr(errorMsg, $('projection'));
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
		$('errorMsg').set('html', err);
}

function validateFs_click()
{
	// Do some validation stuffs
	
	if(!validateForm())
		return false;
	
	// Build the WPS post body
	diffusionServerName = $('diffusionServerName').value;
	FeatureSourceId = $('featureSourceGuid').value == 0 ? 'none' : $('featureSourceGuid').value;
	URLFile = '';
	//alert($('fileList').value);
	fileList = $('fileList').value.split(",");
	for (var i = 0; i < fileList.length; i++){
		if(URLFile != '' && i < fileList.length)
			URLFile += ",";
		//encode the file name if it has accent or spaces
		temp = fileList[i].split("/");
		temp[temp.length - 1] = rawurlencode(temp[temp.length - 1]);
		if($('baseUrl').value == "/")
			URLFile +=  $('servAdr').value+temp.join("/");
		else
			URLFile +=  $('servAdr').value+$('baseUrl').value+temp.join("/");
	}
	value = $('transfScriptId').options[$('transfScriptId').selectedIndex].value;
	scriptName = value == 0 ? 'none' : $('transfScriptId').options[$('transfScriptId').selectedIndex].value;
	value = $('transfFormatId').options[$('transfFormatId').selectedIndex].value;
	sourceDataType = value == 0 ? 'none' : $('transfFormatId').options[$('transfFormatId').selectedIndex].value;
	
	//Epsg code
	coordEpsgCode = $('projection').value;
	
	//Dataset
	dataset = $('datasets').options[$('datasets').selectedIndex].value;
	
	var postBody = WPSTransformDatasetRequest(diffusionServerName, FeatureSourceId, URLFile, scriptName, sourceDataType, coordEpsgCode, dataset);
	//alert("url:"+baseUrl + wpsServlet+"   body:"+postBody);
	//WPSTransformDatasetRequest
	//send the request
	//alert("request:" + baseUrl + wpsServlet);
	var req = new Request({
		url: baseUrl + wpsServlet,
		//url: "http://localhost/components/com_easysdi_publish/core/proxy.php",
		method: 'post',
		evalResponse: false,
		onSuccess: function(responseText, responseXML){			
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			$('validateFs').disabled = false;
			$('validateFs').className = '';
			var ex = responseXML.getElementsByTagName('ows:Exception');
			//handle the exception if there is
			if(ex.length > 0){
				code = ex[0].attributes[0].nodeValue;
				//TODO look what we do if an exception occurs.
				//Most relevant are: transformator did not succeed with supplied files
				//Files where missing.
				//For now, simply output the text in front-end for debug suppose.
				exText = responseXML.getElementsByTagName('ows:ExceptionText');
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsg').set('html', exText[0].firstChild.nodeValue);
			}
			//No exception, get the FS id in response and submit
			else
			{
				cont = false;
				var featureSourceGuid;
				var attr;
				out = responseXML.getElementsByTagName('wps:RawDataOutput');
				if(out != null){
					//the datasetId
					lastChild = out[0].lastChild;
					if(lastChild != null){
						featureSourceGuid = lastChild.textContent;
						cont = true;
					}			
				}
				if(cont == true){
					
					$('featureSourceGuid').value = featureSourceGuid;
					$('fieldsName').value = attr;
					$('task').value = 'saveFeatureSource';
					//Make sure the server has created the fs	
					//setTimeout("submitForm();",3000);
					submitForm();
				}else{
					$('errorMsg').style.display = 'block';
					$('errorMsg').style.visibilty = 'visible';
					$('errorMsg').set('html', "Something went wrong with the parser");
				}
			}
		},
		
		headers:{'content-type': 'text/xml' },
		onRequest: function() { 
			//Activate here please wait...
			$('loadingImg').style.visibility = 'visible';
			$('loadingImg').style.display = 'block';
			//disable create / update button
			$('validateFs').disabled = true;
			$('validateFs').className = 'buttonDisabled';
		}, 
		// Our request will most likely succeed, but just in case, we'll add an
		// onFailure method which will let the user know what happened.
		onFailure: function(xhr){
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsg').set('html', "System error: returned status code " + xhr.status + " " + xhr.statusText + " please try again or contact the service provider for help.");
		}
	}).send(postBody);
	
	return false;
}

function searchds_click(){
	URLFile = '';
	//alert($('fileList').value);
	fileList = $('fileList').value.split(",");
	var fileI = 0;
	for (fileI = 0; fileI < fileList.length; fileI++){
		if(URLFile != '' && fileI < fileList.length)
			URLFile += ",";
		//encode the file name if it has accent or spaces
		temp = fileList[fileI].split("/");
		temp[temp.length - 1] = rawurlencode(temp[temp.length - 1]);
		if($('baseUrl').value == "/")
			URLFile +=  $('servAdr').value+temp.join("/");
		else
			URLFile +=  $('servAdr').value+$('baseUrl').value+temp.join("/");
	}
	if(fileI == 0)
		alert("Please select and unpload file(s) first.")
	
	var req = new Request({
		url: baseUrl + wpsServlet + "/config",
		method: 'post',
		evalResponse: true,
		data:{'files':URLFile,
					'operation':'GetAvailableDatasetFromSource'},
		onSuccess: function(responseText, responseXML){			
			$('loadingImg').style.visibility = 'hidden';
			$('loadingImg').style.display = 'none';
			$('validateFs').disabled = false;
			$('validateFs').className = '';
			var ex = responseXML.getElementsByTagName('exception');
			//handle the exception if there is
			if(ex.length > 0){
				code = ex[0].attributes[0].nodeValue;
				//TODO look what we do if an exception occurs.
				//Most relevant are: transformator did not succeed with supplied files
				//Files where missing.
				//For now, simply output the text in front-end for debug suppose.
				exText = ex[0].firstChild.nodeValue;
				$('errorMsg').style.display = 'block';
				$('errorMsg').style.visibilty = 'visible';
				$('errorMsg').set('html', "Une erreur est survenue: code:"+code+" message:"+exText);
			}
			//No exception, get the FS id in response and submit
			else
			{
				cont = false;
				var out = responseXML.getElementsByTagName('dataset');
				if(out != null){
					//the dataset name
					var searchdsObj = $('datasets');
					for(i=0; i<out.length; i++){
						var dsname = out[i].firstChild.nodeValue;
						searchdsObj.options[searchdsObj.options.length] = new Option(dsname,dsname);
						//set the only dataset as selected
						if(out.length == 1)
							 searchdsObj.options[1].selected = true;
					}
				}
			}
		},
		
		headers:{'content-type': 'text/xml' },
		onRequest: function() { 
			//Activate here please wait...
			$('loadingImg').style.visibility = 'visible';
			$('loadingImg').style.display = 'block';
			//disable create / update button
			$('validateFs').disabled = true;
			$('validateFs').className = 'buttonDisabled';
		}, 
		// Our request will most likely succeed, but just in case, we'll add an
		// onFailure method which will let the user know what happened.
		onFailure: function(xhr){
			$('errorMsg').style.display = 'block';
			$('errorMsg').style.visibilty = 'visible';
			$('errorMsg').set('html', "System error: returned status code " + xhr.status + " " + xhr.statusText + " please try again or contact the service provider for help.");
		}
	}).send();
	
	return false;
	
	
}

function submitForm(){
			$('publish_form').submit();
}

function chkBxAdvanced_click()
{
	//toggle the visibility of the advanced tab and file format
	if($('chkBxAdvanced').checked)
	{
		$('advancedTab').style.visibility = 'visible';
		$('advancedTab').style.display = 'block';
		
	}
	else
	{
		$('advancedTab').style.visibility = 'hidden';
		$('advancedTab').style.display = 'none';
	}
}

/*
function loadXMLDoc1(dname) 
{
var xmlDoc;
if (window.XMLHttpRequest)
  {
  xmlDoc=new window.XMLHttpRequest();
  xmlDoc.open("GET",'http://localhost/Joomla/components/com_easysdi_publish/xml/'+dname,false);
  xmlDoc.send("");
  return xmlDoc.responseXML;
  }
// IE 5 and IE 6
else if (ActiveXObject("Microsoft.XMLDOM"))
  {
  xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
  xmlDoc.async=false;
  xmlDoc.load('http://localhost/Joomla/components/com_easysdi_publish/xml/'+dname);
  return xmlDoc;
  }
alert("Error loading document");
return null;
}


function loadXMLDoc(dname) 
{
	var xmlDoc;
	var req = new Request({

		url: 'http://localhost/Joomla/components/com_easysdi_publish/xml/'.dname,
		
		evalResponse: true,
				
		data : {'plop':'plop'},
		
		onComplete: function(responseXML) { 
			alert('Request completed successfully.'+responseXML); 
		},
		
		onSuccess: function(responseXML){
			xmlDoc = responseXML;
		},

		// Our request will most likely succeed, but just in case, we'll add an
		// onFailure method which will let the user know what happened.
		onFailure: function(){
			$('result').set('text', 'The request failed.');
		}

	}).send();
	return xmlDoc;

}
*/
/*
function loadXMLDoc(dname) 
{
// Send the request
		doc = null;
		
	  req = new Request({

		url: 'http://localhost/Joomla/components/com_easysdi_publish/xml/'+dname,

		method: 'get',

		evalResponse: true,
		
		onSuccess: function(responseXML){
				doc = responseXML;
		},

		async: false,
		
		onFailure: function(){
			alert('xml docs unreachable');
		}

	});
	req.send();
	
	return doc;
}
*/


function rawurlencode( str ) {
    // URL-encodes string  
    // 
    // version: 905.3122
    // discuss at: http://phpjs.org/functions/rawurlencode
    // +   original by: Brett Zamir (http://brett-zamir.me)
    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Michael Grier
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: rawurlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin%20van%20Zonneveld%21'
    // *     example 2: rawurlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: rawurlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
 
    var histogram = {}, unicodeStr='', hexEscStr='';
    var ret = str.toString();

    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };

    // The histogram is identical to the one in urldecode.
    histogram["'"]   = '%27';
    histogram['(']   = '%28';
    histogram[')']   = '%29';
    histogram['*']   = '%2A'; 
    histogram['~']   = '%7E';
    histogram['!']   = '%21';
    histogram['\u20AC'] = '%80';
    histogram['\u0081'] = '%81';
    histogram['\u201A'] = '%82';
    histogram['\u0192'] = '%83';
    histogram['\u201E'] = '%84';
    histogram['\u2026'] = '%85';
    histogram['\u2020'] = '%86';
    histogram['\u2021'] = '%87';
    histogram['\u02C6'] = '%88';
    histogram['\u2030'] = '%89';
    histogram['\u0160'] = '%8A';
    histogram['\u2039'] = '%8B';
    histogram['\u0152'] = '%8C';
    histogram['\u008D'] = '%8D';
    histogram['\u017D'] = '%8E';
    histogram['\u008F'] = '%8F';
    histogram['\u0090'] = '%90';
    histogram['\u2018'] = '%91';
    histogram['\u2019'] = '%92';
    histogram['\u201C'] = '%93';
    histogram['\u201D'] = '%94';
    histogram['\u2022'] = '%95';
    histogram['\u2013'] = '%96';
    histogram['\u2014'] = '%97';
    histogram['\u02DC'] = '%98';
    histogram['\u2122'] = '%99';
    histogram['\u0161'] = '%9A';
    histogram['\u203A'] = '%9B';
    histogram['\u0153'] = '%9C';
    histogram['\u009D'] = '%9D';
    histogram['\u017E'] = '%9E';
    histogram['\u0178'] = '%9F';


    // Begin with encodeURIComponent, which most resembles PHP's encoding functions
    ret = encodeURIComponent(ret);

    for (unicodeStr in histogram) {
        hexEscStr = histogram[unicodeStr];
        ret = replacer(unicodeStr, hexEscStr, ret); // Custom replace. No regexing
    }

    // Uppercase for full PHP compatibility
    return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
        return "%"+m2.toUpperCase();
    });
}
