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

package ch.ethz.mxquery.update.store.llImpl;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.MXQueryBinary;
import ch.ethz.mxquery.datamodel.MXQueryDate;
import ch.ethz.mxquery.datamodel.MXQueryDateTime;
import ch.ethz.mxquery.datamodel.MXQueryDayTimeDuration;
import ch.ethz.mxquery.datamodel.MXQueryDouble;
import ch.ethz.mxquery.datamodel.MXQueryDuration;
import ch.ethz.mxquery.datamodel.MXQueryGregorian;
import ch.ethz.mxquery.datamodel.MXQueryNumber;
import ch.ethz.mxquery.datamodel.MXQueryTime;
import ch.ethz.mxquery.datamodel.MXQueryYearMonthDuration;
import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.xdm.Token;

public class LLNormalToken extends LLToken {
	private int depth;

	public LLNormalToken(Token token, int depth) {
		super(token);
		this.depth = depth;
	}
	
	public boolean isAttribute() {
		return this.token.isAttribute();
	}

	public void setNext(LLToken next) {
		this.next = next;
	}

	public void setPrev(LLToken prev) {
		this.prev = prev;
	}

	public Token getToken() {
		return token;
	}

	public int getEventType() {
		if (this.token == null) {
			return -1;
		} else {
			return this.token.getEventType();
		}
	}

	// ////////////////////////////////////////
	// Methods for Iterator function support //
	// ////////////////////////////////////////
	public int getDepth() {
		return this.depth;
	}
	
	public void setDepth(int depth) {
		this.depth = depth;
	}

	void addDepth(int i) {
		this.depth += i;
	}

	public String getText() {
		return this.token.getText();
	}
	
	public String getValueAsString() {
		return this.token.getValueAsString();
	}

	public String getName() {
		return this.token.getName();
	}
	
	public String getNS() {
		return this.token.getNS();
	}
	
	public String getLocal() {
		return this.token.getLocal();
	}

	public long getLong() {
		return this.token.getLong();
	}

	public boolean getBoolean() {
		return this.token.getBoolean();
	}

	public MXQueryDouble getDouble() {
		return this.token.getDouble();
	}

	public MXQueryNumber getNumber() {
		return null;
	}	
	
	public MXQueryDateTime getDateTime() {
		return this.token.getDateTime();
	}

	public MXQueryDate getDate() {
		return this.token.getDate();
	}
	
	public MXQueryTime getTime() {
		return this.token.getTime();
	}
	
	public MXQueryDayTimeDuration getDayTimeDur() {
		return this.token.getDayTimeDur();
	}

	public MXQueryYearMonthDuration getYearMonthDur() {
		return this.token.getYearMonthDur();
	}

	public MXQueryDuration getDuration() {
		return this.token.getDuration();
	}
	
	public QName getQNameTokenValue() {
		return this.token.getQNameTokenValue();
	}
	
	public MXQueryGregorian getGregorianValue() {
		return this.token.getGregorian();
	}		
	
	public MXQueryBinary getBinaryValue() {
		return this.token.getBinary();
	}		
	
	public String toString() {
		String str;
		int eventType = Type.getEventTypeSubstituted(token.getEventType(), Context.getDictionary());
		
		if ( Type.isTextNode(eventType) )  {
			eventType = Type.getTextNodeValueType(eventType);
		}		
		
		switch (eventType) {
		case Type.START_TAG:
			str = /*"<" +*/ this.token.getName();
			break;
		case Type.END_TAG:
			str = "</" + this.token.getName() + ">";
			break;
		case Type.STRING:
		case Type.UNTYPED_ATOMIC:
		case Type.UNTYPED:
		case Type.ANY_URI:
		case Type.INTEGER:
		case Type.BOOLEAN:
		case Type.DOUBLE:
		case Type.FLOAT:
		case Type.DECIMAL:			
		case Type.DATE_TIME:
		case Type.DATE:
		case Type.TIME:
		case Type.DAY_TIME_DURATION:
		case Type.YEAR_MONTH_DURATION:
		case Type.DURATION:
		case Type.QNAME:
		case Type.BASE64_BINARY:
		case Type.HEX_BINARY:
		case Type.NOTATION:
		case Type.G_DAY:
		case Type.G_MONTH:
		case Type.G_YEAR:
		case Type.G_YEAR_MONTH:
		case Type.G_MONTH_DAY:
			str = this.token.getValueAsString();
			break;
		case Type.START_SEQUENCE:
		case Type.END_SEQUENCE:
			throw new RuntimeException("OberNEF");
		case Type.START_DOCUMENT:
			str = "<mxDocument>";
			break;
		case Type.END_DOCUMENT:
			str = "</mxDocument>";
			break;
		default:
			if (this.token.isAttribute()) {
				str = this.getName() + "=\"" + this.getValueAsString() + "\"";
				if (str == null) {
					str = "";
				}
			} else {
				//str = Type.getTypeQName(this.token.getEventType()).toString();
				str = "";
			}
		}
		return str;
	}
	
	public LLToken copy() {
		return new LLNormalToken(token.copy(), depth);
	}
}
