<?php defined('_JEXEC') or die('Restricted access');

class easysdi_HTML {

//
//Configure using the config link
//
function configComponent($xmlConfig, $fnblLogger){

JToolBarHelper::title( JText::_(  'CONFIGURE COMPONENT' ), 'generic.png' );
$option = JRequest::getVar('option'); 

?>
<script>
//FunambolDB Ajax service point
var fnblBDurl = './components/com_easysdi_syncdbtable/core/syncdbtable.funambolDBAjaxScripts.easysdi.php';

//convenience function to add eventlisteners when page loads
function addLoadEvent(func){
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

//when page loads, fill in user, devices, principal tables
function init(){
	loadUsers();
	loadDevices();
	loadPrincipals();
}
addLoadEvent(init);

function loadUsers(){
	var url =  fnblBDurl;
    var params = {operation: 'getusers', db: 'funambol'};

	document.getElementById("dbErrorMessage").innerHTML = "loading datas, please wait...";
	var req = new Ajax(url, {
		method: 'get',
		onSuccess: function(){
			//check if exception returned, display it
			var ex = this.response.xml.getElementsByTagName('exception');
			if(ex.length > 0){
				document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
			}
			//No exception
			else
			{	
				updateUserfields(this.response.xml);
				document.getElementById("dbErrorMessage").innerHTML = "";
			}
		},
		onFailure: function(){
			document.getElementById("dbErrorMessage").innerHTML = "Request failed";
		}
	}).request(params);
}

function loadDevices(){
	var url =  fnblBDurl;
    var params = {operation: 'getdevices', db: 'funambol'};

	document.getElementById("dbErrorMessage").innerHTML = "loading datas, please wait...";
	var req = new Ajax(url, {
		method: 'get',
		onSuccess: function(){
			//check if exception returned, display it
			var ex = this.response.xml.getElementsByTagName('exception');
			if(ex.length > 0){
				document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
			}
			//No exception
			else
			{	
				updateDevicesfields(this.response.xml);
				document.getElementById("dbErrorMessage").innerHTML = "";
			}
		},
		onFailure: function(){
			document.getElementById("dbErrorMessage").innerHTML = "Request failed";
		}
	}).request(params);
}

function loadPrincipals(){
	var url =  fnblBDurl;
    var params = {operation: 'getprincipals', db: 'funambol'};

	document.getElementById("dbErrorMessage").innerHTML = "loading datas, please wait...";
	var req = new Ajax(url, {
		method: 'get',
		onSuccess: function(){
			//check if exception returned, display it
			var ex = this.response.xml.getElementsByTagName('exception');
			if(ex.length > 0){
				document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
			}
			//No exception
			else
			{	
				updatePrincipalsfields(this.response.xml);
				document.getElementById("dbErrorMessage").innerHTML = "";
			}
		},
		onFailure: function(){
			document.getElementById("dbErrorMessage").innerHTML = "Request failed";
		}
	}).request(params);
}

function updateDevicesfields(xmlresponse)
{
	var root = xmlresponse.getElementsByTagName('rows')[0];
	oldTable = document.getElementById("fnblDeviceTable");
	table = document.createElement(oldTable.tagName);
	table.id = oldTable.id;
	oldTable.parentNode.replaceChild(table, oldTable);
	
	//for each result
	for (i=0; i<root.childNodes.length; i++) 
	{
		//skip empty text nodes
		if(root.childNodes[i].nodeType != 1)
			continue;
		elem = root.childNodes[i];
		//for each row
		arrHeaders = new Array();
		arrValues = new Array();
		k = 0;
		for (j=0; j<elem.childNodes.length; j++) 
		{	
			//skip empty text nodes
			if(elem.childNodes[j].nodeType != 1)
				continue;
			attribute = elem.childNodes[j].attributes[0].nodeValue;
			//take care of null values
			if(elem.childNodes[j].firstChild != null)
				value = elem.childNodes[j].firstChild.nodeValue;
			else
				value = '';
			switch (attribute) {
				case 'id':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'description':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
			}
		}
		//call update row
		addTableRow(table, arrHeaders, arrValues);
	}
}


//Update table User
function updateUserfields(xmlresponse)
{
	var root = xmlresponse.getElementsByTagName('rows')[0];
	oldTable = document.getElementById("fnblUserTable");
	table = document.createElement(oldTable.tagName);
	table.id = oldTable.id;
	oldTable.parentNode.replaceChild(table, oldTable);
	
	//for each result
	for (i=0; i<root.childNodes.length; i++) 
	{
		//skip empty text nodes
		if(root.childNodes[i].nodeType != 1)
			continue;
		elem = root.childNodes[i];
		//for each row
		arrHeaders = new Array();
		arrValues = new Array();
		k = 0;
		for (j=0; j<elem.childNodes.length; j++) 
		{	
			//skip empty text nodes
			if(elem.childNodes[j].nodeType != 1)
				continue;
			attribute = elem.childNodes[j].attributes[0].nodeValue;
			//take care of null values
			if(elem.childNodes[j].firstChild != null)
				value = elem.childNodes[j].firstChild.nodeValue;
			else
				value = '';
			switch (attribute) {
				case 'username':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'password':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'email':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'first_name':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'last_name':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
			}
		}
		//call update row
		addTableRow(table, arrHeaders, arrValues);
	}
}

function updatePrincipalsfields(xmlresponse)
{
	var root = xmlresponse.getElementsByTagName('rows')[0];
	oldTable = document.getElementById("fnblPrincipalTable");
	table = document.createElement(oldTable.tagName);
	table.id = oldTable.id;
	oldTable.parentNode.replaceChild(table, oldTable);
	
	//for each result
	for (i=0; i<root.childNodes.length; i++) 
	{
		//skip empty text nodes
		if(root.childNodes[i].nodeType != 1)
			continue;
		elem = root.childNodes[i];
		//for each row
		arrHeaders = new Array();
		arrValues = new Array();
		k = 0;
		for (j=0; j<elem.childNodes.length; j++) 
		{	
			//skip empty text nodes
			if(elem.childNodes[j].nodeType != 1)
				continue;
			attribute = elem.childNodes[j].attributes[0].nodeValue;
			//take care of null values
			if(elem.childNodes[j].firstChild != null)
				value = elem.childNodes[j].firstChild.nodeValue;
			else
				value = '';
			switch (attribute) {
				case 'id':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'username':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
				case 'device':
					arrHeaders[k] = attribute;
					arrValues[k] = value;
					k++;
				break;
			}
		}
		//call update row
		addTableRow(table, arrHeaders, arrValues);
	}
}


function addTableRow(table, arrHeader, arrValues)
{
	childCount = table.getElementsByTagName('tr').length;
	tableId = table.id;
	var newline = addNewTableRow(table, false);
	for (elem = 0; elem<arrHeaders.length; elem++)
	{
		newInput = newline.getElementsByTagName('input')[elem];
		newInput.value = arrValues[elem];
	}
}

function addNewTableRow(table, isnNewLine)
{
	childCount = table.getElementsByTagName('tr').length;
	tableId = table.id;
	//create a new line base on result number
	var newline = document.createElement('tr');
	//custom attribute: tells it is a new line
	if(isnNewLine)
		newline.isNew = true;
	else
		newline.isNew = false;
	table.appendChild(newline);
	newline.id = tableId+'_row_'+childCount;
	newline.className = 'row'+(childCount%2);
	//count number of columns
	headers = document.getElementById(tableId+'Headers');
	//-1 because of sel and update field which are no data field
	columnsCount = headers.getElementsByTagName('th').length - 2;
	//each arrHeader elem represents a row
	for (elem = 0; elem<columnsCount; elem++)
	{
		newTd = document.createElement('td');
		newline.appendChild(newTd);
		newTd.colSpan = 4;
		newTd.align = 'center';
		newInput = document.createElement('input');
		newTd.appendChild(newInput);
		newInput.type = 'text';
		newInput.name = tableId+'_'+childCount+'_'+elem;
		newInput.id = tableId+'_'+childCount+'_'+elem;
		newInput.value = '';
	}
	//Add update button
	newTd = document.createElement('td');
	newline.appendChild(newTd);
	newTd.colSpan = 4;
	newTd.align = 'center';
	newButton = document.createElement('input');
	newButton.type = 'button';
	newTd.appendChild(newButton);
	newButton.name = childCount;
	newButton.id = tableId+'_update_'+childCount;
	newButton.value = '=>';
	//just a reference for delegate below
	newButton.tableRef = tableId;
	//newButton.addEventListener('onclick', UpdateRowValues(), false);
	newButton.onclick = function () { UpdateRowValues(this.tableRef, this.name); };
	//Add sel radio and update radio
	newTd = document.createElement('td');
	newline.appendChild(newTd);
	newTd.colSpan = 4;
	newTd.align = 'center';
	
	var newRadio;
	//because IE and FF doesn't handle dynamic radio the same way...
	try
	{  
		newRadio = document.createElement('<input type="radio" name="'+tableId+'_selected" />');
	}catch(err){  
		newRadio = document.createElement('input');
		newRadio.type = 'radio';
	}
	newRadio.id = tableId+'_selected_'+childCount;
	newRadio.value = tableId+'_selected';
	newTd.appendChild(newRadio);
	return newline;
}

function UpdateRowValues(tableId, rowNr)
{
	//is it a new record or an update? (lineRef.isNew)
	rowName = tableId+"_row_rowNr";
	var lineRef = $(rowName.replace("rowNr", rowNr));
	var operationHeader = '';
	if(lineRef.isNew)
		operationHeader='insert';
	else
		operationHeader='update';
	var params;
	switch (tableId) {
		case 'fnblUserTable':
			params = {operation: operationHeader+'user',
			db: 'funambol',
			password: $("fnblUserTable_rowNr_1".replace("rowNr", rowNr)).value,
			email: $("fnblUserTable_rowNr_2".replace("rowNr", rowNr)).value,
			first_name: $("fnblUserTable_rowNr_3".replace("rowNr", rowNr)).value, 
			last_name: $("fnblUserTable_rowNr_4".replace("rowNr", rowNr)).value,
			username:$("fnblUserTable_rowNr_0".replace("rowNr", rowNr)).value};
		break;
		case 'fnblDeviceTable':
			params = {operation: operationHeader+'device',
			db: 'funambol',
			description: $("fnblDeviceTable_rowNr_1".replace("rowNr", rowNr)).value,
			deviceid: $("fnblDeviceTable_rowNr_0".replace("rowNr", rowNr)).value};
		break;
		case 'fnblPrincipalTable':
			params = {operation: operationHeader+'principal',
			db: 'funambol',
			username: $("fnblPrincipalTable_rowNr_0".replace("rowNr", rowNr)).value,
			deviceid: $("fnblPrincipalTable_rowNr_1".replace("rowNr", rowNr)).value,
			principalid: $("fnblPrincipalTable_rowNr_2".replace("rowNr", rowNr)).value};
		break;
	}
	
	var url =  fnblBDurl;
	document.getElementById("dbErrorMessage").innerHTML = "updating datas, please wait...";
	var req = new Ajax(url, {
		method: 'get',
		onSuccess: function(){
			//check if exception returned, display it
			var ex = this.response.xml.getElementsByTagName('exception');
			if(ex.length > 0){
				document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
			}
			//No exception, refresh tables
			else
			{	
				switch (tableId) {
					case 'fnblUserTable':
						loadUsers();
						break;
					case 'fnblDeviceTable':
						loadDevices();
						break;
					case 'fnblPrincipalTable':
						loadPrincipals();
						break;
				}
				document.getElementById("dbErrorMessage").innerHTML = '';
			}
		},
		onFailure: function(){
			document.getElementById("dbErrorMessage").innerHTML = "Request failed";
		}
	}).request(params);
}


	
function removeTableRow(table, radio)
{
	tableId = table.id;
	var lineChecked;
	var empty = true;
	if(!radio.length)
		return;
	for (var i=0; i<radio.length;i++) {
         if (radio[i].checked) {
			 rowNr = i;
			 switch (tableId) {
				 //call ajax script to erase data from db and refresh
				 case 'fnblUserTable':
					params = {operation: 'deleteuser',
					db: 'funambol',
					username:$("fnblUserTable_rowNr_0".replace("rowNr", rowNr)).value};
				break;
				case 'fnblDeviceTable':
					params = {operation: 'deletedevice',
					db: 'funambol',
					deviceid: $("fnblDeviceTable_rowNr_0".replace("rowNr", rowNr)).value};
				break;
				case 'fnblPrincipalTable':
				params = {operation: 'deleteprincipal',
					db: 'funambol',
					principalid: $("fnblPrincipalTable_rowNr_2".replace("rowNr", rowNr)).value};
				break;
			 }
			empty = false;
			var url =  fnblBDurl;
			document.getElementById("dbErrorMessage").innerHTML = "updating datas, please wait...";
			var req = new Ajax(url, {
				method: 'get',
				onSuccess: function(){
					//check if exception returned, display it
					var ex = this.response.xml.getElementsByTagName('exception');
					if(ex.length > 0){
						document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
					}
					//No exception, refresh tables
					else
					{	
						switch (tableId) {
							case 'fnblUserTable':
								loadUsers();
								break;
							case 'fnblDeviceTable':
								loadDevices();
								break;
							case 'fnblPrincipalTable':
								loadPrincipals();
								break;
						}
						document.getElementById("dbErrorMessage").innerHTML = '';
					}
				},
				onFailure: function(){
					document.getElementById("dbErrorMessage").innerHTML = "Request failed";
				}
			}).request(params);
		 }
	}
	if(empty){
	   alert("You must select a line first");
	}
}

</script>


<form name='adminForm' id='adminForm' action='index.php' method='POST'>	
<input
type='hidden' name='option' value='<?php echo $option;?>'> <input
type='hidden' name='task' value=''> 

<fieldset class="adminform"><legend><?php echo JText::_( 'FUNAMBOL PATH' );?></legend>
<table class="admintable">
	<tr>
		<td colspan="4"><?php echo JText::_( 'FUNAMBOL HOME' );?></td>
		<td colspan="4"><input type='text' name='funambolHome' size="200"
			value='<?php echo $xmlConfig->funambol_home;?>'></td>
	</tr>
</table>
</fieldset>

<fieldset class="adminform"><legend><?php echo JText::_( 'FUNAMBOL LOG' );?></legend>
<fieldset class="adminform"><legend><?php echo JText::_( 'LOGGERS' );?></legend>
<table class="admintable">
	<tr>
		<td colspan="4"><?php echo JText::_( 'LEVEL' );?></td>
		<td colspan="4">
			<select name="fnblLoggingLevel" id="fnblLoggingLevel">
			    <?php  $res = $fnblLogger->xpath('//void[@property="level"]');?>
				<option value="OFF" <?php if ($res[0]->string == "OFF") echo "SELECTED"; ?>>OFF</option>
				<option value="FATAL" <?php if ($res[0]->string == "FATAL") echo "SELECTED"; ?> >FATAL</option>
				<option value="ERROR" <?php if ($res[0]->string == "ERROR") echo "SELECTED"; ?>>ERROR</option>
				<option value="WARN" <?php if ($res[0]->string == "WARN") echo "SELECTED"; ?>>WARN</option>
				<option value="INFO" <?php if ($res[0]->string == "INFO") echo "SELECTED"; ?>>INFO</option>
				<option value="DEBUG" <?php if ($res[0]->string == "DEBUG") echo "SELECTED"; ?>>DEBUG</option>
				<option value="TRACE" <?php if ($res[0]->string == "TRACE") echo "SELECTED"; ?>>TRACE</option>
				<option value="ALL" <?php if ($res[0]->string == "ALL") echo "SELECTED"; ?>>ALL</option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="4"><?php echo JText::_( 'APPENDER' );?></td>
		<td colspan="4">
			<select name="fnblLoggingAppenders" id="fnblLoggingAppenders">
			    <?php  $res = $fnblLogger->xpath('//void[@property="appenders"]');?>
				<option value="funambol.console" <?php if ($res[0]->object[0]->void[0]->string == "funambol.console") echo "SELECTED"; ?>>funambol.console</option>
				<option value="funambol.content-provider.logfile" <?php if ($res[0]->object[0]->void[0]->string == "funambol.content-provider.logfile") echo "SELECTED"; ?> >funambol.content-provider.logfile</option>
				<option value="funambol.daily.logfile" <?php if ($res[0]->object[0]->void[0]->string == "funambol.daily.logfile") echo "SELECTED"; ?>>funambol.daily.logfile</option>
				<option value="funambol.logfile" <?php if ($res[0]->object[0]->void[0]->string == "funambol.logfile") echo "SELECTED"; ?>>funambol.logfile</option>
			</select>
		</td>
	</tr>
</table>
</fieldset>

<!--
Start here if you want to enable editing Appenders
place each appender in a <div> and set the visibility according the select box

<fieldset class="adminform"><legend><?php echo JText::_( 'APPENDERS' );?></legend>
<table class="admintable">
	<tr>
		<td colspan="4"><?php echo JText::_( 'LEVEL' );?></td>
		<td colspan="4">
			<select name="fnblShowLoggingAppenders" id="fnblShowLoggingAppenders">
				<option value="funambol.console">funambol.console</option>
				<option value="funambol.content-provider.logfile">funambol.content-provider.logfile</option>
				<option value="funambol.daily.logfile">funambol.daily.logfile</option>
				<option value="funambol.logfile">funambol.logfile</option>
			</select>
		</td>
	</tr>
</table>
</fieldset>
-->
</fieldset>
<!-- error Message -->
<table class="admintable">
	<tr>
	<td colspan="4">Message:</td>
		<td colspan="4"><div id="dbErrorMessage"></div></td>
	</tr>
</table>
<!-- Funambol users -->
<table width="100%">
  <tr>
  	<td width="60%">
		<script></script>
			<fieldset class="adminform"><legend><?php echo JText::_( 'USERS' );?></legend>
				<table class="adminlist" width="50">
					<thead id="fnblUserTableHeaders">
						<tr>
							<th colspan="4"><?php echo JText::_( 'USERNAME'); ?></th>
							<th colspan="4"><?php echo JText::_( 'PASSWORD'); ?></th>
							<th colspan="4"><?php echo JText::_( 'EMAIL'); ?></th>
							<th colspan="4"><?php echo JText::_( 'FIRSTNAME'); ?></th>
							<th colspan="4"><?php echo JText::_( 'LASTNAME'); ?></th>
							</th>
							<th colspan="4"><?php echo JText::_( 'SAVE'); ?></th>
							<th colspan="4"><?php echo JText::_( 'SEL'); ?></th>
						</tr>
					</thead>
					<tbody id="fnblUserTable"/>
				</table>
			</fieldset>
	</td>
	<td width="40%">
		<table>
			<!-- Insert new User -->
			<tr>
				<td colspan="4"><input type="button" name="insertUser" value="New" onclick="addNewTableRow(document.getElementById('fnblUserTable'), true);"></td>
			</tr>
			<!-- Remove existing user -->
			<tr>
				<td colspan="4"><input type="button" name="removeUser" value="Remove" onclick="removeTableRow(document.getElementById('fnblUserTable'), this.form.fnblUserTable_selected);"></td>
			</tr>
		</table>
	</td>
  </tr>
</table>

<!-- Funambol devices -->
<table width="100%">
  <tr>
  	<td width="60%">
		<script></script>
			<fieldset class="adminform"><legend><?php echo JText::_( 'DEVICES' );?></legend>
				<table class="adminlist" width="50">
					<thead>
						<tr id="fnblDeviceTableHeaders">
							<th colspan="4"><?php echo JText::_( 'ID'); ?></th>
							<th colspan="4"><?php echo JText::_( 'DESCRIPTION'); ?></th>
							<th colspan="4"><?php echo JText::_( 'SAVE'); ?></th>
							<th colspan="4"><?php echo JText::_( 'SEL'); ?></th>
						</tr>
					</thead>
					<tbody id="fnblDeviceTable"/>
				</table>
			</fieldset>
	</td>
	<td width="40%">
		<table>
			<!-- Insert new device -->
			<tr>
				<td colspan="4"><input type="button" name="insertDevice" value="New" onclick="addNewTableRow(document.getElementById('fnblDeviceTable'), true);"></td>
			</tr>
			<!-- Remove existing device -->
			<tr>
				<td colspan="4"><input type="button" name="removeDevice" value="Remove" onclick="removeTableRow(document.getElementById('fnblDeviceTable'), this.form.fnblDeviceTable_selected);"></td>
			</tr>	
		</table>
	</td>
  </tr>
</table>

<!-- Funambol Principals -->
<table width="100%">
  <tr>
  	<td width="60%">
		<script></script>
			<fieldset class="adminform"><legend><?php echo JText::_( 'PRINCIPALS' );?></legend>
				<table class="adminlist" width="50">
					<thead>
						<tr id="fnblPrincipalTableHeaders">
							<th colspan="4"><?php echo JText::_( 'USERNAME'); ?></th>
							<th colspan="4"><?php echo JText::_( 'DEVICE'); ?></th>
							<th colspan="4"><?php echo JText::_( 'ID'); ?></th>
							<th colspan="4"><?php echo JText::_( 'SAVE'); ?></th>
							<th colspan="4"><?php echo JText::_( 'SEL'); ?></th>
						</tr>
					</thead>
					<tbody id="fnblPrincipalTable"/>
				</table>
			</fieldset>
	</td>
	<td width="40%">
		<table>
			<!-- Insert new User -->
			<tr>
				<td colspan="4"><input type="button" name="insertPrincipal" value="New" onclick="addNewTableRow(document.getElementById('fnblPrincipalTable'), true);"></td>
			</tr>
			<!-- Remove existing user -->
			<tr>
				<td colspan="4"><input type="button" name="insertPrincipal" value="Remove" onclick="removeTableRow(document.getElementById('fnblPrincipalTable'), this.form.fnblPrincipalTable_selected);"></td>
			</tr>	
		</table>
	</td>
  </tr>
</table>

</form>

<?php
}

//
//End configComponent
//



//Called by controller at first load
	function ctrlPanel(){
		JToolBarHelper::title( JText::_(  'EASYSDI CONTROL PANEL' ), 'generic.png' );
		global $mainframe;
		$lang		=& JFactory::getLanguage();
		$template	= $mainframe->getTemplate();
	
		jimport('joomla.html.pane');
$pane		=& JPane::getInstance('sliders');
	echo $pane->startPane("content-pane");
			echo $pane->startPanel( JText::_('EASYSDI MODULES'), 'cpanel-panel-1' );
		?>
	<div id="cpanel">
		<?php
		$link = 'index.php?option=com_easysdi&amp;task=componentConfig';
?>
		<div style="float:<?php echo ($lang->isRTL()) ? 'right' : 'left'; ?>;">
			<div class="icon">
				<a href="<?php echo $link; ?>">
				
					<?php 
					$text = JText::_( 'COMPONENT CONFIGURATION' );
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
						$text = JText::_( 'PROXY CONFIGURATION' );					
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

	
		echo $pane->startPanel( JText::_('LICENSE'), 'cpanel-panel-licence' );
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

	
//Edit a syncML Job, $xml = job file
function editconfig($xml, $new){
	$option = JRequest::getVar('option');
	$configId = JRequest::getVar("configId");
	JToolBarHelper::title( JText::_( 'EDIT TABLE SYNC SOURCE :' ).$configId, 'edit.png' );
	?>
<script>
var fnblBDurl = './components/com_easysdi_syncdbtable/core/syncdbtable.funambolDBAjaxScripts.easysdi.php';

function submitbutton(pressbutton){

if (pressbutton=="addNewServer"){	
	addNewServer();
	}
	else{	
	submitform(pressbutton);
	}
}

function loadFieldsFromDb(tableName){
	var url =  fnblBDurl;
	var provider = document.getElementById("adodbProvider").value;	
	var host = document.getElementById("adodbName").value;
	var user = document.getElementById("adodbUser").value;
	var pwd = document.getElementById("adodbPassword").value;
	var db = document.getElementById("adodbDatabase").value;
	
	if(host=='' ||user==''||db==''||provider==''){
		alert('You must specifiy the host, user and db at least.');
		return false;
	}
	
	var params = {
		operation: 'gettablerows',
                tablename: tableName,
		host: host,
		provider: provider,
                user: user,
                pwd: pwd,
                db: db
        };
	
	document.getElementById("dbErrorMessage").innerHTML = "loading datas, please wait...";
	var req = new Ajax(url, {
		method: 'get',
		onSuccess: function(){
			//check if exception returned, display it
			var ex = this.response.xml.getElementsByTagName('exception');
			if(ex.length > 0){
				document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
			}
			//No exception
			else
			{	
				updateTablefields(this.response.xml);
				document.getElementById("dbErrorMessage").innerHTML = "";
			}
		},
		onFailure: function(){
			document.getElementById("dbErrorMessage").innerHTML = "Request failed";
		}
	}).request(params);
}

function loadTablesFromDb(){
	
	var url =  fnblBDurl;	
	var provider = document.getElementById("adodbProvider").value;
	var host = document.getElementById("adodbName").value;
	var user = document.getElementById("adodbUser").value;
	var pwd = document.getElementById("adodbPassword").value;
	var db = document.getElementById("adodbDatabase").value;
	
	if(host=='' ||user==''||db==''||provider==''){
		alert('You must specifiy the host, user and db at least.');
		return false;
	}
	
	var params = {
		operation: 'gettablenames',
                provider: provider,
		host: host,
                user: user,
                pwd: pwd,
                db: db
        };
	
	document.getElementById("dbErrorMessage").innerHTML = "loading datas, please wait...";
	var req = new Ajax(url, {
		method: 'get',
		onSuccess: function(){
			//check if exception returned, display it
			var ex = this.response.xml.getElementsByTagName('exception');
			if(ex.length > 0){
				document.getElementById("dbErrorMessage").innerHTML = ex[0].firstChild.nodeValue;
			}
			//No exception
			else
			{	
				//update tablename selectbox
				updateTableName(this.response.xml);
				//update field mappings
				loadFieldsFromDb(document.getElementById('tiTableName').options[document.getElementById('tiTableName').selectedIndex].value);
				document.getElementById("dbErrorMessage").innerHTML = "";
			}
		},
		onFailure: function(){
			document.getElementById("dbErrorMessage").innerHTML = "Request failed";
		}
	}).request(params);
}

//fill in the option box with table names retrieved from db

function updateTableName(xmlresponse){
	var doc = xmlresponse;	
	var root = doc.getElementsByTagName('tables')[0];
	//select box to update
	var tableOpt = document.getElementById("tiTableName");
	//reset options
	tableOpt.options.length = 0;
	//add new options
	for (i=0; i<root.childNodes.length; i++) {
		value = root.childNodes[i].firstChild.nodeValue;
		tableOpt.options[tableOpt.options.length] = new Option(value,value);
	}		
}
//Fill in select boxes
//Fill in Mapping fields
function updateTablefields(xmlresponse){
	
	var doc = xmlresponse;	
	var root = doc.getElementsByTagName('columns')[0];
	//select boxes to update
	var tiPrincipal = document.getElementById("tiPrincipal");
	var tiLastUpdateType = document.getElementById("tiLastUpdateType");
	var tiLastUpdateTime = document.getElementById("tiLastUpdateTime");
	var tiKey = document.getElementById("tiKey");
	
	//reset options
	tiPrincipal.options.length = 0;
	tiLastUpdateType.options.length = 0;
	tiLastUpdateTime.options.length = 0;
	tiKey.options.length = 0;

	//add an empty option for principal
	tiPrincipal.options[tiPrincipal.options.length] = new Option("---","");
	//add new options
	for (i=0; i<root.childNodes.length; i++) {
		value = root.childNodes[i].firstChild.nodeValue;
		tiPrincipal.options[tiPrincipal.options.length] = new Option(value,value);
		tiLastUpdateType.options[tiLastUpdateType.options.length] = new Option(value,value);
		tiLastUpdateTime.options[tiLastUpdateTime.options.length] = new Option(value,value);
		tiKey.options[tiKey.options.length] = new Option(value,value);
	}
	
	//Clear all existing fields
	table = document.getElementById("fieldMappingTable");
	count = table.getElementsByTagName('tr').length;
	radio =  document.getElementById("adminForm").MappedField_Selected;
	index = count-1;
	for (var i=0; i<count-1; i++) {
	   document.getElementById("MappedField_Selected"+index).checked = true;
	   removeMappingField(radio);
	   index--;
	}
	
	//fill in Mapping fields with fields retrieved from db
	for (i=0; i<root.childNodes.length; i++){
		value = root.childNodes[i].firstChild.nodeValue;
		addMappingField(value, value);
	}
	
	//remove first element
	document.getElementById("MappedField_Selected0").checked = true;
	removeMappingField(radio);
	//Check Mapped fields, because key-field, timestamp, update-type, principal
	//must'nt figure into the Mapping fields
	checkMappedfields();
}

//Check Mapped fields, because key-field, timestamp, update-type, principal
//must'nt figure into the Mapping fields
function checkMappedfields(){
	var tiPrincipal = document.getElementById("tiPrincipal");
	var tiPrincipalValue = tiPrincipal[tiPrincipal.options.selectedIndex].value;
	var tiLastUpdateType = document.getElementById("tiLastUpdateType");
	var tiLastUpdateTypeValue = tiLastUpdateType[tiLastUpdateType.options.selectedIndex].value;
	var tiLastUpdateTime = document.getElementById("tiLastUpdateTime");
	var tiLastUpdateTimeValue = tiLastUpdateTime[tiLastUpdateTime.options.selectedIndex].value;
	var tiKey = document.getElementById("tiKey");
	var tiKeyValue = tiKey[tiKey.options.selectedIndex].value;
	
	//unpermitted mapped fields
	unPermMappedFields = new Array(tiPrincipalValue, tiLastUpdateTypeValue, tiLastUpdateTimeValue, tiKeyValue);
	table = document.getElementById("fieldMappingTable");
	count = table.getElementsByTagName('tr').length;
	j = 0;
	for (var i=0; i<count; i++) {
	   var serverField = document.getElementById("MappedField_Server"+i).value;
	   if(contains(unPermMappedFields, serverField)){
		   //td  background-color
		   document.getElementById("mapping"+i).getElementsByTagName('td')[0].style.background = '#FF0000';
		   document.getElementById("mapping"+i).getElementsByTagName('td')[1].style.background = '#FF0000';
	   }else
	   {
		   document.getElementById("mapping"+i).getElementsByTagName('td')[0].style.background = '';
		   document.getElementById("mapping"+i).getElementsByTagName('td')[1].style.background = '';
	   }
	}
}

function contains(a, obj){
	for(var i = 0; i < a.length; i++) {
		if(a[i] === obj){
			return true;    
		}  
	}  
return false;
}

</script>

<form name='adminForm' id='adminForm' action='index.php' method='POST'>
	<input type='hidden' name="isNewConfig" value="<?php echo $new; ?>">
	<input
	type='hidden' name='option' value='<?php echo $option;?>'> <input
	type='hidden' name='task' value=''> <input type='hidden'
	name='configId' value='<?php echo $configId;?>'> <?php
	?>

<!-- General infos -->
<fieldset class="adminform"><legend><?php echo JText::_( 'GENERAL INFOS' );?></legend>
<table class="admintable">
	<!-- Source URI correspond to job id -->
	<tr>
	<?php if($new){ ?>
		<td colspan="4"><?php echo JText::_( 'SOURCE URI' );?> :</td>
		<td colspan="4"><input type='text' name='newSourceUri' value=''></td>
	<?php }else{ ?>
		<td colspan="4"><?php echo JText::_( 'SOURCE URI' );?> :</td>
		<td colspan="4"><?php $res = $xml->xpath('//void[@property="sourceURI"]'); echo $res[0]->string; ?></td>
		<input type='hidden' name='sourceUri' value='<?php $res = $xml->xpath('//void[@property="sourceURI"]'); echo $res[0]->string; ?>'>
	<?php }?>
	</tr>
	
	<!-- Job id -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'JOB ID' );?> :</td>
		<td colspan="4"><input type='text' name='newConfigName'
			value='<?php $res = $xml->xpath('//void[@property="name"]'); echo $res[0]->string; ?>'></td>
	</tr>
	<!-- Encoding Type -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'ENCODING TYPE' );?> :</td>
		<td colspan="4"><input type='text' name='encodingType'
			value='<?php $res = $xml->xpath('//void[@property="type"]'); echo $res[0]->string; ?>'></td>
	</tr>
	<!-- JNDI Datasource name -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'JNDI DATASOURCE NAME' );?> :</td>
		<td colspan="4"><input type='text' name='jndiDataSource'
			value='<?php $res = $xml->xpath('//void[@property="jndiName"]'); echo $res[0]->string;  ?>'></td>
	</tr>
</table>
</fieldset>

<!-- Load data from DB -->
<fieldset class="adminform"><legend><?php echo JText::_( 'DSN CONNECTION INFO' );?></legend>
<table class="admintable">
	<!-- provider -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'PROVIDER' );?> :</td>
		<td colspan="4"><input type='text' id='adodbProvider' name='adodbProvider' value='mysql'
			value=''></td>
	</tr>
	
	<!-- host -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'HOST' );?> :</td>
		<td colspan="4"><input type='text' id='adodbName' name='adodbName' value='localhost:3306'
			value=''></td>
	</tr>
	
	<!-- user -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'USER' );?> :</td>
		<td colspan="4"><input type='text' id='adodbUser' name='adodbUser' value='root'
			value=''></td>
	</tr>
	
	<!-- password -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'PASSWORD' );?> :</td>
		<td colspan="4"><input type='password' id='adodbPassword' name='adodbPassword' value=''
			value=''></td>
	</tr>
	
	<!-- database -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'DATABASE' );?> :</td>
		<td colspan="4"><input type='text' id='adodbDatabase' name='adodbDatabase' value='employees'
			value=''></td>
	</tr>
		
	<!-- error Message -->
	<tr>
	<td colspan="4">Message:</td>
		<td colspan="4"><div id="dbErrorMessage"></div></td>
	</tr>
	
