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

Ext.onReady(function() {


	/**
	 * Renderer for the GridPanel
	 */
var proxy = new Ext.data.HttpProxy({
		
		url: EasySDI_Mon.proxy+EasySDI_Mon.DefaultJobCollection
	});

	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	}); 

	var store = new Ext.data.JsonStore({
		root: 'data',
		id: 'name',
		idProperty : 'data.id',
		totalProperty :'count',		
		restful:true,
		proxy: proxy,
		writer: writer,
		remoteSort : true,
		sortInfo :{
			field :'lastStatusUpdate',
			direction :"DESC"
			
		},
		fields:['status', 'statusCode', 'httpMethod', 'testInterval', 'bizErrors', 'isPublic', 'allowsRealTime', 'httpErrors', 'serviceType', 'password', 'url' ,'id' ,'slaEndTime', 'name', 'queries', 'login', 'triggersAlerts', 'timeout', 'isAutomatic', 'slaStartTime', {name: 'lastStatusUpdate', type: 'date', dateFormat: 'Y-m-d H:i:s'},'saveResponse']
	});



	var _loadMsk = new Ext.LoadMask(Ext.getBody(), {msg:EasySDI_Mon.lang.getLocal('message wait')})
	var cm = new Ext.grid.ColumnModel([{
		header:EasySDI_Mon.lang.getLocal('status'),
		dataIndex:"statusCode",
		width:50,
		renderer: EasySDI_Mon.StatusRenderer
	},{
		header:EasySDI_Mon.lang.getLocal('grid header name'),
		dataIndex:"name",
		width:100,
		sortable: true,
		editable:false
	},{
		header:EasySDI_Mon.lang.getLocal('grid header type'),
		dataIndex:"serviceType",
		width:50,
		sortable: true,
		editable:false
	},{
		header:EasySDI_Mon.lang.getLocal('grid header url'),
		dataIndex:"url",
		width:270
	},{
		header:EasySDI_Mon.lang.getLocal('grid header isrealtime short'),
		dataIndex:"allowsRealTime",
		width:60,
		trueText: 'true',
		falseText: 'false',
		renderer: EasySDI_Mon.TrueFalseRenderer
	},{
		header:EasySDI_Mon.lang.getLocal('grid header isauto'),
		dataIndex:"isAutomatic",
		width:70,
		trueText: 'true',
		falseText: 'false',
		renderer: EasySDI_Mon.TrueFalseRenderer
	},{
		header:EasySDI_Mon.lang.getLocal('grid header triggersAlerts'),
		dataIndex:"triggersAlerts",
		width:80,
		trueText: 'true',
		falseText: 'false',
		renderer: EasySDI_Mon.TrueFalseRenderer
	},{
	  header:EasySDI_Mon.lang.getLocal('grid header lastJobStatusUpdateTime'),
	  dataIndex:"lastStatusUpdate",
	  width:150,
	  sortable: true,
	  renderer: EasySDI_Mon.DateTimeRenderer
	}
	/*
	,{
	header:EasySDI_Mon.lang.getLocal('grid header dateTime'),
	dataIndex:"time",
	width:150,
	sortable: true,
	renderer: EasySDI_Mon.DateTimeRenderer
	}*/

	]);

	var _jobStateGrid = new Ext.grid.GridPanel({
		id:'JobStateGrid',
		loadMask:true,
		region:'center',
		stripeRows: true,
		tbar: [{
			iconCls:'icon-service-execute',
			ref: '../executeBtn',
			disabled: true,
			text: EasySDI_Mon.lang.getLocal('action execute'),
			handler: onRealTimeExecute
		}
		,'-',{
			iconCls:'icon-service-refresh',
			ref: '../majBtn',
			text: EasySDI_Mon.lang.getLocal('grid action update'),
			disabled: false,
			handler: function(){
			Ext.getCmp('JobGrid').store.load();
		}
		},'-',{
			iconCls:'icon-service-view-alerts',
			ref: '../viewAlertsBtn',
			text: EasySDI_Mon.lang.getLocal('action view alerts'),
			disabled: true,
			handler: function(){
			Ext.getCmp('card-tabs-panel').setActiveTab(3);
			var rec = _jobStateGrid.getSelectionModel().getSelected();
			Ext.getCmp('AlertGrid').cbJobs.setValue(rec.get('name'));
		}
		},'-',{
			iconCls:'icon-service-view-reports',
			ref: '../viewReportsBtn',
			text: EasySDI_Mon.lang.getLocal('action view reports'),
			disabled: true,
			handler: function(){
			Ext.getCmp('card-tabs-panel').setActiveTab(2);
			var rec = _jobStateGrid.getSelectionModel().getSelected();
			Ext.getCmp('repCbJobs').setValue(rec.get('name'));
			Ext.getCmp('repCbMeth').store.addListener('load', methCmbStoreLoaded);
			Ext.getCmp('repCbJobs').fireEvent('select');
			
                        //Ext.getCmp('mtnBtnView').fireEvent('click');
			//EasySDI_Mon.mtnBtnView_click();
	        }
		}
		],

		title:EasySDI_Mon.lang.getLocal('job list'),
		loadMask:_loadMsk,
		store:store,
		cm:cm,
		/*
       sm: new Ext.grid.RowSelectionModel({
           singleSelect: true,
           listeners: {
               rowselect: function(sm, row, rec) {
                   Ext.getCmp("jobAdvForm").getForm().loadRecord(rec);
               }
           }
       }),
		*/
		// paging bar on the bottom
		bbar: new Ext.PagingToolbar({
			pageSize: 15,
			store: store,
			displayInfo: true,
			displayMsg: EasySDI_Mon.lang.getLocal('paging display msg'),
			emptyMsg: EasySDI_Mon.lang.getLocal('paging empty msg')
		})
		 
	});
	
	function methCmbStoreLoaded(){
	    Ext.getCmp('repCbMeth').store.removeListener('load', methCmbStoreLoaded);
	    Ext.getCmp('mtnBtnView').fireEvent('click');
	}

	function onRealTimeExecute(btn, ev){
		var rec = _jobStateGrid.getSelectionModel().getSelected();
		if (!rec) {
			return false;
		}

		if(!rec.get('allowsRealTime')){
			Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('msg alert'), EasySDI_Mon.lang.getLocal('paging empty msg'), null);
			return false;
		}

		new  Ext.Window({
			id: 'win'+rec.get('name'),
			width:450,
			autoScroll:true,
			//modal:true,
			title:EasySDI_Mon.lang.getLocal('msg real time execute summary')+' '+rec.get('name'),
			items: [new Ext.FormPanel({
				id: 'formPanel'+rec.get('name'),
				labelWidth: 90, // label settings here cascade unless overridden
				region:'center',
				bodyStyle:'padding:5px 5px 0',
				frame:true,
				autoHeight:true,
				items: [{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header name'),
					// disabled:true,
					xtype: 'textfield',
					name: 'jobName'
				},
				{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header type'),
					xtype: 'textfield',
					name: 'serviceType'
				},
				{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header method'),
					xtype: 'textfield',
					name: 'httpMethod'
				},
				{
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header url'),
					width:260,
					xtype: 'textfield',
					name: 'url'
				},
				{
					fieldLabel: EasySDI_Mon.lang.getLocal('status'),
					xtype: 'textfield',
					name: 'status'
				},
				{
					fieldLabel: EasySDI_Mon.lang.getLocal('cause'),
					width:260,
					xtype: 'textfield',
					name: 'statusCause'
				},{
					xtype: 'fieldset',
					//defaults:{anchor:'-20'},
					collapsible: true,
					collapsed: true,
					height: 'auto',
					title: EasySDI_Mon.lang.getLocal('requests'),
					//bodyStyle:'margin:15px',
					items:[
					       new Ext.Panel({
					    	   title: EasySDI_Mon.lang.getLocal('requests'),
					    	   layout: 'border',
					    	   border:true,
					    	   frame:true,
					    	   height:100,
					    	   items: [
					    	           new Ext.grid.GridPanel({
					    	        	   id:'qrGrid'+rec.get('name'),
					    	        	   region: 'center',
					    	        	   autoScroll:true,
					    	        	   height:100,
					    	        	   store: new Ext.data.JsonStore({
					    	        		   autoDestroy: true,
					    	        		   fields:[
					    	        		           {name: 'queryName'}
					    	        		           ,{name: 'httpCode'}
					    	        		           ,{name: 'message'}
					    	        		           ,{name: 'requestTime'}
					    	        		           ,{name: 'responseDelay', type: 'float'}
					    	        		           ,{name: 'serviceExceptionCode'}
					    	        		           ,{name: 'status'}
					    	        		           ,{name: 'statusCode'}
					    	        		           ,{name: 'testedUrl'}
					    	        		           ],
					    	        		           data:[]
					    	        	   }),
					    	        	   cm:new Ext.grid.ColumnModel([{
					    	        		   header:EasySDI_Mon.lang.getLocal('grid header name'),
					    	        		   dataIndex:"queryName",
					    	        		   width:100,
					    	        		   sortable: true,
					    	        		   renderer: function (value, scope, row){
					    	        		   return '<a href="'+row.get('testedUrl')+'" target="_blank">'+value+'</a>';
					    	        	   }
					    	        	   },{
					    	        		   header:EasySDI_Mon.lang.getLocal('status'),
					    	        		   dataIndex:"statusCode",
					    	        		   width:45,
					    	        		   renderer:EasySDI_Mon.StatusRenderer					    	        	   
					    	        	   },{
					    	        		   header:EasySDI_Mon.lang.getLocal('delay'),
					    	        		   dataIndex:"responseDelay",
					    	        		   width:60,
					    	        		   renderer: EasySDI_Mon.DelayRenderer
					    	        	   },{
					    	        		   header:EasySDI_Mon.lang.getLocal('grid header httpcode'),
					    	        		   dataIndex:"httpCode",
					    	        		   width:60
					    	        	   },{
					    	        		   header:EasySDI_Mon.lang.getLocal('grid header ogccode'),
					    	        		   dataIndex:"serviceExceptionCode",
					    	        		   width:60
					    	        	   },{
					    	        		   header:EasySDI_Mon.lang.getLocal('grid header message'),
					    	        		   dataIndex:"message",
					    	        		   width:100
					    	        	   }
					    	        	   ])
					    	           })
					    	           ]
					       })
					       ]//end fieldset items
				}],
				buttons: [{
					text: EasySDI_Mon.lang.getLocal('grid action close'),
					handler: function(){
					Ext.getCmp('win'+rec.get('name')).close();
				}
				}]
			})]
		}).show();

		var myMask = new Ext.LoadMask(Ext.getCmp('win'+rec.get('name')).getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
		myMask.show();
		Ext.Ajax.request({
			loadMask: true,
			url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+rec.get('name')+'/status',
			success: function(response){
			myMask.hide();
			var jsonResp = Ext.util.JSON.decode(response.responseText);
			Ext.getCmp('formPanel'+rec.get('name')).getForm().setValues(jsonResp.data);
			Ext.getCmp('qrGrid'+rec.get('name')).store.loadData(jsonResp.data.queriesResults);
		},
		failure: function(response){
			myMask.hide();
		}
		});

	}

	store.setDefaultSort("lastStatusUpdate", "DESC");
	store.load({params:{start:0, limit:15}});
	_jobStateGrid.getSelectionModel().on('selectionchange', function(sm){
		_jobStateGrid.executeBtn.setDisabled(sm.getCount() < 1);
		_jobStateGrid.viewAlertsBtn.setDisabled(sm.getCount() < 1);
		_jobStateGrid.viewReportsBtn.setDisabled(sm.getCount() < 1);

	});


});