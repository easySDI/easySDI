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
   //Moved to definitions.js
   //var App = new Ext.App({});
   var eLogType = {"daily" : 0, "aggregate" : 1};

/**
*  View logs section
*/
    

    /*
    *  The daily grid
    */
    var logDailyStore = new Ext.data.JsonStore({
       id: 'name',
       totalProperty:'totalCount',
       //If totalCount implemented, then it's mandatory to define the root!
       //root:'rows',
       restful:true,
       proxy: new Ext.data.HttpProxy({
          url: '?'
       }),
       fields:['message', {name: 'time', type: 'date', dateFormat: 'Y-m-d H:i:s'}, 'httpCode', 'status', 'delay', 'queryId'],
       defaultParamNames:{
        start : 'startIndex',
        limit : 'maxResults',
        sort : 'sort',
        dir : 'dir'
       }
    });
    
   // var _loadMsk = new Ext.LoadMask(Ext.getBody(), {msg:"Attendre svp..."})
    var logDailyCm = new Ext.grid.ColumnModel([{
	header:EasySDI_Mon.lang.getLocal('grid header method'),
	dataIndex:"queryId",
	width:100,
	sortable: true,
	editable:false,
	renderer: function (value) {
		if(methodComboStore.getById(value) != null)
                   return methodComboStore.getById(value).get('name');
        }
	},{
	header:EasySDI_Mon.lang.getLocal('grid header httpcode'),
	dataIndex:"httpCode",
	width:50
	},{
	header:EasySDI_Mon.lang.getLocal('grid header status'),
	dataIndex:"status",
	width:50,
	renderer: EasySDI_Mon.StatusRenderer
        },{
	header:EasySDI_Mon.lang.getLocal('grid header message'),
	dataIndex:"message",
	width:150
        },{
	header:EasySDI_Mon.lang.getLocal('grid header delay'),
	dataIndex:"delay",
	width:50,
	renderer: EasySDI_Mon.DelayRenderer
        },{
	header:EasySDI_Mon.lang.getLocal('grid header dateTime'),
	dataIndex:"time",
	sortable: true,
	width:150,
	renderer: EasySDI_Mon.DateTimeRenderer
	}]);
   
    
    _jobDailyGrid = new Ext.grid.GridPanel({
       id:'JobDailyGrid',
       loadMask:true,
       region:'center',
       frame:true,
       store:logDailyStore,
       cm:logDailyCm,
       // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: logDailyStore,
            displayInfo: true,
            displayMsg: EasySDI_Mon.lang.getLocal('paging display msg'),
            emptyMsg: EasySDI_Mon.lang.getLocal('paging empty msg')
        })
   });
    
    dailyGridPanel = {
	    title: EasySDI_Mon.lang.getLocal('daily logs'),
	    xtype: 'panel',
            layout: 'border',
	    region: 'center',
	    border:false,
            items: [_jobDailyGrid]
    };
    
    var viewGridTabs = new Ext.TabPanel({
      //xtype: 'tabpanel',
      id: 'card-tabs-panel',
      //plain: true, //remove the header border
    //  height: 'auto',
      activeTab: 0,
      defaults: {bodyStyle: 'padding:15px'},
      items:[ 
               dailyGridPanel,
               {title:'plip',html:'foo',region:'center'}
	       //,
	       //_jobAggregateGrid
	    ]
   });
    
   
    var cardGridPanel = new Ext.Panel({
     // id: 'content-panel',
      //height:200,
      //width:600,
      region: 'center', // this is what makes this panel into a region within the containing layout
      layout: 'card',
      //margins: '2 5 5 0',
      activeItem: 0,
      border: false,
      items: [viewGridTabs]
   });
    
   var vlFs = {
          xtype: 'fieldset',
	  region: 'center',
	  layout: 'border',
	  collapsible: true,
          collapsed: true,
          //height: 'auto',
          title: EasySDI_Mon.lang.getLocal('logs preview'),
	  //height:230,
	 // html:'fooo!!'
	 items:[
	    cardGridPanel
	    //,
	    //{frame:true, border:true,title:'plip',html:'foo',region:'center'}
	 ]
       }
       
   

