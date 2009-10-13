Ext.override(Ext.form.FormPanel, {
    /**
	 * Returns array of component under this panel
	 * @param {String} ns  Required namespace to which the component should be bound
	 * @param {String} xtype Required type of component
	* @param {String} master Optional  identifier whether the component shouldn't be a clone If master is not empty, the component can't be a clone
	 * @return {Array} 
	 */
	findFormComponents : function (ns,xtype,master) {
		if ( Ext.isEmpty(master) ) {
			return this.findBy(function(cmp) {
				return cmp.isXType(xtype) && cmp.nameSpace == ns;
			});	
		} else {
			return this.findBy(function(cmp) {
				return cmp.isXType(xtype) && cmp.nameSpace == ns && Ext.isEmpty(cmp.template);
			});	
		}
	},	
    /**
	 * Adds value to collection In case of single value collection a single value is returned Otherwise an array of values will be returned
	 * @param {Mixed}  coll  Required collection to which the value should be added
	 * @param {String}  v Required value that should be added to the collection
	 * @return Returns mixed elements of collection
	 */
	aggregate : function(coll,v) {
			if ( coll === undefined || coll === null ) {
				return v;
			} else if ( Ext.isArray(coll) ) {
				coll.push(v);
				return coll;
			} else {
				coll = [coll];
				coll.push(v);
				return coll;
			}
	},
    /**
	 * Extract value(s) of all component under this panel that are bound to a named collection.
	 * If xtype is field an object of field names and aggregated values is returned
	 * If xtype is fieldset an array of objects of field names and aggregated values, each object representing a fieldset, is returned
	 * @param {String} ns  Required namespace to which the component should be bound
	 * @param {String} xtype Required type of component
	 * @return Returns object in case xtype is field and returns array in case xtype is fieldset

	 Example of it's usage :
	 
	  var location = Ext.getCmp('panel').extract('location','field');
	  var fsperson = Ext.getCmp('panel').extract('person','fieldset');
	  var fperson = Ext.getCmp('panel').extract('person','field');

	 */
    extract : function(ns,xtype){
		if ( xtype === 'fieldset' ) {
			var obja = [];
			Ext.each(this.findFormComponents(ns,xtype),function(fst) {
				var obj = {};
				var fs = fst.findBy(function(cmp) {
					return cmp.isFormField;
				});
				Ext.each(fs,function(f) {
					obj[f.name]=this.aggregate(obj[f.name],f.getValue());
				},this);
				obja.push(obj);
			},this);
			return obja;			
		} else if ( xtype === 'field' ) {
			var obj = {};
			Ext.each(this.findFormComponents(ns,xtype),function(f) {
				obj[f.name]=this.aggregate(obj[f.name],f.getValue());			
			},this);
			return obj;
		}
    },
    /**
	 * Populate components under this panel that are bound to a named collection.
	 * If xtype is field Populating is based on an object of field names and aggregated values that is used to set the values of the components under this panel
	 * If xtype is fieldset Populating is based on an array of objects of field names and aggregated values, each object representing a fieldset, that is used to set the values of the components under this panel
	 * @param {Mixed} o  Required values used for populating the fields
	 * @param {String} ns  Required namespace to which the component should be bound
	 * @param {String} xtype Required type of component
	 
	 Example of it's usage :
	 
	 person.populate(Ext.decode('{"state":["Netherlands","Delaware"]}'),'location','field');
	 person.populate(Ext.decode('[{"first":["Adriaan","Cornelis"],"last":"Zaanen"},{"first":["Bill"],"last":"Joy"}]'),'person','fieldset');
	 person.populate(Ext.decode('{"birthDate":"03/12/2009"}'),'person','field');	 	 
	 
	 Known restrictions :
		1.   Only one type of dynamic fieldset per namespace allowed Therefore populating fieldsets of different types is not allowed.	 
	 */
	populate : function(o,ns,xtype) {
		var findField = function(multi,name) {
			var fields = [];
			if ( Ext.isArray(multi) ) {				
				// determine field in array of fields based on it's name
				var fs = multi;
				Ext.each(fs,function(field) {
						if(Ext.isEmpty(field.template) && field.isFormField && (field.dataIndex == name || field.id == name || field.name == name)){
							fields.push(field);
						}
				},this);			
			} else {
				// determine field under this component based on it's name
				var cntr = multi;
				fields = cntr.findBy(function(cmp) {			
					if(Ext.isEmpty(cmp.template) && cmp.isFormField && (cmp.dataIndex == name || cmp.id == name || cmp.name == name)){
						return true;					
					}
				});
			}
			if ( fields.length > 0 ) return fields[0];
		};	
		var setValues = function(field,values) {
			if (Ext.isEmpty(values)) values = '';
			if (!Ext.isArray(values)) values = [values];
			var fields = [];
			// acquire required amount of fields			
			if (Ext.isArray(field)) {
				fields = field;
			} else {
				fields = [field];
				if ( field.clones ) {
					fields = fields.concat(field.clones(values.length-1));
				} 			
			}
			// populate fields			
			for ( var i = 0 ; i < values.length ; i ++ ) {
				fields[i].setValue(values[i]);
			}
		};
		if ( xtype === 'fieldset' ) {
			// restrictions :
			//	* Only one dynamic fieldset per namespace allowed
			if ( !Ext.isArray(o) ) return;
			var array = o;
			// acquire required amount of fieldsets
			var fieldsets = this.findFormComponents(ns,xtype,'master');
			if ( fieldsets[0].clones ) {
				if ( array.length == 0 ) {
					fieldsets[0].clones(0)
				} else {
					fieldsets = fieldsets.concat(fieldsets[0].clones(array.length-1));				
				}
			} 
			// acquire fieldset
			for ( var i = 0 ; i < array.length ; i ++ ) {
				// populate fields
				for ( name in array[i]) {
					var field = findField(fieldsets[i],name);
					var values = array[i][name];				
					if (!Ext.isEmpty(field)) {
						setValues(field,values);
					}
				}				
			}
		} else if ( xtype == 'field' ) {
			if ( Ext.isArray(o) ) return;
			var object = o;			
			var fields = this.findFormComponents(ns,xtype,'master');
			if ( fields.length == 0 ) return;						
			// populate fields			
			for ( name in object) {
				var field = findField(fields,name);
				var values = object[name];											
				if (!Ext.isEmpty(field)) {
					setValues(field,values);
				}
			}
		}
	}
});	