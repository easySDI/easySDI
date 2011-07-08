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

Ext.form.VTypes['jobnameMask'] = /[a-z0-9_-]/i;
var alphanummore = /^[a-zA-Z0-9_-]+$/;
Ext.form.VTypes['alphanummore'] = function(v)
{   
	return alphanummore.test(v);
}

Ext.form.VTypes['jobname'] = function(v)
{   
	if(Ext.form.VTypes['alphanummore'](v)){
		if( Ext.getCmp('JobGrid').store.getById(v)){
			Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('jobname already exists');
			return false;
		}
		if(v.toUpperCase() == 'ALL'){
			Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('error reserved keyword');
			return false;
		}
		return true;
	}else{
		Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('error ressource name');
		return false;
	}	
	return true;
}

Ext.form.VTypes['slaname'] = function(v)
{   
	if(Ext.form.VTypes['alphanummore'](v)){
		if( Ext.getCmp('SlaGrid').store.getById(v)){
			Ext.form.VTypes['slanameText'] = EasySDI_Mon.lang.getLocal('slaname already exists');
			return false;
		}
		/*if(v.toUpperCase() == 'ALL'){
			Ext.form.VTypes['jobnameText'] = EasySDI_Mon.lang.getLocal('error reserved keyword');
			return false;
		}*/
		return true;
	}else{
		Ext.form.VTypes['slanameText'] = EasySDI_Mon.lang.getLocal('error ressource name');
		return false;
	}
	return true;
}

Ext.form.VTypes['reqname'] = function(v)
{   
	if(Ext.form.VTypes['alphanum'](v)){
		if( Ext.getCmp('ReqGrid').store.getById(v)){
			Ext.form.VTypes['reqnameText'] = EasySDI_Mon.lang.getLocal('reqname already exists');
			return false;
		}
		if(v.toUpperCase() == 'ALL'){
			Ext.form.VTypes['reqnameText'] = EasySDI_Mon.lang.getLocal('error reserved keyword');
			return false;
		}
		return true;
	}else{
		Ext.form.VTypes['reqnameText'] = EasySDI_Mon.lang.getLocal('error ressource name');
		return false;
	}
	return true;
}


