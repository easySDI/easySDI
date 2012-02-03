/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */

/**
 * Class for a layer manager - lists available layers by category.
 * 
 * @requires LayerNode.js
 */

// i18n prefix 01
Ext.namespace("EasySDI_Map");
Ext.namespace("GeoExt.tree");
Ext.namespace("SData");

EasySDI_Map.BaseLayerNode = Ext.extend(GeoExt.tree.LayerNode, {

	isInScaleRange : null,

	constructor : function(config) {
		EasySDI_Map.BaseLayerNode.superclass.constructor.apply(this, arguments);
	}
});

/**
 * A subclass of the GeoExt LayerNode which also stores some basic style
 * information, with methods to apply them to the layer and also persist the
 * information to a cookie.
 */
EasySDI_Map.StyledLayerNode = Ext
		.extend(
				GeoExt.tree.LayerNode,
				{
					fillColor : null,

					strokeColor : null,

					opacity : null,

					isInScaleRange : null,

					constructor : function(config) {
						EasySDI_Map.StyledLayerNode.superclass.constructor.apply(this, arguments);
					},

					/**
					 * Update the style of the layer according to the settings,
					 * and save the settings to the cookie.
					 */
					updateStyle : function(save) {
						// A WFS layer is styled locally, WMS is done via SLD on
						// the server
						// if
						// (this.layer.CLASS_NAME=="OpenLayers.Layer.WMS.Untiled")
						// {
						if (this.layer.CLASS_NAME == "OpenLayers.Layer.WMS") {
							// reset the default opacity (applied to the layer
							// in the map)
							// to be able to only see the opacity defined by the
							// user and styled by
							// the server
							this.layer.setOpacity(1);
							var sld = this._getSLD();

							this.layer.mergeNewParams( {
								SLD_BODY : sld
							});
						}
						if (this.layer.CLASS_NAME == "OpenLayers.Layer.WFS") {
							var style = new OpenLayers.Style( {
								strokeColor : "#" + this.strokeColor,
								strokeWidth : 1,
								fillColor : "#" + this.fillColor,
								fillOpacity : this.opacity
							});
							this.layer.setOpacity(1);
							this.layer.styleMap = new OpenLayers.StyleMap( {
								"default" : style
							});
							this.layer.refresh();
						}
						if (save) {
							this._save();
						}
					},

					/**
					 * Delete the user defined style to recover the default
					 * server SLD. Delete the saved style from the cookie.
					 */
					defautStyle : function() {
						// if
						// (this.layer.CLASS_NAME=="OpenLayers.Layer.WMS.Untiled")
						// {
						// Delete node user settings
						this.strokeColor = null;
						this.fillColor = null;
						this.opacity = null;

						// Get the default opacity defined for this node
						Ext.each(SData.overlayLayers, function(layer) {
							if (layer.name == this.layer.name) {
								this.layer.setOpacity(layer.defaultOpacity);
							}
						}, this);

						// Send empty SLD to the server
						if (this.layer.CLASS_NAME == "OpenLayers.Layer.WMS") {
							// This is the only way to get empty SLD works with
							// geoserver : build
							// SLD_BODY with no value for fill, stroke and
							// opacity
							var sld = EasySDI_Map.StyledLayerDescriptor.getSLDHeader();
							sld += EasySDI_Map.StyledLayerDescriptor.getDefaultSLD();
							sld += EasySDI_Map.StyledLayerDescriptor.getSLDFooter();
							this.layer.mergeNewParams( {
								SLD_BODY : sld
							});
							// this.layer.mergeNewParams({SLD_BODY:''});

						}
						// Set an empty styleMap to the layer --> TODO : test
						// this
						if (this.layer.CLASS_NAME == "OpenLayers.Layer.WFS") {
							this.layer.styleMap = new OpenLayers.StyleMap();
							this.layer.refresh();
						}
						this._delete();
					},

					/**
					 * Save the node settings to a cookie
					 */
					_save : function() {
						var styles = Ext.state.Manager.get('overlayLayerStyle', null);
						if (styles === null) {
							styles = {};
						}
						styles[this.id] = {
							fillColor : this.fillColor,
							strokeColor : this.strokeColor,
							opacity : this.opacity
						};
						Ext.state.Manager.set('overlayLayerStyle', styles);
						this.getOwnerTree().tree.refreshLegend();
					},

					/**
					 * Delete the node settings in the cookie
					 */
					_delete : function() {
						var styles = [];
						styles = Ext.state.Manager.get('overlayLayerStyle', null);
						if (styles != null) {
							delete styles[this.id];
						}
						Ext.state.Manager.set('overlayLayerStyle', styles);
						this.getOwnerTree().tree.refreshLegend();
					},

					/**
					 * Build an SLD string from the fill, stroke and opacity.
					 */
					_getSLD : function() {
						return String
								.format(
										'<StyledLayerDescriptor version="1.0.0" xmlns="http://www.opengis.net/sld" xmlns:ogc="http://www.opengis.net/ogc" '
												+ '  xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
												+ '  xsi:schemaLocation="http://www.opengis.net/sld http://schemas.opengis.net/sld/1.0.0/StyledLayerDescriptor.xsd">'
												+ '  <NamedLayer>' + '    <Name>{0}</Name>' + '    <UserStyle>'
												+ '      <FeatureTypeStyle>' + '        <Rule>' + '          <Title>Polygon</Title>'
												+ '          <PolygonSymbolizer>' + '            <Fill>'
												+ '              <CssParameter name="fill">#{1}</CssParameter>'
												+ '              <CssParameter name="fill-opacity">{2}</CssParameter>'
												+ '            </Fill>' + '            <Stroke>'
												+ '              <CssParameter name="stroke">#{3}</CssParameter>' + '            </Stroke>'
												+ '          </PolygonSymbolizer>' + '        </Rule>' + '      </FeatureTypeStyle>'
												+ '    </UserStyle>' + '  </NamedLayer>' + '</StyledLayerDescriptor>',
										this.layer.params.LAYERS, this.fillColor, this.opacity, this.strokeColor);
					}
				});

