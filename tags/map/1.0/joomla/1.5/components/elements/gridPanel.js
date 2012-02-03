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

// Localisation prefix GP

Ext.namespace("EasySDI_Map");

// Some basic parsing of dates, so we can sort the vague date columns. Doesn't handle the obscure
// date formats properly.

var _DATE_FORMAT_REGXES = {
    'Y': new RegExp('^-?[0-9]+'),
    'd': new RegExp('^[0-9]{1,2}'),
    'm': new RegExp('^[0-9]{1,2}'),
    'H': new RegExp('^[0-9]{1,2}'),
    'M': new RegExp('^[0-9]{1,2}')
};

/*
 * _parseData does the actual parsing job needed by `strptime`
 */
function _parseDate(datestring, format) {
    var parsed = {};
    for (var i1=0,i2=0;i1<format.length;i1++,i2++) {
    var c1 = format[i1];
    var c2 = datestring[i2];
    if (c1 == '%') {
        c1 = format[++i1];
        var data = _DATE_FORMAT_REGXES[c1].exec(datestring.substring(i2));
        if (!data || !data.length) {
            return null;
        }
        data = data[0];
        i2 += data.length-1;
        var value = parseInt(data, 10);
        if (isNaN(value)) {
            return null;
        }
        parsed[c1] = value;
        continue;
    }
    if (c1 != c2) {
        return null;
    }
    }
    return parsed;
}

/*
 * basic implementation of strptime. The only recognized formats
 * defined in _DATE_FORMAT_REGEXES (i.e. %Y, %d, %m, %H, %M)
 */
function strptime(datestring, format) {
    var parsed = _parseDate(datestring, format);
    if (!parsed) {
    return null;
    }
    // create initial date (!!! year=0 means 1900 !!!)
    var date = new Date(0, 0, 1, 0, 0);
    date.setFullYear(0); // reset to year 0
    if (parsed.Y) {
    date.setFullYear(parsed.Y);
    }
    if (parsed.m) {
    if (parsed.m < 1 || parsed.m > 12) {
        return null;
    }
    // !!! month indexes start at 0 in javascript !!!
    date.setMonth(parsed.m - 1);
    }
    if (parsed.d) {
    if (parsed.m < 1 || parsed.m > 31) {
        return null;
    }
    date.setDate(parsed.d);
    }
    if (parsed.H) {
    if (parsed.H < 0 || parsed.H > 23) {
        return null;
    }
    date.setHours(parsed.H);
    }
    if (parsed.M) {
    if (parsed.M < 0 || parsed.M > 59) {
        return null;
    }
    date.setMinutes(parsed.M);
    }
    return date;
}


