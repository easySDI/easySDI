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

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/WmsPhysicalService.php');

class WmsWebservice {
	/*
	 * Entry point of the class
	 * 
	 * @param Array Usually the $_GET, or any associative array
	 * @param Boolean Set to true to force a return value, results are echoed otherwise
	*/
	public static function request ($params) {
		switch ($params['method']) {
			case 'getWmsLayerForm':
				echo WmsWebservice::getWmsLayerForm($params);
				break;
			case 'setWmsLayerSettings':
				if (WmsWebservice::setWmsLayerSettings($params)) {
					echo 'OK';
				}
				break;
			case 'deleteWmsLayer':
				if (WmsWebservice::deleteWmsLayer($params)) {
					echo 'OK';
				}
				break;
			default:
				echo 'Unknown method.';
				break;
		}
		die();
	}
	
	private static function getWmsLayerForm ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$virtualServiceID = $raw_GET['virtualServiceID'];
		$policyID = ('' == $raw_GET['policyID'])?0:$raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$layerObj = WmsWebservice::getWmsLayerSettings(
			$virtualServiceID,
			$physicalServiceID,
			$policyID,
			$layerID
		);
		
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('*');
                $query->from('#__sdi_sys_spatialoperator');
                $query->where('state=1');
                $query->order('ordering');
                
		$db->setQuery($query);
		
		try {
			$db->execute();
			$resultset = $db->loadObjectList();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		$html = '
		<div class="well">
	    	<div class="control-group inline">
				<label class="control-label" for="minimumscale">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_MINIMUM_SCALE') . '</label>
				<div class="controls">
					<input type="text" name="minimumscale" value="' .  $layerObj->minimumScale . '" />
				</div>
			</div>
	  		<div class="control-group">
				<label class="control-label" for="maximumscale">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_MAXIMUM_SCALE') . '</label>
				<div class="controls">
					<input type="text" name="maximumscale" value="' . $layerObj->maximumScale . '" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="geographicfilter">' . JText::_('COM_EASYSDI_SERVICE_WMS_LAYER_FILTER') . '</label>
				<div class="controls">
					<textarea name="geographicfilter" rows="8" class="input-xlarge">' . $layerObj->geographicFilter . '</textarea>
				</div>
			</div>
		</div>

			<input type="hidden" name="psID" value="' . $physicalServiceID . '"/>
			<input type="hidden" name="vsID" value="' . $virtualServiceID . '"/>
			<input type="hidden" name="policyID" value="' . $policyID . '"/>
			<input type="hidden" name="layerID" value="' . $layerID . '"/>
		';
		return $html;
	}
	
