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
require_once(JPATH_BASE.DS.'administrator'.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');


$user = JFactory::getUser();

$language=&JFactory::getLanguage();
$language->load('com_easysdi_core', JPATH_ADMINISTRATOR);

if (!$user->guest)
{
	?>
	<div>
	<div class="module_menu">
	<div>
	<div>
	<div>
	
	<?php 
	if(userManagerRight::isEasySDIUser($user))
	{
		?>
		<ul class="menu">
		<?php 
		$db =& JFactory::getDBO();
		$rowPartner = new accountByUserId( $db );
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
			if(userManagerRight::hasRight($rowPartner->id,"MYACCOUNT"))
			{
				?>
				<li >
				<a href ="./index.php?option=com_easysdi_core&task=showaccount"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYACCOUNT"); ?></span></a>
				</li>
				
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->id,"ACCOUNT"))
			{
				?>
				<li >
				<a href ="./index.php?option=com_easysdi_core&task=listAffiliateAccount"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYAFFILIATES"); ?></span></a>
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
			if(userManagerRight::hasRight($rowPartner->id,"REQUEST_INTERNAL") 
				|| userManagerRight::hasRight($rowPartner->id,"REQUEST_EXTERNAL") )
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listOrders"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYORDERS"); ?></span></a>
				</li>
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->id,"METADATA"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listProductMetadata"><span><?php echo JText::_("EASYSDI_MENU_ITEM_METADATA"); ?></span></a>
				</li>
				
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->id,"PRODUCT"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listProduct"><span><?php echo JText::_("EASYSDI_MENU_ITEM_PRODUCTS"); ?></span></a>
				</li>
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->id,"FAVORITE"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=manageFavoriteProduct"><span><?php echo JText::_("EASYSDI_MENU_ITEM_FAVORITES"); ?></span></a>
				</li>
				<?php
			}
			/*
			$query = "SELECT role_id FROM `#__easysdi_community_role` where role_code='DIFFUSION'";
			$db->setQuery( $query);
			$diffusionRole = $db->loadResult();
			
			$query = "SELECT COUNT(*) FROM `#__easysdi_product` as p, `#__easysdi_community_actor` as a where p.partner_id=a.partner_id and a.role_id='".$diffusionRole."' and p.partner_id = '".$rowPartner->partner_id."' ";
			//$query = "SELECT COUNT(*) FROM `#__easysdi_product` where a.diffusion_partner_id='".$rowPartner->partner_id."'";
			
			$db->setQuery( $query);
			$product_count = $db->loadResult();
			if($product_count > 0)*/
			if (userManagerRight::hasRight($rowPartner->id,"DIFFUSION"))
			{
				?>
				<li>
				<a href ="./index.php?option=com_easysdi_shop&task=listOrdersForProvider"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYTREATMENT"); ?></span></a>
				</li>
				<?php
			}
			
			?>
		</ul>
		<table class="easysdi_disconnect_table" width="100%">
			<tr>
			   <td>
			    <button onclick="window.open('./index.php?option=com_user&task=logout&return=aW5kZXgucGhw', '_self');"
				class="easysdi_disconnect_button" type="submit"><?php echo JText::_("EASYSDI_MENU_ITEM_LOGIN"); ?></button>
			   </td>
			 </tr>
		</table>
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
		$database->setQuery( "SELECT COUNT(*) FROM #__sdi_account WHERE user_id=".$user->id);
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
				  FROM #__sdi_actor a ,
				  	   #__sdi_list_role b  
				  WHERE a.role_id = b.id 
				  and account_id = $partner_id 
				  and b.code = '$right'";
		$database->setQuery($query );
		$total = $database->loadResult();
		
		return ($total > 0 );
	}
}
?>