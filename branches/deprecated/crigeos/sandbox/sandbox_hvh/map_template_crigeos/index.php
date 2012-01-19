<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d�Arche 40b, CH-1870 Monthey, easysdi@depth.ch
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
<!--<meta http-equiv="X-UA-Compatible" content="IE=7.5" />-->
<!-- <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/easysdi_map/css/reset.css" type="text/css" />-->
<jdoc:include type="head" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/components/com_easysdi_map/externals/ext/resources/css/ext-all.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/components/com_easysdi_map/externals/ext/resources/css/xtheme-<?php echo $this->params->get('extThemeVariation'); ?>.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/easysdi_map.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/template.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/crigeos/css/<?php echo $this->params->get('colorVariation'); ?>.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/crigeos/css/<?php echo $this->params->get('backgroundVariation'); ?>_bg.css" type="text/css" />
<!--[if lte IE 6]>
<link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/ieonly.css" rel="stylesheet" type="text/css" />
<![endif]-->
<?php if($this->direction == 'rtl') : ?>
  <link href="<?php echo $this->baseurl ?>/templates/crigeos/css/template_rtl.css" rel="stylesheet" type="text/css" />
<?php endif; ?>

</head>
<body id="page_bg" class="color_<?php echo $this->params->get('colorVariation'); ?> bg_<?php echo $this->params->get('backgroundVariation');?>">
<div id="map"><div class="loader"><br><br><br><img src="templates/<?php echo $this->template ?>/images/loader.gif">&nbsp;<b>Chargement...</b></div>
</div>
<script type="text/javascript">
Ext.onReady(function(){
	  var companyObject = [
	  		{
	         id : 'companyLogo',         
	         tag : 'a',
	         href :'<?php echo $this->baseurl?>/index.php'
	      },{
		     id : 'companyMessage',         
	         tag : 'img',
             src :'<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/texte_map_crigeos.png'
    	         
	      }];
	      
	  var companyBackgroundImgObject = [
	                                	{
	                                 id : 'companyBackgroundImg',         
	                                 tag : 'img',
	                                 src :'<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/fond_map_crigeos.png'
	                                }];

	  var companyDivId = Ext.DomHelper.insertFirst(document.body,   
	  		[{
	             id : 'companyBackgroundImg'
	          },{
	             id : 'companyBanner'
	          }]);

	  Ext.DomHelper.append(Ext.get('companyBanner'), companyObject);
	  Ext.DomHelper.append(Ext.get('companyBackgroundImg'), companyBackgroundImgObject);
	  
	  } 
);

</script>
</body>
</html>
