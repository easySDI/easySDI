/*   Copyright 2006 - 2009 ETH Zurich 
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

package ch.ethz.mxquery.datamodel;

import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.Date;
import java.util.SimpleTimeZone;
import java.util.StringTokenizer;
import java.util.TimeZone;

import ch.ethz.mxquery.exceptions.DynamicException;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;

/**
 * @author rokast Specification at http://www.w3.org/TR/xmlschema11-2/#dateTime
 *         dateTimeLexicalRep ::= yearFrag '-' monthFrag '-' dayFrag
 *         'T'((hourFrag ':' minuteFrag ':' secondFrag) | endOfDayFrag)
 *         timezoneFrag?
 * 
 * Note: Current implementation ignores time zones! Time zones are not
 * validated!
 * 
 * Implemented operators: A + B xs:dateTime xs:dayTimeDuration
 * op:add-dayTimeDuration-to-dateTime(A, B) xs:dateTime A - B xs:dateTime
 * xs:dateTime op:subtract-dateTimes(A, B) xs:dayTimeDuration A - B xs:dateTime
 * xs:dayTimeDuration op:subtract-dayTimeDuration-from-dateTime(A, B)
 * xs:dateTime A + B xs:dateTime xs:yearMonthDuration
 * op:add-yearMonthDuration-to-dateTime(A, B) xs:dateTime A - B xs:dateTime
 * xs:yearMonthDuration op:subtract-yearMonthDuration-from-dateTime(A, B)
 * xs:dateTime
 * 
 * A eq B xs:dateTime xs:dateTime op:datetime-equal(A, B) xs:boolean A ne B
 * xs:dateTime xs:dateTime fn:not(op:datetime-equal(A, B)) xs:boolean A
 * gt,lt,ge,le B xs:dateTime xs:dateTime xs:boolean
 * 
 * 
 */

/**
 * Note: Saxon implementation (several functions) used for parsing input string
 */
public class MXQueryDateTime {

    public static final int VALUE_TYPE_TIME = 1;
    public static final int VALUE_TYPE_DATE = 2;
    public static final int VALUE_TYPE_DATE_TIME = 3;

    public static final int NO_TIME_ZONE_DEFINED = Integer.MIN_VALUE;

    // default date
    private int year = 2000; // the year as written, +1 for BC years
    private byte month = 1; // the month as written, range 1-12
    private byte day = 1; // the day as written, range 1-31
    private byte hour = 0; // the hour as written (except for midnight), range
    // 0-23
    private byte minute = 0; // the minutes as written, range 0-59
    private byte second = 0; // the seconds as written, range 0-59 (no leap
    // seconds)
    private int microsecond = 0;
    private int tzMinutes = NO_TIME_ZONE_DEFINED;

    private Calendar c;
    private int type = VALUE_TYPE_DATE_TIME;

    public MXQueryDateTime(int year, byte month, byte day, byte hour,
	    byte minute, byte second, int microsecond, int timeZoneMin, int type) {
	this.year = year;
	this.month = month;
	this.day = day;
	this.hour = hour;
	this.minute = minute;
	this.second = second;
	this.microsecond = microsecond;
	this.tzMinutes = timeZoneMin;
	this.type = type;
	if (this.hour == 24)
	    this.hour = 0;
	this.c = createCalendar();
    }

    public MXQueryDateTime(Calendar pCal, int timeZoneMin, int type) {
	if (type == VALUE_TYPE_TIME) {
	    this.year = 2000;
	    this.month = 1;
	    this.day = 1;
	    pCal.set(Calendar.YEAR, 2000);
	    pCal.set(Calendar.MONTH, 0);
	    pCal.set(Calendar.DAY_OF_MONTH, 1);
	} else {
	    int yr = pCal.get(Calendar.YEAR);

	    if (pCal.get(Calendar.ERA) == GregorianCalendar.BC) {
		this.year = 1 - yr;
	    } else {
		this.year = yr;
	    }

	    this.month = (byte) (pCal.get(Calendar.MONTH) + 1);
	    this.day = (byte) pCal.get(Calendar.DAY_OF_MONTH);
	}
	this.hour = (byte) pCal.get(Calendar.HOUR_OF_DAY);
	this.minute = (byte) pCal.get(Calendar.MINUTE);
	this.second = (byte) pCal.get(Calendar.SECOND);
	this.microsecond = pCal.get(Calendar.MILLISECOND) * 1000;
	this.tzMinutes = timeZoneMin;
	this.type = type;
	c = pCal;
    }

