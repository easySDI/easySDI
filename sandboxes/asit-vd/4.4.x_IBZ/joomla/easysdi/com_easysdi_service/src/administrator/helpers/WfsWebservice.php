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

require_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/WfsPhysicalService.php');

class WfsWebservice {
	/*
	 * Entry point of the class
	 * 
	 * @param Array Usually the $_GET, or any associative array
	 * @param Boolean Set to true to force a return value, results are echoed otherwise
	*/
	public static function request ($params) {
		switch ($params['method']) {
			case 'getFeatureTypeForm':
				echo WfsWebservice::getFeatureTypeForm($params);
				break;
			case 'setFeatureTypeSettings':
				if (WfsWebservice::setFeatureTypeSettings($params)) {
					echo 'OK';
				}
				break;
			case 'deleteFeatureType':
				if (WfsWebservice::deleteFeatureType($params)) {
					echo 'OK';
				}
				break;
			default:
				echo 'Unknown method.';
				break;
		}
		die();
	}
	
	private static function getFeatureTypeForm ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$virtualServiceID = $raw_GET['virtualServiceID'];
		$policyID = ('' == $raw_GET['policyID'])?0:$raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$layerObj = WfsWebservice::getFeatureTypeSettings(
			$virtualServiceID,
			$physicalServiceID,
			$policyID,
			$layerID
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
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		$html = '
		<div class="well">
	    	<div class="control-group inline">
				<label class="control-label" for="localgeographicfilter">' . JText::_('COM_EASYSDI_SERVICE_WFS_LAYER_LOCAL_FILTER') . '</label>
				<div class="controls">
					<textarea name="localgeographicfilter" rows="5" class="input-xlarge">' . $layerObj->localFilterGML . '</textarea>
				</div>
			</div>
			<div class="control-group inline">
				<label class="control-label" for="remotegeographicfilter">' . JText::_('COM_EASYSDI_SERVICE_WFS_LAYER_REMOTE_FILTER') . '</label>
				<div class="controls">
					<textarea name="remotegeographicfilter" rows="5" class="input-xlarge">' . $layerObj->remoteFilterGML . '</textarea>
				</div>
			</div>
		</div>
			<hr />
			<div id="div_included_attributes">
		';
		
                $query = $db->getQuery(true);
                $query->select('ia.name');
                $query->from('#__sdi_includedattribute ia');
                $query->innerJoin('#__sdi_featuretype_policy ftp ON ftp.id = ia.featuretypepolicy_id');
                $query->where('ftp.name = ' . $query->quote($layerID));
                
		$db->setQuery($query);
		$db->execute();
		$items = $db->loadColumn();
		$item_count = 0;
		foreach ($items as $item) {
			$html.= '
			<div class="div_ia_' . $item_count . ' input-xxlarge">
				<input type="text" name="included_attribute[' . $item_count . ']" class="input-xlarge" value="'.$item.'" />
				<button class="btn btn-danger btn-small btn_ia_delete" onClick="onDeleteIncludedAttribute(' . $item_count . ');return false;"><i class="icon-white icon-remove"></i></button>
				<br /><br />
			</div>';
			$item_count ++;
		}
		
		$html .= '</div>
			<button class="btn" data-count="' . $item_count . '" id="btn_add_included_attribute" onClick="onAddIncludedAttribute();return false;">
				' . JText::_('COM_EASYSDI_SERVICE_WFS_BTN_ADD_INCLUDED_ATTRIBUTE') . '
			</button>
			<input type="hidden" name="psID" value="' . $physicalServiceID . '"/>
			<input type="hidden" name="vsID" value="' . $virtualServiceID . '"/>
			<input type="hidden" name="policyID" value="' . $policyID . '"/>
			<input type="hidden" name="layerID" value="' . $layerID . '"/>
		';
		return $html;
	}
	
