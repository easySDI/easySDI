<?php defined('_JEXEC') or die('Restricted access'); ?>

<form id="searchForm" action="<?php echo JRoute::_( 'index.php?option=com_search' );?>" method="post">
	<div class="contentpaneopen<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<div>
				<label for="search_searchword">
					<?php echo JText::_( 'Search Keyword' ); ?>:
				</label>
		</div>
		<div>
				<input type="text" name="searchword" id="search_searchword" size="30" maxlength="20" value="<?php echo $this->escape($this->searchword); ?>" class="inputbox" /><button name="Search" onclick="this.form.submit()" class="button"><?php echo JText::_( 'Search' );?></button>
				
		</div>
		<div>
		</div>
		<div>
				<?php echo $this->lists['searchphrase']; ?>
		</div>
		<div>
				<label for="ordering">
					<?php echo JText::_( 'Ordering' );?>:
				</label>
				<?php echo $this->lists['ordering'];?>
		</div>
	</div>
	<?php if ($this->params->get( 'search_areas', 1 )) : ?>
		<div>
		<?php echo JText::_( 'Search Only' );?>:
		</div>
		<?php foreach ($this->searchareas['search'] as $val => $txt) :
			$checked = is_array( $this->searchareas['active'] ) && in_array( $val, $this->searchareas['active'] ) ? 'checked="checked"' : '';
		?>
		<ins>
		<input type="checkbox" name="areas[]" value="<?php echo $val;?>" id="area_<?php echo $val;?>" <?php echo $checked;?> />
			<label for="area_<?php echo $val;?>">
				<?php echo JText::_($txt); ?>
			</label>
		</ins>
		<?php endforeach; ?>
	<?php endif; ?>


	<div class="searchintro<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<div>
			<br />
			<?php echo JText::_( 'Search Keyword' ) .' <b>'. $this->escape($this->searchword) .'</b>'; ?>
		</div>
		<div>
			<br />
			<?php echo $this->result; ?>
		</div>
</div>

<?php if($this->total > 0) : ?>
<div style="margin:0px auto;">
	<div style="float: right;">
		<label for="limit">
			<?php echo JText::_( 'Display Num' ); ?>
		</label>
		<?php echo $this->pagination->getLimitBox( ); ?>
	</div>
	<div>
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
</div>
<?php endif; ?>
<div>
<input type="hidden" name="task"   value="search" />
</div>
</form>
<div class="clear"></div>