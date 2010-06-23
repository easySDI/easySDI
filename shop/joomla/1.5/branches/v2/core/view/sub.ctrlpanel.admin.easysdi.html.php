<tr>
	<td>
		<div id="cpanel">
			<br/>
			<h1><?php echo JText::_( 'CORE_CPANEL_SHOP_LABEL' );?></h1>
			<?php
			$link = "index.php?option=com_easysdi_shop&amp;task=listBasemap";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						
						<?php 
						$text = JText::_( 'EASYSDI_BASEMAP_CTRL_PANEL' );
						echo JHTML::_('image.site',  'icon-48-media.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>	
			<?php	
			$link = "index.php?option=com_easysdi_shop&amp;task=listPerimeter";
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
			$link = "index.php?option=com_easysdi_shop&amp;task=listLocation";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_LOCATION_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-checkin.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>
		<div id="cpanel">
			<br/>
			<?php	
			$link = "index.php?option=com_easysdi_shop&amp;task=ctrlPanelProduct";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_PRODUCT_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-generic.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php	
			$link = "index.php?option=com_easysdi_shop&amp;task=ctrlPanelProperties";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">
						<?php 
							$text = JText::_( 'EASYSDI_PROPERTIES_CTRL_PANEL' );					
							echo JHTML::_('image.site',  'icon-48-cpanel.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>
			<?php
			$link = "index.php?option=com_easysdi_shop&amp;task=listOrders";
			?>
			<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
				<div class="icon">
					<a href="<?php echo $link; ?>">				
						<?php 
						$text = JText::_( 'EASYSDI_LIST_ORDERS' );
						echo JHTML::_('image.site',  'icon-48-module.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
						<span><?php echo $text; ?></span></a>
				</div>
			</div>											 
		</div>
	</td>
</tr>