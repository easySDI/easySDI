Ext.override(Ext.form.FieldSet, {
    dynamic : false,
	
	/**
	 * Clones a fieldset untill the required amount specified is reached
	 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
	 * @return {Array}  required clones of type {Ext.form.FieldSet}  
	 */				
	clones : function(card,ownerCtrl,isClone) {
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
				var oldIndexComponent = Ext.ComponentMgr.get(name + '_index');
					//console.log("Fieldset: "+name + '_index');
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
			    
				var coll=false;
				if (master.relation && !isClone) coll=true;
			    
			    
				var clone = master.cloneConfig({
					id : newName,
					name : newName,
					clone : isClone,
					template : master,
					collapsible: !coll,
					collapsed: coll
				});

				clone.constructClone(master);
				if (isClone)
				{
					var idx = idx = panel.items.indexOf(master)+1+i;
			   		panel.insert(idx,clone);	
				}
				else
				{
					panel.add(clone);
				}
				panel.doLayout();
			}	
			
			
			
			//
			// remove clones untill cardinality is reached
			for ( var i = cmps.length ; i > card ; i -- ) {
					var fieldset = cmps[i-1];
					panel.remove(fieldset,true);
			}				
			cmps  = panel.findBy(function(cmp) {
						 if ( cmp.template ) {
							return cmp.template == this.template;
						 }
						},{template:master});					
		}
		return cmps;								
	},
	manageIcons: function(component)
	{
		var isHiddenPlus = (component.clone) ? true : false;
		var isHiddenMinus = (component.clone) ? false : true;
		
		//console.log(component.minOccurs+"-"+component.maxOccurs+"-"+component.clone);
		if (component.minOccurs==1 && component.maxOccurs==1 && component.clone) 
		{
			isHiddenMinus = true;
			isHiddenPlus = true;
		}
		
		if (!component.clone && component.clones().length == component.maxOccurs) isHiddenPlus=true;
		if (component.clone && component.clones().length == component.minOccurs) isHiddenMinus=true;
		
/*		if (!component.clone)
		{
			var cnt = component.clones().length;
			if (cnt>0)
			{
				isHiddenMinus = false;
			}
			else
			{
				isHiddenMinus = true;
			}
		}*/
		if (component.tools) if (component.getTool('minus')) (isHiddenMinus) ? component.getTool('minus').hide() : component.getTool('minus').show();
		if (component.tools) if (component.getTool('plus')) (isHiddenPlus) ? component.getTool('plus').hide() : component.getTool('plus').show();
	},
	constructClone : function(original) 
	{
		var childs = Ext.getCmp(original.getId()).items;

		// Clone each child and add it to the clone of the original parent
		for (var i=0; i<childs.length;i++)
		{
			// Get the child
			var child = childs.get(i);
			
			if (!child.clone)
			{
				child.clones(1,this, false);
			}
		}
	},
	collapseFieldSet: function(component)
	{
   			var cmpId = component.getId();
   			var cmpIsFirst = cmpId.substring(cmpId.length-3)=='__1';
   			
   			//console.log(cmpId);
   			if (!component.clone && component.dynamic && cmpIsFirst && component.relation) 
   			{
   				component.collapse(false);
   				component.getTool('toggle').hide();
   				component.doLayout();
   			}
   	},
	onRender : Ext.form.FieldSet.prototype.onRender.createInterceptor(function(ct, position) 
	{ 	
		if ( this.dynamic) 
		{	
			
			this.on("afterrender",this.manageIcons);
			//this.on("afterrender",this.collapseFieldSet);
			
			this.tools = 
			[ 
				{
					id:'plus', 
					handler: function(event, toolEl, fieldset)
					{	
						var cnt = fieldset.clones().length;
						   if ( !Ext.isEmpty(fieldset.maxOccurs) ) {
							   if ( fieldset.maxOccurs  <= cnt) {
								   fieldset.fireEvent('maxoccurs',fieldset);
								   return;													   
							   }
						    }	
						    
						fieldset.cascade(function(cmp)
						{
							if (cmp.xtype=='fieldset') cmp.layout.layout(true);
						});
						
						var panel = fieldset.ownerCt;
						fieldset.clones(cnt+1);
						var listOfClones = fieldset.clones();
						var lastClone = listOfClones[listOfClones.length-1];									
						if (lastClone) lastClone.manageIcons(lastClone);
						fieldset.manageIcons(fieldset);
						//fieldset.collapseFieldSet(fieldset);
						panel.doLayout();																								   
					}
				},
				{
					id:'minus', 
					handler: function(event, toolEl, fieldset)
					{	
						var cnt = fieldset.clones().length;
					   if ( !Ext.isEmpty(fieldset.minOccurs) ) {
					   	   //console.log(fieldset.minOccurs);
						   if ( fieldset.minOccurs  >= cnt) {
							   fieldset.fireEvent('minoccurs',fieldset);
							   return;													   
						   }
					    }
						var panel = fieldset.ownerCt;

						if (!fieldset.clone)
						{									
							var listOfClones = fieldset.clones();
							var firstClone = listOfClones[0];			
							var name = fieldset.getId();						
							var oldIndex = name + "_index";
							var oldCmp = Ext.getCmp(oldIndex);									
							var newName = firstClone.getId() + "_index";
							var newValue = oldCmp.getValue();
							var idx = panel.items.indexOf(oldCmp);
							panel.remove(oldCmp, true);
							var newCmp = new Ext.ux.ExtendedHidden({id: newName, name: newName, value: newValue});

							panel.insert(idx,newCmp);
							panel.doLayout();
			
							firstClone.clone = false;
							firstClone.template = undefined;
							panel.remove(fieldset, true);
							firstClone.doLayout();
							
							for (i=1; i < listOfClones.length; i++)
							{
								listOfClones[i].template = firstClone;
							}
							firstClone.manageIcons(firstClone); //mise a jour des boutons
						}
						else
						{
							var tmpl = fieldset.template;
							panel.remove(fieldset, true);
							tmpl.manageIcons(tmpl); //mise a jour des boutons
							
							var listOfClones = tmpl.clones();
							var lastClone = listOfClones[listOfClones.length-1];									
							if (lastClone) lastClone.manageIcons(lastClone);
						}
					}
				}
			];
		}
	})
});