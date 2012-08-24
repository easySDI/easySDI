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

class ADMIN_mailing {

	function listMailing($option) {
		global $mainframe;
		$database =& JFactory::getDBO();
		
		$limit = $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', 10 );
		$limitstart = $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 );		
		$use_pagination = JRequest::getVar('use_pagination',0);
		$type = $mainframe->getUserStateFromRequest( "type{$option}", 'type', '' );
		$search = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
		$search = $database->getEscaped( trim( strtolower( $search ) ) );
		$filter = "";
		if ( $search ) {
			$filter .= " AND (#__users.name LIKE '%$search%'";
			$filter .= " OR #__users.username LIKE '%$search%'";		
			$filter .= " OR #__sdi_account.acronym LIKE '%$search%'";		
			$filter .= " OR #__sdi_account.id LIKE '%$search%'";		
			$filter .= " OR #__sdi_account.code LIKE '%$search%')";		
		}


		if ($type != '') {

			// D�compte des enregistrements totaux
			switch ($type) {
				case '1':	// Bulletin de vote pour AG
					$query = "SELECT COUNT(*)";
					$query .= " FROM #__users,#__sdi_account";
					$query .= " WHERE #__users.id=#__sdi_account.user_id";
					$query .= " AND #__sdi_account.root_id IS NULL";
					$query .= " AND #__sdi_account.profile_id IN (2,3)";
					break;
				case '2':	// Cotisation adh�rents
					$query = "SELECT COUNT(*)";
					$query .= " FROM #__users,#__sdi_account";
					$query .= " WHERE #__users.id=#__sdi_account.user_id";
					$query .= " AND #__sdi_account.root_id IS NULL";
					$query .= " AND #__sdi_account.id IN (2,3)";
					break;
				case '3':	// Facturation utilisateurs
					$query = "SELECT COUNT(*)";
					$query .= " FROM #__users,#__sdi_account";
					$query .= " WHERE #__users.id=#__sdi_account.user_id";
					$query .= " AND #__sdi_account.root_id IS NULL";
					$query .= " AND #__sdi_account.profile_id IN (4)";
					break;
				default:
					break;

			}
	
			$query .= $filter;
			$database->setQuery( $query );
			$total = $database->loadResult();
			echo $database->getErrorMsg();
			//require_once("includes/pageNavigation.php");
			$pageNav = new JPagination($total,$limitstart,$limit);			
			// Recherche des enregistrements selon les limites
			switch ($type) {
				case '1':	// Bulletin de vote pour AG
					$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.*";
					$query .= " FROM #__users,#__sdi_account";
					$query .= " WHERE #__users.id=#__sdi_account.user_id";
					$query .= " AND #__sdi_account.root_id IS NULL";
					$query .= " AND #__sdi_account.id IN (2,3)";
					break;
				case '2':	// Cotisation adh�rents
					$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.*";
					$query .= " FROM #__users,#__sdi_account";
					$query .= " WHERE #__users.id=#__sdi_account.user_id";
					$query .= " AND #__sdi_account.root_id IS NULL";
					$query .= " AND #__sdi_account.id IN (2,3)";
					break;
				case '3':	// Facturation utilisateurs
					$query = "SELECT #__users.name as account_name,#__users.username as account_username,#__sdi_account.*";
					$query .= " FROM #__users,#__sdi_account";
					$query .= " WHERE #__users.id=#__sdi_account.user_id";
					$query .= " AND #__sdi_account.root_id IS NULL";
					$query .= " AND #__sdi_account.id IN (4)";
					break;
				default:
					break;

			}
			$query .= $filter;
			$query .= " ORDER BY #__users.name";
			if ($use_pagination) {
				$query .= " LIMIT $pageNav->limitstart, $pageNav->limit";	
			}
			$database->setQuery( $query );
			$rows = $database->loadObjectList();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}

		}

		HTML_mailing::listMailing($use_pagination, $rows, $pageNav, $search, $option, $type);	

	}

	function exportXML( $cid, $option ) {
		
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('S�lectionnez un enregistrement � publier'); window.history.go(-1);</script>\n";
			exit;
		}

		$document = new DomDocument();
		$document->loadXML(ADMIN_mailing::createXML( $cid ));
		$result = $document->saveXML();
	
		ADMIN_mailing::sendResult( $result, 'export.xml' );

	}

	function exportFO( $cid, $option ) {
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('S�lectionnez un enregistrement � publier'); window.history.go(-1);</script>\n";
			exit;
		}

		$document = new DomDocument();
		$document->loadXML(ADMIN_mailing::createXML( $cid ));
		$result = ADMIN_mailing::applyXSL( $document );
	
		ADMIN_mailing::sendResult( $result, 'export.fo' );
	}

	function exportPDF( $cid, $option ) {
		$database =& JFactory::getDBO();

		if (!is_array( $cid ) || count( $cid ) < 1) {
			echo "<script> alert('S�lectionnez un enregistrement � publier'); window.history.go(-1);</script>\n";
			exit;
		}

		$document = new DomDocument();
		$document->loadXML(ADMIN_mailing::createXML( $cid ));
		$result = ADMIN_mailing::applyXSL( $document );
	
		/*if (!extension_loaded('java')) {
			echo JText::_("EASYSDI_ERROR_JAVA");
			
		} else {*/
		//require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'java'.DS.'Java.inc');
		
		//require_once("http://localhost:8081/JavaBridge/java/Java.inc");
		require_once("http://localhost:8081/JavaBridge/java/Java.inc");
			//$java_library_path = 'file:/usr/share/fop/lib/fop.jar;file:/usr/share/fop/lib/FOPWrapper.jar';
			
			$java_library_path = 'file:'.JPATH_COMPONENT_ADMINISTRATOR.DS.'java'.DS.'fop'.DS.'fop.jar;'.'file:'.JPATH_COMPONENT_ADMINISTRATOR.DS.'java'.DS.'fop'.DS.'FOPWrapper.jar';
			
			$fopcfg = JPATH_COMPONENT_ADMINISTRATOR.DS.'config'.DS.'fop.xml';
			$foptmp = JPATH_COMPONENT_ADMINISTRATOR.DS.'tmp'.DS.'export.pdf';
			try {
				
				java_require($java_library_path);
			//java_autoload($java_library_path);
				$j_fw = new Java("FOPWrapper");
				//Version du processeur FOP int�gr�
				$version = $j_fw->FOPVersion();
				//G�n�ration du document PDF sous forme de fichier
				$j_fw->convert($fopcfg,$result,$foptmp);

				//G�n�ration du document PDF sous forme de flux de bytes
				//$result = $j_fw->convert($result);
				//$in = new Java("java.io.ByteArrayOutputStream");
				//$in->write($j_fw->convert($result));
				//$result = $in->toByteArray();
				
				@java_reset();

				$fp = fopen ($foptmp, 'r');
				$result = fread($fp, filesize($foptmp));
				fclose ($fp);

				 ADMIN_mailing::sendResult( $result, 'export.pdf' );
			} catch (JavaException $ex) {
				$trace = new Java("java.io.ByteArrayOutputStream");
				$ex->printStackTrace(new Java("java.io.PrintStream", $trace));
				print "java stack trace: $trace\n";
			}
		//}
	}

	function createXML( $cid ) {
		$database =& JFactory::getDBO();

		$type = JRequest::getVar('type','');
		
		$mailing_date = JRequest::getVar('mailing_date','');
		$mailing_locality = JRequest::getVar('mailing_locality','');
		$mailing_year = JRequest::getVar('mailing_year',date('Y'));
		$mailing_discount = JRequest::getVar('mailing_discount','0');

		$xml = '<asit-vd>';
		switch ($type) {
			case '1' :
				$xml .= '<parameters date="'.$mailing_date.'" locality="'.$mailing_locality.'" />';
				break;
			case '2' :
				$xml .= '<parameters date="'.$mailing_date.'" year="'.$mailing_year.'" discount="'.$mailing_discount.'" />';
				break;
			case '3' :
				$xml .= '<parameters date="'.$mailing_date.'" year="'.$mailing_year.'" discount="'.$mailing_discount.'" />';
				break;
		}
		foreach( $cid as $partner_id )
		{
			$partner = new partner( $database );
			$partner->load( $partner_id );
		
			$user =& new JTableUser($database);
			//$user = new mosUser( $database );
			$user->load( $partner->user_id );

			$xml .= '<account id="'.$partner->partner_id.'" code="'.$partner->partner_code.'" migration="'.$partner->partner_migration.'" user="'.$user->username.'" name="'.$user->name.'" acronym="'.$partner->partner_acronym.'">';
			$xml .= ADMIN_mailing::addressXML( 'contact', $partner_id, 1 );
			$xml .= ADMIN_mailing::addressXML( 'billing', $partner_id, 2 );
			$xml .= ADMIN_mailing::addressXML( 'delivery', $partner_id, 3 );
			$xml .= '<detail category="'.$partner->category_id.'" profile="'.$partner->profile_id.'">';
			$xml .= '<collaborator>'.$partner->collaborator_id.'</collaborator>';
			$xml .= '<member>'.$partner->member_id.'</member>';
			$xml .= '<activity>'.$partner->activity_id.'</activity>';
			$xml .= '<base>0</base>';
			$xml .= '<contract>'.$partner->partner_contract.'</contract>';
			$xml .= '<inhabitant>'.$partner->partner_inhabitant.'</inhabitant>';
			$xml .= '<electricity>'.$partner->partner_electricity.'</electricity>';
			$xml .= '<gas>'.$partner->partner_gas.'</gas>';
			$xml .= '<heating>'.$partner->partner_heating.'</heating>';
			$xml .= '<telcom>'.$partner->partner_telcom.'</telcom>';
			$xml .= '<network>'.$partner->partner_network.'</network>';
			$xml .= '<estimate>0</estimate>';
			$xml .= '<command>0</command>';
			$xml .= '</detail>';
			$xml .= '</account>';
		}
		$xml .= '</asit-vd>';
		$xml = utf8_encode($xml);
		return $xml;
	}

	function addressXML( $name, $partner, $type ) {
		$database =& JFactory::getDBO();

		$database->setQuery( "SELECT address_id FROM #__easysdi_community_address WHERE partner_id=".$partner." AND type_id=".$type );	
		$address_id = $database->loadResult();
		$address = new address( $database );
		$address->load( $address_id );

		$xml .= '<'.$name.'>';
		$xml .= '<corporate1>'.$address->address_corporate_name1.'</corporate1>';
		$xml .= '<corporate2>'.$address->address_corporate_name2.'</corporate2>';
		$xml .= '<title>'.$address->title_id.'</title>';
		$xml .= '<firstname>'.$address->address_agent_firstname.'</firstname>';
		$xml .= '<lastname>'.$address->address_agent_lastname.'</lastname>';
		$xml .= '<address1>'.$address->address_street1.'</address1>';
		$xml .= '<address2>'.$address->address_street2.'</address2>';
		$xml .= '<postalcode>'.$address->address_postalcode.'</postalcode>';
		$xml .= '<locality>'.$address->address_locality.'</locality>';
		$xml .= '<country>'.$address->country_code.'</country>';
		$xml .= '</'.$name.'>';
		return $xml;
	}

	function applyXSL( $document ) {
		$type = JRequest::getVar('type','');
		
		switch ($type) {
			case '1' :
				$xslFile = 'mailing.vote.xsl';
				break;
			case '2' :
				$xslFile = 'mailing.subscription.xsl';
				break;
			case '3' :
				$xslFile = 'mailing.billing.xsl';
				break;
		}

		$processor = new xsltProcessor();
		$style = new DomDocument();
		
		
		$style->load(dirname(__FILE__).'/../xsl/'.$xslFile);
		$processor->importStylesheet($style);
		return $processor->transformToXml($document);
	}

	function sendResult( $result, $saveas ) {
		error_reporting(0);
		ini_set('zlib.output_compression', 0);
		header('Pragma: public');
		header('Cache-Control: must-revalidate, pre-checked=0, post-check=0, max-age=0');
		header('Content-Transfer-Encoding: none');
		header('Content-Type: application/octetstream; name="'.$saveas.'"');
		header('Content-Disposition: attachement; filename="'.$saveas.'"');

		echo $result;
		die();
	}

}

?>
