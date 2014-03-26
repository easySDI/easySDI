/**
 * Copyright (c) 2008 The Open Planning Project
 */
Ext.namespace("GeoExt.tree");

/**
 * Class: GeoExt.tree.TristateCheckboxNode
 * 
 * Provides a tree node that will have a third state (stored in
 * attributes.thirdState) to have a proper semi-checked state for
 * nodes with only *some* children checked. attributes.thirdState will be
 * undefined if the node is checked or unchecked, and true if the node is
 * semi-checked.
 * 
 * This node has also a childcheckchange event that will be triggered with a
 * child node and it's checked state to notify listeners when a the checked
 * state of a child's node has changed.
 * 
 * Applications using this class should not rely on the checkchange event to
 * determine the checked state of non-leaf nodes. Instead, applications should
 * also listen to the childcheckchange event and read out attributes.checked
 * and attributes.thirdState to get the node's checked state.
 * 
 * To use this node type in a JSON config, set nodeType to "tristateCheckbox".
 * 
 * Inherits from:
 * - <Ext.tree.TreeNode>
 */
GeoExt.tree.TristateCheckboxNode = Ext.extend(Ext.tree.TreeNode, {
    
    /**
     * Property: checkedChildNodes
     * {Object} Hash of 0.1 for thirdState nodes and 1 for fully checked
     *     nodes, keyed by node ids. In combination with
     *     {<checkedCount>}, this provides an
     *     efficient way of keeping track of the childnodes' checked status.
     */
    checkedChildNodes: null,
    
    /**
     * Property: checkedCount
     * {Number} A cache for the sum of checkedChildNodes' values.
     */
    checkedCount: null,
    
    /**
     * Constructor: GeoExt.tree.TristateCheckboxNode
     * 
     * Parameters:
     * config - {Object}
     */
    constructor: function(config) {
        this.checkedChildNodes = {};
        this.checkedCount = 0;
        
        this.defaultUI = this.defaultUI || GeoExt.tree.TristateCheckboxNodeUI;
        this.addEvents.apply(this, GeoExt.tree.TristateCheckboxNode.EVENT_TYPES);
        
        GeoExt.tree.TristateCheckboxNode.superclass.constructor.apply(this, arguments);

        this.on("childcheckchange", this.updateCheckedChildNodes, this);
    },
    
    /**
     * Method: render
     * 
     * Parameters:
     * bulkRender - {Boolean}
     */
    render: function(bulkRender) {
        var rendered = this.rendered;
        var checked = this.attributes.checked;
        this.attributes.checked =
            typeof this.attributes.checked == "undefined" ? false :
            this.attributes.checked;
        GeoExt.tree.TristateCheckboxNode.superclass.render.call(this, bulkRender);
        var ui = this.getUI();
        if(!rendered) {
            if(typeof checked == "undefined" && this.parentNode.ui.checkbox) {
                ui.toggleCheck(this.parentNode.ui.checkbox.checked);
            }
            this.parentNode.on("checkchange", function(node, checked) {
                ui.toggleCheck(checked);
            }, this);
        }
    },
    
    /**
     * Method: updateCheckedChildNodes
     * Updates the status cache of checked child nodes.
     * 
     * Parameters:
     * node - {Ext.tree.Node} child node that has changed
     * checked - {Boolean} new checked status of the changed child node
     */
    updateCheckedChildNodes: function(node, checked) {
        if(checked) {
            this.addChecked(node, node.attributes.thirdState);
        } else {
            this.removeChecked(node);
        }

        var childrenChecked, childrenThirdState;
        if(this.checkedCount.toFixed() == this.childNodes.length) {
            childrenChecked = true;
            childrenThirdState = false;
        } else if(this.checkedCount.toFixed(1) == 0) {
            childrenChecked = false;
            childrenThirdState = false;
        } else {
            childrenChecked = true;
            childrenThirdState = true;
        }
        // do a special silent toggleCheck to avoid checkchange events being
        // triggered
        this.getUI().toggleCheck(childrenChecked, childrenThirdState,
            {silent: true});
        if(this.parentNode) {
            this.parentNode.fireEvent("childcheckchange", this,
                childrenChecked);
        }
    },
    
    /**
     * Method: appendChild
     * 
     * Parameters:
     * node - {Ext.tree.Node}
     */
    appendChild: function(node) {
        GeoExt.tree.TristateCheckboxNode.superclass.appendChild.call(this, node);
        if(this.attributes.checked || node.attributes.checked) {
            this.addChecked(node);
        }
        // We do not want this event handler to trigger checkchange events on
        // parent nodes, because this would cause bouncing between this
        // handler and the handler for (un-)checking children on a parent's
        // checkchange event. So we introduce a special childcheckchange
        // event with a handler that will also trigger this event on the
        // parent.
        node.on("checkchange", function(node, checked) {
            if (this.childrenRendered) {
                this.fireEvent("childcheckchange", node, checked);
            }
        }, this);
    },
    
    /**
     * Method: addChecked
     * Adds a child node to the checkedChildNodes hash. Adds 1 for fully
     * checked nodes, 0.1 for third state checked nodes.
     * 
     * Parameters:
     * node - {Ext.tree.Node}
     * thirdState - {Boolean}
     */
    addChecked: function(node, thirdState) {
        // subtract current value (if any). This is needed to change from a
        // tristate to a fully checked state and vice versa.
        this.checkedCount -= (this.checkedChildNodes[node.id] || 0);
        
        var add = thirdState ? 0.1 : 1;
        this.checkedChildNodes[node.id] = add;
        this.checkedCount += add;
    },
    
    /**
     * Method: removeChecked
     * Removes a child node from the checkedChildNodes hash.
     * 
     * Parameters:
     * node - {Ext.tree.Node}
     */
    removeChecked: function(node) {
        var remove = this.checkedChildNodes[node.id]
        if(remove) {
            delete this.checkedChildNodes[node.id];
            this.checkedCount -= remove;
        }
    }
});

/**
 * Constant: EVENT_TYPES
 * {Array(String)} - supported event types
 * 
 * Event types supported for this class, in additon to the ones inherited
 * from {<GeoExt.tree.TristateCheckboxNode>}:
 * - *childcheckchange* fired to notify a parent node that the status of
 *     its checked child nodes has changed
 */
GeoExt.tree.TristateCheckboxNode.EVENT_TYPES = ["childcheckchange"];

/**
 * NodeType: tristateCheckbox
 */
Ext.tree.TreePanel.nodeTypes.tristateCheckbox = GeoExt.tree.TristateCheckboxNode;
