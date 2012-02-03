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

// Sadly, the Ext extend method doesn't really work well here, so we implement a lightweight mixin method.

Ext.mixin = function(source, destination)
{
  // Start at first argument
  for (var i = 1; i < arguments.length; i++)
  {
    if (typeof arguments[i]!=="undefined") {
      for (var property in arguments[i].prototype)
      {
        source.prototype[property] = arguments[i].prototype[property];
      }
    }
  }
};

// 'merge' function - similar to $.extend, merges changes into an array or similar
Ext.merge = function(source, destination)
{
	var copy, property;
  for (var i = 1; i < arguments.length; i++)
  {
    for (property in arguments[i])
    {
      copy = arguments[i][property];
      if (copy !== undefined) {
        source[property] = copy;
      }
    }
  }
  return source;
};

Ext.namespace("EasySDI_Map");
Ext.namespace("EasySDI_Map.data");

EasySDI_Map.data.recordize = function(attrs){
  var records = [];
  var mapping = {
    "double": "float",
    "string": "string",
    "int": "int",
    "MultiSurfacePropertyType": "auto"
  };

  for (var i = 0; i < attrs.length; i++){
    var a = attrs[i];
    var k = a.type.replace(/.*:/, '');
    if (mapping[k]){
        var thisRecord = {
                name: a.name,
                ping: a.name,
                type: mapping[k]
        };
        if (a.mapping) {
          thisRecord.mapping = a.mapping;
        }
        records.push(thisRecord);
    }
  }

  return Ext.data.Record.create(records);
};

// Rewritten store to apply defaults

EasySDI_Map.WfsStore = Ext.extend(Ext.data.Store, {
  // Defaults to apply to config
  _defaults : {
    remoteSort : false
  },  

  constructor : function(config, wfsconfig)
  {
    // Things are a bit messy here, and how best to tidy it depends on exactly what needs to be
    // configured automatically and what needs to be overridable. General TODO to tidy this up,
    // then.
    var settings = {};
    // Merge in wfs settings
    var wfsDefaults = {}
    if(componentParams.pubFeaturePrefix)
    {
    	wfsDefaults = {
    		    url : componentParams.proxiedPubWfsUrl,
    		    featureNS : componentParams.pubFeatureNS,
    		    featurePrefix : componentParams.pubFeaturePrefix
    		  };
    }
    else
    {
    	wfsDefaults = {
    		    url : componentParams.proxiedPubWfsUrl,
    		    featureNS : componentParams.pubFeatureNS
    		  };
    }

    var wfsSettings = Ext.merge({}, wfsDefaults, wfsconfig);

    var proto = null;
    if(wfsSettings.featurePrefix)
    {
    	proto = new OpenLayers.Protocol.WFS.v1_1_0({
    	      url: wfsSettings.url,
    	      featureType: wfsSettings.featureType,
    	      featureNS: wfsSettings.featureNS,
    	      featurePrefix: wfsSettings.featurePrefix,
    	      filter: wfsSettings.filter,
    	      srsName: wfsSettings.srsName,
    	      maxFeatures: wfsSettings.maxFeatures,
    	      format: new OpenLayers.Format.WFST.v1_0_0_Sortable({
    	          version: "1.0.0",
    	          featureType: wfsSettings.featureType,
    	          featureNS: wfsSettings.featureNS,
    	          featurePrefix: wfsSettings.featurePrefix,
    	          srsName: wfsSettings.srsName
    	        })
    	    });
    }
    else
    {
    	proto = new OpenLayers.Protocol.WFS.v1_1_0({
    	      url: wfsSettings.url,
    	      featureType: wfsSettings.featureType,
    	      featureNS: wfsSettings.featureNS,
    	      filter: wfsSettings.filter,
    	      srsName: wfsSettings.srsName,
    	      maxFeatures: wfsSettings.maxFeatures,
    	      format: new OpenLayers.Format.WFST.v1_0_0_Sortable({
    	          version: "1.0.0",
    	          featureType: wfsSettings.featureType,
    	          srsName: wfsSettings.srsName
    	        })
    	    });
    }

    settings.proxy = new GeoExt.data.ProtocolProxy({protocol: proto});
    settings.reader = new GeoExt.data.FeatureReader({}, EasySDI_Map.data.recordize(wfsconfig.fields));

    // Allow provided settings to override defaults created here
    Ext.merge(settings, this._defaults, config);

    this.queryField = wfsSettings.queryField || 'dummy';
    this.proxy = settings.proxy;
    this.featureType = wfsSettings.featureType;
    EasySDI_Map.WfsStore.superclass.constructor.apply(this, [settings]);
  },

  load : function(options){
      options = options || {};
      if(this.fireEvent("beforeload", this, options) !== false){
          this.storeOptions(options);
          var p = Ext.apply(options.params || {}, this.baseParams);
          if(this.sortInfo && this.remoteSort){
              var pn = this.paramNames;
              p[pn.sort] = this.sortInfo.field;
              p[pn.dir] = this.sortInfo.direction;
          }
          var filters = [];
          if(p.filter){
            filters.push(p.filter);
            delete p.filter;
          }
          if(p.query){
            filters.push(new OpenLayers.Filter.Comparison({
                  type: OpenLayers.Filter.Comparison.LIKE,
                  property: this.queryField,
                  value: p.query+"*",
                  matchCase: false
            }));
            delete p.query;
          }
          if (filters.length > 0) {
            options.filter = new OpenLayers.Filter.Logical({
                      type: OpenLayers.Filter.Logical.AND,
                      filters: filters
            });
          }
          this.proxy.load(p, this.reader, this.loadRecords, this, options);
          return true;
      } else {
        return false;
      }
  },
  // Called as a callback by the Reader during a load operation.
  loadRecords : function(o, options, success){
    if(o && success === true && this.extractFieldFromFID){
      var r = o.records;
      for(var i = r.length-1; i >=0; i--){
          // This assumes that the fid format is the featureTypefollowed by the PK, separated by a punctuation mark
          r[i].set(this.extractFieldFromFID, r[i].data.fid.substring(this.featureType.length+1));
      }
    }
    EasySDI_Map.WfsStore.superclass.loadRecords.apply(this, arguments);
  }

});

