/** 
 * Copyright (c) 2008 The Open Planning Project 
 */ 
Ext.namespace("GeoExt.tree"); 

/** 
 * Class: GeoExt.tree.TristateCheckboxNodeUI 
 *  
 * Inherits from: 
 * - <Ext.tree.TreeNodeUI> 
 */ 
GeoExt.tree.TristateCheckboxNodeUI = Ext.extend(Ext.tree.TreeNodeUI, { 

    /** 
     * Constructor: GeoExt.tree.TristateCheckbosNodeUI 
     *  
     * Parameters: 
     * config - {Object} 
     */ 
    constructor: function(config) { 
        GeoExt.tree.TristateCheckboxNodeUI.superclass.constructor.apply(this, arguments); 
    }, 

    /** 
     * Method: toggleCheck 
     *  
     * Parameters: 
     * value - {Boolean} checked status 
     * thirdState - {Boolean} 
     * options - {Object} Hash of options for this method 
     *  
     * Currently supported options: 
     * silent - {Boolean} set to true if no checkchange event should be 
     *     fired 
     */ 
    toggleCheck: function(value, thirdState, options) { 
        var cb = this.checkbox; 
        if(thirdState == true) { 
            if(cb) { 
                Ext.get(cb).setOpacity(0.5); 
            } 
            this.node.attributes.thirdState = true; 
        } else { 
            if(cb) { 
                Ext.get(cb).clearOpacity(); 
            } 
            delete this.node.attributes.thirdState; 
        } 
        if(options && options.silent == true){ 
            this.node.suspendEvents(); 
        } 
        GeoExt.tree.TristateCheckboxNodeUI.superclass.toggleCheck.call(this, 
            value); 
        this.node.resumeEvents(); 
    } 
}); 