</table>
</fieldset>

<!-- Table infos -->
<fieldset class="adminform"><legend><?php echo JText::_( 'TABLE INFOS' );?></legend>
<table class="admintable">
	<!-- Table Name -->

	<tr>
		<td colspan="4"><?php echo JText::_( 'TABLE NAME' );?> :</td>
		<td colspan="4">
		<select name="tiTableName" id="tiTableName" onChange="javascript:loadFieldsFromDb(this.value);">
				<?php $res = $xml->xpath('//void[@property="tableName"]');?>
				<option value="<?php echo $res[0]->string; ?>"><?php echo $res[0]->string; ?></option>
			</select>
		</td>
		<!-- load data button -->
		<td colspan="4"><input type="button" name="loadDataFromDB" value="Load tables from DB" onClick="javascript:loadTablesFromDb();"></td>
	</tr>
	<!-- Key Field -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'KEY FIELD' );?> :</td>
		<td colspan="4">
		<select name="tiKey" id="tiKey" onchange="javascript:checkMappedfields();">
				<?php $res = $xml->xpath('//void[@property="keyField"]');?>
				<option value="<?php echo $res[0]->string; ?>"><?php echo $res[0]->string; ?></option>
			</select>
		</td>
	</tr>
	<!-- Last update timestamp field -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'LAST UPDATE TIMESTAMP FIELD' );?> :</td>
		<td colspan="4">
			<select name="tiLastUpdateTime" id="tiLastUpdateTime" onchange="javascript:checkMappedfields();">
				<?php $res = $xml->xpath('//void[@property="updateDateField"]');?>
				<option value="<?php echo $res[0]->string; ?>"><?php echo $res[0]->string; ?></option>
			</select>
		</td>
	</tr>
	<!-- Last update type field -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'LAST UPDATE TYPE FIELD' );?> :</td>
		<td colspan="4">
			<select id="tiLastUpdateType" name="tiLastUpdateType" onchange="javascript:checkMappedfields();">
				<?php $res = $xml->xpath('//void[@property="updateTypeField"]');?>
				<option value="<?php echo $res[0]->string; ?>"><?php echo $res[0]->string; ?></option>
			</select>
		</td>
	</tr>
	<!-- Principal field -->
	<tr>
		<td colspan="4"><?php echo JText::_( 'PRINCIPAL FIELD' );?> :</td>
		<td colspan="4">
			<select id="tiPrincipal" name="tiPrincipal" onchange="javascript:checkMappedfields();">
				<?php $res = $xml->xpath('//void[@property="updatePrincipalField"]');?>
				<option value="">---</option>
				<option value="<?php echo $res[0]->string; ?>"><?php echo $res[0]->string; ?></option>
			</select>
		</td>
	</tr>
