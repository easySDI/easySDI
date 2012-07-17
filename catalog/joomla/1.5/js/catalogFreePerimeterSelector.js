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

catalogFreePerimeterSelector = Ext.extend(Ext.form.Field,  {
	
    imagePath:"",
    iconLeft:"delete.png",
    iconRight:"add.png",
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
		catalogFreePerimeterSelector.superclass.initComponent.call(this);
        this.addEvents({
            'change' : true,
            'addItemTo' : true,
            'removeItemTo' : true
        });
    },
    
    onRender: function(ct, position){
    	
    	catalogFreePerimeterSelector.superclass.onRender.call(this, ct, position);

        this.fieldnorth = {
	            id:this.id+'_north',
	            disabled:this.disabled,
	            xtype: 'textfield',
				name: this.id+'_north',
	            allowBlank: true,
	            width: 200
		    	};
		this.fieldsouth = {
	            id:this.id+'_south',
	            disabled:this.disabled,
	            xtype: 'textfield',
				name: this.id+'_south',
	            allowBlank: true,
	            width: 200
		    	};
		this.fieldeast = {
	            id:this.id+'_east',
	            disabled:this.disabled,
	            xtype: 'textfield',
				name: this.id+'_east',
	            allowBlank: true,
	           width: 200
		    	};
		this.fieldwest = {
	            id:this.id+'_west',
	            disabled:this.disabled,
	            xtype: 'textfield',
				name: this.id+'_west',
	            allowBlank: true,
	             width: 200
		    	};
		
        this.fromMultiselect = new Ext.Panel({
    			layout:"table",
    			border:this.border,
    			width: 200,
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
		    			text: this.westLabel,
		    			margins: '0 0 0 10'
    		    	},
    		    	this.fieldwest,
    		    	{
    		    		xtype: 'spacer',
    		    		height: 10
		    		},
		    		{
	    		        xtype: 'label',
	    		        text: this.eastLabel,
	    		        margins: '0 0 0 10'
    		    	},
    		    	this.fieldeast
    		    	]
            });
        
        this.toMultiselect = new Ext.ux.form.MultiSelect(this.multiselects[0]);
        
        var p = new Ext.Panel({
            bodyStyle:this.bodyStyle,
            border:this.border,
            layout:"table",
            width:550,
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
        
        if(this.disabled == false){
        	this.addIcon.on('click', this.fromTo, this);
        	this.removeIcon.on('click', this.toFrom, this);
        }
        
        var tb = p.body.first();
        this.el.setWidth(p.body.first().getWidth());
        p.body.removeClass();

        this.hiddenName = this.name;
        var hiddenTag = {tag: "input", type: "hidden", value: "", name: this.name};
        this.hiddenField = this.el.createChild(hiddenTag);
        
        this.boundaryItemSelector.handleValidSelectionBoundaryCount();
    },
    
    doLayout: function(){
        if(this.rendered){
            this.toMultiselect.fs.doLayout();
        }
    },
    
    afterRender: function(){
    	catalogFreePerimeterSelector.superclass.afterRender.call(this);

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
      		text: '['+(Math.round(northvalue*1000))/1000+'...,'+(Math.round(southvalue*1000))/1000+'...,'+(Math.round(eastvalue*1000))/1000+'...,'+(Math.round(westvalue*1000))/1000+'...]',
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
         
         this.boundaryItemSelector.handleValidSelectionBoundaryCount();
         
    },
    
    isMaxCardReach: function(){
    	var totalSelectionCount =this.toMultiselect.view.store.getCount() + this.boundaryItemSelector.toMultiselect.view.store.getCount(); 
        if(totalSelectionCount == this.maxcardbound){
    		alert (this.maxcardReachMessage);
    		return true;
        }else{
        	return false;
        }	
    },
    
    fromTo : function() {
         var northvalue =  Ext.getCmp(this.id+'_north').getValue();
         var eastvalue =  Ext.getCmp(this.id+'_east').getValue();
         var southvalue =  Ext.getCmp(this.id+'_south').getValue();
         var westvalue =  Ext.getCmp(this.id+'_west').getValue();

         var  record = this.buildRecord(northvalue, southvalue, eastvalue, westvalue);
        
         var totalSelectionCount =this.toMultiselect.view.store.getCount() + this.boundaryItemSelector.toMultiselect.view.store.getCount(); 
         if(totalSelectionCount == this.maxcardbound){
     		alert (this.maxcardReachMessage);
     	 }else{
     		this.toMultiselect.view.store.add(record);
	     	 Ext.getCmp(this.id+'_north').setValue('');
	         Ext.getCmp(this.id+'_east').setValue('');
	         Ext.getCmp(this.id+'_south').setValue('');
	         Ext.getCmp(this.id+'_west').setValue('');
	         this.fireEvent ('addItemTo', record);
	         this.toMultiselect.view.refresh();
	         var si = this.toMultiselect.store.sortInfo;
	         if(si){
	             this.toMultiselect.store.sort(si.field, si.direction);
	         }   
	         
	         this.boundaryItemSelector.handleValidSelectionBoundaryCount();
     	}
         
        
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
                this.fireEvent ('removeItemTo', record);
                this.toMultiselect.view.store.remove(record);
               
            }
            
            this.boundaryItemSelector.handleValidSelectionBoundaryCount();
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
    }
