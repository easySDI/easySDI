
Ext.namespace("EasySDI_Map");_addBaseLayer=function(layer){if(this.mapDescriber.basemap==layer.layers){var options={isBaseLayer:true,singleTile:layer.singletile,buffer:0,opacity:layer.defaultOpacity}
if(layer.units!=undefined)
options.units=layer.units;if(layer.projection!=undefined)
options.projection=layer.projection;if(layer.maxExtent!=undefined)
options.maxExtent=layer.maxExtent;else if(thisMap.maxExtent!=undefined)
options.maxExtent=thisMap.maxExtent;else{}
if(layer.minScale!=undefined)
options.minScale=options.minScale;else if(thisMap.minScale!=undefined)
options.minScale=thisMap.minScale;else{}
if(layer.maxScale!=undefined)
options.maxScale=layer.maxScale;else if(thisMap.maxScale!=undefined)
options.maxScale=thisMap.maxScale;else{}
if(layer.resolutions!=undefined)
options.resolutions=layer.resolutions;else{options.minResolution="auto";options.maxResolution="auto";}
var WMSoptions={LAYERS:layer.layers,SERVICE:layer.url_type,VERSION:layer.version,STYLES:'',SRS:layer.projection,FORMAT:layer.imageFormat};if(layer.cache)
WMSoptions.CACHE=true;var l=new OpenLayers.Layer.WMS(layer.name,layer.url,WMSoptions,options);l.setVisibility(this.mapDescriber.basemapVisible);thisMap.addLayer(l);}},_addLayers=function(layer){Ext.each(this.mapDescriber.layers,function(layerToAdd){if(layerToAdd.name==layer.layers){var l='';switch(layer.url_type.toUpperCase()){case'WMS':var options={isBaseLayer:false,singleTile:layer.singletile,buffer:0,opacity:layer.defaultOpacity}
if(layer.units!=undefined)
options.units=layer.units;if(layer.projection!=undefined)
options.projection=layer.projection;if(layer.maxExtent!=undefined)
options.maxExtent=layer.maxExtent;else if(thisMap.maxExtent!=undefined)
options.maxExtent=thisMap.maxExtent;else{}
if(layer.minScale!=undefined)
options.minScale=options.minScale;else if(thisMap.minScale!=undefined)
options.minScale=thisMap.minScale;else{}
if(layer.maxScale!=undefined)
options.maxScale=layer.maxScale;else if(thisMap.maxScale!=undefined)
options.maxScale=thisMap.maxScale;else{}
if(layer.resolutions!=undefined)
options.resolutions=layer.resolutions;else{options.minResolution="auto";options.maxResolution="auto";}
var WMSoptions={LAYERS:layer.layers,SERVICE:layer.url_type,VERSION:layer.version,STYLES:'',SRS:layer.projection,FORMAT:layer.imageFormat,TRANSPARENT:true};l=new OpenLayers.Layer.WMS(layer.name,layer.url,WMSoptions,options);if(layer.cache)
l.params.CACHE=true;if(this.mapDescriber.style&&this.mapDescriber.style[layer.id]){sld=EasySDI_Map.StyledLayerDescriptor.getSLDHeader();sld+=EasySDI_Map.StyledLayerDescriptor.getSLD(layer.layers,this.mapDescriber.style[layer.id].fillColor,this.mapDescriber.style[layer.id].opacity,this.mapDescriber.style[layer.id].strokeColor);sld+=EasySDI_Map.StyledLayerDescriptor.getSLDFooter();l.mergeNewParams({SLD_BODY:sld});};thisMap.addLayer(l);break;case'WFS':l=new OpenLayers.Layer.WFS(layer.name,layer.url,{typename:layer.layers});thisMap.addLayer(l);if(this.mapDescriber.style[layer.id]){var style=new OpenLayers.Style({strokeColor:"#"+this.mapDescriber.style[layer.id].strokeColor,strokeWidth:1,fillColor:"#"+this.mapDescriber.style[layer.id].fillColor,fillOpacity:this.mapDescriber.style[layer.id].opacity});l.styleMap=new OpenLayers.StyleMap({"default":style});l.refresh();}
break;}}},this);},_addAnnotations=function(layer){Ext.each(this.mapDescriber.layers,function(layerToAdd){if(layerToAdd.name==layer.text){var template={pointRadius:layer.pointRadius,fillColor:layer.fillColor,fillOpacity:layer.fillOpacity,strokeColor:layer.strokeColor,strokeWidth:layer.strokeWidth,strokeOpacity:layer.strokeOpacity,stroke:layer.stroke,fill:layer.fill,externalGraphic:layer.externalGraphic,graphicWidth:layer.graphicWidth,graphicHeight:layer.graphicHeight,graphicYOffset:layer.graphicYOffset,graphicXOffset:layer.graphicXOffset,graphicOpacity:layer.graphicOpacity};var style={"default":new OpenLayers.Style(template)};var vectors=new OpenLayers.Layer.Vector(layer.text,{isBaseLayer:false,transparent:"true",styleMap:new OpenLayers.StyleMap(style)});if(layerToAdd.features){var featuresDescriber=layerToAdd.features;var featuresArray=[];var end=false;while(end==false){var index=featuresDescriber.indexOf(";",0);if(index==-1){end=true;featuresArray.push(this._createVectorFeature(featuresDescriber));}else{var featureDescriber=featuresDescriber.substr(0,index);featuresArray.push(this._createVectorFeature(featureDescriber));featuresDescriber=featuresDescriber.substr(index+1);}}
vectors.addFeatures(featuresArray);}
thisMap.addLayer(vectors);}},this);},_createVectorFeature=function(feature){var index=feature.indexOf("(",0);var featureType=feature.substr(0,index);feature=feature.substr(index+1);switch(featureType.toUpperCase()){case'POLYGON':var newLinearRingComponents=new Array();var start=feature.indexOf("(",0);var geometry=feature.substr(start+1,feature.length-2);var end=false;while(end==false){start=geometry.indexOf(",");if(start==-1){end=true;var middle=geometry.indexOf(" ");var x=geometry.substr(0,middle);var y=geometry.substr(middle+1,geometry.length-middle-1);newLinearRingComponents.push(new OpenLayers.Geometry.Point(x,y));}else{var middle=geometry.indexOf(" ");var x=geometry.substr(0,middle);var y=geometry.substr(middle+1,start-middle-1);geometry=geometry.substr(start+1);newLinearRingComponents.push(new OpenLayers.Geometry.Point(x,y));}}
var newLinearRing=new OpenLayers.Geometry.LinearRing(newLinearRingComponents);var polygon=new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([newLinearRing]));return polygon;break;case'POINT':var middle=feature.indexOf(" ");var x=feature.substr(0,middle);var y=feature.substr(middle+1,feature.length-middle-2);var point=new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(x,y));return point;break;case'LINESTRING':var points=new Array();var end=false;while(end==false){comma=feature.indexOf(",");if(comma==-1){end=true;var middle=feature.indexOf(" ");var x=feature.substr(0,middle);var y=feature.substr(middle+1,feature.length-middle-1);points.push(new OpenLayers.Geometry.Point(x,y));}else{var middle=feature.indexOf(" ");var x=feature.substr(0,middle);var y=feature.substr(middle+1,comma-middle-1);feature=feature.substr(comma+1);points.push(new OpenLayers.Geometry.Point(x,y));}}
var lineString=new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(points));return lineString;break;}},_getSLD=function(layer,fillColor,opacity,strokeColor){return String.format('<StyledLayerDescriptor version="1.0.0" xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" '
+'  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
+'  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">'
+'  <NamedLayer>'+'    <Name>{0}</Name>'+'    <UserStyle>'+'      <FeatureTypeStyle>'+'        <Rule>'
+'          <Title>Polygon</Title>'+'          <PolygonSymbolizer>'+'            <Fill>'
+'              <CssParameter name="fill">#{1}</CssParameter>'
+'              <CssParameter name="fill-opacity">{2}</CssParameter>'+'            </Fill>'+'            <Stroke>'
+'              <CssParameter name="stroke">#{3}</CssParameter>'+'            </Stroke>'
+'          </PolygonSymbolizer>'+'        </Rule>'+'      </FeatureTypeStyle>'+'    </UserStyle>'
+'  </NamedLayer>'+'</StyledLayerDescriptor>',layer,fillColor,opacity,strokeColor);}
printMap=function(config){this.mapDescriber=JSON.parse(completeMap);var options={controls:[],layers:[]};if(componentParams.projection!='')
options.projection=componentParams.projection;if(componentParams.mapUnit!='')
options.units=componentParams.mapUnit;if(componentParams.mapMaxExtent!='')
options.maxExtent=componentParams.mapMaxExtent;if(componentParams.mapMinScale!='')
options.minScale=componentParams.mapMinScale;if(componentParams.mapMaxScale!='')
options.maxScale=componentParams.mapMaxScale;if(componentParams.mapResolutions!='')
options.resolutions=componentParams.mapResolutions;if(componentParams.numZoomLevels!='')
options.numZoomLevels=componentParams.numZoomLevels;thisMap=new OpenLayers.Map('map',options);Ext.each(SData.baseLayers,this._addBaseLayer,this);Ext.each(SData.overlayLayers,this._addLayers,this);Ext.each(annotationStyle,this._addAnnotations,this);thisMap.addControl(new OpenLayers.Control.ScaleLine());extent=new OpenLayers.Bounds(this.mapDescriber.extent.left,this.mapDescriber.extent.bottom,this.mapDescriber.extent.right,this.mapDescriber.extent.top);thisMap.zoomToExtent(extent,true);}