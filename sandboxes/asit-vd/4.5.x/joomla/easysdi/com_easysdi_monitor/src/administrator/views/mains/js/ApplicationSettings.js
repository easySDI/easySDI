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

   //Url to the proxy for cross scripting
   //You normally do not have to change this!
   EasySDI_Mon.proxy = '../index.php?option=com_easysdi_monitor&view=proxy&proxy_url=';
   EasySDI_Mon.proxyserverside = '?option=com_easysdi_monitor&view=proxy&proxy_url=';
   
   //EasySDI_Mon.proxy = '/web2/administrator/components/com_easysdi_monitor/views/proxy/tmpl/default.php?proxy_url=';
   
   //Default application height
   EasySDI_Mon.appHeight = 500;
   
   //Default tab the application should display after loading
   //0=Jobs, 1=Reports, 2=Alerts, 3=State, 4=Maintenance
   EasySDI_Mon.defaultTab = 0;
   
   //Default theme for the graphs: grid, skies, gray, dark-blue, dark-green
   EasySDI_Mon.theme = '';
   
   //Format used for datetime in grids
   EasySDI_Mon.dateTimeFormat = 'd.m.Y H:i:s';
   
   //Format used for dates in grids
   EasySDI_Mon.dateFormat = 'd.m.Y';
   
   //Default job collection you want to work with
   //private collection: adminJobs
   //public collection:  jobs
   EasySDI_Mon.DefaultJobCollection = 'jobs';
   EasySDI_Mon.MonitorRoot = "index.php?option=com_easysdi_monitor";
   //EasySDI_Mon.DefaultExportCollection = 'exportTypes';