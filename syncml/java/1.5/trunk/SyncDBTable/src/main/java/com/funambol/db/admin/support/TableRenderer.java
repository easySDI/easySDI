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

import java.awt.Color;
import java.awt.Component;

import javax.swing.JTable;
import javax.swing.table.DefaultTableCellRenderer;

/**
 * This TableCellRenderer is used to set the background color of the already used
 * fields to Color.lightGray.
 *
 *
 * @version $Id: TableRenderer.java,v 1.4 2007/06/18 14:31:45 luigiafassina Exp $
 */
public class TableRenderer extends DefaultTableCellRenderer {

    public Component getTableCellRendererComponent(
        JTable table,
        Object value,
        boolean isSelected,
        boolean hasFocus,
        int row,
        int column) {

        FieldMappingTableModel model = (FieldMappingTableModel)table.
                                       getModel();

        if (model.isAlreadyUsed(row)) {
            setBackground(Color.RED);
        } else {
            setBackground(Color.white);
        }
                
        return super.getTableCellRendererComponent(table, value, isSelected,
            hasFocus, row, column);
    }
}
