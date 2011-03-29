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

// Localisation Prefix SM
/**
 * Class to co-ordinate the search bar with the WFS layers, grid tabs and
 * feature select controls it is associated with.
 */

Ext.namespace("EasySDI_Map");

EasySDI_Map.SearchManager = function(searchPanel, viewPort, mapPanel,
		precisionTree) {
	// Create an array to hold each of the currently available searchBars.
	this.searchPanel = searchPanel;
	this.viewPort = viewPort;
	this.mapPanel = mapPanel;
	this.precisionTree = precisionTree;
	this.searchBars = [];
};

/**
 * Triggerable method that performs a search. Passes the filter to the map to
 * add a WMS layer. Options should include the search bar and optionally an
 * override for public/private access called switchAccess.
 */
EasySDI_Map.SearchManager.prototype.doSearch = function(options) {
	Ext.Msg.show( {
		title : EasySDI_Map.lang.getLocal('Please_Wait') + '...',
		msg : EasySDI_Map.lang.getLocal('SM_LOADING_MAP'),
		icon : 'msg-wait'
	});
	this._cleanup(options.searchBar, true, true);
	var featureType = SData.searchLayer.featureType;
	if (typeof options.switchAccess != "undefined") {
		featureType = featureType.replace(/private|public/,
				options.switchAccess);
		options.searchBar.switchAccess = options.switchAccess;
	} else {
		options.switchAccess = undefined;
	}

	// Check component option for run search
	if (componentParams.WMSFilterSupport == 'true') {
		// Option 1 : call WMS
		var layers = this.mapPanel.addWmsLayerSet(featureType,
				options.searchBar.filter, options.searchBar.layerStyle,
				options.searchBar.barIdx);
		options.searchBar.wfsLoaded = false;
		options.searchBar.layers = layers;
		if (!rwg.gridPanel.collapsed) {
			this.enableWfsSearch(options.searchBar);
		} else {
			this.trigger('refreshLegend');
		}
	} else {
		// Option 2 : call WFS
		var layers = [];
		options.searchBar.wfsLoaded = false;
		options.searchBar.layers = layers;
		this.enableWfsSearch(options.searchBar);
	}

};

/**
 * Triggerable method that starts a WFS search. This can either be for the
 * entire set of search bars (e.g. when expanding the grid for the first time),
 * or if searchBar is specified then it will just load the one grid.
 */
EasySDI_Map.SearchManager.prototype.enableWfsSearch = function(searchBar) {
	// expanding the grid panel so all search bars must have their wfs loaded.
	if (typeof searchBar == "undefined") {
		Ext.each(this.searchBars, function(item) {
			// We only want to do this the first time the grid panel is expanded
			// for any search bar's query.
				this.runSingleWfsSearch(item.searchBar);
			}, this);
	} else {
		// Just doing a search on one search bar
		this.runSingleWfsSearch(searchBar);
	}
	this.trigger('refreshLegend');
};

/**
 * Runs a single Wfs search for a search bar, assuming the search bar has a
 * search to run. Also displays the loading message.
 */
EasySDI_Map.SearchManager.prototype.runSingleWfsSearch = function(searchBar) {
	// if WMSFilterSupport is set to 'false', searchBar.layers will be empty so
	// disable this test :
	// if (searchBar.layers.length>0) {
	Ext.Msg.show( {
		title : EasySDI_Map.lang.getLocal('Please_Wait') + '...',
		msg : EasySDI_Map.lang.getLocal('SM_LOADING_SEARCH'),
		icon : 'msg-wait'
	});
	this.doWfsSearch(searchBar);
	// }
};

/**
 * Load the search for a specific search bar as a WFS query - adding the layer
 * to the map and also to the grid panel.
 */
