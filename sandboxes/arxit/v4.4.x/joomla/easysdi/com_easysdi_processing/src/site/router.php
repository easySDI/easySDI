<?php
/**
* @version     4.4.5
* @package     com_easysdi_processing
* @copyright   Copyright (C) 2013-2017. All rights reserved.
* @license     GNU General Public License version 3 or later; see LICENSE.txt
* @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/*function Easysdi_processingBuildRoute(&$query)
{
	$segments = array();

	foreach (['view','id',] as $value) {
		if(isset($query[$value])){
			$segments[] = $query[$value];
			unset($query[$value]);
		}
	};

	return $segments;
}

function Easysdi_processingParseRoute($segments)
{
	$vars = array();
	// Count segments
	$count = count($segments);
	//Handle View and Identifier


	foreach (['view','id'] as $key => $value) {
		if (isset($segments[$key])) $vars[$value]=$segments[$key];
	}

	return $vars;
}*/



function Easysdi_processingBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (isset($query['id'])) {
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}


function Easysdi_processingParseRoute($segments)
{
	$vars = array();

	// view is always the first element of the array
	$count = count($segments);

	if ($count)
	{
		$count--;
		$segment = array_shift($segments);
		if (is_numeric($segment)) {
			$vars['id'] = $segment;
		} else {
			$vars['task'] = $segment;
		}
	}

	if ($count)
	{
		$count--;
		$segment = array_shift($segments) ;
		if (is_numeric($segment)) {
			$vars['id'] = $segment;
		}
	}

	return $vars;
}

?>