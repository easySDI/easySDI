<?php

// no direct access
defined('_JEXEC') or die('Restricted access');
if ( ! defined('modMainMenuXMLCallbackDefined') )
{
function modMainMenuXMLCallback(&$node, $args)
{
	$user	= &JFactory::getUser();
	$menu	= &JSite::getMenu();
	$active	= $menu->getActive();
	$path	= isset($active) ? array_reverse($active->tree) : null;
	if (($args['end']) && ($node->attributes('level') >= $args['end']))
	{
		$children = &$node->children();
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
			if ($child->attributes('access') > $user->get('aid', 0)) {
				$node->removeChild($child);
			}
		}
	}

	if (($node->name() == 'li') && isset($node->ul)) {
		$node->addAttribute('class', 'haschild');
		$children = $node->children();
		if ($node->attributes('level') == 1) {
			if ($children[0]->name() == 'a' or $children[0]->name() == 'span') {
				$children[0]->addAttribute('class', 'haschild');
			}
		} else {
			if ($children[0]->name() == 'a' or $children[0]->name() == 'span') {
				$children[0]->addAttribute('class', 'child');
			}
		}

	}

	if (isset($path) && in_array($node->attributes('id'), $path))
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
			$node->addAttribute('class', $node->attributes('class'));
		}
	}

	if (isset($path) && $node->attributes('id') == $path[0]) {
		if ($node->attributes('class')) {
			$node->addAttribute('class', $node->attributes('class').' current');
		} else {
			$node->addAttribute('class', 'current');
		}
	}

	$node->removeAttribute('id');
	$node->removeAttribute('level');
	$node->removeAttribute('access');
}
	define('modMainMenuXMLCallbackDefined', true);
}

modMainMenuHelper::render($params, 'modMainMenuXMLCallback');
?>