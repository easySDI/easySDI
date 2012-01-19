Ext.QuickTips.init();

Ext.override(Ext.form.TriggerField, {
    alignErrorIcon : function() {
        this.errorIcon.alignTo(this.wrap, 'tl-tr', [2 + this.dicon? this.dicon.getWidth() + 4 : 0, 0]);
    }
});	

var findLabel = function(field) 
{
	var wrapDiv = null;
	var label = null
	//find form-element and label?
	wrapDiv = field.getEl().up('div.x-form-element');
	if(wrapDiv)
	{
		label = wrapDiv.child('label'); 
	}
	if(label) 
	{
		return label;
	}
	
	//find form-item and label
	wrapDiv = field.getEl().up('div.x-form-item');
	if(wrapDiv)
	{
		label = wrapDiv.child('label'); 
	}
	
	if(label) 
	{
		return label; 
	}
} 


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
				if (isClone)
				{
					var masterName = parentName + name.substring(parentName.length);
					master = Ext.getCmp(masterName);
				}
				//console.log("Master: "+ master.getId());
				//console.log("Master clones_count: "+ master.clones_count);
				//console.log("parentName: "+ parentName);
				//console.log("name: "+ name);
				if (!master.clones_count)
					master.clones_count=1;
				//var oldIndexComponent = Ext.ComponentMgr.get(name + '_index');
				//console.log("Fieldset: "+name + '_index');
				var partOfNameToModify = name.substring(parentName.length);
				//console.log("partOfNameToModify: "+ partOfNameToModify);
				//var partOfNameToModify2 = name.substring(parentName.length,name.length-String(master.clones_count).length);
				//console.log("partOfNameToModify2: "+ partOfNameToModify2);
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
				
				
/*				var indexComponent = Ext.ComponentMgr.get(parentName + partOfNameToModify + '_index');
				var newVal = 1;
				var newPos = 1;
				if (indexComponent!=undefined)
				{	var newVal = Number(indexComponent.getValue()) + 1;
					newPos = indexComponent.getValue().length;
			    	indexComponent.setValue(newVal);
				}
*/				
				var nameEndPart = partOfNameToModify.substring(partOfNameToModify2.length+String(master.clones_count).length);
				//console.log("nameEndPart: "+ nameEndPart);
				var newName = parentName + partOfNameToModify2 + clones_count + nameEndPart;
				//console.log("newName: "+ newName);
				
				//hiddenName:id + '_hidden',
				//console.log(master.xtype);
				if (master.xtype == "choicetext" || master.xtype == "combo")
				{
					var clone = master.cloneConfig({
						id : newName,
						name : newName,
						hiddenName: newName + '_hidden',
						clone : isClone,
						clones_count: clones_count,
						template : master,
						iconCfg : {cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'}																	   
					});
				}
				else if (master.xtype == "superboxselect")
				{
					var clone = master.cloneConfig({
						id : newName,
						name : newName,
						hiddenName: newName + '_hidden[]',
						clone : isClone,
						clones_count: clones_count,
						template : master,
						iconCfg : {cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'}																	   
					});
				}
				else
				{
					var clone = master.cloneConfig({
						id : newName,
						name : newName,
						clone : isClone,
						clones_count: clones_count,
						template : master,
						iconCfg : {cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'}																	   
					});
				}
				
				if (isClone)
				{
					var idx = idx = panel.items.indexOf(master)+1+i;
			   		panel.insert(idx,clone);	
				}
				else
				{
					panel.add(clone);
				}
				
				
				//console.log(newName + " - " + clone.xtype);
				if (clone.xtype!='multiselect' && clone.xtype!='checkboxgroup' && clone.xtype!='radiogroup') 
				{
					clone.setValue(master.defaultVal);
				}
				/*
				// Traitement des checkboxs et radiobuttons
				if (clone.xtype=='checkboxgroup' || clone.xtype=='radiogroup') 
				{
					var listBoxes = clone.items;
					//console.log(listBoxes);
					for (var b=0; b<listBoxes.length; b++)
					{
						//console.log(listBoxes[b]);
						//console.log(listBoxes[b].xtype);
						if (listBoxes[b].xtype=='checkbox' || listBoxes[b].xtype=='radio') 
							listBoxes[b].checked = false;
					}
				}
				*/
				
				/*if (clone.xtype=='multiselect') 
					alert(clone.id + " - " + clone.getValue(clone));
				*/
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
		//console.log(isHiddenPlus+"-"+isHiddenMinus);
		if (component.minOccurs==1 && component.maxOccurs==1) 
		{
			isHiddenMinus = true;
			isHiddenPlus = true;
		}
		
		if (component.clones().length+1 == component.maxOccurs) isHiddenPlus=true;
		if (component.clones().length+1 == component.minOccurs) isHiddenMinus=true;

		if (component.xtype == "superboxselect")
		{
			isHiddenPlus=true;
			isHiddenMinus=true;
		}

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
					/*var name = field.getId();						
					var oldIndex = name + "_index";
					var oldCmp = Ext.getCmp(oldIndex);					
					var newName = firstClone.getId() + "_index";
					var newValue = oldCmp.getValue();
					var idx = fieldset.items.indexOf(oldCmp);
					fieldset.remove(oldCmp, true);
					var newCmp = new Ext.ux.ExtendedHidden({id: newName, name: newName, value: newValue});

					fieldset.insert(idx,newCmp);
					fieldset.doLayout();
					*/
					firstClone.clone = false;
					firstClone.template = undefined;
					firstClone.clones_count = field.clones_count;
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
		
		// Tooltips
		//console.log(this.name + " - " + this.xtype + " - " + this.qTip);
		var qt = this.qTip;        
		var dismissDelay = this.qTipDelay;
		if(qt){
			/*Ext.QuickTips.register({
				target:  this,
				title: '',
				text: qt,
				enabled: true
			});*/
			
			// Positionner l'aide contextuelle sur le label
			var label = findLabel(this);
			if (label)
			{
				Ext.QuickTips.register({
					target:  label,
					title: '',
					text: qt,
					dismissDelay: dismissDelay,
					enabled: true
					});
			}
		}	

		/* S'assurer que le nouveau champ a bien été validé. Dans certains cas,
		   on ne sait pourquoi, ce n'est pas fait automatiquement.*/
		this.isValid(false);
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