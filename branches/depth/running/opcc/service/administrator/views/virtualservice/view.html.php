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
class Easysdi_serviceViewVirtualService extends JView
{

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
	
		$canDo	= Easysdi_serviceHelper::getActions();
		
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_VIRTUALSERVICE')." : ".$this->id, 'module.png');
		
		if(JRequest::getVar('layout',null)!='CSW' &&  $canDo->get('core.edit'))
			JToolBarHelper::addNew('virtualservice.addserver',JText::_( 'COM_EASYSDI_SERVICE_NEW_SERVER'));
		
		
		if ((!isset($this->id)&& $canDo->get('core.create')) || (isset($this->id)&& $canDo->get('core.edit'))){
			JToolBarHelper::save('virtualservice.save', 'JTOOLBAR_SAVE');
		}
		
		JToolBarHelper::cancel('virtualservice.cancel', 'JTOOLBAR_CANCEL');
	}
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		?>
		<script type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			if (task == 'virtualservice.addserver') 
			{
				addNewServer();
			}
			else if (task == 'virtualservice.save') 
			{
				if(document.getElementById('service_title').value == ""  )
				{
					alert ('<?php echo  JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_EDIT_VALIDATION_SERVICE_MD_ERROR');?>');	
					return;
				}
				var t = document.getElementById('supportedVersionsByConfig').value;
				if( !t || t == "null" || t=="undefined"){
					alert ('<?php echo  JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_EDIT_VALIDATION_SUPPORTED_VERSION_ERROR');?>');
					return;
				}
				if(document.getElementById('policyFile').value == ""  )
				{
					alert ('<?php echo  JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_EDIT_VALIDATION_POLICYFILE_ERROR');?>');	
					return;
				}
				if( document.getElementById('logPath').value == "" || 
					document.getElementById('logPrefix').value == "" || 
					document.getElementById('logSuffix').value == "" ){
					alert ('<?php echo  JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_EDIT_VALIDATION_LOGFILE_ERROR');?>');	
					return;
				}
				Joomla.submitform(task,document.getElementById('item-form'));
				
			}else{
				Joomla.submitform(task,document.getElementById('item-form'));
			}
		}
		
		function addNewServer()
		{
			var tr = document.createElement('tr');	
			tr.id = "remoteServerTableRow"+nbServer;
			
			var tdservice = document.createElement('td');
			var service = document.getElementById('service_0').cloneNode(true);
			service.name = 'service_'+nbServer;
			service.id = 'service_'+nbServer;
			service.options[0].selected= true;
			tdservice.appendChild(service);
			tr.appendChild(tdservice);
			
			var tdRemove = document.createElement('td');	
			var aButton = document.createElement('input');
			aButton.type="button";
			aButton.value="<?php echo JText::_( 'COM_EASYSDI_SERVICE_SERVICE_REMOVE' ); ?>";
			aButton.setAttribute("onClick","removeServer("+nbServer+");");
			tdRemove.appendChild(aButton);
			tr.appendChild(tdRemove);
			
			document.getElementById("remoteServerTable").appendChild(tr);
			nbServer = nbServer + 1;
			document.getElementById("nbServer").value = nbServer;	
		}
		
		function removeServer(servNo)
		{
			noeud = document.getElementById("remoteServerTable");
			var fils = document.getElementById("remoteServerTableRow"+servNo);
			noeud.removeChild(fils);	
			nbServer = nbServer - 1;	
			document.getElementById("nbServer").value = nbServer;
			serviceSelection(servNo);
		}
		function serviceSelection(servNo)
		{
			//Mettre à jour la liste des versions supportées par la config
			var supportedVersionsArray ;
			for(i = 0 ; i < nbServer ; i++)
			{
				var selectBoxName = 'service_'+i;
				var server = document.getElementById(selectBoxName);
				if(server.getSelected()[0].value == 0 ){
					if(nbServer == 1){
						document.getElementById("supportedVersionsByConfig").value=JSON.stringify(supportedVersionsArray);
						removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
						return;
					}
					continue;
				}
				var selected = server.getSelected()[0].text;
				var versions = selected.split(' - ')[2];
				var versionsArray = versions.substring(1, versions.length -1).split('-');

				if(supportedVersionsArray){
					var j = supportedVersionsArray.length;
					while(j--){
						if(!contains(versionsArray,supportedVersionsArray[j])){
							supportedVersionsArray.splice(1,j);
						}
					}
				}else{
					supportedVersionsArray = versionsArray;
				}
			}
			document.getElementById("supportedVersionsByConfig").value=JSON.stringify(supportedVersionsArray);
			removeAllElementChild( document.getElementById("supportedVersionsByConfigText"));
			if(supportedVersionsArray.length > 0)
				document.getElementById("supportedVersionsByConfigText").appendChild(createSupportedVersionByConfigTable(supportedVersionsArray)) ; 
		}

		function contains(arr, findValue) {
		    var i = arr.length;
		     
		    while (i--) {
		        if (arr[i] === findValue) return true;
		    }
		    return false;
		}
		function removeAllElementChild (cell){
			if ( cell.hasChildNodes() )
			{
			    while ( cell.childNodes.length >= 1 )
			    {
			        cell.removeChild( cell.firstChild );       
			    } 
			}
		}
		function createSupportedVersionByConfigTable(aNupportedVersionByConfig){
			var table = document.createElement('table');
			var tr = document.createElement('tr');
			table.appendChild(tr);
			
			for( var i = 0 ; i < aNupportedVersionByConfig.length ; i++ ){
				var td = document.createElement('td');
				var text = document.createTextNode(aNupportedVersionByConfig[i]);
				td.setAttribute("class","supportedversion");
				td.appendChild(text);
				tr.appendChild(td);
			}

			return table;
		}
		</script>
		
		<?php
		$params 			= JComponentHelper::getParams('com_easysdi_service');
		$this->xml 			= simplexml_load_file($params->get('proxyconfigurationfile'));
		$this->id 			= JRequest::getVar('id',null);
		
		if(!isset($layout)){
			if(isset($cid)){
				foreach ($cid as $id ){
					foreach ($xml->config as $config) {
						if (strcmp($config['id'],$id)==0){
							if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
							{
								$layout = "wms";
							}
							else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.WMTSProxyServlet")
							{
								$layout = "wmts";
							}
							else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
							{
								$layout = "csw";
							}
							else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
							{
								$layout = "wfs";
							}
						}
					}
				}
			}
		}
		if(isset ($this->id)) {
			foreach ($this->xml->config as $config) {
				if (strcmp($config['id'],$this->id)==0){
					$this->config = $config;
					$keywordArray = array();
					foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
					{
						array_push($keywordArray, $keyword) ;
					}
					$this->keywordString = implode(',',$keywordArray) ;
					break;
				}
			}
		}
		
		if(!isset($this->config))
			$this->config = new stdClass();
		
		$db 			= JFactory::getDBO();
		$db->setQuery("SELECT 0 AS id, '- Please select -' AS value UNION SELECT id, value FROM #__sdi_sys_serviceconnector WHERE state = 1") ;
		$this->serviceconnectorlist = $db->loadObjectList();
		
		$db->setQuery("SELECT 0 AS alias, '- Please select -' AS value UNION SELECT s.alias as alias,CONCAT(s.alias, ' - ', s.resourceurl,' - [',GROUP_CONCAT(syv.value SEPARATOR '-'),']') as value FROM #__sdi_physicalservice s
				INNER JOIN #__sdi_service_servicecompliance sc ON sc.service_id = s.id
				INNER JOIN #__sdi_sys_servicecompliance syc ON syc.id = sc.servicecompliance_id
				INNER JOIN #__sdi_sys_serviceversion syv ON syv.id = syc.serviceversion_id
				INNER JOIN #__sdi_sys_serviceconnector sycc ON sycc.id = syc.serviceconnector_id
				WHERE sc.servicetype = 'physical'
				AND sycc.value = '".JRequest::getVar('serviceconnector',null)."'
				AND s.state= 1
				GROUP BY s.id") ;
		$this->servicelist = $db->loadObjectList();
		
		$this->addToolbar();
		parent::display($tpl);
	}


	
	/**
	 *
	 * Generic servlet informations form :
	 * - config Id
	 * - servlet type
	 * - host translator
	 * @param unknown_type $config
	 */
	function genericServletInformationsHeader ($serviceconnector)
	{
		?>
		<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_SERVICE_LIST'); ?><span class="star">*</span></legend>
				<table class="admintable">
					<thead>
					</thead>
					<tbody id="remoteServerTable" >
					<?php
					$iServer=0;
					if(isset($this->config->{'remote-server-list'}))
					{
						$remoteServerList = $this->config->{'remote-server-list'};
						
						foreach ($remoteServerList->{'remote-server'} as $remoteServer){
							?><tr id="remoteServerTableRow<?php echo $iServer;?>">
								<td>
								<?php echo JHTML::_("select.genericlist",$this->servicelist, 'service_'.$iServer, 'size="1" onChange="serviceSelection('.$iServer.')"', 'alias', 'value', $remoteServer->alias); ?>
								</td>
								<?php if ($iServer > 0){?>	
								<td><input id="removeServerButton" type="button" onClick="javascript:removeServer(<?php echo $iServer;?>);" value="<?php echo JText::_( 'COM_EASYSDI_SERVICE_SERVICE_REMOVE' ); ?>"></td>
								<?php }?>
							</tr>
							<?php if ($serviceconnector == "CSW"){?>
							<tr>						
								<td colspan="4">
								<div id="specificGeonetowrk" >
									<table>	
									<tr>									
									<td><?php echo JText::_( 'COM_EASYSDI_SERVICE_MAX_RECORDS');?></td><td><input type="text" name="max-records_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'max-records'}; ?>" size=5></td>
									</tr>							
									</table>
								</div>
								</td>
								</tr>
							<?php
							}
						$iServer=$iServer+1;
						}
					}
					if($iServer == 0){
						?>
						<tr id="remoteServerTableRow<?php echo $iServer;?>">
								<td>
								<?php echo JHTML::_("select.genericlist",$this->servicelist, 'service_'.$iServer, 'size="1" onChange="serviceSelection('.$iServer.')"', 'alias', 'value', ''); ?>
								</td>
								
						</tr>
						<?php if ($serviceconnector == "CSW"){?>
						<tr>						
							<td colspan="4">
							<div id="specificGeonetowrk" >
								<table>	
								<tr>									
								<td><?php echo JText::_( 'COM_EASYSDI_SERVICE_MAX_RECORDS');?></td><td><input type="text" name="max-records_<?php echo $iServer;?>" value="" size=5></td>
								</tr>							
								</table>
							</div>
							</td>
							</tr>
						<?php
						}
						$iServer=$iServer+1;
					}
					?>
					</tbody>
				</table>
				</fieldset>
				<input type='hidden' name="nbServer" id="nbServer" value="<?php echo $iServer ; ?>" />
				<script>
				var nbServer = <?php echo $iServer?>;
				
				</script>
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_VIRTUALSERVICE_ID' );?></legend>
				<table class="admintable">
					<tr>
						<th>
						<?php echo JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_ID' );?> :<span class="star">*</span> 
						</th>
						<td colspan="4">
							<input class="inputbox required" type='text' name='id' value='<?php echo $this->id;?>' <?php if(!empty($this->id)){ echo "disabled='disabled'";};?>>
							<?php if(!empty($this->id)){ ?> <input type='hidden' name="id" id="id" value="<?php echo $this->id; ?>" /> <?php };?>
						</td>
					</tr>
					<tr>
						<th>
						<?php echo JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_VERSION' );?> : 
						</th>
						<td  id="supportedVersionsByConfigText" >
						<table>
						<tr>
						<?php 
						if(isset($this->config->{"supported-versions"}->{"version"})){
							foreach($this->config->{"supported-versions"}->{"version"} as $versionConfig){
								?>
								<td class="supportedversion">
								<?php 
								echo $versionConfig;
								?>
								</td>
								<?php 
							}
						}
						?>
						</tr>
						</table>
						</td>
						<td>
							<input type="hidden" id="supportedVersionsByConfig" name="supportedVersionsByConfig" value='<?php echo json_encode ($versionConfig); ?>'></input>
						</td>
					</tr>
				</table>
			</fieldset>
				
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_VIRTUALSERVICE_HOST_TRANSLATOR'); ?></legend>
				<table class="admintable">
					<tr>
						<td><input size="100" type="text" name="hostTranslator" value="<?php  if(isset($this->config->{'host-translator'})) echo $this->config->{'host-translator'}; ?>"></td>
					</tr>
				</table>
			</fieldset>
			
				
			<?php 
		}
		/**
		 *
		 * Generic servlet informations form :
		 * -  exception mode
		 * -  policy file
		 * -  log file parameter
		 * @param unknown_type $config
		 */
		function genericServletInformationsFooter ()
		{
			?>
			
						<fieldset class="adminform" id="exceptionMode"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_EXCEPTION_MANAGEMENT'); ?></legend>
						<table class="admintable">
							<tr>
								<td><input type="radio" name="exception_mode" value="permissive" <?php if (strcmp($this->config->{"exception"}->{"mode"},"permissive")==0 || !$this->config->{"exception"}->{"mode"}){echo "checked";} ?> > <?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_EXCEPTION_MANAGEMENT_MODE_PERMISSIVE'); ?><br></td>
							</tr>
							<tr>
								<td><input type="radio" name="exception_mode" value="restrictive" <?php if (strcmp($this->config->{"exception"}->{"mode"},"restrictive")==0){echo "checked";} ?> > <?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_EXCEPTION_MANAGEMENT_MODE_RESTRICTIVE'); ?><br></td>
							</tr>
						</table>
					</fieldset>
					<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_FILE_LOCATION'); ?><span class="star">*</span></legend>
					<table class="admintable">
						<tr>
							<td><input name="policyFile" id="policyFile" type="text" size=100
								value="<?php if(isset($this->config->{"authorization"}->{"policy-file"})) echo $this->config->{"authorization"}->{"policy-file"}; ?>"></td>
						</tr>
					</table>
					</fieldset>
			
					<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_XSLT_PATH'); ?></legend>
					<table class="admintable">
						<tr>
							<td><input name="xsltPath" type="text" size=100
								value="<?php if(isset($this->config->{"xslt-path"}->{"url"})) echo $this->config->{"xslt-path"}->{"url"}; ?>"></td>
						</tr>
					</table>
					</fieldset>
					<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_LOG_CONFIGURATION'); ?></legend>
					<table class="admintable">
						
						<tr>
							<td colspan = "2">
								<table class="admintable">
									<tr>
										<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_FILE_NAME'); ?></td>
										<td><b>[<?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PREFIX'); ?>].[YYYYMMDD].[<?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_SUFFIX'); ?>].[<?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_EXTENSION'); ?>]</b></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan = "2">
							<table class="admintable">
								<tr>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PATH'); ?><span class="star">*</span></th>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PREFIX'); ?><span class="star">*</span></th>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_SUFFIX'); ?><span class="star">*</span></th>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_EXTENSION'); ?><span class="star">*</span></th>
								</tr>
								<tr>
									<td><input name="logPath"  id="logPath" size=70 type="text"
										value="<?php if(isset($this->config->{"log-config"}->{"file-structure"}->{"path"})) echo $this->config->{"log-config"}->{"file-structure"}->{"path"};?>"></td>
									<td><input name="logPrefix" id="logPrefix" type="text"
										value="<?php if (isset($this->config->{"log-config"}->{"file-structure"}->{"prefix"})) echo $this->config->{"log-config"}->{"file-structure"}->{"prefix"};?>"></td>
									<td><input name="logSuffix" id="logSuffix" type="text"
										value="<?php if(isset($this->config->{"log-config"}->{"file-structure"}->{"suffix"})) echo $this->config->{"log-config"}->{"file-structure"}->{"suffix"};?>"></td>
									<td><input name="logExt" type="text"
										value="<?php if(isset($this->config->{"log-config"}->{"file-structure"}->{"extension"})) echo $this->config->{"log-config"}->{"file-structure"}->{"extension"};?>"></td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan = "2">
								<table class="admintable">
									<tr>
										<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_LEVEL'); ?></td>
										<td><select name="logLevel">
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"OFF")==0){echo "selected";} ?>
												value="OFF">OFF</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"FATAL")==0){echo "selected";} ?>
												value="FATAL">FATAL</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"ERROR")==0){echo "selected";} ?>
												value="ERROR">ERROR</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"WARN")==0){echo "selected";} ?>
												value="WARN">WARN</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"INFO")==0){echo "selected";} ?>
												value="INFO">INFO</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"DEBUG")==0){echo "selected";} ?>
												value="DEBUG">DEBUG</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"TRACE")==0){echo "selected";} ?>
												value="TRACE">TRACE</option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"log-level"},"ALL")==0){echo "selected";} ?>
												value="TRACE">ALL</option>
										</select></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan = "2">
								<table class="admintable">
									<tr>
										<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PERIOD'); ?></td>
										<td><select name="logPeriod">
											<option <?php if (strcmp($this->config->{"log-config"}->{"file-structure"}->{"period"},"daily")==0){echo "selected";} ?>
												value="daily"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PERIOD_DAILY'); ?></option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"file-structure"}->{"period"},"monthly")==0){echo "selected";} ?>
												value="monthly"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PERIOD_MONTHLY'); ?></option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"file-structure"}->{"period"},"weekly")==0){echo "selected";} ?>
												value="weekly"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PERIOD_WEEKLY'); ?></option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"file-structure"}->{"period"},"annualy")==0){echo "selected";} ?>
												value="annually"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PERIOD_ANNUALLY'); ?></option>
										</select></td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					</fieldset>
					<?php 
			}
			
			
}
