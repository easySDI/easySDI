<?php

/**
 * @version     4.4.5
 * @package     com_easysdi_core
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

/**
 * represent the current version of easySDI installed, uses the php
 * revision infos created on build or sync time (ant script) : "sdiBuildInfos.php"
 */
class sdiVersion {

    /**
     * Static stored version (eg: 4.4.0)
     * @var string 
     */
    private static $easySdiVersion = null;

    /**
     * Static stored svn revision (eg: 9458)
     * @var string 
     */
    private static $easySdiRevision = null;

    /**
     * Static stored Full version (eg: 4.4.0-9458)
     * @var string 
     */
    private static $easySdiFullVersion = null;

    /**
     * get easySDI version 
     * @return string (eg: 4.4.0)
     */
    public function getSdiVersion() {
        if (is_null(self::$easySdiVersion)) {
            $this->_loadVersionInfos();
        }
        return self::$easySdiVersion;
    }

    /**
     * get easySDI svn revision
     * @return string (eg: 9458)
     */
    public function getSdiRevision() {
        if (is_null(self::$easySdiRevision)) {
            $this->_loadVersionInfos();
        }
        return self::$easySdiRevision;
    }

    /**
     * get easySDI Full version 
     * @return string (eg: 4.4.0-9458)
     * [version]-[revision]
     */
    public function getSdiFullVersion() {
        if (is_null(self::$easySdiFullVersion)) {
            $this->_loadVersionInfos();
        }
        return self::$easySdiFullVersion;
    }

    /**
     * Load version and revision from the build generated file in
     * /components/com_easysdi_core/libraries/easysdi/common/sdiBuildInfos.php
     */
    private function _loadVersionInfos() {
        require_once JPATH_ADMINISTRATOR . '/components/com_easysdi_core/libraries/easysdi/common/sdiBuildInfos.php';
        self::$easySdiVersion = $sdi_build_version;
        self::$easySdiRevision = $sdi_build_revision;
        self::$easySdiFullVersion = self::$easySdiVersion . '-' . self::$easySdiRevision;
    }

}

?>
