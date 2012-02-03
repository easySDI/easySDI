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

package ch.ethz.mxquery.model.ft;

import ch.ethz.mxquery.datamodel.QName;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * FTExtension Match Option
 * @author jimhof
 *
 */

public class FTExtensionMatchOption extends MatchOption {
	
	QName qname;
	XDMIterator temp;

	public FTExtensionMatchOption(QName qname, XDMIterator temp) {
		super(MatchOption.MATCH_OPTION_TYPE_EXTENSION,true);
		this.qname = qname;
		this.temp = temp;
	}

	public QName getQname() {
		return qname;
	}

	public XDMIterator getTemp() {
		return temp;
	}

}
