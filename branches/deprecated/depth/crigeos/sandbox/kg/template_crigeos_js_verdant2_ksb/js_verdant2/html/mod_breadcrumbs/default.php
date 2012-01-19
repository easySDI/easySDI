<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div id="main_breadcrumb">
	<!--<div class="side TL"></div>
	<div class="side TR"></div>
	<div class="side BL"></div>
	<div class="side BR"></div>
	<span class="top">Pathway</span>-->
	<div class="module_body">
		<span class="breadcrumbs pathway">
		<?php for ($i = 0; $i < $count; $i ++) :

			// If not the last item in the breadcrumbs add the separator
			if ($i < $count -1) {
				if(!empty($list[$i]->link)) {
					echo '<a href="'.$list[$i]->link.'" class="pathway">'.$list[$i]->name.'</a>';
				} else {
					echo $list[$i]->name;
				}
				echo ' '.$separator.' ';
			}  else if ($params->get('showLast', 1)) { // when $i == $count -1 and 'showLast' is true
			    echo $list[$i]->name;
			}
		endfor; ?>
		</span>
	</div>
</div>