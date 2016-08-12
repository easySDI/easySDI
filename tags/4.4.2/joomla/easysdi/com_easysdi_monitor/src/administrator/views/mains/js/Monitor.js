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
   // Ext.BLANK_IMAGE_URL = 'images/s.gif';
    Ext.QuickTips.init();



	var editor = new Ext.ux.grid.RowEditor({
		saveText: EasySDI_Mon.lang.getLocal('grid action update'),
		cancelText: EasySDI_Mon.lang.getLocal('grid action cancel'),
		clicksToEdit: 2,
		 listeners: {
             afteredit: syncStore
         }
	});
	
	  EasySDI_Mon.MonitorRoot = "index.php?option=com_easysdi_monitor";
	  EasySDI_Mon.ExportTypeStore =  [['CSV'],['XML'],['XHTML']];

    var expportConfig = Ext.data.Record.create([
     'id','exportName', 'exportType','exportDesc', 'xsltUrl'      
    ]);
 
    var store = new Ext.data.Store({
        api: {
            create :  EasySDI_Mon.MonitorRoot+"&task=create",
            read : EasySDI_Mon.MonitorRoot+"&task=read",
            update: EasySDI_Mon.MonitorRoot+"&task=update",
            destroy: EasySDI_Mon.MonitorRoot+"&task=delete"
        },
        reader: new Ext.data.JsonReader({
            root: 'rows',
            totalProperty: 'results',
            idProperty: 'id'
        }, expportConfig),
        writer: new Ext.data.JsonWriter({
            writeAllFields: true
        }),
        autoLoad: false,
        autoSave: false     
      
    });
    
    function syncStore(rowEditor, changes, r, rowIndex) {
        store.save();
        
        var task = new Ext.util.DelayedTask(function(){ 
        	_exportGrid.getBottomToolbar().doRefresh();
        });
        task.delay(2000); 


       
    }


    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
           
            '<p>{exportDesc}</p>'
        ),
        enableCaching:false
    });

	
    var _exportGrid = new Ext.grid.GridPanel({
       id:'testGrid',
      
       loadMask:true,
		region:'center',
		stripeRows: true,
        title:   EasySDI_Mon.lang.getLocal('export type config'), 
        store: store,
        sm: new Ext.grid.RowSelectionModel({
            singleSelect: true
        }),
        columns: [expander,
                  {header: EasySDI_Mon.lang.getLocal('name'), dataIndex: 'exportName', width:100,	
                	  id :'name-col'  , 
              		sortable: true,
            		editable:true,
            		editor: {
            		xtype: 'textfield',
            		allowBlank: false,
            	         		
            		}},
                  {header: EasySDI_Mon.lang.getLocal('exporttype'), dataIndex: 'exportType', width:100,
            				editor: {
            				xtype: 'combo',
            				store: new Ext.data.SimpleStore({
            					fields: ['name'],
            					data : EasySDI_Mon.ExportTypeStore
            				}),
            				displayField:'name',
            				typeAhead: true,
            				mode: 'local',
            				triggerAction: 'all',
            				emptyText:'',
            				selectOnFocus:true
            		}},
                  {header: EasySDI_Mon.lang.getLocal('desc'), dataIndex: 'exportDesc', width:270,
            				editor: {
	              			xtype: 'textfield',
	              			allowBlank: false
	              }},
                  {header: EasySDI_Mon.lang.getLocal('xsltUrl'), dataIndex: 'xsltUrl',width:270,
            					editor: {
            					xtype: 'textfield',
            					allowBlank: true
            	  }}                 
              ]  , 
              plugins: [editor, expander],
	tbar: [{
		iconCls:'icon-service-add',
		text: EasySDI_Mon.lang.getLocal('grid action add'),
		handler: onAddExportConfig
	},'-',{
		iconCls:'icon-service-rem',
		ref: '../removeBtn',
		text: EasySDI_Mon.lang.getLocal('grid action rem'),
		handler: onDeleteExportConfig
	}], 
	bbar: new Ext.PagingToolbar({
		pageSize: 15,
		store: store,
		displayInfo: true,
		displayMsg: EasySDI_Mon.lang.getLocal('paging display msg'),
		emptyMsg: EasySDI_Mon.lang.getLocal('paging empty msg')
	}),
	autoExpandColumn :'name-col',
	collapsible: true,
    animCollapse: false



    });
    
    _exportGrid.getBottomToolbar().doLoad();
    
    function onAddExportConfig(btn, ev) {
    	
    	//create default record
		var u = new _exportGrid.store.recordType();

		//Open a window for entering job's first values
		var win = new  Ext.Window({
			width:380,
			autoScroll:true,
			modal:true,
			title:EasySDI_Mon.lang.getLocal('new export config'),
			items: [
			        new Ext.FormPanel({
			        	labelWidth: 90, // label settings here cascade unless overridden
			        	monitorValid:true,
			        	ref: 'exportPanel',
			        	region:'center',
			        	bodyStyle:'padding:5px 5px 0',
			        	autoHeight:true,
			        	frame:true,
			        	defaults: {width: 200},
			        	defaultType: 'textfield',
			        	autoHeight:true,
			        	items: [{
			        		fieldLabel: 'Export config name',			        		
			        		name: 'exportName',
			        		allowBlank:false,
			        		
			        	},{
			        		xtype:          'combo',
			        		mode:           'local',			        		
			        		triggerAction:  'all',
			        		forceSelection: true,
			        		editable:       false,
			        		fieldLabel:     EasySDI_Mon.lang.getLocal('export config type'),
			        		name:           'exportType',
			        		displayField:   'name',
			        		valueField:     'name',
			        		store:          new Ext.data.SimpleStore({
			        			fields : ['name'],
			        			data   :  EasySDI_Mon.ExportTypeStore
			        		})
			        	},{
			        		fieldLabel: 'xsltUrl',			        	
			        		name: 'xsltUrl',			        		
			        		xtype: 'textfield',
			        		allowBlank: true
			        	},{
			        		fieldLabel: 'description',			        		
			        		name: 'exportDesc',
			        		allowBlank:false,
			        		xtype: 'textfield'
			        		
			        	}],
			        	buttons: [{
			        		text: EasySDI_Mon.lang.getLocal('grid action ok'),
			        		//If validation fails disable the button
			        		formBind:true,
			        		handler: function(){
			        		editor.stopEditing();
			        		var fields = win.exportPanel.getForm().getFieldValues();
			        		var plop = u;
			        		u.set('exportName', fields.exportName);
			        		u.set('exportType', fields.exportType);
			        		u.set('exportDesc', fields.exportDesc);
			        		u.set('xsltUrl', fields.xsltUrl);			        		
			        		_exportGrid.store.insert(0, u);
			        		syncStore();
			        		win.close();
			        		//editor.startEditing(0);
			        		//Also create a request getCap for this job
			        		//Ext.data.DataProxy.addListener('write', afterJobInserted);
			        	}
			        	},{
			        		text: EasySDI_Mon.lang.getLocal('grid action cancel'),
			        		handler: function(){
			        		win.close();
			        	}
			        	}]
			        })
			        ]
		});

		win.show();
    	
    }
    
    function onDeleteExportConfig(btn, ev) {
        var sm = _exportGrid.getSelectionModel(),
        sel = sm.getSelected();
        if (sm.hasSelection()){
        Ext.Msg.show({
            title: EasySDI_Mon.lang.getLocal('Remove Export Config'), 
            buttons: Ext.MessageBox.YESNOCANCEL,
            msg:  EasySDI_Mon.lang.getLocal('remove') +" "+sel.data.exportName+'?',
            fn: function(btn){
                if (btn == 'yes'){
                	_exportGrid.getStore().remove(sel);
                	syncStore();
                }
            }
        });
    }
    	
    	
    }
});/**
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
	Ext.QuickTips.init();

	var url = String.format("../components/com_easysdi_core/libraries/ext/src/locale/ext-lang-{0}.js", EasySDI_Mon.locale);
	Ext.Ajax.request({
		url: url,
		success: function(response, opts){
		eval(response.responseText);
	},
	failure: function(){
		Ext.Msg.alert('Failure', EasySDI_Mon.lang.getLocal('error_lang')+' "'+EasySDI_Mon.locale+'"');
	},
	scope: this 
	});

});/**
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

Ext.form.VTypes['jobnameMask'] = /[a-z0-9_-]/i;
var alphanummore = /^[a-zA-Z0-9_-]+$/;
Ext.form.VTypes['alphanummore'] = function(v)
{   
	return alphanummore.test(v);
}

Ext.form.VTypes['jobname'] = function(v)
{   
	if(Ext.form.VTypes['alphanummore'](v)){
		if( Ext.getCmp('JobGrid').store.getById(v)){
			Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('jobname already exists');
			return false;
		}
		if(v.toUpperCase() == 'ALL'){
			Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('error reserved keyword');
			return false;
		}
		return true;
	}else{
		Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('error ressource name');
		return false;
	}	
	return true;
}

Ext.form.VTypes['slaname'] = function(v)
{   
	if(Ext.form.VTypes['alphanummore'](v)){
		if( Ext.getCmp('SlaGrid').store.getById(v)){
			Ext.form.VTypes['slanameText'] = EasySDI_Mon.lang.getLocal('slaname already exists');
			return false;
		}
		/*if(v.toUpperCase() == 'ALL'){
			Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('error reserved keyword');
			return false;
		}*/
		return true;
	}else{
		Ext.form.VTypes['slanameText'] = EasySDI_Mon.lang.getLocal('error ressource name');
		return false;
	}
	return true;
}

Ext.form.VTypes['reqname'] = function(v)
{   
	if(Ext.form.VTypes['alphanum'](v)){
		if( Ext.getCmp('ReqGrid').store.getById(v)){
			Ext.form.VTypes['reqnameText'] = EasySDI_Mon.lang.getLocal('reqname already exists');
			return false;
		}
		if(v.toUpperCase() == 'ALL'){
			Ext.form.VTypes['reqnameText'] = EasySDI_Mon.lang.getLocal('error reserved keyword');
			return false;
		}
		return true;
	}else{
		Ext.form.VTypes['reqnameText'] = EasySDI_Mon.lang.getLocal('error ressource name');
		return false;
	}
	return true;
}


/*!
 * Ext JS Library 3.2.1
 * Copyright(c) 2006-2010 Ext JS, Inc.
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
/**
 * Ext.App
 * @extends Ext.util.Observable
 * @author Chris Scott
 */
