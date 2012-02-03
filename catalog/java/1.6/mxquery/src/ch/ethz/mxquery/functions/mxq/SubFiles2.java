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
package ch.ethz.mxquery.functions.mxq;

import java.io.File;
import java.io.FileFilter;
import java.util.Stack;
import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.TextToken;
import ch.ethz.mxquery.datamodel.xdm.Token;
import ch.ethz.mxquery.exceptions.ErrorCodes;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.exceptions.TypeException;
import ch.ethz.mxquery.model.CurrentBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

public class SubFiles2 extends CurrentBasedIterator {
	
	private class DirLevelPair {
		File dir;
		int level;
		public DirLevelPair(File dir, int level) {
			super();
			this.dir = dir;
			this.level = level;
		}
	}
	
	private FileFilter filter;
	
	private Vector seenFiles = new Vector();
	
	protected void init() throws MXQueryException {
		Stack files;
		Stack dirs;

		files = new Stack();
		dirs = new Stack();
		this.current = null;
		
		XDMIterator startDirIt = subIters[0];
		XDMIterator patternIt = this.subIters[1];
		Token depTok = 	this.subIters[2].next();
		if (!Type.isTypeOrSubTypeOf(depTok.getEventType(), Type.INTEGER, Context.getDictionary()))
			throw new TypeException(ErrorCodes.E0004_TYPE_INAPPROPRIATE_TYPE, "Integer subtype expected",loc);
		long maxDepth = depTok.getLong();
		Token tok1 = startDirIt.next();
		if (tok1.getEventType() == Type.END_SEQUENCE) {
			return;
		}
		File root  = new File(tok1.getValueAsString());
		if (!root.exists()) {
			return;
		}
		dirs.push(new DirLevelPair(root,0));
		
		Token curToken;
		Vector vSuff = new Vector();
		while ((curToken = patternIt.next()).getEventType() != Type.END_SEQUENCE) {
			vSuff.add(curToken.getValueAsString());
		}
		String[] suffixes = new String[vSuff.size()];
		vSuff.copyInto(suffixes);
		filter = new Filter(suffixes);
		
		while (files.size() > 0 || dirs.size() > 0) {
			if (files.size() > 0){
				File file = (File)files.pop();
				String res = file.toURI().toString();
				seenFiles.addElement(res);
			}
			
			if (dirs.size() > 0) {
				DirLevelPair dir = (DirLevelPair)dirs.pop();
				if (dir.level <= maxDepth) {
					File[] filez = dir.dir.listFiles(this.filter);
					for (int i = 0; i < filez.length; i++) {
						if (filez[i].isDirectory()) {
							dirs.push(new DirLevelPair(filez[i],dir.level+1));
						} else {
							files.push(filez[i]);
						}
					}
				}
			}
		}	
	}

	public Token next() throws MXQueryException {
		if (this.called == 0) {
			this.init();
		}
		
		if (called < seenFiles.size()) {
			String pathStr = (String)seenFiles.elementAt(called);
			this.called++;
			return new TextToken(null,pathStr);
		}
		return Token.END_SEQUENCE_TOKEN;
	}

	private static class Filter implements FileFilter {
		private String[] suffixes;
		
		public Filter(String[] suffixes) {
			this.suffixes = suffixes;
		}
		
		public boolean accept(File file) {
			if (file.isDirectory()) {
				return true;
			}
			for (int i = 0; i < this.suffixes.length; i++) {
				if (file.getName().endsWith(this.suffixes[i])) {
					return true;
				}
			}
			return false;
		}
	}

	public TypeInfo getStaticType() {
		return new TypeInfo(Type.STRING,Type.OCCURRENCE_IND_ZERO_OR_MORE,null,null);
	}		
	
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new SubFiles2();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
}
