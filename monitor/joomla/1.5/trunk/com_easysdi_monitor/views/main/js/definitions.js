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

Ext.BLANK_IMAGE_URL = './components/com_easysdi_monitor/lib/ext/resources/images/default/s.gif';	

Ext.onReady(function(){
   
   EasySDI_Mon.proxy = '?option=com_easysdi_monitor&view=proxy&proxy_url=';
   
   //default, grid, skies, gray, dark-blue, dark-green
   EasySDI_Mon.theme = 'grid';
   
   EasySDI_Mon.ServiceMethodStore = { 
       wms: [
              ['GetCapabilities'],
   	   ['GetMap']
   	 ],
       wfs: [
              ['GetCapabilities'],
   	   ['GetFeature']
   	 ],
       wmts: [
              ['GetCapabilities'],
   	   ['GetTile']
   	 ],
       csw: [
              ['GetCapabilities'],
   	   ['GetRecordById'],
   	   ['GetRecords']
   	 ],
       sos: [
              ['GetCapabilities'],
   	   ['DescribeSensor']
   	 ],
       wcs: [
              ['GetCapabilities'],
   	   ['GetCoverage']
   	 ]
   }
   
   EasySDI_Mon.HttpMethodStore = [
                             ['GET'],
   		          ['POST']
   		       ];
   
   EasySDI_Mon.OgcServiceStore = [
   		         ['WMS'],
   			 ['WFS'],
   			 ['WMTS'],
   			 ['CSW'],
   			 ['SOS'],
   			 ['WCS']
   		       ];
   
   EasySDI_Mon.RepPeriodStore = [
   		         [EasySDI_Mon.lang.getLocal('today'),'today'],
   			 [EasySDI_Mon.lang.getLocal('yesterday'),'yesterday'],
   			 [EasySDI_Mon.lang.getLocal('last week'),'lastweek'],
   			 [EasySDI_Mon.lang.getLocal('this month'),'thismonth'],
   			 [EasySDI_Mon.lang.getLocal('past 6 months'),'past6months'],
   			 [EasySDI_Mon.lang.getLocal('past year'),'pastyear'],
   			 [EasySDI_Mon.lang.getLocal('all'),'All']
   		       ];
   		       
   EasySDI_Mon.DefaultJob = {
   		name:'',
   		httpMethod:'GET',
   		serviceType:'WMS',
   		url:'http://',
   		login:'',
   		password:'',
   		testInterval:3600,
   		timeout:5,
   		isPublic:'true',
   		isAutomatic:true,
   		allowsRealTime:'true',
   		triggersAlerts:'true',
   		slaStartTime:'08:00:00',
   		slaEndTime:'18:00:00',
   		httpErrors:'true',
   		bizErrors:'true'
   };
   
   EasySDI_Mon.DefaultReq = {
   		name:'',
   		serviceMethod:'',
   		params:''
   };
   
   
   EasySDI_Mon.YesNoCombo = [[EasySDI_Mon.lang.getLocal('YES'), 'Y'],[EasySDI_Mon.lang.getLocal('NO'), 'N']];
   
   /*common renderers*/
   
   EasySDI_Mon.TrueFalseRenderer = function(value) {
         return value ? '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-boolean-true"/></td></tr></table>': '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-boolean-false"/></td></tr></table>';  
   };
   
   //status renderer (available, failure, unavailable, untested)
   EasySDI_Mon.StatusRenderer = function(status){
              switch (status){
                 case 'Disponible':
                       return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-available"/></td></tr></table>';
                 break;
                 case 'En dérangement':
                       return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-failure"/></td></tr></table>';
                 break;
                 case 'Indisponible':
                       return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-unavailable"/></td></tr></table>';
                 break;
   	      case 'Non testé':
                       return '<table width="100%"><tr><td align="center"><div class="icon-gridrenderer-untested"/></td></tr></table>';
                 break;
                 default: 
                    return status;
                 break;
            }
   };
   
   EasySDI_Mon.AlertStatusRenderer = function (newStatus, scope, row) {
      var oldStatus = row.get('oldStatus');
             switch (newStatus){
                case 'Disponible':
   	 if(oldStatus == 'En dérangement')
                      return '<div class="icon-gridrenderer-failure-to-available"/>';
            if(oldStatus == 'Indisponible')
                      return '<div class="icon-gridrenderer-unavailable-to-available"/>';
                break;
                case 'En dérangement':
   	  if(oldStatus == 'Disponible')
                      return '<div class="icon-gridrenderer-available-to-failure"/>';
             if(oldStatus == 'Indisponible')
                      return '<div class="icon-gridrenderer-unavailable-to-failure"/>';
                break;
                case 'Indisponible':
   	 if(oldStatus == 'En dérangement')
                      return '<div class="icon-gridrenderer-failure-to-unavailable"/>';
            if(oldStatus == 'Disponible')
                      return '<div class="icon-gridrenderer-available-to-unavailable"/>';
                break;
                default: 
                   return value;
                break;
            }
   };
   
   EasySDI_Mon.DelayRenderer = function(value){
      return Math.round(value * 1000);
   };
   
   EasySDI_Mon.DateTimeRenderer = function (value){
      return Ext.util.Format.date(value,'d-m-Y H:i:s');
   };
   
   EasySDI_Mon.App = new Ext.App({});
});

