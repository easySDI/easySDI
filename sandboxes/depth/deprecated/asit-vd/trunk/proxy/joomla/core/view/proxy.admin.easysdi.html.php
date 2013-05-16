<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) EasySDI Community
 * For more information : www.easysdi.org
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or 
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html. 
 */
/*foreach($_POST as $key => $val) 
echo '$_POST["'.$key.'"]='.$val.'<br />';*/

defined('_JEXEC') or die('Restricted access');

class HTML_proxy {

	
	function configComponent($xmlConfig){

	JToolBarHelper::title( JText::_(  'EASYSDI_EASYSDI_CONFIGURE COMPONENT' ), 'generic.png' );

	$option = JRequest::getVar('option'); 

	?>
		
	<form name='adminForm' id='adminForm' action='index.php' method='POST'>	
	<input
	type='hidden' name='option' value='<?php echo $option;?>'> <input
	type='hidden' name='task' value=''> 
	
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_EASYSDI_CONFIG FILE PATH' );?></legend>
<table class="admintable">
	<tr>
		<td colspan="4"><input type='text' name='filePath' size="200"
			value='<?php echo $xmlConfig->proxy->configFilePath;?>'></td>
	</tr>
</table>
</fieldset>

</form>

<?php
}
	
	function ctrlPanel(){
		JToolBarHelper::title( JText::_(  'EASYSDI_EASYSDI CONTROL PANEL' ), 'generic.png' );
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
			
$pane		=& JPane::getInstance('sliders');
	echo $pane->startPane("content-pane");
			echo $pane->startPanel( JText::_('EASYSDI_EASYSDI MODULES'), 'cpanel-panel-1' );
		?>
	<div id="cpanel">
		<?php
		$link = 'index.php?option=com_easysdi&amp;task=componentConfig';
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
				
					<?php 
					$text = JText::_( 'EASYSDI_COMPONENT CONFIGURATION' );
					echo JHTML::_('image.site',  'icon-48-component.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
		
	<?php	
		$link = 'index.php?option=com_easysdi&amp;task=showConfigList';


?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
					<?php 
						$text = JText::_( 'EASYSDI_PROXY CONFIGURATION' );					
						echo JHTML::_('image.site',  'icon-48-config.png', '/templates/'. $template .'/images/header/', NULL, NULL, $text ); ?>
					<span><?php echo $text; ?></span></a>
			</div>
		</div>
	
	</div> 
		<?php
echo $pane->endPanel();
	?>
<div id="rightcpanel">
<?php 

	
		echo $pane->startPanel( JText::_('EASYSDI_LICENSE'), 'cpanel-panel-licence' );
		?><PRE>
		<?php 		 
		$file = file_get_contents (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi'.DS.'license.txt');
		Echo $file;
		?></PRE>
		<?php
		echo $pane->endPanel();
	

	echo $pane->endPane();
	?>
	
	</div>
	
		<?php 
	}
	
	
	function alter_array_value_with_Jtext(&$rows)
	{		
		if (count($rows)>0){
		  foreach($rows as $key => $row) {		  	
       		$rows[$key]->text = JText::_($rows[$key]->text);
  		}			    
		}
	}
	
		
	/**
	 * 
	 * Generic servlet informations form :
	 * - config Id
	 * - servlet type
	 * - host translator
	 * @param unknown_type $config
	 */
	function genericServletInformationsHeader ($config, $configId, $servletClass, $availableServletList,$availableVersion,$serviceType)
	{
		$supportedVersionsByConfigArray = array();
		foreach($config->{"supported-versions"}->{"version"} as $versionConfig){
			array_push($supportedVersionsByConfigArray,(string) $versionConfig);
		}
		
		?>
		<div id="progress">
			<img id="progress_image"  src="components/com_easysdi_proxy/templates/images/loader.gif" alt="">
		</div>
		<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_SERVLET TYPE' );?></legend>
			<table class="admintable">
				<tr>
					<td>
					<?php 
					echo JHTML::_("select.genericlist",$availableServletList, 'servletClass', 'size="1" onChange="submit()"', 'value', 'text', $servletClass ); ?>
					</td>
					<?php if ($servletClass == "org.easysdi.proxy.csw.CSWProxyServlet"){?>
					<td>
						<?php echo JText::_( 'EASYSDI_SERVLET_CSW_HARVESTING' );?>
						<input type="checkbox" name="harvestingConfig" value="1" <?php if($config->{"harvesting-config"}=="true"){echo "checked";}?> />
					</td>
					<?php }?>
					
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_CONFIG ID' );?></legend>
			<table class="admintable">
				<tr>
					<th>
					<?php echo JText::_( 'EASYSDI_PROXY_ID' );?> : 
					</th>
					<td colspan="4"><input type='text' name='newConfigId'
						value='<?php echo $configId;?>'>
					</td>
				</tr>
				<tr>
					<th>
					<?php echo JText::_( 'EASYSDI_VERSION' );?> : 
					</th>
					<td  id="supportedVersionsByConfigText" >
					<table>
					<tr>
					<?php 
					foreach ($supportedVersionsByConfigArray as $vc){
						?>
						<td class="supportedversion">
						<?php 
						echo $vc;
						?>
						</td>
						<?php 
					}
					?>
					</tr>
					</table>
					</td>
					<td>
						<input type="hidden" id="supportedVersionsByConfig" name="supportedVersionsByConfig" value='<?php echo json_encode ($supportedVersionsByConfigArray); ?>'></input>
					</td>
				</tr>
			</table>
		</fieldset>
			
			
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_HOST TRANSLATOR'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input size="100" type="text" name="hostTranslator"
						value="<?php  echo $config->{'host-translator'}; ?>"></td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_SERVER LIST'); ?></legend>
			<table class="admintable">
				<thead>
				<tr>
					<th><?php echo JText::_( 'EASYSDI_SERVER_ALIAS'); ?></th>
					<th><?php echo JText::_( 'EASYSDI_URL'); ?></th>
					<th><?php echo JText::_( 'EASYSDI_USER'); ?></th>
					<th><?php echo JText::_( 'EASYSDI_PASSWORD'); ?></th>
					<th colspan="6"><?php echo JText::_( 'EASYSDI_VERSION'); ?></th>
				</tr>
				</thead>
				<tbody id="remoteServerTable" >
				<?php
				$remoteServerList = $config->{'remote-server-list'};
				$iServer=0;
				foreach ($remoteServerList->{'remote-server'} as $remoteServer){
					?><tr id="remoteServerTableRow<?php echo $iServer;?>">
							<td><input type="text" name="ALIAS_<?php echo $iServer;?>" id="ALIAS_<?php echo $iServer;?>" value="<?php echo $remoteServer->alias; ?>" size=20></td>
							<td><input type="text" id="URL_<?php echo $iServer;?>" name="URL_<?php echo $iServer;?>" value="<?php echo $remoteServer->url; ?>" size=70></td>
							<td><input id="USER_<?php echo $iServer;?>" name="USER_<?php echo $iServer;?>" type="text" value="<?php echo $remoteServer->user; ?>"></td>
							<td><input id="PASSWORD_<?php echo $iServer;?>" name="PASSWORD_<?php echo $iServer;?>" type="password" value="<?php echo $remoteServer->password; ?>">	</td>
							<td>
								<a href="#" onclick="javascript:negoVersionServer(<?php echo $iServer;?>,'<?php echo $serviceType; ?>', '<?php echo str_replace('"','&quot;',json_encode ($availableVersion)); ?>');" >
									<img class="helpTemplate" src="../templates/easysdi/icons/silk/arrow_switch.png" alt="<?php echo JText::_("EASYSDI_VERSION") ?>"/>
								</a>
							</td>
							<td><?php HTML_proxy::getTableVersionForService ($iServer,$remoteServer,$servletClass,$availableVersion)?></td>
							<?php if ($iServer > 0){?>	
							<td >		
							<input id="removeServerButton" type="button" onClick="javascript:removeServer(<?php echo $iServer;?>);" value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>">
							</td>
							<?php }?>
							
					</tr>
					<?php if ($servletClass == "org.easysdi.proxy.csw.CSWProxyServlet"){?>
					<tr>						
						<td colspan="4">
						<div id="specificGeonetowrk" >
							<table>	
							<tr>									
							<td><?php echo JText::_( 'EASYSDI_MAX_RECORDS');?></td><td><input type="text" name="max-records_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'max-records'}; ?>" size=5></td>
							</tr>
							<tr>
							<td><?php echo JText::_( 'EASYSDI_LOGIN_SERVICE');?></td><td><input type="text" name="login-service_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'login-service'}; ?>" size=70></td>
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
			var availableVersions = <?php echo json_encode ($availableVersion); ?>;
			</script>
			
		<?php 
	}
	
	function getTableVersionForService ($iServer,$remoteServer,$serviceType,$availableVersion){
		$array_version = array();
		foreach ($remoteServer->{"supported-versions"}->{"version"} as $version){
			$array_version[]=$version;
		}?>
		<table>
		<tr>
		<?php 
		foreach ($availableVersion as $version){
			if (in_array($version,$array_version)){
				?>
				<td class="supportedversion" id="<?php echo $version;?>_<?php echo $iServer;?>"><?php echo $version;?>
				<input type='hidden' name="<?php echo $version;?>_<?php echo $iServer;?>_state" id="<?php echo $version;?>_<?php echo $iServer;?>_state" value="supported" >
				</td>
				<?php 
			}else{
				?>
				<td class="unsupportedversion" id="<?php echo $version;?>_<?php echo $iServer;?>"><?php echo $version;?>
				<input type='hidden' name="<?php echo $version;?>_<?php echo $iServer;?>_state" id="<?php echo $version;?>_<?php echo $iServer;?>_state" value="unsupported" >
				</td>
				<?php
			}
			 
		}
		?>
		</tr>
		</table>
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
	function genericServletInformationsFooter ($config)
	{
		?>
				<fieldset class="adminform" id="exceptionMode"><legend><?php echo JText::_( 'PROXY_CONFIG_EXCEPTION_MANAGEMENT_MODE'); ?></legend>
				<table class="admintable">
					<tr>
						<td><input type="radio" name="exception_mode" value="permissive" <?php if (strcmp($config->{"exception"}->{"mode"},"permissive")==0 || !$config->{"exception"}->{"mode"}){echo "checked";} ?> > <?php echo JText::_( 'PROXY_CONFIG_EXCEPTION_MANAGEMENT_MODE_PERMISSIVE'); ?><br></td>
					</tr>
					<tr>
						<td><input type="radio" name="exception_mode" value="restrictive" <?php if (strcmp($config->{"exception"}->{"mode"},"restrictive")==0){echo "checked";} ?> > <?php echo JText::_( 'PROXY_CONFIG_EXCEPTION_MANAGEMENT_MODE_RESTRICTIVE'); ?><br></td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY FILE LOCATION'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input name="policyFile" type="text" size=100
						value="<?php echo $config->{"authorization"}->{"policy-file"}; ?>"></td>
				</tr>
			</table>
			</fieldset>
	
			<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_XSLT_PATH'); ?></legend>
			<table class="admintable">
				<tr>
					<td><input name="xsltPath" type="text" size=100
						value="<?php echo $config->{"xslt-path"}->{"url"}; ?>"></td>
				</tr>
			</table>
			</fieldset>
			<fieldset class="adminform"><legend><?php echo JText::_( 'PROXY_CONFIG_LOG'); ?></legend>
			<table class="admintable">
				
				<tr>
					<td colspan = "2">
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_FILE_NAME'); ?></td>
								<td><b>[<?php echo JText::_( 'EASYSDI_PREFIX'); ?>].[YYYYMMDD].[<?php echo JText::_( 'EASYSDI_SUFFIX'); ?>].[<?php echo JText::_( 'EASYSDI_EXTENSION'); ?>]</b></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan = "2">
					<table class="admintable">
						<tr>
							<th><?php echo JText::_( 'EASYSDI_PATH'); ?></th>
							<th><?php echo JText::_( 'EASYSDI_PREFIX'); ?></th>
							<th><?php echo JText::_( 'EASYSDI_SUFFIX'); ?></th>
							<th><?php echo JText::_( 'EASYSDI_EXTENSION'); ?></th>
						</tr>
						<tr>
							<td><input name="logPath"  id="logPath" size=70 type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"path"};?>"></td>
							<td><input name="logPrefix" id="logPrefix" type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"prefix"};?>"></td>
							<td><input name="logSuffix" id="logSuffix" type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"suffix"};?>"></td>
							<td><input name="logExt" type="text"
								value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"extension"};?>"></td>
						</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td colspan = "2">
						<table class="admintable">
							<tr>
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE'); ?></td>
								<td><select name="logger">
									<option <?php if (strcmp($config->{"log-config"}->{"logger"},"org.apache.log4j.Logger")==0){echo "selected";} ?>
										value="org.apache.log4j.Logger"><?php echo JText::_( 'PROXY_CONFIG_LOG_MODE_LOG4J'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"logger"},"org.easysdi.proxy.log.ProxyLogger")==0){echo "selected";} ?>
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
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_LEVEL'); ?></td>
								<td><select name="logLevel">
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"OFF")==0){echo "selected";} ?>
										value="OFF">OFF</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"FATAL")==0){echo "selected";} ?>
										value="FATAL">FATAL</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"ERROR")==0){echo "selected";} ?>
										value="ERROR">ERROR</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"WARN")==0){echo "selected";} ?>
										value="WARN">WARN</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"INFO")==0){echo "selected";} ?>
										value="INFO">INFO</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"DEBUG")==0){echo "selected";} ?>
										value="DEBUG">DEBUG</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"TRACE")==0){echo "selected";} ?>
										value="TRACE">TRACE</option>
									<option <?php if (strcmp($config->{"log-config"}->{"log-level"},"ALL")==0){echo "selected";} ?>
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
								<td class="key"><?php echo JText::_( 'PROXY_CONFIG_LOG_PERIOD'); ?></td>
								<td><select name="logPeriod">
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"daily")==0){echo "selected";} ?>
										value="daily"><?php echo JText::_( 'EASYSDI_DAILY'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"monthly")==0){echo "selected";} ?>
										value="monthly"><?php echo JText::_( 'EASYSDI_MONTHLY'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"weekly")==0){echo "selected";} ?>
										value="weekly"><?php echo JText::_( 'EASYSDI_WEEKLY'); ?></option>
									<option <?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"annualy")==0){echo "selected";} ?>
										value="annually"><?php echo JText::_( 'EASYSDI_ANNUALLY'); ?></option>
								</select></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			</fieldset>
			<?php 
	}
	
	function showPoliciesList($xml){
		global $mainframe;
		$option = JRequest::getVar('option');
		$configId = JRequest::getVar("configId");
		$policyId = JRequest::getVar("policyId");
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');

		?>


<form name='adminForm'  action='index.php' method='POST'>
<table>
	<tr>
		
		<td align="right" width="100%">
			<?php echo JText::_("EASYSDI_FILTER");?>&nbsp;
			<input type="text" name="search" id="search" value="<?php echo $search;?>" class="inputbox" onchange="document.adminForm.submit();"  />			
		</td>
		<td nowrap="nowrap"></td>
	</tr>
</table>

<table class="adminlist">
	<thead>
		<tr class="title">
			<th width="2%"><?php echo JText::_( 'EASYSDI_NUM'); ?></th>
			<th width="20" class='title'></th>
			<th width="30%"><?php echo JText::_( 'EASYSDI_POLICY ID'); ?></th>
			<th><?php echo JText::_( 'EASYSDI_USERS AND ROLES'); ?></th>
			<th><?php echo JText::_( 'EASYSDI_ORDER'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$isChecked = false;
	$i=0;
	foreach ($xml->config as $config) {

		if (strcmp($config['id'],$configId)==0){
			$policyFile = $config->{'authorization'}->{'policy-file'};
			
			$i=0;
			$count=0;
			if (!file_exists($policyFile)){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
			}
			
			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($policyFile);
				 
			

				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['ConfigId'],$configId)==0){
			
		$i++;	
		}
	}}}}
	
	$pageNav = new JPagination($i,$limitstart,$limit);
	foreach ($xml->config as $config) {

		if (strcmp($config['id'],$configId)==0){

			$servletClass=$config->{'servlet-class'};
			$policyFile = $config->{'authorization'}->{'policy-file'};
			$i=0;
			$count=0;
		if (!file_exists($policyFile)){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
			}
			
			if (file_exists($policyFile)) {
				$xmlConfigFile = simplexml_load_file($policyFile);
			
				foreach ($xmlConfigFile->Policy as $policy){
					if (strcmp($policy['ConfigId'],$configId)==0){

						if ( (!(stripos($policy['Id'],$search)===False)) || strlen($search)==0){
							if (($count>=$limitstart || $limit==0)&& ($count < $limitstart+$limit || $limit==0)){
								?>
		<tr class="row<?php echo $i%2;?>">
			<td><?php echo $i + 1; ?></td>
			<td><input
			<?php 
				if (strlen($policyId)==0)
				{ 
					if (!$isChecked) 
					{
						echo 'checked'; 
						$isChecked =true;
					}
				}
				else
				{ 
					if(strcmp($policyId,$policy['Id'])==0)
					{
						echo 'checked';
					} 
				}?>
				type='radio' id="cb<?php echo$i ?>" name="policyId" value="<?php echo $policy['Id']; ?>" onclick="isChecked(this.checked);"> 
				</td>
				<td>
								
				<a href="#edit" onclick="document.getElementById('configId').value='<?php echo $configId;?>';document.getElementById('task').value='editPolicy';document.getElementById('cb<?php echo $i;?>').checked=true;document.adminForm.submit();;">
				<?php echo $policy['Id']; ?>	</a>
				
				</td>
			<td><?php  
			if (strcasecmp($policy->{'Subjects'}['All'],"true")==0){
				echo JText::_( 'EASYSDI_ALL USERS AND ROLES');
			}else{
					
				if (count($policy->Subjects->Role)>0){
					foreach ($policy->Subjects->Role as $role){
						echo  JText::_( $role).",";
					}
				}
					
				if (count($policy->Subjects->User)>0){
					foreach ($policy->Subjects->User as $user){
						echo  JText::_( $user).",";
					}

				}
			}

			?></td>
			<td>
			
            <?php echo $pageNav->orderUpIcon($i,  true, 'orderuppolicy', 'Move Up'); ?>
            <?php echo $pageNav->orderDownIcon($i,1,  true, 'orderdownpolicy', 'Move Down' ); ?>                       
        </td>
		</tr>

		<?php
		$i++;
							}
							$count++;
						}}
				}
			}
		}
	}
	?>
	</tbody>
	<tfoot>
	<?php
	
	//$pageNav = new JPagination($count,$limitstart,$limit);
	?>
		<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
	</tfoot>
