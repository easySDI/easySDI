
CREATE TABLE IF NOT EXISTS #__sdi_category (
    id serial NOT NULL ,
    guid character varying(36) NOT NULL,
    created_by integer NOT NULL,
    created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    modified_by integer,
    modified timestamp(3) without time zone,
    ordering integer NOT NULL,
    state integer DEFAULT 1 NOT NULL,
    checked_out integer DEFAULT 0 NOT NULL,
    checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL,
    alias character varying(150),
    description character varying(500),
    name character varying(255) NOT NULL,
    access integer NOT NULL,
    asset_id integer NOT NULL
);

ALTER TABLE ONLY #__sdi_category
    ADD CONSTRAINT #__sdi_category_pkey PRIMARY KEY (id);

CREATE TABLE IF NOT EXISTS #__sdi_organism_category (
    id serial NOT NULL ,
    organism_id integer NOT NULL,
    category_id integer NOT NULL
);

INSERT INTO #__sdi_sys_accessscope (id, ordering, state, value) VALUES (4, 4, 1, 'category');

CREATE TABLE IF NOT EXISTS #__sdi_policy_category (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    category_id bigint NOT NULL
);



ALTER TABLE `#__sdi_resourcetype` RENAME COLUMN `meta` TO `application`;


DELETE FROM `#__sdi_user_role_organism` WHERE role_id=(SELECT id FROM `#__sdi_sys_role` WHERE `value`='ordereligible');
DELETE FROM `#__sdi_sys_role` WHERE `value`='ordereligible';


ALTER TABLE ONLY #__sdi_order
    DROP CONSTRAINT #__sdi_order_fk4;
ALTER TABLE ONLY #__sdi_order
    ADD CONSTRAINT #__sdi_order_fk4 FOREIGN KEY (thirdparty_id) REFERENCES #__sdi_organism(id) MATCH FULL;


ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_fk1 FOREIGN KEY (user_id) REFERENCES #__sdi_user(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_fk2 FOREIGN KEY (role_id) REFERENCES #__sdi_sys_role(id) MATCH FULL;
ALTER TABLE ONLY #__sdi_user_role_organism
    ADD CONSTRAINT #__sdi_user_role_organism_fk3 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism(id) MATCH FULL;