/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/** api: (define)
 *  module = sdi.widgets
 *  class = IndoorLevelSlider
 */
Ext.namespace("sdi.widgets");

/** api: constructor
 *  .. class:: IndoorLevelSlider(config)
 *
 *  Create a slider for controlling level in indoor navigation context.
 */
sdi.widgets.IndoorLevelSlider = Ext.extend(Ext.slider.SingleSlider, {
    /** api: config[map]
     *  ``OpenLayers.Map`` 
     *  The map this slider changes the indoor level of. (required)
     */
    /** private: property[map]
     *  ``OpenLayers.Map``
     */
    map: null,
    /** api: config[delay]
     *  ``Number`` Time in milliseconds before setting the level value to the
     *  map. If the value change again within that time, the original value
     *  is not set. Only applicable if aggressive is true.
     */
    delay: 5,
    /** api: config[aggressive]
     *  ``Boolean``
     *  If set to true, the level is changed as soon as the thumb is moved.
     *  Otherwise when the thumb is released (default).
     */
    aggressive: false,
    /** api: config[value]
     *  ``Number``
     *  The value to initialize the slider with. 
     *  If this value is not
     *  defined in the config object then the slider initializes
     *  it to the min value.
     */
    value: null,
    /** api: config[baseCls]
     *  ``String``
     *  The CSS class name for the slider elements.  Default is "sdi-indoorlevelslider".
     */
    baseCls: "sdi-indoorlevelslider",
    /**
     * 
     */
    levels: [],
    /**
     * 
     */
    style: "position: absolute; right: 50px; top: 20px; z-index: 100;",
    /** private: method[constructor]
     *  Construct the component.
     */
    constructor: function(config) {
        config.value = (config.value !== undefined) ? config.value : config.minValue;
        this.addEvents(
                "indoorlevelsliderready",
                "indoorlevelchanged"
                );
        sdi.widgets.IndoorLevelSlider.superclass.constructor.call(this, config);
    },
    /** private: method[initComponent]
     *  Initialize the component.
     */
    initComponent: function() {
        sdi.widgets.IndoorLevelSlider.superclass.initComponent.call(this);

        if (this.map) {
            if (this.map instanceof GeoExt.MapPanel) {
                this.map = this.map.map;
            }
        }
        if (this.aggressive === true) {
            this.on('change', this.changeIndoorLevel, this);
        } else {
            this.on('changecomplete', this.changeIndoorLevel, this);
        }
    },
    /** private: method[onRender]
     *  Override onRender to set base css class.
     */
    onRender: function() {
        sdi.widgets.IndoorLevelSlider.superclass.onRender.apply(this, arguments);
        this.el.addClass(this.baseCls);
    },
    /** private: method[changeIndoorLevel]
     *  :param slider: :class:`sdi.widgets.IndoorLevelSlider`
     *  :param value: ``Number`` The slider value
     *
     *  Updates the WMS filter on level and redraw the layers
     */
    changeIndoorLevel: function(slider, value) {
        if (!value) {
            value = this.getValue();
        }
        this.setValue(value);
        var layers = this.map.layers;

        for (var a = 0; a < layers.length; a++) {
            this.redrawLayer(layers[a]);
        }
        this.fireEvent("indoorlevelchanged", this);
        this.map.events.triggerEvent("indoorlevelchanged", value);
    },
    /**
     * Change indoorlevel by the level code
     * @param {type} slider
     * @param {type} code
     * @returns {undefined}
     */
    changeIndoorLevelByCode: function(slider, code) {
        if (!code)
            return;
        slider.levels.forEach(function(level) {
            if (level.code === code)
                slider.changeIndoorLevel(slider, slider.levels.indexOf(level));
        })
    },
    /**
     * Updates the WMS filter on level and redraw the layer
     * Event "layerredrawn" is sent with the concerned layer as parameter.
     * 
     * @param {openlayers layer} layer
     */
    redrawLayer: function(layer) {
        var level = this.getLevel();
        if (layer.isindoor && layer.isindoor == 1 && layer.levelfield) {
            var servertype = layer.servertype;
            if (servertype == 1 || servertype == 3) {
                var cql_filter;
                var new_filter =  layer.levelfield + "='" + level.code + "'";
                
                //existing CQL_FILTER 
                if(typeof(layer.params.CQL_FILTER) != 'undefined'){
                    //if the perimeter is restricted by user
                    if(layer.params.CQL_FILTER.match(/INTERSECTS/g)){
                        //remove level filter
                        var re = new RegExp(" AND "+layer.levelfield+"='.+'","g");
                        cql_filter = layer.params.CQL_FILTER.replace(re, "");
                        //add new level
                        cql_filter = cql_filter + " AND " + new_filter;
                    }else{
                        //should only contain a level, let's overwrite it
                        cql_filter = new_filter;
                    }  
                //no CQL_FILTER yet
                }else{
                    cql_filter = new_filter;
                }
                layer.params.CQL_FILTER = cql_filter;
            } 
            if (servertype == 2 || servertype == 3) {
                layer.mergeNewParams({'layerDefs': "{\"" + layer.params.LAYERS + "\":\"" + layer.levelfield + "='" + level.code + "'\"}"});
            }
            layer.redraw(true);
            this.map.events.triggerEvent("layerredrawn", {layer: layer});
        }
    },
    /**
     * Get the object level for a specific value
     * or, if not specified, the current value
     * @param {int} value
     * @returns {object} selected level
     */
    getLevel: function(value) {
        if (!value) {
            value = this.getValue();
        }
        return levels[value];
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
                this.el.addClass(this.baseCls);
            },
            afterrender: function() {
                this.map = panel.map;
                panel.map.indoorlevelslider = this;
                //TODO : to activate after test and remove from map.js
                this.map.events.on({"addlayer": function(e) {
                        this.indoorlevelslider.redrawLayer(e.layer);
                    }});
                this.map.indoorlevelslider.changeIndoorLevel(this);
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
    },
    /** private: method[stopMouseEvents]
     *  :param e: ``Object``
     */
    stopMouseEvents: function(e) {
        e.stopEvent();
    }
});

/** api: xtype = sdi_indoorlevelslider */
Ext.reg('sdi_indoorlevelslider', sdi.widgets.IndoorLevelSlider);
