<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Easysdi_service.
 */
class Easysdi_serviceViewPolicy extends JView
{

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
	
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_POLICY').' ['.$this->id.']', 'article-edit.png');
		
		$canDo	= Easysdi_serviceHelper::getActions();
		
		if ((!isset($this->id)&& $canDo->get('core.create')) || (isset($this->id)&& $canDo->get('core.edit')))
			JToolBarHelper::save('policy.save', 'JTOOLBAR_SAVE');
		
		JToolBarHelper::cancel('policy.cancel', 'JTOOLBAR_CANCEL');
	}
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		$params 				= JComponentHelper::getParams('com_easysdi_service');
		$this->xml 				= simplexml_load_file($params->get('proxyconfigurationfile'));
		$this->id 				= JRequest::getVar('id',null);
		$this->config 			= JRequest::getVar('config',null);
		$this->connector 		= JRequest::getVar('layout',null);
		$db			 			= JFactory::getDBO();
		//Get Joomla Groups
		$db->setQuery( "SELECT id as value, title as text FROM #__usergroups l ORDER BY text" );
		$this->rowsGroup = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		
		//Get users
		$db->setQuery( "SELECT #__users.username as value, #__users.name as text FROM #__users INNER JOIN #__sdi_user ON  #__users.id = #__sdi_user.user_id WHERE #__users.block = 0 AND #__users.activation = 0 ORDER BY text" );
		$this->rowsUser = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		?>
		<script>
		function addOption(selectList,myText)
		{
			var elOptNew = document.createElement('option'); 
			elOptNew.text = document.getElementById(myText).value ; 
			elOptNew.value = document.getElementById(myText).value ;
			var elSel = document.getElementById(selectList);
			try { elSel.add(elOptNew, null);  } 
			catch(ex) {elSel.add(elOptNew); }

		}
		function removeOptionSelected(selectX)
		{
		  var elSel = document.getElementById(selectX);
		  var i;
		  for (i = elSel.length - 1; i>=0; i--) {
		    if (elSel.options[i].selected) {
		      elSel.remove(i);
		    }
		  }
		}
		function disableList(chkBox,list)
		{
			if (document.getElementById(chkBox).checked==true)
			{
				document.getElementById(list).disabled=true;
				for (i = document.getElementById(list).length - 1; i>=0; i--) 
				{
				    document.getElementById(list).options[i].selected = false;
				}
			}
			else
			{
				document.getElementById(list).disabled=false;
			}
		}
		function disableButton(chkBox,button)
		{
			if (document.getElementById(chkBox).checked==true){
				document.getElementById(button).disabled=true;
			}else{
				document.getElementById(button).disabled=false;
			}
		}
		Array.prototype.remove=function(s)
		{
			for(i=0; i < this.length ; i++)
			{
				if(s==this[i])
				{
					this.splice(i, 1);
					return;
				}
			}
		}
		function fillTextArea (elementId, text)
		{
			document.getElementById(elementId).value = "";
			document.getElementById(elementId).value = text;
		}
		
		</script>
		<?php 
		$this->addToolbar();
		parent::display($tpl);
	}
	
	function getWMSLayerLocalFilter($theServer,$layer){
	
		if (count($theServer->Layers->Layer)==0) return "";
	
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->{'Filter'};
			}
		}
		return "";
	}
	
	function getLayerMinScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return $theLayer->ScaleMin;
			}
		}
		return "";
	}
	
	function getLayerMaxScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return $theLayer->ScaleMax;
			}
		}
		return "";
	}
	
	function getWMSLayerMinScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->ScaleMin;
			}
		}
		return "";
	}
	
	function getWMSLayerMaxScale($theServer,$layer){
		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->ScaleMax;
			}
		}
		return "";
	}
	
	function isLayerChecked($theServer,$layer){
	
		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;
	
		if (count($theServer->Layers->Layer)==0) return false;
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return true;
			}
		}
		return false;
	}
	
	function isWMSLayerChecked($theServer,$layer){
	
		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;
	
		if (count($theServer->Layers->Layer)==0) return false;
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return true;
			}
		}
		return false;
	}
	
	function getFeatureTypeAttributesList($theServer,$featureType){
	
		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
	
		foreach ($theServer->FeatureTypes->FeatureType as $ft )
		{
			if (strcmp($ft->{'Name'},$featureType->{'Name'})==0)
			{
				return $this->buildAttributesListString($ft->Attributes);
			}
		}
		return "";
	}
	
	function buildAttributesListString ($Attributes)
	{
		$attString ="";
		foreach($Attributes->{'Attribute'} as $att)
		{
			$attString .= $att;
			$attString .= ",";
		}
		$attString = substr  ($attString, 0, strlen ($attString)-1 );
		return $attString;
	}
		
	function getFeatureTypeRemoteFilter($theServer,$featureType)
	{
		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
	
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){
	
			if (strcmp($ft->{'Name'},$featureType->{'Name'})==0)
			{
				return $ft->{'RemoteFilter'};
			}
		}
	
		return "";
	}
	
	function getFeatureTypeLocalFilter($theServer,$featureType){
	
		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){
	
			if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){
	
				return $ft->{'LocalFilter'};
			}
		}
		return "";
	}
	
	function getLayerLocalFilter($theServer,$layer){
	
		if (count($theServer->Layers->Layer)==0) return "";
	
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0)
			{
				return $theLayer->{'Filter'};
			}
		}
		return "";
	}
	
	function isChecked($theServer,$featureType){
	
		if (strcasecmp($theServer->{"FeatureTypes"}['All'],"true")==0) return true;
	
		if (count($theServer->FeatureTypes->FeatureType )==0) return false;
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){
			if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){
					
				return true;
			}
	
		}
		return false;
	}
	/**
	 * Get the Layer local filter
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @return string
	 */
	function getWMTSLayerLocalFilter($theServer,$layer){
	
		if (count($theServer->Layers->Layer)==0) return "";
	
	
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return $theLayer->{'Filter'};
			}
		}
		return "";
	}
	
	/**
	 * Is current layer checked
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @return boolean
	 */
	function isWMTSLayerChecked($theServer,$layer){
		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;
		if (count($theServer->Layers->Layer)==0) return false;
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Get the current layer BBOX filter
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @return Ambiguous
	 */
	function getWMTSLayerBBOX($theServer, $layer){
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				$bbox = array();
				$bbox['minx'] = $theLayer->BoundingBox['minx'];
				$bbox['miny'] = $theLayer->BoundingBox['miny'];
				$bbox['maxx'] = $theLayer->BoundingBox['maxx'];
				$bbox['maxy'] = $theLayer->BoundingBox['maxy'];
				$bbox['spatial-operator'] = $theLayer->BoundingBox['spatialoperator'];
				return $bbox;
			}
		}
	}
	
	/**
	 * Get select minscaledenominator
	 * @param unknown_type $theServer
	 * @param unknown_type $layer
	 * @param unknown_type $TileMatrixSet
	 */
	function getWMTSTileMatrixSetMinScaleDenominator($theServer, $layer, $TileMatrixSet){
		foreach ($theServer->Layers->Layer as $theLayer )
		{
			if (strcmp($theLayer->{'Name'},$layer)==0)
			{
				foreach ($theLayer->TileMatrixSet as $theTileMatrixSet){
					if(strcmp($theTileMatrixSet['id'],$TileMatrixSet) == 0){
						return $theTileMatrixSet->{'minScaleDenominator'};
					}
				}
			}
		}
	}
	

	function genericPolicyFields($thePolicy){
		?>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_IDENTIFICATION'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONFIGURATION_ID'); ?></td>
					<td><input type="text" size="100" value="<?php echo $this->config;  ?>" disabled="disabled"></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_ID'); ?></td>
					<td><input type="text" size="100" name="newPolicyId" value="<?php echo $this->id ?>"></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONNECTOR_CLASS'); ?></td>
					<td><input type="text" size="100" name="servlet-class" id="servlet-class" value="<?php echo $this->servletClass;?>" disabled="disabled" size=50></td>
				</tr>
			
			</table>
		</fieldset>

		<fieldset class="adminform"><legend>Users and Groups</legend>
		<table class="admintable">
			<tr>
				<td><input
				<?php if (strcasecmp($thePolicy->Subjects['All'],'True')==0){echo 'checked';} ?>
					type="checkBox" name="AllUsers[]" id="AllUsers" 
					onclick="disableList('AllUsers','userNameList');disableList('AllUsers','groupNameList');">
				<?php echo JText::_( 'COM_EASYSDI_SERVICE_ANONYMOUS'); ?></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<th><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_USERS'); ?></b></th>
				<th></th>		
				<th><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_GROUPS'); ?></b></th>
			</tr>
			
			<tr>
				<?php
				
				$userSelected = array();
				foreach ($thePolicy->Subjects->User as $user)
				{
					$ou->value = $user;
					$userSelected[] =$ou;
					$ou = null;				
				}
				
				$groupSelected = array();
				foreach ($thePolicy->Subjects->Group as $group)
				{
					$or->value = $group;
					$groupSelected[] = $or;
					$or = null;
				}
				$disabled ="";
				if (strcasecmp($thePolicy->Subjects['All'],'True')==0)
				{
					$disabled = "disabled ";
				}
				?>
				<td><?php echo JHTML::_("select.genericlist",$this->rowsUser, 'userNameList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userSelected ); ?></td>
				<td></td>
				<td><?php echo JHTML::_("select.genericlist", $this->rowsGroup, 'groupNameList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $groupSelected ); ?></td>
			</tr>
		</table>
		</fieldset>
		<?php JHTML::_( 'behavior.modal' ); ?>
		<?php JHTML::_('behavior.calendar'); ?>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_AVAILIBILITY'); ?></legend>
		<table class="admintable">
			<tr>
				<th><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_DATE_TIME_FORMAT'); ?> </b>: <?php echo $thePolicy->{'AvailabilityPeriod'}->Mask; ?>
				</th>
				<td></td>
			</tr>
			<tr>
					
				<td>
					<label for="dateFrom" class="sdidate" ><?php echo JText::_( 'COM_EASYSDI_SERVICE_FROM'); ?></label>				
					<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->From->Date, "dateFrom","dateFrom","%d-%m-%Y"); ?>			
				</td>
			</tr>
			<tr>
				<td>
					<label for="dateTo" class="sdidate" ><?php echo JText::_( 'COM_EASYSDI_SERVICE_TO'); ?></label>			
					<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->To->Date, "dateTo","dateTo","%d-%m-%Y"); ?>
				</td>
					
			</tr>
			<input name="dateFormat" type="hidden" value"dd-mm-yyyy">
		</table>
		</fieldset>
		<?php 
	}
	
	
}
