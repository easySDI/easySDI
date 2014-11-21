/** api: (define)
 *  module = GeoExt
 *  class = LayerOpacitySliderTip
 *  base_link = `Ext.Tip <http://dev.sencha.com/deploy/dev/docs/?class=Ext.Tip>`_
 */
Ext.namespace("sdi.widgets");

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
sdi.widgets.IndoorLevelSliderTip = Ext.extend(GeoExt.SliderTip, {

    /** api: config[template]
     *  ``String``
     *  Template for the tip. Can be customized using the following keywords in
     *  curly braces:
     *
     *  * ``opacity`` - the opacity value in percent.
     */
    template: '<div>{level}</div>',
    
    /**
     * 
     */
    levels:[],

    /** private: property[compiledTemplate]
     *  ``Ext.Template``
     *  The template compiled from the ``template`` string on init.
     */
    compiledTemplate: null,

    /** private: method[constructor]
     *  Construct the component.
     */
    constructor: function(config) {
        levels = config.levels;        
        sdi.widgets.IndoorLevelSliderTip.superclass.constructor.call(this, config);
    },
    /** private: method[init]
     *  Called to initialize the plugin.
     */
    init: function(slider) {
        this.compiledTemplate = new Ext.Template(this.template);
        
        sdi.widgets.IndoorLevelSliderTip.superclass.init.call(this, slider);
    },

    /** private: method[getText]
     *  :param slider: ``Ext.slider.SingleSlider`` The slider this tip is attached to.
     */
    getText: function(thumb) {
        var level = levels[thumb.value].label
        var data = {
            level: level
        };
        return this.compiledTemplate.apply(data);
    }
});
