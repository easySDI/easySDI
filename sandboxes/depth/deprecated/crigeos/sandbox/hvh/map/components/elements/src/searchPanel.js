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

// Localisation Prefix 04
Ext.namespace("EasySDI_Map");

EasySDI_Map.InnerSearchBar = Ext.extend(Ext.Panel, {

	/**
	 * Has the WFS details been loaded for this search bar yet?
	 */
	wfsLoaded : false,

	/**
	 * Style to load for the layer
	 */
	layerStyle : '',

	/**
	 * Description of current search
	 */
	searchDesc : null,

	/**
	 * Type of current search
	 */
	searchType : null,

	/**
	 * Was current search spatial?
	 */
	searchSpatial : 0,
	/**
	 * Constructor. Takes the parent panel as a parameter.
	 */
	constructor : function(config, panel) {
		this.panel = panel;

		// Call parent constructor
	EasySDI_Map.SearchPanel.superclass.constructor.apply(this, arguments);

	// Enable according to the component display options stored in the database
	if (!componentDisplayOption.SimpleSearchEnable && !componentDisplayOption.AdvancedSearchEnable) {
		this.hide();
	}
},

initComponent : function() {
	this._selectStyle();

	// Because there are events on some of the stores (and they are 'remote')
	// these stores can
	// not be shared between the search bars. Their creation has therefore been
	// moved from the
	// panel to this function.

	// Setup the search method combo boxes
	var simpleMethodDefaultValue = 0;
	this.simpleMethod = new Ext.form.ComboBox( {
		store : this.panel.searchtypestore,
		displayField : 'item',
		valueField : 'id',
		value : simpleMethodDefaultValue,
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local',
		ctCls : 'vshift',
		width : 100
	});
	this.simpleMethod.addListener('select', this._simpleMethodSelect, this);

	// The simpleFor stores are all remote, so are created for each bar
	// individually. They also have
	// an event on them (to populate the combobox automatically if there is only
	// one entry in the
	// store - if the same store is shared across bars, then all will be set
	// together).
	this.simpleForStores = [];
	var emptySimpleStore = new Ext.data.SimpleStore( {
		fields : [ 'fid', 'name' ],
		data : [ [ 0, EasySDI_Map.lang.getLocal('SP_ERROR_NO_WFS') ] ]
	});

	Ext.each(SData.simpleSearchTypes, function(simpleSearch, i) {
		if (simpleSearch.dropDownFeatureType === '') {
			// feature type not defined.
			this.simpleForStores.push(emptySimpleStore);
		} else {
			var storeOptions = {
				url : componentParams.proxiedPubWfsUrl,
				featureType : simpleSearch.dropDownFeatureType,
				featurePrefix : componentParams.pubFeaturePrefix,
				featureNS : componentParams.pubFeatureNS,
				fields : [ {
					name : simpleSearch.dropDownIdAttr || 'fid',
					type : 'string'
				}, {
					name : simpleSearch.dropDownDisplayAttr,
					type : 'string'
				} ],
				queryField : simpleSearch.dropDownDisplayAttr,
				maxFeatures : componentParams.autocompleteMaxFeat
			};
			this.simpleForStores.push(new EasySDI_Map.WfsStore( {}, storeOptions));
		}
	}, this);

	// This assumes that the default has a valid WFS entry: if this is not the
	// case, then the look up
	// will return blank lines.
	this.simpleFor = new Ext.form.ComboBox( {
		store : this.simpleForStores[simpleMethodDefaultValue],
		valueField : SData.simpleSearchTypes[simpleMethodDefaultValue].dropDownIdAttr || 'fid',
		displayField : SData.simpleSearchTypes[simpleMethodDefaultValue].dropDownDisplayAttr,
		loadingText : EasySDI_Map.lang.getLocal('SP_SEARCHING'),
		width : 150,
		hideTrigger : true,
		// triggerAction: 'all',
		mode : 'remote',
		minChars : componentParams.autocompleteNumChars,
		beforeBlur : Ext.emptyFn
	});
	// When the user types, we need to clear the value from previous searches
	this.simpleFor.oldInitEvents = this.simpleFor.initEvents;
	this.simpleFor.initEvents = function() {
		this.oldInitEvents();
		this.keyNav.doRelay = function(foo, bar, hname) {
			if (hname == 'down' || this.scope.isExpanded()) {
				return Ext.KeyNav.prototype.doRelay.apply(this, arguments);
			} else if (hname == 'enter') {
				this.scope.onTriggerClick();
			}
			return true;
		};
	};
	this.simpleFor.on('beforequery', function() {
		this.value = '';
	});
	this.simpleFor.store.on('load', this._onSimpleForLoad, this.simpleFor);

	// The inplacestore store is remote, so is created for each bar
	// individually. It also has
	// an event on it (to populate the combobox automatically if there is only
	// one entry in the
	// store - if the same store is shared across bars, then all will be set
	// together).
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
			// Note that in the fieldlist the "fid" is a generated feature id
			// comprising the
				// feature type and the id - the id is not available as a
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
						// prefix: parts[1].toLowerCase()},
						prefix : parts[1]
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
		this.inplacestore = new EasySDI_Map.WfsMultiStore( {}, storeObject);
	} else {
		this.inplacestore = new Ext.data.SimpleStore( {
			fields : [ 'ipa_fullid', 'ipa_display_name' ],
			data : [ [ '0', EasySDI_Map.lang.getLocal('SP_ERROR_NO_LOCALISATION') ] ]
		});
	}

	this.inplaceAutocomplete = new Ext.form.ComboBox( {
		store : this.inplacestore,
		valueField : 'ipa_fullid',
		displayField : 'ipa_display_name',
		loadingText : EasySDI_Map.lang.getLocal('SP_SEARCHING'),
		width : 140,
		hideTrigger : true,
		// triggerAction: 'all',
		mode : 'remote',
		// forceSelection: true,
		minChars : componentParams.autocompleteNumChars,
		beforeBlur : Ext.emptyFn
	});
	this.inplaceAutocomplete.oldInitEvents = this.inplaceAutocomplete.initEvents;
	this.inplaceAutocomplete.initEvents = function() {
		this.oldInitEvents();
		this.keyNav.doRelay = function(foo, bar, hname) {
			if (hname == 'down' || this.scope.isExpanded()) {
				return Ext.KeyNav.prototype.doRelay.apply(this, arguments);
			} else if (hname == 'enter') {
				this.scope.onTriggerClick();
			}
			return true;
		};
	};
	// When the user types, we need to clear the value from previous searches
	this.inplaceAutocomplete.on('beforequery', function() {
		this.value = '';
	});
	this.inplaceAutocomplete.store.on('load', function() {
		// The load event actually gets fired once for each of the WFS calls in
		// the multiWFS: ie if there are
			// 3 different calls to WFS services for this WFSmultistore, then
			// the load event will be called once
			// for each call - ie 3 times in total.
			if (this.store.proxy.responses) {
				var complete = true;
				Ext.each(this.store.proxy.responses, function(response) {
					if (response.priv) {
						if (response.priv.readyState != 4) // cXMLHttpRequest.DONE
							complete = false;
					}
				}, this);
				if (complete && this.store.getCount() == 1) {
					var r = this.store.getAt(0);
					this.setValue(r.data[this.valueField]);
				}
			}
		}, this.inplaceAutocomplete);

	// In config error condition, make sure error message is displayed in field
	if (SData.localisationLayers.length === 0) {
		this.inplaceAutocomplete.setValue('0');
	}

	this.simpleGoButton = new Ext.Toolbar.Button( {
		text : EasySDI_Map.lang.getLocal('SP_SIMPLE_SEARCH_GO'),
		cls : 'x-form-toolbar-standardButton'
	});

	this.simpleGoButton.addListener('click', this._simpleGoClick, this);

	// If the search layer is not defined, disable the simpleGoButton to avoid
	// error generation and non sense request
	if (!SData.searchLayer) {
		this.simpleGoButton.disable();
	}

	// Allow an empty dropdown list
	var initial_value = null;
	if (this.panel.methodstore.data.items[0]) {
		initial_value = this.panel.methodstore.data.items[0].data.id;
	}

	this.advancedMethod = new Ext.form.ComboBox( {
		store : this.panel.methodstore,
		displayField : 'method',
		valueField : 'id',
		value : initial_value,
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local',
		ctCls : 'vshift',
		width : 170
	});

	if (initial_value == null) {
		this.advancedMethod.hide();
	}

	var advancedGoButton = new Ext.Toolbar.Button( {
		text : EasySDI_Map.lang.getLocal('SP_ADV_SEARCH_GO'),
		cls : 'x-form-toolbar-standardButton'
	});

	advancedGoButton.addListener('click', this._advancedGoClick, this);

	// If the search layer is not defined, disable the advancedGoButton to avoid
	// error generation and non sense request
	if (!SData.searchLayer) {
		advancedGoButton.disable();
	}

	var loadButton = new Ext.Toolbar.Button( {
		hidden : !componentParams.authorisedTo.SEARCH_SAVE_LOAD || !user.loggedIn,
		xtype : "tbbutton",
		iconCls : "x-btn-icon loadBtn",
		tooltip : EasySDI_Map.lang.getLocal('SP_OPEN_SEARCH_TTIP')
	});

	loadButton.addListener('click', this._loadSearch, this);

	var saveButton = new Ext.Toolbar.Button( {
		hidden : !componentParams.authorisedTo.SEARCH_SAVE_LOAD || !user.loggedIn,
		xtype : "tbbutton",
		iconCls : "x-btn-icon saveBtn",
		tooltip : EasySDI_Map.lang.getLocal('SP_SAVE_SEARCH_TTIP')
	});

	saveButton.addListener('click', this._saveSearch, this);

	this.removeButton = new Ext.Toolbar.Button( {
		iconCls : "x-btn-icon removeSearchBtn",
		tooltip : EasySDI_Map.lang.getLocal('SP_REMOVE_SEARCH')
	});

	// Setup a handler for the button to remove searchbar rows
	this.removeButton.addListener('click', function() {
		this.panel.trigger('removeSearchBar', this);
		this.panel.ownerCt.doLayout();
	}, this);

	var clearButton = new Ext.Toolbar.Button( {
		iconCls : "x-btn-icon clearSearchBtn",
		tooltip : EasySDI_Map.lang.getLocal('SP_CLEAR_SEARCH')
	});

	// Handler to reset the search
	clearButton.addListener('click', function() {
		this.panel.trigger('clearSearchBar', this);
		this.panel.ownerCt.doLayout();
	}, this);

	this.addButton = new Ext.Toolbar.Button( {
		iconCls : "x-btn-icon addSearchBtn",
		tooltip : EasySDI_Map.lang.getLocal('SP_ADD_SEARCH')
	});

	// Handler to add a new search bar row
	this.addButton.addListener('click', function() {
		this.panel.trigger('addSearchBar', null);
		this.panel.ownerCt.doLayout();
	}, this);

	var addRemoveBar = new Ext.Toolbar( {
		region : "east",
		height : 35,
		width : 70,
		items : [ clearButton, this.removeButton, this.addButton ]
	});

	var spacer = new Ext.Panel( {
		border : false,
		width : 100
	});

	// Enable according to the component display options stored in the database
	var defConfig;
	if (componentDisplayOption.SimpleSearchEnable && componentDisplayOption.AdvancedSearchEnable) {
		defConfig = {
			border : false,
			layout : "border",
			height : 30,
			items : [
					{
						height : 30,
						xtype : "toolbar",
						region : "center",

						items : [ (this.barIdx + 1).toString(), {
							xtype : 'tbseparator'
						}, {
							xtype : 'box',
							autoEl : {
								tag : 'img',
								src : 'templates/easysdi_map/icons/silk/find.png'
							}
						}, EasySDI_Map.lang.getLocal('SP_SEARCH'), this.simpleMethod, EasySDI_Map.lang.getLocal('SP_FOR'), this.simpleFor,
								EasySDI_Map.lang.getLocal('SP_IN_PLACE'), this.inplaceAutocomplete, ' ', this.simpleGoButton, spacer, {
									xtype : 'box',
									autoEl : {
										tag : 'img',
										src : 'templates/easysdi_map/icons/silk/find.png'
									}
								}, EasySDI_Map.lang.getLocal('SP_BUILD_ADV_SEARCH'), this.advancedMethod, ' ', advancedGoButton,
								loadButton, saveButton, {
									xtype : 'tbspacer'
								}, {
									xtype : 'tbseparator'
								} ]
					}, addRemoveBar ]
		};
	}
	;
	if (!componentDisplayOption.SimpleSearchEnable && componentDisplayOption.AdvancedSearchEnable) {
		defConfig = {
			border : false,
			layout : "border",
			height : 30,
			items : [ {
				height : 30,
				xtype : "toolbar",
				region : "center",

				items : [ (this.barIdx + 1).toString(), {
					xtype : 'tbseparator'
				}, {
					xtype : 'box',
					autoEl : {
						tag : 'img',
						src : 'templates/easysdi_map/icons/silk/find.png'
					}
				}, EasySDI_Map.lang.getLocal('SP_BUILD_ADV_SEARCH'), this.advancedMethod, ' ', advancedGoButton, loadButton, saveButton, {
					xtype : 'tbspacer'
				}

				]
			}, addRemoveBar ]
		};
	}
	;
	if (componentDisplayOption.SimpleSearchEnable && !componentDisplayOption.AdvancedSearchEnable) {
		defConfig = {
			border : false,
			layout : "border",
			height : 30,
			items : [
					{
						height : 30,
						xtype : "toolbar",
						region : "center",

						items : [ (this.barIdx + 1).toString(), {
							xtype : 'tbseparator'
						}, {
							xtype : 'box',
							autoEl : {
								tag : 'img',
								src : 'templates/easysdi_map/icons/silk/find.png'
							}
						}, EasySDI_Map.lang.getLocal('SP_SEARCH'), this.simpleMethod, EasySDI_Map.lang.getLocal('SP_FOR'), this.simpleFor,
								EasySDI_Map.lang.getLocal('SP_IN_PLACE'), this.inplaceAutocomplete, ' ', this.simpleGoButton,

								{
									xtype : 'tbspacer'
								}

						]
					}, addRemoveBar ]
		};
	}
	;
	if (!componentDisplayOption.SimpleSearchEnable && !componentDisplayOption.AdvancedSearchEnable) {
		defConfig = {};
	}
	;
	Ext.applyIf(this, defConfig);

	// Keep track of other stuff related to the bar, so we can delete them when
	// destroyed.
	this.featureGrid = null;
	this.distinctGrids = []; // TODO - handle distincts in a proper
								// configurable manner
	this.selectionGrid = null;
	this.layers = [];
	this.gridConfig = null;

	this.on('resize', function(cmp, adjWidth, adjHeight, rawWidth, rawHeight) {
		var size = cmp.getSize();
		if (size.width < 1000) {
			// Running on a very small screen, so squash the search bar as much
			// as possible
			this.simpleGoButton.setText('>>');
			advancedGoButton.setText('>>');
			this.inplaceAutocomplete.setWidth(90);
			this.simpleFor.setWidth(95);
			this.advancedMethod.setWidth(120);
			spacer.setWidth(8);
			// We now have to lose functionality to add and remove search rows
			addRemoveBar.setWidth(25);
			loadButton.setVisible(false);
			saveButton.setVisible(false);
			this.removeButton.setVisible(false);
			this.addButton.setVisible(false);
		} else if (size.width < 1270) {
			// If running on a fairly small screen, just squash the search
			// boxes.
			this.inplaceAutocomplete.setWidth(100);
			this.simpleFor.setWidth(105);
			spacer.setWidth(25);
		}
	}, this);

	EasySDI_Map.InnerSearchBar.superclass.initComponent.call(this);
},

_selectStyle : function() {
	// Select a style, by cycling through the available ones so each search bar
	// looks different
	if (SData.searchLayer && typeof SData.searchLayer.styles !== "undefined") {
		if (typeof SData.searchLayer.barIdx == "undefined") {
			SData.searchLayer.barIdx = 0;
		} else {
			SData.searchLayer.barIdx++;
		}
		this.barIdx = SData.searchLayer.barIdx;
		// cycle round through the available styles
		this.layerStyle = SData.searchLayer.styles[this.barIdx % SData.searchLayer.styles.length];
	}
},

_onSimpleForLoad : function() { // this context needs to be the combobox.
		if (this.store.getCount() == 1) {
			var r = this.store.getAt(0);
			this.setValue(r.data[this.valueField]);
		}
	},

	_simpleMethodSelect : function() {
		this.simpleFor.collapse();
		this.simpleFor.clearValue();
		this.simpleFor.lastQuery = null; // ensure requery takes place
	this.simpleFor.valueField = SData.simpleSearchTypes[this.simpleMethod.value].dropDownIdAttr || 'fid';
	this.simpleFor.displayField = SData.simpleSearchTypes[this.simpleMethod.value].dropDownDisplayAttr || 'name';
	if (SData.simpleSearchTypes[this.simpleMethod.value].dropDownFeatureType === '') {
		// default data when using the default empty store.
		this.simpleFor.mode = 'local';
		this.simpleFor.minChars = 0;
	} else {
		this.simpleFor.mode = 'remote';
		this.simpleFor.minChars = componentParams.autocompleteNumChars;
	}
	// Destroy the existing list and rebuild, with new store, display and ID
	// fields.
	if (this.simpleFor.view) {
		Ext.destroy(this.simpleFor.view);
	}
	if (this.simpleFor.list) {
		this.simpleFor.list.destroy();
		this.simpleFor.list = null;
	}
	this.simpleFor.tpl = null;

	this.simpleFor.store.un('load', this._onSimpleForLoad, this.simpleFor);
	this.simpleFor.bindStore(this.simpleForStores[this.simpleMethod.value], false);
	this.simpleFor.store.on('load', this._onSimpleForLoad, this.simpleFor);

	this.simpleFor.initList();
	if (this.simpleFor.mode == 'local') {
		this.simpleFor.setValue(0);
	}
},

_simpleGoClick : function() {
	// record the grid configuration for this as the most recent search run by
	// this search bar
	this.gridConfig = SData.simpleSearchTypes[this.simpleMethod.value].gridConfig;
	// first check that at least some rstriction on the data has been set:
	if ((this.simpleFor.value === undefined || this.simpleFor.value === "")
			&& (this.inplaceAutocomplete.value === undefined || this.inplaceAutocomplete.value === "")) {
		alert(EasySDI_Map.lang.getLocal('SP_ENTER_VALUES'));
		return;
	}
	var searchConfig = SData.simpleSearchTypes[this.simpleMethod.value];
	this.filter = this.panel._getFilter(this, searchConfig);
	this.panel.trigger('doSearch', {
		searchBar : this
	});
	// Build a search string
	this.searchDesc = EasySDI_Map.lang.getLocal('SP_SEARCH') + ' ' + searchConfig.title;
	if (typeof this.simpleFor.value != "undefined" && this.simpleFor.value != "") {
		this.searchDesc += ' ' + EasySDI_Map.lang.getLocal('SP_FOR') + ' ' + this.simpleFor.lastSelectionText;
		this.searchSpatial = 0;
	}
	if (typeof this.inplaceAutocomplete.value != "undefined" && this.inplaceAutocomplete.value != "") {
		this.searchDesc += ' ' + EasySDI_Map.lang.getLocal('SP_IN_PLACE') + ' ' + this.inplaceAutocomplete.lastSelectionText;
		this.searchSpatial = 1;
	}
	// And set the search type
	this.searchType = SData.simpleSearchTypes[this.simpleMethod.value].untranslatedTitle;
},

/**
 * Click handler for the advanced Go button.
 */
_advancedGoClick : function() {
	this.gridConfig = {
		distinctResultsGrids : [],
		rowDetailsFeatureType : SData.searchLayer.rowDetailsFeatureType
	};
	// Get the appropriate distinct grids for this search type.
	/*
	 * if (this.advancedMethod.getValue() == filterPanelMode.taxonStatus ||
	 * this.advancedMethod.getValue() == filterPanelMode.taxa ||
	 * this.advancedMethod.getValue() == filterPanelMode.allTaxa ||
	 * this.advancedMethod.getValue() == filterPanelMode.all) {
	 * this.gridConfig.distinctResultsGrids.push('taxon'); } if
	 * (this.advancedMethod.getValue() == filterPanelMode.biotopeStatus ||
	 * this.advancedMethod.getValue() == filterPanelMode.biotopes ||
	 * this.advancedMethod.getValue() == filterPanelMode.allBiotopes ||
	 * this.advancedMethod.getValue() == filterPanelMode.all) {
	 * this.gridConfig.distinctResultsGrids.push('biotope'); }
	 */
	this.panel.trigger('showFilterPanel', this);
	this.panel.trigger('filterPanel.setMode', this.advancedMethod.getValue());
	this.panel.trigger('filterPanel.updateFilter');
},

/**
 * Display the load search dialog.
 */
_loadSearch : function() {
	var dlg = new EasySDI_Map.Dlg.LoadFilter();
	dlg.registerTrigger('loadFilter', this.loadFilter.createDelegate(this));
	dlg.show();
},

/**
 * Display the save search dialog.
 */
_saveSearch : function(evt) {
	if (typeof this.searchDesc == "undefined" || this.searchDesc === "") {
		alert(EasySDI_Map.lang.getLocal('SP_NO_SEARCH_TO_SAVE'));
	} else {
		var dlg = new EasySDI_Map.Dlg.SaveFilter( {
			data : {
				description : this.searchDesc,
				filter_data : this.filterData,
				filter_mode : this.advancedMethod.getValue()
			}
		});
		dlg.show();
	}
},

/**
 * Trigger method to load a selected saved search definition.
 */
loadFilter : function(filter) {
	this.searchDesc = filter.description;
	this.filterData = filter.filter_data;
	this.advancedMethod.setValue(filter.filter_mode);
	this._advancedGoClick();
},

/**
 * Zoom to the extents of the current results set. Needs a WFS layer to work.
 */
zoomToExtent : function() {
	Ext.each(this.layers, function(layer) {
		if (layer.CLASS_NAME == "OpenLayers.Layer.Vector") {
			this.panel.trigger('zoomToExtent', layer.getDataExtent());
			return;
		}
	}, this);
}

});

