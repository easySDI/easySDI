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

// Localisation prefix PAG

Ext.namespace("EasySDI_Map");

/**
 * EasySDI_Map.PagingToolbar
 * A toolbar for paging against WFS datasets, which have to be paged client-side.
 * Must use the EasySDI_Map.PagingGridView and also GeoExt.data.FeatureStore
 * for the grid data.
 */
EasySDI_Map.PagingToolbar = Ext.extend(Ext.Toolbar, {
  gridView: null,

  displayName: null,

  featureType: null,

  /**
   * The name of the feature type that supplies the data for the advanced report version of the grid.
   */
  listDetailsFeatureType: null,

  listDetailsGeometryName: null,

  includeReportDownload: false,

  includeReloadButton: false,

  /**
   * The current search description, so we can log it if the data is downloaded.
   */
  searchDesc: null,

  /**
   * The current search type, so we can log it if the data is downloaded.
   */
  searchType: null,

  /**
   * Is this a spatial search? So we can log it if the data is downloaded.
   */
  searchSpatial: false,
  
  /**
   * Name of ID field
   */
  idField: null,

  /**
   * Constructor
   * Config.gridView should be an instance of a PagingGridView
   * Config.displayName should be the display name for the class of each row (e.g. occurrences)
   */
  constructor: function(config) {
    // remember the gridView we are hooking up to for pagination
    this.gridView = config.gridView;
    this.displayName = config.displayName;

    EasySDI_Map.PagingToolbar.superclass.constructor.apply(this, arguments);
    config.store.on("load", this.refresh, this);
  },

  // private
  onDestroy : function(){
    if(this.store){
      this.store.un("load", this.onLoad, this);
    }
    EasySDI_Map.PagingToolbar.superclass.onDestroy.call(this);
  },

  setStore: function(store) {
    if (this.store){
      this.store.un("load", this.onLoad, this);
    }
    this.store = store;
    this.store.on("load", this.refresh, this);
  },

  onRender : function(ct, position) {
    Ext.PagingToolbar.superclass.onRender.call(this, ct, position);

    // Some combo box options for download format
    var formatstore = new Ext.data.SimpleStore({
      fields: ['id', 'format'],
      data: [
        [0, EasySDI_Map.lang.getLocal('PAG_FORMAT_ADOBE_PDF')],
        [1, EasySDI_Map.lang.getLocal('PAG_FORMAT_RTF')],
        [2, EasySDI_Map.lang.getLocal('PAG_FORMAT_CSV')],
        [3, EasySDI_Map.lang.getLocal('PAG_FORMAT_ADOBE_PDF_MAP')]
      ]
    });

    this.first = this.addButton({
        tooltip: EasySDI_Map.lang.getLocal('PAG_FIRST_PAGE'),
        iconCls: "resultset_first",
        disabled: true,
        handler: function() {
          this.gridView.firstPage();
          this.refresh();
        },
        scope: this
    });
    this.prev = this.addButton({
        tooltip: EasySDI_Map.lang.getLocal('PAG_PREVIOUS_PAGE'),
        iconCls: "resultset_previous",
        disabled: true,
        handler: function() {
          this.gridView.prevPage();
          this.refresh();
        },
        scope: this
    });
    this.addSeparator();
    this.add(EasySDI_Map.lang.getLocal('PAG_PAGE'));
    this.field = Ext.get(this.addDom({
       tag: "input",
       type: "text",
       size: "3",
       value: "1",
       cls: "x-tbar-page-number"
    }).el);
    this.field.on("keydown", this.onPagingKeydown, this);
    this.field.on("focus", function(){this.dom.select();});
    this.field.on("blur", this.onPagingBlur, this);
    this.afterTextEl = this.addText(String.format(EasySDI_Map.lang.getLocal('PAG_OF_N'), 1));
    this.field.setHeight(18);
    this.addSeparator();
    this.next = this.addButton({
        tooltip: EasySDI_Map.lang.getLocal('PAG_NEXT_PAGE'),
        iconCls: "resultset_next",
        disabled: true,
        handler: function() {
          this.gridView.nextPage();
          this.refresh();
        },
        scope: this
    });
    this.last = this.addButton({
        tooltip: EasySDI_Map.lang.getLocal('PAG_LAST_PAGE'),
        iconCls: "resultset_last",
        disabled: true,
        handler: function() {
          this.gridView.lastPage();
          this.refresh();
        },
        scope: this
    });
    this.addSeparator();
    if (this.includeReloadButton) {
      this.reloadQuery = this.addButton({
          tooltip: EasySDI_Map.lang.getLocal('PAG_RELOAD_QUERY'),
          iconCls: "table_refresh",
          handler: function() {
            this.trigger('reloadQuery');
            this.refresh();
          },
          scope: this
      });
      this.zoomToExtent = this.addButton({
          tooltip: EasySDI_Map.lang.getLocal('PAG_FILTER_EXTENTS'),
          iconCls: "x-btn-icon zoom",
          handler: function() {
            this.trigger('zoomToExtent');
            this.refresh();
          },
          scope: this
      });
      this.addSeparator();
    }
    if (this.includeReportDownload) {
      if (this.listDetailsFeatureType!==null) {
        this.report = this.addButton({
          iconCls: "rptBtn",
          handler: this._displayAdvancedGrid,
          scope: this
        });
        // Add a hidden form that we can use to POST data when loading report windows
        this.add({
          hidden: true,
          xtype: 'panel',
          html: '<form id="postform" target="_blank" method="post">' +
              '<input id="postBody" name="body"/></form>'
        });
      }
      this.print = this.addButton({
          iconCls: "printBtn",
          handler: this._displayPrintGrid,
          scope: this
      });
      this.addSeparator();
      this.add(EasySDI_Map.lang.getLocal('Download_as'));
      this.downloadFormat = new Ext.form.ComboBox({
        emptyText: EasySDI_Map.lang.getLocal('Sel_format'),
        store: formatstore,
        minListWidth: 170,
        displayField: 'format',
        valueField: 'id',
        typeAhead: true,
        triggerAction: 'all',
        mode: 'local'
      });
      this.add(this.downloadFormat);
      this.download = this.addButton({
          text: EasySDI_Map.lang.getLocal('Download'),
          handler: this._downloadReport,
          scope: this,
          cls: 'x-form-toolbar-standardButton'
      });
    }
    this.displayEl = Ext.fly(this.el.dom).createChild({cls:'x-paging-info'});
  },

  /**
   * Issue a request to the WPS service to download a report file for the current grid content.
   */
  _downloadReport: function() {
    var filterText=this._getFilterText();
    var properties = this._getProperties();
    // Construct the appropriate request to WPS
    var transform, mimeType, identifier;
    if (this.downloadFormat.value===0 || this.downloadFormat.value==3) {
      mimeType='application/pdf';
      if (this.downloadFormat.value===0) {
        transform='fop-default';
      } else {
        // Transformation needs to include the map image
        transform='fop-default-map';
      }
    } else {
      transform='xslt-default';
      if (this.downloadFormat.value==1) {
        mimeType='application/rtf';
      } else {
        mimeType='text/csv';
      }
    }
    var wmcInput;
    if (this.downloadFormat.value==3) {
      identifier = 'getMapReport';
      // Build a web map context to instruct the PDF what map to load
      var WMC = new OpenLayers.Format.WMC({parser : new OpenLayers.Format.WMC.v1_1_0_WithWFS({restrictToLayersInTree: rwg.layerTree})});
      wmcInput = '<wps:Input><ows:Identifier>mapContext</ows:Identifier>'+
          '<wps:Data><wps:ComplexData>' + WMC.write(rwg.mapPanel.map) +
          '</wps:ComplexData></wps:Data></wps:Input>';
    } else {
      identifier = 'getListReport';
      wmcInput = '';
    }
  // Don't access the background layers via the php proxy because we are already accessing the reports servlet through this route,
  // so the Joomla user authentication is already done.
  wmcInput = wmcInput.replace(new RegExp(this.escapeRegExpSpecialChars(
      componentParams.proxyURL.asString.replace(/&/g, '&amp;') + '&amp;url='
      ), 'g'), '');

    // Put the filter into a hidden input so we can POST the form.
    document.getElementById('postBody').value=
'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'+
'<wps:Execute service="WPS" version="1.0.0" xmlns:wps="http://www.opengis.net/wps/1.0.0" '+
'xmlns:ows="http://www.opengis.net/ows/1.1" xmlns:xlink="http://www.w3.org/1999/xlink" '+
'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opengis.net/wps/1.0.0 ../wpsExecute_request.xsd">'+
'<ows:Identifier>' + identifier + '</ows:Identifier>'+
'<wps:DataInputs>'+
'<wps:Input><ows:Identifier>userName</ows:Identifier>'+
'<wps:Data><wps:LiteralData>' + user.name + '</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>filters</ows:Identifier>'+
'<wps:Data><wps:LiteralData>' + this.searchDesc + '</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>type</ows:Identifier>'+
'<wps:Data><wps:LiteralData>' + this.searchType + '</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>spatial</ows:Identifier>'+
'<wps:Data><wps:LiteralData>' + this.searchSpatial + '</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>wfsRequest</ows:Identifier>'+
'<wps:Data><wps:ComplexData schema="http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd" mimeType="text/xml" encoding="UTF-8">'+
'<wfs:GetFeature service="WFS" version="1.0.0" outputFormat="GML2" xmlns:wfs="http://www.opengis.net/wfs" '+ 'xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '+
'xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd"> '+
'<wfs:Query typeName="' + componentParams.pubFeaturePrefix + ':' + this.featureType.replace('{geom}','') + '">' +
properties +
filterText +
'</wfs:Query></wfs:GetFeature></wps:ComplexData></wps:Data>'+
'</wps:Input>'+
wmcInput +
'</wps:DataInputs>'+
'<wps:ResponseForm>'+
'<wps:RawDataOutput mimeType="' + mimeType + '"><ows:Identifier>' + transform + '</ows:Identifier></wps:RawDataOutput>'+
'</wps:ResponseForm>'+
'</wps:Execute>';
    document.forms.postform.action=componentParams.wpsReportsUrl;
    document.forms.postform.submit();
  },

  /**
       * Escapes a string so that it can be inserted into a RegEx
       */
  escapeRegExpSpecialChars: function(pattern) {
    var specialChars = /(\.|\*|\+|\?|\^|\$|\||\(|\)|\[|\]|\{|\})/mg;
    return pattern.replace(specialChars, "\\$1");
  },

  /**
     * Update the state of the buttons and the labels
     */
  refresh: function() {
    if (typeof this.first!=='undefined') {
      this.first.setDisabled(this.gridView.offset === 0);
      this.prev.setDisabled(this.gridView.offset === 0);
      var isLastPage = this.gridView.offset >=
          Math.floor(this.store.totalLength / this.gridView.pageSize) * this.gridView.pageSize;
      this.next.setDisabled(isLastPage);
      this.last.setDisabled(isLastPage);
      this.field.dom.value = this.gridView.offset / this.gridView.pageSize + 1;
      this.afterTextEl.el.innerHTML = String.format(
          EasySDI_Map.lang.getLocal('PAG_OF_N'), Math.ceil(this.store.totalLength / this.gridView.pageSize));
      // Update the display label on the right
      var count = this.store.getCount();
      var msg = count === 0 ?
          this.emptyMsg :
          String.format(
              EasySDI_Map.lang.getLocal('PAG_DISPLAY_MSG'),
              this.displayName, this.gridView.offset+1,
              Math.min(this.gridView.offset+this.gridView.pageSize, this.store.totalLength), this.store.totalLength
          );
      this.displayEl.update(msg);
    }
  },

  //private
  onPagingBlur: function(e){
      this.field.dom.value = Math.floor(this.gridView.offset/this.gridView.pageSize)+1;
  },

  // private
  onPagingKeydown : function(e){
    var k = e.getKey();
    if (k == e.RETURN) {
      e.stopEvent();
      var pageNum = this.field.dom.value;
      if(parseInt(pageNum) !== 'NaN' && pageNum !== false) {
        this.gridView.selectPage(pageNum);
        this.refresh();
      }
    }
  },

  /**
   * Displays the advanced version of this grid in a new page/tab.
   */
  _displayAdvancedGrid: function() {
   var filterText = this._getFilterText();
   // Put the filter into a hidden input so we can POST the form.
   document.getElementById('postBody').value=filterText;
   document.forms.postform.action=componentParams.componentUrl + '&view=featureGrid' +
       '&featureType=' + this.listDetailsFeatureType;
   document.forms.postform.submit();
 },

   /**
   * Displays the printable version of this grid in a new page/tab.
   */
  _displayPrintGrid: function() {
   var filterText=this._getFilterText();
   // Put the filter into a hidden input so we can POST the form.
   document.getElementById('postBody').value=filterText;
   var url=componentParams.componentUrl + '&view=printGrid' +
      '&featureType=' + this.featureType;

   // Add sort information if available
   if (typeof this.store.sortInfo!=="undefined") {
     url += '&sortField=' + this.store.sortInfo.field;
     url += '&sortDir=' + this.store.sortInfo.direction;
   }
   document.forms.postform.action=url;
   document.forms.postform.submit();
 },

 /**
  * Returns the ogc filter requried for the current grid content as a text string.
  */
 _getFilterText: function() {
   var filter;
   if (typeof this.store.proxy!=="undefined") {
     // We have a WFS proxy so can use the filter
     filter=this.store.proxy.protocol.filter;
   } else {
     // In the selection grid, so need to grab each selected item individually
     var filters=[], pk=null;
     // Add filters - first find which field acts as the unique identifier
     Ext.each(this.store.fields, function(field) {
       if (field.pk===true) {
         pk = field.name;
       }
     });
     if (pk===null) {
       alert('Configuration error. Search attributes have no pk set.');
     }
     Ext.each(this.store.data.items, function(item) {
        filters[filters.length] = new OpenLayers.Filter.Comparison({
          type: OpenLayers.Filter.Comparison.EQUAL_TO,
          property: pk,
          value: item.data[pk]
        });
     }, this);
     filter = new OpenLayers.Filter.Logical({filters: filters, type: OpenLayers.Filter.Logical.OR});
   }
   var dom = new OpenLayers.Format.Filter.v1_0_0().write(filter);
   var filterText = this._XMLtoString(dom);
   return filterText;
 },

 /**
  * Retrieve a list of properties for inserting into a WFS request.
  */
  _getProperties: function() {
    var visibleCols = Ext.state.Manager.get(this.featureType + 'Cols', null);
    if (visibleCols===null) {
      visibleCols = SData.defaultAttrs[this.featureType.replace('{geom}', '')];
    }
    props='<ogc:PropertyName>' + this.idField + '</ogc:PropertyName>';
    Ext.each(visibleCols, function(col) {
      props += '<ogc:PropertyName>' + col + '</ogc:PropertyName>';
    }, this);
    return props;
  },

  /**
   * Convert an XML DOM element to a string, so it can be passed as a POST parameter.
   */
  _XMLtoString: function(elem) {
    var serialized;
    try {
      // XMLSerializer exists in current Mozilla browsers
      serializer = new XMLSerializer();
      serialized = serializer.serializeToString(elem);
    }
    catch (e) {
      // Internet Explorer has a different approach to serializing XML
      serialized = elem.xml;
    }
    return serialized;
  }

});

