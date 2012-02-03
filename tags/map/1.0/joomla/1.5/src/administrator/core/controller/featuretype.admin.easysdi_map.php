<?php
/**
 *  EasySDI, a solution to implement easily any spatial data infrastructure
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

defined('_JEXEC') or die('Restricted access');

class ADMIN_featuretype 
{
	function listFeatureType ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',0);
		
		$query ="SELECT COUNT(*) FROM #__easysdi_map_feature_type";
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__easysdi_map_feature_type ";
		$query .= " ORDER BY name";
		if ($use_pagination) 
		{
			$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		}
		else
		{
			$db->setQuery( $query);
		}
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_featuretype::listFeatureType($use_pagination, $rows, $pageNav, $option);
	}
	
	function editFeatureType ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$feature_type = new feature_type ($db);
		$feature_type->load($id);
		
		//Get availaible feature type use
		$db->setQuery( "SELECT id as value, translation as text FROM #__easysdi_map_use" );
		$rowsUses = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		//Get current feature type use
		$db->setQuery( "SELECT id as value FROM #__easysdi_map_use WHERE id IN (SELECT id_use FROM #__easysdi_map_feature_type_use WHERE id_ft=$id)" );
		$rowsSelectedUses = $db->loadObjectList();
		echo $db->getErrorMsg();

		//Get defined attributes of the feature type
		$db->setQuery( "SELECT * FROM #__easysdi_map_attribute WHERE id_ft=".$id );
		$rowsAttributes = $db->loadObjectList();
		echo $db->getErrorMsg();
		//Get selected profiles for each attributes
		$rowsAttributeProfiles = array();		
		foreach ($rowsAttributes as $attr)
		{
			$db->setQuery( "SELECT id_prof as value FROM #__easysdi_map_attribute_profile WHERE id_attr= ".$attr->id );
			$rowsAttrProf = $db->loadObjectList();
			echo $db->getErrorMsg();
			$rowsAttributeProfiles [$attr->id] = $rowsAttrProf;			
		}
		
		
		//Get availaible profile
		$db->setQuery( "SELECT profile_id as value, profile_translation as text FROM #__easysdi_community_profile" );
		$rowsProfiles = $db->loadObjectList();
		echo $db->getErrorMsg();
		
		HTML_featuretype::editFeatureType($feature_type, $rowsUses,$rowsSelectedUses,$rowsAttributes,$rowsProfiles,$rowsAttributeProfiles,$option);
	}
	
	function deleteFeatureType($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=featureType" );
			exit;
		}
		foreach( $cid as $feature_type_id )
		{
			$feature_type = new feature_type ($db);
			$feature_type->load($feature_type_id);
				
			if (!$feature_type->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=featureType" );
			}				
		}	
	}
	
	function saveFeatureType($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO();

		$feature_type = new feature_type ($db);
		if (!$feature_type->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=featureType" );
			exit();
		}		
				
		if (!$feature_type->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=featureType" );
			exit();
		}
		
		if(isset($_POST['uses_id']))
		{
			$db->setQuery( "DELETE FROM #__easysdi_map_feature_type_use WHERE id_ft=".$feature_type->id );
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			if (count ($_POST['uses_id'] )>0)
			{
				foreach( $_POST['uses_id'] as $use_id )
				{
					$db->setQuery( "INSERT INTO #__easysdi_map_feature_type_use (id_ft, id_use) VALUES (".$feature_type->id.",".$use_id.")" );
					if (!$db->query()) 
					{
						$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
					}
				}
			}
		}
		
		
		$i = 0;		
		$iAttributes = JRequest::getVar("iAttribute");
		$count = 0;
		While ($count < $iAttributes)
		{
			if (JRequest::getVar('NAME_'.$i))
			{
			
				$attribute = new attribute($db);
				$attribute->id = JRequest::getVar('ID_'.$i);				
				$attribute->id_ft = $feature_type->id;
				//$attribute->name = JRequest::getVar('NAME_'.$i);
				$attribute->name = $_POST['NAME_'.$i];
				$attribute->data_type = JRequest::getVar('DATATYPE_'.$i);
				$attribute->width = JRequest::getVar('WIDTH_'.$i);
				$attribute->initial_visibility = JRequest::getVar('VISIBILITY_'.$i, 0);
				$attribute->visible = JRequest::getVar('VISIBLE_'.$i, 0);
				if (!$attribute->store()) 
				{			
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
				if(isset($_POST['PROFILE_'.$i]))
				{
					if (count ($_POST['PROFILE_'.$i])>0)
					{					
						foreach( $_POST['PROFILE_'.$i] as $profile_id )
						{
							$db->setQuery( "INSERT INTO #__easysdi_map_attribute_profile (id_attr, id_prof) VALUES (".$attribute->id.",".$profile_id.")" );
							if (!$db->query()) 
							{
								$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
							}
						}
					}			
				}
				$i += 1;
				$count +=1;
			}
			else
			{
				$i += 1;
			}
		}
		/*
		$attrList = explode(',', JRequest::getVar('toRemoveAttrList'));
		if (count ($attrList) > 0)
		{	
			for ($i = 0; $i < count($attrList); $i++)
			{
				$db->setQuery( "DELETE FROM #__easysdi_map_attribute WHERE id_ft =".$feature_type->id." AND id =".$attrList[$i] );
				if (!$db->query()) 
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
			}
		}
		*/
		if (JRequest::getVar('toRemoveAttrList'))
		{
				$db->setQuery( "DELETE FROM #__easysdi_map_attribute WHERE id_ft =".$feature_type->id." AND id IN (".JRequest::getVar('toRemoveAttrList').")" );
				if (!$db->query()) 
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
		}
	}

}
?>