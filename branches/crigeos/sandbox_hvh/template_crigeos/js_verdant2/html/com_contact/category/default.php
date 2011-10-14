<?php
/**
 * $Id: default.php 10967 2008-09-26 00:01:51Z ian $
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

$cparams =& JComponentHelper::getParams('com_media');
?>

<?php if ( $this->params->get( 'show_page_title', 1 ) ) : ?>
<h2 class="componentheading<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php echo $this->escape($this->params->get('page_title')); ?>
</h2>
<?php endif; ?>
<div class="contentpane<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
<?php if ($this->category->image || $this->category->description) : ?>
	<div class="contentdescription<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
	<?php if ($this->params->get('image') != -1 && $this->params->get('image') != '') : ?>
		<img src="<?php echo $this->baseurl .'/'. 'images/stories' . '/'. $this->params->get('image'); ?>" class="float<?php echo $this->params->get('image_align'); ?>" alt="<?php echo JText::_( 'Contacts' ); ?>" />
	<?php elseif ($this->category->image) : ?>
		<img src="<?php echo $this->baseurl .'/'. 'images/stories' . '/'. $this->category->image; ?>" class="float<?php echo $this->category->image_position; ?>" alt="<?php echo JText::_( 'Contacts' ); ?>" />
	<?php endif; ?>
	<?php echo $this->category->description; ?>
	</div>
	<div class="clear"></div>
<?php endif; ?>
<script type="text/javascript">
	function tableOrdering( order, dir, task ) {
	var form = document.adminForm;

	form.filter_order.value 	= order;
	form.filter_order_Dir.value	= dir;
	document.adminForm.submit( task );
}
</script>
<form action="<?php echo $this->action; ?>" method="post" id="adminForm">
<table class="w100" border="0" cellspacing="0" cellpadding="0">
	<thead>
		<tr>
			<td align="right" colspan="6">
			<?php if ($this->params->get('show_limit')) :
				echo JText::_('Display Num') .'&nbsp;';
				echo $this->pagination->getLimitBox();
			endif; ?>
			</td>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td align="center" colspan="6" class="sectiontablefooter<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</td>
		</tr>
		<tr>
			<td colspan="6" align="right">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php if ($this->params->get( 'show_headings' )) : ?>
		<tr>
			<td align="right" class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w5">
				<?php echo JText::_('Num'); ?>
			</td>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
				<?php echo JHTML::_('grid.sort',  'Name', 'cd.name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</td>
			<?php if ( $this->params->get( 'show_position' ) ) : ?>
			<td class="sectiontableheader<?php echo  $this->params->get( 'pageclass_sfx' ); ?>">
				<?php echo JHTML::_('grid.sort',  'Position', 'cd.con_position', $this->lists['order_Dir'], $this->lists['order'] ); ?>
			</td>
			<?php endif; ?>
			<?php if ( $this->params->get( 'show_email' ) ) : ?>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w20">
				<?php echo JText::_( 'Email' ); ?>
			</td>
			<?php endif; ?>
			<?php if ( $this->params->get( 'show_telephone' ) ) : ?>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w15">
				<?php echo JText::_( 'Phone' ); ?>
			</td>
			<?php endif; ?>
			<?php if ( $this->params->get( 'show_mobile' ) ) : ?>
			<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w15">
				<?php echo JText::_( 'Mobile' ); ?>
			</td>
			<?php endif; ?>
			<?php if ( $this->params->get( 'show_fax' ) ) : ?>
				<td class="sectiontableheader<?php echo $this->params->get( 'pageclass_sfx' ); ?> w15">
					<?php echo JText::_( 'Fax' ); ?>
				</td>
			<?php endif; ?>
		</tr>
	<?php endif; ?>
	<?php echo $this->loadTemplate('items'); ?>
</tbody>
</table>

<div><input type="hidden" name="option" value="com_contact" /></div>
<div><input type="hidden" name="catid" value="<?php echo $this->category->id;?>" /></div>
<div><input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" /></div>
<div><input type="hidden" name="filter_order_Dir" value="" /></div>
</form>
</div>
