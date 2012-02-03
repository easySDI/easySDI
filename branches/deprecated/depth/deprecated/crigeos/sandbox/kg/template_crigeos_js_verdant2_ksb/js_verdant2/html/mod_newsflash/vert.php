<?php defined('_JEXEC') or die('Restricted access'); ?>

<?php for ($i = 0, $n = count($list); $i < $n; $i ++) :
	modNewsFlashHelper::renderItem($list[$i], $params, $access);
	if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>


 	<?php endif; ?>
<?php endfor; ?>
