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

/**
 * A selection model to attach to grids which synchronises the selection of a set of precision layers with the
 * selection in the grid.
 * Selecting a single feature in the grid or on the map will select all the different precisions of that same feature
 * on the map.
 * Set the layers config to an array of layers.
 */

EasySDI_Map.FeatureSelectionModel = function(cfg) {
    EasySDI_Map.FeatureSelectionModel.superclass.constructor.apply(this, arguments);
    Ext.each(this.layers, function(layer) {
      layer.events.on({
          "featureselected": function(evt) {
              var row = this.grid.store.findBy(function(record, id){
                  return record.data.feature.fid == evt.feature.fid;
              });
              // Select row in main grid
              if (!this.isSelected(row)){
                  this.selectRow(row, true);
              }
              // and add row to selected grid
              var selectionRow = this.selectList.store.findBy(function(record, id){
                  return record.data.feature == evt.feature;
              });
              if (selectionRow==-1) {
                var record=this.grid.store.getAt(row);
                this.selectList.store.add([record]);
              }
              // Check the feature is selected in all layers
              Ext.each(this.layers, function(layer) {
                var feature=this._getFeatureByFid(layer, evt.feature.fid);
                if (typeof feature !== "undefined" && 
                    OpenLayers.Util.indexOf(layer.selectedFeatures, feature)==-1) {
                  layer.selectedFeatures.push(feature);
                  this.selectControl.highlight(feature);
                }
              }, this);
              return true;
          },
          "featureunselected": function(evt) {
              var row = this.grid.store.findBy(function(record, id) {
                  return record.data.feature == evt.feature;
              });
              // Deselect row in main grid
              if (this.isSelected(row)){
                  this.deselectRow(row);
              }
              // And remove from selection grid
              var selectionRow = this.selectList.store.findBy(function(record, id){
                  return record.data.feature == evt.feature;
              });
              if (selectionRow!=-1) {
                this.selectList.store.removeAt(selectionRow);
              }
              // Check the feature is unselected in all layers
              Ext.each(this.layers, function(layer) {
                var feature=this._getFeatureByFid(layer, evt.feature.fid);
                if (typeof feature !== "undefined") {
                  OpenLayers.Util.removeItem(layer.selectedFeatures, feature);
                  this.selectControl.unhighlight(feature);
                }
              }, this);
              return true;
          },

          scope: this
      });
    }, this);

    this.on("rowselect", function(model, row, record) {
        if (this.layers[0].selectedFeatures.indexOf(record.data.feature) == -1)
          this.selectControl.select(record.data.feature);
    }, this);

    this.on("rowdeselect", function (model, row, record){
        if (this.layers[0].selectedFeatures.indexOf(record.data.feature) != -1)
          this.selectControl.unselect(record.data.feature);
    }, this);
};

Ext.extend(EasySDI_Map.FeatureSelectionModel, Ext.grid.RowSelectionModel, {
  _getFeatureByFid: function(layer, fid) {
    var feature = null;
    for(var i=0, len=layer.features.length; i<len; ++i) {
      if(layer.features[i].fid == fid) {
        feature = layer.features[i];
        break;
      }
    }
    return feature;
  },
  
  onRefresh : function(){
        var ds = this.grid.store, index;
        var s = this.getSelections();
        this.clearSelections(true);
        for(var i = 0, len = s.length; i < len; i++){
            var r = s[i];
            if((index = ds.indexOfId(r.id)) != -1){
                this.selectRow(index-this.grid.view.offset, true);
            }
        }
        if(s.length != this.selections.getCount()){
            this.fireEvent("selectionchange", this);
        }
    },

  selectRow: function(index, keepExisting, preventViewNotify){
    if(this.isLocked() || (index < 0 || index >= this.grid.store.getCount()) || this.isSelected(index)) return;
      var r = this.grid.store.getAt(index+this.grid.view.offset);    
    if(r && this.fireEvent("beforerowselect", this, index, keepExisting, r) !== false){
	  if(!keepExisting || this.singleSelect){
	    this.clearSelections();
	  }
	  this.selections.add(r);
	  this.last = this.lastActive = index;
	  if(!preventViewNotify){
	    this.grid.getView().onRowSelect(index);
	  }
	  this.fireEvent("rowselect", this, index, r);
	  this.fireEvent("selectionchange", this);
    }
  },
  
  clearSelections : function(fast){
    if(this.isLocked()) return;
    if(fast !== true){
      var ds = this.grid.store;
      var s = this.selections;
      var index;
      s.each(function(r){
        index=ds.indexOfId(r.id)-this.grid.view.offset;
        this.deselectRow(index);                
      }, this);
      s.clear();
    } else {        
      this.selections.clear();        
    }
    // We also need to clear the layer selections, which could be on other grid pages so not handled
    this.selectControl.unselectAll();
    this.last = false;
    this.fireEvent("selectionchange", this);
  }
  
});