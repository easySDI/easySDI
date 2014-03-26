/*   Copyright 2006 - 2009 ETH Zurich 
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */

package ch.ethz.mxquery.xdmio;

import java.util.Vector;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.Source;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.model.updatePrimitives.UpdateableStore;
import ch.ethz.mxquery.sms.StreamStoreSettings;
import ch.ethz.mxquery.sms.ftstore.FullTextStore;
import ch.ethz.mxquery.sms.interfaces.ActiveStore;
import ch.ethz.mxquery.sms.interfaces.StreamStore;

/**
 * Interface of classes that contain a collection of stores.
 * 
 * @author David Alexander Graf
 * 
 */
public interface StoreSet {
	/**
	 * Creates a Store with an automatically generated URI.
	 * @param initialDataIterator TODO
	 * 
	 * @return created data source
	 * @throws MXQueryException 
	 */
	public Source createStore(XDMIterator initialDataIterator) throws MXQueryException;
	
	/**
	 * Creates a temporary Store that belongs to a particular transaction, e.g. for data to be inserted/snapshots
	 * @param transactionID Identifier for a transaction
	 * @return Store instance associated with transaction and store set
	 */
	public UpdateableStore createTransactionStore(long transactionID);
	/**
	 * Remove all temporary stores that are connected to the give transaction ID
	 * @param transactionID
	 */
	public void cleanTransationStores(long transactionID);
	
	/**
	 * Creates a Store with the passed URI.
	 * 
	 * @param uri
	 * @param initialDataIterator Iterator to load the initial contents of this store from
	 * @param maybeSerialized TODO
	 * @return created data source
	 * @throws MXQueryException 
	 */
	public Source createStore(String uri, XDMIterator initialDataIterator, boolean maybeSerialized) throws MXQueryException;
	/**
	 * Creates an updateable store. The contents are materialized completely on the first request for data
	 * @param uri identifier of store, for serializeable stores the location URI
	 * @param initialDataIterator source of initial
	 * @param newIds Shall new node IDs be assigned ?
	 * @param serializeable shall this store be considered for serialization/persistence to disk
	 * @return A Updateable store instance. If the identifier already exists, the existing store will be used 
	 */
	public UpdateableStore createUpdateableStore(String uri, XDMIterator initialDataIterator, boolean newIds, boolean serializeable);
	
	/**
	 * Create a new updateable store (copy) for an existing item   
	 * @param item the node identifier of the item
	 * @param uri identifier of store, for serializeable stores the location URI
	 * @param serializeable shall this store be considered for serialization/persistence to disk
	 * @return A new updateable store for this item
	 */
	public UpdateableStore getNewStoreForItem(Identifier item, String uri, boolean serializeable) throws MXQueryException;

	/**
	 * Remove store from this store set
	 * @param store
	 */
	public void removeStore(Source store) ;
	/**
	 * Creates a full text store 
	 * Note: An additional version of this function will be implemented later, where the types of full-text indexes can be chosen (currently, all indexes are always generated)
	 * @param uri identifier of store
	 * @return A fulltext store If the identifier already exists, the existing store will be used
	 */
	public FullTextStore createFulltextStore(String uri, XDMIterator initialDataIterator) throws MXQueryException;
	/**
	 * Create a stream store, in which the XDM data can be possibly infinite, 
	 * contents are lazily materialized and there are optimizations 
	 * for access patterns and other parameters
	 * In this version, the store type is explicitly requested  
	 * @param type A SMS store type
	 * @param uri the identifier of this stream
	 * @return A stream store fullfilling the requested paramers
	 * @throws MXQueryException 
	 */
	public StreamStore createStreamStore(int type, String uri) throws MXQueryException;
	/**
	 * Create a stream store, in which the XDM data can be possibly infinite, 
	 * contents are lazily materialized and there are optimizations 
	 * for access patterns and other parameters
	 * In this version, the store type is explicitly requested
	 * @param sts the stream store settings for this store  
	 * @param uri the identifier for this store
	 * @return a stream store with the requested properties
	 * @throws MXQueryException 
	 */
	public StreamStore createStreamStore(StreamStoreSettings sts, String uri) throws MXQueryException;
	
