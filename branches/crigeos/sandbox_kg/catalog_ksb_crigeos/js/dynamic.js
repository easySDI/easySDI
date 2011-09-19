	function createFieldSet(id, title, border, clone, collapsible, relation, dynamic, master, min, max, tip, dismissDelay, isLanguageFieldset)
	{	
		//if (title) title = title+" "+min+" - "+max;
		debugger;
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
		            qTipDelay: dismissDelay,
		            isLanguageFieldset: isLanguageFieldset
	        });
		//if (navigator.appName == "Netscape")
		//	console.log(id+" - "+clone+" - "+clones_count);
		
		return f;
	}
	
	function createTextArea(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
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
	            itemId:id,
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
	            qTip: tip,
	            qTipDelay: dismissDelay
	        });

		return ta;
	}
	
	function createComboBox(id, label, mandatory, min, max, data, value, defaultVal, dis, tip, dismissDelay, mandatoryMsg)
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
            qTip: tip,
            qTipDelay: dismissDelay
         });
		 
		 return c;
	}
	
	function createMultiSelector(id, label, mandatory, min, max, data, value, defaultVal, dis, tip, dismissDelay, mandatoryMsg)
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
		     	cls: 'ux-mselect-fieldset',
		     	fieldLabel: label,
	            allowBlank: optional,
	            //minSelections:minselect,
	            blankText: mandatoryMsg,
	            store: data,
	            initValues: value,
	            value: value,
	            defaultVal:defaultVal,
	            dynamic:true,
	            editable:false,
	            minOccurs:min,
	            maxOccurs:max,
		     	qTip: tip,
		     	qTipDelay: dismissDelay,
	            view: new Ext.ListView({
	                multiSelect: true,
	                store: this.store,
	                columns: [{ header: 'Value', width: 1, dataIndex: this.displayField }],
	                hideHeaders: true
	            })
	        });
		 return ta;
	}
	
	function createChoiceBox(id, label, mandatory, min, max, data, value, defaultVal, dis, tip, dismissDelay, mandatoryMsg, master, clone)
	{
		//console.log(data);
		optional = !mandatory;
		if (master) 
			master.clones_count=master.clones_count+1;
		if (clone) optional=true;
		var clones_count = (master) ? master.clones_count : 1;
		
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
	    //console.log(store.getAt(1).get('id'));
	    //console.log(store.getAt(1).get('key'));

		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		displayField='id';
		//console.log(store.getAt(1).get('id') == '');
		if (store.getAt(1).get('id') == '')
			displayField='key';
		
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
            displayField:displayField,
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
	        qTip: tip,
            qTipDelay: dismissDelay,
            clone: clone,
			clones_count: clones_count,
            template: master
         });
		
		 return c;
	}
	
	function createCheckboxGroup(id, label, mandatory, min, max, data, dis, tip, dismissDelay, mandatoryMsg)
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
	            qTipDelay: dismissDelay,
	            items: data
	        });
		
		 return g;
	}
	
	function createRadioGroup(id, label, mandatory, min, max, data, dis, tip, dismissDelay)
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
	            qTipDelay: dismissDelay,
	            items: data
	        });
		
		 return g;
	}
	
	function createTextField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
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
	            qTip: tip,
	            qTipDelay: dismissDelay
	        });
		 return tf;
	}
	
	function createDisplayField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
	{
		optional = !mandatory;
		//if (!clone) optional=true;
		if (clone) optional=true;
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		var dynamic = !dis;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
		
		var df = new Ext.form.DisplayField({
	            id:id,
	            xtype: 'displayfield',
				cls: 'easysdi_shop_backend_textfield', 
	            fieldLabel: label,
	            name: id,
	            //allowBlank: optional,
	            //blankText: mandatoryMsg,
	            //regex: eval("/"+regex+"/"),
	            //regexText: regexMsg,
	            value: value,
	            defaultVal:defaultVal,
	            dynamic:dynamic,
	            minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
	            //disabled: dis,
	            //maxLength: maxL,
	            qTip: tip,
	            qTipDelay: dismissDelay
	        });
		 return df;
	}
	
	function createNumberField(id, label, mandatory, clone, master, min, max, value, defaultVal, allowdec, dec, dis, maxL, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
	{
		debugger;
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
	            qTip: tip,
	            decimalSeparator:'.',
	            qTipDelay: dismissDelay
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
	
	function createDateTimeField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
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
	            invalidText: regexMsg,
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
	            qTip: tip,
	            qTipDelay: dismissDelay
	        });
		 
		 return df;
	}
	
	function createDateField(id, label, mandatory, clone, master, min, max, value, defaultVal, dis, maxL, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
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
	            invalidText: regexMsg,
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
	            qTip: tip,
	            qTipDelay: dismissDelay
	        });
		 
		 return df;
	}
	
	function createComboBox_Boundaries(id, label, mandatory, min, max, data, value, dis, tip, dismissDelay, mandatoryMsg, boundaries, paths)
	{
		//console.log(boundaries);
		//console.log(paths);
		debugger;
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
            qTipDelay: dismissDelay,
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
	
	function createSearchField(id, objecttype_id, label, mandatory, clone, master, min, max, value, dis, maxL, tip, dismissDelay, regex, mandatoryMsg, regexMsg)
	{
		//optional = !mandatory;
		optional=false;
		var size = 20;
		
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

	        baseParams: {limit:size, forumId: 4, objecttype_id: objecttype_id}
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
	        pageSize:size,
	        mode:'remote',
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
            selectOnFocus: true,
            qTip: tip,
            qTipDelay: dismissDelay
	    });
	    //Ext.util.Observable.capture(Ext.getCmp(id), console.info);
		
		return sf;
	}
	
	function createSuperBoxSelect(id, label, value, clone, master, min, max, mandatoryMsg)
	{
		var optional=false;
		if (clone) optional=true; // Pas sûr que ce soit juste, il faut peut-être toujours l'avoir à false?
		if (master) master.clones_count=master.clones_count+1;
		var clones_count = (master) ? master.clones_count : 1;
		
		// Valeur max = n
		if (max == 999) max = Number.MAX_VALUE;
				
		var store = new Ext.data.SimpleStore({
			storeId:id + '_store',
            fields: ['keyword', 'value'],
            sortInfo: {field: 'keyword', direction: 'ASC'}
        });

		var sbs = new Ext.ux.form.SuperBoxSelect({
				id:id,
				name:id,
	            hiddenName:id + '_hidden[]',
	            xtype:'superboxselect',
		        fieldLabel: label,
	            allowBlank: optional,
	            blankText: mandatoryMsg,
	            emptyText:'',
	          	minChars: 1,
		        editable: false,
	            forceSelection: false,
	            selectOnFocus: true,
	            typeAhead: false,
		        hideTrigger:true,
		        resizable: true,
		        anchor:'100%',
		        store: store,
		        mode: 'local',
		        allowAddNewData:true,
		        removeValuesFromStore:false,
		        displayField: 'keyword',
		        displayFieldTpl: '{keyword}',
		        valueField:'value',
				//value:value,
			 	// Champs spécifiques au clonage
		        dynamic:true,
		        minOccurs:min,
	            maxOccurs:max,
	            clone: clone,
				clones_count: clones_count,
	            template: master,
		     	// Ajout des valeurs existantes
	            listeners: {'afterrender': function (){if (value!= "") this.addItems(value);}}
		    });
		//Ext.util.Observable.capture(Ext.getCmp(id), console.info);
		// Ajout des valeurs existantes
		//sbs.addItems(value);
		return sbs;
	}
	

	 function createBBox(id, fieldsetId) {


		OpenLayers.ProxyHost="/proxy/?url=";

		var mymap = new OpenLayers.Map( id);
	
		//var mm = new OpenLayers.Layer.MultiMap( "MultiMap", {minZoomLevel: 1});
		
		//var wms = new OpenLayers.Layer.WMS( "OpenLayers WMS", "http://labs.metacarta.com/wms/vmap0", {layers: 'basic'} );
		if(defaultBBoxConfig!=""){
			
			//for(var i= 0; i<defaultBBoxConfig.layers.length;i++)
			mymap.addLayers(defaultBBoxConfig.layers);
		}
			// wms = new OpenLayers.Layer.WMS(defaultBBoxConfig.name,defaultBBoxConfig.url,{layers :defaultBBoxConfig.layers });
		else
			console.log("defaultBBoxConfig param is missing");
		
	
		var ov_options = {};
		
			ov_options.maxExtent = defaultBBoxConfig.defaultExtent;
			var ovControl = new OpenLayers.Control.OverviewMap( {
				mapOptions :ov_options,
				size : new OpenLayers.Size(100, 100)
			});
			// This forces the overview to never pan or zoom, since it
			// is always
			// suitable.
			ovControl.isSuitableOverview = function() {
				return true;
			};
			mymap.addControl(ovControl);
		

		
		mymap.addControl(new OpenLayers.Control.PanZoomBar());		

		mymap.navCtrl = new OpenLayers.Control.NavigationHistory();
		
		  
          // parent control must be added to the map
		  mymap.addControl(mymap.navCtrl);
			// Zoom in
		  mymap.zoomInBoxCtrl = new OpenLayers.Control.ZoomBox();
			mymap.addControl( mymap.zoomInBoxCtrl);
			// Zoom out
			mymap.zoomOutBoxCtrl = new OpenLayers.Control.ZoomBox( {
				out : true
			});
			mymap.addControl(mymap.zoomOutBoxCtrl);

		
		
		mymap.updateManuallyTriggered = true;
		mymap.zoomToExtent(new OpenLayers.Bounds(defaultBBoxConfig.defaultExtent.left,defaultBBoxConfig.defaultExtent.bottom,defaultBBoxConfig.defaultExtent.right,defaultBBoxConfig.defaultExtent.top));
		
		
			
		mymap.fieldsetId = fieldsetId;
		return mymap ;

	}
	 

	
	
	 function addBBoxToFieldSet(fieldsetId){
		
		console.log(fieldsetId +"registering started");	
		
		Ext.getCmp(fieldsetId).add( new Ext.Panel( {
			id : fieldsetId+"_BBox",
			width : 500,
			height : 500,
			xtype: 'panel',
			frame: false,
			style :{position:'relative',
			top:'-100px',
			left:'500px'
			}
			
		}));		
		Ext.getCmp(fieldsetId).doLayout();
		console.log("idBBox" +fieldsetId+"_BBox");	
		map = createBBox(Ext.getCmp(fieldsetId+"_BBox").body.id, fieldsetId);
		var updateManuallyTriggered = false ;
		
		var  updateMapExtent = function(){
			map.updateManuallyTriggered = true;
			var extent = new Array();

			
			Ext.each(Ext.getCmp(fieldsetId).items.items, function(item, index) {
				if(item.id.indexOf("east")>=0){
					
					extent["east"]= item.getValue();
					
				}else if(item.id.indexOf("west")>=0){
					
					extent["west"]= item.getValue();		
	
				}else if(item.id.indexOf("south")>=0){
					
					extent["south"]= item.getValue();		
	
				}else if(item.id.indexOf("north")>=0){
					
					extent["north"]= item.getValue();		
	
				}else{}
			});
			
			//left, bottom, right, top 
			var currentBounds = new OpenLayers.Bounds(extent["west"],extent["south"],extent["east"],extent["north"]);
			
			map.zoomToExtent(currentBounds);
			
		};
		
		Ext.getCmp(fieldsetId).items.items[0].addListener("change",  updateMapExtent, null);
		Ext.getCmp(fieldsetId).items.items[1].addListener("change",  updateMapExtent, null);
		Ext.getCmp(fieldsetId).items.items[2].addListener("change",  updateMapExtent, null);
		Ext.getCmp(fieldsetId).items.items[3].addListener("change",  updateMapExtent, null);
		
		map.events.register('moveend', null, function(ev){
			
			if(!map.updateManuallyTriggered){
			
				console.log(ev);
					var extent = new Array();
					var currentExtent = ev.object.layers[0].getExtent();
					extent["south"] = currentExtent.bottom ;
					extent["west"] = currentExtent.left ;
					extent["north"] = currentExtent.top ;
					extent["east"] = currentExtent.right ;
					
					console.log(Ext.getCmp(ev.object.fieldsetId).items);
			
					Ext.each(Ext.getCmp(ev.object.fieldsetId).items.items, function(item, index) {
						if(item.id.indexOf("east")>=0){
							
							item.setValue(extent["east"]);
							
						}else if(item.id.indexOf("west")>=0){
							
							item.setValue(extent["west"]);				
			
						}else if(item.id.indexOf("south")>=0){
							
							item.setValue(extent["south"]);				
			
						}else if(item.id.indexOf("north")>=0){
							
							item.setValue(extent["north"]);				
			
						}else{}
					});
		}
		else
				map.updateManuallyTriggered = false;
		});
		
		
		 map.updateCtrlBtns = function() {
			if (map.navButton.pressed) {
				map.navCtrl.activate();
				// this.getFeatureInfoCtrl.activate();
			} else {
				map.navCtrl.deactivate();
				// this.getFeatureInfoCtrl.deactivate();
			}
		
			if (map.zoomInBoxButton.pressed) {
				map.zoomInBoxCtrl.activate();
			} else {
				map.zoomInBoxCtrl.deactivate();
			}
			if (map.zoomOutBoxButton.pressed) {
				map.zoomOutBoxCtrl.activate();
			} else {
				map.zoomOutBoxCtrl.deactivate();
			}
				
		}
		
		 
		  getToolbar = function() {

			// General tool bar items
		
			map.previousButton = new Ext.Toolbar.Button( {
				iconCls : 'previousBtn',

				handler : function() {
					map.navCtrl.previousTrigger();
				},
				scope : map
			});
			
			
		
			map.nextButton = new Ext.Toolbar.Button( {
				iconCls : 'nextBtn',

				handler : function() {
					map.navCtrl.nextTrigger();
				},
				scope : map
			});
			
			
			
			map.navButton = new Ext.Toolbar.Button( {
				iconCls : 'navBtn',

				enableToggle : true,
				toggleGroup : 'mapCtrl',
				allowDepress : false,
				handler : map.updateCtrlBtns,
				scope : map,
				pressed : true
			});
			
		
		
			map.zoomInBoxButton = new Ext.Toolbar.Button( {
				iconCls : 'zoomInBoxBtn',

				enableToggle : true,
				toggleGroup : 'mapCtrl',
				allowDepress : false,
				handler : map.updateCtrlBtns,
				scope : map
			});
			
			
			map.zoomOutBoxButton = new Ext.Toolbar.Button( {
				iconCls : 'zoomOutBoxBtn',

				enableToggle : true,
				toggleGroup : 'mapCtrl',
				allowDepress : false,
				handler : map.updateCtrlBtns,
				scope : map
			});
			
			
			
			
			return new Ext.Toolbar( {
				id : fieldsetId+"_BBoxPanel",
				autoHeight : true,
				width : 500,
				height : 30,				
				frame: false,
				style :{position:'relative',			
				left:'500px',
				top:'-100px',
				},
				items : [ map.previousButton, map.nextButton, {
					xtype : 'tbseparator'
				}, map.zoomInBoxButton, map.zoomOutBoxButton,map.navButton
				]
			});
			
	
		}
		 
		Ext.getCmp(fieldsetId).add(getToolbar());	
			
		Ext.getCmp(fieldsetId).doLayout();
		 
	
	
		}
		

	
