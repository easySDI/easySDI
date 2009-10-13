<script>
	/*!
	 * Ext JS Library 3.0+
	 * Copyright(c) 2006-2009 Ext JS, LLC
	 * licensing@extjs.com
	 * http://www.extjs.com/license
	 */
	
	/**  
	 * * Fires when the document is ready (before onload and before images are loaded).  Shorthand of {@link Ext.EventManager#onDocumentReady}.  
	 * * @param {Function} fn The method the event invokes  
	 * * @param {Object} scope An object that becomes the scope of the handler  
	 * * @param {boolean} options (optional) An object containing standard {@link #addListener} options  
	 * * @member Ext  
	 * * @method onReady */

	Ext.onReady(function(){
	
	    Ext.QuickTips.init();
	});

	function createFieldSet(id, title, border, clone, collapsible, relation, dynamic, master, min, max)
	{	
		if (title) title = title+" "+min+" - "+max;
		
		// Créer un nouveau fieldset
		var f = new Ext.form.FieldSet(
				{
					xtype: 'fieldset', 
					title:title, 
					id:id, 
					name:id,  
					minOccurs:min, 
		            maxOccurs:max,
		            border: border,
					clone: clone,
					collapsible: collapsible,
					relation: relation,
					dynamic: dynamic,
					template: master,
					listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		return f;
	}
	
	function createTextArea(id, label, optional, clone, master, min, max, value, width, height)
	{
		 var ta = new Ext.form.TextArea({
	            id:id,
	            xtype: 'textarea',
	            fieldLabel: label,
	            name: id,
	            allowBlank: optional,
	            value: value,
	            grow: true,
	            width:width,
	            height:height,
	            dynamic:true,
	            clone: clone,
	            template: master,
	            minOccurs:min,
	            maxOccurs:max,
	            multiline:true,
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 
		 return ta;
	}
	
	function createComboBox(id, label, optional, min, max, data, value)
	{
		var store = new Ext.data.ArrayStore({
						    fields: ['id', 'key', 'translation'],
						    data: data
						});
		 
		 var c = new Ext.form.ComboBox({
	            id:id,
	            name: id,
	            fieldLabel: label,
	            allowBlank: optional,
	            store: store,
	            typeAhead:true,
	            dynamic:true,
	            minOccurs:min,
	            maxOccurs:max,
	            editable:false,
	            valueField:'id',
	            value:value,
	            displayField:'key',
		        mode: 'local',
		        forceSelection: true,
		        triggerAction: 'all',
		        emptyText:'Select a value...',
		        selectOnFocus:true,
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 
		 return c;
	}
	
	function createMultiSelector(id, label, optional, min, max, data, value)
	{
		 var ta = new Ext.ux.form.MultiSelect({
	            id:id,
	            name: id,
	            xtype: 'multiselect',
	            fieldLabel: label,
	            allowBlank: optional,
	            store: data,
	            initValues: value,
	            dynamic:true,
	            editable:false,
	            minOccurs:min,
	            maxOccurs:max,
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 return ta;
	}
	
	function createTextField(id, label, optional, clone, master, min, max, value, length, dis)
	{
		var dynamic = !dis;
		 var tf = new Ext.form.TextField({
	            id:id,
	            xtype: 'textfield',
	            fieldLabel: label,
	            name: id,
	            allowBlank: optional,
	            value: value,
	            dynamic:dynamic,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
	            template: master,
	            disabled: dis,
	            minLength:length,
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 return tf;
	}
	
	function createNumberField(id, label, optional, clone, master, min, max, value, def, allowdec, dec)
	{

		 var tf = new Ext.form.NumberField({
	            id:id,
	            xtype: 'numberfield',
	            fieldLabel: label,
	            name: id,
	            decimalPrecision: dec,
	            allowBlank: optional,
	            value: value,
	            dynamic:true,
	            allowDecimals: true,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
	            template: master,
	            emptyText:def,
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 
		 return tf;
	}
	
	function createHidden(id, name, value)
	{
		 var h = new Ext.ux.ExtendedHidden({
	            id:id,
	            xtype: 'hidden',
	            name: name,
	            value: value
	        });
		 
		 return h;
	}
	
	function createDateTimeField(id, label, optional, clone, master, min, max, value)
	{	
		 var df = new Ext.form.DateField({
	            id:id,
	            xtype: 'datefield',
	            fieldLabel: label,
	            name: id,
	            dynamic: true,
	            allowBlank: optional,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
	            template: master,
	            value:value,
	            format: 'Y-m-dTH:i:s',
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 
		 return df;
	}
	
	function createDateField(id, label, optional, clone, master, min, max, value)
	{	
		 var df = new Ext.form.DateField({
	            id:id,
	            xtype: 'datefield',
	            fieldLabel: label,
	            name: id,
	            dynamic: true,
	            allowBlank: optional,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
	            template: master,
	            value:value,
	            format: 'd.m.Y',
	            listeners : { 
	            				'minoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite minimale atteinte', 'Le nombre d\'elements minimum de ce type a ete atteint.\nVous ne pouvez pas en supprimer d\'autres.'); 
       								}
       							},
       							'maxoccurs' : 
       							{
       								fn: function(field) 
       								{ 
       									Ext.Msg.alert('Limite maximale atteinte', 'Le nombre d\'elements maximum de ce type a ete atteint.\nVous ne pouvez pas en ajouter d\'autres.'); 
       								}
       							}
	            			}
	        });
		 
		 return df;
	}
</script>