<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');


$user = JFactory::getUser();

if (!$user->guest)
{
	?>
	<div>
	<div class="module_menu">
	<div>
	<div>
	<div>
	<h3><?php echo JText::_("EASYSDI_MENU_TITLE"); ?></h3>
	
	<?php 
	if(userManagerRight::isEasySDIUser($user))
	{
		
		?>
		<ul class="menu">
		<?php 
		$db =& JFactory::getDBO();
		$rowPartner = new partnerByUserId( $db );
		$rowPartner->load( $user->id );
		
		/*
		 * EasySDI Account
		 */
		$count = 0;
		$query = "SELECT COUNT(*) FROM `#__components` where `option` = 'com_easysdi_core' ";
		$db->setQuery( $query);
		$count = $db->loadResult();
		if ($count > 0) 
		{			
			if(userManagerRight::hasRight($rowPartner->partner_id,"MYACCOUNT"))
			{
				?>
				<li >
				<a href ="./index.php?option=com_easysdi_core&task=showPartner"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYACCOUNT"); ?></span></a>
				</li>
				
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"ACCOUNT"))
			{
				?>
				<li >
				<a href ="./index.php?option=com_easysdi_core&task=listAffiliatePartner"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYAFFILIATES"); ?></span></a>
				</li>
				
				<?php
			}
		}
		/*
		 * EasySDI Catalog 
		 */
	/*	$count = 0;
		$query = "SELECT COUNT(*) FROM `#__components` where `option` = 'com_easysdi_catalog' ";
		$db->setQuery( $query);
		$count = $db->loadResult();
		if ($count > 0) 
		{
			
		}*/
		
		/**
		 * EasySDI Shop
		 */
		$count = 0;
		$query = "SELECT COUNT(*) FROM `#__components` where `option` = 'com_easysdi_shop' ";
		$db->setQuery( $query);
		$count = $db->loadResult();
		if ($count > 0) 
		{
			if(userManagerRight::hasRight($rowPartner->partner_id,"REQUEST_INTERNAL") 
				|| userManagerRight::hasRight($rowPartner->partner_id,"REQUEST_EXTERNAL") )
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listOrders"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYORDERS"); ?></span></a>
				</li>
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"METADATA"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listProductMetadata"><span><?php echo JText::_("EASYSDI_MENU_ITEM_METADATA"); ?></span></a>
				</li>
				
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"PRODUCT"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listProduct"><span><?php echo JText::_("EASYSDI_MENU_ITEM_PRODUCTS"); ?></span></a>
				</li>
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"FAVORITE"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=manageFavoriteProduct"><span><?php echo JText::_("EASYSDI_MENU_ITEM_FAVORITES"); ?></span></a>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
		}
		
	}
	?>
		
</div>
</div>
</div>
</div>
</div>


<?php

}



class userManagerRight
{
	static function isEasySDIUser ($user)
	{
		$database =& JFactory::getDBO(); 
		$database->setQuery( "SELECT COUNT(*) FROM #__easysdi_community_partner WHERE user_id=".$user->id);
		$result = $database->loadResult();
		if($result == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	static function hasRight($partner_id,$right){
				
		$database =& JFactory::getDBO();		
		
		$query = "SELECT count(*) 
				  FROM #__easysdi_community_actor a ,
				  	   #__easysdi_community_role b  
				  WHERE a.role_id = b.role_id 
				  and partner_id = $partner_id 
				  and role_code = '$right'";
		
				
		$database->setQuery($query );
		$total = $database->loadResult();
		
		return ($total > 0 );
	}
}
?>