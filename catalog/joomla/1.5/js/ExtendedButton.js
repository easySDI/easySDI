Ext.QuickTips.init();

Ext.override(Ext.Button, {
    alignErrorIcon : function() {
        this.errorIcon.alignTo(this.wrap, 'tl-tr', [2 + this.dicon? this.dicon.getWidth() + 4 : 0, 0]);
    }
});

Ext.override(Ext.Button, {
    dynamic : false,
	
	/**
	 * Clones a button
	 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
	 * @return {Array}  required clones of type {Ext.Button}  
	 */		
	clones : function(card, ownerCtrl, isClone) 
	{			
    	var panel = (ownerCtrl) ? ownerCtrl : this.ownerCt;
    	var isClone = (isClone!=undefined) ? isClone : true;
		var master = this;
		//console.log(panel);
		//console.log(master);
		//console.log(this);
		//console.log(this.extendedTemplate);
		//console.log(isClone);
		
		if ( this.extendedTemplate && Ext.getCmp(this.extendedTemplate.getId()) && panel.findById(this.extendedTemplate.getId())) 
		{
			master = this.extendedTemplate;
		}
		
		var cmps  = panel.findBy(
			function(cmp) 
			{
				if ( cmp.extendedTemplate ) 
				{
					return cmp.extendedTemplate == this.extendedTemplate;
				}
			},{extendedTemplate:master});

		if ( Ext.isEmpty(card)) 
		{
			return cmps;
		}										
		
		// sanitize amount of clones untill cardinality is reached
		if ( !Ext.isEmpty(card) ) {
			//
			// add clones untill card is reached			
			for ( var i = cmps.length ; i < card ; i ++ ) {

				// Le nom à construire est celui utilisé pour créer les noms de tous les éléments d'un attribut du stéréotype Thesaurus GEMET
				// Il faut donc bien retirer le _BUTTON à la fin d'une entrée pour pouvoir gérer les numéros
				var parentName = panel.getId();
				var name = master.getId();
				var truncatedName = name.substring(0, name.length-String('_BUTTON').length);
				//console.log(truncatedName);
				//console.log("parentName: "+ parentName);
				//console.log("name: "+ name);
				
				var partOfNameToModify = name.substring(parentName.length);
				
				// Retirer la partie _BUTTON du nom à modifier
				partOfNameToModify = partOfNameToModify.substring(0, partOfNameToModify.length-String('_BUTTON').length);
				//console.log("partOfNameToModify: "+ partOfNameToModify);
				
			    clones_count = 1;
				
				var newName = parentName + partOfNameToModify;
				//console.log("newName: "+ newName);
				
				// L'extension Thesaurus GEMET à dupliquer
				var thesMaster = Ext.ComponentMgr.get(truncatedName + '_PANEL_THESAURUS');
				//console.log(thesMaster);
				
				var winthge;
				
				var clone = master.cloneConfig({
					id : newName + '_BUTTON',
					name : newName + '_BUTTON',
					hiddenName: newName + '_BUTTON' + '_hidden',
					clone : isClone,
					clones_count: clones_count,
					extendedTemplate : master,
					handler: function()
	                {
	                	// Créer une iframe pour demander à l'utilisateur le type d'import
						if (!winthge)
							winthge = new Ext.Window({
							                id: newName + '_WIN',
									  title: thesMaster.win_title, //Ext.ComponentMgr.get( newName + '_WIN').title,
							                width:500,
							                height:500,
							                closeAction:'hide',
							                layout:'fit', 
										    border:true, 
										    closable:true, 
										    renderTo:Ext.getBody(), 
										    frame:true,
											listeners: {
														'show': function (animateTarget, cb, scope)
																{
																	this.items.get(0).emptyAll();
																	this.items.get(0).getTopConcepts(this.items.get(0).CONCEPT);
																}
														},
										    items:[new ThesaurusReader({
												  id:newName + '_PANEL_THESAURUS',
												  lang: thesMaster.lang,
											      outputLangs: thesMaster.outputLangs, 
											      separator: thesMaster.separator,
											      appPath: thesMaster.appPath,
											      returnPath: false,
											      returnInspire: true,
											      thesaurusUrl : thesaurusConfig,
											      width: 300, 
											      height:400,
											      win_title: thesMaster.win_title,
											      layout: 'fit',
											      targetField: newName,
											      proxy: thesMaster.proxy,
											      handler: function(result){
											      				var target = Ext.ComponentMgr.get(this.targetField);
															    var s = '';
												      		    
												      		    var reliableRecord = result.terms[this.lang];
												      		    
												      		    // S'assurer que le mot-clé n'est pas déjà sélectionné
												      		    if (!target.usedRecords.containsKey(reliableRecord))
																{
																	// Sauvegarde dans le champs SuperBoxSelect des mots-clés dans toutes les langues de EasySDI
																    for(l in result.terms) 
																    {
																    	s += l+': '+result.terms[l]+';';
																    }
																    target.addItem({keyword:result.terms[this.lang], value: s});
																}
																else
																{
																	//Ext.MessageBox.alert('".JText::_('CATALOG_EDITMETADATA_THESAURUSSELECT_MSG_SUCCESS_TITLE')."', 
																	//					 '".JText::_('CATALOG_EDITMETADATA_THESAURUSSELECT_MSG_SUCCESS_TEXT')."');
																
																}
																
															    //winthge.hide();
															}
											  })]
							            });
						else
						{
							// Vider les contenus
						}	
							
						winthge.show();
		        	}																	   
				});
				//console.log(clone);
				
				panel.add(clone);
				
				panel.doLayout();

				
			}			

			// remove clones untill cardinality is reached
			for ( var i = cmps.length ; i > card ; i -- ) {
					var field = cmps[i-1];
					var item = Ext.get(field.el.findParent('.x-btn'));
					item.remove();
					panel.remove(field);			
			}
			cmps  = panel.findBy(function(cmp) {
						 if ( cmp.extendedTemplate ) {
							return cmp.extendedTemplate == this.extendedTemplate;
						 }
						},{extendedTemplate:master});
		}
		return cmps;								
	}
});