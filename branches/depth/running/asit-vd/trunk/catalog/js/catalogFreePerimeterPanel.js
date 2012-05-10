/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

catalogFreePerimeterPanel = Ext.extend(Ext.form.Field,  {
	
    hideNavIcons:false,
    imagePath:"",
    iconUp:"up2.gif",
    iconDown:"down2.gif",
    iconLeft:"left2.gif",
    iconRight:"right2.gif",
    iconTop:"top2.gif",
    iconBottom:"bottom2.gif",
    drawUpIcon:true,
    drawDownIcon:true,
    drawLeftIcon:true,
    drawRightIcon:true,
    drawTopIcon:true,
    drawBotIcon:true,
    delimiter:',',
    bodyStyle:null,
    border:false,
    defaultAutoCreate:{tag: "div"},
    dynamic : false,
    clone : false,
    comboboxname : null,
    maxcardbound : 1,
    mincardbound : 1,
    
	initComponent: function(){
		catalogFreePerimeterPanel.superclass.initComponent.call(this);
        this.addEvents({
            'change' : true,
            'addItemTo' : true,
            'removeItemTo' : true
        });
    },
    
    onRender: function(ct, position){
    	catalogFreePerimeterPanel.superclass.onRender.call(this, ct, position);

        // Internal default configuration for multiselect
        var msConfig = [{
            legend: 'Selected',
            droppable: true,
            draggable: true,
            width: 100,
            height: 100
        }];

        this.fromMultiselect = 
    		new Ext.Panel({
    			layout:"table",
    			border:this.border,
    			width: 250,
                height: 200,
    			layoutConfig:{columns:1},
    			items:[{
		    		  xtype: 'spacer',
		    		  height: 10
		    		},{
    		        xtype: 'label',
    		        text: this.northLabel,
    		        margins: '0 0 0 10'
    		    	},{
    	            id:this.id+'_north',
    	            xtype: 'textfield',
    				cls: 'easysdi_shop_backend_textfield', 
    	            name: this.id+'_north',
    	            allowBlank: true,
    	            blankText: '',
    	            value: '',
    	            defaultVal:'',
    	            width: 250
    		    	},{
		    		  xtype: 'spacer',
		    		  height: 10
		    		},{
    		        xtype: 'label',
    		        text: this.southLabel,
    		        margins: '0 0 0 10'
    		    	},{
    	            id:this.id+'_south',
    	            xtype: 'textfield',
    				cls: 'easysdi_shop_backend_textfield', 
    	            name: this.id+'_south',
    	            allowBlank: true,
    	            blankText: '',
    	            value: '',
    	            defaultVal:'',
    	            width: 250
    		    	},{
		    		  xtype: 'spacer',
		    		  height: 10
		    		},{
        		        xtype: 'label',
        		        text: this.eastLabel,
        		        margins: '0 0 0 10'
        		    	},{
        	            id:this.id+'_east',
        	            
        	            xtype: 'textfield',
        				cls: 'easysdi_shop_backend_textfield', 
        	            name: this.id+'_east',
        	            allowBlank: true,
        	            blankText: '',
        	            value: '',
        	            defaultVal:'',
        	            width: 250
        		    	},{
      		    		  xtype: 'spacer',
    		    		  height: 10
    		    		},{
            		        xtype: 'label',
            		        text: this.westLabel,
            		        margins: '0 0 0 10'
            		    	},{
            	            id:this.id+'_west',
            	            xtype: 'textfield',
            				cls: 'easysdi_shop_backend_textfield', 
            	            name: this.id+'_west',
            	            allowBlank: true,
            	            blankText: '',
            	            value: '',
            	            defaultVal:'',
            	            width: 250
            		    	}]
            });
        this.toMultiselect = new Ext.ux.form.MultiSelect(this.multiselects[0]);
        
        var p = new Ext.Panel({
            bodyStyle:this.bodyStyle,
            border:this.border,
            layout:"table",
            layoutConfig:{columns:4}
        });

        p.add(this.fromMultiselect);
        var icons = new Ext.Panel({header:false});
        p.add(icons);
        p.add(this.toMultiselect);
        p.render(this.el);
        icons.el.down('.'+icons.bwrapCls).remove();

        // ICON HELL!!!
        if (this.imagePath!="" && this.imagePath.charAt(this.imagePath.length-1)!="/")
            this.imagePath+="/";
        this.iconLeft = this.imagePath + (this.iconLeft || 'left2.gif');
        this.iconRight = this.imagePath + (this.iconRight || 'right2.gif');
        var el=icons.getEl();
        this.addIcon = el.createChild({tag:'img', src:this.iconRight, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.removeIcon = el.createChild({tag:'img', src:this.iconLeft, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.addIcon.on('click', this.fromTo, this);
        this.removeIcon.on('click', this.toFrom, this);
        if (!this.drawLeftIcon || this.hideNavIcons) { this.addIcon.dom.style.display='none'; }
        if (!this.drawRightIcon || this.hideNavIcons) { this.removeIcon.dom.style.display='none'; }
       
        var tb = p.body.first();
        this.el.setWidth(p.body.first().getWidth());
        p.body.removeClass();

        this.hiddenName = this.name;
        var hiddenTag = {tag: "input", type: "hidden", value: "", name: this.name};
        this.hiddenField = this.el.createChild(hiddenTag);
    },
    
    fromTo : function() {
    	
        
        var TopicRecord = Ext.data.Record.create(
        	    {name: 'value', mapping: 'value'},
        	    {name: 'text', mapping: 'text'},
        	    {name: 'northbound', mapping: 'northbound'},
        	    {name: 'southbound', mapping: 'southbound'},
        	    {name: 'eastbound', mapping: 'eastbound'},
        	    {name: 'westbound', mapping: 'westbound'}
        	);

        	var myNewRecord = new TopicRecord({
        		value: 'value test',
        	    text: 'text test',
        	    northbound: 'test 1',
        	    southbound: 'test 2',
        	    eastbound: 'test 3',
        	    westbound: 'test 4'
        	});
        	
        	
        
        this.toMultiselect.view.store.add(myNewRecord);
            
        this.toMultiselect.view.refresh();
        var si = this.toMultiselect.store.sortInfo;
        if(si){
            this.toMultiselect.store.sort(si.field, si.direction);
        }
        
    },

    toFrom : function() {
        
    }
    
 

    
});

Ext.reg('catalogFreePerimeterPanel', catalogFreePerimeterPanel);