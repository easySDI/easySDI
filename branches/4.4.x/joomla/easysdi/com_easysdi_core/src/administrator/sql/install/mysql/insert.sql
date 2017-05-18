INSERT IGNORE INTO `#__sdi_sys_isolanguage` VALUES ('1','1','1','iso639-1');
INSERT IGNORE INTO `#__sdi_sys_isolanguage` VALUES ('2','2','1','iso639-2B');
INSERT IGNORE INTO `#__sdi_sys_isolanguage` VALUES ('3','3','1','iso639-2T');

INSERT IGNORE INTO `#__sdi_sys_unit` (ordering,state,alias,name) 
VALUES 
(1,1,'m','meter'),
(2,1,'dd','degree')
;

INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('1','1','1','member' );
INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('2','2','1','resourcemanager' );
INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('3','3','1','metadataresponsible' );
INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('4','4','1','metadataeditor' );
INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('5','5','1','diffusionmanager' );
INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('6','6','1','previewmanager' );
INSERT IGNORE INTO `#__sdi_sys_role` VALUES ('7','7','1','extractionresponsible' );
INSERT IGNORE INTO `#__sdi_sys_role` SET id=9, ordering=9, `state`=1, value='pricingmanager';
INSERT IGNORE INTO `#__sdi_sys_role` SET id=10, ordering=10, `state`=1, `value`='validationmanager';
INSERT IGNORE INTO `#__sdi_sys_role` SET id=11, ordering=11, `state`=1, `value`='organismmanager';

INSERT IGNORE INTO `#__sdi_sys_versiontype` (ordering,state,value) 
VALUES 
(1,1,'all'),
(2,1,'lastPublishedVersion')
;

INSERT IGNORE INTO `#__sdi_sys_servicescope` (ordering,state,value) 
VALUES 
(1,1,'all'),
(2,1,'organism'),
(3,1,'none')
;

INSERT IGNORE INTO `#__sdi_sys_metadatastate` (ordering,state,value) 
VALUES 
(1,1,'inprogress'),
(2,1,'validated'),
(3,1,'published'),
(4,1,'archived')
;

INSERT IGNORE INTO `#__sdi_sys_spatialoperator` (ordering,state,value) 
VALUES 
(1,1,'within'),
(3,1,'touch')
;

INSERT IGNORE INTO `#__sdi_sys_accessscope` (ordering,state,value) 
VALUES 
(1,1,'public'),
(3,1,'organism'),
(4,1,'user'),
(2,1,'category')
;

INSERT IGNORE INTO `#__sdi_sys_authenticationlevel` (ordering,state,value) 
VALUES 
(1,1,'resource'),
(2,1,'service')
;

INSERT IGNORE INTO `#__sdi_sys_authenticationconnector` (ordering,state,authenticationlevel_id,value) 
VALUES 
(1,1,1,'HTTPBasic'),
(2,1,2,'Geonetwork')
;

INSERT IGNORE INTO `#__sdi_sys_serviceversion` (ordering,state,value) 
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

INSERT IGNORE INTO `#__sdi_sys_serviceconnector` (ordering,state,value) 
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

INSERT IGNORE INTO `#__sdi_sys_servicecon_authenticationcon` (serviceconnector_id,authenticationconnector_id) 
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

