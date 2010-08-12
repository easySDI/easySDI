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

    var proxy = new Ext.data.HttpProxy({
       url: EasySDI_Mon.proxy+'/jobs'
    });
    
    var writer = new Ext.data.JsonWriter({
       encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
    }); 
    // Listen to all DataProxy beforewrite events
    //
    Ext.data.DataProxy.addListener('beforewrite', function(proxy, action) {
	EasySDI_Mon.App.setAlert(EasySDI_Mon.App.STATUS_NOTICE, "Before " + action);
    });
    ////
    // all write events
    //
    Ext.data.DataProxy.addListener('write', function(proxy, action, result, res, rs) {
        EasySDI_Mon.App.setAlert(true, action + ':' + res.message);
    });
    
    ////
    // all exception events
    //
    Ext.data.DataProxy.addListener('exception', function(proxy, type, action, options, res) {
       EasySDI_Mon.App.setAlert(false, "Something bad happend while executing " + action);
    });
    
    var store = new Ext.data.JsonStore({
       //Will be used when writing JSON to the server as the root element
       //if not set: {"undefined":{"url":"blah","id":"plop"}}
       //root: '',
       id: 'name',
       restful:true,
       proxy: proxy,
       writer: writer,
       fields:['status', 'httpMethod', 'testInterval', 'bizErrors', 'isPublic', 'allowsRealTime', 'httpErrors', 'serviceType', 'password', 'url' ,'id' ,'slaEndTime', 'name', 'queries', 'login', 'triggersAlerts', 'timeout', 'isAutomatic', 'slaStartTime']
    });
    
    var editor = new Ext.ux.grid.RowEditor({
        saveText: 'Update'
    });
    
    
    var cm = new Ext.grid.ColumnModel([{
	header:EasySDI_Mon.lang.getLocal('grid header name'),
	dataIndex:"name",
	width:100,
	sortable: true,
	editable:false,
	editor: {
                xtype: 'textfield',
                allowBlank: false
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
	dataIndex:"url",
	width:270,
	editor: {
                xtype: 'textfield',
                allowBlank: false,
		vtype: 'url'
           }
	},{
	header:EasySDI_Mon.lang.getLocal('grid header interval'),
	dataIndex:"testInterval",
	width:50,
	editor: {
                xtype: 'numberfield',
                allowBlank: false,
		minValue: 1
            }
	},{
	header:EasySDI_Mon.lang.getLocal('grid header isauto'),
	dataIndex:"isAutomatic",
	width:40,
	trueText: 'true',
        falseText: 'false',
	renderer: EasySDI_Mon.TrueFalseRenderer,
        editor: {
                xtype: 'checkbox'
        }
    }
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
    
    _jobGrid = new Ext.grid.GridPanel({
       id:'JobGrid',
       loadMask:true,
       region:'center',
        plugins: [editor],
        tbar: [{
            text: EasySDI_Mon.lang.getLocal('grid action add'),
            handler: onAdd
        },'-',{
            ref: '../removeBtn',
            text: EasySDI_Mon.lang.getLocal('grid action rem'),
            disabled: true,
            handler: onDelete
        }],
       title:"Liste des jobs",
       loadMask:new Ext.LoadMask(Ext.getBody(), {msg:EasySDI_Mon.lang.getLocal('message wait')}),
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
       // paging bar on the bottom
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: store,
            displayInfo: true,
            displayMsg: EasySDI_Mon.lang.getLocal('paging display msg'),
            emptyMsg: EasySDI_Mon.lang.getLocal('paging empty msg')
        })
   });
    
    
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
        	              allowBlank:false
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
			        allowBlank: false,
			        vtype: 'url'
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
			      value: u.data['isAutomatic'],
			      checked: u.data['isAutomatic']
        	          }],
		   buttons: [{
        	              text: EasySDI_Mon.lang.getLocal('grid action ok'),
		   	      handler: function(){
   	       			     //editor.stopEditing();
				     var fields = win.jobPanel.getForm().getFieldValues();
                                     u.set('name', fields.name);
				     u.set('httpMethod', fields.httpMethod);
				     u.set('serviceType', fields.serviceType);
				     u.set('url', fields.url);
				     u.set('testInterval', fields.testInterval);
				     u.set('isAutomatic', fields.isAutomatic);
	       			     _jobGrid.store.insert(0, u);
				     win.close();
	       			     //editor.startEditing(0);
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
     * onDelete
     */
    function onDelete() {
        var rec = _jobGrid.getSelectionModel().getSelected();
        if (!rec) {
            return false;
        }
        _jobGrid.store.remove(rec);
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
    store.load();
    
    _jobGrid.getSelectionModel().on('selectionchange', function(sm){
        _jobGrid.removeBtn.setDisabled(sm.getCount() < 1);
	if(sm.getCount() < 1)
	   _advForm.updateAdv.disable();
        else
	   _advForm.updateAdv.enable();
   
    });
    
    
    //Advanced edition form
    _advForm = new Ext.FormPanel({
        id: 'jobAdvForm',
	title: EasySDI_Mon.lang.getLocal('advanced'),
       // labelAlign: 'top',
        frame:true,
        //bodyStyle:'padding:5px 5px 0',
       // width: 400,
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
                    xtype:'timefield',
                    fieldLabel: EasySDI_Mon.lang.getLocal('grid header sla start'),
                    name: 'slaStartTime',
		    minValue: '7:00',
                    maxValue: '18:00',
                    increment: 30,
		    format: 'H:i:s',
                    anchor:'95%'
              }]
              },{
              columnWidth:.5,
              layout: 'form',
              items: [{
                    xtype:'textfield',
                    fieldLabel: EasySDI_Mon.lang.getLocal('grid header password'),
                    name: 'password',
		    allowBlank:true,
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
	      falseText: 'false',
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
	      falseText: 'false',
        },{
              xtype:'checkbox',
              fieldLabel: EasySDI_Mon.lang.getLocal('grid header httperrors'),
              name: 'httpErrors',
              trueText: 'true',
	      falseText: 'false',
        },{
              xtype:'checkbox',
              fieldLabel: EasySDI_Mon.lang.getLocal('grid header ogcerrors'),
              name: 'bizErrors',
              trueText: 'true',
	      falseText: 'false',
        }],
        buttons: [{
           text: EasySDI_Mon.lang.getLocal('grid action update'),
	   ref: '../updateAdv',
	   disabled: true,
	   handler: function(){
		var rec = _jobGrid.getSelectionModel().getSelected();
                if (!rec) {
                    return false;
                }
		//get form values
		var fields = _advForm.getForm().getFieldValues();
		//update rec values
		rec.beginEdit();
		for (var el in fields){
			rec.set(el, fields[el]);
		}
                rec.endEdit();
		//save the model.
		//_jobGrid.store.save();
	   }
       }]
    });

    //simple.render(document.body);

    
    
});	