/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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

 // Localisation prefix FGR

/**
 * A custom grid view that displays the column's category as an extra header row
 */
EasySDI_Map.CategoryHeaderGridView = Ext.extend(EasySDI_Map.PagingGridView, {
  /**
   * Override renderHeaders to add the extra column category information
   */
  renderHeaders : function(){
    var cm = this.cm, ts = this.templates;
    var ct = ts.hcell;
    var catTmpl = ts.catHeadCell;

    var cb = [], sb = [], cats=[], p = {};
    var	currentCat=null, cat={}, catSpan=0, catWidth=0, col=null;
    var len = cm.getColumnCount();
    var last = len - 1;

    for(var i = 0; i < len; i++){
      p.id = cm.getColumnId(i);
      p.value = cm.getColumnHeader(i) || "";
      p.style = this.getColumnStyle(i, true);
      p.tooltip = this.getColumnTooltip(i);
      p.css = i == 0 ? 'x-grid3-cell-first ' : (i == last ? 'x-grid3-cell-last ' : '');
      if(cm.config[i].align == 'right'){
        p.istyle = 'padding-right:16px';
      } else {
        delete p.istyle;
      }
      cb[cb.length] = ct.apply(p);
      // extra code to handle category headers
      col=cm.getColumnById(p.id);
      if (!col.hidden) {
		    if (currentCat!==null && currentCat!==col.category) {
		      cat.value=currentCat;
		      cat.colspan=catSpan;
		      cat.width=catWidth-9; // allow for padding and border
		      cats[cats.length] = catTmpl.apply(cat);
		      // reset span counter
		      catSpan=0;
		      catWidth=0
		    }
		    catSpan++; // how many cols does this category span?
		    catWidth += cm.getColumnWidth(i);
		    currentCat=col.category;
		  }
    }
    // Dump out the last column's category
    if (currentCat!==null) {
        cat.value=currentCat;
      cat.colspan=catSpan;
      cat.width=catWidth-9; // allow for padding and border
      cats[cats.length] = catTmpl.apply(cat);
    }
    return ts.header.apply({categories: cats.join(""), cells: cb.join(""), tstyle:'width:'+this.getTotalWidth()+';'});
  },

  /**
   * Override template initialisation to set a header template with 2 rows
   */
  initTemplates : function() {
    this.templates = this.templates || {};
    if (typeof this.templates.header=="undefined") {
      this.templates.header = new Ext.Template(
          '<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
          '<thead><tr class="x-grid3-hd-row category-headers">{categories}</tr></thead></table>',
          '<table border="0" cellspacing="0" cellpadding="0" style="{tstyle}">',
          '<thead><tr class="x-grid3-hd-row">{cells}</tr></thead>',
          "</table>"
          );
    }
    if (typeof this.templates.catHeadCell=="undefined") {
      this.templates.catHeadCell = new Ext.Template(
        '<th colspan="{colspan}" width="{width}">{value}</th>'
      );
    }    
    EasySDI_Map.CategoryHeaderGridView.superclass.initTemplates.apply(this, arguments);
  },

  beforeColMenuShow : function(){
    var cm = this.cm,  colCount = cm.getColumnCount();
    var colId, col, catMenu, cats=[], catsCountVisible=[];
    this.colMenu.removeAll();
    for(var i = 0; i < colCount; i++){
      if(cm.config[i].fixed !== true && cm.config[i].hideable !== false){
        colId = cm.getColumnId(i);
        col=cm.getColumnById(colId);
        if (typeof cats[col.category]=="undefined") {
          catMenu = this.colMenu.add(new Ext.menu.CheckItem({
            id: 'group-' + col.category,
            text: col.category,
            menu: [],
            hideOnClick: false,
            checked: !cm.isHidden(i)
          }));
          cats[col.category]=catMenu;
          // start a counter to work out the number of visible columns in a category so we can set the state
          catsCountVisible[col.category]=0;
        } else {
          catMenu = cats[col.category];
          catMenu.setChecked(catMenu.checked || !cm.isHidden(i));
        }
        if (!cm.isHidden(i)) {
          catsCountVisible[col.category]=catsCountVisible[col.category]+1;
        }
        catMenu.menu.add(new Ext.menu.CheckItem({
          id: "col-"+colId,
          text: cm.getColumnHeader(i),
          checked: !cm.isHidden(i),
          hideOnClick:false,
          disabled: cm.config[i].hideable === false,
          listeners: {'checkchange': {
            fn: this.handleColMenuCheck,
            scope: this
          }}
        }));
      }
    };
    // Now work out the state of each category item
    Ext.each(this.colMenu.items.items, function(item) {
      if (item.menu.items.length>catsCountVisible[item.text]) {
        item.setChecked(false);
      }
      if (catsCountVisible[item.text]>0 && catsCountVisible[item.text]<item.menu.items.length) {
        item.setIconClass('midstate_checkbox');
      }
      // register event now so it only picks up user interaction
      item.on('checkchange', this.handleCatMenuCheck, this);
    }, this);
  },

  /**
   * Click handler for the header context menu. Overrides the Ext version, to prevent handling of the
   * category and column checkboxes.
   */
  handleHdMenuClick : function(item){
    var index = this.hdCtxIndex;
    var cm = this.cm, ds = this.ds;
    switch(item.id) {
      case "asc":
        ds.sort(cm.getDataIndex(index), "ASC");
        break;
      case "desc":
        ds.sort(cm.getDataIndex(index), "DESC");
        break;
    }
    return true;
  },

  /**
   * Handle the click of a category checkbox in the menu. Checks or
   * unchecks all the child nodes and therefore changes the relevant
   * column's visibility.
   */
  handleCatMenuCheck: function(item) {
    var cm=this.cm;
    Ext.each(item.menu.items.items, function(colItem) {
      colItem.setChecked(item.checked);
    }, this);
    this.refresh(true);
  },

  /**
   * Handle the checking of a column checkbox in the menu
   */
  handleColMenuCheck: function(item) {
    var cm=this.cm;
    index = cm.getIndexById(item.id.substr(4));
    if(index != -1){
      if(!item.checked && cm.getColumnsBy(this.isHideableColumn, this).length <= 1){
        this.onDenyColumnHide();
        return false;
      }
      cm.setHidden(index, !item.checked);
      this.refreshCategory(cm.getColumnById(item.id.substr(4)).category);
    }
    this.refresh(true);
  },

  /**
   * Refresh the state checkbox for a column category menu item. Uses the
   * child menu item's state to determine checked, unchecked, or intermediate state.
   */
  refreshCategory: function(category) {
    // look for the category
    var catsCountVisible;
    Ext.each(this.colMenu.items.items, function(catItem) {
      if (catItem.text=category) {
        catsCountVisible=0;
        // count the checked items
        Ext.each(catItem.menu.items.items, function(subItem) {
          if (subItem.checked)
            catsCountVisible++;
        });
        if (catItem.menu.items.length==catsCountVisible) {
          // all checked
          catItem.setChecked(true);
          catItem.setIconClass('');
        } else if (catsCountVisible==0) {
          // none checked
          catItem.setChecked(false);
          catItem.setIconClass('');
        } else {
          // some checked
          catItem.setIconClass('midstate_checkbox');
        }
      }
    }, this);
  }

});


