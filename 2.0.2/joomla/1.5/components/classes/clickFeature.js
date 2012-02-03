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

 /**
  * An OpenLayers control to detect mouse clicks on features in the search layers.
  */
 OpenLayers.Control.ClickFeature = OpenLayers.Class(OpenLayers.Control, {

    /**
     * APIProperty: onClick
     * {Function} Optional function to be called when a feature is clicked.
     * The function should expect to be called with a feature.
     */
    onClick: function() {},

    /**
     * APIProperty: geometryTypes
     * {Array(String)} To restrict selecting to a limited set of geometry types,
     *     send a list of strings corresponding to the geometry class names.
     */
    geometryTypes: null,

    /**
     * Property: layer
     * {<OpenLayers.Layer.Vector>}
     */
    layer: null,

    /**
     * APIProperty: callbacks
     * {Object} The functions that are sent to the handlers.feature for callback
     */
    callbacks: null,

    /**
     * Property: handlers
     * {Object} Object with references to multiple <OpenLayers.Handler>
     *     instances.
     */
    handlers: null,

    /**
     * Constructor: <OpenLayers.Control.ClickFeature>
     *
     * Parameters:
     * layer - {<OpenLayers.Layer.Vector>}
     * options - {Object}
     */
    initialize: function(layer, options) {
        OpenLayers.Control.prototype.initialize.apply(this, [options]);
        this.layer = layer;
        var callbacks = {
            click: this.clickFeature
        };

        this.callbacks = OpenLayers.Util.extend(callbacks, this.callbacks);
        this.handlers = {
            feature: new OpenLayers.Handler.Feature(
                this, layer, this.callbacks, {geometryTypes: this.geometryTypes}
            )
        };
    },

    /**
     * Method: activate
     * Activates the control.
     *
     * Returns:
     * {Boolean} The control was effectively activated.
     */
    activate: function () {
        if (!this.active) {
            this.handlers.feature.activate();
        }
        return OpenLayers.Control.prototype.activate.apply(
            this, arguments
        );
    },

    /**
     * Method: deactivate
     * Deactivates the control.
     *
     * Returns:
     * {Boolean} The control was effectively deactivated.
     */
    deactivate: function () {
        if (this.active) {
            this.handlers.feature.deactivate();
        }
        return OpenLayers.Control.prototype.deactivate.apply(
            this, arguments
        );
    },

    /**
     * Method: clickFeature
     * Called on click in a feature
     *
     * Parameters:
     * feature - {<OpenLayers.Feature.Vector>}
     */
    clickFeature: function(feature) {
      var cont = this.layer.events.triggerEvent("beforefeatureclicked", {
        feature: feature
      });
      if(cont !== false) {
        this.layer.events.triggerEvent("featureclicked", {feature: feature});
        this.onClick(feature);
      }
    },

    /**
     * Method: setMap
     * Set the map property for the control.
     *
     * Parameters:
     * map - {<OpenLayers.Map>}
     */
    setMap: function(map) {
        this.handlers.feature.setMap(map);
        if (this.box) {
            this.handlers.box.setMap(map);
        }
        OpenLayers.Control.prototype.setMap.apply(this, arguments);
    },

    CLASS_NAME: "OpenLayers.Control.ClickFeature"
});
