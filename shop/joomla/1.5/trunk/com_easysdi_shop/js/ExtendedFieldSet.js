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
				//var oldIndexComponent = Ext.ComponentMgr.get(name + '_index');
					//console.log("Fieldset: "+name + '_index');
				var partOfNameToModify = name.substring(parentName.length);
				var partOfNameToModify2 = name.substring(parentName.length,name.length-String(master.clones_count).length);
			    
			    master.clones_count = master.clones_count+1;
			    clones_count = master.clones_count;
				
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
			    var newName = parentName + partOfNameToModify2 + clones_count + nameEndPart;
			    //console.log(partOfNameToModify+" - "+partOfNameToModify2+" - "+nameEndPart+" - "+String(master.clones_count).length);
				var coll=false;
				if (master.relation && !isClone) coll=true;
			    
			    
				var clone = master.cloneConfig({
					id : newName,
					name : newName,
					clone : isClone,
					clones_count: clones_count,
					template : master,
					collapsible: !coll,
					collapsed: coll
				});
				
				//console.log(name+" - "+clones_count);

				clone.constructClone(master);
				if (isClone)
				{
					if (panel.items.indexOf(master) == 0)
						var idx = panel.items.indexOf(master)+2+i;
					else
						var idx = panel.items.indexOf(master)+1+i;
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
	manageTitle: function(component)
	{
		if (component.clone)
		{
			var clones = component.template.clones();
			
			for (var i=0 ; i<clones.length ; i++)
			{
			//console.log(clones[i].getId());
				var position = clones.indexOf(clones[i]);
				clones[i].setTitle(clones[i].originalTitle + " - N&deg;" + (Number(position)+1));
			}
		}
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
	onRender : Ext.form.FieldSet.prototype.onRender.createInterceptor(function(ct, position) 
	{ 	
		if ( this.dynamic) 
		{	
			
			this.on("afterrender",this.manageIcons);
			this.on("afterrender",this.manageTitle);
			
			this.tools = 
			[ 
				{
					id:'plus', 
					handler: function(event, toolEl, fieldset)
					{	
						var cnt = fieldset.clones().length;				    
						fieldset.cascade(function(cmp)
						{
							if (cmp.xtype=='fieldset') cmp.layout.layout(true);
						});
						
						var panel = fieldset.ownerCt;
						fieldset.clones(cnt+1);
						var listOfClones = fieldset.clones();
						var firstClone = listOfClones[0];									
						if (firstClone) firstClone.manageIcons(firstClone);
						fieldset.manageIcons(fieldset);
						fieldset.manageTitle(fieldset);
						panel.doLayout();																								   
					}
				},
				{
					id:'minus', 
					handler: function(event, toolEl, fieldset)
					{	
						var cnt = fieldset.clones().length;
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
							firstClone.manageTitle(firstClone); //mise a jour des boutons
						}
						else
						{
							var tmpl = fieldset.template;
							panel.remove(fieldset, true);
							tmpl.manageIcons(tmpl); //mise a jour des boutons
							
							var listOfClones = tmpl.clones();
							var firstClone = listOfClones[0];									
							if (firstClone) firstClone.manageIcons(firstClone);									
							if (firstClone) firstClone.manageTitle(firstClone);
						}
					}
				}
			];
		}
	})
});