/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Format/WFSDescribeFeatureType.js
 */

/* api: (define)
 *  module = GeoExt.data
 *  class = AttributeReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: AttributeReader(meta, recordType)
 *  
 *      :arg meta: ``Object`` Reader configuration.
 *      :arg recordType: ``Array or Ext.data.Record`` An array of field
 *          configuration objects or a record object.
 *
 *      Create a new attributes reader object.
 *      
 *      Valid meta properties:
 *      
 *      * format - ``OpenLayers.Format`` A parser for transforming the XHR response
 *        into an array of objects representing attributes.  Defaults to
 *        an ``OpenLayers.Format.WFSDescribeFeatureType`` parser.
 *      * ignore - ``Object`` Properties of the ignore object should be field names.
 *        Values are either arrays or regular expressions.
 *      * feature - ``OpenLayers.Feature.Vector`` A vector feature. If provided
 *        records created by the reader will include a field named "value"
 *        referencing the attribute value as set in the feature.
 */
GeoExt.data.AttributeReader = function(meta, recordType) {
    meta = meta || {};
    if(!meta.format) {
        meta.format = new OpenLayers.Format.WFSDescribeFeatureType();
    }
    GeoExt.data.AttributeReader.superclass.constructor.call(
        this, meta, recordType || meta.fields
    );
    if(meta.feature) {
        this.recordType.prototype.fields.add(new Ext.data.Field("value"));
    }
};

Ext.extend(GeoExt.data.AttributeReader, Ext.data.DataReader, {

    /** private: method[read]
     *  :arg request: ``Object`` The XHR object that contains the parsed doc.
     *  :return: ``Object``  A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Records``.
     *  
     *  This method is only used by a DataProxy which has retrieved data from a
     *  remote server.
     */
    read: function(request) {
        var data = request.responseXML;
        if(!data || !data.documentElement) {
            data = request.responseText;
        }
        return this.readRecords(data);
    },

    /** private: method[readRecords]
     *  :arg data: ``DOMElement or String or Array`` A document element or XHR
     *      response string.  As an alternative to fetching attributes data from
     *      a remote source, an array of attribute objects can be provided given
     *      that the properties of each attribute object map to a provided field
     *      name.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Records``.
     *  
     *  Create a data block containing Ext.data.Records from an XML document.
     */
    readRecords: function(data) {
        var attributes;
        if(data instanceof Array) {
            attributes = data;
        } else {
            // only works with one featureType in the doc
            var output = this.meta.format.read(data);
            if (!!output.error) {
                throw new Ext.data.DataReader.Error("invalid-response", output.error);
            }
            attributes = output.featureTypes[0].properties;
        }
        var feature = this.meta.feature;
        var recordType = this.recordType;
        var fields = recordType.prototype.fields;
        var numFields = fields.length;
        var attr, values, name, record, ignore, value, field, records = [];
        for(var i=0, len=attributes.length; i<len; ++i) {
            ignore = false;
            attr = attributes[i];
            values = {};
            for(var j=0; j<numFields; ++j) {
                field = fields.items[j];
                name = field.name;
                value = field.convert(attr[name]);
                if(this.ignoreAttribute(name, value)) {
                    ignore = true;
                    break;
                }
                values[name] = value;
            }
            if(feature) {
                value = feature.attributes[values["name"]];
                if(value !== undefined) {
                    if(this.ignoreAttribute("value", value)) {
                        ignore = true;
                    } else {
                        values["value"] = value;
                    }
                }
            }
            if(!ignore) {
                records[records.length] = new recordType(values);
            }
        }

        return {
            success: true,
            records: records,
            totalRecords: records.length
        };
    },

    /** private: method[ignoreAttribute]
     *  :arg name: ``String`` The field name.
     *  :arg value: ``String`` The field value.
     *
     *  :return: ``Boolean`` true if the attribute should be ignored.
     */
    ignoreAttribute: function(name, value) {
        var ignore = false;
        if(this.meta.ignore && this.meta.ignore[name]) {
            var matches = this.meta.ignore[name];
            if(typeof matches == "string") {
                ignore = (matches === value);
            } else if(matches instanceof Array) {
                ignore = (matches.indexOf(value) > -1);
            } else if(matches instanceof RegExp) {
                ignore = (matches.test(value));
            }
        }
        return ignore;
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/AttributeReader.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = AttributeStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/**
 * Function: GeoExt.data.AttributeStoreMixin
 *
 * This function generates a mixin object to be used when extending an Ext.data.Store
 * to create an attribute store.
 *
 * (start code)
 * var AttrStore = Ext.extend(Ext.data.Store, GeoExt.data.AttributeStoreMixin);
 * var store = new AttrStore();
 * (end)
 *
 * For convenience, a GeoExt.data.AttributeStore class is available as a
 * shortcut to the Ext.extend sequence in the above code snippet. The above
 * is equivalent to:
 * (start code)
 * var store = new GeoExt.data.AttributeStore();
 * (end)
 */
GeoExt.data.AttributeStoreMixin = function() {
    return {
        /** private */
        constructor: function(c) {
            c = c || {};
            arguments.callee.superclass.constructor.call(
                this,
                Ext.apply(c, {
                    proxy: c.proxy || (!c.data ?
                        new Ext.data.HttpProxy({url: c.url, disableCaching: false, method: "GET"}) :
                        undefined
                    ),
                    reader: new GeoExt.data.AttributeReader(
                        c, c.fields || ["name", "type", "restriction", {
                            name: "nillable", type: "boolean"
                        }, "annotation"]
                    )
                })
            );
            if(this.feature) {
                this.bind();
            }
        },

        /** private: method[bind]
         */
        bind: function() {
            this.on({
                "update": this.onUpdate,
                "load": this.onLoad,
                "add": this.onAdd,
                scope: this
            });
            var records = [];
            this.each(function(record) {
                records.push(record);
            });
            this.updateFeature(records);
        },

        /** private: method[onUpdate]
         *  :param store: ``Ext.data.Store``
         *  :param record: ``Ext.data.Record``
         *  :param operation: ``String``
         *
         *  Handler for store update event.
         */
        onUpdate: function(store, record, operation) {
            this.updateFeature([record]);
        },

        /** private: method[onLoad]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param options: ``Object``
         *
         *  Handler for store load event
         */
        onLoad: function(store, records, options) {
            // if options.add is true an "add" event was already
            // triggered, and onAdd already did the work of
            // adding the features to the layer.
            if(!options || options.add !== true) {
                this.updateFeature(records);
            }
        },

        /** private: method[onAdd]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param index: ``Number``
         *
         *  Handler for store add event
         */
        onAdd: function(store, records, index) {
            this.updateFeature(records);
        },

        /** private: method[updateFeature]
         *  :param records: ``Array(Ext.data.Record)``
         *
         *  Update feature from records.
         */
        updateFeature: function(records) {
            var feature = this.feature, layer = feature.layer;
            var i, len, record, name, value, oldValue, dirty;
            for(i=0,len=records.length; i<len; i++) {
                record = records[i];
                name = record.get("name");
                value = record.get("value");
                oldValue = feature.attributes[name];
                if(oldValue !== value) {
                    dirty = true;
                }
            }
            if(dirty && layer && layer.events &&
                        layer.events.triggerEvent("beforefeaturemodified",
                            {feature: feature}) !== false) {
                for(i=0,len=records.length; i<len; i++) {
                    record = records[i];
                    name = record.get("name");
                    value = record.get("value");
                    feature.attributes[name] = value;
                }
                layer.events.triggerEvent(
                    "featuremodified", {feature: feature});
                layer.drawFeature(feature);
            }
        }

    };
};

/** api: constructor
 *  .. class:: AttributeStore(config)
 *
 *      Small helper class to make creating stores for remotely-loaded attributes
 *      data easier. AttributeStore is pre-configured with a built-in
 *      ``Ext.data.HttpProxy`` and :class:`GeoExt.data.AttributeReader`.  The
 *      HttpProxy is configured to allow caching (disableCaching: false) and
 *      uses GET. If you require some other proxy/reader combination then you'll
 *      have to configure this with your own proxy or create a basic
 *      ``Ext.data.Store`` and configure as needed.
 */

/** api: config[format]
 *  ``OpenLayers.Format``
 *  A parser for transforming the XHR response into an array of objects
 *  representing attributes.  Defaults to an
 *  ``OpenLayers.Format.WFSDescribeFeatureType`` parser.
 */

/** api: config[fields]
 *  ``Array or Function``
 *  Either an array of field definition objects as passed to
 *  ``Ext.data.Record.create``, or a record constructor created using
 *  ``Ext.data.Record.create``.  Defaults to ``["name", "type", "restriction"]``.
 */

/** api: config[feature]
 *  ``OpenLayers.Feature.Vector``
 *  A vector feature. If provided, and if the reader is a
 *  :class:`GeoExt.data.AttributeReader` (the default), then records
 *  of this store will include a field named "value" referencing the
 *  corresponding attribute value in the feature. And if the "value"
 *  field of a record is updated the update will propagate to the
 *  corresponding feature attribute. Optional.
 */
GeoExt.data.AttributeStore = Ext.extend(
    Ext.data.Store,
    GeoExt.data.AttributeStoreMixin()
);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = FeatureRecord
 *  base_link = `Ext.data.Record <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Record>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: FeatureRecord
 *  
 *      A record that represents an ``OpenLayers.Feature.Vector``. This record
 *      will always have at least the following fields:
 *
 *      * state ``String``
 *      * fid ``String``
 *
 */
GeoExt.data.FeatureRecord = Ext.data.Record.create([
    {name: "feature"}, {name: "state"}, {name: "fid"}
]);

/** api: method[getFeature]
 *  :return: ``OpenLayers.Feature.Vector``
 *
 *  Gets the feature for this record.
 */
GeoExt.data.FeatureRecord.prototype.getFeature = function() {
    return this.get("feature");
};

/** api: method[setFeature]
 *  :param feature: ``OpenLayers.Feature.Vector``
 *
 *  Sets the feature for this record.
 */
GeoExt.data.FeatureRecord.prototype.setFeature = function(feature) {
    if (feature !== this.data.feature) {
        this.dirty = true;
        if (!this.modified) {
            this.modified = {};
        }
        if (this.modified.feature === undefined) {
            this.modified.feature = this.data.feature;
        }
        this.data.feature = feature;
        if (!this.editing){
            this.afterEdit();
        }
    }
};

/** api: classmethod[create]
 *  :param o: ``Array`` Field definition as in ``Ext.data.Record.create``. Can
 *      be omitted if no additional fields are required.
 *  :return: ``Function`` A specialized :class:`GeoExt.data.FeatureRecord`
 *      constructor.
 *  
 *  Creates a constructor for a :class:`GeoExt.data.FeatureRecord`, optionally
 *  with additional fields.
 */
GeoExt.data.FeatureRecord.create = function(o) {
    var f = Ext.extend(GeoExt.data.FeatureRecord, {});
    var p = f.prototype;

    p.fields = new Ext.util.MixedCollection(false, function(field) {
        return field.name;
    });

    GeoExt.data.FeatureRecord.prototype.fields.each(function(f) {
        p.fields.add(f);
    });

    if(o) {
        for(var i = 0, len = o.length; i < len; i++){
            p.fields.add(new Ext.data.Field(o[i]));
        }
    }

    f.getField = function(name) {
        return p.fields.get(name);
    };

    return f;
};

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/FeatureRecord.js
 * @require OpenLayers/Feature/Vector.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = FeatureReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace('GeoExt', 'GeoExt.data');

/** api: example
 *  Typical usage in a store:
 * 
 *  .. code-block:: javascript
 *     
 *      var store = new Ext.data.Store({
 *          reader: new GeoExt.data.FeatureReader({}, [
 *              {name: 'name', type: 'string'},
 *              {name: 'elevation', type: 'float'}
 *          ])
 *      });
 *      
 */

/** api: constructor
 *  .. class:: FeatureReader(meta, recordType)
 *   
 *      Data reader class to create an array of
 *      :class:`GeoExt.data.FeatureRecord` objects from an
 *      ``OpenLayers.Protocol.Response`` object for use in a
 *      :class:`GeoExt.data.FeatureStore` object.
 */
GeoExt.data.FeatureReader = function(meta, recordType) {
    meta = meta || {};
    if(!(recordType instanceof Function)) {
        recordType = GeoExt.data.FeatureRecord.create(
            recordType || meta.fields || {});
    }
    GeoExt.data.FeatureReader.superclass.constructor.call(
        this, meta, recordType);
};

Ext.extend(GeoExt.data.FeatureReader, Ext.data.DataReader, {

    /**
     * APIProperty: totalRecords
     * {Integer}
     */
    totalRecords: null,

    /** private: method[read]
     *  :param response: ``OpenLayers.Protocol.Response``
     *  :return: ``Object`` An object with two properties. The value of the
     *      ``records`` property is the array of records corresponding to
     *      the features. The value of the ``totalRecords" property is the
     *      number of records in the array.
     *      
     *  This method is only used by a DataProxy which has retrieved data.
     */
    read: function(response) {
        return this.readRecords(response.features);
    },

    /** api: method[readRecords]
     *  :param features: ``Array(OpenLayers.Feature.Vector)`` List of
     *      features for creating records
     *  :return: ``Object``  An object with ``records`` and ``totalRecords``
     *      properties.
     *  
     *  Create a data block containing :class:`GeoExt.data.FeatureRecord`
     *  objects from an array of features.
     */
    readRecords : function(features) {
        var records = [];

        if (features) {
            var recordType = this.recordType, fields = recordType.prototype.fields;
            var i, lenI, j, lenJ, feature, values, field, v;
            for (i = 0, lenI = features.length; i < lenI; i++) {
                feature = features[i];
                values = {};
                if (feature.attributes) {
                    for (j = 0, lenJ = fields.length; j < lenJ; j++){
                        field = fields.items[j];
                        if (/[\[\.]/.test(field.mapping)) {
                            try {
                                v = new Function("obj", "return obj." + field.mapping)(feature.attributes);
                            } catch(e){
                                v = field.defaultValue;
                            }
                        }
                        else {
                            v = feature.attributes[field.mapping || field.name] || field.defaultValue;
                        }
                        if (field.convert) {
                            v = field.convert(v, feature);
                        }
                        values[field.name] = v;
                    }
                }
                values.feature = feature;
                values.state = feature.state;
                values.fid = feature.fid;

                // newly inserted features need to be made into phantom records
                var id = (feature.state === OpenLayers.State.INSERT) ? undefined : feature.id;
                records[records.length] = new recordType(values, id);
            }
        }

        return {
            records: records,
            totalRecords: this.totalRecords != null ? this.totalRecords : records.length
        };
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/FeatureReader.js
 * @require OpenLayers/Feature/Vector.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = FeatureStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: FeatureStore
 *
 *      A store containing :class:`GeoExt.data.FeatureRecord` entries that
 *      optionally synchronizes with an ``OpenLayers.Layer.Vector``.
 */

/** api: example
 *  Sample code to create a store with features from a vector layer:
 *  
 *  .. code-block:: javascript
 *
 *      var store = new GeoExt.data.FeatureStore({
 *          layer: myLayer,
 *          features: myFeatures
 *      });
 */

/**
 * Class: GeoExt.data.FeatureStoreMixin
 * A store that synchronizes a features array of an {OpenLayers.Layer.Vector} with a
 * feature store holding {<GeoExt.data.FeatureRecord>} entries.
 * 
 * This class can not be instantiated directly. Instead, it is meant to extend
 * {Ext.data.Store} or a subclass of it:
 * (start code)
 * var store = new (Ext.extend(Ext.data.Store, new GeoExt.data.FeatureStoreMixin))({
 *     layer: myLayer,
 *     features: myFeatures
 * });
 * (end)
 * 
 * For convenience, a {<GeoExt.data.FeatureStore>} class is available as a
 * shortcut to the Ext.extend sequence in the above code snippet. The above
 * is equivalent to:
 * (start code)
 * var store = new GeoExt.data.FeatureStore({
 *     layer: myLayer,
 *     features: myFeatures
 * });
 * (end)
 */
GeoExt.data.FeatureStoreMixin = function() {
    return {
        /** api: config[layer]
         *  ``OpenLayers.Layer.Vector``  Layer to synchronize the store with.
         */
        layer: null,
        
        /** api: config[features]
         *  ``Array(OpenLayers.Feature.Vector)``  Features that will be added to the
         *  store (and the layer if provided).
         */

        /** api: config[reader]
         *  ``Ext.data.DataReader`` The reader used to produce records from objects
         *  features.  Default is :class:`GeoExt.data.FeatureReader`.
         */
        reader: null,

        /** api: config[featureFilter]
         *  ``OpenLayers.Filter`` This filter is evaluated before a feature
         *  record is added to the store.
         */
        featureFilter: null,
        
        /** api: config[initDir]
         *  ``Number``  Bitfields specifying the direction to use for the
         *  initial sync between the layer and the store, if set to 0 then no
         *  initial sync is done. Default is
         *  ``GeoExt.data.FeatureStore.LAYER_TO_STORE|GeoExt.data.FeatureStore.STORE_TO_LAYER``.
         */

        /** private */
        constructor: function(config) {
            config = config || {};
            config.reader = config.reader ||
                            new GeoExt.data.FeatureReader({}, config.fields);
            var layer = config.layer;
            delete config.layer;
            // 'features' option - is an alias 'data' option
            if (config.features) {
                config.data = config.features;
            }
            delete config.features;
            // "initDir" option
            var options = {initDir: config.initDir};
            delete config.initDir;
            arguments.callee.superclass.constructor.call(this, config);
            if(layer) {
                this.bind(layer, options);
            }
        },

        /** api: method[bind]
         *  :param layer: ``OpenLayers.Layer`` Layer that the store should be
         *      synchronized with.
         *  
         *  Bind this store to a layer instance, once bound the store
         *  is synchronized with the layer and vice-versa.
         */ 
        bind: function(layer, options) {
            if(this.layer) {
                // already bound
                return;
            }
            this.layer = layer;
            options = options || {};

            var initDir = options.initDir;
            if(options.initDir == undefined) {
                initDir = GeoExt.data.FeatureStore.LAYER_TO_STORE |
                          GeoExt.data.FeatureStore.STORE_TO_LAYER;
            }

            // create a snapshot of the layer's features
            var features = layer.features.slice(0);

            if(initDir & GeoExt.data.FeatureStore.STORE_TO_LAYER) {
                var records = this.getRange();
                for(var i=records.length - 1; i>=0; i--) {
                    this.layer.addFeatures([records[i].getFeature()]);
                }
            }

            if(initDir & GeoExt.data.FeatureStore.LAYER_TO_STORE) {
                this.loadData(features, true /* append */);
            }

            layer.events.on({
                "featuresadded": this.onFeaturesAdded,
                "featuresremoved": this.onFeaturesRemoved,
                "featuremodified": this.onFeatureModified,
                scope: this
            });
            this.on({
                "load": this.onLoad,
                "clear": this.onClear,
                "add": this.onAdd,
                "remove": this.onRemove,
                "update": this.onUpdate,
                scope: this
            });
        },

        /** api: method[unbind]
         *  Unbind this store from the layer it is currently bound.
         */
        unbind: function() {
            if(this.layer) {
                this.layer.events.un({
                    "featuresadded": this.onFeaturesAdded,
                    "featuresremoved": this.onFeaturesRemoved,
                    "featuremodified": this.onFeatureModified,
                    scope: this
                });
                this.un("load", this.onLoad, this);
                this.un("clear", this.onClear, this);
                this.un("add", this.onAdd, this);
                this.un("remove", this.onRemove, this);
                this.un("update", this.onUpdate, this);

                this.layer = null;
            }
        },
       
        /** api: method[getRecordFromFeature]
         *  :arg feature: ``OpenLayers.Vector.Feature``
         *  :returns: :class:`GeoExt.data.FeatureRecord` The record corresponding
         *      to the given feature.  Returns null if no record matches.
         *
         *  *Deprecated* Use getByFeature instead.
         *
         *  Get the record corresponding to a feature.
         */
        getRecordFromFeature: function(feature) {
            return this.getByFeature(feature) || null;
        },
        
        /** api: method[getByFeature]
         *  :arg feature: ``OpenLayers.Vector.Feature``
         *  :returns: :class:`GeoExt.data.FeatureRecord` The record corresponding
         *      to the given feature.  Returns undefined if no record matches.
         *
         *  Get the record corresponding to a feature.
         */
        getByFeature: function(feature) {
            var record;
            if(feature.state !== OpenLayers.State.INSERT) {
                record = this.getById(feature.id);
            } else {
                var index = this.findBy(function(r) {
                    return r.getFeature() === feature;
                });
                if(index > -1) {
                    record = this.getAt(index);
                }
            }
            return record;
        },
       
        /** private: method[onFeaturesAdded]
         *  Handler for layer featuresadded event
         */
        onFeaturesAdded: function(evt) {
            if(!this._adding) {
                var features = evt.features, toAdd = features;
                if(this.featureFilter) {
                    toAdd = [];
                    var i, len, feature;
                    for(var i=0, len=features.length; i<len; i++) {
                        feature = features[i];
                        if (this.featureFilter.evaluate(feature) !== false) {
                            toAdd.push(feature);
                        }
                    }
                }
                // add feature records to the store, when called with
                // append true loadData triggers an "add" event and
                // then a "load" event
                this._adding = true;
                this.loadData(toAdd, true /* append */);
                delete this._adding;
            }
        },
        
        /** private: method[onFeaturesRemoved]
         *  Handler for layer featuresremoved event
         */
        onFeaturesRemoved: function(evt){
            if(!this._removing) {
                var features = evt.features, feature, record, i;
                for(i=features.length - 1; i>=0; i--) {
                    feature = features[i];
                    record = this.getByFeature(feature);
                    if(record !== undefined) {
                        this._removing = true;
                        this.remove(record);
                        delete this._removing;
                    }
                }
            }
        },
        
        /** private: method[onFeatureModified]
         *  Handler for layer featuremodified event
         */
        onFeatureModified: function(evt) {
            if(!this._updating) {
                var feature = evt.feature;
                var record = this.getByFeature(feature);
                if(record !== undefined) {
                    record.beginEdit();
                    var attributes = feature.attributes;
                    if(attributes) {
                        var fields = this.recordType.prototype.fields;
                        for(var i=0, len=fields.length; i<len; i++) {
                            var field = fields.items[i];
                            var key = field.mapping || field.name;
                            if(key in attributes) {
                                record.set(field.name, field.convert(attributes[key]));
                            }
                        }
                    }
                    // the calls to set below won't trigger "update"
                    // events because we called beginEdit to start a
                    // "transaction", "update" will be triggered by
                    // endEdit
                    record.set("state", feature.state);
                    record.set("fid", feature.fid);
                    record.setFeature(feature);
                    this._updating = true;
                    record.endEdit();
                    delete this._updating;
                }
            }
        },

        /** private: method[addFeaturesToLayer]
         *  Given an array of records add features to the layer. This
         *  function is used by the onLoad and onAdd handlers.
         */
        addFeaturesToLayer: function(records) {
            var i, len, features;
            features = new Array((len=records.length));
            for(i=0; i<len; i++) {
                features[i] = records[i].getFeature();
            }
            if(features.length > 0) {
                this._adding = true;
                this.layer.addFeatures(features);
                delete this._adding;
            }
        },
       
        /** private: method[onLoad]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param options: ``Object``
         * 
         *  Handler for store load event
         */
        onLoad: function(store, records, options) {
            // if options.add is true an "add" event was already
            // triggered, and onAdd already did the work of 
            // adding the features to the layer.
            if(!options || options.add !== true) {
                this._removing = true;
                this.layer.removeFeatures(this.layer.features);
                delete this._removing;

                this.addFeaturesToLayer(records);
            }
        },
        
        /** private: method[onClear]
         *  :param store: ``Ext.data.Store``
         *      
         *  Handler for store clear event
         */
        onClear: function(store) {
            this._removing = true;
            this.layer.removeFeatures(this.layer.features);
            delete this._removing;
        },
        
        /** private: method[onAdd]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param index: ``Number``
         * 
         *  Handler for store add event
         */
        onAdd: function(store, records, index) {
            if(!this._adding) {
                // addFeaturesToLayer takes care of setting
                // this._adding to true and deleting it
                this.addFeaturesToLayer(records);
            }
        },
        
        /** private: method[onRemove]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param index: ``Number``
         *      
         *  Handler for store remove event
         */
        onRemove: function(store, record, index){
            if(!this._removing) {
                var feature = record.getFeature();
                if (this.layer.getFeatureById(feature.id) != null) {
                    this._removing = true;
                    this.layer.removeFeatures([record.getFeature()]);
                    delete this._removing;
                }
            }
        },

        /** private: method[onUpdate]
         *  :param store: ``Ext.data.Store``
         *  :param record: ``Ext.data.Record``
         *  :param operation: ``String``
         *
         *  Handler for update.
         */
        onUpdate: function(store, record, operation) {
            if(!this._updating) {
                /**
                  * TODO: remove this if the FeatureReader adds attributes
                  * for all fields that map to feature.attributes.
                  * In that case, it would be sufficient to check (key in feature.attributes). 
                  */
                var defaultFields = new GeoExt.data.FeatureRecord().fields;
                var feature = record.getFeature();
                if (feature.state !== OpenLayers.State.INSERT) {
                    feature.state = OpenLayers.State.UPDATE;
                }
                if(record.fields) {
                    var cont = this.layer.events.triggerEvent(
                        "beforefeaturemodified", {feature: feature}
                    );
                    if(cont !== false) {
                        var attributes = feature.attributes;
                        record.fields.each(
                            function(field) {
                                var key = field.mapping || field.name;
                                if (!defaultFields.containsKey(key)) {
                                    attributes[key] = record.get(field.name);
                                }
                            }
                        );
                        this._updating = true;
                        this.layer.events.triggerEvent(
                            "featuremodified", {feature: feature}
                        );
                        delete this._updating;
                        if (this.layer.getFeatureById(feature.id) != null) {
                            this.layer.drawFeature(feature);
                        }
                    }
                }
            }
        },

        /** private: method[destroy]
         */
        destroy: function() {
            this.unbind();
            GeoExt.data.FeatureStore.superclass.destroy.call(this);
        }

    };
};

GeoExt.data.FeatureStore = Ext.extend(
    Ext.data.Store,
    new GeoExt.data.FeatureStoreMixin
);

/**
 * Constant: GeoExt.data.FeatureStore.LAYER_TO_STORE
 * {Integer} Constant used to make the store be automatically updated
 * when changes occur in the layer.
 */
GeoExt.data.FeatureStore.LAYER_TO_STORE = 1;

/**
 * Constant: GeoExt.data.FeatureStore.STORE_TO_LAYER
 * {Integer} Constant used to make the layer be automatically updated
 * when changes occur in the store.
 */
GeoExt.data.FeatureStore.STORE_TO_LAYER = 2;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = LayerRecord
 *  base_link = `Ext.data.Record <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Record>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: LayerRecord
 *  
 *      A record that represents an ``OpenLayers.Layer``. This record
 *      will always have at least the following fields:
 *
 *      * title ``String``
 */
GeoExt.data.LayerRecord = Ext.data.Record.create([
    {name: "layer"},
    {name: "title", type: "string", mapping: "name"}
]);

/** api: method[getLayer]
 *  :return: ``OpenLayers.Layer``
 *
 *  Gets the layer for this record.
 */
GeoExt.data.LayerRecord.prototype.getLayer = function() {
    return this.get("layer");
};

/** api: method[setLayer]
 *  :param layer: ``OpenLayers.Layer``
 *
 *  Sets the layer for this record.
 */
GeoExt.data.LayerRecord.prototype.setLayer = function(layer) {
    if (layer !== this.data.layer) {
        this.dirty = true;
        if(!this.modified) {
            this.modified = {};
        }
        if(this.modified.layer === undefined) {
            this.modified.layer = this.data.layer;
        }
        this.data.layer = layer;
        if(!this.editing) {
            this.afterEdit();
        }
    }
};

/** api: method[clone]
 *  :param id: ``String`` (optional) A new Record id.
 *  :return: class:`GeoExt.data.LayerRecord` A new layer record.
 *  
 *  Creates a clone of this LayerRecord. 
 */
GeoExt.data.LayerRecord.prototype.clone = function(id) { 
    var layer = this.getLayer() && this.getLayer().clone(); 
    return new this.constructor( 
        Ext.applyIf({layer: layer}, this.data), 
        id || layer.id
    );
}; 

/** api: classmethod[create]
 *  :param o: ``Array`` Field definition as in ``Ext.data.Record.create``. Can
 *      be omitted if no additional fields are required.
 *  :return: ``Function`` A specialized :class:`GeoExt.data.LayerRecord`
 *      constructor.
 *  
 *  Creates a constructor for a :class:`GeoExt.data.LayerRecord`, optionally
 *  with additional fields.
 */
GeoExt.data.LayerRecord.create = function(o) {
    var f = Ext.extend(GeoExt.data.LayerRecord, {});
    var p = f.prototype;

    p.fields = new Ext.util.MixedCollection(false, function(field) {
        return field.name;
    });

    GeoExt.data.LayerRecord.prototype.fields.each(function(f) {
        p.fields.add(f);
    });

    if(o) {
        for(var i = 0, len = o.length; i < len; i++){
            p.fields.add(new Ext.data.Field(o[i]));
        }
    }

    f.getField = function(name) {
        return p.fields.get(name);
    };

    return f;
};

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerRecord.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = LayerReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt", "GeoExt.data");

/** api: example
 *  Sample using a reader to create records from an array of layers:
 * 
 *  .. code-block:: javascript
 *     
 *      var reader = new GeoExt.data.LayerReader();
 *      var layerData = reader.readRecords(map.layers);
 *      var numRecords = layerData.totalRecords;
 *      var layerRecords = layerData.records;
 */

/** api: constructor
 *  .. class:: LayerReader(meta, recordType)
 *  
 *      Data reader class to create an array of
 *      :class:`GeoExt.data.LayerRecord` objects from an array of 
 *      ``OpenLayers.Layer`` objects for use in a
 *      :class:`GeoExt.data.LayerStore` object.
 */
GeoExt.data.LayerReader = function(meta, recordType) {
    meta = meta || {};
    if(!(recordType instanceof Function)) {
        recordType = GeoExt.data.LayerRecord.create(
            recordType || meta.fields || {});
    }
    GeoExt.data.LayerReader.superclass.constructor.call(
        this, meta, recordType);
};

Ext.extend(GeoExt.data.LayerReader, Ext.data.DataReader, {

    /** private: property[totalRecords]
     *  ``Integer``
     */
    totalRecords: null,

    /** api: method[readRecords]
     *  :param layers: ``Array(OpenLayers.Layer)`` List of layers for creating
     *      records.
     *  :return: ``Object``  An object with ``records`` and ``totalRecords``
     *      properties.
     *  
     *  From an array of ``OpenLayers.Layer`` objects create a data block
     *  containing :class:`GeoExt.data.LayerRecord` objects.
     */
    readRecords : function(layers) {
        var records = [];
        if(layers) {
            var recordType = this.recordType, fields = recordType.prototype.fields;
            var i, lenI, j, lenJ, layer, values, field, v;
            for(i = 0, lenI = layers.length; i < lenI; i++) {
                layer = layers[i];
                values = {};
                for(j = 0, lenJ = fields.length; j < lenJ; j++){
                    field = fields.items[j];
                    v = layer[field.mapping || field.name] ||
                        field.defaultValue;
                    v = field.convert(v);
                    values[field.name] = v;
                }
                values.layer = layer;
                records[records.length] = new recordType(values, layer.id);
            }
        }
        return {
            records: records,
            totalRecords: this.totalRecords != null ? this.totalRecords : records.length
        };
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerReader.js
 * @include GeoExt/widgets/MapPanel.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = LayerStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** private: constructor
 *  .. class:: LayerStoreMixin
 *      A store that synchronizes a layers array of an {OpenLayers.Map} with a
 *      layer store holding {<GeoExt.data.LayerRecord>} entries.
 * 
 *      This class can not be instantiated directly. Instead, it is meant to
 *      extend ``Ext.data.Store`` or a subclass of it.
 */

/** private: example
 *  Sample code to extend a store with the LayerStoreMixin.
 *
 *  .. code-block:: javascript
 *  
 *      var store = new (Ext.extend(Ext.data.Store, new GeoExt.data.LayerStoreMixin))({
 *          map: myMap,
 *          layers: myLayers
 *      });
 * 
 *  For convenience, a :class:`GeoExt.data.LayerStore` class is available as a
 *  shortcut to the ``Ext.extend`` sequence in the above code snippet.
 */

GeoExt.data.LayerStoreMixin = function() {
    return {
        /** api: config[map]
         *  ``OpenLayers.Map``
         *  Map that this store will be in sync with. If not provided, the
         *  store will not be bound to a map.
         */
        
        /** api: property[map]
         *  ``OpenLayers.Map``
         *  Map that the store is synchronized with, if any.
         */
        map: null,
        
        /** api: config[layers]
         *  ``Array(OpenLayers.Layer)``
         *  Layers that will be added to the store (and the map, depending on the
         *  value of the ``initDir`` option.
         */
        
        /** api: config[initDir]
         *  ``Number``
         *  Bitfields specifying the direction to use for the initial sync between
         *  the map and the store, if set to 0 then no initial sync is done.
         *  Defaults to ``GeoExt.data.LayerStore.MAP_TO_STORE|GeoExt.data.LayerStore.STORE_TO_MAP``
         */

        /** api: config[fields]
         *  ``Array``
         *  If provided a custom layer record type with additional fields will be
         *  used. Default fields for every layer record are `layer`
         *  (``OpenLayers.Layer``) `title` (``String``). The value of this option is
         *  either a field definition objects as passed to the
         *  :meth:`GeoExt.data.LayerRecord.create` function or a
         *  :class:`GeoExt.data.LayerRecord` constructor created using
         *  :meth:`GeoExt.data.LayerRecord.create`.
         */

        /** api: config[reader]
         *  ``Ext.data.DataReader`` The reader used to produce
         *  :class:`GeoExt.data.LayerRecord` objects from ``OpenLayers.Layer``
         *  objects.  If not provided, a :class:`GeoExt.data.LayerReader` will be
         *  used.
         */
        reader: null,

        /** private: method[constructor]
         */
        constructor: function(config) {
            config = config || {};
            config.reader = config.reader ||
                            new GeoExt.data.LayerReader({}, config.fields);
            delete config.fields;
            // "map" option
            var map = config.map instanceof GeoExt.MapPanel ?
                      config.map.map : config.map;
            delete config.map;
            // "layers" option - is an alias to "data" option
            if(config.layers) {
                config.data = config.layers;
            }
            delete config.layers;
            // "initDir" option
            var options = {initDir: config.initDir};
            delete config.initDir;
            arguments.callee.superclass.constructor.call(this, config);
            
            this.addEvents(
                /** api:event[bind]
                 *  Fires when the store is bound to a map.
                 *
                 *  Listener arguments:
                 *  * :class:`GeoExt.data.LayerStore`
                 *  * ``OpenLayers.Map``
                 */
                "bind"
            );
            
            if(map) {
                this.bind(map, options);
            }
        },

        /** api: method[bind]
         *  :param map: ``OpenLayers.Map`` The map instance.
         *  :param options: ``Object``
         *  
         *  Bind this store to a map instance, once bound the store
         *  is synchronized with the map and vice-versa.
         */
        bind: function(map, options) {
            if(this.map) {
                // already bound
                return;
            }
            this.map = map;
            options = options || {};

            var initDir = options.initDir;
            if(options.initDir == undefined) {
                initDir = GeoExt.data.LayerStore.MAP_TO_STORE |
                          GeoExt.data.LayerStore.STORE_TO_MAP;
            }

            // create a snapshot of the map's layers
            var layers = map.layers.slice(0);

            if(initDir & GeoExt.data.LayerStore.STORE_TO_MAP) {
                this.each(function(record) {
                    this.map.addLayer(record.getLayer());
                }, this);
            }
            if(initDir & GeoExt.data.LayerStore.MAP_TO_STORE) {
                this.loadData(layers, true);
            }

            map.events.on({
                "changelayer": this.onChangeLayer,
                "addlayer": this.onAddLayer,
                "removelayer": this.onRemoveLayer,
                scope: this
            });
            this.on({
                "load": this.onLoad,
                "clear": this.onClear,
                "add": this.onAdd,
                "remove": this.onRemove,
                "update": this.onUpdate,
                scope: this
            });
            this.data.on({
                "replace" : this.onReplace,
                scope: this
            });
            this.fireEvent("bind", this, map);
        },

        /** api: method[unbind]
         *  Unbind this store from the map it is currently bound.
         */
        unbind: function() {
            if(this.map) {
                this.map.events.un({
                    "changelayer": this.onChangeLayer,
                    "addlayer": this.onAddLayer,
                    "removelayer": this.onRemoveLayer,
                    scope: this
                });
                this.un("load", this.onLoad, this);
                this.un("clear", this.onClear, this);
                this.un("add", this.onAdd, this);
                this.un("remove", this.onRemove, this);

                this.data.un("replace", this.onReplace, this);

                this.map = null;
            }
        },
        
        /** private: method[onChangeLayer]
         *  :param evt: ``Object``
         * 
         *  Handler for layer changes.  When layer order changes, this moves the
         *  appropriate record within the store.
         */
        onChangeLayer: function(evt) {
            var layer = evt.layer;
            var recordIndex = this.findBy(function(rec, id) {
                return rec.getLayer() === layer;
            });
            if(recordIndex > -1) {
                var record = this.getAt(recordIndex);
                if(evt.property === "order") {
                    if(!this._adding && !this._removing) {
                        var layerIndex = this.map.getLayerIndex(layer);
                        if(layerIndex !== recordIndex) {
                            this._removing = true;
                            this.remove(record);
                            delete this._removing;
                            this._adding = true;
                            this.insert(layerIndex, [record]);
                            delete this._adding;
                        }
                    }
                } else if(evt.property === "name") {
                    record.set("title", layer.name);
                } else {
                    this.fireEvent("update", this, record, Ext.data.Record.EDIT);
                }
            }
        },
       
        /** private: method[onAddLayer]
         *  :param evt: ``Object``
         *  
         *  Handler for a map's addlayer event
         */
        onAddLayer: function(evt) {
            if(!this._adding) {
                var layer = evt.layer;
                this._adding = true;
                this.loadData([layer], true);
                delete this._adding;
            }
        },
        
        /** private: method[onRemoveLayer]
         *  :param evt: ``Object``
         * 
         *  Handler for a map's removelayer event
         */
        onRemoveLayer: function(evt){
            //TODO replace the check for undloadDestroy with a listener for the
            // map's beforedestroy event, doing unbind(). This can be done as soon
            // as http://trac.openlayers.org/ticket/2136 is fixed.
            if(this.map.unloadDestroy) {
                if(!this._removing) {
                    var layer = evt.layer;
                    this._removing = true;
                    this.remove(this.getById(layer.id));
                    delete this._removing;
                }
            } else {
                this.unbind();
            }
        },
        
        /** private: method[onLoad]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param options: ``Object``
         * 
         *  Handler for a store's load event
         */
        onLoad: function(store, records, options) {
            if (!Ext.isArray(records)) {
                records = [records];
            }
            if (options && !options.add) {
                this._removing = true;
                for (var i = this.map.layers.length - 1; i >= 0; i--) {
                    this.map.removeLayer(this.map.layers[i]);
                }
                delete this._removing;

                // layers has already been added to map on "add" event
                var len = records.length;
                if (len > 0) {
                    var layers = new Array(len);
                    for (var j = 0; j < len; j++) {
                        layers[j] = records[j].getLayer();
                    }
                    this._adding = true;
                    this.map.addLayers(layers);
                    delete this._adding;
                }
            }
        },
        
        /** private: method[onClear]
         *  :param store: ``Ext.data.Store``
         * 
         *  Handler for a store's clear event
         */
        onClear: function(store) {
            this._removing = true;
            for (var i = this.map.layers.length - 1; i >= 0; i--) {
                this.map.removeLayer(this.map.layers[i]);
            }
            delete this._removing;
        },
        
        /** private: method[onAdd]
         *  :param store: ``Ext.data.Store``
         *  :param records: ``Array(Ext.data.Record)``
         *  :param index: ``Number``
         * 
         *  Handler for a store's add event
         */
        onAdd: function(store, records, index) {
            if(!this._adding) {
                this._adding = true;
                var layer;
                for(var i=records.length-1; i>=0; --i) {
                    layer = records[i].getLayer();
                    this.map.addLayer(layer);
                    if(index !== this.map.layers.length-1) {
                        this.map.setLayerIndex(layer, index);
                    }
                }
                delete this._adding;
            }
        },
        
        /** private: method[onRemove]
         *  :param store: ``Ext.data.Store``
         *  :param record: ``Ext.data.Record``
         *  :param index: ``Number``
         * 
         *  Handler for a store's remove event
         */
        onRemove: function(store, record, index){
            if(!this._removing) {
                var layer = record.getLayer();
                if (this.map.getLayer(layer.id) != null) {
                    this._removing = true;
                    this.removeMapLayer(record);
                    delete this._removing;
                }
            }
        },
        
        /** private: method[onUpdate]
         *  :param store: ``Ext.data.Store``
         *  :param record: ``Ext.data.Record``
         *  :param operation: ``Number``
         * 
         *  Handler for a store's update event
         */
        onUpdate: function(store, record, operation) {
            if(operation === Ext.data.Record.EDIT) {
                if (record.modified && record.modified.title) {
                    var layer = record.getLayer();
                    var title = record.get("title");
                    if(title !== layer.name) {
                        layer.setName(title);
                    }
                }
            }
        },

        /** private: method[removeMapLayer]
         *  :param record: ``Ext.data.Record``
         *  
         *  Removes a record's layer from the bound map.
         */
        removeMapLayer: function(record){
            this.map.removeLayer(record.getLayer());
        },

        /** private: method[onReplace]
         *  :param key: ``String``
         *  :param oldRecord: ``Object`` In this case, a record that has been
         *      replaced.
         *  :param newRecord: ``Object`` In this case, a record that is replacing
         *      oldRecord.

         *  Handler for a store's data collections' replace event
         */
        onReplace: function(key, oldRecord, newRecord){
            this.removeMapLayer(oldRecord);
        },
        
        /** api: method[getByLayer]
         *  :param layer: ``OpenLayers.Layer``
         *  :return: :class:`GeoExt.data.LayerRecord` or undefined if not found
         *  
         *  Get the record for the specified layer
         */
        getByLayer: function(layer) {
            var index = this.findBy(function(r) {
                return r.getLayer() === layer;
            });
            if(index > -1) {
                return this.getAt(index);
            }
        },
        
        /** private: method[destroy]
         */
        destroy: function() {
            this.unbind();
            GeoExt.data.LayerStore.superclass.destroy.call(this);
        }
    };
};

/** api: example
 *  Sample to create a new store containing a cache of
 *  :class:`GeoExt.data.LayerRecord` instances derived from map layers.
 *
 *  .. code-block:: javascript
 *  
 *      var store = new GeoExt.data.LayerStore({
 *          map: myMap,
 *          layers: myLayers
 *      });
 */

/** api: constructor
 *  .. class:: LayerStore
 *
 *      A store that contains a cache of :class:`GeoExt.data.LayerRecord`
 *      objects.
 */
GeoExt.data.LayerStore = Ext.extend(
    Ext.data.Store,
    new GeoExt.data.LayerStoreMixin
);

/**
 * Constant: GeoExt.data.LayerStore.MAP_TO_STORE
 * {Integer} Constant used to make the store be automatically updated
 * when changes occur in the map.
 */
GeoExt.data.LayerStore.MAP_TO_STORE = 1;

/**
 * Constant: GeoExt.data.LayerStore.STORE_TO_MAP
 * {Integer} Constant used to make the map be automatically updated
 * when changes occur in the store.
 */
GeoExt.data.LayerStore.STORE_TO_MAP = 2;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/MapPanel.js
 * @require OpenLayers/Util.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = ScaleStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: ScaleStore
 *
 *      A store that contains a cache of available zoom levels.  The store can
 *      optionally be kept synchronized with an ``OpenLayers.Map`` or
 *      :class:`GeoExt.MapPanel` object.
 *
 *      Records have the following fields:
 *
 *      * level - ``Number``  The zoom level.
 *      * scale - ``Number`` The scale denominator.
 *      * resolution - ``Number`` The map units per pixel.
 */
GeoExt.data.ScaleStore = Ext.extend(Ext.data.Store, {

    /** api: config[map]
     *  ``OpenLayers.Map`` or :class:`GeoExt.MapPanel`
     *  Optional map or map panel from which to derive scale values.
     */
    map: null,

    /** private: method[constructor]
     *  Construct a ScaleStore from a configuration.  The ScaleStore accepts
     *  some custom parameters addition to the fields accepted by Ext.Store.
     */
    constructor: function(config) {
        var map = (config.map instanceof GeoExt.MapPanel ? config.map.map : config.map);
        delete config.map;
        config = Ext.applyIf(config, {reader: new Ext.data.JsonReader({}, [
            "level",
            "resolution",
            "scale"
        ])});

        GeoExt.data.ScaleStore.superclass.constructor.call(this, config);

        if (map) {
            this.bind(map);
        }
    },

    /** api: method[bind]
     *  :param map: :class:`GeoExt.MapPanel` or ``OpenLayers.Map`` Panel or map
     *      to which we should bind.
     *  
     *  Bind this store to a map; that is, maintain the zoom list in sync with
     *  the map's current configuration.  If the map does not currently have a
     *  set scale list, then the store will remain empty until the map is
     *  configured with one.
     */
    bind: function(map, options) {
        this.map = (map instanceof GeoExt.MapPanel ? map.map : map);
        this.map.events.register('changebaselayer', this, this.populateFromMap);
        if (this.map.baseLayer) {
            this.populateFromMap();
        } else {
            this.map.events.register('addlayer', this, this.populateOnAdd);
        }
    },

    /** api: method[unbind]
     *  Un-bind this store from the map to which it is currently bound.  The
     *  currently stored zoom levels will remain, but no further changes from
     *  the map will affect it.
     */
    unbind: function() {
        if (this.map) {
            this.map.events.unregister('addlayer', this, this.populateOnAdd);
            this.map.events.unregister('changebaselayer', this, this.populateFromMap);
            delete this.map;
        }
    },

    /** private: method[populateOnAdd]
     *  :param evt: ``Object``
     *  
     *  This method handles the case where we have bind() called on a
     *  not-fully-configured map so that the zoom levels can be detected when a
     *  baselayer is finally added.
     */
    populateOnAdd: function(evt) {
        if (evt.layer.isBaseLayer) {
            this.populateFromMap();
            this.map.events.unregister('addlayer', this, this.populateOnAdd);
        }
    },

    /** private: method[populateFromMap]
     *  This method actually loads the zoom level information from the
     *  OpenLayers.Map and converts it to Ext Records.
     */
    populateFromMap: function() {
        var zooms = [];
        var resolutions = this.map.baseLayer.resolutions;
        var units = this.map.baseLayer.units;

        for (var i=resolutions.length-1; i >= 0; i--) {
            var res = resolutions[i];
            zooms.push({
                level: i,
                resolution: res,
                scale: OpenLayers.Util.getScaleFromResolution(res, units)
            });
        }

        this.loadData(zooms);
    },

    /** private: method[destroy]
     */
    destroy: function() {
        this.unbind();
        GeoExt.data.ScaleStore.superclass.destroy.apply(this, arguments);
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = StyleReader
 *  base_link = `Ext.data.JsonReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.JsonReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: StyleReader
 *
 *  A smart reader that creates records for client-side rendered legends. If
 *  its store is configured with an ``OpenLayers.Style2`` instance as ``data``,
 *  each record will represent a Rule of the Style, and the store will be
 *  configured with ``symbolizers`` (Array of ``OpenLayers.Symbolizer``),
 *  ``filter`` (``OpenLayers.Filter``), ``label`` (String, the rule's title),
 *  ``name`` (String), ``description`` (String), ``elseFilter`` (Boolean),
 *  ``minScaleDenominator`` (Number) and ``maxScaleDenominator`` (Number)
 *  fields. If the store's ``data`` is an ``OpenLayers.Symbolizer.Raster``
 *  instance, records will represent its ColorMap entries, and the available
 *  fields will only be ``symbolizers`` (object literal with ``color`` and
 *  ``opacity`` properties from the ColorMapEntry, and stroke set to false),
 *  ``filter`` (String, the ColorMapEntry's quantity) and ``label`` (String).
 *
 *  The store populated by this reader is synchronized with the underlying data
 *  object. To write back changes to the Style or Symbolizer object, call
 *  ``commitChanges`` on the store.
 *
 *  .. note::
 *
 *      Calling ``commitChanges`` on the store that is populated with
 *      this reader will fail with OpenLayers 2.11 - it requires at least
 *      revision
 *      https://github.com/openlayers/openlayers/commit/1db5ac3cbe874317968f78832901d6ef887ecca6
 *      from 2011-11-28 of OpenLayers.
 */

/** api: example
 *  Sample code to create a store that reads from an ``OpenLayers.Style2``
 *  object:
 *  
 *  .. code-block:: javascript
 *
 *      var store = new Ext.data.Store({
 *          reader: new GeoExt.data.StyleReader(),
 *          data: myStyle // OpenLayers.Style2 or OpenLayers.Symbolizer.Raster
 *      });
 */
GeoExt.data.StyleReader = Ext.extend(Ext.data.JsonReader, {
    
    /** private: property[raw]
     *  ``Object`` The ``data`` object that the store was configured with. Will
     *  be updated with changes when ``commitChanges`` is called on the store.
     */
    
    /** private: method[onMetaChange]
     *  Override to intercept the commit method of the record prototype used
     *  by the reader, so it triggers the ``storeToData`` method that writes
     *  changes back to the underlying raw data.
     */
    onMetaChange: function() {
        GeoExt.data.StyleReader.superclass.onMetaChange.apply(this, arguments);
        this.recordType.prototype.commit = Ext.createInterceptor(this.recordType.prototype.commit, function() {
            var reader = this.store.reader;
            reader.raw[reader.meta.root] = reader.meta.storeToData(this.store);
        });
    },
    
    /** private: method[readRecords]
     */
    readRecords: function(o) {
        var type, rows;
        if (o instanceof OpenLayers.Symbolizer.Raster) {
            type = "colorMap";
        } else {
            type = "rules";
        }
        this.raw = o;
        Ext.applyIf(this.meta, GeoExt.data.StyleReader.metaData[type]);
        var data = {metaData: this.meta};
        data[type] = o[type];
        return GeoExt.data.StyleReader.superclass.readRecords.call(this, data);
    }
});

/** private: constant[metaData]
 *  ``Object`` MetaData configurations for raster and vector styles.
 */
GeoExt.data.StyleReader.metaData = {
    colorMap: {
        root: "colorMap",
        idProperty: "filter",
        fields: [
            {name: "symbolizers", mapping: function(v) {
                return {
                    fillColor: v.color,
                    fillOpacity: v.opacity,
                    stroke: false
                };
            }},
            {name: "filter", mapping: "quantity", type: "float"},
            {name: "label", mapping: function(v) {
                // fill label with quantity if empty
                return v.label || v.quantity;
            }}
        ],
        storeToData: function(store) {
            // ColorMap entries always need to be sorted in ascending order
            store.sort("filter", "ASC");
            var colorMap = [];
            store.each(function(rec) {
                var symbolizer = rec.get("symbolizers"),
                    label = rec.get("label"),
                    labelModified = rec.isModified("label");

                // make sure we convert to number, so users can have a grid
                // with a textfield editor instead of a numberfield. This adds
                // convenience because a column definition with a textfield
                // editor can also be used for editing a rule filter (CQL).
                var quantity = Number(rec.get("filter"));
                rec.data.filter = quantity;

                if ((!rec.json.label && !labelModified && rec.isModified("filter")) || (labelModified && !label)) {
                    // fill label with quantity if empty
                    rec.data.label = quantity;
                }
                colorMap.push(Ext.apply(rec.json, {
                    color: symbolizer.fillColor,
                    label: typeof label == "string" ? label : undefined,
                    opacity: symbolizer.opacity,
                    quantity: quantity
                }));
            });
            return colorMap;
        }
    },
    rules: {
        root: "rules",
        fields: [
            "symbolizers",
            "filter",
            {name: "label", mapping: "title"},
            "name", "description", "elseFilter",
            "minScaleDenominator", "maxScaleDenominator"
        ],
        storeToData: function(store) {
            var rules = [];
            store.each(function(rec) {
                var filter = rec.get("filter");
                if (typeof filter === "string") {
                    filter = filter ? OpenLayers.Format.CQL.prototype.read(filter) : null;
                }
                rules.push(Ext.apply(rec.json, {
                    symbolizers: rec.get("symbolizers"),
                    filter: filter,
                    title: rec.get("label"),
                    name: rec.get("name"),
                    description: rec.get("description"),
                    elseFilter: rec.get("elseFilter"),
                    minScaleDenominator: rec.get("minScaleDenominator"),
                    maxScaleDenominator: rec.get("maxScaleDenominator")
                }));
            });
            return rules;
        }
    }
};
/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerRecord.js
 * @require OpenLayers/Format/WMSCapabilities.js
 * @require OpenLayers/Format/WMSCapabilities/v1_1_1.js
 * @require OpenLayers/Util.js
 * @require OpenLayers/Layer/WMS.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMSCapabilitiesReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMSCapabilitiesReader(meta, recordType)
 *  
 *      :param meta: ``Object`` Reader configuration from which:
 *          ``layerOptions`` is an optional object passed as default options
 *          to the ``OpenLayers.Layer.WMS`` constructor.
 *          ``layerParams`` is an optional set of parameters to pass into the
 *          ``OpenLayers.Layer.WMS`` constructor.
 *      :param recordType: ``Array | Ext.data.Record`` An array of field
 *          configuration objects or a record object.  Default is
 *          :class:`GeoExt.data.LayerRecord` with the following fields:
 *          name, title, abstract, queryable, opaque, noSubsets, cascaded,
 *          fixedWidth, fixedHeight, minScale, maxScale, prefix, formats,
 *          styles, srs, dimensions, bbox, llbbox, attribution, keywords,
 *          identifiers, authorityURLs, metadataURLs, infoFormats.
 *          The type of these fields is the same as for the matching fields in
 *          the object returned from
 *          ``OpenLayers.Format.WMSCapabilities::read()``.
 *   
 *      Data reader class to create an array of
 *      :class:`GeoExt.data.LayerRecord` objects from a WMS GetCapabilities
 *      response.
 */
GeoExt.data.WMSCapabilitiesReader = function(meta, recordType) {
    meta = meta || {};
    if(!meta.format) {
        meta.format = new OpenLayers.Format.WMSCapabilities();
    }
    if(typeof recordType !== "function") {
        recordType = GeoExt.data.LayerRecord.create(
            recordType || meta.fields || [
                {name: "name", type: "string"},
                {name: "title", type: "string"},
                {name: "abstract", type: "string"},
                {name: "queryable", type: "boolean"},
                {name: "opaque", type: "boolean"},
                {name: "noSubsets", type: "boolean"},
                {name: "cascaded", type: "int"},
                {name: "fixedWidth", type: "int"},
                {name: "fixedHeight", type: "int"},
                {name: "minScale", type: "float"},
                {name: "maxScale", type: "float"},
                {name: "prefix", type: "string"},
                {name: "formats"}, // array
                {name: "styles"}, // array
                {name: "srs"}, // object
                {name: "dimensions"}, // object
                {name: "bbox"}, // object
                {name: "llbbox"}, // array
                {name: "attribution"}, // object
                {name: "keywords"}, // array
                {name: "identifiers"}, // object
                {name: "authorityURLs"}, // object
                {name: "metadataURLs"}, // array
                {name: "infoFormats"} // array
            ]
        );
    }
    GeoExt.data.WMSCapabilitiesReader.superclass.constructor.call(
        this, meta, recordType
    );
};

Ext.extend(GeoExt.data.WMSCapabilitiesReader, Ext.data.DataReader, {


    /** api: config[attributionCls]
     *  ``String`` CSS class name for the attribution DOM elements.
     *  Element class names append "-link", "-image", and "-title" as
     *  appropriate.  Default is "gx-attribution".
     */
    attributionCls: "gx-attribution",

    /** private: method[read]
     *  :param request: ``Object`` The XHR object which contains the parsed XML
     *      document.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     */
    read: function(request) {
        var data = request.responseXML;
        if(!data || !data.documentElement) {
            data = request.responseText;
        }
        return this.readRecords(data);
    },
    
    /** private: method[serviceExceptionFormat]
     *  :param formats: ``Array`` An array of service exception format strings.
     *  :return: ``String`` The (supposedly) best service exception format.
     */
    serviceExceptionFormat: function(formats) {
        if (OpenLayers.Util.indexOf(formats, 
            "application/vnd.ogc.se_inimage")>-1) {
            return "application/vnd.ogc.se_inimage";
        }
        if (OpenLayers.Util.indexOf(formats, 
            "application/vnd.ogc.se_xml")>-1) {
            return "application/vnd.ogc.se_xml";
        }
        return formats[0];
    },
    
    /** private: method[imageFormat]
     *  :param layer: ``Object`` The layer's capabilities object.
     *  :return: ``String`` The (supposedly) best mime type for requesting 
     *      tiles.
     */
    imageFormat: function(layer) {
        var formats = layer.formats;
        if (layer.opaque && 
            OpenLayers.Util.indexOf(formats, "image/jpeg")>-1) {
            return "image/jpeg";
        }
        if (OpenLayers.Util.indexOf(formats, "image/png")>-1) {
            return "image/png";
        }
        if (OpenLayers.Util.indexOf(formats, "image/png; mode=24bit")>-1) {
            return "image/png; mode=24bit";
        }
        if (OpenLayers.Util.indexOf(formats, "image/gif")>-1) {
            return "image/gif";
        }
        return formats[0];
    },

    /** private: method[imageTransparent]
     *  :param layer: ``Object`` The layer's capabilities object.
     *  :return: ``Boolean`` The TRANSPARENT param.
     */
    imageTransparent: function(layer) {
        return layer.opaque == undefined || !layer.opaque;
    },

    /** private: method[readRecords]
     *  :param data: ``DOMElement | String | Object`` A document element or XHR
     *      response string.  As an alternative to fetching capabilities data
     *      from a remote source, an object representing the capabilities can
     *      be provided given that the structure mirrors that returned from the
     *      capabilities parser.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     *  
     *  Create a data block containing Ext.data.Records from an XML document.
     */
    readRecords: function(data) {
        if(typeof data === "string" || data.nodeType) {
            data = this.meta.format.read(data);
        }
        if (!!data.error) {
            throw new Ext.data.DataReader.Error("invalid-response", data.error);
        }
        var version = data.version;
        var capability = data.capability || {};
        var url = capability.request && capability.request.getmap &&
            capability.request.getmap.href; 
        var layers = capability.layers; 
        var formats = capability.exception ? capability.exception.formats : [];
        var exceptions = this.serviceExceptionFormat(formats);
        var records = [];
        
        if(url && layers) {
            var fields = this.recordType.prototype.fields; 
            var layer, values, options, params, field, v;

            for(var i=0, lenI=layers.length; i<lenI; i++){
                layer = layers[i];
                if(layer.name) {
                    values = {};
                    for(var j=0, lenJ=fields.length; j<lenJ; j++) {
                        field = fields.items[j];
                        v = layer[field.mapping || field.name] ||
                        field.defaultValue;
                        v = field.convert(v);
                        values[field.name] = v;
                    }
                    options = {
                        attribution: layer.attribution ?
                            this.attributionMarkup(layer.attribution) :
                            undefined,
                        minScale: layer.minScale,
                        maxScale: layer.maxScale
                    };
                    if(this.meta.layerOptions) {
                        Ext.apply(options, this.meta.layerOptions);
                    }
                    params = {
                            layers: layer.name,
                            exceptions: exceptions,
                            format: this.imageFormat(layer),
                            transparent: this.imageTransparent(layer),
                            version: version
                    };
                    if (this.meta.layerParams) {
                        Ext.apply(params, this.meta.layerParams);
                    }
                    values.layer = new OpenLayers.Layer.WMS(
                        layer.title || layer.name, url, params, options
                    );
                    records.push(new this.recordType(values, values.layer.id));
                }
            }
        }
        
        return {
            totalRecords: records.length,
            success: true,
            records: records
        };

    },

    /** private: method[attributionMarkup]
     *  :param attribution: ``Object`` The attribution property of the layer
     *      object as parsed from a WMS Capabilities document
     *  :return: ``String`` HTML markup to display attribution
     *      information.
     *  
     *  Generates attribution markup using the Attribution metadata
     *      from WMS Capabilities
     */
    attributionMarkup : function(attribution){
        var markup = [];
        
        if (attribution.logo){
            markup.push("<img class='"+this.attributionCls+"-image' "
                        + "src='" + attribution.logo.href + "' />");
        }
        
        if (attribution.title) {
            markup.push("<span class='"+ this.attributionCls + "-title'>"
                        + attribution.title
                        + "</span>");
        }
        
        if(attribution.href){
            for(var i = 0; i < markup.length; i++){
                markup[i] = "<a class='"
              + this.attributionCls + "-link' "
                    + "href="
                    + attribution.href
                    + ">"
                    + markup[i]
                    + "</a>";
            }
        }

        return markup.join(" ");
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/WMSCapabilitiesReader.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMSCapabilitiesStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMSCapabilitiesStore
 *  
 *      Small helper class to make creating stores for remote WMS layer data
 *      easier.  The store is pre-configured with a built-in
 *      ``Ext.data.HttpProxy`` and :class:`GeoExt.data.WMSCapabilitiesReader`.
 *      The proxy is configured to allow caching and issues requests via GET.
 *      If you require some other proxy/reader combination then you'll have to
 *      configure this with your own proxy or create a basic
 *      :class:`GeoExt.data.LayerStore` and configure as needed.
 */

/** api: config[format]
 *  ``OpenLayers.Format``
 *  A parser for transforming the XHR response into an array of objects
 *  representing attributes.  Defaults to an ``OpenLayers.Format.WMSCapabilities``
 *  parser.
 */

/** api: config[fields]
 *  ``Array | Function``
 *  Either an Array of field definition objects as passed to
 *  ``Ext.data.Record.create``, or a record constructor created using
 *  ``Ext.data.Record.create``.  Defaults to ``["name", "type"]``. 
 */

GeoExt.data.WMSCapabilitiesStore = function(c) {
    c = c || {};
    GeoExt.data.WMSCapabilitiesStore.superclass.constructor.call(
        this,
        Ext.apply(c, {
            proxy: c.proxy || (!c.data ?
                new Ext.data.HttpProxy({url: c.url, disableCaching: false, method: "GET"}) :
                undefined
            ),
            reader: new GeoExt.data.WMSCapabilitiesReader(
                c, c.fields
            )
        })
    );
};
Ext.extend(GeoExt.data.WMSCapabilitiesStore, Ext.data.Store);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerRecord.js
 * @require OpenLayers/Format/WMTSCapabilities.js
 * @require OpenLayers/Format/WMTSCapabilities/v1_0_0.js
 * @require OpenLayers/Util.js
 * @require OpenLayers/Layer/WMTS.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMTSCapabilitiesReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMTSCapabilitiesReader(meta, recordType)
 *  
 *      :param meta: ``Object`` Reader configuration from which:
 *          ``layerOptions`` is an optional object passed as default options
 *          to the ``OpenLayers.Layer.WMTS`` constructor.
 *          ``layerParams`` is an optional set of parameters to pass into the
 *          ``OpenLayers.Layer.WMTS`` constructor.
 *      :param recordType: ``Array | Ext.data.Record`` An array of field
 *          configuration objects or a record object.  Default is
 *          :class:`GeoExt.data.LayerRecord` with the following fields:
 *          name, title, abstract, queryable, opaque, noSubsets, cascaded,
 *          fixedWidth, fixedHeight, minScale, maxScale, prefix, formats,
 *          styles, srs, dimensions, bbox, llbbox, attribution, keywords,
 *          identifiers, authorityURLs, metadataURLs, infoFormats.
 *          The type of these fields is the same as for the matching fields in
 *          the object returned from
 *          ``OpenLayers.Format.WMTSCapabilities::read()``.
 *   
 *      Data reader class to create an array of
 *      :class:`GeoExt.data.LayerRecord` objects from a WMTS GetCapabilities
 *      response.
 */
GeoExt.data.WMTSCapabilitiesReader = function(meta, recordType) {
    meta = meta || {};
    if (!meta.format) {
        meta.format = new OpenLayers.Format.WMTSCapabilities();
    }
    if (typeof recordType !== "function") {
        recordType = GeoExt.data.LayerRecord.create(
            recordType || meta.fields || [
                {name: "name", type: "string", mapping: "identifier"},
                {name: "title", type: "string"},
                {name: "abstract", type: "string"},
                {name: "queryable", type: "boolean"},
                {name: "llbbox", mapping: "bounds", convert: function(v){
                    return [v.left, v.bottom, v.right, v.top];
                }},
                {name: "formats"}, // array
                {name: "infoFormats"}, // array
                {name: "styles"}, // array
                {name: "keywords"} // object
            ]
        );
    }
    GeoExt.data.WMTSCapabilitiesReader.superclass.constructor.call(
        this, meta, recordType
    );
};

Ext.extend(GeoExt.data.WMTSCapabilitiesReader, Ext.data.DataReader, {


    /** private: method[read]
     *  :param request: ``Object`` The XHR object which contains the parsed XML
     *      document.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     */
    read: function(request) {
        var data = request.responseXML;
        if (!data || !data.documentElement) {
            data = request.responseText;
        }
        return this.readRecords(data);
    },
    
    /** private: method[imageFormat]
     *  :param layer: ``Object`` The layer's capabilities object.
     *  :return: ``String`` The (supposedly) best mime type for requesting 
     *      tiles.
     */
    imageFormat: function(layer) {
        var formats = layer.formats;
        if (OpenLayers.Util.indexOf(formats, "image/png")>-1) {
            return "image/png";
        }
        if (OpenLayers.Util.indexOf(formats, "image/jpeg")>-1) {
            return "image/jpeg";
        }
        if (OpenLayers.Util.indexOf(formats, "image/png8")>-1) {
            return "image/png8";
        }
        if (OpenLayers.Util.indexOf(formats, "image/gif")>-1) {
            return "image/gif";
        }
        return formats[0];
    },

    /** private: method[readRecords]
     *  :param data: ``DOMElement | String | Object`` A document element or XHR
     *      response string.  As an alternative to fetching capabilities data
     *      from a remote source, an object representing the capabilities can
     *      be provided given that the structure mirrors that returned from the
     *      capabilities parser.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     *  
     *  Create a data block containing Ext.data.Records from an XML document.
     */
    readRecords: function(data) {
        if (typeof data === "string" || data.nodeType) {
            data = this.meta.format.read(data);
        }
        if (!!data.error) {
            throw new Ext.data.DataReader.Error("invalid-response", data.error);
        }
        var operationsMetadata = data.operationsMetadata,
            layers = data.contents && data.contents.layers;
        
        var records = [];

        if (layers) {
            var fields = this.recordType.prototype.fields; 
            var layer, values, options, params, field, v, matrixSet;

            for (var i=0, lenI=layers.length; i<lenI; i++){
                layer = layers[i];
                if (layer.identifier) {
                    values = {};
                    for (var j=0, lenJ=fields.length; j<lenJ; j++) {
                        field = fields.items[j];
                        v = layer[field.mapping || field.name] ||
                        field.defaultValue;
                        v = field.convert(v);
                        values[field.name] = v;
                    }
                    values.queryable = !!layer.infoFormats;

                    try {
                        values.layer = this.meta.format.createLayer(data, Ext.apply({
                            layer: layer.identifier,
                            name: layer.title || layer.identifier,
                            format: this.imageFormat(layer)
                        }, this.meta.layerOptions || {}));

                        records.push(new this.recordType(values, values.layer.id));
                    } catch (e) {
                        // ignore silently (eg: no matching CRS)
                    }
                }
            }
        }

        return {
            totalRecords: records.length,
            success: true,
            records: records
        };

    }
});
/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/WMTSCapabilitiesReader.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMTSCapabilitiesStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMTSCapabilitiesStore
 *  
 *      Small helper class to make creating stores for remote WMTS layer data
 *      easier.  The store is pre-configured with a built-in
 *      ``Ext.data.HttpProxy`` and :class:`GeoExt.data.WMTSCapabilitiesReader`.
 *      The proxy is configured to allow caching and issues requests via GET.
 *      If you require some other proxy/reader combination then you'll have to
 *      configure this with your own proxy or create a basic
 *      :class:`GeoExt.data.LayerStore` and configure as needed.
 */

/** api: config[format]
 *  ``OpenLayers.Format``
 *  A parser for transforming the XHR response into an array of objects
 *  representing attributes.  Defaults to an ``OpenLayers.Format.WMTSCapabilities``
 *  parser.
 */

/** api: config[fields]
 *  ``Array | Function``
 *  Either an Array of field definition objects as passed to
 *  ``Ext.data.Record.create``, or a record constructor created using
 *  ``Ext.data.Record.create``.  Defaults to ``["name", "type"]``. 
 */
GeoExt.data.WMTSCapabilitiesStore = function(c) {
    c = c || {};
    GeoExt.data.WMTSCapabilitiesStore.superclass.constructor.call(
        this,
        Ext.apply(c, {
            proxy: c.proxy || (!c.data ?
                new Ext.data.HttpProxy({url: c.url, disableCaching: false, method: "GET"}) :
                undefined
            ),
            reader: new GeoExt.data.WMTSCapabilitiesReader(
                c, c.fields
            )
        })
    );
};
Ext.extend(GeoExt.data.WMTSCapabilitiesStore, Ext.data.Store);
/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerRecord.js
 * @require OpenLayers/Format/WFSCapabilities.js
 * @require OpenLayers/Format/WFSCapabilities/v1_1_0.js
 * @require OpenLayers/Protocol/WFS.js
 * @require OpenLayers/Protocol/WFS/v1_0_0.js
 * @require OpenLayers/Strategy/Fixed.js
 * @require OpenLayers/Layer/Vector.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WFSCapabilitiesReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WFSCapabilitiesReader(meta, recordType)
 *  
 *      :param meta: ``Object`` Reader configuration from which:
 *          ``layerOptions`` is an optional object (or function that returns
 *          an object) passed as default options to the
 *          ``OpenLayers.Layer.Vector`` constructor.
 *          ``protocolOptions`` is an optional set of parameters to pass to the
 *          ``OpenLayers.Protocol.WFS`` constructor.
 *      :param recordType: ``Array | Ext.data.Record`` An array of field
 *          configuration objects or a record object.  Default is
 *          :class:`GeoExt.data.LayerRecord`.
 *   
 *      Data reader class to create an array of
 *      :class:`GeoExt.data.LayerRecord` objects from a WFS GetCapabilities
 *      response.
 */
GeoExt.data.WFSCapabilitiesReader = function(meta, recordType) {
    meta = meta || {};
    if(!meta.format) {
        meta.format = new OpenLayers.Format.WFSCapabilities();
    }
    if(!(typeof recordType === "function")) {
        recordType = GeoExt.data.LayerRecord.create(
            recordType || meta.fields || [
                {name: "name", type: "string"},
                {name: "title", type: "string"},
                {name: "namespace", type: "string", mapping: "featureNS"},
                {name: "abstract", type: "string"}
            ]
        );
    }
    GeoExt.data.WFSCapabilitiesReader.superclass.constructor.call(
        this, meta, recordType
    );
};

Ext.extend(GeoExt.data.WFSCapabilitiesReader, Ext.data.DataReader, {

    /** private: method[read]
     *  :param request: ``Object`` The XHR object which contains the parsed XML
     *      document.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     */
    read: function(request) {
        var data = request.responseXML;
        if(!data || !data.documentElement) {
            data = request.responseText;
        }
        return this.readRecords(data);
    },

    /** private: method[readRecords]
     *  :param data: ``DOMElement | String | Object`` A document element or XHR
     *      response string.  As an alternative to fetching capabilities data
     *      from a remote source, an object representing the capabilities can
     *      be provided given that the structure mirrors that returned from the
     *      capabilities parser.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     *  
     *  Create a data block containing Ext.data.Records from an XML document.
     */
    readRecords: function(data) {
        if(typeof data === "string" || data.nodeType) {
            data = this.meta.format.read(data);
        }

        var featureTypes = data.featureTypeList.featureTypes;
        var fields = this.recordType.prototype.fields;

        var featureType, values, field, v, parts, layer;
        var layerOptions, protocolOptions;

        var url = (parseFloat(data.version) >= 1.1) ? 
            data.operationsMetadata && data.operationsMetadata["GetFeature"].dcp.http.post[0].url :
                data.capability.request.getfeature.href.post;

        var protocolDefaults = {
            url: url,
            version : data.version
        };

        var records = [];

        for(var i=0, lenI=featureTypes.length; i<lenI; i++) {
            featureType = featureTypes[i];
            if(featureType.name) {
                values = {};

                for(var j=0, lenJ=fields.length; j<lenJ; j++) {
                    field = fields.items[j];
                    v = featureType[field.mapping || field.name] ||
                        field.defaultValue;
                    v = field.convert(v);
                    values[field.name] = v;
                }

                protocolOptions = {
                    featureType: featureType.name,
                    featureNS: featureType.featureNS
                };
                if(this.meta.protocolOptions) {
                    Ext.apply(protocolOptions, this.meta.protocolOptions, 
                        protocolDefaults);
                } else {
                    Ext.apply(protocolOptions, {}, protocolDefaults);
                }

                layerOptions = {
                    protocol: new OpenLayers.Protocol.WFS(protocolOptions),
                    strategies: [new OpenLayers.Strategy.Fixed()]
                };
                var metaLayerOptions = this.meta.layerOptions;
                if (metaLayerOptions) {
                    Ext.apply(layerOptions, Ext.isFunction(metaLayerOptions) ?
                        metaLayerOptions() : metaLayerOptions);
                }

                values.layer = new OpenLayers.Layer.Vector(
                    featureType.title || featureType.name,
                    layerOptions
                );

                records.push(new this.recordType(values, values.layer.id));
            }
        }
        return {
            totalRecords: records.length,
            success: true,
            records: records
        };
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/WFSCapabilitiesReader.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WFSCapabilitiesStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WFSCapabilitiesStore
 *  
 *      Small helper class to make creating stores for remote WFS layer data
 *      easier.  The store is pre-configured with a built-in
 *      ``Ext.data.HttpProxy`` and :class:`GeoExt.data.WFSCapabilitiesReader`.
 *      The proxy is configured to allow caching and issues requests via GET.
 *      If you require some other proxy/reader combination then you'll have to
 *      configure this with your own proxy or create a basic
 *      :class:`GeoExt.data.LayerStore` and configure as needed.
 */

/** api: config[format]
 *  ``OpenLayers.Format``
 *  A parser for transforming the XHR response into an array of objects
 *  representing attributes.  Defaults to an ``OpenLayers.Format.WFSCapabilities``
 *  parser.
 */

/** api: config[fields]
 *  ``Array | Function``
 *  Either an Array of field definition objects as passed to
 *  ``Ext.data.Record.create``, or a record constructor created using
 *  ``Ext.data.Record.create``.  Defaults to ``["name", "type"]``. 
 */

GeoExt.data.WFSCapabilitiesStore = function(c) {
    c = c || {};
    GeoExt.data.WFSCapabilitiesStore.superclass.constructor.call(
        this,
        Ext.apply(c, {
            proxy: c.proxy || (!c.data ?
                new Ext.data.HttpProxy({url: c.url, disableCaching: false, method: "GET"}) :
                undefined
            ),
            reader: new GeoExt.data.WFSCapabilitiesReader(
                c, c.fields
            )
        })
    );
};
Ext.extend(GeoExt.data.WFSCapabilitiesStore, Ext.data.Store);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Format/WMSDescribeLayer.js
 * @require OpenLayers/Format/WMSDescribeLayer/v1_1.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMSDescribeLayerReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMSDescribeLayerReader(meta, recordType)
 *  
 *      :param meta: ``Object`` Reader configuration.
 *      :param recordType: ``Array | Ext.data.Record`` An array of field
 *          configuration objects or a record object.  Default has
 *          fields for owsType, owsURL, and typeName.
 *   
 *      Data reader class to create an array of
 *      layer description objects from a WMS DescribeLayer
 *      response.
 */
GeoExt.data.WMSDescribeLayerReader = function(meta, recordType) {
    meta = meta || {};
    if(!meta.format) {
        meta.format = new OpenLayers.Format.WMSDescribeLayer();
    }
    if(!(typeof recordType === "function")) {
        recordType = Ext.data.Record.create(
            recordType || meta.fields || [
                {name: "owsType", type: "string"},
                {name: "owsURL", type: "string"},
                {name: "typeName", type: "string"}
            ]
        );
    }
    GeoExt.data.WMSDescribeLayerReader.superclass.constructor.call(
        this, meta, recordType
    );
};

Ext.extend(GeoExt.data.WMSDescribeLayerReader, Ext.data.DataReader, {

    /** private: method[read]
     *  :param request: ``Object`` The XHR object which contains the parsed XML
     *      document.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     */
    read: function(request) {
        var data = request.responseXML;
        if(!data || !data.documentElement) {
            data = request.responseText;
        }
        return this.readRecords(data);
    },

    /** private: method[readRecords]
     *  :param data: ``DOMElement | Strint | Object`` A document element or XHR
     *      response string.  As an alternative to fetching layer description data
     *      from a remote source, an object representing the layer descriptions can
     *      be provided given that the structure mirrors that returned from the
     *      layer description parser.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     *  
     *  Create a data block containing Ext.data.Records from an XML document.
     */
    readRecords: function(data) {
        
        if(typeof data === "string" || data.nodeType) {
            data = this.meta.format.read(data);
        }
        if (!!data.error) {
            throw new Ext.data.DataReader.Error("invalid-response", data.error);
        }
        var records = [], description;        
        for(var i=0, len=data.length; i<len; i++){
            description = data[i];
            if(description) {
                records.push(new this.recordType(description));
            }
        }

        return {
            totalRecords: records.length,
            success: true,
            records: records
        };

    }
});

/**x
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/WMSDescribeLayerReader.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMSDescribeLayerStore
 *  base_link = `Ext.data.Store <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.Store>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMSDescribeLayerStore
 *  
 *      Small helper class to make creating stores for remote WMS layer description
 *      easier.  The store is pre-configured with a built-in
 *      ``Ext.data.HttpProxy`` and :class:`GeoExt.data.WMSDescribeLayerReader`.
 *      The proxy is configured to allow caching and issues requests via GET.
 *      If you require some other proxy/reader combination then you'll have to
 *      configure this with your own proxy or create a basic
 *      store and configure as needed.
 */

/** api: config[format]
 *  ``OpenLayers.Format``
 *  A parser for transforming the XHR response into an array of objects
 *  representing attributes.  Defaults to an ``OpenLayers.Format.WMSDescribeLayer``
 *  parser.
 */

/** api: config[fields]
 *  ``Array | Function``
 *  Either an Array of field definition objects as passed to
 *  ``Ext.data.Record.create``, or a record constructor created using
 *  ``Ext.data.Record.create``.  Defaults to ``["name", "type"]``. 
 */

GeoExt.data.WMSDescribeLayerStore = function(c) {
    c = c || {};
    GeoExt.data.WMSDescribeLayerStore.superclass.constructor.call(
        this,
        Ext.apply(c, {
            proxy: c.proxy || (!c.data ?
                new Ext.data.HttpProxy({url: c.url, disableCaching: false, method: "GET"}) :
                undefined
            ),
            reader: new GeoExt.data.WMSDescribeLayerReader(
                c, c.fields
            )
        })
    );
};
Ext.extend(GeoExt.data.WMSDescribeLayerStore, Ext.data.Store);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerRecord.js
 * @require OpenLayers/Format/WMC.js
 * @require OpenLayers/Format/WMC/v1_1_0.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = WMCReader
 *  base_link = `Ext.data.DataReader <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataReader>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: WMCReader(meta, recordType)
 *  
 *      :param meta: ``Object`` Reader configuration.
 *      :param recordType: ``Array | Ext.data.Record`` An array of field
 *          configuration objects or a record object.  Default is
 *          :class:`GeoExt.data.LayerRecord`.
 *   
 *      Data reader class to create an array of
 *      :class:`GeoExt.data.LayerRecord` objects from a WMS GetCapabilities
 *      response.
 */
GeoExt.data.WMCReader = function(meta, recordType) {
    meta = meta || {};
    if(!meta.format) {
        meta.format = new OpenLayers.Format.WMC();
    }
    if(!(typeof recordType === "function")) {
        recordType = GeoExt.data.LayerRecord.create(
            recordType || meta.fields || [
                // give only non-OpenLayers fields as default recordType
                {name: "abstract", type: "string"},
                {name: "metadataURL", type: "string"},
                {name: "queryable", type: "boolean"},
                {name: "formats"}, // array
                {name: "styles"} // array
            ]
        );
    }
    GeoExt.data.WMCReader.superclass.constructor.call(
        this, meta, recordType
    );
};

Ext.extend(GeoExt.data.WMCReader, Ext.data.DataReader, {

    /** private: method[read]
     *  :param request: ``Object`` The XHR object which contains the parsed XML
     *      document.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     */
    read: function(request) {
        var data = request.responseXML;
        if(!data || !data.documentElement) {
            data = request.responseText;
        }
        return this.readRecords(data);
    },

    /** private: method[readRecords]
     *  :param data: ``DOMElement | String | Object`` A document element or XHR
     *      response string.  As an alternative to fetching capabilities data
     *      from a remote source, an object representing the capabilities can
     *      be provided given that the structure mirrors that returned from the
     *      capabilities parser.
     *  :return: ``Object`` A data block which is used by an ``Ext.data.Store``
     *      as a cache of ``Ext.data.Record`` objects.
     *  
     *  Create a data block containing Ext.data.Records from an XML document.
     */
    readRecords: function(data) {
        var format = this.meta.format;
        if(typeof data === "string" || data.nodeType) {
            data = format.read(data);
        }
        var layersContext = data ? data.layersContext : undefined;
        var records = [];        

        if(layersContext) {
            var recordType = this.recordType, fields = recordType.prototype.fields;
            var i, lenI, j, lenJ, layerContext, values, field, v;
            for (i = 0, lenI = layersContext.length; i < lenI; i++) {
                layerContext = layersContext[i];
                values = {};
                for(j = 0, lenJ = fields.length; j < lenJ; j++){
                    field = fields.items[j];
                    v = layerContext[field.mapping || field.name] ||
                        field.defaultValue;
                    v = field.convert(v);
                    values[field.name] = v;
                }
                values.layer = format.getLayerFromContext(layerContext);
                records.push(new this.recordType(values, values.layer.id));
            }
        }
        
        return {
            totalRecords: records.length,
            success: true,
            records: records
        };

    }

});


/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Format/CSWGetRecords/v2_0_2.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = CSWRecordsReader
 *  base_link = `Ext.data.JsonReader <http://extjs.com/deploy/dev/docs/?class=Ext.data.JsonReader>`_
 */

Ext.namespace("GeoExt.data");

/** api: example
 *  Typical usage in a store:
 * 
 *  .. code-block:: javascript
 *     
 *      var store = new Ext.data.Store({
 *          proxy: new GeoExt.data.ProtocolProxy({
 *              protocol: new OpenLayers.Protocol.CSW({
 *                  url: "http://demo.geonode.org/geonetwork/srv/en/csw"
 *              })
 *          }),
 *          reader: new GeoExt.data.CSWRecordsReader({
 *             fields: ['title', 'subject', 'URI', 'bounds', 'projection']
 *          })
 *      });
 *      
 */

/** api: constructor
 *  .. class:: CSWRecordsReader(meta, recordType)
 *  
 *      :param meta: ``Object`` Reader configuration.
 *      :param recordType: ``Array | Ext.data.Record`` An array of field
 *          configuration objects or a record object.  Default is
 *          :class:`Ext.data.Record`.
 *   
 *      Data reader class to create an array of records from a CSW
 *      GetRecords response. The raw response from the OpenLayers parser
 *      is available through the jsonData property.
 */
GeoExt.data.CSWRecordsReader = function(meta, recordType) {
    meta = meta || {};
    if(!meta.format) {
        meta.format = new OpenLayers.Format.CSWGetRecords();
    }
    if(!meta.root) {
        meta.root = 'records';
    }
    GeoExt.data.CSWRecordsReader.superclass.constructor.call(
        this, meta, recordType
    );
};

Ext.extend(GeoExt.data.CSWRecordsReader, Ext.data.JsonReader, {

    /** private: method[read]
     *  :param data: ``XMLHttpRequest | OpenLayers.Protocol.Response`` If a
     *  ProtocolProxy is configured with OpenLayers.Protocol.CSW data will be
     *  ``OpenLayers.Protocol.Response``. Otherwise data will be the 
     * ``XMLHttpRequest`` object.
     *  :return: ``Object`` A data block which is used by an
     *      ``Ext.data.Store`` as a cache of ``Ext.data.Record``
     *      objects.
     */
    read: function(data) {
        var o = data.data;
        if (!o) {
            o = data.responseXML;
            if(!o || !o.documentElement) {
                o = data.responseText;
            }
        }
        return this.readRecords(o);
    },

    /** private: method[readRecords]
     *  :param data: ``DOMElement | String | Object`` A document
     *      element or XHR response string.
     *  :return: ``Object`` A data block which is used by an
     *      ``Ext.data.Store`` as a cache of ``Ext.data.Record``
     *      objects.
     */
    readRecords: function(data) {
        if(typeof data === "string" || data.nodeType) {
            data = this.meta.format.read(data);
        }
        if (data.success === false) {
            throw new Ext.data.DataReader.Error("invalid-response", data);
        }
        var result = GeoExt.data.CSWRecordsReader.superclass.readRecords.call(
            this, data
        );
        // post-process so we flatten simple objects with a value property
        Ext.each(result.records, function(record) {
            for (var key in record.data) {
                var value = record.data[key];
                if (value instanceof Array) {
                    for (var i=0, ii=value.length; i<ii; ++i) {
                        if (value[i] instanceof Object) {
                            var size = 0;
                            for (var property in value[i]) {
                                if (value[i].hasOwnProperty(property)) {
                                    size++;
                                }
                            }
                            if (size === 1 && value[i].value) {
                                value[i] = value[i].value;
                            }
                        }
                    }
                }
            }
        });
        if (data.SearchResults) {
            result.totalRecords = data.SearchResults.numberOfRecordsMatched;
        }
        return result;
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Control.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = Action
 *  base_link = `Ext.Action <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Action>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to create a toolbar with an OpenLayers control into it.
 * 
 *  .. code-block:: javascript
 *  
 *      var action = new GeoExt.Action({
 *          text: "max extent",
 *          control: new OpenLayers.Control.ZoomToMaxExtent(),
 *          map: map
 *      });
 *      var toolbar = new Ext.Toolbar([action]);
 */

/** api: constructor
 *  .. class:: Action(config)
 *  
 *      Create a GeoExt.Action instance. A GeoExt.Action is created
 *      to insert an OpenLayers control in a toolbar as a button or
 *      in a menu as a menu item. A GeoExt.Action instance can be
 *      used like a regular Ext.Action, look at the Ext.Action API
 *      doc for more detail.
 */
GeoExt.Action = Ext.extend(Ext.Action, {

    /** api: config[control]
     *  ``OpenLayers.Control`` The OpenLayers control wrapped in this action.
     */
    control: null,

    /** api: config[activateOnEnable]
     *  ``Boolean`` Activate the action's control when the action is enabled.
     *  Default is ``false``.
     */

    /** api: property[activateOnEnable]
     *  ``Boolean`` Activate the action's control when the action is enabled.
     */
    activateOnEnable: false,

    /** api: config[deactivateOnDisable]
     *  ``Boolean`` Deactivate the action's control when the action is disabled.
     *  Default is ``false``.
     */

    /** api: property[deactivateOnDisable]
     *  ``Boolean`` Deactivate the action's control when the action is disabled.
     */
    deactivateOnDisable: false,

    /** api: config[map]
     *  ``OpenLayers.Map`` The OpenLayers map that the control should be added
     *  to.  For controls that don't need to be added to a map or have already
     *  been added to one, this config property may be omitted.
     */
    map: null,

    /** private: property[uScope]
     *  ``Object`` The user-provided scope, used when calling uHandler,
     *  uToggleHandler, and uCheckHandler.
     */
    uScope: null,

    /** private: property[uHandler]
     *  ``Function`` References the function the user passes through
     *  the "handler" property.
     */
    uHandler: null,

    /** private: property[uToggleHandler]
     *  ``Function`` References the function the user passes through
     *  the "toggleHandler" property.
     */
    uToggleHandler: null,

    /** private: property[uCheckHandler]
     *  ``Function`` References the function the user passes through
     *  the "checkHandler" property.
     */
    uCheckHandler: null,

    /** private */
    constructor: function(config) {
        
        // store the user scope and handlers
        this.uScope = config.scope;
        this.uHandler = config.handler;
        this.uToggleHandler = config.toggleHandler;
        this.uCheckHandler = config.checkHandler;

        config.scope = this;
        config.handler = this.pHandler;
        config.toggleHandler = this.pToggleHandler;
        config.checkHandler = this.pCheckHandler;

        // set control in the instance, the Ext.Action
        // constructor won't do it for us
        var ctrl = this.control = config.control;
        delete config.control;
        
        this.activateOnEnable = !!config.activateOnEnable;
        delete config.activateOnEnable;
        this.deactivateOnDisable = !!config.deactivateOnDisable;
        delete config.deactivateOnDisable;

        // register "activate" and "deactivate" listeners
        // on the control
        if(ctrl) {
            // If map is provided in config, add control to map.
            if(config.map) {
                config.map.addControl(ctrl);
                delete config.map;
            }
            if((config.pressed || config.checked) && ctrl.map) {
                ctrl.activate();
            }
            if (ctrl.active) {
                config.pressed = true;
                config.checked = true;
            }
            ctrl.events.on({
                activate: this.onCtrlActivate,
                deactivate: this.onCtrlDeactivate,
                scope: this
            });
        }

        arguments.callee.superclass.constructor.call(this, config);
    },

    /** private: method[pHandler]
     *  :param cmp: ``Ext.Component`` The component that triggers the handler.
     *
     *  The private handler.
     */
    pHandler: function(cmp) {
        var ctrl = this.control;
        if(ctrl &&
           ctrl.type == OpenLayers.Control.TYPE_BUTTON) {
            ctrl.trigger();
        }
        if(this.uHandler) {
            this.uHandler.apply(this.uScope, arguments);
        }
    },

    /** private: method[pTogleHandler]
     *  :param cmp: ``Ext.Component`` The component that triggers the toggle handler.
     *  :param state: ``Boolean`` The state of the toggle.
     *
     *  The private toggle handler.
     */
    pToggleHandler: function(cmp, state) {
        this.changeControlState(state);
        if(this.uToggleHandler) {
            this.uToggleHandler.apply(this.uScope, arguments);
        }
    },

    /** private: method[pCheckHandler]
     *  :param cmp: ``Ext.Component`` The component that triggers the check handler.
     *  :param state: ``Boolean`` The state of the toggle.
     *
     *  The private check handler.
     */
    pCheckHandler: function(cmp, state) {
        this.changeControlState(state);
        if(this.uCheckHandler) {
            this.uCheckHandler.apply(this.uScope, arguments);
        }
    },

    /** private: method[changeControlState]
     *  :param state: ``Boolean`` The state of the toggle.
     *
     *  Change the control state depending on the state boolean.
     */
    changeControlState: function(state) {
        if(state) {
            if(!this._activating) {
                this._activating = true;
                this.control.activate();
                // update initialConfig for next component created from this action
                this.initialConfig.pressed = true;
                this.initialConfig.checked = true;
                this._activating = false;
            }
        } else {
            if(!this._deactivating) {
                this._deactivating = true;
                this.control.deactivate();
                // update initialConfig for next component created from this action
                this.initialConfig.pressed = false;
                this.initialConfig.checked = false;
                this._deactivating = false;
            }
        }
    },

    /** private: method[onCtrlActivate]
     *  
     *  Called when this action's control is activated.
     */
    onCtrlActivate: function() {
        var ctrl = this.control;
        if(ctrl.type == OpenLayers.Control.TYPE_BUTTON) {
            this.enable();
        } else {
            // deal with buttons
            this.safeCallEach("toggle", [true]);
            // deal with check items
            this.safeCallEach("setChecked", [true]);
        }
    },

    /** private: method[onCtrlDeactivate]
     *  
     *  Called when this action's control is deactivated.
     */
    onCtrlDeactivate: function() {
        var ctrl = this.control;
        if(ctrl.type == OpenLayers.Control.TYPE_BUTTON) {
            this.disable();
        } else {
            // deal with buttons
            this.safeCallEach("toggle", [false]);
            // deal with check items
            this.safeCallEach("setChecked", [false]);
        }
    },

    /** private: method[safeCallEach]
     *
     */
    safeCallEach: function(fnName, args) {
        var cs = this.items;
        for(var i = 0, len = cs.length; i < len; i++){
            if(cs[i][fnName]) {
                cs[i].rendered ?
                    cs[i][fnName].apply(cs[i], args) :
                    cs[i].on({
                        "render": cs[i][fnName].createDelegate(cs[i], args),
                        single: true
                    });
            }
        }
    },
    
    /** private: method[setDisabled]
     *  :param v: ``Boolean`` Disable the action's components.
     *
     *  Override method on super to optionally deactivate controls on disable.
     */
    setDisabled : function(v) {
        if (!v && this.activateOnEnable && this.control && !this.control.active) {
            this.control.activate();
        }
        if (v && this.deactivateOnDisable && this.control && this.control.active) {
            this.control.deactivate();
        }
        return GeoExt.Action.superclass.setDisabled.apply(this, arguments);
    }

});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/BaseTypes.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = ProtocolProxy
 *  base_link = `Ext.data.DataProxy <http://dev.sencha.com/deploy/dev/docs/?class=Ext.data.DataProxy>`_
 */
Ext.namespace('GeoExt', 'GeoExt.data');

GeoExt.data.ProtocolProxy = function(config) {
    Ext.apply(this, config);
    GeoExt.data.ProtocolProxy.superclass.constructor.apply(this, arguments);
};

/** api: constructor
 *  .. class:: ProtocolProxy
 *   
 *      A data proxy for use with ``OpenLayers.Protocol`` objects.
 */
Ext.extend(GeoExt.data.ProtocolProxy, Ext.data.DataProxy, {

    /** api: config[protocol]
     *  ``OpenLayers.Protocol``
     *  The protocol used to fetch features.
     */
    protocol: null,

    /** api: config[abortPrevious]
     *  ``Boolean``
     *  Abort any previous request before issuing another.  Default is ``true``.
     */
    abortPrevious: true,

    /** api: config[setParamsAsOptions]
     *  ``Boolean``
     *  Should options.params be set directly on options before passing it into
     *  the protocol's read method? Default is ``false``.
     */
    setParamsAsOptions: false,

    /** private: property[response]
     *  ``OpenLayers.Protocol.Response``
     *  The response returned by the read call on the protocol.
     */
    response: null,

    /** private: method[load]
     *  :param params: ``Object`` An object containing properties which are to
     *      be used as HTTP parameters for the request to the remote server.
     *  :param reader: ``Ext.data.DataReader`` The Reader object which converts
     *      the data object into a block of ``Ext.data.Records``.
     *  :param callback: ``Function`` The function into which to pass the block
     *      of ``Ext.data.Records``. The function is passed the Record block
     *      object, the ``args`` argument passed to the load function, and a
     *      boolean success indicator.
     *  :param scope: ``Object`` The scope in which to call the callback.
     *  :param arg: ``Object`` An optional argument which is passed to the
     *      callback as its second parameter.
     *
     *  Calls ``read`` on the protocol.
     */
    load: function(params, reader, callback, scope, arg) {
        if (this.fireEvent("beforeload", this, params) !== false) {
            var o = {
                params: params || {},
                request: {
                    callback: callback,
                    scope: scope,
                    arg: arg
                },
                reader: reader
            };
            var cb = OpenLayers.Function.bind(this.loadResponse, this, o);
            if (this.abortPrevious) {
                this.abortRequest();
            }
            var options = {
                params: params,
                callback: cb,
                scope: this
            };
            Ext.applyIf(options, arg);
            if (this.setParamsAsOptions === true) {
                Ext.applyIf(options, options.params);
                delete options.params;
            }
            this.response = this.protocol.read(options);
        } else {
           callback.call(scope || this, null, arg, false);
        }
    },

    /** private: method[abortRequest]
     *  Called to abort any ongoing request.
     */
    abortRequest: function() {
        if (this.response) {
            this.protocol.abort(this.response);
            this.response = null;
        }
    },

    /** private: method[loadResponse]
     *  :param o: ``Object``
     *  :param response: ``OpenLayers.Protocol.Response``
     *  
     *  Handle response from the protocol
     */
    loadResponse: function(o, response) {
        if (response.success()) {
            var result;
            try {
                result = o.reader.read(response);
            } catch(e) {
                // @deprecated: fire old loadexception for backwards-compat.
                // TODO remove
                this.fireEvent('loadexception', this, o, response, e);
                this.fireEvent('exception', this, 'response', null, o, response, e);
                o.request.callback.call(o.request.scope, null, o.request.arg, false);
                return;
            }
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

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Feature/Vector.js
 * @require OpenLayers/Geometry/Point.js
 * @require OpenLayers/Geometry/LineString.js
 * @require OpenLayers/Geometry/LinearRing.js
 * @require OpenLayers/Geometry/Polygon.js
 * @require OpenLayers/BaseTypes/Bounds.js
 * @require OpenLayers/BaseTypes/Size.js
 * @require OpenLayers/Renderer.js
 * @require OpenLayers/Symbolizer.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = FeatureRenderer
 *  base_link = `Ext.BoxComponent <http://dev.sencha.com/deploy/dev/docs/?class=Ext.BoxComponent>`_
 */
Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: FeatureRenderer(config)
 *   
 *      Create a box component for rendering a vector feature.
 */
GeoExt.FeatureRenderer = Ext.extend(Ext.BoxComponent, {

    /** api: config[feature]
     *  ``OpenLayers.Feature.Vector``
     *  Optional vector to be drawn.  If not provided, and if ``symbolizers``
     *  is configured with an array of plain symbolizer objects, ``symbolType``
     *  should be configured.
     */
    feature: undefined,
    
    /** api: config[symbolizers]
     *  ``Array(Object)``
     *  An array of ``OpenLayers.Symbolizer`` instances or plain symbolizer
     *  objects (in painters order) for rendering a  feature.  If no
     *  symbolizers are provided, the OpenLayers default will be used. If a
     *  symbolizer is an instance of ``OpenLayers.Symbolizer``, its type will
     *  override the symbolType for rendering.
     */
    symbolizers: [OpenLayers.Feature.Vector.style["default"]],

    /** api: config[symbolType]
     *  ``String``
     *  One of ``"Point"``, ``"Line"``, ``"Polygon"`` or ``"Text"``.  Only 
     *  pertinent if OpenLayers.Symbolizer objects are not used.  If ``feature``
     *  is provided, it will be preferred.  The default is "Polygon".
     */
    symbolType: "Polygon",

    /** api: config[labelText]
     *  ``String``
     *  Label text to display for text features.
     */
    labelText: null,
    
    /** private: property[resolution]
     *  ``Number``
     *  The resolution for the renderer.
     */
    resolution: 1,
    
    /** private: property[minWidth]
     *  ``Number``
     */
    minWidth: 20,

    /** private: property[minHeight]
     *  ``Number``
     */
    minHeight: 20,

    /** private: property[renderers]
     * ``Array(String)`` 
     *  List of supported Renderer classes. Add to this list to add support for 
     *  additional renderers. The first renderer in the list that returns 
     *  ``true`` for the ``supported`` method will be used, if not defined in 
     *  the ``renderer`` config property.
     */
    renderers: ["SVG", "VML", "Canvas"],

    /** private: property[rendererOptions]
     *  ``Object``
     *  Options for the renderer. See ``OpenLayers.Renderer`` for supported 
     *  options.
     */
    rendererOptions: null,
    
    /** private: property[pointFeature]
     *  ``OpenLayers.Feature.Vector``
     *  Feature with point geometry.
     */
    pointFeature: undefined,
    
    /** private: property[lineFeature]
     *  ``OpenLayers.Feature.Vector`` 
     *  Feature with LineString geometry.  Default zig-zag is provided.
     */
    lineFeature: undefined,

    /** private: property[polygonFeature]
     *  ``OpenLayers.Feature.Vector``
     *   Feature with Polygon geometry.  Default is a soft cornered rectangle.
     */
    polygonFeature: undefined,

    /** private: property[textFeature]
     *  ``OpenLayers.Feature.Vector``
     *   Feature with invisible Point geometry and text label.
     */
    textFeature: undefined,
    
    /** private: property[renderer]
     *  ``OpenLayers.Renderer``
     */
    renderer: null,

    /** private: method[initComponent]
     */
    initComponent: function() {
        GeoExt.FeatureRenderer.superclass.initComponent.apply(this, arguments);
        Ext.applyIf(this, {
            pointFeature: new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.Point(0, 0)
            ),
            lineFeature: new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.LineString([
                    new OpenLayers.Geometry.Point(-8, -3),
                    new OpenLayers.Geometry.Point(-3, 3),
                    new OpenLayers.Geometry.Point(3, -3),
                    new OpenLayers.Geometry.Point(8, 3)
                ])
            ),
            polygonFeature: new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.Polygon([
                    new OpenLayers.Geometry.LinearRing([
                        new OpenLayers.Geometry.Point(-8, -4),
                        new OpenLayers.Geometry.Point(-6, -6),
                        new OpenLayers.Geometry.Point(6, -6),
                        new OpenLayers.Geometry.Point(8, -4),
                        new OpenLayers.Geometry.Point(8, 4),
                        new OpenLayers.Geometry.Point(6, 6),
                        new OpenLayers.Geometry.Point(-6, 6),
                        new OpenLayers.Geometry.Point(-8, 4)
                    ])
                ])
            ),
            textFeature: new OpenLayers.Feature.Vector(
                new OpenLayers.Geometry.Point(0, 0)
            )
        });
        if(!this.feature) {
            this.setFeature(null, {draw: false});
        }
        this.addEvents(
            /** api: event[click]
             *  Fires when the feature is clicked on.
             *
             *  Listener arguments:
             *  
             *  * renderer - :class:`GeoExt.FeatureRenderer` This feature renderer.
             */
            "click"
        );
    },

    /** private: method[initCustomEvents]
     */
    initCustomEvents: function() {
        this.clearCustomEvents();
        this.el.on("click", this.onClick, this);
    },
    
    /** private: method[clearCustomEvents]
     */
    clearCustomEvents: function() {
        if (this.el && this.el.removeAllListeners) {
            this.el.removeAllListeners();            
        }
    },
    
    /** private: method[onClick]
     */
    onClick: function() {
        this.fireEvent("click", this);
    },

    /** private: method[onRender]
     */
    onRender: function(ct, position) {
        if(!this.el) {
            this.el = document.createElement("div");
            this.el.id = this.getId();
        }
        if(!this.renderer || !this.renderer.supported()) {  
            this.assignRenderer();
        }
        // monkey-patch renderer so we always get a resolution
        this.renderer.map = {
            getResolution: (function() {
                return this.resolution;
            }).createDelegate(this)
        };
        
        GeoExt.FeatureRenderer.superclass.onRender.apply(this, arguments);

        this.drawFeature();
    },

    /** private: method[afterRender]
     */
    afterRender: function() {
        GeoExt.FeatureRenderer.superclass.afterRender.apply(this, arguments);
        this.initCustomEvents();
    },

    /** private: method[onResize]
     */
    onResize: function(w, h) {
        this.setRendererDimensions();
        GeoExt.FeatureRenderer.superclass.onResize.apply(this, arguments);
    },
    
    /** private: method[setRendererDimensions]
     */
    setRendererDimensions: function() {
        var gb = this.feature.geometry.getBounds();
        var gw = gb.getWidth();
        var gh = gb.getHeight();
        /**
         * Determine resolution based on the following rules:
         * 1) always use value specified in config
         * 2) if not specified, use max res based on width or height of element
         * 3) if no width or height, assume a resolution of 1
         */
        var resolution = this.initialConfig.resolution;
        if(!resolution) {
            resolution = Math.max(gw / this.width || 0, gh / this.height || 0) || 1;
        }
        this.resolution = resolution;
        // determine height and width of element
        var width = Math.max(this.width || this.minWidth, gw / resolution);
        var height = Math.max(this.height || this.minHeight, gh / resolution);
        // determine bounds of renderer
        var center = gb.getCenterPixel();
        var bhalfw = width * resolution / 2;
        var bhalfh = height * resolution / 2;
        var bounds = new OpenLayers.Bounds(
            center.x - bhalfw, center.y - bhalfh,
            center.x + bhalfw, center.y + bhalfh
        );
        this.renderer.setSize(new OpenLayers.Size(Math.round(width), Math.round(height)));
        this.renderer.setExtent(bounds, true);
    },

    /** private: method[assignRenderer]
     *  Iterate through the available renderer implementations and selects 
     *  and assign the first one whose ``supported`` method returns ``true``.
     */
    assignRenderer: function()  {
        for(var i=0, len=this.renderers.length; i<len; ++i) {
            var Renderer = OpenLayers.Renderer[this.renderers[i]];
            if(Renderer && Renderer.prototype.supported()) {
                this.renderer = new Renderer(
                    this.el, this.rendererOptions
                );
                break;
            }  
        }  
    },
    
    /** api: method[setSymbolizers]
     *  :arg symbolizers: ``Array(Object)`` An array of symbolizers
     *  :arg options: ``Object``
     *
     *  Update the symbolizers used to render the feature.
     *
     *  Valid options:
     *  
     *  * draw - ``Boolean`` Draw the feature after setting it.  Default is ``true``.
     */
    setSymbolizers: function(symbolizers, options) {
        this.symbolizers = symbolizers;
        if(!options || options.draw) {
            this.drawFeature();
        }
    },
    
    /** api: method[setSymbolType]
     *  :arg type: ``String`` One of the ``symbolType`` strings.
     *  :arg options: ``Object``
     * 
     *  Create a new feature based on the geometry type and render it.
     *
     *  Valid options:
     *  
     *  * draw - ``Boolean`` Draw the feature after setting it.  Default is ``true``.
     */
    setSymbolType: function(type, options) {
        this.symbolType = type;
        this.setFeature(null, options);
    },
    
    /** api: method[setFeature]
     *  :arg feature: ``OpenLayers.Feature.Vector`` The feature to be rendered.  
     *      If none is provided, one will be created based on ``symbolType``.
     *  :arg options: ``Object``
     *
     *  Update the feature and redraw.
     *
     *  Valid options:
     *  
     *  * draw - ``Boolean`` Draw the feature after setting it.  Default is ``true``.
     */
    setFeature: function(feature, options) {
        this.feature = feature || this[this.symbolType.toLowerCase() + "Feature"];
        if(!options || options.draw) {
            this.drawFeature();
        }
    },

    /** private: method[drawFeature]
     *  Render the feature with the symbolizers.
     */
    drawFeature: function() {
        this.renderer.clear();
        this.setRendererDimensions();
        var symbolizer, feature, geomType;
        for (var i=0, len=this.symbolizers.length; i<len; ++i) {
            symbolizer = this.symbolizers[i];
            feature = this.feature;
            if (symbolizer instanceof OpenLayers.Symbolizer) {
                symbolizer = symbolizer.clone();
                if (OpenLayers.Symbolizer.Text && 
                    symbolizer instanceof OpenLayers.Symbolizer.Text &&
                    symbolizer.graphic === false) {
                        // hide the point geometry
                        symbolizer.fill = symbolizer.stroke = false;
                }
                if (!this.initialConfig.feature) {
                    geomType = symbolizer.CLASS_NAME.split(".").pop().toLowerCase();
                    feature = this[geomType + "Feature"];
                }
            } else {
                // TODO: remove this when OpenLayers.Symbolizer is used everywhere
                symbolizer = Ext.apply({}, symbolizer);
            }
            if (symbolizer.label !== undefined && this.labelText !== null) {
                symbolizer.label = this.labelText;
            }
            this.renderer.drawFeature(
                feature.clone(),
                symbolizer
            );
        }
    },
    
    /** api: method[update]
     *  :arg options: ``Object`` Object with properties to be updated.
     * 
     *  Update the ``symbolType`` or ``feature`` and ``symbolizer`` and redraw
     *  the feature.
     *
     *  Valid options:
     *  
     *  * feature - ``OpenLayers.Feature.Vector`` The new or updated feature.  
     *      If provided, the feature gets precedence over ``symbolType``.
     *  * symbolType - ``String`` One of the allowed ``symbolType`` values.
     *  * symbolizers - ``Array(Object)`` An array of symbolizer objects.
     */
    update: function(options) {
        options = options || {};
        if(options.feature) {
            this.setFeature(options.feature, {draw: false});
        } else if(options.symbolType) {
            this.setSymbolType(options.symbolType, {draw: false});
        }
        if(options.symbolizers) {
            this.setSymbolizers(options.symbolizers, {draw: false});
        }
        this.drawFeature();
    },

    /** private: method[beforeDestroy]
     *  Private method called during the destroy sequence.
     */
    beforeDestroy: function() {
        this.clearCustomEvents();
        if (this.renderer) {
            this.renderer.destroy();
        }
    }
    
});

/** api: xtype = gx_renderer */
Ext.reg('gx_renderer', GeoExt.FeatureRenderer);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/data/LayerStore.js
 * @require OpenLayers/Map.js
 * @require OpenLayers/BaseTypes/LonLat.js
 * @require OpenLayers/BaseTypes/Bounds.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = MapPanel
 *  base_link = `Ext.Panel <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Panel>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to create a panel with a new map:
 * 
 *  .. code-block:: javascript
 *     
 *      var mapPanel = new GeoExt.MapPanel({
 *          border: false,
 *          renderTo: "div-id",
 *          map: {
 *              maxExtent: new OpenLayers.Bounds(-90, -45, 90, 45)
 *          }
 *      });
 *     
 *  Sample code to create a map panel with a bottom toolbar in a Window:
 * 
 *  .. code-block:: javascript
 * 
 *      var win = new Ext.Window({
 *          title: "My Map",
 *          items: [{
 *              xtype: "gx_mappanel",
 *              bbar: new Ext.Toolbar()
 *          }]
 *      });
 */

/** api: constructor
 *  .. class:: MapPanel(config)
 *   
 *      Create a panel container for a map. The map contained by this panel
 *      will initially be zoomed to either the center and zoom level configured
 *      by the ``center`` and ``zoom`` configuration options, or the configured
 *      ``extent``, or - if neither are provided - the extent returned by the
 *      map's ``getExtent()`` method.
 */
GeoExt.MapPanel = Ext.extend(Ext.Panel, {

    /** api: config[map]
     *  ``OpenLayers.Map or Object``  A configured map or a configuration object
     *  for the map constructor.  A configured map will be available after
     *  construction through the :attr:`map` property.
     */

    /** api: property[map]
     *  ``OpenLayers.Map`` or ``Object``  A map or map configuration.
     */
    map: null,
    
    /** api: config[layers]
     *  ``GeoExt.data.LayerStore or GeoExt.data.GroupingStore or Array(OpenLayers.Layer)``
     *  A store holding records. The layers provided here will be added to this
     *  MapPanel's map when it is rendered.
     */
    
    /** api: property[layers]
     *  :class:`GeoExt.data.LayerStore`  A store containing
     *  :class:`GeoExt.data.LayerRecord` objects.
     */
    layers: null,
    
    /** api: config[center]
     *  ``OpenLayers.LonLat or Array(Number)``  A location for the initial map
     *  center.  If an array is provided, the first two items should represent
     *  x & y coordinates.
     */
    center: null,

    /** api: config[zoom]
     *  ``Number``  An initial zoom level for the map.
     */
    zoom: null,

    /** api: config[extent]
     *  ``OpenLayers.Bounds or Array(Number)``  An initial extent for the map (used
     *  if center and zoom are not provided.  If an array, the first four items
     *  should be minx, miny, maxx, maxy.
     */
    extent: null,
    
    /** api: config[prettyStateKeys]
     *  ``Boolean`` Set this to true if you want pretty strings in the MapPanel's
     *  state keys. More specifically, layer.name instead of layer.id will be used
     *  in the state keys if this option is set to true. But in that case you have
     *  to make sure you don't have two layers with the same name. Defaults to 
     *  false.
     */
    prettyStateKeys: false,

    /** private: property[stateEvents]
     *  ``Array(String)`` Array of state events
     */
    stateEvents: ["aftermapmove",
                  "afterlayervisibilitychange",
                  "afterlayeropacitychange",
                  "afterlayerorderchange",
                  "afterlayernamechange",
                  "afterlayeradd",
                  "afterlayerremove"],

    /** private: method[initComponent]
     *  Initializes the map panel. Creates an OpenLayers map if
     *  none was provided in the config options passed to the
     *  constructor.
     */
    initComponent: function(){
        if(!(this.map instanceof OpenLayers.Map)) {
            this.map = new OpenLayers.Map(
                Ext.applyIf(this.map || {}, {allOverlays: true, fallThrough: true})
            );
        }
        var layers = this.layers;
        if(!layers || layers instanceof Array) {
            this.layers = new GeoExt.data.LayerStore({
                layers: layers,
                map: this.map.layers.length > 0 ? this.map : null
            });
        }
        
        if(typeof this.center == "string") {
            this.center = OpenLayers.LonLat.fromString(this.center);
        } else if(this.center instanceof Array) {
            this.center = new OpenLayers.LonLat(this.center[0], this.center[1]);
        }
        if(typeof this.extent == "string") {
            this.extent = OpenLayers.Bounds.fromString(this.extent);
        } else if(this.extent instanceof Array) {
            this.extent = OpenLayers.Bounds.fromArray(this.extent);
        }
        
        GeoExt.MapPanel.superclass.initComponent.call(this);

        this.addEvents(
            /** private: event[aftermapmove]
             *  Fires after the map is moved.
             */
            "aftermapmove",

            /** private: event[afterlayervisibilitychange]
             *  Fires after a layer changed visibility.
             */
            "afterlayervisibilitychange",

            /** private: event[afterlayeropacitychange]
             *  Fires after a layer changed opacity.
             */
            "afterlayeropacitychange",

            /** private: event[afterlayerorderchange]
             *  Fires after a layer order changed.
             */
            "afterlayerorderchange",

            /** private: event[afterlayernamechange]
             *  Fires after a layer name changed.
             */
            "afterlayernamechange",

            /** private: event[afterlayeradd]
             *  Fires after a layer added to the map.
             */
            "afterlayeradd",

            /** private: event[afterlayerremove]
             *  Fires after a layer removed from the map.
             */
            "afterlayerremove"
        );
        this.map.events.on({
            "moveend": this.onMoveend,
            "changelayer": this.onChangelayer,
            "addlayer": this.onAddlayer,
            "removelayer": this.onRemovelayer,
            scope: this
        });
        //TODO This should be handled by a LayoutManager
        this.on("afterlayout", function() {
            //TODO remove function check when we require OpenLayers > 2.11
            if (typeof this.map.getViewport === "function") {
                this.items.each(function(cmp) {
                    if (typeof cmp.addToMapPanel === "function") {
                        cmp.getEl().appendTo(this.map.getViewport());
                    }
                }, this);
            }
        }, this);
    },

    /** private: method[onMoveend]
     *
     *  The "moveend" listener.
     */
    onMoveend: function() {
        this.fireEvent("aftermapmove");
    },

    /** private: method[onChangelayer]
     *  :param e: ``Object``
     *
     * The "changelayer" listener.
     */
    onChangelayer: function(e) {
        if(e.property) {
            if(e.property === "visibility") {
                this.fireEvent("afterlayervisibilitychange");
            } else if(e.property === "order") {
                this.fireEvent("afterlayerorderchange");
            } else if(e.property === "name") {
                this.fireEvent("afterlayernamechange");
            } else if(e.property === "opacity") {
                this.fireEvent("afterlayeropacitychange");
            }
        }
    },

    /** private: method[onAddlayer]
     */
    onAddlayer: function() {
        this.fireEvent("afterlayeradd");
    },

    /** private: method[onRemovelayer]
     */
    onRemovelayer: function() {
        this.fireEvent("afterlayerremove");
    },

    /** private: method[applyState]
     *  :param state: ``Object`` The state to apply.
     *
     *  Apply the state provided as an argument.
     */
    applyState: function(state) {

        // if we get strings for state.x, state.y or state.zoom
        // OpenLayers will take care of converting them to the
        // appropriate types so we don't bother with that
        this.center = new OpenLayers.LonLat(state.x, state.y);
        this.zoom = state.zoom;

        // set layer visibility and opacity
        var i, l, layer, layerId, visibility, opacity;
        var layers = this.map.layers;
        for(i=0, l=layers.length; i<l; i++) {
            layer = layers[i];
            layerId = this.prettyStateKeys ? layer.name : layer.id;
            visibility = state["visibility_" + layerId];
            if(visibility !== undefined) {
                // convert to boolean
                visibility = (/^true$/i).test(visibility);
                if(layer.isBaseLayer) {
                    if(visibility) {
                        this.map.setBaseLayer(layer);
                    }
                } else {
                    layer.setVisibility(visibility);
                }
            }
            opacity = state["opacity_" + layerId];
            if(opacity !== undefined) {
                layer.setOpacity(opacity);
            }
        }
    },

    /** private: method[getState]
     *  :return:  ``Object`` The state.
     *
     *  Returns the current state for the map panel.
     */
    getState: function() {
        var state;

        // Ext delays the call to getState when a state event
        // occurs, so the MapPanel may have been destroyed
        // between the time the event occurred and the time
        // getState is called
        if(!this.map) {
            return;
        }

        // record location and zoom level
        var center = this.map.getCenter();
        // map may not be centered yet, because it may still have zero
        // dimensions or no layers
        state = center ? {
            x: center.lon,
            y: center.lat,
            zoom: this.map.getZoom()
        } : {};

        // record layer visibility and opacity
        var i, l, layer, layerId, layers = this.map.layers;
        for(i=0, l=layers.length; i<l; i++) {
            layer = layers[i];
            layerId = this.prettyStateKeys ? layer.name : layer.id;
            state["visibility_" + layerId] = layer.getVisibility();
            state["opacity_" + layerId] = layer.opacity == null ?
                1 : layer.opacity;
        }

        return state;
    },

    /** private: method[updateMapSize]
     *  Tell the map that it needs to recalculate its size and position.
     */
    updateMapSize: function() {
        if(this.map) {
            this.map.updateSize();
        }
    },

    /** private: method[renderMap]
     *  Private method called after the panel has been rendered or after it
     *  has been laid out by its parent's layout.
     */
    renderMap: function() {
        var map = this.map;
        map.render(this.body.dom);

        this.layers.bind(map);

        if (map.layers.length > 0) {
            this.setInitialExtent();
        } else {
            this.layers.on("add", this.setInitialExtent, this, {single: true});
        }
    },
    
    /** private: method[setInitialExtent]
     *  Sets the initial extent of this panel's map
     */
    setInitialExtent: function() {
        var map = this.map;
        if(this.center || this.zoom != null) {
            // both do not have to be defined
            map.setCenter(this.center, this.zoom);
        } else if(this.extent) {
            map.zoomToExtent(this.extent);
        } else {
            map.zoomToMaxExtent();
        }
    },
    
    /** private: method[afterRender]
     *  Private method called after the panel has been rendered.
     */
    afterRender: function() {
        GeoExt.MapPanel.superclass.afterRender.apply(this, arguments);
        if(!this.ownerCt) {
            this.renderMap();
        } else {
            this.ownerCt.on("move", this.updateMapSize, this);
            this.ownerCt.on({
                "afterlayout": this.afterLayout,
                scope: this
            });
        }
    },
    
    /** private: method[afterLayout]
     *  Private method called after owner container has been laid out until
     *  this panel has dimensions greater than zero.
     */
    afterLayout: function() {
        var width = this.getInnerWidth() -
                                this.body.getBorderWidth("lr");
        var height = this.getInnerHeight() -
                                this.body.getBorderWidth("tb");
        if (width > 0 && height > 0) {
            this.ownerCt.un("afterlayout", this.afterLayout, this);
            this.renderMap();
        }
    },

    /** private: method[onResize]
     *  Private method called after the panel has been resized.
     */
    onResize: function() {
        GeoExt.MapPanel.superclass.onResize.apply(this, arguments);
        this.updateMapSize();
    },
    
    /** private: method[onBeforeAdd]
     *  Private method called before a component is added to the panel.
     */
    onBeforeAdd: function(item) {
        if(typeof item.addToMapPanel === "function") {
            item.addToMapPanel(this);
        }
        GeoExt.MapPanel.superclass.onBeforeAdd.apply(this, arguments);
    },
    
    /** private: method[remove]
     *  Private method called when a component is removed from the panel.
     */
    remove: function(item, autoDestroy) {
        if(typeof item.removeFromMapPanel === "function") {
            item.removeFromMapPanel(this);
        }
        GeoExt.MapPanel.superclass.remove.apply(this, arguments);
    },

    /** private: method[beforeDestroy]
     *  Private method called during the destroy sequence.
     */
    beforeDestroy: function() {
        if(this.ownerCt) {
            this.ownerCt.un("move", this.updateMapSize, this);
        }
        if(this.map && this.map.events) {
            this.map.events.un({
                "moveend": this.onMoveend,
                "changelayer": this.onChangelayer,
                "addlayer": this.onAddlayer,
                "removelayer": this.onRemovelayer,
                scope: this
            });
        }
        // if the map panel was passed a map instance, this map instance
        // is under the user's responsibility
        if(!this.initialConfig.map ||
           !(this.initialConfig.map instanceof OpenLayers.Map)) {
            // we created the map, we destroy it
            if(this.map && this.map.destroy) {
                this.map.destroy();
            }
        }
        delete this.map;
        GeoExt.MapPanel.superclass.beforeDestroy.apply(this, arguments);
    }
    
});

/** api: function[guess]
 *  :return: ``GeoExt.MapPanel`` The first map panel found by the Ext
 *      component manager.
 *  
 *  Convenience function for guessing the map panel of an application. This
 *     can reliably be used for all applications that just have one map panel
 *     in the viewport.
 */
GeoExt.MapPanel.guess = function() {
    return Ext.ComponentMgr.all.find(function(o) { 
        return o instanceof GeoExt.MapPanel; 
    }); 
};


/** api: xtype = gx_mappanel */
Ext.reg('gx_mappanel', GeoExt.MapPanel); 

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/MapPanel.js
 * @require OpenLayers/Feature/Vector.js
 * @require OpenLayers/Geometry.js
 * @require OpenLayers/BaseTypes/Pixel.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = Popup
 *  base_link = `Ext.Window <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Window>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to create a popup anchored to a feature:
 *
 *  .. code-block:: javascript
 *
 *      var popup = new GeoExt.Popup({
 *          title: "My Popup",
 *          location: feature,
 *          width: 200,
 *          html: "<div>Popup content</div>",
 *          collapsible: true
 *      });
 */

/** api: constructor
 *  .. class:: Popup(config)
 *
 *      Popups are a specialized Window that supports anchoring
 *      to a particular location in a MapPanel.  When a popup
 *      is anchored to a location, that means that the popup
 *      will visibly point to the location on the map, and move
 *      accordingly when the map is panned or zoomed.
 */
GeoExt.Popup = Ext.extend(Ext.Window, {

    /** api: config[anchored]
     *  ``Boolean``  The popup begins anchored to its location.  Default is
     *  ``true``.
     */
    anchored: true,

    /** api: config[map]
     *  ``OpenLayers.Map`` or :class:`GeoExt.MapPanel`
     *  The map this popup will be anchored to (only required if ``anchored``
     *  is set to true and the map cannot be derived from the ``location``'s
     *  layer.
     */
    map: null,

    /** api: config[panIn]
     *  ``Boolean`` The popup should pan the map so that the popup is
     *  fully in view when it is rendered.  Default is ``true``.
     */
    panIn: true,

    /** api: config[unpinnable]
     *  ``Boolean`` The popup should have a "unpin" tool that unanchors it from
     *  its location.  Default is ``true``.
     */
    unpinnable: true,

    /** api: config[location]
     *  ``OpenLayers.Feature.Vector`` or ``OpenLayers.LonLat`` or
     *  ``OpenLayers.Pixel`` or ``OpenLayers.Geometry`` A location for this
     *  popup's anchor.
     */

    /** private: property[location]
     *  ``OpenLayers.LonLat``
     */
    location: null,

    /** private: property[insideViewport]
     *  ``Boolean`` Wether the popup is currently inside the map viewport.
     */
    insideViewport: null,

    /**
     * Some Ext.Window defaults need to be overriden here
     * because some Ext.Window behavior is not currently supported.
     */

    /** private: config[animCollapse]
     *  ``Boolean`` Animate the transition when the panel is collapsed.
     *  Default is ``false``.  Collapsing animation is not supported yet for
     *  popups.
     */
    animCollapse: false,

    /** private: config[draggable]
     *  ``Boolean`` Enable dragging of this Panel.  Defaults to ``false``
     *  because the popup defaults to being anchored, and anchored popups
     *  should not be draggable.
     */
    draggable: false,

    /** private: config[shadow]
     *  ``Boolean`` Give the popup window a shadow.  Defaults to ``false``
     *  because shadows are not supported yet for popups (the shadow does
     *  not look good with the anchor).
     */
    shadow: false,

    /** api: config[popupCls]
     *  ``String`` CSS class name for the popup DOM elements.  Default is
     *  "gx-popup".
     */
    popupCls: "gx-popup",

    /** api: config[ancCls]
     *  ``String``  CSS class name for the popup's anchor.
     */
    ancCls: null,

    /** api: config[anchorPosition]
     *  ``String``  Controls the anchor position for the popup. If set to
     *  ``auto``, the anchor will be positioned on the top or the bottom of
     *  the window, minimizing map movement. Supported values are ``bottom-left``,
     *  ``bottom-right``, ``top-left``, ``top-right`` or ``auto``.
     *  Defaults to ``auto``.
     */
    anchorPosition: "auto",

    /** private: method[initComponent]
     *  Initializes the popup.
     */
    initComponent: function() {
        if(this.map instanceof GeoExt.MapPanel) {
            this.map = this.map.map;
        }
        if(!this.map && this.location instanceof OpenLayers.Feature.Vector &&
                                                        this.location.layer) {
            this.map = this.location.layer.map;
        }
        if (this.location instanceof OpenLayers.Feature.Vector) {
            this.location = this.location.geometry;
        }
        if (this.location instanceof OpenLayers.Geometry) {
            if (typeof this.location.getCentroid == "function") {
                this.location = this.location.getCentroid();
            }
            this.location = this.location.getBounds().getCenterLonLat();
        } else if (this.location instanceof OpenLayers.Pixel) {
            this.location = this.map.getLonLatFromViewPortPx(this.location);
        }
        if (!(this.location instanceof OpenLayers.LonLat)) {
            this.anchored = false;
        }

        var mapExtent =  this.map.getExtent();
        if (mapExtent && this.location) {
            this.insideViewport = mapExtent.containsLonLat(this.location);
        }

        if(this.anchored) {
            this.addAnchorEvents();
            this.elements += ',anc';
        } else {
            this.unpinnable = false;
        }

        this.baseCls = this.popupCls + " " + this.baseCls;

        GeoExt.Popup.superclass.initComponent.call(this);
    },

    /** private: method[onRender]
     *  Executes when the popup is rendered.
     */
    onRender: function(ct, position) {
        GeoExt.Popup.superclass.onRender.call(this, ct, position);
        if (this.anchored) {
            this.ancCls = this.popupCls + "-anc";
            //create anchor dom element.
            this.createElement("anc", this.el.dom);
        } else {
            this.makeDraggable();
        }
    },

    /** private: method[initTools]
     *  Initializes the tools on the popup.  In particular,
     *  it adds the 'unpin' tool if the popup is unpinnable.
     */
    initTools : function() {
        if(this.unpinnable) {
            this.addTool({
                id: 'unpin',
                handler: this.unanchorPopup.createDelegate(this, [])
            });
        }

        GeoExt.Popup.superclass.initTools.call(this);
    },

    /** private: method[show]
     *  Override.
     */
    show: function() {
        GeoExt.Popup.superclass.show.apply(this, arguments);
        if(this.anchored) {
            this.position();
            if(this.panIn && !this._mapMove) {
                this.panIntoView();
            }
        }
    },

    /** private: method[maximize]
     *  Override.
     */
    maximize: function() {
        if(!this.maximized && this.anc) {
            this.unanchorPopup();
        }
        GeoExt.Popup.superclass.maximize.apply(this, arguments);
    },

    /** api: method[setSize]
     *  :param w: ``Integer``
     *  :param h: ``Integer``
     *
     *  Sets the size of the popup, taking into account the size of the anchor.
     */
    setSize: function(w, h) {
        if(this.anc) {
            var ancSize = this.anc.getSize();
            if(typeof w == 'object') {
                h = w.height - ancSize.height;
                w = w.width;
            } else if(!isNaN(h)){
                h = h - ancSize.height;
            }
        }
        GeoExt.Popup.superclass.setSize.call(this, w, h);
    },

    /** private: method[position]
     *  Positions the popup relative to its location
     */
    position: function() {
        var me = this;
        if(me._mapMove === true) {
            me.insideViewport = me.map.getExtent().containsLonLat(me.location);
            if(me.insideViewport !== me.isVisible()) {
                me.setVisible(me.insideViewport);
            }
        }

        if(me.isVisible()) {
            var locationPx = me.map.getPixelFromLonLat(me.location),
                mapBox = Ext.fly(me.map.div).getBox(true),
                y = locationPx.y + mapBox.y,
                x = locationPx.x + mapBox.x,
                elSize = me.el.getSize(),
                ancSize = me.anc.getSize(),
                ancPos = me.anchorPosition;
            if (ancPos.indexOf("right") > -1 || locationPx.x > mapBox.width / 2) {
                // right
                me.anc.addClass("right");
                var ancRight = me.el.getX(true) + elSize.width -
                               me.anc.getX(true) - ancSize.width;
                x -= elSize.width - ancRight - ancSize.width / 2;
            } else {
                // left
                me.anc.removeClass("right");
                var ancX = me.anc.getLeft(true);
                x -= ancX + ancSize.width / 2;
            }

            if (ancPos.indexOf("bottom") > -1 || locationPx.y > mapBox.height / 2) {
                // bottom
                me.anc.removeClass("top");
                y -= elSize.height + ancSize.height;
            } else {
                // top
                me.anc.addClass("top");
                y += ancSize.height; // ok
            }

            // Needed to have the right position on the first display
            // (no flash on the center of the map).
            me.setPagePosition(x, y);
            // position in the next cycle - otherwise strange shifts can occur.
            window.setTimeout(function() {
                if (me.el.dom) {
                    me.setPagePosition(x, y);
                }
            }, 0);
        }
    },

    /** private: method[makeDraggable]
     *  Make the window draggable
     */
    makeDraggable: function() {
        this.draggable = true;
        this.header.addClass("x-window-draggable");
        this.dd = new Ext.Window.DD(this);
    },

    /** private: method[unanchorPopup]
     *  Unanchors a popup from its location.  This removes the popup from its
     *  MapPanel and adds it to the page body.
     */
    unanchorPopup: function() {
        this.removeAnchorEvents();

        this.makeDraggable();

        //remove anchor
        this.anc.remove();
        this.anc = null;

        //hide unpin tool
        this.tools.unpin.hide();
    },

    /** private: method[panIntoView]
     *  Pans the MapPanel's map so that an anchored popup can come entirely
     *  into view, with padding specified as per normal OpenLayers.Map popup
     *  padding.
     */
    panIntoView: function() {
        var mapBox = Ext.fly(this.map.div).getBox(true);

        //assumed viewport takes up whole body element of map panel
        var popupPos =  this.getPosition(true);
        popupPos[0] -= mapBox.x;
        popupPos[1] -= mapBox.y;

        var panelSize = [mapBox.width, mapBox.height]; // [X,Y]

        var popupSize = this.getSize();

        var newPos = [popupPos[0], popupPos[1]];

        //For now, using native OpenLayers popup padding.  This may not be ideal.
        var padding = this.map.paddingForPopups;

        // X
        if(popupPos[0] < padding.left) {
            newPos[0] = padding.left;
        } else if(popupPos[0] + popupSize.width > panelSize[0] - padding.right) {
            newPos[0] = panelSize[0] - padding.right - popupSize.width;
        }

        // Y
        if(popupPos[1] < padding.top) {
            newPos[1] = padding.top;
        } else if(popupPos[1] + popupSize.height > panelSize[1] - padding.bottom) {
            newPos[1] = panelSize[1] - padding.bottom - popupSize.height;
        }

        var dx = popupPos[0] - newPos[0];
        var dy = popupPos[1] - newPos[1];

        this.map.pan(dx, dy);
    },

    /** private: method[onMapMove]
     */
    onMapMove: function() {
        if (!(this.hidden && this.insideViewport)){
            this._mapMove = true;
            this.position();
            delete this._mapMove;
        }
    },

    /** private: method[addAnchorEvents]
     */
    addAnchorEvents: function() {
        this.map.events.on({
            "move" : this.onMapMove,
            scope : this
        });

        this.on({
            "resize": this.position,
            "collapse": this.position,
            "expand": this.position,
            scope: this
        });
    },

    /** private: method[removeAnchorEvents]
     */
    removeAnchorEvents: function() {
        //stop position with location
        this.map.events.un({
            "move" : this.onMapMove,
            scope : this
        });

        this.un("resize", this.position, this);
        this.un("collapse", this.position, this);
        this.un("expand", this.position, this);

    },

    /** private: method[beforeDestroy]
     *  Cleanup events before destroying the popup.
     */
    beforeDestroy: function() {
        if(this.anchored) {
            this.removeAnchorEvents();
        }
        GeoExt.Popup.superclass.beforeDestroy.call(this);
    }
});

/** api: xtype = gx_popup */
Ext.reg('gx_popup', GeoExt.Popup);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */
/**
 * @require OpenLayers/Filter/Comparison.js
 * @require OpenLayers/Filter/Logical.js
 */

Ext.namespace("GeoExt.form");

/** private: function[toFilter]
 *  :param form: ``Ext.form.BasicForm|Ext.form.FormPanel``
 *  :param logicalOp: ``String`` Either ``OpenLayers.Filter.Logical.AND`` or
 *      ``OpenLayers.Filter.Logical.OR``, set to
 *      ``OpenLayers.Filter.Logical.AND`` if null or undefined
 *  :param wildcard: ``Integer`` Determines the wildcard behaviour of like
 *      queries. This behaviour can either be: none, prepend, append or both.
 *
 *  :return: ``OpenLayers.Filter``
 *
 *  Create an {OpenLayers.Filter} object from a {Ext.form.BasicForm}
 *      or a {Ext.form.FormPanel} instance.
 */
GeoExt.form.toFilter = function(form, logicalOp, wildcard) {
    if(form instanceof Ext.form.FormPanel) {
        form = form.getForm();
    }
    var filters = [], values = form.getValues(false);
    for(var prop in values) {
        var s = prop.split("__");

        var value = values[prop], type;

        if(s.length > 1 && 
           (type = GeoExt.form.toFilter.FILTER_MAP[s[1]]) !== undefined) {
            prop = s[0];
        } else {
            type = OpenLayers.Filter.Comparison.EQUAL_TO;
        }

        if (type === OpenLayers.Filter.Comparison.LIKE) {
            switch(wildcard) {
                case GeoExt.form.ENDS_WITH:
                    value = '.*' + value;
                    break;
                case GeoExt.form.STARTS_WITH:
                    value += '.*';
                    break;
                case GeoExt.form.CONTAINS:
                    value = '.*' + value + '.*';
                    break;
                default:
                    // do nothing, just take the value
                    break;
            }
        }

        filters.push(
            new OpenLayers.Filter.Comparison({
                type: type,
                value: value,
                property: prop
            })
        );
    }

    return filters.length == 1 && logicalOp != OpenLayers.Filter.Logical.NOT ?
        filters[0] :
        new OpenLayers.Filter.Logical({
            type: logicalOp || OpenLayers.Filter.Logical.AND,
            filters: filters
        });
};

/** private: constant[FILTER_MAP]
 *  An object mapping operator strings as found in field names to
 *      ``OpenLayers.Filter.Comparison`` types.
 */
GeoExt.form.toFilter.FILTER_MAP = {
    "eq": OpenLayers.Filter.Comparison.EQUAL_TO,
    "ne": OpenLayers.Filter.Comparison.NOT_EQUAL_TO,
    "lt": OpenLayers.Filter.Comparison.LESS_THAN,
    "le": OpenLayers.Filter.Comparison.LESS_THAN_OR_EQUAL_TO,
    "gt": OpenLayers.Filter.Comparison.GREATER_THAN,
    "ge": OpenLayers.Filter.Comparison.GREATER_THAN_OR_EQUAL_TO,
    "like": OpenLayers.Filter.Comparison.LIKE
};

GeoExt.form.ENDS_WITH = 1;
GeoExt.form.STARTS_WITH = 2;
GeoExt.form.CONTAINS = 3;

/** private: function[recordToField]
 *  :param record: ``Ext.data.Record``, typically from an attributeStore
 *  :param options: ``Object``, optional object litteral. Valid options:
 *
 *  * checkboxLabelProperty - ``String`` The name of the property used to set
 *  the label in the checkbox. Only applies if the record is of the "boolean"
 *  type. Possible values are "boxLabel" and "fieldLabel". Default is "boxLabel".
 *  * mandatoryFieldLabelStyle - ``String`` A CSS style specification string
 *  to apply to the field label if the field is not nillable (that is,
 *  the corresponding record has the "nillable" attribute set to ``false``).
 *  Default is ``"font-weigth: bold;"``.
 *  * labelTpl - ``Ext.Template`` or ``String`` or ``Array`` If set, 
 *  the field label is obtained by applying the record's data hash to this 
 *  template. This allows for very customizable field labels. 
 *  See for instance :
 *
 *  .. code-block:: javascript
 *
 *      var formPanel = new Ext.form.FormPanel({
 *          autoScroll: true,
 *          plugins: [
 *              new GeoExt.plugins.AttributeForm({
 *                  attributeStore: store,
 *                  recordToFieldOptions: {
 *                      mandatoryFieldLabelStyle: 'font-style:italic;',
 *                      labelTpl: new Ext.XTemplate(
 *                          '<span ext:qtip="{[this.getTip(values)]}">{name}</span>', {
 *                              compiled: true,
 *                              disableFormats: true,
 *                              getTip: function(v) {
 *                                  if (!v.type) {
 *                                      return '';
 *                                  }
 *                                  var type = v.type.split(":").pop();
 *                                  return OpenLayers.i18n(type) + 
 *                                      (v.nillable ? '' : ' (required)');
 *                              }
 *                          }
 *                      )
 *                  }
 *              })
 *          ]
 *      });
 *
 *  :return: ``Object`` An object literal with a xtype property, use
 *  ``Ext.ComponentMgr.create`` (or ``Ext.create`` in Ext 3) to create
 *  an ``Ext.form.Field`` from this object.
 *
 *  This function can be used to create an ``Ext.form.Field`` from
 *  an ``Ext.data.Record`` containing name, type, restriction and
 *  label fields.
 */
GeoExt.form.recordToField = function(record, options) {

    options = options || {};

    var type = record.get("type");
    if(typeof type === "object" && type.xtype) {
        // we have an xtype'd object literal in the type
        // field, just return it
        return type;
    }
    type = type.split(":").pop(); // remove ns prefix
    
    var field;
    var name = record.get("name");
    var restriction = record.get("restriction") || {};
    var nillable = record.get("nillable") || false;
    
    var label = record.get("label");
    var labelTpl = options.labelTpl;
    if (labelTpl) {
        var tpl = (labelTpl instanceof Ext.Template) ?
            labelTpl :
            new Ext.XTemplate(labelTpl);
        label = tpl.apply(record.data);
    } else if (label == null) {
        // use name for label if label isn't defined in the record
        label = name;
    }
    
    var baseOptions = {
        name: name,
        labelStyle: nillable ? '' : 
                        options.mandatoryFieldLabelStyle != null ? 
                            options.mandatoryFieldLabelStyle : 
                            'font-weight:bold;'
    };

    var r = GeoExt.form.recordToField.REGEXES;

    if (restriction.enumeration) {
        field = Ext.apply({
            xtype: "combo",
            fieldLabel: label,
            mode: "local",
            forceSelection: true,
            triggerAction: "all",
            editable: false,
            store: restriction.enumeration
        }, baseOptions);
    } else if(type.match(r["text"])) {
        var maxLength = restriction["maxLength"] !== undefined ?
            parseFloat(restriction["maxLength"]) : undefined;
        var minLength = restriction["minLength"] !== undefined ?
            parseFloat(restriction["minLength"]) : undefined;
        field = Ext.apply({
            xtype: "textfield",
            fieldLabel: label,
            maxLength: maxLength,
            minLength: minLength
        }, baseOptions);
    } else if(type.match(r["number"])) {
        var maxValue = restriction["maxInclusive"] !== undefined ?
            parseFloat(restriction["maxInclusive"]) : undefined;
        var minValue = restriction["minInclusive"] !== undefined ?
            parseFloat(restriction["minInclusive"]) : undefined;
        field = Ext.apply({
            xtype: "numberfield",
            fieldLabel: label,
            maxValue: maxValue,
            minValue: minValue
        }, baseOptions);
    } else if(type.match(r["boolean"])) {
        field = Ext.apply({
            xtype: "checkbox"
        }, baseOptions);
        var labelProperty = options.checkboxLabelProperty || "boxLabel";
        field[labelProperty] = label;
    } else if(type.match(r["date"])) {
        field = Ext.apply({
            xtype: "datefield",
            fieldLabel: label,
            format: 'c'
        }, baseOptions);
    }

    return field;
};

/** private: constant[REGEXES]
  *  ``Object`` Regular expressions for determining what type
  *  of field to create from an attribute record.
  */
GeoExt.form.recordToField.REGEXES = {
    "text": new RegExp(
        "^(text|string)$", "i"
    ),
    "number": new RegExp(
        "^(number|float|decimal|double|int|long|integer|short)$", "i"
    ),
    "boolean": new RegExp(
        "^(boolean)$", "i"
    ),
    "date": new RegExp(
        "^(date|dateTime)$", "i"
    )
};

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.form
 *  class = SearchAction
 *  base_link = `Ext.form.Action <http://dev.sencha.com/deploy/dev/docs/?class=Ext.form.Action>`_
 */

/**
 * @include GeoExt/widgets/form.js
 */

Ext.namespace("GeoExt.form");
 
/** api: example
 *  Sample code showing how to use a GeoExt SearchAction with an Ext form panel:
 *  
 *  .. code-block:: javascript
 *
 *      var formPanel = new Ext.form.Panel({
 *          renderTo: "formpanel",
 *          items: [{
 *              xtype: "textfield",
 *              name: "name__like",
 *              value: "mont"
 *          }, {
 *              xtype: "textfield",
 *              name: "elevation__ge",
 *              value: "2000"
 *          }]
 *      });
 *
 *      var searchAction = new GeoExt.form.SearchAction(formPanel.getForm(), {
 *          protocol: new OpenLayers.Protocol.WFS({
 *              url: "http://publicus.opengeo.org/geoserver/wfs",
 *              featureType: "tasmania_roads",
 *              featureNS: "http://www.openplans.org/topp"
 *          }),
 *          abortPrevious: true
 *      });
 *
 *      formPanel.getForm().doAction(searchAction, {
 *          callback: function(response) {
 *              // response.features includes the features read
 *              // from the server through the protocol
 *          }
 *      });
 */

/** api: constructor
 *  .. class:: SearchAction(form, options)
 *
 *      A specific ``Ext.form.Action`` to be used when using a form to do
 *      trigger search requests througn an ``OpenLayers.Protocol``.
 *
 *      Arguments:
 *
 *      * form ``Ext.form.BasicForm`` A basic form instance.
 *      * options ``Object`` Options passed to the protocol'read method
 *            One can add an abortPrevious property to these options, if set
 *            to true, the abort method will be called on the protocol if
 *            there's a pending request.
 *
 *      When run this action builds an ``OpenLayers.Filter`` from the form
 *      and passes this filter to its protocol's read method. The form fields
 *      must be named after a specific convention, so that an appropriate 
 *      ``OpenLayers.Filter.Comparison`` filter is created for each
 *      field.
 *
 *      For example a field with the name ``foo__like`` would result in an
 *      ``OpenLayers.Filter.Comparison`` of type
 *      ``OpenLayers.Filter.Comparison.LIKE`` being created.
 *
 *      Here is the convention:
 *
 *      * ``<name>__eq: OpenLayers.Filter.Comparison.EQUAL_TO``
 *      * ``<name>__ne: OpenLayers.Filter.Comparison.NOT_EQUAL_TO``
 *      * ``<name>__lt: OpenLayers.Filter.Comparison.LESS_THAN``
 *      * ``<name>__le: OpenLayers.Filter.Comparison.LESS_THAN_OR_EQUAL_TO``
 *      * ``<name>__gt: OpenLayers.Filter.Comparison.GREATER_THAN``
 *      * ``<name>__ge: OpenLayers.Filter.Comparison.GREATER_THAN_OR_EQUAL_TO``
 *      * ``<name>__like: OpenLayers.Filter.Comparison.LIKE``
 *
 *      In most cases your would not directly create ``GeoExt.form.SearchAction``
 *      objects, but use :class:`GeoExt.form.FormPanel` instead.
 */
GeoExt.form.SearchAction = Ext.extend(Ext.form.Action, {
    /** private: property[type]
     *  ``String`` The action type string.
     */
    type: "search",

    /** api: property[response]
     *  ``OpenLayers.Protocol.Response`` A reference to the response
     *  resulting from the search request. Read-only.
     */
    response: null,

    /** private */
    constructor: function(form, options) {
        GeoExt.form.SearchAction.superclass.constructor.call(this, form, options);
    },

    /** private: method[run]
     *  Run the action.
     */
    run: function() {
        var o = this.options;
        var f = GeoExt.form.toFilter(this.form, o.logicalOp, o.wildcard);
        if(o.clientValidation === false || this.form.isValid()){

            if (o.abortPrevious && this.form.prevResponse) {
                o.protocol.abort(this.form.prevResponse);
            }

            this.form.prevResponse = o.protocol.read(
                Ext.applyIf({
                    filter: f,
                    callback: this.handleResponse,
                    scope: this
                }, o)
            );

        } else if(o.clientValidation !== false){
            // client validation failed
            this.failureType = Ext.form.Action.CLIENT_INVALID;
            this.form.afterAction(this, false);
        }
    },

    /** private: method[handleResponse]
     *  :param response: ``OpenLayers.Protocol.Response`` The response
     *  object.
     *
     *  Handle the response to the search query.
     */
    handleResponse: function(response) {
        this.form.prevResponse = null;
        this.response = response;
        if(response.success()) {
            this.form.afterAction(this, true);
        } else {
            this.form.afterAction(this, false);
        }
        var o = this.options;
        if(o.callback) {
            o.callback.call(o.scope, response);
        }
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/form/SearchAction.js
 */

/** api: (define)
 *  module = GeoExt.form
 *  class = BasicForm
 *  base_link = `Ext.form.BasicForm <http://dev.sencha.com/deploy/dev/docs/?class=Ext.form.BasicForm>`_
 */

Ext.namespace("GeoExt.form");

/** api: constructor
 *  .. class:: BasicForm(config)
 *
 *      A specific ``Ext.form.BasicForm`` whose doAction method creates
 *      a :class:`GeoExt.form.SearchAction` if it is passed the string
 *      "search" as its first argument.
 *
 *      In most cases one would not use this class directly, but
 *      :class:`GeoExt.form.FormPanel` instead.
 */
GeoExt.form.BasicForm = Ext.extend(Ext.form.BasicForm, {
    /** private: property[protocol]
     *  ``OpenLayers.Protocol`` The protocol configured in this
     *  instance.
     */
    protocol: null,

    /**
     * private: property[prevResponse]
     * ``OpenLayers.Protocol.Response`` The response return by a call to
     *  protocol.read method.
     */
    prevResponse: null,

    /**
     * api: config[autoAbort]
     * ``Boolean`` Tells if pending requests should be aborted
     *      when a new action is performed.
     */
    autoAbort: true,

    /** api: method[doAction]
     *  :param action: ``String or Ext.form.Action`` Either the name
     *      of the action or a ``Ext.form.Action`` instance.
     *  :param options: ``Object`` The options passed to the Action
     *      constructor.
     *  :return: :class:`GeoExt.form.BasicForm` This form.
     *
     *  Performs the action, if the string "search" is passed as the
     *  first argument then a :class:`GeoExt.form.SearchAction` is created.
     */
    doAction: function(action, options) {
        if(action == "search") {
            options = Ext.applyIf(options || {}, {
                protocol: this.protocol,
                abortPrevious: this.autoAbort
            });
            action = new GeoExt.form.SearchAction(this, options);
        }
        return GeoExt.form.BasicForm.superclass.doAction.call(
            this, action, options
        );
    },

    /** api: method[search]
     *  :param options: ``Object`` The options passed to the Action
     *      constructor.
     *  :return: :class:`GeoExt.form.BasicForm` This form.
     *  
     *  Shortcut to do a search action.
     */
    search: function(options) {
        return this.doAction("search", options);
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * Published under the BSD license.
 * See http://geoext.org/svn/geoext/core/trunk/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.form
 *  class = GeocoderComboBox
 *  base_link = `Ext.form.ComboBox <http://dev.sencha.com/deploy/dev/docs/?class=Ext.form.ComboBox>`_
 */
Ext.namespace("GeoExt.form");

/** api: constructor
 *  .. class:: GeocoderComboBox(config)
 *
 *  Creates a combo box that handles results from a geocoding service. By
 *  default it uses OSM Nominatim, but it can be configured with a custom store
 *  to use other services. If the user enters a valid address in the search
 *  box, the combo's store will be populated with records that match the
 *  address.  By default, records have the following fields:
 *  
 *  * name - ``String`` The formatted address.
 *  * lonlat - ``Array`` Location matching address, for use with
 *      OpenLayers.LonLat.fromArray.
 *  * bounds - ``Array`` Recommended viewing bounds, for use with
 *      OpenLayers.Bounds.fromArray.
 */   
GeoExt.form.GeocoderComboBox = Ext.extend(Ext.form.ComboBox, {
    
    /** api: config[emptyText]
     *  ``String`` Text to display for an empty field (i18n).
     */
    emptyText: "Search",
    
    /** api: config[map]
     *  ``GeoExt.MapPanel|OpenLayers.Map`` The map that will be controlled by
     *  this GeoCoderComboBox. Only used if this component is not added as item
     *  or toolbar item to a ``GeoExt.MapPanel``.
     */
    
    /** private: property[map]
     *  ``OpenLayers.Map``
     */

    /** api: config[srs]
     *  ``String|OpenLayers.Projection`` The srs used by the geocoder service.
     *  Default is "EPSG:4326".
     */
    srs: "EPSG:4326",
    
    /** api: property[srs]
     *  ``OpenLayers.Projection``
     */
    
    /** api: config[zoom]
     *  ``String`` The minimum zoom level to use when zooming to a location.
     *  Not used when zooming to a bounding box. Default is 10.
     */
    zoom: 10,
    
    /** api: config[layer]
     *  ``OpenLayers.Layer.Vector`` If provided, a marker will be drawn on this
     *  layer with the location returned by the geocoder. The location will be
     *  cleared when the map panned. 
     */
    
    /** api: config[queryDelay]
     *  ``Number`` Delay before the search occurs.  Default is 100ms.
     */
    queryDelay: 100,
    
    /** api: config[valueField]
     *  ``String`` Field from selected record to use when the combo's
     *  :meth:`getValue` method is called.  Default is "bounds". This field is
     *  supposed to contain an array of [left, bottom, right, top] coordinates
     *  for a bounding box or [x, y] for a location. 
     */
    valueField: "bounds",

    /** api: config[displayField]
     *  ``String`` The field to display in the combo boy. Default is
     *  "name" for instant use with the default store for this component.
     */
    displayField: "name",
    
    /** api: config[locationField]
     *  ``String`` The field to get the location from. This field is supposed
     *  to contain an array of [x, y] for a location. Default is "lonlat" for
     *  instant use with the default store for this component.
     */
    locationField: "lonlat",
    
    /** api: config[url]
     *  ``String`` URL template for querying the geocoding service. If a
     *  :obj:`store` is configured, this will be ignored. Note that the
     *  :obj:`queryParam` will be used to append the user's combo box
     *  input to the url. Default is
     *  "http://nominatim.openstreetmap.org/search?format=json", for instant
     *  use with the OSM Nominatim geolocator. However, if you intend to use
     *  that, note the
     *  `Nominatim Usage Policy <http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy>`_.
     */
    url: "http://nominatim.openstreetmap.org/search?format=json",
    
    /** api: config[queryParam]
     *  ``String`` The query parameter for the user entered search text.
     *  Default is "q" for instant use with OSM Nominatim.
     */
    queryParam: "q",
    
    /** api: config[minChars]
     *  ``Number`` Minimum number of entered characters to trigger a search.
     *  Default is 3.
     */
    minChars: 3,
    
    /** api: config[store]
     *  ``Ext.data.Store`` The store used for this combo box. Default is a
     *  store with a ScriptTagProxy and the url configured as :obj:`url`
     *  property.
     */
    
    /** private: property[center]
     *  ``OpenLayers.LonLat`` Last center that was zoomed to after selecting
     *  a location in the combo box.
     */
    
    /** private: property[locationFeature]
     *  ``OpenLayers.Feature.Vector`` Last location provided by the geolocator.
     *  Only set if :obj:`layer` is configured.
     */
    
    /** private: method[initComponent]
     *  Override
     */
    initComponent: function() {
        if (this.map) {
            this.setMap(this.map);
        }
        if (Ext.isString(this.srs)) {
            this.srs = new OpenLayers.Projection(this.srs);
        }
        if (!this.store) {
            this.store = new Ext.data.JsonStore({
                root: null,
                fields: [
                    {name: "name", mapping: "display_name"},
                    {name: "bounds", convert: function(v, rec) {
                        var bbox = rec.boundingbox;
                        return [bbox[2], bbox[0], bbox[3], bbox[1]];
                    }},
                    {name: "lonlat", convert: function(v, rec) {
                        return [rec.lon, rec.lat];
                    }}
                ],
                proxy: new Ext.data.ScriptTagProxy({
                    url: this.url,
                    callbackParam: "json_callback"
                })
            });
        }
        
        this.on({
            added: this.handleAdded,
            select: this.handleSelect,
            focus: function() {
                this.clearValue();
                this.removeLocationFeature();
            },
            scope: this
        });
        
        return GeoExt.form.GeocoderComboBox.superclass.initComponent.apply(this, arguments);
    },
    
    /** private: method[handleAdded]
     *  When this component is added to a container, see if it has a parent
     *  MapPanel somewhere and set the map
     */
    handleAdded: function() {
        var mapPanel = this.findParentBy(function(cmp) {
            return cmp instanceof GeoExt.MapPanel;
        });
        if (mapPanel) {
            this.setMap(mapPanel);
        }
    },
    
    /** private: method[handleSelect]
     *  Zoom to the selected location, and also set a location marker if this
     *  component was configured with an :obj:`layer`.
     */
    handleSelect: function(combo, rec) {                
        var value = this.getValue();
        if (Ext.isArray(value)) {
            var mapProj = this.map.getProjectionObject();
            delete this.center;
            delete this.locationFeature;
            if (value.length === 4) {
                this.map.zoomToExtent(
                    OpenLayers.Bounds.fromArray(value)
                        .transform(this.srs, mapProj)
                );
            } else {
                this.map.setCenter(
                    OpenLayers.LonLat.fromArray(value)
                        .transform(this.srs, mapProj),
                    Math.max(this.map.getZoom(), this.zoom)
                );
            }
            this.center = this.map.getCenter();

            var lonlat = rec.get(this.locationField);
            if (this.layer && lonlat) {
                var geom = new OpenLayers.Geometry.Point(
                    lonlat[0], lonlat[1]).transform(this.srs, mapProj);
                this.locationFeature = new OpenLayers.Feature.Vector(geom, rec.data);
                this.layer.addFeatures([this.locationFeature]);
            }
        }
        // blur the combo box
        //TODO Investigate if there is a more elegant way to do this.
        (function() {
            this.triggerBlur();
            this.el.blur();
        }).defer(100, this);
    },
    
    /** private: method[removeLocationFeature]
     *  Remove the location marker from the :obj:`layer` and destroy the
     *  :obj:`locationFeature`.
     */
    removeLocationFeature: function() {
        if (this.locationFeature) {
            this.layer.destroyFeatures([this.locationFeature]);
        }
    },
    
    /** private: method[clearResult]
     *  Handler for the map's moveend event. Clears the selected location
     *  when the map center has changed.
     */
    clearResult: function() {
        if (this.center && !this.map.getCenter().equals(this.center)) {
            this.clearValue();
        }
    },
    
    /** private: method[setMap]
     *  :param map: ``GeoExt.MapPanel||OpenLayers.Map``
     *
     *  Set the :obj:`map` for this instance.
     */
    setMap: function(map) {
        if (map instanceof GeoExt.MapPanel) {
            map = map.map;
        }
        this.map = map;
        map.events.on({
            "moveend": this.clearResult,
            "click": this.removeLocationFeature,
            scope: this
        });
    },
    
    /** private: method[addToMapPanel]
     *  :param panel: :class:`GeoExt.MapPanel`
     *  
     *  Called by a MapPanel if this component is one of the items in the panel.
     */
    addToMapPanel: Ext.emptyFn,
    
    /** private: method[beforeDestroy]
     */
    beforeDestroy: function() {
        this.map.events.un({
            "moveend": this.clearResult,
            "click": this.removeLocationFeature,
            scope: this
        });
        this.removeLocationFeature();
        delete this.map;
        delete this.layer;
        delete this.center;
        GeoExt.form.GeocoderComboBox.superclass.beforeDestroy.apply(this, arguments);
    }
});

/** api: xtype = gx_geocodercombo */
Ext.reg("gx_geocodercombo", GeoExt.form.GeocoderComboBox);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.form
 *  class = FormPanel
 *  base_link = `Ext.form.FormPanel <http://dev.sencha.com/deploy/dev/docs/?class=Ext.form.FormPanel>`_
 */

/**
 * @include GeoExt/widgets/form/BasicForm.js
 */

Ext.namespace("GeoExt.form");

/** api: example
 *  Sample code showing how to use a GeoExt form panel.
 *
 *  .. code-block:: javascript
 *
 *      var formPanel = new GeoExt.form.FormPanel({
 *          renderTo: "formpanel",
 *          protocol: new OpenLayers.Protocol.WFS({
 *              url: "http://publicus.opengeo.org/geoserver/wfs",
 *              featureType: "tasmania_roads",
 *              featureNS: "http://www.openplans.org/topp"
 *          }),
 *          items: [{
 *              xtype: "textfield",
 *              name: "name__ilike",
 *              value: "mont"
 *          }, {
 *              xtype: "textfield",
 *              name: "elevation__ge",
 *              value: "2000"
 *          }],
 *          listeners: {
 *              actioncomplete: function(form, action) {
 *                  // this listener triggers when the search request
 *                  // is complete, the OpenLayers.Protocol.Response
 *                  // resulting from the request is available
 *                  // in "action.response"
 *              }
 *          }
 *      });
 *
 *      formPanel.addButton({
 *          text: "search",
 *          handler: function() {
 *              this.search();
 *          },
 *          scope: formPanel
 *      });
 */

/** api: constructor
 *  .. class:: FormPanel(config)
 *
 *      A specific ``Ext.form.FormPanel`` whose internal form is a
 *      :class:`GeoExt.form.BasicForm` instead of ``Ext.form.BasicForm``.
 *      One would use this form to do search requests through
 *      an ``OpenLayers.Protocol`` object (``OpenLayers.Protocol.WFS``
 *      for example).
 *
 *      Look at :class:`GeoExt.form.SearchAction` to understand how
 *      form fields must be named for appropriate filters to be
 *      passed to the protocol.
 */
GeoExt.form.FormPanel = Ext.extend(Ext.form.FormPanel, {
    /** api: config[protocol]
     *  ``OpenLayers.Protocol`` The protocol instance this form panel
     *  is configured with, actions resulting from this form
     *  will be performed through the protocol.
     */
    protocol: null,

    /** private: method[createForm]
     *  Create the internal :class:`GeoExt.form.BasicForm` instance.
     */
    createForm: function() {
        delete this.initialConfig.listeners;
        return new GeoExt.form.BasicForm(null, this.initialConfig);
    },

    /** api: method[search]
     *  :param options: ``Object`` The options passed to the
     *      :class:`GeoExt.form.SearchAction` constructor.
     *
     *  Shortcut to the internal form's search method.
     */
    search: function(options) {
        this.getForm().search(options);
    }
});

/** api: xtype = gx_formpanel */
Ext.reg("gx_formpanel", GeoExt.form.FormPanel);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/FeatureRenderer.js
 */

 /** api: (define)
  *  module = GeoExt.grid
  *  class = SymbolizerColumn
  *  base_link = `Ext.grid.Column <http://dev.sencha.com/deploy/dev/docs/?class=Ext.grid.Column>`_
  */

Ext.namespace('GeoExt.grid');

/** api: constructor
 *  .. class:: SymbolizerColumn(config)
 *
 *      Grid column for rendering a symbolizer or an array of symbolizers.
 */
GeoExt.grid.SymbolizerColumn = Ext.extend(Ext.grid.Column, {

    /** private: method[renderer]
     */ 
    renderer: function(value, meta) {
        if (value != null) {
            var id = Ext.id();
            window.setTimeout(function() {
                var ct = Ext.get(id);
                // ct for old field may not exist any more during a grid update
                if (ct) {
                    new GeoExt.FeatureRenderer({
                        symbolizers: value instanceof Array ? value : [value],
                        renderTo: ct
                    });
                }
            }, 0);
            meta.css = "gx-grid-symbolizercol";
            return '<div id="' + id + '"></div>';
        }
    }
});

/** api: xtype = gx_symbolizercolumn */
Ext.grid.Column.types.gx_symbolizercolumn = GeoExt.grid.SymbolizerColumn;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt
 *  class = SliderTip
 *  base_link = `Ext.Tip <http://dev.sencha.com/deploy/dev/docs/?class=Ext.slider.Tip>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to create a slider tip to display slider value on hover:
 * 
 *  .. code-block:: javascript
 *     
 *      var slider = new Ext.slider.SingleSlider({
 *          renderTo: document.body,
 *          width: 200,
 *          plugins: new GeoExt.SliderTip()
 *      });
 */

/** api: constructor
 *  .. class:: SliderTip(config)
 *   
 *      Create a slider tip displaying ``Ext.slider.SingleSlider`` values over slider thumbs.
 */
GeoExt.SliderTip = Ext.extend(Ext.slider.Tip, {

    /** api: config[hover]
     *  ``Boolean``
     *  Display the tip when hovering over the thumb.  If ``false``, tip will
     *  only be displayed while dragging.  Default is ``true``.
     */
    hover: true,
    
    /** api: config[minWidth]
     *  ``Number``
     *  Minimum width of the tip.  Default is 10.
     */
    minWidth: 10,

    /** api: config[offsets]
     *  ``Array(Number)``
     *  A two item list that provides x, y offsets for the tip.  Default is
     *  [0, -10].
     */
    offsets : [0, -10],
    
    /** private: property[dragging]
     *  ``Boolean``
     *  The thumb is currently being dragged.
     */
    dragging: false,

    /** private: method[init]
     *  :param slider: ``Ext.slider.SingleSlider``
     *  
     *  Called when the plugin is initialized.
     */
    init: function(slider) {
        GeoExt.SliderTip.superclass.init.apply(this, arguments);
        if (this.hover) {
            slider.on("render", this.registerThumbListeners, this);
        }
        this.slider = slider;
    },

    /** private: method[registerThumbListeners]
     *  Set as a listener for 'render' if hover is true.
     */
    registerThumbListeners: function() {
        var thumb, el;
        for (var i=0, ii=this.slider.thumbs.length; i<ii; ++i) {
            thumb = this.slider.thumbs[i];
            el = thumb.tracker.el;
            (function(thumb, el) {
                el.on({
                    mouseover: function(e) {
                        this.onSlide(this.slider, e, thumb);
                        this.dragging = false;
                    },
                    mouseout: function() {
                        if (!this.dragging) {
                            this.hide.apply(this, arguments);
                        }
                    },
                    scope: this
                });
            }).apply(this, [thumb, el]);
        }
    },

    /** private: method[onSlide]
     *  :param slider: ``Ext.slider.SingleSlider``
     *
     *  Listener for dragstart and drag.
     */
    onSlide: function(slider, e, thumb) {
        this.dragging = true;
        return GeoExt.SliderTip.superclass.onSlide.apply(this, arguments);
    }

});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/widgets/tips/SliderTip.js
 */

/** api: (extends)
 *  GeoExt/widgets/tips/SliderTip.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = LayerOpacitySliderTip
 *  base_link = `Ext.Tip <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Tip>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to create a slider tip to display scale and resolution:
 *
 *  .. code-block:: javascript
 *
 *      var slider = new GeoExt.LayerOpacitySlider({
 *          renderTo: document.body,
 *          width: 200,
 *          layer: layer,
 *          plugins: new GeoExt.LayerOpacitySliderTip({
 *              template: "Opacity: {opacity}%"
 *          })
 *      });
 */

/** api: constructor
 *  .. class:: LayerOpacitySliderTip(config)
 *
 *      Create a slider tip displaying :class:`GeoExt.LayerOpacitySlider` values.
 */
GeoExt.LayerOpacitySliderTip = Ext.extend(GeoExt.SliderTip, {

    /** api: config[template]
     *  ``String``
     *  Template for the tip. Can be customized using the following keywords in
     *  curly braces:
     *
     *  * ``opacity`` - the opacity value in percent.
     */
    template: '<div>{opacity}%</div>',

    /** private: property[compiledTemplate]
     *  ``Ext.Template``
     *  The template compiled from the ``template`` string on init.
     */
    compiledTemplate: null,

    /** private: method[init]
     *  Called to initialize the plugin.
     */
    init: function(slider) {
        this.compiledTemplate = new Ext.Template(this.template);
        GeoExt.LayerOpacitySliderTip.superclass.init.call(this, slider);
    },

    /** private: method[getText]
     *  :param slider: ``Ext.slider.SingleSlider`` The slider this tip is attached to.
     */
    getText: function(thumb) {
        var data = {
            opacity: thumb.value
        };
        return this.compiledTemplate.apply(data);
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/widgets/tips/SliderTip.js
 */

/** api: (extends)
 *  GeoExt/widgets/tips/SliderTip.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = ZoomSliderTip
 *  base_link = `Ext.Tip <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Tip>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to create a slider tip to display scale and resolution:
 * 
 *  .. code-block:: javascript
 *     
 *      var slider = new GeoExt.ZoomSlider({
 *          renderTo: document.body,
 *          width: 200,
 *          map: map,
 *          plugins: new GeoExt.ZoomSliderTip({
 *              template: "Scale: 1 : {scale}<br>Resolution: {resolution}"
 *          })
 *      });
 */

/** api: constructor
 *  .. class:: ZoomSliderTip(config)
 *   
 *      Create a slider tip displaying :class:`GeoExt.ZoomSlider` values.
 */
GeoExt.ZoomSliderTip = Ext.extend(GeoExt.SliderTip, {
    
    /** api: config[template]
     *  ``String``
     *  Template for the tip. Can be customized using the following keywords in
     *  curly braces:
     *  
     *  * ``zoom`` - the zoom level
     *  * ``resolution`` - the resolution
     *  * ``scale`` - the scale denominator
     */
    template: '<div>Zoom Level: {zoom}</div>' +
        '<div>Resolution: {resolution}</div>' +
        '<div>Scale: 1 : {scale}</div>',
    
    /** private: property[compiledTemplate]
     *  ``Ext.Template``
     *  The template compiled from the ``template`` string on init.
     */
    compiledTemplate: null,
    
    /** private: method[init]
     *  Called to initialize the plugin.
     */
    init: function(slider) {
        this.compiledTemplate = new Ext.Template(this.template);
        GeoExt.ZoomSliderTip.superclass.init.call(this, slider);
    },
    
    /** private: method[getText]
     *  :param slider: ``Ext.slider.SingleSlider`` The slider this tip is attached to.
     */
    getText: function(thumb) {
        var data = {
            zoom: thumb.value,
            resolution: this.slider.getResolution(),
            scale: Math.round(this.slider.getScale()) 
        };
        return this.compiledTemplate.apply(data);
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/MapPanel.js
 * @require OpenLayers/Layer.js
 */

Ext.namespace("GeoExt.tree");

/** private: constructor
 *  .. class:: LayerNodeUI
 *
 *      Place in a separate file if this should be documented.
 */
GeoExt.tree.LayerNodeUI = Ext.extend(Ext.tree.TreeNodeUI, {
    
    /** private: method[constructor]
     */
    constructor: function(config) {
        GeoExt.tree.LayerNodeUI.superclass.constructor.apply(this, arguments);
    },
    
    /** private: method[render]
     *  :param bulkRender: ``Boolean``
     */
    render: function(bulkRender) {
        var a = this.node.attributes;
        if (a.checked === undefined) {
            a.checked = this.node.layer.getVisibility();
        }
        /* Ext.tree.treeNodeUI render looks for and handles checked
         * attribute, but not the disabled attribute, so we set it
         * directly on the node object and not the attributes hash*/
        if (a.disabled === undefined && this.node.autoDisable) {
            this.node.disabled = this.node.layer.inRange === false || !this.node.layer.calculateInRange();
        }
        GeoExt.tree.LayerNodeUI.superclass.render.apply(this, arguments);
        var cb = this.checkbox;
        if(a.checkedGroup) {
            // replace the checkbox with a radio button
            var radio = Ext.DomHelper.insertAfter(cb,
                ['<input type="radio" name="', a.checkedGroup,
                '_checkbox" class="', cb.className,
                cb.checked ? '" checked="checked"' : '',
                '"></input>'].join(""));
            radio.defaultChecked = cb.defaultChecked;
            Ext.get(cb).remove();
            this.checkbox = radio;
        }
        this.enforceOneVisible();
    },
    
    /** private: method[onClick]
     *  :param e: ``Object``
     */
    onClick: function(e) {
        if(e.getTarget('.x-tree-node-cb', 1)) {
            this.toggleCheck(this.isChecked());
        } else {
            GeoExt.tree.LayerNodeUI.superclass.onClick.apply(this, arguments);
        }
    },
    
    /** private: method[toggleCheck]
     * :param value: ``Boolean``
     */
    toggleCheck: function(value) {
        value = (value === undefined ? !this.isChecked() : value);
        GeoExt.tree.LayerNodeUI.superclass.toggleCheck.call(this, value);
        
        this.enforceOneVisible();
    },
    
    /** private: method[enforceOneVisible]
     * 
     *  Makes sure that only one layer is visible if checkedGroup is set.
     */
    enforceOneVisible: function() {
        var attributes = this.node.attributes;
        var group = attributes.checkedGroup;
        // If we are in the baselayer group, the map will take care of
        // enforcing visibility.
        if(group && group !== "gx_baselayer") {
            var layer = this.node.layer;
            var checkedNodes = this.node.getOwnerTree().getChecked();
            var checkedCount = 0;
            // enforce "not more than one visible"
            Ext.each(checkedNodes, function(n){
                var l = n.layer;
                if(!n.hidden && n.attributes.checkedGroup === group) {
                    checkedCount++;
                    if(l != layer && attributes.checked) {
                        l.setVisibility(false);
                    }
                }
            });
            // enforce "at least one visible"
            if(checkedCount === 0 && attributes.checked == false) {
                layer.setVisibility(true);
            }
        }
    },
    
    /** private: method[appendDDGhost]
     *  :param ghostNode ``DOMElement``
     *  
     *  For radio buttons, makes sure that we do not use the option group of
     *  the original, otherwise only the original or the clone can be checked 
     */
    appendDDGhost : function(ghostNode){
        var n = this.elNode.cloneNode(true);
        var radio = Ext.DomQuery.select("input[type='radio']", n);
        Ext.each(radio, function(r) {
            r.name = r.name + "_clone";
        });
        ghostNode.appendChild(n);
    }
});


/** api: (define)
 *  module = GeoExt.tree
 *  class = LayerNode
 *  base_link = `Ext.tree.TreeNode <http://dev.sencha.com/deploy/dev/docs/?class=Ext.tree.TreeNode>`_
 */

/** api: constructor
 *  .. class:: LayerNode(config)
 * 
 *      A subclass of ``Ext.tree.TreeNode`` that is connected to an
 *      ``OpenLayers.Layer`` by setting the node's layer property. Checking or
 *      unchecking the checkbox of this node will directly affect the layer and
 *      vice versa. The default iconCls for this node's icon is
 *      "gx-tree-layer-icon", unless it has children.
 * 
 *      Setting the node's layer property to a layer name instead of an object
 *      will also work. As soon as a layer is found, it will be stored as layer
 *      property in the attributes hash.
 * 
 *      The node's text property defaults to the layer name.
 *      
 *      If the node has a checkedGroup attribute configured, it will be
 *      rendered with a radio button instead of the checkbox. The value of
 *      the checkedGroup attribute is a string, identifying the options group
 *      for the node.
 * 
 *      To use this node type in a ``TreePanel`` config, set ``nodeType`` to
 *      "gx_layer".
 */
GeoExt.tree.LayerNode = Ext.extend(Ext.tree.AsyncTreeNode, {
    
    /** api: config[layer]
     *  ``OpenLayers.Layer or String``
     *  The layer that this layer node will
     *  be bound to, or the name of the layer (has to match the layer's
     *  name property). If a layer name is provided, ``layerStore`` also has
     *  to be provided.
     */

    /** api: property[layer]
     *  ``OpenLayers.Layer``
     *  The layer this node is bound to.
     */
    layer: null,
    
    /** api: property[autoDisable]
     *  ``Boolean``
     *  Should this node automattically disable itself when the layer
     *  is out of range and enable itself when the layer is in range.
     *  Defaults to true, unless ``layer`` has ``isBaseLayer``==true
     *  or ``alwaysInRange``==true.
     */
    autoDisable: null,
    
    /** api: config[layerStore]
     *  :class:`GeoExt.data.LayerStore` ``or "auto"``
     *  The layer store containing the layer that this node represents.  If set
     *  to "auto", the node will query the ComponentManager for a
     *  :class:`GeoExt.MapPanel`, take the first one it finds and take its layer
     *  store. This property is only required if ``layer`` is provided as a
     *  string.
     */
    layerStore: null,
    
    /** api: config[checkedGroup]
     *  ``String`` If provided, nodes will be rendered with a radio button
     *  instead of a checkbox. All layers represented by nodes with the same
     *  checkedGroup are considered mutually exclusive - only one can be
     *  visible at a time.
     */
    
    /** api: config[loader]
     *  ``Ext.tree.TreeLoader|Object`` If provided, subnodes will be added to
     *  this LayerNode. Obviously, only loaders that process an
     *  ``OpenLayers.Layer`` or :class:`GeoExt.data.LayerRecord` (like
     *  :class:`GeoExt.tree.LayerParamsLoader`) will actually generate child
     *  nodes here. If provided as ``Object``, a
     *  :class:`GeoExt.tree.LayerParamLoader` instance will be created, with
     *  the provided object as configuration.
     */
    
    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(config) {
        config.leaf = config.leaf || !(config.children || config.loader);
        
        if(!config.iconCls && !config.children) {
            config.iconCls = "gx-tree-layer-icon";
        }
        if(config.loader && !(config.loader instanceof Ext.tree.TreeLoader)) {
            config.loader = new GeoExt.tree.LayerParamLoader(config.loader);
        }
        
        this.defaultUI = this.defaultUI || GeoExt.tree.LayerNodeUI;
        
        Ext.apply(this, {
            layer: config.layer,
            layerStore: config.layerStore,
            autoDisable: config.autoDisable
        });
        if (config.text) {
            this.fixedText = true;
        }
        GeoExt.tree.LayerNode.superclass.constructor.apply(this, arguments);
    },

    /** private: method[render]
     *  :param bulkRender: ``Boolean``
     */
    render: function(bulkRender) {
        var layer = this.layer instanceof OpenLayers.Layer && this.layer;
        if(!layer) {
            // guess the store if not provided
            if(!this.layerStore || this.layerStore == "auto") {
                this.layerStore = GeoExt.MapPanel.guess().layers;
            }
            // now we try to find the layer by its name in the layer store
            var i = this.layerStore.findBy(function(o) {
                return o.get("title") == this.layer;
            }, this);
            if(i != -1) {
                // if we found the layer, we can assign it and everything
                // will be fine
                layer = this.layerStore.getAt(i).getLayer();
            }
        }
        if (!this.rendered || !layer) {
            var ui = this.getUI();
            
            if(layer) {
                this.layer = layer;
                // no DD and radio buttons for base layers
                if(layer.isBaseLayer) {
                    this.draggable = false;
                    Ext.applyIf(this.attributes, {
                        checkedGroup: "gx_baselayer"
                    });
                }
                
                //base layers & alwaysInRange layers should never be auto-disabled
                this.autoDisable = !(this.autoDisable===false || this.layer.isBaseLayer || this.layer.alwaysInRange);
                
                if(!this.text) {
                    this.text = layer.name;
                }
                
                ui.show();
                this.addVisibilityEventHandlers();
            } else {
                ui.hide();
            }
            
            if(this.layerStore instanceof GeoExt.data.LayerStore) {
                this.addStoreEventHandlers(layer);
            }            
        }
        GeoExt.tree.LayerNode.superclass.render.apply(this, arguments);
    },
    
    /** private: method[addVisibilityHandlers]
     *  Adds handlers that sync the checkbox state with the layer's visibility
     *  state
     */
    addVisibilityEventHandlers: function() {
        this.layer.events.on({
            "visibilitychanged": this.onLayerVisibilityChanged,
            scope: this
        }); 
        this.on({
            "checkchange": this.onCheckChange,
            scope: this
        });
        if(this.autoDisable){
            if (this.layer.map) {
                this.layer.map.events.register("moveend", this, this.onMapMoveend);
            } else {
                this.layer.events.register("added", this, function added() {
                    this.layer.events.unregister("added", this, added);
                    this.layer.map.events.register("moveend", this, this.onMapMoveend);
                });
            }
        }
    },
    
    /** private: method[onLayerVisiilityChanged
     *  handler for visibilitychanged events on the layer
     */
    onLayerVisibilityChanged: function() {
        if(!this._visibilityChanging) {
            this.getUI().toggleCheck(this.layer.getVisibility());
        }
    },
    
    /** private: method[onCheckChange]
     *  :param node: ``GeoExt.tree.LayerNode``
     *  :param checked: ``Boolean``
     *
     *  handler for checkchange events 
     */
    onCheckChange: function(node, checked) {
        if(checked != this.layer.getVisibility()) {
            this._visibilityChanging = true;
            var layer = this.layer;
            if(checked && layer.isBaseLayer && layer.map) {
                layer.map.setBaseLayer(layer);
            } else {
                layer.setVisibility(checked);
            }
            delete this._visibilityChanging;
        }
    },
    
    /** private: method[onMapMoveend]
     *  :param evt: ``OpenLayers.Event``
     *
     *  handler for map moveend events to determine if node should be
     *  disabled or enabled 
     */
    onMapMoveend: function(evt){
        /* scoped to node */
        if (this.autoDisable) {
            if (this.layer.inRange === false) {
                this.disable();
            }
            else {
                this.enable();
            }
        }
    },
    
    /** private: method[addStoreEventHandlers]
     *  Adds handlers that make sure the node disappeares when the layer is
     *  removed from the store, and appears when it is re-added.
     */
    addStoreEventHandlers: function() {
        this.layerStore.on({
            "add": this.onStoreAdd,
            "remove": this.onStoreRemove,
            "update": this.onStoreUpdate,
            scope: this
        });
    },
    
    /** private: method[onStoreAdd]
     *  :param store: ``Ext.data.Store``
     *  :param records: ``Array(Ext.data.Record)``
     *  :param index: ``Number``
     *
     *  handler for add events on the store 
     */
    onStoreAdd: function(store, records, index) {
        var l;
        for(var i=0; i<records.length; ++i) {
            l = records[i].getLayer();
            if(this.layer == l) {
                this.getUI().show();
                break;
            } else if (this.layer == l.name) {
                // layer is a string, which means the node has not yet
                // been rendered because the layer was not found. But
                // now we have the layer and can render.
                this.render();
                break;
            }
        }
    },
    
    /** private: method[onStoreRemove]
     *  :param store: ``Ext.data.Store``
     *  :param record: ``Ext.data.Record``
     *  :param index: ``Number``
     *
     *  handler for remove events on the store 
     */
    onStoreRemove: function(store, record, index) {
        if(this.layer == record.getLayer()) {
            this.getUI().hide();
        }
    },

    /** private: method[onStoreUpdate]
     *  :param store: ``Ext.data.Store``
     *  :param record: ``Ext.data.Record``
     *  :param operation: ``String``
     *  
     *  Listener for the store's update event.
     */
    onStoreUpdate: function(store, record, operation) {
        var layer = record.getLayer();
        if(!this.fixedText && (this.layer == layer && this.text !== layer.name)) {
            this.setText(layer.name);
        }
    },

    /** private: method[destroy]
     */
    destroy: function() {
        var layer = this.layer;
        if (layer instanceof OpenLayers.Layer) {
            if (layer.map) {
                layer.map.events.unregister("moveend", this, this.onMapMoveend);
            }
            layer.events.un({
                "visibilitychanged": this.onLayerVisibilityChanged,
                scope: this
            });
        }
        delete this.layer;
        var layerStore = this.layerStore;
        if(layerStore) {
            layerStore.un("add", this.onStoreAdd, this);
            layerStore.un("remove", this.onStoreRemove, this);
            layerStore.un("update", this.onStoreUpdate, this);
        }
        delete this.layerStore;
        this.un("checkchange", this.onCheckChange, this);

        GeoExt.tree.LayerNode.superclass.destroy.apply(this, arguments);
    }
});

/**
 * NodeType: gx_layer
 */
Ext.tree.TreePanel.nodeTypes.gx_layer = GeoExt.tree.LayerNode;

/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

Ext.namespace("GeoExt.tree");

/** api: (define)
 *  module = GeoExt.tree
 *  class = TreeNodeUIEventMixin
 */

/** api: constructor
 *  A mixin that adds events to TreeNodeUIs. With these events, tree plugins
 *  can modify the node ui's DOM when it is rendered, and react to raw click
 *  events on tree nodes.
 */

 /** api: example
  *  Sample code to create a tree with a node that uses the
  *  :class:`GeoExt.tree.TreeNodeUIEventMixin`:
  *
  *  .. code-block:: javascript
  *
  *      var UIClass = Ext.extend(
  *          Ext.tree.TreeNodeUI,
  *          GeoExt.tree.TreeNodeUIEventMixin()
  *      );
  *      var tree = new Ext.tree.TreePanel({
  *          root: {
  *              nodeType: "node",
  *              uiProvider: UIClass,
  *              text: "My Node"
  *          }
  *      }
  */

GeoExt.tree.TreeNodeUIEventMixin = function(){
    return {
        
        constructor: function(node) {
            
            node.addEvents(

                /** api: event[rendernode]
                 *  Fires on the tree when a node is rendered.
                 *
                 *  Listener arguments:
                 *  
                 *  * node - ``Ext.TreeNode`` The rendered node.
                 */
                "rendernode",

                /** api: event[rawclicknode]
                 *  Fires on the tree when a node is clicked.
                 *
                 *  Listener arguments:
                 *  
                 *  * node - ``Ext.TreeNode`` The clicked node.
                 *  * event - ``Ext.EventObject`` The click event.
                 */
                "rawclicknode"
            );
            this.superclass = arguments.callee.superclass;
            this.superclass.constructor.apply(this, arguments);
            
        },
        
        /** private: method[render]
         *  :param bulkRender: ``Boolean``
         */
        render: function(bulkRender) {
            if(!this.rendered) {
                this.superclass.render.apply(this, arguments);
                this.fireEvent("rendernode", this.node);
            }
        },
        
        /** private: method[onClick]
         *  :param e: ``Ext.EventObject``
         */
        onClick: function(e) {
            if(this.fireEvent("rawclicknode", this.node, e) !== false) {
                this.superclass.onClick.apply(this, arguments);
            }
        }
    };
};

/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

Ext.namespace("GeoExt.plugins");

/** api: (define)
 *  module = GeoExt.plugins
 *  class = TreeNodeComponent
 */

/** api: constructor
 *  A plugin to create tree node UIs that can have an Ext.Component below the
 *  node's title. Can be plugged into any ``Ext.tree.TreePanel`` and will be
 *  applied to nodes that are extended with the
 *  :class:`GeoExt.Tree.TreeNodeUIEventMixin`.
 *
 *  If a node is configured with a ``component`` attribute, it will be rendered
 *  with the component in addition to icon and title.
 */

/** api: example
 *  Sample code to create a tree with a node that has a component:
 *
 *  .. code-block:: javascript
 *
 *      var uiClass = Ext.extend(
 *          Ext.tree.TreeNodeUI,
 *          GeoExt.tree.TreeNodeUIEventMixin()
 *      );
 *      var tree = new Ext.tree.TreePanel({
 *          plugins: [
 *              new GeoExt.plugins.TreeNodeComponent(),
 *          ],
 *          root: {
 *              nodeType: "node",
 *              uiProvider: uiClass,
 *              text: "My Node",
 *              component: {
 *                  xtype: "box",
 *                  autoEl: {
 *                      tag: "img",
 *                      src: "/images/my-image.jpg"
 *                  }
 *              }
 *          }
 *      }
 */

GeoExt.plugins.TreeNodeComponent = Ext.extend(Ext.util.Observable, {
    
    /** private: method[constructor]
     *  :param config: ``Object``
     */
    constructor: function(config) {
        Ext.apply(this.initialConfig, Ext.apply({}, config));
        Ext.apply(this, config);

        GeoExt.plugins.TreeNodeComponent.superclass.constructor.apply(this, arguments);
    },

    /** private: method[init]
     *  :param tree: ``Ext.tree.TreePanel`` The tree.
     */
    init: function(tree) {
        tree.on({
            "rendernode": this.onRenderNode,
            "beforedestroy": this.onBeforeDestroy,
            scope: this
        });
    },
    
    /** private: method[onRenderNode]
     *  :param node: ``Ext.tree.TreeNode``
     */
    onRenderNode: function(node) {
        var rendered = node.rendered;
        var attr = node.attributes;
        var component = attr.component || this.component;
        if(!rendered && component) {
            var elt = Ext.DomHelper.append(node.ui.elNode, [
                {"tag": "div"}
            ]);
            if(typeof component == "function") {
                component = component(node, elt);
            } else if (typeof component == "object" &&
                       typeof component.fn == "function") {
                component = component.fn.apply(
                    component.scope, [node, elt]
                );
            }
            if(typeof component == "object" &&
               typeof component.xtype == "string") {
                component = Ext.ComponentMgr.create(component);
            }
            if(component instanceof Ext.Component) {
                component.render(elt);
                node.component = component;
            }
        }
    },
    
    /** private: method[onBeforeDestroy]
     */
    onBeforeDestroy: function(tree) {
        tree.un("rendernode", this.onRenderNode, this);
        tree.un("beforedestroy", this.onBeforeDestroy, this);
    }

});

/** api: ptype = gx_treenodecomponent */
Ext.preg("gx_treenodecomponent", GeoExt.plugins.TreeNodeComponent);

/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

 /**
  * @include GeoExt/widgets/tree/TreeNodeUIEventMixin.js
  */
Ext.namespace("GeoExt.plugins");

/** api: (define)
 *  module = GeoExt.plugins
 *  class = TreeNodeRadioButton
 */

/** api: constructor
 *  A plugin to create tree node UIs with radio buttons. Can be plugged into
 *  any ``Ext.tree.TreePanel`` and will be applied to nodes that are extended
 *  with the :class:`GeoExt.Tree.TreeNodeUIEventMixin`, in particular
 *  :class:`GeoExt.tree.LayerNodeUI` nodes.
 *
 *  A tree with a ``GeoExt.plugins.TreeNodeRadioButton`` fires the additional
 *  ``radiochange`` event when a node's radio button is clicked.
 *
 *  Only if a node is configured ``radioGroup`` attribute, it will be rendered
 *  with a radio button next to its icon. The ``radioGroup`` works like a
 *  HTML checkbox with its ``name`` attribute, and ``radioGroup`` is a string
 *  that identifies the options group.
 * 
 */

/** api: example
 *  Sample code to create a tree with a node that has a radio button:
 *
 *  .. code-block:: javascript
 *
 *      var UIClass = Ext.extend(
 *          Ext.tree.TreeNodeUI,
 *          GeoExt.tree.TreeNodeUIEventMixin
 *      );
 *      var tree = new Ext.tree.TreePanel({
 *          plugins: [
 *              new GeoExt.plugins.TreeNodeRadioButton({
 *                  listeners: {
 *                      "radiochange": function(node) {
 *                          alert(node.text + "'s radio button was clicked.");
 *                      }
 *                  }
 *              })
 *          ],
 *          root: {
 *              nodeType: "node",
 *              uiProvider: UIClass,
 *              text: "My Node",
 *              radioGroup: "myGroupId"
 *          }
 *      }
 */

GeoExt.plugins.TreeNodeRadioButton = Ext.extend(Ext.util.Observable, {
    
    /** private: method[constructor]
     *  :param config: ``Object``
     */
    constructor: function(config) {
        Ext.apply(this.initialConfig, Ext.apply({}, config));
        Ext.apply(this, config);

        this.addEvents(

            /** api: event[radiochange]
             *  Fires when a radio button is clicked.
             *
             *  Listener arguments:
             *  
             *  * node - ``Ext.TreeNode`` The node of the clicked radio button.
             */
            "radiochange"
        );

        GeoExt.plugins.TreeNodeRadioButton.superclass.constructor.apply(this, arguments);
    },

    /** private: method[init]
     *  :param tree: ``Ext.tree.TreePanel`` The tree.
     */
    init: function(tree) {
        tree.on({
            "rendernode": this.onRenderNode,
            "rawclicknode": this.onRawClickNode,
            "disabledchange": this.onDisabledChange,
            "beforedestroy": this.onBeforeDestroy,
            scope: this
        });
    },
    
    /** private: method[onRenderNode]
     *  :param node: ``Ext.tree.TreeNode``
     */
    onRenderNode: function(node) {
        var a = node.attributes;
        if(a.radioGroup && !a.radio) {
            a.radio = Ext.DomHelper.insertBefore(node.ui.anchor,
                ['<input type="radio" class="gx-tree-radio" name="',
                a.radioGroup, '_radio"></input>'].join(""));
        }
    },
    
    /** private: method[onRawClickNode]
     *  :param node: ``Ext.tree.TreeNode``
     *  :param e: ``Ext.EventObject``
     */
    onRawClickNode: function(node, e) {
        var el = e.getTarget('.gx-tree-radio', 1); 
        if(el) {
            el.defaultChecked = el.checked;
            this.fireEvent("radiochange", node);
            return false;
        }
    },
    
    /** private: method[onDisabledChange]
     * :param node: ``Ext.tree.TreeNode``
     * :param disabled: ``Boolean``
     */
    onDisabledChange: function(node, disabled) {
        var radio = node.attributes.radio;
        if (radio) {
            radio.disabled = disabled;
        }
    },
    
    /** private: method[onBeforeDestroy]
     */
    onBeforeDestroy: function(tree) {
        tree.un("rendernode", this.onRenderNode, this);
        tree.un("rawclicknode", this.onRawClickNode, this);
        tree.un("beforedestroy", this.onBeforeDestroy, this);
    }

});

/** api: ptype = gx_treenoderadiobutton */
Ext.preg("gx_treenoderadiobutton", GeoExt.plugins.TreeNodeRadioButton);

/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

Ext.namespace("GeoExt.plugins");

/** api: (define)
 *  module = GeoExt.plugins
 *  class = TreeNodeActions
 */

/** api: constructor
 *  A plugin to create tree node UIs with actions.
 *
 *  An action is a clickable image in a tree node, which, when clicked,
 *  leads to an "action" event being triggered by the node.
 *
 *  To set actions in a node an ``actions`` property must be provided in
 *  the node config options. This property  is an array of 
 *  action objects, each action object has the following property:
 *
 *  * "action" ``String`` the name of the action. It is used as
 *    the name of the ``<img>`` class. The ``img`` tag being placed in a
 *    div whose class is "gx-tree-layer-actions" a CSS selector for the
 *    action is ``.gx-tree-layer-actions .action-name``. The name of the
 *    action is also provided in the "action" event for listeners to know
 *    which action got clicked. (required)
 *  * "qtip" ``String`` the tooltip displayed when the action
 *    image is hovered. (required)
 *  * "update" ``Function`` a function executed after the action is
 *    rendered in the node, it receives the ``Ext.Element`` object
 *    representing the image and executes with the node as its
 *    scope. For example, this function can be used to hide the
 *    action based on some condition. (optional)
 */

/** api: example
 *  Sample code to create a layer node UI with an actions plugin:
 *
 *  .. code-block:: javascript
 *
 *      var uiClass = GeoExt.examples.LayerNodeUI = Ext.extend(
 *         GeoExt.tree.LayerNodeUI,
 *         new GeoExt.tree.TreeNodeUIEventMixin()
 *      );
 *
 *      // this function takes action based on the "action"
 *      // parameter, it is used as a listener to layer
 *      // nodes' "action" events
 *      function onAction(node, action, evt) {
 *          var layer = node.layer;
 *          switch(action) {
 *          case "delete":
 *              layer.destroy();
 *              break;
 *          }
 *      };
 *
 *      var tree = new Ext.tree.TreePanel({
 *          region: "west",
 *          width: 250,
 *          title: "Layer Tree",
 *          loader: {
 *              applyLoader: false,
 *              uiProviders: {
 *                  "ui": GeoExt.examples.LayerNodeUI
 *              }
 *          },
 *          // apply the tree node actions plugin to layer nodes
 *          plugins: [{
 *              ptype: "gx_treenodeactions",
 *              listeners: {
 *                  action: onAction
 *              }
 *          }],
 *          root: {
 *              nodeType: "gx_layercontainer",
 *              loader: {
 *                  baseAttrs: {
 *                      radioGroup: "radiogroup",
 *                      uiProvider: "ui",
 *                      actions: [{
 *                          action: "delete",
 *                          qtip: "delete"
 *                      }]
 *                  }
 *              }
 *          },
 *          rootVisible: false
 *      });
 */

GeoExt.plugins.TreeNodeActions = Ext.extend(Ext.util.Observable, { 
    /** private: constant[actionsCls]
     */
    actionsCls: "gx-tree-layer-actions",
 
    /** private: constant[actionCls]
     */
    actionCls: "gx-tree-layer-action",

    /** private: method[constructor]
     *  :param config: ``Object``
     */
    constructor: function(config) {
        Ext.apply(this.initialConfig, Ext.apply({}, config));
        Ext.apply(this, config);

        this.addEvents(

            /** api: event[radiochange]
             *  Fires when an action image is clicked.
             *
             *  Listener arguments:
             *  
             *  * node - ``Ext.TreeNode`` The node of the clicked action image.
             */
            "action"
        );

        GeoExt.plugins.TreeNodeActions.superclass.constructor.apply(this, arguments);
    },

    /** private: method[init]
     *  :param tree: ``Ext.tree.TreePanel`` The tree.
     */
    init: function(tree) {
        tree.on({
            "rendernode": this.onRenderNode,
            "beforedestroy": this.onBeforeDestroy,
            scope: this
        });
    },

    /** private: method[onRenderNode]
     *  :param node: ``Ext.tree.TreeNode``
     */
    onRenderNode: function(node) {
        var rendered = node.rendered;
        if(!rendered) {
            var attr = node.attributes;
            var actions = attr.actions || this.actions;
            if(actions && actions.length > 0) {
                var html = '<div class="' + this.actionsCls + '"></div>';
                var div = Ext.DomHelper.insertFirst(node.ui.elNode, html);
                for(var i=0,len=actions.length; i<len; i++) {
                    var a = actions[i],
                        action = a.action;
                    var actionEl = Ext.get(Ext.DomHelper.append(div,
                        this.createActionMarkup(node, a)));
                    actionEl.on({
                        'click': (function(e, target, o, node, action) {
                            this.fireEvent("action", node, action, e);
                        }).createDelegate(this, [node, action], true)
                    });
                }
            }
            if (node.layer && node.layer.map) {
                this.updateActions(node);
            } else if (node.layerStore) {
                node.layerStore.on({
                    'bind': function() {
                        this.updateActions(node);
                    },
                    scope: this
                });
            }
        }
    },

    /** private: method[createActionMarkup]
     *  :param node: ``Ext.tree.TreeNode``
     *  :param a: ``Object``
     *  :returns: ``String``
     */
    createActionMarkup: function(node, a) {
        return '<img id="'+node.id+'_'+a.action +
            '" ext:qtip="'+a.qtip + '" src="'+Ext.BLANK_IMAGE_URL +
            '" class="'+this.actionCls+' '+a.action+'" />';
    },

    /** private: method[updateActions]
     *
     *  Update all the actions.
     */
    updateActions: function(node) {
        var actions = node.attributes.actions || this.actions || [];
        Ext.each(actions, function(a, index) {
            var el = Ext.get(node.id + '_' + a.action);
            if (el && typeof a.update == "function") {
                a.update.call(node, el);
            }
        });
    },
    
    /** private: method[onBeforeDestroy]
     */
    onBeforeDestroy: function(tree) {
        tree.un("rendernode", this.onRenderNode, this);
        tree.un("beforedestroy", this.onBeforeDestroy, this);
    }
});

/** api: ptype = gx_treenodeactions */
Ext.preg("gx_treenodeactions", GeoExt.plugins.TreeNodeActions);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/MapPanel.js
 * @include GeoExt/widgets/tree/LayerNode.js
 * @include GeoExt/widgets/tree/LayerContainer.js
 */
Ext.namespace("GeoExt.tree");

/** api: (define)
 *  module = GeoExt.tree
 *  class = LayerLoader
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */

/** api: constructor
 *  .. class:: LayerLoader
 * 
 *      A loader that will load all layers of a :class:`GeoExt.data.LayerStore`
 *      By default, only layers that have displayInLayerSwitcher set to true
 *      will be included. The childrens' iconCls defaults to
 *      "gx-tree-layer-icon".
 */
GeoExt.tree.LayerLoader = function(config) {
    Ext.apply(this, config);
    this.addEvents(
    
        /** api: event[beforeload]
         *  Triggered before loading children. Return false to avoid
         *  loading children.
         *  
         *  Listener arguments:
         *  
         *  * loader - :class:`GeoExt.tree.LayerLoader` this loader
         *  * node - ``Ex.tree.TreeNode`` the node that this loader is
         *      configured with
         */
        "beforeload",
        
        /** api: event[load]
         *  Triggered after children wer loaded.
         *  
         *  Listener arguments:
         *  
         *  * loader - :class:`GeoExt.tree.LayerLoader` this loader
         *  * node - ``Ex.tree.TreeNode`` the node that this loader is
         *      configured with
         */
        "load"
    );
    
    this.iconCls = {};

    GeoExt.tree.LayerLoader.superclass.constructor.call(this);
};

Ext.extend(GeoExt.tree.LayerLoader, Ext.util.Observable, {

    /** api: config[store]
     *  :class:`GeoExt.data.LayerStore`
     *  The layer store containing layers to be added by this loader.
     */
    store: null,
    
    /** api: config[filter]
     *  ``Function``
     *  A function, called in the scope of this loader, with a layer record
     *  as argument. Is expected to return true for layers to be loaded, false
     *  otherwise. By default, the filter checks for displayInLayerSwitcher:
     *  
     *  .. code-block:: javascript
     *  
     *      filter: function(record) {
     *          return record.getLayer().displayInLayerSwitcher == true
     *      }
     */
    filter: function(record) {
        return record.getLayer().displayInLayerSwitcher == true;
    },
    
    /** api: config[baseAttrs]
     *  An object containing attributes to be added to all nodes created by
     *  this loader.
     */
    baseAttrs: null,
    
    /** api: config[uiProviders]
     *  ``Object``
     *  An optional object containing properties which specify custom
     *  GeoExt.tree.LayerNodeUI implementations. If the optional uiProvider
     *  attribute for child nodes is a string rather than a reference to a
     *  TreeNodeUI implementation, then that string value is used as a
     *  property name in the uiProviders object. If not provided, the
     *  uiProviders object will be taken from the ownerTree's loader.
     */
    uiProviders: null,

    /** private: property[iconCls]
     *  ``Object`` An object where the keys are the record ids and the
     *  values are the iconCls values of the corresponding nodes. This is used
     *  to restore the iconCls of a node after move whenever possible. It is 
     *  needed since moving a layer node involves removing it and re-adding it.
     */
    
    /** private: method[load]
     *  :param node: ``Ext.tree.TreeNode`` The node to add children to.
     *  :param callback: ``Function``
     */
    load: function(node, callback) {
        if(this.fireEvent("beforeload", this, node)) {
            this.removeStoreHandlers();
            while (node.firstChild) {
                node.removeChild(node.firstChild);
            }
            
            if(!this.uiProviders) {
                this.uiProviders = node.getOwnerTree().getLoader().uiProviders;
            }
    
            if(!this.store) {
                this.store = GeoExt.MapPanel.guess().layers;
            }
            this.store.each(function(record) {
                this.addLayerNode(node, record);
            }, this);
            this.addStoreHandlers(node);
    
            if(typeof callback == "function"){
                callback();
            }
            
            this.fireEvent("load", this, node);
        }
    },
    
    /** private: method[onStoreAdd]
     *  :param store: ``Ext.data.Store``
     *  :param records: ``Array(Ext.data.Record)``
     *  :param index: ``Number``
     *  :param node: ``Ext.tree.TreeNode``
     *  
     *  Listener for the store's add event.
     */
    onStoreAdd: function(store, records, index, node) {
        if(!this._reordering) {
            var nodeIndex = node.recordIndexToNodeIndex(index+records.length-1);
            for(var i=0; i<records.length; ++i) {
                this.addLayerNode(node, records[i], nodeIndex);
            }
        }
    },
    
    /** private: method[onStoreRemove]
     *  :param store: ``Ext.data.Store``
     *  :param record: ``Ext.data.Record``
     *  :param index: ``Number``
     *  :param node: ``Ext.tree.TreeNode``
     *  
     *  Listener for the store's remove event.
     */
    onStoreRemove: function(store, record, index, node) {
        if(!this._reordering) {
            this.removeLayerNode(node, record);
        }
    },

    /** private: method[addLayerNode]
     *  :param node: ``Ext.tree.TreeNode`` The node that the layer node will
     *      be added to as child.
     *  :param layerRecord: ``Ext.data.Record`` The layer record containing the
     *      layer to be added.
     *  :param index: ``Number`` Optional index for the new layer.  Default is 0.
     *  
     *  Adds a child node representing a layer of the map
     */
    addLayerNode: function(node, layerRecord, index) {
        index = index || 0;
        if (this.filter(layerRecord) === true) {
            var child = this.createNode({
                nodeType: 'gx_layer',
                layer: layerRecord.getLayer(),
                layerStore: this.store,
                iconCls: this.iconCls[layerRecord.id]
            });
            var sibling = node.item(index);
            if(sibling) {
                node.insertBefore(child, sibling);
            } else {
                node.appendChild(child);
            }
            child.on("move", this.onChildMove, this);
        }
    },

    /** private: method[removeLayerNode]
     *  :param node: ``Ext.tree.TreeNode`` The node that the layer node will
     *      be removed from as child.
     *  :param layerRecord: ``Ext.data.Record`` The layer record containing the
     *      layer to be removed.
     * 
     *  Removes a child node representing a layer of the map
     */
    removeLayerNode: function(node, layerRecord) {
        if (this.filter(layerRecord) === true) {
            var child = node.findChildBy(function(node) {
                return node.layer == layerRecord.getLayer();
            });
            if(child) {
                child.un("move", this.onChildMove, this);
                child.remove();
                node.reload();
            }
        }
    },
    
    /** private: method[onChildMove]
     *  :param tree: ``Ext.data.Tree``
     *  :param node: ``Ext.tree.TreeNode``
     *  :param oldParent: ``Ext.tree.TreeNode``
     *  :param newParent: ``Ext.tree.TreeNode``
     *  :param index: ``Number``
     *  
     *  Listener for child node "move" events.  This updates the order of
     *  records in the store based on new node order if the node has not
     *  changed parents.
     */
    onChildMove: function(tree, node, oldParent, newParent, index) {
        this._reordering = true;
        // remove the record and re-insert it at the correct index
        var record = this.store.getByLayer(node.layer);

        delete oldParent.loader.iconCls[record.id];
        if(newParent instanceof GeoExt.tree.LayerContainer && 
                                    this.store === newParent.loader.store) {
            newParent.loader._reordering = true;
            newParent.loader.iconCls[record.id] = node.attributes.iconCls;
            this.store.remove(record);
            var newRecordIndex;
            if(newParent.childNodes.length > 1) {
                // find index by neighboring node in the same container
                var searchIndex = (index === 0) ? index + 1 : index - 1;
                newRecordIndex = this.store.findBy(function(r) {
                    return newParent.childNodes[searchIndex].layer === r.getLayer();
                });
                index === 0 && newRecordIndex++;
            } else if(oldParent.parentNode === newParent.parentNode){
                // find index by last node of a container above
                var prev = newParent;
                do {
                    prev = prev.previousSibling;
                } while (prev && !(prev instanceof GeoExt.tree.LayerContainer && prev.lastChild));
                if(prev) {
                    newRecordIndex = this.store.findBy(function(r) {
                        return prev.lastChild.layer === r.getLayer();
                    });
                } else {
                    // find indext by first node of a container below
                    var next = newParent;
                    do {
                        next = next.nextSibling;
                    } while (next && !(next instanceof GeoExt.tree.LayerContainer && next.firstChild));
                    if(next) {
                        newRecordIndex = this.store.findBy(function(r) {
                            return next.firstChild.layer === r.getLayer();
                        });
                    }
                    newRecordIndex++;
                }
            }
            if(newRecordIndex !== undefined) {
                this.store.insert(newRecordIndex, [record]);
                window.setTimeout(function() {
                    newParent.reload();
                    oldParent.reload();
                });
            } else {
                this.store.insert(oldRecordIndex, [record]);
            }
            delete newParent.loader._reordering;
        }
        delete this._reordering;
    },
    
    /** private: method[addStoreHandlers]
     *  :param node: :class:`GeoExt.tree.LayerNode`
     */
    addStoreHandlers: function(node) {
        if(!this._storeHandlers) {
            this._storeHandlers = {
                "add": this.onStoreAdd.createDelegate(this, [node], true),
                "remove": this.onStoreRemove.createDelegate(this, [node], true)
            };
            for(var evt in this._storeHandlers) {
                this.store.on(evt, this._storeHandlers[evt], this);
            }
        }
    },
    
    /** private: method[removeStoreHandlers]
     */
    removeStoreHandlers: function() {
        if(this._storeHandlers) {
            for(var evt in this._storeHandlers) {
                this.store.un(evt, this._storeHandlers[evt], this);
            }
            delete this._storeHandlers;
        }
    },

    /** api: method[createNode]
     *  :param attr: ``Object`` attributes for the new node
     *
     *  Override this function for custom TreeNode node implementation, or to
     *  modify the attributes at creation time.
     */
    createNode: function(attr) {
        if(this.baseAttrs){
            Ext.apply(attr, this.baseAttrs);
        }
        if(typeof attr.uiProvider == 'string'){
           attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        attr.nodeType = attr.nodeType || "gx_layer";

        return new Ext.tree.TreePanel.nodeTypes[attr.nodeType](attr);
    },

    /** private: method[destroy]
     */
    destroy: function() {
        this.removeStoreHandlers();
        this.iconCls = null;
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/tree/LayerLoader.js
 */
Ext.namespace("GeoExt.tree");

/** api: (define)
 *  module = GeoExt.tree
 *  class = LayerContainer
 *  base_link = `Ext.tree.AsyncTreeNode <http://dev.sencha.com/deploy/dev/docs/?class=Ext.tree.AsyncTreeNode>`_
 */

/** api: constructor
 *  .. class:: LayerContainer
 * 
 *      A subclass of ``Ext.tree.AsyncTreeNode`` that will collect all layers of an
 *      OpenLayers map. Only layers that have displayInLayerSwitcher set to true
 *      will be included. The childrens' iconCls defaults to
 *      "gx-tree-layer-icon" and this node' text defaults to "Layers".
 *      
 *      Note: if this container is loaded by an ``Ext.tree.TreeLoader``, the
 *      ``applyLoader`` config option of that loader needs to be set to
 *      "false". Also note that the list of available uiProviders will be
 *      taken from the ownerTree if this container's loader is configured
 *      without one.
 * 
 *      To use this node type in ``TreePanel`` config, set nodeType to
 *      "gx_layercontainer".
 */
GeoExt.tree.LayerContainer = Ext.extend(Ext.tree.AsyncTreeNode, {
    
    /** api: config[loader]
     *  :class:`GeoExt.tree.LayerLoader` or ``Object`` The loader to use with
     *  this container. If an ``Object`` is provided, a
     *  :class:`GeoExt.tree.LayerLoader`, configured with the the properties
     *  from the provided object, will be created. 
     */
    
    /** api: config[layerStore]
     *  :class:`GeoExt.data.LayerStore` The layer store containing layers to be
     *  displayed in the container. If loader is not provided or provided as
     *  ``Object``, this property will be set as the store option of the
     *  loader. Otherwise it will be ignored.
     */

    /** private: property[text]
     *  ``String`` The text for this node.
     */
    text: 'Layers',
    
    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(config) {
        config = Ext.applyIf(config || {}, {
            text: this.text
        });
        this.loader = config.loader instanceof GeoExt.tree.LayerLoader ?
            config.loader :
            new GeoExt.tree.LayerLoader(Ext.applyIf(config.loader || {}, {
                store: config.layerStore
            }));
        
        GeoExt.tree.LayerContainer.superclass.constructor.call(this, config);
    },
    
    /** private: method[recordIndexToNodeIndex]
     *  :param index: ``Number`` The record index in the layer store.
     *  :return: ``Number`` The appropriate child node index for the record.
     */
    recordIndexToNodeIndex: function(index) {
        var store = this.loader.store;
        var count = store.getCount();
        var nodeCount = this.childNodes.length;
        var nodeIndex = -1;
        for(var i=count-1; i>=0; --i) {
            if(this.loader.filter(store.getAt(i)) === true) {
                ++nodeIndex;
                if(index === i || nodeIndex > nodeCount-1) {
                    break;
                }
            }
        }
        return nodeIndex;
    },
    
    /** private: method[destroy]
     */
    destroy: function() {
        delete this.layerStore;
        GeoExt.tree.LayerContainer.superclass.destroy.apply(this, arguments);
    }
});
    
/**
 * NodeType: gx_layercontainer
 */
Ext.tree.TreePanel.nodeTypes.gx_layercontainer = GeoExt.tree.LayerContainer;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/widgets/tree/LayerContainer.js
 */
Ext.namespace("GeoExt.tree");

/** api: (define)
 *  module = GeoExt.tree
 *  class = BaseLayerContainer
 */

/** api: (extends)
 * GeoExt/widgets/tree/LayerContainer.js
 */

/** api: constructor
 *  .. class:: BaseLayerContainer
 * 
 *     A layer container that will collect all base layers of an OpenLayers
 *     map. Only layers that have displayInLayerSwitcher set to true will be
 *     included. The childrens' iconCls defaults to
 *     "gx-tree-baselayer-icon" and this node' text defaults to
 *     "Base Layer".
 *     
 *     Children will be rendered with a radio button instead of a checkbox,
 *     showing the user that only one base layer can be active at a time.
 * 
 *     To use this node type in ``TreePanel`` config, set nodeType to
 *     "gx_baselayercontainer".
 */
GeoExt.tree.BaseLayerContainer = Ext.extend(GeoExt.tree.LayerContainer, {

    /** private: property[text]
     *  ``String`` The text for this node.
     */
    text: 'Base Layer',

    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(config) {
        config = Ext.applyIf(config || {}, {
            text: this.text,
            loader: {}
        });
        config.loader = Ext.applyIf(config.loader, {
            baseAttrs: Ext.applyIf(config.loader.baseAttrs || {}, {
                iconCls: 'gx-tree-baselayer-icon',
                checkedGroup: 'baselayer'
            }),
            filter: function(record) {
                var layer = record.getLayer();
                return layer.displayInLayerSwitcher === true &&
                    layer.isBaseLayer === true;
            }
        });

        GeoExt.tree.BaseLayerContainer.superclass.constructor.call(this,
            config);
    }
});

/**
 * NodeType: gx_baselayercontainer
 */
Ext.tree.TreePanel.nodeTypes.gx_baselayercontainer = GeoExt.tree.BaseLayerContainer;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/widgets/tree/LayerContainer.js
 */
Ext.namespace("GeoExt.tree");

/** api: (define)
 *  module = GeoExt.tree
 *  class = OverlayLayerContainer
 */

/** api: (extends)
 * GeoExt/widgets/tree/LayerContainer.js
 */

/** api: constructor
 * .. class:: OverlayLayerContainer
 * 
 *     A layer container that will collect all overlay layers of an OpenLayers
 *     map. Only layers that have displayInLayerSwitcher set to true will be
 *     included. The childrens' iconCls defaults to
 *     "gx-tree-layer-icon" and this node' text defaults to "Overlays".
 * 
 *     To use this node type in ``TreePanel`` config, set nodeType to
 *     "gx_overlaylayercontainer".
 */
GeoExt.tree.OverlayLayerContainer = Ext.extend(GeoExt.tree.LayerContainer, {

    /** private: property[text]
     *  ``String`` The text for this node.
     */
    text: 'Overlays',

    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(config) {
        config = Ext.applyIf(config || {}, {
            text: this.text
        });
        config.loader = Ext.applyIf(config.loader || {}, {
            filter: function(record){
                var layer = record.getLayer();
                return layer.displayInLayerSwitcher === true &&
                layer.isBaseLayer === false;
            }
        });
        
        GeoExt.tree.OverlayLayerContainer.superclass.constructor.call(this,
            config);
    }
});

/**
 * NodeType: gx_overlaylayercontainer
 */
Ext.tree.TreePanel.nodeTypes.gx_overlaylayercontainer = GeoExt.tree.OverlayLayerContainer;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/MapPanel.js
 * @require OpenLayers/Layer.js
 */

/** api: (define)
 *  module = GeoExt.tree
 *  class = LayerParamNode
 *  base_link = `Ext.tree.TreeNode <http://dev.sencha.com/deploy/dev/docs/?class=Ext.tree.TreeNode>`_
 */
Ext.namespace("GeoExt.tree");

/** api: constructor
 *  .. class:: LayerParamNode
 * 
 *  A subclass of ``Ext.tree.TreeNode`` that represents a value of a list of
 *  values provided as one of an ``OpenLayers.Layer.HTTPRequest``'s params.
 *  The default iconCls for this node's icon is "gx-tree-layerparam-icon".
 *  
 *  To use this node type in a ``TreePanel`` config, set ``nodeType`` to
 *  "gx_layerparam".
 */
GeoExt.tree.LayerParamNode = Ext.extend(Ext.tree.TreeNode, {
    
    /** api: config[layer]
     *  ``OpenLayers.Layer.HTTPRequest|String`` The layer that this node
     *  represents a subnode of. If provided as string, the string has to
     *  match the title of one of the records in the ``layerStore``.
     */
    
    /** private: property[layer]
     *  ``OpenLayers.Layer.HTTPRequest``
     */
    layer: null,
    
    /** api: config[layerStore]
     *  :class:`GeoExt.data.LayerStore` Only used if layer is provided as
     *  string. The store where we can find the layer. If not provided, the
     *  store of a map panel found by ``GeoExt.MapPanel::guess`` will be used.
     */
    
    /** api: config[param]
     *  ``String`` Key for a param (key-value pair in the params object of the
     *  layer) that this node represents an item of. The value can either be an
     *  ``Array`` or a ``String``, delimited by the character (or string)
     *  provided as ``delimiter`` config option.
     */
    
    /** private: property[param]
     *  ``String``
     */
    param: null,
    
    /** api: config[item]
     *  ``String`` The param's value's item that this node represents.
     */
    
    /** private: property[item]
     *  ``String``
     */
    item: null,
    
    /** api: config[delimiter]
     *  ``String`` Delimiter of the ``param``'s value's items. Default is
     *  ``,`` (comma). If the ``param``'s value is an array, this property
     *  has no effect.
     */
    
    /** private: property[delimiter]
     *  ``String``
     */
    delimiter: null,
    
    /** private: property[allItems]
     *  ``Array`` All items in the param value.
     */
    allItems: null,
    
    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(attributes) {
        var config = attributes || {};
        config.iconCls = config.iconCls || "gx-tree-layerparam-icon";
        config.text = config.text || config.item;
        
        this.param = config.param;
        this.item = config.item;
        this.delimiter = config.delimiter || ",";
        this.allItems = config.allItems;
                
        GeoExt.tree.LayerParamNode.superclass.constructor.apply(this, arguments);

        this.getLayer();

        if(this.layer) {

            // read items from layer if allItems isn't set
            // in the attributes
            if(!this.allItems) {
                this.allItems = this.getItemsFromLayer();
            }

            // if the "checked" attribute isn't set we derive
            // it from what we have in the layer. Else, we need
            // to update the layer param based on the value of
            // the "checked" attribute
            if(this.attributes.checked == null) {
                this.attributes.checked =
                    this.layer.getVisibility() &&
                    this.getItemsFromLayer().indexOf(this.item) >= 0;
            } else {
                this.onCheckChange(this, this.attributes.checked);
            }

            this.layer.events.on({
                "visibilitychanged": this.onLayerVisibilityChanged,
                scope: this
            });

            this.on({
                "checkchange": this.onCheckChange,
                scope: this
            });
        }
    },
    
    /** private: method[getLayer]
     *  :return: ``OpenLayers.Layer.HTTPRequest`` the layer
     *  
     *  Sets this.layer and returns the layer.
     */
    getLayer: function() {
        if(!this.layer) {
            var layer = this.attributes.layer;
            if(typeof layer == "string") {
                var store = this.attributes.layerStore ||
                    GeoExt.MapPanel.guess().layers;
                var i = store.findBy(function(o) {
                    return o.get("title") == layer;
                });
                layer = i != -1 ? store.getAt(i).getLayer() : null;
            }
            this.layer = layer;
        }
        return this.layer;
    },
    
    /** private: method[getItemsFromLayer]
     *  :return: ``Array`` the items of this node's layer's param
     */
    getItemsFromLayer: function() {
        var paramValue = this.layer.params[this.param];
        return paramValue instanceof Array ?
            paramValue :
            (paramValue ? paramValue.split(this.delimiter) : []);
    },
    
    /** private: method[createParams]
     *  :param items: ``Array``
     *  :return: ``Object`` The params object to pass to mergeNewParams
     */
    createParams: function(items) {
        var params = {};
        params[this.param] = this.layer.params[this.param] instanceof Array ?
            items :
            items.join(this.delimiter);
        return params;
    },

    /** private: method[onLayerVisibilityChanged]
     *  Handler for visibilitychanged events on the layer.
     */
    onLayerVisibilityChanged: function() {
        if(this.getItemsFromLayer().length === 0) {
            this.layer.mergeNewParams(this.createParams(this.allItems));
        }
        var visible = this.layer.getVisibility();
        if(visible && this.getItemsFromLayer().indexOf(this.item) !== -1) {
            this.getUI().toggleCheck(true);
        }
        if(!visible) {
            this.layer.mergeNewParams(this.createParams([]));
            this.getUI().toggleCheck(false);
        }
    },
    
    /** private: method[onCheckChange]
     *  :param node: :class:`GeoExt.tree.LayerParamNode``
     *  :param checked: ``Boolean``
     *
     *  Handler for checkchange events.
     */
    onCheckChange: function(node, checked) {
        var layer = this.layer;

        var newItems = [];
        var curItems = this.getItemsFromLayer();
        // if the layer is invisible, and a subnode is checked for the first
        // time, we need to pretend that no subnode param items are set.
        if(checked === true && layer.getVisibility() === false &&
                                curItems.length === this.allItems.length) {
            curItems = [];
            
        }
        Ext.each(this.allItems, function(item) {
            if((item !== this.item && curItems.indexOf(item) !== -1) ||
                            (checked === true && item === this.item)) {
                newItems.push(item);
            }
        }, this);
        
        var visible = (newItems.length > 0);
        // if there is something to display, we want to update the params
        // before the layer is turned on
        visible && layer.mergeNewParams(this.createParams(newItems));
        if(visible !== layer.getVisibility()) {
            layer.setVisibility(visible);
        }
        // if there is nothing to display, we want to update the params
        // when the layer is turned off, so we don't fire illegal requests
        // (i.e. param value being empty)
        (!visible) && layer.mergeNewParams(this.createParams([]));
    },
    
    /** private: method[destroy]
     */
    destroy: function() {
        var layer = this.layer;
        if (layer instanceof OpenLayers.Layer) {
            layer.events.un({
                "visibilitychanged": this.onLayerVisibilityChanged,
                scope: this
            });
        }
        delete this.layer;
        
        this.un("checkchange", this.onCheckChange, this);

        GeoExt.tree.LayerParamNode.superclass.destroy.apply(this, arguments);
    }
});

/**
 * NodeType: gx_layerparam
 */
Ext.tree.TreePanel.nodeTypes.gx_layerparam = GeoExt.tree.LayerParamNode;

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Layer/HTTPRequest.js
 */

/** api: (define)
 *  module = GeoExt.tree
 *  class = LayerParamLoader
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */
Ext.namespace("GeoExt.tree");

/** api: constructor
 *  .. class:: LayerParamLoader
 * 
 *      A loader that creates children from its node's layer
 *      (``OpenLayers.Layer.HTTPRequest``) by items in one of the values in
 *      the layer's params object.
 */
GeoExt.tree.LayerParamLoader = function(config) {
    Ext.apply(this, config);
    this.addEvents(
    
        /** api: event[beforeload]
         *  Triggered before loading children. Return false to avoid
         *  loading children.
         *  
         *  Listener arguments:
         *  
         *  * loader - :class:`GeoExt.tree.LayerLoader` this loader
         *  * node - ``Ex.tree.TreeNode`` the node that this loader is
         *      configured with
         */
        "beforeload",
        
        /** api: event[load]
         *  Triggered after children were loaded.
         *  
         *  Listener arguments:
         *  
         *  * loader - :class:`GeoExt.tree.LayerLoader` this loader
         *  * node - ``Ex.tree.TreeNode`` the node that this loader is
         *      configured with
         */
        "load"
    );

    GeoExt.tree.LayerParamLoader.superclass.constructor.call(this);
};

Ext.extend(GeoExt.tree.LayerParamLoader, Ext.util.Observable, {
    
    /** api: config[param]
     *  ``String`` Key for a param (key-value pair in the params object of the
     *  layer) that this loader uses to create childnodes from its items. The
     *  value can either be an ``Array`` or a ``String``, delimited by the
     *  character (or string) provided as ``delimiter`` config option.
     */
    
    /** private: property[param]
     *  ``String``
     */
    param: null,
    
    /** api: config[delimiter]
     *  ``String`` Delimiter of the ``param``'s value's items. Default is
     *  ``,`` (comma). If the ``param``'s value is an array, this property has
     *  no effect.
     */
    
    /** private: property[delimiter]
     *  ``String``
     */
    delimiter: ",",

    /** private: method[load]
     *  :param node: ``Ext.tree.TreeNode`` The node to add children to.
     *  :param callback: ``Function``
     */
    load: function(node, callback) {
        if(this.fireEvent("beforeload", this, node)) {
            while (node.firstChild) {
                node.removeChild(node.firstChild);
            }
            
            var paramValue =
                (node.layer instanceof OpenLayers.Layer.HTTPRequest) &&
                node.layer.params[this.param];
            if(paramValue) {
                var items = (paramValue instanceof Array) ?
                    paramValue.slice() :
                    paramValue.split(this.delimiter);

                Ext.each(items, function(item, index, allItems) {
                    this.addParamNode(item, allItems, node);
                }, this);
            }
    
            if(typeof callback == "function"){
                callback();
            }
            
            this.fireEvent("load", this, node);
        }
    },
    
    /** private: method[addParamNode]
     *  :param paramItem: ``String`` The param item that the child node will
     *      represent.
     *  :param allParamItems: ``Array`` The full list of param items.
     *  :param node: :class:`GeoExt.tree.LayerNode`` The node that the param
     *      node will be added to as child.
     *  
     *  Adds a child node representing a param value of the layer
     */
    addParamNode: function(paramItem, allParamItems, node) {
        var child = this.createNode({
            layer: node.layer,
            param: this.param,
            item: paramItem,
            allItems: allParamItems,
            delimiter: this.delimiter
        });
        var sibling = node.item(0);
        if(sibling) {
            node.insertBefore(child, sibling);
        } else {
            node.appendChild(child);
        }
    },

    /** api: method[createNode]
     *  :param attr: ``Object`` attributes for the new node
     *
     *  Override this function for custom TreeNode node implementation, or to
     *  modify the attributes at creation time.
     */
    createNode: function(attr){
        if(this.baseAttrs){
            Ext.apply(attr, this.baseAttrs);
        }
        if(typeof attr.uiProvider == 'string'){
           attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        attr.nodeType = attr.nodeType || "gx_layerparam";

        return new Ext.tree.TreePanel.nodeTypes[attr.nodeType](attr);
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt.tree
 *  class = WMSCapabilitiesLoader
 *  base_link = `Ext.tree.TreeLoader <http://www.dev.sencha.com/deploy/dev/docs/?class=Ext.tree.TreeLoader>`_
 */

/**
 * @require OpenLayers/Format/WMSCapabilities.js
 * @require OpenLayers/Format/WMSCapabilities/v1_1_1.js
 * @require OpenLayers/Layer/WMS.js
 * @require OpenLayers/BaseTypes/Class.js
 */

Ext.namespace("GeoExt.tree");

/** api: constructor
 *  .. class:: WMSCapabilitiesLoader
 *
 *      A loader that will load create a tree of all layers of a Web Map
 *      Service (WMS), maintaining its tree structure. Nodes created by this
 *      loader are instances of ``Ext.tree.TreeNode``. If the WMS Capabilities
 *      advertise a name for a layer, an OpenLayers.Layer.WMS instance will
 *      be set on the node in its ``layer`` attribute.
 */
GeoExt.tree.WMSCapabilitiesLoader = function(config) {
    Ext.apply(this, config);
    GeoExt.tree.WMSCapabilitiesLoader.superclass.constructor.call(this);
};

Ext.extend(GeoExt.tree.WMSCapabilitiesLoader, Ext.tree.TreeLoader, {

    /** api: config[url]
     *  ``String``
     *  The online resource of the Web Map Service.
     */
    url: null,

    /** api: config[layerOptions]
     *  ``Object``
     *  Optional options to set on the WMS layers which will be created by
     *  this loader.
     */
    layerOptions: null,

    /** api: config[layerParams]
     *  ``Object``
     *  Optional parameters to set on the WMS layers which will be created by
     *  this loader.
     */
    layerParams: null,

    /** private: property[requestMethod]
     *  ``String`` WMS GetCapabilities request needs to be done using HTTP GET
     */
    requestMethod: 'GET',

    /** private: method[getParams]
     *  Private getParams override.
     */
    getParams: function(node) {
        return {'service': 'WMS', 'request': 'GetCapabilities'};
    },

    /** private: method[processResponse]
     *  :param response: ``Object`` The XHR object
     *  :param node: ``Ext.tree.TreeNode``
     *  :param callback: ``Function``
     *  :param scope: ``Object``
     *
     *  Private processResponse override.
     */
    processResponse : function(response, node, callback, scope){
        var capabilities = new OpenLayers.Format.WMSCapabilities().read(
            response.responseXML && response.responseXML.documentElement ?
                response.responseXML : response.responseText);
        capabilities.capability && this.processLayer(capabilities.capability,
            capabilities.capability.request.getmap.href, node);
        if (typeof callback == "function") {
            callback.apply(scope || node, [node]);
        }
    },

    /** private: method[createWMSLayer]
     *  :param layer: ``Object`` The layer object from the WMS GetCapabilities
     *  parser
     *  :param url: ``String`` The online resource of the WMS
     *  :return: ``OpenLayers.Layer.WMS`` or ``null`` The WMS layer created or
     *  null.
     *
     *  Create a WMS layer which will be attached as an attribute to the
     *  node.
     */
    createWMSLayer: function(layer, url) {
        if (layer.name) {
            return new OpenLayers.Layer.WMS( layer.title, url,
                OpenLayers.Util.extend({format: layer.formats[0], 
                    layers: layer.name}, this.layerParams),
                OpenLayers.Util.extend({minScale: layer.minScale,
                    queryable: layer.queryable, maxScale: layer.maxScale,
                    metadata: layer
                }, this.layerOptions));
        } else {
            return null;
        }
    },

    /** private: method[processLayer]
     *  :param layer: ``Object`` The layer object from the WMS GetCapabilities
     *  parser
     *  :param url: ``String`` The online resource of the WMS
     *  :param node: ``Ext.tree.TreeNode``
     *
     *  Recursive function to create the tree nodes for the layer structure
     *  of a WMS GetCapabilities response.
     */
    processLayer: function(layer, url, node) {
        Ext.each(layer.nestedLayers, function(el) {
            var n = this.createNode({text: el.title || el.name, 
                // use nodeType 'node' so no AsyncTreeNodes are created
                nodeType: 'node',
                layer: this.createWMSLayer(el, url),
                leaf: (el.nestedLayers.length === 0)});
            if(n){
                node.appendChild(n);
            }
            if (el.nestedLayers) {
                this.processLayer(el, url, n);
            }
        }, this);
    }

});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * Published under the BSD license.
 * See http://geoext.org/svn/geoext/core/trunk/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/tips/LayerOpacitySliderTip.js
 * @include GeoExt/data/LayerRecord.js
 * @require OpenLayers/Layer.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = LayerOpacitySlider
 *  base_link = `Ext.slider.SingleSlider <http://dev.sencha.com/deploy/dev/docs/?class=Ext.slider.SingleSlider>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to render a slider outside the map viewport:
 *
 *  .. code-block:: javascript
 *
 *      var slider = new GeoExt.LayerOpacitySlider({
 *          renderTo: document.body,
 *          width: 200,
 *          layer: layer
 *      });
 *
 *  Sample code to add a slider to a map panel:
 *
 *  .. code-block:: javascript
 *
 *      var layer = new OpenLayers.Layer.WMS(
 *          "Global Imagery",
 *          "http://maps.opengeo.org/geowebcache/service/wms",
 *          {layers: "bluemarble"}
 *      );
 *      var panel = new GeoExt.MapPanel({
 *          renderTo: document.body,
 *          height: 300,
 *          width: 400,
 *          map: {
 *              controls: [new OpenLayers.Control.Navigation()]
 *          },
 *          layers: [layer],
 *          extent: [-5, 35, 15, 55],
 *          items: [{
 *              xtype: "gx_opacityslider",
 *              layer: layer,
 *              aggressive: true,
 *              vertical: true,
 *              height: 100,
 *              x: 10,
 *              y: 20
 *          }]
 *      });
 */

/** api: constructor
 *  .. class:: LayerOpacitySlider(config)
 *
 *      Create a slider for controlling a layer's opacity.
 */
GeoExt.LayerOpacitySlider = Ext.extend(Ext.slider.SingleSlider, {

    /** api: config[layer]
     *  ``OpenLayers.Layer`` or :class:`GeoExt.data.LayerRecord`
     *  The layer this slider changes the opacity of. (required)
     */
    /** private: property[layer]
     *  ``OpenLayers.Layer``
     */
    layer: null,

    /** api: config[complementaryLayer]
     *  ``OpenLayers.Layer`` or :class:`GeoExt.data.LayerRecord` 
     *  If provided, a layer that will be made invisible (its visibility is
     *  set to false) when the slider value is set to its max value. If this
     *  slider is used to fade visibility between to layers, setting
     *  ``complementaryLayer`` and ``changeVisibility`` will make sure that
     *  only visible tiles are loaded when the slider is set to its min or max
     *  value. (optional)
     */
    complementaryLayer: null,

    /** api: config[delay]
     *  ``Number`` Time in milliseconds before setting the opacity value to the
     *  layer. If the value change again within that time, the original value
     *  is not set. Only applicable if aggressive is true.
     */
    delay: 5,

    /** api: config[changeVisibilityDelay]
     *  ``Number`` Time in milliseconds before changing the layer's visibility.
     *  If the value changes again within that time, the layer's visibility
     *  change does not occur. Only applicable if changeVisibility is true.
     *  Defaults to 5.
     */
    changeVisibilityDelay: 5,

    /** api: config[aggressive]
     *  ``Boolean``
     *  If set to true, the opacity is changed as soon as the thumb is moved.
     *  Otherwise when the thumb is released (default).
     */
    aggressive: false,

    /** api: config[changeVisibility]
     *  ``Boolean``
     *  If set to true, the layer's visibility is handled by the
     *  slider, the slider makes the layer invisible when its
     *  value is changed to the min value, and makes the layer
     *  visible again when its value goes from the min value
     *  to some other value. The layer passed to the constructor
     *  must be visible, as its visibility is fully handled by
     *  the slider. Defaults to false.
     */
    changeVisibility: false,

    /** api: config[value]
     *  ``Number``
     *  The value to initialize the slider with. This value is
     *  taken into account only if the layer's opacity is null.
     *  If the layer's opacity is null and this value is not
     *  defined in the config object then the slider initializes
     *  it to the max value.
     */
    value: null,

    /** api: config[inverse]
     *  ``Boolean``
     *  If true, we will work with transparency instead of with opacity.
     *  Defaults to false.
     */
    /** private: property[inverse]
     *  ``Boolean``
     */
    inverse: false,

    /** private: method[constructor]
     *  Construct the component.
     */
    constructor: function(config) {
        if (config.layer) {
            this.layer = this.getLayer(config.layer);
            this.bind();
            this.complementaryLayer = this.getLayer(config.complementaryLayer);
            // before we call getOpacityValue inverse should be set
            if (config.inverse !== undefined) {
                this.inverse = config.inverse;
            }
            config.value = (config.value !== undefined) ? 
                config.value : this.getOpacityValue(this.layer);
            delete config.layer;
            delete config.complementaryLayer;
        }
        GeoExt.LayerOpacitySlider.superclass.constructor.call(this, config);
    },

    /** private: method[bind]
     */
    bind: function() {
        if (this.layer && this.layer.map) {
            this.layer.map.events.on({
                changelayer: this.update,
                scope: this
            });
        }
    },

    /** private: method[unbind]
     */
    unbind: function() {
        if (this.layer && this.layer.map && this.layer.map.events) {
            this.layer.map.events.un({
                changelayer: this.update,
                scope: this
            });
        }
    },

    /** private: method[update]
     *  Registered as a listener for opacity change.  Updates the value of the slider.
     */
    update: function(evt) {
        if (evt.property === "opacity" && evt.layer == this.layer &&
            !this._settingOpacity) {
            this.setValue(this.getOpacityValue(this.layer));
        }
    },

    /** api: method[setLayer]
     *  :param layer: ``OpenLayers.Layer`` or :class:`GeoExt.data.LayerRecord`
     *
     *  Bind a new layer to the opacity slider.
     */
    setLayer: function(layer) {
        this.unbind();
        this.layer = this.getLayer(layer);
        this.setValue(this.getOpacityValue(layer));
        this.bind();
    },

    /** private: method[getOpacityValue]
     *  :param layer: ``OpenLayers.Layer`` or :class:`GeoExt.data.LayerRecord`
     *  :return:  ``Integer`` The opacity for the layer.
     *
     *  Returns the opacity value for the layer.
     */
    getOpacityValue: function(layer) {
        var value;
        if (layer && layer.opacity !== null) {
            value = parseInt(layer.opacity * (this.maxValue - this.minValue));
        } else {
            value = this.maxValue;
        }
        if (this.inverse === true) {
            value = (this.maxValue - this.minValue) - value;
        }
        return value;
    },

    /** private: method[getLayer]
     *  :param layer: ``OpenLayers.Layer`` or :class:`GeoExt.data.LayerRecord`
     *  :return:  ``OpenLayers.Layer`` The OpenLayers layer object
     *
     *  Returns the OpenLayers layer object for a layer record or a plain layer 
     *  object.
     */
    getLayer: function(layer) {
        if (layer instanceof OpenLayers.Layer) {
            return layer;
        } else if (layer instanceof GeoExt.data.LayerRecord) {
            return layer.getLayer();
        }
    },

    /** private: method[initComponent]
     *  Initialize the component.
     */
    initComponent: function() {

        GeoExt.LayerOpacitySlider.superclass.initComponent.call(this);

        if (this.changeVisibility && this.layer &&
            (this.layer.opacity == 0 || 
            (this.inverse === false && this.value == this.minValue) || 
            (this.inverse === true && this.value == this.maxValue))) {
            this.layer.setVisibility(false);
        }

        if (this.complementaryLayer &&
            ((this.layer && this.layer.opacity == 1) ||
             (this.inverse === false && this.value == this.maxValue) ||
             (this.inverse === true && this.value == this.minValue))) {
            this.complementaryLayer.setVisibility(false);
        }

        if (this.aggressive === true) {
            this.on('change', this.changeLayerOpacity, this, {
                buffer: this.delay
            });
        } else {
            this.on('changecomplete', this.changeLayerOpacity, this);
        }

        if (this.changeVisibility === true) {
            this.on('change', this.changeLayerVisibility, this, {
                buffer: this.changeVisibilityDelay
            });
        }

        if (this.complementaryLayer) {
            this.on('change', this.changeComplementaryLayerVisibility, this, {
                buffer: this.changeVisibilityDelay
            });
        }
        this.on("beforedestroy", this.unbind, this);
    },

    /** private: method[changeLayerOpacity]
     *  :param slider: :class:`GeoExt.LayerOpacitySlider`
     *  :param value: ``Number`` The slider value
     *
     *  Updates the ``OpenLayers.Layer`` opacity value.
     */
    changeLayerOpacity: function(slider, value) {
        if (this.layer) {
            value = value / (this.maxValue - this.minValue);
            if (this.inverse === true) {
                value = 1 - value;
            }
            this._settingOpacity = true;
            this.layer.setOpacity(value);
            delete this._settingOpacity;
        }
    },

    /** private: method[changeLayerVisibility]
     *  :param slider: :class:`GeoExt.LayerOpacitySlider`
     *  :param value: ``Number`` The slider value
     *
     *  Updates the ``OpenLayers.Layer`` visibility.
     */
    changeLayerVisibility: function(slider, value) {
        var currentVisibility = this.layer.getVisibility();
        if ((this.inverse === false && value == this.minValue) ||
            (this.inverse === true && value == this.maxValue) &&
            currentVisibility === true) {
            this.layer.setVisibility(false);
        } else if ((this.inverse === false && value > this.minValue) ||
            (this.inverse === true && value < this.maxValue) &&
                   currentVisibility == false) {
            this.layer.setVisibility(true);
        }
    },

    /** private: method[changeComplementaryLayerVisibility]
     *  :param slider: :class:`GeoExt.LayerOpacitySlider`
     *  :param value: ``Number`` The slider value
     *
     *  Updates the complementary ``OpenLayers.Layer`` visibility.
     */
    changeComplementaryLayerVisibility: function(slider, value) {
        var currentVisibility = this.complementaryLayer.getVisibility();
        if ((this.inverse === false && value == this.maxValue) ||
            (this.inverse === true && value == this.minValue) &&
            currentVisibility === true) {
            this.complementaryLayer.setVisibility(false);
        } else if ((this.inverse === false && value < this.maxValue) ||
            (this.inverse === true && value > this.minValue) &&
                   currentVisibility == false) {
            this.complementaryLayer.setVisibility(true);
        }
    },

    /** private: method[addToMapPanel]
     *  :param panel: :class:`GeoExt.MapPanel`
     *
     *  Called by a MapPanel if this component is one of the items in the panel.
     */
    addToMapPanel: function(panel) {
        this.on({
            render: function() {
                var el = this.getEl();
                el.setStyle({
                    position: "absolute",
                    zIndex: panel.map.Z_INDEX_BASE.Control
                });
                el.on({
                    mousedown: this.stopMouseEvents,
                    click: this.stopMouseEvents
                });
            },
            scope: this
        });
    },

    /** private: method[removeFromMapPanel]
     *  :param panel: :class:`GeoExt.MapPanel`
     *
     *  Called by a MapPanel if this component is one of the items in the panel.
     */
    removeFromMapPanel: function(panel) {
        var el = this.getEl();
        el.un({
            mousedown: this.stopMouseEvents,
            click: this.stopMouseEvents,
            scope: this
        });
        this.unbind();
    },

    /** private: method[stopMouseEvents]
     *  :param e: ``Object``
     */
    stopMouseEvents: function(e) {
        e.stopEvent();
    }
});

/** api: xtype = gx_opacityslider */
Ext.reg('gx_opacityslider', GeoExt.LayerOpacitySlider);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt
 *  class = LayerLegend
 *  base_link = `Ext.Container <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Container>`_
 */

Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: LayerLegend(config)
 *
 *      Base class for components of :class:`GeoExt.LegendPanel`.
 */
GeoExt.LayerLegend = Ext.extend(Ext.Container, {

    /** api: config[layerRecord]
     *  :class:`GeoExt.data.LayerRecord`  The layer record for the legend
     */
    layerRecord: null,

    /** api: config[showTitle]
     *  ``Boolean``
     *  Whether or not to show the title of a layer. This can be overridden
     *  on the LayerStore record using the hideTitle property.
     */
    showTitle: true,

    /** api: config[legendTitle]
     *  ``String``
     *  Optional title to be displayed instead of the layer title.  If this is
     *  set, the value of ``showTitle`` will be ignored (assumed to be true).
     */
    legendTitle: null,

    /** api: config[labelCls]
     *  ``String``
     *  Optional css class to use for the layer title labels.
     */
    labelCls: null,

    /** private: property[layerStore]
     *  :class:`GeoExt.data.LayerStore`
     */
    layerStore: null,

    /** private: method[initComponent]
     */
    initComponent: function() {
        GeoExt.LayerLegend.superclass.initComponent.call(this);
        this.autoEl = {};
        this.add({
            xtype: "label",
            html: this.getLayerTitle(this.layerRecord),
            cls: 'x-form-item x-form-item-label' +
                (this.labelCls ? ' ' + this.labelCls : '')
        });
        if (this.layerRecord && this.layerRecord.store) {
            this.layerStore = this.layerRecord.store;
            this.layerStore.on("update", this.onStoreUpdate, this);
            this.layerStore.on("add", this.onStoreAdd, this);
            this.layerStore.on("remove", this.onStoreRemove, this);
        }
    },

    /** private: method[getText]
     *  :returns: ``String``
     *
     *  Get the label text of the legend.
     */
    getLabel: function() {
        var label = this.items.get(0);
        return label.rendered ? label.el.dom.innerHTML : label.html;
    },

    /** private: method[onStoreRemove]
     *  Handler for remove event of the layerStore
     *
     *  :param store: ``Ext.data.Store`` The store from which the record was
     *      removed.
     *  :param record: ``Ext.data.Record`` The record object corresponding
     *      to the removed layer.
     *  :param index: ``Integer`` The index in the store at which the record
     *      was remvoed.
     */
    onStoreRemove: function(store, record, index) {
        // to be implemented by subclasses if needed
    },

    /** private: method[onStoreAdd]
     *  Handler for add event of the layerStore
     *
     *  :param store: ``Ext.data.Store`` The store to which the record was
     *      added.
     *  :param record: ``Ext.data.Record`` The record object corresponding
     *      to the added layer.
     *  :param index: ``Integer`` The index in the store at which the record
     *      was added.
     */
    onStoreAdd: function(store, record, index) {
        // to be implemented by subclasses if needed
    },

    /** private: method[onStoreUpdate]
     *  Update a the legend. Gets called when the store fires the update event.
     *  This usually means the visibility of the layer, its style or title
     *  has changed.
     *
     *  :param store: ``Ext.data.Store`` The store in which the record was
     *      changed.
     *  :param record: ``Ext.data.Record`` The record object corresponding
     *      to the updated layer.
     *  :param operation: ``String`` The type of operation.
     */
    onStoreUpdate: function(store, record, operation) {
        // if we don't have items, we are already awaiting garbage
        // collection after being removed by LegendPanel::removeLegend, and
        // updating will cause errors
        if (record === this.layerRecord && this.items.getCount() > 0) {
            var layer = record.getLayer();
            this.setVisible(layer.getVisibility() &&
                layer.calculateInRange() && layer.displayInLayerSwitcher &&
                !record.get('hideInLegend'));
            this.update();
        }
    },

    /** private: method[update]
     *  Updates the legend.
     */
    update: function() {
        var title = this.getLayerTitle(this.layerRecord);
        var item = this.items.itemAt(0);
        if (item instanceof Ext.form.Label && this.getLabel() !== title) {
            // we need to update the title
            item.setText(title, false);
        }
    },

    /** private: method[getLayerTitle]
     *  :arg record: :class:GeoExt.data.LayerRecord
     *  :returns: ``String``
     *
     *  Get a title for the layer.  If the record doesn't have a title, use the
     *  name.
     */
    getLayerTitle: function(record) {
        var title = this.legendTitle || "";
        if (this.showTitle && !title) {
            if (record && !record.get("hideTitle")) {
                title = record.get("title") ||
                    record.get("name") ||
                    record.getLayer().name || "";
            }
        }
        return title;
    },

    /** private: method[beforeDestroy]
     */
    beforeDestroy: function() {
        if (this.layerStore) {
            this.layerStore.un("update", this.onStoreUpdate, this);
            this.layerStore.un("remove", this.onStoreRemove, this);
            this.layerStore.un("add", this.onStoreAdd, this);
        }
        GeoExt.LayerLegend.superclass.beforeDestroy.apply(this, arguments);
    },

    /** private: method[onDestroy]
     */
    onDestroy: function() {
        this.layerRecord = null;
        this.layerStore = null;
        GeoExt.LayerLegend.superclass.onDestroy.apply(this, arguments);
    }

});

/** class: method[getTypes]
 *  :param layerRecord: class:`GeoExt.data.LayerRecord` A layer record to get
 *      legend types for. If not provided, all registered types will be
 *      returned.
 *  :param preferredTypes: ``Array(String)`` Types that should be considered.
 *      first. If not provided, all registered legend types will be returned
 *      in the order of their score for support of the provided layerRecord.
 *  :return: ``Array(String)`` xtypes of legend types that can be used with
 *      the provided ``layerRecord``.
 *
 *  Gets an array of legend xtypes that support the provided layer record,
 *  with optionally provided preferred types listed first.
 */
GeoExt.LayerLegend.getTypes = function(layerRecord, preferredTypes) {
    var types = (preferredTypes || []).concat(),
        scoredTypes = [], score, type;
    for (type in GeoExt.LayerLegend.types) {
        score = GeoExt.LayerLegend.types[type].supports(layerRecord);
        if(score > 0) {
            // add to scoredTypes if not preferred
            if (types.indexOf(type) == -1) {
                scoredTypes.push({
                    type: type,
                    score: score
                });
            }
        } else {
            // preferred, but not supported
            types.remove(type);
        }
    }
    scoredTypes.sort(function(a, b) {
        return a.score < b.score ? 1 : (a.score == b.score ? 0 : -1);
    });
    var len = scoredTypes.length, goodTypes = new Array(len);
    for (var i=0; i<len; ++i) {
        goodTypes[i] = scoredTypes[i].type;
    }
    // take the remaining preferred types, and add other good types
    return types.concat(goodTypes);
};

/** private: method[supports]
 *  :param layerRecord: :class:`GeoExt.data.LayerRecord` The layer record
 *      to check support for.
 *  :return: ``Integer`` score indicating how good the legend supports the
 *      provided record. 0 means not supported.
 *
 *  Checks whether this legend type supports the provided layerRecord.
 */
GeoExt.LayerLegend.supports = function(layerRecord) {
    // to be implemented by subclasses
};

/** class: constant[GeoExt.LayerLegend.types]
 *  An object containing a name-class mapping of LayerLegend subclasses.
 *  To register as LayerLegend, a subclass should add itself to this object:
 *
 *  .. code-block:: javascript
 *
 *      GeoExt.GetLegendGraphicLegend = Ext.extend(GeoExt.LayerLegend, {
 *      });
 *
 *      GeoExt.LayerLegend.types["getlegendgraphic"] =
 *          GeoExt.GetLegendGraphicLegend;
 */
GeoExt.LayerLegend.types = {};

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt
 *  class = LegendImage
 *  base_link = `Ext.BoxComponent <http://dev.sencha.com/deploy/dev/docs/?class=Ext.BoxComponent>`_
 */

Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: LegendImage(config)
 *
 *      Show a legend image in a BoxComponent and make sure load errors are 
 *      dealt with.
 */
GeoExt.LegendImage = Ext.extend(Ext.BoxComponent, {

    /** api: config[url]
     *  ``String``  The url of the image to load
     */
    url: null,
    
    /** api: config[defaultImgSrc]
     *  ``String`` Path to image that will be used if the legend image fails
     *  to load.  Default is Ext.BLANK_IMAGE_URL.
     */
    defaultImgSrc: null,

    /** api: config[imgCls]
     *  ``String``  Optional css class to apply to img tag
     */
    imgCls: null,
    
    /** private: config[noImgCls]
     *  ``String`` CSS class applied to img tag when no image is available or
     *  the default image was loaded.
     */
    noImgCls: "gx-legend-noimage",
    
    /** private: method[initComponent]
     *  Initializes the legend image component. 
     */
    initComponent: function() {
        GeoExt.LegendImage.superclass.initComponent.call(this);
        if(this.defaultImgSrc === null) {
            this.defaultImgSrc = Ext.BLANK_IMAGE_URL;
        }
        this.autoEl = {
            tag: "img",
            "class": (this.imgCls ? this.imgCls + " " + this.noImgCls : this.noImgCls),
            src: this.defaultImgSrc
        };
    },

    /** api: method[getImgEl]
     *  :return:  ``Ext.Element`` The image element.
     *
     *  Returns the image element.
     *  This method is supposed to be overriden in subclasses.
     */
    getImgEl: function() {
        return this.getEl();
    },

    /** api: method[setUrl]
     *  :param url: ``String`` The new URL.
     *  
     *  Sets the url of the legend image.
     */
    setUrl: function(url) {
        this.url = url;
        var el = this.getImgEl();
        if (el) {
            el.un("load", this.onImageLoad, this);
            el.on("load", this.onImageLoad, this, {single: true});
            el.un("error", this.onImageLoadError, this);
            el.on("error", this.onImageLoadError, this, {single: true});
            el.dom.src = url;
        }
    },

    /** private: method[onRender]
     *  Private method called when the legend image component is being
     *  rendered.
     */
    onRender: function(ct, position) {
        GeoExt.LegendImage.superclass.onRender.call(this, ct, position);
        if(this.url) {
            this.setUrl(this.url);
        }
    },

    /** private: method[onDestroy]
     *  Private method called during the destroy sequence.
     */
    onDestroy: function() {
        var el = this.getImgEl();
        if(el) {
            el.un("load", this.onImageLoad, this);
            el.un("error", this.onImageLoadError, this);
        }
        GeoExt.LegendImage.superclass.onDestroy.apply(this, arguments);
    },
    
    /** private: method[onImageLoadError]
     *  Private method called if the legend image fails loading.
     */
    onImageLoadError: function() {
        var el = this.getImgEl();
        el.addClass(this.noImgCls);
        el.dom.src = this.defaultImgSrc;
    },
    
    /** private: method[onImageLoad]
     *  Private method called after the legend image finished loading.
     */
    onImageLoad: function() {
        var el = this.getImgEl();
        if (!OpenLayers.Util.isEquivalentUrl(el.dom.src, this.defaultImgSrc)) {
            el.removeClass(this.noImgCls);
        }
    }

});

/** api: xtype = gx_legendimage */
Ext.reg('gx_legendimage', GeoExt.LegendImage);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/LegendImage.js
 * @requires GeoExt/widgets/LayerLegend.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = UrlLegend
 */

/** api: (extends)
 * GeoExt/widgets/LayerLegend.js
 */

Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: UrlLegend(config)
 *
 *      Show a legend image in a BoxComponent and make sure load errors are 
 *      dealt with.
 */
GeoExt.UrlLegend = Ext.extend(GeoExt.LayerLegend, {

    /** private: method[initComponent]
     *  Initializes the legend image component. 
     */
    initComponent: function() {
        GeoExt.UrlLegend.superclass.initComponent.call(this);
        this.add(new GeoExt.LegendImage({
            url: this.layerRecord.get("legendURL")
        }));
    },
    
    /** private: method[update]
     *  Private override
     */
    update: function() {
        GeoExt.UrlLegend.superclass.update.apply(this, arguments);
        this.items.get(1).setUrl(this.layerRecord.get("legendURL"));
    }

});

/** private: method[supports]
 *  Private override
 */
GeoExt.UrlLegend.supports = function(layerRecord) {
    return layerRecord.get("legendURL") == null ? 0 : 10;
};

/** api: legendtype = gx_urllegend */
GeoExt.LayerLegend.types["gx_urllegend"] = GeoExt.UrlLegend;

/** api: xtype = gx_urllegend */
Ext.reg('gx_urllegend', GeoExt.UrlLegend);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/LegendImage.js
 * @requires GeoExt/widgets/LayerLegend.js
 * @require OpenLayers/Util.js
 * @require OpenLayers/Layer/WMS.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = WMSLegend
 */

/** api: (extends)
 *  GeoExt/widgets/LayerLegend.js
 */
Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: WMSLegend(config)
 *
 *  Show a legend image for a WMS layer. The image can be read from the styles
 *  field of a layer record (if the record comes e.g. from a
 *  :class:`GeoExt.data.WMSCapabilitiesReader`). If not provided, a
 *  GetLegendGraphic request will be issued to retrieve the image.
 */
GeoExt.WMSLegend = Ext.extend(GeoExt.LayerLegend, {

    /** api: config[defaultStyleIsFirst]
     *  ``Boolean``
     *  The WMS spec does not say if the first style advertised for a layer in
     *  a Capabilities document is the default style that the layer is
     *  rendered with. We make this assumption by default. To be strictly WMS
     *  compliant, set this to false, but make sure to configure a STYLES
     *  param with your WMS layers, otherwise LegendURLs advertised in the
     *  GetCapabilities document cannot be used.
     */
    defaultStyleIsFirst: true,

    /** api: config[useScaleParameter]
     *  ``Boolean``
     *  Should we use the optional SCALE parameter in the SLD WMS
     *  GetLegendGraphic request? Defaults to true.
     */
    useScaleParameter: true,

    /** api: config[baseParams]
     * ``Object``
     *  Optional parameters to add to the legend url, this can e.g. be used to
     *  support vendor-specific parameters in a SLD WMS GetLegendGraphic
     *  request. To override the default MIME type of image/gif use the
     *  FORMAT parameter in baseParams.
     *
     *  .. code-block:: javascript
     *
     *      var legendPanel = new GeoExt.LegendPanel({
     *          map: map,
     *          title: 'Legend Panel',
     *          defaults: {
     *              style: 'padding:5px',
     *              baseParams: {
     *                  FORMAT: 'image/png',
     *                  LEGEND_OPTIONS: 'forceLabels:on'
     *              }
     *          }
     *      });
     */
    baseParams: null,

    /** api: config[itemXType]
     *  ``String``
     *  The xtype to be used for each item of this legend. Defaults to
     *  `gx_legendimage`.
     */
    itemXType: "gx_legendimage",
    
    /** private: method[initComponent]
     *  Initializes the WMS legend. For group layers it will create multiple
     *  image box components.
     */
    initComponent: function() {
        GeoExt.WMSLegend.superclass.initComponent.call(this);
        var layer = this.layerRecord.getLayer();
        this._noMap = !layer.map;
        layer.events.register("moveend", this, this.onLayerMoveend);
        this.update();
    },

    /** private: method[onLayerMoveend]
     *  :param e: ``Object``
     */
    onLayerMoveend: function(e) {
        if ((e.zoomChanged === true && this.useScaleParameter === true) ||
                                                                this._noMap) {
            delete this._noMap;
            this.update();
        }
    },

    /** private: method[getLegendUrl]
     *  :param layerName: ``String`` A sublayer.
     *  :param layerNames: ``Array(String)`` The array of sublayers,
     *      read from this.layerRecord if not provided.
     *  :return: ``String`` The legend URL.
     *
     *  Get the legend URL of a sublayer.
     */
    getLegendUrl: function(layerName, layerNames) {
        var rec = this.layerRecord;
        var url;
        var styles = rec && rec.get("styles");
        var layer = rec.getLayer();
        layerNames = layerNames || [layer.params.LAYERS].join(",").split(",");

        var styleNames = layer.params.STYLES &&
                             [layer.params.STYLES].join(",").split(",");
        var idx = layerNames.indexOf(layerName);
        var styleName = styleNames && styleNames[idx];

        // check if we have a legend URL in the record's
        // "styles" data field
        if(styles && styles.length > 0) {
            if(styleName) {
                Ext.each(styles, function(s) {
                    url = (s.name == styleName && s.legend) && s.legend.href;
                    return !url;
                });
            } else {
                if(!styleNames && !layer.params.SLD && !layer.params.SLD_BODY) {
                    // let's search for a style with a 'layerName' attribute
                    Ext.each(styles, function(s) {
                        url = (s.layerName == layerName && s.legend) &&
                                                                s.legend.href;
                        return !url;
                    });
                    if (!url && this.defaultStyleIsFirst === true) {
                        url = styles[0].legend && styles[0].legend.href;
                    }
                }
            }
            if (url) {
                url = decodeURIComponent(url);
            }
        }
        if(!url) {
            url = layer.getFullRequestString({
                REQUEST: "GetLegendGraphic",
                WIDTH: null,
                HEIGHT: null,
                EXCEPTIONS: "application/vnd.ogc.se_xml",
                LAYER: layerName,
                LAYERS: null,
                STYLE: (styleName !== '') ? styleName: null,
                STYLES: null,
                SRS: null,
                FORMAT: null,
                TIME: null
            });
        }
        var params = Ext.apply({}, this.baseParams);
        if (layer.params._OLSALT) {
            // update legend after a forced layer redraw
            params._OLSALT = layer.params._OLSALT;
        }
        var appendParams = Ext.urlEncode(params);
        var formatRegEx = /([&\?]?)format=[^&]*&?/i;
        if (formatRegEx.test(appendParams) && formatRegEx.test(url)) {
            url = url.replace(formatRegEx, '$1');
        }
        url = OpenLayers.Util.urlAppend(url, appendParams);
        if (url.toLowerCase().indexOf("request=getlegendgraphic") != -1) {
            if (url.toLowerCase().indexOf("format=") == -1) {
                url = Ext.urlAppend(url, "FORMAT=image%2Fgif");
            }
            // add scale parameter - also if we have the url from the record's
            // styles data field and it is actually a GetLegendGraphic request.
            if (this.useScaleParameter === true) {
                var scale = layer.map.getScale();
                url = Ext.urlAppend(url, "SCALE=" + scale);
            }
        }

        return url;
    },

    /** private: method[update]
     *  Update the legend, adding, removing or updating
     *  the per-sublayer box component.
     */
    update: function() {
        var layer = this.layerRecord.getLayer();
        // In some cases, this update function is called on a layer
        // that has just been removed, see ticket #238.
        // The following check bypass the update if map is not set.
        if(!(layer && layer.map)) {
            return;
        }
        GeoExt.WMSLegend.superclass.update.apply(this, arguments);

        var layerNames, layerName, i, len;

        layerNames = [layer.params.LAYERS].join(",").split(",");

        var destroyList = [];
        var textCmp = this.items.find(function(item){
            return item.isXType('label');
        });
        this.items.each(function(cmp) {
            i = layerNames.indexOf(cmp.itemId);
            if(i < 0 && cmp != textCmp) {
                destroyList.push(cmp);
            } else if(cmp !== textCmp){
                layerName = layerNames[i];
                var newUrl = this.getLegendUrl(layerName, layerNames);
                if(!OpenLayers.Util.isEquivalentUrl(newUrl, cmp.url)) {
                    cmp.setUrl(newUrl);
                }
            }
        }, this);
        for(i = 0, len = destroyList.length; i<len; i++) {
            var cmp = destroyList[i];
            // cmp.destroy() does not remove the cmp from
            // its parent container!
            this.remove(cmp);
            cmp.destroy();
        }

        for(i = 0, len = layerNames.length; i<len; i++) {
            layerName = layerNames[i];
            if(!this.items || !this.getComponent(layerName)) {
                this.add({
                    xtype: this.itemXType,
                    url: this.getLegendUrl(layerName, layerNames),
                    itemId: layerName
                });
            }
        }
        this.doLayout();
    },

    /** private: method[beforeDestroy]
     */
    beforeDestroy: function() {
        if (this.useScaleParameter === true) {
            var layer = this.layerRecord.getLayer();
            layer && layer.events &&
                layer.events.unregister("moveend", this, this.onLayerMoveend);
        }
        GeoExt.WMSLegend.superclass.beforeDestroy.apply(this, arguments);
    }

});

/** private: method[supports]
 *  Private override
 */
GeoExt.WMSLegend.supports = function(layerRecord) {
    return layerRecord.getLayer() instanceof OpenLayers.Layer.WMS ? 1 : 0;
};

/** api: legendtype = gx_wmslegend */
GeoExt.LayerLegend.types["gx_wmslegend"] = GeoExt.WMSLegend;

/** api: xtype = gx_wmslegend */
Ext.reg('gx_wmslegend', GeoExt.WMSLegend);

/**
 * Copyright (c) 2008-2011 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/LegendImage.js
 * @requires GeoExt/widgets/LayerLegend.js
 */
GeoExt.WMTSLegend = Ext.extend(GeoExt.LayerLegend, {

    /** private: method[initComponent]
     *  Initializes the WMTS legend. 
     */
    initComponent: function() {
        GeoExt.WMTSLegend.superclass.initComponent.call(this);
        var layer = this.layerRecord.getLayer();
        this._noMap = !layer.map;
        layer.events.register("moveend", this, this.onLayerMoveend);
        this.update();
    },
    
    /** private: method[onLayerMoveend]
     *  :param e: ``Object``
     */
    onLayerMoveend: function(e) {
        if (e.zoomChanged === true || this._noMap) {
            delete this._noMap;
            this.update();
        }
    },

    /** private: method[getLegendUrl]

     *  :return: ``String`` The legend URL.
     *
     *  Get the legend URL of a layer.
     */
    getLegendUrl: function() {
        var rec = this.layerRecord,
            layer = rec.getLayer();

        var mapDenominator = layer.map && layer.map.getScale();
        if (!mapDenominator) {
            return;
        }

        var styles = rec.get("styles"),
            url, style, legends, legend;

        for (var i=0, l=styles.length; i<l; i++) {
            style = styles[i];
            if (style.identifier === layer.style) {
                legends = style.legends;
                if (!legends) {
                    return;
                }
                // get the legend for the current layer scale
                for (var j=0, ll=legends.length; j<ll; j++) {
                    legend = legends[j];
                    if (!legend.href) {
                        continue;
                    }
                    var hasMin = legend.hasOwnProperty("minScaleDenominator"),
                        hasMax = legend.hasOwnProperty("maxScaleDenominator");
                    if (!hasMin && !hasMax) {
                        return legend.href;
                    }
                    if (!hasMin && mapDenominator<legend.maxScaleDenominator) {
                        return legend.href;
                    }
                    if (!hasMax && mapDenominator>=legend.minScaleDenominator){
                        return legend.href;
                    }
                    if (mapDenominator < legend.maxScaleDenominator && 
                        mapDenominator >= legend.minScaleDenominator) {

                        return legend.href;
                    }
                }
                break;
            }
        }
        return url;
    },

    /** private: method[update]
     *  Update the legend, adding, removing or updating
     *  the box component.
     */
    update: function() {
        var layer = this.layerRecord.getLayer();
        // In some cases, this update function is called on a layer
        // that has just been removed, see ticket #238.
        // The following check bypass the update if map is not set.
        if(!(layer && layer.map)) {
            return;
        }
        GeoExt.WMTSLegend.superclass.update.apply(this, arguments);

        var newURL = this.getLegendUrl();
        if (this.items.getCount() == 2) {
            var cmp = this.items.itemAt(1);
            if (cmp.url !== newURL) {
                this.remove(cmp);
                cmp.destroy();
                if (newURL) {
                    this.add({
                        xtype: "gx_legendimage",
                        url: newURL
                    });
                }
            }
        } else if (newURL) {
            this.add({
                xtype: "gx_legendimage",
                url: newURL
            });
        }
        this.doLayout();
    },

    /** private: method[beforeDestroy]
     */
    beforeDestroy: function() {
        var layer = this.layerRecord.getLayer();
        layer && layer.events &&
            layer.events.unregister("moveend", this, this.onLayerMoveend);

        GeoExt.WMTSLegend.superclass.beforeDestroy.apply(this, arguments);
    }

});

/** private: method[supports]
 *  Private override
 */
GeoExt.WMTSLegend.supports = function(layerRecord) {
    return layerRecord.getLayer() instanceof OpenLayers.Layer.WMTS ? 1 : 0;
};

/** api: legendtype = gx_wmtslegend */
GeoExt.LayerLegend.types["gx_wmtslegend"] = GeoExt.WMTSLegend;

/** api: xtype = gx_wmtslegend */
Ext.reg('gx_wmtslegend', GeoExt.WMTSLegend);
/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/FeatureRenderer.js
 * @requires GeoExt/widgets/LayerLegend.js
 * @requires OpenLayers/Style.js
 * @requires OpenLayers/Rule.js
 * @requires OpenLayers/Layer/Vector.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = VectorLegend
 */

/** api: (extends)
 * GeoExt/widgets/LayerLegend.js
 */

Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: VectorLegend(config)
 *
 *      Create a vector legend.
 */
GeoExt.VectorLegend = Ext.extend(GeoExt.LayerLegend, {

    /** api: config[layerRecord]
     *  :class:`GeoExt.data.LayerRecord`
     *  The record containing a vector layer that this legend will be based on.  
     *  One of ``layerRecord``, ``layer``,  or ``rules`` must be specified in 
     *  the config.
     */
    layerRecord: null,
    
    /** api: config[layer]
     *  ``OpenLayers.Layer.Vector``
     *  The layer that this legend will be based on.  One of ``layer``, 
     *  ``rules``, or ``layerRecord`` must be specified in the config.
     */
    layer: null,

    /** api: config[rules]
     * ``Array(OpenLayers.Rule)``
     *  List of rules.  One of ``rules``, ``layer``, or ``layerRecord`` must be 
     *  specified in the config.  The ``symbolType`` property must also be
     *  provided if only ``rules`` are given in the config.
     */
    rules: null,
    
    /** api: config[symbolType]
     *  ``String``
     *  The symbol type for legend swatches.  Must be one of ``"Point"``, 
     *  ``"Line"``, or ``"Polygon"``.  If not provided, the ``layer`` or
     *  ``layerRecord`` config property must be specified, and the geometry type
     *  of the first feature found on the layer will be used. If a rule does
     *  not have a symbolizer for ``symbolType``, we look at the symbolizers
     *  for the rule, and see if it has a ``"Point"``, ``"Line"`` or
     *  ``"Polygon"`` symbolizer, which we use for rendering a swatch of the
     *  respective geometry type. 
     */
    symbolType: null,

    /** api: config[untitledPrefix]
     *  ``String``
     *  The prefix to use as a title for rules with no title or
     *  name.  Default is ``"Untitled "``.  Prefix will be appended with a
     *  number that corresponds to the index of the rule (1 for first rule).
     */
    untitledPrefix: "Untitled ",
    
    /** api: config[clickableSymbol]
     *  ``Boolean``
     *  Set cursor style to "pointer" for symbolizers.  Register for
     *  the ``symbolclick`` event to handle clicks.  Note that click events
     *  are fired regardless of this value.  If ``false``, no cursor style will
     *  be set.  Default is ``false``.
     */
    clickableSymbol: false,
    
    /** api: config[clickableTitle]
     *  ``Boolean``
     *  Set cursor style to "pointer" for rule titles.  Register for
     *  the ``titleclick`` event to handle clicks.  Note that click events
     *  are fired regardless of this value.  If ``false``, no cursor style will
     *  be set.  Default is ``false``.
     */
    clickableTitle: false,
    
    /** api: config[selectOnClick]
     *  ``Boolean``
     *  Set to true if a rule should be selected by clicking on the
     *  symbol or title. Selection will trigger the ruleselected event, and
     *  a click on a selected rule will unselect it and trigger the
     *  ``ruleunselected`` event. Default is ``false``.
     */
    selectOnClick: false,
    
    /** api: config[enableDD]
     *  ``Boolean``
     *  Allow drag and drop of rules. Default is ``false``.
     */
    enableDD: false,
    
    /** api: config[bodyBorder]
     *  ``Boolean``
     *  Show a border around the legend panel. Default is ``false``.
     */
    bodyBorder: false,

    /** private: property[feature]
     *  ``OpenLayers.Feature.Vector``
     *  Cached feature for rendering.
     */
    feature: null,
    
    /** private: property[selectedRule]
     *  ``OpenLayers.Rule``
     *  The rule that is currently selected.
     */
    selectedRule: null,

    /** private: property[currentScaleDenominator]
     *  ``Number`` 
     *  The current scale denominator of any map associated with this
     *  legend.  Use :meth`setCurrentScaleDenominator` to change this.  If not
     *  set an entry for each rule will be rendered.  If set, only rules that
     *  apply for the given scale will be rendered.
     */
    currentScaleDenominator: null,
    
    /** private: method[initComponent]
     *  Initializes the Vector legend.
     */
    initComponent: function() {
        GeoExt.VectorLegend.superclass.initComponent.call(this);
        if (this.layerRecord) {
            this.layer = this.layerRecord.getLayer();
            if (this.layer.map) {
                this.map = this.layer.map;
                this.currentScaleDenominator = this.layer.map.getScale();
                this.layer.map.events.on({
                    "zoomend": this.onMapZoom,
                    scope: this
                });
            }
        }
        
        // determine symbol type
        if (!this.symbolType) {
            if (this.feature) {
                this.symbolType = this.symbolTypeFromFeature(this.feature);
            } else if (this.layer) {
                if (this.layer.features.length > 0) {
                    var feature;
                    for (var i=0, ii=this.layer.features.length; i<ii; i++) {
                        if (this.layer.features[i].geometry !== null) {
                            feature = this.layer.features[i].clone();
                            break;
                        }
                    }
                    feature.attributes = {};
                    this.feature = feature;
                    this.symbolType = this.symbolTypeFromFeature(this.feature);
                } else {
                    this.layer.events.on({
                        featuresadded: this.onFeaturesAdded,
                        scope: this
                    });
                }
            }
        }
        
        // set rules if not provided
        if (this.layer && this.feature && !this.rules) {
            this.setRules();
        }

        this.rulesContainer = new Ext.Container({
            autoEl: {}
        });
        
        this.add(this.rulesContainer);
        
        this.addEvents(
            /** api: event[titleclick]
             *  Fires when a rule title is clicked.
             *
             *  Listener arguments:
             *  
             *  * comp - :class:`GeoExt.VectorLegend`` This component.
             *  * rule - ``OpenLayers.Rule`` The rule whose title was clicked.
             */
            "titleclick", 

            /** api: event[symbolclick]
             *  Fires when a rule symbolizer is clicked.
             *
             *  Listener arguments:
             *  
             *  * comp - :class:`GeoExt.VectorLegend`` This component.
             *  * rule - ``OpenLayers.Rule`` The rule whose symbol was clicked.
             */
            "symbolclick",

            /** api: event[ruleclick]
             *  Fires when a rule entry is clicked (fired with symbolizer or
             *  title click).
             *
             *  Listener arguments:
             *  
             *  * comp - :class:`GeoExt.VectorLegend`` This component.
             *  * rule - ``OpenLayers.Rule`` The rule that was clicked.
             */
            "ruleclick",
            
            /** api: event[ruleselected]
             *  Fires when a rule is clicked and ``selectOnClick`` is set to 
             *  ``true``.
             * 
             *  Listener arguments:
             *  
             *  * comp - :class:`GeoExt.VectorLegend`` This component.
             *  * rule - ``OpenLayers.Rule`` The rule that was selected.
             */
            "ruleselected",
            
            /** api: event[ruleunselected]
             *  Fires when the selected rule is clicked and ``selectOnClick`` 
             *  is set to ``true``, or when a rule is unselected by selecting a
             *  different one.
             * 
             *  Listener arguments:
             *  
             *  * comp - :class:`GeoExt.VectorLegend`` This component.
             *  * rule - ``OpenLayers.Rule`` The rule that was unselected.
             */
            "ruleunselected",
            
            /** api: event[rulemoved]
             *  Fires when a rule is moved.
             * 
             *  Listener arguments:
             *  
             *  * comp - :class:`GeoExt.VectorLegend`` This component.
             *  * rule - ``OpenLayers.Rule`` The rule that was moved.
             */
            "rulemoved"
        ); 
        
        this.update();
    },
    
    /** private: method[onMapZoom]
     *  Listener for map zoomend.
     */
    onMapZoom: function() {
        this.setCurrentScaleDenominator(
            this.layer.map.getScale()
        );
    },
    
    /** private: method[symbolTypeFromFeature]
     *  :arg feature:  ``OpenLayers.Feature.Vector``
     *
     *  Determine the symbol type given a feature.
     */
    symbolTypeFromFeature: function(feature) {
        var match = feature.geometry.CLASS_NAME.match(/Point|Line|Polygon/);
        return (match && match[0]) || "Point";
    },
    
    /** private: method[onFeaturesAdded]
     *  Set as a one time listener for the ``featuresadded`` event on the layer
     *  if it was provided with no features originally.
     */
    onFeaturesAdded: function(evt) {
        for (var i=0, ii=evt.features.length; i<ii; ++i) {
            if (evt.features[i].geometry !== null) {
                this.layer.events.un({
                    featuresadded: this.onFeaturesAdded,
                    scope: this
                });
                var feature = evt.features[i].clone();
                feature.attributes = {};
                this.feature = feature;
                this.symbolType = this.symbolTypeFromFeature(this.feature);
                if (!this.rules) {
                    this.setRules();
                }
                this.update();
                break;
            }
        }
    },
    
    /** private: method[setRules]
     *  Sets the ``rules`` property for this.  This is called when the component
     *  is constructed without rules.  Rules will be derived from the layer's 
     *  style map if it has one.
     */
    setRules: function() {
        var style = this.layer.styleMap && this.layer.styleMap.styles["default"];
        if (!style) {
            style = new OpenLayers.Style();
        }
        if (style.rules.length === 0) {
            this.rules = [
                new OpenLayers.Rule({
                    title: style.title,
                    symbolizer: style.createSymbolizer(this.feature)
                })
            ];
        } else {
            this.rules = style.rules;                
        }
    },
    
    /** api: method[setCurrentScaleDenominator]
     *  :arg scale: ``Number`` The scale denominator.
     *
     *  Set the current scale denominator.  This will hide entries for any
     *  rules that don't apply at the current scale.
     */
    setCurrentScaleDenominator: function(scale) {
        if (scale !== this.currentScaleDenominator) {
            this.currentScaleDenominator = scale;
            this.update();
        }
    },

    /** private: method[getRuleEntry]
     *  :arg rule: ``OpenLayers.Rule``
     *  :returns: ``Ext.Container``
     *
     *  Get the item corresponding to the rule.
     */
    getRuleEntry: function(rule) {
        return this.rulesContainer.items.get(this.rules.indexOf(rule));
    },

    /** private: method[addRuleEntry]
     *  :arg rule: ``OpenLayers.Rule``
     *  :arg noDoLayout: ``Boolean``  Don't call doLayout after adding rule.
     *      Default is ``false``.
     *
     *  Add a new rule entry in the rules container. This
     *  method does not add the rule to the rules array.
     */
    addRuleEntry: function(rule, noDoLayout) {
        this.rulesContainer.add(this.createRuleEntry(rule));
        if (!noDoLayout) {
            this.doLayout();
        }
    },

    /** private: method[removeRuleEntry]
     *  :arg rule: ``OpenLayers.Rule``
     *  :arg noDoLayout: ``Boolean``  Don't call doLayout after removing rule.
     *      Default is ``false``.
     *
     *  Remove a rule entry from the rules container, this
     *  method assumes the rule is in the rules array, and
     *  it does not remove the rule from the rules array.
     */
    removeRuleEntry: function(rule, noDoLayout) {
        var ruleEntry = this.getRuleEntry(rule);
        if (ruleEntry) {
            this.rulesContainer.remove(ruleEntry);
            if (!noDoLayout) {
                this.doLayout();
            }
        }
    },
    
    /** private: method[selectRuleEntry]
     */
    selectRuleEntry: function(rule) {
        var newSelection = rule != this.selectedRule;
        if (this.selectedRule) {
            this.unselect();
        }
        if (newSelection) {
            var ruleEntry = this.getRuleEntry(rule);
            ruleEntry.body.addClass("x-grid3-row-selected");
            this.selectedRule = rule;
            this.fireEvent("ruleselected", this, rule);
        }
    },
    
    /** private: method[unselect]
     */
    unselect: function() {
        this.rulesContainer.items.each(function(item, i) {
            if (this.rules[i] == this.selectedRule) {
                item.body.removeClass("x-grid3-row-selected");
                this.selectedRule = null;
                this.fireEvent("ruleunselected", this, this.rules[i]);
            }
        }, this);
    },

    /** private: method[createRuleEntry]
     */
    createRuleEntry: function(rule) {
        var applies = true;
        if (this.currentScaleDenominator != null) {
            if (rule.minScaleDenominator) {
                applies = applies && (this.currentScaleDenominator >= rule.minScaleDenominator);
            }
            if (rule.maxScaleDenominator) {
                applies = applies && (this.currentScaleDenominator < rule.maxScaleDenominator);
            }
        }
        return {
            xtype: "panel",
            layout: "column",
            border: false,
            hidden: !applies,
            bodyStyle: this.selectOnClick ? {cursor: "pointer"} : undefined,
            defaults: {
                border: false
            },
            items: [
                this.createRuleRenderer(rule),
                this.createRuleTitle(rule)
            ],
            listeners: {
                render: function(comp){
                    this.selectOnClick && comp.getEl().on({
                        click: function(comp){
                            this.selectRuleEntry(rule);
                        },
                        scope: this
                    });
                    if (this.enableDD == true) {
                        this.addDD(comp);
                    }
                },
                scope: this
            }
        };
    },

    /** private: method[createRuleRenderer]
     *  :arg rule: ``OpenLayers.Rule``
     *  :returns: ``GeoExt.FeatureRenderer``
     *
     *  Create a renderer for the rule.
     */
    createRuleRenderer: function(rule) {
        var types = [this.symbolType, "Point", "Line", "Polygon"];
        var type, haveType, i, len, ii;
        var symbolizers = rule.symbolizers;
        if (!symbolizers) {
            // TODO: remove this when OpenLayers.Symbolizer is used everywhere
            var symbolizer = rule.symbolizer;
            for (i=0, len=types.length; i<len; ++i) {
                type = types[i];
                if (symbolizer[type]) {
                    symbolizer = symbolizer[type];
                    haveType = true;
                    break;
                }
            }
            symbolizers = [symbolizer];
        } else {
            var Type;
            outer: for (i=0, ii=types.length; i<ii; ++i) {
                type = types[i];
                Type = OpenLayers.Symbolizer[type];
                if (Type) {
                    for (var j=0, jj=symbolizers.length; j<jj; ++j) {
                        if (symbolizers[j] instanceof Type) {
                            haveType = true;
                            break outer;
                        }
                    }
                }
            }
        }
        return {
            xtype: "gx_renderer",
            labelText: "Ab",
            symbolType: haveType ? type : this.symbolType,
            symbolizers: symbolizers,
            style: this.clickableSymbol ? {cursor: "pointer"} : undefined,
            listeners: {
                click: function() {
                    if (this.clickableSymbol) {
                        this.fireEvent("symbolclick", this, rule);
                        this.fireEvent("ruleclick", this, rule);
                    }
                },
                scope: this
            }
        };
    },

    /** private: method[createRuleTitle]
     *  :arg rule: ``OpenLayers.Rule``
     *  :returns: ``Ext.Component``
     *
     *  Create a title component for the rule.
     */
    createRuleTitle: function(rule) {
        return {
            cls: "x-form-item",
            style: "padding: 0.2em 0.5em 0;", // TODO: css
            bodyStyle: Ext.applyIf({background: "transparent"}, 
                this.clickableTitle ? {cursor: "pointer"} : undefined),
            html: this.getRuleTitle(rule),
            listeners: {
                render: function(comp) {
                    this.clickableTitle && comp.getEl().on({
                        click: function() {
                            this.fireEvent("titleclick", this, rule);
                            this.fireEvent("ruleclick", this, rule);
                        },
                        scope: this
                    });
                },
                scope: this
            }
        };
    },
    
    /** private: method[addDD]
     *  :arg component: ``Ext.Component``
     *
     *  Adds drag & drop functionality to a rule entry.
     */
    addDD: function(component) {
        var ct = component.ownerCt;
        var panel = this;
        new Ext.dd.DragSource(component.getEl(), {
            ddGroup: ct.id,
            onDragOut: function(e, targetId) {
                var target = Ext.getCmp(targetId);
                target.removeClass("gx-ruledrag-insert-above");
                target.removeClass("gx-ruledrag-insert-below");
                return Ext.dd.DragZone.prototype.onDragOut.apply(this, arguments);
            },
            onDragEnter: function(e, targetId) {
                var target = Ext.getCmp(targetId);
                var cls;
                var sourcePos = ct.items.indexOf(component);
                var targetPos = ct.items.indexOf(target);
                if (sourcePos > targetPos) {
                    cls = "gx-ruledrag-insert-above";
                } else if (sourcePos < targetPos) {
                    cls = "gx-ruledrag-insert-below";
                }                
                cls && target.addClass(cls);
                return Ext.dd.DragZone.prototype.onDragEnter.apply(this, arguments);
            },
            onDragDrop: function(e, targetId) {
                panel.moveRule(ct.items.indexOf(component),
                    ct.items.indexOf(Ext.getCmp(targetId)));
                return Ext.dd.DragZone.prototype.onDragDrop.apply(this, arguments);
            },
            getDragData: function(e) {
                var sourceEl = e.getTarget(".x-column-inner");
                if(sourceEl) {
                    var d = sourceEl.cloneNode(true);
                    d.id = Ext.id();
                    return {
                        sourceEl: sourceEl,
                        repairXY: Ext.fly(sourceEl).getXY(),
                        ddel: d
                    };
                }
            }
        });
        new Ext.dd.DropTarget(component.getEl(), {
            ddGroup: ct.id,
            notifyDrop: function() {
                return true;
            }
        });
    },
    
    /** api: method[update]
     *  Update rule titles and symbolizers.
     */
    update: function() {
        GeoExt.VectorLegend.superclass.update.apply(this, arguments);
        var i, ii;
        if (this.symbolType && this.rules) {
            if (this.rulesContainer.items) {
                var comp;
                for (i=this.rulesContainer.items.length-1; i>=0; --i) {
                    comp = this.rulesContainer.getComponent(i);
                    this.rulesContainer.remove(comp, true);
                }
            }
            for (i=0, ii=this.rules.length; i<ii; ++i) {
                this.addRuleEntry(this.rules[i], true);
            }
            this.doLayout();
            // make sure that the selected rule is still selected after update
            if (this.selectedRule) {
                this.getRuleEntry(this.selectedRule).body.addClass("x-grid3-row-selected");
            }
        }
    },

    /** private: method[updateRuleEntry]
     *  :arg rule: ``OpenLayers.Rule``
     *
     *  Update the renderer and the title of a rule.
     */
    updateRuleEntry: function(rule) {
        var ruleEntry = this.getRuleEntry(rule);
        if (ruleEntry) {
            ruleEntry.removeAll();
            ruleEntry.add(this.createRuleRenderer(rule));
            ruleEntry.add(this.createRuleTitle(rule));
            ruleEntry.doLayout();
        }
    },
    
    /** private: method[moveRule]
     */
    moveRule: function(sourcePos, targetPos) {
        var srcRule = this.rules[sourcePos];
        this.rules.splice(sourcePos, 1);
        this.rules.splice(targetPos, 0, srcRule);
        this.update();
        this.fireEvent("rulemoved", this, srcRule);
    },
    
    /** private: method[getRuleTitle]
     *  :returns: ``String``
     *
     *  Get a rule title given a rule.
     */
    getRuleTitle: function(rule) {
        var title = rule.title || rule.name || "";
        if (!title && this.untitledPrefix) {
            title = this.untitledPrefix + (this.rules.indexOf(rule) + 1);
        }
        return title;
    },

    /** private: method[beforeDestroy]
     *  Override.
     */
    beforeDestroy: function() {
        if (this.layer) {
            if (this.layer.events) {
                this.layer.events.un({
                    featuresadded: this.onFeaturesAdded,
                    scope: this
                });
            }
            if (this.layer.map && this.layer.map.events) {
                this.layer.map.events.un({
                    "zoomend": this.onMapZoom,
                    scope: this
                });
            }
        }
        delete this.layer;
        delete this.map;
        delete this.rules;
        GeoExt.VectorLegend.superclass.beforeDestroy.apply(this, arguments);
    },

    /** private: method[onStoreRemove]
     *  Handler for remove event of the layerStore
     *
     *  :param store: ``Ext.data.Store`` The store from which the record was
     *      removed.
     *  :param record: ``Ext.data.Record`` The record object corresponding
     *      to the removed layer.
     *  :param index: ``Integer`` The index in the store.
     */
    onStoreRemove: function(store, record, index) {
        if (record.getLayer() === this.layer) {
            if (this.map && this.map.events) {
                this.map.events.un({
                    "zoomend": this.onMapZoom,
                    scope: this
                });
            }
        }
    },

    /** private: method[onStoreAdd]
     *  Handler for add event of the layerStore
     *
     *  :param store: ``Ext.data.Store`` The store to which the record was
     *      added.
     *  :param records: Array(``Ext.data.Record``) The record object(s) corresponding
     *      to the added layer(s).
     *  :param index: ``Integer`` The index in the store at which the record
     *      was added.
     */
    onStoreAdd: function(store, records, index) {
        for (var i=0, len=records.length; i<len; i++) {
            var record = records[i];
            if (record.getLayer() === this.layer) {
                if (this.layer.map && this.layer.map.events) {
                    this.layer.map.events.on({
                        "zoomend": this.onMapZoom,
                        scope: this
});
                }
            }
        }
    }

});

/** private: method[supports]
 *  Private override
 */
GeoExt.VectorLegend.supports = function(layerRecord) {
    return layerRecord.getLayer() instanceof OpenLayers.Layer.Vector ? 1 : 0;
};

/** api: legendtype = gx_vectorlegend */
GeoExt.LayerLegend.types["gx_vectorlegend"] = GeoExt.VectorLegend;

/** api: xtype = gx_vectorlegend */
Ext.reg("gx_vectorlegend", GeoExt.VectorLegend); 

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/MapPanel.js
 * @include GeoExt/widgets/LayerLegend.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = LegendPanel
 *  base_link = `Ext.Panel <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Panel>`_
 */

Ext.namespace('GeoExt');

/** api: constructor
 *  .. class:: LegendPanel(config)
 *
 *  A panel showing legends of all layers in a layer store.
 *  Depending on the layer type, a legend renderer will be chosen.
 *
 *  The LegendPanel will include legends for all the layers in the
 *  ``layerStore`` it is configured with, unless the layer is configured with
 *  ``displayInLayerSwitcher: false``, or a layer record has a
 *  ``hideInLegend`` field with a value of ``true``. Additional filtering can
 *  be done by configuring a ``filter`` on the LegendPanel.
 */
GeoExt.LegendPanel = Ext.extend(Ext.Panel, {

    /** api: config[dynamic]
     *  ``Boolean``
     *  If false the LegendPanel will not listen to the add, remove and change 
     *  events of the LayerStore. So it will load with the initial state of
     *  the LayerStore and not change anymore. 
     */
    dynamic: true,
    
    /** api: config[layerStore]
     *  ``GeoExt.data.LayerStore``
     *  The layer store containing layers to be displayed in the legend 
     *  container. If not provided it will be taken from the MapPanel.
     */
    layerStore: null,
    
    /** api: config[preferredTypes]
     *  ``Array(String)`` An array of preferred legend types.
     */
    
    /** private: property[preferredTypes]
     */
    preferredTypes: null,

    /** api: config[filter]
     *  ``Function``
     *  A function, called in the scope of the legend panel, with a layer record
     *  as argument. Is expected to return true for layers to be displayed, false
     *  otherwise. By default, all layers will be displayed.
     *
     *  .. code-block:: javascript
     *
     *      filter: function(record) {
     *          return record.getLayer().isBaseLayer;
     *      }
     */
    filter: function(record) {
        return true;
    },

    /** private: method[onRender]
     *  Private method called when the legend panel is being rendered.
     */
    onRender: function() {
        GeoExt.LegendPanel.superclass.onRender.apply(this, arguments);
        if(!this.layerStore) {
            this.layerStore = GeoExt.MapPanel.guess().layers;
        }
        this.layerStore.each(function(record) {
                this.addLegend(record);
            }, this);
        if (this.dynamic) {
            this.layerStore.on({
                "add": this.onStoreAdd,
                "remove": this.onStoreRemove,
                "clear": this.onStoreClear,
                scope: this
            });
        }
    },

    /** private: method[recordIndexToPanelIndex]
     *  Private method to get the panel index for a layer represented by a
     *  record.
     *
     *  :param index ``Integer`` The index of the record in the store.
     *
     *  :return: ``Integer`` The index of the sub panel in this panel.
     */
    recordIndexToPanelIndex: function(index) {
        var store = this.layerStore;
        var count = store.getCount();
        var panelIndex = -1;
        var legendCount = this.items ? this.items.length : 0;
        var record, layer;
        for(var i=count-1; i>=0; --i) {
            record = store.getAt(i);
            layer = record.getLayer();
            var types = GeoExt.LayerLegend.getTypes(record);
            if(layer.displayInLayerSwitcher && types.length > 0 &&
                (store.getAt(i).get("hideInLegend") !== true)) {
                    ++panelIndex;
                    if(index === i || panelIndex > legendCount-1) {
                        break;
                    }
            }
        }
        return panelIndex;
    },
    
    /** private: method[getIdForLayer]
     *  :arg layer: ``OpenLayers.Layer``
     *  :returns: ``String``
     *
     *  Generate an element id that is unique to this panel/layer combo.
     */
    getIdForLayer: function(layer) {
        return this.id + "-" + layer.id;
    },

    /** private: method[onStoreAdd]
     *  Private method called when a layer is added to the store.
     *
     *  :param store: ``Ext.data.Store`` The store to which the record(s) was 
     *      added.
     *  :param record: ``Ext.data.Record`` The record object(s) corresponding
     *      to the added layers.
     *  :param index: ``Integer`` The index of the inserted record.
     */
    onStoreAdd: function(store, records, index) {
        var panelIndex = this.recordIndexToPanelIndex(index+records.length-1);
        for (var i=0, len=records.length; i<len; i++) {
            this.addLegend(records[i], panelIndex);
        }
        this.doLayout();
    },

    /** private: method[onStoreRemove]
     *  Private method called when a layer is removed from the store.
     *
     *  :param store: ``Ext.data.Store`` The store from which the record(s) was
     *      removed.
     *  :param record: ``Ext.data.Record`` The record object(s) corresponding
     *      to the removed layers.
     *  :param index: ``Integer`` The index of the removed record.
     */
    onStoreRemove: function(store, record, index) {
        this.removeLegend(record);            
    },

    /** private: method[removeLegend]
     *  Remove the legend of a layer.
     *  :param record: ``Ext.data.Record`` The record object from the layer 
     *      store to remove.
     */
    removeLegend: function(record) {
        if (this.items) {
            var legend = this.getComponent(this.getIdForLayer(record.getLayer()));
            if (legend) {
                this.remove(legend, true);
                this.doLayout();
            }
        }
    },

    /** private: method[onStoreClear]
     *  Private method called when a layer store is cleared.
     *
     *  :param store: ``Ext.data.Store`` The store from which was cleared.
     */
    onStoreClear: function(store) {
        this.removeAllLegends();
    },

    /** private: method[removeAllLegends]
     *  Remove all legends from this legend panel.
     */
    removeAllLegends: function() {
        this.removeAll(true);
        this.doLayout();
    },

    /** private: method[addLegend]
     *  Add a legend for the layer.
     *
     *  :param record: ``Ext.data.Record`` The record object from the layer 
     *      store.
     *  :param index: ``Integer`` The position at which to add the legend.
     */
    addLegend: function(record, index) {
        if (this.filter(record) === true) {
            var layer = record.getLayer();
            index = index || 0;
            var legend;
            var types = GeoExt.LayerLegend.getTypes(record,
                this.preferredTypes);
            if(layer.displayInLayerSwitcher && !record.get('hideInLegend') &&
                types.length > 0) {
                this.insert(index, {
                    xtype: types[0],
                    id: this.getIdForLayer(layer),
                    layerRecord: record,
                    hidden: !((!layer.map && layer.visibility) ||
                        (layer.getVisibility() && layer.calculateInRange()))
                });
            }
        }
    },

    /** private: method[onDestroy]
     *  Private method called during the destroy sequence.
     */
    onDestroy: function() {
        if(this.layerStore) {
            this.layerStore.un("add", this.onStoreAdd, this);
            this.layerStore.un("remove", this.onStoreRemove, this);
            this.layerStore.un("clear", this.onStoreClear, this);
        }
        GeoExt.LegendPanel.superclass.onDestroy.apply(this, arguments);
    }
});

/** api: xtype = gx_legendpanel */
Ext.reg('gx_legendpanel', GeoExt.LegendPanel);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/tips/ZoomSliderTip.js
 * @require OpenLayers/Util.js
 */

/** api: (define)
 *  module = GeoExt
 *  class = ZoomSlider
 *  base_link = `Ext.slider.SingleSlider <http://dev.sencha.com/deploy/dev/docs/?class=Ext.slider.SingleSlider>`_
 */
Ext.namespace("GeoExt");

/** api: example
 *  Sample code to render a slider outside the map viewport:
 * 
 *  .. code-block:: javascript
 *     
 *      var slider = new GeoExt.ZoomSlider({
 *          renderTo: document.body,
 *          width: 200,
 *          map: map
 *      });
 *     
 *  Sample code to add a slider to a map panel:
 * 
 *  .. code-block:: javascript
 * 
 *      var panel = new GeoExt.MapPanel({
 *          renderTo: document.body,
 *          height: 300,
 *          width: 400,
 *          map: {
 *              controls: [new OpenLayers.Control.Navigation()]
 *          },
 *          layers: [new OpenLayers.Layer.WMS(
 *              "Global Imagery",
 *              "http://maps.opengeo.org/geowebcache/service/wms",
 *              {layers: "bluemarble"}
 *          )],
 *          extent: [-5, 35, 15, 55],
 *          items: [{
 *              xtype: "gx_zoomslider",
 *              aggressive: true,
 *              vertical: true,
 *              height: 100,
 *              x: 10,
 *              y: 20
 *          }]
 *      });
 */

/** api: constructor
 *  .. class:: ZoomSlider(config)
 *   
 *      Create a slider for controlling a map's zoom level.
 */
GeoExt.ZoomSlider = Ext.extend(Ext.slider.SingleSlider, {
    
    /** api: config[map]
     *  ``OpenLayers.Map`` or :class:`GeoExt.MapPanel`
     *  The map that the slider controls.
     */
    map: null,
    
    /** api: config[baseCls]
     *  ``String``
     *  The CSS class name for the slider elements.  Default is "gx-zoomslider".
     */
    baseCls: "gx-zoomslider",

    /** api: config[aggressive]
     *  ``Boolean``
     *  If set to true, the map is zoomed as soon as the thumb is moved. Otherwise 
     *  the map is zoomed when the thumb is released (default).
     */
    aggressive: false,
    
    /** private: property[updating]
     *  ``Boolean``
     *  The slider position is being updated by itself (based on map zoomend).
     */
    updating: false,
    
    /** private: method[initComponent]
     *  Initialize the component.
     */
    initComponent: function() {
        GeoExt.ZoomSlider.superclass.initComponent.call(this);
        
        if(this.map) {
            if(this.map instanceof GeoExt.MapPanel) {
                this.map = this.map.map;
            }
            this.bind(this.map);
        }

        if (this.aggressive === true) {
            this.on('change', this.changeHandler, this);
        } else {
            this.on('changecomplete', this.changeHandler, this);
        }
        this.on("beforedestroy", this.unbind, this);        
    },
    
    /** private: method[onRender]
     *  Override onRender to set base css class.
     */
    onRender: function() {
        GeoExt.ZoomSlider.superclass.onRender.apply(this, arguments);
        this.el.addClass(this.baseCls);
    },

    /** private: method[afterRender]
     *  Override afterRender because the render event is fired too early
     *  to call update.
     */
    afterRender : function(){
        Ext.slider.SingleSlider.superclass.afterRender.apply(this, arguments);
        this.update();
    },
    
    /** private: method[addToMapPanel]
     *  :param panel: :class:`GeoExt.MapPanel`
     *  
     *  Called by a MapPanel if this component is one of the items in the panel.
     */
    addToMapPanel: function(panel) {
        this.on({
            render: function() {
                var el = this.getEl();
                el.setStyle({
                    position: "absolute",
                    zIndex: panel.map.Z_INDEX_BASE.Control
                });
                el.on({
                    mousedown: this.stopMouseEvents,
                    click: this.stopMouseEvents
                });
            },
            afterrender: function() {
                this.bind(panel.map);
            },
            scope: this
        });
    },
    
    /** private: method[stopMouseEvents]
     *  :param e: ``Object``
     */
    stopMouseEvents: function(e) {
        e.stopEvent();
    },
    
    /** private: method[removeFromMapPanel]
     *  :param panel: :class:`GeoExt.MapPanel`
     *  
     *  Called by a MapPanel if this component is one of the items in the panel.
     */
    removeFromMapPanel: function(panel) {
        var el = this.getEl();
        el.un("mousedown", this.stopMouseEvents, this);
        el.un("click", this.stopMouseEvents, this);
        this.unbind();
    },
    
    /** private: method[bind]
     *  :param map: ``OpenLayers.Map``
     */
    bind: function(map) {
        this.map = map;
        this.map.events.on({
            zoomend: this.onZoomEnd,
            updatesize: this.initZoomValues,
            changebaselayer: this.initZoomValues,
            scope: this
        });
        this.initZoomValues();
    },
    
    /** private: method[onZoomEnd]
     *  Registered as a listener for zoomend.
     */
    onZoomEnd: function() {
        this.update();
    },
    
    /** private: method[unbind]
     */
    unbind: function() {
        if(this.map && this.map.events) {
            this.map.events.un({
                zoomend: this.onZoomEnd,
                updatesize: this.initZoomValues,
                changebaselayer: this.initZoomValues,
                scope: this
            });
        }
    },
    
    /** private: method[initZoomValues]
     *  Set the min/max values for the slider if not set in the config.
     */
    initZoomValues: function() {
        var layer = this.map.baseLayer;
        if (layer) {
            if(this.initialConfig.minValue === undefined) {
                //TODO remove check for getMinZoom when we require OpenLayers 2.12.
                var minZoom = this.map.getMinZoom ? this.map.getMinZoom() : 0;
                this.minValue = Math.max(minZoom, layer.minZoomLevel || 0);
            }
            if(this.initialConfig.maxValue === undefined) {
                this.maxValue = layer.minZoomLevel == null ?
                    layer.numZoomLevels - 1 : layer.maxZoomLevel;
            }
            // reset the thumb value so it gets repositioned when we call update
            this.update(true);
        }
    },
    
    /** api: method[getZoom]
     *  :return: ``Number`` The map zoom level.
     *  
     *  Get the zoom level for the associated map based on the slider value.
     */
    getZoom: function() {
        return this.getValue();
    },
    
    /** api: method[getScale]
     *  :return: ``Number`` The map scale denominator.
     *  
     *  Get the scale denominator for the associated map based on the slider value.
     */
    getScale: function() {
        return OpenLayers.Util.getScaleFromResolution(
            this.map.getResolutionForZoom(this.getValue()),
            this.map.getUnits()
        );
    },
    
    /** api: method[getResolution]
     *  :return: ``Number`` The map resolution.
     *  
     *  Get the resolution for the associated map based on the slider value.
     */
    getResolution: function() {
        return this.map.getResolutionForZoom(this.getValue());
    },
    
    /** private: method[changeHandler]
     *  Registered as a listener for slider changecomplete.  Zooms the map.
     */
    changeHandler: function() {
        if(this.map && !this.updating) {
            this.map.zoomTo(this.getValue());
        }
    },
    
    /** private: method[update]
     *  :param force: ``Boolean`` Force an update of the thumb position even
     *      if the value may not have changed (but min/max or length have).
     *
     *  Registered as a listener for map zoomend.  Updates the value of the slider.
     */
    update: function(force) {
        if(this.rendered && this.map) {
            this.updating = true;
            if (force) {
                /**
                 * This triggers repositioning even if the value doesn't 
                 * change.  We want this when the min/max values change but 
                 * the zoom level doesn't.
                 */
                this.thumbs[0].value = null;
            }
            this.setValue(this.map.getZoom());
            this.updating = false;
        }
    }

});

/** api: xtype = gx_zoomslider */
Ext.reg('gx_zoomslider', GeoExt.ZoomSlider);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Control/SelectFeature.js
 * @require OpenLayers/Layer/Vector.js
 * @require OpenLayers/BaseTypes/Class.js
 */

/** api: (define)
 *  module = GeoExt.grid
 *  class = FeatureSelectionModel
 *  base_link = `Ext.grid.RowSelectionModel <http://dev.sencha.com/deploy/dev/docs/?class=Ext.grid.RowSelectionModel>`_
 */

Ext.namespace('GeoExt.grid');

/** api: constructor
 *  .. class:: FeatureSelectionModel
 *
 *      A row selection model which enables automatic selection of features
 *      in the map when rows are selected in the grid and vice-versa.
 */

/** api: example
 *  Sample code to create a feature grid with a feature selection model:
 *  
 *  .. code-block:: javascript
 *
 *       var gridPanel = new Ext.grid.GridPanel({
 *          title: "Feature Grid",
 *          region: "east",
 *          store: store,
 *          width: 320,
 *          columns: [{
 *              header: "Name",
 *              width: 200,
 *              dataIndex: "name"
 *          }, {
 *              header: "Elevation",
 *              width: 100,
 *              dataIndex: "elevation"
 *          }],
 *          sm: new GeoExt.grid.FeatureSelectionModel() 
 *      });
 */

GeoExt.grid.FeatureSelectionModelMixin = function() {
    return {
        /** api: config[autoActivateControl]
         *  ``Boolean`` If true the select feature control is activated and
         *  deactivated when binding and unbinding. Defaults to true.
         */
        autoActivateControl: true,

        /** api: config[layerFromStore]
         *  ``Boolean`` If true, and if the constructor is passed neither a
         *  layer nor a select feature control, a select feature control is
         *  created using the layer found in the grid's store. Set it to
         *  false if you want to manually bind the selection model to a
         *  layer. Defaults to true.
         */
        layerFromStore: true,

        /** api: config[selectControl]
         *
         *  ``OpenLayers.Control.SelectFeature`` A select feature control. If not
         *  provided one will be created.  If provided any "layer" config option
         *  will be ignored, and its "multiple" option will be used to configure
         *  the selectionModel.  If an ``Object`` is provided here, it will be
         *  passed as config to the SelectFeature constructor, and the "layer"
         *  config option will be used for the layer.
         */

        /** private: property[selectControl] 
         *  ``OpenLayers.Control.SelectFeature`` The select feature control 
         *  instance. 
         */ 
        selectControl: null, 
        
        /** api: config[layer]
         *  ``OpenLayers.Layer.Vector`` The vector layer used for the creation of
         *  the select feature control, it must already be added to the map. If not
         *  provided, the layer bound to the grid's store, if any, will be used.
         */

        /** private: property[bound]
         *  ``Boolean`` Flag indicating if the selection model is bound.
         */
        bound: false,
        
        /** private: property[superclass]
         *  ``Ext.grid.AbstractSelectionModel`` Our superclass.
         */
        superclass: null,
        
        /** private: property[selectedFeatures]
         *  ``Array`` An array to store the selected features.
         */
        selectedFeatures: [],
        
        /** api: config[autoPanMapOnSelection]
         *  ``Boolean`` If true the map will recenter on feature selection
         *  so that the selected features are visible. Defaults to false.
         */
        autoPanMapOnSelection: false,

        /** private */
        constructor: function(config) {
            config = config || {};
            if(config.selectControl instanceof OpenLayers.Control.SelectFeature) { 
                if(!config.singleSelect) {
                    var ctrl = config.selectControl;
                    config.singleSelect = !(ctrl.multiple || !!ctrl.multipleKey);
                }
            } else if(config.layer instanceof OpenLayers.Layer.Vector) {
                this.selectControl = this.createSelectControl(
                    config.layer, config.selectControl
                );
                delete config.layer;
                delete config.selectControl;
            }
            if (config.autoPanMapOnSelection) {
                this.autoPanMapOnSelection = true;
                delete config.autoPanMapOnSelection;
            }
            this.superclass = arguments.callee.superclass;
            this.superclass.constructor.call(this, config);
        },
        
        /** private: method[initEvents]
         *
         *  Called after this.grid is defined
         */
        initEvents: function() {
            this.superclass.initEvents.call(this);
            if(this.layerFromStore) {
                var layer = this.grid.getStore() && this.grid.getStore().layer;
                if(layer &&
                   !(this.selectControl instanceof OpenLayers.Control.SelectFeature)) {
                    this.selectControl = this.createSelectControl(
                        layer, this.selectControl
                    );
                }
            }
            if(this.selectControl) {
                this.bind(this.selectControl);
            }
        },

        /** private: createSelectControl
         *  :param layer: ``OpenLayers.Layer.Vector`` The vector layer.
         *  :param config: ``Object`` The select feature control config.
         *
         *  Create the select feature control.
         */
        createSelectControl: function(layer, config) {
            config = config || {};
            var singleSelect = config.singleSelect !== undefined ?
                               config.singleSelect : this.singleSelect;
            config = OpenLayers.Util.extend({
                toggle: true,
                multipleKey: singleSelect ? null :
                    (Ext.isMac ? "metaKey" : "ctrlKey")
            }, config);
            var selectControl = new OpenLayers.Control.SelectFeature(
                layer, config
            );
            layer.map.addControl(selectControl);
            return selectControl;
        },
        
        /** api: method[bind]
         *
         *  :param obj: ``OpenLayers.Layer.Vector`` or
         *      ``OpenLayers.Control.SelectFeature`` The object this selection model
         *      should be bound to, either a vector layer or a select feature
         *      control.
         *  :param options: ``Object`` An object with a "controlConfig"
         *      property referencing the configuration object to pass to the
         *      ``OpenLayers.Control.SelectFeature`` constructor.
         *  :return: ``OpenLayers.Control.SelectFeature`` The select feature
         *      control this selection model uses.
         *
         *  Bind the selection model to a layer or a SelectFeature control.
         */
        bind: function(obj, options) {
            if(!this.bound) {
                options = options || {};
                this.selectControl = obj;
                if(obj instanceof OpenLayers.Layer.Vector) {
                    this.selectControl = this.createSelectControl(
                        obj, options.controlConfig
                    );
                }
                if(this.autoActivateControl) {
                    this.selectControl.activate();
                }
                var layers = this.getLayers();
                for(var i = 0, len = layers.length; i < len; i++) {
                    layers[i].events.on({
                        featureselected: this.featureSelected,
                        featureunselected: this.featureUnselected,
                        scope: this
                    });
                }
                this.on("rowselect", this.rowSelected, this);
                this.on("rowdeselect", this.rowDeselected, this);
                this.bound = true;
            }
            return this.selectControl;
        },
        
        /** api: method[unbind]
         *  :return: ``OpenLayers.Control.SelectFeature`` The select feature
         *      control this selection model used.
         *
         *  Unbind the selection model from the layer or SelectFeature control.
         */
        unbind: function() {
            var selectControl = this.selectControl;
            if(this.bound) {
                var layers = this.getLayers();
                for(var i = 0, len = layers.length; i < len; i++) {
                    layers[i].events.un({
                        featureselected: this.featureSelected,
                        featureunselected: this.featureUnselected,
                        scope: this
                    });
                }
                this.un("rowselect", this.rowSelected, this);
                this.un("rowdeselect", this.rowDeselected, this);
                if(this.autoActivateControl) {
                    selectControl.deactivate();
                }
                this.selectControl = null;
                this.bound = false;
            }
            return selectControl;
        },
        
        /** private: method[featureSelected]
         *  :param evt: ``Object`` An object with a feature property referencing
         *                         the selected feature.
         */
        featureSelected: function(evt) {
            if(!this._selecting) {
                var store = this.grid.store;
                var row = store.findBy(function(record, id) {
                    return record.getFeature() == evt.feature;
                });
                if(row != -1 && !this.isSelected(row)) {
                    this._selecting = true;
                    this.selectRow(row, !this.singleSelect);
                    this._selecting = false;
                    // focus the row in the grid to ensure it is visible
                    this.grid.getView().focusRow(row);
                }
            }
        },
        
        /** private: method[featureUnselected]
         *  :param evt: ``Object`` An object with a feature property referencing
         *                         the unselected feature.
         */
        featureUnselected: function(evt) {
            if(!this._selecting) {
                var store = this.grid.store;
                var row = store.findBy(function(record, id) {
                    return record.getFeature() == evt.feature;
                });
                if(row != -1 && this.isSelected(row)) {
                    this._selecting = true;
                    this.deselectRow(row); 
                    this._selecting = false;
                    this.grid.getView().focusRow(row);
                }
            }
        },
        
        /** private: method[rowSelected]
         *  :param model: ``Ext.grid.RowSelectModel`` The row select model.
         *  :param row: ``Integer`` The row index.
         *  :param record: ``Ext.data.Record`` The record.
         */
        rowSelected: function(model, row, record) {
            var feature = record.getFeature();
            if(!this._selecting && feature) {
                var layers = this.getLayers();
                for(var i = 0, len = layers.length; i < len; i++) {
                    if(layers[i].selectedFeatures.indexOf(feature) == -1) {
                        this._selecting = true;
                        this.selectControl.select(feature);
                        this._selecting = false;
                        this.selectedFeatures.push(feature);
                        break;
                    }
                }
                if(this.autoPanMapOnSelection) {
                    this.recenterToSelectionExtent();
                }
             }
        },
        
        /** private: method[rowDeselected]
         *  :param model: ``Ext.grid.RowSelectModel`` The row select model.
         *  :param row: ``Integer`` The row index.
         *  :param record: ``Ext.data.Record`` The record.
         */
        rowDeselected: function(model, row, record) {
            var feature = record.getFeature();
            if(!this._selecting && feature) {
                var layers = this.getLayers();
                for(var i = 0, len = layers.length; i < len; i++) {
                    if(layers[i].selectedFeatures.indexOf(feature) != -1) {
                        this._selecting = true;
                        this.selectControl.unselect(feature);
                        this._selecting = false;
                        OpenLayers.Util.removeItem(this.selectedFeatures, feature);
                        break;
                    }
                }
                if(this.autoPanMapOnSelection && this.selectedFeatures.length > 0) {
                    this.recenterToSelectionExtent();
                }
            }
        },

        /** private: method[getLayers]
         *  Return the layers attached to the select feature control.
         */
        getLayers: function() {
            return this.selectControl.layers || [this.selectControl.layer];
        },
        
        /**
         * private: method[recenterToSelectionExtent]
         * centers the map in order to display all
         * selected features
         */
        recenterToSelectionExtent: function() {
            var map = this.selectControl.map;
            var selectionExtent = this.getSelectionExtent();
            var selectionExtentZoom = map.getZoomForExtent(selectionExtent, false);
            if(selectionExtentZoom > map.getZoom()) {
                map.setCenter(selectionExtent.getCenterLonLat());
            }
            else {
                map.zoomToExtent(selectionExtent);
            }
        },
        
        /** api: method[getSelectionExtent]
         *  :return: ``OpenLayers.Bounds`` or null if the layer has no features with
         *      geometries
         *
         *  Calculates the max extent which includes all selected features.
         */
        getSelectionExtent: function () {
            var maxExtent = null;
            var features = this.selectedFeatures;
            if(features && (features.length > 0)) {
                var geometry = null;
                for(var i=0, len=features.length; i<len; i++) {
                    geometry = features[i].geometry;
                    if (geometry) {
                        if (maxExtent === null) {
                            maxExtent = new OpenLayers.Bounds();
                        }
                        maxExtent.extend(geometry.getBounds());
                    }
                }
            }
            return maxExtent;
        }
    };
};

GeoExt.grid.FeatureSelectionModel = Ext.extend(
    Ext.grid.RowSelectionModel,
    new GeoExt.grid.FeatureSelectionModelMixin
);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Feature/Vector.js
 * @require OpenLayers/Geometry/Polygon.js
 * @require OpenLayers/Util.js
 * @require OpenLayers/BaseTypes/Bounds.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = PrintPage
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */
Ext.namespace("GeoExt.data");

/** api: constructor
 *  .. class:: PrintPage
 * 
 *  Provides a representation of a print page for
 *  :class:`GeoExt.data.PrintProvider`. The extent of the page is stored as
 *  ``OpenLayers.Feature.Vector``. Widgets can use this to display the print
 *  extent on the map.
 */
GeoExt.data.PrintPage = Ext.extend(Ext.util.Observable, {
    
    /** api:config[printProvider]
     * :class:`GeoExt.data.PrintProvider` The print provider to use with
     * this page.
     */
    
    /** private: property[printProvider]
     *  :class:`GeoExt.data.PrintProvider`
     */
    printProvider: null,
    
    /** api: property[feature]
     *  ``OpenLayers.Feature.Vector`` Feature representing the page extent. To
     *  get the extent of the print page for a specific map, use
     *  ``getPrintExtent``.
     *  Read-only.
     */
    feature: null,
    
    /** api: property[center]
     *  ``OpenLayers.LonLat`` The current center of the page. Read-only.
     */
    center: null,
    
    /** api: property[scale]
     *  ``Ext.data.Record`` The current scale record of the page. Read-only.
     */
    scale: null,
    
    /** api: property[rotation]
     *  ``Float`` The current rotation of the page. Read-only.
     */
    rotation: 0,
    
    /** api:config[customParams]
     *  ``Object`` Key-value pairs of additional parameters that the
     *  printProvider will send to the print service for this page.
     */

    /** api: property[customParams]
     *  ``Object`` Key-value pairs of additional parameters that the
     *  printProvider will send to the print service for this page.
     */
    customParams: null,
    
    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(config) {
        this.initialConfig = config;
        Ext.apply(this, config);
        
        if(!this.customParams) {
            this.customParams = {};
        }
        
        this.addEvents(
            /** api: event[change]
             *  Triggered when any of the page properties have changed
             *  
             *  Listener arguments:
             *
             *  * printPage - :class:`GeoExt.data.PrintPage` this printPage
             *  * modifications - ``Object`` Object with one or more of
             *      ``scale``, ``center`` and ``rotation``, notifying
             *      listeners of the changed properties.
             */
            "change"
        );

        GeoExt.data.PrintPage.superclass.constructor.apply(this, arguments);

        this.feature = new OpenLayers.Feature.Vector(
            new OpenLayers.Geometry.Polygon([
                new OpenLayers.Geometry.LinearRing([
                    new OpenLayers.Geometry.Point(-1, -1),
                    new OpenLayers.Geometry.Point(1, -1),
                    new OpenLayers.Geometry.Point(1, 1),
                    new OpenLayers.Geometry.Point(-1, 1)
                ])
            ])
        );

        if(this.printProvider.capabilities) {
            this.setScale(this.printProvider.scales.getAt(0));
        } else {
            this.printProvider.on({
                "loadcapabilities": function() {
                    this.setScale(this.printProvider.scales.getAt(0));
                },
                scope: this,
                single: true
            });
        }

        this.printProvider.on({
            "layoutchange": this.onLayoutChange,
            scope: this
        });
    },
    
    /** api: method[getPrintExtent]
     *  :param map: ``OpenLayers.Map`` or :class:`GeoExt.MapPanel` the map to
     *      get the print extent for. 
     *  :returns: ``OpenLayers.Bounds``
     *
     *  Gets this page's print extent for the provided map.
     */
    getPrintExtent: function(map) {
        map = map instanceof GeoExt.MapPanel ? map.map : map;
        return this.calculatePageBounds(this.scale, map.getUnits());
    },

    /** api: method[setScale]
     *  :param scale: ``Ext.data.Record`` The new scale record.
     *  :param units: ``String`` map units to use for the scale calculation.
     *      Optional if the ``feature`` is on a layer which is added to a map.
     *      If not found, "dd" will be assumed.
     * 
     *  Updates the page geometry to match a given scale. Since this takes the
     *  current layout of the printProvider into account, this can be used to
     *  update the page geometry feature when the layout has changed.
     */
    setScale: function(scale, units) {
        var bounds = this.calculatePageBounds(scale, units);
        var geom = bounds.toGeometry();
        var rotation = this.rotation;
        if(rotation != 0) {
            geom.rotate(-rotation, geom.getCentroid());
        }
        this.updateFeature(geom, {scale: scale});
    },
    
    /** api: method[setCenter]
     *  :param center: ``OpenLayers.LonLat`` The new center.
     * 
     *  Moves the page extent to a new center.
     */
    setCenter: function(center) {
        var geom = this.feature.geometry;
        var oldCenter = geom.getBounds().getCenterLonLat();
        var dx = center.lon - oldCenter.lon;
        var dy = center.lat - oldCenter.lat;
        geom.move(dx, dy);
        this.updateFeature(geom, {center: center});
    },
    
    /** api: method[setRotation]
     *  :param rotation: ``Float`` The new rotation.
     *  :param force: ``Boolean`` If set to true, the rotation will also be
     *      set when the layout does not support it. Default is false.
     *  
     *  Sets a new rotation for the page geometry.
     */
    setRotation: function(rotation, force) {
        if(force || this.printProvider.layout.get("rotation") === true) {
            var geom = this.feature.geometry;
            geom.rotate(this.rotation - rotation, geom.getCentroid());
            this.updateFeature(geom, {rotation: rotation});
        }
    },
    
    /** api: method[fit]
     *  :param fitTo: :class:`GeoExt.MapPanel` or ``OpenLayers.Map`` or ``OpenLayers.Feature.Vector``
     *      The map or feature to fit the page to.
     *  :param options: ``Object`` Additional options to determine how to fit
     *
     *  Fits the page layout to a map or feature extent. If the map extent has
     *  not been centered yet, this will do nothing.
     * 
     *  Available options:
     *
     *  * mode - ``String`` How to calculate the print extent? If "closest",
     *    the closest matching print extent will be chosen. If "printer", the
     *    chosen print extent will be the closest one that can show the entire
     *    ``fitTo`` extent on the printer. If "screen", it will be the closest
     *    one that is entirely visible inside the ``fitTo`` extent. Default is
     *    "printer".
     * 
     */
    fit: function(fitTo, options) {
        options = options || {};
        var map = fitTo, extent;
        if(fitTo instanceof GeoExt.MapPanel) {
            map = fitTo.map;
        } else if(fitTo instanceof OpenLayers.Feature.Vector) {
            map = fitTo.layer.map;
            extent = fitTo.geometry.getBounds();
        }
        if(!extent) {
            extent = map.getExtent();
            if(!extent) {
                return;
            }
        }
        this._updating = true;
        var center = extent.getCenterLonLat();
        this.setCenter(center);
        var units = map.getUnits();
        var scale = this.printProvider.scales.getAt(0);
        var closest = Number.POSITIVE_INFINITY;
        var mapWidth = extent.getWidth();
        var mapHeight = extent.getHeight();
        this.printProvider.scales.each(function(rec) {
            var bounds = this.calculatePageBounds(rec, units);
            if (options.mode == "closest") {
                var diff = 
                    Math.abs(bounds.getWidth() - mapWidth) +
                    Math.abs(bounds.getHeight() - mapHeight);
                if (diff < closest) {
                    closest = diff;
                    scale = rec;
                }
            } else {
                var contains = options.mode == "screen" ?
                    !extent.containsBounds(bounds) :
                    bounds.containsBounds(extent);
                if (contains || (options.mode == "screen" && !contains)) {
                    scale = rec;
                }
                return contains;
            }
        }, this);
        this.setScale(scale, units);
        delete this._updating;
        this.updateFeature(this.feature.geometry, {
            center: center,
            scale: scale
        });
    },

    /** private: method[updateFeature]
     *  :param geometry: ``OpenLayers.Geometry`` New geometry for the feature.
     *      If not provided, the existing geometry will be left unchanged.
     *  :param mods: ``Object`` An object with one or more of ``scale``,
     *      ``center`` and ``rotation``, reflecting the page properties to
     *      update.
     *      
     *  Updates the page feature with a new geometry and notifies listeners
     *  of changed page properties.
     */
    updateFeature: function(geometry, mods) {
        var f = this.feature;
        var modified = f.geometry !== geometry;
        geometry.id = f.geometry.id;
        f.geometry = geometry;
        
        if(!this._updating) {
            for(var property in mods) {
                if(mods[property] === this[property]) {
                    delete mods[property];
                } else {
                    this[property] = mods[property];
                    modified = true;
                }
            }
            Ext.apply(this, mods);
            
            f.layer && f.layer.drawFeature(f);
            modified && this.fireEvent("change", this, mods);
        }
    },    
    
    /** private: method[calculatePageBounds]
     *  :param scale: ``Ext.data.Record`` Scale record to calculate the page
     *      bounds for.
     *  :param units: ``String`` Map units to use for the scale calculation.
     *      Optional if ``feature`` is added to a layer which is added to a
     *      map. If not provided, "dd" will be assumed.
     *  :return: ``OpenLayers.Bounds``
     *  
     *  Calculates the page bounds for a given scale.
     */
    calculatePageBounds: function(scale, units) {
        var s = scale.get("value");
        var f = this.feature;
        var geom = this.feature.geometry;
        var center = geom.getBounds().getCenterLonLat();

        var size = this.printProvider.layout.get("size");
        var units = units ||
            (f.layer && f.layer.map && f.layer.map.getUnits()) ||
            "dd";
        var unitsRatio = OpenLayers.INCHES_PER_UNIT[units];
        var w = size.width / 72 / unitsRatio * s / 2;
        var h = size.height / 72 / unitsRatio * s / 2;
        
        return new OpenLayers.Bounds(center.lon - w, center.lat - h,
            center.lon + w, center.lat + h);
    },
    
    /** private: method[onLayoutChange]
     *  Handler for the printProvider's layoutchange event.
     */
    onLayoutChange: function() {
        if(this.printProvider.layout.get("rotation") === false) {
            this.setRotation(0, true);
        }
        // at init time the print provider triggers layoutchange
        // before loadcapabilities, i.e. before we set this.scale
        // to the first scale in the scales store, we need to
        // guard against that
        this.scale && this.setScale(this.scale);
    },
    
    /** private: method[destroy]
     */
    destroy: function() {
        this.printProvider.un("layoutchange", this.onLayoutChange, this);
    }

});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */
Ext.namespace("GeoExt.plugins");

/** api: (define)
 *  module = GeoExt.plugins
 *  class = PrintPageField
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */

/** api: example
 *  A form with a combo box for the scale and text fields for rotation and a
 *  page title. The page title is a custom parameter of the print module's
 *  page configuration:
 * 
 *  .. code-block:: javascript
 *     
 *      var printPage = new GeoExt.data.PrintPage({
 *          printProvider: new GeoExt.data.PrintProvider({
 *              capabilities: printCapabilities
 *          })
 *      });
 *      new Ext.form.FormPanel({
 *          renderTo: "form",
 *          width: 200,
 *          height: 300,
 *          items: [{
 *              xtype: "combo",
 *              displayField: "name",
 *              store: printPage.scales, // printPage.scale
 *              name: "scale",
 *              fieldLabel: "Scale",
 *              typeAhead: true,
 *              mode: "local",
 *              forceSelection: true,
 *              triggerAction: "all",
 *              selectOnFocus: true,
 *              plugins: new GeoExt.plugins.PrintPageField({
 *                  printPage: printPage
 *              })
 *          }, {
 *              xtype: "textfield",
 *              name: "rotation", // printPage.rotation
 *              fieldLabel: "Rotation",
 *              plugins: new GeoExt.plugins.PrintPageField({
 *                  printPage: printPage
 *              })
 *          }, {
 *              xtype: "textfield",
 *              name: "mapTitle", // printPage.customParams["mapTitle"]
 *              fieldLabel: "Map Title",
 *              plugins: new GeoExt.plugins.PrintPageField({
 *                  printPage: printPage
 *              })
 *          }]
 *      });
 */

/** api: constructor
 *  .. class:: PrintPageField
 * 
 *  A plugin for ``Ext.form.Field`` components which provides synchronization
 *  with a :class:`GeoExt.data.PrintPage`. The field name has to match the
 *  respective property of the printPage (e.g. ``scale``, ``rotation``).
 */
GeoExt.plugins.PrintPageField = Ext.extend(Ext.util.Observable, {
    
    /** api: config[printPage]
     *  ``GeoExt.data.PrintPage`` The print page to synchronize with.
     */

    /** private: property[printPage]
     *  ``GeoExt.data.PrintPage`` The print page to synchronize with.
     *  Read-only.
     */
    printPage: null,
    
    /** private: property[target]
     *  ``Ext.form.Field`` This plugin's target field.
     */
    target: null,
    
    /** private: method[constructor]
     */
    constructor: function(config) {
        this.initialConfig = config;
        Ext.apply(this, config);
        
        GeoExt.plugins.PrintPageField.superclass.constructor.apply(this, arguments);
    },
    
    /** private: method[init]
     *  :param target: ``Ext.form.Field`` The component that this plugin
     *      extends.
     * @param {Object} target
     */
    init: function(target) {
        this.target = target;
        var onCfg = {
            "beforedestroy": this.onBeforeDestroy,
            scope: this
        };
        var eventName = target instanceof Ext.form.ComboBox ?
                            "select" : target instanceof Ext.form.Checkbox ?
                                "check" : "valid";
        onCfg[eventName] = this.onFieldChange;
        target.on(onCfg);
        this.printPage.on({
            "change": this.onPageChange,
            scope: this
        });
        this.printPage.printProvider.on({
            "layoutchange": this.onLayoutChange,
            scope: this
        });
        this.setValue(this.printPage);
    },

    /** private: method[onFieldChange]
     *  :param field: ``Ext.form.Field``
     *  :param record: ``Ext.data.Record`` Optional.
     *  
     *  Handler for the target field's "valid" or "select" event.
     */
    onFieldChange: function(field, record) {
        var printProvider = this.printPage.printProvider;
        var value = field.getValue();
        this._updating = true;
        if(field.store === printProvider.scales || field.name === "scale") {
            this.printPage.setScale(record);
        } else if(field.name == "rotation") {
            !isNaN(value) && this.printPage.setRotation(value);
        } else {
            this.printPage.customParams[field.name] = value;
        }
        delete this._updating;
    },

    /** private: method[onPageChange]
     *  :param printPage: :class:`GeoExt.data.PrintPage`
     *  
     *  Handler for the "change" event for the page this plugin is configured
     *  with.
     */
    onPageChange: function(printPage) {
        if(!this._updating) {
            this.setValue(printPage);
        }
    },
    
    /** private: method[onPageChange]
     *  :param printProvider: :class:`GeoExt.data.PrintProvider`
     *  :param layout: ``Ext.Record``
     *  
     *  Handler for the "layoutchange" event of the printProvider.
     */
    onLayoutChange: function(printProvider, layout) {
        var t = this.target;
        t.name == "rotation" && t.setDisabled(!layout.get("rotation"));
    },

    /** private: method[setValue]
     *  :param printPage: :class:`GeoExt.data.PrintPage`
     *
     *  Sets the value in the target field.
     */
    setValue: function(printPage) {
        var t = this.target;
        t.suspendEvents();
        if(t.store === printPage.printProvider.scales || t.name === "scale") {
            if(printPage.scale) {
                t.setValue(printPage.scale.get(t.displayField));
            }
        } else if(t.name == "rotation") {
            t.setValue(printPage.rotation);
        }
        t.resumeEvents();
    },

    /** private: method[onBeforeDestroy]
     */
    onBeforeDestroy: function() {
        this.target.un("beforedestroy", this.onBeforeDestroy, this);
        this.target.un("select", this.onFieldChange, this);
        this.target.un("valid", this.onFieldChange, this);
        this.printPage.un("change", this.onPageChange, this);
        this.printPage.printProvider.un("layoutchange", this.onLayoutChange,
            this);
    }

});

/** api: ptype = gx_printpagefield */
Ext.preg("gx_printpagefield", GeoExt.plugins.PrintPageField);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */
Ext.namespace("GeoExt.plugins");

/** api: (define)
 *  module = GeoExt.plugins
 *  class = PrintProviderField
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */

/** api: example
 *  A form with combo boxes for layout and resolution, and a text field for a
 *  map title. The latter is a custom parameter to the print module, which is
 *  a default for all print pages. For setting custom parameters on the page
 *  level, use :class:`GeoExt.plugins.PrintPageField`):
 * 
 *  .. code-block:: javascript
 *     
 *      var printProvider = new GeoExt.data.PrintProvider({
 *          capabilities: printCapabilities
 *      });
 *      new Ext.form.FormPanel({
 *          renderTo: "form",
 *          width: 200,
 *          height: 300,
 *          items: [{
 *              xtype: "combo",
 *              displayField: "name",
 *              store: printProvider.layouts, // printProvider.layout
 *              fieldLabel: "Layout",
 *              typeAhead: true,
 *              mode: "local",
 *              forceSelection: true,
 *              triggerAction: "all",
 *              selectOnFocus: true,
 *              plugins: new GeoExt.plugins.PrintProviderField({
 *                  printProvider: printProvider
 *              })
 *          }, {
 *              xtype: "combo",
 *              displayField: "name",
 *              store: printProvider.dpis, // printProvider.dpi
 *              fieldLabel: "Resolution",
 *              typeAhead: true,
 *              mode: "local",
 *              forceSelection: true,
 *              triggerAction: "all",
 *              selectOnFocus: true,
 *              plugins: new GeoExt.plugins.PrintProviderField({
 *                  printProvider: printProvider
 *              })
 *          }, {
 *              xtype: "textfield",
 *              name: "mapTitle", // printProvider.customParams.mapTitle
 *              fieldLabel: "Map Title",
 *              plugins: new GeoExt.plugins.PrintProviderField({
 *                  printProvider: printProvider
 *              })
 *          }]
 *      }):
 */

/** api: constructor
 *  .. class:: PrintProviderField
 * 
 *  A plugin for ``Ext.form.Field`` components which provides synchronization
 *  with a :class:`GeoExt.data.PrintProvider`.
 */
GeoExt.plugins.PrintProviderField = Ext.extend(Ext.util.Observable, {
    
    /** api: config[printProvider]
     *  ``GeoExt.data.PrintProvider`` The print provider to use with this
     *  plugin's field. Not required if set on the owner container of the
     *  field.
     */
    
    /** private: property[target]
     *  ``Ext.form.Field`` This plugin's target field.
     */
    target: null,
    
    /** private: method[constructor]
     */
    constructor: function(config) {
        this.initialConfig = config;
        Ext.apply(this, config);
        
        GeoExt.plugins.PrintProviderField.superclass.constructor.apply(this, arguments);
    },
    
    /** private: method[init]
     *  :param target: ``Ext.form.Field`` The component that this plugin
     *      extends.
     */
    init: function(target) {
        this.target = target;
        var onCfg = {
            scope: this,
            "render": this.onRender,
            "beforedestroy": this.onBeforeDestroy
        };
        onCfg[target instanceof Ext.form.ComboBox ? "select" : "valid"] =
            this.onFieldChange;
        target.on(onCfg);
    },
    
    /** private: method[onRender]
     *  :param field: ``Ext.Form.Field``
     *  
     *  Handler for the target field's "render" event.
     */
    onRender: function(field) {
        var printProvider = this.printProvider || field.ownerCt.printProvider;
        if(field.store === printProvider.layouts) {
            field.setValue(printProvider.layout.get(field.displayField));
            printProvider.on({
                "layoutchange": this.onProviderChange,
                scope: this
            });
        } else if(field.store === printProvider.dpis) {
            field.setValue(printProvider.dpi.get(field.displayField));
            printProvider.on({
                "dpichange": this.onProviderChange,
                scope: this
            });
        } else if(field.initialConfig.value === undefined) {
            field.setValue(printProvider.customParams[field.name]);
        }
    },
    
    /** private: method[onFieldChange]
     *  :param field: ``Ext.form.Field``
     *  :param record: ``Ext.data.Record`` Optional.
     *  
     *  Handler for the target field's "valid" or "select" event.
     */
    onFieldChange: function(field, record) {
        var printProvider = this.printProvider || field.ownerCt.printProvider;
        var value = field.getValue();
        this._updating = true;
        if(record) {
            switch(field.store) {
                case printProvider.layouts:
                    printProvider.setLayout(record);
                    break;
                case printProvider.dpis:
                    printProvider.setDpi(record);
            }
        } else {
            printProvider.customParams[field.name] = value;
        }
        delete this._updating;
    },
    
    /** private: method[onProviderChange]
     *  :param printProvider: :class:`GeoExt.data.PrintProvider`
     *  :param rec: ``Ext.data.Record``
     *  
     *  Handler for the printProvider's dpichange and layoutchange event
     */
    onProviderChange: function(printProvider, rec) {
        if(!this._updating) {
            this.target.setValue(rec.get(this.target.displayField));
        }
    },
    
    /** private: method[onBeforeDestroy]
     */
    onBeforeDestroy: function() {
        var target = this.target;
        target.un("beforedestroy", this.onBeforeDestroy, this);
        target.un("render", this.onRender, this);
        target.un("select", this.onFieldChange, this);
        target.un("valid", this.onFieldChange, this);
        var printProvider = this.printProvider || target.ownerCt.printProvider;
        printProvider.un("layoutchange", this.onProviderChange, this);
        printProvider.un("dpichange", this.onProviderChange, this);
    }

});

/** api: ptype = gx_printproviderfield */
Ext.preg("gx_printproviderfield", GeoExt.plugins.PrintProviderField);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Layer/Vector.js
 * @require OpenLayers/Control/TransformFeature.js
 * @require OpenLayers/BaseTypes/LonLat.js
 */

/** api: (define)
 *  module = GeoExt.plugins
 *  class = PrintExtent
 */
Ext.namespace("GeoExt.plugins");

/** api: example
 *  Sample code to create a MapPanel with a PrintExtent, and print it
 *  immediately:
 * 
 *  .. code-block:: javascript
 *
 *      var printExtent = new GeoExt.plugins.PrintExtent({
 *          printProvider: new GeoExt.data.PrintProvider({
 *              capabilities: printCapabilities
 *          })
 *      });
 *     
 *      var mapPanel = new GeoExt.MapPanel({
 *          border: false,
 *          renderTo: "div-id",
 *          layers: [new OpenLayers.Layer.WMS("Tasmania", "http://demo.opengeo.org/geoserver/wms",
 *              {layers: "topp:tasmania_state_boundaries"}, {singleTile: true})],
 *          center: [146.56, -41.56],
 *          zoom: 6,
 *          plugins: printExtent
 *      });
 *
 *      printExtent.addPage();
 *
 *      // print the map
 *      printExtent.print();
 */

/** api: constructor
 *  .. class:: PrintExtent
 * 
 *  Provides a way to show and modify the extents of print pages on the map. It
 *  uses a layer to render the page extent and handle features of print pages,
 *  and provides a control to modify them. Must be set as a plugin to a
 *  :class:`GeoExt.MapPanel`.
 */
GeoExt.plugins.PrintExtent = Ext.extend(Ext.util.Observable, {

    /** private: initialConfig
     *  ``Object`` Holds the initial config object passed to the
     *  constructor.
     */
    initialConfig: null,

    /** api: config[printProvider]
     *  :class:`GeoExt.data.PrintProvider` The print provider this form
     *  is connected to. Optional if pages are provided.
     */
    /** api: property[printProvider]
     *  :class:`GeoExt.data.PrintProvider` The print provider this form
     *  is connected to. Read-only.
     */
    printProvider: null,
    
    /** private: property[map]
     *  ``OpenLayers.Map`` The map the layer and control are added to.
     */
    map: null,
    
    /** api: config[layer]
     *  ``OpenLayers.Layer.Vector`` The layer used to render extent and handle
     *  features to. Optional, will be created if not provided.
     */
    /** private: property[layer]
     *  ``OpenLayers.Layer.Vector`` The layer used to render extent and handle
     *  features to.
     */
    layer: null,
    
    /** api: config[transformFeatureOptions]
     *  ``Object`` Optional options for the`OpenLayers.Control.TransformFeature` 
     *  control.
     */
    transformFeatureOptions: null,

    /** private: property[control]
     *  ``OpenLayers.Control.TransformFeature`` The control used to change
     *      extent, center, rotation and scale.
     */
    control: null,
    
    /** api: config[pages]
     *  Array of :class:`GeoExt.data.PrintPage` The pages that this plugin
     *  controls. Optional. If not provided, it will be created with one page
     *  that is completely contained within the visible map extent.
     *  
     *  .. note:: All pages must use the same PrintProvider.
     */
    /** api: property[pages]
     *  Array of :class:`GeoExt.data.PrintPage` The pages that this component
     *  controls. Read-only.
     */
    pages: null,

    /** api: property[page]
     *  :class:`GeoExt.data.PrintPage` The page currently set for
     *  transformation.
     */
    page: null,

    /** private: method[constructor]
     *  Private constructor override.
     */
    constructor: function(config) {
        config = config || {};

        Ext.apply(this, config);
        this.initialConfig = config;

        if(!this.printProvider) {
            this.printProvider = this.pages[0].printProvider;
        }

        if(!this.pages) {
            this.pages = [];
        }
        
        this.addEvents(
            /** api: event[selectpage]
             *  Triggered when a page has been selected using the control
             *  
             *  Listener arguments:
             *  * printPage - :class:`GeoExt.data.PrintPage` this printPage
             */
            "selectpage"
        );

        GeoExt.plugins.PrintExtent.superclass.constructor.apply(this, arguments);
    },

    /** api: method[print]
     *  :param options: ``Object`` Options to send to the PrintProvider's
     *      print method. See :class:`GeoExt.data.PrintProvider` :: ``print``.
     *  
     *  Prints all pages as shown on the map.
     */
    print: function(options) {
        this.printProvider.print(this.map, this.pages, options);
    },

    /** private: method[init]
     *  :param mapPanel: class:`GeoExt.MapPanel`
     *  
     *  Initializes the plugin.
     */
    init: function(mapPanel) {
        this.map = mapPanel.map;
        mapPanel.on("destroy", this.onMapPanelDestroy, this);

        if (!this.layer) {
            this.layer = new OpenLayers.Layer.Vector(null, {
                displayInLayerSwitcher: false
            });
        }
        this.createControl();

        for(var i=0, len=this.pages.length; i<len; ++i) {
            this.addPage(this.pages[i]);
        }
        this.show();
    },

    /** api: method[addPage]
     *  :param page: :class:`GeoExt.data.PrintPage` The page to add
     *       to this plugin. If not provided, a page that fits the current
     *       extent is created.
     *  :return: page :class:``GeoExt.data.PrintPage``
     *  
     *  Adds a page to the list of pages that this plugin controls.
     */
    addPage: function(page) {
        page = page || new GeoExt.data.PrintPage({
            printProvider: this.printProvider
        });
        if(this.pages.indexOf(page) === -1) {
            this.pages.push(page);
        }
        this.layer.addFeatures([page.feature]);
        page.on("change", this.onPageChange, this);

        this.page = page;
        var map = this.map;
        if(map.getCenter()) {
            this.fitPage();
        } else {
            map.events.register("moveend", this, function() {
                map.events.unregister("moveend", this, arguments.callee);
                this.fitPage();
            });
        }
        return page;
    },

    /** api: method[removePage]
     *  :param page: :class:`GeoExt.data.PrintPage` The page to remove
     *       from this plugin.
     *       
     *  Removes a page from the list of pages that this plugin controls.
     */
    removePage: function(page) {
        this.pages.remove(page);
        if (page.feature.layer) {
            this.layer.removeFeatures([page.feature]);
        }
        page.un("change", this.onPageChange, this);
    },
    
    /** api: method[selectPage]
     *  :param page: :class:`GeoExt.data.PrintPage` The page to select
     *  
     *  Selects the given page (ie. calls the setFeature on the modify feature
     *  control)
     */
    selectPage: function(page) {
        this.control.active && this.control.setFeature(page.feature);
        // FIXME raise the feature up so that it is on top
    },

    /** api: method[show]
     * 
     *  Sets up the plugin, initializing the ``OpenLayers.Layer.Vector``
     *  layer and ``OpenLayers.Control.TransformFeature``, and centering
     *  the first page if no pages were specified in the configuration.
     */
    show: function() {
        this.map.addLayer(this.layer);
        this.map.addControl(this.control);
        this.control.activate();

        // if we have a page and if the map has a center then update the
        // transform box for that page, in case the transform control
        // was deactivated when fitPage (and therefore onPageChange)
        // was called.
        if (this.page && this.map.getCenter()) {
            this.updateBox();
        }
    },

    /** api: method[hide]
     * 
     *  Tear downs the plugin, removing the
     *  ``OpenLayers.Control.TransformFeature`` control and
     *  the ``OpenLayers.Layer.Vector`` layer.
     */
    hide: function() {
        // note: we need to be extra cautious when destroying OpenLayers
        // objects here (the tests will fail if we're not cautious anyway).
        // We use obj.events to test whether an OpenLayers object is
        // destroyed or not.

        var map = this.map, layer = this.layer, control = this.control;

        if(control && control.events) {
            control.deactivate();
            if(map && map.events && control.map) {
                map.removeControl(control);
            }
        }

        if(map && map.events && layer && layer.map) {
            map.removeLayer(layer);
        }
    },

    /** private: method[onMapPanelDestroy]
     */
    onMapPanelDestroy: function() {

        var map = this.map;

        for(var len = this.pages.length - 1, i = len; i>=0; i--) {
            this.removePage(this.pages[i]);
        }

        this.hide();

        var control = this.control;
        if(map && map.events &&
           control && control.events) {
            control.destroy();
        }

        var layer = this.layer;
        if(!this.initialConfig.layer &&
           map && map.events &&
           layer && layer.events) {
            layer.destroy();
        }

        delete this.layer;
        delete this.control;
        delete this.page;
        this.map = null;
    },
    
    /** private: method[createControl]
     */
    createControl: function() {
        this.control = new OpenLayers.Control.TransformFeature(this.layer, Ext.applyIf({
            preserveAspectRatio: true,
            eventListeners: {
                "beforesetfeature": function(e) {
                    for(var i=0, len=this.pages.length; i<len; ++i) {
                        if(this.pages[i].feature === e.feature) {
                            this.page = this.pages[i];
                            e.object.rotation = -this.pages[i].rotation;
                            break;
                        }
                    }
                },
                "setfeature": function(e) {
                    for(var i=0, len=this.pages.length; i<len; ++i) {
                        if(this.pages[i].feature === e.feature) {
                            this.fireEvent("selectpage", this.pages[i]);
                            break;
                        }
                    }
                },
                "beforetransform": function(e) {
                    this._updating = true;
                    var page = this.page;
                    if(e.rotation) {
                        if(this.printProvider.layout.get("rotation")) {
                            page.setRotation(-e.object.rotation);
                        } else {
                            e.object.setFeature(page.feature);
                        }
                    } else if(e.center) {
                        page.setCenter(OpenLayers.LonLat.fromString(
                            e.center.toShortString()
                        ));
                    } else {
                        page.fit(e.object.box, {mode: "closest"});
                        var minScale = this.printProvider.scales.getAt(0);
                        var maxScale = this.printProvider.scales.getAt(
                            this.printProvider.scales.getCount() - 1);
                        var boxBounds = e.object.box.geometry.getBounds();
                        var pageBounds = page.feature.geometry.getBounds();
                        var tooLarge = page.scale === minScale &&
                            boxBounds.containsBounds(pageBounds);
                        var tooSmall = page.scale === maxScale &&
                            pageBounds.containsBounds(boxBounds);
                        if(tooLarge === true || tooSmall === true) {
                            this.updateBox();
                        }
                    }
                    delete this._updating;
                    return false;
                },
                "transformcomplete": this.updateBox,
                scope: this
            }
        }, this.transformFeatureOptions));
    },

    /** private: method[fitPage]
     *  Fits the current print page to the map.
     */
    fitPage: function() {
        if(this.page) {
            this.page.fit(this.map, {mode: "screen"});
        }
    },

    /** private: method[updateBox]
     *  Updates the transformation box after setting a new scale or
     *  layout, or to fit the box to the extent feature after a tranform.
     */
    updateBox: function() {
        var page = this.page;
        this.control.active &&
            this.control.setFeature(page.feature, {rotation: -page.rotation});
    },

    /** private: method[onPageChange]
     *  Handler for a page's change event.
     */
    onPageChange: function(page, mods) {
        if(!this._updating) {
            this.control.active &&
                this.control.setFeature(page.feature, {rotation: -page.rotation});
        }
    }
});

/** api: ptype = gx_printextent */
Ext.preg("gx_printextent", GeoExt.plugins.PrintExtent);

/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @include GeoExt/widgets/form.js
 */

Ext.namespace("GeoExt.plugins");

/** api: (define)
 *  module = GeoExt.plugins
 *  class = AttributeForm
 */

/** api: example
 *  Sample code showing how to use an Ext form panel as a feature
 *  attribute form (for editing features for example).
 *
 *  .. code-block:: javascript
 *
 *      var formPanel = new Ext.form.FormPanel({
 *          autoScroll: true,
 *          height: 300,
 *          width: 350,
 *          defaults: {
 *              maxLengthText: "too long",
 *              minLengthText: "too short"
 *          }
 *          plugins: [
 *              new GeoExt.plugins.AttributeForm({
 *                  attributeStore: new GeoExt.data.AttributeStore({
 *                      url: "http://some.wfs",
 *                      baseParams: {
 *                          "SERVICE": "WFS",
 *                          "VERSION": "1.1.0",
 *                          "REQUEST": "DescribeFeatureType",
 *                          "TYPENAME": "the_typename"
 *                      }
 *                  })
 *              })
 *          ]
 *      });
 */

/** api: constructor
 *  .. class:: AttributeForm
 *
 *  This plugin allows creating form items from attribute records
 *  and fill a form panel with these items.
 */

GeoExt.plugins.AttributeForm = function(config) {
    Ext.apply(this, config);
};

GeoExt.plugins.AttributeForm.prototype = {

    /** api: config[attributeStore]
     *  ``Ext.data.Store`` The attribute store to bind to this plugin.
     *  It can be any Ext store configured with a
     *  :class:`GeoExt.data.AttributeReader`. If set form items
     *  will be created from the attribute records in the form. In
     *  most cases this store will be a :class:`GeoExt.data.AttributeStore`.
     */
    /** private: property[attributeStore]
     *  ``Ext.data.Store`` The attribute store.
     */
    attributeStore: null,

    /** private: property[formPanel]
     *  ``Ext.form.FormPanel`` This form panel.
     */
    formPanel: null,
    
    /** api: config[recordToFieldOptions]
     *  ``Object`` Options to pass on to :meth:`GeoExt.form.recordToField`.
     */

    /** private: method[init]
     *  :param formPanel: class:`Ext.form.FormPanel`
     *
     *  Initializes the plugin.
     */
    init: function(formPanel) {
        this.formPanel = formPanel;
        if(this.attributeStore instanceof Ext.data.Store) {
            this.fillForm();
            this.bind(this.attributeStore);
        }
        formPanel.on("destroy", this.onFormDestroy, this);
    },

    /** private: method[bind]
     *  :param store: ``Ext.data.Store`` The attribute store this form panel
     *  is to be bound to.
     *
     *  Bind the panel to the attribute store passed as a parameter.
     */
    bind: function(store) {
        this.unbind();
        store.on({
            "load": this.onLoad,
            scope: this
        });
        this.attributeStore = store;
    },

    /** private: method[unbind]
     *
     *  Unbind the panel from the attribute store it is currently bound
     *  to, if any.
     */
    unbind: function() {
        if(this.attributeStore) {
            this.attributeStore.un("load", this.onLoad, this);
        }
    },

    /** private: method[onLoad]
     *
     *  Callback called when the store is loaded.
     */
    onLoad: function() {
        if(this.formPanel.items) {
            this.formPanel.removeAll();
        }
        this.fillForm();
    },

    /** private: method[fillForm]
     *
     *  For each attribute record in the attribute store create
     *  a form field and add it to the form.
     */
    fillForm: function() {
        this.attributeStore.each(function(record) {
            var field = GeoExt.form.recordToField(record, Ext.apply({
                checkboxLabelProperty: 'fieldLabel'
            }, this.recordToFieldOptions || {}));
            if(field) {
                this.formPanel.add(field);
            }
        }, this);
        this.formPanel.doLayout();
    },

    /** private: method[onFormDestroy]
     */
    onFormDestroy: function() {
        this.unbind();
    }
};

/** api: ptype = gx_attributeform */
Ext.preg("gx_attributeform", GeoExt.plugins.AttributeForm);

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/widgets/MapPanel.js
 * @include GeoExt/data/PrintProvider.js
 * @include GeoExt/data/PrintPage.js
 */
Ext.namespace("GeoExt");

/** api: (define)
 *  module = GeoExt
 *  class = PrintMapPanel
 */

/** api: (extends)
 * GeoExt/widgets/MapPanel.js
 */

/** api: example
 *  A map with a "Print..." button. If clicked, a dialog containing a
 *  PrintMapPanel will open, with a "Create PDF" button.
 * 
 *  .. code-block:: javascript
 *     
 *      var mapPanel = new GeoExt.MapPanel({
 *          renderTo: "map",
 *          layers: [new OpenLayers.Layer.WMS("Tasmania State Boundaries",
 *              "http://demo.opengeo.org/geoserver/wms",
 *              {layers: "topp:tasmania_state_boundaries"}, {singleTile: true})],
 *          center: [146.56, -41.56],
 *          zoom: 6,
 *          bbar: [{
 *              text: "Print...",
 *              handler: function() {
 *                  var printDialog = new Ext.Window({
 *                      autoHeight: true,
 *                      width: 350,
 *                      items: [new GeoExt.PrintMapPanel({
 *                          sourceMap: mapPanel,
 *                          printProvider: {
 *                              capabilities: printCapabilities
 *                          }
 *                      })],
 *                      bbar: [{
 *                          text: "Create PDF",
 *                          handler: function() {
 *                              printDialog.items.get(0).print();
 *                          }
 *                      }]
 *                  });
 *                  printDialog.show();
 *              }
 *          }]
 *      });
 */

/** api: constructor
 *  .. class:: PrintMapPanel
 * 
 *  A map panel that controls scale and center of a print page. Based on the
 *  current view (i.e. layers and extent) of a source map, this panel will be
 *  sized according to the aspect ratio of the print page. As the user zooms
 *  and pans in the :class:`GeoExt.PrintMapPanel`, the print page will update
 *  its scale and center accordingly. If the scale on the print page changes
 *  (e.g. by setting it using a combo box with a
 *  :class:`GeoExt.plugins.PrintPageField`), the extent of the
 *  :class:`GeoExt.PrintMapPanel` will be updated to match the page bounds.
 *  
 *  .. note:: The ``zoom``, ``center`` and ``extent`` config options will have
 *      no affect, as they will be determined by the ``sourceMap``.
 */
GeoExt.PrintMapPanel = Ext.extend(GeoExt.MapPanel, {
    
    /** api: config[map]
     *  ``Object`` Optional configuration for the ``OpenLayers.Map`` object
     *  that this PrintMapPanel creates. Useful e.g. to configure a map with a
     *  custom set of controls, or to add a ``preaddlayer`` listener for
     *  filtering out layer types that cannot be printed.
     */
    
    /** api: config[sourceMap]
     *  :class:`GeoExt.MapPanel` or ``OpenLayers.Map`` The map that is to be
     *  printed.
     */
    
    /** private: property[sourceMap]
     *  ``OpenLayers.Map``
     */
    sourceMap: null,
    
    /** api: config[printProvider]
     *  :class:`GeoExt.data.PrintProvider` or ``Object`` PrintProvider to use
     *  for printing. If an ``Object`` is provided, a new PrintProvider will
     *  be created and configured with the object.
     *  
     *  .. note:: The PrintMapPanel requires the printProvider's capabilities
     *    to be available upon initialization. This means that a PrintMapPanel
     *    configured with an ``Object`` as ``printProvider`` will only work
     *    when ``capabilities`` is provided in the printProvider's
     *    configuration object. If ``printProvider`` is provided as an instance
     *    of :class:`GeoExt.data.PrintProvider`, the capabilities must be
     *    loaded before PrintMapPanel initialization.
     */
    
    /** api: property[printProvider]
     *  :class:`GeoExt.data.PrintProvider` PrintProvider for this
     *  PrintMapPanel.
     */
    printProvider: null,
    
    /** api: property[printPage]
     *  :class:`GeoExt.data.PrintPage` PrintPage for this PrintMapPanel.
     *  Read-only.
     */
    printPage: null,
    
    /** api: config[limitScales]
     *  ``Boolean`` If set to true, the printPage cannot be set to scales that
     *  would generate a preview in this :class:`GeoExt.PrintMapPanel` with a
     *  completely different extent than the one that would appear on the
     *  printed map. Default is false.
     */
     
    /** api: property[previewScales]
     *  ``Ext.data.Store`` A data store with a subset of the printProvider's
     *  scales. By default, this contains all the scales of the printProvider.
     *  If ``limitScales`` is set to true, it will only contain print scales
     *  that can properly be previewed with this :class:`GeoExt.PrintMapPanel`.
     */
    previewScales: null,
    
    /** api: config[center]
     *  ``OpenLayers.LonLat`` or ``Array(Number)``  A location for the map
     *  center. Do not set, as this will be overridden with the ``sourceMap``
     *  center.
     */
    center: null,

    /** api: config[zoom]
     *  ``Number``  An initial zoom level for the map. Do not set, because the
     *  initial extent will be determined by the ``sourceMap``.
     */
    zoom: null,

    /** api: config[extent]
     *  ``OpenLayers.Bounds or Array(Number)``  An initial extent for the map.
     *  Do not set, because the initial extent will be determined by the
     *  ``sourceMap``.
     */
    extent: null,
    
    /** private: property[currentZoom]
     *  ``Number``
     */
    currentZoom: null,
    
    /**
     * private: method[initComponent]
     * private override
     */
    initComponent: function() {
        if(this.sourceMap instanceof GeoExt.MapPanel) {
            this.sourceMap = this.sourceMap.map;
        }

        if (!this.map) {
            this.map = {};
        }
        Ext.applyIf(this.map, {
            projection: this.sourceMap.getProjection(),
            maxExtent: this.sourceMap.getMaxExtent(),
            maxResolution: this.sourceMap.getMaxResolution(),
            units: this.sourceMap.getUnits()
        });
        
        if(!(this.printProvider instanceof GeoExt.data.PrintProvider)) {
            this.printProvider = new GeoExt.data.PrintProvider(
                this.printProvider);
        }
        this.printPage = new GeoExt.data.PrintPage({
            printProvider: this.printProvider
        });
        
        this.previewScales = new Ext.data.Store();
        this.previewScales.add(this.printProvider.scales.getRange());

        this.layers = [];
        var layer;
        Ext.each(this.sourceMap.layers, function(layer) {
            if (layer.getVisibility() === true) {
                if (layer instanceof OpenLayers.Layer.Vector) {
                    var features = layer.features,
                        clonedFeatures = new Array(features.length),
                        vector = new OpenLayers.Layer.Vector(layer.name);
                    for (var i=0, ii=features.length; i<ii; ++i) {
                        clonedFeatures[i] = features[i].clone();
                    }
                    vector.addFeatures(clonedFeatures, {silent: true});
                    this.layers.push(vector);
                } else {
                    this.layers.push(layer.clone());
                }
            }
        }, this);

        this.extent = this.sourceMap.getExtent();
        
        GeoExt.PrintMapPanel.superclass.initComponent.call(this);
    },
    
    /** private: method[bind]
     */
    bind: function() {
        this.printPage.on("change", this.fitZoom, this);
        this.printProvider.on("layoutchange", this.syncSize, this);
        this.map.events.register("moveend", this, this.updatePage);

        this.printPage.fit(this.sourceMap);

        if (this.initialConfig.limitScales === true) {
            this.on("resize", this.calculatePreviewScales, this);
            this.calculatePreviewScales();
        }
    },
    
    /** private: method[afterRender]
     *  Private method called after the panel has been rendered.
     */
    afterRender: function() {
        GeoExt.PrintMapPanel.superclass.afterRender.apply(this, arguments);
        this.syncSize();
        if (!this.ownerCt) {
            this.bind();
        } else {
            this.ownerCt.on({
                "afterlayout": {
                    fn: this.bind,
                    scope: this,
                    single: true
                }
            });
        }
    },
    
    /** private: method[adjustSize]
     *  :param width: ``Number`` If not provided or 0, initialConfig.width will
     *      be used.
     *  :param height: ``Number`` If not provided or 0, initialConfig.height
     *      will be used.
     *  Private override - sizing this component always takes the aspect ratio
     *  of the print page into account.
     */
    adjustSize: function(width, height) {        
        var printSize = this.printProvider.layout.get("size");
        var ratio = printSize.width / printSize.height;
        // respect width & height when sizing according to the print page's
        // aspect ratio - do not exceed either, but don't take values for
        // granted if container is configured with autoWidth or autoHeight.
        var ownerCt = this.ownerCt;
        var targetWidth = (ownerCt && ownerCt.autoWidth) ? 0 :
            (width || this.initialConfig.width);
        var targetHeight = (ownerCt && ownerCt.autoHeight) ? 0 :
            (height || this.initialConfig.height);
        if (targetWidth) {
            height = targetWidth / ratio;
            if (targetHeight && height > targetHeight) {
                height = targetHeight;
                width = height * ratio;
            } else {
                width = targetWidth;
            }
        } else if (targetHeight) {
            width = targetHeight * ratio;
            height = targetHeight;
        }

        return {width: width, height: height};
    },
    
    /** private: method[fitZoom]
     *  Fits this PrintMapPanel's zoom to the print scale.
     */
    fitZoom: function() {
        if (!this._updating && this.printPage.scale) {
            this._updating = true;
            var printBounds = this.printPage.getPrintExtent(this.map);
            this.currentZoom = this.map.getZoomForExtent(printBounds);
            this.map.zoomToExtent(printBounds);
            delete this._updating;
        }
    },

    /** private: method[updatePage]
     *  updates the print page to match this PrintMapPanel's center and scale.
     */
    updatePage: function() {
        if (!this._updating) {
            var zoom = this.map.getZoom();
            this._updating = true;
            if (zoom === this.currentZoom) {
                this.printPage.setCenter(this.map.getCenter());
            } else {
                this.printPage.fit(this.map);
            }
            delete this._updating;
            this.currentZoom = zoom;
        }
    },
    
    /** private: method[calculatePreviewScales]
     */
    calculatePreviewScales: function() {
        this.previewScales.removeAll();

        this.printPage.suspendEvents();
        var scale = this.printPage.scale;

        // group print scales by the zoom level they would be previewed at
        var viewSize = this.map.getSize();
        var scalesByZoom = {};
        var zooms = [];
        this.printProvider.scales.each(function(rec) {
            this.printPage.setScale(rec);
            var extent = this.printPage.getPrintExtent(this.map);
            var zoom = this.map.getZoomForExtent(extent);

            var idealResolution = Math.max(
                extent.getWidth() / viewSize.w,
                extent.getHeight() / viewSize.h
            );
            var resolution = this.map.getResolutionForZoom(zoom);
            // the closer to the ideal resolution, the better the fit
            var diff = Math.abs(idealResolution - resolution);
            if (!(zoom in scalesByZoom) || scalesByZoom[zoom].diff > diff) {
                scalesByZoom[zoom] = {
                    rec: rec,
                    diff: diff
                };
                zooms.indexOf(zoom) == -1 && zooms.push(zoom);
            }
        }, this);
        
        // add only the preview scales that closely fit print extents
        for (var i=0, ii=zooms.length; i<ii; ++i) {
            this.previewScales.add(scalesByZoom[zooms[i]].rec);
        }

        scale && this.printPage.setScale(scale);
        this.printPage.resumeEvents();

        if (scale && this.previewScales.getCount() > 0) {
            var maxScale = this.previewScales.getAt(0);
            var minScale = this.previewScales.getAt(this.previewScales.getCount()-1);
            if (scale.get("value") < minScale.get("value")) {
                this.printPage.setScale(minScale);
            } else if (scale.get("value") > maxScale.get("value")) {
                this.printPage.setScale(maxScale);
            }
        }

        this.fitZoom();
    },
    
    /** api: method[print]
     *  :param options: ``Object`` options for
     *      the :class:`GeoExt.data.PrintProvider` :: ``print``  method.
     *  
     *  Convenience method for printing the map, without the need to
     *  interact with the printProvider and printPage.
     */
    print: function(options) {
        this.printProvider.print(this.map, [this.printPage], options);
    },
    
    /** private: method[beforeDestroy]
     */
    beforeDestroy: function() {
        this.map.events.unregister("moveend", this, this.updatePage);
        this.printPage.un("change", this.fitZoom, this);
        this.printProvider.un("layoutchange", this.syncSize, this);
        GeoExt.PrintMapPanel.superclass.beforeDestroy.apply(this, arguments);
    }
});

/** api: xtype = gx_printmappanel */
Ext.reg('gx_printmappanel', GeoExt.PrintMapPanel); 


/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Util.js
 */

/** api: (define)
 *  module = GeoExt.state
 *  class = PermalinkProvider
 *  base_link = `Ext.state.Provider <http://dev.sencha.com/deploy/dev/docs/?class=Ext.state.Provider>`_
 */
Ext.namespace("GeoExt.state");

/** api: example
 *  Sample code displaying a new permalink each time the map is moved.
 *
 *  .. code-block:: javascript
 *
 *      // create permalink provider
 *      var permalinkProvider = new GeoExt.state.PermalinkProvider();
 *
 *      // set it in the state manager
 *      Ext.state.Manager.setProvider(permalinkProvider);
 *
 *      // create a map panel, and make it stateful
 *      var mapPanel = new GeoExt.MapPanel({
 *          renderTo: "map",
 *          layers: [
 *              new OpenLayers.Layer.WMS(
 *                  "Global Imagery",
 *                  "http://maps.opengeo.org/geowebcache/service/wms",
 *                  {layers: "bluemarble"}
 *              )
 *          ],
 *          stateId: "map",
 *          prettyStateKeys: true // for pretty permalinks
 *      });
 *
 *      // display permalink each time state is changed
 *      permalinkProvider.on({
 *          statechanged: function(provider, name, value) {
 *              alert(provider.getLink());
 *          }
 *      });
 */

/** api: constructor
 *  .. class:: PermalinkProvider(config)
 *
 *      Create a permalink provider.
 *
 */
GeoExt.state.PermalinkProvider = function(config) {
    GeoExt.state.PermalinkProvider.superclass.constructor.apply(this, arguments);

    config = config || {};

    var url = config.url;
    delete config.url;

    Ext.apply(this, config);

    this.state = this.readURL(url);
};

Ext.extend(GeoExt.state.PermalinkProvider, Ext.state.Provider, {

    /** api: config[encodeType]
     *  ``Boolean`` Specifies whether type of state values should be encoded
     *  and decoded. Set it to false if you work with components that don't
     *  require encoding types, and want pretty permalinks. Defaults to true.
     */
    /** private: property[encodeType]
     *  ``Boolean``
     */
    encodeType: true,

    /** private: method[readURL]
     *  :param url: ``String`` The URL to get the state from.
     *  :return: ``Object`` The state object.
     *
     *  Create a state object from a URL.
     */
    readURL: function(url) {
        var state = {};
        var params = OpenLayers.Util.getParameters(url);
        var k, split, stateId;
        for(k in params) {
            if(params.hasOwnProperty(k)) {
                split = k.split("_");
                if(split.length > 1) {
                    stateId = split[0];
                    state[stateId] = state[stateId] || {};
                    state[stateId][split.slice(1).join("_")] = this.encodeType ?
                        this.decodeValue(params[k]) : params[k];
                }
            }
        }
        return state;
    },

    /** api: method[getLink]
     *  :param base: ``String`` The base URL, optional.
     *  :return: ``String`` The permalink.
     *
     *  Return the permalink corresponding to the current state.
     */
    getLink: function(base) {
        base = base || document.location.href;

        var params = {};

        var id, k, state = this.state;
        for(id in state) {
            if(state.hasOwnProperty(id)) {
                for(k in state[id]) {
                    params[id + "_" + k] = this.encodeType ?
                        unescape(this.encodeValue(state[id][k])) : state[id][k];
                }
            }
        }

        // merge params in the URL into the state params
        OpenLayers.Util.applyDefaults(
            params, OpenLayers.Util.getParameters(base));

        var paramsStr = OpenLayers.Util.getParameterString(params);

        var qMark = base.indexOf("?");
        if(qMark > 0) {
            base = base.substring(0, qMark);
        }

        return Ext.urlAppend(base, paramsStr);
    }
});

/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 *
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = GeoExt
 *  class = Lang
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */
Ext.namespace("GeoExt");

/** api: constructor
 *  .. class:: Lang
 *
 *      The GeoExt.Lang singleton is created when the library is loaded.
 *      Include all relevant language files after this file in your build.
 */
GeoExt.Lang = new (Ext.extend(Ext.util.Observable, {

    /** api: property[locale]
     *  ``String``
     *  The current language tag.  Use :meth:`set` to set the locale.  Defaults
     *  to the browser language where available.
     */
    locale: navigator.language || navigator.userLanguage,

    /** private: property[dict]
     *  ``Object``
     *  Dictionary of string lookups per language.
     */
    dict: null,

    /** private: method[constructor]
     *  Construct the Lang singleton.
     */
    constructor: function() {
        this.addEvents(
            /** api: event[localize]
             *  Fires when localized strings are set.  Listeners will receive a
             *  single ``locale`` event with the language tag.
             */
            "localize"
        );
        this.dict = {};
        Ext.util.Observable.constructor.apply(this, arguments);
    },

    /** api: method[add]
     *  :param locale: ``String`` A language tag that follows the "en-CA"
     *      convention (http://www.ietf.org/rfc/rfc3066.txt).
     *  :param lookup: ``Object`` An object with properties that are dot
     *      delimited names of objects with localizable strings (e.g.
     *      "GeoExt.VectorLegend.prototype").  The values for these properties
     *      are objects that will be used to extend the target objects with
     *      localized strings (e.g. {untitledPrefix: "Untitiled "})
     *
     *  Add translation strings to the dictionary.  This method can be called
     *  multiple times with the same language tag (locale argument) to extend
     *  a single dictionary.
     */
    add: function(locale, lookup) {
        var obj = this.dict[locale];
        if (!obj) {
            this.dict[locale] = Ext.apply({}, lookup);
        } else {
            for (var key in lookup) {
                obj[key] = Ext.apply(obj[key] || {}, lookup[key]);
            }
        }
        if (!locale || locale === this.locale) {
            this.set(locale);
        } else if (this.locale.indexOf(locale + "-") === 0) {
            // current locale is regional variation of added strings
            // call set so newly added strings are used where appropriate
            this.set(this.locale);
        }
    },

    /** api: method[set]
     * :arg locale: ''String'' Language identifier tag following recommendations
     *     at http://www.ietf.org/rfc/rfc3066.txt.
     *
     * Set the language for all GeoExt components.  This will use any localized
     * strings in the dictionary (set with the :meth:`add` method) that
     * correspond to the complete matching language tag or any "higher order"
     * tag (e.g. setting "en-CA" will use strings from the "en" dictionary if
     * matching strings are not found in the "en-CA" dictionary).
     */
    set: function(locale) {
        // compile lookup based on primary and all subtags
        var tags = locale ? locale.split("-") : [];
        var id = "";
        var lookup = {}, parent;
        for (var i=0, ii=tags.length; i<ii; ++i) {
            id += (id && "-" || "") + tags[i];
            if (id in this.dict) {
                parent = this.dict[id];
                for (var str in parent) {
                    if (str in lookup) {
                        Ext.apply(lookup[str], parent[str]);
                    } else {
                        lookup[str] = Ext.apply({}, parent[str]);
                    }
                }
            }
        }

        // now extend all objects given by dot delimited names in lookup
        for (var str in lookup) {
            var obj = window;
            var parts = str.split(".");
            var missing = false;
            for (var i=0, ii=parts.length; i<ii; ++i) {
                var name = parts[i];
                if (name in obj) {
                    obj = obj[name];
                } else {
                    missing = true;
                    break;
                }
            }
            if (!missing) {
                Ext.apply(obj, lookup[str]);
            }
        }
        this.locale = locale;
        this.fireEvent("localize", locale);
    }
}))();


