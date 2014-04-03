
INSERT INTO action_types ("ID_ACTION_TYPE", "NAME") VALUES (1, 'E-MAIL');
INSERT INTO action_types ("ID_ACTION_TYPE", "NAME") VALUES (2, 'RSS');


--
-- TOC entry 2987 (class 0 OID 32742)
-- Dependencies: 172
-- Data for Name: actions; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 2988 (class 0 OID 32745)
-- Dependencies: 173
-- Data for Name: alerts; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 2989 (class 0 OID 32751)
-- Dependencies: 174
-- Data for Name: holidays; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 2990 (class 0 OID 32754)
-- Dependencies: 175
-- Data for Name: http_methods; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO http_methods ("ID_HTTP_METHOD", "NAME") VALUES (1, 'GET');
INSERT INTO http_methods ("ID_HTTP_METHOD", "NAME") VALUES (2, 'POST');


--
-- TOC entry 2991 (class 0 OID 32757)
-- Dependencies: 176
-- Data for Name: job_agg_hour_log_entries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 2992 (class 0 OID 32770)
-- Dependencies: 177
-- Data for Name: job_agg_log_entries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 2993 (class 0 OID 32783)
-- Dependencies: 178
-- Data for Name: job_defaults; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (1, 'IS_PUBLIC', 'false', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (2, 'IS_AUTOMATIC', 'false', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (3, 'ALLOWS_REALTIME', 'true', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (4, 'TRIGGERS_ALERTS', 'false', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (5, 'TEST_INTERVAL', '3600', 'int');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (6, 'TIMEOUT', '30', 'int');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (7, 'BUSINESS_ERRORS', 'true', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (8, 'HTTP_ERRORS', 'true', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (9, 'SLA_START_TIME', '08:00:00', 'time');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (10, 'SLA_END_TIME', '18:00:00', 'time');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (11, 'RUN_SIMULATANEOUS', 'false', 'bool');
INSERT INTO job_defaults ("ID_PARAM", "COLUMN_NAME", "STRING_VALUE", "VALUE_TYPE") VALUES (12, 'SAVE_RESPONSE', 'false', 'bool');


--
-- TOC entry 2994 (class 0 OID 32786)
-- Dependencies: 179
-- Data for Name: jobs; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 2995 (class 0 OID 33367)
-- Dependencies: 252
-- Data for Name: jos_sdi_language; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (1, 0, 1, 'العربية', 'ar-DZ', 'ar', 'ara', 'ar', 'DZ', 'ara');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (3, 0, 1, 'български език', 'bg-BG', 'bg', 'bul', 'bg', 'BG', 'bul');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (4, 0, 1, 'català', 'ca-ES', 'ca', 'cat', 'ca', 'ES', 'cat');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (5, 0, 1, 'čeština', 'cs-CZ', 'cs', 'ces', 'cs', 'CZ', 'cze');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (6, 0, 1, 'dansk', 'da-DK', 'da', 'dan', 'da', 'DK', 'dan');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (7, 0, 1, 'Deutsch', 'de-DE', 'de', 'deu', 'de', 'DE', 'ger');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (8, 0, 1, 'ελληνικά', 'el-GR', 'el', 'ell', 'el', 'GR', 'gre');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (9, 0, 1, 'English (UK)', 'en-GB', 'en', 'eng', 'en', 'GB', 'eng');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (10, 0, 1, 'English (US)', 'en-US', 'en-US', 'eng', 'en', 'US', 'eng');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (11, 0, 1, 'español', 'es-ES', 'es', 'spa', 'es', 'ES', 'spa');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (12, 0, 1, 'eesti', 'et-EE', 'et', 'est', 'et', 'EE', 'est');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (13, 0, 1, 'euskara', 'eu-ES', 'eu', 'eus', 'eu', 'ES', 'baq');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (14, 0, 1, 'suomi', 'fi-FI', 'fi', 'fin', 'fi', 'FI', 'fin');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (15, 0, 1, 'Français', 'fr-FR', 'fr', 'fra', 'fr', 'FR', 'fre');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (16, 0, 1, 'Gaeilge', 'ga-IE', 'ga', 'gle', 'ga', 'IE', 'gle');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (17, 0, 1, 'hrvatski jezik', 'hr-HR', 'hr', 'scr', 'hr', 'HR', 'hrv');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (18, 0, 1, 'magyar', 'hu-HU', 'hu', 'hun', 'hu', 'HU', 'hun');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (19, 0, 1, 'italiano', 'it-IT', 'it', 'ita', 'it', 'IT', 'ita');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (20, 0, 1, 'lietuvių kalba', 'lt-LT', 'lt', 'lit', 'lt', 'LT', 'lit');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (21, 0, 1, 'latviešu valoda', 'lv-LV', 'lv', 'lav', 'lv', 'LV', 'lav');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (22, 0, 1, 'Malti', 'mt-MT', 'mt', 'mlt', 'mt', 'MT', 'mlt');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (23, 0, 1, 'Nederlands', 'nl-NL', 'nl', 'nld', 'nl', 'NL', 'dut');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (24, 0, 1, 'Norsk', 'no-NO', 'no', 'nor', 'no', 'NO', 'nor');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (25, 0, 1, 'język polski', 'pl-PL', 'pl', 'pol', 'pl', 'PL', 'pol');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (26, 0, 1, 'português', 'pt-PT', 'pt', 'por', 'pt', 'PT', 'por');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (27, 0, 1, 'română', 'ro-RO', 'ro', 'ron', 'ro', 'RO', 'rum');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (28, 0, 1, 'русский язык', 'ru-RU', 'ru', 'rus', 'ru', 'RU', 'rus');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (29, 0, 1, 'slovenčina', 'sk-SK', 'sk', 'slk', 'sk', 'SK', 'slo');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (30, 0, 1, 'Svenska', 'sv-SE', 'sv', 'swe', 'sv', 'SE', 'swe');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (31, 0, 1, 'Türkçe', 'tr-TR', 'tr', 'tur', 'tr', 'TR', 'tur');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (32, 0, 1, 'українська мова', 'uk-UA', 'uk', 'ukr', 'uk', 'UA', 'ukr');
INSERT INTO jos_sdi_language (id, ordering, state, value, code, gemet, "iso639-2T", "iso639-1", "iso3166-1-alpha2", "iso639-2B") VALUES (33, 0, 1, 'Chinese', 'zh-CN', 'zh-CN', 'zho', 'zh', 'CN', 'chi');


--
-- TOC entry 2996 (class 0 OID 33451)
-- Dependencies: 264
-- Data for Name: jos_sdi_namespace; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_namespace (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, prefix, uri, system, access, asset_id) VALUES (1, '6df1fcd1-0a57-8b74-cd21-354dc5ef0b3d', 'gmd', 356, '2013-06-21 12:12:47', NULL, NULL, 1, B'1', 0, '2013-06-21 12:12:47', 'gmd', 'gmd', 'http://www.isotc211.org/2005/gmd', B'1', 1, 0);
INSERT INTO jos_sdi_namespace (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, prefix, uri, system, access, asset_id) VALUES (2, '016318b2-29ec-3a74-c161-14aa1b1d3b97', 'gco', 356, '2013-06-21 12:12:47', NULL, NULL, 2, B'1', 0, '2013-06-21 12:12:47', 'gco', 'gco', 'http://www.isotc211.org/2005/gco', B'1', 1, 0);
INSERT INTO jos_sdi_namespace (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, prefix, uri, system, access, asset_id) VALUES (3, '3e31cc00-8fa3-97a4-8510-dac7e4bac992', 'gml', 356, '2013-06-21 12:12:47', NULL, NULL, 3, B'1', 0, '2013-06-21 12:12:47', 'gml', 'gml', 'http://www.opengis.net/gml', B'1', 1, 0);
INSERT INTO jos_sdi_namespace (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, prefix, uri, system, access, asset_id) VALUES (4, 'd4b19594-af15-0b44-516b-22284be8dc66', 'sdi', 356, '2013-06-21 12:12:47', NULL, NULL, 4, B'1', 0, '2013-06-21 12:12:47', 'sdi', 'sdi', 'http://www.easysdi.org/2011/sdi', B'1', 1, 0);
INSERT INTO jos_sdi_namespace (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, prefix, uri, system, access, asset_id) VALUES (5, 'd84c3757-6471-49ed-a109-c8cef52840a8', 'catalog', 356, '2013-06-21 12:12:47', NULL, NULL, 5, B'1', 0, '2013-06-21 12:12:47', 'catalog', 'catalog', 'http://www.easysdi.org/2011/sdi/catalog', B'1', 1, 0);


--
-- TOC entry 2997 (class 0 OID 33509)
-- Dependencies: 270
-- Data for Name: jos_sdi_perimeter; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_perimeter (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, description, accessscope_id, perimetertype_id, wfsservice_id, wfsservicetype_id, featuretypename, prefix, namespace, featuretypefieldid, featuretypefieldname, featuretypefieldsurface, featuretypefielddescription, featuretypefieldgeometry, featuretypefieldresource, wmsservice_id, wmsservicetype_id, layername, access, asset_id) VALUES (1, '1a9f342c-bb1e-9bc4-dd19-38910dff0f59', 'freeperimeter', 356, '2013-07-23 09:16:11', NULL, NULL, 1, B'1', 0, '0002-11-30 00:00:00', 'Free perimeter', '', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0);
INSERT INTO jos_sdi_perimeter (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, description, accessscope_id, perimetertype_id, wfsservice_id, wfsservicetype_id, featuretypename, prefix, namespace, featuretypefieldid, featuretypefieldname, featuretypefieldsurface, featuretypefielddescription, featuretypefieldgeometry, featuretypefieldresource, wmsservice_id, wmsservicetype_id, layername, access, asset_id) VALUES (2, '9adc6d4e-262a-d6e4-e152-6de437ba80ed', 'myperimeter', 356, '2013-07-23 09:16:11', NULL, NULL, 2, B'1', 0, '0002-11-30 00:00:00', 'My perimeter', '', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0);


--
-- TOC entry 2998 (class 0 OID 33685)
-- Dependencies: 292
-- Data for Name: jos_sdi_searchcriteria; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (1, '58dfe161-60c3-4b72-b768-e4a09bae8cdb', 'fulltext', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'fulltext', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (2, '05b0fb40-459c-4ed2-a985-ce1611593969', 'resourcetype', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'resourcetype', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (3, 'f839e3ae-d983-4366-b24f-2678f4cbe188', 'versions', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'versions', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (4, '4d402bfd-b50a-42ae-8db4-af8ef940575b', 'resourcename', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'resourcename', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (5, '2157fe2c-3705-4db9-a623-462ae38405fa', 'created', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'created', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (6, '979a4e90-601e-46fe-9239-9080e4238c1e', 'published', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'published', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (7, 'f761bc2d-57ac-4252-9cd2-17ae5e92793b', 'organism', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'organism', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (8, 'b2a4c66a-f40c-473d-a03f-5b5e4f93f760', 'definedBoundary', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'definedBoundary', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (9, '8a85ed55-6a9c-4af7-aba1-a3c0f8281453', 'isDownloadable', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'isDownloadable', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (10, 'f80fcf1c-84df-4202-8838-6bbcb273a68d', 'isFree', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'isFree', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (11, 'a9a44261-05da-4ee8-a3f2-4ec1c53bcb00', 'isOrderable', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'isOrderable', B'1', 1, 1, NULL, 0, 0);
INSERT INTO jos_sdi_searchcriteria (id, guid, alias, created_by, created, modified_by, modified, ordering, state, checked_out, checked_out_time, name, issystem, criteriatype_id, rendertype_id, relation_id, access, asset_id) VALUES (12, 'a9a44261-05da-4ee8-a3f2-4ec1c53bcb00', 'isViewable', 356, '2013-06-17 11:22:36', NULL, NULL, 0, 1, 0, '2013-06-21 12:12:47', 'isViewable', B'1', 1, 1, NULL, 0, 0);


--
-- TOC entry 2999 (class 0 OID 33699)
-- Dependencies: 294
-- Data for Name: jos_sdi_sys_accessscope; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_accessscope (id, ordering, state, value) VALUES (1, 1, 1, 'public');
INSERT INTO jos_sdi_sys_accessscope (id, ordering, state, value) VALUES (2, 2, 1, 'organism');
INSERT INTO jos_sdi_sys_accessscope (id, ordering, state, value) VALUES (3, 3, 1, 'user');


--
-- TOC entry 3000 (class 0 OID 33704)
-- Dependencies: 295
-- Data for Name: jos_sdi_sys_addresstype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_addresstype (id, ordering, state, value) VALUES (1, 1, 1, 'contact');
INSERT INTO jos_sdi_sys_addresstype (id, ordering, state, value) VALUES (2, 2, 1, 'billing');
INSERT INTO jos_sdi_sys_addresstype (id, ordering, state, value) VALUES (3, 3, 1, 'delivry');


--
-- TOC entry 3001 (class 0 OID 33708)
-- Dependencies: 296
-- Data for Name: jos_sdi_sys_authenticationconnector; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_authenticationconnector (id, ordering, state, authenticationlevel_id, value) VALUES (1, 1, 1, 1, 'HTTPBasic');
INSERT INTO jos_sdi_sys_authenticationconnector (id, ordering, state, authenticationlevel_id, value) VALUES (2, 2, 1, 2, 'Geonetwork');


--
-- TOC entry 3002 (class 0 OID 33712)
-- Dependencies: 297
-- Data for Name: jos_sdi_sys_authenticationlevel; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_authenticationlevel (id, ordering, state, value) VALUES (1, 1, 1, 'resource');
INSERT INTO jos_sdi_sys_authenticationlevel (id, ordering, state, value) VALUES (2, 2, 1, 'service');


--
-- TOC entry 3003 (class 0 OID 33716)
-- Dependencies: 298
-- Data for Name: jos_sdi_sys_country; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (1, 1, 1, 'Afghanistan', 'AF', 'AFG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (2, 2, 1, 'Åland Islands', 'AX', 'ALA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (3, 3, 1, 'Albania', 'AL', 'ALB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (4, 4, 1, 'Algeria (El Djazaïr)', 'DZ', 'DZA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (5, 5, 1, 'American Samoa', 'AS', 'ASM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (6, 6, 1, 'Andorra', 'AD', 'AND');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (7, 7, 1, 'Angola', 'AO', 'AGO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (8, 8, 1, 'Anguilla', 'AI', 'AIA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (9, 9, 1, 'Antarctica', 'AQ', 'ATA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (10, 10, 1, 'Antigua and Barbuda', 'AG', 'ATG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (11, 11, 1, 'Argentina', 'AR', 'ARG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (12, 12, 1, 'Armenia', 'AM', 'ARM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (13, 13, 1, 'Aruba', 'AW', 'ABW');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (14, 14, 1, 'Australia', 'AU', 'AUS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (15, 15, 1, 'Austria', 'AT', 'AUT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (16, 16, 1, 'Azerbaijan', 'AZ', 'AZE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (17, 17, 1, 'Bahamas', 'BS', 'BHS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (18, 18, 1, 'Bahrain', 'BH', 'BHR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (19, 19, 1, 'Bangladesh', 'BD', 'BGD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (20, 20, 1, 'Barbados', 'BB', 'BRB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (21, 21, 1, 'Belarus', 'BY', 'BLR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (22, 22, 1, 'Belgium', 'BE', 'BEL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (23, 23, 1, 'Belize', 'BZ', 'BLZ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (24, 24, 1, 'Benin', 'BJ', 'BEN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (25, 25, 1, 'Bermuda', 'BM', 'BMU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (26, 26, 1, 'Bhutan', 'BT', 'BTN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (27, 27, 1, 'Bolivia', 'BO', 'BOL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (28, 28, 1, 'Bosnia and Herzegovina', 'BA', 'BIH');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (29, 29, 1, 'Botswana', 'BW', 'BWA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (30, 30, 1, 'Bouvet Island', 'BV', 'BVT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (31, 31, 1, 'Brazil', 'BR', 'BRA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (32, 32, 1, 'British Indian Ocean Territory', 'IO', 'IOT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (33, 33, 1, 'Brunei Darussalam', 'BN', 'BRN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (34, 34, 1, 'Bulgaria', 'BG', 'BGR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (35, 35, 1, 'Burkina Faso', 'BF', 'BFA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (36, 36, 1, 'Burundi', 'BI', 'BDI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (37, 37, 1, 'Cambodia', 'KH', 'KHM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (38, 38, 1, 'Cameroon', 'CM', 'CMR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (39, 39, 1, 'Canada', 'CA', 'CAN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (40, 40, 1, 'Cape Verde', 'CV', 'CPV');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (41, 41, 1, 'Cayman Islands', 'KY', 'CYM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (42, 42, 1, 'Central African Republic', 'CF', 'CAF');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (43, 43, 1, 'Chad (T''Chad)', 'TD', 'TCD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (44, 44, 1, 'Chile', 'CL', 'CHL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (45, 45, 1, 'China', 'CN', 'CHN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (46, 46, 1, 'Christmas Island', 'CX', 'CXR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (47, 47, 1, 'Cocos (Keeling) Islands', 'CC', 'CCK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (48, 48, 1, 'Colombia', 'CO', 'COL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (49, 49, 1, 'Comoros', 'KM', 'COM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (50, 50, 1, 'Congo, Republic Of', 'CG', 'COG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (51, 51, 1, 'Congo, The Democratic Republic of the (formerly Zaire)', 'CD', 'COD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (52, 52, 1, 'Cook Islands', 'CK', 'COK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (53, 53, 1, 'Costa Rica', 'CR', 'CRI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (54, 54, 1, 'CÔte D''Ivoire (Ivory Coast)', 'CI', 'CIV');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (55, 55, 1, 'Croatia (hrvatska)', 'HR', 'HRV');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (56, 56, 1, 'Cuba', 'CU', 'CUB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (57, 57, 1, 'Cyprus', 'CY', 'CYP');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (58, 58, 1, 'Czech Republic', 'CZ', 'CZE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (59, 59, 1, 'Denmark', 'DK', 'DNK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (60, 60, 1, 'Djibouti', 'DJ', 'DJI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (61, 61, 1, 'Dominica', 'DM', 'DMA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (62, 62, 1, 'Dominican Republic', 'DO', 'DOM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (63, 63, 1, 'Ecuador', 'EC', 'ECU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (64, 64, 1, 'Egypt', 'EG', 'EGY');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (65, 65, 1, 'El Salvador', 'SV', 'SLV');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (66, 66, 1, 'Equatorial Guinea', 'GQ', 'GNQ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (67, 67, 1, 'Eritrea', 'ER', 'ERI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (68, 68, 1, 'Estonia', 'EE', 'EST');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (69, 69, 1, 'Ethiopia', 'ET', 'ETH');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (70, 70, 1, 'Faeroe Islands', 'FO', 'FRO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (71, 71, 1, 'Falkland Islands (Malvinas)', 'FK', 'FLK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (72, 72, 1, 'Fiji', 'FJ', 'FJI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (73, 73, 1, 'Finland', 'FI', 'FIN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (74, 74, 1, 'France', 'FR', 'FRA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (75, 75, 1, 'French Guiana', 'GF', 'GUF');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (76, 76, 1, 'French Polynesia', 'PF', 'PYF');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (77, 77, 1, 'French Southern Territories', 'TF', 'ATF');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (78, 78, 1, 'Gabon', 'GA', 'GAB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (79, 79, 1, 'Gambia, The', 'GM', 'GMB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (80, 80, 1, 'Georgia', 'GE', 'GEO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (81, 81, 1, 'Germany (Deutschland)', 'DE', 'DEU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (82, 82, 1, 'Ghana', 'GH', 'GHA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (83, 83, 1, 'Gibraltar', 'GI', 'GIB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (84, 84, 1, 'Great Britain', 'GB', 'GBR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (85, 85, 1, 'Greece', 'GR', 'GRC');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (86, 86, 1, 'Greenland', 'GL', 'GRL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (87, 87, 1, 'Grenada', 'GD', 'GRD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (88, 88, 1, 'Guadeloupe', 'GP', 'GLP');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (89, 89, 1, 'Guam', 'GU', 'GUM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (90, 90, 1, 'Guatemala', 'GT', 'GTM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (91, 91, 1, 'Guinea', 'GN', 'GIN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (92, 92, 1, 'Guinea-bissau', 'GW', 'GNB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (93, 93, 1, 'Guyana', 'GY', 'GUY');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (94, 94, 1, 'Haiti', 'HT', 'HTI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (95, 95, 1, 'Heard Island and Mcdonald Islands', 'HM', 'HMD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (96, 96, 1, 'Honduras', 'HN', 'HND');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (97, 97, 1, 'Hong Kong (Special Administrative Region of China)', 'HK', 'HKG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (98, 98, 1, 'Hungary', 'HU', 'HUN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (99, 99, 1, 'Iceland', 'IS', 'ISL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (100, 100, 1, 'India', 'IN', 'IND');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (101, 101, 1, 'Indonesia', 'ID', 'IDN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (102, 102, 1, 'Iran (Islamic Republic of Iran)', 'IR', 'IRN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (103, 103, 1, 'Iraq', 'IQ', 'IRQ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (104, 104, 1, 'Ireland', 'IE', 'IRL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (105, 105, 1, 'Israel', 'IL', 'ISR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (106, 106, 1, 'Italy', 'IT', 'ITA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (107, 107, 1, 'Jamaica', 'JM', 'JAM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (108, 108, 1, 'Japan', 'JP', 'JPN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (109, 109, 1, 'Jordan (Hashemite Kingdom of Jordan)', 'JO', 'JOR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (110, 110, 1, 'Kazakhstan', 'KZ', 'KAZ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (111, 111, 1, 'Kenya', 'KE', 'KEN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (112, 112, 1, 'Kiribati', 'KI', 'KIR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (113, 113, 1, 'Korea (Democratic Peoples Republic pf [North] Korea)', 'KP', 'PRK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (114, 114, 1, 'Korea (Republic of [South] Korea)', 'KR', 'KOR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (115, 115, 1, 'Kuwait', 'KW', 'KWT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (116, 116, 1, 'Kyrgyzstan', 'KG', 'KGZ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (117, 117, 1, 'Lao People''s Democratic Republic', 'LA', 'LAO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (118, 118, 1, 'Latvia', 'LV', 'LVA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (119, 119, 1, 'Lebanon', 'LB', 'LBN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (120, 120, 1, 'Lesotho', 'LS', 'LSO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (121, 121, 1, 'Liberia', 'LR', 'LBR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (122, 122, 1, 'Libya (Libyan Arab Jamahirya)', 'LY', 'LBY');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (123, 123, 1, 'Liechtenstein (Fürstentum Liechtenstein)', 'LI', 'LIE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (124, 124, 1, 'Lithuania', 'LT', 'LTU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (125, 125, 1, 'Luxembourg', 'LU', 'LUX');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (126, 126, 1, 'Macao (Special Administrative Region of China)', 'MO', 'MAC');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (127, 127, 1, 'Macedonia (Former Yugoslav Republic of Macedonia)', 'MK', 'MKD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (128, 128, 1, 'Madagascar', 'MG', 'MDG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (129, 129, 1, 'Malawi', 'MW', 'MWI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (130, 130, 1, 'Malaysia', 'MY', 'MYS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (131, 131, 1, 'Maldives', 'MV', 'MDV');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (132, 132, 1, 'Mali', 'ML', 'MLI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (133, 133, 1, 'Malta', 'MT', 'MLT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (134, 134, 1, 'Marshall Islands', 'MH', 'MHL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (135, 135, 1, 'Martinique', 'MQ', 'MTQ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (136, 136, 1, 'Mauritania', 'MR', 'MRT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (137, 137, 1, 'Mauritius', 'MU', 'MUS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (138, 138, 1, 'Mayotte', 'YT', 'MYT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (139, 139, 1, 'Mexico', 'MX', 'MEX');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (140, 140, 1, 'Micronesia (Federated States of Micronesia)', 'FM', 'FSM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (141, 141, 1, 'Moldova', 'MD', 'MDA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (142, 142, 1, 'Monaco', 'MC', 'MCO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (143, 143, 1, 'Mongolia', 'MN', 'MNG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (144, 144, 1, 'Montserrat', 'MS', 'MSR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (145, 145, 1, 'Morocco', 'MA', 'MAR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (146, 146, 1, 'Mozambique (Moçambique)', 'MZ', 'MOZ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (147, 147, 1, 'Myanmar (formerly Burma)', 'MM', 'MMR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (148, 148, 1, 'Namibia', 'NA', 'NAM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (149, 149, 1, 'Nauru', 'NR', 'NRU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (150, 150, 1, 'Nepal', 'NP', 'NPL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (151, 151, 1, 'Netherlands', 'NL', 'NLD');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (152, 152, 1, 'Netherlands Antilles', 'AN', 'ANT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (153, 153, 1, 'New Caledonia', 'NC', 'NCL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (154, 154, 1, 'New Zealand', 'NZ', 'NZL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (155, 155, 1, 'Nicaragua', 'NI', 'NIC');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (156, 156, 1, 'Niger', 'NE', 'NER');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (157, 157, 1, 'Nigeria', 'NG', 'NGA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (158, 158, 1, 'Niue', 'NU', 'NIU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (159, 159, 1, 'Norfolk Island', 'NF', 'NFK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (160, 160, 1, 'Northern Mariana Islands', 'MP', 'MNP');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (161, 161, 1, 'Norway', 'NO', 'NOR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (162, 162, 1, 'Oman', 'OM', 'OMN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (163, 163, 1, 'Pakistan', 'PK', 'PAK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (164, 164, 1, 'Palau', 'PW', 'PLW');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (165, 165, 1, 'Palestinian Territories', 'PS', 'PSE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (166, 166, 1, 'Panama', 'PA', 'PAN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (167, 167, 1, 'Papua New Guinea', 'PG', 'PNG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (168, 168, 1, 'Paraguay', 'PY', 'PRY');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (169, 169, 1, 'Peru', 'PE', 'PER');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (170, 170, 1, 'Philippines', 'PH', 'PHL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (171, 171, 1, 'Pitcairn', 'PN', 'PCN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (172, 172, 1, 'Poland', 'PL', 'POL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (173, 173, 1, 'Portugal', 'PT', 'PRT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (174, 174, 1, 'Puerto Rico', 'PR', 'PRI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (175, 175, 1, 'Qatar', 'QA', 'QAT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (176, 176, 1, 'RÉunion', 'RE', 'REU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (177, 177, 1, 'Romania', 'RO', 'ROU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (178, 178, 1, 'Russian Federation', 'RU', 'RUS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (179, 179, 1, 'Rwanda', 'RW', 'RWA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (180, 180, 1, 'Saint Helena', 'SH', 'SHN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (181, 181, 1, 'Saint Kitts and Nevis', 'KN', 'KNA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (182, 182, 1, 'Saint Lucia', 'LC', 'LCA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (183, 183, 1, 'Saint Pierre and Miquelon', 'PM', 'SPM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (184, 184, 1, 'Saint Vincent and the Grenadines', 'VC', 'VCT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (185, 185, 1, 'Samoa (formerly Western Samoa)', 'WS', 'WSM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (186, 186, 1, 'San Marino (Republic of)', 'SM', 'SMR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (187, 187, 1, 'Sao Tome and Principe', 'ST', 'STP');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (188, 188, 1, 'Saudi Arabia (Kingdom of Saudi Arabia)', 'SA', 'SAU');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (189, 189, 1, 'Senegal', 'SN', 'SEN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (190, 190, 1, 'Serbia and Montenegro (formerly Yugoslavia)', 'CS', 'SCG');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (191, 191, 1, 'Seychelles', 'SC', 'SYC');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (192, 192, 1, 'Sierra Leone', 'SL', 'SLE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (193, 193, 1, 'Singapore', 'SG', 'SGP');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (194, 194, 1, 'Slovakia (Slovak Republic)', 'SK', 'SVK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (195, 195, 1, 'Slovenia', 'SI', 'SVN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (196, 196, 1, 'Solomon Islands', 'SB', 'SLB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (197, 197, 1, 'Somalia', 'SO', 'SOM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (198, 198, 1, 'South Africa (zuid Afrika)', 'ZA', 'ZAF');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (199, 199, 1, 'South Georgia and the South Sandwich Islands', 'GS', 'SGS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (200, 200, 1, 'Spain (españa)', 'ES', 'ESP');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (201, 201, 1, 'Sri Lanka', 'LK', 'LKA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (202, 202, 1, 'Sudan', 'SD', 'SDN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (203, 203, 1, 'Suriname', 'SR', 'SUR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (204, 204, 1, 'Svalbard and Jan Mayen', 'SJ', 'SJM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (205, 205, 1, 'Swaziland', 'SZ', 'SWZ');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (206, 206, 1, 'Sweden', 'SE', 'SWE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (207, 207, 1, 'Switzerland (Confederation of Helvetia)', 'CH', 'CHE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (208, 208, 1, 'Syrian Arab Republic', 'SY', 'SYR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (209, 209, 1, 'Taiwan ("Chinese Taipei" for IOC)', 'TW', 'TWN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (210, 210, 1, 'Tajikistan', 'TJ', 'TJK');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (211, 211, 1, 'Tanzania', 'TZ', 'TZA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (212, 212, 1, 'Thailand', 'TH', 'THA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (213, 213, 1, 'Timor-Leste (formerly East Timor)', 'TL', 'TLS');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (214, 214, 1, 'Togo', 'TG', 'TGO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (215, 215, 1, 'Tokelau', 'TK', 'TKL');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (216, 216, 1, 'Tonga', 'TO', 'TON');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (217, 217, 1, 'Trinidad and Tobago', 'TT', 'TTO');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (218, 218, 1, 'Tunisia', 'TN', 'TUN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (219, 219, 1, 'Turkey', 'TR', 'TUR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (220, 220, 1, 'Turkmenistan', 'TM', 'TKM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (221, 221, 1, 'Turks and Caicos Islands', 'TC', 'TCA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (222, 222, 1, 'Tuvalu', 'TV', 'TUV');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (223, 223, 1, 'Uganda', 'UG', 'UGA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (224, 224, 1, 'Ukraine', 'UA', 'UKR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (225, 225, 1, 'United Arab Emirates', 'AE', 'ARE');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (226, 226, 1, 'United Kingdom (Great Britain)', 'GB', 'GBR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (227, 227, 1, 'United States', 'US', 'USA');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (228, 228, 1, 'United States Minor Outlying Islands', 'UM', 'UMI');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (229, 229, 1, 'Uruguay', 'UY', 'URY');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (230, 230, 1, 'Uzbekistan', 'UZ', 'UZB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (231, 231, 1, 'Vanuatu', 'VU', 'VUT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (232, 232, 1, 'Vatican City (Holy See)', 'VA', 'VAT');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (233, 233, 1, 'Venezuela', 'VE', 'VEN');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (234, 234, 1, 'Viet Nam', 'VN', 'VNM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (235, 235, 1, 'Virgin Islands, British', 'VG', 'VGB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (236, 236, 1, 'Virgin Islands, U.S.', 'VI', 'VIR');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (237, 237, 1, 'Wallis and Futuna', 'WF', 'WLF');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (238, 238, 1, 'Western Sahara (formerly Spanish Sahara)', 'EH', 'ESH');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (239, 239, 1, 'Yemen (Arab Republic)', 'YE', 'YEM');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (240, 240, 1, 'Zambia', 'ZM', 'ZMB');
INSERT INTO jos_sdi_sys_country (id, ordering, state, name, iso2, iso3) VALUES (241, 241, 1, 'Zimbabwe', 'ZW', 'ZWE');


--
-- TOC entry 3004 (class 0 OID 33721)
-- Dependencies: 299
-- Data for Name: jos_sdi_sys_criteriatype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_criteriatype (id, ordering, state, value) VALUES (1, 1, 1, 'system');
INSERT INTO jos_sdi_sys_criteriatype (id, ordering, state, value) VALUES (2, 2, 1, 'relation');
INSERT INTO jos_sdi_sys_criteriatype (id, ordering, state, value) VALUES (3, 3, 1, 'csw');


--
-- TOC entry 3005 (class 0 OID 33726)
-- Dependencies: 300
-- Data for Name: jos_sdi_sys_entity; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_entity (id, ordering, state, value) VALUES (1, 1, 1, 'attribute');
INSERT INTO jos_sdi_sys_entity (id, ordering, state, value) VALUES (2, 2, 1, 'class');


--
-- TOC entry 3006 (class 0 OID 33731)
-- Dependencies: 301
-- Data for Name: jos_sdi_sys_exceptionlevel; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_exceptionlevel (id, ordering, state, value) VALUES (1, 1, 1, 'permissive');
INSERT INTO jos_sdi_sys_exceptionlevel (id, ordering, state, value) VALUES (2, 2, 1, 'restrictive');


--
-- TOC entry 3007 (class 0 OID 33735)
-- Dependencies: 302
-- Data for Name: jos_sdi_sys_importtype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_importtype (id, ordering, state, value) VALUES (1, 1, 1, 'replace');
INSERT INTO jos_sdi_sys_importtype (id, ordering, state, value) VALUES (2, 2, 1, 'merge');


--
-- TOC entry 3008 (class 0 OID 33740)
-- Dependencies: 303
-- Data for Name: jos_sdi_sys_isolanguage; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_isolanguage (id, ordering, state, value) VALUES (1, 1, 1, 'iso639-2T');
INSERT INTO jos_sdi_sys_isolanguage (id, ordering, state, value) VALUES (2, 2, 1, 'iso639-2B');
INSERT INTO jos_sdi_sys_isolanguage (id, ordering, state, value) VALUES (3, 3, 1, 'iso639-1');


--
-- TOC entry 3009 (class 0 OID 33745)
-- Dependencies: 304
-- Data for Name: jos_sdi_sys_loglevel; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (1, 1, 1, 'off');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (2, 2, 1, 'fatal');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (3, 3, 1, 'error');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (4, 4, 1, 'warning');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (5, 5, 1, 'info');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (6, 6, 1, 'debug');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (7, 7, 1, 'trace');
INSERT INTO jos_sdi_sys_loglevel (id, ordering, state, value) VALUES (8, 8, 1, 'All');


--
-- TOC entry 3010 (class 0 OID 33749)
-- Dependencies: 305
-- Data for Name: jos_sdi_sys_logroll; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_logroll (id, ordering, state, value) VALUES (1, 1, 1, 'daily');
INSERT INTO jos_sdi_sys_logroll (id, ordering, state, value) VALUES (2, 2, 1, 'weekly');
INSERT INTO jos_sdi_sys_logroll (id, ordering, state, value) VALUES (3, 3, 1, 'monthly');
INSERT INTO jos_sdi_sys_logroll (id, ordering, state, value) VALUES (4, 4, 1, 'annually');


--
-- TOC entry 3011 (class 0 OID 33753)
-- Dependencies: 306
-- Data for Name: jos_sdi_sys_maptool; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (1, 'googleearth', 1, 1, 'Google Earth');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (2, 'navigation', 2, 1, 'Navigation');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (3, 'zoom', 3, 1, 'Zoom');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (4, 'navigationhistory', 4, 1, 'Navigation history');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (5, 'zoomtoextent', 5, 1, 'Zoom to extent');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (6, 'measure', 6, 1, 'Measure');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (7, 'googlegeocoder', 7, 1, 'Google Geocoder');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (8, 'print', 8, 1, 'Print');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (9, 'addlayer', 9, 1, 'Add layer');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (10, 'removelayer', 10, 1, 'Remove layer');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (11, 'layerproperties', 11, 1, 'Layer properties');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (12, 'getfeatureinfo', 12, 1, 'Get feature info');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (13, 'layertree', 13, 1, 'Layer tree');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (14, 'scaleline', 14, 1, 'Scale line');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (15, 'mouseposition', 15, 1, 'Mouse position');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (16, 'wfslocator', 16, 1, 'Wfs locator');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (17, 'searchcatalog', 17, 1, 'Catalog search');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (18, 'layerdetailsheet', 18, 1, 'Layer detail sheet');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (19, 'layerdownload', 19, 1, 'Layer download');
INSERT INTO jos_sdi_sys_maptool (id, alias, ordering, state, name) VALUES (20, 'layerorder', 20, 1, 'Layer order');


--
-- TOC entry 3012 (class 0 OID 33757)
-- Dependencies: 307
-- Data for Name: jos_sdi_sys_metadatastate; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_metadatastate (id, ordering, state, value) VALUES (1, 1, 1, 'inprogress');
INSERT INTO jos_sdi_sys_metadatastate (id, ordering, state, value) VALUES (2, 2, 1, 'validated');
INSERT INTO jos_sdi_sys_metadatastate (id, ordering, state, value) VALUES (3, 3, 1, 'published');
INSERT INTO jos_sdi_sys_metadatastate (id, ordering, state, value) VALUES (4, 4, 1, 'archived');
INSERT INTO jos_sdi_sys_metadatastate (id, ordering, state, value) VALUES (5, 5, 1, 'trashed');


--
-- TOC entry 3013 (class 0 OID 33762)
-- Dependencies: 308
-- Data for Name: jos_sdi_sys_metadataversion; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_metadataversion (id, ordering, state, value) VALUES (1, 1, 1, 'all');
INSERT INTO jos_sdi_sys_metadataversion (id, ordering, state, value) VALUES (2, 2, 1, 'last');


--
-- TOC entry 3014 (class 0 OID 33767)
-- Dependencies: 309
-- Data for Name: jos_sdi_sys_operationcompliance; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (1, 1, 1, 1, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (2, 2, 1, 1, 2, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (3, 3, 1, 1, 3, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (4, 4, 1, 1, 4, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (5, 5, 1, 1, 5, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (6, 6, 1, 1, 6, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (7, 7, 1, 1, 7, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (8, 8, 1, 1, 8, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (9, 9, 1, 1, 9, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (10, 10, 1, 1, 10, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (11, 11, 1, 2, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (12, 12, 1, 2, 2, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (13, 13, 1, 2, 3, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (14, 14, 1, 2, 4, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (15, 15, 1, 2, 5, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (16, 16, 1, 2, 6, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (17, 17, 1, 2, 7, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (18, 18, 1, 2, 8, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (19, 19, 1, 2, 9, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (20, 20, 1, 2, 10, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (21, 21, 1, 6, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (22, 22, 1, 6, 11, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (23, 23, 1, 6, 12, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (24, 24, 1, 7, 13, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (25, 25, 1, 7, 14, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (26, 26, 1, 7, 15, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (27, 27, 1, 7, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (28, 28, 1, 7, 16, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (29, 29, 1, 3, 17, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (30, 30, 1, 3, 18, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (31, 31, 1, 3, 19, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (32, 32, 1, 3, 20, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (33, 33, 1, 3, 21, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (34, 34, 1, 3, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (35, 35, 1, 4, 17, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (36, 36, 1, 4, 18, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (37, 37, 1, 4, 19, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (38, 38, 1, 4, 20, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (39, 39, 1, 4, 21, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (40, 40, 1, 4, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (41, 41, 1, 5, 17, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (42, 42, 1, 5, 18, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (43, 43, 1, 5, 19, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (44, 44, 1, 5, 20, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (45, 45, 1, 5, 21, B'0');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (46, 46, 1, 5, 1, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (47, 47, 1, 3, 12, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (48, 48, 1, 4, 12, B'1');
INSERT INTO jos_sdi_sys_operationcompliance (id, ordering, state, servicecompliance_id, serviceoperation_id, implemented) VALUES (49, 49, 1, 5, 12, B'1');


--
-- TOC entry 3015 (class 0 OID 33772)
-- Dependencies: 310
-- Data for Name: jos_sdi_sys_orderstate; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (1, 1, 1, 'archived');
INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (2, 2, 1, 'historized');
INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (3, 3, 1, 'finish');
INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (4, 4, 1, 'await');
INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (5, 5, 1, 'progress');
INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (6, 6, 1, 'sent');
INSERT INTO jos_sdi_sys_orderstate (id, ordering, state, value) VALUES (7, 7, 1, 'saved');


--
-- TOC entry 3016 (class 0 OID 33777)
-- Dependencies: 311
-- Data for Name: jos_sdi_sys_ordertype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_ordertype (id, ordering, state, value) VALUES (1, 1, 1, 'order');
INSERT INTO jos_sdi_sys_ordertype (id, ordering, state, value) VALUES (2, 2, 1, 'estimate');
INSERT INTO jos_sdi_sys_ordertype (id, ordering, state, value) VALUES (3, 3, 1, 'draft');


--
-- TOC entry 3017 (class 0 OID 33782)
-- Dependencies: 312
-- Data for Name: jos_sdi_sys_perimetertype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_perimetertype (id, ordering, state, value) VALUES (1, 1, 1, 'extraction');
INSERT INTO jos_sdi_sys_perimetertype (id, ordering, state, value) VALUES (2, 2, 1, 'download');
INSERT INTO jos_sdi_sys_perimetertype (id, ordering, state, value) VALUES (3, 3, 1, 'both');


--
-- TOC entry 3018 (class 0 OID 33787)
-- Dependencies: 313
-- Data for Name: jos_sdi_sys_pricing; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_pricing (id, ordering, state, value) VALUES (1, 1, 1, 'free');
INSERT INTO jos_sdi_sys_pricing (id, ordering, state, value) VALUES (2, 2, 1, 'fee');


--
-- TOC entry 3019 (class 0 OID 33792)
-- Dependencies: 314
-- Data for Name: jos_sdi_sys_productmining; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_productmining (id, ordering, state, value) VALUES (1, 1, 1, 'automatic');
INSERT INTO jos_sdi_sys_productmining (id, ordering, state, value) VALUES (2, 2, 1, 'manual');


--
-- TOC entry 3020 (class 0 OID 33797)
-- Dependencies: 315
-- Data for Name: jos_sdi_sys_productstate; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_productstate (id, ordering, state, value) VALUES (1, 1, 1, 'available');
INSERT INTO jos_sdi_sys_productstate (id, ordering, state, value) VALUES (2, 2, 1, 'await');
INSERT INTO jos_sdi_sys_productstate (id, ordering, state, value) VALUES (3, 3, 1, 'sent');


--
-- TOC entry 3021 (class 0 OID 33802)
-- Dependencies: 316
-- Data for Name: jos_sdi_sys_productstorage; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_productstorage (id, ordering, state, value) VALUES (1, 1, 1, 'upload');
INSERT INTO jos_sdi_sys_productstorage (id, ordering, state, value) VALUES (2, 2, 1, 'url');
INSERT INTO jos_sdi_sys_productstorage (id, ordering, state, value) VALUES (3, 3, 1, 'zoning');


--
-- TOC entry 3022 (class 0 OID 33807)
-- Dependencies: 317
-- Data for Name: jos_sdi_sys_propertytype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_propertytype (id, ordering, state, value) VALUES (1, 1, 1, 'list');
INSERT INTO jos_sdi_sys_propertytype (id, ordering, state, value) VALUES (2, 2, 1, 'multiplelist');
INSERT INTO jos_sdi_sys_propertytype (id, ordering, state, value) VALUES (3, 3, 1, 'checkbox');
INSERT INTO jos_sdi_sys_propertytype (id, ordering, state, value) VALUES (4, 4, 1, 'text');
INSERT INTO jos_sdi_sys_propertytype (id, ordering, state, value) VALUES (5, 5, 1, 'textarea');
INSERT INTO jos_sdi_sys_propertytype (id, ordering, state, value) VALUES (6, 6, 1, 'message');


--
-- TOC entry 3023 (class 0 OID 33811)
-- Dependencies: 318
-- Data for Name: jos_sdi_sys_proxytype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_proxytype (id, ordering, state, value) VALUES (1, 1, 1, 'harvest');
INSERT INTO jos_sdi_sys_proxytype (id, ordering, state, value) VALUES (2, 2, 1, 'relay');
INSERT INTO jos_sdi_sys_proxytype (id, ordering, state, value) VALUES (3, 3, 1, 'aggregate');


--
-- TOC entry 3024 (class 0 OID 33815)
-- Dependencies: 319
-- Data for Name: jos_sdi_sys_relationscope; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_relationscope (id, ordering, state, value) VALUES (1, 1, 1, 'editable');
INSERT INTO jos_sdi_sys_relationscope (id, ordering, state, value) VALUES (2, 2, 1, 'visible');
INSERT INTO jos_sdi_sys_relationscope (id, ordering, state, value) VALUES (3, 3, 1, 'hidden');


--
-- TOC entry 3025 (class 0 OID 33820)
-- Dependencies: 320
-- Data for Name: jos_sdi_sys_relationtype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_relationtype (id, ordering, state, value) VALUES (1, 1, 1, 'association');
INSERT INTO jos_sdi_sys_relationtype (id, ordering, state, value) VALUES (2, 2, 1, 'aggregation');
INSERT INTO jos_sdi_sys_relationtype (id, ordering, state, value) VALUES (3, 3, 1, 'composition');
INSERT INTO jos_sdi_sys_relationtype (id, ordering, state, value) VALUES (4, 4, 1, 'generalization');


--
-- TOC entry 3026 (class 0 OID 33825)
-- Dependencies: 321
-- Data for Name: jos_sdi_sys_rendertype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_rendertype (id, ordering, state, value) VALUES (1, 1, 1, 'textarea');
INSERT INTO jos_sdi_sys_rendertype (id, ordering, state, value) VALUES (2, 2, 1, 'checkbox');
INSERT INTO jos_sdi_sys_rendertype (id, ordering, state, value) VALUES (3, 3, 1, 'radiobutton');
INSERT INTO jos_sdi_sys_rendertype (id, ordering, state, value) VALUES (4, 4, 1, 'list');
INSERT INTO jos_sdi_sys_rendertype (id, ordering, state, value) VALUES (5, 5, 1, 'textbox');
INSERT INTO jos_sdi_sys_rendertype (id, ordering, state, value) VALUES (6, 6, 1, 'date');


--
-- TOC entry 3027 (class 0 OID 33830)
-- Dependencies: 322
-- Data for Name: jos_sdi_sys_rendertype_criteriatype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_rendertype_criteriatype (id, criteriatype_id, rendertype_id) VALUES (1, 3, 5);
INSERT INTO jos_sdi_sys_rendertype_criteriatype (id, criteriatype_id, rendertype_id) VALUES (2, 3, 6);


--
-- TOC entry 3028 (class 0 OID 33833)
-- Dependencies: 323
-- Data for Name: jos_sdi_sys_rendertype_stereotype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (1, 1, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (2, 2, 1);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (3, 2, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (4, 3, 1);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (5, 3, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (6, 4, 1);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (7, 4, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (8, 5, 6);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (9, 6, 2);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (10, 6, 3);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (11, 6, 4);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (12, 7, 1);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (13, 7, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (14, 8, 6);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (16, 9, 4);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (17, 10, 4);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (18, 12, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (19, 13, 5);
INSERT INTO jos_sdi_sys_rendertype_stereotype (id, stereotype_id, rendertype_id) VALUES (20, 14, 5);


--
-- TOC entry 3029 (class 0 OID 33836)
-- Dependencies: 324
-- Data for Name: jos_sdi_sys_role; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (1, 1, 1, 'member');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (2, 2, 1, 'resourcemanager');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (3, 3, 1, 'metadataresponsible');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (4, 4, 1, 'metadataeditor');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (5, 5, 1, 'diffusionmanager');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (6, 6, 1, 'previewmanager');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (7, 7, 1, 'extractionresponsible');
INSERT INTO jos_sdi_sys_role (id, ordering, state, value) VALUES (8, 8, 1, 'ordereligible');


--
-- TOC entry 3030 (class 0 OID 33841)
-- Dependencies: 325
-- Data for Name: jos_sdi_sys_searchtab; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_searchtab (id, ordering, state, value) VALUES (1, 1, 1, 'simple');
INSERT INTO jos_sdi_sys_searchtab (id, ordering, state, value) VALUES (2, 2, 1, 'advanced');
INSERT INTO jos_sdi_sys_searchtab (id, ordering, state, value) VALUES (3, 3, 1, 'hidden');
INSERT INTO jos_sdi_sys_searchtab (id, ordering, state, value) VALUES (4, 4, 1, 'none');


--
-- TOC entry 3031 (class 0 OID 33846)
-- Dependencies: 326
-- Data for Name: jos_sdi_sys_servicecompliance; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (1, 1, 1, 1, 7, B'1', B'1', B'0', B'1');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (2, 2, 1, 1, 8, B'1', B'1', B'0', B'1');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (3, 3, 1, 2, 2, B'1', B'1', B'1', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (4, 4, 1, 2, 3, B'1', B'1', B'1', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (5, 5, 1, 2, 4, B'1', B'1', B'1', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (6, 6, 1, 3, 1, B'1', B'1', B'0', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (7, 7, 1, 4, 1, B'1', B'1', B'1', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (8, 8, 1, 11, 2, B'1', B'1', B'1', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (9, 9, 1, 11, 3, B'1', B'1', B'1', B'0');
INSERT INTO jos_sdi_sys_servicecompliance (id, ordering, state, serviceconnector_id, serviceversion_id, implemented, relayable, aggregatable, harvestable) VALUES (10, 10, 1, 11, 4, B'1', B'1', B'1', B'0');


--
-- TOC entry 3032 (class 0 OID 33854)
-- Dependencies: 327
-- Data for Name: jos_sdi_sys_servicecon_authenticationcon; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (1, 1, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (2, 1, 2);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (3, 2, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (4, 3, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (5, 4, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (6, 5, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (7, 6, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (8, 7, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (9, 8, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (10, 9, 1);
INSERT INTO jos_sdi_sys_servicecon_authenticationcon (id, serviceconnector_id, authenticationconnector_id) VALUES (11, 10, 1);


--
-- TOC entry 3033 (class 0 OID 33857)
-- Dependencies: 328
-- Data for Name: jos_sdi_sys_serviceconnector; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (1, 1, 1, 'CSW');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (2, 2, 1, 'WMS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (3, 3, 1, 'WMTS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (4, 4, 1, 'WFS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (5, 5, 0, 'WCS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (6, 6, 0, 'WCPS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (7, 7, 0, 'SOS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (8, 8, 0, 'SPS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (9, 9, 0, 'WPS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (10, 10, 0, 'OLS');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (11, 11, 1, 'WMSC');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (12, 12, 0, 'Bing');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (13, 13, 0, 'Google');
INSERT INTO jos_sdi_sys_serviceconnector (id, ordering, state, value) VALUES (14, 14, 0, 'OSM');


--
-- TOC entry 3034 (class 0 OID 33861)
-- Dependencies: 329
-- Data for Name: jos_sdi_sys_serviceoperation; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (1, 1, 1, 'GetCapabilities');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (2, 2, 1, 'GetRecords');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (3, 3, 1, 'GetRecordById');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (4, 4, 1, 'DescribeRecord');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (5, 5, 1, 'TransactionInsert');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (6, 6, 1, 'TransactionUpdate');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (7, 7, 1, 'TransactionReplace');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (8, 8, 1, 'TransactionDelete');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (9, 9, 1, 'GetDomain');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (10, 10, 1, 'Harvest');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (11, 11, 1, 'GetTile');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (12, 12, 1, 'GetFeatureInfo');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (13, 13, 1, 'DescribeFeatureType');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (14, 14, 1, 'GetFeature');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (15, 15, 1, 'LockFeature');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (16, 16, 1, 'GetFeatureWithLock');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (17, 17, 1, 'GetMap');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (18, 18, 1, 'GetLegendGraphic');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (19, 19, 1, 'DescribeLayer');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (20, 20, 1, 'GetStyles');
INSERT INTO jos_sdi_sys_serviceoperation (id, ordering, state, value) VALUES (21, 21, 1, 'PutStyles');


--
-- TOC entry 3035 (class 0 OID 33865)
-- Dependencies: 330
-- Data for Name: jos_sdi_sys_servicescope; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_servicescope (id, ordering, state, value) VALUES (1, 1, 1, 'all');
INSERT INTO jos_sdi_sys_servicescope (id, ordering, state, value) VALUES (2, 2, 1, 'organism');
INSERT INTO jos_sdi_sys_servicescope (id, ordering, state, value) VALUES (3, 3, 1, 'none');


--
-- TOC entry 3036 (class 0 OID 33870)
-- Dependencies: 331
-- Data for Name: jos_sdi_sys_servicetype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_servicetype (id, ordering, state, value) VALUES (1, 1, 1, 'physical');
INSERT INTO jos_sdi_sys_servicetype (id, ordering, state, value) VALUES (2, 2, 1, 'virtual');


--
-- TOC entry 3037 (class 0 OID 33875)
-- Dependencies: 332
-- Data for Name: jos_sdi_sys_serviceversion; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (1, 1, 1, '1.0.0');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (2, 2, 1, '1.1.0');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (3, 3, 1, '1.1.1');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (4, 4, 1, '1.3.0');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (5, 5, 1, '2.0');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (6, 6, 1, '2.0.0');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (7, 7, 1, '2.0.1');
INSERT INTO jos_sdi_sys_serviceversion (id, ordering, state, value) VALUES (8, 8, 1, '2.0.2');


--
-- TOC entry 3038 (class 0 OID 33879)
-- Dependencies: 333
-- Data for Name: jos_sdi_sys_spatialoperator; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_spatialoperator (id, ordering, state, value) VALUES (1, 1, 1, 'within');
INSERT INTO jos_sdi_sys_spatialoperator (id, ordering, state, value) VALUES (2, 3, 1, 'touch');


--
-- TOC entry 3039 (class 0 OID 33884)
-- Dependencies: 334
-- Data for Name: jos_sdi_sys_stereotype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (1, 1, 1, 'guid', '([A-Z0-9]{8}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{4}|-|[A-Z0-9]{12})', 'CharacterString', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (2, 2, 1, 'text', '', 'CharacterString', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (3, 3, 1, 'locale', '', NULL, NULL, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (4, 4, 1, 'number', '[0-9.-]', 'Decimal', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (5, 5, 1, 'date', '([0-9]{4}-[0-9]{2}-[0-9]{2})', 'Date', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (6, 6, 1, 'list', '', NULL, NULL, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (7, 7, 1, 'link', '((https?://)?([w.-]+).([a-z.]{2,6})([/w .#:+?%=&;,]*)*/?)', 'URL', 1, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (8, 8, 1, 'datetime', '([0-9]{4}-[0-9]{2}-[0-9]{2})', 'DateTime', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (9, 9, 1, 'textchoice', '', 'CharacterString', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (10, 10, 1, 'localechoice', '', NULL, NULL, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (11, 11, 1, 'gemet', NULL, NULL, NULL, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (12, 12, 1, 'distance', '[0-9.-]', 'Distance', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (13, 13, 1, 'integer', '[0-9.-]', 'Integer', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (14, 14, 1, 'file', '', 'CharacterString', 2, 1);
INSERT INTO jos_sdi_sys_stereotype (id, ordering, state, value, defaultpattern, isocode, namespace_id, entity_id) VALUES (15, 15, 1, 'geographicextent', NULL, NULL, NULL, 2);


--
-- TOC entry 3040 (class 0 OID 33892)
-- Dependencies: 335
-- Data for Name: jos_sdi_sys_topiccategory; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (1, 1, 1, 'farming');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (2, 2, 1, 'biota');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (3, 3, 1, 'bounderies');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (4, 4, 1, 'climatologyMeteorologyAtmosphere');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (5, 5, 1, 'economy');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (6, 6, 1, 'elevation');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (7, 7, 1, 'environment');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (8, 8, 1, 'geoscientificinformation');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (9, 9, 1, 'health');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (10, 10, 1, 'imageryBaseMapsEarthCover');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (11, 11, 1, 'intelligenceMilitary');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (12, 12, 1, 'inlandWaters');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (13, 13, 1, 'location');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (14, 14, 1, 'oceans');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (15, 15, 1, 'planningCadastre');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (16, 16, 1, 'society');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (17, 17, 1, 'structure');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (18, 18, 1, 'transportation');
INSERT INTO jos_sdi_sys_topiccategory (id, ordering, state, value) VALUES (19, 19, 1, 'utilitiesCommunication');


--
-- TOC entry 3041 (class 0 OID 33897)
-- Dependencies: 336
-- Data for Name: jos_sdi_sys_unit; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_unit (id, ordering, state, alias, name) VALUES (1, 1, 1, 'm', 'meter');
INSERT INTO jos_sdi_sys_unit (id, ordering, state, alias, name) VALUES (2, 2, 1, 'dd', 'degree');


--
-- TOC entry 3042 (class 0 OID 33901)
-- Dependencies: 337
-- Data for Name: jos_sdi_sys_versiontype; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO jos_sdi_sys_versiontype (id, ordering, state, value) VALUES (1, 1, 1, 'all');
INSERT INTO jos_sdi_sys_versiontype (id, ordering, state, value) VALUES (2, 2, 1, 'lastPublishedVersion');


--
-- TOC entry 3043 (class 0 OID 34188)
-- Dependencies: 372
-- Data for Name: last_ids; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('ACTIONS', 2);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('ALERTS', 161);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('HTTP_METHODS', 3);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('JOBS', 45);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('JOB_DEFAULTS', 11);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('LAST_QUERY_RESULTS', 39);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('LOG_ENTRIES', 1046);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('OVERVIEW_PAGE', 18);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('OVERVIEW_QUERIES', 8);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('QUERIES', 94);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('QUERY_VALIDATION_RESULTS', 12);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('QUERY_VALIDATION_SETTINGS', 48);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('SERVICE_METHODS', 10);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('SERVICE_TYPES', 8);
INSERT INTO last_ids ("TABLE_NAME", "LAST_ID") VALUES ('STATUSES', 5);


--
-- TOC entry 3044 (class 0 OID 34192)
-- Dependencies: 373
-- Data for Name: last_query_results; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3045 (class 0 OID 34198)
-- Dependencies: 374
-- Data for Name: log_entries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3046 (class 0 OID 34204)
-- Dependencies: 375
-- Data for Name: overview_page; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3047 (class 0 OID 34208)
-- Dependencies: 376
-- Data for Name: overview_queries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3048 (class 0 OID 34211)
-- Dependencies: 377
-- Data for Name: periods; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3049 (class 0 OID 34223)
-- Dependencies: 378
-- Data for Name: queries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3050 (class 0 OID 34227)
-- Dependencies: 379
-- Data for Name: query_agg_hour_log_entries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3051 (class 0 OID 34240)
-- Dependencies: 380
-- Data for Name: query_agg_log_entries; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3052 (class 0 OID 34253)
-- Dependencies: 381
-- Data for Name: query_params; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3053 (class 0 OID 34259)
-- Dependencies: 382
-- Data for Name: query_validation_results; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3054 (class 0 OID 34265)
-- Dependencies: 383
-- Data for Name: query_validation_settings; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3055 (class 0 OID 34274)
-- Dependencies: 384
-- Data for Name: roles; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO roles ("ID_ROLE", "NAME", "RANK") VALUES (1, 'ROLE_ADMIN', 1);
INSERT INTO roles ("ID_ROLE", "NAME", "RANK") VALUES (2, 'ROLE_USER', 3);


--
-- TOC entry 3056 (class 0 OID 34277)
-- Dependencies: 385
-- Data for Name: service_methods; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (1, 'GetCapabilities');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (2, 'GetMap');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (3, 'GetFeature');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (4, 'GetRecordById');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (5, 'GetTile');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (6, 'GetRecords');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (7, 'GetCoverage');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (8, 'DescribeSensor');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (9, 'SOAP 1.1');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (10, 'SOAP 1.2');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (11, 'HTTP POST');
INSERT INTO service_methods ("ID_SERVICE_METHOD", "NAME") VALUES (12, 'HTTP GET');


--
-- TOC entry 3057 (class 0 OID 34280)
-- Dependencies: 386
-- Data for Name: service_types; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (1, 'WMS', '1.1.1');
INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (2, 'WFS', '1.1.0');
INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (4, 'WMTS', '1.0.0');
INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (5, 'CSW', '2.0.2');
INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (6, 'SOS', '1.0.0');
INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (7, 'WCS', '1.0.0');
INSERT INTO service_types ("ID_SERVICE_TYPE", "NAME", "VERSION") VALUES (8, 'ALL', '0');


--
-- TOC entry 3058 (class 0 OID 34283)
-- Dependencies: 387
-- Data for Name: service_types_methods; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (1, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (2, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (4, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (5, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (6, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (7, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 1);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (1, 2);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 2);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (2, 3);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 3);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (5, 4);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 4);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (4, 5);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 5);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (5, 6);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 6);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (7, 7);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 7);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (6, 8);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 8);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 9);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 10);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 11);
INSERT INTO service_types_methods ("ID_SERVICE_TYPE", "ID_SERVICE_METHOD") VALUES (8, 12);


--
-- TOC entry 3059 (class 0 OID 34286)
-- Dependencies: 388
-- Data for Name: sla; Type: TABLE DATA; Schema: joomla; Owner: postgres
--



--
-- TOC entry 3060 (class 0 OID 34291)
-- Dependencies: 389
-- Data for Name: statuses; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO statuses ("ID_STATUS", "NAME") VALUES (1, 'AVAILABLE');
INSERT INTO statuses ("ID_STATUS", "NAME") VALUES (2, 'OUT_OF_ORDER');
INSERT INTO statuses ("ID_STATUS", "NAME") VALUES (3, 'UNAVAILABLE');
INSERT INTO statuses ("ID_STATUS", "NAME") VALUES (4, 'NOT_TESTED');


--
-- TOC entry 3061 (class 0 OID 34294)
-- Dependencies: 390
-- Data for Name: users; Type: TABLE DATA; Schema: joomla; Owner: postgres
--

INSERT INTO users ("LOGIN", "PASSWORD", "ID_ROLE", "EXPIRATION", "ENABLED", "LOCKED") VALUES ('Admin', 'adm', 1, NULL, B'1', B'0');
INSERT INTO users ("LOGIN", "PASSWORD", "ID_ROLE", "EXPIRATION", "ENABLED", "LOCKED") VALUES ('user', 'usr', 2, NULL, B'1', B'0');


-- Completed on 2014-04-03 09:04:05

--
-- PostgreSQL database dump complete
--

