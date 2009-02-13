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
package com.funambol.db.engine.source;

import java.sql.*;
import java.util.*;
import java.util.logging.Level;

import com.funambol.framework.engine.*;
import com.funambol.framework.engine.source.SyncSourceException;
import com.funambol.framework.security.Sync4jPrincipal;
import com.funambol.framework.tools.DBTools;
import com.funambol.framework.tools.beans.BeanInitializationException;

import com.funambol.db.common.XMLHashMapParser;
import com.funambol.db.engine.source.support.Tools;

/**
 * SyncSource to sync entities stored in a table (data table) and the association
 * between a row of this table and the owner of such row is stored in a secondary table
 * (partitioning table)
 * <br/>See design document for the detail about the data format.
 *
 * @version $Id: PartitionedTableSyncSource.java,v 1.13 2007/06/18 14:31:47 luigiafassina Exp $
 */
public class PartitionedTableSyncSource extends BaseSyncSource {

    // --------------------------------------------------------------- Constants
    private static final String ALIAS_PARTITIONING_TABLE = "PARTITIONING";

    private static final String ALIAS_DATA_TABLE    = "DATA";

    private static final String ALIAS_PARTITIONING_TABLE_COMPLETE =
            ALIAS_PARTITIONING_TABLE + ".";

    private static final String ALIAS_DATA_TABLE_COMPLETE    =
            ALIAS_DATA_TABLE + ".";

    private static final String ALIAS_FIELD_DATA_KEY = "DATA_KEY";

    // -------------------------------------------------------------- Properties

    private Map    fieldMapping;

    private String partitioningTableName;

    private String principalFieldPartitioningTable;

    private String updateDateFieldPartitioningTable;

    private String updateTypeFieldPartitioningTable;

    private String linkFieldPartitioningTable;

    private String dataTableName;

    private String updateDateFieldDataTable;

    private String updateTypeFieldDataTable;

    private String linkFieldDataTable;

    private String keyFieldDataTable;

    private java.util.List binaryFields;

    // ------------------------------------------------------------ Private data

    private String[] fieldList;

    private StringBuffer selectKeysQuery;

    private StringBuffer selectAllQuery;

    private StringBuffer selectNewQuery;

    private StringBuffer selectDeletedQuery;

    private StringBuffer selectUpdatedQuery;

    private StringBuffer removeQuery;
    
    private StringBuffer removeAllQuery;

    private StringBuffer insertQuery;

    private StringBuffer updateQuery;

    private StringBuffer insertQueryForPartitioningTable;

    private StringBuffer updateQueryForPartitioningTable;

    // ------------------------------------------------------------ Constructors

    /**
     * Initialize the SyncSource
     */
    public void init() throws BeanInitializationException {
        initQueries();
        super.init();
    }

    // ------------------------------------------------------ SyncSource methods
     
    /**
     * Adds the given item
     * @param syncItem the item to add
     * @return the item added
     * @throws SyncSourceException if an error occurs
     */
    public SyncItem addSyncItem(SyncItem syncItem) throws SyncSourceException {
        if (log.isLoggable(Level.FINE)) {
            log.fine("Calling addSyncItem [" + sourceURI + "]");
        }
        
        return setSyncItem(syncItem);

    }

    /**
     * Updates the given item
     * @param syncItem the item to update
     * @return SyncItem the item updated
     * @throws SyncSourceException if an error occurs
     */
    public SyncItem updateSyncItem(SyncItem syncItem) throws SyncSourceException {
        if (log.isLoggable(Level.FINE)) {
            log.fine("Calling updateSyncItem [" + sourceURI + "]");
        }
        
        return setSyncItem(syncItem);
        
    }



    // ------------------------------------------------------- Protected methods

    /**
     * Removes all the items.
     * @param syncItem SyncItem
     * @throws SyncSourceException
     */
    public void removeAllSyncItems()
    throws SyncSourceException {
      
        long principalId       = ((Sync4jPrincipal)principal).getId();
        
        Connection conn        = null;
        PreparedStatement stmt = null;

        if (log.isLoggable(Level.FINEST)) {
            log.finest("Query to execute: " + removeAllQuery);
            log.finest("\tparam 1 (" + principalFieldPartitioningTable + "): '" + principalId + "'");
        }

        try {
            conn = dataSource.getConnection();
            stmt = conn.prepareStatement(removeAllQuery.toString());

            stmt.setLong(1, System.currentTimeMillis());
            stmt.setLong   (2, principalId);

            stmt.executeUpdate();

        } catch (SQLException e) {
            throw new SyncSourceException("Error removing items", e);
        } finally {
            DBTools.close(conn, stmt, null);
        } 
    }
    