/**
 * The main class for rendering the feature grid page
 */
EasySDI_Map.FeatureGrid = Ext.extend(EasySDI_Map.ReportBase, {
  // config option to specify the filter as Xml
  filter: null,

  constructor: function(config) {
    Ext.Msg.show({
      title: EasySDI_Map.lang.getLocal('Please_Wait') + '...',
      msg: EasySDI_Map.lang.getLocal('FGR_LOADING'),
      icon:'msg-wait'
    });
    this.filter = config.filter;

    EasySDI_Map.FeatureGrid.superclass.constructor.apply(this, arguments);
    
    this.columns=[];
		var fields=[], props=[];
		var catAndName;
    Ext.each(SData.attrs[featureType.replace('{geom}','')], function(attr) {
      props.push(attr.name);
      if (attr.visible!==false) {
        fields.push({name: attr.name, type: attr.type});
        catAndName=EasySDI_Map.lang.getLocal('COL_' + attr.name).split('/');                
	      this.columns.push({
	          header: catAndName[1],
	          category: catAndName[0],
	          sortable: true,
	          dataIndex: attr.name,
	          width: attr.width
        });
      }
    }, this);   
    var geometryName = featureType==SData.searchLayer.featureType ? SData.searchLayer.geometryName : null;
    this.protocol = new OpenLayers.Protocol.WFS({
      url: componentParams.proxiedPubWfsUrl,
      featureNS: componentParams.pubFeatureNS,
      featurePrefix: componentParams.pubFeaturePrefix,
      featureType: featureType.replace('{geom}',geometryName==null ? '' : geometryName),
      geometryName: geometryName,
      propertyNames: props,
      srsName: componentParams.projection,
      version: componentParams.pubWfsVersion,
      filter: this.getFilter()
    });    
    
    var proxy = new GeoExt.data.ProtocolProxy({protocol: this.protocol});
    var store = new GeoExt.data.FeatureStore({
        fields: fields,
        proxy: proxy,
        srsName: componentParams.projection
    });
    store.on('load', this.loadStore, this);
    store.load();
  },  

  /**
   * Override the getFilter method to provide a filter that retrieves all the
   * features in the filter passed in the POST parameters. The this.filter value
   * is previously set as a config of the class, and should be a filter Xml represented
   * as a string.
   */
  getFilter: function() {
    // Get our XML as a DOM object
    try //Internet Explorer
      {
      xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
      xmlDoc.async="false";
      xmlDoc.loadXML(this.filter);
      }
    catch(e)
      {
      parser=new DOMParser();
      xmlDoc=parser.parseFromString(this.filter,"text/xml");
      }
    // Now OL can turn this into a filter object
    var format = new OpenLayers.Format.Filter({version:"1.0.0"});
    var filter = format.read(xmlDoc.firstChild);

    return filter;
  }, 
    

  /**
   * Build an HTML table for the feature list that should be in the store.
   */
  loadStore: function(store) {
    this.view = new EasySDI_Map.CategoryHeaderGridView({pageSize: 20});
    var grid = new Ext.grid.GridPanel({
      columns: this.columns,
      store: store,
      cls: "cat-head-table",
      view: this.view,
      columnLines: true,
      bbar: new EasySDI_Map.PagingToolbar({
        pageSize: 20,
        displayName: EasySDI_Map.lang.getLocal("Occurrences"),
        store: store,
        displayInfo: true,
        displayMsg: EasySDI_Map.lang.getLocal("GP_PAGE_FOOTER_FEATURE"),
        emptyMsg: 'No occurrences to display',
        gridView: this.view
      }),
      listeners: {
      	'columnresize': {
      		fn: function() {
      			this.view.refresh(true)
      		},
      		scope: this
      	}
      }
    });
    this.gridPanel.add(grid);
    this.doLayout();
    Ext.Msg.hide();
    grid.getBottomToolbar().refresh();
  }

});