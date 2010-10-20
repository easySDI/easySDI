<tr>
	<td>
		<div id="cpanel">
			<br/>
			<h1><?php echo JText::_( 'CORE_CPANEL_AGI_STAT_LABEL' );?></h1>
			<?php
			$link = "index.php?option=com_agi_stat&amp;task=statistic";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						
						<?php 
						$text = JText::_( 'AGI_STAT_CPANEL_STATISTIC' );
						echo JHTML::_('image.site',  'icon-48-media.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>	
		</div>
	</td>
</tr>
