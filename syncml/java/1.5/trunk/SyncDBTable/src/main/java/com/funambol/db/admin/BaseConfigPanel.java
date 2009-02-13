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

package com.funambol.db.admin;

import java.awt.Container;
import java.awt.Frame;

import javax.swing.JOptionPane;

import com.funambol.admin.ui.SourceManagementPanel;


/**
 * This is the base class for all management panel of this module.
 *
 *
 * @version $Id: BaseConfigPanel.java,v 1.4 2007/06/18 14:31:44 luigiafassina Exp $
 */
public abstract class BaseConfigPanel extends SourceManagementPanel {

    /**
     * Show the given error message
     * @param msg String
     */
    protected void showError(String msg) {
        JOptionPane.showMessageDialog(null, msg, "Error",
                                      JOptionPane.ERROR_MESSAGE);
    }

    /*
     * Returns the frame owner of this panel
     * @return Frame
     */
    protected Frame getOwner() {
        Container parent = this;
        while ( (parent = parent.getParent()) != null) {
            if (parent instanceof Frame) {
                return (Frame)parent;
            }
        }
        return null;
    }

}
