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
    
    /** private: method[createOutputConfig]
     *  :returns: ``Object`` Configuration object for an Ext.tree.TreePanel
     */
    createOutputConfig: function() {
        var treeRoot = new Ext.tree.TreeNode({
            text: "Context 1",
            expanded: true,
            checked:false,
            isTarget: false,
            allowDrop: false,
            iconCls: "sdi-gxp-tree-node-root",
            listeners: {
                checkchange : function (node, checked ){
                	node.eachChild(function(n) {
                	    n.getUI().toggleCheck(checked);
                	});
                }
        
            }
        });
        
        var defaultGroup = this.defaultGroup,
            plugin = this,
            groupConfig,
            exclusive;
        for (var group in this.groups) {
            groupConfig = typeof this.groups[group] == "string" ?
                {title: this.groups[group]} : this.groups[group];
            exclusive = groupConfig.exclusive;
            treeRoot.appendChild(new GeoExt.tree.LayerContainer(Ext.apply({
                text: groupConfig.title,
                iconCls: "gxp-folder",
                expanded: true,
                checked:false,
                group: group == this.defaultGroup ? undefined : group,
                loader: new GeoExt.tree.LayerLoader({
                    baseAttrs: exclusive ?
                        {checkedGroup: Ext.isString(exclusive) ? exclusive : group} :
                        undefined,
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
                    checkchange : function (node, checked ){
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
            border: false,
            enableDD: true,
            selModel: new Ext.tree.DefaultSelectionModel({
                listeners: {
                    beforeselect: this.handleBeforeSelect,
                    scope: this
                }
            }),
            listeners: {
                contextmenu: this.handleTreeContextMenu,
                beforemovenode: this.handleBeforeMoveNode,                
                scope: this
            },
            contextMenu: new Ext.menu.Menu({
                items: []
            })
        };
    },
    
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
