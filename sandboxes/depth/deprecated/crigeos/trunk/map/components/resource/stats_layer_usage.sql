CREATE TABLE #__easysdi_stats_layer_usage (
id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
layer_name VARCHAR(100) NOT NULL,
activations INT NOT NULL,
hits INT NOT NULL,
access_date DATE NOT NULL
);