EasySDI_Map.GridPanel = Ext.extend(Ext.Panel, {
  tabPanel: null,

  constructor: function(config) {
    EasySDI_Map.GridPanel.superclass.constructor.apply(this, arguments);
    this.addListener('expand', function() {
      this.trigger('enableWfsSearch');
    }, this);
    this.addListener('collapse', function() {
      this.trigger('disableWfsSearch');
    }, this);
    
    //Enable this panel according to the component display options stored in the database
    if  (!componentDisplayOption.SimpleSearchEnable  && !componentDisplayOption.AdvancedSearchEnable )
    {
    	this.hide();
    }
  },
  
  /**
   * Add a search to the set of grids, or update an existing one. Each search is uniquely identified by the searchbar
   * it is associated with.
   */
  addSearch: function(protocol, layers, selectControl, searchBar) {
  	
    var attrs = this._loadAttrs(SData.searchLayer.featureType.replace('{geom}',''));
    this._initTabPanel();
    // Selection tab must be built before Feature tab.
    this._initSelectionTab(searchBar, attrs);
    this._initFeatureTab(protocol, layers, selectControl, searchBar, attrs);
    this._initDistinctTabs(searchBar);
    // Find the position of the tabs relative to the other search bars' tabs.
    var pos;
    if (searchBar.ownerCt.items.indexOf(searchBar)===0) {
      // First searchbar
      pos=0;
    } else {
      // Not the first, so insert after the selection tab of the previous
      var barPos=searchBar.ownerCt.items.indexOf(searchBar)-1;
      // Work backwards through the search bars to find a searchbar that has search grids, to insert after
      while (barPos>=0 && searchBar.ownerCt.items.items[barPos].selectionGrid===null) {
        barPos--;
      }
      if (barPos>=0) {
        var previousBar = searchBar.ownerCt.items.items[barPos];
        pos=this.tabPanel.items.indexOf(previousBar.selectionGrid)+1;
      } else {
        // No previous searchBar has any grids
        pos=0;
      }
    }
    this.tabPanel.insert(pos, searchBar.selectionGrid);
    Ext.each(searchBar.distinctGrids, function(grid) {
      this.tabPanel.insert(pos, grid);
    }, this);
    this.tabPanel.insert(pos, searchBar.featureGrid);
    // Don't load the store - it is already loaded when the map is built.
    this.expand();
    this.tabPanel.setActiveTab(searchBar.featureGrid);
    this.doLayout();
  },

  /**
   * Create a columns and fields object from the configurable list of results attributes for a grid.
   * featureType - name of the feature type Excluding the Geom part.
   */
  _loadAttrs : function(featureType) {
    var r = {fields: [], columns: []};
    var catAndName=[], sortType;
    var visibleCols = Ext.state.Manager.get(featureType + 'Cols', null);
    if (visibleCols===null) {
      visibleCols = SData.defaultAttrs[featureType];
    }
    Ext.each(SData.attrs[featureType], function(attr) {    	
    	var isInGrid = (visibleCols===null || visibleCols.indexOf(attr.name) != -1);
    	
    	// Any field that is in the grid, or is marked as invisible must be included in the datastore.
    	// If marked as invisible the column must have some other purpose, e.g. a key.
    	if (isInGrid || attr.visible===false) {
        // For the date field, which contains a date string, we need to parse it to a date to be able to sort.
        if (attr.name=='date') {
          sortType=function(value) {
            return strptime(value, '%d/%m/%Y');
          };
        } else {
          sortType=null;
        }
        r.fields.push({
          name: attr.name,
          type: attr.type,
          pk: attr.name==componentParams.featureIdAttribute,
          sortType: sortType
        });
      }
      // invisible columns are added as feature fields, but not grid columns. E.g the primary key.
      // Extra available columns are added but made invisible, so they can be user-added.
      if ((attr.visible || typeof attr.visible=="undefined")) {
        // split the attribute category|name into an array
        catAndName=EasySDI_Map.lang.getLocal('COL_' + attr.name).split('/');
        r.columns.push({
          header: catAndName[1],
          category: catAndName[0],
          width: attr.width,
          sortable: true,
          dataIndex: attr.name,
          hidden: !isInGrid
        });
      }
    });
    return r;
  },

  /**
   * On demand creation of the tab panel.
   */
  _initTabPanel: function() {
     if (this.tabPanel===null) {
       this.tabPanel = new Ext.TabPanel();
       this.add(this.tabPanel);
    }
  },
  
  /**
   * Prepare the grid and store used for the selection tab.
   */
  _initSelectionTab: function(searchBar, attrs) {
    var selectionStore = new Ext.data.Store({
      fields: attrs.fields
    });
    var gridView = new EasySDI_Map.PagingGridView();
    var pagingBar = new EasySDI_Map.PagingToolbar({
      store: selectionStore,
      displayName: EasySDI_Map.lang.getLocal("GP_SEL_LIST"),
      pageSize: 7,
      featureType: SData.searchLayer.featureType,
      listDetailsFeatureType: SData.searchLayer.rowDetailsFeatureType,
			listDetailsGeometryName: SData.detailsReportGeoms[SData.searchLayer.rowDetailsFeatureType],
      displayInfo: true,
      displayMsg: EasySDI_Map.lang.getLocal("GP_PAGE_FOOTER_SEL"),
      gridView: gridView,
      includeReportDownload: true,
      searchDesc: searchBar.searchDesc,
      searchType: "SELECTION",
      searchSpatial: searchBar.searchSpatial,
      idField: componentParams.featureIdAttribute
    });    
    searchBar.selectionGrid = new Ext.grid.GridPanel({
      title: EasySDI_Map.lang.getLocal("GP_SEL_LIST"),
      view: gridView,
      height: 202,
      store: selectionStore,
      columns: attrs.columns,
      bbar: pagingBar
    });     
    searchBar.selectionGrid.colModel.type = SData.searchLayer.featureType.replace('{geom}','');
    searchBar.selectionGrid.colModel.on("hiddenchange", this._colHiddenChange, this);
    this._addContextMenu(searchBar, searchBar.selectionGrid, searchBar.gridConfig, this._getFeatureMenu.createDelegate(this), false);
  },
  
  
  /**
   * Prepare the grid and store used for the main feature tab. This must be done
   * after the selection tab, since the selection model refers to it.
   */
  _initFeatureTab: function(protocol, layers, selectControl, searchBar, attrs) {
    var store = this._initFeatureStore(protocol, layers[0], attrs.fields, searchBar);
    var selectionModel = new EasySDI_Map.FeatureSelectionModel({
      layers: layers,
      selectControl: selectControl,
      selectList: searchBar.selectionGrid
    });
    
    // The main list of features
    var gridView = new EasySDI_Map.PagingGridView();
    var pagingBar = new EasySDI_Map.PagingToolbar({
      store: store,
      displayName: EasySDI_Map.lang.getLocal("Occurrences"),
      featureType: SData.searchLayer.featureType,
      listDetailsFeatureType: SData.searchLayer.rowDetailsFeatureType,
      listDetailsGeometryName: SData.detailsReportGeoms[SData.searchLayer.rowDetailsFeatureType],
      pageSize: 7,
      displayInfo: true,
      displayMsg: EasySDI_Map.lang.getLocal("GP_PAGE_FOOTER_FEATURE"),
      gridView: gridView,
      includeReportDownload: true,
      includeReloadButton: true,
      searchDesc: searchBar.searchDesc,
      searchType: searchBar.searchType,
      searchSpatial: searchBar.searchSpatial,      
      idField: componentParams.featureIdAttribute
    });
    pagingBar.registerTrigger('reloadQuery', function() {    	
    	rwg.searchManager.doWfsSearch(searchBar);
    });
    pagingBar.registerTrigger('zoomToExtent', function() {
    	searchBar.zoomToExtent();
    });
    searchBar.featureGrid = new Ext.grid.GridPanel({
      title: EasySDI_Map.lang.getLocal("GP_FEATURE_LIST"),
      view: gridView,
      height: 202,
      store: store,
      columns: attrs.columns,
      bbar: pagingBar,
      selModel: selectionModel
    });
    // Ensure that when the columns are shown/hidden, the column manager knows the name of the 
    // feature type so it can store the correct state name.
    searchBar.featureGrid.colModel.type = SData.searchLayer.featureType.replace('{geom}','');
    searchBar.featureGrid.colModel.on("hiddenchange", this._colHiddenChange, this);
    this._addContextMenu(searchBar, searchBar.featureGrid, searchBar.gridConfig, this._getFeatureMenu.createDelegate(this), true);
  },

  /** 
   * When a column's hide state change, save the state.
   */  
  _colHiddenChange: function(cm, col, hidden) {
    var visibleCols = Ext.state.Manager.get(cm.type + 'Cols', null);
    var colName = cm.getColumnById(col).dataIndex;
    if (visibleCols===null) {
      visibleCols = SData.defaultAttrs[cm.type];
    }    
    if (hidden) {
      for(var i=0;i<visibleCols.length; i++) {
				if(colName==visibleCols[i]) {
					visibleCols.splice(i, 1);
				}
			}
    } else {
    	visibleCols.push(colName);
    }
    Ext.state.Manager.set(cm.type + 'Cols', visibleCols);
  },


  /**
   * Initialise a WFS Feature Store, linking it to a layer, and retrieving the supplied list of fields.
   * The store is returned. When the store loads, any progress messages are hidden.
   */
  _initFeatureStore: function(protocol, layer, fields, searchBar) {
    var proxy = new GeoExt.data.ProtocolProxy({protocol: protocol});
    var store = new GeoExt.data.FeatureStore({
        layer: layer,
        fields: fields,
        proxy: proxy
    });    
    store.on('load', function() {
      Ext.Msg.hide();      
      if (store.data.length >= componentParams.maxFeatures) {
      	Ext.Msg.alert(EasySDI_Map.lang.getLocal("GP_TOO_MUCH_DATA"), 
      			EasySDI_Map.lang.getLocal("GP_TOO_MUCH_DATA_DESC"));
      }
    }, this);
    store.on('loadexception', function(e) {
      Ext.Msg.hide();
      Ext.Msg.alert(EasySDI_Map.lang.getLocal("GP_LAYER_ACCESS_ERROR"), e.response.priv.statusText);
    }, this);
    return store;
  },

  /**
   * Initialise the grids that hold a distincted version of the results.
   */
  _initDistinctTabs: function(searchBar) {
    var distinctGrid, store, gridView, grid, attrs;

    // cleanup any old distinct grids, since we will rebuild them in case the query type is different.
    //Ext.each(searchBar.distinctGrids, function(grid) { grid.destroy(); });
    searchBar.distinctGrids = [];

    Ext.each(searchBar.gridConfig.distinctResultsGrids, function(gridName) {
      grid=SData.distinctResultsGrids[gridName];
      attrs = this._loadAttrs(grid.featureType.replace('{geom}',''));
      store = new Ext.data.Store({
        fields: attrs.fields
      });
      gridView = new EasySDI_Map.PagingGridView();
      distinctGrid=new Ext.grid.GridPanel({
        title: grid.title,
        height: 202,
        store: store,
        columns: attrs.columns,
        view: gridView,
        cls: 'distinctGrid',
        loadMask: {
          msg: EasySDI_Map.lang.getLocal('FGR_LOADING') + '...'
        },
        bbar: new EasySDI_Map.PagingToolbar({
          store: store,
          displayName: grid.title,
          pageSize: 7,
          featureType: grid.featureType,
          displayInfo: true,
          displayMsg: EasySDI_Map.lang.getLocal("GP_PAGE_FOOTER_DET"),
          gridView: gridView,
          includeReportDownload: true,
          listDetailsFeatureType: grid.featureType,
          searchDesc: searchBar.searchDesc,
          searchType: "ADDN_" + grid.title,
          searchSpatial: searchBar.searchSpatial,          
      		idField: grid.distinctPk
        }),
        listeners : {
          'activate' : {
            fn : function(tab) { this._loadDistinctTab(tab, searchBar); },
            single: true,
            scope: this
          }
        }
      });
      distinctGrid.gridConfig = grid;
      searchBar.distinctGrids.push(distinctGrid);
      this._addContextMenu(searchBar, distinctGrid, grid, this._getDistinctMenu.createDelegate(this));
    }, this);
  },

  /**
   * Load the contents of a distincted tab when the tab is first accessed. This builds a list
   * of distinct entries from the main feature tab, then issues a WFS query to retrieve the
   * rows for the distinct keys found.
   */
  _loadDistinctTab : function(tab, searchBar) {
    var distinctVals = [];
    var filters = [];
    Ext.each(searchBar.featureGrid.store.data.items, function(occurrence) {
      if (distinctVals.indexOf(occurrence.data[tab.gridConfig.distinctFk])==-1) {
        distinctVals.push(occurrence.data[tab.gridConfig.distinctFk]);
        filters.push(new OpenLayers.Filter.Comparison({
          type: OpenLayers.Filter.Comparison.EQUAL_TO,
          property: tab.gridConfig.distinctPk,
          value: occurrence.data[tab.gridConfig.distinctFk]
        }));
      }
    }, this);
    var attrs = this._loadAttrs(tab.gridConfig.featureType.replace('{geom}',''));
    var protocol = new OpenLayers.Protocol.WFS({
      url: componentParams.proxiedPubWfsUrl,
      featureNS: componentParams.pubFeatureNS,
      featurePrefix: componentParams.pubFeaturePrefix,			
      featureType: tab.gridConfig.featureType,
      filter: new OpenLayers.Filter.Logical({filters: filters, type: OpenLayers.Filter.Logical.OR}),
      srsName: componentParams.projection,
      version: "1.0.0"
    });
    // Initialise a feature store, not linked to a layer as this is not spatial data
    var store = this._initFeatureStore(protocol, null, attrs.fields);
    tab.reconfigure(store, new Ext.grid.ColumnModel(attrs.columns));
    // Ensure the cm knows the type so it can persist column info in the correct named state object
    tab.colModel.type = tab.gridConfig.featureType;
    tab.colModel.on("hiddenchange", this._colHiddenChange, this);
    // Also point the pagination bar at the store
    tab.getBottomToolbar().setStore(store);
    tab.getBottomToolbar().refresh();
    store.load();
  },

  /**
   * Adds a context menu for features to a feature grid - either the main grid
   * or the selection grid.
   *
   * gridCtrl - Ext.GridPanel to add the menu to
   * gridConfig - the grid's configuration settings
   * menuFunction - function that builds the menu UI
   * params - params for the menuFunction.
   * 		E.g. should highlighting a feature be allowed? Doesn't make sense for selection grid.
   */
  _addContextMenu: function(searchBar, gridCtrl, gridConfig, menuFunction, params) {
    gridCtrl.on('cellcontextmenu', function(grid, rowIndex, cellIndex, e) {
      // Cancel the standard context menu.
      e.stopEvent();

      // Get the cursor coordinates so we'll know where to show
      // our custom context menu later.
      var coords = e.getXY();

      // Get the row object using the row index passed into this
      // event handler.
      var row = grid.store.data.items[rowIndex];

      var menu = menuFunction(grid, searchBar, row, gridConfig, params);
      menu.showAt([coords[0], coords[1]]);
    },
    this);
  },

  /**
   * Builds the feature context menu UI
   */
  _getFeatureMenu : function(grid, searchBar, row, gridConfig, allowHighlight) {
  	// If grid config has not specified the feature type, it must be the main results grid
  	if (typeof gridConfig.rowDetailsFeatureType=="undefined") {
  		if (typeof gridConfig.featureType=="undefined") {
  			gridConfig.rowDetailsFeatureType=SData.searchLayer.rowDetailsFeatureType;
  		} else {
  		  gridConfig.rowDetailsFeatureType=gridConfig.featureType;
  		}
  	}
    var addCommentCaption =
        (user.loggedIn && typeof SData.commentFeatureType !== "undefined") ?
        EasySDI_Map.lang.getLocal('GP_VIEW_ADD_COMMENTS') : EasySDI_Map.lang.getLocal('GP_VIEW_COMMENTS');
    var menu = new Ext.menu.Menu([{
      text: EasySDI_Map.lang.getLocal('FDR_TITLE_' + gridConfig.rowDetailsFeatureType.replace('{geom}','')),
      iconCls: 'externalRpt',
      handler: function(evt){
        this._showDetailsRpt(row.data.feature.data[componentParams.featureIdAttribute],
            gridConfig.rowDetailsFeatureType, componentParams.featureIdAttribute);
      },
      scope: this
    }, {
      text: EasySDI_Map.lang.getLocal('GP_HIGHLIGHT_ON_MAP'),
      iconCls: 'highlightFeature',
      handler: function(evt){
        this.trigger('highlightFeature', row.data.feature);
      },
      hidden: !allowHighlight,
      scope: this
    },{
      text: EasySDI_Map.lang.getLocal('GP_ZOOM_ON_MAP'),
      iconCls: 'zoom',
      handler: function() {
        this.trigger('zoomToExtent', row.data.feature.geometry.getBounds());
      },
      scope: this
    },{
      text: addCommentCaption,
      iconCls: 'commentAdd',
      handler: function(evt){
        // Display the popup dialog, allowing a feature to be commented if the user has rights
        this.trigger('featurePopup', row.data[componentParams.featureIdAttribute]);
      },
      scope: this
    }]);
    return menu;
  },

  /**
   * Builds the distinct context menu UI
   */
  _getDistinctMenu : function(grid, searchBar, row, gridConfig) {
    var menu = new Ext.menu.Menu([{
      text: EasySDI_Map.lang.getLocal('FDR_TITLE_' + gridConfig.rowDetailsFeatureType),
      iconCls: 'externalRpt',
      handler: function(){
        this._showDetailsRpt(row.data[gridConfig.distinctPk], gridConfig.rowDetailsFeatureType, gridConfig.distinctPk);
      },
      scope: this,
      hidden: typeof gridConfig.rowDetailsFeatureType=="undefined" || gridConfig.rowDetailsFeatureType===null
    }, {
      text: EasySDI_Map.lang.getLocal('GP_HIGHLIGHT_ON_MAP'),
      iconCls: 'highlightFeature',
      handler: function(){
        // clear existing selection
        this.trigger('clearSelection');
        // each loop to highlight all the features which match on our distincted field.
        Ext.each(searchBar.featureGrid.store.data.items, function(item) {
          if (item.data[gridConfig.distinctFk]==row.data[gridConfig.distinctPk]) {
            this.trigger('highlightFeature', item.data.feature);
          }
        }, this);
      },
      scope: this
    }]);
    return menu;
  },

  /**
   * Show a details report page (e.g. occurrence or taxon details).
   *
   * pk - Primary key value of the item to show
   * gridConfig - grid configuration object describing the rowDetailsFeatureType and pk (field name)
   */
  _showDetailsRpt: function(pkValue, rowDetailsFeatureType, rowDetailsPk) {
    window.open(componentParams.componentUrl +
        '&view=featureDetails&type='+rowDetailsFeatureType+'&filter='+pkValue+'&filterfield='+rowDetailsPk);
  },

  /**
   * Triggerable function called when a comment is saved. Find all instances of a feature in the
   * grids, and increase the comment count.
   * param featureId - Id of the feature (according to componentParams.featureIdAttribute]
   */
  incCommentCount: function(featureId) {
    var idx;
    Ext.each(this.tabPanel.items.items, function(grid) {
      if (grid.cls!=='distinctGrid') {
        idx = grid.store.findBy(function(record, id) {
          return record.data[componentParams.featureIdAttribute]==featureId;
        });
        if (idx>-1 && grid.title!=EasySDI_Map.lang.getLocal("GP_SEL_LIST")) {
          grid.store.data.items[idx].data[SData.commentFeatureType.featureCommentCount]++;
        }
        if (grid.rendered) {
            grid.view.refresh();
        }        
      }
    }, this);
  }

});

Ext.mixin(EasySDI_Map.GridPanel, EasySDI_Map.TriggerManager);