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
	function ctrlPanelCore($option, $catalogExist, $proxyExist, $shopExist){
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
									
							
								</div>
							</td>
						</tr>
<?php
if ($catalogExist > 0){ 
?>
						<tr>
							<td>
									<br/>
									<h1><?php echo JText::_( 'CORE_CPANEL_CATALOG_LABEL' );?></h1>
									
									<div id="cpanel">
										<?php	
									$link = "index.php?option=$option&amp;task=listObject";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_OBJECT_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
							
							
									<?php	
									$link = "index.php?option=$option&amp;task=listObjectType";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_OBJECTTYPE_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>
										<?php
										$link = "index.php?option=$option&amp;task=listBoundary";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_BOUNDARY_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>
									<?php
										$link = "index.php?option=$option&amp;task=listNamespace";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_NAMESPACE_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>
										<?php
										$link = "index.php?option=$option&amp;task=listImportRef";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_IMPORTREF_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>
									</div>
									</td>
								</tr>
							</table>
							<table>
								<tr>
									<td>
										<div id="cpanel">
									
										<?php
										$link = "index.php?option=$option&amp;task=listPackage";
								?>
										<!-- <div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_PACKAGE_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>		
										 -->								
										<?php
										$link = "index.php?option=$option&amp;task=listProfile";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_PROFILE_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>		
									
									
									<?php
										$link = "index.php?option=$option&amp;task=listAttributeType";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_ATTRIBUTETYPE_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>		
									
									
										<?php
										$link = "index.php?option=$option&amp;task=listClass";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_CLASS_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>		
									
									<?php
										$link = "index.php?option=$option&amp;task=listAttribute";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_ATTRIBUTE_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>		
									
										<?php
										$link = "index.php?option=$option&amp;task=listRelation";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_RELATION_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>
										
										<?php
										$link = "index.php?option=$option&amp;task=listObjectTypeLink";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_OBJECTTYPELINK_PANEL' );
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>
								</div>
							</td>
						</tr>
<?php 
}
if ($proxyExist > 0){
?>
						<tr>
							<td>
								<div id="cpanel">
									<br/>
									<h1><?php echo JText::_( 'CORE_CPANEL_PROXY_LABEL' );?></h1>
										<?php	
										$link = "index.php?option=$option&amp;task=showConfigList";
										?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
													<?php 
														$text = JText::_( 'CORE_PROXY_PANEL' );					
														echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
													<span><?php echo $text; ?></span></a>
											</div>
										</div>
								</div>
							</td>
						</tr>
<?php 
}

if ($shopExist > 0){
?>
						<tr>
							<td>
								<div id="cpanel">
									<br/>
									<h1><?php echo JText::_( 'CORE_CPANEL_SHOP_LABEL' );?></h1>
									<?php	
									$link = "index.php?option=$option&amp;task=ctrlPanelMetadata";
							
							
							?>
									<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
										<div class="icon">
											<a href="<?php echo $link; ?>">
												<?php 
													$text = JText::_( 'CORE_METADATA_PANEL' );					
													echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
												<span><?php echo $text; ?></span></a>
										</div>
									</div>									 
								</div>
							</td>
						</tr>
<?php 
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
	
