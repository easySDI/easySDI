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

/**
 * PHP script to emit Joomla's language strings into JavaScript.
 */

$document = &JFactory::getDocument();

// Now generate server-side code to be used by the javascript
// Language files
$lang = &JFactory::getLanguage();
$i18n = $lang->_strings;
$locale = explode('-', $lang->_lang);
$locale = $locale[0];

$s = "Ext.namespace('SData', 'EasySDI_Pub');
/* I18n late implementation */

var i18n = function()
{
	this._getLocalisedStringFunction = function(x){return x;};
};

i18n.prototype.getLocal = function(key)
{
	return this._getLocalisedStringFunction(key);
};

i18n.prototype.setHandler = function(handler)
{
	this._getLocalisedStringFunction = handler;
};

/*
* Add an i18n instance to the namespace.
*/
EasySDI_Pub.fqlocale = '$lang->_lang';
EasySDI_Pub.locale = '$locale';
EasySDI_Pub.lang = new i18n();
SData.i18n = {};";
$i = 0;
foreach ($i18n as $key => $val)
{
	//file_put_contents('c:/i18n.txt', "$key=$val\n", FILE_APPEND);
	// We use the $ prefix to indicate this should be passed to the javascript
	if (substr($key, 0, 1) == '$')
	{
		$key = addslashes(substr($key,1));
		$val = addslashes($val);
		$s .= "SData.i18n['$key'] = '$val';";
	}
}

$s .= "\nEasySDI_Pub.lang.setHandler(function(key) {
 return (typeof SData.i18n[key] !== 'undefined') ? SData.i18n[key] : key;
 });\n";

$document->addScriptDeclaration($s);
?>