/**
*  Clear logs section
*/


   var jobComboStore = new Ext.data.SimpleStore({
      id:'jobId',
      fields:[
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
       data:[]
   });
   
   
   var clFs = {
          xtype: 'fieldset',
	  id: 'clearLogsFs',
	  region: 'north',
          height: 100,
	  layout:'table',
	  layoutConfig:{columns:7},
	  defaults: {
             bodyStyle:'padding:5px 5px'
          }, 
          collapsible:false,
          title: EasySDI_Mon.lang.getLocal('clear logs'),
	  items:[ {
		     html:EasySDI_Mon.lang.getLocal('job')
	     },{
		     html:EasySDI_Mon.lang.getLocal('grid header method')
	     },{
		     html:EasySDI_Mon.lang.getLocal('younger or equal than'),
		     colspan:5
	     },{
	     items:[{
                xtype:          'combo',
                mode:           'local',
	        id:             'mtnCbJobs',
                //value:          rec.get('serviceMethod'),
                triggerAction:  'all',
                forceSelection: true,
                editable:       false,
                //fieldLabel:     'Job',
                name:           'jobComboFilter',
                displayField:   'name',
                valueField:     'name',
	        emptyText: EasySDI_Mon.lang.getLocal('combo select a job'),
                store:jobComboStore
              }]
          },{
	     items:[{
                xtype:          'combo',
                mode:           'local',
	        id:             'mtnCbMeth',
                //value:          rec.get('serviceMethod'),
                triggerAction:  'all',
                forceSelection: true,
                editable:       false,
                //fieldLabel:     'Job',
                name:           'jobComboFilter',
                displayField:   'name',
                valueField:     'name',
	        emptyText:EasySDI_Mon.lang.getLocal('combo select a method'),
                store:methodComboStore
              }]
          },{
             items:[{
		id: 'mtnMinDate',
                xtype: 'datefield',
		format: 'd-m-Y',
		value: new Date(),
		allowBlank:false,
		editable:false,
		//altFormats: 'Y-m-d',
                timeWidth:60
              }]
          },{
             items:[{
		id: 'mtnDelDaily',
	        xtype:'button',
		disabled:true,
	        handler: function(){
		   clearLogs(eLogType.daily, false);
		},
	        text: EasySDI_Mon.lang.getLocal('action clear raw logs')
	     }]
          },{
             items:[{
		id: 'mtnDelAgg',
	        xtype:'button',
		disabled:true,
	        handler: function(){
		    clearLogs(eLogType.aggregate, false);
		},
	        text: EasySDI_Mon.lang.getLocal('action clear agg logs')
	     }]
          },{
             items:[{
		id: 'mtnDelAllDaily',
	        xtype:'button',
		disabled:false,
	        handler: function(){
		    clearLogs(eLogType.aggregate, true);
		},
	        text: EasySDI_Mon.lang.getLocal('action clear raw logs')
	     }]
          },{
             items:[{
		id: 'mtnDelAllAgg',
	        xtype:'button',
		disabled:false,
	        handler: function(){
		    clearLogs(eLogType.aggregate, true);
		},
	        text: EasySDI_Mon.lang.getLocal('action clear all agg logs')
	     }]
          }]
       }

       var maintenancePanel = new Ext.Panel({
            id:'MaintenancePanel',
	    region:'center',
	    layout: 'border',
	    border:false,
	    frame:true,
	    items: [clFs,
	    {html:'', region:'center'}
	    //For now we don't view the logs
	    //  , 
	    //   vlFs
	    ]
        });
       
   /*****
   *
   * Event Handlers 
   *
   *****/
          //Initialize this store from the job grid store
   Ext.getCmp('JobGrid').store.on('load', function() {
      
      //add an option for all jobs
      //var allJobRec = new jobComboStore.recordType({name:'All'});
      //jobComboStore.add(allJobRec);
      
      var aRec = Ext.getCmp('JobGrid').store.getRange();
      for ( var i=0; i< aRec.length; i++ )
      {
             jobComboStore.add(aRec[i]);
      }
   });
   
   Ext.getCmp('mtnCbJobs').on('select', function(cmb, rec){
      //refresh method store      
      methodComboStore.proxy.setUrl(EasySDI_Mon.proxy+'/jobs/'+rec.id+'/queries');
      methodComboStore.load();
      
      //refresh log grid as soon as the method store is loaded
     
      methodComboStore.on('load', function() {
         var minDate = Ext.getCmp('mtnMinDate').getValue().format('Y-m-d');
	 var p = logDailyStore.proxy;
	 logDailyStore.proxy.api.read.url=EasySDI_Mon.proxy+'/jobs/'+rec.id+'/logs?minDate='+minDate;
	 logDailyStore.proxy.conn.url=EasySDI_Mon.proxy+'/jobs/'+rec.id+'/logs?minDate='+minDate;
         logDailyStore.load();
      });
      
      
      
   });
   
   /* type: daily/aggregate*/
   function clearLogs(type, all){
     var selJob = Ext.getCmp('mtnCbJobs').getValue();
     var selMet = Ext.getCmp('mtnCbMeth').getValue();     
     var minDate = Ext.getCmp('mtnMinDate').getValue().format('Y-m-d');
     var selType = type == eLogType.daily ? 'logs' : 'aggLogs';
     var arrJob = Array();
     
     //get jobs to delete
     if(all){
	var aRec = jobComboStore.getRange();
        for(var j=0; j< aRec.length; j++ )
	   arrJob.push(aRec[j].id);
     }else{
	if(selJob != '')
           arrJob.push(selJob);
     }
     
     var myMask = new Ext.LoadMask(Ext.getCmp('clearLogsFs').getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
     myMask.show();
     var counter = arrJob.length;
     for( var j=0; j< arrJob.length; j++ ){
        if(selMet == ''){
	   Ext.Ajax.request({
              loadMask: true,
	      method: 'DELETE',
              url: EasySDI_Mon.proxy+'/jobs/'+arrJob[j]+'/'+selType+'?minDate='+minDate,
	      success: function(response){
		   counter --;
	           doResponse(response, counter, myMask);
              },
	      failure: function(response){
	           counter --;
	           doResponse(response, counter, myMask);
	      }
           });
	}else{
	   Ext.Ajax.request({
              loadMask: true,
	      method: 'DELETE',
              url: EasySDI_Mon.proxy+'/jobs/'+arrJob[j]+'/queries/'+selMet+'/'+selType+'?minDate='+minDate,
	      success: function(response){
	           counter --;
	           doResponse(response, counter, myMask);
              },
	      failure: function(response){
	           counter --;
	           doResponse(response, counter, myMask);
	      }
           });
	}
     }
   }
   
   function doResponse(response, counter, myMask){
      if(counter == 0)
	 myMask.hide();
      var jsonResp = Ext.util.JSON.decode(response.responseText);
      if(jsonResp.error != null)
         EasySDI_Mon.App.setAlert(EasySDI_Mon.App.STATUS_ERROR, jsonResp.error);
      if(!jsonResp.message != null)
         EasySDI_Mon.App.setAlert(EasySDI_Mon.App.STATUS_NOTICE, jsonResp.message);
   }
   
   Ext.getCmp('mtnCbJobs').on("select", function(field, newValue, oldValue) {
      Ext.getCmp('mtnDelDaily').setDisabled(false);
      Ext.getCmp('mtnDelAgg').setDisabled(false);
   });
   

   
});