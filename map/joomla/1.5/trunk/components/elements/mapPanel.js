/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
 * 
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version. This
 * program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/gpl.html.
 */

Ext.namespace("EasySDI_Map");

EasySDI_Map.MapPanel = Ext.extend(Ext.Panel, {
	selectFeatureCtrl : null, // OL tool for selection
	// getFeatureInfoCtrl : null, // OL tool for getting item info at
	// click point on hover
	projMenuButton : null,
	overviewWidth : 120,
	overviewHeight : 80,
	constructor : function(config) {
		this._initProjections();
		this._initMap();
		this.GeoExtMapPanel = new GeoExt.MapPanel( {
			border : true,
			region : "center",
			id : 'geoMap',
			map : this.map
		});
		config.layout = "border";
		config.items = [ this.GeoExtMapPanel ];

		if (componentDisplayOption.LocalisationEnable || componentDisplayOption.ToolBarEnable) {
			config.items.push(this._getToolbar());
		}
		if (componentDisplayOption.AnnotationEnable || componentDisplayOption.CoordinateEnable) {
			config.items.push(this._getToolbarSouth());
		}

		EasySDI_Map.MapPanel.superclass.constructor.apply(this, arguments);
	},

	/**
	 * On an initial search, load the static WMS map for the results.
	 */
	addWmsLayerSet : function(featureType, filter, style, searchBarIdx) {
		var layer, layerOptions, p, dom = new OpenLayers.Format.Filter.v1_0_0().write(filter);
		var filterText = this._XMLtoString(dom);
		var actualFeatureType, layers = [];
		for ( var geom in SData.searchPrecisions) {
			p = SData.searchPrecisions[geom];
			actualFeatureType = featureType.replace('{geom}', geom);
			if (p.active) {
				var WMSoptions = {
					LAYERS : componentParams.pubFeaturePrefix + ':' + actualFeatureType,
					SERVICE : 'WMS',
					STYLES : style + (geom == '_geom_orig' ? '_geom_orig' : ''),
					SRS : componentParams.projection,
					TRANSPARENT : true,
					FILTER : filterText,
					GEOMETRYNAME : geom,
					FORMAT : 'image/png',
					VERSION : componentParams.pubWmsVersion
				};
				layerOptions = {
					isBaseLayer : false,
					singleTile : true,
					opacity : (p.style !== undefined ? p.style.fillOpacity || 0.8 : 0.8)
				};
				if (p.maxScale !== null && p.maxScale !== 0) {
					layerOptions.maxScale = p.maxScale;
				}
				if (p.minScale !== null) {
					layerOptions.minScale = p.minScale;
				}
				// layer = new
				// OpenLayers.Layer.WMS.Untiled(EasySDI_Map.lang.getLocal('MP_SEARCH_RESULTS')
				// + ' ' +(searchBarIdx+1),
				layer = new OpenLayers.Layer.WMS(EasySDI_Map.lang.getLocal('MP_SEARCH_RESULTS') + ' ' + (searchBarIdx + 1),
						componentParams.pubWmsUrl, WMSoptions, layerOptions);
				layer.events.register('loadend', layer, this._hideMsg);
				layers.push(layer);
				// Do we need to add an extra layer for when this layer
				// is too
				// zoomed out to see?
				if (p.minScale && p.lowScaleSwitchTo) {
					actualFeatureType = featureType.replace('{geom}', p.lowScaleSwitchTo);
					lp = SData.searchPrecisions[p.lowScaleSwitchTo];
					var LowScaleWMSoptions = {
						LAYERS : actualFeatureType,
						SERVICE : 'WMS',
						STYLES : style,
						SRS : componentParams.projection,
						TRANSPARENT : true,
						FILTER : filterText,
						GEOMETRYNAME : p.lowScaleSwitchTo,
						FORMAT : 'image/png',
						VERSION : componentParams.pubWmsVersion
					};
					layerOptions = {
						isBaseLayer : false,
						singleTile : true,
						opacity : (lp.style !== undefined ? lp.style.fillOpacity || 0.8 : 0.8),
						maxScale : p.minScale,
						minScale : lp.minScale || 1000, // A default
						// high
						// maxresolution.
						displayInLayerSwitcher : false
					};

					// var lowScaleLayer = new
					// OpenLayers.Layer.WMS.Untiled("Search results",
					var lowScaleLayer = new OpenLayers.Layer.WMS("Search results", componentParams.pubWmsUrl, LowScaleWMSoptions,
							layerOptions);
					this.map.addLayer(lowScaleLayer);
					layers.push(lowScaleLayer);
					lowScaleLayer.events.register('loadend', lowScaleLayer, this._hideMsg);
				}
				this.map.addLayer(layer);
			}
		}

		// No dataprecision selected
		// A layer corresponding to the searchlayer definition is added
		// to the
		// map as the unique layer.
		if (layers.length === 0) {
			var geom = SData.searchLayer.geometryName;
			actualFeatureType = featureType.replace('{geom}', geom);
			var WMSoptions = {
				LAYERS : componentParams.pubFeaturePrefix + ':' + actualFeatureType,
				SERVICE : 'WMS',
				STYLES : style,
				SRS : componentParams.projection,
				TRANSPARENT : true,
				FILTER : filterText,
				GEOMETRYNAME : geom,
				FORMAT : 'image/png',
				TILED : true,
				VERSION : componentParams.pubWmsVersion
			};
			layerOptions = {
				isBaseLayer : false,
				singleTile : true,
				opacity : 0.4
			};

			layer = new OpenLayers.Layer.WMS(EasySDI_Map.lang.getLocal('MP_SEARCH_RESULTS') + ' ' + (searchBarIdx + 1),
					componentParams.pubWmsUrl, WMSoptions, layerOptions);
			layer.events.register('loadend', layer, this._hideMsg);
			layers.push(layer);
			this.map.addLayer(layer);
		}
		return layers;
	},

	/**
	 * Convert an XML DOM element to a string, so it can be passed as a POST
	 * parameter.
	 */
	_XMLtoString : function(elem) {
		var serializer, serialized;
		try {
			// XMLSerializer exists in current Mozilla browsers
			serializer = new XMLSerializer();
			serialized = serializer.serializeToString(elem);
		} catch (e) {
			// Internet Explorer has a different approach to serializing
			// XML
			serialized = elem.xml;
		}
		return serialized;
	},

	/**
	 * Add WFS layers of features to the map. Parameters are the layer title and
	 * the protocol. The layers are hooked up to the feature selection control.
	 * One layer is created per selected precision. Returns the WFS vector
	 * layers created in form: { precisionGeomName: { layer: x, lowScaleLayer: y },
	 * .... }
	 */
	addWfsFeatureLayerSet : function(protocol) {
		var layers = [], lp, p;
		// We need to use the FixedMultiLayer strategy for loading WFS,
		// so that
		// the filter gets through
		// and this allows multiple layers to be build from a single WFS
		// call.
		var strategy = new OpenLayers.Strategy.FixedMultiLayer( {
			protocol : protocol
		});
		// Enable reading of multiple precision layers in one WFS call
		protocol.readModeMulti = true;
		try {
			var layer, lowScaleLayer;
			for ( var geom in SData.searchPrecisions) {
				p = SData.searchPrecisions[geom];
				if (p.active) {
					var selectOpacity = (p.style !== undefined ? p.style.fillOpacity || 0.4 : 0.4) + 0.3;
					layer = new OpenLayers.Layer.Vector(EasySDI_Map.lang.getLocal('MP_SELECTABLE_LAYER'), {
						strategies : [ strategy ],
						protocol : protocol,
						maxScale : p.maxScale,
						minScale : p.minScale,
						geometryName : geom,
						styleMap : new OpenLayers.StyleMap( {
							"default" : new OpenLayers.Style( {
								strokeColor : "#3399ff",
								strokeWidth : 2,
								fillOpacity : 0
							}),
							"select" : new OpenLayers.Style( {
								fillColor : "#ff9933",
								strokeColor : "#3399ff",
								fillOpacity : selectOpacity
							})
						})
					});
					this.map.addLayer(layer);
					layers.push(layer);
					// now check if another layer is required for when
					// the scale
					// is too low
					if (p.minScale && p.lowScaleSwitchTo) {
						lp = SData.searchPrecisions[p.lowScaleSwitchTo];
						selectOpacity = (lp.style !== undefined ? lp.style.fillOpacity || 0.4 : 0.4) + 0.3;
						lowScaleLayer = new OpenLayers.Layer.Vector(p.title + ' low scale', {
							strategies : [ strategy ],
							protocol : protocol,
							maxScale : p.minScale,
							minScale : lp.minScale || 1000, // a default
							// high
							// max resolution if
							// not specified
							geometryName : p.lowScaleSwitchTo,
							displayInLayerSwitcher : false,
							styleMap : new OpenLayers.StyleMap( {
								"default" : new OpenLayers.Style( {
									strokeColor : "#3399ff",
									strokeWidth : 2,
									fillOpacity : 0
								}),
								"select" : new OpenLayers.Style( {
									fillColor : "#66ccff",
									strokeColor : "#3399ff",
									fillOpacity : lp.style.fillOpacity + 0.3
								})
							})
						});
						this.map.addLayer(lowScaleLayer);
						layers.push(lowScaleLayer);
					}
				}
			}

			if (layers.length === 0) {
				// Means : No data precision selected.
				// So, a layer corresponding to the searchLayer
				// definition is
				// added to the map as the unique layer.
				layer = new OpenLayers.Layer.Vector(EasySDI_Map.lang.getLocal('MP_SELECTABLE_LAYER'), {
					strategies : [ strategy ],
					protocol : protocol,
					geometryName : SData.searchLayer.geometryName,
					styleMap : new OpenLayers.StyleMap( {
						"default" : new OpenLayers.Style( {
							strokeColor : "#3399ff",
							strokeWidth : 2,
							fillOpacity : 0
						}),
						"select" : new OpenLayers.Style( {
							fillColor : "#ff9933",
							strokeColor : "#3399ff",
							fillOpacity : 0.7
						})
					})
				});
				this.map.addLayer(layer);
				layers.push(layer);
			}
			// activate all the layers in one go (from a single WFS
			// call).

			strategy.activate(protocol);
		} finally {
			protocol.readModeMulti = false;
		}

		// Since we start out with no layers, the select feature control
		// needs
		// to have it's
		// initialisation completed.
		var origActive = this.selectFeatureCtrl.active;
		if (origActive) {
			this.selectFeatureCtrl.deactivate();
		}
		if (this.selectFeatureCtrl.layers === null) {
			this.selectFeatureCtrl.layers = [];
		}
		this.selectFeatureCtrl.layers = this.selectFeatureCtrl.layers.concat(layers);
		this.selectFeatureCtrl.layer = new OpenLayers.Layer.Vector.RootContainer(this.selectFeatureCtrl.id + "_container", {
			layers : this.selectFeatureCtrl.layers
		});
		this.selectFeatureCtrl.handlers.feature.layer = this.selectFeatureCtrl.layer;
		if (origActive) {
			this.selectFeatureCtrl.activate();
		}

		// Same for the getFeatureInfo control.
		// if (this.getFeatureInfoCtrl.layers===null ||
		// this.getFeatureInfoCtrl.layers===undefined) {
		// this.getFeatureInfoCtrl.layer=layers[0];
		// this.getFeatureInfoCtrl.handlers.feature.layer = layers[0];
		// this.getFeatureInfoCtrl.layers=[];
		// }
		// this.getFeatureInfoCtrl.layers =
		// this.getFeatureInfoCtrl.layers.concat(layers);
		// If the navigation toolbutton is active, the getFeatureInfo
		// button can
		// now be active as it has a layer
		// this.navButton can be null if the ToolBar is disable
		// (componentDisplayOption.ToolBarEnable:false)
		// if (this.navButton) {
		// if (this.navButton.pressed) {
		// this.getFeatureInfoCtrl.activate();
		// }
		// }
		return layers;
	},

	/**
	 * Initialise the map and all it's controls.
	 */
	_initMap : function() {
		var options = {
			controls : [],
			layers : []
		};

		if (SData.baseMap.projection != undefined)
			options.projection = SData.baseMap.projection;
		if (SData.baseMap.units != undefined)
			options.units = SData.baseMap.units;
		if (SData.baseMap.maxExtent != undefined)
			options.maxExtent = SData.baseMap.maxExtent;
		if (SData.baseMap.minScale != undefined)
			options.minScale = SData.baseMap.minScale;
		if (SData.baseMap.maxScale != undefined)
			options.maxScale = SData.baseMap.maxScale;
		if (SData.baseMap.resolutions != undefined)
			options.resolutions = SData.baseMap.resolutions;
		if (componentParams.numZoomLevels != undefined)
			options.numZoomLevels = componentParams.numZoomLevels;

		var ov_options = {};
		if (SData.baseMap.maxExtent != undefined)
			ov_options.maxExtent = SData.baseMap.maxExtent;

		this.map = new OpenLayers.Map(options);

		// hack for click decalage
		if ($.browser.mozilla)
			this.map.events.hack = 5;
		else if ($.browser.msie)
			this.map.events.hack = 2;
		if ($.browser.safari)
			this.map.events.hack = 4;

		this.map.events.getMousePosition = function(evt) {
			if (!this.includeXY) {
				this.clearMouseCache();
			} else if (!this.element.hasScrollEvent) {
				OpenLayers.Event.observe(window, "scroll", this.clearMouseListener);
				this.element.hasScrollEvent = true;
			}

			if (!this.element.scrolls) {
				this.element.scrolls = [ (document.documentElement.scrollLeft || document.body.scrollLeft),
						(document.documentElement.scrollTop || document.body.scrollTop) ];
			}

			if (!this.element.lefttop) {
				this.element.lefttop = [ (document.documentElement.clientLeft || 0), (document.documentElement.clientTop || 0) ];
			}

			if (!this.element.offsets) {
				this.element.offsets = OpenLayers.Util.pagePosition(this.element);
				this.element.offsets[0] += this.element.scrolls[0];
				this.element.offsets[1] += this.element.scrolls[1];
			}
			return new OpenLayers.Pixel((evt.clientX + this.element.scrolls[0]) - this.element.offsets[0] - this.element.lefttop[0]
					- this.hack, (evt.clientY + this.element.scrolls[1]) - this.element.offsets[1] - this.element.lefttop[1] - this.hack);
		}

		this.map.addControl(new OpenLayers.Control.ScaleLine());

		// Adds a scale line to the map

		// Adds mouse position coordinates
		// this.map.addControl(new OpenLayers.Control.MousePosition());

		// Adds an overview control to the map
		if (componentDisplayOption.MapOverviewEnable) {
			var ovControl = new OpenLayers.Control.OverviewMap( {
				mapOptions : ov_options,
				size : new OpenLayers.Size(this.overviewWidth, this.overviewHeight)
			});
			// This forces the overview to never pan or zoom, since it
			// is always
			// suitable.
			ovControl.isSuitableOverview = function() {
				return true;
			};
			this.map.addControl(ovControl);
		}

		if (componentDisplayOption.ToolBarEnable) {
			this.map.addControl(new OpenLayers.Control.PanZoomBar());
		}

		// Add navigation history control. We'll hook our own toolbar
		// buttons to
		// this.
		this.navHistoryCtrl = new OpenLayers.Control.NavigationHistory();
		this.map.addControl(this.navHistoryCtrl);

		this.navCtrl = new OpenLayers.Control.Navigation();
		this.map.addControl(this.navCtrl);
		this.navCtrl.activate();

		this.selectFeatureCtrl = new OpenLayers.Control.SelectFeature(null, {
			clickout : true,
			toggle : true,
			multiple : false,
			hover : false,
			toggleKey : "ctrlKey", // ctrl key removes from selection
			multipleKey : "shiftKey", // shift key adds to selection
			box : true
		});

		// Zoom in
		this.zoomInBoxCtrl = new OpenLayers.Control.ZoomBox();
		this.map.addControl(this.zoomInBoxCtrl);
		// Zoom out
		this.zoomOutBoxCtrl = new OpenLayers.Control.ZoomBox( {
			out : true
		});
		this.map.addControl(this.zoomOutBoxCtrl);

		this.map.addControl(this.selectFeatureCtrl);

		this.addGetFeatureCtrl();

		// this.getFeatureInfoCtrl = new
		// OpenLayers.Control.ClickFeature(null);
		// this.getFeatureInfoCtrl.onClick =
		// this._displayFeaturePopup.createDelegate(this);
		// this.map.addControl(this.getFeatureInfoCtrl);
		// Register a handler for pan or zoom, so we can update history
		// buttons
		this.map.events.register('moveend', this, this._onMapMoveEnd);
		// TODO DEREGISTER THIS EVENT
	},

	// Test : to remove.
	addGetFeatureCtrl : function() {
		/**
		 * this.getFeatureCtrl = new OpenLayers.Control.GetFeature({ protocol:
		 * new OpenLayers.Protocol.WFS({ url: componentParams.proxiedPubWfsUrl,
		 * featureNS: componentParams.pubFeatureNS, featurePrefix:
		 * componentParams.pubFeaturePrefix, featureType: 'ReservesNaturelles',
		 * geometryName: 'the_geom', srsName: componentParams.projection,
		 * version: "1.0.0", propertyNames: ["NOM"] }), box: true, hover: false,
		 * multipleKey: "shiftKey", toggleKey: "ctrlKey" });
		 */
		this.getFeatureCtrl = new EasySDI_Map.WMSGetFeatureInfo( {
			url : componentParams.componentUrl + '&view=getfeatureinfo',
			infoFormat : 'text/html',
			queryVisible : true,
			format : new OpenLayers.Format.WMSGetFeatureInfo(),
			eventListeners : {
				getfeatureinfo : function(event) {
					if (this.popup != null)
						this.popup.destroy();
					this.popup = new OpenLayers.Popup.FramedCloud("epopup", this.map.getLonLatFromPixel(event.xy), null, event.text, null,
							true);
					this.map.addPopup(this.popup);
					$(".tabs").tabs();
					this.popup.updateSize();
				}
			}
		});

		this.map.addControl(this.getFeatureCtrl);
		// support GetFeatureInfo
	},

	/**
	 * Event handler for the click when the get feature info control is active
	 */
	_displayFeaturePopup : function(feature) {
		this.trigger('featurePopup', feature.attributes[componentParams.featureIdAttribute]);
	},

	/**
	 * moveend event handler for the map. Updates the state of the map history
	 * buttons.
	 */
	_onMapMoveEnd : function() {
		// To set previous button, note that the first item in the
		// history stack
		// is created
		// during page setup so we ignore it (hence <2).
		// this.previousButton can be null if the ToolBar is disable
		// (componentDisplayOption.ToolBarEnable:false)
		if (this.previousButton) {
			this.previousButton.setDisabled(this.navHistoryCtrl.previousStack.length < 2);
			this.nextButton.setDisabled(this.navHistoryCtrl.nextStack.length === 0);
		}
	},

	_onZoomToLocation : function(combo, newValue, index) {

		// the value displayed was picked from the drop down, so use
		// the
		// store record to define
		// the geometry
		// for ( var i = this.locAutocomplete.store.getCount() - 1; i >=
		// 0; i--) {
		// var record = this.locAutocomplete.store.getAt(i);
		// if (record.data.ipa_fullid == this.locAutocomplete.value) {
		Ext.getCmp('geoMap').map.zoomToExtent(newValue.data.feature.geometry.getBounds());
		// }
		// }
		if (false) {
			// the value is different to last selected.
			// assume value typed in: try to convert to a coordinate in
			// current
			// projection
			// then zoom in to it. Need to spec an area.
			var x, y, start, q;
			var mode = 0; // start
			// process the x and y coords
			var rawValue = this.locAutocomplete.getRawValue();
			for (q = 0; q < rawValue.length; q++) {
				if (mode === 0) { // at start
					if (rawValue.charAt(q) !== ' ') {
						mode = 1;
						start = q;
					}
				} else if (mode == 1) { // reading in X value
					if (rawValue.charAt(q) === ' ' || rawValue.charAt(q) == ',') {
						mode = 2;
						x = rawValue.substring(start, q);
					}
				} else if (mode == 2) {
					if (rawValue.charAt(q) != ' ' && rawValue.charAt(q) != ',') {
						mode = 3;
						y = rawValue.substring(q);
					}
				}
			}
			if (mode != 3 || isNaN(parseFloat(x)) || isNaN(parseFloat(y))) {
				alert(EasySDI_Map.lang.getLocal('MP_ZOOM_ERROR') + " : [" + x + "] [" + y + "]");
			} else {
				var point = new OpenLayers.Geometry.Point(parseFloat(x), parseFloat(y));
				var projTitle = this.projMenuButton.getText();
				var fromProjection;
				for (q = 0; q < componentParams.displayProjections.length; q++) {
					if (projTitle == componentParams.displayProjections[q].title) {
						fromProjection = new OpenLayers.Projection(componentParams.displayProjections[q].name);
					}
				}
				var toProjection = new OpenLayers.Projection(componentParams.projection);
				point.transform(fromProjection, toProjection);
				var lonlat = new OpenLayers.LonLat(point.x, point.y);
				this.map.panTo(lonlat);
				this.map.zoomTo(componentParams.defaultCoordMapZoom || 4);
			}
		}
	},

	/**
	 * Remove an array of layers from the map.
	 */
	removeLayers : function(layers, removeWms, removeWfs) {
		Ext.each(layers, function(layer) {
			if (this.selectFeatureCtrl.layers !== null && typeof this.selectFeatureCtrl.layers !== "undefined") {
				OpenLayers.Util.removeItem(this.selectFeatureCtrl.layers, layer);
			}
			// if (this.getFeatureInfoCtrl.layers !== null && typeof
				// this.getFeatureInfoCtrl.layers !== "undefined") {
				// OpenLayers.Util.removeItem(this.getFeatureInfoCtrl.layers,
				// layer);
				// }
				// if (layer.CLASS_NAME=="OpenLayers.Layer.WMS.Untiled"
				// &&
				// removeWms) {
				if (layer.CLASS_NAME == "OpenLayers.Layer.WMS" && removeWms) {
					layer.events.unregister('loadend', layer, this._hideMsg);
				}
				// if ((removeWms &&
				// layer.CLASS_NAME=="OpenLayers.Layer.WMS.Untiled") ||
				if ((removeWms && layer.CLASS_NAME == "OpenLayers.Layer.WMS")
						|| (removeWfs && layer.CLASS_NAME == "OpenLayers.Layer.Vector")) {
					this.map.removeLayer(layer);
				}
			}, this);
	},

	/**
	 * Hide the loading progress popup.
	 */
	_hideMsg : function(layer) {
		Ext.Msg.hide();
		// we only want to do this once on initial load, not when the
		// map view
		// changes.
		layer.object.events.unregister('loadend', null, arguments.callee);
	},

	/**
	 * Method which creates an array of projections for the mouse position
	 * control.
	 */
	_initProjections : function() {
		Ext.each(componentParams.displayProjections, function(proj) {
			if (typeof proj.proj4text !== "undefined") {
				Proj4js.defs[proj.name] = proj.proj4text;
			}
		});
	},

	/**
	 * Trigger: zoomToExtent Zooms the map to a boundary extent.
	 */
	zoomToExtent : function(bounds) {
		this.map.zoomToExtent(bounds);
	},

	/**
	 * Trigger: highlightFeature Adds a feature to the selection.
	 */
	highlightFeature : function(feature) {
		this.selectFeatureCtrl.select(feature);
	},

	/**
	 * Trigger: clearSelection Clears the current selection.
	 */
	clearSelection : function() {
		this.selectFeatureCtrl.unselectAll();
	},

	/**
	 * Trigger: refreshLegend Refresh legend when edit annotations layer
	 */
	refreshLegend : function() {
		this.trigger('refreshLegend');
	},

	/**
	 * Initialise the coordinates toolbar
	 */
	_getToolbarSouth : function() {
		// Annotation toolbar
		// Get the annotation styles to populate the dropdown list
		var styleDropDownItems = this._createAnnotationStyleDropDownItems();

		this.rectangleButton = new Ext.Toolbar.Button( {
			iconCls : 'rectangleBtn',

			minWidth : 26,
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.polygonButton = new Ext.Toolbar.Button( {
			iconCls : 'polygonBtn',
			minWidth : 26,
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.pointButton = new Ext.Toolbar.Button( {
			iconCls : 'pointBtn',
			minWidth : 26,
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.modifyFeatureButton = new Ext.Toolbar.Button( {
			iconCls : 'modifyFeatureBtn',
			minWidth : 26,
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.pathButton = new Ext.Toolbar.Button( {
			iconCls : 'pathBtn',
			minWidth : 26,
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.selectFeatureButton = new Ext.Toolbar.Button( {
			iconCls : 'deleteFeatureBtn',
			minWidth : 26,
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});

		// Position toolbar
		var mouseposDiv = document.createElement("div");
		mouseposDiv.setAttribute('id', 'mapposition');
		// Add a map listener that drops mouse position in the status
		// bar.
		this.mousePos = new OpenLayers.Control.MousePosition( {
			div : mouseposDiv,
			numDigits : 0
		});
		this.map.addControl(this.mousePos);
		// Build a menu for the projections available
		var projMenuItems = [];
		Ext.each(componentParams.displayProjections, function(item, i) {
			projMenuItems.push( {
				text : item.title,
				checked : i === 0, // first item checked
				group : 'proj',
				checkHandler : this.onProjChecked,
				scope : this,
				stateId : i
			});
		}, this);
		var projMenu = {
			xtype : 'item',
			items : projMenuItems
		};
		this.projMenuButton = new Ext.Toolbar.Button( {
			text : EasySDI_Map.lang.getLocal(projMenuItems[0].text), // TODO
			// -
			// read
			// this
			// from
			// user
			// settings
			menu : projMenu
		});

		if (componentDisplayOption.AnnotationEnable && componentDisplayOption.CoordinateEnable) {
			return new Ext.Toolbar( {
				region : "south",
				autoHeight : true,
				items : [ this.rectangleButton, this.polygonButton, this.pointButton, this.pathButton, this.modifyFeatureButton,
						this.selectFeatureButton, {
							iconCls : 'styleChooserBtn',
							menu : {
								items : styleDropDownItems
							}
						}, {
							xtype : 'tbfill'
						}, this.projMenuButton, new Ext.Toolbar.Item(mouseposDiv) ]
			});
		} else if (componentDisplayOption.AnnotationEnable && !componentDisplayOption.CoordinateEnable) {
			return new Ext.Toolbar( {
				region : "south",
				autoHeight : true,
				items : [ this.rectangleButton, this.polygonButton, this.pointButton, this.pathButton, this.modifyFeatureButton,
						this.selectFeatureButton, {
							iconCls : 'styleChooserBtn',
							menu : {
								items : styleDropDownItems
							}
						} ]
			});
		} else if (!componentDisplayOption.AnnotationEnable && componentDisplayOption.CoordinateEnable) {
			return new Ext.Toolbar( {
				region : "south",
				autoHeight : true,
				items : [ {
					xtype : 'tbfill'
				}, this.projMenuButton, new Ext.Toolbar.Item(mouseposDiv) ]
			});
		} else {
			return null;
		}
	},

	/**
	 * Initialise the toolbar controls
	 */
	_getToolbar : function() {
		// Toolbar items for localisation
		if (SData.localisationLayers.length > 0) {
			var storeObject = {
				fields : [ {
					name : 'ipa_fullid',
					type : 'string'
				}, {
					name : 'ipa_display_name',
					type : 'string'
				} ],
				wfslist : []
			};
			Ext.each(SData.localisationLayers, function(loc) {
				var parts = loc.feature_type_name.split(":");
				// Note that in the fieldlist the "fid" is a generated
					// feature
					// id comprising the
					// feature type and the id - the id is not available
					// as a
					// distinct entry in the record
					storeObject.wfslist.push( {
						url : loc.wfs_url,
						featureType : parts[1],
						featurePrefix : parts[0],
						featureNS : loc.featureNS,
						fields : [ {
							name : 'ipa_fullid',
							mapping : (componentParams.autocompleteUseFID ? 'fid' : loc.id_field_name),
							type : 'string',
							prefix : loc.id
						}, {
							name : 'ipa_display_name',
							mapping : loc.name_field_name,
							type : 'string',
							append : " (" + loc.title + ")"
						} ],
						filterField : loc.name_field_name,
						maxFeatures : componentParams.autocompleteMaxFeat
					});
				}, this);
			this.locStore = new EasySDI_Map.WfsMultiStore( {}, storeObject);
		} else {
			this.locStore = new Ext.data.SimpleStore( {
				fields : [ 'ipa_fullid', 'ipa_display_name' ],
				data : [ [ '0', EasySDI_Map.lang.getLocal('SP_ERROR_NO_LOCALISATION') ] ]
			});
		}
		if (componentParams.localisationInputWidth == undefined)
			componentParams.localisationInputWidth = 200;
		else
			componentParams.localisationInputWidth = parseInt(componentParams.localisationInputWidth);
		this.locAutocomplete = new Ext.form.ComboBox( {
			id : 'localisationInputWidth',
			store : this.locStore,
			valueField : 'ipa_fullid',
			displayField : 'ipa_display_name',
			emptyText : EasySDI_Map.lang.getLocal('FP_SURVEY_SEARCH_FOR'),
			loadingText : EasySDI_Map.lang.getLocal('SP_SEARCHING'),
			width : componentParams.localisationInputWidth,
			triggerAction : 'all',
			mode : 'remote',
			selectOnFocus : true,
			minChars : componentParams.autocompleteNumChars
		});

		this.locAutocomplete.on('select', this._onZoomToLocation);
		/*
		 * this.zoomButton = new Ext.Toolbar.Button( { tooltip :
		 * EasySDI_Map.lang.getLocal('MP_ZOOM_TTIP'), iconCls : "x-btn-icon
		 * zoom", handler : this._onZoomToLocation, scope : this });
		 */

		// General tool bar items
		this.previousButton = new Ext.Toolbar.Button( {
			iconCls : 'previousBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_HIST_BACK_TTIP'),
			handler : function() {
				this.navHistoryCtrl.previousTrigger();
			},
			scope : this
		});
		this.nextButton = new Ext.Toolbar.Button( {
			iconCls : 'nextBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_HIST_NEXT_TTIP'),
			handler : function() {
				this.navHistoryCtrl.nextTrigger();
			},
			scope : this
		});
		this.navButton = new Ext.Toolbar.Button( {
			iconCls : 'navBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_PAN_TTIP'),
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this,
			pressed : true
		});
		this.selectButton = new Ext.Toolbar.Button( {
			iconCls : 'selectBtn',
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.zoomInBoxButton = new Ext.Toolbar.Button( {
			iconCls : 'zoomInBoxBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_ZOOM_IN_TTIP'),
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.zoomOutBoxButton = new Ext.Toolbar.Button( {
			iconCls : 'zoomOutBoxBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_ZOOM_OUT_TTIP'),
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.zoomToScaleField = new Ext.form.TextField( {
			height : 20,
			width : 50,
			tooltip : EasySDI_Map.lang.getLocal('MP_ZOOM_TO_SCALE_TTIP'),
			readOnly : false,
			enableKeyEvents : true
		});
		this.zoomToMaxExtentButton = new Ext.Toolbar.Button( {
			iconCls : 'zoomToScaleBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_ZOOM_TO_EXTENT_TTIP'),
			enableToggle : false,
			allowDepress : false,
			handler : function() {
				this._zoomToMaxExtent();
			},
			scope : this
		});
		this.printMapButton = new Ext.Toolbar.Button( {
			iconCls : 'printMapBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_PRINT_VERSION_TTIP'),
			enableToggle : true,
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.saveMapButton = new Ext.Toolbar.Button( {
			iconCls : 'saveMapBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_SAVE_AS_IMAGE_TTIP'),
			enableToggle : true,
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.pdfButton = new Ext.Toolbar.Button( {
			iconCls : 'pdfBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_PDF'),
			enableToggle : true,
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});
		this.getFeatureButton = new Ext.Toolbar.Button( {
			iconCls : 'selectBtn',
			tooltip : EasySDI_Map.lang.getLocal('MP_SELECT_BUTTON_TOOLTIP'),
			enableToggle : true,
			toggleGroup : 'mapCtrl',
			allowDepress : false,
			handler : this._updateCtrlBtns,
			scope : this
		});

		// Build toolbar according to component display options
		// localisation AND toolbar
		if (componentDisplayOption.LocalisationEnable && componentDisplayOption.ToolBarEnable) {
			return new Ext.Toolbar( {
				region : "north",
				autoHeight : true,
				items : [ this.previousButton, this.nextButton, {
					xtype : 'tbseparator'
				}, this.zoomInBoxButton, this.zoomOutBoxButton, this.zoomToMaxExtentButton, this.navButton, this.getFeatureButton, {
					xtype : 'tbseparator'
				}, EasySDI_Map.lang.getLocal('1:'), this.zoomToScaleField, {
					xtype : 'tbseparator'
				},
				// this.selectButton,
						this.saveMapButton, this.printMapButton, this.pdfButton, {
							xtype : 'tbfill'
						}, EasySDI_Map.lang.getLocal('MP_ZOOM'), this.locAutocomplete

				]
			});
		}
		// Only toolbar
		if (componentDisplayOption.ToolBarEnable && !componentDisplayOption.LocalisationEnable) {
			return new Ext.Toolbar( {
				region : "north",
				autoHeight : true,
				items : [ this.previousButton, this.nextButton, {
					xtype : 'tbseparator'
				}, this.zoomInBoxButton, this.zoomOutBoxButton, this.zoomToMaxExtentButton, this.navButton, this.getFeatureButton, {
					xtype : 'tbseparator'
				}, EasySDI_Map.lang.getLocal('1:'), this.zoomToScaleField, {
					xtype : 'tbseparator'
				},
				// this.selectButton,
						this.saveMapButton, this.printMapButton ]
			});
		}
		// Only localisation
		if (componentDisplayOption.LocalisationEnable && !componentDisplayOption.ToolBarEnable) {
			return new Ext.Toolbar( {
				region : "north",
				autoHeight : true,
				items : [ {
					xtype : 'tbfill'
				}, EasySDI_Map.lang.getLocal('MP_ZOOM'), this.locAutocomplete ]
			});
		}

	},

	/**
	 * Get the annotation styles description from the params.php and add the
	 * specific attributes used for the dropdown list behaviour
	 */
	_createAnnotationStyleDropDownItems : function() {
		var styleDropDownItems = [];
		var first = true;
		Ext.each(annotationStyle, function(style) {
			var item = style;
			first ? item.checked = true : item.checked = false;
			item.group = 'theme';
			item.checkHandler = this._onAnnotationStyleSelect;
			item.scope = this;
			styleDropDownItems.push(item);
			if (first) {
				this.defaultAnnotationStyle = item;
			}
			first = false;
		}, this);

		return styleDropDownItems;
	},

	/**
	 * Init annotation toolbar and When select an other annotation style, update
	 * the layer vector style
	 */
	_onAnnotationStyleSelect : function(item, checked) {
		// First call : initialization of the annotation toolbar
		if (!item) {
			// Select the first style by default
			item = this.defaultAnnotationStyle;
		} else {
			// It is not the first call, so
			// disable toolBar buttons and deactivate openlayers
			// controls
			this.rectangleButton.toggle(false);
			this.polygonButton.toggle(false);
			this.pointButton.toggle(false);
			this.modifyFeatureButton.toggle(false);
			this.pathButton.toggle(false);
			this.selectFeatureButton.toggle(false);
			this.printMapButton.toggle(false);

			this.rectControl.deactivate();
			this.polyControl.deactivate();
			this.pointControl.deactivate();
			this.modifyFeatureControl.deactivate();
			this.pathControl.deactivate();
			this.selectControl.deactivate();
		}

		// The item passed in parameter is the checked one
		if (checked) {
			if (!item)
				return;
			this.template = {
				pointRadius : item.pointRadius,
				fillColor : item.fillColor,
				fillOpacity : item.fillOpacity,
				strokeColor : item.strokeColor,
				strokeWidth : item.strokeWidth,
				strokeOpacity : item.strokeOpacity,
				stroke : item.stroke,
				fill : item.fill,
				externalGraphic : item.externalGraphic,
				graphicWidth : item.graphicWidth,
				graphicHeight : item.graphicHeight,
				graphicYOffset : item.graphicYOffset,
				graphicXOffset : item.graphicXOffset,
				graphicOpacity : item.graphicOpacity

			};

			var style = {
				"default" : new OpenLayers.Style(this.template)
			};

			// Each style will be affected to one vector layer.
			// Get the layer corresponding to the style
			var exists = false;
			for (i = 0; i < this.map.layers.length; i++) {
				if (this.map.layers[i].name === item.text) {
					this.vectors = this.map.layers[i];
					this.vectors.styleMap = new OpenLayers.StyleMap(style);
					exists = true;
					break;
				}
			}
			// Or create it if does not exist
			if (!exists) {
				this.vectors = new OpenLayers.Layer.Vector(item.text, {
					isBaseLayer : false,
					transparent : "true",
					styleMap : new OpenLayers.StyleMap(style)
				});
				this.vectors.events.register("featureadded", this, function(myEvent) {
					this.refreshLegend();
				});
				this.vectors.events.register("featureremoved", this, function(myEvent) {
					this.refreshLegend();
				});
				this.map.addLayer(this.vectors);
				if (!this.vectorsLayersArray) {
					this.vectorsLayersArray = [];
				}
				this.vectorsLayersArray.push(this.vectors);
			}

			// Define the openlayers controls used for annotation
			if (!this.rectControl) {
				// Create the control
				this.rectControl = new OpenLayers.Control.DrawFeature(this.vectors, OpenLayers.Handler.RegularPolygon);
				this.rectControl.handler.setOptions( {
					irregular : true
				});
				this.map.addControl(this.rectControl);
			} else {
				// Update the control that it can point to the right
				// layer
				this.rectControl.layer = this.vectors;
			}
			if (!this.polyControl) {
				this.polyControl = new OpenLayers.Control.DrawFeature(this.vectors, OpenLayers.Handler.Polygon);
				this.map.addControl(this.polyControl);
			} else {
				this.polyControl.layer = this.vectors;
			}
			if (!this.pointControl) {
				this.pointControl = new OpenLayers.Control.DrawFeature(this.vectors, OpenLayers.Handler.Point);
				this.map.addControl(this.pointControl);
			} else {
				this.pointControl.layer = this.vectors;
			}
			if (this.modifyFeatureControl) {
				this.modifyFeatureControl.destroy();
			}
			this.modifyFeatureControl = new OpenLayers.Control.ModifyFeature(this.vectors);
			this.map.addControl(this.modifyFeatureControl);

			if (!this.pathControl) {
				this.pathControl = new OpenLayers.Control.DrawFeature(this.vectors, OpenLayers.Handler.Path);
				this.map.addControl(this.pathControl);
			} else {
				this.pathControl.layer = this.vectors;
			}
			if (!this.selectControl) {
				this.selectControl = new OpenLayers.Control.SelectFeature(this.vectorsLayersArray, {
					box : true,
					onSelect : function(feature) {
						feature.destroy();
					}
				});
				this.map.addControl(this.selectControl);
			} else {
				this.selectControl.layers = this.vectorsLayersArray;
			}
		}
	},

	/**
	 * When a control button pressed state changes, update the underlying OL
	 * controls.
	 */
	_updateCtrlBtns : function() {
		if (this.navButton.pressed) {
			this.navCtrl.activate();
			// this.getFeatureInfoCtrl.activate();
		} else {
			this.navCtrl.deactivate();
			// this.getFeatureInfoCtrl.deactivate();
		}
		if (this.selectButton.pressed) {
			this.selectFeatureCtrl.activate();
		} else {
			this.selectFeatureCtrl.deactivate();
		}
		if (this.zoomInBoxButton.pressed) {
			this.zoomInBoxCtrl.activate();
		} else {
			this.zoomInBoxCtrl.deactivate();
		}
		if (this.zoomOutBoxButton.pressed) {
			this.zoomOutBoxCtrl.activate();
		} else {
			this.zoomOutBoxCtrl.deactivate();
		}
		if (this.printMapButton.pressed) {
			window.open(componentParams.componentUrl + '&view=printMap&mapPanel=' + this._encodeCurrentMap() + '&mapPanelHeight='
					+ this.map.getCurrentSize().h + '&mapPanelWidth=' + this.map.getCurrentSize().w, '_blank');
			this.printMapButton.toggle(false);
		}
		if (this.saveMapButton.pressed) {
			var popup = new EasySDI_Map.Dlg.SaveAsPopup( {
				mapPanel : this
			});
			popup.show();
			// window.open(componentParams.proxyURL.asString+"&url=" +
			// this._getOneImageMapURL());
			this.saveMapButton.toggle(false);
		}
		if (this.pdfButton.pressed) {
			var popup = new EasySDI_Map.Dlg.InputPDFTitle( {
				mapPanel : this
			});
			popup.show();
			this.pdfButton.toggle(false);
		}
		if (componentDisplayOption.AnnotationEnable) {
			if (this.rectangleButton.pressed) {
				this.rectControl.activate();
			} else {
				this.rectControl.deactivate();
			}
			if (this.polygonButton.pressed) {
				this.polyControl.activate();
			} else {
				this.polyControl.deactivate();
			}
			if (this.pointButton.pressed) {
				this.pointControl.activate();
			} else {
				this.pointControl.deactivate();
			}
			if (this.modifyFeatureButton.pressed) {
				this.modifyFeatureControl.activate();
			} else {
				this.modifyFeatureControl.deactivate();
			}
			if (this.pathButton.pressed) {
				this.pathControl.activate();
			} else {
				this.pathControl.deactivate();
			}
			if (this.selectFeatureButton.pressed) {
				this.selectControl.activate();
			} else {
				this.selectControl.deactivate();
			}
		}
		if (this.getFeatureButton.pressed) {
			this.getFeatureCtrl.activate();
		} else {
			this.getFeatureCtrl.deactivate();
		}
	},

	/**
	 * Encode the current map to send its definition as a string to the printMap
	 * view. Definition of the current map will contain : - current extent -
	 * visible base map - visible overlays - annotations - user defined styles
	 * for overlays
	 * 
	 * NB : a problem(bug?) in JSON.stringify and Ext.util.JSON.encode does not
	 * allow to add feature geometries as an object array to the object 'Layer'.
	 * Feature geometries are added as a unique object (string concatenation of
	 * all features)
	 */
	_encodeCurrentMap : function() {
		var currentMap = {};
		currentMap.extent = this.map.getExtent();
		// currentMap.zoom = this.map.getZoom();
		currentMap.basemap = this.map.baseLayer.params.LAYERS;
		currentMap.basemapVisible = this.map.baseLayer.getVisibility();

		var currentLayers = [];
		var count = 0;
		for (i = 0; i < this.map.layers.length; i++) {
			if (this.map.layers[i].getVisibility()) {
				var l = this.map.layers[i];
				var currentLayer = {}
				currentLayer.name = (l.CLASS_NAME == 'OpenLayers.Layer.Vector') ? l.name : l.params.LAYERS;
				var geometries = '';
				// var currentFeatures =[];
				var featGeom = {};
				if (l.isVector) {
					for (j = 0; j < l.features.length; j++) {
						var feature = l.features[j];
						featGeom.value = feature.geometry;
						;
						geometries += feature.geometry;
						if (j < l.features.length - 1) {
							geometries += ";";
						}
						// currentFeatures[j] = featGeom;
					}
					currentLayer.features = geometries;
					// currentLayer.features = currentFeatures;
				}
				currentLayers[count] = currentLayer;
				count += 1;
			}
		}
		currentMap.layers = currentLayers;

		var styles = Ext.state.Manager.get('overlayLayerStyle', null);
		currentMap.style = styles;
		// alert (JSON.stringify(currentMap));
		// return Ext.util.JSON.encode(currentMap);
		return JSON.stringify(currentMap)
	},

	/**
	 * Build the URL to get an non-tiled image containing all the visible layers
	 * of the current map The purpose is to allow to save as an image the
	 * current map.
	 */
	_getOneImageMapURL : function(img_format) {
		var styles = Ext.state.Manager.get('overlayLayerStyle', null);
		var url = '';
		url += componentParams.pubWmsUrl;
		url += "?LAYERS=";
		var found = false;
		Ext.each(this.map.layers, function(layer) {
			if (layer.inRange && layer.getVisibility() && layer.CLASS_NAME == "OpenLayers.Layer.WMS")
				url += layer.params.LAYERS + ",";
			found = true;
		}, this);

		if (found) {
			url = url.substr(0, url.length - 1);
		}

		url += "&VERSION="+componentParams.pubWmsVersion+"&SRS=" + componentParams.projection + "&FORMAT=" + img_format
				+ "&SERVICE=WMS&REQUEST=GetMap&EXCEPTIONS=application%2Fvnd.ogc.se_inimage&BBOX=";
		url += this.map.getExtent().left + "," + this.map.getExtent().bottom + "," + this.map.getExtent().right + ","
				+ this.map.getExtent().top;
		url += "&WIDTH=" + this.GeoExtMapPanel.getSize().width + "&HEIGHT=" + this.GeoExtMapPanel.getSize().height;

		var sld = "";
		var first = true;
		if (styles) {
			for (i = 0; i < this.map.layers.length; i++) {
				if (this.map.layers[i].getVisibility()) {
					Ext.each(SData.overlayLayers, function(layer) {
						if (layer.name == this.map.layers[i].name) {
							if (styles && styles[layer.id]) {
								if (first) {
									url += "&SLD_BODY=";
									sld += EasySDI_Map.StyledLayerDescriptor.getSLDHeader();
									first = false;
								}
								sld += EasySDI_Map.StyledLayerDescriptor.getSLD(layer.layers, styles[layer.id].fillColor,
										styles[layer.id].opacity, styles[layer.id].strokeColor);
								// alert(sld);
						}
						/*
						 * else if(layer.defaultOpacity && layer.defaultOpacity !=
						 * 1) { if (first) { url += "&SLD_BODY="; sld +=
						 * EasySDI_Map.StyledLayerDescriptor.getSLDHeader();
						 * first = false; } //alert('layer '+ layer.name+ ' as
						 * default opacty : '+ layer.defaultOpacity); sld +=
						 * EasySDI_Map.StyledLayerDescriptor.getSLD(layer.layers,'666000',layer.defaultOpacity,'000666');
						 * //alert(sld); }
						 */
					}
				}, this);
					/*
					 * Ext.each(SData.baseLayers, function (layer){
					 * if(layer.name == this.map.layers[i].name) {
					 * if(layer.defaultOpacity && layer.defaultOpacity != 1) {
					 * if (first) { url += "&SLD_BODY="; sld +=
					 * EasySDI_Map.StyledLayerDescriptor.getSLDHeader(); first =
					 * false; } //alert('layer '+ layer.name+ ' as default
					 * opacty : '+ layer.defaultOpacity); sld +=
					 * EasySDI_Map.StyledLayerDescriptor.getSLD(layer.layers,'',layer.defaultOpacity,''); } } },
					 * this);
					 */
				}
			}
			if (!first) {
				sld += EasySDI_Map.StyledLayerDescriptor.getSLDFooter();
				sld = encodeURIComponent(sld);
			}
		}
		url += sld;
		// alert (url);
		return url;
	},

	_zoomToScale : function(scale) {
		this.map.zoomToScale(scale);
	},

	_zoomToMaxExtent : function() {
		this.map.zoomToExtent(SData.baseMap.maxExtent);
	},

	/**
	 * Handler for when the projection is changed in the toolbar for the
	 * mousepos control.
	 */
	onProjChecked : function(item) {
		if (item.checked) {
			this.projMenuButton.setText(item.text);
			this.mousePos.displayProjection = new OpenLayers.Projection(componentParams.displayProjections[item.stateId].name);
			this.mousePos.numDigits = componentParams.displayProjections[item.stateId].numDigits;
		}
	}

});

Ext.mixin(EasySDI_Map.MapPanel, EasySDI_Map.TriggerManager);