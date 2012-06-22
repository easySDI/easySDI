
INSERT INTO `#__sdi_sys_authenticationlevel` (ordering,state,checked_out,value) 
VALUES 
(1,1,0,'resource'),
(2,1,0,'service')
;

INSERT INTO `#__sdi_sys_authenticationconnector` (ordering,state,checked_out,authenticationlevel_id,value) 
VALUES 
(1,1,0,1,'HTTPBasic'),
(2,1,0,2,'Geonetwork')
;

INSERT INTO `#__sdi_sys_serviceversion` (ordering,state,checked_out,value) 
VALUES 
(1,1,0,'1.0.0'),
(2,1,0,'1.1.0'),
(3,1,0,'1.1.1'),
(4,1,0,'1.3.0'),
(5,1,0,'2.0'),
(6,1,0,'2.0.0'),
(7,1,0,'2.0.1'),
(8,1,0,'2.0.2')
;

INSERT INTO `#__sdi_sys_serviceconnector` (ordering,state,checked_out,value) 
VALUES 
(1,1,0,'CSW'),
(2,1,0,'WMS'),
(3,1,0,'WMTS'),
(4,1,0,'WFS'),
(5,1,0,'WCS'),
(6,1,0,'WCPS'),
(7,1,0,'SOS'),
(8,1,0,'SPS'),
(9,1,0,'WPS'),
(10,1,0,'OLS')
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

INSERT INTO `#__sdi_sys_servicecompliance` (ordering,state,checked_out,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(1,1,0,1,7,1,1,0,1),
(2,1,0,1,8,1,1,0,1),
(3,1,0,2,2,1,1,1,0),
(4,1,0,2,3,1,1,1,0),
(5,1,0,2,4,1,1,1,0),
(6,1,0,3,1,1,1,0,0),
(7,1,0,4,1,1,1,1,0)
;

INSERT INTO `#__sdi_sys_serviceoperation` (ordering,state,checked_out,value) 
VALUES 
(1,1,0,'GetCapabilities'),
(2,1,0,'GetRecords'),
(3,1,0,'GetRecordById'),
(4,1,0,'DescribeRecord'),
(5,1,0,'TransactionInsert'),
(6,1,0,'TransactionUpdate'),
(7,1,0,'TransactionReplace'),
(8,1,0,'TransactionDelete'),
(9,1,0,'GetDomain'),
(10,1,0,'Harvest'),
(11,1,0,'GetTile'),
(12,1,0,'GetFeatureInfo'),
(13,1,0,'DescribeFeatureType'),
(14,1,0,'GetFeature'),
(15,1,0,'LockFeature'),
(16,1,0,'GetFeatureWithLock'),
(17,1,0,'GetMap'),
(18,1,0,'GetLegendGraphic'),
(19,1,0,'DescribeLayer'),
(20,1,0,'GetStyles'),
(21,1,0,'PutStyles')
;

INSERT INTO `#__sdi_sys_operationcompliance` (ordering,state,checked_out,servicecompliance_id,serviceoperation_id,implemented) 
VALUES 
(1,1,0,1,1,1)
;
