<?php
defined('_JEXEC') or die('Restricted access');


class ADMIN_query {
	function executeQuery()
	{
		$q=$_GET["q"];
		
		$database =& JFactory::getDBO(); 
		
		$rendertypes = array();
		$rendertypes[] = JHTML::_('select.option','0', JText::_("EASYSDI_RENDERTYPE_LIST") );
		$database->setQuery( "SELECT rt.id AS value, rt.name as text FROM #__sdi_list_rendertype rt, #__sdi_list_renderattributetype rat, #__sdi_attribute a WHERE rt.id = rat.rendertype_id and rat.attributetype_id=a.attributetype_id and a.id=".$q." ORDER BY rt.name");
		$rendertypes = array_merge( $rendertypes, $database->loadObjectList() );
		
		$codevalues=array();
		$codevalues[] = JHTML::_('select.option','', '');
		$database->setQuery( "SELECT label as value, label as text FROM #__sdi_codevalue WHERE attribute_id=".$q." ORDER BY name" );
		$codevalues = array_merge( $codevalues, $database->loadObjectList() );
				
		//print_r($results);
		echo JHTML::_("select.genericlist",$rendertypes, 'result', 'size="1" class="inputbox"', 'value', 'text');
		echo JHTML::_("select.genericlist",$codevalues, 'result2', 'size="1" class="inputbox"', 'value', 'text');
		die();
		mysql_close($con);
		
	}
	
}
?> 