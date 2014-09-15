INSERT INTO #__sdi_sys_role (ordering, state, value) VALUES (9, 1, 'pricingmanager');
INSERT INTO #__sdi_sys_role (ordering, state, value) VALUES (10, 1, 'validationmanager');

ALTER TABLE #__sdi_category ADD overall_fee decimal(6,2) UNSIGNED DEFAULT 0;

ALTER TABLE #__sdi_organism ADD internal_free smallint DEFAULT 0;
ALTER TABLE #__sdi_organism ADD fixed_fee_ti decimal(6,2) UNSIGNED DEFAULT 0;
ALTER TABLE #__sdi_organism ADD data_free_fixed_fee smallint DEFAULT 0;
ALTER TABLE #__sdi_organism ADD selectable_as_thirdparty smallint(1) DEFAULT 0 AFTER perimeter;

CREATE TABLE IF NOT EXISTS #__sdi_organism_category_pricing_rebate (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    organism_id INT(11) UNSIGNED,
    category_id INT(11) UNSIGNED,
    rebate decimal(6,2),
    PRIMARY KEY (id),
  KEY #__sdi_organism_category_pricing_rebate_fk1 (organism_id),
  KEY #__sdi_organism_category_pricing_rebate_fk2 (category_id),
  CONSTRAINT #__sdi_organism_category_pricing_rebate_fk1 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism (id) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT #__sdi_organism_category_pricing_rebate_fk2 FOREIGN KEY (category_id) REFERENCES #__sdi_category (id) ON DELETE CASCADE ON UPDATE NO ACTION
);

INSERT INTO #__sdi_sys_pricing (ordering, state, value) VALUES (1, 1, 'free');
INSERT INTO #__sdi_sys_pricing (ordering, state, value) VALUES (2, 1, 'fee without a pricing profile');
INSERT INTO #__sdi_sys_pricing (ordering, state, value) VALUES (3, 1, 'fee with a pricing profile');

CREATE TABLE IF NOT EXISTS #__sdi_pricing_profile (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    guid VARCHAR(36)  NOT NULL ,
    alias VARCHAR(50)   ,
    created_by INT(11)  NOT NULL ,
    created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    modified_by INT(11)   ,
    modified DATETIME ,
    ordering INT(11)  ,
    state int(11)  NOT NULL DEFAULT '1',
    checked_out INT(11) NOT NULL DEFAULT '0'  ,
    checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    organism_id int(11) UNSIGNED NOT NULL,
    name varchar(75) NOT NULL,
    fixed_fee decimal(6,2),
    surface_rate decimal(6,2),
    min_fee decimal(6,2),
    max_fee decimal(6,2),
    PRIMARY KEY (id),
    KEY #__sdi_pricing_profile_fk1 (organism_id),
    CONSTRAINT #__sdi_pricing_profile_fk1 FOREIGN KEY (organism_id) REFERENCES #__sdi_organism (id) ON DELETE CASCADE ON UPDATE NO ACTION
);

CREATE TABLE IF NOT EXISTS #__sdi_pricing_profile_category_pricing_rebate (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    pricing_profile_id int(11) UNSIGNED NOT NULL,
    category_id int(11) UNSIGNED NOT NULL,
    rebate decimal(6,2) UNSIGNED DEFAULT 100,
    PRIMARY KEY (id),
    KEY #__sdi_pricing_profile_category_pricing_rebate_fk1 (pricing_profile_id),
    KEY #__sdi_pricing_profile_category_pricing_rebate_fk2 (category_id),
    CONSTRAINT #__sdi_pricing_profile_category_pricing_rebate_fk1 FOREIGN KEY (pricing_profile_id) REFERENCES #__sdi_pricing_profile (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT #__sdi_pricing_profile_category_pricing_rebate_fk2 FOREIGN KEY (category_id) REFERENCES #__sdi_category (id) ON DELETE CASCADE ON UPDATE NO ACTION
);

