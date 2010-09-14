var findHeaderText = function(fieldset) 
{
	var wrapDiv = null;
	var header = null
	//find fieldset-header.text
	wrapDiv = fieldset.getEl();
	
	if(wrapDiv)
	{
		header = wrapDiv.child('legend').child('span'); 
	}
	
	if(header) 
	{
		return header;
	}
} 

Ext.override(Ext.form.FieldSet, {
    dynamic : false,
	
	/**
	 * Clones a fieldset untill the required amount specified is reached
	 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
	 * @return {Array}  required clones of type {Ext.form.FieldSet}  
	 */				
	clones : function(card,ownerCtrl,isClone, mas) {
    	var panel = (ownerCtrl) ? ownerCtrl : this.ownerCt;
    	var isClone = (isClone!=undefined) ? isClone : true;
    	//var master = (master!=undefined) ? master : this;
    	var master = this;
    	
		//console.log("master avant: " + master.getId());
		if ( this.template && Ext.getCmp(this.template.getId()) && panel.findById(this.template.getId())) 
		{
			master = this.template;
		}
		
		//console.log("master apres: " + master.getId());
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
			//console.log("Appel de clone: " + cmps.length + " - " + card + " - " + this.getId() + " - " + master.getId() + " - " + isClone); 
			//
			// add clones untill card is reached			
			for ( var i = cmps.length ; i < card ; i ++ ) {
				
				var parentName = panel.getId();
				var name = master.getId();
				//console.log("parentName: " + parentName);
				//console.log("name: " + name);
				//console.log("======================================================================");
				if (isClone)
				{
					var masterName = parentName + name.substring(parentName.length);
					//console.log ("masterName: " + masterName);
					master = Ext.getCmp(masterName);
				}
				//console.log("master: " + master.getId());
				//console.log("nombre d'enfants clones: " + master.clones_count);
				//var oldIndexComponent = Ext.ComponentMgr.get(name + '_index');
				//console.log("Fieldset: "+name + '_index');
				var partOfNameToModify = name.substring(parentName.length);
				//var partOfNameToModify = name.substring(name.lastIndexOf('-'));
				//console.log("partOfNameToModify: " + partOfNameToModify);
				//console.log("test: " + test);
				var partOfNameToModify2 = name.substring(parentName.length,name.length-String(master.clones_count).length);
				//var partOfNameToModify2 = name.substring(name.lastIndexOf('-'),name.length-String(master.clones_count).length);
				//console.log("partOfNameToModify2: " + partOfNameToModify2 + " - " + name.length + " - " + String(master.clones_count).length );
				//console.log("partOfNameToModify2: " + partOfNameToModify2);
				
				master.clones_count = master.clones_count+1;
			    //clones_count = master.clones_count;
				
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
			    var test_new_name = name.substring(0, name.lastIndexOf('__') + '__'.length); 
				//console.log("test_new_name: " + test_new_name);
			    var test_new_name2 = test_new_name + clones_count; 
			    //console.log("test_new_name2: " + test_new_name2);
				
			    
				var nameEndPart = partOfNameToModify.substring(partOfNameToModify2.length+String(master.clones_count).length);
				//console.log("nameEndPart: " + nameEndPart);
				var newName = parentName + partOfNameToModify2 + clones_count + nameEndPart;
			    //console.log("newName: " + parentName +" - "+partOfNameToModify2+" - "+clones_count +" - "+ nameEndPart);
				//console.log("newName: " + newName);
				var coll=false;
				if (master.relation && !isClone) coll=true;
			    
			    hidden = master.hidden;
			    if (isClone) hidden=false;
			    
				var clone = master.cloneConfig({
					id : newName,
					name : newName,
					clone : isClone,
					clones_count: clones_count,
					template : master,
					collapsible: !coll,
					collapsed: coll,
					hidden:hidden
				});
				
				//console.log("master: " + master.getId());
				//console.log("clone: " + clone.name);
				//console.log(name+" - "+clones_count);
				count=0;
				clone.constructClone(master, count);
				//console.log(name+" - "+isClone);
				if (isClone)
				{
					// [SGI - 28.10.09] Pourquoi ce calcul pour ins�rer le clone?
					// [SGI - 20.01.10] Comment� car je ne comprends pas son utilit� et que j'ai un bug avec ce code
					//console.log(master.getId() + " - " + panel.items.indexOf(master));
			   		//if (panel.items.indexOf(master) == 0)
					//	var idx = panel.items.indexOf(master)+2+i;
					//else
						var idx = panel.items.indexOf(master)+1+i;
						//console.log(clone.getId() + " - " + idx);
			   		panel.insert(idx,clone);
				}
				else
				{
					panel.add(clone);
					/*if (clone.maxOccurs == clone.minOccurs)
						clone.clones(1,panel, true); // Equivalent: clone.clones(1);
					*/
				}

				//var lastClone = listOfClones[listOfClones.length-1];									
						
						// Permettre les nulls pour les champs cach�s
						var hiddenFields= new Array();
						clone.cascade(function(cmp)
						{
							var newItems = clone.items;
							//console.log(newItems);
							// Gestion du cas o� aucune relation enfant n'a �t� publi�e
							newItems_count = 0;
							if (newItems)
								newItems_count = newItems.length;
							for (var i=0; i < newItems_count ; i++)
							{
								var item = newItems.get(i);
								
								if (item.xtype=='fieldset')
								{
									//console.log('Fieldset: ' + item.getId() + ' - ' + item.clone);
									//console.log('Fieldset: ' + item.getId() + ' - ' + item.hidden);
									if (item.clone == false)
									{
										//var f = cmp.items;
										item.cascade(function (field)
										{
											hiddenFields.push(field.getId());
											//console.log('Field: ' + field.getId() + ' - ' + field.allowBlank);
											if (field.allowBlank == false)
											{
												//console.log('Field or fieldset to change: ' + field.getId());
												field.allowBlank = true;
											}
											if (field.regex)
											{
												//console.log('Field or fieldset to change: ' + field.getId());
												field.regex = '';
											}
										})
									}
								}
							}
						});
						//console.log(hiddenFields);

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
	/**
	 * Duplicates a fieldset, attach it to the source fieldset and rename with the given name
	 * @param {String} newName New name to use for the duplicated fieldset
	 */				
	duplicates : function(newName, title, ancestor) { //, isClone, mas) {
    	var panel = this.ownerCt;


    	//console.log("newName: " + newName);
		
		var clone = this.cloneConfig({
			id : newName,
			name : newName,
			title : title,
			originaltitle : title,
			minOccurs:1, 
            maxOccurs:1,
			clone : false,
			clones_count: 1,
			template : null,
			collapsible: true,
			collapsed: false,
			hidden: false,
		    relation: true,
			dynamic: true
		});
		
		//console.log(name+" - "+clones_count);
		count=0;
		
		 
		ancestor.add(clone);
		//ancestor.doLayout();
		//clone.constructClone(this, count);	
			
		return clone;
	},
	manageTitle: function(component)
	{
		if (component.maxOccurs != 1)
		{
			if (component.clone)
			{
				var clones = component.template.clones();
				
				for (var i=0 ; i<clones.length ; i++)
				{
				//console.log(clones[i].getId());
					var position = clones.indexOf(clones[i]);
						clones[i].setTitle(clones[i].originalTitle + " [" + (Number(position)+1) + "]");
				}
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
	constructClone : function(original, count) 
	{
		//console.log("Original: " + original.getId());
		var childs = Ext.getCmp(original.getId()).items;

		if (childs)
			count=childs.length;
		//console.log("Nombre d'enfants: " + count);
		// Clone each child and add it to the clone of the original parent
		for (var i=0; i<count;i++)
		{
			// Get the child
			var child = childs.get(i);
			
			//console.log(child.minOccurs == child.maxOccurs);
			//console.log("Enfant " + i + ": " + child.getId());
			if (!child.clone)
			{
				child.clones(1,this, false);
				
				if (child.minOccurs > 0 && child.xtype=='fieldset')
				{
					child.clones(child.minOccurs+1,this, true);
				}
			}
			// [SGI - 28.10.09] Cas de lowerbound >0 => cloner les clones
			/*else if (child.minOccurs > 0)
			{
				var idx = original.ownerCt.items.indexOf(original)+1+i;
				console.log("template owner: " + child.template.ownerCt.getId());
				console.log("this: " + this.getId());
			   	//child.clones(1, child.template.ownerCt, true);
				child.clones(1, this, true);
			}*/
		}
	},
	constructDuplicataChilds : function(original, count) 
	{
		//console.log("Original: " + original.getId());
		var childs = Ext.getCmp(original.getId()).items;

		if (childs)
			count=childs.length;
		//console.log("Nombre d'enfants: " + count);
		// Clone each child and add it to the clone of the original parent
		for (var i=0; i<count;i++)
		{
			// Get the child
			var child = childs.get(i);
			
			//console.log(child.minOccurs == child.maxOccurs);
			//console.log("Enfant " + i + ": " + child.getId());
			if (!child.clone)
			{
				child.clones(1,this, false);
				
				if (child.minOccurs > 0 && child.xtype=='fieldset')
				{
					child.clones(child.minOccurs+1,this, true);
				}
			}
			// [SGI - 28.10.09] Cas de lowerbound >0 => cloner les clones
			/*else if (child.minOccurs > 0)
			{
				var idx = original.ownerCt.items.indexOf(original)+1+i;
				console.log("template owner: " + child.template.ownerCt.getId());
				console.log("this: " + this.getId());
			   	//child.clones(1, child.template.ownerCt, true);
				child.clones(1, this, true);
			}*/
		}
	},
	manageTooltip: function(component)
	{
		// Tooltips
		//console.log(component.name + " - " + component.xtype + " - " + component.qTip);
		var qt = component.qTip;
		var dismissDelay = component.qTipDelay;
		if(qt){
			/*Ext.QuickTips.register({
				target:  component,
				title: '',
				text: qt,
				enabled: true
			});
			*/
			
			// Positionner l'aide contextuelle sur le titre du fieldset
			var headerText = findHeaderText(component);
			if (headerText)
			{
				Ext.QuickTips.register({
					target:  headerText,
					title: '',
					text: qt,
					dismissDelay: dismissDelay,
					enabled: true
					});
			}
		}
		
		
	},
	onRender : Ext.form.FieldSet.prototype.onRender.createInterceptor(function(ct, position) 
	{ 	
		if ( this.dynamic) 
		{	
			
			this.on("afterrender",this.manageIcons);
			this.on("afterrender",this.manageTitle);
			this.on("afterrender",this.manageTooltip);
			
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