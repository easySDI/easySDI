
Ext.namespace('Ext.ux.form');

Ext.ux.form.ExtendedItemSelector = function(config) {
	 
    // call parent constructor
	Ext.ux.form.ExtendedItemSelector.superclass.constructor.call(this, config);

} 

Ext.extend(Ext.ux.form.ExtendItemSelector,Ext.ux.form.ItemSelector, {

    dynamic : false,
    
    
    /**
	 * Clones an ItemSelector untill the required amount specified is reached
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
		
		// sanitize amount of clones untill cardinality is reached
		if ( !Ext.isEmpty(card) ) {
			//
			// add clones untill card is reached			
			for ( var i = cmps.length ; i < card ; i ++ ) {

				var parentName = panel.getId();
				var name = master.getId();
				console.log("parentName - " + parentName);
				console.log("name - " + name);
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
				
				var clone = master.cloneConfig({
					id : newName,
					name : newName,
					hiddenName: newName + '_hidden',
					clone : isClone,
					clones_count: clones_count,
					template : master,
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
				
				
				//console.log(newName + " - " + clone.xtype);
				
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

	
	listOfClonesManagement : function (field, cnt, item, fieldset){
		// Traitement pour le premier élément de la liste, le master
    	if (!field.clone)
    	{
			// Get the first clone of the master
			var listOfClones = field.clones();
			var firstClone = listOfClones[0];			
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
	}
	
	
});