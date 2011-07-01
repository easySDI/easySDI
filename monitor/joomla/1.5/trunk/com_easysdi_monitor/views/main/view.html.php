<?php
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
 

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
class MonitorViewmain extends JView
{
    function display($tpl = null)
    {
  //Toolbar init
  JToolBarHelper::title(JText::_( 'EASYSDI_MONITOR_MENU_STATE' ), 'generic.png');
  //JToolBarHelper::custom( 'task', 'icon', 'icon over', 'alt', boolean, boolean );
  JToolBarHelper::help(null);
  JToolBarHelper::back();
  
  $document = & JFactory::getDocument();
  
  //Load css files
  $document->addStyleSheet('components/com_easysdi_monitor/lib/ext/resources/css/ext-all.css');
  $document->addStyleSheet('components/com_easysdi_monitor/lib/ext/resources/css/xtheme-gray.css');
  $document->addStyleSheet('components/com_easysdi_monitor/lib/ext/examples/shared/examples.css');
  $document->addStyleSheet('components/com_easysdi_monitor/lib/ext/examples/ux/css/RowEditor.css');
  $document->addStyleSheet('components/com_easysdi_monitor/css/monitor.css');
  
  //Common js lib
  $document->addScript('components/com_easysdi_monitor/lib/jquery/jquery.min.js');
  $document->addScript('components/com_easysdi_monitor/lib/ext/adapter/ext/ext-base.js');
  $document->addScript('components/com_easysdi_monitor/lib/ext/ext-all.js');
  $document->addScript('components/com_easysdi_monitor/lib/Highcharts-2.0.3/js/highcharts.js');
  $document->addScript('components/com_easysdi_monitor/lib/Highcharts-2.0.3/js/modules/exporting.js');
  $document->addScript('components/com_easysdi_monitor/lib/ext/examples/ux/RowEditor.js');
  $document->addScript('components/com_easysdi_monitor/lib/ext/examples/ux/RowExpander.js');
  $document->addScript('components/com_easysdi_monitor/ApplicationSettings.js');
  
  //minified files -> use for prod
  $document->addScript('components/com_easysdi_monitor/script/Monitor.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/themes.js');

  
  //Unminimized files -> use for developpment
  /**
  //Monitor libs
  $document->addScript('components/com_easysdi_monitor/views/main/js/language.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/vtypes.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/app/App.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/definitions.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/jobManager.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/jobRequest.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/jobAlert.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/reports.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/alerts.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/state.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/maintenance.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/reports.js');
  $document->addScript('components/com_easysdi_monitor/views/main/js/monitor.js');
     //graphs
     $document->addScript('components/com_easysdi_monitor/views/main/js/graphs/healthGraphRaw.js');
     $document->addScript('components/com_easysdi_monitor/views/main/js/graphs/healthGraphAgg.js');
     $document->addScript('components/com_easysdi_monitor/views/main/js/graphs/healthLineGraph.js');
     $document->addScript('components/com_easysdi_monitor/views/main/js/graphs/responseTimeGraph.js');
  
  //themes
  $document->addScript('components/com_easysdi_monitor/views/main/js/themes.js');
*/
  
    
        parent::display($tpl);
    }
}
