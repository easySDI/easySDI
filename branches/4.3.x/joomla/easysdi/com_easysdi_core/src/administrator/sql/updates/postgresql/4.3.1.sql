

ALTER TABLE #__sdi_order_diffusion ADD storage_id bigint NOT NULL AFTER completed;

ALTER TABLE ONLY #__sdi_order_diffusion
    ADD CONSTRAINT #__sdi_order_diffusion_fk4 FOREIGN KEY (storage_id) REFERENCES #__sdi_sys_extractstorage(id) MATCH FULL;