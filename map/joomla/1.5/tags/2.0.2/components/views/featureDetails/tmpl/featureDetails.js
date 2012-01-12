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

 // Localisation prefix FDR

Ext.namespace("EasySDI_Map");

EasySDI_Map.FeatureDetails = Ext.extend(EasySDI_Map.ReportBase, {
  constructor: function(config) {
    var formPanel = new Ext.form.FormPanel({
      labelWidth: 200,
      border: false,
      autoScroll: true,
      anchors: "100% 100%",
      buttonAlign: "right"
    });
        // Add save button to footer if commenting allowed
    if (user.loggedIn && typeof SData.commentFeatureType !== "undefined") {
      formPanel.addButton({
        text: EasySDI_Map.lang.getLocal('Save_comment')
      }, this.saveComment, this);
    }
    this.outputCntr = formPanel;
    EasySDI_Map.FeatureDetails.superclass.constructor.apply(this, arguments);
    this.gridPanel.add(formPanel);
    this.featureId = this.url.filter;    
    var fields=[], props=[];
    if (SData.detailsReportGeoms[this.url.type] !==undefined) {    	
    	props.push(SData.detailsReportGeoms[this.url.type]);
    }
    Ext.each(SData.attrs[this.url.type], function(attr) {
      props.push(attr.name);
      if (attr.visible!==false) {
        fields.push({name: attr.name, type: attr.type});      
      }
    }, this);      
    this.protocol = new OpenLayers.Protocol.WFS({
      url: componentParams.proxiedPubWfsUrl,
      featureNS: componentParams.pubFeatureNS,
      featurePrefix: componentParams.pubFeaturePrefix,
      featureType: this.url.type,
      srsName: componentParams.projection,
      version: componentParams.pubWfsVersion,
      propertyNames: props,
      filter: new OpenLayers.Filter.Comparison({
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: this.url.filterfield,
        value: this.featureId
      })
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
   * Function stub that can be overridden to add extra controls to the page in subclasses.
   */
  addExtraControls: function() {
    // Some combo box options for download format
    var formatstore = new Ext.data.SimpleStore({
      fields: ['id','format'],
      data: [
        [0, EasySDI_Map.lang.getLocal('PAG_FORMAT_ADOBE_PDF')],
        [1, EasySDI_Map.lang.getLocal('PAG_FORMAT_RTF')],
        [2, EasySDI_Map.lang.getLocal('PAG_FORMAT_CSV')]
      ]
    });
    this.downloadFormat = new Ext.form.ComboBox({      
      emptyText: EasySDI_Map.lang.getLocal('Sel_format'),
      store: formatstore,
      minListWidth: 170,
      displayField: 'format',
      valueField: 'id',
      typeAhead: true,
      triggerAction: 'all',
      mode: 'local',
      width: 160
    });
    this.topPanel = new Ext.Panel({
      region: "north",
      height: 60,
      border: false,
      defaults: {border: false, defaults: {border: false}}, // remove borders 2 levels deep
      items: [{
        html : '<h1>' + EasySDI_Map.lang.getLocal("FDR_TITLE_" + this.url.type ) + '</h1>'
      }, {
        layout: 'column',
        items: [{
          html: EasySDI_Map.lang.getLocal("Download_as") + ':',
          width: 200
        }, 
        this.downloadFormat, 
        {
          items: [{
            xtype: 'button',
            text: EasySDI_Map.lang.getLocal('Download'),
            width: 80,
            handler: this._downloadReport,
            scope: this
          }],
          width: 80
        },
        {// Add a hidden form that we can use to POST data when loading report windows
          hidden: true,
          xtype: 'panel',
          html: '<form id="postform" target="_blank" method="post">' +
              '<input id="postBody" name="body"/></form>'
        }]
      }]
    });
    this.sidePanel = new Ext.Panel({
      region: "east",
      width: 300,
      border: false
    });
    this.rptPanel.add(this.sidePanel);
    this.rptPanel.add(this.topPanel);
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
  },
  
  /**
   * Issue a request to the WPS service to download a report file for the current grid content.
   */
  _downloadReport: function() {
  	var dom = new OpenLayers.Format.Filter.v1_0_0().write(new OpenLayers.Filter.Comparison({
      type: OpenLayers.Filter.Comparison.EQUAL_TO,
      property: this.url.filterfield,
      value: this.featureId
    }));
    var filterText = this._XMLtoString(dom);    
    // Construct the appropriate request to WPS
    var transform, mimeType, identifier;
    if (this.downloadFormat.value===0 || this.downloadFormat.value==3) {
      transform='fop-default';
      mimeType='application/pdf';
    } else {
      transform='xslt-default';
      if (this.downloadFormat.value==1) {
        mimeType='application/rtf';
      } else {
        mimeType='text/csv';
      }
    }
    identifier = 'getListReport';    
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
'<wps:Data><wps:LiteralData>record</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>type</ows:Identifier>'+
'<wps:Data><wps:LiteralData>Record</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>spatial</ows:Identifier>'+
'<wps:Data><wps:LiteralData>0</wps:LiteralData>'+
'</wps:Data>'+
'</wps:Input>'+
'<wps:Input><ows:Identifier>wfsRequest</ows:Identifier>'+
'<wps:Data><wps:ComplexData schema="http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd" mimeType="text/xml" encoding="UTF-8">'+
'<wfs:GetFeature service="WFS" version="'+ componentParams.pubWfsVersion +'" outputFormat="GML2" xmlns:wfs="http://www.opengis.net/wfs" '+ 'xmlns:ogc="http://www.opengis.net/ogc" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" '+
'xsi:schemaLocation="http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/WFS-basic.xsd"> '+
'<wfs:Query typeName="' + componentParams.pubFeaturePrefix + ':' + this.url.type.replace('{geom}','') + '">' +
this._getProperties() +
filterText +
'</wfs:Query></wfs:GetFeature></wps:ComplexData></wps:Data>'+
'</wps:Input>'+
'</wps:DataInputs>'+
'<wps:ResponseForm>'+
'<wps:RawDataOutput mimeType="' + mimeType + '"><ows:Identifier>' + transform + '</ows:Identifier></wps:RawDataOutput>'+
'</wps:ResponseForm>'+
'</wps:Execute>';
    document.forms.postform.action=componentParams.wpsReportsUrl; 
    document.forms.postform.submit();
  },

  /**
  * Retrieve a list of properties for inserting into a WFS request.
  */
  _getProperties: function() {
    var props = '';
    Ext.each(SData.attrs[this.url.type], function(attr) {  
      if (attr.visible!==false) {	
        props += '<ogc:PropertyName>' + attr.name + '</ogc:PropertyName>';
      }
    }, this);
    return props;
  },

  /**
   * Adds an overview map to the right of the report, which shows the supplied feature as an overlay.
   */
  displayGeom: function(feature) {
    // Determine what base layer should be enabled
    var savedLayerId = Ext.state.Manager.get('baseLayerState', false);
    var baseLayer = null;
    Ext.each(SData.baseLayers, function(layer) {
      if (baseLayer === null && (savedLayerId == layer.id || !savedLayerId)) {
        baseLayer = layer;
      }
    });    
    var bounds = baseLayer.maxExtent;
    this.mapPanel = new GeoExt.MapPanel({
      map: {
        projection: componentParams.projection,
        minExtent: bounds,
        maxExtent: bounds,
        minScale: 262,
        maxScale: 262,
        units: "m",
        numZoomLevels: 1
      },
      width: 260,
      height: 380,
      frame: true,
      style: "margin-left: 20px"
    });

    this.sidePanel.add(this.mapPanel);

    var WMSoptions = {      
      basemapscontentid: baseLayer.id,
      LAYERS: baseLayer.layers,
      SERVICE: baseLayer.url_type,
      VERSION: baseLayer.version,
      STYLES: '',
      SRS: baseLayer.projection,
      FORMAT: baseLayer.imageFormat,
      tiled: 'true'
    };    

    var l = new OpenLayers.Layer.WMS(baseLayer.name, baseLayer.url,
      WMSoptions
    );
    this.mapPanel.map.addLayer(l);

    // Add a feature and layer to display this report item's geometry info
    var dist;
    if (feature.geometry===null) {
    	var filter=new OpenLayers.Filter.Comparison({
    		type: OpenLayers.Filter.Comparison.LIKE,
				property: "lineage_keylist",
				value: this.url.filter
		  });
		  var geom=SData.detailsReportGeoms[SData.searchLayer.featureType.replace('{geom}','')];
		  var featureType = SData.searchLayer.featureType.replace('{geom}',geom); 		  		
		  var dom = new OpenLayers.Format.Filter.v1_0_0().write(filter);
      var filterText = this._XMLtoString(dom);
      var WMSoptions = {
              VERSION: componentParams.pubWmsVersion,
              LAYERS: featureType,
              SRS: componentParams.projection,
              TRANSPARENT: true, 
              FILTER: filterText,
              GEOMETRYNAME: geom,
              STYLES: SData.searchLayer.styles[0]
                       	
      };
      
      dist = new OpenLayers.Layer.WMS(layer.name, componentParams.pubWmsUrl,
            WMSoptions,
            {
              isBaseLayer: false              
            }
      ); 
    } else {
    	var style = OpenLayers.Util.extend({}, OpenLayers.Feature.Vector.style["default"]);
    	dist = new OpenLayers.Layer.Vector('', {style: style});
      dist.addFeatures([feature]);
    }
    this.mapPanel.map.addLayer(dist);

    // Hide the panzoom control
    this.mapPanel.map.removeControl(this.mapPanel.map.controls[1]);
    // And prevent attempts to pan by deactivating the navigate control
    this.mapPanel.map.controls[0].deactivate();
  },

  /**
   * Callback for comment posting. Refresh the comments panel.
   */
  handleCommentPost: function(evt) {
    if (!evt.success()) {
      alert(evt.priv.responseXML.firstChild.textContent);
    }
    this.getComments();
  }

});

Ext.mixin(EasySDI_Map.FeatureDetails, EasySDI_Map.FeatureDetailsHelper);