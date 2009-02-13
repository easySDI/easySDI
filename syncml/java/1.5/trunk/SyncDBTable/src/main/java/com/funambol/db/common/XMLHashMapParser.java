/*
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * Copies of this file are distributed by Funambol as part of server-side
 * programs (such as Funambol Data Synchronization Server) installed on a
 * server and also as part of client-side programs installed on individual
 * devices.
 *
 * The following license notice applies to copies of this file that are
 * distributed as part of server-side programs:
 *
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the Honest Public License, as published by
 * Funambol, either version 1 or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY, TITLE, NONINFRINGEMENT or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the Honest Public License for more details.
 *
 * You should have received a copy of the Honest Public License
 * along with this program; if not, write to Funambol,
 * 643 Bair Island Road, Suite 305 - Redwood City, CA 94063, USA
 *
 * The following license notice applies to copies of this file that are
 * distributed as part of client-side programs:
 *
 * Copyright (C) 2003-2007 Funambol, Inc.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY, TITLE, NONINFRINGEMENT or FITNESS FOR A PARTICULAR
 * PURPOSE.  See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307  USA
 */

package com.funambol.db.common;

import java.util.*;

/**
 * It supplies the methods for the marshall and unmarshall of a Map in xml.
 * <p>The scope of this class is to convert a Map in text format without use
 * complex parser.
 * <p>This is an example of text representation:<br/><br/>
 *
 * <CODE>
 * &lt;field_1&gt;value_1&lt;/field_1&gt;<br/>
 * &lt;field_2&gt;value_2&lt;/field_2&gt;<br/>
 * &lt;field_3&gt;value_3&lt;/field_3&gt;<br/>
 * &lt;field_4&gt;value_4&lt;/field_4&gt;<br/>
 * &lt;MULTI&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&lt;field_5_detail><br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_1_1&gt;NULL_VALUE&lt;/field_1_1&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_2_1&gt;NULL_VALUE&lt;/field_2_1&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_3_1&gt;NULL_VALUE&lt;/field_3_1&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_4_1&gt;value_4_1&lt;/field_4_1&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&lt;/field_5_detail&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&lt;field_5_detail&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_1_2&gt;value_1_2&lt;/field_1_2&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_2_2&gt;NULL_VALUE&lt;/field_2_2&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_3_2&gt;value_3_2&lt;/field_3_2&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;field_4_2&gt;&nbsp;&lt;/field_4_2&gt;<br/>
 * &nbsp;&nbsp;&nbsp;&nbsp;&lt;/field_5_detail&gt;<br/>
 * &lt;/MULTI&gt;<br/>
 * &lt;field_6&gt;value_6&lt;/field_6&gt;<br/>
 *
 * </CODE>
 * <p>for a Map contains:<br/><br/>
 * <table cellspacing="0" cellpadding="10" BORDER="1" BORDERCOLOR="black">
 *      <tr>
 *          <th>KEY</th>
 *          <th>VALUE</th>
 *      </tr>
 *      <tr>
 *          <td>field_1</td>
 *          <td>value_1</td>
 *      </tr>
 *      <tr>
 *          <td>field_2</td>
 *          <td>value_2</td>
 *      </tr>
 *      <tr>
 *          <td>field_3</td>
 *          <td>value_3</td>
 *      </tr>
 *      <tr>
 *          <td>field_4</td>
 *          <td>value_4</td>
 *      </tr>
 *
 *      <tr>
 *          <td>field_5_detail</td>
 *          <td>List of Map:<br/>-&nbsp;First Element:<br/>
 *             <table cellspacing="0" cellpadding="10" BORDER="1" BORDERCOLOR="black">
 *                  <tr>
 *                     <th>KEY</th>
 *                     <th>VALUE</th>
 *                  </tr>
 *                  <tr>
 *                      <td>field_1_1</td>
 *                      <td>null</td>
 *                  </tr>
 *                  <tr>
 *                      <td>field_2_1</td>
 *                      <td>null</td>
 *                  </tr>
 *                  <tr>
 *                      <td>field_3_1</td>
 *                      <td>null</td>
 *                  </tr>
 *                  <tr>
 *                      <td>field_4_1</td>
 *                      <td>value_4_1</td>
 *                  </tr>
 *               </table>
 *               <br/>-&nbsp;Second Element:<br/>
 *               <table cellspacing="0" cellpadding="10" BORDER="1" BORDERCOLOR="black">
 *                  <tr>
 *                     <th>KEY</th>
 *                     <th>VALUE</th>
 *                  </tr>
 *                  <tr>
 *                      <td>field_1_2</td>
 *                      <td>value_1_2</td>
 *                  </tr>
 *                  <tr>
 *                      <td>field_2_2</td>
 *                      <td>null</td>
 *                  </tr>
 *                  <tr>
 *                      <td>field_3_2</td>
 *                      <td>value_3_2</td>
 *                  </tr>
 *                  <tr>
 *                      <td>field_4_2</td>
 *                      <td>&nbsp;</td>
 *                  </tr>
 *               </table>
 *          </td>
 *      </tr>
 *
 *      <tr>
 *          <td>field_6</td>
 *          <td>value_6</td>
 *      </tr>
 * </table>
 *
 * @version  $Id: XMLHashMapParser.java,v 1.7 2007/06/18 14:31:46 luigiafassina Exp $
 *
 */

