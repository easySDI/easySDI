/*
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
 */
package com.funambol.db.engine.source.support;

import java.util.Collection;

import com.funambol.framework.engine.SyncItem;
import com.funambol.framework.engine.SyncItemKey;
import com.funambol.framework.tools.Base64;

/**
 * Contains support methods.
 *
 * @version $Id: Tools.java,v 1.4 2007/06/18 14:31:48 luigiafassina Exp $
 */
public class Tools {

    /**
     * Encode the given byte[] and return a String representation
     * @param bin the byte[] to encode
     * @return String the String representation
     */
    public static String encode(byte[] bin) {
        String s = new String(Base64.encode(bin));
        return s;
    }

    /**
     * Decode the given String
     * @param s the string to decode
     * @return byte[] the byte[]
     */
    public static byte[] decode(String s) {
        return Base64.decode(s.getBytes());
    }

    /**
     * Turn <i>Collection<i> containing <i>SyncItem</i> objects into a <i>SyncItem[]</i>.
     *
     * @param items the collection of items - NOT NULL
     *
     * @return the SyncItem[] array
     */
    public static SyncItem[] toSyncItemArray(Collection items) {
        SyncItem[] itemArray = new SyncItem[items.size()];

        return (SyncItem[])items.toArray(itemArray);
    }

        /**
     * Turn <i>Collection<i> containing <i>SyncItemKey</i> objects into a <i>SyncItemKey[]</i>.
     *
     * @param items the collection of items - NOT NULL
     *
     * @return the SyncItem[] array
     */
    public static SyncItemKey[] toSyncItemKeyArray(Collection itemKeys) {
        SyncItemKey[] itemKeyArray = new SyncItemKey[itemKeys.size()];

        return (SyncItemKey[])itemKeys.toArray(itemKeyArray);
    }

}
