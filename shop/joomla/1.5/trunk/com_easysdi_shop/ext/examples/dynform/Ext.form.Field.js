Ext.override(Ext.form.TriggerField, {
    alignErrorIcon : function() {
        this.errorIcon.alignTo(this.wrap, 'tl-tr', [2 + this.dicon? this.dicon.getWidth() + 4 : 0, 0]);
    }
});	

Ext.override(Ext.form.Field, {
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
				   var clone = master.cloneConfig({
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
		getIconCt : function(el){
			return  el.findParent('.x-form-element', 5, true) || // use form element wrap if available
				el.findParent('.x-form-field-wrap', 5, true);   // else direct field wrap
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
				this.addListener('onIcon',function(field) {
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
				this.dicon.addListener('click',this.onIcon,this);				
                this.alignIcon();				
                this.on('resize', this.alignIcon, this);
			}
		}
});