public class XMLHashMapParser {

    // --------------------------------------------------------------- Constants
    private static final char TAG_INIT_CHAR = '<';
    private static final char TAG_END_CHAR  = '>';

    private static final String FIELD_MULTIPLE = "MULTI";

    private static final String FIELD_MULTIPLE_START =
        TAG_INIT_CHAR +
        FIELD_MULTIPLE +
        TAG_END_CHAR;

    private static final String FIELD_MULTIPLE_END =
        TAG_INIT_CHAR +
        "/" +
        FIELD_MULTIPLE +
        TAG_END_CHAR;

    private static final String NULL_VALUE = "NULL_VALUE";

    // ---------------------------------------------------------- Public Methods

    /**
     * Returns a string representation of the given map
     * @param map the map to convert
     * @return the string representation
     */
    public static String toXML(Map map) {
        StringBuffer xml = new StringBuffer();

        Set keys = map.keySet();
        Iterator it = keys.iterator();

        String key = null;
        Object oValue = null;

        while (it.hasNext()) {
            key = (String)it.next();

            oValue = map.get(key);

            if (oValue == null) {

                xml.append(TAG_INIT_CHAR).append(key).append(TAG_END_CHAR);

                xml.append(NULL_VALUE);

                xml.append(TAG_INIT_CHAR).append("/").append(key).append(TAG_END_CHAR);

            } else if (oValue instanceof java.util.List) {
                xml.append(FIELD_MULTIPLE_START);

                int size = ( (List)oValue).size();
                Map hash = null;
                for (int i = 0; i < size; i++) {

                    hash = ( (Map) ( (List)oValue).get(i));

                    xml.append(TAG_INIT_CHAR).append(key).append(TAG_END_CHAR);
                    xml.append(toXML(hash));
                    xml.append(TAG_INIT_CHAR).append("/").append(key).append(TAG_END_CHAR);
                }
                xml.append(FIELD_MULTIPLE_END);

            } else {

                xml.append(TAG_INIT_CHAR).append(key).append(TAG_END_CHAR);

                xml.append(oValue.toString());

                xml.append(TAG_INIT_CHAR).append("/").append(key).append(TAG_END_CHAR);

            }
        }

        String xmlToReturn = xml.toString();

        return xmlToReturn;
    }

