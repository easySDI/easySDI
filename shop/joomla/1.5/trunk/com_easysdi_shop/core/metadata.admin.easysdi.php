<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d’Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class ADMIN_metadata {

	function deleteMetadataClass($cid,$option){
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");			
			exit;
		}
		foreach( $cid as $id )
		{
			$rowMDClasses =&	 new MDClasses($database);
			$rowMDClasses->load( $id );
					
			if (!$rowMDClasses->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				exit;
			}
			
			
			//delete the links  
		
		$query = "DELETE FROM  #__easysdi_metadata_classes_classes WHERE classes_from_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				exit();		
				}
			$query = "DELETE FROM  #__easysdi_metadata_classes_freetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				exit();		
				}	
			$query = "DELETE FROM  #__easysdi_metadata_classes_ext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				exit();		
				}	
	
			$query = "DELETE FROM  #__easysdi_metadata_classes_locfreetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				exit();		
				}	
		$query = "DELETE FROM  #__easysdi_metadata_classes_list WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				exit();		
				}	
		
				
				
			
		}
		
	}
		function deleteMetadataList($cid,$option){
			
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$mdList = new MDList( $database );
			$mdList->load( $id );
					
			if (!$mdList->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			}
			
			$query ="delete from #__easysdi_metadata_list_content where list_id = $id";
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataList" );	
				exit();		
				}
			
		
		}
			
			
		}
		
		
