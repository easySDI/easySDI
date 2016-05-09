
ALTER TABLE #__sdi_accessscope ADD category_id bigint;
ALTER TABLE ONLY #__sdi_accessscope
    ADD CONSTRAINT #__sdi_accessscope_fk3 FOREIGN KEY (category_id) REFERENCES #__sdi_category(id) MATCH FULL ON DELETE CASCADE;