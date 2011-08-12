<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2009 Antoine Elbel & R�my Baud (aelbel@solnet.ch remy.baud@asitvd.ch)
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

class publishConfig extends JTable
{
	var $id=null;
	var $default_publisher_layer_number=0;
	var $default_dataset_upload_size=0;
	var $default_diffusion_server_id=null;
	var $default_datasource_handler=null;
	var $default_prefered_crs=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_config', 'id', $db ) ;    		
	}
}

class crsObject extends JTable
{
	var $id=null;
	var $code=null;
	var $name=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_crs', 'id', $db ) ;    		
	}
}

class diffusor extends JTable
{
	var $id=null;
	var $diffusor_type_id=0;
	var $diffusion_server_host=null;
	var $diffusion_server_port=0;
	var $diffusion_server_name=null;
	var $diffusion_server_service_name=null;
	var $diffusion_server_password=null;
	var $diffusion_server_username=null;
	var $diffusion_server_db_name=null;
	var $diffusion_server_db_schema=null;
	var $diffusion_server_db_template=null;
	var $diffusion_server_db_port=0;
	var $diffusion_server_db_username=null;
	var $diffusion_server_db_password=null;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_diffuser', 'id', $db ) ;
	}
}

class publishUser extends JTable
{
	var $id=null;
	var $easysdi_user_id=0;
	var $publish_user_max_layers=0;
	var $publish_user_total_space=0;
	var $publish_user_diff_server_id=0;
	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_user', 'id', $db ) ;
	}
}

class script extends JTable
{
	var $id=null;
	var $publish_script_name=null;
	var $publish_script_file=null;
	var $publish_script_description=null;
	var $publish_script_conditions=null;
	var $publish_script_is_public=null;
	var $publish_script_is_generic=null;

	
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_publish_script', 'id', $db ) ;
	}
}

?>