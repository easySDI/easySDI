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

Ext.namespace("EasySDI_Map");

EasySDI_Map.LayerPanel = Ext.extend(Ext.tree.TreePanel, {
	_defaults : {
		border : false,
		rootVisible : false,
		lines : false,
		autoScroll : true,
		autoHeight : true
	},
	constructor : function(config) {
		if (!componentParams.authorisedTo.DATA_PRECISION || !componentDisplayOption.DataPrecisionEnable) {
			var treePanelWidth = (componentParams.treePanelWidth != undefined) ? parseInt(componentParams.treePanelWidth) : 200;
			this._defaults.width = treePanelWidth;
			this._defaults.border = true;
			this._defaults.collapsible = true;
			this._defaults.split = true;
			this._defaults.autoHeight = false;
		}
		this._tree = config.tree;
		this._defaults.root = this._tree.getRootNode();
		var settings = Ext.merge( {}, this._defaults, config);
		EasySDI_Map.LayerPanel.superclass.constructor.apply(this, [ settings ]);

		// Add menu
	this.on('contextmenu', function(node, e) {
		// Only show if we're at a second level node that is not a base
			// layer
			if (node.isLeaf() && !node.layer.isBaseLayer) {
				if (node.layer.customStyle) {
					var menu = new Ext.menu.Menu( [ {
						id : "style",
						text : EasySDI_Map.lang.getLocal("LP_DEFINE_LAYER_SYMBOL"),
						iconCls : 'color_swatch',
						handler : function(evt) {
							var styler = new EasySDI_Map.Dlg.Styler( {}, node);
							styler.show();
						},
						scope : this
					} ]);
					menu.showAt(e.getPoint());
				}
			}
		});
}
});

EasySDI_Map.DataPrecisionPanel = Ext.extend(Ext.tree.TreePanel, {
	_defaults : {
		border : false,
		rootVisible : false,
		lines : false,
		autoScroll : true,
		autoHeight : true
	},
	constructor : function(config) {
		this._tree = config.tree;
		this._defaults.root = this._tree.getRootNode();
		var settings = Ext.merge( {}, this._defaults, config);
		EasySDI_Map.DataPrecisionPanel.superclass.constructor.apply(this, [ settings ]);
	}
});