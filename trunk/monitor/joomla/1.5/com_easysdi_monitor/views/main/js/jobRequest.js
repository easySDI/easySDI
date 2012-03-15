	/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community 
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */

Ext.namespace("EasySDI_Mon");

Ext.onReady(function(){
  
  var fieldParams = null;
  var testRun = false;
  var showPreview = false;
  var queryNameToUse = "";
	var proxy = new Ext.data.HttpProxy({
		url: '?'
	});

	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	}); 

	var store = new Ext.data.JsonStore({
		//Will be used when writing JSON to the server as the root element
		//if not set: {"undefined":{"url":"blah","id":"plop"}}
		root: 'data',
		id: 'name',
		autoSave: false,
		//url:'jobs/vd-wms-fonds2/queries',
		restful:true,
		proxy: proxy,
		writer: writer,
		fields:[{name:'serviceMethod'},{name: 'status'},{name: 'name'},{name: 'statusCode'},{name: 'params'},{name: 'soapUrl'},{name: 'queryMethod'},{name:'queryServiceType'},
		        {name:'queryValidationSettings',mapping:'queryValidationSettings'},{name:'id',mapping:'queryValidationSettings.id'},{name: 'queryID',mapping: 'queryValidationSettings.queryID'},{name:'useSizeValidation',mapping:'queryValidationSettings.useSizeValidation'},
		        {name:'normSize',mapping:'queryValidationSettings.normSize'},{name:'normSizeTolerance',mapping:'queryValidationSettings.normSizeTolerance'},
		        {name:'useTimeValidation',mapping:'queryValidationSettings.useTimeValidation'}, {name:'normTime',mapping:'queryValidationSettings.normTime'},
		        {name:'useXpathValidation',mapping:'queryValidationSettings.useXpathValidation'},
		        {name:'xpathExpression',mapping:'queryValidationSettings.xpathExpression'}, {name:'expectedXpathOutput',mapping:'queryValidationSettings.expectedXpathOutput'},
		        {name:'queryValidationResult', mapping: 'queryValidationResult'},{name:'sizeValidationResult', mapping: 'queryValidationResult.sizeValidationResult'},
		        {name:'responseSize', mapping: 'queryValidationResult.responseSize'},
		        {name:'timeValidationResult', mapping: 'queryValidationResult.timeValidationResult'},{name:'deliveryTime', mapping: 'queryValidationResult.deliveryTime'},
		        {name:'xpathValidationResult', mapping: 'queryValidationResult.xpathValidationResult'},{name:'xpathValidationOutput', mapping: 'queryValidationResult.xpathValidationOutput'}        
		]
	});

	var cm = new Ext.grid.ColumnModel([{
		header:EasySDI_Mon.lang.getLocal('grid header name'),
		dataIndex:"name",
		width:50,
		sortable: true
	},{
		header:EasySDI_Mon.lang.getLocal('grid header method'),
		dataIndex:"serviceMethod",
		width:100,
		displayField:'method'
	},{
		header:EasySDI_Mon.lang.getLocal('grid header params'),
		dataIndex:"params",
		renderer: paramsRenderer,
		width:320
	}
	]);

	function paramsRenderer(value) {
	
		if(value == null)
			return "";
		if(!value.constructor)
			return "";
		if (value.constructor.toString().indexOf("Array") == -1)
			return value;		
		else{
			var str = '';
			for (var i=0; i<value.length; i++){
				if(value[i].value.indexOf(":Envelope>")!=-1){
					str = Ext.util.Format.htmlEncode(value[i].value);
					break; // we do not care about any other params in case we are dealing with a soap envelope
				}if(value[i].value.toLowerCase().indexOf("csw:getrecords>")!=-1){
					str = Ext.util.Format.htmlEncode(value[i].value);
					break; // we do not care about any other params in case we are dealing with a csw:getrecords
				}
				else
					str += value[i].name+"="+value[i].value;
				if(i<value.length-1)
					str += "&";
			}
			return str;
		}
	}

	
	var reqOptionsHandler = function(){

		return{	
			doLoadTypeOptions : function(method, type){
				if(type== "all"){
						return EasySDI_Mon.ServiceMethodStore[type+method];						
				}	
				else{
					return EasySDI_Mon.ServiceMethodStore[type];
					
				}	
			},
		    enableSOAP :function (combo, fieldToToggle ){
		    	
		    	if(Ext.getCmp(fieldToToggle)){
			    	Ext.getCmp(fieldToToggle).el.up('.x-form-item').setDisplayed(false);
			    	Ext.getCmp(fieldToToggle).allowBlank = true;
		    	}
		    	
				if(!combo)
					return null;
				if(!combo.value)
					return null;
			
				if(combo.value.toLowerCase().indexOf("soap")!=-1){
					if(combo.value.toLowerCase().indexOf("1.1")!=-1){
						Ext.getCmp(fieldToToggle).el.up('.x-form-item').setDisplayed(true);
						Ext.getCmp(fieldToToggle).allowBlank = false;
					}
				}
				
				var value = combo.getValue().toLowerCase();
				if(Ext.getCmp('sTComboxID'))
				{
					if(value && (value.indexOf("soap") != -1 ||  value.indexOf("http get") != -1 || value.indexOf("http post") != -1 || Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase() != 'all'))
					{	
						Ext.getCmp('sTComboxID').el.up('.x-form-item').setDisplayed(false);
					}else
					{
						Ext.getCmp('sTComboxID').el.up('.x-form-item').setDisplayed(true);
					}
				}else
				{
					if(value && (value.indexOf("soap") != -1 ||  value.indexOf("http get") != -1 || value.indexOf("http post") != -1 || Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase() != 'all'))
					{
						Ext.getCmp('sTComboxIDEdit').el.up('.x-form-item').setDisplayed(false);
					}else
					{
						Ext.getCmp('sTComboxIDEdit').el.up('.x-form-item').setDisplayed(true);
					}			
				}	
			}
		}
	}();
	
	var _reqGrid = new Ext.grid.GridPanel({
		id:'ReqGrid',
		loadMask:true,
		region:'center',
		frame:true,
		border:true,
		autoHeight:true,
		tbar: [{
			//iconCls: 'icon-user-add',
			ref: '../addBtn',
			text: EasySDI_Mon.lang.getLocal('grid action add'),
			disabled: true,
			handler: onAdd
		},'-',{
			//iconCls: 'icon-user-add',
			ref: '../editBtn',
			text: EasySDI_Mon.lang.getLocal('grid action edit'),
			disabled: true,
			handler: onEdit
		},'-',{
			//iconCls: 'icon-user-delete',
			ref: '../removeBtn',
			text: EasySDI_Mon.lang.getLocal('grid action rem'),
			disabled: true,
			handler: onDelete
		}],
		//el:"productGrid",
		//width:700,
		//height:500,
		//title:"Liste des jobs",
		//loadMask:new Ext.LoadMask(Ext.getBody(), {msg:EasySDI_Mon.lang.getLocal('message wait')}),
		store:store,
		cm:cm,
		sm: new Ext.grid.RowSelectionModel({
			singleSelect: true,
			listeners: {
			rowselect: function(sm, row, rec) {
			Ext.getCmp("jobAdvForm").getForm().loadRecord(rec);
		}
		}
		})
	});
	
	_reqGrid.store.on('load', function(store, records, options) {
		if(showPreview)
		{
			_reqGrid.getSelectionModel().selectRow(_reqGrid.getStore().find("name",queryNameToUse,0,true,true));
			queryNameToUse = "";
			onEdit();
		}
	}, _reqGrid);
	
	function enableNormSave()
	{
		if(Ext.getCmp('reqTextID').getValue() != "" && ((Ext.getCmp('sMComboxID') && Ext.getCmp('sMComboxID').getValue() != "")
				|| (Ext.getCmp('reqServiceMethodComboEdit') && Ext.getCmp('reqServiceMethodComboEdit').getValue() != "" ) ))
		{
					Ext.getCmp('saveNormBtnID').enable();
					if(Ext.getCmp('runTestNormBtnID'))
					{
						Ext.getCmp('runTestNormBtnID').enable();
					}	
					if(Ext.getCmp('runTestPreviewBtnID'))
					{
						Ext.getCmp('runTestPreviewBtnID').enable();
					}
					if(Ext.getCmp('runsaveTestBtnID'))
					{
						Ext.getCmp('runsaveTestBtnID').enable();
					}
		}else
		{
					Ext.getCmp('saveNormBtnID').disable();
					if(Ext.getCmp('runTestNormBtnID'))
					{
						Ext.getCmp('runTestNormBtnID').disable();
					}
					if(Ext.getCmp('runTestPreviewBtnID'))
					{
						Ext.getCmp('runTestPreviewBtnID').disable();
					}
					if(Ext.getCmp('runsaveTestBtnID'))
					{
						Ext.getCmp('runsaveTestBtnID').disable();
					}
		}
	}
	
	function changeMethods(param,id)
	{
		var sM = Ext.getCmp(id);
		if(sM)
		{
			var store = reqOptionsHandler.doLoadTypeOptions(param.toLowerCase(), Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase());
			if(store)
			{
				sM.bindStore(new Ext.data.SimpleStore({
					fields : ['name'],
					data   : store
				}));
				sM.clearValue();
			}
		}
	}
	
	/**
	 * onAdd
	 */
	function onAdd(btn, ev) {
		var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		if (!rec) {
			return false;
		}
		
		options = reqOptionsHandler.doLoadTypeOptions(Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('httpMethod').toLowerCase(), Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase());
		
		var disableQueryAdv = Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase() != 'all';
		
		var reqP = new Ext.FormPanel({
					id: 'newReqPanel',
					labelWidth: 90,
					monitorValid:true,
					bodyStyle:'padding:5px 5px 0',
					frame:true,
					height: 370,
					defaults: {width: 290},
					defaultType: 'textfield',
					items: [{
						id: 'reqTextID',
						fieldLabel: EasySDI_Mon.lang.getLocal('grid header name'),
						xtype: 'textfield',
						name: 'name',
						allowBlank:false,
						vtype: 'reqname',
						listeners: {
							'invalid': enableNormSave,
							'valid': enableNormSave		
						}
					},{
						id: 'sMComboxID',
						xtype:          'combo',
						mode:           'local',
						triggerAction:  'all',
						allowBlank:false,
						forceSelection: true,
						fieldLabel:      EasySDI_Mon.lang.getLocal('grid header method'),
						name:           'serviceMethod',
						displayField:   'name',
						valueField:     'name',
						store:          new Ext.data.SimpleStore({
							fields : ['name'],
							data : options
						}),
						listeners: {
							'change': enableNormSave
						}
					},					
					{
						id: 'reqSoapAction',
						fieldLabel: EasySDI_Mon.lang.getLocal('soap action'),
						xtype: 'textfield',
						name: 'soapUrl'
					},
					{
						id: 'hMComboxID',
						xtype:          'combo',
		        		mode:           'local',
		        		value:          Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('httpMethod'),
		        		triggerAction:  'all',
		        		forceSelection: true,
		        		editable:       false,
		        		fieldLabel:     EasySDI_Mon.lang.getLocal('add_edit request method'),
		        		name:           'queryMethod',
		        		displayField:   'name',
		        		valueField:     'name',
		        		store:          new Ext.data.SimpleStore({
		        			fields : ['name'],
		        			data   : EasySDI_Mon.HttpMethodStore
		        		}),
						listeners: {
							'change': enableNormSave
						}
					},
					{
						id: 'sTComboxID',
						xtype:          'combo',
		        		mode:           'local',
		        		value:          EasySDI_Mon.OgcServiceStoreAll[0],
		        		triggerAction:  'all',
		        		forceSelection: true,
		        		editable:       false,
		        		fieldLabel:     EasySDI_Mon.lang.getLocal('grid header type'),
		        		name:           'queryServiceType',
		        		displayField:   'name',
		        		valueField:     'name',
		        		store:          new Ext.data.SimpleStore({
		        			fields : ['name'],
		        			data : EasySDI_Mon.OgcServiceStoreAll
		        		})
					}
					,{
						fieldLabel: EasySDI_Mon.lang.getLocal('grid header params'),
						name: 'params',
						height:200,
						allowBlank:true,
						xtype: 'textarea'
						
					}],
					buttons: [
		            {
		            		formBind:true,
		             		text: EasySDI_Mon.lang.getLocal('jobRequestTabTestBtn'),
		             		handler: function(){
		             			saveNewRequest(true,rec);
		             		}
					}, {
						formBind:true,
						text: EasySDI_Mon.lang.getLocal('grid action ok'),
						handler: function(){
							saveNewRequest(false,rec);	
					}
					},{
						text: EasySDI_Mon.lang.getLocal('grid action cancel'),
						handler: function(){
							win.close();
						}
					}]
		});

		var reqNorm = new Ext.FormPanel({
					id: 'newNormPanel',
					monitorValid:true,
					frame:true,
					labelWidth: 150,
					autoWidth:true,
					height: 370,
					region:'center',
					items: [
					{
					layout:'column',
						items:[
						{	
							layout: 'form',
							items: [{
								xtype: 'checkbox',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request size validation'),
								name: 'useSizeValidation',
								trueText: 'true',
								checked: rec.get('useSizeValidation') ? true :false,
								falseText: 'false',
								handler: function(com){
										if(com.checked)
										{
											Ext.getCmp('normSize').enable();
											Ext.getCmp('normSizeTolerance').enable();
										}else
										{
											Ext.getCmp('normSize').disable();
											Ext.getCmp('normSizeTolerance').disable();
										}
								},
								columnWidth: 1.0
							},
							{
								id: 'normSize',
								xtype: 'numberfield',
								minValue: 0,
								disabled: rec.get('useSizeValidation') ? false :true,
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request norm size'),
								name: 'normSize',
								width: 80,
								columnWidth: 1.0
							},
							{
								id: 'normSizeTolerance',
								xtype:          'combo',
								mode:           'local',
								triggerAction:  'all',
								editable: false,
								disabled: rec.get('useSizeValidation') ? false :true,
								allowBlank:false,
								forceSelection: true,
								fieldLabel:     EasySDI_Mon.lang.getLocal('norm request size tolerance'),
								name:           'normSizeTolerance',
								displayField:   'value',
								valueField:     'normSizeTolerance',
								store:          new Ext.data.SimpleStore({
									fields : ['normSizeTolerance','value'],
									data : EasySDI_Mon.ToleranceStore
								}),
								columnWidth: 1.0
							},
							{
								xtype: 'checkbox',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request time validation'),
								name: 'useTimeValidation',
								trueText: 'true',
								checked: rec.get('useTimeValidation') ? true: false,
								falseText: 'false',
								handler: function(com){
									if(com.checked)
									{
										Ext.getCmp('normTime').enable();
									}else
									{
										Ext.getCmp('normTime').disable();
									}
								},
								columnWidth: 1.0
							},
							{
								id:'normTime',
								xtype: 'numberfield',
								minValue: 0,
								disabled: rec.get('useTimeValidation') ? false: true,
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request norm time'),
								name: 'normTime',
								width: 80,
								columnWidth: 1.0
							},
							{
								xtype: 'checkbox',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request xpath validation'),
								name: 'useXpathValidation',	
								handler: function(com){
									if(com.checked)
									{
										Ext.getCmp('xpathExpression').enable();
										Ext.getCmp('expectedXpathOutput').enable();
									}else
									{
										Ext.getCmp('xpathExpression').disable();
										Ext.getCmp('expectedXpathOutput').disable();
									}
								},
								trueText: 'true',
								falseText: 'false',
								columnWidth: 1.0
							},
							{
								id: 'expectedXpathOutput',
								xtype: 'textarea',
								height:60,
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request norm xpath result'),
								name: 'expectedXpathOutput',
								allowBlank: true,
								disabled: rec.get('useXpathValidation') ? false: true,
								width: 250,
								columnWidth: 1.0,
								value: ''
							},
							{
								id: 'xpathExpression',
								xtype: 'textarea',
								height:60,
								name: 'xpathExpression',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request xpath'),
								allowBlank:true,
								disabled: rec.get('useXpathValidation') ? false: true,
								width: 250,
								columnWidth: 1.0,
								value: ''
							},
							{
								html: '<a href="http://www.w3schools.com/xpath/xpath_syntax.asp" target="_blank">'+EasySDI_Mon.lang.getLocal('norm request help xpathsyntax')+'</a>'
							}
							],
							buttons: [
					      	{
					      		formBind:true,
					      		id:'runTestNormBtnID',
								text: EasySDI_Mon.lang.getLocal('jobRequestTabTestBtn'),
								handler: function(){
									saveNewRequest(true,rec);
								}
							},         
							{
								formBind:true,
								id:'saveNormBtnID',
								text: EasySDI_Mon.lang.getLocal('grid action ok'),
								handler: function(){
									saveNewRequest(false,rec);
							}
							},{
								text: EasySDI_Mon.lang.getLocal('grid action cancel'),
								handler: function(){
									win.close();
								}
							}]
						}
						]}
						]
				});
		
		var previewNorm = new Ext.FormPanel({
			id: 'newPreviewPanel',
			monitorValid:true,
			frame:true,
			labelWidth: 50,
			autoWidth:true,
			height: 370,
			region:'center',
			items: [
				{
					id:'regNamePreview',
					xtype: 'label',
					text: '',
					fieldLabel: 'Request',
					height: 80
				},
				{
				layout:'column',
				items:[		
							{
								columnWidth:.2,
								layout: 'form',
								items:[
								{
									html: 'Size(Bytes)',
									height: 30
								},
								{
									html: 'Time(ms)',
									height: 30
								},
								{
									html: 'Xpath',
									height: 30
								}
								]
							},
							{
								columnWidth:.4,
								layout: 'form',
								items: [
								{
									id: 'normSizePreview',
									xtype: 'textfield',
									anchor:'95%',
									disabled: true,
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request norm'),
									name: 'normSizePreview'
								},
								{
									id: 'normTimePreview',
									xtype: 'textfield',
									anchor:'95%',
									disabled: true,
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request norm'),
									name: 'normTimePreview'
								},
								{
									id: 'xpathNormPreview',
									xtype: 'textarea',
									height:100,
									name: 'xpathNormPreview',
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request norm'),
									allowBlank:true,
									disabled: true,
									anchor:'95%',
									value: ''
								},
								{
									id: 'responseLinkPreview',
									html: '',
									anchor:'95%'
								}
								]
							},
							{
								columnWidth:.4,
								layout: 'form',
								items:[
							
								{
									id: 'resultSizePreview',
									xtype: 'textfield',
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request result'),
									name: 'resultSizePreview',
									anchor:'95%'
								},
								{
									id: 'resultTimePreview',
									xtype: 'textfield',
									anchor:'95%',
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request result'),
									name: 'resultTimePreview'
								},
								{
									id: 'xpathResultPreview',
									xtype: 'textarea',
									height:100,
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request result'),
									name: 'xpathResultPreview',
									allowBlank: true,
									anchor:'95%',
									value: ''
								},
								{
									id: 'responseImagePreview',
									anchor:'95%',
									height: 142,
									html: ''
								}
								]
							}
							]}
							],
							buttons: [
							{
								id:'runTestPreviewBtnID',
								text: EasySDI_Mon.lang.getLocal('jobRequestTabTestBtn'),
								handler: function(){
									saveNewRequest(true,rec);
								}
							},         
							{
								id:'runsaveTestBtnID',
								text: EasySDI_Mon.lang.getLocal('grid action ok'),
								handler: function(){
									saveNewRequest(false,rec);
							}
							},{
								text: EasySDI_Mon.lang.getLocal('grid action cancel'),
								handler: function(){
									win.close();
								}
							}]
					
			
		});
		
		requestParmPanel = {
			title: 'Request',
			xtype: 'panel',
			height: 370,
			border:false,
			items: [reqP]
		};
		
		requestNormPanel = {
			title: 'Norm values',
			xtype: 'panel',
			height: 370,
			border:false,
			items: [reqNorm]
		};
		
		requestPreviewPanel = {
				title: 'Preview',
				xtype: 'panel',
				height: 370,
				border:false,
				items: [previewNorm]
		};
		
		var win = new  Ext.Window({
			id: 'winQuery',
			title:EasySDI_Mon.lang.getLocal('new request'),
			width:450,
			height: 450,
			autoScroll:true,
			modal:true,
			resizable:false,
			items: [
			new Ext.TabPanel({
			id: 'card-tabs-panel2',
			activeTab: 0,
				items:[
					requestParmPanel,
					requestNormPanel,
					requestPreviewPanel
				]})
			]
		});
		win.show();
		if(disableQueryAdv)
		{
			Ext.getCmp("hMComboxID").el.up('.x-form-item').setDisplayed(false);
			Ext.getCmp("sTComboxID").el.up('.x-form-item').setDisplayed(false);
		}
		Ext.getCmp("reqSoapAction").el.up('.x-form-item').setDisplayed(false);
		Ext.getCmp("sMComboxID").on('select', function(){reqOptionsHandler.enableSOAP(Ext.getCmp("sMComboxID"), "reqSoapAction")} );
		
		Ext.getCmp('hMComboxID').on('select', function(cmb, rec){
			if(rec.data && rec.data.name)
			{
				changeMethods(rec.data.name,'sMComboxID');
			}
		});
	}

	/**
	 * onEdit
	 */
	function onEdit() {
		var rec = _reqGrid.getSelectionModel().getSelected();
		if (!rec) {
			return false;
		}

		var params = rec.get('params');
		var strParams = '';
		strParams = Ext.util.Format.htmlDecode(paramsRenderer(params));
		
		options = reqOptionsHandler.doLoadTypeOptions(Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('httpMethod').toLowerCase(), Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase());
		
		var disableQueryAdv = Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase() != 'all';
		
		var reqP = new Ext.FormPanel({
				id: 'editReqPanel',
				labelWidth: 90,
				height: 370,
				monitorValid:true,
				bodyStyle:'padding:5px 5px 0',
				frame:true,
				defaults: {width: 290},
				defaultType: 'textfield',
				items: [{
					id: 'reqTextID',
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header name'),
					xtype: 'textfield',
					value: rec.get('name'),
					name: 'name',
					allowBlank:false,
					disabled:true
				
				},{
					id : 'reqServiceMethodComboEdit',
					xtype:          'combo',
					mode:           'local',
					value:          rec.get('serviceMethod'),
					triggerAction:  'all',
					allowBlank:false,
					forceSelection: true,
					editable:       false,
					fieldLabel:      EasySDI_Mon.lang.getLocal('grid header method'),
					name:           'serviceMethod',
					displayField:   'name',
					valueField:     'name',
					store:          new Ext.data.SimpleStore({
						fields : ['name'],
						data :options
					}),
					listeners: {
						'change': enableNormSave
					}
				},					
				{
					id: 'reqSoapActionEdit',
					fieldLabel: EasySDI_Mon.lang.getLocal('soap action'),
					value:   rec.get('soapUrl'),
					xtype: 'textfield',
					name: 'soapUrl'
					
				},
				{
					id: 'hMComboxIDEdit',
					xtype:          'combo',
	        		mode:           'local',
	        		value:          rec.get('queryMethod'),
	        		triggerAction:  'all',
	        		forceSelection: true,
	        		editable:       false,
	        		fieldLabel:     EasySDI_Mon.lang.getLocal('add_edit request method'),
	        		name:           'queryMethod',
	        		displayField:   'name',
	        		valueField:     'name',
	        		store:          new Ext.data.SimpleStore({
	        			fields : ['name'],
	        			data   : EasySDI_Mon.HttpMethodStore
	        		}),
	        		listeners: {
						'change': enableNormSave
					}
				},
				{
					id: 'sTComboxIDEdit',
					xtype:          'combo',
	        		mode:           'local',
	        		value:          rec.get('queryServiceType'),
	        		triggerAction:  'all',
	        		forceSelection: true,
	        		editable:       false,
	        		fieldLabel:     EasySDI_Mon.lang.getLocal('grid header type'),
	        		name:           'queryServiceType',
	        		displayField:   'name',
	        		valueField:     'name',
	        		store:          new Ext.data.SimpleStore({
	        			fields : ['name'],
	        			data : EasySDI_Mon.OgcServiceStoreAll
	        		})
				},
				{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header params'),
					value: strParams,
					name: 'params',
					height:200,
					allowBlank:true,
					xtype: 'textarea'
				}],
				buttons: [
		             {
		            	formBind:true,
	             		text: EasySDI_Mon.lang.getLocal('jobRequestTabTestBtn'),
	             		handler: function(){
	             			updateRequest(true,rec);
	             		}
					},{
					formBind:true,
					text: EasySDI_Mon.lang.getLocal('grid action ok'),
					handler: function(){
						updateRequest(false,rec);
				}
				},{
					text: EasySDI_Mon.lang.getLocal('grid action cancel'),
					handler: function(){
					win.close();
				}
				}]
			});

		var reqNorm = new Ext.FormPanel({
					id: 'editNormPanel',
					monitorValid:true,
					frame:true,
					labelWidth: 150,
					autoWidth:true,
					height: 370,
					region:'center',
					items: [
					{
					layout:'column',
						items:[
						{	
							layout: 'form',
							items: [{
								xtype: 'checkbox',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request size validation'),
								name: 'useSizeValidation',
								trueText: 'true',
								falseText: 'false',
								checked: rec.get('useSizeValidation') ? true :false,
								handler: function(com){
										if(com.checked)
										{
											Ext.getCmp('normSize').enable();
											Ext.getCmp('normSizeTolerance').enable();
										}else
										{
											Ext.getCmp('normSize').disable();
											Ext.getCmp('normSizeTolerance').disable();
										}
								},
								columnWidth: 1.0
							},
							{
								id: 'normSize',
								xtype: 'numberfield',
								minValue: 0,
								disabled: rec.get('useSizeValidation') ? false :true,
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request norm size'),
								name: 'normSize',
								width: 80,
								value: rec.get('normSize'),
								columnWidth: 1.0
							},
							{
								id: 'normSizeTolerance',
								xtype:          'combo',
								mode:           'local',
								triggerAction:  'all',
								editable: false,
								disabled: rec.get('useSizeValidation') ? false :true,
								allowBlank:false,
								value: rec.get('normSizeTolerance') ? rec.get('normSizeTolerance'):'5',
								forceSelection: true,
								fieldLabel:     EasySDI_Mon.lang.getLocal('norm request size tolerance'),
								name:           'normSizeTolerance',
								displayField:   'value',
								valueField:     'normSizeTolerance',
								store:          new Ext.data.SimpleStore({
									fields : ['normSizeTolerance','value'],
									data : EasySDI_Mon.ToleranceStore
								}),
								columnWidth: 1.0
							},
							{
								xtype: 'checkbox',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request time validation'),
								name: 'useTimeValidation',
								trueText: 'true',
								checked: rec.get('useTimeValidation') ? true: false,
								falseText: 'false',
								handler: function(com){
									if(com.checked)
									{
										Ext.getCmp('normTime').enable();
									}else
									{
										Ext.getCmp('normTime').disable();
									}
								},
								columnWidth: 1.0
							},
							{
								id:'normTime',
								xtype: 'numberfield',
								minValue: 0,
								disabled: rec.get('useTimeValidation') ? false: true,
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request norm time'),
								name: 'normTime',
								width: 80,
								value: rec.get('normTime'),
								columnWidth: 1.0
							},
							{
								xtype: 'checkbox',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request xpath validation'),
								name: 'useXpathValidation',	
								checked: rec.get('useXpathValidation') ? true: false,
								handler: function(com){
									if(com.checked)
									{
										Ext.getCmp('xpathExpression').enable();
										Ext.getCmp('expectedXpathOutput').enable();
									}else
									{
										Ext.getCmp('xpathExpression').disable();
										Ext.getCmp('expectedXpathOutput').disable();
									}
								},
								trueText: 'true',
								falseText: 'false',
								columnWidth: 1.0
							},
							{
								id: 'expectedXpathOutput',
								xtype: 'textarea',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request norm xpath result'),
								name: 'expectedXpathOutput',
								allowBlank: true,
								value: rec.get('expectedXpathOutput') ? rec.get('expectedXpathOutput') : '',
								disabled: rec.get('useXpathValidation') ? false: true,
								height:60,
								width: 250,
								columnWidth: 1.0
							},
							{
								id: 'xpathExpression',
								xtype: 'textarea',
								height:60,
								name: 'xpathExpression',
								fieldLabel: EasySDI_Mon.lang.getLocal('norm request xpath'),
								allowBlank:true,
								value: rec.get('xpathExpression') ? rec.get('xpathExpression') : '',
								disabled: rec.get('useXpathValidation') ? false: true,
								width: 250,
								columnWidth: 1.0
							},
							{
								html: '<a href="http://www.w3schools.com/xpath/xpath_syntax.asp" target="_blank">'+EasySDI_Mon.lang.getLocal('norm request help xpathsyntax')+'</a>'
							}],
							buttons: [
						         {
					        	 	id:'runTestNormBtnID',
				             		text: EasySDI_Mon.lang.getLocal('jobRequestTabTestBtn'),
				             		handler: function(){
				             			updateRequest(true,rec);
				             		}
								},
							    {
									id:'saveNormBtnID',
									text: EasySDI_Mon.lang.getLocal('grid action ok'),
									handler: function(){
										updateRequest(false,rec);
									}
							},{
								text: EasySDI_Mon.lang.getLocal('grid action cancel'),
								handler: function(){
									win.close();
							  }
							}]
							
						}
						]}
					]
				});
		
		var previewNorm = new Ext.FormPanel({
			id: 'newPreviewPanel',
			monitorValid:true,
			frame:true,
			labelWidth: 50,
			autoWidth:true,
			height: 370,
			region:'center',
			items: [
				{
					id:'regNamePreview',
					xtype: 'label',
					text:  rec.get('name'),
					fieldLabel: 'Request',
					height: 80
			
				},
				{
				layout:'column',
				items:[		
							{
								columnWidth:.2,
								layout: 'form',
								items:[
								{
									html: 'Size(Bytes)',
									height: 30
								},
								{
									html: 'Time(ms)',
									height: 30
								},
								{
									html: 'Xpath',
									height: 30
								}
								]
							},
							{
								columnWidth:.4,
								layout: 'form',
								items: [
								{
									id: 'normSizePreview',
									xtype: 'textfield',
									anchor:'95%',
									disabled: true,
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request norm'),
									name: 'normSizePreview'
								},
								{
									id: 'normTimePreview',
									xtype: 'textfield',
									anchor:'95%',
									disabled: true,
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request norm'),
									name: 'normTimePreview'
								},
								{
									id: 'xpathNormPreview',
									xtype: 'textarea',
									height:100,
									name: 'xpathNormPreview',
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request norm'),
									allowBlank:true,
									disabled: true,
									anchor:'95%',
									value: ''
								},
								{
									id: 'responseLinkPreview',
									html: '',
									anchor:'95%'
								}
								]
							},
							{
								columnWidth:.4,
								layout: 'form',
								items:[
							
								{
									id: 'resultSizePreview',
									xtype: 'textfield',
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request result'),
									name: 'resultSizePreview',
									anchor:'95%'
								},
								{
									id: 'resultTimePreview',
									xtype: 'textfield',
									anchor:'95%',
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request result'),
									name: 'resultTimePreview'
								},
								{
									id: 'xpathResultPreview',
									xtype: 'textarea',
									height:100,
									fieldLabel: EasySDI_Mon.lang.getLocal('preview request result'),
									name: 'xpathResultPreview',
									allowBlank: true,
									anchor:'95%',
									value: ''
								},
								{
									id: 'responseImagePreview',
									anchor:'95%',
									height: 142,
									html: ''
								}
								]
							}
							]}
							],
							buttons: [
							{
								id: 'runTestPreviewBtnID',
								text: EasySDI_Mon.lang.getLocal('jobRequestTabTestBtn'),
								handler: function(){
									updateRequest(true,rec);
								}
							},   
							{
								id:'runsaveTestBtnID',
								text: EasySDI_Mon.lang.getLocal('grid action ok'),
								handler: function(){
									updateRequest(false,rec);
								}
							},{
							text: EasySDI_Mon.lang.getLocal('grid action cancel'),
							handler: function(){
								win.close();
							}
							}]
					
			
		});
		
		requestParmPanel = {
			title: 'Request',
			xtype: 'panel',
			height: 370,
			border:false,
			items: [reqP]
		};
		
		requestNormPanel = {
			title: 'Norm values',
			xtype: 'panel',
			height: 370,
			border:false,
			items: [reqNorm]
		};
		
		requestPreviewPanel = {
				title: 'Preview',
				xtype: 'panel',
				height: 370,
				border:false,
				items: [previewNorm]
		};
	
		//Open a window for entering job's first values
		win = new  Ext.Window({
			id: 'winQuery',
			title:EasySDI_Mon.lang.getLocal('edit request'),
			width:450,
			height: 450,
			autoScroll:true,
			modal:true,
			resizable:false,
			items: [
			new Ext.TabPanel({
			id: 'card-tabs-panel2',
			activeTab: 0,
				items:[
					requestParmPanel,
					requestNormPanel,
					requestPreviewPanel
				]})
			]
		});	
		win.show();
		if(disableQueryAdv)
		{
			Ext.getCmp("hMComboxIDEdit").el.up('.x-form-item').setDisplayed(false);
			Ext.getCmp("sTComboxIDEdit").el.up('.x-form-item').setDisplayed(false);
		}
		reqOptionsHandler.enableSOAP(Ext.getCmp("reqServiceMethodComboEdit"), "reqSoapActionEdit");
		Ext.getCmp("reqServiceMethodComboEdit").on('select', function(){reqOptionsHandler.enableSOAP(Ext.getCmp("reqServiceMethodComboEdit"), "reqSoapActionEdit")} );
		Ext.getCmp('hMComboxIDEdit').on('select', function(cmb, rec){
			if(rec.data && rec.data.name)
			{
				changeMethods(rec.data.name,'reqServiceMethodComboEdit');
			}
		});
		
		if(showPreview)
		{
			showPreview = false;
			runRequestTest(rec.data);
		}
	}  
	
	/*
	 * Update request
	 * */
	function updateRequest(runTest,rec)
	{
	   var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
	   var jobName = jobRec.get('name');
	   //Change the proxy to the good url
	   proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries');
	   var fields = Ext.getCmp('editReqPanel').getForm().getFieldValues();
	   var fieldsValiation = Ext.getCmp('editNormPanel').getForm().getFieldValues(); 
	   var simple = Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase() != 'all';
	   //Avoids commit to each "set()"
	   var r = rec;
	   rec.beginEdit();
	   rec.set('serviceMethod', fields.serviceMethod);
	   rec.set('soapUrl', fields.soapUrl);
	   if(simple)
	   {
		   rec.set('queryMethod','');
	   }else
	   {
		   rec.set('queryMethod',fields.queryMethod);
	   }
	   var value = fields.serviceMethod.toLowerCase();
	   if(value.indexOf("soap") != -1 || value.indexOf("http get") != -1 || value.indexOf("http post") != -1 || simple)
	   {
		    rec.set('queryServiceType','');
		}else
		{
			var temp = fields.queryServiceType.toString();
			temp = temp.replace('[','').replace(']','');
			rec.set('queryServiceType',temp);
		}
	
	   rec.set('params', fields.params);
	   for(var el in fieldsValiation)
	   {
			rec.set(el, fieldsValiation[el]);
	   }
	 
	   rec.endEdit();
	   fieldParams = fields.params;
	   //after save, save the params
	   Ext.data.DataProxy.addListener('write', createMethodParams);
	   store.save();
	   // 0 when not and bigger if not
	  if(store.modified.length == 0)
	  {
		runRequestTest(r.data);
	  }
	
	   if(runTest)
	   {
		   testRun = true;
	   }else
	   {
		   testRun = false;
		   Ext.getCmp('winQuery').close();
	   }
	     	
	}
	
	/*
	 * Save new request
	 * */
	function saveNewRequest(runTest,rec)
	{
		var name = rec.get('name');
		proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/queries');
		var fields = Ext.getCmp('newReqPanel').getForm().getFieldValues();
		var fields2 = Ext.getCmp('newNormPanel').getForm().getFieldValues();
		
		var simple = Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase() != 'all';
		
		var u = new _reqGrid.store.recordType(EasySDI_Mon.DefaultReq);
		for (var el in fields)
		{
			if(el == "queryServiceType")
			{
				var value = Ext.getCmp('sMComboxID').getValue().toLowerCase();
				if(value.indexOf("soap") != -1 || value.indexOf("http get") != -1 || value.indexOf("http post") != -1 || simple)
				{
					u.set(el, '');
				}else
				{
					var temp = fields[el].toString();
					u.set(el, temp.replace('[','').replace(']',''));
				}
				
				
			}else if(el == "queryMethod" && simple)
			{
				u.set(el, '');
			}else
			{
				u.set(el, fields[el]);
			}
			if(el == "params"){
			
			   fieldParams = fields[el];
			}
		}
		for(var el in fields2)
		{
			u.set(el, fields2[el]);
		}
		_reqGrid.store.insert(0, u);
		Ext.data.DataProxy.addListener('write', createMethodParams);
		store.save();
	  if(runTest)
	  {
		  showPreview = true;  
	  }
	  else
	  {
		  showPreview = false;
	  }
	   Ext.getCmp('winQuery').close();
	}	
	
	function createMethodParams (proxy, action, result, res, rs) {
	  	
        Ext.data.DataProxy.removeListener('write', createMethodParams);      
	      
        var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		var jobName = jobRec.get('name');
		var name = rs.get('name');
	 
		if(res.raw.data.serviceMethod.toLowerCase().indexOf("soap")!=-1)
		{
			fieldParams ="soapenvelope="+encodeURIComponent(fieldParams);
		}
		var queryMethod = "";
		if(res.data && res.data[0].queryMethod)
		{
			queryMethod = res.data[0].queryMethod;
		}

		if(res.raw.data.serviceMethod.toLowerCase().indexOf("getrecords")!=-1 && (queryMethod.toLowerCase() == "post" || (queryMethod.toLowerCase() != "get" && jobRec.get('httpMethod').toLowerCase().indexOf("post") != -1 )))
		{
			fieldParams ="cswparam="+encodeURIComponent(fieldParams);
		}
		// Handling error when writing key word "format" with small
		fieldParams = trim(fieldParams);
		if(fieldParams.indexOf("format=") == 0)
		{
			fieldParams = fieldParams.replace("format=","FORMAT=");
		}else
		{
			fieldParams = fieldParams.replace("&format=","&FORMAT=");
		}
		if(showPreview)
		{
			queryNameToUse = name;
		}
		Ext.Ajax.request({
				loadMask: true,
				method: 'POST',
			  params: fieldParams,
			  url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries/'+name+"/params",
			  success: function(response){
			  	//reloadReqGrid();
			  },
			  failure: function(response){
		          EasySDI_Mon.App.setAlert(false,  EasySDI_Mon.lang.getLocal('error meth params')+'. '+'status:'+response.status+' message:'+response.statusText);
			  }
			});
	        
	      var paramsTemp = "";
		  if(rs.data.id)
		  {
			  paramsTemp = '{"data":{"id":"'+rs.data.id+'","useSizeValidation":"'+rs.data.useSizeValidation+'","normSize":"'+rs.data.normSize+'","normSizeTolerance": "'+rs.data.normSizeTolerance+'","useTimeValidation":"'+rs.data.useTimeValidation+'","normTime":"'+rs.data.normTime+'","useXpathValidation":"'+rs.data.useXpathValidation+'","xpathExpression":"'+rs.data.xpathExpression+'","expectedXpathOutput": "'+rs.data.expectedXpathOutput+'"}}';
		  }else{
			  paramsTemp = '{"data":{"useSizeValidation":"'+rs.data.useSizeValidation+'","normSize":"'+rs.data.normSize+'","normSizeTolerance": "'+rs.data.normSizeTolerance+'","useTimeValidation":"'+rs.data.useTimeValidation+'","normTime":"'+rs.data.normTime+'","useXpathValidation":"'+rs.data.useXpathValidation+'","xpathExpression":"'+rs.data.xpathExpression+'","expectedXpathOutput": "'+rs.data.expectedXpathOutput+'"}}';
		  }
	      Ext.Ajax.request({
					loadMask: true,
					method: 'PUT',
					headers: {
					'Content-Type': 'application/json'
				},
				params: paramsTemp,
				url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries/'+name+'/validationSettings',
				success: function(response){
					var jsonResp = Ext.util.JSON.decode(response.responseText);
					reloadReqGrid();
					if(testRun)
					{
						 runRequestTest(jsonResp.data);
					}
				},
				failure: function(response){
					 EasySDI_Mon.App.setAlert(false,  EasySDI_Mon.lang.getLocal('error meth params')+'. '+'status:'+response.status+' message:'+response.statusText);
				}
		});	
	}
  
	function runRequestTest(data){
  		var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
 		var jobName = jobRec.get('name'); 
 		var tab = Ext.getCmp('card-tabs-panel2');
		var rec = data;
		if(tab)
		{
			tab.setActiveTab(2);
		}
		var myMask = new Ext.LoadMask(Ext.getCmp('winQuery').getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
		myMask.show();
 		Ext.Ajax.request({
				loadMask: true,
				method: 'get',
				headers: {
				'Content-Type': 'application/json'
			},
			url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries/'+rec.queryID+'/preview',
			success: function(response){
				myMask.hide();
				var jsonResp = Ext.util.JSON.decode(response.responseText);
				if(jsonResp)
				{
					var dataurl = EasySDI_Mon.proxy+'image/preview/'+jsonResp.data.queryID+'?contenttype='+jsonResp.data.content_type;
					Ext.getCmp('resultSizePreview').setValue(jsonResp.data.size);
					Ext.getCmp('resultTimePreview').setValue(Math.round(jsonResp.data.time*1000));
					Ext.getCmp('xpathResultPreview').setValue(jsonResp.data.xpath_result);
					if(rec.useSizeValidation)
					{
						Ext.getCmp('normSizePreview').setValue(rec.normSize);
					}else
					{
						Ext.getCmp('normSizePreview').setValue(EasySDI_Mon.lang.getLocal('preview request novalidation'));
					}
					if(rec.useTimeValidation)
					{
						Ext.getCmp('normTimePreview').setValue(rec.normTime);
					}else
					{
						Ext.getCmp('normTimePreview').setValue(EasySDI_Mon.lang.getLocal('preview request novalidation'));
					}
					if(rec.useXpathValidation)
					{
						Ext.getCmp('xpathNormPreview').setValue(rec.expectedXpathOutput);
					}else
					{
						Ext.getCmp('xpathNormPreview').setValue(EasySDI_Mon.lang.getLocal('preview request novalidation'));
					}
				
					var testLink = Ext.getCmp('responseLinkPreview');
					if(testLink)
					{
						testLink.el.dom.setHTML('<a href="'+dataurl+'" target="_blank">'+EasySDI_Mon.lang.getLocal('overview text datalink')+'</a>')
					}
					var testImage = Ext.getCmp('responseImagePreview');
					if(jsonResp.data.content_type && jsonResp.data.content_type.toLowerCase().indexOf("image") != -1)
					{
						testImage.el.dom.setHTML('<img src='+dataurl+' alt="missing image" style="border:1px solid;" width="140" height="140" />');
					}else
					{
						testImage.el.dom.setHTML('');
					}
				}
			},
			failure: function(response){
				myMask.hide();
				EasySDI_Mon.App.setAlert(false,  EasySDI_Mon.lang.getLocal('error meth params')+'. '+'status:'+response.status+' message:'+response.statusText);
			}
 		});	
  	}
  
	/**
	 * onDelete
	 */
	function onDelete() {
		var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		var jobName = jobRec.get('name');
		var rec = _reqGrid.getSelectionModel().getSelected();
		var name = rec.get('name');
		proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries');
		if (!rec) {
			return false;
		}

		Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), String.format(EasySDI_Mon.lang.getLocal('confirm suppress req'), rec.get('name')), function(btn){
			if (btn == 'no')
				return false;
			else
				_reqGrid.store.remove(rec);
				store.save();
		});
	}

	//double click on a cell -> Update
	_reqGrid.on('rowdblclick', function(grid, rowIndex) {
		var record = grid.store.getAt(rowIndex);
		if (record) {
			onEdit();
		}
	});

	Ext.getCmp('JobGrid').getSelectionModel().on('selectionchange', function(sm){
		reloadReqGrid();
	});


  function reloadReqGrid(){
  //There is no job selected
    var sm = Ext.getCmp('JobGrid').getSelectionModel();
		if(sm.getCount() < 1){
			_reqGrid.addBtn.setDisabled(true);
		}
		else
			//A job has been selected, load the grid
		{
			_reqGrid.addBtn.setDisabled(false);

			var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
			var name = rec.get('name');
			var serviceType = rec.get('serviceType');
			//Change the proxy to the good url
			if(rec.get('isPublic') == true){
				proxy.setUrl(EasySDI_Mon.proxy+'/jobs/'+name+'/queries');
			}else{
				proxy.setUrl(EasySDI_Mon.proxy+'/adminJobs/'+name+'/queries');
			}
			store.load();
		}
  }



	_reqGrid.getSelectionModel().on('selectionchange', function(sm){
		_reqGrid.removeBtn.setDisabled(sm.getCount() < 1);
		_reqGrid.editBtn.setDisabled(sm.getCount() < 1);
	});
	
	function trim(s) {
		s = s.replace(/(^\s*)|(\s*$)/gi,"");
		s = s.replace(/[ ]{2,}/gi," ");
	return s;
	}
});