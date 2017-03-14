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
	Ext.QuickTips.init();

	var url = String.format("../components/com_easysdi_core/libraries/ext/src/locale/ext-lang-{0}.js", EasySDI_Mon.locale);
	Ext.Ajax.request({
		url: url,
		success: function(response, opts){
		eval(response.responseText);
	},
	failure: function(){
		Ext.Msg.alert('Failure', EasySDI_Mon.lang.getLocal('error_lang')+' "'+EasySDI_Mon.locale+'"');
	},
	scope: this 
	});

});