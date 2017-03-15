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
});