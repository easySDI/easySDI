<script>
// Create user extensions namespace (Ext.ux)
Ext.namespace('Ext.ux');
 
/**
  * Ext.ux.ExtendedTextArea Extension Class
  *
  * @author  Stephanie Dondainaz
  * @version 1.0
  *
  * @class Ext.ux.ExtendedTextArea
  * @extends Ext.form.TextArea
  * @constructor
  * @param {Object} config Configuration options
  */
Ext.ux.ExtendedTextArea = function(config) {
 
    // call parent constructor
    Ext.ux.ExtendedTextArea.superclass.constructor.call(this, config);

} // end of Ext.ux.ExtendedTextArea constructor
 
// extend
Ext.extend(Ext.ux.ExtendedTextArea, Ext.form.TextArea, {
    setValue: function(value) {
        Ext.ux.ExtendedTextArea.superclass.setValue.call(this, value);
    },
    dynamic : false,		
	
	/**
	 * Clones a field untill the required amount specified is reached
	 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
	 * @return {Array}  required clones of type {Ext.form.Field}  
	 */		
	clones : function(card) {
		var panel = this.ownerCt;			
		var master = this;
		if ( this.template ) {
			master = this.template;
		} 
		var cmps  = panel.findBy(function(cmp) {
						 if ( cmp.template ) {
							return cmp.template == this.template;
						 }
						},{template:master});	
						
		if ( Ext.isEmpty(card)) {
			return cmps;
		}			
		//
		// sanitize amount of clones untill cardinality is reached							
		if ( !Ext.isEmpty(card) ) {
			//
			// add clones untill card is reached
			for ( var i = cmps.length ; i < card ; i ++ ) {
				var parentName = panel.getId();
				// console.log('TextField Parent: ' + parentName);
				var name = master.getName();
				var partOfNameToModify = name.substring(parentName.length);
				// console.log('partOfNameToModify: ' + partOfNameToModify);
				var nameRootPart = partOfNameToModify.substring(0, partOfNameToModify.lastIndexOf("__")+2);
				var nameEndPart = partOfNameToModify.substring(partOfNameToModify.lastIndexOf("__")+3);
				
				// console.log(name + '_index');
				var indexComponent = Ext.ComponentMgr.get(parentName + partOfNameToModify + '_index');
				var newVal = 1;
				if (indexComponent!=undefined)
				{	var newVal = Number(indexComponent.value) + 1;
			    	indexComponent.setValue(newVal);
				}
			    // console.log(nameRootPart + newVal + nameEndPart);
				
			    var newName = parentName + nameRootPart + '1' + nameEndPart;
			    if (Ext.ComponentMgr.get(newName))
			    {
			    	newName = parentName + nameRootPart + newVal + nameEndPart;
			    }
			    
			    var clone = master.cloneConfig({
			    	id: newName,
				    name : newName,
				    clone : true,
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
			    clone.addListener('onMinusIcon',function(field) {
			    	var item = Ext.get(field.el.findParent('.x-form-item'));
					
			    	// Traitement pour le premier élément de la liste, le master
			    	if (field.clone != true)
			    	{
				    	var fieldset = field.ownerCt;
						
						// Get the first clone af the master
						var listOfClones = field.clones();
						var firstClone = listOfClones[0];
						
						// Replace the values of the master by the values of the first clone
						var newMaster = field.cloneConfig();
						
						// Set the new master to each clone
						for (i=0; i < listOfClones.length; i++)
						{
							listOfClones[i].template = newMaster;
						}

						panel.insert(field.getPosition(),newMaster);
						panel.remove(firstClone,true);
			    	}
					item.remove();
					panel.remove(field, true);
					panel.doLayout();
					}											 
				);
			   // Put an empty value for the new field
			   clone.setValue('');
			   
			   var idx = panel.items.indexOf(master);
			   panel.insert(idx+1+i,clone);												   				
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
	},
	alignPlusIcon : function(){
		if ( this.isXType('combo') ||  this.isXType('datefield') ) {
			this.dicon.alignTo(this.el, 'tl-tr', [17, 3]);
		} else {
			this.dicon.alignTo(this.el, 'tl-tr', [2, 3]);
		}		
	},
	alignIcon : function(){
		if ( this.isXType('combo') ||  this.isXType('datefield') ) {
			this.dicon.alignTo(this.el, 'tl-tr', [17, 3]);
		} else {
			this.dicon.alignTo(this.el, 'tl-tr', [2, 3]);
		}		
	},
	alignErrorIcon : function() {
		this.errorIcon.alignTo(this.el, 'tl-tr', [2 + this.dicon? this.dicon.getWidth() + 4 : 0, 0]);
	},		
	afterRender : Ext.form.Field.prototype.afterRender.createSequence(function() { 			
		if ( this.dynamic && Ext.isEmpty(this.clone)) {
			this.addIcon({cls:'x-tool x-tool-plus',clsOnOver:'x-tool-plus-over'});
			this.addIcon({cls:'x-tool x-tool-minus',clsOnOver:'x-tool-minus-over'});
			this.addListener('onPlusIcon',function(field) {
										   var cnt = this.clones().length;
										   if ( !Ext.isEmpty(this.maxOccurs) ) {
											   if ( this.maxOccurs  <= cnt + 1) {
												   field.fireEvent('maxoccurs',this);
												   return;													   
											   }
										   }			
										   var panel = this.ownerCt;	
										   this.clones(cnt+1);
										   panel.doLayout();																								   
										}											 
							);	
			this.addListener('onMinusIcon',function(field) {
		    	var item = Ext.get(field.el.findParent('.x-form-item'));
				
		    	// Traitement pour le premier élément de la liste, le master
		    	if (field.clone != true)
		    	{
			    	var fieldset = field.ownerCt;
					
					// Get the first clone af the master
					var listOfClones = field.clones();
					var firstClone = listOfClones[0];
					
					// Replace the values of the master by the values of the first clone
					var newMaster = field.cloneConfig();
					newMaster.setValue(firstClone.getValue());
					
					// Set the new master to each clone
					for (i=0; i < listOfClones.length; i++)
					{
						listOfClones[i].template = newMaster;
					}
					alert(fieldset.getComponent(field));
					fieldset.insert(field.getItemId() ,newMaster);
					fieldset.remove(firstClone,true);
		    	}
				item.remove();
				fieldset.remove(field, true);
				fieldset.doLayout();
				}											 
			);
		} else {
			this.addIcon(this.iconCfg);
		}
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
			}
			else if (iconCfg.clsOnOver == 'x-tool-minus-over')
			{
				this.dicon.addListener('click',this.onMinusIcon,this);
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
}); // end of extend
// end of file	
</script>