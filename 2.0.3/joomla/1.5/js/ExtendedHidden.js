Ext.namespace('Ext.ux');
 
/**
  * Ext.ux.ExtendedHidden Extension Class
  *
  * @author  Stephanie Dondainaz
  * @version 1.0
  *
  * @class Ext.ux.ExtendedHiddenField
  * @extends Ext.form.TextField
  * @constructor
  * @param {Object} config Configuration options
  */
Ext.ux.ExtendedHidden = function(config) {
 
    // call parent constructor
    Ext.ux.ExtendedHidden.superclass.constructor.call(this, config);

} // end of Ext.ux.ExtendedFieldSet constructor
 
// extend
Ext.extend(Ext.ux.ExtendedHidden, Ext.form.Hidden, {
    setValue: function(value) {
        Ext.ux.ExtendedHidden.superclass.setValue.call(this, value);
    },
    dynamic : false,
	
	/**
	 * Clones a field untill the required amount specified is reached
	 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
	 * @return {Array}  required clones of type {Ext.form.Field}  
	 */		
	clones : function(card, ownerCtrl, isClone) {
    	var panel = (ownerCtrl) ? ownerCtrl : this.ownerCt;
    	var isClone = (isClone!=undefined) ? isClone : true;
		//var panel = this.ownerCt;			
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
			    // console.log('Hidden Parent: ' + parentName);
				var name = master.getName();
				var partOfNameToModify = name.substring(parentName.length);
				// console.log('partOfNameToModify: ' + partOfNameToModify);
				var nameRootPart = partOfNameToModify.substring(0, partOfNameToModify.lastIndexOf("__")+2);
				var nameEndPart = partOfNameToModify.substring(partOfNameToModify.lastIndexOf("__")+3);
				
				// console.log(name);
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
				    clone : isClone,
					template : master
			   });
			   // Put the start value for the new field
			   clone.setValue('1');
			   

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
	}
});