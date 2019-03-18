/*
 * Copyright (C) 2017 arx iT
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
package org.easysdi.extract.utils;

import java.util.Calendar;
import org.easysdi.extract.utils.SimpleTemporalSpan.TemporalField;
import org.joda.time.Period;



/**
 * A set of helper functions to manipulate temporal objects.
 *
 * @author Yves Grasset
 */
public abstract class DateTimeUtils {

    /**
     * Gets the interval between two time points expressed in the floored value of the most significant
     * temporal field.
     * <p>
     * Examples:
     * May 25, 2014 16:35:54 - May 26, 2014 19:00:00 = 1 day
     * May 25, 2014 16:35:54 - May 26, 2014 10:00:00 = 17 hours
     *
     * @param start the time point where the interval starts
     * @param end   the time point where the interval ends
     * @return an simple temporal span object that contains the numeric value of the interval and the most significant
     *         field
     */
    public static final SimpleTemporalSpan getFloorDifference(final Calendar start, final Calendar end) {
        Period difference = new Period(start.getTimeInMillis(), end.getTimeInMillis());

        if (difference.getYears() > 0) {
            return new SimpleTemporalSpan(difference.getYears(), TemporalField.YEARS);
        }

        if (difference.getMonths() > 0) {
            return new SimpleTemporalSpan(difference.getMonths(), TemporalField.MONTHS);
        }

        if (difference.getWeeks() > 0) {
            return new SimpleTemporalSpan(difference.getWeeks(), TemporalField.WEEKS);
        }

        if (difference.getDays() > 0) {
            return new SimpleTemporalSpan(difference.getDays(), TemporalField.DAYS);
        }

        if (difference.getHours() > 0) {
            return new SimpleTemporalSpan(difference.getHours(), TemporalField.HOURS);
        }

        if (difference.getMinutes() > 0) {
            return new SimpleTemporalSpan(difference.getMinutes(), TemporalField.MINUTES);
        }

        return new SimpleTemporalSpan(difference.getSeconds(), TemporalField.SECONDS);
    }

}
