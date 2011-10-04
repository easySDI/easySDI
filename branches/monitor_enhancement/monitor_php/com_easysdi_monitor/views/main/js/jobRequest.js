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
		//fields:['serviceMethod', 'status', 'name', 'params']
		fields:[{name:'serviceMethod'},{name: 'status'},{name: 'name'},{name: 'statusCode'},{name: 'params'},{name: 'soapUrl'},
		        {name:'queryValidationSettings',mapping:'queryValidationSettings'},{name:'id',mapping:'queryValidationSettings.id'},{name:'useSizeValidation',mapping:'queryValidationSettings.useSizeValidation'},
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
	
	function enableNormSave()
	{
		if(Ext.getCmp('reqTextID').getValue() != "" && Ext.getCmp('sMComboxID').getValue() != "")
		{
					Ext.getCmp('saveNormBtnID').enable();
		}else
		{
					Ext.getCmp('saveNormBtnID').disable();
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
								//reqOptionsHandler.doLoadTypeOptions(Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('httpMethod'), Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType'))

						//	data : EasySDI_Mon.ServiceMethodStore[Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase()]
						}),
						listeners: {
							'change': enableNormSave
							//'select': reqOptionsHandler.enableSOAP(this, "reqSoapAction")
						}
					},					
					{
						id: 'reqSoapAction',
						fieldLabel: EasySDI_Mon.lang.getLocal('soap action'),
						xtype: 'textfield',
						name: 'soapUrl'
						
						
					},{
						fieldLabel: EasySDI_Mon.lang.getLocal('grid header params'),
						name: 'params',
						height:200,
						allowBlank:true,
						xtype: 'textarea'
						
					}],
					buttons: [{
						formBind:true,
						text: EasySDI_Mon.lang.getLocal('grid action ok'),
						handler: function(){
						var name = rec.get('name');
						proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/queries');
						var fields = Ext.getCmp('newReqPanel').getForm().getFieldValues();
						var fields2 = Ext.getCmp('newNormPanel').getForm().getFieldValues();
						var u = new _reqGrid.store.recordType(EasySDI_Mon.DefaultReq);
						for (var el in fields){
							u.set(el, fields[el]);
							if(el == "params")
							   fieldParams = fields[el];
						}
						for(var el in fields2)
						{
							u.set(el, fields2[el]);
						}
						_reqGrid.store.insert(0, u);
						Ext.data.DataProxy.addListener('write', createMethodParams);
						store.save();
						win.close();

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
								columnWidth: 1.0
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
								columnWidth: 1.0
							},
							{
								html: '<a href="http://www.w3schools.com/xpath/xpath_syntax.asp" target="_blank">'+EasySDI_Mon.lang.getLocal('norm request help xpathsyntax')+'</a>'
							}
							],
							buttons: [{
								id:'saveNormBtnID',
								formBind:true,
								text: EasySDI_Mon.lang.getLocal('grid action ok'),
								handler: function(){
								var name = rec.get('name');
								proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/queries');
								var fields = Ext.getCmp('newReqPanel').getForm().getFieldValues();
								var fields2 = Ext.getCmp('newNormPanel').getForm().getFieldValues();
								var u = new _reqGrid.store.recordType(EasySDI_Mon.DefaultReq);
								for (var el in fields){
									u.set(el, fields[el]);
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
								win.close();

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
		
		//Open a window for entering job's first values
		var win = new  Ext.Window({
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
					requestNormPanel 
				]})
			]
		});
		win.show();
		Ext.getCmp("reqSoapAction").el.up('.x-form-item').setDisplayed(false);
		Ext.getCmp("sMComboxID").on('select', function(){reqOptionsHandler.enableSOAP(Ext.getCmp("sMComboxID"), "reqSoapAction")} );
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
//		for (var i=0; i<params.length; i++){
//			if(value[i].value.indexOf("<soap:Envelope")!=-1){
//				str = value[i].value;
//				break; // we do not care about any other params in case we are dealing with a soap envelope
//			}else
//				strParams += params[i].name+"="+params[i].value;
//			
//			if(i<params.length-1)
//				strParams += "&";
//		}
		
		options = reqOptionsHandler.doLoadTypeOptions(Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('httpMethod').toLowerCase(), Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase());

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
						//data : EasySDI_Mon.ServiceMethodStore[Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase()]
					}),
					listeners:{
						
						//'select': reqOptionsHandler.enableSOAP(this, "reqSoapActionEdit")
					}
				},					
				{
					id: 'reqSoapActionEdit',
					fieldLabel: EasySDI_Mon.lang.getLocal('soap action'),
					value:   rec.get('soapUrl'),
					xtype: 'textfield',
					name: 'soapUrl'
					
				},{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header params'),
					value: strParams,
					name: 'params',
					height:200,
					allowBlank:true,
					xtype: 'textarea'
				}],
				buttons: [{
					formBind:true,
					text: EasySDI_Mon.lang.getLocal('grid action ok'),
					handler: function(){
					   var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
					   var jobName = jobRec.get('name');
					   var name = rec.get('name');
					   //Change the proxy to the good url
					   proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries');
					   var fields = Ext.getCmp('editReqPanel').getForm().getFieldValues();
					   var fieldsValiation = Ext.getCmp('editNormPanel').getForm().getFieldValues(); 
					   //Avoids commit to each "set()"
					   var r = rec;
					   rec.beginEdit();
					   rec.set('serviceMethod', fields.serviceMethod);
					   rec.set('params', fields.params);
					   rec.set('soapUrl', fields.soapUrl);
					   for(var el in fieldsValiation)
					   {
							rec.set(el, fieldsValiation[el]);
					   }
					   rec.endEdit();
					   fieldParams = fields.params;
					   //after save, save the params
					   Ext.data.DataProxy.addListener('write', createMethodParams);
					   store.save();
					   win.close();
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
							buttons: [{
								formBind:true,
								text: EasySDI_Mon.lang.getLocal('grid action ok'),
								handler: function(){
									 var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
									   var jobName = jobRec.get('name');
									   var name = rec.get('name');
									   //Change the proxy to the good url
									   proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries');
									   var fields = Ext.getCmp('editReqPanel').getForm().getFieldValues();
									   var fieldsValiation = Ext.getCmp('editNormPanel').getForm().getFieldValues(); 
									   //Avoids commit to each "set()"
									   var r = rec;
									   rec.beginEdit();
									   rec.set('serviceMethod', fields.serviceMethod);
									   
									   rec.set('params', fields.params);
									   for(var el in fieldsValiation)
									   {
											rec.set(el, fieldsValiation[el]);
									   }
									   rec.endEdit();
									   rec.endEdit();
									   fieldParams = fields.params;
									   //after save, save the params
									   Ext.data.DataProxy.addListener('write', createMethodParams);
									   store.save();
									   win.close();
									  
									}// end handler
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
	
		//Open a window for entering job's first values
		win = new  Ext.Window({
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
					requestNormPanel 
				]})
			]
		});	
		win.show();
		//Ext.getCmp("reqSoapActionEdit").el.up('.x-form-item').setDisplayed(false);
		reqOptionsHandler.enableSOAP(Ext.getCmp("reqServiceMethodComboEdit"), "reqSoapActionEdit");
		Ext.getCmp("reqServiceMethodComboEdit").on('select', function(){reqOptionsHandler.enableSOAP(Ext.getCmp("reqServiceMethodComboEdit"), "reqSoapActionEdit")} );
  }  
  
  function createMethodParams (proxy, action, result, res, rs) {
  	
	      Ext.data.DataProxy.removeListener('write', createMethodParams);      
	      
	      var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
			  var jobName = jobRec.get('name');
				var name = rs.get('name');
	 
		if(res.raw.data.serviceMethod.toLowerCase().indexOf("soap")!=-1)
			fieldParams ="soapenvelope="+encodeURIComponent(fieldParams);
		
	      Ext.Ajax.request({
					loadMask: true,
					method: 'POST',
				  params: fieldParams,
				  url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries/'+name+"/params",
				  success: function(response){
				  	reloadReqGrid();
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
					method: 'PUT',// BOTH insert/update
					headers: {
					'Content-Type': 'application/json'
				},
				params: paramsTemp,
				url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/queries/'+name+'/validationSettings',
				success: function(response){
					reloadReqGrid();
				},
				failure: function(response){
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

});