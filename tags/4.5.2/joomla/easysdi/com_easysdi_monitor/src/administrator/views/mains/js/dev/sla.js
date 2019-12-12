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
});