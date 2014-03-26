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
package ch.ethz.mxquery.opt.expr;

import ch.ethz.mxquery.exceptions.MXQueryException;
import ch.ethz.mxquery.util.KXmlSerializer;
import ch.ethz.mxquery.util.Traversable;
import ch.ethz.mxquery.util.Utils;

/**
 * Every term and literal inherits from logical unit. This calls introduces some helper methods
 * @author Tim Kraska (<a href="mailto:mail@tim-kraska.de">mail@tim-kraska.de</a>)
 *
 */
public abstract class LogicalUnit implements Comparable, Cloneable, Traversable {
	
	public static final int RESET = -1;
	
	public static final int DEPENDENCY_NO = 0;
	public static final int DEPENDENCY_START = 1;
	public static final int DEPENDENCY_END = 2;
	public static final int DEPENDENCY_STARTEND = 3;
	
	public static final int RESULT_FALSE=-1;
	public static final int RESULT_UNKNOWN=0;
	public static final int RESULT_TRUE=1;

	public static final int INDEX_POSSIBLE = 0;
	public static final int INDEX_RECOMMEND = 1;
	public static final int INDEX_IMPOSSIBLE = -1;
	
	protected int indexable=INDEX_POSSIBLE;
	
	protected int dependency;
	
	protected int result = RESULT_UNKNOWN;
	
	protected int currentEvalLevel = -1;
	
	public int getDependency() {
		return dependency;
	}

	public void setDependency(int dep) {
		dependency = dep;
	}
	
	/**
	 * Compares to logical units according to their dependencies 
	 * @param o
	 * @return -1 of this logical unit is small, 0 if equal, 1 if this is greater
	 */
	public int compareTo(Object o) {
		LogicalUnit lu2 = (LogicalUnit) o;
		int value1 = getDependency();
		int value2 = lu2.getDependency();
		if(value1 < value2){
			return -1;
		}else if(value1 == value2){
			return 0;
		}else{
			return 1;
		}
	}
	
	/**
	 * Resets the logical unit up to a certain degree on variable dependency
	 * @param level
	 * @return UNKNOWN, TRUE or FALSE
	 */
	public int reset(int level){
		if(isLowerOrEqualDependency(level, dependency)){
			result = RESULT_UNKNOWN;
		}
		return result;
	}
	
	/**
	 * Evaluates the logical unit until a certain degree on variable
	 * dependency and returns if the result is false, true or unknown
	 * @param level Up to this dependency level the unit is evaluated
	 * @return false, true or unknown
	 * @throws MXQueryException
	 */
	public abstract int evaluate(int level) throws MXQueryException;

	protected Object clone() {
	     try  {
	         return super.clone ();
	       }
	       catch (CloneNotSupportedException e) {
	         throw new Error ("This should never happen!");
	       }
	}
	
	/**
	 * Compares to dependencies to each other
	 * @param dep1
	 * @param dep2
	 * @return  true if equals or lower dependency
	 */
	public boolean isLowerOrEqualDependency(int dep1, int dep2){
		if(isLowerDependency(dep1, dep2)){
			return true;
		}else{
			if(dep1 == dep2){
				return true;
			}else{
				return false;
			}
		}
	}
	
	/**
	 * Returns if for this logical unit a index is recommended. 
	 * @return true if an index is recommended, false otherwise
	 */
	public boolean isIndexable() {
		return (indexable == INDEX_RECOMMEND);
	}
	
