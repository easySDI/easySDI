function manageObjectLinkFilter(objecttype, id, name, status, manager, editor, from, to) {
	var objecttypeFilter = new Ext.form.ComboBox( {
		typeAhead : true,
		triggerAction : 'all',
		mode : 'local',
		fieldLabel : objecttype['label'],
		id : 'objecttype_id',
		//hiddenName : 'objecttypeid_hidden',
		xtype : 'combo',
		editable : false,
		store : new Ext.data.ArrayStore( {
			id : 0,
			fields : [ 'value', 'text' ],
			data : objecttype['list']
		}),
		valueField : 'value',
		displayField : 'text',
		listeners : {
			select : {
				fn : function(combo, value) {
					var modelDest = Ext.getCmp('unselected');
					modelDest.store.removeAll();

					var selectedValues = new Array();
					var grid = Ext.getCmp('selected').store.data;
					for ( var i = 0; i < grid.length; i++) {
						selectedValues.push(grid.get(i).get('value'));
					}

					modelDest.store.reload( {
						params : {
							objecttype_id : combo.getValue(),
							id : Ext.getCmp('id').getValue(),
							name : Ext.getCmp('name').getValue(),
							status : Ext.getCmp('status').getValue(),
							manager : Ext.getCmp('manager').getValue(),
							editor : Ext.getCmp('editor').getValue(),
							fromDate : Ext.getCmp('fromDate').getValue(),
							toDate : Ext.getCmp('toDate').getValue(),
							selectedObjects : selectedValues.join(', ')
						}
					});
				}
			}
		}
	});

	var idFilter = new Ext.form.TextField( {
		xtype : 'textfield',
		id : 'id',
		fieldLabel : id['label'],
		enableKeyEvents : true,
		listeners : {
			keyup : {
				fn : function(value) {
					var modelDest = Ext.getCmp('unselected');
					modelDest.store.removeAll();

					var selectedValues = new Array();
					var grid = Ext.getCmp('selected').store.data;
					for ( var i = 0; i < grid.length; i++) {
						selectedValues.push(grid.get(i).get('value'));
					}

					modelDest.store.reload( {
						params : {
							objecttype_id : Ext.getCmp('objecttype_id')
									.getValue(),
							id : Ext.getCmp('id').getValue(),
							name : Ext.getCmp('name').getValue(),
							status : Ext.getCmp('status').getValue(),
							manager : Ext.getCmp('manager').getValue(),
							editor : Ext.getCmp('editor').getValue(),
							fromDate : Ext.getCmp('fromDate').getValue(),
							toDate : Ext.getCmp('toDate').getValue(),
							selectedObjects : selectedValues.join(', ')
						}
					});
				}
			}
		}
	});

	var nameFilter = new Ext.form.TextField( {
		xtype : 'textfield',
		id : 'name',
		fieldLabel : name['label'],
		enableKeyEvents : true,
		listeners : {
			keyup : {
				fn : function(value) {
					var modelDest = Ext.getCmp('unselected');
					modelDest.store.removeAll();

					var selectedValues = new Array();
					var grid = Ext.getCmp('selected').store.data;
					for ( var i = 0; i < grid.length; i++) {
						selectedValues.push(grid.get(i).get('value'));
					}

					modelDest.store.reload( {
						params : {
							objecttype_id : Ext.getCmp('objecttype_id')
									.getValue(),
							id : Ext.getCmp('id').getValue(),
							name : Ext.getCmp('name').getValue(),
							status : Ext.getCmp('status').getValue(),
							manager : Ext.getCmp('manager').getValue(),
							editor : Ext.getCmp('editor').getValue(),
							fromDate : Ext.getCmp('fromDate').getValue(),
							toDate : Ext.getCmp('toDate').getValue(),
							selectedObjects : selectedValues.join(', ')
						}
					});
				}
			}
		}
	});

	var statusFilter = new Ext.form.ComboBox( {
		typeAhead:true,
      	 triggerAction:'all',
      	 mode:'local',
        fieldLabel: status['label'], 
        id:'status', 
        //hiddenName:'status_hidden', 
        xtype: 'combo',
        editable: false,
        store: new Ext.data.ArrayStore({
			        id: 0,
			        fields: [
			            'value',
			            'text'
			        ],
			        data: status['list']
			    }),
		 valueField:'value',
		 displayField:'text',
		 listeners: {        
		 				select: {            
		 							fn:function(combo, value) {
		 								var modelDest = Ext.getCmp('unselected');                
		 								modelDest.store.removeAll();                
		 								
		 								var selectedValues = new Array();
		 								var grid = Ext.getCmp('selected').store.data;
		 								// console.log(grid.length);
		 								for (var i = 0 ; i < grid.length ;i++) 
		 								{
		 									selectedValues.push(grid.get(i).get('value'));
										}
										
		 								modelDest.store.reload({                    
		 								params: { 
		 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
		 									id:Ext.getCmp('id').getValue(),
		 									name:Ext.getCmp('name').getValue(),
		 									status:Ext.getCmp('status').getValue(),
		 									manager:Ext.getCmp('manager').getValue(),
		 									editor:Ext.getCmp('editor').getValue(),
		 									fromDate:Ext.getCmp('fromDate').getValue(),
		 									toDate:Ext.getCmp('toDate').getValue(),
		 									selectedObjects: selectedValues.join(', ')
											}                
										});	
									}        
								}	
					}
	});
	
	var managerFilter = new Ext.form.ComboBox( {
		typeAhead:true,
      	 triggerAction:'all',
      	 mode:'local',
        fieldLabel: manager['label'], 
        id:'manager', 
        //hiddenName:'manager_hidden', 
        xtype: 'combo',
        editable: false,
        store: new Ext.data.ArrayStore({
			        id: 0,
			        fields: [
			            'value',
			            'text'
			        ],
			        data: manager['list']
			    }),
		 valueField:'value',
		 displayField:'text',
		 listeners: {        
		 				select: {            
		 							fn:function(combo, value) {
		 								var modelDest = Ext.getCmp('unselected');                
		 								modelDest.store.removeAll();                
		 								
		 								var selectedValues = new Array();
		 								var grid = Ext.getCmp('selected').store.data;
		 								// console.log(grid.length);
		 								for (var i = 0 ; i < grid.length ;i++) 
		 								{
		 									selectedValues.push(grid.get(i).get('value'));
										}
										
		 								modelDest.store.reload({                    
		 								params: { 
		 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
		 									id:Ext.getCmp('id').getValue(),
		 									name:Ext.getCmp('name').getValue(),
		 									status:Ext.getCmp('status').getValue(),
		 									manager:Ext.getCmp('manager').getValue(),
		 									editor:Ext.getCmp('editor').getValue(),
		 									fromDate:Ext.getCmp('fromDate').getValue(),
		 									toDate:Ext.getCmp('toDate').getValue(),
		 									selectedObjects: selectedValues.join(', ')
											}                
										});	
									}        
								}	
					}
	});
	
	var editorFilter = new Ext.form.ComboBox( {
		typeAhead:true,
      	 triggerAction:'all',
      	 mode:'local',
        fieldLabel: editor['label'], 
        id:'editor', 
        //hiddenName:'editor_hidden', 
        xtype: 'combo',
        editable: false,
        store: new Ext.data.ArrayStore({
			        id: 0,
			        fields: [
			            'value',
			            'text'
			        ],
			        data: editor['list']
			    }),
		 valueField:'value',
		 displayField:'text',
		 listeners: {        
		 				select: {            
		 							fn:function(combo, value) {
		 								var modelDest = Ext.getCmp('unselected');                
		 								modelDest.store.removeAll();                
		 								
		 								var selectedValues = new Array();
		 								var grid = Ext.getCmp('selected').store.data;
		 								// console.log(grid.length);
		 								for (var i = 0 ; i < grid.length ;i++) 
		 								{
		 									selectedValues.push(grid.get(i).get('value'));
										}
										
		 								modelDest.store.reload({                    
		 								params: { 
		 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
		 									id:Ext.getCmp('id').getValue(),
		 									name:Ext.getCmp('name').getValue(),
		 									status:Ext.getCmp('status').getValue(),
		 									manager:Ext.getCmp('manager').getValue(),
		 									editor:Ext.getCmp('editor').getValue(),
		 									fromDate:Ext.getCmp('fromDate').getValue(),
		 									toDate:Ext.getCmp('toDate').getValue(),
		 									selectedObjects: selectedValues.join(', ')
											}                
										});	
									}        
								}	
					}
	});
	
	var fullFilter = new Array(objecttypeFilter, idFilter, nameFilter, statusFilter, managerFilter, editorFilter, fromtoDateFilter(from, to));

	return fullFilter;
}

