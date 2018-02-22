/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @requires GeoExt/widgets/tree/LayerLoader.js
 * @requires gxp/plugins/LayerTree.js
 * @requires GeoExt/widgets/tree/LayerNode.js
 * @requires GeoExt/widgets/tree/TreeNodeUIEventMixin.js
 * @requires GeoExt/widgets/tree/LayerContainer.js
 * @requires GeoExt/widgets/tree/LayerLoader.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = LayerTree
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: LayerTree(config)
 *
 *    Plugin for adding a tree of layers to a :class:`gxp.Viewer`. Also
 *    provides a context menu on layer nodes.
 */
/** 
 * sdi extension
 */
sdi.gxp.plugins.LayerTree = Ext.extend(gxp.plugins.LayerTree, {

    /** api: ptype = gxp_layertree */
    ptype: "sdi_gxp_layertree",

    /** private: method[createOutputConfig]
     *  :returns: ``Object`` Configuration object for an Ext.tree.TreePanel
     */
    createOutputConfig: function() {
        var treeRoot = new Ext.tree.TreeNode({
            text: this.rootNodeText,
            expanded: true,
            checked: false,
            isTarget: false,
            allowDrop: false,
            iconCls: "sdi-gxp-tree-node-root",
            listeners: {
                checkchange: function(node, checked) {
                    node.eachChild(function(n) {
                        n.getUI().toggleCheck(checked);
                    });
                }

            }
        });

        var baseAttrs;
        if (this.initialConfig.loader && this.initialConfig.loader.baseAttrs) {
            baseAttrs = this.initialConfig.loader.baseAttrs;
        }

        var defaultGroup = this.defaultGroup,
            plugin = this,
            groupConfig,
            exclusive;
        for (var group in this.groups) {
            groupConfig = typeof this.groups[group] == "string" ? { title: this.groups[group] } : this.groups[group];
            exclusive = groupConfig.exclusive;
            treeRoot.appendChild(new GeoExt.tree.LayerContainer(Ext.apply({
                text: groupConfig.title,
                iconCls: "gxp-folder",
                expanded: true,
                checked: false,
                group: group == this.defaultGroup ? undefined : group,
                loader: new GeoExt.tree.LayerLoader({
                    baseAttrs: exclusive ?
                        Ext.apply({ checkedGroup: Ext.isString(exclusive) ? exclusive : group }, baseAttrs) : baseAttrs,
                    store: this.target.mapPanel.layers,
                    filter: (function(group) {
                        return function(record) {
                            return (record.get("group") || defaultGroup) == group &&
                                record.getLayer().displayInLayerSwitcher == true;
                        };
                    })(group),
                    createNode: function(attr) {
                        plugin.configureLayerNode(this, attr);
                        return GeoExt.tree.LayerLoader.prototype.createNode.apply(this, arguments);
                    }
                }),
                singleClickExpand: true,
                allowDrag: false,
                listeners: {
                    append: function(tree, node) {
                        node.expand();
                    },
                    checkchange: function(node, checked) {
                        node.eachChild(function(n) {
                            n.getUI().toggleCheck(checked);
                        });
                    }

                }
            }, groupConfig)));
        }

        return {
            xtype: "treepanel",
            root: treeRoot,
            rootVisible: true,
            shortTitle: this.shortTitle,
            autoScroll: true,
            border: false,
            enableDD: true,
            selModel: new Ext.tree.DefaultSelectionModel({
                listeners: {
                    beforeselect: this.handleBeforeSelect,
                    scope: this
                }
            }),
            contextMenu: new Ext.menu.Menu({
                items: []
            })
        };
    },




});

Ext.preg(sdi.gxp.plugins.LayerTree.prototype.ptype, sdi.gxp.plugins.LayerTree);
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @requires plugins/Tool.js
 * @requires GeoExt/data/PrintProvider.js
 * @requires GeoExt/widgets/PrintMapPanel.js
 * @requires OpenLayers/Control/ScaleLine.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = Print
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: Print(config)
 *
 *    Provides an action to print the map. Requires GeoExt.ux.PrintPreview,
 *    which is currently mirrored at git://github.com/GeoNode/PrintPreview.git.
 */

/** 
 * sdi extension
 */
