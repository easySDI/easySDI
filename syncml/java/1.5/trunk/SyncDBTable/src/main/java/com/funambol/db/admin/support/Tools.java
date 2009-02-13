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

package com.funambol.db.admin.support;

import java.util.List;

import javax.swing.JComboBox;


/**
 * Contains the useful methods
 *
 * @version $Id: Tools.java,v 1.4 2007/06/18 14:31:45 luigiafassina Exp $
 */

public class Tools {

    /**
     * Replace all items of the given JComboBox with the given list except the first
     * value.
     * @param combo JComboBox
     * @param list Vector
     */
    public static void replaceAllExceptFirst(JComboBox combo, List list) {
        String firstValue = (String)combo.getItemAt(0);
        combo.removeAllItems();
        combo.addItem(firstValue);

        int num = list.size();
        for (int i = 0; i < num; i++) {
            combo.addItem( (String)list.get(i));
        }
    }

}