</table>
<!-- field mappings -->
<table width="100%">
<tr>
<td width="30%">
<script>
function removeMappingField(radio){
	var empty = true;
	if(!radio.length)
		return;
	for (var i=0; i<radio.length;i++) {
         if (radio[i].checked) {
	    table = document.getElementById("fieldMappingTable");
	    line = document.getElementById(radio[i].value);
	    table.removeChild(line);
	    //correct id and names
	    childCount = table.getElementsByTagName('tr').length;
	    for (j=0; j < childCount; j++) {
		    table.rows[j].className = 'row'+(j%2);
		    table.rows[j].id = 'mapping'+j;
		    table.rows[j].cells[0].firstChild.name = "MappedField_Server"+j;
		    table.rows[j].cells[0].firstChild.id = "MappedField_Server"+j;
		    table.rows[j].cells[1].firstChild.name = "MappedField_Client"+j;
		    table.rows[j].cells[1].firstChild.id = "MappedField_Client"+j;
		    table.rows[j].cells[2].firstChild.name = "MappedField_Binary"+j;
		    table.rows[j].cells[2].firstChild.id = "MappedField_Binary"+j;
		    table.rows[j].cells[3].firstChild.name = "MappedField_Selected";
		    table.rows[j].cells[3].firstChild.id = "MappedField_Selected"+j;
		    table.rows[j].cells[3].firstChild.value = "mapping"+j;
	    }
	    
	    empty = false
         }
        }
	if(empty){
	   alert("You must select a line first");
	}
}

