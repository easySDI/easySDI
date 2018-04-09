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