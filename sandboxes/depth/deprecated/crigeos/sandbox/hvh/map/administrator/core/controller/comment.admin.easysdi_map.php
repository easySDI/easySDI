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

class ADMIN_comment 
{
	function listComment ($option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );
		
		//Search
		$search = $mainframe->getUserStateFromRequest( "searchComment{$option}", 'searchComment', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );
		if ($search)
		{
			$query_search = ' where LOWER(featuretypename) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(countattribute) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(description) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(name) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$query_search .= ' or LOWER(enable) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
			
		//Base query
		$query ="SELECT COUNT(*) FROM #__sdi_commentfeaturetype";
		$query .= $query_search;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);
		
		$query = "SELECT *  FROM #__sdi_commentfeaturetype ";
		$query .= $query_search;
		
		// table ordering
		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'id',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'ASC',		'word' );
		if ($filter_order <> "featuretypename" && $filter_order <> "countattribute" && $filter_order <> "name"  && $filter_order <> "description")
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
		
		HTML_comment::listComment($rows, $pageNav, $search, $filter_order_Dir, $filter_order,$option);
	}
	
	function editComment ($id,$option)
	{
		global  $mainframe;
		$db =& JFactory::getDBO(); 
		
		$comment = new comment ($db);
		$comment->load($id);
		
		$comment->tryCheckOut($option,'comment');

		$user =& JFactory::getUser();
		$createUser="";
		$updateUser="";
		if ($comment->created)
		{ 
			if ($comment->createdby and $comment->createdby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$comment->createdby.")" ;
				$db->setQuery($query);
				$createUser = $db->loadResult();
			}
			else
				$createUser = "";
					
		}
		if ($comment->updated and $comment->updated<> '0000-00-00 00:00:00')
		{ 
			if ($comment->updatedby and $comment->updatedby<> 0)
			{
				$query = "SELECT name FROM #__users WHERE id=(SELECT user_id FROM #__sdi_account WHERE id =".$comment->updatedby.")" ;
				$db->setQuery($query);
				$updateUser = $db->loadResult();
			}
			else
				$updateUser = "";
		}
		
		HTML_comment::editComment($comment,$createUser, $updateUser,$comment->getFieldsLength(),  $option);
	}
	
	function deleteComment($cid,$option)
	{
		global $mainframe;
		$db =& JFactory::getDBO();
		
		if (!is_array( $cid ) || count( $cid ) < 1) 
		{
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=mapConfig" );
			exit;
		}
		foreach( $cid as $comment_id )
		{
			$comment = new comment ($db);
			$comment->load($comment_id);
				
			if (!$comment->delete()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=comment" );
			}				
		}	
		$mainframe->redirect("index.php?option=$option&task=comment");
	}
	
	function saveComment($option)
	{
		global $mainframe;
		$db=& JFactory::getDBO(); 
			
		$comment =& new comment($db);
		if (!$comment->bind( $_POST )) 
		{
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");						
			$mainframe->redirect("index.php?option=$option&task=comment" );
			exit();
		}		
		if($comment->enable == 1)
		{
			/** Disable all other comment feature type*/
			$db->setQuery( "UPDATE #__sdi_commentfeaturetype SET enable='0'");
			if (!$db->query()) {
				$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=comment" );				
			}
		}
		if (!$comment->store()) 
		{			
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=comment" );
			exit();
		}
		
		$comment->checkin();
		$mainframe->redirect("index.php?option=$option&task=comment");
	}
	
	function cancelComment($option)
	{
		global $mainframe;
		$db = & JFactory::getDBO();
		$comment =& new comment($db);
		$comment->bind(JRequest::get('post'));
		$comment->checkin();

		$mainframe->redirect("index.php?option=$option&task=comment" );
	}

}
?>