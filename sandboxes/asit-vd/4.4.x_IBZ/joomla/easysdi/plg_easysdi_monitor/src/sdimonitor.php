<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');
jimport('joomla.application.component.helper');

class plgContentSdiMonitor extends JPlugin {

    var $_plgMonitorNbr = 0;

    function _setSdiMonitorPluginNumber() {
        $this->_plgMonitorNbr = (int) $this->_plgMonitorNbr + 1;
    }

    function plgContentSdiMonitor(&$subject, $params) {
        parent::__construct($subject, $params);
    }

    public function onContentPrepare($context, &$article, &$params, $page = 0) {

        // Start Plugin
        $regex_one = '/({sdimonitor\s*)(.*?)(})/si';
        $regex_all = '/{sdimonitor\s*.*?}/si';
        $matches = array();
        $count_matches = preg_match_all($regex_all, $article->text, $matches, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

        // Start if count_matches

        if ($count_matches != 0) {

            //get document
            $document = &JFactory::getDocument();

            //get lang from backend
            $language = &JFactory::getLanguage();
            $language->load('com_easysdi_monitor', JPATH_ADMINISTRATOR);

            //add factory if plugin used outside easySDI (like in content...)
            require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/factory/sdifactory.php';

            if (!JComponentHelper::isEnabled('com_easysdi_monitor', true)) {
                JText::_('Sdi Monitor Plugin requires EasySDI Monitor Component');
                return true;
            }

            for ($i = 0; $i < $count_matches; $i++) {

                $this->_setSdiMonitorPluginNumber();

                $job = '';

                // Get plugin parameters
                $sdimonitors = $matches[0][$i][0];
                preg_match($regex_one, $sdimonitors, $sdimonitors_parts);
                $parts = explode("|", $sdimonitors_parts[2]);
                $values_replace = array("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");

                // Browse plugin occurences
                foreach ($parts as $key => $value) {
                    $values = explode("=", $value, 2);
                    foreach ($values_replace as $key2 => $values2) {
                        $values = preg_replace($values2, '', $values);
                    }
                    // Get plugin parameters from article
                    if ($values[0] == 'job') {
                        $job = $values[1];
                    }
                }

                //Link librairie and css on the first match
                $output = '';
                if ($i != 0) {
                    $output = '';
                } else {
                    $document = JFactory::getDocument();
                    $document->addScript(Juri::root(true) . '/components/com_easysdi_core/libraries/ext/adapter/ext/ext-base.js');
                    $document->addScript(Juri::root(true) . '/components/com_easysdi_core/libraries/ext/ext-all.js');
                    $document->addStyleSheet(Juri::root(true) . '/components/com_easysdi_monitor/assets/css/easysdi_monitor.css?v=' . sdiFactory::getSdiFullVersion());
                }
                // div for Monitor plugin displayed
                $output .= '<div id="sdimon-box' . $i . '" class="sdimonitor-box" align="center"></div>';

                $output .= "
				 <script>
				 Ext.onReady(function(){
				  Ext.Ajax.request({
					url: 'index.php',
					method:'GET',
					params : {
				 	   option: 'com_easysdi_monitor',
				           view: 'proxy',
				 	   proxy_url: 'jobs/$job/status'
				 	},
					success: function(response){
					   var jsonResp = Ext.util.JSON.decode(response.responseText);
					   if(jsonResp.data.statusCode == 'AVAILABLE'){
					       document.getElementById('sdimon-box$i').className = 'icon-gridrenderer-available';
					       document.getElementById('sdimon-box$i').title = '" . JText::_('COM_EASYSDI_MONITOR_SERVICE_AVAILABLE') . "'
					   }
					   else if(jsonResp.data.statusCode == 'FAILURE'){
					       document.getElementById('sdimon-box$i').className = 'icon-gridrenderer-failure';
					       document.getElementById('sdimon-box$i').title = '" . JText::_('COM_EASYSDI_MONITOR_SERVICE_FAILURE') . "'
					   }
					   else if(jsonResp.data.statusCode == 'UNAVAILABLE'){
					       document.getElementById('sdimon-box$i').className = 'icon-gridrenderer-unavailable';
					       document.getElementById('sdimon-box$i').title = '" . JText::_('COM_EASYSDI_MONITOR_SERVICE_UNAVAILABLE') . "'
					   }
					}
				   });
				});
				</script>";

                $article->text = preg_replace($regex_all, html_entity_decode($output), $article->text, 1);
            }
        }// end if count_matches
        return true;
    }
}
?>