    private static int getTimezone(int tzDate, int tzTime)
	    throws MXQueryException {

	int tzRes;
	if (tzDate != NO_TIME_ZONE_DEFINED && tzTime != NO_TIME_ZONE_DEFINED) {
	    if (tzDate == tzTime)
		tzRes = tzDate;
	    else
		throw new DynamicException(
			ErrorCodes.F0029_BOTH_ARGUMENTS_TO_DATETIME_HAVE_SPECIFIC_TIMEZONE,
			"Different timezones for xs:dateTime constructor", null);
	}

	if (tzDate == NO_TIME_ZONE_DEFINED)
	    tzRes = tzTime;
	else
	    tzRes = tzDate;

	return tzRes;
    }

    public MXQueryDateTime(MXQueryDate datePart, MXQueryTime timePart)
	    throws MXQueryException {
	this(datePart.getYear(), (byte) (datePart.getMonth()), (byte) datePart
		.getDay(), (byte) timePart.getHours(), (byte) timePart
		.getMinutes(), (byte) timePart.getSeconds(), timePart
		.getMiliseconds(), getTimezone(datePart.getTimezoneInMinutes(),
		timePart.getTimezoneInMinutes()), VALUE_TYPE_DATE_TIME);
    }

    /**
     * Constructor: create a dateTime value from a supplied string, in ISO 8601
     * format
     */

