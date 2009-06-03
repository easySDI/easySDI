<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin dâ€™Arche 40b, CH-1870 Monthey, easysdi@depth.ch
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

class ADMIN_product {

	function publish($cid,$published){
		global  $mainframe;
		$db =& JFactory::getDBO();
		if ($published){
			$query = "update #__easysdi_product  set published = 1  where id=$cid[0]";

		}else{
			$query = "update #__easysdi_product  set published = 0  where id=$cid[0]";
		}
		$db->setQuery( $query ,$limitstart,$limit);
		if (!$db->query()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
		}

	}

	function listProduct($option) {
		global  $mainframe;
		$db =& JFactory::getDBO();

		$limit = JRequest::getVar('limit', 10 );
		$limitstart = JRequest::getVar('limitstart', 0 );
		$use_pagination = JRequest::getVar('use_pagination',1);
		$profile = $mainframe->getUserStateFromRequest( "profile{$option}", 'profile', '' );
		$category = $mainframe->getUserStateFromRequest( "category{$option}", 'category', '' );
		$payment = $mainframe->getUserStateFromRequest( "payment{$option}", 'payment', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $db->getEscaped( trim( strtolower( $search ) ) );

		$query = "SELECT COUNT(*) FROM #__easysdi_product ";

		//$query .= $filter;
		$db->setQuery( $query );
		$total = $db->loadResult();
		$pageNav = new JPagination($total,$limitstart,$limit);


		// Recherche des enregistrements selon les limites

		 
		$query = "SELECT * FROM #__easysdi_product   ";
		if ($search !=null && strlen($search)>0 ) {$query = $query ." WHERE data_title like '%$search%' ";}	

		if ($use_pagination) {
			$db->setQuery( $query ,$limitstart,$limit);
		}
		else {
			$db->setQuery( $query );
		}
		
		
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			$mainframe->enqueueMessage($db->getErrorMsg(),"ERROR");
			exit();
		}

		HTML_product::listProduct($use_pagination, $rows, $pageNav,$option);

	}


