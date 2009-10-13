Ext.override(Ext.form.FieldSet, {
		dynamic : false,		
		
		/**
		 * Clones a fieldset untill the required amount specified is reached
		 * @param {Number} card  Number of clones required. When no card is specified, the current clones will be returned
		 * @return {Array}  required clones of type {Ext.form.FieldSet}  
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
					var idx = panel.items.indexOf(master);
					var clone = master.cloneConfig({
						clone : true,
						template : master									
					});
					panel.insert(idx+1+i,clone);
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
		onRender : Ext.form.FieldSet.prototype.onRender.createInterceptor(function(ct, position) { 			
			if ( this.dynamic) {
				if ( this.clone ) {
					this.tools = [ {id:'minus', handler: function(event, toolEl, fieldset){	
										var panel = fieldset.ownerCt;																
										panel.remove(fieldset,true);
										panel.doLayout();
									}}				
								];					
				} else {
					this.tools = [ {id:'plus', handler: function(event, toolEl, fieldset){	
										var cnt = fieldset.clones().length;
 									    if ( !Ext.isEmpty(fieldset.maxOccurs) ) {
										   if ( fieldset.maxOccurs  <= cnt + 1) {
											   fieldset.fireEvent('maxoccurs',fieldset);
											   return;													   
										   }
									    }	
										var panel = fieldset.ownerCt;	
										fieldset.clones(cnt+1);
										panel.doLayout();																								   
									}}				
								 ];		
				}						 
			}
		})
});