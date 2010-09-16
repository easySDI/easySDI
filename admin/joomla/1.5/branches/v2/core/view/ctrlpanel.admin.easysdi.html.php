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
			JToolBarHelper::title( JText::_(  'CORE_CPANEL_TITLE' ), 'generic.png' );
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
								<div id="cpanel">
									<h1><?php echo JText::_( 'CORE_CPANEL_CORE_LABEL' );?></h1>
									<?php
									$link = "index.php?option=$option&amp;task=ctrlPanelConfig";
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
											
												<?php 
												$text = JText::_( 'CORE_CONFIGURATION_PANEL' );
												echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
									
								<?php	
									$link = "index.php?option=$option&amp;task=listResources";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_RESSOURCES_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-language.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
								
								
								
									<?php	
									$link = "index.php?option=$option&amp;task=ctrlPanelAccountManager";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_ACCOUNT_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-user.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
									
									<?php	
									$link = "index.php?option=$option&amp;task=listAccountProfile";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_ACCOUNTPROFILE_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-user.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
							
							<?php	
									$link = "index.php?option=$option&amp;task=listLanguage";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_LANGUAGE_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
									
							<?php	
									$link = "index.php?option=$option&amp;task=serviceAccount";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_SERVICEACCOUNT_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-user.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
								</div>
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
					
						$pane =& JPane::getInstance('sliders', array('allowAllClose' => true));
						echo $pane->startPane("content-pane");
							
						echo $pane->startPanel( JText::_('CORE_CPANEL_LICENSE'), 'cpanel-panel-licence' );
						?><PRE>
						<?php 		 
						$file = file_get_contents (JPATH_COMPONENT_ADMINISTRATOR.DS.'license.txt');
						echo htmlspecialchars  ($file,ENT_QUOTES);
						?></PRE>
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
