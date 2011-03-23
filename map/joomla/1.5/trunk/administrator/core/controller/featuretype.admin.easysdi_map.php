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
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchFeatureType{$option}", 'searchFeatureType', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(id) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_featuretype ";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_featuretype ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "name" && $filter_order <> "description" )
		{
			$filter_order		= "id";
			$filter_order_Dir	= "ASC";
		}
		$orderby 	= ' order by '. $filter_order .' '. $filter_order_Dir;
		$query .= $orderby;
		
		$db->setQuery( $query ,$pageNav->limitstart, $pageNav->limit);	
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_featuretype::listFeatureType( $rows, $pageNav,$search, $filter_order_Dir, $filter_order, $option);
	}
	
	function editFeatureType ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$feature_type = new featureType ($db);
		$feature_type->load($id);
		
		$feature_type->tryCheckOut($option,'featureType');
		
		//Get user name
		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($feature_type->created)
		{ 
			if ($feature_type->createdby and $feature_type->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$feature_type->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($feature_type->updated and $feature_type->updated<> '0000-00-00 00:00:00')
		{ 
			if ($feature_type->updatedby and $feature_type->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$feature_type->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		//Get availaible feature type use
		$db->setQuery( "SELECT id as value, translation as text FROM #__sdi_usage" );
		$rowsUses = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		//Get current feature type use
		$db->setQuery( "SELECT id as value FROM #__sdi_usage WHERE id IN (SELECT usage_id FROM #__sdi_featuretype_usage WHERE ft_id=$id)" );
		$rowsSelectedUses = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}

		//Get defined attributes of the feature type
		$db->setQuery( "SELECT * FROM #__sdi_featuretypeattribute WHERE ft_id=".$id );
		$rowsAttributes = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		//Get selected profiles for each attributes
		$rowsAttributeProfiles = array();		
		foreach ($rowsAttributes as $attr)
		{
			$db->setQuery( "SELECT profile_id as value FROM #__sdi_ftatt_profile WHERE ftatt_id= ".$attr->id );
			$rowsAttrProf = $db->loadObjectList();
			if ($db->getErrorNum()) 
			{
				$mainframe->enqueueMessage($db->stderr(),"error");
				return ;
			}
			$rowsAttributeProfiles [$attr->id] = $rowsAttrProf;			
		}
		
		//Get availaible profile
		$db->setQuery( "SELECT id as value, code as text FROM #__sdi_accountprofile" );
		$rowsProfiles = $db->loadObjectList();
		if ($db->getErrorNum()) 
		{
			$mainframe->enqueueMessage($db->stderr(),"error");
			return ;
		}
		
		HTML_featuretype::editFeatureType($feature_type, $rowsUses,$rowsSelectedUses,$rowsAttributes,$rowsProfiles,$rowsAttributeProfiles,$createUser,$updateUser,$feature_type->getFieldsLength(),$option);
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
			$featureType = new featureType ($db);
			$featureType->load($feature_type_id);
				
			if (!$featureType->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=featureType" );
			}				
		}	
		$mainframe->redirect("index.php?option=$option&task=featureType");
	}
	
	function saveFeatureType($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO();

		$featureType = new featureType ($db);
		if (!$featureType->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=featureType" );
			exit();
		}		
				
		if (!$featureType->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=featureType" );
			exit();
		}
		
		if(isset($_POST['uses_id']))
		{
			$db->setQuery( "DELETE FROM #__sdi_featuretype_usage WHERE ft_id=".$featureType->id );
			if (!$db->query()) 
			{
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			}
			if (count ($_POST['uses_id'] )>0)
			{
				foreach( $_POST['uses_id'] as $use_id )
				{
					$db->setQuery( "INSERT INTO #__sdi_featuretype_usage (ft_id, usage_id) VALUES (".$featureType->id.",".$use_id.")" );
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
				$attribute->ft_id = $featureType->id;
				//$attribute->name = JRequest::getVar('NAME_'.$i);
				$attribute->name = $_POST['NAME_'.$i];
				$attribute->datatype = JRequest::getVar('DATATYPE_'.$i);
				$attribute->width = JRequest::getVar('WIDTH_'.$i);
				$attribute->initialvisibility = JRequest::getVar('VISIBILITY_'.$i, 0);
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
							$db->setQuery( "INSERT INTO #__sdi_ftatt_profile (ftatt_id, profile_id) VALUES (".$attribute->id.",".$profile_id.")" );
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
	
		if (JRequest::getVar('toRemoveAttrList'))
		{
				$db->setQuery( "DELETE FROM #__sdi_featuretypeattribute WHERE ft_id =".$featureType->id." AND id IN (".JRequest::getVar('toRemoveAttrList').")" );
				if (!$db->query()) 
				{
					$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				}
		}
		
		$featureType->checkin();
		$mainframe->redirect("index.php?option=$option&task=featureType");
	}

	function cancelFeatureType($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$featureType = new featureType ($db);
		$featureType->bind(JRequest::get('post'));
		$featureType->checkin();

		$mainframe->redirect("index.php?option=$option&task=featureType" );
	}
}
?>