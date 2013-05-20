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

class partnerByUserId extends JTable
{
	var $user_id=null;
	var $partner_id=null;
	var $root_id=null;
	var $parent_id=null;
	var $state_id=null;
	var $partner_code=null;	
	var $partner_acronym=null;
	var $partner_url=null;
	var $partner_description=null;
	var $partner_contract=0;
	var $partner_logo=0;
/*	var $partner_entry=null;*/
	/*var $partner_exit=null;*/
	var $notify_new_metadata=0;
	var $notify_distribution=0;
	var $notify_order_ready=0;
	var $rebate=0;
 	var $isrebate=0;
 	
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_community_partner', 'user_id', $db ) ;    		
	}

}

class partnerByPartnerId extends JTable
{
	var $user_id=null;
	var $partner_id=null;
	var $root_id=null;
	var $parent_id=null;
	var $state_id=null;
	var $partner_code=null;	
	var $partner_acronym=null;
	var $partner_url=null;
	var $partner_description=null;
	var $partner_contract=0;
	var $partner_logo=0;
	var $partner_entry=null;
	var $partner_exit=null;
	var $notify_new_metadata=0;
	var $notify_distribution=0;
	var $notify_order_ready=0;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__easysdi_community_partner', 'partner_id', $db ) ;    		
	}

}

class address extends JTable
{
	var $address_id=null;
	var $partner_id=null;
	var $type_id=null;
	var $title_id=null;
	var $country_code='CH';
	var $address_corporate_name1=null;
	var $address_corporate_name2=null;
	var $address_agent_firstname=null;
	var $address_agent_lastname=null;
	var $address_agent_function=null;
	var $address_street1=null;
	var $address_street2=null;
	var $address_postalcode=null;
	var $address_locality=null;
	var $address_phone=null;
	var $address_fax=null;
	var $address_email=null;
	var $address_update=null;

	// Class constructor
	function __construct( &$db )
	{
    		parent::__construct( '#__easysdi_community_address', 'address_id', $db );
	}
}

?>
