--
-- Initialization data for Funambol DB Connector
-- @version $Id: init_schema.sql,v 1.1 2006/01/18 18:43:29 luigiafassina Exp $
--

--
-- SyncSourceType registration
--
delete from fnbl_sync_source_type where id='tss-db';
insert into fnbl_sync_source_type(id, description, class, admin_class)
values('tss-db','Table SyncSource','com.funambol.db.engine.source.TableSyncSource', 'com.funambol.db.admin.TableSyncSourceConfigPanel');

delete from fnbl_sync_source_type where id='ptss-db';
insert into fnbl_sync_source_type(id, description, class, admin_class)
values('ptss-db','Partitioned Table SyncSource','com.funambol.db.engine.source.PartitionedTableSyncSource', 'com.funambol.db.admin.PartitionedTableSyncSourceConfigPanel');


--
-- SyncConnector registration
--
delete from fnbl_connector where id='db';
insert into fnbl_connector(id, name, description, admin_class)
values('db','FunambolDBConnector','Funambol DB Connector','');


delete from fnbl_connector_source_type where connector='db';
insert into fnbl_connector_source_type(connector, sourcetype)
values('db','tss-db');
insert into fnbl_connector_source_type(connector, sourcetype)
values('db','ptss-db');

--
-- SyncModule registration
--
delete from fnbl_module where id='db';
insert into fnbl_module (id, name, description)
values('db','db','DB');

delete from fnbl_module_connector where module='db' and connector='db';
insert into fnbl_module_connector(module, connector)
values('db','db');

