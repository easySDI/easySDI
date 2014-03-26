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

package ch.ethz.mxquery.mobile;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

import javax.microedition.midlet.MIDlet;
import javax.microedition.lcdui.Display;
import javax.microedition.lcdui.TextBox;

import org.xmlpull.v1.XmlPullParser;

import ch.ethz.mxquery.query.*;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.XDMIterator;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.query.impl.CompilerImpl;
import ch.ethz.mxquery.xdmio.XDMSerializer;
import ch.ethz.mxquery.util.LineReader;
import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.contextConfig.CompilerOptions;


public class MIDP extends MIDlet{
	private Display display = null;
	private TextBox textbox = null;
	protected static XDMIterator result;
	private static StringBuffer myBuffer = new StringBuffer();
	
	public MIDP() {
		try {
			String text = "ok";
			
			Context ctx = new Context();

			
//			String query = "<david graf=\"\"/>";
			
//			InputStream is = this.getClass().getResourceAsStream(
//			"test.xq");
//			StringBuffer str = new StringBuffer();
//			try {
//				String thisLine;
//				LineReader br = new LineReader(new InputStreamReader(is));
//				while ((thisLine = br.readLine()) != null) {
//		
//					str.append(thisLine + "\n");
//				}
//			} catch (IOException e) {
//				throw new MXQueryException(e);
//			}
			String query = "for $seq in (1,2,3,4,5) where $seq mod 2 eq 0 return $seq";
			XQCompiler compiler = new CompilerImpl();
			PreparedStatement statement =  compiler.compile(ctx, query, new CompilerOptions());
			
			if (statement != null) {
				result = statement.evaluate();
				XDMSerializer ip = new XDMSerializer();
				text = new String(ip.eventsToXML(result));
				statement.applyPUL();
			}
			
			 System.out.println(text);
			 textbox = new TextBox("", "Query: "+query+"\nResult: "+text, 350, 0);	
		} catch (Exception e) {
			try {
				e.printStackTrace();
				textbox = new TextBox("", "error"+ e.toString(), 250, 0);
			}
			catch(IllegalArgumentException ie) {
				textbox = new TextBox("", "error: too large", 20, 0);
			}
		}
	}
	
	public void startApp() {
		display = Display.getDisplay(this);
		display.setCurrent(textbox);     
	}
	
	public void pauseApp() {}

	public void destroyApp(boolean unconditional) {}



/*
package ch.ethz.mxquery.mobile;

import javax.microedition.midlet.MIDlet;
import javax.microedition.lcdui.Display;
import javax.microedition.lcdui.TextBox;

import org.xmlpull.v1.XmlPullParser;

import ch.ethz.mxquery.core.XQueryExpression;
import ch.ethz.mxquery.core.XQueryRuntime;
import ch.ethz.mxquery.model.iterators.Iterator;

public class MIDP extends MIDlet{
	private Display display = null;
	private TextBox textbox = null;
	private static Iterator result = null;
	private static StringBuffer myBuffer = new StringBuffer();
	
	public MIDP() {
		long start, end;
		try {
			XQueryRuntime runtime = new XQueryRuntime();
			
			String query = "1+1";
			//String query = "for $x in (1,2,3) return <res>{$x+1}</res>";
			//String query = "(<doc ns:myAttr='{ 1+56 }'><node><nodeContent/><elem eAttr='{4 ge 3}'>elementContent</elem></node></doc>)//elem";
			
			XQueryExpression expr = runtime.prepareQuery(query);
			
			if (expr != null) {
				start = System.currentTimeMillis();
				
				result = expr.evaluate();
				
				while (result.next() != Type.END_DOCUMENT) {
					//writeTokenComp();
				}
				
				end = System.currentTimeMillis();
			}
			
			textbox = new TextBox("", query + "\n" + myBuffer.toString(), 20, 0);	
		} catch (Exception e) {
			textbox = new TextBox("", "error"+e.toString(), 20, 0);
		}
	}
	
	public void startApp() {
		display = Display.getDisplay(this);
		display.setCurrent(textbox);     
	}
	
	public void pauseApp() {}

	public void destroyApp(boolean unconditional) {}
*/	
}
