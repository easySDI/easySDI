<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ( $this->params->get( 'show_page_title', 1 ) ) : ?>
	<h2 class="componentheading<?php echo $this->params->get('pageclass_sfx')?>"><?php echo $this->escape($this->params->get('page_title')); ?></h2>
<?php endif; ?>

<table cellpadding="4" cellspacing="0" border="0" class="w100 contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php if ( @$this->image || @$this->category->description ) : ?>
<tr>
	<td valign="top" class="contentdescription<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php
		if ( isset($this->image) ) :  echo $this->image; endif;
		echo $this->category->description;
	?>
	</td>
</tr>
<?php endif; ?>
<tr>
	<td class="w60" colspan="2">
	<?php echo $this->loadTemplate('items'); ?>
	</td>
</tr>
</table>
