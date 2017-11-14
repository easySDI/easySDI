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

Ext.BLANK_IMAGE_URL = '../components/com_easysdi_core/libraries/ext/resources/images/default/s.gif';	
//use not utc time to prevent time shift in graphs
Highcharts.setOptions({
	global: {
		useUTC: false
	}
});

Ext.onReady(function(){
   
   EasySDI_Mon.ServiceMethodStore = { 
       wms: [
              ['GetCapabilities'],
   	   ['GetMap']
   	 ],
       wfs: [
              ['GetCapabilities'],
   	   ['GetFeature']
   	 ],
       wmts: [
              ['GetCapabilities'],
   	   ['GetTile']
   	 ],
       csw: [
              ['GetCapabilities'],
   	   ['GetRecordById'],
   	   ['GetRecords']
   	 ],
       sos: [
              ['GetCapabilities'],
   	   ['DescribeSensor']
   	 ],
       wcs: [
              ['GetCapabilities'],
   	   ['GetCoverage']
   	 ],
   	allpost: [
     	    ['GetCapabilities'],
     	    ['GetMap'],
     	    ['GetFeature'],
     	    ['GetTile'],
     	    ['GetRecordById'],
 	        ['GetRecords'],
 	        ['DescribeSensor'],
 	        ['GetCoverage'],
    	    ['SOAP 1.1'],
 	        ['SOAP 1.2 '],
 	        ['HTTP POST']
	     ], 
	     
	allget: [
	 	     	    ['GetCapabilities'],
	 	     	    ['GetMap'],
	 	     	    ['GetFeature'],
	 	     	    ['GetTile'],
	 	     	    ['GetRecordById'],
	 	 	        ['GetRecords'],
	 	 	        ['DescribeSensor'],
	 	 	        ['GetCoverage'],
	 	 	        ['HTTP GET']
	 		 ]/*,
   	 all: [
      	    ['GetCapabilities'],
   	          ['GetMap'],
           ['GetFeature'],
   	      ['GetTile'],
   		  ['GetRecordById'],
   	          ['GetRecords'],
   		  ['DescribeSensor'],
   		  ['GetCoverage']
  	       ]*/
   }
   

   
   EasySDI_Mon.HttpMethodStore = [
                             ['GET'],
   		          ['POST']
   		       ];
   
   EasySDI_Mon.OgcServiceStore = [
   		         ['WMS'],
   			 ['WFS'],
   			 ['WMTS'],
   			 ['CSW'],
   			 ['SOS'],
   			 ['WCS'],
   			 ['ALL']
   		       ];
   
   EasySDI_Mon.RepPeriodStore = [
   		         [EasySDI_Mon.lang.getLocal('today'),'today'],
   			 [EasySDI_Mon.lang.getLocal('yesterday'),'yesterday'],
   			 [EasySDI_Mon.lang.getLocal('last week'),'lastweek'],
   			 [EasySDI_Mon.lang.getLocal('this month'),'thismonth'],
   			 [EasySDI_Mon.lang.getLocal('past 6 months'),'past6months'],
   			 [EasySDI_Mon.lang.getLocal('past year'),'pastyear'],
   			 [EasySDI_Mon.lang.getLocal('Enter period...'),'period'],
   			 [EasySDI_Mon.lang.getLocal('all'),'All']
   		       ];
   
   EasySDI_Mon.ToleranceStore = [
         ['0','0%'],['5','5%'],['10','10%'],['15','15%'],['20','20%'],['25','25%'],['40','40%'],['50','50%'],['75','75%'],['100','100%']
   ];
   
   /*Used in report for select between aggLogs and logs */
   EasySDI_Mon.DaysForUsingLogs = 3;                      
   		       
   EasySDI_Mon.DefaultJob = {
   		name:'',
   		httpMethod:'GET',
   		serviceType:'WMS',
   		url:'http://',
   		login:'',
   		password:'',
   		testInterval:3600,
   		timeout:5,
   		isPublic:true,
   		isAutomatic:true,
   		allowsRealTime:true,
   		triggersAlerts:true,
   		slaStartTime:'08:00:00',
   		slaEndTime:'18:00:00',
   		httpErrors:true,
   		bizErrors:true,
   		saveResponse:false,
   		runSimultaneous:false
   };
   
   EasySDI_Mon.DefaultSla = {
			name: '',
			excludeWorst: true,
			measureTimeToFirst: true
	   };
	   
   EasySDI_Mon.DefaultPeriod = {
			name: '',
			isMonday: false,
			isTuesday: false,
			isWednesday: false,
			isThursday: false,
			isFriday: false,
			isSaturday: false,
			isSunday: false,
			isHolidays: false,
			slaStartTime: '00:00:00',
			slaEndTime: '24:00:00',
			isInclude: true,
			date: ''
   };
   
   EasySDI_Mon.DefaultReq = {
   		name:'',
   		serviceMethod:'',
   		params:''
   };
   
   EasySDI_Mon.DefaultGetCapReq = {
   		name:'',
   		serviceMethod:''
   };
   
   EasySDI_Mon.DefaultOverviewPage = {
		   name:'',
		   isPublic:''
   };
   
   EasySDI_Mon.DefaultPageCombo = {
		   name: ''
   };
   
   EasySDI_Mon.YesNoCombo = [[EasySDI_Mon.lang.getLocal('YES'), 'Y'],[EasySDI_Mon.lang.getLocal('NO'), 'N']];
   
   /*common renderers*/
   
   EasySDI_Mon.TrueFalseRenderer = function(value) {
	 if(value == 'true')
            return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-boolean-true"/></td></tr></table>';
         else if(value == true)
            return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-boolean-true"/></td></tr></table>';
         else
            return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-boolean-false"/></td></tr></table>';  
   };
   
   EasySDI_Mon.StatusRenderer = function(status){
              switch (status){
                 case 'AVAILABLE':
                       return '<table'+' title="'+EasySDI_Mon.lang.getLocal('available')+'" '+'width="100%"><tr><td align="center"><div class="icon-gridrenderer-available"/></td></tr></table>';
                 break;
                 case 'OUT_OF_ORDER':
                       return '<table'+' title="'+EasySDI_Mon.lang.getLocal('failure')+'" '+'width="100%"><tr><td align="center"><div class="icon-gridrenderer-failure"/></td></tr></table>';
                 break;
                 case 'UNAVAILABLE':
                       return '<table'+' title="'+EasySDI_Mon.lang.getLocal('unavailable')+'" '+'width="100%"><tr><td align="center"><div class="icon-gridrenderer-unavailable"/></td></tr></table>';
                 break;
   	             case 'NOT_TESTED':
                       return '<table'+' title="'+EasySDI_Mon.lang.getLocal('untested-unknown')+'" '+'width="100%"><tr><td align="center"><div class="icon-gridrenderer-untested"/></td></tr></table>';
                 break;
                 default: 
                    return status;
                 break;
            }
   };
   
   EasySDI_Mon.AlertStatusRenderer = function (newStatus, scope, row) {
      var oldStatus = row.get('oldStatusCode');
             switch (newStatus){
                case 'AVAILABLE':
   	            if(oldStatus == 'OUT_OF_ORDER')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title failure-to-available')+'" '+'class="icon-gridrenderer-failure-to-available"/>';
                if(oldStatus == 'UNAVAILABLE')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title unavailable-to-available')+'" '+'class="icon-gridrenderer-unavailable-to-available"/>';
                if(oldStatus == 'NOT_TESTED')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title untested-to-available')+'" '+'class="icon-gridrenderer-untested-to-available"/>';
                break;
                case 'OUT_OF_ORDER':
   	            if(oldStatus == 'AVAILABLE')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title available-to-failure')+'" '+'class="icon-gridrenderer-available-to-failure"/>';
                if(oldStatus == 'UNAVAILABLE')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title unavailable-to-failure')+'" '+'class="icon-gridrenderer-unavailable-to-failure"/>';
                if(oldStatus == 'NOT_TESTED')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title untested-to-failure')+'" '+'class="icon-gridrenderer-untested-to-failure"/>';
                break;
                case 'UNAVAILABLE':
   	            if(oldStatus == 'OUT_OF_ORDER')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title failure-to-unavailable')+'" '+'class="icon-gridrenderer-failure-to-unavailable"/>';
                if(oldStatus == 'AVAILABLE')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title available-to-unavailable')+'" '+'class="icon-gridrenderer-available-to-unavailable"/>';
                if(oldStatus == 'NOT_TESTED')
                      return '<div'+' title="'+EasySDI_Mon.lang.getLocal('title untested-to-unavailable')+'" '+'class="icon-gridrenderer-untested-to-unavailable"/>';
                break;
                default: 
                   return newStatus;
                break;
            }
   };
   
   EasySDI_Mon.DelayRenderer = function(value){
      return Math.round(value * 1000);
   };
   
   EasySDI_Mon.DateTimeRenderer = function (value){
      return Ext.util.Format.date(value, EasySDI_Mon.dateTimeFormat);
   };
   
   EasySDI_Mon.DateRenderer = function (value){
      return Ext.util.Format.date(value, EasySDI_Mon.dateFormat);
   };
   
   EasySDI_Mon.CurrentJobCollection = EasySDI_Mon.DefaultJobCollection;
   
   EasySDI_Mon.JobCollectionStore = [
   		         [EasySDI_Mon.lang.getLocal('job collection public'),'jobs'],
   			 [EasySDI_Mon.lang.getLocal('job collection private'),'adminJobs']
   		       ];
   
   EasySDI_Mon.App = new Ext.App({});
   
    // Listen to all DataProxy beforewrite events
    //
    //Ext.data.DataProxy.addListener('beforewrite', function(proxy, action) {
    //	EasySDI_Mon.App.setAlert(EasySDI_Mon.App.STATUS_NOTICE, "Before " + action);
    //});
    ////
    // all write events
    //
   
   EasySDI_Mon.EventComponent =  Ext.extend(Ext.util.Observable, {
	    constructor : function() {
	      this.addEvents('updatedSla');
	      EasySDI_Mon.EventComponent.superclass.constructor.call(this);
	    }
	  });
	  
   	EasySDI_Mon.SlaUpdateEvent = new EasySDI_Mon.EventComponent();
   
    Ext.data.DataProxy.addListener('write', function(proxy, action, result, res, rs) {
        EasySDI_Mon.App.setAlert(true, res.raw.message);
    });
    
    Ext.data.DataProxy.addListener('delete', function(proxy, action, result, res, rs) {
        EasySDI_Mon.App.setAlert(true, res.raw.message);
    });
    
    ////
    // all exception events
    //
    Ext.data.DataProxy.addListener('exception', function(proxy, type, action, options, res) {
		    if(res.raw != null)
		            EasySDI_Mon.App.setAlert(false,  res.raw.message);
		    else
			    EasySDI_Mon.App.setAlert(false,  'status:'+res.status+' message:'+res.statusText);
    });
   
});

