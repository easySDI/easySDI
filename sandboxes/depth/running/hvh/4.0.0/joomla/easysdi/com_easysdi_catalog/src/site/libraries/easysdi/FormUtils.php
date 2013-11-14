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
    
}

?>
