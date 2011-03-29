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
		JToolBarHelper::title( JText::_(  'MAP_CARTO_CONTROL_PANEL' ), 'generic.png' );
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
		
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
		$pane		=& JPane::getInstance('sliders');
		echo $pane->startPane("content-pane");
		echo $pane->startPanel( JText::_('MAP_OVERLAY_MODULES'), 'cpanel-panel-1' );
		?>
		<form ation="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<input type="hidden" name="option" id="option" value="<?php echo $option?>">
			<input type="hidden" name="task" id="task" value="">
		</form>
		<div id="cpanel">		
					
			<?php	
			$link = "index.php?option=$option&amp;task=baseMap";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'MAP_BASEMAP' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php	
			$link = "index.php?option=$option&amp;task=baseLayer";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'MAP_BASELAYER' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			
			<?php	
			$link = "index.php?option=$option&amp;task=geolocation";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'MAP_GEOLOCATION' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			
			<?php	
			$link = "index.php?option=$option&amp;task=overlay";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'MAP_OVERLAY_DEFINITION' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			
			<?php	
			$link = "index.php?option=$option&amp;task=overlayGroup";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'MAP_OVERLAY_GROUP' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			
			<?php
			echo $pane->endPanel();
			?>
			<div id="rightcpanel">
			</div>
			<?php
			echo $pane->endPanel();
			echo $pane->endPane();
			?>
	
		</div>
		<?php 
	}
}
?>