</table>
	<input type='hidden' name='option' id='option' value='<?php echo $option;?>'>
	<input type='hidden' name='task' id='task' value='editPolicyList'>
	<input type='hidden' name='configId'id='configId' value='<?php echo $configId;?>'>
	<input type="hidden" name="boxchecked" value="<?php echo ($_POST['policyId'] or $isChecked)?1:0;?>" />
	<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >
</form>

	<?php

	}

	function editPolicy($xml,$new=false, $rowsProfile, $rowsUser, $rowsVisibility, $rowsStatus, $rowsObjectTypes){

		$policyId = JRequest::getVar("policyId");
		$configId = JRequest::getVar("configId");
		$option = JRequest::getVar("option");
		
		foreach ($xml->config as $config) {
			if (strcmp($config['id'],$configId)==0){
				$policyFile = $config->{'authorization'}->{'policy-file'};
				$servletClass =  $config->{'servlet-class'};
				$servletVersion =  "";
				foreach($config->{"supported-versions"}->{"version"} as $versionConfig){
					if(strcmp($servletVersion, $versionConfig)< 0){
						$servletVersion = $versionConfig;
					}
				}
				
				if (!file_exists($policyFile)){
						global $mainframe;		
						$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
				}
			
				if (file_exists($policyFile)) {
					$xmlConfigFile = simplexml_load_file($policyFile);
				
					if($new){
						
					$thePolicy  = $xmlConfigFile->addChild('Policy');
					$thePolicy ['Id']="new Policy";
					$policyId=$thePolicy ['Id'];
					$thePolicy ['ConfigId']=$configId;
					$thePolicy ->Servers['All']="false";
					$thePolicy ->Subjects['All']="false";
					$thePolicy ->Operations['All']="true";
					$thePolicy ->AvailabilityPeriod->Mask="dd-mm-yyyy";
					$thePolicy ->AvailabilityPeriod->From->Date="28-01-2008";
					$thePolicy ->AvailabilityPeriod->To->Date="28-01-2108";				
					}else{
						foreach ($xmlConfigFile->Policy as $policy){
	
							if (strcmp($policy['Id'],$policyId)==0  && strcmp($policy['ConfigId'],$configId)==0){								
								$thePolicy = $policy;
								break;
	
							}
						}
					}
				}				
				?>

<form name='adminForm' action='index.php' method='POST'><input
	type='hidden' name='option' value='<?php echo $option;?>'> <input
	type='hidden' name='task' value=''> <input type='hidden'
	name='servletClass' value='<?php echo $servletClass; ?>'> <input
	type='hidden' name='configId' value='<?php echo $configId; ?>'> <input
	type='hidden' name='policyId' value='<?php echo $policyId; ?>'> <input
	type='hidden' name='isNewPolicy' value='<?php echo $new; ?>'>
	<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >



<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY IDENTIFICATION'); ?></legend>
<table class="admintable">
	<tr>
		<td class="key"><?php echo JText::_( 'EASYSDI_CONFIGURATION ID'); ?></td>
		<td><input type="text" size="100" value="<?php echo $configId ?>" disabled="disabled"></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_( 'EASYSDI_POLICY ID'); ?></td>
		<td><input type="text" size="100" name="newPolicyId" value="<?php echo $policyId ?>"></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_( 'EASYSDI_SERVLET'); ?></td>
		<td><input type="text" size="100" name="servlet-class" id="servlet-class" value="<?php echo $servletClass;?>" disabled="disabled" size=50></td>
	</tr>

</table>
</fieldset>

<fieldset class="adminform"><legend>Users and Groups</legend>
<table class="admintable">
	<tr>
		<td><input
		<?php if (strcasecmp($thePolicy->Subjects['All'],'True')==0){echo 'checked';} ?>
			type="checkBox" name="AllUsers[]" id="AllUsers" 
			onclick="disableList('AllUsers','userNameList');disableList('AllUsers','roleNameList');">
		<?php echo JText::_( 'EASYSDI_ANONYMOUS'); ?></td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_USERS'); ?></b></th>
		<th></th>		
		<th><b><?php echo JText::_( 'EASYSDI_ROLES'); ?></b></th>
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
			
			$profileSelected = array();
			foreach ($thePolicy->Subjects->Role as $role)
			{
				$or->value = $role;
				$profileSelected[] = $or;
				$or = null;
			}
			$disabled ="";
			if (strcasecmp($thePolicy->Subjects['All'],'True')==0)
			{
				$disabled = "disabled ";
			}
			HTML_proxy::alter_array_value_with_Jtext($rowsProfile);
?>
	<td><?php echo JHTML::_("select.genericlist",$rowsUser, 'userNameList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $userSelected ); ?></td>
	<td></td>
	<td><?php echo JHTML::_("select.genericlist", $rowsProfile, 'roleNameList[]', 'size="15" multiple="true" class="selectbox" '.$disabled, 'value', 'text', $profileSelected ); ?></td>
	</tr>

</table>
</fieldset>
<?php JHTML::_( 'behavior.modal' ); ?>
<?php JHTML::_('behavior.calendar'); ?>
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_AVAILIBILITY'); ?></legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_DATE TIME FORMAT'); ?> </b>: <?php echo $thePolicy->{'AvailabilityPeriod'}->Mask; ?>
		</th>
		<td></td>
	</tr>
	<tr>
			
		<td><b><?php echo JText::_( 'EASYSDI_FROM'); ?></b> 	
			<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->From->Date, "dateFrom","dateFrom","%d-%m-%Y"); ?>		
			</td>
		<td><b><?php echo JText::_( 'EASYSDI_TO'); ?></b>		
			<?php echo JHTML::_('calendar',$thePolicy->{'AvailabilityPeriod'}->To->Date, "dateTo","dateTo","%d-%m-%Y"); ?>
			</td>
			
	</tr>
	<input name="dateFormat" type="hidden" value"dd-mm-yyyy">
</table>
</fieldset>

			<?php 
			if (strcmp($servletClass,"org.easysdi.proxy.wfs.WFSProxyServlet")==0){
				HTML_proxyWFS::generateWFSHTML($config,$thePolicy,$servletVersion);
			}
			else if (strcmp($servletClass,"org.easysdi.proxy.wms.WMSProxyServlet")==0 ){
				HTML_proxyWMS::generateWMSHTML($config,$thePolicy,$servletVersion);  }
				
			else if (strcmp($servletClass,"org.easysdi.proxy.csw.CSWProxyServlet")==0 ){					
				HTML_proxyCSW::generateCSWHTML($config,$thePolicy, $rowsVisibility, $rowsStatus, $rowsObjectTypes,$servletVersion);  
			}
			else if (strcmp($servletClass,"org.easysdi.proxy.wmts.WMTSProxyServlet")==0 ){					
				HTML_proxyWMTS::generateWMTSHTML($config,$thePolicy,$servletVersion);  
			}
			break;
			}		
				
		} ?>