    public MXQueryDateTime(String s, int pType) throws MXQueryException {
	// xs:dateTime input must have format
	// [-]yyyy-mm-ddThh:mm:ss[.fff*][([+|-]hh:mm | Z)]
	// xs:date input must have format [-]yyyy-mm-dd[([+|-]hh:mm | Z)]
	// xs:time input must have format hh:mm:ss[.fff*][([+|-]hh:mm | Z)]
	type = pType;
	StringTokenizer tok = new StringTokenizer(s.trim(), "-:.+TZ", true);
	try {

	    if (!tok.hasMoreElements()) {
		badDate("too short", s);
	    }
	    String part = (String) tok.nextElement();

	    if (pType == MXQueryDateTime.VALUE_TYPE_DATE
		    || pType == MXQueryDateTime.VALUE_TYPE_DATE_TIME) {

		int era = +1;
		if ("+".equals(part)) {
		    badDate("Date may not start with '+' sign", s);
		} else if ("-".equals(part)) {
		    era = -1;
		    part = (String) tok.nextElement();
		}
		year = Integer.parseInt(part) * era;
		if (part.length() < 4) {
		    badDate("Year is less than four digits", s);
		}
		if (part.length() > 4 && part.charAt(0) == '0') {
		    badDate(
			    "When year exceeds 4 digits, leading zeroes are not allowed",
			    s);
		}
		if (year == 0) {
		    badDate("Year zero is not allowed", s);
		}
		if (era < 0) {
		    year++; // internal representation allows a year zero.
		}
		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		if (!"-".equals(tok.nextElement()))
		    badDate("Wrong delimiter after year", s);

		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		part = (String) tok.nextElement();
		if (part.length() != 2)
		    badDate("Month must be two digits", s);
		month = (byte) Integer.parseInt(part);
		if (month < 1 || month > 12)
		    badDate("Month is out of range", s);

		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		if (!"-".equals(tok.nextElement()))
		    badDate("Wrong delimiter after month", s);
		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		part = (String) tok.nextElement();
		if (part.length() != 2)
		    badDate("Day must be two digits", s);
		day = (byte) Integer.parseInt(part);
		if (day < 1 || day > 31)
		    badDate("Day is out of range", s);
	    }

	    if (pType == MXQueryDateTime.VALUE_TYPE_DATE_TIME) {
		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		if (!"T".equals(tok.nextElement()))
		    badDate("Wrong delimiter after day", s);

		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		part = (String) tok.nextElement();
	    }

	    if (pType == MXQueryDateTime.VALUE_TYPE_DATE_TIME
		    || pType == MXQueryDateTime.VALUE_TYPE_TIME) {

		if (part.length() != 2)
		    badDate("Hour must be two digits", s);
		hour = (byte) Integer.parseInt(part);
		if (hour > 24)
		    badDate("Hour is out of range", s);

		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		if (!":".equals(tok.nextElement()))
		    badDate("Wrong delimiter after hour", s);

		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		part = (String) tok.nextElement();
		if (part.length() != 2)
		    badDate("Minute must be two digits", s);
		minute = (byte) Integer.parseInt(part);
		if (minute > 59)
		    badDate("Minute is out of range", s);
		if (hour == 24 && minute != 0)
		    badDate("If hour is 24, minute must be 00", s);
		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		if (!":".equals(tok.nextElement()))
		    badDate("Wrong delimiter after minute", s);

		if (!tok.hasMoreElements())
		    badDate("Too short", s);
		part = (String) tok.nextElement();
		if (part.length() != 2)
		    badDate("Second must be two digits", s);
		second = (byte) Integer.parseInt(part);

		if (second > 59)
		    badDate("Second is out of range", s);
		if (hour == 24 && second != 0)
		    badDate("If hour is 24, second must be 00", s);

	    }

	    // time zone for all
	    int tz = 0;
	    int state = 0;
	    boolean negative = false;
	    while (tok.hasMoreElements()) {
		if (state == 9) {
		    badDate("Characters after the end", s);
		}
		String delim = (String) tok.nextElement();

		if (".".equals(delim)) {

		    if (pType == MXQueryDateTime.VALUE_TYPE_DATE_TIME
			    || pType == MXQueryDateTime.VALUE_TYPE_TIME) {
			if (state != 0) {
			    badDate("Decimal separator occurs twice", s);
			}
			part = (String) tok.nextElement();
			double fractionalSeconds = Double
				.parseDouble('.' + part);
			microsecond = (int) (Math
				.round(fractionalSeconds * 1000000));

			if (hour == 24 && microsecond != 0) {
			    badDate(
				    "If hour is 24, fractional seconds must be 0",
				    s);
			}
			state = 1;
		    } else
			badDate("No '.' deliminator for xs:date", s);

		} else if ("Z".equals(delim)) {
		    if (state > 1) {
			badDate("Z cannot occur here", s);
		    }
		    tz = 0;
		    state = 9; // we've finished
		    setTimezoneInMinutes(0);
		} else if ("+".equals(delim) || "-".equals(delim)) {
		    if (state > 1) {
			badDate(delim + " cannot occur here", s);
		    }
		    state = 2;
		    if (!tok.hasMoreElements())
			badDate("Missing timezone", s);
		    part = (String) tok.nextElement();
		    if (part.length() != 2)
			badDate("Timezone hour must be two digits", s);

		    tz = Integer.parseInt(part);
		    if (tz > 14)
			badDate("Timezone is out of range (-14:00 to +14:00)",
				s);
		    tz *= 60;

		    // if (tz > 12*60) badDate("Because of Java limitations
		    // timezone limited to +/- 12 hours");
		    if ("-".equals(delim))
			negative = true;

		} else if (":".equals(delim)) {
		    if (state != 2) {
			badDate("Misplaced ':'", s);
		    }
		    state = 9;
		    part = (String) tok.nextElement();
		    int tzminute = Integer.parseInt(part);
		    if (part.length() != 2)
			badDate("Timezone minute must be two digits", s);
		    if (tzminute > 59)
			badDate("Timezone minute is out of range", s);
		    if (tz < 0)
			tzminute = -tzminute;
		    if (Math.abs(tz) == 14 * 60 && tzminute != 0) {
			badDate("Timezone is out of range (-14:00 to +14:00)",
				s);
		    }
		    tz += tzminute;
		    if (negative)
			tz = -tz;
		    setTimezoneInMinutes(tz);
		} else {
		    badDate("Timezone format is incorrect", s);
		}
	    }

	    if (state == 2 || state == 3) {
		badDate("Timezone incomplete", s);
	    }

	    if (pType == MXQueryDateTime.VALUE_TYPE_DATE
		    || pType == MXQueryDateTime.VALUE_TYPE_DATE_TIME) {
		boolean midnight = false;
		if (hour == 24) {
		    hour = 0;
		    midnight = true;
		}

		// Check that this is a valid calendar date
		if (!MXQueryDateTime.isValidDate(year, month, day)) {
		    badDate("Non-existent date", s);
		}

		// Adjust midnight to 00:00:00 on the next day
		if (midnight) {
		    makeTomorrow();
		}
	    } else { // pType == MXQueryDateTime.VALUE_TYPE_TIME
		if (hour == 24) {
		    hour = 0;
		}
	    }

	    this.c = createCalendar();

	} catch (NumberFormatException err) {
	    badDate("Non-numeric component", s);
	}

    }