/**
 * A grid view which handles pagination by drawing just the appropriate records from the
 * already loaded dataset
 */
EasySDI_Map.PagingGridView = Ext.extend(Ext.grid.GridView, {
  /**
   * @cfg {Number} pageSize
   * The number of records to display per page (defaults to 7)
   */
  pageSize: 7,

  /**
   * @cfg {Number} offset
   * The initial (or current) record offset
   */
  offset: 0,

  /**
   * Selects the first page
   */
  firstPage : function() {
    this.offset = 0;
    this.refresh();
  },

  /**
   * Selects the last page
   */
  lastPage : function() {
    this.offset = Math.floor(this.grid.store.totalLength / this.pageSize) * this.pageSize;
    this.refresh();
  },

  /**
   * Selects the previous page
   */
  prevPage : function() {
    if (this.offset-this.pageSize >= 0) {
      this.offset -= this.pageSize;
      this.refresh();
    }
  },

  /**
   * Selects the next page
   */
  nextPage : function() {
    if (this.offset+this.pageSize < this.grid.store.totalLength) {
      this.offset += this.pageSize;
      this.refresh();
    }
  },

  /**
   * Selects a specific page
   */
  selectPage : function(page) {
    if ((page-1)*this.pageSize < this.grid.store.totalLength && page>0) {
      this.offset = (page-1)*this.pageSize;
      this.refresh();
    }
  },

  /**
   * Override renderRows to enforce a page size limit.
   */
  renderRows : function(startRow, endRow){
    endRow = typeof endRow == "undefined"? this.grid.store.getCount()-1 : endRow;
    endRow = Math.min(endRow, (startRow || 0) + this.pageSize-1);
    // Not sure why, but a superclass call does not work.
//    EasySDI_Map.PagingGridView.superclass.renderRows.call(this, startRow, endRow);

    var g = this.grid, cm = g.colModel, ds = g.store, stripe = g.stripeRows;
    var colCount = cm.getColumnCount();

    if(ds.getCount() < 1){
        return "";
    }

    var cs = this.getColumnData();

    startRow = startRow || 0;
    endRow = typeof endRow == "undefined"? ds.getCount()-1 : endRow;

    // records to render
    var rs = ds.getRange(startRow, endRow);

    return this.doRender(cs, rs, ds, startRow, colCount, stripe);
  },

  /**
   * Override renderBody to prevent drawing of rows other than the current page
   */
  renderBody : function() {
    var markup = this.renderRows(this.offset, this.offset + this.pageSize-1);
    return this.templates.body.apply({rows: markup});
  },

  /**
   * Override processRows to skip processing if the grid not initialised yet, as some of our
   * grids have lazy loading.
   */
  processRows : function(startRow, skipStripe) {
    if (this.ds!==null) {
      EasySDI_Map.PagingGridView.superclass.processRows.apply(this, arguments);
    }
  }
});

Ext.mixin(EasySDI_Map.PagingToolbar, EasySDI_Map.TriggerManager);