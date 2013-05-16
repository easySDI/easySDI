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

Ext.namespace('EasySDI_Map.Dlg');

/**
 * Dialog for styling a map layer.
 */
EasySDI_Map.Dlg.Styler = Ext.extend(Ext.Window, {

	constructor : function(config, layerNode) {
		var defaults = {
			title : EasySDI_Map.lang.getLocal('DLG_SYMBOL_APPEARANCE'),
			closable : true,
			width : 400,
			autoHeight : true,
			plain : true,
			layout : 'form',
			labelWidth : 125,
			bodyStyle : "padding: 5px",
			cls : 'left-right-buttons'

		};
		// Store reference
	var dlg = this;
	var layer = layerNode.layer;
	var node = layerNode;

	// Disable the "back to default settings" button if no
	// user defined style exists for this node
	var defaultStyle = true;
	var styles = Ext.state.Manager.get('overlayLayerStyle', null);
	if (styles != null) {
		if (styles[node.id]) {
			defaultStyle = false;
		}
	}
	// Items
	this.fillPallete = new Ext.ColorPalette( {
		handler : this._updatePreview,
		scope : this
	});
	this.strokePallete = new Ext.ColorPalette( {
		handler : this._updatePreview,
		scope : this
	});
	this.opacity = new Ext.Slider( {
		fieldLabel : EasySDI_Map.lang.getLocal('DLG_OPACITY'),
		isFormField : true,
		width : 200,
		listeners : {
			'change' : {
				fn : this._updatePreview,
				scope : this
			}
		},
		scope : this
	});
	defaults.items = [
			{
				xtype : 'panel',
				fieldLabel : EasySDI_Map.lang.getLocal('DLG_FILL_COLOUR'),
				name : 'color',
				isFormField : true,
				width : 145,
				height : 95,
				items : this.fillPallete
			},
			{
				xtype : 'panel',
				fieldLabel : EasySDI_Map.lang.getLocal('DLG_LINE_COLOUR'),
				name : 'strokecolor',
				isFormField : true,
				width : 145,
				height : 95,
				items : this.strokePallete
			},
			this.opacity,
			{
				xtype : 'checkbox',
				fieldLabel : EasySDI_Map.lang.getLocal('DLG_SHOW_LABELS')
			},
			{
				html : '<div style="width: 50px; height: 50px; padding: 25px; background: url(' + componentParams.componentPath
						+ 'assets/images/map-preview.jpg) top left no-repeat">'
						+ '<div id="preview-stroke" style="height: 50px; width: 50px; border: solid black 1px;">'
						+ '<div id="preview" style="height: 50px; width: 50px;"></div>' + '</div></div>',
				fieldLabel : EasySDI_Map.lang.getLocal('DLG_PREVIEW'),
				isFormField : true,
				border : false,
				height : 100,
				width : 100
			} ];
	defaults.buttons = [ {
		text : EasySDI_Map.lang.getLocal('DLG_DEFAULT_SETTINGS'),
		scope : this,
		disabled : defaultStyle,
		cls : 'x-btn-left',
		handler : function() {
			if (layer !== null) {
				// Update the layer node (which will
		// reconfigure it's layer)
		this._defaultSettings(node);
		dlg.destroy();
	}
}
	}, {
		text : EasySDI_Map.lang.getLocal('Submit'),
		scope : this,
		// cls: 'x-btn-left',
		ctCls : 'width_100',
		handler : function() {
			if (layer !== null) {
				// Update the layer node (which will
		// reconfigure it's layer)
		this._saveSettings(node);

		dlg.destroy();
	}
}
	}, {
		text : EasySDI_Map.lang.getLocal('Close'),
		handler : function() {
			dlg.destroy();
		}
	} ];
	var settings = Ext.merge( {}, defaults, config);
	EasySDI_Map.Dlg.Styler.superclass.constructor.apply(this, [ settings ]);
	this._loadSettings(node);
},

/**
 * Load the settings for the controls and the preview from the node.
 */
_loadSettings : function(node) {
	this.fillPallete.value = node.fillColor;
	this.strokePallete.value = node.strokeColor;
	this.opacity.value = node.opacity * 100;
	this._updatePreview();
},

/**
 * Update the node's settings, which in turn updates the layer style and saves
 * the settings.
 */
_saveSettings : function(node) {
	node.fillColor = this.fillPallete.value;
	node.strokeColor = this.strokePallete.value;
	node.opacity = this.opacity.value / 100;
	node.updateStyle(true);
},

/**
 * Update the node's settings, which in turn updates the layer style and saves
 * the settings.
 */
_defaultSettings : function(node) {
	node.defautStyle();
},

/**
 * Update the fill colour preview
 */
_updatePreview : function() {
	var preview = Ext.get('preview');
	if (preview !== null) {
		preview.setStyle('background-color', '#' + this.fillPallete.value);
		preview.setStyle('opacity', this.opacity.value / 100);
		Ext.get('preview-stroke').setStyle('border-color', '#' + this.strokePallete.value);
	}
}

});

