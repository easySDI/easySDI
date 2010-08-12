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
 Ext.onReady(function() {

 	var appPanel

        var item2 = new Ext.Panel({
            title: EasySDI_Mon.lang.getLocal('requests'),
	    layout: 'fit',
	    border:false,
	    frame:true,
	    items: [Ext.getCmp('ReqGrid')]
        });
	
        var accordion = new Ext.Panel({
            region:'east',
            margins:'5 5 5 0',
            split:true,
            width: '40%',
            layout:'accordion',
	    frame:"true",
            items: [Ext.getCmp('jobAdvForm'), item2, Ext.getCmp('AlertForm')]
        });

        //Job panel
	jobPanel = {
	    title: EasySDI_Mon.lang.getLocal('jobs'),
	    xtype: 'panel',
            layout: 'border',
	    region: 'center',
	    border:false,
            items: [accordion, Ext.getCmp('JobGrid')]
	};
	
	reportPanel = {
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
	
	alertPanel = {
	    title:EasySDI_Mon.lang.getLocal('alerts'),
	    xtype: 'panel',
            layout: 'border',
	    region: 'center',
	    border: false,
	    items: [Ext.getCmp('AlertGrid')]
	};
	
	statePanel = {
	    title:EasySDI_Mon.lang.getLocal('state'),
	    xtype: 'panel',
            layout: 'border',
	    region: 'center',
	    border:false,
	    items: [Ext.getCmp('JobStateGrid')]
	};
	
	maintenancePanel = {
	    title:EasySDI_Mon.lang.getLocal('maintenance'),
	    xtype: 'panel',
            layout: 'border',
	    region: 'center',
	    border:false,
	   // html:'fooo!!'
	    items: [Ext.getCmp('MaintenancePanel')]
	};
	
   var cardTabs = new Ext.TabPanel({
      id: 'card-tabs-panel',
      activeTab: 0,
      defaults: {bodyStyle: 'padding:15px'},
      items:[
               jobPanel,
	       reportPanel,
               alertPanel,
	       statePanel,
	       maintenancePanel
	    ]
   });
   
   var appPanel = new Ext.Panel({
      id: 'appPanel',
      renderTo: "tabsContainer",
      height:400,
      anchor: '50%',
      region: 'center', // this is what makes this panel into a region within the containing layout
      layout: 'card',
      margins: '2 5 5 0',
      activeItem: 0,
      border: false,
      items: [cardTabs]
   });
   
   
   if(cardTabs.getActiveTab().id == 'reportPanel')
      Ext.getCmp('appPanel').setHeight(100);

   cardTabs.on('tabchange', function(cardTab, panel){
       if(panel.id == 'reportPanel'){
             Ext.getCmp('appPanel').setHeight(100);
       }else{
	     EasySDI_Mon.clearCharts();
	     
	     var OrigTB = document.getElementById('toolbar-box');
               var c = document.getElementById('container1');
	       if(c.firstChild != null)
	            c.removeChild(c.firstChild);
	       c = document.getElementById('container2');
	       if(c.firstChild != null)
	            c.removeChild(c.firstChild);
	       c = document.getElementById('container3');
	       if(c.firstChild != null)
	            c.removeChild(c.firstChild);
	     
             Ext.getCmp('appPanel').setHeight(400);
       }
    });
   
   
   //Resize when the browser window size changes
   Ext.EventManager.onWindowResize( function(){ 
     appPanel.setWidth(Ext.getDom('tabsContainer').clientWidth);
   });
   
   /* Note: if Joomla menu appears under the app, you need to adapt the css for #menu li ul (prop z-index)*/
   //We remove the default Joomla admin submenu till we use Ext TabPanel.
   var OrigTB = document.getElementById('toolbar-box');
   var parentNode = document.getElementById('toolbar-box').parentNode;
   parentNode.removeChild(OrigTB);
   });