EasySDI_Map.SearchManager.prototype.doWfsSearch = function(searchBar) {
	if (SData.searchLayer) {
		var propertyNames = this
				._getPropertyNames(SData.searchLayer.featureType + 'Cols');
		if (searchBar.wfsLoaded) {
			this._cleanup(searchBar, false, true);
		}
		var protocol, layers, filter;
		var featureType = SData.searchLayer.featureType.replace('{geom}', '');
		if (typeof searchBar.switchAccess != "undefined") {
			featureType = featureType.replace(/private|public/,
					searchBar.switchAccess);
		}
		filter = searchBar.filter.clone();

		// Allow empty searchbar
		if (!filter.filters) {
			filter.filters = [];
		}
		filter.filters.push(new OpenLayers.Filter.Spatial( {
			type : OpenLayers.Filter.Spatial.BBOX,
			property : SData.searchLayer.geometryName,
			value : rwg.mapPanel.map.getExtent()
		}));

		protocol = new OpenLayers.Protocol.WFS.Custom( {
			url : componentParams.proxiedPubWfsUrl,
			featureNS : componentParams.pubFeatureNS,
			featurePrefix : componentParams.pubFeaturePrefix,
			featureType : featureType,
			filter : filter,
			srsName : componentParams.projection,
			version : componentParams.pubWfsVersion,
			propertyNames : propertyNames,
			maxFeatures : componentParams.maxFeatures,
			format : new OpenLayers.Format.WFST.Custom( {
				featureType : featureType,
				featureNS : componentParams.pubFeatureNS,
				featurePrefix : componentParams.pubFeaturePrefix,
				geometryName : SData.searchLayer.geometryName,
				srsName : componentParams.projection
			})
		});
		layers = this.mapPanel.addWfsFeatureLayerSet(protocol);
		searchBar.layers = searchBar.layers.concat(layers);
		this.viewPort.gridPanel.addSearch(protocol, layers,
				this.mapPanel.selectFeatureCtrl, searchBar);
		searchBar.wfsLoaded = true;
	}
};

/**
 * Returns a list of the properties that need to be included in a WFS request to
 * populate the feature results grid, including the geometries for each search
 * precision.
 */
EasySDI_Map.SearchManager.prototype._getPropertyNames = function(stateName) {
	var props = [];
	// First get the geometries we might need
	for ( var geom in SData.searchPrecisions) {
		var p = SData.searchPrecisions[geom];
		if (p.required) {
			props.push(geom);
		}
	}
	if (props.length === 0) {
		// No data precision selected, so add the geometry from the feature type
		// define in the search layer
		props.push(SData.searchLayer.geometryName);
	}
	// always include the primary key
	props.push(componentParams.featureIdAttribute);

	// Now the grid attributes
	var visibleCols = Ext.state.Manager.get(stateName, null);
	if (visibleCols === null) {
		visibleCols = SData.defaultAttrs[SData.searchLayer.featureType.replace(
				'{geom}', '')];
	}
	Ext
			.each(
					SData.attrs[SData.searchLayer.featureType.replace('{geom}',
							'')],
					function(attr) {
						// Note, invisible attrs should always be requested as
						// they are normally keys
						if ((visibleCols.indexOf(attr.name) != -1 || attr.visible === false)
								&& attr.name != componentParams.featureIdAttribute) {
							props.push(attr.name);
						}
					});
	return props;
};

/**
 * Cleanup a search bar layer set. Removes the grids and layers.
 */
EasySDI_Map.SearchManager.prototype._cleanup = function(searchBar, removeWms,
		removeWfs) {
	if (typeof removeWms == "undefined") {
		removeWms = true;
	}
	if (typeof removeWfs == "undefined") {
		removeWfs = true;
	}
	if (removeWfs) {
		this._killGrids(searchBar);
	}
	this.mapPanel.removeLayers(searchBar.layers, removeWms, removeWfs);
	for ( var i = searchBar.layers.length - 1; i >= 0; i--) {
		if ((removeWms && searchBar.layers[i].CLASS_NAME == "OpenLayers.Layer.WMS")
				|| (removeWfs && searchBar.layers[i].CLASS_NAME == "OpenLayers.Layer.Vector")) {
			searchBar.layers.splice(i, 1);
		}
	}
};

