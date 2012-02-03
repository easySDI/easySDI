/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */

Ext.namespace("EasySDI_Map");
Ext.namespace("SData");

EasySDI_Map.PrecisionTree = Ext.extend(Ext.data.Tree, {
  constructor : function(config)
  {
    EasySDI_Map.PrecisionTree.superclass.constructor.apply(this, arguments);
    // Define the root node
    this._rootNode = new Ext.tree.TreeNode({ id : 'precisionRoot' });
    // Load layers from SData
    this._loadLayers();
    // Set the root node
    this.setRootNode(this._rootNode);
  },

  _loadLayers : function()
  {
    var loadedState = Ext.state.Manager.get('precisionLayerState', false);
    var tree = this;
    var first = true;
    for (var geom in SData.searchPrecisions) {
      var p = SData.searchPrecisions[geom];
      // If no state, use first precision as the selected one.
      //p.active = (loadedState && loadedState[geom]);
      p.active = (loadedState && loadedState[geom]); 
      		//||
          //(!loadedState && first)
          //|| first; // TODO: remove this last line - it forces first item to always be loaded
      // A geom is required in the request if it is active, or it is the replacement for another precision
      // at low scale.
      if (p.minScale && p.lowScaleSwitchTo) {
        SData.searchPrecisions[p.lowScaleSwitchTo].required = true;
      }
      p.required = p.required || p.active;
      this._rootNode.appendChild(new Ext.tree.TreeNode({
        text : p.title,
        id: 'p_'+geom, // the id must be stored so we can find the geom from the node.
        checked : p.active,
        iconCls: 'grid',
        listeners : {
          'checkchange' : function(node, checked){
            // TODO: Select or deselect. Load layers if required. Save state
            var geom=node.id.substring(2);
            SData.searchPrecisions[geom].active = checked;
            SData.searchPrecisions[geom].required = SData.searchPrecisions[geom].required || checked;
          }
        },
        allowDrag : false
      }));
      // Since we are iterating all the search precisions, we might as well set up their styles here
      if (typeof p.style !== "undefined") {
        Ext.applyIf(p.style, OpenLayers.Feature.Vector.style['default']);
      }
      first=false;
    }
  },

  _saveState : function()
  {
    //Ext.state.Manager.set('precisionLayerState', this._layerState);
  }
});