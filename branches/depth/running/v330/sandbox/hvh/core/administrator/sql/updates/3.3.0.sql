ALTER TABLE `#__sdi_sys_unit` DROP COLUMN checked_out;
ALTER TABLE `#__sdi_sys_unit` DROP COLUMN checked_out_time;
ALTER TABLE `#__sdi_sys_unit` DROP COLUMN created_by;
ALTER TABLE `#__sdi_sys_unit` DROP COLUMN created;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_role` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_role` VALUES ('1','1','1','resourcemanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('2','2','1','metadatamanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('3','3','1','metadataeditor' );
INSERT INTO `#__sdi_sys_role` VALUES ('4','4','1','productmanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('5','5','1','previewmanager' );

CREATE TABLE `#__sdi_sys_country` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ordering` bigint(20) NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_country` VALUES ('1', '1', '1', 'AFGHANISTAN', 'AF');
INSERT INTO `#__sdi_sys_country` VALUES ('2', '2', '1', 'SOUTH AFRICA', 'ZA');
INSERT INTO `#__sdi_sys_country` VALUES ('3', '3', '1', 'ALBANIA', 'AL');
INSERT INTO `#__sdi_sys_country` VALUES ('4', '4', '1', 'Algérie', 'DZ');
INSERT INTO `#__sdi_sys_country` VALUES ('5', '5', '1', 'GERMANY', 'DE');
INSERT INTO `#__sdi_sys_country` VALUES ('6', '6', '1', 'ANDORRA', 'AD');
INSERT INTO `#__sdi_sys_country` VALUES ('7', '7', '1', 'ANGOLA', 'AO');
INSERT INTO `#__sdi_sys_country` VALUES ('8', '8', '1', 'ANGUILLA', 'AI');
INSERT INTO `#__sdi_sys_country` VALUES ('9', '9', '1', 'ANTARCTICA', 'AQ');
INSERT INTO `#__sdi_sys_country` VALUES ('10', '10', '1', 'ANTIGUA AND BARBUDA', 'AG');
INSERT INTO `#__sdi_sys_country` VALUES ('11', '11', '1', 'Antilles néerl.', 'AN');
INSERT INTO `#__sdi_sys_country` VALUES ('12', '12', '1', 'apatride', 'STL');
INSERT INTO `#__sdi_sys_country` VALUES ('13', '13', '1', 'SAUDI ARABIA', 'SA');
INSERT INTO `#__sdi_sys_country` VALUES ('14', '14', '1', 'ARGENTINA', 'AR');
INSERT INTO `#__sdi_sys_country` VALUES ('15', '15', '1', 'ARMENIA', 'AM');
INSERT INTO `#__sdi_sys_country` VALUES ('16', '16', '1', 'ARUBA', 'AW');
INSERT INTO `#__sdi_sys_country` VALUES ('17', '17', '1', 'AUSTRALIA', 'AU');
INSERT INTO `#__sdi_sys_country` VALUES ('18', '18', '1', 'AUSTRIA', 'AT');
INSERT INTO `#__sdi_sys_country` VALUES ('19', '19', '1', 'AZERBAIJAN', 'AZ');
INSERT INTO `#__sdi_sys_country` VALUES ('20', '20', '1', 'BAHAMAS', 'BS');
INSERT INTO `#__sdi_sys_country` VALUES ('21', '21', '1', 'BAHRAIN', 'BH');
INSERT INTO `#__sdi_sys_country` VALUES ('22', '22', '1', 'BANGLADESH', 'BD');
INSERT INTO `#__sdi_sys_country` VALUES ('23', '23', '1', 'BARBADOS', 'BB');
INSERT INTO `#__sdi_sys_country` VALUES ('24', '24', '1', 'BELGIUM', 'BE');
INSERT INTO `#__sdi_sys_country` VALUES ('25', '25', '1', 'BELIZE', 'BZ');
INSERT INTO `#__sdi_sys_country` VALUES ('26', '26', '1', 'BENIN', 'BJ');
INSERT INTO `#__sdi_sys_country` VALUES ('27', '27', '1', 'BERMUDA', 'BM');
INSERT INTO `#__sdi_sys_country` VALUES ('28', '28', '1', 'BHUTAN', 'BT');
INSERT INTO `#__sdi_sys_country` VALUES ('29', '29', '1', 'BELARUS', 'BY');
INSERT INTO `#__sdi_sys_country` VALUES ('30', '30', '1', 'BOLIVIA, PLURINATIONAL STATE OF', 'BO');
INSERT INTO `#__sdi_sys_country` VALUES ('31', '31', '1', 'BOSNIA AND HERZEGOVINA', 'BA');
INSERT INTO `#__sdi_sys_country` VALUES ('32', '32', '1', 'BOTSWANA', 'BW');
INSERT INTO `#__sdi_sys_country` VALUES ('33', '33', '1', 'BRAZIL', 'BR');
INSERT INTO `#__sdi_sys_country` VALUES ('34', '34', '1', 'BRUNEI DARUSSALAM', 'BN');
INSERT INTO `#__sdi_sys_country` VALUES ('35', '35', '1', 'BULGARIA', 'BG');
INSERT INTO `#__sdi_sys_country` VALUES ('36', '36', '1', 'BURKINA FASO', 'BF');
INSERT INTO `#__sdi_sys_country` VALUES ('37', '37', '1', 'BURUNDI', 'BI');
INSERT INTO `#__sdi_sys_country` VALUES ('38', '38', '1', 'CAMBODIA', 'KH');
INSERT INTO `#__sdi_sys_country` VALUES ('39', '39', '1', 'CAMEROON', 'CM');
INSERT INTO `#__sdi_sys_country` VALUES ('40', '40', '1', 'CANADA', 'CA');
INSERT INTO `#__sdi_sys_country` VALUES ('41', '41', '1', 'CAPE VERDE', 'CV');
INSERT INTO `#__sdi_sys_country` VALUES ('42', '42', '1', 'CHILE', 'CL');
INSERT INTO `#__sdi_sys_country` VALUES ('43', '43', '1', 'CHILE', 'CN');
INSERT INTO `#__sdi_sys_country` VALUES ('44', '44', '1', 'CHRISTMAS ISLAND', 'CX');
INSERT INTO `#__sdi_sys_country` VALUES ('45', '45', '1', 'CYPRUS', 'CY');
INSERT INTO `#__sdi_sys_country` VALUES ('46', '46', '1', 'COLOMBIA', 'CO');
INSERT INTO `#__sdi_sys_country` VALUES ('47', '47', '1', 'COMOROS', 'KM');
INSERT INTO `#__sdi_sys_country` VALUES ('48', '48', '1', 'CONGO', 'CG');
INSERT INTO `#__sdi_sys_country` VALUES ('49', '49', '1', 'Corée du Nord', 'KP');
INSERT INTO `#__sdi_sys_country` VALUES ('50', '50', '1', 'Corée du Sud', 'KR');
INSERT INTO `#__sdi_sys_country` VALUES ('51', '51', '1', 'COSTA RICA', 'CR');
INSERT INTO `#__sdi_sys_country` VALUES ('52', '52', '1', 'CÔTE D IVOIRE', 'CI');
INSERT INTO `#__sdi_sys_country` VALUES ('53', '53', '1', 'CROATIA', 'HR');
INSERT INTO `#__sdi_sys_country` VALUES ('54', '54', '1', 'CUBA', 'CU');
INSERT INTO `#__sdi_sys_country` VALUES ('55', '55', '1', 'DENMARK', 'DK');
INSERT INTO `#__sdi_sys_country` VALUES ('56', '56', '1', 'DJIBOUTI', 'DJ');
INSERT INTO `#__sdi_sys_country` VALUES ('57', '57', '1', 'EGYPT', 'EG');
INSERT INTO `#__sdi_sys_country` VALUES ('58', '58', '1', 'EH', 'EH');
INSERT INTO `#__sdi_sys_country` VALUES ('59', '59', '1', 'EL SALVADOR', 'SV');
INSERT INTO `#__sdi_sys_country` VALUES ('60', '60', '1', 'Emir.arab.unis', 'AE');
INSERT INTO `#__sdi_sys_country` VALUES ('61', '61', '1', 'ECUADOR', 'EC');
INSERT INTO `#__sdi_sys_country` VALUES ('62', '62', '1', 'ERITREA', 'ER');
INSERT INTO `#__sdi_sys_country` VALUES ('63', '63', '1', 'Espagne', 'ES');
INSERT INTO `#__sdi_sys_country` VALUES ('64', '64', '1', 'ESTONIA', 'EE');
INSERT INTO `#__sdi_sys_country` VALUES ('65', '65', '1', 'ETHIOPIA', 'ET');
INSERT INTO `#__sdi_sys_country` VALUES ('66', '66', '1', 'Féd. russe', 'RU');
INSERT INTO `#__sdi_sys_country` VALUES ('67', '67', '1', 'FAROE ISLANDS', 'FO');
INSERT INTO `#__sdi_sys_country` VALUES ('68', '68', '1', 'FIJI', 'FJ');
INSERT INTO `#__sdi_sys_country` VALUES ('69', '69', '1', 'FINLAND', 'FI');
INSERT INTO `#__sdi_sys_country` VALUES ('70', '70', '1', 'FRANCE', 'FR');
INSERT INTO `#__sdi_sys_country` VALUES ('71', '71', '1', 'GABON', 'GA');
INSERT INTO `#__sdi_sys_country` VALUES ('72', '72', '1', 'GAMBIA', 'GM');
INSERT INTO `#__sdi_sys_country` VALUES ('73', '73', '1', 'GEORGIA', 'GE');
INSERT INTO `#__sdi_sys_country` VALUES ('74', '74', '1', 'GHANA', 'GH');
INSERT INTO `#__sdi_sys_country` VALUES ('75', '75', '1', 'GIBRALTAR', 'GI');
INSERT INTO `#__sdi_sys_country` VALUES ('76', '76', '1', 'Grande Bretagne', 'GB');
INSERT INTO `#__sdi_sys_country` VALUES ('77', '77', '1', 'GREECE', 'GR');
INSERT INTO `#__sdi_sys_country` VALUES ('78', '78', '1', 'GRENADA', 'GD');
INSERT INTO `#__sdi_sys_country` VALUES ('79', '79', '1', 'GREENLAND', 'GL');
INSERT INTO `#__sdi_sys_country` VALUES ('80', '80', '1', 'GUADELOUPE', 'GP');
INSERT INTO `#__sdi_sys_country` VALUES ('81', '81', '1', 'GUAM', 'GU');
INSERT INTO `#__sdi_sys_country` VALUES ('82', '82', '1', 'GUATEMALA', 'GT');
INSERT INTO `#__sdi_sys_country` VALUES ('83', '83', '1', 'GUINEA', 'GN');
INSERT INTO `#__sdi_sys_country` VALUES ('84', '84', '1', 'Guinée Equator.', 'GQ');
INSERT INTO `#__sdi_sys_country` VALUES ('85', '85', '1', 'GUINEA-BISSAU', 'GW');
INSERT INTO `#__sdi_sys_country` VALUES ('86', '86', '1', 'GUYANA', 'GY');
INSERT INTO `#__sdi_sys_country` VALUES ('87', '87', '1', 'Guyane fran.', 'GF');
INSERT INTO `#__sdi_sys_country` VALUES ('88', '88', '1', 'HAITI', 'HT');
INSERT INTO `#__sdi_sys_country` VALUES ('89', '89', '1', 'HONDURAS', 'HN');
INSERT INTO `#__sdi_sys_country` VALUES ('90', '90', '1', 'HONG KONG', 'HK');
INSERT INTO `#__sdi_sys_country` VALUES ('91', '91', '1', 'HUNGARY', 'HU');
INSERT INTO `#__sdi_sys_country` VALUES ('92', '92', '1', 'I. vierges amér', 'VI');
INSERT INTO `#__sdi_sys_country` VALUES ('93', '93', '1', 'I. vierges brit', 'VG');
INSERT INTO `#__sdi_sys_country` VALUES ('94', '94', '1', 'Il.Heard/McDon.', 'HM');
INSERT INTO `#__sdi_sys_country` VALUES ('95', '95', '1', 'Ile Maurice', 'MU');
INSERT INTO `#__sdi_sys_country` VALUES ('96', '96', '1', 'Ile N.Mariana', 'MP');
INSERT INTO `#__sdi_sys_country` VALUES ('97', '97', '1', 'Iles Bouvet', 'BV');
INSERT INTO `#__sdi_sys_country` VALUES ('98', '98', '1', 'Iles caïmans', 'KY');
INSERT INTO `#__sdi_sys_country` VALUES ('99', '99', '1', 'Iles Cocos', 'CC');
INSERT INTO `#__sdi_sys_country` VALUES ('100', '100', '1', 'Iles Cook', 'CK');
INSERT INTO `#__sdi_sys_country` VALUES ('101', '101', '1', 'Iles Marshall', 'MH');
INSERT INTO `#__sdi_sys_country` VALUES ('102', '102', '1', 'Iles Minor Outl', 'UM');
INSERT INTO `#__sdi_sys_country` VALUES ('103', '103', '1', 'Iles Niue', 'NU');
INSERT INTO `#__sdi_sys_country` VALUES ('104', '104', '1', 'Iles Norfolk', 'NF');
INSERT INTO `#__sdi_sys_country` VALUES ('105', '105', '1', 'Iles Pitcairn', 'PN');
INSERT INTO `#__sdi_sys_country` VALUES ('106', '106', '1', 'Iles Tokelau', 'TK');
INSERT INTO `#__sdi_sys_country` VALUES ('107', '107', '1', 'Ind.occ.ter.br.', 'IO');
INSERT INTO `#__sdi_sys_country` VALUES ('108', '108', '1', 'INDIA', 'IN');
INSERT INTO `#__sdi_sys_country` VALUES ('109', '109', '1', 'INDONESIA', 'ID');
INSERT INTO `#__sdi_sys_country` VALUES ('110', '110', '1', 'IRAQ', 'IQ');
INSERT INTO `#__sdi_sys_country` VALUES ('111', '111', '1', 'IRAN, ISLAMIC REPUBLIC OF', 'IR');
INSERT INTO `#__sdi_sys_country` VALUES ('112', '112', '1', 'IRELAND', 'IE');
INSERT INTO `#__sdi_sys_country` VALUES ('113', '113', '1', 'Islande', 'IS');
INSERT INTO `#__sdi_sys_country` VALUES ('114', '114', '1', 'ISRAEL', 'IL');
INSERT INTO `#__sdi_sys_country` VALUES ('115', '115', '1', 'ITALY', 'IT');
INSERT INTO `#__sdi_sys_country` VALUES ('116', '116', '1', 'JAMAICA', 'JM');
INSERT INTO `#__sdi_sys_country` VALUES ('117', '117', '1', 'JAPAN', 'JP');
INSERT INTO `#__sdi_sys_country` VALUES ('118', '118', '1', 'Jordanie', 'JO');
INSERT INTO `#__sdi_sys_country` VALUES ('119', '119', '1', 'Kazakhstan', 'KZ');
INSERT INTO `#__sdi_sys_country` VALUES ('120', '120', '1', 'Kenya', 'KE');
INSERT INTO `#__sdi_sys_country` VALUES ('121', '121', '1', 'Kirghiztan', 'KG');
INSERT INTO `#__sdi_sys_country` VALUES ('122', '122', '1', 'Kiribati', 'KI');
INSERT INTO `#__sdi_sys_country` VALUES ('123', '123', '1', 'Koweït', 'KW');
INSERT INTO `#__sdi_sys_country` VALUES ('124', '124', '1', 'La Dominique', 'DM');
INSERT INTO `#__sdi_sys_country` VALUES ('125', '125', '1', 'Laos', 'LA');
INSERT INTO `#__sdi_sys_country` VALUES ('126', '126', '1', 'Lesotho', 'LS');
INSERT INTO `#__sdi_sys_country` VALUES ('127', '127', '1', 'Lettonie', 'LV');
INSERT INTO `#__sdi_sys_country` VALUES ('128', '128', '1', 'Liban', 'LB');
INSERT INTO `#__sdi_sys_country` VALUES ('129', '129', '1', 'Liberia', 'LR');
INSERT INTO `#__sdi_sys_country` VALUES ('130', '130', '1', 'Libye', 'LY');
INSERT INTO `#__sdi_sys_country` VALUES ('131', '131', '1', 'Liechtenstein', 'LI');
INSERT INTO `#__sdi_sys_country` VALUES ('132', '132', '1', 'Lituanie', 'LT');
INSERT INTO `#__sdi_sys_country` VALUES ('133', '133', '1', 'Luxembourg', 'LU');
INSERT INTO `#__sdi_sys_country` VALUES ('134', '134', '1', 'Macao', 'MO');
INSERT INTO `#__sdi_sys_country` VALUES ('135', '135', '1', 'Macédoine', 'MK');
INSERT INTO `#__sdi_sys_country` VALUES ('136', '136', '1', 'Madagascar', 'MG');
INSERT INTO `#__sdi_sys_country` VALUES ('137', '137', '1', 'Malaisie', 'MY');
INSERT INTO `#__sdi_sys_country` VALUES ('138', '138', '1', 'Malawi', 'MW');
INSERT INTO `#__sdi_sys_country` VALUES ('139', '139', '1', 'Maldives', 'MV');
INSERT INTO `#__sdi_sys_country` VALUES ('140', '140', '1', 'Mali', 'ML');
INSERT INTO `#__sdi_sys_country` VALUES ('141', '141', '1', 'Malouines', 'FK');
INSERT INTO `#__sdi_sys_country` VALUES ('142', '142', '1', 'Malte', 'MT');
INSERT INTO `#__sdi_sys_country` VALUES ('143', '143', '1', 'Maroc', 'MA');
INSERT INTO `#__sdi_sys_country` VALUES ('144', '144', '1', 'Martinique', 'MQ');
INSERT INTO `#__sdi_sys_country` VALUES ('145', '145', '1', 'Mauritanie', 'MR');
INSERT INTO `#__sdi_sys_country` VALUES ('146', '146', '1', 'Mayotte', 'YT');
INSERT INTO `#__sdi_sys_country` VALUES ('147', '147', '1', 'Mexique', 'MX');
INSERT INTO `#__sdi_sys_country` VALUES ('148', '148', '1', 'Micronésie', 'FM');
INSERT INTO `#__sdi_sys_country` VALUES ('149', '149', '1', 'Moldavie', 'MD');
INSERT INTO `#__sdi_sys_country` VALUES ('150', '150', '1', 'Monaco', 'MC');
INSERT INTO `#__sdi_sys_country` VALUES ('151', '151', '1', 'Mongolie', 'MN');
INSERT INTO `#__sdi_sys_country` VALUES ('152', '152', '1', 'Montserrat', 'MS');
INSERT INTO `#__sdi_sys_country` VALUES ('153', '153', '1', 'Mozambique', 'MZ');
INSERT INTO `#__sdi_sys_country` VALUES ('154', '154', '1', 'Myanmar', 'MM');
INSERT INTO `#__sdi_sys_country` VALUES ('155', '155', '1', 'Namibie', 'NA');
INSERT INTO `#__sdi_sys_country` VALUES ('156', '156', '1', 'Nauru', 'NR');
INSERT INTO `#__sdi_sys_country` VALUES ('157', '157', '1', 'Népal', 'NP');
INSERT INTO `#__sdi_sys_country` VALUES ('158', '158', '1', 'Nicaragua', 'NI');
INSERT INTO `#__sdi_sys_country` VALUES ('159', '159', '1', 'Niger', 'NE');
INSERT INTO `#__sdi_sys_country` VALUES ('160', '160', '1', 'Nigéria', 'NG');
INSERT INTO `#__sdi_sys_country` VALUES ('161', '161', '1', 'Nlle Calédonie', 'NC');
INSERT INTO `#__sdi_sys_country` VALUES ('162', '162', '1', 'Nlle Zélande', 'NZ');
INSERT INTO `#__sdi_sys_country` VALUES ('163', '163', '1', 'Norvège', 'NO');
INSERT INTO `#__sdi_sys_country` VALUES ('164', '164', '1', 'Oman', 'OM');
INSERT INTO `#__sdi_sys_country` VALUES ('165', '165', '1', 'Ouganda', 'UG');
INSERT INTO `#__sdi_sys_country` VALUES ('166', '166', '1', 'Ouzbékistan', 'UZ');
INSERT INTO `#__sdi_sys_country` VALUES ('167', '167', '1', 'Pakistan', 'PK');
INSERT INTO `#__sdi_sys_country` VALUES ('168', '168', '1', 'Palauan', 'PW');
INSERT INTO `#__sdi_sys_country` VALUES ('169', '169', '1', 'Panama', 'PA');
INSERT INTO `#__sdi_sys_country` VALUES ('170', '170', '1', 'Pap.Nouv.Guinée', 'PG');
INSERT INTO `#__sdi_sys_country` VALUES ('171', '171', '1', 'Paraguay', 'PY');
INSERT INTO `#__sdi_sys_country` VALUES ('172', '172', '1', 'Pays-Bas', 'NL');
INSERT INTO `#__sdi_sys_country` VALUES ('173', '173', '1', 'Pérou', 'PE');
INSERT INTO `#__sdi_sys_country` VALUES ('174', '174', '1', 'Philippines', 'PH');
INSERT INTO `#__sdi_sys_country` VALUES ('175', '175', '1', 'Pologne', 'PL');
INSERT INTO `#__sdi_sys_country` VALUES ('176', '176', '1', 'Polynésie fran.', 'PF');
INSERT INTO `#__sdi_sys_country` VALUES ('177', '177', '1', 'Porto Rico', 'PR');
INSERT INTO `#__sdi_sys_country` VALUES ('178', '178', '1', 'Portugal', 'PT');
INSERT INTO `#__sdi_sys_country` VALUES ('179', '179', '1', 'Qatar', 'QA');
INSERT INTO `#__sdi_sys_country` VALUES ('180', '180', '1', 'Rép. centrafr.', 'CF');
INSERT INTO `#__sdi_sys_country` VALUES ('181', '181', '1', 'Rép. tchèque', 'CZ');
INSERT INTO `#__sdi_sys_country` VALUES ('182', '182', '1', 'Rép.Dominicaine', 'DO');
INSERT INTO `#__sdi_sys_country` VALUES ('183', '183', '1', 'Réunion', 'RE');
INSERT INTO `#__sdi_sys_country` VALUES ('184', '184', '1', 'Roumanie', 'RO');
INSERT INTO `#__sdi_sys_country` VALUES ('185', '185', '1', 'RS', 'RS');
INSERT INTO `#__sdi_sys_country` VALUES ('186', '186', '1', 'Rwanda', 'RW');
INSERT INTO `#__sdi_sys_country` VALUES ('187', '187', '1', 'S.Tomé-et-princ', 'ST');
INSERT INTO `#__sdi_sys_country` VALUES ('188', '188', '1', 'Saint-Marin', 'SM');
INSERT INTO `#__sdi_sys_country` VALUES ('189', '189', '1', 'Sainte-Hélène', 'SH');
INSERT INTO `#__sdi_sys_country` VALUES ('190', '190', '1', 'Salomon', 'SB');
INSERT INTO `#__sdi_sys_country` VALUES ('191', '191', '1', 'Samoa occident.', 'WS');
INSERT INTO `#__sdi_sys_country` VALUES ('192', '192', '1', 'Samoa, améric.', 'AS');
INSERT INTO `#__sdi_sys_country` VALUES ('193', '193', '1', 'Sénégal', 'SN');
INSERT INTO `#__sdi_sys_country` VALUES ('194', '194', '1', 'Seychelles', 'SC');
INSERT INTO `#__sdi_sys_country` VALUES ('195', '195', '1', 'Sierra Leone', 'SL');
INSERT INTO `#__sdi_sys_country` VALUES ('196', '196', '1', 'Singapour', 'SG');
INSERT INTO `#__sdi_sys_country` VALUES ('197', '197', '1', 'Slovaquie', 'SK');
INSERT INTO `#__sdi_sys_country` VALUES ('198', '198', '1', 'Slovénie', 'SI');
INSERT INTO `#__sdi_sys_country` VALUES ('199', '199', '1', 'Somalie', 'SO');
INSERT INTO `#__sdi_sys_country` VALUES ('200', '200', '1', 'Soudan', 'SD');
INSERT INTO `#__sdi_sys_country` VALUES ('201', '201', '1', 'Sri Lanka', 'LK');
INSERT INTO `#__sdi_sys_country` VALUES ('202', '202', '1', 'St. Lucie', 'LC');
INSERT INTO `#__sdi_sys_country` VALUES ('203', '203', '1', 'St. Vincent', 'VC');
INSERT INTO `#__sdi_sys_country` VALUES ('204', '204', '1', 'St.Chr.,Nevis', 'KN');
INSERT INTO `#__sdi_sys_country` VALUES ('205', '205', '1', 'St.Pierre,Miqu.', 'PM');
INSERT INTO `#__sdi_sys_country` VALUES ('206', '206', '1', 'Suède', 'SE');
INSERT INTO `#__sdi_sys_country` VALUES ('207', '207', '1', 'Suisse', 'CH');
INSERT INTO `#__sdi_sys_country` VALUES ('208', '208', '1', 'Suriname', 'SR');
INSERT INTO `#__sdi_sys_country` VALUES ('209', '209', '1', 'Svalbard', 'SJ');
INSERT INTO `#__sdi_sys_country` VALUES ('210', '210', '1', 'Swaziland', 'SZ');
INSERT INTO `#__sdi_sys_country` VALUES ('211', '211', '1', 'Syrie', 'SY');
INSERT INTO `#__sdi_sys_country` VALUES ('212', '212', '1', 'Tadjikistan', 'TJ');
INSERT INTO `#__sdi_sys_country` VALUES ('213', '213', '1', 'Taiwan', 'TW');
INSERT INTO `#__sdi_sys_country` VALUES ('214', '214', '1', 'Tanzanie', 'TZ');
INSERT INTO `#__sdi_sys_country` VALUES ('215', '215', '1', 'Tchad', 'TD');
INSERT INTO `#__sdi_sys_country` VALUES ('216', '216', '1', 'Thaïland', 'TH');
INSERT INTO `#__sdi_sys_country` VALUES ('217', '217', '1', 'Timor orient.', 'TP');
INSERT INTO `#__sdi_sys_country` VALUES ('218', '218', '1', 'Togo', 'TG');
INSERT INTO `#__sdi_sys_country` VALUES ('219', '219', '1', 'Tonga', 'TO');
INSERT INTO `#__sdi_sys_country` VALUES ('220', '220', '1', 'Trinidad,Tobago', 'TT');
INSERT INTO `#__sdi_sys_country` VALUES ('221', '221', '1', 'Tunisie', 'TN');
INSERT INTO `#__sdi_sys_country` VALUES ('222', '222', '1', 'Turkménistan', 'TM');
INSERT INTO `#__sdi_sys_country` VALUES ('223', '223', '1', 'Turks & Caicos', 'TC');
INSERT INTO `#__sdi_sys_country` VALUES ('224', '224', '1', 'Turquie', 'TR');
INSERT INTO `#__sdi_sys_country` VALUES ('225', '225', '1', 'Tuvalu', 'TV');
INSERT INTO `#__sdi_sys_country` VALUES ('226', '226', '1', 'Ukraine', 'UA');
INSERT INTO `#__sdi_sys_country` VALUES ('227', '227', '1', 'Uruguay', 'UY');
INSERT INTO `#__sdi_sys_country` VALUES ('228', '228', '1', 'USA', 'US');
INSERT INTO `#__sdi_sys_country` VALUES ('229', '229', '1', 'Vanuatu', 'VU');
INSERT INTO `#__sdi_sys_country` VALUES ('230', '230', '1', 'Vatican', 'VA');
INSERT INTO `#__sdi_sys_country` VALUES ('231', '231', '1', 'Vénézuéla', 'VE');
INSERT INTO `#__sdi_sys_country` VALUES ('232', '232', '1', 'Viêt Nam', 'VN');
INSERT INTO `#__sdi_sys_country` VALUES ('233', '233', '1', 'Wallis, Futuna', 'WF');
INSERT INTO `#__sdi_sys_country` VALUES ('234', '234', '1', 'Yémen', 'YE');
INSERT INTO `#__sdi_sys_country` VALUES ('235', '235', '1', 'Yougoslavie', 'YU');
INSERT INTO `#__sdi_sys_country` VALUES ('236', '236', '1', 'Zaïre', 'ZR');
INSERT INTO `#__sdi_sys_country` VALUES ('237', '237', '1', 'Zambie', 'ZM');
INSERT INTO `#__sdi_sys_country` VALUES ('238', '238', '1', 'Zimbabwe', 'ZW');
INSERT INTO `#__sdi_sys_country` VALUES ('239', '239', '1', 'Non Connu', 'UNK');

CREATE TABLE IF NOT EXISTS `#__sdi_sys_versiontype` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;

INSERT INTO `#__sdi_sys_versiontype` (ordering,state,value) 
VALUES 
(1,1,'all'),
(2,1,'lastPublishedVersion')
;

CREATE TABLE IF NOT EXISTS `#__sdi_sys_accessscope` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`ordering` INT(11)  NOT NULL DEFAULT '1' ,
`state` TINYINT(1)  NOT NULL DEFAULT '1',
`value` VARCHAR(150)  NOT NULL ,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


INSERT INTO `#__sdi_sys_accessscope` (ordering,state,value) 
VALUES 
(1,1,'public'),
(2,1,'organism'),
(3,1,'user')
;
