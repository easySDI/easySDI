<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT_ADMINISTRATOR . '/helpers/WmtsPhysicalService.php');

class WmtsWebservice {
    /*
     * Entry point of the class
     * 
     * @param Array Usually the $_GET, or any associative array
     * @param Boolean Set to true to force a return value, results are echoed otherwise
     */

    public static function request($params) {
        switch ($params['method']) {
            case 'getWmtsLayerForm':
                echo WmtsWebservice::getWmtsLayerForm($params);
                break;
            case 'setWmtsLayerSettings':
                if (WmtsWebservice::setWmtsLayerSettings($params)) {
                    echo 'OK';
                }
                break;
            case 'deleteWmtsLayer':
                if (WmtsWebservice::deleteWmtsLayer($params)) {
                    echo 'OK';
                }
                break;
            default:
                echo 'Unknown method.';
                break;
        }
        die();
    }

    private static function getWmtsLayerForm($raw_GET) {
        $physicalServiceID = $raw_GET['physicalServiceID'];
        $virtualServiceID = $raw_GET['virtualServiceID'];
        $policyID = ('' == $raw_GET['policyID']) ? 0 : $raw_GET['policyID'];
        $layerID = $raw_GET['layerID'];

        $layerObj = WmtsWebservice::getWmtsLayerSettings(
                        $virtualServiceID, $physicalServiceID, $policyID, $layerID
        );

        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_sys_spatialoperator');
        $query->where('state = 1');
        $query->order('ordering');
        
        $db->setQuery($query);

        try {
            $db->execute();
            $resultset = $db->loadObjectList();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $html = '
		<div class="well">
			<table>
				<tr>
					<td></td>
					<td>
						<input type="text" name="northBoundLatitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_NORTH_BOUND_LATITUDE') . '" value="' . $layerObj->northBoundLatitude . '"/>
					</td>
					<td></td>
				</tr>
				<tr>
					<td>
						<input type="text" name="westBoundLongitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_WEST_BOUND_LONGITUDE') . '" value="' . $layerObj->westBoundLongitude . '"/>
					</td>
					<td></td>
					<td>
						<input type="text" name="eastBoundLongitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_EAST_BOUND_LONGITUDE') . '" value="' . $layerObj->eastBoundLongitude . '"/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="text" name="southBoundLatitude" placeholder="' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_SOUTH_BOUND_LATITUDE') . '" value="' . $layerObj->southBoundLatitude . '"/>
					</td>
					<td></td>
				</tr>
			</table>
			<br />
			<select name="spatialoperatorid">
				';
        foreach ($resultset as $spatialOperator) {
            $html .= '<option value="' . $spatialOperator->id . '" ' . (($spatialOperator->id == $layerObj->spatialOperator) ? 'selected="selected"' : '') . '>' . $spatialOperator->value . '</option>';
        }
        $html .= '</select>
		</div>
			<hr />
		';

        $html .= '
			<table class="table">
				<thead>
					<tr>
						<th>Tile matrix set</th>
						<th>Min scale denominator</th>
					</tr>
				</thead>
				<tbody>
		';
        $tms_identifier_list = Array();
        foreach ($layerObj->getTileMatrixSetList() as $tms) {
            $tms_identifier_list[] = $tms->identifier;
            $html .= '
				<tr>
					<td>' . $tms->identifier . '</td>
					<td>
						<select name="select[' . $tms->identifier . ']">
							<option value="">' . JText::_('COM_EASYSDI_SERVICE_WMTS_LAYER_TILE_MATRIX_LABEL') . '</option>
			';
            foreach ($tms->getTileMatrixList() as $tm) {
                $selected = '';
                if ($tm->identifier == $tms->maxTileMatrix) {
                    $selected = 'selected="selected"';
                }
                $html .= '
					<option value="' . $tm->identifier . '" ' . $selected . '>' . $tm->identifier . ' [' . number_format($tm->scaleDenominator, 3, '.', ' ') . ']</option>
				';
            }
            $html .= '
						</select>
						<input type="hidden" name="srs[' . $tms->identifier . ']" value="' . $tms->srs . '"/>
					</td>
				</tr>
			';
        }

        $html .= '
				</tbody>
			</table>
			<input type="hidden" name="psID" value="' . $physicalServiceID . '"/>
			<input type="hidden" name="vsID" value="' . $virtualServiceID . '"/>
			<input type="hidden" name="policyID" value="' . $policyID . '"/>
			<input type="hidden" name="layerID" value="' . $layerID . '"/>
			<input type="hidden" name="tms_list" value="' . implode(';', $tms_identifier_list) . '"/>
		';
        return $html;
    }

    private static function getWmtsLayerSettings($virtualServiceID, $physicalServiceID, $policyID, $layerID) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('resourceurl');
        $query->from('#__sdi_physicalservice');
        $query->where('id = ' . (int)$physicalServiceID);
        
        $db->setQuery($query);

        try {
            $url = $db->loadResult();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $query = $db->getQuery(true);
        $query->select('wp.*, wsp.*, wp.id AS wmtslayerpolicy_id, tmsp.identifier AS tmsp_identifier, tmp.identifier AS tmp_identifier');
        $query->from('#__sdi_wmtslayer_policy wp');
        $query->innerJoin('#__sdi_physicalservice_policy pp ON wp.physicalservicepolicy_id = pp.id');
        $query->leftJoin('#__sdi_wmts_spatialpolicy wsp ON wp.spatialpolicy_id = wsp.id');
        $query->innerJoin('#__sdi_tilematrixset_policy tmsp ON wp.id = tmsp.wmtslayerpolicy_id');
        $query->leftJoin('#__sdi_tilematrix_policy tmp ON tmsp.id = tmp.tilematrixsetpolicy_id');
        $query->where('pp.physicalservice_id = ' . (int)$physicalServiceID);
        $query->where('pp.policy_id = ' . (int)$policyID);
        $query->where('wp.identifier = ' . $query->quote($layerID));
        
        $db->setQuery($query);

        try {
            $db->execute();
            $resultset = $db->loadObjectList();
            $wmtslayerpolicy = $db->loadObject();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $tms_arr = Array();
        foreach ($resultset as $tilematrixset) {
            $tms_arr[$tilematrixset->tmsp_identifier] = Array('maxTileMatrix' => $tilematrixset->tmp_identifier);
        }

        
        //preparing the object to be returned
        $data = Array();
        if (isset($wmtslayerpolicy)) {
            $data[$layerID] = Array(
                'enabled' => $wmtslayerpolicy->enabled,
                'spatialOperator' => $wmtslayerpolicy->spatialoperator_id,
                'westBoundLongitude' => $wmtslayerpolicy->westboundlongitude,
                'eastBoundLongitude' => $wmtslayerpolicy->eastboundlongitude,
                'northBoundLatitude' => $wmtslayerpolicy->northboundlatitude,
                'southBoundLatitude' => $wmtslayerpolicy->southboundlatitude,
                'tileMatrixSetList' => $tms_arr,
            );
        }

        $wmtsObj = new WmtsPhysicalService($physicalServiceID, $url);
        $wmtsObj->getCapabilities(self::getXmlFromCache($physicalServiceID, $virtualServiceID));
        $wmtsObj->populate();
        $wmtsObj->sortLists();
        $wmtsObj->loadData($data);
        $layerObj = $wmtsObj->getLayerByName($layerID);

        return $layerObj;
    }

    private static function setWmtsLayerSettings($raw_GET) {
        //$enabled = (isset($raw_GET['enabled']))?1:0;
        $physicalServiceID = $raw_GET['psID'];
        $policyID = $raw_GET['policyID'];
        $layerID = $raw_GET['layerID'];

        $raw_GET['spatialoperatorid'] = ('' != $raw_GET['spatialoperatorid']) ? $raw_GET['spatialoperatorid'] : 1;
        $raw_GET['eastBoundLongitude'] = ('' != $raw_GET['eastBoundLongitude']) ? $raw_GET['eastBoundLongitude'] : 'null';
        $raw_GET['westBoundLongitude'] = ('' != $raw_GET['westBoundLongitude']) ? $raw_GET['westBoundLongitude'] : 'null';
        $raw_GET['northBoundLatitude'] = ('' != $raw_GET['northBoundLatitude']) ? $raw_GET['northBoundLatitude'] : 'null';
        $raw_GET['southBoundLatitude'] = ('' != $raw_GET['southBoundLatitude']) ? $raw_GET['southBoundLatitude'] : 'null';

        $db = JFactory::getDbo();
        $spatial_policy_id = 'null';
        try {
            //save Spatial Policy
            if ('null' != $raw_GET['eastBoundLongitude'] && 
                    'null' != $raw_GET['westBoundLongitude'] && 
                    'null' != $raw_GET['northBoundLatitude'] && 
                    'null' != $raw_GET['southBoundLatitude']) {
                
                $query = $db->getQuery(true);
                $query->select('sp.id');
                $query->from('#__sdi_wmtslayer_policy p');
                $query->innerJoin('#__sdi_wmtslayer_policy p ON sp.id = p.spatialpolicy_id');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = p.physicalservicepolicy_id');
                $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('sp.policy_id = ' . (int)$policyID);
                $query->where('p.identifier = ' . $query->quote($layerID));
                
                $db->setQuery($query);
                $db->execute();
                $num_result = $db->getNumRows();
                $spatial_policy_id = $db->loadResult();

                $wmts_spatialpolicy = new stdClass();
                $wmts_spatialpolicy->spatialoperator_id = $raw_GET['spatialoperatorid'];
                $wmts_spatialpolicy->eastboundlongitude = $raw_GET['eastBoundLongitude'];
                $wmts_spatialpolicy->westboundlongitude = $raw_GET['westBoundLongitude'];
                $wmts_spatialpolicy->northboundlatitude = $raw_GET['northBoundLatitude'];
                $wmts_spatialpolicy->southboundlatitude = $raw_GET['southBoundLatitude'];
                
                if (0 == $num_result) {
                    $db->insertObject('#__sdi_wmts_spatialpolicy', $wmts_spatialpolicy);
                } else {
                    $db->updateObject('#__sdi_wmts_spatialpolicy', $wmts_spatialpolicy, $spatial_policy_id);
                }
                
                if (0 == $num_result) {
                    $spatial_policy_id = $db->insertid();
                }
            }

            //save Wmts Layer Policy
            $query = $db->getQuery(true);
            $query->select('p.id');
            $query->from('#__sdi_wmtslayer_policy p');
            $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = p.physicalservicepolicy_id');
            $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
            $query->where('psp.policy_id = ' . (int)$policyID);
            $query->where('p.identifier = ' . $query->quote($layerID));
            
            $db->setQuery($query);
            $db->execute();
            $num_result = $db->getNumRows();
            $wmtslayerpolicy_id = $db->loadResult();

            if (0 == $num_result) {
                $query = $db->getQuery(true);
                $query->select('id');
                $query->from('#__sdi_physicalservice_policy');
                $query->where('physicalservice_id = ' . $physicalServiceID);
                $query->where('policy_id = ' . $policyID);
                
                $db->setQuery($query);
                $db->execute();
                $physicalservice_policy_id = $db->loadResult();

                $query = $db->getQuery(true);
                $columns = array('identifier', 'spatialpolicy_id', 'physicalservicepolicy_id', 'inheritedspatialpolicy');
                $values = array($layerID,$spatial_policy_id,$physicalservice_policy_id,0);
                $query->insert('#__sdi_wmtslayer_policy')
                        ->columns($query->quoteName($columns))
                        ->values(implode(',', $values));
            } else {
                $query = $db->getQuery(true);
                if($spatial_policy_id == 'null')
                {
                     $query->update('#__sdi_wmtslayer_policy')
                           ->set(Array(
                                        'spatialpolicy_id = NULL',
                                        'inheritedspatialpolicy = 1',))
                           ->where(Array(
                                            'id = ' . (int)$wmtslayerpolicy_id ,));
                }
                else{
                     $query->update('#__sdi_wmtslayer_policy')
                             ->set(Array(
                    'spatialpolicy_id = ' . (int)$spatial_policy_id,
                    'inheritedspatialpolicy = 0',
                    ))->where(Array(
                        'id = ' . (int)$wmtslayerpolicy_id ,
                    ));
                }
            }

            $db->setQuery($query);
            $db->execute();
            if (0 == $num_result) {
                $wmtslayerpolicy_id = $db->insertid();
            }

            JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_easysdi_service/tables');
            $dispatcher = JEventDispatcher::getInstance();
            // Include the content plugins for the on save events.
            JPluginHelper::importPlugin('content');
            $table = JTable::getInstance("policy", "Easysdi_serviceTable", array());
            $table->load($policyID);
            // Trigger the onContentAfterSave event.
     //       $dispatcher->trigger('onContentAfterSave', array('com_easysdi_service.policy', $table, false));

            return WmtsWebservice::setTileMatrixSettings($wmtslayerpolicy_id, $raw_GET);
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }
    }

    private static function setTileMatrixSettings($wmtslayerpolicy_id, $raw_GET) {
        $physicalServiceID = $raw_GET['psID'];
        $virtualServiceID = $raw_GET['vsID'];
        $policyID = $raw_GET['policyID'];
        $layerID = $raw_GET['layerID'];
        $tileMatrixSet_arr = $raw_GET['select'];
        $precalculatedData = json_decode($raw_GET['precalculated']);

        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__sdi_sys_spatialoperator');
        $query->where('state = 1');
        
        $db->setQuery($query);

        try {
            $db->execute();
            $resultset = $db->loadObjectList();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }
        $spatialOperators = Array();
        foreach ($resultset as $result) {
            $spatialOperators[$result->id] = $result->value;
        }

        $query = $db->getQuery(true);
        $query->select('resourceurl');
        $query->from('#__sdi_physicalservice');
        $query->where('id = ' . (int)$physicalServiceID);
        
        $db->setQuery($query);

        try {
            $db->execute();
            $ps_url = $db->loadResult();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        $wmtsObj = new WmtsPhysicalService($physicalServiceID, $ps_url);
        $wmtsObj->getCapabilities(self::getXmlFromCache($physicalServiceID, $virtualServiceID));
        $wmtsObj->populate();

        $form_values[$layerID] = Array(
            'spatialOperator' => $spatialOperators[$raw_GET['spatialoperatorid']],
            'westBoundLongitude' => $raw_GET['westBoundLongitude'],
            'eastBoundLongitude' => $raw_GET['eastBoundLongitude'],
            'northBoundLatitude' => $raw_GET['northBoundLatitude'],
            'southBoundLatitude' => $raw_GET['southBoundLatitude'],
            'tileMatrixSetList' => Array(),
        );
        foreach ($raw_GET['select'] as $tms => $tm) {
            $form_values[$layerID]['tileMatrixSetList'][$tms] = Array(
                'maxTileMatrix' => $tm,
                'minX' => $raw_GET['minX'][$tms],
                'maxX' => $raw_GET['maxX'][$tms],
                'minY' => $raw_GET['minY'][$tms],
                'maxY' => $raw_GET['maxY'][$tms],
                'srsUnit' => $raw_GET['srsUnit'][$tms],
            );
        }
        $wmtsObj->loadData($form_values);

        //we insert the inherited bbox set for the server
        $wmtsObj->setAllBoundingBoxes(Array(
            'north' => $precalculatedData->inherit_server->{$physicalServiceID}->northBoundLatitude,
            'east' => $precalculatedData->inherit_server->{$physicalServiceID}->eastBoundLongitude,
            'south' => $precalculatedData->inherit_server->{$physicalServiceID}->southBoundLatitude,
            'west' => $precalculatedData->inherit_server->{$physicalServiceID}->westBoundLongitude,
        ));
        //we insert the inherited bbox set for the all policy
        $wmtsObj->setAllBoundingBoxes(Array(
            'north' => $precalculatedData->inherit_policy->northBoundLatitude,
            'east' => $precalculatedData->inherit_policy->eastBoundLongitude,
            'south' => $precalculatedData->inherit_policy->southBoundLatitude,
            'west' => $precalculatedData->inherit_policy->westBoundLongitude,
        ));
        //we insert the srsUnits
        $wmtsObj->setAllSRSUnit($precalculatedData->srs_units);

        $layerObj = $wmtsObj->getLayerByName($layerID);
        $layerObj->calculateAuthorizedTiles();

        //flushing the previous Tile Matrix and Tile Matrix Set
        $query = $db->getQuery(true);
        $query->delete('#__sdi_tilematrixset_policy')->where('wmtslayerpolicy_id = ' . (int)$wmtslayerpolicy_id);
        $db->setQuery($query);

        try {
            $db->execute();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        foreach ($layerObj->getTileMatrixSetList() as $tmsObj) {
            $maxTmsIdentifier = (isset($raw_GET['select'][$tmsObj->identifier])) ? $raw_GET['select'][$tmsObj->identifier] : null;

            //save Tile Matrix Set
            $query = $db->getQuery(true);
            $columns = array('wmtslayerpolicy_id', 'identifier', 'anytilematrix');
            $values = array($wmtslayerpolicy_id, $query->quote($tmsObj->identifier), ((empty($maxTmsIdentifier)) ? 1 : 0));
            $query->insert('#__sdi_tilematrixset_policy')
                    ->columns($query->quoteName($columns))
                    ->values(implode(',', $values));
            $db->setQuery($query);

            try {
                $db->execute();
                $tilematrixsetpolicy_id = $db->insertid();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }

            foreach ($tmsObj->getUpperTileMatrix($maxTmsIdentifier) as $tmObj) {
                //save Tile Matrix
                $query = $db->getQuery(true);
                $columns = array('tilematrixsetpolicy_id', 'identifier', 'anytile', 'tileminrow', 'tilemaxrow', 'tilemincol', 'tilemaxcol');
                $values = array($tilematrixsetpolicy_id, $tmObj->identifier, $tmObj->anyTile, $tmObj->minTileRow, $tmObj->maxTileRow, $tmObj->minTileCol, $tmObj->maxTileCol);
                $query->insert('#__sdi_tilematrix_policy')
                      ->columns($query->quoteName($columns))
                      ->values(implode(',', $values));
                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (JDatabaseException $e) {
                    $je = new JException($e->getMessage());
                    $this->setError($je);
                    return false;
                }
            }
        }
        return true;
    }

    private static function deleteWmtsLayer($raw_GET) {
        $physicalServiceID = $raw_GET['physicalServiceID'];
        $policyID = $raw_GET['policyID'];
        $layerID = $raw_GET['layerID'];

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('wp.id AS wp_id, sp.id AS sp_id');
        $query->from('#__sdi_wmtslayer_policy wp');
        $query->innerJoin('#__sdi_physicalservice_policy pp ON wp.physicalservicepolicy_id = pp.id');
        $query->leftJoin('#__sdi_wmts_spatialpolicy sp ON wp.spatialpolicy_id = sp.id');
        $query->where('pp.physicalservice_id = ' . (int)$physicalServiceID);
        $query->where('pp.policy_id = ' . (int)$policyID);
        $query->where('wp.identifier = ' . $query->quote($layerID));
        
        $db->setQuery($query);

        try {
            $db->execute();
            $result = $db->loadObject();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return false;
        }

        if (is_numeric($result->sp_id) && 0 < $result->sp_id) {
            $query = $db->getQuery(true);
            $query->update('#__sdi_wmtslayer_policy')
                    ->set(Array(
                            'spatialpolicy_id = NULL',
                            'inheritedspatialpolicy = 1',
            ))->where(Array(
                            'spatialpolicy_id = ' . (int)$result->sp_id ,
            ));
            $db->setQuery($query);
            $db->execute();
//            $db->setQuery("UPDATE #__sdi_wmtslayer_policy SET spatialpolicy_id = NULL AND inheritedspatialpolicy = 1 WHERE spatialpolicy_id = " . $result->sp_id);
           // $db->execute();

            $query = $db->getQuery(true);
            $query->delete('#__sdi_wmts_spatialpolicy')->where('id = ' . (int)$result->sp_id);

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }

        if (is_numeric($result->wp_id) && 0 < $result->wp_id) {
            $query = $db->getQuery(true);
            $query->delete('#__sdi_tilematrixset_policy')->where('wmtslayerpolicy_id = ' . (int)$result->wp_id);

            $db->setQuery($query);

            try {
                $db->execute();
            } catch (JDatabaseException $e) {
                $je = new JException($e->getMessage());
                $this->setError($je);
                return false;
            }
        }
    }

    private static function getXmlFromCache($physicalServiceID, $virtualServiceID) {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select('pssc.capabilities');
        $query->from('#__sdi_virtualservice vs');
        $query->innerJoin('#__sdi_virtual_physical vp ON vs.id = vp.virtualservice_id');
        $query->innerJoin('#__sdi_physicalservice ps ON ps.id = vp.physicalservice_id');
        $query->innerJoin('#__sdi_physicalservice_servicecompliance pssc ON ps.id = pssc.service_id');
        $query->innerJoin('#__sdi_virtualservice_servicecompliance vssc ON vs.id = vssc.service_id');
        $query->innerJoin('#__sdi_sys_servicecompliance sc ON sc.id = vssc.servicecompliance_id');
        $query->innerJoin('#__sdi_sys_serviceversion sv ON sv.id = sc.serviceversion_id');
        $query->where('ps.id = ' . $physicalServiceID);
        $query->where('vs.id = ' . $virtualServiceID);
        $query->order('sv.ordering DESC');
        
        $db->setQuery($query,0,1);
        try {
            $db->execute();
            return $db->loadResult();
        } catch (JDatabaseException $e) {
            $je = new JException($e->getMessage());
            $this->setError($je);
            return null;
        }
    }

}
