/**
 * Purpose: Set language parameters 
 * Author: Jachym Cepicky <jachym bnhelp cz>
 * URL: http://bnhelp.cz
 * Licence: GNU/LGPL v3
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */


/**
 * Class: HS
 * Base Help Service class with global methods and variables, common for
 * all applications
 *
 * Example:
 *
 * 1) define the strings in your source code:
 * (code)
 * HS.Lang[cze]["Halo"] = "Ahoj";
 * HS.Lang[cze]["World"] = "Svete";
 *
 * HS.Lang[eng]["nic"] = "foo";
 * HS.Lang[cze]["neco"] = "bar";
 * (end)
 *
 * 2) Set the default language in your application
 * (code)
 * HS.setLang(HS.getLastLangCode());
 * // or
 * HS.setLang("cze");
 * (end)
 *
 *  3) And use it
 * (code)
 *  ...
 *  label: HS.i18n("Halo"),
 *  ...
 * (end)
 *
 */
var HS = {

    /**
    * Property: lang
    * Current language code in ISO (3 characters) format. Default null.
    */
    lang : null,

    /**
    * Property: defaultLang
    * Default language code: "eng"
    */
    defaultLang : "eng",

    /**
    * Property: allLangsSet
    * Do all possible libraries (openlayers, custom, ...) set their languages
    * settings?
    */
    allLangsSet : false,

    /**
    * Property: Lang
    * Dictionary with various languages. e.g. HS.Lang["cze"], HS.Lang["eng"]
    * ...
    */
    Lang : {},

    /**
    * Function: i18n
    * Translation function
    *
    * Parameters:
    * code - {String} language code, in which you want the translation. If
    * ommited (the usual case), current language is taken.
    * key - {String} key for string, you want to get translation for.
    *
    * Example setting the application language:
    * (code)
    * if (!HS.lang) {
    *     var lastLang = HS.getLastLangCode();
    *     if (!lastLang) {
    *         lastLang = "czech";
    *     }
    *     HS.setLang(lastLang);
    * }
    * (end)
    */
    i18n : function() {

        if (!this.getLang()) {
            this.setLang(this.defaultLang);
        }

        var trans = null;
        var KEY = null;
        if (typeof(arguments[0]) == typeof({})) {
            trans = arguments[0];
            KEY = arguments[1];
        }
        else {
            trans = this.Lang;
            KEY = arguments[0];
        }

        // search the translation 
        var retString = "";
        for (var lang in trans) {
            if (lang == this.lang) {
                retString =  trans[this.lang][KEY];
            }
        }

        return (retString ? retString : KEY);
    },

    /**
    * Function: setLang
    * Set current language according to given code. If not available, "eng"
    * will be used. <setCookie> will  be called as well.
    *
    * Parameters:
    * code - {String} language code. Can be *any* form (e.g.
    *  "česky","cs","cze", ...). 
    * saveToCookie - {Boolean},  default: false
    *
    *
    * Return:
    * {Boolean} it worked, or not
    */
    setLang : function(code,saveToCookie) {
        this.initLangs()

        if (this.allLangsSet == false) {
            
            var hsset = false;
            var olset = false;

            var hscode = null;
            var olcode = null;
            // get HS languega code
            for (var l in this.langs) {
                breakthis = false;
                var keys = this.langs[l];
                for (var i = 0; i < keys.length; i++) {
                    if (keys[i] == code) {
                        hscode = l.split(";")[0];
                        olcode = this.getOLCode(hscode);
                        breakthis = true;
                        break;
                    }
                }

                if (breakthis) { //found
                    break;
                }
            }
            
            if (hscode == null) {
                hscode = "eng";
                olcode = "en";
            }

            // set lang
            this.lang = hscode;

            if (saveToCookie == true) {
                this.setCookie("lang",hscode);
            }

            hsset = true;

            if (window.OpenLayers && window.OpenLayers.Lang) {
                OpenLayers.Lang.setCode(olcode);
                olset = true;
            }

            if (olset && hsset) {
            }
        }
            
        return true;
    },


    /**
    * Function: getLang
    * Get current language code.
    *
    * Parameters:
    * type - {String} *2* *3* *"ol"*
    * 
    * Return:
    * code - {String} in 3 characters format
    */
    getLang : function(type) {

        if (!type) {
            type = 3;
        }

        if (!this.lang) {
            return null;
        }
        else {
            return this.getCodeFromLanguage(this.lang,type);
        }
    },

    /**
    * Function: getLastLangCode
    * Get language code from URL or Cookie. URL first, if not found, cookie
    * last. If not found, null returned
    *
    * Returns:
    * null or {String} 3 characters code
    */
    getLastLangCode : function() {

        var code = null;
        /* parse link location */
        if (window.location.search.length > 0) {
            var search = window.location.search;
            var params = search.substr(1,search.length); /* without "?" */
            params = params.split("&"); 
            for (var i = 0; i < params.length; i++) {
                var param = params[i].split("=");

                /* language */
                if (param[0] == "lang") {
                    code = this.getCodeFromLanguage(param[1],3); 
                }
            }
        }

        if (!code) {
            try {
                    code = this.getCookie("lang");
                    code = this.getCodeFromLanguage(code);
            }
            catch(e) {}
        }
        
        return code;
    },

    /**
    * Function: getCodeFromLanguage
    * Get desired code 
    * Params:
    * code {String} anything, that identifies the language, from "eng" to * "cesky"
    * type {String} currenly: 2: en, 3: eng, "ol":  en
    */
    getCodeFromLanguage : function(code,type) {
        
        if (!type) {
            type = 3;
        }

        for (var l in this.langs) {
            breakthis = false;
            var keys = this.langs[l];
            for (var i = 0; i < keys.length; i++) {
                if (keys[i] == code) {
                    codes = l.split(";");


                    switch(type) {
                        case 2: return codes[2]; break;
                        case 3: return codes[0]; break;
                        case "ol": return codes[0]; break;
                        default: return "eng"; break;
                    }
                }
            }
        }
        return null;
    },

    /**
    * Function: setCookie
    * Set the cookie of given name with given value.
    * Called from <setLang>
    *
    * Parameters:
    * c_name - {String}  name of the cookie
    * value - {String} value of the cookie
    * expiredays - {Integer} when it expires, default is never
    */
    setCookie : function(c_name,value,expiredays) {
        var exdate=new Date();
        exdate.setDate(exdate.getDate()+expiredays);
        document.cookie=c_name+ "=" +escape(value)+
        ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
    },

    /**
    * Function: getCookie
    * Get value of the cookie with given name. Used in <getLang>
    *
    * Parameters:
    * c_name - {String} name of the cookie
    * 
    * Returns:
    * {String} or ""
    */
    getCookie : function(c_name) {
        if (document.cookie.length>0) {
            c_start=document.cookie.indexOf(c_name + "=");
            if (c_start!=-1) { 
                c_start=c_start + c_name.length+1; 
                c_end=document.cookie.indexOf(";",c_start);
                if (c_end==-1) c_end=document.cookie.length;
                return unescape(document.cookie.substring(c_start,c_end));
            } 
        }
        return "";
    },

    /**
    * Property: langs
    * List of langue codes in various formats.
    *
    * Format:
    * (code) 
    * {
    *      "ISO 639-2 (3 letters);OpenLayers identification; ISO 639-1 (2 letters)":[list of strings, which should be identified by this language]
    * }
    * (end)
    *
    * Example:
    * (code)
    * {
    *  "cze;cs-CZ;cs":["cz","cesky","cestina","jak_mi_zobak_narost"]
    * }
    * (end)
    */
    langs : {
                "eng;en;en":    ["en","eng","english"],
                "ger;de;de":    ["de","ger","deutsch"],
                "fre;fr;fr":    ["fr","fre","france"],
                "pol;pl;pl":    ["pl","pol","polska"],
                "ita:it;it":    ["it","ita","italiano","italien"],
                "rus:ru;ru":    ["rus","ru","russe"],
                "spa:es:spa":   ["es","spa","espagnol","castillan"],
                "slk:sk:sk":    ["slk","slo","sk","slovensky","slovak"],
                "cze;cs-CZ;cs": ["cz","cze","cs-CZ","czech","cesky","cs"],
                "lav;lv-LV;lv": ["lv","lav","lv-LV","latvian","latv"]
    },

    /**
    * Method: initLangs
    * Initialize <HS.Lang> property for all available languages
    */
    initLangs : function() {
        for (var lang in this.langs) {
            var hsLangName = lang.split(";")[0];
            var olangName = lang.split(";")[1];
            if (!this.Lang[hsLangName]) {
                this.Lang[hsLangName] = {};
            }
            try {
                if (window.OpenLayers && !OpenLayers.Lang[olangName]) {
                    OpenLayers.Lang[olangName] = {};
                }
            } catch(e) {}
        }
    },

    getOLCode : function(code) {
        for (var c in this.langs) {
            var codes = c.split(";");
            if (code == codes[0]){
                return (codes.length > 1 ? codes[1] : codes[0]);
            }
        }
        return "en";
    },

    /**
    * Function: setDefaultLanguage
    * Set derault value for this application and init langs. Use this method
    * for initial lanuage settings.
    */
    setDefaultLanguage : function() {
        var lastLang = this.getLastLangCode();
        if (!lastLang) {
            lastLang = this.defaultLang;
        }
        this.setLang(lastLang);
    }

};
/* Init
    */
HS.initLangs();
