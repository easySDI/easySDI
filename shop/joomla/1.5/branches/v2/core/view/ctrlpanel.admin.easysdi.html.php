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

class HTML_ctrlpanel {
	
	function ctrlPanelShop($option){
		JToolBarHelper::title( JText::_(  'EASYSDI_EASYSDI_CONTROL_PANEL_SHOP' ), 'generic.png' );
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();			
		$pane		=& JPane::getInstance('sliders');
		echo $pane->startPane("content-pane");
		echo $pane->startPanel( JText::_('EASYSDI_EASYSDI MODULES'), 'cpanel-panel-1' );
		?>
		<div id="cpanel">		
			<?php
			$link = "index.php?option=$option&amp;task=listBasemap";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						
						<?php 
						$text = JText::_( 'EASYSDI_BASEMAP_CTRL_PANEL' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php	
			$link = "index.php?option=$option&amp;task=listPerimeter";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_PERIMETER_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php	
			$link = "index.php?option=$option&amp;task=ctrlPanelLocation";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_LOCATION_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php	
			$link = "index.php?option=$option&amp;task=ctrlPanelProduct";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_PRODUCT_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php	
			$link = "index.php?option=$option&amp;task=ctrlPanelProperties";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_PROPERTIES_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php
			$link = "index.php?option=$option&amp;task=listOrders";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">				
						<?php 
						$text = JText::_( 'EASYSDI_LIST_ORDERS' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>			
		</div> 
	 	<?php
		echo $pane->endPanel();
		?>
		<div id="rightcpanel">
		<?php 
		echo $pane->startPanel( JText::_('EASYSDI_LICENSE'), 'cpanel-panel-licence' );
		?><PRE>
		<?php 		 
		$file = file_get_contents (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_shop'.DS.'license.txt');
		echo htmlspecialchars  ($file,ENT_QUOTES);
		?></PRE>
		<?php
		echo $pane->endPanel();
		echo $pane->endPane();
		?>
		</div>
		<?php 
	}
}
?>