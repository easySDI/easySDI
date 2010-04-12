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

import java.io.File;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 *
 *
 * @version $Id: CreateHypersonicDB.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class CreateHypersonicDB {

    private final String DB_DRIVER   = "org.hsqldb.jdbcDriver";
    private final String DB_URL      = "jdbc:hsqldb:data/db";
    private final String DB_USER     = "sa";
    private final String DB_PASSWORD = "";

    private final String CREATE_SCHEMA_SCRIPT = "sql/client/hypersonic/create_schema.ddl";

    public static void main(String[] args) {

        File dataDir = new File("./data");
        dataDir.mkdir();

        CreateHypersonicDB createdb = new CreateHypersonicDB();
        Connection conn = createdb.getConnection();

        createdb.createSchema(conn);

        try {
            if (conn != null) {
                conn.close();
            }
        } catch (SQLException ex) {
            ex.printStackTrace();
        }
    }

    private Connection getConnection() {
        Connection conn = null;
        try {
            Class.forName(DB_DRIVER);

            conn = DriverManager.getConnection(DB_URL, DB_USER, DB_PASSWORD);

            conn.setAutoCommit(false);
        } catch (Exception e) {
            e.printStackTrace();
        }

        return conn;
    }

    private void createSchema(Connection conn) {
        File scriptFile = new File(CREATE_SCHEMA_SCRIPT);
        try {
            SQLTools.executeScript(conn, scriptFile);
        } catch (Exception ex) {
            ex.printStackTrace();
        }
    }

}