sdi.gxp.plugins.Print = Ext.extend(gxp.plugins.Print, {
    
	/** api: ptype = gxp_print */
    ptype: "sdi_gxp_print",

//    /** private: method[constructor]
//     */
//    constructor: function(config) {
//        sdi.gxp.plugins.Print.superclass.constructor.apply(this, arguments);
//    },

    
    /** api: method[addActions]
     */
    addActions: function() {
        // don't add any action if there is no print service configured
        if (this.printService !== null || this.printCapabilities != null) {

            var printProvider = new sdi.geoext.data.PrintProvider({
                capabilities: this.printCapabilities,
                url: this.printService,
                printurl: this.printURL,
                createurl: this.createURL,
                customParams: this.customParams,
                autoLoad: false,
                listeners: {
                    beforedownload: function(provider, url) {
                        if (this.openInNewWindow === true) {
                            window.open(url);
                            return false;
                        }
                    },
                    beforeencodelegend: function(provider, jsonData, legend) {
                        if (legend && legend.ptype === "gxp_layermanager") {
                            var encodedLegends = [];
                            var output = legend.output;
                            if (output && output[0]) {
                                output[0].getRootNode().cascade(function(node) {
                                    if (node.component && !node.component.hidden) {
                                        var cmp = node.component;
                                        var encFn = this.encoders.legends[cmp.getXType()];
                                        encodedLegends = encodedLegends.concat(
                                            encFn.call(this, cmp, jsonData.pages[0].scale));
                                    }
                                }, provider);
                            }
                            jsonData.legends = encodedLegends;
                            // cancel normal encoding of legend
                            return false;
                        }
                    },
                    beforeprint: function() {
                        // The print module does not like array params.
                        // TODO Remove when http://trac.geoext.org/ticket/216 is fixed.
                        printWindow.items.get(0).printMapPanel.layers.each(function(l) {
                            var params = l.get("layer").params;
                            for(var p in params) {
                                if (params[p] instanceof Array) {
                                    params[p] = params[p].join(",");
                                }
                            }
                        });
                    },
                    loadcapabilities: function() {
                        if (printButton) {
                            printButton.initialConfig.disabled = false;
                            printButton.enable();
                        }
                    },
                    print: function() {
                        try {
                            printWindow.close();
                        } catch (err) {
                            // TODO: improve destroy
                        }
                    },
                    printException: function(cmp, response) {
                        this.target.displayXHRTrouble && this.target.displayXHRTrouble(response);
                    },
                    scope: this
                }
            });

            var actions = gxp.plugins.Print.superclass.addActions.call(this, [{
                menuText: this.menuText,
                buttonText: this.buttonText,
                tooltip: this.tooltip,
                iconCls: "gxp-icon-print",
                disabled: this.printCapabilities !== null ? false : true,
                handler: function() {
                    var supported = getPrintableLayers();
                    if (supported.length > 0) {
                    	//If Google and Bing layers were discarded, notify the user
                    	if(isGoogleLayerSelected() || isBingLayerSelected())
                    	{
                    		var mes = "";
                    		if(isGoogleLayerSelected())
                    		{
                    			mes = mes + this.googleLayerCanNotBePrinted;
                    		}
                    		if(isBingLayerSelected())
                    		{
                    			mes = mes + this.bingLayerCanNotBePrinted;
                    		}
                    		Ext.Msg.alert(
                                this.someLayersNotPrintableText,
                                mes, 
                                function () {
                                	 var printWindow = createPrintWindow.call(this);
                                     showPrintWindow.call(this);
                                     return printWindow;
                                },
                                this
                            );
                    	}
                    	else
                    	{
                    		var printWindow = createPrintWindow.call(this);
                            showPrintWindow.call(this);
                            return printWindow;
                    	}
                       
                    } else {
                    	// no layers supported
                    	//If Google and Bing layers were discarded, notify the user
                    	if(isGoogleLayerSelected() || isBingLayerSelected())
                    	{
                    		var mes = "";
                    		if(isGoogleLayerSelected())
                    		{
                    			mes = mes + this.googleLayerCanNotBePrinted;
                    		}
                    		if(isBingLayerSelected())
                    		{
                    			mes = mes + this.bingLayerCanNotBePrinted;
                    		}
                    		Ext.Msg.alert(
                    			this.notAllNotPrintableText,
                                mes
                            );
                    	}
                    	else
                    	{
	                        Ext.Msg.alert(
	                            this.notAllNotPrintableText,
	                            this.nonePrintableText
	                        );
                    	}
                    }
                },
                scope: this,
                listeners: {
                    render: function() {
                        // wait to load until render so we can enable on success
                        printProvider.loadCapabilities();
                    }
                }
            }]);

            var printButton = actions[0].items[0];

            var printWindow;

            function destroyPrintComponents() {
                if (printWindow) {
                    // TODO: fix this in GeoExt
                    try {
                        var panel = printWindow.items.first();
                        panel.printMapPanel.printPage.destroy();
                        //panel.printMapPanel.destroy();
                    } catch (err) {
                        // TODO: improve destroy
                    }
                    printWindow = null;
                }
            }

            var mapPanel = this.target.mapPanel;
            function getPrintableLayers() {
                var supported = [];
                mapPanel.layers.each(function(record) {
                    var layer = record.getLayer();
                    if (isPrintable(layer)) {
                        supported.push(layer);
                    }
                });
                return supported;
            }
            
            function isGoogleLayerSelected() {
            	var is = false;
            	mapPanel.layers.each(function(record) {
            		var layer = record.getLayer();
                    if(layer.getVisibility() === true && layer instanceof OpenLayers.Layer.Google)
                    	is = true;
                });
            	return is;
            }
            
            function isBingLayerSelected() {
            	var is = false;
            	mapPanel.layers.each(function(record) {
            		var layer = record.getLayer();
                    if(layer.getVisibility() === true && layer instanceof OpenLayers.Layer.Bing)
                    	is = true;
                });
            	return is;
            }
            
            function isPrintable(layer) {
                return layer.getVisibility() === true && (
                    layer instanceof OpenLayers.Layer.WMS ||
                    layer instanceof OpenLayers.Layer.OSM||
                    layer instanceof OpenLayers.Layer.WMTS 
                );
            }

            function createPrintWindow() {
                var legend = null;
                if (this.includeLegend === true) {
                    var key, tool;
                    for (key in this.target.tools) {
                        tool = this.target.tools[key];
                        if (tool.ptype === "gxp_legend") {
                            legend = tool.getLegendPanel();
                            break;
                        }
                    }
                    // if not found, look for a layer manager instead
                    if (legend === null) {
                        for (key in this.target.tools) {
                            tool = this.target.tools[key];
                            if (tool.ptype === "gxp_layermanager") {
                                legend = tool;
                                break;
                            }
                        }
                    }
                }
                printWindow = new Ext.Window({
                    title: this.previewText,
                    modal: true,
                    border: false,
                    autoHeight: true,
                    resizable: false,
                    width: 360,
                    items: [
                        new sdi.geoext.ux.PrintPreview({
                            minWidth: 336,
                            mapTitle: this.target.about && this.target.about["title"],
                            comment: this.target.about && this.target.about["abstract"],
                            printMapPanel: {
                                autoWidth: true,
                                height: Math.min(420, Ext.get(document.body).getHeight()-150),
                                limitScales: true,
                                map: Ext.applyIf({
                                    controls: [
                                        new OpenLayers.Control.Navigation({
                                            zoomWheelEnabled: false,
                                            zoomBoxEnabled: false
                                        }),
                                        new OpenLayers.Control.PanPanel(),
                                        new OpenLayers.Control.ZoomPanel(),
                                        new OpenLayers.Control.Attribution()
                                    ],
                                    eventListeners: {
                                        preaddlayer: function(evt) {
                                            return isPrintable(evt.layer);
                                        }
                                    }
                                }, mapPanel.initialConfig.map),
                                items: [{
                                    xtype: "gx_zoomslider",
                                    vertical: true,
                                    height: 100,
                                    aggressive: true
                                }],
                                listeners: {
                                    afterlayout: function(evt) {
                                        printWindow.setWidth(Math.max(360, this.getWidth() + 24));
                                        printWindow.center();
                                    }
                                }
                            },
                            printProvider: printProvider,
                            includeLegend: this.includeLegend,
                            legend: legend,
                            sourceMap: mapPanel
                        })
                    ],
                    listeners: {
                        beforedestroy: destroyPrintComponents
                    }
                });
                return printWindow;
            }

            function showPrintWindow() {
                printWindow.show();

                // measure the window content width by it's toolbar
                printWindow.setWidth(0);
                var tb = printWindow.items.get(0).items.get(0);
                var w = 0;
                tb.items.each(function(item) {
                    if(item.getEl()) {
                        w += item.getWidth();
                    }
                });
                printWindow.setWidth(
                    Math.max(printWindow.items.get(0).printMapPanel.getWidth(),
                    w + 20)
                );
                printWindow.center();
            }

            return actions;
        }
    }

});

Ext.preg(sdi.gxp.plugins.Print.prototype.ptype, sdi.gxp.plugins.Print);

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @require plugins/LayerTree.js
 * @require GeoExt/plugins/TreeNodeComponent.js
 * @require GeoExt/widgets/WMSLegend.js
 * @require GeoExt/widgets/VectorLegend.js
 * @require easysdi/gxp/plugins/LayerTree.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = LayerManager
 */

/** api: (extends)
 *  plugins/LayerTree.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: LayerManager(config)
 *
 *    Plugin for adding a tree of layers with their legend to a
 *    :class:`gxp.Viewer`. Also provides a context menu on layer nodes.
 */   
/** 
 * sdi extension
 */
sdi.gxp.plugins.LayerManager = Ext.extend(sdi.gxp.plugins.LayerTree, {
    
    /** api: ptype = gxp_layermanager */
    ptype: "sdi_gxp_layermanager",

    /** api: config[baseNodeText]
     *  ``String``
     *  Text for baselayer node of layer tree (i18n).
     */
    baseNodeText: "Base Maps",
    
    /** api: config[groups]
     *  ``Object`` The groups to show in the layer tree. Keys are group names,
     *  and values are either group titles or an object with ``title`` and
     *  ``exclusive`` properties. ``exclusive`` means that nodes will have
     *  radio buttons instead of checkboxes, so only one layer of the group can
     *  be active at a time. Optional, the default is
     *
     *  .. code-block:: javascript
     *
     *      groups: {
     *          "default": "Overlays", // title can be overridden with overlayNodeText
     *          "background": {
     *              title: "Base Maps", // can be overridden with baseNodeText
     *              exclusive: true
     *          }
     *      }
     */
    
    /** private: method[createOutputConfig] */
    createOutputConfig: function() {
        var tree = sdi.gxp.plugins.LayerManager.superclass.createOutputConfig.apply(this, arguments);
        Ext.applyIf(tree, Ext.apply({
            cls: "gxp-layermanager-tree",
            lines: false,
            useArrows: true,
            plugins: [{
                ptype: "gx_treenodecomponent"
            }]
        }, this.treeConfig));
        
        return tree;        
    },
    
    /** private: method[configureLayerNode] */
    configureLayerNode: function(loader, attr) {
        sdi.gxp.plugins.LayerManager.superclass.configureLayerNode.apply(this, arguments);
        var legendXType;
        // add a WMS legend to each node created
        if (OpenLayers.Layer.WMS && attr.layer instanceof OpenLayers.Layer.WMS) {
            legendXType = "gx_wmslegend";
        } else if (OpenLayers.Layer.Vector && attr.layer instanceof OpenLayers.Layer.Vector) {
            legendXType = "gx_vectorlegend";
        }
        if (legendXType) {
            var baseParams;
            if (loader && loader.baseAttrs && loader.baseAttrs.baseParams) {
                baseParams = loader.baseAttrs.baseParams;
            }
            Ext.apply(attr, {
                component: {
                    xtype: legendXType,
                    // TODO these baseParams were only tested with GeoServer,
                    // so maybe they should be configurable - and they are
                    // only relevant for gx_wmslegend.
                    hidden: !attr.layer.getVisibility(),
                    baseParams: Ext.apply({
                        transparent: true,
                        format: "image/png",
                        legend_options: "fontAntiAliasing:true;fontSize:11;fontName:Arial"
                    }, baseParams),
                    layerRecord: this.target.mapPanel.layers.getByLayer(attr.layer),
                    showTitle: false,
                    // custom class for css positioning
                    // see tree-legend.html
                    cls: "legend"
                }
            });
        }
    }
    
});

