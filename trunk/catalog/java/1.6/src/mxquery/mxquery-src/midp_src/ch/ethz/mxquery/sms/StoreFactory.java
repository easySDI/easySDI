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

package ch.ethz.mxquery.sms;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.sms.StreamStoreSettings;
import ch.ethz.mxquery.sms.MMimpl.TokenBufferStore;
import ch.ethz.mxquery.sms.ftstore.FTTokenBufferStore;
import ch.ethz.mxquery.sms.ftstore.FullTextStore;
import ch.ethz.mxquery.sms.interfaces.StreamStore;


public class StoreFactory {
	
	public static final int SEQ_FIFO = 1; 
	public static final int RANDOM_FIFO = 2; 
	public static final int INDEX_INPLACE = 3;
	public static final int INDEX_FIFO = 4; 
	public static final int TOKEN_BUFFER = 5; 
	public static final int RANDOM_FIFO_SCHEMA = 6;
	public static final int SHARED_SEQ_FIFO = 7;
	public static final int SHARED_RANDOM_FIFO = 8;
	public static final int LAZY_SEQ_FIFO = 9;
	public static final int LAZY_RANDOM_FIFO = 10;
	
	
	public static final int READ_PATTERN_SEQUENTIAL = 0;
	public static final int READ_PATTERN_RANDOM = 1;
	public static final int READ_PATTERN_CLUSTERED = 2;
	
	public static final int UPDATE_PATTERN_FIFO = 0;
	public static final int UPDATE_PATTERN_RANDOM = 1;
	public static final int UPDATE_PATTERN_INPLACE = 2;
	
	// full text store
	public static final int FT_STORE = 20;
		
	private static int id = 0;
		
	public StoreFactory() {}

	/* Dimensions of store:
	 * Role: Active/Passive Materialization into store, Push 
	 * Access Type of SPE towards store: Push/Pull
	 * Synchronisation: false/synchronisation condition 
	 * Schema: No Schema, Item Schema
	 * Persistence: Persistent/Transient
	 * Read Pattern: Sequential, Random, Clustered
	 * Update Pattern: FIFO, RANDOM, IN-PLACE
	 * Sharing: Shared, Not Shared
	 * 
	 * Tuneables:
	 * Blocksize
	 * 
	 */
	
	public static synchronized StreamStore createStore(StreamStoreSettings sts, WindowBuffer container) throws MXQueryException{
//			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,
//					"Persistent stores are currently not yet implemented",QueryLocation.OUTSIDE_QUERY_LOC);
//			
//		switch (update) {
//		case UPDATE_PATTERN_FIFO:
//			switch (read) {
//				case READ_PATTERN_SEQUENTIAL:
//					if (shared) {
//						return new SharedSeqFIFOStore(id++, blockSize, container); 
//					} else {
//						return new SeqFIFOStore(id++, blockSize,container);
//					}
//				case READ_PATTERN_RANDOM:
//					if (shared) {
//						return new SharedRandomFIFOStore(id++, blockSize, container); 
//					} else {
//						if (schema != null) {
//							new RandomFIFOStoreSchema(id++, blockSize, container); 
//						} else {
//							return new RandomFIFOStore(id++, blockSize,container);
//						}
//					}
//					break;
//				case READ_PATTERN_CLUSTERED:
//					 return new IndexFIFOStore(id++, blockSize,container);
//			}
//			break;
//		case UPDATE_PATTERN_RANDOM:
//			switch (read) {
//			case READ_PATTERN_SEQUENTIAL:
//				break;
//			case READ_PATTERN_RANDOM:
//				break;
//			case READ_PATTERN_CLUSTERED:
//				break;
//			}
//			break;
//		case UPDATE_PATTERN_INPLACE:
//			return new IndexInPlaceStore(id++, container);
//		}
		throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,
				"Generating stores by parameters is currently not support on this version",QueryLocation.OUTSIDE_QUERY_LOC);
	}
	
	public static synchronized StreamStore createStore(int type, int blockSize, WindowBuffer container) throws MXQueryException {
	
		StreamStore st = null;
				
		switch(type){
//		case SEQ_FIFO: st = new SeqFIFOStore(id++, blockSize,container); break;
//		case RANDOM_FIFO: st = new RandomFIFOStore(id++, blockSize,container); break;
//		case RANDOM_FIFO_SCHEMA: st = new RandomFIFOStoreSchema(id++, blockSize, container); break;
//		case SHARED_SEQ_FIFO: st = new SharedSeqFIFOStore(id++, blockSize, container); break;
//		case SHARED_RANDOM_FIFO: st = new SharedRandomFIFOStore(id++, blockSize, container); break;
//		case INDEX_FIFO: st = new IndexFIFOStore(id++, blockSize,container); break;
//		case INDEX_INPLACE: st = new IndexInPlaceStore(id++, container); break;
		case TOKEN_BUFFER: st = new TokenBufferStore(id++, container); break;
//		case LAZY_RANDOM_FIFO: st = new LazyRandomFIFOStore(id++,blockSize, container);
//		case LAZY_SEQ_FIFO: st = new LazySequentialFIFOStore(id++,blockSize, container);
		default:
			throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Unsupported store type requested",QueryLocation.OUTSIDE_QUERY_LOC);
		}
		
		if (container == null) 
			container = new WindowBuffer(st);

		
		//stores[storeCount++] = st;
		
		return st;
	}

	public static synchronized FullTextStore createFTStore(WindowBuffer container) throws MXQueryException {
		//fit();
		
//		try {
//		Date date = new Date();
//		FTBenchmark.openFile();
//		FTBenchmark.writeFile("Loading start: ", date.getTime());
//		System.out.println("Loading start: ");
//    } catch (IOException e) {
//    	
//    }
		
			return new FTTokenBufferStore(id++, container); 
		
	}
}
