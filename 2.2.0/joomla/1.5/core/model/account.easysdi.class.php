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

class account extends JTable
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
	var $ordering=null;
	var $publish_id=null;
	var $user_id=null;
	var $root_id=null;
	var $parent_id=null;
	var $state_id=null;
	var $acronym=null;
	var $url=null;
	var $invoice=null;
	var $call1=null;
	var $call2=null;
	var $contract=0;
	var $notify_new_metadata=0;
	var $notify_distribution=0;
	var $notify_order_ready=0;
	var $rebate=0;
 	var $isrebate=0;
 	var $logo=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_account', 'id', $db ) ;
	}
	
	static function getEasySDIAccountsList ()
	{
		$database =& JFactory::getDBO();
		$database->setQuery( "SELECT p.id as value, u.name as text FROM #__users u INNER JOIN #__sdi_account p ON u.id = p.user_id " );
		return  $database->loadObjectList();
	}
	
	function getParentList(){
		$resultList =  array();
		
		$root_id = $this->root_id;
		
		//Current account is root
		if($root_id == null){
			$resultList[]=$this->id;
			return $resultList;
		}
		
		$parent_id = null;
		$account_id = $this->id;
		
		while ($account_id != $root_id){
			$parent_id = account::getParentId($account_id);
			$resultList[]=$parent_id;
			$account_id = $parent_id;
		}
		return $resultList;
	}
	
	static function getParentId ($account_id){
		global  $mainframe;	
		$db =& JFactory::getDBO();
		$db->setQuery( "SELECT parent_id FROM #__sdi_account WHERE id = ".$account_id);
		$parent_id = $db->loadResult();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg());
			return null;
		}
		return $parent_id;
	}

}

class accountByUserId extends JTable
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
	var $ordering=null;
	var $publish_id=null;
	var $user_id=null;
	var $root_id=null;
	var $parent_id=null;
	var $state_id=null;
	var $acronym=null;
	var $url=null;
	var $invoice=null;
	var $call1=null;
	var $call2=null;
	var $contract=0;
	var $notify_new_metadata=0;
	var $notify_distribution=0;
	var $notify_order_ready=0;
	var $rebate=0;
 	var $isrebate=0;
 	var $logo=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_account', 'user_id', $db ) ;
	}

}

class address extends JTable
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
	var $ordering=null;
	var $account_id=null;
	var $type_id=null;
	var $title_id=null;
	var $country_id=1;
	var $corporatename1=null;
	var $corporatename2=null;
	var $agentfirstname=null;
	var $agentlastname=null;
	var $function=null;
	var $street1=null;
	var $street2=null;
	var $postalcode=null;
	var $locality=null;
	var $phone=null;
	var $fax=null;
	var $email=null;
	
	// Class constructor
	function __construct( &$db )
	{
    		parent::__construct( '#__sdi_address', 'id', $db );
	}
}


?>
