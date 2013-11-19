<?php

/**
 * Description of FormUtils
 *
 * @author Marc Battaglia <marc.battaglia@depth.ch>
 */
class FormUtils {
    
    /**
     * Remove index to xpath at a specific position
     * 
     * @param string $xpath
     * @param int $position
     * @return array
     */
    public static function removeIndexToXpath($xpath, $to = 4, $from = 7) {
        $arrayPath = array_reverse(explode('-', $xpath));
        if($arrayPath[$to+1] != 'ra'){
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
     * @author Depth S.A.
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
     * @author Depth S.A.
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
