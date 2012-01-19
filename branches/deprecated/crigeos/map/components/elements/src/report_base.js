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

 // Localisation prefix RB

//Ext.BLANK_IMAGE_URL = "../../../externals/ext/resources/images/default/s.gif";

Ext.namespace("EasySDI_Map");

/*
* Add an i18n instance to the namespace.
*/
EasySDI_Map.lang = new i18n();

/**
 * EasySDI_Map.ReportBase
 * Base class for single item details and grid reports. These are onscreen reports, not downloads.
 */

EasySDI_Map.ReportBase = Ext.extend(Ext.Viewport, {
  constructor: function(config) {
    config.layout = "border";
    this.rptPanel = new Ext.Panel({region: "center", layout: "border"});
    this.gridPanel = new Ext.Panel({
      region: "center",
      border: false,
      layout: "fit",
      style: "padding-left: 10px;"
    });
    this.rptPanel.add(this.gridPanel);
    config.items =  [
      new Ext.Panel({
        region: "north",
        id: "banner",
        layout: "border",
        items: [
          {
          	html: '<div id="banner-bg" ><img src="templates/easysdi_map/images/gouvernement.gif" />'+
        		      '<img src="templates/easysdi_map/images/topvisu.gif" /></div>',          	
          	region: "center",
          	border: false
          },
          {
          	html: '<img src="templates/easysdi_map/images/gouvernementLogo.png" alt="' + 
          			EasySDI_Map.lang.getLocal("Grande_Duche") + '" />',          	
          	region: "east",
          	width: 143,
          	border: false
          }
        ],                
        border: false
      }),
      this.rptPanel
    ];
    // decode the URL to use elsewhere
    this.url = Ext.urlDecode(location.search.substring(1));
    if (this.url.type !== undefined) {
    	this.usableType = this.url.type.replace('{geom}','');
    }

    // allow subclasses to inject their own controls.
    this.addExtraControls();

    EasySDI_Map.ReportBase.superclass.constructor.apply(this, arguments);
  },

  /**
   * Function stub that can be overridden to add extra controls to the page in subclasses.
   */
  addExtraControls: function() {

  }
});