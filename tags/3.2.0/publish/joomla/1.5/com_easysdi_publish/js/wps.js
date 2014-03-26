function WPSTransformDatasetRequest(diffusionServerName, FeatureSourceId, URLFile, scriptName, sourceDataType, coordEpsgCode, dataset)
{
	//xml headers
	var req = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	
	//WPS execute
  req += '<wps:Execute service="WPS" version="1.0.0"';
	req += ' xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1"';
	req += ' xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
	req += ' xsi:schemaLocation="http://www.opengis.net/wps/1.0.0';
  req += ' http://schemas.opengis.net/wps/1.0.0/wpsExecute_request.xsd">';
	
	//the request type
	req += '<ows:Identifier>TransformDataset</ows:Identifier>';
		req += '<wps:DataInputs>';
		
		//loop data iputs
	
			//server name
			req += '<wps:Input>';
			req += '<ows:Identifier>DiffusionServerName</ows:Identifier>';
			req += '<ows:Title>diffusion Server Name</ows:Title>';
			req += '<wps:Data>';
				req += '<wps:LiteralData>'+diffusionServerName+'</wps:LiteralData>';
			req += '</wps:Data>';
			req += '</wps:Input>'; 
			
			//FeatureSourceId
			req += '<wps:Input>';
			req += '<ows:Identifier>FeatureSourceId</ows:Identifier>';
			req += '<ows:Title>Feature Source Id (only for updates; for new set to none)</ows:Title>';
			req += '<wps:Data>';
				req += '<wps:LiteralData>'+FeatureSourceId+'</wps:LiteralData>';
			req += '</wps:Data>';
			req += '</wps:Input>'; 
			
			//URLFile
			files = URLFile.split(",");
			for (var i = 0; i < files.length; i++){
				req += '<wps:Input>';
				req += '<ows:Identifier>URLFile</ows:Identifier>';
				req += '<ows:Title>comma separated URLs</ows:Title>';
				req += '<wps:Data>';
					req += '<wps:LiteralData>'+files[i]+'</wps:LiteralData>';
				req += '</wps:Data>';
				req += '</wps:Input>';
			}
			
			//scriptName
			req += '<wps:Input>';
			req += '<ows:Identifier>ScriptName</ows:Identifier>';
			req += '<ows:Title>Name of the script to execute</ows:Title>';
			req += '<wps:Data>';
				req += '<wps:LiteralData>'+scriptName+'</wps:LiteralData>';
			req += '</wps:Data>';
			req += '</wps:Input>';
			
			//sourceDataType
			req += '<wps:Input>';
			req += '<ows:Identifier>SourceDataType</ows:Identifier>';
			req += '<ows:Title>Type of the data source (e.g SHAPE)</ows:Title>';
			req += '<wps:Data>';
				req += '<wps:LiteralData>'+sourceDataType+'</wps:LiteralData>';
			req += '</wps:Data>';
			req += '</wps:Input>';
			
			//coordEpsgCode
			req+='<wps:Input>';
			req+='<ows:Identifier>CoordEpsgCode</ows:Identifier>';
			req+='<ows:Title>The EPSG code of the projection</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+coordEpsgCode+'</wps:LiteralData>';
			req+='</wps:Data>';
			req+='</wps:Input>';
			
			//dataset
			req+='<wps:Input>';
			req+='<ows:Identifier>Dataset</ows:Identifier>';
			req+='<ows:Title>The dataset to transform contained in the source file</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+dataset+'</wps:LiteralData>';
			req+='</wps:Data>';
			req+='</wps:Input>';
			
		req += '</wps:DataInputs>';
		
		//async request
		req += '<wps:ResponseForm>';
		req += '<wps:ResponseDocument storeExecuteResponse="true"	status="true">';
			req += '<wps:Output>';
				req += '<ows:Identifier>FeatureSourceGuid</ows:Identifier>';
			req += '</wps:Output>';
		req += '</wps:ResponseDocument>';
	req += '</wps:ResponseForm>';
			
	req += '</wps:Execute>';
	
	return req;
}


