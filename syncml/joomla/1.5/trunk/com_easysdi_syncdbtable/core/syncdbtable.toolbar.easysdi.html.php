<?php
defined('_JEXEC') or die('Restricted access');
class TOOLBAR_easysdi{
	
	function editComponentConfig(){
		JToolBarHelper::custom('saveComponentConfig','save.png','save.png',JText::_( 'SAVE' ),false);		
		JToolBarHelper::cancel('cancelComponentConfig');					
	}
	
	function editConfig(){
		JToolBarHelper::custom('saveConfig','save.png','save.png',JText::_( 'SAVE' ),false);		
		JToolBarHelper::cancel();					
	}
	
	function editPolicy(){	
		
		JToolBarHelper::custom('savePolicy','save.png','save.png',JText::_( 'SAVE POLICY' ),false);
		JToolBarHelper::cancel('cancelPolicy');
	}
	
	function editPolicyList(){
		JToolBarHelper::custom('editPolicy','edit.png','edit.png',JText::_( 'EDIT POLICY' ),false);
		JToolBarHelper::custom('addPolicy','new.png','new.png',JText::_( 'CREATE A POLICY' ),false);
		JToolBarHelper::custom('copyPolicy','copy.png','copy.png',JText::_( 'COPY A POLICY' ),false);
		JToolBarHelper::custom('deletePolicy','delete.png','delete.png',JText::_( 'DELETE POLICY' ),false);		
		JToolBarHelper::cancel();
	}
	
	
	function _NEW(){
				JToolBarHelper::save();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();		
		JToolBarHelper::deleteList();
	}
	
	function configList(){
		
		JToolBarHelper::custom('editConfig','edit.png','edit.png',JText::_( 'EDIT CONFIG' ),false);
		//JToolBarHelper::custom('copyConfig','copy.png','copy.png',JText::_( 'COPY A CONFIG' ),false);
		JToolBarHelper::custom('addConfig','new.png','new.png',JText::_( 'NEW CONFIG' ),false);
		JToolBarHelper::custom('deleteConfig','delete.png','delete.png',JText::_( 'DELETE CONFIG' ),false);
		
		JToolBarHelper::cancel('cancelConfigList');		
	}
	
}
?>