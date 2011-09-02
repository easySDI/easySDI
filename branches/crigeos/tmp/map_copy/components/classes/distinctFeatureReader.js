/**
* EasySDI, a solution to implement easily any spatial data infrastructure
* Copyright (C) EasySDI Community
* For more information : www.easysdi.org
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
 *
 */

EasySDI_Map.DistinctFeatureReader = Ext.extend(GeoExt.data.FeatureReader, {
  // config option to set the field that will be used to determine the distinct rows to include.
  distinct : null,

  constructor: function(config) {
    this.distinct = config.distinct;
    // Call parent constructor
    EasySDI_Map.DistinctFeatureReader.superclass.constructor.apply(this, arguments);
  },

  /**
   * Replace the readRecords method to skip features which we have already loaded based on the
   * distinct field. Basically a hack for WFS not supporting a distinct query.
   */
  readRecords : function(features) {
    var records = [];
    // array to track the distinct values we have already loaded
    var distinctVals = [];

    if (features) {
      var recordType = this.recordType, fields = recordType.prototype.fields;
      var i, lenI, j, lenJ, feature, values, field, v;
      for (i = 0, lenI = features.length; i < lenI; i++) {
        feature = features[i];
        // Is this a new distinct record?
        if (distinctVals.indexOf(feature.data[this.distinct])==-1) {
          values = {};
          if (feature.attributes) {
            for (j = 0, lenJ = fields.length; j < lenJ; j++){
              field = fields.items[j];
              if (/[\[\.]/.test(field.mapping)) {
                try {
                  v = new Function("obj", "return obj." + field.mapping)(feature.attributes);
                } catch(e){
                  v = field.defaultValue;
                }
              }
              else {
                v = feature.attributes[field.mapping || field.name] || field.defaultValue;
              }
              v = field.convert(v);
              values[field.name] = v;
            }
          }
          values.feature = feature;
          values.state = feature.state;
          values.fid = feature.fid;

          // store this record so we don't add a duplicate
          distinctVals.push(feature.data[this.distinct]);

          records[records.length] = new recordType(values, feature.id);
        }
      }
    }

    return {
      records: records,
      totalRecords: this.totalRecords != null ? this.totalRecords : records.length
    };
}
});