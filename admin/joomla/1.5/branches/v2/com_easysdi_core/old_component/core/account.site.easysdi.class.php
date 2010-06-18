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

class accountByAccountId extends JTable
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
