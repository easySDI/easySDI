<?php

/**
 * @version		4.4.0
 * @package     mod_easysdi_pendingrequests
 * @copyright	
 * @license		
 * @author		
 */
class modEasysdiPendingrequestsHelper {

    // This function takes a DateTime object and returns a formated string of the time difference relative to now
    public static function relTime(DateTime $date) {
        $current = new DateTime;
        $diff = $current->diff($date);
        $units = array("YEAR" => $diff->format("%y"),
            "MONTH" => $diff->format("%m"),
            "DAY" => $diff->format("%d"),
            "HOUR" => $diff->format("%h"),
            "MINUTE" => $diff->format("%i"),
            "SECOND" => $diff->format("%s"),
        );
        $out = JText::_('MOD_EASYSDI_PENDINGREQUESTS_TIME_NOW');
        foreach ($units as $unit => $amount) {
            if (empty($amount)) {
                continue;
            }
            $out = $amount . " " . ($amount == 1 ? $unit : $unit . "s") . " ago";
            $out = JText::plural('MOD_EASYSDI_PENDINGREQUESTS_TIME_' . $unit . '_AGO', $amount);
            break;
        }
        return $out;
    }

}

?>