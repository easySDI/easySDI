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

import org.apache.commons.lang.StringUtils;

import com.funambol.db.common.XMLHashMapParser;
import com.funambol.db.engine.source.support.Tools;

/**
 * SyncSource to sync a single table (server side).
 * <br/>See design document for the detail about the data format.
 *
 * @version $Id: TableSyncSource.java,v 1.11 2007/06/18 14:31:47 luigiafassina Exp $
 */
public class TableSyncSource extends BaseSyncSource {

    // -------------------------------------------------------------- Properties

    /** Table name */
    private String tableName;

    /** Server field - client field mapping */
    private Map fieldMapping;

    /** Update date field */
    private String updateDateField;

    /** Update type field */
    private String updateTypeField;

    /** Principal field */
    private String principalField;

    /** Key field */
    private String keyField;

    /** List of the binary fields */
    private java.util.List binaryFields;

    // ------------------------------------------------------------ Private data

    protected StringBuffer selectKeysQuery;
    protected StringBuffer selectQuery;
    protected StringBuffer removeQuery;
    protected StringBuffer removeAllQuery;
    protected StringBuffer insertQuery;
    protected StringBuffer updateQuery;

    protected String[] fieldList;

    // ------------------------------------------------------------ Constructors

    // ------------------------------------------------------ SyncSource Methods

    /**
     * Initialize the SyncSource
     */
    public void init() throws BeanInitializationException {
        initQueries();
        super.init();
    }
    
    /**
     * Updates the given item
     * @param syncItem the item to update
     * @return SyncItem the item updated
     * @throws SyncSourceException if an error occurs
     */
    public SyncItem updateSyncItem(SyncItem syncItem) throws SyncSourceException {

    	log.info("Called SyncItem updateSyncItem(SyncItem syncItem), Server");
        if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling updateSyncItem [" + sourceURI + "]");
        }
        