</form>
		<?php
	}

	function showConfigList($xml){
		global $mainframe;
		JToolBarHelper::title( JText::_(  'EASYSDI_SHOW CONFIGURATIONS LIST' ), 'generic.png' );
		jimport("joomla.html.pagination");
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');

		?>

<form name='adminForm' action='index.php' method='POST'>
<input type='hidden' name='option' value='<?php echo JRequest::getVar('option') ;?>'> 
<input type='hidden' name='task' id='task' value='<?php echo JRequest::getVar('task') ;?>'> 
<input type='hidden' name='boxchecked' value='<?php echo ($_POST['configId'])?1:0;?>'>
<input type='hidden' name='serviceType' id='serviceType' value="<?php echo JRequest::getVar('serviceType');?>" >

<table>
	<tr>

		<td align="right" width="100%">
			<?php echo JText::_("EASYSDI_FILTER");?>&nbsp;
			<input type="text" name="search" id="search" value="<?php echo $search;?>" class="inputbox" onchange="document.adminForm.submit();"  />			
		</td>
		<td nowrap="nowrap"></td>
	</tr>
</table>


<table class="adminlist">
	<thead>
		<tr>
			<th width="2%" class='title'><?php echo JText::_( 'EASYSDI_NUM' ); ?></th>
			<th width="2%" class='title'></th>
			<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION ID'); ?></b></th>
			<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION_TYPE'); ?></b></th>
			<th class='title'><b><?php echo JText::_( 'EASYSDI_CONFIGURATION_URL'); ?></b></th>

		</tr>
	</thead>
	<tbody>
	<?php
	$id = JRequest::getVar("configId","");

	$i=0;
	foreach ($xml->config as $config) {
		if (!(stripos($config['id'],$search)===False) || !(stripos($config->{'servlet-class'},$search)===False) || strlen($search)==0){
			if (($i>=$limitstart || $limit==0)&& ($i < $limitstart+$limit || $limit==0)){
				$policyFile = $config->{'authorization'}->{'policy-file'};
				?>
		<tr class="row<?php echo $i%2; ?>">
			<td><?php echo $i+1;?></td>
			<td><input
			<?php if (strlen($id)>0)
				  {
				  	if (strcmp($id,$config['id'])==0)
				  	{
				  		echo 'checked';
				  	}
				  } 
				 ?>
				type="radio" id="cb<?php echo$i ?>" name="configId" value="<?php echo $config['id'] ?>" onclick="document.getElementById('serviceType').value='<?php 
					if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
					{
						echo "WMS";
					}
					else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.WMTSProxyServlet")
					{
						echo "WMTS";
					}
					else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
					{
						echo "CSW";
					}

					else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
					{
						echo "WFS";
					} ?>'; isChecked(this.checked);"></td>
			<td>
			<a href="#edit" onclick="document.getElementById('task').value='editConfig';document.getElementById('cb<?php echo $i;?>').checked=true;document.adminForm.submit();;">
				<?php echo $config['id']?></a>
			 </td>
			<td><?php 
			if($config->{'servlet-class'} == "org.easysdi.proxy.wms.WMSProxyServlet")
			{
				//echo "<b>".WMS."  </b>";
				echo "<b>WMS</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.wmts.WMTSProxyServlet")
			{
				echo "<b>WMTS</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.csw.CSWProxyServlet")
			{
				//echo "<b>".CSW."  </b>";  
				echo "<b>CSW</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.wfs.WFSProxyServlet")
			{
				//echo "<b>".WFS."  </b>";
				echo "<b>WFS</b>";
			}
			else if($config->{'servlet-class'} == "org.easysdi.proxy.cgp.GGPProxyServlet")
			{
				//echo "<b>".CGP."  </b>";
				echo "<b>CGP</b>";
			}
			echo  "{".$config->{'servlet-class'}."}";  ?>
			</td>
			<td><?php	echo $config->{'host-translator'};?></td>
		</tr>
		

		<?php
			}
			$i++;

		}

	}?>
	</tbody>
	<tfoot>
	<?php

	$pageNav = new JPagination(count($xml->config),$limitstart,$limit);
	?>
		<td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
	</tfoot>
</table>
</form>
	<?php
	
	}
	
	function helpAttributeFilter ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_ALL' ); ?>
		</h3>
		<p>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_ALL_CONTENT' ); ?>
		</p>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_SELECT' ); ?>
		</h3>
		<p>
		<?php echo JText::_(  'EASYSDI_HELP_ATTRIBUTE_FILTER_SELECT_CONTENT' ); ?>
		</p>
		<?php 
	}
	
	function helpImageSize ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_IMAGE_SIZE_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_IMAGE_SIZE' ); ?>
		</h3>
		<p>
		<?php echo JText::_(  'EASYSDI_HELP_IMAGE_SIZE_CONTENT' ); ?>
		</p>
		
		<?php 
	}
	
	function helpQueryTemplate ($filter_type)
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_TEMPLATE_TITLE' ); ?>
		</h2>
		<h3>
		<?php if($filter_type == "filterQuery")
		{
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY' ); 
		}
		else
		{
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_ANSWER' ); 
		}
		?>
		</h3>
		
		<?php if($filter_type == "filterQuery")
		{
		?>
		<textarea ROWS="8" COLS="75"><Filter xmlns:gml='http://www.opengis.net/gml'>
  <BBOX>
    <PropertyName>geometryName</PropertyName>
      <Box srsName=\"EPSG:4326\" gml=\"http://www.opengis.net/gml\">
        <coordinates>-180,-90 180,90</coordinates>
      </Box>
  </BBOX>
</Filter></textarea>
		<?php
		}
		else
		{ 
		?>
		<textarea ROWS="13" COLS="75"><Filter xmlns:gml='http://www.opengis.net/gml'>
  <Within>
    <PropertyName>geometryName</PropertyName>
      <gml:Polygon xmlns:gml='http://www.opengis.net/gml' srsName='EPSG:4326'>
      <gml:outerBoundaryIs>
        <gml:LinearRing>
          <gml:coordinates>-180,-90 -180,90 180,90 180,-90 -180,-90
          </gml:coordinates>
        </gml:LinearRing>
      </gml:outerBoundaryIs>
    </gml:Polygon>
  </Within>
</Filter></textarea>
		<?php
		} 
		if($filter_type == "filterQuery")
		{
			?>
			<p>
			<?php 
			echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY_REM' );
			?>
			</p>
			<?php
		}
		
	}
	
	function helpQueryWMSTemplate ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_TEMPLATE_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_WMS_QUERY' ); ?>
		</h3>
		
		
		<textarea ROWS="8" COLS="75"><gml:Polygon xmlns:gml="http://www.opengis.net/gml" srsName="EPSG:21781">
  <gml:outerBoundaryIs>
    <gml:LinearRing>                    
      <gml:coordinates>470000,50000 470000,210000 600000,210000 600000,50000 470000,50000
      </gml:coordinates>
    </gml:LinearRing>
  </gml:outerBoundaryIs>
