function manageObjectLinkFilter(objecttype, id, name, status,version, manager, editor, from, to) {
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
		displayField : 'text'
	});

	var idFilter = new Ext.form.TextField( {
		xtype : 'textfield',
		id : 'id',
		fieldLabel : id['label'],
		enableKeyEvents : true
	});

	var nameFilter = new Ext.form.TextField( {
		xtype : 'textfield',
		id : 'name',
		fieldLabel : name['label'],
		enableKeyEvents : true
		
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
		 displayField:'text'
	});
	
	var versionFilter = new Ext.form.RadioGroup( {

        fieldLabel: version['label'], 
        id:'version', 
        vertical: false,
        items: [
                {boxLabel: 'Last', name: 'Last', inputValue: 'Last'},
                {boxLabel: 'All', name: 'All', inputValue: 'All', checked:true}
        ] 
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
		 displayField:'text'
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
		 displayField:'text'
	});
	
	var fullFilter = new Array(objecttypeFilter, idFilter, nameFilter, statusFilter,versionFilter, managerFilter, editorFilter, fromtoDateFilter(from, to));

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
		            		 editable: true
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
		            		 editable: true
					       }
					       ]}
						]
	});
	
	return dateFilter;

}