    	return setSyncItem(syncItem);

    }

    /**
     * Adds the given item
     * @param syncItem the item to add
     * @return the item added
     * @throws SyncSourceException if an error occurs
     */
    public SyncItem addSyncItem(SyncItem syncItem) throws SyncSourceException {
        
    	if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling addSyncItem [" + sourceURI + "]");
        }
    	
    	return setSyncItem(syncItem);

    }
    


    // ------------------------------------------------------- Protected methods

    /**
     * Removes the given item.
     * @param syncItem SyncItem
     * @param timestamp Timestamp
     * @throws SyncSourceException
     */
    protected void removeSyncItem(SyncItemKey syncItemKey, Timestamp timestamp) throws SyncSourceException {

        if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling removeSyncItem [" + sourceURI + "]");
        }

        Connection conn = null;
        PreparedStatement stmt = null;

        if (log.isLoggable(Level.FINEST)) {
            log.finest("Query to execute: " + removeQuery);
        }

        try {
            conn = dataSource.getConnection();
            stmt = conn.prepareStatement(removeQuery.toString());
            stmt.setLong(1, timestamp.getTime());
            stmt.setString(2, syncItemKey.getKeyAsString());

            if (StringUtils.isNotEmpty(principalField)) {
                long principalId = ((Sync4jPrincipal)principal).getId();
                stmt.setLong(3, principalId);
            }

            stmt.executeUpdate();
        } catch (SQLException e) {
            throw new SyncSourceException("Error removing item", e);
        } finally {
            DBTools.close(conn, stmt, null);
        }
    }
    
    /**
     * Removes all the items.
     * @throws SyncSourceException
     */  
    protected void removeAllSyncItems() throws SyncSourceException {

    	if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling removeAllSyncItems [" + sourceURI + "]");
        }
    	
        Connection        conn = null;
        PreparedStatement stmt = null;

        if (log.isLoggable(Level.FINEST)) {
            log.finest("Query to execute: " + removeAllQuery);
        }

        try {
            conn = dataSource.getConnection();
            stmt = conn.prepareStatement(removeAllQuery.toString());
            stmt.setLong(1, System.currentTimeMillis());
            if (StringUtils.isNotEmpty(principalField)) {
                long principalId = ((Sync4jPrincipal)principal).getId();
                if (log.isLoggable(Level.FINEST)) {
                	log.finest("\tparam 1 (" + principalField + "): '" + principalId + "'");
                }               
                stmt.setLong(2, principalId);
            }

            stmt.executeUpdate();
        } catch (SQLException e) {
            throw new SyncSourceException("Error removing all items", e);
        } finally {
            DBTools.close(conn, stmt, null);
        }
        
    }
    
    /**
     * Get the SyncItemKey[] in accord with the given parameters
     * @param type String
     * @param since Timestamp
     * @return SyncItem[]
     * @throws SyncSourceException
     */
    protected SyncItemKey[] getSyncItemKeys(String type, java.sql.Timestamp since)
    throws SyncSourceException {

        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        long principalId = ((Sync4jPrincipal)principal).getId();

        StringBuffer query = new StringBuffer(selectKeysQuery.toString());

        String andOrWhere = null;

        if (StringUtils.isNotEmpty(principalField)) {
            andOrWhere = " AND ";
        } else {
            andOrWhere = " WHERE ";
        }

        try {
            conn = dataSource.getConnection();

            if (type != null && since != null) {
                query.append(andOrWhere + updateTypeField + "=? AND " +
                             updateDateField +
                             ">?");
            } else {
                // eliminate the records with state Deleted
                query.append(andOrWhere).append(updateTypeField).append(
                    "<>'");
                query.append(SyncItemState.DELETED);
                query.append("'");
            }

            query.append(" ORDER BY " + keyField);

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Executing query: " + query);
            }
            stmt = conn.prepareStatement(query.toString());

            if (type != null && since != null) {

                if (log.isLoggable(Level.FINEST)) {
                    log.finest(" - param 1: " + String.valueOf(type));
                    log.finest(" - param 2: " + since);
                }

                if (StringUtils.isNotEmpty(principalField)) {
                    stmt.setLong(1, principalId);
                    stmt.setString(2, String.valueOf(type));
                    stmt.setLong(3, since.getTime());
                } else {
                    stmt.setString(1, String.valueOf(type));
                    stmt.setLong(2, since.getTime());
                }
            } else {
                if (StringUtils.isNotEmpty(principalField)) {
                    stmt.setLong(1, principalId);
                }
            }

            rs = stmt.executeQuery();

            ArrayList itemKeyList = new ArrayList();
            while (rs.next()) {

                itemKeyList.add(new SyncItemKey(rs.getString(keyField)));
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
     * Get the SyncItem with the given itemId for the given principal
     * @param itemId String
     * @return SyncItem
     * @throws SyncSourceException
     */
    protected SyncItem getSyncItem(String itemId) throws SyncSourceException {
        Connection conn = null;
        PreparedStatement stmt = null;
        ResultSet rs = null;

        long principalId = ((Sync4jPrincipal)principal).getId();

        StringBuffer query = new StringBuffer(selectQuery.toString());

        String andOrWhere = null;

        if (StringUtils.isNotEmpty(principalField)) {
            andOrWhere = " AND ";
        } else {
            andOrWhere = " WHERE ";
        }

        try {
            conn = dataSource.getConnection();
            query.append(andOrWhere + keyField + "=?");

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Executing query: " + query);
            }
            stmt = conn.prepareStatement(query.toString());

            if (StringUtils.isNotEmpty(principalField)) {
                stmt.setLong(1, principalId);
                stmt.setObject(2, itemId);
            } else {
                stmt.setObject(1, itemId);
            }

            rs = stmt.executeQuery();

            SyncItem item = null;
            while (rs.next()) {
                item = rowToSyncItem(rs);
            }

            return item;

        } catch (Exception e) {
            log.severe(e.getMessage());
            log.throwing(getClass().getName(), "getSyncItem", e);

            throw new SyncSourceException("Error getting items", e);
        } finally {
            DBTools.close(conn, stmt, rs);
        }
    }

    // --------------------------------------------------------- Private methods
    
    /**
     * Sets the given item
     * @param syncItem the item to add
     * @return the item added
     * @throws SyncSourceException if an error occurs
     */
    private SyncItem setSyncItem(SyncItem syncItem) throws SyncSourceException {
    	long principalId = ((Sync4jPrincipal)principal).getId();

        Connection        conn = null;
        PreparedStatement stmt = null;

        String key = syncItem.getKey().getKeyAsString();

        try {
            byte[] itemContent = syncItem.getContent();

            String xml = new String(itemContent);

            Map values = XMLHashMapParser.toMap(xml);
            Timestamp timestamp = syncItem.getTimestamp();

            conn = dataSource.getConnection();
            stmt = conn.prepareStatement(updateQuery.toString());

            if (log.isLoggable(Level.FINEST)) {
                log.finest("Query to execute: " + updateQuery);
            }

            int numFields = fieldMapping.size();

            String fieldServerName = null;
            String fieldClientName = null;
            for (int i = 0; i < numFields; i++) {
                fieldServerName = fieldList[i];
                fieldClientName = (String)fieldMapping.get(
                    fieldServerName);
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("\tparam " + (i + 1) + "(" + fieldServerName +
                              ", " +
                              fieldClientName + "): " +
                              (String)values.get(fieldClientName));
                }

                if (binaryFields != null &&
                    binaryFields.indexOf(fieldServerName) != -1) {
                    // binary field, we must encode it
                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(fieldServerName +
                                  ": binary field, we must encode it");
                    }

                    byte[] data = Tools.decode(
                    		          (String)values.get(fieldClientName)
                    		      );

                    stmt.setObject(i + 1, data);
                } else {
                    stmt.setObject(i + 1,
                                   (String)values.get(fieldClientName));
                }
            }

            if (log.isLoggable(Level.FINEST)) {
                log.finest("\tparam " + (numFields + 1) + ": " + timestamp.getTime());
                log.finest("\tparam " + (numFields + 2) + ": '" +
                          SyncItemState.UPDATED +
                          "'");
                log.finest("\tparam " + (numFields + 3) + ": " + key);
            }

            stmt.setLong  (numFields + 1, timestamp.getTime());
            stmt.setString(numFields + 2, String.valueOf(SyncItemState.UPDATED));
            stmt.setObject(numFields + 3, key);

            if (StringUtils.isNotEmpty(principalField)) {
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("\tparam " + (numFields + 4) + ": " +
                              principalId);
                }
                stmt.setLong(numFields + 4, principalId);
            }

            int ret = stmt.executeUpdate();
            if (log.isLoggable(Level.FINEST)) {
                log.finest("Records updated: " + ret);
            }

            if (ret != 0) {   
                DBTools.close(conn, stmt, null);
                return syncItem; // Update OK
            }
            
            stmt = conn.prepareStatement(insertQuery.toString());

            stmt.setObject(1, key);

            if (log.isLoggable(Level.FINEST)) {
                log.finest("setObject: 1 - '" + key + "'");
            }

            for (int i = 0; i < numFields; i++) {
                fieldServerName = fieldList[i];
                fieldClientName = (String)fieldMapping.get(
                    fieldServerName);
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("\tparam " + (i + 2) + "(" + fieldServerName +
                              "): " +
                              (String)values.get(fieldClientName));
                }

                if (binaryFields != null &&
                    binaryFields.indexOf(fieldServerName) != -1) {
                    // binary field, we must encode it
                    if (log.isLoggable(Level.FINEST)) {
                        log.finest(fieldServerName +
                                  ": binary field, we must encode it");
                    }
                    byte[] data = Tools.decode( 
                    		          (String)values.get(fieldClientName)
                    		      );
                    stmt.setObject(i + 2, data);
                } else {
                    stmt.setObject(i + 2,
                                   (String)values.get(fieldClientName));
                }
            }

            stmt.setLong(numFields + 2, timestamp.getTime());

            if (StringUtils.isNotEmpty(principalField)) {
                if (log.isLoggable(Level.FINEST)) {
                    log.finest("\tparam " + (numFields + 3) + ": " +
                              principalId);
                }
                stmt.setLong(numFields + 3, principalId);
            }

            stmt.executeUpdate();                      

        } catch (Exception e) {
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
     * Convert a resultset row into a SyncItem.
     *
     * @param rs the resultset to convert
     * @return a SyncItem containing the row data
     * @throws Exception
     */
    private SyncItem rowToSyncItem(ResultSet rs) throws SQLException {

        Map values = new TreeMap();

        String key = rs.getString(keyField);

        int numField = fieldList.length;

        Object value = null;
        for (int i = 0; i < numField; i++) {
            value = rs.getObject(fieldList[i]);

            if (binaryFields != null &&
                binaryFields.indexOf(fieldList[i]) != -1) {
                // binary data, we must encode it
                if (log.isLoggable(Level.FINEST)) {
                    log.finest(fieldList[i] +
                              ": binary data, we must encode it");
                }
                values.put(fieldMapping.get(fieldList[i]),
                           Tools.encode( (byte[])value));
            } else {
                values.put(fieldMapping.get(fieldList[i]),
                           value);
            }

        }

        char upType = rs.getString(updateTypeField).charAt(0);

        //
        // The content is stored in the <i>SyncItem.PROPERTY_BINARY_CONTENT</i>
        // content as XML (see the design document for details on the XML schema).
        //
        SyncItem item = null;
        item = new SyncItemImpl(
                   this,
                   key,
                   upType
               );

        byte[] xmlByte = null;

        xmlByte = XMLHashMapParser.toXML(values).getBytes();

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

        selectKeysQuery = new StringBuffer("SELECT " + keyField);
        selectQuery     = new StringBuffer("SELECT " + keyField + ", ");
        updateQuery     = new StringBuffer("UPDATE " + tableName + " SET ");
        removeQuery     = new StringBuffer("UPDATE " + tableName + " SET ");
        removeAllQuery  = new StringBuffer("UPDATE " + tableName + " SET ");
        insertQuery     = new StringBuffer("INSERT INTO " + tableName + "(" +
                                            keyField + ",");

        Iterator keysIt = fieldMapping.keySet().iterator();
        int cont = 0;
        while (keysIt.hasNext()) {
            fieldList[cont] = (String) keysIt.next();

            selectQuery.append(fieldList[cont] + ", ");

            updateQuery.append(fieldList[cont] + "=?,");

            insertQuery.append(fieldList[cont] + ",");

            cont++;
        }

        selectKeysQuery.append(" FROM " + tableName);

        selectQuery.append(updateTypeField + " FROM " + tableName);

        updateQuery.append(updateDateField + "=?, " + updateTypeField +
                           "=? WHERE " + keyField + "=?");

        removeQuery.append(updateDateField + "=?, " + updateTypeField +
                           "='" +
                           SyncItemState.DELETED + "' WHERE " + keyField +
                           "=?");
        
        removeAllQuery.append(updateDateField + "=?, " + updateTypeField +
                              "='" + SyncItemState.DELETED + "'");
        
        insertQuery.append(updateDateField + "," + updateTypeField);

        if (StringUtils.isNotEmpty(principalField)) {
            selectQuery.append(" WHERE " + principalField + "=? ");
            updateQuery.append(" AND " + principalField + "=?");
            insertQuery.append("," + principalField);
            removeQuery.append(" AND " + principalField + "=?");
            removeAllQuery.append(" WHERE " + principalField + "=?");
            selectKeysQuery.append(" WHERE " + principalField + "=? ");
        }

        insertQuery.append(") VALUES (?,");

        for (int i = 0; i < numField; i++) {
            insertQuery.append("?,");
        }
        // updateDateField, updateTypeField
        insertQuery.append("?,'" + SyncItemState.NEW + "'");

        if (StringUtils.isNotEmpty(principalField)) {
            insertQuery.append(",?");
        }

        insertQuery.append(")");

        if (log.isLoggable(Level.FINEST)) {
            log.finest("SELECT QUERY: "      + selectQuery);
            log.finest("UPDATE QUERY: "      + updateQuery);
            log.finest("REMOVE QUERY: "      + removeQuery);
            log.finest("REMOVEALL QUERY: "   + removeAllQuery);
            log.finest("INSERT QUERY: "      + insertQuery);
            log.finest("SELECT KEYS QUERY: " + selectKeysQuery);
        }

    }

    // ------------------------------------------------------------- Getter/Setter

    /**
     * Getter for property tableName.
     * @return value of property tableName.
     */
    public String getTableName() {
        return tableName;
    }

    /**
     * Setter for property tableName.
     * @param tableName new value of property tableName.
     */
    public void setTableName(String tableName) {
        this.tableName = tableName;
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
     * @param fieldMapping new value of property fieldMapping.
     */
    public void setFieldMapping(Map fieldMapping) {
        this.fieldMapping = fieldMapping;
    }

    /**
     * Getter for property updateDateField.
     * @return value of property updateDateField.
     */
    public String getUpdateDateField() {
        return updateDateField;
    }

    /**
     * Setter for property updateDateField.
     * @param updateDateField new value of property updateDateField.
     */
    public void setUpdateDateField(String updateDateField) {
        this.updateDateField = updateDateField;
    }

    /**
     * Getter for property principalField.
     * @return value of property principalField.
     */
    public String getPrincipalField() {
        return principalField;
    }

    /**
     * Setter for property principalField.
     * @param principalField new value of property principalField.
     */
    public void setPrincipalField(String principalField) {
        this.principalField = principalField;
    }

    /**
     * Getter for property updateTypeField.
     * @return value of property updateTypeField.
     */
    public String getUpdateTypeField() {
        return updateTypeField;
    }

    /**
     * Setter for property updateTypeField.
     * @param updateTypeField new value of property updateTypeField.
     */
    public void setUpdateTypeField(String updateTypeField) {
        this.updateTypeField = updateTypeField;
    }

    /**
     * Getter for property keyField.
     * @return value of property keyField.
     */
    public String getKeyField() {
        return keyField;
    }

    /**
     * Setter for property keyField.
     * @param keyField new value of property keyField.
     */
    public void setKeyField(String keyField) {
        this.keyField = keyField;
    }

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

}
