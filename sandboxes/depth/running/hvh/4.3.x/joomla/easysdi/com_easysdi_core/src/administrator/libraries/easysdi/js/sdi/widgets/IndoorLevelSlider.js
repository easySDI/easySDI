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
     *  If set to true, the opacity is changed as soon as the thumb is moved.
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
    
    /**
     * 
     */
    levels : [],
    
    /** private: method[constructor]
     *  Construct the component.
     */
    constructor: function(config) {
        config.value = (config.value !== undefined) ? config.value : config.minValue;
        
        sdi.widgets.IndoorLevelSlider.superclass.constructor.call(this, config);
    },
    /** private: method[initComponent]
     *  Initialize the component.
     */
    initComponent: function() {
        sdi.widgets.IndoorLevelSlider.superclass.initComponent.call(this);

//        if (this.map) {
//            if (this.map instanceof GeoExt.MapPanel) {
//                this.map = this.map.map;
//            }            
//        }
        if (this.aggressive === true) {
            this.on('change', this.changeIndoorLevel, this);
        } else {
            this.on('changecomplete', this.changeIndoorLevel, this);
        }
       // this.on("beforedestroy", this.unbind, this);        
    },    
//    /**
//     * @param {type} evt
//     * Registered as a listener for map ready.  Updates the value of the layers filter.
//     */
//    initLayer:function(evt){
//        this.changeIndoorLevel(this, this.value);
//    },
    /** private: method[changeLayerOpacity]
     *  :param slider: :class:`GeoExt.LayerOpacitySlider`
     *  :param value: ``Number`` The slider value
     *
     *  Updates the ``OpenLayers.Layer`` opacity value.
     */
    changeIndoorLevel: function(slider, value) {
        var layers = this.map.layers;
        var level = levels[value];
        
        for (var a = 0; a < layers.length; a++){
            if (layers[a].levelfield) {
                var servertype = layers[a].servertype;
                if (servertype == 1) {
                    layers[a].mergeNewParams({'CQL_FILTER': "\"" + layers[a].levelfield + "=" + level.code + "\""});
                } else if (servertype == 2) {
                    layers[a].mergeNewParams({'layerDefs': "{\"" + layers[a].params.LAYERS + "\":\"" + layers[a].levelfield + "='" + level.code + "'\"}"});
                }
                layers[a].redraw(true);
            }
        };
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
                this.map = panel.map;
                panel.map.indoorlevelslider = this;
//                this.bind(panel.map);                
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
//        this.unbind();
    },
    /** private: method[stopMouseEvents]
     *  :param e: ``Object``
     */
    stopMouseEvents: function(e) {
        e.stopEvent();
    },
//    /** private: method[bind]
//     *  :param map: ``OpenLayers.Map``
//     */
//    bind: function(map) {
//        this.map = map;
//        this.map.events.on({
//            ready: this.initLayer,
//            scope: this
//        });
//
//    },
//    /** private: method[unbind]
//     */
//    unbind: function() {
//        if (this.map && this.map.events) {
//            this.map.events.un({
//                ready: this.initLayer,
//                scope: this
//            });
//        }
//    }
});

/** api: xtype = sdi_indoorlevelslider */
Ext.reg('sdi_indoorlevelslider', sdi.widgets.IndoorLevelSlider);