Ext.preg(sdi.gxp.plugins.LayerManager.prototype.ptype, sdi.gxp.plugins.LayerManager);

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: BingSource(config)
 *
 *    Plugin for using Bing layers with :class:`gxp.Viewer` instances.
 *
 *    Available layer names are "Road", "Aerial" and "AerialWithLabels"
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "bing": {
 *        ptype: "gxp_bingsource"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "bing",
 *        title: "Bing Road Map",
 *        name: "Road"
 *    }
 *
 */
sdi.gxp.plugins.BingSource = Ext.extend(gxp.plugins.BingSource, {

	 /** api: ptype = gxp_bingsource */
    ptype: "sdi_gxp_bingsource",
    
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
    	var record = sdi.gxp.plugins.BingSource.superclass.createLayerRecord.apply(this, arguments);
        
       record.json = config;
       return record;
    }
});

Ext.preg(sdi.gxp.plugins.BingSource.prototype.ptype, sdi.gxp.plugins.BingSource);
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: GoolgeSource(config)
 *
 *    Plugin for using Google layers with :class:`gxp.Viewer` instances. The
 *    plugin uses the GMaps v3 API and also takes care of loading the
 *    required Google resources.
 *
 *    Available layer names for this source are "ROADMAP", "SATELLITE",
 *    "HYBRID" and "TERRAIN"
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "google": {
 *        ptype: "gxp_google"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "google",
 *        name: "TERRAIN"
 *    }
 *
 */
sdi.gxp.plugins.GoogleSource = Ext.extend(gxp.plugins.GoogleSource, {
    /** api: ptype = gxp_googlesource */
    ptype: "sdi_gxp_googlesource",
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.GoogleSource.superclass.createLayerRecord.apply(this, arguments);

        record.json = config;
        return record;
    }
});

Ext.preg(sdi.gxp.plugins.GoogleSource.prototype.ptype, sdi.gxp.plugins.GoogleSource);
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: OSMSource(config)
 *
 *    Plugin for using OpenStreetMap layers with :class:`gxp.Viewer` instances.
 *
 *    Available layer names are "mapnik" and "osmarender"
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "osm": {
 *        ptype: "gxp_osmsource"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "osm",
 *        name: "osmarander"
 *    }
 *
 */
sdi.gxp.plugins.OSMSource = Ext.extend(gxp.plugins.OSMSource, {
    /** api: ptype = gxp_googlesource */
    ptype: "sdi_gxp_osmsource",
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.OSMSource.superclass.createLayerRecord.apply(this, arguments);

        record.json = config;
        return record;
    },

    /** api: method[createStore]
     *
     *  Creates a store of layer records.  Fires "ready" when store is loaded.
     */
    createStore: function() {

        var options = {
            projection: "EPSG:900913",
            maxExtent: new OpenLayers.Bounds(-128 * 156543.0339, -128 * 156543.0339,
                128 * 156543.0339, 128 * 156543.0339
            ),
            maxResolution: 156543.03390625,
            numZoomLevels: 19,
            units: "m",
            buffer: 1,
            transitionEffect: "resize"
        };

        var layers = [
            new OpenLayers.Layer.OSM(
                "OpenStreetMap", [
                    "https://a.tile.openstreetmap.org/${z}/${x}/${y}.png",
                    "https://b.tile.openstreetmap.org/${z}/${x}/${y}.png",
                    "https://c.tile.openstreetmap.org/${z}/${x}/${y}.png"
                ],
                OpenLayers.Util.applyDefaults({
                    attribution: this.mapnikAttribution,
                    type: "mapnik"
                }, options)
            )
        ];

        this.store = new GeoExt.data.LayerStore({
            layers: layers,
            fields: [
                { name: "source", type: "string" },
                { name: "name", type: "string", mapping: "type" },
                { name: "abstract", type: "string", mapping: "attribution" },
                { name: "group", type: "string", defaultValue: "background" },
                { name: "fixed", type: "boolean", defaultValue: true },
                { name: "selected", type: "boolean" }
            ]
        });
        this.store.each(function(l) {
            l.set("group", "background");
        });
        this.fireEvent("ready", this);

    }
});

Ext.preg(sdi.gxp.plugins.OSMSource.prototype.ptype, sdi.gxp.plugins.OSMSource);
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */
/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = LoadingIndicator
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: LoadingIndicator(config)
 *
 *    Static plugin for show a loading indicator on the map.
 */   
sdi.gxp.plugins.LoadingIndicator = Ext.extend(gxp.plugins.LoadingIndicator, {

    /** api: ptype = gxp_loadingindicator */
    ptype: "sdi_gxp_loadingindicator",

    /** private: method[init]
     *  :arg target: ``Object``
     */
    init: function(target) {
         var map = target instanceof GeoExt.MapPanel ?
            target.map : target.mapPanel.map;
        map.events.register("preaddlayer", this, function(e) {
            var layer = e.layer;
            if (layer instanceof OpenLayers.Layer.WMS || layer instanceof OpenLayers.Layer.WMTS) {
                layer.events.on({
                    "loadstart": function() {
                        this.layerCount++;
                        if (!this.busyMask) {
                            this.busyMask = new Ext.LoadMask(
                                map.div, {
                                    msg: this.loadingMapMessage
                                }
                            );
                        }
                        this.busyMask.show();
                        if (this.onlyShowOnFirstLoad === true) {
                            layer.events.unregister("loadstart", this, arguments.callee);
                        }
                    },
                    "loadend": function() {
                        this.layerCount--;
                        if(this.layerCount === 0) {
                            this.busyMask.hide();
                        }
                        if (this.onlyShowOnFirstLoad === true) {
                            layer.events.unregister("loadend", this, arguments.callee);
                        }
                    },
                    scope: this
                });
            } 
        });
    }

});

Ext.preg(sdi.gxp.plugins.LoadingIndicator.prototype.ptype, sdi.gxp.plugins.LoadingIndicator);

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: WMSSource(config)
 *
 *    Plugin for using WMS layers with :class:`gxp.Viewer` instances. The
 *    plugin issues a GetCapabilities request to create a store of the WMS's
 *    layers.
 */   
/** api: example
 *  Configuration in the  :class:`gxp.Viewer`:
 *
 *  .. code-block:: javascript
 *
 *    defaultSourceType: "gxp_wmssource",
 *    sources: {
 *        "opengeo": {
 *            url: "http://suite.opengeo.org/geoserver/wms"
 *        }
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "opengeo",
 *        name: "world",
 *        group: "background"
 *    }
 *
 *  For initial programmatic layer configurations, to leverage lazy loading of
 *  the Capabilities document, it is recommended to configure layers with the
 *  fields listed in :obj:`requiredProperties`.
 */