</gml:Polygon></textarea>
		
			<p>
			<?php 
			//echo JText::_(  'EASYSDI_HELP_TEMPLATE_FILTER_TYPE_QUERY_REM' );
			?>
			</p>
			<?php
		
	}
	
	function helpGeoGraphicalFilter ()
	{
		?>
		<h2>
		<?php echo JText::_(  'EASYSDI_HELP_TEMPLATE_TITLE' ); ?>
		</h2>
		<h3>
		<?php echo JText::_(  'EASYSDI_HELP_GEOGRAPHICAL_FILTER' ); ?>
		</h3>
		
		
		<textarea ROWS="8" COLS="75"><ogc:BBOX xmlns:ogc="http://www.opengis.net/ogc" xmlns:gml="http://www.opengis.net/gml">
        <ogc:PropertyName>BoundingBox</ogc:PropertyName>
          <gml:Envelope>
            <gml:lowerCorner>-17 -34</gml:lowerCorner>
            <gml:upperCorner>-16.5 -33.5</gml:upperCorner>
          </gml:Envelope>
</ogc:BBOX></textarea>
		
			<p>
			<?php 
			echo JText::_(  'EASYSDI_HELP_GEOGRAPHICAL_FILTER_REM' );
			?>
			</p>
			<?php
		
	}


}
?>