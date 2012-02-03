/**
 * Copyright (c) 2008 The Open Planning Project
 * 
 * @requires GeoExt/widgets/tree/TristateCheckboxNode.js
 */
Ext.namespace("GeoExt.tree");

/**
 * Class: GeoExt.tree.LayerNode
 * 
 * A subclass of {Ext.tree.AsyncTreeNode} that is connected to an
 * {OpenLayers.Layer} by setting the node's layer property. Checking or
 * unchecking the checkbox of this node will directly affect the layer and
 * vice versa. The default iconCls for this node's icon is "layer-icon",
 * unless it has children.
 * 
 * This node can contain children, e.g. filter nodes.
 * 
 * Setting the node's layer property to a layer name instead of an object
 * will also work. As soon as a layer is found, it will be stored as layer
 * property in the attributes hash.
 * 
 * The node's text property defaults to the layer name.
 * 
 * If the layer has a queryable property set to true, the node will render a
 * radio button to select the query layer. Clicking on the radio button will
 * fire the querychange event, with the layer as argument. A queryGroup
 * attribute, set to the map's id, will be added to the attributes hash.
 * 
 * To use this node type in a JSON config, set nodeType to "gxLayer".
 * 
 * Inherits from:
 * - <GeoExt.tree.TristateCheckboxNode>
 */
GeoExt.tree.LayerNode = Ext.extend(GeoExt.tree.TristateCheckboxNode, {
    
    /**
     * ConfigProperty: layer
     * {OpenLayers.Layer} or {String}. The layer that this layer node will
     * be bound to, or the name of the layer (has to match the layer's name
     * property). Subclasses or applications can always rely on finding an
     * {OpenLayers.Layer} object in attributes.layer.
     */
    layer: null,
    
    /**
     * ConfigProperty: map
     * {OpenLayers.Map} or {String}. Map or id of an {Ext.Component} that
     * has a map property with an {OpenLayers.Map} set. This node will
     * be connected to that map. If omitted, the node will query the
     * ComponentManager for the first component that has a map property
     * with an {OpenLayers.Map} set.
     */
    map: null,
    
    /**
     * Property: haveLayer
     * {Boolean} will be set to true as soon as this node is connected to a
     * layer.
     */
    haveLayer: null,
    
    /**
     * Property: updating
     * {Boolean} The visibility status of the layer is being updated by itself
     *     (i.e. not by clicking on this node, but by layer visibilitychanged)
     */
    updating: false,

    /**
     * Constructor: GeoExt.tree.LayerNode
     * 
     * Parameters:
     * config - {Object}
     */
    constructor: function(config) {
        this.layer = config.layer;
        this.map = config.map;
        this.haveLayer = false;

        config.leaf = config.leaf || !config.children;
        config.iconCls = typeof config.iconCls == "undefined" &&
            !config.children ? "layer-icon" : config.iconCls;
        // checked status will be set by layer event, so setting it to false
        // to always get the checkbox rendered
        config.checked = false;
        
        this.defaultUI = this.defaultUI || GeoExt.tree.LayerNodeUI;
        this.addEvents.apply(this, GeoExt.tree.LayerNode.EVENT_TYPES);
        
        GeoExt.tree.LayerNode.superclass.constructor.apply(this, arguments);
    },

    /**
     * Method: render
     * 
     * Properties:
     * bulkRender {Boolean} - optional
     * layer {<OpenLayers.Layer>} - optional
     */
    render: function(bulkRender) {
        if (!this.rendered || !this.haveLayer) {
            var map = this.map instanceof OpenLayers.Map ? this.map :
                (typeof this.map == "string" ? Ext.getCmp(this.map).map :
                Ext.ComponentMgr.all.find(function(o) {
                    return o.map instanceof OpenLayers.Map;
                }).map);
            var layer = this.attributes.layer || this.layer;
            this.haveLayer = layer && typeof layer == "object";
            if(typeof layer == "string") {
                var matchingLayers = map.getLayersByName(layer);
                if(matchingLayers.length > 0) {
                    layer = matchingLayers[0];
                    this.haveLayer = true;
                }
            }
            var ui = this.getUI();
            if(this.haveLayer) {
                this.attributes.layer = layer;
                if(layer.queryable == true) {
                    this.attributes.radioGroup = layer.map.id;
                }
                if(!this.text) {
                    this.text = layer.name;
                }
                ui.show();
                ui.toggleCheck(layer.getVisibility());
                layer.events.register("visibilitychanged", this, function(){
                    this.updating = true;
                    if(this.attributes.checked != layer.getVisibility()) {
                        ui.toggleCheck(layer.getVisibility());
                    }
                    this.updating = false;
                });
                this.on("checkchange", function(node, checked){
                    if(!this.updating) {
                        if(checked && layer.isBaseLayer) {
                            map.setBaseLayer(layer);
                        }
                        layer.setVisibility(checked);
                    }
                    if(!layer.isBaseLayer || checked) { // only fire for layers which have selected
                      // this is required otherwise there will be two fired for baselayer changes, one for the old being unchecked, and one for the new
                      this.fireEvent("postcheckchange", this);
					}
                }, this);
                
                // set initial checked status
                this.attributes.checked = layer.getVisibility();
            } else {
                ui.hide();
            }
            map.events.register("addlayer", this, function(e) {
                if(layer == e.layer) {
                    this.getUI().show();
                } else if (layer == e.layer.name) {
                    // layer is a string, which means the node has not
                    // yet been rendered because the layer was not found.
                    // But now we have the layer and can render.
                    this.render(bulkRender);
                    return;
                }
            });
            map.events.register("removelayer", this, function(e) {
                if(layer == e.layer) {
                    this.getUI().hide();
                }
            });
        }
        GeoExt.tree.LayerNode.superclass.render.call(this, bulkRender);
    }
});

/**
 * Constant: GeoExt.tree.LayerNode.EVENT_TYPES
 * {Array(String)} - supported event types
 * 
 * Event types supported for this class, in additon to the ones inherited
 * from {<GeoExt.tree.TristateCheckboxNode>}:
 * - *querylayerchange* notifies listener when the query layer has
 *     changed. Will be called with the new query layer as argument.
 */
GeoExt.tree.LayerNode.EVENT_TYPES = ["querylayerchange", "postcheckchange"];

/**
 * NodeType: gxLayer
 */
Ext.tree.TreePanel.nodeTypes.gxLayer = GeoExt.tree.LayerNode;