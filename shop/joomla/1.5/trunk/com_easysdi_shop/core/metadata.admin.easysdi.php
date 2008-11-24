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
	
	function listMetadataTabs($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_tabs ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_tabs order by id";
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
			
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listMetadataTabs($use_pagination,$rows,$pageNav,$option);		
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
	
	function listStandardClasses($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
				
		if ($type == ''){
			
			$query  = "SELECT id AS value FROM #__easysdi_metadata_standard";
			$db->setQuery( $query ,1,1);
			 $type = $db->loadResult();
		}
		$query = "select count(*) from  #__easysdi_metadata_standard_classes where standard_id = $type ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select a.id as id, b.name as standard_name , c.name as class_name ,a.position from  #__easysdi_metadata_standard_classes a ,#__easysdi_metadata_standard b,#__easysdi_metadata_classes c  where b.is_deleted =0 AND b.id=a.standard_id and c.id = a.class_id and standard_id = $type order by standard_name,position";
		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);	
		}else{
			$db->setQuery( $query);
		}	
		
	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {						
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");								
		}		

		HTML_metadata::listStandardClasses($use_pagination,$rows,$pageNav,$option,$type);
		
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
	
	
	
function listClass($option){
		
		global  $mainframe;
		$db =& JFactory::getDBO();
		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);		
		
		
		$query = "select count(*) from  #__easysdi_metadata_classes ";								
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "select * from  #__easysdi_metadata_classes ";
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

		HTML_metadata::listClass($use_pagination,$rows,$pageNav,$option);
		
	}
	
	
	function saveMDClass($option){
		global  $mainframe;
		$database=& JFactory::getDBO(); 
		
		$rowMDClasses =&	 new MDClasses($database);
				
		
		if (!$rowMDClasses->bind( $_POST )) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
			exit();
		}				
		if (!$rowMDClasses->store()) {			
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );
			exit();
		}

		
		//delete the links  
		$query = "DELETE FROM  #__easysdi_metadata_classes_classes WHERE classes_from_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
				}
			$query = "DELETE FROM  #__easysdi_metadata_classes_freetext WHERE classes_id = ".$rowMDClasses->id;
			$database->setQuery( $query );
			if (!$database->query()) {		
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listMetadataClass" );	
				exit();		
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
		
		$rowMDList =&	 new MDListContent($database);
				
		
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
		
		$rowMDFreetext =&	 new MDFreetext($database);
				
		
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
	
	
}

?>