    private void badDate(String msg, CharSequence value)
	    throws MXQueryException {
	throw new DynamicException(
		ErrorCodes.F0023_INVALID_VALUE_FOR_CAST_CONSTRUCTOR,
		"Date parsing Exception", null);
    }

    public final void setTimezoneInMinutes(int minutes) {
	tzMinutes = minutes;
    }

    public final int getTimezoneInMinutes() {
	return tzMinutes;
    }

    public final MXQueryDayTimeDuration getTimezoneAsDuration() {
	int negative = 1;
	int tzMin = tzMinutes;
	int tzHour = 0;
	if (tzMin < 0) {
	    negative = -1;
	    tzMin = -tzMin;
	}
	if (tzMin > 59) {
	    tzHour = tzMin / 60;
	    tzMin = tzMin % 60;
	}
	return new MXQueryDayTimeDuration(negative, 0, tzHour, tzMin, 0, 0);
    }

    /**
     * Test whether a candidate date is actually a valid date in the proleptic
     * Gregorian calendar
     */

    private static byte[] daysPerMonth = { 31, 28, 31, 30, 31, 30, 31, 31, 30,
	    31, 30, 31 };

    public static boolean isValidDate(int year, int month, int day) {
	if (month > 0 && month <= 12 && day > 0
		&& day <= daysPerMonth[month - 1]) {
	    return true;
	}
	if (month == 2 && day == 29) {
	    return isLeapYear(year);
	}
	return false;
    }

    /**
     * Test whether a year is a leap year
     */

    private static boolean isLeapYear(int year) {
	return (year % 4 == 0) && !(year % 100 == 0 && !(year % 400 == 0));
    }

    /**
     * Get the date that immediately follows a given date
     */

    private void makeTomorrow() {
	if (isValidDate(year, month, day + 1)) {
	    day = (byte) (day + 1);
	} else if (month < 12) {
	    month = (byte) (month + 1);
	    day = (byte) 1;
	} else {
	    year = year + 1;
	    month = (byte) (1);
	    day = (byte) 1;
	}
    }

    public MXQueryDateTime(String input) throws MXQueryException {
	this(input, MXQueryDateTime.VALUE_TYPE_DATE_TIME);
    }

    /* methods */
    public String toString() {

	StringBuffer sb = new StringBuffer();

	if (type == MXQueryDateTime.VALUE_TYPE_DATE_TIME
		|| type == MXQueryDateTime.VALUE_TYPE_DATE) {
	    int yr = year;
	    if (year <= 0) {
		sb.append('-');
		yr = -yr + 1; // no year zero in lexical space
	    }
	    appendString(sb, yr, (yr > 9999 ? (yr + "").length() : 4));
	    sb.append('-');
	    appendString(sb, month, 2);
	    sb.append('-');
	    appendString(sb, day, 2);
	}

	if (type == MXQueryDateTime.VALUE_TYPE_DATE_TIME) {
	    sb.append('T');
	}

	if (type == MXQueryDateTime.VALUE_TYPE_DATE_TIME
		|| type == MXQueryDateTime.VALUE_TYPE_TIME) {

	    appendString(sb, hour, 2);
	    sb.append(':');
	    appendString(sb, minute, 2);
	    sb.append(':');
	    appendString(sb, second, 2);
	    if (microsecond != 0) {

		sb.append('.');
		int ms = microsecond;
		int div = 100000;
		while (ms > 0) {
		    int d = ms / div;
		    sb.append((char) (d + '0'));
		    ms = ms % div;
		    div /= 10;
		}
	    }
	}

	if (hasTimezone()) {
	    appendTimezone(sb, tzMinutes);
	}

	return sb.toString();
    }

