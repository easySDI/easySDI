<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d'Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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
	
	
	
		
		
		
		
	function getFeatureTypeRemoteFilter($theServer,$featureType){

		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){

			if (! (strrpos($featureType->{'Name'}, ":") ===False)){			 
			if (strcmp($ft->{'Name'},substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1))==0){

				return $ft->{'RemoteFilter'};
			}
			}else {
				if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){
				return $ft->{'RemoteFilter'};
			}
				
				
			}
		}

		return "";
	}

	function getFeatureTypeLocalFilter($theServer,$featureType){

		if (count($theServer->FeatureTypes->FeatureType )==0) return "";
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){

			if (!(strrpos($featureType->{'Name'}, ":") ===False)){			 
			if (strcmp($ft->{'Name'},substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1))==0){

				return $ft->{'LocalFilter'};
			}}else{
			if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){

				return $ft->{'LocalFilter'};
			}	
			}
		}

		return "";
	}




	function getLayerLocalFilter($theServer,$theLayer){

		if (count($theServer->Layers->Layer)==0) return "";


		foreach ($theServer->Layers->Layer as $layer ){

			if (strcmp($layer->{'Name'},$theLayer->{'Name'})==0){
				return $layer->{'Filter'};
			}
		}

		return "";
	}



	function getLayerMinScale($theServer,$layer){


		if (count($theServer->Layers->Layer)==0) return "";


		foreach ($theServer->Layers->Layer as $layer ){


			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0){
				return $theLayer->ScaleMin;
			}
		}

		return "";


	}

	function getLayerMaxScale($theServer,$layer){

		if (count($theServer->Layers->Layer)==0) return "";
		foreach ($theServer->Layers->Layer as $theLayer ){

			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0){
				return $theLayer->ScaleMax;
			}
		}

		return "";

	}


	function isLayerChecked($theServer,$layer){

		if (strcasecmp($theServer->{"Layers"}['All'],"true")==0) return true;

		if (count($theServer->Layers->Layer)==0) return "";

		foreach ($theServer->Layers->Layer as $theLayer ){

			if (strcmp($theLayer->{'Name'},$layer->{'Name'})==0){
				return true;
			}
		}

		return false;


	}


	function isChecked($theServer,$featureType){

		if (strcasecmp($theServer->{"FeatureTypes"}['All'],"true")==0) return true;
		
		if (count($theServer->FeatureTypes->FeatureType )==0) return false;
		foreach ($theServer->FeatureTypes->FeatureType as $ft ){

			
			if (!(strrpos($featureType->{'Name'}, ":") ===False)){
				if (strcmp($ft->{'Name'},substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1))==0){

					return true;
				}
			}else{				
				if (strcmp($ft->{'Name'},$featureType->{'Name'})==0){
					
					return true;
				}

			}


		}

		return false;


	}

	function editconfig($xml,$new = false){
			
		$option = JRequest::getVar('option');
		if (!$new){
			$configId = JRequest::getVar("configId");
		}else{
			$found = false;
			$configId = "New Config";
			$i=0;
			foreach ($xml->config as $config) {
				if (strcmp($config['id'],$configId)==0){
					$found = true;
					break;
				}
			}

			while($found){
				foreach ($xml->config as $config) {
					$found=false;
					if (strcmp($config['id'],$configId.$i)==0){
						$found = true;
						break;
					}
				}
				if ($found == false){
					$configId = $configId.$i;
				}
				$i++;
			}

			$config = $xml->addChild("config");
			$config->addAttribute("id",$configId);
			
			$config->addChild("authorization")->addChild("policy-file");
			$config->{'remote-server-list'}="";	
			$remoteServer = $config->{'remote-server-list'}->addChild("remote-server");
			$remoteServer->user="";
			$remoteServer->url="";
			$remoteServer->password="";
		}
		JToolBarHelper::title( JText::_( 'EASYSDI_EDIT CONFIG :' ).$configId, 'edit.png' );
		?>
<script>
function changeValues(){
if(document.getElementById('servletClass').value == 'ch.depth.proxy.csw.CSWProxyServlet'){
document.getElementById('specificGeonetowrk').style.display="block";
}else{
document.getElementById('specificGeonetowrk').style.display="none";
}


}


function submitbutton(pressbutton){

if (pressbutton=="addNewServer"){	
	addNewServer();
	}
	else{	
	submitform(pressbutton);
	}
}
</script>

<form name='adminForm' id='adminForm' action='index.php' method='POST'>
	<input type='hidden' name="isNewConfig" value="<?php echo $new; ?>">
	<input
	type='hidden' name='option' value='<?php echo $option;?>'> <input
	type='hidden' name='task' value=''> <input type='hidden'
	name='configId' value='<?php echo $configId;?>'> <?php
	foreach ($xml->config as $config) {
		if (strcmp($config[id],$configId)==0){
			$servletClass=$config->{'servlet-class'};
			?>

<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_CONFIG ID' );?></legend>
<table class="admintable">
	<tr>
		<td colspan="4"><input type='text' name='newConfigId'
			value='<?php echo $configId;?>'></td>
	</tr>
</table>
</fieldset>
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_SERVLET TYPE' );?></legend>

<table class="admintable">

	<tr>
		<td colspan="4"><select name="servletClass" id="servletClass" onChange="changeValues()">
			<option
			<?php if (strcmp($servletClass,"ch.depth.proxy.wfs.SimpleWFSProxyServlet")==0 ){echo "selected";}?>
				value="ch.depth.proxy.wfs.SimpleWFSProxyServlet">
			ch.depth.proxy.wfs.SimpleWFSProxyServlet</option>
			<option
			<?php if (strcmp($servletClass,"ch.depth.proxy.wfs.WFSProxyServlet")==0 ){echo "selected";}?>
				value="ch.depth.proxy.wfs.WFSProxyServlet">
			ch.depth.proxy.wfs.WFSProxyServlet</option>
			<option
			<?php if (strcmp($servletClass,"ch.depth.proxy.wms.WMSProxyServlet")==0 ){echo "selected";}?>
				value="ch.depth.proxy.wms.WMSProxyServlet">
			ch.depth.proxy.wms.WMSProxyServlet</option>
			<option
			<?php if (strcmp($servletClass,"ch.depth.proxy.cgp.CGPProxyServlet")==0 ){echo "selected";}?>
				value="ch.depth.proxy.cgp.CGPProxyServlet">
			ch.depth.proxy.cgp.CGPProxyServlet</option>
			<option
			<?php if (strcmp($servletClass,"ch.depth.proxy.csw.CSWProxyServlet")==0 ){echo "selected";}?>
				value="ch.depth.proxy.csw.CSWProxyServlet">
			ch.depth.proxy.csw.CSWProxyServlet</option>
		</select></td>
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
		<th><?php echo JText::_( 'EASYSDI_URL'); ?></th>
		<th><?php echo JText::_( 'EASYSDI_USER'); ?></th>
		<th><?php echo JText::_( 'EASYSDI_PASSWORD'); ?></th>
	</tr>
	</thead>
	<tbody id="remoteServerTable" ><?php
	$remoteServerList = $config->{'remote-server-list'};
	$iServer=0;
	foreach ($remoteServerList->{'remote-server'} as $remoteServer){
		?><tr>
				<td class="key"><input type="text" name="URL_<?php echo $iServer;?>" value="<?php echo $remoteServer->url; ?>" size=70></td>
				<td><input name="USER_<?php echo $iServer;?>" type="text" value="<?php echo $remoteServer->user; ?>"></td>
				<td><input name="PASSWORD_<?php echo $iServer;?>" type="password" value="<?php echo $remoteServer->password; ?>">				
				<input type="button" onClick="javascript:removeServer(<?php echo $iServer;?>);" value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>"></td>
								
			</tr>
			<tr>						
			<td colspan="3">
			<div id="specificGeonetowrk" style="display:<?php if (strcmp($servletClass,"ch.depth.proxy.csw.CSWProxyServlet")==0 ){echo "block";}else{echo"none";} ?>">
			<table>	
			<tr>									
			<td><?php echo JText::_( 'EASYSDI_MAX_RECORDS');?></td><td><input type="text" name="max-records_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'max-records'}; ?>" size=5></td>
			</tr>
			<tr>
			<td><?php echo JText::_( 'EASYSDI_LOGIN_SERVICE');?></td><td><input type="text" name="login-service_<?php echo $iServer;?>" value="<?php echo $remoteServer->{'login-service'}; ?>" size=70></td>
			</tr>								
			<tr>
			<td><?php echo JText::_( 'EASYSDI_SEARCH_SERVICE_URL');?></td><td><input type="text" name="search-service-url_<?php echo $iServer;?>" value="<?php echo $remoteServer->transaction->{'search-service-url'}; ?>" size=70></td>
			</tr>
			<tr>
			<td><?php echo JText::_( 'EASYSDI_DELETE_SERVICE_URL');?></td><td><input type="text" name="delete-service-url_<?php echo $iServer;?>" value="<?php echo $remoteServer->transaction->{'delete-service-url'}; ?>" size=70></td>
			</tr>
			<tr>
			<td><?php echo JText::_( 'EASYSDI_INSERT_SERVICE_URL');?></td><td><input type="text" name="insert-service-url_<?php echo $iServer;?>" value="<?php echo $remoteServer->transaction->{'insert-service-url'}; ?>" size=70></td>					
			</tr>	
			</table>
			</div>
			</td>
			</tr>
		<?php
	$iServer=$iServer+1;
	}
	?></tbody>
</table>
</fieldset>
<script>
var nbServer = <?php echo $iServer?>;

function removeServer(servNo){

noeud = document.getElementById("remoteServerTable");
var fils = noeud.childNodes;

noeud.removeChild(fils[servNo]);

noeud = document.getElementById("remoteServerTable");
fils = noeud.childNodes;
var nbFils = fils.length;

for(var i = 0; i < nbFils; i++){
		fils[i].childNodes[0].childNodes[0].name="URL_"+i;	
		fils[i].childNodes[1].childNodes[0].name="USER_"+i;
		fils[i].childNodes[2].childNodes[0].name="PASSWORD_"+i;						
		fils[i].childNodes[2].childNodes[1].setAttribute("onClick","javascript:removeServer("+i+");");
} 
nbServer = nbServer - 1;
}

function addNewServer(){
	
	var tr = document.createElement('tr');	
	var tdUrl = document.createElement('td');
	tdUrl.className="key";
	
	var tdUser = document.createElement('td');
	var tdPwd = document.createElement('td');				
	var inputUrl = document.createElement('input');
	inputUrl.size=70;
	inputUrl.type="text";
	inputUrl.name="URL_"+nbServer;
	
	var inputUser = document.createElement('input');
	inputUser.type="text";
	inputUser.name="USER_"+nbServer;
	
	var inputPassword = document.createElement('input');
	inputPassword.type="password";
	inputPassword.name="PASSWORD_"+nbServer;
	
	tdUrl.appendChild(inputUrl);
	tr.appendChild(tdUrl);
	tdUser.appendChild(inputUser);
	tr.appendChild(tdUser);
	
	tdPwd.appendChild(inputPassword);
	
	var aButton = document.createElement('input');
	aButton.type="button";
	aButton.value="<?php echo JText::_( 'EASYSDI_REMOVE' ); ?>";
	aButton.setAttribute("onClick","javascript:removeServer("+nbServer+");");
		
	tdPwd.appendChild(aButton);
	
	
	tr.appendChild(tdPwd);
	
	document.getElementById("remoteServerTable").appendChild(tr);
	
	
	nbServer = nbServer + 1;
}
</script>
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY FILE LOCATION'); ?></legend>
<table class="admintable">
	<tr>
		<td><input name="policyFile" type="text" size=100
			value="<?php echo $config->{"authorization"}->{"policy-file"}; ?>"></td>
	</tr>
</table>
</fieldset>

<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_LOG CONFIG'); ?></legend>
<table class="admintable">
	<tr>
		<td><select name="logPeriod">
			<option
			<?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"daily")==0){echo "selected";} ?>
				value="daily"><?php echo JText::_( 'EASYSDI_DAILY'); ?></option>
			<option
			<?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"monthly")==0){echo "selected";} ?>
				value="monthly"><?php echo JText::_( 'EASYSDI_MONTHLY'); ?></option>
			<option
			<?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"weekly")==0){echo "selected";} ?>
				value="weekly"><?php echo JText::_( 'EASYSDI_WEEKLY'); ?></option>
			<option
			<?php if (strcmp($config->{"log-config"}->{"file-structure"}->{"period"},"annualy")==0){echo "selected";} ?>
				value="annually"><?php echo JText::_( 'EASYSDI_ANNUALLY'); ?></option>
		</select></td>
	</tr>

	<tr>
		<td>
		<table class="admintable">
			<tr>
				<th><?php echo JText::_( 'EASYSDI_PATH'); ?></th>
				<th><?php echo JText::_( 'EASYSDI_SUFFIX'); ?></th>
				<th><?php echo JText::_( 'EASYSDI_PREFIX'); ?></th>
				<th><?php echo JText::_( 'EASYSDI_EXTENSION'); ?></th>
			</tr>
			<tr>
				<td><input name="logPath" size=70 type="text"
					value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"path"};?>"></td>
				<td><input name="logSuffix" type="text"
					value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"suffix"};?>"></td>
				<td><input name="logPrefix" type="text"
					value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"prefix"};?>"></td>
				<td><input name="logExt" type="text"
					value="<?php  echo $config->{"log-config"}->{"file-structure"}->{"extension"};?>"></td>
			</tr>
		</table>
		</td>

	</tr>
