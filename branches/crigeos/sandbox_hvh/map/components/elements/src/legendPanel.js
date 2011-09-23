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

EasySDI_Map.LegendPanel = function(config){
    Ext.apply(this, config);
    EasySDI_Map.LegendPanel.superclass.constructor.call(this);
};

EasySDI_Map.LegendPanel.LEGENDURL = 1;
EasySDI_Map.LegendPanel.GETLEGENDGRAPHIC = 0;

EasySDI_Map.LegendPanel = Ext.extend(Ext.Panel, {

  layerTree: null,

  constructor: function(config) {
    // Call parent constructor
    EasySDI_Map.LegendPanel.superclass.constructor.apply(this, arguments);
  },

  /**
   * Load the legend according to the layerTree's layers
   */
  refresh : function() {    
    // Clear the legend if previously initialised
    if (typeof this.items !== "undefined") {
      this.removeAll(true);
    }
    
    //Get all the layers on the map
    var layersToDo = [].concat(this.map.layers);
    
    //Remove from this array the layers present in the layer tree : the overlay and base layers.
    //So we keep only the annotation layers and the search result layers
    Ext.each(this.layerTree.root.childNodes, function(group) {
      Ext.each(group.childNodes, function(layerNode) {
      	OpenLayers.Util.removeItem(layersToDo, layerNode.layer);
      	}, this);
     }, this);
     
     //Reverse the order of the layers array to match the visual order on the map
     layersToDo.reverse();
     
    // Now build the legend for those layers, they are always on top of the layers present in the layer tree
    Ext.each(layersToDo, function(layer) {    
    	if (layer.displayInLayerSwitcher) 
    	{	
	    	if (layer instanceof OpenLayers.Layer.Vector)
		    {
		      	//Annotation layer
		      	//Check if layer contains object
		      	if(layer.features.length > 0)
			     {
			     	var styleToApply = '';
			     	var styleToApplyToLine = '';
			     	var styleToApplyToPoint = '';
			     	var bodystyleToApply = '';
			     	Ext.each(annotationStyle, function(style) 
			     	{
				    	if(style.text == layer.name)
				    	{
				    		styleToApply = "border: solid " + style.strokeColor  + " 1px; ";
				    		styleToApplyToLine = "border-top: solid 1px "+ style.strokeColor + ";border-left: solid 1px "+ style.strokeColor + ";";
				    		bodystyleToApply= "background-color: " + style.fillColor + "; opacity:" + style.fillOpacity + ";filter:alpha(opacity=" + style.fillOpacity*100+ ")";
				    		styleToApplyToPoint = "background-image:url('"+style.externalGraphic+"');";				              		    		
				    	}
				    });
				   var panel = new Ext.Panel({
										        layout: 'column',
										        border: false,
										        items: [
										          new Ext.form.Label({text: layer.name}),
										          new Ext.Panel({
													              style: '',       
													              bodyStyle :'"' + styleToApplyToPoint +'"',  
													              cls: "legend-icon",
													              border: false
													            }),
									            new Ext.Panel({
													              style: '"' + styleToApplyToLine + '"',       
													              bodyStyle :'',  
													              cls: "graphic",
													              border: false
													            }),
									            new Ext.Panel({
													              style: '"'+styleToApply+'"',       
													              bodyStyle :'"'+bodystyleToApply+'"',  
													              cls: "graphic",
													              border: false
													            })
										        ]
											  });
					
		            this.add(panel);
		      	}
		      	
		      }
		      else
		      {
		      	//Search result
		    	var panel = new Ext.Panel({
		        layout: 'column',
		        baseCls: 'legend x-panel',
		        border: false,
		        items: [
		          new Ext.form.Label({text: layer.name})
		        ]
			      });
			      if (layer instanceof OpenLayers.Layer.WMS) 
			      {
			      	this._addServerWMSLegend(panel, layer);	        
			      } 	      
			      else 
			      {
		            panel.add(new Ext.Panel({
		              style: "border: solid #3399ff 1px;",              
		              cls: "graphic",
		              border: false
		            }));
		          }
			      this.add(panel);
			    }
		    }
    }, this);
    
    //Now do the layers of the layer tree : base and overlay layers.
    Ext.each(this.layerTree.root.childNodes, function(group) {
      Ext.each(group.childNodes, function(layerNode) {
      	//OpenLayers.Util.removeItem(layersToDo, layerNode.layer);
        if (this._isVisible(layerNode) && this._isInScaleRange(layerNode)) {
          var panel = new Ext.Panel({
            layout: 'column',
            border: false,
            baseCls: 'legend x-panel',
            items: [
              new Ext.form.Label({text: layerNode.text})
            ]
          });
          if (layerNode.fillColor !== null && typeof layerNode.strokeColor !== "undefined") {
            // layer has user-defined styling attached
            panel.add(new Ext.Panel({
              style: "border: solid #" + layerNode.strokeColor + " 1px;",
              bodyStyle: "background-color: #" + layerNode.fillColor + "; opacity:" + layerNode.opacity + ";filter:alpha(opacity=" + layerNode.opacity*100+ ")",
              cls: "graphic",
              border: false
            }));
          } else {
            // No user styling. If WMS, ask the server for a legend, otherwise show a WFS default style
            if (layerNode.layer instanceof OpenLayers.Layer.WMS) {
              if (layerNode.layer.isBaseLayer) {
			      		panel.add(new Ext.Panel({
			      			  width: 18, 
			      			  height: 18, 
			      			  style: "margin: 3px;",
			      			  bodyStyle: "opacity: 0",
		                cls: "baseLayer",
		                border: false
		              }));
	          } else {         	
                this._addServerWMSLegend(panel, layerNode.layer);
              }
            } else {
              panel.add(new Ext.Panel({
                style: "border: solid #ee9900 1px;",
                bodyStyle: "background-color: #ee9900; opacity: 0.4; filter:alpha(opacity=50)",
                cls: "graphic",
                border: false
              }));
            }
          }
          this.add(panel);
        }        
      }, this);
    }, this);
    
    if (this.rendered) {
      this.doLayout();
    }
  },

  /**
   * Returns true if a layer should be visible. The method used depends on whether this is
   * pre-render or post render, because the checkbox state only works after render, whereas the layer's
   * visibility is not updated on the checkchanged event.
   */
  _isVisible: function(layerNode) {
    // note that at this point we checked whether the checkbox has been rendered, not this.
    if (layerNode.ui.rendered) {
      return layerNode.ui.isChecked() || typeof layerNode.ui.checkbox=="undefined";
    } else if (layerNode.layer !== undefined) {
      // pre-render
      return layerNode.layer.getVisibility() || typeof layerNode.layer.getVisibility=="undefined";
    } else {
      return false;
    }
  },
  
  /**
   *
   */
  _isInScaleRange: function(layerNode) {
  
    if (layerNode.isInScaleRange || layerNode.isInScaleRange == "undefined") {
      return true;
    } else {
      return false;
    }
  },

  /**
   * For a WMS layer not styled locally, we need to ask the server for a legend graphic
   */
  _addServerWMSLegend: function(panel, layer) {
    // if LAYERS param is in the form of LAYERS=A,B,C we need to
    //split them up, and show an image per layer
    var layers = layer.params.LAYERS.split(",");
    var legImg = [];
    for (var i=0; i<layers.length; i++) {
      var layerName = layers[i];
      legImg.push(this._createImage(
        (layer.legendURL ? layer.legendURL : this._getLegendUrl(layer, layerName)),
        this._generatePanelId(layer)+i, layer));
    }
    for (i=0; i<legImg.length; i++) {
      panel.add(new Ext.BoxComponent({el: legImg[i]}));
    }
  },

  /**
   * Method: createImage
   *     Create an image object for the legend image
   *
   * Parameters:
   * src - {String} the source of the image (url)
   * id - {String} the id (prefix) for the image object
   * layer - {<OpenLayers.Layer.WMS>}
   *
   * Returns:
   * {DOMElement}
   */
  _createImage: function(src, id, layer) {
    var legendImage = document.createElement("img");
    Ext.EventManager.addListener(legendImage, 'error',
        this.onImageLoadError, {img: legendImage, lyr: layer});
    legendImage.src = src;
    legendImage.id = id+'_img';
    return legendImage;
  },

  /**
   * Retrieve a legend image from a WMS service that is styled on the server.
   * Parameters:
   * layer - {<OpenLayers.Layer.WMS>} the WMS layer
   * layerName - {String} one of the layers from the LAYERS parameter
   *
   * Returns:
   * {String}
   */
  _getLegendUrl: function(layer, layerName) {
    var url;
    if (this.wmsMode == EasySDI_Map.LegendPanel.GETLEGENDGRAPHIC) {
      url = layer.getFullRequestString({
        REQUEST: "GetLegendGraphic",
        WIDTH: null,
        HEIGHT: null,
        EXCEPTIONS: "application/vnd.ogc.se_xml",
        LAYER: layerName,
        LAYERS: null,
        STYLE: layer.params.STYLES,
        STYLES: null,
        FILTER: null,
        SRS: null,
        FORMAT: this.wmsLegendFormat,
        VERSION: layer.params.VERSION
      });
      layer.legendURL = url;
      return url;
    }
  },

  /**	
   * Method: generatePanelId
   *     Generate an id attribute value for the panel.
   *     It is assumed that the combination of layer.params.LAYER and
   *     layer.mame is unique.
   *
   * Parameters:
   * layer - {<OpenLayers.Layer.WMS>} the layer object
   *
   * Returns:
   * {String}
   */
  _generatePanelId: function(layer) {
    if (layer && layer.params) {
      return this.idPrefix + layer.params.LAYERS + layer.name;
    }
  },

  /**
   * Method: onImageLoadError
   *     When the image fails loading (e.g. when the server returns an XML
   *     exception) we need to set the src to a blank image otherwise IE
   *     will show the infamous red cross.
   */
  onImageLoadError: function() {
	 
      this.img.src = Ext.BLANK_IMAGE_URL;
      this.lyr.legendURL = null;
  }

});