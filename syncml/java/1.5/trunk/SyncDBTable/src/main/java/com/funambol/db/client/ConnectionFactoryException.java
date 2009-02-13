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

//import com.funambol.framework.engine.SyncException;

import com.funambol.framework.engine.SyncException;

/**
 *
 *
 * @version $Id: ConnectionFactoryException.java,v 1.5 2007/06/18 14:31:46 luigiafassina Exp $
 */
public class ConnectionFactoryException extends SyncException {

     public ConnectionFactoryException(final String strMsg) {
         super(strMsg);
     }

     public ConnectionFactoryException(final String strMsg, final Throwable cause) {
         super(strMsg, cause);

    }
}
