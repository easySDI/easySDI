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
package com.funambol.db.client.test;

import java.sql.*;
import java.util.*;

import com.funambol.syncclient.spdm.*;
import com.funambol.syncclient.spds.*;
import com.funambol.syncclient.common.logging.Logger;

import com.funambol.db.client.*;
//import com.funambol.framework.core.*;

/**
 *
 *
 * @version $Id: TestSync.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class TestSync {

    public static void main(String[] args) throws Exception {

        //
        // For JDK 1.1.8 compatibility we cannot use System.setPropery()...
        //
        //System.setProperty(SimpleDeviceManager.PROP_DM_DIR_BASE, "config");
        Properties props = System.getProperties();
        props.put(SimpleDeviceManager.PROP_DM_DIR_BASE, "conf");
        props.put("spds.charset", "8859_1");

        System.setProperties(props);

        // Load the connection factory for dm tree
        com.funambol.db.client.ConnectionFactory connectionFactory = null;

        try {
            connectionFactory = (com.funambol.db.client.ConnectionFactory )(SimpleDeviceManager.
                getDeviceManager().
                getManagementTree(".").
                getManagementObject("db/connectionfactory"));
        } catch (DMException ex) {
            if (Logger.isLoggable(Logger.INFO)) {
                Logger.info("Error creating the connection factory: " + ex.getMessage());
            }
            throw new IllegalStateException("Unable to obtain the connection factory");
        }


        showContact(connectionFactory);
        System.out.println("-----------------------------------------------------");
        System.out.println("-----------------------------------------------------");
        showCustomer(connectionFactory);

        SyncManager syncManager = SyncManager.getSyncManager("");

		System.out.println("*************************************************");
		System.out.println("*                     Sync                      *");
		System.out.println("*************************************************");
        syncManager.sync();

        showContact(connectionFactory);
        System.out.println("-----------------------------------------------------");
        System.out.println("-----------------------------------------------------");

        showCustomer(connectionFactory);

    }

    private static void showContact(com.funambol.db.client.ConnectionFactory connectionFactory) throws SQLException {
        System.out.println("\nContacts");
        System.out.println("-----------------------------------------------------");
        Connection conn = connectionFactory.getConnection();
        Statement stmt = conn.createStatement();
        ResultSet rs = stmt.executeQuery("SELECT * FROM contact");
        ResultSetMetaData rsmd = rs.getMetaData();
        int numCol = rsmd.getColumnCount();
        String[] cols = new String[numCol];
        for (int i = 0; i < numCol; i++) {
            cols[i] = rsmd.getColumnName(i + 1);
        }
        int cont = 0;
        while (rs.next()) {
            System.out.println("\nContact n. " + cont);
            for (int i = 0; i < numCol; i++) {
                System.out.println(cols[i] + ": " + rs.getObject(i + 1));
            }
            cont++;
        }
        if (cont == 0) {
            System.out.println("No contacts");
        }
        System.out.println("-----------------------------------------------------");
        DBTools.close(null, stmt, rs);
    }


    private static void showCustomer(com.funambol.db.client.ConnectionFactory connectionFactory) throws SQLException {
        System.out.println("\nCustomers");
        System.out.println("-----------------------------------------------------");
        Connection conn = connectionFactory.getConnection();
        Statement stmt = conn.createStatement();
        ResultSet rs = stmt.executeQuery("SELECT * FROM customer");
        ResultSetMetaData rsmd = rs.getMetaData();
        int numCol = rsmd.getColumnCount();
        String[] cols = new String[numCol];
        for (int i = 0; i < numCol; i++) {
            cols[i] = rsmd.getColumnName(i + 1);
        }
        int cont = 0;
        while (rs.next()) {
            System.out.println("\nCustomer n. " + cont);
            for (int i = 0; i < numCol; i++) {
                System.out.println(cols[i] + ": " + rs.getObject(i + 1));
            }
            cont++;
        }
        if (cont == 0) {
            System.out.println("No customers");
        }
        System.out.println("-----------------------------------------------------");
        DBTools.close(null, stmt, rs);
    }

}