/**
 * The container panel for the search bar.
 */
EasySDI_Map.SearchPanel = Ext.extend(Ext.Panel, {

	/**
	 * Constructor - builds an initially empty search panel with no search bars.
	 */
	constructor : function(config) {
		// create a unique id so this can control it's layers/grids etc.
	this.methodstore = new Ext.data.SimpleStore( {
		fields : [ 'id', 'method' ],
		data : this._getAvailableSearchTypes()
	});

	var simpleTypeList = [];
	Ext.each(SData.simpleSearchTypes, function(type, i) {
		simpleTypeList.push( [ i, type.title ]);
	}, this);

	this.searchtypestore = new Ext.data.SimpleStore( {
		fields : [ 'id', 'item' ],
		data : simpleTypeList
	});

	if (SData.localisationLayers.length === 0) {
		// alert(EasySDI_Map.lang.getLocal('SP_ERROR_NO_LOCALISATION'));
	}

	config.autoHeight = true;

	// Call parent constructor
	EasySDI_Map.SearchPanel.superclass.constructor.apply(this, arguments);

},

/**
 * Return the list of search types that the user is authorised to access.
 */
_getAvailableSearchTypes : function() {
	var result = [];
	// A search for Any (taxa|biotopes|any) is only enabled if there are other
	// filter tabs present,
	// otherwise this becomes a search for anything with no filter
	var searchForAnyEnabled = componentParams.authorisedTo.ADV_SEARCH_MISC || componentParams.authorisedTo.ADV_SEARCH_PLACE;
	/*
	 * var searchForAnyEnabled = componentParams.authorisedTo.ADV_SEARCH_SURVEY ||
	 * componentParams.authorisedTo.ADV_SEARCH_CRITERIA ||
	 * componentParams.authorisedTo.ADV_SEARCH_MISC ||
	 * componentParams.authorisedTo.ADV_SEARCH_PLACE; if
	 * (componentParams.authorisedTo.ADV_SEARCH_TAXA) {
	 * result.push([filterPanelMode.taxa,
	 * EasySDI_Map.lang.getLocal('SP_OBS_SEL_TAXA')]); if (searchForAnyEnabled) {
	 * result.push([filterPanelMode.allTaxa,
	 * EasySDI_Map.lang.getLocal('SP_OBS_ANY_TAXA')]); } } if
	 * (componentParams.authorisedTo.ADV_SEARCH_BIOTOPE) {
	 * result.push([filterPanelMode.biotopes,
	 * EasySDI_Map.lang.getLocal('SP_OBS_SEL_BIOTOPES')]); if
	 * (searchForAnyEnabled) { result.push([filterPanelMode.allBiotopes,
	 * EasySDI_Map.lang.getLocal('SP_OBS_ANY_BIOTOPES')]); } } if
	 * (componentParams.authorisedTo.ADV_SEARCH_TAX_STS) {
	 * result.push([filterPanelMode.taxonStatus,
	 * EasySDI_Map.lang.getLocal('SP_OBS_TAXA_STATUS')]); } if
	 * (componentParams.authorisedTo.ADV_SEARCH_BIO_STS) {
	 * result.push([filterPanelMode.biotopeStatus,
	 * EasySDI_Map.lang.getLocal('SP_OBS_BIOTOPES_STATUS')]); } if
	 * (componentParams.authorisedTo.ADV_SEARCH_TAXA &&
	 * componentParams.authorisedTo.ADV_SEARCH_BIOTOPE && searchForAnyEnabled) {
	 * result.push([filterPanelMode.all,
	 * EasySDI_Map.lang.getLocal('SP_OBS_ANYTHING')]); }
	 */
	return result;
},

/**
 * Add a new search bar into the panel and return it. Idx is the position of the
 * search bar in the list, defaults to the end.
 */
getNewSearchBar : function(searchManager, idx) {

	var innerSearchBar = new EasySDI_Map.InnerSearchBar( {}, this);

	innerSearchBar.addListener('beforedestroy', function() {
		// TODO: consider deleting the feature selector and the distinct tabs
			// TODO: remove the layers
			if (this.featureGrid !== null) {
				this.featureGrid.destroy();
			}
			if (this.selectionGrid !== null) {
				this.selectionGrid.destroy();
			}
		});

	if (idx === undefined || idx === null) {
		this.add(innerSearchBar);
	} else {
		this.insert(idx, innerSearchBar);
	}
	this.doLayout();
	return innerSearchBar;
},

/**
 * Return the open layers filter object for the simple filter.
 * 
 * param searchConfig - The search configuration from SData.simpleSearchTypes
 */
_getFilter : function(innerSearchBar, searchConfig) {
	// next if we have a defined taxa etc, set up a filter. Note that the search
	// column could be anything....
	// We have to rely on extracting the id from the fid, as it is not
	// possible to request the ID on its own! Have to make massive assumption
	// on format of FID: that is is the featuretype followed by the id,
	// separated
	// by a full stop.
	var filters = [];
	if (innerSearchBar.simpleFor.value !== undefined) {
		var val;
		if (innerSearchBar.simpleFor.value.indexOf('.') != -1) {
			var parts = innerSearchBar.simpleFor.value.split(".");
			val = parts[1];
		} else {
			// Using a specific key, not Fid so use it as it is.
			val = innerSearchBar.simpleFor.value;
		}
		filters.push(new OpenLayers.Filter.Comparison( {
			type : searchConfig.operator || "==",
			property : searchConfig.searchAttribute,
			value : val
		}));
	}
	Ext.each(searchConfig.additionalFilters, function(filterConfig) {
		filters.push(new OpenLayers.Filter.Comparison( {
			type : filterConfig.operator || "==",
			property : filterConfig.attribute,
			value : filterConfig.value
		}));
	}, this);
	// Then we define any geometry restrictions.
	if (innerSearchBar.inplaceAutocomplete.value !== undefined) {
		for ( var i = innerSearchBar.inplaceAutocomplete.store.getCount() - 1; i >= 0; i--) {
			var record = innerSearchBar.inplaceAutocomplete.store.getAt(i);
			if (record.data.ipa_fullid == innerSearchBar.inplaceAutocomplete.value) {
				if (componentParams.useVectorisedLocations) {
					var id;
					var level1 = innerSearchBar.inplaceAutocomplete.value.split(':');
					var featureType = level1[0];
					// the inplaceAutocomplete.value has 2 possible formats
					if (componentParams.autocompleteUseFID) {
						// In this case the ipa_fullid is comprised of:
						// "<featureType>:<feature_type>.<id>"
						var level2 = level1[1].split('.');
						id = level2[1];
					} else {
						// In this case the ipa_fullid is comprised of:
						// <featureType>:<id>
						id = level1[1];
					}
					filters.push(new OpenLayers.Filter.Comparison( {
						type : OpenLayers.Filter.Comparison.LIKE,
						value : id,
						property : featureType + (user.loggedIn ? "_private" : "_public") + '_keylist'
					}));
				} else {
					// the ID field is undefined so we use the geometry directly
					filters.push(new OpenLayers.Filter.Spatial( {
						type : OpenLayers.Filter.Spatial.INTERSECTS,
						value : record.data.feature.geometry,
						property : SData.searchLayer.geometryName
					}));
				}
			}
		}
	}
	if (filters.length == 1) {
		return filters[0];
	} else {
		// multiple filters, so AND them together
		return new OpenLayers.Filter.Logical( {
			filters : filters,
			type : OpenLayers.Filter.Logical.AND
		});
	}
}

});

Ext.mixin(EasySDI_Map.SearchPanel, EasySDI_Map.TriggerManager);