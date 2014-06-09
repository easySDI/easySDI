
CREATE TABLE IF NOT EXISTS jos_sdi_category (
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

CREATE TABLE IF NOT EXISTS jos_sdi_organism_category (
    id serial NOT NULL ,
    organism_id integer NOT NULL references jos_sdi_organism(id),
    category_id integer NOT NULL references jos_sdi_category(id)
);

INSERT INTO jos_sdi_sys_accessscope (id, ordering, state, `value`) VALUES (4, 4, 1, 'category');

CREATE TABLE IF NOT EXISTS jos_sdi_policy_category (
    id serial NOT NULL ,
    policy_id bigint NOT NULL,
    category_id bigint NOT NULL
);



ALTER TABLE `jos_sdi_resourcetype` RENAME COLUMN `meta` TO `application`;


DELETE FROM `jos_sdi_user_role_organism` WHERE role_id=(SELECT id FROM `jos_sdi_sys_role` WHERE `value`='ordereligible');
DELETE FROM `jos_sdi_sys_role` WHERE `value`='ordereligible';


ALTER TABLE ONLY jos_sdi_order
    DROP CONSTRAINT jos_sdi_order_fk4;
ALTER TABLE ONLY jos_sdi_order
    ADD CONSTRAINT jos_sdi_order_fk4 FOREIGN KEY (thirdparty_id) REFERENCES jos_sdi_organism(id) MATCH FULL;