sdi.gxp.plugins.WMSSource = Ext.extend(gxp.plugins.WMSSource, {
    
    /** api: ptype = gxp_wmssource */
    ptype: "sdi_gxp_wmssource",
    
    
     
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord`` or null when the source is lazy.
     *
     *  Create a layer record given the config. Applications should check that
     *  the source is not :obj:`lazy`` or that the ``config`` is complete (i.e.
     *  configured with all fields listed in :obj:`requiredProperties` before
     *  using this method. Otherwise, it is recommended to use the asynchronous
     *  :meth:`gxp.Viewer.createLayerRecord` method on the target viewer
     *  instead, which will load the source's store to complete the
     *  configuration if necessary.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.WMSSource.superclass.createLayerRecord.apply(this, arguments);
        if(!jQuery.isEmptyObject(record)){
	 record.data.layer.attribution = config.attribution;
         record.data.layer.isindoor = config.isindoor;
         record.data.layer.levelfield = config.levelfield;
         record.data.layer.servertype = config.servertype;
        }
        return record;
    }
    
    
    
});

Ext.preg(sdi.gxp.plugins.WMSSource.prototype.ptype, sdi.gxp.plugins.WMSSource);

/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @requires plugins/Tool.js
 * @requires widgets/form/GoogleGeocoderComboBox.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = GoogleGeocoder
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: GoogleGeocoder(config)
 *
 *    Plugin for adding a GoogleGeocoderComboBox to a viewer.  The underlying
 *    GoogleGeocoderComboBox can be configured by setting this tool's 
 *    ``outputConfig`` property. The gxp.form.GoogleGeocoderComboBox requires 
 *    the gxp.plugins.GoogleSource or the Google Maps V3 API to be loaded.
 */
sdi.gxp.plugins.GoogleGeocoder = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = gxp_googlegeocoder */
    ptype: "sdi_gxp_googlegeocoder",

    /** api: config[updateField]
     *  ``String``
     *  If value is specified, when an item is selected in the combo, the map
     *  will be zoomed to the corresponding field value in the selected record.
     *  If ``null``, no map navigation will occur.  Valid values are the field
     *  names described for the :class:`gxp.form.GoogleGeocoderComboBox`.
     *  Default is "viewport".
     */
    updateField: "viewport",
    
    init: function(target) {

        var combo = new sdi.gxp.form.GoogleGeocoderComboBox(Ext.apply({
            listeners: {
                select: this.onComboSelect,
                scope: this
            }
        }, this.outputConfig));
        
        var bounds = target.mapPanel.map.restrictedExtent;
        if (bounds && !combo.bounds) {
            target.on({
                ready: function() {
                    combo.bounds = bounds.clone().transform(
                        target.mapPanel.map.getProjectionObject(),
                        new OpenLayers.Projection("EPSG:4326")
                    );
                }
            });
        }
        this.combo = combo;
        
        return sdi.gxp.plugins.GoogleGeocoder.superclass.init.apply(this, arguments);

    },
    
    /** api: method[addOutput]
     */
    addOutput: function(config) {
        return sdi.gxp.plugins.GoogleGeocoder.superclass.addOutput.call(this, this.combo);
    },
    
    /** private: method[onComboSelect]
     *  Listener for combo's select event.
     */
    onComboSelect: function(combo, record) {
        if (this.updateField) {
            var map = this.target.mapPanel.map;
            var location = record.get(this.updateField).clone().transform(
                new OpenLayers.Projection("EPSG:4326"),
                map.getProjectionObject()
            );
            if (location instanceof OpenLayers.Bounds) {
                map.zoomToExtent(location, true);
            } else {
                map.setCenter(location);
            }
        }
    }

});

Ext.preg(sdi.gxp.plugins.GoogleGeocoder.prototype.ptype, sdi.gxp.plugins.GoogleGeocoder);

/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @requires plugins/Tool.js
 * @requires plugins/FeatureEditorGrid.js
 * @requires GeoExt/widgets/Popup.js
 * @requires OpenLayers/Control/WMSGetFeatureInfo.js
 * @requires OpenLayers/Format/WMSGetFeatureInfo.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = WMSGetFeatureInfo
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("gxp.plugins");

/** api: constructor
 *  .. class:: WMSGetFeatureInfo(config)
 *
 *    This plugins provides an action which, when active, will issue a
 *    GetFeatureInfo request to the WMS of all layers on the map. The output
 *    will be displayed in a popup.
 */
gxp.plugins.WMSGetFeatureInfo = Ext.extend(gxp.plugins.Tool, {

    /** api: ptype = gxp_wmsgetfeatureinfo */
    ptype: "gxp_wmsgetfeatureinfo",

    /** api: config[outputTarget]
     *  ``String`` Popups created by this tool are added to the map by default.
     */
    outputTarget: "map",

    /** private: property[popupCache]
     *  ``Object``
     */
    popupCache: null,

    /** api: config[infoActionTip]
     *  ``String``
     *  Text for feature info action tooltip (i18n).
     */
    infoActionTip: "Get Feature Info",

    /** api: config[popupTitle]
     *  ``String``
     *  Title for info popup (i18n).
     */
    popupTitle: "Feature Info",

    /** api: config[text]
     *  ``String`` Text for the GetFeatureInfo button (i18n).
     */
    buttonText: "Identify",

    /** api: config[format]
     *  ``String`` Either "html" or "grid". If set to "grid", GML will be
     *  requested from the server and displayed in an Ext.PropertyGrid.
     *  Otherwise, the html output from the server will be displayed as-is.
     *  Default is "html".
     */
    format: "html",

    /** api: config[vendorParams]
     *  ``Object``
     *  Optional object with properties to be serialized as vendor specific
     *  parameters in the requests (e.g. {buffer: 10}).
     */

    /** api: config[layerParams]
     *  ``Array`` List of param names that should be taken from the layer and
     *  added to the GetFeatureInfo request (e.g. ["CQL_FILTER"]).
     */

    /** api: config[itemConfig]
     *  ``Object`` A configuration object overriding options for the items that
     *  get added to the popup for each server response or feature. By default,
     *  each item will be configured with the following options:
     *
     *  .. code-block:: javascript
     *
     *      xtype: "propertygrid", // only for "grid" format
     *      title: feature.fid ? feature.fid : title, // just title for "html" format
     *      source: feature.attributes, // only for "grid" format
     *      html: text, // responseText from server - only for "html" format
     */

    /** api: method[addActions]
     */
    addActions: function() {
        this.popupCache = {};

        var actions = gxp.plugins.WMSGetFeatureInfo.superclass.addActions.call(this, [{
            tooltip: this.infoActionTip,
            iconCls: "gxp-icon-getfeatureinfo",
            buttonText: this.buttonText,
            toggleGroup: this.toggleGroup,
            enableToggle: true,
            allowDepress: true,
            toggleHandler: function(button, pressed) {
                for (var i = 0, len = info.controls.length; i < len; i++) {
                    if (pressed) {
                        info.controls[i].activate();
                    } else {
                        info.controls[i].deactivate();
                    }
                }
            }
        }]);
        var infoButton = this.actions[0].items[0];

        var info = { controls: [] };
        var updateInfo = function() {
            var queryableLayers = this.target.mapPanel.layers.queryBy(function(x) {
                return x.get("queryable");
            });

            var map = this.target.mapPanel.map;
            var control;
            for (var i = 0, len = info.controls.length; i < len; i++) {
                control = info.controls[i];
                control.deactivate(); // TODO: remove when http://trac.openlayers.org/ticket/2130 is closed
                control.destroy();
            }

            info.controls = [];
            queryableLayers.each(function(x) {
                var layer = x.getLayer();
                var vendorParams = Ext.apply({}, this.vendorParams),
                    param;
                if (this.layerParams) {
                    for (var i = this.layerParams.length - 1; i >= 0; --i) {
                        param = this.layerParams[i].toUpperCase();
                        vendorParams[param] = layer.params[param];
                    }
                }
                var infoFormat = x.get("infoFormat");
                if (infoFormat === undefined) {
                    // TODO: check if chosen format exists in infoFormats array
                    // TODO: this will not work for WMS 1.3 (text/xml instead for GML)
                    infoFormat = this.format == "html" ? "text/html" : "application/vnd.ogc.gml";
                }
                var control = new OpenLayers.Control.WMSGetFeatureInfo(Ext.applyIf({
                    url: layer.url,
                    queryVisible: true,
                    layers: [layer],
                    infoFormat: infoFormat,
                    vendorParams: vendorParams,
                    eventListeners: {
                        getfeatureinfo: function(evt) {
                            var title = x.get("title") || x.get("name");
                            if (infoFormat == "text/html") {
                                var match = evt.text.match(/<body[^>]*>([\s\S]*)<\/body>/);
                                if (match && !match[1].match(/^\s*$/)) {
                                    this.displayPopup(evt, title, match[1]);
                                }
                            } else if (infoFormat == "text/plain") {
                                this.displayPopup(evt, title, '<pre>' + evt.text + '</pre>');
                            } else if (evt.features && evt.features.length > 0) {
                                this.displayPopup(evt, title, null, x.get("getFeatureInfo"));
                            }
                        },
                        scope: this
                    }
                }, this.controlOptions));
                map.addControl(control);
                info.controls.push(control);
                if (infoButton.pressed) {
                    control.activate();
                }
            }, this);

        };

        this.target.mapPanel.layers.on("update", updateInfo, this);
        this.target.mapPanel.layers.on("add", updateInfo, this);
        this.target.mapPanel.layers.on("remove", updateInfo, this);

        return actions;
    },

    /** private: method[displayPopup]
     * :arg evt: the event object from a 
     *     :class:`OpenLayers.Control.GetFeatureInfo` control
     * :arg title: a String to use for the title of the results section 
     *     reporting the info to the user
     * :arg text: ``String`` Body text.
     */
    displayPopup: function(evt, title, text, featureinfo) {
        var popup;
        var popupKey = evt.xy.x + "." + evt.xy.y;
        featureinfo = featureinfo || {};
        if (!(popupKey in this.popupCache)) {
            popup = this.addOutput({
                xtype: "gx_popup",
                title: this.popupTitle,
                layout: "accordion",
                fill: false,
                autoScroll: true,
                location: evt.xy,
                map: this.target.mapPanel,
                width: this.popupWidth,
                height: this.popupHeight,
                //width: this.popupWidth,
                //height: this.popupHeight,
                defaults: {
                    layout: "fit",
                    autoScroll: true,
                    autoHeight: true,
                    autoWidth: true,
                    collapsible: true
                }
            });
            popup.on({
                close: (function(key) {
                    return function(panel) {
                        delete this.popupCache[key];
                    };
                })(popupKey),
                scope: this
            });
            this.popupCache[popupKey] = popup;
        } else {
            popup = this.popupCache[popupKey];
        }

        var features = evt.features,
            config = [];
        if (!text && features) {
            var feature;
            for (var i = 0, ii = features.length; i < ii; ++i) {
                feature = features[i];
                config.push(Ext.apply({
                    xtype: "gxp_editorgrid",
                    readOnly: true,
                    listeners: {
                        'beforeedit': function(e) {
                            return false;
                        }
                    },
                    title: feature.fid ? feature.fid : title,
                    feature: feature,
                    fields: featureinfo.fields,
                    propertyNames: featureinfo.propertyNames
                }, this.itemConfig));
            }
        } else if (text) {
            config.push(Ext.apply({
                title: title,
                html: text
            }, this.itemConfig));
        }
        popup.add(config);
        popup.doLayout();
    }

});

Ext.preg(gxp.plugins.WMSGetFeatureInfo.prototype.ptype, gxp.plugins.WMSGetFeatureInfo);
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.widgets");

/** api: constructor
 *  .. class:: ScaleOverlay(config)
 *   
 *      Create a panel for showing a ScaleLine control and a combobox for 
 *      selecting the map scale.
 */
sdi.gxp.ScaleOverlay = Ext.extend(gxp.ScaleOverlay, {


    /** private: method[addScaleLine]
     *  
     *  Create the scale line control and add it to the panel.
     */
    addScaleLine: function() {
        var scaleLinePanel = new Ext.BoxComponent({
            autoEl: {
                tag: "div",
                cls: "olControlScaleLine overlay-element overlay-scaleline"
            }
        });
        this.on("afterlayout", function() {
            scaleLinePanel.getEl().dom.style.position = 'relative';
            scaleLinePanel.getEl().dom.style.display = 'inline';

            this.getEl().on("click", this.stopMouseEvents, this);
            this.getEl().on("mousedown", this.stopMouseEvents, this);
        }, this);
        scaleLinePanel.on('render', function() {
            var scaleLine = new OpenLayers.Control.ScaleLine({
                bottomInUnits: SdiScaleLineParams.bottomInUnits,
                bottomOutUnits: SdiScaleLineParams.bottomOutUnits,
                topInUnits: SdiScaleLineParams.topInUnits,
                topOutUnits: SdiScaleLineParams.topOutUnits,
                geodesic: true,
                div: scaleLinePanel.getEl().dom
            });

            this.map.addControl(scaleLine);
            scaleLine.activate();
        }, this);
        this.add(scaleLinePanel);
    }

});

Ext.reg('sdi_gxp_scaleoverlay', sdi.gxp.ScaleOverlay);
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("gxp");

var sourceConfig;
var layerConfig;

/**
 * Add a source to the viewer and a layer config to the map configuration
 * the layer is not added to the map here.
 * @param {type} lsourceConfig
 * @param {type} llayerConfig
 * @returns {Ext@call;extend.prototype.addExtraLayer.queue|gxp.Viewer.prototype.addExtraLayer.queue|Array}
 */
gxp.Viewer.prototype.addExtraLayer = function(lsourceConfig, llayerConfig) {

    sourceConfig = lsourceConfig;
    layerConfig = llayerConfig;
    if (this.sources[sourceConfig.id] === undefined) {
        this.sources[sourceConfig.id] = sourceConfig;
    }
    this.initialConfig.map.layers.push(layerConfig);

    var queue = [];
    queue.push(this.createSourceLoader(sourceConfig.id));

    return queue;
};

/**
 * Create layer record and add layer to the map
 * Call after addExtraLayer
 * @returns {undefined}
 */
gxp.Viewer.prototype.reactivate = function() {
    // initialize tooltips
    Ext.QuickTips.init();

    var mapConfig = this.initialConfig.map;
    if (mapConfig && mapConfig.layers) {
        var conf, source, record, baseRecords = [],
            overlayRecords = [];
        //Get the last layer
        conf = mapConfig.layers[mapConfig.layers.length - 1];
        source = this.layerSources[conf.source];
        if (source) {
            if (source.id === sourceConfig.id) {
                // source may not have loaded properly (failure handled elsewhere)
                record = source.createLayerRecord(conf);
                if (record) {
                    if (record.get("group") === "background") {
                        baseRecords.push(record);
                    } else {
                        overlayRecords.push(record);
                    }
                }
            }
        }

        var panel = this.mapPanel;
        //extent = record.getLayer().maxExtent.clone();

        var records = baseRecords.concat(overlayRecords);
        if (records.length) {
            panel.layers.add(records);
        }
    }

    // respond to any queued requests for layer records
    this.checkLayerRecordQueue();
    return record;
};

/**
 * Overwrite the initMapPanel method in gxp.Viewer to avoid the default blank baselayer.
 * Since easySDI doesn't allow any map without baselayer, we don't need this baselayer.
 * Without it, an easySDI baselayer can set the resolutions of the map (for example with WMTS )
 */
gxp.Viewer.prototype.initMapPanel = function() {
    var config = Ext.apply({}, this.initialConfig.map);
    var mapConfig = {};

    // split initial map configuration into map and panel config
    if (this.initialConfig.map) {
        var props = "theme,controls,resolutions,projection,units,maxExtent,restrictedExtent,maxResolution,numZoomLevels,panMethod".split(",");
        var prop;
        for (var i = props.length - 1; i >= 0; --i) {
            prop = props[i];
            if (prop in config) {
                mapConfig[prop] = config[prop];
                delete config[prop];
            }
        }
    }

    this.mapPanel = Ext.ComponentMgr.create(Ext.applyIf({
        xtype: config.xtype || "gx_mappanel",
        map: Ext.applyIf({
            theme: mapConfig.theme || null,
            controls: mapConfig.controls || [
                new OpenLayers.Control.Navigation({
                    zoomWheelOptions: { interval: 250 },
                    dragPanOptions: { enableKinetic: true }
                }),
                new OpenLayers.Control.PanPanel(),
                new OpenLayers.Control.ZoomPanel(),
                new OpenLayers.Control.Attribution()
            ],
            maxExtent: mapConfig.maxExtent && OpenLayers.Bounds.fromArray(mapConfig.maxExtent),
            restrictedExtent: mapConfig.restrictedExtent && OpenLayers.Bounds.fromArray(mapConfig.restrictedExtent),
            numZoomLevels: mapConfig.numZoomLevels || 20
        }, mapConfig),
        center: config.center && new OpenLayers.LonLat(config.center[0], config.center[1]),
        resolutions: config.resolutions,
        forceInitialExtent: true,
        layers: [],
        items: this.mapItems,
        plugins: this.mapPlugins,
        tbar: config.tbar || new Ext.Toolbar({
            hidden: true
        })
    }, config));
    this.mapPanel.getTopToolbar().on({
        afterlayout: this.mapPanel.map.updateSize,
        show: this.mapPanel.map.updateSize,
        hide: this.mapPanel.map.updateSize,
        scope: this.mapPanel.map
    });

    this.mapPanel.layers.on({
        "add": function(store, records) {
            // check selected layer status
            var record;
            for (var i = records.length - 1; i >= 0; i--) {
                record = records[i];
                if (record.get("selected") === true) {
                    this.selectLayer(record);
                }
            }
        },
        "remove": function(store, record) {
            if (record.get("selected") === true) {
                this.selectLayer();
            }
        },
        scope: this
    });
};
/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
Ext.namespace("sdi.gxp.form");

sdi.gxp.form.GoogleGeocoderComboBox = Ext.extend(gxp.form.GoogleGeocoderComboBox, {
    
    /** api: xtype = gxp_googlegeocodercombo */
    xtype: "sdi_gxp_googlegeocodercombo",

    /** api: config[queryDelay]
     *  ``Number`` Delay before the search occurs.  Default is 100ms.
     */
    queryDelay: 100,
    
    /** api: config[bounds]
     *  ``OpenLayers.Bounds | Array`` Optional bounds (in geographic coordinates)
     *  for restricting search.
     */
    
    /** api: config[valueField]
     *  ``String``
     *  Field from selected record to use when the combo's ``getValue`` method
     *  is called.  Default is "location".  Possible value's are "location",
     *  "viewport", or "address".  The location value will be an 
     * ``OpenLayers.LonLat`` object that corresponds to the geocoded address.
     *  The viewport value will be an ``OpenLayers.Bounds`` object that is 
     *  the recommended viewport for viewing the resulting location.  The
     *  address value will be a string that is the formatted address.
     */
    valueField: "viewport",

    /** private: config[displayField]
     */
    displayField: "address",
    
    /** private: method[initComponent]
     *  Override
     */
    initComponent: function() {
        
        // only enable when Google Maps API is available
        this.disabled = true;
        var ready = !!(window.google && google.maps);
        if (!ready) {
            if (!gxp.plugins || !gxp.plugins.GoogleSource) {
                throw new Error("The gxp.form.GoogleGeocoderComboBox requires the gxp.plugins.GoogleSource or the Google Maps V3 API to be loaded.");
            }
            gxp.plugins.GoogleSource.loader.onLoad({
                otherParams: gxp.plugins.GoogleSource.prototype.otherParams,
                callback: this.prepGeocoder,
                errback: function() {
                    throw new Error("The Google Maps script failed to load within the given timeout.");
                },
                scope: this
            });
        } else {
            // call in the next turn to complete initialization
            window.setTimeout((function() {
                this.prepGeocoder();
            }).createDelegate(this), 0);
        }

        this.store = new Ext.data.JsonStore({
            root: "results",
            fields: [
                {name: "address", type: "string"},
                {name: "location"}, // OpenLayers.LonLat
                {name: "viewport"} // OpenLayers.Bounds
            ],
            autoLoad: false
        });
        
        this.on({
            focus: function() {
                this.clearValue();
            },
            scope: this
        });
        
        return sdi.gxp.form.GoogleGeocoderComboBox.superclass.initComponent.apply(this, arguments);

    },
    
    prepGeocoder: function() {
        var geocoder = new google.maps.Geocoder();
        

        // create an async proxy for getting geocoder results
        var api = {};
        api[Ext.data.Api.actions.read] = true;
        var proxy = new Ext.data.DataProxy({api: api});
        var combo = this;
        
        // TODO: unhack this - this is due to the the tool output being generated too early
        var getBounds = (function() {
            // optional bounds for restricting search
            var bounds = this.bounds;
            if (bounds) {
                if (bounds instanceof OpenLayers.Bounds) {
                    bounds = bounds.toArray();
                }
                bounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(bounds[1], bounds[0]),
                    new google.maps.LatLng(bounds[3], bounds[2])
                );
            }
            return bounds;
        }).createDelegate(this);
        
        proxy.doRequest = function(action, rs, params, reader, callback, scope, options) {
            // Assumes all actions read.
            geocoder.geocode(
                {address: params.query/*, bounds: getBounds()*/},
                function(results, status) {
                    var readerResult;
                    if (status === google.maps.GeocoderStatus.OK || 
                        status === google.maps.GeocoderStatus.ZERO_RESULTS) {
                        try {
                            results = combo.transformResults(results);
                            readerResult = reader.readRecords({results: results});
                        } catch (err) {
                            combo.fireEvent("exception", combo, "response", action, options, status, err);
                        }
                    } else {
                        combo.fireEvent("exception", combo, "remote", action, options, status, null);
                    }
                    if (readerResult) {
                        callback.call(scope, readerResult, options, true);                        
                    } else {
                        callback.call(scope, null, options, false);                        
                    }
                }
            );
        };
        
        this.store.proxy = proxy;
        if (this.initialConfig.disabled != true) {
            this.enable();
        }
    }
    
    });

Ext.reg('sdi_gxp_googlegeocodercombobox', sdi.gxp.form.GoogleGeocoderComboBox);


/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Copyright (c) 2008-2012 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */

/**
 * @require OpenLayers/Layer.js
 * @require OpenLayers/Format/JSON.js
 * @require OpenLayers/Format/GeoJSON.js
 * @require OpenLayers/BaseTypes/Class.js
 */

/** api: (define)
 *  module = GeoExt.data
 *  class = PrintProvider
 *  base_link = `Ext.util.Observable <http://dev.sencha.com/deploy/dev/docs/?class=Ext.util.Observable>`_
 */
Ext.namespace("sdi.geoext.data");

/** 
 * sdi extension
 */
sdi.geoext.data.PrintProvider = Ext.extend(GeoExt.data.PrintProvider, {
   
    
    /** api: method[loadCapabilities]
     *
     *  Loads the capabilities from the print service. If this instance is
     *  configured with either ``capabilities`` or a ``url`` and ``autoLoad``
     *  set to true, then this method does not need to be called from the
     *  application.
     */
    loadCapabilities: function() {
        if (!this.url) {
            return;
        }
        var url = this.url + "info.json";
        Ext.Ajax.request({
            url: url,
            method: "GET",
            disableCaching: false,
            success: function(response) {
                this.capabilities = Ext.decode(response.responseText);
                this.capabilities.createURL = this.createurl ;
                this.capabilities.printURL = this.printurl ;
                this.loadStores();
            },
            params: this.initialConfig.baseParams,
            scope: this
        });
    }
    
  
    
});

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
/**
 * Copyright (c) 2008-2009 The Open Source Geospatial Foundation
 * 
 * Published under the BSD license.
 * See http://svn.geoext.org/core/trunk/geoext/license.txt for the full text
 * of the license.
 */
Ext.namespace("sdi.geoext.ux");

/** 
 * sdi extension
 */
sdi.geoext.ux.PrintPreview = Ext.extend(GeoExt.ux.PrintPreview, {
    
  
    /** private: method[initComponent]
     */
    initComponent: function() {
        var printMapPanelOptions = {
            sourceMap: this.sourceMap,
            printProvider: this.printProvider
        };
        if(this.printMapPanel) {
            if(!(this.printMapPanel instanceof GeoExt.PrintMapPanel)) {
                printMapPanelOptions.xtype = "gx_printmappanel";
                this.printMapPanel = new sdi.geoext.widgets.PrintMapPanel(
                    Ext.applyIf(this.printMapPanel, printMapPanelOptions));
            }
        } else {
            this.printMapPanel = new sdi.geoext.widgets.PrintMapPanel(
                printMapPanelOptions);
        }
        this.sourceMap = this.printMapPanel.sourceMap;
        this.printProvider = this.printMapPanel.printProvider;
        
        this.form = this.createForm();

        if (!this.items) {
            this.items = [];
        }
        this.items.push(this.createToolbar(), {
            xtype: "container",
            cls: "gx-printpreview",
            autoHeight: this.autoHeight,
            autoWidth: this.autoWidth,
            items: [
                this.form,
                this.printMapPanel
            ]
        });

        GeoExt.ux.PrintPreview.superclass.initComponent.call(this);
        
        this.addMapOverlay && this.printMapPanel.add(this.createMapOverlay());

        this.printMapPanel.on({
            "resize": this.updateSize,
            scope: this
        });
        this.on({
            "render": function() {
                if (!this.busyMask) {
                    this.busyMask = new Ext.LoadMask(this.getEl(), {
                        msg: this.creatingPdfText
                    });
                }
                this.printProvider.on({
                    "beforeprint": this.busyMask.show,
                    "print": this.busyMask.hide,
                    "printexception": this.busyMask.hide,
                    scope: this.busyMask
                });
            },
            scope: this
        });
    }
});


/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
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
Ext.namespace("sdi.geoext.widgets");

/** 
 * sdi extension
 */
sdi.geoext.widgets.PrintMapPanel = Ext.extend(GeoExt.PrintMapPanel, {
     
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
                	//clone function seems to not correctly handle visibility for WMTS layer. 
                	var l = layer.clone();
                	try{
                		//This fails with a Google layer but it doesn't matter because Google layer can't be printed
                		l.setVisibility ( layer.getVisibility());
                	}catch (err)
                	{}
                    this.layers.push(l);
                }
            }
        }, this);

        this.extent = this.sourceMap.getExtent();
        
        GeoExt.PrintMapPanel.superclass.initComponent.call(this);
    }
    
   
});


