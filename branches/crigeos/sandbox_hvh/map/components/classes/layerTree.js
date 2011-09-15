
Ext.namespace("EasySDI_Map");Ext.namespace("GeoExt.tree");Ext.namespace("SData");EasySDI_Map.BaseLayerNode=Ext.extend(GeoExt.tree.LayerNode,{isInScaleRange:null,constructor:function(config){EasySDI_Map.BaseLayerNode.superclass.constructor.apply(this,arguments);}});EasySDI_Map.StyledLayerNode=Ext.extend(GeoExt.tree.LayerNode,{fillColor:null,strokeColor:null,opacity:null,isInScaleRange:null,constructor:function(config){EasySDI_Map.StyledLayerNode.superclass.constructor.apply(this,arguments);},updateStyle:function(save){if(this.layer.CLASS_NAME=="OpenLayers.Layer.WMS"){this.layer.setOpacity(1);var sld=this._getSLD();this.layer.mergeNewParams({SLD_BODY:sld});}
if(this.layer.CLASS_NAME=="OpenLayers.Layer.WFS"){var style=new OpenLayers.Style({strokeColor:"#"+this.strokeColor,strokeWidth:1,fillColor:"#"+this.fillColor,fillOpacity:this.opacity});this.layer.setOpacity(1);this.layer.styleMap=new OpenLayers.StyleMap({"default":style});this.layer.refresh();}
if(save){this._save();}},defautStyle:function(){this.strokeColor=null;this.fillColor=null;this.opacity=null;Ext.each(SData.overlayLayers,function(layer){if(layer.name==this.layer.name){this.layer.setOpacity(layer.defaultOpacity);}},this);if(this.layer.CLASS_NAME=="OpenLayers.Layer.WMS"){var sld=EasySDI_Map.StyledLayerDescriptor.getSLDHeader();sld+=EasySDI_Map.StyledLayerDescriptor.getDefaultSLD();sld+=EasySDI_Map.StyledLayerDescriptor.getSLDFooter();this.layer.mergeNewParams({SLD_BODY:sld});}
if(this.layer.CLASS_NAME=="OpenLayers.Layer.WFS"){this.layer.styleMap=new OpenLayers.StyleMap();this.layer.refresh();}
this._delete();},_save:function(){var styles=Ext.state.Manager.get('overlayLayerStyle',null);if(styles===null){styles={};}
styles[this.id]={fillColor:this.fillColor,strokeColor:this.strokeColor,opacity:this.opacity};Ext.state.Manager.set('overlayLayerStyle',styles);this.getOwnerTree().tree.refreshLegend();},_delete:function(){var styles=[];styles=Ext.state.Manager.get('overlayLayerStyle',null);if(styles!=null){delete styles[this.id];}
Ext.state.Manager.set('overlayLayerStyle',styles);this.getOwnerTree().tree.refreshLegend();},_getSLD:function(){return String.format('<StyledLayerDescriptor version="1.0.0" xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" '
+'  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
+'  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">'
+'  <NamedLayer>'+'    <Name>{0}</Name>'+'    <UserStyle>'
+'      <FeatureTypeStyle>'+'        <Rule>'+'          <Title>Polygon</Title>'
+'          <PolygonSymbolizer>'+'            <Fill>'
+'              <CssParameter name="fill">#{1}</CssParameter>'
+'              <CssParameter name="fill-opacity">{2}</CssParameter>'
+'            </Fill>'+'            <Stroke>'
+'              <CssParameter name="stroke">#{3}</CssParameter>'+'            </Stroke>'
+'          </PolygonSymbolizer>'+'        </Rule>'+'      </FeatureTypeStyle>'
+'    </UserStyle>'+'  </NamedLayer>'+'</StyledLayerDescriptor>',this.layer.params.LAYERS,this.fillColor,this.opacity,this.strokeColor);}});EasySDI_Map.LayerTree=Ext.extend(Ext.data.Tree,{constructor:function(config){EasySDI_Map.LayerTree.superclass.constructor.apply(this,arguments);this._layerStore=new GeoExt.data.LayerStore({map:config.map.map});this._rootNode=new Ext.tree.TreeNode({id:'root'});this._baseLayers=new Ext.tree.TreeNode({id:'baseLayers',text:EasySDI_Map.lang.getLocal('LT_BASE_LAYERS'),singleClickExpand:true,iconCls:'folderLayer'});this._overlays=[];this._addOverlayGroups();this._rebuildTree();this.isFirstLoad=true;this._layerStore.map.events.register('moveend',this,this._onMapMoveEnd);this._layerStore.map.events.register("changelayer",this,this.onPreAddLayer);this._layerStore.map.events.register("loadend",this,this.onLayerAdded);_loadMask=new Ext.LoadMask(Ext.getBody(),{msg:EasySDI_Map.lang.getLocal('LOADER')});},_loadMask:null,onPreAddLayer:function(evt){if(evt.layer.visibility)
_loadMask.show();else
_loadMask.hide();},onLayerAdded:function(evt){_loadMask.hide();},_onMapMoveEnd:function(){Ext.each(SData.overlayGroups,function(group){var nodes=this._overlays['overlay_'+group.id].childNodes;Ext.each(nodes,function(node){this._updateNodeStyle(node);},this);},this);Ext.each(SData.baseLayers,function(layer){var nodes=this._baseLayers.childNodes;Ext.each(nodes,function(node){this._updateNodeStyle(node);},this);},this);this.refreshLegend();},_updateNodeStyle:function(node){if(!node.layer)
return;if(this._layerStore.map.getScale()<node.layer.maxScale||this._layerStore.map.getScale()>node.layer.minScale){node.isInScaleRange=false;node.getUI().addClass('hiddenLayer');node.getUI().removeClass('metadataAvailable');var el=Ext.get(node.getUI().iconNode);if(el)
el.addClass('hiddenLayerIcon');if(node.getUI().getTextEl()){if(node.getUI().getTextEl().setAttributeNS){Ext.each(SData.overlayLayers,function(layer){if(layer.name==node.layer.name){node.getUI().getTextEl().setAttributeNS("ext","qtip",EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP')+" "+layer.minScale+" - "+layer.maxScale);}},this);}else{Ext.each(SData.overlayLayers,function(layer){if(layer.name==node.layer.name){node.getUI().getTextEl().setAttribute("ext:qtip",EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP')+" "+layer.minScale+" - "+layer.maxScale);}},this);}}}else{node.isInScaleRange=true;node.getUI().removeClass('hiddenLayer');Ext.each(SData.overlayLayers,function(layer){if(layer.name==node.layer.name){if(layer.metadataUrl.length>0){node.getUI().addClass('metadataAvailable');}}},this);var el=Ext.get(node.getUI().iconNode);if(el)
el.removeClass('hiddenLayerIcon');if(node.getUI().getTextEl()){if(node.getUI().getTextEl().setAttributeNS){node.getUI().getTextEl().removeAttributeNS("ext","qtip");}else{node.getUI().getTextEl().removeAttribute("ext:qtip");}}}},loadLayers:function(){this._addLayers();this._loadState();},refreshLegend:function(){this.trigger('refreshLegend',true);},_rebuildTree:function(){Ext.each(SData.overlayGroups,function(group){this._rootNode.appendChild(this._overlays['overlay_'+group.id]);},this);this._rootNode.appendChild(this._baseLayers);this.setRootNode(this._rootNode);},_addOverlayGroups:function(){Ext.each(SData.overlayGroups,function(group){this._overlays['overlay_'+group.id]=new Ext.tree.TreeNode({text:EasySDI_Map.lang.getLocal(group.name),singleClickExpand:true,iconCls:'folderLayer',expanded:group.open});},this);},_addLayers:function(){this.reader=new GeoExt.data.LayerReader();var noLayerNode=new Ext.tree.TreeNode({text:EasySDI_Map.lang.getLocal('LT_NONE'),iconCls:'baseLayer',allowDrag:false,checked:false,uiProvider:Ext.tree.RadioNode});noLayerNode.addListener('click',function(){Ext.each(this._baseLayers.childNodes,function(layer){if(layer.layer!==undefined){layer.layer.setVisibility(false);}});this._saveState();},this);this._baseLayers.appendChild(noLayerNode);this.previousNode=null;Ext.each(SData.baseLayers,this._addBaseLayer,this);this.previousLayerNode=null;Ext.each(SData.overlayLayers,this._addOverlayLayer,this);this._layerStore.map.zoomToExtent(componentParams.mapMaxExtent,true);},_addBaseLayer:function(layer,i){var tree=this;var extraOptions={isBaseLayer:true,singleTile:layer.singletile,buffer:0,opacity:layer.defaultOpacity}
if(layer.customStyle!=undefined)
extraOptions.customStyle=layer.customStyle;if(layer.units!=undefined)
extraOptions.units=layer.units;else if(extraOptions.units!=undefined)
extraOptions.units=this._layerStore.map.units;else{}
if(layer.maxExtent!=undefined)
extraOptions.maxExtent=layer.maxExtent;else if(this._layerStore.map.maxExtent!=undefined)
extraOptions.maxExtent=this._layerStore.map.maxExtent;else{}
if(layer.minScale!=undefined)
extraOptions.minScale=layer.minScale;else if(this._layerStore.map.minScale!=undefined)
extraOptions.minScale=this._layerStore.map.minScale;else{}
if(layer.maxScale!=undefined)
extraOptions.maxScale=layer.maxScale;else if(this._layerStore.map.maxScale!=undefined)
extraOptions.maxScale=this._layerStore.map.maxScale;else{}
if(layer.resolutions!=undefined)
extraOptions.resolutions=layer.resolutions;else{extraOptions.minResolution="auto";extraOptions.maxResolution="auto";}
var WMSoptions={LAYERS:layer.layers,SERVICE:layer.url_type,VERSION:layer.version,STYLES:'',SRS:layer.projection,FORMAT:layer.imageFormat};if(layer.cache)
WMSoptions.CACHE=true;var l=new OpenLayers.Layer.WMS(layer.name,layer.url,WMSoptions,extraOptions);l.events.register('loadend',null,this.onLayerAdded);if(layer.cache)
l.params.CACHE=true;this._layerStore.map.addLayer(l);if(layer.defaultVisibility)
this._layerStore.map.baseLayer=l;l.setVisibility(false);var mstyle='';var toolTip='';if(layer.metadataUrl.length>0)
mstyle='metadataAvailable';if(this._layerStore.map.getScale()<layer.maxScale||this._layerStore.map.getScale()>layer.minScale){mstyle='hiddenLayer';toolTip=EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP')+layer.minScale+" - "+layer.maxScale;}
var blConfig={id:'b'+layer.id,text:layer.name,cls:mstyle,iconCls:'baseLayer',allowDrag:false,checked:false,visibility:false,layer:l,map:tree._layerStore.map,uiProvider:Ext.tree.RadioNode,listeners:{'postcheckchange':function(){tree._saveState();}}};if(layer.metadataUrl){blConfig.href=layer.metadataUrl;blConfig.hrefTarget='_blank';}
var baseLayerNode=new EasySDI_Map.BaseLayerNode(blConfig);this._baseLayers.insertBefore(baseLayerNode,this.previousNode);this.previousNode=baseLayerNode;if(user.loggedIn){Ext.Ajax.request({url:componentParams.componentUrl+'&format=raw&controller=map_context&task=getlist',params:{'user_id':user.id},success:function(response,opts){var object=Ext.util.JSON.decode(response.responseText);if(object.totalCount!=1){if(layer.defaultVisibility==1)
l.setVisibility(true);}},failure:function(response,opts){if(layer.defaultVisibility==1)
l.setVisibility(true);},scope:this});};},_addOverlayLayer:function(layer){var tree=this;var styles=Ext.state.Manager.get('overlayLayerStyle',false);var l;switch(layer.url_type.toUpperCase()){case'WMS':var extraOptions={isBaseLayer:false,singleTile:layer.singletile,buffer:0,opacity:layer.defaultOpacity}
if(layer.customStyle!=undefined)
extraOptions.customStyle=layer.customStyle;if(layer.units!=undefined)
extraOptions.units=layer.units;else if(extraOptions.units!=undefined)
extraOptions.units=this._layerStore.map.units;else{}
if(layer.maxExtent!=undefined)
extraOptions.maxExtent=layer.maxExtent;else if(this._layerStore.map.maxExtent!=undefined)
extraOptions.maxExtent=this._layerStore.map.maxExtent;else{}
if(layer.minScale!=undefined)
extraOptions.minScale=layer.minScale;else if(this._layerStore.map.minScale!=undefined)
extraOptions.minScale=this._layerStore.map.minScale;else{}
if(layer.maxScale!=undefined)
extraOptions.maxScale=layer.maxScale;else if(this._layerStore.map.maxScale!=undefined)
extraOptions.maxScale=this._layerStore.map.maxScale;else{}
if(layer.resolutions!=undefined)
extraOptions.resolutions=layer.resolutions;else{extraOptions.minResolution="auto";extraOptions.maxResolution="auto";}
var WMSoptions={LAYERS:layer.layers,SERVICE:layer.url_type,VERSION:layer.version,STYLES:'',SRS:layer.projection,FORMAT:layer.imageFormat,TRANSPARENT:true};l=new OpenLayers.Layer.WMS(layer.name,layer.url,WMSoptions,extraOptions);if(layer.cache)
l.params.CACHE=true;break;case'WFS':l=new OpenLayers.Layer.WFS(layer.name,layer.url,{typename:layer.layers});break;}
l.setVisibility(false);l.events.register('loadend',null,this.onLayerAdded);this._layerStore.map.addLayer(l);var mstyle='';var toolTip='';var metadataUrl='';var target='';if(layer.metadataUrl.length>0){mstyle='metadataAvailable';metadataUrl=layer.metadataUrl;target='_blank';}
if(this._layerStore.map.getScale()<layer.maxScale||this._layerStore.map.getScale()>layer.minScale){mstyle='hiddenLayer';toolTip=EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP')+" "+layer.minScale+" - "+layer.maxScale;}
var layerNode=new EasySDI_Map.StyledLayerNode({id:'o'+layer.id,text:layer.name,href:metadataUrl,hrefTarget:target,cls:mstyle,qtip:toolTip,iconCls:'overlayLayer',allowDrag:false,layer:l,map:tree._layerStore.map,listeners:{'postcheckchange':function(){tree._saveState();}}});if(styles&&typeof styles[layer.id]!="undefined"){if(typeof styles[layer.id].fillColor=="string"){layerNode.fillColor=styles[layer.id].fillColor;}
if(typeof styles[layer.id].strokeColor=="string"){layerNode.strokeColor=styles[layer.id].strokeColor;}
layerNode.opacity=styles[layer.id].opacity;layerNode.updateStyle(false);}else{layerNode.opacity=0.5;}
if(user.loggedIn){Ext.Ajax.request({url:componentParams.componentUrl+'&format=raw&controller=map_context&task=getlist',params:{'user_id':user.id},success:function(response,opts){var object=Ext.util.JSON.decode(response.responseText);if(object.totalCount!=1){if(layer.defaultVisibility==1)
l.setVisibility(true);}},failure:function(response,opts){if(layer.defaultVisibility==1)
l.setVisibility(true);},scope:this});}else{if(layer.defaultVisibility==1)
l.setVisibility(true);};if(layer.group)
this._overlays['overlay_'+layer.group].insertBefore(layerNode,this.previousLayerNode);this.previousLayerNode=layerNode;this.refreshLegend();},_saveState:function(){if(user.loggedIn&&!this._loading){var WMC=new OpenLayers.Format.WMC({parser:new OpenLayers.Format.WMC.v1_1_0_WithWFS()});Ext.Ajax.request({url:componentParams.componentUrl+'&format=raw&controller=map_context&task=save',params:{'user_id':user.id,'WMC_text':WMC.write(this._layerStore.map)},scope:this});}
this.refreshLegend();},_loadState:function(){if(user.loggedIn){this._loading=true;Ext.Ajax.request({url:componentParams.componentUrl+'&format=raw&controller=map_context&task=getlist',params:{'user_id':user.id},success:function(response,opts){var object=Ext.util.JSON.decode(response.responseText);var baseLayerSet=false;if(object.totalCount==1){var WMC=new OpenLayers.Format.WMC();var storedContextMap=WMC.read(object.map_contexts[0].WMC_text,{});Ext.each(storedContextMap.layers,function(layer,i){var found=false;for(var j=0;j<this._layerStore.map.layers.length;j++){if(this._layerStore.map.layers[j].name==layer.name){this._layerStore.map.layers[j].setVisibility(layer.getVisibility());found=true;if(this._layerStore.map.layers[j].getVisibility()&&this._layerStore.map.layers[j].isBaseLayer){this._layerStore.map.setBaseLayer(this._layerStore.map.layers[j]);baseLayerSet=true;}}}},this);}
if(!baseLayerSet){this._layerStore.map.baseLayer.setVisibility(true);}
this.refreshLegend();this._loading=false;},failure:function(response,opts){this._layerStore.map.baseLayer.setVisibility(true);this.refreshLegend();this._loading=false;},scope:this});}else{this._layerStore.map.baseLayer.setVisibility(true);}}});Ext.mixin(EasySDI_Map.LayerTree,EasySDI_Map.TriggerManager);