EasySDI_Map.Dlg.LoadFilter = Ext.extend(Ext.Window, {

	constructor : function(config) {
		this.ds = new Ext.data.Store( {
			url : componentParams.componentUrl + '&format=raw&controller=map_filter&task=getlist',
			reader : new Ext.data.JsonReader( {
				root : 'filters',
				totalProperty : 'totalCount',
				id : 'id'
			}, [ {
				name : 'id'
			}, {
				name : 'user_id'
			}, {
				name : 'name'
			}, {
				name : 'title'
			}, {
				name : 'description'
			}, {
				name : 'filter_mode'
			}, {
				name : 'filter_data'
			} ]),
			baseParams : {
				limit : 20
			}
		});

		// Custom rendering Template for the View
		var resultTpl = new Ext.XTemplate('<tpl for=".">', '<div class="search-item" id={id}>',
				'<h3>{title}</h3><p>{description}<br/>By {name}</p>', '</div></tpl>');

		this.dataView = new Ext.DataView( {
			tpl : resultTpl,
			store : this.ds,
			overClass : 'x-view-over',
			itemSelector : 'div.search-item',
			singleSelect : true,
			height : 214,
			style : "overflow: auto"
		});

		var defaults = {
			title : EasySDI_Map.lang.getLocal('DLG_LOAD_SEARCH_TITLE'),
			closable : true,
			width : 500,
			autoHeight : true,
			plain : true,
			layout : 'form',
			modal : true,
			labelWidth : 145,
			bodyStyle : "padding: 5px",
			items : [ {
				xtype : 'checkbox',
				fieldLabel : EasySDI_Map.lang.getLocal('DLG_LOAD_OWN_SEARCH'),
				checked : true,
				hidden : user.role < 4,
				listeners : {
					'check' : {
						fn : function(ctrl, checked) {
							var params = {
								start : 0,
								limit : 20
							};
							if (checked) {
								params.user_id = user.id;
							}
							this.ds.reload( {
								params : params
							});
						},
						scope : this
					}
				}
			}, new Ext.ux.form.SearchField( {
				store : this.ds,
				width : 324,
				fieldLabel : EasySDI_Map.lang.getLocal('DLG_LOAD_SEARCH'),
				isFormField : true
			}), {
				xtype : 'panel',
				height : 214,
				items : [ this.dataView ]
			} ],
			buttons : [ {
				text : EasySDI_Map.lang.getLocal('Open'),
				handler : this._openSelected,
				scope : this
			}, {
				text : EasySDI_Map.lang.getLocal('Delete'),
				handler : this._deleteSelected,
				scope : this
			}, {
				text : EasySDI_Map.lang.getLocal('Close'),
				handler : function() {
					this.destroy();
				},
				scope : this
			} ]
		};
		this.ds.load( {
			params : {
				start : 0,
				limit : 20,
				user_id : user.id
			}
		});
		var settings = Ext.merge( {}, defaults, config);
		EasySDI_Map.Dlg.LoadFilter.superclass.constructor.apply(this, [ settings ]);
	},

	/**
	 * Opens the selected filter, and closes the dialog. Does not close if there
	 * is no selection.
	 */
	_openSelected : function() {
		var selIndexes = this.dataView.getSelectedIndexes();
		if (selIndexes.length > 0) {
			Ext.Ajax.request( {
				url : componentParams.componentUrl + '&format=raw&controller=map_filter&task=getitem',
				params : {
					'id' : this.ds.data.items[selIndexes[0]].id
				},
				success : function(response, opts) {
					var object = Ext.util.JSON.decode(response.responseText);
					if (object.filters.length == 1) {
						this.trigger("loadFilter", object.filters[0]);
					} else {
						alert(EasySDI_Map.lang.getLocal('DLG_ERROR_LOADING'));
					}
					this.destroy();

				},
				scope : this
			});
		}
	},

	/**
	 * Delete the selected filter
	 */
	_deleteSelected : function() {
		var selIndexes = this.dataView.getSelectedIndexes();
		if (selIndexes.length > 0) {
			Ext.Msg.confirm(EasySDI_Map.lang.getLocal('DLG_CONFIRM_DELETION'), EasySDI_Map.lang.getLocal('DLG_ARE_YOU_SURE_DELETE_')
					.replace('*', this.ds.data.items[selIndexes[0]].data.title), function(btn) {
				if (btn == "yes") {
					Ext.Ajax.request( {
						url : componentParams.componentUrl + '&format=raw&controller=map_filter&task=delete',
						params : {
							'id' : this.ds.data.items[selIndexes[0]].data.id
						},
						success : function(response, opts) {
							if (response.responseText == "OK") {
								this.ds.reload();
							} else {
								alert(EasySDI_Map.lang.getLocal('DLG_ERROR_DELETING'));
							}
						},
						scope : this
					});
				}
			}, this);
		}
	}

});

