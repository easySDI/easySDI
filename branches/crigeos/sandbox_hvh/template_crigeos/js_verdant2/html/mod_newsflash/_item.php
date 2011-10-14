<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php if ($params->get('item_title')) : ?>
	<h2 class="contentheading<?php echo $params->get( 'moduleclass_sfx' ); ?> w100">
	<?php if ($params->get('link_titles') && $item->linkOn != '') : ?>
		<a href="<?php echo $item->linkOn;?>" class="contentpagetitle<?php echo $params->get( 'moduleclass_sfx' ); ?>">
			<?php echo $item->title;?></a>
	<?php else : ?>
		<?php echo $item->title; ?>
	<?php endif; ?>
	</h2>

<?php endif; ?>

<?php if (!$params->get('intro_only')) :
	echo $item->afterDisplayTitle;
endif; ?>

<?php echo $item->beforeDisplayContent; ?>

<div class="contentpaneopen<?php echo $params->get( 'moduleclass_sfx' ); ?>">
<?php echo $item->text; ?>

<?php if (isset($item->linkOn) && $item->readmore && $params->get('readmore')) :
	      echo '<a class="readmore" href="'.$item->linkOn.'">'.$item->linkText.'</a>';
        endif; ?>
		</div>
<div class="article_separator"></div>
