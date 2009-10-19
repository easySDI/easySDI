Ext.override(Ext.form.TriggerField, {
    alignErrorIcon : function() {
        this.errorIcon.alignTo(this.wrap, 'tl-tr', [2 + this.dicon? this.dicon.getWidth() + 4 : 0, 0]);
    }
});	

Ext.override(Ext.form.Field, {
    dynamic : false,
	
	/**
	 * Clones a field untill the required amount specified is reached
	 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
	 * @return {Array}  required clones of type {Ext.form.Field}  
	 */		
	clones : function(card, ownerCtrl, isClone) 
	{			
    	var panel = (ownerCtrl) ? ownerCtrl : this.ownerCt;
    	var isClone = (isClone!=undefined) ? isClone : true;
		var master = this;

		if ( this.template && Ext.getCmp(this.template.getId()) && panel.findById(this.template.getId())) 
		{
			master = this.template;
			//console.log("master1");
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
		//
		// sanitize amount of clones untill cardinality is reached
		if ( !Ext.isEmpty(card) ) {
			//
			// add clones untill card is reached			
			for ( var i = cmps.length ; i < card ; i ++ ) {
				
				var parentName = panel.getId();
				var name = master.getId();
					//console.log("Field: "+name + '_index');
				var oldIndexComponent = Ext.ComponentMgr.get(name + '_index');
				
				var partOfNameToModify = name.substring(parentName.length);
				var partOfNameToModify2 = name.substring(parentName.length,name.length-oldIndexComponent.getValue().length);
				
				var indexComponent = Ext.ComponentMgr.get(parentName + partOfNameToModify + '_index');
				var newVal = 1;
				var newPos = 1;
				if (indexComponent!=undefined)
				{	var newVal = Number(indexComponent.getValue()) + 1;
					newPos = indexComponent.getValue().length;
			    	indexComponent.setValue(newVal);
				}

				var nameEndPart = partOfNameToModify.substring(partOfNameToModify2.length+oldIndexComponent.getValue().length);
			    var newName = parentName + partOfNameToModify2 + newVal + nameEndPart;
			    
				var clone = master.cloneConfig({
					id : newName,
					name : newName,
					clone : isClone,
					template : master,
					iconCfg : {cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'},
					listeners : { 'onIcon' : {fn: function(field) {
												var item = Ext.get(field.el.findParent('.x-form-item'));
												item.remove();
												panel.remove(field);
												panel.doLayout();
											  }}											 
								}																	   
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
				if (clone.xtype!='multiselect') 
					clone.setValue('');
				panel.doLayout();
			}			

			//
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
	onIcon : function(e,icon) {
		this.fireEvent('onIcon',this);
	},
	onPlusIcon : function(e,icon) {
		this.fireEvent('onPlusIcon',this);
	},
	onMinusIcon : function(e,icon) {
		this.fireEvent('onMinusIcon',this);
	},
	getIconCt : function(el){
		return  el.findParent('.x-form-element', 5, true) || // use form element wrap if available
			el.findParent('.x-form-field-wrap', 5, true);   // else direct field wrap
	},
	alignMinusIcon : function(){
		if ( this.isXType('combo') ||  this.isXType('datefield') ) {
			this.dicon.alignTo(this.el, 'tl-tr', [32, 3]);
		} else {
			if (this.dicon)
				this.dicon.alignTo(this.el, 'tl-tr', [17, 3]);
			else
				this.dicon.alignTo(this.el, 'tl-tr', [17, 3]);
		}	
		this.minusIcon = this.dicon;
			
	},
	alignPlusIcon : function(){
		if ( this.isXType('combo') ||  this.isXType('datefield') ) {
			this.dicon.alignTo(this.el, 'tl-tr', [17, 3]);
		} else {
			this.dicon.alignTo(this.el, 'tl-tr', [2, 3]);
		}	
		this.plusIcon = this.dicon;
			
	},
	alignIcon : function(){
		if ( this.isXType('combo') ||  this.isXType('datefield') ) {
			this.dicon.alignTo(this.el, 'tl-tr', [17, 3]);
		} else {
			this.dicon.alignTo(this.el, 'tl-tr', [2, 3]);
		}
	},
	alignErrorIcon : function() {
        this.errorIcon.alignTo(this.el, 'tl-tr', [2 + this.dicon? this.dicon.getWidth() + 4 : 20, 0]);
    },
    manageIcons: function(component)
    {
    	if (component.xtype!='hidden')
    	{
		var isHiddenPlus = (component.clone) ? true : false;
		var isHiddenMinus = false;
		
		if (component.minOccurs==0) component.minOccurs=1;
		
		//console.log(component.getId()+"-"+component.minOccurs+"-"+component.maxOccurs+"-"+component.clones().length);
		if (component.minOccurs==1 && component.maxOccurs==1) 
		{
			isHiddenMinus = true;
			isHiddenPlus = true;
		}
		
		if (component.clones().length+1 == component.maxOccurs) isHiddenPlus=true;
		if (component.clones().length+1 == component.minOccurs) isHiddenMinus=true;

		var plusIcon = component.plusIcon;
		var minusIcon = component.minusIcon;
		if (plusIcon) (isHiddenPlus) ? plusIcon.setVisible(false) : plusIcon.setVisible(true);
		if (minusIcon) (isHiddenMinus) ? minusIcon.setVisible(false) : minusIcon.setVisible(true);
		}
    },
	afterRender : Ext.form.Field.prototype.afterRender.createSequence(function() { 			
		if ( this.dynamic) 
		{			
			this.addIcon({cls:'x-tool x-tool-plus',clsOnOver:'x-tool-plus-over'});
			this.addIcon({cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'});
			this.addListener('onPlusIcon',function(field) 
			{
			   	var cnt = this.clones().length;
			   	var panel = this.ownerCt;	
			   	this.clones(cnt+1);
				var listOfClones = field.clones();
				var lastClone = listOfClones[listOfClones.length-1];									
				if (lastClone) lastClone.manageIcons(lastClone);
			   	panel.doLayout();
			   	field.manageIcons(field);
			});
			
			this.addListener('onMinusIcon',function(field) 
			{
		    	var cnt = this.clones().length;
				var item = Ext.get(field.el.findParent('.x-form-item'));
			    var fieldset = field.ownerCt;
				
		    	// Traitement pour le premier élément de la liste, le master
		    	if (!field.clone)
		    	{
					
					// Get the first clone af the master
					var listOfClones = field.clones();
					var firstClone = listOfClones[0];			
					var name = field.getId();						
					var oldIndex = name + "_index";
					var oldCmp = Ext.getCmp(oldIndex);					
					var newName = firstClone.getId() + "_index";
					var newValue = oldCmp.getValue();
					var idx = fieldset.items.indexOf(oldCmp);
					fieldset.remove(oldCmp, true);
					var newCmp = new Ext.ux.ExtendedHidden({id: newName, name: newName, value: newValue});

					fieldset.insert(idx,newCmp);
					fieldset.doLayout();
					
					firstClone.clone = false;
					firstClone.template = undefined;
					item.remove();
					fieldset.remove(field, true);
					fieldset.doLayout();

					for (i=1; i < listOfClones.length; i++)
					{
						listOfClones[i].template = firstClone;
					}
					firstClone.manageIcons(firstClone);
			    }
			    else
			    {
			    	var tmpl = field.template;
			    	item.remove();
					fieldset.remove(field, true);
					fieldset.doLayout();
					tmpl.manageIcons(tmpl); //mise a jour des boutons
							
							var listOfClones = tmpl.clones();
							var lastClone = listOfClones[listOfClones.length-1];									
							if (lastClone) lastClone.manageIcons(lastClone);
			    }
			});
		} 
		else 
		{
			this.addIcon(this.iconCfg);
		}
		this.manageIcons(this);
	}),
	
	/**
	 * Add icon on rightside of field to create the ability to implement dynamic behaviour in the context of the specified field.
	 * Example of its usage : see implementation of clones method of {Ext.form.Field}
	 * @param {Object}  
	 */		
	addIcon : function(iconCfg){
		if(!this.rendered || this.preventMark || Ext.isEmpty(iconCfg)){ // not rendered
			return;
		}
		
		if(!this.dicon){
			var elp = this.getIconCt(this.el);
			if(!elp){ // field has no container el
				return;
			}
			this.dicon = elp.createChild({cls:iconCfg.cls});
			this.dicon.setStyle( {position:'absolute'}) 
			this.dicon.addClassOnOver(iconCfg.clsOnOver);
			if (iconCfg.clsOnOver == 'x-tool-plus-over')
			{
				this.dicon.addListener('click',this.onPlusIcon,this);	
				this.plusIcon = this.dicon;			
			}
			else if (iconCfg.clsOnOver == 'x-tool-minus-over')
			{
				this.dicon.addListener('click',this.onMinusIcon,this);	
				this.minusIcon = this.dicon;			
			}
			else
			{
				this.dicon.addListener('click',this.onIcon,this);
			}

			this.alignIcon();
            this.on('resize', this.alignIcon, this);
		}
		else
		{
			var elp = this.getIconCt(this.el);
			if(!elp){ // field has no container el
				return;
			}
			this.dicon = elp.createChild({cls:iconCfg.cls});
			this.dicon.setStyle( {position:'absolute'}) 
			this.dicon.addClassOnOver(iconCfg.clsOnOver);
			this.dicon.addListener('click',this.onMinusIcon,this);
			this.alignMinusIcon();				
			
            this.on('resize', this.alignMinusIcon, this);
		}
	}
});