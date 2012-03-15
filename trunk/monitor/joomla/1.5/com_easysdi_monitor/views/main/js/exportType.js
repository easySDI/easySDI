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
});