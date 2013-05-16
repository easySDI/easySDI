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

Ext.BLANK_IMAGE_URL = "../../../externals/ext/resources/images/default/s.gif";

Ext.namespace("EasySDI_Map");

/*
* Add an i18n instance to the namespace.
*/
EasySDI_Map.lang = new i18n();

/*
* State manager
*/
Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

/**
 * Base class for the layout viewport. Contains all functionality that is not specific to the actual layout on screen.
 * This is then subclassed to provide the actual layout, making it easy to produce new layouts for the map component.
 */
EasySDI_Map.LayoutBase = Ext.extend(Ext.Panel, {
//EasySDI_Map.LayoutBase = Ext.extend(EasySDI_Map.Viewport, {
  mapPanel: null,
  searchPanel: null,
  searchManager: null,
  gridPanel: null,
  
  constructor: function(config) {
    this.searchManager = new EasySDI_Map.SearchManager(this.searchPanel, this, this.mapPanel, this.precisionTree);
    this.searchPanel.registerTrigger('doSearch', this.searchManager.doSearch.createDelegate(this.searchManager));    
    this.searchPanel.registerTrigger('addSearchBar', this.searchManager.addSearchBar.createDelegate(this.searchManager));
    this.searchPanel.registerTrigger('removeSearchBar', this.searchManager.removeSearchBar.createDelegate(this.searchManager));
    this.searchPanel.registerTrigger('clearSearchBar', this.searchManager.clearSearchBar.createDelegate(this.searchManager));
    this.searchPanel.registerTrigger('zoomToExtent', this.mapPanel.zoomToExtent.createDelegate(this.mapPanel));    
    this.searchManager.registerTrigger('refreshLegend', this.legendPanel.refresh.createDelegate(this.legendPanel));
    this.searchManager.registerTrigger('hideFilterPanel', this.hideFilterPanel.createDelegate(this));	
    this.gridPanel.registerTrigger('zoomToExtent', this.mapPanel.zoomToExtent.createDelegate(this.mapPanel));
    this.gridPanel.registerTrigger('enableWfsSearch', this.searchManager.enableWfsSearch.createDelegate(this.searchManager));
    this.gridPanel.registerTrigger('highlightFeature', this.mapPanel.highlightFeature.createDelegate(this.mapPanel));
    this.gridPanel.registerTrigger('clearSelection', this.mapPanel.clearSelection.createDelegate(this.mapPanel));
    this.gridPanel.registerTrigger('featurePopup', this.displayFeaturePopup.createDelegate(this));
    this.mapPanel.registerTrigger('featurePopup', this.displayFeaturePopup.createDelegate(this));
    this.mapPanel.registerTrigger('refreshLegend', this.legendPanel.refresh.createDelegate(this.legendPanel));
    this.layerTree.registerTrigger('refreshLegend', this.legendPanel.refresh.createDelegate(this.legendPanel));
    this.searchManager.addSearchBar();
    EasySDI_Map.LayoutBase.superclass.constructor.apply(this, arguments);
    this.mapPanel.navHistoryCtrl.clear();
  },

  /**
   * Display the popup dialog for a feature, allowing comments to be added if
   * logged in and commenting available.
   * param config.gridView - gridView allowing a refresh if commetn count increases
   * param config.row - data row containing the unique id and comment count attributes.
   */
  displayFeaturePopup: function(featureId) {
    var popup=new EasySDI_Map.FeaturePopup({
      featureId: featureId,
      featureType: SData.searchLayer.featureType.replace('{geom}',''),
      title: EasySDI_Map.lang.getLocal('MP_FEATURE_POPUP_TITLE')
    });
    popup.registerTrigger('incCommentCount', this.gridPanel.incCommentCount.createDelegate(this.gridPanel));
    popup.show();
  }

});