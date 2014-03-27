ALTER TABLE `#__sdi_language` ADD COLUMN `iso639-2B`  varchar(10) NULL AFTER `iso3166-1-alpha2`;

UPDATE `#__sdi_language` SET `iso639-2B` = 'ara' WHERE `id` = 1;
UPDATE `#__sdi_language` SET `iso639-2B` = 'bul' WHERE `id` = 3;
UPDATE `#__sdi_language` SET `iso639-2B` = 'cat' WHERE `id` = 4;
UPDATE `#__sdi_language` SET `iso639-2B` = 'cze' WHERE `id` = 5;
UPDATE `#__sdi_language` SET `iso639-2B` = 'dan' WHERE `id` = 6;
UPDATE `#__sdi_language` SET `iso639-2B` = 'ger' WHERE `id` = 7;
UPDATE `#__sdi_language` SET `iso639-2B` = 'gre' WHERE `id` = 8;
UPDATE `#__sdi_language` SET `iso639-2B` = 'eng' WHERE `id` = 9;
UPDATE `#__sdi_language` SET `iso639-2B` = 'eng' WHERE `id` = 10;
UPDATE `#__sdi_language` SET `iso639-2B` = 'spa' WHERE `id` = 11;
UPDATE `#__sdi_language` SET `iso639-2B` = 'est' WHERE `id` = 12;
UPDATE `#__sdi_language` SET `iso639-2B` = 'baq' WHERE `id` = 13;
UPDATE `#__sdi_language` SET `iso639-2B` = 'fin' WHERE `id` = 14;
UPDATE `#__sdi_language` SET `iso639-2B` = 'fre' WHERE `id` = 15;
UPDATE `#__sdi_language` SET `iso639-2B` = 'gle' WHERE `id` = 16;
UPDATE `#__sdi_language` SET `iso639-2B` = 'hrv' WHERE `id` = 17;
UPDATE `#__sdi_language` SET `iso639-2B` = 'hun' WHERE `id` = 18;
UPDATE `#__sdi_language` SET `iso639-2B` = 'ita' WHERE `id` = 19;
UPDATE `#__sdi_language` SET `iso639-2B` = 'lit' WHERE `id` = 20;
UPDATE `#__sdi_language` SET `iso639-2B` = 'lav' WHERE `id` = 21;
UPDATE `#__sdi_language` SET `iso639-2B` = 'mlt' WHERE `id` = 22;
UPDATE `#__sdi_language` SET `iso639-2B` = 'dut' WHERE `id` = 23;
UPDATE `#__sdi_language` SET `iso639-2B` = 'nor' WHERE `id` = 24;
UPDATE `#__sdi_language` SET `iso639-2B` = 'pol' WHERE `id` = 25;
UPDATE `#__sdi_language` SET `iso639-2B` = 'por' WHERE `id` = 26;
UPDATE `#__sdi_language` SET `iso639-2B` = 'rum' WHERE `id` = 27;
UPDATE `#__sdi_language` SET `iso639-2B` = 'rus' WHERE `id` = 28;
UPDATE `#__sdi_language` SET `iso639-2B` = 'slo' WHERE `id` = 29;
UPDATE `#__sdi_language` SET `iso639-2B` = 'swe' WHERE `id` = 30;
UPDATE `#__sdi_language` SET `iso639-2B` = 'tur' WHERE `id` = 31;
UPDATE `#__sdi_language` SET `iso639-2B` = 'ukr' WHERE `id` = 32;
UPDATE `#__sdi_language` SET `iso639-2B` = 'chi' WHERE `id` = 33;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_isolanguage` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` int(11)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_isolanguage` VALUES ('1','1','1','iso639-2T');
INSERT INTO `#__sdi_sys_isolanguage` VALUES ('2','2','1','iso639-2B');
INSERT INTO `#__sdi_sys_isolanguage` VALUES ('3','3','1','iso639-1');