//    ,
    
//    clones : function(card, ownerCtrl, isClone) 
//	{			
//    	var panel = (ownerCtrl) ? ownerCtrl : this.ownerCt;
//    	var isClone = (isClone!=undefined) ? isClone : true;
//		var master = this;
//
//		if ( this.template && Ext.getCmp(this.template.getId()) && panel.findById(this.template.getId())) 
//		{
//			master = this.template;
//		}
//		
//		var cmps  = panel.findBy(
//			function(cmp) 
//			{
//				if ( cmp.template ) 
//				{
//					return cmp.template == this.template;
//				}
//			},{template:master});
//
//		if ( Ext.isEmpty(card)) 
//		{
//			return cmps;
//		}										
//		
//		// sanitize amount of clones untill cardinality is reached
//		if ( !Ext.isEmpty(card) ) {
//			
//			// add clones untill card is reached			
//			for ( var i = cmps.length ; i < card ; i ++ ) {
//				var parentName = panel.getId();
//				var name = master.getId();
//
//				if (isClone)
//				{
//					var masterName = parentName + name.substring(parentName.length);
//					master = Ext.getCmp(masterName);
//				}
//				if (!master.clones_count)
//					master.clones_count=1;
//				var partOfNameToModify = name.substring(parentName.length);
//				var aName = name.split('__');
//				var sName = name.split('-');
//                var partOfNameToModify2 = name.substring(parentName.length, name.length-aName[aName.length - 1].length);
//				if(partOfNameToModify2.match("^-") != "-")
//				    partOfNameToModify2 = "-"+partOfNameToModify2;
//				
//			    master.clones_count = master.clones_count+1;
//			    if (isClone)
//					clones_count = master.clones_count;
//				else
//					clones_count = 1;
//
//			    var nameEndPart = partOfNameToModify.substring(partOfNameToModify2.length+String(master.clones_count).length);
//				var newName = parentName + partOfNameToModify2 + clones_count + nameEndPart;
//				
//				var destinationDS = new   Ext.data.ArrayStore({
//					data: [],
//					fields: ['value','text'],
//				        sortInfo: {
//				            field: 'value',
//				            direction: 'ASC'
//				        }
//				});
//				var comboboxName = Ext.get(parentName+"-sdi_extentType__1");
//				var boundaryItemSelectorName = Ext.get(parentName+"-gmd_geographicElement__1");
//				
//				var clone = master.cloneConfig({
//					id : newName,
//					name : newName,
//					comboboxname: comboboxName,
//					hiddenName: newName + '_hidden',
//					clone : isClone,
//					clones_count: clones_count,
//					template : master,
//					boundaryItemSelector:boundaryItemSelectorName,
//					multiselects: [{
//							id: newName+'_selected',
//			            	name: newName+'_selected',
//			            	legend: 'Selected',
//			            	minOccurs:1,
//	            			maxOccurs:1,
//			                dynamic:true,
//			                width: 200,
//			                height: 200,
//			                store: destinationDS,
//			                displayField: 'text',
//			                valueField: 'value'
//			            }],
//					iconCfg : {cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'}																	   
//				});
//				
//				
//				
//				if (isClone)
//				{
//					var idx = idx = panel.items.indexOf(master)+1+i;
//			   		panel.insert(idx,clone);	
//				}
//				else
//				{
//					panel.add(clone);
//				}
//				
//				clone.setValue(master.defaultVal);
//				panel.doLayout();
//			}			
//
//			// remove clones untill cardinality is reached
//			for ( var i = cmps.length ; i > card ; i -- ) {
//					var field = cmps[i-1];
//					var item = Ext.get(field.el.findParent('.x-form-item'));
//					item.remove();
//					panel.remove(field);			
//			}
//			cmps  = panel.findBy(function(cmp) {
//						 if ( cmp.template ) {
//							return cmp.template == this.template;
//						 }
//						},{template:master});
//		}
//		return cmps;								
//	}
    
   
});

Ext.reg('catalogFreePerimeterSelector', catalogFreePerimeterSelector);