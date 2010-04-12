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
import java.sql.Statement;
import java.util.Properties;
import com.funambol.db.client.ConnectionFactory;
import com.funambol.db.client.DBTools;
import com.funambol.syncclient.spdm.ManagementNode;
import com.funambol.syncclient.spdm.SimpleDeviceManager;


/**
 *
 * @version $Id: ExecQuery.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class ExecQuery {

	/**
     * DM root node
     */
    private static ManagementNode rootNode = null;
    
    /**
     * The root directory
     */
    private static String rootDirectory = null;
    
    public static final String PROP_WD = "wd";
    
    public static final String ROOT_DIRECTORY = "config";
    
    public static final String PROP_CHARSET = "spds.charset";
    
    public static final String VALUE_UTF8 = "UTF8";
    /**
     * Gets args[], concatenates them and executes the result using
     * <code>com.funambol.db.client.test.ConnectionFactory</code>
     * @param args String[]
     * @throws Exception
     */
    public static void main(String[] args) throws Exception  {

        int numArgs = args.length;

        StringBuffer query = new StringBuffer();
        for (int i=0; i<numArgs; i++) {
            query.append(args[i]).append(" ");
        }
        System.out.println("Query to execute: " + query);

        rootDirectory = System.getProperty(PROP_WD);
        
        Properties props = System.getProperties();
        
        props = System.getProperties();
        if (System.getProperty(SimpleDeviceManager.PROP_DM_DIR_BASE) == null) {
            props.put(SimpleDeviceManager.PROP_DM_DIR_BASE,
                      buildPath(rootDirectory, ROOT_DIRECTORY));
            System.setProperties(props);
        }
        props = System.getProperties();
        props.put(PROP_CHARSET, VALUE_UTF8);
        System.setProperties(props);
        
        
        System.out.println(SimpleDeviceManager.PROP_DM_DIR_BASE);
        props.put(SimpleDeviceManager.PROP_DM_DIR_BASE, "conf");

        System.setProperties(props);
        
        //device manager
        
        rootNode = SimpleDeviceManager.getDeviceManager().getManagementTree();
        //rootNode = SimpleDeviceManager.getDeviceManager().getManagementTree(".");
        
        System.out.println(rootNode.getFullContext());
        
        ConnectionFactory connFactory = (ConnectionFactory)rootNode.getManagementObject("db/connectionfactory");
        
        Connection conn = connFactory.getConnection();

        Statement stmt = conn.createStatement();
        stmt.execute(query.toString());
        if(!conn.getAutoCommit())
           conn.commit();

        DBTools.close(null, stmt, null);

        connFactory.closeConnection(conn);
    }
    
    private static String buildPath(String sourceDirectory, String dir) {
        return sourceDirectory+"/"+dir;
    }
}
