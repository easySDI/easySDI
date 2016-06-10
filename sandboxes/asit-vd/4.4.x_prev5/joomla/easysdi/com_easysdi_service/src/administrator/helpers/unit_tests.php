<?php
require('WmtsPhysicalService.php');
require('CswPhysicalService.php');
require('WmsPhysicalService.php');
require('WfsPhysicalService.php');

$data = Array(
	'cookbook:sld_cookbook_line' => Array(
		'westBoundLongitude' => 1,
		'tileMatrixSetList' => Array(
			'EPSG:4326' => Array(
				'EPSG:4326:0' => Array(
					'tileWidth' => 3
				),
				'EPSG:4326:2' => Array(
					'tileWidth' => 4
				),
			),
			'EPSG:900913' => Array(
				'EPSG:900913:0' => Array(
					'tileWidth' => 5
				),
				'EPSG:900913:2' => Array(
					'tileWidth' => 6
				),
			),
		)
	),
	'cookbook:sld_cookbook_point' => Array(
		'westBoundLongitude' => 2,
		'tileMatrixSetList' => Array()
	)
);

$wmts = new WmtsPhysicalService(1, 'http://v2.suite.opengeo.org/geoserver/gwc/service/wmts');
$wmts->getCapabilities();
$wmts->populate();
$wmts->sortLists();
$wmts->loadData($data);
$l_list = $wmts->getLayerList();
$tms_list = $l_list['cookbook:sld_cookbook_line']->getTileMatrixSetList();
$tm_list = $tms_list['EPSG:4326']->getTileMatrixList();
var_dump($tm_list);

/*$csw = new CswPhysicalService(2, 'http://depth.ch:8080/proxy_testagi/ogc/geonetwork');

$wms = new WmsPhysicalService(3, 'http://v2.suite.opengeo.org/geoserver/gwc/service/wms');
$wms->getCapabilities();
$wms->populate();
$wms->sortLists();
//var_dump($wms);

$wfs = new WfsPhysicalService(4, 'http://geoservices.brgm.fr/geologie');
$wfs->getCapabilities();
$wfs->populate();
$wfs->sortLists();
//var_dump($wfs);
*/