/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

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
        SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
        SqueezeBox.setContent('iframe', this.initialConfig.url);
        
    }
});

Ext.preg(sdi.plugins.SearchCatalog.prototype.ptype, sdi.plugins.SearchCatalog);

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * @requires plugins/Tool.js
 */

/** api: (define)
 *  module = sdi.plugins
 *  class = LayerDetailSheet
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.plugins");

/** api: constructor
 *  
 *
 *    Plugin for opening the layer's detail sheet.
 
 */
sdi.plugins.LayerDetailSheet = Ext.extend(gxp.plugins.Tool, {
    /** api: ptype = sdi_layerdetailsheet */
    ptype: "sdi_layerdetailsheet",
    /** api: config[layerDetailMenuText]
     *  ``String``
     *  Text for detail sheet menu item (i18n).
     */
    layerDetailMenuText: "Layer details sheet",
    /** api: config[layerDetailActionTip]
     *  ``String``
     *  Text for detail sheet action tooltip (i18n).
     */
    layerDetailActionTip: "Layer details sheet",
    /** api: method[addActions]
     */
    addActions: function() {
        var selectedLayer;
        var actions = sdi.plugins.LayerDetailSheet.superclass.addActions.apply(this, [{
                menuText: this.layerDetailMenuText,
                iconCls: "gxp-icon-getfeatureinfo",
                disabled: true,
                tooltip: this.layerDetailActionTip,
                handler: function() {
                    var record = selectedLayer;
                    SqueezeBox.initialize({});
                    SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
                    SqueezeBox.setContent('iframe', record.json.href);
                },
                scope: this
            }]);
        var layerDetailAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerDetailAction.setDisabled(
                    !record || !record.json || !record.json.href 
                    );
        }, this);

        return actions;
    }

});

