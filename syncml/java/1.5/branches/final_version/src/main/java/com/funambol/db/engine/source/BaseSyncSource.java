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

import java.security.Principal;
import java.sql.Timestamp;
import java.util.*;
import java.util.logging.Level;
import java.util.logging.Logger;

import javax.naming.InitialContext;
import javax.naming.NamingException;
import javax.sql.DataSource;

import com.funambol.framework.tools.merge.MergeResult;
import com.funambol.framework.tools.merge.MergeUtils;
//import com.funambol.foundation.common.merge.MergeResult;
//import com.funambol.foundation.common.merge.MergeUtils;

import com.funambol.framework.core.AlertCode;
import com.funambol.framework.engine.*;
import com.funambol.framework.engine.source.*;
import com.funambol.framework.tools.beans.BeanInitializationException;
import com.funambol.framework.tools.beans.LazyInitBean;


import com.funambol.db.common.XMLHashMapParser;


/**
 * Base class for db connector SyncSource.
 *
 * @version $Id: BaseSyncSource.java,v 1.9 2007/06/18 14:31:47 luigiafassina Exp $
 */
public abstract class BaseSyncSource extends AbstractSyncSource
implements MergeableSyncSource, LazyInitBean {

    // --------------------------------------------------------------- Constants
    protected static final String MIMETYPE    = "text/plain";

    protected static final String LOGGER_NAME = "funambol";

    // -------------------------------------------------------------- Properties

    /** Jndi name  used to connect with the database */
    private String jndiName = null;

    // ------------------------------------------------------------ Private data

    protected transient DataSource dataSource = null;

    protected transient Logger log = Logger.getLogger(LOGGER_NAME);

    protected transient Principal principal = null;

    // ------------------------------------------------------------ Constructors

    public BaseSyncSource() {
        setType(MIMETYPE);
    }

    // ------------------------------------------------------ SyncSource Methods

    /**
     * Initialize the SyncSource
     */
    public void init() throws BeanInitializationException {

        InitialContext ctx = null;

        try {
            ctx = new InitialContext();
            try {
                dataSource = (DataSource) ctx.lookup(jndiName);
            } catch (NamingException e) {

                if (jndiName.startsWith("java:/")) {
                    jndiName = (jndiName.length() > 6)
                               ? jndiName.substring(6)
                               : jndiName
                               ;
                }

                dataSource = (DataSource) ctx.lookup("java:/comp/env/" +
                                                         jndiName);
            }
        } catch (NamingException e) {
            throw new BeanInitializationException("Data source "
                                                  + jndiName
                                                  + " not found"
                                                  , e
                    );

        }

    }

    /*
    * @see SyncSource
    */
    public void beginSync(SyncContext context)
    throws SyncSourceException {
        //
        // Reset counters
        //
        super.beginSync(context);

        this.principal = context.getPrincipal();
        
        if (context.getSyncMode() == AlertCode.REFRESH_FROM_CLIENT){     		
        	removeAllSyncItems();
        }
        
    }

    // ------------------------------------------------------ SyncSource Methods


    /*
    * @see SyncSource
    */
     public SyncItemKey[] getAllSyncItemKeys() throws SyncSourceException {
             if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling getAllSyncItemKeys [" + sourceURI + "]");
        }

        return getSyncItemKeys(null, null);
    }

    /*
    * @see SyncSource
    */
    public SyncItemKey[] getDeletedSyncItemKeys(Timestamp since, Timestamp until) throws SyncSourceException {
         if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling getDeletedSyncItemKeys [" + sourceURI + "]");
        }

        return getSyncItemKeys(String.valueOf(SyncItemState.DELETED), since);
    }

    /**
     * @see SyncSource
     */
    public SyncItemKey[] getNewSyncItemKeys(Timestamp since, Timestamp until) throws SyncSourceException {
        if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling getNewSyncItemKeys [" + sourceURI + "]"); ;
        }

        return getSyncItemKeys(String.valueOf(SyncItemState.NEW), since);
    }

    /**
     * @see SyncSource
     */
    public SyncItem getSyncItemFromId(SyncItemKey syncItemKey) throws SyncSourceException {
         if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling getSyncItemFromId [" + sourceURI + "]");
        }

        return getSyncItem(syncItemKey.getKeyAsString());
    }

    /**
     * @see SyncSource
     */
    public SyncItemKey[] getSyncItemKeysFromTwin(SyncItem syncItem)
    throws SyncSourceException {
        if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling getSyncItemKeysFromTwins [" + sourceURI + "]");
        }

        SyncItem item = getSyncItemFromId(syncItem.getKey());
        if (item == null) {
            return new SyncItemKey[0];
        }

        if (item.getState() == SyncItemState.DELETED) {
            return new SyncItemKey[0];
        }
        
        return new SyncItemKey[]{item.getKey()};
    }

    /**
     * @see SyncSource
     */
     public SyncItemKey[] getUpdatedSyncItemKeys(Timestamp since, Timestamp until) throws SyncSourceException {
        if (log.isLoggable(Level.FINEST)) {
            log.finest("Calling getUpdatedSyncItemKeys [" + sourceURI + "]");
        }

        return getSyncItemKeys(String.valueOf(SyncItemState.UPDATED), since);
    }

    /**
     * @see SyncSource
     */
    public void removeSyncItem(SyncItemKey syncItemKey,
                               Timestamp   time,
                               boolean     softDelete)
    throws SyncSourceException {

        if (softDelete) {
            //
            // Ignore soft delete
            //
            return ;
        } else {
            removeSyncItem(syncItemKey, time);
        }
    }

    /**
     * @see SyncSource
     */
    public void setOperationStatus(String operation, int statusCode, SyncItemKey[] keys) {
        if (log.isLoggable(Level.FINEST)) {
            StringBuffer message = new StringBuffer("Received status code '");
            message.append(statusCode).append("' for a '").append(operation).append("'").
                    append(" for this items: ");

            for (int i = 0; i < keys.length; i++) {
                message.append("\n- " + keys[i].getKeyAsString());
            }
            log.finest(message.toString());
        }
    }

    /**
     * Merges serverItem and clientItem
     * @param serverItem the item on the server
     * @param clientItem the item on the client
     * @return SyncItem the mergedItem
     * @throws SyncSourceException
     */
    public boolean mergeSyncItems(SyncItemKey serverKey,
                                  SyncItem clientItem) throws SyncSourceException {

        SyncItem serverItem = getSyncItemFromId(serverKey);

        Map mapA, mapB = null;

        Map mergedMap  = new HashMap();

        try {

            mapA = getMapFromItem(clientItem);
            mapB = getMapFromItem(serverItem);

        } catch (Exception ex) {
            throw new SyncSourceException("Error getting item properties", ex);
        }

        //
        // Put mapA (client properties) and mapB (server properties) in mergedMap.
        // The server properties override client properties.
        //
        mergedMap.putAll(mapA);
        mergedMap.putAll(mapB);

        MergeResult result = MergeUtils.mergeMap(mapA, mapB);

        if (result.isSetBRequired()) {
            //
            // The serverMap is changed. We have to store it.
            //

            serverItem.setTimestamp(clientItem.getTimestamp());
            serverItem.setState(SyncItemState.UPDATED);

            byte[] xml = null;
            try {
                xml = XMLHashMapParser.toXML(new TreeMap(mapB)).getBytes();
            } catch (Exception e) {
                throw new SyncSourceException("Error converting the merged map into xml", e);
            }

            serverItem.setContent(xml);

            updateSyncItem(serverItem);
        }

        if (result.isSetARequired()) {
            //
            // The item on the client has to be updated
            //

            byte[] xml = null;
            try {
                xml = XMLHashMapParser.toXML(new TreeMap(mapB)).getBytes();
            } catch (Exception e) {
                throw new SyncSourceException("Error converting the merged map into xml", e);
            }

            clientItem.setContent(xml);
        }

        log.finest("MergeResult: " + result);

        return result.isSetARequired();
    }

    // -------------------------------------------------------- Abstract methods

    /**
     * Get the SyncItemKey[] in accord with the given parameters
     * @param type String
     * @param since Timestamp
     * @return SyncItem[]
     * @throws SyncSourceException
     */
    protected abstract SyncItemKey[] getSyncItemKeys(String type,
                                                     Timestamp since) throws SyncSourceException;

    /**
     * Get the SyncItem with the given itemId for the given principal
     * @param principal Principal
     * @param itemId String
     * @return SyncItem
     * @throws SyncSourceException
     */
    protected abstract SyncItem getSyncItem(String itemId)
            throws SyncSourceException;

    /**
     * Removes the item with the given itemKey marking the item deleted with the
     * give time.
     * @param syncItemKey the key of the item to remove
     * @param time the deletion time
     */
    protected abstract void removeSyncItem(SyncItemKey syncItemKey,
                                           Timestamp   time) throws SyncSourceException;

    /**
     * Removes all the items.
     * This method is used when a REFRESH_FROM_CLIENT is required.
     * (see beginSync)
     */
    protected abstract void removeAllSyncItems()
    	throws SyncSourceException;     

    
    // --------------------------------------------------------- Private methods


    /**
     * Return a map from the content item
     * @param item SyncItem
     * @return Map
     */
    private Map getMapFromItem(SyncItem item) throws Exception  {
        byte[] itemContentA = item.getContent();

        if (itemContentA == null) {
            itemContentA = new byte[0];
        }

        Map map = null;

        if (itemContentA.length > 0) {
            map = XMLHashMapParser.toMap(new String(itemContentA));
        } else {
            map = new HashMap();
        }

        return map;
    }

    // ----------------------------------------------------------- Getter/Setter


    /**
     * Getter for property jndiName.
     * @return value of property jndiName.
     */
    public String getJndiName() {
        return jndiName;
    }

    /**
     * Setter for property jndiName.
     * @param jndiName new value of property jndiName.
     */
    public void setJndiName(String jndiName) {
        this.jndiName = jndiName;
    }

}