EasySDI_Map.ProtocolReaderObject = function(config) {
  Ext.apply(this, config);
};

EasySDI_Map.ProtocolReaderObject.prototype = {
  options: [],
  eventFunc: null,
  eventFuncScope: null,

  loadResponse: function(response) {
    if (response.success()) {
      var result = this.options.reader.read(response);
      this.eventFunc.call(this.eventFuncScope, "load", this.eventFuncScope, this.options, this.options.request.arg);
        this.options.request.callback.call(
            this.options.request.scope, result, this.options.request.arg, true);
    } else {
      this.eventFunc.call(this.eventFuncScope, "loadexception", this.eventFuncScope, this.options, response);
        this.options.request.callback.call(
            this.options.request.scope, null, this.options.request.arg, false);
    }
  }
};

EasySDI_Map.data.ProtocolMultiProxy = function(config) {
  EasySDI_Map.data.ProtocolMultiProxy.superclass.constructor.call(this);
    Ext.apply(this, config);
};

Ext.extend(EasySDI_Map.data.ProtocolMultiProxy, Ext.data.DataProxy, {
    /**
     * APIProperty: protoSpecList
     * [{protocol: <OpenLayers.Protocol>, reader: reader function}]
     * The array of protocols and associated readers used to fetch features.
     */
    protoSpecList: [],

    /**
     * APIProperty: abortPrevious
     * {Boolean} Whether to abort the previous request or not, defaults
     * to true.
     */
    abortPrevious: true,

    /**
     * Property: responses
     * [{<OpenLayers.Protocol.Response>}] An array of responses returned by
     * the read calls on the protocol list.
     */
    responses: [],

    /**
     * Method: load
     *
     * Parameters:
     * params - {Object} An object containing properties which are to be used
     *     as HTTP parameters for the request to the remote server.
     * reader - legacy from single source data proxys - can't be used in multiple
     *     source situations as parameters are dependent on the individual proxy
     * callback - {Function} The function into which to pass the block of
     *     Ext.data.Records. The function is passed the Record block object,
     *     the "args" argument passed to the load function, and a boolean
     *     success indicator
     * scope - {Object} The scope in which to call the callback
     * arg - {Object} An optional argument which is passed to the callback
     *     as its second parameter.
     */
    load: function(params, reader, callback, scope, arg) {
        if (this.fireEvent("beforeload", this, params) !== false) {
          if (this.abortPrevious) {
                this.abortRequest();
            }
            this.responses = [];
            Ext.each(this.protoSpecList, function(protoSpec) {
              var args = Ext.merge({},arg);
              args.fields = protoSpec.fields;
              var cb = new EasySDI_Map.ProtocolReaderObject({
                options: {
                      params: params || {},
                      request: {
                          callback: callback, // this is usually loadRecords
                          scope: scope,
                          arg: args
                      },
                      reader: protoSpec.reader
                    },
                eventFunc: this.fireEvent,
                eventFuncScope: this
              });
              var options = {
                params: params,
                callback: cb.loadResponse,
                scope: cb,
                filter: new OpenLayers.Filter.Comparison({
                      type: OpenLayers.Filter.Comparison.LIKE,
                      property: protoSpec.filterField,
                      value: "*"+params.query+"*",
                      matchCase: false
                 })
              };
              Ext.applyIf(options, arg);
              this.responses.push(protoSpec.protocol.read(options));
            }, this);
        } else {
           callback.call(scope || this, null, arg, false);
        }
    },

    /**
     * Method: abortRequest
     * Called to abort any ongoing request.
     */
    abortRequest: function() {
        // FIXME really we should rely on the protocol itself to
        // cancel the request, the Protocol class in OpenLayers
        // 2.7 does not expose a cancel() method
        if (this.responses) {
            Ext.each(this.responses, function(response) {
              if (response.priv &&
                  typeof response.priv.abort == "function") {
                  response.priv.abort();
              }
            }, this);
            this.responses = [];
        }
    },

    /**
     * Method: loadResponse
     * Handle response from the protocol
     *
     * Parameters:
     * o - {Object}
     * response - {<OpenLayers.Protocol.Response>}
     */
    loadResponse: function(o, response) {
        if (response.success()) {
            var result = o.reader.read(response);
            this.fireEvent("load", this, o, o.request.arg);
            o.request.callback.call(
               o.request.scope, result, o.request.arg, true);
        } else {
            this.fireEvent("loadexception", this, o, response);
            o.request.callback.call(
                o.request.scope, null, o.request.arg, false);
        }
    }
});

