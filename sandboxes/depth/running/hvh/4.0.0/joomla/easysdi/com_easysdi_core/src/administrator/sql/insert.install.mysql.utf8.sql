INSERT INTO `#__sdi_sys_unit` (ordering,state,alias,name) 
VALUES 
(1,1,'m','meter'),
(2,1,'dd','degree')
;

INSERT INTO `#__sdi_sys_role` VALUES ('1','1','1','resourcemanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('2','2','1','metadatamanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('3','3','1','metadataeditor' );
INSERT INTO `#__sdi_sys_role` VALUES ('4','4','1','productmanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('5','5','1','previewmanager' );
INSERT INTO `#__sdi_sys_role` VALUES ('6','6','1','member' );

INSERT INTO `#__sdi_sys_versiontype` (ordering,state,value) 
VALUES 
(1,1,'all'),
(2,1,'lastPublishedVersion')
;

INSERT INTO `#__sdi_sys_servicescope` (ordering,state,value) 
VALUES 
(1,1,'all'),
(2,1,'organism'),
(3,1,'none')
;

INSERT INTO `#__sdi_sys_metadatastate` (ordering,state,value) 
VALUES 
(1,1,'archived'),
(2,1,'inprogress'),
(3,1,'published'),
(3,1,'trashed'),
(3,1,'validated')
;

INSERT INTO `#__sdi_sys_spatialoperator` (ordering,state,value) 
VALUES 
(1,1,'within'),
(3,1,'touch')
;

INSERT INTO `#__sdi_sys_accessscope` (ordering,state,value) 
VALUES 
(1,1,'public'),
(2,1,'organism'),
(3,1,'user')
;

INSERT INTO `#__sdi_sys_authenticationlevel` (ordering,state,value) 
VALUES 
(1,1,'resource'),
(2,1,'service')
;

INSERT INTO `#__sdi_sys_authenticationconnector` (ordering,state,authenticationlevel_id,value) 
VALUES 
(1,1,1,'HTTPBasic'),
(2,1,2,'Geonetwork')
;

INSERT INTO `#__sdi_sys_serviceversion` (ordering,state,value) 
VALUES 
(1,1,'1.0.0'),
(2,1,'1.1.0'),
(3,1,'1.1.1'),
(4,1,'1.3.0'),
(5,1,'2.0'),
(6,1,'2.0.0'),
(7,1,'2.0.1'),
(8,1,'2.0.2')
;

INSERT INTO `#__sdi_sys_serviceconnector` (ordering,state,value) 
VALUES 
(1,1,'CSW'),
(2,1,'WMS'),
(3,1,'WMTS'),
(4,1,'WFS'),
(5,0,'WCS'),
(6,0,'WCPS'),
(7,0,'SOS'),
(8,0,'SPS'),
(9,0,'WPS'),
(10,0,'OLS')
;

INSERT INTO `#__sdi_sys_servicecon_authenticationcon` (serviceconnector_id,authenticationconnector_id) 
VALUES 
(1,1),
(1,2),
(2,1),
(3,1),
(4,1),
(5,1),
(6,1),
(7,1),
(8,1),
(9,1),
(10,1)
;

INSERT INTO `#__sdi_sys_servicecompliance` (ordering,state,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(1,1,1,7,1,1,0,1),
(2,1,1,8,1,1,0,1),
(3,1,2,2,1,1,1,0),
(4,1,2,3,1,1,1,0),
(5,1,2,4,1,1,1,0),
(6,1,3,1,1,1,0,0),
(7,1,4,1,1,1,1,0)
;

INSERT INTO `#__sdi_sys_serviceoperation` (ordering,state,value) 
VALUES 
(1,1,'GetCapabilities'),
(2,1,'GetRecords'),
(3,1,'GetRecordById'),
(4,1,'DescribeRecord'),
(5,1,'TransactionInsert'),
(6,1,'TransactionUpdate'),
(7,1,'TransactionReplace'),
(8,1,'TransactionDelete'),
(9,1,'GetDomain'),
(10,1,'Harvest'),
(11,1,'GetTile'),
(12,1,'GetFeatureInfo'),
(13,1,'DescribeFeatureType'),
(14,1,'GetFeature'),
(15,1,'LockFeature'),
(16,1,'GetFeatureWithLock'),
(17,1,'GetMap'),
(18,1,'GetLegendGraphic'),
(19,1,'DescribeLayer'),
(20,1,'GetStyles'),
(21,1,'PutStyles')
;

INSERT INTO `#__sdi_sys_operationcompliance` (ordering,state,servicecompliance_id,serviceoperation_id,implemented) 
VALUES 
(1,1,1,1,1),
(2,1,1,2,1),
(3,1,1,3,1),
(4,1,1,4,1),
(5,1,1,5,0),
(6,1,1,6,0),
(7,1,1,7,0),
(8,1,1,8,0),
(9,1,1,9,0),
(10,1,1,10,0),
(11,1,2,1,1),
(12,1,2,2,1),
(13,1,2,3,1),
(14,1,2,4,1),
(15,1,2,5,0),
(16,1,2,6,0),
(17,1,2,7,0),
(18,1,2,8,0),
(19,1,2,9,0),
(20,1,2,10,0),
(21,1,6,1,1),
(22,1,6,11,1),
(23,1,6,12,1),
(24,1,7,13,1),
(25,1,7,14,1),
(26,1,7,15,0),
(27,1,7,1,1),
(28,1,7,16,0),
(29,1,3,17,1),
(30,1,3,18,1),
(31,1,3,19,0),
(32,1,3,20,0),
(33,1,3,21,0),
(34,1,3,1,1),
(35,1,4,17,1),
(36,1,4,18,1),
(37,1,4,19,0),
(38,1,4,20,0),
(39,1,4,21,0),
(40,1,4,1,1),
(41,1,5,17,1),
(42,1,5,18,1),
(43,1,5,19,0),
(44,1,5,20,0),
(45,1,5,21,0),
(46,1,5,1,1),
(47,1,3,12,1),
(48,1,4,12,1),
(49,1,5,12,1)
;

INSERT INTO `#__sdi_sys_serviceconnector` (ordering,state,value) 
VALUES 
(11,1,'WMSC'),
(12,0,'Bing'),
(13,0,'Google'),
(14,0,'OSM')
;

INSERT INTO `#__sdi_sys_servicecompliance` (ordering,state,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(8,1,11,2,1,1,1,0),
(9,1,11,3,1,1,1,0),
(10,1,11,4,1,1,1,0)
;

INSERT INTO `#__sdi_sys_exceptionlevel` (ordering, state, value )
VALUES
(1,1,'permissive'),
(2,1,'restrictive')
;

INSERT INTO `#__sdi_sys_proxytype` (ordering, state, value )
VALUES
(1,1,'harvest'),
(2,1,'relay'),
(3,1,'aggregate');

INSERT INTO `#__sdi_sys_loglevel` (ordering, state, value )
VALUES
(1,1,'off'),
(2,1,'fatal'),
(3,1,'error'),
(4,1,'warning'),
(5,1,'info'),
(6,1,'debug'),
(7,1,'trace'),
(8,1,'All')
;

INSERT INTO `#__sdi_sys_logroll` (ordering, state, value )
VALUES
(1,1,'daily'),
(2,1,'weekly'),
(3,1,'monthly'),
(4,1,'annually')
;

INSERT INTO `#__sdi_sys_metadataversion` VALUES ('1','1','1','all' );
INSERT INTO `#__sdi_sys_metadataversion` VALUES ('2','2','1','last' );

INSERT INTO `#__sdi_sys_country` VALUES ('1', '1', '1', 'Afghanistan', 'AF', 'AFG');
INSERT INTO `#__sdi_sys_country` VALUES ('2', '2', '1', 'Åland Islands', 'AX', 'ALA');
INSERT INTO `#__sdi_sys_country` VALUES ('3', '3', '1', 'Albania', 'AL', 'ALB');
INSERT INTO `#__sdi_sys_country` VALUES ('4', '4', '1', 'Algeria (El Djazaïr)', 'DZ', 'DZA');
INSERT INTO `#__sdi_sys_country` VALUES ('5', '5', '1', 'American Samoa', 'AS', 'ASM');
INSERT INTO `#__sdi_sys_country` VALUES ('6', '6', '1', 'Andorra', 'AD', 'AND');
INSERT INTO `#__sdi_sys_country` VALUES ('7', '7', '1', 'Angola', 'AO', 'AGO');
INSERT INTO `#__sdi_sys_country` VALUES ('8', '8', '1', 'Anguilla', 'AI', 'AIA');
INSERT INTO `#__sdi_sys_country` VALUES ('9', '9', '1', 'Antarctica', 'AQ', 'ATA');
INSERT INTO `#__sdi_sys_country` VALUES ('10', '10', '1', 'Antigua and Barbuda', 'AG', 'ATG');
INSERT INTO `#__sdi_sys_country` VALUES ('11', '11', '1', 'Argentina', 'AR', 'ARG');
INSERT INTO `#__sdi_sys_country` VALUES ('12', '12', '1', 'Armenia', 'AM', 'ARM');
INSERT INTO `#__sdi_sys_country` VALUES ('13', '13', '1', 'Aruba', 'AW', 'ABW');
INSERT INTO `#__sdi_sys_country` VALUES ('14', '14', '1', 'Australia', 'AU', 'AUS');
INSERT INTO `#__sdi_sys_country` VALUES ('15', '15', '1', 'Austria', 'AT', 'AUT');
INSERT INTO `#__sdi_sys_country` VALUES ('16', '16', '1', 'Azerbaijan', 'AZ', 'AZE');
INSERT INTO `#__sdi_sys_country` VALUES ('17', '17', '1', 'Bahamas', 'BS', 'BHS');
INSERT INTO `#__sdi_sys_country` VALUES ('18', '18', '1', 'Bahrain', 'BH', 'BHR');
INSERT INTO `#__sdi_sys_country` VALUES ('19', '19', '1', 'Bangladesh', 'BD', 'BGD');
INSERT INTO `#__sdi_sys_country` VALUES ('20', '20', '1', 'Barbados', 'BB', 'BRB');
INSERT INTO `#__sdi_sys_country` VALUES ('21', '21', '1', 'Belarus', 'BY', 'BLR');
INSERT INTO `#__sdi_sys_country` VALUES ('22', '22', '1', 'Belgium', 'BE', 'BEL');
INSERT INTO `#__sdi_sys_country` VALUES ('23', '23', '1', 'Belize', 'BZ', 'BLZ');
INSERT INTO `#__sdi_sys_country` VALUES ('24', '24', '1', 'Benin', 'BJ', 'BEN');
INSERT INTO `#__sdi_sys_country` VALUES ('25', '25', '1', 'Bermuda', 'BM', 'BMU');
INSERT INTO `#__sdi_sys_country` VALUES ('26', '26', '1', 'Bhutan', 'BT', 'BTN');
INSERT INTO `#__sdi_sys_country` VALUES ('27', '27', '1', 'Bolivia', 'BO', 'BOL');
INSERT INTO `#__sdi_sys_country` VALUES ('28', '28', '1', 'Bosnia and Herzegovina', 'BA', 'BIH');
INSERT INTO `#__sdi_sys_country` VALUES ('29', '29', '1', 'Botswana', 'BW', 'BWA');
INSERT INTO `#__sdi_sys_country` VALUES ('30', '30', '1', 'Bouvet Island', 'BV', 'BVT');
INSERT INTO `#__sdi_sys_country` VALUES ('31', '31', '1', 'Brazil', 'BR', 'BRA');
INSERT INTO `#__sdi_sys_country` VALUES ('32', '32', '1', 'British Indian Ocean Territory', 'IO', 'IOT');
INSERT INTO `#__sdi_sys_country` VALUES ('33', '33', '1', 'Brunei Darussalam', 'BN', 'BRN');
INSERT INTO `#__sdi_sys_country` VALUES ('34', '34', '1', 'Bulgaria', 'BG', 'BGR');
INSERT INTO `#__sdi_sys_country` VALUES ('35', '35', '1', 'Burkina Faso', 'BF', 'BFA');
INSERT INTO `#__sdi_sys_country` VALUES ('36', '36', '1', 'Burundi', 'BI', 'BDI');
INSERT INTO `#__sdi_sys_country` VALUES ('37', '37', '1', 'Cambodia', 'KH', 'KHM');
INSERT INTO `#__sdi_sys_country` VALUES ('38', '38', '1', 'Cameroon', 'CM', 'CMR');
INSERT INTO `#__sdi_sys_country` VALUES ('39', '39', '1', 'Canada', 'CA', 'CAN');
INSERT INTO `#__sdi_sys_country` VALUES ('40', '40', '1', 'Cape Verde', 'CV', 'CPV');
INSERT INTO `#__sdi_sys_country` VALUES ('41', '41', '1', 'Cayman Islands', 'KY', 'CYM');
INSERT INTO `#__sdi_sys_country` VALUES ('42', '42', '1', 'Central African Republic', 'CF', 'CAF');
INSERT INTO `#__sdi_sys_country` VALUES ('43', '43', '1', 'Chad (T\'Chad)', 'TD', 'TCD');
INSERT INTO `#__sdi_sys_country` VALUES ('44', '44', '1', 'Chile', 'CL', 'CHL');
INSERT INTO `#__sdi_sys_country` VALUES ('45', '45', '1', 'China', 'CN', 'CHN');
INSERT INTO `#__sdi_sys_country` VALUES ('46', '46', '1', 'Christmas Island', 'CX', 'CXR');
INSERT INTO `#__sdi_sys_country` VALUES ('47', '47', '1', 'Cocos (Keeling) Islands', 'CC', 'CCK');
INSERT INTO `#__sdi_sys_country` VALUES ('48', '48', '1', 'Colombia', 'CO', 'COL');
INSERT INTO `#__sdi_sys_country` VALUES ('49', '49', '1', 'Comoros', 'KM', 'COM');
INSERT INTO `#__sdi_sys_country` VALUES ('50', '50', '1', 'Congo, Republic Of', 'CG', 'COG');
INSERT INTO `#__sdi_sys_country` VALUES ('51', '51', '1', 'Congo, The Democratic Republic of the (formerly Zaire)', 'CD', 'COD');
INSERT INTO `#__sdi_sys_country` VALUES ('52', '52', '1', 'Cook Islands', 'CK', 'COK');
INSERT INTO `#__sdi_sys_country` VALUES ('53', '53', '1', 'Costa Rica', 'CR', 'CRI');
INSERT INTO `#__sdi_sys_country` VALUES ('54', '54', '1', 'CÔte D\'Ivoire (Ivory Coast)', 'CI', 'CIV');
INSERT INTO `#__sdi_sys_country` VALUES ('55', '55', '1', 'Croatia (hrvatska)', 'HR', 'HRV');
INSERT INTO `#__sdi_sys_country` VALUES ('56', '56', '1', 'Cuba', 'CU', 'CUB');
INSERT INTO `#__sdi_sys_country` VALUES ('57', '57', '1', 'Cyprus', 'CY', 'CYP');
INSERT INTO `#__sdi_sys_country` VALUES ('58', '58', '1', 'Czech Republic', 'CZ', 'CZE');
INSERT INTO `#__sdi_sys_country` VALUES ('59', '59', '1', 'Denmark', 'DK', 'DNK');
INSERT INTO `#__sdi_sys_country` VALUES ('60', '60', '1', 'Djibouti', 'DJ', 'DJI');
INSERT INTO `#__sdi_sys_country` VALUES ('61', '61', '1', 'Dominica', 'DM', 'DMA');
INSERT INTO `#__sdi_sys_country` VALUES ('62', '62', '1', 'Dominican Republic', 'DO', 'DOM');
INSERT INTO `#__sdi_sys_country` VALUES ('63', '63', '1', 'Ecuador', 'EC', 'ECU');
INSERT INTO `#__sdi_sys_country` VALUES ('64', '64', '1', 'Egypt', 'EG', 'EGY');
INSERT INTO `#__sdi_sys_country` VALUES ('65', '65', '1', 'El Salvador', 'SV', 'SLV');
INSERT INTO `#__sdi_sys_country` VALUES ('66', '66', '1', 'Equatorial Guinea', 'GQ', 'GNQ');
INSERT INTO `#__sdi_sys_country` VALUES ('67', '67', '1', 'Eritrea', 'ER', 'ERI');
INSERT INTO `#__sdi_sys_country` VALUES ('68', '68', '1', 'Estonia', 'EE', 'EST');
INSERT INTO `#__sdi_sys_country` VALUES ('69', '69', '1', 'Ethiopia', 'ET', 'ETH');
INSERT INTO `#__sdi_sys_country` VALUES ('70', '70', '1', 'Faeroe Islands', 'FO', 'FRO');
INSERT INTO `#__sdi_sys_country` VALUES ('71', '71', '1', 'Falkland Islands (Malvinas)', 'FK', 'FLK');
INSERT INTO `#__sdi_sys_country` VALUES ('72', '72', '1', 'Fiji', 'FJ', 'FJI');
INSERT INTO `#__sdi_sys_country` VALUES ('73', '73', '1', 'Finland', 'FI', 'FIN');
INSERT INTO `#__sdi_sys_country` VALUES ('74', '74', '1', 'France', 'FR', 'FRA');
INSERT INTO `#__sdi_sys_country` VALUES ('75', '75', '1', 'French Guiana', 'GF', 'GUF');
INSERT INTO `#__sdi_sys_country` VALUES ('76', '76', '1', 'French Polynesia', 'PF', 'PYF');
INSERT INTO `#__sdi_sys_country` VALUES ('77', '77', '1', 'French Southern Territories', 'TF', 'ATF');
INSERT INTO `#__sdi_sys_country` VALUES ('78', '78', '1', 'Gabon', 'GA', 'GAB');
INSERT INTO `#__sdi_sys_country` VALUES ('79', '79', '1', 'Gambia, The', 'GM', 'GMB');
INSERT INTO `#__sdi_sys_country` VALUES ('80', '80', '1', 'Georgia', 'GE', 'GEO');
INSERT INTO `#__sdi_sys_country` VALUES ('81', '81', '1', 'Germany (Deutschland)', 'DE', 'DEU');
INSERT INTO `#__sdi_sys_country` VALUES ('82', '82', '1', 'Ghana', 'GH', 'GHA');
INSERT INTO `#__sdi_sys_country` VALUES ('83', '83', '1', 'Gibraltar', 'GI', 'GIB');
INSERT INTO `#__sdi_sys_country` VALUES ('84', '84', '1', 'Great Britain', 'GB', 'GBR');
INSERT INTO `#__sdi_sys_country` VALUES ('85', '85', '1', 'Greece', 'GR', 'GRC');
INSERT INTO `#__sdi_sys_country` VALUES ('86', '86', '1', 'Greenland', 'GL', 'GRL');
INSERT INTO `#__sdi_sys_country` VALUES ('87', '87', '1', 'Grenada', 'GD', 'GRD');
INSERT INTO `#__sdi_sys_country` VALUES ('88', '88', '1', 'Guadeloupe', 'GP', 'GLP');
INSERT INTO `#__sdi_sys_country` VALUES ('89', '89', '1', 'Guam', 'GU', 'GUM');
INSERT INTO `#__sdi_sys_country` VALUES ('90', '90', '1', 'Guatemala', 'GT', 'GTM');
INSERT INTO `#__sdi_sys_country` VALUES ('91', '91', '1', 'Guinea', 'GN', 'GIN');
INSERT INTO `#__sdi_sys_country` VALUES ('92', '92', '1', 'Guinea-bissau', 'GW', 'GNB');
INSERT INTO `#__sdi_sys_country` VALUES ('93', '93', '1', 'Guyana', 'GY', 'GUY');
INSERT INTO `#__sdi_sys_country` VALUES ('94', '94', '1', 'Haiti', 'HT', 'HTI');
INSERT INTO `#__sdi_sys_country` VALUES ('95', '95', '1', 'Heard Island and Mcdonald Islands', 'HM', 'HMD');
INSERT INTO `#__sdi_sys_country` VALUES ('96', '96', '1', 'Honduras', 'HN', 'HND');
INSERT INTO `#__sdi_sys_country` VALUES ('97', '97', '1', 'Hong Kong (Special Administrative Region of China)', 'HK', 'HKG');
INSERT INTO `#__sdi_sys_country` VALUES ('98', '98', '1', 'Hungary', 'HU', 'HUN');
INSERT INTO `#__sdi_sys_country` VALUES ('99', '99', '1', 'Iceland', 'IS', 'ISL');
INSERT INTO `#__sdi_sys_country` VALUES ('100', '100', '1', 'India', 'IN', 'IND');
INSERT INTO `#__sdi_sys_country` VALUES ('101', '101', '1', 'Indonesia', 'ID', 'IDN');
INSERT INTO `#__sdi_sys_country` VALUES ('102', '102', '1', 'Iran (Islamic Republic of Iran)', 'IR', 'IRN');
INSERT INTO `#__sdi_sys_country` VALUES ('103', '103', '1', 'Iraq', 'IQ', 'IRQ');
INSERT INTO `#__sdi_sys_country` VALUES ('104', '104', '1', 'Ireland', 'IE', 'IRL');
INSERT INTO `#__sdi_sys_country` VALUES ('105', '105', '1', 'Israel', 'IL', 'ISR');
INSERT INTO `#__sdi_sys_country` VALUES ('106', '106', '1', 'Italy', 'IT', 'ITA');
INSERT INTO `#__sdi_sys_country` VALUES ('107', '107', '1', 'Jamaica', 'JM', 'JAM');
INSERT INTO `#__sdi_sys_country` VALUES ('108', '108', '1', 'Japan', 'JP', 'JPN');
INSERT INTO `#__sdi_sys_country` VALUES ('109', '109', '1', 'Jordan (Hashemite Kingdom of Jordan)', 'JO', 'JOR');
INSERT INTO `#__sdi_sys_country` VALUES ('110', '110', '1', 'Kazakhstan', 'KZ', 'KAZ');
INSERT INTO `#__sdi_sys_country` VALUES ('111', '111', '1', 'Kenya', 'KE', 'KEN');
INSERT INTO `#__sdi_sys_country` VALUES ('112', '112', '1', 'Kiribati', 'KI', 'KIR');
INSERT INTO `#__sdi_sys_country` VALUES ('113', '113', '1', 'Korea (Democratic Peoples Republic pf [North] Korea)', 'KP', 'PRK');
INSERT INTO `#__sdi_sys_country` VALUES ('114', '114', '1', 'Korea (Republic of [South] Korea)', 'KR', 'KOR');
INSERT INTO `#__sdi_sys_country` VALUES ('115', '115', '1', 'Kuwait', 'KW', 'KWT');
INSERT INTO `#__sdi_sys_country` VALUES ('116', '116', '1', 'Kyrgyzstan', 'KG', 'KGZ');
INSERT INTO `#__sdi_sys_country` VALUES ('117', '117', '1', 'Lao People\'s Democratic Republic', 'LA', 'LAO');
INSERT INTO `#__sdi_sys_country` VALUES ('118', '118', '1', 'Latvia', 'LV', 'LVA');
INSERT INTO `#__sdi_sys_country` VALUES ('119', '119', '1', 'Lebanon', 'LB', 'LBN');
INSERT INTO `#__sdi_sys_country` VALUES ('120', '120', '1', 'Lesotho', 'LS', 'LSO');
INSERT INTO `#__sdi_sys_country` VALUES ('121', '121', '1', 'Liberia', 'LR', 'LBR');
INSERT INTO `#__sdi_sys_country` VALUES ('122', '122', '1', 'Libya (Libyan Arab Jamahirya)', 'LY', 'LBY');
INSERT INTO `#__sdi_sys_country` VALUES ('123', '123', '1', 'Liechtenstein (Fürstentum Liechtenstein)', 'LI', 'LIE');
INSERT INTO `#__sdi_sys_country` VALUES ('124', '124', '1', 'Lithuania', 'LT', 'LTU');
INSERT INTO `#__sdi_sys_country` VALUES ('125', '125', '1', 'Luxembourg', 'LU', 'LUX');
INSERT INTO `#__sdi_sys_country` VALUES ('126', '126', '1', 'Macao (Special Administrative Region of China)', 'MO', 'MAC');
INSERT INTO `#__sdi_sys_country` VALUES ('127', '127', '1', 'Macedonia (Former Yugoslav Republic of Macedonia)', 'MK', 'MKD');
INSERT INTO `#__sdi_sys_country` VALUES ('128', '128', '1', 'Madagascar', 'MG', 'MDG');
INSERT INTO `#__sdi_sys_country` VALUES ('129', '129', '1', 'Malawi', 'MW', 'MWI');
INSERT INTO `#__sdi_sys_country` VALUES ('130', '130', '1', 'Malaysia', 'MY', 'MYS');
INSERT INTO `#__sdi_sys_country` VALUES ('131', '131', '1', 'Maldives', 'MV', 'MDV');
INSERT INTO `#__sdi_sys_country` VALUES ('132', '132', '1', 'Mali', 'ML', 'MLI');
INSERT INTO `#__sdi_sys_country` VALUES ('133', '133', '1', 'Malta', 'MT', 'MLT');
INSERT INTO `#__sdi_sys_country` VALUES ('134', '134', '1', 'Marshall Islands', 'MH', 'MHL');
INSERT INTO `#__sdi_sys_country` VALUES ('135', '135', '1', 'Martinique', 'MQ', 'MTQ');
INSERT INTO `#__sdi_sys_country` VALUES ('136', '136', '1', 'Mauritania', 'MR', 'MRT');
INSERT INTO `#__sdi_sys_country` VALUES ('137', '137', '1', 'Mauritius', 'MU', 'MUS');
INSERT INTO `#__sdi_sys_country` VALUES ('138', '138', '1', 'Mayotte', 'YT', 'MYT');
INSERT INTO `#__sdi_sys_country` VALUES ('139', '139', '1', 'Mexico', 'MX', 'MEX');
INSERT INTO `#__sdi_sys_country` VALUES ('140', '140', '1', 'Micronesia (Federated States of Micronesia)', 'FM', 'FSM');
INSERT INTO `#__sdi_sys_country` VALUES ('141', '141', '1', 'Moldova', 'MD', 'MDA');
INSERT INTO `#__sdi_sys_country` VALUES ('142', '142', '1', 'Monaco', 'MC', 'MCO');
INSERT INTO `#__sdi_sys_country` VALUES ('143', '143', '1', 'Mongolia', 'MN', 'MNG');
INSERT INTO `#__sdi_sys_country` VALUES ('144', '144', '1', 'Montserrat', 'MS', 'MSR');
INSERT INTO `#__sdi_sys_country` VALUES ('145', '145', '1', 'Morocco', 'MA', 'MAR');
INSERT INTO `#__sdi_sys_country` VALUES ('146', '146', '1', 'Mozambique (Moçambique)', 'MZ', 'MOZ');
INSERT INTO `#__sdi_sys_country` VALUES ('147', '147', '1', 'Myanmar (formerly Burma)', 'MM', 'MMR');
INSERT INTO `#__sdi_sys_country` VALUES ('148', '148', '1', 'Namibia', 'NA', 'NAM');
INSERT INTO `#__sdi_sys_country` VALUES ('149', '149', '1', 'Nauru', 'NR', 'NRU');
INSERT INTO `#__sdi_sys_country` VALUES ('150', '150', '1', 'Nepal', 'NP', 'NPL');
INSERT INTO `#__sdi_sys_country` VALUES ('151', '151', '1', 'Netherlands', 'NL', 'NLD');
INSERT INTO `#__sdi_sys_country` VALUES ('152', '152', '1', 'Netherlands Antilles', 'AN', 'ANT');
INSERT INTO `#__sdi_sys_country` VALUES ('153', '153', '1', 'New Caledonia', 'NC', 'NCL');
INSERT INTO `#__sdi_sys_country` VALUES ('154', '154', '1', 'New Zealand', 'NZ', 'NZL');
INSERT INTO `#__sdi_sys_country` VALUES ('155', '155', '1', 'Nicaragua', 'NI', 'NIC');
INSERT INTO `#__sdi_sys_country` VALUES ('156', '156', '1', 'Niger', 'NE', 'NER');
INSERT INTO `#__sdi_sys_country` VALUES ('157', '157', '1', 'Nigeria', 'NG', 'NGA');
INSERT INTO `#__sdi_sys_country` VALUES ('158', '158', '1', 'Niue', 'NU', 'NIU');
INSERT INTO `#__sdi_sys_country` VALUES ('159', '159', '1', 'Norfolk Island', 'NF', 'NFK');
INSERT INTO `#__sdi_sys_country` VALUES ('160', '160', '1', 'Northern Mariana Islands', 'MP', 'MNP');
INSERT INTO `#__sdi_sys_country` VALUES ('161', '161', '1', 'Norway', 'NO', 'NOR');
INSERT INTO `#__sdi_sys_country` VALUES ('162', '162', '1', 'Oman', 'OM', 'OMN');
INSERT INTO `#__sdi_sys_country` VALUES ('163', '163', '1', 'Pakistan', 'PK', 'PAK');
INSERT INTO `#__sdi_sys_country` VALUES ('164', '164', '1', 'Palau', 'PW', 'PLW');
INSERT INTO `#__sdi_sys_country` VALUES ('165', '165', '1', 'Palestinian Territories', 'PS', 'PSE');
INSERT INTO `#__sdi_sys_country` VALUES ('166', '166', '1', 'Panama', 'PA', 'PAN');
INSERT INTO `#__sdi_sys_country` VALUES ('167', '167', '1', 'Papua New Guinea', 'PG', 'PNG');
INSERT INTO `#__sdi_sys_country` VALUES ('168', '168', '1', 'Paraguay', 'PY', 'PRY');
INSERT INTO `#__sdi_sys_country` VALUES ('169', '169', '1', 'Peru', 'PE', 'PER');
INSERT INTO `#__sdi_sys_country` VALUES ('170', '170', '1', 'Philippines', 'PH', 'PHL');
INSERT INTO `#__sdi_sys_country` VALUES ('171', '171', '1', 'Pitcairn', 'PN', 'PCN');
INSERT INTO `#__sdi_sys_country` VALUES ('172', '172', '1', 'Poland', 'PL', 'POL');
INSERT INTO `#__sdi_sys_country` VALUES ('173', '173', '1', 'Portugal', 'PT', 'PRT');
INSERT INTO `#__sdi_sys_country` VALUES ('174', '174', '1', 'Puerto Rico', 'PR', 'PRI');
INSERT INTO `#__sdi_sys_country` VALUES ('175', '175', '1', 'Qatar', 'QA', 'QAT');
INSERT INTO `#__sdi_sys_country` VALUES ('176', '176', '1', 'RÉunion', 'RE', 'REU');
INSERT INTO `#__sdi_sys_country` VALUES ('177', '177', '1', 'Romania', 'RO', 'ROU');
INSERT INTO `#__sdi_sys_country` VALUES ('178', '178', '1', 'Russian Federation', 'RU', 'RUS');
INSERT INTO `#__sdi_sys_country` VALUES ('179', '179', '1', 'Rwanda', 'RW', 'RWA');
INSERT INTO `#__sdi_sys_country` VALUES ('180', '180', '1', 'Saint Helena', 'SH', 'SHN');
INSERT INTO `#__sdi_sys_country` VALUES ('181', '181', '1', 'Saint Kitts and Nevis', 'KN', 'KNA');
INSERT INTO `#__sdi_sys_country` VALUES ('182', '182', '1', 'Saint Lucia', 'LC', 'LCA');
INSERT INTO `#__sdi_sys_country` VALUES ('183', '183', '1', 'Saint Pierre and Miquelon', 'PM', 'SPM');
INSERT INTO `#__sdi_sys_country` VALUES ('184', '184', '1', 'Saint Vincent and the Grenadines', 'VC', 'VCT');
INSERT INTO `#__sdi_sys_country` VALUES ('185', '185', '1', 'Samoa (formerly Western Samoa)', 'WS', 'WSM');
INSERT INTO `#__sdi_sys_country` VALUES ('186', '186', '1', 'San Marino (Republic of)', 'SM', 'SMR');
INSERT INTO `#__sdi_sys_country` VALUES ('187', '187', '1', 'Sao Tome and Principe', 'ST', 'STP');
INSERT INTO `#__sdi_sys_country` VALUES ('188', '188', '1', 'Saudi Arabia (Kingdom of Saudi Arabia)', 'SA', 'SAU');
INSERT INTO `#__sdi_sys_country` VALUES ('189', '189', '1', 'Senegal', 'SN', 'SEN');
INSERT INTO `#__sdi_sys_country` VALUES ('190', '190', '1', 'Serbia and Montenegro (formerly Yugoslavia)', 'CS', 'SCG');
INSERT INTO `#__sdi_sys_country` VALUES ('191', '191', '1', 'Seychelles', 'SC', 'SYC');
INSERT INTO `#__sdi_sys_country` VALUES ('192', '192', '1', 'Sierra Leone', 'SL', 'SLE');
INSERT INTO `#__sdi_sys_country` VALUES ('193', '193', '1', 'Singapore', 'SG', 'SGP');
INSERT INTO `#__sdi_sys_country` VALUES ('194', '194', '1', 'Slovakia (Slovak Republic)', 'SK', 'SVK');
INSERT INTO `#__sdi_sys_country` VALUES ('195', '195', '1', 'Slovenia', 'SI', 'SVN');
INSERT INTO `#__sdi_sys_country` VALUES ('196', '196', '1', 'Solomon Islands', 'SB', 'SLB');
INSERT INTO `#__sdi_sys_country` VALUES ('197', '197', '1', 'Somalia', 'SO', 'SOM');
INSERT INTO `#__sdi_sys_country` VALUES ('198', '198', '1', 'South Africa (zuid Afrika)', 'ZA', 'ZAF');
INSERT INTO `#__sdi_sys_country` VALUES ('199', '199', '1', 'South Georgia and the South Sandwich Islands', 'GS', 'SGS');
INSERT INTO `#__sdi_sys_country` VALUES ('200', '200', '1', 'Spain (españa)', 'ES', 'ESP');
INSERT INTO `#__sdi_sys_country` VALUES ('201', '201', '1', 'Sri Lanka', 'LK', 'LKA');
INSERT INTO `#__sdi_sys_country` VALUES ('202', '202', '1', 'Sudan', 'SD', 'SDN');
INSERT INTO `#__sdi_sys_country` VALUES ('203', '203', '1', 'Suriname', 'SR', 'SUR');
INSERT INTO `#__sdi_sys_country` VALUES ('204', '204', '1', 'Svalbard and Jan Mayen', 'SJ', 'SJM');
INSERT INTO `#__sdi_sys_country` VALUES ('205', '205', '1', 'Swaziland', 'SZ', 'SWZ');
INSERT INTO `#__sdi_sys_country` VALUES ('206', '206', '1', 'Sweden', 'SE', 'SWE');
INSERT INTO `#__sdi_sys_country` VALUES ('207', '207', '1', 'Switzerland (Confederation of Helvetia)', 'CH', 'CHE');
INSERT INTO `#__sdi_sys_country` VALUES ('208', '208', '1', 'Syrian Arab Republic', 'SY', 'SYR');
INSERT INTO `#__sdi_sys_country` VALUES ('209', '209', '1', 'Taiwan (\"Chinese Taipei\" for IOC)', 'TW', 'TWN');
INSERT INTO `#__sdi_sys_country` VALUES ('210', '210', '1', 'Tajikistan', 'TJ', 'TJK');
INSERT INTO `#__sdi_sys_country` VALUES ('211', '211', '1', 'Tanzania', 'TZ', 'TZA');
INSERT INTO `#__sdi_sys_country` VALUES ('212', '212', '1', 'Thailand', 'TH', 'THA');
INSERT INTO `#__sdi_sys_country` VALUES ('213', '213', '1', 'Timor-Leste (formerly East Timor)', 'TL', 'TLS');
INSERT INTO `#__sdi_sys_country` VALUES ('214', '214', '1', 'Togo', 'TG', 'TGO');
INSERT INTO `#__sdi_sys_country` VALUES ('215', '215', '1', 'Tokelau', 'TK', 'TKL');
INSERT INTO `#__sdi_sys_country` VALUES ('216', '216', '1', 'Tonga', 'TO', 'TON');
INSERT INTO `#__sdi_sys_country` VALUES ('217', '217', '1', 'Trinidad and Tobago', 'TT', 'TTO');
INSERT INTO `#__sdi_sys_country` VALUES ('218', '218', '1', 'Tunisia', 'TN', 'TUN');
INSERT INTO `#__sdi_sys_country` VALUES ('219', '219', '1', 'Turkey', 'TR', 'TUR');
INSERT INTO `#__sdi_sys_country` VALUES ('220', '220', '1', 'Turkmenistan', 'TM', 'TKM');
INSERT INTO `#__sdi_sys_country` VALUES ('221', '221', '1', 'Turks and Caicos Islands', 'TC', 'TCA');
INSERT INTO `#__sdi_sys_country` VALUES ('222', '222', '1', 'Tuvalu', 'TV', 'TUV');
INSERT INTO `#__sdi_sys_country` VALUES ('223', '223', '1', 'Uganda', 'UG', 'UGA');
INSERT INTO `#__sdi_sys_country` VALUES ('224', '224', '1', 'Ukraine', 'UA', 'UKR');
INSERT INTO `#__sdi_sys_country` VALUES ('225', '225', '1', 'United Arab Emirates', 'AE', 'ARE');
INSERT INTO `#__sdi_sys_country` VALUES ('226', '226', '1', 'United Kingdom (Great Britain)', 'GB', 'GBR');
INSERT INTO `#__sdi_sys_country` VALUES ('227', '227', '1', 'United States', 'US', 'USA');
INSERT INTO `#__sdi_sys_country` VALUES ('228', '228', '1', 'United States Minor Outlying Islands', 'UM', 'UMI');
INSERT INTO `#__sdi_sys_country` VALUES ('229', '229', '1', 'Uruguay', 'UY', 'URY');
INSERT INTO `#__sdi_sys_country` VALUES ('230', '230', '1', 'Uzbekistan', 'UZ', 'UZB');
INSERT INTO `#__sdi_sys_country` VALUES ('231', '231', '1', 'Vanuatu', 'VU', 'VUT');
INSERT INTO `#__sdi_sys_country` VALUES ('232', '232', '1', 'Vatican City (Holy See)', 'VA', 'VAT');
INSERT INTO `#__sdi_sys_country` VALUES ('233', '233', '1', 'Venezuela', 'VE', 'VEN');
INSERT INTO `#__sdi_sys_country` VALUES ('234', '234', '1', 'Viet Nam', 'VN', 'VNM');
INSERT INTO `#__sdi_sys_country` VALUES ('235', '235', '1', 'Virgin Islands, British', 'VG', 'VGB');
INSERT INTO `#__sdi_sys_country` VALUES ('236', '236', '1', 'Virgin Islands, U.S.', 'VI', 'VIR');
INSERT INTO `#__sdi_sys_country` VALUES ('237', '237', '1', 'Wallis and Futuna', 'WF', 'WLF');
INSERT INTO `#__sdi_sys_country` VALUES ('238', '238', '1', 'Western Sahara (formerly Spanish Sahara)', 'EH', 'ESH');
INSERT INTO `#__sdi_sys_country` VALUES ('239', '239', '1', 'Yemen (Arab Republic)', 'YE', 'YEM');
INSERT INTO `#__sdi_sys_country` VALUES ('240', '240', '1', 'Zambia', 'ZM', 'ZMB');
INSERT INTO `#__sdi_sys_country` VALUES ('241', '241', '1', 'Zimbabwe', 'ZW', 'ZWE');

INSERT INTO `#__sdi_sys_entity` VALUES ('1', '1', '1', 'attribute');
INSERT INTO `#__sdi_sys_entity` VALUES ('2', '2', '1', 'class');

INSERT INTO `#__sdi_sys_stereotype` VALUES ('1', '1', '1', 'guid', '([A-Z0-9]{8}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{12})', 'CharacterString', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('2', '2', '1', 'text', '^[a-zA-Z0-9_]{1,}$', 'CharacterString', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('3', '3', '1', 'locale', '^[a-zA-Z0-9_]{1,}$', null, null,'1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('4', '4', '1', 'number', '[0-9.-]', 'Decimal', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('5', '5', '1', 'date', '(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}', 'Date', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('6', '6', '1', 'list', '^[a-zA-Z0-9_]{1,}$', null, null,'1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('7', '7', '1', 'link', '^[a-zA-Z0-9_]{1,}$', 'CharacterString', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('8', '8', '1', 'datetime', '(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}', 'DateTime', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('9', '9', '1', 'textchoice', '^[a-zA-Z0-9_]{1,}$', 'CharacterString', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('10', '10', '1', 'localechoice', '^[a-zA-Z0-9_]{1,}$', null, null,'1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('11', '11', '1', 'gemet', null, null, null,'1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('12', '12', '1', 'distance', '[0-9.-]', 'Distance', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('13', '13', '1', 'integer', '[0-9]', 'Integer', '2','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('14', '14', '1', 'file', '', 'MI_Identifier', '1','1');
INSERT INTO `#__sdi_sys_stereotype` VALUES ('15', '15', '1', 'geographicextent', null, null, null,'2');

INSERT INTO `#__sdi_sys_criteriatype` VALUES ('1', '1', '1', 'system');
INSERT INTO `#__sdi_sys_criteriatype` VALUES ('2', '2', '1', 'relation');
INSERT INTO `#__sdi_sys_criteriatype` VALUES ('3', '3', '1', 'csw');

INSERT INTO `#__sdi_sys_importtype` VALUES ('1', '1', '1', 'replace');
INSERT INTO `#__sdi_sys_importtype` VALUES ('2', '2', '1', 'merge');

INSERT INTO `#__sdi_sys_orderstate` VALUES ('1', '1', '1', 'archived');
INSERT INTO `#__sdi_sys_orderstate` VALUES ('2', '2', '1', 'historized');
INSERT INTO `#__sdi_sys_orderstate` VALUES ('3', '3', '1', 'finish');
INSERT INTO `#__sdi_sys_orderstate` VALUES ('4', '4', '1', 'await');
INSERT INTO `#__sdi_sys_orderstate` VALUES ('5', '5', '1', 'progress');
INSERT INTO `#__sdi_sys_orderstate` VALUES ('6', '6', '1', 'sent');
INSERT INTO `#__sdi_sys_orderstate` VALUES ('7', '7', '1', 'saved');

INSERT INTO `#__sdi_sys_ordertype` VALUES ('1', '1', '1', 'order');
INSERT INTO `#__sdi_sys_ordertype` VALUES ('2', '2', '1', 'estimate');

INSERT INTO `#__sdi_sys_productstate` VALUES ('1', '1', '1', 'available');
INSERT INTO `#__sdi_sys_productstate` VALUES ('2', '2', '1', 'await');

INSERT INTO `#__sdi_sys_relationtype` VALUES ('1', '1', '1', 'association');
INSERT INTO `#__sdi_sys_relationtype` VALUES ('2', '2', '1', 'aggregation');
INSERT INTO `#__sdi_sys_relationtype` VALUES ('3', '3', '1', 'composition');
INSERT INTO `#__sdi_sys_relationtype` VALUES ('4', '4', '1', 'generalization');

INSERT INTO `#__sdi_sys_rendertype` VALUES ('1', '1', '1', 'textarea');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('2', '2', '1', 'checkbox');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('3', '3', '1', 'radiobutton');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('4', '4', '1', 'list');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('5', '5', '1', 'textbox');
INSERT INTO `#__sdi_sys_rendertype` VALUES ('6', '6', '1', 'date');

INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('1', '1', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('2', '2', '1');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('3', '2', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('4', '3', '1');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('5', '3', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('6', '4', '1');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('7', '4', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('8', '5', '1');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('9', '6', '2');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('10', '6', '3');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('11', '6', '4');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('12', '7', '1');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('13', '7', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('14', '8', '1');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('15', '8', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('16', '9', '4');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('17', '10', '4');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('18', '12', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('19', '13', '5');
INSERT INTO `#__sdi_sys_rendertype_stereotype` VALUES ('20', '14', '5');

INSERT INTO `#__sdi_sys_rendertype_criteriatype` VALUES ('1', '3', '5');
INSERT INTO `#__sdi_sys_rendertype_criteriatype` VALUES ('2', '3', '6');

INSERT INTO `#__sdi_sys_searchtab` VALUES ('1', '1', '1', 'simple');
INSERT INTO `#__sdi_sys_searchtab` VALUES ('2', '2', '1', 'advanced');
INSERT INTO `#__sdi_sys_searchtab` VALUES ('3', '3', '1', 'hidden');
INSERT INTO `#__sdi_sys_searchtab` VALUES ('4', '4', '1', 'none');

INSERT INTO `#__sdi_sys_topiccategory` VALUES ('1', '1', '1', 'farming');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('2', '2', '1', 'biota');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('3', '3', '1', 'bounderies');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('4', '4', '1', 'climatologyMeteorologyAtmosphere');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('5', '5', '1', 'economy');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('6', '6', '1', 'elevation');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('7', '7', '1', 'environment');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('8', '8', '1', 'geoscientificinformation');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('9', '9', '1', 'health');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('10', '10', '1', 'imageryBaseMapsEarthCover');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('11', '11', '1', 'intelligenceMilitary');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('12', '12', '1', 'inlandWaters');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('13', '13', '1', 'location');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('14', '14', '1', 'oceans');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('15', '15', '1', 'planningCadastre');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('16', '16', '1', 'society');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('17', '17', '1', 'structure');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('18', '18', '1', 'transportation');
INSERT INTO `#__sdi_sys_topiccategory` VALUES ('19', '19', '1', 'utilitiesCommunication');

INSERT INTO `#__sdi_sys_productmining` VALUES ('1', '1', '1', 'manual');
INSERT INTO `#__sdi_sys_productmining` VALUES ('2', '2', '1', 'automatic');

INSERT INTO `#__sdi_sys_relationscope` VALUES ('1', '1', '1', 'editable');
INSERT INTO `#__sdi_sys_relationscope` VALUES ('2', '2', '1', 'visible');
INSERT INTO `#__sdi_sys_relationscope` VALUES ('3', '3', '1', 'hidden');

INSERT INTO `#__sdi_language` VALUES ('1', '0', '1', 'العربية', 'ar-DZ', 'ar', 'ara', 'ar', 'DZ');
INSERT INTO `#__sdi_language` VALUES ('3', '0', '1', 'български език', 'bg-BG', 'bg', 'bul', 'bg', 'BG');
INSERT INTO `#__sdi_language` VALUES ('4', '0', '1', 'català, valencià', 'ca-ES', 'ca', 'cat', 'ca', 'ES');
INSERT INTO `#__sdi_language` VALUES ('5', '0', '1', 'čeština, český jazyk', 'cs-CZ', 'cs', 'ces', 'cs', 'CZ');
INSERT INTO `#__sdi_language` VALUES ('6', '0', '1', 'dansk', 'da-DK', 'da', 'dan', 'da', 'DK');
INSERT INTO `#__sdi_language` VALUES ('7', '0', '1', 'Deutsch', 'de-DE', 'de', 'deu', 'de', 'DE');
INSERT INTO `#__sdi_language` VALUES ('8', '0', '1', 'ελληνικά', 'el-GR', 'el', 'ell', 'el', 'GR');
INSERT INTO `#__sdi_language` VALUES ('9', '0', '1', 'English (United Kingdom)', 'en-GB', 'en', 'eng', 'en', 'GB');
INSERT INTO `#__sdi_language` VALUES ('10', '0', '1', 'English (US)', 'en-US', 'en-US', 'eng', 'en', 'US');
INSERT INTO `#__sdi_language` VALUES ('11', '0', '1', 'español, castellano', 'es-ES', 'es', 'spa', 'es', 'ES');
INSERT INTO `#__sdi_language` VALUES ('12', '0', '1', 'eesti, eesti keel', 'et-EE', 'et', 'est', 'et', 'EE');
INSERT INTO `#__sdi_language` VALUES ('13', '0', '1', 'euskara, euskera', 'eu-ES', 'eu', 'eus', 'eu', 'ES');
INSERT INTO `#__sdi_language` VALUES ('14', '0', '1', 'suomi, suomen kieli', 'fi-FI', 'fi', 'fin', 'fi', 'FI');
INSERT INTO `#__sdi_language` VALUES ('15', '0', '1', 'français, langue française', 'fr-FR', 'fr', 'fra', 'fr', 'FR');
INSERT INTO `#__sdi_language` VALUES ('16', '0', '1', 'Gaeilge', 'ga-IE', 'ga', 'gle', 'ga', 'IE');
INSERT INTO `#__sdi_language` VALUES ('17', '0', '1', 'hrvatski jezik', 'hr-HR', 'hr', 'hrv', 'hr', 'HR');
INSERT INTO `#__sdi_language` VALUES ('18', '0', '1', 'magyar', 'hu-HU', 'hu', 'hun', 'hu', 'HU');
INSERT INTO `#__sdi_language` VALUES ('19', '0', '1', 'italiano', 'it-IT', 'it', 'ita', 'it', 'IT');
INSERT INTO `#__sdi_language` VALUES ('20', '0', '1', 'lietuvių kalba', 'lt-LT', 'lt', 'lit', 'lt', 'LT');
INSERT INTO `#__sdi_language` VALUES ('21', '0', '1', 'latviešu valoda', 'lv-LV', 'lv', 'lav', 'lv', 'LV');
INSERT INTO `#__sdi_language` VALUES ('22', '0', '1', 'Malti', 'mt-MT', 'mt', 'mlt', 'mt', 'MT');
INSERT INTO `#__sdi_language` VALUES ('23', '0', '1', 'Nederlands, Vlaams', 'nl-NL', 'nl', 'nld', 'nl', 'NL');
INSERT INTO `#__sdi_language` VALUES ('24', '0', '1', 'Norsk', 'no-NO', 'no', 'nor', 'no', 'NO');
INSERT INTO `#__sdi_language` VALUES ('25', '0', '1', 'język polski, polszczyzna', 'pl-PL', 'pl', 'pol', 'pl', 'PL');
INSERT INTO `#__sdi_language` VALUES ('26', '0', '1', 'português', 'pt-PT', 'pt', 'por', 'pt', 'PT');
INSERT INTO `#__sdi_language` VALUES ('27', '0', '1', 'limba română', 'ro-RO', 'ro', 'ron', 'ro', 'RO');
INSERT INTO `#__sdi_language` VALUES ('28', '0', '1', 'русский язык', 'ru-RU', 'ru', 'rus', 'ru', 'RU');
INSERT INTO `#__sdi_language` VALUES ('29', '0', '1', 'slovenčina, slovenský jazyk', 'sk-SK', 'sk', 'slk', 'sk', 'SK');
INSERT INTO `#__sdi_language` VALUES ('30', '0', '1', 'Svenska', 'sv-SE', 'sv', 'swe', 'sv', 'SE');
INSERT INTO `#__sdi_language` VALUES ('31', '0', '1', 'Türkçe', 'tr-TR', 'tr', 'tur', 'tr', 'TR');
INSERT INTO `#__sdi_language` VALUES ('32', '0', '1', 'українська мова', 'uk-UA', 'uk', 'ukr', 'uk', 'UA');
INSERT INTO `#__sdi_language` VALUES ('33', '0', '1', 'Chinese', 'zh-CN', 'zh-CN', 'zho', 'zh', 'CN');

