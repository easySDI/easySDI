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
		
	// Class constructor
	function __construct( $table, $id, &$db )
	{
		parent::__construct ( $table , $id , $db ) ;    		
	}
	
	function store ()
	{
		$user = JFactory::getUser();
		$account = new accountByUserId($this->_db);
		$account->load($user->id);
		if ($this->id == '0'){
			$this->created =date('Y-m-d H:i:s');
			$this->createdby = $account->id;	 			 			
		}
		if ($this->guid == null || $this->guid == '0' )
		{
			$this->guid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0x0fff ) | 0x4000, mt_rand( 0, 0x3fff ) | 0x8000, mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ) );
		}
		
		if($this->ordering == 0)
		{
			$this->_db->setQuery( "SELECT COUNT(*) FROM  $this->_tbl " );
			$this->ordering = $this->_db->loadResult() + 1;
		}
			
		$this->updated = date('Y-m-d H:i:s'); 
		$this->updatedby = $account->id;
		
		return parent::store();
	}
	
	function delete ()
	{
		$this->_db->setQuery( "SELECT *  FROM $this->_tbl WHERE ordering > $this->ordering  order by ordering ASC" );
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
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$o = $o+1;
		}	
		return parent::delete();
	}

	 function publishedLanguages ()
	 {
	 	$languages = array();
		$this->_db->setQuery( "SELECT l.id, c.code FROM #__sdi_language l, #__sdi_list_codelang c WHERE l.codelang_id=c.id AND published=true ORDER BY id" );
		$languages = array_merge( $languages, $this->_db->loadObjectList() );
		return $languages;
	 } 
	 
	 function getLabels ()
	 {
	 	// Les labels
		$labels = array();
		$languages = $this->publishedLanguages();
		foreach ($languages as $lang)
		{
			$this->_db->setQuery("SELECT label FROM #__sdi_translation WHERE element_guid='".$this->guid."' AND language_id=".$lang->id);
			$label = $this->_db->loadResult();
			$labels[$lang->id] = $label;
		}
		return $labels;
	 }
	 
	 function storeLabels ()
	 {
	 // Stocker les labels
		 $user =& JFactory::getUser();
		 $languages = $this->publishedLanguages();
		foreach ($languages as $lang)
		{
			$this->_db->setQuery("SELECT count(*) FROM #__sdi_translation WHERE element_guid='".$this->guid."' AND language_id=".$lang->id);
			$total = $this->_db->loadResult();
			
			if ($total > 0)
			{
				//Update
				$this->_db->setQuery("UPDATE #__sdi_translation SET label='".str_replace("'","\'",$_POST['label_'.$lang->code])."', updated='".$_POST['updated']."', updatedby=".$_POST['updatedby']." WHERE element_guid='".$this->guid."' AND language_id=".$lang->id);
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
	 }

}


?>