    public String datePartToString() throws MXQueryException {
	StringBuffer sb = new StringBuffer();

	int yr = year;
	if (year <= 0) {
	    sb.append('-');
	    yr = -yr + 1; // no year zero in lexical space
	}
	appendString(sb, yr, (yr > 9999 ? (yr + "").length() : 4));
	sb.append('-');
	appendString(sb, month, 2);
	sb.append('-');
	appendString(sb, day, 2);
	return sb.toString();

    }

    public String timePartToString() throws MXQueryException {
	StringBuffer sb = new StringBuffer();

	appendString(sb, hour, 2);
	sb.append(':');
	appendString(sb, minute, 2);
	sb.append(':');
	appendString(sb, second, 2);
	if (microsecond != 0) {
	    sb.append('.');
	    int ms = microsecond;
	    int div = 100000;
	    while (ms > 0) {
		int d = ms / div;
		sb.append((char) (d + '0'));
		ms = ms % div;
		div /= 10;
	    }
	}

	if (hasTimezone()) {
	    appendTimezone(sb, tzMinutes);
	}

	return sb.toString();

    }

    /**
     * Add a string representation of the timezone, typically formatted as "Z"
     * or "+03:00" or "-10:00", to a supplied string buffer
     * 
     * @param sb
     *                The StringBuffer that will be updated with the resulting
     *                string representation
     */

    public static final void appendTimezone(StringBuffer sb, int tz) {
	if (tz == 0) {
	    sb.append("Z");
	} else {
	    sb.append(tz > 0 ? "+" : "-");
	    tz = Math.abs(tz);
	    appendString(sb, tz / 60, 2);
	    sb.append(':');
	    appendString(sb, tz % 60, 2);
	}
    }

    /**
     * Append an integer, formatted with leading zeros to a fixed size, to a
     * string buffer
     * 
     * @param sb
     *                the string buffer
     * @param value
     *                the integer to be formatted
     * @param size
     *                the number of digits required (max 9)
     */

    private static void appendString(StringBuffer sb, int value, int size) {
	String s = "000000000" + value;
	sb.append(s.substring(s.length() - size));
    }

    /**
     * Determine whether this value includes a timezone
     * 
     * @return true if there is a timezone in the value, false if not
     */

    public final boolean hasTimezone() {
	return tzMinutes != NO_TIME_ZONE_DEFINED;
    }

    /**
     * Create a Calendar object representing the value of this DateTime. This
     * will respect the timezone if there is one, or be in GMT otherwise.
     */
    public GregorianCalendar createCalendar() {
	int tz = (hasTimezone() ? tzMinutes * 60000 : 0);

	GregorianCalendar calendar = (GregorianCalendar) getNewCalendar(tz);

	int yr = year;
	if (year <= 0) {
	    yr = 1 - year;
	    calendar.set(Calendar.ERA, GregorianCalendar.BC);
	}
	calendar.set(yr, month - 1, day, hour, minute, second);
	calendar.set(Calendar.MILLISECOND, microsecond / 1000); // /1000 loses
	// precision
	// unavoidably

	return calendar;
    }

    // -------------- operations --------------
    // ----------------------------------------

    public boolean equals(Object o) {
	if (o instanceof MXQueryDateTime) {
	    return equals((MXQueryDateTime) o);
	}
	return false;
    }

    public boolean equals(MXQueryDateTime d) {
	return this.c.getTimeInMillis() == (d.c.getTimeInMillis());
    }