EasySDI_Map.LayerTree = Ext.extend(Ext.data.Tree, {
	
	loadEvents :[],
	_loadMask :null,
	loadStarted : false,
	
	constructor : function(config) {
		EasySDI_Map.LayerTree.superclass.constructor.apply(this, arguments);
		// We use a layer store to manage synchronicity between the map and the
	// layer tree
	this._layerStore = new GeoExt.data.LayerStore( {
		map : config.map.map
	});
	// Define the root node
	this._rootNode = new Ext.tree.TreeNode( {
		id : 'root'
	});
	// Define the layers
	this._baseLayers = new Ext.tree.TreeNode( {
		id : 'baseLayers',
		text : EasySDI_Map.lang.getLocal('LT_BASE_LAYERS'),
		singleClickExpand : true,
		iconCls : 'folderLayer'
	});
	// We build the overlays from the jos_easysdi_overlay_groups table
	this._overlays = [];

	this._addOverlayGroups();
	this._rebuildTree();

	this.isFirstLoad = true;

	this._layerStore.map.events.register('moveend', this, this._onMapMoveEnd);

	this._layerStore.map.events.register("changelayer", this, this.onPreAddLayer);
	this._layerStore.map.events.register("movestart", this, this.onMoveStarted);	
	this._layerStore.map.events.register("loadstart", this, this.onMoveStarted)
	this._layerStore.map.events.register("moveend", this, this.onMoveEnd);
	
/*	this._layerStore.map.events.register("loadstart", this, this.onMoveStarted);	
	this._layerStore.map.events.register("loadend", this, this.onMoveEnd);*/
	

	

	
	//_loadMask = new Ext.LoadMask(Ext.getBody(), {msg:EasySDI_Map.lang.getLocal('LOADER')});
	
	this._loadMask = this._layerStore.map.loadingPanel ; 
	this._loadMask.activate();
	// TODO DEREGISTER THIS EVENT
	},
		
	onPreAddLayer :function(evt) {
		
	
		if(evt.layer.visibility){
			this._loadMask.maximizeControl();
			this.loadEvents.push(evt.type) ;
		}		
		else{
			this.onMoveEnd(evt)
		}
	}, 
	
	onMoveStarted :function(evt) {
		
		this.loadEvents.push(evt.type) ;
		this._loadMask.maximizeControl();			
		
	}, 
	onMoveEnd :function(evt) {
		
		a = this.loadEvents.pop();
		console.log(this.loadEvents);
		if(this.loadEvents.length == 0)
			this._loadMask.hide();
		
		/*(function() {
			this.onLayerAdded(evt);
		}.defer(500, this));*/
			
		
	},
	
	



/**
 * After a map zoom, update treeNode aspect according to the visibility (visible
 * scale range) of the corresponding layer
 */
_onMapMoveEnd : function() {
	// alert('LAyerTree.onMapMoveEnd : '+ this._layerStore.map.getScale());
	// Update Overlay node style
	Ext.each(SData.overlayGroups, function(group) {
		var nodes = this._overlays['overlay_' + group.id].childNodes;
		Ext.each(nodes, function(node) {
			this._updateNodeStyle(node);
		}, this);
	}, this);

	// Update Base map node style
	Ext.each(SData.baseLayers, function(layer) {
		// alert('update baseLayer');
			var nodes = this._baseLayers.childNodes;
			Ext.each(nodes, function(node) {
				this._updateNodeStyle(node);
			}, this);
		}, this);

	this.refreshLegend();
},

_updateNodeStyle : function(node) {
	if (!node.layer)
		return;
	if (this._layerStore.map.getScale() < node.layer.maxScale || this._layerStore.map.getScale() > node.layer.minScale) {
		// alert (node.layer.name +' is not visible : '+ node.layer.minScale + "
	// - " + node.layer.maxScale);
	// Layer is not visible at this scale/resolution
	node.isInScaleRange = false;
	// Remove link to metadata URL

	// Add css class for not visible layer
	node.getUI().addClass('hiddenLayer');
	node.getUI().removeClass('metadataAvailable');
	/*
	 * if(node.getUI().getTextEl()) {
	 * node.getUI().getTextEl().removeAttributeNS("ext", "href");
	 * node.getUI().getTextEl().removeAttributeNS("ext", "hrefTarget"); }
	 */
	// Add css class for not visible layer Icon (-> not working)
	var el = Ext.get(node.getUI().iconNode);
	if (el)
		el.addClass('hiddenLayerIcon');

	// Add the tooltip displaying the range of scales/resolutions at
	// which the layer is visible
	if (node.getUI().getTextEl()) {
		if (node.getUI().getTextEl().setAttributeNS) {
			Ext.each(SData.overlayLayers, function(layer) {
				if (layer.name == node.layer.name) {
					node.getUI().getTextEl().setAttributeNS("ext", "qtip",
							EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP') + " " + layer.minScale + " - " + layer.maxScale);
					// node.getUI().getTextEl().setAttributeNS("ext",
					// "qtip", node.layer.minScale + " - " +
					// node.layer.maxScale);
				}
			}, this);
		} else {
			Ext.each(SData.overlayLayers, function(layer) {
				if (layer.name == node.layer.name) {
					node.getUI().getTextEl().setAttribute("ext:qtip",
							EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP') + " " + layer.minScale + " - " + layer.maxScale);
				}
			}, this);
			// node.getUI().getTextEl().setAttribute("ext:qtip",
			// node.layer.minScale + " - " + node.layer.maxScale);
		}
	}

} else {
	// alert (node.layer.name +' is visible : '+ node.layer.minScale + " - " +
	// node.layer.maxScale);
	// Layer is visible at this scale
	node.isInScaleRange = true;
	// Remove the css class for not visible layer (in case it was added)
	node.getUI().removeClass('hiddenLayer');
	// Add the link to metadata Url if needed
	Ext.each(SData.overlayLayers, function(layer) {
		if (layer.name == node.layer.name) {
			if (layer.metadataUrl.length > 0) {
				node.getUI().addClass('metadataAvailable');
				/*
				 * if(node.getUI().getTextEl()) { //Add metadata url value
				 * if(node.getUI().getTextEl().setAttributeNS) {
				 * node.getUI().getTextEl().setAttributeNS("ext", "href",
				 * layer.metadataUrl);
				 * node.getUI().getTextEl().setAttributeNS("ext", "hrefTarget",
				 * '_blank'); } else {
				 * node.getUI().getTextEl().setAttribute("ext:href",
				 * layer.metadataUrl);
				 * node.getUI().getTextEl().setAttribute("ext:hrefTarget",
				 * '_blank'); } }
				 */
			}
		}
	}, this);

	// Remove css class for not visible layer Icon (-> not working)
	var el = Ext.get(node.getUI().iconNode);
	if (el)
		el.removeClass('hiddenLayerIcon');

	// Remove the tooltip displaying the range of scales/resolutions at
	// which the layer is visible
	if (node.getUI().getTextEl()) {
		if (node.getUI().getTextEl().setAttributeNS) {
			node.getUI().getTextEl().removeAttributeNS("ext", "qtip");
		} else {
			node.getUI().getTextEl().removeAttribute("ext:qtip");
		}
	}
}
},

/**
 * Load the layers and set visibility from the database state. Has to occur
 * after render is complete otherwise the resize event causes WFS requests to
 * abort, which triggers a bug in OpenLayers (see
 * http://trac.openlayers.org/ticket/2065).
 */
loadLayers : function() {
	
this._addLayers();
this._loadState();
},

refreshLegend : function() {

this.trigger('refreshLegend', true);

},

/**
 * Initiate the build of the entire tree
 */
_rebuildTree : function() {
// this.previousNode = this._baseLayers;
	Ext.each(SData.overlayGroups, function(group) {
		this._rootNode.appendChild(this._overlays['overlay_' + group.id]);
		// this.previousNode = this._overlays['overlay_' + group.id];
		}, this);
	this._rootNode.appendChild(this._baseLayers);
	this.setRootNode(this._rootNode);
},

/**
 * Add the list of overlay groups as top level nodes in the tree.
 */
_addOverlayGroups : function() {
	Ext.each(SData.overlayGroups, function(group) {
		this._overlays['overlay_' + group.id] = new Ext.tree.TreeNode( {
			text : EasySDI_Map.lang.getLocal(group.name),
			singleClickExpand : true,
			iconCls : 'folderLayer',
			expanded : group.open
		});
	}, this);
},

/**
 * Add the layers including the base and overlay layers into the tree.
 */
_addLayers : function() {
	
	this.reader = new GeoExt.data.LayerReader();
	var noLayerNode = new Ext.tree.TreeNode( {
		text : EasySDI_Map.lang.getLocal('LT_NONE'),
		iconCls : 'baseLayer',
		allowDrag : false,
		checked : false,
		uiProvider : Ext.tree.RadioNode
	});
	noLayerNode.addListener('click', function() {
		// deactivate the base layers
			Ext.each(this._baseLayers.childNodes, function(layer) {
				if (layer.layer !== undefined) {
					layer.layer.setVisibility(false);
				}
			});
			this._saveState();
		}, this);
	this._baseLayers.appendChild(noLayerNode);
	this.previousNode = null;
	Ext.each(SData.baseLayers, this._addBaseLayer, this);
	this.previousLayerNode = null;
	
	Ext.each(SData.overlayLayers, this._addOverlayLayer, this);
	this._layerStore.map.zoomToExtent(componentParams.mapMaxExtent, true);
},

/**
 * Add a node and the associated layer for a base layer
 */
_addBaseLayer : function(layer, i) {
	// Store a reference to this object
	var tree = this;

	var extraOptions = {
		isBaseLayer : true,
		singleTile : layer.singletile,
		buffer : 0,
		opacity : layer.defaultOpacity
	}
	if (layer.customStyle != undefined)
		extraOptions.customStyle = layer.customStyle;
	if (layer.units != undefined)
		extraOptions.units = layer.units;
	else if (extraOptions.units != undefined)
		extraOptions.units= this._layerStore.map.units;
	else{}
	
	if (layer.maxExtent != undefined)
		extraOptions.maxExtent = layer.maxExtent;	
	else if (this._layerStore.map.maxExtent != undefined)
		extraOptions.maxExtent= this._layerStore.map.maxExtent;
	else{}
	
	if (layer.minScale != undefined)
		extraOptions.minScale = layer.minScale;
	else if (this._layerStore.map.minScale !=undefined)
		extraOptions.minScale= this._layerStore.map.minScale;
	else{}
	
	if (layer.maxScale != undefined)
		extraOptions.maxScale = layer.maxScale;
	else if (this._layerStore.map.maxScale !=undefined)
		extraOptions.maxScale= this._layerStore.map.maxScale;
	else{}
	
	if (layer.resolutions != undefined)
		extraOptions.resolutions = layer.resolutions;
	

	//New option used on WMTS layer
	if (layer.minResolution != undefined)
		extraOptions.minResolution = layer.minResolution;
	
	if (layer.maxResolution != undefined)
		extraOptions.maxResolution = layer.maxResolution;
	
	
	if (layer.resolutions == undefined && layer.minResolution == undefined && layer.maxResolution == undefined) {
		extraOptions.minResolution ="auto";
		extraOptions.maxResolution ="auto";
	}
		
	var l = null;
	switch (layer.type.toUpperCase()) {
	case 'WMTS':
		extraOptions.name=layer.name;
		extraOptions.url=layer.url;
		extraOptions.layer=layer.layers;
		extraOptions.matrixSet=layer.matrixSet;
		if(layer.matrixIds != undefined)
			extraOptions.matrixIds=layer.matrixIds;
		if(layer.style != undefined)
			extraOptions.style=layer.style;
		extraOptions.format=layer.imageFormat;
		
		l = new OpenLayers.Layer.WMTS( extraOptions);
		
		
		
		break;
	case 'WMS' :
		var WMSoptions = {
			LAYERS : layer.layers,
			SERVICE : layer.type,
			VERSION : layer.version,
			STYLES : '',
			SRS : layer.projection,
			FORMAT : layer.imageFormat
		};
		if (layer.cache)
			WMSoptions.CACHE = true;
		l = new OpenLayers.Layer.WMS(layer.name, layer.url, WMSoptions, extraOptions);
		
		
		if (layer.cache)
			l.params.CACHE = true;
		break;
	}
	this._layerStore.map.addLayer(l);
	
	l.events.register("loadstart", this, this.onMoveStarted);		
	l.events.register('loadend', this, this.onMoveEnd);
	//l.events.register('moveend', this, this.onMoveEnd);
	l.events.register('visibilitychanged', this, this.onMoveEnd);
	
	
	
	
	// At this point, the first basemap loaded will have been set up as the
	// default map basemap, and
	// its visibility set true: override this to prevent excess map fetches. (NB
	// it still seems to do one)
	// the visibility will be set by the loadState function.
	if (layer.defaultVisibility)
		this._layerStore.map.baseLayer = l;
	l.setVisibility(false);
	var mstyle = '';
	var toolTip = '';
	if (layer.metadataUrl.length > 0)
		mstyle = 'metadataAvailable';

	// To allow the map.getScale() function to return the expected value (the
	// scale of the displayed map at the component initilisation)
	// the map must be zoomToMaxExtent before the getScale call
	// this._layerStore.map.zoomToMaxExtent();
	if (this._layerStore.map.getScale() < layer.maxScale || this._layerStore.map.getScale() > layer.minScale) {
		mstyle = 'hiddenLayer';
		// inScaleRange = false;
		toolTip = EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP') + layer.minScale + " - " + layer.maxScale;
	}
	var blConfig = {
		id : 'b' + layer.id,
		text : layer.name,
		cls : mstyle,
		iconCls : 'baseLayer',
		allowDrag : false,
		checked : false,
		visibility : false,
		layer : l,
		map : tree._layerStore.map,
		uiProvider : Ext.tree.RadioNode,
		listeners : {
			'postcheckchange' : function() {
				tree._saveState();
			}
		}
	};

	if (layer.metadataUrl) {
		blConfig.href = layer.metadataUrl;
		blConfig.hrefTarget = '_blank';
	}
	var baseLayerNode = new EasySDI_Map.BaseLayerNode(blConfig);

	this._baseLayers.insertBefore(baseLayerNode, this.previousNode);
	this.previousNode = baseLayerNode;
	// this._baseLayers.appendChild(baseLayerNode);
	// baseLayerNode.ensureVisible();
	// If the user is logged in and already save a personnal state (means it is
	// not the first load of the component),
	// do not apply default visibility of the layers
	// Else, get the default visibility of the layer and apply it
	if (user.loggedIn) {
		Ext.Ajax.request( {
			url : componentParams.componentUrl + '&format=raw&controller=map_context&task=getlist',
			params : {
				'user_id' : user.id
			},
			success : function(response, opts) {
				var object = Ext.util.JSON.decode(response.responseText);
				if (object.totalCount != 1) {
					if (layer.defaultVisibility == 1)
						l.setVisibility(true);
				}
			},
			failure : function(response, opts) {
				if (layer.defaultVisibility == 1)
					l.setVisibility(true);
			},
			scope : this
		});
	}
	;
	

},

/**
 * Add a node and the associated layer for an overlay layer
 */
_addOverlayLayer : function(layer) {
	

	// Store a reference to this object
	var tree = this;
	var styles = Ext.state.Manager.get('overlayLayerStyle', false);
	var l; // layer

	// Overlay layers are initially created not visible, and made visible if
	// indicated in the Cookie.
	switch (layer.url_type.toUpperCase()) {
	case 'WMS':
		var extraOptions = {
			isBaseLayer : false,
			singleTile : layer.singletile,
			buffer : 0,
			opacity : layer.defaultOpacity
		}
		if (layer.customStyle != undefined)
			extraOptions.customStyle = layer.customStyle;
		if (layer.units != undefined)
			extraOptions.units = layer.units;
		else if (extraOptions.units != undefined)
			extraOptions.units= this._layerStore.map.units;
		else{}
		
		if (layer.maxExtent != undefined)
			extraOptions.maxExtent = layer.maxExtent;	
		else if (this._layerStore.map.maxExtent != undefined)
			extraOptions.maxExtent= this._layerStore.map.maxExtent;
		else{}
		
		if (layer.minScale != undefined)
			extraOptions.minScale = layer.minScale;
		else if (this._layerStore.map.minScale !=undefined)
			extraOptions.minScale= this._layerStore.map.minScale;
		else{}
		
		if (layer.maxScale != undefined)
			extraOptions.maxScale = layer.maxScale;
		else if (this._layerStore.map.maxScale !=undefined)
			extraOptions.maxScale= this._layerStore.map.maxScale;
		else{}
		
		if (layer.resolutions != undefined)
			extraOptions.resolutions = layer.resolutions;
		
		if (layer.minResolution != undefined)
			extraOptions.minResolution = layer.minResolution;
		
		if (layer.maxResolution != undefined)
			extraOptions.maxResolution = layer.maxResolution;
		
		
		if (layer.resolutions == undefined && layer.minResolution == undefined && layer.maxResolution == undefined) {
			extraOptions.minResolution ="auto";
			extraOptions.maxResolution ="auto";
		}

		var WMSoptions = {
			LAYERS : layer.layers,
			SERVICE : layer.url_type,
			VERSION : layer.version,
			STYLES : '',
			SRS : layer.projection,
			FORMAT : layer.imageFormat,
			TRANSPARENT : true
		};
		// l = new OpenLayers.Layer.WMS.Untiled(layer.name,
		// l = new OpenLayers.Layer.WMS(layer.name,
		// componentParams.proxyURL.asString + "&url=" + layer.url, WMSoptions,
		// {
		l = new OpenLayers.Layer.WMS(layer.name, layer.url, WMSoptions, extraOptions);

		if (layer.cache)
			l.params.CACHE = true;
		break;

	case 'WFS':
		l = new OpenLayers.Layer.WFS(layer.name, layer.url, {
			typename : layer.layers
		});
		break;
		
	case 'WMTS':
		var extraOptions = {
			isBaseLayer : false,
			buffer : 0,
			opacity : layer.defaultOpacity
		}
		if (layer.customStyle != undefined)
			extraOptions.customStyle = layer.customStyle;
		if (layer.units != undefined)
			extraOptions.units = layer.units;
		else if (extraOptions.units != undefined)
			extraOptions.units= this._layerStore.map.units;
		else{}
		
		if (layer.maxExtent != undefined)
			extraOptions.maxExtent = layer.maxExtent;	
		else if (this._layerStore.map.maxExtent != undefined)
			extraOptions.maxExtent= this._layerStore.map.maxExtent;
		else{}
		
		if (layer.minScale != undefined)
			extraOptions.minScale = layer.minScale;
		else if (this._layerStore.map.minScale !=undefined)
			extraOptions.minScale= this._layerStore.map.minScale;
		else{}
		
		if (layer.maxScale != undefined)
			extraOptions.maxScale = layer.maxScale;
		else if (this._layerStore.map.maxScale !=undefined)
			extraOptions.maxScale= this._layerStore.map.maxScale;
		else{}
		
		if (layer.resolutions != undefined)
			extraOptions.resolutions = layer.resolutions;
		
		if (layer.minResolution != undefined)
			extraOptions.minResolution = layer.minResolution;
		
		if (layer.maxResolution != undefined)
			extraOptions.maxResolution = layer.maxResolution;
		
		
		if (layer.resolutions == undefined && layer.minResolution == undefined && layer.maxResolution == undefined) {
			extraOptions.minResolution ="auto";
			extraOptions.maxResolution ="auto";
		}

		extraOptions.name=layer.name;
		extraOptions.url=layer.url;
		extraOptions.layer=layer.layers;
		extraOptions.matrixSet=layer.matrixSet;
		if(layer.matrixIds != undefined)
			extraOptions.matrixIds=layer.matrixIds
		if(layer.style != undefined)
			extraOptions.style=layer.style;
		extraOptions.format=layer.imageFormat;
		
		l = new OpenLayers.Layer.WMTS(extraOptions);

		break;
	}

	l.setVisibility(false);
	l.events.register("loadstart", this, this.onMoveStarted);	
	l.events.register('loadend', this, this.onMoveEnd);
	//l.events.register('moveend', this, this.onMoveEnd);
	l.events.register('visibilitychanged', this, this.onMoveEnd);
//	l.events.register('removelayer', null, this.onLayerRemoved(l.getVisibility()));
	
	// this._layerStore.add(this.reader.readRecords( [ l ]).records);
	
	this._layerStore.map.addLayer(l);
	
	// this._layerStore.map.setLayerIndex(l, 0);
	var mstyle = '';
	var toolTip = '';
	var metadataUrl = '';
	var target = '';
	if (layer.metadataUrl.length > 0) {
		mstyle = 'metadataAvailable';
		metadataUrl = layer.metadataUrl;
		target = '_blank';
	}
	if (this._layerStore.map.getScale() < layer.maxScale || this._layerStore.map.getScale() > layer.minScale) {
		mstyle = 'hiddenLayer';
		toolTip = EasySDI_Map.lang.getLocal('LT_SCALE_RAGE_TOOLTIP') + " " + layer.minScale + " - " + layer.maxScale;
		// metadataUrl= '';
		// target = '';
	}

	var layerNode = new EasySDI_Map.StyledLayerNode( {
		id : 'o' + layer.id,
		text : layer.name,
		href : metadataUrl,
		hrefTarget : target,
		cls : mstyle,
		qtip : toolTip,
		iconCls : 'overlayLayer',
		allowDrag : false,
		layer : l,
		map : tree._layerStore.map,
		listeners : {
			'postcheckchange' : function() {
				tree._saveState();
			}
		}
	});

	if (styles && typeof styles[layer.id] != "undefined") {
		// Safety check in case bad data in cookie
		if (typeof styles[layer.id].fillColor == "string") {
			layerNode.fillColor = styles[layer.id].fillColor;
		}
		if (typeof styles[layer.id].strokeColor == "string") {
			layerNode.strokeColor = styles[layer.id].strokeColor;
		}
		layerNode.opacity = styles[layer.id].opacity;
		layerNode.updateStyle(false);
	} else {
		// default opacity for when user styles the layer
		layerNode.opacity = 0.5;
	}

	// If the user is logged in and already save a personnal state (means it is
	// not the first load of the component),
	// do not apply default visibility of the layers
	// Else, get the default visibility of the layer and apply it
	if (user.loggedIn) {
		Ext.Ajax.request( {
			url : componentParams.componentUrl + '&format=raw&controller=map_context&task=getlist',
			params : {
				'user_id' : user.id
			},
			success : function(response, opts) {
				var object = Ext.util.JSON.decode(response.responseText);
				if (object.totalCount != 1) {
					if (layer.defaultVisibility == 1)
						l.setVisibility(true);
				}
			},
			failure : function(response, opts) {
				if (layer.defaultVisibility == 1)
					l.setVisibility(true);
			},
			scope : this
		});
	} else {
		if (layer.defaultVisibility == 1)
			l.setVisibility(true);
	}
	;

	// The node are inserted in reverse order : the last one in first...
	// So, add each node before the previous
	if (layer.group)
		this._overlays['overlay_' + layer.group].insertBefore(layerNode, this.previousLayerNode);
	this.previousLayerNode = layerNode;

	// layerNode.ensureVisible();
	
	this.refreshLegend();
},



/**
 * Save the state of the Map to database using WMC. We are primarily interested
 * in the "hidden" (ie !visible) property of the Layers
 */
// TODO This need to be adapted so that WFS layer states are also saved.
	_saveState : function() {
		// Only save state if Logged in ie a valid user
		if (user.loggedIn && !this._loading) {
			var WMC = new OpenLayers.Format.WMC( {
				parser : new OpenLayers.Format.WMC.v1_1_0_WithWFS()
			});
			Ext.Ajax.request( {
				url : componentParams.componentUrl + '&format=raw&controller=map_context&task=save',
				params : {
					'user_id' : user.id,
					'WMC_text' : WMC.write(this._layerStore.map)
				},
				scope : this
			});
		}
		this.refreshLegend();
	},

	_loadState : function() {
		// Only load state if Logged in ie a valid user
	if (user.loggedIn) {
		this._loading = true;
		Ext.Ajax.request( {
			url : componentParams.componentUrl + '&format=raw&controller=map_context&task=getlist',
			params : {
				'user_id' : user.id
			},
			success : function(response, opts) {
				var object = Ext.util.JSON.decode(response.responseText);
				// track if we have loaded a base layer
			var baseLayerSet = false;
			if (object.totalCount == 1) {
				var WMC = new OpenLayers.Format.WMC();
				var storedContextMap = WMC.read(object.map_contexts[0].WMC_text, {});

				Ext.each(storedContextMap.layers, function(layer, i) {
					var found = false;
					for ( var j = 0; j < this._layerStore.map.layers.length; j++) {
						if (this._layerStore.map.layers[j].name == layer.name) {
							this._layerStore.map.layers[j].setVisibility(layer.getVisibility());
							found = true;
							if (this._layerStore.map.layers[j].getVisibility() && this._layerStore.map.layers[j].isBaseLayer) {
								this._layerStore.map.setBaseLayer(this._layerStore.map.layers[j]);
								baseLayerSet = true;
							}
						}
					}
				}, this);
			}
			if (!baseLayerSet) {
				this._layerStore.map.baseLayer.setVisibility(true);
			}
			this.refreshLegend();
			this._loading = false;
		},
		failure : function(response, opts) {
			this._layerStore.map.baseLayer.setVisibility(true);
			this.refreshLegend();
			this._loading = false;
		},
		scope : this
		});
	} else {
		this._layerStore.map.baseLayer.setVisibility(true);
	}
}

});

// Mixin the trigger manager so we can update the legend
Ext.mixin(EasySDI_Map.LayerTree, EasySDI_Map.TriggerManager);