    /**
     * Returns the map correspondent to the given xml
     * @param xml String
     * @return Map
     * @throws ParseException if an error occurs
     */
    public static Map toMap(String xml) throws ParseException {
        Map values = new HashMap();

        xml = xml.trim();

        char[] xmlChar = xml.toCharArray();
        int messageLength = xmlChar.length;

        if (xmlChar[0] != TAG_INIT_CHAR) {
            throw new ParseException("The message must begin with a '" + TAG_INIT_CHAR + "'");
        }

        if (xmlChar[messageLength - 1] != TAG_END_CHAR) {
            throw new ParseException("The message must end with a '" + TAG_END_CHAR + "'");
        }

        String tagName  = null;
        String tagClose = null;

        int initTag         = 0;
        int lengthTagClose  = -1;
        int indexOfCloseTag = -1;

        String value          = null;
        String multiFieldName = null;
        List multiValues      = null;

        while (initTag != -1) {

            initTag = xml.indexOf(TAG_INIT_CHAR, initTag);

            tagName = findTag(xml, initTag);

            //
            // We check the last char. If it's '/' then the tag is, for example,
            // <EMAIL/>
            //
            int indexOfLastChar = tagName.length() - 1;
            if (indexOfLastChar == -1) {
                break;
            }
            if (tagName.charAt(indexOfLastChar) == '/') {
                tagName = tagName.substring(0, indexOfLastChar);

                values.put(tagName, null);

                initTag = initTag + tagName.length() + 3;

                if (initTag >= messageLength) {
                    break;
                }
                continue;
            }

            if (tagName != null) {
                tagClose = new StringBuffer(String.valueOf(TAG_INIT_CHAR)).
                           append("/").append(tagName).append(TAG_END_CHAR).toString();

                lengthTagClose = tagClose.length();
                indexOfCloseTag = xml.indexOf(tagClose, initTag);

                if (indexOfCloseTag == -1) {
                    throw new ParseException("Tag '" + tagName + "' not closed");
                }

                int startValue = xml.indexOf(TAG_END_CHAR, initTag) + 1;
                if (startValue == -1) {
                    throw new ParseException("Tag '" + tagName + "' lost track of '>'");
                }
                value = xml.substring(startValue, indexOfCloseTag);

                if (tagName.equals(FIELD_MULTIPLE)) {
                    multiFieldName = findMultiFieldName(value);

                    multiValues = toList(value, multiFieldName);
                    values.put(multiFieldName, multiValues);

                } else {

                    if (value.equals(NULL_VALUE)) {
                        values.put(tagName, null);
                    } else {
                        values.put(tagName, value);
                    }

                }

            } else {
                throw new ParseException("No tag found");
            }

            initTag = indexOfCloseTag + lengthTagClose;

            if (initTag >= messageLength) {
                break;
            }
        }

        return values;
    }

    // --------------------------------------------------------- Private methods
    private static List toList(String xml, String tagName) throws
        ParseException {
        List values    = new ArrayList();
        List xmlValues = findXmlMultiple(xml, tagName);

        int num = xmlValues.size();

        String xmlValue = null;
        Map mapValues   = null;
        for (int i = 0; i < num; i++) {
            xmlValue  = (String)xmlValues.get(i);
            mapValues = toMap(xmlValue);
            values.add(mapValues);
        }

        return values;
    }

    private static List findXmlMultiple(String xml, String tagName) {
        String tagStart = new StringBuffer(String .valueOf(TAG_INIT_CHAR)).
                              append(tagName).append(TAG_END_CHAR).toString();

        String tagEnd   = new StringBuffer(String.valueOf(TAG_INIT_CHAR)).
                              append("/").append(tagName).append(TAG_END_CHAR).toString();

        int tagStartLength = tagStart.length();

        List values = new ArrayList();

        int indexOfTagStart = -1;
        int indexOfTagEnd = -1;
        String xmlValue = null;
        while ( (indexOfTagStart = xml.indexOf(tagStart, indexOfTagEnd)) != -1) {
            indexOfTagEnd = xml.indexOf(tagEnd, indexOfTagStart);
            xmlValue = xml.substring(indexOfTagStart + tagStartLength, indexOfTagEnd);
            values.add(xmlValue);
        }

        return values;
    }


    private static String findTag(String xml, int start) {

        String tag = null;

        int endTag = xml.indexOf(TAG_END_CHAR, start);

        if (endTag != -1) {
            tag = xml.substring(start + 1, endTag).trim();
            int space = tag.indexOf(' ');
            if (space != -1) {
                tag = tag.substring(0, space).trim();
            }
        }

        return tag;
    }

    private static String findMultiFieldName(String xml) {
        xml = xml.trim();
        return findTag(xml, 0);
    }

}