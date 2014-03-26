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
package examples;

import javax.xml.namespace.QName;
import javax.xml.xquery.*;
import ch.ethz.mxquery.xqj.MXQueryXQDataSource;

/**
 * Simple XQJ sample
 * For more extensive tutorials, please look at
 * http://www.xquery.com/tutorials/xqj_tutorial/
 * 
 * @author Peter Fischer
 *
 */

public class XQJExample {

	public static void main(String[] args)throws XQException {
		String query = "declare variable $ext external; (14 + $ext, $ext * $ext)";
	    XQDataSource ds = new MXQueryXQDataSource();
        XQConnection conn = ds.getConnection();
        
        System.out.println("Running query: "+query);
        
        XQPreparedExpression exp = conn.prepareExpression(query);
        exp.bindInt(new QName("ext"), 7, null);
        XQResultSequence result = exp.executeQuery();
        XQSequence sequence = conn.createSequence(result);
        
        System.out.println("Result for value 7");
        sequence.writeSequence(System.out, null);

        exp.bindInt(new QName("ext"), 11, null);
        result = exp.executeQuery();
        sequence = conn.createSequence(result);
        
        System.out.println("\nResult for value 11");
        sequence.writeSequence(System.out, null);

        
        result.close();
        sequence.close();
	}

}