	private static function getWmsLayerSettings ($virtualServiceID, $physicalServiceID, $policyID, $layerID) {
		$db = JFactory::getDbo();
		
                $query = $db->getQuery(true);
                $query->select('resourceurl');
                $query->from('#__sdi_physicalservice');
                $query->where('id = ' . $physicalServiceID);
                
		$db->setQuery($query);
		
		try {
			$url = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
                $query =$db->getQuery(true);
                $query->select('wlp.*, wsp.*, wlp.id AS wmslayerpolicy_id');
                $query->from('#__sdi_wmslayer_policy wlp');
                $query->innerJoin('#__sdi_physicalservice_policy pp ON wlp.physicalservicepolicy_id = pp.id');
                $query->innerJoin('#__sdi_wms_spatialpolicy wsp ON wlp.spatialpolicy_id = wsp.id');
                $query->where('pp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('pp.policy_id = ' . (int)$policyID);
                $query->where('wlp.name = ' . $query->quote($layerID));
                
		$db->setQuery($query);
		
		try {
			$db->execute();
			$wmslayerpolicy = $db->loadObject();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//preparing the object to be returned
		$data = Array();
		if (isset($wmslayerpolicy)) {
			$data[$layerID] = Array(
				'enabled' => $wmslayerpolicy->enabled,
				'geographicFilter' => $wmslayerpolicy->geographicfilter,
				'maximumScale' => $wmslayerpolicy->maximumscale,
				'minimumScale' => $wmslayerpolicy->minimumscale,
			);
		}
		
		$wmsObj = new WmsPhysicalService($physicalServiceID, $url);
		$wmsObj->getCapabilities(self::getXmlFromCache($physicalServiceID, $virtualServiceID));
		$wmsObj->populate();
		$wmsObj->loadData($data);
		$layerObj = $wmsObj->getLayerByName($layerID);
		return $layerObj;
	}
	
	private static function setWmsLayerSettings ($raw_GET) {
		$physicalServiceID = $raw_GET['psID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		$raw_GET['maxX'] = ('' != $raw_GET['maxX'])?$raw_GET['maxX']:'null';
		$raw_GET['maxY'] = ('' != $raw_GET['maxY'])?$raw_GET['maxY']:'null';
		$raw_GET['minX'] = ('' != $raw_GET['minX'])?$raw_GET['minX']:'null';
		$raw_GET['minY'] = ('' != $raw_GET['minY'])?$raw_GET['minY']:'null';
		$raw_GET['minimumscale'] = ('' != $raw_GET['minimumscale'])?$raw_GET['minimumscale']:'null';
		$raw_GET['maximumscale'] = ('' != $raw_GET['maximumscale'])?$raw_GET['maximumscale']:'null';
		$db = JFactory::getDbo();
		
		try{
			//save Spatial Policy
                        $query = $db->getQuery(true);
                        $query->select('sp.id');
                        $query->from('#__sdi_wms_spatialpolicy sp');
                        $query->innerJoin('#__sdi_wmslayer_policy wlp ON sp.id = wlp.spatialpolicy_id');
                        $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = wlp.physicalservicepolicy_id');
                        $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
                        $query->where('psp.policy_id = ' . (int)$policyID);
                        $query->where('wlp.name = ' . $query->quote($layerID));
                        
			$db->setQuery($query);
			$db->execute();
			$num_result = $db->getNumRows();
			$spatial_policy_id = $db->loadResult();
		
			$query = $db->getQuery(true);
			if (0 == $num_result) {
                                $columns = array('geographicfilter','maxx','minx','miny','minimumscale','maximumscale','srssource');
                                $values = array($db->quote($raw_GET['geographicfilter']),$raw_GET['maxX'],$raw_GET['maxY'],$raw_GET['minX'],$raw_GET['minY'],$raw_GET['minimumscale'],$raw_GET['maximumscale'],$db->quote($raw_GET['srs']));
				$query->insert('#__sdi_wms_spatialpolicy')
                                        ->columns($db->quoteName($columns))
                                        ->values(implode(',',$values));
			}
			else {
				$query->update('#__sdi_wms_spatialpolicy')->set(Array(
					'geographicfilter = ' . $query->quote($raw_GET['geographicfilter']) ,
					'maxx = ' . $raw_GET['maxX'],
					'maxy = ' . $raw_GET['maxY'],
					'minx = ' . $raw_GET['minX'],
					'miny = ' . $raw_GET['minY'],
					'minimumscale = ' . $raw_GET['minimumscale'],
					'maximumscale = ' . $raw_GET['maximumscale'],
					'srssource = ' . $query->quote($raw_GET['srs']) ,
				))->where(Array(
					'id = ' . (int)$spatial_policy_id ,
				));
			}
			$db->setQuery($query);
			$db->execute();
			if (0 == $num_result) {
				$spatial_policy_id = $db->insertid();
			}
		
			//save Wms Layer Policy
                        $query = $db->getQuery(true);
                        $query->select('wlp.id');
                        $query->from('#__sdi_wmslayer_policy wlp');
                        $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = wlp.physicalservicepolicy_id');
                        $query->where('psp.physicalservice_id = ' . $physicalServiceID);
                        $query->where('psp.policy_id = ' . $policyID);
                        $query->where('wlp.name = ' . $query->quote($layerID));
                        
			$db->setQuery($query);
			$db->execute();
			$num_result = $db->getNumRows();
			$wmslayerpolicy_id = $db->loadResult();
		
			
			if (0 != $num_result) {//Update the spatialpolicy_id in the wmslayer object
				$query = $db->getQuery(true);
				$query->update('#__sdi_wmslayer_policy')->set(Array(
					'spatialpolicy_id = ' . (int)$spatial_policy_id ,
					'inheritedspatialpolicy = 0',
				))->where(Array(
					'id = ' . (int)$wmslayerpolicy_id ,
				));
				$db->setQuery($query);
				$db->execute();
			}
			
		        
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_easysdi_service/tables');
	        $dispatcher = JEventDispatcher::getInstance();
	        // Include the content plugins for the on save events.
	        JPluginHelper::importPlugin('content');
	        $table = JTable::getInstance("policy", "Easysdi_serviceTable", array());
	        $table->load($policyID);
	        // Trigger the onContentAfterSave event.
	        $dispatcher->trigger('onContentAfterSave', array('com_easysdi_service.policy', $table, false));
			return true;
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
	}
	
	/*
	 * Save all layers of a given virtual service
	 * 
	 * @param Int virtual service ID
	 * @param Int policy ID
	*/
	public static function saveAllLayers($virtualServiceID, $policyID) {
		$db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query->select('ps.id, ps.resourceurl AS url, psp.id AS psp_id, ps.resourceusername, ps.resourcepassword');
                $query->from('#__sdi_virtualservice vs');
                $query->innerJoin('#__sdi_virtual_physical vp ON vs.id = vp.virtualservice_id');
                $query->innerJoin('#__sdi_physicalservice ps ON ps.id = vp.physicalservice_id');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON ps.id = psp.physicalservice_id');
                $query->where('vs.id = ' . (int)$virtualServiceID);
                $query->where('psp.policy_id = ' . (int)$policyID);
                
		$db->setQuery($query);
		
		try {
			$db->execute();
			$resultset = $db->loadObjectList();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		foreach ($resultset as $result) {
			$physicalServiceID = $result->id;
                        $wmsObj = new WmsPhysicalService($result->id, $result->url, $result->resourceusername, $result->resourcepassword);
           		$wmsObj->getCapabilities();
			$wmsObj->populate();
			$layerList = $wmsObj->getLayerList();
			
			foreach ($layerList as $layer) {
				//we check if the layer already exists
                                $query = $db->getQuery(true);
                                $query->select('wlp.id');
                                $query->from('#__sdi_wmslayer_policy wlp');
                                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = wlp.physicalservicepolicy_id');
                                $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
                                $query->where('psp.policy_id = ' . (int)$policyID);
                                $query->where('wlp.name =' . $query->quote($layer->name));
                            
				$db->setQuery($query);
				
				try {
					$db->execute();
					$layer_exists = (0 == $db->getNumRows())?false:true;
				}
				catch (JDatabaseException $e) {
					$je = new JException($e->getMessage());
					$this->setError($je);
					return false;
				}
				
				if ($layer_exists) {
					//if the layer already exists, we do nothing and we skip to the next layer
					continue;
				}
				else {
					//we retrieve the physicalservice_policy id to link the layer policy with
                                        $query = $db->getQuery(true);
                                        $query->select('id');
                                        $query->from('#__sdi_physicalservice_policy');
                                        $query->where('physicalservice_id = ' . $physicalServiceID);
                                        $query->where('policy_id = ' . $policyID);
                                        
					$db->setQuery($query);
					
					try {
						$db->execute();
						$physicalservice_policy_id = $db->loadResult();
					}
					catch (JDatabaseException $e) {
						$je = new JException($e->getMessage());
						$this->setError($je);
						return false;
					}
					
					//we save the layer policy
					$query = $db->getQuery(true);
                                        $columns = array('name','description','physicalservicepolicy_id');
                                        $values = array($query->quote($layer->name), $query->quote($db->escape($layer->description)), $physicalservice_policy_id);
					$query->insert('#__sdi_wmslayer_policy')
                                                ->columns($query->quoteName($columns))
                                                ->values(implode(',',$values));
					
					$db->setQuery($query);
					
					try {
						$db->execute();
					}
					catch (JDatabaseException $e) {
						$je = new JException($e->getMessage());
						$this->setError($je);
						return false;
					}
				}
				echo '<hr />';
			}
		}
		return true;
	}
	
	private static function deleteWmsLayer ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$db = JFactory::getDbo();
		
                $query = $db->getQuery(true);
                $query->select('wp.spatialpolicy_id');
                $query->from('#__sdi_wmslayer_policy wp');
                $query->innerJoin('#__sdi_physicalservice_policy pp ON wp.physicalservicepolicy_id = pp.id');
                $query->where('pp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('pp.policy_id = ' . (int)$policyID);
                $query->where('wp.name = ' . $query->quote($layerID));
                
		$db->setQuery($query);

		try {
			$db->execute();
			$pk = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		if (is_numeric($pk) && 0 < $pk) {
			try {
				$query = $db->getQuery(true);
				$query->update('#__sdi_wmslayer_policy')->set(Array(
						'spatialpolicy_id = NULL',
						'inheritedspatialpolicy = 1',
				))->where(Array(
						'spatialpolicy_id = ' . (int)$pk ,
				));
				$db->setQuery($query);
				$db->execute();
// 				$db->setQuery("UPDATE #__sdi_wmslayer_policy SET spatialpolicy_id = NULL AND inheritedspatialpolicy = 1 WHERE spatialpolicy_id = ".$pk);
// 				$db->execute();
				
				$query = $db->getQuery(true);
				$query->delete('#__sdi_wms_spatialpolicy')
                                        ->where('id = ' . (int)$pk);
				
				$db->setQuery($query);
				$db->execute();
				
				
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
		}
	}
	
	private static function getXmlFromCache ($physicalServiceID, $virtualServiceID) {
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
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return null;
		}
	}
	
}