	/**
	 * Sets if a logical unit is indexable. But only following changes are allowed: <br>
	 * INDEX_POSSIBLE -->  INDEX_RECOMMEND
	 * INDEX_POSSIBLE -->  INDEX_IMPOSSIBLE
	 * INDEX_RECOMMEND --> INDEX_IMPOSSIBLE
	 * @param value
	 */
	public void setIndexable(int value){
		if(value == LogicalUnit.INDEX_IMPOSSIBLE){
			indexable = INDEX_IMPOSSIBLE;
		}else if(value == INDEX_RECOMMEND && indexable == INDEX_POSSIBLE ){
			indexable = INDEX_RECOMMEND;
		}
	}

	
	/**
	 * Compares two dependecies. The order is as follows
	 * RESET < DEPENDENCY_NO < DEPENDENCY_START | DEPENDENCY_END < DEPENDENCY_STARTEND
	 * @param dep1
	 * @param dep2
	 * @return true of dep1 has a lower dependency 
	 */
	public boolean isLowerDependency(int dep1, int dep2){
		switch(dep2){
		case RESET:
		case DEPENDENCY_NO:
		case DEPENDENCY_START:
			return (dep1 < dep2);
		case DEPENDENCY_END:
			return (dep1 <  DEPENDENCY_START);	
		case DEPENDENCY_STARTEND:
			return (dep1 <  DEPENDENCY_STARTEND);	
		default:
			return false;
		}
	}
	
	/**
	 * Adds a new dependency to the logical unit
	 * @param dep
	 */
	public void addDependency(int dep){
		if(dependency != DEPENDENCY_STARTEND){
			boolean start = isDependendOnStart(dependency) || isDependendOnStart(dep);
			boolean end = isDependendOnEnd(dependency) || isDependendOnEnd(dep);
			dependency = getDependencyNb(start, end);
		}
	}
	
	/**
	 * Test if it depends on the start variables
	 * @param dependency
	 * @return true of there is a dependency on start variables
	 */
	public static boolean isDependendOnStart(int dependency){
		return (dependency == DEPENDENCY_START || dependency == DEPENDENCY_STARTEND);
	}
	
	/**
	 * Test if it depends on the end variables
	 * @param dependency
	 * @return true of there is a dependency on end variables
	 */
	public static boolean isDependendOnEnd(int dependency){
		return (dependency == DEPENDENCY_END || dependency == DEPENDENCY_STARTEND);
	}
	
	public static String getDependencyName(int dependency){
		switch(dependency){
		case DEPENDENCY_NO:
			return "DEPENDENCY_NO";
		case DEPENDENCY_START:
			return "DEPENDENCY_START";
		case DEPENDENCY_END:
			return "DEPENDENCY_END";
		case DEPENDENCY_STARTEND:
			return "DEPENDENCY_STARTEND";
		}		
		throw new RuntimeException("Unknown type! That should never happen!");
	}
	
	/**
	 * Returns for the combination of start and end variable the corresponding constant
	 * @param start Depends on the start variable
	 * @param end Depends on the end variable
	 * @return the correct dependency class 
	 */
	public static int getDependencyNb(boolean start, boolean end){
		if(!start && !end){
			return LogicalUnit.DEPENDENCY_NO;
		}else if(start && !end){
			return LogicalUnit.DEPENDENCY_START;
		}else if(!start && end){
			return LogicalUnit.DEPENDENCY_END;
		}else{
			return LogicalUnit.DEPENDENCY_STARTEND;
		}
	}


	/**
	 * Returns if the result if the result if false, ture or unknown
	 * @return the predicate index result
	 */
	public int getResult() {
		return result;
	}

	public KXmlSerializer traverse(KXmlSerializer serializer){
		createStartTag(serializer);
		createEndTag(serializer);
		return serializer;
	}
	
	protected KXmlSerializer createStartTag(KXmlSerializer serializer){
		try{
		serializer.startTag(null,  Utils.getSimpleClassName(getClass().getName()) );
		serializer.attribute(null, "indexable", ""+ indexable);
		serializer.attribute(null, "dependency", getDependencyName(this.dependency));
		serializer.attribute(null, "evalLevel", "" + this.currentEvalLevel);
		serializer.attribute(null, "result", "" + this.result);
		return serializer;
		}catch(Exception err){
			throw new RuntimeException(err);
		}
	}
	
	protected KXmlSerializer createEndTag(KXmlSerializer serializer){
		try{
		serializer.endTag(null,  Utils.getSimpleClassName(getClass().getName()) );
		return serializer;
		}catch(Exception err){
			throw new RuntimeException(err);
		}
	}
	
	
}
