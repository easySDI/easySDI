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
EasySDI_Mon.chart3;
   
EasySDI_Mon.clearCharts = function() {
           if(EasySDI_Mon.chart1 != null){
	       EasySDI_Mon.chart1.destroy();
	       EasySDI_Mon.chart1 = null;
	    }
	    
            if(EasySDI_Mon.chart2 != null){
	       EasySDI_Mon.chart2.destroy();
	       EasySDI_Mon.chart2 = null;
	    }
            
	    if(EasySDI_Mon.chart3 != null){
	       EasySDI_Mon.chart3.destroy();
	       EasySDI_Mon.chart3 = null;
	    }
};
	
Ext.onReady(function() {
   
   var aStores = Array();
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
      restful:true,
      proxy: new Ext.data.HttpProxy({
        url: '?'
      }),
      fields:[
          {id: 'id'},
          {name: 'name'}
       ],
       data:[{name:'All',value:'All'}]
   });
   
   
   /*
   * Menu 
   */
   
   var reportMenu = new Ext.Panel({
	    id: 'ReportMenu',
            layout: 'table',
	    region: 'center',
            height: 50,
	    frame:true,
	    layoutConfig:{columns:11},
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
		     html:EasySDI_Mon.lang.getLocal('grid header method')+':'
	     },{
	     items:[{
                xtype:          'combo',
                mode:           'local',
	        id:             'repCbMeth',
                triggerAction:  'all',
                forceSelection: true,
                editable:       false,
                fieldLabel:     'Job',
                name:           'jobComboFilter',
                displayField:   'name',
                valueField:     'name',
	        emptyText:EasySDI_Mon.lang.getLocal('combo select a method'),
                store:methodComboStore
              }]
             },{
		     html:EasySDI_Mon.lang.getLocal('period')+':',
	     },{
	        xtype: 'combo',
	        mode: 'local',
	        id: 'repCbPeriod',
		triggerAction: 'all',
		forceSelection: true,
		editable:       false,
               // fieldLabel:     'Job',
                name:           'repCbPeriod',
	        emptyText: EasySDI_Mon.lang.getLocal('combo select a period'),
	        displayField:   'name',
                valueField:     'value',
                store:          new Ext.data.SimpleStore({
                    fields : ['name', 'value'],
                    data : EasySDI_Mon.RepPeriodStore
                })
	     }
	     /*
	     ,{
		     html:' or from: '
	     },{
             items:[{
		id: 'reqMinDate',
                xtype: 'datefield',
		format: 'd-m-Y',
		//value: new Date(),
		allowBlank:false,
		editable:false,
		//altFormats: 'Y-m-d',
                timeWidth:60
              }]
             },{
		     html:' to: '
	     },{
             items:[{
		id: 'reqMaxDate',
                xtype: 'datefield',
		format: 'd-m-Y',
		//value: new Date(),
		allowBlank:false,
		editable:false,
		//altFormats: 'Y-m-d',
                timeWidth:60
              }]
             }
	     */
	     ,{
             items:[{
		id: 'mtnBtnView',
	        xtype:'button',
		//disabled:true,
	        handler: function(){
		   mtnBtnView_click();
		},
	        text: EasySDI_Mon.lang.getLocal('action view')
	     }]
             }]
	});

   
       /*****
       * Event Handlers 
       *****/
       //Initialize this store from the job grid store
       Ext.getCmp('JobGrid').store.on('load', function() {
          var aRec = Ext.getCmp('JobGrid').store.getRange();
          for ( var i=0; i< aRec.length; i++ )
          {
                 jobComboStore.add(aRec[i]);
          }
       });
       
       Ext.getCmp('repCbJobs').on('select', function(cmb, rec){
          //refresh method store
          myMask.show();
          methodComboStore.proxy.setUrl(EasySDI_Mon.proxy+'/jobs/'+rec.id+'/queries');
          methodComboStore.load();
          
	  //pick timout for threshold in graphic.
	  jobRecord = Ext.getCmp('JobGrid').store.getAt( Ext.getCmp('JobGrid').store.findExact('name', rec.id));
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
            methodComboStore.add(new methodComboStore.recordType({name:'All',value:'All'}));
       });
       
       var tickInterval;
       var jobRecord;
       
       function mtnBtnView_click(){
	  myMask.show();
	  var selJob = Ext.getCmp('repCbJobs').getValue();
          var selMet = Ext.getCmp('repCbMeth').getValue();
	  var selPer = Ext.getCmp('repCbPeriod').getValue();
	  
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
	        if(aRec[i].get('name') != "All")
		   aMethods.push(aRec[i].get('name'));
	     }
	  }else{
	     aMethods.push(selMet);
	  }
	  
	  //get the logs into a store. 1 store / method
	  var loadedStores = 0;
	  for ( var i=0; i< aMethods.length; i++ ){
	     
	     var fields = null;
	     if(logRes == "aggLogs")
		     fields = ['h24Availability', 'slaNbBizErrors','h24NbConnErrors','h24MeanRespTime','slaMeanRespTime','h24NbBizErrors','slaAvalabilty','slaNbConnErrors', {name: 'date', type: 'date', dateFormat: 'Y-m-d'}];
	     else
		     fields = [{name: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 'message', 'httpCode', 'status', 'delay']
	     
	     aStores[aMethods[i]] = new Ext.data.JsonStore({
	           autoLoad: true,
                   totalProperty:'totalCount',
                   restful:true,
                   proxy: new Ext.data.HttpProxy({
                      url: EasySDI_Mon.proxy+'/jobs/'+selJob+'/queries/'+aMethods[i]+'/'+logRes+'?minDate='+minDate.format('Y-m-d')+'?maxDate='+maxDate.format('Y-m-d')
                   }),
                   fields:fields,
		   listeners: {
                      load: function(){
	                 loadedStores ++;
			 //Wait that all stores are loaded
			 if(loadedStores == aMethods.length){
			    myMask.hide();
			    onDataReady(aStores, logRes);
			 }
	              }
                    }
                });
	  }
       }
       
       
        //called when all stores are loaded
	  function onDataReady(aStores, logRes){
            
	    EasySDI_Mon.clearCharts();
            
	    //output the graphs
	    EasySDI_Mon.chart1 = EasySDI_Mon.drawResponseTimeGraph('container1', aStores, logRes, tickInterval);
	    if(logRes == 'logs')
	       EasySDI_Mon.chart2 = EasySDI_Mon.drawHealthGraphRaw('container2', aStores, logRes);
            else{
               EasySDI_Mon.chart2 = EasySDI_Mon.drawHealthGraphAgg('container2', aStores, logRes);
	       EasySDI_Mon.chart3 = EasySDI_Mon.drawHealthLineGraph('container3', aStores, logRes, tickInterval);
	    }
	  }
	  
});