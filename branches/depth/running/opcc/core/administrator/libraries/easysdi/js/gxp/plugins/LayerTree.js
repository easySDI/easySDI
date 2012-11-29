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
sdi.gxp.plugins.LayerTree = Ext.extend(gxp.plugins.LayerTree, {
    
	/** api: ptype = gxp_layertree */
    ptype: "sdi_gxp_layertree",
    
    /** private: method[configureLayerNode]
     *  :arg loader: ``GeoExt.tree.LayerLoader``
     *  :arg node: ``Object`` The node
     */
    configureLayerNode: function(loader, attr) {
        attr.uiProvider = this.treeNodeUI;
        var layer = attr.layer;
        var store = attr.layerStore;
        if (layer && store) {
            var record = store.getAt(store.findBy(function(r) {
                return r.getLayer() === layer;
            }));
            if (record) {
                attr.qtip = record.get('abstract');
                if (!record.get("queryable")) {
                    attr.iconCls = "gxp-tree-rasterlayer-icon";
                }
                if (record.get("fixed")) {
                    attr.allowDrag = false;
                }
               
                if(record.json)
                {
                	if(record.json.metadataURL)
                	{
                		attr.href = record.json.metadataURL;
                        attr.hrefTarget = "_blank";
                        attr.cls="sdiMDlink";
                	}	
                }
                
                attr.listeners = {
                    rendernode: function(node) {
                        if (record === this.target.selectedLayer) {
                            node.select();
                        }
                        this.target.on("layerselectionchange", function(rec) {
                            if (!this.selectionChanging && rec === record) {
                                node.select();
                            }
                        }, this);
                    },
                    scope: this
                };
            }
        }
    },
   
        
});

Ext.preg(sdi.gxp.plugins.LayerTree.prototype.ptype,sdi.gxp.plugins.LayerTree);