	private static function getFeatureTypeSettings ($virtualServiceID, $physicalServiceID, $policyID, $layerID) {
		$db = JFactory::getDbo();
		
                $query = $db->getQuery(true);
                $query->select('resourceurl');
                $query->from('#__sdi_physicalservice');
                $query->where('id = ' . (int)$physicalServiceID);
                
		$db->setQuery($query);
		
		try {
			$url = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
                $query = $db->getQuery(true);
                $query->select('ftp.*, wsp.*, ftp.id AS featuretypepolicy_id');
                $query->from('#__sdi_featuretype_policy ftp');
                $query->innerJoin('#__sdi_physicalservice_policy pp ON ftp.physicalservicepolicy_id = pp.id');
                $query->innerJoin('#__sdi_wfs_spatialpolicy wsp ON ftp.spatialpolicy_id = wsp.id');
                $query->where('pp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('pp.policy_id = ' . (int)$policyID);
                $query->where('ftp.name = ' . $query->quote($layerID));
                
		$db->setQuery($query);
		
		try {
			$db->execute();
			$featuretypepolicy = $db->loadObject();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//preparing the object to be returned
		$data = Array();
		if (isset($featuretypepolicy)) {
			$data[$layerID] = Array(
				'enabled' => $featuretypepolicy->enabled,
				'remoteFilterGML' => $featuretypepolicy->localgeographicfilter,
				'localFilterGML' => $featuretypepolicy->remotegeographicfilter,
			);
		}
		
		$wfsObj = new WfsPhysicalService($physicalServiceID, $url);
		$wfsObj->getCapabilities(self::getXmlFromCache($physicalServiceID, $virtualServiceID));
		$wfsObj->populate();
		$wfsObj->loadData($data);
		$layerObj = $wfsObj->getLayerByName($layerID);
		
		return $layerObj;
	}
	
	private static function setFeatureTypeSettings ($raw_GET) {
		$physicalServiceID = $raw_GET['psID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$db = JFactory::getDbo();
		
                $query = $db->getQuery(true);
                $query->select('sp.id');
                $query->from('#__sdi_wfs_spatialpolicy sp');
                $query->innerJoin('#__sdi_featuretype_policy ftp ON sp.id = ftp.spatialpolicy_id');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = ftp.physicalservicepolicy_id');
                $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('psp.policy_id = ' . (int)$policyID);
                $query->where('ftp.name = ' . $query->quote($layerID));
                
		//save Spatial Policy
		$db->setQuery($query);
		
		try {
			$db->execute();
			$num_result = $db->getNumRows();
			$spatial_policy_id = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		if (0 == $num_result) {
			$query = $db->getQuery(true);
                        $columns = array('localgeographicfilter','remotegeographicfilter');
                        $values = array($db->quote($raw_GET['localgeographicfilter']), $db->quote($raw_GET['remotegeographicfilter']));
                        
			$query->insert('#__sdi_wfs_spatialpolicy')
                                ->columns($db->quoteName($columns))
                                ->values(implode(',',$values));
		}
		else {
			$query = $db->getQuery(true);
                        
			$query->update('#__sdi_wfs_spatialpolicy')->set(Array(
				'localgeographicfilter = ' . $db->quote($raw_GET['localgeographicfilter']) ,
				'remotegeographicfilter = ' . $db->quote($raw_GET['remotegeographicfilter']) ,
			))->where(Array(
				'id = ' . $db->quote($spatial_policy_id),
			));
		}
		
		$db->setQuery($query);
		
		try {
			$db->execute();
			if (0 == $num_result) {
				$spatial_policy_id = $db->insertid();
			}
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		//save Feature Type Policy
                $query = $db->getQuery(true);
                $query->select('ftp.id');
                $query->from('#__sdi_featuretype_policy ftp');
                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = ftp.physicalservicepolicy_id');
                $query->where('psp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('psp.policy_id = ' . (int)$policyID );
                $query->where('ftp.name = ' . $query->quote($layerID));
                
		$db->setQuery($query);
		
		try {
			$db->execute();
			$num_result = $db->getNumRows();
			$featuretypepolicy_id = $db->loadResult();
		}
		catch (JDatabaseException $e) {
			$je = new JException($e->getMessage());
			$this->setError($je);
			return false;
		}
		
		
		if (0 != $num_result) {
			$query = $db->getQuery(true);
			$query->update('#__sdi_featuretype_policy')->set(Array(
				'spatialpolicy_id = ' . (int)$spatial_policy_id ,
				'inheritedspatialpolicy = 0',
			))->where(Array(
				'id = ' . (int)$featuretypepolicy_id ,
			));
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
		
		//save included attributes
                $query = $db->getQuery(true);
                $query->delete('#__sdi_includedattribute');
                $query->where('featuretypepolicy_id = ' . (int)$featuretypepolicy_id);
                
		$db->setQuery($query);
		$db->query();
		
		$arr_ex = $raw_GET['included_attribute'];
		foreach ($arr_ex as $value) {
			if (!empty($value)) {
                                $query = $db->getQuery(true);
                                $columns = array('featuretypepolicy_id', 'name');
                                $values = array($featuretypepolicy_id, $query->quote($value));
                                $query->insert('#__sdi_includedattribute');
                                $query->columns($query->quoteName($columns));
                                $query->values(implode(',', $values));
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
	
	/*
	 * Save all layers of a given virtual service
	 * 
	 * @param Int virtual service ID
	 * @param Int policy ID
	*/
	public static function saveAllFeatureTypes($virtualServiceID, $policyID) {
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
			$wfsObj = new WfsPhysicalService($result->id, $result->url, $result->resourceusername, $result->resourcepassword);
			$wfsObj->getCapabilities();
			$wfsObj->populate();
			$layerList = $wfsObj->getLayerList();
			
			foreach ($layerList as $layer) {
				//we check if the layer already exists
                                $query = $db->getQuery(true);
                                $query->select('ftp.id');
                                $query->from('#__sdi_featuretype_policy ftp');
                                $query->innerJoin('#__sdi_physicalservice_policy psp ON psp.id = ftp.physicalservicepolicy_id');
                                $query->where('psp.physicalservice_id = ' . $physicalServiceID);
                                $query->where('psp.policy_id = ' . $policyID);
                                $query->where('ftp.name = ' . $query->quote($layer->name));
                                
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
                                        $query = $db->getQuery('true');
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
                                        $values = array($db->quote($layer->name), $db->quote($db->escape($layer->description)), $db->quote($physicalservice_policy_id));
					$query->insert('#__sdi_featuretype_policy')
                                                ->columns($db->quoteName($columns))
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
	
	private static function deleteFeatureType ($raw_GET) {
		$physicalServiceID = $raw_GET['physicalServiceID'];
		$policyID = $raw_GET['policyID'];
		$layerID = $raw_GET['layerID'];
		
		$db = JFactory::getDbo();
		
                $query = $db->getQuery(true);
                $query->select('ftp.spatialpolicy_id AS id');
                $query->from('#__sdi_featuretype_policy ftp');
                $query->innerJoin('#__sdi_physicalservice_policy pp ON ftp.physicalservicepolicy_id = pp.id');
                $query->where('pp.physicalservice_id = ' . (int)$physicalServiceID);
                $query->where('pp.policy_id = ' . (int)$policyID);
                $query->where('ftp.name = ' . $query->quote($layerID));
                
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
				//Update spatialspolicy_id on __sdi_featuretype_policy
				$query = $db->getQuery(true);
				$query->update('#__sdi_featuretype_policy')
                                        ->set(Array(
					'spatialpolicy_id = NULL',
					'inheritedspatialpolicy = 1'))
                                        ->where('spatialpolicy_id = ' . (int)$pk);
				$db->setQuery($query);
				$db->execute();
				
				//Delete spatialspolicy_id
				$query = $db->getQuery(true);
				$query->delete('#__sdi_wfs_spatialpolicy')->where('id = ' . (int)$pk);
				$db->setQuery($query);
				$db->execute();
			}
			catch (JDatabaseException $e) {
				$je = new JException($e->getMessage());
				$this->setError($je);
				return false;
			}
			
			$query = $db->getQuery(true);
			$query->delete('#__sdi_includedattribute')->where('featuretypepolicy_id = ' . (int)$pk);
			
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
		
		$dispatcher = JEventDispatcher::getInstance();
		$data = new stdClass();
		$data->id = $policyID;
		$dispatcher->trigger('onContentAfterDelete', array('com_easysdi_service.policy', $data));
		
		return true;
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
