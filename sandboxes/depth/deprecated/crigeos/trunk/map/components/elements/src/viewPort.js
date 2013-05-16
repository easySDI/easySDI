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
 
 Ext.namespace("EasySDI_Map");

EasySDI_Map.Viewport = Ext.extend(Ext.Panel, {
    initComponent : function() 
    {
        EasySDI_Map.Viewport.superclass.initComponent.call(this);
        this.el = Ext.get('map');
        this.el.dom.scroll = 'no';
        this.allowDomMove = false;
        this.autoWidth = true;
        Ext.EventManager.onWindowResize(this.fireResize, this);

        var vpSize = Ext.getBody().getViewSize();
        this.fireResize(vpSize.width, vpSize.height);
        this.renderTo = this.el;
    },

    fireResize : function(w, h)
    {
       var l = this.el.getLeft();
        var r = this.el.getRight()-this.el.getWidth();
        var t = this.el.getTop();
        var b = this.el.getBottom()-this.el.getHeight()

        this.setSize(w-l-r,h-t-b);
    }
});
