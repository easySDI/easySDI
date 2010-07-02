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