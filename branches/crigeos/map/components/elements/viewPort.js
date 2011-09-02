
Ext.namespace("EasySDI_Map");EasySDI_Map.Viewport=Ext.extend(Ext.Panel,{initComponent:function()
{EasySDI_Map.Viewport.superclass.initComponent.call(this);this.el=Ext.get('map');this.el.dom.scroll='no';this.allowDomMove=false;this.autoWidth=true;Ext.EventManager.onWindowResize(this.fireResize,this);var vpSize=Ext.getBody().getViewSize();this.fireResize(vpSize.width,vpSize.height);this.renderTo=this.el;},fireResize:function(w,h)
{var l=this.el.getLeft();var r=this.el.getRight()-this.el.getWidth();var t=this.el.getTop();var b=this.el.getBottom()-this.el.getHeight()
this.setSize(w-l-r,h-t-b);}});