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

 Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

 // Localisation prefix PGR

/**
 * The main class for rendering the print grid page

  *@todo This class functionality for loading the filter prototype should be moved to a separate class
  * and mixed in, so it can be shared with the other reports.
 */
PrintGrid = function(filterString) {
	
  var filter = this._convertToFilter(filterString);
  // decode the page URL to use it's GET parameters
  this.url = location.href.split('?')[0];
  this._readFeatureInfo();

  // Issue a DescribeFeatureType request to find out the report content.
  this.protocol = new OpenLayers.Protocol.WFS({
    url: componentParams.proxiedPubWfsUrl,
    featureNS: componentParams.pubFeatureNS,
    featurePrefix: componentParams.pubFeaturePrefix,
    featureType: this.usableFeatureType,
    geometryName: null,
    srsName: componentParams.projection,
    version: componentParams.pubWfsVersion,
    filter: filter,
    propertyNames: this.attrs,
    maxFeatures: componentParams.maxFeatures
  });
  this.protocol.read({callback: this._handleFeatures.createDelegate(this)});
};

/**
 * Build a grid from the list of features obtained.
 * TODO: sort by sort: {
      sortField: sortField,
      sortDir: sortDir
    },
 */
PrintGrid.prototype._handleFeatures = function(response) {
  var feature = {}, colIndex, catAndName;
  var table='<table>';
  table += '<thead><tr>';
  for (colIndex=0; colIndex<this.attrs.length; colIndex++) {
    // Split the category/name of the column header
    catAndName=EasySDI_Map.lang.getLocal('COL_' + this.attrs[colIndex]).split('/');
    table += '<th>' + catAndName[1] + '</th>';
  }
  table += '</tr></thead><tbody>';
  // iterate the list of features
  for (var fIndex=0; fIndex<response.features.length; fIndex++) {
    feature = response.features[fIndex];
    table += '<tr>';
    // For each feature, iterate out the column data
    for (colIndex=0; colIndex<this.attrs.length; colIndex++) {
      var value=feature.data[this.attrs[colIndex]];
      if (value==null) value='';
      table += '<td>' + value + '</td>';
    }
    table += '</tr>';
  }
  table += '</tbody></table>';
  // Put the HTML table into the DOM.
  var div=document.getElementById('printreport');
  div.innerHTML = table;
};

/**
 * Convert the filter POST data into an XML DOM element.
 */
PrintGrid.prototype._convertToFilter = function(filterString) {
  // Get our XML as a DOM object
  try //Internet Explorer
  {
    xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
    xmlDoc.async="false";
    xmlDoc.loadXML(filterString);
  } catch(e) {
    parser=new DOMParser();
    xmlDoc=parser.parseFromString(filterString,"text/xml");
  }
  // Now OL can turn this into a filter object
  var format = new OpenLayers.Format.Filter({version:"1.0.0"});
  var filter = format.read(xmlDoc.firstChild);

  return filter;
};

PrintGrid.prototype._readFeatureInfo = function() {
  this.usableFeatureType = featureType.replace('{geom}','');
  // Get the attrs from the cookie if the grid state is saved
  var visibleCols = Ext.state.Manager.get(this.usableFeatureType + 'Cols', null);
  this.attrs = visibleCols;
  if (this.attrs==null) {
    // No saved grid state
    this.attrs = SData.defaultAttrs[this.usableFeatureType];
  } else {
    // just in case there is a problem in the cookie, check all the columns exist
    this.attrs=[];
    Ext.each(SData.attrs[this.usableFeatureType], function(attr) {
      // Note, invisible attrs should always be requested as they are normally keys
      if (visibleCols.indexOf(attr.name)!=-1) {
        this.attrs.push(attr.name);
      }
    }, this);
  }
  console.log(this.attrs);
}