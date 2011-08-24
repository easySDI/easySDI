<?php

/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

defined('_JEXEC') or die('Restricted access');

class HTML_ctrlpanel {
	function ctrlPanelCore($option, $panels){
			JToolBarHelper::title( JText::_(  'CORE_CPANEL_TITLE' ), 'easysdi.png' );
			global $mainframe;
			$lang		=& JFactory::getLanguage();
			$template	= $mainframe->getTemplate();
				
			?>
		<table width=100% border="0">
			<tr valign=top>
				<td width=60%>
					<table width=100% border="0">
						<tr>
							<td>
							        <table class="header_sdi_table" width="100%">
								   <tbody>
								      <tr>
								         <td class="header_sdi_comp_title">
									 <div class="header_sdi icon-48-core"><?php echo JText::_( 'CORE_CPANEL_CORE_LABEL' );?></div>
									 </td>
									 <td>&nbsp;</td>
								      </tr>
								      <tr>
								         <td colspan="2">
									    <table width="100%">
									      <tr>
								               <td class="header_sdi_comp_links">&nbsp;</td>
								               <td class="header_sdi_list">
									          <ul>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=ctrlPanelConfig">', $option); echo JText::_( 'CORE_CONFIGURATION_PANEL' ); ?></a></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=listResources">', $option); echo JText::_( 'CORE_RESSOURCES_PANEL' ); ?></a></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=listLanguage">', $option); echo JText::_( 'CORE_LANGUAGE_PANEL' ); ?></a></li>
									          </ul>
									       </td>
									       <td class="header_sdi_list">
									       <ul >
									             <li><?php printf('<a href="index.php?option=%s&amp;task=ctrlPanelAccountManager">', $option); echo JText::_( 'CORE_ACCOUNT_PANEL' ); ?></a></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=listAccountProfile">', $option); echo JText::_( 'CORE_ACCOUNTPROFILE_PANEL' ); ?></a></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=systemAccount&code=service">', $option); echo JText::_( 'CORE_SERVICE_ACCOUNT_PANEL' ); ?></a></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=systemAccount&code=guest">', $option); echo JText::_( 'CORE_GUEST_ACCOUNT_PANEL' ); ?></a></li>
									          </ul>
									       </td>
									       <td class="header_sdi_list">&nbsp;</td>
									       </tr>
									       </table>
									 </td>
								      </tr>
								    </tbody>
								 </table>
							</td>
						</tr>
<?php
	foreach ($panels as $panel)
	{
		include_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$panel->view_path);
	}
