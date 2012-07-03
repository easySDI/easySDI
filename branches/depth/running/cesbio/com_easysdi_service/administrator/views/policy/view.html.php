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
	
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_POLICY').' ['.$this->id.']', 'service.png');
		JToolBarHelper::save('policy.save', 'JTOOLBAR_SAVE');
		
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_service&view=policies&config='.$this->config.'&connector='.$this->connector);
	}
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{

		$params 				= JComponentHelper::getParams('com_easysdi_core');
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
		$db->setQuery( "SELECT #__users.username as value, #__users.name as text FROM #__users INNER JOIN #__sdi_user ON  #__users.id = #__sdi_user.user_id ORDER BY text" );
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
		function disableButton(chkBox,button){

			if (document.getElementById(chkBox).checked==true){
			document.getElementById(button).disabled=true;
			}else{
			document.getElementById(button).disabled=false;
			}
			}

			function activateAttributeList(server,featureType)
			{
				if (document.getElementById('selectAttribute@'+server+'@'+featureType).checked==true){
					document.getElementById('AttributeList@'+server+'@'+featureType).disabled=false;
					document.getElementById('AttributeList@'+server+'@'+featureType).value="";
				}
				else
				{
					document.getElementById('AttributeList@'+server+'@'+featureType).disabled=true;
					document.getElementById('AttributeList@'+server+'@'+featureType).value="";
				}
			}
			function activateFeatureType(server,featureType){


				if (document.getElementById('featuretype@'+server+'@'+featureType).checked==true){
					document.getElementById('LocalFilter@'+server+'@'+featureType).disabled=false;
					document.getElementById('LocalFilter@'+server+'@'+featureType).value = "";
					document.getElementById('RemoteFilter@'+server+'@'+featureType).disabled=false;		
					document.getElementById('RemoteFilter@'+server+'@'+featureType).value = "";
					document.getElementById('selectAttribute@'+server+'@'+featureType).checked = false;
					document.getElementById('selectAttribute@'+server+'@'+featureType).disabled = false;
					document.getElementById('AttributeList@'+server+'@'+featureType).disabled=true;
					document.getElementById('AttributeList@'+server+'@'+featureType).value="";

				}
				else
				{	
					document.getElementById('AllFeatureTypes@'+nb).checked = false;
					document.getElementById('LocalFilter@'+server+'@'+featureType).disabled=true;
					document.getElementById('LocalFilter@'+server+'@'+featureType).value = "";
					document.getElementById('RemoteFilter@'+server+'@'+featureType).disabled=true;		
					document.getElementById('RemoteFilter@'+server+'@'+featureType).value = "";
					document.getElementById('selectAttribute@'+server+'@'+featureType).checked = false;
					document.getElementById('selectAttribute@'+server+'@'+featureType).disabled = true;
					document.getElementById('AttributeList@'+server+'@'+featureType).disabled=true;
					document.getElementById('AttributeList@'+server+'@'+featureType).value="";
					
				}
			}

			function CheckQuery(server,featureType)
			{
					var remote = document.getElementById('RemoteFilter@'+server+'@'+featureType).value;
					var local = document.getElementById('LocalFilter@'+server+'@'+featureType).value;
					if (remote.length == 0 && local.length >0)
					{
						geoQueryValid[geoQueryValid.length] = 'RemoteFilter@'+server+'@'+featureType;
						document.getElementById('RemoteFilter@'+server+'@'+featureType).style.backgroundColor = "#E2A09B";
					}
					else
					{
						geoQueryValid.remove('RemoteFilter@'+server+'@'+featureType);
						
						document.getElementById('RemoteFilter@'+server+'@'+featureType).style.backgroundColor = document.getElementById('LocalFilter@'+server+'@'+featureType).style.backgroundColor;
					}
			}

			Array.prototype.remove=function(s){
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

			function activateLayer(server,layerName){


				if (document.getElementById('layer@'+server+'@'+layerName).checked==true){
					document.getElementById('scaleMin@'+server+'@'+layerName).disabled=false;
					document.getElementById('scaleMax@'+server+'@'+layerName).disabled=false;
					document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=false;
					
				}else{
					document.getElementById('AllLayers@'+server).checked = false;
					document.getElementById('scaleMin@'+server+'@'+layerName).disabled=true;
					document.getElementById('scaleMin@'+server+'@'+layerName).value ="";
					document.getElementById('scaleMax@'+server+'@'+layerName).disabled=true;
					document.getElementById('scaleMax@'+server+'@'+layerName).value ="";
					document.getElementById('LocalFilter@'+server+'@'+layerName).disabled=true;
					document.getElementById('LocalFilter@'+server+'@'+layerName).value ="";	
				}
			}
		function disableServersLayers ()
		{
			var nb = 0;
			var iLay = 0;
			var display = "block";
			var check = document.getElementById('AllServers').checked;
			if (document.getElementById('AllServers').checked)
			{
				display="none";
			}
			
			while (document.getElementById('remoteServerTable@'+nb) != null)
			{
				document.getElementById('remoteServerTable@'+nb).style.display=display;
				document.getElementById('AllLayers@'+nb).checked = check;
				while (document.getElementById('layer@'+nb+'@'+iLay) != null)
				{
					document.getElementById('layer@'+nb+'@'+iLay).checked = check;
					document.getElementById('scaleMin@'+nb+'@'+iLay).disabled=!check;
					document.getElementById('scaleMax@'+nb+'@'+iLay).disabled=!check;
					document.getElementById('LocalFilter@'+nb+'@'+iLay).disabled=!check;
					iLay ++;
				}
				iLay = 0;
				nb ++;
			}	
		}

		function disableServersFeatureTypes ()
		{
			var nb = 0;
			var iFeat = 0;
			var display = "block";
			var check = document.getElementById('AllServers').checked;
			if (document.getElementById('AllServers').checked)
			{
				display="none";
			}
			
			while (document.getElementById('remoteServerTable@'+nb) != null)
			{
				document.getElementById('remoteServerTable@'+nb).style.display=display;
				document.getElementById('AllFeatureTypes@'+nb).checked = check;
				while (document.getElementById('featuretype@'+nb+'@'+iFeat) != null)
				{
					document.getElementById('featuretype@'+nb+'@'+iFeat).checked = check;
					document.getElementById('selectAttribute@'+nb+'@'+iFeat).disabled=check;
					document.getElementById('AttributeList@'+nb+'@'+iFeat).disabled=check;
					document.getElementById('RemoteFilter@'+nb+'@'+iFeat).disabled=check;
					document.getElementById('LocalFilter@'+nb+'@'+iFeat).disabled=check;
					iFeat ++;
				}
				iFeat = 0;
				nb ++;
			}	
		}

		function disableLayers(iServ)
		{
			var iLay = 0;
			var check = document.getElementById('AllLayers@'+iServ).checked;
			
			while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
			{
				document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
				document.getElementById('scaleMin@'+iServ+'@'+iLay).disabled=check;
				document.getElementById('scaleMax@'+iServ+'@'+iLay).disabled=check;
				document.getElementById('LocalFilter@'+iServ+'@'+iLay).disabled=check;
				
				iLay ++;
			}
		}

		function disableWMTSLayers(iServ)
		{
			var iLay = 0;
			var check = document.getElementById('AllLayers@'+iServ).checked;
			while (document.getElementById('layer@'+iServ+'@'+iLay) != null)
			{
				document.getElementById('layer@'+iServ+'@'+iLay).checked = check;
				document.getElementById('layer@'+iServ+'@'+iLay).disabled = check;
				document.getElementById('fsLayer@'+iServ+'@'+iLay).disabled = check;
				document.getElementById('tableLayer@'+iServ+'@'+iLay).style.display = "none";
				iLay ++;
			}
		}

		function enableTableLayer(iServ,iLay)
		{
			var check = document.getElementById('layer@'+iServ+'@'+iLay).checked;
			var display = "none";
			if(check){
				display="block";
			}
			document.getElementById('tableLayer@'+iServ+'@'+iLay).style.display = display;
		}

		function disableWMTSServersLayers ()
		{
			var nb = 0;
			var iLay = 0;
			var display = "block";
			var check = document.getElementById('AllServers').checked;
			if (document.getElementById('AllServers').checked)
			{
				display="none";
			}
			
			while (document.getElementById('remoteServerTable@'+nb) != null)
			{
				document.getElementById('remoteServerTable@'+nb).style.display=display;
				document.getElementById('AllLayers@'+nb).checked = check;
				while (document.getElementById('layer@'+nb+'@'+iLay) != null)
				{
					document.getElementById('layer@'+nb+'@'+iLay).checked = check;
					iLay ++;
				}
				iLay = 0;
				nb ++;
			}	
		}

		function disableFeatureTypes(iServ)
		{
			var iFeat = 0;
			var check = document.getElementById('AllFeatureTypes@'+iServ).checked;
			
			while (document.getElementById('featuretype@'+iServ+'@'+iFeat) != null)
			{
				document.getElementById('featuretype@'+iServ+'@'+iFeat).checked = check;
				document.getElementById('selectAttribute@'+iServ+'@'+iFeat).disabled=check;
				document.getElementById('AttributeList@'+iServ+'@'+iFeat).disabled=check;
				document.getElementById('RemoteFilter@'+iServ+'@'+iFeat).disabled=check;
				document.getElementById('LocalFilter@'+iServ+'@'+iFeat).disabled=check;
				
				iFeat ++;
			}
		}

		function addNewMetadataToExclude(nbParam,nbServer)
		{
			var tr = document.createElement('tr');	
			var tdParam = document.createElement('td');	
			var inputParam = document.createElement('input');
			inputParam.size=200;
			inputParam.type="text";
			inputParam.name="param_"+nbServer+"_"+document.getElementById(nbParam).value;
			tdParam.appendChild(inputParam);
			tr.appendChild(tdParam);
			document.getElementById("metadataParamTable").appendChild(tr);
			document.getElementById(nbParam).value = document.getElementById(nbParam).value +1 ;
		}

		function disableVisibilitiesCheckBoxes ()
		{
			var check = document.getElementById('AllVisibilities').checked;

			var visibilityArray = new Array();
			visibilityArray = document.getElementsByName('visibility[]');
			for ( i = 0 ; i < visibilityArray.length ; i++)
			{
				visibilityArray[i].disabled = check;
				visibilityArray[i].checked = check;
			}
		}
		function disableStatusCheckBoxes ()
		{
			var check = document.getElementById('AllStatus').checked;

			var statusArray = new Array();
			statusArray = document.getElementsByName('status[]');
			for ( i = 0 ; i < statusArray.length ; i++)
			{
				statusArray[i].disabled = check;
				statusArray[i].checked = check;
			}
			if(check)
			{
				document.getElementsByName('objectversion_mode')[0].disabled = check;
				document.getElementsByName('objectversion_mode')[1].disabled = check;
				document.getElementsByName('objectversion_mode')[1].checked = check;
			}
			
		}

		function disableCheckBoxes (nameAll, name)
		{
			var check = document.getElementById(nameAll).checked;

			var objectArray = new Array();
			objectArray = document.getElementsByName(name);
			for ( i = 0 ; i < objectArray.length ; i++)
			{
				objectArray[i].disabled = check;
				objectArray[i].checked = check;
			}
		}
		function disableVersionModeRadio()
		{
			var check = !document.getElementById('published').checked;
			document.getElementsByName('objectversion_mode')[0].disabled = check;
			document.getElementsByName('objectversion_mode')[1].disabled = check;
			
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

	function genericPolicyFields($thePolicy){
		?>
		<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY IDENTIFICATION'); ?></legend>
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
					
				<td><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_FROM'); ?></b> 	
					<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->From->Date, "dateFrom","dateFrom","%d-%m-%Y"); ?>		
					</td>
				<td><b><?php echo JText::_( 'COM_EASYSDI_SERVICE_TO'); ?></b>		
					<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->To->Date, "dateTo","dateTo","%d-%m-%Y"); ?>
					</td>
					
			</tr>
			<input name="dateFormat" type="hidden" value"dd-mm-yyyy">
		</table>
		</fieldset>
		<?php 
	}
}
