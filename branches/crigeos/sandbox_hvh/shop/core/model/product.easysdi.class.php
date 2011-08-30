<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
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

class product extends sdiTable
{
	
	var $objectversion_id			=0;
	var $surfacemin					=0;
	var $surfacemax					=0;
	var $published					=0;
	var $visibility_id				=0;
	var $available					=0;
	var $free						=0;
	var $diffusion_id				=null;
	var $treatmenttype_id			=null;
	var $notification				=null;
	var $viewbasemap_id				=null;
	var $viewurltype				=null;
	var $viewurlwms					=null;
	var $viewlayers					=null;
	var $viewminresolution			=null;
	var $viewmaxresolution			=null;
	var $viewprojection				=null;
	var $viewextent 				=null;
	var $viewmatrixset 				=null;
	var $viewmatrix 				=null;
	var $viewstyle 					=null;
	var $viewunit					=null;
	var $viewimgformat				=null;
	var $viewuser					=null;
	var $viewpassword				=null;
	var $viewaccount_id				=null;
	var $viewaccessibility_id		=null;
	var $loadaccessibility_id		=null;
	var $pathfile					=null;
		
	// Class constructor
	function __construct( &$db )
	{
		parent::__construct ( '#__sdi_product', 'id', $db ) ;    		
	}
	
