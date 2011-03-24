<?php

// ensure a valid entry point
//defined(_JEXEC) or die('Restricted Access');

jimport('joomla.application.component.model');

/**
 * Map_filter model.
 */
 class EasySDI_mapModelMap_filter extends JModel {
 
  /** 
  * Map filter ID
  *
  * var int
  */
  var $_id;
  
  /** 
  * Requesting user's id, to filter list against
  *
  * var int
  */
  var $_user_id;
  
  /**
  * Map filter object
  *
  * @var object
  */
  var $_map_filter;
  
  /**
   * Constructor. Builds the object.
   */
  function __construct()
  {
  	parent::__construct();
  	
  	$id = JRequest::getInt('id', 0);
  	
  	$this->setId($id);
  }
  
  /**
   * Reset the id and data.
   */
  function setId($id=0) 
  {
    $this->_id = $id;
    $this->_map_filter=null;	
  }
  
  /**
   * Reset the user_id and data.
   */
  function setUserId($user_id) 
  {
    $this->_user_id = $user_id;    
  }
  
  /**
   * Retrieve a map_filter (as a single item in an array for compatibility with list methods).
   */
  function getItem()
  {
  	if ($this->_id==null) {
  		JError::raiseError('500', JText::_('No id supplied in request for map filter'));
  	}
  	// Load if not already loaded
  	if (!$this->_map_filter) {
  		$db =& $this->getDBO();
  		$query = "SELECT * FROM ".$db->nameQuote('#__sdi_mapfilter').
  		" WHERE ".$db->nameQuote('id')." = ".$this->_id;
  		$db->setQuery($query);
  		$this->_map_filter = $db->loadObject();
  	}
  	return array($this->_map_filter);
  }
  
  /**
   * Return a list of the filters
   */
  function getList()
  {
  	// Load if not already loaded  	
  	$db =& $this->getDBO();
  	$query = "SELECT mf.id, mf.name, mf.description, mf.filter_mode, u.name FROM #__sdi_mapfilter ".
  		"mf INNER JOIN #__users u ON u.id=mf.user_id";
  	if ($this->_user_id!=null) {
  	  $query .= " WHERE user_id=".$this->_user_id;
  	}  	
  	$filter = JRequest::getCmd('query', null);
  	if ($filter) {
  		if ($this->_user_id==null) {
  			$query .= " WHERE";
  		} else {
  			$query .= " AND";
  		}
  		$query .= " name LIKE '$filter%'";
  	}  	    	
  	$query .=	" ORDER BY ".$db->nameQuote('name');
  	$limit = JRequest::getCmd('limit', '');
  	$offset = JRequest::getCmd('offset', '');
  	
  	if ($limit!='') {
  		$query .= " LIMIT $limit";
  	}
  	if ($offset!='') {
  		$query .= " OFFSET $offset";
  	}
  	$db->setQuery($query);  	
  	  	
  	return $this->_map_filter = $db->loadObjectList();  	
  }
  
  function save($data) {
  	// Load if not already loaded  	
  	$db =& $this->getDBO();
  	// Check if this is an overwrite based on unique username/title
  	$title=$data['title'];
  	$user_id=$data['user_id'];
  	$query = "SELECT mf.id FROM #__sdi_mapfilter mf ".
	  	"INNER JOIN #__users u ON u.id=mf.user_id ".
	  	"WHERE mf.name='$title' AND u.id=$user_id";
  	$db->setQuery($query);
  	$existing=$db->loadObject();
  	if ($existing) {
  		// Got a match, so set the ID for overwrite.
  		$data['id'] = $existing->id;
  	}
  	$table =& $this->getTable('sdi_mapfilter');
  	if (!$table->save($data)) {
  		// error occurred, so update model error message
  		$this->setError($table->getError());
  		return false;
  	}
  	return true;  	
  }
  
  /**
   * Remove a saved filter by ID
   */
  function delete($id) {
  	$table =& $this->getTable('Easysdi_map_filter');
  	$table->delete($id);
  }
}

?>