/**
 * Clean up the grids associated with a search bar
 */
EasySDI_Map.SearchManager.prototype._killGrids = function(searchBar) {
	if (this.viewPort.gridPanel.tabPanel !== null) {
		// Kill the distinct grids first so they don't activate and load their
		// data unnecessarily.
		Ext.each(searchBar.distinctGrids, function(grid) {
			this.viewPort.gridPanel.tabPanel.remove(grid);
			grid.destroy();
		}, this);
		searchBar.distinctGrids = [];
		if (searchBar.featureGrid !== null) {
			this.viewPort.gridPanel.tabPanel.remove(searchBar.featureGrid);
			searchBar.featureGrid.destroy();
			searchBar.featureGrid = null;
		}
		if (searchBar.selectionGrid !== null) {
			this.viewPort.gridPanel.tabPanel.remove(searchBar.selectionGrid);
			searchBar.selectionGrid.destroy();
			searchBar.selectionGrid = null;
		}
		// If there are no tabs left, remove the entire tab panel. Otherwise
		// there seems to be an Ext bug
		// when a tab panel is emptied completely then filled again - the grids
		// appear blank.
		var gridPanel = this.viewPort.gridPanel;
		if (gridPanel.tabPanel.items.getCount() === 0) {
			gridPanel.remove(gridPanel.tabPanel);
			gridPanel.tabPanel.destroy();
			gridPanel.tabPanel = null;
		}
	}
};

/**
 * Create a search bar inside the search panel and register it with the search
 * manager. param idx - Position of the bar to add, default to adding to the
 * end.
 */
EasySDI_Map.SearchManager.prototype.addSearchBar = function(idx) {
	var searchBar = this.searchPanel.getNewSearchBar(this, idx);
	if (idx === null) {
		this.searchBars.push( {
			searchBar : searchBar,
			featureSelector : null
		});
	} else {
		this.searchBars.splice(idx, 0, {
			searchBar : searchBar,
			featureSelector : null
		});
	}
	this.viewPort.doLayout();
	this.setBtnState();
};

/**
 * Remove an existing search bar (unsticking it). Returns the index of the bar
 * that is removed.
 */
EasySDI_Map.SearchManager.prototype.removeSearchBar = function(searchBar) {
	var toDelete; // index of search bar delete
	Ext.each(this.searchBars, function(item, index) {
		if (item.searchBar === searchBar) {
			// delete it later as in middle of for each.
			toDelete = index;
		}
	});
	// Now actually do the deletion
	searchBar.collapse();
	this.searchPanel.remove(searchBar, true);
	this.searchBars.splice(toDelete, 1);
	this._cleanup(searchBar);
	searchBar.destroy();
	this.setBtnState();
	this.trigger('refreshLegend');
	return toDelete;
};

/**
 * Remove an existing search bar (destroy and replace it)
 */
EasySDI_Map.SearchManager.prototype.clearSearchBar = function(searchBar) {
	var idx = this.removeSearchBar(searchBar);
	this.addSearchBar(idx);
	this.trigger('refreshLegend');
	// Close the filter panel.
	this.trigger('hideFilterPanel');
};

/**
 * Run through the add and remove search buttons, setting the appropriate
 * visibility for each.
 */
EasySDI_Map.SearchManager.prototype.setBtnState = function() {
	Ext
			.each(
					this.searchBars,
					function(item, index) {
						// last row only has Add button
						item.searchBar.addButton
								.setVisible(index == this.searchBars.length - 1
										&& this.searchBars.length < componentParams.maxSearchBars);
						// Remove button present, unless only row
						item.searchBar.removeButton
								.setVisible(this.searchBars.length > 1);
					}, this);
};

Ext.mixin(EasySDI_Map.SearchManager, EasySDI_Map.TriggerManager);