EasySDI_Map.WfsMultiStore = Ext.extend(Ext.data.Store, {
    // Defaults to apply to config
    _defaults : {
      remoteSort : false
    },
    constructor : function(config, wfsmulticonfig)
    {
      var protolist = [];
      var settings;
      var wfsSettings, wfsDefaults={
        srsName: componentParams.projection
      };
      var proto;
      Ext.each(wfsmulticonfig.wfslist, function(wfsconfig) {
        settings = {};
        // Merge in wfs settings
        wfsSettings = Ext.merge({}, wfsDefaults, wfsconfig);

        if(wfsSettings.featurePrefix)
        {
        	 proto = new OpenLayers.Protocol.WFS.v1_1_0({
                 url: wfsSettings.url,
                 featureType: wfsSettings.featureType,
                 featurePrefix: wfsSettings.featurePrefix,
                 featureNS: wfsSettings.featureNS,
                 maxFeatures: wfsSettings.maxFeatures,
                 format: new OpenLayers.Format.WFST.v1_0_0_Sortable({
                     version: "1.0.0",
                     featureType: wfsSettings.featureType,
                     featureNS: componentParams.pubFeatureNS,
                     featurePrefix: wfsSettings.featurePrefix,
                     srsName: wfsSettings.srsName
                   })
               });
        }
        else
        {
        	 proto = new OpenLayers.Protocol.WFS.v1_1_0({
                 url: wfsSettings.url,
                 featureType: wfsSettings.featureType,
                 featureNS: wfsSettings.featureNS,
                 maxFeatures: wfsSettings.maxFeatures,
                 format: new OpenLayers.Format.WFST.v1_0_0_Sortable({
                     version: "1.0.0",
                     featureType: wfsSettings.featureType,
                     srsName: wfsSettings.srsName
                   })
               });
        }
       

        protolist.push({protocol: proto,
                      reader: new GeoExt.data.FeatureReader({}, EasySDI_Map.data.recordize(wfsconfig.fields)),
                      fields: wfsconfig.fields,
                      filterField: wfsconfig.filterField
        });
      }, this);

      settings.proxy = new EasySDI_Map.data.ProtocolMultiProxy({protoSpecList: protolist});
      settings.reader = null;

      // Allow provided settings to override defaults created here
      Ext.merge(settings, this._defaults, config);

      this.proxy = settings.proxy;
      EasySDI_Map.WfsMultiStore.superclass.constructor.apply(this, [settings]);
    },

      // private
    // Called as a callback by the Reader during a load operation.
    loadRecords : function(o, options, success){
            if(!o || success === false){
                if(success !== false){
                    this.fireEvent("load", this, [], options);
                }
                if(options.callback){
                    options.callback.call(options.scope || this, [], options, false);
                }
                return;
            }
            var r = o.records, t = o.totalRecords || r.length, recsToAdd = [];
            for(var i = r.length-1; i >=0; i--){
              // at this point, because the mapping field has been set in recordise, the transfer of
              // any value from the "mapping" field to the "name" field will already have been done.
              // This is done in the featureReader
              // However this does not work for "fid", as this is not a normal field.
              for (var k=0, lenK = options.fields.length; k < lenK; k++){
                if (options.fields[k].mapping && (options.fields[k].mapping != options.fields[k].name) && (options.fields[k].mapping == 'fid')) {
                  r[i].set(options.fields[k].name, r[i].data[options.fields[k].mapping]);
                }
                if(options.fields[k].prefix) {
                  r[i].set(options.fields[k].name, options.fields[k].prefix+':'+r[i].data[options.fields[k].name]);
                }
                if (options.fields[k].append) {
                  r[i].set(options.fields[k].name, r[i].data[options.fields[k].name]+options.fields[k].append);
                }
              }
              recsToAdd.push(r[i]);
            }
            this.totalLength = Math.max(t, this.data.length+recsToAdd.length);
            this.add(recsToAdd);
            this.totalLength = t;
//            this.applySort();
            this.fireEvent("datachanged", this);
            this.fireEvent("load", this, recsToAdd, options);
            if(options.callback){
                options.callback.call(options.scope || this, recsToAdd, options, true);
            }
        },

    load : function(options){
        this.removeAll();
            options = options || {};
            if(this.fireEvent("beforeload", this, options) !== false){
                this.storeOptions(options);
                var p = Ext.apply(options.params || {}, this.baseParams);
                if(this.sortInfo && this.remoteSort){
                    var pn = this.paramNames;
                    p[pn.sort] = this.sortInfo.field;
                    p[pn.dir] = this.sortInfo.direction;
                }
                this.proxy.load(p, this.reader, this.loadRecords, this, options);
                return true;
            } else {
              return false;
            }
        }

});