    public boolean unequals(MXQueryDateTime d) {
	return !equals(d);
    }

    /**
     * Compares 2 DateTime values
     * 
     * @param d
     *                DateTime values to compare with
     * @return 0 if the argument Date is equal to this Date a value less than 0
     *         if this Date is before the Date argument a value greater than 0
     *         if this Date is after the Date argument
     */
    public int compareTo(MXQueryDateTime d) {
	return this.c.getTime().compareTo(d.c.getTime());
    }

    // -----
    public MXQueryDateTime addDuration(MXQueryDayTimeDuration d) {
	return addsubt(d, 1);
    }

    public MXQueryDateTime subtractDuration(MXQueryDayTimeDuration d) {
	return addsubt(d, -1);
    }

    private MXQueryDateTime addsubt(MXQueryDayTimeDuration d, int sign) {
	this.c.add(Calendar.DAY_OF_MONTH, sign * d.getDays());
	this.c.add(Calendar.HOUR_OF_DAY, sign * d.getHours());
	this.c.add(Calendar.MINUTE, sign * d.getMinutes());
	this.c.add(Calendar.SECOND, sign * d.getSeconds());
	this.c.add(Calendar.MILLISECOND, sign * d.getMiliseconds());

	// the time part of the date type value needs to be zeroed (e.g. for
	// comparison)
	if (this.type == VALUE_TYPE_DATE) {
	    this.c.set(Calendar.HOUR_OF_DAY, 0);
	    this.c.set(Calendar.MINUTE, 0);
	    this.c.set(Calendar.SECOND, 0);
	    this.c.set(Calendar.MILLISECOND, 0);
	}

	return new MXQueryDateTime(this.c, this.tzMinutes, this.type);
    }

    // ----
    public MXQueryDateTime addDuration(MXQueryYearMonthDuration d) {
	return addsubt(d, 1);
    }

    public MXQueryDateTime subtractDuration(MXQueryYearMonthDuration d) {
	return addsubt(d, -1);
    }

    private MXQueryDateTime addsubt(MXQueryYearMonthDuration d, int sign) {

	this.c.add(Calendar.YEAR, sign * d.getYears());
	this.c.add(Calendar.MONTH, sign * d.getMonths());

	return new MXQueryDateTime(this.c, this.tzMinutes, this.type);
    }

    // -----

    public MXQueryDayTimeDuration subtractDateTime(MXQueryDateTime d) {

	long d1ValInSec = this.getNormalizedDateTime().getTimeInMilis();
	long d2ValInSec = d.getNormalizedDateTime().getTimeInMilis();

	// difference in miliseconds
	long resVal = d1ValInSec - d2ValInSec;

	return MXQueryDayTimeDuration.microSecondsToDuration(resVal * 1000);
    }

    /* Time values remain the same only new time zone is set */
    private static Calendar setCalendarNewTimezone(Calendar pCal, int offset) {
	Calendar cal = getNewCalendar(offset);
	cal.set(Calendar.ERA, pCal.get(Calendar.ERA));
	cal.set(Calendar.YEAR, pCal.get(Calendar.YEAR));
	cal.set(Calendar.MONTH, pCal.get(Calendar.MONTH));
	cal.set(Calendar.DAY_OF_MONTH, pCal.get(Calendar.DAY_OF_MONTH));
	cal.set(Calendar.HOUR_OF_DAY, pCal.get(Calendar.HOUR_OF_DAY));
	cal.set(Calendar.MINUTE, pCal.get(Calendar.MINUTE));
	cal.set(Calendar.SECOND, pCal.get(Calendar.SECOND));
	cal.set(Calendar.MILLISECOND, pCal.get(Calendar.MILLISECOND));
	return cal;
    }

    /* Time values are changed when new time zone is set */
    private static Calendar changeCalendarTimezone(Calendar pCal, int offset) {

	pCal.get(Calendar.HOUR_OF_DAY); // to make sure change in the calendar
	// is applied
	pCal.setTimeZone(getNewTimeZone(offset));
	pCal.get(Calendar.HOUR_OF_DAY);
	// System.out.println(pCal.get(Calendar.HOUR_OF_DAY));

	return pCal;
    }

