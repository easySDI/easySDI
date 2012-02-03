//inits the language
Ext.onReady(function(){
	Ext.QuickTips.init();

	var url = String.format("./components/com_easysdi_publish/js/styler/externals/ext/src/locale/ext-lang-{0}.js", EasySDI_Pub.locale);
	Ext.Ajax.request({
		url: url,
		success: function(response, opts){
		eval(response.responseText);
	},
	failure: function(){
		Ext.Msg.alert('Failure', EasySDI_Pub.lang.getLocal('error_lang')+' "'+EasySDI_Pub.locale+'"');
	},
	scope: this 
	});

});