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

	var rssRec;
	var emailRec;
	var rssCrVal='';
	var emailCrVal='';
	var emailTxtCrVal='';

	var proxy = new Ext.data.HttpProxy({
		url: EasySDI_Mon.proxy
	});

	var writer = new Ext.data.JsonWriter({
		encode: false   // <-- don't return encoded JSON -- causes Ext.Ajax#request to send data using jsonData config rather than HTTP params
	});

	var store = new Ext.data.JsonStore({
		id: 'actionId',
		//if the store is not coupled with a form or grid, use autosave=flase and call store.save().
		autoSave: false,
		root: 'data',
		restful:true,
		proxy: proxy,
		writer: writer,
		fields:['actionId', 'type', 'target']
	});

	var _alertForm = new Ext.FormPanel({
		id:'AlertForm',
		title: EasySDI_Mon.lang.getLocal('alerts'),
		labelWidth: 90,
		region:'center',
		bodyStyle:'padding:5px 5px 0',
		autoHeight:true,
		frame:true,
		defaults: {width: 200},
		defaultType: 'textfield',
		items: [{
			xtype:'combo',
			mode:'local',
			ref: 'cbEmail',
			disabled:true,
			triggerAction:  'all',
			forceSelection: true,
			editable:       false,
			fieldLabel:     EasySDI_Mon.lang.getLocal('grid header email'),
			name:           'email',
			displayField:   'name',
			valueField:     'value',
			store:          new Ext.data.SimpleStore({
				fields : ['name', 'value'],
				data : EasySDI_Mon.YesNoCombo
			})
		},{
			fieldLabel: '',
			disabled:true,
			ref: 'txtEmail',
			//value: strParams,
			name: 'email_list',
			allowBlank:true,
			xtype: 'textarea'
		},{
			xtype:'combo',
			mode:'local',
			ref: 'cbRss',
			iconCls:'icon-service-add',
			disabled:true,
			triggerAction:  'all',
			forceSelection: true,
			editable:       false,
			fieldLabel:     EasySDI_Mon.lang.getLocal('grid header rss'),
			name:           'rss',
			displayField:   'name',
			valueField:     'value',
			store:          new Ext.data.SimpleStore({
				fields : ['name', 'value'],
				data : EasySDI_Mon.YesNoCombo
			})
		},{
			xtype:'button',
			ref: 'btnRss',
			iconCls:'icon-rss',
			disabled:true,
			fieldLabel:     EasySDI_Mon.lang.getLocal('rss link'),
			width:20
		}],
		buttons: [{
			text: EasySDI_Mon.lang.getLocal('grid action update'),
			disabled:true,
			ref: '../btnUpdate',
			handler: function(){
			var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
			var name = rec.get('name');
			proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions');
			proxy.api.destroy.url = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions';
			proxy.api.create.url = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions';
			var fields = _alertForm.getForm().getFieldValues();
			//Rss notification
			if(fields.rss == 'Y'){
				//create record if it does not exit
				if(rssRec == null){
					//create a new record
					var u = new store.recordType({
						type: 'RSS',
						target: ''
					});
					store.insert(0, u);
					rssCrVal = 'Y';
				}
			}
			else
			{
				if(rssRec != null){
					//drop record
					store.remove(rssRec);
					rssCrVal = 'N';
				}
			}

			//User requests Email notification
			if(fields.email == 'Y'){
				//if it doesn't exist yet, create a record
				if(emailRec == null){
					var u = new store.recordType({
						type: 'E-MAIL',
						target: fields.email_list
					});
					store.insert(0, u);
					emailCrVal = 'Y';
				}else{
					//if it already exit, update the target
					emailRec.set('target', fields.email_list);
					emailTxtCrVal = fields.email_list;
				}
			}
			//User doesn't requests Email notification
			else
			{
				//drop record if it exist
				if(emailRec != null){
					store.remove(emailRec);
					emailCrVal = 'N';
				}
			}
			Ext.data.DataProxy.addListener('write', afterUpdate);
			store.save();
			_alertForm.btnUpdate.setDisabled(true);
		}//end update
		}]
	});

  //reload the store after update
  function afterUpdate(proxy, action, result, res, rs){
		Ext.data.DataProxy.removeListener('write', afterUpdate);
		var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		var name = rec.get('name');
		proxy.setUrl(EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions');
		proxy.api.read.url = EasySDI_Mon.proxy+EasySDI_Mon.CurrentJobCollection+'/'+name+'/actions';
		store.load();
	}

	Ext.getCmp('JobGrid').getSelectionModel().on('selectionchange', function(sm){
		//There is no job selected
		if(sm.getCount() < 1){
			_alertForm.cbRss.setDisabled(true);
			_alertForm.cbEmail.setDisabled(true);
			_alertForm.txtEmail.setDisabled(true);
			_alertForm.btnUpdate.setDisabled(true);
		}
		else
			//A job has been selected, load the grid
		{
			_alertForm.cbRss.setDisabled(false);
			_alertForm.cbEmail.setDisabled(false);
			_alertForm.txtEmail.setDisabled(false);

			var rec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
			var name = rec.get('name');
			//var serviceType = rec.get('serviceType');
			//Change the proxy to the good url
			if(rec.get('isPublic') == true)
				proxy.setUrl(EasySDI_Mon.proxy+'/jobs/'+name+'/actions');
			else
				proxy.setUrl(EasySDI_Mon.proxy+'/adminJobs/'+name+'/actions');

			store.load();
		}
	});

	store.on("load", function(store) {
		//Get the first occurence of type E-Mail and RSS
		var jobRec = Ext.getCmp('JobGrid').getSelectionModel().getSelected();
		var jobName = jobRec.get('name');
		rssRec = store.getAt(store.findExact('type', 'RSS'));
		emailRec =  store.getAt(store.findExact('type', 'E-MAIL'));
		//Set the value in the form
		_alertForm.txtEmail.setValue('');
		if(rssRec == null){
			_alertForm.btnRss.setDisabled(true);
			_alertForm.btnRss.handler = null;
			_alertForm.cbRss.setValue('N');
			rssCrVal = 'N';
		}else{
			_alertForm.btnRss.setDisabled(false);
			_alertForm.btnRss.handler = function(){
				window.open(EasySDI_Mon.proxy+'/jobs/'+jobName+'/alerts&alt=rss',EasySDI_Mon.lang.getLocal('rss title')+": "+jobName);
			};
			_alertForm.cbRss.setValue('Y');
			rssCrVal = 'Y';
		}
		if(emailRec == null){
			_alertForm.cbEmail.setValue('N');
			emailCrVal = 'N';
		}else{
			_alertForm.cbEmail.setValue('Y');
			emailCrVal = 'Y';
			_alertForm.txtEmail.setValue(emailRec.get('target'));
			emailTxtCrVal = emailRec.get('target');
		}
	});

	//Some ennoying event handler for the update button
	_alertForm.cbRss.on("change", function(field, newValue, oldValue) {
		if(newValue == 'N')
			_alertForm.btnRss.setDisabled(true);
		else
			_alertForm.btnRss.setDisabled(false);

		if(newValue != rssCrVal || _alertForm.cbEmail.getValue() != emailCrVal || _alertForm.txtEmail.getValue() != emailTxtCrVal)
			_alertForm.btnUpdate.setDisabled(false);
		else
			_alertForm.btnUpdate.setDisabled(true);
	});

	_alertForm.cbEmail.on("change", function(field, newValue, oldValue) {
		if(newValue != emailCrVal || _alertForm.cbRss.getValue() != rssCrVal || _alertForm.txtEmail.getValue() != emailTxtCrVal)
			_alertForm.btnUpdate.setDisabled(false);
		else
			_alertForm.btnUpdate.setDisabled(true);
	});

	_alertForm.txtEmail.on("change", function(field, newValue, oldValue) {
		if(newValue != emailTxtCrVal || _alertForm.cbEmail.getValue() != emailCrVal || _alertForm.cbRss.getValue() != rssCrVal)
			_alertForm.btnUpdate.setDisabled(false);
		else
			_alertForm.btnUpdate.setDisabled(true);
	});

});