    /**
     * Removes the given item.
     * @param syncItem SyncItem
     * @throws SyncSourceException
     */
    protected void removeSyncItem(SyncItemKey syncItemKey, Timestamp timestamp)
    throws SyncSourceException {
        if (log.isLoggable(Level.FINE)) {
            log.fine("Calling removeSyncItem [" + sourceURI + "]");
        }

        long principalId       = ((Sync4jPrincipal)principal).getId();

        Connection conn        = null;
        PreparedStatement stmt = null;

        if (log.isLoggable(Level.FINEST)) {
            log.finest("Query to execute: " + removeQuery);
        }

        try {
            conn = dataSource.getConnection();
            stmt = conn.prepareStatement(removeQuery.toString());

            stmt.setLong  (1, System.currentTimeMillis());
            stmt.setString(2, syncItemKey.getKeyAsString());

            stmt.executeUpdate();

            DBTools.close(null, stmt, null);

            stmt = conn.prepareStatement(updateQueryForPartitioningTable.toString());

            stmt.setLong  (1, System.currentTimeMillis());
            stmt.setString(2, String.valueOf(SyncItemState.DELETED));
            stmt.setLong  (3, principalId);
            stmt.setString(4, syncItemKey.getKeyAsString());

            stmt.executeUpdate();

        } catch (SQLException e) {
            throw new SyncSourceException("Error removing items", e);
        } finally {
            DBTools.close(conn, stmt, null);
        }
    }
     
    /**
     * Returns the itemKeys for the given principal in the beginSync with the given status
     * @param type String
     * @param since Timestamp
     * @return SyncItemKeys[]
     * @throws SyncSourceException
     */
    protected SyncItemKey[] getSyncItemKeys(String status,
                                            Timestamp since) throws SyncSourceException {
        Connection conn        = null;
        PreparedStatement stmt = null;
        ResultSet rs           = null;

        long principalId       = ((Sync4jPrincipal)principal).getId();

        StringBuffer query     = null;

        if (status == null) {
            query = selectKeysQuery;
        } else {
            char t = status.charAt(0);

            switch (t) {
                case SyncItemState.DELETED:
                    query = selectDeletedQuery;
                    break;
                case SyncItemState.NEW:
                    query = selectNewQuery;
                    break;
                case SyncItemState.UPDATED:
                    query = selectUpdatedQuery;
                    break;
                default:
                    throw new SyncSourceException("Error getting itemKeys. '" + status +
                                                  "' isn't a valid item status");
            }
        }

        query.append(" ORDER BY ").
              append(keyFieldDataTable);

        try {
            conn = dataSource.getConnection();

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Query to execute: " + query.toString());
            }
            stmt = conn.prepareStatement(query.toString());

            char t = SyncItemState.NEW;

            if (status == null) {
                // getAll
                if (log.isLoggable(Level.FINEST)) {
                    log.finest(" - param 1: " + principalId);
                }
                stmt.setLong(1, principalId);

            } else {
                t = status.charAt(0);

                if (t == SyncItemState.DELETED) {

                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(" - param 1: " + principalId);
                        log.finest(" - param 2: " + since.getTime());
                        log.finest(" - param 3: " + principalId);
                        log.finest(" - param 4: " + since);
                    }

                    stmt.setLong(1, principalId);
                    stmt.setLong(2, since.getTime());
                    stmt.setLong(3, principalId);
                    stmt.setLong(4, since.getTime());

                } else if (t == SyncItemState.NEW) {

                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(" - param 1: " + principalId);
                        log.finest(" - param 2: " + since.getTime());
                        log.finest(" - param 3: " + principalId);
                        log.finest(" - param 4: " + since);
                    }

                    stmt.setLong(1, principalId);
                    stmt.setLong(2, since.getTime());
                    stmt.setLong(3, principalId);
                    stmt.setLong(4, since.getTime());

                } else if (t == SyncItemState.UPDATED) {

                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(" - param 1: " + principalId);
                        log.finest(" - param 2: " + since.getTime());
                    }