    public MXQueryDateTime adjustTimeZone(MXQueryDayTimeDuration dur) {

	// remove timezone (note: the calendar timezone value is not updated)
	if (dur == null) {
	    this.tzMinutes = NO_TIME_ZONE_DEFINED;
	    this.c = setCalendarNewTimezone(this.c, 0);
	    return this;
	}

	int tz = (int) (dur.timeInMicroSeconds() / 1000);

	// set timezone
	if (this.tzMinutes == NO_TIME_ZONE_DEFINED) {
	    this.c = setCalendarNewTimezone(this.c, tz);
	    this.tzMinutes = tz / 60 / 1000;
	    return this;
	}

	// change timezone
	if (this.tzMinutes != (tz / 60 / 1000)) {
	    this.c = changeCalendarTimezone(this.c, tz);

	    if (this.type == VALUE_TYPE_DATE) {
		this.c.set(Calendar.HOUR_OF_DAY, 0);
		this.c.set(Calendar.MINUTE, 0);
		this.c.set(Calendar.SECOND, 0);
		this.c.set(Calendar.MILLISECOND, 0);
	    }

	    return new MXQueryDateTime(this.c, (tz / 60 / 1000), this.type);
	} else
	    return this; // no need for change
    }

    /*
     * normalized value with context timezone
     * http://www.w3.org/TR/xquery-operators/#func-subtract-dateTimes
     * http://www.w3.org/TR/xquery-operators/#func-subtract-dates
     */
    public MXQueryDateTime getNormalizedDateTime() {

	int tz = new GregorianCalendar().get(Calendar.ZONE_OFFSET); // should be
	// the same
	// as in the
	// context
	// field
	// TIMEZONE

	if (this.tzMinutes == NO_TIME_ZONE_DEFINED) {
	    Calendar cal = setCalendarNewTimezone(this.c, tz);
	    return new MXQueryDateTime(cal, (tz / 60 / 1000), this.type);
	}

	return this;
    }

    public void setType(int t) {
	this.type = t;
    }

    public int getYear() {

	if (year <= 0) {
	    return year - 1;
	}
	return year;
    }

    public int getMonth() {
	return month;
    }

    public int getDay() {
	return day;
    }

    public int getHours() {
	return hour;
    }

    public int getMinutes() {
	return minute;
    }

    public MXQueryDouble getSecondsWithMili() throws MXQueryException {
	return new MXQueryDouble(second + "." + microsecond / 1000);
    }

    public int getSeconds() {
	return second;
    }

    public int getMiliseconds() {
	return microsecond / 1000;
    }

    public long getTimeInMilis() {
	return this.c.getTimeInMillis();
    }

    public String getTimeZone() {
	if (hasTimezone()) {
	    StringBuffer sb = new StringBuffer();
	    appendTimezone(sb, tzMinutes);
	    return sb.toString();
	} else
	    return "";
    }

    /**
     * Java and CLDC use different Calendar classes. Calendar object
     * initialization is hidden by this method, since both versions have their
     * own version of this class.
     */
    public static Calendar getNewCalendar() {
	// Zone offset from system's default timezone
	int offset = new GregorianCalendar().get(Calendar.ZONE_OFFSET);
	return getNewCalendar(offset);
    }

    private static TimeZone getNewTimeZone(int zone_offset) {
	// Daily Savings Time in the time zone is ignored
	return new SimpleTimeZone(zone_offset, "LLL");
    }

    private static Calendar getNewCalendar(int zone_offset) {
	TimeZone zone = getNewTimeZone(zone_offset);
	GregorianCalendar cal = new GregorianCalendar(zone);
	cal.setGregorianChange(new Date(Long.MIN_VALUE)); // purely Gregorian
	// calendar
	cal.setLenient(false);

	return cal;
    }

    public int hashCode() {
	return ((year * 12 + month) * 30 + day)
		^ (((hour * 60 + minute) * 60) + second) ^ microsecond;
    }
}