	/**
	 * Returns the Store which has the passed URI as identifier
	 * 
	 * @param uri The identifier of the store
	 * @return Store Data Source, if uri present, otherwise null
	 */
	public Source getStore(String uri);
	
	/**
	 * Returns all stores 
	 * @return a sequence of all stores in the store set
	 */
	public Source [] getAllStores();	
	/**
	 * Returns all updateable stores
	 * @return a sequence of all updateable stores in the store set
	 */
	public UpdateableStore  [] getUpdateableStores();

	/**
	 * Retrieve all fulltext stores in this store set
	 * @return all fulltext stores in the store set
	 */

	public FullTextStore [] getFulltextStores();
	
	/**
	 * Retrieve all streams/stream stores associated with this store set	
	 * @return all stream stores in this store set
	 */
	public StreamStore [] getStreamStores();
	/**
	 * Get all active stores in this store set, i.e. all stores that gather there 
	 * their input under their own control, not by the calling expressions
	 * @return all active stores in the store set 
	 */
	public ActiveStore [] getActiveStores();
	
	/**
	 * Returns the Identifier of the event with the passed identifier.
	 * 
	 * @param identifier
	 * @return parent identifier
	 * @throws MXQueryException 
	 */
	public Identifier getParentId(Identifier identifier) throws MXQueryException;

	/**
	 * Returns true if the event with the passed identifier has a parent.
	 * 
	 * @param identifier
	 * @return true for has parent
	 * @throws MXQueryException 
	 */
	public boolean hasParent(Identifier identifier) throws MXQueryException;
	
	
	/** 
	 * Adds a store to the list of stores to be serialized at PUL apply
	 * @param store Store to serialize
	 * @param uri Location where to store the contents of the store 
	 */
	public void addStoreToSerialize (UpdateableStore store, String uri);
	

	/**
	 * Serialize the stores in this storeset into an XML file. This affects only stores that serializable and have been modified
	 * @param backup Create backup file of the overwritten files 
	 */
	public void serializeStores(boolean backup, String baseURI) throws MXQueryException;
	
	/**
	 * 
	 * @param serializeStores if true, stores to be serialized should be collected, otherwise they are not
	 */
	public void setSerializeStores(boolean serializeStores);
	/**
	 * Stores created without additional parameters should be updateable
	 * @param updateStores
	 */
	public void setUseUpdateStores(boolean updateStores);
	/**
	 * Check if updateable stores are used
	 * @return true, if updateable stores are used by default
	 */
	public boolean isUseUpdateStores();
    /**
     * Stores created without additional parameters should support fulltext operations
     * @param fulltextStores if true, use fulltext Stores,
     */
	public void setUseFulltextStores(boolean fulltextStores);
	/**
	 * Check if fulltext stores are used
	 * @return true, if fulltext stores are used by default
	 */
	public boolean isUseFulltextStores();
	/**
	 * Discard all stores and collections that have been generated inside this store set
	 */
	public void freeRessources();
	/**
	 * Create a shallow copy of this Store Set, retaining all the contained stores and collections
	 * @return a copy of the store set
	 */
	public StoreSet copy();
	/**
	 * Get the contents of a collection
	 * @param uri The identifier of the collection
	 * @return null if collection does not exist, the set of iterator
	 */
	public abstract XDMIterator[] getCollection(String uri);
	/**
	 * Add a collection to the storeset
	 * @param uri Collection identifier
	 * @param coll Vector of iterators representing the contents of the collection
	 * @throws MXQueryException
	 */
	public abstract void addCollection(String uri, Vector coll)  throws MXQueryException;
	/**
	 * Delete a collection
	 * @param uri Collection identifier
	 * @throws MXQueryException
	 */
	public abstract void deleteCollection(String uri)  throws MXQueryException;
	
}