                    stmt.setLong(1, principalId);
                    stmt.setLong(2, since.getTime());
                }
            }

            rs = stmt.executeQuery();

            ArrayList itemKeyList = new ArrayList();

            while (rs.next()) {
                itemKeyList.add(new SyncItemKey(rs.getString(keyFieldDataTable)));
            }

            SyncItemKey[] itemKeys = Tools.toSyncItemKeyArray(itemKeyList);

            return itemKeys;

        } catch (Exception e) {
            log.severe(e.getMessage());
            log.throwing(getClass().getName(), "getSyncItemKeys", e);

            throw new SyncSourceException("Error getting itemKeys", e);
        } finally {
            DBTools.close(conn, stmt, rs);
        }

    }

    /**
     * Return the item with the give id
     * @param principal Principal
     * @param itemId String
     * @return SyncItem
     * @throws SyncSourceException
     */
    protected SyncItem getSyncItem(String itemId) throws
        SyncSourceException {

        Connection conn        = null;
        PreparedStatement stmt = null;
        ResultSet rs           = null;

        long principalId       = ((Sync4jPrincipal)principal).getId();

        StringBuffer query     = new StringBuffer(selectAllQuery.toString());

        try {
            conn = dataSource.getConnection();
            query.append(" AND " + ALIAS_DATA_TABLE_COMPLETE +
                         keyFieldDataTable + "=?");

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Query to execute: " + query);
            }
            stmt = conn.prepareStatement(query.toString());

            if (log.isLoggable(Level.FINEST)) {
                log.finest(" - param 1: " + principalId);
                log.finest(" - param 2: " + itemId);
            }

            stmt.setLong  (1, principalId);
            stmt.setObject(2, itemId);

            rs = stmt.executeQuery();

            SyncItem item = null;
            while (rs.next()) {
                item = rowToSyncItem(rs, 'N');
            }

            return item;

        } catch (Exception e) {
            log.severe(e.getMessage());
            log.throwing(getClass().getName(), "getSyncItem", e);

            throw new SyncSourceException("Error getting item", e);
        } finally {
            DBTools.close(conn, stmt, rs);
        }

    }

    // --------------------------------------------------------- Private Methods

    /**
     * Sets the given item
     * @param syncItem the item to update
     * @return SyncItem the item updated
     * @throws SyncSourceException if an error occurs
     */
    private SyncItem setSyncItem(SyncItem syncItem) throws SyncSourceException {

        long principalId       = ((Sync4jPrincipal)principal).getId();

        Connection conn        = null;
        PreparedStatement stmt = null;

        String key = syncItem.getKey().getKeyAsString();

        try {

            String xml = new String(syncItem.getContent());

            Map values = XMLHashMapParser.toMap(xml);

            Timestamp timestamp = syncItem.getTimestamp();

            conn = dataSource.getConnection();
            stmt = conn.prepareStatement(updateQuery.toString());

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Update query: " + updateQuery);
            }

            int numField = fieldMapping.size();

            String fieldServerName = null;
            String fieldClientName = null;
            for (int i = 0; i < numField; i++) {
                fieldServerName = fieldList[i];

                fieldClientName =
                        (String)fieldMapping.get(fieldServerName);

                if (log.isLoggable(Level.FINEST)) {
                    log.finest("\tparam" + (i + 1) + "(" + fieldServerName +
                              ", " + fieldClientName + "): " +
                              (String)values.get(fieldClientName));
                }

                if (binaryFields != null &&
                    binaryFields.indexOf(fieldServerName) != -1) {
                    //
                    // binary field, we have to encode
                    //
                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(fieldServerName +
                                  ": binary field, we have to encode");
                    }
                    byte[] data = Tools.decode( (String)values.get(
                        fieldClientName));
                    stmt.setObject(i + 1, data);
                } else {
                    stmt.setObject(i + 1,
                                   (String) values.get(fieldClientName));
                }
            }

            if (log.isLoggable(Level.FINEST)) {
                log.finest("\tparam" + (numField + 1) + ": " + timestamp.getTime());
                log.finest("\tparam" + (numField + 2) + ": '" +
                          SyncItemState.UPDATED +
                          "'");
                log.finest("\tparam" + (numField + 3) + ": " + key);
            }

            stmt.setLong(numField + 1, timestamp.getTime());
            stmt.setString(numField + 2,    String.valueOf(SyncItemState.UPDATED));
            stmt.setObject(numField + 3,    key);

            int ret = stmt.executeUpdate();
            if (log.isLoggable(Level.FINEST)) {
                log.finest("Records updated: " + ret);
            }

            if (ret != 0) {
                DBTools.close(conn, stmt, null);
                //
                // update partitioning table
                //
                setItemInPartitioningTable(principalId, key, timestamp);
                return syncItem; // Update OK
            }
            
            //
            // Add a new item
            //
            
            if (log.isLoggable(Level.FINEST)) {
                log.finest("Insert query: " + insertQuery);
            }

            stmt = conn.prepareStatement(insertQuery.toString());

            stmt.setObject(1, key);

            if (log.isLoggable(Level.FINEST)) {
                log.finest("\tparam 1 (" + keyFieldDataTable + "): '" + key + "'");
            }

            for (int i = 0; i < numField; i++) {
                fieldServerName = fieldList[i];
                fieldClientName = (String)fieldMapping.get(
                    fieldServerName);
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("\tparam" + (i + 2) + "(" + fieldServerName +
                              "): " +
                              (String)values.get(fieldClientName));
                }

                if (binaryFields != null &&
                    binaryFields.indexOf(fieldServerName) != -1) {

                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(fieldServerName +
                                  ": binary field, we have to encode");
                    }
                    byte[] data = Tools.decode( (String)values.get(
                        fieldClientName));
                    stmt.setObject(i + 2, data);
                } else {
                    stmt.setObject(i + 2,
                                   (String)values.get(fieldClientName));
                }
            }

            stmt.setLong(numField + 2, timestamp.getTime());

            stmt.executeUpdate();
            DBTools.close(null, stmt, null);            
            
            setItemInPartitioningTable(principalId, key, timestamp);
            } catch (Exception e) {
            log.severe(e.getMessage());
            log.throwing(getClass().getName(), "updateSyncItem", e);

            throw new SyncSourceException("Error updating item "
                                          + syncItem.getKey().getKeyAsString(),
                                          e
                );
        } finally {
            DBTools.close(conn, stmt, null);
        }
        return syncItem;    
        
    }
    
    /**
     * Sets the association principal-item into the principal table
     * @param principalId long
     * @param key String
     * @param timestamp Timestamp
     * @throws Exception
     */
    private void setItemInPartitioningTable(long      principalId,
                                            String    key,
                                            Timestamp timestamp)
    throws SQLException {

        Connection conn        = null;
        PreparedStatement stmt = null;

        try {
            conn = dataSource.getConnection();

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Update principalTable: " + updateQueryForPartitioningTable);
                log.finest("param 1: " + timestamp.getTime());
                log.finest("param 2: " + SyncItemState.UPDATED);
                log.finest("param 3: " + principalId);
                log.finest("param 4: " + key);
            }

            stmt = conn.prepareStatement(updateQueryForPartitioningTable.
                                         toString());

            stmt.setLong  (1, timestamp.getTime());
            stmt.setString(2, String.valueOf(SyncItemState.UPDATED));
            stmt.setLong  (3, principalId);
            stmt.setString(4, key);

            int numRowsUpdated = stmt.executeUpdate();

            if (log.isLoggable(Level.FINEST)) {
                log.finest("NumRowsUpdated: " + numRowsUpdated);
            }
            if (numRowsUpdated > 0) {
                DBTools.close(conn, stmt, null);
                return;
            }

            //
            // We have to do an insert
            //
            if (log.isLoggable(Level.FINEST)) {
                log.finest("Insert principalTable: " + insertQueryForPartitioningTable);
                log.finest("param 1: " + principalId);
                log.finest("param 2: " + key);
                log.finest("param 3: " + timestamp.getTime());
                log.finest("param 4: " + SyncItemState.NEW);
            }

            stmt = conn.prepareStatement(insertQueryForPartitioningTable.
                                         toString());
            stmt.setLong  (1, principalId);
            stmt.setObject(2, key);
            stmt.setLong  (3, timestamp.getTime());
            stmt.setString(4, String.valueOf(SyncItemState.NEW));

            stmt.executeUpdate();

        } finally {
            DBTools.close(conn, stmt, null);
        }

    }

    /**
     * Convert a resultset row into a SyncItem.
     *
     * @param rs the resultset to convert
     * @param status the item status
     * @return a SyncItem containing the row data
     *
     * @throws Exception
     */
    private SyncItem rowToSyncItem(ResultSet rs, char status) throws SQLException {
        StringBuffer xml = new StringBuffer();

        Map values = new TreeMap();

        String key     = rs.getString(keyFieldDataTable);

        int numField   = fieldList.length;
        Object value   = null;

        for (int i = 0; i < numField; i++) {
            if (log.isLoggable(Level.FINEST)) {
                log.finest("fieldList[i]: " + fieldList[i] +
                           ", keyFieldDataTable: " + keyFieldDataTable);
            }
            if (fieldList[i].equals(keyFieldDataTable)) {
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("...Use: " + ALIAS_FIELD_DATA_KEY);
                }
                value = rs.getObject(ALIAS_FIELD_DATA_KEY);
            } else {
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("...Use: " + fieldList[i]);
                }

                value = rs.getObject(fieldList[i]);
            }
            if (binaryFields != null &&
                binaryFields.indexOf(fieldList[i]) != -1) {
                //
                // Binary field, we have to encode
                //
                if (log.isLoggable(Level.FINEST)) {
                    log.finest(fieldList[i] +
                              ":  binary field, we have to encode");
                }
                values.put(
                        fieldMapping.get(fieldList[i]),
                        Tools.encode((byte[]) value)
                );
            } else {

                values.put(
                        fieldMapping.get(fieldList[i]),
                        value
                );
            }
        }

        //
        // The content is stored in the <i>SyncItem.PROPERTY_BINARY_CONTENT</i>
        // content as XML (see the design document for details on the XML schema).
        //
        SyncItem item = null;
        item = new SyncItemImpl(
                   this,
                   key,
                   status
               );

        xml.append(XMLHashMapParser.toXML(values));

        byte[] xmlByte = null;

        xmlByte = xml.toString().getBytes();

        item.setContent(xmlByte);
        item.setType(getType());

        if (log.isLoggable(Level.FINEST)) {
            log.finest("Return item: " + item);
        }

        return item;
    }

    /**
     * Init the queries
     */
    private void initQueries() {

        int numField = fieldMapping.size();
        fieldList = new String[numField];

        selectKeysQuery = new StringBuffer("SELECT " +
                                           ALIAS_DATA_TABLE_COMPLETE +
                                           keyFieldDataTable);
        selectKeysQuery.append(" as ").append(keyFieldDataTable);

        selectAllQuery = new StringBuffer("SELECT " +
                                          ALIAS_DATA_TABLE_COMPLETE +
                                          keyFieldDataTable + ", ");

        updateQuery = new StringBuffer("UPDATE " + dataTableName +
                                       " SET ");

        removeQuery = new StringBuffer("UPDATE " + dataTableName +
                                       " SET ");
        
        removeAllQuery = new StringBuffer("UPDATE " + partitioningTableName +
        " SET ");
        
        insertQueryForPartitioningTable = new StringBuffer("INSERT INTO " +
                partitioningTableName +
                " ( ");

        updateQueryForPartitioningTable = new StringBuffer("UPDATE " +
                partitioningTableName +
                " SET ");

        insertQuery = new StringBuffer("INSERT INTO " +
                                       dataTableName + "(" +
                                       keyFieldDataTable + ",");

        Iterator itKeys = fieldMapping.keySet().iterator();
        int cont = 0;
        while (itKeys.hasNext()) {
            fieldList[cont] = (String)itKeys.next();

            if (cont != 0) {
                selectAllQuery.append(",");
            }
            selectAllQuery.append(ALIAS_DATA_TABLE_COMPLETE +
                                  fieldList[cont]);

            updateQuery.append(fieldList[cont]).append("=?,");

            insertQuery.append(fieldList[cont]).append(",");

            cont++;
        }

        initSelectQuery();

        updateQuery.append(updateDateFieldDataTable).append("=?, ").
                    append(updateTypeFieldDataTable).append("=? WHERE ").
                    append(keyFieldDataTable).append("=?");

        removeQuery.append(updateDateFieldDataTable).append( "=?, ").
                    append(updateTypeFieldDataTable).append("='").
                    append(SyncItemState.DELETED).append("' WHERE ").
                    append(keyFieldDataTable).append("=?");
        
        removeAllQuery.append(updateDateFieldPartitioningTable).append( "=?, ").
                    append(updateTypeFieldPartitioningTable).append("='").
                    append(SyncItemState.DELETED).append("' WHERE ").
                    append(principalFieldPartitioningTable).append("=?");

        insertQuery.append(updateDateFieldDataTable).append(",").
                    append(updateTypeFieldDataTable);

        insertQuery.append(") VALUES (?,");

        for (int i = 0; i < numField; i++) {
            insertQuery.append("?,");
        }
        // updateDateField, updateTypeField
        insertQuery.append("?,'").append(SyncItemState.NEW).append("'");

        insertQuery.append(")");

        insertQueryForPartitioningTable.append(principalFieldPartitioningTable).
                                     append(", ").
                                     append(linkFieldPartitioningTable).
                                     append(", ").
                                     append(updateDateFieldPartitioningTable).
                                     append(", ").
                                     append(updateTypeFieldPartitioningTable).
                                     append(") values (?,?,?,?) ");

        updateQueryForPartitioningTable.append(updateDateFieldPartitioningTable).
                                     append("=?,  ").
                                     append(updateTypeFieldPartitioningTable).
                                     append("=? where  ").
                                     append(principalFieldPartitioningTable).
                                     append("=? and ").
                                     append(linkFieldPartitioningTable).
                                     append("=? ");

        if (log.isLoggable(Level.FINEST)) {
            log.finest("SELECT KEYS QUERY: "    + selectKeysQuery);
            log.finest("SELECT ALL QUERY: "     + selectAllQuery);
            log.finest("SELECT DELETED QUERY: " + selectDeletedQuery);
            log.finest("SELECT NEW QUERY: "     + selectNewQuery);
            log.finest("SELECT UPDATED QUERY: " + selectUpdatedQuery);
            log.finest("UPDATE QUERY: "         + updateQuery);
            log.finest("REMOVE QUERY: "         + removeQuery);
            log.finest("REMOVE ALL QUERY: "     + removeAllQuery);
            log.finest("INSERT QUERY: "         + insertQuery);
            log.finest("INSERT QUERY FOR PRINCIPAL TABLE: " +
                       insertQueryForPartitioningTable);
            log.finest("UPDATE QUERY FOR PRINCIPAL TABLE: " +
                       updateQueryForPartitioningTable);
        }
    }

    private void initSelectQuery() {
        selectAllQuery.append(" FROM ").
                append(partitioningTableName).
                append(" ").
                append(ALIAS_PARTITIONING_TABLE).
                append(", ").append(dataTableName).
                append(" ").append(ALIAS_DATA_TABLE).
                append(" WHERE ").append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                append(principalFieldPartitioningTable).
                append("=? ");

        selectKeysQuery.append(" FROM ").
                append(partitioningTableName).
                append(" ").
                append(ALIAS_PARTITIONING_TABLE).
                append(", ").append(dataTableName).
                append(" ").append(ALIAS_DATA_TABLE).
                append(" WHERE ").append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                append(principalFieldPartitioningTable).
                append("=? ");

        String baseQuery = selectKeysQuery.toString();
        StringBuffer selectDeletedQuery_1 = new StringBuffer(baseQuery);
        StringBuffer selectDeletedQuery_2 = new StringBuffer(baseQuery);
        StringBuffer selectNewQuery_1     = new StringBuffer(baseQuery);
        StringBuffer selectNewQuery_2     = new StringBuffer(baseQuery);

        selectUpdatedQuery = new StringBuffer(baseQuery);

        selectKeysQuery.append(" AND ").
                       append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                       append(updateTypeFieldPartitioningTable).
                       append( "!='").append(SyncItemState.DELETED).append("'");

        selectAllQuery.append(" AND ").
                       append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                       append(updateTypeFieldPartitioningTable).
                       append( "!='").append(SyncItemState.DELETED).append("'");

        selectDeletedQuery_1.append(" AND ").
                             append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                             append(updateTypeFieldPartitioningTable).
                             append("='").append(SyncItemState.DELETED).append("'");

        selectDeletedQuery_2.append(" AND ").
                             append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                             append(updateTypeFieldPartitioningTable).
                             append("!='").append(SyncItemState.DELETED).append("'");

        selectNewQuery_1.append(" AND ").
                         append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                         append(updateTypeFieldPartitioningTable).
                         append("='").append(SyncItemState.NEW).append("'");

        selectNewQuery_2.append(" AND ").
                         append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                         append(updateTypeFieldPartitioningTable).
                         append("!='").append(SyncItemState.DELETED).append("'");

        selectUpdatedQuery.append(" AND ").
                           append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                           append(updateTypeFieldPartitioningTable).
                           append("!='").append(SyncItemState.DELETED).append("'");


        StringBuffer linkCondition = new StringBuffer(" AND ");
        linkCondition.append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                      append(linkFieldPartitioningTable).
                      append("=").append(ALIAS_DATA_TABLE_COMPLETE).
                      append(linkFieldDataTable);

        selectKeysQuery.append(linkCondition);
        selectAllQuery.append(linkCondition);
        selectUpdatedQuery.append(linkCondition);

        selectDeletedQuery_1.append(linkCondition);
        selectDeletedQuery_2.append(linkCondition);
        selectNewQuery_1.append(linkCondition);
        selectNewQuery_2.append(linkCondition);

        selectKeysQuery.append(" AND ").append(ALIAS_DATA_TABLE_COMPLETE).
                       append(updateTypeFieldDataTable).append("!='").
                       append(SyncItemState.DELETED).append("'");

        selectAllQuery.append(" AND ").append(ALIAS_DATA_TABLE_COMPLETE).
                       append(updateTypeFieldDataTable).append("!='").
                       append(SyncItemState.DELETED).append("'");

        selectDeletedQuery_1.append(" AND ").append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                             append(updateDateFieldPartitioningTable).append(">?");

        selectDeletedQuery_2.append(" AND ").append(ALIAS_DATA_TABLE_COMPLETE).
                             append(updateTypeFieldDataTable).append("='").
                             append(SyncItemState.DELETED).append("' AND ").
                             append(ALIAS_DATA_TABLE_COMPLETE).
                             append(updateDateFieldDataTable).append(">?");

        selectNewQuery_1.append(" AND ").append(ALIAS_PARTITIONING_TABLE_COMPLETE).
                         append(updateDateFieldPartitioningTable).append(">? AND ").
                         append(ALIAS_DATA_TABLE_COMPLETE).
                         append(updateTypeFieldDataTable).
                         append("!='").append(SyncItemState.DELETED).append("'");

        selectNewQuery_2.append(" AND ").append(ALIAS_DATA_TABLE_COMPLETE).
                         append(updateTypeFieldDataTable).append("='").
                         append(SyncItemState.NEW).append("' AND ").
                         append(ALIAS_DATA_TABLE_COMPLETE).
                         append(updateDateFieldDataTable).append(">?");

        selectUpdatedQuery.append(" AND ").
                           append(ALIAS_DATA_TABLE_COMPLETE).
                           append(updateTypeFieldDataTable).
                           append("='").append(SyncItemState.UPDATED).append("' AND ").
                           append(ALIAS_DATA_TABLE_COMPLETE).
                           append(updateDateFieldDataTable).append(">?");

        selectDeletedQuery = new StringBuffer();
        selectDeletedQuery.append(selectDeletedQuery_1).
                           append(" UNION ").
                           append(selectDeletedQuery_2);

        selectNewQuery = new StringBuffer();
        selectNewQuery.append(selectNewQuery_1).
                       append(" UNION ").
                       append(selectNewQuery_2);
    }

    // ----------------------------------------------------------- Getter/Setter

    /**
     * Getter for property binaryFields.
     * @return value of property binaryFields.
     */
    public java.util.List getBinaryFields() {
        return binaryFields;
    }

    /**
     * Setter for property binaryFields.
     * @param binaryFields new value of property binaryFields.
     */
    public void setBinaryFields(java.util.List binaryFields) {
        this.binaryFields = binaryFields;
    }

    /**
     * Getter for property partitioningTableName.
     * @return value of property partitioningTableName.
     */
    public String getPartitioningTableName() {
        return partitioningTableName;
    }

    /**
     * Setter for property partitioningTableName.
     * @param partitioningTableName new value of property partitioningTableName.
     */
    public void setPartitioningTableName(String partitioningTableName) {
        this.partitioningTableName = partitioningTableName;
    }

    /**
     * Getter for property principalFieldPartitioningTable.
     * @return value of property principalFieldPartitioningTable.
     */
    public String getPrincipalFieldPartitioningTable() {
        return principalFieldPartitioningTable;
    }

    /**
     * Setter for property principalFieldPartitioningTable.
     * @param principalFieldPartitioningTable
     *        new value of principalFieldPartitioningTable updateDateField.
     */
    public void setPrincipalFieldPartitioningTable(
            String principalFieldPartitioningTable) {

        this.principalFieldPartitioningTable = principalFieldPartitioningTable;
    }

    /**
     * Getter for property updateDateFieldPartitioningTable.
     * @return value of property updateDateFieldPartitioningTable.
     */
    public String getUpdateDateFieldPartitioningTable() {
        return updateDateFieldPartitioningTable;
    }

    /**
     * Setter for property updateDateFieldPartitioningTable.
     * @param updateDateFieldPartitioningTable
     *        new value of property updateDateFieldPartitioningTable.
     */
    public void setUpdateDateFieldPartitioningTable(
            String updateDateFieldPartitioningTable) {
        this.updateDateFieldPartitioningTable = updateDateFieldPartitioningTable;
    }

    /**
     * Getter for property updateTypeFieldPartitioningTable.
     * @return value of property updateTypeFieldPartitioningTable.
     */
    public String getUpdateTypeFieldPartitioningTable() {
        return updateTypeFieldPartitioningTable;
    }

    /**
     * Setter for property updateTypeFieldPartitioningTable.
     * @param updateTypeFieldPartitioningTable
     *        new value of property updateTypeFieldPartitioningTable.
     */
    public void setUpdateTypeFieldPartitioningTable(
            String updateTypeFieldPartitioningTable) {
        this.updateTypeFieldPartitioningTable = updateTypeFieldPartitioningTable;
    }

    /**
     * Getter for property linkFieldPartitioningTable.
     * @return value of property linkFieldPartitioningTable.
     */
    public String getLinkFieldPartitioningTable() {
        return linkFieldPartitioningTable;
    }

    /**
     * Setter for property linkFieldPartitioningTable.
     * @param linkFieldPartitioningTable new value of property linkFieldPartitioningTable.
     */
    public void setLinkFieldPartitioningTable(String linkFieldPartitioningTable) {
        this.linkFieldPartitioningTable = linkFieldPartitioningTable;
    }

    /**
     * Getter for property dataTableName.
     * @return value of property dataTableName.
     */
    public String getDataTableName() {
        return dataTableName;
    }

    /**
     * Setter for property dataTableName.
     * @param dataTableName new value of property dataTableName.
     */
    public void setDataTableName(String dataTableName) {
        this.dataTableName = dataTableName;
    }

    /**
     * Getter for property updateDateFieldDataTable.
     * @return value of property updateDateFieldDataTable.
     */
    public String getUpdateDateFieldDataTable() {
        return updateDateFieldDataTable;
    }

    /**
     * Setter for property updateDateFieldDataTable.
     * @param updateDateFieldDataTable new value of property updateDateFieldDataTable.
     */
    public void setUpdateDateFieldDataTable(
            String updateDateFieldDataTable) {
        this.updateDateFieldDataTable = updateDateFieldDataTable;
    }

    /**
     * Getter for property updateTypeFieldDataTable.
     * @return value of property updateTypeFieldDataTable.
     */
    public String getUpdateTypeFieldDataTable() {
        return updateTypeFieldDataTable;
    }

    /**
     * Setter for property updateTypeFieldDataTable.
     * @param updateTypeFieldDataTable new value of property updateTypeFieldDataTable.
     */
    public void setUpdateTypeFieldDataTable(String updateTypeFieldDataTable) {
        this.updateTypeFieldDataTable = updateTypeFieldDataTable;
    }

    /**
     * Getter for property linkFieldDataTable.
     * @return value of property linkFieldDataTable.
     */
    public String getLinkFieldDataTable() {
        return linkFieldDataTable;
    }

    /**
     * Setter for property linkFieldDataTable.
     * @param linkFieldDataTable new value of property linkFieldDataTable.
     */
    public void setLinkFieldDataTable(String linkFieldDataTable) {
        this.linkFieldDataTable = linkFieldDataTable;
    }

    /**
     * Getter for property keyFieldDataTable.
     * @return value of property keyFieldDataTable.
     */
    public String getKeyFieldDataTable() {
        return keyFieldDataTable;
    }

    /**
     * Setter for property keyFieldDataTable.
     * @param keyFieldDataTable new value of property keyFieldDataTable.
     */
    public void setKeyFieldDataTable(String keyFieldDataTable) {
        this.keyFieldDataTable = keyFieldDataTable;
    }

    /**
     * Getter for property fieldMapping.
     * @return value of property fieldMapping.
     */
    public Map getFieldMapping() {
        return fieldMapping;
    }

    /**
     * Setter for property fieldMapping.
     * @param fieldMappingServerClient new value of property fieldMapping.
     */
    public void setFieldMapping(Map fieldMapping) {
        this.fieldMapping = fieldMapping;
    }

}
