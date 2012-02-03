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

class HTML_ctrlpanel 
{
	function mapCtrlPanel($option)
	{
		JToolBarHelper::title( JText::_(  'EASYSDI_MAP_CONTROL_PANEL' ), 'generic.png' );
		JToolBarHelper::custom( 'ctrlPanel', 'tool_easysdi_admin.png', 'tool_easysdi_admin.png', JTEXT::_("CORE_MENU_CPANEL"), false );
		
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
			
		$pane		=& JPane::getInstance('sliders');
		echo $pane->startPane("content-pane");
		echo $pane->startPanel( JText::_('EASYSDI_MAP_MODULES'), 'cpanel-panel-1' );
		?>
		<form ation="index.php" method="post" name="adminForm" id="adminForm" class="adminForm">
			<input type="hidden" name="option" id="option" value="<?php echo $option?>">
			<input type="hidden" name="task" id="task" value="">
		</form>
		<div id="cpanel">	
		<table>
			<tr>
			<td>
			<?php
			$link = "index.php?option=$option&amp;task=display";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_DISPLAY_OPTION' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php
			$link = "index.php?option=$option&amp;task=rightCtrlPanel";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_PROFILE' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php
			$link = "index.php?option=$option&amp;task=annotationStyle";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_annotationStyle' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php
			$link = "index.php?option=com_easysdi_map&amp;task=editResource&filename=".JPATH_COMPONENT_SITE.DS.'resource'.DS.'xslt'.DS.'getFeatureInfo.xslt';
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_RENDER_INFO' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php
			$link = "index.php?option=com_easysdi_map&amp;task=editResource&filename=".JPATH_COMPONENT_SITE.DS.'resource'.DS.'xslt'.DS.'getFeatureInfo.css';
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_RENDER_INFO_STYLE' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			</tr>
			
			<tr>
			<td>
			<?php	
			$link = "index.php?option=$option&amp;task=projection";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_MAP_PROJECTION' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php
			$link = "index.php?option=$option&amp;task=featureType";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_FEATURE_TYPE' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php
			$link = "index.php?option=$option&amp;task=comment";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
					
						<?php 
						$text = JText::_( 'EASYSDI_MAP_COMMENT' );
						echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			
			</tr>
			
			<tr>
			<td>
			<?php	
			$link = "index.php?option=$option&amp;task=searchLayer";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_MAP_SEARCH_LAYER' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php	
			$link = "index.php?option=$option&amp;task=precision";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_MAP_PRECISION' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php	
			$link = "index.php?option=$option&amp;task=resultGrid";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_MAP_RESULT_GRID' );					
							echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			</tr>
						
			<tr>	
			<td>		
			<?php	
			$link = "index.php?option=$option&amp;task=simplesearchCtrlPanel";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_MAP_SIMPLE_SEARCH' );					
							echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			
			<td>
			<?php	
			$link = "index.php?option=$option&amp;task=overlayCtrlPanel";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_MAP_LAYERS' );					
							echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			</td>
			</tr>
			
			</table>
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