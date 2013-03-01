<?php
/**
 * @version     3.3.0
 * @package     com_easysdi_service
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
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
		JHtml::_('bootstrap.framework');
		
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
		
		function updateAgregatedVersion ()
		{
			var supportedVersionsArray ;
			
			jQuery('#div-supportedversions').html("");
			jQuery('#jform_compliance').val("");
			jQuery('#jform_physicalservice_id :selected').each(function(i, selected){ 
				var selected = jQuery(selected).text();
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
				 
			});
			
			jQuery('#jform_compliance').val(JSON.stringify(supportedVersionsArray));

			if(supportedVersionsArray && supportedVersionsArray.length > 0)
			{
				jQuery('#div-supportedversions').html(createSupportedVersionLabel(supportedVersionsArray)) ;
			}
		}

		function contains(arr, findValue) {
		    var i = arr.length;
		     
		    while (i--) {
		        if (arr[i] === findValue) return true;
		    }
		    return false;
		}

		function createSupportedVersionLabel(versions){
			var html = '';
			for( var i = 0 ; i < versions.length ; i++ ){
				html += '<span class="label label-info">';
				html += versions[i];
				html += '</span>';
			}
			return html;
		}
		</script>
		
		<?php
		
		$params 			= JComponentHelper::getParams('com_easysdi_service');
		$this->id 			= JRequest::getVar('id',null);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
		
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
		$this->canDo		= Easysdi_serviceHelper::getActions();

		JToolBarHelper::title(JText::_('COM_EASYSDI_SERVICE_TITLE_VIRTUALSERVICE'), 'virtualservice.png');
		
		if(JRequest::getVar('layout',null)!='CSW' &&  $this->canDo->get('core.edit'))
			JToolBarHelper::addNew('virtualservice.addserver',JText::_( 'COM_EASYSDI_SERVICE_NEW_SERVER'));
		
		// If not checked out, can save the item.
		if (!$checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create'))))
		{

			JToolBarHelper::apply('virtualservice.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('virtualservice.save', 'JTOOLBAR_SAVE');
		}
		if (!$checkedOut && ($this->canDo->get('core.create'))){
			JToolBarHelper::custom('virtualservice.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
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
