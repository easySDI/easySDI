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

EasySDI_Mon.chart1;
EasySDI_Mon.chart2;

var aStores = Array();
var tickInterval;
var jobRecord;

EasySDI_Mon.clearCharts = function() {
	if(EasySDI_Mon.chart1 != null){
		EasySDI_Mon.chart1.destroy();
		EasySDI_Mon.chart1 = null;
	}

	if(EasySDI_Mon.chart2 != null){
		EasySDI_Mon.chart2.destroy();
		EasySDI_Mon.chart2 = null;
	}

	//clean the stores
	for ( var storeName in aStores)
	{
		if(typeof aStores[storeName] != 'function'){
			if(aStores[storeName] != null)
				aStores[storeName].destroy();
		}
	}

	aStores = Array();
};

Ext.onReady(function() {

	var myMask = new Ext.LoadMask(Ext.getBody(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
	var jobComboStore = new Ext.data.SimpleStore({
		id:'jobId'
			,fields:[
			         {name: 'name'}
			         ],
			         data:[]
	});

	var methodComboStore = new Ext.data.JsonStore({
		id:'id',
		root:'data',
		restful:true,
		proxy: new Ext.data.HttpProxy({
			url: '?'
		}),
		fields:[
		        {id: 'id'},
		        {name: 'name'}
		        ]
	,
	data:{'data': [{'name':'All','value':'All'}]}
	});

	var slaComboStore = new Ext.data.SimpleStore({
		id: 'idSlaStore',
		fields:[{name: 'name',id:'id'}],
		data:[]
	});
	
	var exportConfig = Ext.data.Record.create(['id','exportName', 'exportType','exportDesc', 'xsltUrl' ]);
//  var exportTypeStore = new Ext.data.Store({
//	  proxy: new Ext.data.HttpProxy({  url :EasySDI_Mon.MonitorRoot+"&task=read"	}),
//	  root :'rows',
//	  fields :['id','exportName', 'exportType','exportDesc', 'xsltUrl']
//	//  autoload : true 
//	  
//  });
 var exportTypeStore = new Ext.data.Store({
	  id:'exportTypeStore',
	  proxy: new Ext.data.HttpProxy({  url :EasySDI_Mon.MonitorRoot+"&task=read"	}),
     reader: new Ext.data.JsonReader({
         root: 'rows',
         totalProperty: 'results',
         idProperty: 'id'
     }, exportConfig)
     //autoLoad: true
 });
 exportTypeStore.load();

 var body = Ext.getBody();
 var frame = body.createChild({
	 tag:'iframe'
		 ,cls:'x-hidden'
			 ,id:'iframe'
				 ,name:'iframe'
 });

 var exportForm = body.createChild({
	 tag:'form'
		 ,cls:'x-hidden'
			 ,id:'form'
				 ,action:EasySDI_Mon.MonitorRoot+"&task=requestExportData"
				 ,method:'POST'
				 ,target:'iframe'
 });
  
	/*
	 * Menu 
	 */
	// this is a singleton that manages the export report buttons
	var exportReportHandler = function(){

		return{
			
			doLoadExportTypes : function(btn){
				exportTypeStore.load();
				if (!btn.menu){
				btn.menu = new Ext.menu.Menu();
				}
				else{
					btn.menu.removeAll();
				}
				
				exportTypeStore.each(function(r) {
				
					 var action = new Ext.Action({
						 	id:r.data['id'],
					        text: r.data['exportName'],
					        handler: exportReportHandler.requestExportData	
					       
					    });

				 btn.menu.add(action)
				});
				
				
				
				btn.showMenu();
	

			},
			getExportTypes : function(btn){
				alert("getExportTypes");
				
			},
			setExportTypesAsBtnItems : function(btn){
				alert("setExportTypesAsBtnItems");
			},
			requestExportData :function(btn){
				exportTypeId = btn.id;			
			
				urls = mtnBtnView_click(true);
				dataUrls= [urls.join(',')]
				var params = {exportID: exportTypeId, proxyUrls: dataUrls};
				exportForm.dom.action = exportForm.dom.action+'&' + Ext.urlEncode(params);
				exportForm.dom.submit();
			

				myMask.hide();
				
			}
		
			
		}
	}();

	var reportMenu = new Ext.Panel({
		id: 'ReportMenu',
		layout: 'table',
		region: 'center',
		height: 50,
		frame:true,
		layoutConfig:{columns:10},
		border:false,
		defaults: {
			border: false,
			bodyStyle:'padding:5px 5px'
		}, 
		collapsible:false,
		items: [{
			html:EasySDI_Mon.lang.getLocal('job')+':'
		},{
			items:[{
				xtype:          'combo',
				mode:           'local',
				id:             'repCbJobs',
				triggerAction:  'all',
				forceSelection: true,
				editable:       false,
				fieldLabel:     'Job',
				name:           'jobComboFilter',
				displayField:   'name',
				valueField:     'name',
				emptyText: EasySDI_Mon.lang.getLocal('combo select a job'),
				store:jobComboStore
			}]
		},{
			html:EasySDI_Mon.lang.getLocal('report request select')+':'
		},{
			items:[{
				xtype:          'combo',
				mode:           'local',
				id:             'repCbMeth',
				value:          'All',
				triggerAction:  'all',
				forceSelection: true,
				editable:       false,
				fieldLabel:     'Job',
				name:           'reqComboFilter',
				displayField:   'name',
				valueField:     'name',
				emptyText:EasySDI_Mon.lang.getLocal('combo select a method'),
				store:methodComboStore
			}]
		},{
			html:EasySDI_Mon.lang.getLocal('period')+':'
		},{
			xtype: 'combo',
			mode: 'local',
			id: 'repCbPeriod',
			triggerAction: 'all',
			forceSelection: true,
			editable:       false,
			name:           'repCbPeriod',
			emptyText: EasySDI_Mon.lang.getLocal('combo select a period'),
			displayField:   'name',
			valueField:     'value',
			value: 'today',
			store:          new Ext.data.SimpleStore({
				fields : ['name', 'value'],
				data : EasySDI_Mon.RepPeriodStore
			}),
			width: 200
		},
		/*{
			html:EasySDI_Mon.lang.getLocal('report sla')+':'
		},*/
		{
			// New sla store
			items:[{
				xtype:          'combo',
				mode:           'local',
				id:             'repCbSla',
				triggerAction:  'all',
				forceSelection: true,
                                hidden: true,
				editable:       false,
				fieldLabel:     'Sla',
				name:           'slaComboFilter',
				displayField:   'name',
				valueField:     'id',
				emptyText: EasySDI_Mon.lang.getLocal('report combo select a sla'),
				store:  slaComboStore
			}]
		},
		{
			items:[{
				id: 'mtnBtnView',
				xtype:'button',
				handler: function(){
				   mtnBtnView_click();
			        },
				listeners: {
                                    click: function() {
				        mtnBtnView_click();
                                    }
                                },
			        text:  EasySDI_Mon.lang.getLocal('action view')
			}		
			]
		},
		{	items:[{
			id: 'exportBtn',
			xtype:'splitbutton',			
		    text: 	EasySDI_Mon.lang.getLocal('export report data'),
		    handler : exportReportHandler.doLoadExportTypes	,
		    listeners :{
		    	arrowclick : exportReportHandler.doLoadExportTypes
		    }
			
			}]
		},
		{},
		{},
		{},
		{},
		{},
		{
			items:[
			new Ext.FormPanel({
					id: 'periodeDatePanel',
					layout:'table',
					layoutConfig:{columns:2},
					hidden: true,
			items:[		
			{
				id: 'minDatePicker',
				xtype: 'datefield',
				name: 'minDatePicker',
				cellCls: 'ReportTableCell',
				width: 100,
				format: 'Y-m-d'
			},
			{
				id: 'maxDatePicker',
				xtype: 'datefield',
				name: 'maxDatePicker',
				maxValue: new Date().format('Y-m-d'),
				cellCls: 'ReportTableCell',
				width: 100,
				format: 'Y-m-d'
			}
			]
			})
			]
		}
		]
	});
	//load the menu items
	//debugger;


	/*****
	 * Event Handlers 
	 *****/

	//Initialize this store from the job grid store
	Ext.getCmp('JobGrid').store.on('load', function() {
		refreshComboValuesFromJobStore();
	});

	Ext.getCmp('JobGrid').store.on('write', function() {
		refreshComboValuesFromJobStore();
	});
	
	/**
	 * Handels event fired when sla store is updated
	 * */
	EasySDI_Mon.SlaUpdateEvent.on('updatedSla', function() {
		refreshComboValuesFromSlaStore();
	});

	function refreshComboValuesFromJobStore(){
		var aRec = Ext.getCmp('JobGrid').store.getRange();
		jobComboStore.removeAll();
		for ( var i=0; i< aRec.length; i++ )
		{
			var u = new jobComboStore.recordType({name:''});
			u.set('name', aRec[i].get('name'));
			jobComboStore.insert(0, u);
			//jobComboStore.add(aRec[i]);
		}
	}
	
	function refreshComboValuesFromSlaStore(){
		var sGrid = Ext.getCmp('SlaGrid');
		if(sGrid)
		{
			var aRec = Ext.getCmp('SlaGrid').store.getRange();
			slaComboStore.removeAll();
			var u = new slaComboStore.recordType({name:'',id:''});
			for ( var i=0; i< aRec.length; i++ )
			{
				u = new slaComboStore.recordType({name:'',id:''});
				u.set('id',aRec[i].id);
				u.set('name',aRec[i].get('name'));
				slaComboStore.insert(0,u);
			}
			u = new slaComboStore.recordType({ name:'',id:''});
			u.set('id','0');
			u.set('name','Default');
			slaComboStore.insert(0,u);
		}
	}
	
	Ext.getCmp('repCbPeriod').on('select',function(cmd,rec)
			{
				var name;
				if(rec == null){
				   name = Ext.getCmp('repCbPeriod').getValue();
				}else{
				   name = rec.get("value");
				}
				
				if(name && name == "period")
				{
					Ext.getCmp('periodeDatePanel').setVisible(true);
				}else
				{
					Ext.getCmp('periodeDatePanel').setVisible(false);
				}
	});
	
	Ext.getCmp('repCbJobs').on('select', function(cmb, rec){
		//refresh method store
		var name;
		if(rec == null){
		   name = Ext.getCmp('repCbJobs').getValue();
		}else{
		   name = rec.get("name");
		}		
		myMask.show();
		methodComboStore.proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/queries');
		methodComboStore.load();
		//pick timout for threshold in graphic.
		jobRecord = Ext.getCmp('JobGrid').store.getAt( Ext.getCmp('JobGrid').store.findExact('name', name));
		//refresh log grid as soon as the method store is loaded
		/*
             var minDate = Ext.getCmp('mtnMinDate').getValue().format('Y-m-d');
	     var p = logDailyStore.proxy;
	     logDailyStore.proxy.api.read.url=EasySDI_Mon.proxy+'/jobs/'+rec.id+'/logs?minDate='+minDate;
	     logDailyStore.proxy.conn.url=EasySDI_Mon.proxy+'/jobs/'+rec.id+'/logs?minDate='+minDate;
             logDailyStore.load();

		 */
	});


	methodComboStore.on('load', function() {
		myMask.hide();
		methodComboStore.addListener('add', methodComboStoreOnAdd);
		methodComboStore.add(new methodComboStore.recordType({name:'All',value:'All'}));
	});


	function methodComboStoreOnAdd(store, rec){
		methodComboStore.removeListener('add', methodComboStoreOnAdd);
		Ext.getCmp('repCbMeth').setValue(rec[0].get('value'));
	}

	//
        function mtnBtnView_click(getUrlOnly){
		//clean up the stores and the graphs
		EasySDI_Mon.clearCharts();

		var selJob = Ext.getCmp('repCbJobs').getValue();
		var selMet = Ext.getCmp('repCbMeth').getValue();
		var selPer = Ext.getCmp('repCbPeriod').getValue();

		if(selJob == ""){
			Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('report select job'));
			return false;
		}else if(selMet == ""){
			Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('report select method'));
			return false;
		}else if(selPer == ""){
			Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('report select period'));
			return false;
		}
		myMask.show();
		var logRes;
		var today = new Date();
		var minDate = new Date();
		//1st january 1970
		minDate.setTime(0);
		//today
		var maxDate = new Date();
		//Pick log ressource regarding the period

		switch (selPer){
		case "today":
			minDate = today;
			maxDate = today;
			logRes = "logs";
			//two hours
			tickInterval = 2 * 3600 * 1000;
			break;
		case "yesterday":
			var yesterday= new Date();
			yesterday.setDate(today.getDate()-1);
			minDate = yesterday;
			maxDate = today;
			logRes = "logs";
			//two hours
			tickInterval = 4 * 3600 * 1000;
			break;
		case "lastweek":
			var lastweek= new Date();
			lastweek.setDate(today.getDate()-7);
			minDate = lastweek;
			maxDate = new Date();
			logRes = "aggLogs";
			//a day
			tickInterval = 24 * 3600 * 1000;
			break;
		case "thismonth":
			var thismonth= new Date();
			thismonth.setDate(1);
			minDate = thismonth;
			maxDate = new Date();
			logRes = "aggLogs";
			//a week
			tickInterval = 7 * 24 * 3600 * 1000;
			break;
		case "past6months":
			var past6months= new Date();
			past6months.setMonth(today.getMonth()-5);
			past6months.setDate(1);
			minDate = past6months;
			maxDate = new Date();
			logRes = "aggLogs";
			//a month
			tickInterval = 4 * 7 * 24 * 3600 * 1000;
			break;
		case "pastyear":
			var pastyear= new Date();
			pastyear.setYear(today.getYear()-1);
			pastyear.setDate(1);
			minDate = pastyear;
			maxDate = new Date();
			logRes = "aggLogs";
			//a month
			tickInterval = 4 * 7 * 24 * 3600 * 1000;
			break;
		case "period":
			var minPeriod = Ext.getCmp('minDatePicker').getValue();
			var maxPeriod = Ext.getCmp('maxDatePicker').getValue();
			if(maxPeriod == "" || minPeriod == "")
			{
				Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('report enter period'));
				myMask.hide();
				return false;
			}
			minDate = minPeriod;
			maxDate = maxPeriod;
			if(!maxDate || !minDate || (maxDate < minDate))
			{
				Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('report invalid period'));
				myMask.hide();
				return false;
			}
			
			// find days
			var ms = minDate.getElapsed(maxDate);
			var r = ms % 86400000;
			var days = (ms - r) / 86400000;
			days++;
			if(days <= EasySDI_Mon.DaysForUsingLogs)
			{
				logRes = "logs";
			}else
			{
				logRes = "aggLogs";
			}
						
			tickInterval = findtickInterval(days);
			break;
		case "All":
			logRes = "aggLogs";
			//a month
			tickInterval = 4 * 7 * 24 * 3600 * 1000;
			break;
		default : 'All';
		}

		var aMethods = Array();
		if(selMet == "All"){
			var aRec = methodComboStore.getRange();
			for ( var i=0; i< aRec.length; i++ ){
				//if(aRec[i].get('name') != "All")
					aMethods.push(aRec[i].get('name'));
			}
		}else{
			aMethods.push(selMet);
		}
		// Use advanced SLA
		var useSla = Ext.getCmp('repCbSla').getValue() != "" &&  Ext.getCmp('repCbSla').getValue() != "0" ? true: false;
		
		//get the logs into a store. 1 store / method
		var loadedStores = 0;
		
		if(null!=getUrlOnly){
			
			urls =[];
			for ( var i=0; i< aMethods.length; i++ ){

			/*	var fields = null;
				if(logRes == "aggLogs")
				{
					if(useSla)
					{
						fields = ['h1Availability', 'inspireNbBizErrors','h1NbConnErrors','h1MeanRespTime','inspireMeanRespTime','h1NbBizErrors','inspireAvailability','inspireNbConnErrors','inspireMaxRespTime','h1MaxRespTime','inspireMinRespTime','h1MinRespTime','h1Unavailability','inspireUnavailability','h1Failure','inspireFailure','h1Untested','inspireUntested', {name: 'date', type: 'date', dateFormat: 'Y-m-d H:i:s'}];
					}else
					{
						fields = ['h24Availability', 'slaNbBizErrors','h24NbConnErrors','h24MeanRespTime','slaMeanRespTime','h24NbBizErrors','slaAvailability','slaNbConnErrors','slaMaxRespTime','h24MaxRespTime','slaMinRespTime','h24MinRespTime','h24Unavailability','slaUnavailability','h24Failure','slaFailure','h24Untested','slaUntested', {name: 'date', type: 'date', dateFormat: 'Y-m-d'}];
					}
				}else
				{
					if(aMethods[i] == "All")
					{
						fields = [{name: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 'message', 'httpCode', 'status', 'statusCode', 'delay','size','avCount','unavCount','fCount','otherCount','maxTime'];
					}else
					{
						fields = [{name: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 'message', 'httpCode', 'status', 'statusCode', 'delay','size']
					}
				}*/
				var slaParam = "";
				if(useSla)
				{
					slaParam = "&useSla="+ Ext.getCmp('repCbSla').getValue();
				}
				if(aMethods[i] == "All")
				{
					urls.push( EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+selJob+'/'+logRes+'?minDate='+minDate.format('Y-m-d')+'&maxDate='+maxDate.format('Y-m-d')+'&export=true'+ slaParam);
				}else
				{
					urls.push(	EasySDI_Mon.proxyserverside+EasySDI_Mon.CurrentJobCollection+'/'+selJob+'/queries/'+aMethods[i]+'/'+logRes+'?minDate='+minDate.format('Y-m-d')+'&maxDate='+maxDate.format('Y-m-d')+'&export=true'+slaParam);	
				}
			}
			return urls;	
		}
			
		for ( var i=0; i< aMethods.length; i++ ){

			var fields = null;
			if(logRes == "aggLogs")
			{
				if(useSla)
				{
					fields = ['h1Availability', 'inspireNbBizErrors','h1NbConnErrors','h1MeanRespTime','inspireMeanRespTime','h1NbBizErrors','inspireAvailability','inspireNbConnErrors','inspireMaxRespTime','h1MaxRespTime','inspireMinRespTime','h1MinRespTime','h1Unavailability','inspireUnavailability','h1Failure','inspireFailure','h1Untested','inspireUntested', {name: 'date', type: 'date', dateFormat: 'Y-m-d H:i:s'}];
				}else
				{
					fields = ['h24Availability', 'slaNbBizErrors','h24NbConnErrors','h24MeanRespTime','slaMeanRespTime','h24NbBizErrors','slaAvailability','slaNbConnErrors','slaMaxRespTime','h24MaxRespTime','slaMinRespTime','h24MinRespTime','h24Unavailability','slaUnavailability','h24Failure','slaFailure','h24Untested','slaUntested', {name: 'date', type: 'date', dateFormat: 'Y-m-d'}];
				}
				
				
			}else
			{
				if(aMethods[i] == "All")
				{
					fields = [{name: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 'message', 'httpCode', 'status', 'statusCode', 'delay','size','avCount','unavCount','fCount','otherCount','maxTime'];
				}else
				{
					fields = [{name: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 'message', 'httpCode', 'status', 'statusCode', 'delay','size']
				}
			}
			
			var slaParam = "";
			var slaID = -1;
			if(useSla)
			{
				slaParam = "&useSla="+ Ext.getCmp('repCbSla').getValue();
				slaID = Ext.getCmp('repCbSla').getValue();
			}
			
			// If All selected get summery graph for all the job methods
			if(aMethods[i] == "All")
			{
				var summeryGraph = EasySDI_Mon.lang.getLocal('graph summery');
				aStores[summeryGraph] = new Ext.data.JsonStore({
					root:'data',
					autoLoad: true,
					totalProperty:'totalCount',
					restful:true,
					proxy: new Ext.data.HttpProxy({
						url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+selJob+'/'+logRes+'?minDate='+minDate.format('Y-m-d')+'&maxDate='+maxDate.format('Y-m-d')+slaParam
					}),
					fields:fields,
					listeners: {
					load: function(){
					loadedStores ++;
					//Wait that all stores are loaded
					if(loadedStores == aMethods.length){
						var b = aStores;
						myMask.hide();
						onDataReady(aStores, logRes,useSla,slaID);
					}
				}
				}
				});
			}else
			{
				aStores[aMethods[i]] = new Ext.data.JsonStore({
					root:'data',
					autoLoad: true,
					totalProperty:'totalCount',
					restful:true,
					proxy: new Ext.data.HttpProxy({
						url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+selJob+'/queries/'+aMethods[i]+'/'+logRes+'?minDate='+minDate.format('Y-m-d')+'&maxDate='+maxDate.format('Y-m-d')+slaParam
					}),
					fields:fields,
					listeners: {
						load: function(){
							loadedStores ++;
							//Wait that all stores are loaded
							if(loadedStores == aMethods.length){
								var b = aStores;
								myMask.hide();
								onDataReady(aStores, logRes,useSla,slaID);
							}
						}
					}
				});
			}
		}
	}
    
    function findtickInterval(days)
	{
		var ticks = 0;
		if(days == 1)
		{
			ticks = 2 * 3600 * 1000;
		}else if(days == 2)
		{
			ticks = 4 * 3600 * 1000;
		}else if(days < 8 )
		{
			ticks = 24 * 3600 * 1000;
		}else if(days < 32)
		{
			ticks = 7 * 24 * 3600 * 1000;
		}else
		{
			ticks = 4 * 7 * 24 * 3600 * 1000;
		}
		return ticks;
	}
        
	//called when all stores are loaded
	function onDataReady(aStores, logRes,useSla,slaID){
		
		if(useSla)
		{
			var slaGrid = Ext.getCmp('SlaGrid');
			if(slaGrid)
			{
				var rec = slaGrid.store.getById(slaID);
				if(rec)
				{
					var showInspireGraph = false; 
					if(rec.get('isExcludeWorst') == 1)
					{
						showInspireGraph = true;
					}
				}
			}
		}
		
		//output the graphs
		EasySDI_Mon.chart1 = EasySDI_Mon.drawResponseTimeGraph('container1', aStores, logRes, tickInterval, jobRecord,useSla,showInspireGraph);
		if(logRes == 'logs')
		{
			EasySDI_Mon.chart2 = EasySDI_Mon.drawHealthGraphRaw('container2', aStores, logRes,useSla);
		}else{
			EasySDI_Mon.chart2 = EasySDI_Mon.drawHealthGraphAgg('container2', aStores, logRes,useSla,showInspireGraph);
			//EasySDI_Mon.chart3 = EasySDI_Mon.drawHealthLineGraph('container3', aStores, logRes, tickInterval);
		}

	}

});