Ext.mixin(EasySDI_Map.Dlg.LoadFilter, EasySDI_Map.TriggerManager);

EasySDI_Map.Dlg.SaveFilter = Ext.extend(Ext.Window, {
	/**
	 * Object defining the description and filter data to be saved.
	 */
	data : null,

	constructor : function(config) {
		config = config || {
			data : {
				description : "unknown",
				filter_mode : -1,
				filter_data : "none"
			}
		};
		var defaults = {
			title : EasySDI_Map.lang.getLocal('DLG_SAVE_SEARCH_TITLE'),
			closable : true,
			width : 400,
			authoHeight : true,
			modal : true,
			labelWidth : 125,
			items : [ new Ext.form.FormPanel( {
				bodyStyle : "padding: 10px;",
				border : false,
				id : 'save-form',
				items : [ {
					xtype : 'textfield',
					name : 'title',
					width : 250,
					fieldLabel : EasySDI_Map.lang.getLocal('DLG_SAVE_SEARCH')
				}, {
					xtype : 'hidden',
					name : 'description',
					value : config.data.description
				}, {
					xtype : 'hidden',
					name : 'filter_mode',
					value : config.data.filter_mode
				}, {
					xtype : 'hidden',
					name : 'filter_data',
					value : config.data.filter_data
				}, {
					xtype : 'hidden',
					name : 'user_id',
					value : user.id
				} ]
			}) ],
			buttons : [ {
				text : EasySDI_Map.lang.getLocal('Save'),
				handler : function() {
					Ext.Ajax.request( {
						url : componentParams.componentUrl + '&format=raw&controller=map_filter&task=save',
						form : Ext.getCmp('save-form').getForm().id,
						success : function(response, opts) {
							this.destroy();
						},
						scope : this
					});
				},
				scope : this
			}, {
				text : EasySDI_Map.lang.getLocal('Cancel'),
				handler : function() {
					this.destroy();
				},
				scope : this
			} ]
		};
		var settings = Ext.merge( {}, defaults, config);
		EasySDI_Map.Dlg.SaveFilter.superclass.constructor.apply(this, [ settings ]);
	}

});
EasySDI_Map.Dlg.InputPDFTitle = Ext
		.extend(
				Ext.Window,
				{
					/**
					 * Object defining the description and filter data to be
					 * saved.
					 */
					data : null,

					constructor : function(config) {
						var defaults = {
							title : EasySDI_Map.lang.getLocal('DLG_PDF_TITLE'),
							closable : true,
							width : 350,
							authoHeight : true,
							modal : true,
							labelWidth : 250,
							defaultType : 'checkbox',
							items : [ {
								id : 'txtPDFTitle',
								xtype : 'textfield',
								fieldLabel : 'pdfTitle',
								width : 350
							}, {
								id : 'txtPDFshowMap',
								boxLabel : EasySDI_Map.lang.getLocal('DLG_PDF_Map'),
								checked : true,
								name : 'p-showMap',
								width : 350
							}, {
								id : 'txtPDFshowLegend',
								boxLabel : EasySDI_Map.lang.getLocal('DLG_PDF_Legends'),
								name : 'p-showLegend',
								checked : true,
								width : 350
							}, {
								id : 'txtPDFshowList',
								boxLabel : EasySDI_Map.lang.getLocal('DLG_PDF_Details'),
								name : 'p-showList',
								checked : true,
								width : 350
							} ],
							buttons : [
									{
										text : EasySDI_Map.lang.getLocal('Export'),
										handler : function() {
											var pdfParams = '&p-showMap=' + (this.getComponent('txtPDFshowMap').getValue() ? '1' : '0');
											pdfParams += '&p-showLegend=' + (this.getComponent('txtPDFshowLegend').getValue() ? '1' : '0');
											pdfParams += '&p-showList=' + (this.getComponent('txtPDFshowList').getValue() ? '1' : '0');
											var overlays = '&overlays=';
											var anyOverlay = false;

											Ext.each(this.mapPanel.map.layers, function(layer) {
												if (layer.inRange && layer.getVisibility() && !layer.isBaseLayer
														&& layer.CLASS_NAME == "OpenLayers.Layer.WMS") {
													anyOverlay = true;
													overlays += layer.params.LAYERS + ",";
												}
											}, this);

											if (anyOverlay)
												overlays = overlays.substring(0, overlays.length - 1);
											var curentExtent = this.mapPanel.map.getExtent();
											var baseLayerString = (this.mapPanel.map.baseLayer.visibility) ? this.mapPanel.map.baseLayer.params.LAYERS
													: '';
											$.download(componentParams.maptofopURL + '?title='
													+ this.getComponent('txtPDFTitle').getValue() + '&baseLayer=' + baseLayerString
													+ '&epsg=' + this.mapPanel.map.getProjection() + overlays + pdfParams + '&minX='
													+ curentExtent.left + '&minY=' + curentExtent.bottom + '&maxX=' + curentExtent.right
													+ '&maxY=' + curentExtent.top + '&w=' + this.mapPanel.map.getSize().w + '&h='
													+ this.mapPanel.map.getSize().h, {
												download : 1
											});
											this.destroy();
										},
										scope : this
									}, {
										text : EasySDI_Map.lang.getLocal('Cancel'),
										handler : function() {
											this.destroy();
										},
										scope : this
									} ]
						};
						var settings = Ext.merge( {}, defaults, config);
						EasySDI_Map.Dlg.InputPDFTitle.superclass.constructor.apply(this, [ settings ]);
					}

				});

