INSERT INTO #__sdi_sys_rendertype (ordering, state, value) VALUES (9, 1, 'upload');
INSERT INTO #__sdi_sys_rendertype (ordering, state, value) VALUES (10, 1, 'url');
INSERT INTO #__sdi_sys_rendertype (ordering, state, value) VALUES (11, 1, 'upload and url');

INSERT INTO #__sdi_sys_rendertype_stereotype (stereotype_id, rendertype_id) VALUES (14, 9);
INSERT INTO #__sdi_sys_rendertype_stereotype (stereotype_id, rendertype_id) VALUES (14, 10);
INSERT INTO #__sdi_sys_rendertype_stereotype (stereotype_id, rendertype_id) VALUES (14, 11);

DELETE FROM #__sdi_sys_rendertype_stereotype WHERE id=20;

ALTER TABLE #__sdi_visualization MODIFY alias VARCHAR(50) NOT NULL;

INSERT INTO #__sdi_sys_role (id, ordering, state, value) VALUES (11, 11, 1, 'organismmanager');

ALTER TABLE #__sdi_order ADD COLUMN "freeperimetertool" VARCHAR(100) NULL;

ALTER TABLE #__sdi_order_diffusion MODIFY completed datetime NULL;

ALTER TABLE #__sdi_pricing_profile
MODIFY fixed_fee  decimal(19,2) NULL,
MODIFY surface_rate  decimal(19,2) NULL,
MODIFY min_fee  decimal(19,2) NULL,
MODIFY max_fee  decimal(19,2) NULL;

ALTER TABLE #__sdi_pricing_order_supplier_product_profile
MODIFY cfg_fixed_fee  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cfg_surface_rate  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cfg_min_fee  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cfg_max_fee  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cfg_pct_category_profile_discount  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE #__sdi_pricing_order_supplier_product
MODIFY cfg_pct_category_supplier_discount  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE #__sdi_pricing_order_supplier
MODIFY cfg_fixed_fee_ti  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cal_fee_ti  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE #__sdi_pricing_order
MODIFY cfg_vat  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cfg_overall_default_fee  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cal_fee_ti  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE #__sdi_organism_category_pricing_rebate
MODIFY rebate  decimal(19,2) NULL;

ALTER TABLE #__sdi_diffusion ADD packageurl character varying(500);
UPDATE #__sdi_diffusion SET packageurl='{CODE}';

CREATE INDEX text1 ON #__sdi_translation USING btree (text1);
CREATE INDEX text2 ON #__sdi_translation USING btree (text2);

INSERT INTO #__sdi_sys_productstate (id, ordering, state, value) VALUES (7, 7,1, 'deleted');

INSERT INTO #__sdi_sys_rendertype_criteriatype (criteriatype_id, rendertype_id) VALUES (3, 2);

DELETE FROM #__sdi_sys_metadatastate WHERE id=5;

UPDATE #__sdi_sys_orderstate SET value = 'rejectedbythirdparty' WHERE id = 9;
UPDATE #__sdi_sys_orderstate SET value = 'rejectedbysupplier' WHERE id = 10;

UPDATE #__sdi_sys_productstate SET value = 'rejectedbythirdparty' WHERE id = 5;
UPDATE #__sdi_sys_productstate SET value = 'rejectedbysupplier' WHERE id = 6;

ALTER TABLE #__sdi_order MODIFY remark VARCHAR(4000);

UPDATE #__sdi_sys_pricing SET value = 'feewithoutapricingprofile' WHERE id = 2;
UPDATE #__sdi_sys_pricing SET value = 'feewithapricingprofile' WHERE id = 3;

ALTER TABLE #__sdi_pricing_order_supplier_product
MODIFY cal_amount_data_te   decimal(19,2) NULL,
MODIFY cal_total_amount_te  decimal(19,2) NULL,
MODIFY cal_total_amount_ti  decimal(19,2) NULL,
MODIFY cal_total_rebate_ti  decimal(19,2) NOT NULL DEFAULT 0.00;

ALTER TABLE #__sdi_pricing_order_supplier
MODIFY cal_total_rebate_ti  decimal(19,2) NOT NULL DEFAULT 0.00,
MODIFY cal_total_amount_ti  decimal(19,2) NULL;

ALTER TABLE #__sdi_pricing_order
MODIFY cal_total_amount_ti  decimal(19,2) NULL 0.00;

UPDATE #__sdi_sys_orderstate SET ordering = 1  WHERE id = 7;
UPDATE #__sdi_sys_orderstate SET ordering = 2  WHERE id = 8;
UPDATE #__sdi_sys_orderstate SET ordering = 3  WHERE id = 6;
UPDATE #__sdi_sys_orderstate SET ordering = 4  WHERE id = 4;
UPDATE #__sdi_sys_orderstate SET ordering = 5  WHERE id = 5;
UPDATE #__sdi_sys_orderstate SET ordering = 6  WHERE id = 3;
UPDATE #__sdi_sys_orderstate SET ordering = 7  WHERE id = 1;
UPDATE #__sdi_sys_orderstate SET ordering = 8  WHERE id = 2;
UPDATE #__sdi_sys_orderstate SET ordering = 9  WHERE id = 9;
UPDATE #__sdi_sys_orderstate SET ordering = 10 WHERE id = 10;

ALTER TABLE #__sdi_order
ADD COLUMN validated_by INT(11) UNSIGNED NULL DEFAULT NULL AFTER validated_reason,
ADD CONSTRAINT #__sdi_order_fk5 FOREIGN KEY ("validated_by") REFERENCES #__sdi_user ("id") MATCH FULL ON UPDATE NO ACTION ON DELETE NO ACTION;