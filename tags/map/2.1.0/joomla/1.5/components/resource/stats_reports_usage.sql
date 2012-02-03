CREATE TABLE #__easysdi_stats_reports_usage (
id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
type VARCHAR(100) NOT NULL,
spatial_uses INT NOT NULL,
non_spatial_uses INT NOT NULL,
access_date DATE NOT NULL
);