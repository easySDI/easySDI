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

ALTER TABLE #__sdi_map ADD COLUMN "type" VARCHAR(10) DEFAULT 'geoext';