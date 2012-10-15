

/**
 * @class Ext.ux.form.ItemSelector
 * @extends Ext.form.Field
 * A control that allows selection of between two Ext.ux.form.MultiSelect controls.
 *
 *  @history
 *    2008-06-19 bpm Original code contributed by Toby Stuart (with contributions from Robert Williams)
 *
 * @constructor
 * Create a new ItemSelector
 * @param {Object} config Configuration options
 * @xtype itemselector 
 */
BoundaryItemSelector = Ext.extend(Ext.form.Field,  {
    hideNavIcons:false,
    imagePath:"",
    iconUp:"arrow_up.png",
    iconDown:"arrow_down.png",
    iconLeft:"arrow_left.png",
    iconRight:"arrow_right.png",
    iconTop:"arrow_top.png",
    iconBottom:"arrow_bottom.png",
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
    freePerimeterSelector:null,
    isValidCmp:true,
    
    /**
     * EasySDI
     */
    dynamic : false,
    clone : false,
    comboboxname : null,
    maxcardbound : 1,
    mincardbound : 1,
    
    /**
     * @cfg {Array} multiselects An array of {@link Ext.ux.form.MultiSelect} config objects, with at least all required parameters (e.g., store)
     */
    multiselects:null,

    initComponent: function(){
    	BoundaryItemSelector.superclass.initComponent.call(this);
        this.addEvents({
            'rowdblclick' : true,
            'change' : true,
            'addItemTo' : true,
            'removeItemTo' : true
        });
    },

    onRender: function(ct, position){
        BoundaryItemSelector.superclass.onRender.call(this, ct, position);

        // Internal default configuration for both multiselects
        var msConfig = [{
            legend: 'Available',
            draggable: true,
            droppable: true,
            width: 100,
            height: 100
        },{
            legend: 'Selected',
            droppable: true,
            draggable: true,
            width: 100,
            height: 100
        }];

       	this.fromMultiselect = new Ext.ux.form.MultiSelect(Ext.applyIf(this.multiselects[0], msConfig[0]));
       	this.fromMultiselect.on('dblclick', this.onRowDblClick, this);

        this.toMultiselect = new Ext.ux.form.MultiSelect(Ext.applyIf(this.multiselects[1], msConfig[1]));
        this.toMultiselect.on('dblclick', this.onRowDblClick, this);

        this.p = new Ext.Panel({
            bodyStyle:this.bodyStyle,
            border:this.border,
            layout:"table",
            width:550,
            layoutConfig:{columns:3}
        });

        this.p.add(this.fromMultiselect);
        var icons = new Ext.Panel({header:false});
        this.p.add(icons);
        this.p.add(this.toMultiselect);
        this.p.render(this.el);
        icons.el.down('.'+icons.bwrapCls).remove();

        // ICON HELL!!!
        if (this.imagePath!="" && this.imagePath.charAt(this.imagePath.length-1)!="/")
            this.imagePath+="/";
        this.iconUp = this.imagePath + (this.iconUp || 'up2.gif');
        this.iconDown = this.imagePath + (this.iconDown || 'down2.gif');
        this.iconLeft = this.imagePath + (this.iconLeft || 'left2.gif');
        this.iconRight = this.imagePath + (this.iconRight || 'right2.gif');
        this.iconTop = this.imagePath + (this.iconTop || 'top2.gif');
        this.iconBottom = this.imagePath + (this.iconBottom || 'bottom2.gif');
        var el=icons.getEl();
        this.toTopIcon = el.createChild({tag:'img', src:this.iconTop, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.upIcon = el.createChild({tag:'img', src:this.iconUp, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.addIcon = el.createChild({tag:'img', src:this.iconRight, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.removeIcon = el.createChild({tag:'img', src:this.iconLeft, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.downIcon = el.createChild({tag:'img', src:this.iconDown, style:{cursor:'pointer', margin:'2px'}});
        el.createChild({tag: 'br'});
        this.toBottomIcon = el.createChild({tag:'img', src:this.iconBottom, style:{cursor:'pointer', margin:'2px'}});
        this.toTopIcon.on('click', this.toTop, this);
        this.upIcon.on('click', this.up, this);
        this.downIcon.on('click', this.down, this);
        this.toBottomIcon.on('click', this.toBottom, this);
        this.addIcon.on('click', this.fromTo, this);
        this.removeIcon.on('click', this.toFrom, this);
        if (!this.drawUpIcon || this.hideNavIcons) { this.upIcon.dom.style.display='none'; }
        if (!this.drawDownIcon || this.hideNavIcons) { this.downIcon.dom.style.display='none'; }
        if (!this.drawLeftIcon || this.hideNavIcons) { this.addIcon.dom.style.display='none'; }
        if (!this.drawRightIcon || this.hideNavIcons) { this.removeIcon.dom.style.display='none'; }
        if (!this.drawTopIcon || this.hideNavIcons) { this.toTopIcon.dom.style.display='none'; }
        if (!this.drawBotIcon || this.hideNavIcons) { this.toBottomIcon.dom.style.display='none'; }

        var tb = this.p.body.first();
        this.el.setWidth(this.p.body.first().getWidth());
        this.p.body.removeClass();

        this.hiddenName = this.name;
        var hiddenTag = {tag: "input", type: "hidden", value: "", name: this.name};
        this.hiddenField = this.el.createChild(hiddenTag);
        
        
		if(this.toMultiselect.view.store.getCount() < this.mincardbound){
         	this.toMultiselect.markInvalid(String.format(this.toMultiselect.minSelectionsText, this.toMultiselect.minSelections));
         	this.isValidCmp = false;
         }else{
         	this.toMultiselect.clearInvalid();
         	this.isValidCmp = true;
         }
    },
    
    doLayout: function(){
        if(this.rendered){
           	this.fromMultiselect.fs.doLayout();
            this.toMultiselect.fs.doLayout();
        }
    },

    afterRender: function(){
        BoundaryItemSelector.superclass.afterRender.call(this);

        //this.markInvalid(String.format(this.minSelectionsText, this.minSelections));
        
        this.toStore = this.toMultiselect.store;
        this.toStore.on('add', this.valueChanged, this);
        this.toStore.on('remove', this.valueChanged, this);
        this.toStore.on('load', this.valueChanged, this);
        this.valueChanged(this.toStore);

    },
    
    setFromMultiSelect: function (multiSelect){
    	this.fromMultiselect = new Ext.ux.form.MultiSelect(multiSelect);;
    	this.fromMultiselect.on('dblclick', this.onRowDblClick, this);
    	this.p.render (this.el);
    },

    toTop : function() {
        var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
        var records = [];
        if (selectionsArray.length > 0) {
            selectionsArray.sort();
            for (var i=0; i<selectionsArray.length; i++) {
                record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
                records.push(record);
            }
            selectionsArray = [];
            for (var i=records.length-1; i>-1; i--) {
                record = records[i];
                this.toMultiselect.view.store.remove(record);
                this.toMultiselect.view.store.insert(0, record);
                selectionsArray.push(((records.length - 1) - i));
            }
        }
        this.toMultiselect.view.refresh();
        this.toMultiselect.view.select(selectionsArray);
    },

    toBottom : function() {
        var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
        var records = [];
        if (selectionsArray.length > 0) {
            selectionsArray.sort();
            for (var i=0; i<selectionsArray.length; i++) {
                record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
                records.push(record);
            }
            selectionsArray = [];
            for (var i=0; i<records.length; i++) {
                record = records[i];
                this.toMultiselect.view.store.remove(record);
                this.toMultiselect.view.store.add(record);
                selectionsArray.push((this.toMultiselect.view.store.getCount()) - (records.length - i));
            }
        }
        this.toMultiselect.view.refresh();
        this.toMultiselect.view.select(selectionsArray);
    },

    up : function() {
        var record = null;
        var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
        selectionsArray.sort();
        var newSelectionsArray = [];
        if (selectionsArray.length > 0) {
            for (var i=0; i<selectionsArray.length; i++) {
                record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
                if ((selectionsArray[i] - 1) >= 0) {
                    this.toMultiselect.view.store.remove(record);
                    this.toMultiselect.view.store.insert(selectionsArray[i] - 1, record);
                    newSelectionsArray.push(selectionsArray[i] - 1);
                }
            }
            this.toMultiselect.view.refresh();
            this.toMultiselect.view.select(newSelectionsArray);
        }
    },

    down : function() {
        var record = null;
        var selectionsArray = this.toMultiselect.view.getSelectedIndexes();
        selectionsArray.sort();
        selectionsArray.reverse();
        var newSelectionsArray = [];
        if (selectionsArray.length > 0) {
            for (var i=0; i<selectionsArray.length; i++) {
                record = this.toMultiselect.view.store.getAt(selectionsArray[i]);
                if ((selectionsArray[i] + 1) < this.toMultiselect.view.store.getCount()) {
                    this.toMultiselect.view.store.remove(record);
                    this.toMultiselect.view.store.insert(selectionsArray[i] + 1, record);
                    newSelectionsArray.push(selectionsArray[i] + 1);
                }
            }
            this.toMultiselect.view.refresh();
            this.toMultiselect.view.select(newSelectionsArray);
        }
    },

    fromTo : function() {
        var selectionsArray = this.fromMultiselect.view.getSelectedIndexes();
        var records = [];
        if (selectionsArray.length > 0) {
            for (var i=0; i<selectionsArray.length; i++) {
                record = this.fromMultiselect.view.store.getAt(selectionsArray[i]);
                records.push(record);
            }
            if(!this.allowDup)selectionsArray = [];
            for (var i=0; i<records.length; i++) {
                record = records[i];
                if(this.allowDup){
                    var x=new Ext.data.Record();
                    record.id=x.id;
                    delete x;
                    this.toMultiselect.view.store.add(record);
                }else{
                	var totalSelectionCount = 0; 
                	if (typeof (this.freePerimeterSelector) == 'undefined' || this.freePerimeterSelector == null)
                		totalSelectionCount = this.toMultiselect.view.store.getCount() ;
                	else
                		totalSelectionCount = this.toMultiselect.view.store.getCount() + this.freePerimeterSelector.toMultiselect.view.store.getCount();
                	if(totalSelectionCount == this.maxcardbound){
                		alert (this.maxcardReachMessage);
                	}else{
	                    this.fromMultiselect.view.store.remove(record);
	                    this.toMultiselect.view.store.add(record);
	                    selectionsArray.push((this.toMultiselect.view.store.getCount() - 1));
	                    this.fireEvent ('addItemTo', record);
	                    
	                    this.handleValidSelectionBoundaryCount();
                	}
                }
            }
        }
        this.toMultiselect.view.refresh();
        this.fromMultiselect.view.refresh();
        var si = this.toMultiselect.store.sortInfo;
        if(si){
            this.toMultiselect.store.sort(si.field, si.direction);
        }
        this.toMultiselect.view.select(selectionsArray);
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
                
                if(!this.allowDup){
                	//EasySDI specific :
                	//Check if the 'to' record is from the same record category that the records display in the 'from' store
                	if(this.comboboxname != null){
                		var comboboxCategories = Ext.getCmp(this.comboboxname);
                		var selectedValue = comboboxCategories.getValue();
                		if(selectedValue && 0 != selectedValue.length && selectedValue != comboboxCategories.emptyText){
                				if(record.data.text.indexOf( '['+selectedValue+']') != -1){
	                				this.fromMultiselect.view.store.add(record);
	                                selectionsArray.push((this.fromMultiselect.view.store.getCount() - 1));
	                			}
	                		
                		}else{
                			this.fromMultiselect.view.store.add(record);
                            selectionsArray.push((this.fromMultiselect.view.store.getCount() - 1));
                		}
                	}else{
	                    this.fromMultiselect.view.store.add(record);
	                    selectionsArray.push((this.fromMultiselect.view.store.getCount() - 1));
                	}
                }
                this.fireEvent ('removeItemTo', record);
            }
            this.handleValidSelectionBoundaryCount();
        }
        this.fromMultiselect.view.refresh();
        this.toMultiselect.view.refresh();
        var si = this.fromMultiselect.store.sortInfo;
        if (si){
            this.fromMultiselect.store.sort(si.field, si.direction);
        }
        this.fromMultiselect.view.select(selectionsArray);
        
        
    },

    isValid :function (preventMark){
    	return this.isValidCmp;
    },
    
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

    onRowDblClick : function(vw, index, node, e) {
        if (vw == this.toMultiselect.view){
            this.toFrom();
        } else if (vw == this.fromMultiselect.view) {
            this.fromTo();
        }
        return this.fireEvent('rowdblclick', vw, index, node, e);
    },

    reset: function(){
        range = this.toMultiselect.store.getRange();
        this.toMultiselect.store.removeAll();
        this.fromMultiselect.store.add(range);
        var si = this.fromMultiselect.store.sortInfo;
        if (si){
            this.fromMultiselect.store.sort(si.field, si.direction);
        }
        this.valueChanged(this.toMultiselect.store);
    },
    
    clones : function(card, ownerCtrl, isClone) 
	{			
    	var panel = (ownerCtrl) ? ownerCtrl : this.ownerCt;
    	var isClone = (isClone!=undefined) ? isClone : true;
		var master = this;

		if ( this.template && Ext.getCmp(this.template.getId()) && panel.findById(this.template.getId())) 
		{
			master = this.template;
		}
		
		var cmps  = panel.findBy(
			function(cmp) 
			{
				if ( cmp.template ) 
				{
					return cmp.template == this.template;
				}
			},{template:master});

		if ( Ext.isEmpty(card)) 
		{
			return cmps;
		}										
		
		// sanitize amount of clones untill cardinality is reached
		if ( !Ext.isEmpty(card) ) {
			
			// add clones untill card is reached			
			for ( var i = cmps.length ; i < card ; i ++ ) {
				var parentName = panel.getId();
				var name = master.getId();

				if (isClone)
				{
					var masterName = parentName + name.substring(parentName.length);
					master = Ext.getCmp(masterName);
				}
				if (!master.clones_count)
					master.clones_count=1;
				var partOfNameToModify = name.substring(parentName.length);
				var aName = name.split('__');
				var sName = name.split('-');
                var partOfNameToModify2 = name.substring(parentName.length, name.length-aName[aName.length - 1].length);
				if(partOfNameToModify2.match("^-") != "-")
				    partOfNameToModify2 = "-"+partOfNameToModify2;
				
			    master.clones_count = master.clones_count+1;
			    if (isClone)
					clones_count = master.clones_count;
				else
					clones_count = 1;

			    var nameEndPart = partOfNameToModify.substring(partOfNameToModify2.length+String(master.clones_count).length);
				var newName = parentName + partOfNameToModify2 + clones_count + nameEndPart;
				
				var destinationDS = new   Ext.data.ArrayStore({
					data: [],
					fields: ['value','text'],
				        sortInfo: {
				            field: 'value',
				            direction: 'ASC'
				        }
				});
				
				 var sourceData = new Array();
				 
					 master.multiselects[0].store.each (function (record){
						 sourceData.push([record.data.value,record.data.text]); 
					 }, this);
					 
				
				 var	sourceDS = new   Ext.data.ArrayStore({
					data: sourceData,
					fields: ['value','text'],
				        sortInfo: {
				            field: 'value',
				            direction: 'ASC'
				        }
				});
				 
				var clone = master.cloneConfig({
					id : newName,
					name : newName,
					hiddenName: newName + '_hidden',
					clone : isClone,
					clones_count: clones_count,
					template : master,
					multiselects: [{
			            	legend: 'Available',
			            	id: newName+'_available',
			            	minOccurs:1,
	            			maxOccurs:1,
	            			dynamic:true,
			                width: 250,
			                height: 200,
			                store: sourceDS,
			                displayField: 'text',
			                valueField: 'value'
			            },{
			            	legend: 'Selected',
			            	id: newName+'_selected',
			                minOccurs:1,
	            			maxOccurs:1,
			                dynamic:true,
			                width: 250,
			                height: 200,
			                store: destinationDS,
			                displayField: 'text',
			                valueField: 'value'
			            }],
					iconCfg : {cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'}																	   
				});
				
				if (isClone)
				{
					var idx = idx = panel.items.indexOf(master)+1+i;
			   		panel.insert(idx,clone);	
				}
				else
				{
					panel.add(clone);
				}
				
				clone.setValue(master.defaultVal);
				panel.doLayout();
			}			

			// remove clones untill cardinality is reached
			for ( var i = cmps.length ; i > card ; i -- ) {
					var field = cmps[i-1];
					var item = Ext.get(field.el.findParent('.x-form-item'));
					item.remove();
					panel.remove(field);			
			}
			cmps  = panel.findBy(function(cmp) {
						 if ( cmp.template ) {
							return cmp.template == this.template;
						 }
						},{template:master});
		}
		return cmps;								
	},
	
	setFreePerimeterSelector : function(selector){
		this.freePerimeterSelector = selector;
	},
	
	 handleValidSelectionBoundaryCount :function (){
		var totalSelectionCount = null;
		if (typeof (this.freePerimeterSelector) == 'undefined' || this.freePerimeterSelector == null){
			totalSelectionCount =this.toMultiselect.view.store.getCount() ;
			if(totalSelectionCount < this.mincardbound){
	         	this.toMultiselect.markInvalid(String.format(this.toMultiselect.minSelectionsText, this.toMultiselect.minSelections));
	         	this.isValidCmp = false;
	         }else{
	         	this.toMultiselect.clearInvalid();
	         	this.isValidCmp = true;
	         }
	 	}else{
			totalSelectionCount =this.toMultiselect.view.store.getCount() + this.freePerimeterSelector.toMultiselect.view.store.getCount();
			if(totalSelectionCount < this.mincardbound){
	         	this.toMultiselect.markInvalid(String.format(this.toMultiselect.minSelectionsText, this.toMultiselect.minSelections));
	         	this.freePerimeterSelector.toMultiselect.markInvalid(String.format(this.freePerimeterSelector.toMultiselect.minSelectionsText, this.freePerimeterSelector.toMultiselect.minSelections));
	         	this.isValidCmp = false;
	         }else{
	         	this.toMultiselect.clearInvalid();
	         	this.freePerimeterSelector.toMultiselect.clearInvalid();
	         	this.isValidCmp = true;
	         }
	 	}
    }
});

Ext.reg('boundaryitemselector', BoundaryItemSelector);


