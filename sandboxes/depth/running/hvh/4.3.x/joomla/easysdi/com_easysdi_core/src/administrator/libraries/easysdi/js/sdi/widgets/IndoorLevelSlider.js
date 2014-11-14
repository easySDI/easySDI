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
        if(config.map){
            this.map = config.map;
            // before we call getOpacityValue inverse should be set
            if (config.inverse !== undefined) {
                this.inverse = config.inverse;
            }
            config.value = (config.value !== undefined) ? 
                config.value : config.minValue;            
        }
        sdi.widgets.IndoorLevelSlider.superclass.constructor.call(this, config);
    },

    /** private: method[initComponent]
     *  Initialize the component.
     */
    initComponent: function() {
        sdi.widgets.IndoorLevelSlider.superclass.initComponent.call(this);

        if (this.aggressive === true) {
            this.on('change', this.changeIndoorLevel, this, {
                buffer: this.delay
            });
        } else {
            this.on('changecomplete', this.changeIndoorLevel, this);
        }
    },

    /** private: method[changeLayerOpacity]
     *  :param slider: :class:`GeoExt.LayerOpacitySlider`
     *  :param value: ``Number`` The slider value
     *
     *  Updates the ``OpenLayers.Layer`` opacity value.
     */
    changeIndoorLevel: function(slider, value) {        

            this.setValue(value);
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

/** api: xtype = sdi_indoorlevelslider */
Ext.reg('sdi_indoorlevelslider', sdi.widgets.IndoorLevelSlider);