function addMappingField(serverField, clientField){
	
	table = document.getElementById("fieldMappingTable");
	childCount = table.getElementsByTagName('tr').length;
	newLine = table.rows[0].cloneNode(true);
	newLine.className = 'row'+(childCount%2);
	newLine.id = 'mapping'+childCount;
	table.appendChild(newLine);
	
	//server field
	newLine.cells[0].firstChild.name = "MappedField_Server"+childCount;
	newLine.cells[0].firstChild.id = "MappedField_Server"+childCount;
	newLine.cells[0].firstChild.value = serverField;
	
	//client field
	newLine.cells[1].firstChild.name = "MappedField_Client"+childCount;
	newLine.cells[1].firstChild.id = "MappedField_Client"+childCount;
	newLine.cells[1].firstChild.value = clientField;
	
	//binary field checkBox
	newLine.cells[2].firstChild.name = "MappedField_Binary"+childCount;
	newLine.cells[2].firstChild.id = "MappedField_Binary"+childCount;
	newLine.cells[2].firstChild.value = false;

	//Selected radio
	newLine.cells[3].firstChild.name = "MappedField_Selected";
	newLine.cells[3].firstChild.id = "MappedField_Selected"+childCount;
	newLine.cells[3].firstChild.value = "mapping"+childCount;
}