</table>
</fieldset>

			<?php
			break;
		}
	}
	?></form>
	<?php

	}

	function showPoliciesList($xml){
		global $mainframe;
		$option = JRequest::getVar('option');
		$configId = JRequest::getVar("configId");
		$policyId = JRequest::getVar("policyId");
		$task = JRequest::getVar("task");
		
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');


		JToolBarHelper::title( JText::_( 'EASYSDI_POLICIES :' ).$configId, 'edit.png' );
		?>


<form name='adminForm' action='index.php' method='GET'><input
	type='hidden' name='option' value='<?php echo $option;?>'><input
	type='hidden' name='task' value='<?php echo $task; ?>'><input
	type='hidden' name='configId' value='<?php echo $configId;?>'><input 
	type="hidden" name="boxchecked" value="0" />
<table>
	<tr>
		<td width="100%"><?php echo JText::_( 'EASYSDI_FILTER' ); ?>: <input
			type="text" name="search" id="search" value="" class="text_area"
			onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'EASYSDI_GO' ); ?></button>
		</td>
		<td nowrap="nowrap"></td>
	</tr>
</table>

<table class="adminlist">
	<thead>
		<tr class="title">
			<th width="2%"><?php echo JText::_( 'EASYSDI_NUM'); ?></th>
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

		if (strcmp($config[id],$configId)==0){
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

		if (strcmp($config[id],$configId)==0){

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
			<?php if (strlen($policyId)==0){ if (!$isChecked) {echo 'checked'; $isChecked =true;}}else{ if(strcmp($policyId,$policy['Id'])==0){echo 'checked';} }?>
				type='radio' id="cb<?php echo$i ?>" name="policyId" value="<?php echo $policy['Id']; ?>"> <?php echo $policy['Id']; ?>					
								 
				
				</td>
			<td><?php  
			if (strcasecmp($policy->{Subjects}['All'],"true")==0){
				echo JText::_( 'EASYSDI_ALL USERS AND ROLES');
			}else{
					
				if (count($policy->Subjects->Role)>0){
					foreach ($policy->Subjects->Role as $role){
						echo $role.",";
					}
				}
					
				if (count($policy->Subjects->User)>0){
					foreach ($policy->Subjects->User as $user){
						echo $user.",";
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
</form>

	<?php

	}

	function editPolicy($xml,$new=false){

		JToolBarHelper::title( JText::_( 'EASYSDI_EDIT POLICY' ), 'edit.png' );

		$policyId = JRequest::getVar("policyId");
		$configId = JRequest::getVar("configId");
		$option = JRequest::getVar("option");
		
		foreach ($xml->config as $config) {
			if (strcmp($config[id],$configId)==0){

				$policyFile = $config->{'authorization'}->{'policy-file'};
				$servletClass =  $config->{'servlet-class'};
				
			if (!file_exists($policyFile)){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO LOAD THE POLICY FILE. PLEASE VERIFY THAT THE FILE EXISTS.' ),'error');
			}
			
				if (file_exists($policyFile)) {
					$xmlConfigFile = simplexml_load_file($policyFile);
				
					if($new){
						
				$thePolicy  = $xmlConfigFile->addChild('Policy');
				$thePolicy [Id]="new Policy";
				$policyId=$thePolicy [Id];
				$thePolicy [ConfigId]=$configId;
				$thePolicy ->Servers[All]="false";
				$thePolicy ->Subjects[All]="false";
				$thePolicy ->Operations[All]="true";
				//$thePolicy ->AvailabilityPeriod->Mask="d MMM yyyy HH:mm:ss";
				$thePolicy ->AvailabilityPeriod->Mask="dd-mm-yyyy";
				$thePolicy ->AvailabilityPeriod->From->Date="28-01-2008";
				$thePolicy ->AvailabilityPeriod->To->Date="28-01-2108";				
				}else{
					foreach ($xmlConfigFile->Policy as $policy){

						if (strcmp($policy['Id'],$policyId)==0){								
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



<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_POLICY IDENTIFICATION'); ?></legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_CONFIGURATION ID'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_POLICY ID'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_SERVLET'); ?></b></th>
	</tr>
	<tr>
		<td><input type="text" value="<?php echo $configId ?>"
			disabled="disabled"></td>
		<td><input type="text" name="newPolicyId"
			value="<?php echo $policyId ?>"></td>
		<td><input type="text" value="<?php echo $servletClass;?>"
			disabled="disabled" size=50></td>
	</tr>
</table>
</fieldset>
<script>
function submitbutton(pressbutton){

for(i=0;i<=document.getElementById('userNameList').length-1;i++) { 
document.getElementById('userNameList').options[i].selected = true; 
} 
for(i=0;i<=document.getElementById('roleNameList').length-1;i++) { 
document.getElementById('roleNameList').options[i].selected = true; 
}
document.getElementById('userNameList').disabled=false;
document.getElementById('roleNameList').disabled=false;
submitform(pressbutton);
}

function addOption(selectList,myText){
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
function disableList(chkBox,list){

if (document.getElementById(chkBox).checked==true){
document.getElementById(list).disabled=true;
}else{
document.getElementById(list).disabled=false;
}
}


function activateFeatureType(featureType){


	if (document.getElementById('featuretype@'+featureType).checked==true){
		document.getElementById('LocalFilter@'+featureType).disabled=false;
		document.getElementById('RemoteFilter@'+featureType).disabled=false;		
		
	}else{
	
		document.getElementById('LocalFilter@'+featureType).disabled=true;
		document.getElementById('RemoteFilter@'+featureType).disabled=true;		
	}
}

function activateLayer(layerName){


	if (document.getElementById('layer@'+layerName).checked==true){
		document.getElementById('scaleMin@'+layerName).disabled=false;
		document.getElementById('scaleMax@'+layerName).disabled=false;
		document.getElementById('LocalFilter@'+layerName).disabled=false;
		
	}else{
	
		document.getElementById('scaleMin@'+layerName).disabled=true;
		document.getElementById('scaleMax@'+layerName).disabled=true;
		document.getElementById('LocalFilter@'+layerName).disabled=true;	
	}
}
</script>

<fieldset class="adminform"><legend>Users and Groups</legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_USERS'); ?></b></th>
		<th></th>
		<th><b><?php echo JText::_( 'EASYSDI_USER OR ROLE NAME'); ?></b></th>
		<th></th>
		<th><b><?php echo JText::_( 'EASYSDI_ROLES'); ?></b></th>
	</tr>
	<tr>
		<td></td>
		<td></td>

		<td><input
		<?php if (strcasecmp($thePolicy->Subjects[All],'True')==0){echo 'checked';} ?>
			type="checkBox" name="AllUsers[]" id="AllUsers"
			onclick="disableList('AllUsers','userNameList');disableList('AllUsers','roleNameList');">
		All</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td><select
		<?php if (strcasecmp($thePolicy->Subjects[All],'True')==0){echo "disabled='true'";} ?>
			name="userNameList[]" id="userNameList" size="10" multiple="multiple">
			<?php
			foreach ($thePolicy->Subjects->User as $user){
				echo "<option value='".$user."'>".$user."</option>";
			}
			?>

		</select></td>
		<td>
		<table>
			<tr>
				<td><input type="button" value="<=="
					onclick="addOption('userNameList','textUserRole');"></td>
			</tr>
			<tr>
				<td><input type="button" value="X"
					onclick="removeOptionSelected('userNameList');"></td>
			</tr>
		</table>
		</td>
		<td><input type="text" id="textUserRole"></td>

		<td>
		<table>
			<tr>
				<td><input type="button" value="==>"
					onclick="addOption('roleNameList','textUserRole');"></td>
			</tr>
			<tr>
				<td><input type="button" value="X"
					onclick="removeOptionSelected('roleNameList');"></td>
			</tr>
		</table>
		</td>
		<td><select
		<?php if (strcasecmp($thePolicy->Subjects[All],'True')==0){echo "disabled='true'";} ?>
			name="roleNameList[]" id="roleNameList" size="10" multiple="multiple">
			<?php
			foreach ($thePolicy->Subjects->Role as $role){
				echo "<option value='".$role."'>".$role."</option>";
			}
			?>
		</select></td>
	</tr>
</table>
</fieldset>

<?php JHTML::_('behavior.calendar'); ?>
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_AVAILIBILITY'); ?></legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_DATE TIME FORMAT'); ?> </b>: <?php echo $thePolicy->{'AvailabilityPeriod'}->Mask; ?>
		</th>
		<input name="dateFormat" type="hidden" value"dd-mm-yyyy">
		<td></td>
	</tr>
	<tr>
			
		<td><b><?php echo JText::_( 'EASYSDI_FROM : '); ?></b> <input size="40"
			id="dateFrom" name="dateFrom" type="text"
			value="<?php echo $thePolicy->{'AvailabilityPeriod'}->From->Date; ?>"  >
			<input type="button" onClick="showCalendar('dateFrom','%d-%m-%Y');">			
			</td>
		<td><b><?php echo JText::_( 'EASYSDI_TO : '); ?></b><input size="40"
			name="dateTo" id="dateTo" type="text"
			value="<?php echo $thePolicy->{'AvailabilityPeriod'}->To->Date; ?>">
			<input type="button" onClick="showCalendar('dateTo','%d-%m-%Y');">
			</td>
			
	</tr>
</table>
</fieldset>

			<?php if (strcmp($servletClass,"ch.depth.proxy.wfs.SimpleWFSProxyServlet")==0 ||strcmp($servletClass,"ch.depth.proxy.wfs.WFSProxyServlet")==0){
				HTML_proxy::generateWFSHTML($config,$thePolicy);
			}
			?> <?php if (strcmp($servletClass,"ch.depth.proxy.wms.WMSProxyServlet")==0 ){
				HTML_proxy::generateWMSHTML($config,$thePolicy);  }
				
				if (strcmp($servletClass,"ch.depth.proxy.csw.CSWProxyServlet")==0 ){					
				HTML_proxy::generateCSWHTML($config,$thePolicy);  
				}
				
				break;
			}		
				
		} ?>

</form>
		<?php
	}

function generateCSWHTML($config,$thePolicy){
	
	?>
	<script>
	function addNewMetadataToExclude(nbParam,nbServer){
	
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
</script>
	
	
	<?php
$remoteServerList = $config->{'remote-server-list'};
		$iServer=0;


		foreach ($remoteServerList->{'remote-server'} as $remoteServer){
	?>			
			<input type="hidden"
	name="remoteServer<?php echo $iServer;?>"
	value="<?php echo $remoteServer->url ?>">
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_CSW SERVER :'); ?> <?php echo $remoteServer->url ?> <input type="button" value="<?php echo JText::_( 'EASYSDI_ADD NEW PARAM');?>" onClick="addNewMetadataToExclude('nbParam<?php echo $iServer; ?>',<?php echo $iServer; ?>);"></legend>
<table  class="admintable">
<thead>
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_ATTRIBUTE TO EXCLUDE'); ?></b></th>		
	</tr>
	</thead>
	<tbody id="metadataParamTable">
	

<?php 
			foreach ($thePolicy->Servers->Server as $policyServer){			
				if (strcmp($policyServer->url,$remoteServer->url)==0){
					$theServer  = $policyServer;
					break;
				}
			}
			$iparam  =0;
			if ($theServer !=null && $theServer->{'Metadata'} !=null && $theServer->{'Metadata'}->{'Attributes'}!=null && $theServer->{'Metadata'}->{'Attributes'}->{'Exclude'} !=null && $theServer->{'Metadata'}->{'Attributes'}->{'Exclude'}->{'Attribute'} !=null){
		foreach ($theServer->{'Metadata'}->{'Attributes'}->{'Exclude'}->{'Attribute'} as $attributeToExclude){			
?>
	<tr>
		<td><input name="param_<?php echo $iServer;?>_<?php echo $iparam;?>" type="text" class="text_area" size="200" value='<?php echo $attributeToExclude; ?>'></td>
	</tr>
	<?php 
			$iparam  ++;
		} 
		}?>
		
		<input type ="hidden" id="nbParam<?php echo $iServer; ?>"  value="<?php echo $iparam;?>">
	
	</tbody>
</table>
</fieldset>	
		<?php	
		$iServer = $iServer +1;
		}
		
}
	function generateWMSHTML($config,$thePolicy){

		$remoteServerList = $config->{'remote-server-list'};
		$iServer=0;


		foreach ($remoteServerList->{'remote-server'} as $remoteServer){

			$urlWithPassword = $remoteServer->url;

			if (strlen($remoteServer->user)!=null && strlen($remoteServer->password)!=null){
				if (strlen($remoteServer->user)>0 && strlen($remoteServer->password)>0){

					if (strpos($remoteServer->url,"http:")===False){
						$urlWithPassword =  "https://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,8);
					}else{
						$urlWithPassword =  "http://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,7);
					}
				}
			}
						
			$pos1 = stripos($urlWithPassword, "?");
			$separator = "&";
			if ($pos1 === false) {
	    		//"?" Not found then use ? instead of &
	    		$separator = "?";  
			}

			$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WMS");

			if ($xmlCapa === false){
					global $mainframe;		
							$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' ),'error');
			}
			else{			
			foreach ($thePolicy->Servers->Server as $policyServer){			
				if (strcmp($policyServer->url,$remoteServer->url)==0){
					$theServer  = $policyServer;
				}
					
			}
			?>

<input type="hidden"
	name="remoteServer<?php echo $iServer;?>"
	value="<?php echo $remoteServer->url ?>">
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_WMS SERVER :'); ?> <?php echo $remoteServer->url ?></legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_LAYER NAME'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_SCALE MIN'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_SCALE MAX'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_LOCAL FILTER'); ?></b></th>
	</tr>

	<?php
	foreach ($xmlCapa->xpath('//Layer') as $layer){
		if ($layer->{'Name'} !=null){
			if (strlen($layer->{'Name'})>0 ){
				?>
	<tr>
		<td><input
			onClick="activateLayer('<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>')"
			<?php if( HTML_proxy ::isLayerChecked($theServer,$layer)) echo 'checked';?>
			type="checkbox"
			id="layer@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			name="layer@<?php echo $iServer; ?>@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			value="<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>">
			<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?></td>
		<td><input
		<?php if(! HTML_proxy ::isLayerChecked($theServer,$layer)) {echo 'disabled';}?>
			type="text" size="10"
			id="scaleMin@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			name="scaleMin@<?php echo $iServer; ?>@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			value="<?php echo HTML_proxy ::getLayerMinScale($theServer,$layer); ?>"></td>
		<td><input
		<?php if(! HTML_proxy ::isLayerChecked($theServer,$layer)) {echo 'disabled';}?>
			type="text" size="10"
			id="scaleMax@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			name="scaleMax@<?php echo $iServer; ?>@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			value="<?php echo HTML_proxy ::getLayerMaxScale($theServer,$layer); ?>"></td>
		<td><textarea
		<?php if(! HTML_proxy ::isLayerChecked($theServer,$layer)) {echo 'disabled';}?>
			rows="3" cols="70"
			id="LocalFilter@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"
			name="LocalFilter@<?php echo $iServer; ?>@<?php if (!(strpos($layer->{'Name'},":")===False)) {echo substr($layer->{'Name'},strrpos($layer->{'Name'}, ":")+1);}else{echo $layer->Name;}?>"> <?php $localFilter = HTML_proxy ::getLayerLocalFilter($theServer,$layer); if (!(strlen($localFilter)>	0)){?><gml:Polygon
			xmlns:gml="http://www.opengis.net/gml" srsName="EPSG:4326">
            <gml:outerBoundaryIs>
                <gml:LinearRing>                    
					<gml:coordinates>-180,-90 -180,90 180,90 180,-90 -180,-90</gml:coordinates>
                </gml:LinearRing>
            </gml:outerBoundaryIs>
        </gml:Polygon>        		
        <?php } else {echo $localFilter;} ?></textarea></td>
	</tr>
	<?php }}}?>
</table>

</fieldset>
	<?php
	$iServer = $iServer +1;
			}
		}

	}

	//--------------------------------------------------

	function generateWFSHTML($config,$thePolicy){

		$remoteServerList = $config->{'remote-server-list'};
		$iServer=0;


		foreach ($remoteServerList->{'remote-server'} as $remoteServer){

			$urlWithPassword = $remoteServer->url;

			if (strlen($remoteServer->user)!=null && strlen($remoteServer->password)!=null){
				if (strlen($remoteServer->user)>0 && strlen($remoteServer->password)>0){

					if (strpos($remoteServer->url,"http:")===False){
						$urlWithPassword =  "https://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,8);
					}else{
						$urlWithPassword =  "http://".$remoteServer->user.":".$remoteServer->password."@".substr($remoteServer->url,7);
					}
				}
			}


			
			$pos1 = stripos($urlWithPassword, "?");
			$separator = "&";
			if ($pos1 === false) {
	    		//"?" Not found then use ? instead of &
	    		$separator = "?";  
			}
			
			$xmlCapa = simplexml_load_file($urlWithPassword.$separator."REQUEST=GetCapabilities&SERVICE=WFS");
			if ($xmlCapa === false){
					global $mainframe;		
							$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO RETRIEVE THE CAPABILITIES OF THE REMOTE SERVER' ),'error');
			}
			else{
			//$policyServerList = $thePolicy->xpath('//Server');

			foreach ($thePolicy->Servers->Server as $policyServer){
				if (strcmp($policyServer->{'url'},$remoteServer->url)==0){
					$theServer  = $policyServer;
				}
			}
			
			?>
<input type="hidden"
	name="remoteServer<?php echo $iServer; ?>"
	value="<?php echo $remoteServer->url; ?>">
<fieldset class="adminform"><legend><?php echo JText::_( 'EASYSDI_WFS SERVER'); ?> <?php echo $remoteServer->url ?></legend>
<table class="admintable">
	<tr>
		<th><b><?php echo JText::_( 'EASYSDI_FEATURETYPE NAME'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_LOCAL FILTER'); ?></b></th>
		<th><b><?php echo JText::_( 'EASYSDI_REMOTE FILTER'); ?></b></th>
	</tr>

	<?php
	$pos1 = stripos($urlWithPassword, "?");
			$separator = "&";
			if ($pos1 === false) {
	    		//"?" Not found then use ? instead of &
	    		$separator = "?";  
			}
	
	foreach ($xmlCapa->{'FeatureTypeList'}->{'FeatureType'} as $featureType){
		if (! (strrpos($featureType->{'Name'}, ":") ===False)){			
			$xmlDescribeFeature = simplexml_load_file($urlWithPassword.$separator."VERSION=1.0.0&REQUEST=DescribeFeatureType&SERVICE=WFS&TYPENAME=".substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1));
		}else{
			$xmlDescribeFeature = simplexml_load_file($urlWithPassword.$separator."VERSION=1.0.0&REQUEST=DescribeFeatureType&SERVICE=WFS&TYPENAME=".$featureType->{'Name'});
		}
		if ($xmlDescribeFeature === false){
					global $mainframe;		
					$mainframe->enqueueMessage(JText::_(  'EASYSDI_UNABLE TO DESCRIBE THE FEATURE TYPE OF THE REMOTE SERVER.' ),'error');
			}
	
	 foreach ($xmlDescribeFeature->children('http://www.w3.org/2001/XMLSchema') as $entry) {
	 	$element = $entry->{'complexContent'}->{'extension'}->{'sequence'}->{'element'};
	 	for ($i=0;$i<count($element);$i++){
	 		$a = $element[$i]->attributes();
	 		$type = substr($a['type'],strrpos($a['type'], ":")+1);
	 		$geometryName = '';
	 		switch($type){
	 			case 'AbstractGeometryType':
	 			case 'PointType':
	 			case 'PointPropertyType':
	 			case 'CurvePropertyType':
	 			case 'OrientableCurveType':
	 			case 'CompositeCurveType':
	 			case 'AbstractCurveSegmentType':
	 			case 'LineStringType':
	 			case 'ArcStringType':
	 			case 'ArcType':
	 			case 'CircleType':
	 			case 'ArcStringByBulgeType':
	 			case 'ArcByBulgeType':
	 			case 'ArcByCenterPointType':
	 			case 'CircleByCenterPointType':
	 			case 'CubicSplineType':
	 			case 'KnotType':
	 			case 'KnotPropertyType':
	 			case 'BSplineType':
	 			case 'BezierType':
	 			case 'SurfacePropertyType':
	 			case 'SurfaceArrayPropertyType':
	 			case 'OrientableSurfaceType':
	 			case 'CompositeSurfaceType':
	 			case 'PolygonType':
	 			case 'TriangleType':
	 			case 'RectangleType':
	 			case 'RingType':
	 			case 'RingPropertyType':
	 			case 'LinearRingType':
	 			case 'LinearRingPropertyType':
	 			case 'SolidPropertyType':
	 			case 'SolidArrayPropertyType':
	 			case 'SolidType':
	 			case 'CompositeSolidType':
	 			case 'GeometricComplexType':
	 			case 'GeometricComplexPropertyType':
	 			case 'MultiGeometryType':
	 			case 'MultiGeometryPropertyType':
	 			case 'MultiPointType':
	 			case 'MultiPointPropertyType':
	 			case 'MultiCurveType':
	 			case 'MultiCurvePropertyType':
	 			case 'MultiSurfaceType':
	 			case 'MultiSurfacePropertyType':
	 			case 'MultiSolidType':
	 			case 'MultiSolidPropertyType':
	 			case 'DirectPositionType':
	 			case 'CoordType':
	 			case 'MultiLineStringType':
	 			case 'MultiPolygonType':
	 			case 'LineStringPropertyType':
	 			case 'PolygonPropertyType':
	 			case 'MultiLineStringPropertyType':
	 			case 'MultiPolygonPropertyType':
	 			case 'GeometryPropertyType':
	 			case 'GeometryArrayPropertyType':
	 			case 'GeometricPrimitivePropertyType':
	 			case 'GeometryAssociationType':
	 				$geometryName  = $a['name'];
	 				break;
	 		}
	 	}
	 }
	   	 	 	
	 ?>	 
	<tr>
		<td><input
			onClick="activateFeatureType('<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>')"
			<?php if( HTML_proxy ::isChecked($theServer,$featureType)) echo 'checked';?>
			type="checkbox"
			id="featuretype@<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>"
			name="featuretype@<?php echo $iServer; ?>@<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>"
			value="<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>">
			<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?></td>
		<td><textarea rows="3" cols="70"
		<?php if( ! HTML_proxy ::isChecked($theServer,$featureType)) echo 'disabled';?>
			id="LocalFilter@<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>"
			name="LocalFilter@<?php echo $iServer; ?>@<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>"> <?php $localFilter = HTML_proxy ::getFeatureTypeLocalFilter($theServer,$featureType); if (strcmp($localFilter,"")==0){?><Filter
			xmlns:gml='http://www.opengis.net/gml'><Within><PropertyName><?php echo $geometryName ?></PropertyName><gml:Polygon
			xmlns:gml='http://www.opengis.net/gml' srsName='EPSG:4326'>
			<gml:outerBoundaryIs>
				<gml:LinearRing>
					<gml:coordinates>-180,-90 -180,90 180,90 180,-90 -180,-90</gml:coordinates>    </gml:LinearRing>
			</gml:outerBoundaryIs>
		</gml:Polygon></Within></Filter><?php } else {echo $localFilter;} ?></textarea></td>
		<td><textarea rows="3" cols="70"
		<?php if( ! HTML_proxy ::isChecked($theServer,$featureType)) echo 'disabled';?>
			id="RemoteFilter@<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>"
			name="RemoteFilter@<?php echo $iServer; ?>@<?php if (strrpos($featureType->{'Name'}, ":") === false) echo $featureType->{'Name'}; else echo substr($featureType->{'Name'},strrpos($featureType->{'Name'}, ":")+1);?>"> <?php $remoteFilter = HTML_proxy ::getFeatureTypeRemoteFilter($theServer,$featureType); if (strcmp($remoteFilter,"")==0){?><Filter
			xmlns:gml='http://www.opengis.net/gml'><BBOX><PropertyName><?php echo $geometryName ?></PropertyName><Box
			srsName="EPSG:4326" gml="http://www.opengis.net/gml"> <coordinates>-180,-90 180,90</coordinates> </Box></BBOX></Filter><?php } else {echo $remoteFilter;} ?></textarea>
		</td>

		
	</tr>
	<?php }
	?>
</table>

</fieldset>
	<?php
	$iServer = $iServer +1;
			}
		}

	}
	function showConfigList($xml){
		global $mainframe;
		JToolBarHelper::title( JText::_(  'EASYSDI_SHOW CONFIGURATIONS LIST' ), 'generic.png' );
		jimport("joomla.html.pagination");
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');

		?>

<form name='adminForm' action='index.php' method='GET'><input
	type='hidden' name='option'
	value='<?php echo JRequest::getVar('option') ;?>'> <input type='hidden'
	name='task' value='<?php echo JRequest::getVar('task') ;?>'> <input type='hidden' name='boxchecked'
	value='1'>

<table>
	<tr>
		<td width="100%"><?php echo JText::_( 'EASYSDI_FILTER' ); ?>: <input
			type="text" name="search" id="search" value="" class="text_area"
			onchange="document.adminForm.submit();" />
		<button onclick="this.form.submit();"><?php echo JText::_( 'EASYSDI_GO' ); ?></button>
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
			<?php if (strlen($id)>0){if (strcmp($id,$config[id])==0){echo 'checked';}} ?>
				type="radio" name="configId" value="<?php echo $config['id'] ?>"></td>
			<td><b><?php echo $config['id']?></b> <?php echo "{", $config->{'servlet-class'},"}";  ?></td>
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
}
?>