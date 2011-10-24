<?php

// ensure a valid entry point
//defined(_JEXEC) or die('Restricted Access');

jimport('joomla.application.component.model');

/**
 * Map_context model.
 */
 class EasySDI_mapModelMap_context extends JModel {
 
  /** 
  * Map context ID
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
  * map context object
  *
  * @var object
  */
  var $_map_context;
  
  /**
   * Constructor. Builds the object.
   */
  function __construct()
  {
    parent::__construct();

    // get the cid array from the request
    $id = JRequest::getInt('id', 0);
    $user_id = JRequest::getInt('user_id', 0);

    $this->setId($id);
    $this->setUserId($user_id);
  }

  /**
   * Reset the id and data.
   */
  function setId($id=0) 
  {
    $this->_id = $id;
    $this->_map_context=null;	
  }

  function setUserId($user_id) 
  {
    $this->_user_id = $user_id;    
  }

  /**
   * Retrieve a map_context (as a single item in an array for compatibility with list methods).
   */
  function getItem()
  {
    if ($this->_id==null) {
      JError::raiseError('500', JText::_('No id supplied in request for map context'));
    }
    // Load if not already loaded
    if (!$this->_map_context) {
      $db =& $this->getDBO();
      $query = "SELECT * FROM ".$db->nameQuote('#__sdi_mapcontext');
      if($this->_id!=null) {
        $query = $query." WHERE ".$db->nameQuote('id')." = ".$this->_id;
      } else {
        $query = $query." WHERE ".$db->nameQuote('user_id')." = ".$this->_user_id;
      }
      $db->setQuery($query);
      $this->_map_context = $db->loadObjectList();
    }
    return $this->_map_context;
  }

  /**
   * Retrieve a list of map_contexts (as a single item in an array for compatibility with list methods).
   * Used when fetching ny user_id.
   */
  function getList()
  {
    // Load if not already loaded   
    $db =& $this->getDBO();
    $query = "SELECT * FROM #__sdi_mapcontext mc ";
    if ($this->_user_id!=null) {
      $query .= " WHERE user_id=".$this->_user_id;
    }   
    $db->setQuery($query);
    return $this->_map_context = $db->loadObjectList();
  }

  function save($data) {
    // Load if not already loaded  	
    $db =& $this->getDBO();
    // Check if this is an overwrite based on unique user id
    $user_id=$data['user_id'];
    // This method prevents insertion of rows if the user does not exist
    $query = "SELECT u.id FROM #__users u ".
      "WHERE u.id=$user_id";
    $db->setQuery($query);
    $existing=$db->loadObject();
    if (!$existing) {
      // no user for this user id. Do not save
      return false;
    }

    $query = "SELECT mc.id FROM #__sdi_mapcontext mc ".
      "WHERE mc.user_id=$user_id";
    $db->setQuery($query);
    $existing=$db->loadObject();
    if ($existing) {
      // Got a match, so set the ID for overwrite.
      $data['id'] = $existing->id;
    }
    $table =& $this->getTable('sdi_map_context');
    if (!$table->save($data)) {
      // error occurred, so update model error message
      $this->setError($table->getError());
      return false;
    }
    return true;  	
  }

  /**
   * Remove a saved context by ID
   */
  function delete($id) {
    $table =& $this->getTable('sdi_map_context');
    $table->delete($id);
  }
}

?>