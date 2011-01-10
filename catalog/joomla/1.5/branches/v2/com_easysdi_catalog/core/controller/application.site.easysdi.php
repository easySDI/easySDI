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


class SITE_application 
{
	function listApplication($object_id, $option) {
		global  $mainframe;
		$db =& JFactory::getDBO();
		$user = JFactory::getUser();
		
		$option=JRequest::getVar("option");
		$context	= $option.'.listApplication';
		$limit		= $mainframe->getUserStateFromRequest($option.'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');
		
		// Problème avec le retour au début ou à la page une, quand limitstart n'est pas présent dans la session.
		// La mise à zéro ne se fait pas, il faut donc la forcer
		if (! isset($_REQUEST['limitstart']))
			$limitstart=0;
		
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor($limitstart / $limit) * $limit) : 0 );
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( $option.".filter_order",		'filter_order',		'name',	'word' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.".filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "name" 
			and $filter_order <> "windowname"
			and $filter_order <> "url")
		{
			$filter_order		= "name";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		$query = "	SELECT COUNT(*) 
					FROM #__sdi_application a 
					WHERE a.object_id=".$object_id;
		$db->setQuery( $query );
		$total = $db->loadResult();
		
		// Create the pagination object
		jimport('joomla.html.pagination');
		$pagination = new JPagination($total, $limitstart, $limit);
		
		// Recherche des enregistrements selon les limites
		$query = "	SELECT 	* 
					FROM #__sdi_application a 
					WHERE a.object_id=".$object_id;
		$query .= $orderby;
		
		
		$db->setQuery($query, $pagination->limitstart, $pagination->limit);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			//exit();
		}
		
		$rowObject = new object($db);
		$rowObject->load($object_id);
		
		$lists['order_Dir'] 	= $filter_order_Dir;
		$lists['order'] 		= $filter_order;
		
		HTML_application::listApplication($pagination, $rows, $object_id, $rowObject->name, $option, $lists);
	}

	function editApplication($id, $option)
	{
		
?>
		<script type="text/javascript">
			function verify() 
			{
				var form = document.adminForm;
				
				// do field validation
				if (form.name.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_APPLICATION_SUBMIT_NONAME', true ); ?>" );
				}
				else if (form.windowname.value == "") 
				{
					alert( "<?php echo JText::_( 'CATALOG_APPLICATION_SUBMIT_NOWINDOWNAME', true ); ?>" );
				}
				else if (form.url.value == "") 
				{
				alert( "<?php echo JText::_( 'CATALOG_APPLICATION_SUBMIT_NOURL', true ); ?>" );
				} 
				else 
				{
					form.task.value='saveApplication';
					form.submit();
				}
			}
		</script>
		
		<?php 
		$database =& JFactory::getDBO(); 
		$user = & JFactory::getUser();
		
		$rowApplication = new application( $database );
		$rowApplication->load( $id );
		
		$object_id = JRequest::getVar ('object_id');
		
		/*
		 * If the item is checked out we cannot edit it... unless it was checked
		 * out by the current user.
		 */
		if ( JTable::isCheckedOut($user->get('id'), $rowApplication->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $rowApplication->name);
			$mainframe->redirect("index.php?option=$option&task=listApplication&object_id=".$object_id, $msg );
		}

		$rowApplication->checkout($user->get('id'));
		
		// Récupération des types mysql pour les champs
		$tableFields = array();
		$tableFields = $database->getTableFields("#__sdi_application", false);
		
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
		
		HTML_application::editApplication($rowApplication, $fieldsLength, $object_id, $option);
	}
	
	function saveApplication($option){
		global  $mainframe;
		$database=& JFactory::getDBO();
		$option =  JRequest::getVar("option");
		$rowApplication =& new application($database);
		$user =& JFactory::getUser();
		$object_id=JRequest::getVar("object_id");
		
		if (!$rowApplication->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listApplication&object_id='.$object_id, false ));
			exit();
		}

		// Générer un guid
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'core'.DS.'common.easysdi.php');
		if ($rowApplication->guid == null)
			$rowApplication->guid = helper_easysdi::getUniqueId();
		
	
		if (!$rowApplication->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			//$mainframe->redirect("index.php?option=$option&task=listObject" );
			$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listApplication&object_id'.$object_id, false ));
			exit();
		}
		
		$rowApplication->checkin();
	}

	function deleteApplication($cid, $option)
	{
		global $mainframe;
		$database =& JFactory::getDBO();

		$object_id = JRequest::getVar('object_id',0);
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage("S�lectionnez un enregistrement � supprimer","error");
			$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listApplication&object_id'.$object_id, false ));
			exit;
		}
		
		foreach( $cid as $id )
		{
			$rowApplication = new application( $database );
			$rowApplication->load( $id );

			if (!$rowApplication->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listApplication&object_id'.$object_id, false ));
			}
		}
	}
	
	/**
	* Cancels an edit operation
	*/
	function cancelApplication($id, $option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();

		$object_id = JRequest::getVar ('object_id');
		
		// Check the application in if checked out
		$rowApplication = new application( $database );
		$rowApplication->bind(JRequest::get('post'));
		$rowApplication->checkin();
		
		//$mainframe->redirect(JRoute::_('index.php?option='.$option.'&task=listApplication&object_id'.$object_id, false ));
	}
	
	/**
	* Back
	*/
	function backApplication($option)
	{
		global $mainframe;

		// Initialize variables
		$database = & JFactory::getDBO();
		$object_id = JRequest::getVar('object_id',0);
		
		// Récupérer les états du listing des objets, pour éviter que les états des applications soient utilisés
		// alors qu'on change de contexte
		JRequest::setVar('filter_order', $mainframe->getUserState($option."listObject.filter_order"));
		JRequest::setVar('filter_order_Dir', $mainframe->getUserState($option."listObject.filter_order_Dir"));
		
		// Check the object in if checked out
		$rowObject = new object( $database );
		$rowObject->load($object_id);
		$rowObject->checkin();
	}
}

?>