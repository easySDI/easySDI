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

class sdiTable extends JTable
{
	var $id=null;
	var $guid=null;
	var $code=null;
	var $name=null;
	var $description=null;
	var $created=null;
	var $updated=null;
	var $createdby=null;
	var $updatedby=null;
	var $label=null;
	var $ordering=0;
	var $checked_out=null;
	var $checked_out_time=null;
		
	// Class constructor
	function __construct( $table, $id, &$db )
	{
		parent::__construct ( $table , $id , $db ) ;
	}

	function store ($filter="", $filter_value="")
	{
		$user = JFactory::getUser();
		$account = new accountByUserId($this->_db);
		$account->load($user->id);
		if ($this->id == null || $this->id == '0'){
			$this->created =date('Y-m-d H:i:s');
			$this->createdby = $account->id;	 			 			
		}
		if ($this->guid == null || $this->guid == '0' )
		{
			$this->guid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}

		if($this->ordering == 0)
		{
			if($filter)
			{
				$this->_db->setQuery( "SELECT COUNT(*) FROM  $this->_tbl WHERE $filter = '".$filter_value."'" );
				$this->ordering = $this->_db->loadResult() + 1;
			}
			else
			{
				$this->_db->setQuery( "SELECT COUNT(*) FROM  $this->_tbl " );
				$this->ordering = $this->_db->loadResult() + 1;
			}
		}
			
		$this->updated = date('Y-m-d H:i:s');
		$this->updatedby = $account->id;

		return parent::store(true);
	}

	function delete ($filter="", $filter_value="")
	{
		global  $mainframe;
		if($filter)
		{
			$this->_db->setQuery( "SELECT *  FROM $this->_tbl WHERE ordering > $this->ordering AND $filter = '".$filter_value."' order by ordering ASC" );
		}
		else
		{
			$this->_db->setQuery( "SELECT *  FROM $this->_tbl WHERE ordering > $this->ordering  order by ordering ASC" );
		}
		$rows = $this->_db->loadObjectList() ;
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		$o = $this->ordering;
		foreach ($rows as $row )
		{
			$this->_db->setQuery( "update $this->_tbl set ordering= $o where id =$row->id" );
			$this->_db->query();
			if ($this->_db->getErrorNum()) {
				$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
				return false;
			}
			$o = $o+1;
		}

		$this->_db->setQuery("DELETE FROM #__sdi_translation WHERE element_guid='".$this->guid."'");
		$this->_db->query();
		if ($this->_db->getErrorNum()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}

		return parent::delete();
	}

	function orderDown($filter="", $filter_value="")
	{
		global  $mainframe;
		if($filter)
			$this->_db->setQuery( "select * from  $this->_tbl  where `ordering` > $this->ordering AND $filter = '".$filter_value."'  order by `ordering` " );
		else
			$this->_db->setQuery( "select * from  $this->_tbl  where `ordering` > $this->ordering   order by `ordering` " );
		$row = $this->_db->loadObject() ;
		if ($this->_db->getErrorNum()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}

		$this->_db->setQuery( "update $this->_tbl set `ordering`= $this->ordering where id =$row->id" );
		if (!$this->_db->query()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}

		$this->ordering = $row->ordering;
		$this->store();
		return true;
	}

	function orderUp($filter="", $filter_value="")
	{
		global  $mainframe;
		if($filter)
			$this->_db->setQuery( "select * from  $this->_tbl  where `ordering` < $this->ordering AND $filter = '".$filter_value."'  order by `ordering` desc " );
		else
			$this->_db->setQuery( "select * from  $this->_tbl  where `ordering` < $this->ordering   order by `ordering` desc" );
		$row = $this->_db->loadObject() ;
		if ($this->_db->getErrorNum()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}

		$this->_db->setQuery( "update $this->_tbl set `ordering`= $this->ordering where id =$row->id" );
		if (!$this->_db->query()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}

		$this->ordering = $row->ordering;
		$this->store();
		return true;
	}

