Ext.namespace("EasySDI_Mon");

Ext.onReady(function() {
	
	EasySDI_Mon.appHeight = 800;
	var pagename = "";
	
	var proxy_URL = "/administrator/components/com_easysdi_monitor/views/main/tmpl/proxy.php?proxy_url=";
	
	var proxyTableOverview = new Ext.data.HttpProxy({
		api: {
		    read    : '?',
			create  : '?',
	        update  : '?',
	        destroy : '?'
	    }
	});
		// Proxy
	var proxyOverview = new Ext.data.HttpProxy({
		   api: {
		        read    : '?',
		        create  : '?',
		        update  : '?',
		        destroy : '?'
		    }
	});

	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather 	 than HTTP params
	}); 
		
	/*data Stores*/
	var overviewPageStore = new Ext.data.JsonStore({
		id:'name',
		root: 'data',
		restful:true,
		fields:['name','isPublic'],
		proxy: proxyOverview,
		writer: writer
	});
	
	// Store for table data
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
				{name:'lastQueryResult',mapping:'lastQueryResult'},{name:'picture_url',mapping:'lastQueryResult.picture_url'},
				{name:'xml_result',mapping:'lastQueryResult.xml_result'},{name:'text_result',mapping:'lastQueryResult.text_result'}
			]

	});
	
	// View and edit Table 
	var controlTable = 
	{	
			xtype: 'panel',
			id: 'requestTable',
			region: 'center',
			autoHeight: true,
			hidden: true,
			width: '98%',
			//layout:'table',	
			//layoutConfig:{columns:3},
			defaults: {
				bodyStyle:'padding:8px',
				//width:380, 
				height:290
			},
			title: ''
	};
	
	// Panel
	var responseoverviewPanel = new Ext.Panel({
		id:'ResponseOverviewPanel',
		region:'center',
		layout: 'border',
		height:EasySDI_Mon.appHeight,
		renderTo: document.body,
		border:false,	
		bodyStyle:'overflow-x:hidden;overflow-y:auto',
		frame:true,
		items: [controlTable]
	});
	
	function createView()
	{	
		// Create tableview
		var table = Ext.getCmp('requestTable');
		try
		{
			table.removeAll();
			table.setVisible(true);
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

			if( aRec[i].get('isPublic') == "1")
			{
					controls = createTable(aRec[i],content_type.toLowerCase());
					table.add(controls);
			}
		}
		
		// Add float left style to div 
		for(var i = 0;i < table.items.items.length;i++)
		{
			table.items.items[i].addClass('Div_overview_request_pub');
		}
		table.doLayout();
		// Set update timeout to 1 min
	   window.setTimeout(initTable, 60000);
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
		
		if(dTime < 0 || rec.get('statusCode').toLowerCase() == 'unavailable')
		{
			tHeaderColor = 'OverviewTableCellsError';
			tCellColor = 'OverviewTableCellsError';
			sCellColor = 'OverviewTableCellsError';
			oCellColor = 'OverviewTableCellsError';
		}
		
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
							text: 'Service name', // TODO lang
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
							text: 'Size', // TODO lang
							cls: 'OverviewLabel',
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'responseSize',
							text: rec.get('responseSize') + ' bytes', // TODO lang
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'norm_label',
							text: 'Norm size', // TODO lang
							cls: 'OverviewLabel',
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'normSize',
							text: rec.get('normSize') + ' bytes', // TODO lang
							cellCls: sCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Deliverytime_label',
							text: 'Delivery time', // TODO lang
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'deliveryTime',
							text: Math.round(rec.get('deliveryTime') * 1000)+ ' ms', // TODO lang
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: 'Norm time', // TODO lang
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: rec.get('normTime') + ' ms', // TODO lang
							cellCls: tCellColor
						},
						{
						width: rec.get('picture_url') ? 142: 350,
						height: 142,
						html: rec.get('picture_url') ? '<img src='+rec.get('picture_url')+' alt="missing image" style="border:1px solid;" width="140" height="140" />': '<textarea class="Text_area_result" >'+rec.get('text_result')+'</textarea>',
						colspan: 2,
						cellCls: 'OverviewTableCellsImg'
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
							text: 'Service name', // TODO lang
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
							text: 'Found',
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
							text: 'Norm',
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
							text: 'Delivery time', // TODO lang
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'deliveryTime',
							text: Math.round(rec.get('deliveryTime') * 1000) + ' ms', // TODO lang
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'Normtime_label',
							text: 'Norm time', // TODO lang
							cls: 'OverviewLabel',
							cellCls: tCellColor
						},
						{
							xtype: 'label',
	        	        	value: '',
	        	        	name: 'normTime',
							text: rec.get('normTime') + ' ms',
							cellCls: tCellColor
						},
						{
							xtype: 'textarea',
							autoScroll: true,
							width: 350,//180,
							height:140,
							style: 'border:1px solid;',
							readOnly: true,
							value: rec.get('xml_result')? rec.get('xml_result') :rec.get('text_result'),
							colspan: 2,
							cellCls: 'OverviewTableCells'
						}
						]
			        })// End form panel
        		]     
			}// end control
			
			break;
		default:
			break;
		}// end switch		
		return controls;
	}
	
	function createProxyURL()
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
		// Proxy_URL
		mainURL += proxy_URL;
		
		// ONLY FOR TEST
		if(window.location.host.toLowerCase() == "localhost")
		{
			mainURL = window.location.protocol + "//" + window.location.host+"/Monitor/";
		}
		
		return mainURL;
	}
	
	function initTable()
	{
			var url = createProxyURL()+'overviews/'+pagename+'/queries';
			overviewTableStore.proxy.api.read.url = url;
			overviewTableStore.load();
	}
	
	
	function getOverviewname()
	{  
		var name = "name";
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS ); 
		var results = regex.exec( window.location.href );
		if( results == null )
		{
			return "";
		}
		else
		{
			return results[1];
		}
	}
	
	function start()
	{
		pagename = getOverviewname();
		if(pagename != "")
		{
			var url = createProxyURL()+'overviews';
			overviewPageStore.proxy.api.read.url = url;
			overviewPageStore.load()
		}else
		{
			alert("The page doesn't exist!");
		}
	}
	
	/* Events */
	// Table store events
	overviewPageStore.on('load', function() {
		var index = overviewPageStore.find('name',pagename);
		if(index >= 0)
		{	
			if(overviewPageStore.getAt(index).get('isPublic'))
			{
				initTable();
			}else
			{
				// Test for login
								//Open a window for entering create a new response page
					var win = new  Ext.Window({
						width:250,
						autoScroll:false,
						modal:true,
						title: 'Login',//EasySDI_Mon.lang.getLocal('title new overviewpage'),
						items: [ 
						        new Ext.FormPanel({
						        	labelWidth: 70, // label settings here cascade unless overridden
						        	monitorValid:true,
						        	ref: 'login_panel',
						        	region:'center',
						        	bodyStyle:'padding:5px 5px 0',
						        	autoHeight:true,
						        	frame:true,
						        	defaults: {width: 200},
						        	defaultType: 'textfield',
						        	autoHeight:true,
						        	items: [
					        	        {
					        	        	fieldLabel: 'Brugernavn',
					        	        	value: '',
					        	        	name: 'name',
					        	        	allowBlank:false,
					        	        	xtype: 'textfield',
					        	        	width: 100
					        	        },
										{
					        	        	fieldLabel: 'Password',
					        	        	value: '',
					        	        	name: 'password',
					        	        	allowBlank:false,
					        	        	xtype: 'textfield',
					        	        	width: 100,
											minLength: 3
					        	        }
					        	        ],
					        	        buttons: [{
											formBind:true,
					        	        	text: 'Login',
					        	        	handler: function()
					        	        	{
												// DO REQUEST
												var fields = win.login_panel.getForm().getFieldValues();
												if(fields)
												{
													Ext.Ajax.request({
														loadMask: true,
														method: 'POST',
														params: 'name='+fields.name+'&pw='+fields.password+'',
														url: 'CheckAccess.php',
														success: function(response){
															initTable();
															win.close();
														},
														failure: function(response){
															Ext.MessageBox.alert('Error', 'Username or password wrong');
														}
													});
	      
												}
					        	        	}
					        	        }]
							        })
							        ]
						});
					// open add Window
					win.show();	
			}
		}
	});
	overviewTableStore.on('load', function()
	{
		createView();
	});
	
	start();
	
});