// The WfsStoreWithHold class has the following extras on top of the WfsStore class:
// It tags the fid of removed records, to allow a conditional reinstatement at a later
// date: attempts to reinstate a record which was not part of the filtered set will be ignored
// It will only load records if they are not present in an 'exclusion' store
//
// These additions are to allow it to be used in a combobox pair (see advanced search
// survey tab)

EasySDI_Map.WfsStoreWithHold = Ext.extend(EasySDI_Map.WfsStore, {

    constructor : function(config, wfsconfig)
    {
      this.holdRecordList = [];
      this.exclusionStore = config.exclusionStore;
      EasySDI_Map.WfsStoreWithHold.superclass.constructor.apply(this, arguments);
    },


    load : function(options){
        this.holdRecordList = [];
        return EasySDI_Map.WfsStoreWithHold.superclass.load.apply(this, arguments);
    },

      // Called as a callback by the Reader during a load operation.
      loadRecords : function(o, options, success){
          if(!o || success === false){
              if(success !== false){
                  this.fireEvent("load", this, [], options);
              }
              if(options.callback){
                  options.callback.call(options.scope || this, [], options, false);
              }
              return;
          }
          var j, r = o.records, recsToAdd = [];
          for(var i = 0, recFound = false; i < r.length; i++, recFound = false){
              for (j = 0; j < this.exclusionStore.data.items.length; j++){
                if (r[i].data.fid == this.exclusionStore.data.items[j].data.fid) {
                  recFound = true;
                }
              }
              this.holdRecordList[r[i].data.fid] = recFound;
              if (recFound === false) {
                recsToAdd.push(r[i]);
              }
          }
          if(this.pruneModifiedRecords){
              this.modified = [];
          }
          for(i = 0; i < recsToAdd.length; i++){
            recsToAdd[i].join(this);
          }
          if(this.snapshot){
              this.data = this.snapshot;
              delete this.snapshot;
          }
          this.data.clear();
          this.data.addAll(recsToAdd);
          this.totalLength = recsToAdd.length;
          this.applySort();
          this.fireEvent("datachanged", this);
          this.fireEvent("load", this, recsToAdd, options);
          if(options.callback){
              options.callback.call(options.scope || this, recsToAdd, options, true);
          }
      },

    removeAndHold : function (records) {
      for (var i = 0; i < records.length; i++) {
              this.holdRecordList[records[i].data.fid]=true;
              this.remove(records[i]);
      }
    },

    reinstateFromHold : function (records) {
      for (var i = 0; i < records.length; i++) {
        if (this.holdRecordList[records[i].data.fid]) { // would be undefined if not in filtered list
          this.add(records[i]);
          this.holdRecordList[records[i].data.fid]=false;
        }
      }
     }
  });


