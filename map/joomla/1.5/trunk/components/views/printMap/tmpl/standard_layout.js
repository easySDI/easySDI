/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community For more information : www.easysdi.org
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

// Localisation prefix 02
Ext.namespace("EasySDI_Map");

/**
 * Standard layout class for the print map page.
 * 
 * The purpose of this page is to give a way to print a view of the map with
 * only : - base map - visible layers - all annotations - graphic scale - no
 * additional panel, just the MapPanel This replace the functionality of saving
 * the map as an image (jpg, png).
 */
EasySDI_Map.printMapLayout = Ext
		.extend(
				Ext.Viewport,
				{
					constructor : function(config) {
						// this contains all the map elements description
						// (layers, features, styles)
						this.mapDescriber = JSON.parse(completeMap);

						var options = {
							projection : SData.baseMap.projection,
							// resolutions:
							// JSON.parse(componentParams.resolutions),
							units : SData.baseMap.units,

							controls : []
						};

						if (SData.baseMap.unit != undefined)
							options.units = SData.baseMap.unit;
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

						this.map = new OpenLayers.Map(options);

						/*
						 * var gphy = new OpenLayers.Layer.Google( "Google
						 * Physical", {type: G_PHYSICAL_MAP} ); var gmap = new
						 * OpenLayers.Layer.Google( "Google Streets", // the
						 * default {numZoomLevels: 20} ); var ghyb = new
						 * OpenLayers.Layer.Google( "Google Hybrid", {type:
						 * G_HYBRID_MAP, numZoomLevels: 20} ); var gsat = new
						 * OpenLayers.Layer.Google( "Google Satellite", {type:
						 * G_SATELLITE_MAP, numZoomLevels: 20} );
						 * 
						 * 
						 * this.map.addLayers([gphy, gmap, ghyb, gsat]);
						 */

						// Add all the layers
						Ext.each(SData.baseLayers, this._addBaseLayer, this);
						this.map.zoomToExtent(new OpenLayers.Bounds(this.mapDescriber.extent.left, this.mapDescriber.extent.bottom,
								this.mapDescriber.extent.right, this.mapDescriber.extent.top));
						Ext.each(SData.overlayLayers, this._addLayers, this);
						Ext.each(annotationStyle, this._addAnnotations, this);

						// Define the mapPanel
						// Give it the size of the mapPanel displaying in the
						// rwgLayout (main map component)
						// TODO --> update the renderTo definition
						this.mapPanel = new GeoExt.MapPanel( {
							renderTo : 'map',
							height : mapPanelHeight,
							width : mapPanelWidth,
							map : this.map,
							zoom : this.mapDescriber.zoom
						/*
						 * , title: 'A Simple GeoExt Map'
						 */
						});

						// Add a scale line
						this.map.addControl(new OpenLayers.Control.ScaleLine());

						// Zoom to the Extent giving in parameter
						// this.map.zoomToExtent(new
						// OpenLayers.Bounds(this.mapDescriber.extent.left,this.mapDescriber.extent.bottom,this.mapDescriber.extent.right,this.mapDescriber.extent.top));
						// alert(this.map.getExtent());
						EasySDI_Map.printMapLayout.superclass.constructor.apply(this, arguments);
					},

					/*
					 * Add the base layer
					 */
					_addBaseLayer : function(layer) {
						if (this.mapDescriber.basemap == layer.layers) {
							var WMSoptions = {
								LAYERS : layer.layers,
								SERVICE : layer.url_type,
								VERSION : "1.1.0",
								STYLES : '',
								SRS : layer.projection,
								FORMAT : layer.imageFormat
							};

							var l = new OpenLayers.Layer.WMS(layer.name, layer.url, WMSoptions, {
								maxExtent : layer.maxExtent,
								// minScale: layer.minScale,
								// maxScale: layer.maxScale,
								// units: layer.units,
								isBaseLayer : true,
								singleTile : layer.singletile,
								opacity : layer.defaultOpacity
							});
							this.map.addLayer(l);
							l.setVisibility(this.mapDescriber.basemapVisible);
						}
					},

					/*
					 * Add overlays
					 */
					_addLayers : function(layer) {
						Ext.each(this.mapDescriber.layers, function(layerToAdd) {
							if (layerToAdd.name == layer.layers) {
								var l = '';
								switch (layer.url_type.toUpperCase()) {

								case 'WMS':
									var WMSoptions = {
										LAYERS : layer.layers,
										SERVICE : layer.url_type,
										VERSION : "1.1.0",
										STYLES : '',
										SRS : layer.projection,
										FORMAT : layer.imageFormat,
										TRANSPARENT : true
									};
									l = new OpenLayers.Layer.WMS(layer.name, layer.url, WMSoptions, {
										// maxExtent:new
										// OpenLayers.Bounds(layer.maxExtent.left,layer.maxExtent.bottom,layer.maxExtent.right,layer.maxExtent.top),
										// minScale: layer.minScale,
										// maxScale: layer.maxScale,
										// units: layer.units,
										isBaseLayer : false,
										singleTile : layer.singletile,
										opacity : layer.defaultOpacity
									});

									// A style has been locally defined by the
									// user : apply it to the layer
								if (this.mapDescriber.style && this.mapDescriber.style[layer.id]) {
									// var sld =
									// this._getSLD(layer.layers,this.mapDescriber.style[layer.id].fillColor,this.mapDescriber.style[layer.id].opacity,this.mapDescriber.style[layer.id].strokeColor
									// );
								sld = EasySDI_Map.StyledLayerDescriptor.getSLDHeader();
								sld += EasySDI_Map.StyledLayerDescriptor.getSLD(layer.layers, this.mapDescriber.style[layer.id].fillColor,
										this.mapDescriber.style[layer.id].opacity, this.mapDescriber.style[layer.id].strokeColor);
								sld += EasySDI_Map.StyledLayerDescriptor.getSLDFooter();
								l.mergeNewParams( {
									SLD_BODY : sld
								});
							}
							;

							this.map.addLayer(l);
							break;

						case 'WFS':
							l = new OpenLayers.Layer.WFS(layer.name, layer.url, {
								typename : layer.layers
							});
							this.map.addLayer(l);

							// A style has been locally defined by the user :
							// apply it to the layer
							if (this.mapDescriber.style[layer.id]) {
								var style = new OpenLayers.Style( {
									strokeColor : "#" + this.mapDescriber.style[layer.id].strokeColor,
									strokeWidth : 1,
									fillColor : "#" + this.mapDescriber.style[layer.id].fillColor,
									fillOpacity : this.mapDescriber.style[layer.id].opacity
								});
								l.styleMap = new OpenLayers.StyleMap( {
									"default" : style
								});
								l.refresh();
							}
							break;
						}
					}
				}, this	);
					},

					/*
					 * Add annotations layer with features
					 */
					_addAnnotations : function(layer) {
						Ext.each(this.mapDescriber.layers, function(layerToAdd) {
							if (layerToAdd.name == layer.text) {
								// Get the layer's style defined in the params
								var template = {
									pointRadius : layer.pointRadius,
									fillColor : layer.fillColor,
									fillOpacity : layer.fillOpacity,
									strokeColor : layer.strokeColor,
									strokeWidth : layer.strokeWidth,
									strokeOpacity : layer.strokeOpacity,
									stroke : layer.stroke,
									fill : layer.fill,
									externalGraphic : layer.externalGraphic,
									graphicWidth : layer.graphicWidth,
									graphicHeight : layer.graphicHeight,
									graphicYOffset : layer.graphicYOffset,
									graphicXOffset : layer.graphicXOffset,
									graphicOpacity : layer.graphicOpacity
								};

								// Create the vector layer that will contain
								// annotations
								var style = {
									"default" : new OpenLayers.Style(template)
								};
								var vectors = new OpenLayers.Layer.Vector(layer.text, {
									isBaseLayer : false,
									transparent : "true",
									styleMap : new OpenLayers.StyleMap(style)
								});

								// Add features (=annotations) to the layer.
								// The features are send to this view as a
								// string :
								// {POLYGON((x y,x y,x y,x y));POINT(x
								// y);LINESTRING(x y,x y,x y)}
								// Each feature definition needs to be extracted
								// from this string and
								// an OpenLayers.Geometry created on this base.
								if (layerToAdd.features) {
									var featuresDescriber = layerToAdd.features;
									var featuresArray = [];
									var end = false;
									while (end == false) {
										var index = featuresDescriber.indexOf(";", 0);
										if (index == -1) {
											end = true;
											featuresArray.push(this._createVectorFeature(featuresDescriber));
										} else {
											var featureDescriber = featuresDescriber.substr(0, index);
											featuresArray.push(this._createVectorFeature(featureDescriber));
											featuresDescriber = featuresDescriber.substr(index + 1);
										}
									}
									vectors.addFeatures(featuresArray);
								}
								this.map.addLayer(vectors);
							}
						}, this);
					},

					/*
					 * Create vector feature by parsing the string definition
					 */
					_createVectorFeature : function(feature) {
						var index = feature.indexOf("(", 0);
						var featureType = feature.substr(0, index);
						feature = feature.substr(index + 1);
						switch (featureType.toUpperCase()) {
						case 'POLYGON':
							var newLinearRingComponents = new Array();
							var start = feature.indexOf("(", 0);
							var geometry = feature.substr(start + 1, feature.length - 2);
							var end = false;
							while (end == false) {
								start = geometry.indexOf(",");
								if (start == -1) {
									end = true;
									var middle = geometry.indexOf(" ");
									var x = geometry.substr(0, middle);
									var y = geometry.substr(middle + 1, geometry.length - middle - 1);
									newLinearRingComponents.push(new OpenLayers.Geometry.Point(x, y));
								} else {
									var middle = geometry.indexOf(" ");
									var x = geometry.substr(0, middle);
									var y = geometry.substr(middle + 1, start - middle - 1);
									geometry = geometry.substr(start + 1);
									newLinearRingComponents.push(new OpenLayers.Geometry.Point(x, y));
								}
							}
							var newLinearRing = new OpenLayers.Geometry.LinearRing(newLinearRingComponents);
							var polygon = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon( [ newLinearRing ]));
							return polygon;
							break;
						case 'POINT':
							var middle = feature.indexOf(" ");
							var x = feature.substr(0, middle);
							var y = feature.substr(middle + 1, feature.length - middle - 2);
							var point = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(x, y));
							return point;
							break;
						case 'LINESTRING':
							var points = new Array();

							var end = false;
							while (end == false) {
								comma = feature.indexOf(",");
								if (comma == -1) {
									end = true;
									var middle = feature.indexOf(" ");
									var x = feature.substr(0, middle);
									var y = feature.substr(middle + 1, feature.length - middle - 1);
									points.push(new OpenLayers.Geometry.Point(x, y));
								} else {
									var middle = feature.indexOf(" ");
									var x = feature.substr(0, middle);
									var y = feature.substr(middle + 1, comma - middle - 1);
									feature = feature.substr(comma + 1);
									points.push(new OpenLayers.Geometry.Point(x, y));
								}
							}
							var lineString = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(points));
							return lineString;
							break;
						}
					},

					/**
					 * Build an SLD string from the fill, stroke and opacity.
					 */
					_getSLD : function(layer, fillColor, opacity, strokeColor) {
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
												+ '    </UserStyle>' + '  </NamedLayer>' + '</StyledLayerDescriptor>', layer, fillColor,
										opacity, strokeColor);
					}
				});