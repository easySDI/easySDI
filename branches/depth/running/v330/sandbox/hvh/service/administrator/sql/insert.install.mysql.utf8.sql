INSERT INTO `#__sdi_sys_authenticationlevel` (ordering,state,value) 
VALUES 
(1,1,'resource'),
(2,1,'service')
;

INSERT INTO `#__sdi_sys_authenticationconnector` (ordering,state,authenticationlevel_id,value) 
VALUES 
(1,1,1,'HTTPBasic'),
(2,1,2,'Geonetwork')
;

INSERT INTO `#__sdi_sys_serviceversion` (ordering,state,value) 
VALUES 
(1,1,'1.0.0'),
(2,1,'1.1.0'),
(3,1,'1.1.1'),
(4,1,'1.3.0'),
(5,1,'2.0'),
(6,1,'2.0.0'),
(7,1,'2.0.1'),
(8,1,'2.0.2')
;

INSERT INTO `#__sdi_sys_serviceconnector` (ordering,state,value) 
VALUES 
(1,1,'CSW'),
(2,1,'WMS'),
(3,1,'WMTS'),
(4,1,'WFS'),
(5,0,'WCS'),
(6,0,'WCPS'),
(7,0,'SOS'),
(8,0,'SPS'),
(9,0,'WPS'),
(10,0,'OLS')
;

INSERT INTO `#__sdi_sys_servicecon_authenticationcon` (serviceconnector_id,authenticationconnector_id) 
VALUES 
(1,1),
(1,2),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1),
(10,1)
;

INSERT INTO `#__sdi_sys_servicecompliance` (ordering,state,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(1,1,1,7,1,1,0,1),
(2,1,1,8,1,1,0,1),
(3,1,2,2,1,1,1,0),
(4,1,2,3,1,1,1,0),
(5,1,2,4,1,1,1,0),
(6,1,3,1,1,1,0,0),
(7,1,4,1,1,1,1,0)
;

INSERT INTO `#__sdi_sys_serviceoperation` (ordering,state,value) 
VALUES 
(1,1,'GetCapabilities'),
(2,1,'GetRecords'),
(3,1,'GetRecordById'),
(4,1,'DescribeRecord'),
(5,1,'TransactionInsert'),
(6,1,'TransactionUpdate'),
(7,1,'TransactionReplace'),
(8,1,'TransactionDelete'),
(9,1,'GetDomain'),
(10,1,'Harvest'),
(11,1,'GetTile'),
(12,1,'GetFeatureInfo'),
(13,1,'DescribeFeatureType'),
(14,1,'GetFeature'),
(15,1,'LockFeature'),
(16,1,'GetFeatureWithLock'),
(17,1,'GetMap'),
(18,1,'GetLegendGraphic'),
(19,1,'DescribeLayer'),
(20,1,'GetStyles'),
(21,1,'PutStyles')
;

INSERT INTO `#__sdi_sys_operationcompliance` (ordering,state,servicecompliance_id,serviceoperation_id,implemented) 
VALUES 
(1,1,1,1,1)
;

INSERT INTO `#__sdi_sys_serviceconnector` (ordering,state,value) 
VALUES 
(11,1,'WMSC'),
(12,0,'Bing'),
(13,0,'Google'),
(14,0,'OSM')
;

INSERT INTO `#__sdi_sys_servicecompliance` (ordering,state,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(8,1,11,2,1,1,1,0),
(9,1,11,3,1,1,1,0),
(10,1,11,4,1,1,1,0)
;

INSERT INTO `#__sdi_sys_exceptionlevel` (ordering, state, value )
VALUES
(1,1,'permissive'),
(2,1,'restrictive')
;

INSERT INTO `#__sdi_sys_proxytype` (ordering, state, value )
VALUES
(1,1,'harvest'),
(2,1,'relay'),
(3,1,'aggregate');

INSERT INTO `#__sdi_sys_loglevel` (ordering, state, value )
VALUES
(1,1,'off'),
(2,1,'fatal'),
(3,1,'error'),
(4,1,'warning'),
(5,1,'info'),
(6,1,'debug'),
(7,1,'trace'),
(8,1,'All')
;

INSERT INTO `#__sdi_sys_logroll` (ordering, state, value )
VALUES
(1,1,'daily'),
(2,1,'weekly'),
(3,1,'monthly'),
(4,1,'annually')
;

