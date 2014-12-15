ALTER TABLE ONLY #__sdi_translation 
    ADD COLUMN text3  character varying(255);

ALTER TABLE ONLY #__sdi_resourcetypelink 
    DROP CONSTRAINT #__sdi_resourcetypelink_fk3;

ALTER TABLE ONLY #__sdi_resourcetypelink 
    DROP COLUMN class_id;

ALTER TABLE ONLY #__sdi_resourcetypelink 
    DROP CONSTRAINT #__sdi_resourcetypelink_fk4;

ALTER TABLE ONLY #__sdi_resourcetypelink 
    DROP COLUMN attribute_id;

ALTER TABLE ONLY #__sdi_assignment 
    DROP FOREIGN KEY #__sdi_assignment_fk3;

TRUNCATE TABLE #__sdi_assignment;

ALTER TABLE ONLY #__sdi_assignment
    ADD CONSTRAINT #__sdi_assignment_fk3 FOREIGN KEY (metadata_id) REFERENCES #__sdi_metadata(id) MATCH FULL ON DELETE CASCADE;

INSERT INTO #__sdi_sys_rendertype VALUES 
(7, 7, 1, 'datetime'),
(8, 8, 1, 'gemet');

INSERT INTO #__sdi_sys_rendertype_stereotype VALUES ('21', '11', '8');
