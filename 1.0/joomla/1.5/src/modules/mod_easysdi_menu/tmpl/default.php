<?php

require_once(JPATH_BASE.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'partner.site.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once(JPATH_BASE.DS.'modules'.DS.'mod_mainmenu'.DS.'helper.php');


// no direct access
defined('_JEXEC') or die('Restricted access');

if ( ! defined('modEasysdiMenuXMLCallbackDefined') )
{
function modEasysdiMenuXMLCallback(&$node, $args)
{
	$user	= &JFactory::getUser();
	$menu	= &JSite::getMenu();
	$active	= $menu->getActive();
	$task = JRequest::getVar("task");
	$path	= isset($active) ? array_reverse($active->tree) : null;

	if (($args['end']) && ($node->attributes('level') >= $args['end']))
	{
		$children = $node->children();
		foreach ($node->children() as $child)
		{
			if ($child->name() == 'ul') {
				$node->removeChild($child);
			}
		}
	}
	if ($node->name() == 'ul') {
		foreach ($node->children() as $child)
		{
		  //Right management for easysdi
	
		      if (($child->attributes('access') > $user->get('aid', 0)) or !(userManagerRightESDY::menuRight($child->_children[0]->_attributes['href'], $user)))
		      $node->removeChild($child);
		      	    
		}
	}

	if (($node->name() == 'li') && isset($node->ul)) {
		$node->addAttribute('class', 'parent');
	}

	if (isset($path) && (in_array($node->attributes('id'), $path) || in_array($node->attributes('rel'), $path)))
	{
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' active');
		} else {
			$node->addAttribute('class', 'active');
		}
	}
	else
	{
		if (isset($args['children']) && !$args['children'])
		{
			$children = $node->children();
			foreach ($node->children() as $child)
			{
				if ($child->name() == 'ul') {
					$node->removeChild($child);
				}
			}
		}
	}

	if (($node->name() == 'li') && ($id = $node->attributes('id'))) {
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' item'.$id);
		} else {
			$node->addAttribute('class', 'item'.$id);
		}
	}

	if (isset($path) && $node->attributes('id') == $path[0]) {
		$node->addAttribute('id', 'current');
	} else {
		$node->removeAttribute('id');
	}
	$node->removeAttribute('rel');
	$node->removeAttribute('level');
	$node->removeAttribute('access');
	
	if (strlen(strstr($node->_children[0]->_attributes['href'],"task=logout"))>0) {
	   $data = $node->_children[0]->span[0]->_data;
	   $href = $node->_children[0]->_attributes['href'];
	   $children = $node->children();
	   //remove "a" tag
	   foreach ($node->children() as $child)
	   {
	   	if ($child->name() == 'a') {
	   		$node->removeChild($child);
	   	}
	   }
	   //add button
	   $btn = $node->addChild('button');
	  
	   $btn->_data = $data;
	   $btn->addAttribute('type','submit');
	   $btn->addAttribute('class','easysdi_disconnect_button');
	   $btn->addAttribute('onclick',"window.open('".$href."', '_self');");
	}
	
}
	define('modEasysdiMenuXMLCallbackDefined', true);
}
modMainMenuHelper::render($params, 'modEasysdiMenuXMLCallback');


/**
 * EasySdi Right Management Class.
 */
class userManagerRightESDY
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
	
	static function menuRight($url,$user) {
		//Is the user from ESDY project
		if(userManagerRightESDY::isEasySDIUser($user))
		{
			$db =& JFactory::getDBO();
			$rowPartner = new partnerByUserId( $db );
			$rowPartner->load( $user->id );
			
			//Is the URL from ESDY
			if (preg_match("/(com_easysdi_core|com_easysdi_shop|com_easysdi_publish)/i", $url)) {

				preg_match('/task=([a-z]+)&/i', $url, $tasks);
				$task = $tasks[1];
				
				if ($task=="listOrders") {
					return (userManagerRightESDY::hasRight($rowPartner->partner_id,"REQUEST_INTERNAL") 
				|| userManagerRightESDY::hasRight($rowPartner->partner_id,"REQUEST_EXTERNAL") );
				}
				elseif ($task=="showPartner") {
					return userManagerRightESDY::hasRight($rowPartner->partner_id,"MYACCOUNT");
				}
				
				elseif ($task=="listAffiliatePartner") {
					return userManagerRightESDY::hasRight($rowPartner->partner_id,"ACCOUNT");
				}
				
				elseif ($task=="listProductMetadata") {
				//the partner must at least have a metadata assigned to him
					$db->setQuery("SELECT count(*) FROM #__easysdi_product where metadata_partner_id=".$rowPartner->partner_id);
					$res = $db->loadResult();
					return (userManagerRightESDY::hasRight($rowPartner->partner_id,"METADATA") && $res > 0);
				}
				
				elseif ($task=="listProduct") {
					return userManagerRightESDY::hasRight($rowPartner->partner_id,"PRODUCT");
					}
					
				elseif ($task=="listOrdersForProvider") {
					$db->setQuery("SELECT count(*) FROM #__easysdi_product where diffusion_partner_id=".$rowPartner->partner_id);
					$res = $db->loadResult();
					return (userManagerRightESDY::hasRight($rowPartner->partner_id,"DIFFUSION") && $res > 0);
					}
					
				elseif ($task=="manageFavoriteProduct") {
					$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
					return (userManagerRightESDY::hasRight($rowPartner->partner_id,"FAVORITE") && $enableFavorites == 1);
					}
				elseif ($task=="gettingStarted") {
					return (userManagerRightESDY::hasRight($rowPartner->partner_id,"GEOSERVICE_DATA_MANA")||userManagerRightESDY::hasRight($rowPartner->partner_id,"GEOSERVICE_MANAGER"));
					}
				elseif ($task=="createFeatureSource") {
					return userManagerRightESDY::hasRight($rowPartner->partner_id,"GEOSERVICE_DATA_MANA");
					}
				elseif ($task=="createLayer") {
					return userManagerRightESDY::hasRight($rowPartner->partner_id,"GEOSERVICE_MANAGER");
					}
				else {
					return true;
				}
				} else {
				//Not a ESDY URL
				    return true;
				}	
		}
		//Not a EasySDI user, so hide the declared here EasySDI entires
		else
		{
			if (preg_match("/(com_easysdi_core|com_easysdi_shop)/i", $url)) {
				preg_match('/task=([a-z]+)&/i', $url, $tasks);
				$task = $tasks[1];
			  if($task=="listOrders")
			     return false;
			  elseif($task=="showPartner")
			     return false;
        elseif($task=="listAffiliatePartner")
			     return false;
		    elseif($task=="listProductMetadata")
		       return false;
			  elseif($task=="listProduct")
			     return false;
			  elseif($task=="listOrdersForProvider")
			     return false;
		    elseif($task=="manageFavoriteProduct")
		       return false;
	       elseif($task=="gettingStarted")
		       return false;
	       elseif($task=="createFeatureSource")
		       return false;
	       elseif($task=="createLayer")
		       return false;
		    else
		    //by default, display the menu link
		       return true;
		     
		   } else {
			//Not a ESDY URL
			    return true;
			}			     
		}
	}
	
}