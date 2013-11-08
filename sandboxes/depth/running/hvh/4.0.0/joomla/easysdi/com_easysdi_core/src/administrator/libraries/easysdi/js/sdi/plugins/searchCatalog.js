
/**
 * @requires plugins/Tool.js
 * @requires widgets/NewSourceDialog.js
 */

/** api: (define)
 *  module = sdi.plugins
 *  class = searchCatalog
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.plugins");

/** api: constructor
 *  .. class:: searchCatalog(config)
 *
 */
sdi.plugins.SearchCatalog = Ext.extend(gxp.plugins.Tool, {
    /** api: ptype = sdi_searchCatalog */
    ptype: "sdi_searchcatalog",
    /** api: config[addActionMenuText]
     *  ``String``
     *  Text for add menu item (i18n).
     */
    addActionMenuText: "Search catalog",
    /** api: config[addActionTip]
     *  ``String``
     *  Text for add action tooltip (i18n).
     */
    addActionTip: "Search catalog",
    /** api: config[addButtonText]
     *  ``String``
     *  Text for add layers button (i18n).
     */
    addButtonText: "Search catalog",
    /** api: config[untitledText]
     *  ``String``
     *  Text for an untitled layer (i18n).
     */
    untitledText: "Untitled",
    /** api: config[doneText]
     *  ``String``
     *  Text for Done button (i18n).
     */
    doneText: "Done",
    /** private: method[constructor]
     */
    constructor: function(config) {
        gxp.plugins.AddLayers.superclass.constructor.apply(this, arguments);
    },
    /** api: method[addActions]
     */
    addActions: function() {
        var commonOptions = {
            tooltip: this.addActionTip,
            text: this.addActionText,
            menuText: this.addActionMenuText,
            disabled: true,
            iconCls: "gxp-icon-addlayersfromcatalog"
        };
        options = Ext.apply(commonOptions, {
            handler: this.showCatalogFrame,
            scope: this
        });

        var actions = gxp.plugins.AddLayers.superclass.addActions.apply(this, [options]);

        this.target.on("ready", function() {

            actions[0].enable();
        }, this);
        return actions;
    },
    showCatalogFrame: function() {
        SqueezeBox.initialize({});
        SqueezeBox.setContent('iframe', 'http://localhost/sdi4a8/index.php/catalog-main?tmpl=component');
    }
});

Ext.preg(sdi.plugins.SearchCatalog.prototype.ptype, sdi.plugins.SearchCatalog);
