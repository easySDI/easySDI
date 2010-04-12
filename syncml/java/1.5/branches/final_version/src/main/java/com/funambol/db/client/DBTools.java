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

import java.sql.*;

import com.funambol.syncclient.common.logging.Logger;

/**
 * Container of utility methods for db access
 *
 *
 * @version $Id: DBTools.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class DBTools {

    /**
     * Execute the given query on the given connection with the given parameters
     * @param conn the connection to use
     * @param query the query to execute
     * @param params the parameters to use
     * @return ResultSet
     * @throws SQLException
     */
    public static ResultSet execPreparedSelect(Connection conn,
                                               String query,
                                               Object[] params) throws SQLException {

        ResultSet rs = null;

        if (conn == null) {
            throw new IllegalStateException("Connection is null");
        }

        if (Logger.isLoggable(Logger.DEBUG)) {
            Logger.debug("Execute: " + query);
        }

        try {

            PreparedStatement pStmt = conn.prepareStatement(query);

            int numParam = -1;
            if (params != null) {
                numParam = params.length;
            }

            for (int i = 0; i < numParam; i++) {
                if (Logger.isLoggable(Logger.DEBUG)) {
                    Logger.debug("\tparam[" + i + "]: " + params[i]);
                }

                if (params[i] instanceof java.util.Date) {
                    pStmt.setTimestamp(i + 1, new Timestamp( ( (java.util.Date)params[i]).getTime()));
                } else {
                    pStmt.setObject(i + 1, params[i]);
                }
            }

            rs = pStmt.executeQuery();

        } catch (SQLException sqle) {
            sqle.printStackTrace();
            throw sqle;
        }

        return rs;
    }

    /**
     * Execute the given query on the given connection with the given parameters
     * @param conn the connection to use
     * @param query the query to execute
     * @param params the parameters to use
     * @return ResultSet
     * @throws SQLException
     */
    public static int execPreparedUpdate(Connection conn,
                                         String query,
                                         Object params[]) throws SQLException {

        if (conn == null) {
            throw new IllegalStateException("Connection is null");
        }

        int numRowUpdated = -1;

        if (Logger.isLoggable(Logger.DEBUG)) {
            Logger.debug("Execute: " + query);
        }

        try {

            PreparedStatement pStmt = conn.prepareStatement(query);

            int numParam = -1;

            if (params != null) {
                numParam = params.length;
            }

            for (int i = 0; i < numParam; i++) {
                if (Logger.isLoggable(Logger.DEBUG)) {
                    Logger.debug("\tparam[" + i + "]: " + params[i]);
                }

                pStmt.setObject(i + 1, params[i]);
            }

            numRowUpdated = pStmt.executeUpdate();

            pStmt.close();

        } catch (SQLException sqle) {
            sqle.printStackTrace();
            throw sqle;
        }
        return numRowUpdated;
    }

    /**
     * Execute the given query on the given connection with the given parameters
     * @param conn the connection to use
     * @param query the query to execute
     * @param params the parameters to use
     * @return ResultSet
     * @throws SQLException
     */
    public static void execPreparedInsert(Connection conn,
                                          String query,
                                          Object params[]) throws SQLException {

        if (conn == null) {
            throw new IllegalStateException("Connection is null");
        }

        if (Logger.isLoggable(Logger.DEBUG)) {
            Logger.debug("Execute: " + query);
        }

        try {

            PreparedStatement pStmt = conn.prepareStatement(query);

            int numParam = -1;

            if (params != null) {
                numParam = params.length;
            }

            for (int i = 0; i < numParam; i++) {
                if (Logger.isLoggable(Logger.DEBUG)) {
                    Logger.debug("\tparam[" + i + "]: " + params[i]);
                }

                pStmt.setObject(i + 1, params[i]);
            }

            pStmt.execute();

            pStmt.close();

        } catch (SQLException sqle) {
            sqle.printStackTrace();
            throw sqle;
        }
    }

    /**
     * Close the given connection, statement and resultset
     * @param c the connection to close
     * @param s the statement to close
     * @param r the resultset to close
     */
    public static void close(Connection c, Statement s, ResultSet r) {
        try {
            if (r != null)
                r.close();
        } catch (Exception e) {}
        try {
            if (s != null)
                s.close();
        } catch (Exception e) {}
        try {
            if (c != null)
                c.close();
        } catch (Exception e) {}
    }

}
