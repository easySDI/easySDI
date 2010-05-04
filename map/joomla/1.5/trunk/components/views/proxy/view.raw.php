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

// TODO - why does the next line need to be commented out?
//defined(_JEXEC) or die('Restricted Access');

jimport('joomla.application.component.view');

class EasySDI_mapViewProxy extends JView {

	function display()
	{
		ob_end_clean();
		$url = JRequest::getVar('url', ''); // note this is case sensitive
		$request = strtolower(JRequest::getVar('request', ''));
		if(!$request) $request = strtolower(JRequest::getVar('REQUEST', ''));
		if ($this->contains("?",$url)){
			$url=$url."&";
		} else {
			$url=$url."?";
		}

		$found = false;
		$proxyParams = array("url", "option", "Itemid", "view", "format", "basemapscontentid",
		                     "perimeterdefid", "locationid", "overlaycontentid");
		if($request == 'getfeatureinfo') $_GET['info_format']='application/vnd.ogc.gml';
		foreach($_GET AS $cle => $valeur)
		{
			// at this point do not copy through the parameters which are specific to the proxy:
			if ($found && !in_array($cle, $proxyParams)){
				//$url=$url."$cle=".urlencode($valeur)."&";
				// Probl�me : le filtre des wfs est �galement d�compos� dans la variable GET en param�tre chaque fois que le signe
				// �gal est rencontr�. Pour palier � ce probl�me on enl�ve les cl�s qui n'ont pas de valeur correspondantes
				if ((strcasecmp ($valeur,"style"))|| ($valeur != null && strlen($valeur)>0 )){
					$url=$url."$cle=$valeur&";
				}
			}
			if ($cle == "url"){
				$found =true;
			}
		}

		$postData = file_get_contents( "php://input" );

		$url = str_replace ('\"','"',$url);
		$url = str_replace (' ','%20',$url);
			
		//fclose($fh);

		// Get the user details
		$u = JFactory::getUser();
		if ($u->id==0) {
			// not logged in, so load the service level account instead
			$db =& JFactory::getDBO();
			$query = "SELECT cp.user_id ".
          "FROM #__easysdi_map_service_account sa ".
          "INNER JOIN #__easysdi_community_partner cp ON cp.partner_id=sa.partner_id LIMIT 1";
			$db->setQuery($query);
			$result = $db->loadAssocList();
			if (count($result)>0)
			$u = JFactory::getUser($result[0]['user_id']);
		}

		$user=$u->username;
		$password=$u->password;

		$session = curl_init($url);
		// Set the POST options.
		$httpHeader = array();
		if (!empty($postData)) {
			curl_setopt($session, CURLOPT_POST, 1);
			curl_setopt($session, CURLOPT_POSTFIELDS, $postData);
			// post contains a raw XML document?
			if (substr($postData, 0, 1)=='<') {
				$httpHeader[]='Content-Type: text/xml';
			}
		}
		if ($user != null && strlen($user)>0 && $password != null && strlen($password)>0) {
			$httpHeader[]='Authorization: Basic '.base64_encode($user.':'.$password);
		}
		if (count($httpHeader)>0) {
			curl_setopt($session, CURLOPT_HTTPHEADER, $httpHeader);
		}

		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		// Do the POST and then close the session
		$response = curl_exec($session);
		if (curl_errno($session) || strpos($response, 'HTTP/1.1 200 OK')===false) {
			echo 'cUrl POST request failed. Please check cUrl is installed on the server.';
			if (curl_errno($session))
			echo 'Error number: '.curl_errno($session).'';
			echo "Server response ";
			echo $response;
		} else {
			$offset = strpos($response, "\r\n\r\n");
			$headers = curl_getinfo($session);
			curl_close($session);
			$document =& JFactory::getDocument();
			$content_type = $headers['content_type'];
			if($_GET['download'])
			{
				$content_type = 'application/octet-stream';
				header('Content-Description: File Transfer');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				if (strpos($content_type, '/')!==false) {
					$fileType = array_pop(explode('/',$headers['content_type']));
				}
				header('Content-Disposition: attachment; filename=download.'.$fileType);

			}
			header("Content-Type: $content_type");
			if (array_key_exists('charset', $headers)) {
				$document->setCharset($headers['charset']);
			} else {
				//$document->setCharset(null);
			}
			// last part of response is the actual data
			$response = array_pop(explode("\r\n\r\n", $response));
			if($request == 'getfeatureinfo')
			{
				$document->setMimeEncoding('text/html');
				$xsltDoc = new DOMDocument();
				$xsltString = file_get_contents(JPATH_COMPONENT.DS.'resource'.DS.'xslt'.DS.'getFeatureInfo.xslt');
				$rnd = rand(10000,9999999);
				$xsltString = str_replace('fragment', "fragment$rnd", $xsltString);
				$xsltDoc->loadXML($xsltString);
				$proc = new XSLTProcessor();
				$proc->importStylesheet($xsltDoc);
				$doc = new DOMDocument();
				$doc->loadXML($response);
				$response = $proc->transformToXML($doc);
			}
			JResponse::setHeader( 'Content-length', strlen($response));
			echo $response;
		}
	}

	function contains($str, $content, $ignorecase=true) {
		if ($ignorecase){
			$str = strtolower($str);
			$content = strtolower($content);
		}
		return strpos($content,$str) ? true : false;
	}

}
?>