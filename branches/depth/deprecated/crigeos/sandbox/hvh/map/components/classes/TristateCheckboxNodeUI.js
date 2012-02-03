
Ext.namespace("GeoExt.tree");GeoExt.tree.TristateCheckboxNodeUI=Ext.extend(Ext.tree.TreeNodeUI,{constructor:function(config){GeoExt.tree.TristateCheckboxNodeUI.superclass.constructor.apply(this,arguments);},toggleCheck:function(value,thirdState,options){var cb=this.checkbox;if(thirdState==true){if(cb){Ext.get(cb).setOpacity(0.5);}
this.node.attributes.thirdState=true;}else{if(cb){Ext.get(cb).clearOpacity();}
delete this.node.attributes.thirdState;}
if(options&&options.silent==true){this.node.suspendEvents();}
GeoExt.tree.TristateCheckboxNodeUI.superclass.toggleCheck.call(this,value);this.node.resumeEvents();}});