
Ext.namespace("EasySDI_Map");Ext.namespace("SData");EasySDI_Map.PrecisionTree=Ext.extend(Ext.data.Tree,{constructor:function(config)
{EasySDI_Map.PrecisionTree.superclass.constructor.apply(this,arguments);this._rootNode=new Ext.tree.TreeNode({id:'precisionRoot'});this._loadLayers();this.setRootNode(this._rootNode);},_loadLayers:function()
{var loadedState=Ext.state.Manager.get('precisionLayerState',false);var tree=this;var first=true;for(var geom in SData.searchPrecisions){var p=SData.searchPrecisions[geom];p.active=(loadedState&&loadedState[geom]);if(p.minScale&&p.lowScaleSwitchTo){SData.searchPrecisions[p.lowScaleSwitchTo].required=true;}
p.required=p.required||p.active;this._rootNode.appendChild(new Ext.tree.TreeNode({text:p.title,id:'p_'+geom,checked:p.active,iconCls:'grid',listeners:{'checkchange':function(node,checked){var geom=node.id.substring(2);SData.searchPrecisions[geom].active=checked;SData.searchPrecisions[geom].required=SData.searchPrecisions[geom].required||checked;}},allowDrag:false}));if(typeof p.style!=="undefined"){Ext.applyIf(p.style,OpenLayers.Feature.Vector.style['default']);}
first=false;}},_saveState:function()
{}});