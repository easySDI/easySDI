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

class perimeter extends JTable
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
	var $urlwms=null;
	var $minscale=0;
  	var $maxscale=-1;
	var $minresolution=0;
	var $maxresolution=0;
	var $imgformat=null;
	var $layername=null;
	var $urlwfs=null;
	var $featuretype=null;
	var $fieldid=null;
	var $fieldname=null;
	var $fieldarea=null;
	var $fieldsearch=null;
	var $fieldfilter=null;
	var $filterperimeter_id=null;
	var $islocalisation=0;
	var $maxfeatures=-1;
	var $multipleselection=1;
	var $searchbox=0;
	var $sort=0;
	var $user=null;
	var $password=null;
	var $account_id=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_perimeter', 'id', $db ) ;    		
	}
	
	function store ()
	{
		$user = JFactory::getUser();
		$account = new accountByUserId($this->_db);
		$account->load($user->id);
		if ($this->id == '0'){
			$this->created =date('d.m.Y H:i:s');
			$this->createdby = $account->id;	 			 			
		}
		$this->updated = date('d.m.Y H:i:s'); 
		$this->updatedby = $account->id;
		
		if($this->ordering == 0)
		{
			$this->_db->setQuery( "SELECT COUNT(*) FROM  #__sdi_perimeter " );
			$this->ordering = $this->_db->loadResult() + 1;
		}
		
		return parent::store();
	}
	
	function delete ()
	{
		$this->_db->setQuery( "SELECT *  FROM #__sdi_perimeter WHERE ordering > $this->ordering  order by ordering ASC" );
		$rows = $this->_db->loadObjectList() ;
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}	
		
		$o = $this->ordering;
		foreach ($rows as $row )
		{
			$this->_db->setQuery( "update #__sdi_perimeter set ordering= $o where id =$row->id" );	
			$this->_db->query();
			if ($this->_db->getErrorNum()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			$o = $o+1;
		}	
		return parent::delete();
	}

}


?>