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
										<?php
										$link = "index.php?option=$option&amp;task=listContext";
								?>
										<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
											<div class="icon">
												<a href="<?php echo $link; ?>">
												
													<?php 
													$text = JText::_( 'CORE_CONTEXT_PANEL' );
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
								</div>
							</td>
						</tr>