/**
 * Class: OpenLayers.Format.WMC.v1_1_0_WithWFS
 * Instances of OpenLayers.Format.WMC.v1_1_0_WithWFS are used to process data in WMC
 *     format, with extensions to handle WFS layers
 *     
 * WARNING: this is not a complete implementation, but does just enough for our purposes.
 * 2nd WARNING: this only stores visible layers, in order to keep the size of the text down.
 * 
 * Inherits from:
 *  - <OpenLayers.Format.WMC.v1_1_0>
 */
OpenLayers.Format.WMC.v1_1_0_WithWFS = OpenLayers.Class(
  OpenLayers.Format.WMC.v1_1_0, {

    restrictToLayersInTree: null,

    initialize: function(options) {
      OpenLayers.Format.WMC.v1_1_0.prototype.initialize.apply(this, [options]);
    },

    write_wmc_LayerList:function(context){
      var list=this.createElementDefaultNS("LayerList");
      var layer;
      var treeRootNode;
      
      if (this.restrictToLayersInTree) {
        treeRootNode = this.restrictToLayersInTree.getRootNode();
      } else {
        treeRootNode = null;
      }
      for(var i=0,len=context.layersContext.length;i<len;++i){
        layer=context.layersContext[i];
        if(layer.visibility && this._checkTree(layer, treeRootNode)) { // Only include layers that are visible and in restriction tree.
          if(layer instanceof OpenLayers.Layer.WMS){
            list.appendChild(this.write_wmc_Layer(layer));
          } else {
            // Skip the selection layer, which will have no params
            if (layer.params!=undefined) {
              list.appendChild(this.write_wmc_wfs_Layer(layer));
            }
          }
        }
      }
      return list;
    },

    _checkTree: function(layer, node) {
      if(!node) return true;
      if(node.leaf){
        if(layer.id == node.layer.id) {
          return true;
        }
      } else {
        for(var i = 0; i < node.childNodes.length; i++) {
          if(this._checkTree(layer, node.childNodes[i])){
            return true;
          }
        }
      }
      return false;
    },
    
    // As this is a "hack", store a cut down version of the layer data, sufficient for
    // our purposes at the moment.
    write_wmc_wfs_Layer:function(layer){
      var node=this.createElementDefaultNS("Layer",null,{
                queryable:layer.queryable?"1":"0",
                hidden:layer.visibility?"0":"1"});
      node.appendChild(this.write_wfs_wmc_Server(layer));
      node.appendChild(this.createElementDefaultNS("Title",layer.name));
      return node;
    },

    write_wfs_wmc_Server:function(layer){
      var node=this.createElementDefaultNS("Server");
      this.setAttributes(node,{service:"OGC:WFS",
                version:layer.params.VERSION});
      node.appendChild(this.write_wmc_OnlineResource(layer.url));
      return node;
    },

    CLASS_NAME: "OpenLayers.Format.WMC.v1_1_0_WithWFS"
});
