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

import java.io.*;

import java.sql.Connection;
import java.sql.SQLException;
import java.sql.Statement;


/**
 *
 *
 * @version $Id: SQLTools.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class SQLTools {

    // ---------------------------------------------------------- Public Methods

    /**
     * Executes all sql queries contained in the given script file.
     * <br/>Performs a simply parser of the file ignoring line that start with '--'
     *
     * @param conn the connection to use to execute the query
     * @param file the script file
     *
     * @throws IOException
     * @throws SQLException
     */
    public static void executeScript(Connection conn, File file) throws IOException, SQLException {

        StringBuffer query = new StringBuffer();

        BufferedReader br = new BufferedReader(new FileReader(file));

        String line = null;
        StringReader lineReader = null;
        int c = 0;

        while ( (line = br.readLine()) != null ) {
            if (line.startsWith("--")) {
                // ignore comment line
                continue;
            }

            lineReader = new StringReader(line);

            while ( (c = lineReader.read()) != -1) {

                if ( (char)c == ';' ) {
                    executeQuery(conn, query.toString().trim());
                    query = new StringBuffer();
                } else {
                    query.append( (char)c);
                }

            }

            lineReader.close();
        }

        br.close();

    }

    /**
     * Executes a query on the given connection
     *
     * @param conn the connection to use to execute the query
     * @param query the query to execute
     *
     * @throws SQLException
     * @return int
     */
    public static int executeQuery(Connection conn, String query) throws SQLException {
        System.out.println("Execute: " + query);
        Statement stmt = conn.createStatement();
        int i = stmt.executeUpdate(query);
        stmt.close();
        return i;
    }



}
