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

package ch.ethz.mxquery.datamodel.xdm;

import org.apache.xerces.xs.ShortList;

import ch.ethz.mxquery.datamodel.Identifier;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.exceptions.MXQueryException;

public class NamedToken extends Token {
	protected String local;

	protected String prefix;

	protected String namespace;
	
	protected String ID = null;
	protected String IDREF = null;
	protected String IDREFS = null;


	private ShortList listValueTypes;
	
	NamedToken(NamedToken token) {
		super(token);
		this.eventType = token.eventType;
		this.namespace = token.getNS();
		this.prefix = token.getPrefix();
		this.local = token.getLocal();
		this.ID = token.getID();
		this.IDREF = token.getIDREF();
		this.IDREFS = token.getIDREFS();
		this.listValueTypes = token.getListValueTypes();
	}

	public NamedToken(int eventType, Identifier id, QName qName, XDMScope scope) {
		super(eventType, id, scope);
		this.local = qName.getLocalPart();
		this.prefix = qName.getNamespacePrefix();
		namespace = qName.getNamespaceURI();
	}
	
	public void setID(String id) {
		ID = id;
	}
	
	public void setIDREF(String ef) {
		IDREF = ef;
	}
	
	public void setIDREFS(String idrefs) {
		IDREFS = idrefs;
	}
	public void setNS(String name) {
		this.namespace = name;
	}

	public String getID() {
		return ID;
	}
	
	public String getIDREF() {
		return IDREF;
	}
	
	public String getIDREFS() {
		return IDREFS;
	}
	
	public String getName() {
		return (this.prefix == null) ? this.local
				: (this.prefix + ":" + this.local);
	}
	
	public String getLocal(){
		return this.local;
	}
	
	public String getPrefix() {
		return this.prefix;
	}
	
	public String getNS(){
		return this.namespace;
	}
	
	public Token toAttrToken(QName name, XDMScope scope) {
		return this;
	}
	public String getValueAsString() {
		return getName();
	}
	
	public NamedToken copy(QName newName) throws MXQueryException{
		return new NamedToken(this.eventType, this.id, newName, dynamicScope);
	}
	
	public Token copy() {
		//System.out.println("copy2 "+this.local+" with type "+Type.getTypeQName(this.getTypeAnnotation(), Context.getDictionary()));
		return new NamedToken(this);
	}
	public NamedToken copyStrip(){
		NamedToken token = (NamedToken)this.copy();
		int type = token.eventType;
		token.setListValueTypes(null);
		if (Type.isAttribute(type)) {token.setEventType(this.eventType & MASK_GET_START_TAG | Type.UNTYPED_ATOMIC);
		}
		else 
		if(!Type.isAttribute(type) && ((type & MASK_GET_START_TAG) == Type.START_TAG)){
		token.setEventType((type & MASK_GET_START_TAG) | Type.UNTYPED);
		}
		return token;
		
	}

	public void setListValueTypes(ShortList listValueTypes) {
	 this.listValueTypes = listValueTypes;
	}
	
	public ShortList getListValueTypes() {
		return listValueTypes;
	}
}

