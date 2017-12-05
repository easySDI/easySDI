<?php
/**
 * @version     4.4.5
 * @package     com_easysdi_catalog
 * @copyright   Copyright (C) 2013-2017. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

class FormUtils {

    /**
     * Remove index from a xpath
     * 
     * @param string $xpath
     * @param int $offset Si offset est non-négatif, la série commencera à cet offset dans le tableau array. Si offset est négatif, cette série commencera à l'offset offset, mais en commençant à la fin du tableau array.
     * @param int $length
     * @return string
     */
    public static function removeIndexFromXpath($xpath, $offset, $length = null) {
        $segments = explode('/', $xpath);
        $replacement = array_slice($segments, $offset, $length);

        for ($i = 0; $i < count($replacement); $i++) {
            $replacement[$i] = preg_replace('/[\[0-9\]*]/i', '', $replacement[$i]);
        }

        array_splice($segments, $offset, count($replacement), $replacement);
        
        return implode('/', $segments);
    }

    /**
     * Remove index to serialized xpath at a specific position
     * 
     * @param string $xpath
     * @param int $position
     * @return array
     */
    public static function removeIndexToXpath($xpath, $to = 4, $from = 7) {
        $arrayPath = array_reverse(explode('-', $xpath));
        if ($arrayPath[$to + 1] != 'ra') {
            return $xpath;
        }

        for ($i = $to; $i < $from + 1; $i++) {
            unset($arrayPath[$i]);
        }

        return implode('-', array_reverse($arrayPath));
    }

    /**
     * Serialze the Xpath
     * 
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     * 
     * @param string $xpath
     * @return string Serualized XPath
     */
    public static function serializeXpath($xpath) {
        $xpath = str_replace('[', '-la-', $xpath);
        $xpath = str_replace(']', '-ra-', $xpath);
        $xpath = str_replace('/', '-sla-', $xpath);
        $xpath = str_replace(':', '-dp-', $xpath);
        return $xpath;
    }

    /**
     * Unserialze the Xpath
     * 
     * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
     * @since 4.0
     * 
     * @param string $xpath
     * @return string Unserialized XPath
     */
    public static function unSerializeXpath($xpath) {
        $xpath = str_replace('-la-', '[', $xpath);
        $xpath = str_replace('-ra-', ']', $xpath);
        $xpath = str_replace('-sla-', '/', $xpath);
        $xpath = str_replace('-dp-', ':', $xpath);
        return $xpath;
    }

    /**
     * Remove index from XPath
     * 
     * @param string $xpath
     * @return string
     */
    public static function removeIndex($xpath) {
        return preg_replace('/[\[0-9\]*]/i', '', $xpath);
    }

}

?>
