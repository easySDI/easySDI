<?php
defined('_JEXEC') or die('Restricted access');

// Include the template specific functions
require_once(JPATH_THEMES.DS.$mainframe->getTemplate().'/functions.php'); 

/**
 * SHACK GRID Module 
 * (i.e. <jdoc:include type="modules" name="user3" grid="<?php echo $user3gridcount;?>" style="shackgrid" />)
 */
function modChrome_shackgrid($module, &$params, &$attribs) {
	
	// Assign template parameters
	$templateParams 	= &jShackFunc::getTemplateParams(); 
	$titleSeparator		= $templateParams->get('titleseparator');
	
?>
<div class="module<?php echo $params->get( 'moduleclass_sfx' ); ?> grid_<?php echo $attribs['grid'] ?>">
  <?php if ($module->showtitle) : ?>
  <h3><?php echo jShackFunc::titleStyle($module->title, $titleSeparator); ?></h3>
  <?php endif; ?>
  <?php echo $module->content; ?>
</div>
<?php
}

/**
 * SHACK GRID + Rounded Corners
 * (i.e. <jdoc:include type="modules" name="user1" grid="<?php echo $user2gridcount;?>" style="shackflexgridrounded" />)
 */
function modChrome_shackgridrounded($module, &$params, &$attribs) {
	if (!$module->showtitle) {
		$moduletitle = ' notitle';
	} else {
		$moduletitle = NULL;
	}
	
	// Assign template parameters
	$templateParams 	= &jShackFunc::getTemplateParams(); 
	$titleSeparator		= $templateParams->get('titleseparator');
	
?>
<div class="module<?php echo $params->get( 'moduleclass_sfx' ); ?> grid_<?php echo $attribs['grid']; ?>">
	<div class="side TL"></div>
	<div class="side TR"></div>
	<div class="side BL"></div>
	<div class="side BR"></div>
	<?php if ($module->showtitle) : ?>
		<h3><?php echo jShackFunc::titleStyle($module->title, $titleSeparator); ?></h3>
	<?php endif; ?>
		<div class="module_body<?php echo $moduletitle; ?>">
			<?php echo $module->content; ?>
		</div>
</div>
<?php
}

/**
 * SHACK FLEX GRID
 * (i.e. <jdoc:include type="modules" name="user1" grid="<?php echo $user2gridcount;?>" style="shackflexgrid" />)
 */
function modChrome_shackflexgrid($module, &$params, &$attribs) {
	
	// Assign template parameters
	$templateParams 	= &jShackFunc::getTemplateParams(); 
	$titleSeparator		= $templateParams->get('titleseparator');
	
?>
<div class="module<?php echo $params->get( 'moduleclass_sfx' ); ?> flexgrid_<?php echo $attribs['grid'] ?>">
  <?php if ($module->showtitle) : ?>
  <h3><?php echo jShackFunc::titleStyle($module->title, $titleSeparator); ?></h3>
  <?php endif; ?>
  <?php echo $module->content; ?>
</div>
<?php
}

/**
 * SHACK FLEX GRID + Rounded Corners
 * (i.e. <jdoc:include type="modules" name="user1" grid="<?php echo $user2gridcount;?>" style="shackflexgridrounded" />)
 */
function modChrome_shackflexgridrounded($module, &$params, &$attribs) {
	if (!$module->showtitle) {
		$moduletitle = ' notitle';
	} else {
		$moduletitle = NULL;
	}
	
	// Assign template parameters
	$templateParams 	= &jShackFunc::getTemplateParams(); 
	$titleSeparator		= $templateParams->get('titleseparator');
	
?>
<div class="module<?php echo $params->get( 'moduleclass_sfx' ); ?> flexgrid_<?php echo $attribs['grid'] ?>">
	<div class="side TL"></div>
	<div class="side TR"></div>
	<div class="side BL"></div>
	<div class="side BR"></div>
	<?php if ($module->showtitle) : ?>
		<h3><?php echo jShackFunc::titleStyle($module->title, $titleSeparator); ?></h3>
	<?php endif; ?>
		<div class="module_body<?php echo $moduletitle; ?>">
			<?php echo $module->content; ?>
		</div>
</div>
<?php
}

/**
 * SHACK ROUNDED CORNER 1
 * (i.e. <jdoc:include type="modules" name="right" style="shackrounded" />)
 */
function modChrome_shackrounded($module, &$params, &$attribs) {
	
	// Assign template parameters
	$templateParams 	= &jShackFunc::getTemplateParams(); 
	$titleSeparator		= $templateParams->get('titleseparator');
	
?>
<div class="moduletable<?php echo $params->get( 'moduleclass_sfx' ); ?>">
<span class="tl"></span><span class="tr"></span>
    <div>
	<?php if ($module->showtitle) : ?>
  <h3><?php echo jShackFunc::titleStyle($module->title, $titleSeparator); ?></h3>
  <?php endif; ?>
  <?php echo $module->content; ?>
  </div>
  <span class="bl"></span><span class="br"></span>
</div>
<?php
}

/**
 * SHACK ROUNDED CORNER 2
 * (i.e. <jdoc:include type="modules" name="right" style="shackrounded"2 />)
 */
function modChrome_shackrounded2($module, &$params, &$attribs) {
	if (!$module->showtitle) {
		$moduletitle = ' notitle';
	} else {
		$moduletitle = NULL;
	}
	
	// Assign template parameters
	$templateParams 	= &jShackFunc::getTemplateParams(); 
	$titleSeparator		= $templateParams->get('titleseparator');
	
?>
<div class="moduletable<?php echo $params->get( 'moduleclass_sfx' ); ?>">
	<div class="side TL"></div>
	<div class="side TR"></div>
	<div class="side BL"></div>
	<div class="side BR"></div>
<?php if ($module->showtitle) : ?>
	<h3><?php echo jShackFunc::titleStyle($module->title, $titleSeparator); ?></h3>
<?php endif; ?>
	<div class="module_body<?php echo $moduletitle; ?>">
		<?php echo $module->content; ?>
	</div>
</div>
<?php
}

?>