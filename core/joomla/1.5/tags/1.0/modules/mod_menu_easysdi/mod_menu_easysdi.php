<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');


$user = JFactory::getUser();
$task = JRequest::getVar("task");
$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
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
		$rowPartner = new partnerByUserId( $db );
		$rowPartner->load( $user->id );
		
		/*
		 * Custom home
		 * Add a link to a custom user home article. This article is normally the one displayed
		 * after the login. The EasySDI USER_HOME Key must be set to enable this link.
		 */
		
		 $userHome = config_easysdi::getValue("USER_HOME");
		 if($userHome != ''){
			 ?>
			 <li <?php if( $task == "")echo"class=\"mod_menu_item_active\"";?>>
			<a href ="<?php echo $userHome; ?>"><span><?php echo JText::_("EASYSDI_USER_HOME"); ?></span></a>
			</li>
			<?php
		 }
		
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
				<li <?php if( $task == "showPartner" || $task=="editPartner")echo"class=\"mod_menu_item_active\"";?>>
				<a href ="./index.php?option=com_easysdi_core&task=showPartner"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYACCOUNT"); ?></span></a>
				</li>
				
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"ACCOUNT"))
			{
				?>
				<li <?php if( $task == "listAffiliatePartner" || $task == "editAffiliateById")echo"class=\"mod_menu_item_active\"";?>>
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
				<li <?php if( $task == "listOrders")echo"class=\"mod_menu_item_active\"";?>>
				<a href ="./index.php?option=com_easysdi_shop&task=listOrders&limit=20&limitstart=0"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYORDERS"); ?></span></a>
				</li>
				<?php
			}
			//the partner must at least have a metadata assigned to him
			//
			$db->setQuery("SELECT count(*) FROM #__easysdi_product where metadata_partner_id=".$rowPartner->partner_id);
			$res = $db->loadResult();
			if(userManagerRight::hasRight($rowPartner->partner_id,"METADATA") && $res > 0)
			{
				?>
				<li <?php if($task == "listProductMetadata" || $task == "editMetadata")echo"class=\"mod_menu_item_active\"";?>>
				<a href ="./index.php?option=com_easysdi_shop&task=listProductMetadata"><span><?php echo JText::_("EASYSDI_MENU_ITEM_METADATA"); ?></span></a>
				</li>
				
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"PRODUCT"))
			{
				?>
				<li <?php if( $task == "listProduct" || $task == "editProduct")echo"class=\"mod_menu_item_active\"";?>>
				<a href ="./index.php?option=com_easysdi_shop&task=listProduct"><span><?php echo JText::_("EASYSDI_MENU_ITEM_PRODUCTS"); ?></span></a>
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
			
			//the partner must at least have a product for diffusion assigned to him
			//
			$db->setQuery("SELECT count(*) FROM #__easysdi_product where diffusion_partner_id=".$rowPartner->partner_id);
			$res = $db->loadResult();
			if (userManagerRight::hasRight($rowPartner->partner_id,"DIFFUSION") && $res > 0)
			{
				?>
				<li <?php if( $task == "listOrdersForProvider" || $task == "processOrder")echo"class=\"mod_menu_item_active\"";?>>
				<a href ="./index.php?option=com_easysdi_shop&task=listOrdersForProvider"><span><?php echo JText::_("EASYSDI_MENU_ITEM_MYTREATMENT"); ?></span></a>
				</li>
				<?php
			}
			if(userManagerRight::hasRight($rowPartner->partner_id,"FAVORITE") && $enableFavorites == 1)
			{
				?>
				<li <?php if( $task == "manageFavorite")echo"class=\"mod_menu_item_active\"";?>>
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
	<table class="easysdi_disconnect_table" width="100%">
		<tr>
		   <td>
		    <button onclick="window.open('./index.php?option=com_user&task=logout&return=aW5kZXgucGhw', '_self');"
			class="easysdi_disconnect_button" type="submit"><?php echo JText::_("EASYSDI_MENU_ITEM_LOGIN"); ?></button>
		   </td>
		 </tr>
	</table>
	
		
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