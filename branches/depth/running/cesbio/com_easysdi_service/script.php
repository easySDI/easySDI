<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
class com_easysdi_serviceInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		
		echo '<p>' . JText::_('COM_EASYSDI_SERVICE_PREFLIGHT_SCRIPT') . '</p>';
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
		echo '<p>' . JText::_('COM_EASYSDI_SERVICE_INSTALL_SCRIPT' )  . '</p>';
		// You can have the backend jump directly to the newly installed component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		echo '<p>' . JText::_('COM_EASYSDI_SERVICE_UPDATE_SCRIPT' ) . '</p>';
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		
		if($type == 'install'){
			//EasySDI control Panel form update
			$fielset_string = "<fieldset name=\"service\" >
				      		<field name=\"services\" 
						     		type=\"link\" 
						     		default=\"0\" 
						     		label=\"COM_EASYSDI_CORE_CTRLPANEL_LBL_SERVICESLINK\"
						          	readonly=\"true\" 
						          	class=\"readonly\" 
						          	component=\"com_easysdi_service\"
						          	description=\"COM_EASYSDI_CORE_CTRLPANEL_DESC_SERVICESLINK\" /> 
						          	
						      <field extension=\"com_easysdi_service\" 
							     	type=\"link\" 
						     		default=\"0\" 
						     		label=\"COM_EASYSDI_CORE_CTRLPANEL_LBL_SERVICE_CATEGORIES\"
						          	readonly=\"true\" 
						          	class=\"readonly\" 
						          	component=\"com_categories\"
						          	description=\"COM_EASYSDI_CORE_CTRLPANEL_DESC_SERVICE_CATEGORIES\" /> 
							</fieldset>";
			
			$form_dom = new DomDocument();
			$form_dom->load(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/models/forms/easysdi.xml');
			$form_fields = $form_dom->getElementsByTagName('fields')->item(0);
			
			$fragment 				= $form_dom->createDocumentFragment();
			$fragment->appendXML($fielset_string);
			$form_fields->appendChild($fragment);
			$form_dom->save(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/models/forms/easysdi.xml');
			
			//EasySDI configuration form update
			$core_dom = new DomDocument();
			$core_dom->load(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/config.xml');
			$service_dom = new DomDocument();
			$service_dom->load(JPATH_ADMINISTRATOR.'/components/com_easysdi_service/config.xml');
			
			$core_fieldsets = $core_dom->getElementsByTagName('fieldset');
			$service_fieldsets = $service_dom->getElementsByTagName('fieldset');
			
			foreach($core_fieldsets as $core_fieldset){
				if($core_fieldset->getAttribute("name") == 'component'){
					foreach($service_fieldsets as $service_fieldset){
						if($service_fieldset->getAttribute("name") == 'component'){
							$fields = $service_fieldset->getElementsByTagName('field');
							foreach ($fields as $field){
								$node = $core_dom->importNode($field, true);
								$core_fieldset->appendChild ($node);
							}
							break;
						}
					}
				}
			}
			$core_dom->save(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/config.xml');
		}
		
		echo '<p>' . JText::_('COM_EASYSDI_SERVICE_POSTFLIGHT_SCRIPT ') . '</p>';
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		
		//EasySDI control Panel form cleaning
		$form_dom = new DomDocument();
		$form_dom->load(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/models/forms/easysdi.xml');
		$form_fields = $form_dom->getElementsByTagName('fields')->item(0);
		$form_fieldsets = $form_fields->getElementsByTagName('fieldset');
		$nodeToRemove= null;
		foreach($form_fieldsets as $form_fieldset){
			if($form_fieldset->getAttribute("name") == 'service'){
				$nodeToRemove = $form_fieldset;
				break;
			}
		}
		$form_fields->removeChild($nodeToRemove);
		$form_dom->save(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/models/forms/easysdi.xml');
		
		//EasySDI configuration form cleaning
		$core_dom = new DomDocument();
		$core_dom->load(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/config.xml');
		$service_dom = new DomDocument();
		$service_dom->load(JPATH_ADMINISTRATOR.'/components/com_easysdi_service/config.xml');
			
		$core_fieldsets = $core_dom->getElementsByTagName('fieldset');
		$service_fieldsets = $service_dom->getElementsByTagName('fieldset');
		$namesToRemove = array();
		foreach($service_fieldsets as $service_fieldset){
			if($service_fieldset->getAttribute("name") == 'component'){
				$fields = $service_fieldset->getElementsByTagName('field');
				foreach ($fields as $field){
					$namesToRemove[]= $field->getAttribute("name");
				}
				break;
			}
		}
		
		$fieldsToRemove = array();
		foreach($core_fieldsets as $core_fieldset){
			if($core_fieldset->getAttribute("name") == 'component'){
				$core_fields = $core_fieldset->getElementsByTagName('field'); 
				foreach($core_fields as $core_field){
					if(in_array($core_field->getAttribute("name"), $namesToRemove) ){
						$fieldsToRemove[]=$core_field;
					}
				}
			}
		}
		
		foreach ($fieldsToRemove as $field){
			$field->parentNode->removeChild($field);
		}
		$core_dom->save(JPATH_ADMINISTRATOR.'/components/com_easysdi_core/config.xml');
		
		echo '<p>' . JText::_('COM_EASYSDI_SERVICE_UNINSTALL_SCRIPT ') . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_easysdi_service"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_easysdi_service"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_easysdi_service"' );
				$db->query();
		}
	}
}
