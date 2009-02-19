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


function com_install(){

	global  $mainframe;
	$db =& JFactory::getDBO();

	
	if (!file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'license.txt')){
		
		$mainframe->enqueueMessage("Core component does not exists. Easysdi_proxy could not be installed. Please install core component first.","ERROR");
		return false;
		
	}
	
	
	/**
	 * Creates the database structure
	 */
	/**
	 * Gets the component versions
	 */
	
	$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'" ;
	$db->setQuery( $query);
	$id = $db->loadResult();

	if ($id){
		
	}else{
		
	$mainframe->enqueueMessage("EASYSDI menu was not installed. Usually this menu is created during the installation of the easysdi core component. Please be sure that the easysdi_core component is installed before installing this component.","ERROR");
	return false;	
	//Insert the EasySdi Main Menu		
	/*$query =  "insert into #__components (name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values('Easy SDI','option=com_easysdi_core','option=com_easysdi_core','Easysdi main menu','com_easysdi_core','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
		$query =  "SELECT ID FROM #__components WHERE name ='Easy SDI'"  ;
		$db->setQuery( $query);
		$id = $db->loadResult();	*/
	}
	
	//Create the database table needed by funambol
    $aQuery = array();
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_user (
      username   varchar(255) binary not null,
      password   varchar(255) binary not null,
      email      varchar(255) binary,
      first_name varchar(255) binary,
      last_name  varchar(255) binary,
      constraint pk_user primary key (username)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_device (
      id                   varchar(128) binary not null,
      description          varchar(255),
      type                 varchar(255),
      client_nonce         varchar(255),
      server_nonce         varchar(255),
      server_password      varchar(255),
      timezone             varchar(32) ,
      convert_date         char(1)     ,
      charset              varchar(16) ,
      address              varchar(50) ,
      msisdn               varchar(50) ,
      notification_builder varchar(255),
      notification_sender  varchar(255),
      id_caps              bigint      ,
      constraint pk_device primary key (id)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_principal (
      username varchar(255) binary not null,
      device   varchar(128) binary not null,
      id       bigint       not null,
      constraint pk_principal primary key (id),
      constraint fk_device foreign key (device)
      references fnbl_device (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_sync_source (
      uri        varchar(128) binary not null,
      config     varchar(255) binary not null,
      name       varchar(200) binary not null,
      sourcetype varchar(128) binary not null,
    
      constraint pk_sync_source primary key (uri)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_last_sync (
      principal          bigint      not null,
      sync_source        varchar(128) binary not null,
      sync_type          integer     not null,
      status             integer,
      last_anchor_server varchar(20) binary,
      last_anchor_client varchar(20) binary,
      start_sync         bigint,
      end_sync           bigint,
    
      constraint pk_last_sync primary key (principal, sync_source),
    
      constraint fk_principal foreign key (principal)
      references fnbl_principal (id) on delete cascade on update cascade,
    
      constraint fk_source foreign key (sync_source)
      references fnbl_sync_source (uri) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_client_mapping (
      principal   bigint       not null,
      sync_source varchar(128) binary not null,
      luid        varchar(200) binary not null,
      guid        varchar(200) binary not null,
      last_anchor varchar(20)  binary,
    
      constraint pk_clientmapping primary key (principal, sync_source, luid, guid),
    
      constraint fk_principal_cm foreign key (principal)
      references fnbl_principal (id) on delete cascade on update cascade,
    
      constraint fk_source_cm foreign key (sync_source)
      references fnbl_sync_source (uri) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_id (
      idspace      varchar(30) binary not null,
      counter      bigint      not null,
      increment_by int         default 100,
    
      constraint pk_id primary key (idspace)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_module (
      id          varchar(128) binary not null,
      name        varchar(200) binary not null,
      description varchar(200) binary,
    
      constraint pk_module primary key (id)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_sync_source_type (
      id          varchar(128) binary not null,
      description varchar(200) binary,
      class       varchar(255) binary not null,
      admin_class varchar(255) binary,
    
      constraint pk_sst primary key (id)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_connector (
      id          varchar(128) binary not null,
      name        varchar(200) binary not null,
      description varchar(200) binary,
      admin_class varchar(255) binary,
    
      constraint pk_connector primary key (id)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_module_connector (
      module    varchar(128) binary not null,
      connector varchar(128) binary not null,
    
      constraint pk_mod_connector primary key (module, connector)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_connector_source_type (
      connector  varchar(128) binary not null,
      sourcetype varchar(128) binary not null,
    
      constraint pk_connector_sst primary key (connector, sourcetype)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_role (
      role        varchar(128) binary not null,
      description varchar(200) binary not null,
    
      constraint pk_role primary key (role)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_user_role (
      username varchar(255)  binary not null,
      role     varchar(128)  binary not null,
    
      constraint pk_user_role primary key (username,role),
    
      constraint fk_userrole foreign key (username)
      references fnbl_user (username) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_device_caps (
      id      bigint     not null,
      version varchar(16) not null,
      man     varchar(100),
      model   varchar(100),
      fwv     varchar(100),
      swv     varchar(100),
      hwv     varchar(100),
      utc     char(1)     not null,
      lo      char(1)     not null,
      noc     char(1)     not null,
    
      constraint pk_device_caps primary key (id)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_device_ext (
      id     bigint not null,
      caps   bigint,
      xname  varchar(255),
      xvalue varchar(255),
    
      constraint pk_dev_ext primary key (id),
    
      constraint fk_dev_ext foreign key (caps)
      references fnbl_device_caps (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_device_datastore (
      id          bigint       not null,
      caps        bigint      ,
      sourceref   varchar(128) not null,
      label       varchar(128),
      maxguidsize integer     ,
      dsmem       char(1)      not null,
      shs         char(1)      not null,
      synccap     varchar(32)  not null,
    
      constraint pk_dev_datastore primary key (id),
    
      constraint fk_dev_datastore foreign key (caps)
      references fnbl_device_caps (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_device_config (
      principal    bigint       not null,
      uri          varchar(128) binary not null,
      value        varchar(255) binary not null,
      last_update  bigint       not null,
      status       char         not null,
      constraint pk_config primary key (principal, uri)
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_cttype_rx (
      datastore bigint      not null,
      type      varchar(64) not null,
      version   varchar(16) not null,
      preferred char(1)     not null,
    
      constraint pk_ds_cttype_rx primary key (type,version,datastore),
    
      constraint fk_ds_cttype_rx foreign key (datastore)
      references fnbl_device_datastore (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_cttype_tx (
      datastore bigint      not null,
      type      varchar(64) not null,
      version   varchar(16) not null,
      preferred char(1)     not null,
    
      constraint pk_ds_cttype_tx primary key (type,version,datastore),
    
      constraint fk_ds_cttype_tx foreign key (datastore)
      references fnbl_device_datastore (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_ctcap (
      id        bigint      not null,
      datastore bigint      not null,
      type      varchar(64) not null,
      version   varchar(16) not null,
      field     char(1)     not null,
    
      constraint pk_ds_ctcap primary key (id),
    
      constraint fk_ds_ctcap foreign key (datastore)
      references fnbl_device_datastore (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_ctcap_prop (
      id        bigint      not null,
      ctcap     bigint      not null,
      name      varchar(64) not null,
      label     varchar(128),
      type      varchar(32) ,
      maxoccur  integer     ,
      maxsize   integer     ,
      truncated char(1)     not null,
      valenum   varchar(255),
    
      constraint pk_ds_ctcap_prop primary key (id),
    
      constraint fk_ds_ctcap_prop foreign key (ctcap)
      references fnbl_ds_ctcap (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_ctcap_prop_param (
      property bigint      not null,
      name     varchar(64) not null,
      label    varchar(128),
      type     varchar(32) ,
      valenum  varchar(255),
    
      constraint fk_ctcap_propparam foreign key (property)
      references fnbl_ds_ctcap_prop (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_filter_rx (
      datastore bigint      not null,
      type      varchar(64) not null,
      version   varchar(16) not null,
    
      constraint pk_ds_filter_rx primary key (type,version,datastore),
    
      constraint fk_ds_filter_rx foreign key (datastore)
      references fnbl_device_datastore (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_filter_cap (
      datastore  bigint      not null,
      type       varchar(64) not null,
      version    varchar(16) not null,
      keywords   varchar(255),
      properties varchar(255),
    
      constraint pk_ds_filter_cap primary key (type,version,datastore),
    
      constraint fk_ds_filter_cap foreign key (datastore)
      references fnbl_device_datastore (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "CREATE TABLE IF NOT EXISTS fnbl_ds_mem (
      datastore bigint,
      shared    char(1) not null,
      maxmem    integer,
      maxid     integer,
    
      constraint fk_ds_mem foreign key (datastore)
      references fnbl_device_datastore (id) on delete cascade on update cascade
    )";
    
    $aQuery[] = "create index ind_user           on fnbl_user               (username, password)";
    $aQuery[] = "create index ind_principal      on fnbl_principal          (username, device  )";
    $aQuery[] = "create index ind_device_ext     on fnbl_device_ext         (caps     )";
    $aQuery[] = "create index ind_datastore      on fnbl_device_datastore   (caps     )";
    $aQuery[] = "create index ind_cttype_rx      on fnbl_ds_cttype_rx       (datastore)";
    $aQuery[] = "create index ind_cttype_tx      on fnbl_ds_cttype_tx       (datastore)";
    $aQuery[] = "create index ind_ctcap          on fnbl_ds_ctcap           (datastore)";
    $aQuery[] = "create index ind_ctcap_prop     on fnbl_ds_ctcap_prop      (ctcap    )";
    $aQuery[] = "create index ind_ctcappropparam on fnbl_ds_ctcap_prop_param(property )";
    $aQuery[] = "create index ind_filter_rx      on fnbl_ds_filter_rx       (datastore)";
    $aQuery[] = "create index ind_filter_cap     on fnbl_ds_filter_cap      (datastore)";
    $aQuery[] = "create index ind_mem            on fnbl_ds_mem             (datastore)";
    		
    //Initialise the Funambol database
    	
    $aQuery[] = "insert into fnbl_user (username, password, email, first_name, last_name) values('admin', 'lltUbBHM7oA=', 'admin@funambol.com', 'admin', 'admin')";
    $aQuery[] = "insert into fnbl_user (username, password, email, first_name, last_name) values('guest', '65GUmi03K6o=', 'guest@funambol.com', 'guest', 'guest')";
    $aQuery[] = "insert into fnbl_id values('device', 0, 100)";
    $aQuery[] = "insert into fnbl_id values('principal', 0, 100)";
    $aQuery[] = "insert into fnbl_id values('guid', 3, 100)";
    $aQuery[] = "insert into fnbl_id values('datastore', 0, 100)";
    $aQuery[] = "insert into fnbl_id values('capability', 0, 100)";
    $aQuery[] = "insert into fnbl_id values('ext', 0, 100)";
    $aQuery[] = "insert into fnbl_id values('ctcap', 0, 100)";
    $aQuery[] = "insert into fnbl_id values('ctcap_property', 0, 100)";
    $aQuery[] = "insert into fnbl_role values('sync_user','User')";
    $aQuery[] = "insert into fnbl_role values('sync_administrator','Administrator')";
    $aQuery[] = "insert into fnbl_user_role values('admin','sync_administrator')";
    $aQuery[] = "insert into fnbl_user_role values('guest','sync_user')";
    $aQuery[] = "insert into fnbl_connector values('db','FunambolDBConnector','Funambol DB Connector','')";
    $aQuery[] = "insert into fnbl_connector_source_type values('db','ptss-db')";
    $aQuery[] = "insert into fnbl_connector_source_type values('db','tss-db')";
    $aQuery[] = "insert into fnbl_module values('db','db','DB')";
    $aQuery[] = "insert into fnbl_module_connector values('db','db')";
    $aQuery[] = "insert into fnbl_sync_source values('employeesDB','db/db/tss-db/employeesJob.xml','employeesJob','tss-db')";
    $aQuery[] = "insert into fnbl_sync_source_type values('ptss-db','Partitioned Table SyncSource','com.funambol.db.engine.source.PartitionedTableSyncSource','com.funambol.db.admin.PartitionedTableSyncSourceConfigPanel')";
    $aQuery[] = "insert into fnbl_sync_source_type values('tss-db','Table SyncSource','com.funambol.db.engine.source.TableSyncSource','com.funambol.db.admin.TableSyncSourceConfigPanel')";
    
    //Execute Queries
    foreach ($aQuery as $value) {
    	$db->setQuery($value);
    	if (!$db->query()) {
    		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
    	}
    }
       
	//component subscription
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'SyncML','','option=com_easysdi_syncml&task=showConfigList','SyncML','com_easysdi_syncml','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	$query =  "insert into #__components (parent,name,link,admin_menu_link,admin_menu_alt,`option`,admin_menu_img,params)
		values($id,'SyncML Config','','option=com_easysdi_syncml&task=componentConfig','SyncML Config','com_easysdi_syncml','js/ThemeOffice/component.png','')";
	$db->setQuery( $query);

	if (!$db->query()) {
		$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");		
	}
	
	
	$mainframe->enqueueMessage("Congratulation proxy manager for EasySdi is installed and ready to be used. Enjoy EasySdi!","INFO");
	return true;
}


?>