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

defined('_JEXEC') or die('Restricted access');

class HTML_overlayctrlpanel 
{
	function overlayCtrlPanel($option)
	{
		JToolBarHelper::title( JText::_(  'MAP_CARTO_CONTROL_PANEL' ), 'map.png' );
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
		
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
		$pane		=& JPane::getInstance('sliders');
		echo $pane->startPane("content-pane");
	
		?>
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<input type="hidden" name="option" id="option" value="<?php echo $option?>">
			<input type="hidden" name="task" id="task" value="">
		</form>
		
		<table width=100% border="0">
			<tr valign=top>
				<td width=60%>
					<table width=100% border="0">
						<tr>
							<td>
							     <table class="header_sdi_table" width="100%">
								   <tbody>
								      <tr>
								         <td colspan="2">
									    <table width="100%">
									      <tr>
								               <td class="header_sdi_comp_links">&nbsp;</td>
								               <td class="header_sdi_list">
									          <ul>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=baseMap">', $option); echo JText::_( 'MAP_BASEMAP' ); ?></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=baseLayer">', $option); echo JText::_( 'MAP_BASELAYER' ); ?></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=geolocation">', $option); echo JText::_( 'MAP_GEOLOCATION' ); ?></li>
									          </ul>
									       </td>
									       <td class="header_sdi_list">
									       <ul >
									             <li><?php printf('<a href="index.php?option=%s&amp;task=overlay">', $option); echo JText::_( 'MAP_OVERLAY_DEFINITION' ); ?></li>
									             <li><?php printf('<a href="index.php?option=%s&amp;task=overlayGroup">', $option); echo JText::_( 'MAP_OVERLAY_GROUP' ); ?></li>
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
						</table>
						</td>
						</tr>
						</table>
			<?php
			echo $pane->endPanel();

	}
}
?>