Ext.preg(sdi.plugins.LayerDetailSheet.prototype.ptype, sdi.plugins.LayerDetailSheet);

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * @requires plugins/Tool.js
 */

/** api: (define)
 *  module = sdi.plugins
 *  class = LayerDownload
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.plugins");

/** api: constructor
 *
 *    Plugin for downloading the linked product.
 */
sdi.plugins.LayerDownload = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = sdi_layerdownload */
    ptype: "sdi_layerdownload",
    
    /** api: config[layerDownloadMenuText]
     *  ``String``
     *  Text for download action menu item (i18n).
     */
    layerDownloadMenuText: "Download",

    /** api: config[layerDownloadActionTip]
     *  ``String``
     *  Text for download action tooltip (i18n).
     */
    layerDownloadActionTip: "Download",
    
    /** api: method[addActions]
     */
    addActions: function() {
        var selectedLayer;
        var actions = sdi.plugins.LayerDownload.superclass.addActions.apply(this, [{
            menuText: this.layerDownloadMenuText,
            iconCls: "gxp-icon-filebrowse",
            disabled: true,
            tooltip: this.layerDownloadActionTip,
            handler: function() {
               var record = selectedLayer;
               SqueezeBox.initialize({});
               SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
               SqueezeBox.setContent('iframe', record.json.download + '&origin=map');
               
            },
            scope: this
        }]);
        var layerDownloadAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerDownloadAction.setDisabled(
                !record || !record.json || !record.json.download
            );
        }, this);
               
        return actions;
    }
        
});

