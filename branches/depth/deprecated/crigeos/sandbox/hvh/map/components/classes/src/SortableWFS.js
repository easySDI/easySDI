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
 * OpenLayers.Format.Filter.v1_0_0_Sortable
 * A subclass of the OpenLayers Filter v1.0 class which extends the ability to write XML to include
 * support for the WFS SortBy feature.
 */
OpenLayers.Format.Filter.v1_0_0_Sortable = OpenLayers.Class(
  OpenLayers.Format.Filter.v1_0_0, {

  initialize: function(options) {
    OpenLayers.Format.Filter.v1_0_0.prototype.initialize.apply(this, [options]);
  },

  /**
   * Extend the writers to include ogc:SortBy support.
   */
  writers: {
    "ogc": OpenLayers.Util.applyDefaults({
        "SortBy": function(sort) {
            var sortByEl = this.createElementNSPlus("ogc:SortBy");
            var propEl = this.createElementNSPlus("ogc:SortProperty");
            var nameEl = this.createElementNSPlus("ogc:PropertyName", {value: sort.sortField});
            propEl.appendChild(nameEl);
            if (sort.sortDir) {
              var dirEl = this.createElementNSPlus("ogc:SortOrder", {value: sort.sortDir});
              propEl.appendChild(dirEl);
            }
            sortByEl.appendChild(propEl);

            return sortByEl;
        },
        "PropertyIsLike": function(filter) {
          // new version allows setting of matchcase
          // this is outside the spec but still seems to work
            var node = this.createElementNSPlus("ogc:PropertyIsLike", {
                attributes: {
                    wildCard: "*", singleChar: ".", escape: "!", matchCase: filter.matchCase
                }
            });
            // no ogc:expression handling for now
            this.writeNode("PropertyName", filter, node);
            // convert regex string to ogc string
            this.writeNode("Literal", filter.regex2value(), node);
            return node;
        }
        }, OpenLayers.Format.Filter.v1_0_0.prototype.writers["ogc"])
  },

  CLASS_NAME: "OpenLayers.Format.Filter.v1_0_0_Sortable"
});

/**
 * OpenLayers.Format.WFST.v1_0_0_Sortable
 * Extend the WFST v1.0 OpenLayers Format, which is used to write WFS Protocol requests,
 * so that it can support ogc:SortBy. Depends on the Filter.v1_0_0_Sortable to write the
 * node content.
 */
OpenLayers.Format.WFST.v1_0_0_Sortable = OpenLayers.Class(
  OpenLayers.Format.Filter.v1_0_0_Sortable, OpenLayers.Format.WFST.v1_0_0, {

  initialize: function(options) {
    OpenLayers.Format.Filter.v1_0_0_Sortable.prototype.initialize.apply(this, [options]);
    OpenLayers.Format.WFST.v1_0_0.prototype.initialize.apply(this, [options]);
  },

    /**
     * Property: writers
     * As a compliment to the readers property, this structure contains public
     *     writing functions grouped by namespace alias and named like the
     *     node names they produce.
     */
    writers: {
        "wfs": OpenLayers.Util.applyDefaults({
            "Query": function(options) {
                options = OpenLayers.Util.extend({
                    featureNS: this.featureNS,
                    featurePrefix: this.featurePrefix,
                    featureType: this.featureType,
                    srsName: this.srsName
                }, options);
                var node = this.createElementNSPlus("wfs:Query", {
                    attributes: {
                		//typeName: (options.featureNS ? options.featurePrefix + ":" : "") +
                        typeName: (options.featurePrefix ? options.featurePrefix + ":" : "") +
                            options.featureType,
                        srsName: options.srsName
                    }
                });
                if(options.featureNS) {
                    node.setAttribute("xmlns:" + options.featurePrefix, options.featureNS);
                }
                if(options.propertyNames) {
                    for(var i=0,len = options.propertyNames.length; i<len; i++) {
                        this.writeNode(
                            "wfs:PropertyName",
                            {property: options.propertyNames[i]},
                            node
                        );
                    }
                }
                if(options.filter) {
                    this.setFilterProperty(options.filter);
                    this.writeNode("ogc:Filter", options.filter, node);
                }
                // The additional bit - sortBy support.
                if(options.sort && options.sort.sortField) {
                    this.writeNode("ogc:SortBy", options.sort, node);
                }
                return node;
            }
        }, OpenLayers.Format.WFST.v1_0_0.prototype.writers["wfs"]),
        "gml": OpenLayers.Format.GML.v3.prototype.writers["gml"],
        "feature": OpenLayers.Format.GML.v3.prototype.writers["feature"],
        "ogc": OpenLayers.Format.Filter.v1_0_0_Sortable.prototype.writers["ogc"]
    },

  CLASS_NAME: "OpenLayers.Format.WFST.v1_0_0_Sortable"

});