ALTER TABLE #__sdi_diffusion ADD pricing_profile_id int(11) UNSIGNED AFTER pricing_id;
ALTER TABLE ONLY #__sdi_diffusion
    ADD CONSTRAINT #__sdi_diffusion_fk6 FOREIGN KEY (pricing_profile_id) REFERENCES #__sdi_pricing_profile(id) MATCH FULL ON DELETE SET NULL;



INSERT INTO #__sdi_sys_orderstate (ordering, state, value) VALUES (8, 1, 'validation');
INSERT INTO #__sdi_sys_orderstate (ordering, state, value) VALUES (9, 1, 'rejected by thirdparty');
INSERT INTO #__sdi_sys_orderstate (ordering, state, value) VALUES (10, 1, 'rejected by supplier');

ALTER TABLE #__sdi_order ADD validated smallint DEFAULT NULL AFTER thirdparty_id;
ALTER TABLE #__sdi_order ADD validated_date DATETIME DEFAULT NULL AFTER validated;
ALTER TABLE #__sdi_order ADD validated_reason VARCHAR(500) AFTER validated_date;


CREATE TABLE IF NOT EXISTS #__sdi_pricing_order (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    guid VARCHAR(36)  NOT NULL ,
    created_by INT(11)  NOT NULL ,
    created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    modified_by INT(11)   ,
    modified DATETIME ,
    ordering INT(11)  ,
    state int(11)  NOT NULL DEFAULT '1',
    checked_out INT(11) NOT NULL DEFAULT '0'  ,
    checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    order_id int(11) UNSIGNED NOT NULL,
    cfg_vat decimal(6,2) NOT NULL DEFAULT 0,
    cfg_currency char(3) NOT NULL DEFAULT 'CHF',
    cfg_rounding decimal(3,2) NOT NULL DEFAULT '0.05',
    cfg_overall_default_fee decimal(6,2) NOT NULL DEFAULT 0,
    cfg_free_data_fee TINYINT DEFAULT 0,
    cal_total_amount_ti float,
    cal_fee_ti decimal(6,2) NOT NULL DEFAULT 0,
    ind_lbl_category_order_fee varchar(255),
    PRIMARY KEY (id),
    KEY #__sdi_pricing_order_fk1 (order_id),
    CONSTRAINT #__sdi_pricing_order_fk1 FOREIGN KEY (order_id) REFERENCES #__sdi_order (id) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS #__sdi_pricing_order_supplier (
    id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    guid VARCHAR(36)  NOT NULL ,
    created_by INT(11)  NOT NULL ,
    created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    modified_by INT(11)   ,
    modified DATETIME ,
    ordering INT(11)  ,
    state int(11)  NOT NULL DEFAULT '1',
    checked_out INT(11) NOT NULL DEFAULT '0'  ,
    checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    pricing_order_id int(11) UNSIGNED NOT NULL,
    supplier_id int(11) UNSIGNED NOT NULL,
    supplier_name varchar(255) NOT NULL,
    cfg_internal_free TINYINT NOT NULL DEFAULT 1,
    cfg_fixed_fee_ti decimal(6,2) NOT NULL DEFAULT 0,
    cfg_data_free_fixed_fee TINYINT NOT NULL DEFAULT 0,
    cal_total_rebate_ti float NOT NULL DEFAULT 0,
    cal_fee_ti decimal(6,2) NOT NULL DEFAULT 0,
    cal_total_amount_ti float,
    PRIMARY KEY (id),
    KEY #__sdi_pricing_order_supplier_fk1 (pricing_order_id),
    KEY #__sdi_pricing_order_supplier_fk2 (supplier_id),
    CONSTRAINT #__sdi_pricing_order_supplier_fk1 FOREIGN KEY (pricing_order_id) REFERENCES #__sdi_pricing_order (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT #__sdi_pricing_order_supplier_fk2 FOREIGN KEY (supplier_id) REFERENCES #__sdi_organism (id) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS #__sdi_pricing_order_supplier_product (
    id int(11) unsigned not null auto_increment,
    guid VARCHAR(36)  NOT NULL ,
    created_by INT(11)  NOT NULL ,
    created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    modified_by INT(11)   ,
    modified DATETIME ,
    ordering INT(11)  ,
    state int(11)  NOT NULL DEFAULT '1',
    checked_out INT(11) NOT NULL DEFAULT '0'  ,
    checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    pricing_order_supplier_id int(11) unsigned not null,
    product_id int(11) unsigned not null,
    pricing_id int(11) unsigned not null,
    cfg_pct_category_supplier_discount decimal(6,2) NOT NULL DEFAULT 0,
    ind_lbl_category_supplier_discount varchar(255),
    cal_amount_data_te float,
    cal_total_amount_te float,
    cal_total_amount_ti float,
    cal_total_rebate_ti float,
    PRIMARY KEY (id),
    KEY #__sdi_pricing_order_supplier_product_fk1 (pricing_order_supplier_id),
    KEY #__sdi_pricing_order_supplier_product_fk2 (product_id),
    KEY #__sdi_pricing_order_supplier_product_fk3 (pricing_id),
    CONSTRAINT #__sdi_pricing_order_supplier_product_fk1 FOREIGN KEY (pricing_order_supplier_id) REFERENCES #__sdi_pricing_order_supplier (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT #__sdi_pricing_order_supplier_product_fk2 FOREIGN KEY (product_id) REFERENCES #__sdi_diffusion (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT #__sdi_pricing_order_supplier_product_fk3 FOREIGN KEY (pricing_id) REFERENCES #__sdi_sys_pricing (id) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS #__sdi_pricing_order_supplier_product_profile (
    id int(11) unsigned not null auto_increment,
    guid VARCHAR(36)  NOT NULL ,
    created_by INT(11)  NOT NULL ,
    created DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    modified_by INT(11)   ,
    modified DATETIME ,
    ordering INT(11)  ,
    state int(11)  NOT NULL DEFAULT '1',
    checked_out INT(11) NOT NULL DEFAULT '0'  ,
    checked_out_time DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    pricing_order_supplier_product_id int(11) unsigned not null,
    pricing_profile_id int(11) unsigned not null,
    pricing_profile_name varchar(255) not null,
    cfg_fixed_fee decimal(6,2) NOT NULL DEFAULT 0,
    cfg_surface_rate decimal(6,2) NOT NULL DEFAULT 0,
    cfg_min_fee decimal(6,2) NOT NULL DEFAULT 0,
    cfg_max_fee decimal(6,2) NOT NULL DEFAULT 0,
    cfg_pct_category_profile_discount decimal(6,2) NOT NULL DEFAULT 0,
    ind_lbl_category_profile_discount varchar(255),
    PRIMARY KEY (id),
    KEY #__sdi_pricing_order_supplier_product_profile_fk1 (pricing_order_supplier_product_id),
    KEY #__sdi_pricing_order_supplier_product_profile_fk2 (pricing_profile_id),
    CONSTRAINT #__sdi_pricing_order_supplier_product_profile_fk1 FOREIGN KEY (pricing_order_supplier_product_id) REFERENCES #__sdi_pricing_order_supplier_product (id) ON DELETE CASCADE ON UPDATE NO ACTION,
    CONSTRAINT #__sdi_pricing_order_supplier_product_profile_fk2 FOREIGN KEY (pricing_profile_id) REFERENCES #__sdi_pricing_profile (id) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS #__sdi_sys_extractstorage (
    id int(11) unsigned not null auto_increment,
    ordering int(11),
    state int(11) not null default '1',
    value varchar(255) not null,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO #__sdi_sys_extractstorage (ordering, state, value) VALUES (1, 1, 'local');
INSERT INTO #__sdi_sys_extractstorage (ordering, state, value) VALUES (2, 1, 'remote');


