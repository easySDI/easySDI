<?php
/**
 * @package    Joomla.Tutorials
 * @subpackage Components
 * @link http://docs.joomla.org/Developing_a_Model-View-Controller_Component_-_Part_1
 * @license    GNU/GPL
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
/**
 * HTML View class for the HelloWorld Component
 *
 * @package    HelloWorld
 */
 
class MonitorViewmain extends JView
{
    function display($tpl = null)
    {
    	//Toolbar init
    	// JToolBarHelper::title(JText::_( 'EASYSDI_MONITOR_MENU_JOB_LIST' ), 'generic.png');
	//JToolBarHelper::deleteList();
	//JToolBarHelper::editListX();
	
	$document	= & JFactory::getDocument();
	
	//Load internationalization
	$document->addScript('components/com_easysdi_monitor/views/main/js/i18n.js');
	
	//JQuery needed by Highcharts
	//Attention, youu need to add "jQuery.noConflict();" at the end of this file or
	//you'll get conflicts with $() accessor.
	//This file comes from: http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js
	$document->addScript('components/com_easysdi_monitor/lib/jquery/jquery.min.js');
	
	//Lib ext
	$document->addStyleSheet('components/com_easysdi_monitor/lib/ext/resources/css/ext-all.css');
	//$document->addStyleSheet('components/com_easysdi_monitor/lib/ext/resources/css/xtheme-gray.css');
	
	//JQuery adapter
        $document->addScript('components/com_easysdi_monitor/lib/ext/adapter/ext/ext-base.js');
        $document->addScript('components/com_easysdi_monitor/lib/ext/ext-all.js');
	
	//ExtJS adapter for Highcharts
        //$document->addScript('components/com_easysdi_monitor/lib/hChartsExt/adapter-extjs.js');
	
	
	//Highcharts libs
        $document->addScript('components/com_easysdi_monitor/lib/Highcharts-2.0.3/js/highcharts.js');
        $document->addScript('components/com_easysdi_monitor/lib/Highcharts-2.0.3/js/modules/exporting.js');
        //$document->addScript('components/com_easysdi_monitor/lib/hChartsExt/highcharts-1-2-5-src.js');

	
	//ExtJS Plugin for Highcharts
	//$document->addScript('components/com_easysdi_monitor/lib/hChartsExt/Ext.ux.HighChart.js');
	
	//lib example
        $document->addScript('components/com_easysdi_monitor/lib/ext/examples/shared/extjs/App.js');
	$document->addStyleSheet('components/com_easysdi_monitor/lib/ext/examples/shared/examples.css');

	//Built-in language for ext, local read in Joomla.
	$document->addScript('components/com_easysdi_monitor/views/main/js/language.js');
	
	//row editor
	$document->addScript('components/com_easysdi_monitor/lib/ext/examples/ux/RowEditor.js');
	$document->addStyleSheet('components/com_easysdi_monitor/lib/ext/examples/ux/css/RowEditor.css');
	$document->addStyleSheet('components/com_easysdi_monitor/css/monitor.css');

	//has some conflicts with mootools
	//$document->addScript('components/com_easysdi_monitor/lib/jquery/jquery-1.4.2.js');
  

	//Monitor libs
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
    
    
       //$greeting = "Hello Monitor!";
        //$this->assignRef( 'greeting', $greeting );
        parent::display($tpl);
    }
}
