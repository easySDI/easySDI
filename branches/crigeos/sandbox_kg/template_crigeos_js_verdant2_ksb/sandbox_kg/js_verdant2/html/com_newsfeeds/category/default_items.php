<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<form action="<?php echo JRoute::_('index.php?view=category&id='.$this->category->slug); ?>" method="post" id="adminForm">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php if ($this->params->get('show_limit')) : ?>
<tr>
	<td align="right" colspan="4">
	<?php
		echo JText::_('Display Num') .'&nbsp;';
		echo $this->pagination->getLimitBox();
	?>
	</td>
</tr>
<?php endif; ?>
<?php if ( $this->params->get( 'show_headings' ) ) : ?>
<tr>
	<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w5" align="right">
		<?php echo JText::_('Num'); ?>
	</td>
	<?php if ( $this->params->get( 'show_name' ) ) : ?>
	<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w90">
		<?php echo JText::_( 'Feed Name' ); ?>
	</td>
	<?php endif; ?>
	<?php if ( $this->params->get( 'show_articles' ) ) : ?>
	<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w10 nw" align="center">
		<?php echo JText::_( 'Num Articles' ); ?>
	</td>
	<?php endif; ?>
 </tr>
<?php endif; ?>
<?php foreach ($this->items as $item) : ?>
<tr class="sectiontableentry<?php echo $item->odd + 1; ?>">
	<td align="right" class="w5">
		<?php echo $item->count + 1; ?>
	</td>
	<td class="w90">
		<a href="<?php echo $item->link; ?>" class="category<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
			<?php echo $item->name; ?></a>
	</td>
	<?php if ( $this->params->get( 'show_articles' ) ) : ?>
	<td class="w10" align="center">
		<?php echo $item->numarticles; ?>
	</td>
	<?php endif; ?>
</tr>
<?php endforeach; ?>
<tr>
	<td align="center" colspan="4" class="sectiontablefooter<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php

		echo $this->pagination->getPagesLinks();
	?>
	</td>
</tr>
<tr>
	<td colspan="4" align="right">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</td>
</tr>
</table>
</form>