?>
						
	
					</table>
				</td>
				<td width="40%">
					<div id="configuration">
					<?php 
					
						$pane =& JPane::getInstance('sliders', array('startOffset'=>0, 'allowAllClose' => true));
						echo $pane->startPane("content-pane");
						
						//welcome panel
						echo $pane->startPanel( JText::_('SHOP_CPANEL_WELCOME_TITLE'), 'cpanel-panel-welcome' );
						?>
						
						<h3 class="contrib-sdi"><?php echo JText::_('SHOP_CPANEL_WELCOME_GZ_TITLE'); ?></h3>
						<p class="contrib-sdi">
						   <?php echo JText::_('SHOP_CPANEL_WELCOME_GZ_BODY'); ?>
						</p>
						
						<h3 class="contrib-sdi"><?php echo JText::_('SHOP_CPANEL_WELCOME_HELP_TITLE'); ?></h3>
						<p class="contrib-sdi">
						   <?php echo JText::_('SHOP_CPANEL_WELCOME_HELP_BODY'); ?>
						   <a href="http://forge.easysdi.org/projects/show/easysdi" target="_blank"> <?php echo JText::_('SHOP_CPANEL_WELCOME_HELP_MAIN_WIKI_LINK'); ?></a>
						   <br/>
						   <?php echo JText::_('SHOP_CPANEL_WELCOME_HELP_FOOTER'); ?>
						   <a href="http://forge.easysdi.org/projects/easysdi/boards" target="_blank"> <?php echo JText::_('SHOP_CPANEL_WELCOME_HELP_FORUM_LINK'); ?></a>
						   <?php echo JText::_('SHOP_CPANEL_WELCOME_HELP_FOOTER2'); ?>
						</p>
						
						<h3 class="contrib-sdi"><?php echo JText::_('SHOP_CPANEL_WELCOME_GI_TITLE'); ?></h3>
						<p class="contrib-sdi">
						   <?php echo JText::_('SHOP_CPANEL_WELCOME_GI_BODY'); ?>
						   <a href="http://www.easysdi.org/index.php/en/community/join-us" target="_blank"><?php echo JText::_('SHOP_CPANEL_WELCOME_GI_LINK'); ?></a>
						</p>
						
						<?php
						echo $pane->endPanel('cpanel-panel-welcome');
						
						//contributors panel
						echo $pane->startPanel( JText::_('SHOP_CPANEL_THANKS_TITLE'), 'cpanel-panel-contrib' );
						?>
						<br/>
						<table width="100%" class="contrib-sdi">
						   <tr>
						      <td><?php echo JText::_('SHOP_CPANEL_THANKS_BODY'); ?></td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- CORE -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_CORE'); ?></td>
						   </tr>
						   <tr>
						      <td>
						          <a href="http://de.siteof.de" target="_blank">Extended Menu for Joomla - version: 1.0.6</a>,&nbsp;
						          <a href="" target="_blank">dTree 2.05</a>,&nbsp;
						          <a href="http://www.mpdf1.com/mpdf/" target="_blank">mPDF V5.0</a>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- CATALOG -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_CATALOG'); ?></td>
						   </tr>
						   <tr>
						      <td>
						         <a href="http://www.sencha.com" target="_blank">EXT JS 3.2.1</a>,&nbsp;
							 <a href="http://dev.bnhelp.cz/trac/gemetclient/wiki" target="_blank">GemetClient</a>,&nbsp;
							 <a href="http://alexgorbatchev.com/" target="_blank">SyntaxHighlighter 2.1.364</a>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- SHOP -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_SHOP'); ?></td>
						   </tr>
						   <tr>
						      <td>
						         <a href="http://openlayers.org" target="_blank">Openlayers 2.8</a>,&nbsp;
							 <a href="http://proj4js.org/" target="_blank">Proj4js</a>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- PROXY -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_PROXY'); ?></td>
						   </tr>
						   <tr>
						      <td>
						         <a href="http://openlayers.org" target="_blank">Openlayers 2.10</a>,&nbsp;
							 <a href="http://proj4js.org/" target="_blank">Proj4js</a>,&nbsp;
							 <a href="http://ehcache.org/" target="_blank">Ehcache 2.0.1</a>,&nbsp;
							 <a href="http://static.springsource.org/spring-security/site/" target="_blank">Spring Secrurity</a>,&nbsp;
							 <a href="http://logging.apache.org/log4j/1.2/" target="_blank">Log4j 1.2</a>,&nbsp;
							 <a href="http://xmlgraphics.apache.org/fop/" target="_blank">FOP 0.95</a>,&nbsp;
							 <a href="http://www.geotools.org/" target="_blank">GeoTools 2.4.1</a>,&nbsp;
							 <a href="http://www.geoapi.org/" target="_blank">GeoAPI 2.1</a>,&nbsp;
							 <a href="http://java.sun.com/javase/technologies/desktop/media/jai/" target="_blank">JAI 1.1.3</a>,&nbsp;
							 <a href="http://xmlgraphics.apache.org/batik/" target="_blank">Batik 1.7</a>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- MONITOR -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_MONITOR'); ?></td>
						   </tr>
						   <tr>
						      <td>
						         <a href="http://www.sencha.com/" target="_blank">EXT JS 3.2.1</a>,&nbsp;
							 <a href="http://jquery.com/" target="_blank">JQuery 1.4.2</a>,&nbsp;
							 <a href="http://www.highcharts.com/" target="_blank">Highcharts JS v2.0.3</a>,&nbsp;
							 <a href="http://www.deegree.org" target="_blank">Deegree 2.3 OWSWatch</a>,&nbsp;
							 <a href="http://static.springsource.org/spring-security/site/" target="_blank">Spring Secrurity</a>
						      </td>
						   </tr>
						   <tr>
						      <td>
						         <b>Please note that Highcharts is not free for commercial use. Please see licence details <a href="http://www.highcharts.com/license" target="_blank">here</a></b>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- MAP -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_MAP'); ?></td>
						   </tr>
						   <tr>
						      <td>
						         <a href="http://www.sencha.com" target="_blank">EXT JS 2.2.1</a>,&nbsp;
							 <a href="http://www.geoext.org" target="_blank">GeoExt 0.7</a>,&nbsp;
							 <a href="http://jquery.com" target="_blank">JQuery 1.3.2</a>,&nbsp;
							 <a href="http://openlayers.org/" target="_blank">Openlayers 2.8</a>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						   <!-- PUBLISH -->
						   <tr>
						      <td class="title"><?php echo JText::_('SHOP_CPANEL_THANKS_PUBLISH'); ?></td>
						   </tr>
						   <tr>
						      <td>
						         <a href="http://www.geoext.org" target="_blank">GeoExt 1.0</a>,&nbsp;
						         <a href="http://openlayers.org/" target="_blank">Openlayers 2.10</a>,&nbsp;
							 <a href="http://www.sencha.com/" target="_blank">EXT JS 3.2</a>,&nbsp;
							 <a href="http://projects.opengeo.org/styler" target="_blank">GeoExt Styler</a>,&nbsp;
							 <a href="http://www.gdal.org" target="_blank">Gdal - OGR 1.7.2</a>
						      </td>
						   </tr>
						   <tr>
						      <td>&nbsp;</td>
						   </tr>
						</table>
						
						
						<?php
						echo $pane->endPanel('cpanel-panel-contrib');
						
						//licence panel
						echo $pane->startPanel( JText::_('CORE_CPANEL_LICENSE'), 'cpanel-panel-licence' );
						?><PRE style="padding-left:10px">
						<?php 		 
						$file = file_get_contents (JPATH_COMPONENT_ADMINISTRATOR.DS.'license.txt');
						echo htmlspecialchars  ($file,ENT_QUOTES);
						?></PRE>
						<br/>
						<p style="padding-left:10px">
						<b>Please note that Highcharts is not free for commercial use. Please see licence details <a href="http://www.highcharts.com/license" target="_blank">here</a></b>
						</p>
						<?php
						echo $pane->endPanel('cpanel-panel-licence');
						
						echo $pane->endPane();
					?>
					</div>
				</td>
			</tr>
		</table>
			<?php 
	}
	
}
	?>