Ext.preg(sdi.plugins.LayerDownload.prototype.ptype, sdi.plugins.LayerDownload);

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * @requires plugins/Tool.js
 */

/** api: (define)
 *  module = sdi.plugins
 *  class = LayerDownload
 */

/** api: (extends)
 *  plugins/Tool.js
 */
Ext.namespace("sdi.plugins");

/** api: constructor
 *
 *    Plugin for opening shop order form
 */
sdi.plugins.LayerOrder = Ext.extend(gxp.plugins.Tool, {
    
    /** api: ptype = sdi_layerorder */
    ptype: "sdi_layerorder",
    
    /** api: config[layerOrderMenuText]
     *  ``String``
     *  Text for shop menu item (i18n).
     */
    layerOrderMenuText: "Order",

    /** api: config[layerOrderActionTip]
     *  ``String``
     *  Text for shop action tooltip (i18n).
     */
    layerOrderActionTip: "Order",
    
    /** api: method[addActions]
     */
    addActions: function() {
        var selectedLayer;
        var actions = sdi.plugins.LayerDownload.superclass.addActions.apply(this, [{
            menuText: this.layerOrderMenuText,
            iconCls: "gxp-icon-addnote",
            disabled: true,
            tooltip: this.layerOrderActionTip,
            handler: function() {
               var record = selectedLayer;
               SqueezeBox.initialize({});
               SqueezeBox.resize({x: this.initialConfig.iwidth, y: this.initialConfig.iheight});
               SqueezeBox.setContent('iframe', record.json.order);
               
            },
            scope: this
        }]);
        var layerOrderAction = actions[0];

        this.target.on("layerselectionchange", function(record) {
            selectedLayer = record;
            layerOrderAction.setDisabled(
                !record || !record.json || !record.json.order
            );
        }, this);
               
        return actions;
    }
        
});

Ext.preg(sdi.plugins.LayerOrder.prototype.ptype, sdi.plugins.LayerOrder);

/**
 * Copyright (c) 2008-2011 The Open Planning Project
 * 
 * Published under the GPL license.
 * See https://github.com/opengeo/gxp/raw/master/license.txt for the full text
 * of the license.
 */

/**
 * @requires plugins/LayerSource.js
 */

/** api: (define)
 *  module = gxp.plugins
 *  class = OLSource
 */

/** api: (extends)
 *  plugins/LayerSource.js
 */
Ext.namespace("sdi.gxp.plugins");

/** api: constructor
 *  .. class:: OLSource(config)
 *
 *    Plugin for using any ``OpenLayers.Layer`` layers with :class:`gxp.Viewer`
 *    instances.
 *
 *    Configuration for layers from a :class:`gxp.OLSource`:
 *
 *    * type: ``String`` - the CLASS_NAME of an ``OpenLayers.Layer``
 *    * args: ``Array`` - the arguments passed to the layer's constructor
 */
/** api: example
 *  The configuration in the ``sources`` property of the :class:`gxp.Viewer` is
 *  straightforward:
 *
 *  .. code-block:: javascript
 *
 *    "ol": {
 *        ptype: "gxp_olsource"
 *    }
 *
 *  A typical configuration for a layer from this source (in the ``layers``
 *  array of the viewer's ``map`` config option would look like this:
 *
 *  .. code-block:: javascript
 *
 *    {
 *        source: "ol",
 *        type: "OpenLayers.Layer.OSM"
 *        args: ["Mapnik"]
 *    }
 *
 */
sdi.gxp.plugins.OLSource = Ext.extend(gxp.plugins.OLSource, {
    /** api: ptype = gxp_olsource */
    ptype: "sdi_gxp_olsource",
    /** api: method[createLayerRecord]
     *  :arg config:  ``Object``  The application config for this layer.
     *  :returns: ``GeoExt.data.LayerRecord``
     *
     *  Create a layer record given the config.
     */
    createLayerRecord: function(config) {
        var record = sdi.gxp.plugins.OLSource.superclass.createLayerRecord.apply(this, arguments);

        record.json = config;
        return record;
    }
});

