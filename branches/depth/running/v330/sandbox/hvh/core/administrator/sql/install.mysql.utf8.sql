CREATE TABLE IF NOT EXISTS `#__sdi_sys_unit` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`alias` VARCHAR(20)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`),
INDEX `alias` USING BTREE (`alias`) 
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_unit` (ordering,state,checked_out,created_by,created,alias,name) 
VALUES 
(1,1,0,62,NOW(),'m','meter'),
(2,1,0,62,NOW(),'dd','degree')
;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_role` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`created_by` INT(11)  NOT NULL ,
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_country` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`guid` VARCHAR (36) NOT NULL ,
`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`created_by` INT(11)  NOT NULL ,
`created` DATETIME NOT NULL ,
`code` VARCHAR(150)  NOT NULL ,
`name` VARCHAR(250)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

CREATE TABLE `#__sdi_sys_country` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `guid` varchar(36) NOT NULL,
  `ordering` bigint(20) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_country` VALUES ('1', '643001fc-a3fb-11e0-9e98-00163e3098aa', '1', '1', '2011-07-01 18:01:33', '1', 'AFGHANISTAN', 'AF');
INSERT INTO `#__sdi_sys_country` VALUES ('2', '6430153e-a3fb-11e0-9e98-00163e3098aa', '2', '1', '2011-07-01 18:01:33', '1', 'SOUTH AFRICA', 'ZA');
INSERT INTO `#__sdi_sys_country` VALUES ('3', 'ed15733e-0c26-11e0-81e4-001f29c92132', '3', '1', '2010-12-20 11:50:14', '1', 'ALBANIA', 'AL');
INSERT INTO `#__sdi_sys_country` VALUES ('4', '64302f6a-a3fb-11e0-9e98-00163e3098aa', '4', '1', '2011-07-01 18:01:33', '1', 'Algérie', 'DZ');
INSERT INTO `#__sdi_sys_country` VALUES ('5', '667798a0-0c28-11e0-81e4-001f29c92132', '5', '1', '2010-12-20 12:00:47', '1', 'GERMANY', 'DE');
INSERT INTO `#__sdi_sys_country` VALUES ('6', '64304554-a3fb-11e0-9e98-00163e3098aa', '6', '1', '2011-07-01 18:01:33', '1', 'ANDORRA', 'AD');
INSERT INTO `#__sdi_sys_country` VALUES ('7', '64305030-a3fb-11e0-9e98-00163e3098aa', '7', '1', '2011-07-01 18:01:33', '1', 'ANGOLA', 'AO');
INSERT INTO `#__sdi_sys_country` VALUES ('8', '64305b02-a3fb-11e0-9e98-00163e3098aa', '8', '1', '2011-07-01 18:01:33', '1', 'ANGUILLA', 'AI');
INSERT INTO `#__sdi_sys_country` VALUES ('9', '643065de-a3fb-11e0-9e98-00163e3098aa', '9', '1', '2011-07-01 18:01:33', '1', 'ANTARCTICA', 'AQ');
INSERT INTO `#__sdi_sys_country` VALUES ('10', '6430718c-a3fb-11e0-9e98-00163e3098aa', '10', '1', '2011-07-01 18:01:33', '1', 'ANTIGUA AND BARBUDA', 'AG');
INSERT INTO `#__sdi_sys_country` VALUES ('11', '64307d30-a3fb-11e0-9e98-00163e3098aa', '11', '1', '2011-07-01 18:01:33', '1', 'Antilles néerl.', 'AN');
INSERT INTO `#__sdi_sys_country` VALUES ('12', '6430880c-a3fb-11e0-9e98-00163e3098aa', '12', '1', '2011-07-01 18:01:33', '1', 'apatride', 'STL');
INSERT INTO `#__sdi_sys_country` VALUES ('13', '64309306-a3fb-11e0-9e98-00163e3098aa', '13', '1', '2011-07-01 18:01:33', '1', 'SAUDI ARABIA', 'SA');
INSERT INTO `#__sdi_sys_country` VALUES ('14', '64309df6-a3fb-11e0-9e98-00163e3098aa', '14', '1', '2011-07-01 18:01:33', '1', 'ARGENTINA', 'AR');
INSERT INTO `#__sdi_sys_country` VALUES ('15', '6430ab34-a3fb-11e0-9e98-00163e3098aa', '15', '1', '2011-07-01 18:01:33', '1', 'ARMENIA', 'AM');
INSERT INTO `#__sdi_sys_country` VALUES ('16', '6430b610-a3fb-11e0-9e98-00163e3098aa', '16', '1', '2011-07-01 18:01:33', '1', 'ARUBA', 'AW');
INSERT INTO `#__sdi_sys_country` VALUES ('17', '6430c114-a3fb-11e0-9e98-00163e3098aa', '17', '1', '2011-07-01 18:01:33', '1', 'AUSTRALIA', 'AU');
INSERT INTO `#__sdi_sys_country` VALUES ('18', '6430cc04-a3fb-11e0-9e98-00163e3098aa', '18', '1', '2011-07-01 18:01:33', '1', 'AUSTRIA', 'AT');
INSERT INTO `#__sdi_sys_country` VALUES ('19', '6430d6fe-a3fb-11e0-9e98-00163e3098aa', '19', '1', '2011-07-01 18:01:33', '1', 'AZERBAIJAN', 'AZ');
INSERT INTO `#__sdi_sys_country` VALUES ('20', '6430e72a-a3fb-11e0-9e98-00163e3098aa', '20', '1', '2011-07-01 18:01:33', '1', 'BAHAMAS', 'BS');
INSERT INTO `#__sdi_sys_country` VALUES ('21', '6430f21a-a3fb-11e0-9e98-00163e3098aa', '21', '1', '2011-07-01 18:01:33', '1', 'BAHRAIN', 'BH');
INSERT INTO `#__sdi_sys_country` VALUES ('22', '6430fd0a-a3fb-11e0-9e98-00163e3098aa', '22', '1', '2011-07-01 18:01:33', '1', 'BANGLADESH', 'BD');
INSERT INTO `#__sdi_sys_country` VALUES ('23', '64310886-a3fb-11e0-9e98-00163e3098aa', '23', '1', '2011-07-01 18:01:33', '1', 'BARBADOS', 'BB');
INSERT INTO `#__sdi_sys_country` VALUES ('24', '64311394-a3fb-11e0-9e98-00163e3098aa', '24', '1', '2011-07-01 18:01:33', '1', 'BELGIUM', 'BE');
INSERT INTO `#__sdi_sys_country` VALUES ('25', '64311e7a-a3fb-11e0-9e98-00163e3098aa', '25', '1', '2011-07-01 18:01:33', '1', 'BELIZE', 'BZ');
INSERT INTO `#__sdi_sys_country` VALUES ('26', '64312974-a3fb-11e0-9e98-00163e3098aa', '26', '1', '2011-07-01 18:01:33', '1', 'BENIN', 'BJ');
INSERT INTO `#__sdi_sys_country` VALUES ('27', '64313450-a3fb-11e0-9e98-00163e3098aa', '27', '1', '2011-07-01 18:01:33', '1', 'BERMUDA', 'BM');
INSERT INTO `#__sdi_sys_country` VALUES ('28', '64314184-a3fb-11e0-9e98-00163e3098aa', '28', '1', '2011-07-01 18:01:33', '1', 'BHUTAN', 'BT');
INSERT INTO `#__sdi_sys_country` VALUES ('29', '64314c74-a3fb-11e0-9e98-00163e3098aa', '29', '1', '2011-07-01 18:01:33', '1', 'BELARUS', 'BY');
INSERT INTO `#__sdi_sys_country` VALUES ('30', '643157a0-a3fb-11e0-9e98-00163e3098aa', '30', '1', '2011-07-01 18:01:33', '1', 'BOLIVIA, PLURINATIONAL STATE OF', 'BO');
INSERT INTO `#__sdi_sys_country` VALUES ('31', '18b338f4-0c28-11e0-81e4-001f29c92132', '31', '1', '2010-12-20 11:58:37', '1', 'BOSNIA AND HERZEGOVINA', 'BA');
INSERT INTO `#__sdi_sys_country` VALUES ('32', '64316d58-a3fb-11e0-9e98-00163e3098aa', '32', '1', '2011-07-01 18:01:33', '1', 'BOTSWANA', 'BW');
INSERT INTO `#__sdi_sys_country` VALUES ('33', '47392602-0c28-11e0-81e4-001f29c92132', '33', '1', '2010-12-20 11:59:55', '1', 'BRAZIL', 'BR');
INSERT INTO `#__sdi_sys_country` VALUES ('34', '6431834c-a3fb-11e0-9e98-00163e3098aa', '34', '1', '2011-07-01 18:01:33', '1', 'BRUNEI DARUSSALAM', 'BN');
INSERT INTO `#__sdi_sys_country` VALUES ('35', '64318e3c-a3fb-11e0-9e98-00163e3098aa', '35', '1', '2011-07-01 18:01:33', '1', 'BULGARIA', 'BG');
INSERT INTO `#__sdi_sys_country` VALUES ('36', '6431995e-a3fb-11e0-9e98-00163e3098aa', '36', '1', '2011-07-01 18:01:33', '1', 'BURKINA FASO', 'BF');
INSERT INTO `#__sdi_sys_country` VALUES ('37', '6431a49e-a3fb-11e0-9e98-00163e3098aa', '37', '1', '2011-07-01 18:01:33', '1', 'BURUNDI', 'BI');
INSERT INTO `#__sdi_sys_country` VALUES ('38', '6431afac-a3fb-11e0-9e98-00163e3098aa', '38', '1', '2011-07-01 18:01:33', '1', 'CAMBODIA', 'KH');
INSERT INTO `#__sdi_sys_country` VALUES ('39', '6431baa6-a3fb-11e0-9e98-00163e3098aa', '39', '1', '2011-07-01 18:01:33', '1', 'CAMEROON', 'CM');
INSERT INTO `#__sdi_sys_country` VALUES ('40', '4fd34644-0c28-11e0-81e4-001f29c92132', '40', '1', '2010-12-20 12:00:09', '1', 'CANADA', 'CA');
INSERT INTO `#__sdi_sys_country` VALUES ('41', '6431da5e-a3fb-11e0-9e98-00163e3098aa', '41', '1', '2011-07-01 18:01:33', '1', 'CAPE VERDE', 'CV');
INSERT INTO `#__sdi_sys_country` VALUES ('42', '6431e56c-a3fb-11e0-9e98-00163e3098aa', '42', '1', '2011-07-01 18:01:33', '1', 'CHILE', 'CL');
INSERT INTO `#__sdi_sys_country` VALUES ('43', '6431f034-a3fb-11e0-9e98-00163e3098aa', '43', '1', '2011-07-01 18:01:33', '1', 'CHILE', 'CN');
INSERT INTO `#__sdi_sys_country` VALUES ('44', '6431fb2e-a3fb-11e0-9e98-00163e3098aa', '44', '1', '2011-07-01 18:01:33', '1', 'CHRISTMAS ISLAND', 'CX');
INSERT INTO `#__sdi_sys_country` VALUES ('45', '64320aa6-a3fb-11e0-9e98-00163e3098aa', '45', '1', '2011-07-01 18:01:33', '1', 'CYPRUS', 'CY');
INSERT INTO `#__sdi_sys_country` VALUES ('46', '6432158c-a3fb-11e0-9e98-00163e3098aa', '46', '1', '2011-07-01 18:01:33', '1', 'COLOMBIA', 'CO');
INSERT INTO `#__sdi_sys_country` VALUES ('47', '6432207c-a3fb-11e0-9e98-00163e3098aa', '47', '1', '2011-07-01 18:01:33', '1', 'COMOROS', 'KM');
INSERT INTO `#__sdi_sys_country` VALUES ('48', '64322b4e-a3fb-11e0-9e98-00163e3098aa', '48', '1', '2011-07-01 18:01:33', '1', 'CONGO', 'CG');
INSERT INTO `#__sdi_sys_country` VALUES ('49', '64323634-a3fb-11e0-9e98-00163e3098aa', '49', '1', '2011-07-01 18:01:33', '1', 'Corée du Nord', 'KP');
INSERT INTO `#__sdi_sys_country` VALUES ('50', '643241ec-a3fb-11e0-9e98-00163e3098aa', '50', '1', '2011-07-01 18:01:33', '1', 'Corée du Sud', 'KR');
INSERT INTO `#__sdi_sys_country` VALUES ('51', '64324d18-a3fb-11e0-9e98-00163e3098aa', '51', '1', '2011-07-01 18:01:33', '1', 'COSTA RICA', 'CR');
INSERT INTO `#__sdi_sys_country` VALUES ('52', '64325812-a3fb-11e0-9e98-00163e3098aa', '52', '1', '2011-07-01 18:01:33', '1', 'CÔTE D\'IVOIRE', 'CI');
INSERT INTO `#__sdi_sys_country` VALUES ('53', '643262f8-a3fb-11e0-9e98-00163e3098aa', '53', '1', '2011-07-01 18:01:33', '1', 'CROATIA', 'HR');
INSERT INTO `#__sdi_sys_country` VALUES ('54', '64326fe6-a3fb-11e0-9e98-00163e3098aa', '54', '1', '2011-07-01 18:01:33', '1', 'CUBA', 'CU');
INSERT INTO `#__sdi_sys_country` VALUES ('55', '64327a9a-a3fb-11e0-9e98-00163e3098aa', '55', '1', '2011-07-01 18:01:33', '1', 'DENMARK', 'DK');
INSERT INTO `#__sdi_sys_country` VALUES ('56', '64328530-a3fb-11e0-9e98-00163e3098aa', '56', '1', '2011-07-01 18:01:33', '1', 'DJIBOUTI', 'DJ');
INSERT INTO `#__sdi_sys_country` VALUES ('57', '64328fc6-a3fb-11e0-9e98-00163e3098aa', '57', '1', '2011-07-01 18:01:33', '1', 'EGYPT', 'EG');
INSERT INTO `#__sdi_sys_country` VALUES ('58', '64329a66-a3fb-11e0-9e98-00163e3098aa', '58', '1', '2011-07-01 18:01:33', '1', 'EH', 'EH');
INSERT INTO `#__sdi_sys_country` VALUES ('59', '6432a4fc-a3fb-11e0-9e98-00163e3098aa', '59', '1', '2011-07-01 18:01:33', '1', 'EL SALVADOR', 'SV');
INSERT INTO `#__sdi_sys_country` VALUES ('60', '6432af9c-a3fb-11e0-9e98-00163e3098aa', '60', '1', '2011-07-01 18:01:33', '1', 'Emir.arab.unis', 'AE');
INSERT INTO `#__sdi_sys_country` VALUES ('61', '6432ba28-a3fb-11e0-9e98-00163e3098aa', '61', '1', '2011-07-01 18:01:33', '1', 'ECUADOR', 'EC');
INSERT INTO `#__sdi_sys_country` VALUES ('62', '6432cf7c-a3fb-11e0-9e98-00163e3098aa', '62', '1', '2011-07-01 18:01:33', '1', 'ERITREA', 'ER');
INSERT INTO `#__sdi_sys_country` VALUES ('63', '6432daa8-a3fb-11e0-9e98-00163e3098aa', '63', '1', '2011-07-01 18:01:33', '1', 'Espagne', 'ES');
INSERT INTO `#__sdi_sys_country` VALUES ('64', '6432e570-a3fb-11e0-9e98-00163e3098aa', '64', '1', '2011-07-01 18:01:33', '1', 'ESTONIA', 'EE');
INSERT INTO `#__sdi_sys_country` VALUES ('65', '6432ef98-a3fb-11e0-9e98-00163e3098aa', '65', '1', '2011-07-01 18:01:33', '1', 'ETHIOPIA', 'ET');
INSERT INTO `#__sdi_sys_country` VALUES ('66', '6432ffd8-a3fb-11e0-9e98-00163e3098aa', '66', '1', '2011-07-01 18:01:33', '1', 'Féd. russe', 'RU');
INSERT INTO `#__sdi_sys_country` VALUES ('67', '64330a78-a3fb-11e0-9e98-00163e3098aa', '67', '1', '2011-07-01 18:01:33', '1', 'FAROE ISLANDS', 'FO');
INSERT INTO `#__sdi_sys_country` VALUES ('68', '64331518-a3fb-11e0-9e98-00163e3098aa', '68', '1', '2011-07-01 18:01:33', '1', 'FIJI', 'FJ');
INSERT INTO `#__sdi_sys_country` VALUES ('69', '64331fae-a3fb-11e0-9e98-00163e3098aa', '69', '1', '2011-07-01 18:01:33', '1', 'FINLAND', 'FI');
INSERT INTO `#__sdi_sys_country` VALUES ('70', '42c10f6a-3652-11df-8ada-001f29c92132', '70', '1', '2010-01-01 00:00:00', '1', 'FRANCE', 'FR');
INSERT INTO `#__sdi_sys_country` VALUES ('71', '643334e4-a3fb-11e0-9e98-00163e3098aa', '71', '1', '2011-07-01 18:01:33', '1', 'GABON', 'GA');
INSERT INTO `#__sdi_sys_country` VALUES ('72', '64333f70-a3fb-11e0-9e98-00163e3098aa', '72', '1', '2011-07-01 18:01:33', '1', 'GAMBIA', 'GM');
INSERT INTO `#__sdi_sys_country` VALUES ('73', '643349fc-a3fb-11e0-9e98-00163e3098aa', '73', '1', '2011-07-01 18:01:33', '1', 'GEORGIA', 'GE');
INSERT INTO `#__sdi_sys_country` VALUES ('74', '643354a6-a3fb-11e0-9e98-00163e3098aa', '74', '1', '2011-07-01 18:01:33', '1', 'GHANA', 'GH');
INSERT INTO `#__sdi_sys_country` VALUES ('75', '64335f28-a3fb-11e0-9e98-00163e3098aa', '75', '1', '2011-07-01 18:01:33', '1', 'GIBRALTAR', 'GI');
INSERT INTO `#__sdi_sys_country` VALUES ('76', '643369be-a3fb-11e0-9e98-00163e3098aa', '76', '1', '2011-07-01 18:01:33', '1', 'Grande Bretagne', 'GB');
INSERT INTO `#__sdi_sys_country` VALUES ('77', '64337486-a3fb-11e0-9e98-00163e3098aa', '77', '1', '2011-07-01 18:01:33', '1', 'GREECE', 'GR');
INSERT INTO `#__sdi_sys_country` VALUES ('78', '64337f44-a3fb-11e0-9e98-00163e3098aa', '78', '1', '2011-07-01 18:01:33', '1', 'GRENADA', 'GD');
INSERT INTO `#__sdi_sys_country` VALUES ('79', '64338c96-a3fb-11e0-9e98-00163e3098aa', '79', '1', '2011-07-01 18:01:33', '1', 'GREENLAND', 'GL');
INSERT INTO `#__sdi_sys_country` VALUES ('80', '6433972c-a3fb-11e0-9e98-00163e3098aa', '80', '1', '2011-07-01 18:01:33', '1', 'GUADELOUPE', 'GP');
INSERT INTO `#__sdi_sys_country` VALUES ('81', '6433a1c2-a3fb-11e0-9e98-00163e3098aa', '81', '1', '2011-07-01 18:01:33', '1', 'GUAM', 'GU');
INSERT INTO `#__sdi_sys_country` VALUES ('82', '6433ac62-a3fb-11e0-9e98-00163e3098aa', '82', '1', '2011-07-01 18:01:33', '1', 'GUATEMALA', 'GT');
INSERT INTO `#__sdi_sys_country` VALUES ('83', '6433b6e4-a3fb-11e0-9e98-00163e3098aa', '83', '1', '2011-07-01 18:01:33', '1', 'GUINEA', 'GN');
INSERT INTO `#__sdi_sys_country` VALUES ('84', '6433c18e-a3fb-11e0-9e98-00163e3098aa', '84', '1', '2011-07-01 18:01:33', '1', 'Guinée Equator.', 'GQ');
INSERT INTO `#__sdi_sys_country` VALUES ('85', '6433da3e-a3fb-11e0-9e98-00163e3098aa', '85', '1', '2011-07-01 18:01:33', '1', 'GUINEA-BISSAU', 'GW');
INSERT INTO `#__sdi_sys_country` VALUES ('86', '6433e538-a3fb-11e0-9e98-00163e3098aa', '86', '1', '2011-07-01 18:01:33', '1', 'GUYANA', 'GY');
INSERT INTO `#__sdi_sys_country` VALUES ('87', '6433efc4-a3fb-11e0-9e98-00163e3098aa', '87', '1', '2011-07-01 18:01:33', '1', 'Guyane fran.', 'GF');
INSERT INTO `#__sdi_sys_country` VALUES ('88', '6433fa46-a3fb-11e0-9e98-00163e3098aa', '88', '1', '2011-07-01 18:01:33', '1', 'HAITI', 'HT');
INSERT INTO `#__sdi_sys_country` VALUES ('89', '643404dc-a3fb-11e0-9e98-00163e3098aa', '89', '1', '2011-07-01 18:01:33', '1', 'HONDURAS', 'HN');
INSERT INTO `#__sdi_sys_country` VALUES ('90', '64340f7c-a3fb-11e0-9e98-00163e3098aa', '90', '1', '2011-07-01 18:01:33', '1', 'HONG KONG', 'HK');
INSERT INTO `#__sdi_sys_country` VALUES ('91', '64341a80-a3fb-11e0-9e98-00163e3098aa', '91', '1', '2011-07-01 18:01:33', '1', 'HUNGARY', 'HU');
INSERT INTO `#__sdi_sys_country` VALUES ('92', '64342796-a3fb-11e0-9e98-00163e3098aa', '92', '1', '2011-07-01 18:01:33', '1', 'I. vierges amér', 'VI');
INSERT INTO `#__sdi_sys_country` VALUES ('93', '6434324a-a3fb-11e0-9e98-00163e3098aa', '93', '1', '2011-07-01 18:01:33', '1', 'I. vierges brit', 'VG');
INSERT INTO `#__sdi_sys_country` VALUES ('94', '64343cd6-a3fb-11e0-9e98-00163e3098aa', '94', '1', '2011-07-01 18:01:33', '1', 'Il.Heard/McDon.', 'HM');
INSERT INTO `#__sdi_sys_country` VALUES ('95', '6434476c-a3fb-11e0-9e98-00163e3098aa', '95', '1', '2011-07-01 18:01:33', '1', 'Ile Maurice', 'MU');
INSERT INTO `#__sdi_sys_country` VALUES ('96', '643451f8-a3fb-11e0-9e98-00163e3098aa', '96', '1', '2011-07-01 18:01:33', '1', 'Ile N.Mariana', 'MP');
INSERT INTO `#__sdi_sys_country` VALUES ('97', '64345c84-a3fb-11e0-9e98-00163e3098aa', '97', '1', '2011-07-01 18:01:33', '1', 'Iles Bouvet', 'BV');
INSERT INTO `#__sdi_sys_country` VALUES ('98', '64346738-a3fb-11e0-9e98-00163e3098aa', '98', '1', '2011-07-01 18:01:33', '1', 'Iles caïmans', 'KY');
INSERT INTO `#__sdi_sys_country` VALUES ('99', '643471e2-a3fb-11e0-9e98-00163e3098aa', '99', '1', '2011-07-01 18:01:33', '1', 'Iles Cocos', 'CC');
INSERT INTO `#__sdi_sys_country` VALUES ('100', '64347c78-a3fb-11e0-9e98-00163e3098aa', '100', '1', '2011-07-01 18:01:33', '1', 'Iles Cook', 'CK');
INSERT INTO `#__sdi_sys_country` VALUES ('101', '6434870e-a3fb-11e0-9e98-00163e3098aa', '101', '1', '2011-07-01 18:01:33', '1', 'Iles Marshall', 'MH');
INSERT INTO `#__sdi_sys_country` VALUES ('102', '6434919a-a3fb-11e0-9e98-00163e3098aa', '102', '1', '2011-07-01 18:01:33', '1', 'Iles Minor Outl', 'UM');
INSERT INTO `#__sdi_sys_country` VALUES ('103', '64349c30-a3fb-11e0-9e98-00163e3098aa', '103', '1', '2011-07-01 18:01:33', '1', 'Iles Niue', 'NU');
INSERT INTO `#__sdi_sys_country` VALUES ('104', '6434a6d0-a3fb-11e0-9e98-00163e3098aa', '104', '1', '2011-07-01 18:01:33', '1', 'Iles Norfolk', 'NF');
INSERT INTO `#__sdi_sys_country` VALUES ('105', '6434b404-a3fb-11e0-9e98-00163e3098aa', '105', '1', '2011-07-01 18:01:33', '1', 'Iles Pitcairn', 'PN');
INSERT INTO `#__sdi_sys_country` VALUES ('106', '6434becc-a3fb-11e0-9e98-00163e3098aa', '106', '1', '2011-07-01 18:01:33', '1', 'Iles Tokelau', 'TK');
INSERT INTO `#__sdi_sys_country` VALUES ('107', '6434c958-a3fb-11e0-9e98-00163e3098aa', '107', '1', '2011-07-01 18:01:33', '1', 'Ind.occ.ter.br.', 'IO');
INSERT INTO `#__sdi_sys_country` VALUES ('108', '6434d3f8-a3fb-11e0-9e98-00163e3098aa', '108', '1', '2011-07-01 18:01:33', '1', 'INDIA', 'IN');
INSERT INTO `#__sdi_sys_country` VALUES ('109', '6434de84-a3fb-11e0-9e98-00163e3098aa', '109', '1', '2011-07-01 18:01:33', '1', 'INDONESIA', 'ID');
INSERT INTO `#__sdi_sys_country` VALUES ('110', '6434fa72-a3fb-11e0-9e98-00163e3098aa', '110', '1', '2011-07-01 18:01:33', '1', 'IRAQ', 'IQ');
INSERT INTO `#__sdi_sys_country` VALUES ('111', '64350512-a3fb-11e0-9e98-00163e3098aa', '111', '1', '2011-07-01 18:01:33', '1', 'IRAN, ISLAMIC REPUBLIC OF', 'IR');
INSERT INTO `#__sdi_sys_country` VALUES ('112', '64350f9e-a3fb-11e0-9e98-00163e3098aa', '112', '1', '2011-07-01 18:01:33', '1', 'IRELAND', 'IE');
INSERT INTO `#__sdi_sys_country` VALUES ('113', '64351aca-a3fb-11e0-9e98-00163e3098aa', '113', '1', '2011-07-01 18:01:33', '1', 'Islande', 'IS');
INSERT INTO `#__sdi_sys_country` VALUES ('114', '64352560-a3fb-11e0-9e98-00163e3098aa', '114', '1', '2011-07-01 18:01:33', '1', 'ISRAEL', 'IL');
INSERT INTO `#__sdi_sys_country` VALUES ('115', '64352fd8-a3fb-11e0-9e98-00163e3098aa', '115', '1', '2011-07-01 18:01:33', '1', 'ITALY', 'IT');
INSERT INTO `#__sdi_sys_country` VALUES ('116', '64353a64-a3fb-11e0-9e98-00163e3098aa', '116', '1', '2011-07-01 18:01:33', '1', 'JAMAICA', 'JM');
INSERT INTO `#__sdi_sys_country` VALUES ('117', '6435489c-a3fb-11e0-9e98-00163e3098aa', '117', '1', '2011-07-01 18:01:33', '1', 'JAPAN', 'JP');
INSERT INTO `#__sdi_sys_country` VALUES ('118', '64355396-a3fb-11e0-9e98-00163e3098aa', '118', '1', '2011-07-01 18:01:33', '1', 'Jordanie', 'JO');
INSERT INTO `#__sdi_sys_country` VALUES ('119', '64355e5e-a3fb-11e0-9e98-00163e3098aa', '119', '1', '2011-07-01 18:01:33', '1', 'Kazakhstan', 'KZ');
INSERT INTO `#__sdi_sys_country` VALUES ('120', '643568ea-a3fb-11e0-9e98-00163e3098aa', '120', '1', '2011-07-01 18:01:33', '1', 'Kenya', 'KE');
INSERT INTO `#__sdi_sys_country` VALUES ('121', '643573b2-a3fb-11e0-9e98-00163e3098aa', '121', '1', '2011-07-01 18:01:33', '1', 'Kirghiztan', 'KG');
INSERT INTO `#__sdi_sys_country` VALUES ('122', '64357e5c-a3fb-11e0-9e98-00163e3098aa', '122', '1', '2011-07-01 18:01:33', '1', 'Kiribati', 'KI');
INSERT INTO `#__sdi_sys_country` VALUES ('123', '643588e8-a3fb-11e0-9e98-00163e3098aa', '123', '1', '2011-07-01 18:01:33', '1', 'Koweït', 'KW');
INSERT INTO `#__sdi_sys_country` VALUES ('124', '64359388-a3fb-11e0-9e98-00163e3098aa', '124', '1', '2011-07-01 18:01:33', '1', 'La Dominique', 'DM');
INSERT INTO `#__sdi_sys_country` VALUES ('125', '64359e14-a3fb-11e0-9e98-00163e3098aa', '125', '1', '2011-07-01 18:01:33', '1', 'Laos', 'LA');
INSERT INTO `#__sdi_sys_country` VALUES ('126', '6435a8a0-a3fb-11e0-9e98-00163e3098aa', '126', '1', '2011-07-01 18:01:33', '1', 'Lesotho', 'LS');
INSERT INTO `#__sdi_sys_country` VALUES ('127', '6435b32c-a3fb-11e0-9e98-00163e3098aa', '127', '1', '2011-07-01 18:01:33', '1', 'Lettonie', 'LV');
INSERT INTO `#__sdi_sys_country` VALUES ('128', '6435bdae-a3fb-11e0-9e98-00163e3098aa', '128', '1', '2011-07-01 18:01:33', '1', 'Liban', 'LB');
INSERT INTO `#__sdi_sys_country` VALUES ('129', '6435c83a-a3fb-11e0-9e98-00163e3098aa', '129', '1', '2011-07-01 18:01:33', '1', 'Liberia', 'LR');
INSERT INTO `#__sdi_sys_country` VALUES ('130', '6435d50a-a3fb-11e0-9e98-00163e3098aa', '130', '1', '2011-07-01 18:01:33', '1', 'Libye', 'LY');
INSERT INTO `#__sdi_sys_country` VALUES ('131', '6435df96-a3fb-11e0-9e98-00163e3098aa', '131', '1', '2011-07-01 18:01:33', '1', 'Liechtenstein', 'LI');
INSERT INTO `#__sdi_sys_country` VALUES ('132', '6435eaa4-a3fb-11e0-9e98-00163e3098aa', '132', '1', '2011-07-01 18:01:33', '1', 'Lituanie', 'LT');
INSERT INTO `#__sdi_sys_country` VALUES ('133', '6435f562-a3fb-11e0-9e98-00163e3098aa', '133', '1', '2011-07-01 18:01:33', '1', 'Luxembourg', 'LU');
INSERT INTO `#__sdi_sys_country` VALUES ('134', '6436000c-a3fb-11e0-9e98-00163e3098aa', '134', '1', '2011-07-01 18:01:33', '1', 'Macao', 'MO');
INSERT INTO `#__sdi_sys_country` VALUES ('135', '64360ad4-a3fb-11e0-9e98-00163e3098aa', '135', '1', '2011-07-01 18:01:33', '1', 'Macédoine', 'MK');
INSERT INTO `#__sdi_sys_country` VALUES ('136', '64362a3c-a3fb-11e0-9e98-00163e3098aa', '136', '1', '2011-07-01 18:01:33', '1', 'Madagascar', 'MG');
INSERT INTO `#__sdi_sys_country` VALUES ('137', '64363504-a3fb-11e0-9e98-00163e3098aa', '137', '1', '2011-07-01 18:01:33', '1', 'Malaisie', 'MY');
INSERT INTO `#__sdi_sys_country` VALUES ('138', '64363f9a-a3fb-11e0-9e98-00163e3098aa', '138', '1', '2011-07-01 18:01:33', '1', 'Malawi', 'MW');
INSERT INTO `#__sdi_sys_country` VALUES ('139', '64364a12-a3fb-11e0-9e98-00163e3098aa', '139', '1', '2011-07-01 18:01:33', '1', 'Maldives', 'MV');
INSERT INTO `#__sdi_sys_country` VALUES ('140', '643654d0-a3fb-11e0-9e98-00163e3098aa', '140', '1', '2011-07-01 18:01:33', '1', 'Mali', 'ML');
INSERT INTO `#__sdi_sys_country` VALUES ('141', '64365f52-a3fb-11e0-9e98-00163e3098aa', '141', '1', '2011-07-01 18:01:33', '1', 'Malouines', 'FK');
INSERT INTO `#__sdi_sys_country` VALUES ('142', '64367726-a3fb-11e0-9e98-00163e3098aa', '142', '1', '2011-07-01 18:01:33', '1', 'Malte', 'MT');
INSERT INTO `#__sdi_sys_country` VALUES ('143', '64368220-a3fb-11e0-9e98-00163e3098aa', '143', '1', '2011-07-01 18:01:33', '1', 'Maroc', 'MA');
INSERT INTO `#__sdi_sys_country` VALUES ('144', '64368cde-a3fb-11e0-9e98-00163e3098aa', '144', '1', '2011-07-01 18:01:33', '1', 'Martinique', 'MQ');
INSERT INTO `#__sdi_sys_country` VALUES ('145', '643698f0-a3fb-11e0-9e98-00163e3098aa', '145', '1', '2011-07-01 18:01:33', '1', 'Mauritanie', 'MR');
INSERT INTO `#__sdi_sys_country` VALUES ('146', '6436a35e-a3fb-11e0-9e98-00163e3098aa', '146', '1', '2011-07-01 18:01:33', '1', 'Mayotte', 'YT');
INSERT INTO `#__sdi_sys_country` VALUES ('147', '6436ae6c-a3fb-11e0-9e98-00163e3098aa', '147', '1', '2011-07-01 18:01:33', '1', 'Mexique', 'MX');
INSERT INTO `#__sdi_sys_country` VALUES ('148', '6436b90c-a3fb-11e0-9e98-00163e3098aa', '148', '1', '2011-07-01 18:01:33', '1', 'Micronésie', 'FM');
INSERT INTO `#__sdi_sys_country` VALUES ('149', '6436c398-a3fb-11e0-9e98-00163e3098aa', '149', '1', '2011-07-01 18:01:33', '1', 'Moldavie', 'MD');
INSERT INTO `#__sdi_sys_country` VALUES ('150', '6436ce24-a3fb-11e0-9e98-00163e3098aa', '150', '1', '2011-07-01 18:01:33', '1', 'Monaco', 'MC');
INSERT INTO `#__sdi_sys_country` VALUES ('151', '6436d8ba-a3fb-11e0-9e98-00163e3098aa', '151', '1', '2011-07-01 18:01:33', '1', 'Mongolie', 'MN');
INSERT INTO `#__sdi_sys_country` VALUES ('152', '6436e350-a3fb-11e0-9e98-00163e3098aa', '152', '1', '2011-07-01 18:01:33', '1', 'Montserrat', 'MS');
INSERT INTO `#__sdi_sys_country` VALUES ('153', '6436eddc-a3fb-11e0-9e98-00163e3098aa', '153', '1', '2011-07-01 18:01:33', '1', 'Mozambique', 'MZ');
INSERT INTO `#__sdi_sys_country` VALUES ('154', '6436f87c-a3fb-11e0-9e98-00163e3098aa', '154', '1', '2011-07-01 18:01:33', '1', 'Myanmar', 'MM');
INSERT INTO `#__sdi_sys_country` VALUES ('155', '64370560-a3fb-11e0-9e98-00163e3098aa', '155', '1', '2011-07-01 18:01:33', '1', 'Namibie', 'NA');
INSERT INTO `#__sdi_sys_country` VALUES ('156', '64370fec-a3fb-11e0-9e98-00163e3098aa', '156', '1', '2011-07-01 18:01:33', '1', 'Nauru', 'NR');
INSERT INTO `#__sdi_sys_country` VALUES ('157', '64371a6e-a3fb-11e0-9e98-00163e3098aa', '157', '1', '2011-07-01 18:01:33', '1', 'Népal', 'NP');
INSERT INTO `#__sdi_sys_country` VALUES ('158', '643724dc-a3fb-11e0-9e98-00163e3098aa', '158', '1', '2011-07-01 18:01:33', '1', 'Nicaragua', 'NI');
INSERT INTO `#__sdi_sys_country` VALUES ('159', '64372f72-a3fb-11e0-9e98-00163e3098aa', '159', '1', '2011-07-01 18:01:33', '1', 'Niger', 'NE');
INSERT INTO `#__sdi_sys_country` VALUES ('160', '64373a08-a3fb-11e0-9e98-00163e3098aa', '160', '1', '2011-07-01 18:01:33', '1', 'Nigéria', 'NG');
INSERT INTO `#__sdi_sys_country` VALUES ('161', '64374494-a3fb-11e0-9e98-00163e3098aa', '161', '1', '2011-07-01 18:01:33', '1', 'Nlle Calédonie', 'NC');
INSERT INTO `#__sdi_sys_country` VALUES ('162', '64374f34-a3fb-11e0-9e98-00163e3098aa', '162', '1', '2011-07-01 18:01:33', '1', 'Nlle Zélande', 'NZ');
INSERT INTO `#__sdi_sys_country` VALUES ('163', '643759b6-a3fb-11e0-9e98-00163e3098aa', '163', '1', '2011-07-01 18:01:33', '1', 'Norvège', 'NO');
INSERT INTO `#__sdi_sys_country` VALUES ('164', '643779aa-a3fb-11e0-9e98-00163e3098aa', '164', '1', '2011-07-01 18:01:33', '1', 'Oman', 'OM');
INSERT INTO `#__sdi_sys_country` VALUES ('165', '6437845e-a3fb-11e0-9e98-00163e3098aa', '165', '1', '2011-07-01 18:01:33', '1', 'Ouganda', 'UG');
INSERT INTO `#__sdi_sys_country` VALUES ('166', '64378f08-a3fb-11e0-9e98-00163e3098aa', '166', '1', '2011-07-01 18:01:33', '1', 'Ouzbékistan', 'UZ');
INSERT INTO `#__sdi_sys_country` VALUES ('167', '6437998a-a3fb-11e0-9e98-00163e3098aa', '167', '1', '2011-07-01 18:01:33', '1', 'Pakistan', 'PK');
INSERT INTO `#__sdi_sys_country` VALUES ('168', '6437a664-a3fb-11e0-9e98-00163e3098aa', '168', '1', '2011-07-01 18:01:33', '1', 'Palauan', 'PW');
INSERT INTO `#__sdi_sys_country` VALUES ('169', '6437b0fa-a3fb-11e0-9e98-00163e3098aa', '169', '1', '2011-07-01 18:01:33', '1', 'Panama', 'PA');
INSERT INTO `#__sdi_sys_country` VALUES ('170', '6437bbfe-a3fb-11e0-9e98-00163e3098aa', '170', '1', '2011-07-01 18:01:33', '1', 'Pap.Nouv.Guinée', 'PG');
INSERT INTO `#__sdi_sys_country` VALUES ('171', '6437c6a8-a3fb-11e0-9e98-00163e3098aa', '171', '1', '2011-07-01 18:01:33', '1', 'Paraguay', 'PY');
INSERT INTO `#__sdi_sys_country` VALUES ('172', '6437d120-a3fb-11e0-9e98-00163e3098aa', '172', '1', '2011-07-01 18:01:33', '1', 'Pays-Bas', 'NL');
INSERT INTO `#__sdi_sys_country` VALUES ('173', '6437dba2-a3fb-11e0-9e98-00163e3098aa', '173', '1', '2011-07-01 18:01:33', '1', 'Pérou', 'PE');
INSERT INTO `#__sdi_sys_country` VALUES ('174', '6437e642-a3fb-11e0-9e98-00163e3098aa', '174', '1', '2011-07-01 18:01:33', '1', 'Philippines', 'PH');
INSERT INTO `#__sdi_sys_country` VALUES ('175', '6437f0c4-a3fb-11e0-9e98-00163e3098aa', '175', '1', '2011-07-01 18:01:33', '1', 'Pologne', 'PL');
INSERT INTO `#__sdi_sys_country` VALUES ('176', '6437fb5a-a3fb-11e0-9e98-00163e3098aa', '176', '1', '2011-07-01 18:01:33', '1', 'Polynésie fran.', 'PF');
INSERT INTO `#__sdi_sys_country` VALUES ('177', '643805dc-a3fb-11e0-9e98-00163e3098aa', '177', '1', '2011-07-01 18:01:33', '1', 'Porto Rico', 'PR');
INSERT INTO `#__sdi_sys_country` VALUES ('178', '64381068-a3fb-11e0-9e98-00163e3098aa', '178', '1', '2011-07-01 18:01:33', '1', 'Portugal', 'PT');
INSERT INTO `#__sdi_sys_country` VALUES ('179', '64381af4-a3fb-11e0-9e98-00163e3098aa', '179', '1', '2011-07-01 18:01:33', '1', 'Qatar', 'QA');
INSERT INTO `#__sdi_sys_country` VALUES ('180', '64382576-a3fb-11e0-9e98-00163e3098aa', '180', '1', '2011-07-01 18:01:33', '1', 'Rép. centrafr.', 'CF');
INSERT INTO `#__sdi_sys_country` VALUES ('181', '6438323c-a3fb-11e0-9e98-00163e3098aa', '181', '1', '2011-07-01 18:01:33', '1', 'Rép. tchèque', 'CZ');
INSERT INTO `#__sdi_sys_country` VALUES ('182', '64383cdc-a3fb-11e0-9e98-00163e3098aa', '182', '1', '2011-07-01 18:01:33', '1', 'Rép.Dominicaine', 'DO');
INSERT INTO `#__sdi_sys_country` VALUES ('183', '6438474a-a3fb-11e0-9e98-00163e3098aa', '183', '1', '2011-07-01 18:01:33', '1', 'Réunion', 'RE');
INSERT INTO `#__sdi_sys_country` VALUES ('184', '643851d6-a3fb-11e0-9e98-00163e3098aa', '184', '1', '2011-07-01 18:01:33', '1', 'Roumanie', 'RO');
INSERT INTO `#__sdi_sys_country` VALUES ('185', '64385ca8-a3fb-11e0-9e98-00163e3098aa', '185', '1', '2011-07-01 18:01:33', '1', 'RS', 'RS');
INSERT INTO `#__sdi_sys_country` VALUES ('186', '64386752-a3fb-11e0-9e98-00163e3098aa', '186', '1', '2011-07-01 18:01:33', '1', 'Rwanda', 'RW');
INSERT INTO `#__sdi_sys_country` VALUES ('187', '643871de-a3fb-11e0-9e98-00163e3098aa', '187', '1', '2011-07-01 18:01:33', '1', 'S.Tomé-et-princ', 'ST');
INSERT INTO `#__sdi_sys_country` VALUES ('188', '64387cb0-a3fb-11e0-9e98-00163e3098aa', '188', '1', '2011-07-01 18:01:33', '1', 'Saint-Marin', 'SM');
INSERT INTO `#__sdi_sys_country` VALUES ('189', '64388746-a3fb-11e0-9e98-00163e3098aa', '189', '1', '2011-07-01 18:01:33', '1', 'Sainte-Hélène', 'SH');
INSERT INTO `#__sdi_sys_country` VALUES ('190', '643891d2-a3fb-11e0-9e98-00163e3098aa', '190', '1', '2011-07-01 18:01:33', '1', 'Salomon', 'SB');
INSERT INTO `#__sdi_sys_country` VALUES ('191', '64389c54-a3fb-11e0-9e98-00163e3098aa', '191', '1', '2011-07-01 18:01:33', '1', 'Samoa occident.', 'WS');
INSERT INTO `#__sdi_sys_country` VALUES ('192', '6438bf4a-a3fb-11e0-9e98-00163e3098aa', '192', '1', '2011-07-01 18:01:33', '1', 'Samoa, améric.', 'AS');
INSERT INTO `#__sdi_sys_country` VALUES ('193', '6438cc1a-a3fb-11e0-9e98-00163e3098aa', '193', '1', '2011-07-01 18:01:33', '1', 'Sénégal', 'SN');
INSERT INTO `#__sdi_sys_country` VALUES ('194', '6438d6ba-a3fb-11e0-9e98-00163e3098aa', '194', '1', '2011-07-01 18:01:33', '1', 'Seychelles', 'SC');
INSERT INTO `#__sdi_sys_country` VALUES ('195', '6438e150-a3fb-11e0-9e98-00163e3098aa', '195', '1', '2011-07-01 18:01:33', '1', 'Sierra Leone', 'SL');
INSERT INTO `#__sdi_sys_country` VALUES ('196', '6438ebc8-a3fb-11e0-9e98-00163e3098aa', '196', '1', '2011-07-01 18:01:33', '1', 'Singapour', 'SG');
INSERT INTO `#__sdi_sys_country` VALUES ('197', '6438f6e0-a3fb-11e0-9e98-00163e3098aa', '197', '1', '2011-07-01 18:01:33', '1', 'Slovaquie', 'SK');
INSERT INTO `#__sdi_sys_country` VALUES ('198', '64390194-a3fb-11e0-9e98-00163e3098aa', '198', '1', '2011-07-01 18:01:33', '1', 'Slovénie', 'SI');
INSERT INTO `#__sdi_sys_country` VALUES ('199', '64390c0c-a3fb-11e0-9e98-00163e3098aa', '199', '1', '2011-07-01 18:01:33', '1', 'Somalie', 'SO');
INSERT INTO `#__sdi_sys_country` VALUES ('200', '643916ac-a3fb-11e0-9e98-00163e3098aa', '200', '1', '2011-07-01 18:01:33', '1', 'Soudan', 'SD');
INSERT INTO `#__sdi_sys_country` VALUES ('201', '64392138-a3fb-11e0-9e98-00163e3098aa', '201', '1', '2011-07-01 18:01:33', '1', 'Sri Lanka', 'LK');
INSERT INTO `#__sdi_sys_country` VALUES ('202', '64392bb0-a3fb-11e0-9e98-00163e3098aa', '202', '1', '2011-07-01 18:01:33', '1', 'St. Lucie', 'LC');
INSERT INTO `#__sdi_sys_country` VALUES ('203', '6439363c-a3fb-11e0-9e98-00163e3098aa', '203', '1', '2011-07-01 18:01:33', '1', 'St. Vincent', 'VC');
INSERT INTO `#__sdi_sys_country` VALUES ('204', '643940be-a3fb-11e0-9e98-00163e3098aa', '204', '1', '2011-07-01 18:01:33', '1', 'St.Chr.,Nevis', 'KN');
INSERT INTO `#__sdi_sys_country` VALUES ('205', '64394b54-a3fb-11e0-9e98-00163e3098aa', '205', '1', '2011-07-01 18:01:33', '1', 'St.Pierre,Miqu.', 'PM');
INSERT INTO `#__sdi_sys_country` VALUES ('206', '64395b1c-a3fb-11e0-9e98-00163e3098aa', '206', '1', '2011-07-01 18:01:33', '1', 'Suède', 'SE');
INSERT INTO `#__sdi_sys_country` VALUES ('207', '2bc66c74-3652-11df-8ada-001f29c92132', '207', '1', '2010-01-01 00:00:00', '1', 'Suisse', 'CH');
INSERT INTO `#__sdi_sys_country` VALUES ('208', '64397016-a3fb-11e0-9e98-00163e3098aa', '208', '1', '2011-07-01 18:01:33', '1', 'Suriname', 'SR');
INSERT INTO `#__sdi_sys_country` VALUES ('209', '64397ac0-a3fb-11e0-9e98-00163e3098aa', '209', '1', '2011-07-01 18:01:33', '1', 'Svalbard', 'SJ');
INSERT INTO `#__sdi_sys_country` VALUES ('210', '6439854c-a3fb-11e0-9e98-00163e3098aa', '210', '1', '2011-07-01 18:01:33', '1', 'Swaziland', 'SZ');
INSERT INTO `#__sdi_sys_country` VALUES ('211', '64399046-a3fb-11e0-9e98-00163e3098aa', '211', '1', '2011-07-01 18:01:33', '1', 'Syrie', 'SY');
INSERT INTO `#__sdi_sys_country` VALUES ('212', '64399ac8-a3fb-11e0-9e98-00163e3098aa', '212', '1', '2011-07-01 18:01:33', '1', 'Tadjikistan', 'TJ');
INSERT INTO `#__sdi_sys_country` VALUES ('213', '6439a5c2-a3fb-11e0-9e98-00163e3098aa', '213', '1', '2011-07-01 18:01:33', '1', 'Taiwan', 'TW');
INSERT INTO `#__sdi_sys_country` VALUES ('214', '6439b03a-a3fb-11e0-9e98-00163e3098aa', '214', '1', '2011-07-01 18:01:33', '1', 'Tanzanie', 'TZ');
INSERT INTO `#__sdi_sys_country` VALUES ('215', '6439bb98-a3fb-11e0-9e98-00163e3098aa', '215', '1', '2011-07-01 18:01:33', '1', 'Tchad', 'TD');
INSERT INTO `#__sdi_sys_country` VALUES ('216', '6439c642-a3fb-11e0-9e98-00163e3098aa', '216', '1', '2011-07-01 18:01:33', '1', 'Thaïland', 'TH');
INSERT INTO `#__sdi_sys_country` VALUES ('217', '6439d0d8-a3fb-11e0-9e98-00163e3098aa', '217', '1', '2011-07-01 18:01:33', '1', 'Timor orient.', 'TP');
INSERT INTO `#__sdi_sys_country` VALUES ('218', '6439ddb2-a3fb-11e0-9e98-00163e3098aa', '218', '1', '2011-07-01 18:01:33', '1', 'Togo', 'TG');
INSERT INTO `#__sdi_sys_country` VALUES ('219', '6439e852-a3fb-11e0-9e98-00163e3098aa', '219', '1', '2011-07-01 18:01:33', '1', 'Tonga', 'TO');
INSERT INTO `#__sdi_sys_country` VALUES ('220', '643a0bfc-a3fb-11e0-9e98-00163e3098aa', '220', '1', '2011-07-01 18:01:33', '1', 'Trinidad,Tobago', 'TT');
INSERT INTO `#__sdi_sys_country` VALUES ('221', '643a1674-a3fb-11e0-9e98-00163e3098aa', '221', '1', '2011-07-01 18:01:33', '1', 'Tunisie', 'TN');
INSERT INTO `#__sdi_sys_country` VALUES ('222', '643a210a-a3fb-11e0-9e98-00163e3098aa', '222', '1', '2011-07-01 18:01:33', '1', 'Turkménistan', 'TM');
INSERT INTO `#__sdi_sys_country` VALUES ('223', '643a2c04-a3fb-11e0-9e98-00163e3098aa', '223', '1', '2011-07-01 18:01:33', '1', 'Turks & Caicos', 'TC');
INSERT INTO `#__sdi_sys_country` VALUES ('224', '643a3672-a3fb-11e0-9e98-00163e3098aa', '224', '1', '2011-07-01 18:01:33', '1', 'Turquie', 'TR');
INSERT INTO `#__sdi_sys_country` VALUES ('225', '643a4108-a3fb-11e0-9e98-00163e3098aa', '225', '1', '2011-07-01 18:01:33', '1', 'Tuvalu', 'TV');
INSERT INTO `#__sdi_sys_country` VALUES ('226', '643a4b8a-a3fb-11e0-9e98-00163e3098aa', '226', '1', '2011-07-01 18:01:33', '1', 'Ukraine', 'UA');
INSERT INTO `#__sdi_sys_country` VALUES ('227', '643a55f8-a3fb-11e0-9e98-00163e3098aa', '227', '1', '2011-07-01 18:01:33', '1', 'Uruguay', 'UY');
INSERT INTO `#__sdi_sys_country` VALUES ('228', '643a6084-a3fb-11e0-9e98-00163e3098aa', '228', '1', '2011-07-01 18:01:33', '1', 'USA', 'US');
INSERT INTO `#__sdi_sys_country` VALUES ('229', '643a6afc-a3fb-11e0-9e98-00163e3098aa', '229', '1', '2011-07-01 18:01:33', '1', 'Vanuatu', 'VU');
INSERT INTO `#__sdi_sys_country` VALUES ('230', '643a7592-a3fb-11e0-9e98-00163e3098aa', '230', '1', '2011-07-01 18:01:33', '1', 'Vatican', 'VA');
INSERT INTO `#__sdi_sys_country` VALUES ('231', '643a824e-a3fb-11e0-9e98-00163e3098aa', '231', '1', '2011-07-01 18:01:33', '1', 'Vénézuéla', 'VE');
INSERT INTO `#__sdi_sys_country` VALUES ('232', '643a8cda-a3fb-11e0-9e98-00163e3098aa', '232', '1', '2011-07-01 18:01:33', '1', 'Viêt Nam', 'VN');
INSERT INTO `#__sdi_sys_country` VALUES ('233', '643a9770-a3fb-11e0-9e98-00163e3098aa', '233', '1', '2011-07-01 18:01:33', '1', 'Wallis, Futuna', 'WF');
INSERT INTO `#__sdi_sys_country` VALUES ('234', '643aa210-a3fb-11e0-9e98-00163e3098aa', '234', '1', '2011-07-01 18:01:33', '1', 'Yémen', 'YE');
INSERT INTO `#__sdi_sys_country` VALUES ('235', '643aac74-a3fb-11e0-9e98-00163e3098aa', '235', '1', '2011-07-01 18:01:33', '1', 'Yougoslavie', 'YU');
INSERT INTO `#__sdi_sys_country` VALUES ('236', '643ab6f6-a3fb-11e0-9e98-00163e3098aa', '236', '1', '2011-07-01 18:01:33', '1', 'Zaïre', 'ZR');
INSERT INTO `#__sdi_sys_country` VALUES ('237', '643ac0ce-a3fb-11e0-9e98-00163e3098aa', '237', '1', '2011-07-01 18:01:33', '1', 'Zambie', 'ZM');
INSERT INTO `#__sdi_sys_country` VALUES ('238', '643ac9d4-a3fb-11e0-9e98-00163e3098aa', '238', '1', '2011-07-01 18:01:33', '1', 'Zimbabwe', 'ZW');
INSERT INTO `#__sdi_sys_country` VALUES ('239', 'e0452b78-75d3-11e1-bb00-00163e3098aa', '239', '1', '2012-03-24 18:07:45', '7', 'Non Connu', 'UNK');


