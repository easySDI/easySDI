
Ext.namespace("EasySDI_Map");EasySDI_Map.LayerPanel=Ext.extend(Ext.tree.TreePanel,{_defaults:{border:false,rootVisible:false,lines:false,autoScroll:true,autoHeight:true},constructor:function(config){if(!componentParams.authorisedTo.DATA_PRECISION||!componentDisplayOption.DataPrecisionEnable){var treePanelWidth=(componentParams.treePanelWidth!=undefined)?parseInt(componentParams.treePanelWidth):200;this._defaults.width=treePanelWidth;this._defaults.border=true;this._defaults.collapsible=true;this._defaults.split=true;this._defaults.autoHeight=false;}
this._tree=config.tree;this._defaults.root=this._tree.getRootNode();var settings=Ext.merge({},this._defaults,config);EasySDI_Map.LayerPanel.superclass.constructor.apply(this,[settings]);this.on('contextmenu',function(node,e){if(node.isLeaf()&&!node.layer.isBaseLayer){if(node.layer.customStyle){var menu=new Ext.menu.Menu([{id:"style",text:EasySDI_Map.lang.getLocal("LP_DEFINE_LAYER_SYMBOL"),iconCls:'color_swatch',handler:function(evt){var styler=new EasySDI_Map.Dlg.Styler({},node);styler.show();},scope:this}]);menu.showAt(e.getPoint());}}});}});EasySDI_Map.DataPrecisionPanel=Ext.extend(Ext.tree.TreePanel,{_defaults:{border:false,rootVisible:false,lines:false,autoScroll:true,autoHeight:true},constructor:function(config){this._tree=config.tree;this._defaults.root=this._tree.getRootNode();var settings=Ext.merge({},this._defaults,config);EasySDI_Map.DataPrecisionPanel.superclass.constructor.apply(this,[settings]);}});