function fromtoDateFilter(from, to)
{
	var dateFilter = new Ext.Panel({
		layout:'column',
        border: false,
		items        : [
        				 {
        				 width:310,
						 layout: 'form',
						 border: false,
						 items: [
				           { 
					       	 id: 'fromDate',
				             xtype: 'datefield',
							 fieldLabel: from['label'],
							 format: 'd.m.Y',
		            		 editable: true,
		            		 listeners: {        
							 				select: {            
							 							fn:function(combo, value) {
							 								var modelDest = Ext.getCmp('unselected');                
							 								modelDest.store.removeAll();                
							 								
							 								var selectedValues = new Array();
							 								var grid = Ext.getCmp('selected').store.data;
							 								//console.log(grid.length);
							 								for (var i = 0 ; i < grid.length ;i++) 
							 								{
							 									selectedValues.push(grid.get(i).get('value'));
															}
															
							 								modelDest.store.reload({                    
							 								params: { 
							 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
							 									id:Ext.getCmp('id').getValue(),
							 									name:Ext.getCmp('name').getValue(),
							 									status:Ext.getCmp('status').getValue(),
							 									manager:Ext.getCmp('manager').getValue(),
							 									editor:Ext.getCmp('editor').getValue(),
							 									fromDate:Ext.getCmp('fromDate').getValue(),
							 									toDate:Ext.getCmp('toDate').getValue(),
							 									selectedObjects: selectedValues.join(', ')
																}                
															});	
														}        
													},
												change: {            
							 							fn:function(combo, value) {
							 								var modelDest = Ext.getCmp('unselected');                
							 								modelDest.store.removeAll();                
							 								
							 								var selectedValues = new Array();
							 								var grid = Ext.getCmp('selected').store.data;
							 								//console.log(grid.length);
							 								for (var i = 0 ; i < grid.length ;i++) 
							 								{
							 									selectedValues.push(grid.get(i).get('value'));
															}
															
							 								modelDest.store.reload({                    
							 								params: { 
							 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
							 									id:Ext.getCmp('id').getValue(),
							 									name:Ext.getCmp('name').getValue(),
							 									status:Ext.getCmp('status').getValue(),
							 									manager:Ext.getCmp('manager').getValue(),
							 									editor:Ext.getCmp('editor').getValue(),
							 									fromDate:Ext.getCmp('fromDate').getValue(),
							 									toDate:Ext.getCmp('toDate').getValue(),
							 									selectedObjects: selectedValues.join(', ')
																}                
															});	
														}        
													}
										}
					       }
					      ]},
					       {
        				 layout: 'form',
						 border: false,
						 items: [
				           { 
					       	 id: 'toDate',
				             xtype: 'datefield',
							 fieldLabel: to['label'],
							 itemCls: 'date_label_to_style',
							 labelStyle: 'width:20px;',
							 format: 'd.m.Y',
		            		 editable: true,
		            		 listeners: {        
							 				select: {            
							 							fn:function(combo, value) {
							 								var modelDest = Ext.getCmp('unselected');                
							 								modelDest.store.removeAll();                
							 								
							 								var selectedValues = new Array();
							 								var grid = Ext.getCmp('selected').store.data;
							 								//console.log(grid.length);
							 								for (var i = 0 ; i < grid.length ;i++) 
							 								{
							 									selectedValues.push(grid.get(i).get('value'));
															}
															
							 								modelDest.store.reload({                    
							 								params: { 
							 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
							 									id:Ext.getCmp('id').getValue(),
							 									name:Ext.getCmp('name').getValue(),
							 									status:Ext.getCmp('status').getValue(),
							 									manager:Ext.getCmp('manager').getValue(),
							 									editor:Ext.getCmp('editor').getValue(),
							 									fromDate:Ext.getCmp('fromDate').getValue(),
							 									toDate:Ext.getCmp('toDate').getValue(),
							 									selectedObjects: selectedValues.join(', ')
																}                
															});	
														}        
													},
													change: {            
							 							fn:function(combo, value) {
							 								var modelDest = Ext.getCmp('unselected');                
							 								modelDest.store.removeAll();                
							 								
							 								var selectedValues = new Array();
							 								var grid = Ext.getCmp('selected').store.data;
							 								//console.log(grid.length);
							 								for (var i = 0 ; i < grid.length ;i++) 
							 								{
							 									selectedValues.push(grid.get(i).get('value'));
															}
															
							 								modelDest.store.reload({                    
							 								params: { 
							 									objecttype_id: Ext.getCmp('objecttype_id').getValue(),
							 									id:Ext.getCmp('id').getValue(),
							 									name:Ext.getCmp('name').getValue(),
							 									status:Ext.getCmp('status').getValue(),
							 									manager:Ext.getCmp('manager').getValue(),
							 									editor:Ext.getCmp('editor').getValue(),
							 									fromDate:Ext.getCmp('fromDate').getValue(),
							 									toDate:Ext.getCmp('toDate').getValue(),
							 									selectedObjects: selectedValues.join(', ')
																}                
															});	
														}        
													}	
										}
					       }
					       ]}
						]
	});
	
	return dateFilter;

}