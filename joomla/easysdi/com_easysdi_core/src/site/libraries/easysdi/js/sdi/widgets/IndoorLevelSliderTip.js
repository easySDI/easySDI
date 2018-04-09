/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

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
         var me = this;
        this.compiledTemplate = new Ext.Template(this.template);
        
        sdi.widgets.IndoorLevelSliderTip.superclass.init.call(this, slider);
        
//        slider.on('afterRender', me.onSliderRender, me, {scope:me,delay:100, single:true});
//        slider.un("dragend", me.hide, me);

    },
    
//    onSliderRender : function(slider) {
//        var thumbs  = slider.thumbs,
//            t       = 0,
//            tLen    = thumbs.length,
//            onSlide = this.onSlide;
//
//        for (; t < tLen; t++) {
//            this.onSlide(slider, null, thumbs[t]);
//        }
//    },

    /** private: method[getText]
     *  :param slider: ``Ext.slider.SingleSlider`` The slider this tip is attached to.
     */
    getText: function(thumb) {
        var level = levels[thumb.value].label;
        var data = {
            level: level
        };
        return this.compiledTemplate.apply(data);
    }
});
