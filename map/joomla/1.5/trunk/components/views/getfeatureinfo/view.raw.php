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

class EasySDI_mapViewGetfeatureinfo extends JView {

	function display()
	{
		ob_end_clean();
		$db =& JFactory::getDBO();
		$query = "SELECT value FROM #__sdi_configuration c where name = 'pubWmsUrl' limit 1".
		$db->setQuery($query);
		$pubwmsURL = $db->getOne($query).'?';
		$pubwmsURL.=substr($_SERVER["QUERY_STRING"],52);
		$pubwmsURL = urldecode($pubwmsURL);
		$pubwmsURL = str_replace('info_format=text/html','info_format=application/vnd.ogc.gml', $pubwmsURL);
		$session = curl_init($pubwmsURL);
		$cookie = 'Cookie: ';
		$i = count($_COOKIE);
		foreach($_COOKIE as $key => $value)
		{
			$cookie.="$key=$value";
			$i--;
			if($i>0) $cookie.='; ';
		}
		curl_setopt($session, CURLOPT_HTTPHEADER, array($cookie, 'Referer: '.$_SERVER['HTTP_REFERER']));
		curl_setopt($session, CURLOPT_HEADER, true);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		if (curl_errno($session) || strpos($response, 'HTTP/1.1 200 OK')===false) {
			echo "getfeature info failed..\n$pubwmsURL";
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
			header("Content-Type: $content_type");
			if (array_key_exists('charset', $headers)) {
				$document->setCharset($headers['charset']);
			}
			// last part of response is the actual data
			$response = array_pop(explode("\r\n\r\n", $response));
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
			JResponse::setHeader( 'Content-length', strlen($response));
			echo $response;
		}
	}
}
?>