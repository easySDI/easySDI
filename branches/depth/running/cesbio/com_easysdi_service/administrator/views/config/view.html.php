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
class Easysdi_serviceViewConfig extends JView
{

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
	
		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_CONFIG')." : ".$this->id, 'service.png');
		JToolBarHelper::addNew('config.addserver',JText::_( 'COM_EASYSDI_SERVICE_NEW_SERVER'));
		JToolBarHelper::save('config.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::back('JTOOLBAR_BACK','index.php?option=com_easysdi_service&view=configs');
	}
	
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		?>
		<script type="text/javascript">
		
		function addNewServer()
		{
			var tr = document.createElement('tr');	
			tr.id = "remoteServerTableRow"+nbServer;
			
			var tdservice = document.createElement('td');
			var service = document.getElementById('service_0').cloneNode(true);
			service.name = 'service_'+nbServer;
			service.id = 'service_'+nbServer;
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
		}
		
		Joomla.submitbutton = function(task)
		{
			if (task == 'config.addserver') {
				addNewServer();
			}
			else {
				Joomla.submitform(task,document.getElementById('adminForm'));
			}
		}
		
		function removeServer(servNo)
		{
			noeud = document.getElementById("remoteServerTable");
			var fils = document.getElementById("remoteServerTableRow"+servNo);
			noeud.removeChild(fils);			
		}
		function serviceSelection(servNo)
		{
			//Mettre à jour la liste des versions supportées par la config
			for(i = 0 ; i < nbServer ; i++)
			{
				
			}
		}
		</script>
		
		<?php
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$params 		= JComponentHelper::getParams('com_easysdi_core');
		$this->xml 		= simplexml_load_file($params->get('proxyconfigurationfile'));
		$this->id 		= JRequest::getVar('id',null);
		
		if(isset ($this->id)) {
			foreach ($this->xml->config as $config) {
				if (strcmp($config['id'],$this->id)==0){
					$this->config = $config;
					$keywordArray = array();
					foreach ($config->{"service-metadata"}->{'KeywordList'}->Keyword as $keyword)
					{
						array_push($keywordArray, $keyword) ;
					}
					$this->keywordString = explode(',',$keywordArray) ;
					break;
				}
			}
		}
		
		if(!isset($this->config))
			$this->config = new stdClass();
		
		
		$db 			= JFactory::getDBO();
		$db->setQuery("SELECT 0 AS id, '- Please select -' AS value UNION SELECT id, value FROM #__sdi_sys_serviceconnector") ;
		$this->serviceconnectorlist = $db->loadObjectList();
		
// 		$db->setQuery("SELECT  id, alias, CONCAT (alias, ' - ', resourceurl) as value FROM #__sdi_service") ;
		$db->setQuery("SELECT s.id, s.alias,CONCAT(s.alias, ' - ', s.resourceurl,' - [',GROUP_CONCAT(syv.value SEPARATOR '-'),']') as value FROM #__sdi_service s
				INNER JOIN #__sdi_service_servicecompliance sc ON sc.service_id = s.id
				INNER JOIN #__sdi_sys_servicecompliance syc ON syc.id = sc.servicecompliance_id
				INNER JOIN #__sdi_sys_serviceversion syv ON syv.id = syc.serviceversion_id") ;
		$this->servicelist = $db->loadObjectList();
		
// 		$db->setQuery("SELECT  s.alias, GROUP_CONCAT(syv.value) FROM #__sdi_service s 
// 											INNER JOIN #__sdi_service_servicecompliance sc ON sc.service_id = s.id 
// 											INNER JOIN #__sdi_sys_servicecompliance syc ON syc.id = sc.servicecompliance_id
// 											INNER JOIN #__sdi_sys_serviceversion syv ON syv.id = syc.serviceversion_id") ;
// 		$this->servicelist = $db->loadObjectList();
		
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
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_LEGEND_CONFIG_ID' );?></legend>
				<table class="admintable">
					<tr>
						<th>
						<?php echo JText::_( 'COM_EASYSDI_SERVICE_CONFIG_ID' );?> : 
						</th>
						<td colspan="4">
							<input type='text' name='id' value='<?php echo $this->id;?>'>
						</td>
					</tr>
					<tr>
						<th>
						<?php echo JText::_( 'COM_EASYSDI_SERVICE_CONFIG_VERSION' );?> : 
						</th>
						<td  id="supportedVersionsByConfigText" >
						<table>
						<tr>
						<?php 
						foreach($this->config->{"supported-versions"}->{"version"} as $versionConfig){
							?>
							<td class="supportedversion">
							<?php 
							echo $versionConfig;
							?>
							</td>
							<?php 
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
				
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONFIG_HOST_TRANSLATOR'); ?></legend>
				<table class="admintable">
					<tr>
						<td><input size="100" type="text" name="hostTranslator"
							value="<?php  echo $this->config->{'host-translator'}; ?>"></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_CONFIG_SERVICE_LIST'); ?></legend>
				<table class="admintable">
					<thead>
					<tr>
						<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_SERVICE'); ?></th>
						<th></th>
					</tr>
					
					</thead>
					<tbody id="remoteServerTable" >
					<?php
					$remoteServerList = $this->config->{'remote-server-list'};
					$iServer=0;
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
					?></tbody>
				</table>
				</fieldset>
				
				<script>
				var nbServer = <?php echo $iServer?>;
				var service = '<?php echo $serviceType?>';
				</script>
				
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
					<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_POLICY_FILE_LOCATION'); ?></legend>
					<table class="admintable">
						<tr>
							<td><input name="policyFile" type="text" size=100
								value="<?php echo $this->config->{"authorization"}->{"policy-file"}; ?>"></td>
						</tr>
					</table>
					</fieldset>
			
					<fieldset class="adminform"><legend><?php echo JText::_( 'COM_EASYSDI_SERVICE_XSLT_PATH'); ?></legend>
					<table class="admintable">
						<tr>
							<td><input name="xsltPath" type="text" size=100
								value="<?php echo $this->config->{"xslt-path"}->{"url"}; ?>"></td>
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
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PATH'); ?></th>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_PREFIX'); ?></th>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_SUFFIX'); ?></th>
									<th><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_EXTENSION'); ?></th>
								</tr>
								<tr>
									<td><input name="logPath"  id="logPath" size=70 type="text"
										value="<?php  echo $this->config->{"log-config"}->{"file-structure"}->{"path"};?>"></td>
									<td><input name="logPrefix" id="logPrefix" type="text"
										value="<?php  echo $this->config->{"log-config"}->{"file-structure"}->{"prefix"};?>"></td>
									<td><input name="logSuffix" id="logSuffix" type="text"
										value="<?php  echo $this->config->{"log-config"}->{"file-structure"}->{"suffix"};?>"></td>
									<td><input name="logExt" type="text"
										value="<?php  echo $this->config->{"log-config"}->{"file-structure"}->{"extension"};?>"></td>
								</tr>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan = "2">
								<table class="admintable">
									<tr>
										<td class="key"><?php echo JText::_( 'COM_EASYSDI_SERVICE_LOG_MODE'); ?></td>
										<td><select name="logger">
											<option <?php if (strcmp($this->config->{"log-config"}->{"logger"},"org.apache.log4j.Logger")==0){echo "selected";} ?>
												value="org.apache.log4j.Logger"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE_LOG4J'); ?></option>
											<option <?php if (strcmp($this->config->{"log-config"}->{"logger"},"org.easysdi.proxy.log.ProxyLogger")==0){echo "selected";} ?>
												value="org.easysdi.proxy.log.ProxyLogger"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE_EASYSDI'); ?></option>
										</select></td>
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