	function store()
	{
		global  $mainframe;	
		if(! parent::store())
		{
			return false;
		}
		
		//Notify users for which this product is a favorite
		$this->_db->setQuery( "SELECT account_id FROM  #__sdi_favorite 
												 WHERE metadata_id = (SELECT metadata_id FROM #__sdi_objectversion WHERE id = $this->objectversion_id)
												 AND enable_notification = 1" );
		$users = $this->_db->loadObjectList();
		if ($this->_db->getErrorNum()) {
			$mainframe->enqueueMessage($this->_db->getErrorNum(), "ERROR");
			//TODO : Do something to notify but let the store process goes on
		}
		
		//send an email
		$mailer =& JFactory::getMailer();
		foreach ($users as $user)
		{
			$query = "SELECT u.email FROM #__users u INNER JOIN #__sdi_account a ON a.user_id=u.id
										WHERE a.id=$user->account_id";
			$this->_db->setQuery( $query );
			$email = $this->_db->loadResult();
			
			if ($email != ""){
				$mailer->addBCC($email);
				$mailer->setSubject(JText::_("SHOP_PRODUCT_NOTIFICATION_SUBJECT"));	
				$mailer->setBody(JText::sprintf("SHOP_PRODUCT_NOTIFICATION_MAIL_BODY",$this->name));	
				if ($mailer->send() !==true){
					//TODO : Do something to notify but let the store process goes on		
				}
			}
		}
				
		if($this->available == 0 || $this->free == 0)
		{
			//The product is not avalaible, delete the product stored in the database
			$this->_db->setQuery( "DELETE FROM  #__sdi_product_file WHERE product_id = ".$this->id );
			if (!$this->_db->query()) {
				return false;
			}
			//Clean the pathFile value
			$this->pathfile = null;
			if(! parent::store())
			{
				return false;
			}
		}
		else
		{
			if(isset($_FILES['productfile']) && !empty($_FILES['productfile']['name'])) 
		 	{
		 		//A file was uploaded, store it in the database
		 		$fileName = $_FILES['productfile']["name"];
		 		$tmpName =  $_FILES['productfile']["tmp_name"];
		 		$type = strtolower(substr($fileName, strrpos($fileName, '.')+1)) ;
  				$size = ($_FILES['productfile']["size"] ) ;
			 	$fp      = fopen($tmpName, 'r');
			 	$content = fread($fp, filesize($tmpName));
			 	$content = addslashes($content);
			 	fclose($fp);
				
				$this->_db->setQuery( "SELECT COUNT(*) FROM  #__sdi_product_file WHERE product_id = ".$this->id );
				$result = $this->_db->loadResult();
				if ($this->_db->getErrorNum()) {
					$mainframe->enqueueMessage($this->_db->getErrorMsg(), "ERROR");
					return false;
				}
				if($result > 0)
				{
					$this->_db->setQuery( "UPDATE  #__sdi_product_file SET data='".$content."', filename='".$fileName."', size =".$size.", type='".$type."' WHERE product_id = ".$this->id );
					if (!$this->_db->query()) {
						$mainframe->enqueueMessage($this->_db->getErrorMsg(), "ERROR");
						return false;
					}
				}
				else
				{
					$query =  "INSERT INTO  #__sdi_product_file (filename, data,product_id, type, size) VALUES ('".$fileName."' ,'".$content."', ".$this->id.", '".$type."', ".$size." )" ;
					$this->_db->setQuery($query);
					if (!$this->_db->query()) {
						$mainframe->enqueueMessage($this->_db->getErrorMsg(), "ERROR");
						return false;
					}
				}
		 		$this->pathfile = null;
				if(! parent::store())
				{
					return false;
				}
			}elseif ($this->pathfile != null){
				//A local path file is defined, delete the file stored in the database
				$this->_db->setQuery( "DELETE FROM  #__sdi_product_file WHERE product_id = ".$this->id );
				if (!$this->_db->query()) {
					return false;
				}
			}
			else
			{
				//If no local file path and no file are defined, set the available attribute value to false
				$this->_db->setQuery( "SELECT COUNT(*) FROM  #__sdi_product_file WHERE product_id = ".$this->id );
				$result = $this->_db->loadResult();
				if ($this->_db->getErrorNum()) {
					$mainframe->enqueueMessage($this->_db->getErrorMsg(), "ERROR");
					return false;
				}
				if($result == 0 && $this->pathfile == null){
					$this->available = 0;
					if(! parent::store())
					{
						return false;
					}
				}
			}
		}
		return true;
	}
	
	function getFile(){
		
		if ($this->pathfile != null){
			$handle = fopen($this->pathfile, "r");
			$contents = '';
			while (!feof($handle)) {
			  $contents .= fread($handle, 8192);
			}
			fclose($handle);
			return $contents;
		}else{
			$this->_db->setQuery("SELECT data,filename FROM #__sdi_product_file where product_id = ".$this->id);
			$row = $this->_db->loadObject();
			return $row->data;
		}
		return null;
	}
	
	function getFileExtension (){
		if ($this->pathfile != null){
			$path_parts = pathinfo($this->pathfile);
			return $path_parts['extension'];
		}else{
			$this->_db->setQuery("SELECT type FROM #__sdi_product_file where product_id = ".$this->id);
			return  $this->_db->loadResult();;
		}
		return null;
	}
	
	function getFileName()
	{
		if ($this->pathfile != null){
			return basename($this->pathfile);
		}
		
		global  $mainframe;	
		$this->_db->setQuery( "SELECT filename FROM  #__sdi_product_file WHERE product_id = ".$this->id );
		$filename = $this->_db->loadResult();
		if ($this->_db->getErrorNum()) {
			$mainframe->enqueueMessage($this->_db->getErrorMsg());
			return false;
		}
		return $filename;
	}
	
	function deleteProduct ()
	{
		$this->_db->setQuery( "DELETE FROM  $this->_tbl WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}

		$this->_db->setQuery( "DELETE FROM  $this->_tbl WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}
		
		return parent::delete();
		
	}
	
	function delete()
	{
		$this->_db->setQuery( "DELETE FROM  #__sdi_product_perimeter WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}
	
		$this->_db->setQuery( "DELETE FROM  #__sdi_product_property WHERE product_id = ".$this->id );
		if (!$this->_db->query()) {
			return false;
		}
		
		return parent::delete();
	}
	
	function publish ()
	{
		$this->published = 1;
		return $this->store();
	}
	
	function unpublish()
	{
		$this->published = 0;
		return $this->store();
	}
	
	function isUserAllowedToView ($account_id){
		global  $mainframe;	
		$account = new account( $this->_db );
		$account->load( $account_id );
		
		if($this->viewaccessibility_id != null || $this->viewaccessibility_id != ''){
			//Predefine accessibility is set
			$this->_db->setQuery( "SELECT a.code FROM  #__sdi_list_accessibility a WHERE a.id = ".$this->viewaccessibility_id );
			$code = $this->_db->loadResult();
			if ($this->_db->getErrorNum()) {
				return false;
			}
			
			if($code == 'all'){
				return true;
			}else if ($code == 'ofRoot'){
				//View is allowed for accounts affiliated to the product supplier
				$this->_db->setQuery( "SELECT o.account_id FROM  #__sdi_object o 
										INNER JOIN #__sdi_objectversion v ON v.object_id = o.id 
										INNER JOIN #__sdi_product p ON p.objectversion_id = v.id
										WHERE p.id = ".$this->id);
				$supplier_id = $this->_db->loadResult();
				if ($this->_db->getErrorNum()) {
					$mainframe->enqueueMessage($this->_db->getErrorMsg());
					return false;
				}
				if($supplier_id == $account->root_id){
					return true;
				}else{
					return false;
				}
			}else if ($code == 'ofManager'){
				//View is allowed for accounts affiliated to the product manager
				$this->_db->setQuery( "SELECT m.account_id 
										FROM #__sdi_product p 
										INNER JOIN #__sdi_objectversion v ON p.objectversion_id = v.id
										INNER JOIN #__sdi_object o ON o.id = v.object_id
										INNER JOIN #__sdi_manager_object m ON m.object_id = o.id 
										WHERE p.id = ".$this->id);
				$managerAccount_id = $this->_db->loadResultArray();
				if ($this->_db->getErrorNum()) {
					$mainframe->enqueueMessage($this->_db->getErrorMsg());
					return false;
				}
				foreach ($managerAccount_id as $manager_id){
					if($manager_id == $account_id || $manager_id == $account->root_id ){
						return true;
					}
					$manager = new account( $this->_db );
					$manager->load( $manager_id );
					$parentList = $manager->getParentList();
					foreach ($parentList as $parent_id){
						if($manager_id == $parent_id ){
							return true;
						}
					}
				}
				return false;
			}else{
				return false;
			}
		}else{
			//Check if the current user or his root account is allowed to view the product
			$this->_db->setQuery( "SELECT pa.account_id FROM  #__sdi_product_account pa 
										WHERE pa.product_id = ".$this->id.
									" AND pa.code='preview'" );
			$authorizedAccount_id = $this->_db->loadResultArray();
			if ($this->_db->getErrorNum()) {
				$mainframe->enqueueMessage($this->_db->getErrorMsg());
				return false;
			}
			foreach ($authorizedAccount_id as $authorized_id){
				if($authorized_id == $account_id || $authorized_id == $account->root_id){
					return true;
				}
			}
			return false;
		}
		return false;
	}

	function isUserAllowedToLoad ($account_id){
		global  $mainframe;	
		global  $mainframe;	
		$account = new account( $this->_db );
		$account->load( $account_id );
		
		if($this->loadaccessibility_id != null || $this->loadaccessibility_id != ''){
			//Predefine accessibility is set
			$this->_db->setQuery( "SELECT a.code FROM  #__sdi_list_accessibility a WHERE a.id = ".$this->loadaccessibility_id );
			$code = $this->_db->loadResult();
			if ($this->_db->getErrorNum()) {
				return false;
			}
			
			if($code == 'all'){
				return true;
			}else if ($code == 'ofRoot'){
				//Download is allowed for accounts affiliated to the product supplier
				$this->_db->setQuery( "SELECT o.account_id FROM  #__sdi_object o 
										INNER JOIN #__sdi_objectversion v ON v.object_id = o.id 
										INNER JOIN #__sdi_product p ON p.objectversion_id = v.id
										WHERE p.id = ".$this->id);
				$supplier_id = $this->_db->loadResult();
				if ($this->_db->getErrorNum()) {
					$mainframe->enqueueMessage($this->_db->getErrorMsg());
					return false;
				}
				if($supplier_id == $account->root_id){
					return true;
				}else{
					return false;
				}
			}else if ($code == 'ofManager'){
				//Download is allowed for accounts affiliated to the product manager
				$this->_db->setQuery( "SELECT m.account_id 
										FROM #__sdi_product p 
										INNER JOIN #__sdi_objectversion v ON p.objectversion_id = v.id
										INNER JOIN #__sdi_object o ON o.id = v.object_id
										INNER JOIN #__sdi_manager_object m ON m.object_id = o.id 
										WHERE p.id = ".$this->id);
				$managerAccount_id = $this->_db->loadResultArray();
				if ($this->_db->getErrorNum()) {
					$mainframe->enqueueMessage($this->_db->getErrorMsg());
					return false;
				}
				foreach ($managerAccount_id as $manager_id){
					if($manager_id == $account_id || $manager_id == $account->root_id ){
						return true;
					}
					$manager = new account( $this->_db );
					$manager->load( $manager_id );
					$parentList = $manager->getParentList();
					foreach ($parentList as $parent_id){
						if($manager_id == $parent_id ){
							return true;
						}
					}
				}
				return false;
			}else{
				return false;
			}
		}else{
			//Check if the current user or his root account is allowed to download the product
			$this->_db->setQuery( "SELECT pa.account_id FROM  #__sdi_product_account pa 
										WHERE pa.product_id = ".$this->id.
									" AND pa.code='download'" );
			$authorizedAccount_id = $this->_db->loadResultArray();
			if ($this->_db->getErrorNum()) {
				$mainframe->enqueueMessage($this->_db->getErrorMsg());
				return false;
			}
			foreach ($authorizedAccount_id as $authorized_id){
				if($authorized_id == $account_id || $authorized_id == $account->root_id){
					return true;
				}
			}
			return false;
		}
		return false;
	}
}

?>