INSERT IGNORE INTO `#__sdi_sys_servicecompliance` (ordering,state,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(1,1,1,7,1,1,0,1),
(2,1,1,8,1,1,0,1),
(3,1,2,2,1,1,1,0),
(4,1,2,3,1,1,1,0),
(5,1,2,4,1,1,1,0),
(6,1,3,1,1,1,0,0),
(7,1,4,1,1,1,1,0)
;

INSERT IGNORE INTO `#__sdi_sys_serviceoperation` (ordering,state,value) 
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

INSERT IGNORE INTO `#__sdi_sys_operationcompliance` (ordering,state,servicecompliance_id,serviceoperation_id,implemented) 
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

INSERT IGNORE INTO `#__sdi_sys_serviceconnector` (ordering,state,value) 
VALUES 
(11,1,'WMSC'),
(12,0,'Bing'),
(13,0,'Google'),
(14,0,'OSM')
;

INSERT IGNORE INTO `#__sdi_sys_servicecompliance` (ordering,state,serviceconnector_id,serviceversion_id,implemented,relayable,aggregatable,harvestable) 
VALUES 
(8,1,11,2,1,1,1,0),
(9,1,11,3,1,1,1,0),
(10,1,11,4,1,1,1,0)
;

INSERT IGNORE INTO `#__sdi_sys_exceptionlevel` (ordering, state, value )
VALUES
(1,1,'permissive'),
(2,1,'restrictive')
;

INSERT IGNORE INTO `#__sdi_sys_proxytype` (ordering, state, value )
VALUES
(1,1,'harvest'),
(2,1,'relay'),
(3,1,'aggregate');

INSERT IGNORE INTO `#__sdi_sys_loglevel` (ordering, state, value )
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

INSERT IGNORE INTO `#__sdi_sys_logroll` (ordering, state, value )
VALUES
(1,1,'daily'),
(2,1,'weekly'),
(3,1,'monthly'),
(4,1,'annually')
;

INSERT IGNORE INTO `#__sdi_sys_metadataversion` VALUES ('1','1','1','all' );
INSERT IGNORE INTO `#__sdi_sys_metadataversion` VALUES ('2','2','1','last' );

INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('1', '1', '1', 'Afghanistan', 'AF', 'AFG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('2', '2', '1', 'Åland Islands', 'AX', 'ALA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('3', '3', '1', 'Albania', 'AL', 'ALB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('4', '4', '1', 'Algeria (El Djazaïr)', 'DZ', 'DZA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('5', '5', '1', 'American Samoa', 'AS', 'ASM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('6', '6', '1', 'Andorra', 'AD', 'AND');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('7', '7', '1', 'Angola', 'AO', 'AGO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('8', '8', '1', 'Anguilla', 'AI', 'AIA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('9', '9', '1', 'Antarctica', 'AQ', 'ATA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('10', '10', '1', 'Antigua and Barbuda', 'AG', 'ATG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('11', '11', '1', 'Argentina', 'AR', 'ARG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('12', '12', '1', 'Armenia', 'AM', 'ARM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('13', '13', '1', 'Aruba', 'AW', 'ABW');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('14', '14', '1', 'Australia', 'AU', 'AUS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('15', '15', '1', 'Austria', 'AT', 'AUT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('16', '16', '1', 'Azerbaijan', 'AZ', 'AZE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('17', '17', '1', 'Bahamas', 'BS', 'BHS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('18', '18', '1', 'Bahrain', 'BH', 'BHR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('19', '19', '1', 'Bangladesh', 'BD', 'BGD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('20', '20', '1', 'Barbados', 'BB', 'BRB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('21', '21', '1', 'Belarus', 'BY', 'BLR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('22', '22', '1', 'Belgium', 'BE', 'BEL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('23', '23', '1', 'Belize', 'BZ', 'BLZ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('24', '24', '1', 'Benin', 'BJ', 'BEN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('25', '25', '1', 'Bermuda', 'BM', 'BMU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('26', '26', '1', 'Bhutan', 'BT', 'BTN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('27', '27', '1', 'Bolivia', 'BO', 'BOL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('28', '28', '1', 'Bosnia and Herzegovina', 'BA', 'BIH');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('29', '29', '1', 'Botswana', 'BW', 'BWA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('30', '30', '1', 'Bouvet Island', 'BV', 'BVT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('31', '31', '1', 'Brazil', 'BR', 'BRA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('32', '32', '1', 'British Indian Ocean Territory', 'IO', 'IOT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('33', '33', '1', 'Brunei Darussalam', 'BN', 'BRN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('34', '34', '1', 'Bulgaria', 'BG', 'BGR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('35', '35', '1', 'Burkina Faso', 'BF', 'BFA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('36', '36', '1', 'Burundi', 'BI', 'BDI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('37', '37', '1', 'Cambodia', 'KH', 'KHM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('38', '38', '1', 'Cameroon', 'CM', 'CMR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('39', '39', '1', 'Canada', 'CA', 'CAN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('40', '40', '1', 'Cape Verde', 'CV', 'CPV');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('41', '41', '1', 'Cayman Islands', 'KY', 'CYM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('42', '42', '1', 'Central African Republic', 'CF', 'CAF');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('43', '43', '1', 'Chad (T\'Chad)', 'TD', 'TCD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('44', '44', '1', 'Chile', 'CL', 'CHL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('45', '45', '1', 'China', 'CN', 'CHN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('46', '46', '1', 'Christmas Island', 'CX', 'CXR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('47', '47', '1', 'Cocos (Keeling) Islands', 'CC', 'CCK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('48', '48', '1', 'Colombia', 'CO', 'COL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('49', '49', '1', 'Comoros', 'KM', 'COM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('50', '50', '1', 'Congo, Republic Of', 'CG', 'COG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('51', '51', '1', 'Congo, The Democratic Republic of the (formerly Zaire)', 'CD', 'COD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('52', '52', '1', 'Cook Islands', 'CK', 'COK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('53', '53', '1', 'Costa Rica', 'CR', 'CRI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('54', '54', '1', 'CÔte D\'Ivoire (Ivory Coast)', 'CI', 'CIV');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('55', '55', '1', 'Croatia (hrvatska)', 'HR', 'HRV');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('56', '56', '1', 'Cuba', 'CU', 'CUB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('57', '57', '1', 'Cyprus', 'CY', 'CYP');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('58', '58', '1', 'Czech Republic', 'CZ', 'CZE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('59', '59', '1', 'Denmark', 'DK', 'DNK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('60', '60', '1', 'Djibouti', 'DJ', 'DJI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('61', '61', '1', 'Dominica', 'DM', 'DMA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('62', '62', '1', 'Dominican Republic', 'DO', 'DOM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('63', '63', '1', 'Ecuador', 'EC', 'ECU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('64', '64', '1', 'Egypt', 'EG', 'EGY');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('65', '65', '1', 'El Salvador', 'SV', 'SLV');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('66', '66', '1', 'Equatorial Guinea', 'GQ', 'GNQ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('67', '67', '1', 'Eritrea', 'ER', 'ERI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('68', '68', '1', 'Estonia', 'EE', 'EST');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('69', '69', '1', 'Ethiopia', 'ET', 'ETH');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('70', '70', '1', 'Faeroe Islands', 'FO', 'FRO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('71', '71', '1', 'Falkland Islands (Malvinas)', 'FK', 'FLK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('72', '72', '1', 'Fiji', 'FJ', 'FJI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('73', '73', '1', 'Finland', 'FI', 'FIN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('74', '74', '1', 'France', 'FR', 'FRA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('75', '75', '1', 'French Guiana', 'GF', 'GUF');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('76', '76', '1', 'French Polynesia', 'PF', 'PYF');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('77', '77', '1', 'French Southern Territories', 'TF', 'ATF');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('78', '78', '1', 'Gabon', 'GA', 'GAB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('79', '79', '1', 'Gambia, The', 'GM', 'GMB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('80', '80', '1', 'Georgia', 'GE', 'GEO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('81', '81', '1', 'Germany (Deutschland)', 'DE', 'DEU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('82', '82', '1', 'Ghana', 'GH', 'GHA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('83', '83', '1', 'Gibraltar', 'GI', 'GIB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('84', '84', '1', 'Great Britain', 'GB', 'GBR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('85', '85', '1', 'Greece', 'GR', 'GRC');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('86', '86', '1', 'Greenland', 'GL', 'GRL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('87', '87', '1', 'Grenada', 'GD', 'GRD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('88', '88', '1', 'Guadeloupe', 'GP', 'GLP');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('89', '89', '1', 'Guam', 'GU', 'GUM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('90', '90', '1', 'Guatemala', 'GT', 'GTM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('91', '91', '1', 'Guinea', 'GN', 'GIN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('92', '92', '1', 'Guinea-bissau', 'GW', 'GNB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('93', '93', '1', 'Guyana', 'GY', 'GUY');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('94', '94', '1', 'Haiti', 'HT', 'HTI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('95', '95', '1', 'Heard Island and Mcdonald Islands', 'HM', 'HMD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('96', '96', '1', 'Honduras', 'HN', 'HND');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('97', '97', '1', 'Hong Kong (Special Administrative Region of China)', 'HK', 'HKG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('98', '98', '1', 'Hungary', 'HU', 'HUN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('99', '99', '1', 'Iceland', 'IS', 'ISL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('100', '100', '1', 'India', 'IN', 'IND');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('101', '101', '1', 'Indonesia', 'ID', 'IDN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('102', '102', '1', 'Iran (Islamic Republic of Iran)', 'IR', 'IRN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('103', '103', '1', 'Iraq', 'IQ', 'IRQ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('104', '104', '1', 'Ireland', 'IE', 'IRL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('105', '105', '1', 'Israel', 'IL', 'ISR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('106', '106', '1', 'Italy', 'IT', 'ITA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('107', '107', '1', 'Jamaica', 'JM', 'JAM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('108', '108', '1', 'Japan', 'JP', 'JPN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('109', '109', '1', 'Jordan (Hashemite Kingdom of Jordan)', 'JO', 'JOR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('110', '110', '1', 'Kazakhstan', 'KZ', 'KAZ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('111', '111', '1', 'Kenya', 'KE', 'KEN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('112', '112', '1', 'Kiribati', 'KI', 'KIR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('113', '113', '1', 'Korea (Democratic Peoples Republic pf [North] Korea)', 'KP', 'PRK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('114', '114', '1', 'Korea (Republic of [South] Korea)', 'KR', 'KOR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('115', '115', '1', 'Kuwait', 'KW', 'KWT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('116', '116', '1', 'Kyrgyzstan', 'KG', 'KGZ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('117', '117', '1', 'Lao People\'s Democratic Republic', 'LA', 'LAO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('118', '118', '1', 'Latvia', 'LV', 'LVA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('119', '119', '1', 'Lebanon', 'LB', 'LBN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('120', '120', '1', 'Lesotho', 'LS', 'LSO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('121', '121', '1', 'Liberia', 'LR', 'LBR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('122', '122', '1', 'Libya (Libyan Arab Jamahirya)', 'LY', 'LBY');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('123', '123', '1', 'Liechtenstein (Fürstentum Liechtenstein)', 'LI', 'LIE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('124', '124', '1', 'Lithuania', 'LT', 'LTU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('125', '125', '1', 'Luxembourg', 'LU', 'LUX');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('126', '126', '1', 'Macao (Special Administrative Region of China)', 'MO', 'MAC');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('127', '127', '1', 'Macedonia (Former Yugoslav Republic of Macedonia)', 'MK', 'MKD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('128', '128', '1', 'Madagascar', 'MG', 'MDG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('129', '129', '1', 'Malawi', 'MW', 'MWI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('130', '130', '1', 'Malaysia', 'MY', 'MYS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('131', '131', '1', 'Maldives', 'MV', 'MDV');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('132', '132', '1', 'Mali', 'ML', 'MLI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('133', '133', '1', 'Malta', 'MT', 'MLT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('134', '134', '1', 'Marshall Islands', 'MH', 'MHL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('135', '135', '1', 'Martinique', 'MQ', 'MTQ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('136', '136', '1', 'Mauritania', 'MR', 'MRT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('137', '137', '1', 'Mauritius', 'MU', 'MUS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('138', '138', '1', 'Mayotte', 'YT', 'MYT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('139', '139', '1', 'Mexico', 'MX', 'MEX');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('140', '140', '1', 'Micronesia (Federated States of Micronesia)', 'FM', 'FSM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('141', '141', '1', 'Moldova', 'MD', 'MDA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('142', '142', '1', 'Monaco', 'MC', 'MCO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('143', '143', '1', 'Mongolia', 'MN', 'MNG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('144', '144', '1', 'Montserrat', 'MS', 'MSR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('145', '145', '1', 'Morocco', 'MA', 'MAR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('146', '146', '1', 'Mozambique (Moçambique)', 'MZ', 'MOZ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('147', '147', '1', 'Myanmar (formerly Burma)', 'MM', 'MMR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('148', '148', '1', 'Namibia', 'NA', 'NAM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('149', '149', '1', 'Nauru', 'NR', 'NRU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('150', '150', '1', 'Nepal', 'NP', 'NPL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('151', '151', '1', 'Netherlands', 'NL', 'NLD');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('152', '152', '1', 'Netherlands Antilles', 'AN', 'ANT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('153', '153', '1', 'New Caledonia', 'NC', 'NCL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('154', '154', '1', 'New Zealand', 'NZ', 'NZL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('155', '155', '1', 'Nicaragua', 'NI', 'NIC');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('156', '156', '1', 'Niger', 'NE', 'NER');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('157', '157', '1', 'Nigeria', 'NG', 'NGA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('158', '158', '1', 'Niue', 'NU', 'NIU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('159', '159', '1', 'Norfolk Island', 'NF', 'NFK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('160', '160', '1', 'Northern Mariana Islands', 'MP', 'MNP');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('161', '161', '1', 'Norway', 'NO', 'NOR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('162', '162', '1', 'Oman', 'OM', 'OMN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('163', '163', '1', 'Pakistan', 'PK', 'PAK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('164', '164', '1', 'Palau', 'PW', 'PLW');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('165', '165', '1', 'Palestinian Territories', 'PS', 'PSE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('166', '166', '1', 'Panama', 'PA', 'PAN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('167', '167', '1', 'Papua New Guinea', 'PG', 'PNG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('168', '168', '1', 'Paraguay', 'PY', 'PRY');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('169', '169', '1', 'Peru', 'PE', 'PER');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('170', '170', '1', 'Philippines', 'PH', 'PHL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('171', '171', '1', 'Pitcairn', 'PN', 'PCN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('172', '172', '1', 'Poland', 'PL', 'POL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('173', '173', '1', 'Portugal', 'PT', 'PRT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('174', '174', '1', 'Puerto Rico', 'PR', 'PRI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('175', '175', '1', 'Qatar', 'QA', 'QAT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('176', '176', '1', 'RÉunion', 'RE', 'REU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('177', '177', '1', 'Romania', 'RO', 'ROU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('178', '178', '1', 'Russian Federation', 'RU', 'RUS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('179', '179', '1', 'Rwanda', 'RW', 'RWA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('180', '180', '1', 'Saint Helena', 'SH', 'SHN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('181', '181', '1', 'Saint Kitts and Nevis', 'KN', 'KNA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('182', '182', '1', 'Saint Lucia', 'LC', 'LCA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('183', '183', '1', 'Saint Pierre and Miquelon', 'PM', 'SPM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('184', '184', '1', 'Saint Vincent and the Grenadines', 'VC', 'VCT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('185', '185', '1', 'Samoa (formerly Western Samoa)', 'WS', 'WSM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('186', '186', '1', 'San Marino (Republic of)', 'SM', 'SMR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('187', '187', '1', 'Sao Tome and Principe', 'ST', 'STP');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('188', '188', '1', 'Saudi Arabia (Kingdom of Saudi Arabia)', 'SA', 'SAU');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('189', '189', '1', 'Senegal', 'SN', 'SEN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('190', '190', '1', 'Serbia and Montenegro (formerly Yugoslavia)', 'CS', 'SCG');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('191', '191', '1', 'Seychelles', 'SC', 'SYC');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('192', '192', '1', 'Sierra Leone', 'SL', 'SLE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('193', '193', '1', 'Singapore', 'SG', 'SGP');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('194', '194', '1', 'Slovakia (Slovak Republic)', 'SK', 'SVK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('195', '195', '1', 'Slovenia', 'SI', 'SVN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('196', '196', '1', 'Solomon Islands', 'SB', 'SLB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('197', '197', '1', 'Somalia', 'SO', 'SOM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('198', '198', '1', 'South Africa (zuid Afrika)', 'ZA', 'ZAF');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('199', '199', '1', 'South Georgia and the South Sandwich Islands', 'GS', 'SGS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('200', '200', '1', 'Spain (españa)', 'ES', 'ESP');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('201', '201', '1', 'Sri Lanka', 'LK', 'LKA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('202', '202', '1', 'Sudan', 'SD', 'SDN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('203', '203', '1', 'Suriname', 'SR', 'SUR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('204', '204', '1', 'Svalbard and Jan Mayen', 'SJ', 'SJM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('205', '205', '1', 'Swaziland', 'SZ', 'SWZ');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('206', '206', '1', 'Sweden', 'SE', 'SWE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('207', '207', '1', 'Switzerland (Confederation of Helvetia)', 'CH', 'CHE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('208', '208', '1', 'Syrian Arab Republic', 'SY', 'SYR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('209', '209', '1', 'Taiwan (\"Chinese Taipei\" for IOC)', 'TW', 'TWN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('210', '210', '1', 'Tajikistan', 'TJ', 'TJK');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('211', '211', '1', 'Tanzania', 'TZ', 'TZA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('212', '212', '1', 'Thailand', 'TH', 'THA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('213', '213', '1', 'Timor-Leste (formerly East Timor)', 'TL', 'TLS');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('214', '214', '1', 'Togo', 'TG', 'TGO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('215', '215', '1', 'Tokelau', 'TK', 'TKL');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('216', '216', '1', 'Tonga', 'TO', 'TON');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('217', '217', '1', 'Trinidad and Tobago', 'TT', 'TTO');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('218', '218', '1', 'Tunisia', 'TN', 'TUN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('219', '219', '1', 'Turkey', 'TR', 'TUR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('220', '220', '1', 'Turkmenistan', 'TM', 'TKM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('221', '221', '1', 'Turks and Caicos Islands', 'TC', 'TCA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('222', '222', '1', 'Tuvalu', 'TV', 'TUV');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('223', '223', '1', 'Uganda', 'UG', 'UGA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('224', '224', '1', 'Ukraine', 'UA', 'UKR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('225', '225', '1', 'United Arab Emirates', 'AE', 'ARE');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('226', '226', '1', 'United Kingdom (Great Britain)', 'GB', 'GBR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('227', '227', '1', 'United States', 'US', 'USA');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('228', '228', '1', 'United States Minor Outlying Islands', 'UM', 'UMI');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('229', '229', '1', 'Uruguay', 'UY', 'URY');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('230', '230', '1', 'Uzbekistan', 'UZ', 'UZB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('231', '231', '1', 'Vanuatu', 'VU', 'VUT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('232', '232', '1', 'Vatican City (Holy See)', 'VA', 'VAT');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('233', '233', '1', 'Venezuela', 'VE', 'VEN');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('234', '234', '1', 'Viet Nam', 'VN', 'VNM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('235', '235', '1', 'Virgin Islands, British', 'VG', 'VGB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('236', '236', '1', 'Virgin Islands, U.S.', 'VI', 'VIR');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('237', '237', '1', 'Wallis and Futuna', 'WF', 'WLF');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('238', '238', '1', 'Western Sahara (formerly Spanish Sahara)', 'EH', 'ESH');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('239', '239', '1', 'Yemen (Arab Republic)', 'YE', 'YEM');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('240', '240', '1', 'Zambia', 'ZM', 'ZMB');
INSERT IGNORE INTO `#__sdi_sys_country` VALUES ('241', '241', '1', 'Zimbabwe', 'ZW', 'ZWE');

INSERT IGNORE INTO `#__sdi_sys_entity` VALUES ('1', '1', '1', 'attribute');
INSERT IGNORE INTO `#__sdi_sys_entity` VALUES ('2', '2', '1', 'class');

INSERT IGNORE INTO `#__sdi_sys_orderstate` VALUES ('2', '2', '1', 'historized');
INSERT IGNORE INTO `#__sdi_sys_orderstate` VALUES ('3', '3', '1', 'finish');
INSERT IGNORE INTO `#__sdi_sys_orderstate` VALUES ('4', '4', '1', 'await');
INSERT IGNORE INTO `#__sdi_sys_orderstate` VALUES ('5', '5', '1', 'progress');
INSERT IGNORE INTO `#__sdi_sys_orderstate` VALUES ('6', '6', '1', 'sent');
INSERT IGNORE INTO `#__sdi_sys_orderstate` VALUES ('7', '7', '1', 'saved');
INSERT IGNORE INTO `#__sdi_sys_orderstate` SET id=8, ordering=8, `state`=1, `value`='validation';
INSERT IGNORE INTO `#__sdi_sys_orderstate` SET id=9, ordering=9, `state`=1, `value`='rejectedbythirdparty';
INSERT IGNORE INTO `#__sdi_sys_orderstate` SET id=10, ordering=10, `state`=1, `value`='rejectedbysupplier';

INSERT IGNORE INTO `#__sdi_sys_ordertype` VALUES ('1', '1', '1', 'order');
INSERT IGNORE INTO `#__sdi_sys_ordertype` VALUES ('2', '2', '1', 'estimate');
INSERT IGNORE INTO `#__sdi_sys_ordertype` VALUES ('3', '3', '1', 'draft');

INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('1', '1', '1', 'available');
INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('2', '2', '1', 'await');
INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('3', '3', '1', 'sent');
INSERT IGNORE INTO `#__sdi_sys_productstate` SET id=4, ordering=4, `state`=1, `value`='validation';
INSERT IGNORE INTO `#__sdi_sys_productstate` SET id=5, ordering=5, `state`=1, `value`='rejectedbythirdparty';
INSERT IGNORE INTO `#__sdi_sys_productstate` SET id=6, ordering=6, `state`=1, `value`='rejectedbysupplier';
INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('7', '7', '1', 'deleted');
INSERT IGNORE INTO `#__sdi_sys_productstate` VALUES ('8', '8', '1', 'blocked');


INSERT INTO `#__sdi_sys_pricing` VALUES ('1', '1', '1', 'free');
INSERT INTO `#__sdi_sys_pricing` VALUES ('2', '2', '1', 'feewithoutapricingprofile');
INSERT INTO `#__sdi_sys_pricing` SET `ordering`=3, `state`=1, `value`='feewithapricingprofile';

INSERT IGNORE INTO `#__sdi_sys_productstorage` VALUES ('1', '1', '1', 'upload');
INSERT IGNORE INTO `#__sdi_sys_productstorage` VALUES ('2', '2', '1', 'url');
INSERT IGNORE INTO `#__sdi_sys_productstorage` VALUES ('3', '3', '1', 'zoning');

INSERT IGNORE INTO `#__sdi_sys_productmining` VALUES ('1', '2', '1', 'automatic');
INSERT IGNORE INTO `#__sdi_sys_productmining` VALUES ('2', '1', '1', 'manual');

INSERT IGNORE INTO `#__sdi_sys_perimetertype` (ordering,state,value) 
VALUES 
(1,1,'extraction'),
(2,1,'download'),
(3,1,'both')
;

INSERT IGNORE INTO `#__sdi_sys_extractstorage` SET id=1, ordering=1, `state`=1, `value`='local';
INSERT IGNORE INTO `#__sdi_sys_extractstorage` SET id=2, ordering=2, `state`=1, `value`='remote';

INSERT IGNORE INTO `#__sdi_language` VALUES ('1', '0', '1', 'العربية', 'ar-DZ', 'ar', 'ara', 'ar', 'DZ', 'ara', 'Arabic');
INSERT IGNORE INTO `#__sdi_language` VALUES ('3', '0', '1', 'български език', 'bg-BG', 'bg', 'bul', 'bg', 'BG','bul', 'Bulgarian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('4', '0', '1', 'català', 'ca-ES', 'ca', 'cat', 'ca', 'ES','cat', 'Catalan');
INSERT IGNORE INTO `#__sdi_language` VALUES ('5', '0', '1', 'čeština', 'cs-CZ', 'cs', 'ces', 'cs', 'CZ','cze', 'Czech');
INSERT IGNORE INTO `#__sdi_language` VALUES ('6', '0', '1', 'dansk', 'da-DK', 'da', 'dan', 'da', 'DK','dan', 'Danish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('7', '0', '1', 'Deutsch', 'de-DE', 'de', 'deu', 'de', 'DE','ger', 'German');
INSERT IGNORE INTO `#__sdi_language` VALUES ('8', '0', '1', 'ελληνικά', 'el-GR', 'el', 'ell', 'el', 'GR','gre', 'Greek');
INSERT IGNORE INTO `#__sdi_language` VALUES ('9', '0', '1', 'English (UK)', 'en-GB', 'en', 'eng', 'en', 'GB','eng', 'English');
INSERT IGNORE INTO `#__sdi_language` VALUES ('10', '0', '1', 'English (US)', 'en-US', 'en-US', 'eng', 'en', 'US','eng', 'English');
INSERT IGNORE INTO `#__sdi_language` VALUES ('11', '0', '1', 'español', 'es-ES', 'es', 'spa', 'es', 'ES','spa', 'Spanish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('12', '0', '1', 'eesti', 'et-EE', 'et', 'est', 'et', 'EE','est', 'Estonian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('13', '0', '1', 'euskara', 'eu-ES', 'eu', 'eus', 'eu', 'ES','baq', 'Spanish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('14', '0', '1', 'suomi', 'fi-FI', 'fi', 'fin', 'fi', 'FI','fin', 'Finnish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('15', '0', '1', 'Français', 'fr-FR', 'fr', 'fra', 'fr', 'FR','fre', 'French');
INSERT IGNORE INTO `#__sdi_language` VALUES ('16', '0', '1', 'Gaeilge', 'ga-IE', 'ga', 'gle', 'ga', 'IE','gle', 'Irish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('17', '0', '1', 'hrvatski jezik', 'hr-HR', 'hr', 'scr', 'hr', 'HR','hrv', 'Croatian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('18', '0', '1', 'magyar', 'hu-HU', 'hu', 'hun', 'hu', 'HU','hun', 'Hungarian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('19', '0', '1', 'italiano', 'it-IT', 'it', 'ita', 'it', 'IT','ita', 'Italian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('20', '0', '1', 'lietuvių kalba', 'lt-LT', 'lt', 'lit', 'lt', 'LT','lit', 'Lithuanian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('21', '0', '1', 'latviešu valoda', 'lv-LV', 'lv', 'lav', 'lv', 'LV','lav', 'Latvian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('22', '0', '1', 'Malti', 'mt-MT', 'mt', 'mlt', 'mt', 'MT','mlt', 'English');
INSERT IGNORE INTO `#__sdi_language` VALUES ('23', '0', '1', 'Nederlands', 'nl-NL', 'nl', 'nld', 'nl', 'NL','dut', 'Dutch');
INSERT IGNORE INTO `#__sdi_language` VALUES ('24', '0', '1', 'Norsk', 'no-NO', 'no', 'nor', 'no', 'NO','nor', 'Norwegian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('25', '0', '1', 'język polski', 'pl-PL', 'pl', 'pol', 'pl', 'PL','pol', 'Polish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('26', '0', '1', 'português', 'pt-PT', 'pt', 'por', 'pt', 'PT','por', 'Portuguese');
INSERT IGNORE INTO `#__sdi_language` VALUES ('27', '0', '1', 'română', 'ro-RO', 'ro', 'ron', 'ro', 'RO','rum', 'Romanian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('28', '0', '1', 'русский язык', 'ru-RU', 'ru', 'rus', 'ru', 'RU','rus', 'Russian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('29', '0', '1', 'slovenčina', 'sk-SK', 'sk', 'slk', 'sk', 'SK','slo', 'Slovak');
INSERT IGNORE INTO `#__sdi_language` VALUES ('30', '0', '1', 'Svenska', 'sv-SE', 'sv', 'swe', 'sv', 'SE','swe', 'Swedish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('31', '0', '1', 'Türkçe', 'tr-TR', 'tr', 'tur', 'tr', 'TR','tur', 'Turkish');
INSERT IGNORE INTO `#__sdi_language` VALUES ('32', '0', '1', 'українська мова', 'uk-UA', 'uk', 'ukr', 'uk', 'UA','ukr', 'Ukranian');
INSERT IGNORE INTO `#__sdi_language` VALUES ('33', '0', '1', 'Chinese', 'zh-CN', 'zh-CN', 'zho', 'zh', 'CN','chi', 'Chinese');

-- com_easysdi_contact

INSERT IGNORE INTO `#__sdi_sys_addresstype` (ordering,state,value) 
VALUES 
(1 ,1,'contact'),
(2 ,1,'billing'),
(3 ,1,'delivry')
;

-- com_easysdi_catalog

INSERT IGNORE INTO `#__sdi_sys_criteriatype` VALUES ('1', '1', '1', 'system');
INSERT IGNORE INTO `#__sdi_sys_criteriatype` VALUES ('2', '2', '1', 'relation');
INSERT IGNORE INTO `#__sdi_sys_criteriatype` VALUES ('3', '3', '1', 'csw');

INSERT IGNORE INTO `#__sdi_sys_importtype` VALUES ('1', '1', '1', 'replace');
INSERT IGNORE INTO `#__sdi_sys_importtype` VALUES ('2', '2', '1', 'merge');

INSERT IGNORE INTO `#__sdi_sys_relationtype` VALUES ('1', '1', '1', 'association');
INSERT IGNORE INTO `#__sdi_sys_relationtype` VALUES ('2', '2', '1', 'aggregation');
INSERT IGNORE INTO `#__sdi_sys_relationtype` VALUES ('3', '3', '1', 'composition');
INSERT IGNORE INTO `#__sdi_sys_relationtype` VALUES ('4', '4', '1', 'generalization');

INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('1', '1', '1', 'textarea');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('2', '2', '1', 'checkbox');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('3', '3', '1', 'radiobutton');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('4', '4', '1', 'list');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('5', '5', '1', 'textbox');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('6', '6', '1', 'date');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('7', '7', '1', 'datetime');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('8', '8', '1', 'gemet');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('9', '9', '1', 'upload');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('10', '10', '1', 'url');
INSERT IGNORE INTO `#__sdi_sys_rendertype` VALUES ('11', '11', '1', 'upload and url');

INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('1', '58dfe161-60c3-4b72-b768-e4a09bae8cdb', 'fulltext', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'fulltext', '1', '1', '5', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('2', '05b0fb40-459c-4ed2-a985-ce1611593969', 'resourcetype', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'resourcetype', '1', '1', '2', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('3', 'f839e3ae-d983-4366-b24f-2678f4cbe188', 'versions', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'versions', '1', '1', '2', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('4', '4d402bfd-b50a-42ae-8db4-af8ef940575b', 'resourcename', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'resourcename', '1', '1', '5', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('5', '2157fe2c-3705-4db9-a623-462ae38405fa', 'created', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'created', '1', '1', '6', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('6', '979a4e90-601e-46fe-9239-9080e4238c1e', 'published', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'published', '1', '1', '6', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('7', 'f761bc2d-57ac-4252-9cd2-17ae5e92793b', 'organism', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'organism', '1', '1', '4', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('8', 'b2a4c66a-f40c-473d-a03f-5b5e4f93f760', 'definedBoundary', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'definedBoundary', '1', '1', '4', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('9', '8a85ed55-6a9c-4af7-aba1-a3c0f8281453', 'isDownloadable', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'isDownloadable', '1', '1', '2', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('10', 'f80fcf1c-84df-4202-8838-6bbcb273a68d', 'isFree', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'isFree', '1', '1', '2', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('11', 'a9a44261-05da-4ee8-a3f2-4ec1c53bcb00', 'isOrderable', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'isOrderable', '1', '1', '2', null, '0', '0');
INSERT IGNORE INTO `#__sdi_searchcriteria` VALUES ('12', 'a9a44261-05da-4ee8-a3f2-4ec1c53bcb00', 'isViewable', '356', '2013-06-17 11:22:36', null, null, '0', '1', '0', '0000-00-00 00:00:00', 'isViewable', '1', '1', '2', null, '0', '0');

INSERT IGNORE INTO `#__sdi_namespace` VALUES ('1', '6df1fcd1-0a57-8b74-cd21-354dc5ef0b3d', 'gmd', '356', '2013-06-21 12:12:47', null, null, '1', '1', '0', '0000-00-00 00:00:00', 'gmd', 'gmd', 'http://www.isotc211.org/2005/gmd', '1', '1', '0');
INSERT IGNORE INTO `#__sdi_namespace` VALUES ('2', '016318b2-29ec-3a74-c161-14aa1b1d3b97', 'gco', '356', '2013-06-21 12:12:47', null, null, '2', '1', '0', '0000-00-00 00:00:00', 'gco', 'gco', 'http://www.isotc211.org/2005/gco', '1', '1', '0');
INSERT IGNORE INTO `#__sdi_namespace` VALUES ('3', '3e31cc00-8fa3-97a4-8510-dac7e4bac992', 'gml', '356', '2013-06-21 12:12:47', null, null, '3', '1', '0', '0000-00-00 00:00:00', 'gml', 'gml', 'http://www.opengis.net/gml', '1', '1', '0');
INSERT IGNORE INTO `#__sdi_namespace` VALUES ('4', 'd4b19594-af15-0b44-516b-22284be8dc66', 'sdi', '356', '2013-06-21 12:12:47', null, null, '4', '1', '0', '0000-00-00 00:00:00', 'sdi', 'sdi', 'http://www.easysdi.org/2011/sdi', '1', '1', '0');
INSERT IGNORE INTO `#__sdi_namespace` VALUES ('5', 'd84c3757-6471-49ed-a109-c8cef52840a8', 'catalog', '356', '2013-06-21 12:12:47', null, null, '5', '1', '0', '0000-00-00 00:00:00', 'catalog', 'catalog', 'http://www.easysdi.org/2011/sdi/catalog', '1', '1', '0');

INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('1', '1', '1', 'guid', '^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$', 'CharacterString', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('2', '2', '1', 'text', '', 'CharacterString', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('3', '3', '1', 'locale', '', null, null,'1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('4', '4', '1', 'number', '^[\-+]?[0-9.]+$', 'Decimal', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('5', '5', '1', 'date', '^([0-9]{4}-[0-9]{2}-[0-9]{2})$', 'Date', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('6', '6', '1', 'list', '', null, null,'1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('7', '7', '1', 'link', '((http:\/\/|https:\/\/|ftp:\/\/)(www.)?(([a-zA-Z0-9-]){2,}.){1,4}([a-zA-Z]){2,6}(\/([a-zA-Z-_\/.0-9#:?=&;,]*)?)?)|^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$', 'URL', '1','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('8', '8', '1', 'datetime', '^([0-9]{4}-[0-9]{2}-[0-9]{2})$', 'DateTime', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('9', '9', '1', 'textchoice', '', 'CharacterString', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('10', '10', '1', 'localechoice', '', null, null,'1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('11', '11', '1', 'gemet', null, null, null,'1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('12', '12', '1', 'distance', '^[\-+]?[0-9.]*[0-9]([Ee]\-?[0-9.]*[0-9])?$', 'Distance', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('13', '13', '1', 'integer', '^[\-+]?[0-9]+$', 'Integer', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('14', '14', '1', 'file', '', 'CharacterString', '2','1');
INSERT IGNORE INTO `#__sdi_sys_stereotype` VALUES ('15', '15', '1', 'geographicextent', null, null, null,'2');

INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('1', '1', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('2', '2', '1');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('3', '2', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('4', '3', '1');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('5', '3', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('6', '4', '1');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('7', '4', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('8', '5', '6');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('9', '6', '2');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('10', '6', '3');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('11', '6', '4');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('12', '7', '1');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('13', '7', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('14', '8', '6');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('16', '9', '4');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('17', '10', '4');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('18', '12', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('19', '13', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('21', '11', '8');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('22', '14', '9');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('23', '14', '10');
INSERT IGNORE INTO `#__sdi_sys_rendertype_stereotype` VALUES ('24', '14', '11');

INSERT IGNORE INTO `#__sdi_sys_rendertype_criteriatype` VALUES ('1', '3', '5');
INSERT IGNORE INTO `#__sdi_sys_rendertype_criteriatype` VALUES ('2', '3', '6');
INSERT IGNORE INTO `#__sdi_sys_rendertype_criteriatype` VALUES ('3', '3', '2');

INSERT IGNORE INTO `#__sdi_sys_searchtab` VALUES ('1', '1', '1', 'simple');
INSERT IGNORE INTO `#__sdi_sys_searchtab` VALUES ('2', '2', '1', 'advanced');
INSERT IGNORE INTO `#__sdi_sys_searchtab` VALUES ('3', '3', '1', 'hidden');
INSERT IGNORE INTO `#__sdi_sys_searchtab` VALUES ('4', '4', '1', 'none');

INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('1', '1', '1', 'farming');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('2', '2', '1', 'biota');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('3', '3', '1', 'bounderies');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('4', '4', '1', 'climatologyMeteorologyAtmosphere');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('5', '5', '1', 'economy');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('6', '6', '1', 'elevation');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('7', '7', '1', 'environment');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('8', '8', '1', 'geoscientificinformation');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('9', '9', '1', 'health');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('10', '10', '1', 'imageryBaseMapsEarthCover');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('11', '11', '1', 'intelligenceMilitary');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('12', '12', '1', 'inlandWaters');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('13', '13', '1', 'location');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('14', '14', '1', 'oceans');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('15', '15', '1', 'planningCadastre');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('16', '16', '1', 'society');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('17', '17', '1', 'structure');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('18', '18', '1', 'transportation');
INSERT IGNORE INTO `#__sdi_sys_topiccategory` VALUES ('19', '19', '1', 'utilitiesCommunication');


INSERT IGNORE INTO `#__sdi_sys_relationscope` VALUES ('1', '1', '1', 'editable');
INSERT IGNORE INTO `#__sdi_sys_relationscope` VALUES ('2', '2', '1', 'visible');
INSERT IGNORE INTO `#__sdi_sys_relationscope` VALUES ('3', '3', '1', 'hidden');

-- com_easysdi_map

INSERT IGNORE INTO `#__sdi_sys_maptool` (alias,ordering,state,name) 
VALUES 
('googleearth',1,1,'Google Earth'),
('navigation',2,1,'Navigation'),
('zoom',3,1,'Zoom'),
('navigationhistory',4,1,'Navigation history'),
('zoomtoextent',5,1,'Zoom to extent'),
('measure',6,1,'Measure'),
('googlegeocoder',7,1,'Google Geocoder'),
('print',8,1,'Print'),
('addlayer',9,1,'Add layer'),
('removelayer',10,1,'Remove layer'),
('layerproperties',11,1,'Layer properties'),
('getfeatureinfo',12,1,'Get feature info'),
('layertree',13,1,'Layer tree'),
('scaleline',14,1,'Scale line'),
('mouseposition',15,1,'Mouse position'),
('wfslocator',16,1,'Wfs locator'),
('searchcatalog',17,1,'Catalog search'),
('layerdetailsheet',18,1,'Layer detail sheet'),
('layerdownload',19,1,'Layer download'),
('layerorder',20,1,'Layer order'),
('indoornavigation',21,1,'Indoor navigation')
;

-- com_easysdi_shop

INSERT IGNORE INTO `#__sdi_sys_propertytype` (ordering,state,value) 
VALUES 
(1,1,'list'),
(2,1,'multiplelist'),
(3,1,'checkbox'),
(4,1,'text'),
(5,1,'textarea'),
(6,1,'message')
;

INSERT IGNORE INTO `#__sdi_sys_servicetype` (ordering,state,value) 
VALUES 
(1,1,'physical'),
(2,1,'virtual')
;

INSERT IGNORE INTO `#__sdi_perimeter` (id,guid,alias,created_by,created, ordering, state, name, description, accessscope_id, perimetertype_id ) 
VALUES 
('1', '1a9f342c-bb1e-9bc4-dd19-38910dff0f59', 'freeperimeter', '356', '2013-07-23 09:16:11','1', '1', 'Free perimeter', '',1,1),
('2', '9adc6d4e-262a-d6e4-e152-6de437ba80ed', 'myperimeter', '356', '2013-07-23 09:16:11','2', '1', 'My perimeter', '',1,1)
;

INSERT IGNORE INTO `#__sdi_sys_server` VALUES ('1','1','1','geoserver' );
INSERT IGNORE INTO `#__sdi_sys_server` VALUES ('2','1','1','arcgisserver' );

INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('1', '1', '2');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('2', '1', '3');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('3', '1', '4');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('4', '1', '5');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('5', '1', '11');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('6', '2', '2');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('7', '2', '4');
INSERT IGNORE INTO `#__sdi_sys_server_serviceconnector` VALUES ('8', '2', '5');
