/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community For more information : www.easysdi.org
 * 
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version. This
 * program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see http://www.gnu.org/licenses/gpl.html.
 */

// Localisation prefix 02
Ext.namespace("EasySDI_Map");

/**
 * A plugin which displays the panel title when it is collapsed if oriented
 * vertically. Set this to the plugin option when setting up a panel.
 */
Ext.ux.collapsedPanelTitlePlugin = function() {
	this.init = function(p) {
		if (p.collapsible) {
			var r = p.region;
			if ((r == 'north') || (r == 'south')) {
				p.on('render', function() {
					var ct = p.ownerCt;
					ct.on('afterlayout', function() {
						if (ct.layout[r].collapsedEl) {
							p.collapsedTitleEl = ct.layout[r].collapsedEl.createChild( {
								tag : 'span',
								cls : 'x-panel-collapsed-text',
								html : p.title
							});
							p.setTitle = Ext.Panel.prototype.setTitle.createSequence(function(t) {
								p.collapsedTitleEl.dom.innerHTML = t;
							});
						}
					}, false, {
						single : true
					});
					p.on('collapse', function() {
						if (ct.layout[r].collapsedEl && !p.collapsedTitleEl) {
							p.collapsedTitleEl = ct.layout[r].collapsedEl.createChild( {
								tag : 'span',
								cls : 'x-panel-collapsed-text',
								html : p.title
							});
							p.setTitle = Ext.Panel.prototype.setTitle.createSequence(function(t) {
								p.collapsedTitleEl.dom.innerHTML = t;
							});
						}
					}, false, {
						single : true
					});
				});
			}
		}
	};
};

/**
 * Standard layout class for the web map module. Places each UI element in the
 * correct place.
 */
