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
package com.funambol.db.client.engine.source;

import java.security.*;
import java.sql.*;
import java.util.*;
import java.util.Map;

import com.funambol.framework.core.*;
import com.funambol.framework.tools.*;
import com.funambol.syncclient.spds.*;
import com.funambol.syncclient.spds.engine.*;
import com.funambol.syncclient.spdm.*;

import com.funambol.db.client.*;
import com.funambol.db.client.DBTools;
import com.funambol.db.common.*;

import com.funambol.framework.logging.FunambolLogger;
import com.funambol.framework.logging.FunambolLoggerFactory;
/**
 * SyncSource to sync a single table (client side).
 * <br/>See design document for the detail about the data format
 *
 * @version $Id: TableSyncSource.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class TableSyncSource implements SyncSource{

    // --------------------------------------------------------------- Constants
    private static final String CONNECTION_FACTORY_MANAGEMENT_NODE = "db/connectionfactory";

    // -------------------------------------------------------------- Properties

    private String sourceURI;
    private String type;
    private String name;
    private String description;

    /** The table name to sync */
    private String tableName       = null;

    /** The key field */
    private String keyField        = null;

    /** The update date field */
    private String updateDateField = null;

    /** The update type field */
    private String updateTypeField = null;

    /** The list of the field */
    private Vector fields       = null;

    /** The list of the binary field */
    private Vector binaryFields = null;

    // ------------------------------------------------------------ Private data

    /** The connection factory to connect with the database */
    private ConnectionFactory connectionFactory = null;

    private StringBuffer selectQuery = null;
    private String removeQuery       = null;
    private StringBuffer insertQuery = null;
    private StringBuffer updateQuery = null;

    private int syncMode = -1;

    private boolean thereAreNew     = false;
    private boolean thereAreUpdated = false;
    private boolean thereAreDeleted = false;

    protected static final FunambolLogger log = FunambolLoggerFactory.getLogger();
    /**
     * Creates a new TableSyncSource
     */
    public TableSyncSource() {
        if (log.isDebugEnabled()) {
            log.debug("Calling new TableSyncSource() [" + sourceURI + "]");
        }

        fields = new Vector();
        binaryFields = new Vector();

        // Load the connection factory for dm tree
        try {
            connectionFactory = (ConnectionFactory)(SimpleDeviceManager.
                                                    getDeviceManager().
                                                    getManagementTree(".").
                                                    getManagementObject(CONNECTION_FACTORY_MANAGEMENT_NODE));

            DatabaseMetaData metaData;
			try {
				metaData = connectionFactory.getConnection().getMetaData();
				 // Print out the metadata Information.
	            System.out.println("  **  Database Name   :"
	                + metaData.getDatabaseProductName());
	            System.out.println("      UserName:"
	                + metaData.getUserName());
	            System.out.println("      Url:"
		                + metaData.getURL());
			} catch (SQLException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

        } catch (DMException ex) {
            if (log.isInfoEnabled()) {
                log.info("Error creating the connection factory: " + ex.getMessage());
            }
            throw new IllegalStateException("Unable to obtain the connection factory");
        }

    }
    

    // ------------------------------------------------------ SyncSource methods


    public SyncItem[] getAllSyncItems(java.security.Principal principal) throws SyncException {

        if (log.isDebugEnabled()) {
            log.debug("Calling getAllSyncItems() [" + sourceURI + "]");
        }

        SyncItem[] items = getSyncItems(principal, null);

        if (log.isDebugEnabled()) {
            log.debug("Found " + items.length + " items");
        }

        return items;
    }

    public SyncItem[] getDeletedSyncItems(java.security.Principal principal, java.util.Date since) throws
        SyncException {

        if (log.isDebugEnabled()) {
            log.debug("Calling getDeletedSyncItems() [" + sourceURI + "]");
        }

        SyncItem[] items = getSyncItems(principal, String.valueOf(SyncItemState.DELETED));

        if (items.length > 0) {
            thereAreDeleted = true;
        } else {
            thereAreDeleted = false;
        }

        if (log.isDebugEnabled()) {
            log.debug("Found " + items.length + " deleted items");
        }

        return items;
    }

    public SyncItem[] getNewSyncItems(java.security.Principal principal, java.util.Date since) throws
        SyncException {

        if (log.isDebugEnabled()) {
            log.debug("Calling getNewSyncItems() [" + sourceURI + "]");
        }

        SyncItem[] items = getSyncItems(principal, String.valueOf(SyncItemState.NEW));

        if (items.length > 0) {
            thereAreNew = true;
        } else {
            thereAreNew = false;
        }

        if (log.isDebugEnabled()) {
            log.debug("Found " + items.length + " new items");
        }

        return items;
    }

    public SyncItem[] getUpdatedSyncItems(java.security.Principal principal, java.util.Date since) throws
        SyncException {

        if (log.isDebugEnabled()) {
            log.debug("Calling getUpdatedSyncItems() [" + sourceURI + "]");
        }

        SyncItem[] items = getSyncItems(principal, String.valueOf(SyncItemState.UPDATED));

        if (items.length > 0) {
            thereAreUpdated = true;
        } else {
            thereAreUpdated = false;
        }

        if (log.isDebugEnabled()) {
            log.debug("Found " + items.length + " updated items");
        }

        return items;
    }

    public SyncItem setSyncItem(java.security.Principal principal, SyncItem syncItem) throws
        SyncException {

        if (log.isDebugEnabled()) {
            log.debug("Calling setSyncItem() [" + sourceURI + "]");
        }

        Connection conn = null;

        try {

            String xml = new String( (byte[])syncItem.getPropertyValue(SyncItem.
                PROPERTY_BINARY_CONTENT));

            Map values = XMLHashMapParser.toMap(xml);

            String key = syncItem.getKey().getKeyAsString();

            // update set fieldsList=?..., updateDateField=?, updateTypeField='U' where keyField=?
            int numField = fields.size();

            int numParam = numField + 1;
            Object[] paramsUpdate = new Object[numParam];

            String fieldName = null;
            Object fieldValue = null;
            for (int i = 0; i < numField; i++) {
                fieldName = (String)fields.elementAt(i);

                if (binaryFields.indexOf(fieldName) != -1) {
                    fieldValue = decode( (String) (values.get(fieldName)));
                } else {
                    fieldValue = (String) (values.get(fieldName));
                }

                paramsUpdate[i] = fieldValue;
            }

            paramsUpdate[numField] = key;

            conn = connectionFactory.getConnection();
            int numRowUpdated = DBTools.execPreparedUpdate(conn,
                updateQuery.toString(),
                paramsUpdate);

            if (numRowUpdated != 0) {
                if (!conn.getAutoCommit()) {
                    conn.commit();
                }
                return syncItem;
            }

            // insert into table (keyField, fieldsList..., 'N') values (?,?.......
            numParam = numField + 1;

            Object[] paramsInsert = new Object[numParam];
            paramsInsert[0] = key;

            for (int i = 0; i < numField; i++) {
                fieldName = (String)fields.elementAt(i);

                if (binaryFields.indexOf(fieldName) != -1) {
                    fieldValue = decode( (String) (values.get(fieldName)));
                } else {
                    fieldValue = (String) (values.get(fieldName));
                }

                paramsInsert[i + 1] = fieldValue;
            }

            DBTools.execPreparedInsert(conn,
                                       insertQuery.toString(),
                                       paramsInsert);

            if (!conn.getAutoCommit()) {
                conn.commit();
            }

        } catch (Exception e) {
            if (log.isInfoEnabled()) {
                log.info("Error setting item: " + e.getMessage());
            }
            throw new SyncException("Error setting item "
                                    + syncItem.getKey().getKeyAsString(),
                                    e
                );
        } finally {
            connectionFactory.closeConnection(conn);
        }

        return syncItem;
    }

    public void removeSyncItem(java.security.Principal principal, SyncItem syncItem) throws
        SyncException {

        if (log.isDebugEnabled()) {
            log.debug("Calling removeSyncItem() [" + sourceURI + "]");
        }

        String key = syncItem.getKey().getKeyAsString();

        Connection conn = null;

        try {
            conn = connectionFactory.getConnection();

            // DELETE FROM tableName WHERE keyField=?
            Object[] params = {key};

            DBTools.execPreparedUpdate(conn,
                                       removeQuery.toString(),
                                       params);

            if (!conn.getAutoCommit()) {
                conn.commit();
            }

        } catch (Exception e) {
            if (log.isInfoEnabled()) {
                           log.info("Error removing item: " + e.getMessage());
            }
            throw new SyncException("Error removing item "
                                    + syncItem.getKey().getKeyAsString(),
                                    e
                );
        } finally {
            connectionFactory.closeConnection(conn);
        }
    }


    public void beginSync(int syncMode) throws SyncException {

        if (log.isInfoEnabled()) {
            log.info("Calling beginSync [" + sourceURI + "] with syncMode: " +
                    syncMode);
        }

        this.syncMode = syncMode;

        if (connectionFactory == null) {
            throw new SyncException("Error begin the sync. connectionFactory is null");
        }

        buildQueries();
    }

    public void commitSync() throws SyncException {

        if (log.isInfoEnabled()) {
            log.info("Calling commitSync [" + sourceURI + "]");
        }

        String query       = null;
        boolean needCommit = false;

        Connection conn = null;

        try {

            conn = connectionFactory.getConnection();

            if (syncMode != AlertCode.SLOW) {

                if (log.isDebugEnabled()) {
                    log.debug("In " + sourceURI + " there are new items: " + thereAreNew);
                    log.debug("In " + sourceURI + " there are updated items: " + thereAreUpdated);
                    log.debug("In " + sourceURI + " there are deleted items: " + thereAreDeleted);
                }

                /**
                 * If there are new items, we set their state to SyncItemState.SYNCHRONIZED
                 */
                if (thereAreNew) {
                    query = "UPDATE " + tableName + " SET " + updateTypeField + "='" +
                            SyncItemState.SYNCHRONIZED +
                            "' WHERE " + updateTypeField + "='" + SyncItemState.NEW + "'";

                    DBTools.execPreparedUpdate(conn, query.toString(), null);
                    needCommit = true;
                }

                /**
                 * If there are updated items, we set their state to SyncItemState.SYNCHRONIZED
                 */
                if (thereAreUpdated) {
                    query = "UPDATE " + tableName + " SET " + updateTypeField + "='" +
                            SyncItemState.SYNCHRONIZED +
                            "' WHERE " + updateTypeField + "='" + SyncItemState.UPDATED + "'";

                    DBTools.execPreparedUpdate(conn, query.toString(), null);
                    needCommit = true;
                }

                /**
                 * If there are deleted items, we delete these
                 */
                if (thereAreDeleted) {
                    query = "DELETE FROM " + tableName + " WHERE " + updateTypeField + "='" +
                            SyncItemState.DELETED +
                            "'";

                    DBTools.execPreparedUpdate(conn, query.toString(), null);
                    needCommit = true;
                }

            } else {

                /**
                 * Update the state of all items to SyncItemState.SYNCHRONIZED
                 */

                // slow sync
                query = "UPDATE " + tableName + " SET " + updateTypeField + "='" +
                        SyncItemState.SYNCHRONIZED + "'";

                int numRows = DBTools.execPreparedUpdate(conn, query.toString(), null);

                if (numRows > 0) {
                    needCommit = true;
                }
            }

            if (needCommit) {
                if (!conn.getAutoCommit()) {
                    conn.commit();
                }
            }

        } catch (SQLException e) {
            if (log.isInfoEnabled()) {
               log.info("Error committing SyncSource [" + sourceURI + "]: " + e.getMessage());
           }

            throw new SyncException("Error committing SyncSource [" + sourceURI + "]", e);
        } finally {
            connectionFactory.closeConnection(conn);
        }
    }


    // ---------------------------------------------------------- Public Methods

    /**
     * Returns the source uri
     * @return the source uri
     */
    public String getSourceURI() {
        return sourceURI;
    }

    /**
     * Sets the source uri
     * @param sourceURI the source uri to set
     */
    public void setSourceURI(String sourceURI) {
        this.sourceURI = sourceURI;
    }

    /**
     * Returns the type
     * @return the type
     */
    public String getType() {
        return type;
    }

    /**
     * Sets the type
     * @param type the type to set
     */
    public void setType(String type) {
        this.type = type;
    }

    /**
     * Returns the name
     * @return the name
     */
    public String getName() {
        return name;
    }

    /**
     * Sets the name
     * @param name the name to set
     */
    public void setName(String name) {
        this.name = name;
    }

    /**
     * Returns the description
     * @return the description
     */
    public String getDescription() {
        return description;
    }

    /**
     * Sets the description
     * @param description the description to set
     */
    public void setDescription(String description) {
        this.description = description;
    }

    /**
     * Sets the fieldsList
     * @param fieldsList the fieldsList to set
     */
    public void setFieldsList(String fieldsList) {
        StringTokenizer stList = new StringTokenizer(fieldsList, ",");
        fields.removeAllElements();
        while (stList.hasMoreTokens()) {
            fields.addElement(stList.nextToken());
        }
    }

    /**
     * Returns the updateDateField
     * @return the updateDateField
     */
    public String getUpdateDateField() {
        return updateDateField;
    }

    /**
     * Sets the updateDateField
     * @param updateDateField the updateDateField to set
     */
    public void setUpdateDateField(String updateDateField) {
        this.updateDateField = updateDateField;
    }

    /**
     * Returns the updateTypeField
     * @return the updateTypeField
     */
    public String getUpdateTypeField() {
        return updateTypeField;
    }

    /**
     * Sets the updateTypeField
     * @param updateTypeField the updateTypeField to set
     */
    public void setUpdateTypeField(String updateTypeField) {
        this.updateTypeField = updateTypeField;
    }

    /**
     * Returns the tableName
     * @return the tableName
     */
    public String getTableName() {
        return tableName;
    }

    /**
     * Sets the tableName
     * @param tableName the tableName to set
     */
    public void setTableName(String tableName) {
        this.tableName = tableName;
    }

    /**
     * Returns the keyField
     * @return the keyField
     */
    public String getKeyField() {
        return keyField;
    }

    /**
     * Sets the keyField
     * @param keyField the keyField to set
     */
    public void setKeyField(String keyField) {
        this.keyField = keyField;
    }

    /**
     * Sets the binary fields list
     * @param binaryFieldsList the binary fields list to set
     */
    public void setBinaryFieldsList(String binaryFieldsList) {
        StringTokenizer stList = new StringTokenizer(binaryFieldsList, ",");
        binaryFields.removeAllElements();
        String value = null;
        while (stList.hasMoreTokens()) {
            value = stList.nextToken();
            binaryFields.addElement(value);
        }
    }


    // --------------------------------------------------------- Private Methods

    /**
     * Returns the items with the given type
     * @param principal Principal
     * @param type String
     * @return SyncItem[]
     * @throws SyncException
     */
    private SyncItem[] getSyncItems(Principal principal, String type)
        throws SyncException {

        ResultSet rs = null;

        StringBuffer query = new StringBuffer(selectQuery.toString());

        if (type != null) {
            query.append(" WHERE " + updateTypeField + "=? ");
        }

        SyncItem[] items = null;

        Connection conn = null;

        try {
            conn = connectionFactory.getConnection();

            if (type != null) {
                Object[] params = {type};
                rs = DBTools.execPreparedSelect(conn,
                                                query.toString(),
                                                params);
            } else {
                rs = DBTools.execPreparedSelect(conn,
                                                query.toString(),
                                                null);
            }

            items = rsToSyncItem(rs);

        } catch (SQLException e) {
            throw new SyncException("Error getting SyncItems with type '" + type + "'", e);
        } finally {
            DBTools.close(null, null, rs);
            connectionFactory.closeConnection(conn);
        }
        return items;
    }


    /**
     * Converts the given ResultSet in a SyncItem[]
     * @param rs ResultSet
     * @return SyncItem[]
     * @throws SQLException
     */
    private SyncItem[] rsToSyncItem(ResultSet rs) throws SQLException {

        ArrayList items = new ArrayList();

        int numField = fields.size();
        int indexOfTypeField = numField + 1;

        Object key = null;
        String type = null;
        HashMap values = new HashMap();
        String xml = null;

        while (rs.next()) {
            values.clear();

            SyncItem item = null;

            // SELECT key, filedList...., updateTypeField FROM ARTICOLI

            key = rs.getObject(1); // key

            type = rs.getString(indexOfTypeField + 1); // updateTypeField

            item = new SyncItemImpl(
                this,
                key,
                type.charAt(0)
                   );

            String fieldName = null;
            Object fieldValue = null;
            for (int j = 0; j < numField; j++) {
                fieldName = (String)fields.elementAt(j);
                if (binaryFields.indexOf(fieldName) != -1) {
                    fieldValue = encode( (byte[]) (rs.getObject(j + 2)));
                } else {
                    fieldValue = rs.getObject(j + 2);
                }

                values.put(fieldName, fieldValue);
            }

            xml = XMLHashMapParser.toXML(values);

            item.setProperty(
                new SyncItemProperty(SyncItem.PROPERTY_BINARY_CONTENT,
                                     xml.getBytes())
                );

            items.add(item);

        }
        return toSyncItemArray(items);
    }

    /**
     * Init the queries
     */
    private void buildQueries() {
        selectQuery = new StringBuffer("SELECT " + keyField + ", ");
        updateQuery = new StringBuffer("UPDATE " + tableName + " SET ");
        removeQuery = "DELETE FROM " + tableName + " WHERE " + keyField + "=?";
        insertQuery = new StringBuffer("INSERT INTO " + tableName + " (" + keyField + ",");

        int numFields = fields.size();
        int cont = 0;
        String field = null;
        for (int i = 0; i < numFields; i++) {
            field = (String)fields.elementAt(i);

            selectQuery.append(field + ", ");
            updateQuery.append(field + "=?,");
            insertQuery.append(field + ",");

            cont++;
        }

        selectQuery.append(updateTypeField + " FROM " + tableName);

        updateQuery.append(updateTypeField + "='" + SyncItemState.SYNCHRONIZED + "' WHERE " +
                           keyField +
                           "=?");

        insertQuery.append(updateTypeField + ") VALUES (?,");

        for (int i = 0; i < numFields; i++) {
            insertQuery.append("?,");
        }

        //  updateTypeField
        insertQuery.append("'" + SyncItemState.SYNCHRONIZED + "')");

        if (log.isDebugEnabled()) {
            log.debug("Select query for " + sourceURI + ": " +  selectQuery);
            log.debug("Update query for " + sourceURI + ": " +  updateQuery);
            log.debug("Remove query for " + sourceURI + ": " +  removeQuery);
            log.debug("Insert query for " + sourceURI + ": " +  insertQuery);
        }
    }

    /**
     * Converts the given ArrayList of SyncItem in a SyncItem[]
     * @param items ArrayList
     * @return SyncItem[]
     */
    private static SyncItem[] toSyncItemArray(ArrayList items) {
        SyncItem[] itemArray = new SyncItem[items.size()];

        return (SyncItem[])items.toArray(itemArray);
    }

    /**
     * Encode the given byte[] and return a String representation
     * @param bin the byte[] to encode
     * @return String the String representation
     */
    private static String encode(byte[] bin) {
        String s = new String(Base64.encode(bin));
        return s;
    }

    /**
     * Decode the given String
     * @param s the string to decode
     * @return byte[] the byte[]
     */
    private static byte[] decode(String s) {
        return Base64.decode(s.getBytes());
    }

}