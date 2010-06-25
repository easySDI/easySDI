	function createFieldSet(id, title, border, clone, collapsible, relation, dynamic, master, min, max, tip, isLanguageFieldset)
	{	
		//if (title) title = title+" "+min+" - "+max;
		var collapsed = (relation && !clone) ? collapsed=true : collapsed = false;
		var hidden = (max==1 && min==1 && !clone && relation) ? true : false;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		//collapsible = true;
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
					clones_count: clones_count,
					hidden: hidden,
			        collapsible: collapsible,
			        collapsed: collapsed,
				    relation: relation,
					dynamic: dynamic,
					template: master,
		            qTip: tip,
		            isLanguageFieldset: isLanguageFieldset
	        });
		//if (navigator.appName == "Netscape")
		//	console.log(id+" - "+clone+" - "+clones_count);
		return f;
	}
	
	function createTextArea(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, regex, mandatoryMsg, regexMsg)
	{
		optional = !mandatory;
		
		if (master) master.clones_count=master.clones_count+1;
		//if (!clone) optional=true;
		if (clone) optional=true;
		var clones_count = (master) ? master.clones_count : 1;
		
		// Valeur max = n
		if (max == 999) 
		{
			max = Number.MAX_VALUE;
		}
		
		if (maxL == 999 || maxL == 0) 
		{
			maxL = Number.MAX_VALUE;
		}
		
		var ta = new Ext.form.TextArea({
	            id:id,
	            xtype: 'textarea',
				cls: 'easysdi_shop_backend_textarea', 
	            fieldLabel: label,
	            name: id,
	            allowBlank: optional,
	            blankText: mandatoryMsg,
	            regex: eval("/"+regex+"/"),
	            regexText: regexMsg,
	            value: value,
	            defaultVal:defaultVal,
	            grow: true,
	            dynamic:true,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
	            disabled: dis,
	            minOccurs:min,
	            maxOccurs:max,
	            multiline:true,
	            maxLength: maxL,
	            qTip: tip
	        });

		return ta;
	}
	
	function createComboBox(id, label, mandatory, min, max, data, value, defaultVal, dis, tip, mandatoryMsg)
	{
		var store = new Ext.data.ArrayStore({
						    //fields: ['id', 'key', 'translation'],
							fields: ['id', 'key'],
						    data: data
						});
		 
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		optional = !mandatory;
		
		//alert(id + " - " + value);
		//console.log(id + " - " + value);
		
		var c = new Ext.form.ComboBox({
			id:id,
			name:id,
            hiddenName:id + '_hidden',
			//hiddenId:id,
    		cls: 'easysdi_shop_backend_combobox',
    		xtype: 'combo',
            fieldLabel: label,
            allowBlank: optional,
            blankText: mandatoryMsg,
            store: store,
            typeAhead:true,
            dynamic:true,
            minOccurs:min,
            maxOccurs:max,
            editable:false,
            displayField:'key',
            valueField:'id',
            value:value,
            defaultVal:defaultVal,
          	mode: 'local',
          	forceSelection: true,
          	triggerAction: 'all',
          	emptyText:'',
          	disabled: dis,
	        selectOnFocus:true,
            qTip: tip
         });
		 
		 return c;
	}
	
	function createMultiSelector(id, label, mandatory, min, max, data, value, defaultVal, dis, tip, mandatoryMsg)
	{
		//console.log(data);
		//console.log(min + " - " + max);
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		 
		optional = !mandatory;
		
		minselect = 0;
		if (mandatory)
			minselect = 1;
		
		var ta = new Ext.ux.form.MultiSelect({
	            id:id,
	            name: id,
	            xtype: 'multiselect',
				cls: 'easysdi_shop_backend_multiselect', 
	            fieldLabel: label,
	            allowBlank: optional,
	            //minSelections:minselect,
	            blankText: mandatoryMsg,
	            store: data,
	            initValues: value,
	            defaultVal:defaultVal,
	            dynamic:true,
	            editable:false,
	            minOccurs:min,
	            maxOccurs:max,
	            qTip: tip
	        });
		 return ta;
	}
	
	function createChoiceBox(id, label, mandatory, min, max, data, value, defaultVal, dis, tip, mandatoryMsg)
	{
		//console.log(data);
		
		var store = new Ext.data.ArrayStore({
						    //fields: ['id', 'key', 'translation'],
							fields: ['id', 'key', 'guid'],
						    data: data
						});
		
		// Custom rendering Template
		var resultTpl = new Ext.XTemplate(
	        '<tpl for="."><div class="search-item" style="min-height:10px;">',
		        '<h3>{id}</h3>',
	            '{key}',
	        '</div></tpl>'
	    );
	    
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		optional = !mandatory;
		
		//alert(id + " - " + value);
		//console.log(id + " - " + value);
		var c = new Ext.form.ComboBox({
			id:id,
			name:id,
            hiddenName:id + '_hidden',
			//hiddenId:id,
    		cls: 'easysdi_shop_backend_choicebox', 
    		xtype: 'choicetext',
			fieldLabel: label,
            allowBlank: optional,
            blankText: mandatoryMsg,
            store: store,
            typeAhead:true,
            dynamic:true,
            minOccurs:min,
            maxOccurs:max,
            editable:false,
            displayField:'id',
            valueField:'guid',
            value:value,
            defaultVal:defaultVal,
          	mode: 'local',
          	forceSelection: true,
          	triggerAction: 'all',
          	emptyText:'',
          	disabled: dis,
	        selectOnFocus:true,
	        //pageSize:10,
	        tpl: resultTpl,
	        itemSelector: 'div.search-item',
	        qTip: tip
         });
		
		 return c;
	}
	
	function createCheckboxGroup(id, label, mandatory, min, max, data, dis, tip, mandatoryMsg)
	{
		optional = !mandatory;
		
		var g = new Ext.form.CheckboxGroup({
	            id:id,
	            name: id,
	            xtype: 'checkboxgroup',
	            cls: 'easysdi_shop_backend_checkboxgroup', 
				fieldLabel: label,
				allowBlank: optional,
	            blankText: mandatoryMsg,
	            dynamic:true,
				disabled: dis,
	            minOccurs:min,
	            maxOccurs:max,
	            qTip: tip,
	            items: data
	        });
		
		 return g;
	}
	
	function createRadioGroup(id, label, mandatory, min, max, data, dis, tip)
	{
		optional = !mandatory;
		
		var g = new Ext.form.RadioGroup({
	            id:id,
	            name: id,
	            xtype: 'radiogroup',
				cls: 'easysdi_shop_backend_radiogroup', 
				fieldLabel: label,
				allowBlank: optional,
	            blankText: mandatoryMsg,
	            dynamic:true,
				disabled: dis,
	            minOccurs:min,
	            maxOccurs:max,
	            inputType: 'radio',
	            qTip: tip,
	            items: data
	        });
		
		 return g;
	}
	
	function createTextField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, regex, mandatoryMsg, regexMsg)
	{
		optional = !mandatory;
		
		//if (!clone) optional=true;
		if (clone) optional=true;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		var dynamic = !dis;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		var tf = new Ext.form.TextField({
	            id:id,
	            xtype: 'textfield',
				cls: 'easysdi_shop_backend_textfield', 
	            fieldLabel: label,
	            name: id,
	            allowBlank: optional,
	            blankText: mandatoryMsg,
	            regex: eval("/"+regex+"/"),
	            regexText: regexMsg,
	            value: value,
	            defaultVal:defaultVal,
	            dynamic:dynamic,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
	            disabled: dis,
	            maxLength: maxL,
	            qTip: tip
	        });
		 return tf;
	}
	
	function createNumberField(id, label, mandatory, clone, master, min, max, value, defaultVal, allowdec, dec, dis, maxL, tip, regex, mandatoryMsg, regexMsg)
	{
		optional = !mandatory;
		
		//if (!clone) optional=true;
		if (clone) optional=true;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;

		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		var tf = new Ext.form.NumberField({
	            id:id,
	            xtype: 'numberfield',
				cls: 'easysdi_shop_backend_numberfield', 
	            fieldLabel: label,
	            name: id,
	            decimalPrecision: dec,
	            allowBlank: optional,
	            blankText: mandatoryMsg,
	            regex: eval("/"+regex+"/"),
	            regexText: regexMsg,
	            value: value,
	            defaultVal:defaultVal,
	            dynamic:true,
	            allowDecimals: true,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
	            disabled: dis,
	            maxLength: maxL,
	            qTip: tip
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
	
	function createDateTimeField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, tip, regex, mandatoryMsg, regexMsg)
	{	
		optional = !mandatory;
		
		//if (!clone) optional=true;
		if (clone) optional=true;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		var df = new Ext.form.DateField({
	            id:id,
	            xtype: 'datefield',
				cls: 'easysdi_shop_backend_datetimefield', 
	            fieldLabel: label,
	            name: id,
	            dynamic: true,
	            allowBlank: optional,
	            blankText: mandatoryMsg,
	            regex: eval("/"+regex+"/"),
	            regexText: regexMsg,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
	            value:value,
	            defaultVal:defaultVal,
	            disabled: dis,
	            format: 'd.m.Y',
	            maxLength: maxL,
	            qTip: tip
	        });
		 
		 return df;
	}
	
	function createDateField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, regex, mandatoryMsg, regexMsg)
	{	
		optional = !mandatory;
		
		//if (!clone) optional=true;
		if (clone) optional=true;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		var df = new Ext.form.DateField({
	            id:id,
	            xtype: 'datefield',
				cls: 'easysdi_shop_backend_datefield', 
	            fieldLabel: label,
	            name: id,
	            dynamic: true,
	            allowBlank: optional,
	            blankText: mandatoryMsg,
	            regex: eval("/"+regex+"/"),
	            regexText: regexMsg,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
	            value:value,
	            defaultVal:defaultVal,
	            disabled: dis,
	            format: 'd.m.Y',
	            maxLength: maxL,
	            qTip: tip
	        });
		 
		 return df;
	}
	
	function createComboBox_Boundaries(id, label, mandatory, min, max, data, value, dis, tip, mandatoryMsg, boundaries, paths)
	{
		//console.log(boundaries);
		//console.log(paths);
		var store = new Ext.data.ArrayStore({
						    //fields: ['id', 'key', 'translation'],
							fields: ['id', 'key'],
						    data: data
						});
		 
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		optional = !mandatory;
		
		//alert(id + " - " + value);
		//console.log(id + " - " + value);
		
		var c = new Ext.form.ComboBox({
			id:id,
			name:id,
            cls: 'easysdi_shop_backend_combobox', 
            fieldLabel: label,
            allowBlank: optional,
            blankText: mandatoryMsg,
            store: store,
            typeAhead:true,
            dynamic:true,
            minOccurs:min,
            maxOccurs:max,
            editable:false,
            displayField:'key',
            valueField:'key',
            value:value,
          	mode: 'local',
          	forceSelection: true,
          	triggerAction: 'all',
          	emptyText:'',
          	disabled: dis,
	        selectOnFocus:true,
            qTip: tip,
            boundaries:boundaries,
            paths:paths,
            listeners: {
							'select': function (){ 
								var ownerCt = this.ownerCt.id;
								
								if (this.value == "")
								{
									Ext.getCmp(ownerCt + this.paths[0].northbound).setValue('');
									Ext.getCmp(ownerCt + this.paths[0].southbound).setValue('');
									Ext.getCmp(ownerCt + this.paths[0].westbound).setValue('');
									Ext.getCmp(ownerCt + this.paths[0].eastbound).setValue('');
								}
								else
								{
									Ext.getCmp(ownerCt + this.paths[0].northbound).setValue(this.boundaries[this.value].northbound);
									Ext.getCmp(ownerCt + this.paths[0].southbound).setValue(this.boundaries[this.value].southbound);
									Ext.getCmp(ownerCt + this.paths[0].westbound).setValue(this.boundaries[this.value].westbound);
									Ext.getCmp(ownerCt + this.paths[0].eastbound).setValue(this.boundaries[this.value].eastbound);
								}
							}
						}
         });
		 
		 return c;
	}
	
	function createSearchField(id, objecttype_id, label, mandatory, clone, master, min, max, value, dis, maxL, tip, regex, mandatoryMsg, regexMsg)
	{
		optional = !mandatory;
		
		//if (!clone) optional=true;
		if (clone) optional=true;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		var dynamic = !dis;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		if (regex != "")
			regex = new RegExp(regex,"g")
		
		var ds = new Ext.data.Store({
			proxy: new Ext.data.HttpProxy({
	            url: 'index.php?option=com_easysdi_catalog&task=getContact'
	        }),
	        reader: new Ext.data.JsonReader({
	            root: 'contacts',
	            totalProperty: 'total',
	            id: 'guid'
	        }, [
	            {name: 'name', mapping: 'name'},
	            {name: 'guid', mapping: 'guid'}
	        ]),

	        baseParams: {limit:20, forumId: 4, objecttype_id: objecttype_id}
	    });
	   
	    var resultTpl = new Ext.XTemplate(
            '<tpl for="."><div class="search-item">',
                '{name}',
            '</div></tpl>'
        );
        
	    var name ="";
	    if (value[0])
	    	name = value[0].name;
	    var guid ="";
	    if (value[0])
	    	guid = value[0].guid;
	    
	    var sf = new Ext.form.ComboBox({
			id:id,
			name:id,
            hiddenName:id + '_hidden',
            cls: 'easysdi_shop_backend_combobox',
            xtype: 'combo',
            fieldLabel: label,
            allowBlank: optional,
            blankText: mandatoryMsg,
            store: ds,
	        displayField:'name',
	        valueField:'guid',
	        value:name,
	        hiddenValue:guid,
          	typeAhead: false,
	        loadingText: 'Searching...',
	        width: 570,
	        pageSize:10,
	        hideTrigger:true,
	        tpl: resultTpl,
	        itemSelector: 'div.search-item',
	        dynamic:true,
	        minOccurs:min,
            maxOccurs:max,
            emptyText:'',
          	disabled: dis,
          	clone: clone,
			clones_count: clones_count,
            template: master,
            minChars: 4,
            forceSelection: true,
            selectOnFocus: true
	    });
	    //Ext.util.Observable.capture(Ext.getCmp(id), console.info);
		
		return sf;
	}