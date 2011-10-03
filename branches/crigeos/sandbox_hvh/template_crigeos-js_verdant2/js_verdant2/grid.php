<?php
defined( '_JEXEC' ) or die( 'Restricted index access' );
// remove mootools.js and caption.js if set in params
$headerjs = $this->getHeadData();
reset($headerjs['scripts']);
foreach ($headerjs['scripts'] as $script=>$type) {
    if (($mootools_enabled == "false" and strpos($script,'mootools.js')) or ($caption_enabled == "false" and strpos($script,'caption.js'))) {
        unset($headerjs['scripts'][$script]);
    }
}
$this->setHeadData($headerjs);
?>
<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
switch ($this->countModules('user1')) {
	case 1:
		$user1gridcount = "12";
		break;
	case 2:
		$user1gridcount = "6";
		break;	
	case 3:
		$user1gridcount = "4";
		break;		
	case 4:
		$user1gridcount = "3";
		break;		
	case 6:
		$user1gridcount = "2";
		break;	
	default:
		$user1gridcount = "2";
		break;
}
switch ($this->countModules('user2')) {
	case 1:
		$user2gridcount = "12";
		break;	
	case 2:
		$user2gridcount = "6";
		break;		
	case 3:
		$user2gridcount = "4";
		break;		
	case 4:
		$user2gridcount = "3";
		break;		
	case 6:
		$user2gridcount = "2";
		break;	
	default:
		$user2gridcount = "2";
		break;
}
switch ($this->countModules('user3')) {
	case 1:
		$user3gridcount = "12";
		break;	
	case 2:
		$user3gridcount = "6";
		break;		
	case 3:
		$user3gridcount = "4";
		break;		
	case 4:
		$user3gridcount = "3";
		break;		
	case 6:
		$user3gridcount = "2";
		break;	
	default:
		$user3gridcount = "2";
		break;
}
switch ($this->countModules('user4')) {
	case 1:
		$user4gridcount = "12";
		break;	
	case 2:
		$user4gridcount = "6";
		break;		
	case 3:
		$user4gridcount = "4";
		break;		
	case 4:
		$user4gridcount = "3";
		break;		
	case 6:
		$user4gridcount = "2";
		break;	
	default:
		$user4gridcount = "2";
		break;
}
?>
<?php
if ($this->countModules('left') and $layoutstyle=="lmr") :?>
<?php $leftcolgrid = "grid_".$sidecolumnwidth." pull_".(12-$sidecolumnwidth);?>
<?php $maingrid = "grid_".(12-$sidecolumnwidth)." push_".$sidecolumnwidth;?>
<?php endif; ?>
<?php
if ($this->countModules('right') and $layoutstyle=="lmr") :?>
<?php $rightcolgrid = "grid_".$sidecolumnwidth;?>
<?php $maingrid = "grid_".(12-$sidecolumnwidth);?>
<?php endif; ?>
<?php
if ($this->countModules('left and right') == 1 and $layoutstyle=="lmr") :?>
<?php $leftcolgrid = "grid_".$sidecolumnwidth." pull_".(12-($sidecolumnwidth)*2);?>
<?php $rightcolgrid = "grid_".$sidecolumnwidth;?>
<?php $maingrid = "grid_".(12-($sidecolumnwidth)*2)." push_".$sidecolumnwidth;?>
<?php endif; ?>

<?php
if ($this->countModules('left') and $layoutstyle=="mlr") :?>
<?php $leftcolgrid = "grid_".$sidecolumnwidth;?>
<?php $maingrid = "grid_".(12-$sidecolumnwidth);?>
<?php endif; ?>
<?php
if ($this->countModules('right') and $layoutstyle=="mlr") :?>
<?php $rightcolgrid = "grid_".$sidecolumnwidth;?>
<?php $maingrid = "grid_".(12-$sidecolumnwidth);?>
<?php endif; ?>
<?php
if ($this->countModules('left and right') == 1 and $layoutstyle=="mlr") :?>
<?php $leftcolgrid = "grid_".$sidecolumnwidth;?>
<?php $rightcolgrid = "grid_".$sidecolumnwidth;?>
<?php $maingrid = "grid_".(12-($sidecolumnwidth)*2);?>
<?php endif; ?>

<?php
if ($this->countModules('left') and $layoutstyle=="lrm") :?>
<?php $leftcolgrid = "grid_".$sidecolumnwidth." pull_".(12-$sidecolumnwidth);?>
<?php $maingrid = "grid_".(12-$sidecolumnwidth)." push_".$sidecolumnwidth;?>
<?php endif; ?>
<?php
if ($this->countModules('right') and $layoutstyle=="lrm") :?>
<?php $rightcolgrid = "grid_".$sidecolumnwidth." pull_".(12-$sidecolumnwidth);?>
<?php $maingrid = "grid_".(12-$sidecolumnwidth)." push_".$sidecolumnwidth;?>
<?php endif; ?>
<?php
if ($this->countModules('left and right') == 1 and $layoutstyle=="lrm") :?>
<?php $leftcolgrid = "grid_".$sidecolumnwidth." pull_".(12-($sidecolumnwidth)*2);?>
<?php $rightcolgrid = "grid_".$sidecolumnwidth." pull_".(12-($sidecolumnwidth)*2);?>
<?php $maingrid = "grid_".(12-($sidecolumnwidth)*2)." push_".$sidecolumnwidth*2;?>
<?php endif; ?>