//control state of radio at first load

</script>
<fieldset class="adminform"><legend><?php echo JText::_( 'MAPPING FIELDS' );?></legend>
<table class="adminlist" width="50">
<thead>
	<tr>
		<th colspan="4"><?php echo JText::_( 'SERVER'); ?></th>
		<th colspan="4"><?php echo JText::_( 'CLIENT'); ?></th>
		<th colspan="4"><?php echo JText::_( 'BINARY DATA'); ?></th>
		<th colspan="4"><?php echo JText::_( 'SEL'); ?></th>
	</tr>
	</thead>
	<tbody id="fieldMappingTable" >
	<?php
	  function isBinary($xml, $field){
	  	 $result = false;
	  	 $fieldBinary = $xml->xpath('//void[@property="binaryFields"]');
	  	 $binary = $fieldBinary[0]->object[0];
	  	 foreach ($binary as $node) {
	  		 if(strcmp($node->string[0],$field)==0)
	  			 $result = true;
	  	 }
	  	return $result;
	  }
	  
	  $fieldMappings = $xml->xpath('//void[@property="fieldMapping"]');
	  $fieldBinary = $xml->xpath('//void[@property="binaryFields"]');
	  $mappings = $fieldMappings[0]->object[0];
	  $binary = $fieldBinary[0]->object[0];
	  $i = 0;
	  foreach ($mappings as $node) {
	?>
	
	<tr id='mapping<?php echo $i;?>' class="row<?php echo $i%2;?>">
		<td colspan="4"><input type='text' name='MappedField_Server<?php echo $i;?>' id='MappedField_Server<?php echo $i;?>' value='<?php echo $node->string[0] ?>'></td>
		<td colspan="4"><input type='text' name='MappedField_Client<?php echo $i;?>' id='MappedField_Client<?php echo $i;?>' value='<?php echo $node->string[1] ?>'></td>
		<td align="center" colspan="4"><input type='checkbox' name='MappedField_Binary<?php echo $i;?>' id='MappedField_Binary<?php echo $i;?>' value='<?php echo $node->string[0] ?>' <?php if(isBinary($xml, $node->string[0]))echo 'CHECKED'; ?>></td>
		<td align="center" colspan="4"><input type='radio' name='MappedField_Selected' id='MappedField_Selected<?php echo $i;?>' value='mapping<?php echo $i;?>'></td>
	</tr>
		
	<?php $i++;} ?>
	</tbody>
