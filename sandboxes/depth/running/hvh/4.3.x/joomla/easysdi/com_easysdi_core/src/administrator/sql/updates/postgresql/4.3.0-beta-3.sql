ALTER TABLE #__sdi_maplayer ADD COLUMN levelfield  character varying(255);
ALTER TABLE #__sdi_maplayer ADD COLUMN isindoor  integer;

CREATE TABLE IF NOT EXISTS #__sdi_sys_server (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    ordering integer DEFAULT 1 NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    value character varying(150) NOT NULL
    PRIMARY KEY (id)  
);

INSERT INTO #__sdi_sys_server (ordering, state, value) VALUES (1, 1, 'geoserver');
INSERT INTO #__sdi_sys_server (ordering, state, value) VALUES (2, 1, 'arcgisserver');

CREATE TABLE IF NOT EXISTS #__sdi_sys_server_serviceconnector (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    server_id INT(11) UNSIGNED,
    serviceconnector_id INT(11) UNSIGNED,
    PRIMARY KEY (id),
  KEY #__sdi_sys_server_serviceconnector_fk1 (server_id),
  KEY #__sdi_sys_server_serviceconnector_fk2 (serviceconnector_id),
  CONSTRAINT #__sdi_sys_server_serviceconnector_fk1 FOREIGN KEY (server_id) REFERENCES #__sdi_sys_server (id) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT #__sdi_sys_server_serviceconnector_fk2 FOREIGN KEY (serviceconnector_id) REFERENCES #__sdi_sys_serviceconnector (id) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('1', '1', '2');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('2', '1', '3');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('3', '1', '4');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('4', '1', '5');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('5', '1', '11');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('6', '2', '2');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('7', '2', '4');
INSERT INTO #__sdi_sys_server_serviceconnector (id, server_id, service_connector) VALUES ('8', '2', '5');

ALTER TABLE #__sdi_physicalservice ADD COLUMN server_id INT(11) UNSIGNED;

ALTER TABLE ONLY #__sdi_physicalservice
    ADD CONSTRAINT #__sdi_physicalservice_server_fk1 FOREIGN KEY (server_id) REFERENCES #__sdi_sys_server(id) MATCH FULL;