	function editProductMetadata2( $id, $option ) {
		global  $mainframe;
		$database =& JFactory::getDBO();
		$rowProduct = new product( $database );
		$rowProduct->load( $id );

		$rowProduct->update_date = date('d.m.Y H:i:s');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
			$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
			HTML_product::editProductMetadata2( $rowProduct,$id, $option );
		}
	}

	function editProductMetadata( $id, $option ) {
		global  $mainframe;
		$database =& JFactory::getDBO();
		$rowProduct = new product( $database );
		$rowProduct->load( $id );

		$rowProduct->update_date = date('d.m.Y H:i:s');
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");
		if (strlen($catalogUrlBase )==0){
			$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
			HTML_product::editProductMetadata( $rowProduct,$id, $option );
		}
	}

	function editProduct( $id, $option ) {
		global  $mainframe;
		$user = JFactory::getUser();
		$database =& JFactory::getDBO();

		$rowProduct = new product( $database );

		if($id == '0')
		{
			$id = JRequest::getVar('id', 0 );
		}
		if ($id == '0' ){
			$rowProduct->creation_date =date('d.m.Y H:i:s');
			$rowProduct->metadata_id = helper_easysdi::getUniqueId();
			$partner = new partnerByUserId($database);
			$partner->load($user->id);
			$rowProduct->partner_id = $partner->partner_id;
			if(userManager::hasRight($partner->partner_id,"METADATA"))
			{
				$rowProduct->metadata_partner_id = $partner->partner_id;
			}
		}
		else
		{
			$rowProduct->load( $id );
		}
		/*$product = JRequest::getVar('product_id', 0 );

		if($id == '0' && $product != '0')
		{
		$rowProduct->load( $product );
		}
		else if ($id == '0' && $product == '0'){
		$rowProduct->creation_date =date('d.m.Y H:i:s');
		$rowProduct->metadata_id = helper_easysdi::getUniqueId();
		$partner = new partnerByUserId($database);
		$partner->load($user->id);
		$rowProduct->partner_id = $partner->partner_id;
		if(userManager::hasRight($partner->partner_id,"METADATA"))
		{
		$rowProduct->metadata_partner_id = $partner->partner_id;
		}
		}
		else
		{
		$rowProduct->load( $id );
		}*/

		$rowProduct->update_date = date('d.m.Y H:i:s');

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		if (strlen($catalogUrlBase )==0){
			$mainframe->enqueueMessage("NO VALID CATALOG URL IS DEFINED","ERROR");
		}else{
			HTML_product::editProduct( $rowProduct,$id, $option );
		}
	}

	function saveProductMetadata($option){

		global  $mainframe;
		$database =& JFactory::getDBO();

			
		$metadata_standard_id = JRequest::getVar("standard_id");
		$metadata_id = JRequest::getVar("metadata_id");
		
		$query = "SELECT b.text as text,a.tab_id as tab_id FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_tabs b where a.tab_id =b.id  and (a.standard_id = $metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and id = $metadata_standard_id)) group by a.tab_id" ;
		$database->setQuery($query);
		$rows = $database->loadObjectList();
		if ($database->getErrorNum()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}
			

		$doc = "<gmd:MD_Metadata xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xmlns:gco=\"http://www.isotc211.org/2005/gco\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" xmlns:gml=\"http://www.opengis.net/gml\" xmlns:gts=\"http://www.isotc211.org/2005/gts\" xmlns:ext=\"http://www.depth.ch/2008/ext\">";

		

		foreach ($rows as $row){


		$query = "SELECT  * FROM #__easysdi_metadata_standard_classes a, #__easysdi_metadata_classes b where a.class_id =b.id and a.tab_id = $row->tab_id and (a.standard_id = $metadata_standard_id or a.standard_id in (select inherited from #__easysdi_metadata_standard where is_deleted =0 AND inherited !=0 and id = $metadata_standard_id)) order by position" ;
			
		$database->setQuery($query);
		$rowsClasses = $database->loadObjectList();
		if ($database->getErrorNum()) {
		$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
		}

		foreach ($rowsClasses as $rowClasses){
		$doc=$doc."<$rowClasses->iso_key>";
					
		$count = helper_easysdi::searchForLastEntry($rowClasses,$metadata_standard_id);
				
		for ($i=0;$i<helper_easysdi::searchForLastEntry($rowClasses,$metadata_standard_id);$i++){										
			helper_easysdi::generateMetadata($rowClasses,$row->tab_id,$metadata_standard_id,$rowClasses->iso_key,&$doc,$i);							
		}

		$doc=$doc."</$rowClasses->iso_key>";
		}
		}				
		
		$doc=$doc."</gmd:MD_Metadata>";

	
		$xmlstr = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<csw:Transaction service=\"CSW\"
		version=\"2.0.0\"
		xmlns:csw=\"http://www.opengis.net/cat/csw\" >
		<csw:Insert>
		$doc
		</csw:Insert>
		</csw:Transaction>";

		//$mainframe->enqueueMessage(htmlentities($xmlstr),"ERROR");	

			
		ADMIN_product::deleteMetadata($metadata_id);
		ADMIN_product::SaveMetadata($xmlstr);
			
		$query = "UPDATE #__easysdi_product SET hasMetadata = 1 WHERE id = ".$metadata_standard_id = JRequest::getVar("id");
		
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}


	}

	function saveProduct($returnList ,$option){
		global  $mainframe;
		$database=& JFactory::getDBO();

		$rowProduct =&	 new Product($database);


		if (!$rowProduct->bind( $_POST )) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}


		if (!$rowProduct->store()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}



		$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$rowProduct->id;
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}



		foreach( $_POST['perimeter_id'] as $perimeter_id )
		{
			$query = "INSERT INTO #__easysdi_product_perimeter VALUES (0,$rowProduct->id,$perimeter_id,0)";

			$database->setQuery( $query );
			if (!$database->query()) {
				//echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}

	foreach( $_POST['buffer'] as $bufferPerimeterId )
		{
			$query = "UPDATE #__easysdi_product_perimeter SET isBufferAllowed=1 WHERE product_id = $rowProduct->id AND perimeter_id = $bufferPerimeterId";
			
			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				//$mainframe->redirect("index.php?option=$option&task=listProduct" );	
					exit();			
			}
		}


		$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$rowProduct->id;
		$database->setQuery( $query );
		if (!$database->query()) {
			$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit();
		}



		foreach( $_POST['properties_id'] as $properties_id )
		{
			$query = "INSERT INTO #__easysdi_product_property VALUES (0,".$rowProduct->id.",".$properties_id.")";

			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}
		}

		//	 ADMIN_product::SaveMetadata();

		/*
		 * sdondainaz
		 * 
		 * Ajouter ici la mise à jour du paramètre de visibilité pour la métadonnée, avec CSW.
		 * Se baser sur le paramètre "externe" (boolean)
		 * "lors de la sauvegarde d'un produit, la valeur de la disponibilité interne/externe est
		 * stockée également dans la métadonnée correspondante (geonetwork ne gère pas les mises
		 * à jour et il est nécessaire de charger la totalité de la métadonnée en mémoire, modifier
		 * l'attribut désiré, supprimer la métadonnée actuelle dans GeoNetwork puis insérer la
		 * métadonnée mise à jour en prenant soin de conserver le même identifiant)"
		 * 
		 */ 
		
		if ($returnList == true) {
			$mainframe->redirect("index.php?option=$option&task=listProduct");
		}

	}

	function deleteProduct($cid ,$option){

		global $mainframe;
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			$mainframe->enqueueMessage(JText::_("EASYSDI_SELECT_ROW_TO_DELETE"),"error");
			$mainframe->redirect("index.php?option=$option&task=listProduct" );
			exit;
		}
		foreach( $cid as $id )
		{
			$product = new product( $database );
			$product->load( $id );

			if (!$product->delete()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
			}

			ADMIN_product::deleteMetadata($product->metadata_id);


			$query = "DELETE FROM  #__easysdi_product_perimeter WHERE PRODUCT_ID = ".$id;
			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}

			$query = "DELETE FROM  #__easysdi_product_property WHERE PRODUCT_ID = ".$id;
			$database->setQuery( $query );
			if (!$database->query()) {
				$mainframe->enqueueMessage($database->getErrorMsg(),"ERROR");
				$mainframe->redirect("index.php?option=$option&task=listProduct" );
				exit();
			}



		}



		$mainframe->redirect("index.php?option=$option&task=listProduct" );
	}

	function deleteMetadata($metadata_id){
		$xmlstr = "
		<csw:Transaction service=\"CSW\" 
   version=\"2.0.0\" 
   xmlns:csw=\"http://www.opengis.net/cat/csw\" 
   xmlns:dc=\"http://www.purl.org/dc/elements/1.1/\"
   xmlns:ogc=\"http://www.opengis.net/ogc\"
   xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" 
   xmlns:gco=\"http://www.isotc211.org/2005/gco\">
  <csw:Delete typeName=\"csw:Record\">
    <csw:Constraint version=\"2.0.0\">
      <ogc:Filter>
        <ogc:PropertyIsEqualTo>
            <ogc:PropertyName>//gmd:MD_Metadata/gmd:fileIdentifier/gco:CharacterString</ogc:PropertyName>
            <ogc:Literal>".$metadata_id."</ogc:Literal>
        </ogc:PropertyIsEqualTo>
      </ogc:Filter>
    </csw:Constraint>
  </csw:Delete>
</csw:Transaction>";

		include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$session = curl_init($catalogUrlBase);


		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $xmlstr);


		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

		// Make the call
		$xml = curl_exec($session);



		echo $xml;
		curl_close($session);


	}

	function SaveMetadata($xmlstr){
		$content_length = strlen($xmlstr);

		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'common'.DS.'easysdi.config.php');
		$catalogUrlBase = config_easysdi::getValue("catalog_url");

		$session = curl_init($catalogUrlBase);



		curl_setopt ($session, CURLOPT_POST, true);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $xmlstr);


		// Don't return HTTP headers. Do return the contents of the call
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

		// Make the call
		$xml = curl_exec($session);
		echo $xml;

		curl_close($session);

	}



}

?>