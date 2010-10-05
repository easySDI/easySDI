<?php
/**
 * EasySDI, a solution to implement easily any spatial data infrastructure
 * Copyright (C) 2008 DEPTH SA, Chemin d'Arche 40b, CH-1870 Monthey, easysdi@depth.ch 
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

class reportEngine{
	
	function getReport()
	{

		// Charger la configuration de Joomla pour un accès à la base de données mysql
		//echo dirname(__FILE__).'\..\..\..\..\..\configuration.php';
		//require_once (dirname(__FILE__).'\..\..\..\..\..\configuration.php');
		require_once ('configuration.php');
		//require_once (dirname(__FILE__).'\..\..\..\..\..\libraries\joomla\environment\request.php');
		$jconfig = new JConfig(); 
		
		// Connection à la base de données
		/*$database =& mysql_pconnect($jconfig->host, $jconfig->user, $jconfig->password);
		if (!$database) 
		{
		   die('Impossible de se connecter : ' . mysql_error());
		}
		$db_selected = mysql_select_db($jconfig->db, $database);
		if (!$db_selected) {
		   die ('Impossible de sélectionner la base de données : ' . mysql_error());
		}*/
		$database=& JFactory::getDBO();
		
		/* Début du code de génération du rapport */
		$query="";
		$results=array();
		
		// Récupération des paramètres
		$params = array();
		//$objecttype_code = JRequest::getVar ('metadatatype', '');
		$objecttype_code = $_GET['metadatatype'];
		$params['metadatatype'] = $objecttype_code;
		//$format = JRequest::getVar ('format', '');
		$format = $_GET['format'];
		$params['format'] = $format;
		//$reporttype = JRequest::getVar ('reporttype', '');
		$reporttype = $_GET['reporttype'];
		$params['reporttype'] = $reporttype;
		//$metadata_guid = JRequest::getVar ('metadata_guid', array(0));
		$metadata_guid = $_GET['metadata_guid'];
		$params['metadata_guid'] = $metadata_guid;
		//$language = JRequest::getVar ('language', '');
		$language = $_GET['language'];
		$params['language'] = $language;
		//$lastVersion = JRequest::getVar ('lastVersion', '');
		$lastVersion = $_GET['lastVersion'];
		$params['lastVersion'] = $lastVersion;
		echo "================ PARAMETRES =================<br>";
		print_r($params);
		echo "<br>=============================================<br>";
		// Contrôler que tous les paramètres soient renseignés
		foreach ($params as $key => $param)
		{
			if ($param == '' or count($param) == 0)
			{
				// Retourner une erreur au format XML
				/*$style->load(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easysdi_core'.DS.'xsl'.DS.'XHTML_GETREPORT_MISSINGPARAMETER.xsl');
				$processor->importStylesheet($style);
				$xmlToHtml = $processor->transformToXml($xml);
				printf($document);*/
				echo "PARAMETRE ".$key." MANQUANT";
				exit;
			}
		}
		
		// Rassembler les guids de métadonnées indiqués en une string de la forme (guid1, guid2, guid3, ..., guidn)
		$guids = "";
		foreach ($metadata_guid as &$mg)
		{
			$mg="'".$mg."'";
		}
		$guids = implode(",", $metadata_guid);
		echo "=============== Liste de GUIDS ==============<br>";
		print_r($guids);
		echo "<br>=============================================<br>";
		// Récupérer tous les guids qui sont publics
		$query = "	SELECT m.guid as metadata_guid, o.id as object_id
					FROM #__sdi_metadata m
					INNER JOIN #__sdi_objectversion ov ON ov.metadata_id=m.id
					INNER JOIN #__sdi_object o ON ov.object_id=o.id
					INNER JOIN #__sdi_objecttype ot ON o.objecttype_id=ot.id
					INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
					WHERE v.code='public'
				";
		echo "================== Query 1 ==================<br>";
		echo $query."<br>";
		echo "=============================================<br>";
		
		// Qui sont indiqués dans le paramètre metadata_guid
		$query .= " AND m.guid IN (".$guids.")";
		
		echo "================== Query 2 ==================<br>";
		echo $query."<br>";
		echo "=============================================<br>";
		
		
		// Qui sont du type indiqué dans le paramètre metadatatype
		$query .= " AND ot.code = '".$objecttype_code."'";

		echo "================== Query 3 ==================<br>";
		echo $query."<br>";
		echo "=============================================<br>";
		
		//$query = replacePrefix($query, $jconfig->dbprefix);
		//$results = loadObjectList($query);
		$database->setQuery($query);
		$results = $database->loadObjectList();
		
		echo "================== RESULTS ==================<br>";
		print_r($results);
		echo "<br>=============================================<br>";
		
		// Si c'est la dernière version qui est demandée, il faut faire des traitements supplémentaires
		if ($lastVersion)
		{
			foreach ($results as &$result)
			{
				// Pour chaque métadonnée qui a satisfait aux critères précédents, trouver sa dernière version
				$query = "SELECT m.guid as metadata_guid 
									  FROM #__sdi_objectversion ov 
									  INNER JOIN #__sdi_metadata m ON ov.metadata_id=m.id
									  WHERE ov.object_id=".$result->object_id." 
									  ORDER BY ov.created DESC";
				
				echo "================== Query 4 ==================<br>";
				echo $query."<br>";
				echo "=============================================<br>";
				
				$query = replacePrefix($query, $jconfig->dbprefix);
				$result->metadata_guid = loadResult($query);
				//$result->metadata_guid = $database->loadResult();
			}
		}
		
		echo "============= GUIDS a recuperer ================<br>";
		print_r($results);
		echo "<br>=============================================<br>";		
		
		$filter = "";
		foreach ($results as $result)
		{
			$filter  .= "<ogc:PropertyIsEqualTo><ogc:PropertyName>fileId</ogc:PropertyName><ogc:Literal>".$result->metadata_guid."</ogc:Literal></ogc:PropertyIsEqualTo>";
		}
		if (count($results) > 1)
			$filter = "<ogc:Or>".$filter."</ogc:Or>";

		echo "===================== FILTRE ========================<br>";
		print_r(htmlspecialchars($filter));
		echo "<br>=============================================<br>";

		// Construire une requête Geonetwork GetRecords pour demander les métadonnées choisies pour le rapport
		//Bug: If we have accents, we must specify ISO-8859-1
		$req = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
		$req .= "";
		
		//Get Records section
		$req .=  "<csw:GetRecords xmlns:csw=\"http://www.opengis.net/cat/csw/2.0.2\" service=\"CSW\" version=\"2.0.2\" resultType=\"results\" outputSchema=\"csw:IsoRecord\" content=\"COMPLETE\" ";
		$req .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gmd=\"http://www.isotc211.org/2005/gmd\" xsi:schemaLocation=\"http://www.opengis.net/cat/csw/2.0.2 http://schemas.opengis.net/csw/2.0.2/CSW-discovery.xsd\">\r\n";
	
		//Query section
		//Types name
		$req .= "<csw:Query typeNames=\"datasetcollection dataset application service\">";
		//ElementSetName
		$req .= "<csw:ElementSetName>full</csw:ElementSetName>";
		//ConstraintVersion
		$req .="<csw:Constraint version=\"1.1.0\">";
		//filter
		$req .= "<ogc:Filter xmlns:ogc=\"http://www.opengis.net/ogc\" xmlns:gml=\"http://www.opengis.net/gml\">";
		$req .= $filter;
		$req .= "</ogc:Filter>";
		$req .= "</csw:Constraint>";
		$req .= "</csw:Query>";
		$req .= "</csw:GetRecords>";
		
		$req = utf8_encode($req);

		echo "==================== REQUETE =======================<br>";
		print_r(htmlspecialchars($req));
		echo "<br>=============================================<br>";

		// Envoi de la requête
		//$catalogUrlBase = config_easysdi::getValue("catalog_url");
		//$xmlResponse = ADMIN_metadata::CURLRequest("POST", $catalogUrlBase,$req);



		function replacePrefix( $sql, $table_prefix, $prefix='#__' )
	{
		$sql = trim( $sql );

		$escaped = false;
		$quoteChar = '';

		$n = strlen( $sql );

		$startPos = 0;
		$literal = '';
		while ($startPos < $n) {
			$ip = strpos($sql, $prefix, $startPos);
			if ($ip === false) {
				break;
			}

			$j = strpos( $sql, "'", $startPos );
			$k = strpos( $sql, '"', $startPos );
			if (($k !== FALSE) && (($k < $j) || ($j === FALSE))) {
				$quoteChar	= '"';
				$j			= $k;
			} else {
				$quoteChar	= "'";
			}

			if ($j === false) {
				$j = $n;
			}

			$literal .= str_replace( $prefix, $table_prefix,substr( $sql, $startPos, $j - $startPos ) );
			$startPos = $j;

			$j = $startPos + 1;

			if ($j >= $n) {
				break;
			}

			// quote comes first, find end of quote
			while (TRUE) {
				$k = strpos( $sql, $quoteChar, $j );
				$escaped = false;
				if ($k === false) {
					break;
				}
				$l = $k - 1;
				while ($l >= 0 && $sql{$l} == '\\') {
					$l--;
					$escaped = !$escaped;
				}
				if ($escaped) {
					$j	= $k+1;
					continue;
				}
				break;
			}
			if ($k === FALSE) {
				// error in the query - no end quote; ignore it
				break;
			}
			$literal .= substr( $sql, $startPos, $k - $startPos + 1 );
			$startPos = $k+1;
		}
		if ($startPos < $n) {
			$literal .= substr( $sql, $startPos, $n - $startPos );
		}
		return $literal;
	}
	
	/*
	* Construction d'un set de résultat sous forme d'objets, inspiré de loadObjectList dans 
	* mysql.php de joomla, libraries\joomla\database\database\
	*/
	function loadObjectList($query)
	{
		$result = mysql_query($query);
		if (!$result) 
		{
			$message  = 'Requête invalide : ' . mysql_error() . "\n";
			$message .= 'Requête complète : ' . $query;
			die($message);
		}
		$array = array();
		while ($row = mysql_fetch_object( $result )) 
		{
			$array[] = $row;
		}
		mysql_free_result( $result );
		
		return $array;
	}

	/**
	 * This method loads the first field of the first row returned by the query.
	 *
	 * @access	public
	 * @return The value returned in the query or null if the query failed.
	 */
	function loadResult($query)
	{
		$result = mysql_query($query);

		if (!$result) {
			$message  = 'Requête invalide : ' . mysql_error() . "\n";
			$message .= 'Requête complète : ' . $query;
			die($message);
		}
		$ret = null;
		if ($row = mysql_fetch_row( $result )) {
			$ret = $row[0];
		}
		mysql_free_result( $result );
		return $ret;
	}

	}
}

?>