	/*
	 function orderContent($direction, $option)
	 {
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array());

		if (isset( $cid[0] ))
		{
		$row = new object( $db );
		$row->load( (int) $cid[0] );
		$row->move($direction);

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();
		}

		$mainframe->redirect("index.php?option=$option&task=listObject" );
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
		$row = new object( $db );
			
		$row->load( (int) $cid[$i] );
		if ($row->ordering != $order[$i]) {
		$row->ordering = $order[$i];
		if (!$row->store()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		$mainframe->redirect("index.php?option=$option&task=listObject" );
		exit();
		}
		}
		}

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage(JText::_('New ordering saved'),"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listObject" );
		exit();
		}

		function changeState( $column, $state = 0 )
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

		$query = 'UPDATE #__sdi_attribute' .
		' SET '.$column.' = '. (int) $state .
		' WHERE id IN ( '. $cids .' )';
		$db->setQuery($query);
		if (!$db->query()) {
		$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
		exit();
		}

		if (count($cid) == 1) {
		$row = new attribute( $db );
		$row->checkin($cid[0]);
		}

		$msg = JText::sprintf('State successfully changed');

		$cache = & JFactory::getCache('com_easysdi_catalog');
		$cache->clean();

		$mainframe->enqueueMessage($msg,"SUCCESS");
		$mainframe->redirect("index.php?option=$option&task=listAttribute" );
		exit();
		}
		*/
	function publishedLanguages ()
	{
		global  $mainframe;
		$languages = array();
		$this->_db->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $this->_db->loadObjectList() );
		if ($this->_db->getErrorNum()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}
		return $languages;
	}

	function loadLabels ()
	{
		// Les labels
		$labels = array();
		$languages = $this->publishedLanguages();
		foreach ($languages as $lang)
		{
			$this->_db->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$this->guid."' AND language_id=".$lang->id);
			$label = $this->_db->loadResult();
			if ($this->_db->getErrorNum()) {
				$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
				return false;
			}
			$labels[$lang->id] = $label;
		}
		return $labels;
	}
	 
	 function storeLabels ()
	 {
	 	global  $mainframe;
		 $user =& JFactory::getUser();
		 $languages = $this->publishedLanguages();

		foreach ($languages as $lang)
		{
			$this->_db->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$this->guid."' AND language_id=".$lang->id);
			$total = $this->_db->loadResult();

			if ($total > 0)
			{
				//Update
				$this->_db->setQuery("UPDATE #__sdi_translation SET label='".str_replace("'","\'",$_POST['label_'.$lang->code])."', updated='".date('Y-m-d H:i:s')."', updatedby=".$user->id." WHERE element_guid='".$this->guid."' AND language_id=".$lang->id);
				if (!$this->_db->query())
				{
					$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
					return false;
				}
			}
			else
			{
				// Create
				$this->_db->setQuery("INSERT INTO #__sdi_translation (element_guid, language_id, label, created, createdby) VALUES ('".$this->guid."', ".$lang->id.", '".str_replace("'","\'",$_POST['label_'.$lang->code])."', '".date ("Y-m-d H:i:s")."', ".$user->id.")");
				if (!$this->_db->query())
				{
					$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
					return false;
				}
			}
		}
		return true;
	}

	function getObjectCount()
	{
		global  $mainframe;
		$this->_db->setQuery( "select count(*) from  $this->_tbl " );
		if (!$this->_db->query())
		{
			$mainframe->enqueueMessage($this->_db->getErrorMsg(),"ERROR");
			return false;
		}
		return $this->_db->loadResult();
	 }
	 
	 function getObjectListAsArray()
	 {
	 	$this->_db->setQuery( "SELECT id AS value, name AS text FROM ".$this->_tbl." order by name" );
	 	return  $this->_db->loadObjectList();
	 }
	 
	 function getFieldsLength()
	 {
		$tableFields = array();
		$tableFields = $this->_db->getTableFields($this->_tbl, false);
		
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
		return $fieldsLength;
	 }

	function tryCheckOut($option, $task)
	{
		global $mainframe;
		$user = & JFactory::getUser();
		if ( JTable::isCheckedOut($user->get('id'), $this->checked_out ))
		{
			$msg = JText::sprintf('DESCBEINGEDITTED', JText::_('The item'), $this->name);
			$mainframe->redirect("index.php?option=$option&task=$task", $msg );
		}
		$this->checkout($user->get('id'));
	}
}


?>