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

?>
<script type="text/javascript">
	function submitbutton(pressbutton) 
	{
		var form = document.adminForm;
		if (pressbutton != 'saveLanguage' && pressbutton != 'applyLanguage') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','codelang_id') < 1) 
		{
			alert( "<?php echo JText::_( 'You must provide a code.', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 

class ADMIN_language {

	
	function listLanguage($option) {
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$context	= $option.'.listLanguage';
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );

		
		$query = "SELECT COUNT(*) FROM #__sdi_language";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "label" and $filter_order <> "ordering" and $filter_order <> "code" and $filter_order <> "codelang" and $filter_order <> "isocode" and $filter_order <> "defaultlang" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);

		// Recherche des enregistrements selon les limites
		$query = "SELECT l.*, c.code as codelang FROM #__sdi_language l LEFT OUTER JOIN #__sdi_list_codelang c ON l.codelang_id=c.id ";
		$query .= $orderby;
		$db->setQuery( $query, $pagination->limitstart, $pagination->limit);
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		

		//$rows = array_slice( $rows, $pageNav->limitstart, $pageNav->limit );
		HTML_language::listLanguage($rows, $pagination, $option,  $filter_order_Dir, $filter_order);
	}

	
	//id = 0 means new Config entry
	function editLanguage( $id, $option ) {
		$database =& JFactory::getDBO(); 
		$rowLanguage= new language( $database );
		$rowLanguage->load( $id );

		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_language", false);
		
		// Parcours des champs pour extraire les informations utiles:
		// - le nom du champ
		// - sa longueur en caract�res
		$fieldsLength = array();
		foreach($tableFields as $table)
		{
			foreach ($table as $field)
			{
				if (substr($field->Type, 0, strlen("varchar")) == "varchar")
				{
					$length = strpos($field->Type, ")")-strpos($field->Type, "(")-1;
					$fieldsLength[$field->Field] = substr($field->Type, strpos($field->Type, "(")+ 1, $length);
				}
			} 
		}

		$codes=array();
		$codes[] = JHTML::_('select.option','0', JText::_("CORE_LANGUAGE_LIST_CODELANG_SELECT") );
		$database->setQuery("SELECT id AS value, name as text FROM #__sdi_list_codelang ORDER BY name");
		$codes = array_merge( $codes, $database->loadObjectList() );
		
		
		HTML_language::editLanguage($rowLanguage, $fieldsLength, $codes, $option );
	}
	
	function saveLanguage( $option ) 
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
				
		$rowLanguage= new language( $database );
		
		if (!$rowLanguage->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listLanguage" );
			exit();
		}	

		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowLanguage->guid == null)
			$rowLanguage->guid = helper_easysdi::getUniqueId();
		
		
		if (!$rowLanguage->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listLanguage" );
			exit();
		}
		
		// Au cas o� on sauve avec Apply, recharger la page 
		$task = JRequest::getCmd( 'task' );
		switch ($task)
		{
			case 'applyLanguage' :
				// Reprendre en �dition la langue
				TOOLBAR_language::_EDIT();
				ADMIN_language::editLanguage($rowLanguage->id,$option);
				break;

			case 'saveLanguage' :
			default :
				break;
		}
	}
	
	function removeLanguage($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listLanguage" );
			exit;
		}
		foreach( $id as $language_id )
		{
			$rowLanguage= new language( $database );
			$rowLanguage->load( $language_id );
			
			if (!$rowLanguage->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listLanguage" );
				exit();
			}
		}
	}
	
	function setDefault($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 

		$rowLanguage= new language( $database );
		$rowLanguage->load( $id );
		
		// Effacer les autres valeurs par d�faut
		$query="UPDATE #__sdi_language SET defaultlang=0"; 
		$database->setQuery( $query);	
		if (!$database->query()) 
		{
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
			
		// Mettre la valeur par d�faut sur l'entr�e s�lectionn�e
		$rowLanguage->defaultlang = 1;
		
		if (!$rowLanguage->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listLanguage" );
			exit();
		}
		
		$mainframe->enqueueMessage(JText::_('Default value saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listLanguage" );
		exit();
	}
	
	function saveOrder($option)
	{
		global $mainframe;

		// Initialize variables
		$db			= & JFactory::getDBO();

		$cid		= JRequest::getVar( 'cid', array(0));
		$order		= JRequest::getVar( 'ordering', array (0));
		$total		= count($cid);
		$conditions	= array ();

		JArrayHelper::toInteger($cid, array(0));
		JArrayHelper::toInteger($order, array(0));

		// Update the ordering for items in the cid array
		for ($i = 0; $i < $total; $i ++)
		{
			// Instantiate an article table object
			$row = new language( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listLanguage" );
					exit();
				}
			}
		}

		$cache = & JFactory::getCache('com_easysdi_core');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listLanguage" );
		exit();
	}
	
	function orderContent($direction, $option)
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array());

		if (isset( $cid[0] ))
		{
			$row = new language( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_core');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listLanguage" );
		exit();
	}
	
	function changeContent( $state = 0 )
	{
		global $mainframe;
		
		// Initialize variables
		$db		= & JFactory::getDBO();
		
		$cid = JRequest::getVar('cid', array());
		JArrayHelper::toInteger($cid);
		$option	= JRequest::getCmd( 'option' );
		$task	= JRequest::getCmd( 'task' );
		$total	= count($cid);
		$cids	= implode(',', $cid);
		
		$query = 'UPDATE #__sdi_language' .
				' SET published = '. (int) $state .
				' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listLanguage" );
			exit();
		}

		switch ($state)
		{
			case 1 :
				$msg = JText::sprintf('Item successfully Published');
				break;

			case 0 :
			default :
				$msg = JText::sprintf('Item successfully Unpublished');
				break;
		}

		$cache = & JFactory::getCache('com_easysdi_core');
		$cache->clean();
		
		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listLanguage" );
		exit();
	}
}

?>
