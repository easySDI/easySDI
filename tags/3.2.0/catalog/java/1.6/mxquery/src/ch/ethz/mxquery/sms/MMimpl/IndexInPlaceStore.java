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

package ch.ethz.mxquery.sms.MMimpl;

import java.util.HashMap;
import java.util.LinkedList;
import java.util.List;
import java.util.Map;
import java.util.concurrent.locks.ReentrantReadWriteLock;

import ch.ethz.mxquery.bindings.WindowBuffer;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.NamedToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.datamodel.xdm.XDMScope;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.QueryLocation;
import ch.ethz.mxquery.model.EmptySequenceIterator;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.opt.index.IndexSchema;
import ch.ethz.mxquery.opt.index.SimpleEqIndex;

import ch.ethz.mxquery.sms.interfaces.IndexRead;
import ch.ethz.mxquery.sms.iterators.ResultDataItemsIterator;
import ch.ethz.mxquery.sms.iterators.ResultDataIterator;

import ch.ethz.mxquery.util.IntegerList;

public class IndexInPlaceStore extends InPlaceStore implements IndexRead {
	
	private static int schemaId = 0;
	private Map indexes = null;
	private ReentrantReadWriteLock lockConsume = null;
	private int itemSize = 0;
	private UpdateTokenBuffer crtBuffer = null;
	private List schemas = null;
	private static XDMIterator empty = new EmptySequenceIterator(null, null);
	private Token[] defaultValues = null;
	private boolean hasDefault = false;
	
	XDMScope curNsScope = new XDMScope();

	public IndexInPlaceStore(int id, WindowBuffer container){
		super(id, container);
		schemas = new LinkedList();
		indexes = new HashMap();
		lockConsume = new ReentrantReadWriteLock();
	}
	
	public void setDefaultValues(Token[] tokens){
		defaultValues = tokens;
		hasDefault = true;
	}
	
	private UpdateTokenBuffer getDefaultBuffer(Token id) throws MXQueryException{
		
		UpdateTokenBuffer defaultBuffer = new UpdateTokenBuffer(false,cont);
		
		Token startToken = new NamedToken(Type.START_TAG,null,new QName("res"),curNsScope);
		defaultBuffer.bufferToken(startToken);
		
		defaultBuffer.bufferToken(id);
		
		for ( int i=0 ; i<defaultValues.length; i++ ){
			defaultBuffer.bufferToken(defaultValues[i]);
		}
		
		Token endToken = new NamedToken(Type.END_TAG,null,new QName("res"),curNsScope);
		defaultBuffer.bufferToken(endToken);
		
		return defaultBuffer;
	}
	
	public void update(IndexSchema schema, Token[] toks, Token[] forIndex) throws MXQueryException{
		
		//TO DO : really change this!!!!!!!!!!!!!!!!!!!
		
//		List tokens = new LinkedList();
//		while(true){
//			Token tok = null;
//			try {
//				tok = update.next();
//			} catch (MXQueryException e) {
//				// TODO Auto-generated catch block
//				e.printStackTrace();
//			}
//			if ( tok == Token.END_SEQUENCE_TOKEN )
//				break;
//			((LinkedList<Token>)tokens).addLast(tok);
//		}
//		
//		Token[] toks = new Token[tokens.size()];
		
//		int size = tokens.size();
//		
//		for ( int i=0 ;i<size; i++ ){
//			toks[i] = (Token)((LinkedList)tokens).removeFirst();
//		}
		
		
		if (hasDefault)
			lockConsume.readLock().lock();
		
		SimpleEqIndex sei = (SimpleEqIndex)indexes.get(new Integer(schema.getId()));
		
		IntegerList pos = sei.retreive(forIndex);
		if (hasDefault)
			lockConsume.readLock().unlock();
		
		
		if ( pos == null ){
			if (hasDefault)
				lockConsume.writeLock().lock();
			
			if ( crtBuffer == null )
				crtBuffer = new UpdateTokenBuffer(hasDefault,cont);
			
			
			int indexPos = crtBuffer.getCurrentTokenId();
			crtBuffer.bufferItem(toks,toks.length);
			itemSize = toks.length;
			((SimpleEqIndex)indexes.get(new Integer(schema.getId()))).index(forIndex,indexPos);
			
			if (hasDefault)
				lockConsume.writeLock().unlock();
		}
		else{
			if (hasDefault)
				lockConsume.writeLock().lock();
			crtBuffer.update(toks,pos,toks.length,hasDefault);
			if (hasDefault)
				lockConsume.writeLock().unlock();
		}		
	}
	
	public XDMIterator retrieve(IndexSchema schema, Token[] tokens) throws MXQueryException{
		SimpleEqIndex index = (SimpleEqIndex)indexes.get(new Integer(schema.getId()));
		
		Token id = tokens[0]; 
		UpdateTokenBuffer dBuffer = null;		
		
		if (hasDefault) {
			dBuffer = getDefaultBuffer(id);
		}
		
		if ( index == null ){
			if (hasDefault){
				return new ResultDataIterator(dBuffer,0,dBuffer.size());
			}
			return empty;
		}
		if (hasDefault){
			lockConsume.readLock().lock();
		}
		
		IntegerList il = index.retreive(tokens);
		
		if ( il == null ){
			if (hasDefault){
				lockConsume.readLock().unlock();
				return new ResultDataIterator(dBuffer,0,dBuffer.size());
			}
			return empty;
		}		
		
		XDMIterator retIt = new ResultDataItemsIterator(crtBuffer,il,itemSize);
		if (hasDefault)
			lockConsume.readLock().unlock();
		return retIt;
	}
	
	public Token get(int pos){
		return crtBuffer.get(pos);
	}
	
	public void newItem(){
		
	}
	
	public void buffer(Token tok, int event) throws MXQueryException{
		throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED, "Not defined", null);
	}
	
	public IndexSchema registerIndex (IndexSchema schema){
		int indexId = matchSchema(schema);
		
		if ( indexId != -1 ){
			schema.setId(indexId);
			return schema;
		}
		
		schemaId++;
		schema.setId(schemaId);
		schemas.add(schema);
		indexes.put(new Integer(schemaId),new SimpleEqIndex());
		return schema;
	}
	
	private int matchSchema(IndexSchema schema){
		
		for ( int i=0; i<schemas.size(); i++ ){
			if (((IndexSchema)schemas.get(i)).equals(schema))
				return ((IndexSchema)(schemas.get(i))).getId();
		}
		
		return -1;
	}

	public void setIterator(XDMIterator it) throws MXQueryException {
		throw new MXQueryException(ErrorCodes.A0002_EC_NOT_SUPPORTED,"Not supported for this type of store",QueryLocation.OUTSIDE_QUERY_LOC);
	}
}
