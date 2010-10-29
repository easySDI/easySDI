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

class mailing extends JTable
{
	var $user_id=null;
	var $partner_id=null;
	var $root_id=null;
	var $parent_id=null;
	var $profile_id=null;
	var $category_id=null;
	var $state_id=null;
	var $payment_id=null;
	var $activity_id=null;
	var $member_id=null;
	var $collaborator_id=null;
	var $partner_code=null;
	var $partner_acronym=null;
	var $partner_url=null;
	var $partner_description=null;
	var $partner_invoice=null;
	var $partner_call1=null;
	var $partner_call2=null;
	var $partner_entry=null;
	var $partner_exit=null;
	var $partner_contract=0;
	var $partner_inhabitant=0;
	var $partner_electricity=0;
	var $partner_gas=0;
	var $partner_heating=0;
	var $partner_telcom=0;
	var $partner_network=0;
	var $partner_update=null;

	// Class constructor
	function partner( &$db )
	{
    		$this->mosDBTable( '#__easysdi_community_partner', 'partner_id', $db );
	}
}

class contact extends JTable
{
	var $address_id=null;
	var $partner_id=null;
	var $type_id=null;
	var $title_id=null;
	var $country_code=CH;
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
	function address( &$db )
	{
    		$this->mosDBTable( '#__easysdi_community_address', 'address_id', $db );
	}

}

?>
