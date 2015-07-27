
ALTER TABLE #__sdi_order_diffusion ADD COLUMN guid character varying(36) NOT NULL AFTER id;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN created timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL AFTER created_by;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN modified_by integer AFTER created;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN modified timestamp(3) without time zone AFTER modified_by;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN ordering INT(11)  NOT NULL AFTER modified;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN state int(11)  NOT NULL DEFAULT 1 AFTER ordering;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN checked_out integer DEFAULT 0 NOT NULL AFTER state;
ALTER TABLE #__sdi_order_diffusion ADD COLUMN checked_out_time timestamp(3) without time zone DEFAULT '0002-11-30 00:00:00'::timestamp without time zone NOT NULL AFTER checked_out;