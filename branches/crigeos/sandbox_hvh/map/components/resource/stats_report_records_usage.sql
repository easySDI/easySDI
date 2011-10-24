CREATE TABLE #__easysdi_stats_report_records_usage (
id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
username VARCHAR(100) NOT NULL,
record_id INT NOT NULL,
access_time DATETIME NOT NULL,
filters VARCHAR(1000)
);