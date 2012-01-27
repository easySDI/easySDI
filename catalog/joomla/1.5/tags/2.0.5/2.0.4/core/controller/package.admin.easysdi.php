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
		if (pressbutton != 'savePackage' && pressbutton != 'applyPackage') {
			submitform( pressbutton );
			return;
		}
		// do field validation
		if (form.name.value == "") 
		{
			alert( "<?php echo JText::_( 'You must provide a name.', true ); ?>" );
		}
		else if (getSelectedValue('adminForm','profile_id') < 1) 
		{
			alert( "<?php echo JText::_( 'Please select a profile.', true ); ?>" );
		} 
		else if (getSelectedValue('adminForm','class_id') < 1) 
		{
			alert( "<?php echo JText::_( 'Please select a class.', true ); ?>" );
		}
		else 
		{
			submitform( pressbutton );
		}
	}
</script>

<?php 
class ADMIN_package {
	function listPackage($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);

		$query = "SELECT COUNT(*) FROM #__sdi_package";					
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
	
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "name" and $filter_order <> "ordering" and $filter_order <> "created" and $filter_order <> "updated")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		
		// Recherche des enregistrements selon les limites
		$query = "SELECT * FROM #__sdi_package ";
		$query .= $orderby;
		
		if ($use_pagination) {
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else{
			$db->setQuery( $query);
		}
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}		
		
		HTML_package::listPackage(&$rows, &$pageNav, $option,  $filter_order_Dir, $filter_order, $use_pagination);
	}
	
	function editPackage($id, $option)
	{
		$database =& JFactory::getDBO(); 
		$rowPackage = new package( $database );
		$rowPackage->load( $id );
		
		$profiles = array();
		$profiles[] = JHTML::_('select.option','0', JText::_("EASYSDI_PROFILE_LIST") );
		$database->setQuery( "SELECT id AS value, name as text FROM #__sdi_profile ORDER BY name" );
		$profiles = array_merge( $profiles, $database->loadObjectList() );
		
		$classes = array();
		$classes[] = JHTML::_('select.option','0', JText::_("EASYSDI_CHILDCLASSES_LIST") );
		if ($rowPackage->profile_id<>0)
		{
			// R�cup�rer la classe root du profil s�lectionn� pour ce package 
			$database->setQuery( "SELECT c.id FROM #__sdi_class c, #__sdi_profile p WHERE p.class_id=c.id AND p.id=".$rowPackage->profile_id );
			$rootId = $database->loadResult();
			//echo $rootId."<br>";
			
			// Liste des classes enfant de la classe root du profil
			$database->setQuery( "SELECT c.id AS value, c.name as text FROM #__sdi_class c, #__sdi_relation rel WHERE rel.classchild_id=c.id AND rel.parent_id = ".$rootId." ORDER BY c.name" );
			$classes = array_merge( $classes, $database->loadObjectList() );
		}
		
		// Correspondance Classe racine -> Enfants du premier niveau
		$childClasses=array();
		$database->setQuery( "SELECT rel.parent_id as parent, c.id AS value, c.name as text FROM #__sdi_class c, #__sdi_relation rel WHERE rel.classchild_id=c.id ORDER BY c.name" );
		$childClasses = $database->loadObjectList();
		
		// Correspondance Profil -> Classe racine
		$rootClasses=array(); 
		$database->setQuery( "SELECT p.id as pack, c.id as rootId FROM #__sdi_class c, #__sdi_profile p WHERE p.class_id=c.id");
		$rootClasses = $database->loadObjectList();
		
		// R�cup�ration des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_package", false);
		
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
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
		// Les labels
		$labels = array();
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$rowPackage->guid."' AND language_id=".$lang->id);
			$label = $database->loadResult();
			
			$labels[$lang->id] = $label;
		}
			
		HTML_package::editPackage($rowPackage, $profiles, $classes, $option, $rootId, $childClasses, $rootClasses, $fieldsLength, $languages, $labels);
	}
	
	function savePackage($option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 
		$user =& JFactory::getUser();
		
		$rowPackage= new package( $database );
		
		if (!$rowPackage->bind( $_POST )) {
		
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=listPackage" );
			exit();
		}		
		
		// G�n�rer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowPackage->guid == null)
			$rowPackage->guid = helper_easysdi::getUniqueId();
		
		if (!$rowPackage->store(false)) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listPackage" );
			exit();
		}
		
		// Langues � g�rer
		$languages = array();
		$database->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $database->loadObjectList() );
		
	
	
		// Stocker les labels
		foreach ($languages as $lang)
		{
			$database->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$rowPackage->guid."' AND language_id='".$lang->id."'");
			$total = $database->loadResult();
			
			if ($total > 0)
			{
				//Update
				$database->setQuery("UPDATE #__sdi_translation SET label='".addslashes($_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$rowPackage->guid."' AND language_id=".$lang->id);
				if (!$database->query())
					{	
						$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
						return false;
					}
			}
			else
			{
				// Create
				$database->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$rowPackage->guid."', ".$lang->id.", '".addslashes($_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$database->query())
				{	
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
	}
	
	function removePackage($id, $option)
	{
		global $mainframe;
			
		$database=& JFactory::getDBO(); 

		if (!is_array( $id ) || count( $id ) < 1) {
			//echo "<script> alert('S�lectionnez un enregistrement � supprimer'); window.history.go(-1);</script>\n";
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect("index.php?option=$option&task=listAttribute" );
			exit;
		}
		foreach( $id as $package_id )
		{
			$rowPackage= new package( $database );
			$rowPackage->load( $package_id );
			
			if (!$rowPackage->delete()) {			
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listPackage" );
				exit();
			}
		}
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
			$row = new package( $db );
			
			$row->load( (int) $cid[$i] );
			if ($row->ordering != $order[$i]) {
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listAttribute" );
					exit();
				}
			}
		}
		
		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listPackage" );
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
			$row = new package( $db );
			$row->load( (int) $cid[0] );
			$row->move($direction);

			$cache = & JFactory::getCache('com_easysdi_catalog');
			$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listPackage" );
		exit();
	}
}
?>