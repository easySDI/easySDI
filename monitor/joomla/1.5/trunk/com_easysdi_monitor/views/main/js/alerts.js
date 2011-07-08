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

	var store = new Ext.data.SimpleStore({
		id:'jobId',
		fields:[
		        {name: 'alertId'},
		        {name: 'newStatusCode'}
		        ,{name: 'oldStatusCode'}
		        ,{name: 'cause'}
		        ,{name: 'httpCode'}
		        ,{name: 'responseDelay'}
		        ,{name: 'isExposedToRss', type: 'boolean'}
		        ,{name: 'jobId', type: 'int'}
		        ,{name: 'dateTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
		        {name: 'content_type'}
		        ],
		        data:[]
	});

	//store.loadData(mydata);

	var jobComboStore = new Ext.data.SimpleStore({
		id:'jobId',
		fields:[
		        {name: 'name'}
		        ],
		data:['All']
	});



	//Initialize this store from the job grid store
	Ext.getCmp('JobGrid').store.on('load', function() {
		refreshComboValuesFromJobStore();
	});

	Ext.getCmp('JobGrid').store.on('write', function() {
		refreshComboValuesFromJobStore();
	});

	function refreshComboValuesFromJobStore(){
		var aRec = Ext.getCmp('JobGrid').store.getRange();
		jobComboStore.removeAll();
		for ( var i=0; i< aRec.length; i++ )
		{
			var u = new jobComboStore.recordType({name:''});
			u.set('name', aRec[i].get('name'));
			jobComboStore.insert(0, u);
		}
		var u = new jobComboStore.recordType({name:'All'});
		jobComboStore.insert(0, u);
	}

	var cm = new Ext.grid.ColumnModel([{
		header:EasySDI_Mon.lang.getLocal('job'),
		dataIndex:"jobId",
		width:100,
		renderer: function (value) {
		//return the job name from its id from the job store. Isn't it beautiful?
		return Ext.getCmp('JobGrid').store.getAt(Ext.getCmp('JobGrid').store.findExact('id', value)).get('name');
	},
	sortable: true
	},{
		header:EasySDI_Mon.lang.getLocal('status'),
		dataIndex:"newStatusCode",
		width:64,
		renderer: function (newStatus, scope, row){
		return EasySDI_Mon.AlertStatusRenderer(newStatus, scope, row);
	}
	},{
		header:EasySDI_Mon.lang.getLocal('cause'),
		dataIndex:"cause",
		width:100
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
		header:EasySDI_Mon.lang.getLocal('grid header dateTime'),
		dataIndex:"dateTime",
		width:150,
		sortable: true,
		renderer: EasySDI_Mon.DateTimeRenderer
	},
	{
		header:EasySDI_Mon.lang.getLocal('grid header responseview'),
		dataIndex: "alertId",
		width:100,
		 renderer: function (value, scope, row){
			 var response_url = EasySDI_Mon.proxy+'image/alert/'+value+'?contenttype='+row.data.content_type;
			 return '<a href="'+response_url+'" target="_blank">'+EasySDI_Mon.lang.getLocal('grid alert view')+'</a>';
		 }
	}
	]);

	var _alertsGrid = new Ext.grid.GridPanel({
		id:'AlertGrid',
		region:'center',
		stripeRows: true,
		title: EasySDI_Mon.lang.getLocal('alert list'),
		loadMask:new Ext.LoadMask(Ext.getBody(), {msg:EasySDI_Mon.lang.getLocal('message wait')}),
		store:store,
		cm:cm,
		// paging bar on the bottom
		/*
      No pagination because arraystore do not support load.
*/
       bbar: new Ext.PagingToolbar({
	    ref:'../gridPag',
            pageSize: 15,
            store: store,
            displayInfo: true,
            displayMsg: 'Affichage alertes {0} à {1} de {2}',
            emptyMsg: "Aucun job à afficher"
        }),
		 
		tbar: [{
			xtype:          'combo',
			mode:           'local',
			ref:             '../cbJobs',
			//value:          rec.get('serviceMethod'),
			triggerAction:  'all',
			forceSelection: true,
			editable:       false,
			fieldLabel:     EasySDI_Mon.lang.getLocal('job'),
			name:           'jobComboFilter',
			displayField:   'name',
			valueField:     'name',
			emptyText: EasySDI_Mon.lang.getLocal('combo select a job'),
			store:jobComboStore
			/*
	    listeners: {
               select: function(cmb, rec) {
                  alert(rec.id);
               }
             }
			 */
		}, '-',{
			id: 'btnLatestAlerts',
			ref:'../btnLatestAlerts',
			iconCls:'icon-only-newest-alert',
			text: EasySDI_Mon.lang.getLocal('only latest alerts'),
			enableToggle: true,
			toggleHandler: function (item, pressed){
			//clear the store
			store.removeAll();
			//trigger the job alerts loading either for one or all jobs
			
			if(_alertsGrid.cbJobs.getValue() == 'All'){
				var arrRec = jobComboStore.getRange();
				for ( var i=0; i< arrRec.length; i++ ){
					//create a store for all jobs and get their alerts
					if(arrRec[i].get('name') != 'All')
					   loadAlertData(arrRec[i].get('name'), i+1, arrRec.length);
				}
			}else{
				if(_alertsGrid.cbJobs.getValue() != ""){
					loadAlertData(_alertsGrid.cbJobs.getValue(), 1, 1);
				}
			}
		}
		}]
	});

	_alertsGrid.cbJobs.on('select', function(cmb, rec) {

		//clear the store
		store.removeAll();
		
		if(rec.get('name') != 'All'){
		   loadAlertData(rec.get('name'), 1, 1);
		}else{
		   var arrRec = jobComboStore.getRange();
       
		   for ( var i=0; i< arrRec.length; i++ ){
		   	  if(arrRec[i].get('name') != 'All')
		   	     //create a store for all jobs and get their alerts
		   	     loadAlertData(arrRec[i].get('name'), i+1, arrRec.length);
		   }
		}
	});


	function loadAlertData(jobName, current, total){
		var myMask = new Ext.LoadMask(Ext.getCmp('AlertGrid').getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
		myMask.show();
		if(total == 1){
			store.removeAll();
		var newStore = new Ext.data.JsonStore({
			//id: 'jobId',
			root: 'data',			
			proxy: new Ext.data.HttpProxy({
				url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/alerts'
			}),
			restful:true,
			idProperty : 'id',
			totalProperty :'count',
			fields:['newStatusCode', 'oldStatusCode', 'cause', 'httpCode', 'responseDelay', 'isExposedToRss', 'jobId', {name: 'dateTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},'alertId','content_type'],
			listeners :{
				load: function(){

					store.removeAll();
					var aRec = this.getRange();
					if(_alertsGrid.btnLatestAlerts.pressed){
						//If there is at least one record
						if(aRec.length > 0)
							store.add(aRec[aRec.length - 1]);
					}else{
						for ( var j=0; j< aRec.length; j++ )
						{
							//feed the grid store with the collected alerts
							store.add(aRec[j]);
						}
					}
				}
			}
		});
			component = Ext.getCmp('AlertGrid');
			if(component.getBottomToolbar()){
				component.getBottomToolbar().show();
				component.getBottomToolbar().bindStore(newStore);
				component.getBottomToolbar().doRefresh();
			}
			
			
			myMask.hide();
		
		}
		else{		
				component = Ext.getCmp('AlertGrid');
				if(component.getBottomToolbar())
					component.getBottomToolbar().hide();
				
				var newStore = new Ext.data.JsonStore({
					//id: 'jobId',
					root: 'data',
					autoLoad: true,
					proxy: new Ext.data.HttpProxy({
						url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/alerts'
					}),
					restful:true,
					fields:['newStatusCode', 'oldStatusCode', 'cause', 'httpCode', 'responseDelay', 'isExposedToRss', 'jobId', {name: 'dateTime', type: 'date', dateFormat: 'Y-m-d H:i:s'}],
					listeners: {
					load: function(){
					
					var aRec = this.getRange();
					if(_alertsGrid.btnLatestAlerts.pressed){
						//If there is at least one record
						if(aRec.length > 0)
							store.add(aRec[aRec.length - 1]);
					}else{
						for ( var j=0; j< aRec.length; j++ )
						{
							//feed the grid store with the collected alerts
							store.add(aRec[j]);
						}
					}
					if(current == total)
						myMask.hide();
				}
				}
				});
		}
		
	
			
			
		}
		
	}

);