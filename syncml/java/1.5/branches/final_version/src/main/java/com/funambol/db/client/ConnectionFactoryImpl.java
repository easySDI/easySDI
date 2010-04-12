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
package com.funambol.db.client;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

import com.funambol.syncclient.common.logging.Logger;
import com.funambol.syncclient.spdm.ManagementNode;
import com.funambol.syncclient.spdm.SimpleDeviceManager;
import com.funambol.syncclient.spdm.DMException;


/**
 * This is a simple ConnectionFactory that opens always a new connection to the database for all calls
 * to the getConnection() method and always closes the connection for all calls to the method closeConnection().
 *
 *
 * @version $Id: ConnectionFactoryImpl.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class ConnectionFactoryImpl implements com.funambol.db.client.ConnectionFactory {

    // --------------------------------------------------------------- Constants

    // ------------------------------------------------------------ Private data
    private String driver   = null;
    private String url      = null;
    private String user     = null;
    private String password = null;

    /**
     * Createa a new instance.
     * @throws ConnectionFactoryException
     */
    public ConnectionFactoryImpl() throws ConnectionFactoryException {

    }


    /**
     * Open a new connection to the database
     * @return the connection
     * @throws SQLException if an error occurs
     */
    public Connection getConnection() throws SQLException{
        Connection conn = null;

        try {
            if (Logger.isLoggable(Logger.DEBUG)) {
                Logger.debug("Load " + driver);
            }

            Class.forName(driver);

            if (Logger.isLoggable(Logger.DEBUG)) {
                Logger.debug("Connecting to " + url + ", user '" + user + "', password: '" + password +
                             "'");
            }

            conn = DriverManager.getConnection(url, user, password);

            if (Logger.isLoggable(Logger.DEBUG)) {
                Logger.debug("Connected to " + url);
            }

        } catch (ClassNotFoundException ex) {
            throw new SQLException("Error loading the driver [" + ex.getMessage() + "]");
        }
        return conn;

    }

    /**
     * Closes the given connection
     * @param conn the connection to close
     */
    public void closeConnection(Connection conn) {
        try {
            conn.close();
        } catch (Exception ex) {
            if (Logger.isLoggable(Logger.INFO)) {
                Logger.info("Error closing the connection [" + ex.getMessage() + "]");
            }
        }
    }

    /**
     * Sets the driver
     * @param driver the driver to set
     */
    public void setDriver(String driver) {
        this.driver = driver;
    }

    /**
     * Sets the user
     * @param user the user to set
     */
    public void setUser(String user) {
        this.user = user;
    }

    /**
     * Sets the password
     * @param password the password to set
     */
    public void setPassword(String password) {
        this.password = password;
    }

    /**
     * Sets the url
     * @param url the url to set
     */
    public void setUrl(String url) {
        this.url = url;
    }
}
