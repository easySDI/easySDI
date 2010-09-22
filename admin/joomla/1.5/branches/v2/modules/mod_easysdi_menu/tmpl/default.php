<?php

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'model'.DS.'account.easysdi.class.php');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
require_once(JPATH_BASE.DS.'modules'.DS.'mod_mainmenu'.DS.'helper.php');

// no direct access
defined('_JEXEC') or die('Restricted access');


if ( ! defined('modMainMenuXMLCallbackDefined') )
{
function modMainMenuXMLCallback(&$node, $args)
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
}
	define('modMainMenuXMLCallbackDefined', true);
}

modMainMenuHelper::render($params, 'modMainMenuXMLCallback');


/**
 * EasySdi Right Management Class.
 */
class userManagerRightESDY
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
	static function hasRight($account_id,$right){

		$database =& JFactory::getDBO();

		$query = "SELECT count(*) 
				  FROM #__sdi_actor a ,
				  	   #__sdi_list_role b  
				  WHERE a.role_id = b.id 
				  and a.account_id = $account_id 
				  and b.code = '$right'";

		$database->setQuery($query );
		$total = $database->loadResult();

		return ($total > 0 );
	}
	
	static function menuRight($url,$user) {
	
	
		//Is the user from ESDY project
		if(userManagerRightESDY::isEasySDIUser($user))
		{
			$db =& JFactory::getDBO();
			$account = new accountByUserId($db);
			$account->load($user->id);	
			
			//Is the URL from ESDY
			if (preg_match("/(com_easysdi_core|com_easysdi_shop|com_easysdi_catalog)/i", $url)) 
			{
				preg_match('/task=([a-z]+)&/i', $url, $tasks);
				$task = $tasks[1];
				
				if ($task=="listOrders") 
				{
					return (userManagerRightESDY::hasRight($account->id,"REQUEST_INTERNAL") 
							|| userManagerRightESDY::hasRight($account->id,"REQUEST_EXTERNAL") );
				}
				elseif ($task=="showPartner") 
				{
					return userManagerRightESDY::hasRight($account->id,"MYACCOUNT");
				}
				elseif ($task=="listAffiliatePartner") 
				{
					return userManagerRightESDY::hasRight($account->id,"ACCOUNT");
				}
				elseif($task=="listMetadata")
				{
					return userManagerRightESDY::hasRight($account->id,"METADATA");
				}
				elseif($task=="listObject")
				{
					return userManagerRightESDY::hasRight($account->id,"METADATA");
				}
				elseif ($task=="listProduct") 
				{
					//the partner must at least have a product assigned to him
					$db->setQuery("SELECT count(p.*)
									FROM #__sdi_product p 
									INNER JOIN #__sdi_objectversion v ON p.objectversion_id = v.id
									INNER JOIN #__sdi_object o ON o.id = v.object_id
									INNER JOIN #__sdi_metadata md ON md.id = v.metadata_id
									INNER JOIN #__sdi_manager_object m ON m.object_id = o.id 
									WHERE m.account_id = ".$account->id);
					$res = $db->loadResult();
					return (userManagerRightESDY::hasRight($account->id,"PRODUCT") && $res > 0);
				}
				elseif ($task=="listOrdersForProvider") 
				{
					$db->setQuery("SELECT count(*) FROM #__sdi_product where diffusion_id=".$account->id);
					$res = $db->loadResult();
					return (userManagerRightESDY::hasRight($account->id,"DIFFUSION") && $res > 0);
				}
				elseif ($task=="manageFavorite") 
				{
//					$enableFavorites = config_easysdi::getValue("ENABLE_FAVORITES", 1);
//					return (userManagerRightESDY::hasRight($account->id,"FAVORITE") && $enableFavorites == 1);
					return (userManagerRightESDY::hasRight($account->id,"FAVORITE"));
				}
				else 
				{
					return true;
				}
			} else 
			{
				//Not a ESDY URL
			    return true;
			}	
		}
		else
		{
			return true;
		}
	}
	
}