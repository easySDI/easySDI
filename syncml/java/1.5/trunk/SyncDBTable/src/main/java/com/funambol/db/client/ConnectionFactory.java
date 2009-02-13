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
import java.sql.SQLException;

/**
 * This is the interface used by the TableSyncSource (client side) to get and close
 * a database connection
 *
 * @version $Id: ConnectionFactory.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public interface ConnectionFactory {

    /**
     * Return the database connection
     * @return the database connection
     */
    public Connection getConnection() throws SQLException;

    /**
     * Close the database connection
     * @param con the database connection to close
     */
    public void closeConnection(Connection con);
}