EasySDI_Map.Dlg.SaveAsPopup = Ext.extend(Ext.Window, {

	constructor : function(config) {
		config.width = 305;

		// Get the available image formats defined in the params.php file
		this.storeFormat = [];
		Ext.each(componentParams.getMapImageFormat, function(format) {
			this.storeFormat.push( [ format.value, format.text ])
		}, this);
		this.dataStoreFormat = new Ext.data.SimpleStore( {
			fields : [ 'value', 'text' ],
			data : this.storeFormat
		})

		// Init the combobox
		this.combo = new Ext.form.ComboBox( {
			forceSelection : true,
			store : this.dataStoreFormat,
			valueField : 'value',
			displayField : 'text',
			value : this.dataStoreFormat.getAt(0).get('value'),
			typeAhead : true,
			mode : 'local',
			minChars : 1,
			// emptyText:'Format...',
			// selectOnFocus:true,
			triggerAction : 'all',
			fieldLabel : EasySDI_Map.lang.getLocal('Format '),
			name : 'Format',
			allowBlank : false,
			width : 210
		});

		// Init the form contained by the window
		this.form = new Ext.FormPanel( {
			labelWidth : 50, // label settings here cascade unless overridden
			frame : true,
			bodyStyle : 'padding:0 0 0 0',
			/*
			 * width: 250, height:80,
			 */
			// defaults: {width: 250},
			defaultType : 'textfield',
			items : this.combo
		});

		// Init the window
		this.mapPanel = config.mapPanel;
		config.title = EasySDI_Map.lang.getLocal('Save as');
		// config.width=300;
		config.height = 100;
		config.autoScroll = false;
		config.modal = true;
		// config.layout="form";
		config.bodyStyle = "padding: 0 0 0 0";
		config.labelWidth = 150;

		config.items = this.form;

		config.buttons = [ {
			text : EasySDI_Map.lang.getLocal('Save'),
			handler : function() {
				$.download(this.mapPanel._getOneImageMapURL(this.combo.getValue()), {
					download : 1
				});
				this.destroy();
			},
			scope : this
		}, {
			text : EasySDI_Map.lang.getLocal('Cancel'),
			handler : function() {
				this.destroy();
			},
			scope : this
		} ];

		EasySDI_Map.Dlg.SaveAsPopup.superclass.constructor.apply(this, arguments);
	}

});
