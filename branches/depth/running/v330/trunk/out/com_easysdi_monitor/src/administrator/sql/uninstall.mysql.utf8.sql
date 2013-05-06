SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `action_types`;
DROP TABLE IF EXISTS `actions`;
DROP TABLE IF EXISTS `alerts`;
DROP TABLE IF EXISTS `holidays`;
DROP TABLE IF EXISTS `http_methods`;
DROP TABLE IF EXISTS `job_agg_hour_log_entries`;
DROP TABLE IF EXISTS `job_agg_log_entries`;
DROP TABLE IF EXISTS `job_defaults`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `last_ids`;
DROP TABLE IF EXISTS `last_query_results`;
DROP TABLE IF EXISTS `log_entries`;
DROP TABLE IF EXISTS `overview_page`;
DROP TABLE IF EXISTS `overview_queries`;
DROP TABLE IF EXISTS `periods`;
DROP TABLE IF EXISTS `queries`;
DROP TABLE IF EXISTS `query_agg_hour_log_entries`;
DROP TABLE IF EXISTS `query_agg_log_entries`;
DROP TABLE IF EXISTS `query_params`;
DROP TABLE IF EXISTS `query_validation_results`;
DROP TABLE IF EXISTS `query_validation_settings`;
DROP TABLE IF EXISTS `roles`;
DROP TABLE IF EXISTS `service_methods`;
DROP TABLE IF EXISTS `service_types`;
DROP TABLE IF EXISTS `service_types_methods`;
DROP TABLE IF EXISTS `sla`;
DROP TABLE IF EXISTS `statuses`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `#__sdi_monitor_exports`;


SET foreign_key_checks = 1;