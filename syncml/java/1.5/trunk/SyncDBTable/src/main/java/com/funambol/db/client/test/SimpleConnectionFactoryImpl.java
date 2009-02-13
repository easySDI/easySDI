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

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;


/**
 * This is an example of a simple ConnectionFactory that guarantees to use only
 * one connection to the database.
 *
 * @version $Id: SimpleConnectionFactoryImpl.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class SimpleConnectionFactoryImpl implements com.funambol.db.client.ConnectionFactory {

    // --------------------------------------------------------------- Constants

    // ------------------------------------------------------------ Private data
    private String driver   = null;
    private String url      = null;
    private String user     = null;
    private String password = null;

    private static Connection conn   = null;

    /**
     * Open a new connection if there isn't one.
     * @return Connection
     * @throws SQLException
     */
    public Connection getConnection() throws SQLException {
        if (conn == null) {
            connect();
        }
        return conn;
    }

    /**
     * This implementation doesn't close the connection
     * @param conn Connection
     */
    public void closeConnection(Connection conn) {

    }

    /**
     * Closes the connection
     * @throws SQLException
     */
    public void closeConnection() throws SQLException {
        if (conn != null) {
            conn.close();
        }
    }

    // --------------------------------------------------------- Private Methods

    /**
     * Creates the connection to the database
     * @throws SQLException
     */
    private synchronized void connect() throws SQLException {

        if (conn != null) {
            return ;
        }
        try {
            Class.forName(driver);
            conn = DriverManager.getConnection(url, user, password);
        } catch (Exception e) {
            e.printStackTrace();
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