function WPSPublishLayer(featureTypeId, layerId, aliases, theTitle, theName, quality, keywords, theAbstract, geometry)
{
	var req='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
  req+='<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1"	xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"	xsi:schemaLocation="http://www.opengis.net/wps/1.0.0http://schemas.opengis.net/wps/1.0.0/wpsExecute_request.xsd">';
	req+='<ows:Identifier>PublishLayer</ows:Identifier>';
	req+='<wps:DataInputs>';
		
		//FeatureTypeId
		req+='<wps:Input>';
			req+='<ows:Identifier>FeatureSourceId</ows:Identifier>';
			req+='<ows:Title>id of the feature Type</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+featureTypeId+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		//layerId
		req+='<wps:Input>';
			req+='<ows:Identifier>LayerId</ows:Identifier>';
			req+='<ows:Title>id of an existing, none creates a new one</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+layerId+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		//aliases
		aliases = aliases.split(",");
		for (var i = 0; i < aliases.length; i++){
			req+='<wps:Input>';
				req+='<ows:Identifier>AttributeAlias</ows:Identifier>';
				req+='<ows:Title>Alias for an attribute</ows:Title>';
				req+='<wps:Data>';
					req+='<wps:LiteralData>'+aliases[i]+'</wps:LiteralData>';
				req+='</wps:Data>';
			req+='</wps:Input>';
		}
		
		//Title
		req+='<wps:Input>';
			req+='<ows:Identifier>Title</ows:Identifier>';
			req+='<ows:Title>Alias for layer name</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+theTitle+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		//Title
		req+='<wps:Input>';
			req+='<ows:Identifier>Name</ows:Identifier>';
			req+='<ows:Title>Alias for layer name</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+theName+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		//Quality
		req+='<wps:Input>';
			req+='<ows:Identifier>Quality_Area</ows:Identifier>';
			req+='<ows:Title>Quality/Area</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+quality+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		
		req+='<wps:Input>';
			req+='<ows:Identifier>KeywordList</ows:Identifier>';
			req+='<ows:Title>Layer Keyword</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+keywords+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		req+='<wps:Input>';
			req+='<ows:Identifier>Abstract</ows:Identifier>';
			req+='<ows:Title>Layer description and abstract</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+theAbstract+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		//geometry
			req+='<wps:Input>';
			req+='<ows:Identifier>Geometry</ows:Identifier>';
			req+='<ows:Title>The geometry type</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+geometry+'</wps:LiteralData>';
			req+='</wps:Data>';
			req+='</wps:Input>';
		
	req+='</wps:DataInputs>';
req+='</wps:Execute>';


	return req;
}

function WPSCopyLayer(layerId, theName)
{
	var req='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	req+='<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1"	xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"	xsi:schemaLocation="http://www.opengis.net/wps/1.0.0http://schemas.opengis.net/wps/1.0.0/wpsExecute_request.xsd">';
	req+='<ows:Identifier>CopyLayer</ows:Identifier>';
	req+='<wps:DataInputs>';
		
		//layerId
		req+='<wps:Input>';
			req+='<ows:Identifier>LayerId</ows:Identifier>';
			req+='<ows:Title>id of an existing, none creates a new one</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+layerId+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
		//Title
		req+='<wps:Input>';
			req+='<ows:Identifier>Name</ows:Identifier>';
			req+='<ows:Title>Alias for layer name</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+theName+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
	req+='</wps:DataInputs>';
req+='</wps:Execute>';


	return req;
}

function WPSDeleteFeatureSource(fsId)
{
	
  var req='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	req+='<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"	xsi:schemaLocation="http://www.opengis.net/wps/1.0.0 http://schemas.opengis.net/wps/1.0.0/wpsExecute_request.xsd">';
	
	req+='<ows:Identifier>DeleteFeatureSource</ows:Identifier>';
	req+='<wps:DataInputs>';
		
		//FeatureSourceId
		req+='<wps:Input>';
			req+='<ows:Identifier>FeatureSourceId</ows:Identifier>';
			req+='<ows:Title>id of an existing feature source</ows:Title>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+fsId+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
	req+='</wps:DataInputs>';
req+='</wps:Execute>';

	return req;
}

function WPSDeleteLayer(layerId)
{
var req='<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wps/1.0.0../wpsExecute_request.xsd">';
	
  var req='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
	req += '<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"	xsi:schemaLocation="http://www.opengis.net/wps/1.0.0 http://schemas.opengis.net/wps/1.0.0/wpsExecute_request.xsd">';
	req+='<ows:Identifier>DeleteLayer</ows:Identifier>';
	req+='<wps:DataInputs>';
		
		//LayerId
		req+='<wps:Input>';
			req+='<ows:Identifier>LayerId</ows:Identifier>';
			req+='<wps:Data>';
				req+='<wps:LiteralData>'+layerId+'</wps:LiteralData>';
			req+='</wps:Data>';
		req+='</wps:Input>';
		
	req+='</wps:DataInputs>';
req+='</wps:Execute>';

	return req;
}