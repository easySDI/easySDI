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
	
});