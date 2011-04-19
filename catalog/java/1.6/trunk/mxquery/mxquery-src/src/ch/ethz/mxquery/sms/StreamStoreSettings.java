package ch.ethz.mxquery.sms;

public class StreamStoreSettings {
	public static final Object MINUTE_SYNC_CONDITION = new Object();
	public static final Object LR_ITEM_SCHEMA = new Object();

	
	public static final int READ_PATTERN_SEQUENTIAL = 0;
	public static final int READ_PATTERN_RANDOM = 1;
	public static final int READ_PATTERN_CLUSTERED = 2;
	
	public static final int UPDATE_PATTERN_FIFO = 0;
	public static final int UPDATE_PATTERN_RANDOM = 1;
	public static final int UPDATE_PATTERN_INPLACE = 2;
	
	/*
	 * Multiple dimensions of materialization (not only for stream stores:
	 * - complete or partial/streaming materialization (stream stores do only partial)
	 * - on-demand or preload (for complete)
	 * - 
	 * - control: by the store or by the data producer
	 * - lazy or eager (if controller by store)
	 */

	/* Index dimensions: 
	 * - Index target: value, node id, paths, item
	 * - index "predicate": equality, greater/smaller, range, prefix
	 * - (index value target) 
	 */
	
	int materializationStrategy; 
	boolean pushAccess;
	Object syncCond;
	Object schema;
	boolean persistance;
	int read;
	int update; 
	boolean shared; 
	int blockSize;
	public int getBlockSize() {
		return blockSize;
	}
	public void setBlockSize(int blockSize) {
		this.blockSize = blockSize;
	}
	public int getMaterializationStrategy() {
		return materializationStrategy;
	}
	public void setMaterializationStrategy(int materializationStrategy) {
		this.materializationStrategy = materializationStrategy;
	}
	public boolean isPushAccess() {
		return pushAccess;
	}
	public void setPushAccess(boolean pushAccess) {
		this.pushAccess = pushAccess;
	}
	public Object getSyncCond() {
		return syncCond;
	}
	public void setSyncCond(Object syncCond) {
		this.syncCond = syncCond;
	}
	public Object getSchema() {
		return schema;
	}
	public void setSchema(Object schema) {
		this.schema = schema;
	}
	public boolean isPersistance() {
		return persistance;
	}
	public void setPersistance(boolean persistance) {
		this.persistance = persistance;
	}
	public int getRead() {
		return read;
	}
	public void setRead(int read) {
		this.read = read;
	}
	public int getUpdate() {
		return update;
	}
	public void setUpdate(int update) {
		this.update = update;
	}
	public boolean isShared() {
		return shared;
	}
	public void setShared(boolean shared) {
		this.shared = shared;
	}
	
	/* Dimensions of store:
	 * Role: Active (Eager/Lazy), Passive: Materialization into store, controlled by store or by data generator 
	 * Access Type of SPE towards store: Push/Pull
	 * Synchronisation: false/synchronisation condition 
	 * Schema: No Schema, Item Schema
	 * Persistence: Persistent/Transient
	 * Read Pattern: Sequential, Random, Clustered
	 * Indexe
	 * Update Pattern: FIFO, RANDOM, IN-PLACE
	 * Sharing: Shared, Not Shared
	 * 
	 * Tuneables:
	 * Blocksize
	 * 
	 */	
	
}