Ext.App = function(config) {

    // set up StateProvider
    this.initStateProvider();

    // array of views
    this.views = [];

    Ext.apply(this, config);
    if (!this.api.actions) { this.api.actions = {}; }

    // init when onReady fires.
    Ext.onReady(this.onReady, this);

    Ext.App.superclass.constructor.apply(this, arguments);
}
Ext.extend(Ext.App, Ext.util.Observable, {

    /***
     * response status codes.
     */
    STATUS_EXCEPTION :          'exception',
    STATUS_VALIDATION_ERROR :   "validation",
    STATUS_ERROR:               "error",
    STATUS_NOTICE:              "notice",
    STATUS_OK:                  "ok",
    STATUS_HELP:                "help",

    /**
     * @cfg {Object} api
     * remoting api.  should be defined in your own config js.
     */
    api: {
        url: null,
        type: null,
        actions: {}
    },

    // private, ref to message-box Element.
    msgCt : null,

    // @protected, onReady, executes when Ext.onReady fires.
    onReady : function() {
        // create the msgBox container.  used for App.setAlert
        this.msgCt = Ext.DomHelper.insertFirst(document.body, {id:'msg-div'}, true);
        this.msgCt.setStyle('position', 'absolute');
        this.msgCt.setStyle('z-index', 9999);
        this.msgCt.setWidth(300);
    },

    initStateProvider : function() {
        /*
         * set days to be however long you think cookies should last
         */
        var days = '';        // expires when browser closes
        if(days){
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var exptime = "; expires="+date.toGMTString();
        } else {
            var exptime = null;
        }

        // register provider with state manager.
        Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
            path: '/',
            expires: exptime,
            domain: null,
            secure: false
        }));
    },

    /**
     * registerView
     * register an application view component.
     * @param {Object} view
     */
    registerView : function(view) {
        this.views.push(view);
    },

    /**
     * getViews
     * return list of registered views
     */
    getViews : function() {
        return this.views;
    },

    /**
     * registerActions
     * registers new actions for API
     * @param {Object} actions
     */
    registerActions : function(actions) {
        Ext.apply(this.api.actions, actions);
    },

    /**
     * getAPI
     * return Ext Remoting api
     */
    getAPI : function() {
        return this.api;
    },

    /***
     * setAlert
     * show the message box.  Aliased to addMessage
     * @param {String} msg
     * @param {Bool} status
     */
    setAlert : function(status, msg) {
        this.addMessage(status, msg);
    },

    /***
     * adds a message to queue.
     * @param {String} msg
     * @param {Bool} status
     */
    addMessage : function(status, msg) {
        var delay = 3;    // <-- default delay of msg box is 1 second.
        if (status == false) {
            delay = 5;    // <-- when status is error, msg box delay is 3 seconds.
        }
        // add some smarts to msg's duration (div by 13.3 between 3 & 9 seconds)
        if(msg)
        {
		    delay = msg.length / 13.3;
		    if (delay < 3) {
		        delay = 3;
		    }
		    else if (delay > 9) {
		        delay = 9;
		    }
        }

        this.msgCt.alignTo(document, 't-t');
        Ext.DomHelper.append(this.msgCt, {html:this.buildMessageBox(status, String.format.apply(String, Array.prototype.slice.call(arguments, 1)))}, true).slideIn('t').pause(delay).ghost("t", {remove:true});
    },

    /***
     * buildMessageBox
     */
    buildMessageBox : function(title, msg) {
        switch (title) {
            case true:
                title = EasySDI_Mon.lang.getLocal(this.STATUS_OK);
                break;
            case false:
                title = EasySDI_Mon.lang.getLocal(this.STATUS_ERROR);
                break;
        }
        return [
            '<div class="app-msg">',
            '<div class="x-box-tl"><div class="x-box-tr"><div class="x-box-tc"></div></div></div>',
            '<div class="x-box-ml"><div class="x-box-mr"><div class="x-box-mc"><h3 class="x-icon-text icon-status-' + title + '">', title, '</h3>', msg, '</div></div></div>',
            '<div class="x-box-bl"><div class="x-box-br"><div class="x-box-bc"></div></div></div>',
            '</div>'
        ].join('');
    },

    /**
     * decodeStatusIcon
     * @param {Object} status
     */
    decodeStatusIcon : function(status) {
        var iconCls = '';
        switch (status) {
            case true:
            case this.STATUS_OK:
                iconCls = this.ICON_OK;           
                break;
            case this.STATUS_NOTICE:
                iconCls = this.ICON_NOTICE;
                break;
            case false:
            case this.STATUS_ERROR:
                iconCls = this.ICON_ERROR;
                break;
            case this.STATUS_HELP:
                iconCls = this.ICON_HELP;
                break;
        }
        return iconCls;
    },

    /***
     * setViewState, alias for Ext.state.Manager.set
     * @param {Object} key
     * @param {Object} value
     */
    setViewState : function(key, value) {
        Ext.state.Manager.set(key, value);
    },

    /***
     * getViewState, aliaz for Ext.state.Manager.get
     * @param {Object} cmd
     */
    getViewState : function(key) {
        return Ext.state.Manager.get(key);
    },

    /**
     * t
     * translation function.  needs to be implemented.  simply echos supplied word back currently.
     * @param {String} to translate
     * @return {String} translated.
     */
    t : function(words) {
        return words;
    },

    handleResponse : function(res) {
        if (res.type == this.STATUS_EXCEPTION) {
            return this.handleException(res);
        }
        if (res.message.length > 0) {
            this.setAlert(res.status, res.message);
        }
    },

    handleException : function(res) {
        Ext.MessageBox.alert(res.type.toUpperCase(), res.message);
    }
});/**
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
	Ext.QuickTips.init();

	var proxy = new Ext.data.HttpProxy({
		
		//url: EasySDI_Mon.proxy+EasySDI_Mon.DefaultJobCollection
                api:{
                read: { url: EasySDI_Mon.proxy+EasySDI_Mon.DefaultJobCollection, method: 'GET' },
                create: { url: EasySDI_Mon.proxy+EasySDI_Mon.DefaultJobCollection, method: 'POST' },
                update: { url: EasySDI_Mon.proxy+EasySDI_Mon.DefaultJobCollection, method: 'POST' },
                destroy: { url: EasySDI_Mon.proxy+EasySDI_Mon.DefaultJobCollection, method: 'DELETE' }
            }
	});

	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	}); 

	var store = new Ext.data.JsonStore({
		root: 'data',
		id: 'name',
		idProperty : 'data.id',
		totalProperty :'count',
		remoteSort : true,
		//autoSave: true,
		restful:true,
		proxy: proxy,
		writer: writer,
		sortInfo :{
			field :'name',
			direction :"DESC"
			
		},
		fields:['status', 'statusCode', 'httpMethod', 'testInterval', 'bizErrors', 'isPublic', 'allowsRealTime', 'httpErrors', 'serviceType', 'password', 'url' ,'id' ,'slaEndTime', 'name', 'queries', 'login', 'triggersAlerts', 'timeout', 'isAutomatic', 'slaStartTime', {name: 'lastStatusUpdate', type: 'date', dateFormat: 'Y-m-d H:i:s'},'saveResponse','runSimultaneous']
	
	
	});


	var editor = new Ext.ux.grid.RowEditor({
		saveText: EasySDI_Mon.lang.getLocal('grid action update'),
		cancelText: EasySDI_Mon.lang.getLocal('grid action cancel'),
		clicksToEdit: 2
	});


	var cm = new Ext.grid.ColumnModel([{
		header:EasySDI_Mon.lang.getLocal('grid header name'),
		dataIndex:"name",
		width:100,
		sortable: true,
		editable:false,
		editor: {
		xtype: 'textfield',
		allowBlank: false,
		vtype: 'alphanum'
	}
	},{
		header:EasySDI_Mon.lang.getLocal('grid header method'),
		dataIndex:"httpMethod",
		width:50,
		editor: {
		xtype: 'combo',
		store: new Ext.data.SimpleStore({
			fields: ['name'],
			data :EasySDI_Mon.HttpMethodStore
		}),
		displayField:'name',
		typeAhead: true,
		mode: 'local',
		triggerAction: 'all',
		emptyText:'',
		selectOnFocus:true
	}
	},{
		header:EasySDI_Mon.lang.getLocal('grid header type'),
		dataIndex:"serviceType",
		width:50,
		editor: {
		xtype: 'combo',
		store: new Ext.data.SimpleStore({
			fields: ['name'],
			data : EasySDI_Mon.OgcServiceStore
		}),
		displayField:'name',
		typeAhead: true,
		mode: 'local',
		triggerAction: 'all',
		emptyText:'',
		selectOnFocus:true
	}
	},{
		header:EasySDI_Mon.lang.getLocal('grid header url'),
		id:'url',
		dataIndex:"url",
		width:270,
		editor: {
		xtype: 'textfield',
		allowBlank: false
			}
	},{
		header:EasySDI_Mon.lang.getLocal('grid header interval'),
		dataIndex:"testInterval",
		width:60,
		editor: {
		xtype: 'numberfield',
		allowBlank: false,
		minValue: 1
	}
	},{
		header:EasySDI_Mon.lang.getLocal('grid header isauto'),
		dataIndex:"isAutomatic",
		width:30,
		//trueText: 'true',
		//falseText: 'false',
		renderer: EasySDI_Mon.TrueFalseRenderer,
		editor: {
		xtype: 'checkbox'
	}
	}
	/*
	,{
	header:EasySDI_Mon.lang.getLocal('grid header triggersAlerts'),
	dataIndex:"triggersAlerts",
	width:40,
	//trueText: 'true',
        //falseText: 'false',
	renderer: EasySDI_Mon.TrueFalseRenderer,
        editor: {
                xtype: 'checkbox'
           }
        }
	 */

	/*
    ,{
	header:"Status",
	dataIndex:"status",
	width:40,
	renderer: function (status){
           switch (status){
              case 'Disponible':
                    return '<div class="icon-gridrenderer-available"/>';
              break;
              case 'En dérangement':
                    return '<div class="icon-gridrenderer-failure"/>';
              break;
              case 'Indisponible':
                    return '<div class="icon-gridrenderer-unavailable"/>';
              break;
	      case 'Non testé':
                    return '<div class="icon-gridrenderer-untested"/>';
              break;
              default: 
                 return status;
              break;
          }
	}
	}
	 */
	]);

	var _jobGrid = new Ext.grid.GridPanel({
		id:'JobGrid',
		loadMask:true,
		region:'center',
		plugins: [editor],
		stripeRows: true,
		autoExpandColumn: 'url',
		tbar: [{
			iconCls:'icon-service-add',
			text: EasySDI_Mon.lang.getLocal('grid action add'),
			handler: onAdd
		},'-',{
			iconCls:'icon-service-rem',
			ref: '../removeBtn',
			text: EasySDI_Mon.lang.getLocal('grid action rem'),
			disabled: true,
			handler: onDelete
		}],
		title:EasySDI_Mon.lang.getLocal('job list'),
		//Ext.getCmp('AlertGrid').getEl()
		store:store,
		cm:cm,
		sm: new Ext.grid.RowSelectionModel({
			singleSelect: true,
			listeners: {
			rowselect: function(sm, row, rec) {
			Ext.getCmp("jobAdvForm").getForm().loadRecord(rec);
		}
		}
		}),
		bbar: new Ext.PagingToolbar({
			pageSize: 15,
			store: store,
			displayInfo: true,
			displayMsg: EasySDI_Mon.lang.getLocal('paging display msg'),
			emptyMsg: EasySDI_Mon.lang.getLocal('paging empty msg')
		})
		/*
		,
		// paging bar on the bottom
		bbar: new Ext.PagingToolbar({
			pageSize: 15,
			store: store,
			displayInfo: true,
			displayMsg: EasySDI_Mon.lang.getLocal('paging display msg'),
			emptyMsg: EasySDI_Mon.lang.getLocal('paging empty msg')
		})
		*/
	});
	//_jobGrid.loadMask = new Ext.LoadMask(_jobGrid.getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});


	/**
	 * onAdd
	 */
	function onAdd(btn, ev) {

		//create default record
		var u = new _jobGrid.store.recordType(EasySDI_Mon.DefaultJob);

		//Open a window for entering job's first values
		var win = new  Ext.Window({
			width:380,
			autoScroll:true,
			modal:true,
			title:EasySDI_Mon.lang.getLocal('title new job'),
			items: [
			        new Ext.FormPanel({
			        	labelWidth: 90, // label settings here cascade unless overridden
			        	monitorValid:true,
			        	ref: 'jobPanel',
			        	region:'center',
			        	bodyStyle:'padding:5px 5px 0',
			        	autoHeight:true,
			        	frame:true,
			        	defaults: {width: 200},
			        	defaultType: 'textfield',
			        	autoHeight:true,
			        	items: [{
			        		fieldLabel: EasySDI_Mon.lang.getLocal('grid header name'),
			        		value: u.data['name'],
			        		name: 'name',
			        		allowBlank:false,
			        		vtype: 'jobname'
			        	},{
			        		xtype:          'combo',
			        		mode:           'local',
			        		value:          u.data['httpMethod'],
			        		triggerAction:  'all',
			        		forceSelection: true,
			        		editable:       false,
			        		fieldLabel:     EasySDI_Mon.lang.getLocal('grid header method'),
			        		name:           'httpMethod',
			        		displayField:   'name',
			        		valueField:     'name',
			        		store:          new Ext.data.SimpleStore({
			        			fields : ['name'],
			        			data   : EasySDI_Mon.HttpMethodStore
			        		})
			        	},{
			        		xtype:          'combo',
			        		mode:           'local',
			        		value:          u.data['serviceType'],
			        		triggerAction:  'all',
			        		forceSelection: true,
			        		editable:       false,
			        		fieldLabel:     EasySDI_Mon.lang.getLocal('grid header type'),
			        		name:           'serviceType',
			        		displayField:   'name',
			        		valueField:     'name',
			        		store:          new Ext.data.SimpleStore({
			        			fields : ['name'],
			        			data : EasySDI_Mon.OgcServiceStore
			        		})
			        	},{
			        		fieldLabel: EasySDI_Mon.lang.getLocal('grid header url'),
			        		value: u.data['url'],
			        		name: 'url',
			        		allowBlank:false,
			        		xtype: 'textfield',
			        		allowBlank: false
			        					        	},{
			        		fieldLabel: EasySDI_Mon.lang.getLocal('grid header interval'),
			        		value: u.data['testInterval'],
			        		name: 'testInterval',
			        		allowBlank:false,
			        		xtype: 'numberfield',
			        		minValue: 1
			        	},{
			        		xtype: 'checkbox',
			        		fieldLabel: EasySDI_Mon.lang.getLocal('grid header isauto'),
			        		name: 'isAutomatic',
			        		checked: u.data['isAutomatic']
			        	},{
			        		xtype: 'checkbox',
			        		fieldLabel: EasySDI_Mon.lang.getLocal('grid header ispublic'),
			        		name: 'isPublic',
			        		checked: u.data['isPublic']
			        	}],
			        	buttons: [{
			        		text: EasySDI_Mon.lang.getLocal('grid action ok'),
			        		//If validation fails disable the button
			        		formBind:true,
			        		handler: function(){
			        		editor.stopEditing();
			        		var fields = win.jobPanel.getForm().getFieldValues();
			        		var plop = u;
			        		u.set('name', fields.name);
			        		u.set('httpMethod', fields.httpMethod);
			        		u.set('serviceType', fields.serviceType);
			        		u.set('url', fields.url);
			        		u.set('testInterval', fields.testInterval);
			        		u.set('isAutomatic', fields.isAutomatic);
			        		u.set('isPublic', fields.isPublic);
			        		_jobGrid.store.insert(0, u);
			        		win.close();
			        		//editor.startEditing(0);
			        		//Also create a request getCap for this job
			        		Ext.data.DataProxy.addListener('write', afterJobInserted);
			        	}
			        	},{
			        		text: EasySDI_Mon.lang.getLocal('grid action cancel'),
			        		handler: function(){
			        		win.close();
			        	}
			        	}]
			        })
			        ]
		});

		win.show();
	}

	/**
	 * addGetCap
	 */
	function afterJobInserted(proxy, action, result, res, rs){

		Ext.data.DataProxy.removeListener('write', afterJobInserted);
		Ext.data.DataProxy.addListener('write', afterReqInserted);
		var reqGrid = Ext.getCmp('ReqGrid');
		reqGrid.store.proxy.setUrl(EasySDI_Mon.proxy+'/jobs/'+result[0].name+'/queries');
		var u = new reqGrid.store.recordType(EasySDI_Mon.DefaultGetCapReq);
		u.set('name', 'GetCap');
		u.set('serviceMethod', 'GetCapabilities');
		reqGrid.store.insert(0, u);
		reqGrid.store.save();
		//If the job has been added to the other collection than the current,
		//we need to refresh the grid
		if((Ext.getCmp('jobCbCollection').getValue() == 'jobs' && result[0].isPublic == false)||
				(Ext.getCmp('jobCbCollection').getValue() == 'adminJobs' && result[0].isPublic == true))
			store.load();

	}

	function afterReqInserted(proxy, action, result, res, rs){
		Ext.data.DataProxy.removeListener('write', afterReqInserted);
		//Select the new row Row
		_jobGrid.getSelectionModel().selectFirstRow();
		_jobGrid.getView().focusRow(0);
	}

	/**
	 * onDelete
	 */
	function onDelete() {
		var rec = _jobGrid.getSelectionModel().getSelected();
		if (!rec) {
			return false;
		}
		Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), String.format(EasySDI_Mon.lang.getLocal('confirm suppress job'), rec.get('name')), function(btn){
			if (btn == 'no')
				return false;
			else
				_jobGrid.store.remove(rec);
		});
	}

	/**
	 * onShowAdvancedTab
	 */
	function onShowAdvancedTab() {

	}

	//dataStore.on('add', alert("add"));
	//dataStore.on('remove', alert("remove"));
	//dataStore.on('update', alert("update"));

	//grid.render('jobGrid');
	store.setDefaultSort("name", "ASC");
	store.load({params:{start:0, limit:15}});

	_jobGrid.getSelectionModel().on('selectionchange', function(sm){
		_jobGrid.removeBtn.setDisabled(sm.getCount() < 1);
		if(sm.getCount() < 1)
			_advForm.updateAdv.disable();
		else
			_advForm.updateAdv.enable();

	});


	//Advanced edition form
	var _advForm = new Ext.FormPanel({
		id: 'jobAdvForm',
		title: EasySDI_Mon.lang.getLocal('advanced'),
		// labelAlign: 'top',
		frame:true,
		//bodyStyle:'padding:5px 5px 0',
		// width: 400,
		autoHeight:true, // To fit for extra checkbox
		labelWidth: 100,
		autoWidth:true,
		region:'center',
		items: [{
			layout:'column',
			items:[{
				columnWidth:.5,
				layout: 'form',
				items: [{
					xtype:'textfield',
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header login'),
					name: 'login',
					allowBlank:true,
					anchor:'95%'
				},{
					xtype:'textfield',
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header password'),
					name: 'password',
					allowBlank:true,
					anchor:'95%'
				}]
			},{
				columnWidth:.5,
				layout: 'form',
				items: [{
					xtype:'timefield',
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header sla start'),
					name: 'slaStartTime',
					minValue: '7:00',
					maxValue: '18:00',
					increment: 30,
					format: 'H:i:s',
					anchor:'95%'
				},{
					xtype:'timefield',
					fieldLabel: EasySDI_Mon.lang.getLocal('grid header sla end'),
					name: 'slaEndTime',
					minValue: '7:00',
					maxValue: '18:00',
					increment: 30,
					format: 'H:i:s',
					anchor:'95%'
				}]
			}]
		},{
			xtype: 'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header isrealtime'),
			name: 'allowsRealTime',
			trueText: 'true',
			falseText: 'false'
		},{
			xtype: 'numberfield',
			minValue: 1,
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header timeout'),
			name: 'timeout',
			width: 40
		},{
			xtype: 'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header triggersAlerts'),
			name: 'triggersAlerts',
			trueText: 'true',
			falseText: 'false'
		},{
			xtype:'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header httperrors'),
			name: 'httpErrors',
			trueText: 'true',
			falseText: 'false'
		},{
			xtype:'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header ogcerrors'),
			name: 'bizErrors',
			trueText: 'true',
			falseText: 'false'
		},{
			xtype:'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header ispublic'),
			name: 'isPublic',
			trueText: 'true',
			falseText: 'false'
		},{
			xtype:'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header saveresponse'),
			name: 'saveResponse',
			trueText: 'true',
			falseText: 'false'
		},
		{
			xtype:'checkbox',
			fieldLabel: EasySDI_Mon.lang.getLocal('grid header runSimultaneous'),
			name: 'runSimultaneous',
			trueText: 'true',
			falseText: 'false'
			
		}

		],
		buttons: [{
			text: EasySDI_Mon.lang.getLocal('grid action update'),
			ref: '../updateAdv',
			disabled: true,
			handler: function(){
			var rec = _jobGrid.getSelectionModel().getSelected();
			if (!rec) {
				return false;
			}
			//rec = store.getById(rec.get('name'));
			//get form values
			var fields = _advForm.getForm().getFieldValues();
			//update rec values
			rec.beginEdit();
			for (var el in fields){
				rec.set(el, fields[el]);
			}
			rec.endEdit();
			rec.store.save();
			//reload the store because isPublic might changed
			Ext.data.DataProxy.addListener('write', afterStoreUpdated);
		}
		}]
	});

	function afterStoreUpdated(proxy, action, result, res, rs){

		Ext.data.DataProxy.removeListener('write', afterStoreUpdated);
		store.load();
	}




});		/**
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

});/**
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

	var rssRec;
	var emailRec;
	var rssCrVal='';
	var emailCrVal='';
	var emailTxtCrVal='';

	var proxy = new Ext.data.HttpProxy({
		url: EasySDI_Mon.proxy
	});

	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	});

	var store = new Ext.data.JsonStore({
		id: 'actionId',
		//if the store is not coupled with a form or grid, use autosave=flase and call store.save().
		autoSave: false,
		root: 'data',
		restful:true,
		proxy: proxy,
		writer: writer,
		fields:['actionId', 'type', 'target']
	});

	var _alertForm = new Ext.FormPanel({
		id:'AlertForm',
		title: EasySDI_Mon.lang.getLocal('alerts'),
		labelWidth: 90,
		region:'center',
		bodyStyle:'padding:5px 5px 0',
		autoHeight:true,
		frame:true,
		defaults: {width: 200},
		defaultType: 'textfield',
		items: [{
			xtype:'combo',
			mode:'local',
			ref: 'cbEmail',
			disabled:true,
			triggerAction:  'all',
			forceSelection: true,
			editable:       false,
			fieldLabel:     EasySDI_Mon.lang.getLocal('grid header email'),
			name:           'email',
			displayField:   'name',
			valueField:     'value',
			store:          new Ext.data.SimpleStore({
				fields : ['name', 'value'],
				data : EasySDI_Mon.YesNoCombo
			})
		},{
			fieldLabel: '',
			disabled:true,
			ref: 'txtEmail',
			//value: strParams,
			name: 'email_list',
			allowBlank:true,
			xtype: 'textarea'
		},{
			xtype:'combo',
			mode:'local',
			ref: 'cbRss',
			iconCls:'icon-service-add',
			disabled:true,
			triggerAction:  'all',
			forceSelection: true,
			editable:       false,
			fieldLabel:     EasySDI_Mon.lang.getLocal('grid header rss'),
			name:           'rss',
			displayField:   'name',
			valueField:     'value',
			store:          new Ext.data.SimpleStore({
				fields : ['name', 'value'],
				data : EasySDI_Mon.YesNoCombo
			})
		},{
			xtype:'button',
			ref: 'btnRss',
			iconCls:'icon-rss',
			disabled:true,
			fieldLabel:     EasySDI_Mon.lang.getLocal('rss link'),
			width:20
		}],
		buttons: [{
			text: EasySDI_Mon.lang.getLocal('grid action update'),
			disabled:true,
			ref: '../btnUpdate',
			handler: function(){
			var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
			var name = rec.get('name');
			proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions');
			proxy.api.destroy.url = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions';
			proxy.api.create.url = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions';
			var fields = _alertForm.getForm().getFieldValues();
			//Rss notification
			if(fields.rss == 'Y'){
				//create record if it does not exit
				if(rssRec == null){
					//create a new record
					var u = new store.recordType({
						type: 'RSS',
						target: ''
					});
					store.insert(0, u);
					rssCrVal = 'Y';
				}
			}
			else
			{
				if(rssRec != null){
					//drop record
					store.remove(rssRec);
					rssCrVal = 'N';
				}
			}

			//User requests Email notification
			if(fields.email == 'Y'){
				//if it doesn't exist yet, create a record
				if(emailRec == null){
					var u = new store.recordType({
						type: 'E-MAIL',
						target: fields.email_list
					});
					store.insert(0, u);
					emailCrVal = 'Y';
				}else{
					//if it already exit, update the target
					emailRec.set('target', fields.email_list);
					emailTxtCrVal = fields.email_list;
				}
			}
			//User doesn't requests Email notification
			else
			{
				//drop record if it exist
				if(emailRec != null){
					store.remove(emailRec);
					emailCrVal = 'N';
				}
			}
			Ext.data.DataProxy.addListener('write', afterUpdate);
			store.save();
			_alertForm.btnUpdate.setDisabled(true);
		}//end update
		}]
	});

  //reload the store after update
  function afterUpdate(proxy, action, result, res, rs){
		Ext.data.DataProxy.removeListener('write', afterUpdate);
		var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		var name = rec.get('name');
		proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions');
		proxy.api.read.url = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions';
		store.load();
	}

	Ext.getCmp('JobGrid').getSelectionModel().on('selectionchange', function(sm){
		//There is no job selected
		if(sm.getCount() < 1){
			_alertForm.cbRss.setDisabled(true);
			_alertForm.cbEmail.setDisabled(true);
			_alertForm.txtEmail.setDisabled(true);
			_alertForm.btnUpdate.setDisabled(true);
		}
		else
			//A job has been selected, load the grid
		{
			_alertForm.cbRss.setDisabled(false);
			_alertForm.cbEmail.setDisabled(false);
			_alertForm.txtEmail.setDisabled(false);

			var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
			var name = rec.get('name');
			//var serviceType = rec.get('serviceType');
			//Change the proxy to the good url
			if(rec.get('isPublic') == true)
				proxy.setUrl(EasySDI_Mon.proxy+'/jobs/'+name+'/actions');
			else
				proxy.setUrl(EasySDI_Mon.proxy+'/adminJobs/'+name+'/actions');

			store.load();
		}
	});

	store.on("load", function(store) {
		//Get the first occurence of type E-Mail and RSS
		var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		var jobName = jobRec.get('name');
		rssRec = store.getAt(store.findExact('type', 'RSS'));
		emailRec =  store.getAt(store.findExact('type', 'E-MAIL'));
		//Set the value in the form
		_alertForm.txtEmail.setValue('');
		if(rssRec == null){
			_alertForm.btnRss.setDisabled(true);
			_alertForm.btnRss.handler = null;
			_alertForm.cbRss.setValue('N');
			rssCrVal = 'N';
		}else{
			_alertForm.btnRss.setDisabled(false);
			_alertForm.btnRss.handler = function(){
				window.open(EasySDI_Mon.proxy+'/jobs/'+jobName+'/alerts&alt=rss',EasySDI_Mon.lang.getLocal('rss title')+": "+jobName);
			};
			_alertForm.cbRss.setValue('Y');
			rssCrVal = 'Y';
		}
		if(emailRec == null){
			_alertForm.cbEmail.setValue('N');
			emailCrVal = 'N';
		}else{
			_alertForm.cbEmail.setValue('Y');
			emailCrVal = 'Y';
			_alertForm.txtEmail.setValue(emailRec.get('target'));
			emailTxtCrVal = emailRec.get('target');
		}
	});

	//Some ennoying event handler for the update button
	_alertForm.cbRss.on("change", function(field, newValue, oldValue) {
		if(newValue == 'N')
			_alertForm.btnRss.setDisabled(true);
		else
			_alertForm.btnRss.setDisabled(false);

		if(newValue != rssCrVal || _alertForm.cbEmail.getValue() != emailCrVal || _alertForm.txtEmail.getValue() != emailTxtCrVal)
			_alertForm.btnUpdate.setDisabled(false);
		else
			_alertForm.btnUpdate.setDisabled(true);
	});

	_alertForm.cbEmail.on("change", function(field, newValue, oldValue) {
		if(newValue != emailCrVal || _alertForm.cbRss.getValue() != rssCrVal || _alertForm.txtEmail.getValue() != emailTxtCrVal)
			_alertForm.btnUpdate.setDisabled(false);
		else
			_alertForm.btnUpdate.setDisabled(true);
	});

	_alertForm.txtEmail.on("change", function(field, newValue, oldValue) {
		if(newValue != emailTxtCrVal || _alertForm.cbEmail.getValue() != emailCrVal || _alertForm.cbRss.getValue() != rssCrVal)
			_alertForm.btnUpdate.setDisabled(false);
		else
			_alertForm.btnUpdate.setDisabled(true);
	});

});/**
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
	* Property: editmode
	* Handles the period add/edit mode
	**/
	var periodeditmode = false;
	
	/**
	 * Proxy api for sla
	 */
	var proxySla = new Ext.data.HttpProxy({
		   api: {
		        read    : EasySDI_Mon.proxy+'sla',
		        create  : EasySDI_Mon.proxy+'sla',
		        update  : EasySDI_Mon.proxy+'sla',
		        destroy : EasySDI_Mon.proxy+'sla'
		    }
	});
	
	/**
	 * Proxy api for holidays
	 */ 
	var proxyHoliday = new Ext.data.HttpProxy({
		   api: {
		        read    : EasySDI_Mon.proxy+'holidays',
		        create  : EasySDI_Mon.proxy+'holidays',
		        update  : EasySDI_Mon.proxy+'holidays',
		        destroy : EasySDI_Mon.proxy+'holidays'
		    }
	});
	
	/**
	 * Proxy api for period
	 */
	var proxyPeriod = new Ext.data.HttpProxy({
		api: {
			read	: '?',
			create	: '?',
			update	: '?',
			destroy	: '?'
		}
	});
	
	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	});
	
	/**
	 * JsonStore: 
	 * For sla
	 */
	var slaStore = new Ext.data.JsonStore({
		id:'id',
		root: 'data',
		restful:true,
		fields:['name','isExcludeWorst','isMeasureTimeToFirst'],
		proxy: proxySla,
		writer: writer
	});
	
	/**
	 * JsonStore
	 * For holiday
	 */
	var holidayStore = new Ext.data.JsonStore({
		id: 'id',
		root: 'data',
		restful: true,
		fields:['name',{name :'date',type: 'date',dateFormat: 'Y-m-d H:i:s' }],
		proxy: proxyHoliday,
		writer: writer
	});

	
	/**
	 * JsonStore
	 * For period
	 */
	var periodStore = new Ext.data.JsonStore({
		id:'id',
		root: 'data',
		restful: true,
		autoSave: false,
		fields:['name','isMonday','isTuesday','isWednesday','isThursday','isFriday',
		        'isSaturday','isSunday','isHolidays','slaStartTime','slaEndTime','isInclude',
		        {name :'date',type: 'date',dateFormat: 'Y-m-d H:i:s' }],
		proxy: proxyPeriod,
		writer: writer
	});
	
	/**
	 * Editor for sla row
	 */
	var editor = new Ext.ux.grid.RowEditor({
		saveText: EasySDI_Mon.lang.getLocal('grid action update'),
		cancelText: EasySDI_Mon.lang.getLocal('grid action cancel'),
		clicksToEdit: 2
	});
	
	/**
	 * Editor for holiday row
	 */
	var editorHoliday = new Ext.ux.grid.RowEditor({
		saveText: EasySDI_Mon.lang.getLocal('grid action update'),
		cancelText: EasySDI_Mon.lang.getLocal('grid action cancel'),
		clicksToEdit: 2
	});
	
	/**
	 * ColumnModel for sla
	 */
	var cmSla = new Ext.grid.ColumnModel([
	    {
		header:EasySDI_Mon.lang.getLocal('grid header name'),
		dataIndex:"name",
		width:100,
		sortable: true,
		editable:true,
		editor: {
			xtype: 'textfield',
			allowBlank: false,
			vtype: 'alphanum'
			}
		},{
			header:EasySDI_Mon.lang.getLocal('grid header inspire'),
			dataIndex:"isExcludeWorst",
			width:100,
			renderer: EasySDI_Mon.TrueFalseRenderer,
			editor: {
				xtype: 'checkbox'
		}
	},
	{
		header:EasySDI_Mon.lang.getLocal('grid header measureTime'),
		dataIndex:"isMeasureTimeToFirst",
		width:120,
		renderer: EasySDI_Mon.TrueFalseRenderer,
		editor: {
			xtype: 'checkbox'
		}
	}
	]);
	
	/**
	 * Grid for sla
	 */
	var _slaGrid = new Ext.grid.GridPanel({
		id:'SlaGrid',
		loadMask:true,
		region:'center',
		plugins: [editor],
		stripeRows: true,
		tbar: [{
			iconCls:'icon-service-add',
			text: EasySDI_Mon.lang.getLocal('grid action add'),
			handler: onAdd
		},'-',{
			iconCls:'icon-service-rem',
			ref: '../removeBtn',
			text: EasySDI_Mon.lang.getLocal('grid action rem'),
			disabled: true,
			handler: onDelete
		},'-',{
			text: 'Add/edit periods',
			ref: '../editPeriodBtn',
			disabled: true,
			handler: onEditPeriods
		},'-',{
			text: 'Holidays',
			handler: onEditHolidays
		}
		],
		title:EasySDI_Mon.lang.getLocal('sla list'),
		store:slaStore,
		cm:cmSla,
		sm: new Ext.grid.RowSelectionModel({
			singleSelect: true,
			listeners: {
				rowselect: function(sm, row, rec) {
				}
			}
		})
	});
	
	/**
	 * onAdd for sla grid
	 */
	function onAdd(btn, ev) {

		//create default record
		var u = new _slaGrid.store.recordType(EasySDI_Mon.DefaultSla);

		var win = new  Ext.Window({
			width:450,
                        height: 300,
			autoScroll:true,
			modal:true,
                        layout: 'fit',
			title:EasySDI_Mon.lang.getLocal('title new sla'),
			items: [
			        new Ext.FormPanel({
			        	monitorValid:true,
			        	ref: 'slaPanel',
			        	region:'center',
			        	bodyStyle:'padding:5px 5px 0;',
                                        layout: 'fit',
                                        padding: 5,
			        	frame:false,
                                        border: false,
                                        layout: 'fit',
                                        labelWidth:1,
			        	items: 
                                        [{
                                            xtype: 'fieldset',
                                            title: EasySDI_Mon.lang.getLocal('field slaname title'),
                                            autoHeight: true,
                                            items:[{
                                                    xtype: 'textfield',
                                                    value: u.data['name'],
                                                    name: 'name',
                                                    width: 280,
                                                    allowBlank:false,
                                                    vtype: 'slaname' // validate name
                                            }]
			        	},
			        	{
                                            layout:'column',
                                            
                                            border: false,
                                            frame: false,      
                                            items:
                                            [
                                                {
                                                    xtype: 'fieldset',
                                                    columnWidth: .5,
                                                    title: EasySDI_Mon.lang.getLocal('field worstexclude title'),
                                                    items: 
                                                    [{
                                                        boxLabel: EasySDI_Mon.lang.getLocal('grid header excludeWorst'),
                                                                            fieldLabel: '',
                                                                            labelSeparator: '',
                                                        xtype: 'checkbox',
                                                        name: 'isExcludeWorst',
                                                        checked: u.data['isExcludeWorst']
                                                     }]
                                                },
                                                {   
                                                    xtype: 'fieldset',
                                                    columnWidth: .5,
                                                    title: EasySDI_Mon.lang.getLocal('field slameasure title'),
                                                    defaultType: 'radio',
                                                    border: 0,
                                                    items: 
                                                    [{
                                                        name: 'isMeasureTimeToFirst',
                                                        boxLabel: EasySDI_Mon.lang.getLocal('sla firstbyte boxlabel'),
                                                        checked: u.data['isMeasureTimeToFirst'],
                                                        fieldLabel: '',
                                                        labelSeparator: '',
                                                        xtype: 'radio'
                                                    },
                                                    {
                                                        name: 'isMeasureTimeToFirst',
                                                        boxLabel: EasySDI_Mon.lang.getLocal('sla lastbyte boxlabel'),
                                                        checked: true,
                                                        fieldLabel: '',
                                                        xtype: 'radio',
                                                        labelSeparator: ''
                                                    }]

                                                }]
			        	}
			        	],
			        	buttons: [{
			        		text: EasySDI_Mon.lang.getLocal('grid action ok'),
			        		//If validation fails disable the button
			        		formBind:true,
			        		handler: function(){
			        		editor.stopEditing();
			        		var fields = win.slaPanel.getForm().getFieldValues();
			        		var u = new slaStore.recordType({name:'','isExcludeWorst': '0','isMeasureTimeToFirst':'0'});
    	        			
			        		u.set('name', fields.name);
			        		u.set('isExcludeWorst', fields.isExcludeWorst);
			        		if(fields.isMeasureTimeToFirst[0])
			        		{
			        			u.set('isMeasureTimeToFirst', true);
			        		}else
			        		{
			        			u.set('isMeasureTimeToFirst', false);
			        		}
			        		_slaGrid.store.insert(0, u);
			        		win.close();
			        	}
			        	},{
			        		text: EasySDI_Mon.lang.getLocal('grid action cancel'),
			        		handler: function(){
			        		win.close();
			        	}
			        	}]
			        })
			        ]
		});
		win.show();
	}
	
	/**
	 * onDelete for sla grid
	 */
	function onDelete(btn, ev) {
		var rec = _slaGrid.getSelectionModel().getSelected();
		if (!rec) {
			return false;
		}
		Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), String.format(EasySDI_Mon.lang.getLocal('confirm suppress sla'), rec.get('name')), function(btn){
			if (btn == 'no')
			{
				return false;
			}
			else
			{
				_slaGrid.store.remove(rec);
			}
		});
	}
	
	/**
	 * Select event handler for sla grid row
	 */
	_slaGrid.getSelectionModel().on('selectionchange', function(sm){
		_slaGrid.removeBtn.setDisabled(sm.getCount() < 1);
		_slaGrid.editPeriodBtn.setDisabled(sm.getCount() < 1);
	});
	
	/**
	 * Click event for editPeriods
	 */
	function onEditPeriods(btn, ev) 
	{
		var rec = _slaGrid.getSelectionModel().getSelected();
		if(rec)
		{
			// Set proxy URL with select SLA
			periodStore.proxy.api.create.url = EasySDI_Mon.proxy+'sla/'+rec.id+'/period';
			periodStore.proxy.api.destroy.url = EasySDI_Mon.proxy+'sla/'+rec.id+'/period';
			periodStore.proxy.api.update.url = EasySDI_Mon.proxy+'sla/'+rec.id+'/period';
			periodStore.proxy.api.read.url = EasySDI_Mon.proxy+'sla/'+rec.id+'/period';
			periodStore.load();
		}
	}
	
	/**
	 * Click event for Holidays 
	 */
	function onEditHolidays()
	{
		// Load the holidayStore
		holidayStore.load();
	}
	
	function formatDate(value){
        return value ? value.dateFormat('Y-m-d') : '';
    }
	
	/**
	 * ColumnModel for holiday
	 */
	var cmHoliday = new Ext.grid.ColumnModel([
    {
		header:EasySDI_Mon.lang.getLocal('grid header name'),
		dataIndex:"name",
		width:100,
		sortable: true,
		editable:true,
		editor: {
			xtype: 'textfield',
			allowBlank: true
		}
	},{
		header:EasySDI_Mon.lang.getLocal('grid header holidydate'),
		dataIndex:"date",
		sortable: true,
		width:120,
		renderer: formatDate,
		editor: {
			id: 'holidayDatePicker',
			xtype: 'datefield',
			name: 'date',
			allowBlank: false,
			format: 'Y-m-d'
			}
		}
  	]);
	
	/**
	 * Load event for holiday store 
	 */
	holidayStore.on('load', function() 
	{
		/**
		 * Grid for holidays
		 */
		var _holidayGrid = new Ext.grid.GridPanel({
			id:'HolidayGrid',
			loadMask:true,
			height: 200,
			region:'center',
			plugins: [editorHoliday],
			stripeRows: true,
			tbar: [{
				iconCls:'icon-service-add',
				text: EasySDI_Mon.lang.getLocal('grid action add'),
				handler: onAddHoliday
			},'-',{
				iconCls:'icon-service-rem',
				ref: '../removeBtnHoliday',
				text: EasySDI_Mon.lang.getLocal('grid action rem'),
				disabled: true,
				handler: onDeleteHoliday
			}
			],
			store:holidayStore,
			cm:cmHoliday,
			sm: new Ext.grid.RowSelectionModel({
				singleSelect: true,
				listeners: {
					rowselect: function(sm, row, rec) {
					}
				}
			})
		});
		
		var newHolidayPanel = new Ext.FormPanel({
			id: 'holidayPanel',
			hidden: true,
			monitorValid:true,
			region:'center',
			autoHeight:true,
			frame:true,
			items:[{
			       layout: 'form',
			       items:[
			       {
			    	   fieldLabel: EasySDI_Mon.lang.getLocal('holiday textfield label name'),
			    	   id: 'holidayName',
			    	   allowBlank:true,
			    	   xtype: 'textfield',
			    	   name: 'name',
			    	   width: 120
			       },
			       {
			    	   fieldLabel: EasySDI_Mon.lang.getLocal('holiday datepicker label date'),
			    	   id: 'holidayDatePicker',
			    	   xtype: 'datefield',
			    	   name: 'date',
			    	   width: 120,
			    	   format: 'Y-m-d H:i:s'
			       }],
			       buttons: [{
		        		text: EasySDI_Mon.lang.getLocal('grid action ok'),
		        		formBind:true,
		        		handler: function(){
		        			editorHoliday.stopEditing();
		        			var fields = Ext.getCmp('holidayPanel').getForm().getFieldValues();
		        			var u = new holidayStore.recordType({name:'','date': '2000-01-01'});
		        			u.set('name', fields.name);
		        			u.set('date', fields.date);
		        			_holidayGrid.store.insert(0, u);
		        			// Remove add form
		        			Ext.getCmp('holidayPanel').setVisible(false);
		        			Ext.getCmp('holidayPanel').doLayout();
		        		}
		        	},{
		        		text: EasySDI_Mon.lang.getLocal('grid action cancel'),
		        		handler: function(){
		        			Ext.getCmp('holidayPanel').setVisible(false);
		        			Ext.getCmp('holidayPanel').doLayout();
		        		}
		        	}]
			}]
		});
		
		/**
		 * Select eventhandler for holiday grid row
		 */
		_holidayGrid.getSelectionModel().on('selectionchange', function(sm){
			_holidayGrid.removeBtnHoliday.setDisabled(sm.getCount() < 1);
		});
		
		function onAddHoliday()
		{
			Ext.getCmp('holidayPanel').setVisible(true);
			Ext.getCmp('holidayPanel').doLayout();
		}
		
		/**
		 * Event for deleting a holiday
		 */
		function onDeleteHoliday()
		{
			var rec = _holidayGrid.getSelectionModel().getSelected();
			if (!rec) {
				return false;
			}
			Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), String.format(EasySDI_Mon.lang.getLocal('confirm suppress holiday'), rec.get('name')), function(btn){
				if (btn == 'no')
				{
					return false;
				}
				else
				{
					_holidayGrid.store.remove(rec);
				}
			});
		}
			
		var win = new  Ext.Window({
			width:400,
			autoScroll:true,
			modal:true,
			shadow: false,
			title:EasySDI_Mon.lang.getLocal('title new holiday'),
        	items: [_holidayGrid,newHolidayPanel]
		});
		
		win.show();
	});
	
	/**
	 * Load event for period store
	 */
	periodStore.on('load', function()
	{
		// Default period values
		var m = new _slaGrid.store.recordType(EasySDI_Mon.DefaultPeriod);
		
		var cmPeriod = new Ext.grid.ColumnModel([
	    {
	  		dataIndex:"name",
	  		width:200,
	  		sortable:false,
	  		editable:false
	  	}]);
		
		var topPeriodPanel = new Ext.FormPanel({
			id: 'topPeridPanel',
			monitorValid:true,
			region:'center',
			autoHeight:true,
			frame:true,
			items: [{
				xtype: 'fieldset',
				title: EasySDI_Mon.lang.getLocal('field period title'),
				autoHeight: true,
				anchor: '95%',
				items:[{
					layout:'column',
					items:[
					{
						columnWidth:.75,
						layout: 'form',
						items:[{
							xtype: 'grid',
							id: 'periodGrid',
							loadMast: true,
							height: 100,
							hideHeaders:true,
							autoScroll:true,
							width: 240,
							region: 'left',
							frame : true,
							stripeRows: true,
							store:periodStore,
							cm:cmPeriod,
							sm: new Ext.grid.RowSelectionModel({
							singleSelect: true,
							listeners: {
								rowselect: function(sm, row, rec) {
									var periodform = Ext.getCmp('periodmodifyPanel').getForm();
									periodform.setValues({name: rec.get('name')});
									periodform.setValues({isMonday: rec.get('isMonday')});
									periodform.setValues({isTuesday: rec.get('isTuesday')});
									periodform.setValues({isWednesday: rec.get('isWednesday')});
									periodform.setValues({isThursday: rec.get('isThursday')});
									periodform.setValues({isFriday: rec.get('isFriday')});
									periodform.setValues({isSaturday: rec.get('isSaturday')});
									periodform.setValues({isSunday: rec.get('isSunday')});
									periodform.setValues({isHolidays: rec.get('isHolidays')});
									periodform.setValues({slaStartTime: rec.get('slaStartTime')});
									periodform.setValues({slaEndTime: rec.get('slaEndTime')});
									periodform.setValues({date: rec.get('date')});
									Ext.getCmp('checkInclude').setValue(rec.get('isInclude'));
									Ext.getCmp('checkExclude').setValue(!rec.get('isInclude'));
									// Check if period is a date or general rule
									if(rec.get('date') && rec.get('date') != "")
									{
										Ext.getCmp('specificdate').setValue(true);
									}else
									{
										Ext.getCmp('generalrule').setValue(true);
									}
								}
							}
							})
						}]
					},
					{
						columnWidth:.25,
						layout: 'form',
						items:[{
							id: 'editperiodBtn',
							xtype: 'button',
							disabled: true,
							text: EasySDI_Mon.lang.getLocal('button period edit'),
							width: 80,
							handler: function(){
								periodeditmode = true;
								Ext.getCmp('periodmodifyPanel').setVisible(true);
								// handle layout for form
								Ext.getCmp('periodmodifyPanel').doLayout();
							},
							style: 'padding:4px'
						},
						{
							xtype: 'button',
							text: EasySDI_Mon.lang.getLocal('button period add'),
							width: 80,
							handler: function(){
								// Clear select rows
								Ext.getCmp('periodGrid').getSelectionModel().clearSelections();
								periodeditmode = false;
								Ext.getCmp('periodmodifyPanel').getForm().reset();
								Ext.getCmp('periodmodifyPanel').setVisible(true);
								// handle layout for form
								Ext.getCmp('periodmodifyPanel').doLayout();
								
							},
							style: 'padding:4px'	
						}
						,{
							id:'deleteperiodbtn',
							xtype: 'button',
							disabled: true,
							text: EasySDI_Mon.lang.getLocal('button period delete'),
							width: 80,
							handler: function(){
								var rec = Ext.getCmp('periodGrid').getSelectionModel().getSelected();
								if (!rec) {
									return false;
								}
								Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), String.format(EasySDI_Mon.lang.getLocal('confirm delete period'), rec.get('name')), function(btn){
									if (btn == 'no')
									{
										return false;
									}
									else
									{
										Ext.getCmp('periodmodifyPanel').getForm().reset();
										Ext.getCmp('periodmodifyPanel').setVisible(false);
										Ext.getCmp('periodmodifyPanel').doLayout();
										Ext.getCmp('periodGrid').store.remove(rec);
										Ext.getCmp('periodGrid').store.save();// Auto save no used
									}
								});
							},
							style: 'padding:4px'	
						}
						]
					}
					]
    			}]
			}]
		});
		
		var periodEditPanel = new Ext.FormPanel({
			id: 'periodmodifyPanel',
			monitorValid:true,
			labelWidth: 1,
			region:'center',
			frame:true,
			hidden: true,
			ref: 'periodPanel',
			autoHeight:true,
			items:[{
        			xtype: 'fieldset',
        			title: EasySDI_Mon.lang.getLocal('field editperiod name title'),
        			autoHeight: true,
        			anchor: '95%',
        			items:[{
        				id: 'periodname',
        				xtype: 'textfield',
		        		value: m.data['name'],
		        		name: 'name',
		        		width: '90%',
		        		allowBlank:false
        			}]
    			},
    			{
    				xtype: 'fieldset',
        			title: EasySDI_Mon.lang.getLocal('field editperiod include/exclude title'),
        			items:[{
						layout:'column',
						style: 'padding-left:100px',
						width: 250,
        				items:[{
							columnWidth:.5,
							xtype: 'radio',
							id:'checkInclude',
							name: 'isInclude',
							boxLabel: EasySDI_Mon.lang.getLocal('period isInclude boxlabel'),
							labelSeparator: '',
							checked: m.data['isInclude']
        					},{
							columnWidth:.5,
							xtype: 'radio',
							id:'checkExclude',
							name: 'isInclude',
							boxLabel: EasySDI_Mon.lang.getLocal('period isExclude boxlabel'),
							labelSeparator: '',
							checked: !m.data['isInclude']
        					}]
        			}]		
    			},
    			{
    				xtype: 'fieldset',
    				title: EasySDI_Mon.lang.getLocal('field period date'),
    					items:[{
    						layout:'column',
    						items:[
    						{
    							columnWidth:.5,
    							layout: 'form',
    							items:[{
    								id:'specificdate',
    								xtype: 'radio',
    								name: 'datetype',
    								boxLabel: EasySDI_Mon.lang.getLocal('period specifie date boxlabel'),
    								labelSeparator: '',
    								checked: false,
    								handler: function(control)
    								{
    									if(control.checked)
    									{
    										Ext.getCmp('periodDatePicker').setDisabled(false);
    									}
    								}
    							}]
    						},
    						{
    							columnWidth:.5,
    							layout: 'form',
    							items:[{
    								   fieldLabel: '', 
    						    	   id: 'periodDatePicker',
    						    	   disabled: true,
    						    	   xtype: 'datefield',
    						    	   name: 'date',
    						    	   width: 100,
    						    	   format: 'Y-m-d',
    						    	   value: new Date() // current date
    							}]
    						}]
    					},
    					{
    						id: 'generalrule',
    						xtype: 'radio',
    						name: 'datetype',
							boxLabel: EasySDI_Mon.lang.getLocal('period general rule boxlabel'),
							labelSeparator: '',
							checked: true,
							handler: function(control)
							{
								if(control.checked)
								{
									Ext.getCmp('periodDatePicker').setDisabled(true);
								}
							}
    					},
    					{
							layout:'column',
							style: 'padding-left:40px',
							width: 350,
							items:[
	        				{
								id: 'mondayCheck',
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period monday boxlabel'),
	        					name: 'isMonday',
								columnWidth:.2
	        				},
	        				{
								id: 'tuesdayCheck',
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period tuesday boxlabel'),
	        					name: 'isTuesday',
								columnWidth:.2
								
	        				},
	        				{
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period wednesday boxlabel'),
	        					name: 'isWednesday',
								columnWidth:.2
	        				},
	        				{
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period thursday boxlabel'),
	        					name: 'isThursday',
								columnWidth:.2
	        				},
	        				{
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period friday boxlabel'),
	        					name: 'isFriday',
								columnWidth:.2
	        				}
	        				]
    					},
    					{
    						layout:'column',
    						style: 'padding-left:120px',
    						width: 240,
							items:[
	        				{
								columnWidth:.5,
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period saturday boxlabel'),
	        					name: 'isSaturday'
	        				},
	        				{
								columnWidth:.5,
								xtype: 'checkbox',
	        					boxLabel: EasySDI_Mon.lang.getLocal('period sunday boxlabel'),
	        					name: 'isSunday'
							}]
    					},
    					{
							xtype: 'checkbox',
							boxLabel: EasySDI_Mon.lang.getLocal('period holiday boxlabel'),
							name: 'isHolidays',
							labelStyle:'padding-left:140px'  					
    					}
    					]
    			},
    			{
					xtype: 'fieldset',
        			title: EasySDI_Mon.lang.getLocal('period timerinterval'),
        			autoHeight: true,
					items:[{
						layout:'column',
						style: 'padding-left:40px',
						width: 300,
						items:[{
							columnWidth:.5,
							layout: 'form',
							items:[{
								id: 'slatimeStart',
								xtype:'timefield',
								fieldLabel: EasySDI_Mon.lang.getLocal('period slatime start'),
								name: 'slaStartTime',
								minValue: '00:00:00',
								maxValue: '23:00:00',
								value: '00:00:00',
								increment: 60,
								format: 'H:i:s',
								labelStyle: 'width:30px',
								width: 80,
								allowBlank:false
							}]
						},
						{
							columnWidth:.5,
							layout: 'form',
							items:[{
								id: 'slatimeEnd',
								xtype:'timefield',
								fieldLabel: EasySDI_Mon.lang.getLocal('period slatime end'),
								name: 'slaEndTime',
								minValue: '00:00:00',
								maxValue: '23:00:00',
								value: '00:00:00',
								increment: 60,
								format: 'H:i:s',
								labelStyle: 'width:20px',
								width: 80,
								allowBlank:false
							}]
						}]
					}]		
				},
				{
					layout:'column',
					style: 'padding-left:100px',
					width: 250,
					items:[{
						columnWidth:.5,
						layout: 'form',
						items:[{
							xtype: 'button',
							width: 70,
							text: EasySDI_Mon.lang.getLocal('period save btn'),
							formBind:true,
							handler: function(){
								var fields = Ext.getCmp('periodmodifyPanel').getForm().getFieldValues();
								if(fields)
								{
									// Check time
									var minSlatime = fields.slaStartTime;
									var maxSlatime = fields.slaEndTime;
									if(maxSlatime == "" || minSlatime == "")
									{
										Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('period sla timepicket'));
										return false;
									}
									// Check date
									var dateStr = "";
									if(fields.datetype[0])
									{
										if(fields.date == "")
										{
											Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('period date empty error'));
											return false;
										}
										try
										{
											var dt = new Date();
											dt = fields.date;
											dateStr = dt.format('Y-m-d')
										}catch(e)
										{
											Ext.MessageBox.alert(EasySDI_Mon.lang.getLocal('error'), EasySDI_Mon.lang.getLocal('period date format error'));
											return false;
										}
									}

									if(periodeditmode)
									{
										var rec = Ext.getCmp('periodGrid').getSelectionModel().getSelected();
										if (!rec) {
											return false;
										}
									
										
										rec.beginEdit();
										rec.set('name',fields.name);
										rec.set('slaStartTime', fields.slaStartTime);
										rec.set('slaEndTime', fields.slaEndTime);
										rec.set('isInclude', fields.isInclude[0]);
										if(fields.datetype[0])
										{
											rec.set('isMonday', '0');
											rec.set('isTuesday', '0');
											rec.set('isWednesday', '0');
											rec.set('isThursday', '0');
											rec.set('isFriday', '0');
											rec.set('isSaturday', '0');
											rec.set('isSunday', '0');
											rec.set('isHolidays', '0');
											rec.set('date',dateStr);
										
										}else
										{
											rec.set('isMonday', fields.isMonday);
											rec.set('isTuesday', fields.isTuesday);
											rec.set('isWednesday', fields.isWednesday);
											rec.set('isThursday', fields.isThursday);
											rec.set('isFriday', fields.isFriday);
											rec.set('isSaturday', fields.isSaturday);
											rec.set('isSunday', fields.isSunday);
											rec.set('isHolidays', fields.isHolidays);
											rec.set('date', '');
										}
										rec.endEdit();
										Ext.getCmp('periodGrid').store.save(); // Auto save not used
									}else
									{
										// Create new period
										var u = new periodStore.recordType({name:'','isMonday':'0','isTuesday':'0','isWednesday':'0',
										'isThursday':'0','isFriday':'0','isSaturday':'0','isSunday':'0','isHolidays':'0',
										'slaStartTime':'00:00:00','slaEndTime':'00:00:00','isInclude':'1','date':''})				
																				
										u.set('name', fields.name);
										u.set('isInclude', fields.isInclude[0]);
										u.set('slaStartTime', fields.slaStartTime);
										u.set('slaEndTime', fields.slaEndTime);
										// Use specific date
										if(fields.datetype[0])
										{
											u.set('date', dateStr);
										}else
										{
											u.set('isMonday', fields.isMonday);
											u.set('isTuesday', fields.isTuesday);
											u.set('isWednesday', fields.isWednesday);
											u.set('isThursday', fields.isThursday);
											u.set('isFriday', fields.isFriday);
											u.set('isSaturday', fields.isSaturday);
											u.set('isSunday', fields.isSunday);
											u.set('isHolidays', fields.isHolidays);
											u.set('date', '');	
										}
										Ext.getCmp('periodGrid').store.insert(0, u);
										Ext.getCmp('periodGrid').store.save(); // Auto save not used
									}
									// Hidden panel again
									Ext.getCmp('periodmodifyPanel').setVisible(false);
									Ext.getCmp('periodmodifyPanel').doLayout();
								}
							}
						}]
					},
					{
						columnWidth:.5,
						layout: 'form',
						items:[{
							xtype: 'button',
							width: 70,
							text: EasySDI_Mon.lang.getLocal('period cancel btn'),
							handler: function(){
								Ext.getCmp('periodmodifyPanel').setVisible(false);
								Ext.getCmp('periodmodifyPanel').doLayout();
							}
						}]
					}]
				}
    		]
		});
		
		var btnClose = new Ext.Button({
			text: EasySDI_Mon.lang.getLocal('period close btn'),
			width: 70,
			style:'float:right;',
			handler: function(){
				win.close();
			}
		});
		
		var win = new  Ext.Window({
			width:400,
			autoScroll:true,
			shadow: false,
			modal:true,
			title:EasySDI_Mon.lang.getLocal('title period window'),
			items: [topPeriodPanel,periodEditPanel,btnClose]
		});
		
		// Selectchange eventhandler for period grid
		Ext.getCmp('periodGrid').getSelectionModel().on('selectionchange', function(sm){
			Ext.getCmp('deleteperiodbtn').setDisabled(sm.getCount() < 1);
			Ext.getCmp('editperiodBtn').setDisabled(sm.getCount() < 1);
			if(sm.getCount() > 0)
			{
				periodeditmode = true;
			}
		});
		
		win.show();
	});
	

	/*
	 * Eventhandler for load of the sla store
	 * Fires event updatedSla to given message that sla have been updated
	 * */
	Ext.getCmp('SlaGrid').store.on('load', function() {
		EasySDI_Mon.SlaUpdateEvent.fireEvent('updatedSla');
	});
	
	/*
	 * Eventhandler for write of the sla store
	 * Fires event updatedSla to given message that sla have been updated
	 * */
	Ext.getCmp('SlaGrid').store.on('write', function() {
		EasySDI_Mon.SlaUpdateEvent.fireEvent('updatedSla');
	});
	
	// Load sla store
	slaStore.load();
});/**
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

});/**
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
		}
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
		listeners:{
			
			headerclick :function(grid, colnum){
				if(grid.colModel.config[colnum].sortable == true)
					this.getBottomToolbar().moveFirst();
			}
				
		},

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
			
		}]
	});

	_alertsGrid.cbJobs.on('select', function(cmb, rec) {

		//clear the store
		store.removeAll();
		
		if(rec.get('name') != 'All'){
		   loadAlertData(rec.get('name'), 1, 1, false);
		}else{
			 loadAlertData(null, 1, 1, true);

		}
	});



	function loadAlertData(jobName, current, total, getAll){
		var myMask = new Ext.LoadMask(Ext.getCmp('AlertGrid').getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
		myMask.show();
	
		proxyUrl ="";
		if((null == jobName)&&(getAll == true))			
			proxyUrl =  EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/all/alerts'+"&all=true";
		else if((null != jobName)&&(getAll == false))				
			proxyUrl = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+jobName+'/alerts';
		else{
			console.log("No job name provided");
			return false;
		}
		
				
		store.removeAll();
		var newStore = new Ext.data.JsonStore({
			
			//id: 'jobId',
			root: 'data',			
			proxy: new Ext.data.HttpProxy({
				
				url: proxyUrl
			}),
			restful:true,
			idProperty : 'id',
			totalProperty :'count',
			remoteSort : true,
			fields:['newStatusCode', 'oldStatusCode', 'cause', 'httpCode', 'responseDelay', 'isExposedToRss', 'jobId', {name: 'dateTime',  type: 'date', dateFormat: 'Y-m-d H:i:s'},'alertId','content_type'],
			sortInfo:{
				field : "dateTime",
				direction : "DESC"
			},
			listeners :{
				load: function(){

					store.removeAll();
					var aRec = this.getRange();
				
						for ( var j=0; j< aRec.length; j++ )
						{
							//feed the grid store with the collected alerts
							store.add(aRec[j]);
						}
					
				}
			}
		});
			
			component = Ext.getCmp('AlertGrid');
			var colModel = component.getColumnModel();
			component.reconfigure(newStore, colModel);
			
			if(component.getBottomToolbar()){
				component.getBottomToolbar().show();
				component.getBottomToolbar().bindStore(newStore);
				component.getBottomToolbar().doRefresh();
			}
			
			
			myMask.hide();		
				
		}
		
	}

);/**
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
	* Property: editmode
	* 
	**/
	var editmode = true;
	
	/**
	 * Function: findMainURL
	 * Detects url for public overview page.
	 */
	function findMainURL(name)
	{
		var mainURL = window.location.protocol + "//" + window.location.host;
		var pathArray = window.location.pathname.split( '/' );
		for (var i = 0; i < pathArray.length; i++ ) {
			if(pathArray[i] != "" && pathArray[i] != "/" )
			{
				if(pathArray[i].toLowerCase().indexOf("joomla") > -1)
				{
					mainURL += "/";
					mainURL += pathArray[i];
					break;
				}else
				{
					mainURL += "/";
					mainURL += pathArray[i];
				}
			}
		}
		mainURL += "/";
		mainURL += "overview.php?name="+name
		return mainURL;
	}

	
	// proxy api for overview pages
	var proxyOverview = new Ext.data.HttpProxy({
		   api: {
		        read    : EasySDI_Mon.proxy+'overviews',
		        create  : EasySDI_Mon.proxy+'overviews',
		        update  : EasySDI_Mon.proxy+'overviews',
		        destroy : EasySDI_Mon.proxy+'overviews'
		    }
	});
	
	// proxy api for last requests 
	var proxyTableOverview = new Ext.data.HttpProxy({
		api: {
		    read    : '?',
	        create  : '?',
	        update  : '?',
	        destroy : '?'
	    }
	});
	
	
	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	}); 
		
	/*Data Stores*/
	/**
	 * JsonStore: overviewPageStore
	 * For holding overview pages
	 */
	var overviewPageStore = new Ext.data.JsonStore({
		id:'name',
		root: 'data',
		restful:true,
		fields:['name','isPublic'],
		proxy: proxyOverview,
		writer: writer
	});
	
	/**
	 * SimpleStore: pageComboStore
	 * Used for overviewpages in combobox
	 * */
	var pageComboStore = new Ext.data.SimpleStore({
		id:0,
		fields:['name'],
		data:[]
	});
	
	// Store for request table data
	var overviewTableStore = new Ext.data.JsonStore({
		id: 'queryId',
		root: 'data',
		restful:true,
		proxy: proxyTableOverview,
		writer: writer,
		fields:[{name: 'overviewId'},{name:'queryId'},{name:'isPublic'},
				{name:'query',mapping:'query'},{name:'id', mapping:'query.id'},
				{name:'name', mapping:'query.name'},{name:'status', mapping:'query.status'},
				{name:'statusCode', mapping:'query.statusCode'},{name:'serviceMethod', mapping:'query.serviceMethod'},
				{name: 'queryValidationResult',mapping: 'query.queryValidationResult'},{name: 'id',mapping: 'query.queryValidationResult.id'},
				{name: 'sizeValidationResult',mapping: 'query.queryValidationResult.sizeValidationResult'},{name: 'responseSize',mapping: 'query.queryValidationResult.responseSize'},
				{name: 'timeValidationResult',mapping: 'query.queryValidationResult.timeValidationResult'},{name: 'deliveryTime',mapping: 'query.queryValidationResult.deliveryTime'},
				{name: 'xpathValidationResult',mapping: 'query.queryValidationResult.xpathValidationResult'},{name: 'xpathValidationOutput',mapping: 'query.queryValidationResult.xpathValidationOutput'},
				{name: 'queryValidationSettings',mapping: 'query.queryValidationSettings'},{name: 'id',mapping: 'query.queryValidationSettings.id'},
				{name: 'useSizeValidation',mapping: 'query.queryValidationSettings.useSizeValidation'},{name: 'normSize',mapping: 'query.queryValidationSettings.normSize'},
				{name: 'normSizeTolerance',mapping: 'query.queryValidationSettings.normSizeTolerance'},{name: 'useTimeValidation',mapping: 'query.queryValidationSettings.useTimeValidation'},
				{name: 'normTime',mapping: 'query.queryValidationSettings.normTime'},{name: 'useXpathValidation',mapping: 'query.queryValidationSettings.useXpathValidation'},
				{name: 'xpathExpression',mapping: 'query.queryValidationSettings.xpathExpression'},{name: 'expectedXpathOutput',mapping: 'query.queryValidationSettings.expectedXpathOutput'},	
				{name:'lastQueryResult',mapping:'lastQueryResult'},
				/*{name:'picture_url',mapping:'lastQueryResult.picture_url'},
				{name:'xml_result',mapping:'lastQueryResult.xml_result'},*/{name:'text_result',mapping:'lastQueryResult.text_result'},
				{name: 'content_type',mapping:'lastQueryResult.content_type'}
			]
	});
	
	var controlsORP = {
			xtype: 'fieldset',
			id: 'overviewFs',
			region: 'north',
			height: 80,
			layout:'table',
			layoutConfig:{columns:6},
			defaults: {
				bodyStyle:'padding:5px'
			}, 
			collapsible:false,
			title: '',
			items:[
			{
					items:[{
						xtype:          'combo',
						mode:           'local',
						id:             'overviewcomboOVP',
						triggerAction:  'all',
						forceSelection: true,
						editable:       false,
						name:           'overviewComboFilter',
						displayField:   'name',
						valueField:     'name',
						emptyText: EasySDI_Mon.lang.getLocal('combo select a overviewpage'),
						store:pageComboStore
					}]
			
			},
			{
				id: 'modeswitch',
				xtype: 'button',
				text: EasySDI_Mon.lang.getLocal('overview switch mode view'),
				disabled: false,
				handler: function()
				{
					var btn = Ext.getCmp('modeswitch');
					if(editmode)
					{
						editmode = false;
						btn.setText(EasySDI_Mon.lang.getLocal('overview switch mode edit'));
					}else
					{
						editmode = true;
						btn.setText(EasySDI_Mon.lang.getLocal('overview switch mode view'));
					}
					var pagename = Ext.getCmp('overviewcomboOVP').getValue();
					if(pagename && pagename != "")
					{
						overviewTableStore.proxy.api.read.url = EasySDI_Mon.proxy+'overviews/'+pagename+'/queries';
						overviewTableStore.load();
					}
				}
			},       
			{
				id: 'newbtnResponsePage',
				xtype:'button',
				text: 'New',
				disabled:false,
				handler: function(){
					//Open a window for entering create a new overview response page
					var win = new  Ext.Window({
						width:200,
						autoScroll:false,
						modal:true,
						title: EasySDI_Mon.lang.getLocal('overview panel title'),
						items: [ 
						        new Ext.FormPanel({
						        	labelWidth: 35, // label settings here cascade unless overridden
						        	monitorValid:true,
						        	ref: 'overviewpagePanel',
						        	region:'center',
						        	bodyStyle:'padding:5px 5px 0',
						        	autoHeight:true,
						        	frame:true,
						        	defaults: {width: 200},
						        	defaultType: 'textfield',
						        	autoHeight:true,
						        	items: [
					        	        {
					        	        	fieldLabel: EasySDI_Mon.lang.getLocal('overview label pagename'),
					        	        	value: '',
					        	        	name: 'name',
					        	        	allowBlank:false,
					        	        	xtype: 'textfield',
					        	        	width: 120
					        	        }
					        	        ],
					        	        buttons: [{
					        	        	text: EasySDI_Mon.lang.getLocal('grid action ok'),
					        	        	handler: function()
					        	        	{
					        	        		var fields = win.overviewpagePanel.getForm().getFieldValues();
					        	        		var index = overviewPageStore.find('name',fields.name);  
					        	        		// ONLY CREATED IF NAME DOES NOT EXIST 
					        	        		if(index < 0)
					        	        		{
					        	        			var u = new overviewPageStore.recordType({name:'',isPublic:'0'});
					        	        			u.set('name', fields.name);
					        	        			u.set('isPublic', '0');
					        	        			overviewPageStore.insert(0, u);  	    			 
					        	        			win.close();
					        	        		}else
					        	        		{
					        	        			// TODO Language
					        	        			alert('The pagename already exist: '+fields.name);
					        	        		}
					        	        	}
					        	        },{
					        	        	text: EasySDI_Mon.lang.getLocal('grid action cancel'),
					        	        	handler: function(){
					        	        		win.close();
					        	        	}
							        	}]
							        })
							        ]
						});
					// open add Window
					win.show();	
				}
				
			},
			{
				id: 'delbtnResponsePage',
				xtype:'button',
				text: 'Delete',
				disabled:false,
				handler: function(){
					if(confirm('Do you want to delete: '+Ext.getCmp('overviewcomboOVP').getValue()))
					{				
						var name = Ext.getCmp('overviewcomboOVP').getValue();
						if(name != '')
						{
							// Delete URL
							var index = -1;
							index = overviewPageStore.find('name',name);
							if(index > -1)
							{
								try
								{
									overviewPageStore.remove(overviewPageStore.getAt(index));
									refreshComboValuesFromOverviewStore();
									resetPage();
								}catch(e)
								{
									alert(e);
								}
							}				    
						}	
					}
				}
			},
			{
				html:'',
				colspan:2
			},
			{	
				items:[
					new Ext.FormPanel({
					layout:'table',
					layoutConfig:{columns:2},
					defaults: {
					bodyStyle:'padding:5px'
					}, 
					items:[
					{
						xtype: 'checkbox',
						id: 'checkpublic',
						checked: false,
						hidden: true,
						handler: function(){
							var alink = document.getElementById("apublicLink");
							var name = Ext.getCmp('overviewcomboOVP').getValue();
							var index = overviewPageStore.find('name',name);
							if(index > -1)
							{	
								var isPublicChecked = overviewPageStore.getAt(index).get('isPublic');
								if(Ext.getCmp('checkpublic').checked)
								{
									if(isPublicChecked == '0')
									{
										overviewPageStore.getAt(index).set('isPublic','1');
										alink.href = findMainURL(name);
										alink.innerHTML = findMainURL(name);
										toggleLink(true,true);
									}
								}else
								{
									if(isPublicChecked == '1'){
										overviewPageStore.getAt(index).set('isPublic','0');
										toggleLink(true,true);
									}
								}
							}
						}
				  },
				  {			  
						hidden: true,
						id:'linkPublic',
						html: 'Public <a id="apublicLink" href="'+findMainURL('')+'" target="blank">'+findMainURL('')+'</a>'
				  }]
				  })
				],
				colspan:6
			}
			]
			
	}// end control
	
	// View and edit Table 
	var controlTable = 
	{
			xtype: 'panel',
			id: 'requestTable',
			region: 'center',
			height:350,
			autoScroll: true,
			hidden: true,
		//	layout:'table',
		//	layoutConfig:{columns:3},
			defaults: {
				bodyStyle:'padding:5px',
				//width:320,//280 
				height:300 //260
			}, 
			//collapsible:false,
			title: ''		
	};
	
	// Panel
	var responseoverviewPanel = new Ext.Panel({
		id:'ResponseOverviewPanel',
		region:'center',
		layout: 'border',
		border:true,
		frame:true,
		items: [
		        controlsORP,
		        controlTable
		]
	});
	
	// Default load
	overviewPageStore.load();
	
	function resetPage()
	{
		Ext.getCmp('overviewcomboOVP').clearValue();
		Ext.getCmp('requestTable').removeAll();;
		toggleLink(false,false);
	}
	
	function toggleLink(modeCheck,modeLink)
	{
		Ext.getCmp('checkpublic').setVisible(modeCheck);
		Ext.getCmp('linkPublic').setVisible(modeLink);
	}
	
	/*****
	 *
	 * Event Handlers 
	 *
	 *****/
	
	function refreshComboValuesFromOverviewStore(){
		var aRec = overviewPageStore.getRange();	
		// Clear list
		pageComboStore.removeAll();
		// add new list
		for ( var i=0; i< aRec.length; i++ )
		{
			var u = new pageComboStore.recordType({name:''});
			u.set('name', aRec[i].get('name'));
			pageComboStore.insert(0, u);
		}
	}
	
	function createView()
	{
		var name = Ext.getCmp('overviewcomboOVP').getValue();
		var index = overviewPageStore.find('name',name);
		if(index > -1)
		{
			var rec = overviewPageStore.getAt(index);
			var alink = document.getElementById("apublicLink");
			var name = Ext.getCmp('overviewcomboOVP').getValue();
			alink.href = findMainURL(name);
			alink.innerHTML = findMainURL(name);
			toggleLink(true,true);
	
			if(rec.get('isPublic') == 1)
			{			
				Ext.getCmp('checkpublic').setValue(true);
			}else
			{
				Ext.getCmp('checkpublic').setValue(false);
			}
		}else
		{
			toggleLink(false,false);
		}
		
		// Create tableview
		var table = Ext.getCmp('requestTable');
		try
		{
			// Remove all items from fieldSet
			table.removeAll();
			table.setVisible(true);
			// remove cells from table
			for (row = 0; row <= table.getLayout().currentRow; row++) {
				
				var tr = table.getLayout().getRow(row);
				while(tr.children.length > 0)
				{
					var child = tr.children[tr.children.length-1];
					tr.removeChild(child);
				}
			}
			table.layout.currentRow = 0;
			table.layout.currentColumn = 0;
			table.layout.cells = [];
			table.doLayout();
		}catch(e)
		{
		}
		
		// Get request data
		var aRec = overviewTableStore.getRange();
		for ( var i=0; i< aRec.length; i++ )
		{	
			var content_type = aRec[i].get('serviceMethod');
			var controls;
			if(editmode)
			{
				controls = createTable(aRec[i],content_type.toLowerCase());
				table.add(controls);
			}else
			{
				if( aRec[i].get('isPublic') == 1)
				{
					controls = createTable(aRec[i],content_type.toLowerCase());
					table.add(controls);
				}
			}
		}
		
		for(var i = 0;i < table.items.items.length;i++)
		{
			table.items.items[i].addClass('Div_overview_request');
		}
		
		table.doLayout();
	}
	
	function createTable(rec,type)
	{
		var tHeaderColor = 'OverviewTableCellsOk';
		var dTime = Math.round(rec.get('deliveryTime') * 1000);
		var nTime = rec.get('normTime');
		var tCellColor = 'OverviewTableCellsOk';
		if(!rec.get('useTimeValidation'))
		{
			tCellColor = 'OverviewTableCellsOk';
		}else if(!rec.get('timeValidationResult'))
		{
			tCellColor = 'OverviewTableCellsFailed';
		}
		
		var sSize = rec.get('responseSize');
		var nSize = rec.get('normSize');
		var sCellColor = 'OverviewTableCellsOk';
		if(!rec.get('useSizeValidation'))
		{
			sCellColor = 'OverviewTableCellsOk';
		}else if(!rec.get('sizeValidationResult')) 
		{
			sCellColor = 'OverviewTableCellsError';
		}
	    
		var oFound = rec.get('xpathValidationOutput');
		var oNorm = rec.get('normOutput'); 
		var oCellColor = 'OverviewTableCellsOk';
		if(!rec.get('useXpathValidation'))
		{
			oCellColor = 'OverviewTableCellsOk';
		}else if(!rec.get('xpathValidationResult'))
		{
			oCellColor = 'OverviewTableCellsError';
		}
		var reqError = false;
		if(dTime < 0 || rec.get('statusCode').toLowerCase() == 'unavailable')
		{
			reqError = true;
			tHeaderColor = 'OverviewTableCellsError';
			tCellColor = 'OverviewTableCellsError';
			sCellColor = 'OverviewTableCellsError';
			oCellColor = 'OverviewTableCellsError';
		}
		var dataurl = EasySDI_Mon.proxy+'image/lastoverview/'+rec.get('queryId')+'?contenttype='+rec.get('content_type');
		var controls;
		switch(type)
		{
		case "gettile":
		case "getmap":
			controls = {
				items: 
				[
			        new Ext.FormPanel(
			        {
			        	id: 'tablepagePanel',
			        	region:'north',
			        	layout:'table',
			        	layoutConfig:{columns:2},
					    defaults: {
					    },
					    items:
					    [
	        	       {
	        	    	   	xtype: 'label',
	        	        	value: '',
	        	        	name: 'service_label',
							text: EasySDI_Mon.lang.getLocal('overview label service name'),
							cls: 'OverviewLabel',
							cellCls: tHeaderColor
						},
	        	        {
	        	           	xtype: 'label',
	        	        	value: '',
	        	        	name: 'service_name',
							
							text: rec.get('name'),
							cellCls: tHeaderColor
	        	        },
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Size_label',
							text: EasySDI_Mon.lang.getLocal('overview label size'),
							cls: 'OverviewLabel',
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'size',
							text: rec.get('responseSize')+ EasySDI_Mon.lang.getLocal('overview label bytes'),
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'norm_label',
							text: EasySDI_Mon.lang.getLocal('overview label norm size'),
							cls: 'OverviewLabel',
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'norm',
							text: rec.get('normSize') ? rec.get('normSize') + EasySDI_Mon.lang.getLocal('overview label bytes'): '',
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Deliverytime_label',
							text: EasySDI_Mon.lang.getLocal('overview label delivery time'),
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Deliverytime',
							text:  Math.round(rec.get('deliveryTime') * 1000)+EasySDI_Mon.lang.getLocal('overview label ms'),
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: EasySDI_Mon.lang.getLocal('overview label norm time'),
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: rec.get('normTime') ? rec.get('normTime')+EasySDI_Mon.lang.getLocal('overview label ms'):'',
							cellCls: tCellColor
						},
						{
							width: 142,
							height: 142,
							html: rec.get('queryId') && !reqError ? '<img src='+dataurl+' alt="missing image" style="border:1px solid;" width="140" height="140" />': '<a href="'+dataurl+'" target="_blank">'+EasySDI_Mon.lang.getLocal('overview text datalink')+'</a>', //'<textarea class="Text_area_result" >'+rec.get('text_result')+'</textarea>',
							colspan: 2,
							cellCls: 'OverviewTableCellsImg'
						},
						{
							xtype: 'checkbox',
							id: 'check_'+rec.get('queryId'),
							inputValue: rec.get('queryId'),
							hidden: editmode ? false:true,
							checked: rec.get('isPublic') == 1? true: false,
							handler: function(com){
								var index = overviewTableStore.find('queryId',com.inputValue);
								if(index > -1)
								{						
									if(com.checked)
									{
										overviewTableStore.getAt(index).set('isPublic','1');
									}else
									{
										overviewTableStore.getAt(index).set('isPublic','0');
									}
								}
							},
	        	        	cellCls: 'OverviewTableCellsInludeBox'
						},
						{
							hidden: editmode ? false:true,
							html: EasySDI_Mon.lang.getLocal('overview label include'),
							cellCls: 'OverviewTableCellsInludeText'
						}
						]
			        })// End form panel
        		]     
			}// end control 
			break;
		case "getfeature":
		case "getrecords":
		case "getrecordbyid":
		case "getcapabilities":
		default: 	
			controls = {
				items: 
				[
				 	new Ext.FormPanel(
			        {
			        	id: 'tablepagePanel',
			        	region:'north',
			        	layout:'table',
			        	layoutConfig:{columns:2},
					    defaults: {
					    },
					    items:
					    [
	        	        {
	        	    	   	xtype: 'label',
	        	        	value: '',
	        	        	name: 'service_label',
							text: EasySDI_Mon.lang.getLocal('overview label service name'),
							cls: 'OverviewLabel',
							cellCls: tHeaderColor
						},
	        	        {
	        	           	xtype: 'label',
	        	        	value: '',
	        	        	name: 'service_name',
							text: rec.get('name'),
							cellCls: tHeaderColor
	        	        },
	        	        {
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'foundOutput_label',
							text: EasySDI_Mon.lang.getLocal('overview label foundoutput'),
							cls: 'OverviewLabel',
							cellCls: oCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'xpathValidationOutput',
							text: rec.get('xpathValidationOutput'),
							cellCls: oCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'norm_label',
							text: EasySDI_Mon.lang.getLocal('overview label normoutput'),
							cls: 'OverviewLabel',
							cellCls: oCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'expectedXpathOutput',
							text: rec.get('expectedXpathOutput'),
							cellCls: oCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Deliverytime_label',
							text: EasySDI_Mon.lang.getLocal('overview label delivery time'),
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Deliverytime',
							text: Math.round(rec.get('deliveryTime') * 1000)+EasySDI_Mon.lang.getLocal('overview label ms'),
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: EasySDI_Mon.lang.getLocal('overview label norm time'),
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: rec.get('normTime') ? rec.get('normTime') + EasySDI_Mon.lang.getLocal('overview label ms'): '',
							cellCls: tCellColor
						},
						{
							html: '<a href="'+dataurl+'" target="_blank">'+EasySDI_Mon.lang.getLocal('overview text datalink')+'</a>', //'<textarea class="Text_area_result" >'+rec.get('text_result')+'</textarea>',
							width: 300,
							height:30,
							colspan: 2,
							cellCls: 'OverviewTableCells'
						},
						{
							xtype: 'checkbox',
							id: 'check_'+rec.get('queryId'),
							inputValue: rec.get('queryId'),
							hidden: editmode ? false:true,
							checked: rec.get('isPublic') == 1? true: false,
							handler: function(com){
								var index = overviewTableStore.find('queryId',com.inputValue);
								if(index > -1)
								{
									if(com.checked)
									{
										overviewTableStore.getAt(index).set('isPublic','1');
									}else
									{
										overviewTableStore.getAt(index).set('isPublic','0');
									}								
								}
							},
	        	        	cellCls: 'OverviewTableCellsInludeBox'
						},
						{
							hidden: editmode ? false:true,
							html: EasySDI_Mon.lang.getLocal('overview label include'),
							cellCls: 'OverviewTableCellsInludeText'
						}
						]
			        })// End form panel
        		]     
			}// end control
		}// end switch		
		return controls;
	}
	
	/* Events */
	/**
	* Select of page in combox
	*/
	Ext.getCmp('overviewcomboOVP').on('select', function(cmb, rec){
		overviewTableStore.proxy.api.update.url = EasySDI_Mon.proxy+'overviews/'+rec.data.name+'/queries';
		overviewTableStore.proxy.api.read.url = EasySDI_Mon.proxy+'overviews/'+rec.data.name+'/queries';
		overviewTableStore.load();
	});
	
	overviewPageStore.on('load', function() {
		refreshComboValuesFromOverviewStore();
	});
	
	overviewPageStore.on('add',function(store, records, index )
	{
		refreshComboValuesFromOverviewStore();
	});
	
	// Table store events
	overviewTableStore.on('load', function()
	{
		createView();
	});
	
});/**
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


});/**
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
		root:'rows',
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


	var _jobDailyGrid = new Ext.grid.GridPanel({
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

	var dailyGridPanel = {
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
		root:'data',
		proxy: new Ext.data.HttpProxy({
			url: '?'
		}),
		fields:[
		        {id: 'id'},
		        {name: 'name'}
		        ],
		        data:{'data':[{name:'All',value:'All'}]}
	});


	var clFs = {
			xtype: 'fieldset',
			id: 'clearLogsFs',
			region: 'north',
			height: 140,
			layout:'table',
			layoutConfig:{columns:5},
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
				colspan:3
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
					value:          'All',
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
					id: 'mtnMaxDate',
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
					var text;
					var selJob = Ext.getCmp('mtnCbJobs').getValue();
					var selMet = Ext.getCmp('mtnCbMeth').getValue();     
					var maxDate = Ext.getCmp('mtnMaxDate').getValue().format('Y-m-d');
					var isAll =  selJob == 'All' ? true : false;

					if(isAll)
						text = String.format(EasySDI_Mon.lang.getLocal('confirm suppress all raw logs detail'), EasySDI_Mon.DateRenderer(maxDate));
					else if(selMet != 'All')
						text = String.format(EasySDI_Mon.lang.getLocal('confirm suppress raw log meth detail'), selJob, selMet, EasySDI_Mon.DateRenderer(maxDate));
					else
						text = String.format(EasySDI_Mon.lang.getLocal('confirm suppress raw log detail'), selJob, EasySDI_Mon.DateRenderer(maxDate));

					Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), text, function(btn){
						if (btn == 'no')
							return false;
						else
							clearLogs(eLogType.daily, isAll)
					});
				},
				text: EasySDI_Mon.lang.getLocal('action clear raw logs')
				}]
			},{
				items:[{
					id: 'mtnDelAgg',
					xtype:'button',
					disabled:true,
					handler: function(){
					var text;
					var selJob = Ext.getCmp('mtnCbJobs').getValue();
					var selMet = Ext.getCmp('mtnCbMeth').getValue();     
					var maxDate = Ext.getCmp('mtnMaxDate').getValue().format('Y-m-d');
					var isAll =  selJob == 'All' ? true : false;

					if(isAll)
						text = String.format(EasySDI_Mon.lang.getLocal('confirm suppress all agg logs detail'), EasySDI_Mon.DateRenderer(maxDate));
					else if(selMet != 'All')
						text = String.format(EasySDI_Mon.lang.getLocal('confirm suppress agg log meth detail'), selJob, selMet, EasySDI_Mon.DateRenderer(maxDate));
					else
						text = String.format(EasySDI_Mon.lang.getLocal('confirm suppress agg log detail'), selJob, EasySDI_Mon.DateRenderer(maxDate));

					Ext.MessageBox.confirm(EasySDI_Mon.lang.getLocal('confirm'), text, function(btn){
						if (btn == 'no')
							return false;
						else
							clearLogs(eLogType.aggregate, isAll)
					});
				},
				text: EasySDI_Mon.lang.getLocal('action clear agg logs')
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
			//jobComboStore.add(aRec[i]);
		}
		jobComboStore.insert(0, new jobComboStore.recordType({name:'All'}));
	}

	Ext.getCmp('mtnCbJobs').on('select', function(cmb, rec){
		//refresh method store
		methodComboStore.removeAll();
		if(rec.get('name') != 'All'){
			methodComboStore.proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+rec.get('name')+'/queries');
			methodComboStore.load();
		}else{
			methodComboStore.insert(0, new methodComboStore.recordType({name:'All',value:'All'}));
		}

		//refresh log grid as soon as the method store is loaded
		/*
      methodComboStore.on('load', function() {
         var maxDate = Ext.getCmp('mtnMaxDate').getValue().format('Y-m-d');
	 var p = logDailyStore.proxy;
	 logDailyStore.proxy.api.read.url=EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+rec.get('name')+'/logs?maxDate='+maxDate;
	 logDailyStore.proxy.conn.url=EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+rec.get('name')+'/logs?maxDate='+maxDate;
         logDailyStore.load();
      });
		 */
	});

	methodComboStore.on('load', function() {
		methodComboStore.insert(0, new methodComboStore.recordType({name:'All',value:'All'}));
	});

	/* type: daily/aggregate*/
	function clearLogs(type, all){
		var selJob = Ext.getCmp('mtnCbJobs').getValue();
		var selMet = Ext.getCmp('mtnCbMeth').getValue();     
		var maxDate = Ext.getCmp('mtnMaxDate').getValue().format('Y-m-d');
		var selType = type == eLogType.daily ? 'logs' : 'aggLogs';
		var arrJob = Array();

		//get jobs to delete
		if(all){
			var aRec = jobComboStore.getRange();
			for(var j=0; j< aRec.length; j++ )
				arrJob.push(aRec[j].get('name'));
		}else{
			if(selJob != '')
				arrJob.push(selJob);
		}

		var myMask = new Ext.LoadMask(Ext.getCmp('clearLogsFs').getEl(), {msg:EasySDI_Mon.lang.getLocal('message wait')});
		myMask.show();
		var counter = arrJob.length;
		for( var j=0; j< arrJob.length; j++ ){
			if(selMet == 'All' || selMet == ''){
				Ext.Ajax.request({
					loadMask: true,
					method: 'DELETE',
					headers: {
					'Content-Type': 'application/json'
				},
				params: '{"data":{"maxDate":"'+maxDate+'"}}',
				url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+arrJob[j]+'/'+selType,
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
					headers: {
					'Content-Type': 'application/json'
				},
				params: '{"data":{"maxDate":"'+maxDate+'"}}',
				url: EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+arrJob[j]+'/queries/'+selMet+'/'+selType,
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



});/**
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
/**
 * Main application Panel
 */

EasySDI_Mon.mainPanel = 2;
Ext.onReady(function() {

	var appPanel

	var item1 = new Ext.Panel({
		title: EasySDI_Mon.lang.getLocal('advanced'),
		layout: 'fit',
		border:false,
		frame:true,
                autoScroll: true,
		items: [Ext.getCmp('jobAdvForm')]
	});

	var item2 = new Ext.Panel({
		title: EasySDI_Mon.lang.getLocal('requests'),
		layout: 'fit',
		border:false,
		frame:true,
                autoScroll: true,
		items: [Ext.getCmp('ReqGrid')]
	});

	var item3 = new Ext.Panel({
		title: EasySDI_Mon.lang.getLocal('alerts'),
		layout: 'fit',
		border:false,
		frame:true,
                autoScroll: true,
		items: [Ext.getCmp('AlertForm')]
	});

	var accordion = new Ext.Panel({
		region:'east',
		//margins:'5 5 5 0',
		split:true,
		width: '40%',
		layout:'accordion',
		items:
                        [item1, item2, item3]
	});

	//Job panel
	var jobPanel = {
			title: EasySDI_Mon.lang.getLocal('jobs'),
			frame:true,
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [accordion, Ext.getCmp('JobGrid')]
	};
	

	var reportPanel = {
			id:'reportPanel',
			title: EasySDI_Mon.lang.getLocal('reports'),
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [
			        Ext.getCmp('ReportMenu')
			        ]
	};

	var alertPanel = {
			title:EasySDI_Mon.lang.getLocal('alerts'),
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border: false,
			items: [Ext.getCmp('AlertGrid')]
	};

	var statePanel = {
			title:EasySDI_Mon.lang.getLocal('state'),
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [Ext.getCmp('JobStateGrid')]
	};

	var maintenancePanel = {
			title:EasySDI_Mon.lang.getLocal('maintenance'),
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [Ext.getCmp('MaintenancePanel')]
	};
	
	responseoverviewPanel = {
			title:EasySDI_Mon.lang.getLocal('responseoverview'),
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [Ext.getCmp('ResponseOverviewPanel')]
	};
	
	slaPanel = {
			title: EasySDI_Mon.lang.getLocal('sla'),
			frame:true,
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [Ext.getCmp('SlaGrid')]
	};
	
	exportPanel = {
			title:EasySDI_Mon.lang.getLocal('export'),
			xtype: 'panel',
			layout: 'border',
			region: 'center',
			border:false,
			items: [Ext.getCmp('testGrid')]
	};

	var cardTabs = new Ext.TabPanel({
		id: 'card-tabs-panel',
		activeTab: EasySDI_Mon.defaultTab,
		defaults: {bodyStyle: 'padding:15px'},
		items:[
		       statePanel,
		       jobPanel,
		       reportPanel,
		       alertPanel,
		       maintenancePanel/*,
		       slaPanel*/
		       ]
	});


	var appPanel = new Ext.Panel({
		id: 'appPanel',
		frame:true,
		anchor: '50%',
		region: 'center', // this is what makes this panel into a region within the containing layout
		layout: 'card',
		margins: '2 5 5 0',
		activeItem: 0,
		border: true,
		items: [cardTabs]
	});

	EasySDI_Mon.mainPanel = new Ext.Panel({
		height:EasySDI_Mon.appHeight,
		id: 'mainPanel',
		xtype: 'panel',
		renderTo: "tabsContainer",
		layout: 'border',
		border:false,
		frame:false,
		items: [
		        new Ext.Panel({
		        	region:'north',
		        	height:35,
		        	id:'JobCollectionPanel',
		        	ref:'JobCollectionPanel',
		        	border:false,
		        	frame:false,
		        	//margins:'0 0 0 0',
		        	layout:'table',
		        	layoutConfig:{columns:2},
		        	items: [{
		        		html:EasySDI_Mon.lang.getLocal('job collection')+':',
		        		handleMouseEvents: false,
		        		border:false
		        	},{
		        		xtype: 'combo',
		        		border:false,
		        		mode: 'local',
		        		id: 'jobCbCollection',
		        		triggerAction: 'all',
		        		forceSelection: true,
		        		editable:       false,
		        		value:          EasySDI_Mon.DefaultJobCollection,
		        		name:           'jobCbCollection',
		        		displayField:   'name',
		        		valueField:     'value',
		        		store:          new Ext.data.SimpleStore({
		        			fields : ['name', 'value'],
		        			data : EasySDI_Mon.JobCollectionStore
		        		})
		        	}]
		        }),
		        appPanel
		        ]
	});

	//handler for job collection combo

	Ext.getCmp('jobCbCollection').on('select', function(cmb, rec){
		var store = Ext.getCmp('JobGrid').store;
		EasySDI_Mon.CurrentJobCollection = rec.data.value;
		store.proxy.setUrl(EasySDI_Mon.proxy+rec.data.value);
		//change the api if you require other stuffs than "get"
		store.proxy.api.create.url = EasySDI_Mon.proxy+rec.data.value;
		store.proxy.api.destroy.url = EasySDI_Mon.proxy+rec.data.value;
		store.proxy.api.read.url = EasySDI_Mon.proxy+rec.data.value;
		store.proxy.api.update.url = EasySDI_Mon.proxy+rec.data.value;
		store.load();
	});

	if(cardTabs.getActiveTab().id == 'reportPanel')
		Ext.getCmp('mainPanel').setHeight(150);

	cardTabs.on('tabchange', function(cardTab, panel){
		if(panel.id == 'reportPanel'){
			Ext.getCmp('mainPanel').setHeight(150);
		}else{
			EasySDI_Mon.clearCharts();

			var OrigTB = document.getElementById('element-box');
			var c = document.getElementById('container1');
			if(c.firstChild != null)
				c.removeChild(c.firstChild);
			c = document.getElementById('container2');
			if(c.firstChild != null)
				c.removeChild(c.firstChild);
			c = document.getElementById('container3');
			if(c.firstChild != null)
				c.removeChild(c.firstChild);

			Ext.getCmp('mainPanel').setHeight(EasySDI_Mon.appHeight);
		}
	});


	//Resize when the browser window size changes
	Ext.EventManager.onWindowResize( function(){ 
		appPanel.setWidth(Ext.getDom('tabsContainer').clientWidth);
		EasySDI_Mon.mainPanel.JobCollectionPanel.setWidth(Ext.getDom('tabsContainer').clientWidth);
	});

	//Touille the help link
	try
	{
		Ext.getDom('toolbar-help').getChildren()[0].setAttribute("onClick","window.open('http://forge.easysdi.org/wiki/monitor')");
	}catch(e)
	{
		// ERROR IE 7
	}
	

	/* Note: if Joomla menu appears under the app, you need to adapt the css for #menu li ul (prop z-index)*/
	//We remove the default Joomla admin submenu till we use Ext TabPanel.
	//var OrigTB = document.getElementById('toolbar-box');
	//var parentNode = document.getElementById('toolbar-box').parentNode;
	//parentNode.removeChild(OrigTB);
}); /**
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

EasySDI_Mon.drawHealthGraphRaw = function(container, aStores, logRes,useSla){
	     //Prepare graph options
	     var options = {
                chart: {
			renderTo: container,
			defaultSeriesType: 'column'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('service health')
		},
		xAxis: {
			categories: []
		},
		yAxis: {
			min: 0,
			max: 100,
			title: {
				text: EasySDI_Mon.lang.getLocal('percentage')
			}
		},
		legend: {
			backgroundColor: '#FFFFFF',
			reversed: true
		},
		tooltip: {
			formatter: function() {
				return ''+
					 this.series.name +': '+ this.y +'%';
			}
		},
		plotOptions: {
			series: {
				stacking: 'normal'
			}
		},
		       series: []
	     };
	     
	     //prepare graph data

	     var avSeries = {
                name: EasySDI_Mon.lang.getLocal('available'),
                data: [],
                color: '#7dff9c'
             };
             var unavSeries = {
                name: EasySDI_Mon.lang.getLocal('unavailable'),
                data: [],
                color: '#ff7f7f'
             };
             var fSeries = {
                name: EasySDI_Mon.lang.getLocal('failure'),
                data: [],
                color: '#e2ff1d'
             };
             var otherSeries = {
                name: EasySDI_Mon.lang.getLocal('untested-unknown'),
                data: [],
                color: '#b3b3b3'
             };
	     //contains untested and unknown
	     var otherSeries;
	     //push categories
	     for ( var storeName in aStores)
         {
	    	 if(typeof aStores[storeName] != 'function'){
	    		 options.xAxis.categories.push(storeName);
	    	 }
	     }
	     
	    var avCount = 0;
        var unavCount = 0;
        var fCount = 0;
        var otherCount = 0;
	    
        //push series
        for ( var storeName in aStores)
        {
        	// Reset for each method
        	avCount = 0;
            unavCount = 0;
            fCount = 0;
            otherCount = 0;
            
        	if(typeof aStores[storeName] != 'function'){
	            var aRec = aStores[storeName].getRange();
	        	var summaryCount = 0;
                    //push percentiles
                    for ( var i=0; i< aRec.length; i++ )
                    {   
                    	if(aRec[i].get('avCount') >= 0)
                    	{
                    		avCount+=aRec[i].get('avCount');
                    		fCount+=aRec[i].get('fCount');
                    		unavCount+=aRec[i].get('unavCount');
                    		otherCount+=aRec[i].get('otherCount');
                    		summaryCount = avCount + fCount +unavCount + otherCount;
                    	}else
                    	{
                    		var status = aRec[i].get('statusCode');
	                    	switch (status){
	                             case 'AVAILABLE':
	                                   avCount++;
	                             break;
	                             case 'OUT_OF_ORDER':
	                                   fCount++;
	                             break;
	                             case 'UNAVAILABLE':
	                                   unavCount++;
	                             break;
		                         case 'NOT_TESTED':
	                                   otherCount++;
	                             break;
	                             default: 
	                                   otherCount++;
	                             break;
	                        	}
                    	}
                    }
        			if(summaryCount > 0)
					{
						avSeries.data.push(Math.round((avCount/summaryCount)*10000)/100);
						unavSeries.data.push(Math.round((unavCount/summaryCount)*10000)/100);
						fSeries.data.push(Math.round((fCount/summaryCount)*10000)/100);
						otherSeries.data.push(Math.round((otherCount/summaryCount)*10000)/100);
					}else
					{
						avSeries.data.push(Math.round((avCount/aRec.length)*10000)/100);
						unavSeries.data.push(Math.round((unavCount/aRec.length)*10000)/100);
						fSeries.data.push(Math.round((fCount/aRec.length)*10000)/100);
						otherSeries.data.push(Math.round((otherCount/aRec.length)*10000)/100);
                    }
		    
        	}
	     }
	     
	    //push this series
	    options.series.push(otherSeries);
	    options.series.push(unavSeries);
	    options.series.push(fSeries);
	    options.series.push(avSeries);
	    
	    //Output the graph
	    var chart = new Highcharts.Chart(options);
	    return chart;
	  };/**
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

EasySDI_Mon.drawHealthGraphAgg = function (container, aStores, logRes,useSla,showInspireGraph){
	//Prepare graph options
	var options = {
		chart: {
			renderTo: container,
			defaultSeriesType: 'column'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('service health summary')
		},
		xAxis: {
			categories: []
		},
		yAxis: {
			min: 0,
			max: 100,
			title: {
			text: EasySDI_Mon.lang.getLocal('percentage')
		}
		},
		legend: {
			backgroundColor: '#FFFFFF',
			reversed: true
		},
		tooltip: {
			formatter: function() {
			return ''+
			this.series.name +': '+ this.y +'%';
		}
		},
		plotOptions: {
			series: {
			stacking: 'normal'
		}
		},
		series: []
	};

	//prepare graph data

	var avSeries = {
			name: EasySDI_Mon.lang.getLocal('available'),
			data: [],
			color: '#7dff9c'
	};
    var unavSeries = {
            name: EasySDI_Mon.lang.getLocal('unavailable'),
            data: [],
            color: '#ff7f7f'
     };
     var fSeries = {
        name: EasySDI_Mon.lang.getLocal('failure'),
        data: [],
        color: '#e2ff1d'
     };
     var otherSeries = {
        name: EasySDI_Mon.lang.getLocal('untested-unknown'),
        data: [],
        color: '#b3b3b3'
     };

	//push categories
	for ( var storeName in aStores)
	{
		if(typeof aStores[storeName] != 'function'){
			if(useSla)
			{
				if(!showInspireGraph)
				{
					options.xAxis.categories.push(storeName);//+EasySDI_Mon.lang.getLocal('h1 suffix'));
				}else
				{
					options.xAxis.categories.push(storeName);//+EasySDI_Mon.lang.getLocal('inspire suffix'));
				}
			}else
			{
				options.xAxis.categories.push(storeName+EasySDI_Mon.lang.getLocal('h24 suffix'));
				options.xAxis.categories.push(storeName+EasySDI_Mon.lang.getLocal('sla suffix'));
			}
		}
	}

	var avCountH24;
	var avCountSLA;
	var unavCount;
	var unavCountSP;
	var fCount;
	var fCountSP
	var otherCount;
	var otherCountSP;
	if(useSla)
	{
		//push series
		for ( var storeName in aStores)
		{
			if(typeof aStores[storeName] != 'function'){
				var aRec = aStores[storeName].getRange();
				avCountH24 = 0;
				avCountSLA = 0;
				unavCount = 0;
				unavCountSP = 0;
				fCount = 0;
				fCountSP = 0;
				otherCount = 0;
				otherCountSP = 0;
				//push percentiles
				for ( var i=0; i< aRec.length; i++ )
				{   
					avCountH24 += aRec[i].get('h1Availability');
					avCountSLA += aRec[i].get('inspireAvailability');
					
					unavCount += aRec[i].get('h1Unavailability');
					unavCountSP += aRec[i].get('inspireUnavailability');
					fCount += aRec[i].get('h1Failure');
					fCountSP += aRec[i].get('inspireFailure');
					otherCount += aRec[i].get('h1Untested');
					otherCountSP += aRec[i].get('inspireUntested');
				}
		      
				if(!showInspireGraph)
				{
					avSeries.data.push(Math.round((avCountH24/aRec.length) * 100)/100);
					unavSeries.data.push(Math.round((unavCount/aRec.length) * 100)/100);
					fSeries.data.push(Math.round((fCount/aRec.length) * 100)/100);
					otherSeries.data.push(Math.round((otherCount/aRec.length) * 100)/100);
				}else{
					avSeries.data.push(Math.round((avCountSLA/aRec.length) * 100)/100);
					unavSeries.data.push(Math.round((unavCountSP/aRec.length) * 100)/100);
					fSeries.data.push(Math.round((fCountSP/aRec.length) * 100)/100);
					otherSeries.data.push(Math.round((otherCountSP/aRec.length) * 100)/100);
				}
			}
		}
	}else
	{
				//push series
				for ( var storeName in aStores)
				{
					if(typeof aStores[storeName] != 'function'){
						var aRec = aStores[storeName].getRange();
						avCountH24 = 0;
						avCountSLA = 0;
						unavCount = 0;
						unavCountSP = 0;
						fCount = 0;
						fCountSP = 0;
						otherCount = 0;
						otherCountSP = 0;
						//push percentiles
						for ( var i=0; i< aRec.length; i++ )
						{   
							avCountH24 += aRec[i].get('h24Availability');
							avCountSLA += aRec[i].get('slaAvalabilty');
							unavCount += aRec[i].get('h24Unavailability');
							unavCountSP += aRec[i].get('slaUnavailability');
							fCount += aRec[i].get('h24Failure');
							fCountSP += aRec[i].get('slaFailure');
							otherCount += aRec[i].get('h24Untested');
							otherCountSP += aRec[i].get('slaUntested');
						}
						
						avSeries.data.push(Math.round( (avCountH24/aRec.length) * 100)/100);
						avSeries.data.push(Math.round((avCountSLA/aRec.length) * 100)/100);
						unavSeries.data.push(Math.round((unavCount/aRec.length) * 100)/100);
						unavSeries.data.push(Math.round((unavCountSP/aRec.length) * 100)/100);
						fSeries.data.push(Math.round((fCount/aRec.length) * 100)/100);
						fSeries.data.push(Math.round((fCountSP/aRec.length) * 100)/100);
						otherSeries.data.push(Math.round((otherCount/aRec.length) * 100)/100);
						otherSeries.data.push(Math.round((otherCountSP/aRec.length) * 100)/100);
					}
				}
	}

	options.series.push(otherSeries);
	options.series.push(unavSeries);
	options.series.push(fSeries);
	options.series.push(avSeries);
	//Output the graph
	var chart = new Highcharts.Chart(options);
	return chart;
};/**
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

EasySDI_Mon.drawHealthLineGraph = function(container, aStores, logRes, tickInterval){
	//Prepare graph options
	var options = {
		chart: {
				renderTo: container,
				marginRight: 130,
				zoomType: 'x'
			//,
			//defaultSeriesType: 'spline'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('service health'),
			x: -20 //center
		},
		xAxis: {
			title: EasySDI_Mon.lang.getLocal('grid header dateTime'),
			type: 'datetime',
			maxZoom: tickInterval / 10,
			// one day interval
			tickInterval: tickInterval
		},
		yAxis: {
			title: {
			text:EasySDI_Mon.lang.getLocal('percentage')
		}
		},
		tooltip: {
			formatter: function() {
			return '<b>'+ this.series.name +'</b><br/>'+
			new Date(this.x).format('d-m-Y') +': '+ this.y +'[%]';
		}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: -10,
			y: 100,
			borderWidth: 0
		},
		series: []
	};

	//prepare graph data
	var series;
	for ( var storeName in aStores)
	{
		if(typeof aStores[storeName] != 'function'){
			var aRec = aStores[storeName].getRange();
			series = {data: []};
			//H24
			series.name = storeName+EasySDI_Mon.lang.getLocal('h24 suffix');
			for ( var i=0; i< aRec.length; i++ )
			{   
				series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('h24Availability'))]);
			}
			options.series.push(series);

			//SLA
			series = {data: []};
			series.name = storeName+EasySDI_Mon.lang.getLocal('sla suffix');
			for ( var i=0; i< aRec.length; i++ )
			{   
				series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('slaAvalabilty'))]);
			}
			options.series.push(series);

		}
	}

	//Output the graph
	var chart = new Highcharts.Chart(options);
	return chart;

};/**
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

EasySDI_Mon.drawResponseTimeGraph = function (container, aStores, logRes, tickInterval, jobRecord,useSla,showInspireGraph){
	//Prepare graph options
	var options = {
		colors: [
			         '#000000', 
			         '#414141', 
			         '#7b7b7b', 
			         '#874900', 
			         '#855720', 
			         '#7e5e39', 
			         '#7e6c57'
			         ],
	    chart: {
			renderTo: container,
			marginRight: 130,
			zoomType: 'x'
			//,
			//defaultSeriesType: 'spline'
		},
		title: {
			text: EasySDI_Mon.lang.getLocal('response time graph title'),
			x: -20 //center
		},
		xAxis: {
			title: EasySDI_Mon.lang.getLocal('dateTime'),
			type: 'datetime',
			maxZoom: tickInterval / 10,
			// one day interval
			tickInterval: tickInterval
		},
		yAxis: {
			title: {
			text:EasySDI_Mon.lang.getLocal('response time')+' '+EasySDI_Mon.lang.getLocal('ms suffix')
		}
		,
		min: 0, 
		//Sets the maxY to 4/3 the timeout
		max: jobRecord.get('timeout')*1000*1.3333333,
		minorGridLineWidth: 0, 
		gridLineWidth: 0,
		alternateGridColor: null,
		plotBands: [{ // Available
			from: 0,
			//set a colored area for the timout value
			to: jobRecord.get('timeout')*1000,
			color: 'rgba(68, 170, 213, 0.1)'
		}]},
		labels: {
			items: [{
				html: EasySDI_Mon.lang.getLocal('area within timeout'),
				style: {
				left: '10px',
				top: '240px'
			}
			}]
		},
		tooltip: {
			formatter: function() {
				var tip =  
					"<b>"+ this.series.name +"</b><br/>"+
					new Date(this.x).format('d-m-Y H:i:s') +" -> "+ this.y + EasySDI_Mon.lang.getLocal('ms suffix')+" <br/>";
					
					if(this.point.log == "aggLogs")
					{
						if(useSla)
						{
							if(this.point.normalGraph)
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h1Availability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h1Unavailability * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.h1Failure * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.h1Untested * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.h1NbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.h1NbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.h1MaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.h1MinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
							}else
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.inspireAvailability * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.inspireUnavailability * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.inspireFailure * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.inspireUntested * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.inspireNbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.inspireNbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.inspireMaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.inspireMinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";	
							}
						}
						else
						{
							if(this.point.normalGraph)
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h24Availability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.h24Unavailability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.h24Failure * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.h24Untested * 100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.h24NbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.h24NbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.h24MaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.h24MinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";		
							}else
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip AVAILABILITY')+":</b> "+(Math.round(this.point.data.data.slaAvailability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNAVAILABILITY')+":</b> "+(Math.round(this.point.data.data.slaUnavailability *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip FAILURE')+":</b> "+(Math.round(this.point.data.data.slaFailure *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip UNTESTED')+":</b> "+(Math.round(this.point.data.data.slaUntested *100)/100)+"%<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_CONN_ERRORS')+":</b> "+this.point.data.data.slaNbConnErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip NB_BIZ_ERRORS')+":</b> "+this.point.data.data.slaNbBizErrors+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b> -> "+Math.round(this.point.data.data.slaMaxRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MinRespTime')+"</b> -> "+Math.round(this.point.data.data.slaMinRespTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";	
				
							}
						}
					}else
					{
						tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip response size')+"</b>: "+Math.round(this.point.data.data.size)+" bytes<br/>";
						// Test for summmary
						if(this.point.data.data.maxTime)
						{
						 	tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip MaxRespTime')+"</b>: "+ Math.round(this.point.data.data.maxTime * 1000) + EasySDI_Mon.lang.getLocal('ms suffix')+"<br/>";	
						}else
						{
							if(this.point.data.data.statusCode.toLowerCase() == "unavailable")
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip http statuscode')+"</b>: "+ this.point.data.data.httpCode+"<br/>";
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip response message')+"</b>: "+ this.point.data.data.message+"<br/>";
							}
							if(this.point.data.data.statusCode.toLowerCase() == "out_of_order")// failed
							{
								tip+= "<b>"+EasySDI_Mon.lang.getLocal('tooltip response message')+"</b>: "+ this.point.data.data.message;
							}
						}
					}
				return tip;
			}
		},
		legend: {
			layout: 'vertical',
			align: 'right',
			verticalAlign: 'top',
			x: -10,
			y: 100,
			borderWidth: 0
		},
		series: []
	};

	//prepare graph data
	var series;
	for ( var storeName in aStores)
	{
		if(typeof aStores[storeName] != 'function'){
			var aRec = aStores[storeName].getRange();
			series = {data: []};
			//add h24 or delay response time
			if(useSla)
			{
				series.name = storeName;//+EasySDI_Mon.lang.getLocal('h1 suffix');
			}else
			{
				series.name = storeName+'[h24]';
			}
			
			for ( var i=0; i< aRec.length; i++ )
			{   
				if(logRes == 'aggLogs')
				{
					var point;
					if(useSla)
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('h1MeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('h1MeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: true
							};
					}else
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('h24MeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('h24MeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: true
							};
					}
					if(useSla && showInspireGraph)
					{
						// No need to push this graph
					}else
					{
						series.data.push(point);
					}
					
				
				}
				else{
					var status = aRec[i].get('statusCode');
					var color;
					switch (status){
                    case 'AVAILABLE':
						color = '#7dff9c';
						break;
                    case 'OUT_OF_ORDER':
						color = '#e2ff1d';
						break;
                    case 'UNAVAILABLE':
						color = '#ff7f7f';
						break;
                    case 'NOT_TESTED':
						color = '#b3b3b3';
						break;
					default: 
						color = '#b3b3b3';
						break;
					}
					var point = {
							x: aRec[i].get('time').getTime(),
							y: Math.round(aRec[i].get('delay') * 1000) != -1 ? Math.round(aRec[i].get('delay') * 1000) : 0,
							marker: {
								fillColor: color
							},
							data: aRec[i], // record info for tooltip
							log: logRes,
							normalGraph: true
					};
					series.data.push(point);
					//series.data.push([aRec[i].get('time').getTime(), Math.round(aRec[i].get('delay') * 1000)]);
				}
			}
			//push this serie
			if(useSla && showInspireGraph && logRes == 'aggLogs')
			{
				// No need to push this graph
			}else
			{
				options.series.push(series);
			}
			
			if(logRes == 'aggLogs'){
				series = {data: []};
				if(useSla)
				{
					series.name = storeName; // +EasySDI_Mon.lang.getLocal('inspire suffix');
				}else
				{
					series.name = storeName+EasySDI_Mon.lang.getLocal('sla suffix');
				}
				
				for ( var i=0; i< aRec.length; i++ )
				{   
					var point;
					if(useSla)
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('inspireMeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('inspireMeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: false
						};
					}else
					{
						point = {
								x: aRec[i].get('date').getTime(),
								y: Math.round(aRec[i].get('slaMeanRespTime') * 1000) != -1 ? Math.round(aRec[i].get('slaMeanRespTime') * 1000) : 0,
								data: aRec[i],
								log: logRes,
								normalGraph: false
						};
					}
					if(useSla && !showInspireGraph)
					{
						// No need to push this graph
					}else
					{
						series.data.push(point);
					}
									
					//series.data.push([aRec[i].get('date').getTime(), Math.round(aRec[i].get('slaMeanRespTime') * 1000)]);
				}
				if(useSla && !showInspireGraph)
				{
					// No need to push this graph
				}else{
					options.series.push(series);
				}
				
			}

		}
	}

	//Output the graph
	var chart = new Highcharts.Chart(options);
	return chart;
};