EasySDI_Map.RwgLayout = Ext
		.extend(
				EasySDI_Map.LayoutBase,
				{
					constructor : function(config) {
						Ext.QuickTips.init();
						this._initPanels();
						config.layout = "border";
						var treePanelWidth = (componentParams.treePanelWidth != undefined) ? parseInt(componentParams.treePanelWidth) : 300;
						var layersAndPrecisions;
						if (componentParams.authorisedTo.DATA_PRECISION && componentDisplayOption.DataPrecisionEnable) {
							layersAndPrecisions = new Ext.Panel( {
								region : "west",
								title : EasySDI_Map.lang.getLocal("02000000"), // internationalise
								border : true,
								collapsible : true,
								width : treePanelWidth,
								items : new Ext.TabPanel( {
									activeTab : 0,
									items : [ this.layerPanel, this.dataPrecisionPanel ]
								})
							});
						} else {
							// data precision panel not authorised so just
							// display layers
							layersAndPrecisions = this.layerPanel;
						}
						config.items = [
						// new Ext.Panel({
								// region: "north",
								// id: "banner",
								// layout: "border",
								// items: [
								// {
								// region: "center",
								// border: false
								// },
								// {
								// region: "east",
								// width: 143,
								// border: false
								// }
								// ],
								// border: false
								// }),
								this.searchPanel, layersAndPrecisions, this.legendOrFilterPanel, this.gridPanel, this.mapPanel ];
						EasySDI_Map.RwgLayout.superclass.constructor.apply(this, arguments);

						// Add annotation tool after all initialization process
						this.mapPanel._onAnnotationStyleSelect(null, true);

						// Add the keyMap on the zoomToScale field after layout
						// rendering that the this.mapPanel.zoomToScaleField.id
						// was initialized
						// this.mapPanel.zoomToScaleField can be null if the
						// toolbar is not displayed
						if (componentDisplayOption.zoomToScaleFieldEnable) {
							var keyMap = new Ext.KeyMap(this.mapPanel.zoomToScaleField.id, [ {
								key : Ext.EventObject.ENTER,
								fn : function() {
									this.mapPanel._zoomToScale(this.mapPanel.zoomToScaleField.getValue());
								},
								scope : this
							} ]);
						}

					},

					/**
					 * Construct the panels that are required on startup of the
					 * page
					 */
					_initPanels : function() {
						this.mapPanel = new EasySDI_Map.MapPanel( {
							region : "center",
							overviewWidth : 120,
							overviewHeight : 170
						});
						// this.searchPanel = new
						// EasySDI_Map.SearchPanel({region: "north", border:
						// false});
					this.searchPanel = new EasySDI_Map[extensionClasses.SearchPanelClassName]( {
						region : "north",
						border : false
					});
					this.layerTree = new EasySDI_Map.LayerTree( {
						map : this.mapPanel
					});

					var precisionTree = new EasySDI_Map.PrecisionTree();
					this.layerPanel = new EasySDI_Map.LayerPanel( {
						title : EasySDI_Map.lang.getLocal("02000002"),
						tree : this.layerTree,
						// Occupy the west region of the entire viewport if not
						// in the tab panel
						region : componentParams.authorisedTo.PRECISE_DATA && componentDisplayOption.DataPrecisionEnable ? null : "west"
					// width: componentDisplayOption.DataPrecisionEnable ? 300 :
					// 200
							});

					this.dataPrecisionPanel = new EasySDI_Map.DataPrecisionPanel( {
						title : EasySDI_Map.lang.getLocal("02000001"),
						tree : precisionTree
					});
					this.legendPanel = new EasySDI_Map.LegendPanel( {
						id : 'legend',
						map : this.mapPanel.map,
						autoScroll : true,
						layerTree : this.layerTree
					});
					var legendOrFilterPanelWidth = (componentParams.legendOrFilterPanelWidth != undefined) ? parseInt(componentParams.legendOrFilterPanelWidth)
							: 300;
					this.legendOrFilterPanel = new Ext.Panel( {
						region : "east",
						layout : "card",
						width : legendOrFilterPanelWidth,
						activeItem : 0,
						collapsible : true,
						title : EasySDI_Map.lang.getLocal("02000003"),
						split : true,
						items : [ this.legendPanel
						// the filter panel will be created on demand
						]
					});
					this.gridPanel = new EasySDI_Map.GridPanel( {
						title : EasySDI_Map.lang.getLocal("02000004"),
						plugins : new Ext.ux.collapsedPanelTitlePlugin(),
						region : "south",
						height : 260,
						collapsible : true,
						collapsed : true
					});

					// Register cross-panel events
					this.searchPanel.registerTrigger('showFilterPanel', this.showFilterPanel.createDelegate(this));
				},

				/**
				 * showFilterPanel specific for this layout it replaces the
				 * legend.
				 */
				showFilterPanel : function(innerSearchBar) {
					// On demand creation of the filter panel
					if (!this.filterPanel) {

						// Must instantiate the right filterPanel
						// this.filterPanel = new
						// EasySDI_Map.RwgFilterPanel({mapPanel: this.mapPanel,
						// innerSearchBar: innerSearchBar});
						// this.filterPanel = new
						// EasySDI_Map.FilterPanel({mapPanel: this.mapPanel,
						// innerSearchBar: innerSearchBar});
						this.filterPanel = new EasySDI_Map[extensionClasses.FilterPanelClassName]( {
							mapPanel : this.mapPanel,
							innerSearchBar : innerSearchBar
						});

						this.legendOrFilterPanel.add(this.filterPanel);
						this.searchPanel.registerTrigger('filterPanel.setMode', this.filterPanel.SetMode.createDelegate(this.filterPanel));
						this.searchPanel.registerTrigger('filterPanel.updateFilter', this.filterPanel.updateFilter
								.createDelegate(this.filterPanel));
						this.filterPanel.registerTrigger('doSearch', this.searchManager.doSearch.createDelegate(this.searchManager));
						this.filterPanel.registerTrigger('hideFilterPanel', this.hideFilterPanel.createDelegate(this));
					} else {
						// Ensure the old filter panel links to the current
						// search bar.
						this.filterPanel.innerSearchBar = innerSearchBar;
						// Drawing and Selection layers visible if the user can
						// access this tab
						if (this.filterPanel.placeTab) {
							this.filterPanel.placeTab.doShow();
						}
					}

					this.legendOrFilterPanel.getLayout().setActiveItem(1);
					this.legendOrFilterPanel.setWidth(490);
					this.legendOrFilterPanel.setTitle(EasySDI_Map.lang.getLocal("02000005"));

					this.doLayout();
				},

				/**
				 * Hide the filter builder panel.
				 */
				hideFilterPanel : function() {
					this.legendOrFilterPanel.getLayout().setActiveItem(0);
					this.legendOrFilterPanel.setWidth(300);
					this.legendOrFilterPanel.setTitle(EasySDI_Map.lang.getLocal("02000003"));
					this.doLayout();
				}

				});