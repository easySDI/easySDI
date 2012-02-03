 xquery version '1.0';
 let $doc := doc('<XMLFILE_TO_REPLACE>')
 for $x in $doc /gmd:MD_Metadata
 return $x/gmd:identificationInfo/gmd:MD_DataIdentification/gmd:abstract/gco:CharacterString