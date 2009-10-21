	function createFieldSet(id, title, border, clone, collapsible, relation, dynamic, master, min, max)
	{	
		//if (title) title = title+" "+min+" - "+max;
		var collapsed = (relation && !clone) ? collapsed=true : collapsed = false;
		var hidden = (max==1 && min==1 && !clone && relation) ? true : false;
		
		// Créer un nouveau fieldset
		var f = new Ext.form.FieldSet(
				{
					xtype: 'fieldset',
					cls: 'easysdi_shop_backend_fieldset', 
					title:title, 
					originalTitle:title,
					id:id, 
					name:id,
					minOccurs:min, 
		            maxOccurs:max,
		            border: border,
					clone: clone,
					hidden: hidden,
			        collapsible: !collapsed,
			        collapsed: collapsed,
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
	
	function createTextArea(id, label, optional, clone, master, min, max, value)
	{
		 var ta = new Ext.form.TextArea({
	            id:id,
	            xtype: 'textarea',
				cls: 'easysdi_shop_backend_textarea', 
	            fieldLabel: label,
	            name: id,
	            allowBlank: optional,
	            value: value,
	            grow: true,
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
				cls: 'easysdi_shop_backend_combobox', 
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
		        emptyText:'',
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
	//console.log(data);
		 var ta = new Ext.ux.form.MultiSelect({
	            id:id,
	            name: id,
	            xtype: 'multiselect',
				cls: 'easysdi_shop_backend_multiselect', 
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
				cls: 'easysdi_shop_backend_textfield', 
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
				cls: 'easysdi_shop_backend_numberfield', 
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
				cls: 'easysdi_shop_backend_datetimefield', 
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
	
	function createDateField(id, label, optional, clone, master, min, max, value)
	{	
		 var df = new Ext.form.DateField({
	            id:id,
	            xtype: 'datefield',
				cls: 'easysdi_shop_backend_datefield', 
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