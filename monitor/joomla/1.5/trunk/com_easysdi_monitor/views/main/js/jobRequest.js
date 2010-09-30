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
		//url:'jobs/vd-wms-fonds2/queries',
		restful:true,
		proxy: proxy,
		writer: writer,
		fields:['serviceMethod', 'status', 'name', 'params']
	});

	var cm = new Ext.grid.ColumnModel([{
		header:EasySDI_Mon.lang.getLocal('grid header name'),
		dataIndex:"name",
		width:50,
		sortable: true,
	},{
		header:EasySDI_Mon.lang.getLocal('grid header method'),
		dataIndex:"serviceMethod",
		width:100,
		displayField:'method'
	},{
		header:EasySDI_Mon.lang.getLocal('grid header params'),
		dataIndex:"params",
		renderer: paramsRenderer,
		width:320,
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
				str += value[i].name+"="+value[i].value;
				if(i<value.length-1)
					str += "&";
			}
			return str;
		}
	}

	_reqGrid = new Ext.grid.GridPanel({
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


	/**
	 * onAdd
	 */
	function onAdd(btn, ev) {
		var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		if (!rec) {
			return false;
		}
		//Open a window for entering job's first values
		win = new  Ext.Window({
			title:EasySDI_Mon.lang.getLocal('new request'),
			width:450,
			autoScroll:true,
			modal:true,
			resizable:false,
			items: [new Ext.FormPanel({
				ref: 'newReqPanel',
				labelWidth: 90,
				monitorValid:true,
				bodyStyle:'padding:5px 5px 0',
				frame:true,
				defaults: {width: 290},
				defaultType: 'textfield',
				items: [{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header name'),
					xtype: 'textfield',
					name: 'name',
					allowBlank:false,
					vtype: 'reqname'
				},{
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
						data : EasySDI_Mon.ServiceMethodStore[Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase()]
					})
				},{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header params'),
					name: 'params',
					height:200,
					allowBlank:true,
					xtype: 'textarea',
					allowBlank: true
				}],
				buttons: [{
					formBind:true,
					text: EasySDI_Mon.lang.getLocal('grid action ok'),
					handler: function(){
					var name = rec.get('name');
					proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/queries');
					var fields = win.newReqPanel.getForm().getFieldValues();
					var u = new _reqGrid.store.recordType(EasySDI_Mon.DefaultReq);
					//u.set('name', fields.name);				     
					//u.set('serviceMethod', fields.serviceMethod);
					//u.set('params', fields.params);
					for (var el in fields){
						u.set(el, fields[el]);
					}
					_reqGrid.store.insert(0, u);
					win.close();
				}
				},{
					text: EasySDI_Mon.lang.getLocal('grid action cancel'),
					handler: function(){
					win.close();
				}
				}]
			})//en form panel new request
			]
		});
		win.show();
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
		for (var i=0; i<params.length; i++){
			strParams += params[i].name+"="+params[i].value;
			if(i<params.length-1)
				strParams += "&";
		}

		win = new  Ext.Window({
			title:EasySDI_Mon.lang.getLocal('edit request'),
			width:450,
			autoScroll:true,
			modal:true,
			resizable:false,
			items: [new Ext.FormPanel({
				ref: 'editReqPanel',
				labelWidth: 90,
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
					xtype:          'combo',
					mode:           'local',
					value:          rec.get('serviceMethod'),
					triggerAction:  'all',
					allowBlank:false,
					forceSelection: true,
					editable:       false,
					fieldLabel:     EasySDI_Mon.lang.getLocal('grid header method'),
					name:           'serviceMethod',
					displayField:   'name',
					valueField:     'name',
					store:          new Ext.data.SimpleStore({
						fields : ['name'],
						data : EasySDI_Mon.ServiceMethodStore[Ext.getCmp('JobGrid').getSelectionModel().getSelected().get('serviceType').toLowerCase()]
					})
				},{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header params'),
					value: strParams,
					height:200,
					name: 'params',
					allowBlank:true,
					xtype: 'textarea',
					allowBlank: true
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
					var fields = win.editReqPanel.getForm().getFieldValues();
					//Avoids commit to each "set()"
					var r = rec;
					rec.beginEdit();
					rec.set('serviceMethod', fields.serviceMethod);
					rec.set('params', fields.params);
					rec.endEdit();
					win.close();
				}
				},{
					text: EasySDI_Mon.lang.getLocal('grid action cancel'),
					handler: function(){
					win.close();
				}
				}]
			})//end form panel
			]
		});

		win.show();
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
				__reqGrid.store.remove(rec);
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
		//There is no job selected
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
	});

	_reqGrid.getSelectionModel().on('selectionchange', function(sm){
		_reqGrid.removeBtn.setDisabled(sm.getCount() < 1);
		_reqGrid.editBtn.setDisabled(sm.getCount() < 1);
	});

});