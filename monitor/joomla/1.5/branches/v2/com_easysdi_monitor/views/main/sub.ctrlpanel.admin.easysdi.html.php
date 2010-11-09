<tr>
	<td>
		<div id="cpanel">
			<br/>
		<img src="./components/com_easysdi_monitor/icons/logo_monitor.png" width="130" height="40"/><br/>
			<?php	
			$link = "index.php?option=com_easysdi_monitor&amp;view=main";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'MONITOR_CPANEL_MAIN' );					
							echo JHTML::_('image.site',  'icon-48-cpanel.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
		</div>
	</td>
</tr>