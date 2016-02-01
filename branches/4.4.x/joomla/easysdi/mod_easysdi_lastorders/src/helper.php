<?php

/**
 * @version     4.3.2
 * @package     mod_easysdi_lastorders
 * @copyright   Copyright (C) 2013-2015. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 * @author      EasySDI Community <contact@easysdi.org> - http://www.easysdi.org
 */

class modEasysdiLastordersHelper {

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
        $out = JText::_('MOD_EASYSDI_LASTORDERS_TIME_NOW');
        foreach ($units as $unit => $amount) {
            if (empty($amount)) {
                continue;
            }
            $out = $amount . " " . ($amount == 1 ? $unit : $unit . "s") . " ago";
            $out = JText::plural('MOD_EASYSDI_LASTORDERS_TIME_'.$unit.'_AGO',$amount);
            break;
        }
        return $out;
    }

}

?>