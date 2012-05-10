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
	
    imagePath:"",
    iconLeft:"left2.gif",
    iconRight:"right2.gif",
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

        this.fieldnorth = {
	            id:this.id+'_north',
	            xtype: 'textfield',
				cls: 'easysdi_shop_backend_textfield', 
	            name: this.id+'_north',
	            allowBlank: true,
	            width: 250
		    	};
		this.fieldsouth = {
	            id:this.id+'_south',
	            xtype: 'textfield',
				cls: 'easysdi_shop_backend_textfield', 
	            name: this.id+'_south',
	            allowBlank: true,
	            width: 250
		    	};
		this.fieldeast = {
	            id:this.id+'_east',
	            xtype: 'textfield',
				cls: 'easysdi_shop_backend_textfield', 
	            name: this.id+'_east',
	            allowBlank: true,
	           width: 250
		    	};
		this.fieldwest = {
	            id:this.id+'_west',
	            xtype: 'textfield',
				cls: 'easysdi_shop_backend_textfield', 
	            name: this.id+'_west',
	            allowBlank: true,
	             width: 250
		    	};
		
        this.fromMultiselect = new Ext.Panel({
    			layout:"table",
    			border:this.border,
    			width: 250,
                height: 200,
    			layoutConfig:{columns:1},
    			items:[
    			    {
    			    	xtype: 'spacer',
    			    	height: 10
		    		},
		    		{
		    			xtype: 'label',
		    			text: this.northLabel,
		    			margins: '0 0 0 10'
				    },
				    this.fieldnorth,
				    {
				    	xtype: 'spacer',
				    	height: 10
		    		},
		    		{
		    			xtype: 'label',
	    		        text: this.southLabel,
	    		        margins: '0 0 0 10'
    		    	},
    		    	this.fieldsouth,
    		    	{
    		    		xtype: 'spacer',
    		    		height: 10
		    		},
		    		{
	    		        xtype: 'label',
	    		        text: this.eastLabel,
	    		        margins: '0 0 0 10'
    		    	},
    		    	this.fieldeast,
    		    	{
    		    		xtype: 'spacer',
    		    		height: 10
		    		},
		    		{
		    			xtype: 'label',
		    			text: this.westLabel,
		    			margins: '0 0 0 10'
    		    	},
    		    	this.fieldwest
    		    	]
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
        
        var tb = p.body.first();
        this.el.setWidth(p.body.first().getWidth());
        p.body.removeClass();

        this.hiddenName = this.name;
        var hiddenTag = {tag: "input", type: "hidden", value: "", name: this.name};
        this.hiddenField = this.el.createChild(hiddenTag);
    },
    
    doLayout: function(){
        if(this.rendered){
            this.toMultiselect.fs.doLayout();
        }
    },
    
    afterRender: function(){
        Ext.ux.form.ItemSelector.superclass.afterRender.call(this);

        this.toStore = this.toMultiselect.store;
        this.toStore.on('add', this.valueChanged, this);
        this.toStore.on('remove', this.valueChanged, this);
        this.toStore.on('load', this.valueChanged, this);
        this.valueChanged(this.toStore);

    },
    
    buildRecord : function (northvalue, southvalue, eastvalue, westvalue){
    	 var TopicRecord = Ext.data.Record.create(
          	    {name: 'value', mapping: 'value'},
          	    {name: 'text', mapping: 'text'},
          	    {name: 'northbound', mapping: 'northbound'},
          	    {name: 'southbound', mapping: 'southbound'},
          	    {name: 'eastbound', mapping: 'eastbound'},
          	    {name: 'westbound', mapping: 'westbound'}
          	);
    	 
    	 var record = new TopicRecord({
      		value: '['+northvalue+','+southvalue+','+eastvalue+','+westvalue+']',
      	    text: '['+northvalue+','+southvalue+','+eastvalue+','+westvalue+']',
      	    northbound: northvalue,
      	    southbound: southvalue,
      	    eastbound: eastvalue,
      	    westbound: westvalue
      	});
    	 
    	 return record;
    },
    
    addRecord : function(record){
    	var northvalue =  record.feature.geometry.bounds.top;
        var eastvalue =  record.feature.geometry.bounds.right;
        var southvalue =  record.feature.geometry.bounds.bottom;
        var westvalue =  record.feature.geometry.bounds.left;
         	
        this.toMultiselect.view.store.add(this.buildRecord(northvalue, southvalue, eastvalue, westvalue));
             
        this.toMultiselect.view.refresh();
        var si = this.toMultiselect.store.sortInfo;
         if(si){
             this.toMultiselect.store.sort(si.field, si.direction);
         }
         
    },
    
    fromTo : function() {
         var northvalue =  Ext.getCmp(this.id+'_north').getValue();
         var eastvalue =  Ext.getCmp(this.id+'_east').getValue();
         var southvalue =  Ext.getCmp(this.id+'_south').getValue();
         var westvalue =  Ext.getCmp(this.id+'_west').getValue();

        this.toMultiselect.view.store.add(this.buildRecord(northvalue, southvalue, eastvalue, westvalue));
            
        this.toMultiselect.view.refresh();
        var si = this.toMultiselect.store.sortInfo;
        if(si){
            this.toMultiselect.store.sort(si.field, si.direction);
        }
        
        Ext.getCmp(this.id+'_north').setValue('');
        Ext.getCmp(this.id+'_east').setValue('');
        Ext.getCmp(this.id+'_south').setValue('');
        Ext.getCmp(this.id+'_west').setValue('');
        
        this.fireEvent ('addItemTo', record);
    },

    toFrom : function() {
    	var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
        var records = [];
        if (selectionsArray.length > 0) {
            for (var i=0; i<selectionsArray.length; i++) {
                record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
                records.push(record);
            }
            selectionsArray = [];
            for (var i=0; i<records.length; i++) {
                record = records[i];
                this.toMultiselect.view.store.remove(record);
                this.fireEvent ('removeItemTo', record);
            }
        }
        
        this.toMultiselect.view.refresh();
        
    } ,
    
    valueChanged: function(store) {
        var record = null;
        var values = [];
        for (var i=0; i<store.getCount(); i++) {
            record = store.getAt(i);
            values.push(record.get(this.toMultiselect.valueField));
        }
        this.hiddenField.dom.value = values.join(this.delimiter);
        this.fireEvent('change', this, this.getValue(), this.hiddenField.dom.value);
    },
    
    getValue : function() {
        return this.hiddenField.dom.value;
    },
});

Ext.reg('catalogFreePerimeterPanel', catalogFreePerimeterPanel);