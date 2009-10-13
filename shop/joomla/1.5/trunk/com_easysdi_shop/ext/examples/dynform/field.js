Ext.onReady(function(){
    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    var store = new Ext.data.SimpleStore({
        fields: ['abbr', 'state', 'nick'],
        data : Ext.exampledata.states // from states.js
    });

	var company = new Ext.FormPanel({
		frame:true,
        defaultType: 'textfield',		
        items: [{	fieldLabel: 'Company Name',
					name: 'name',
					nameSpace : 'company',
					labelSeparator  : '',
					width: 230,
					allowBlank:false,
					dynamic:true,
					maxOccurs:5,
					listeners : { 'maxoccurs' : {fn: function(field) {
													Ext.example.msg('maxoccurs', 'Implement behaviour of maxoccurs');
											  }}											 
								}							
				}				],
        buttons: [{text: 'Save'},{text: 'Cancel'}]
    });	
	var person = new Ext.FormPanel({		
		id:'panel',
		frame:true,
        defaultType: 'textfield',		
        items: [{dynamic : true,
					maxOccurs:5,
                        xtype : 'fieldset',
						title: 'Just a set of fields',
						id : 'fs',
						nameSpace:'person',
                        autoHeight:true,
						width: 300,
                        defaultType: 'textfield',
						listeners : { 'maxoccurs' : {fn: function(fieldset) {
													Ext.example.msg('maxoccurs', 'Implement behaviour of maxoccurs');
											  }}											 
									},							
						items :[
								{
                                    fieldLabel: 'First Name',
                                    name: 'first',
									dynamic:true,
									maxOccurs:5,
									listeners : { 'maxoccurs' : {fn: function(field) {
																Ext.example.msg('maxoccurs', 'Implement behaviour of maxoccurs');
														  }}											 
												},							
									width : 150
								},
								{
                                    fieldLabel: 'Last Name',
                                    name: 'last',
									width : 150
                                }
								]
				},		
			    {	fieldLabel: 'State',
				    name : 'state',
					nameSpace : 'location',
					labelStyle: 'width:75px;',
					store: store,
					displayField:'state',
					typeAhead: true,
					mode: 'local',
					forceSelection: true,
					triggerAction: 'all',
					emptyText:'Select a state...',
					selectOnFocus:true,
					xtype:'combo',
					labelSeparator  : '',
					width: 230,
					allowBlank:false,
					dynamic:true,
					maxOccurs:5,
					listeners : { 'maxoccurs' : {fn: function(field) {
													Ext.example.msg('maxoccurs', 'Implement behaviour of maxoccurs');
											  }}											 
								}					
				},
				{	fieldLabel: 'Birth date',
					name: 'birthDate',
					nameSpace : 'person',
					allowBlank:false,
					xtype:'datefield',
					labelSeparator  : '',
					width: 230,
					dynamic:true,
					maxOccurs:5,
					listeners : { 'maxoccurs' : {fn: function(field) {
													Ext.example.msg('maxoccurs', 'Implement behaviour of maxoccurs');
											  }}											 
								}					
				}
				],
			buttons: [{text: 'GetValues'
					,handler : function(b,e) {				  
						  var panel = Ext.getCmp('panel');
						  var location = panel.extract('location','field');
						  var fsperson = panel.extract('person','fieldset');
						  var fperson = panel.extract('person','field');
						  Ext.Msg.alert('Status', 'nameSpace location of field'+Ext.encode(location)+' nameSpace person of fieldset'+Ext.encode(fsperson)+' nameSpace person of field'+Ext.encode(fperson));
				  }
				}
				]
		});	
	var tabs = new Ext.TabPanel({
		width:450,
		defaults:{autoHeight: true},
		activeTab: 0,
		items: [
				{title:'Person',id: 'person' ,items: [person]},
				{title:'Company',id: 'company' ,items: [company]}
				]

	});
	
	tabs.render(document.body);

	//
	// you can change the amount of clones on the fly 
	// fieldSet.clones(2);
	// person.getForm().findField('state').clones(1);
	// person.doLayout();	
	
	person.populate(Ext.decode('{"state":["Netherlands","Delaware"]}'),'location','field');
	person.populate(Ext.decode('[{"first":["Adriaan","Cornelis"],"last":"Zaanen"},{"first":["Bill"],"last":"Joy"}]'),'person','fieldset');
	person.populate(Ext.decode('{"birthDate":"03/12/2009"}'),'person','field');
	company.populate(Ext.decode('{"name":["UCan","Informa"]}'),'company','field');	
	person.doLayout();
	company.doLayout();

	// you can retrieve the current set of clones 
	var curr = Ext.getCmp('fs').clones();	
});