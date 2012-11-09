<?php
/**
 * @version     3.0.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit
 */
class Easysdi_serviceViewVirtualservice extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}
		
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
		$this->id 			= JRequest::getVar('id',null);
		
		if(!isset($layout)){
			if(isset($cid)){
				foreach ($cid as $id ){
					$layout = "wms";
				}
			}
		}
		
		$db 			= JFactory::getDBO();
		$db->setQuery("SELECT 0 AS id, '- Please select -' AS value UNION SELECT id, value FROM #__sdi_sys_serviceconnector WHERE state = 1") ;
		$this->serviceconnectorlist = $db->loadObjectList();
		
		$db->setQuery("SELECT 0 AS alias, '- Please select -' AS value UNION SELECT s.alias as alias,CONCAT(s.alias, ' - ', s.resourceurl,' - [',GROUP_CONCAT(syv.value SEPARATOR '-'),']') as value FROM #__sdi_physicalservice s
				INNER JOIN #__sdi_physicalservice_servicecompliance sc ON sc.physicalservice_id = s.id
				INNER JOIN #__sdi_sys_servicecompliance syc ON syc.id = sc.servicecompliance_id
				INNER JOIN #__sdi_sys_serviceversion syv ON syv.id = syc.serviceversion_id
				INNER JOIN #__sdi_sys_serviceconnector sycc ON sycc.id = syc.serviceconnector_id
				WHERE sycc.value = '".JRequest::getVar('layout',null)."'
				AND s.state= 1
				GROUP BY s.id") ;
		$this->servicelist = $db->loadObjectList();
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
		$canDo		= Easysdi_serviceHelper::getActions();

		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_VIRTUALSERVICE'), 'virtualservice.png');
		
		if(JRequest::getVar('layout',null)!='CSW' &&  $canDo->get('core.edit'))
			JToolBarHelper::addNew('virtualservice.addserver',JText::_( 'COM_EASYSDI_SERVICE_NEW_SERVER'));
		
		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
		{

			JToolBarHelper::apply('virtualservice.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('virtualservice.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($canDo->get('core.create'))){
			JToolBarHelper::custom('virtualservice.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('virtualservice.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id)) {
			JToolBarHelper::cancel('virtualservice.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('virtualservice.cancel', 'JTOOLBAR_CLOSE');
		}

	}
}