</table>
</fieldset>
</td>
<td width="70%">
<table>
	<!-- Insert new Mapping field -->
	<tr>
	<td colspan="4"><input type="button" name="insertMappingField" value="New" onclick="childCount = document.getElementById('fieldMappingTable').getElementsByTagName('tr').length; javascript:addMappingField('server field'+childCount, 'client field'+childCount);"></td>
	</tr>
	<!-- Remove existing Mapped field -->
	<tr>
		<td colspan="4"><input type="button" name="removeMappedField" value="Remove" onclick="javascript:removeMappingField(this.form.MappedField_Selected);"></td>
	</tr>
</table>
</td>
</tr>
</table>
</fieldset>


<script>

function loadDbData(){
	var url='loadDataFromDB.easysdi.html.php?option=com_content&task=wizard';
	window.open(url, 700, 500, null);
}

</script>
</form>
<?php
//end edit config
}

//Function that display the config list, called when component "proxy" is clicked
function showConfigList($fnblDB){
		global $mainframe;
		JToolBarHelper::title( JText::_(  'SHOW SYNCML JOB LIST' ), 'generic.png' );
		jimport("joomla.html.pagination");
		$limitstart = JRequest::getVar('limitstart',0);
		$limit = JRequest::getVar('limit',$mainframe->getCfg('list_limit'));
		$search = JRequest::getVar('search','');

		?>
<!-- This form contains the entire config page -->
<form name='adminForm' action='index.php' method='GET'><input
	type='hidden' name='option'
	value='<?php echo JRequest::getVar('option') ;?>'> <input type='hidden'
	name='task' value='<?php echo JRequest::getVar('task') ;?>'> <input type='hidden' name='boxchecked'
	value='1'>

<!-- Put here all configs registered in Funambol DB -->
<table class="adminlist">
	<thead>
		<tr>
			<th width="2%" class='title'><?php echo JText::_( 'NUM' ); ?></th>
			<th width="2%" class='title'></th>
			<th class='title'><b><?php echo JText::_( 'JOB ID'); ?></b></th>
		</tr>
	</thead>
	<tbody>
	<?php
	$fnblDB->connect();
	$jobList = $fnblDB->getConfigList();
	$i=0;
	while (!$jobList->EOF) 
	{ ?> 
		<tr class="row<?php echo $i%2; ?>">
			<td><?php echo $i+1;?></td>
			<td><input type="radio" name="configId" value=<?php echo "\"".$jobList->fields[0]."\""; ?>></td>
			<td><b><?php echo $jobList->fields[0];?></b></td>
			</tr>
		</td>
		<?php	
		$i++;
		$jobList->MoveNext();
	} 
	?>

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