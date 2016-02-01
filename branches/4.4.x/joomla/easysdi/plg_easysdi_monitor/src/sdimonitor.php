<?php
/**
 * @version     4.3.2
 * @package     com_easysdi_monitor
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.application.component.helper' );

$language=&JFactory::getLanguage();
$lang = $language->load('com_easysdi_monitor', JPATH_SITE);

class plgContentSdiMonitor extends JPlugin
{	
	var $_plgMonitorNbr	= 0;
	
	function _setSdiMonitorPluginNumber() {
		$this->_plgMonitorNbr = (int)$this->_plgMonitorNbr + 1;
	}
	
	function plgContentSdiMonitor( &$subject, $params ) {
        parent::__construct( $subject, $params  );
    }

        public function onContentPrepare($context, &$article, &$params, $page = 0){
                
		// Start Plugin
		$regex_one		= '/({sdimonitor\s*)(.*?)(})/si';
		$regex_all		= '/{sdimonitor\s*.*?}/si';
		$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$article->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);
		
		// Start if count_matches
		
		if ($count_matches != 0) {
		
			if (!JComponentHelper::isEnabled('com_easysdi_monitor', true)) {
				JText::_('Sdi Monitor Plugin requires EasySDI Monitor Component');
				return true;
			}
		
			$document		= &JFactory::getDocument();
			$db 			= &JFactory::getDBO();
			$menu 			= &JSite::getMenu();			
			$plugin 		= &JPluginHelper::getPlugin('content', 'sdimonitor');
                        //var_dump($plugin);
			//$paramsPlugin 	= new JParameter( $plugin->params );
			
			

			for($i = 0; $i < $count_matches; $i++) {
				
				$this->_setSdiMonitorPluginNumber();
				$id	= 'PlgSDIMonitor'.(int)$this->_plgMonitorNbr;
				
				$job	= '';
				$text	= '';
				//$lang   = '';
				
				// Get plugin parameters
				$sdimonitors	= $matches[0][$i][0];
				preg_match($regex_one,$sdimonitors,$sdimonitors_parts);
				$parts = explode("|", $sdimonitors_parts[2]);
				$values_replace = array ("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");
								
				// Browse plugin occurences
				foreach($parts as $key => $value) {
					$values = explode("=", $value, 2);
					foreach ($values_replace as $key2 => $values2) {
						$values = preg_replace($values2, '', $values);
					}
					// Get plugin parameters from article
				        if($values[0]=='job')	{$job	= $values[1];}
				}
				
                            
                                
				// Load map using map id
				//$query = 'SELECT a.* FROM #__map  AS a WHERE a.id = '.(int) $idMap;
				//$db->setQuery($query);
				//$map = $db->loadObject();
				
				// Raise error if map id doesn't exist
				//if (empty($map)) {
				//	JError::raiseError('Ogcmaps Plugin Error', JText::_('Map does not exist') . ' (ID = '.$idMap.')');
				//}
				
				//Link librairie and css on the first match
                                $output = '';
				if($i != 0){
					$output = '';
				}else{
					$output .= '<script type="text/javascript" src="administrator/components/com_easysdi_monitor/libraries/ext/adapter/ext/ext-base.js"></script>';
					$output .= '<script type="text/javascript" src="administrator/components/com_easysdi_monitor/libraries/ext/ext-all.js"></script>';
					$output .= '<link rel="stylesheet" href="administrator/components/com_easysdi_monitor/assets/css/monitor.css" type="text/css" />'; 
				}
				// div for Monitor plugin displayed
				$output .= '<div id="sdimon-box'.$i.'" class="sdimonitor-box" align="center">';
				
			        /* 
        			 $output .= "
				 <script>
				 var url = 'administrator/index.php';
				 var params = {
				 	option: 'com_easysdi_monitor',
				               view: 'proxy',
				 	      proxy_url: '$collection/$job/status'
				 	      };
				 var req = new Json.Remote(url, {
				 	onComplete: function(jsonObj){
					   alert(jsonObj);
				 	}
				 }).request(params);
				 </script>";
	                        */
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
					       document.getElementById('sdimon-box$i').title = '".JText::_('MONITOR_SERVICE_AVAILABLE')."'
					   }
					   else if(jsonResp.data.statusCode == 'FAILURE'){
					       document.getElementById('sdimon-box$i').className = 'icon-gridrenderer-failure';
					       document.getElementById('sdimon-box$i').title = '".JText::_('MONITOR_SERVICE_FAILURE')."'
					   }
					   else if(jsonResp.data.statusCode == 'UNAVAILABLE'){
					       document.getElementById('sdimon-box$i').className = 'icon-gridrenderer-unavailable';
					       document.getElementById('sdimon-box$i').title = '".JText::_('MONITOR_SERVICE_UNAVAILABLE')."'
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