function deleteMetadataListContent($cid,$option){
			
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit;
		}
		
		foreach( $cid as $id )
		{
			$mdList = new MDListContent( $database );
			$mdList->load( $id );
					
			if (!$mdList->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			}
			
			
		
		}
			
			
		}
	function deleteMDTabs($cid,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			exit;
		}
		foreach( $cid as $id )
		{
			$mdTabs = new MDTabs( $database );
			$mdTabs->load( $id );
					
			if (!$mdTabs->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			}														
		
		}

		
	}
	
	
	function saveMDTabs($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
							
		$row =&	 new MDTabs($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			exit();
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
			exit();
		}	
	}
	
	
	function editMDTabs($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row = new MDTabs( $db );
		
		$row->load( $id );					
	
		
		HTML_metadata::editMetadataTabs($row,$id, $option );
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function deleteMDStandard($cid,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit;
		}
		foreach( $cid as $id )
		{
			
			$query = "UPDATE  #__easysdi_metadata_standard SET is_deleted= 1  WHERE id = $id ";
			$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				} 														
		
		}		
	}
	
	
	
	
	
	
	
	function deleteMDStandardClasses($cid,$option){
		
		global $mainframe;
		$database =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit;
		}
		foreach( $cid as $id )
		{
			$standardClasses = new MDStandardClasses( $database );
			$standardClasses->load( $id );
					
			if (!$standardClasses->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			}														
		
		}

		
	}
	
	
	function saveMDStandardClasses($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
							
		$row =&	 new MDStandardClasses($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit();
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
			exit();
		}	
	}
	
	
	function editStandardClasses($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row = new MDStandardClasses( $db );
		
		$row->load( $id );					
	
		
		HTML_metadata::editStandardClasses($row,$id, $option );
		
	}
	
	
	function saveMDStandard($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$row =&	 new MDStandard($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
			exit();
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandard" );
			exit();
		}
	}
	
	
	function editStandard($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row = new MDStandard( $db );
		
		$row->load( $id );					
	
		
		
		HTML_metadata::editStandard($row,$id, $option );
		
	}
	
	function listStandard($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_standard WHERE is_deleted =0 ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_standard WHERE is_deleted =0 ";
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listStandard($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	
	
	
	
	
	
	function listExt($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_ext ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_ext ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
					
		}		

		HTML_metadata::listExt($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	
	function editExt($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDExt = new MDExt( $db );
		
		$rowMDExt->load( $id );					
	
		
		
		HTML_metadata::editExt($rowMDExt,$id, $option );
		
	}
	function saveMDExt($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$row =&	 new MDExt($database);
				
		
		if (!$row->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataExt" );
			exit();
		}				
	if (!$row->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataExt" );
			exit();
		}
	}
	
	
	function saveMDLocfreetext($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDFreetext =&	 new MDLocFreetext($database);
				
		
		if (!$rowMDFreetext->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
			exit();
		}				
	if (!$rowMDFreetext->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataLocfreetext" );
			exit();
		}
	}
	
	
	function editLocfreetext($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDFreetext = new MDLocFreetext( $db );
		
		$rowMDFreetext->load( $id );					
	
		
		
		HTML_metadata::editLocfreetext($rowMDFreetext,$id, $option );
		
	}
	
	function listLocfreetext($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_loc_freetext ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_loc_freetext ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
					
		}		

		HTML_metadata::listLocfreetext($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	
	
	function saveMDClass($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDClasses =&	 new MDClasses($database);
				
		
		if (!$rowMDClasses->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			
			return;
		}				
		if (!$rowMDClasses->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			
			return;
		}

		
		//delete the links  
		
		$query = "DELETE FROM  #__easysdi_metadata_classes_classes WHERE classes_from_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				return ;		
				}
			$query = "DELETE FROM  #__easysdi_metadata_classes_freetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					
				return;		
				}	
			$query = "DELETE FROM  #__easysdi_metadata_classes_ext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}	
	
			$query = "DELETE FROM  #__easysdi_metadata_classes_locfreetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}	
		$query = "DELETE FROM  #__easysdi_metadata_classes_list WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}	
		
						
		if ($_POST[type]=='class'){
			
			foreach( $_POST['class'] as $class_id ) {
				
				$query = "INSERT INTO #__easysdi_metadata_classes_classes VALUES (0,".$rowMDClasses->id.",".$class_id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}

		
		if ($_POST[type]=='list'){
			
			foreach( $_POST['list'] as $class_id ) {
				
				$query = "INSERT INTO #__easysdi_metadata_classes_list VALUES (0,".$rowMDClasses->id.",".$class_id.")";
			
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}
		
		if ($_POST[type]=='ext'){
			foreach( $_POST['ext'] as $id ) {
				$query = "INSERT INTO #__easysdi_metadata_classes_ext VALUES (0,".$rowMDClasses->id.",".$id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}			
		
		if ($_POST[type]=='freetext'){
			foreach( $_POST['freetext'] as $id ) {
				$query = "INSERT INTO #__easysdi_metadata_classes_freetext VALUES (0,".$rowMDClasses->id.",".$id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}			
		if ($_POST[type]=='locfreetext'){
			foreach( $_POST['locfreetext'] as $id ) {
				$query = "INSERT INTO #__easysdi_metadata_classes_locfreetext VALUES (0,".$rowMDClasses->id.",".$id.")";
				
				$database->setQuery( $query );
				if (!$database->query()) {
					$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
					$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
					exit();			
				}
			}							
		}			
	
	
	
		
	}
	
	function editClass($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDClasses = new MDClasses( $db );
		$rowMDClasses->load( $id );					
	
		
		
		HTML_metadata::editClass($rowMDClasses,$id, $option );
		
	}
	
	
	function saveMDListContent($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDList =& new MDListContent($database);
				
		if (!$rowMDList->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataListContent" );
			exit();
		}				
	if (!$rowMDList->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataListContent" );
			exit();
		}
	}

	
	
	function saveMDList($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDList =&	 new MDList($database);
				
		
		if (!$rowMDList->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit();
		}				
	if (!$rowMDList->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataList" );
			exit();
		}
	}
	
	function saveMDFreetext($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDFreetext =& new MDFreetext($database);
				
		
		if (!$rowMDFreetext->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
			exit();
		}				
		if (!$rowMDFreetext->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataFreetext" );
			exit();
		}
	}

	function editNumerics($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$row= new MDNumeric( $db );
		$row->load( $id );					
	
		
		
		HTML_metadata::editNumerics($row,$id, $option );
		
	}
	
	
	function editFreetext($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDFreetext = new MDFreetext( $db );
		$rowMDFreetext->load( $id );					
	
		
		
		HTML_metadata::editFreetext($rowMDFreetext,$id, $option );
		
	}
	
	
	
	//if id = 0, create a new Entry
	function editList($id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDList = new MDList( $db );
		$rowMDList->load( $id );					
	
		
		
		HTML_metadata::editList($rowMDList,$id, $option );
		
	}
	
	function editListContent($id,$option,$list_id){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$rowMDList = new MDListContent( $db );
		$rowMDList->load( $id );					
	
		
		HTML_metadata::editListContent($rowMDList,$id, $option,$list_id );
		
	}
	function listNumerics($option){
			global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_numeric ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_numeric ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listNumerics($use_pagination,$rows,$pageNav,$option);
		
	
} 	
	function listFreetext($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_freetext ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_freetext ";
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listFreetext($use_pagination,$rows,$pageNav,$option);
		
	}
	
	function listDate($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_date ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_date ";
	if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listDate($use_pagination,$rows,$pageNav,$option);
		
	}
	
	function listList($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_list ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_list ";
if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listList($use_pagination,$rows,$pageNav,$option);
		
	}
	
	function listListContent($list_id,$option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_list_content where list_id = $list_id ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_list_content where list_id = $list_id ";
if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listListContent($use_pagination,$rows,$pageNav,$option,$list_id);
		
	}
	
	function goDownMetadataClass($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_metadata_classes  where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
						
			$query = "select * from  #__easysdi_metadata_classes  where ordering > $row1->ordering   order by ordering ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_metadata_classes set ordering= $row1->ordering where id =$row2->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_metadata_classes set ordering= $row2->ordering where id =$row1->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
	}
	function goUpMetadataClass($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_metadata_classes  where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_metadata_classes  where ordering < $row1->ordering  order by ordering desc";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_metadata_classes set ordering= $row1->ordering where id =$row2->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_metadata_classes set ordering= $row2->ordering where id =$row1->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}	
			$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );				
	}
	
	function listClass($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		$search				= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
		$search				= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' and LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(type) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(iso_key) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			//$where .= ' or LOWER(text) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		$query = "select count(*) from  #__easysdi_metadata_classes ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "user_name" and $filter_order<>"class_name" and $filter_order <> "type" and $filter_order <> "iso_key" and $filter_order <> "text" and $filter_order <> "ordering" and $filter_order <> "id")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		$query = "select c.*, c.name as class_name, u.name AS user_name from  #__easysdi_metadata_classes c left outer join #__easysdi_community_partner p on p.partner_id=c.partner_id left outer join #__users u on u.id=p.user_id ";
		$query .= $where;
		$query .= $orderby;
		
		if ($use_pagination) {
		$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}		

		HTML_metadata::listClass($use_pagination,$rows,$pageNav,$option, $filter_order, $filter_order_Dir, $search);
		
	}
	
	function saveOrderMetadataClass($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "select count(*) from  #__easysdi_metadata_classes ";								
		$db->setQuery( $query );
		$total = $db->loadResult();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$rowMDClass =& new MDClasses( $db );
		
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}
		
		$order = $_POST[order];
		
		// update ordering values
		
		for ($i = 0; $i < $total; $i++)
		{
			$rowMDClass->load($cid[$i]);
			
			if ($rowMDClass->ordering != $order[$i])
			{
				$rowMDClass->ordering = $order[$i];
				if (!$rowMDClass->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
	}
	
	
	
	function goDownMetadataStandardClasses($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		
			if (strlen($type)==0){
				
				$type = JRequest::getVar('type','');
			}
			
			if (strlen($type)==0){			
				$query  = "SELECT id AS value FROM #__easysdi_metadata_standard";
				$db->setQuery( $query ,0,1);
				 $type = $db->loadResult();
			
			}
			
			$query = "select count(*) from  #__easysdi_metadata_standard_classes where standard_id = $type ";								
			$db->setQuery( $query );
			$total = $db->loadResult();
			
			
			$query = "select * from  #__easysdi_metadata_standard_classes  where id=$cid[0] and standard_id = $type ";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
							
			$query = "select * from  #__easysdi_metadata_standard_classes  where ordering > $row1->ordering  and standard_id = ".$type." order by ordering ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_metadata_standard_classes set ordering= $row1->ordering where id =$row2->id  and standard_id = $type ";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_metadata_standard_classes set ordering= $row2->ordering where id =$row1->id  and standard_id = $type ";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
	}
	function goUpMetadataStandardClasses($cid,$option){

				global  $mainframe;
			$db =& JFactory::getDBO();
			
			$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		
			if (strlen($type)==0){
				
				$type = JRequest::getVar('type','');
			}
			
			if (strlen($type)==0){			
				$query  = "SELECT id AS value FROM #__easysdi_metadata_standard";
				$db->setQuery( $query ,0,1);
				 $type = $db->loadResult();
			
			}
			
			$query = "select count(*) from  #__easysdi_metadata_standard_classes where standard_id = $type ";								
			$db->setQuery( $query );
			$total = $db->loadResult();
			
			
			$query = "select * from  #__easysdi_metadata_standard_classes  where id=$cid[0] and standard_id = $type ";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_metadata_standard_classes  where ordering < $row1->ordering  and standard_id = ".$type." order by ordering desc";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_metadata_standard_classes set ordering= $row1->ordering where id =$row2->id  and standard_id = $type ";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_metadata_standard_classes set ordering= $row2->ordering where id =$row1->id  and standard_id = $type ";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );				
	}
	
	function listStandardClasses($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		
		$search				= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
		$search				= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' and( LOWER(a.id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(b.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(c.name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(a.position) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ')';
		}
		
		if (strlen($type)==0){
			
			$type = JRequest::getVar('type','');
		}
		
		if (strlen($type)==0){			
			$query  = "SELECT id AS value FROM #__easysdi_metadata_standard";
			$db->setQuery( $query ,0,1);
			 $type = $db->loadResult();
		
		}
		$query = "select count(*) from  #__easysdi_metadata_standard_classes where standard_id = $type ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "standard_name" and $filter_order <> "class_name" and $filter_order <> "ordering" and $filter_order <> "position")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		$query = "select a.id as id, b.name as standard_name , c.name as class_name ,a.position, a.ordering as ordering from  #__easysdi_metadata_standard_classes a ,#__easysdi_metadata_standard b,#__easysdi_metadata_classes c  where b.is_deleted =0 AND b.id=a.standard_id and c.id = a.class_id and standard_id = $type";
		$query .= $where;
		$query .= $orderby;
		
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listStandardClasses($use_pagination,$rows,$pageNav,$option,$type, $filter_order, $filter_order_Dir, $search);
		
	}
	
	function saveOrderMetadataStandardClasses($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
			
		if (strlen($type)==0){
			
			$type = JRequest::getVar('type','');
		}
		
		if (strlen($type)==0){			
			$query  = "SELECT id AS value FROM #__easysdi_metadata_standard";
			$db->setQuery( $query ,0,1);
			 $type = $db->loadResult();
		
		}
	
		$query = "select count(*) from  #__easysdi_metadata_standard_classes where standard_id = $type ";							
		$db->setQuery( $query );
		$total = $db->loadResult();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$rowMDStandardClasses =& new MDStandardClasses( $db );
		
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}
		
		$order = $_POST[order];
		
		// update ordering values
		
		for ($i = 0; $i < $total; $i++)
		{
			$rowMDStandardClasses->load($cid[$i]);
			
			if ($rowMDStandardClasses->ordering != $order[$i])
			{
				$rowMDStandardClasses->ordering = $order[$i];
				if (!$rowMDStandardClasses->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listMetadataStandardClasses" );
	}

	function goDownMetadataTabs($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_metadata_tabs  where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
							
			$query = "select * from  #__easysdi_metadata_tabs  where ordering > $row1->ordering   order by ordering ";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_metadata_tabs set ordering= $row1->ordering where id =$row2->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_metadata_tabs set ordering= $row2->ordering where id =$row1->id";
			$db->setQuery( $query );
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		

			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
	}
	function goUpMetadataTabs($cid,$option){

			global  $mainframe;
			$db =& JFactory::getDBO();
			
			$query = "select * from  #__easysdi_metadata_tabs where id=$cid[0]";
			$db->setQuery( $query );
			
			$row1 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
								
			$query = "select * from  #__easysdi_metadata_tabs  where ordering < $row1->ordering  order by ordering desc";
			$db->setQuery( $query );
			$row2 = $db->loadObject() ;
			if ($db->getErrorNum()) {
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			
			$query = "update #__easysdi_metadata_tabs set ordering= $row1->ordering where id =$row2->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}		
			
			$query = "update #__easysdi_metadata_tabs set ordering= $row2->ordering where id =$row1->id";
			$db->setQuery( $query );				
			if (!$db->query()) {		
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
			}	
			$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );				
	}
	
	function listMetadataTabs($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		$search				= $mainframe->getUserStateFromRequest( "$option.search",'search','','string' );
		$search				= JString::strtolower( $search );
		
		$where="";
		if ($search)
		{
			$where = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where .= ' or LOWER(text) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		$query = "select count(*) from  #__easysdi_metadata_tabs ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		
		// Test si le filtre est valide
		if ($filter_order <> "id" and $filter_order <> "text" and $filter_order <> "partner_name" and $filter_order <> "ordering")
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		
		$query = "select t.*, u.name as partner_name from  #__easysdi_metadata_tabs t left outer join #__easysdi_community_partner p on t.partner_id=p.partner_id left outer join #__users u on u.id=p.user_id";
		$query .= $where;
		$query .= $orderby;
		
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
			
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listMetadataTabs($use_pagination,$rows,$pageNav,$option, $filter_order_Dir, $filter_order, $search);		
	}
	
	
	function saveOrderMetadataTabs($cid, $option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO();
		
		$query = "select count(*) from  #__easysdi_metadata_tabs ";								
		$db->setQuery( $query );
		$total = $db->loadResult();

		if (empty( $cid)) {
			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
		}

		$rowMDTabs =& new MDTabs( $db );
		
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");			
			exit();			
		}
		
		$order = $_POST[order];
		
		// update ordering values
		
		for ($i = 0; $i < $total; $i++)
		{
			$rowMDTabs->load($cid[$i]);
			
			if ($rowMDTabs->ordering != $order[$i])
			{
				$rowMDTabs->ordering = $order[$i];
				if (!$rowMDTabs->store()) {
					return JError::raiseError( 500, $db->getErrorMsg() );
				}
			}
		}

		$mainframe->redirect("index.php?option=$option&task=listMetadataTabs" );
	}
}

?>