Ext.preg(sdi.gxp.plugins.OLSource.prototype.ptype, sdi.gxp.plugins.OLSource);

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

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * Define a couple WMS/WFS to allow features selection :
 * - WMS displays features
 * - WFS performs the selection
 * Specific functionnalities "user perimeter" and "indoor level navigation" are
 * handle by this class.
 * Objects that have to be previously initialized in the calling context :
 * - app : a gxp.viewer object
 * Variables that have to be declared in the calling context :
 * - selectLayer 
 * - selectControl
 * @param {type} item
 * @returns {predefinedPerimeter}
 */
function predefinedPerimeter(item) {
    this.item = item;
}
;

/**
 * Build and add to the current gxp viewer (app) layer and corresponding service source.
 * Configure and add to the map a OpenLayers.Control.GetFeature.
 * @returns {undefined}
 */
predefinedPerimeter.prototype.init = function(userrestriction) {
    this.userrestriction = userrestriction;
    //Perimeter Layer WMS
    this.initPerimeterLayer();

    //Vector layer to handle selection
    this.initSelectLayer();

    //Map select control on WFS
    this.initSelectControl();
};

/**
 * 
 * @returns {undefined}
 */
predefinedPerimeter.prototype.initPerimeterLayer = function() {
    var layerconfig = {type: "OpenLayers.Layer.WMS",
        name: this.item.maplayername,
        transparent: true,
        isindoor: this.item.isindoor,
        servertype: this.item.server,
        levelfield: this.item.levelfield,
        opacity: this.item.opacity,
        source: this.item.source,
        tiled: false,
        title: "perimeterLayer",
        iwidth: "360",
        iheight: "360",
        visibility: true};
    var sourceconfig = {id: this.item.source,
        ptype: "sdi_gxp_wmssource",
        hidden: "true",
        url: this.item.wmsurl
    };

    //Handle restriction on user specific perimeter
    if (typeof (this.userrestriction) !== 'undefined') {
        var exp = new OpenLayers.Format.WKT().write(this.userrestriction);
        //Geoserver
        if (this.item.server === "1") {
            layerconfig.cql_filter = "INTERSECTS(" + this.item.featuretypefieldgeometry + "," + exp + ")";
        }
        /**
         * ArcGIS : geometry filter has to be sent in a specific parametre 'geometry'
         * describe here : http://resources.arcgis.com/en/help/rest/apiref/
         * TODO : find a way to send geometry parameter in the GetMap request
         * WMSSource doesn't send it if we just set it on the layerconfig like below
         * 
         * NB : see note in myperimeter.js, ArcGIS server can't filter WFS requests
         * as expected, so ArcGIS server can't support user perimeter filter functionnality.
         */
//        if (this.item.server === "2") {
//            var polygon = "{\"rings\" : [ [ [6.101531982421875,46.23451309019769], [6.1052656173706055,46.237006565073216], [6.112003326416016,46.235641104770565], [6.109728813171387,46.232613223769555], [6.104021072387695,46.2313960872759] ,[6.101531982421875,46.23451309019769]],  ],\"spatialReference\" : {\"wkid\" : 4326}}";
//            layerconfig.geometry = polygon;
//        }
    }

    var queue = app.addExtraLayer(sourceconfig, layerconfig);
    gxp.util.dispatch(queue, app.reactivate, app);
};

/**
 * Initialize the vector layer in which selected features will be drawn 
 * @returns {undefined}
 */
predefinedPerimeter.prototype.initSelectLayer = function() {
    //Selection  Layer
    selectLayer = new OpenLayers.Layer.Vector("Selection", {srsName: app.mapPanel.map.projection, projection: app.mapPanel.map.projection});
    app.mapPanel.map.addLayer(selectLayer);

    //Keep selection layer on top
    app.mapPanel.map.events.register('addlayer', this, function() {
        if (app.mapPanel.map.getLayersByName("Selection").length > 0) {
            app.mapPanel.map.setLayerIndex(selectLayer, app.mapPanel.map.getNumLayers());
        }
    });
};

/**
 * Initialize the OpenLayers map control GetFeature with the WFS parameters
 * @returns {undefined}
 */
predefinedPerimeter.prototype.initSelectControl = function() {
    selectControl = new OpenLayers.Control.GetFeature({
        protocol: new OpenLayers.Protocol.WFS({
            version: "1.0.0",
            url: this.item.wfsurl,
            srsName: app.mapPanel.map.projection,
            featureType: this.item.featuretypename,
            featurePrefix: this.item.prefix,
            featureNS: this.item.namespace,
            geometryName: this.item.featuretypefieldgeometry
        }),
        box: false,
        click: true,
        toggle: true,
        multipleKey: "ctrlKey",
        clickout: false
    });

    //Build the default filter : merge existing filters on user perimeter and indoor level
    selectControl.protocol.defaultFilter = this.getSelectControlFilter();

    //In case indoor level navigation is active on the map
    if (this.item.featuretypefieldlevel && typeof (app.mapPanel.map.indoorlevelslider) !== 'undefined') {
        //Update indoor level filter at each IndoorLevelSlider event
        app.mapPanel.map.events.register("indoorlevelchanged", this, function() {
            if (selectLayer)
                selectLayer.removeAllFeatures();
            if (selectControl && selectControl.protocol) {
                selectControl.protocol.defaultFilter = this.getSelectControlFilter();
            }
        });
    }
    app.mapPanel.map.addControl(selectControl);
    selectControl.activate();
};

/**
 * Return an OpenLayers Filter corresponding to indoor level value
 * @returns {OpenLayers.Filter.Comparison}
 */
predefinedPerimeter.prototype.getIndoorLevelFilter = function() {
    selectControl.fieldlevel = this.item.prefix + ':' + this.item.featuretypefieldlevel;
    return new OpenLayers.Filter.Comparison({
        type: OpenLayers.Filter.Comparison.EQUAL_TO,
        property: selectControl.fieldlevel,
        value: app.mapPanel.map.indoorlevelslider.getLevel().code
    });
};

/**
 * Return the specific user perimeter filter if restriction has to be applied
 * @returns {undefined}
 */
predefinedPerimeter.prototype.getUserPerimeterFilter = function() {
    var g = this.userrestriction.geometry;
    return  new OpenLayers.Filter.Spatial({
        type: OpenLayers.Filter.Spatial.INTERSECTS,
        value: g
    });
};

/**
 * Return a complete filter to apply on the GetFeature control.
 * This feature merge user perimeter filter and indoor level navigation filter.
 * @returns {undefined}
 */
predefinedPerimeter.prototype.getSelectControlFilter = function() {
    var merged, userfilter, levelfilter;

    //If userperimeter activated, handle restriction with user specific perimeter
    if (typeof (this.userrestriction) !== 'undefined') {
        userfilter = this.getUserPerimeterFilter();
    }

    //In case indoor level navigation is active on the map, handle filter on indoor level value
    if (this.item.featuretypefieldlevel && typeof (app.mapPanel.map.indoorlevelslider) !== 'undefined') {
        levelfilter = this.getIndoorLevelFilter();
    }

    //Merged, if needed, filters
    if (levelfilter && userfilter) {
        merged = new OpenLayers.Filter.Logical({
            type: OpenLayers.Filter.Logical.AND,
            filters: [levelfilter, userfilter]
        });
    } else {
        merged = levelfilter || userfilter || undefined;
    }

    return merged;
};

/**
 * Define the function to call when a feature is selected on the map
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureSelected = function(f) {
    selectControl.events.register("featureselected", this, f);
};

/**
 * Define the function to call when a feature is unselected from the map
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureUnSelected = function(f) {
    selectControl.events.register("featureunselected", this, f);
};

/**
 * Define the function to call when indoor level changed
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerIndoorLevelChanged = function(f) {
    app.mapPanel.map.events.register("indoorlevelchanged", this, f);
};

/**
 * Define the function to call after a feature was added to the map.
 * @param {type} f
 * @returns {undefined}
 */
predefinedPerimeter.prototype.setListenerFeatureAdded = function(f) {
    selectLayer.events.register("featureadded", selectLayer, f);
};

