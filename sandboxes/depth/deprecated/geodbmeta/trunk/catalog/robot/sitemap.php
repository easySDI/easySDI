<?php
/*
* G�n�ration du sitemap des m�tadonn�es visibles pour le public
*/
	// Charger la configuration de Joomla pour un acc�s � la base de donn�es mysql
	require_once ('configuration.php');
	$jconfig = new JConfig(); 
	
	// Connection � la base de donn�es
	$db =& mysql_pconnect($jconfig->host, $jconfig->user, $jconfig->password);
	if (!$db) 
	{
	   die('Impossible de se connecter : ' . mysql_error());
	}
	$db_selected = mysql_select_db($jconfig->db, $db);
	if (!$db_selected) {
	   die ('Impossible de s�lectionner la base de donn�es : ' . mysql_error());
	}
	
	/* D�but du code de g�n�ration du sitemap.xml */
	
	// URL d'acc�s � chaque m�tadonn�e
	if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) {
		$https = 's://';
	} else {
		$https = '://';
	}
	
	$root = "http".$https.substr($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'], '/'));
//	$url = $root."/index.php?tmpl=component&amp;option=com_easysdi_catalog&amp;task=showMetadata&amp;type=complete&amp;id=";
	
	// Cr�ation d'un DOMDocument
	$XMLDoc = new DOMDocument('1.0', 'UTF-8');
	$XMLDoc->formatOutput = true;
	
	// Noeud racine
	$XMLRoot = $XMLDoc->createElement("urlset");
	$XMLDoc->appendChild($XMLRoot);
	$XMLRoot->setAttribute('xmlns', "http://www.sitemaps.org/schemas/sitemap/0.9");
	
	// R�cup�rer toutes les m�tadonn�es dont le statut de publication est "public"
	$mdList=array();
	$query = "	SELECT m.guid, m.updated, ot.code, ot.sitemapParams
				FROM #__sdi_metadata m
				INNER JOIN #__sdi_objectversion ov ON ov.metadata_id=m.id
				INNER JOIN #__sdi_object o ON o.id=ov.object_id
				INNER JOIN #__sdi_objecttype ot ON ot.id =o.objecttype_id
				INNER JOIN #__sdi_list_visibility v ON o.visibility_id=v.id
				INNER JOIN #__sdi_list_metadatastate ms ON m.metadatastate_id=ms.id
				WHERE v.code='public'
					  AND ms.code='published'
					  AND m.published <= '".date('Y-m-d')."'";
	$query = replacePrefix($query, $jconfig->dbprefix);
	$mdList = loadObjectList($query);
	
	// Parcours des m�tadonn�es pour la cr�ation de chaque noeud XML
	foreach ($mdList as $md)
	{
		// Noeud principal
		if( strpos($md->code, "contact") === FALSE){
			$XMLUrl = $XMLDoc->createElement("url");
			$XMLRoot->appendChild($XMLUrl);
			
			//URL de la fiche de m�tadonn�e compl�te d'EasySDI
			$XMLLoc = $XMLDoc->createElement("loc", htmlspecialchars($root."/index.php?".$md->sitemapParams."&id=".$md->guid));
			$XMLUrl->appendChild($XMLLoc);
			
			// Date modification de la m�tadonn�e
			$updated = $md->updated;
			if ($updated <> "")
				$updated = date('Y-m-d', strtotime($updated));
			$XMLLastMod = $XMLDoc->createElement("lastmod", $updated);
			$XMLUrl->appendChild($XMLLastMod);
			
			// Fr�quence de modification
			$XMLChangeFreq = $XMLDoc->createElement("changefreq", "always");
			$XMLUrl->appendChild($XMLChangeFreq);
			
			// Priorit�
			$XMLPriority = $XMLDoc->createElement("priority", "0.5");
			$XMLUrl->appendChild($XMLPriority);
		}
	}
	
	// Affichage du sitemap.xml
	echo $XMLDoc->saveXML();

	// Fermeture de la connection � la base de donn�es
	mysql_close($db);
	
	/* Fonctions pour all�ger le code ci-dessus*/
	
	/** 
	* Fonction reprise de database.php dans libraries\joomla\database\
	*
	 * This function replaces a string identifier <var>$prefix</var> with the
	 * string held is the <var>_table_prefix</var> class variable.
	 *
	 * @access public
	 * @param string The SQL query
	 * @param string The common table prefix
	 */
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
	* Construction d'un set de r�sultat sous forme d'objets, inspir� de loadObjectList dans 
	* mysql.php de joomla, libraries\joomla\database\database\
	*/
	function loadObjectList($query)
	{
		$result = mysql_query($query);
		if (!$result) 
		{
			$message  = 'Requ�te invalide : ' . mysql_error() . "\n";
			$message .= 'Requ�te compl�te : ' . $query;
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
?>