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

import java.util.Vector;

import ch.ethz.mxquery.contextConfig.Context;
import ch.ethz.mxquery.datamodel.types.Type;
import ch.ethz.mxquery.datamodel.types.TypeInfo;
import ch.ethz.mxquery.datamodel.xdm.BooleanToken;
import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.model.TokenBasedIterator;
import ch.ethz.mxquery.model.XDMIterator;

/**
 * Tests if a number is prime.
 * 
 * @author Matthias Braun
 * 
 */
public class Eratosthenes extends TokenBasedIterator {
	protected void init() throws MXQueryException {
		isPrime(subIters[0].next().getLong());
	}
	
	protected XDMIterator copy(Context context, XDMIterator[] subIters, Vector nestedPredCtxStack) throws MXQueryException {
		XDMIterator copy = new Eratosthenes();
		copy.setContext(context, true);
		copy.setSubIters(subIters);
		return copy;
	}
	
	public void isPrime(long x) {
		int i, j=0;
		long n=3;
		long primes[] = new long[5602];
		
		primes[0] = 2;
		
		boolean xPrime = true, nPrime;
		
		if (x%2==0 && x!=2) {
			xPrime = false;
			n = 2;
		}

	    xWhile: while (n<=Math.sqrt(x)) {
	
		    i = 0;
		    nPrime = true;
	
		    nWhile: while(primes[i]*primes[i]<=n) {
		    	if (n%primes[i]==0) {
					nPrime = false;
					break nWhile;
		        }
		        i++;
		    }
		    
		    if (nPrime) {
		    	if (x%n==0) {
		    		xPrime = false;
		    		break xWhile;
		    	}
		    	if (j<5601) primes[j++] = n;
		    }
		    n+=2;
		}
	
		if (xPrime) {
			currentToken = BooleanToken.TRUE_TOKEN; 
		} else {
			currentToken = BooleanToken.FALSE_TOKEN; 
		}
	}
	public TypeInfo getStaticType() {
		return new TypeInfo(Type.BOOLEAN,Type.OCCURRENCE_IND_EXACTLY_ONE,null,null);
	}		

}
