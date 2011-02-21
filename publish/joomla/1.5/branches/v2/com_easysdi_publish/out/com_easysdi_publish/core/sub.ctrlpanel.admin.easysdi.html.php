<tr>
	<td>
		<div id="cpanel">
			<br/>
		<img src="./components/com_easysdi_publish/icons/logo_easysdi_publish.jpg" width="120" height="45"/><br/>
			<?php	
			$link = "index.php?option=com_easysdi_publish&task=editGlobalSettings";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'CORE_CONFIGURATION_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-cpanel.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
		</div>
	</td>
</tr>